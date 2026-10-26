<?php
require_once 'koneksi.php';

echo "=== HAPUS JADWAL DUPLIKAT MAPEL_ID 22 (bukan mapel valid di SIAKAD) ===\n\n";

// Cek dulu semua jadwal yang pakai mapel_id = 22
$stmt = $siakad_db->prepare("
    SELECT j.id, j.hari, j.kelas_id, j.guru_id, j.jam_mulai, j.jam_selesai, j.tahun_ajaran_id, j.semester,
           g.nama as nama_guru, k.nama_kelas
    FROM jadwal_pelajaran j
    LEFT JOIN aula_db.guru g ON j.guru_id = g.id
    LEFT JOIN aula_db.kelas k ON j.kelas_id = k.id
    WHERE j.mata_pelajaran_id = 22
    ORDER BY j.hari, j.jam_mulai
");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Jadwal yang menggunakan mapel_id=22 (tidak valid di SIAKAD):\n";
foreach ($rows as $r) {
    echo "  ID: {$r['id']} | {$r['hari']} | Kelas: {$r['nama_kelas']} | Guru: {$r['nama_guru']} | {$r['jam_mulai']}-{$r['jam_selesai']} | TA: {$r['tahun_ajaran_id']} Sem: {$r['semester']}\n";
}
echo "\nTotal: " . count($rows) . " jadwal\n";

// Hapus semua jadwal dengan mapel_id = 22
$stmt_del = $siakad_db->prepare("DELETE FROM jadwal_pelajaran WHERE mata_pelajaran_id = 22");
$stmt_del->execute();
echo "\n✅ Berhasil dihapus " . $stmt_del->rowCount() . " jadwal dengan mapel_id=22\n";

// Verifikasi
echo "\n=== VERIFIKASI: Jadwal Pak Syaiful Jumat Kelas 2 ===\n";
$stmt = $siakad_db->prepare("
    SELECT j.id, j.mata_pelajaran_id, m.nama_mapel, j.jam_mulai, j.jam_selesai
    FROM jadwal_pelajaran j
    LEFT JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id
    WHERE j.hari = 'Jumat' AND j.kelas_id = 2 AND j.guru_id = 2
    ORDER BY j.jam_mulai
");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo "  ID: {$r['id']} | MapelID: {$r['mata_pelajaran_id']} | Mapel: {$r['nama_mapel']} | {$r['jam_mulai']}-{$r['jam_selesai']}\n";
}
