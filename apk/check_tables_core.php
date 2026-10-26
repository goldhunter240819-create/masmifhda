<?php
require 'koneksi.php';
print_r($core_db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN));
