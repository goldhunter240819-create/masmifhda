<?php
require 'c:\xampp\htdocs\masmifhda\apk\koneksi.php';
$stmt = $siakad_db->query("SELECT DISTINCT siswa_id, tanggal FROM absensi_permapel WHERE tanggal >= '2026-08-01'");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$count = 0;
foreach($rows as $r) {
    sinkronisasi_status_pusat($r['siswa_id'], $r['tanggal'], $core_db, $siakad_db);
    $count++;
}
echo "Synced $count records!";
?>
