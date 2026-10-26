<?php
require 'c:/xampp/htdocs/masmifhda/apk/koneksi.php';

echo "--- JADWAL ERWANSAH (guru 5) ---\n";
$stmt = $siakad_db->prepare('SELECT jp.kelas_id, jp.tahun_ajaran_id, jp.semester, k.nama_kelas, mp.nama_mapel FROM jadwal_pelajaran jp JOIN aula_db.kelas k ON jp.kelas_id = k.id JOIN mata_pelajaran mp ON jp.mata_pelajaran_id = mp.id WHERE jp.guru_id = 5');
$stmt->execute();
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
