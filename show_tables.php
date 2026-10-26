<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=aula_siakad_db', 'root', '');
$res = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
print_r($res);
