<?php
require 'c:/xampp/htdocs/masmifhda/apk/koneksi.php';
$stmt = $core_db->query("SELECT * FROM tahun_ajaran WHERE id = 2");
print_r($stmt->fetch(PDO::FETCH_ASSOC));
