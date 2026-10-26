<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=aula_db;charset=utf8', 'root', '');
$stmt = $pdo->query('SHOW TABLES LIKE "%bku%"');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
$stmt = $pdo->query('SHOW TABLES LIKE "%kas%"');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
$stmt = $pdo->query('SHOW TABLES LIKE "%keuangan%"');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
