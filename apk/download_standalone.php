<?php
/**
 * Direct Download for CBT Standalone Madrasah Package
 */
$syncSecret = 'MIFHDA_CBT_SYNC_2026';
$providedToken = $_REQUEST['token'] ?? $_SERVER['HTTP_X_SYNC_TOKEN'] ?? '';

// Izinkan jika token cocok atau ada session aktif
session_start();
if ($providedToken !== $syncSecret && empty($_SESSION['user_id'])) {
    http_response_code(401);
    die("Akses ditolak: Token sinkronisasi tidak valid.");
}

require_once __DIR__ . '/../tools/build_cbt_standalone.php';
$zipFile = __DIR__ . '/../public/downloads/cbt_standalone_madrasah.zip';

// Otomatis regenerate jika diminta (force=1), belum ada berkas, atau usia zip > 1800 detik (30 menit)
if (!empty($_GET['force']) || !file_exists($zipFile) || (time() - filemtime($zipFile) > 1800)) {
    build_cbt_standalone_package();
}

if (file_exists($zipFile)) {
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="cbt_standalone_madrasah.zip"');
    header('Content-Length: ' . filesize($zipFile));
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    readfile($zipFile);
    exit;
}
die("Paket standalone belum siap digenerate.");
