<?php
require 'koneksi.php';
$stmt = $siakad_db->query('DESCRIBE absensi_permapel');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
$stmt2 = $siakad_db->query('SHOW INDEXES FROM absensi_permapel');
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
