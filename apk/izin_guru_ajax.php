<?php
require_once 'koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['guru_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$guru_id = $_SESSION['guru_id'];
$tanggal = $_GET['tanggal'] ?? '';
$edit_id = $_GET['edit_id'] ?? 0;

if (empty($tanggal)) {
    echo json_encode(['success' => false, 'message' => 'Tanggal tidak valid']);
    exit;
}

// Konversi tanggal ke hari dalam bahasa Indonesia
$day_num = date('w', strtotime($tanggal));
$days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$hari = $days[$day_num];

try {
    // Cek apakah sudah ada pengajuan izin di tanggal ini (kecuali yang sedang di-edit)
    $stmt_cek = $siakad_db->prepare("SELECT id, status FROM ajuan_izin_guru WHERE guru_id = ? AND tanggal_izin = ? AND id != ? LIMIT 1");
    $stmt_cek->execute([$guru_id, $tanggal, $edit_id]);
    $cek_izin = $stmt_cek->fetch(PDO::FETCH_ASSOC);

    if ($cek_izin) {
        echo json_encode(['success' => true, 'already_applied' => true, 'status' => $cek_izin['status']]);
        exit;
    }

    // Ambil tahun ajaran dan semester aktif
    $stmt_ta = $core_db->query("SELECT id, name, semester FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
    $ta_aktif = $stmt_ta->fetch(PDO::FETCH_ASSOC);

    if (!$ta_aktif) {
        echo json_encode(['success' => true, 'data' => []]);
        exit;
    }

    $ta_id = $ta_aktif['id'];
    // Semester bisa berupa angka '1'/'2' atau teks 'Ganjil'/'Genap'
    $sem_raw = strtolower($ta_aktif['semester']);
    if ($sem_raw === 'ganjil') {
        $semester = '1';
    } elseif ($sem_raw === 'genap') {
        $semester = '2';
    } else {
        $semester = $ta_aktif['semester']; // sudah angka
    }

    $sql = "
        SELECT 
            MIN(j.id) as id, 
            MIN(j.jam_mulai) as jam_mulai, 
            MAX(j.jam_selesai) as jam_selesai, 
            k.nama_kelas as kelas, 
            m.nama_mapel as mapel
        FROM jadwal_pelajaran j
        JOIN aula_db.kelas k ON j.kelas_id = k.id
        JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id
        WHERE j.guru_id = :guru_id 
          AND j.hari = :hari 
          AND j.tahun_ajaran_id = :ta_id
          AND j.semester = :semester
        GROUP BY j.kelas_id, j.mata_pelajaran_id
        ORDER BY jam_mulai ASC
    ";

    $stmt = $siakad_db->prepare($sql);
    $stmt->execute([
        'guru_id' => $guru_id,
        'hari' => $hari,
        'ta_id' => $ta_id,
        'semester' => $semester
    ]);

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format jam (hapus detik)
    foreach ($data as &$d) {
        $d['jam_mulai'] = date('H:i', strtotime($d['jam_mulai']));
        $d['jam_selesai'] = date('H:i', strtotime($d['jam_selesai']));
    }

    echo json_encode(['success' => true, 'data' => $data]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
