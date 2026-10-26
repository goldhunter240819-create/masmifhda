<?php
require_once 'apk/koneksi.php';
$stmt = $siakad_db->query("UPDATE jurnal_guru SET mapel_id = 6 WHERE id = 1229");
echo "Updated to mapel_id 6";
?>
