<?php
require_once 'koneksi.php';

if (isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 99])) {
    unset($_SESSION['siswa_id'], $_SESSION['siswa_nama'], $_SESSION['siswa_nis'], $_SESSION['siswa_status']);
    unset($_SESSION['guru_id'], $_SESSION['guru_nama'], $_SESSION['guru_nip']);
} else {
    // Hapus semua session
    session_unset();
    session_destroy();
}

// Hapus cookie remember me untuk siswa dan guru
setcookie('apk_remember_siswa', '', time() - 3600, "/");
setcookie('apk_remember_guru', '', time() - 3600, "/");

// Redirect kembali ke halaman login APK
header("Location: login.php");
exit;
