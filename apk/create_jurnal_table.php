<?php
require 'koneksi.php';

$sql = "CREATE TABLE IF NOT EXISTS jurnal_guru (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    guru_id INT(11) NOT NULL,
    kelas_id INT(11) NOT NULL,
    mapel_id INT(11) NOT NULL,
    tanggal DATE NOT NULL,
    materi TEXT,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

try {
    $siakad_db->exec($sql);
    echo "Table jurnal_guru created successfully.";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
