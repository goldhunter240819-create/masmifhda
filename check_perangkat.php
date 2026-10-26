<?php
$db = new PDO('mysql:host=localhost;dbname=siakad_db','root','');
$cols = $db->query('DESCRIBE perangkat_pembelajaran')->fetchAll(PDO::FETCH_ASSOC);
print_r($cols);
