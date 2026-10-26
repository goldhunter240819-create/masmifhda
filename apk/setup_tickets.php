<?php
require_once 'koneksi.php';

$core_db->exec("CREATE TABLE IF NOT EXISTS it_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL DEFAULT 0,
    user_type ENUM('guru','siswa') NOT NULL DEFAULT 'guru',
    nama_pelapor VARCHAR(150) NOT NULL,
    judul VARCHAR(255) NOT NULL,
    deskripsi TEXT NOT NULL,
    kategori VARCHAR(50) NOT NULL DEFAULT 'Lainnya',
    status ENUM('Baru','Diproses','Selesai') NOT NULL DEFAULT 'Baru',
    balasan TEXT NULL,
    dibuat_pada DATETIME DEFAULT CURRENT_TIMESTAMP,
    diperbarui_pada DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

echo "<h2 style='font-family:sans-serif;color:green;'>✅ Tabel it_tickets berhasil dibuat!</h2>";
echo "<p style='font-family:sans-serif'>Hapus file ini setelah setup selesai.</p>";
?>
