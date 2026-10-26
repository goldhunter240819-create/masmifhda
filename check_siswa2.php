<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db = 'aula_db';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "--- Siswa with NIS ---\n";
    $q = $conn->query("SELECT id, nis, nama, user_id FROM siswa WHERE nis IS NOT NULL AND nis != '' LIMIT 5");
    while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
