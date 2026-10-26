<?php
$db = new PDO('mysql:host=127.0.0.1;dbname=aula_db', 'root', '');
print_r($db->query('DESCRIBE institusi')->fetchAll(PDO::FETCH_ASSOC));
print_r($db->query('SELECT * FROM institusi LIMIT 1')->fetchAll(PDO::FETCH_ASSOC));
