<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id']) && !isset($_SESSION['siswa_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$type = isset($_POST['type']) ? $_POST['type'] : '';

if (!isset($_FILES['foto'])) {
    echo json_encode(['success' => false, 'message' => 'Tidak ada file yang diunggah']);
    exit;
}

$file = $_FILES['foto'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed = ['jpg', 'jpeg', 'png'];

if (!in_array($ext, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Format file tidak didukung']);
    exit;
}

if ($file['size'] > 2000000) {
    echo json_encode(['success' => false, 'message' => 'Ukuran file maksimal 2MB']);
    exit;
}

$upload_dir = $type === 'guru' ? '../public/uploads/guru/' : '../public/uploads/siswa/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$filename = ($type === 'guru' ? 'guru_' : 'siswa_') . time() . '_' . rand(100,999) . '.' . $ext;
$target = $upload_dir . $filename;

if (move_uploaded_file($file['tmp_name'], $target)) {
    try {
        if ($type === 'guru' && isset($_SESSION['guru_id'])) {
            $stmt = $core_db->prepare("SELECT foto FROM guru WHERE id = ?");
            $stmt->execute([$_SESSION['guru_id']]);
            $old = $stmt->fetchColumn();
            if ($old && file_exists($upload_dir . $old)) @unlink($upload_dir . $old);

            $stmt = $core_db->prepare("UPDATE guru SET foto = ? WHERE id = ?");
            $stmt->execute([$filename, $_SESSION['guru_id']]);
        } else if ($type === 'siswa' && isset($_SESSION['siswa_id'])) {
            $stmt = $core_db->prepare("SELECT foto FROM siswa WHERE id = ?");
            $stmt->execute([$_SESSION['siswa_id']]);
            $old = $stmt->fetchColumn();
            if ($old && file_exists($upload_dir . $old)) @unlink($upload_dir . $old);

            $stmt = $core_db->prepare("UPDATE siswa SET foto = ? WHERE id = ?");
            $stmt->execute([$filename, $_SESSION['siswa_id']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid session']);
            exit;
        }

        echo json_encode(['success' => true, 'message' => 'Foto berhasil diperbarui']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal mengunggah file']);
}
