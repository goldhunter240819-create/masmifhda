<?php
$filename = isset($_GET['file']) ? basename($_GET['file']) : '';
if (empty($filename)) {
    die("Parameter file tidak valid.");
}

$filepath = __DIR__ . '/../public/downloads/temp/' . $filename;

if (file_exists($filepath)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filepath));
    readfile($filepath);
    exit;
} else {
    die("File tidak ditemukan atau sudah kadaluarsa.");
}
