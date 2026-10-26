<?php
require 'c:\xampp\htdocs\masmifhda\apk\koneksi.php';
print_r($siakad_db->query("DESCRIBE absensi_siswa")->fetchAll(PDO::FETCH_ASSOC));
print_r($core_db->query("DESCRIBE presensi")->fetchAll(PDO::FETCH_ASSOC));
