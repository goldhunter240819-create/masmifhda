<?php
try {
    $db = new PDO("mysql:host=localhost;dbname=aula_db;charset=utf8", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create kurikulum_pkg_guru
    $sql1 = "CREATE TABLE IF NOT EXISTS `kurikulum_pkg_guru` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `guru_id` int(11) NOT NULL,
        `periode_penilaian` varchar(255) NOT NULL,
        `tahun` varchar(10) NOT NULL,
        `penilai_id` int(11) NOT NULL,
        `tanggal_penilaian` date DEFAULT NULL,
        `status_selesai` enum('Belum','Proses','Selesai') DEFAULT 'Belum',
        `total_skor_14_kompetensi` int(11) DEFAULT 0,
        `nilai_akhir_pkg` double DEFAULT 0,
        `sebutan` varchar(50) DEFAULT NULL,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $db->query($sql1);
    echo "Table kurikulum_pkg_guru created or already exists.\n";
    
    // Create kurikulum_pkg_nilai
    $sql2 = "CREATE TABLE IF NOT EXISTS `kurikulum_pkg_nilai` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `pkg_id` int(11) NOT NULL,
        `kompetensi_ke` int(11) NOT NULL,
        `skor_json` text NOT NULL,
        `total_skor` int(11) DEFAULT 0,
        `nilai_konversi` double DEFAULT 0,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `pkg_kompetensi_unique` (`pkg_id`,`kompetensi_ke`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $db->query($sql2);
    echo "Table kurikulum_pkg_nilai created or already exists.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
