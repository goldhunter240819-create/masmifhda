<?php
require_once 'apk/koneksi.php';
$stmt = $siakad_db->query("SELECT j.id, j.hari, j.jam_mulai, m.nama_mapel, j.guru_id FROM jadwal_pelajaran j LEFT JOIN mapel m ON j.mata_pelajaran_id = m.id WHERE j.kelas_id = 1 AND j.hari IN ('Senin', 'Selasa') ORDER BY j.hari, j.jam_mulai");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

$stmt2 = $siakad_db->query("SELECT j.id, j.tanggal, j.mapel_id, j.guru_id FROM jurnal_guru j WHERE j.kelas_id = 1 AND j.tanggal = '2026-09-21'");
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
?>
