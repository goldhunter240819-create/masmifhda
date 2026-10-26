<?php
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['role_id'] = 1;
$_GET['cetak'] = 1;
$_GET['tanggal_mulai'] = '2026-07-29';
$_GET['tanggal_sampai'] = '2026-07-29';
$_GET['kelas_id'] = 4;
$_GET['guru_id'] = 0;

require 'app/Core/Database.php';
require 'app/Controllers/SiakadJurnalGlobalController.php';

$controller = new App\Controllers\SiakadJurnalGlobalController();
ob_start();
$controller->index();
$html = ob_get_clean();
echo "Done! Read debug_jumat.txt for dump.";
?>
