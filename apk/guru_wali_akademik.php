<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];
$siswa_id = isset($_GET['siswa_id']) ? intval($_GET['siswa_id']) : 0;

if (!$siswa_id) {
    echo "<script>alert('Siswa tidak ditemukan.'); window.location.href='guru_wali.php?action=pantau';</script>";
    exit;
}

// Cek apakah siswa ini adalah anak asuh dari guru ini
$stmt_siswa = $core_db->prepare("
    SELECT s.*, k.nama_kelas 
    FROM guru_wali_siswa gws
    JOIN aula_db.siswa s ON gws.siswa_id = s.id 
    LEFT JOIN aula_db.kelas k ON s.kelas_id = k.id 
    WHERE gws.guru_id = :gid AND s.id = :sid
");
$stmt_siswa->execute(['gid' => $guru_id, 'sid' => $siswa_id]);
$siswa = $stmt_siswa->fetch(PDO::FETCH_ASSOC);

if (!$siswa) {
    echo "<script>alert('Siswa ini bukan anak asuh Anda.'); window.location.href='guru_wali.php?action=pantau';</script>";
    exit;
}

$bulan = date('Y-m');
$m = date('m');
$y = date('Y');

// Fungsi bantu untuk normalisasi status
function normalizeStatus($st) {
    $st = ucfirst(strtolower(trim($st)));
    if ($st == 'Alpha' || $st == 'A') return 'Alpa';
    if ($st == 'H') return 'Hadir';
    if ($st == 'I') return 'Izin';
    if ($st == 'S') return 'Sakit';
    if ($st == 'B') return 'Bolos';
    return $st;
}

// Ambil rekap absensi siakad_db (Bulan Ini)
$rekap_absen = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0, 'Bolos' => 0];
try {
    $stmt_absen = $siakad_db->prepare("SELECT status, COUNT(*) as total FROM absensi_siswa WHERE siswa_id = ? AND MONTH(tanggal) = ? AND YEAR(tanggal) = ? GROUP BY status");
    $stmt_absen->execute([$siswa_id, $m, $y]);
    $hasil_absen = $stmt_absen->fetchAll(PDO::FETCH_ASSOC);
    foreach ($hasil_absen as $a) {
        $st = normalizeStatus($a['status']);
        if (isset($rekap_absen[$st])) {
            $rekap_absen[$st] += $a['total'];
        }
    }
} catch(Exception $e) {}

// Ambil rekap absensi siakad_db (Riwayat Semester Ini - Bulan Sebelumnya)
$start_month = ($m >= 7) ? 7 : 1;
$first_day = date('Y-m-01');

$histori_absen = [];
try {
    $stmt_hist = $siakad_db->prepare("SELECT MONTH(tanggal) as bln, YEAR(tanggal) as thn, status, COUNT(*) as total 
                                      FROM absensi_siswa 
                                      WHERE siswa_id = ? 
                                        AND tanggal < ? 
                                        AND MONTH(tanggal) >= ? 
                                        AND YEAR(tanggal) = ? 
                                      GROUP BY YEAR(tanggal), MONTH(tanggal), status 
                                      ORDER BY YEAR(tanggal) DESC, MONTH(tanggal) DESC");
    $stmt_hist->execute([$siswa_id, $first_day, $start_month, $y]);
    
    $raw_hist = $stmt_hist->fetchAll(PDO::FETCH_ASSOC);
    foreach ($raw_hist as $row) {
        $bln = (int)$row['bln'];
        $st = normalizeStatus($row['status']);
        
        if (!isset($histori_absen[$bln])) {
            $histori_absen[$bln] = ['Hadir'=>0, 'Sakit'=>0, 'Izin'=>0, 'Alpa'=>0, 'Bolos'=>0, 'thn' => $row['thn']];
        }
        if (isset($histori_absen[$bln][$st])) {
            $histori_absen[$bln][$st] += $row['total'];
        }
    }
} catch(Exception $e) {}

$page_title = 'Pantau Akademik';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%); padding-top: 3.5rem;">
    <div class="header-top">
        <div class="greeting">
            <p>Akademik Anak Asuh</p>
            <h1>Pantau Akademik</h1>
        </div>
        <div class="avatar" style="color: #0ea5e9; background: white;">
            <i class="fa-solid fa-chart-line"></i>
        </div>
    </div>
</div>

<div class="main-content">
    
    <!-- Profil Siswa Minimalis -->
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10; display: flex; align-items: center; gap: 15px;">
        <div style="width: 50px; height: 50px; border-radius: 50%; overflow: hidden; background: #e0f2fe; display: flex; justify-content: center; align-items: center; color: #0284c7; font-size: 1.5rem; flex-shrink: 0;">
            <?php if (!empty($siswa['foto'])): ?>
                <img src="/public/uploads/siswa/<?= htmlspecialchars($siswa['foto']) ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.outerHTML='<i class=\'fa-solid fa-user\'></i>';">
            <?php else: ?>
                <i class="fa-solid fa-user"></i>
            <?php endif; ?>
        </div>
        <div>
            <h3 style="margin: 0; font-size: 1.1rem; color: #0f172a;"><?= htmlspecialchars($siswa['nama']) ?></h3>
            <p style="margin: 2px 0 0 0; color: #64748b; font-size: 0.8rem;">NIS: <?= htmlspecialchars($siswa['nis']) ?> &bull; Kelas <?= htmlspecialchars($siswa['nama_kelas']) ?></p>
        </div>
    </div>

    <!-- Rekap Kehadiran Bulan Ini -->
    <h3 class="section-title" style="margin-top: 20px;"><i class="fa-solid fa-calendar-check" style="color: #10b981; margin-right: 5px;"></i> Absensi Bulan Ini (<?= date('F Y') ?>)</h3>
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 25px;">
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; display: flex; align-items: center; gap: 10px;">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #ecfdf5; color: #10b981; display: flex; justify-content: center; align-items: center; font-size: 1.2rem;">
                <i class="fa-solid fa-check-double"></i>
            </div>
            <div>
                <div style="color: #64748b; font-size: 0.75rem; font-weight: 600;">Hadir</div>
                <div style="color: #0f172a; font-size: 1.2rem; font-weight: 700;"><?= $rekap_absen['Hadir'] ?></div>
            </div>
        </div>
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; display: flex; align-items: center; gap: 10px;">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #fef2f2; color: #ef4444; display: flex; justify-content: center; align-items: center; font-size: 1.2rem;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <div style="color: #64748b; font-size: 0.75rem; font-weight: 600;">Alpa</div>
                <div style="color: #0f172a; font-size: 1.2rem; font-weight: 700;"><?= $rekap_absen['Alpa'] ?></div>
            </div>
        </div>
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; display: flex; align-items: center; gap: 10px;">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #fffbeb; color: #f59e0b; display: flex; justify-content: center; align-items: center; font-size: 1.2rem;">
                <i class="fa-solid fa-bed-pulse"></i>
            </div>
            <div>
                <div style="color: #64748b; font-size: 0.75rem; font-weight: 600;">Sakit</div>
                <div style="color: #0f172a; font-size: 1.2rem; font-weight: 700;"><?= $rekap_absen['Sakit'] ?></div>
            </div>
        </div>
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; display: flex; align-items: center; gap: 10px;">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #eff6ff; color: #3b82f6; display: flex; justify-content: center; align-items: center; font-size: 1.2rem;">
                <i class="fa-solid fa-envelope-open-text"></i>
            </div>
            <div>
                <div style="color: #64748b; font-size: 0.75rem; font-weight: 600;">Izin</div>
                <div style="color: #0f172a; font-size: 1.2rem; font-weight: 700;"><?= $rekap_absen['Izin'] ?></div>
            </div>
        </div>
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; display: flex; align-items: center; gap: 10px; grid-column: 1 / -1;">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #fdf2f8; color: #db2777; display: flex; justify-content: center; align-items: center; font-size: 1.2rem;">
                <i class="fa-solid fa-person-walking-arrow-right"></i>
            </div>
            <div>
                <div style="color: #64748b; font-size: 0.75rem; font-weight: 600;">Bolos</div>
                <div style="color: #0f172a; font-size: 1.2rem; font-weight: 700;"><?= $rekap_absen['Bolos'] ?></div>
            </div>
        </div>
    </div>
    
    <!-- Riwayat Kehadiran Bulan Sebelumnya (Minimalis) -->
    <h3 class="section-title" style="margin-top: 20px;"><i class="fa-solid fa-clock-rotate-left" style="color: #8b5cf6; margin-right: 5px;"></i> Riwayat Bulan Sebelumnya</h3>
    <?php if (empty($histori_absen)): ?>
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; text-align: center; color: #64748b; font-size: 0.9rem; margin-bottom: 25px;">
            Belum ada riwayat absensi bulan sebelumnya di semester ini.
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 25px;">
            <?php 
            $namaBulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
            foreach ($histori_absen as $bln => $data): 
            ?>
            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 15px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                <div style="font-weight: 700; color: #334155; font-size: 0.85rem;">
                    <?= $namaBulan[$bln] ?> <?= $data['thn'] ?>
                </div>
                <div style="display: flex; gap: 10px; font-size: 0.75rem; font-weight: 700;">
                    <span style="color:#10b981;" title="Hadir">H: <?= $data['Hadir'] ?></span>
                    <span style="color:#f59e0b;" title="Sakit">S: <?= $data['Sakit'] ?></span>
                    <span style="color:#3b82f6;" title="Izin">I: <?= $data['Izin'] ?></span>
                    <span style="color:#ef4444;" title="Alpa">A: <?= $data['Alpa'] ?></span>
                    <span style="color:#db2777;" title="Bolos">B: <?= $data['Bolos'] ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Nilai Akademik -->
    <h3 class="section-title"><i class="fa-solid fa-star" style="color: #f59e0b; margin-right: 5px;"></i> Nilai E-Raport (Semester Berjalan)</h3>
    <div style="background: white; border-radius: 12px; padding: 25px 20px; text-align: center; border: 1px solid #e2e8f0; margin-bottom: 30px;">
        <i class="fa-solid fa-clock-rotate-left" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 10px; display: block;"></i>
        <h4 style="margin: 0 0 5px 0; color: #334155;">Sedang Sinkronisasi</h4>
        <p style="margin: 0; font-size: 0.85rem; color: #64748b;">Modul pantau nilai E-Raport sedang dalam proses integrasi dengan database utama SIAKAD.</p>
    </div>

    <div style="display: flex; gap: 10px;">
        <a href="guru_wali.php?action=pantau" class="btn" style="flex: 1; text-align: center; background: #f1f5f9; color: #475569; padding: 12px; border-radius: 50px; font-weight: 600; text-decoration: none;">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
        <a href="guru_wali_detail.php?siswa_id=<?= $siswa_id ?>" class="btn" style="flex: 1; text-align: center; background: #0ea5e9; color: white; padding: 12px; border-radius: 50px; font-weight: 600; text-decoration: none;">
            Ke Jurnal <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

</div>

<?php include 'footer_guru.php'; ?>
