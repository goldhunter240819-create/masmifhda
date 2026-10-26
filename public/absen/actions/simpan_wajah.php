<?php
include '../includes/koneksi.php';

header('Content-Type: application/json');

if (isset($_POST['user_id']) && isset($_POST['descriptor'])) {
    $user_id = mysqli_real_escape_string($koneksi, $_POST['user_id']);
    $descriptor = mysqli_real_escape_string($koneksi, $_POST['descriptor']);

    $query = "UPDATE users SET face_descriptor = '$descriptor' WHERE id = '$user_id'";
    if (mysqli_query($koneksi, $query)) {
        echo json_encode(['status' => 'success', 'message' => 'Sidik jari wajah berhasil disimpan.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data ke database.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
}
?>
