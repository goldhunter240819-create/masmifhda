<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=aula_db;charset=utf8', 'root', '');
$stmt = $pdo->query('SHOW COLUMNS FROM bos_bku');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
