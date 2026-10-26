<?php
require 'koneksi.php';
$stmt = $core_db->query('DESCRIBE keuangan_bos_transaksi');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
