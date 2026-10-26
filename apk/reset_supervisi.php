<?php
require 'koneksi.php';
try {
    $core_db->exec("TRUNCATE TABLE kurikulum_supervisi_administrasi");
    echo "Table truncated successfully";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
