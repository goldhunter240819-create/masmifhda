<?php
require_once __DIR__ . '/../app/Core/Database.php';

header('Content-Type: application/json');
$secret = $_GET['secret'] ?? '';
if ($secret !== 'MIFHDA_DEBUG_394') {
    die(json_encode(['error' => 'unauthorized']));
}

$core = \App\Core\Database::connect('core');
$siakad = \App\Core\Database::connect('siakad');

$siakadUsers = [];
try {
    $siakadUsers = $siakad->query("SELECT * FROM users WHERE id = 394 OR id = 53 OR username = '46278530' OR username = '240005'")->fetchAll(PDO::FETCH_ASSOC);
} catch (\Throwable $e) {
    $siakadUsers = ['error' => $e->getMessage()];
}

// Let's also check all students in core.siswa: who are they?
// In our earlier check, there were 92 students in core.siswa.
// Let's check which student in core.siswa corresponds to users 381..405!
$allSiswaWithHexUsers = $core->query("SELECT s.id as siswa_id, s.nama, s.nis, s.user_id, u.username as user_username FROM siswa s LEFT JOIN users u ON s.user_id = u.id WHERE u.username REGEXP '^[0-9A-F]{8}$'")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'siakadUsers' => $siakadUsers,
    'siswaWithHexUsers' => $allSiswaWithHexUsers
], JSON_PRETTY_PRINT);
