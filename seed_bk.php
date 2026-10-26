<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=aula_db;charset=utf8', 'root', '');

$kategori = [
    // Pelanggaran - Ringan
    ['nama' => 'Terlambat datang ke sekolah', 'tipe' => 'pelanggaran', 'tingkat' => 'ringan', 'poin' => 10],
    ['nama' => 'Tidak memakai atribut seragam lengkap (dasi/topi/badge)', 'tipe' => 'pelanggaran', 'tingkat' => 'ringan', 'poin' => 10],
    ['nama' => 'Membuang sampah sembarangan', 'tipe' => 'pelanggaran', 'tingkat' => 'ringan', 'poin' => 10],
    ['nama' => 'Makan/minum di dalam kelas saat jam pelajaran', 'tipe' => 'pelanggaran', 'tingkat' => 'ringan', 'poin' => 15],
    ['nama' => 'Tidak mengerjakan PR atau Tugas', 'tipe' => 'pelanggaran', 'tingkat' => 'ringan', 'poin' => 15],
    
    // Pelanggaran - Sedang
    ['nama' => 'Membolos atau keluar area sekolah tanpa izin', 'tipe' => 'pelanggaran', 'tingkat' => 'sedang', 'poin' => 30],
    ['nama' => 'Membawa HP ke sekolah tanpa izin', 'tipe' => 'pelanggaran', 'tingkat' => 'sedang', 'poin' => 30],
    ['nama' => 'Memakai riasan/make-up berlebihan atau rambut gondrong (putra)', 'tipe' => 'pelanggaran', 'tingkat' => 'sedang', 'poin' => 30],
    ['nama' => 'Mengganggu teman atau membuat keributan di kelas', 'tipe' => 'pelanggaran', 'tingkat' => 'sedang', 'poin' => 40],
    
    // Pelanggaran - Berat
    ['nama' => 'Berkelahi di lingkungan sekolah', 'tipe' => 'pelanggaran', 'tingkat' => 'berat', 'poin' => 75],
    ['nama' => 'Merokok di dalam atau di sekitar sekolah', 'tipe' => 'pelanggaran', 'tingkat' => 'berat', 'poin' => 75],
    ['nama' => 'Mencuri barang milik sekolah atau teman', 'tipe' => 'pelanggaran', 'tingkat' => 'berat', 'poin' => 100],
    ['nama' => 'Membawa senjata tajam atau barang berbahaya', 'tipe' => 'pelanggaran', 'tingkat' => 'berat', 'poin' => 100],
    ['nama' => 'Melakukan tindakan perundungan (bullying)', 'tipe' => 'pelanggaran', 'tingkat' => 'berat', 'poin' => 100],
    
    // Prestasi
    ['nama' => 'Pengurus OSIS atau Ekstrakurikuler (Aktif)', 'tipe' => 'prestasi', 'tingkat' => null, 'poin' => 20],
    ['nama' => 'Juara 1 s/d 3 Lomba Tingkat Kabupaten', 'tipe' => 'prestasi', 'tingkat' => null, 'poin' => 50],
    ['nama' => 'Juara 1 s/d 3 Lomba Tingkat Provinsi', 'tipe' => 'prestasi', 'tingkat' => null, 'poin' => 75],
    ['nama' => 'Juara 1 s/d 3 Lomba Tingkat Nasional', 'tipe' => 'prestasi', 'tingkat' => null, 'poin' => 100],
    ['nama' => 'Hafal Al-Quran minimal 1 Juz', 'tipe' => 'prestasi', 'tingkat' => null, 'poin' => 50]
];

// Kosongkan tabel dulu supaya bersih dari data tes lama
$pdo->exec("TRUNCATE TABLE bk_kategori");

$stmt = $pdo->prepare("INSERT INTO bk_kategori (nama_kategori, tipe, tingkat, poin) VALUES (?, ?, ?, ?)");
foreach ($kategori as $k) {
    $stmt->execute([$k['nama'], $k['tipe'], $k['tingkat'], $k['poin']]);
}

echo "Data berhasil dimasukkan!\n";
