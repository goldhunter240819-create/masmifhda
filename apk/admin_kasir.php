<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];
$user_id = $_SESSION['user_id'] ?? 0;

// Cek hak akses keuangan (app_admins atau keuangan_akses)
$is_keuangan = false;
$allowed_tagihan = [];
$is_super_keuangan = false;

$stmt_app = $core_db->prepare("SELECT app_code FROM app_admins WHERE user_id = :uid AND app_code = 'keuangan' LIMIT 1");
$stmt_app->execute(['uid' => $user_id]);
if ($stmt_app->fetch()) {
    $is_keuangan = true;
    $is_super_keuangan = true;
} else {
    $cek_bendahara = $core_db->prepare("SELECT id, akses_pembayaran FROM keuangan_akses WHERE guru_id = ?");
    $cek_bendahara->execute([$guru_id]);
    $bendahara = $cek_bendahara->fetch(PDO::FETCH_ASSOC);
    if ($bendahara) {
        $is_keuangan = true;
        if (!empty($bendahara['akses_pembayaran'])) {
            $allowed_tagihan = json_decode($bendahara['akses_pembayaran'], true) ?: [];
        }
    }
}

if (!$is_keuangan) {
    header("Location: akses_ditolak.php");
    exit;
}

$page_title = 'Kasir Pembayaran';
include 'header.php';

// Jika POST pembayaran
$message = '';
$status = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'bayar') {
    $siswa_id = intval($_POST['siswa_id']);
    $tagihan_id = intval($_POST['tagihan_id']);
    $nominal = intval($_POST['nominal']);
    
    if ($nominal > 0) {
        try {
            $core_db->beginTransaction();
            
            // Dapatkan detail tagihan
            $stmt_t = $core_db->prepare("SELECT * FROM keuangan_tagihan WHERE id = ?");
            $stmt_t->execute([$tagihan_id]);
            $tagihan = $stmt_t->fetch(PDO::FETCH_ASSOC);
            
            if ($tagihan) {
                // Hitung sisa yang belum dibayar
                $terbayar_sebelum = $core_db->query("SELECT COALESCE(SUM(jumlah), 0) FROM keuangan_komite_pembayaran WHERE siswa_id = $siswa_id AND jenis_pembayaran = '".$tagihan['nama_tagihan']."'")->fetchColumn();
                $sisa_tagihan = $tagihan['jumlah_tagihan'] - $terbayar_sebelum;

                if ($nominal > $sisa_tagihan) {
                    throw new Exception("Nominal pembayaran melebihi sisa tagihan (Sisa: Rp " . number_format($sisa_tagihan,0,',','.') . ").");
                }

                // Simpan ke keuangan_komite_pembayaran
                $stmt = $core_db->prepare("INSERT INTO keuangan_komite_pembayaran (siswa_id, jenis_pembayaran, jumlah, tanggal_bayar) VALUES (?, ?, ?, CURDATE())");
                $stmt->execute([$siswa_id, $tagihan['nama_tagihan'], $nominal]);
                
                // SINKRONISASI: Masukkan ke Kas Umum agar masuk di Dashboard Web
                $stmt2 = $core_db->prepare("INSERT INTO keuangan_transaksi (tanggal_bayar, keterangan, jenis, jumlah) VALUES (CURDATE(), ?, 'Pemasukan', ?)");
                $stmt2->execute(["Pembayaran Kas (" . $tagihan['nama_tagihan'] . "): Via Kasir Mobile", $nominal]);
                
                // Update status lunas di keuangan_tagihan jika sudah terpenuhi
                $terbayar = $core_db->query("SELECT COALESCE(SUM(jumlah), 0) FROM keuangan_komite_pembayaran WHERE siswa_id = $siswa_id AND jenis_pembayaran = '".$tagihan['nama_tagihan']."'")->fetchColumn();
                
                if ($terbayar >= $tagihan['jumlah_tagihan']) {
                    $core_db->prepare("UPDATE keuangan_tagihan SET status = 'Lunas' WHERE id = ?")->execute([$tagihan_id]);
                } else {
                    $core_db->prepare("UPDATE keuangan_tagihan SET status = 'Menyicil' WHERE id = ?")->execute([$tagihan_id]);
                }
                
                
                $core_db->commit();
                
                require_once __DIR__ . '/../vendor/autoload.php';
                if (class_exists('\App\Services\AuditLogger')) {
                    \App\Services\AuditLogger::dataChange('Keuangan', 'PEMBAYARAN_KASIR', "tagihan={$tagihan['nama_tagihan']}, nominal={$nominal} (APK)");
                }

                $message = 'Pembayaran Rp '.number_format($nominal,0,',','.').' berhasil disimpan!';
                
                // --- KIRIM PUSH NOTIFICATION ---
                try {
                    $nominal_rp = formatRupiah($nominal);
                    $notif_title = "✅ Pembayaran Berhasil";
                    $notif_msg = "Terima kasih, pembayaran " . $tagihan['nama_tagihan'] . " sebesar $nominal_rp telah kami terima.";
                    
                    // Kirim notifikasi PRIVATE khusus ke HP siswa/wali murid ini saja
                    sendOneSignalNotification('siswa_' . $siswa_id, $notif_title, $notif_msg);
                } catch (Exception $e) {}

                $status = 'success';
            }
        } catch (Exception $e) {
            $core_db->rollBack();
            $message = 'Gagal menyimpan pembayaran: ' . $e->getMessage();
            $status = 'error';
        }
    }
}

// Hitung Total Saldo Kasir (Saldo Bersih)
$total_saldo = 0;
if ($is_super_keuangan) {
    $tot_siswa = $core_db->query("SELECT SUM(jumlah) FROM keuangan_komite_pembayaran")->fetchColumn() ?: 0;
    $tot_in = $core_db->query("SELECT SUM(jumlah) FROM keuangan_komite_transaksi WHERE jenis='Pemasukan'")->fetchColumn() ?: 0;
    $tot_out = $core_db->query("SELECT SUM(jumlah) FROM keuangan_komite_transaksi WHERE jenis='Pengeluaran'")->fetchColumn() ?: 0;
    $total_saldo = ($tot_siswa + $tot_in) - $tot_out;
} else if (!empty($allowed_tagihan)) {
    // 1. Total dari Siswa (berdasarkan tagihan)
    $in_tot = str_repeat('?,', count($allowed_tagihan) - 1) . '?';
    $sql_tot_siswa = "SELECT SUM(jumlah) FROM keuangan_komite_pembayaran WHERE jenis_pembayaran IN ($in_tot)";
    $stmt_tot_siswa = $core_db->prepare($sql_tot_siswa);
    $stmt_tot_siswa->execute($allowed_tagihan);
    $tot_siswa = $stmt_tot_siswa->fetchColumn() ?: 0;

    // Cari kategori dari tagihan-tagihan ini
    $sql_kat = "SELECT DISTINCT kategori FROM keuangan_komite_jenis WHERE nama_tagihan IN ($in_tot)";
    $stmt_kat = $core_db->prepare($sql_kat);
    $stmt_kat->execute($allowed_tagihan);
    $kategori_managed = $stmt_kat->fetchAll(PDO::FETCH_COLUMN);

    $tot_in = 0;
    $tot_out = 0;
    if (!empty($kategori_managed)) {
        $in_kat = str_repeat('?,', count($kategori_managed) - 1) . '?';
        
        $sql_in = "SELECT SUM(jumlah) FROM keuangan_komite_transaksi WHERE jenis='Pemasukan' AND kategori IN ($in_kat)";
        $stmt_in = $core_db->prepare($sql_in);
        $stmt_in->execute($kategori_managed);
        $tot_in = $stmt_in->fetchColumn() ?: 0;
        
        $sql_out = "SELECT SUM(jumlah) FROM keuangan_komite_transaksi WHERE jenis='Pengeluaran' AND kategori IN ($in_kat)";
        $stmt_out = $core_db->prepare($sql_out);
        $stmt_out->execute($kategori_managed);
        $tot_out = $stmt_out->fetchColumn() ?: 0;
    }

    $total_saldo = ($tot_siswa + $tot_in) - $tot_out;
}
?>

<div class="header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
    <div class="header-top">
        <div class="greeting">
            <p>Admin Keuangan</p>
            <h1>Kasir Pembayaran</h1>
        </div>
        <div class="avatar" style="color: #10b981; background: white;">
            <i class="fa-solid fa-cash-register"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <?php if(!empty($message)): ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tampilkan toast notification
            setTimeout(() => {
                showToast("<?= addslashes($message) ?>", <?= $status == 'error' ? 'true' : 'false' ?>);
            }, 100);
            
            // Kembalikan status pencarian siswa jika sukses bayar
            <?php if ($status == 'success' && !empty($_POST['siswa_id'])): 
                $s_id = intval($_POST['siswa_id']);
                $s_nama = $core_db->query("SELECT nama FROM siswa WHERE id = $s_id")->fetchColumn() ?: 'Siswa';
            ?>
            loadTagihan(<?= $s_id ?>, "<?= addslashes($s_nama) ?>");
            <?php endif; ?>
        });
        </script>
    <?php endif; ?>

    <!-- Kartu Total Saldo -->
    <div style="background: white; border-radius: 14px; padding: 15px 20px; box-shadow: 0 4px 15px -3px rgba(0,0,0,0.05); margin-top: -45px; z-index: 10; position: relative; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #f1f5f9;">
        <div>
            <div style="font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 4px;">Total Saldo</div>
            <div id="total_saldo_display" style="font-weight: 800; font-size: 1.4rem; color: #10b981;">Rp <?= number_format($total_saldo, 0, ',', '.') ?></div>
        </div>
        <div style="background: #ecfdf5; width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 1.2rem; flex-shrink: 0;">
            <i class="fa-solid fa-wallet"></i>
        </div>
    </div>

    <div class="data-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; margin-top: 0;">
            <h3 class="section-title" style="margin: 0;">Pencarian Siswa</h3>
            <a href="admin_kasir_riwayat.php" style="background: #e0f2fe; color: #0284c7; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: bold; text-decoration: none;"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat</a>
        </div>
        <div class="search-box" style="display: flex; gap: 10px;">
            <input type="text" id="searchInput" placeholder="Ketik Nama atau NIS siswa..." style="flex: 1; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc;" onkeyup="liveSearch()">
            <button onclick="searchSiswa()" style="background: #10b981; color: white; border: none; padding: 0 20px; border-radius: 8px; font-weight: bold;"><i class="fa-solid fa-search"></i></button>
        </div>
        
        <div id="searchResult" style="margin-top: 15px;"></div>
    </div>
    
    <div id="billContainer" style="display: none;"></div>
    
    <a href="guru_dashboard.php" class="btn" style="display: block; text-align: center; background: #f1f5f9; color: #475569; padding: 12px; border-radius: 50px; font-weight: 600; text-decoration: none; margin-bottom: 20px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
    </a>
</div>

<!-- Modal Bayar -->
<div id="modalBayar" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
    <div style="background:white; width:90%; max-width:400px; border-radius:12px; overflow:hidden;">
        <div style="background:#10b981; color:white; padding:15px; font-weight:bold; font-size:1.1rem; display:flex; justify-content:space-between;">
            <span>Proses Pembayaran</span>
            <i class="fa-solid fa-times" onclick="closeModal()" style="cursor:pointer;"></i>
        </div>
        <form method="POST" style="padding:20px;">
            <input type="hidden" name="action" value="bayar">
            <input type="hidden" name="siswa_id" id="bayar_siswa_id">
            <input type="hidden" name="tagihan_id" id="bayar_tagihan_id">
            
            <div style="margin-bottom:10px; font-size:0.85rem; color:#64748b;">Tagihan: <strong id="lbl_tagihan" style="color:#1e293b;">-</strong></div>
            <div style="margin-bottom:20px; font-size:0.85rem; color:#64748b;">Sisa Bayar: <strong id="lbl_sisa" style="color:#ef4444;">-</strong></div>
            
            <label style="display:block; margin-bottom:8px; font-weight:600; font-size:0.9rem;">Nominal Dibayar (Rp)</label>
            <input type="text" inputmode="numeric" id="input_nominal_display" required style="width:100%; padding:12px; border:1px solid #cbd5e1; border-radius:8px; font-size:1.2rem; font-weight:bold; margin-bottom:20px;" placeholder="0" oninput="formatNominal(this)">
            <input type="hidden" name="nominal" id="input_nominal">
            
            <button type="submit" style="width:100%; background:#10b981; color:white; border:none; padding:12px; border-radius:8px; font-weight:bold; font-size:1rem; cursor:pointer;">
                <i class="fa-solid fa-check"></i> Konfirmasi Pembayaran
            </button>
        </form>
    </div>
</div>

<script>
let searchTimeout;
let current_siswa_id = null;

function liveSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        var kw = document.getElementById('searchInput').value;
        if (kw.length >= 2) {
            searchSiswa();
        } else if (kw.length === 0) {
            document.getElementById('searchResult').innerHTML = '';
        }
    }, 400); // Debounce 400ms
}

function searchSiswa() {
    var kw = document.getElementById('searchInput').value;
    var res = document.getElementById('searchResult');
    if (kw.length < 2) return;
    
    res.innerHTML = '<div style="text-align:center; padding:10px;"><i class="fa-solid fa-spinner fa-spin" style="color:#10b981;"></i> Mencari...</div>';
    
    fetch('api_search_siswa.php?q=' + encodeURIComponent(kw))
        .then(r => r.json())
        .then(data => {
            if(data.length === 0) {
                res.innerHTML = '<div style="color:#ef4444; font-size:0.85rem; text-align:center; padding:10px;">Siswa tidak ditemukan.</div>';
                return;
            }
            var html = '<div style="display:flex; flex-direction:column; gap:10px;">';
            data.forEach(s => {
                var kls = s.nama_kelas ? s.nama_kelas : '-';
                html += `
                <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; display: flex; justify-content: space-between; align-items: center; background: white;">
                    <div>
                        <div style="font-weight:bold; font-size:0.95rem; color:#1e293b;">${s.nama}</div>
                        <div style="font-size:0.75rem; color:#64748b; margin-top:3px;">NIS: ${s.nis} | Kelas: ${kls}</div>
                    </div>
                    <button onclick="loadTagihan(${s.id}, '${s.nama.replace(/'/g, "\\'")}')" style="background:#10b981; color:white; border:none; padding:8px 15px; border-radius:6px; font-size:0.8rem; font-weight:bold; cursor:pointer;">Pilih</button>
                </div>
                `;
            });
            html += '</div>';
            res.innerHTML = html;
        });
}

function loadTagihan(siswa_id, nama) {
    current_siswa_id = siswa_id;
    document.getElementById('searchResult').innerHTML = '';
    document.getElementById('searchInput').value = nama;
    var container = document.getElementById('billContainer');
    container.style.display = 'block';
    container.innerHTML = '<div style="text-align:center; padding:20px;"><i class="fa-solid fa-spinner fa-spin" style="color:#10b981; font-size:1.5rem;"></i><p style="font-size:0.85rem; color:#64748b; margin-top:10px;">Memuat rincian tagihan...</p></div>';
    
    fetch('api_get_tagihan_siswa.php?siswa_id=' + siswa_id)
        .then(r => r.text())
        .then(html => {
            container.innerHTML = html;
        });
}

function bukaBayar(siswa_id, tagihan_id, nama_tagihan, sisa) {
    document.getElementById('bayar_siswa_id').value = siswa_id;
    document.getElementById('bayar_tagihan_id').value = tagihan_id;
    document.getElementById('lbl_tagihan').innerText = nama_tagihan;
    document.getElementById('lbl_sisa').innerText = "Rp " + new Intl.NumberFormat('id-ID').format(sisa);
    document.getElementById('input_nominal').value = sisa;
    document.getElementById('input_nominal_display').value = new Intl.NumberFormat('id-ID').format(sisa);
    
    document.getElementById('modalBayar').style.display = 'flex';
}

function closeModal() {
    document.getElementById('modalBayar').style.display = 'none';
}

function formatNominal(input) {
    let num = input.value.replace(/[^0-9]/g, '');
    document.getElementById('input_nominal').value = num;
    if (num !== '') {
        input.value = new Intl.NumberFormat('id-ID').format(num);
    } else {
        input.value = '';
    }
}

function bukaKwitansi(id) {
    window.location.href = 'admin_kasir_kwitansi.php?id=' + id;
}

function hapusPembayaran(id) {
    Swal.fire({
        title: 'Hapus Pembayaran?',
        text: "Saldo akan disesuaikan otomatis dan status tagihan diperbarui.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        backdrop: 'rgba(0,0,0,0.4)',
        customClass: {
            popup: 'swal2-custom-popup'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            var btn = event.currentTarget;
            var originalBtnHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
            btn.disabled = true;
            
            fetch('api_delete_pembayaran.php?id=' + id, {
                method: 'GET'
            }).then(r => r.json())
            .then(data => {
                if(data.success) {
                    showToast("Pembayaran berhasil dihapus!");
                    
                    // Update Total Saldo di layar tanpa perlu reload
                    if (data.new_saldo_formatted) {
                        document.getElementById('total_saldo_display').innerText = data.new_saldo_formatted;
                    }
                    
                    // Reload daftar tagihan untuk siswa ini
                    if(current_siswa_id) {
                        loadTagihan(current_siswa_id, document.getElementById('searchInput').value);
                    }
                } else {
                    showToast("Gagal menghapus: " + (data.message || "Unknown error"), true);
                    btn.innerHTML = originalBtnHtml;
                    btn.disabled = false;
                }
            }).catch(err => {
                showToast("Terjadi kesalahan jaringan.", true);
                btn.innerHTML = originalBtnHtml;
                btn.disabled = false;
            });
        }
    });
}

function showToast(message, isError = false) {
    // Hapus toast lama jika ada
    var old = document.getElementById('kasirToast');
    if (old) old.remove();
    
    var toast = document.createElement('div');
    toast.id = 'kasirToast';
    var bgColor = isError ? '#ef4444' : '#10b981';
    var icon = isError ? 'fa-circle-xmark' : 'fa-circle-check';
    
    toast.style.cssText = `position: fixed; top: 20px; left: 50%; transform: translateX(-50%); background: ${bgColor}; color: white; padding: 12px 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); font-size: 0.9rem; font-weight: 600; z-index: 99999; display: flex; align-items: center; gap: 10px; transition: opacity 0.3s;`;
    toast.innerHTML = `<i class="fa-solid ${icon}"></i> <span>${message}</span>`;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>

<?php include 'footer_guru.php'; ?>
