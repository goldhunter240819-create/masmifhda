<?php
require 'c:/xampp/htdocs/masmifhda/apk/koneksi.php';
$stmt_inst = $core_db->query("SELECT * FROM institusi LIMIT 1");
$institusi = $stmt_inst->fetch(PDO::FETCH_ASSOC);
print_r($institusi);
