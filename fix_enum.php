<?php
require_once 'apk/koneksi.php';

try {
    // Ubah kolom status menjadi VARCHAR agar bisa menampung 'Dinas Luar'
    $siakad_db->exec("ALTER TABLE absensi_guru MODIFY COLUMN status VARCHAR(50) DEFAULT 'Hadir'");
    echo "Kolom status berhasil diubah menjadi VARCHAR.<br>";

    // Perbaiki data yang kosong akibat enum error sebelumnya
    $siakad_db->exec("UPDATE absensi_guru SET status = 'Dinas Luar' WHERE status = '' OR status IS NULL");
    echo "Data status kosong berhasil diupdate menjadi 'Dinas Luar'.<br>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
