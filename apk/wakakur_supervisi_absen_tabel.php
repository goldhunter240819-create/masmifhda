<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$logged_in_guru_id = $_SESSION['guru_id'];
$is_kamad = false;
$is_wakakur = false;

// Cek hak akses
$stmt_akses = $core_db->prepare("SELECT is_kamad FROM guru WHERE id = :id LIMIT 1");
$stmt_akses->execute(['id' => $logged_in_guru_id]);
$logged_in_guru = $stmt_akses->fetch(PDO::FETCH_ASSOC);

if ($logged_in_guru && $logged_in_guru['is_kamad'] == 1) {
    $is_kamad = true;
} else {
    $stmt_w = $core_db->prepare("SELECT id FROM guru_tugas WHERE guru_id = ? AND (tugas LIKE '%Kurikulum%' OR tugas LIKE '%Waka Kurikulum%') LIMIT 1");
    $stmt_w->execute([$logged_in_guru_id]);
    if ($stmt_w->fetch()) {
        $is_wakakur = true;
    }
}

if (!$is_kamad && !$is_wakakur) {
    echo "<script>alert('Akses Ditolak'); window.close();</script>";
    exit;
}

$guru_id = isset($_GET['guru_id']) ? (int)$_GET['guru_id'] : 0;
$mapel_id = isset($_GET['mapel_id']) ? (int)$_GET['mapel_id'] : 0;
$kelas_id = isset($_GET['kelas_id']) ? (int)$_GET['kelas_id'] : 0;

if (!$guru_id || !$mapel_id || !$kelas_id) {
    echo "<script>alert('Parameter tidak valid'); window.close();</script>";
    exit;
}

// Info Guru, Mapel, Kelas
$stmt = $core_db->prepare("SELECT nama FROM guru WHERE id = ?");
$stmt->execute([$guru_id]);
$guru = $stmt->fetch();

$stmt = $siakad_db->prepare("SELECT nama_mapel FROM mata_pelajaran WHERE id = ?");
$stmt->execute([$mapel_id]);
$mapel = $stmt->fetch();

$stmt = $siakad_db->prepare("SELECT nama_kelas FROM kelas WHERE id = ?");
$stmt->execute([$kelas_id]);
$kelas_info = $stmt->fetch();

// Tahun ajaran aktif
$stmt_inst = $core_db->query("SELECT tahun_ajaran_id, semester FROM institusi LIMIT 1");
$institusi = $stmt_inst->fetch(PDO::FETCH_ASSOC);
$ta_id = $institusi['tahun_ajaran_id'] ?? 1;
$is_ganjil = (stripos($institusi['semester'] ?? 'Ganjil', 'ganjil') !== false);

// Penentuan rentang tanggal berdasarkan semester
$current_year = date('Y');
if ($is_ganjil) {
    $start_date = $current_year . '-07-01';
    $end_date = $current_year . '-12-31';
} else {
    $start_date = $current_year . '-01-01';
    $end_date = $current_year . '-06-30';
}
$real_today = date('Y-m-d');
if ($end_date > $real_today) {
    $end_date = $real_today;
}

// Ambil riwayat absen UNTUK KELAS INI SAJA, dan di dalam rentang waktu semester ini
$stmt_a = $siakad_db->prepare("
    SELECT j.id, j.tanggal, k.nama_kelas,
    (SELECT COUNT(*) FROM absensi_permapel WHERE jurnal_id = j.id AND status = 'Hadir') as hadir,
    (SELECT COUNT(*) FROM absensi_permapel WHERE jurnal_id = j.id AND status = 'Sakit') as sakit,
    (SELECT COUNT(*) FROM absensi_permapel WHERE jurnal_id = j.id AND status = 'Izin') as izin,
    (SELECT COUNT(*) FROM absensi_permapel WHERE jurnal_id = j.id AND status IN ('Alpa', 'Alpha')) as alpa
    FROM jurnal_guru j
    LEFT JOIN kelas k ON j.kelas_id = k.id
    WHERE j.guru_id = ? AND j.mapel_id = ? AND j.kelas_id = ? AND j.tanggal BETWEEN ? AND ?
    ORDER BY j.tanggal DESC
");
$stmt_a->execute([$guru_id, $mapel_id, $kelas_id, $start_date, $end_date]);
$absensi_raw = $stmt_a->fetchAll(PDO::FETCH_ASSOC);

$jurnal_ids = array_column($absensi_raw, 'id');
$detail_absen = [];
if (!empty($jurnal_ids)) {
    $inQuery = implode(',', array_fill(0, count($jurnal_ids), '?'));
    $stmt_det = $siakad_db->prepare("SELECT jurnal_id, siswa_id, status FROM absensi_permapel WHERE jurnal_id IN ($inQuery) AND status != 'Hadir'");
    $stmt_det->execute($jurnal_ids);
    $absen_list = $stmt_det->fetchAll(PDO::FETCH_ASSOC);
    
    $siswa_ids = array_unique(array_column($absen_list, 'siswa_id'));
    $siswa_names = [];
    if (!empty($siswa_ids)) {
        $inSiswa = implode(',', array_fill(0, count($siswa_ids), '?'));
        $stmt_s = $core_db->prepare("SELECT id, nama FROM siswa WHERE id IN ($inSiswa)");
        $stmt_s->execute(array_values($siswa_ids));
        while($r = $stmt_s->fetch()) {
            $siswa_names[$r['id']] = $r['nama'];
        }
    }
    
    foreach($absen_list as $ab) {
        $nama = $siswa_names[$ab['siswa_id']] ?? 'Unknown';
        $detail_absen[$ab['jurnal_id']][] = [
            'nama' => $nama,
            'status' => $ab['status']
        ];
    }
}
// Ambil data kalender pendidikan
$stmt_cal = $siakad_db->prepare("SELECT tanggal_mulai, tanggal_selesai, keterangan, tipe, jam_mulai_kosong FROM kalender_akademik WHERE tanggal_mulai <= ? AND (tanggal_selesai >= ? OR tanggal_selesai IS NULL OR tanggal_selesai = '0000-00-00')");
$stmt_cal->execute([$end_date, $start_date]);
$kalender_data = $stmt_cal->fetchAll(PDO::FETCH_ASSOC);

$kalender_dates = [];
foreach($kalender_data as $cal) {
    $t_start = strtotime($cal['tanggal_mulai']);
    $t_end = (!empty($cal['tanggal_selesai']) && $cal['tanggal_selesai'] != '0000-00-00') ? strtotime($cal['tanggal_selesai']) : $t_start;
    for($t = $t_start; $t <= $t_end; $t += 86400) {
        $kalender_dates[date('Y-m-d', $t)] = [
            'keterangan' => $cal['keterangan'],
            'jam_mulai_kosong' => $cal['jam_mulai_kosong'],
            'is_full_day' => (empty($cal['jam_mulai_kosong']) || $cal['jam_mulai_kosong'] === '00:00:00')
        ];
    }
}

// Ambil jadwal mengajar per hari UNTUK KELAS INI SAJA
$stmt_hari = $siakad_db->prepare("SELECT DISTINCT hari, jam_mulai FROM jadwal_pelajaran WHERE guru_id = ? AND mata_pelajaran_id = ? AND kelas_id = ? AND tahun_ajaran_id = ? ORDER BY jam_mulai ASC");
$stmt_hari->execute([$guru_id, $mapel_id, $kelas_id, $ta_id]);
$jadwal_raw = $stmt_hari->fetchAll(PDO::FETCH_ASSOC);

$jadwal_hari = [];
foreach($jadwal_raw as $jdwl) {
    if (!isset($jadwal_hari[$jdwl['hari']])) {
        $jadwal_hari[$jdwl['hari']] = $jdwl['jam_mulai'];
    }
}
$hari_map = [1=>'Senin',2=>'Selasa',3=>'Rabu',4=>'Kamis',5=>'Jumat',6=>'Sabtu',7=>'Minggu'];
$absen_by_date = [];
foreach($absensi_raw as $a) {
    $absen_by_date[$a['tanggal']][] = $a;
}

$monthly_data = [];
if (!empty($jadwal_hari)) {
    $current_date = strtotime($start_date);
    $end_time = strtotime($end_date);
    
    while ($current_date <= $end_time) {
        $day_of_week = date('N', $current_date);
        $nama_hari = $hari_map[$day_of_week];
        $date_str = date('Y-m-d', $current_date);
        $month_str = date('Y-m', $current_date);
        
        if (array_key_exists($nama_hari, $jadwal_hari)) {
            $is_filled = isset($absen_by_date[$date_str]);
            $is_excused = false;
            $keterangan_excuse = '';

            if (isset($kalender_dates[$date_str])) {
                $cal_info = $kalender_dates[$date_str];
                if ($cal_info['is_full_day']) {
                    $is_excused = true;
                    $keterangan_excuse = $cal_info['keterangan'];
                } else {
                    $jadwal_start = $jadwal_hari[$nama_hari];
                    if ($jadwal_start >= $cal_info['jam_mulai_kosong']) {
                        $is_excused = true;
                        $keterangan_excuse = $cal_info['keterangan'] . ' (Ditiadakan)';
                    }
                }
            }
            
            if (!isset($monthly_data[$month_str])) $monthly_data[$month_str] = [];
            
            if ($is_excused) {
                // Absen ditiadakan
                $monthly_data[$month_str][] = [
                    'tanggal' => $date_str,
                    'status' => 'excused',
                    'keterangan' => $keterangan_excuse
                ];
            } else {
                if ($is_filled) {
                    foreach($absen_by_date[$date_str] as $a_item) {
                        $a_item['status'] = 'filled';
                        $monthly_data[$month_str][] = $a_item;
                    }
                } else {
                    $monthly_data[$month_str][] = [
                        'tanggal' => $date_str,
                        'status' => 'missing',
                        'keterangan' => 'Tidak Isi Absen (Alpa)'
                    ];
                }
            }
        }
        $current_date = strtotime('+1 day', $current_date);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisi Absen - <?= htmlspecialchars($mapel['nama_mapel']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; margin: 0; padding: 20px; color: #1e293b; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        h2 { margin-top: 0; color: #0f172a; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; font-size: 1.3rem; }
        .info-box { background: #f0f9ff; border: 1px solid #bae6fd; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 20px; font-size: 0.9rem; }
        .info-item span { font-weight: 700; color: #0369a1; display: block; font-size: 0.75rem; margin-bottom: 3px; }
        .month-header { background: #1e293b; color: white; padding: 10px 15px; border-radius: 8px; font-weight: 700; margin-top: 25px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 0.85rem; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f1f5f9; font-weight: 700; color: #475569; }
        .badge { padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; margin-right: 4px; display: inline-block; width: 35px; text-align: center; }
        .badge-h { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
        .badge-s { background: #fef9c3; color: #d97706; border: 1px solid #fef08a; }
        .badge-i { background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; }
        .badge-a { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        
        /* Baris dengan status khusus */
        tr.status-missing td { background-color: #fef2f2; border-left: 4px solid #ef4444; }
        tr.status-missing td div { color: #b91c1c; }
        
        tr.status-excused td { background-color: #fffbeb; border-left: 4px solid #f59e0b; }
        tr.status-excused td div { color: #b45309; }
        
        tr.status-filled td { border-left: 4px solid #10b981; }
    </style>
</head>
<body>

<div class="container">
    <h2>Rekap Absensi Siswa</h2>
    
    <div class="info-box">
        <div class="info-item">
            <span>Guru</span>
            <?= htmlspecialchars($guru['nama'] ?? '-') ?>
        </div>
        <div class="info-item">
            <span>Mata Pelajaran</span>
            <?= htmlspecialchars($mapel['nama_mapel'] ?? '-') ?>
        </div>
        <div class="info-item">
            <span>Kelas</span>
            <?= htmlspecialchars($kelas_info['nama_kelas'] ?? '-') ?>
        </div>
    </div>

    <?php if (empty($monthly_data)): ?>
        <div style="text-align: center; padding: 40px; color: #64748b;">
            <i class="fa-solid fa-users-slash" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px;"></i>
            <p>Belum ada rekaman absensi yang diisi oleh guru ini.</p>
        </div>
    <?php else: ?>
        <?php 
        krsort($monthly_data);
        foreach($monthly_data as $month_key => $rows): 
            $nama_bulan = date('F Y', strtotime($month_key . '-01'));
            
            // Hitung statistik bulanan
            $hadir_m = 0; $alpa_m = 0; $libur_m = 0;
            foreach($rows as $r) {
                if($r['status'] == 'filled') $hadir_m++;
                elseif($r['status'] == 'missing') $alpa_m++;
                elseif($r['status'] == 'excused') $libur_m++;
            }
        ?>
        <div class="month-header">
            <div><?= $nama_bulan ?></div>
            <div style="font-size: 0.8rem; font-weight: normal;">
                <span style="color: #4ade80; margin-right:10px;"><i class="fa-solid fa-check"></i> <?= $hadir_m ?></span>
                <span style="color: #f87171; margin-right:10px;"><i class="fa-solid fa-xmark"></i> <?= $alpa_m ?></span>
                <span style="color: #fbbf24;"><i class="fa-solid fa-umbrella-beach"></i> <?= $libur_m ?></span>
            </div>
        </div>
        <div>
            <table style="width: 100%;">
                <tbody>
                    <?php 
                    // Reverse urutan agar tanggal terbaru di atas
                    usort($rows, function($a, $b) { return strtotime($b['tanggal']) - strtotime($a['tanggal']); });
                    foreach($rows as $r): 
                    ?>
                    <tr class="status-<?= $r['status'] ?>">
                        <td style="padding: 12px 15px;">
                            <div style="font-weight: 700; font-size: 0.95rem; margin-bottom: 8px;"><?= date('d M Y', strtotime($r['tanggal'])) ?></div>
                            <?php if($r['status'] == 'filled'): ?>
                                <details style="cursor: pointer;">
                                    <summary style="outline: none; margin-bottom: 5px;">
                                        <div style="display: inline-flex; gap: 8px;">
                                            <span class="badge badge-h" title="Hadir">H: <?= $r['hadir'] ?></span>
                                            <span class="badge badge-s" title="Sakit">S: <?= $r['sakit'] ?></span>
                                            <span class="badge badge-i" title="Izin">I: <?= $r['izin'] ?></span>
                                            <span class="badge badge-a" title="Alpa">A: <?= $r['alpa'] ?></span>
                                        </div>
                                    </summary>
                                    <?php if (!empty($detail_absen[$r['id']])): ?>
                                        <div style="margin-top: 10px; padding: 10px; background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0; font-size: 0.8rem;">
                                            <div style="font-weight: 600; color: #475569; margin-bottom: 5px;">Siswa Tidak Hadir:</div>
                                            <ul style="margin: 0; padding-left: 20px;">
                                                <?php foreach($detail_absen[$r['id']] as $da): ?>
                                                    <li><?= htmlspecialchars($da['nama']) ?> - <b><?= $da['status'] ?></b></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php else: ?>
                                        <div style="margin-top: 10px; padding: 10px; background: #f0fdf4; border-radius: 6px; border: 1px solid #bbf7d0; font-size: 0.8rem; color: #16a34a;">
                                            <i class="fa-solid fa-check"></i> Hadir Semua
                                        </div>
                                    <?php endif; ?>
                                </details>
                            <?php else: ?>
                                <div style="font-size: 0.85rem; font-weight: 600;">
                                    Status: <?= htmlspecialchars($r['keterangan']) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
