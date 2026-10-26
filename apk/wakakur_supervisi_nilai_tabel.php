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
$semester = $institusi['semester'] ?? 'Ganjil';

// Ambil riwayat nilai harian
$stmt_n = $core_db->prepare("
    SELECT n.id, n.jenis_evaluasi, n.judul_materi, n.tanggal
    FROM nilai_harian n
    WHERE n.guru_id = ? AND n.mapel_id = ? AND n.kelas_id = ? AND n.tahun_ajaran_id = ? AND n.semester = ?
    ORDER BY n.tanggal DESC
");
$stmt_n->execute([$guru_id, $mapel_id, $kelas_id, $ta_id, $semester]);
$nilai_raw = $stmt_n->fetchAll(PDO::FETCH_ASSOC);

// Normalisasi dan cek kelengkapan
$wajib = ['PH 1', 'PTS', 'PAS'];
$semua_nilai = [];

foreach($nilai_raw as $n) {
    $je = strtoupper(trim($n['jenis_evaluasi']));
    if (preg_match('/^PH\s*(\d+)$/i', $je, $matches)) {
        $je = 'PH ' . $matches[1];
    }
    $n['jenis_evaluasi'] = $je;
    $n['status'] = 'filled';
    $semua_nilai[$je][] = $n;
}

// Tambahkan placeholder jika wajib tidak ada
foreach($wajib as $w) {
    if (!isset($semua_nilai[$w])) {
        $semua_nilai[$w][] = [
            'id' => null,
            'jenis_evaluasi' => $w,
            'judul_materi' => '-',
            'tanggal' => date('Y-m-d'), // Dummy untuk sorting terbaru
            'status' => 'missing'
        ];
    }
}

// Gabungkan dan urutkan
$final_list = [];
foreach($semua_nilai as $k => $list) {
    foreach($list as $n) {
        $final_list[] = $n;
    }
}
usort($final_list, function($a, $b) { 
    return strtotime($b['tanggal']) - strtotime($a['tanggal']); 
});

// Ambil detail nilai per siswa untuk yang status='filled'
$nilai_ids = array_filter(array_column($final_list, 'id'));
$detail_nilai = [];

if (!empty($nilai_ids)) {
    $inQuery = implode(',', array_fill(0, count($nilai_ids), '?'));
    $stmt_det = $core_db->prepare("SELECT nilai_harian_id, siswa_id, nilai FROM nilai_harian_detail WHERE nilai_harian_id IN ($inQuery)");
    $stmt_det->execute($nilai_ids);
    $det_list = $stmt_det->fetchAll(PDO::FETCH_ASSOC);
    
    $siswa_ids = array_unique(array_column($det_list, 'siswa_id'));
    $siswa_names = [];
    if (!empty($siswa_ids)) {
        $inSiswa = implode(',', array_fill(0, count($siswa_ids), '?'));
        $stmt_s = $core_db->prepare("SELECT id, nama, nisn FROM siswa WHERE id IN ($inSiswa)");
        $stmt_s->execute(array_values($siswa_ids));
        while($r = $stmt_s->fetch()) {
            $siswa_names[$r['id']] = $r;
        }
    }
    
    foreach($det_list as $det) {
        $siswa = $siswa_names[$det['siswa_id']] ?? ['nama' => 'Unknown', 'nisn' => '-'];
        $detail_nilai[$det['nilai_harian_id']][] = [
            'nama' => $siswa['nama'],
            'nisn' => $siswa['nisn'],
            'nilai' => $det['nilai']
        ];
    }
    
    // Sort daftar siswa tiap nilai secara alfabetis
    foreach($detail_nilai as $nid => &$siswa_list) {
        usort($siswa_list, function($a, $b) {
            return strcmp($a['nama'], $b['nama']);
        });
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisi Daftar Nilai - <?= htmlspecialchars($mapel['nama_mapel']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; margin: 0; padding: 20px; color: #1e293b; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        h2 { margin-top: 0; color: #0f172a; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; font-size: 1.3rem; }
        .info-box { background: #fffbeb; border: 1px solid #fde68a; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 20px; font-size: 0.9rem; flex-wrap: wrap; }
        .info-item span { font-weight: 700; color: #d97706; display: block; font-size: 0.75rem; margin-bottom: 3px; }
        .month-header { background: #1e293b; color: white; padding: 10px 15px; border-radius: 8px; font-weight: 700; margin-top: 25px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 0.85rem; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        
        .badge { padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; margin-right: 4px; display: inline-block; text-align: center; }
        .badge-filled { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
        .badge-missing { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        
        /* Baris dengan status khusus */
        tr.status-missing td { background-color: #fef2f2; border-left: 4px solid #ef4444; }
        tr.status-missing td div { color: #b91c1c; }
        
        tr.status-filled td { border-left: 4px solid #10b981; }
        
        .siswa-table { width: 100%; border-collapse: collapse; font-size: 0.8rem; margin-top: 10px; background: white;}
        .siswa-table th, .siswa-table td { padding: 8px; border: 1px solid #e2e8f0; }
        .siswa-table th { background: #f1f5f9; font-weight: 600; color: #475569;}
        .siswa-table tr:hover { background: #f8fafc; }
    </style>
</head>
<body>

<div class="container">
    <h2>Rekap Daftar Nilai (<?= htmlspecialchars($semester) ?>)</h2>
    
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

    <div class="month-header">
        <div>Daftar Evaluasi & Penilaian</div>
        <div style="font-size: 0.8rem; font-weight: normal; background: #334155; padding: 4px 10px; border-radius: 20px;">
            <?= count($final_list) ?> Evaluasi
        </div>
    </div>
    
    <div>
        <table style="width: 100%;">
            <tbody>
                <?php foreach($final_list as $r): ?>
                <tr class="status-<?= $r['status'] ?>">
                    <td style="padding: 12px 15px;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                            <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">
                                <?= htmlspecialchars($r['jenis_evaluasi']) ?>
                            </div>
                            <?php if ($r['status'] == 'filled'): ?>
                                <span class="badge badge-filled"><i class="fa-solid fa-check"></i> Terisi</span>
                            <?php else: ?>
                                <span class="badge badge-missing"><i class="fa-solid fa-xmark"></i> Belum Diisi</span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if($r['status'] == 'filled'): ?>
                            <div style="font-size: 0.85rem; margin-bottom: 6px;">
                                <span style="display:inline-block; width: 60px; font-weight: 600; color: #64748b;">Tanggal</span> 
                                : <?= date('d M Y', strtotime($r['tanggal'])) ?>
                            </div>
                            <div style="font-size: 0.85rem; margin-bottom: 10px;">
                                <span style="display:inline-block; width: 60px; font-weight: 600; color: #64748b;">Materi</span> 
                                : <?= htmlspecialchars($r['judul_materi']) ?>
                            </div>
                            
                            <details style="cursor: pointer; margin-top: 10px;">
                                <summary style="outline: none; margin-bottom: 5px; color: #0284c7; font-weight: 600; font-size: 0.85rem;">
                                    <i class="fa-solid fa-list-ol"></i> Lihat Daftar Nilai Siswa
                                </summary>
                                <?php if (!empty($detail_nilai[$r['id']])): ?>
                                    <div style="margin-top: 10px; border-radius: 6px; overflow: hidden;">
                                        <table class="siswa-table">
                                            <thead>
                                                <tr>
                                                    <th width="10%">No</th>
                                                    <th width="65%">Nama Siswa</th>
                                                    <th width="25%">Nilai</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $no = 1; foreach($detail_nilai[$r['id']] as $dn): ?>
                                                <tr>
                                                    <td style="text-align: center; color: #94a3b8;"><?= $no++ ?></td>
                                                    <td><?= htmlspecialchars($dn['nama']) ?></td>
                                                    <td style="text-align: center; font-weight: 700; color: <?= $dn['nilai'] < 75 ? '#dc2626' : '#16a34a' ?>;"><?= $dn['nilai'] ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div style="margin-top: 10px; padding: 10px; background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0; font-size: 0.8rem; color: #64748b;">
                                        Belum ada siswa yang dinilai.
                                    </div>
                                <?php endif; ?>
                            </details>
                        <?php else: ?>
                            <div style="font-size: 0.85rem; font-weight: 600; color: #b91c1c; margin-top: 5px;">
                                Guru belum menginput nilai untuk evaluasi ini.
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
