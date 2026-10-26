<?php
require 'koneksi.php';
print_r($core_db->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_ASSOC));
