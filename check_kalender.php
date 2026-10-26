<?php
require 'c:\xampp\htdocs\masmifhda\apk\koneksi.php';
$stmt = $siakad_db->query("DESCRIBE kalender_akademik");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
