<?php
require 'app/Core/Database.php';
require 'app/Core/AcademicYear.php';
$db = \App\Core\Database::connect('core');
$siswas = $db->query("SELECT s.id, s.nama FROM siswa s LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id AND rks.tahun_ajaran_id IN (SELECT id FROM tahun_ajaran WHERE name = '2026/2027') WHERE s.status IN ('Aktif', 'Alumni') AND (rks.id IS NOT NULL OR s.tahun_ajaran = '2026/2027') AND COALESCE(rks.kelas_id, s.kelas_id) = 4 ORDER BY s.nama ASC")->fetchAll();
print_r($siswas);
