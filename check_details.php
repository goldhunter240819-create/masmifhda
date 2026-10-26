<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=aula_db', 'root', '');
$res = $pdo->query("SELECT * FROM users WHERE id = 339")->fetchAll(PDO::FETCH_ASSOC);
print_r($res);

$res2 = $pdo->query("SELECT * FROM guru WHERE user_id = 339")->fetchAll(PDO::FETCH_ASSOC);
print_r($res2);

$res3 = $pdo->query("SELECT * FROM guru WHERE nama LIKE '%Sulis%'")->fetchAll(PDO::FETCH_ASSOC);
print_r($res3);
