<?php
require_once 'koneksi.php';
try {
    $core_db->exec("ALTER TABLE pesan_chat ADD COLUMN is_delivered TINYINT(1) DEFAULT 0 AFTER is_read");
    echo "Success";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
