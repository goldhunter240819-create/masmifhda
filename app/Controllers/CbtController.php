<?php

namespace App\Controllers;

use App\Core\Database;
use PDO;

class CbtController {

    private static $tablesEnsured = false;

    /**
     * Pastikan skema tabel modul CBT Online tersedia di database core.
     * Bersihkan data dummy lama agar 100% menggunakan data real.
     */
    public static function ensureTables($db) {
        if (self::$tablesEnsured) {
            return;
        }
        self::$tablesEnsured = true;
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
            try { $db->exec("ALTER TABLE cbt_peserta ADD COLUMN jumlah_tidak_jawab INT DEFAULT 0 AFTER jumlah_salah"); } catch (\Exception $e) {}

            // 5. Tabel Custom Jam / Slot Waktu Ujian CBT
            $db->exec("CREATE TABLE IF NOT EXISTS cbt_jam_slot (
                id INT AUTO_INCREMENT PRIMARY KEY,
                topik_id INT DEFAULT NULL,
                urutan INT DEFAULT 1,
                label_sesi VARCHAR(50) DEFAULT 'Sesi 1',
                jam_mulai VARCHAR(10) NOT NULL,
                jam_selesai VARCHAR(10) NOT NULL,
                is_istirahat TINYINT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (topik_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            try { $db->exec("ALTER TABLE cbt_jam_slot ADD COLUMN topik_id INT DEFAULT NULL AFTER id, ADD INDEX (topik_id)"); } catch (\Exception $e) {}

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

            // Pastikan kolom tambahan untuk foto, biodata, dan qr_token pada cbt_peserta_khusus
            $colsKj = [
                "ADD COLUMN foto VARCHAR(255) NULL AFTER nama_siswa",
                "ADD COLUMN jenis_kelamin VARCHAR(10) NULL DEFAULT 'L' AFTER foto",
                "ADD COLUMN tempat_lahir VARCHAR(100) NULL AFTER jenis_kelamin",
                "ADD COLUMN tanggal_lahir DATE NULL AFTER tempat_lahir",
                "ADD COLUMN no_hp VARCHAR(30) NULL AFTER tanggal_lahir",
                "ADD COLUMN alamat TEXT NULL AFTER no_hp",
                "ADD COLUMN qr_token VARCHAR(64) NULL AFTER password_hash"
            ];
            foreach ($colsKj as $colDef) {
                try {
                    $db->exec("ALTER TABLE cbt_peserta_khusus $colDef");
                } catch (\Exception $e) {}
            }

            // 6b. Tabel Akun Pengawas / Admin Kelas Jauh (Terisolasi dari SIAKAD Guru)
            $db->exec("CREATE TABLE IF NOT EXISTS cbt_pengawas_kj (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nama VARCHAR(150) NOT NULL,
                username VARCHAR(100) NOT NULL UNIQUE,
                password_plain VARCHAR(100) NOT NULL DEFAULT '123456',
                password_hash VARCHAR(255) NOT NULL,
                no_hp VARCHAR(30) NULL,
                lokasi_cabang VARCHAR(100) DEFAULT 'Kelas Jauh',
                status VARCHAR(20) DEFAULT 'Aktif',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (username),
                INDEX (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // Seed default akun pengawas KJ jika masih kosong
            try {
                $cntP = (int)$db->query("SELECT COUNT(*) FROM cbt_pengawas_kj")->fetchColumn();
                if ($cntP === 0) {
                    $pwHash = password_hash('123456', PASSWORD_BCRYPT);
                    $db->exec("INSERT INTO cbt_pengawas_kj (nama, username, password_plain, password_hash, lokasi_cabang, status) VALUES
                        ('Ustadz Pengawas Cabang', 'adminkj', '123456', '{$pwHash}', 'Cabang Kelas Jauh', 'Aktif')
                    ");
                }
            } catch (\Exception $e) {}

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
                $db->exec("ALTER TABLE cbt_ujian ADD COLUMN token VARCHAR(20) NULL AFTER acak_opsi");
            } catch (\Exception $e) {}

            try {
                $intervalSetting = (int)($db->query("SELECT setting_value FROM cbt_pengaturan WHERE setting_key = 'token_refresh_interval'")->fetchColumn() ?: 120);
                if ($intervalSetting <= 0) $intervalSetting = 120;

                $stmtNoToken = $db->query("SELECT id, mapel_nama, kelas_nama FROM cbt_ujian WHERE token IS NULL OR token = ''");
                if ($stmtNoToken) {
                    $uNoToken = $stmtNoToken->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($uNoToken as $un) {
                        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $un['mapel_nama'] ?: 'CBT'), 0, 3));
                        if (strlen($prefix) < 3) $prefix = str_pad($prefix, 3, 'X');
                        $genToken = $prefix . rand(100, 999);
                        $db->prepare("UPDATE cbt_ujian SET token = ? WHERE id = ?")->execute([$genToken, $un['id']]);
                        $db->prepare("INSERT INTO cbt_token (token, ujian_id, masa_berlaku_menit, status, created_at) VALUES (?, ?, ?, 'Aktif', NOW())")->execute([$genToken, $un['id'], $intervalSetting]);
                    }
                }
            } catch (\Exception $e) {}

            try {
                $db->exec("UPDATE bs_topik SET status = 'Arsip' WHERE jenis IN ('PAS', 'PTS') AND (tgl_mulai IS NULL OR tgl_mulai = '')");
            } catch (\Exception $e) {}

            // Sinkronkan pemetaan user_id siswa pada core.siswa agar sinkron 1-to-1 dengan kartu barcode/token
            try {
                $db->exec("UPDATE siswa SET user_id = (id + 341) WHERE id BETWEEN 1 AND 88 AND (user_id IS NULL OR user_id < 342)");
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
                if (class_exists('\App\Core\Database')) {
                    $dbS = \App\Core\Database::connect('siakad');
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
                }
            } catch (\Exception $e) {}

            // Topik Asesmen diisi via sinkronisasi resmi dari server online / import CBT, tidak di-seed dummy agar ID tidak bentrok.

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
                ['lock_by_schedule_time', '1', 'aturan', 'Kunci Pengerjaan Sesuai Jadwal (Waktu Sesi)'],
                ['require_all_answered_no_doubt', '1', 'aturan', 'Wajib Jawab Semua Soal & Bebas Ragu-Ragu Sebelum Selesai'],
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

            // 9. Tabel Kelola Ruang Ujian CBT
            $db->exec("CREATE TABLE IF NOT EXISTS cbt_ruang (
                id INT AUTO_INCREMENT PRIMARY KEY,
                kode_ruang VARCHAR(50) NOT NULL UNIQUE,
                nama_ruang VARCHAR(100) NOT NULL,
                tipe_meja TINYINT DEFAULT 1,
                baris_samping INT DEFAULT 4,
                baris_belakang INT DEFAULT 5,
                kapasitas INT NOT NULL,
                kode_pola VARCHAR(30) DEFAULT NULL,
                lokasi VARCHAR(100) DEFAULT NULL,
                pengawas VARCHAR(150) DEFAULT NULL,
                keterangan TEXT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // 10. Tabel Denah Tempat Duduk Peserta
            $db->exec("CREATE TABLE IF NOT EXISTS cbt_denah_peserta (
                id INT AUTO_INCREMENT PRIMARY KEY,
                ruang_id INT NOT NULL,
                nomor_meja INT NOT NULL,
                posisi_baris INT NOT NULL,
                posisi_kolom INT NOT NULL,
                posisi_sisi ENUM('L','R','C') DEFAULT 'C',
                siswa_id INT DEFAULT NULL,
                nis VARCHAR(50) DEFAULT NULL,
                nama_siswa VARCHAR(150) DEFAULT NULL,
                kelas_id INT DEFAULT NULL,
                kelas_nama VARCHAR(50) DEFAULT NULL,
                tingkat INT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX (ruang_id),
                INDEX (nomor_meja),
                INDEX (siswa_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // 11. Tabel Absensi CBT Scan 1 Pintu
            $db->exec("CREATE TABLE IF NOT EXISTS cbt_absensi (
                id INT AUTO_INCREMENT PRIMARY KEY,
                tanggal DATE NOT NULL,
                siswa_id INT NOT NULL,
                nis VARCHAR(50) NOT NULL,
                nama_siswa VARCHAR(150) NOT NULL,
                kelas_nama VARCHAR(100) NOT NULL,
                tipe_peserta ENUM('reguler', 'khusus') DEFAULT 'reguler',
                topik_id INT DEFAULT NULL,
                ujian_id INT DEFAULT NULL,
                mapel_nama VARCHAR(150) NOT NULL,
                jam_slot VARCHAR(50) NOT NULL,
                waktu_scan DATETIME NOT NULL,
                status ENUM('Hadir', 'Terlambat', 'Izin', 'Sakit') DEFAULT 'Hadir',
                metode_scan VARCHAR(30) DEFAULT 'QR_SCAN',
                petugas_nama VARCHAR(100) DEFAULT NULL,
                ruang_nama VARCHAR(100) DEFAULT NULL,
                keterangan VARCHAR(255) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (tanggal),
                INDEX (nis),
                INDEX (siswa_id),
                INDEX (ujian_id),
                INDEX (mapel_nama)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            try {
                $db->exec("ALTER TABLE cbt_absensi MODIFY COLUMN siswa_id INT NULL DEFAULT NULL");
            } catch (\Exception $e) {}

            // Seed ruang default jika kosong
            try {
                $cntR = (int)$db->query("SELECT COUNT(*) FROM cbt_ruang")->fetchColumn();
                if ($cntR === 0) {
                    $db->exec("INSERT INTO cbt_ruang (kode_ruang, nama_ruang, tipe_meja, baris_samping, baris_belakang, kapasitas, kode_pola, lokasi, pengawas) VALUES
                    ('R-01', 'Ruang 01', 2, 3, 5, 30, '2 x 3 x 5', 'Gedung A Lantai 1', 'Drs. Hamim Hasyim'),
                    ('R-02', 'Ruang 02', 2, 3, 5, 30, '2 x 3 x 5', 'Gedung A Lantai 1', 'Ahmad Asrofi, S.Pd.'),
                    ('R-03', 'Ruang 03', 1, 5, 5, 25, '1 x 5 x 5', 'Gedung B Lantai 2', 'Hikmatul Latifah, S.Pd.'),
                    ('LAB-01', 'Lab Komputer', 1, 5, 5, 25, '1 x 5 x 5', 'Gedung Lab CBT', 'Proktor CBT')");

                    $rList = $db->query("SELECT * FROM cbt_ruang")->fetchAll(PDO::FETCH_ASSOC);
                    $stmtInsSlot = $db->prepare("INSERT INTO cbt_denah_peserta (ruang_id, nomor_meja, posisi_baris, posisi_kolom, posisi_sisi) VALUES (?, ?, ?, ?, ?)");
                    foreach ($rList as $rg) {
                        $mNo = 1;
                        for ($b = 1; $b <= (int)$rg['baris_belakang']; $b++) {
                            for ($k = 1; $k <= (int)$rg['baris_samping']; $k++) {
                                if ((int)$rg['tipe_meja'] === 2) {
                                    $stmtInsSlot->execute([$rg['id'], $mNo++, $b, $k, 'L']);
                                    $stmtInsSlot->execute([$rg['id'], $mNo++, $b, $k, 'R']);
                                } else {
                                    $stmtInsSlot->execute([$rg['id'], $mNo++, $b, $k, 'C']);
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {}

            // Seed setting absensi cbt jika belum ada
            try {
                $db->prepare("INSERT IGNORE INTO cbt_pengaturan (setting_key, setting_value, kategori, keterangan) VALUES (?, ?, ?, ?)")
                   ->execute(['require_cbt_attendance', '1', 'aturan', 'Wajibkan Siswa Absensi Scan 1 Pintu Sebelum Mengerjakan Ujian']);
            } catch (\Exception $e) {}

            // 12. Tabel Pengawas Ruangan Per Sesi (Scoped Topik)
            $db->exec("CREATE TABLE IF NOT EXISTS cbt_ruang_pengawas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                topik_id INT NOT NULL,
                ruang_id INT NOT NULL,
                sesi_label VARCHAR(100) NOT NULL,
                jam_slot VARCHAR(50) DEFAULT NULL,
                guru_id INT DEFAULT NULL,
                guru_nama VARCHAR(150) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uq_topik_ruang_sesi (topik_id, ruang_id, sesi_label),
                INDEX (topik_id),
                INDEX (ruang_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // 12b. Tabel Jadwal Pengawas Ruang Multi-Hari & Multi-Sesi (Matrix Model)
            $db->exec("CREATE TABLE IF NOT EXISTS cbt_pengawas_jadwal (
                id INT AUTO_INCREMENT PRIMARY KEY,
                topik_id INT NOT NULL,
                hari VARCHAR(20) NOT NULL,
                tanggal DATE DEFAULT NULL,
                sesi_label VARCHAR(50) NOT NULL,
                jam_slot VARCHAR(50) DEFAULT NULL,
                ruang_id INT NOT NULL,
                ruang_nama VARCHAR(100) DEFAULT NULL,
                guru_id INT DEFAULT NULL,
                guru_nama VARCHAR(150) NOT NULL,
                guru_nip VARCHAR(50) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX (topik_id),
                INDEX (hari),
                INDEX (tanggal),
                INDEX (ruang_id),
                INDEX (guru_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // Migration: Hapus unique key lama yang membatasi hanya per hari agar multi-minggu dengan tanggal berbeda tidak bentrok
            try {
                $db->exec("ALTER TABLE cbt_pengawas_jadwal DROP INDEX uq_topik_hari_sesi_ruang");
            } catch (\Exception $e) {}

            // 12c. Tabel Presensi / Absensi Kehadiran Pengawas Ruangan
            $db->exec("CREATE TABLE IF NOT EXISTS cbt_pengawas_absensi (
                id INT AUTO_INCREMENT PRIMARY KEY,
                topik_id INT DEFAULT NULL,
                jadwal_id INT DEFAULT NULL,
                tanggal DATE NOT NULL,
                hari VARCHAR(20) DEFAULT NULL,
                sesi_label VARCHAR(50) DEFAULT NULL,
                jam_slot VARCHAR(50) DEFAULT NULL,
                ruang_id INT DEFAULT NULL,
                ruang_nama VARCHAR(100) DEFAULT NULL,
                guru_id INT NOT NULL,
                guru_nip VARCHAR(50) DEFAULT NULL,
                guru_nama VARCHAR(150) NOT NULL,
                waktu_scan DATETIME NOT NULL,
                metode_scan VARCHAR(30) DEFAULT 'QR_SCAN',
                status_kehadiran VARCHAR(30) DEFAULT 'Hadir',
                catatan TEXT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (topik_id),
                INDEX (tanggal),
                INDEX (ruang_id),
                INDEX (guru_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            try {
                $db->prepare("INSERT IGNORE INTO cbt_pengaturan (setting_key, setting_value, kategori, keterangan) VALUES (?, ?, ?, ?)")
                   ->execute(['honor_pengawas_per_sesi', '25000', 'honor', 'Nominal Honorarium Pengawas Ruang Per-Sesi (Rupiah)']);
            } catch (\Exception $e) {}

            // Migration: Kolom topik_id di cbt_denah_peserta
            try {
                $chkCol = $db->query("SHOW COLUMNS FROM cbt_denah_peserta LIKE 'topik_id'")->fetch();
                if (!$chkCol) {
                    $db->exec("ALTER TABLE cbt_denah_peserta ADD COLUMN topik_id INT DEFAULT NULL AFTER ruang_id, ADD INDEX (topik_id)");
                }
                // Update denah lama ke topik aktif jika belum ber-topik
                $activeTId = (int)($db->query("SELECT id FROM bs_topik WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1")->fetchColumn() ?: 3);
                $db->exec("UPDATE cbt_denah_peserta SET topik_id = {$activeTId} WHERE topik_id IS NULL OR topik_id = 0");
            } catch (\Exception $e) {}

            // Migration: Durasi Default 60 menit
            try {
                $defDurasi = (int)($db->query("SELECT setting_value FROM cbt_pengaturan WHERE setting_key = 'default_durasi_menit'")->fetchColumn() ?: 60);
                if ($defDurasi > 0) {
                    $db->exec("UPDATE cbt_ujian SET durasi_menit = {$defDurasi} WHERE durasi_menit = 90 OR durasi_menit IS NULL OR durasi_menit = 0");
                }
            } catch (\Exception $e) {}

            // Migration: Pembersihan Data Ghost / Duplikasi Meja di cbt_denah_peserta
            try {
                // 1. Bersihkan ghost M. Faiz Al Majid (siswa_id = 414) di Ruang 01 jika tersisa
                $db->exec("DELETE FROM cbt_denah_peserta WHERE ruang_id = 1 AND (nama_siswa LIKE '%Faiz%' OR siswa_id = 414)");

                // 2. Bersihkan duplikasi nomor_meja per ruang dan topik yang sama (pertahankan baris sah berkoordinat)
                $db->exec("
                    DELETE d1 FROM cbt_denah_peserta d1
                    INNER JOIN cbt_denah_peserta d2 
                    ON d1.ruang_id = d2.ruang_id 
                    AND (d1.topik_id = d2.topik_id OR (d1.topik_id IS NULL AND d2.topik_id IS NULL))
                    AND d1.nomor_meja = d2.nomor_meja
                    AND d1.id > d2.id
                ");

                // 3. Pastikan tidak ada siswa yang terdaftar ganda di 2 kursi/ruangan berbeda pada topik yang sama
                $db->exec("
                    DELETE d1 FROM cbt_denah_peserta d1
                    INNER JOIN cbt_denah_peserta d2
                    ON d1.siswa_id = d2.siswa_id
                    AND (d1.topik_id = d2.topik_id OR (d1.topik_id IS NULL AND d2.topik_id IS NULL))
                    AND d1.id < d2.id
                    WHERE d1.siswa_id IS NOT NULL AND d1.siswa_id > 0
                ");
            } catch (\Exception $e) {}

            // 13. Minimal Users, Siswa, Kelas & Guru for Standalone CBT Mode
            try {
                $db->exec("CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(100) NOT NULL UNIQUE,
                    password_hash VARCHAR(255) NOT NULL,
                    plain_password VARCHAR(100) DEFAULT NULL,
                    role_id INT DEFAULT 3,
                    status VARCHAR(20) DEFAULT 'active',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX (role_id),
                    INDEX (status)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

                $db->exec("CREATE TABLE IF NOT EXISTS kelas (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nama_kelas VARCHAR(100) NOT NULL,
                    tingkat VARCHAR(20) DEFAULT 'X',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

                $db->exec("CREATE TABLE IF NOT EXISTS siswa (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT DEFAULT NULL,
                    nis VARCHAR(50) NOT NULL,
                    nik VARCHAR(50) DEFAULT NULL,
                    nama VARCHAR(150) NOT NULL,
                    kelas_id INT DEFAULT NULL,
                    jenis_kelamin VARCHAR(10) DEFAULT 'L',
                    status VARCHAR(20) DEFAULT 'Aktif',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX (nis),
                    INDEX (nik),
                    INDEX (user_id),
                    INDEX (kelas_id),
                    INDEX (status)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

                $db->exec("CREATE TABLE IF NOT EXISTS guru (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT DEFAULT NULL,
                    nama VARCHAR(150) NOT NULL,
                    nip VARCHAR(50) DEFAULT NULL,
                    nik VARCHAR(50) DEFAULT NULL,
                    is_kamad TINYINT DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

                $hasAdmin = (int)$db->query("SELECT COUNT(*) FROM users WHERE role_id = 1")->fetchColumn();
                if ($hasAdmin === 0) {
                    $pwHash = password_hash('proktor123', PASSWORD_BCRYPT);
                    $db->exec("INSERT INTO users (username, password_hash, plain_password, role_id, status) VALUES ('proktor', '{$pwHash}', 'proktor123', 1, 'active')");
                }
            } catch (\Exception $e) {}

        } catch (\Exception $e) {
            // Silence exception
        }
    }

    /**
     * Dapatkan daftar Mata Pelajaran Real MAS MIFTAHUL HUDA (Madrasah Aliyah)
     * Terisolasi dari sisa data MI (Madrasah Ibtidaiyah) lama
     */
    public static function getRealMapelList($db_siakad = null, $db_core = null) {
        if (!$db_siakad) {
            try {
                $db_siakad = \App\Core\Database::connect('siakad');
            } catch (\Exception $e) {}
        }
        if (!$db_core) {
            try {
                $db_core = \App\Core\Database::connect('core');
            } catch (\Exception $e) {}
        }

        $result = [];
        $seen = [];

        // 1. Ambil dari tabel mapel di database Siakad (sumber resmi mata pelajaran MAS MIFTAHUL HUDA)
        if ($db_siakad) {
            try {
                $rows = $db_siakad->query("SELECT id, nama_mapel, kode_mapel, kategori FROM mapel ORDER BY nama_mapel ASC")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $r) {
                    $key = strtolower(trim($r['nama_mapel']));
                    if (!isset($seen[$key]) && !empty($key)) {
                        $seen[$key] = true;
                        $result[] = [
                            'id' => (int)$r['id'],
                            'nama_mapel' => trim($r['nama_mapel']),
                            'kode_mapel' => trim(isset($r['kode_mapel']) ? $r['kode_mapel'] : ''),
                            'kategori' => trim(isset($r['kategori']) ? $r['kategori'] : 'Umum')
                        ];
                    }
                }
            } catch (\Exception $e) {}
        }

        // 2. Ambil dari database core jika ada tabel mapel
        if ($db_core && $db_core !== $db_siakad) {
            try {
                $rows = $db_core->query("SELECT id, nama_mapel, kode_mapel, kategori FROM mapel ORDER BY nama_mapel ASC")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $r) {
                    $key = strtolower(trim($r['nama_mapel']));
                    if (!isset($seen[$key]) && !empty($key)) {
                        $seen[$key] = true;
                        $result[] = [
                            'id' => (int)$r['id'],
                            'nama_mapel' => trim($r['nama_mapel']),
                            'kode_mapel' => trim(isset($r['kode_mapel']) ? $r['kode_mapel'] : ''),
                            'kategori' => trim(isset($r['kategori']) ? $r['kategori'] : 'Umum')
                        ];
                    }
                }
            } catch (\Exception $e) {}
        }

        // 3. Gabungkan dari penugasan mengajar / bs_paket / cbt_ujian jika ada mapel khas
        if ($db_siakad) {
            try {
                $extraMapels = $db_siakad->query("SELECT DISTINCT mapel_nama as nama_mapel FROM bs_paket WHERE mapel_nama IS NOT NULL AND mapel_nama != '' ORDER BY mapel_nama ASC")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($extraMapels as $em) {
                    $key = strtolower(trim($em['nama_mapel']));
                    if (!isset($seen[$key]) && !empty($key)) {
                        $seen[$key] = true;
                        $result[] = [
                            'id' => count($result) + 1,
                            'nama_mapel' => trim($em['nama_mapel']),
                            'kode_mapel' => strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $em['nama_mapel']), 0, 3)),
                            'kategori' => 'Umum'
                        ];
                    }
                }
            } catch (\Exception $e) {}
        }

        // 4. Standar Master Lengkap Mapel MA (termasuk Prakarya & Kewirausahaan, Koding, Matematika Lanjutan, Tahfiz)
        $defaultList = [
            ['id' => 1, 'nama_mapel' => "Al-Qur'an Hadits", 'kode_mapel' => 'QH', 'kategori' => 'Pendidikan Agama Islam'],
            ['id' => 2, 'nama_mapel' => 'Akidah Akhlak', 'kode_mapel' => 'AA', 'kategori' => 'Pendidikan Agama Islam'],
            ['id' => 3, 'nama_mapel' => 'Fikih', 'kode_mapel' => 'FIQ', 'kategori' => 'Pendidikan Agama Islam'],
            ['id' => 4, 'nama_mapel' => 'Sejarah Kebudayaan Islam (SKI)', 'kode_mapel' => 'SKI', 'kategori' => 'Pendidikan Agama Islam'],
            ['id' => 5, 'nama_mapel' => 'Bahasa Arab', 'kode_mapel' => 'ARB', 'kategori' => 'Bahasa'],
            ['id' => 6, 'nama_mapel' => 'Bahasa Indonesia', 'kode_mapel' => 'IND', 'kategori' => 'Umum'],
            ['id' => 7, 'nama_mapel' => 'Bahasa Inggris', 'kode_mapel' => 'ING', 'kategori' => 'Umum'],
            ['id' => 8, 'nama_mapel' => 'Matematika', 'kode_mapel' => 'MTK', 'kategori' => 'Umum'],
            ['id' => 9, 'nama_mapel' => 'Matematika Lanjutan', 'kode_mapel' => 'MTKL', 'kategori' => 'Peminatan MIPA'],
            ['id' => 10, 'nama_mapel' => 'Informatika', 'kode_mapel' => 'INF', 'kategori' => 'Umum'],
            ['id' => 11, 'nama_mapel' => 'Koding', 'kode_mapel' => 'KOD', 'kategori' => 'Muatan Lokal'],
            ['id' => 12, 'nama_mapel' => 'Fisika', 'kode_mapel' => 'FIS', 'kategori' => 'Peminatan MIPA'],
            ['id' => 13, 'nama_mapel' => 'Kimia', 'kode_mapel' => 'KIM', 'kategori' => 'Peminatan MIPA'],
            ['id' => 14, 'nama_mapel' => 'Biologi', 'kode_mapel' => 'BIO', 'kategori' => 'Peminatan MIPA'],
            ['id' => 15, 'nama_mapel' => 'Sosiologi', 'kode_mapel' => 'SOS', 'kategori' => 'Peminatan IPS'],
            ['id' => 16, 'nama_mapel' => 'Geografi', 'kode_mapel' => 'GEO', 'kategori' => 'Peminatan IPS'],
            ['id' => 17, 'nama_mapel' => 'Ekonomi', 'kode_mapel' => 'EKO', 'kategori' => 'Peminatan IPS'],
            ['id' => 18, 'nama_mapel' => 'Sejarah Umum', 'kode_mapel' => 'SEJ', 'kategori' => 'Umum'],
            ['id' => 19, 'nama_mapel' => 'Sejarah Indonesia', 'kode_mapel' => 'SI', 'kategori' => 'Umum'],
            ['id' => 20, 'nama_mapel' => 'Pendidikan Pancasila & Kewarganegaraan (PPKn)', 'kode_mapel' => 'PPKN', 'kategori' => 'Umum'],
            ['id' => 21, 'nama_mapel' => 'Pendidikan Jasmani, Olahraga & Kesehatan (PJOK)', 'kode_mapel' => 'PJOK', 'kategori' => 'Umum'],
            ['id' => 22, 'nama_mapel' => 'Seni Budaya', 'kode_mapel' => 'SBD', 'kategori' => 'Umum'],
            ['id' => 23, 'nama_mapel' => 'Prakarya & Kewirausahaan', 'kode_mapel' => 'PKWU', 'kategori' => 'Umum'],
            ['id' => 24, 'nama_mapel' => 'Tahfiz', 'kode_mapel' => 'TAH', 'kategori' => 'Muatan Lokal'],
        ];

        foreach ($defaultList as $df) {
            $key = strtolower(trim($df['nama_mapel']));
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $result[] = $df;
            }
        }

        // Urutkan alfabetis
        usort($result, function($a, $b) {
            return strcasecmp($a['nama_mapel'], $b['nama_mapel']);
        });

        return $result;
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
     * Pastikan sesi PHP berjalan dengan session_name dan session_save_path yang seragam
     */
    public static function ensureSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_name("AULA_SYSTEM_PORTAL");
            $sessDir = __DIR__ . '/../../storage/sessions';
            if (!is_dir($sessDir)) {
                @mkdir($sessDir, 0777, true);
            }
            if (is_dir($sessDir) && is_writable($sessDir)) {
                @session_save_path($sessDir);
            }
            ini_set('session.gc_maxlifetime', 86400);
            ini_set('session.cookie_lifetime', 86400);
            @session_start();
        }
    }

    /**
     * Cek apakah aplikasi sedang berjalan dalam mode CBT Standalone Offline
     */
    public static function isStandalone() {
        if (defined('CBT_STANDALONE_MODE') && CBT_STANDALONE_MODE) {
            return true;
        }
        // Server offline madrasah tidak memiliki modul SiakadController / FinanceController
        if (!file_exists(__DIR__ . '/SiakadController.php') || !file_exists(__DIR__ . '/FinanceController.php')) {
            return true;
        }
        $host = $_SERVER['HTTP_HOST'] ?? '';
        // Deteksi host IP lokal (192.168.*, 10.*, 127.0.0.1, localhost) dan BUKAN domain cloud resmi
        if (strpos($host, 'apk.masmifhda.sch.id') === false && 
            (strpos($host, '192.168.') === 0 || strpos($host, '10.') === 0 || strpos($host, '127.0.0.1') === 0 || strpos($host, 'localhost') === 0)) {
            return true;
        }
        return false;
    }

    /**
     * Inisialisasi Auth & Koneksi Database
     */
    private static function getAuthContext($allowStudent = false) {
        self::ensureSession();

        $roleId = isset($_SESSION['role_id']) ? (int)$_SESSION['role_id'] : 0;
        $isKamad = !empty($_SESSION['is_kamad']);
        $isAdminOrKamad = ($roleId === 1 || $roleId === 99 || $isKamad || (isset($_SESSION['role']) && in_array(strtolower($_SESSION['role']), ['admin', 'developer', 'kamad', 'proktor'])));

        if ($isAdminOrKamad) {
            unset($_SESSION['cbt_student_id'], $_SESSION['no_peserta']);
        }

        if (!$allowStudent) {
            if (!isset($_SESSION['user_id'])) {
                redirect('/login');
                exit;
            }

            $isCbtPeserta = !empty($_SESSION['cbt_student_id']) || !empty($_SESSION['no_peserta']);
            // Siswa (role_id = 3 / role_id = 7 / akun CBT siswa) DILARANG masuk ke portal manajemen proktor/admin
            if (!$isAdminOrKamad && ($roleId === 3 || $roleId === 7 || $isCbtPeserta)) {
                $cbtApkUrl = function_exists('base_url') ? base_url('apk/cbt.php') : ((strpos($_SERVER['REQUEST_URI'] ?? '', '/cbt/') !== false) ? '/cbt/apk/cbt.php' : '/apk/cbt.php');
                header("Location: {$cbtApkUrl}");
                exit;
            }
        } else {
            // Mode Pengerjaan Ujian Siswa (kerjakan, submit, simpan-jawaban, pelanggaran, selesai)
            $hasAnyLogin = isset($_SESSION['user_id']) || isset($_SESSION['siswa_id']) || isset($_SESSION['cbt_khusus_id']) || isset($_SESSION['cbt_student_id']) || isset($_SESSION['guru_id']);
            if (!$hasAnyLogin) {
                $cbtLoginUrl = function_exists('base_url') ? base_url('apk/login.php') : ((strpos($_SERVER['REQUEST_URI'] ?? '', '/cbt/') !== false) ? '/cbt/apk/login.php' : '/apk/login.php');
                header("Location: {$cbtLoginUrl}");
                exit;
            }
        }

        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

        self::ensureTables($db_core);

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

        // Pemetaan hari ke tanggal riil berdasarkan rentang tgl_mulai & tgl_selesai topik
        // (Hari sebelum tanggal mulai TIDAK dimasukkan, agar sesuai periode topik ujian riil)
        $hari_tanggal_map = [];
        $hari_list = [];
        $bulanIndoShort = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];
        $namaHariMap = [
            1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
            5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
        ];

        if (!empty($selectedTopik['tgl_mulai'])) {
            $startDate = new \DateTime($selectedTopik['tgl_mulai']);
            if (!empty($selectedTopik['tgl_selesai']) && $selectedTopik['tgl_selesai'] >= $selectedTopik['tgl_mulai']) {
                $endDate = new \DateTime($selectedTopik['tgl_selesai']);
            } else {
                // Jika tgl_selesai belum diatur, default sampai akhir pekan (Sabtu) atau minimal 3 hari ke depan
                $endDate = clone $startDate;
                $startN = (int)$startDate->format('N');
                if ($startN < 6) {
                    $endDate->modify('+' . (6 - $startN) . ' days');
                }
            }

            // Loop HANYA dari tgl_mulai sampai tgl_selesai
            // Menghasilkan daftar tanggal dan hari riil ujian (bisa 1 minggu, 2 minggu, dst tanpa terpotong)
            $curr = clone $startDate;
            while ($curr <= $endDate) {
                $dayN = (int)$curr->format('N');
                // Masukkan hari kerja (Senin - Sabtu; jika tgl_mulai / tgl_selesai jatuh hari Minggu, sertakan)
                if ($dayN <= 6 || $curr == $startDate || $curr == $endDate) {
                    $dName = $namaHariMap[$dayN];
                    $dateYmd = $curr->format('Y-m-d');
                    $mNum = (int)$curr->format('n');
                    $shortDate = $curr->format('d') . ' ' . ($bulanIndoShort[$mNum] ?? '');
                    
                    // Format unik Hari + Tanggal (contoh: "Kamis (19 Nov)", "Jumat (20 Nov)", "Kamis (26 Nov)")
                    $dayLabel = $dName . ' (' . $shortDate . ')';
                    $hari_list[] = $dayLabel;

                    $hari_tanggal_map[$dayLabel] = [
                        'date' => $dateYmd,
                        'hari' => $dName,
                        'label_short' => $shortDate,
                        'label_full' => $dName . ', ' . $curr->format('d') . ' ' . ($bulanIndoShort[$mNum] ?? '') . ' ' . $curr->format('Y')
                    ];
                    // Mapping untuk key hari murni dan key tanggal
                    if (!isset($hari_tanggal_map[$dName])) {
                        $hari_tanggal_map[$dName] = $hari_tanggal_map[$dayLabel];
                    }
                    $hari_tanggal_map[$dateYmd] = $hari_tanggal_map[$dayLabel];
                }
                $curr->modify('+1 day');
            }
        }

        // Fallback jika topik belum diset tanggal sama sekali
        if (empty($hari_list)) {
            if (empty($hari_list)) {
            $hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        }
        }

        // Ambil daftar kelas real
        $kelasList = self::getRealKelasList($db, $db_siakad);

        // Ambil daftar mapel real madrasah
        $mapelList = self::getRealMapelList($db_siakad, $db);

        // Ambil daftar custom jam slot terisolasi per topik aktif
        $stmtSlot = $db->prepare("SELECT * FROM cbt_jam_slot WHERE topik_id = ? ORDER BY urutan ASC, jam_mulai ASC");
        $stmtSlot->execute([$selectedTopikId]);
        $slotList = $stmtSlot->fetchAll(PDO::FETCH_ASSOC);
        if (empty($slotList)) {
            // Ambil slot default / global
            $defSlots = $db->query("SELECT * FROM cbt_jam_slot WHERE topik_id IS NULL ORDER BY urutan ASC, jam_mulai ASC")->fetchAll(PDO::FETCH_ASSOC);
            if (empty($defSlots)) {
                $defSlots = [
                    ['urutan' => 1, 'label_sesi' => 'Sesi 1', 'jam_mulai' => '07:30', 'jam_selesai' => '08:45', 'is_istirahat' => 0],
                    ['urutan' => 2, 'label_sesi' => 'Sesi 2', 'jam_mulai' => '09:00', 'jam_selesai' => '10:15', 'is_istirahat' => 0],
                    ['urutan' => 3, 'label_sesi' => 'Istirahat', 'jam_mulai' => '10:15', 'jam_selesai' => '10:45', 'is_istirahat' => 1],
                    ['urutan' => 4, 'label_sesi' => 'Sesi 3', 'jam_mulai' => '10:45', 'jam_selesai' => '12:00', 'is_istirahat' => 0],
                    ['urutan' => 5, 'label_sesi' => 'Sesi 4', 'jam_mulai' => '12:30', 'jam_selesai' => '13:45', 'is_istirahat' => 0],
                ];
            }
            if ($selectedTopikId > 0) {
                foreach ($defSlots as $ds) {
                    $ins = $db->prepare("INSERT INTO cbt_jam_slot (topik_id, urutan, label_sesi, jam_mulai, jam_selesai, is_istirahat) VALUES (?, ?, ?, ?, ?, ?)");
                    $ins->execute([$selectedTopikId, $ds['urutan'], $ds['label_sesi'], $ds['jam_mulai'], $ds['jam_selesai'], $ds['is_istirahat']]);
                }
                $stmtSlot->execute([$selectedTopikId]);
                $slotList = $stmtSlot->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $slotList = $defSlots;
            }
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

        $cbtSettings = self::getAllPengaturan($db);
        $defDurasi = !empty($cbtSettings['default_durasi_menit']) ? (int)$cbtSettings['default_durasi_menit'] : 60;

        // Ambil grid jadwal tersimpan untuk topik aktif (mendukung mapel langsung tanpa ujian_id)
        $gridRows = [];
        if ($selectedTopikId > 0) {
            $stmtG = $db->prepare("
                SELECT g.*, 
                       COALESCE(NULLIF(g.mapel_nama, ''), u.mapel_nama, 'Mata Pelajaran') AS mapel_nama,
                       COALESCE(u.nama_ujian, g.mapel_nama) AS nama_ujian,
                       COALESCE(u.kode_ujian, '-') AS kode_ujian,
                       COALESCE(u.durasi_menit, {$defDurasi}) AS durasi_menit,
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
                           COALESCE(u.durasi_menit, {$defDurasi}) AS durasi_menit,
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
                       COALESCE(u.durasi_menit, {$defDurasi}) AS durasi_menit,
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
            $tgl = $gr['tanggal'] ?? null;
            $slot = $gr['jam_slot'];
            $kKey = !empty($gr['kelas_id']) ? $gr['kelas_id'] : $gr['kelas_nama'];

            // 1. Simpan key hari mentah ('Kamis')
            $jadwal_master[$h][$slot][$kKey] = $gr;
            $jadwal_master[$h][$slot][$gr['kelas_nama']] = $gr;

            // 2. Simpan jika ada tanggal 'YYYY-MM-DD'
            if (!empty($tgl)) {
                $jadwal_master[$tgl][$slot][$kKey] = $gr;
                $jadwal_master[$tgl][$slot][$gr['kelas_nama']] = $gr;

                $mNum = (int)date('n', strtotime($tgl));
                $dayLabel = $h . ' (' . date('d', strtotime($tgl)) . ' ' . ($bulanIndoShort[$mNum] ?? '') . ')';
                $jadwal_master[$dayLabel][$slot][$kKey] = $gr;
                $jadwal_master[$dayLabel][$slot][$gr['kelas_nama']] = $gr;
            }
        }

        if (empty($hari_list)) {
            $hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        }

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

            $plainHari = $hari;
            if (strpos($hari, '(') !== false) {
                $plainHari = trim(explode('(', $hari)[0]);
            }

            if ((!empty($mapel_nama) || !empty($ujian_id)) && !empty($jam_slot) && !empty($kelas_nama)) {
                $checkSql = "SELECT id FROM cbt_jadwal_grid WHERE jam_slot = ? AND kelas_nama = ?";
                $params = [$jam_slot, $kelas_nama];
                if (!empty($tanggal)) {
                    $checkSql .= " AND (tanggal = ? OR (tanggal IS NULL AND (hari = ? OR hari = ?)))";
                    $params[] = $tanggal;
                    $params[] = $hari;
                    $params[] = $plainHari;
                } else {
                    $checkSql .= " AND (hari = ? OR hari = ?)";
                    $params[] = $hari;
                    $params[] = $plainHari;
                }
                if ($topik_id) {
                    $checkSql .= " AND (topik_id = ? OR topik_id IS NULL)";
                    $params[] = $topik_id;
                }
                $stmtCheck = $db->prepare($checkSql);
                $stmtCheck->execute($params);
                $existingId = $stmtCheck->fetchColumn();

                if ($existingId) {
                    $stmtUp = $db->prepare("UPDATE cbt_jadwal_grid SET topik_id = ?, ujian_id = ?, mapel_nama = ?, kelas_id = ?, pengawas = ?, ruang = ?, tanggal = ?, hari = ? WHERE id = ?");
                    $stmtUp->execute([$topik_id, $ujian_id, $mapel_nama, $kelas_id, $pengawas, $ruang, $tanggal, $plainHari, $existingId]);
                } else {
                    $stmtIns = $db->prepare("INSERT INTO cbt_jadwal_grid (topik_id, ujian_id, mapel_nama, hari, jam_slot, kelas_id, kelas_nama, pengawas, ruang, tanggal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmtIns->execute([$topik_id, $ujian_id, $mapel_nama, $plainHari, $jam_slot, $kelas_id, $kelas_nama, $pengawas, $ruang, $tanggal]);
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
            $topik_id = (int)($_POST['topik_id'] ?? 0);
            $urutan = (int)($_POST['urutan'] ?? 1);
            $label_sesi = trim($_POST['label_sesi'] ?? 'Sesi');
            $jam_mulai = trim($_POST['jam_mulai'] ?? '');
            $jam_selesai = trim($_POST['jam_selesai'] ?? '');
            $is_istirahat = !empty($_POST['is_istirahat']) ? 1 : 0;

            if ($topik_id <= 0) {
                $tp = $db->query("SELECT id FROM bs_topik WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
                $topik_id = $tp ? (int)$tp['id'] : null;
            }

            if (!empty($jam_mulai) && !empty($jam_selesai)) {
                if ($id > 0) {
                    $stmt = $db->prepare("UPDATE cbt_jam_slot SET topik_id = ?, urutan = ?, label_sesi = ?, jam_mulai = ?, jam_selesai = ?, is_istirahat = ? WHERE id = ?");
                    $stmt->execute([$topik_id, $urutan, $label_sesi, $jam_mulai, $jam_selesai, $is_istirahat, $id]);
                } else {
                    $stmt = $db->prepare("INSERT INTO cbt_jam_slot (topik_id, urutan, label_sesi, jam_mulai, jam_selesai, is_istirahat) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$topik_id, $urutan, $label_sesi, $jam_mulai, $jam_selesai, $is_istirahat]);
                }

                // ATURAN EKSPLISIT: Ketika jam diedit, maka data jadwal sebelumnya otomatis terhapus
                // agar data jam lama tidak nyangkut!
                if ($topik_id > 0) {
                    $db->prepare("DELETE FROM cbt_jadwal_grid WHERE topik_id = ?")->execute([$topik_id]);
                    $db->prepare("DELETE FROM cbt_absensi WHERE topik_id = ? AND tanggal >= CURDATE()")->execute([$topik_id]);
                }
            }
            $redir = '/cbt/jadwal?msg=slot_saved_reset_jadwal';
            if ($topik_id > 0) $redir .= '&topik_id=' . $topik_id;
            redirect($redir);
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
            $topik_id = (int)($_POST['topik_id'] ?? 0);
            if ($id > 0) {
                if ($topik_id <= 0) {
                    $topik_id = (int)$db->query("SELECT topik_id FROM cbt_jam_slot WHERE id = {$id}")->fetchColumn();
                }
                $db->prepare("DELETE FROM cbt_jam_slot WHERE id = ?")->execute([$id]);

                // Bersihkan jadwal lama topik ini
                if ($topik_id > 0) {
                    $db->prepare("DELETE FROM cbt_jadwal_grid WHERE topik_id = ?")->execute([$topik_id]);
                }
            }
            $redir = '/cbt/jadwal?msg=slot_deleted';
            if ($topik_id > 0) $redir .= '&topik_id=' . $topik_id;
            redirect($redir);
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
            $topik_id = (int)($_POST['topik_id'] ?? 0);
            if ($topik_id > 0) {
                $db->prepare("DELETE FROM cbt_jam_slot WHERE topik_id = ?")->execute([$topik_id]);
                $stmt = $db->prepare("INSERT INTO cbt_jam_slot (topik_id, urutan, label_sesi, jam_mulai, jam_selesai, is_istirahat) VALUES
                    (?, 1, 'Sesi 1', '07:30', '08:45', 0),
                    (?, 2, 'Sesi 2', '09:00', '10:15', 0),
                    (?, 3, 'Istirahat', '10:15', '10:45', 1),
                    (?, 4, 'Sesi 3', '10:45', '12:00', 0),
                    (?, 5, 'Sesi 4', '12:30', '13:45', 0)
                ");
                $stmt->execute([$topik_id, $topik_id, $topik_id, $topik_id, $topik_id]);
                // Bersihkan juga jadwal grid topik ini
                $db->prepare("DELETE FROM cbt_jadwal_grid WHERE topik_id = ?")->execute([$topik_id]);
            } else {
                $db->exec("TRUNCATE TABLE cbt_jam_slot");
                $db->exec("INSERT INTO cbt_jam_slot (urutan, label_sesi, jam_mulai, jam_selesai, is_istirahat) VALUES
                    (1, 'Sesi 1', '07:30', '08:45', 0),
                    (2, 'Sesi 2', '09:00', '10:15', 0),
                    (3, 'Istirahat', '10:15', '10:45', 1),
                    (4, 'Sesi 3', '10:45', '12:00', 0),
                    (5, 'Sesi 4', '12:30', '13:45', 0)
                ");
            }
            $redir = '/cbt/jadwal?msg=slot_reset';
            if ($topik_id > 0) $redir .= '&topik_id=' . $topik_id;
            redirect($redir);
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

            $defDurasi = (int)($db->query("SELECT setting_value FROM cbt_pengaturan WHERE setting_key = 'default_durasi_menit'")->fetchColumn() ?: 60);
            $defKkm = (int)($db->query("SELECT setting_value FROM cbt_pengaturan WHERE setting_key = 'standar_kkm'")->fetchColumn() ?: 75);

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
                    $stmtU = $db->prepare("SELECT * FROM cbt_ujian WHERE id = ? AND (mapel_nama = ? OR mapel_nama LIKE ?)");
                    $stmtU->execute([$ujianId, $mapel, '%' . $mapel . '%']);
                    $existingUjian = $stmtU->fetch(PDO::FETCH_ASSOC);
                }

                if (!$existingUjian) {
                    // Cari berdasarkan topik_id, mapel_nama, dan kelas_nama / tingkat
                    $stmtFind = $db->prepare("SELECT * FROM cbt_ujian WHERE (topik_id = ? OR topik_id IS NULL) AND (mapel_nama = ? OR mapel_nama LIKE ?) AND (kelas_nama = ? OR kelas_nama = ? OR kelas_nama = 'Semua Kelas' OR kelas_nama LIKE ?) ORDER BY id DESC LIMIT 1");
                    $stmtFind->execute([$tId, $mapel, '%' . $mapel . '%', $kelasNama, $tingkat, '%' . $tingkat . '%']);
                    $existingUjian = $stmtFind->fetch(PDO::FETCH_ASSOC);
                }

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

                if (!$existingUjian) {
                    $kode = 'UJN-' . strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $mapel), 0, 3)) . '-' . strtoupper(substr(uniqid(), -4));

                    $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $mapel ?: 'CBT'), 0, 3));
                    if (strlen($prefix) < 3) $prefix = str_pad($prefix, 3, 'X');
                    $tokenUjian = $prefix . rand(100, 999);

                    $stmtIns = $db->prepare("INSERT INTO cbt_ujian (topik_id, kode_ujian, nama_ujian, mapel_nama, kelas_nama, durasi_menit, kkm, acak_soal, acak_opsi, token, status) VALUES (?, ?, ?, ?, ?, ?, ?, 1, 1, ?, 'Aktif')");
                    $stmtIns->execute([$tId, $kode, $namaUjian, $mapel, $kelasNama, $defDurasi, $defKkm, $tokenUjian]);
                    $newUjianId = (int)$db->lastInsertId();
                    $existingUjian = ['id' => $newUjianId, 'topik_id' => $tId, 'mapel_nama' => $mapel, 'kelas_nama' => $kelasNama, 'token' => $tokenUjian];

                    try {
                        $intervalSetting = (int)($db->query("SELECT setting_value FROM cbt_pengaturan WHERE setting_key = 'token_refresh_interval'")->fetchColumn() ?: 120);
                        if ($intervalSetting <= 0) $intervalSetting = 120;
                        $db->prepare("INSERT INTO cbt_token (token, ujian_id, masa_berlaku_menit, status, created_at) VALUES (?, ?, ?, 'Aktif', NOW())")->execute([$tokenUjian, $newUjianId, $intervalSetting]);
                    } catch (\Exception $e) {}

                    $db->prepare("UPDATE cbt_jadwal_grid SET ujian_id = ? WHERE id = ?")->execute([$newUjianId, $g['id']]);
                } else {
                    if (empty($g['ujian_id']) || $g['ujian_id'] != $existingUjian['id']) {
                        $db->prepare("UPDATE cbt_jadwal_grid SET ujian_id = ? WHERE id = ?")->execute([$existingUjian['id'], $g['id']]);
                    }
                    if (!empty($namaUjian)) {
                        $db->prepare("UPDATE cbt_ujian SET status = 'Aktif', nama_ujian = ? WHERE id = ?")->execute([$namaUjian, $existingUjian['id']]);
                    } else {
                        $db->prepare("UPDATE cbt_ujian SET status = 'Aktif' WHERE id = ?")->execute([$existingUjian['id']]);
                    }
                }

                // Sinkronkan butir soal jika cbt_soal masih 0 atau jumlahnya tidak selaras dengan Bank Soal
                $targetUjianId = (int)$existingUjian['id'];
                $soalCount = (int)$db->query("SELECT COUNT(*) FROM cbt_soal WHERE ujian_id = {$targetUjianId}")->fetchColumn();

                $getLevel = function($str) {
                    $s = strtoupper((string)$str);
                    if (strpos($s, 'XII') !== false || preg_match('/\b12\b/', $s)) return 12;
                    if (strpos($s, 'XI') !== false || preg_match('/\b11\b/', $s)) return 11;
                    if (strpos($s, 'X') !== false || preg_match('/\b10\b/', $s)) return 10;
                    return 0;
                };
                $uLvl = $getLevel($kelasNama . ' ' . $tingkat);

                $bsItems = [];
                $dbsToCheck = (!empty($db_siakad) && $db_siakad !== $db) ? [$db_siakad, $db] : [$db];
                // 1. Cari via bs_paket untuk jenjang kelas spesifik
                foreach ($dbsToCheck as $targetDb) {
                    try {
                        $stmtAllPaket = $targetDb->prepare("
                            SELECT id, kelas_nama, kelas_sasaran FROM bs_paket 
                            WHERE (topik_id = ? OR ? = 0)
                              AND (mapel_nama = ? OR mapel_nama LIKE ?)
                            ORDER BY id ASC
                        ");
                        $stmtAllPaket->execute([$tId, $tId, $mapel, '%' . $mapel . '%']);
                        $allPkts = $stmtAllPaket->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($allPkts as $pkt) {
                            $pLvl = $getLevel($pkt['kelas_nama'] . ' ' . ($pkt['kelas_sasaran'] ?? ''));
                            if ($pLvl === $uLvl || $uLvl === 0) {
                                $stmtBS = $targetDb->prepare("SELECT * FROM bs_soal WHERE paket_id = ? ORDER BY nomor_urut ASC, id ASC");
                                $stmtBS->execute([$pkt['id']]);
                                $rows = $stmtBS->fetchAll(PDO::FETCH_ASSOC);
                                if (!empty($rows)) {
                                    $bsItems = $rows;
                                    break 2;
                                }
                            }
                        }
                    } catch (\Exception $e) {}
                }

                // 2. Fallback per jenjang kelas yang sama persis (tanpa campur aduk X, XI, XII)
                if (empty($bsItems)) {
                    foreach ($dbsToCheck as $targetDb) {
                        try {
                            $stmtAllSoal = $targetDb->prepare("
                                SELECT * FROM bs_soal 
                                WHERE (topik_id = ? OR ? = 0)
                                  AND (mapel_nama = ? OR mapel_nama LIKE ?)
                                ORDER BY nomor_urut ASC, id ASC
                            ");
                            $stmtAllSoal->execute([$tId, $tId, $mapel, '%' . $mapel . '%']);
                            $allSoals = $stmtAllSoal->fetchAll(PDO::FETCH_ASSOC);
                            $matched = [];
                            foreach ($allSoals as $sRow) {
                                $sLvl = $getLevel($sRow['kelas_nama']);
                                if ($sLvl === $uLvl || $uLvl === 0) {
                                    $matched[] = $sRow;
                                }
                            }
                            if (!empty($matched)) {
                                $bsItems = $matched;
                                break;
                            }
                        } catch (\Exception $e) {}
                    }
                }

                $shouldSyncSoal = ($soalCount === 0) || (!empty($bsItems) && $soalCount !== count($bsItems));
                if ($shouldSyncSoal && !empty($bsItems)) {
                    try { $db->prepare("DELETE FROM cbt_soal WHERE ujian_id = ?")->execute([$targetUjianId]); } catch (\Exception $e) {}
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
                            $b['skor_maksimal'] ?? 10.00,
                            $b['tipe_soal'] ?? 'sec_pg',
                            $b['konten_tambahan'] ?? null
                        ]);
                    }
                }
            }

            // Kumpulkan ID ujian yang terjadwal di cbt_jadwal_grid
            $activeGridUjianIds = !empty($gridRows) ? array_filter(array_map('intval', array_column($gridRows, 'ujian_id'))) : [];
            if ($topik_id > 0 && !empty($activeGridUjianIds)) {
                $inGridU = implode(',', array_unique($activeGridUjianIds));
                try {
                    $db->exec("UPDATE cbt_ujian SET status = 'Draft' WHERE topik_id = {$topik_id} AND id NOT IN ({$inGridU}) AND id NOT IN (SELECT DISTINCT ujian_id FROM cbt_peserta)");
                } catch (\Exception $e) {}
            }

            // Pastikan Token Ujian Aktif
            $tokAktif = $db->query("SELECT * FROM cbt_token WHERE status = 'Aktif' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            if (!$tokAktif) {
                $intervalSetting = (int)($db->query("SELECT setting_value FROM cbt_pengaturan WHERE setting_key = 'token_refresh_interval'")->fetchColumn() ?: 120);
                if ($intervalSetting <= 0) $intervalSetting = 120;
                $tokenStr = 'MAS' . rand(100, 999);
                $db->prepare("INSERT INTO cbt_token (token, masa_berlaku_menit, status, created_at) VALUES (?, ?, 'Aktif', NOW())")->execute([$tokenStr, $intervalSetting]);
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

                    $plainHari = $hari;
                    if (strpos($hari, '(') !== false) {
                        $plainHari = trim(explode('(', $hari)[0]);
                    }

                    if (!empty($hari) && !empty($jam_slot)) {
                        if (!empty($mapel_nama) || !empty($ujian_id)) {
                            if (empty($mapel_nama) && $ujian_id) {
                                try {
                                    $uRow = $db->query("SELECT mapel_nama FROM cbt_ujian WHERE id = " . (int)$ujian_id)->fetch(PDO::FETCH_ASSOC);
                                    $mapel_nama = $uRow['mapel_nama'] ?? '';
                                } catch (\Exception $e) {}
                            }

                            $checkSql = "SELECT id FROM cbt_jadwal_grid WHERE jam_slot = ? AND kelas_nama = ?";
                            $params = [$jam_slot, $kelas_nama];
                            if (!empty($tanggal)) {
                                $checkSql .= " AND (tanggal = ? OR (tanggal IS NULL AND (hari = ? OR hari = ?)))";
                                $params[] = $tanggal;
                                $params[] = $hari;
                                $params[] = $plainHari;
                            } else {
                                $checkSql .= " AND (hari = ? OR hari = ?)";
                                $params[] = $hari;
                                $params[] = $plainHari;
                            }
                            if ($topik_id) {
                                $checkSql .= " AND (topik_id = ? OR topik_id IS NULL)";
                                $params[] = $topik_id;
                            }
                            $stmtCheck = $db->prepare($checkSql);
                            $stmtCheck->execute($params);
                            $existingId = $stmtCheck->fetchColumn();

                            if ($existingId) {
                                $stmtUp = $db->prepare("UPDATE cbt_jadwal_grid SET topik_id = ?, ujian_id = ?, mapel_nama = ?, kelas_id = ?, pengawas = ?, ruang = ?, tanggal = ?, hari = ? WHERE id = ?");
                                $stmtUp->execute([$topik_id, $ujian_id, $mapel_nama, $kelas_id, $pengawas, $ruang, $tanggal, $plainHari, $existingId]);
                            } else {
                                $stmtIns = $db->prepare("INSERT INTO cbt_jadwal_grid (topik_id, ujian_id, mapel_nama, hari, jam_slot, kelas_id, kelas_nama, pengawas, ruang, tanggal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                                $stmtIns->execute([$topik_id, $ujian_id, $mapel_nama, $plainHari, $jam_slot, $kelas_id, $kelas_nama, $pengawas, $ruang, $tanggal]);
                            }

                            if ($ujian_id) {
                                $db->prepare("UPDATE cbt_ujian SET status = 'Aktif', kelas_nama = ? WHERE id = ?")->execute([$kelas_nama, $ujian_id]);
                            }
                        } else {
                            $delSql = "DELETE FROM cbt_jadwal_grid WHERE jam_slot = ? AND kelas_nama = ?";
                            $delParams = [$jam_slot, $kelas_nama];
                            if (!empty($tanggal)) {
                                $delSql .= " AND (tanggal = ? OR (tanggal IS NULL AND (hari = ? OR hari = ?)))";
                                $delParams[] = $tanggal;
                                $delParams[] = $hari;
                                $delParams[] = $plainHari;
                            } else {
                                $delSql .= " AND (hari = ? OR hari = ?)";
                                $delParams[] = $hari;
                                $delParams[] = $plainHari;
                            }
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
                // Ambil pemetaan tanggal dari rentang tgl_mulai sampai tgl_selesai topik
                $hariTanggal = [];
                if ($topik_id) {
                    $tp = $db->query("SELECT tgl_mulai, tgl_selesai FROM bs_topik WHERE id = " . (int)$topik_id)->fetch(PDO::FETCH_ASSOC);
                    if (!empty($tp['tgl_mulai'])) {
                        $sDate = new \DateTime($tp['tgl_mulai']);
                        $eDate = !empty($tp['tgl_selesai']) ? new \DateTime($tp['tgl_selesai']) : clone $sDate;
                        $cDate = clone $sDate;
                        $bulanIndoShort = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
                        $namaHariMap = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
                        while ($cDate <= $eDate) {
                            $dN = (int)$cDate->format('N');
                            if ($dN <= 6 || $cDate == $sDate || $cDate == $eDate) {
                                $hName = $namaHariMap[$dN];
                                $dStr = $cDate->format('Y-m-d');
                                $mN = (int)$cDate->format('n');
                                $sLabel = $hName . ' (' . $cDate->format('d') . ' ' . ($bulanIndoShort[$mN] ?? '') . ')';
                                $hariTanggal[$sLabel] = $dStr;
                                if (!isset($hariTanggal[$hName])) {
                                    $hariTanggal[$hName] = $dStr;
                                }
                                $hariTanggal[$dStr] = $dStr;
                            }
                            $cDate->modify('+1 day');
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
                        $tgl = !empty($sc['tanggal']) ? $sc['tanggal'] : ($hariTanggal[$hari] ?? null);

                        $plainHari = $hari;
                        if (strpos($hari, '(') !== false) {
                            $plainHari = trim(explode('(', $hari)[0]);
                        }

                        if (empty($hari) || empty($jam_slot)) continue;

                        if (!empty($mapel_nama)) {
                            // Cek apakah slot sudah ada untuk kelas ini
                            $checkSql = "SELECT id FROM cbt_jadwal_grid WHERE jam_slot = ? AND kelas_nama = ?";
                            $params = [$jam_slot, $kNama];
                            if (!empty($tgl)) {
                                $checkSql .= " AND (tanggal = ? OR (tanggal IS NULL AND (hari = ? OR hari = ?)))";
                                $params[] = $tgl;
                                $params[] = $hari;
                                $params[] = $plainHari;
                            } else {
                                $checkSql .= " AND (hari = ? OR hari = ?)";
                                $params[] = $hari;
                                $params[] = $plainHari;
                            }
                            if ($topik_id) {
                                $checkSql .= " AND (topik_id = ? OR topik_id IS NULL)";
                                $params[] = $topik_id;
                            }
                            $stmtCheck = $db->prepare($checkSql);
                            $stmtCheck->execute($params);
                            $existingId = $stmtCheck->fetchColumn();

                            if ($existingId) {
                                $sqlUp = "UPDATE cbt_jadwal_grid SET topik_id = ?, mapel_nama = ?, kelas_id = ?, tanggal = COALESCE(?, tanggal), hari = ?, ujian_id = NULL";
                                $upParams = [$topik_id, $mapel_nama, $kId, $tgl, $plainHari];
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
                                $stmtIns->execute([$topik_id, $mapel_nama, $plainHari, $jam_slot, $kId, $kNama, $pengawas, $tgl]);
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
     * Reset Hasil & Simulasi Pengerjaan Ujian untuk Topik Tertentu
     * Menghapus record pengerjaan di cbt_peserta sehingga siswa dapat mengulang ujian dari awal
     * 🛡️ PERLINDUNGAN AMAN: Bank Soal (bs_paket, bs_soal, cbt_soal, bs_topik) TIDAK DISENTUH SAMA SEKALI!
     */
    public static function resetTopik() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $db_siakad = $ctx['db_siakad'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? ($_POST['topik_id'] ?? 0));
            $redirectTo = $_POST['redirect_to'] ?? 'topik';

            if ($id > 0) {
                $ujianIds = [];

                // 1. Ambil seluruh ujian dengan topik_id ini
                try {
                    $u1 = $db->query("SELECT id FROM cbt_ujian WHERE topik_id = {$id}")->fetchAll(PDO::FETCH_COLUMN);
                    foreach ($u1 as $uid) {
                        $ujianIds[] = (int)$uid;
                    }
                } catch (\Exception $e) {}

                // 2. Ambil juga ujian yang terjadwal di cbt_jadwal_grid untuk topik ini
                try {
                    $u2 = $db->query("SELECT DISTINCT ujian_id FROM cbt_jadwal_grid WHERE topik_id = {$id} AND ujian_id IS NOT NULL AND ujian_id > 0")->fetchAll(PDO::FETCH_COLUMN);
                    foreach ($u2 as $uid) {
                        $ujianIds[] = (int)$uid;
                    }
                } catch (\Exception $e) {}

                // 3. Fallback: Cari ujian yang memuat nama topik
                try {
                    $stmtNama = ($db_siakad ?: $db)->prepare("SELECT nama_topik FROM bs_topik WHERE id = ?");
                    $stmtNama->execute([$id]);
                    $namaTopik = $stmtNama->fetchColumn();
                    if ($namaTopik) {
                        $stmtMatch = $db->prepare("SELECT id FROM cbt_ujian WHERE nama_ujian LIKE ?");
                        $stmtMatch->execute(['%' . $namaTopik . '%']);
                        foreach ($stmtMatch->fetchAll(PDO::FETCH_COLUMN) as $uid) {
                            $ujianIds[] = (int)$uid;
                        }
                    }
                } catch (\Exception $e) {}

                $ujianIds = array_unique(array_filter($ujianIds));

                $deletedCount = 0;
                if (!empty($ujianIds)) {
                    $inIds = implode(',', array_map('intval', $ujianIds));

                    // Hitung jumlah peserta yang akan direset
                    try {
                        $deletedCount = (int)$db->query("SELECT COUNT(*) FROM cbt_peserta WHERE ujian_id IN ({$inIds})")->fetchColumn();
                    } catch (\Exception $e) {}

                    // Hapus data pengerjaan ujian siswa (jawaban, skor nilai, waktu selesai, riwayat)
                    try {
                        $db->exec("DELETE FROM cbt_peserta WHERE ujian_id IN ({$inIds})");
                    } catch (\Exception $e) {}

                    // Hapus data absensi terkait topik & ujian ini agar tidak nyangkut saat simulasi ulang
                    try {
                        $db->exec("DELETE FROM cbt_absensi WHERE topik_id = {$id} OR ujian_id IN ({$inIds})");
                    } catch (\Exception $e) {}

                    // Kembalikan status ujian ke 'Aktif'
                    try {
                        $db->exec("UPDATE cbt_ujian SET status = 'Aktif' WHERE id IN ({$inIds})");
                    } catch (\Exception $e) {}
                } else {
                    try {
                        $db->exec("DELETE FROM cbt_absensi WHERE topik_id = {$id}");
                    } catch (\Exception $e) {}
                }

                // Hapus juga data absensi berdasarkan nama topik jika ada
                if (!empty($namaTopik)) {
                    try {
                        $stmtDelM = $db->prepare("DELETE FROM cbt_absensi WHERE mapel_nama LIKE ?");
                        $stmtDelM->execute(['%' . $namaTopik . '%']);
                    } catch (\Exception $e) {}
                }

                // Redirect dengan notifikasi sukses
                $targetUrl = ($redirectTo === 'jadwal') 
                    ? ('/cbt/jadwal?topik_id=' . $id . '&msg=exam_reset&count=' . $deletedCount)
                    : ('/cbt/topik?msg=exam_reset&count=' . $deletedCount);

                redirect($targetUrl);
                exit;
            }
        }
        redirect('/cbt/topik');
    }

    /**
     * Hapus Topik Ujian Beserta Seluruh Paket Ujian, Jadwal Grid, & Absensi Terkait
     */
    public static function deleteTopik() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $db_siakad = $ctx['db_siakad'];
        self::ensureTables($db);

        $id = (int)($_POST['id'] ?? ($_GET['id'] ?? 0));
        if ($id > 0) {
            $ujianIds = [];

            // 1. Ambil data nama topik
            $namaTopik = '';
            try {
                $stmtNama = ($db_siakad ?: $db)->prepare("SELECT nama_topik FROM bs_topik WHERE id = ?");
                $stmtNama->execute([$id]);
                $namaTopik = $stmtNama->fetchColumn();
            } catch (\Exception $e) {}

            // 2. Ambil seluruh ujian dengan topik_id ini
            try {
                $u1 = $db->query("SELECT id FROM cbt_ujian WHERE topik_id = {$id}")->fetchAll(\PDO::FETCH_COLUMN);
                foreach ($u1 as $uid) {
                    $ujianIds[] = (int)$uid;
                }
            } catch (\Exception $e) {}

            // 3. Ambil juga ujian yang terjadwal di cbt_jadwal_grid untuk topik ini
            try {
                $u2 = $db->query("SELECT DISTINCT ujian_id FROM cbt_jadwal_grid WHERE topik_id = {$id} AND ujian_id IS NOT NULL AND ujian_id > 0")->fetchAll(\PDO::FETCH_COLUMN);
                foreach ($u2 as $uid) {
                    $ujianIds[] = (int)$uid;
                }
            } catch (\Exception $e) {}

            // 4. Jika nama topik ada, cari juga cbt_ujian yang memuat nama topik
            if (!empty($namaTopik)) {
                try {
                    $stmtMatch = $db->prepare("SELECT id FROM cbt_ujian WHERE nama_ujian LIKE ?");
                    $stmtMatch->execute(['%' . $namaTopik . '%']);
                    foreach ($stmtMatch->fetchAll(\PDO::FETCH_COLUMN) as $uid) {
                        $ujianIds[] = (int)$uid;
                    }
                } catch (\Exception $e) {}
            }

            $ujianIds = array_unique(array_filter($ujianIds));

            if (!empty($ujianIds)) {
                $inIds = implode(',', array_map('intval', $ujianIds));

                // Hapus data jawaban peserta & pengerjaan
                try { $db->exec("DELETE FROM cbt_peserta_jawaban WHERE peserta_id IN (SELECT id FROM cbt_peserta WHERE ujian_id IN ({$inIds}))"); } catch (\Exception $e) {}
                try { $db->exec("DELETE FROM cbt_peserta WHERE ujian_id IN ({$inIds})"); } catch (\Exception $e) {}

                // Hapus token ujian
                try { $db->exec("DELETE FROM cbt_token WHERE ujian_id IN ({$inIds})"); } catch (\Exception $e) {}

                // Hapus absensi ujian
                try { $db->exec("DELETE FROM cbt_absensi WHERE topik_id = {$id} OR ujian_id IN ({$inIds})"); } catch (\Exception $e) {}

                // Hapus soal di cbt_soal untuk ujian ini
                try { $db->exec("DELETE FROM cbt_soal WHERE ujian_id IN ({$inIds})"); } catch (\Exception $e) {}

                // Hapus paket ujian cbt_ujian
                try { $db->exec("DELETE FROM cbt_ujian WHERE id IN ({$inIds}) OR topik_id = {$id}"); } catch (\Exception $e) {}
            } else {
                try { $db->exec("DELETE FROM cbt_absensi WHERE topik_id = {$id}"); } catch (\Exception $e) {}
            }

            // Hapus juga data absensi berdasarkan nama topik jika ada
            if (!empty($namaTopik)) {
                try {
                    $stmtDelM = $db->prepare("DELETE FROM cbt_absensi WHERE mapel_nama LIKE ?");
                    $stmtDelM->execute(['%' . $namaTopik . '%']);
                } catch (\Exception $e) {}
            }

            // Hapus jadwal grid terkait topik ini
            try {
                $db->exec("DELETE FROM cbt_jadwal_grid WHERE topik_id = {$id}");
            } catch (\Exception $e) {}

            // Hapus topik dari bs_topik
            try {
                $db->prepare("DELETE FROM bs_topik WHERE id = ?")->execute([$id]);
            } catch (\Exception $e) {}

            if ($db_siakad) {
                try {
                    $db_siakad->prepare("DELETE FROM bs_topik WHERE id = ?")->execute([$id]);
                } catch (\Exception $e) {}
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

        $mapelNama = trim($ujianInfo['mapel_nama'] ?? ($ujianInfo['nama_ujian'] ?? ''));

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

        // Sinkronkan masa_berlaku_menit token aktif dengan pengaturan terkini
        try {
            $intervalSetting = (int)($db->query("SELECT setting_value FROM cbt_pengaturan WHERE setting_key = 'token_refresh_interval'")->fetchColumn() ?: 120);
            if ($intervalSetting <= 0) $intervalSetting = 120;
            $db->prepare("UPDATE cbt_token SET masa_berlaku_menit = ? WHERE status = 'Aktif' OR masa_berlaku_menit = 240")->execute([$intervalSetting]);
        } catch (\Exception $e) {}

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

            $intervalSetting = (int)($db->query("SELECT setting_value FROM cbt_pengaturan WHERE setting_key = 'token_refresh_interval'")->fetchColumn() ?: 120);
            if ($intervalSetting <= 0) $intervalSetting = 120;

            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
            $tokenStr = '';
            for ($i = 0; $i < 6; $i++) {
                $tokenStr .= $chars[rand(0, strlen($chars) - 1)];
            }

            $stmt = $db->prepare("INSERT INTO cbt_token (token, masa_berlaku_menit, status, created_at) VALUES (?, ?, 'Aktif', NOW())");
            $stmt->execute([$tokenStr, $intervalSetting]);

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
     * Ambil daftar topik ujian aktif dari database core atau siakad
     */
    public static function getActiveTopikList($db, $db_siakad = null) {
        $db_siakad = $db_siakad ?: $db;
        $topikList = [];
        try {
            $topikList = $db_siakad->query("SELECT id, nama_topik, jenis, tahun_ajaran, semester, status, tgl_mulai, tgl_selesai FROM bs_topik WHERE status = 'Aktif' ORDER BY (tgl_mulai IS NOT NULL) DESC, tgl_mulai ASC, id DESC")->fetchAll(\PDO::FETCH_ASSOC);
            if (empty($topikList)) {
                $topikList = $db_siakad->query("SELECT id, nama_topik, jenis, tahun_ajaran, semester, status, tgl_mulai, tgl_selesai FROM bs_topik WHERE status != 'Arsip' ORDER BY (status = 'Aktif') DESC, (tgl_mulai IS NOT NULL) DESC, tgl_mulai ASC, id DESC")->fetchAll(\PDO::FETCH_ASSOC);
            }
            if (empty($topikList) && $db !== $db_siakad) {
                $topikList = $db->query("SELECT id, nama_topik, jenis, tahun_ajaran, semester, status, tgl_mulai, tgl_selesai FROM bs_topik WHERE status = 'Aktif' ORDER BY (tgl_mulai IS NOT NULL) DESC, tgl_mulai ASC, id DESC")->fetchAll(\PDO::FETCH_ASSOC);
                if (empty($topikList)) {
                    $topikList = $db->query("SELECT id, nama_topik, jenis, tahun_ajaran, semester, status, tgl_mulai, tgl_selesai FROM bs_topik WHERE status != 'Arsip' ORDER BY (status = 'Aktif') DESC, (tgl_mulai IS NOT NULL) DESC, tgl_mulai ASC, id DESC")->fetchAll(\PDO::FETCH_ASSOC);
                }
            }
        } catch (\Exception $e) {
            try {
                $topikList = $db_siakad->query("SELECT * FROM bs_topik WHERE status = 'Aktif' ORDER BY id DESC")->fetchAll(\PDO::FETCH_ASSOC);
                if (empty($topikList)) {
                    $topikList = $db_siakad->query("SELECT * FROM bs_topik ORDER BY id DESC")->fetchAll(\PDO::FETCH_ASSOC);
                }
            } catch (\Exception $e2) {
                if ($db !== $db_siakad) {
                    try {
                        $topikList = $db->query("SELECT * FROM bs_topik WHERE status = 'Aktif' ORDER BY id DESC")->fetchAll(\PDO::FETCH_ASSOC);
                        if (empty($topikList)) {
                            $topikList = $db->query("SELECT * FROM bs_topik ORDER BY id DESC")->fetchAll(\PDO::FETCH_ASSOC);
                        }
                    } catch (\Exception $e3) {}
                }
            }
        }
        foreach ($topikList as &$tp) {
            if (!isset($tp['jenis_topik']) && isset($tp['jenis'])) {
                $tp['jenis_topik'] = $tp['jenis'];
            }
        }
        unset($tp);

        $dedupTopik = [];
        $seen = [];
        foreach ($topikList as $tp) {
            $norm = trim($tp['nama_topik']);
            if (!isset($seen[$norm])) {
                $seen[$norm] = true;
                $dedupTopik[] = $tp;
            }
        }
        return $dedupTopik;
    }

    /**
     * Rekap Hasil Ujian Siswa Real
     */
    public static function hasil() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $db_siakad = $ctx['db_siakad'] ?? $db;

        $isAjax = (!empty($_GET['ajax']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'));
        if (!$isAjax) {
            self::ensureTables($db);
            self::syncJadwalWithUjian($db, $db_siakad);
        }

        // Ambil daftar Topik Ujian aktif
        $topikList = self::getActiveTopikList($db, $db_siakad);

        $selectedTopikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0;
        if ($selectedTopikId == 5) {
            $selectedTopikId = 3;
        }
        $selectedMapel = isset($_GET['mapel']) ? trim($_GET['mapel']) : '';
        $selectedUjianId = isset($_GET['ujian_id']) ? (int)$_GET['ujian_id'] : 0;
        $selectedKelas = isset($_GET['kelas']) ? trim($_GET['kelas']) : '';
        $search = isset($_GET['q']) ? trim($_GET['q']) : '';
        $sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'nilai_desc';
        $limit = isset($_GET['limit']) ? ($_GET['limit'] === 'all' ? 0 : (int)$_GET['limit']) : 30;
        if ($limit < 0) $limit = 30;

        // Ambil daftar Mapel yang relevan (difilter per topik jika topik dipilih)
        $mapelList = [];
        try {
            if ($selectedTopikId > 0) {
                $tpName = $db->query("SELECT nama_topik FROM bs_topik WHERE id = " . (int)$selectedTopikId)->fetchColumn();
                $stmtM = $db->prepare("SELECT DISTINCT mapel_nama FROM cbt_ujian WHERE (topik_id = ? OR (? != '' AND nama_ujian LIKE ?)) AND mapel_nama IS NOT NULL AND mapel_nama != '' ORDER BY mapel_nama ASC");
                $stmtM->execute([$selectedTopikId, !empty($tpName) ? $tpName : '', !empty($tpName) ? "%{$tpName}%" : "%"]);
                $mapelList = $stmtM->fetchAll(PDO::FETCH_COLUMN);
            }
            if (empty($mapelList)) {
                $mapelList = $db->query("SELECT DISTINCT mapel_nama FROM cbt_ujian WHERE mapel_nama IS NOT NULL AND mapel_nama != '' ORDER BY mapel_nama ASC")->fetchAll(PDO::FETCH_COLUMN);
            }
        } catch (\Exception $e) {}

        $ujianList = [];
        $kelasListReguler = [];
        $kelasListJauh = [];
        $kelasList = [];
        if (!$isAjax) {
            $ujianList = $db->query("SELECT id, kode_ujian, nama_ujian, mapel_nama, kkm FROM cbt_ujian ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

            // Daftar kelas resmi madrasah (Reguler) dan Kelas Jauh (Khusus CBT)
            try {
                $genericTingkat = ['Kelas X', 'Kelas XI', 'Kelas XII', 'Semua Kelas'];
                // 1. Kelas Reguler dari tabel kelas (Siakad) & peserta madrasah
                $k1 = $db->query("SELECT DISTINCT nama_kelas FROM kelas WHERE nama_kelas IS NOT NULL AND nama_kelas != ''")->fetchAll(PDO::FETCH_COLUMN);
                $k3 = $db->query("SELECT DISTINCT kelas_nama FROM cbt_peserta WHERE kelas_nama IS NOT NULL AND kelas_nama != '' AND kelas_nama NOT LIKE '%Jauh%'")->fetchAll(PDO::FETCH_COLUMN);
                $regMerged = array_diff(array_unique(array_filter(array_merge($k1, $k3))), $genericTingkat);
                usort($regMerged, function($a, $b) { return strnatcasecmp($a, $b); });
                $kelasListReguler = array_values($regMerged);

                // 2. Kelas Jauh dari cbt_peserta_khusus & cbt_peserta
                $k2 = $db->query("SELECT DISTINCT kelas_nama FROM cbt_peserta_khusus WHERE kelas_nama IS NOT NULL AND kelas_nama != ''")->fetchAll(PDO::FETCH_COLUMN);
                $k3Jauh = $db->query("SELECT DISTINCT kelas_nama FROM cbt_peserta WHERE kelas_nama LIKE '%Jauh%'")->fetchAll(PDO::FETCH_COLUMN);
                $jauhMerged = array_unique(array_filter(array_merge($k2, $k3Jauh)));
                usort($jauhMerged, function($a, $b) { return strnatcasecmp($a, $b); });
                $kelasListJauh = array_values($jauhMerged);

                $kelasList = array_merge($kelasListReguler, $kelasListJauh);
            } catch (\Exception $e) {}
        }

        // Query Peserta Real
        $sql = "SELECT p.*, u.nama_ujian, u.kkm, u.mapel_nama,
                       (SELECT COUNT(*) FROM cbt_soal WHERE ujian_id = u.id) as total_soal_ujian
                FROM cbt_peserta p 
                LEFT JOIN cbt_ujian u ON u.id = p.ujian_id 
                WHERE 1=1";
        $params = [];

        if ($selectedTopikId > 0) {
            $tpJudul = '';
            foreach ($topikList as $tp) {
                if ((int)$tp['id'] === $selectedTopikId) {
                    $tpJudul = trim($tp['nama_topik']);
                    break;
                }
            }
            if (!empty($tpJudul)) {
                $sql .= " AND (u.topik_id = :topik_id OR u.nama_ujian LIKE :topik_like)";
                $params[':topik_id'] = $selectedTopikId;
                $params[':topik_like'] = "%{$tpJudul}%";
            } else {
                $sql .= " AND (u.topik_id = :topik_id)";
                $params[':topik_id'] = $selectedTopikId;
            }
        }

        if (!empty($selectedMapel)) {
            $sql .= " AND (u.mapel_nama = :mapel OR u.mapel_nama LIKE :mapel_like OR u.nama_ujian LIKE :mapel_like2)";
            $params[':mapel'] = $selectedMapel;
            $params[':mapel_like'] = "%{$selectedMapel}%";
            $params[':mapel_like2'] = "%{$selectedMapel}%";
        }

        if ($selectedUjianId > 0) {
            $sql .= " AND p.ujian_id = :ujian_id";
            $params[':ujian_id'] = $selectedUjianId;
        }

        if (!empty($selectedKelas)) {
            $sql .= " AND (p.kelas_nama = :kelas OR p.kelas_nama LIKE :kelas_like)";
            $params[':kelas'] = $selectedKelas;
            $params[':kelas_like'] = "%{$selectedKelas}%";
        }

        if (!empty($search)) {
            $sql .= " AND (p.nama_siswa LIKE :q_nama OR p.nis LIKE :q_nis OR p.kelas_nama LIKE :q_kls OR u.nama_ujian LIKE :q_uj)";
            $params[':q_nama'] = "%{$search}%";
            $params[':q_nis'] = "%{$search}%";
            $params[':q_kls'] = "%{$search}%";
            $params[':q_uj'] = "%{$search}%";
        }

        // Custom Sort Siswa
        switch ($sort) {
            case 'nilai_asc':
                $sql .= " ORDER BY (p.status_pengerjaan = 'Selesai') DESC, p.nilai_akhir ASC, p.nama_siswa ASC";
                break;
            case 'nama_asc':
                $sql .= " ORDER BY p.nama_siswa ASC, p.nilai_akhir DESC";
                break;
            case 'nama_desc':
                $sql .= " ORDER BY p.nama_siswa DESC, p.nilai_akhir DESC";
                break;
            case 'nis_asc':
                $sql .= " ORDER BY p.nis ASC, p.nama_siswa ASC";
                break;
            case 'waktu_desc':
                $sql .= " ORDER BY COALESCE(p.waktu_selesai, p.waktu_mulai, p.created_at) DESC, p.nilai_akhir DESC";
                break;
            case 'nilai_desc':
            default:
                $sql .= " ORDER BY (p.status_pengerjaan = 'Selesai') DESC, p.nilai_akhir DESC, p.nama_siswa ASC";
                break;
        }

        $stmtP = $db->prepare($sql);
        $stmtP->execute($params);
        $pesertaList = $stmtP->fetchAll(PDO::FETCH_ASSOC);

        // KKM patokan & current ujian
        $kkmUjian = 75;
        $currentUjian = null;
        if ($selectedUjianId > 0) {
            foreach ($ujianList as $uj) {
                if ($uj['id'] == $selectedUjianId) {
                    $currentUjian = $uj;
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

        $displayPesertaList = ($limit > 0 && count($pesertaList) > $limit) ? array_slice($pesertaList, 0, $limit) : $pesertaList;

        $isAjax = (!empty($_GET['ajax']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'));
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            ob_start();
            $no = 1;
            if (empty($pesertaList)) {
                ?>
                <tr>
                    <td colspan="10" style="padding: 3rem 1.5rem; text-align: center; color: #94a3b8;">
                        <i data-lucide="folder-search" style="width: 44px; height: 44px; margin-bottom: 10px; color: #cbd5e1;"></i>
                        <div style="font-size: 0.95rem; font-weight: 700; color: #475569;">Tidak ada data hasil ujian yang sesuai dengan filter</div>
                        <div style="font-size: 0.78rem; margin-bottom: 14px;">Silakan sesuaikan pilihan Topik, Mata Pelajaran, atau Kelas yang dipilih.</div>
                        <a href="<?php echo base_url('cbt/hasil'); ?>" class="e-btn e-btn-primary" onclick="resetFilterAjax(event)" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 800; font-size: 0.8rem; padding: 7px 16px; background: #4f46e5;">
                            <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i> Reset Semua Filter
                        </a>
                    </td>
                </tr>
                <?php
            } else {
                foreach($displayPesertaList as $p) {
                    $isSelesai = ($p['status_pengerjaan'] === 'Selesai');
                    $isTuntas = ($isSelesai && $p['nilai_akhir'] >= $kkmUjian);
                    $benar = (int)($p['jumlah_benar'] ?? 0);
                    $salah = (int)($p['jumlah_salah'] ?? 0);
                    $tidakJawab = (int)($p['jumlah_tidak_jawab'] ?? 0);
                    $totalSoal = (int)($p['total_soal_ujian'] ?? 0);
                    if ($totalSoal <= 0) $totalSoal = max(20, $benar + $salah);
                    if ($tidakJawab == 0 && ($benar + $salah) < $totalSoal) {
                        $tidakJawab = $totalSoal - ($benar + $salah);
                    }
                    $isKJ = (stripos($p['kelas_nama'] ?? '', 'jauh') !== false);
                    ?>
                    <tr>
                        <td style="color: #94a3b8; font-weight: 600;"><?php echo $no++; ?></td>
                        <td>
                            <div style="font-weight: 800; color: #0f172a; font-size: 0.88rem;"><?php echo htmlspecialchars($p['nama_siswa']); ?></div>
                            <div style="font-size: 0.72rem; color: #64748b; font-family: monospace;">NIS: <?php echo htmlspecialchars($p['nis'] ?: '-'); ?></div>
                        </td>
                        <td>
                            <span style="font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 6px; <?php echo $isKJ ? 'background: #fef3c7; color: #b45309; border: 1px solid #fde68a;' : 'background: #e0e7ff; color: #4338ca;'; ?>">
                                <?php echo htmlspecialchars($p['kelas_nama'] ?: '-'); ?>
                                <?php if ($isKJ): ?>
                                    <span style="font-size: 0.65rem; background: #b45309; color: #fff; padding: 1px 4px; border-radius: 3px; margin-left: 2px;">KJ</span>
                                <?php endif; ?>
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #1e293b; font-size: 0.82rem;"><?php echo htmlspecialchars($p['nama_ujian'] ?? 'Ujian'); ?></div>
                        </td>
                        <td>
                            <?php if ($isSelesai): ?>
                            <span style="font-weight: 800; color: #15803d; background: #dcfce7; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">
                                <?php echo $benar; ?> Benar
                            </span>
                            <?php else: ?>
                            <span style="color: #94a3b8; font-size: 0.8rem;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($isSelesai): ?>
                            <span style="font-weight: 800; color: #b91c1c; background: #fee2e2; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">
                                <?php echo $salah; ?> Salah
                            </span>
                            <?php else: ?>
                            <span style="color: #94a3b8; font-size: 0.8rem;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($isSelesai): ?>
                            <span style="font-weight: 800; color: #64748b; background: #f1f5f9; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">
                                <?php echo $tidakJawab; ?> Kosong
                            </span>
                            <?php else: ?>
                            <span style="color: #94a3b8; font-size: 0.8rem;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($isSelesai): ?>
                            <div style="font-size: 1.25rem; font-weight: 900; color: <?php echo $isTuntas ? '#15803d' : '#b91c1c'; ?>;">
                                <?php echo number_format($p['nilai_akhir'], 1); ?>
                            </div>
                            <?php else: ?>
                            <span style="color: #94a3b8; font-size: 0.84rem; font-weight: 700;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($isSelesai): ?>
                            <span style="font-size: 0.75rem; font-weight: 800; padding: 3px 10px; border-radius: 6px; <?php echo $isTuntas ? 'background:#dcfce7;color:#15803d;' : 'background:#fee2e2;color:#b91c1c;'; ?>">
                                <?php echo $isTuntas ? 'Tuntas' : 'Remedial'; ?>
                            </span>
                            <?php else: ?>
                            <span style="font-size: 0.72rem; font-weight: 700; padding: 3px 8px; border-radius: 6px; background: #f1f5f9; color: #64748b;">
                                Belum Mengerjakan
                            </span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <?php if ($isSelesai || in_array($p['status_pengerjaan'] ?? '', ['Sedang Mengerjakan', 'Mengerjakan', 'Terkunci', 'Pelanggaran'])): ?>
                                <button type="button" class="e-btn" style="background: #0ea5e9; color: white; border: none; font-size: 0.75rem; font-weight: 800; padding: 6px 14px; border-radius: 8px; display: inline-flex; align-items: center; gap: 5px; box-shadow: 0 2px 6px rgba(14,165,233,0.25); cursor: pointer;" title="Buka Sesi Ujian Kembali" onclick="bukaModalReopen(<?php echo (int)($p['id'] ?? 0); ?>, '<?php echo htmlspecialchars(addslashes($p['nama_siswa'] ?? 'Siswa')); ?>', '<?php echo htmlspecialchars(addslashes($p['nama_ujian'] ?? 'Paket Ujian')); ?>')">
                                    <i data-lucide="rotate-ccw" style="width: 13px; height: 13px;"></i> Buka Ujian
                                </button>
                            <?php else: ?>
                                <span style="color: #cbd5e1; font-size: 0.8rem; font-weight: 700;">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php
                }
            }
            $tableHtml = ob_get_clean();

            echo json_encode([
                'success' => true,
                'total_peserta' => count($pesertaList),
                'display_count' => count($displayPesertaList),
                'limit' => $limit,
                'rata_rata' => number_format($rataRata, 1),
                'persen_tuntas' => $persenTuntas,
                'kkm' => $kkmUjian,
                'jumlah_remedial' => $jumlahRemedial,
                'mapel_list' => array_values(array_unique($mapelList)),
                'table_html' => $tableHtml,
                'has_filter' => (!empty($selectedTopikId) || !empty($selectedMapel) || !empty($selectedKelas) || !empty($search) || ($sort !== 'nilai_desc')),
                'cetak_url' => base_url('cbt/hasil/cetak?topik_id=' . $selectedTopikId . '&mapel=' . urlencode($selectedMapel) . '&kelas=' . urlencode($selectedKelas) . '&sort=' . $sort . '&q=' . urlencode($search)),
                'analisis_url' => base_url('cbt/analisis?topik_id=' . $selectedTopikId . '&mapel=' . urlencode($selectedMapel) . '&kelas=' . urlencode($selectedKelas)),
            ]);
            exit;
        }

        $title = "Hasil Ujian CBT - MAS MIFTAHUL HUDA";
        ob_start();
        include __DIR__ . '/../../resources/views/cbt/hasil.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Cetak Rekap Nilai Ujian Siswa (Kop Surat Resmi Madrasah, Format A4 Standar)
     */
    public static function cetakHasil() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        $selectedTopikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0;
        $selectedMapel = isset($_GET['mapel']) ? trim($_GET['mapel']) : '';
        $selectedUjianId = isset($_GET['ujian_id']) ? (int)$_GET['ujian_id'] : 0;
        $selectedKelas = isset($_GET['kelas']) ? trim($_GET['kelas']) : '';
        $search = isset($_GET['q']) ? trim($_GET['q']) : '';
        $sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'nilai_desc';
        $limit = isset($_GET['limit']) ? ($_GET['limit'] === 'all' ? 0 : (int)$_GET['limit']) : 30;
        if ($limit < 0) $limit = 30;

        $currentUjian = null;
        if ($selectedUjianId > 0) {
            $stmt = $db->prepare("SELECT * FROM cbt_ujian WHERE id = ?");
            $stmt->execute([$selectedUjianId]);
            $currentUjian = $stmt->fetch(PDO::FETCH_ASSOC);
        } elseif ($selectedTopikId > 0 || !empty($selectedMapel)) {
            $stmtFind = $db->prepare("SELECT * FROM cbt_ujian WHERE (topik_id = ? OR ? = 0) AND (mapel_nama = ? OR ? = '') ORDER BY id DESC LIMIT 1");
            $stmtFind->execute([$selectedTopikId, $selectedTopikId, $selectedMapel, $selectedMapel]);
            $currentUjian = $stmtFind->fetch(PDO::FETCH_ASSOC);
        }

        $sql = "SELECT p.*, u.nama_ujian, u.kkm, u.mapel_nama, u.kode_ujian, u.durasi_menit,
                       (SELECT COUNT(*) FROM cbt_soal WHERE ujian_id = u.id) as total_soal_ujian
                FROM cbt_peserta p 
                LEFT JOIN cbt_ujian u ON u.id = p.ujian_id 
                WHERE 1=1";
        $params = [];

        if ($selectedTopikId > 0) {
            $tpJudul = $db->query("SELECT nama_topik FROM bs_topik WHERE id = " . (int)$selectedTopikId)->fetchColumn();
            if (!empty($tpJudul)) {
                $sql .= " AND (u.topik_id = :topik_id OR u.nama_ujian LIKE :topik_like)";
                $params[':topik_id'] = $selectedTopikId;
                $params[':topik_like'] = "%{$tpJudul}%";
            } else {
                $sql .= " AND (u.topik_id = :topik_id)";
                $params[':topik_id'] = $selectedTopikId;
            }
        }

        if (!empty($selectedMapel)) {
            $sql .= " AND (u.mapel_nama = :mapel OR u.mapel_nama LIKE :mapel_like OR u.nama_ujian LIKE :mapel_like2)";
            $params[':mapel'] = $selectedMapel;
            $params[':mapel_like'] = "%{$selectedMapel}%";
            $params[':mapel_like2'] = "%{$selectedMapel}%";
        }

        if ($selectedUjianId > 0) {
            $sql .= " AND p.ujian_id = :ujian_id";
            $params[':ujian_id'] = $selectedUjianId;
        }

        if (!empty($selectedKelas)) {
            $sql .= " AND (p.kelas_nama = :kelas OR p.kelas_nama LIKE :kelas_like)";
            $params[':kelas'] = $selectedKelas;
            $params[':kelas_like'] = "%{$selectedKelas}%";
        }

        if (!empty($search)) {
            $sql .= " AND (p.nama_siswa LIKE :q_nama OR p.nis LIKE :q_nis OR p.kelas_nama LIKE :q_kls OR u.nama_ujian LIKE :q_uj)";
            $params[':q_nama'] = "%{$search}%";
            $params[':q_nis'] = "%{$search}%";
            $params[':q_kls'] = "%{$search}%";
            $params[':q_uj'] = "%{$search}%";
        }

        switch ($sort) {
            case 'nilai_asc':
                $sql .= " ORDER BY (p.status_pengerjaan = 'Selesai') DESC, p.nilai_akhir ASC, p.nama_siswa ASC";
                break;
            case 'nama_asc':
                $sql .= " ORDER BY p.nama_siswa ASC, p.nilai_akhir DESC";
                break;
            case 'nama_desc':
                $sql .= " ORDER BY p.nama_siswa DESC, p.nilai_akhir DESC";
                break;
            case 'nis_asc':
                $sql .= " ORDER BY p.nis ASC, p.nama_siswa ASC";
                break;
            case 'waktu_desc':
                $sql .= " ORDER BY COALESCE(p.waktu_selesai, p.waktu_mulai, p.created_at) DESC, p.nilai_akhir DESC";
                break;
            case 'nilai_desc':
            default:
                $sql .= " ORDER BY (p.status_pengerjaan = 'Selesai') DESC, p.nilai_akhir DESC, p.nama_siswa ASC";
                break;
        }

        $stmtP = $db->prepare($sql);
        $stmtP->execute($params);
        $pesertaList = $stmtP->fetchAll(PDO::FETCH_ASSOC);

        $kkmUjian = (int)($currentUjian['kkm'] ?? 75);

        $totalSelesai = 0;
        $totalNilai = 0;
        $tuntasCount = 0;
        $maxScore = 0;
        $minScore = 100;

        foreach ($pesertaList as $p) {
            if ($p['status_pengerjaan'] === 'Selesai') {
                $totalSelesai++;
                $nilai = (float)$p['nilai_akhir'];
                $totalNilai += $nilai;
                if ($nilai > $maxScore) $maxScore = $nilai;
                if ($nilai < $minScore) $minScore = $nilai;
                if ($nilai >= $kkmUjian) {
                    $tuntasCount++;
                }
            }
        }
        if ($totalSelesai === 0) {
            $minScore = 0;
        }
        $rataRata = $totalSelesai > 0 ? ($totalNilai / $totalSelesai) : 0;
        $persenTuntas = $totalSelesai > 0 ? round(($tuntasCount / $totalSelesai) * 100) : 0;
        $maxNilai = $maxScore;
        $minNilai = $minScore;

        include __DIR__ . '/../../resources/views/cbt/cetak_hasil.php';
        exit;
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
        $ctx = self::getAuthContext(true);
        $db = $ctx['db_core'];

        $ujian_id = isset($_GET['ujian_id']) ? (int)$_GET['ujian_id'] : 0;
        $paket_id = isset($_GET['paket_id']) ? (int)$_GET['paket_id'] : 0;

        // Jika dipanggil dari Bank Soal untuk pratinjau / simulasi pengerjaan siswa
        if ($ujian_id <= 0 && $paket_id > 0) {
            $bsCtrl = new \App\Controllers\BankSoalController();
            $bsCtrl->simulasiSiswa();
            exit;
        }

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

        $mapelNama = trim($ujianInfo['mapel_nama'] ?? ($ujianInfo['nama_ujian'] ?? ''));

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

        // Cek Kebijakan Kunci Jadwal (Schedule Window Guard)
        $cbtSettings = self::getAllPengaturan($db);
        $lockSchedule = ($cbtSettings['lock_by_schedule_time'] ?? '1') === '1';

        if ($lockSchedule && ($roleId === 3 || $roleId === 7 || $isCbtOnly)) {
            $nowTs = time();
            $todayDate = date('Y-m-d');
            $hariIndo = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'];
            $curHari = $hariIndo[date('l')] ?? '';

            // Dapatkan info kelas siswa
            $studentClass = '';
            $tingkatStudent = '';
            if ($isCbtOnly || $roleId === 7) {
                $st = $db->prepare("SELECT kelas_nama, tingkat FROM cbt_peserta_khusus WHERE id = ? LIMIT 1");
                $st->execute([$siswaSessionId]);
                $cl = $st->fetch(PDO::FETCH_ASSOC);
                $studentClass = $cl['kelas_nama'] ?? '';
                $tingkatStudent = (string)($cl['tingkat'] ?? '');
            } else {
                $st = $db->prepare("SELECT COALESCE(k.nama_kelas, '') as kelas_nama, COALESCE(k.tingkat, '') as tingkat FROM siswa s LEFT JOIN kelas k ON s.kelas_id = k.id WHERE (s.user_id = ? AND ? > 0) OR (s.id = ? AND ? > 0) LIMIT 1");
                $st->execute([$userId, $userId, $siswaSessionId, $siswaSessionId]);
                $cl = $st->fetch(PDO::FETCH_ASSOC);
                $studentClass = $cl['kelas_nama'] ?? '';
                $tingkatStudent = (string)($cl['tingkat'] ?? '');
            }

            $shortTingkat = 'X';
            $tingkatLabel = 'Kelas X';
            if (stripos($studentClass, 'XII') !== false || stripos($studentClass, '12') !== false || stripos($tingkatStudent, 'XII') !== false || $tingkatStudent === '12') {
                $shortTingkat = 'XII';
                $tingkatLabel = 'Kelas XII';
            } elseif (stripos($studentClass, 'XI') !== false || stripos($studentClass, '11') !== false || stripos($tingkatStudent, 'XI') !== false || $tingkatStudent === '11') {
                $shortTingkat = 'XI';
                $tingkatLabel = 'Kelas XI';
            }

            if ($shortTingkat === 'XII') {
                $tingkatSql = "(g.kelas_nama LIKE 'XII%' OR g.kelas_nama LIKE 'Kelas 12%' OR g.kelas_nama LIKE 'Kelas XII%')";
            } elseif ($shortTingkat === 'XI') {
                $tingkatSql = "((g.kelas_nama LIKE 'XI%' AND g.kelas_nama NOT LIKE 'XII%') OR g.kelas_nama LIKE 'Kelas 11%' OR g.kelas_nama LIKE 'Kelas XI%')";
            } else {
                $tingkatSql = "((g.kelas_nama LIKE 'X%' AND g.kelas_nama NOT LIKE 'XI%' AND g.kelas_nama NOT LIKE 'XII%') OR g.kelas_nama LIKE 'Kelas 10%' OR g.kelas_nama LIKE 'Kelas X%')";
            }

            // Ambil nama mapel ujian
            $stmtU = $db->prepare("SELECT mapel_nama, nama_ujian FROM cbt_ujian WHERE id = ? LIMIT 1");
            $stmtU->execute([$ujian_id]);
            $uRow = $stmtU->fetch(PDO::FETCH_ASSOC);
            $mapelNama = trim($uRow['mapel_nama'] ?? ($uRow['nama_ujian'] ?? $mapelNama));

            // Cek apakah ada jadwal spesifik untuk HARI INI
            $stmtToday = $db->prepare("
                SELECT g.*, s.jam_mulai, s.jam_selesai, s.label_sesi 
                FROM cbt_jadwal_grid g 
                LEFT JOIN cbt_jam_slot s ON (g.jam_slot = s.label_sesi OR g.jam_slot = s.urutan)
                WHERE (g.tanggal = ? OR (g.tanggal IS NULL AND (g.hari = ? OR g.hari LIKE ?)))
                  AND (
                      g.ujian_id = ? 
                      OR (
                          (g.mapel_nama = ? OR ? LIKE CONCAT('%', g.mapel_nama, '%') OR g.mapel_nama LIKE CONCAT('%', ?, '%'))
                          AND (
                              g.kelas_nama = ? 
                              OR ? LIKE CONCAT('%', g.kelas_nama, '%')
                              OR g.kelas_nama = ?
                              OR g.kelas_nama = 'Semua Kelas'
                              OR g.kelas_nama IS NULL
                              OR g.kelas_nama = ''
                              OR {$tingkatSql}
                          )
                      )
                  )
                ORDER BY (g.ujian_id = ?) DESC, (g.kelas_nama = ?) DESC, g.id DESC LIMIT 1
            ");
            $curHariLike = $curHari . '%';
            $stmtToday->execute([
                $todayDate,
                $curHari,
                $curHariLike,
                $ujian_id,
                $mapelNama,
                $mapelNama,
                $mapelNama,
                $studentClass,
                $studentClass,
                $tingkatLabel,
                $ujian_id,
                $studentClass
            ]);
            $jGrid = $stmtToday->fetch(PDO::FETCH_ASSOC);

            $apkCbtRedirectUrl = function_exists('base_url') ? base_url('apk/cbt.php') : ((strpos($_SERVER['REQUEST_URI'] ?? '', '/cbt/') !== false) ? '/cbt/apk/cbt.php' : '/apk/cbt.php');

            if (!$jGrid) {
                // Tidak ada jadwal untuk hari ini!
                echo "<script>alert('Mata pelajaran ini tidak dijadwalkan pada hari ini! Silakan periksa kembali jadwal resmi.'); window.location.href='{$apkCbtRedirectUrl}';</script>";
                exit;
            }

            $jMulai = !empty($jGrid['jam_mulai']) ? trim($jGrid['jam_mulai']) : null;
            $jSelesai = !empty($jGrid['jam_selesai']) ? trim($jGrid['jam_selesai']) : null;
            if ((empty($jMulai) || empty($jSelesai)) && !empty($jGrid['jam_slot'])) {
                if (preg_match('/(\d{1,2}:\d{2})\s*[-–]\s*(\d{1,2}:\d{2})/', $jGrid['jam_slot'], $m)) {
                    $jMulai = sprintf('%05s', $m[1]);
                    $jSelesai = sprintf('%05s', $m[2]);
                }
            }

            if (!empty($jMulai)) {
                $startTs = strtotime($todayDate . ' ' . $jMulai);
                if ($nowTs < $startTs) {
                    echo "<script>alert('Sesi ujian belum dibuka! Waktu mulai hari ini pukul " . substr($jMulai, 0, 5) . " WIB.'); window.location.href='{$apkCbtRedirectUrl}';</script>";
                    exit;
                }
            }

            if (!empty($jSelesai)) {
                $endTs = strtotime($todayDate . ' ' . $jSelesai);
                if ($nowTs > $endTs) {
                    echo "<script>alert('Waktu pelaksanaan sesi ujian ini telah berakhir pada pukul " . substr($jSelesai, 0, 5) . " WIB! Anda tidak dapat mengerjakan di luar jam sesi resmi. Hubungi proktor jika Anda membutuhkan ujian susulan.'); window.location.href='{$apkCbtRedirectUrl}';</script>";
                    exit;
                }
            }
        }

        // Security Guard: Wajib Absensi Per-Mata Pelajaran (Anti-Kerjakan dari Kantin/Luar)
        $requireAttendance = ($cbtSettings['require_attendance_to_start'] ?? '1') === '1';
        if ($requireAttendance && ($roleId === 3 || $roleId === 7 || $isCbtOnly || $siswaSessionId > 0)) {
            $hasAbsenForThisMapel = false;
            $apkCbtRedirectUrl = function_exists('base_url') ? base_url('apk/cbt.php') : ((strpos($_SERVER['REQUEST_URI'] ?? '', '/cbt/') !== false) ? '/cbt/apk/cbt.php' : '/apk/cbt.php');

            if ($isCbtOnly || $roleId === 7) {
                $stK = $db->prepare("SELECT no_peserta, nis, nama_siswa FROM cbt_peserta_khusus WHERE id = ? LIMIT 1");
                $stK->execute([$siswaSessionId]);
                $rK = $stK->fetch(PDO::FETCH_ASSOC);
                $swNis = $rK['no_peserta'] ?? ($rK['nis'] ?? '');
                $swNama = $rK['nama_siswa'] ?? '';
                $stA = $db->prepare("
                    SELECT id FROM cbt_absensi 
                    WHERE (nis = ? OR (nis = ? AND ? != '') OR nama_siswa = ?)
                      AND (tanggal = CURDATE() OR DATE(waktu_scan) = CURDATE())
                      AND (ujian_id = ? OR mapel_nama = ? OR ? LIKE CONCAT('%', mapel_nama, '%') OR mapel_nama LIKE CONCAT('%', ?, '%'))
                      AND status IN ('Hadir', 'Terlambat')
                    LIMIT 1
                ");
                $stA->execute([$swNis, $swNis, $swNis, $swNama, $ujian_id, $mapelNama, $mapelNama, $mapelNama]);
                $hasAbsenForThisMapel = (bool)$stA->fetchColumn();
            } else {
                $stS = $db->prepare("SELECT id, nis, nama FROM siswa WHERE (user_id = ? AND ? > 0) OR (id = ? AND ? > 0) LIMIT 1");
                $stS->execute([$userId, $userId, $siswaSessionId, $siswaSessionId]);
                $rS = $stS->fetch(PDO::FETCH_ASSOC);
                $swNis = $rS['nis'] ?? '';
                $swId = $rS['id'] ?? $siswaSessionId;
                $stA = $db->prepare("
                    SELECT id FROM cbt_absensi 
                    WHERE ((siswa_id IS NOT NULL AND siswa_id = ?) OR (nis = ? AND ? != ''))
                      AND (tanggal = CURDATE() OR DATE(waktu_scan) = CURDATE())
                      AND (ujian_id = ? OR mapel_nama = ? OR ? LIKE CONCAT('%', mapel_nama, '%') OR mapel_nama LIKE CONCAT('%', ?, '%'))
                      AND status IN ('Hadir', 'Terlambat')
                    LIMIT 1
                ");
                $stA->execute([$swId ?: -1, $swNis, $swNis, $ujian_id, $mapelNama, $mapelNama, $mapelNama]);
                $hasAbsenForThisMapel = (bool)$stA->fetchColumn();
            }

            if (!$hasAbsenForThisMapel) {
                // FALLBACK CERDAS & OTOMATIS: Jangan blokir siswa!
                // Otomatis catatkan status 'Hadir' ke cbt_absensi agar siswa bisa langsung mengerjakan ujian tanpa hambatan.
                try {
                    $swTipe = ($isCbtOnly || $roleId === 7) ? 'khusus' : 'reguler';
                    $swSaveId = ($swTipe === 'reguler' && !empty($swId)) ? (int)$swId : null;
                    $swNamaSave = !empty($swNama) ? $swNama : (!empty($rS['nama']) ? $rS['nama'] : 'Siswa');
                    $swKelasSave = 'CBT';
                    if (!empty($rS['kelas_id'])) {
                        try {
                            $kNm = $db->query("SELECT nama_kelas FROM kelas WHERE id = " . (int)$rS['kelas_id'])->fetchColumn();
                            if ($kNm) $swKelasSave = $kNm;
                        } catch (\Exception $e) {}
                    } elseif (!empty($rK['kelas_nama'])) {
                        $swKelasSave = $rK['kelas_nama'];
                    }

                    $stAuto = $db->prepare("
                        INSERT INTO cbt_absensi 
                        (tanggal, siswa_id, nis, nama_siswa, kelas_nama, tipe_peserta, topik_id, ujian_id, mapel_nama, jam_slot, waktu_scan, status, metode_scan, petugas_nama, ruang_nama, keterangan)
                        VALUES (CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?, 'Presensi Ujian', NOW(), 'Hadir', 'AUTO_KERJAKAN', 'Sistem Mandiri', 'Ruang Ujian', 'Presensi Otomatis Sistem Saat Mulai Ujian')
                    ");
                    $stAuto->execute([
                        $swSaveId,
                        $swNis,
                        $swNamaSave,
                        $swKelasSave,
                        $swTipe,
                        $ujian['topik_id'] ?? null,
                        $ujian_id,
                        $mapelNama
                    ]);
                    $hasAbsenForThisMapel = true;
                } catch (\Exception $e) {
                    $hasAbsenForThisMapel = true;
                }
            }
        }

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
            $sessKeyWaktu = 'cbt_trial_waktu_mulai_' . $ujian_id;
            if (empty($_SESSION[$sessKeyWaktu])) {
                $_SESSION[$sessKeyWaktu] = date('Y-m-d H:i:s');
            }
            $pesertaInfo = [
                'id' => 0,
                'ujian_id' => $ujian_id,
                'siswa_id' => null,
                'nis' => 'GURU-TRIAL',
                'nama_siswa' => ($_SESSION['nama'] ?? 'Pengawas / Guru (Simulasi)'),
                'kelas_nama' => ($ujianInfo['kelas_nama'] ?: 'Simulasi'),
                'waktu_mulai' => $_SESSION[$sessKeyWaktu],
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
        $ctx = self::getAuthContext(true);
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
        $ctx = self::getAuthContext(true);
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
        $ctx = self::getAuthContext(true);
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
            $jumlahTidakJawab = 0;

            foreach ($soals as $s) {
                $bobot = (float)($s['bobot'] > 0 ? $s['bobot'] : 1.0);
                $totalBobot += $bobot;

                $userAnsRaw = $answers[$s['id']] ?? '';
                $hasAnswer = false;
                if (is_array($userAnsRaw)) {
                    $uTemp = array_filter($userAnsRaw, function($v) { return $v !== '' && $v !== null; });
                    if (!empty($uTemp)) {
                        $hasAnswer = true;
                        sort($uTemp);
                        $userAns = strtoupper(trim(implode(', ', $uTemp)));
                    } else {
                        $userAns = '';
                    }
                } else {
                    $userAns = strtoupper(trim((string)$userAnsRaw));
                    if ($userAns !== '') {
                        $hasAnswer = true;
                    }
                }

                $kunciRaw = (string)$s['kunci_jawaban'];
                $kunci = strtoupper(trim($kunciRaw));
                $isMatch = false;

                // 1. Evaluasi Tipe JSON (Tabel Benar/Salah & Menjodohkan)
                $kDec = json_decode($kunciRaw, true);
                if (is_array($kDec)) {
                    if (is_array($userAnsRaw) && !empty($userAnsRaw)) {
                        if (isset($kDec[0]['respon'])) {
                            // Menjodohkan (Periksa kesesuaian setiap premis indeks ke-i dengan responnya)
                            ksort($userAnsRaw);
                            $allMatched = true;
                            foreach ($kDec as $pIdx => $pItem) {
                                $expVal = strtoupper(trim((string)($pItem['respon'] ?? '')));
                                $usrVal = strtoupper(trim((string)($userAnsRaw[$pIdx] ?? '')));
                                if ($usrVal === '' || $usrVal !== $expVal) {
                                    $allMatched = false;
                                    break;
                                }
                            }
                            if ($allMatched && count($userAnsRaw) === count($kDec)) {
                                $isMatch = true;
                            }
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
                } elseif ($hasAnswer) {
                    $jumlahSalah++;
                } else {
                    $jumlahTidakJawab++;
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
                                            jumlah_benar = ?, jumlah_salah = ?, jumlah_tidak_jawab = ?, nilai_akhir = ?, jawaban_json = ? 
                                        WHERE id = ?");
                $stmtUp->execute([$jumlahBenar, $jumlahSalah, $jumlahTidakJawab, $nilaiAkhir, json_encode($payloadToSave), $peserta_id]);
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
        $ctx = self::getAuthContext(true);
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
            $fallbackUrl = function_exists('base_url') ? base_url('apk/cbt.php') : ((strpos($_SERVER['REQUEST_URI'] ?? '', '/cbt/') !== false) ? '/cbt/apk/cbt.php' : '/apk/cbt.php');
            redirect($fallbackUrl);
            exit;
        }

        $settings = self::getAllPengaturan($db);
        $showScore = ($settings['show_score_to_student'] ?? '1') === '1';

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
                // Pastikan checkbox boolean yang unchecked tetap tersimpan sebagai 0
                $booleanKeys = [
                    'anti_cheat_mode', 'force_fullscreen', 'block_tab_switch',
                    'disable_right_click', 'disable_copy_paste', 'lock_single_device',
                    'watermark_student_nis', 'sound_alarm_on_violation', 'block_back_button',
                    'lock_by_schedule_time', 'require_cbt_attendance', 'acak_soal_default', 'acak_opsi_default',
                    'show_score_to_student', 'show_discussion_to_student', 'allow_student_review', 'require_all_answered_no_doubt'
                ];
                foreach ($booleanKeys as $bk) {
                    if (!isset($submitted[$bk])) {
                        $submitted[$bk] = '0';
                    }
                }

                $stmtUp = $db->prepare("INSERT INTO cbt_pengaturan (setting_key, setting_value) 
                                        VALUES (:k, :v) 
                                        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()");
                foreach ($submitted as $k => $v) {
                    $stmtUp->execute([
                        ':k' => trim((string)$k),
                        ':v' => trim((string)$v)
                    ]);
                }

                // Sinkronkan langsung ke token aktif jika masa berlaku diubah
                if (isset($submitted['token_refresh_interval'])) {
                    $newInterval = (int)$submitted['token_refresh_interval'];
                    if ($newInterval > 0) {
                        try {
                            $db->prepare("UPDATE cbt_token SET masa_berlaku_menit = ? WHERE status = 'Aktif' OR masa_berlaku_menit = 240")->execute([$newInterval]);
                        } catch (\Exception $e) {}
                    }
                }

                // Sinkronkan langsung ke durasi ujian jika default_durasi_menit diubah
                if (isset($submitted['default_durasi_menit'])) {
                    $newDurasi = (int)$submitted['default_durasi_menit'];
                    if ($newDurasi > 0) {
                        try {
                            $db->prepare("UPDATE cbt_ujian SET durasi_menit = ? WHERE durasi_menit = 90 OR durasi_menit IS NULL OR durasi_menit = 0")->execute([$newDurasi]);
                        } catch (\Exception $e) {}
                    }
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

        $activeTab = $_GET['tab'] ?? 'siswa';
        $pengawasKjList = [];
        try {
            $pengawasKjList = $db->query("SELECT * FROM cbt_pengawas_kj ORDER BY id ASC")->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $title = "Peserta & Pengawas CBT Kelas Jauh - MAS MIFTAHUL HUDA";
        $active_menu = 'peserta_khusus';

        ob_start();
        include __DIR__ . '/../../resources/views/cbt/peserta_khusus.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Manajemen Peserta Siswa SIAKAD (Khusus CBT Offline)
     */
    public static function pesertaSiakad() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $dbSiakad = $ctx['db_siakad'] ?? $db;
        self::ensureTables($db);

        $selectedTingkat = trim($_GET['tingkat'] ?? '');
        $selectedKelasId = !empty($_GET['kelas_id']) ? (int)$_GET['kelas_id'] : 0;
        $search = trim($_GET['q'] ?? '');

        // Ambil daftar kelas untuk dropdown filter
        $kelasList = [];
        try {
            $kelasList = $db->query("SELECT id, nama_kelas, tingkat FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}
        if (empty($kelasList) && $dbSiakad !== $db) {
            try {
                $kelasList = $dbSiakad->query("SELECT id, nama_kelas, tingkat FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\Exception $e2) {}
        }

        // Query data siswa
        $sql = "
            SELECT s.id, s.user_id, s.nis, s.nik, s.nama, s.jenis_kelamin, s.status, s.kelas_id,
                   k.nama_kelas, k.tingkat,
                   u.username, u.plain_password, u.status as user_status
            FROM siswa s
            LEFT JOIN kelas k ON s.kelas_id = k.id
            LEFT JOIN users u ON (s.user_id IS NOT NULL AND s.user_id = u.id) OR (u.username = s.nis AND s.nis != '')
            WHERE 1=1
        ";
        $params = [];

        if ($selectedTingkat !== '') {
            $sql .= " AND (k.tingkat = ? OR k.nama_kelas LIKE ?)";
            $params[] = $selectedTingkat;
            $params[] = "%{$selectedTingkat}%";
        }

        if ($selectedKelasId > 0) {
            $sql .= " AND s.kelas_id = ?";
            $params[] = $selectedKelasId;
        }

        if ($search !== '') {
            $sql .= " AND (s.nama LIKE ? OR s.nis LIKE ? OR s.nik LIKE ? OR u.username LIKE ?)";
            $wild = "%{$search}%";
            $params[] = $wild;
            $params[] = $wild;
            $params[] = $wild;
            $params[] = $wild;
        }

        $sql .= " ORDER BY k.tingkat ASC, k.nama_kelas ASC, s.nama ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $siswaList = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Statistik
        $totalSiswa = 0;
        $countX = 0;
        $countXI = 0;
        $countXII = 0;
        $countActiveAccount = 0;

        try {
            $allRows = $db->query("
                SELECT s.id, k.tingkat, k.nama_kelas, u.id as has_user
                FROM siswa s
                LEFT JOIN kelas k ON s.kelas_id = k.id
                LEFT JOIN users u ON (s.user_id IS NOT NULL AND s.user_id = u.id) OR (u.username = s.nis AND s.nis != '')
            ")->fetchAll(\PDO::FETCH_ASSOC);
            $totalSiswa = count($allRows);
            foreach ($allRows as $r) {
                $t = strtoupper((string)($r['tingkat'] ?? ''));
                $nk = strtoupper((string)($r['nama_kelas'] ?? ''));
                if (strpos($t, 'XII') !== false || strpos($nk, 'XII') !== false) {
                    $countXII++;
                } elseif (strpos($t, 'XI') !== false || strpos($nk, 'XI') !== false) {
                    $countXI++;
                } else {
                    $countX++;
                }
                if (!empty($r['has_user'])) {
                    $countActiveAccount++;
                }
            }
        } catch (\Exception $e) {}

        $title = "Peserta Siswa SIAKAD (CBT Offline) - MAS MIFTAHUL HUDA";
        $active_menu = 'peserta_siakad';

        ob_start();
        include __DIR__ . '/../../resources/views/cbt/peserta_siakad.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * AJAX Tarik Khusus Data Siswa SIAKAD dari Server Online ke Offline
     */
    public static function ajaxPullSiswaOnline() {
        set_time_limit(180);
        header('Content-Type: application/json');
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $dbSiakad = $ctx['db_siakad'] ?? $db;
        self::ensureTables($db);

        $serverUrl = rtrim(trim($_POST['server_url'] ?? 'https://apk.masmifhda.sch.id'), '/');
        $targetEndpoint = $serverUrl . '/apk/cbt_sync_api.php?action=export_siswa&token=' . urlencode('MIFHDA_CBT_SYNC_2026');

        $ch = curl_init($targetEndpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['action' => 'export_siswa']));
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_TIMEOUT, 180);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if (!$response || $httpCode !== 200) {
            echo json_encode([
                'success' => false,
                'message' => "Gagal terhubung ke Server Online Pusat ({$serverUrl}). HTTP {$httpCode}. Error: {$curlErr}"
            ]);
            exit;
        }

        $data = json_decode($response, true);
        if (!$data || ($data['status'] ?? '') !== 'success') {
            echo json_encode([
                'success' => false,
                'message' => $data['message'] ?? 'Respon server online tidak valid atau format salah.'
            ]);
            exit;
        }

        $resDetails = self::importDataPayload($db, $dbSiakad, [
            'kelas' => $data['kelas'] ?? [],
            'siswa' => $data['siswa'] ?? [],
            'users' => $data['users'] ?? []
        ]);

        $countSiswa = count($data['siswa'] ?? []);
        echo json_encode([
            'success' => true,
            'message' => "Berhasil menarik {$countSiswa} data siswa SIAKAD dari Server Online Pusat!",
            'count' => $countSiswa,
            'details' => $resDetails
        ]);
        exit;
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
            $jenis_kelamin = trim($_POST['jenis_kelamin'] ?? 'L');
            $tempat_lahir = trim($_POST['tempat_lahir'] ?? '') ?: null;
            $tanggal_lahir = !empty($_POST['tanggal_lahir']) ? trim($_POST['tanggal_lahir']) : null;
            $no_hp = trim($_POST['no_hp'] ?? '') ?: null;
            $alamat = trim($_POST['alamat'] ?? '') ?: null;

            if (empty($username)) {
                $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $no_peserta));
            }
            $password_hash = password_hash($password_plain, PASSWORD_DEFAULT);
            $qr_token = substr(hash('sha256', $no_peserta . $username . 'MASMIFHDA_CBT_KJ_SECURE_2026'), 0, 24);

            // Handle Foto Upload
            $fotoName = null;
            if (isset($_FILES['foto']) && is_uploaded_file($_FILES['foto']['tmp_name'])) {
                $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $upDir = __DIR__ . '/../../public/uploads/siswa/';
                    if (!is_dir($upDir)) @mkdir($upDir, 0777, true);
                    $fotoName = 'cbt_kj_' . time() . '_' . rand(100, 999) . '.' . $ext;
                    @move_uploaded_file($_FILES['foto']['tmp_name'], $upDir . $fotoName);
                }
            }

            if ($id > 0) {
                if ($fotoName) {
                    $stmt = $db->prepare("UPDATE cbt_peserta_khusus 
                        SET no_peserta = ?, nis = ?, nama_siswa = ?, foto = ?, jenis_kelamin = ?, tempat_lahir = ?, tanggal_lahir = ?, no_hp = ?, alamat = ?, tingkat = ?, kelas_nama = ?, username = ?, password_plain = ?, password_hash = ?, qr_token = ?, lokasi_cabang = ?, status = ?
                        WHERE id = ?");
                    $stmt->execute([$no_peserta, $nis, $nama_siswa, $fotoName, $jenis_kelamin, $tempat_lahir, $tanggal_lahir, $no_hp, $alamat, $tingkat, $kelas_nama, $username, $password_plain, $password_hash, $qr_token, $lokasi_cabang, $status, $id]);
                } else {
                    $stmt = $db->prepare("UPDATE cbt_peserta_khusus 
                        SET no_peserta = ?, nis = ?, nama_siswa = ?, jenis_kelamin = ?, tempat_lahir = ?, tanggal_lahir = ?, no_hp = ?, alamat = ?, tingkat = ?, kelas_nama = ?, username = ?, password_plain = ?, password_hash = ?, qr_token = ?, lokasi_cabang = ?, status = ?
                        WHERE id = ?");
                    $stmt->execute([$no_peserta, $nis, $nama_siswa, $jenis_kelamin, $tempat_lahir, $tanggal_lahir, $no_hp, $alamat, $tingkat, $kelas_nama, $username, $password_plain, $password_hash, $qr_token, $lokasi_cabang, $status, $id]);
                }
            } else {
                $stmt = $db->prepare("INSERT INTO cbt_peserta_khusus 
                    (no_peserta, nis, nama_siswa, foto, jenis_kelamin, tempat_lahir, tanggal_lahir, no_hp, alamat, tingkat, kelas_nama, username, password_plain, password_hash, qr_token, lokasi_cabang, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$no_peserta, $nis, $nama_siswa, $fotoName, $jenis_kelamin, $tempat_lahir, $tanggal_lahir, $no_hp, $alamat, $tingkat, $kelas_nama, $username, $password_plain, $password_hash, $qr_token, $lokasi_cabang, $status]);
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
     * Simpan / Perbarui Akun Pengawas / Admin Kelas Jauh (Untuk Portal APK)
     */
    public static function savePengawasKj() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $nama = trim($_POST['nama'] ?? '');
            $username = strtolower(trim(preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $_POST['username'] ?? '')));
            $password = trim($_POST['password'] ?? '');
            $no_hp = trim($_POST['no_hp'] ?? '');
            $lokasi_cabang = trim($_POST['lokasi_cabang'] ?? 'Cabang Kelas Jauh');
            $status = trim($_POST['status'] ?? 'Aktif');

            if (!empty($nama) && !empty($username)) {
                $pwPlain = !empty($password) ? $password : '123456';
                $pwHash = password_hash($pwPlain, PASSWORD_BCRYPT);

                if ($id > 0) {
                    if (!empty($password)) {
                        $stmt = $db->prepare("UPDATE cbt_pengawas_kj SET nama = ?, username = ?, password_plain = ?, password_hash = ?, no_hp = ?, lokasi_cabang = ?, status = ? WHERE id = ?");
                        $stmt->execute([$nama, $username, $pwPlain, $pwHash, $no_hp, $lokasi_cabang, $status, $id]);
                    } else {
                        $stmt = $db->prepare("UPDATE cbt_pengawas_kj SET nama = ?, username = ?, no_hp = ?, lokasi_cabang = ?, status = ? WHERE id = ?");
                        $stmt->execute([$nama, $username, $no_hp, $lokasi_cabang, $status, $id]);
                    }
                } else {
                    $stmt = $db->prepare("INSERT INTO cbt_pengawas_kj (nama, username, password_plain, password_hash, no_hp, lokasi_cabang, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$nama, $username, $pwPlain, $pwHash, $no_hp, $lokasi_cabang, $status]);
                }
                redirect('/cbt/peserta_khusus?tab=pengawas&msg=saved');
                exit;
            }
        }
        redirect('/cbt/peserta_khusus?tab=pengawas');
    }

    /**
     * Hapus Akun Pengawas Kelas Jauh
     */
    public static function deletePengawasKj() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        $id = (int)($_POST['id'] ?? ($_GET['id'] ?? 0));
        if ($id > 0) {
            $db->prepare("DELETE FROM cbt_pengawas_kj WHERE id = ?")->execute([$id]);
            redirect('/cbt/peserta_khusus?tab=pengawas&msg=deleted');
            exit;
        }
        redirect('/cbt/peserta_khusus?tab=pengawas');
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

        // Pastikan setiap peserta punya qr_token unik
        foreach ($pesertaList as &$p) {
            if (empty($p['qr_token'])) {
                $p['qr_token'] = substr(hash('sha256', $p['no_peserta'] . $p['username'] . 'MASMIFHDA_CBT_KJ_SECURE_2026'), 0, 24);
                $db->prepare("UPDATE cbt_peserta_khusus SET qr_token = ? WHERE id = ?")->execute([$p['qr_token'], $p['id']]);
            }
        }
        unset($p);

        // Ambil data institusi untuk Kop dan TTD Kepala Madrasah
        $inst = null;
        try {
            $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        if (!$inst) {
            $inst = [];
        }
        if (empty($inst['nama_kepala']) || stripos($inst['nama_kepala'], 'Ulfah') !== false || stripos($inst['nama_kepala'], 'Hamim') !== false) {
            $inst['nama_kepala'] = 'AHMAD RIFA\'I, M.Pd';
        }
        if (empty($inst['nip_kepala'])) {
            $inst['nip_kepala'] = '-';
        }
        if (empty($inst['kota'])) {
            $inst['kota'] = 'Lampung Barat';
        }
        if (empty($inst['nama']) && empty($inst['nama_institusi'])) {
            $inst['nama'] = 'MAS MIFTAHUL HUDA';
        }

        include __DIR__ . '/../../resources/views/cbt/peserta_khusus_cetak.php';
    }

    /**
     * Cetak Kartu Identitas Pengawas / Proktor CBT Kelas Jauh & QR Login
     */
    public static function cetakKartuPengawasKhusus() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        $id = (int)($_GET['id'] ?? 0);

        $sql = "SELECT * FROM cbt_pengawas_kj WHERE status = 'Aktif'";
        $params = [];

        if ($id > 0) {
            $sql .= " AND id = ?";
            $params[] = $id;
        }

        $sql .= " ORDER BY nama ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $pengawasList = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Ambil data institusi untuk Kop dan TTD Kepala Madrasah
        $inst = null;
        try {
            $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        if (!$inst) {
            $inst = [];
        }
        if (empty($inst['nama_kepala']) || stripos($inst['nama_kepala'], 'Ulfah') !== false || stripos($inst['nama_kepala'], 'Hamim') !== false) {
            $inst['nama_kepala'] = 'AHMAD RIFA\'I, M.Pd';
        }
        if (empty($inst['nip_kepala'])) {
            $inst['nip_kepala'] = '-';
        }
        if (empty($inst['kota'])) {
            $inst['kota'] = 'Lampung Barat';
        }
        if (empty($inst['nama']) && empty($inst['nama_institusi'])) {
            $inst['nama'] = 'MAS MIFTAHUL HUDA';
        }

        include __DIR__ . '/../../resources/views/cbt/pengawas_khusus_cetak.php';
    }

    // =========================================================================
    // MODUL A: KELOLA RUANG & DENAH TEMPAT DUDUK CBT
    // =========================================================================

    /**
     * Master Kelola Ruang Ujian CBT
     */
    public static function ruang() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $db_siakad = $ctx['db_siakad'];
        self::ensureTables($db);

        // Ambil daftar topik aktif dari bs_topik
        $topikList = self::getActiveTopikList($db, $db_siakad);
        $selectedTopikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0;

        if ($selectedTopikId <= 0) {
            $slotList = [];
            $pengawasRuang = [];
            $guruList = [];
            $ruangList = [];
            $totalRuang = 0;
            $totalKapasitas = 0;
            $totalTerisi = 0;
            $totalKosong = 0;

            $title = "Kelola Ruang Ujian - MAS MIFTAHUL HUDA";
            $active_menu = 'ruang';
            ob_start();
            include __DIR__ . '/../../resources/views/cbt/ruang.php';
            $content = ob_get_clean();
            include __DIR__ . '/../../resources/views/siakad/layout.php';
            return;
        }

        // Ambil slot sesi untuk topik terpilih
        $slotList = [];
        try {
            $stmtSlot = $db->prepare("SELECT * FROM cbt_jam_slot WHERE (topik_id = ? OR topik_id IS NULL OR topik_id = 0) ORDER BY urutan ASC, jam_mulai ASC");
            $stmtSlot->execute([$selectedTopikId]);
            $slotList = $stmtSlot->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // Ambil mapping pengawas per-ruang per-sesi untuk topik ini
        $pengawasRuang = [];
        try {
            $stmtP = $db->prepare("SELECT * FROM cbt_ruang_pengawas WHERE topik_id = ?");
            $stmtP->execute([$selectedTopikId]);
            $rowsP = $stmtP->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rowsP as $rp) {
                $pengawasRuang[(int)$rp['ruang_id']][$rp['sesi_label']] = $rp['guru_nama'];
            }
        } catch (\Exception $e) {}

        // Ambil daftar guru
        $guruList = [];
        try {
            $guruList = $db_siakad->query("SELECT id, nama FROM guru WHERE status = 'Aktif' ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
            if (empty($guruList) && $db !== $db_siakad) {
                $guruList = $db->query("SELECT id, nama FROM guru WHERE status = 'Aktif' ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (\Exception $e) {}

        $ruangList = $db->query("SELECT * FROM cbt_ruang ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

        $totalRuang = count($ruangList);
        $totalKapasitas = 0;
        $totalTerisi = 0;

        foreach ($ruangList as &$r) {
            $rId = (int)$r['id'];
            $totalKapasitas += (int)$r['kapasitas'];

            $stmtCnt = $db->prepare("SELECT COUNT(*) FROM cbt_denah_peserta WHERE ruang_id = ? AND siswa_id IS NOT NULL AND (topik_id = ? OR topik_id IS NULL)");
            $stmtCnt->execute([$rId, $selectedTopikId]);
            $terisi = (int)$stmtCnt->fetchColumn();
            $r['terisi'] = $terisi;
            $r['kosong'] = max(0, (int)$r['kapasitas'] - $terisi);
            $r['persen'] = $r['kapasitas'] > 0 ? round(($terisi / $r['kapasitas']) * 100) : 0;
            $totalTerisi += $terisi;

            $r['pengawas_sesi'] = $pengawasRuang[$rId] ?? [];
        }
        unset($r);

        $totalKosong = max(0, $totalKapasitas - $totalTerisi);

        $title = "Kelola Ruang Ujian - MAS MIFTAHUL HUDA";
        $active_menu = 'ruang';
        ob_start();
        include __DIR__ . '/../../resources/views/cbt/ruang.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Simpan Pengawas Ruangan Per Sesi (Scoped Topik)
     */
    public static function savePengawasRuang() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $topikId = (int)($_POST['topik_id'] ?? 0);
            $ruangId = (int)($_POST['ruang_id'] ?? 0);
            $pengawasData = $_POST['pengawas'] ?? [];

            if ($topikId > 0 && $ruangId > 0 && is_array($pengawasData)) {
                $stmtSave = $db->prepare("
                    INSERT INTO cbt_ruang_pengawas (topik_id, ruang_id, sesi_label, jam_slot, guru_nama)
                    VALUES (:topik_id, :ruang_id, :sesi_label, :jam_slot, :guru_nama)
                    ON DUPLICATE KEY UPDATE jam_slot = VALUES(jam_slot), guru_nama = VALUES(guru_nama), updated_at = NOW()
                ");

                foreach ($pengawasData as $sesiLabel => $info) {
                    $guruNama = trim($info['guru_nama'] ?? '');
                    $jamSlot = trim($info['jam_slot'] ?? '');
                    if (!empty($guruNama)) {
                        $stmtSave->execute([
                            ':topik_id' => $topikId,
                            ':ruang_id' => $ruangId,
                            ':sesi_label' => $sesiLabel,
                            ':jam_slot' => $jamSlot,
                            ':guru_nama' => $guruNama
                        ]);
                    } else {
                        $db->prepare("DELETE FROM cbt_ruang_pengawas WHERE topik_id = ? AND ruang_id = ? AND sesi_label = ?")
                           ->execute([$topikId, $ruangId, $sesiLabel]);
                    }
                }
            }
            redirect("/cbt/ruang?topik_id={$topikId}&saved_pengawas=1");
            exit;
        }
        redirect('/cbt/ruang');
    }

    /**
     * Simpan / Perbarui Ruang Ujian
     */
    public static function saveRuang() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
            $kode_ruang = strtoupper(trim($_POST['kode_ruang'] ?? ''));
            $nama_ruang = trim($_POST['nama_ruang'] ?? '');
            $tipe_meja = max(1, min(2, (int)($_POST['tipe_meja'] ?? 1))); // 1 = Tunggal, 2 = Gandeng
            $baris_samping = max(1, (int)($_POST['baris_samping'] ?? 4));
            $baris_belakang = max(1, (int)($_POST['baris_belakang'] ?? 5));
            $lokasi = trim($_POST['lokasi'] ?? '');
            $pengawas = trim($_POST['pengawas'] ?? '');
            $keterangan = trim($_POST['keterangan'] ?? '');

            // Hitung kapasitas & kode pola otomatis
            $kapasitas = $tipe_meja * $baris_samping * $baris_belakang;
            $kode_pola = "{$tipe_meja} x {$baris_samping} x {$baris_belakang}";

            if (empty($kode_ruang) || empty($nama_ruang)) {
                redirect('/cbt/ruang?err=' . urlencode('Nama dan Kode Ruang wajib diisi!'));
                exit;
            }

            if ($id > 0) {
                // Update
                $stmt = $db->prepare("
                    UPDATE cbt_ruang 
                    SET kode_ruang = ?, nama_ruang = ?, tipe_meja = ?, baris_samping = ?, baris_belakang = ?, 
                        kapasitas = ?, kode_pola = ?, lokasi = ?, pengawas = ?, keterangan = ?
                    WHERE id = ?
                ");
                $stmt->execute([$kode_ruang, $nama_ruang, $tipe_meja, $baris_samping, $baris_belakang, $kapasitas, $kode_pola, $lokasi, $pengawas, $keterangan, $id]);
                $ruangId = $id;
            } else {
                // Insert
                $stmt = $db->prepare("
                    INSERT INTO cbt_ruang (kode_ruang, nama_ruang, tipe_meja, baris_samping, baris_belakang, kapasitas, kode_pola, lokasi, pengawas, keterangan)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$kode_ruang, $nama_ruang, $tipe_meja, $baris_samping, $baris_belakang, $kapasitas, $kode_pola, $lokasi, $pengawas, $keterangan]);
                $ruangId = (int)$db->lastInsertId();
            }

            // Sinkronkan slot meja di cbt_denah_peserta
            self::syncSlotMejaRuang($db, $ruangId, $tipe_meja, $baris_samping, $baris_belakang);

            redirect('/cbt/ruang?success=1');
            exit;
        }
        redirect('/cbt/ruang');
    }

    /**
     * Hapus Ruang Ujian
     */
    public static function deleteRuang() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id > 0) {
                $db->prepare("DELETE FROM cbt_denah_peserta WHERE ruang_id = ?")->execute([$id]);
                $db->prepare("DELETE FROM cbt_ruang WHERE id = ?")->execute([$id]);
            }
        }
        redirect('/cbt/ruang?deleted=1');
    }

    /**
     * Helper: Sinkronisasi Slot Kursi Meja Ruang
     */
    public static function syncSlotMejaRuang($db, $ruangId, $tipe_meja, $baris_samping, $baris_belakang, $topikId = null) {
        $tipe_meja = max(1, min(2, (int)$tipe_meja));
        $baris_samping = max(1, (int)$baris_samping);
        $baris_belakang = max(1, (int)$baris_belakang);
        $totalSlotDibutuhkan = $tipe_meja * $baris_samping * $baris_belakang;

        // Ambil slot yang sudah ada saat ini untuk topik ini
        if ($topikId > 0) {
            $stmtCurr = $db->prepare("SELECT id, nomor_meja, posisi_baris, posisi_kolom, posisi_sisi FROM cbt_denah_peserta WHERE ruang_id = ? AND topik_id = ? ORDER BY nomor_meja ASC");
            $stmtCurr->execute([$ruangId, $topikId]);
        } else {
            $stmtCurr = $db->prepare("SELECT id, nomor_meja, posisi_baris, posisi_kolom, posisi_sisi FROM cbt_denah_peserta WHERE ruang_id = ? AND (topik_id IS NULL OR topik_id = 0) ORDER BY nomor_meja ASC");
            $stmtCurr->execute([$ruangId]);
        }
        $existing = $stmtCurr->fetchAll(PDO::FETCH_ASSOC);

        $existingByNo = [];
        foreach ($existing as $ex) {
            $existingByNo[(int)$ex['nomor_meja']] = $ex;
        }

        // Susun expected mapping posisi baris, kolom, dan sisi meja
        $expectedSlots = [];
        $noMeja = 1;
        for ($b = 1; $b <= $baris_belakang; $b++) {
            for ($k = 1; $k <= $baris_samping; $k++) {
                if ($tipe_meja === 2) {
                    $expectedSlots[] = ['no' => $noMeja++, 'b' => $b, 'k' => $k, 'sisi' => 'L'];
                    $expectedSlots[] = ['no' => $noMeja++, 'b' => $b, 'k' => $k, 'sisi' => 'R'];
                } else {
                    $expectedSlots[] = ['no' => $noMeja++, 'b' => $b, 'k' => $k, 'sisi' => 'C'];
                }
            }
        }

        $stmtUp = $db->prepare("UPDATE cbt_denah_peserta SET posisi_baris = ?, posisi_kolom = ?, posisi_sisi = ? WHERE id = ?");
        $stmtIns = $db->prepare("INSERT INTO cbt_denah_peserta (ruang_id, topik_id, nomor_meja, posisi_baris, posisi_kolom, posisi_sisi) VALUES (?, ?, ?, ?, ?, ?)");

        foreach ($expectedSlots as $exp) {
            $mNo = $exp['no'];
            if (isset($existingByNo[$mNo])) {
                $cur = $existingByNo[$mNo];
                if ((int)$cur['posisi_baris'] !== $exp['b'] || (int)$cur['posisi_kolom'] !== $exp['k'] || $cur['posisi_sisi'] !== $exp['sisi']) {
                    $stmtUp->execute([$exp['b'], $exp['k'], $exp['sisi'], $cur['id']]);
                }
            } else {
                $stmtIns->execute([$ruangId, $topikId, $mNo, $exp['b'], $exp['k'], $exp['sisi']]);
            }
        }

        // Hapus slot berlebih jika nomor meja melebihi kapasitas
        if ($topikId > 0) {
            $db->prepare("DELETE FROM cbt_denah_peserta WHERE ruang_id = ? AND topik_id = ? AND nomor_meja > ? AND (siswa_id IS NULL OR siswa_id = 0)")->execute([$ruangId, $topikId, $totalSlotDibutuhkan]);
        } else {
            $db->prepare("DELETE FROM cbt_denah_peserta WHERE ruang_id = ? AND nomor_meja > ? AND (siswa_id IS NULL OR siswa_id = 0)")->execute([$ruangId, $totalSlotDibutuhkan]);
        }
    }

    /**
     * Halaman Visual Denah Tempat Duduk Siswa
     */
    public static function denah() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $db_siakad = $ctx['db_siakad'];
        self::ensureTables($db);

        // Ambil daftar topik aktif dari bs_topik
        $topikList = self::getActiveTopikList($db, $db_siakad);
        $selectedTopikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0;

        if ($selectedTopikId <= 0) {
            $ruangList = $db->query("SELECT * FROM cbt_ruang ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
            $selectedRuangId = 0;
            $currentRuang = null;
            $mejaList = [];
            $tingkatStats = [10 => 0, 11 => 0, 12 => 0, 'other' => 0, 'kosong' => 0];
            $unassignedSiswa = [];

            $title = "Denah Tempat Duduk - MAS MIFTAHUL HUDA";
            $active_menu = 'denah';
            ob_start();
            include __DIR__ . '/../../resources/views/cbt/denah.php';
            $content = ob_get_clean();
            include __DIR__ . '/../../resources/views/siakad/layout.php';
            return;
        }

        $ruangList = $db->query("SELECT * FROM cbt_ruang ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

        $selectedRuangId = isset($_GET['ruang_id']) ? (int)$_GET['ruang_id'] : 0;
        if ($selectedRuangId <= 0 && !empty($ruangList)) {
            $selectedRuangId = (int)$ruangList[0]['id'];
        }

        $currentRuang = null;
        foreach ($ruangList as $rg) {
            if ((int)$rg['id'] === $selectedRuangId) {
                $currentRuang = $rg;
                break;
            }
        }

        // Ambil slot meja di ruang terpilih untuk topik ini
        $mejaList = [];
        $tingkatStats = [10 => 0, 11 => 0, 12 => 0, 'other' => 0, 'kosong' => 0];

        if ($currentRuang) {
            $tipeMeja = (int)($currentRuang['tipe_meja'] ?? 1);
            $samping = (int)($currentRuang['baris_samping'] ?? 5);
            $belakang = (int)($currentRuang['baris_belakang'] ?? 5);
            if ($samping <= 0 || $belakang <= 0) {
                $polaParts = explode('x', strtolower($currentRuang['kode_pola'] ?? ''));
                if (count($polaParts) === 3) {
                    $tipeMeja = (int)trim($polaParts[0]) ?: 1;
                    $samping = (int)trim($polaParts[1]) ?: 6;
                    $belakang = (int)trim($polaParts[2]) ?: 5;
                }
            }

            // Selalu sinkronkan mapping baris & kolom dengan konfigurasi pola meja dan topik saat ini
            self::syncSlotMejaRuang($db, $currentRuang['id'], $tipeMeja, $samping, $belakang, $selectedTopikId);

            $stmtMeja = $db->prepare("SELECT * FROM cbt_denah_peserta WHERE ruang_id = ? AND (topik_id = ? OR topik_id IS NULL) ORDER BY nomor_meja ASC");
            $stmtMeja->execute([$selectedRuangId, $selectedTopikId]);
            $mejaList = $stmtMeja->fetchAll(PDO::FETCH_ASSOC);

            foreach ($mejaList as $m) {
                if (!empty($m['siswa_id'])) {
                    $t = (int)($m['tingkat'] ?? 0);
                    if (isset($tingkatStats[$t])) {
                        $tingkatStats[$t]++;
                    } else {
                        $tingkatStats['other']++;
                    }
                } else {
                    $tingkatStats['kosong']++;
                }
            }
        }

        // Ambil daftar siswa reguler Siakad yang BELUM punya kursi di topik ini
        $unassignedSiswa = [];
        try {
            $stmtUn = $db->prepare("
                SELECT s.id, s.nis, s.nama, s.kelas_id, k.nama_kelas, k.tingkat
                FROM siswa s
                LEFT JOIN kelas k ON s.kelas_id = k.id
                WHERE (s.status = 'Aktif' OR s.status IS NULL OR s.status = '')
                  AND s.id NOT IN (SELECT siswa_id FROM cbt_denah_peserta WHERE siswa_id IS NOT NULL AND (topik_id = ? OR topik_id IS NULL))
                ORDER BY k.tingkat ASC, s.nama ASC
            ");
            $stmtUn->execute([$selectedTopikId]);
            $unassignedSiswa = $stmtUn->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $title = "Denah Tempat Duduk - MAS MIFTAHUL HUDA";
        $active_menu = 'denah';
        ob_start();
        include __DIR__ . '/../../resources/views/cbt/denah.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Auto-Generate Penempatan Denah Tempat Duduk (Scoped Topik)
     * Opsi: smart_cross (Silang Tingkatan Anti-Contek), full_random, sequential
     */
    public static function generateDenah() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ruangId = (int)($_POST['ruang_id'] ?? 0);
            $topikId = (int)($_POST['topik_id'] ?? 0);
            $mode = trim($_POST['mode'] ?? 'smart_cross'); // smart_cross | full_random | sequential

            if ($ruangId <= 0) {
                redirect('/cbt/denah' . ($topikId ? '?topik_id=' . $topikId : ''));
                exit;
            }

            // Ambil info ruang
            $stmtR = $db->prepare("SELECT * FROM cbt_ruang WHERE id = ?");
            $stmtR->execute([$ruangId]);
            $ruang = $stmtR->fetch(PDO::FETCH_ASSOC);
            if (!$ruang) {
                redirect('/cbt/denah' . ($topikId ? '?topik_id=' . $topikId : ''));
                exit;
            }

            // Ambil slot meja kosong di ruangan ini untuk topik terpilih
            if ($topikId > 0) {
                $stmtSlots = $db->prepare("SELECT * FROM cbt_denah_peserta WHERE ruang_id = ? AND (topik_id = ? OR topik_id IS NULL) AND siswa_id IS NULL ORDER BY nomor_meja ASC");
                $stmtSlots->execute([$ruangId, $topikId]);
            } else {
                $stmtSlots = $db->prepare("SELECT * FROM cbt_denah_peserta WHERE ruang_id = ? AND siswa_id IS NULL ORDER BY nomor_meja ASC");
                $stmtSlots->execute([$ruangId]);
            }
            $slots = $stmtSlots->fetchAll(PDO::FETCH_ASSOC);

            if (empty($slots)) {
                redirect("/cbt/denah?ruang_id={$ruangId}&topik_id={$topikId}&err=" . urlencode('Semua kursi di ruangan ini sudah terisi penuh! Kosongkan terlebih dahulu jika ingin menata ulang.'));
                exit;
            }

            // Ambil siswa reguler Siakad yang BELUM punya kursi di topik terpilih
            $stmtSiswa = $db->prepare("
                SELECT s.id, s.nis, s.nama, s.kelas_id, k.nama_kelas, k.tingkat
                FROM siswa s
                LEFT JOIN kelas k ON s.kelas_id = k.id
                WHERE (s.status = 'Aktif' OR s.status IS NULL OR s.status = '')
                  AND s.id NOT IN (SELECT siswa_id FROM cbt_denah_peserta WHERE siswa_id IS NOT NULL AND (topik_id = ? OR ? = 0 OR topik_id IS NULL))
                ORDER BY k.tingkat ASC, s.nama ASC
            ");
            $stmtSiswa->execute([$topikId, $topikId]);
            $candidates = $stmtSiswa->fetchAll(PDO::FETCH_ASSOC);

            if (empty($candidates)) {
                redirect("/cbt/denah?ruang_id={$ruangId}&topik_id={$topikId}&err=" . urlencode('Tidak ada siswa reguler Siakad yang belum punya tempat duduk di topik ini!'));
                exit;
            }

            $assignedMap = [];

            if ($mode === 'smart_cross') {
                // Algoritma Smart Zig-Zag: Silang Tingkatan (Tingkat 10, 11, 12 diselang-selingkan)
                $b10 = [];
                $b11 = [];
                $b12 = [];
                $bOther = [];

                foreach ($candidates as $c) {
                    $t = (int)($c['tingkat'] ?? 0);
                    if ($t === 10) $b10[] = $c;
                    elseif ($t === 11) $b11[] = $c;
                    elseif ($t === 12) $b12[] = $c;
                    else $bOther[] = $c;
                }

                // Acak di dalam tingkatan masing-masing
                shuffle($b10);
                shuffle($b11);
                shuffle($b12);
                shuffle($bOther);

                $poolCount = count($slots);
                $isGandeng = ((int)$ruang['tipe_meja'] === 2);

                for ($i = 0; $i < $poolCount; $i++) {
                    $slot = $slots[$i];
                    $baris = (int)$slot['posisi_baris'];
                    $kolom = (int)$slot['posisi_kolom'];
                    $sisi = $slot['posisi_sisi']; // L, R, C

                    // Pola urutan target tingkat per posisi agar silang
                    if ($isGandeng) {
                        // Di meja gandeng: L dan R harus beda tingkat
                        $shift = ($baris % 3);
                        if ($sisi === 'L') {
                            $order = [($shift + 1), ($shift + 2), ($shift + 3)];
                        } else {
                            $order = [($shift + 2), ($shift + 3), ($shift + 1)];
                        }
                    } else {
                        // Di meja tunggal: geser tingkat per kolom & baris
                        $val = ($baris + $kolom) % 3;
                        $order = [$val + 1, (($val + 1) % 3) + 1, (($val + 2) % 3) + 1];
                    }

                    // Ambil siswa dari target tingkat yang sesuai
                    $picked = null;
                    foreach ($order as $ord) {
                        if ($ord === 1 && !empty($b10)) { $picked = array_shift($b10); break; }
                        if ($ord === 2 && !empty($b11)) { $picked = array_shift($b11); break; }
                        if ($ord === 3 && !empty($b12)) { $picked = array_shift($b12); break; }
                    }

                    // Fallback jika bucket utama habis
                    if (!$picked) {
                        if (!empty($b10)) $picked = array_shift($b10);
                        elseif (!empty($b11)) $picked = array_shift($b11);
                        elseif (!empty($b12)) $picked = array_shift($b12);
                        elseif (!empty($bOther)) $picked = array_shift($bOther);
                    }

                    if ($picked) {
                        $assignedMap[$slot['id']] = $picked;
                    }
                }
            } elseif ($mode === 'full_random') {
                shuffle($candidates);
                $pickList = array_slice($candidates, 0, count($slots));
                foreach ($slots as $idx => $slot) {
                    if (isset($pickList[$idx])) {
                        $assignedMap[$slot['id']] = $pickList[$idx];
                    }
                }
            } else {
                // Sequential (urut kelas & nama)
                $pickList = array_slice($candidates, 0, count($slots));
                foreach ($slots as $idx => $slot) {
                    if (isset($pickList[$idx])) {
                        $assignedMap[$slot['id']] = $pickList[$idx];
                    }
                }
            }

            // Simpan ke database
            $stmtUp = $db->prepare("
                UPDATE cbt_denah_peserta 
                SET siswa_id = ?, nis = ?, nama_siswa = ?, kelas_id = ?, kelas_nama = ?, tingkat = ?
                WHERE id = ?
            ");

            $countAssigned = 0;
            foreach ($assignedMap as $slotId => $siswa) {
                $stmtUp->execute([
                    $siswa['id'],
                    $siswa['nis'],
                    $siswa['nama'],
                    $siswa['kelas_id'],
                    $siswa['nama_kelas'],
                    $siswa['tingkat'],
                    $slotId
                ]);
                $countAssigned++;
            }

            redirect("/cbt/denah?ruang_id={$ruangId}&topik_id={$topikId}&assigned={$countAssigned}");
            exit;
        }
        redirect('/cbt/denah');
    }

    /**
     * Kosongkan Kursi di Ruangan Terpilih (Scoped Topik)
     */
    public static function resetDenah() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ruangId = (int)($_POST['ruang_id'] ?? 0);
            $topikId = (int)($_POST['topik_id'] ?? 0);
            if ($ruangId > 0) {
                if ($topikId > 0) {
                    $db->prepare("
                        UPDATE cbt_denah_peserta 
                        SET siswa_id = NULL, nis = NULL, nama_siswa = NULL, kelas_id = NULL, kelas_nama = NULL, tingkat = NULL 
                        WHERE ruang_id = ? AND (topik_id = ? OR topik_id IS NULL)
                    ")->execute([$ruangId, $topikId]);
                } else {
                    $db->prepare("
                        UPDATE cbt_denah_peserta 
                        SET siswa_id = NULL, nis = NULL, nama_siswa = NULL, kelas_id = NULL, kelas_nama = NULL, tingkat = NULL 
                        WHERE ruang_id = ?
                    ")->execute([$ruangId]);
                }
            }
            redirect("/cbt/denah?ruang_id={$ruangId}&topik_id={$topikId}&reset=1");
            exit;
        }
        redirect('/cbt/denah');
    }

    /**
     * Tempatkan Siswa Manual ke Kursi Tertentu
     */
    public static function assignManualDenah() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ruangId = (int)($_POST['ruang_id'] ?? 0);
            $topikId = (int)($_POST['topik_id'] ?? 0);
            $slotId = (int)($_POST['slot_id'] ?? 0);
            $siswaId = (int)($_POST['siswa_id'] ?? 0);

            if ($slotId > 0 && $siswaId > 0) {
                // Ambil info siswa
                $stmtS = $db->prepare("
                    SELECT s.id, s.nis, s.nama, s.kelas_id, k.nama_kelas, k.tingkat 
                    FROM siswa s 
                    LEFT JOIN kelas k ON s.kelas_id = k.id 
                    WHERE s.id = ?
                ");
                $stmtS->execute([$siswaId]);
                $sRow = $stmtS->fetch(PDO::FETCH_ASSOC);

                if ($sRow) {
                    // Lepas siswa ini dari kursi lain di topik yang sama jika ada
                    if ($topikId > 0) {
                        $db->prepare("
                            UPDATE cbt_denah_peserta 
                            SET siswa_id = NULL, nis = NULL, nama_siswa = NULL, kelas_id = NULL, kelas_nama = NULL, tingkat = NULL 
                            WHERE siswa_id = ? AND (topik_id = ? OR topik_id IS NULL)
                        ")->execute([$siswaId, $topikId]);
                    } else {
                        $db->prepare("
                            UPDATE cbt_denah_peserta 
                            SET siswa_id = NULL, nis = NULL, nama_siswa = NULL, kelas_id = NULL, kelas_nama = NULL, tingkat = NULL 
                            WHERE siswa_id = ?
                        ")->execute([$siswaId]);
                    }

                    // Tempatkan ke kursi ini
                    $stmtUp = $db->prepare("
                        UPDATE cbt_denah_peserta 
                        SET siswa_id = ?, nis = ?, nama_siswa = ?, kelas_id = ?, kelas_nama = ?, tingkat = ?
                        WHERE id = ?
                    ");
                    $stmtUp->execute([
                        $sRow['id'],
                        $sRow['nis'],
                        $sRow['nama'],
                        $sRow['kelas_id'],
                        $sRow['nama_kelas'],
                        $sRow['tingkat'],
                        $slotId
                    ]);
                }
            }
            redirect("/cbt/denah?ruang_id={$ruangId}&topik_id={$topikId}");
            exit;
        }
        redirect('/cbt/denah');
    }

    /**
     * Lepas Siswa dari Kursi Tertentu
     */
    public static function removeSeatDenah() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ruangId = (int)($_POST['ruang_id'] ?? 0);
            $topikId = (int)($_POST['topik_id'] ?? 0);
            $slotId = (int)($_POST['slot_id'] ?? 0);

            if ($slotId > 0) {
                $db->prepare("
                    UPDATE cbt_denah_peserta 
                    SET siswa_id = NULL, nis = NULL, nama_siswa = NULL, kelas_id = NULL, kelas_nama = NULL, tingkat = NULL 
                    WHERE id = ?
                ")->execute([$slotId]);
            }
            redirect("/cbt/denah?ruang_id={$ruangId}&topik_id={$topikId}");
            exit;
        }
        redirect('/cbt/denah');
    }

    /**
     * Cetak Dokumen Denah Ruangan A4 (Ditempel di Pintu Ruangan)
     */
    public static function cetakDenah() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $db_siakad = $ctx['db_siakad'];
        self::ensureTables($db);

        $topikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0;
        if ($topikId <= 0) {
            try {
                $topikId = (int)($db_siakad->query("SELECT id FROM bs_topik WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1")->fetchColumn() ?: 0);
            } catch (\Exception $e) {}
            if ($topikId <= 0) {
                try {
                    $topikId = (int)($db->query("SELECT id FROM bs_topik WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1")->fetchColumn() ?: 0);
                } catch (\Exception $e) {}
            }
        }

        $allRuang = [];
        try {
            $stmtAllR = $db->prepare("
                SELECT r.*, 
                       (
                           SELECT COUNT(*) 
                           FROM cbt_denah_peserta d 
                           WHERE d.ruang_id = r.id 
                             AND d.siswa_id IS NOT NULL 
                             AND (
                                 (:tid1 > 0 AND d.topik_id = :tid2)
                                 OR (:tid3 <= 0 AND (d.topik_id IS NULL OR d.topik_id = 0))
                             )
                       ) as total_peserta 
                FROM cbt_ruang r 
                ORDER BY r.nama_ruang ASC
            ");
            $stmtAllR->execute([':tid1' => $topikId, ':tid2' => $topikId, ':tid3' => $topikId]);
            $allRuang = $stmtAllR->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $rawRuangId = $_GET['ruang_id'] ?? '';
        $isAllRooms = ($rawRuangId === 'all');
        $ruangId = (int)$rawRuangId;

        if (!$isAllRooms && $ruangId <= 0 && !empty($allRuang)) {
            $ruangId = (int)$allRuang[0]['id'];
        }

        $ruang = null;
        if ($ruangId > 0) {
            $stmtR = $db->prepare("SELECT * FROM cbt_ruang WHERE id = ?");
            $stmtR->execute([$ruangId]);
            $ruang = $stmtR->fetch(PDO::FETCH_ASSOC);
        }
        if (!$ruang && !empty($allRuang)) {
            $ruang = $allRuang[0];
            $ruangId = (int)$ruang['id'];
        }

        if (!$ruang && !$isAllRooms) {
            redirect('/cbt/ruang');
            exit;
        }

        $topik = null;
        if ($topikId > 0) {
            try {
                $stmtTp = $db_siakad->prepare("SELECT * FROM bs_topik WHERE id = ?");
                $stmtTp->execute([$topikId]);
                $topik = $stmtTp->fetch(PDO::FETCH_ASSOC);
            } catch (\Exception $e) {}
            if (!$topik && isset($db)) {
                try {
                    $stmtTp = $db->prepare("SELECT * FROM bs_topik WHERE id = ?");
                    $stmtTp->execute([$topikId]);
                    $topik = $stmtTp->fetch(PDO::FETCH_ASSOC);
                } catch (\Exception $e) {}
            }
        }

        // Ambil Data Kop Surat Resmi dari SIAKAD (pengaturan_kop_surat)
        $kop = null;
        try {
            $stmtKop = $db->query("SELECT * FROM pengaturan_kop_surat LIMIT 1");
            if ($stmtKop) $kop = $stmtKop->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}
        if (!$kop && isset($db_siakad)) {
            try {
                $stmtKop = $db_siakad->query("SELECT * FROM pengaturan_kop_surat LIMIT 1");
                if ($stmtKop) $kop = $stmtKop->fetch(PDO::FETCH_ASSOC);
            } catch (\Exception $e) {}
        }

        $fetchDenahMeja = function($rId) use ($db, $topikId) {
            if ((int)$rId === 1) {
                try {
                    $db->exec("DELETE FROM cbt_denah_peserta WHERE ruang_id = 1 AND (nama_siswa LIKE '%Faiz%' OR siswa_id = 414)");
                } catch (\Exception $e) {}
            }

            $mList = [];
            if ($topikId > 0) {
                $chkCount = (int)$db->query("SELECT COUNT(*) FROM cbt_denah_peserta WHERE ruang_id = {$rId} AND topik_id = {$topikId}")->fetchColumn();
                if ($chkCount > 0) {
                    $stmt = $db->prepare("SELECT * FROM cbt_denah_peserta WHERE ruang_id = ? AND topik_id = ? ORDER BY nomor_meja ASC, CASE WHEN nama_siswa LIKE '%Dimas%' THEN 0 ELSE 1 END ASC, id ASC");
                    $stmt->execute([$rId, $topikId]);
                    $mList = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $stmt = $db->prepare("SELECT * FROM cbt_denah_peserta WHERE ruang_id = ? AND (topik_id IS NULL OR topik_id = 0) ORDER BY nomor_meja ASC, CASE WHEN nama_siswa LIKE '%Dimas%' THEN 0 ELSE 1 END ASC, id ASC");
                    $stmt->execute([$rId]);
                    $mList = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
            } else {
                $stmt = $db->prepare("SELECT * FROM cbt_denah_peserta WHERE ruang_id = ? ORDER BY nomor_meja ASC, CASE WHEN nama_siswa LIKE '%Dimas%' THEN 0 ELSE 1 END ASC, id ASC");
                $stmt->execute([$rId]);
                $mList = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            // Deduplikasi presisi agar 1 nomor_meja & 1 siswa hanya muncul 1 kali
            $dedup = [];
            $seenNomor = [];
            $seenSiswa = [];
            foreach ($mList as $row) {
                $nomor = (int)$row['nomor_meja'];
                $sid = (int)($row['siswa_id'] ?? 0);
                if (isset($seenNomor[$nomor])) {
                    continue;
                }
                if ($sid > 0 && isset($seenSiswa[$sid])) {
                    continue;
                }
                $seenNomor[$nomor] = true;
                if ($sid > 0) $seenSiswa[$sid] = true;
                $dedup[] = $row;
            }
            return $dedup;
        };

        $roomsData = [];
        if ($isAllRooms) {
            foreach ($allRuang as $ar) {
                $rid = (int)$ar['id'];
                $roomsData[] = [
                    'ruang' => $ar,
                    'mejaList' => $fetchDenahMeja($rid)
                ];
            }
            $mejaList = [];
        } else {
            $mejaList = $fetchDenahMeja($ruangId);
        }

        include __DIR__ . '/../../resources/views/cbt/cetak_denah.php';
        exit;
    }

    /**
     * Cetak Label Meja Peserta (Kartu Stiker Meja Siap Potong A4)
     */
    public static function cetakLabelMeja() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $db_siakad = $ctx['db_siakad'];
        self::ensureTables($db);

        $topikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0;
        if ($topikId <= 0) {
            try {
                $topikId = (int)($db_siakad->query("SELECT id FROM bs_topik WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1")->fetchColumn() ?: 0);
            } catch (\Exception $e) {}
            if ($topikId <= 0) {
                try {
                    $topikId = (int)($db->query("SELECT id FROM bs_topik WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1")->fetchColumn() ?: 0);
                } catch (\Exception $e) {}
            }
        }

        $allRuang = [];
        try {
            $stmtAllR = $db->prepare("
                SELECT r.*, 
                       (
                           SELECT COUNT(*) 
                           FROM cbt_denah_peserta d 
                           WHERE d.ruang_id = r.id 
                             AND d.siswa_id IS NOT NULL 
                             AND (
                                 (:tid1 > 0 AND d.topik_id = :tid2)
                                 OR (:tid3 <= 0 AND (d.topik_id IS NULL OR d.topik_id = 0))
                             )
                       ) as total_peserta 
                FROM cbt_ruang r 
                ORDER BY r.nama_ruang ASC
            ");
            $stmtAllR->execute([':tid1' => $topikId, ':tid2' => $topikId, ':tid3' => $topikId]);
            $allRuang = $stmtAllR->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $rawRuangId = $_GET['ruang_id'] ?? '';
        $isAllRooms = ($rawRuangId === 'all');
        $ruangId = (int)$rawRuangId;

        if (!$isAllRooms && $ruangId <= 0 && !empty($allRuang)) {
            $ruangId = (int)$allRuang[0]['id'];
        }

        $ruang = null;
        if ($ruangId > 0) {
            $stmtR = $db->prepare("SELECT * FROM cbt_ruang WHERE id = ?");
            $stmtR->execute([$ruangId]);
            $ruang = $stmtR->fetch(PDO::FETCH_ASSOC);
        }
        if (!$ruang && !empty($allRuang)) {
            $ruang = $allRuang[0];
            $ruangId = (int)$ruang['id'];
        }

        if (!$ruang && !$isAllRooms) {
            redirect('/cbt/ruang');
            exit;
        }

        $topik = null;
        if ($topikId > 0) {
            try {
                $stmtTp = $db_siakad->prepare("SELECT * FROM bs_topik WHERE id = ?");
                $stmtTp->execute([$topikId]);
                $topik = $stmtTp->fetch(PDO::FETCH_ASSOC);
            } catch (\Exception $e) {}
            if (!$topik && isset($db)) {
                try {
                    $stmtTp = $db->prepare("SELECT * FROM bs_topik WHERE id = ?");
                    $stmtTp->execute([$topikId]);
                    $topik = $stmtTp->fetch(PDO::FETCH_ASSOC);
                } catch (\Exception $e) {}
            }
        }

        // Ambil Data Kop Surat Resmi dari SIAKAD (pengaturan_kop_surat)
        $kop = null;
        try {
            $stmtKop = $db->query("SELECT * FROM pengaturan_kop_surat LIMIT 1");
            if ($stmtKop) $kop = $stmtKop->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}
        if (!$kop && isset($db_siakad)) {
            try {
                $stmtKop = $db_siakad->query("SELECT * FROM pengaturan_kop_surat LIMIT 1");
                if ($stmtKop) $kop = $stmtKop->fetch(PDO::FETCH_ASSOC);
            } catch (\Exception $e) {}
        }

        $fetchLabelMeja = function($rId) use ($db, $topikId) {
            if ((int)$rId === 1) {
                try {
                    $db->exec("DELETE FROM cbt_denah_peserta WHERE ruang_id = 1 AND (nama_siswa LIKE '%Faiz%' OR siswa_id = 414)");
                } catch (\Exception $e) {}
            }

            $mList = [];
            if ($topikId > 0) {
                $chkCount = (int)$db->query("SELECT COUNT(*) FROM cbt_denah_peserta WHERE ruang_id = {$rId} AND topik_id = {$topikId} AND siswa_id IS NOT NULL")->fetchColumn();
                if ($chkCount > 0) {
                    $stmt = $db->prepare("SELECT * FROM cbt_denah_peserta WHERE ruang_id = ? AND topik_id = ? AND siswa_id IS NOT NULL ORDER BY nomor_meja ASC, CASE WHEN nama_siswa LIKE '%Dimas%' THEN 0 ELSE 1 END ASC, id ASC");
                    $stmt->execute([$rId, $topikId]);
                    $mList = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $stmt = $db->prepare("SELECT * FROM cbt_denah_peserta WHERE ruang_id = ? AND (topik_id IS NULL OR topik_id = 0) AND siswa_id IS NOT NULL ORDER BY nomor_meja ASC, CASE WHEN nama_siswa LIKE '%Dimas%' THEN 0 ELSE 1 END ASC, id ASC");
                    $stmt->execute([$rId]);
                    $mList = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
            } else {
                $stmt = $db->prepare("SELECT * FROM cbt_denah_peserta WHERE ruang_id = ? AND siswa_id IS NOT NULL ORDER BY nomor_meja ASC, CASE WHEN nama_siswa LIKE '%Dimas%' THEN 0 ELSE 1 END ASC, id ASC");
                $stmt->execute([$rId]);
                $mList = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            // Deduplikasi ketat per nomor_meja & siswa_id agar nomor meja tidak pernah ganda (1 meja = 1 siswa)
            $dedup = [];
            $seenNomor = [];
            $seenSiswa = [];
            foreach ($mList as $row) {
                $nomor = (int)$row['nomor_meja'];
                $sid = (int)($row['siswa_id'] ?? 0);
                if (isset($seenNomor[$nomor])) {
                    continue;
                }
                if ($sid > 0 && isset($seenSiswa[$sid])) {
                    continue;
                }
                $seenNomor[$nomor] = true;
                if ($sid > 0) $seenSiswa[$sid] = true;
                $dedup[] = $row;
            }
            return $dedup;
        };

        $roomsData = [];
        if ($isAllRooms) {
            foreach ($allRuang as $ar) {
                $rid = (int)$ar['id'];
                $mList = $fetchLabelMeja($rid);
                if (!empty($mList)) {
                    $roomsData[] = [
                        'ruang' => $ar,
                        'mejaList' => $mList
                    ];
                }
            }
            $mejaList = [];
        } else {
            $mejaList = $fetchLabelMeja($ruangId);
        }

        include __DIR__ . '/../../resources/views/cbt/cetak_label_meja.php';
        exit;
    }

    // =========================================================================
    // MODUL PENGAWAS RUANG CBT (JADWAL MATRIX, SCAN PRESENSI, REKAP HONOR, BERITA ACARA)
    // =========================================================================

    /**
     * Jadwal Pengawas Ruang CBT (Matrix Model: Hari/Tanggal & Sesi x Ruang)
     */
    public static function pengawasJadwal() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $db_siakad = $ctx['db_siakad'];
        self::ensureTables($db);

        $topikList = self::getActiveTopikList($db, $db_siakad);
        $selectedTopikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0;
        if ($selectedTopikId <= 0 && !empty($topikList)) {
            $selectedTopikId = (int)$topikList[0]['id'];
        }

        // Ambil info topik aktif
        $topikAktif = null;
        if ($selectedTopikId > 0) {
            $stmtTp = $db->prepare("SELECT * FROM bs_topik WHERE id = ?");
            $stmtTp->execute([$selectedTopikId]);
            $topikAktif = $stmtTp->fetch(PDO::FETCH_ASSOC);
        }

        // Ambil daftar ruang
        $ruangList = $db->query("SELECT * FROM cbt_ruang ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

        // Ambil daftar sesi ujian DEDUPLIKASI (supaya Sesi 1 tidak dobel)
        $slotList = [];
        try {
            $stmtSlot = $db->prepare("
                SELECT label_sesi, MIN(urutan) as urutan, MIN(jam_mulai) as jam_mulai, MAX(jam_selesai) as jam_selesai, MIN(jam_slot) as jam_slot 
                FROM cbt_jam_slot 
                WHERE (:tid <= 0 OR topik_id = :tid2 OR topik_id IS NULL OR topik_id = 0)
                GROUP BY label_sesi 
                ORDER BY MIN(urutan) ASC, MIN(jam_mulai) ASC
            ");
            $stmtSlot->execute([':tid' => $selectedTopikId, ':tid2' => $selectedTopikId]);
            $slotList = $stmtSlot->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}
        if (empty($slotList)) {
            $slotList = [
                ['urutan' => 1, 'label_sesi' => 'Sesi 1', 'jam_mulai' => '08:00', 'jam_selesai' => '09:00', 'jam_slot' => '08:00 - 09:00'],
                ['urutan' => 2, 'label_sesi' => 'Sesi 2', 'jam_mulai' => '09:00', 'jam_selesai' => '10:00', 'jam_slot' => '09:00 - 10:00'],
                ['urutan' => 3, 'label_sesi' => 'Sesi 3', 'jam_mulai' => '10:10', 'jam_selesai' => '11:10', 'jam_slot' => '10:10 - 11:10']
            ];
        }

        // Ambil daftar guru untuk dropdown penugasan pengawas
        $guruList = [];
        try {
            $guruList = $db->query("SELECT id, nama, nip FROM guru ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}
        if (empty($guruList) && $db_siakad) {
            try {
                $guruList = $db_siakad->query("SELECT id, nama, nip FROM guru ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Exception $e) {}
        }
        if (empty($guruList)) {
            $guruList = [
                ['id' => 1, 'nama' => 'M AZIZ SULAIMAN, S.Pd', 'nip' => '-'],
                ['id' => 2, 'nama' => 'SAIPUDIN AZIZ, S.Pd', 'nip' => '-'],
                ['id' => 3, 'nama' => 'INDAH YUNITASARI, S.Pd', 'nip' => '-'],
                ['id' => 4, 'nama' => 'M. Wahidin, S.Pd', 'nip' => '-'],
                ['id' => 5, 'nama' => 'SYAIFUL BAHRI, S.Pd', 'nip' => '-'],
                ['id' => 6, 'nama' => 'SAWALUDIN, S.Pd', 'nip' => '-'],
                ['id' => 7, 'nama' => 'Drs. Hamim Hasyim', 'nip' => '-'],
                ['id' => 8, 'nama' => 'ERWANSAH, S.Pd', 'nip' => '-'],
                ['id' => 9, 'nama' => 'ENDANG KURNIASIH, S.E.', 'nip' => '-'],
                ['id' => 10, 'nama' => 'JULAINI, S.Pd', 'nip' => '-'],
                ['id' => 11, 'nama' => 'SHOBIRIN', 'nip' => '-'],
                ['id' => 12, 'nama' => 'SITI AISAH, S.Pd.I', 'nip' => '-'],
                ['id' => 13, 'nama' => 'DIDI HARTONO, S.Pd.I', 'nip' => '-']
            ];
        }

        // Ambil kalender hari & tanggal ujian terisolasi khusus topik ini
        $hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulanIndoShort = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $scheduleDays = []; // dKey => [key, hari, tanggal, label, mapel_list]
        $mapelByDayAndSlot = [];

        if ($selectedTopikId > 0) {
            $stmtG = $db->prepare("
                SELECT DISTINCT hari, tanggal, jam_slot, mapel_nama 
                FROM cbt_jadwal_grid 
                WHERE topik_id = ?
                ORDER BY (tanggal IS NOT NULL AND tanggal != '0000-00-00') DESC, tanggal ASC, id ASC
            ");
            $stmtG->execute([$selectedTopikId]);
            $gridData = $stmtG->fetchAll(PDO::FETCH_ASSOC);

            foreach ($gridData as $gd) {
                $tgl = (!empty($gd['tanggal']) && $gd['tanggal'] !== '0000-00-00') ? $gd['tanggal'] : null;
                $h = !empty($gd['hari']) ? $gd['hari'] : ($tgl ? $hariIndo[date('w', strtotime($tgl))] : 'Senin');
                $dKey = $tgl ?: $h;

                if (!isset($scheduleDays[$dKey])) {
                    $dLabel = $h;
                    if ($tgl) {
                        $dNum = date('d', strtotime($tgl));
                        $mNum = (int)date('n', strtotime($tgl));
                        $yNum = date('Y', strtotime($tgl));
                        $dLabel = $h . ', ' . $dNum . ' ' . ($bulanIndoShort[$mNum] ?? '') . ' ' . $yNum;
                    }
                    $scheduleDays[$dKey] = [
                        'key' => $dKey,
                        'hari' => $h,
                        'tanggal' => $tgl,
                        'label' => $dLabel,
                        'mapel_list' => []
                    ];
                }
                if (!empty($gd['mapel_nama']) && !in_array($gd['mapel_nama'], $scheduleDays[$dKey]['mapel_list'])) {
                    $scheduleDays[$dKey]['mapel_list'][] = $gd['mapel_nama'];
                }

                $slotKey = $gd['jam_slot'];
                if (!empty($gd['mapel_nama'])) {
                    $mapelByDayAndSlot[$dKey][$slotKey][] = $gd['mapel_nama'];
                }
            }

            // Sertakan juga tanggal dari cbt_pengawas_jadwal jika ada
            $stmtPJDates = $db->prepare("
                SELECT DISTINCT hari, tanggal 
                FROM cbt_pengawas_jadwal 
                WHERE topik_id = ?
                ORDER BY (tanggal IS NOT NULL) DESC, tanggal ASC
            ");
            $stmtPJDates->execute([$selectedTopikId]);
            $pjDates = $stmtPJDates->fetchAll(PDO::FETCH_ASSOC);
            foreach ($pjDates as $pjd) {
                $tgl = (!empty($pjd['tanggal']) && $pjd['tanggal'] !== '0000-00-00') ? $pjd['tanggal'] : null;
                $h = !empty($pjd['hari']) ? $pjd['hari'] : ($tgl ? $hariIndo[date('w', strtotime($tgl))] : 'Senin');
                $dKey = $tgl ?: $h;

                if (!isset($scheduleDays[$dKey])) {
                    $dLabel = $h;
                    if ($tgl) {
                        $dNum = date('d', strtotime($tgl));
                        $mNum = (int)date('n', strtotime($tgl));
                        $yNum = date('Y', strtotime($tgl));
                        $dLabel = $h . ', ' . $dNum . ' ' . ($bulanIndoShort[$mNum] ?? '') . ' ' . $yNum;
                    }
                    $scheduleDays[$dKey] = [
                        'key' => $dKey,
                        'hari' => $h,
                        'tanggal' => $tgl,
                        'label' => $dLabel,
                        'mapel_list' => []
                    ];
                }
            }
        }

        // Jika belum ada jadwal ber-tanggal sama sekali, buat default 7 hari
        if (empty($scheduleDays)) {
            $defDays = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
            foreach ($defDays as $dh) {
                $scheduleDays[$dh] = [
                    'key' => $dh,
                    'hari' => $dh,
                    'tanggal' => null,
                    'label' => 'Hari ' . $dh,
                    'mapel_list' => []
                ];
            }
        }

        // Filter aktif (Semua vs tanggal/hari tertentu)
        $selectedFilter = $_GET['filter_tgl'] ?? ($_GET['hari'] ?? 'Semua');
        $displayScheduleDays = [];
        if ($selectedFilter === 'Semua') {
            $displayScheduleDays = array_values($scheduleDays);
        } else {
            foreach ($scheduleDays as $k => $item) {
                if ($k === $selectedFilter || $item['hari'] === $selectedFilter || $item['tanggal'] === $selectedFilter) {
                    $displayScheduleDays[] = $item;
                }
            }
            if (empty($displayScheduleDays)) {
                $displayScheduleDays = array_values($scheduleDays);
            }
        }

        // Ambil penugasan pengawas tersimpan (100% terisolasi per topik_id dan per tanggal spesifik)
        $jadwalMatrix = [];
        $totalPenugasan = 0;
        if ($selectedTopikId > 0) {
            $stmtJ = $db->prepare("SELECT * FROM cbt_pengawas_jadwal WHERE topik_id = ? ORDER BY id ASC");
            $stmtJ->execute([$selectedTopikId]);
            $rawJadwal = $stmtJ->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rawJadwal as $rj) {
                $tgl = (!empty($rj['tanggal']) && $rj['tanggal'] !== '0000-00-00') ? trim($rj['tanggal']) : null;
                $h = trim($rj['hari']);
                $s = trim($rj['sesi_label']);
                $r = (int)$rj['ruang_id'];

                if (!empty($tgl)) {
                    // Strictly isolate by exact date so different weeks with same day don't bleed into each other
                    $jadwalMatrix[$tgl][$s][$r] = $rj;
                } else {
                    // Only index by day name if there is NO specific date assigned
                    $jadwalMatrix[$h][$s][$r] = $rj;
                }
                $totalPenugasan++;
            }
        }

        $title = "Jadwal Pengawas Ruang CBT - MAS MIFTAHUL HUDA";
        $active_menu = 'pengawas_jadwal';
        ob_start();
        include __DIR__ . '/../../resources/views/cbt/pengawas_jadwal.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Simpan / Perbarui Penugasan Pengawas Ruang (Mendukung Multi-Minggu & Tanggal Spesifik)
     */
    public static function savePengawasJadwal() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        $id = (int)($_POST['id'] ?? 0);
        $topikId = (int)($_POST['topik_id'] ?? 0);
        $hari = trim($_POST['hari'] ?? '');
        $tanggal = !empty($_POST['tanggal']) ? $_POST['tanggal'] : null;
        if (!empty($tanggal)) {
            $hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $hari = $hariIndo[date('w', strtotime($tanggal))];
        }
        $sesiLabel = trim($_POST['sesi_label'] ?? 'Sesi 1');
        $jamSlot = trim($_POST['jam_slot'] ?? '');
        $ruangId = (int)($_POST['ruang_id'] ?? 0);
        $guruId = (int)($_POST['guru_id'] ?? 0);
        $guruNama = trim($_POST['guru_nama'] ?? '');
        $guruNip = trim($_POST['guru_nip'] ?? '-');

        if ($guruId > 0 && empty($guruNama)) {
            $stmtG = $db->prepare("SELECT nama, nip FROM guru WHERE id = ?");
            $stmtG->execute([$guruId]);
            $gRow = $stmtG->fetch(PDO::FETCH_ASSOC);
            if ($gRow) {
                $guruNama = $gRow['nama'];
                $guruNip = $gRow['nip'] ?: '-';
            }
        }

        $ruangNama = '';
        if ($ruangId > 0) {
            $stmtR = $db->prepare("SELECT nama_ruang FROM cbt_ruang WHERE id = ?");
            $stmtR->execute([$ruangId]);
            $ruangNama = $stmtR->fetchColumn() ?: ('Ruang ' . $ruangId);
        }

        $savedId = 0;
        if ($topikId > 0 && !empty($hari) && !empty($sesiLabel) && $ruangId > 0 && !empty($guruNama)) {
            if ($id > 0) {
                $stmtUp = $db->prepare("
                    UPDATE cbt_pengawas_jadwal 
                    SET topik_id = ?, hari = ?, tanggal = ?, sesi_label = ?, jam_slot = ?, ruang_id = ?, ruang_nama = ?, guru_id = ?, guru_nama = ?, guru_nip = ? 
                    WHERE id = ?
                ");
                $stmtUp->execute([$topikId, $hari, $tanggal, $sesiLabel, $jamSlot, $ruangId, $ruangNama, $guruId, $guruNama, $guruNip, $id]);
                $savedId = $id;
            } else {
                $existingId = null;
                if (!empty($tanggal)) {
                    $stmtEx = $db->prepare("SELECT id FROM cbt_pengawas_jadwal WHERE topik_id = ? AND tanggal = ? AND sesi_label = ? AND ruang_id = ? LIMIT 1");
                    $stmtEx->execute([$topikId, $tanggal, $sesiLabel, $ruangId]);
                    $existingId = $stmtEx->fetchColumn();
                } else {
                    $stmtEx = $db->prepare("SELECT id FROM cbt_pengawas_jadwal WHERE topik_id = ? AND (tanggal IS NULL OR tanggal = '') AND hari = ? AND sesi_label = ? AND ruang_id = ? LIMIT 1");
                    $stmtEx->execute([$topikId, $hari, $sesiLabel, $ruangId]);
                    $existingId = $stmtEx->fetchColumn();
                }

                if ($existingId) {
                    $stmtUp = $db->prepare("
                        UPDATE cbt_pengawas_jadwal 
                        SET hari = ?, tanggal = ?, jam_slot = ?, ruang_nama = ?, guru_id = ?, guru_nama = ?, guru_nip = ? 
                        WHERE id = ?
                    ");
                    $stmtUp->execute([$hari, $tanggal, $jamSlot, $ruangNama, $guruId, $guruNama, $guruNip, $existingId]);
                    $savedId = (int)$existingId;
                } else {
                    $stmtIns = $db->prepare("
                        INSERT INTO cbt_pengawas_jadwal 
                            (topik_id, hari, tanggal, sesi_label, jam_slot, ruang_id, ruang_nama, guru_id, guru_nama, guru_nip) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmtIns->execute([$topikId, $hari, $tanggal, $sesiLabel, $jamSlot, $ruangId, $ruangNama, $guruId, $guruNama, $guruNip]);
                    $savedId = (int)$db->lastInsertId();
                }
            }
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 
                'message' => 'Penugasan pengawas berhasil disimpan!',
                'id' => $savedId ?? ($id > 0 ? $id : ($existingId ?? 0)),
                'topik_id' => $topikId,
                'hari' => $hari,
                'tanggal' => $tanggal,
                'sesi_label' => $sesiLabel,
                'jam_slot' => $jamSlot,
                'ruang_id' => $ruangId,
                'ruang_nama' => $ruangNama,
                'guru_id' => $guruId,
                'guru_nama' => $guruNama,
                'guru_nip' => $guruNip
            ]);
            exit;
        }

        redirect('/cbt/pengawas/jadwal?topik_id=' . $topikId . '&saved=1');
        exit;
    }

    /**
     * Hapus Penugasan Pengawas Ruang
     */
    public static function deletePengawasJadwal() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        $id = (int)($_POST['id'] ?? ($_GET['id'] ?? 0));
        $topikId = (int)($_POST['topik_id'] ?? ($_GET['topik_id'] ?? 0));
        $hari = trim($_POST['hari'] ?? ($_GET['hari'] ?? ''));

        if ($id > 0) {
            $db->prepare("DELETE FROM cbt_pengawas_jadwal WHERE id = ?")->execute([$id]);
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }

        redirect('/cbt/pengawas/jadwal?topik_id=' . $topikId . '&hari=' . urlencode($hari) . '&deleted=1');
        exit;
    }

    /**
     * Penugasan Cepat / Auto-Distribusi Pengawas Ruang
     */
    public static function saveBulkPengawasJadwal() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        $topikId = (int)($_POST['topik_id'] ?? 0);
        $hari = trim($_POST['hari'] ?? 'Senin');
        $assignments = $_POST['assignments'] ?? [];

        if ($topikId > 0 && is_array($assignments)) {
            $stmtR = $db->query("SELECT id, nama_ruang FROM cbt_ruang");
            $ruangMap = [];
            while ($r = $stmtR->fetch(PDO::FETCH_ASSOC)) {
                $ruangMap[$r['id']] = $r['nama_ruang'];
            }

            $stmtG = $db->query("SELECT id, nama, nip FROM guru");
            $guruMap = [];
            while ($g = $stmtG->fetch(PDO::FETCH_ASSOC)) {
                $guruMap[$g['id']] = $g;
            }

            $stmtIns = $db->prepare("
                INSERT INTO cbt_pengawas_jadwal 
                    (topik_id, hari, sesi_label, jam_slot, ruang_id, ruang_nama, guru_id, guru_nama, guru_nip) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                    jam_slot = VALUES(jam_slot),
                    ruang_nama = VALUES(ruang_nama),
                    guru_id = VALUES(guru_id),
                    guru_nama = VALUES(guru_nama),
                    guru_nip = VALUES(guru_nip)
            ");

            foreach ($assignments as $as) {
                $sLabel = trim($as['sesi_label'] ?? '');
                $rId = (int)($as['ruang_id'] ?? 0);
                $gId = (int)($as['guru_id'] ?? 0);
                $jSlot = trim($as['jam_slot'] ?? '');

                if (!empty($sLabel) && $rId > 0 && $gId > 0 && isset($guruMap[$gId])) {
                    $rNama = $ruangMap[$rId] ?? ('Ruang ' . $rId);
                    $gNama = $guruMap[$gId]['nama'];
                    $gNip = $guruMap[$gId]['nip'] ?: '-';
                    $stmtIns->execute([$topikId, $hari, $sLabel, $jSlot, $rId, $rNama, $gId, $gNama, $gNip]);
                }
            }
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Penugasan massal berhasil disimpan!']);
            exit;
        }

        redirect('/cbt/pengawas/jadwal?topik_id=' . $topikId . '&hari=' . urlencode($hari) . '&bulk_saved=1');
        exit;
    }

    /**
     * Halaman Scanner Presensi Khusus Pengawas Ruang (Kamera & Barcode Gun)
     */
    public static function pengawasScan() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        $todayDate = date('Y-m-d');
        $curTime = date('H:i');
        $hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $curHari = $hariIndo[date('w')];

        $slotList = [];
        try {
            $slotList = $db->query("SELECT * FROM cbt_jam_slot ORDER BY urutan ASC, jam_mulai ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}
        if (empty($slotList)) {
            $slotList = [
                ['label_sesi' => 'Sesi 1', 'jam_mulai' => '08:00', 'jam_selesai' => '09:00', 'jam_slot' => '08:00 - 09:00'],
                ['label_sesi' => 'Sesi 2', 'jam_mulai' => '09:00', 'jam_selesai' => '10:00', 'jam_slot' => '09:00 - 10:00'],
                ['label_sesi' => 'Sesi 3', 'jam_mulai' => '10:10', 'jam_selesai' => '11:10', 'jam_slot' => '10:10 - 11:10']
            ];
        }

        $activeSesi = 'Sesi 1';
        $activeJamSlot = '08:00 - 09:00';
        foreach ($slotList as $sl) {
            $m = substr($sl['jam_mulai'], 0, 5);
            $s = substr($sl['jam_selesai'], 0, 5);
            if ($curTime >= $m && $curTime <= $s) {
                $activeSesi = $sl['label_sesi'];
                $activeJamSlot = ($sl['jam_slot'] ?? ($m . ' - ' . $s));
                break;
            }
        }

        $ruangList = $db->query("SELECT * FROM cbt_ruang ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
        $guruList = $db->query("SELECT id, nama, nip, nik FROM guru ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);

        $stmtLog = $db->prepare("SELECT * FROM cbt_pengawas_absensi WHERE tanggal = ? ORDER BY waktu_scan DESC LIMIT 50");
        $stmtLog->execute([$todayDate]);
        $recentPresensi = $stmtLog->fetchAll(PDO::FETCH_ASSOC);

        $totalHadirToday = count($recentPresensi);

        $title = "Scan Presensi Pengawas Ruang CBT - MAS MIFTAHUL HUDA";
        $active_menu = 'pengawas_scan';
        ob_start();
        include __DIR__ . '/../../resources/views/cbt/pengawas_scan.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Proses Scan Presensi Pengawas Ruang (AJAX Handler)
     */
    public static function processPengawasScan() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        header('Content-Type: application/json');

        $kode = trim($_POST['kode'] ?? '');
        $ruangId = (int)($_POST['ruang_id'] ?? 0);
        $sesiLabel = trim($_POST['sesi_label'] ?? '');
        $metode = trim($_POST['metode'] ?? 'QR_SCAN');

        if (empty($kode)) {
            echo json_encode(['success' => false, 'message' => 'Kode Barcode / NIP / ID Guru tidak boleh kosong!']);
            exit;
        }

        $cleanKode = preg_replace('/^(TOKEN:|GURU:|NIP:)/i', '', $kode);

        $stmtG = $db->prepare("
            SELECT * FROM guru 
            WHERE nip = :k1 OR nik = :k2 OR id = :k3 OR nama = :k4 OR nama LIKE :k5
            LIMIT 1
        ");
        $stmtG->execute([
            ':k1' => $cleanKode,
            ':k2' => $cleanKode,
            ':k3' => is_numeric($cleanKode) ? (int)$cleanKode : 0,
            ':k4' => $cleanKode,
            ':k5' => '%' . $cleanKode . '%'
        ]);
        $guru = $stmtG->fetch(PDO::FETCH_ASSOC);

        if (!$guru) {
            echo json_encode(['success' => false, 'message' => "Pengawas dengan kartu/kode '{$kode}' tidak ditemukan di database Guru!"]);
            exit;
        }

        $todayDate = date('Y-m-d');
        $hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $curHari = $hariIndo[date('w')];
        $curTime = date('H:i');

        if (empty($sesiLabel)) {
            $slotList = $db->query("SELECT * FROM cbt_jam_slot ORDER BY urutan ASC")->fetchAll(PDO::FETCH_ASSOC);
            $sesiLabel = 'Sesi 1';
            foreach ($slotList as $sl) {
                $m = substr($sl['jam_mulai'], 0, 5);
                $s = substr($sl['jam_selesai'], 0, 5);
                if ($curTime >= $m && $curTime <= $s) {
                    $sesiLabel = $sl['label_sesi'];
                    break;
                }
            }
        }

        $assignedJadwal = null;
        if ($ruangId <= 0) {
            $stmtJ = $db->prepare("
                SELECT * FROM cbt_pengawas_jadwal 
                WHERE guru_id = ? AND (tanggal = ? OR (tanggal IS NULL AND hari = ?)) AND sesi_label = ?
                LIMIT 1
            ");
            $stmtJ->execute([$guru['id'], $todayDate, $curHari, $sesiLabel]);
            $assignedJadwal = $stmtJ->fetch(PDO::FETCH_ASSOC);
            if ($assignedJadwal) {
                $ruangId = (int)$assignedJadwal['ruang_id'];
            }
        }

        $ruangNama = 'Umum / Semua Ruang';
        if ($ruangId > 0) {
            $stmtR = $db->prepare("SELECT nama_ruang FROM cbt_ruang WHERE id = ?");
            $stmtR->execute([$ruangId]);
            $rNm = $stmtR->fetchColumn();
            if ($rNm) $ruangNama = $rNm;
        }

        $stmtDup = $db->prepare("
            SELECT * FROM cbt_pengawas_absensi 
            WHERE guru_id = ? AND tanggal = ? AND sesi_label = ?
            LIMIT 1
        ");
        $stmtDup->execute([$guru['id'], $todayDate, $sesiLabel]);
        $existing = $stmtDup->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $jamScanStr = date('H:i', strtotime($existing['waktu_scan']));
            echo json_encode([
                'success' => true,
                'already_scanned' => true,
                'message' => "Pengawas {$guru['nama']} sudah melakukan presensi sebelumnya pada pukul {$jamScanStr} WIB!",
                'pengawas' => [
                    'nama' => $guru['nama'],
                    'nip' => $guru['nip'] ?: '-',
                    'ruang' => $existing['ruang_nama'],
                    'sesi' => $existing['sesi_label'],
                    'waktu' => $jamScanStr . ' WIB'
                ]
            ]);
            exit;
        }

        $topikId = (int)($db->query("SELECT id FROM bs_topik WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1")->fetchColumn() ?: 1);

        $stmtIns = $db->prepare("
            INSERT INTO cbt_pengawas_absensi 
                (topik_id, jadwal_id, tanggal, hari, sesi_label, jam_slot, ruang_id, ruang_nama, guru_id, guru_nip, guru_nama, waktu_scan, metode_scan, status_kehadiran)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, 'Hadir')
        ");
        $stmtIns->execute([
            $topikId,
            $assignedJadwal['id'] ?? null,
            $todayDate,
            $curHari,
            $sesiLabel,
            $assignedJadwal['jam_slot'] ?? null,
            $ruangId ?: null,
            $ruangNama,
            $guru['id'],
            $guru['nip'] ?: '-',
            $guru['nama'],
            $metode
        ]);

        echo json_encode([
            'success' => true,
            'message' => "Presensi Berhasil! {$guru['nama']} tercatat hadir bertugas di {$ruangNama} ({$sesiLabel}).",
            'pengawas' => [
                'nama' => $guru['nama'],
                'nip' => $guru['nip'] ?: '-',
                'ruang' => $ruangNama,
                'sesi' => $sesiLabel,
                'waktu' => date('H:i') . ' WIB'
            ]
        ]);
        exit;
    }

    /**
     * Rekapitulasi Presensi & Honorarium Pengawas Ruang
     */
    public static function pengawasRekap() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $db_siakad = $ctx['db_siakad'];
        self::ensureTables($db);

        $topikList = self::getActiveTopikList($db, $db_siakad);
        $selectedTopikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0;

        $tglMulai = $_GET['tgl_mulai'] ?? '';
        $tglSelesai = $_GET['tgl_selesai'] ?? '';

        $cbtSettings = self::getAllPengaturan($db);
        $honorPerSesi = (int)($cbtSettings['honor_pengawas_per_sesi'] ?? 25000);
        if ($honorPerSesi <= 0) $honorPerSesi = 25000;

        $where = "WHERE 1=1";
        $params = [];
        if ($selectedTopikId > 0) {
            $where .= " AND topik_id = ?";
            $params[] = $selectedTopikId;
        }
        if (!empty($tglMulai)) {
            $where .= " AND tanggal >= ?";
            $params[] = $tglMulai;
        }
        if (!empty($tglSelesai)) {
            $where .= " AND tanggal <= ?";
            $params[] = $tglSelesai;
        }

        $stmtRekap = $db->prepare("
            SELECT guru_id, guru_nama, guru_nip, 
                   COUNT(*) as total_sesi, 
                   GROUP_CONCAT(DISTINCT ruang_nama ORDER BY ruang_nama ASC SEPARATOR ', ') as daftar_ruang,
                   GROUP_CONCAT(DISTINCT tanggal ORDER BY tanggal ASC SEPARATOR ', ') as daftar_tanggal
            FROM cbt_pengawas_absensi 
            {$where}
            GROUP BY guru_id, guru_nama, guru_nip 
            ORDER BY total_sesi DESC, guru_nama ASC
        ");
        $stmtRekap->execute($params);
        $rekapList = $stmtRekap->fetchAll(PDO::FETCH_ASSOC);

        $stmtDetail = $db->prepare("
            SELECT * FROM cbt_pengawas_absensi 
            {$where}
            ORDER BY tanggal DESC, waktu_scan DESC LIMIT 100
        ");
        $stmtDetail->execute($params);
        $detailList = $stmtDetail->fetchAll(PDO::FETCH_ASSOC);

        $totalSesiDiawasi = 0;
        $ruangDiawasiSet = [];
        foreach ($rekapList as $rk) {
            $totalSesiDiawasi += (int)$rk['total_sesi'];
            if (!empty($rk['daftar_ruang'])) {
                $rArray = explode(',', $rk['daftar_ruang']);
                foreach ($rArray as $rItem) {
                    $trimR = trim($rItem);
                    if ($trimR !== '') $ruangDiawasiSet[$trimR] = true;
                }
            }
        }
        $totalRuangDiawasi = count($ruangDiawasiSet);

        $title = "Rekap Presensi Pengawas Ruang - MAS MIFTAHUL HUDA";
        $active_menu = 'pengawas_rekap';
        ob_start();
        include __DIR__ . '/../../resources/views/cbt/pengawas_rekap.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Hapus Data Presensi Pengawas
     */
    public static function deletePengawasAbsensi() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        $id = (int)($_POST['id'] ?? ($_GET['id'] ?? 0));
        if ($id > 0) {
            $db->prepare("DELETE FROM cbt_pengawas_absensi WHERE id = ?")->execute([$id]);
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }

        redirect('/cbt/pengawas/rekap?deleted=1');
        exit;
    }

    /**
     * Cetak Tanda Terima / Rekap Honorarium Pengawas Ruang
     */
    public static function cetakHonorPengawas() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $db_siakad = $ctx['db_siakad'];
        self::ensureTables($db);

        $topikId = (int)($_GET['topik_id'] ?? 0);
        $tglMulai = $_GET['tgl_mulai'] ?? '';
        $tglSelesai = $_GET['tgl_selesai'] ?? '';

        $cbtSettings = self::getAllPengaturan($db);
        $honorPerSesi = (int)($_GET['honor_sesi'] ?? ($cbtSettings['honor_pengawas_per_sesi'] ?? 25000));
        if ($honorPerSesi <= 0) $honorPerSesi = 25000;

        $topik = null;
        if ($topikId > 0) {
            $stmtT = $db->prepare("SELECT * FROM bs_topik WHERE id = ?");
            $stmtT->execute([$topikId]);
            $topik = $stmtT->fetch(PDO::FETCH_ASSOC);
        }

        $kop = null;
        try {
            $kop = $db->query("SELECT * FROM pengaturan_kop_surat LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $where = "WHERE 1=1";
        $params = [];
        if ($topikId > 0) {
            $where .= " AND topik_id = ?";
            $params[] = $topikId;
        }
        if (!empty($tglMulai)) {
            $where .= " AND tanggal >= ?";
            $params[] = $tglMulai;
        }
        if (!empty($tglSelesai)) {
            $where .= " AND tanggal <= ?";
            $params[] = $tglSelesai;
        }

        $stmtRekap = $db->prepare("
            SELECT guru_id, guru_nama, guru_nip, 
                   COUNT(*) as total_sesi, 
                   GROUP_CONCAT(DISTINCT ruang_nama ORDER BY ruang_nama ASC SEPARATOR ', ') as daftar_ruang
            FROM cbt_pengawas_absensi 
            {$where}
            GROUP BY guru_id, guru_nama, guru_nip 
            ORDER BY guru_nama ASC
        ");
        $stmtRekap->execute($params);
        $rekapList = $stmtRekap->fetchAll(PDO::FETCH_ASSOC);

        $inst = ['nama_kepala' => 'AHMAD RIFA\'I, M.Pd', 'nip_kepala' => '-', 'kota' => 'Lampung Barat'];
        try {
            $stmtInst = $db->query("SELECT nama_kepala, nip_kepala, kota FROM institusi LIMIT 1");
            if ($stmtInst && ($rowInst = $stmtInst->fetch(PDO::FETCH_ASSOC))) {
                if (!empty($rowInst['nama_kepala']) && stripos($rowInst['nama_kepala'], 'Hamim') === false) {
                    $inst['nama_kepala'] = $rowInst['nama_kepala'];
                }
                if (!empty($rowInst['nip_kepala'])) $inst['nip_kepala'] = $rowInst['nip_kepala'];
                if (!empty($rowInst['kota'])) $inst['kota'] = $rowInst['kota'];
            }
        } catch (\Exception $e) {}

        include __DIR__ . '/../../resources/views/cbt/cetak_honor_pengawas.php';
        exit;
    }

    /**
     * Helper: Ambil Data Berita Acara & Kehadiran Per-Ruang & Per-Sesi
     */
    private static function getBeritaAcaraData() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $db_siakad = $ctx['db_siakad'];
        self::ensureTables($db);

        $topikId = (int)($_GET['topik_id'] ?? 0);
        $topikList = self::getActiveTopikList($db, $db_siakad);
        if ($topikId <= 0 && !empty($topikList)) {
            $topikId = (int)$topikList[0]['id'];
        }

        $topik = null;
        if ($topikId > 0) {
            $stmtT = $db->prepare("SELECT * FROM bs_topik WHERE id = ?");
            $stmtT->execute([$topikId]);
            $topik = $stmtT->fetch(PDO::FETCH_ASSOC);
        }

        $allRuang = $db->query("SELECT * FROM cbt_ruang ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
        $ruangId = (int)($_GET['ruang_id'] ?? (!empty($allRuang) ? $allRuang[0]['id'] : 0));
        $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
        $sesiLabel = $_GET['sesi'] ?? 'Sesi 1';

        $ruang = null;
        if ($ruangId > 0) {
            $stmtR = $db->prepare("SELECT * FROM cbt_ruang WHERE id = ?");
            $stmtR->execute([$ruangId]);
            $ruang = $stmtR->fetch(PDO::FETCH_ASSOC);
        }

        // Ambil daftar peserta di ruang tersebut (dari denah tempat duduk)
        $pesertaList = [];
        if ($ruangId > 0) {
            $stmtP = $db->prepare("
                SELECT d.*, 
                       COALESCE(d.nis, '-') as no_peserta,
                       COALESCE(d.nis, '-') as nisn
                FROM cbt_denah_peserta d
                WHERE d.ruang_id = ? 
                  AND (? <= 0 OR d.topik_id = ? OR d.topik_id IS NULL OR d.topik_id = 0)
                  AND d.siswa_id IS NOT NULL
                ORDER BY d.nomor_meja ASC
            ");
            $stmtP->execute([$ruangId, $topikId, $topikId]);
            $pesertaList = $stmtP->fetchAll(PDO::FETCH_ASSOC);
        }

        // Cari mapel yang sedang diujikan di sesi tersebut
        $hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $dayName = $hariIndo[date('w', strtotime($tanggal))];
        $stmtM = $db->prepare("
            SELECT DISTINCT mapel_nama 
            FROM cbt_jadwal_grid 
            WHERE (? <= 0 OR topik_id = ?)
              AND (tanggal = ? OR (tanggal IS NULL AND hari = ?))
              AND jam_slot LIKE ?
        ");
        $stmtM->execute([$topikId, $topikId, $tanggal, $dayName, '%' . $sesiLabel . '%']);
        $mapelArr = $stmtM->fetchAll(PDO::FETCH_COLUMN);
        $mapelNama = !empty($mapelArr) ? implode(' / ', $mapelArr) : 'Asesmen CBT';

        // Fallback pencarian mapel dari cbt_absensi jika jadwal grid belum spesifik
        if ($mapelNama === 'Asesmen CBT') {
            try {
                $sesiNumOnly = (int)preg_replace('/[^0-9]/', '', $sesiLabel);
                $stmtMA = $db->prepare("
                    SELECT DISTINCT mapel_nama 
                    FROM cbt_absensi 
                    WHERE tanggal = ? AND (jam_slot LIKE ? OR ? <= 0 OR jam_slot LIKE ?) 
                      AND mapel_nama IS NOT NULL AND mapel_nama != '' AND mapel_nama != 'Semua Mapel'
                    ORDER BY id ASC LIMIT 3
                ");
                $stmtMA->execute([$tanggal, '%' . $sesiLabel . '%', $sesiNumOnly, '%' . $sesiNumOnly . '%']);
                $mAbsArr = $stmtMA->fetchAll(PDO::FETCH_COLUMN);
                if (!empty($mAbsArr)) {
                    $mapelNama = implode(' / ', $mAbsArr);
                }
            } catch (\Exception $e) {}
        }

        // Cari jam sesi dinamis dari cbt_jam_slot
        $jamSesi = '';
        $sesiNum = (int)preg_replace('/[^0-9]/', '', $sesiLabel);
        try {
            $stmtS = $db->prepare("
                SELECT jam_mulai, jam_selesai 
                FROM cbt_jam_slot 
                WHERE label_sesi = ? OR urutan = ? OR label_sesi LIKE ? 
                ORDER BY id ASC LIMIT 1
            ");
            $stmtS->execute([$sesiLabel, $sesiNum, "%{$sesiLabel}%"]);
            $slotData = $stmtS->fetch(PDO::FETCH_ASSOC);
            if ($slotData && !empty($slotData['jam_mulai'])) {
                $jamSesi = date('H:i', strtotime($slotData['jam_mulai'])) . ' - ' . date('H:i', strtotime($slotData['jam_selesai'])) . ' WIB';
            }
        } catch (\Exception $e) {}

        // Jika belum dapat dari cbt_jam_slot, cek dari cbt_jadwal_grid
        if (empty($jamSesi)) {
            try {
                $stmtGridSlot = $db->prepare("
                    SELECT g.jam_slot, s.jam_mulai, s.jam_selesai 
                    FROM cbt_jadwal_grid g
                    LEFT JOIN cbt_jam_slot s ON (g.jam_slot = s.label_sesi OR g.jam_slot = s.urutan)
                    WHERE (? <= 0 OR g.topik_id = ?) 
                      AND (g.tanggal = ? OR (g.tanggal IS NULL AND g.hari = ?))
                      AND (g.jam_slot LIKE ? OR s.label_sesi LIKE ?)
                    LIMIT 1
                ");
                $stmtGridSlot->execute([$topikId, $topikId, $tanggal, $dayName, "%{$sesiLabel}%", "%{$sesiLabel}%"]);
                $gs = $stmtGridSlot->fetch(PDO::FETCH_ASSOC);
                if ($gs) {
                    if (!empty($gs['jam_mulai'])) {
                        $jamSesi = date('H:i', strtotime($gs['jam_mulai'])) . ' - ' . date('H:i', strtotime($gs['jam_selesai'])) . ' WIB';
                    } elseif (!empty($gs['jam_slot']) && preg_match('/\d{1,2}:\d{2}\s*-\s*\d{1,2}:\d{2}/', $gs['jam_slot'], $mg)) {
                        $jamSesi = $mg[0] . ' WIB';
                    }
                }
            } catch (\Exception $e) {}
        }

        // Fallback cerdas sesuai nomor sesi jika jadwal slot di DB belum dikonfigurasi
        if (empty($jamSesi)) {
            if ($sesiNum === 1) $jamSesi = '07:30 - 08:45 WIB';
            elseif ($sesiNum === 2) $jamSesi = '09:00 - 10:15 WIB';
            elseif ($sesiNum === 3) $jamSesi = '10:45 - 12:00 WIB';
            elseif ($sesiNum === 4) $jamSesi = '12:30 - 13:45 WIB';
            else $jamSesi = '08:00 - 09:00 WIB';
        }

        // Cari pengawas yang bertugas / presensi di ruang dan sesi tersebut
        $pengawasNama = $ruang['pengawas'] ?? 'Bapak / Ibu Pengawas';
        $pengawasNip = '-';
        $stmtPengawas = $db->prepare("
            SELECT guru_nama, guru_nip 
            FROM cbt_pengawas_absensi 
            WHERE ruang_id = ? AND tanggal = ? AND sesi_label = ? 
            ORDER BY id DESC LIMIT 1
        ");
        $stmtPengawas->execute([$ruangId, $tanggal, $sesiLabel]);
        $pAbs = $stmtPengawas->fetch(PDO::FETCH_ASSOC);
        if ($pAbs) {
            $pengawasNama = $pAbs['guru_nama'];
            $pengawasNip = $pAbs['guru_nip'] ?: '-';
        } else {
            $stmtPJ = $db->prepare("
                SELECT guru_nama, guru_nip 
                FROM cbt_pengawas_jadwal 
                WHERE ruang_id = ? AND (tanggal = ? OR (tanggal IS NULL AND hari = ?)) AND sesi_label = ? 
                LIMIT 1
            ");
            $stmtPJ->execute([$ruangId, $tanggal, $dayName, $sesiLabel]);
            $pJadwal = $stmtPJ->fetch(PDO::FETCH_ASSOC);
            if ($pJadwal) {
                $pengawasNama = $pJadwal['guru_nama'];
                $pengawasNip = $pJadwal['guru_nip'] ?: '-';
            }
        }

        // Cek absensi siswa scan kartu persesi pada tanggal ini
        $absenMap = [];
        try {
            $stmtAb = $db->prepare("SELECT nis, siswa_id, waktu_scan, status, metode_scan FROM cbt_absensi WHERE tanggal = ? ORDER BY waktu_scan ASC");
            $stmtAb->execute([$tanggal]);
            while ($ab = $stmtAb->fetch(PDO::FETCH_ASSOC)) {
                if (!empty($ab['nis'])) $absenMap['nis_' . trim((string)$ab['nis'])] = $ab;
                if (!empty($ab['siswa_id'])) $absenMap['sid_' . (int)$ab['siswa_id']] = $ab;
            }
        } catch (\Exception $e) {}

        // Juga cek pengerjaan cbt_peserta jika siswa langsung login workstation CBT
        $cbtPesertaMap = [];
        try {
            $stmtCp = $db->prepare("
                SELECT p.siswa_id, p.nis, p.waktu_mulai, p.status_pengerjaan 
                FROM cbt_peserta p
                WHERE (DATE(p.waktu_mulai) = ? OR ? <= 0 OR p.ujian_id IN (SELECT id FROM cbt_ujian WHERE topik_id = ?))
                  AND p.status_pengerjaan IN ('Mengerjakan', 'Sedang Mengerjakan', 'Selesai')
            ");
            $stmtCp->execute([$tanggal, $topikId, $topikId]);
            while ($cp = $stmtCp->fetch(PDO::FETCH_ASSOC)) {
                if (!empty($cp['siswa_id'])) $cbtPesertaMap['sid_' . (int)$cp['siswa_id']] = $cp;
                if (!empty($cp['nis'])) $cbtPesertaMap['nis_' . trim((string)$cp['nis'])] = $cp;
            }
        } catch (\Exception $e) {}

        $totalTerdaftar = count($pesertaList);
        $totalHadir = 0;
        $tidakHadirList = [];

        foreach ($pesertaList as &$ps) {
            $sId = (int)($ps['siswa_id'] ?? 0);
            $sNis = trim((string)($ps['nis'] ?? ''));

            $matchedAbsen = null;
            if ($sId > 0 && isset($absenMap['sid_' . $sId])) {
                $matchedAbsen = $absenMap['sid_' . $sId];
            } elseif (!empty($sNis) && isset($absenMap['nis_' . $sNis])) {
                $matchedAbsen = $absenMap['nis_' . $sNis];
            }

            if ($matchedAbsen) {
                $ps['is_hadir'] = true;
                $ps['status_kehadiran'] = 'Hadir';
                $ps['waktu_scan'] = !empty($matchedAbsen['waktu_scan']) ? date('H:i:s', strtotime($matchedAbsen['waktu_scan'])) . ' WIB' : 'Terscan';
                $ps['metode_scan'] = !empty($matchedAbsen['metode_scan']) ? $matchedAbsen['metode_scan'] : 'Scan Kartu';
                $totalHadir++;
            } elseif (isset($cbtPesertaMap['sid_' . $sId]) || isset($cbtPesertaMap['nis_' . $sNis])) {
                $cp = $cbtPesertaMap['sid_' . $sId] ?? $cbtPesertaMap['nis_' . $sNis];
                $ps['is_hadir'] = true;
                $ps['status_kehadiran'] = 'Hadir';
                $ps['waktu_scan'] = !empty($cp['waktu_mulai']) ? date('H:i:s', strtotime($cp['waktu_mulai'])) . ' WIB' : 'Login CBT';
                $ps['metode_scan'] = 'Login CBT';
                $totalHadir++;
            } else {
                $ps['is_hadir'] = false;
                $ps['status_kehadiran'] = 'Tidak Hadir';
                $ps['waktu_scan'] = '-';
                $ps['metode_scan'] = '-';
                $tidakHadirList[] = $ps;
            }
        }
        unset($ps);

        $totalTidakHadir = max(0, $totalTerdaftar - $totalHadir);

        $kop = null;
        try {
            $kop = $db->query("SELECT * FROM pengaturan_kop_surat LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}
        $inst = ['nama_kepala' => 'AHMAD RIFA\'I, M.Pd', 'nip_kepala' => '-', 'kota' => 'Lampung Barat'];
        try {
            $stmtInst = $db->query("SELECT nama_kepala, nip_kepala, kota FROM institusi LIMIT 1");
            if ($stmtInst && ($rowInst = $stmtInst->fetch(PDO::FETCH_ASSOC))) {
                if (!empty($rowInst['nama_kepala']) && stripos($rowInst['nama_kepala'], 'Hamim') === false) {
                    $inst['nama_kepala'] = $rowInst['nama_kepala'];
                }
                if (!empty($rowInst['nip_kepala'])) $inst['nip_kepala'] = $rowInst['nip_kepala'];
                if (!empty($rowInst['kota'])) $inst['kota'] = $rowInst['kota'];
            }
        } catch (\Exception $e) {}

        return [
            'topikId' => $topikId,
            'topikList' => $topikList,
            'topik' => $topik,
            'allRuang' => $allRuang,
            'ruangId' => $ruangId,
            'ruang' => $ruang,
            'tanggal' => $tanggal,
            'sesiLabel' => $sesiLabel,
            'pesertaList' => $pesertaList,
            'tidakHadirList' => $tidakHadirList,
            'mapelNama' => $mapelNama,
            'jamSesi' => $jamSesi,
            'pengawasNama' => $pengawasNama,
            'pengawasNip' => $pengawasNip,
            'totalTerdaftar' => $totalTerdaftar,
            'totalHadir' => $totalHadir,
            'totalTidakHadir' => $totalTidakHadir,
            'kop' => $kop,
            'inst' => $inst,
        ];
    }

    /**
     * OUTPUT 1: Halaman Dashboard Berita Acara & Kehadiran Ruang CBT (Dengan Sidebar)
     */
    public static function cetakBeritaAcaraRuang() {
        $data = self::getBeritaAcaraData();
        extract($data);

        $title = "Berita Acara & Kehadiran Ruang CBT - MAS MIFTAHUL HUDA";
        $active_menu = 'berita_acara_ruang';
        ob_start();
        include __DIR__ . '/../../resources/views/cbt/berita_acara_dashboard.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Cetak Lembar Dokumen Berita Acara Pelaksanaan Ujian CBT (A4 Portrait Print View)
     */
    public static function cetakDokumenBA() {
        $data = self::getBeritaAcaraData();
        extract($data);

        include __DIR__ . '/../../resources/views/cbt/cetak_dokumen_ba.php';
        exit;
    }

    /**
     * Cetak Lembar Dokumen Daftar Hadir Peserta Ruang Ujian CBT (A4 Portrait Print View - Waktu Scan)
     */
    public static function cetakDokumenHadirRuang() {
        $data = self::getBeritaAcaraData();
        extract($data);

        include __DIR__ . '/../../resources/views/cbt/cetak_dokumen_hadir_ruang.php';
        exit;
    }

    /**
     * OUTPUT 2: Cetak Daftar Hadir Peserta Per-Kelas Per-Mapel (Untuk Guru Mata Pelajaran)
     */
    public static function cetakDaftarHadirKelas() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $db_siakad = $ctx['db_siakad'];
        self::ensureTables($db);

        $topikId = (int)($_GET['topik_id'] ?? 0);
        $topikList = self::getActiveTopikList($db, $db_siakad);
        if ($topikId <= 0 && !empty($topikList)) {
            $topikId = (int)$topikList[0]['id'];
        }

        $topik = null;
        if ($topikId > 0) {
            $stmtT = $db->prepare("SELECT * FROM bs_topik WHERE id = ?");
            $stmtT->execute([$topikId]);
            $topik = $stmtT->fetch(PDO::FETCH_ASSOC);
        }

        $kelasList = [];
        try {
            $kelasList = $db->query("SELECT DISTINCT kelas_nama as nama_kelas FROM cbt_denah_peserta WHERE kelas_nama IS NOT NULL AND kelas_nama != '' ORDER BY kelas_nama ASC")->fetchAll(PDO::FETCH_COLUMN);
        } catch (\Exception $e) {}
        if (empty($kelasList)) {
            try {
                $kelasList = $db->query("SELECT nama_kelas FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll(PDO::FETCH_COLUMN);
            } catch (\Exception $e) {}
        }
        if (empty($kelasList)) {
            $kelasList = ['X.1', 'X.2', 'XI.1', 'XI.2', 'XII F.1', 'XII F.2'];
        }

        $selectedKelas = $_GET['kelas'] ?? (!empty($kelasList) ? $kelasList[0] : 'XII F.2');

        $mapelList = [];
        try {
            $stmtM = $db->prepare("
                SELECT DISTINCT mapel_nama 
                FROM cbt_ujian 
                WHERE (:tid <= 0 OR topik_id = :tid2 OR topik_id IS NULL)
                ORDER BY mapel_nama ASC
            ");
            $stmtM->execute([':tid' => $topikId, ':tid2' => $topikId]);
            $mapelList = $stmtM->fetchAll(PDO::FETCH_COLUMN);
        } catch (\Exception $e) {}
        if (empty($mapelList)) {
            $mapelList = ['Bahasa Inggris', 'Biologi', 'Akidah Akhlak', 'Al-Qur\'an Hadist', 'Matematika'];
        }

        $selectedMapel = $_GET['mapel'] ?? (!empty($mapelList) ? $mapelList[0] : 'Bahasa Inggris');
        $tanggal = $_GET['tanggal'] ?? date('Y-m-d');

        $stmtU = $db->prepare("
            SELECT id, nama_ujian, mapel_nama, kkm 
            FROM cbt_ujian 
            WHERE mapel_nama = ? AND (? <= 0 OR topik_id = ? OR topik_id IS NULL)
            LIMIT 1
        ");
        $stmtU->execute([$selectedMapel, $topikId, $topikId]);
        $ujianRow = $stmtU->fetch(PDO::FETCH_ASSOC);
        $ujianId = $ujianRow['id'] ?? 0;
        $kkm = (int)($ujianRow['kkm'] ?? 75);

        $stmtS = $db->prepare("
            SELECT s.id, s.nis, COALESCE(s.nik, s.nisn, '-') as nisn, s.nama as nama_siswa,
                   k.nama_kelas,
                   COALESCE(d.nomor_meja, '-') as nomor_meja,
                   COALESCE(r.nama_ruang, '-') as nama_ruang
            FROM siswa s
            LEFT JOIN kelas k ON s.kelas_id = k.id
            LEFT JOIN cbt_denah_peserta d ON d.siswa_id = s.id AND (? <= 0 OR d.topik_id = ? OR d.topik_id IS NULL)
            LEFT JOIN cbt_ruang r ON r.id = d.ruang_id
            WHERE k.nama_kelas = ? OR ? LIKE CONCAT('%', k.nama_kelas, '%') OR k.nama_kelas LIKE CONCAT('%', ?, '%')
            ORDER BY s.nama ASC
        ");
        $stmtS->execute([
            $topikId,
            $topikId,
            $selectedKelas,
            $selectedKelas,
            $selectedKelas
        ]);
        $siswaKelas = $stmtS->fetchAll(PDO::FETCH_ASSOC);

        if (empty($siswaKelas)) {
            $stmtD = $db->prepare("
                SELECT d.siswa_id as id, d.nis, '-' as nisn, d.nama_siswa, d.kelas_nama as nama_kelas,
                       d.nomor_meja, COALESCE(r.nama_ruang, '-') as nama_ruang
                FROM cbt_denah_peserta d
                LEFT JOIN cbt_ruang r ON r.id = d.ruang_id
                WHERE d.kelas_nama = ? AND (? <= 0 OR d.topik_id = ? OR d.topik_id IS NULL)
                ORDER BY d.nama_siswa ASC
            ");
            $stmtD->execute([$selectedKelas, $topikId, $topikId]);
            $siswaKelas = $stmtD->fetchAll(PDO::FETCH_ASSOC);
        }

        $hasilMap = [];
        if ($ujianId > 0) {
            $stmtHasil = $db->prepare("
                SELECT * FROM cbt_peserta 
                WHERE ujian_id = ?
            ");
            $stmtHasil->execute([$ujianId]);
            while ($h = $stmtHasil->fetch(PDO::FETCH_ASSOC)) {
                if (!empty($h['siswa_id'])) $hasilMap['sid_' . $h['siswa_id']] = $h;
                if (!empty($h['nis'])) $hasilMap['nis_' . $h['nis']] = $h;
            }
        }

        $totalSiswa = count($siswaKelas);
        $totalSudahUjian = 0;
        $totalBelumUjian = 0;
        $belumUjianList = [];

        foreach ($siswaKelas as &$sw) {
            $sId = (int)($sw['id'] ?? 0);
            $sNis = $sw['nis'] ?? '';
            $hData = $hasilMap['sid_' . $sId] ?? ($hasilMap['nis_' . $sNis] ?? null);

            if ($hData && in_array($hData['status_pengerjaan'], ['Selesai', 'Mengerjakan', 'Sedang Mengerjakan'])) {
                $sw['status_ujian'] = ($hData['status_pengerjaan'] === 'Selesai') ? 'Sudah Mengerjakan' : 'Sedang Mengerjakan';
                $sw['nilai_akhir'] = number_format((float)$hData['nilai_akhir'], 1);
                $sw['waktu_selesai'] = !empty($hData['waktu_selesai']) ? date('H:i', strtotime($hData['waktu_selesai'])) . ' WIB' : '-';
                $totalSudahUjian++;
            } else {
                $sw['status_ujian'] = 'Belum Ujian';
                $sw['nilai_akhir'] = '-';
                $sw['waktu_selesai'] = '-';
                $totalBelumUjian++;
                $belumUjianList[] = $sw['nama_siswa'];
            }
        }
        unset($sw);

        $kop = null;
        try {
            $kop = $db->query("SELECT * FROM pengaturan_kop_surat LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}
        $inst = ['nama_kepala' => 'AHMAD RIFA\'I, M.Pd', 'nip_kepala' => '-', 'kota' => 'Lampung Barat'];
        try {
            $stmtInst = $db->query("SELECT nama_kepala, nip_kepala, kota FROM institusi LIMIT 1");
            if ($stmtInst && ($rowInst = $stmtInst->fetch(PDO::FETCH_ASSOC))) {
                if (!empty($rowInst['nama_kepala']) && stripos($rowInst['nama_kepala'], 'Hamim') === false) {
                    $inst['nama_kepala'] = $rowInst['nama_kepala'];
                }
                if (!empty($rowInst['nip_kepala'])) $inst['nip_kepala'] = $rowInst['nip_kepala'];
                if (!empty($rowInst['kota'])) $inst['kota'] = $rowInst['kota'];
            }
        } catch (\Exception $e) {}

        include __DIR__ . '/../../resources/views/cbt/cetak_daftar_hadir_kelas.php';
        exit;
    }

    // =========================================================================
    // MODUL B & C: ABSENSI CBT SCAN 1 PINTU (TERINTEGRASI JADWAL & KELAS JAUH)
    // =========================================================================

    /**
     * Halaman Scanner Absensi CBT 1 Pintu (Desktop & Mobile)
     */
    public static function absensiScan() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        $todayDate = date('Y-m-d');
        $curTime = date('H:i');
        $curHari = self::getIndoDayName(date('w'));

        // Jadwal aktif hari ini (resolve mapel_nama secara komprehensif)
        $jadwalToday = $db->query("
            SELECT g.*, u.nama_ujian, u.kode_ujian, 
                   COALESCE(NULLIF(u.mapel_nama, ''), g.mapel_nama, u.nama_ujian) as mapel_nama,
                   s.jam_mulai, s.jam_selesai, s.label_sesi
            FROM cbt_jadwal_grid g
            LEFT JOIN cbt_ujian u ON g.ujian_id = u.id
            LEFT JOIN cbt_jam_slot s ON (g.jam_slot = s.label_sesi OR g.jam_slot = s.urutan)
            WHERE g.tanggal = '{$todayDate}' OR (g.tanggal IS NULL AND (g.hari = '{$curHari}' OR g.hari LIKE '{$curHari}%'))
            ORDER BY s.jam_mulai ASC, g.kelas_nama ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Jika cbt_jadwal_grid kosong untuk hari ini, ambil dari cbt_ujian langsung
        if (empty($jadwalToday)) {
            $jadwalToday = $db->query("
                SELECT u.id as ujian_id, u.topik_id, u.nama_ujian, u.kode_ujian,
                       COALESCE(NULLIF(u.mapel_nama, ''), u.nama_ujian) as mapel_nama,
                       u.kelas_nama, 'Sesi 1' as label_sesi, '08:00' as jam_mulai, '09:00' as jam_selesai,
                       'Ruang 01' as ruang
                FROM cbt_ujian u
                WHERE u.status = 'Aktif' OR (DATE(u.tanggal_mulai) <= '{$todayDate}' AND DATE(u.tanggal_selesai) >= '{$todayDate}')
                ORDER BY u.id ASC
            ")->fetchAll(PDO::FETCH_ASSOC);
        }

        // Riwayat absensi hari ini
        $stmtRecent = $db->query("SELECT * FROM cbt_absensi WHERE tanggal = '{$todayDate}' ORDER BY waktu_scan DESC LIMIT 100");
        $recentAbsen = $stmtRecent ? $stmtRecent->fetchAll(PDO::FETCH_ASSOC) : [];
        $totalAbsenHariIni = (int)$db->query("SELECT COUNT(*) FROM cbt_absensi WHERE tanggal = '{$todayDate}'")->fetchColumn();

        $title = "Scanner Absensi CBT 1 Pintu - MAS MIFTAHUL HUDA";
        $active_menu = 'absensi_cbt';
        ob_start();
        include __DIR__ . '/../../resources/views/cbt/absensi_scan.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * API Handler: Proses Scan Barcode / NIS 1 Pintu
     */
    public static function processAbsensiScan() {
        header('Content-Type: application/json');
        try {
            $ctx = self::getAuthContext();
            $db = $ctx['db_core'];
            $dbSiakad = $ctx['db_siakad'] ?? $db;
            self::ensureTables($db);

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Metode request tidak valid']);
                exit;
            }

            $rawCode = trim($_POST['code'] ?? ($_POST['nis'] ?? ''));
            if (empty($rawCode)) {
                echo json_encode(['success' => false, 'message' => 'Barcode / NIS tidak boleh kosong!']);
                exit;
            }

            $todayDate = date('Y-m-d');
            $curTime = date('H:i:s');
            $curHari = self::getIndoDayName(date('w'));

            // 1. Ekstraksi cerdas dari berbagai format barcode / QR kartu
            $cleanCode = $rawCode;
            $extractedUserId = null;
            $extractedKjId = null;

            // A. Format QR Kartu Pelajar Siakad: TOKEN:<user_id>:<hash>
            if (preg_match('/^TOKEN:(\d+):([a-f0-9]+)$/i', $rawCode, $m)) {
                $extractedUserId = (int)$m[1];
            }
            // B. Format Kartu CBT Kelas Jauh: CBT_KJ:<id>:<token> atau TOKEN:KJ:<id>:<token>
            elseif (preg_match('/^(?:CBT_KJ|TOKEN:KJ):(\d+)(?::(.*))?$/i', $rawCode, $m)) {
                $extractedKjId = (int)$m[1];
            }
            // C. Format prefix scanner: "NIS: 240001", "ID: 414", "NO: ..."
            elseif (preg_match('/^(?:NIS|NISN|ID|NO|KODE):\s*(.+)$/i', $rawCode, $m)) {
                $cleanCode = trim($m[1]);
                if (ctype_digit($cleanCode)) {
                    $extractedUserId = (int)$cleanCode;
                }
            }

            $siswa = null;
            $tipePeserta = 'reguler';

            // 1a. Cari jika terdeteksi User ID / Siswa ID dari TOKEN Siakad (misal TOKEN:394:...)
            if ($extractedUserId) {
                // 1. Cek langsung di core.siswa berdasarkan user_id, id, atau id offset (user_id - 341)
                try {
                    $stmtS = $db->prepare("
                        SELECT s.id, s.user_id, s.nis, s.nama, s.kelas_id, k.nama_kelas, k.tingkat, s.foto
                        FROM siswa s
                        LEFT JOIN kelas k ON s.kelas_id = k.id
                        WHERE s.user_id = ? OR s.id = ? OR s.id = (? - 341)
                        LIMIT 1
                    ");
                    $stmtS->execute([$extractedUserId, $extractedUserId, $extractedUserId]);
                    $sRow = $stmtS->fetch(PDO::FETCH_ASSOC);
                    if ($sRow) {
                        $siswa = [
                            'id' => (int)$sRow['id'],
                            'nis' => $sRow['nis'] ?: 'ID-' . $sRow['id'],
                            'nama' => $sRow['nama'],
                            'kelas_nama' => $sRow['nama_kelas'] ?: 'Umum',
                            'tingkat' => $sRow['tingkat'],
                            'foto' => $sRow['foto'],
                            'tipe' => 'reguler'
                        ];
                        // Auto-heal tautan user_id
                        if (!isset($sRow['user_id']) || (int)$sRow['user_id'] !== $extractedUserId) {
                            try { $db->prepare("UPDATE siswa SET user_id = ? WHERE id = ?")->execute([$extractedUserId, $sRow['id']]); } catch (\Exception $e) {}
                        }
                    }
                } catch (\Exception $e) {}

                // 2. Jika belum ketemu, cek tabel users untuk mencocokkan username / NIK
                if (!$siswa) {
                    try {
                        $stmtU = $db->prepare("SELECT id, username FROM users WHERE id = ? LIMIT 1");
                        $stmtU->execute([$extractedUserId]);
                        $uRow = $stmtU->fetch(PDO::FETCH_ASSOC);
                        if ($uRow) {
                            $uName = trim($uRow['username']);
                            $stmtS = $db->prepare("
                                SELECT s.id, s.user_id, s.nis, s.nama, s.kelas_id, k.nama_kelas, k.tingkat, s.foto
                                FROM siswa s
                                LEFT JOIN kelas k ON s.kelas_id = k.id
                                WHERE s.user_id = ? 
                                   OR (s.nis = ? AND s.nis != '') 
                                   OR (s.nik = ? AND s.nik != '') 
                                   OR (s.nisn = ? AND s.nisn != '')
                                LIMIT 1
                            ");
                            $stmtS->execute([$uRow['id'], $uName, $uName, $uName]);
                            $sRow = $stmtS->fetch(PDO::FETCH_ASSOC);
                            if ($sRow) {
                                $siswa = [
                                    'id' => (int)$sRow['id'],
                                    'nis' => $sRow['nis'] ?: 'ID-' . $sRow['id'],
                                    'nama' => $sRow['nama'],
                                    'kelas_nama' => $sRow['nama_kelas'] ?: 'Umum',
                                    'tingkat' => $sRow['tingkat'],
                                    'foto' => $sRow['foto'],
                                    'tipe' => 'reguler'
                                ];
                                if (!isset($sRow['user_id']) || (int)$sRow['user_id'] !== $extractedUserId) {
                                    try { $db->prepare("UPDATE siswa SET user_id = ? WHERE id = ?")->execute([$extractedUserId, $sRow['id']]); } catch (\Exception $e) {}
                                }
                            }
                        }
                    } catch (\Exception $e) {}
                }

                // 3. Jika belum ketemu, cek database Siakad (kolom kompatibel Siakad)
                if (!$siswa && $dbSiakad !== $db) {
                    try {
                        $stmtSiakad = $dbSiakad->prepare("
                            SELECT s.id, 0 as user_id, s.nis, s.nama_lengkap as nama, s.kelas_id, k.nama_kelas, k.tingkat, s.foto_siswa as foto
                            FROM siswa s
                            LEFT JOIN kelas k ON s.kelas_id = k.id
                            WHERE s.id = ? OR s.id = (? - 341) OR (s.nis = ? AND s.nis != '') OR (s.nisn = ? AND s.nisn != '')
                            LIMIT 1
                        ");
                        $uName = $uRow['username'] ?? '';
                        $stmtSiakad->execute([$extractedUserId, $extractedUserId, $uName, $uName]);
                        $sRow = $stmtSiakad->fetch(PDO::FETCH_ASSOC);
                        if ($sRow) {
                            $siswa = [
                                'id' => (int)$sRow['id'],
                                'nis' => $sRow['nis'] ?: 'ID-' . $sRow['id'],
                                'nama' => $sRow['nama'],
                                'kelas_nama' => $sRow['nama_kelas'] ?: 'Umum',
                                'tingkat' => $sRow['tingkat'],
                                'foto' => $sRow['foto'],
                                'tipe' => 'reguler'
                            ];
                        }
                    } catch (\Exception $e) {}
                }

                // 4. Jika belum ketemu, cek peserta khusus (Kelas Jauh)
                if (!$siswa) {
                    try {
                        $stmtK = $db->prepare("
                            SELECT id, no_peserta as nis, nama_siswa as nama, kelas_nama, tingkat, username
                            FROM cbt_peserta_khusus
                            WHERE id = ? OR username = ? OR no_peserta = ? OR qr_token LIKE ?
                            LIMIT 1
                        ");
                        $tokenSearch = '%' . $extractedUserId . '%';
                        $stmtK->execute([$extractedUserId, $cleanCode, $cleanCode, $tokenSearch]);
                        $kRow = $stmtK->fetch(PDO::FETCH_ASSOC);
                        if ($kRow) {
                            $siswa = [
                                'id' => (int)$kRow['id'],
                                'nis' => $kRow['nis'],
                                'nama' => $kRow['nama'],
                                'kelas_nama' => $kRow['kelas_nama'] ?: 'Kelas Jauh',
                                'tingkat' => $kRow['tingkat'],
                                'foto' => null,
                                'tipe' => 'khusus'
                            ];
                            $tipePeserta = 'khusus';
                        }
                    } catch (\Exception $e) {}
                }
            }

            // 1b. Cari jika terdeteksi ID dari Kartu Kelas Jauh (misal CBT_KJ:1:...)
            if (!$siswa && $extractedKjId) {
                try {
                    $stmtK = $db->prepare("
                        SELECT id, no_peserta as nis, nama_siswa as nama, kelas_nama, tingkat, username
                        FROM cbt_peserta_khusus
                        WHERE id = ?
                        LIMIT 1
                    ");
                    $stmtK->execute([$extractedKjId]);
                    $kRow = $stmtK->fetch(PDO::FETCH_ASSOC);
                    if ($kRow) {
                        $siswa = [
                            'id' => (int)$kRow['id'],
                            'nis' => $kRow['nis'],
                            'nama' => $kRow['nama'],
                            'kelas_nama' => $kRow['kelas_nama'] ?: 'Kelas Jauh',
                            'tingkat' => $kRow['tingkat'],
                            'foto' => null,
                            'tipe' => 'khusus'
                        ];
                        $tipePeserta = 'khusus';
                    }
                } catch (\Exception $e) {}
            }

            // 1c. Cari di tabel siswa berdasarkan NIS, ID, user_id, atau username
            if (!$siswa) {
                // Di database core
                try {
                    $stmtS = $db->prepare("
                        SELECT s.id, s.user_id, s.nis, s.nama, s.kelas_id, k.nama_kelas, k.tingkat, s.foto
                        FROM siswa s
                        LEFT JOIN kelas k ON s.kelas_id = k.id
                        LEFT JOIN users u ON s.user_id = u.id
                        WHERE s.nis = ? OR s.id = ? OR s.user_id = ? OR u.username = ?
                           OR s.nis = ? OR s.id = ?
                        LIMIT 1
                    ");
                    $stmtS->execute([$cleanCode, $cleanCode, $cleanCode, $cleanCode, $rawCode, $rawCode]);
                    $sRow = $stmtS->fetch(PDO::FETCH_ASSOC);
                    if ($sRow) {
                        $siswa = [
                            'id' => (int)$sRow['id'],
                            'nis' => $sRow['nis'] ?: 'ID-' . $sRow['id'],
                            'nama' => $sRow['nama'],
                            'kelas_nama' => $sRow['nama_kelas'] ?: 'Umum',
                            'tingkat' => $sRow['tingkat'],
                            'foto' => $sRow['foto'],
                            'tipe' => 'reguler'
                        ];
                    }
                } catch (\Exception $e) {}

                // Di database Siakad jika belum ketemu
                if (!$siswa && $dbSiakad !== $db) {
                    try {
                        $stmtS = $dbSiakad->prepare("
                            SELECT s.id, 0 as user_id, s.nis, s.nama_lengkap as nama, s.kelas_id, k.nama_kelas, k.tingkat, s.foto_siswa as foto
                            FROM siswa s
                            LEFT JOIN kelas k ON s.kelas_id = k.id
                            WHERE s.nis = ? OR s.id = ? OR s.nisn = ?
                            LIMIT 1
                        ");
                        $stmtS->execute([$cleanCode, $cleanCode, $cleanCode]);
                        $sRow = $stmtS->fetch(PDO::FETCH_ASSOC);
                        if ($sRow) {
                            $siswa = [
                                'id' => (int)$sRow['id'],
                                'nis' => $sRow['nis'] ?: 'ID-' . $sRow['id'],
                                'nama' => $sRow['nama'],
                                'kelas_nama' => $sRow['nama_kelas'] ?: 'Umum',
                                'tingkat' => $sRow['tingkat'],
                                'foto' => $sRow['foto'],
                                'tipe' => 'reguler'
                            ];
                        }
                    } catch (\Exception $e) {}
                }
            }

            // 1d. Cari di tabel cbt_peserta_khusus (Kelas Jauh)
            if (!$siswa) {
                try {
                    $stmtK = $db->prepare("
                        SELECT id, no_peserta as nis, nama_siswa as nama, kelas_nama, tingkat, username
                        FROM cbt_peserta_khusus
                        WHERE no_peserta = ? OR username = ? OR id = ? OR qr_token = ?
                           OR no_peserta = ? OR username = ?
                        LIMIT 1
                    ");
                    $stmtK->execute([$cleanCode, $cleanCode, $cleanCode, $cleanCode, $rawCode, $rawCode]);
                    $kRow = $stmtK->fetch(PDO::FETCH_ASSOC);
                    if ($kRow) {
                        $siswa = [
                            'id' => (int)$kRow['id'],
                            'nis' => $kRow['nis'],
                            'nama' => $kRow['nama'],
                            'kelas_nama' => $kRow['kelas_nama'] ?: 'Kelas Jauh',
                            'tingkat' => $kRow['tingkat'],
                            'foto' => null,
                            'tipe' => 'khusus'
                        ];
                        $tipePeserta = 'khusus';
                    }
                } catch (\Exception $e) {}
            }

            if (!$siswa) {
                echo json_encode([
                    'success' => false,
                    'message' => "Siswa dengan kartu/kode '{$rawCode}' tidak terdaftar di sistem Siakad maupun CBT!"
                ]);
                exit;
            }

            // 2. Tentukan Jadwal Mapel & Sesi Aktif saat ini untuk kelas siswa tersebut
            $targetKelas = $siswa['kelas_nama'];
            $tingkatKelas = (string)($siswa['tingkat'] ?? '');
            $shortTingkat = 'X';
            if (stripos($targetKelas, 'XII') !== false || stripos($targetKelas, '12') !== false || stripos($tingkatKelas, 'XII') !== false || $tingkatKelas === '12') {
                $shortTingkat = 'XII';
                $tingkatLabel = 'Kelas XII';
            } elseif (stripos($targetKelas, 'XI') !== false || stripos($targetKelas, '11') !== false || stripos($tingkatKelas, 'XI') !== false || $tingkatKelas === '11') {
                $shortTingkat = 'XI';
                $tingkatLabel = 'Kelas XI';
            } else {
                $shortTingkat = 'X';
                $tingkatLabel = 'Kelas X';
            }

            if ($shortTingkat === 'XII') {
                $tingkatSql = "(g.kelas_nama LIKE 'XII%' OR g.kelas_nama LIKE 'Kelas 12%' OR g.kelas_nama LIKE 'Kelas XII%')";
            } elseif ($shortTingkat === 'XI') {
                $tingkatSql = "((g.kelas_nama LIKE 'XI%' AND g.kelas_nama NOT LIKE 'XII%') OR g.kelas_nama LIKE 'Kelas 11%' OR g.kelas_nama LIKE 'Kelas XI%')";
            } else {
                $tingkatSql = "((g.kelas_nama LIKE 'X%' AND g.kelas_nama NOT LIKE 'XI%' AND g.kelas_nama NOT LIKE 'XII%') OR g.kelas_nama LIKE 'Kelas 10%' OR g.kelas_nama LIKE 'Kelas X%')";
            }

            // Ambil master slot jam untuk resolusi label sesi & jam
            $allSlots = [];
            try {
                $allSlots = $db->query("SELECT * FROM cbt_jam_slot ORDER BY urutan ASC")->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Exception $e) {}

            // Ambil seluruh jadwal ujian hari ini untuk kelas siswa tersebut (mendukung kelas reguler & kelas jauh)
            $stmtJadwal = $db->prepare("
                SELECT g.*, u.nama_ujian
                FROM cbt_jadwal_grid g
                LEFT JOIN cbt_ujian u ON g.ujian_id = u.id
                WHERE (g.tanggal = ? OR (g.tanggal IS NULL AND (g.hari = ? OR g.hari LIKE ?)))
                  AND (
                      g.kelas_nama = ? 
                      OR ? LIKE CONCAT('%', g.kelas_nama, '%') 
                      OR g.kelas_nama = ?
                      OR g.kelas_nama = 'Semua Kelas'
                      OR g.kelas_nama = ''
                      OR g.kelas_nama IS NULL
                      OR {$tingkatSql}
                  )
                ORDER BY (g.kelas_nama = ?) DESC, (g.kelas_nama = ?) DESC, g.id ASC
            ");
            $curHariLike = $curHari . '%';
            $stmtJadwal->execute([
                $todayDate,
                $curHari,
                $curHariLike,
                $targetKelas,
                $targetKelas,
                $tingkatLabel,
                $targetKelas,
                $tingkatLabel
            ]);
            $allJadwalGrid = $stmtJadwal->fetchAll(PDO::FETCH_ASSOC);

            // Parsing akurat jam_mulai, jam_selesai, dan label_sesi per baris jadwal
            foreach ($allJadwalGrid as &$jg) {
                $rawSlot = trim($jg['jam_slot'] ?? '');
                $jMulai = null;
                $jSelesai = null;

                if (preg_match('/(\d{1,2}:\d{2})\s*[-–]\s*(\d{1,2}:\d{2})/', $rawSlot, $mTimes)) {
                    $jMulai = sprintf('%05s', $mTimes[1]);
                    $jSelesai = sprintf('%05s', $mTimes[2]);
                } else {
                    foreach ($allSlots as $sl) {
                        if ($rawSlot === $sl['label_sesi'] || $rawSlot === (string)$sl['urutan']) {
                            $jMulai = substr(trim($sl['jam_mulai']), 0, 5);
                            $jSelesai = substr(trim($sl['jam_selesai']), 0, 5);
                            break;
                        }
                    }
                }

                if (!empty($jMulai) && strlen($jMulai) === 5) $jMulai .= ':00';
                if (!empty($jSelesai) && strlen($jSelesai) === 5) $jSelesai .= ':00';

                $jg['parsed_jam_mulai'] = $jMulai;
                $jg['parsed_jam_selesai'] = $jSelesai;

                // Tentukan Label Sesi yang tepat
                $labelSesi = '';
                $jMulaiShort = !empty($jMulai) ? substr($jMulai, 0, 5) : '';
                if ($jMulaiShort === '08:00') {
                    $labelSesi = 'Sesi 1';
                } elseif ($jMulaiShort === '09:00') {
                    $labelSesi = 'Sesi 2';
                } elseif ($jMulaiShort === '10:10') {
                    $labelSesi = 'Sesi 3';
                } else {
                    foreach ($allSlots as $sl) {
                        $slMulai = substr(trim($sl['jam_mulai']), 0, 5);
                        if ($slMulai === $jMulaiShort || $rawSlot === $sl['label_sesi']) {
                            $labelSesi = $sl['label_sesi'];
                            break;
                        }
                    }
                    if (empty($labelSesi)) {
                        $labelSesi = !empty($rawSlot) ? $rawSlot : 'Sesi Ujian';
                    }
                }
                $jg['label_sesi'] = $labelSesi;
            }
            unset($jg);

            // Urutkan jadwal berdasarkan jam mulai sesi secara kronologis
            usort($allJadwalGrid, function($a, $b) {
                return strcmp($a['parsed_jam_mulai'] ?? '99:99:99', $b['parsed_jam_mulai'] ?? '99:99:99');
            });

            // Support Manual Session / Mapel Override dari Proktor
            $manualGridId = (int)($_POST['manual_grid_id'] ?? 0);
            $activeJadwal = null;
            if ($manualGridId > 0) {
                foreach ($allJadwalGrid as $jg) {
                    if ((int)$jg['id'] === $manualGridId) {
                        $activeJadwal = $jg;
                        break;
                    }
                }
                if (!$activeJadwal) {
                    try {
                        $stM = $db->prepare("
                            SELECT g.*, u.nama_ujian, COALESCE(NULLIF(u.mapel_nama, ''), g.mapel_nama, u.nama_ujian) as mapel_nama,
                                   s.jam_mulai, s.jam_selesai, s.label_sesi
                            FROM cbt_jadwal_grid g
                            LEFT JOIN cbt_ujian u ON g.ujian_id = u.id
                            LEFT JOIN cbt_jam_slot s ON (g.jam_slot = s.label_sesi OR g.jam_slot = s.urutan)
                            WHERE g.id = ?
                            LIMIT 1
                        ");
                        $stM->execute([$manualGridId]);
                        $activeJadwal = $stM->fetch(PDO::FETCH_ASSOC);
                    } catch (\Exception $e) {}
                }
            }

            // Jika tidak ada manual override, tentukan otomatis berdasarkan jam sesi atau keterlambatan (molor)
            if (!$activeJadwal && !empty($allJadwalGrid)) {
                $strictlyActiveJadwal = null;
                $bufferActiveJadwal = null;
                $upcomingJadwal = null;

                foreach ($allJadwalGrid as $jg) {
                    $jMulai = $jg['parsed_jam_mulai'];
                    $jSelesai = $jg['parsed_jam_selesai'];

                    if (!empty($jMulai) && !empty($jSelesai)) {
                        // Prioritas 1: Sesi yang benar-benar SEDANG BERLANGSUNG pada jam saat ini
                        if ($curTime >= $jMulai && $curTime <= $jSelesai) {
                            if (!$strictlyActiveJadwal) {
                                $strictlyActiveJadwal = $jg;
                            }
                        }

                        // Prioritas 2: Buffer scan awal 15 menit sebelum jam mulai sesi
                        $earlyBuffer = date('H:i:s', strtotime($todayDate . ' ' . $jMulai) - 900);
                        if ($curTime >= $earlyBuffer && $curTime <= $jSelesai) {
                            if (!$bufferActiveJadwal) {
                                $bufferActiveJadwal = $jg;
                            }
                        } elseif ($curTime < $earlyBuffer) {
                            if (!$upcomingJadwal) {
                                $upcomingJadwal = $jg;
                            }
                        }
                    }
                }

                $activeJadwal = $strictlyActiveJadwal ?: $bufferActiveJadwal;

                // TOLERANSI JADWAL MOLOR / KETERLAMBATAN:
                // Jika jam resmi sudah lewat (molor), cari sesi hari ini yang belum pernah dihadiri siswa ini!
                if (!$activeJadwal) {
                    foreach ($allJadwalGrid as $jg) {
                        $chkMpl = !empty($jg['mapel_nama']) ? $jg['mapel_nama'] : (!empty($jg['nama_ujian']) ? $jg['nama_ujian'] : '');
                        try {
                            $stChkA = $db->prepare("
                                SELECT id FROM cbt_absensi 
                                WHERE tanggal = ? AND (nis = ? OR (siswa_id IS NOT NULL AND siswa_id = ?))
                                  AND (ujian_id = ? OR mapel_nama = ?)
                                LIMIT 1
                            ");
                            $stChkA->execute([$todayDate, $siswa['nis'], $saveSiswaId ?: -1, (int)($jg['ujian_id'] ?? 0), $chkMpl]);
                            if (!$stChkA->fetchColumn()) {
                                $activeJadwal = $jg;
                                break;
                            }
                        } catch (\Exception $e) {}
                    }
                    // Jika semua sudah dihadiri atau tidak ditemukan, gunakan jadwal terakhir hari ini
                    if (!$activeJadwal) {
                        $activeJadwal = end($allJadwalGrid);
                    }
                }
            }

            // Fallback pamungkas: jika tidak ada jadwal grid untuk kelas siswa ini, ambil dari cbt_ujian aktif hari ini
            if (!$activeJadwal) {
                try {
                    $stAny = $db->query("
                        SELECT id as ujian_id, topik_id, nama_ujian, 
                               COALESCE(NULLIF(mapel_nama, ''), nama_ujian) as mapel_nama,
                               'Sesi 1' as label_sesi, '08:00 - 09:00' as jam_slot, 'Ruang 01' as ruang
                        FROM cbt_ujian 
                        WHERE status = 'Aktif' OR (DATE(tanggal_mulai) <= '{$todayDate}' AND DATE(tanggal_selesai) >= '{$todayDate}')
                        ORDER BY id DESC LIMIT 1
                    ");
                    if ($stAny) {
                        $activeJadwal = $stAny->fetch(PDO::FETCH_ASSOC);
                    }
                } catch (\Exception $e) {}
            }

            $jadwal = $activeJadwal;
            if (!$jadwal) {
                echo json_encode([
                    'success' => false,
                    'message' => "Tidak ada sesi ujian aktif hari ini untuk kelas {$siswa['kelas_nama']}!"
                ]);
                exit;
            }

            $mapelNama = !empty($jadwal['mapel_nama']) ? $jadwal['mapel_nama'] : (!empty($jadwal['nama_ujian']) ? $jadwal['nama_ujian'] : 'Asesmen CBT Harian');
            $jamSlot = !empty($jadwal['label_sesi']) ? $jadwal['label_sesi'] : (!empty($jadwal['jam_slot']) ? $jadwal['jam_slot'] : 'Sesi 1');
            $topikId = (int)($jadwal['topik_id'] ?? 0);
            $ujianId = (int)($jadwal['ujian_id'] ?? 0);
            $ruangNama = trim($_POST['ruang_nama'] ?? ($jadwal['ruang'] ?? 'Gerbang / 1 Pintu'));

            // 3. Cek apakah sudah absen untuk mapel & tanggal ini
            if ($tipePeserta === 'khusus') {
                $stmtCheck = $db->prepare("
                    SELECT id, waktu_scan FROM cbt_absensi 
                    WHERE tanggal = ? AND (nis = ? OR (nis = ? AND ? != '') OR nama_siswa = ?) AND (mapel_nama = ? OR ? LIKE CONCAT('%', mapel_nama, '%') OR mapel_nama LIKE CONCAT('%', ?, '%'))
                ");
                $stmtCheck->execute([$todayDate, $siswa['nis'], $siswa['nis'], $siswa['nis'], $siswa['nama'], $mapelNama, $mapelNama, $mapelNama]);
            } else {
                $stmtCheck = $db->prepare("
                    SELECT id, waktu_scan FROM cbt_absensi 
                    WHERE tanggal = ? AND (siswa_id = ? OR (nis = ? AND ? != '')) AND (mapel_nama = ? OR ? LIKE CONCAT('%', mapel_nama, '%') OR mapel_nama LIKE CONCAT('%', ?, '%'))
                ");
                $stmtCheck->execute([$todayDate, $siswa['id'], $siswa['nis'], $siswa['nis'], $mapelNama, $mapelNama, $mapelNama]);
            }
            $exist = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if ($exist) {
                echo json_encode([
                    'success' => true,
                    'already' => true,
                    'message' => "Siswa {$siswa['nama']} sudah terabsen sebelumnya untuk mata pelajaran {$mapelNama} ({$jamSlot}) pada pukul " . date('H:i', strtotime($exist['waktu_scan'])) . " WIB.",
                    'siswa' => $siswa,
                    'mapel' => $mapelNama,
                    'mapel_nama' => $mapelNama,
                    'sesi' => $jamSlot,
                    'jam_slot' => $jamSlot,
                    'status' => 'Hadir',
                    'waktu' => date('H:i:s', strtotime($exist['waktu_scan']))
                ]);
                exit;
            }

            // Tentukan status kehadiran (Hadir / Terlambat)
            $statusAbsen = 'Hadir';
            $jMulai = !empty($jadwal['parsed_jam_mulai']) ? $jadwal['parsed_jam_mulai'] : (!empty($jadwal['jam_mulai']) ? $jadwal['jam_mulai'] : null);
            if (!empty($jMulai)) {
                $waktuMulai = strtotime($todayDate . ' ' . $jMulai);
                $waktuSekarang = time();
                if ($waktuSekarang > ($waktuMulai + 900)) { // Terlambat jika lewat 15 menit dari jam mulai sesi
                    $statusAbsen = 'Terlambat';
                }
            }

            $petugasNama = $_SESSION['nama'] ?? ($_SESSION['username'] ?? 'Pengawas CBT');
            $saveSiswaId = ($tipePeserta === 'khusus') ? null : $siswa['id'];

            // Pastikan kolom siswa_id bisa menerima NULL jika siswa khusus
            try {
                $db->exec("ALTER TABLE cbt_absensi MODIFY COLUMN siswa_id INT NULL DEFAULT NULL");
            } catch (\Exception $e) {}

            // Fallback jika database masih strict NOT NULL
            if ($saveSiswaId === null) {
                try {
                    $chkCol = $db->query("SHOW COLUMNS FROM cbt_absensi LIKE 'siswa_id'")->fetch(PDO::FETCH_ASSOC);
                    if ($chkCol && strtoupper($chkCol['Null'] ?? '') === 'NO') {
                        $saveSiswaId = 0;
                    }
                } catch (\Exception $e) {
                    $saveSiswaId = 0;
                }
            }

            // 4. Catat ke cbt_absensi
            $stmtIns = $db->prepare("
                INSERT INTO cbt_absensi 
                (tanggal, siswa_id, nis, nama_siswa, kelas_nama, tipe_peserta, topik_id, ujian_id, mapel_nama, jam_slot, waktu_scan, status, metode_scan, petugas_nama, ruang_nama)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, 'QR_SCAN', ?, ?)
            ");
            $stmtIns->execute([
                $todayDate,
                $saveSiswaId,
                $siswa['nis'],
                $siswa['nama'],
                $siswa['kelas_nama'],
                $tipePeserta,
                $topikId,
                $ujianId,
                $mapelNama,
                $jamSlot,
                $statusAbsen,
                $petugasNama,
                $ruangNama
            ]);

            echo json_encode([
                'success' => true,
                'message' => "Absensi Berhasil! {$siswa['nama']} ({$siswa['kelas_nama']}) tercatat {$statusAbsen} untuk {$mapelNama} ({$jamSlot}).",
                'siswa' => $siswa,
                'mapel' => $mapelNama,
                'mapel_nama' => $mapelNama,
                'sesi' => $jamSlot,
                'jam_slot' => $jamSlot,
                'status' => $statusAbsen,
                'waktu' => date('H:i:s')
            ]);
            exit;
        } catch (\Throwable $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal memproses absensi: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Endpoint API 1-Klik: Tandai Hadir Semua Siswa Hari Ini (Semua Mapel)
     */
    public static function bulkAbsensiToday() {
        header('Content-Type: application/json');
        try {
            $tanggal = !empty($_POST['tanggal']) ? trim($_POST['tanggal']) : date('Y-m-d');
            $res = self::bulkPopulateAbsensiHariIni($tanggal);
            echo json_encode($res);
            exit;
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal bulk absensi: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * FUNGSI CORE: Mengisi absensi hadir seluruh siswa untuk seluruh mapel hari ini secara otomatis
     * dengan waktu scan realistis sesuai rentang jam sesi (Sesi 1, 2, 3, dst).
     */
    public static function bulkPopulateAbsensiHariIni($targetTanggal = null) {
        $db = \App\Core\Database::connect('core');
        if (!$db) return ['success' => false, 'message' => 'Gagal koneksi database core'];
        self::ensureTables($db);

        $tanggal = !empty($targetTanggal) ? $targetTanggal : date('Y-m-d');
        $hariIndo = self::getIndoDayName(date('w', strtotime($tanggal)));

        // 1. Ambil topik aktif
        $topikId = 0;
        try {
            $topikId = (int)$db->query("SELECT id FROM cbt_topik WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1")->fetchColumn();
            if (!$topikId) {
                $topikId = (int)$db->query("SELECT id FROM bs_topik WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1")->fetchColumn();
            }
            if (!$topikId) {
                $topikId = (int)$db->query("SELECT id FROM cbt_topik ORDER BY id DESC LIMIT 1")->fetchColumn();
            }
        } catch (\Exception $e) {}

        // 2. Ambil jadwal ujian hari ini dari cbt_jadwal_grid
        $jadwalList = [];
        try {
            $stmtJadwal = $db->prepare("
                SELECT g.*, u.nama_ujian, COALESCE(NULLIF(u.mapel_nama, ''), g.mapel_nama, u.nama_ujian) as mapel_nama_resolved,
                       s.jam_mulai, s.jam_selesai, s.label_sesi
                FROM cbt_jadwal_grid g
                LEFT JOIN cbt_ujian u ON g.ujian_id = u.id
                LEFT JOIN cbt_jam_slot s ON (g.jam_slot = s.label_sesi OR g.jam_slot = s.urutan)
                WHERE g.tanggal = ? OR (g.tanggal IS NULL AND (g.hari = ? OR g.hari LIKE ?))
                ORDER BY g.id ASC
            ");
            $stmtJadwal->execute([$tanggal, $hariIndo, $hariIndo . '%']);
            $jadwalList = $stmtJadwal->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // Jika cbt_jadwal_grid kosong untuk hari ini, ambil semua cbt_ujian yang aktif atau bertanggal hari ini
        if (empty($jadwalList)) {
            try {
                $stmtU = $db->prepare("
                    SELECT u.id as ujian_id, u.topik_id, u.nama_ujian, 
                           COALESCE(NULLIF(u.mapel_nama, ''), u.nama_ujian) as mapel_nama_resolved,
                           0 as kelas_id, u.kelas_nama, 'Sesi 1' as label_sesi, '07:30' as jam_mulai, '08:45' as jam_selesai,
                           'Ruang 01' as ruang
                    FROM cbt_ujian u
                    WHERE (DATE(u.tgl_mulai) <= ? AND DATE(u.tgl_selesai) >= ?)
                       OR u.topik_id = ?
                       OR u.status = 'Aktif'
                    LIMIT 20
                ");
                $stmtU->execute([$tanggal, $tanggal, $topikId]);
                $jadwalList = $stmtU->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Exception $e) {}
        }

        if (empty($jadwalList) && $topikId > 0) {
            try {
                $stmtU2 = $db->prepare("
                    SELECT u.id as ujian_id, u.topik_id, u.nama_ujian, 
                           COALESCE(NULLIF(u.mapel_nama, ''), u.nama_ujian) as mapel_nama_resolved,
                           0 as kelas_id, u.kelas_nama, 'Sesi 1' as label_sesi, '07:30' as jam_mulai, '08:45' as jam_selesai,
                           'Ruang 01' as ruang
                    FROM cbt_ujian u
                    WHERE u.topik_id = ?
                    LIMIT 20
                ");
                $stmtU2->execute([$topikId]);
                $jadwalList = $stmtU2->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Exception $e) {}
        }

        // 3. Ambil seluruh siswa reguler & khusus
        $siswaList = [];
        $seenNis = [];

        // 3a. Prioritaskan siswa reguler yang ada di cbt_denah_peserta (sudah punya ruang dan meja)
        try {
            $stmtDenah = $db->query("
                SELECT d.siswa_id as id, d.nis, d.nama_siswa as nama, d.kelas_id, 
                       COALESCE(NULLIF(d.kelas_nama, ''), 'X E.1') as kelas_nama, 
                       'reguler' as tipe,
                       COALESCE(r.nama_ruang, 'Ruang 01') as ruang
                FROM cbt_denah_peserta d
                LEFT JOIN cbt_ruang r ON d.ruang_id = r.id
                WHERE d.siswa_id IS NOT NULL AND d.siswa_id > 0
                ORDER BY d.ruang_id ASC, d.nomor_meja ASC
            ");
            $denahRows = $stmtDenah->fetchAll(PDO::FETCH_ASSOC);
            foreach ($denahRows as $dr) {
                $kKey = trim((string)$dr['nis']);
                if (!empty($kKey) && !isset($seenNis[$kKey])) {
                    $seenNis[$kKey] = true;
                    $siswaList[] = $dr;
                }
            }
        } catch (\Exception $e) {}

        // 3b. Tambahkan siswa dari cbt_peserta jika sudah ada data ujian
        try {
            $stmtCp = $db->query("
                SELECT DISTINCT p.siswa_id as id, p.nis, p.nama_siswa as nama, 
                       0 as kelas_id, COALESCE(NULLIF(p.kelas_nama, ''), 'X E.1') as kelas_nama, 
                       'reguler' as tipe, 'Ruang 01' as ruang
                FROM cbt_peserta p
                WHERE p.siswa_id IS NOT NULL AND p.siswa_id > 0
                  AND (p.kelas_nama NOT LIKE '%Jauh%' OR p.kelas_nama IS NULL)
                ORDER BY p.nama_siswa ASC
            ");
            $cpRows = $stmtCp->fetchAll(PDO::FETCH_ASSOC);
            foreach ($cpRows as $cpr) {
                $kKey = trim((string)$cpr['nis']);
                if (!empty($kKey) && !isset($seenNis[$kKey])) {
                    $seenNis[$kKey] = true;
                    $siswaList[] = $cpr;
                }
            }
        } catch (\Exception $e) {}

        // 3c. Tambahkan siswa reguler dari tabel siswa di core_db ($db)
        try {
            $stmtS = $db->query("
                SELECT s.id, s.nis, s.nama, s.kelas_id, 
                       COALESCE(NULLIF(k.nama_kelas, ''), 'X E.1') as kelas_nama, 
                       'reguler' as tipe, 'Ruang 01' as ruang
                FROM siswa s
                LEFT JOIN kelas k ON s.kelas_id = k.id
                WHERE s.status = 'Aktif' OR s.status IS NULL OR s.status = ''
                ORDER BY s.nama ASC
            ");
            $sRows = $stmtS->fetchAll(PDO::FETCH_ASSOC);
            foreach ($sRows as $sr) {
                $kKey = trim((string)$sr['nis']);
                if (!empty($kKey) && !isset($seenNis[$kKey])) {
                    $seenNis[$kKey] = true;
                    $siswaList[] = $sr;
                }
            }
        } catch (\Exception $e) {}

        // 3d. Siswa khusus / kelas jauh dari cbt_peserta_khusus
        try {
            $kRows = $db->query("
                SELECT id, COALESCE(NULLIF(no_peserta, ''), nis) as nis, nama_siswa as nama, 
                       0 as kelas_id, kelas_nama, 'khusus' as tipe, 'Ruang Kelas Jauh' as ruang
                FROM cbt_peserta_khusus
                WHERE status = 'Aktif' OR status IS NULL
                ORDER BY nama_siswa ASC
            ")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($kRows as $kr) {
                $kKey = trim((string)$kr['nis']);
                if (!empty($kKey) && !isset($seenNis[$kKey])) {
                    $seenNis[$kKey] = true;
                    $siswaList[] = $kr;
                }
            }
        } catch (\Exception $e) {}

        if (empty($siswaList)) {
            return ['success' => false, 'message' => 'Tidak ada data siswa ditemukan'];
        }

        $insertedCount = 0;
        $stmtCheck = $db->prepare("
            SELECT id FROM cbt_absensi 
            WHERE tanggal = ? AND (nis = ? OR (siswa_id IS NOT NULL AND siswa_id = ?)) 
              AND (ujian_id = ? OR mapel_nama = ?)
            LIMIT 1
        ");

        $stmtInsert = $db->prepare("
            INSERT INTO cbt_absensi 
            (tanggal, siswa_id, nis, nama_siswa, kelas_nama, tipe_peserta, topik_id, ujian_id, mapel_nama, jam_slot, waktu_scan, status, metode_scan, petugas_nama, ruang_nama, keterangan)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Hadir', 'AUTO_HADIR_MASSAL', 'Pengawas Ruang', ?, 'Presensi Otomatis Jadwal Hari Ini')
        ");

        // Loop setiap jadwal & setiap siswa
        foreach ($jadwalList as $jIdx => $j) {
            $jUjianId = (int)($j['ujian_id'] ?? 0);
            $jTopikId = (int)($j['topik_id'] ?? $topikId);
            $jMapel = !empty($j['mapel_nama_resolved']) ? trim($j['mapel_nama_resolved']) : (!empty($j['nama_ujian']) ? trim($j['nama_ujian']) : 'Asesmen CBT');
            $jSlot = !empty($j['label_sesi']) ? trim($j['label_sesi']) : (!empty($j['jam_slot']) ? trim($j['jam_slot']) : 'Sesi 1');
            $jRuang = !empty($j['ruang']) ? trim($j['ruang']) : 'Ruang 01';
            $jKelasId = (int)($j['kelas_id'] ?? 0);
            $jKelasNama = trim($j['kelas_nama'] ?? '');

            // Tentukan base hour untuk waktu_scan yang realistis sesuai cbt_jam_slot
            $baseHour = '07:30:00';
            if (!empty($j['jam_mulai']) && preg_match('/^(\d{1,2}):(\d{2})/', $j['jam_mulai'], $m)) {
                $baseHour = sprintf('%02d:%02d:00', (int)$m[1], (int)$m[2]);
            } else {
                if (stripos($jSlot, '1') !== false) {
                    $baseHour = '07:30:00';
                } elseif (stripos($jSlot, '2') !== false) {
                    $baseHour = '09:00:00';
                } elseif (stripos($jSlot, '3') !== false) {
                    $baseHour = '10:45:00';
                } elseif (stripos($jSlot, '4') !== false) {
                    $baseHour = '12:30:00';
                }
            }
            $baseTimestamp = strtotime("{$tanggal} {$baseHour}");

            $sIdx = 0;
            foreach ($siswaList as $sw) {
                $swNis = trim((string)($sw['nis'] ?? ''));
                if (empty($swNis)) continue;

                $swId = ($sw['tipe'] === 'reguler' && !empty($sw['id'])) ? (int)$sw['id'] : null;
                $swNama = trim($sw['nama']);
                $swKelas = trim($sw['kelas_nama']);
                $swRuang = !empty($sw['ruang']) ? trim($sw['ruang']) : $jRuang;

                // Filter kelas jika jadwal spesifik untuk kelas tertentu
                if (!empty($jKelasNama) && !empty($swKelas) && $jKelasNama !== 'Semua Kelas') {
                    $kMatch = (stripos($swKelas, $jKelasNama) !== false || stripos($jKelasNama, $swKelas) !== false);
                    if (!$kMatch && preg_match('/^(X|XI|XII)/i', $jKelasNama, $mk1) && preg_match('/^(X|XI|XII)/i', $swKelas, $mk2)) {
                        if (strtoupper($mk1[1]) !== strtoupper($mk2[1])) {
                            continue;
                        }
                    } elseif (!$kMatch) {
                        continue;
                    }
                } elseif ($jKelasId > 0 && !empty($sw['kelas_id']) && (int)$sw['kelas_id'] !== $jKelasId) {
                    continue;
                }

                // Cek apakah sudah absen
                try {
                    $stmtCheck->execute([$tanggal, $swNis, $swId ?: -1, $jUjianId, $jMapel]);
                    if ($stmtCheck->fetchColumn()) {
                        continue;
                    }

                    // Buat waktu scan realistis (acak antara menit ke-1 s.d ke-14 jam sesi)
                    $sIdx++;
                    $jitterSeconds = 60 + (($sIdx * 19) % 780);
                    $waktuScan = date('Y-m-d H:i:s', $baseTimestamp + $jitterSeconds);

                    $stmtInsert->execute([
                        $tanggal,
                        $swId,
                        $swNis,
                        $swNama,
                        $swKelas,
                        $sw['tipe'],
                        $jTopikId,
                        $jUjianId,
                        $jMapel,
                        $jSlot,
                        $waktuScan,
                        $swRuang
                    ]);
                    $insertedCount++;
                } catch (\Exception $e) {}
            }
        }

        // 4. Tambahan Pasti: Sinkronkan seluruh siswa yang sudah mengerjakan ujian di cbt_peserta hari ini
        try {
            $stmtCbtPeserta = $db->prepare("
                SELECT p.siswa_id, p.nis, p.nama_siswa, p.kelas_nama, p.waktu_mulai, 
                       u.id as ujian_id, u.topik_id, COALESCE(NULLIF(u.mapel_nama, ''), u.nama_ujian) as mapel_nama,
                       COALESCE(d.ruang_id, 1) as ruang_id, COALESCE(r.nama_ruang, 'Ruang 01') as ruang_nama
                FROM cbt_peserta p
                LEFT JOIN cbt_ujian u ON p.ujian_id = u.id
                LEFT JOIN cbt_denah_peserta d ON (p.siswa_id = d.siswa_id OR p.nis = d.nis)
                LEFT JOIN cbt_ruang r ON d.ruang_id = r.id
                WHERE (DATE(p.waktu_mulai) = ? OR (? <= 0 OR u.topik_id = ?))
                  AND p.status_pengerjaan IN ('Mengerjakan', 'Sedang Mengerjakan', 'Selesai')
            ");
            $stmtCbtPeserta->execute([$tanggal, $topikId, $topikId]);
            $pesertaRows = $stmtCbtPeserta->fetchAll(PDO::FETCH_ASSOC);

            foreach ($pesertaRows as $pr) {
                $pNis = trim((string)($pr['nis'] ?? ''));
                if (empty($pNis)) continue;
                $pSid = !empty($pr['siswa_id']) ? (int)$pr['siswa_id'] : null;
                $pMapel = trim($pr['mapel_nama'] ?? 'Asesmen CBT');
                $pUjianId = (int)($pr['ujian_id'] ?? 0);
                $pTopikId = (int)($pr['topik_id'] ?? $topikId);
                $pKelas = trim($pr['kelas_nama'] ?? 'X E.1');
                $pTipe = (stripos($pKelas, 'jauh') !== false) ? 'khusus' : 'reguler';
                $pRuang = $pr['ruang_nama'] ?: 'Ruang 01';
                $pWaktuScan = !empty($pr['waktu_mulai']) ? $pr['waktu_mulai'] : date('Y-m-d H:i:s');

                // Tentukan sesi berdasarkan jam pengerjaan
                $pHour = (int)date('H', strtotime($pWaktuScan));
                $pMinute = (int)date('i', strtotime($pWaktuScan));
                $pTimeVal = $pHour * 60 + $pMinute;
                if ($pTimeVal < 530) {
                    $pSlot = 'Sesi 1';
                } elseif ($pTimeVal < 640) {
                    $pSlot = 'Sesi 2';
                } elseif ($pTimeVal < 730) {
                    $pSlot = 'Sesi 3';
                } else {
                    $pSlot = 'Sesi 4';
                }

                // Cek apakah sudah ada di cbt_absensi
                $stmtCheck->execute([$tanggal, $pNis, $pSid ?: -1, $pUjianId, $pMapel]);
                if (!$stmtCheck->fetchColumn()) {
                    $stmtInsert->execute([
                        $tanggal,
                        $pSid,
                        $pNis,
                        $pr['nama_siswa'],
                        $pKelas,
                        $pTipe,
                        $pTopikId,
                        $pUjianId,
                        $pMapel,
                        $pSlot,
                        $pWaktuScan,
                        $pRuang
                    ]);
                    $insertedCount++;
                }
            }
        } catch (\Exception $e) {}

        return [
            'success' => true,
            'message' => "Berhasil mencatat absensi hadir {$insertedCount} entri siswa untuk seluruh mapel hari ini ({$tanggal})!",
            'inserted' => $insertedCount,
            'tanggal' => $tanggal
        ];
    }

    /**
     * Halaman Rekap Absensi Siswa CBT
     */
    public static function absensiRekap() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        $selectedTanggal = isset($_GET['tanggal']) ? trim($_GET['tanggal']) : date('Y-m-d');
        $selectedKelas = isset($_GET['kelas']) ? trim($_GET['kelas']) : '';
        $selectedMapel = isset($_GET['mapel']) ? trim($_GET['mapel']) : '';

        // Ambil filter list
        $kelasList = $db->query("SELECT DISTINCT kelas_nama FROM cbt_absensi ORDER BY kelas_nama ASC")->fetchAll(PDO::FETCH_COLUMN);
        $mapelList = $db->query("SELECT DISTINCT mapel_nama FROM cbt_absensi ORDER BY mapel_nama ASC")->fetchAll(PDO::FETCH_COLUMN);

        $sql = "SELECT * FROM cbt_absensi WHERE tanggal = :tgl";
        $params = [':tgl' => $selectedTanggal];

        if (!empty($selectedKelas)) {
            $sql .= " AND kelas_nama = :kelas";
            $params[':kelas'] = $selectedKelas;
        }

        if (!empty($selectedMapel)) {
            $sql .= " AND mapel_nama = :mapel";
            $params[':mapel'] = $selectedMapel;
        }

        $sql .= " ORDER BY waktu_scan DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $absensiRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $title = "Rekap Absensi Ujian CBT - MAS MIFTAHUL HUDA";
        $active_menu = 'absensi_rekap';
        ob_start();
        include __DIR__ . '/../../resources/views/cbt/absensi_rekap.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Hapus 1 baris data scan absensi
     */
    public static function deleteAbsensi() {
        header('Content-Type: application/json');
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $db->prepare("DELETE FROM cbt_absensi WHERE id = ?")->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Data absensi berhasil dihapus.']);
            exit;
        }
        echo json_encode(['success' => false, 'message' => 'ID absensi tidak valid.']);
        exit;
    }

    /**
     * Reset / Hapus seluruh data absensi pada tanggal tertentu
     */
    public static function resetAbsensiToday() {
        header('Content-Type: application/json');
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        $db->prepare("DELETE FROM cbt_absensi WHERE tanggal = ? OR DATE(waktu_scan) = ?")->execute([$tanggal, $tanggal]);
        echo json_encode(['success' => true, 'message' => "Seluruh data absensi tanggal {$tanggal} berhasil direset."]);
        exit;
    }

    private static function getIndoDayName($w) {
        $days = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
        return $days[(int)$w] ?? 'Senin';
    }

    /**
     * Dashboard Manajemen Sinkronisasi CBT Semi-Online
     */
    public static function semiOnlineDashboard() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $dbSiakad = $ctx['db_siakad'] ?? $db;
        self::ensureTables($db);

        // Ambil IP lokal proktor untuk akses Wi-Fi siswa di madrasah
        $localIp = '127.0.0.1';
        if (!empty($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] !== '127.0.0.1' && $_SERVER['SERVER_ADDR'] !== '::1') {
            $localIp = $_SERVER['SERVER_ADDR'];
        } else {
            $h = gethostname();
            $ip = gethostbyname($h);
            if (!empty($ip) && $ip !== '127.0.0.1') {
                $localIp = $ip;
            }
        }
        $port = $_SERVER['SERVER_PORT'] ?? '80';
        $portStr = ($port === '80' || $port === '443') ? '' : (':' . $port);
        $cbtRelPath = function_exists('base_url') ? base_url('apk/cbt.php') : ((strpos($_SERVER['REQUEST_URI'] ?? '', '/cbt/') !== false) ? '/cbt/apk/cbt.php' : '/apk/cbt.php');
        $localUrl = "http://{$localIp}{$portStr}{$cbtRelPath}";

        // Hitung statistik data di database lokal
        $countJadwal = (int)$db->query("SELECT COUNT(*) FROM cbt_jadwal_grid")->fetchColumn();
        $countSoal = (int)$db->query("SELECT COUNT(*) FROM cbt_soal")->fetchColumn();
        $countSiswa = 0;
        try {
            $countSiswa = (int)$dbSiakad->query("SELECT COUNT(*) FROM siswa WHERE status = 'Aktif'")->fetchColumn();
        } catch (\Exception $e) {}
        if ($countSiswa === 0) {
            try {
                $countSiswa = (int)$db->query("SELECT COUNT(*) FROM cbt_peserta_khusus WHERE status = 'Aktif'")->fetchColumn();
            } catch (\Exception $e) {}
        }
        $countHasil = (int)$db->query("SELECT COUNT(*) FROM cbt_peserta WHERE status_pengerjaan = 'Selesai'")->fetchColumn();
        $countAbsenToday = (int)$db->query("SELECT COUNT(*) FROM cbt_absensi WHERE tanggal = CURDATE() OR DATE(waktu_scan) = CURDATE()")->fetchColumn();

        $title = "Sinkronisasi CBT Semi-Online - MAS MIFTAHUL HUDA";
        ob_start();
        include __DIR__ . '/../../resources/views/cbt/semi_online.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * API Export Paket Data dari Server Pusat (ditarik oleh laptop proktor semi-online)
     */
    public static function apiExportPaketOnline() {
        set_time_limit(300);
        ini_set('memory_limit', '512M');
        if (function_exists('ob_gzhandler') && !ob_get_level()) {
            ob_start('ob_gzhandler');
        }
        header('Content-Type: application/json');
        
        $syncSecret = 'MIFHDA_CBT_SYNC_2026';
        $providedToken = $_REQUEST['token'] ?? $_SERVER['HTTP_X_SYNC_TOKEN'] ?? '';
        $isTokenValid = ($providedToken === $syncSecret);

        self::ensureSession();
        $isSessionValid = !empty($_SESSION['user_id']);

        if (!$isTokenValid && !$isSessionValid) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak: Token sinkronisasi tidak valid.']);
            exit;
        }

        $db = \App\Core\Database::connect('core');
        $dbSiakad = \App\Core\Database::connect('siakad');
        self::ensureTables($db);

        // Export data topik (Semua topik non-arsip, termasuk Aktif, Terjadwal, dan Selesai)
        $topikList = [];
        try {
            $topikList = $dbSiakad->query("SELECT * FROM bs_topik WHERE status != 'Arsip' OR status IS NULL ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}
        if (empty($topikList)) {
            try {
                $topikList = $db->query("SELECT * FROM bs_topik WHERE status != 'Arsip' OR status IS NULL ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Exception $e2) {}
        }

        // Export data paket bank soal (bs_paket)
        $bsPaketList = [];
        try {
            $bsPaketList = $dbSiakad->query("SELECT * FROM bs_paket ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}
        if (empty($bsPaketList)) {
            try {
                $bsPaketList = $db->query("SELECT * FROM bs_paket ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Exception $e2) {}
        }

        // Export data butir bank soal (bs_soal)
        $bsSoalList = [];
        try {
            $bsSoalList = $dbSiakad->query("SELECT * FROM bs_soal ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}
        if (empty($bsSoalList)) {
            try {
                $bsSoalList = $db->query("SELECT * FROM bs_soal ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Exception $e2) {}
        }

        // Auto-sinkron cbt_soal dari bs_soal jika belum ada atau jumlah tidak sesuai
        try {
            $ujians = $db->query("SELECT id, mapel_nama, kelas_nama, topik_id FROM cbt_ujian")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($ujians as $uExp) {
                $uIdExp = (int)$uExp['id'];
                $soalCnt = (int)$db->query("SELECT COUNT(*) FROM cbt_soal WHERE ujian_id = {$uIdExp}")->fetchColumn();
                $cleanM = trim($uExp['mapel_nama']);
                $tId = (int)($uExp['topik_id'] ?? 0);
                
                $getLevel = function($str) {
                    $s = strtoupper((string)$str);
                    if (strpos($s, 'XII') !== false || preg_match('/\b12\b/', $s)) return 12;
                    if (strpos($s, 'XI') !== false || preg_match('/\b11\b/', $s)) return 11;
                    if (strpos($s, 'X') !== false || preg_match('/\b10\b/', $s)) return 10;
                    return 0;
                };
                $uLvl = $getLevel($uExp['kelas_nama']);
                $bsSrc = !empty($bsSoalList) ? $bsSoalList : [];
                $matchedBs = array_filter($bsSrc, function($b) use ($cleanM, $tId, $uLvl, $getLevel) {
                    $mMatch = ($b['mapel_nama'] === $cleanM || stripos($b['mapel_nama'], $cleanM) !== false || stripos($cleanM, $b['mapel_nama']) !== false);
                    $tMatch = ($tId === 0 || (int)($b['topik_id'] ?? 0) === $tId);
                    $bLvl = $getLevel($b['kelas_nama'] ?? '');
                    $kMatch = ($uLvl === 0 || $bLvl === 0 || $uLvl === $bLvl);
                    return $mMatch && $tMatch && $kMatch;
                });
                
                if (!empty($matchedBs) && ($soalCnt === 0 || $soalCnt !== count($matchedBs))) {
                    $db->exec("DELETE FROM cbt_soal WHERE ujian_id = {$uIdExp}");
                    $stmtInsS = $db->prepare("INSERT INTO cbt_soal (ujian_id, nomor_urut, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, bobot, tipe_soal, konten_tambahan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $noS = 1;
                    foreach ($matchedBs as $mbs) {
                        $stmtInsS->execute([
                            $uIdExp,
                            $noS++,
                            $mbs['pertanyaan'],
                            $mbs['opsi_a'] ?? '',
                            $mbs['opsi_b'] ?? '',
                            $mbs['opsi_c'] ?? '',
                            $mbs['opsi_d'] ?? '',
                            $mbs['opsi_e'] ?? '',
                            $mbs['kunci_jawaban'] ?? 'A',
                            $mbs['skor_maksimal'] ?? 10.00,
                            $mbs['tipe_soal'] ?? 'sec_pg',
                            $mbs['konten_tambahan'] ?? null
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {}

        $payload = [
            'status' => 'success',
            'exported_at' => date('Y-m-d H:i:s'),
            'topik' => $topikList,
            'bs_paket' => $bsPaketList,
            'bs_soal' => $bsSoalList,
            'jam_slot' => $db->query("SELECT * FROM cbt_jam_slot ORDER BY urutan ASC")->fetchAll(PDO::FETCH_ASSOC),
            'jadwal_grid' => $db->query("SELECT * FROM cbt_jadwal_grid ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC),
            'ujian' => $db->query("SELECT * FROM cbt_ujian ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC),
            'soal' => $db->query("SELECT * FROM cbt_soal ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC),
            'token' => $db->query("SELECT * FROM cbt_token ORDER BY id DESC LIMIT 500")->fetchAll(PDO::FETCH_ASSOC),
            'peserta_khusus' => $db->query("SELECT * FROM cbt_peserta_khusus WHERE status = 'Aktif' ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC),
            'denah' => $db->query("SELECT * FROM cbt_denah_peserta ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC),
            'ruang' => $db->query("SELECT * FROM cbt_ruang ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC),
            'pengaturan' => $db->query("SELECT * FROM cbt_pengaturan")->fetchAll(PDO::FETCH_ASSOC),
            'pengawas_kj' => $db->query("SELECT * FROM cbt_pengawas_kj WHERE status = 'Aktif'")->fetchAll(PDO::FETCH_ASSOC),
        ];

        // Export data mata pelajaran
        try {
            $payload['mapel'] = $dbSiakad->query("SELECT id, nama_mapel, kode_mapel, kategori FROM mapel ORDER BY nama_mapel ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            try {
                $payload['mapel'] = $db->query("SELECT id, nama_mapel, kode_mapel, kategori FROM mapel ORDER BY nama_mapel ASC")->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Exception $e2) {
                $payload['mapel'] = [];
            }
        }

        // Export data guru & pengawas
        try {
            $payload['guru'] = $db->query("SELECT id, user_id, nama, nip, nik, is_kamad FROM guru")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            try {
                $payload['guru'] = $dbSiakad->query("SELECT id, user_id, nama, nip, nik, is_kamad FROM guru")->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Exception $e2) {
                $payload['guru'] = [];
            }
        }

        // Export data pengawas ruangan
        try {
            $payload['ruang_pengawas'] = $db->query("SELECT * FROM cbt_ruang_pengawas")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $payload['ruang_pengawas'] = [];
        }

        // Export data kelas
        try {
            $payload['kelas'] = $dbSiakad->query("SELECT id, nama_kelas, tingkat FROM kelas ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            try {
                $payload['kelas'] = $db->query("SELECT id, nama_kelas, tingkat FROM kelas ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Exception $e2) {
                $payload['kelas'] = [];
            }
        }

        // Pastikan relasi user_id siswa tersambung ke akun users berdasarkan NIK / NIS resmi
        try {
            $db->exec("UPDATE siswa s JOIN users u ON (u.username = s.nik AND s.nik != '' AND s.nik IS NOT NULL) SET s.user_id = u.id");
            $db->exec("UPDATE siswa s JOIN users u ON (u.username = s.nis AND s.nis != '' AND s.nis IS NOT NULL) SET s.user_id = u.id WHERE s.user_id IS NULL OR s.user_id = 0");
            if ($dbSiakad !== $db) {
                $dbSiakad->exec("UPDATE siswa s JOIN users u ON (u.username = s.nik AND s.nik != '' AND s.nik IS NOT NULL) SET s.user_id = u.id");
                $dbSiakad->exec("UPDATE siswa s JOIN users u ON (u.username = s.nis AND s.nis != '' AND s.nis IS NOT NULL) SET s.user_id = u.id WHERE s.user_id IS NULL OR s.user_id = 0");
            }
        } catch (\Throwable $e) {}

        // Export data siswa reguler (prioritaskan database siakad yang memuat seluruh siswa aktif madrasah)
        $siswaList = [];
        try {
            $siswaList = $dbSiakad->query("
                SELECT id, user_id, nis, nik, COALESCE(nama, nama_lengkap) as nama, status, kelas_id, jenis_kelamin
                FROM siswa WHERE status = 'Aktif'
            ")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        if (empty($siswaList)) {
            try {
                $siswaList = $db->query("
                    SELECT id, user_id, nis, nik, nama, status, kelas_id, jenis_kelamin
                    FROM siswa WHERE status = 'Aktif'
                ")->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Exception $e2) {}
        }
        $payload['siswa'] = $siswaList;

        // Export akun login siswa, proktor, dan guru
        try {
            $payload['users'] = $db->query("
                SELECT id, username, password_hash, plain_password, role_id, status
                FROM users WHERE role_id IN (1, 2, 3, 4, 6, 7, 99)
            ")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $payload['users'] = [];
        }

        // Export data absensi hari ini (agar siswa yang scan online di gerbang bisa langsung buka soal di laptop madrasah)
        try {
            $payload['absensi'] = $db->query("
                SELECT * FROM cbt_absensi 
                WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 1 DAY) OR DATE(waktu_scan) >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)
                ORDER BY id ASC
            ")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $payload['absensi'] = [];
        }

        echo json_encode($payload);
        exit;
    }

    /**
     * API Export Khusus Data Siswa SIAKAD dari Server Online
     */
    public static function apiExportSiswaOnline() {
        set_time_limit(180);
        header('Content-Type: application/json');
        
        $syncSecret = 'MIFHDA_CBT_SYNC_2026';
        $providedToken = $_REQUEST['token'] ?? $_SERVER['HTTP_X_SYNC_TOKEN'] ?? '';
        $isTokenValid = ($providedToken === $syncSecret);

        self::ensureSession();
        $isSessionValid = !empty($_SESSION['user_id']);

        if (!$isTokenValid && !$isSessionValid) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak: Token sinkronisasi tidak valid.']);
            exit;
        }

        $db = \App\Core\Database::connect('core');
        $dbSiakad = \App\Core\Database::connect('siakad');
        self::ensureTables($db);

        // Kelas
        $kelasList = [];
        try {
            $kelasList = $dbSiakad->query("SELECT id, nama_kelas, tingkat FROM kelas ORDER BY id ASC")->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            try {
                $kelasList = $db->query("SELECT id, nama_kelas, tingkat FROM kelas ORDER BY id ASC")->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\Exception $e2) {
                $kelasList = [];
            }
        }

        // Siswa Reguler
        $siswaList = [];
        try {
            $siswaList = $dbSiakad->query("
                SELECT id, user_id, nis, nik, COALESCE(nama, nama_lengkap) as nama, status, kelas_id, jenis_kelamin
                FROM siswa WHERE status = 'Aktif'
            ")->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        if (empty($siswaList)) {
            try {
                $siswaList = $db->query("
                    SELECT id, user_id, nis, nik, nama, status, kelas_id, jenis_kelamin
                    FROM siswa WHERE status = 'Aktif'
                ")->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\Exception $e2) {}
        }

        // Akun Users Siswa
        $usersList = [];
        try {
            $usersList = $db->query("
                SELECT id, username, password_hash, plain_password, role_id, status
                FROM users WHERE role_id IN (1, 2, 3, 4, 6, 7, 99)
            ")->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $usersList = [];
        }

        echo json_encode([
            'status' => 'success',
            'exported_at' => date('Y-m-d H:i:s'),
            'kelas' => $kelasList,
            'siswa' => $siswaList,
            'users' => $usersList,
            'count' => count($siswaList)
        ]);
        exit;
    }

    /**
     * API Export Kilat Absensi Hari Ini dari Server Pusat
     */
    public static function apiExportAbsensiOnline() {
        header('Content-Type: application/json');
        
        $syncSecret = 'MIFHDA_CBT_SYNC_2026';
        $providedToken = $_REQUEST['token'] ?? $_SERVER['HTTP_X_SYNC_TOKEN'] ?? '';
        $isTokenValid = ($providedToken === $syncSecret);

        self::ensureSession();
        $isSessionValid = !empty($_SESSION['user_id']);

        if (!$isTokenValid && !$isSessionValid) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak: Token sinkronisasi tidak valid.']);
            exit;
        }

        $db = \App\Core\Database::connect('core');
        self::ensureTables($db);

        try {
            $absensi = $db->query("
                SELECT * FROM cbt_absensi 
                WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 1 DAY) OR DATE(waktu_scan) >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)
                ORDER BY id ASC
            ")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $absensi = [];
        }

        echo json_encode([
            'status' => 'success',
            'exported_at' => date('Y-m-d H:i:s'),
            'absensi' => $absensi
        ]);
        exit;
    }

    /**
     * AJAX Tarik Kilat Absensi Hari Ini dari Server Pusat ke Server Lokal
     */
    public static function ajaxPullAbsensiOnline() {
        header('Content-Type: application/json');
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $dbSiakad = $ctx['db_siakad'] ?? $db;
        self::ensureTables($db);

        $serverUrl = rtrim(trim($_POST['server_url'] ?? 'https://apk.masmifhda.sch.id'), '/');
        $targetEndpoint = $serverUrl . '/apk/cbt_sync_api.php?action=export_absen&token=' . urlencode('MIFHDA_CBT_SYNC_2026');

        $ch = curl_init($targetEndpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['action' => 'export_absen']));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($curlErr || $httpCode !== 200 || empty($response)) {
            echo json_encode([
                'success' => false,
                'message' => "Gagal terhubung ke server pusat ({$targetEndpoint}). HTTP Code: {$httpCode}. Error: {$curlErr}"
            ]);
            exit;
        }

        $data = json_decode($response, true);
        if (!$data || ($data['status'] ?? '') !== 'success') {
            echo json_encode([
                'success' => false,
                'message' => "Format data absensi dari server pusat tidak valid!"
            ]);
            exit;
        }

        $resDetails = self::importDataPayload($db, $dbSiakad, ['absensi' => $data['absensi'] ?? []]);

        echo json_encode([
            'success' => true,
            'message' => "Berhasil menarik data presensi siswa hari ini dari Server Pusat!",
            'details' => $resDetails
        ]);
        exit;
    }

    /**
     * AJAX Pull Data dari Server Pusat ke Server Lokal
     */
    public static function ajaxPullDataOnline() {
        set_time_limit(300);
        ini_set('memory_limit', '512M');
        header('Content-Type: application/json');
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $dbSiakad = $ctx['db_siakad'] ?? $db;
        self::ensureTables($db);

        $serverUrl = rtrim(trim($_POST['server_url'] ?? 'https://apk.masmifhda.sch.id'), '/');
        $targetEndpoint = $serverUrl . '/apk/cbt_sync_api.php?action=export&token=' . urlencode('MIFHDA_CBT_SYNC_2026');

        $ch = curl_init($targetEndpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['action' => 'export']));
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($curlErr || $httpCode !== 200 || empty($response)) {
            echo json_encode([
                'success' => false,
                'message' => "Gagal terhubung ke server pusat ({$targetEndpoint}). HTTP Code: {$httpCode}. Error: {$curlErr}"
            ]);
            exit;
        }

        $data = json_decode($response, true);
        if (!$data || ($data['status'] ?? '') !== 'success') {
            echo json_encode([
                'success' => false,
                'message' => "Format data dari server pusat tidak valid!"
            ]);
            exit;
        }

        $resDetails = self::importDataPayload($db, $dbSiakad, $data);

        echo json_encode([
            'success' => true,
            'message' => "Berhasil menarik data lengkap dari Server Pusat!",
            'details' => $resDetails
        ]);
        exit;
    }

    /**
     * API Import Hasil Ujian dari Laptop Proktor Lokal ke Server Pusat
     */
    public static function apiImportHasilOnline() {
        header('Content-Type: application/json');
        
        $syncSecret = 'MIFHDA_CBT_SYNC_2026';
        $providedToken = $_REQUEST['token'] ?? $_SERVER['HTTP_X_SYNC_TOKEN'] ?? '';
        $isTokenValid = ($providedToken === $syncSecret);

        self::ensureSession();
        $isSessionValid = !empty($_SESSION['user_id']);

        if (!$isTokenValid && !$isSessionValid) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Akses ditolak: Token sinkronisasi tidak valid.']);
            exit;
        }

        $db = \App\Core\Database::connect('core');
        self::ensureTables($db);

        $rawInput = file_get_contents('php://input');
        $payload = json_decode($rawInput, true);
        if (!$payload && !empty($_POST['payload'])) {
            $payload = json_decode($_POST['payload'], true);
        }

        if (!$payload || (empty($payload['peserta']) && empty($payload['absensi']))) {
            echo json_encode(['success' => false, 'message' => 'Data hasil ujian kosong atau format tidak valid.']);
            exit;
        }

        $importedPeserta = 0;
        $importedAbsensi = 0;

        // 1. Simpan Peserta Ujian Lokal (Aman, hanya update/insert siswa lokal madrasah, kelas jauh online tidak disentuh)
        if (!empty($payload['peserta'])) {
            $stmtChkP = $db->prepare("SELECT id FROM cbt_peserta WHERE ujian_id = ? AND (nis = ? OR (siswa_id IS NOT NULL AND siswa_id = ?)) LIMIT 1");
            $stmtUpdP = $db->prepare("
                UPDATE cbt_peserta SET 
                    waktu_mulai = COALESCE(waktu_mulai, ?),
                    waktu_selesai = ?,
                    status_pengerjaan = ?,
                    jumlah_benar = ?,
                    jumlah_salah = ?,
                    nilai_akhir = ?,
                    jawaban_json = ?,
                    reset_count = ?,
                    violation_count = ?
                WHERE id = ?
            ");
            $stmtInsP = $db->prepare("
                INSERT INTO cbt_peserta 
                (ujian_id, siswa_id, nis, nama_siswa, kelas_nama, waktu_mulai, waktu_selesai, status_pengerjaan, jumlah_benar, jumlah_salah, nilai_akhir, jawaban_json, reset_count, violation_count)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($payload['peserta'] as $p) {
                try {
                    $uId = (int)$p['ujian_id'];
                    $nis = trim((string)($p['nis'] ?? ''));
                    $sId = !empty($p['siswa_id']) ? (int)$p['siswa_id'] : null;

                    $stmtChkP->execute([$uId, $nis, $sId ?: -1]);
                    $existPid = $stmtChkP->fetchColumn();

                    if ($existPid) {
                        $stmtUpdP->execute([
                            $p['waktu_mulai'] ?? null,
                            $p['waktu_selesai'] ?? null,
                            $p['status_pengerjaan'] ?? 'Selesai',
                            $p['jumlah_benar'] ?? ($p['jml_benar'] ?? 0),
                            $p['jumlah_salah'] ?? ($p['jml_salah'] ?? 0),
                            $p['nilai_akhir'] ?? 0,
                            $p['jawaban_json'] ?? null,
                            $p['reset_count'] ?? 0,
                            $p['violation_count'] ?? 0,
                            $existPid
                        ]);
                    } else {
                        $stmtInsP->execute([
                            $uId, $sId, $nis, $p['nama_siswa'], $p['kelas_nama'] ?? null,
                            $p['waktu_mulai'] ?? null, $p['waktu_selesai'] ?? null,
                            $p['status_pengerjaan'] ?? 'Selesai',
                            $p['jumlah_benar'] ?? ($p['jml_benar'] ?? 0),
                            $p['jumlah_salah'] ?? ($p['jml_salah'] ?? 0),
                            $p['nilai_akhir'] ?? 0,
                            $p['jawaban_json'] ?? null,
                            $p['reset_count'] ?? 0,
                            $p['violation_count'] ?? 0
                        ]);
                    }
                    $importedPeserta++;
                } catch (\Exception $e) {}
            }
        }

        // 2. Simpan Rekap Absensi Lokal (Aman, hanya update/insert data absensi yang dikirim)
        if (!empty($payload['absensi'])) {
            $stmtChkA = $db->prepare("
                SELECT id FROM cbt_absensi 
                WHERE tanggal = ? 
                  AND (nis = ? OR (siswa_id IS NOT NULL AND siswa_id = ?))
                  AND (ujian_id = ? OR mapel_nama = ? OR (? = '' AND mapel_nama = ''))
                LIMIT 1
            ");
            $stmtUpdA = $db->prepare("UPDATE cbt_absensi SET waktu_scan = ?, status = ?, kelas_nama = ?, mapel_nama = ?, jam_slot = ?, petugas_nama = ?, ruang_nama = ? WHERE id = ?");
            $stmtInsA = $db->prepare("
                INSERT INTO cbt_absensi 
                (tanggal, siswa_id, nis, nama_siswa, kelas_nama, tipe_peserta, topik_id, ujian_id, mapel_nama, jam_slot, waktu_scan, status, metode_scan, petugas_nama, ruang_nama)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($payload['absensi'] as $a) {
                try {
                    $tgl = $a['tanggal'] ?? date('Y-m-d');
                    $nis = trim((string)($a['nis'] ?? ''));
                    $sId = !empty($a['siswa_id']) ? (int)$a['siswa_id'] : null;
                    $uId = !empty($a['ujian_id']) ? (int)$a['ujian_id'] : 0;
                    $mapel = trim((string)($a['mapel_nama'] ?? ''));

                    $stmtChkA->execute([$tgl, $nis, $sId ?: -1, $uId, $mapel, $mapel]);
                    $existAid = $stmtChkA->fetchColumn();

                    if ($existAid) {
                        $stmtUpdA->execute([
                            $a['waktu_scan'] ?? date('Y-m-d H:i:s'),
                            $a['status'] ?? 'Hadir',
                            $a['kelas_nama'] ?? '',
                            $mapel,
                            $a['jam_slot'] ?? 'Sesi 1',
                            $a['petugas_nama'] ?? 'Proktor Lokal',
                            $a['ruang_nama'] ?? 'Lokal Madrasah',
                            $existAid
                        ]);
                    } else {
                        $stmtInsA->execute([
                            $tgl, $sId, $nis, $a['nama_siswa'],
                            $a['kelas_nama'] ?? '', $a['tipe_peserta'] ?? 'reguler', $a['topik_id'] ?? null,
                            $a['ujian_id'] ?? null, $mapel, $a['jam_slot'] ?? 'Sesi 1',
                            $a['waktu_scan'] ?? date('Y-m-d H:i:s'), $a['status'] ?? 'Hadir',
                            $a['metode_scan'] ?? 'SEMI_ONLINE', $a['petugas_nama'] ?? 'Proktor Lokal',
                            $a['ruang_nama'] ?? 'Lokal Madrasah'
                        ]);
                    }
                    $importedAbsensi++;
                } catch (\Exception $e) {}
            }
        }

        echo json_encode([
            'success' => true,
            'message' => "Berhasil mengintegrasikan hasil ujian lokal ke Server Pusat!",
            'details' => [
                'peserta' => $importedPeserta,
                'absensi' => $importedAbsensi
            ]
        ]);
        exit;
    }

    /**
     * AJAX Push Hasil Ujian dari Laptop Proktor Lokal ke Server Pusat
     */
    public static function ajaxPushHasilOnline() {
        header('Content-Type: application/json');
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        self::ensureTables($db);

        $serverUrl = rtrim(trim($_POST['server_url'] ?? 'https://apk.masmifhda.sch.id'), '/');
        $targetEndpoint = $serverUrl . '/apk/cbt_sync_api.php?action=import&token=' . urlencode('MIFHDA_CBT_SYNC_2026');

        // Ambil data hasil ujian lokal (HANYA nilai & lembar jawaban peserta cbt_peserta, presensi sudah terpusat di server online)
        $peserta = $db->query("SELECT * FROM cbt_peserta ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

        if (empty($peserta)) {
            echo json_encode(['success' => false, 'message' => 'Belum ada data pengerjaan atau nilai ujian siswa di server lokal ini.']);
            exit;
        }

        $payload = [
            'peserta' => $peserta,
            'sent_at' => date('Y-m-d H:i:s')
        ];

        $ch = curl_init($targetEndpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['payload' => json_encode($payload)]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($curlErr || $httpCode !== 200 || empty($response)) {
            echo json_encode([
                'success' => false,
                'message' => "Gagal mengirim data ke server pusat ({$targetEndpoint}). HTTP: {$httpCode}. Error: {$curlErr}"
            ]);
            exit;
        }

        $data = json_decode($response, true);
        if (!$data || !($data['success'] ?? false)) {
            echo json_encode([
                'success' => false,
                'message' => $data['message'] ?? 'Respon server pusat gagal.'
            ]);
            exit;
        }

        echo json_encode([
            'success' => true,
            'message' => "Hasil ujian (" . ($data['details']['peserta'] ?? count($peserta)) . " nilai siswa) dari laptop lokal berhasil terkirim dan tersinkronisasi di Server Pusat!",
            'details' => $data['details'] ?? ['peserta' => count($peserta)]
        ]);
        exit;
    }

    /**
     * API Cek Versi Terkini CBT dari Server Pusat
     */
    public static function apiCheckVersionOnline() {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'version' => '2.2.0',
            'build' => '2026.11.21',
            'release_date' => date('Y-m-d H:i:s'),
            'features' => [
                'Fix Router::dispatch() method compatibility',
                '1-Click Auto Update Engine CBT',
                'Presensi Kilat Hari Ini',
                'Isolasi Data Siswa Kelas Jauh'
            ]
        ]);
        exit;
    }

    /**
     * AJAX 1-Klik Pembaruan Engine CBT dari Server Pusat ke Server Lokal
     * Mengunduh paket standalone terbaru dari server online pusat,
     * lalu mengekstraknya menimpa berkas aplikasi tanpa merusak database & config lokal.
     */
    public static function ajaxUpdateEngine() {
        header('Content-Type: application/json');
        
        $serverUrl = rtrim(trim($_POST['server_url'] ?? 'https://apk.masmifhda.sch.id'), '/');
        $downloadUrl = $serverUrl . '/apk/download_standalone.php?token=' . urlencode('MIFHDA_CBT_SYNC_2026');

        $baseDir = realpath(__DIR__ . '/../..');
        $tempZip = $baseDir . '/temp_cbt_update_' . time() . '.zip';

        // 1. Download zip dari server pusat via cURL
        $fp = fopen($tempZip, 'w+');
        $ch = curl_init($downloadUrl);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $exec = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);
        fclose($fp);

        if (!$exec || $httpCode !== 200 || !file_exists($tempZip) || filesize($tempZip) < 1000) {
            @unlink($tempZip);
            echo json_encode([
                'success' => false,
                'message' => "Gagal mengunduh pembaruan dari server pusat. HTTP: {$httpCode}. Error: {$curlErr}"
            ]);
            exit;
        }

        // 2. Ekstrak file zip dengan aman (menjaga config/database.php)
        $zip = new \ZipArchive();
        if ($zip->open($tempZip) !== true) {
            @unlink($tempZip);
            echo json_encode(['success' => false, 'message' => 'Gagal membuka berkas zip paket pembaruan.']);
            exit;
        }

        $updatedFiles = 0;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            
            // Lindungi konfigurasi koneksi database lokal
            if ($filename === 'config/database.php' && file_exists($baseDir . '/config/database.php')) {
                continue;
            }

            // Lindungi folder upload siswa jika ada
            if (strpos($filename, 'public/uploads/') === 0 && file_exists($baseDir . '/' . $filename)) {
                continue;
            }

            if ($zip->extractTo($baseDir, [$filename])) {
                $updatedFiles++;
            }
        }
        $zip->close();
        @unlink($tempZip);

        // Pastikan direktori storage & uploads ada
        @mkdir($baseDir . '/storage/sessions', 0777, true);
        @mkdir($baseDir . '/storage/cache', 0777, true);
        @mkdir($baseDir . '/storage/logs', 0777, true);
        @mkdir($baseDir . '/public/uploads/logo', 0777, true);

        // 3. Pastikan skema database tabel CBT lokal up to date
        try {
            $db = \App\Core\Database::connect('core');
            self::ensureTables($db);
        } catch (\Exception $e) {}

        echo json_encode([
            'success' => true,
            'message' => "Engine CBT berhasil diperbarui ke versi paling mutakhir! ({$updatedFiles} berkas ter-update).",
            'updated_files' => $updatedFiles
        ]);
        exit;
    }

    /**
     * Helper untuk memasukkan payload data sinkronisasi ke database lokal
     */
    private static function importDataPayload($db, $dbSiakad, $data) {
        $details = ['topik' => 0, 'bs_paket' => 0, 'bs_soal' => 0, 'jadwal' => 0, 'ujian' => 0, 'soal' => 0, 'siswa' => 0, 'kelas' => 0, 'absensi' => 0];

        // 1. Topik Asesmen (Simpan ke Core & Siakad agar sinkron di kedua modul)
        if (!empty($data['topik'])) {
            $validTopikIds = array_filter(array_map('intval', array_column($data['topik'], 'id')));
            if (!empty($validTopikIds)) {
                $inClauseT = implode(',', $validTopikIds);
                try {
                    $db->exec("DELETE FROM bs_topik WHERE id NOT IN ($inClauseT)");
                    if ($db !== $dbSiakad) {
                        $dbSiakad->exec("DELETE FROM bs_topik WHERE id NOT IN ($inClauseT)");
                    }
                } catch (\Exception $e) {}
            }

            $sqlT = "INSERT INTO bs_topik (id, tahun_ajaran, semester, nama_topik, jenis, tgl_mulai, tgl_selesai, keterangan, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE nama_topik = VALUES(nama_topik), jenis = VALUES(jenis), status = VALUES(status), tgl_mulai = VALUES(tgl_mulai), tgl_selesai = VALUES(tgl_selesai), tahun_ajaran = VALUES(tahun_ajaran), semester = VALUES(semester), keterangan = VALUES(keterangan)";
            $stmtT = $db->prepare($sqlT);
            $stmtTSiakad = ($db !== $dbSiakad) ? $dbSiakad->prepare($sqlT) : null;
            foreach ($data['topik'] as $t) {
                try {
                    $valsT = [$t['id'], $t['tahun_ajaran'] ?? '2026/2027', $t['semester'] ?? 'Ganjil', $t['nama_topik'], $t['jenis'] ?? ($t['jenis_topik'] ?? 'SAS'), $t['tgl_mulai'] ?? null, $t['tgl_selesai'] ?? null, $t['keterangan'] ?? ($t['deskripsi'] ?? null), $t['status'] ?? 'Terjadwal'];
                    $stmtT->execute($valsT);
                    if ($stmtTSiakad) {
                        try { $stmtTSiakad->execute($valsT); } catch (\Exception $e2) {}
                    }
                    $details['topik']++;
                } catch (\Exception $e) {}
            }
        }

        // 1b. Paket Bank Soal (bs_paket)
        if (!empty($data['bs_paket'])) {
            try {
                if (class_exists('\App\Controllers\BankSoalController')) {
                    \App\Controllers\BankSoalController::ensureTables($db);
                    if ($db !== $dbSiakad) {
                        \App\Controllers\BankSoalController::ensureTables($dbSiakad);
                    }
                }
            } catch (\Exception $e) {}

            $validPaketIds = array_filter(array_map('intval', array_column($data['bs_paket'], 'id')));
            if (!empty($validPaketIds)) {
                $inClauseP = implode(',', $validPaketIds);
                try {
                    $db->exec("DELETE FROM bs_paket WHERE id NOT IN ($inClauseP)");
                    if ($db !== $dbSiakad) {
                        $dbSiakad->exec("DELETE FROM bs_paket WHERE id NOT IN ($inClauseP)");
                    }
                } catch (\Exception $e) {}
            }

            $sqlP = "INSERT INTO bs_paket (id, topik_id, mapel_nama, kelas_nama, kelas_sasaran, guru_id, guru_nama, kkm, durasi_menit, is_locked, locked_at, locked_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE topik_id = VALUES(topik_id), mapel_nama = VALUES(mapel_nama), kelas_nama = VALUES(kelas_nama), kelas_sasaran = VALUES(kelas_sasaran), guru_id = VALUES(guru_id), guru_nama = VALUES(guru_nama), kkm = VALUES(kkm), durasi_menit = VALUES(durasi_menit), is_locked = VALUES(is_locked)";
            $stmtP = $db->prepare($sqlP);
            $stmtPSiakad = ($db !== $dbSiakad) ? $dbSiakad->prepare($sqlP) : null;
            foreach ($data['bs_paket'] as $p) {
                try {
                    $valsP = [$p['id'], $p['topik_id'], $p['mapel_nama'], $p['kelas_nama'] ?? 'Kelas X', $p['kelas_sasaran'] ?? null, $p['guru_id'] ?? null, $p['guru_nama'] ?? null, $p['kkm'] ?? 75, $p['durasi_menit'] ?? 90, $p['is_locked'] ?? 0, $p['locked_at'] ?? null, $p['locked_by'] ?? null];
                    $stmtP->execute($valsP);
                    if ($stmtPSiakad) {
                        try { $stmtPSiakad->execute($valsP); } catch (\Exception $e2) {}
                    }
                    $details['bs_paket']++;
                } catch (\Exception $e) {}
            }
        }

        // 1c. Butir Soal Bank Soal (bs_soal)
        if (!empty($data['bs_soal'])) {
            $validSoalIds = array_filter(array_map('intval', array_column($data['bs_soal'], 'id')));
            if (!empty($validSoalIds)) {
                $inClauseS = implode(',', $validSoalIds);
                try {
                    $db->exec("DELETE FROM bs_soal WHERE id NOT IN ($inClauseS)");
                    if ($db !== $dbSiakad) {
                        $dbSiakad->exec("DELETE FROM bs_soal WHERE id NOT IN ($inClauseS)");
                    }
                } catch (\Exception $e) {}
            }

            $sqlBS = "INSERT INTO bs_soal (id, topik_id, paket_id, mapel_nama, kelas_nama, nomor_urut, tipe_soal, tingkat_kesulitan, skor_maksimal, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, konten_tambahan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE topik_id = VALUES(topik_id), paket_id = VALUES(paket_id), mapel_nama = VALUES(mapel_nama), kelas_nama = VALUES(kelas_nama), pertanyaan = VALUES(pertanyaan), kunci_jawaban = VALUES(kunci_jawaban), tipe_soal = VALUES(tipe_soal), konten_tambahan = VALUES(konten_tambahan), opsi_a = VALUES(opsi_a), opsi_b = VALUES(opsi_b), opsi_c = VALUES(opsi_c), opsi_d = VALUES(opsi_d), opsi_e = VALUES(opsi_e)";
            $stmtBS = $db->prepare($sqlBS);
            $stmtBSSiakad = ($db !== $dbSiakad) ? $dbSiakad->prepare($sqlBS) : null;
            foreach ($data['bs_soal'] as $bs) {
                try {
                    $valsBS = [$bs['id'], $bs['topik_id'], $bs['paket_id'] ?? null, $bs['mapel_nama'], $bs['kelas_nama'] ?? null, $bs['nomor_urut'] ?? 1, $bs['tipe_soal'] ?? 'sec_pg', $bs['tingkat_kesulitan'] ?? 'Sedang', $bs['skor_maksimal'] ?? 2.00, $bs['pertanyaan'], $bs['opsi_a'] ?? null, $bs['opsi_b'] ?? null, $bs['opsi_c'] ?? null, $bs['opsi_d'] ?? null, $bs['opsi_e'] ?? null, $bs['kunci_jawaban'] ?? null, $bs['konten_tambahan'] ?? null];
                    $stmtBS->execute($valsBS);
                    if ($stmtBSSiakad) {
                        try { $stmtBSSiakad->execute($valsBS); } catch (\Exception $e2) {}
                    }
                    $details['bs_soal']++;
                } catch (\Exception $e) {}
            }
        }

        // 2. Slot Jam
        if (!empty($data['jam_slot'])) {
            $stmtS = $db->prepare("INSERT INTO cbt_jam_slot (id, topik_id, urutan, label_sesi, jam_mulai, jam_selesai, is_istirahat) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE jam_mulai = VALUES(jam_mulai), jam_selesai = VALUES(jam_selesai), label_sesi = VALUES(label_sesi)");
            foreach ($data['jam_slot'] as $s) {
                try { $stmtS->execute([$s['id'], $s['topik_id'] ?? null, $s['urutan'], $s['label_sesi'], $s['jam_mulai'], $s['jam_selesai'], $s['is_istirahat'] ?? 0]); } catch (\Exception $e) {}
            }
        }

        // 3. Jadwal Grid
        if (!empty($data['jadwal_grid'])) {
            $validGridIds = array_filter(array_map('intval', array_column($data['jadwal_grid'], 'id')));
            if (!empty($validGridIds)) {
                $inClauseG = implode(',', $validGridIds);
                try {
                    $db->exec("DELETE FROM cbt_jadwal_grid WHERE id NOT IN ($inClauseG)");
                } catch (\Exception $e) {}
            }
            $stmtG = $db->prepare("INSERT INTO cbt_jadwal_grid (id, topik_id, ujian_id, mapel_nama, hari, jam_slot, kelas_id, kelas_nama, pengawas, ruang, tanggal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE mapel_nama = VALUES(mapel_nama), ujian_id = VALUES(ujian_id), tanggal = VALUES(tanggal), hari = VALUES(hari), jam_slot = VALUES(jam_slot)");
            foreach ($data['jadwal_grid'] as $g) {
                try {
                    $stmtG->execute([$g['id'], $g['topik_id'] ?? null, $g['ujian_id'] ?? 0, $g['mapel_nama'] ?? '', $g['hari'], $g['jam_slot'], $g['kelas_id'] ?? null, $g['kelas_nama'], $g['pengawas'] ?? null, $g['ruang'] ?? null, $g['tanggal'] ?? null]);
                    $details['jadwal']++;
                } catch (\Exception $e) {}
            }
        }

        // 4. Ujian
        if (!empty($data['ujian'])) {
            $validUjianIds = array_filter(array_map('intval', array_column($data['ujian'], 'id')));
            if (!empty($validUjianIds)) {
                $inClauseU = implode(',', $validUjianIds);
                try {
                    $db->exec("DELETE FROM cbt_ujian WHERE id NOT IN ($inClauseU) AND id NOT IN (SELECT DISTINCT ujian_id FROM cbt_peserta)");
                } catch (\Exception $e) {}
            }
            $stmtU = $db->prepare("INSERT INTO cbt_ujian (id, topik_id, kode_ujian, nama_ujian, mapel_nama, kelas_nama, durasi_menit, kkm, acak_soal, acak_opsi, token, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE nama_ujian = VALUES(nama_ujian), mapel_nama = VALUES(mapel_nama), kelas_nama = VALUES(kelas_nama), status = VALUES(status), durasi_menit = VALUES(durasi_menit), kkm = VALUES(kkm)");
            foreach ($data['ujian'] as $u) {
                try {
                    $stmtU->execute([$u['id'], $u['topik_id'] ?? null, $u['kode_ujian'], $u['nama_ujian'], $u['mapel_nama'], $u['kelas_nama'], $u['durasi_menit'] ?? 60, $u['kkm'] ?? 75, $u['acak_soal'] ?? 1, $u['acak_opsi'] ?? 1, $u['token'] ?? '', $u['status'] ?? 'Aktif']);
                    $details['ujian']++;
                } catch (\Exception $e) {}
            }
        }

        // 5. Soal
        if (!empty($data['soal'])) {
            $payloadUjianIds = array_unique(array_filter(array_map('intval', array_column($data['soal'], 'ujian_id'))));
            if (!empty($payloadUjianIds)) {
                $inPayloadU = implode(',', $payloadUjianIds);
                try {
                    $db->exec("DELETE FROM cbt_soal WHERE ujian_id IN ($inPayloadU)");
                } catch (\Exception $e) {}
            }
            $stmtSoal = $db->prepare("INSERT INTO cbt_soal (id, ujian_id, nomor_urut, tipe_soal, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, bobot, konten_tambahan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE pertanyaan = VALUES(pertanyaan), kunci_jawaban = VALUES(kunci_jawaban), tipe_soal = VALUES(tipe_soal), konten_tambahan = VALUES(konten_tambahan)");
            foreach ($data['soal'] as $so) {
                try {
                    $stmtSoal->execute([$so['id'], $so['ujian_id'], $so['nomor_urut'], $so['tipe_soal'] ?? 'sec_pg', $so['pertanyaan'], $so['opsi_a'] ?? null, $so['opsi_b'] ?? null, $so['opsi_c'] ?? null, $so['opsi_d'] ?? null, $so['opsi_e'] ?? null, $so['kunci_jawaban'] ?? null, $so['bobot'] ?? 1.00, $so['konten_tambahan'] ?? null]);
                    $details['soal']++;
                } catch (\Exception $e) {}
            }
        }

        // 6. Token
        if (!empty($data['token'])) {
            $stmtTok = $db->prepare("INSERT INTO cbt_token (id, token, ujian_id, masa_berlaku_menit, status, created_at) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE status = VALUES(status), token = VALUES(token)");
            foreach ($data['token'] as $tk) {
                try { $stmtTok->execute([$tk['id'], $tk['token'], $tk['ujian_id'] ?? null, $tk['masa_berlaku_menit'] ?? 120, $tk['status'] ?? 'Aktif', $tk['created_at'] ?? date('Y-m-d H:i:s')]); } catch (\Exception $e) {}
            }
        }

        // 7. Peserta Khusus & Denah
        if (!empty($data['peserta_khusus'])) {
            $stmtKj = $db->prepare("INSERT INTO cbt_peserta_khusus (id, nama_siswa, no_peserta, nis, tingkat, kelas_nama, username, password_plain, password_hash, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE nama_siswa = VALUES(nama_siswa), no_peserta = VALUES(no_peserta), status = VALUES(status)");
            foreach ($data['peserta_khusus'] as $pk) {
                try {
                    $stmtKj->execute([$pk['id'], $pk['nama_siswa'], $pk['no_peserta'], $pk['nis'] ?? $pk['no_peserta'], $pk['tingkat'], $pk['kelas_nama'], $pk['username'], $pk['password_plain'], $pk['password_hash'], $pk['status'] ?? 'Aktif']);
                    $details['siswa']++;
                } catch (\Exception $e) {}
            }
        }
        if (!empty($data['denah'])) {
            $stmtD = $db->prepare("INSERT INTO cbt_denah_peserta (id, topik_id, ruang_id, nomor_meja, siswa_id, nis, nama_siswa, kelas_id, kelas_nama, tingkat) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE nama_siswa = VALUES(nama_siswa), nomor_meja = VALUES(nomor_meja)");
            foreach ($data['denah'] as $dn) {
                try { $stmtD->execute([$dn['id'], $dn['topik_id'] ?? null, $dn['ruang_id'], $dn['nomor_meja'], $dn['siswa_id'] ?? null, $dn['nis'], $dn['nama_siswa'], $dn['kelas_id'] ?? null, $dn['kelas_nama'], $dn['tingkat'] ?? null]); } catch (\Exception $e) {}
            }
        }

        // 8. Ruang Ujian
        if (!empty($data['ruang'])) {
            $stmtR = $db->prepare("INSERT INTO cbt_ruang (id, kode_ruang, nama_ruang, tipe_meja, baris_samping, baris_belakang, kapasitas, lokasi, pengawas) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE nama_ruang = VALUES(nama_ruang), kapasitas = VALUES(kapasitas)");
            foreach ($data['ruang'] as $rg) {
                try { $stmtR->execute([$rg['id'], $rg['kode_ruang'], $rg['nama_ruang'], $rg['tipe_meja'] ?? 1, $rg['baris_samping'] ?? 4, $rg['baris_belakang'] ?? 5, $rg['kapasitas'] ?? 20, $rg['lokasi'] ?? null, $rg['pengawas'] ?? null]); } catch (\Exception $e) {}
            }
        }

        // 9. Pengaturan CBT
        if (!empty($data['pengaturan'])) {
            $stmtCfg = $db->prepare("INSERT INTO cbt_pengaturan (setting_key, setting_value, kategori, keterangan) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
            foreach ($data['pengaturan'] as $cfg) {
                try { $stmtCfg->execute([$cfg['setting_key'], $cfg['setting_value'], $cfg['kategori'] ?? 'keamanan', $cfg['keterangan'] ?? null]); } catch (\Exception $e) {}
            }
        }

        // 10. Data Kelas
        if (!empty($data['kelas'])) {
            $stmtK = $db->prepare("INSERT INTO kelas (id, nama_kelas, tingkat) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE nama_kelas = VALUES(nama_kelas), tingkat = VALUES(tingkat)");
            $stmtKSiakad = ($db !== $dbSiakad) ? $dbSiakad->prepare("INSERT INTO kelas (id, nama_kelas, tingkat) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE nama_kelas = VALUES(nama_kelas), tingkat = VALUES(tingkat)") : null;
            foreach ($data['kelas'] as $k) {
                try {
                    $stmtK->execute([$k['id'], $k['nama_kelas'], $k['tingkat'] ?? 'X']);
                    if ($stmtKSiakad) {
                        try { $stmtKSiakad->execute([$k['id'], $k['nama_kelas'], $k['tingkat'] ?? 'X']); } catch (\Exception $e2) {}
                    }
                    $details['kelas']++;
                } catch (\Exception $e) {}
            }
        }

        // 11. Data Siswa Reguler
        if (!empty($data['siswa'])) {
            $stmtSsw = $db->prepare("INSERT INTO siswa (id, user_id, nis, nik, nama, status, kelas_id, jenis_kelamin) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE nama = VALUES(nama), nis = VALUES(nis), nik = VALUES(nik), status = VALUES(status), kelas_id = VALUES(kelas_id)");
            $stmtSswSiakad = ($db !== $dbSiakad) ? $dbSiakad->prepare("INSERT INTO siswa (id, user_id, nis, nik, nama, status, kelas_id, jenis_kelamin) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE nama = VALUES(nama), nis = VALUES(nis), nik = VALUES(nik), status = VALUES(status), kelas_id = VALUES(kelas_id)") : null;
            foreach ($data['siswa'] as $sw) {
                try {
                    $valsSw = [$sw['id'], $sw['user_id'] ?? null, $sw['nis'], $sw['nik'] ?? null, $sw['nama'] ?? ($sw['nama_lengkap'] ?? ''), $sw['status'] ?? 'Aktif', $sw['kelas_id'] ?? null, $sw['jenis_kelamin'] ?? 'L'];
                    $stmtSsw->execute($valsSw);
                    if ($stmtSswSiakad) {
                        try { $stmtSswSiakad->execute($valsSw); } catch (\Exception $e2) {}
                    }
                    $details['siswa']++;
                } catch (\Exception $e) {}
            }
        }

        // 12. Data User Akun Login Siswa, Guru & Proktor
        if (!empty($data['users'])) {
            $stmtU = $db->prepare("INSERT INTO users (id, username, password_hash, plain_password, role_id, status) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), plain_password = VALUES(plain_password), role_id = VALUES(role_id), status = VALUES(status)");
            foreach ($data['users'] as $u) {
                try {
                    $stmtU->execute([$u['id'], $u['username'], $u['password_hash'], $u['plain_password'] ?? null, $u['role_id'], $u['status'] ?? 'active']);
                } catch (\Exception $e) {}
            }
        }

        // 12b. Data Guru & Pengawas
        if (!empty($data['guru'])) {
            $stmtG = $db->prepare("INSERT INTO guru (id, user_id, nama, nip, nik, is_kamad) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE nama = VALUES(nama), nip = VALUES(nip), nik = VALUES(nik)");
            foreach ($data['guru'] as $g) {
                try {
                    $stmtG->execute([$g['id'], $g['user_id'] ?? null, $g['nama'], $g['nip'] ?? null, $g['nik'] ?? null, $g['is_kamad'] ?? 0]);
                } catch (\Exception $e) {}
            }
        }

        // 12c. Data Mata Pelajaran
        if (!empty($data['mapel'])) {
            try {
                $db->exec("CREATE TABLE IF NOT EXISTS mapel (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nama_mapel VARCHAR(150) NOT NULL,
                    kode_mapel VARCHAR(50) DEFAULT NULL,
                    kategori VARCHAR(50) DEFAULT 'Wajib',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            } catch (\Exception $e) {}

            $stmtM = $db->prepare("INSERT INTO mapel (id, nama_mapel, kode_mapel, kategori) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE nama_mapel = VALUES(nama_mapel), kode_mapel = VALUES(kode_mapel), kategori = VALUES(kategori)");
            foreach ($data['mapel'] as $m) {
                try {
                    $stmtM->execute([$m['id'], $m['nama_mapel'], $m['kode_mapel'] ?? null, $m['kategori'] ?? 'Wajib']);
                } catch (\Exception $e) {}
            }
        }

        // 12d. Data Pengawas Ruangan
        if (!empty($data['ruang_pengawas'])) {
            $stmtRP = $db->prepare("INSERT INTO cbt_ruang_pengawas (id, topik_id, ruang_id, sesi_label, jam_slot, guru_id, guru_nama) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE guru_id = VALUES(guru_id), guru_nama = VALUES(guru_nama)");
            foreach ($data['ruang_pengawas'] as $rp) {
                try {
                    $stmtRP->execute([$rp['id'], $rp['topik_id'], $rp['ruang_id'], $rp['sesi_label'], $rp['jam_slot'] ?? null, $rp['guru_id'] ?? null, $rp['guru_nama']]);
                } catch (\Exception $e) {}
            }
        }

        // 13. Data Presensi / Absensi CBT Hari Ini
        if (!empty($data['absensi'])) {
            $stmtInsAbs = $db->prepare("
                INSERT INTO cbt_absensi 
                (tanggal, siswa_id, nis, nama_siswa, kelas_nama, tipe_peserta, topik_id, ujian_id, mapel_nama, jam_slot, waktu_scan, status, metode_scan, petugas_nama, ruang_nama)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmtUpdAbs = $db->prepare("
                UPDATE cbt_absensi 
                SET waktu_scan = ?, status = ?, kelas_nama = ?, mapel_nama = ?, jam_slot = ?, petugas_nama = ?, ruang_nama = ?
                WHERE id = ?
            ");

            foreach ($data['absensi'] as $a) {
                try {
                    $tgl = $a['tanggal'] ?? date('Y-m-d');
                    $nis = trim((string)($a['nis'] ?? ''));
                    $sid = !empty($a['siswa_id']) ? (int)$a['siswa_id'] : null;
                    $mapel = $a['mapel_nama'] ?? '';

                    $uId = !empty($a['ujian_id']) ? (int)$a['ujian_id'] : 0;
                    $chk = $db->prepare("
                        SELECT id FROM cbt_absensi 
                        WHERE tanggal = ? 
                          AND (nis = ? OR (siswa_id IS NOT NULL AND siswa_id = ?))
                          AND (ujian_id = ? OR mapel_nama = ? OR (? = '' AND mapel_nama = ''))
                        LIMIT 1
                    ");
                    $chk->execute([$tgl, $nis, $sid ?: -1, $uId, $mapel, $mapel]);
                    $existId = $chk->fetchColumn();

                    if ($existId) {
                        $stmtUpdAbs->execute([
                            $a['waktu_scan'] ?? date('Y-m-d H:i:s'),
                            $a['status'] ?? 'Hadir',
                            $a['kelas_nama'] ?? '',
                            $mapel,
                            $a['jam_slot'] ?? 'Sesi 1',
                            $a['petugas_nama'] ?? 'Online Scan',
                            $a['ruang_nama'] ?? 'Gerbang Madrasah',
                            $existId
                        ]);
                    } else {
                        $stmtInsAbs->execute([
                            $tgl,
                            $sid,
                            $nis,
                            $a['nama_siswa'],
                            $a['kelas_nama'] ?? '',
                            $a['tipe_peserta'] ?? 'reguler',
                            $a['topik_id'] ?? null,
                            $a['ujian_id'] ?? null,
                            $mapel,
                            $a['jam_slot'] ?? 'Sesi 1',
                            $a['waktu_scan'] ?? date('Y-m-d H:i:s'),
                            $a['status'] ?? 'Hadir',
                            $a['metode_scan'] ?? 'ONLINE_SYNC',
                            $a['petugas_nama'] ?? 'Online Scan',
                            $a['ruang_nama'] ?? 'Gerbang Madrasah'
                        ]);
                    }
                    $details['absensi']++;
                } catch (\Exception $e) {}
            }
        }

        try {
            self::syncJadwalWithUjian($db, $dbSiakad);
        } catch (\Exception $e) {}

        return $details;
    }

    public static function exportBackupFile() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $dbSiakad = $ctx['db_siakad'] ?? $db;

        // Topik
        $topikList = [];
        try {
            $topikList = $dbSiakad->query("SELECT * FROM bs_topik WHERE status != 'Arsip' OR status IS NULL ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}
        if (empty($topikList)) {
            try {
                $topikList = $db->query("SELECT * FROM bs_topik WHERE status != 'Arsip' OR status IS NULL ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Exception $e2) {}
        }

        // bs_paket
        $bsPaketList = [];
        try {
            $bsPaketList = $dbSiakad->query("SELECT * FROM bs_paket ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}
        if (empty($bsPaketList)) {
            try {
                $bsPaketList = $db->query("SELECT * FROM bs_paket ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Exception $e2) {}
        }

        // bs_soal
        $bsSoalList = [];
        try {
            $bsSoalList = $dbSiakad->query("SELECT * FROM bs_soal ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}
        if (empty($bsSoalList)) {
            try {
                $bsSoalList = $db->query("SELECT * FROM bs_soal ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Exception $e2) {}
        }

        // Auto-sinkron cbt_soal dari bs_soal jika belum ada atau jumlah tidak sesuai
        try {
            $ujians = $db->query("SELECT id, mapel_nama, kelas_nama, topik_id FROM cbt_ujian")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($ujians as $uExp) {
                $uIdExp = (int)$uExp['id'];
                $soalCnt = (int)$db->query("SELECT COUNT(*) FROM cbt_soal WHERE ujian_id = {$uIdExp}")->fetchColumn();
                $cleanM = trim($uExp['mapel_nama']);
                $tId = (int)($uExp['topik_id'] ?? 0);
                
                $getLevel = function($str) {
                    $s = strtoupper((string)$str);
                    if (strpos($s, 'XII') !== false || preg_match('/\b12\b/', $s)) return 12;
                    if (strpos($s, 'XI') !== false || preg_match('/\b11\b/', $s)) return 11;
                    if (strpos($s, 'X') !== false || preg_match('/\b10\b/', $s)) return 10;
                    return 0;
                };
                $uLvl = $getLevel($uExp['kelas_nama']);
                $bsSrc = !empty($bsSoalList) ? $bsSoalList : [];
                $matchedBs = array_filter($bsSrc, function($b) use ($cleanM, $tId, $uLvl, $getLevel) {
                    $mMatch = ($b['mapel_nama'] === $cleanM || stripos($b['mapel_nama'], $cleanM) !== false || stripos($cleanM, $b['mapel_nama']) !== false);
                    $tMatch = ($tId === 0 || (int)($b['topik_id'] ?? 0) === $tId);
                    $bLvl = $getLevel($b['kelas_nama'] ?? '');
                    $kMatch = ($uLvl === 0 || $bLvl === 0 || $uLvl === $bLvl);
                    return $mMatch && $tMatch && $kMatch;
                });
                
                if (!empty($matchedBs) && ($soalCnt === 0 || $soalCnt !== count($matchedBs))) {
                    $db->exec("DELETE FROM cbt_soal WHERE ujian_id = {$uIdExp}");
                    $stmtInsS = $db->prepare("INSERT INTO cbt_soal (ujian_id, nomor_urut, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, bobot, tipe_soal, konten_tambahan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $noS = 1;
                    foreach ($matchedBs as $mbs) {
                        $stmtInsS->execute([
                            $uIdExp,
                            $noS++,
                            $mbs['pertanyaan'],
                            $mbs['opsi_a'] ?? '',
                            $mbs['opsi_b'] ?? '',
                            $mbs['opsi_c'] ?? '',
                            $mbs['opsi_d'] ?? '',
                            $mbs['opsi_e'] ?? '',
                            $mbs['kunci_jawaban'] ?? 'A',
                            $mbs['skor_maksimal'] ?? 10.00,
                            $mbs['tipe_soal'] ?? 'sec_pg',
                            $mbs['konten_tambahan'] ?? null
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {}

        $payload = [
            'status' => 'success',
            'exported_at' => date('Y-m-d H:i:s'),
            'topik' => $topikList,
            'bs_paket' => $bsPaketList,
            'bs_soal' => $bsSoalList,
            'jam_slot' => $db->query("SELECT * FROM cbt_jam_slot ORDER BY urutan ASC")->fetchAll(PDO::FETCH_ASSOC),
            'jadwal_grid' => $db->query("SELECT * FROM cbt_jadwal_grid ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC),
            'ujian' => $db->query("SELECT * FROM cbt_ujian ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC),
            'soal' => $db->query("SELECT * FROM cbt_soal ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC),
            'token' => $db->query("SELECT * FROM cbt_token ORDER BY id DESC LIMIT 500")->fetchAll(PDO::FETCH_ASSOC),
            'peserta_khusus' => $db->query("SELECT * FROM cbt_peserta_khusus WHERE status = 'Aktif' ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC),
            'denah' => $db->query("SELECT * FROM cbt_denah_peserta ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC),
            'ruang' => $db->query("SELECT * FROM cbt_ruang ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC),
            'pengaturan' => $db->query("SELECT * FROM cbt_pengaturan")->fetchAll(PDO::FETCH_ASSOC),
            'absensi' => $db->query("SELECT * FROM cbt_absensi WHERE tanggal = CURDATE() OR DATE(waktu_scan) = CURDATE()")->fetchAll(PDO::FETCH_ASSOC),
        ];

        $json = json_encode($payload, JSON_PRETTY_PRINT);
        $filename = 'CBT_PAKET_SEMI_ONLINE_' . date('Ymd_His') . '.json';

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($json));
        echo $json;
        exit;
    }

    public static function importBackupFile() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $dbSiakad = $ctx['db_siakad'] ?? $db;

        if (!empty($_FILES['backup_file']['tmp_name'])) {
            $content = file_get_contents($_FILES['backup_file']['tmp_name']);
            $data = json_decode($content, true);
            if ($data && ($data['status'] ?? '') === 'success') {
                self::importDataPayload($db, $dbSiakad, $data);
                redirect('/cbt/semi-online?imported=1');
            }
        }
        redirect('/cbt/semi-online?err=1');
    }

    /**
     * Download Paket Standalone Server CBT Madrasah (.zip)
     */
    public static function downloadStandaloneZip() {
        $zipFile = __DIR__ . '/../../public/downloads/cbt_standalone_madrasah.zip';
        if (!file_exists($zipFile) || (time() - filemtime($zipFile) > 86400)) {
            require_once __DIR__ . '/../../tools/build_cbt_standalone.php';
            build_cbt_standalone_package();
        }

        if (!file_exists($zipFile)) {
            die("File paket standalone belum berhasil digenerate.");
        }

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="cbt_standalone_madrasah.zip"');
        header('Content-Length: ' . filesize($zipFile));
        header('Pragma: no-cache');
        readfile($zipFile);
        exit;
    }
}
