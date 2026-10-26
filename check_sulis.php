<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=aula_db', 'root', '');
$res = $pdo->query("SELECT id, nama, user_id FROM guru WHERE nama LIKE '%Sulis%'")->fetchAll(PDO::FETCH_ASSOC);
print_r($res);

$res2 = $pdo->query("SELECT id, username FROM users WHERE username = 'guru_19'")->fetchAll(PDO::FETCH_ASSOC);
print_r($res2);
