<?php
require_once __DIR__ . '/../app/Core/Database.php';

header('Content-Type: application/json');
$secret = $_GET['secret'] ?? '';
if ($secret !== 'MIFHDA_DEBUG_394') {
    die(json_encode(['error' => 'unauthorized']));
}

$core = \App\Core\Database::connect('core');

$u285 = $core->query("SELECT * FROM users WHERE id IN (285, 394)")->fetchAll(PDO::FETCH_ASSOC);
$s53 = $core->query("SELECT * FROM siswa WHERE id = 53")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'users' => $u285,
    'siswa53' => $s53
], JSON_PRETTY_PRINT);
