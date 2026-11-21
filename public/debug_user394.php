<?php
// Temporary endpoint to debug user 394
require_once __DIR__ . '/../app/Core/Database.php';

header('Content-Type: application/json');
$secret = $_GET['secret'] ?? '';
if ($secret !== 'MIFHDA_DEBUG_394') {
    die(json_encode(['error' => 'unauthorized']));
}

$core = \App\Core\Database::connect('core');
$siakad = \App\Core\Database::connect('siakad');

$user = $core->query("SELECT * FROM users WHERE id = 394")->fetch(PDO::FETCH_ASSOC);

$siswaCore = $core->query("SELECT * FROM siswa WHERE user_id = 394 OR id = 394 OR nis = '46278530' OR nik = '46278530'")->fetchAll(PDO::FETCH_ASSOC);
$siswaSiakad = $siakad->query("SELECT * FROM siswa WHERE user_id = 394 OR id = 394 OR nis = '46278530' OR nik = '46278530'")->fetchAll(PDO::FETCH_ASSOC);

$kjCore = $core->query("SELECT * FROM cbt_peserta_khusus WHERE id = 394 OR username = '46278530' OR no_peserta = '46278530'")->fetchAll(PDO::FETCH_ASSOC);

// Cari di semua siswa jika ada field yang cocok dengan 46278530
$anySiswa = $siakad->query("SELECT id, user_id, nis, nik, nama, status, kelas_id FROM siswa WHERE nis LIKE '%4627%' OR nik LIKE '%4627%' OR nama LIKE '%4627%'")->fetchAll(PDO::FETCH_ASSOC);

// Cari di users jika ada siswa lain
echo json_encode([
    'user' => $user,
    'siswaCore' => $siswaCore,
    'siswaSiakad' => $siswaSiakad,
    'kjCore' => $kjCore,
    'anySiswa' => $anySiswa
]);
