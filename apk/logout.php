<?php
require_once 'koneksi.php';

// 1. Hapus seluruh data di session
$_SESSION = [];
if (session_status() === PHP_SESSION_ACTIVE) {
    session_unset();
    session_destroy();
}

// 2. Hapus session cookie browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Hapus semua persistent remember cookies
$cookiesToClear = [
    'apk_remember_siswa',
    'apk_remember_guru',
    'apk_remember_cbt',
    'cbt_peserta_login',
    'ghtech_vid'
];

foreach ($cookiesToClear as $cName) {
    setcookie($cName, '', time() - 86400, '/');
    setcookie($cName, '', time() - 86400, '');
    unset($_COOKIE[$cName]);
}

// 4. Redirect kembali ke halaman login APK
header("Location: login.php");
exit;
