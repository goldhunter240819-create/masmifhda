<?php
$pdo = new PDO('mysql:host=127.0.0.1', 'root', '');
$stmt = $pdo->query('SHOW DATABASES');
print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
