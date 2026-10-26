<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$is_kamad = false;
$guru_id = $_SESSION['guru_id'];
try {
    $stmt_g = $core_db->prepare("SELECT is_kamad, nama FROM guru WHERE id = :id LIMIT 1");
    $stmt_g->execute(['id' => $guru_id]);
    $guru_data = $stmt_g->fetch(PDO::FETCH_ASSOC);
    if ($guru_data && $guru_data['is_kamad'] == 1) {
        $is_kamad = true;
    }
} catch (Exception $e) {}

if (!$is_kamad) {
    header("Location: akses_ditolak.php");
    exit;
}

// Hitung Monitoring Keuangan
$bos_pemasukan = 0;
$bos_pengeluaran = 0;
$bos_saldo = 0;

try {
    // --- BOS ---
    $r_bos = $core_db->query("
        SELECT 
            SUM(CASE WHEN jenis='Pemasukan' THEN jumlah ELSE 0 END) as in_tot,
            SUM(CASE WHEN jenis='Pengeluaran' THEN jumlah ELSE 0 END) as out_tot
        FROM keuangan_bos_transaksi
    ")->fetch(PDO::FETCH_ASSOC);
    
    if ($r_bos) {
        $bos_pemasukan = floatval($r_bos['in_tot']);
        $bos_pengeluaran = floatval($r_bos['out_tot']);
    }
    $bos_saldo = $bos_pemasukan - $bos_pengeluaran;
    
    $bos_data = [
        'nama' => 'DANA BOS',
        'petugas' => 'Bendahara BOS',
        'saldo' => $bos_saldo,
        'pemasukan' => $bos_pemasukan,
        'pengeluaran' => $bos_pengeluaran
    ];
    
} catch(Exception $e) {}

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}

$page_title = 'Monitoring Keuangan';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
    <div class="header-top">
        <a href="aplikasi_saya.php" style="color: white; font-size: 1.2rem; text-decoration: none;"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 style="color: white; font-size: 1.2rem; margin: 0; flex: 1; text-align: center;">Monitoring BOS</h1>
        <div style="width: 20px;"></div>
    </div>
</div>

<div class="main-content">


    <!-- BOS CARD -->
    <div class="data-card" style="margin-top: 15px; margin-bottom: 20px;">
        <div style="font-size: 1.1rem; font-weight: bold; color: #1e293b; margin-bottom: 15px; border-bottom: 2px solid #3b82f6; display: inline-block; padding-bottom: 4px;">Dana BOS</div>
        
        <div style="display: grid; grid-template-columns: 1fr; gap: 15px; margin-bottom: 20px;">
            <div style="background: #ffffff; border-radius: 12px; padding: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 4px solid #3b82f6;">
                <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; margin-bottom: 5px;">Total Saldo BOS</div>
                <div style="font-size: 0.95rem; font-weight: bold; color: #3b82f6;"><?= formatRupiah($bos_data['saldo']) ?></div>
            </div>
        </div>

        <div style="font-size: 0.85rem; font-weight: bold; color: #475569; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px; margin-bottom: 10px;">Rekap Dana BOS</div>
        <div style="padding: 12px 0; display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;">
            <div>
                <div style="font-weight: bold; color: #1e293b; font-size: 0.85rem; letter-spacing: 0.2px;"><?= htmlspecialchars($bos_data['nama']) ?></div>
                <div style="margin-top: 5px;">
                    <span style="font-size: 0.68rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; display: block;">Petugas</span>
                    <span style="font-size: 0.78rem; color: #475569; font-weight: bold; margin-top: 1px; display: flex; align-items: center; gap: 4px;">
                        <i class="fa-solid fa-user-tie" style="color: #6366f1; font-size: 0.75rem;"></i>
                        <?= htmlspecialchars($bos_data['petugas']) ?>
                    </span>
                </div>
            </div>
            <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 4px; min-width: 120px;">
                <div style="font-size: 0.7rem; color: #16a34a; background: #f0fdf4; border: 1px solid #dcfce7; font-weight: 700; padding: 2px 6px; border-radius: 4px; white-space: nowrap; display: inline-block;">
                    Masuk: <?= formatRupiah($bos_data['pemasukan']) ?>
                </div>
                <div style="font-size: 0.7rem; color: #dc2626; background: #fef2f2; border: 1px solid #fee2e2; font-weight: 700; padding: 2px 6px; border-radius: 4px; white-space: nowrap; display: inline-block;">
                    Keluar: <?= formatRupiah($bos_data['pengeluaran']) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer_guru.php'; ?>
