<?php
require_once __DIR__ . '/../app/Core/Database.php';

header('Content-Type: application/json');
$secret = $_GET['secret'] ?? '';
if ($secret !== 'MIFHDA_DEBUG_394') {
    die(json_encode(['error' => 'unauthorized']));
}

$core = \App\Core\Database::connect('core');

$uList = $core->query("SELECT * FROM users WHERE id BETWEEN 380 AND 410 ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($uList, JSON_PRETTY_PRINT);
