<?php
require_once 'apk/koneksi.php';
$stmt = $siakad_db->query("SELECT * FROM ajuan_izin_guru WHERE tanggal_izin = CURDATE()");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
$stmt2 = $siakad_db->query("SELECT * FROM absensi_guru WHERE tanggal = CURDATE() AND guru_id=11");
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
?>
