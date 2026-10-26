<?php
require_once 'apk/koneksi.php';
$stmt = $siakad_db->query("SELECT id, nama_mapel FROM mapel WHERE id IN (5) OR nama_mapel LIKE '%Bahasa Arab%'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
