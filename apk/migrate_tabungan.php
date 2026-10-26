<?php
require 'koneksi.php';
try {
    // 1. Insert tabungan to app_modules if not exists
    $stmt = $core_db->prepare("INSERT IGNORE INTO app_modules (app_code, app_name, tab_title, app_version, theme_color) VALUES ('tabungan', 'Admin Tabungan', 'Tabungan', '1.0', '#f59e0b')");
    $stmt->execute();
    
    // 2. Create table admin_tabungan_akses
    $sql = "
    CREATE TABLE IF NOT EXISTS admin_tabungan_akses (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        kelas_id INT(11) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY user_kelas (user_id, kelas_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $core_db->exec($sql);
    
    echo "SUCCESS: Database migrated.";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
