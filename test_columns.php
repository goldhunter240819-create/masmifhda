<?php
require_once 'c:/xampp/htdocs/masmifhda/apk/koneksi.php';
$stmt = $siakad_db->query("DESCRIBE absensi_guru");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($cols);
