<?php
require_once __DIR__ . '/../vendor/autoload.php';

$dbCore = \App\Core\Database::connect('core');
$dbSiakad = \App\Core\Database::connect('siakad');

echo "=== MEMULAI SEED DATA DUMMY EKSTRAKURIKULER ROHIS ===\n";

$tahun_ajaran = '2026/2027';
$semester = 'Ganjil';
$nama_ekstra = 'ROHIS (Kerohanian Islam)';

// 1. Pastikan cabang ROHIS ada di ekstra_program
$stmtCek = $dbSiakad->prepare("SELECT id FROM ekstra_program WHERE nama_ekstra = ? AND tahun_ajaran = ?");
$stmtCek->execute([$nama_ekstra, $tahun_ajaran]);
$progId = $stmtCek->fetchColumn();

// Ambil pembina (SITI AISAH, S.Pd.I atau guru lain)
$guru_id = 7;
$cekGuru = $dbCore->query("SELECT id FROM guru WHERE id = 7")->fetchColumn();
if (!$cekGuru) {
    $guru_id = $dbCore->query("SELECT id FROM guru LIMIT 1")->fetchColumn() ?: 1;
}

if (!$progId) {
    $stmtIns = $dbSiakad->prepare("INSERT INTO ekstra_program 
        (nama_ekstra, guru_id, tahun_ajaran, semester, jadwal_hari, jadwal_waktu, target_pencapaian, status) 
        VALUES (?, ?, ?, ?, 'Jumat', '13.30 - 15.00 WIB', ?, 'Aktif')");
    $stmtIns->execute([
        $nama_ekstra,
        $guru_id,
        $tahun_ajaran,
        $semester,
        'Membentuk generasi muslim yang berakhlakul karimah, berwawasan keislaman luas, terampil berdakwah, serta aktif dalam kegiatan keagamaan madrasah.'
    ]);
    $progId = $dbSiakad->lastInsertId();
    echo "Cabang Ekstra '$nama_ekstra' berhasil dibuat (ID: $progId).\n";
} else {
    echo "Cabang Ekstra '$nama_ekstra' sudah ada (ID: $progId).\n";
}

// Sinkronkan juga tugas pembina di guru_tugas
try {
    $stmtGT = $dbCore->prepare("SELECT id FROM guru_tugas WHERE guru_id = ? AND tugas = 'Pembina Ekstrakurikuler' AND keterangan = ? AND tahun_ajaran = ?");
    $stmtGT->execute([$guru_id, $nama_ekstra, $tahun_ajaran]);
    if (!$stmtGT->fetch()) {
        $stmtInsGT = $dbCore->prepare("INSERT INTO guru_tugas (guru_id, tugas, keterangan, tahun_ajaran) VALUES (?, 'Pembina Ekstrakurikuler', ?, ?)");
        $stmtInsGT->execute([$guru_id, $nama_ekstra, $tahun_ajaran]);
    }
} catch (\Throwable $e) {}

// 2. Tambahkan Anggota Siswa (Ambil siswa aktif dari Kelas X, XI, XII sekitar 25-30 siswa)
$sqlSiswa = "SELECT s.id, s.nama, s.nis, COALESCE(k_hist.nama_kelas, k.nama_kelas, 'X') as nama_kelas 
             FROM siswa s 
             LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                 AND rks.tahun_ajaran_id IN (SELECT id FROM tahun_ajaran WHERE name = '$tahun_ajaran')
             LEFT JOIN kelas k ON s.kelas_id = k.id 
             LEFT JOIN kelas k_hist ON rks.kelas_id = k_hist.id 
             WHERE s.status = 'Aktif' 
             ORDER BY nama_kelas ASC, s.nama ASC 
             LIMIT 30";
$siswaList = $dbCore->query($sqlSiswa)->fetchAll(\PDO::FETCH_ASSOC);

echo "Menambahkan " . count($siswaList) . " siswa sebagai anggota ROHIS...\n";
$stmtInsAnggota = $dbSiakad->prepare("INSERT INTO ekstra_anggota (nama_ekstra, siswa_id, tahun_ajaran, status) VALUES (?, ?, ?, 'Aktif')");
$totAnggotaBaru = 0;
foreach ($siswaList as $s) {
    $stmtCekA = $dbSiakad->prepare("SELECT id FROM ekstra_anggota WHERE nama_ekstra = ? AND siswa_id = ? AND tahun_ajaran = ?");
    $stmtCekA->execute([$nama_ekstra, $s['id'], $tahun_ajaran]);
    if (!$stmtCekA->fetch()) {
        $stmtInsAnggota->execute([$nama_ekstra, $s['id'], $tahun_ajaran]);
        $totAnggotaBaru++;
    }
}
echo "Total anggota baru yang ditambahkan: $totAnggotaBaru.\n";

// Ambil semua anggota terdaftar saat ini
$anggotaIds = $dbSiakad->query("SELECT siswa_id FROM ekstra_anggota WHERE nama_ekstra = ".$dbSiakad->quote($nama_ekstra)." AND tahun_ajaran = ".$dbSiakad->quote($tahun_ajaran))->fetchAll(\PDO::FETCH_COLUMN);
echo "Total anggota aktif ROHIS sekarang: " . count($anggotaIds) . " siswa.\n";

// 3. Buat 10 Pertemuan Jurnal & Presensi
$daftarPertemuan = [
    [
        'pertemuan_ke' => 1,
        'tanggal' => '2026-08-07',
        'jam_mulai' => '13:30',
        'jam_selesai' => '15:00',
        'materi' => 'Orientasi dan Pengenalan Program Kerja ROHIS Tahun Pelajaran 2026/2027'
    ],
    [
        'pertemuan_ke' => 2,
        'tanggal' => '2026-08-14',
        'jam_mulai' => '13:30',
        'jam_selesai' => '15:00',
        'materi' => 'Tahsin Al-Qur\'an: Makharijul Huruf dan Sifatul Huruf (Surat Al-Fatihah)'
    ],
    [
        'pertemuan_ke' => 3,
        'tanggal' => '2026-08-21',
        'jam_mulai' => '13:30',
        'jam_selesai' => '15:00',
        'materi' => 'Kajian Aqidah & Akhlak: Karakter Pemuda Muslim Tangguh di Era Digital'
    ],
    [
        'pertemuan_ke' => 4,
        'tanggal' => '2026-08-28',
        'jam_mulai' => '13:30',
        'jam_selesai' => '15:00',
        'materi' => 'Fiqih Praktis: Tata Cara Wudhu Sempurna & Sholat Khusyuk Sesuai Sunnah'
    ],
    [
        'pertemuan_ke' => 5,
        'tanggal' => '2026-09-04',
        'jam_mulai' => '13:30',
        'jam_selesai' => '15:00',
        'materi' => 'Pelatihan Da\'i Muda: Teknik Public Speaking dan Menyusun Naskah Kultum'
    ],
    [
        'pertemuan_ke' => 6,
        'tanggal' => '2026-09-11',
        'jam_mulai' => '13:30',
        'jam_selesai' => '15:00',
        'materi' => 'Kajian Sirah Nabawiyah: Keteladanan Sahabat Remaja di Masa Perjuangan Dakwah'
    ],
    [
        'pertemuan_ke' => 7,
        'tanggal' => '2026-09-18',
        'jam_mulai' => '13:30',
        'jam_selesai' => '15:00',
        'materi' => 'Hafalan dan Tadabbur Makna Hadits Pilihan Arba\'in An-Nawawiyah'
    ],
    [
        'pertemuan_ke' => 8,
        'tanggal' => '2026-10-02',
        'jam_mulai' => '13:30',
        'jam_selesai' => '15:00',
        'materi' => 'Manajemen Organisasi Dakwah Sekolah & Pembentukan Jiwa Kepemimpinan Islami'
    ],
    [
        'pertemuan_ke' => 9,
        'tanggal' => '2026-10-09',
        'jam_mulai' => '13:30',
        'jam_selesai' => '15:00',
        'materi' => 'Aksi Sosial ROHIS: Kebersihan Masjid Madrasah & Santunan Kasih Peduli Lingkungan'
    ],
    [
        'pertemuan_ke' => 10,
        'tanggal' => '2026-10-16',
        'jam_mulai' => '13:30',
        'jam_selesai' => '15:00',
        'materi' => 'Muhasabah Triwulan, Evaluasi Keaktifan Anggota, dan Rencana Kegiatan Mendatang'
    ],
];

$stmtCekJurnal = $dbSiakad->prepare("SELECT id FROM ekstra_jurnal WHERE nama_ekstra = ? AND tanggal = ?");
$stmtInsJurnal = $dbSiakad->prepare("INSERT INTO ekstra_jurnal 
    (nama_ekstra, tahun_ajaran, semester, tanggal, materi, guru_id, pertemuan_ke, jam_mulai, jam_selesai) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmtInsAbsen = $dbSiakad->prepare("INSERT INTO ekstra_absen (jurnal_id, siswa_id, status, status_kehadiran, catatan) VALUES (?, ?, ?, ?, ?)");

$totalAbsenGenerated = 0;

foreach ($daftarPertemuan as $dp) {
    $stmtCekJurnal->execute([$nama_ekstra, $dp['tanggal']]);
    $jurnalId = $stmtCekJurnal->fetchColumn();

    if (!$jurnalId) {
        $stmtInsJurnal->execute([
            $nama_ekstra,
            $tahun_ajaran,
            $semester,
            $dp['tanggal'],
            $dp['materi'],
            $guru_id,
            $dp['pertemuan_ke'],
            $dp['jam_mulai'],
            $dp['jam_selesai']
        ]);
        $jurnalId = $dbSiakad->lastInsertId();
        echo "Jurnal Pertemuan Ke-{$dp['pertemuan_ke']} ({$dp['tanggal']}) dibuat (ID: $jurnalId).\n";
    } else {
        echo "Jurnal Pertemuan Ke-{$dp['pertemuan_ke']} ({$dp['tanggal']}) sudah ada (ID: $jurnalId).\n";
        // Bersihkan absen lama pertemuan ini agar terisi fresh
        $dbSiakad->exec("DELETE FROM ekstra_absen WHERE jurnal_id = " . intval($jurnalId));
    }

    // Generate kehadiran untuk tiap siswa anggota
    foreach ($anggotaIds as $sid) {
        // Tentukan variasi kehadiran yang realistis:
        // Sebagian besar Hadir, sesekali Sakit / Izin / Alpa
        $seed = ($sid * 17 + $dp['pertemuan_ke'] * 31) % 100;
        
        $st = 'H';
        $stText = 'Hadir';
        $catatan = '';

        if ($seed >= 95) {
            $st = 'A';
            $stText = 'Alpa';
            $catatan = 'Tanpa Keterangan';
        } elseif ($seed >= 90) {
            $st = 'I';
            $stText = 'Izin';
            $catatan = 'Izin ada keperluan keluarga';
        } elseif ($seed >= 85) {
            $st = 'S';
            $stText = 'Sakit';
            $catatan = 'Sakit demam / flu';
        } else {
            $st = 'H';
            $stText = 'Hadir';
        }

        $stmtInsAbsen->execute([$jurnalId, $sid, $st, $stText, $catatan]);
        $totalAbsenGenerated++;
    }
}

echo "=== SEED DATA SELESAI! ===\n";
echo "Total Rekord Absensi Dihasilkan: $totalAbsenGenerated kehadiran siswa pada 10 pertemuan ROHIS!\n";
