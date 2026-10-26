<?php
include '../includes/koneksi.php';
include '../includes/sync_handler.php';

if (!isset($_SESSION['admin'])) {
    die("Akses ditolak.");
}

$type = isset($_GET['type']) ? $_GET['type'] : 'all';

try {
    syncSiakad($koneksi, $type);
    $_SESSION['msg'] = "success|Sinkronisasi SIAKAD Berhasil. Data telah diperbarui otomatis.";
} catch (Exception $e) {
    $_SESSION['msg'] = "error|Kegagalan sistem saat sinkronisasi: " . $e->getMessage();
}

$back_url = ($type == 'guru') ? '../pages/data_guru.php' : '../pages/data_siswa.php';
if ($type == 'all') $back_url = '../pages/dashboard.php';

header("Location: $back_url");
exit;
