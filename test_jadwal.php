<?php
require 'c:/xampp/htdocs/masmifhda/apk/koneksi.php';
$stmt = $siakad_db->query("DESCRIBE jadwal_pelajaran");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
