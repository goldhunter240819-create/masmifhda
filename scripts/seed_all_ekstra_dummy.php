<?php
/**
 * Seeder Lengkap Seluruh Ekstrakurikuler MAS MIFTAHUL HUDA
 * Menghasilkan cabang ekstra terstandar, guru pembina, anggota siswa, dan 10 pertemuan presensi per cabang
 */
require_once __DIR__ . '/../vendor/autoload.php';

$dbCore = \App\Core\Database::connect('core');
$dbSiakad = \App\Core\Database::connect('siakad');

echo "=== MEMULAI SEED SELURUH EKSTRAKURIKULER MAS MIFTAHUL HUDA ===\n";

$tahun_ajaran = '2026/2027';
$semester = 'Ganjil';

// Ambil semua siswa aktif dari berbagai kelas
$allSiswa = $dbCore->query("SELECT s.id, s.nama, s.nis, COALESCE(k_hist.nama_kelas, k.nama_kelas, 'X') as nama_kelas 
                            FROM siswa s 
                            LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                                AND rks.tahun_ajaran_id IN (SELECT id FROM tahun_ajaran WHERE name = '$tahun_ajaran')
                            LEFT JOIN kelas k ON s.kelas_id = k.id 
                            LEFT JOIN kelas k_hist ON rks.kelas_id = k_hist.id 
                            WHERE s.status = 'Aktif' 
                            ORDER BY nama_kelas ASC, s.nama ASC")->fetchAll(\PDO::FETCH_ASSOC);

$totalSiswa = count($allSiswa);
echo "Total siswa aktif tersedia: $totalSiswa siswa.\n";

// Pemetaan Guru Pembina
$guruMap = [
    'Pramuka'   => 37, // DIDI HARTONO, S.Pd.I
    'Voli'      => 40, // SAIPUDIN AZIZ, S.Pd
    'Futsal'    => 45, // ERWANSAH, S.Pd
    'TenisMeja' => 14, // M AZIZ SULAIMAN, S.Pd
    'Hadroh'    => 46, // ENDANG KURNIASIH, S.E.
    'PMR'       => 41, // INDAH YUNITASARI, S.Pd
    'Muhadoroh' => 39, // Drs. Hamim Hasyim
    'ROHIS'     => 7,  // SITI AISAH, S.Pd.I
];

// Definisi 7 Ekstrakurikuler Lainnya (selain ROHIS yang sudah ada, atau update agar konsisten)
$daftarEkstra = [
    [
        'nama' => 'Pramuka Penegak',
        'guru_key' => 'Pramuka',
        'hari' => 'Sabtu',
        'waktu' => '14.00 - 16.00 WIB',
        'target' => 'Membentuk karakter disiplin, kepemimpinan, kemandirian, kecakapan bertahan hidup (survival), dan kepedulian sosial melalui gerakan pramuka tingkat penegak.',
        'pertemuan' => [
            [1, '2026-08-01', '14:00', '16:00', 'Penerimaan Tamu Ambalan & Pengenalan Adat Ambalan Penegak'],
            [2, '2026-08-08', '14:00', '16:00', 'Latihan Keterampilan Baris Berbaris (LKBB) Tingkat Penegak'],
            [3, '2026-08-22', '14:00', '16:00', 'Pioneering dan Tali Temali: Pembuatan Menara Pandang & Tandu Darurat'],
            [4, '2026-08-29', '14:00', '16:00', 'Navigasi Darat: Membaca Peta Topografi dan Penggunaan Kompas Bidik'],
            [5, '2026-09-05', '14:00', '16:00', 'Survival Dasar & Pertolongan Pertama Gawat Darurat (PPGD) di Alam Terbuka'],
            [6, '2026-09-12', '14:00', '16:00', 'Semaphore, Sandi Morse, dan Sandi Pramuka Lanjutan'],
            [7, '2026-09-19', '14:00', '16:00', 'Simulasi Pendirian Tenda Cepat & Manajemen Camp Standar Penegak'],
            [8, '2026-10-03', '14:00', '16:00', 'Penjelajahan dan Tanda Jejak Alam Terbuka (Wide Game)'],
            [9, '2026-10-10', '14:00', '16:00', 'Api Unggun, Dinamika Kelompok, dan Malam Keakraban Ambalan'],
            [10, '2026-10-17', '14:00', '16:00', 'Uji Syarat Kecakapan Umum (SKU) Penegak Bantara & Pelantikan'],
        ]
    ],
    [
        'nama' => 'Bola Voli',
        'guru_key' => 'Voli',
        'hari' => 'Sabtu',
        'waktu' => '15.30 - 17.00 WIB',
        'target' => 'Meningkatkan kebugaran jasmani, penguasaan teknik dasar dan lanjutan bola voli, serta kekompakan strategi bertanding tim madrasah.',
        'pertemuan' => [
            [1, '2026-08-01', '15:30', '17:00', 'Pemanasan Fisik, Kelincahan, dan Pengenalan Formasi Bola Voli'],
            [2, '2026-08-08', '15:30', '17:00', 'Teknik Dasar Passing Bawah: Posisi Kaki dan Ayunan Lengan'],
            [3, '2026-08-22', '15:30', '17:00', 'Teknik Dasar Passing Atas: Ketepatan dan Kekuatan Jari-Jari'],
            [4, '2026-08-29', '15:30', '17:00', 'Latihan Servis Bawah dan Servis Atas (Floating Service)'],
            [5, '2026-09-05', '15:30', '17:00', 'Kombinasi Passing Bawah dan Atas Berpasangan & Bergerak'],
            [6, '2026-09-12', '15:30', '17:00', 'Teknik Dasar Smash (Spike): Langkah Awalan, Lompatan, dan Pukulan'],
            [7, '2026-09-19', '15:30', '17:00', 'Teknik Bertahan dan Membendung Bola (Block Tunggal & Ganda)'],
            [8, '2026-10-03', '15:30', '17:00', 'Pola Penyerangan dan Pengaturan Posisi Setter (Tosser)'],
            [9, '2026-10-10', '15:30', '17:00', 'Pola Pertahanan Tim (Receive Service & Defense Bola Rebound)'],
            [10, '2026-10-17', '15:30', '17:00', 'Simulasi Pertandingan Internal (Game 3 Set) & Evaluasi Tim'],
        ]
    ],
    [
        'nama' => 'Futsal',
        'guru_key' => 'Futsal',
        'hari' => 'Jumat',
        'waktu' => '15.30 - 17.00 WIB',
        'target' => 'Mengasah keterampilan teknik dasar futsal, kecepatan pengambilan keputusan, sportivitas, dan daya tahan fisik pemain.',
        'pertemuan' => [
            [1, '2026-08-07', '15:30', '17:00', 'Latihan Kondisi Fisik, Kelenturan, dan Dribbling Dasar'],
            [2, '2026-08-14', '15:30', '17:00', 'Teknik Passing Akurat dengan Kaki Bagian Dalam & Kontrol Telapak Kaki'],
            [3, '2026-08-21', '15:30', '17:00', 'Kombinasi Wall Pass (One-Two Pass) dan Pergerakan Tanpa Bola'],
            [4, '2026-08-28', '15:30', '17:00', 'Teknik Shooting Keras dan Penempatan Bola ke Sudut Gawang'],
            [5, '2026-09-04', '15:30', '17:00', 'Pola Transisi Bertahan ke Menyerang (Counter Attack Cepat)'],
            [6, '2026-09-11', '15:30', '17:00', 'Sistem Pertahanan Zona Marking dan Man-to-Man Marking Futsal'],
            [7, '2026-09-18', '15:30', '17:00', 'Teknik Khusus Penjaga Gawang (Goalkeeping) & Distribusi Bola'],
            [8, '2026-10-02', '15:30', '17:00', 'Variasi Eksekusi Bola Mati (Kick-in, Corner Kick, dan Free Kick)'],
            [9, '2026-10-09', '15:30', '17:00', 'Strategi Formasi Futsal 1-2-1 (Diamond) dan 2-2 (Square)'],
            [10, '2026-10-16', '15:30', '17:00', 'Mini Turnamen Antar Regu Futsal & Evaluasi Performa Pemain'],
        ]
    ],
    [
        'nama' => 'Tenis Meja',
        'guru_key' => 'TenisMeja',
        'hari' => 'Sabtu',
        'waktu' => '14.00 - 16.00 WIB',
        'target' => 'Melatih koordinasi mata dan tangan, kelincahan gerak refleks, serta penguasaan teknik putaran bola (spin) pada tenis meja.',
        'pertemuan' => [
            [1, '2026-08-01', '14:00', '16:00', 'Pengenalan Grip (Shakehand & Penhold) dan Footwork Dasar'],
            [2, '2026-08-08', '14:00', '16:00', 'Teknik Forehand Drive dan Backhand Drive Dasar'],
            [3, '2026-08-22', '14:00', '16:00', 'Teknik Forehand Push dan Backhand Push (Potong Bola)'],
            [4, '2026-08-29', '14:00', '16:00', 'Variasi Pukulan Servis (Spin, Backspin, Sidespin)'],
            [5, '2026-09-05', '14:00', '16:00', 'Pengembalian Servis Lawan (Receive Service) dan Kontrol Meja'],
            [6, '2026-09-12', '14:00', '16:00', 'Latihan Topspin Forehand Melawan Bola Kosong dan Bola Isi'],
            [7, '2026-09-19', '14:00', '16:00', 'Teknik Bertahan (Block & Chop Jarak Jauh)'],
            [8, '2026-10-03', '14:00', '16:00', 'Pola Serangan Tiga Pukulan (Third-ball Attack)'],
            [9, '2026-10-10', '14:00', '16:00', 'Strategi Bermain Ganda (Komunikasi Pasangan & Rotasi Posisi)'],
            [10, '2026-10-17', '14:00', '16:00', 'Pertandingan Sistem Set Tunggal & Ganda serta Evaluasi Teknik'],
        ]
    ],
    [
        'nama' => 'Hadroh / Seni Religi',
        'guru_key' => 'Hadroh',
        'hari' => 'Rabu',
        'waktu' => '15.00 - 16.30 WIB',
        'target' => 'Melestarikan seni budaya Islam, menumbuhkan kecintaan bershalawat kepada Rasulullah SAW, serta melatih harmonisasi musikalitas vokal dan instrumen hadroh.',
        'pertemuan' => [
            [1, '2026-08-05', '15:00', '16:30', 'Pengenalan Instrumen Hadroh (Terbang, Bass, Darbuka, Tam, Keprak)'],
            [2, '2026-08-12', '15:00', '16:30', 'Pukulan Dasar Terbang: Nada Anakan dan Nada Nikahan'],
            [3, '2026-08-19', '15:00', '16:30', 'Kombinasi Pukulan Lambat (Slow Beat) & Sinkronisasi Vokal Shalawat'],
            [4, '2026-08-26', '15:00', '16:30', 'Teknik Pukulan Variasi Cepat (Rampak) dan Irama Banjari'],
            [5, '2026-09-02', '15:00', '16:30', 'Latihan Vokal Vokalis Utama & Backing Vokal Shalawat Klasik'],
            [6, '2026-09-09', '15:00', '16:30', 'Kombinasi Pukulan Bass Mika dan Tam Tunggal'],
            [7, '2026-09-16', '15:00', '16:30', 'Penguasaan Ketukan Darbuka / Calti (Roling & Variasi Ketukan)'],
            [8, '2026-09-23', '15:00', '16:30', 'Latihan Lagu Shalawat Populer (Thibbil Qulub & Ya Thoybah)'],
            [9, '2026-10-07', '15:00', '16:30', 'Harmonisasi Musik & Penampilan Panggung (Stage Performance)'],
            [10, '2026-10-14', '15:00', '16:30', 'Gladi Bersih Penampilan Acara PHBI Madrasah & Evaluasi Tim'],
        ]
    ],
    [
        'nama' => 'PMR (Palang Merah Remaja)',
        'guru_key' => 'PMR',
        'hari' => 'Sabtu',
        'waktu' => '14.00 - 15.30 WIB',
        'target' => 'Membina remaja yang berjiwa kemanusiaan, tanggap bencana, terampil dalam pertolongan pertama, dan peduli terhadap kesehatan diri dan lingkungan.',
        'pertemuan' => [
            [1, '2026-08-01', '14:00', '15:30', 'Pengenalan Gerakan Palang Merah, 7 Prinsip Dasar & Tri Bakti PMR'],
            [2, '2026-08-08', '14:00', '15:30', 'Anatomi dan Fisiologi Dasar Tubuh Manusia'],
            [3, '2026-08-22', '14:00', '15:30', 'Penilaian Korban: Primer & Sekunder serta Pemeriksaan Tanda Vital'],
            [4, '2026-08-29', '14:00', '15:30', 'Pertolongan Pertama pada Luka Lecet, Luka Sayat, dan Pendarahan'],
            [5, '2026-09-05', '14:00', '15:30', 'Teknik Pembalutan Mitela & Pembidaian Fraktur Tulang'],
            [6, '2026-09-12', '14:00', '15:30', 'Pertolongan pada Korban Pingsan, Syok, dan Asma'],
            [7, '2026-09-19', '14:00', '15:30', 'Resusitasi Jantung Paru (RJP / CPR) dan Bantuan Hidup Dasar'],
            [8, '2026-10-03', '14:00', '15:30', 'Teknik Evakuasi Korban (Tandu Darurat, Angkatan 1 & 2 Penolong)'],
            [9, '2026-10-10', '14:00', '15:30', 'Kesiapsiagaan Bencana Sekolah & Sanitasi Kesehatan Remaja'],
            [10, '2026-10-17', '14:00', '15:30', 'Simulasi Tanggap Darurat Bencana Gempa / Kebakaran Madrasah'],
        ]
    ],
    [
        'nama' => 'Muhadoroh / Da\'wah',
        'guru_key' => 'Muhadoroh',
        'hari' => 'Kamis',
        'waktu' => '13.30 - 15.00 WIB',
        'target' => 'Melatih keberanian berbicara di depan umum (public speaking), kemampuan retorika dakwah, penguasaan materi keislaman, dan adab menjadi dai muda.',
        'pertemuan' => [
            [1, '2026-08-06', '13:30', '15:00', 'Pengenalan Seni Berbicara di Depan Umum (Public Speaking Islami)'],
            [2, '2026-08-13', '13:30', '15:00', 'Menyusun Naskah Pidato Bahasa Indonesia: Pembuka, Isi, dan Penutup'],
            [3, '2026-08-20', '13:30', '15:00', 'Latihan Vokal: Artikulasi, Intonasi, dan Penekanan Kata Kunci'],
            [4, '2026-08-27', '13:30', '15:00', 'Bahasa Tubuh, Kontak Mata, dan Pengendalian Rasa Gugup'],
            [5, '2026-09-03', '13:30', '15:00', 'Praktek Pidato Bahasa Arab Sederhana dan Ucapan Salam Fasih'],
            [6, '2026-09-10', '13:30', '15:00', 'Praktek Pidato Bahasa Inggris (Islamic Speech Training)'],
            [7, '2026-09-17', '13:30', '15:00', 'Teknik Menjadi Pembawa Acara (MC) Formal & Semi-Formal Keagamaan'],
            [8, '2026-10-01', '13:30', '15:00', 'Latihan Qori / Tilawatil Qur\'an Pembuka Acara'],
            [9, '2026-10-08', '13:30', '15:00', 'Latihan Khutbah Jum\'at Singkat & Adab Khatib'],
            [10, '2026-10-15', '13:30', '15:00', 'Lomba Pidato Internal Antar Siswa (Muhadoroh Akbar) & Penilaian'],
        ]
    ]
];

$stmtCekProg = $dbSiakad->prepare("SELECT id FROM ekstra_program WHERE nama_ekstra = ?");
$stmtInsProg = $dbSiakad->prepare("INSERT INTO ekstra_program 
    (nama_ekstra, guru_id, tahun_ajaran, semester, jadwal_hari, jadwal_waktu, target_pencapaian, status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, 'Aktif')");
$stmtUpdProg = $dbSiakad->prepare("UPDATE ekstra_program 
    SET guru_id = ?, tahun_ajaran = ?, semester = ?, jadwal_hari = ?, jadwal_waktu = ?, target_pencapaian = ?, status = 'Aktif' 
    WHERE id = ?");

$stmtCekJurnal = $dbSiakad->prepare("SELECT id FROM ekstra_jurnal WHERE nama_ekstra = ? AND tanggal = ?");
$stmtInsJurnal = $dbSiakad->prepare("INSERT INTO ekstra_jurnal 
    (nama_ekstra, tahun_ajaran, semester, tanggal, materi, guru_id, pertemuan_ke, jam_mulai, jam_selesai) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmtInsAbsen = $dbSiakad->prepare("INSERT INTO ekstra_absen (jurnal_id, siswa_id, status, status_kehadiran, catatan) VALUES (?, ?, ?, ?, ?)");
$stmtInsAnggota = $dbSiakad->prepare("INSERT INTO ekstra_anggota (nama_ekstra, siswa_id, tahun_ajaran, status) VALUES (?, ?, ?, 'Aktif')");

$grandTotalAbsen = 0;
$offset = 0;

foreach ($daftarEkstra as $idx => $ek) {
    $nama = $ek['nama'];
    $guru_id = $guruMap[$ek['guru_key']] ?? 1;

    // 1. Simpan / Update Program
    $stmtCekProg->execute([$nama]);
    $progId = $stmtCekProg->fetchColumn();
    if ($progId) {
        $stmtUpdProg->execute([$guru_id, $tahun_ajaran, $semester, $ek['hari'], $ek['waktu'], $ek['target'], $progId]);
        echo "\n[{$nama}] Update program ID: $progId\n";
    } else {
        $stmtInsProg->execute([$nama, $guru_id, $tahun_ajaran, $semester, $ek['hari'], $ek['waktu'], $ek['target']]);
        $progId = $dbSiakad->lastInsertId();
        echo "\n[{$nama}] Buat program baru ID: $progId\n";
    }

    // 2. Tentukan Anggota Siswa (pilih 20-25 siswa secara terdistribusi)
    $sliceCount = 22;
    $anggotaSiswa = [];
    for ($i = 0; $i < $sliceCount; $i++) {
        $pos = ($offset + $i * 3) % $totalSiswa;
        $anggotaSiswa[] = $allSiswa[$pos]['id'];
    }
    $offset = ($offset + 7) % $totalSiswa;

    foreach ($anggotaSiswa as $sid) {
        $stmtCekA = $dbSiakad->prepare("SELECT id FROM ekstra_anggota WHERE nama_ekstra = ? AND siswa_id = ?");
        $stmtCekA->execute([$nama, $sid]);
        if (!$stmtCekA->fetch()) {
            $stmtInsAnggota->execute([$nama, $sid, $tahun_ajaran]);
        }
    }
    echo "  -> Terdaftar " . count($anggotaSiswa) . " anggota siswa.\n";

    // 3. Masukkan 10 Pertemuan & Presensi
    foreach ($ek['pertemuan'] as $p) {
        list($ptm, $tgl, $jMulai, $jSelesai, $mat) = $p;
        $stmtCekJurnal->execute([$nama, $tgl]);
        $jid = $stmtCekJurnal->fetchColumn();
        if (!$jid) {
            $stmtInsJurnal->execute([$nama, $tahun_ajaran, $semester, $tgl, $mat, $guru_id, $ptm, $jMulai, $jSelesai]);
            $jid = $dbSiakad->lastInsertId();
        } else {
            $dbSiakad->exec("DELETE FROM ekstra_absen WHERE jurnal_id = " . intval($jid));
        }

        foreach ($anggotaSiswa as $sid) {
            $seed = ($sid * 19 + $ptm * 23 + $idx * 11) % 100;
            $st = 'H'; $stText = 'Hadir'; $cat = '';
            if ($seed >= 95) {
                $st = 'A'; $stText = 'Alpa'; $cat = 'Tanpa Keterangan';
            } elseif ($seed >= 90) {
                $st = 'I'; $stText = 'Izin'; $cat = 'Izin Lomba / Acara Keluarga';
            } elseif ($seed >= 85) {
                $st = 'S'; $stText = 'Sakit'; $cat = 'Sakit';
            }

            $stmtInsAbsen->execute([$jid, $sid, $st, $stText, $cat]);
            $grandTotalAbsen++;
        }
    }
    echo "  -> 10 pertemuan dan presensi berhasil digenerate.\n";
}

echo "\n=== SEED SELESAI SEMPURNA! ===\n";
echo "Total Presensi Dihasilkan: $grandTotalAbsen record dari seluruh cabang ekstrakurikuler!\n";
