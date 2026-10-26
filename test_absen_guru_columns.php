<?php
$db = new PDO('mysql:host=127.0.0.1;dbname=aula_siakad_db', 'root', '');
print_r($db->query('DESCRIBE absensi_guru')->fetchAll(PDO::FETCH_ASSOC));
