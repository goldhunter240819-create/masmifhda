<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=aula_db', 'root', '');
$res = $pdo->query("DESCRIBE guru")->fetchAll(PDO::FETCH_ASSOC);
print_r($res);
