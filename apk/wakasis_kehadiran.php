<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$today = date('Y-m-d');

// Total Siswa
try {
    $stmt_total = $core_db->query("SELECT COUNT(id) as total FROM siswa WHERE status = 'Aktif'");
    $totalSiswa = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) {
    $totalSiswa = 0;
}

// Kehadiran Hari ini (Hadir, Izin, Sakit, Alpha)
$hadir = 0;
$izin = 0;
$sakit = 0;
$alpha = 0;
$bolos = 0;
$terlambat = 0;

try {
    $stmt_hadir = $siakad_db->prepare("SELECT status, COUNT(*) as jumlah FROM absensi_siswa WHERE tanggal = ? GROUP BY status");
    $stmt_hadir->execute([$today]);
    while($row = $stmt_hadir->fetch(PDO::FETCH_ASSOC)) {
        if ($row['status'] == 'Hadir') $hadir = $row['jumlah'];
        elseif ($row['status'] == 'Izin') $izin = $row['jumlah'];
        elseif ($row['status'] == 'Sakit') $sakit = $row['jumlah'];
        elseif ($row['status'] == 'Alpha' || $row['status'] == 'Alpa') $alpha = $row['jumlah'];
        elseif ($row['status'] == 'Bolos') $bolos = $row['jumlah'];
        elseif ($row['status'] == 'Terlambat') $terlambat = $row['jumlah'];
    }
} catch (Exception $e) {
}

$persentase = $totalSiswa > 0 ? round(($hadir / $totalSiswa) * 100, 1) : 0;

// Data Grafik 7 hari terakhir
$grafik = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    try {
        $stmt = $siakad_db->prepare("SELECT COUNT(*) FROM absensi_siswa WHERE tanggal = ? AND status = 'Hadir'");
        $stmt->execute([$date]);
        $grafik[date('d M', strtotime($date))] = $stmt->fetchColumn();
    } catch (Exception $e) {
        $grafik[date('d M', strtotime($date))] = 0;
    }
}

$page_title = 'Statistik Kehadiran';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #4338ca 0%, #312e81 100%); padding-bottom: 70px;">
    <div class="header-top">
        <div class="greeting">
            <p style="color: #c7d2fe;">Waka Kesiswaan</p>
            <h1 style="color: white;">Statistik Kehadiran</h1>
        </div>
        <div class="avatar" style="color: #4338ca; background: white;">
            <i class="fa-solid fa-chart-pie"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div style="background: #fff; border-radius: 16px; padding: 20px; box-shadow: 0 4px 15px -3px rgba(0,0,0,0.05); text-align: center; margin-bottom: 20px; margin-top: -45px; z-index: 10; position: relative;">
        <h4 style="margin: 0 0 10px 0; font-size: 0.9rem; color: #64748b; text-transform: uppercase; letter-spacing: 1px;">Kehadiran Hari Ini</h4>
        <div style="position: relative; width: 120px; height: 120px; margin: 0 auto; border-radius: 50%; background: conic-gradient(#10b981 <?= $persentase ?>%, #f1f5f9 0); display: flex; align-items: center; justify-content: center;">
            <div style="width: 100px; height: 100px; background: #fff; border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);">
                <span style="font-size: 1.8rem; font-weight: 800; color: #1e293b; line-height: 1;"><?= $persentase ?>%</span>
                <span style="font-size: 0.7rem; font-weight: 700; color: #64748b; margin-top: 2px;">Hadir</span>
            </div>
        </div>
        <div style="font-size: 0.85rem; color: #94a3b8; font-weight: 600; margin-top: 15px;">
            Dari total <?= $totalSiswa ?> siswa aktif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 25px;">
        <div style="background: #ecfdf5; border: 1px solid rgba(16,185,129,0.2); border-radius: 16px; padding: 15px; text-align: center; box-shadow: 0 2px 10px rgba(16,185,129,0.05);">
            <div style="font-size: 1.5rem; font-weight: 800; color: #059669;"><?= $hadir ?></div>
            <div style="font-size: 0.75rem; font-weight: 700; color: #10b981; text-transform: uppercase;">Hadir</div>
        </div>
        <div style="background: #eff6ff; border: 1px solid rgba(59,130,246,0.2); border-radius: 16px; padding: 15px; text-align: center; box-shadow: 0 2px 10px rgba(59,130,246,0.05);">
            <div style="font-size: 1.5rem; font-weight: 800; color: #2563eb;"><?= $izin ?></div>
            <div style="font-size: 0.75rem; font-weight: 700; color: #3b82f6; text-transform: uppercase;">Izin</div>
        </div>
        <div style="background: #fffbeb; border: 1px solid rgba(245,158,11,0.2); border-radius: 16px; padding: 15px; text-align: center; box-shadow: 0 2px 10px rgba(245,158,11,0.05);">
            <div style="font-size: 1.5rem; font-weight: 800; color: #d97706;"><?= $sakit ?></div>
            <div style="font-size: 0.75rem; font-weight: 700; color: #f59e0b; text-transform: uppercase;">Sakit</div>
        </div>
        <div style="background: #fef2f2; border: 1px solid rgba(239,68,68,0.2); border-radius: 16px; padding: 15px; text-align: center; box-shadow: 0 2px 10px rgba(239,68,68,0.05);">
            <div style="font-size: 1.5rem; font-weight: 800; color: #dc2626;"><?= $alpha ?></div>
            <div style="font-size: 0.75rem; font-weight: 700; color: #ef4444; text-transform: uppercase;">Alpha</div>
        </div>
        <div style="background: #fdf2f8; border: 1px solid rgba(219,39,119,0.2); border-radius: 16px; padding: 15px; text-align: center; box-shadow: 0 2px 10px rgba(219,39,119,0.05);">
            <div style="font-size: 1.5rem; font-weight: 800; color: #db2777;"><?= $bolos ?></div>
            <div style="font-size: 0.75rem; font-weight: 700; color: #db2777; text-transform: uppercase;">Bolos</div>
        </div>
        <div style="background: #fefce8; border: 1px solid rgba(202,138,4,0.2); border-radius: 16px; padding: 15px; text-align: center; box-shadow: 0 2px 10px rgba(202,138,4,0.05);">
            <div style="font-size: 1.5rem; font-weight: 800; color: #ca8a04;"><?= $terlambat ?></div>
            <div style="font-size: 0.75rem; font-weight: 700; color: #ca8a04; text-transform: uppercase;">Terlambat</div>
        </div>
    </div>

    <h4 style="font-size: 0.9rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; margin-left: 5px;">Tren 7 Hari Terakhir</h4>
    <div style="background: #fff; padding: 15px; border-radius: 16px; box-shadow: 0 4px 15px -3px rgba(0,0,0,0.05); display: flex; align-items: flex-end; gap: 8px; height: 160px; overflow-x: auto;">
        <?php 
        $maxVal = max($grafik) ?: 1; 
        foreach($grafik as $tgl => $val): 
            $height = ($val / $maxVal) * 100;
        ?>
        <div style="flex: 1; display: flex; flex-direction: column; justify-content: flex-end; min-width: 30px; height: 100%;">
            <div style="font-size: 0.7rem; font-weight: 700; color: #4338ca; margin-bottom: 5px; text-align: center;"><?= $val ?></div>
            <div style="flex: 1; display: flex; align-items: flex-end; justify-content: center;">
                <div style="width: 100%; background: #e0e7ff; border-radius: 6px 6px 0 0; height: <?= max($height, 5) ?>%; position: relative; overflow: hidden; transition: 0.3s;">
                    <div style="position: absolute; bottom: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to top, #4338ca, #6366f1); opacity: 0.9;"></div>
                </div>
            </div>
            <div style="font-size: 0.6rem; font-weight: 600; color: #94a3b8; margin-top: 5px; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%;"><?= $tgl ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <a href="aplikasi_saya.php" class="btn" style="display: block; text-align: center; background: #f1f5f9; color: #475569; padding: 12px; border-radius: 50px; font-weight: 600; text-decoration: none; margin-top: 25px; margin-bottom: 20px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
</div>

<?php include 'footer_guru.php'; ?>
