<?php
require 'koneksi.php';
$stmt = $core_db->query('DESCRIBE kurikulum_supervisi_administrasi');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
