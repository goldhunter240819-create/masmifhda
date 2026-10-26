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
// Ganjil: Juli - Desember
// Genap: Januari - Juni
$current_year = date('Y');
if ($is_ganjil) {
    $start_date = $current_year . '-07-01';
    $end_date = $current_year . '-12-31';
} else {
    $start_date = $current_year . '-01-01';
    $end_date = $current_year . '-06-30';
}
// Jangan tampilkan hari-hari yang belum terjadi di masa depan
$real_today = date('Y-m-d');
if ($end_date > $real_today) {
    $end_date = $real_today;
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

// Ambil jurnal yang diisi UNTUK KELAS INI SAJA
$stmt_j = $siakad_db->prepare("SELECT j.id, j.tanggal, j.materi, k.nama_kelas
                               FROM jurnal_guru j
                               LEFT JOIN kelas k ON j.kelas_id = k.id
                               WHERE j.guru_id = ? AND j.mapel_id = ? AND j.kelas_id = ?
                               ORDER BY j.tanggal ASC");
$stmt_j->execute([$guru_id, $mapel_id, $kelas_id]);
$jurnals = $stmt_j->fetchAll(PDO::FETCH_ASSOC);

$jurnal_by_date = [];
foreach($jurnals as $j) {
    $jurnal_by_date[$j['tanggal']][] = $j;
}

// Generate rentang waktu (hanya tampilkan bulan dimana jadwal ada atau hari terlewat)
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
            $is_filled = isset($jurnal_by_date[$date_str]);
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
                // Jurnal ditiadakan
                $monthly_data[$month_str][] = [
                    'tanggal' => $date_str,
                    'status' => 'excused',
                    'keterangan' => $keterangan_excuse,
                    'materi' => '-',
                    'kelas' => '-'
                ];
            } else {
                if ($is_filled) {
                    foreach($jurnal_by_date[$date_str] as $j_item) {
                        $monthly_data[$month_str][] = [
                            'tanggal' => $date_str,
                            'status' => 'filled',
                            'keterangan' => 'Hadir / Mengajar',
                            'materi' => $j_item['materi'],
                            'kelas' => $j_item['nama_kelas'] ?? '-'
                        ];
                    }
                } else {
                    $monthly_data[$month_str][] = [
                        'tanggal' => $date_str,
                        'status' => 'missing',
                        'keterangan' => 'Tidak Isi Jurnal (Alpa)',
                        'materi' => '-',
                        'kelas' => '-'
                    ];
                }
            }
        }
        $current_date = strtotime("+1 day", $current_date);
    }
}
krsort($monthly_data); // Urutkan bulan dari yang terbaru
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisi Jurnal - <?= htmlspecialchars($mapel['nama_mapel']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; margin: 0; padding: 20px; color: #1e293b; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        h2 { margin-top: 0; color: #0f172a; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; font-size: 1.3rem; }
        .info-box { background: #f0f9ff; border: 1px solid #bae6fd; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 20px; font-size: 0.9rem; }
        .info-item span { font-weight: 700; color: #0369a1; display: block; font-size: 0.75rem; margin-bottom: 3px; }
        .month-header { background: #1e293b; color: white; padding: 10px 15px; border-radius: 8px; font-weight: 700; margin-top: 25px; margin-bottom: 10px; display: flex; justify-content: space-between; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 0.85rem; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f1f5f9; font-weight: 700; color: #475569; }
        .status-filled { color: #16a34a; font-weight: 600; }
        .status-missing { color: #dc2626; font-weight: 600; background: #fef2f2; }
        .status-excused { color: #d97706; font-style: italic; background: #fffbeb; }
    </style>
</head>
<body>

<div class="container">
    <h2>Riwayat Jurnal Mengajar</h2>
    
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
            <i class="fa-solid fa-folder-open" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px;"></i>
            <p>Belum ada jadwal mengajar atau riwayat jurnal.</p>
        </div>
    <?php else: ?>
        <?php 
        krsort($monthly_data);
        foreach($monthly_data as $month_key => $rows): 
            if(empty($rows)) continue;
            // Reverse urutan agar tanggal terbaru di atas
            usort($rows, function($a, $b) { return strtotime($b['tanggal']) - strtotime($a['tanggal']); });
            
            $nama_bulan = date('F Y', strtotime($month_key . '-01'));
            
            // Hitung statistik bulanan
            $hadir = 0; $alpa = 0; $libur = 0;
            foreach($rows as $r) {
                if($r['status'] == 'filled') $hadir++;
                elseif($r['status'] == 'missing') $alpa++;
                elseif($r['status'] == 'excused') $libur++;
            }
        ?>
        <div class="month-header">
            <div><?= $nama_bulan ?></div>
            <div style="font-size: 0.8rem; font-weight: normal;">
                <span style="color: #4ade80; margin-right:10px;"><i class="fa-solid fa-check"></i> <?= $hadir ?></span>
                <span style="color: #f87171; margin-right:10px;"><i class="fa-solid fa-xmark"></i> <?= $alpa ?></span>
                <span style="color: #fbbf24;"><i class="fa-solid fa-umbrella-beach"></i> <?= $libur ?></span>
            </div>
        </div>
        <div>
            <table style="width: 100%;">
                <tbody>
                    <?php foreach($rows as $r): ?>
                    <tr class="status-<?= $r['status'] ?>">
                        <td style="padding: 12px 15px;">
                            <div style="font-weight: 700; font-size: 0.95rem; margin-bottom: 6px;"><?= date('d M Y', strtotime($r['tanggal'])) ?></div>
                            <div style="font-size: 0.85rem; margin-bottom: 4px;">
                                <span style="display:inline-block; width: 60px; font-weight: 600; color: #64748b;">Status</span> 
                                : <?= htmlspecialchars($r['keterangan']) ?>
                            </div>
                            <div style="font-size: 0.85rem;">
                                <span style="display:inline-block; width: 60px; font-weight: 600; color: #64748b;">Materi</span> 
                                : <?= htmlspecialchars($r['materi']) ?>
                            </div>
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
