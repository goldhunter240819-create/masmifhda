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
$is_super_keuangan = false;
$allowed_tagihan = [];

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

// Get allowed categories based on allowed_tagihan
$allowed_kategori = [];
if (!$is_super_keuangan && !empty($allowed_tagihan)) {
    $in = str_repeat('?,', count($allowed_tagihan) - 1) . '?';
    $stmt_kat = $core_db->prepare("SELECT DISTINCT kategori FROM keuangan_komite_jenis WHERE nama_tagihan IN ($in)");
    $stmt_kat->execute($allowed_tagihan);
    $allowed_kategori = $stmt_kat->fetchAll(PDO::FETCH_COLUMN);
}

if (!$is_keuangan) {
    header("Location: akses_ditolak.php");
    exit;
}

$message = '';
$status = '';

// Kategori Filter
$filter_kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';

// Proses POST Simpan Transaksi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'simpan_transaksi') {
    $jenis = $_POST['jenis'] ?? '';
    $kategori = $_POST['kategori'] ?? '';
    $keterangan = trim($_POST['keterangan'] ?? '');
    $nominal = intval($_POST['nominal'] ?? 0);
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    
    if ($nominal > 0 && !empty($kategori) && !empty($jenis) && !empty($keterangan)) {
        if (!$is_super_keuangan && !in_array($kategori, $allowed_kategori)) {
            $message = 'Akses ditolak: Anda tidak memiliki hak akses untuk kategori ini.';
            $status = 'error';
        } else {
            try {
                // Handle Bukti Upload
                $bukti_filename = null;
                if (!empty($_FILES['bukti']['name'])) {
                    $bfile = $_FILES['bukti'];
                    $bext = strtolower(pathinfo($bfile['name'], PATHINFO_EXTENSION));
                    $ballowed = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
                    if (in_array($bext, $ballowed) && $bfile['size'] <= 5000000) {
                        $upload_dir = '../public/uploads/keuangan/';
                        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                        $bukti_filename = 'bukti_' . time() . '_' . rand(100,999) . '.' . $bext;
                        move_uploaded_file($bfile['tmp_name'], $upload_dir . $bukti_filename);
                    }
                }
                
                $stmt = $core_db->prepare("INSERT INTO keuangan_komite_transaksi (tanggal_transaksi, keterangan, jenis, kategori, jumlah, petugas_id, bukti) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$tanggal, $keterangan, $jenis, $kategori, $nominal, $user_id, $bukti_filename]);
                
                require_once __DIR__ . '/../vendor/autoload.php';
                if (class_exists('\App\Services\AuditLogger')) {
                    \App\Services\AuditLogger::dataChange('Keuangan', 'TRANSAKSI_BENDAHARA', "jenis={$jenis}, kategori={$kategori}, nominal={$nominal} (APK)");
                }
                
                $message = 'Transaksi berhasil disimpan!';
                $status = 'success';
            } catch (Exception $e) {
                $message = 'Gagal menyimpan transaksi: ' . $e->getMessage();
                $status = 'error';
            }
        }
    } else {
        $message = 'Mohon lengkapi semua data transaksi.';
        $status = 'error';
    }
}

// Ambil list kategori
if ($is_super_keuangan) {
    $kategori_list = $core_db->query("SELECT nama_kategori FROM keuangan_komite_kategori ORDER BY nama_kategori ASC")->fetchAll(PDO::FETCH_COLUMN);
} else {
    $kategori_list = $allowed_kategori;
}

// Kategori Filter (Dihapus)
$filter_kategori = ''; 

if (!$is_super_keuangan) {
    if (empty($allowed_kategori)) {
        $wherePembayaran = " WHERE 1=0";
        $whereManualMasuk = " WHERE 1=0";
        $whereManualKeluar = " WHERE 1=0";
        $whereTrans = " WHERE 1=0";
    } else {
        $kat_list_quoted = implode(',', array_map(function($k) use ($core_db) { return $core_db->quote($k); }, $allowed_kategori));
        $wherePembayaran = " WHERE p.jenis_pembayaran IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori IN ($kat_list_quoted))";
        $whereManualMasuk = " WHERE jenis='Pemasukan' AND kategori IN ($kat_list_quoted)";
        $whereManualKeluar = " WHERE jenis='Pengeluaran' AND kategori IN ($kat_list_quoted)";
        $whereTrans = " WHERE kategori IN ($kat_list_quoted)";
    }
} else {
    $wherePembayaran = "";
    $whereManualMasuk = " WHERE jenis='Pemasukan'";
    $whereManualKeluar = " WHERE jenis='Pengeluaran'";
    $whereTrans = "";
}

// Proses AJAX Load More
if (isset($_GET['action']) && $_GET['action'] == 'load_more') {
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
    $sqlRiwayat = "
        SELECT * FROM (
            SELECT id, tanggal_transaksi, keterangan, '' as sub_keterangan, jenis, kategori, jumlah, 'manual' as source 
            FROM keuangan_komite_transaksi 
            $whereTrans
            
            UNION ALL
            
            SELECT p.id, p.tanggal_bayar as tanggal_transaksi, 
                   p.jenis_pembayaran as keterangan, 
                   s.nama as sub_keterangan, 
                   'Pemasukan' as jenis, 
                   COALESCE(j.kategori, 'Lainnya') as kategori, 
                   p.jumlah, 
                   'pembayaran' as source
            FROM keuangan_komite_pembayaran p
            JOIN siswa s ON p.siswa_id = s.id
            LEFT JOIN keuangan_komite_jenis j ON TRIM(p.jenis_pembayaran) = TRIM(j.nama_tagihan)
            $wherePembayaran
        ) as gabungan
        ORDER BY tanggal_transaksi DESC, id DESC LIMIT 10 OFFSET $offset
    ";
    
    $items = $core_db->query($sqlRiwayat)->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($items);
    exit;
}

// Rincian Per Kategori
$rincian_kategori = [];
foreach ($kategori_list as $kat) {
    $kat_q = $core_db->quote($kat);
    $r_siswa = $core_db->query("SELECT SUM(jumlah) as total FROM keuangan_komite_pembayaran WHERE jenis_pembayaran IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori = $kat_q)")->fetch();
    $tot_siswa = $r_siswa ? floatval($r_siswa['total']) : 0;
    
    $r_in = $core_db->query("SELECT SUM(jumlah) as total FROM keuangan_komite_transaksi WHERE jenis='Pemasukan' AND kategori = $kat_q")->fetch();
    $tot_in = $r_in ? floatval($r_in['total']) : 0;
    
    $r_out = $core_db->query("SELECT SUM(jumlah) as total FROM keuangan_komite_transaksi WHERE jenis='Pengeluaran' AND kategori = $kat_q")->fetch();
    $tot_out = $r_out ? floatval($r_out['total']) : 0;
    
    $saldo_kat = ($tot_siswa + $tot_in) - $tot_out;
    $rincian_kategori[] = [
        'nama' => $kat,
        'saldo' => $saldo_kat
    ];
}

// Total Pembayaran Siswa
$r_siswa = $core_db->query("SELECT SUM(p.jumlah) as total FROM keuangan_komite_pembayaran p" . $wherePembayaran)->fetch();
$totalSiswa = $r_siswa ? floatval($r_siswa['total']) : 0;

// Total Pemasukan Manual
$r_manual_in = $core_db->query("SELECT SUM(jumlah) as total FROM keuangan_komite_transaksi" . $whereManualMasuk)->fetch();
$totalManualIn = $r_manual_in ? floatval($r_manual_in['total']) : 0;

// Total Pengeluaran
$r_manual_out = $core_db->query("SELECT SUM(jumlah) as total FROM keuangan_komite_transaksi" . $whereManualKeluar)->fetch();
$totalManualOut = $r_manual_out ? floatval($r_manual_out['total']) : 0;

$totalPemasukan = $totalSiswa + $totalManualIn;
$totalPengeluaran = $totalManualOut;
$saldo = $totalPemasukan - $totalPengeluaran;

// Ambil histori transaksi (gabung manual & siswa)
$sqlRiwayat = "
    SELECT * FROM (
        SELECT id, tanggal_transaksi, keterangan, '' as sub_keterangan, jenis, kategori, jumlah, 'manual' as source 
        FROM keuangan_komite_transaksi 
        $whereTrans
        
        UNION ALL
        
        SELECT p.id, p.tanggal_bayar as tanggal_transaksi, 
               p.jenis_pembayaran as keterangan, 
               s.nama as sub_keterangan, 
               'Pemasukan' as jenis, 
               COALESCE(j.kategori, 'Lainnya') as kategori, 
               p.jumlah, 
               'pembayaran' as source
        FROM keuangan_komite_pembayaran p
        JOIN siswa s ON p.siswa_id = s.id
        LEFT JOIN keuangan_komite_jenis j ON TRIM(p.jenis_pembayaran) = TRIM(j.nama_tagihan)
        $wherePembayaran
    ) as gabungan
    ORDER BY tanggal_transaksi DESC, id DESC LIMIT 10
";
$riwayat = $core_db->query($sqlRiwayat)->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Transaksi Bendahara';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding-bottom: 70px;">
    <div class="header-top">
        <div class="greeting">
            <p style="color: #94a3b8;">Admin Keuangan</p>
            <h1 style="color: white;">Transaksi Bendahara</h1>
        </div>
        <div class="avatar" style="color: #1e293b; background: white;">
            <i class="fa-solid fa-wallet"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <?php if(!empty($message)): ?>
    <div id="toastNotif" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); background: <?= $status=='success'?'#10b981':'#ef4444' ?>; color: white; padding: 12px 20px; border-radius: 30px; font-weight: 600; font-size: 0.9rem; z-index: 10000; box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: flex; align-items: center; gap: 8px; animation: slideDown 0.3s ease-out forwards;">
        <i class="fa-solid <?= $status=='success'?'fa-circle-check':'fa-circle-exclamation' ?>"></i> 
        <?= htmlspecialchars($message) ?>
    </div>
    <style>
    @keyframes slideDown { from { top: -50px; opacity: 0; } to { top: 20px; opacity: 1; } }
    @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }
    </style>
    <script>
        setTimeout(() => {
            const t = document.getElementById('toastNotif');
            if(t) {
                t.style.animation = 'fadeOut 0.3s ease-out forwards';
                setTimeout(() => t.remove(), 300);
            }
        }, 3000);
    </script>
    <?php endif; ?>

    <!-- Kartu Saldo Kas -->
    <div style="background: white; border-radius: 14px; padding: 12px 15px; box-shadow: 0 4px 15px -3px rgba(0,0,0,0.05); margin-top: -45px; z-index: 10; position: relative; margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: <?= !empty($rincian_kategori) ? '10px' : '0' ?>;">
            <div>
                <p style="margin: 0; color: #64748b; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Total Saldo Kas</p>
                <h2 style="margin: 2px 0 0; color: #0f172a; font-size: 1.35rem; font-weight: 800; letter-spacing: -0.5px;">Rp <?= number_format($saldo, 0, ',', '.') ?></h2>
            </div>
            <div style="text-align: right; display: flex; flex-direction: column; gap: 4px;">
                <div style="font-size: 0.75rem; color: #10b981; font-weight: 700; display: flex; align-items: center; justify-content: flex-end; gap: 4px;">
                    <i class="fa-solid fa-arrow-down" style="font-size: 0.6rem; background: #ecfdf5; padding: 3px; border-radius: 50%;"></i> Rp <?= number_format($totalPemasukan, 0, ',', '.') ?>
                </div>
                <div style="font-size: 0.75rem; color: #ef4444; font-weight: 700; display: flex; align-items: center; justify-content: flex-end; gap: 4px;">
                    <i class="fa-solid fa-arrow-up" style="font-size: 0.6rem; background: #fef2f2; padding: 3px; border-radius: 50%;"></i> Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?>
                </div>
            </div>
        </div>

        <?php if(!empty($rincian_kategori)): ?>
        <div style="border-top: 1px dashed #e2e8f0; padding-top: 8px;">
            <style>
                .hide-scroll::-webkit-scrollbar { display: none; }
                .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
            </style>
            <div class="hide-scroll" style="display: flex; gap: 8px; overflow-x: auto;">
                <?php foreach ($rincian_kategori as $rk): ?>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 4px 8px; flex-shrink: 0; display: flex; align-items: center; gap: 6px;">
                    <span style="font-size: 0.65rem; color: #64748b; font-weight: 600; white-space: nowrap; max-width: 100px; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($rk['nama']) ?></span>
                    <span style="font-size: 0.75rem; font-weight: 700; color: #0f172a;">Rp <?= number_format($rk['saldo'], 0, ',', '.') ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Action Buttons -->
    <div style="display: flex; gap: 15px; margin-top: 25px; margin-bottom: 25px;">
        <button onclick="openModal('Pemasukan')" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; padding: 14px; border-radius: 12px; font-weight: bold; font-size: 0.95rem; cursor: pointer; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35); transition: transform 0.2s;">
            <i class="fa-solid fa-arrow-down"></i> Catat Pemasukan
        </button>
        <button onclick="openModal('Pengeluaran')" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; padding: 14px; border-radius: 12px; font-weight: bold; font-size: 0.95rem; cursor: pointer; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.35); transition: transform 0.2s;">
            <i class="fa-solid fa-arrow-up"></i> Catat Pengeluaran
        </button>
    </div>

    <!-- Modal Transaksi -->
    <div id="transactionModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); z-index: 9999; justify-content: center; align-items: center; padding: 20px; backdrop-filter: blur(4px); opacity: 0; transition: opacity 0.3s ease; box-sizing: border-box;">
        <div id="modalContent" style="background: white; width: 100%; max-width: 500px; border-radius: 20px; padding: 20px 20px 25px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); transform: scale(0.95); opacity: 0; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); max-height: calc(100vh - 40px); overflow-y: auto; width: 100%;">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 id="formTitle" style="margin: 0; color: #10b981; font-size: 1.1rem; font-weight: 800;">Catat Pemasukan</h3>
                <button onclick="closeModal()" style="background: #f1f5f9; color: #64748b; border: none; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s;"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <form method="POST" enctype="multipart/form-data" onsubmit="return handleSimpan()">
                <input type="hidden" name="action" value="simpan_transaksi">
                <input type="hidden" name="jenis" id="inputJenis" value="Pemasukan">
                
                <div style="margin-bottom: 10px;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 3px; color: #475569;">Kategori</label>
                    <select name="kategori" required style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; background: #f8fafc; appearance: none;">
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach($kategori_list as $kat): ?>
                            <option value="<?= htmlspecialchars($kat) ?>"><?= htmlspecialchars($kat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div style="margin-bottom: 10px;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 3px; color: #475569;">Keterangan</label>
                    <input type="text" name="keterangan" required placeholder="Contoh detail transaksi..." style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; background: #f8fafc;">
                </div>
                
                <div style="margin-bottom: 10px;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 3px; color: #475569;">Nominal (Rp)</label>
                    <input type="number" name="nominal" required placeholder="0" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1.1rem; font-weight: bold; background: #f8fafc;">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 3px; color: #475569;">Tanggal Transaksi</label>
                    <input type="date" name="tanggal" required value="<?= date('Y-m-d') ?>" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; background: #f8fafc;">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <style>
                    #bukti_upload::file-selector-button {
                        background: #e2e8f0;
                        color: #475569;
                        padding: 6px 12px;
                        border-radius: 25px;
                        border: none;
                        font-size: 0.8rem;
                        font-weight: 600;
                        cursor: pointer;
                        margin-right: 10px;
                        transition: 0.2s;
                    }
                    #bukti_upload::file-selector-button:hover {
                        background: #cbd5e1;
                    }
                    </style>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 3px; color: #475569;">Upload Bukti <span style="font-weight: 400; color: #94a3b8;">(Opsional)</span></label>
                    <input type="file" name="bukti" id="bukti_upload" accept="image/*,.pdf" style="width: 100%; padding: 8px; border: 1px dashed #cbd5e1; border-radius: 8px; font-size: 0.8rem; color: #64748b; background: #f8fafc; cursor: pointer;">
                </div>
                
                <button type="submit" id="submitBtn" style="width: 100%; background: #10b981; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: bold; font-size: 1rem; cursor: pointer; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3); transition: transform 0.2s;">
                    <i class="fa-solid fa-paper-plane"></i> Simpan Pemasukan
                </button>
            </form>
        </div>
    </div>
    
    <div style="margin-top: 10px; margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between; padding: 0 5px;">
        <h4 style="margin: 0; font-size: 1rem; color: #1e293b;" id="riwayatTitle">Riwayat Transaksi</h4>
        <select id="filterRiwayat" onchange="filterList()" style="padding: 6px 12px; border-radius: 20px; border: 1px solid #cbd5e1; font-size: 0.8rem; background: white; font-weight: 600; color: #475569;">
            <option value="Semua">Semua</option>
            <option value="Pemasukan">Pemasukan</option>
            <option value="Pengeluaran">Pengeluaran</option>
        </select>
    </div>
    
    <!-- Riwayat List -->
    <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">
        <!-- Pemasukan List -->
        <div id="listRiwayat">
            <?php 
            $countTotal = count($riwayat);
            foreach($riwayat as $r): 
                $isMasuk = $r['jenis'] === 'Pemasukan';
                $color = $isMasuk ? '#10b981' : '#ef4444';
                $bgIcon = $isMasuk ? '#ecfdf5' : '#fef2f2';
                $icon = $isMasuk ? 'fa-arrow-down' : 'fa-arrow-up';
                $sign = $isMasuk ? '+' : '-';
                
                $main_title = !empty($r['sub_keterangan']) ? $r['sub_keterangan'] : $r['keterangan'];
                $sub_title = !empty($r['sub_keterangan']) ? $r['keterangan'] : '';
            ?>
                <div class="riwayat-item" data-jenis="<?= $r['jenis'] ?>" style="background: white; border-radius: 12px; padding: 15px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02); margin-bottom: 10px;">
                    <div style="display: flex; align-items: center; gap: 12px; flex: 1; overflow: hidden;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: <?= $bgIcon ?>; color: <?= $color ?>; flex-shrink: 0;">
                            <i class="fa-solid <?= $icon ?>"></i>
                        </div>
                        <div style="overflow: hidden;">
                            <div style="font-weight: 600; font-size: 0.95rem; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($main_title) ?></div>
                            <?php if(!empty($sub_title)): ?>
                            <div style="font-size: 0.8rem; color: #475569; margin-top: 2px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><i class="fa-solid fa-file-invoice-dollar" style="font-size: 0.7rem; color: #94a3b8; margin-right: 4px;"></i><?= htmlspecialchars($sub_title) ?></div>
                            <?php endif; ?>
                            <div style="font-size: 0.7rem; color: #64748b; margin-top: 3px;">
                                <?= date('d M Y', strtotime($r['tanggal_transaksi'])) ?> &bull; <?= htmlspecialchars($r['kategori']) ?>
                            </div>
                        </div>
                    </div>
                    <div style="text-align: right; margin-left: 10px; font-weight: bold; font-size: 0.95rem; color: <?= $color ?>;">
                        <?= $sign ?>Rp <?= number_format($r['jumlah'],0,',','.') ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if($countTotal == 0): ?>
                <div style="text-align: center; padding: 30px; background: white; border-radius: 12px; border: 1px dashed #cbd5e1; color: #94a3b8;">Belum ada transaksi.</div>
            <?php endif; ?>
        </div>
        
        <?php if($countTotal == 10): ?>
        <div style="text-align: center; margin-top: 10px; margin-bottom: 25px;">
            <button id="btnLoadMore" onclick="loadMore()" style="background: white; border: 1px solid #cbd5e1; padding: 10px 20px; border-radius: 20px; font-weight: 600; color: #475569; cursor: pointer; transition: 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                Lihat Selengkapnya <i class="fa-solid fa-chevron-down" style="margin-left: 5px;"></i>
            </button>
        </div>
        <?php endif; ?>
    </div>
    
    <a href="aplikasi_saya.php" class="btn" style="display: block; text-align: center; background: #f1f5f9; color: #475569; padding: 12px; border-radius: 50px; font-weight: 600; text-decoration: none; margin-bottom: 20px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
</div>

<script>
function openModal(jenis) {
    const modal = document.getElementById('transactionModal');
    const modalContent = document.getElementById('modalContent');
    const inputJenis = document.getElementById('inputJenis');
    const formTitle = document.getElementById('formTitle');
    document.getElementById('transactionModal').style.display = 'flex';
    document.getElementById('inputJenis').value = jenis;
    document.getElementById('formTitle').innerText = 'Catat ' + jenis;
    
    if(jenis === 'Pemasukan') {
        document.getElementById('formTitle').style.color = '#10b981';
        document.getElementById('submitBtn').style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
        document.getElementById('submitBtn').innerHTML = '<i class="fa-solid fa-paper-plane"></i> Simpan Pemasukan';
    } else {
        document.getElementById('formTitle').style.color = '#ef4444';
        document.getElementById('submitBtn').style.background = 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
        document.getElementById('submitBtn').innerHTML = '<i class="fa-solid fa-paper-plane"></i> Simpan Pengeluaran';
    }
    
    setTimeout(() => {
        document.getElementById('transactionModal').style.opacity = '1';
        document.getElementById('modalContent').style.transform = 'scale(1)';
        document.getElementById('modalContent').style.opacity = '1';
    }, 10);
}

function closeModal() {
    document.getElementById('transactionModal').style.opacity = '0';
    document.getElementById('modalContent').style.transform = 'scale(0.95)';
    document.getElementById('modalContent').style.opacity = '0';
    setTimeout(() => {
        document.getElementById('transactionModal').style.display = 'none';
    }, 300);
}

// Close when click outside content
document.getElementById('transactionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

function filterList() {
    const val = document.getElementById('filterRiwayat').value;
    const items = document.querySelectorAll('.riwayat-item');
    items.forEach(item => {
        if(val === 'Semua' || item.getAttribute('data-jenis') === val) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}

function handleSimpan() {
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Memproses...';
    btn.style.opacity = '0.7';
    // We don't disable the button completely because in some browsers it cancels the submit event.
    // Instead we use pointer-events to prevent double clicks
    btn.style.pointerEvents = 'none';
    return true;
}

let currentOffset = 10;
function loadMore() {
    const btn = document.getElementById('btnLoadMore');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Memuat...';
    btn.disabled = true;
    
    fetch(`admin_bendahara.php?action=load_more&offset=${currentOffset}`)
    .then(res => res.json())
    .then(data => {
        if(data.length > 0) {
            const list = document.getElementById('listRiwayat');
            data.forEach(r => {
                const isMasuk = r.jenis === 'Pemasukan';
                const color = isMasuk ? '#10b981' : '#ef4444';
                const bgIcon = isMasuk ? '#ecfdf5' : '#fef2f2';
                const icon = isMasuk ? 'fa-arrow-down' : 'fa-arrow-up';
                const sign = isMasuk ? '+' : '-';
                
                const nominal = new Intl.NumberFormat('id-ID').format(r.jumlah);
                
                const d = new Date(r.tanggal_transaksi);
                const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                const dateStr = `${d.getDate().toString().padStart(2, '0')} ${months[d.getMonth()]} ${d.getFullYear()}`;
                
                let mainTitle = r.sub_keterangan ? r.sub_keterangan : r.keterangan;
                let subTitle = r.sub_keterangan ? r.keterangan : '';
                
                let subKeteranganHTML = '';
                if(subTitle) {
                    subKeteranganHTML = `<div style="font-size: 0.8rem; color: #475569; margin-top: 2px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><i class="fa-solid fa-file-invoice-dollar" style="font-size: 0.7rem; color: #94a3b8; margin-right: 4px;"></i>${subTitle}</div>`;
                }
                
                const html = `
                <div class="riwayat-item" data-jenis="${r.jenis}" style="background: white; border-radius: 12px; padding: 15px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02); margin-bottom: 10px;">
                    <div style="display: flex; align-items: center; gap: 12px; flex: 1; overflow: hidden;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: ${bgIcon}; color: ${color}; flex-shrink: 0;">
                            <i class="fa-solid ${icon}"></i>
                        </div>
                        <div style="overflow: hidden;">
                            <div style="font-weight: 600; font-size: 0.95rem; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${mainTitle}</div>
                            ${subKeteranganHTML}
                            <div style="font-size: 0.7rem; color: #64748b; margin-top: 3px;">
                                ${dateStr} &bull; ${r.kategori}
                            </div>
                        </div>
                    </div>
                    <div style="text-align: right; margin-left: 10px; font-weight: bold; font-size: 0.95rem; color: ${color};">
                        ${sign}Rp ${nominal}
                    </div>
                </div>`;
                list.insertAdjacentHTML('beforeend', html);
            });
            
            currentOffset += 10;
            btn.innerHTML = originalText;
            btn.disabled = false;
            filterList();
            
            if(data.length < 10) {
                btn.style.display = 'none';
            }
        } else {
            btn.style.display = 'none';
        }
    })
    .catch(err => {
        console.error(err);
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}
</script>

<?php include 'footer_guru.php'; ?>
