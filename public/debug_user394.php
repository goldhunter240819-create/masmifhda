<?php
require_once __DIR__ . '/../app/Core/Database.php';

header('Content-Type: application/json');
$secret = $_GET['secret'] ?? '';
if ($secret !== 'MIFHDA_DEBUG_394') {
    die(json_encode(['error' => 'unauthorized']));
}

$core = \App\Core\Database::connect('core');

$users = $core->query("SELECT id, username, plain_password, role_id, created_at FROM users WHERE id BETWEEN 385 AND 405 ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

// Also let's check all siswa where user_id BETWEEN 385 and 405, or id BETWEEN 385 and 405
$siswas = $core->query("SELECT id, user_id, nis, nisn, nik, nama, kelas_id FROM siswa WHERE user_id BETWEEN 385 AND 405 OR id BETWEEN 385 AND 405 ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

// Let's also check all siswa where nis or nisn or nik begins with 462 or has 46278530
$siswas2 = $core->query("SELECT id, user_id, nis, nisn, nik, nama, kelas_id FROM siswa WHERE nis LIKE '%462%' OR nisn LIKE '%462%' OR nik LIKE '%462%'")->fetchAll(PDO::FETCH_ASSOC);

// Let's check users where username has 462
$users2 = $core->query("SELECT id, username, plain_password, role_id, created_at FROM users WHERE username LIKE '%462%'")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'users_range' => $users,
    'siswas_range' => $siswas,
    'siswas_like_462' => $siswas2,
    'users_like_462' => $users2
], JSON_PRETTY_PRINT);
