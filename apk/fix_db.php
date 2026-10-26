<?php
require 'koneksi.php';

try {
    // 1. Ubah enum absensi_permapel
    $siakad_db->query("ALTER TABLE absensi_permapel MODIFY COLUMN status ENUM('Hadir', 'Izin', 'Sakit', 'Alpha', 'Alpa', 'Bolos') NOT NULL DEFAULT 'Hadir'");
    echo "Enum updated.\n";
    
    // 2. Tambahkan UNIQUE index agar ON DUPLICATE KEY UPDATE berfungsi
    // Hapus duplikat dulu sebelum nambah unique index biar ga error
    $siakad_db->query("
        DELETE t1 FROM absensi_permapel t1
        INNER JOIN absensi_permapel t2 
        WHERE 
            t1.id < t2.id AND 
            t1.siswa_id = t2.siswa_id AND 
            t1.mapel_id = t2.mapel_id AND 
            t1.tanggal = t2.tanggal
    ");
    echo "Duplicates removed.\n";
    
    // Cek apakah index unique sudah ada
    $stmt = $siakad_db->query("SHOW INDEXES FROM absensi_permapel WHERE Key_name = 'idx_unique_absen'");
    if (!$stmt->fetch()) {
        $siakad_db->query("ALTER TABLE absensi_permapel ADD UNIQUE INDEX idx_unique_absen (siswa_id, mapel_id, tanggal)");
        echo "Unique index added.\n";
    } else {
        echo "Unique index already exists.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
