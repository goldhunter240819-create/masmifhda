<?php
require 'koneksi.php';
$stmt = $core_db->query('SHOW COLUMNS FROM guru');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
