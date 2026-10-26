<?php
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['role_id'] = 1;

$_POST['guru_id'] = 1;
$_POST['siswa_ids'] = [1]; // Assuming siswa_id 1 exists

require_once __DIR__ . '/app/Core/Database.php';
require_once __DIR__ . '/app/Controllers/SiakadGuruWaliController.php';

$controller = new \App\Controllers\SiakadGuruWaliController();
$controller->assign();
