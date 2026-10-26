<?php
require 'c:/xampp/htdocs/masmifhda/apk/koneksi.php';
$stmt = $siakad_db->query('SHOW TABLES');
print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
