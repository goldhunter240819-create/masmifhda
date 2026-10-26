<?php
require_once 'koneksi.php';
if (!isset($_SESSION['guru_id'])) { header("Location: login.php"); exit; }

// Filter tanggal, default hari ini
$tanggal = $_GET['tgl'] ?? date('Y-m-d');

// Ambil semua guru
$gurus = [];
try {
    $stmt = $core_db->query("SELECT id as guru_id, nama FROM guru ORDER BY nama ASC");
    $gurus = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// Ambil absen dari siakad_db
$absens = [];
try {
    $stmt = $siakad_db->prepare("SELECT guru_id, status, waktu_masuk, waktu_keluar, keterangan FROM absensi_guru WHERE tanggal = ?");
    $stmt->execute([$tanggal]);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($res as $r) {
        $absens[$r['guru_id']] = $r;
    }
} catch (Exception $e) {}

// Gabung data
$rekap = [];
$hadir = 0; $terlambat = 0; $sakit = 0; $izin = 0; $alpa = 0; $belum_absen = 0;

foreach ($gurus as $g) {
    $g_id = $g['guru_id'];
    $status = null;
    $jam_masuk = null;
    $jam_pulang = null;
    $keterangan = null;
    
    if (isset($absens[$g_id])) {
        $a = $absens[$g_id];
        $status = $a['status'];
        $jam_masuk = $a['waktu_masuk'];
        $jam_pulang = $a['waktu_keluar'];
        $keterangan = $a['keterangan'];
    }
    
    $rekap[] = [
        'nama' => $g['nama'],
        'status' => $status,
        'jam_masuk' => $jam_masuk,
        'jam_pulang' => $jam_pulang,
        'keterangan' => $keterangan
    ];
    
    if (!$status) $belum_absen++;
    elseif ($status == 'Hadir') $hadir++;
    elseif ($status == 'Terlambat') $terlambat++;
    elseif ($status == 'Sakit') $sakit++;
    elseif ($status == 'Izin') $izin++;
    elseif ($status == 'Alpa') $alpa++;
}

$page_title = 'Monitor Absen Guru';
include 'header.php';
?>
<div class="header" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);">
    <div class="header-top">
        <a href="aplikasi_saya.php" style="color: white; font-size: 1.2rem; text-decoration: none;"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 style="color: white; font-size: 1.2rem; margin: 0; flex: 1; text-align: center;">Monitor Absen Guru</h1>
        <div style="width: 20px;"></div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: 10px; margin-bottom: 20px;">
        <!-- Form Filter Tanggal -->
        <form action="" method="GET" style="display: flex; gap: 10px; margin-bottom: 20px;">
            <input type="date" name="tgl" value="<?= htmlspecialchars($tanggal) ?>" style="flex: 1; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc; font-size: 0.95rem; outline: none;">
            <button type="submit" style="background: #4f46e5; color: #fff; border: none; padding: 0 15px; border-radius: 12px; font-weight: 700; display: flex; align-items: center; gap: 5px;">
                <i class="fa-solid fa-filter" style="width: 16px; height: 16px;"></i> Filter
            </button>
        </form>

        <!-- Statistik -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 10px;">
            <div style="background: #f0fdf4; border: 1px solid rgba(34,197,94,0.2); padding: 10px; border-radius: 12px; text-align: center;">
                <div style="font-size: 1.2rem; font-weight: 800; color: #16a34a;"><?= $hadir ?></div>
                <div style="font-size: 0.7rem; font-weight: 700; color: #16a34a; text-transform: uppercase;">Hadir</div>
            </div>
            <div style="background: #fffbeb; border: 1px solid rgba(245,158,11,0.2); padding: 10px; border-radius: 12px; text-align: center;">
                <div style="font-size: 1.2rem; font-weight: 800; color: #d97706;"><?= $terlambat ?></div>
                <div style="font-size: 0.7rem; font-weight: 700; color: #d97706; text-transform: uppercase;">T.Lambat</div>
            </div>
            <div style="background: #f8fafc; border: 1px solid rgba(100,116,139,0.2); padding: 10px; border-radius: 12px; text-align: center;">
                <div style="font-size: 1.2rem; font-weight: 800; color: #64748b;"><?= $belum_absen ?></div>
                <div style="font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase;">B.Absen</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 20px;">
            <div style="background: #eff6ff; border: 1px solid rgba(59,130,246,0.2); padding: 10px; border-radius: 12px; text-align: center;">
                <div style="font-size: 1.2rem; font-weight: 800; color: #3b82f6;"><?= $sakit ?></div>
                <div style="font-size: 0.7rem; font-weight: 700; color: #3b82f6; text-transform: uppercase;">Sakit</div>
            </div>
            <div style="background: #f5f3ff; border: 1px solid rgba(139,92,246,0.2); padding: 10px; border-radius: 12px; text-align: center;">
                <div style="font-size: 1.2rem; font-weight: 800; color: #8b5cf6;"><?= $izin ?></div>
                <div style="font-size: 0.7rem; font-weight: 700; color: #8b5cf6; text-transform: uppercase;">Izin</div>
            </div>
            <div style="background: #fef2f2; border: 1px solid rgba(239,68,68,0.2); padding: 10px; border-radius: 12px; text-align: center;">
                <div style="font-size: 1.2rem; font-weight: 800; color: #ef4444;"><?= $alpa ?></div>
                <div style="font-size: 0.7rem; font-weight: 700; color: #ef4444; text-transform: uppercase;">Alpa</div>
            </div>
        </div>

        <!-- Daftar Guru -->
        <h3 style="font-size: 0.95rem; font-weight: 800; color: #1e293b; margin-bottom: 12px; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px;">Daftar Presensi</h3>
        <div style="display: flex; flex-direction: column; gap: 10px;">
            <?php foreach ($rekap as $r): ?>
                <?php 
                    $bgColor = '#ffffff';
                    $statusColor = '#94a3b8';
                    $statusText = 'Belum Absen';
                    
                    if ($r['status'] == 'Hadir') {
                        $statusColor = '#22c55e';
                        $statusText = 'Hadir';
                    } elseif ($r['status'] == 'Terlambat') {
                        $statusColor = '#f59e0b';
                        $statusText = 'Terlambat';
                    } elseif ($r['status'] == 'Sakit') {
                        $statusColor = '#3b82f6';
                        $statusText = 'Sakit';
                    } elseif ($r['status'] == 'Izin') {
                        $statusColor = '#8b5cf6';
                        $statusText = 'Izin';
                    } elseif ($r['status'] == 'Alpa') {
                        $statusColor = '#ef4444';
                        $statusText = 'Alpa';
                        $bgColor = '#fef2f2';
                    }
                ?>
                <div style="background: <?= $bgColor ?>; padding: 12px; border-radius: 14px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
                    <div>
                        <div style="font-weight: bold; color: #1e293b; font-size: 0.85rem; margin-bottom: 5px;"><?= htmlspecialchars($r['nama']) ?></div>
                        <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;">
                            Masuk: <span style="font-weight: bold; color: #3b82f6;"><?= $r['jam_masuk'] ? substr($r['jam_masuk'], 0, 5) : '--:--' ?></span>
                            <span style="margin: 0 5px; color: #cbd5e1;">|</span>
                            Pulang: <span style="font-weight: bold; color: #ef4444;"><?= $r['jam_pulang'] ? substr($r['jam_pulang'], 0, 5) : '--:--' ?></span>
                        </div>
                    </div>
                    <div style="background: rgba(0,0,0,0.03); color: <?= $statusColor ?>; padding: 4px 10px; border-radius: 8px; font-weight: bold; font-size: 0.75rem; border: 1px solid <?= $statusColor ?>; white-space: nowrap;">
                        <?= $statusText ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'footer_guru.php'; ?>
