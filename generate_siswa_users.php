<?php
require 'vendor/autoload.php';
require 'app/Core/Database.php';

$db = \App\Core\Database::connect('core');
$siswas = $db->query('SELECT id, nama FROM siswa WHERE user_id IS NULL')->fetchAll();

foreach($siswas as $s) {
    $rand_user = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
    $rand_pass = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
    
    $stmt = $db->prepare("INSERT INTO users (username, password_hash, plain_password, role_id, status) VALUES (?, ?, ?, 3, 'active')");
    $stmt->execute([$rand_user, password_hash($rand_pass, PASSWORD_BCRYPT), $rand_pass]);
    
    $uid = $db->lastInsertId();
    $db->prepare("UPDATE siswa SET user_id = ? WHERE id = ?")->execute([$uid, $s['id']]);
    echo "Created for " . $s['nama'] . "\n";
}
echo "Done.";
