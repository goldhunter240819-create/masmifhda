<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    try {
        $core_db->beginTransaction();
        
        // Pastikan milik guru yang login
        $stmt_check = $core_db->prepare("SELECT id FROM nilai_harian WHERE id = ? AND guru_id = ?");
        $stmt_check->execute([$id, $guru_id]);
        if ($stmt_check->fetch()) {
            // Hapus detail
            $stmt_del_dtl = $core_db->prepare("DELETE FROM nilai_harian_detail WHERE nilai_harian_id = ?");
            $stmt_del_dtl->execute([$id]);
            
            // Hapus header
            $stmt_del_hdr = $core_db->prepare("DELETE FROM nilai_harian WHERE id = ?");
            $stmt_del_hdr->execute([$id]);
            
            $core_db->commit();
            $_SESSION['msg'] = 'Data nilai harian berhasil dihapus!';
        } else {
            $core_db->rollBack();
            $_SESSION['msg'] = 'Akses ditolak atau data tidak ditemukan.';
        }
    } catch (Exception $e) {
        $core_db->rollBack();
        $_SESSION['msg'] = 'Gagal menghapus data: ' . $e->getMessage();
    }
}

header("Location: nilai_harian.php");
exit;
