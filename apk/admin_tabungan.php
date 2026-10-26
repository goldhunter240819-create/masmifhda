<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];
$user_id = $_SESSION['user_id'] ?? 0;

// Cek hak akses tabungan
$stmt_app = $core_db->prepare("SELECT app_code FROM app_admins WHERE user_id = :uid AND app_code = 'tabungan' LIMIT 1");
$stmt_app->execute(['uid' => $user_id]);
$has_access = (bool)$stmt_app->fetch();
if (!$has_access) {
    try {
        $stmt_kta = $core_db->prepare("SELECT id FROM keuangan_tabungan_admin WHERE user_id = :uid LIMIT 1");
        $stmt_kta->execute(['uid' => $user_id]);
        if ($stmt_kta->fetch()) $has_access = true;
    } catch(Exception $e) {}
}
if (!$has_access) {
    header("Location: akses_ditolak.php");
    exit;
}

$page_title = 'Kasir Tabungan';
include 'header.php';

$message = '';
$status = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $siswa_id = intval($_POST['siswa_id']);
    $jenis = $_POST['action']; // 'setor' atau 'tarik'
    $nominal = intval($_POST['nominal']);
    $catatan = $_POST['catatan'] ?? '';
    
    if ($nominal > 0) {
        try {
            $core_db->beginTransaction();
            
            // Cek buku tabungan, jika tidak ada, buatkan.
            $stmt_cek = $core_db->prepare("SELECT id, saldo FROM keuangan_tabungan WHERE siswa_id = ? FOR UPDATE");
            $stmt_cek->execute([$siswa_id]);
            $tabungan = $stmt_cek->fetch(PDO::FETCH_ASSOC);
            
            $saldo_sekarang = 0;
            $tabungan_id = 0;
            
            if (!$tabungan) {
                throw new Exception("Siswa ini belum membuka rekening tabungan! Silakan aktifkan rekening terlebih dahulu.");
            }
            $tabungan_id = $tabungan['id'];
            $saldo_sekarang = $tabungan['saldo'];
            
            if ($jenis === 'tarik' && $saldo_sekarang < $nominal) {
                throw new Exception("Saldo tidak mencukupi untuk tarik tunai.");
            }
            
            // Masukkan mutasi
            $saldo_sebelum = $saldo_sekarang;
            $saldo_sesudah = ($jenis === 'setor') ? ($saldo_sebelum + $nominal) : ($saldo_sebelum - $nominal);
            $jenis_mutasi = ($jenis === 'setor') ? 'Setor' : 'Tarik';
            $stmt_mutasi = $core_db->prepare("INSERT INTO keuangan_tabungan_mutasi (siswa_id, jenis_mutasi, jumlah, saldo_sebelum, saldo_sesudah, tanggal, keterangan) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
            $stmt_mutasi->execute([$siswa_id, $jenis_mutasi, $nominal, $saldo_sebelum, $saldo_sesudah, $catatan]);
            
            // Update Saldo
            if ($jenis === 'setor') {
                $core_db->prepare("UPDATE keuangan_tabungan SET saldo = saldo + ? WHERE id = ?")->execute([$nominal, $tabungan_id]);
            } else {
                $core_db->prepare("UPDATE keuangan_tabungan SET saldo = saldo - ? WHERE id = ?")->execute([$nominal, $tabungan_id]);
            }
            
            
            $core_db->commit();
            
            require_once __DIR__ . '/../vendor/autoload.php';
            if (class_exists('\App\Services\AuditLogger')) {
                \App\Services\AuditLogger::dataChange('Tabungan', 'MUTASI', "siswa_id={$siswa_id}, jenis={$jenis}, nominal={$nominal} (APK)");
            }

            $verb = ($jenis === 'setor') ? 'Setoran' : 'Penarikan';
            $message = $verb . ' sebesar Rp '.number_format($nominal,0,',','.').' berhasil!';
            
            // --- KIRIM PUSH NOTIFICATION ---
            try {
                require_once __DIR__ . '/../app/Services/OneSignalService.php';
                $nominal_rp = "Rp " . number_format($nominal, 0, ',', '.');
                $notif_title = "Mutasi Tabungan: " . $verb;
                $notif_msg = "Terdapat transaksi $verb tabungan sebesar $nominal_rp pada rekening siswa. Saldo saat ini: Rp " . number_format($saldo_sesudah, 0, ',', '.');
                \App\Services\OneSignalService::sendNotification(
                    $notif_title, 
                    $notif_msg, 
                    "https://mismifhda.my.id/apk/index.php",
                    [$siswa_id]
                );
            } catch (Exception $e) {}

            $status = 'success';
        } catch (Exception $e) {
            $core_db->rollBack();
            $message = 'Transaksi Gagal: ' . $e->getMessage();
            $status = 'error';
        }
    }
}

// Hitung Statistik Tabungan Keseluruhan
$total_rekening = 0;
$total_saldo_tabungan = 0;
$setor_hari_ini = 0;
$tarik_hari_ini = 0;
try {
    $stmt_stat = $core_db->query("SELECT COUNT(*) as cnt, COALESCE(SUM(saldo), 0) as total FROM keuangan_tabungan");
    $row_stat = $stmt_stat->fetch(PDO::FETCH_ASSOC);
    if ($row_stat) {
        $total_rekening = intval($row_stat['cnt']);
        $total_saldo_tabungan = floatval($row_stat['total']);
    }

    $stmt_today = $core_db->query("SELECT 
        COALESCE(SUM(CASE WHEN jenis_mutasi = 'Setor' THEN jumlah ELSE 0 END), 0) as setor,
        COALESCE(SUM(CASE WHEN jenis_mutasi = 'Tarik' THEN jumlah ELSE 0 END), 0) as tarik
        FROM keuangan_tabungan_mutasi WHERE DATE(tanggal) = CURDATE()");
    $row_today = $stmt_today->fetch(PDO::FETCH_ASSOC);
    if ($row_today) {
        $setor_hari_ini = floatval($row_today['setor']);
        $tarik_hari_ini = floatval($row_today['tarik']);
    }
} catch (Exception $e) {}

?>

<div class="header" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
    <div class="header-top">
        <div class="greeting">
            <p>Admin Keuangan</p>
            <h1>Kasir Tabungan</h1>
        </div>
        <div class="avatar" style="color: #f59e0b; background: white;">
            <i class="fa-solid fa-wallet"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <?php if(!empty($message)): ?>
        <div style="background: <?= $status=='success'?'#ecfdf5':'#fee2e2' ?>; border-left: 4px solid <?= $status=='success'?'#10b981':'#ef4444' ?>; padding: 15px; border-radius: 8px; margin-top: -20px; margin-bottom: 20px; z-index: 10; position: relative;">
            <p style="margin:0; color: <?= $status=='success'?'#065f46':'#b91c1c' ?>; font-size: 0.9rem; font-weight: 600;"><?= $message ?></p>
        </div>
    <?php endif; ?>

    <!-- KARTU RINGKASAN TOTAL SALDO TABUNGAN -->
    <div class="data-card" <?= empty($message) ? 'style="margin-top: -2.5rem; z-index: 10; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; border: none; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3); margin-bottom: 1.5rem;"' : 'style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; border: none; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3); margin-bottom: 1.5rem;"' ?>>
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
            <div>
                <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Total Saldo Terkelola</div>
                <div style="font-size: 1.45rem; font-weight: 800; color: #f59e0b; margin-top: 4px;">Rp <?= number_format($total_saldo_tabungan, 0, ',', '.') ?></div>
            </div>
            <div style="background: rgba(245, 158, 11, 0.2); color: #fcd34d; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-users"></i> <?= $total_rekening ?> Rekening
            </div>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 12px;">
            <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); padding: 10px; border-radius: 10px;">
                <div style="font-size: 0.7rem; color: #6ee7b7; font-weight: 600;"><i class="fa-solid fa-arrow-down" style="margin-right: 4px;"></i> Setor Hari Ini</div>
                <div style="font-size: 0.95rem; font-weight: 700; color: #34d399; margin-top: 2px;">+ Rp <?= number_format($setor_hari_ini, 0, ',', '.') ?></div>
            </div>
            <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); padding: 10px; border-radius: 10px;">
                <div style="font-size: 0.7rem; color: #fca5a5; font-weight: 600;"><i class="fa-solid fa-arrow-up" style="margin-right: 4px;"></i> Tarik Hari Ini</div>
                <div style="font-size: 0.95rem; font-weight: 700; color: #f87171; margin-top: 2px;">- Rp <?= number_format($tarik_hari_ini, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>

    <div class="data-card">
        <h3 class="section-title" style="margin-top: 0;">Pencarian Siswa</h3>
        <div class="search-box" style="display: flex; gap: 10px;">
            <input type="text" id="searchInput" placeholder="Ketik Nama atau NIS siswa..." style="flex: 1; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc;" onkeyup="liveSearch()">
            <button onclick="searchSiswa()" style="background: #f59e0b; color: white; border: none; padding: 0 20px; border-radius: 8px; font-weight: bold;"><i class="fa-solid fa-search"></i></button>
        </div>
        
        <div id="searchResult" style="margin-top: 15px;"></div>
    </div>
    
    <div id="tabunganContainer" style="display: none;"></div>

    <a href="admin_tabungan_daftar.php" style="display: block; background: white; border: 1px solid #cbd5e1; color: #334155; padding: 15px; border-radius: 8px; text-decoration: none; font-weight: bold; margin-top: 15px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <div>
            <i class="fa-solid fa-list" style="color: #3b82f6; margin-right: 10px;"></i>
            Lihat Semua Rekening Siswa
        </div>
        <i class="fa-solid fa-chevron-right" style="color: #94a3b8;"></i>
    </a>
    
    <a href="guru_dashboard.php" class="btn" style="display: block; text-align: center; background: #f1f5f9; color: #475569; padding: 12px; border-radius: 50px; font-weight: 600; text-decoration: none; margin-bottom: 20px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
    </a>
</div>

<!-- Modal Transaksi -->
<div id="modalMutasi" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
    <div style="background:white; width:90%; max-width:400px; border-radius:12px; overflow:hidden;">
        <div id="modalMutasiHeader" style="background:#f59e0b; color:white; padding:15px; font-weight:bold; font-size:1.1rem; display:flex; justify-content:space-between;">
            <span id="lbl_judul_mutasi">Proses Mutasi</span>
            <i class="fa-solid fa-times" onclick="closeModal()" style="cursor:pointer;"></i>
        </div>
        <form method="POST" style="padding:20px;">
            <input type="hidden" name="action" id="mutasi_action">
            <input type="hidden" name="siswa_id" id="mutasi_siswa_id">
            
            <div style="margin-bottom:10px; font-size:0.85rem; color:#64748b;">Saldo Saat Ini: <strong id="lbl_saldo" style="color:#1e293b;">-</strong></div>
            
            <label style="display:block; margin-bottom:8px; font-weight:600; font-size:0.9rem;">Nominal (Rp)</label>
            <input type="text" inputmode="numeric" id="input_nominal_display" required style="width:100%; padding:12px; border:1px solid #cbd5e1; border-radius:8px; font-size:1.2rem; font-weight:bold; margin-bottom:15px;" placeholder="0" oninput="formatNominal(this)">
            <input type="hidden" name="nominal" id="input_nominal">
            
            <label style="display:block; margin-bottom:8px; font-weight:600; font-size:0.9rem;">Catatan</label>
            <input type="text" name="catatan" style="width:100%; padding:12px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.95rem; margin-bottom:20px;" placeholder="Contoh: Menabung uang saku">
            
            <button type="submit" id="btnMutasiSubmit" style="width:100%; background:#f59e0b; color:white; border:none; padding:12px; border-radius:8px; font-weight:bold; font-size:1rem; cursor:pointer;">
                <i class="fa-solid fa-check"></i> Konfirmasi Transaksi
            </button>
        </form>
    </div>
</div>

<script>
let searchTimeout;

document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($_GET['cari']) && !empty($_GET['cari'])): ?>
        var kw = <?= json_encode($_GET['cari']) ?>;
        document.getElementById('searchInput').value = kw;
        // Panggil pencarian secara manual
        searchSiswa();
        document.getElementById('tabunganContainer').style.display = 'none';
    <?php endif; ?>
});

function liveSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        var kw = document.getElementById('searchInput').value;
        if (kw.length >= 2) {
            searchSiswa();
            document.getElementById('tabunganContainer').style.display = 'none';
        } else if (kw.length === 0) {
            document.getElementById('searchResult').innerHTML = '';
            document.getElementById('tabunganContainer').style.display = 'none';
        }
    }, 400); // Debounce 400ms
}

function searchSiswa() {
    var kw = document.getElementById('searchInput').value;
    var res = document.getElementById('searchResult');
    if (kw.length < 2) return;
    
    res.innerHTML = '<div style="text-align:center; padding:10px;"><i class="fa-solid fa-spinner fa-spin" style="color:#f59e0b;"></i> Mencari...</div>';
    
    fetch('api_search_siswa.php?module=tabungan&q=' + encodeURIComponent(kw))
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
                    <button onclick="loadTabungan(${s.id}, '${s.nama.replace(/'/g, "\\'")}')" style="background:#f59e0b; color:white; border:none; padding:8px 15px; border-radius:6px; font-size:0.8rem; font-weight:bold; cursor:pointer;">Pilih</button>
                </div>
                `;
            });
            html += '</div>';
            res.innerHTML = html;
        });
}

function loadTabungan(siswa_id, nama) {
    document.getElementById('searchResult').innerHTML = '';
    document.getElementById('searchInput').value = nama;
    
    var container = document.getElementById('tabunganContainer');
    container.style.display = 'block';
    container.innerHTML = '<div style="text-align:center; padding:20px;"><i class="fa-solid fa-spinner fa-spin" style="color:#f59e0b; font-size:1.5rem;"></i><p style="font-size:0.85rem; color:#64748b; margin-top:10px;">Memuat data tabungan...</p></div>';
    
    fetch('api_get_tabungan_siswa.php?siswa_id=' + siswa_id)
        .then(r => r.text())
        .then(html => {
            container.innerHTML = html;
        });
}

function bukaMutasi(jenis, siswa_id, saldo) {
    document.getElementById('mutasi_action').value = jenis;
    document.getElementById('mutasi_siswa_id').value = siswa_id;
    document.getElementById('lbl_saldo').innerText = "Rp " + new Intl.NumberFormat('id-ID').format(saldo);
    
    document.getElementById('input_nominal').value = '';
    document.getElementById('input_nominal_display').value = '';
    
    if (jenis === 'setor') {
        document.getElementById('lbl_judul_mutasi').innerText = "Setor Tunai Tabungan";
        document.getElementById('modalMutasiHeader').style.background = "#10b981";
        document.getElementById('btnMutasiSubmit').style.background = "#10b981";
        document.getElementById('btnMutasiSubmit').innerHTML = '<i class="fa-solid fa-plus-circle"></i> Konfirmasi Setoran';
    } else {
        document.getElementById('lbl_judul_mutasi').innerText = "Tarik Tunai Tabungan";
        document.getElementById('modalMutasiHeader').style.background = "#ef4444";
        document.getElementById('btnMutasiSubmit').style.background = "#ef4444";
        document.getElementById('btnMutasiSubmit').innerHTML = '<i class="fa-solid fa-minus-circle"></i> Konfirmasi Penarikan';
    }
    
    document.getElementById('modalMutasi').style.display = 'flex';
}

function closeModal() {
    document.getElementById('modalMutasi').style.display = 'none';
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
</script>

<?php include 'footer_guru.php'; ?>
