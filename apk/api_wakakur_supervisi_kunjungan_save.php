<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$logged_in_guru_id = $_SESSION['guru_id'];
$is_kamad = false;
$is_wakakur = false;

// Cek hak akses
$stmt_akses = $core_db->prepare("SELECT is_kamad, nama FROM guru WHERE id = :id LIMIT 1");
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
    echo json_encode(['status' => 'error', 'message' => 'Akses Ditolak']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guru_id = isset($_POST['guru_id']) ? (int)$_POST['guru_id'] : 0;
    $mapel_kelas = isset($_POST['mapel_kelas']) ? $_POST['mapel_kelas'] : ''; // format: mapel_id-kelas_id
    $tanggal_supervisi = isset($_POST['tanggal_supervisi']) ? $_POST['tanggal_supervisi'] : date('Y-m-d');
    $catatan = isset($_POST['catatan']) ? $_POST['catatan'] : '';
    $instrumen = isset($_POST['instrumen']) ? $_POST['instrumen'] : [];
    
    if (!$guru_id || empty($mapel_kelas) || empty($instrumen)) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        exit;
    }
    
    $parts = explode('-', $mapel_kelas);
    if(count($parts) != 2) {
        echo json_encode(['status' => 'error', 'message' => 'Format Mapel/Kelas tidak valid']);
        exit;
    }
    $mapel_id = (int)$parts[0];
    $kelas_id = (int)$parts[1];

    $stmt_inst = $core_db->query("SELECT tahun_ajaran_id FROM institusi LIMIT 1");
    $tahun_ajaran_id = $stmt_inst->fetchColumn() ?: 1;

    // Hitung Nilai Akhir
    $total_skor = 0;
    $max_skor = 9 * 4; // 9 aspek * skor maksimal 4 = 36
    
    $instrumen_data = [];
    for ($i = 1; $i <= 9; $i++) {
        $skor = isset($instrumen[$i]) ? (int)$instrumen[$i] : 1;
        $total_skor += $skor;
        $instrumen_data[$i] = $skor;
    }
    
    $nilai_akhir = ($total_skor / $max_skor) * 100;
    
    $ketercapaian = 'K';
    if ($nilai_akhir >= 90) $ketercapaian = 'A';
    elseif ($nilai_akhir >= 80) $ketercapaian = 'B';
    elseif ($nilai_akhir >= 70) $ketercapaian = 'C';

    $instrumen_json = json_encode($instrumen_data);

    // Fetch mapel name and kelas name
    $nama_mapel = '';
    $nama_kelas = '';
    $stmt_m = $siakad_db->prepare("SELECT nama_mapel FROM mata_pelajaran WHERE id = ?");
    $stmt_m->execute([$mapel_id]);
    if ($r = $stmt_m->fetch()) $nama_mapel = $r['nama_mapel'];
    
    $stmt_k = $siakad_db->prepare("SELECT nama_kelas FROM kelas WHERE id = ?");
    $stmt_k->execute([$kelas_id]);
    if ($r = $stmt_k->fetch()) $nama_kelas = $r['nama_kelas'];

    $mata_pelajaran = $nama_mapel;
    $kelas = $nama_kelas;
    $jam_ke = '';
    $materi_pokok = '';
    $tindak_lanjut = '';
    
    // Cek apakah sudah ada
    $stmt_cek = $core_db->prepare("SELECT id FROM kurikulum_supervisi_kelas WHERE tahun_ajaran_id = ? AND guru_id = ?");
    $stmt_cek->execute([$tahun_ajaran_id, $guru_id]);
    $existing = $stmt_cek->fetch();

    $penilai_nama = $logged_in_guru['nama'] ?? 'Admin';
    
    if ($existing) {
        $stmt_upd = $core_db->prepare("UPDATE kurikulum_supervisi_kelas SET mata_pelajaran=?, kelas=?, tanggal_supervisi=?, instrumen_json=?, nilai_akhir=?, catatan=?, penilai_nama=? WHERE id=?");
        $stmt_upd->execute([$mata_pelajaran, $kelas, $tanggal_supervisi, $instrumen_json, $nilai_akhir, $catatan, $penilai_nama, $existing['id']]);
    } else {
        $stmt_ins = $core_db->prepare("INSERT INTO kurikulum_supervisi_kelas (tahun_ajaran_id, guru_id, mata_pelajaran, kelas, jam_ke, materi_pokok, tanggal_supervisi, instrumen_json, nilai_akhir, catatan, tindak_lanjut, penilai_nama) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_ins->execute([$tahun_ajaran_id, $guru_id, $mata_pelajaran, $kelas, $jam_ke, $materi_pokok, $tanggal_supervisi, $instrumen_json, $nilai_akhir, $catatan, $tindak_lanjut, $penilai_nama]);
    }

    echo json_encode(['status' => 'success', 'message' => 'Berhasil disimpan']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Request']);
}
