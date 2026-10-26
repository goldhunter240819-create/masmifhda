<?php
require 'koneksi.php';
print_r($core_db->query("SHOW COLUMNS FROM app_modules")->fetchAll(PDO::FETCH_ASSOC));
