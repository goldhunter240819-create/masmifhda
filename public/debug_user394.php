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

// Cari siapa siswa yang terhubung ke user 394 di core
$siswaCore = $core->query("SELECT * FROM siswa WHERE user_id = 394 OR id = 394")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'siakadCols' => $siakadCols,
    'coreCols' => $coreCols,
    'user394' => $user394,
    'siswaCore' => $siswaCore
]);


