<?php
/**
 * Konfigurasi Koneksi Database
 */
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "aula_absen_db";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set timezone ke Asia/Jakarta
date_default_timezone_set('Asia/Jakarta');

session_start();

// Ambil Pengaturan Sistem (Global) dari aula_db
$sys_q = mysqli_query($koneksi, "SELECT * FROM aula_db.app_modules WHERE app_code = 'absen_qr' LIMIT 1");
$sys_cfg = mysqli_fetch_assoc($sys_q);

// Defaults jika database kosong
if (!$sys_cfg) {
    $sys_cfg = [
        'app_name' => 'Absensi QR',
        'logo_path' => null,
        'favicon_path' => null,
        'theme_color' => '10b981'
    ];
}

// Mengamankan rute ke folder assets dari root web server
$base_web_path = "/public/images/";

// Shortcut variables
$app_name = $sys_cfg['app_name'];
$app_logo = $sys_cfg['logo_path'] ? $base_web_path . $sys_cfg['logo_path'] : null;
$app_favicon = $sys_cfg['favicon_path'] ? $base_web_path . $sys_cfg['favicon_path'] : '/public/absen/assets/img/favicon.png';

// Theme Color Override via CSS Variables
$theme_color = $sys_cfg['theme_color'];
if(strpos($theme_color, '#') === false) $theme_color = '#' . $theme_color;
?>
<style>
    :root {
        --primary-rgb: <?php 
            // Convert hex to rgb
            list($r, $g, $b) = sscanf($theme_color, "#%02x%02x%02x");
            echo "$r, $g, $b";
        ?> !important;
        --accent-color: <?php echo $theme_color; ?> !important;
        --primary-gradient: linear-gradient(135deg, <?php echo $theme_color; ?> 0%, rgba(<?php echo "$r, $g, $b"; ?>, 0.8) 100%) !important;
    }
</style>
