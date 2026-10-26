<?php
require 'koneksi.php';

try {
    $core_db->exec("TRUNCATE TABLE keuangan_tabungan_mutasi");
    $core_db->exec("TRUNCATE TABLE keuangan_tabungan");
    echo "Data mutasi tabungan berhasil dihapus.\n";
    
    // Juga hapus absensi hari ini yang baru saja dibuat tes
    $siakad_db->exec("DELETE FROM absensi_siswa WHERE tanggal = CURDATE()");
    echo "Data absensi hari ini berhasil dihapus.\n";
    
    // Juga hapus pembayaran komite tes hari ini
    $core_db->exec("DELETE FROM keuangan_komite_pembayaran WHERE tanggal_bayar = CURDATE()");
    
    // Kembalikan status tagihan ke Belum Lunas
    $core_db->exec("UPDATE keuangan_tagihan SET status = 'Belum Lunas' WHERE status != 'Belum Lunas'");
    echo "Data pembayaran komite hari ini berhasil dihapus.\n";
    
} catch (Exception $e) {
    echo "Gagal: " . $e->getMessage();
}
?>
