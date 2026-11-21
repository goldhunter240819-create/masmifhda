<?php
require_once __DIR__ . '/../app/Core/Database.php';

header('Content-Type: application/json');
$secret = $_GET['secret'] ?? '';
if ($secret !== 'MIFHDA_DEBUG_394') {
    die(json_encode(['error' => 'unauthorized']));
}

$core = \App\Core\Database::connect('core');
$siakad = \App\Core\Database::connect('siakad');

$siakadCols = $siakad->query("DESCRIBE siswa")->fetchAll(PDO::FETCH_COLUMN);
$coreCols = $core->query("DESCRIBE siswa")->fetchAll(PDO::FETCH_COLUMN);

$user394 = $core->query("SELECT * FROM users WHERE id = 394")->fetch(PDO::FETCH_ASSOC);

$byNis = $siakad->query("SELECT * FROM siswa WHERE nis = '46278530' OR nisn = '46278530'")->fetchAll(PDO::FETCH_ASSOC);
$byId = $siakad->query("SELECT * FROM siswa WHERE id = 394")->fetchAll(PDO::FETCH_ASSOC);

// Juga cek di core
$coreByNis = $core->query("SELECT * FROM siswa WHERE nis = '46278530' OR nisn = '46278530' OR nik = '46278530'")->fetchAll(PDO::FETCH_ASSOC);
$kjCheck = $core->query("SELECT * FROM cbt_peserta_khusus WHERE id = 394 OR username = '46278530' OR no_peserta = '46278530'")->fetchAll(PDO::FETCH_ASSOC);

// Cek apakah ada siswa di siakad yang username 46278530 cocok dengan tahun_masuk atau apapun
$similar = $siakad->query("SELECT id, nis, nisn, nama_lengkap, kelas_id, status FROM siswa WHERE nis LIKE '%4627%' OR nisn LIKE '%4627%'")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'byNis' => $byNis,
    'byId' => $byId,
    'coreByNis' => $coreByNis,
    'kjCheck' => $kjCheck,
    'similar' => $similar
]);


