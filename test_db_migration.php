<?php
require 'apk/koneksi.php';

try {
    $db = $core_db; // aula_db
    $sql = "CREATE TABLE IF NOT EXISTS guru_wali_siswa (
        id INT AUTO_INCREMENT PRIMARY KEY,
        guru_id INT NOT NULL,
        siswa_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY(guru_id, siswa_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $db->exec($sql);
    echo "Tabel guru_wali_siswa berhasil dibuat/sudah ada di aula_db.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
