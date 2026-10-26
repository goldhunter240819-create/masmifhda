<?php
require 'apk/koneksi.php';
print_r($core_db->query("DESCRIBE presensi")->fetchAll(PDO::FETCH_ASSOC));
