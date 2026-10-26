<?php
require 'koneksi.php';

try {
    $core_db->beginTransaction();

    // 1. Hapus riwayat pembayaran SPP
    $core_db->exec("DELETE FROM keuangan_komite_pembayaran WHERE jenis_pembayaran = 'SPP'");

    // 2. Hapus tagihan SPP milik siswa
    $core_db->exec("DELETE FROM keuangan_tagihan WHERE nama_tagihan = 'SPP'");

    // 3. Hapus Master Jenis Tagihan bernama SPP
    // Kita coba eksekusi di tabel komite_jenis_tagihan atau keuangan_jenis_tagihan
    // Cari tahu dulu nama tabelnya:
    $stmt = $core_db->query("SHOW TABLES LIKE '%tagihan%'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach($tables as $t) {
        if ($t != 'keuangan_tagihan') {
            $core_db->exec("DELETE FROM `$t` WHERE nama_tagihan = 'SPP'");
        }
    }

    $core_db->commit();
    echo "Data dummy SPP berhasil dihanguskan!\n";
} catch (Exception $e) {
    $core_db->rollBack();
    echo "Gagal: " . $e->getMessage();
}
?>
