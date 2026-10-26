<?php
require 'app/Core/Database.php';
$db = \App\Core\Database::connect('core');
$s = $db->query("SELECT * FROM siswa WHERE id = 88")->fetch(PDO::FETCH_ASSOC);
$json = json_encode($s);
if ($json === false) {
    echo "JSON Encode Failed: " . json_last_error_msg() . "\n";
} else {
    echo "JSON OK: " . substr($json, 0, 50) . "...\n";
}
