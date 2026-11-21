<?php
require_once __DIR__ . '/../app/Core/Database.php';

header('Content-Type: application/json');
$secret = $_GET['secret'] ?? '';
if ($secret !== 'MIFHDA_DEBUG_394') {
    die(json_encode(['error' => 'unauthorized']));
}

$core = \App\Core\Database::connect('core');
$siakad = \App\Core\Database::connect('siakad');

$user = $core->query("SELECT * FROM users WHERE id = 394")->fetch(PDO::FETCH_ASSOC);

$siakadCols = $siakad->query("DESCRIBE siswa")->fetchAll(PDO::FETCH_COLUMN);
$coreCols = $core->query("DESCRIBE siswa")->fetchAll(PDO::FETCH_COLUMN);

// Siswa di Siakad dengan id=394 atau username 46278530
$siswaById = $siakad->query("SELECT * FROM siswa WHERE id = 394")->fetch(PDO::FETCH_ASSOC);
$siswaByNis = $siakad->query("SELECT * FROM siswa WHERE nis = '46278530' OR nik = '46278530'")->fetch(PDO::FETCH_ASSOC);

// Cari siapa yang punya id=394 di core
$siswaCoreById = null;
try {
    $siswaCoreById = $core->query("SELECT * FROM siswa WHERE id = 394")->fetch(PDO::FETCH_ASSOC);
} catch (\Exception $e) {}

echo json_encode([
    'user' => $user,
    'siakadCols' => $siakadCols,
    'coreCols' => $coreCols,
    'siswaById' => $siswaById,
    'siswaByNis' => $siswaByNis,
    'siswaCoreById' => $siswaCoreById
]);

