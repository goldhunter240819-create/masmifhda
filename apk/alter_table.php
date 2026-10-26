<?php
require 'koneksi.php';
try {
    $core_db->exec("ALTER TABLE kurikulum_supervisi_administrasi ADD COLUMN penilai_id INT NULL, ADD COLUMN penilai_nama VARCHAR(100) NULL");
    echo "Columns added successfully";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
