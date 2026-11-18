<?php

namespace App\Controllers;

use App\Core\Database;
use PDO;

class CbtController {

    /**
     * Pastikan skema tabel modul CBT Online tersedia di database core.
     * Bersihkan data dummy lama agar 100% menggunakan data real.
     */
    public static function ensureTables($db) {
        try {
            // 1. Tabel Paket Ujian
            $db->exec("CREATE TABLE IF NOT EXISTS cbt_ujian (
                id INT AUTO_INCREMENT PRIMARY KEY,
                kode_ujian VARCHAR(50) NOT NULL,
                nama_ujian VARCHAR(255) NOT NULL,
                mapel_nama VARCHAR(150) NOT NULL,
                kelas_nama VARCHAR(100) DEFAULT NULL,
                durasi_menit INT DEFAULT 90,
                tgl_mulai DATETIME DEFAULT NULL,
                tgl_selesai DATETIME DEFAULT NULL,
                kkm INT DEFAULT 75,
                acak_soal TINYINT DEFAULT 1,
                acak_opsi TINYINT DEFAULT 1,
                status VARCHAR(50) DEFAULT 'Draft',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // 2. Tabel Butir Soal Ujian
            $db->exec("CREATE TABLE IF NOT EXISTS cbt_soal (
                id INT AUTO_INCREMENT PRIMARY KEY,
                ujian_id INT NOT NULL,
                nomor_urut INT DEFAULT 1,
                pertanyaan TEXT NOT NULL,
                opsi_a TEXT NOT NULL,
                opsi_b TEXT NOT NULL,
                opsi_c TEXT DEFAULT NULL,
                opsi_d TEXT DEFAULT NULL,
                opsi_e TEXT DEFAULT NULL,
                kunci_jawaban TEXT DEFAULT NULL,
                bobot DECIMAL(5,2) DEFAULT 1.00,
                tipe_soal VARCHAR(50) DEFAULT 'sec_pg',
                konten_tambahan LONGTEXT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (ujian_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            try { $db->exec("ALTER TABLE cbt_soal ADD COLUMN tipe_soal VARCHAR(50) DEFAULT 'sec_pg' AFTER bobot"); } catch (\Exception $e) {}
            try { $db->exec("ALTER TABLE cbt_soal ADD COLUMN konten_tambahan LONGTEXT DEFAULT NULL AFTER tipe_soal"); } catch (\Exception $e) {}
            try { $db->exec("ALTER TABLE cbt_soal MODIFY COLUMN kunci_jawaban TEXT DEFAULT NULL"); } catch (\Exception $e) {}
            try { $db->exec("ALTER TABLE cbt_soal MODIFY COLUMN opsi_a TEXT DEFAULT NULL"); } catch (\Exception $e) {}
            try { $db->exec("ALTER TABLE cbt_soal MODIFY COLUMN opsi_b TEXT DEFAULT NULL"); } catch (\Exception $e) {}

            // 3. Tabel Token Ujian
            $db->exec("CREATE TABLE IF NOT EXISTS cbt_token (
                id INT AUTO_INCREMENT PRIMARY KEY,
                token VARCHAR(20) NOT NULL,
                ujian_id INT DEFAULT NULL,
                masa_berlaku_menit INT DEFAULT 120,
                status VARCHAR(20) DEFAULT 'Aktif',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // 4. Tabel Peserta & Riwayat Pengerjaan Siswa Real
            $db->exec("CREATE TABLE IF NOT EXISTS cbt_peserta (
                id INT AUTO_INCREMENT PRIMARY KEY,
                ujian_id INT NOT NULL,
                siswa_id INT DEFAULT NULL,
                nis VARCHAR(50) DEFAULT NULL,
                nama_siswa VARCHAR(150) NOT NULL,
                kelas_nama VARCHAR(100) DEFAULT NULL,
                waktu_mulai DATETIME DEFAULT NULL,
                waktu_selesai DATETIME DEFAULT NULL,
                status_pengerjaan VARCHAR(50) DEFAULT 'Belum Mengerjakan',
                jumlah_benar INT DEFAULT 0,
                jumlah_salah INT DEFAULT 0,
                nilai_akhir DECIMAL(5,2) DEFAULT 0.00,
                jawaban_json LONGTEXT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (ujian_id),
                INDEX (siswa_id),
                INDEX (nis)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            try { $db->exec("ALTER TABLE cbt_peserta ADD COLUMN reset_count INT DEFAULT 0 AFTER jawaban_json"); } catch (\Exception $e) {}
            try { $db->exec("ALTER TABLE cbt_peserta ADD COLUMN violation_count INT DEFAULT 0 AFTER reset_count"); } catch (\Exception $e) {}

            // 5. Tabel Custom Jam / Slot Waktu Ujian CBT
            $db->exec("CREATE TABLE IF NOT EXISTS cbt_jam_slot (
                id INT AUTO_INCREMENT PRIMARY KEY,
                urutan INT DEFAULT 1,
                label_sesi VARCHAR(50) DEFAULT 'Sesi 1',
                jam_mulai VARCHAR(10) NOT NULL,
                jam_selesai VARCHAR(10) NOT NULL,
                is_istirahat TINYINT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // 6. Tabel Peserta Khusus / Kelas Jauh (Terisolasi 100% dari SIAKAD & Keuangan)
            $db->exec("CREATE TABLE IF NOT EXISTS cbt_peserta_khusus (
                id INT AUTO_INCREMENT PRIMARY KEY,
                no_peserta VARCHAR(50) NOT NULL UNIQUE,
                nis VARCHAR(50) DEFAULT NULL,
                nama_siswa VARCHAR(150) NOT NULL,
                tingkat VARCHAR(20) NOT NULL DEFAULT 'Kelas X',
                kelas_nama VARCHAR(100) NOT NULL DEFAULT 'X - Kelas Jauh',
                username VARCHAR(50) NOT NULL UNIQUE,
                password_plain VARCHAR(100) DEFAULT '123456',
                password_hash VARCHAR(255) DEFAULT NULL,
                lokasi_cabang VARCHAR(100) DEFAULT 'Kelas Jauh',
                status VARCHAR(20) DEFAULT 'Aktif',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (no_peserta),
                INDEX (username),
                INDEX (tingkat),
                INDEX (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // Seed default slots jika masih kosong
            $cntSlot = (int)$db->query("SELECT COUNT(*) FROM cbt_jam_slot")->fetchColumn();
            if ($cntSlot === 0) {
                $db->exec("INSERT INTO cbt_jam_slot (urutan, label_sesi, jam_mulai, jam_selesai, is_istirahat) VALUES
                    (1, 'Sesi 1', '07:30', '08:45', 0),
                    (2, 'Sesi 2', '09:00', '10:15', 0),
                    (3, 'Istirahat', '10:15', '10:45', 1),
                    (4, 'Sesi 3', '10:45', '12:00', 0),
                    (5, 'Sesi 4', '12:30', '13:45', 0)
                ");
            }

            // 6. Tabel Matriks Jadwal Ujian (Grid per Hari, Slot, Kelas)
            $db->exec("CREATE TABLE IF NOT EXISTS cbt_jadwal_grid (
                id INT AUTO_INCREMENT PRIMARY KEY,
                topik_id INT DEFAULT NULL,
                ujian_id INT NOT NULL,
                hari VARCHAR(50) NOT NULL,
                jam_slot VARCHAR(50) NOT NULL,
                kelas_id INT DEFAULT NULL,
                kelas_nama VARCHAR(100) NOT NULL,
                pengawas VARCHAR(150) DEFAULT NULL,
                ruang VARCHAR(100) DEFAULT NULL,
                tanggal DATE DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (topik_id),
                INDEX (ujian_id),
                INDEX (hari),
                INDEX (kelas_nama)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            try {
                $db->exec("ALTER TABLE cbt_jadwal_grid ADD COLUMN topik_id INT DEFAULT NULL AFTER id");
            } catch (\Exception $e) {}

            try {
                $db->exec("ALTER TABLE cbt_jadwal_grid MODIFY COLUMN ujian_id INT DEFAULT NULL");
            } catch (\Exception $e) {}

            try {
                $db->exec("ALTER TABLE cbt_jadwal_grid ADD COLUMN mapel_nama VARCHAR(150) DEFAULT NULL AFTER ujian_id");
            } catch (\Exception $e) {}

            try {
                $db->exec("ALTER TABLE cbt_ujian ADD COLUMN topik_id INT DEFAULT NULL AFTER id");
            } catch (\Exception $e) {}

            try {
                $db->exec("UPDATE bs_topik SET status = 'Arsip' WHERE jenis IN ('PAS', 'PTS') AND (tgl_mulai IS NULL OR tgl_mulai = '')");
            } catch (\Exception $e) {}

            // 7. Tabel Topik Ujian (bs_topik)
            $db->exec("CREATE TABLE IF NOT EXISTS bs_topik (
                id INT AUTO_INCREMENT PRIMARY KEY,
                jenis VARCHAR(50) NOT NULL,
                nama_topik VARCHAR(255) NOT NULL,
                keterangan TEXT DEFAULT NULL,
                tahun_ajaran VARCHAR(50) DEFAULT '2026/2027',
                semester VARCHAR(20) DEFAULT 'Ganjil',
                tgl_mulai DATE DEFAULT NULL,
                tgl_selesai DATE DEFAULT NULL,
                status VARCHAR(30) DEFAULT 'Aktif',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            try { $db->exec("ALTER TABLE bs_topik ADD COLUMN semester VARCHAR(20) DEFAULT 'Ganjil' AFTER tahun_ajaran"); } catch (\Exception $e) {}
            try { $db->exec("ALTER TABLE bs_topik ADD COLUMN tgl_mulai DATE DEFAULT NULL AFTER semester"); } catch (\Exception $e) {}
            try { $db->exec("ALTER TABLE bs_topik ADD COLUMN tgl_selesai DATE DEFAULT NULL AFTER tgl_mulai"); } catch (\Exception $e) {}
            try { $db->exec("ALTER TABLE bs_topik ADD COLUMN status VARCHAR(30) DEFAULT 'Aktif' AFTER tgl_selesai"); } catch (\Exception $e) {}

            // Sinkronkan juga tabel bs_topik di database siakad jika ada
            try {
                $dbS = Database::connect('siakad');
                if ($dbS) {
                    $dbS->exec("CREATE TABLE IF NOT EXISTS bs_topik (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        jenis VARCHAR(50) NOT NULL,
                        nama_topik VARCHAR(255) NOT NULL,
                        keterangan TEXT DEFAULT NULL,
                        tahun_ajaran VARCHAR(50) DEFAULT '2026/2027',
                        semester VARCHAR(20) DEFAULT 'Ganjil',
                        tgl_mulai DATE DEFAULT NULL,
                        tgl_selesai DATE DEFAULT NULL,
                        status VARCHAR(30) DEFAULT 'Aktif',
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
                    try { $dbS->exec("ALTER TABLE bs_topik ADD COLUMN semester VARCHAR(20) DEFAULT 'Ganjil' AFTER tahun_ajaran"); } catch (\Exception $e) {}
                    try { $dbS->exec("ALTER TABLE bs_topik ADD COLUMN tgl_mulai DATE DEFAULT NULL AFTER semester"); } catch (\Exception $e) {}
                    try { $dbS->exec("ALTER TABLE bs_topik ADD COLUMN tgl_selesai DATE DEFAULT NULL AFTER tgl_mulai"); } catch (\Exception $e) {}
                    try { $dbS->exec("ALTER TABLE bs_topik ADD COLUMN status VARCHAR(30) DEFAULT 'Aktif' AFTER tgl_selesai"); } catch (\Exception $e) {}
                }
            } catch (\Exception $e) {}

            try {
                $cntSas = (int)$db->query("SELECT COUNT(*) FROM bs_topik WHERE jenis = 'SAS'")->fetchColumn();
                if ($cntSas === 0) {
                    $insertSql = "INSERT INTO bs_topik (jenis, nama_topik, keterangan, tahun_ajaran, semester, tgl_mulai, tgl_selesai, status) VALUES
                        ('SAS', 'Sumatif Akhir Semester (SAS) Ganjil 2026/2027', 'Asesmen Sumatif Akhir Semester Ganjil TA 2026/2027 seluruh tingkat kelas.', '2026/2027', 'Ganjil', '2026-12-01', '2026-12-08', 'Aktif'),
                        ('STS', 'Sumatif Tengah Semester (STS) Ganjil 2026/2027', 'Asesmen Sumatif Tengah Semester Ganjil TA 2026/2027.', '2026/2027', 'Ganjil', '2026-09-21', '2026-09-26', 'Selesai'),
                        ('UM', 'Ujian Madrasah (UM) 2026/2027', 'Ujian Akhir Madrasah Aliyah Miftahul Huda Mathla\'ul Anwar Tahun Pelajaran 2026/2027.', '2026/2027', 'Genap', '2027-03-15', '2027-03-22', 'Terjadwal'),
                        ('Try Out', 'Try Out Ujian Madrasah 2026/2027', 'Simulasi CBT dan pemantapan materi persiapan Ujian Madrasah.', '2026/2027', 'Genap', '2027-02-15', '2027-02-18', 'Terjadwal')
                    ";
                    $db->exec($insertSql);
                    if (isset($dbS) && $dbS) {
                        try { $dbS->exec($insertSql); } catch (\Exception $e) {}
                    }
                }
            } catch (\Exception $e) {}

            // 8. Tabel Pengaturan CBT & Keamanan
            $db->exec("CREATE TABLE IF NOT EXISTS cbt_pengaturan (
                id INT AUTO_INCREMENT PRIMARY KEY,
                setting_key VARCHAR(100) NOT NULL UNIQUE,
                setting_value LONGTEXT DEFAULT NULL,
                kategori VARCHAR(50) DEFAULT 'keamanan',
                keterangan VARCHAR(255) DEFAULT NULL,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // Seed default pengaturan jika belum ada
            $defaults = [
                ['anti_cheat_mode', '1', 'keamanan', 'Mode Anti-Cheat Utama'],
                ['force_fullscreen', '1', 'keamanan', 'Kunci Layar Penuh (Force Fullscreen)'],
                ['block_tab_switch', '1', 'keamanan', 'Deteksi Pindah Tab & Buka Aplikasi Lain'],
                ['max_tab_violations', '3', 'keamanan', 'Batas Toleransi Pelanggaran Pindah Tab'],
                ['action_on_violation', 'submit', 'keamanan', 'Tindakan Pelanggaran Batas (submit/lock)'],
                ['disable_right_click', '1', 'keamanan', 'Blokir Klik Kanan & Inspect Element'],
                ['disable_copy_paste', '1', 'keamanan', 'Blokir Copy-Paste & Seleksi Teks'],
                ['lock_single_device', '1', 'keamanan', 'Kunci 1 Akun 1 Perangkat (Cegah Multi-Login)'],
                ['watermark_student_nis', '1', 'keamanan', 'Watermark Transparan Nama & NIS Siswa di Layar'],
                ['sound_alarm_on_violation', '1', 'keamanan', 'Bunyikan Alarm Suara Bahaya Saat Keluar Tab'],
                ['block_back_button', '1', 'keamanan', 'Kunci & Blokir Tombol Kembali Browser / Gawai (Anti-Back)'],
                ['default_durasi_menit', '90', 'ujian', 'Durasi Ujian Default (Menit)'],
                ['default_kkm', '75', 'ujian', 'Standar KKM Default Madrasah'],
                ['acak_soal_default', '1', 'ujian', 'Acak Urutan Soal Ujian Default'],
                ['acak_opsi_default', '1', 'ujian', 'Acak Urutan Opsi Jawaban Default'],
                ['show_score_to_student', '1', 'ujian', 'Tampilkan Skor ke Siswa setelah Ujian'],
                ['show_discussion_to_student', '0', 'ujian', 'Tampilkan Kunci/Pembahasan ke Siswa'],
                ['allow_student_review', '1', 'ujian', 'Izinkan Siswa Cek Ulang Jawaban'],
                ['token_mode', 'manual', 'ujian', 'Mode Perilisan Token Ujian (manual/auto)'],
                ['token_refresh_interval', '120', 'ujian', 'Masa Berlaku Token (Menit)'],
                ['institution_name', 'MAS MIFTAHUL HUDA', 'umum', 'Nama Institusi / Madrasah'],
                ['cbt_app_title', 'CBT Online v2.5 Online', 'umum', 'Label Branding Sistem CBT']
            ];

            $stmtInsSetting = $db->prepare("INSERT IGNORE INTO cbt_pengaturan (setting_key, setting_value, kategori, keterangan) VALUES (?, ?, ?, ?)");
            foreach ($defaults as $df) {
                $stmtInsSetting->execute($df);
            }

            // Bersihkan data dummy awal (seperti UJN-MAT-01, UJN-FIQ-02, MAS982, nis dummy)
            $db->exec("DELETE FROM cbt_soal WHERE ujian_id IN (SELECT id FROM cbt_ujian WHERE kode_ujian IN ('UJN-MAT-01', 'UJN-FIQ-02'))");
            $db->exec("DELETE FROM cbt_peserta WHERE ujian_id IN (SELECT id FROM cbt_ujian WHERE kode_ujian IN ('UJN-MAT-01', 'UJN-FIQ-02')) OR nis LIKE '2024100%'");
            $db->exec("DELETE FROM cbt_token WHERE token = 'MAS982'");
            $db->exec("DELETE FROM cbt_ujian WHERE kode_ujian IN ('UJN-MAT-01', 'UJN-FIQ-02')");

        } catch (\Exception $e) {
            // Silence exception
        }
    }

    /**
     * Dapatkan daftar Mata Pelajaran Real MAS MIFTAHUL HUDA (Madrasah Aliyah)
     * Terisolasi dari sisa data MI (Madrasah Ibtidaiyah) lama
     */
    public static function getRealMapelList($db_siakad = null, $db_core = null) {
        return [
            ['id' => 1, 'nama_mapel' => "Al-Qur'an Hadits", 'kode_mapel' => 'QH'],
            ['id' => 2, 'nama_mapel' => 'Akidah Akhlak', 'kode_mapel' => 'AA'],
            ['id' => 3, 'nama_mapel' => 'Fikih', 'kode_mapel' => 'FIQ'],
            ['id' => 4, 'nama_mapel' => 'Sejarah Kebudayaan Islam (SKI)', 'kode_mapel' => 'SKI'],
            ['id' => 5, 'nama_mapel' => 'Bahasa Arab', 'kode_mapel' => 'ARB'],
            ['id' => 6, 'nama_mapel' => 'Bahasa Indonesia', 'kode_mapel' => 'IND'],
            ['id' => 7, 'nama_mapel' => 'Bahasa Inggris', 'kode_mapel' => 'ING'],
            ['id' => 8, 'nama_mapel' => 'Matematika', 'kode_mapel' => 'MTK'],
            ['id' => 9, 'nama_mapel' => 'Informatika', 'kode_mapel' => 'INF'],
            ['id' => 10, 'nama_mapel' => 'Fisika', 'kode_mapel' => 'FIS'],
            ['id' => 11, 'nama_mapel' => 'Kimia', 'kode_mapel' => 'KIM'],
            ['id' => 12, 'nama_mapel' => 'Biologi', 'kode_mapel' => 'BIO'],
            ['id' => 13, 'nama_mapel' => 'Sosiologi', 'kode_mapel' => 'SOS'],
            ['id' => 14, 'nama_mapel' => 'Geografi', 'kode_mapel' => 'GEO'],
            ['id' => 15, 'nama_mapel' => 'Ekonomi', 'kode_mapel' => 'EKO'],
            ['id' => 16, 'nama_mapel' => 'Sejarah Umum', 'kode_mapel' => 'SEJ'],
            ['id' => 17, 'nama_mapel' => 'Pendidikan Pancasila & Kewarganegaraan (PPKn)', 'kode_mapel' => 'PPKN'],
            ['id' => 18, 'nama_mapel' => 'Pendidikan Jasmani, Olahraga & Kesehatan (PJOK)', 'kode_mapel' => 'PJOK'],
            ['id' => 19, 'nama_mapel' => 'Seni Budaya', 'kode_mapel' => 'SBD'],
        ];
    }

    /**
     * Dapatkan daftar Kelas Real dari tabel kelas di database core
     */
    public static function getRealKelasList($db_core, $db_siakad = null) {
        if ($db_siakad) {
            try {
                $list = $db_siakad->query("SELECT id, nama_kelas, tingkat FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($list)) return $list;
            } catch (\Exception $e) {}
        }
        try {
            $list = $db_core->query("SELECT id, nama_kelas, tingkat FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($list)) return $list;
        } catch (\Exception $e) {}

        return [
            ['id' => 1, 'nama_kelas' => 'X E.1', 'tingkat' => 'X'],
            ['id' => 2, 'nama_kelas' => 'XI F.1', 'tingkat' => 'XI'],
            ['id' => 3, 'nama_kelas' => 'XII F.1', 'tingkat' => 'XII'],
            ['id' => 4, 'nama_kelas' => 'XII F.2', 'tingkat' => 'XII']
        ];
    }

    /**
     * Dapatkan daftar Siswa Real dari tabel siswa di database core
     */
    public static function getRealSiswaList($db_core, $kelasNama = '') {
        try {
            $sql = "SELECT s.id, s.nis, s.nama, COALESCE(k.nama_kelas, 'Umum') as kelas_nama 
                    FROM siswa s 
                    LEFT JOIN kelas k ON s.kelas_id = k.id 
                    WHERE s.status IN ('Aktif', 'aktif', '1')";
            $params = [];
            if (!empty($kelasNama) && $kelasNama !== 'Semua Kelas') {
                $sql .= " AND k.nama_kelas = :kelas_nama";
                $params[':kelas_nama'] = $kelasNama;
            }
            $sql .= " ORDER BY s.nama ASC";
            $stmt = $db_core->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Sinkronkan daftar siswa real ke cbt_peserta untuk paket ujian tertentu
     */
    public static function syncPesertaFromRealSiswa($db_core, $ujianId, $kelasNama = '') {
        $realStudents = self::getRealSiswaList($db_core, $kelasNama);
        if (empty($realStudents)) {
            return 0;
        }

        $stmtExisting = $db_core->prepare("SELECT siswa_id FROM cbt_peserta WHERE ujian_id = ? AND siswa_id IS NOT NULL");
        $stmtExisting->execute([$ujianId]);
        $existingIds = $stmtExisting->fetchAll(PDO::FETCH_COLUMN);

        $stmtInsert = $db_core->prepare("INSERT INTO cbt_peserta (ujian_id, siswa_id, nis, nama_siswa, kelas_nama, status_pengerjaan, jumlah_benar, jumlah_salah, nilai_akhir) 
                                         VALUES (?, ?, ?, ?, ?, 'Belum Mengerjakan', 0, 0, 0)");

        $synced = 0;
        foreach ($realStudents as $st) {
            if (!in_array($st['id'], $existingIds)) {
                $stmtInsert->execute([
                    $ujianId, $st['id'], $st['nis'], $st['nama'], $st['kelas_nama']
                ]);
                $synced++;
            }
        }
        return $synced;
    }

    /**
     * Inisialisasi Auth & Koneksi Database
     */
    private static function getAuthContext() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            redirect('/login');
            exit;
        }

        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

        self::ensureTables($db_core);

        $roleId = isset($_SESSION['role_id']) ? (int)$_SESSION['role_id'] : 0;
        $isKamad = !empty($_SESSION['is_kamad']);
        $isAdminOrKamad = ($roleId === 1 || $roleId === 99 || $isKamad);

        return [
            'db_core' => $db_core,
            'db_siakad' => $db_siakad,
            'roleId' => $roleId,
            'isAdminOrKamad' => $isAdminOrKamad
        ];
    }

    /**
     * Dashboard CBT Online (Real Data)
     */
    public static function dashboard() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];

        // Real counts
        $totalUjian = (int)$db->query("SELECT COUNT(*) FROM cbt_ujian")->fetchColumn();
        $totalSoal = (int)$db->query("SELECT COUNT(*) FROM cbt_soal")->fetchColumn();
        $totalPeserta = (int)$db->query("SELECT COUNT(*) FROM cbt_peserta")->fetchColumn();
        $totalPesertaSelesai = (int)$db->query("SELECT COUNT(*) FROM cbt_peserta WHERE status_pengerjaan = 'Selesai'")->fetchColumn();

        // Real active token
        $stmtTok = $db->query("SELECT * FROM cbt_token WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1");
        $tokenAktif = $stmtTok->fetch(PDO::FETCH_ASSOC) ?: null;

        // Real exams
        $ujianList = $db->query("SELECT * FROM cbt_ujian ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

        // Real recent student submissions
        $stmtP = $db->query("SELECT p.*, u.nama_ujian 
                             FROM cbt_peserta p 
                             LEFT JOIN cbt_ujian u ON u.id = p.ujian_id 
                             WHERE p.status_pengerjaan = 'Selesai'
                             ORDER BY p.waktu_selesai DESC LIMIT 6");
        $pesertaTerbaru = $stmtP->fetchAll(PDO::FETCH_ASSOC);

        $title = "Dashboard CBT Online - MAS MIFTAHUL HUDA";
        ob_start();
        include __DIR__ . '/../../resources/views/cbt/dashboard.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Jadwal Ujian
     */
    public static function jadwal() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $db_siakad = $ctx['db_siakad'];
        self::ensureTables($db);

        // 1. Ambil daftar topik dari Bank Soal (bs_topik)
        $allTopikList = [];
        if ($db_siakad) {
            try {
                $allTopikList = $db_siakad->query("SELECT * FROM bs_topik ORDER BY (tgl_mulai IS NOT NULL) DESC, tgl_mulai ASC, id DESC")->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Exception $e) {}
        }
        if (empty($allTopikList)) {
            try {
                $allTopikList = $db->query("SELECT * FROM bs_topik ORDER BY (tgl_mulai IS NOT NULL) DESC, tgl_mulai ASC, id DESC")->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Exception $e) {}
        }

        // Sembunyikan topik yang sudah 'Selesai' atau 'Arsip' dari pilihan dropdown jadwal
        $topikList = array_values(array_filter($allTopikList, function($tp) {
            $st = strtolower(trim($tp['status'] ?? ''));
            return $st !== 'selesai' && $st !== 'arsip';
        }));

        // Tentukan topik aktif: utamakan topik yang berstatus 'Aktif'
        $selectedTopikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0;
        $selectedTopik = null;

        if ($selectedTopikId > 0) {
            foreach ($allTopikList as $tp) {
                if ((int)$tp['id'] === $selectedTopikId) {
                    $selectedTopik = $tp;
                    break;
                }
            }
        }

        // Jika topik belum dipilih atau topik yang diminta sudah selesai, pilih yang berstatus 'Aktif'
        if (!$selectedTopik || strtolower(trim($selectedTopik['status'] ?? '')) === 'selesai') {
            foreach ($topikList as $tp) {
                if (strtolower(trim($tp['status'] ?? '')) === 'aktif') {
                    $selectedTopik = $tp;
                    $selectedTopikId = (int)$tp['id'];
                    break;
                }
            }
        }

        // Jika tetap belum ada topik aktif, gunakan item pertama dari topikList
        if (!$selectedTopik && !empty($topikList)) {
            $selectedTopik = $topikList[0];
            $selectedTopikId = (int)$selectedTopik['id'];
        } elseif (!$selectedTopik && !empty($allTopikList)) {
            $selectedTopik = $allTopikList[0];
            $selectedTopikId = (int)$selectedTopik['id'];
        }

        // Auto-sinkronkan grid jadwal dengan cbt_ujian & Bank Soal
        self::syncJadwalWithUjian($db, $db_siakad, $selectedTopikId);

        // Pemetaan hari ke tanggal riil berdasarkan tgl_mulai topik
        $hari_tanggal_map = [];
        if (!empty($selectedTopik['tgl_mulai'])) {
            $startDate = new \DateTime($selectedTopik['tgl_mulai']);
            $startDayIndex = (int)$startDate->format('N'); // 1 = Senin, 6 = Sabtu, 7 = Minggu
            $mondayOfStart = clone $startDate;
            if ($startDayIndex > 1) {
                $mondayOfStart->modify('-' . ($startDayIndex - 1) . ' days');
            }
            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $bulanIndoShort = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
                7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
            ];
            foreach ($days as $idx => $d) {
                $dDate = clone $mondayOfStart;
                $dDate->modify("+{$idx} days");
                $mNum = (int)$dDate->format('n');
                $hari_tanggal_map[$d] = [
                    'date' => $dDate->format('Y-m-d'),
                    'label_short' => $dDate->format('d') . ' ' . ($bulanIndoShort[$mNum] ?? ''),
                    'label_full' => $d . ', ' . $dDate->format('d') . ' ' . ($bulanIndoShort[$mNum] ?? '') . ' ' . $dDate->format('Y')
                ];
            }
        }

        // Ambil daftar kelas real
        $kelasList = self::getRealKelasList($db, $db_siakad);

        // Ambil daftar mapel real madrasah
        $mapelList = self::getRealMapelList($db_siakad, $db);

        // Ambil daftar custom jam slot
        $slotList = $db->query("SELECT * FROM cbt_jam_slot ORDER BY urutan ASC, jam_mulai ASC")->fetchAll(PDO::FETCH_ASSOC);
        if (empty($slotList)) {
            $slotList = [
                ['id' => 1, 'urutan' => 1, 'label_sesi' => 'Sesi 1', 'jam_mulai' => '07:30', 'jam_selesai' => '08:45', 'is_istirahat' => 0],
                ['id' => 2, 'urutan' => 2, 'label_sesi' => 'Sesi 2', 'jam_mulai' => '09:00', 'jam_selesai' => '10:15', 'is_istirahat' => 0],
                ['id' => 3, 'urutan' => 3, 'label_sesi' => 'Istirahat', 'jam_mulai' => '10:15', 'jam_selesai' => '10:45', 'is_istirahat' => 1],
                ['id' => 4, 'urutan' => 4, 'label_sesi' => 'Sesi 3', 'jam_mulai' => '10:45', 'jam_selesai' => '12:00', 'is_istirahat' => 0],
                ['id' => 5, 'urutan' => 5, 'label_sesi' => 'Sesi 4', 'jam_mulai' => '12:30', 'jam_selesai' => '13:45', 'is_istirahat' => 0],
            ];
        }

        // Ambil semua paket ujian
        $ujianList = $db->query("SELECT * FROM cbt_ujian ORDER BY (tgl_mulai IS NOT NULL) DESC, tgl_mulai DESC, created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

        // Ambil daftar guru untuk pengawas (dengan fallback nama guru lengkap masmifhda)
        $guruList = [];
        try {
            $guruList = $db->query("SELECT id, nama FROM guru ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}
        if (empty($guruList) && $db_siakad) {
            try {
                $guruList = $db_siakad->query("SELECT id, nama FROM aula_db.guru ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Exception $e) {}
        }
        if (empty($guruList)) {
            $guruList = [
                ['id' => 1, 'nama' => 'M AZIZ SULAIMAN, S.Pd'],
                ['id' => 2, 'nama' => 'SAIPUDIN AZIZ, S.Pd'],
                ['id' => 3, 'nama' => 'INDAH YUNITASARI, S.Pd'],
                ['id' => 4, 'nama' => 'M. Wahidin, S.Pd'],
                ['id' => 5, 'nama' => 'SYAIFUL BAHRI, S.Pd'],
                ['id' => 6, 'nama' => 'SAWALUDIN, S.Pd'],
                ['id' => 7, 'nama' => 'Drs. Hamim Hasyim'],
                ['id' => 8, 'nama' => 'ERWANSAH, S.Pd'],
                ['id' => 9, 'nama' => 'ENDANG KURNIASIH, S.E.'],
                ['id' => 10, 'nama' => 'JULAINI, S.Pd'],
                ['id' => 11, 'nama' => 'SHOBIRIN'],
                ['id' => 12, 'nama' => 'SITI AISAH, S.Pd.I'],
                ['id' => 13, 'nama' => 'DIDI HARTONO, S.Pd.I']
            ];
        }

        // Ambil grid jadwal tersimpan untuk topik aktif (mendukung mapel langsung tanpa ujian_id)
        $gridRows = [];
        if ($selectedTopikId > 0) {
            $stmtG = $db->prepare("
                SELECT g.*, 
                       COALESCE(NULLIF(g.mapel_nama, ''), u.mapel_nama, 'Mata Pelajaran') AS mapel_nama,
                       COALESCE(u.nama_ujian, g.mapel_nama) AS nama_ujian,
                       COALESCE(u.kode_ujian, '-') AS kode_ujian,
                       COALESCE(u.durasi_menit, 90) AS durasi_menit,
                       COALESCE(u.status, 'Aktif') as ujian_status 
                FROM cbt_jadwal_grid g
                LEFT JOIN cbt_ujian u ON g.ujian_id = u.id
                WHERE g.topik_id = ?
                ORDER BY g.id ASC
            ");
            $stmtG->execute([$selectedTopikId]);
            $gridRows = $stmtG->fetchAll(PDO::FETCH_ASSOC);

            // Fallback legacy rows jika topik_id belum diset
            if (empty($gridRows) && !empty($topikList) && (int)$topikList[0]['id'] === $selectedTopikId) {
                $stmtLegacy = $db->query("
                    SELECT g.*, 
                           COALESCE(NULLIF(g.mapel_nama, ''), u.mapel_nama, 'Mata Pelajaran') AS mapel_nama,
                           COALESCE(u.nama_ujian, g.mapel_nama) AS nama_ujian,
                           COALESCE(u.kode_ujian, '-') AS kode_ujian,
                           COALESCE(u.durasi_menit, 90) AS durasi_menit,
                           COALESCE(u.status, 'Aktif') as ujian_status 
                    FROM cbt_jadwal_grid g
                    LEFT JOIN cbt_ujian u ON g.ujian_id = u.id
                    WHERE g.topik_id IS NULL OR g.topik_id = 0
                    ORDER BY g.id ASC
                ");
                if ($stmtLegacy) {
                    $gridRows = $stmtLegacy->fetchAll(PDO::FETCH_ASSOC);
                }
            }
        } else {
            $gridRows = $db->query("
                SELECT g.*, 
                       COALESCE(NULLIF(g.mapel_nama, ''), u.mapel_nama, 'Mata Pelajaran') AS mapel_nama,
                       COALESCE(u.nama_ujian, g.mapel_nama) AS nama_ujian,
                       COALESCE(u.kode_ujian, '-') AS kode_ujian,
                       COALESCE(u.durasi_menit, 90) AS durasi_menit,
                       COALESCE(u.status, 'Aktif') as ujian_status 
                FROM cbt_jadwal_grid g
                LEFT JOIN cbt_ujian u ON g.ujian_id = u.id
                ORDER BY g.id ASC
            ")->fetchAll(PDO::FETCH_ASSOC);
        }

        // Susun matriks jadwal: $jadwal_master[hari][jam_slot][kelas_key] = data
        $jadwal_master = [];
        foreach ($gridRows as $gr) {
            $h = $gr['hari'];
            $slot = $gr['jam_slot'];
            $kKey = !empty($gr['kelas_id']) ? $gr['kelas_id'] : $gr['kelas_nama'];
            $jadwal_master[$h][$slot][$kKey] = $gr;
            $jadwal_master[$h][$slot][$gr['kelas_nama']] = $gr;
        }

        $hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $title = "Jadwal Ujian CBT - MAS MIFTAHUL HUDA";
        ob_start();
        include __DIR__ . '/../../resources/views/cbt/jadwal.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Simpan / Perbarui Jadwal Ujian
     */
    public static function saveJadwal() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = !empty($_POST['id']) ? (int)$_POST['id'] : (!empty($_POST['ujian_id']) ? (int)$_POST['ujian_id'] : 0);
            $tgl_mulai = !empty($_POST['tgl_mulai']) ? str_replace('T', ' ', $_POST['tgl_mulai']) : null;
            $tgl_selesai = !empty($_POST['tgl_selesai']) ? str_replace('T', ' ', $_POST['tgl_selesai']) : null;
            $durasi_menit = !empty($_POST['durasi_menit']) ? (int)$_POST['durasi_menit'] : 90;
            $status = !empty($_POST['status']) ? trim($_POST['status']) : 'Aktif';

            if ($id > 0) {
                $stmt = $db->prepare("UPDATE cbt_ujian SET tgl_mulai = ?, tgl_selesai = ?, durasi_menit = ?, status = ? WHERE id = ?");
                $stmt->execute([$tgl_mulai, $tgl_selesai, $durasi_menit, $status, $id]);
            }
            redirect('/cbt/jadwal?success=1');
            exit;
        }
        redirect('/cbt/jadwal');
    }

/**
     * Toggle Status Ujian (Aktif <-> Selesai)
     */
    public static function toggleStatusJadwal() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $curr = isset($_POST['current_status']) ? trim($_POST['current_status']) : '';

            $newStatus = ($curr === 'Aktif') ? 'Selesai' : 'Aktif';
            if ($id > 0) {
                $stmt = $db->prepare("UPDATE cbt_ujian SET status = ? WHERE id = ?");
                $stmt->execute([$newStatus, $id]);
            }
            redirect('/cbt/jadwal?updated=1');
            exit;
        }
        redirect('/cbt/jadwal');
    }

/**
     * Reset / Hapus Jadwal Ujian
     */
    public static function deleteJadwal() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if ($id > 0) {
                $stmt = $db->prepare("UPDATE cbt_ujian SET tgl_mulai = NULL, tgl_selesai = NULL, status = 'Draft' WHERE id = ?");
                $stmt->execute([$id]);
            }
            redirect('/cbt/jadwal?deleted=1');
            exit;
        }
        redirect('/cbt/jadwal');
    }

/**
     * Simpan / Pasang Jadwal Ujian pada Matriks Grid
     */
    public static function saveGridJadwal() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $topik_id = !empty($_POST['topik_id']) ? (int)$_POST['topik_id'] : null;
            $mapel_nama = trim($_POST['mapel_nama'] ?? '');
            $ujian_id = !empty($_POST['ujian_id']) ? (int)$_POST['ujian_id'] : null;
            $hari = trim($_POST['hari'] ?? 'Senin');
            $jam_slot = trim($_POST['jam_slot'] ?? '');
            $kelas_id = !empty($_POST['kelas_id']) ? (int)$_POST['kelas_id'] : null;
            $kelas_nama = trim($_POST['kelas_nama'] ?? '');
            $pengawas = trim($_POST['pengawas'] ?? '');
            $ruang = trim($_POST['ruang'] ?? '');
            $tanggal = !empty($_POST['tanggal']) ? $_POST['tanggal'] : null;

            if (empty($mapel_nama) && $ujian_id) {
                try {
                    $uRow = $db->query("SELECT mapel_nama FROM cbt_ujian WHERE id = " . (int)$ujian_id)->fetch(PDO::FETCH_ASSOC);
                    $mapel_nama = $uRow['mapel_nama'] ?? '';
                } catch (\Exception $e) {}
            }

            if ((!empty($mapel_nama) || !empty($ujian_id)) && !empty($jam_slot) && !empty($kelas_nama)) {
                $checkSql = "SELECT id FROM cbt_jadwal_grid WHERE hari = ? AND jam_slot = ? AND kelas_nama = ?";
                $params = [$hari, $jam_slot, $kelas_nama];
                if ($topik_id) {
                    $checkSql .= " AND (topik_id = ? OR topik_id IS NULL)";
                    $params[] = $topik_id;
                }
                $stmtCheck = $db->prepare($checkSql);
                $stmtCheck->execute($params);
                $existingId = $stmtCheck->fetchColumn();

                if ($existingId) {
                    $stmtUp = $db->prepare("UPDATE cbt_jadwal_grid SET topik_id = ?, ujian_id = ?, mapel_nama = ?, kelas_id = ?, pengawas = ?, ruang = ?, tanggal = ? WHERE id = ?");
                    $stmtUp->execute([$topik_id, $ujian_id, $mapel_nama, $kelas_id, $pengawas, $ruang, $tanggal, $existingId]);
                } else {
                    $stmtIns = $db->prepare("INSERT INTO cbt_jadwal_grid (topik_id, ujian_id, mapel_nama, hari, jam_slot, kelas_id, kelas_nama, pengawas, ruang, tanggal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmtIns->execute([$topik_id, $ujian_id, $mapel_nama, $hari, $jam_slot, $kelas_id, $kelas_nama, $pengawas, $ruang, $tanggal]);
                }

                if ($ujian_id) {
                    $db->prepare("UPDATE cbt_ujian SET status = 'Aktif', kelas_nama = ? WHERE id = ?")->execute([$kelas_nama, $ujian_id]);
                }
            }
            redirect('/cbt/jadwal?topik_id=' . ($topik_id ?: '') . '&msg=saved');
            exit;
        }
        redirect('/cbt/jadwal');
    }

    /**
     * Hapus Slot Jadwal Ujian dari Grid
     */
    public static function deleteGridJadwal() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $topik_id = !empty($_POST['topik_id']) ? (int)$_POST['topik_id'] : '';
            if ($id > 0) {
                $db->prepare("DELETE FROM cbt_jadwal_grid WHERE id = ?")->execute([$id]);
            }
            redirect('/cbt/jadwal?topik_id=' . $topik_id . '&msg=deleted');
            exit;
        }
        redirect('/cbt/jadwal');
    }

    /**
     * Simpan / Perbarui Custom Jam / Slot Waktu Ujian
     */
    public static function saveJamSlot() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $urutan = (int)($_POST['urutan'] ?? 1);
            $label_sesi = trim($_POST['label_sesi'] ?? 'Sesi');
            $jam_mulai = trim($_POST['jam_mulai'] ?? '');
            $jam_selesai = trim($_POST['jam_selesai'] ?? '');
            $is_istirahat = !empty($_POST['is_istirahat']) ? 1 : 0;

            if (!empty($jam_mulai) && !empty($jam_selesai)) {
                if ($id > 0) {
                    $stmt = $db->prepare("UPDATE cbt_jam_slot SET urutan = ?, label_sesi = ?, jam_mulai = ?, jam_selesai = ?, is_istirahat = ? WHERE id = ?");
                    $stmt->execute([$urutan, $label_sesi, $jam_mulai, $jam_selesai, $is_istirahat, $id]);
                } else {
                    $stmt = $db->prepare("INSERT INTO cbt_jam_slot (urutan, label_sesi, jam_mulai, jam_selesai, is_istirahat) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$urutan, $label_sesi, $jam_mulai, $jam_selesai, $is_istirahat]);
                }
            }
            redirect('/cbt/jadwal?msg=slot_saved');
            exit;
        }
        redirect('/cbt/jadwal');
    }

    /**
     * Hapus Slot Jam
     */
    public static function deleteJamSlot() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id > 0) {
                $db->prepare("DELETE FROM cbt_jam_slot WHERE id = ?")->execute([$id]);
            }
            redirect('/cbt/jadwal?msg=slot_deleted');
            exit;
        }
        redirect('/cbt/jadwal');
    }

    /**
     * Reset Slot Jam ke Standar
     */
    public static function resetJamSlot() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db->exec("TRUNCATE TABLE cbt_jam_slot");
            $db->exec("INSERT INTO cbt_jam_slot (urutan, label_sesi, jam_mulai, jam_selesai, is_istirahat) VALUES
                (1, 'Sesi 1', '07:30', '08:45', 0),
                (2, 'Sesi 2', '09:00', '10:15', 0),
                (3, 'Istirahat', '10:15', '10:45', 1),
                (4, 'Sesi 3', '10:45', '12:00', 0),
                (5, 'Sesi 4', '12:30', '13:45', 0)
            ");
            redirect('/cbt/jadwal?msg=slot_reset');
            exit;
        }
        redirect('/cbt/jadwal');
    }

    /**
     * Sinkronisasi Otomatis Matriks Jadwal (cbt_jadwal_grid) dengan Paket Ujian (cbt_ujian)
     * Menjamin bahwa setiap mapel yang dijadwalkan langsung memiliki paket ujian aktif,
     * butir soal tersinkron dari Bank Soal, token aktif tersedia, dan terbaca 100% di APK Siswa!
     */
    public static function syncJadwalWithUjian($db, $db_siakad = null, $topik_id = null) {
        if (!$db) return;
        if (!$db_siakad) {
            $ctx = self::getAuthContext();
            $db_siakad = $ctx['db_siakad'] ?? $db;
        }

        try {
            self::ensureTables($db);

            $sql = "SELECT * FROM cbt_jadwal_grid WHERE mapel_nama IS NOT NULL AND mapel_nama != ''";
            $params = [];
            if ($topik_id) {
                $sql .= " AND (topik_id = ? OR topik_id IS NULL)";
                $params[] = $topik_id;
            }
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $gridRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($gridRows as $g) {
                $tId = !empty($g['topik_id']) ? (int)$g['topik_id'] : ($topik_id ?: 0);
                $mapel = trim($g['mapel_nama']);
                $kelasNama = trim($g['kelas_nama']);
                $ujianId = !empty($g['ujian_id']) ? (int)$g['ujian_id'] : 0;

                // Tentukan tingkat kelas (Kelas X, Kelas XI, Kelas XII)
                $tingkat = 'Kelas X';
                if (stripos($kelasNama, 'XII') !== false || stripos($kelasNama, '12') !== false) {
                    $tingkat = 'Kelas XII';
                } elseif (stripos($kelasNama, 'XI') !== false || stripos($kelasNama, '11') !== false) {
                    $tingkat = 'Kelas XI';
                }

                $existingUjian = null;
                if ($ujianId > 0) {
                    $stmtU = $db->prepare("SELECT * FROM cbt_ujian WHERE id = ?");
                    $stmtU->execute([$ujianId]);
                    $existingUjian = $stmtU->fetch(PDO::FETCH_ASSOC);
                }

                if (!$existingUjian) {
                    // Cari berdasarkan topik_id, mapel_nama, dan kelas_nama / tingkat
                    $stmtFind = $db->prepare("SELECT * FROM cbt_ujian WHERE (topik_id = ? OR topik_id IS NULL) AND mapel_nama = ? AND (kelas_nama = ? OR kelas_nama = ? OR kelas_nama = 'Semua Kelas' OR kelas_nama LIKE ?) ORDER BY id DESC LIMIT 1");
                    $stmtFind->execute([$tId, $mapel, $kelasNama, $tingkat, '%' . $tingkat . '%']);
                    $existingUjian = $stmtFind->fetch(PDO::FETCH_ASSOC);
                }

                if (!$existingUjian) {
                    $kode = 'UJN-' . strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $mapel), 0, 3)) . '-' . strtoupper(substr(uniqid(), -4));
                    $namaUjian = "Ujian {$mapel} - {$kelasNama}";
                    
                    if ($tId) {
                        try {
                            $tpRow = $db_siakad->query("SELECT nama_topik FROM bs_topik WHERE id = {$tId}")->fetch(PDO::FETCH_ASSOC);
                            if (!$tpRow && $db !== $db_siakad) {
                                $tpRow = $db->query("SELECT nama_topik FROM bs_topik WHERE id = {$tId}")->fetch(PDO::FETCH_ASSOC);
                            }
                            if ($tpRow && !empty($tpRow['nama_topik'])) {
                                $namaUjian = $tpRow['nama_topik'] . " - " . $mapel . " (" . $kelasNama . ")";
                            }
                        } catch (\Exception $e) {}
                    }

                    $stmtIns = $db->prepare("INSERT INTO cbt_ujian (topik_id, kode_ujian, nama_ujian, mapel_nama, kelas_nama, durasi_menit, kkm, acak_soal, acak_opsi, status) VALUES (?, ?, ?, ?, ?, 90, 75, 1, 1, 'Aktif')");
                    $stmtIns->execute([$tId, $kode, $namaUjian, $mapel, $kelasNama]);
                    $newUjianId = (int)$db->lastInsertId();
                    $existingUjian = ['id' => $newUjianId, 'topik_id' => $tId, 'mapel_nama' => $mapel, 'kelas_nama' => $kelasNama];

                    $db->prepare("UPDATE cbt_jadwal_grid SET ujian_id = ? WHERE id = ?")->execute([$newUjianId, $g['id']]);
                } else {
                    if (empty($g['ujian_id']) || $g['ujian_id'] != $existingUjian['id']) {
                        $db->prepare("UPDATE cbt_jadwal_grid SET ujian_id = ? WHERE id = ?")->execute([$existingUjian['id'], $g['id']]);
                    }
                    $db->prepare("UPDATE cbt_ujian SET status = 'Aktif' WHERE id = ?")->execute([$existingUjian['id']]);
                }

                // Sinkronkan butir soal jika cbt_soal masih 0
                $targetUjianId = (int)$existingUjian['id'];
                $soalCount = (int)$db->query("SELECT COUNT(*) FROM cbt_soal WHERE ujian_id = {$targetUjianId}")->fetchColumn();
                if ($soalCount === 0) {
                    $bsItems = [];
                    try {
                        $stmtBS = $db_siakad->prepare("SELECT * FROM bs_soal WHERE mapel_nama = ? AND (topik_id = ? OR ? = 0) AND (kelas_nama = ? OR kelas_nama = ? OR kelas_nama LIKE ? OR kelas_nama IS NULL OR kelas_nama = '') ORDER BY nomor_urut ASC, id ASC LIMIT 50");
                        $stmtBS->execute([$mapel, $tId, $tId, $kelasNama, $tingkat, '%' . $tingkat . '%']);
                        $bsItems = $stmtBS->fetchAll(PDO::FETCH_ASSOC);
                    } catch (\Exception $e) {}

                    if (empty($bsItems) && $db !== $db_siakad) {
                        try {
                            $stmtBSCore = $db->prepare("SELECT * FROM bs_soal WHERE mapel_nama = ? AND (topik_id = ? OR ? = 0) AND (kelas_nama = ? OR kelas_nama = ? OR kelas_nama LIKE ? OR kelas_nama IS NULL OR kelas_nama = '') ORDER BY nomor_urut ASC, id ASC LIMIT 50");
                            $stmtBSCore->execute([$mapel, $tId, $tId, $kelasNama, $tingkat, '%' . $tingkat . '%']);
                            $bsItems = $stmtBSCore->fetchAll(PDO::FETCH_ASSOC);
                        } catch (\Exception $e) {}
                    }

                    if (empty($bsItems)) {
                        try {
                            $stmtBSAll = $db_siakad->prepare("SELECT * FROM bs_soal WHERE mapel_nama = ? AND (topik_id = ? OR ? = 0) ORDER BY nomor_urut ASC, id ASC LIMIT 50");
                            $stmtBSAll->execute([$mapel, $tId, $tId]);
                            $bsItems = $stmtBSAll->fetchAll(PDO::FETCH_ASSOC);
                        } catch (\Exception $e) {}
                    }

                    // Fallback cerdas: cari butir soal mapel & tingkat ini dari topik lain di Bank Soal
                    if (empty($bsItems)) {
                        try {
                            $stmtBSAny = $db_siakad->prepare("SELECT * FROM bs_soal WHERE mapel_nama = ? AND (kelas_nama = ? OR kelas_nama = ? OR kelas_nama LIKE ? OR kelas_nama IS NULL OR kelas_nama = '') ORDER BY id DESC LIMIT 50");
                            $stmtBSAny->execute([$mapel, $kelasNama, $tingkat, '%' . $tingkat . '%']);
                            $bsItems = $stmtBSAny->fetchAll(PDO::FETCH_ASSOC);
                        } catch (\Exception $e) {}
                    }

                    if (empty($bsItems)) {
                        try {
                            $stmtBSAnyMapel = $db_siakad->prepare("SELECT * FROM bs_soal WHERE mapel_nama = ? ORDER BY id DESC LIMIT 50");
                            $stmtBSAnyMapel->execute([$mapel]);
                            $bsItems = $stmtBSAnyMapel->fetchAll(PDO::FETCH_ASSOC);
                        } catch (\Exception $e) {}
                    }

                    // Fallback terakhir: salin dari paket ujian CBT yang sudah memiliki soal untuk mapel & tingkat yang sama
                    if (empty($bsItems)) {
                        try {
                            $stmtOther = $db->prepare("SELECT id FROM cbt_ujian WHERE mapel_nama = ? AND (kelas_nama = ? OR kelas_nama = ? OR kelas_nama LIKE ? OR kelas_nama = 'Semua Kelas') AND id != ? AND (SELECT COUNT(*) FROM cbt_soal WHERE ujian_id = cbt_ujian.id) > 0 ORDER BY id DESC LIMIT 1");
                            $stmtOther->execute([$mapel, $kelasNama, $tingkat, '%' . $tingkat . '%', $targetUjianId]);
                            $otherUjianId = $stmtOther->fetchColumn();
                            if ($otherUjianId) {
                                $stmtCopy = $db->prepare("SELECT * FROM cbt_soal WHERE ujian_id = ? ORDER BY nomor_urut ASC");
                                $stmtCopy->execute([$otherUjianId]);
                                $bsItems = $stmtCopy->fetchAll(PDO::FETCH_ASSOC);
                            }
                        } catch (\Exception $e) {}
                    }

                    if (!empty($bsItems)) {
                        $stmtInsS = $db->prepare("INSERT INTO cbt_soal (ujian_id, nomor_urut, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, bobot, tipe_soal, konten_tambahan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $no = 1;
                        foreach ($bsItems as $b) {
                            $stmtInsS->execute([
                                $targetUjianId,
                                $no++,
                                $b['pertanyaan'],
                                $b['opsi_a'] ?? '',
                                $b['opsi_b'] ?? '',
                                $b['opsi_c'] ?? '',
                                $b['opsi_d'] ?? '',
                                $b['opsi_e'] ?? '',
                                $b['kunci_jawaban'] ?? 'A',
                                $b['skor_maksimal'] ?? 2.00,
                                $b['tipe_soal'] ?? 'sec_pg',
                                $b['konten_tambahan'] ?? null
                            ]);
                        }
                    }
                }
            }

            // Pastikan Token Ujian Aktif
            $tokAktif = $db->query("SELECT * FROM cbt_token WHERE status = 'Aktif' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            if (!$tokAktif) {
                $tokenStr = 'MAS' . rand(100, 999);
                $db->prepare("INSERT INTO cbt_token (token, masa_berlaku_menit, status, created_at) VALUES (?, 240, 'Aktif', NOW())")->execute([$tokenStr]);
            }
        } catch (\Exception $e) {}
    }

    /**
     * Atur Jadwal Ujian Bulk untuk Satu Kelas (Tombol [ATUR])
     * Mengatur Mata Pelajaran & Guru Pengawas per slot untuk kelas tertentu
     */
    public static function saveBulkKelasJadwal() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $topik_id = !empty($_POST['topik_id']) ? (int)$_POST['topik_id'] : null;
            $kelas_nama = trim($_POST['kelas_nama'] ?? '');
            $kelas_id = !empty($_POST['kelas_id']) ? (int)$_POST['kelas_id'] : null;
            $schedules = $_POST['schedules'] ?? [];

            if (!empty($kelas_nama) && is_array($schedules)) {
                foreach ($schedules as $sc) {
                    $hari = trim($sc['hari'] ?? '');
                    $jam_slot = trim($sc['jam_slot'] ?? '');
                    $mapel_nama = trim($sc['mapel_nama'] ?? '');
                    $ujian_id = !empty($sc['ujian_id']) ? (int)$sc['ujian_id'] : null;
                    $pengawas = trim($sc['pengawas'] ?? '');
                    $ruang = trim($sc['ruang'] ?? '');
                    $tanggal = !empty($sc['tanggal']) ? $sc['tanggal'] : null;

                    if (!empty($hari) && !empty($jam_slot)) {
                        if (!empty($mapel_nama) || !empty($ujian_id)) {
                            if (empty($mapel_nama) && $ujian_id) {
                                try {
                                    $uRow = $db->query("SELECT mapel_nama FROM cbt_ujian WHERE id = " . (int)$ujian_id)->fetch(PDO::FETCH_ASSOC);
                                    $mapel_nama = $uRow['mapel_nama'] ?? '';
                                } catch (\Exception $e) {}
                            }

                            $checkSql = "SELECT id FROM cbt_jadwal_grid WHERE hari = ? AND jam_slot = ? AND kelas_nama = ?";
                            $params = [$hari, $jam_slot, $kelas_nama];
                            if ($topik_id) {
                                $checkSql .= " AND (topik_id = ? OR topik_id IS NULL)";
                                $params[] = $topik_id;
                            }
                            $stmtCheck = $db->prepare($checkSql);
                            $stmtCheck->execute($params);
                            $existingId = $stmtCheck->fetchColumn();

                            if ($existingId) {
                                $stmtUp = $db->prepare("UPDATE cbt_jadwal_grid SET topik_id = ?, ujian_id = ?, mapel_nama = ?, kelas_id = ?, pengawas = ?, ruang = ?, tanggal = COALESCE(?, tanggal) WHERE id = ?");
                                $stmtUp->execute([$topik_id, $ujian_id, $mapel_nama, $kelas_id, $pengawas, $ruang, $tanggal, $existingId]);
                            } else {
                                $stmtIns = $db->prepare("INSERT INTO cbt_jadwal_grid (topik_id, ujian_id, mapel_nama, hari, jam_slot, kelas_id, kelas_nama, pengawas, ruang, tanggal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                                $stmtIns->execute([$topik_id, $ujian_id, $mapel_nama, $hari, $jam_slot, $kelas_id, $kelas_nama, $pengawas, $ruang, $tanggal]);
                            }

                            if ($ujian_id) {
                                $db->prepare("UPDATE cbt_ujian SET status = 'Aktif', kelas_nama = ? WHERE id = ?")->execute([$kelas_nama, $ujian_id]);
                            }
                        } else {
                            $delSql = "DELETE FROM cbt_jadwal_grid WHERE hari = ? AND jam_slot = ? AND kelas_nama = ?";
                            $delParams = [$hari, $jam_slot, $kelas_nama];
                            if ($topik_id) {
                                $delSql .= " AND (topik_id = ? OR topik_id IS NULL)";
                                $delParams[] = $topik_id;
                            }
                            $stmtDel = $db->prepare($delSql);
                            $stmtDel->execute($delParams);
                        }
                    }
                }
            }
            self::syncJadwalWithUjian($db, $ctx['db_siakad'], $topik_id);
            redirect('/cbt/jadwal?topik_id=' . ($topik_id ?: '') . '&msg=bulk_saved');
            exit;
        }
        redirect('/cbt/jadwal');
    }

    /**
     * Atur Jadwal Serentak (Global Semua Kelas)
     * Mengatur mata pelajaran pada sesi hari tertentu untuk seluruh kelas secara sekaligus
     */
    public static function saveGlobalJadwal() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $db_siakad = $ctx['db_siakad'];
        self::ensureTables($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $topik_id = !empty($_POST['topik_id']) ? (int)$_POST['topik_id'] : null;
            $target_kelas = $_POST['target_kelas'] ?? []; // array of nama_kelas
            $schedules = $_POST['schedules'] ?? []; // array of [hari, jam_slot, mapel_nama, pengawas]

            $allKelas = self::getRealKelasList($db, $db_siakad);
            $kelasMap = [];
            foreach ($allKelas as $k) {
                $kelasMap[$k['nama_kelas']] = $k['id'];
            }

            if (!empty($target_kelas) && is_array($target_kelas) && !empty($schedules) && is_array($schedules)) {
                // Ambil pemetaan tanggal dari topik_id jika ada
                $hariTanggal = [];
                if ($topik_id) {
                    $tp = $db->query("SELECT tgl_mulai FROM bs_topik WHERE id = " . (int)$topik_id)->fetch(PDO::FETCH_ASSOC);
                    if (!empty($tp['tgl_mulai'])) {
                        $sDate = new \DateTime($tp['tgl_mulai']);
                        $sIdx = (int)$sDate->format('N');
                        $mon = clone $sDate;
                        if ($sIdx > 1) $mon->modify('-' . ($sIdx - 1) . ' days');
                        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                        foreach ($days as $idx => $d) {
                            $dD = clone $mon;
                            $dD->modify("+{$idx} days");
                            $hariTanggal[$d] = $dD->format('Y-m-d');
                        }
                    }
                }

                foreach ($target_kelas as $kNama) {
                    $kNama = trim($kNama);
                    if (empty($kNama)) continue;
                    $kId = $kelasMap[$kNama] ?? null;

                    foreach ($schedules as $sc) {
                        $hari = trim($sc['hari'] ?? '');
                        $jam_slot = trim($sc['jam_slot'] ?? '');
                        $mapel_nama = trim($sc['mapel_nama'] ?? '');
                        $pengawas = trim($sc['pengawas'] ?? '');
                        $tgl = $hariTanggal[$hari] ?? null;

                        if (empty($hari) || empty($jam_slot)) continue;

                        if (!empty($mapel_nama)) {
                            // Cek apakah slot sudah ada untuk kelas ini
                            $checkSql = "SELECT id FROM cbt_jadwal_grid WHERE hari = ? AND jam_slot = ? AND kelas_nama = ?";
                            $params = [$hari, $jam_slot, $kNama];
                            if ($topik_id) {
                                $checkSql .= " AND (topik_id = ? OR topik_id IS NULL)";
                                $params[] = $topik_id;
                            }
                            $stmtCheck = $db->prepare($checkSql);
                            $stmtCheck->execute($params);
                            $existingId = $stmtCheck->fetchColumn();

                            if ($existingId) {
                                $sqlUp = "UPDATE cbt_jadwal_grid SET topik_id = ?, mapel_nama = ?, kelas_id = ?, tanggal = COALESCE(?, tanggal)";
                                $upParams = [$topik_id, $mapel_nama, $kId, $tgl];
                                if (!empty($pengawas)) {
                                    $sqlUp .= ", pengawas = ?";
                                    $upParams[] = $pengawas;
                                }
                                $sqlUp .= " WHERE id = ?";
                                $upParams[] = $existingId;
                                $stmtUp = $db->prepare($sqlUp);
                                $stmtUp->execute($upParams);
                            } else {
                                $stmtIns = $db->prepare("INSERT INTO cbt_jadwal_grid (topik_id, mapel_nama, hari, jam_slot, kelas_id, kelas_nama, pengawas, tanggal) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                                $stmtIns->execute([$topik_id, $mapel_nama, $hari, $jam_slot, $kId, $kNama, $pengawas, $tgl]);
                            }
                        }
                    }
                }
            }

            self::syncJadwalWithUjian($db, $db_siakad, $topik_id);
            redirect('/cbt/jadwal?topik_id=' . ($topik_id ?: '') . '&msg=global_saved');
            exit;
        }
        redirect('/cbt/jadwal');
    }

    /**
     * Menu Master Topik Ujian (SAS, STS, Ujian Madrasah, dll)
     */
    public static function topik() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $db_siakad = $ctx['db_siakad'];
        self::ensureTables($db);

        $topikList = [];
        if ($db_siakad) {
            try {
                $topikList = $db_siakad->query("SELECT * FROM bs_topik ORDER BY (tgl_mulai IS NOT NULL) DESC, tgl_mulai ASC, id DESC")->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Exception $e) {}
        }
        if (empty($topikList)) {
            try {
                $topikList = $db->query("SELECT * FROM bs_topik ORDER BY (tgl_mulai IS NOT NULL) DESC, tgl_mulai ASC, id DESC")->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Exception $e) {}
        }

        // Hitung jumlah sesi jadwal yang terpasang untuk masing-masing topik
        foreach ($topikList as &$tp) {
            $tId = (int)$tp['id'];
            $stmtJ = $db->prepare("SELECT COUNT(*) FROM cbt_jadwal_grid WHERE topik_id = ?");
            $stmtJ->execute([$tId]);
            $tp['jml_jadwal'] = (int)$stmtJ->fetchColumn();
        }
        unset($tp);

        $title = "Topik Ujian CBT - MAS MIFTAHUL HUDA";
        ob_start();
        include __DIR__ . '/../../resources/views/cbt/topik.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Simpan / Perbarui Topik Ujian (SAS, STS, UM, Try Out, dll)
     */
    public static function saveTopik() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $db_siakad = $ctx['db_siakad'];
        self::ensureTables($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
            $jenis = trim($_POST['jenis'] ?? 'SAS');
            $nama_topik = trim($_POST['nama_topik'] ?? '');
            $tgl_mulai = !empty($_POST['tgl_mulai']) ? $_POST['tgl_mulai'] : null;
            $tgl_selesai = !empty($_POST['tgl_selesai']) ? $_POST['tgl_selesai'] : null;
            $tahun_ajaran = trim($_POST['tahun_ajaran'] ?? '2026/2027');
            $semester = trim($_POST['semester'] ?? 'Ganjil');
            $status = trim($_POST['status'] ?? 'Aktif');
            $keterangan = trim($_POST['keterangan'] ?? '');
            $redirect_to = trim($_POST['redirect_to'] ?? 'topik');

            $newId = $id;
            if (!empty($nama_topik)) {
                if ($id > 0) {
                    $sql = "UPDATE bs_topik SET jenis = ?, nama_topik = ?, keterangan = ?, tahun_ajaran = ?, semester = ?, tgl_mulai = ?, tgl_selesai = ?, status = ? WHERE id = ?";
                    $params = [$jenis, $nama_topik, $keterangan, $tahun_ajaran, $semester, $tgl_mulai, $tgl_selesai, $status, $id];
                    if ($db_siakad) {
                        try { $db_siakad->prepare($sql)->execute($params); } catch (\Exception $e) {}
                    }
                    try { $db->prepare($sql)->execute($params); } catch (\Exception $e) {}
                } else {
                    $sql = "INSERT INTO bs_topik (jenis, nama_topik, keterangan, tahun_ajaran, semester, tgl_mulai, tgl_selesai, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $params = [$jenis, $nama_topik, $keterangan, $tahun_ajaran, $semester, $tgl_mulai, $tgl_selesai, $status];
                    if ($db_siakad) {
                        try {
                            $stmtS = $db_siakad->prepare($sql);
                            $stmtS->execute($params);
                            $newId = (int)$db_siakad->lastInsertId();
                        } catch (\Exception $e) {}
                    }
                    try {
                        $stmtC = $db->prepare($sql);
                        $stmtC->execute($params);
                        if ($newId === 0) {
                            $newId = (int)$db->lastInsertId();
                        }
                    } catch (\Exception $e) {}
                }
            }

            if ($redirect_to === 'jadwal') {
                redirect('/cbt/jadwal?topik_id=' . ($newId ?: $id) . '&msg=topik_created');
            } else {
                redirect('/cbt/topik?msg=saved');
            }
            exit;
        }
        redirect('/cbt/topik');
    }

    /**
     * Hapus Topik Ujian
     */
    public static function deleteTopik() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $db_siakad = $ctx['db_siakad'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id > 0) {
                if ($db_siakad) {
                    try { $db_siakad->prepare("DELETE FROM bs_topik WHERE id = ?")->execute([$id]); } catch (\Exception $e) {}
                }
                try { $db->prepare("DELETE FROM bs_topik WHERE id = ?")->execute([$id]); } catch (\Exception $e) {}
                try { $db->prepare("DELETE FROM cbt_jadwal_grid WHERE topik_id = ?")->execute([$id]); } catch (\Exception $e) {}
            }
            redirect('/cbt/topik?msg=deleted');
            exit;
        }
        redirect('/cbt/topik');
    }

    /**
     * Quick Update Tanggal Mulai & Selesai Topik Ujian dari Halaman Jadwal
     */
    public static function saveTopikDates() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $db_siakad = $ctx['db_siakad'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $topik_id = (int)($_POST['topik_id'] ?? 0);
            $tgl_mulai = !empty($_POST['tgl_mulai']) ? $_POST['tgl_mulai'] : null;
            $tgl_selesai = !empty($_POST['tgl_selesai']) ? $_POST['tgl_selesai'] : null;

            if ($topik_id > 0) {
                $sql = "UPDATE bs_topik SET tgl_mulai = ?, tgl_selesai = ? WHERE id = ?";
                if ($db_siakad) {
                    try { $db_siakad->prepare($sql)->execute([$tgl_mulai, $tgl_selesai, $topik_id]); } catch (\Exception $e) {}
                }
                try { $db->prepare($sql)->execute([$tgl_mulai, $tgl_selesai, $topik_id]); } catch (\Exception $e) {}
            }

            redirect('/cbt/jadwal?topik_id=' . $topik_id . '&msg=dates_updated');
            exit;
        }
        redirect('/cbt/jadwal');
    }

    /**
     * Backwards-compatible saveTopikJadwal
     */
    public static function saveTopikJadwal() {
        $_POST['redirect_to'] = 'jadwal';
        self::saveTopik();
    }

    /**
     * Kelola Paket Ujian (Real Subjects & Classes)
     */
    public static function ujian() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $db_siakad = $ctx['db_siakad'];

        $ujianList = $db->query("SELECT u.*, 
                                 (SELECT COUNT(*) FROM cbt_soal WHERE ujian_id = u.id) AS jumlah_soal,
                                 (SELECT COUNT(*) FROM cbt_peserta WHERE ujian_id = u.id) AS jumlah_peserta 
                                 FROM cbt_ujian u 
                                 ORDER BY u.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

        // Ambil daftar Mapel & Kelas Real
        $mapelList = self::getRealMapelList($db_siakad);
        $kelasList = self::getRealKelasList($db);

        $title = "Kelola Paket Ujian CBT - MAS MIFTAHUL HUDA";
        ob_start();
        include __DIR__ . '/../../resources/views/cbt/ujian.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Simpan / Perbarui Paket Ujian & Auto-Enroll Siswa Real
     */
    public static function saveUjian() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
            $kode_ujian = trim($_POST['kode_ujian'] ?? ('UJN-' . strtoupper(substr(uniqid(), -4))));
            $nama_ujian = trim($_POST['nama_ujian'] ?? 'Ujian Baru');
            $mapel_nama = trim($_POST['mapel_nama'] ?? 'Umum');
            $kelas_nama = trim($_POST['kelas_nama'] ?? 'Semua Kelas');
            $durasi_menit = !empty($_POST['durasi_menit']) ? (int)$_POST['durasi_menit'] : 90;
            $kkm = !empty($_POST['kkm']) ? (int)$_POST['kkm'] : 75;
            $status = !empty($_POST['status']) ? trim($_POST['status']) : 'Draft';
            $acak_soal = isset($_POST['acak_soal']) ? 1 : 0;
            $acak_opsi = isset($_POST['acak_opsi']) ? 1 : 0;
            $autoSync = isset($_POST['auto_sync_siswa']) ? 1 : 0;

            if ($id > 0) {
                $stmt = $db->prepare("UPDATE cbt_ujian 
                                      SET kode_ujian = ?, nama_ujian = ?, mapel_nama = ?, kelas_nama = ?, durasi_menit = ?, kkm = ?, status = ?, acak_soal = ?, acak_opsi = ? 
                                      WHERE id = ?");
                $stmt->execute([$kode_ujian, $nama_ujian, $mapel_nama, $kelas_nama, $durasi_menit, $kkm, $status, $acak_soal, $acak_opsi, $id]);
                $targetId = $id;
            } else {
                $stmt = $db->prepare("INSERT INTO cbt_ujian (kode_ujian, nama_ujian, mapel_nama, kelas_nama, durasi_menit, kkm, status, acak_soal, acak_opsi) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$kode_ujian, $nama_ujian, $mapel_nama, $kelas_nama, $durasi_menit, $kkm, $status, $acak_soal, $acak_opsi]);
                $targetId = (int)$db->lastInsertId();
            }

            // Daftarkan siswa real secara otomatis jika dicentang
            if ($autoSync && $targetId > 0) {
                self::syncPesertaFromRealSiswa($db, $targetId, $kelas_nama);
            }

            redirect('/cbt/ujian?saved=1');
            exit;
        }
        redirect('/cbt/ujian');
    }

    /**
     * Action Sinkronisasi Siswa Real secara manual
     */
    public static function syncPesertaAction() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ujianId = isset($_POST['ujian_id']) ? (int)$_POST['ujian_id'] : 0;
            if ($ujianId > 0) {
                $stmt = $db->prepare("SELECT kelas_nama FROM cbt_ujian WHERE id = ?");
                $stmt->execute([$ujianId]);
                $kelasNama = $stmt->fetchColumn() ?: '';

                $count = self::syncPesertaFromRealSiswa($db, $ujianId, $kelasNama);
                redirect("/cbt/ujian?synced={$count}");
                exit;
            }
        }
        redirect('/cbt/ujian');
    }

    /**
     * Hapus Paket Ujian beserta butir soal dan nilainya
     */
    public static function deleteUjian() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if ($id > 0) {
                $db->prepare("DELETE FROM cbt_soal WHERE ujian_id = ?")->execute([$id]);
                $db->prepare("DELETE FROM cbt_peserta WHERE ujian_id = ?")->execute([$id]);
                $db->prepare("DELETE FROM cbt_token WHERE ujian_id = ?")->execute([$id]);
                $db->prepare("DELETE FROM cbt_ujian WHERE id = ?")->execute([$id]);
            }
            redirect('/cbt/ujian?deleted=1');
            exit;
        }
        redirect('/cbt/ujian');
    }

    /**
     * Kelola Butir Soal per Paket Ujian
     */
    public static function soal() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $db_siakad = $ctx['db_siakad'];

        $ujian_id = isset($_GET['ujian_id']) ? (int)$_GET['ujian_id'] : 0;
        if ($ujian_id <= 0) {
            $ujian_id = (int)$db->query("SELECT id FROM cbt_ujian ORDER BY id ASC LIMIT 1")->fetchColumn();
            if ($ujian_id <= 0) {
                redirect('/cbt/ujian');
                exit;
            }
        }

        $stmtU = $db->prepare("SELECT * FROM cbt_ujian WHERE id = ?");
        $stmtU->execute([$ujian_id]);
        $ujianInfo = $stmtU->fetch(PDO::FETCH_ASSOC);

        if (!$ujianInfo) {
            redirect('/cbt/ujian');
            exit;
        }

        // Ambil daftar butir soal real
        $stmtS = $db->prepare("SELECT * FROM cbt_soal WHERE ujian_id = ? ORDER BY nomor_urut ASC, id ASC");
        $stmtS->execute([$ujian_id]);
        $soalList = $stmtS->fetchAll(PDO::FETCH_ASSOC);

        // Ambil daftar topik bank soal real dari siakad
        $bankTopiks = [];
        try {
            $stmtBT = $db_siakad->query("SELECT id, jenis, nama_topik FROM bs_topik ORDER BY created_at DESC");
            $bankTopiks = $stmtBT->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $title = "Kelola Soal: " . htmlspecialchars($ujianInfo['nama_ujian']) . " - MAS MIFTAHUL HUDA";
        ob_start();
        include __DIR__ . '/../../resources/views/cbt/soal.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Simpan / Tambah Butir Soal Real
     */
    public static function saveSoal() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ujian_id = isset($_POST['ujian_id']) ? (int)$_POST['ujian_id'] : 0;
            $pertanyaan = trim($_POST['pertanyaan'] ?? '');
            $opsi_a = trim($_POST['opsi_a'] ?? '');
            $opsi_b = trim($_POST['opsi_b'] ?? '');
            $opsi_c = trim($_POST['opsi_c'] ?? '');
            $opsi_d = trim($_POST['opsi_d'] ?? '');
            $opsi_e = trim($_POST['opsi_e'] ?? '');
            $kunci_jawaban = strtoupper(trim($_POST['kunci_jawaban'] ?? 'A'));
            $bobot = !empty($_POST['bobot']) ? (float)$_POST['bobot'] : 1.00;

            if ($ujian_id > 0 && !empty($pertanyaan)) {
                $stmtMax = $db->prepare("SELECT COALESCE(MAX(nomor_urut), 0) + 1 FROM cbt_soal WHERE ujian_id = ?");
                $stmtMax->execute([$ujian_id]);
                $nextNo = (int)$stmtMax->fetchColumn();

                $stmt = $db->prepare("INSERT INTO cbt_soal (ujian_id, nomor_urut, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, bobot) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $ujian_id, $nextNo, $pertanyaan, $opsi_a, $opsi_b, $opsi_c, $opsi_d, $opsi_e, $kunci_jawaban, $bobot
                ]);
            }
            redirect("/cbt/soal?ujian_id={$ujian_id}&saved=1");
            exit;
        }
        redirect('/cbt/ujian');
    }

    /**
     * Hapus Butir Soal
     */
    public static function deleteSoal() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $soal_id = isset($_POST['soal_id']) ? (int)$_POST['soal_id'] : 0;
            $ujian_id = isset($_POST['ujian_id']) ? (int)$_POST['ujian_id'] : 0;

            if ($soal_id > 0) {
                $db->prepare("DELETE FROM cbt_soal WHERE id = ?")->execute([$soal_id]);
            }
            redirect("/cbt/soal?ujian_id={$ujian_id}&deleted=1");
            exit;
        }
        redirect('/cbt/ujian');
    }

    /**
     * Impor Soal dari Bank Soal Pusat (bs_soal)
     */
    public static function importBankSoal() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $db_siakad = $ctx['db_siakad'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ujian_id = isset($_POST['ujian_id']) ? (int)$_POST['ujian_id'] : 0;
            $topik_id = isset($_POST['topik_id']) ? (int)$_POST['topik_id'] : 0;

            if ($ujian_id > 0 && $topik_id > 0) {
                try {
                    $stmtBS = $db_siakad->prepare("SELECT * FROM bs_soal WHERE topik_id = ?");
                    $stmtBS->execute([$topik_id]);
                    $bankItems = $stmtBS->fetchAll(PDO::FETCH_ASSOC);

                    $stmtMax = $db->prepare("SELECT COALESCE(MAX(nomor_urut), 0) FROM cbt_soal WHERE ujian_id = ?");
                    $stmtMax->execute([$ujian_id]);
                    $currNo = (int)$stmtMax->fetchColumn();

                    $stmtInsert = $db->prepare("INSERT INTO cbt_soal (ujian_id, nomor_urut, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, bobot) 
                                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                    foreach ($bankItems as $item) {
                        $currNo++;
                        $pertanyaan = $item['pertanyaan'] ?? '';
                        $kunci = 'A';
                        $opsiA = ''; $opsiB = ''; $opsiC = ''; $opsiD = ''; $opsiE = '';

                        $konten = !empty($item['konten_tambahan']) ? json_decode($item['konten_tambahan'], true) : [];
                        if (is_array($konten)) {
                            $kunci = strtoupper($konten['kunci'] ?? 'A');
                            if (isset($konten['opsi']) && is_array($konten['opsi'])) {
                                $opsiA = $konten['opsi']['A'] ?? '';
                                $opsiB = $konten['opsi']['B'] ?? '';
                                $opsiC = $konten['opsi']['C'] ?? '';
                                $opsiD = $konten['opsi']['D'] ?? '';
                                $opsiE = $konten['opsi']['E'] ?? '';
                            }
                        }

                        $stmtInsert->execute([
                            $ujian_id, $currNo, $pertanyaan, $opsiA, $opsiB, $opsiC, $opsiD, $opsiE, $kunci, (float)($item['skor_maksimal'] ?? 1.0)
                        ]);
                    }
                } catch (\Exception $e) {}
            }
            redirect("/cbt/soal?ujian_id={$ujian_id}&imported=1");
            exit;
        }
        redirect('/cbt/ujian');
    }

    /**
     * Manajemen Token Ujian Real
     */
    public static function token() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];

        $stmtTok = $db->query("SELECT * FROM cbt_token WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1");
        $tokenAktif = $stmtTok->fetch(PDO::FETCH_ASSOC) ?: null;

        $tokenHistory = $db->query("SELECT * FROM cbt_token ORDER BY id DESC LIMIT 25")->fetchAll(PDO::FETCH_ASSOC);

        $title = "Token Ujian CBT - MAS MIFTAHUL HUDA";
        ob_start();
        include __DIR__ . '/../../resources/views/cbt/token.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Generate / Rilis Token Baru (Real 6-char random token)
     */
    public static function generateToken() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db->exec("UPDATE cbt_token SET status = 'Expired' WHERE status = 'Aktif'");

            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
            $tokenStr = '';
            for ($i = 0; $i < 6; $i++) {
                $tokenStr .= $chars[rand(0, strlen($chars) - 1)];
            }

            $stmt = $db->prepare("INSERT INTO cbt_token (token, masa_berlaku_menit, status, created_at) VALUES (?, 120, 'Aktif', NOW())");
            $stmt->execute([$tokenStr]);

            redirect('/cbt/token?msg=generated&success=1');
            exit;
        }
        redirect('/cbt/token');
    }

    /**
     * Hentikan / Nonaktifkan Token
     */
    public static function expireToken() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tokenId = isset($_POST['token_id']) ? (int)$_POST['token_id'] : 0;
            if ($tokenId > 0) {
                $stmt = $db->prepare("UPDATE cbt_token SET status = 'Expired' WHERE id = ?");
                $stmt->execute([$tokenId]);
            } else {
                $db->exec("UPDATE cbt_token SET status = 'Expired' WHERE status = 'Aktif'");
            }
            redirect('/cbt/token?msg=expired&stopped=1');
            exit;
        }
        redirect('/cbt/token');
    }

    /**
     * Rekap Hasil Ujian Siswa Real
     */
    public static function hasil() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);
        self::syncJadwalWithUjian($db, $ctx['db_siakad']);

        $ujianList = $db->query("SELECT id, kode_ujian, nama_ujian, mapel_nama, kkm FROM cbt_ujian ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

        $selectedUjianId = isset($_GET['ujian_id']) ? (int)$_GET['ujian_id'] : 0;
        $selectedKelas = isset($_GET['kelas']) ? trim($_GET['kelas']) : '';
        $search = isset($_GET['q']) ? trim($_GET['q']) : '';

        // Daftar kelas real dari database kelas + kelas jauh
        $kelasList = [];
        try {
            $k1 = $db->query("SELECT DISTINCT nama_kelas FROM kelas WHERE nama_kelas IS NOT NULL AND nama_kelas != ''")->fetchAll(PDO::FETCH_COLUMN);
            $k2 = $db->query("SELECT DISTINCT kelas_nama FROM cbt_peserta_khusus WHERE kelas_nama IS NOT NULL AND kelas_nama != ''")->fetchAll(PDO::FETCH_COLUMN);
            $k3 = $db->query("SELECT DISTINCT kelas_nama FROM cbt_peserta WHERE kelas_nama IS NOT NULL AND kelas_nama != ''")->fetchAll(PDO::FETCH_COLUMN);
            $kelasList = array_unique(array_filter(array_merge($k1, $k2, $k3)));
            sort($kelasList);
        } catch (\Exception $e) {}

        // Query Peserta Real
        $sql = "SELECT p.*, u.nama_ujian, u.kkm, u.mapel_nama 
                FROM cbt_peserta p 
                LEFT JOIN cbt_ujian u ON u.id = p.ujian_id 
                WHERE 1=1";
        $params = [];

        if ($selectedUjianId > 0) {
            $sql .= " AND p.ujian_id = :ujian_id";
            $params[':ujian_id'] = $selectedUjianId;
        }

        if (!empty($selectedKelas)) {
            $sql .= " AND p.kelas_nama = :kelas";
            $params[':kelas'] = $selectedKelas;
        }

        if (!empty($search)) {
            $sql .= " AND (p.nama_siswa LIKE :q OR p.nis LIKE :q)";
            $params[':q'] = "%{$search}%";
        }

        $sql .= " ORDER BY (p.status_pengerjaan = 'Selesai') DESC, p.nilai_akhir DESC, p.nama_siswa ASC";
        $stmtP = $db->prepare($sql);
        $stmtP->execute($params);
        $pesertaList = $stmtP->fetchAll(PDO::FETCH_ASSOC);

        // KKM patokan
        $kkmUjian = 75;
        if ($selectedUjianId > 0) {
            foreach ($ujianList as $uj) {
                if ($uj['id'] == $selectedUjianId) {
                    $kkmUjian = (int)$uj['kkm'];
                    break;
                }
            }
        }

        // Hitung statistik nilai dari peserta yang sudah menyelesaikan ujian
        $totalSelesai = 0;
        $totalNilai = 0;
        $tuntasCount = 0;
        $jumlahRemedial = 0;

        foreach ($pesertaList as $p) {
            if ($p['status_pengerjaan'] === 'Selesai') {
                $totalSelesai++;
                $nilai = (float)$p['nilai_akhir'];
                $totalNilai += $nilai;
                if ($nilai >= $kkmUjian) {
                    $tuntasCount++;
                } else {
                    $jumlahRemedial++;
                }
            }
        }

        $rataRata = $totalSelesai > 0 ? ($totalNilai / $totalSelesai) : 0;
        $persenTuntas = $totalSelesai > 0 ? round(($tuntasCount / $totalSelesai) * 100) : 0;

        $title = "Hasil Ujian CBT - MAS MIFTAHUL HUDA";
        ob_start();
        include __DIR__ . '/../../resources/views/cbt/hasil.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Analisis Butir Soal Real (Tingkat Kesukaran, Daya Pembeda, Pengecoh, KR-20)
     */
    public static function analisis() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);
        self::syncJadwalWithUjian($db, $ctx['db_siakad']);

        $ujianList = $db->query("SELECT id, kode_ujian, nama_ujian, mapel_nama, kelas_nama, durasi_menit, kkm FROM cbt_ujian ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

        $selectedUjianId = isset($_GET['ujian_id']) ? (int)$_GET['ujian_id'] : 0;
        if ($selectedUjianId <= 0 && !empty($ujianList)) {
            $selectedUjianId = (int)$ujianList[0]['id'];
        }

        $currentUjian = null;
        foreach ($ujianList as $u) {
            if ($u['id'] == $selectedUjianId) {
                $currentUjian = $u;
                break;
            }
        }

        // Butir soal ujian
        $stmtSoal = $db->prepare("SELECT * FROM cbt_soal WHERE ujian_id = ? ORDER BY nomor_urut ASC, id ASC");
        $stmtSoal->execute([$selectedUjianId]);
        $soalRows = $stmtSoal->fetchAll(PDO::FETCH_ASSOC);

        // Peserta yang telah selesai mengerjakan
        $stmtPeserta = $db->prepare("SELECT id, nis, nama_siswa, nilai_akhir, jawaban_json FROM cbt_peserta WHERE ujian_id = ? AND status_pengerjaan = 'Selesai' ORDER BY nilai_akhir DESC");
        $stmtPeserta->execute([$selectedUjianId]);
        $pesertaRows = $stmtPeserta->fetchAll(PDO::FETCH_ASSOC);

        $totalPesertaUjian = count($pesertaRows);

        $scores = [];
        $totalNilai = 0;
        $nilaiMaks = 0;
        $nilaiMin = $totalPesertaUjian > 0 ? 100 : 0;

        foreach ($pesertaRows as $p) {
            $sc = (float)$p['nilai_akhir'];
            $scores[] = $sc;
            $totalNilai += $sc;
            if ($sc > $nilaiMaks) $nilaiMaks = $sc;
            if ($sc < $nilaiMin) $nilaiMin = $sc;
        }

        $rataRata = $totalPesertaUjian > 0 ? ($totalNilai / $totalPesertaUjian) : 0;

        $variance = 0;
        if ($totalPesertaUjian > 0) {
            $sumSqDiff = 0;
            foreach ($scores as $s) {
                $sumSqDiff += pow($s - $rataRata, 2);
            }
            $variance = $sumSqDiff / $totalPesertaUjian;
        }
        $standarDeviasi = sqrt($variance);

        // Kelompok atas dan bawah
        $kGroup = ($totalPesertaUjian <= 6) ? max(1, (int)round($totalPesertaUjian / 2)) : max(1, (int)round(0.27 * $totalPesertaUjian));
        $kelompokAtas = array_slice($pesertaRows, 0, $kGroup);
        $kelompokBawah = array_slice($pesertaRows, max(0, $totalPesertaUjian - $kGroup), $kGroup);

        $analisisSoal = [];
        $sumPQ = 0;

        foreach ($soalRows as $s) {
            $soalId = (string)$s['id'];
            $soalNo = (string)$s['nomor_urut'];
            $kunci = strtoupper(trim($s['kunci_jawaban']));

            $jmlBenar = 0;
            $jmlSalah = 0;
            $sebaran = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0];

            foreach ($pesertaRows as $p) {
                $ansData = !empty($p['jawaban_json']) ? json_decode($p['jawaban_json'], true) : [];
                $ans = strtoupper(trim($ansData[$soalId] ?? ($ansData[$soalNo] ?? '')));
                if (isset($sebaran[$ans])) {
                    $sebaran[$ans]++;
                }
                if ($ans === $kunci) {
                    $jmlBenar++;
                } else {
                    $jmlSalah++;
                }
            }

            $pVal = $totalPesertaUjian > 0 ? ($jmlBenar / $totalPesertaUjian) : 0;
            $qVal = 1 - $pVal;
            $sumPQ += ($pVal * $qVal);

            $benarAtas = 0;
            foreach ($kelompokAtas as $pa) {
                $ansData = !empty($pa['jawaban_json']) ? json_decode($pa['jawaban_json'], true) : [];
                $ans = strtoupper(trim($ansData[$soalId] ?? ($ansData[$soalNo] ?? '')));
                if ($ans === $kunci) $benarAtas++;
            }

            $benarBawah = 0;
            foreach ($kelompokBawah as $pb) {
                $ansData = !empty($pb['jawaban_json']) ? json_decode($pb['jawaban_json'], true) : [];
                $ans = strtoupper(trim($ansData[$soalId] ?? ($ansData[$soalNo] ?? '')));
                if ($ans === $kunci) $benarBawah++;
            }

            $dVal = $kGroup > 0 ? (($benarAtas - $benarBawah) / $kGroup) : 0;

            $hasDeadDistractor = false;
            foreach (['A', 'B', 'C', 'D', 'E'] as $opt) {
                if ($opt !== $kunci && !empty($s['opsi_' . strtolower($opt)])) {
                    if ($sebaran[$opt] == 0 && $totalPesertaUjian >= 5) {
                        $hasDeadDistractor = true;
                    }
                }
            }

            if ($totalPesertaUjian == 0) {
                $rekomendasi = 'Menunggu Ujian';
            } elseif ($dVal < 0.20 || $dVal < 0) {
                $rekomendasi = 'Dibuang';
            } elseif ($dVal < 0.30) {
                $rekomendasi = 'Revisi Soal';
            } elseif ($hasDeadDistractor) {
                $rekomendasi = 'Revisi Pengecoh';
            } else {
                $rekomendasi = 'Diterima Baik';
            }

            $analisisSoal[] = array_merge($s, [
                'jumlah_benar' => $jmlBenar,
                'jumlah_salah' => $jmlSalah,
                'tingkat_kesukaran' => $pVal,
                'daya_beda' => $dVal,
                'sebaran' => $sebaran,
                'rekomendasi' => $rekomendasi
            ]);
        }

        $jmlItem = count($soalRows);
        $reliabilitasKR20 = 0.85;
        if ($jmlItem > 1 && $variance > 0) {
            $kr = ($jmlItem / ($jmlItem - 1)) * (1 - ($sumPQ / $variance));
            $reliabilitasKR20 = max(0, min(1, $kr));
        }

        $title = "Analisis Butir Soal CBT - MAS MIFTAHUL HUDA";
        ob_start();
        include __DIR__ . '/../../resources/views/cbt/analisis.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Ruang Ujian CBT (CBT Player untuk Siswa & Pengawas)
     */
    public static function kerjakan() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];

        $ujian_id = isset($_GET['ujian_id']) ? (int)$_GET['ujian_id'] : 0;
        if ($ujian_id <= 0) {
            redirect('/cbt/ujian');
            exit;
        }

        $stmtU = $db->prepare("SELECT * FROM cbt_ujian WHERE id = ?");
        $stmtU->execute([$ujian_id]);
        $ujianInfo = $stmtU->fetch(PDO::FETCH_ASSOC);

        if (!$ujianInfo) {
            redirect('/cbt/ujian');
            exit;
        }

        // Ambil butir soal (Lengkap dengan tipe_soal dan konten_tambahan untuk 5 model CBT)
        $stmtS = $db->prepare("SELECT id, nomor_urut, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, bobot, tipe_soal, konten_tambahan FROM cbt_soal WHERE ujian_id = ? ORDER BY nomor_urut ASC, id ASC");
        $stmtS->execute([$ujian_id]);
        $soalList = $stmtS->fetchAll(PDO::FETCH_ASSOC);

        if (empty($soalList)) {
            // Coba auto-import dari bs_soal jika topik_id atau mapel_nama cocok di Bank Soal
            try {
                $db_siakad = $ctx['db_siakad'] ?? $db;
                $topikIdExam = !empty($ujianInfo['topik_id']) ? (int)$ujianInfo['topik_id'] : 0;
                $stmtBS = $db_siakad->prepare("SELECT * FROM bs_soal WHERE (topik_id = ? AND ? > 0) OR mapel_nama = ? ORDER BY nomor_urut ASC LIMIT 50");
                $stmtBS->execute([$topikIdExam, $topikIdExam, $ujianInfo['mapel_nama']]);
                $bsItems = $stmtBS->fetchAll(PDO::FETCH_ASSOC);
                if (empty($bsItems) && $db !== $db_siakad) {
                    $stmtBSCore = $db->prepare("SELECT * FROM bs_soal WHERE (topik_id = ? AND ? > 0) OR mapel_nama = ? ORDER BY nomor_urut ASC LIMIT 50");
                    $stmtBSCore->execute([$topikIdExam, $topikIdExam, $ujianInfo['mapel_nama']]);
                    $bsItems = $stmtBSCore->fetchAll(PDO::FETCH_ASSOC);
                }

                if (!empty($bsItems)) {
                    $stmtInsS = $db->prepare("INSERT INTO cbt_soal (ujian_id, nomor_urut, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, bobot) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $no = 1;
                    foreach ($bsItems as $bItem) {
                        $kunci = 'A'; $opA = ''; $opB = ''; $opC = ''; $opD = ''; $opE = '';
                        $kData = !empty($bItem['konten_tambahan']) ? json_decode($bItem['konten_tambahan'], true) : [];
                        if (is_array($kData)) {
                            $kunci = strtoupper($kData['kunci'] ?? 'A');
                            if (isset($kData['opsi']) && is_array($kData['opsi'])) {
                                $opA = $kData['opsi']['A'] ?? '';
                                $opB = $kData['opsi']['B'] ?? '';
                                $opC = $kData['opsi']['C'] ?? '';
                                $opD = $kData['opsi']['D'] ?? '';
                                $opE = $kData['opsi']['E'] ?? '';
                            }
                        }
                        $stmtInsS->execute([$ujian_id, $no++, $bItem['pertanyaan'], $opA, $opB, $opC, $opD, $opE, $kunci, 1.0]);
                    }
                    $stmtS->execute([$ujian_id]);
                    $soalList = $stmtS->fetchAll(PDO::FETCH_ASSOC);
                }
            } catch (\Exception $e) {}
        }

        if (empty($soalList)) {
            echo "<script>alert('Paket ujian ini belum memiliki butir soal! Tambahkan butir soal terlebih dahulu.'); window.location.href='" . base_url('cbt/soal?ujian_id=' . $ujian_id) . "';</script>";
            exit;
        }

        // Cek identitas peserta (siswa login via web atau APK Siswa)
        $userId = (int)($_SESSION['user_id'] ?? 0);
        $roleId = (int)($_SESSION['role_id'] ?? 0);
        $siswaSessionId = (int)($_SESSION['siswa_id'] ?? 0);
        $isCbtOnly = !empty($_SESSION['is_cbt_only']);

        $pesertaInfo = null;

        if ($isCbtOnly || $roleId === 7) {
            // Siswa Khusus / Kelas Jauh (CBT Only)
            $stmtK = $db->prepare("SELECT * FROM cbt_peserta_khusus WHERE id = ? LIMIT 1");
            $stmtK->execute([$siswaSessionId]);
            $kSiswa = $stmtK->fetch(PDO::FETCH_ASSOC);

            if ($kSiswa) {
                $noPeserta = $kSiswa['no_peserta'] ?: ($kSiswa['nis'] ?: ('KJ-' . $kSiswa['id']));
                $stmtP = $db->prepare("SELECT * FROM cbt_peserta WHERE ujian_id = ? AND nis = ? LIMIT 1");
                $stmtP->execute([$ujian_id, $noPeserta]);
                $pesertaInfo = $stmtP->fetch(PDO::FETCH_ASSOC);

                if (!$pesertaInfo) {
                    $nowStr = date('Y-m-d H:i:s');
                    $stmtIns = $db->prepare("INSERT INTO cbt_peserta (ujian_id, siswa_id, nis, nama_siswa, kelas_nama, waktu_mulai, status_pengerjaan) 
                                             VALUES (?, NULL, ?, ?, ?, NOW(), 'Mengerjakan')");
                    $stmtIns->execute([$ujian_id, $noPeserta, $kSiswa['nama_siswa'], $kSiswa['kelas_nama']]);
                    $pesertaInfo = [
                        'id' => (int)$db->lastInsertId(),
                        'ujian_id' => $ujian_id,
                        'siswa_id' => null,
                        'nis' => $noPeserta,
                        'nama_siswa' => $kSiswa['nama_siswa'],
                        'kelas_nama' => $kSiswa['kelas_nama'],
                        'waktu_mulai' => $nowStr,
                        'status_pengerjaan' => 'Mengerjakan',
                        'jawaban_json' => null
                    ];
                } elseif ($pesertaInfo['status_pengerjaan'] === 'Selesai') {
                    redirect("/cbt/kerjakan/selesai?peserta_id={$pesertaInfo['id']}&ujian_id={$ujian_id}");
                    exit;
                }
            }
        } elseif ($roleId === 3 || $siswaSessionId > 0) {
            // Role Siswa: Cari data siswa di database core
            $stmtSiswa = $db->prepare("SELECT s.id, s.nis, s.nama, COALESCE(k.nama_kelas, 'Umum') as kelas_nama 
                                       FROM siswa s 
                                       LEFT JOIN kelas k ON s.kelas_id = k.id 
                                       WHERE (s.user_id = ? AND ? > 0) OR (s.id = ? AND ? > 0) LIMIT 1");
            $stmtSiswa->execute([$userId, $userId, $siswaSessionId, $siswaSessionId]);
            $realSiswa = $stmtSiswa->fetch(PDO::FETCH_ASSOC);

            if ($realSiswa) {
                // Cari atau buat record di cbt_peserta
                $stmtP = $db->prepare("SELECT * FROM cbt_peserta WHERE ujian_id = ? AND siswa_id = ? LIMIT 1");
                $stmtP->execute([$ujian_id, $realSiswa['id']]);
                $pesertaInfo = $stmtP->fetch(PDO::FETCH_ASSOC);

                if (!$pesertaInfo) {
                    $nowStr = date('Y-m-d H:i:s');
                    $stmtIns = $db->prepare("INSERT INTO cbt_peserta (ujian_id, siswa_id, nis, nama_siswa, kelas_nama, waktu_mulai, status_pengerjaan) 
                                             VALUES (?, ?, ?, ?, ?, NOW(), 'Mengerjakan')");
                    $stmtIns->execute([$ujian_id, $realSiswa['id'], $realSiswa['nis'], $realSiswa['nama'], $realSiswa['kelas_nama']]);
                    $pesertaInfo = [
                        'id' => (int)$db->lastInsertId(),
                        'ujian_id' => $ujian_id,
                        'siswa_id' => $realSiswa['id'],
                        'nis' => $realSiswa['nis'],
                        'nama_siswa' => $realSiswa['nama'],
                        'kelas_nama' => $realSiswa['kelas_nama'],
                        'waktu_mulai' => $nowStr,
                        'status_pengerjaan' => 'Mengerjakan',
                        'jawaban_json' => null
                    ];
                } elseif ($pesertaInfo['status_pengerjaan'] === 'Selesai') {
                    redirect("/cbt/kerjakan/selesai?peserta_id={$pesertaInfo['id']}&ujian_id={$ujian_id}");
                    exit;
                }
            }
        }

        // Jika Pengawas/Guru melakukan uji coba CBT
        if (!$pesertaInfo) {
            $pesertaInfo = [
                'id' => 0,
                'ujian_id' => $ujian_id,
                'siswa_id' => null,
                'nis' => 'GURU-TRIAL',
                'nama_siswa' => ($_SESSION['nama'] ?? 'Pengawas / Guru (Simulasi)'),
                'kelas_nama' => ($ujianInfo['kelas_nama'] ?: 'Simulasi'),
                'waktu_mulai' => date('Y-m-d H:i:s'),
                'status_pengerjaan' => 'Mengerjakan',
                'jawaban_json' => null
            ];
        }

        // Ambil data jawaban yang telah tersimpan sebelumnya (untuk fitur Lanjutkan Ujian)
        $savedData = [];
        if (!empty($pesertaInfo['jawaban_json'])) {
            $savedData = json_decode($pesertaInfo['jawaban_json'], true) ?: [];
        }

        // Hitung sisa waktu pengerjaan secara akurat (melanjutkan dari waktu_mulai)
        $durasiMenit = (int)($ujianInfo['durasi_menit'] ?? 90);
        $totalDetik = $durasiMenit * 60;
        $durasiDetik = $totalDetik;
        if (!empty($pesertaInfo['waktu_mulai'])) {
            $wMulai = strtotime($pesertaInfo['waktu_mulai']);
            $now = time();
            $elapsed = $now - $wMulai;
            if ($elapsed < 0) {
                // Toleransi jika ada selisih jam server atau clock skew
                $elapsed = 0;
            }
            if ($elapsed >= 0 && $elapsed < $totalDetik) {
                $durasiDetik = $totalDetik - $elapsed;
            } elseif ($elapsed >= $totalDetik) {
                // Beri toleransi darurat 300 detik (5 menit) jika masih berstatus Mengerjakan
                $durasiDetik = 300;
            }
        }

        $cbtSettings = self::getAllPengaturan($db);

        include __DIR__ . '/../../resources/views/cbt/kerjakan.php';
    }

    /**
     * Catat Pelanggaran Anti-Cheat Siswa (AJAX)
     */
    public static function ajaxCatatPelanggaran() {
        header('Content-Type: application/json; charset=utf-8');
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];

        $peserta_id = isset($_POST['peserta_id']) ? (int)$_POST['peserta_id'] : 0;
        $status = $_POST['status'] ?? ''; // 'Terkunci' jika melampaui batas

        if ($peserta_id > 0 && $status === 'Terkunci') {
            $stmt = $db->prepare("UPDATE cbt_peserta SET status_pengerjaan = 'Terkunci' WHERE id = ?");
            $stmt->execute([$peserta_id]);
        }
        echo json_encode(['status' => 'success']);
        exit;
    }

    /**
     * Auto-Save Jawaban Siswa secara Real-Time (AJAX)
     */
    public static function ajaxSimpanJawaban() {
        header('Content-Type: application/json; charset=utf-8');
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];

        $ujian_id = isset($_POST['ujian_id']) ? (int)$_POST['ujian_id'] : 0;
        $peserta_id = isset($_POST['peserta_id']) ? (int)$_POST['peserta_id'] : 0;
        $payloadRaw = $_POST['jawaban_payload'] ?? '';

        if ($peserta_id <= 0 || empty($payloadRaw)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
            exit;
        }

        try {
            $stmt = $db->prepare("UPDATE cbt_peserta SET jawaban_json = ?, status_pengerjaan = 'Mengerjakan' WHERE id = ? AND ujian_id = ?");
            $stmt->execute([$payloadRaw, $peserta_id, $ujian_id]);
            echo json_encode(['status' => 'success', 'saved_at' => date('H:i:s')]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Submit Jawaban Ujian Siswa & Hitung Nilai Real Otomatis
     */
    public static function submitKerjakan() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ujian_id = isset($_POST['ujian_id']) ? (int)$_POST['ujian_id'] : 0;
            $peserta_id = isset($_POST['peserta_id']) ? (int)$_POST['peserta_id'] : 0;
            $rawPayload = $_POST['jawaban_payload'] ?? '{}';
            $answers = json_decode($rawPayload, true) ?: [];

            // Ambil seluruh butir soal dengan tipe dan kuncinya
            $stmtSoal = $db->prepare("SELECT id, nomor_urut, kunci_jawaban, bobot, tipe_soal, konten_tambahan FROM cbt_soal WHERE ujian_id = ?");
            $stmtSoal->execute([$ujian_id]);
            $soals = $stmtSoal->fetchAll(PDO::FETCH_ASSOC);

            $totalBobot = 0;
            $poinPerolehan = 0;
            $jumlahBenar = 0;
            $jumlahSalah = 0;

            foreach ($soals as $s) {
                $bobot = (float)($s['bobot'] > 0 ? $s['bobot'] : 1.0);
                $totalBobot += $bobot;

                $userAnsRaw = $answers[$s['id']] ?? '';
                if (is_array($userAnsRaw)) {
                    $uTemp = $userAnsRaw;
                    sort($uTemp);
                    $userAns = strtoupper(trim(implode(', ', $uTemp)));
                } else {
                    $userAns = strtoupper(trim((string)$userAnsRaw));
                }

                $kunciRaw = (string)$s['kunci_jawaban'];
                $kunci = strtoupper(trim($kunciRaw));
                $isMatch = false;

                // 1. Evaluasi Tipe JSON (Tabel Benar/Salah & Menjodohkan)
                $kDec = json_decode($kunciRaw, true);
                if (is_array($kDec)) {
                    if (is_array($userAnsRaw) && !empty($userAnsRaw)) {
                        if (isset($kDec[0]['respon'])) {
                            // Menjodohkan
                            $expected = array_map(function($v) { return strtoupper(trim((string)$v)); }, array_column($kDec, 'respon'));
                            $actual = array_map(function($v) { return strtoupper(trim((string)$v)); }, array_values($userAnsRaw));
                            if ($expected == $actual) $isMatch = true;
                        } else {
                            // Benar / Salah
                            $expected = array_map(function($v) { return strtoupper(trim((string)$v)); }, $kDec);
                            $actual = array_map(function($v) { return strtoupper(trim((string)$v)); }, array_values($userAnsRaw));
                            if ($expected == $actual) $isMatch = true;
                        }
                    }
                } elseif (!empty($userAns)) {
                    // 2. Evaluasi PG Single Choice
                    if ($userAns === $kunci) {
                        $isMatch = true;
                    } 
                    // 3. Evaluasi PG Kompleks (A, B, C)
                    elseif (strpos($kunci, ',') !== false) {
                        $uParts = array_map('trim', explode(',', $userAns));
                        $kParts = array_map('trim', explode(',', $kunci));
                        sort($uParts);
                        sort($kParts);
                        if ($uParts === $kParts) {
                            $isMatch = true;
                        }
                    } 
                    // 4. Evaluasi Isian Singkat (Numeric & Text tolerance)
                    elseif (is_numeric($kunci)) {
                        $numUser = preg_replace('/[^0-9]/', '', $userAns);
                        if ($numUser === $kunci) {
                            $isMatch = true;
                        }
                    }
                }

                if ($isMatch) {
                    $poinPerolehan += $bobot;
                    $jumlahBenar++;
                } else {
                    $jumlahSalah++;
                }
            }

            $nilaiAkhir = ($totalBobot > 0) ? round(($poinPerolehan / $totalBobot) * 100, 1) : 0;

            // Simpan ke database jika peserta terdaftar
            if ($peserta_id > 0) {
                $payloadToSave = [
                    'answers' => $answers,
                    'answers_by_index' => $answers,
                    'submitted_at' => date('Y-m-d H:i:s')
                ];
                $stmtUp = $db->prepare("UPDATE cbt_peserta 
                                        SET waktu_selesai = NOW(), status_pengerjaan = 'Selesai', 
                                            jumlah_benar = ?, jumlah_salah = ?, nilai_akhir = ?, jawaban_json = ? 
                                        WHERE id = ?");
                $stmtUp->execute([$jumlahBenar, $jumlahSalah, $nilaiAkhir, json_encode($payloadToSave), $peserta_id]);
            } else {
                // Untuk sesi trial pengawas/guru, simpan sebagai sesi percobaan
                $payloadToSave = [
                    'answers' => $answers,
                    'answers_by_index' => $answers,
                    'submitted_at' => date('Y-m-d H:i:s')
                ];
                $namaPengawas = $_SESSION['nama'] ?? 'Guru (Trial)';
                $stmtIns = $db->prepare("INSERT INTO cbt_peserta (ujian_id, siswa_id, nis, nama_siswa, kelas_nama, waktu_mulai, waktu_selesai, status_pengerjaan, jumlah_benar, jumlah_salah, nilai_akhir, jawaban_json) 
                                         VALUES (?, NULL, 'TRIAL', ?, 'Simulasi', NOW() - INTERVAL 15 MINUTE, NOW(), 'Selesai', ?, ?, ?, ?)");
                $stmtIns->execute([$ujian_id, $namaPengawas, $jumlahBenar, $jumlahSalah, $nilaiAkhir, json_encode($payloadToSave)]);
                $peserta_id = (int)$db->lastInsertId();
            }

            redirect("/cbt/kerjakan/selesai?peserta_id={$peserta_id}&ujian_id={$ujian_id}");
            exit;
        }
        redirect('/cbt/dashboard');
    }

    /**
     * Halaman Konfirmasi Selesai Ujian & Tampilan Skor
     */
    public static function selesaiKerjakan() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];

        $peserta_id = isset($_GET['peserta_id']) ? (int)$_GET['peserta_id'] : 0;
        $ujian_id = isset($_GET['ujian_id']) ? (int)$_GET['ujian_id'] : 0;

        $stmtP = $db->prepare("SELECT * FROM cbt_peserta WHERE id = ?");
        $stmtP->execute([$peserta_id]);
        $pesertaInfo = $stmtP->fetch(PDO::FETCH_ASSOC);

        $stmtU = $db->prepare("SELECT * FROM cbt_ujian WHERE id = ?");
        $stmtU->execute([$ujian_id]);
        $ujianInfo = $stmtU->fetch(PDO::FETCH_ASSOC);

        if (!$pesertaInfo || !$ujianInfo) {
            redirect('/cbt/dashboard');
            exit;
        }

        include __DIR__ . '/../../resources/views/cbt/selesai.php';
    }

    /**
     * Dapatkan semua pengaturan CBT sebagai key-value map
     */
    public static function getAllPengaturan($db = null) {
        if (!$db) {
            $db = Database::connect('core');
        }
        self::ensureTables($db);
        try {
            $stmt = $db->query("SELECT setting_key, setting_value FROM cbt_pengaturan");
            return $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Halaman Pengaturan CBT & Keamanan Anti-Cheat
     */
    public static function pengaturan() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        // Ambil semua pengaturan
        $stmt = $db->query("SELECT setting_key, setting_value, kategori, keterangan FROM cbt_pengaturan ORDER BY id ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $settings = [];
        foreach ($rows as $r) {
            $settings[$r['setting_key']] = $r['setting_value'];
        }

        // Statistik ringkas keamanan & pengerjaan peserta saat ini
        $totalPeserta = (int)$db->query("SELECT COUNT(*) FROM cbt_peserta")->fetchColumn();
        $pesertaMengerjakan = (int)$db->query("SELECT COUNT(*) FROM cbt_peserta WHERE status_pengerjaan = 'Sedang Mengerjakan'")->fetchColumn();
        $pesertaSelesai = (int)$db->query("SELECT COUNT(*) FROM cbt_peserta WHERE status_pengerjaan = 'Selesai'")->fetchColumn();
        $pesertaTerkunci = (int)$db->query("SELECT COUNT(*) FROM cbt_peserta WHERE status_pengerjaan IN ('Terkunci', 'Pelanggaran')")->fetchColumn();

        // Daftar peserta yang terblokir / terkunci (jika ada)
        $stmtTerkunci = $db->query("SELECT p.*, u.nama_ujian, u.mapel_nama 
                                   FROM cbt_peserta p 
                                   LEFT JOIN cbt_ujian u ON p.ujian_id = u.id 
                                   WHERE p.status_pengerjaan IN ('Terkunci', 'Pelanggaran') 
                                   ORDER BY p.id DESC LIMIT 50");
        $listTerkunci = $stmtTerkunci->fetchAll(PDO::FETCH_ASSOC);

        // Daftar peserta yang telah selesai / auto-submit untuk pemulihan cepat
        $stmtSelesai = $db->query("SELECT p.*, u.nama_ujian, u.mapel_nama 
                                   FROM cbt_peserta p 
                                   LEFT JOIN cbt_ujian u ON p.ujian_id = u.id 
                                   WHERE p.status_pengerjaan = 'Selesai' 
                                   ORDER BY p.id DESC LIMIT 30");
        $listSelesai = $stmtSelesai->fetchAll(PDO::FETCH_ASSOC);

        $title = "Pengaturan & Keamanan CBT - MAS MIFTAHUL HUDA";
        $active_menu = 'pengaturan';

        ob_start();
        include __DIR__ . '/../../resources/views/cbt/pengaturan.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Simpan Pengaturan CBT & Keamanan
     */
    public static function savePengaturan() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $submitted = $_POST['settings'] ?? [];
            if (!empty($submitted) && is_array($submitted)) {
                $stmtUp = $db->prepare("INSERT INTO cbt_pengaturan (setting_key, setting_value) 
                                        VALUES (:k, :v) 
                                        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()");
                foreach ($submitted as $k => $v) {
                    $stmtUp->execute([
                        ':k' => trim((string)$k),
                        ':v' => trim((string)$v)
                    ]);
                }
            }
            redirect('/cbt/pengaturan?saved=1');
            exit;
        }
        redirect('/cbt/pengaturan');
    }

    /**
     * Buka Kunci Siswa yang Terblokir / Mengalami Pelanggaran Sesi
     */
    public static function resetSiswaTerkunci() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $peserta_id = isset($_POST['peserta_id']) ? (int)$_POST['peserta_id'] : 0;
            if ($peserta_id > 0) {
                $stmt = $db->prepare("UPDATE cbt_peserta 
                                        SET status_pengerjaan = 'Sedang Mengerjakan', 
                                            waktu_selesai = NULL, 
                                            waktu_mulai = NOW(), 
                                            reset_count = COALESCE(reset_count, 0) + 1, 
                                            violation_count = 0 
                                        WHERE id = ?");
                $stmt->execute([$peserta_id]);
            } else {
                $db->exec("UPDATE cbt_peserta 
                            SET status_pengerjaan = 'Sedang Mengerjakan', 
                                waktu_selesai = NULL, 
                                waktu_mulai = NOW(), 
                                reset_count = COALESCE(reset_count, 0) + 1, 
                                violation_count = 0 
                            WHERE status_pengerjaan IN ('Terkunci', 'Pelanggaran')");
            }
            redirect('/cbt/pengaturan?unlocked=1');
            exit;
        }
        redirect('/cbt/pengaturan');
    }

    /**
     * Buka Ujian Kembali / Izinkan Siswa Melanjutkan atau Mengulang Ujian
     * Digunakan ketika siswa ter-auto-submit karena 3x pelanggaran keluar tab, salah kirim, atau kendala jaringan.
     */
    public static function bukaUjianKembali() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $peserta_id = isset($_POST['peserta_id']) ? (int)$_POST['peserta_id'] : 0;
            $mode = $_POST['mode'] ?? 'lanjutkan'; // 'lanjutkan' (simpan jawaban lama) atau 'ulang' (reset dari 0)

            if ($peserta_id > 0) {
                if ($mode === 'ulang') {
                    $stmt = $db->prepare("UPDATE cbt_peserta 
                                            SET status_pengerjaan = 'Mengerjakan', 
                                                waktu_selesai = NULL, 
                                                waktu_mulai = NOW(), 
                                                nilai_akhir = 0.00, 
                                                jumlah_benar = 0, 
                                                jumlah_salah = 0, 
                                                jawaban_json = NULL,
                                                reset_count = COALESCE(reset_count, 0) + 1,
                                                violation_count = 0
                                            WHERE id = ?");
                    $stmt->execute([$peserta_id]);
                } else {
                    // Default: Lanjutkan ujian (mempertahankan jawaban yang sudah tersimpan)
                    $stmt = $db->prepare("UPDATE cbt_peserta 
                                            SET status_pengerjaan = 'Mengerjakan', 
                                                waktu_selesai = NULL, 
                                                waktu_mulai = NOW(),
                                                reset_count = COALESCE(reset_count, 0) + 1,
                                                violation_count = 0
                                            WHERE id = ?");
                    $stmt->execute([$peserta_id]);
                }
            }

            $ref = $_SERVER['HTTP_REFERER'] ?? '/cbt/hasil';
            $parsedPath = parse_url($ref, PHP_URL_PATH) ?: '/cbt/hasil';
            $parsedQuery = parse_url($ref, PHP_URL_QUERY) ?: '';
            parse_str($parsedQuery, $queryParams);
            $queryParams['reopened'] = '1';
            $cleanRedirect = $parsedPath . '?' . http_build_query($queryParams);
            redirect($cleanRedirect);
            exit;
        }
        redirect('/cbt/hasil');
    }

    /**
     * Reset Seluruh Pengaturan CBT ke Standar Pabrik
     */
    public static function resetDefaultPengaturan() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db->exec("TRUNCATE TABLE cbt_pengaturan");
            self::ensureTables($db);
            redirect('/cbt/pengaturan?reset=1');
            exit;
        }
        redirect('/cbt/pengaturan');
    }

    /**
     * Manajemen Peserta CBT Kelas Jauh (Terisolasi dari SIAKAD)
     */
    public static function pesertaKhusus() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        $selectedTingkat = trim($_GET['tingkat'] ?? '');
        $search = trim($_GET['q'] ?? '');

        $sql = "SELECT * FROM cbt_peserta_khusus WHERE 1=1";
        $params = [];

        if ($selectedTingkat !== '') {
            $sql .= " AND tingkat = ?";
            $params[] = $selectedTingkat;
        }

        if ($search !== '') {
            $sql .= " AND (nama_siswa LIKE ? OR no_peserta LIKE ? OR username LIKE ? OR nis LIKE ?)";
            $wildcard = "%{$search}%";
            $params[] = $wildcard;
            $params[] = $wildcard;
            $params[] = $wildcard;
            $params[] = $wildcard;
        }

        $sql .= " ORDER BY tingkat ASC, nama_siswa ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $pesertaList = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Hitung statistik tingkat
        $countX = (int)$db->query("SELECT COUNT(*) FROM cbt_peserta_khusus WHERE tingkat = 'Kelas X'")->fetchColumn();
        $countXI = (int)$db->query("SELECT COUNT(*) FROM cbt_peserta_khusus WHERE tingkat = 'Kelas XI'")->fetchColumn();
        $countXII = (int)$db->query("SELECT COUNT(*) FROM cbt_peserta_khusus WHERE tingkat = 'Kelas XII'")->fetchColumn();

        $title = "Peserta CBT Kelas Jauh - MAS MIFTAHUL HUDA";
        $active_menu = 'peserta_khusus';

        ob_start();
        include __DIR__ . '/../../resources/views/cbt/peserta_khusus.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Simpan / Perbarui Peserta CBT Kelas Jauh
     */
    public static function savePesertaKhusus() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $nama_siswa = trim($_POST['nama_siswa'] ?? '');
            $no_peserta = trim($_POST['no_peserta'] ?? '');
            $nis = trim($_POST['nis'] ?? '') ?: null;
            $tingkat = trim($_POST['tingkat'] ?? 'Kelas X');
            $kelas_nama = trim($_POST['kelas_nama'] ?? 'X - Kelas Jauh');
            $username = trim($_POST['username'] ?? '');
            $password_plain = trim($_POST['password_plain'] ?? '123456');
            $lokasi_cabang = trim($_POST['lokasi_cabang'] ?? 'Kelas Jauh');
            $status = trim($_POST['status'] ?? 'Aktif');

            if (empty($username)) {
                $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $no_peserta));
            }
            $password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

            if ($id > 0) {
                $stmt = $db->prepare("UPDATE cbt_peserta_khusus 
                    SET no_peserta = ?, nis = ?, nama_siswa = ?, tingkat = ?, kelas_nama = ?, username = ?, password_plain = ?, password_hash = ?, lokasi_cabang = ?, status = ?
                    WHERE id = ?");
                $stmt->execute([$no_peserta, $nis, $nama_siswa, $tingkat, $kelas_nama, $username, $password_plain, $password_hash, $lokasi_cabang, $status, $id]);
            } else {
                $stmt = $db->prepare("INSERT INTO cbt_peserta_khusus 
                    (no_peserta, nis, nama_siswa, tingkat, kelas_nama, username, password_plain, password_hash, lokasi_cabang, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$no_peserta, $nis, $nama_siswa, $tingkat, $kelas_nama, $username, $password_plain, $password_hash, $lokasi_cabang, $status]);
            }

            redirect('/cbt/peserta_khusus?msg=saved');
            exit;
        }
        redirect('/cbt/peserta_khusus');
    }

    /**
     * Hapus Peserta CBT Kelas Jauh
     */
    public static function deletePesertaKhusus() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id > 0) {
                $stmt = $db->prepare("DELETE FROM cbt_peserta_khusus WHERE id = ?");
                $stmt->execute([$id]);
            }
            redirect('/cbt/peserta_khusus?msg=deleted');
            exit;
        }
        redirect('/cbt/peserta_khusus');
    }

    /**
     * Import Massal Peserta Kelas Jauh dari CSV
     */
    public static function importPesertaKhusus() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_csv']) && is_uploaded_file($_FILES['file_csv']['tmp_name'])) {
            $handle = fopen($_FILES['file_csv']['tmp_name'], 'r');
            $count = 0;
            $header = true;

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if ($header) {
                    $header = false;
                    // Skip header jika baris pertama adalah judul kolom
                    if (isset($row[0]) && stripos($row[0], 'Peserta') !== false) {
                        continue;
                    }
                }

                $no_peserta = trim($row[0] ?? '');
                $nama_siswa = trim($row[1] ?? '');
                $tingkat = trim($row[2] ?? 'Kelas X');
                $kelas_nama = trim($row[3] ?? 'X - Kelas Jauh');
                $username = trim($row[4] ?? '');
                $password_plain = trim($row[5] ?? '123456');

                if (empty($nama_siswa) || empty($no_peserta)) {
                    continue;
                }

                if (empty($username)) {
                    $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $no_peserta));
                }
                $password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

                // Cek apakah no_peserta atau username sudah ada
                $check = $db->prepare("SELECT id FROM cbt_peserta_khusus WHERE no_peserta = ? OR username = ?");
                $check->execute([$no_peserta, $username]);
                $existingId = $check->fetchColumn();

                if ($existingId) {
                    $stmt = $db->prepare("UPDATE cbt_peserta_khusus 
                        SET nama_siswa = ?, tingkat = ?, kelas_nama = ?, password_plain = ?, password_hash = ?
                        WHERE id = ?");
                    $stmt->execute([$nama_siswa, $tingkat, $kelas_nama, $password_plain, $password_hash, $existingId]);
                } else {
                    $stmt = $db->prepare("INSERT INTO cbt_peserta_khusus 
                        (no_peserta, nama_siswa, tingkat, kelas_nama, username, password_plain, password_hash, status)
                        VALUES (?, ?, ?, ?, ?, ?, ?, 'Aktif')");
                    $stmt->execute([$no_peserta, $nama_siswa, $tingkat, $kelas_nama, $username, $password_plain, $password_hash]);
                }
                $count++;
            }
            fclose($handle);

            redirect('/cbt/peserta_khusus?msg=imported&count=' . $count);
            exit;
        }
        redirect('/cbt/peserta_khusus');
    }

    /**
     * Cetak Kartu Peserta Kelas Jauh
     */
    public static function cetakKartuPesertaKhusus() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        $tingkat = trim($_GET['tingkat'] ?? '');
        $id = (int)($_GET['id'] ?? 0);

        $sql = "SELECT * FROM cbt_peserta_khusus WHERE status = 'Aktif'";
        $params = [];

        if ($id > 0) {
            $sql .= " AND id = ?";
            $params[] = $id;
        } elseif ($tingkat !== '') {
            $sql .= " AND tingkat = ?";
            $params[] = $tingkat;
        }

        $sql .= " ORDER BY tingkat ASC, nama_siswa ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $pesertaList = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        include __DIR__ . '/../../resources/views/cbt/peserta_khusus_cetak.php';
    }
}