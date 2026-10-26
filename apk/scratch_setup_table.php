<?php
require 'koneksi.php';
$sql = "CREATE TABLE IF NOT EXISTS kurikulum_supervisi_kunjungan_kelas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tahun_ajaran_id INT NOT NULL,
    guru_id INT NOT NULL,
    mapel_id INT NOT NULL,
    kelas_id INT NOT NULL,
    tanggal_supervisi DATE NOT NULL,
    instrumen_json TEXT,
    nilai_akhir FLOAT DEFAULT 0,
    ketercapaian VARCHAR(5) DEFAULT '',
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$core_db->exec($sql);
echo "Tabel berhasil dibuat.";
?>
