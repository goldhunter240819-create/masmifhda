<?php
require_once __DIR__ . '/apk/koneksi.php';
$stmt = $core_db->prepare("SELECT id, judul, file_pdf FROM buku_bacaan WHERE id = 3");
$stmt->execute();
print_r($stmt->fetch(PDO::FETCH_ASSOC));
