<?php
require_once __DIR__ . '/../app/Core/Database.php';

header('Content-Type: application/json');
$secret = $_GET['secret'] ?? '';
if ($secret !== 'MIFHDA_DEBUG_394') {
    die(json_encode(['error' => 'unauthorized']));
}

$core = \App\Core\Database::connect('core');
$siakad = \App\Core\Database::connect('siakad');

$siakadAbsenToday = $siakad->query("SELECT a.*, s.nama_lengkap, s.nis FROM absensi_siswa a LEFT JOIN siswa s ON a.siswa_id = s.id WHERE a.tanggal = '2026-11-21' ORDER BY a.id DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

// Also let's check core presensi where user_id = 394:
// Is there a correlation with any row in siakad.absensi_siswa around 08:01:17?
$siakadAbsen8am = $siakad->query("SELECT a.*, s.nama_lengkap, s.nis FROM absensi_siswa a LEFT JOIN siswa s ON a.siswa_id = s.id WHERE a.tanggal = '2026-11-21' AND a.created_at LIKE '2026-11-21 08:01%'")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'siakadAbsenToday' => $siakadAbsenToday,
    'siakadAbsen8am' => $siakadAbsen8am
], JSON_PRETTY_PRINT);
