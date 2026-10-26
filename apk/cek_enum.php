<?php
require 'koneksi.php';

// Cek apakah ada data yang kosong / invalid enum
$stmt = $siakad_db->query("SELECT * FROM absensi_permapel WHERE status = ''");
$invalid_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Data dengan status invalid (kosong) = " . count($invalid_data) . " baris.\n";
if (count($invalid_data) > 0) {
    echo "Contoh data:\n";
    print_r($invalid_data[0]);
}

$stmt2 = $siakad_db->query("SELECT status, count(*) as c FROM absensi_permapel GROUP BY status");
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
