<?php
require 'koneksi.php';
$stmt = $siakad_db->query('DESCRIBE absensi_siswa');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
