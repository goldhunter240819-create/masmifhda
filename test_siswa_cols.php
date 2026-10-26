<?php
require 'apk/koneksi.php';
$q = $core_db->query("DESCRIBE siswa")->fetchAll(PDO::FETCH_ASSOC);
print_r($q);
