<?php
require_once 'koneksi.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['guru_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$siswa_id = isset($input['siswa_id']) ? intval($input['siswa_id']) : 0;
$status = $input['status'] ?? '';

if ($siswa_id === 0 || empty($status)) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

try {
    $stmt_siswa = $core_db->prepare("SELECT nama FROM siswa WHERE id = ?");
    $stmt_siswa->execute([$siswa_id]);
    $siswa = $stmt_siswa->fetch(PDO::FETCH_ASSOC);

    if (!$siswa) {
        echo json_encode(['success' => false, 'message' => 'Siswa tidak ditemukan']);
        exit;
    }

    $nama_siswa = $siswa['nama'];
    $tanggal = !empty($input['tanggal']) ? $input['tanggal'] : date('Y-m-d');
    $waktu = date('H:i:s');
    $guru_id = $_SESSION['guru_id'];
    
    // Get aktif tahun ajaran
    $activeTahunId = 1;
    $stmtTahun = $core_db->query("SELECT id FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
    if ($thn = $stmtTahun->fetch()) {
        $activeTahunId = $thn['id'];
    }

    // Cek apakah sudah absen manual/scan hari ini (di SIAKAD_DB)
    $stmt_cek = $siakad_db->prepare("SELECT id FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ?");
    $stmt_cek->execute([$siswa_id, $tanggal]);
    $exist = $stmt_cek->fetch();

    // 1. Update Pusat
    if ($exist) {
        $stmt_upd = $siakad_db->prepare("UPDATE absensi_siswa SET status = ? WHERE id = ?");
        $stmt_upd->execute([$status, $exist['id']]);
    } else {
        $stmt_s = $core_db->prepare("SELECT kelas_id FROM siswa WHERE id = ?");
        $stmt_s->execute([$siswa_id]);
        $s_info = $stmt_s->fetch(PDO::FETCH_ASSOC);
        $kelas_id = $s_info ? $s_info['kelas_id'] : 0;
        
        $stmt_ins = $siakad_db->prepare("INSERT INTO absensi_siswa (siswa_id, kelas_id, tanggal, status) VALUES (?, ?, ?, ?)");
        $stmt_ins->execute([$siswa_id, $kelas_id, $tanggal, $status]);
    }
    
    // 2. ATURAN MUTLAK (Retroaktif): Jika Izin/Sakit/Alpa, ubah paksa semua absen mapel di hari itu
    if (in_array($status, ['Izin', 'Sakit', 'Alpa', 'Alpha'])) {
        $stmt_override = $siakad_db->prepare("UPDATE absensi_permapel SET status = ? WHERE siswa_id = ? AND tanggal = ?");
        $stmt_override->execute([$status, $siswa_id, $tanggal]);
    }

    echo json_encode([
        'success' => true,
        'message' => "Kehadiran $nama_siswa berhasil diset $status. (Berlaku mutlak)"
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
