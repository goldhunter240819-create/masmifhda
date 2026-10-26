<?php
require 'koneksi.php';
print_r($core_db->query("SHOW COLUMNS FROM app_admins")->fetchAll(PDO::FETCH_ASSOC));
