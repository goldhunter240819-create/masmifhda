<?php
require_once __DIR__ . '/app/Core/Database.php';

$db = \App\Core\Database::connect('core');
echo "Guru Columns:\n";
print_r($db->query("DESCRIBE guru")->fetchAll(PDO::FETCH_COLUMN));

echo "\nGuru Data:\n";
print_r($db->query("SELECT nama, nip, tugas_tambahan, status FROM guru")->fetchAll(PDO::FETCH_ASSOC));
