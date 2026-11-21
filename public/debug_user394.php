<?php
require_once __DIR__ . '/../app/Core/Database.php';

header('Content-Type: application/json');
$secret = $_GET['secret'] ?? '';
if ($secret !== 'MIFHDA_DEBUG_394') {
    die(json_encode(['error' => 'unauthorized']));
}

$core = \App\Core\Database::connect('core');

$uids = [385, 386, 389, 394, 395, 415, 417, 429];
$placeholders = implode(',', $uids);

$siswas = $core->query("SELECT id, user_id, nis, nama, kelas_id FROM siswa WHERE user_id IN ($placeholders)")->fetchAll(PDO::FETCH_ASSOC);

// Also check in siakad
$siakad = \App\Core\Database::connect('siakad');
$siakadSiswas = [];
try {
    $siakadSiswas = $siakad->query("SELECT id, nis, nama_lengkap, kelas_id FROM siswa WHERE id IN ($placeholders)")->fetchAll(PDO::FETCH_ASSOC);
} catch (\Throwable $e) {}

// Also check if there is an id_mapping or user relation table in siakad or core!
$relTables = [];
$tables = $core->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $t) {
    if (strpos($t, 'user') !== false || strpos($t, 'map') !== false || strpos($t, 'rfid') !== false || strpos($t, 'kartu') !== false || strpos($t, 'member') !== false) {
        $relTables[] = $t;
    }
}

echo json_encode([
    'siswas_found' => $siswas,
    'siakadSiswas_found' => $siakadSiswas,
    'relTables' => $relTables
], JSON_PRETTY_PRINT);
