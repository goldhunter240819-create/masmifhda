<?php
require 'vendor/autoload.php';
$db = new PDO('mysql:host=localhost;dbname=aula_db', 'root', '');
$stmt = $db->query('SHOW TABLES');
print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
