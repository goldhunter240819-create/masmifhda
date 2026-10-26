<?php
require_once 'apk/koneksi.php';
$stmt = $siakad_db->query("SELECT id, guru_id, status FROM absensi_guru WHERE tanggal = CURDATE()");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
