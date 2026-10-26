<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];

// Cek Wali Kelas
$stmt_wk = $core_db->prepare("SELECT gt.id, gt.keterangan as nama_kelas, k.id as kelas_id FROM guru_tugas gt JOIN kelas k ON (gt.keterangan = CONCAT('Kelas ', k.nama_kelas) OR gt.keterangan = k.nama_kelas) WHERE gt.guru_id = :gid AND gt.tugas = 'Wali Kelas' LIMIT 1");
$stmt_wk->execute(['gid' => $guru_id]);
$wali_kelas = $stmt_wk->fetch(PDO::FETCH_ASSOC);

if (!$wali_kelas) {
    header("Location: akses_ditolak.php");
    exit;
}

$kelas_id = $wali_kelas['kelas_id'] ?? 0;
$nama_kelas_wk = $wali_kelas['nama_kelas'];

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
$m = date('m', strtotime($bulan . '-01'));
$y = date('Y', strtotime($bulan . '-01'));

// Trigger auto alpa
require_once __DIR__ . '/../vendor/autoload.php';
$start_date_auto = $y . '-' . $m . '-01';
$end_date_auto = date('Y-m-t', strtotime($start_date_auto));
\App\Controllers\AbsenV2Controller::autoGenerateAlpaSiswa($start_date_auto, $end_date_auto);

// Ambil siswa kelas ini
$stmt_siswa = $core_db->prepare("SELECT id, nama, nis, foto FROM siswa WHERE kelas_id = :kid AND status = 'Aktif' ORDER BY nama ASC");
$stmt_siswa->execute(['kid' => $kelas_id]);
$siswa_list = $stmt_siswa->fetchAll(PDO::FETCH_ASSOC);

// Ambil rekap absensi
$rekap = [];
if (!empty($siswa_list)) {
    $ids = array_column($siswa_list, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    // Asumsi tabel absen di siakad_db bernama absensi_siswa
    // Atau jika core_db, kita coba siakad_db
    try {
        $q = "SELECT siswa_id, status, COUNT(*) as total FROM absensi_siswa WHERE siswa_id IN ($placeholders) AND MONTH(tanggal) = ? AND YEAR(tanggal) = ? GROUP BY siswa_id, status";
        $params = $ids;
        $params[] = $m;
        $params[] = $y;
        $stmt_absen = $siakad_db->prepare($q);
        $stmt_absen->execute($params);
        $hasil_absen = $stmt_absen->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($hasil_absen as $a) {
            $sid = $a['siswa_id'];
            $st = $a['status'];
            if (!isset($rekap[$sid])) {
                $rekap[$sid] = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0];
            }
            if (isset($rekap[$sid][$st])) {
                $rekap[$sid][$st] = $a['total'];
            }
        }
    } catch(Exception $e) {}
}

$page_title = 'Rekap Absensi Kelas';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); padding-top: 3.5rem;">
    <div class="header-top">
        <div class="greeting">
            <p>Buku Wali Kelas</p>
            <h1>Rekap Kehadiran</h1>
        </div>
        <div class="avatar" style="color: #16a34a; background: white;">
            <i class="fa-solid fa-user-check"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 class="section-title" style="margin: 0;"><i class="fa-solid fa-calendar-days" style="color: #16a34a; margin-right: 5px;"></i> Bulan</h3>
            <form method="GET" style="margin: 0;">
                <input type="month" name="bulan" value="<?= $bulan ?>" onchange="this.form.submit()" style="padding: 6px 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.8rem; background: #f8fafc; outline: none;">
            </form>
        </div>
        
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 20px;">
            <div style="font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 8px;"><i class="fa-solid fa-print"></i> Cetak Rekap Absensi</div>
            <form action="/siakad/guru/wali-kelas/absen/cetak" target="_blank" method="GET" style="margin: 0;">
                <div style="display: flex; gap: 8px; margin-bottom: 10px;">
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 0.7rem; color: #64748b; margin-bottom: 3px;">Dari Tanggal</label>
                        <input type="date" name="start" value="<?= $bulan ?>-01" required style="width: 100%; padding: 6px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.8rem;">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 0.7rem; color: #64748b; margin-bottom: 3px;">Sampai Tanggal</label>
                        <input type="date" name="end" value="<?= date('Y-m-t', strtotime($bulan . '-01')) ?>" required style="width: 100%; padding: 6px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.8rem;">
                    </div>
                </div>
                <button type="submit" style="width: 100%; background: #16a34a; color: white; border: none; padding: 10px; border-radius: 6px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">
                    <i class="fa-solid fa-print"></i> Cetak Rekap Absensi
                </button>
            </form>
        </div>
        
        <?php if (empty($siswa_list)): ?>
            <div style="text-align: center; padding: 20px; color: #64748b;">
                <i class="fa-solid fa-users-slash" style="font-size: 2rem; opacity: 0.3; margin-bottom: 10px; display: block;"></i>
                <p style="font-size: 0.85rem;">Belum ada siswa di kelas ini.</p>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <?php foreach ($siswa_list as $idx => $s): 
                    $sid = $s['id'];
                    $h = $rekap[$sid]['Hadir'] ?? 0;
                    $i = $rekap[$sid]['Izin'] ?? 0;
                    $sk = $rekap[$sid]['Sakit'] ?? 0;
                    $a = $rekap[$sid]['Alpa'] ?? 0;
                    $total_hadir = $h;
                    $total_hari = $h + $i + $sk + $a;
                    $persen = $total_hari > 0 ? round(($total_hadir / $total_hari) * 100) : 0;
                ?>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px; border-bottom: 1px dashed #cbd5e1; padding-bottom: 10px;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; background: #e2e8f0; display: flex; justify-content: center; align-items: center; color: #94a3b8; font-size: 1.2rem;">
                                <?php if (!empty($s['foto'])): ?>
                                    <img src="/public/thumb.php?src=uploads/siswa/<?= urlencode($s['foto']) ?>&size=100" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.outerHTML='<i class=\'fa-solid fa-user\'></i>';">
                                <?php else: ?>
                                    <i class="fa-solid fa-user"></i>
                                <?php endif; ?>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 700; font-size: 0.95rem; color: #1e293b;"><?= htmlspecialchars($s['nama']) ?></div>
                                <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">NIS: <?= htmlspecialchars($s['nis']) ?></div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-weight: 800; font-size: 1rem; color: <?= $persen >= 80 ? '#16a34a' : ($persen >= 60 ? '#f59e0b' : '#ef4444') ?>;"><?= $persen ?>%</div>
                                <div style="font-size: 0.65rem; color: #94a3b8;">Kehadiran</div>
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; text-align: center;">
                            <div style="background: #dcfce7; padding: 6px; border-radius: 6px;">
                                <div style="font-size: 0.9rem; font-weight: 800; color: #15803d;"><?= $h ?></div>
                                <div style="font-size: 0.65rem; color: #166534; font-weight: 600;">Hadir</div>
                            </div>
                            <div style="background: #fef3c7; padding: 6px; border-radius: 6px;">
                                <div style="font-size: 0.9rem; font-weight: 800; color: #b45309;"><?= $sk ?></div>
                                <div style="font-size: 0.65rem; color: #92400e; font-weight: 600;">Sakit</div>
                            </div>
                            <div style="background: #e0f2fe; padding: 6px; border-radius: 6px;">
                                <div style="font-size: 0.9rem; font-weight: 800; color: #0369a1;"><?= $i ?></div>
                                <div style="font-size: 0.65rem; color: #075985; font-weight: 600;">Izin</div>
                            </div>
                            <div style="background: #fee2e2; padding: 6px; border-radius: 6px;">
                                <div style="font-size: 0.9rem; font-weight: 800; color: #b91c1c;"><?= $a ?></div>
                                <div style="font-size: 0.65rem; color: #991b1b; font-weight: 600;">Alpa</div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <a href="wali_kelas.php" class="btn" style="display: block; text-align: center; background: #f1f5f9; color: #475569; padding: 12px; border-radius: 50px; font-weight: 600; text-decoration: none; margin-bottom: 20px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Menu Wali Kelas
    </a>
</div>

<?php include 'footer_guru.php'; ?>
