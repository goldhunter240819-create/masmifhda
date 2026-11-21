<?php
require_once __DIR__ . '/../app/Core/Database.php';

header('Content-Type: application/json');
$secret = $_GET['secret'] ?? '';
if ($secret !== 'MIFHDA_DEBUG_394') {
    die(json_encode(['error' => 'unauthorized']));
}

$core = \App\Core\Database::connect('core');

$u381 = $core->query("SELECT id, username, plain_password, role_id FROM users WHERE id IN (381, 382, 383, 394)")->fetchAll(PDO::FETCH_ASSOC);

// Check presensi for user 394 - who is the student recorded for presensi of user 394?
// In daily attendance scanner (api_scan_siswa.php):
// When TOKEN:394:... was scanned this morning at 08:01:17:
// $core_db->prepare("SELECT id, nis, nama, kelas_id, user_id FROM siswa WHERE user_id = ?");
// Did it find any siswa? Or how did presensi insert user_id = 394?
$pres394 = $core->query("SELECT * FROM presensi WHERE user_id = 394 ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Let's check siakad.siswa! What are the students in siakad.siswa?
$siakad = \App\Core\Database::connect('siakad');
$siakadSiswa = $siakad->query("SELECT id, nis, nisn, nama_lengkap, kelas_id FROM siswa WHERE id = 394 OR id = 73 OR nis = '46278530' OR nisn = '46278530' LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Check cbt_peserta_khusus
$kj = $core->query("SELECT id, no_peserta, nama_siswa, kelas_nama, username, qr_token FROM cbt_peserta_khusus WHERE id = 394 OR username = '46278530' OR no_peserta = '46278530' OR qr_token LIKE '%394%' LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Let's check if there are other students in core.siswa with user_id NULL or 0 or not listed above!
$otherSiswa = $core->query("SELECT id, user_id, nis, nama FROM siswa WHERE id NOT BETWEEN 1 AND 92 LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'u381' => $u381,
    'pres394' => $pres394,
    'siakadSiswa' => $siakadSiswa,
    'kj' => $kj,
    'otherSiswa' => $otherSiswa
], JSON_PRETTY_PRINT);
