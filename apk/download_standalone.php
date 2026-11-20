<?php
/**
 * Direct Download for CBT Standalone Madrasah Package
 */
require_once __DIR__ . '/../tools/build_cbt_standalone.php';
$zipFile = __DIR__ . '/../public/downloads/cbt_standalone_madrasah.zip';
if (!file_exists($zipFile) || (time() - filemtime($zipFile) > 86400)) {
    build_cbt_standalone_package();
}
if (file_exists($zipFile)) {
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="cbt_standalone_madrasah.zip"');
    header('Content-Length: ' . filesize($zipFile));
    header('Pragma: no-cache');
    readfile($zipFile);
    exit;
}
die("Paket standalone belum siap digenerate.");
