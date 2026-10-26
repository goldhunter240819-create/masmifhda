<?php
$pdoSiakad = new PDO('mysql:host=127.0.0.1;dbname=aula_siakad_db', 'root', '');
$ta = $pdoSiakad->query("SELECT * FROM tahun_ajaran WHERE is_active = 1")->fetch(PDO::FETCH_ASSOC);
$sem = $pdoSiakad->query("SELECT semester_aktif FROM pengaturan LIMIT 1")->fetch(PDO::FETCH_ASSOC);

print_r($ta);
print_r($sem);

$count = $pdoSiakad->query("SELECT COUNT(*) FROM jadwal_pelajaran")->fetchColumn();
echo "Total Jadwal inserted: " . $count . "\n";

$first = $pdoSiakad->query("SELECT * FROM jadwal_pelajaran LIMIT 1")->fetch(PDO::FETCH_ASSOC);
print_r($first);
