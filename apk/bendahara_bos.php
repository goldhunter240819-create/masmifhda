<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];
$nama_guru = $_SESSION['nama'] ?? 'Guru';

// Handle Delete Transaksi
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    // Cek dulu apakah ini id terakhir atau bebas hapus? Sebaiknya dilarang hapus tengah untuk menjaga saldo,
    // tapi untuk sementara biarkan hapus dan kita biarkan saldo (nanti bisa ditambahkan fitur recalculate).
    // Tapi karena manual sederhana, hapus saja barisnya.
    $stmt_del = $core_db->prepare("DELETE FROM bos_bku WHERE id = ?");
    $stmt_del->execute([$del_id]);
    
    // Recalculate saldo
    $core_db->exec("
        SET @saldo := 0;
        UPDATE bos_bku
        SET saldo = (@saldo := @saldo + IFNULL(penerimaan, 0) - IFNULL(pengeluaran, 0))
        ORDER BY tanggal ASC, id ASC;
    ");
    
    header("Location: bendahara_bos.php?msg=del_success");
    exit;
}

// Handle Insert Transaksi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_bku') {
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    $no_bukti = $_POST['no_bukti'] ?? '';
    $uraian = $_POST['uraian'] ?? '';
    $jenis_transaksi = $_POST['jenis_transaksi'] ?? 'Tunai';
    $jenis_dana = $_POST['jenis_dana'] ?? 'Pemasukan';
    $nominal = floatval(str_replace(['Rp', '.', ' '], '', $_POST['nominal'] ?? '0'));
    
    $penerimaan = ($jenis_dana == 'Pemasukan') ? $nominal : 0;
    $pengeluaran = ($jenis_dana == 'Pengeluaran') ? $nominal : 0;
    
    // Handle File Upload
    $file_bukti = null;
    if (isset($_FILES['file_bukti']) && $_FILES['file_bukti']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/bos_bukti/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_tmp = $_FILES['file_bukti']['tmp_name'];
        $file_name = time() . '_' . preg_replace("/[^a-zA-Z0-9.-]/", "_", $_FILES['file_bukti']['name']);
        if (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
            $file_bukti = $file_name;
        }
    }
    
    // Get last saldo (for reference before recalculation)
    $stmt_last = $core_db->query("SELECT saldo FROM bos_bku ORDER BY tanggal DESC, id DESC LIMIT 1");
    $last_row = $stmt_last->fetch();
    $last_saldo = $last_row ? floatval($last_row['saldo']) : 0;
    
    $new_saldo = $last_saldo + $penerimaan - $pengeluaran;

    try {
        $stmt_ins = $core_db->prepare("
            INSERT INTO bos_bku (tanggal, no_bukti, file_bukti, uraian, penerimaan, pengeluaran, saldo, jenis_transaksi) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt_ins->execute([
            $tanggal, $no_bukti, $file_bukti, $uraian, $penerimaan, $pengeluaran, $new_saldo, $jenis_transaksi
        ]);
        
        // Always recalculate to be safe if inserted out of order
        $core_db->exec("
            SET @saldo := 0;
            UPDATE bos_bku
            SET saldo = (@saldo := @saldo + IFNULL(penerimaan, 0) - IFNULL(pengeluaran, 0))
            ORDER BY tanggal ASC, id ASC;
        ");
        
        header("Location: bendahara_bos.php?msg=success");
        exit;
    } catch (Exception $e) {
        $error = "Gagal menyimpan: " . $e->getMessage();
    }
}

// Get Total Summary
$stmt_sum = $core_db->query("
    SELECT 
        SUM(penerimaan) as total_terima, 
        SUM(pengeluaran) as total_keluar 
    FROM bos_bku
");
$sum = $stmt_sum->fetch();
$total_terima = $sum['total_terima'] ?? 0;
$total_keluar = $sum['total_keluar'] ?? 0;
$saldo_akhir = $total_terima - $total_keluar;

// Get Transactions (last 50 for performance on mobile)
$stmt_bku = $core_db->query("SELECT * FROM bos_bku ORDER BY tanggal DESC, id DESC LIMIT 50");
$bkuList = $stmt_bku->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Buku Kas Umum (BOS)';
include 'header.php';
?>
<style>
    .summary-card {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        position: relative;
        z-index: 10;
        margin-top: -3rem;
    }
    
    .saldo-box {
        background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
        color: white;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        margin-bottom: 15px;
        box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
    }
    .saldo-box h2 {
        font-size: 2rem; margin: 5px 0 0 0; font-weight: 800;
    }
    
    .stats-row {
        display: flex; gap: 10px;
    }
    .stat-item {
        flex: 1; padding: 12px; border-radius: 10px; text-align: center;
    }
    .stat-item.in { background: #d1fae5; color: #059669; }
    .stat-item.out { background: #fee2e2; color: #dc2626; }
    .stat-item .val { font-size: 1.1rem; font-weight: 800; margin-top: 5px; }
    
    .data-card {
        background: #fff; border-radius: 16px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 20px;
    }
    .input-group label {
        display: block; margin-bottom: 8px; font-weight: 600; color: #475569; font-size: 0.9rem;
    }
    .form-control {
        width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; margin-bottom: 15px;
    }
    .btn-submit {
        background: #f43f5e; color: white; border: none; padding: 12px; border-radius: 10px; font-weight: bold; width: 100%; font-size: 1rem; cursor: pointer; transition: 0.2s;
    }
    .btn-submit:hover { background: #e11d48; }
    
    .bku-item {
        border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; margin-bottom: 12px; background: #fff;
    }
    .bku-item .tgl {
        font-size: 0.75rem; color: #64748b; font-weight: 600; margin-bottom: 5px;
    }
    .bku-item .uraian {
        font-weight: 700; color: #1e293b; font-size: 0.95rem; margin-bottom: 8px;
    }
    .bku-item .nominal {
        font-size: 1.1rem; font-weight: 800;
    }
    .bku-item .nominal.in { color: #10b981; }
    .bku-item .nominal.out { color: #ef4444; }
    
    .btn-del {
        color: #ef4444; background: #fee2e2; border: none; padding: 5px 10px; border-radius: 6px; font-size: 0.8rem; text-decoration: none; display: inline-block;
    }
</style>

<div class="header" style="background: linear-gradient(135deg, #f43f5e 0%, #be123c 100%); padding-bottom: 4rem;">
    <div class="header-top">
        <div class="greeting">
            <p>Keuangan BOS</p>
            <h1>Buku Kas Umum (BKU)</h1>
        </div>
        <a href="aplikasi_saya.php" class="avatar" style="color: #f43f5e; background: white; text-decoration: none;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>
</div>

<div class="main-content">
    
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({ icon: 'success', title: 'Tersimpan!', text: 'Transaksi BOS berhasil dicatat.', confirmButtonColor: '#f43f5e' });
            });
        </script>
    <?php endif; ?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'del_success'): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({ icon: 'success', title: 'Terhapus!', text: 'Transaksi dan saldo telah diperbarui.', confirmButtonColor: '#f43f5e' });
            });
        </script>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div style="background: #fee2e2; color: #ef4444; padding: 12px; border-radius: 8px; margin-bottom: 15px; text-align: center; font-weight: bold;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Summary Cards -->
    <div class="summary-card">
        <div class="saldo-box">
            <div style="font-size: 0.9rem; opacity: 0.9; text-transform: uppercase; letter-spacing: 1px;">Saldo Kas BOS Saat Ini</div>
            <h2>Rp <?= number_format($saldo_akhir, 0, ',', '.') ?></h2>
        </div>
        <div class="stats-row">
            <div class="stat-item in">
                <div style="font-size: 0.75rem; font-weight: 600;">TOTAL PEMASUKAN</div>
                <div class="val">Rp <?= number_format($total_terima, 0, ',', '.') ?></div>
            </div>
            <div class="stat-item out">
                <div style="font-size: 0.75rem; font-weight: 600;">TOTAL PENGELUARAN</div>
                <div class="val">Rp <?= number_format($total_keluar, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
    
    <!-- Form Input Transaksi -->
    <div class="data-card">
        <h3 style="font-size: 1.1rem; color: #1e293b; margin-bottom: 15px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;"><i class="fa-solid fa-pen-to-square" style="color: #f43f5e;"></i> Catat Transaksi BKU</h3>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="action" value="save_bku">
            
            <div class="input-group">
                <label>Pilih Jenis Transaksi Terlebih Dahulu <span style="color:red">*</span></label>
                <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <label id="lbl-pemasukan" style="flex: 1; background: #f8fafc; border: 1px solid #cbd5e1; padding: 12px; border-radius: 8px; text-align: center; cursor: pointer; transition: 0.2s;">
                        <input type="radio" name="jenis_dana" value="Pemasukan" style="margin-right: 5px;" onclick="showForm('pemasukan')">
                        <span style="color: #10b981; font-weight: 600;">Pemasukan</span>
                    </label>
                    <label id="lbl-pengeluaran" style="flex: 1; background: #f8fafc; border: 1px solid #cbd5e1; padding: 12px; border-radius: 8px; text-align: center; cursor: pointer; transition: 0.2s;">
                        <input type="radio" name="jenis_dana" value="Pengeluaran" style="margin-right: 5px;" onclick="showForm('pengeluaran')">
                        <span style="color: #ef4444; font-weight: 600;">Pengeluaran</span>
                    </label>
                </div>
            </div>
            
            <div id="form-details" style="display: none; animation: fadeIn 0.3s ease-in-out;">
                
                <div style="display: flex; gap: 10px;">
                    <div class="input-group" style="flex: 1;">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required value="<?= date('Y-m-d') ?>" id="input-tanggal">
                    </div>
                    <div class="input-group" style="flex: 1;">
                        <label>No. Bukti (Kwitansi)</label>
                        <input type="text" name="no_bukti" class="form-control" placeholder="Opsional...">
                    </div>
                </div>
                
                <div class="input-group">
                    <label>Uraian / Keterangan Transaksi <span style="color:red">*</span></label>
                    <textarea name="uraian" rows="2" class="form-control" placeholder="Contoh: Pembelian buku siswa semester 1" id="input-uraian"></textarea>
                </div>
                
                <div class="input-group">
                    <label>Jenis Transaksi (Kas) <span style="color:red">*</span></label>
                    <select name="jenis_transaksi" class="form-control">
                        <option value="Tunai">Tunai (Cash)</option>
                        <option value="Bank">Bank (Transfer/Cek)</option>
                    </select>
                </div>
                
                <div class="input-group">
                    <label>Nominal (Rp) <span style="color:red">*</span></label>
                    <input type="text" name="nominal" class="form-control" placeholder="1.000.000" onkeyup="formatRupiah(this)" style="font-weight: bold; font-size: 1.1rem; color: #1e293b;" id="input-nominal">
                </div>
            
                <div class="input-group">
                    <label>Upload Bukti / Nota (Opsional)</label>
                    <input type="file" name="file_bukti" class="form-control" accept="image/*,.pdf" style="padding: 9px;">
                </div>
            
                <button type="submit" class="btn-submit"><i class="fa-solid fa-save"></i> Simpan Transaksi</button>
            </div>
        </form>
    </div>
    
    <!-- Riwayat Transaksi -->
    <h3 style="font-size: 1.1rem; color: #1e293b; margin-bottom: 15px; margin-top: 30px;">Riwayat BKU BOS (50 Terakhir)</h3>
    
    <?php if(count($bkuList) > 0): ?>
        <?php foreach($bkuList as $bku): ?>
        <div class="bku-item">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 5px;">
                <div class="tgl"><i class="fa-regular fa-calendar"></i> <?= date('d M Y', strtotime($bku['tanggal'])) ?> <?= $bku['no_bukti'] ? ' | Bukti: '.$bku['no_bukti'] : '' ?></div>
                <div style="font-size: 0.75rem; background: #e2e8f0; padding: 2px 8px; border-radius: 4px; font-weight: bold; color: #475569;"><?= $bku['jenis_transaksi'] ?></div>
            </div>
            
            <div class="uraian"><?= htmlspecialchars($bku['uraian']) ?></div>
            
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 10px;">
                <?php if($bku['penerimaan'] > 0): ?>
                    <div>
                        <div style="font-size: 0.7rem; color: #64748b; font-weight: bold;">PEMASUKAN</div>
                        <div class="nominal in">+ Rp <?= number_format($bku['penerimaan'], 0, ',', '.') ?></div>
                    </div>
                <?php elseif($bku['pengeluaran'] > 0): ?>
                    <div>
                        <div style="font-size: 0.7rem; color: #64748b; font-weight: bold;">PENGELUARAN</div>
                        <div class="nominal out">- Rp <?= number_format($bku['pengeluaran'], 0, ',', '.') ?></div>
                    </div>
                <?php else: ?>
                    <div></div>
                <?php endif; ?>
                
                <div style="text-align: right;">
                    <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 5px;">Sisa Kas: <strong>Rp <?= number_format($bku['saldo'], 0, ',', '.') ?></strong></div>
                    <a href="?delete_id=<?= $bku['id'] ?>" class="btn-del" onclick="return confirm('Hapus transaksi ini? Saldo BKU akan dihitung ulang secara otomatis.')"><i class="fa-solid fa-trash"></i> Hapus</a>
                </div>
            </div>
            
            <!-- Tampilkan Bukti -->
            <?php if(!empty($bku['file_bukti'])): ?>
            <div style="margin-top: 10px; padding-top: 8px; border-top: 1px dashed #cbd5e1; font-size: 0.8rem;">
                <a href="../uploads/bos_bukti/<?= htmlspecialchars($bku['file_bukti']) ?>" target="_blank" style="color: #0ea5e9; text-decoration: none; font-weight: bold;"><i class="fa-solid fa-paperclip"></i> Lihat Bukti Terlampir</a>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="text-align: center; padding: 20px; color: #94a3b8; font-style: italic; background: #fff; border-radius: 12px; border: 1px dashed #cbd5e1;">
            Belum ada transaksi BKU BOS.
        </div>
    <?php endif; ?>
    
    <div style="height: 30px;"></div>
</div>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
<script>
function showForm(type) {
    document.getElementById('form-details').style.display = 'block';
    
    // Set required fields
    document.getElementById('input-tanggal').required = true;
    document.getElementById('input-uraian').required = true;
    document.getElementById('input-nominal').required = true;
    
    // Update styling for labels
    const lblIn = document.getElementById('lbl-pemasukan');
    const lblOut = document.getElementById('lbl-pengeluaran');
    
    if(type === 'pemasukan') {
        lblIn.style.border = '2px solid #34d399';
        lblIn.style.background = '#d1fae5';
        lblOut.style.border = '1px solid #cbd5e1';
        lblOut.style.background = '#f8fafc';
    } else {
        lblOut.style.border = '2px solid #fca5a5';
        lblOut.style.background = '#fee2e2';
        lblIn.style.border = '1px solid #cbd5e1';
        lblIn.style.background = '#f8fafc';
    }
}

function formatRupiah(input) {
    let value = input.value.replace(/[^,\d]/g, '').toString();
    let split = value.split(',');
    let sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    input.value = rupiah;
}
</script>

<?php include 'footer_guru.php'; ?>