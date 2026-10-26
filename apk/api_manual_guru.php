<?php
date_default_timezone_set('Asia/Jakarta');
require_once 'koneksi.php';
header('Content-Type: application/json');

if (!isset($_SESSION['guru_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit;
}

// Cek akses scanner
$stmt_scan = $core_db->prepare("SELECT id FROM absen_akses_scanner WHERE guru_id = ? AND akses_guru = 1");
$stmt_scan->execute([$_SESSION['guru_id']]);
if (!$stmt_scan->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$guru_id = isset($input['guru_id']) ? intval($input['guru_id']) : 0;
$status = isset($input['status']) ? trim($input['status']) : '';

if (empty($guru_id) || empty($status)) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap.']);
    exit;
}

try {
    $stmt_guru = $core_db->prepare("SELECT nama FROM guru WHERE id = ?");
    $stmt_guru->execute([$guru_id]);
    $guru = $stmt_guru->fetch(PDO::FETCH_ASSOC);

    if (!$guru) {
        echo json_encode(['success' => false, 'message' => 'Guru tidak ditemukan.']);
        exit;
    }

    $today = date('Y-m-d');
    $jam_sekarang = date('H:i:s');

    $stmtCek = $siakad_db->prepare("SELECT id FROM absensi_guru WHERE tanggal = ? AND guru_id = ?");
    $stmtCek->execute([$today, $guru_id]);
    $absenAda = $stmtCek->fetch();

    if ($absenAda) {
        $stmtUpdate = $siakad_db->prepare("UPDATE absensi_guru SET status = ? WHERE id = ?");
        $stmtUpdate->execute([$status, $absenAda['id']]);
    } else {
        $stmtInsert = $siakad_db->prepare("INSERT INTO absensi_guru (guru_id, tanggal, waktu_masuk, status, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmtInsert->execute([$guru_id, $today, $jam_sekarang, $status]);
    }

    echo json_encode(['success' => true, 'message' => 'Berhasil menyimpan status ' . $status . ' untuk ' . $guru['nama']]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}
