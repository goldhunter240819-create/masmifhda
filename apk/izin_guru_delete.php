<?php
require_once 'koneksi.php';
header('Content-Type: application/json');

if (!isset($_SESSION['guru_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$id = $_POST['id'] ?? 0;
if ($id > 0) {
    try {
        $stmt = $siakad_db->prepare("DELETE FROM ajuan_izin_guru WHERE id = ? AND guru_id = ? AND status = 'Pending'");
        $stmt->execute([$id, $_SESSION['guru_id']]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Pengajuan berhasil dibatalkan']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal membatalkan pengajuan (Mungkin sudah disetujui/ditolak)']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
}
