<?php
require_once 'apk/koneksi.php';
$stmt = $siakad_db->query("SELECT j.id, j.hari, j.jam_mulai, m.nama_mapel, j.guru_id FROM jadwal_pelajaran j LEFT JOIN mapel m ON j.mata_pelajaran_id = m.id WHERE j.kelas_id = 1 AND j.mata_pelajaran_id IN (5, 6) ORDER BY j.hari, j.jam_mulai");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
