<?php
require 'c:/xampp/htdocs/masmifhda/apk/koneksi.php';
$stmt = $siakad_db->query('SELECT DISTINCT status FROM absensi_siswa');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
