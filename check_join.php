<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=aula_db', 'root', '');
$res = $pdo->query("SELECT g.id, g.nama, g.user_id, u.username FROM guru g JOIN users u ON g.user_id = u.id ORDER BY g.nama ASC")->fetchAll(PDO::FETCH_ASSOC);
print_r($res);
