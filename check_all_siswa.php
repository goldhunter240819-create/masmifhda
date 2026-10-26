<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db = 'aula_db';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "--- siswa Table cols ---\n";
    $q = $conn->query("DESCRIBE siswa");
    while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
