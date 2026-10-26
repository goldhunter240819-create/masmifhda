<?php
/**
 * download_kwitansi.php
 * Melayani file PNG kwitansi dengan header Content-Disposition: attachment
 * agar Android download manager menyimpannya langsung ke folder Downloads.
 *
 * Tidak menggunakan session — karena Android download manager membuat
 * request HTTP baru yang terpisah (tanpa cookie WebView).
 * Keamanan dijaga dengan HMAC token yang di-generate oleh save_kwitansi.php.
 */

$filename = isset($_GET['file'])  ? basename($_GET['file'])  : '';
$token    = isset($_GET['token']) ? trim($_GET['token'])     : '';

// Validasi format nama file (mencegah path traversal)
// Format: kwitansi_{siswa_id}_KW{id}_{NamaSiswa}_{timestamp}.png
if (
    empty($filename) ||
    !preg_match('/^kwitansi_\d+_KW\d+_[A-Za-z0-9_]+_\d+\.png$/', $filename) ||
    str_contains($filename, '..') ||
    str_contains($filename, '/')
) {
    http_response_code(403);
    exit('Akses ditolak: nama file tidak valid.');
}

// Verifikasi HMAC token (harus cocok dengan yang di-generate save_kwitansi.php)
$dlSecret      = 'MismifhdaDL_S3cr3t_2026';
$expectedToken = substr(hash_hmac('sha256', $filename, $dlSecret), 0, 24);

if (!hash_equals($expectedToken, $token)) {
    http_response_code(403);
    exit('Akses ditolak: token tidak valid.');
}

$filePath = __DIR__ . '/../storage/kwitansi_tmp/' . $filename;

if (!file_exists($filePath)) {
    http_response_code(404);
    exit('File tidak ditemukan. Silakan coba download ulang.');
}

// Ekstrak nama siswa dan nomor KW dari nama file
// Format: kwitansi_{id}_KW{num}_{NamaSiswa}_{ts}.png
$downloadName = 'Kwitansi.png'; // fallback
if (preg_match('/^kwitansi_\d+_(KW\d+)_([A-Za-z0-9_]+)_(\d+)\.png$/', $filename, $m)) {
    $noKw         = $m[1];                                        // KW00004
    $namaDisplay  = str_replace('_', ' ', $m[2]);                 // Mohammad Syamil Arif
    $tglDownload  = date('d-m-Y', (int)$m[3]);                   // 20-06-2026
    $downloadName = 'Kwitansi_' . $noKw . '_' . str_replace(' ', '_', $namaDisplay) . '_' . $tglDownload . '.png';
    // Contoh hasil: Kwitansi_KW00004_Mohammad_Syamil_Arif_20-06-2026.png
}

// Kirim file dengan header attachment — Android download manager simpan ke Downloads
header('Content-Type: image/png');
header('Content-Disposition: attachment; filename="' . $downloadName . '"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('X-Content-Type-Options: nosniff');

readfile($filePath);
exit;
