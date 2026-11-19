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

            try {
                $insertSql = "INSERT INTO bs_topik (jenis, nama_topik, keterangan, tahun_ajaran, semester, tgl_mulai, tgl_selesai, status) VALUES
                    ('SAS', 'Sumatif Akhir Semester (SAS) Ganjil 2026/2027', 'Asesmen Sumatif Akhir Semester Ganjil TA 2026/2027 seluruh tingkat kelas.', '2026/2027', 'Ganjil', '2026-12-01', '2026-12-08', 'Aktif'),
                    ('STS', 'Sumatif Tengah Semester (STS) Ganjil 2026/2027', 'Asesmen Sumatif Tengah Semester Ganjil TA 2026/2027.', '2026/2027', 'Ganjil', '2026-09-21', '2026-09-26', 'Selesai'),
                    ('UM', 'Ujian Madrasah (UM) 2026/2027', 'Ujian Akhir Madrasah Aliyah Miftahul Huda Mathla\'ul Anwar Tahun Pelajaran 2026/2027.', '2026/2027', 'Genap', '2027-03-15', '2027-03-22', 'Terjadwal'),
                    ('Try Out', 'Try Out Ujian Madrasah 2026/2027', 'Simulasi CBT dan pemantapan materi persiapan Ujian Madrasah.', '2026/2027', 'Genap', '2027-02-15', '2027-02-18', 'Terjadwal')
                ";
                $cntCore = (int)$db->query("SELECT COUNT(*) FROM bs_topik WHERE jenis = 'SAS'")->fetchColumn();
                if ($cntCore === 0) {
                    $db->exec($insertSql);
                }
                if (isset($dbS) && $dbS && $dbS !== $db) {
                    $cntSiakad = (int)$dbS->query("SELECT COUNT(*) FROM bs_topik WHERE jenis = 'SAS'")->fetchColumn();
                    if ($cntSiakad === 0) {
                        $dbS->exec($insertSql);
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
                ['lock_by_schedule_time', '1', 'aturan', 'Kunci Pengerjaan Sesuai Jadwal (Waktu Sesi)'],
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
                            'kode_mapel' => trim($r['kode_mapel'] ?? ''),
                            'kategori' => trim($r['kategori'] ?? 'Umum')
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
                            'kode_mapel' => trim($r['kode_mapel'] ?? ''),
                            'kategori' => trim($r['kategori'] ?? 'Umum')
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
                                $sqlUp = "UPDATE cbt_jadwal_grid SET topik_id = ?, mapel_nama = ?, kelas_id = ?, tanggal = COALESCE(?, tanggal), hari = ?";
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
            $topikList = $db_siakad->query("SELECT id, nama_topik, jenis, tahun_ajaran, semester, status, tgl_mulai, tgl_selesai FROM bs_topik WHERE status != 'Arsip' ORDER BY (status = 'Aktif') DESC, (tgl_mulai IS NOT NULL) DESC, tgl_mulai ASC, id DESC")->fetchAll(\PDO::FETCH_ASSOC);
            if (empty($topikList) && $db !== $db_siakad) {
                $topikList = $db->query("SELECT id, nama_topik, jenis, tahun_ajaran, semester, status, tgl_mulai, tgl_selesai FROM bs_topik WHERE status != 'Arsip' ORDER BY (status = 'Aktif') DESC, (tgl_mulai IS NOT NULL) DESC, tgl_mulai ASC, id DESC")->fetchAll(\PDO::FETCH_ASSOC);
            }
        } catch (\Exception $e) {
            try {
                $topikList = $db_siakad->query("SELECT * FROM bs_topik ORDER BY id DESC")->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\Exception $e2) {
                if ($db !== $db_siakad) {
                    try {
                        $topikList = $db->query("SELECT * FROM bs_topik ORDER BY id DESC")->fetchAll(\PDO::FETCH_ASSOC);
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
        return $topikList;
    }

    /**
     * Rekap Hasil Ujian Siswa Real
     */
    public static function hasil() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
        $db_siakad = $ctx['db_siakad'] ?? $db;
        self::ensureTables($db);
        self::syncJadwalWithUjian($db, $db_siakad);

        // Ambil daftar Topik Ujian aktif
        $topikList = self::getActiveTopikList($db, $db_siakad);

        $selectedTopikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0;
        $selectedMapel = isset($_GET['mapel']) ? trim($_GET['mapel']) : '';
        $selectedUjianId = isset($_GET['ujian_id']) ? (int)$_GET['ujian_id'] : 0;
        $selectedKelas = isset($_GET['kelas']) ? trim($_GET['kelas']) : '';
        $search = isset($_GET['q']) ? trim($_GET['q']) : '';
        $sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'nilai_desc';

        // Ambil daftar Mapel yang relevan (difilter per topik jika topik dipilih)
        $mapelList = [];
        try {
            if ($selectedTopikId > 0) {
                $stmtM = $db->prepare("SELECT DISTINCT mapel_nama FROM cbt_ujian WHERE topik_id = ? AND mapel_nama IS NOT NULL AND mapel_nama != '' ORDER BY mapel_nama ASC");
                $stmtM->execute([$selectedTopikId]);
                $mapelList = $stmtM->fetchAll(PDO::FETCH_COLUMN);
            } else {
                $mapelList = $db->query("SELECT DISTINCT mapel_nama FROM cbt_ujian WHERE mapel_nama IS NOT NULL AND mapel_nama != '' ORDER BY mapel_nama ASC")->fetchAll(PDO::FETCH_COLUMN);
            }
        } catch (\Exception $e) {}

        $ujianList = $db->query("SELECT id, kode_ujian, nama_ujian, mapel_nama, kkm FROM cbt_ujian ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

        // Daftar kelas real dari database kelas + kelas jauh
        $kelasList = [];
        try {
            $k1 = $db->query("SELECT DISTINCT nama_kelas FROM kelas WHERE nama_kelas IS NOT NULL AND nama_kelas != ''")->fetchAll(PDO::FETCH_COLUMN);
            $k2 = $db->query("SELECT DISTINCT kelas_nama FROM cbt_peserta_khusus WHERE kelas_nama IS NOT NULL AND kelas_nama != ''")->fetchAll(PDO::FETCH_COLUMN);
            $k3 = $db->query("SELECT DISTINCT kelas_nama FROM cbt_peserta WHERE kelas_nama IS NOT NULL AND kelas_nama != ''")->fetchAll(PDO::FETCH_COLUMN);
            $merged = array_unique(array_filter(array_merge($k1, $k2, $k3)));
            usort($merged, function($a, $b) {
                return strnatcasecmp($a, $b);
            });
            $kelasList = array_values($merged);
        } catch (\Exception $e) {}

        // Query Peserta Real
        $sql = "SELECT p.*, u.nama_ujian, u.kkm, u.mapel_nama 
                FROM cbt_peserta p 
                LEFT JOIN cbt_ujian u ON u.id = p.ujian_id 
                WHERE 1=1";
        $params = [];

        if ($selectedTopikId > 0) {
            $sql .= " AND (u.topik_id = :topik_id)";
            $params[':topik_id'] = $selectedTopikId;
        }

        if (!empty($selectedMapel)) {
            $sql .= " AND (u.mapel_nama = :mapel OR p.mapel_nama = :mapel2)";
            $params[':mapel'] = $selectedMapel;
            $params[':mapel2'] = $selectedMapel;
        }

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
                $sql .= " ORDER BY (p.waktu_selesai IS NOT NULL) DESC, p.waktu_selesai DESC, p.nilai_akhir DESC";
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

        $sql = "SELECT p.*, u.nama_ujian, u.kkm, u.mapel_nama, u.kode_ujian, u.durasi_menit 
                FROM cbt_peserta p 
                LEFT JOIN cbt_ujian u ON u.id = p.ujian_id 
                WHERE 1=1";
        $params = [];

        if ($selectedTopikId > 0) {
            $sql .= " AND (u.topik_id = :topik_id)";
            $params[':topik_id'] = $selectedTopikId;
        }

        if (!empty($selectedMapel)) {
            $sql .= " AND (u.mapel_nama = :mapel OR p.mapel_nama = :mapel2)";
            $params[':mapel'] = $selectedMapel;
            $params[':mapel2'] = $selectedMapel;
        }

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
                $sql .= " ORDER BY (p.waktu_selesai IS NOT NULL) DESC, p.waktu_selesai DESC, p.nilai_akhir DESC";
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
            if ($isCbtOnly || $roleId === 7) {
                $st = $db->prepare("SELECT kelas_nama, tingkat FROM cbt_peserta_khusus WHERE id = ? LIMIT 1");
                $st->execute([$siswaSessionId]);
                $cl = $st->fetch(PDO::FETCH_ASSOC);
                $studentClass = $cl['kelas_nama'] ?? '';
            } else {
                $st = $db->prepare("SELECT COALESCE(k.nama_kelas, '') as kelas_nama FROM siswa s LEFT JOIN kelas k ON s.kelas_id = k.id WHERE (s.user_id = ? AND ? > 0) OR (s.id = ? AND ? > 0) LIMIT 1");
                $st->execute([$userId, $userId, $siswaSessionId, $siswaSessionId]);
                $cl = $st->fetch(PDO::FETCH_ASSOC);
                $studentClass = $cl['kelas_nama'] ?? '';
            }

            // Ambil nama mapel ujian
            $stmtU = $db->prepare("SELECT mapel_nama, nama_ujian FROM cbt_ujian WHERE id = ? LIMIT 1");
            $stmtU->execute([$ujian_id]);
            $uRow = $stmtU->fetch(PDO::FETCH_ASSOC);
            $mapelNama = $uRow['mapel_nama'] ?? '';

            // Cek apakah ada jadwal spesifik untuk HARI INI
            $stmtToday = $db->prepare("
                SELECT g.*, s.jam_mulai, s.jam_selesai, s.label_sesi 
                FROM cbt_jadwal_grid g 
                LEFT JOIN cbt_jam_slot s ON (g.jam_slot = s.label_sesi OR g.jam_slot = s.urutan)
                WHERE (g.tanggal = :today OR (g.tanggal IS NULL AND g.hari = :curHari))
                  AND (
                      g.ujian_id = :uid 
                      OR (
                          (g.mapel_nama = :mapel OR :mapel2 LIKE CONCAT('%', g.mapel_nama, '%') OR g.mapel_nama LIKE CONCAT('%', :mapel3, '%'))
                          AND (
                              g.kelas_nama = :kelas 
                              OR :kelas2 LIKE CONCAT('%', g.kelas_nama, '%')
                              OR g.kelas_nama = 'Semua Kelas'
                              OR g.kelas_nama IS NULL
                              OR g.kelas_nama = ''
                          )
                      )
                  )
                ORDER BY (g.ujian_id = :uid2) DESC, (g.kelas_nama = :kelas3) DESC, g.id DESC LIMIT 1
            ");
            $stmtToday->execute([
                ':today' => $todayDate,
                ':curHari' => $curHari,
                ':uid' => $ujian_id,
                ':mapel' => $mapelNama,
                ':mapel2' => $mapelNama,
                ':mapel3' => $mapelNama,
                ':kelas' => $studentClass,
                ':kelas2' => $studentClass,
                ':uid2' => $ujian_id,
                ':kelas3' => $studentClass
            ]);
            $jGrid = $stmtToday->fetch(PDO::FETCH_ASSOC);

            if (!$jGrid) {
                // Tidak ada jadwal untuk hari ini!
                echo "<script>alert('Mata pelajaran ini tidak dijadwalkan pada hari ini! Silakan periksa kembali jadwal resmi.'); window.location.href='/apk/cbt.php';</script>";
                exit;
            }

            if (!empty($jGrid['jam_mulai'])) {
                $startTs = strtotime($todayDate . ' ' . $jGrid['jam_mulai']);
                if ($nowTs < $startTs) {
                    echo "<script>alert('Sesi ujian belum dibuka! Waktu mulai hari ini pukul " . $jGrid['jam_mulai'] . " WIB.'); window.location.href='/apk/cbt.php';</script>";
                    exit;
                }
            }

            if (!empty($jGrid['jam_selesai'])) {
                $endTs = strtotime($todayDate . ' ' . $jGrid['jam_selesai']);
                if ($nowTs > $endTs) {
                    echo "<script>alert('Waktu pelaksanaan sesi ujian ini telah berakhir pada pukul " . $jGrid['jam_selesai'] . " WIB! Anda tidak dapat mengerjakan di luar jam sesi resmi. Hubungi proktor jika Anda membutuhkan ujian susulan.'); window.location.href='/apk/cbt.php';</script>";
                    exit;
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
                    'show_score_to_student', 'show_discussion_to_student', 'allow_student_review'
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
        if (empty($inst['nama_kepala']) || stripos($inst['nama_kepala'], 'Ulfah') !== false) {
            $inst['nama_kepala'] = 'Drs. Hamim Hasyim';
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
        if (empty($inst['nama_kepala']) || stripos($inst['nama_kepala'], 'Ulfah') !== false) {
            $inst['nama_kepala'] = 'Drs. Hamim Hasyim';
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

        $ruangId = isset($_GET['ruang_id']) ? (int)$_GET['ruang_id'] : 0;
        $topikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0;
        $stmtR = $db->prepare("SELECT * FROM cbt_ruang WHERE id = ?");
        $stmtR->execute([$ruangId]);
        $ruang = $stmtR->fetch(PDO::FETCH_ASSOC);

        if (!$ruang) {
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
        }

        if ($topikId > 0) {
            $stmtMeja = $db->prepare("SELECT * FROM cbt_denah_peserta WHERE ruang_id = ? AND (topik_id = ? OR topik_id IS NULL) ORDER BY nomor_meja ASC");
            $stmtMeja->execute([$ruangId, $topikId]);
        } else {
            $stmtMeja = $db->prepare("SELECT * FROM cbt_denah_peserta WHERE ruang_id = ? ORDER BY nomor_meja ASC");
            $stmtMeja->execute([$ruangId]);
        }
        $mejaList = $stmtMeja->fetchAll(PDO::FETCH_ASSOC);

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

        $ruangId = isset($_GET['ruang_id']) ? (int)$_GET['ruang_id'] : 0;
        $topikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0;
        $stmtR = $db->prepare("SELECT * FROM cbt_ruang WHERE id = ?");
        $stmtR->execute([$ruangId]);
        $ruang = $stmtR->fetch(PDO::FETCH_ASSOC);

        if (!$ruang) {
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
        }

        if ($topikId > 0) {
            $stmtMeja = $db->prepare("SELECT * FROM cbt_denah_peserta WHERE ruang_id = ? AND (topik_id = ? OR topik_id IS NULL) AND siswa_id IS NOT NULL ORDER BY nomor_meja ASC");
            $stmtMeja->execute([$ruangId, $topikId]);
        } else {
            $stmtMeja = $db->prepare("SELECT * FROM cbt_denah_peserta WHERE ruang_id = ? AND siswa_id IS NOT NULL ORDER BY nomor_meja ASC");
            $stmtMeja->execute([$ruangId]);
        }
        $mejaList = $stmtMeja->fetchAll(PDO::FETCH_ASSOC);

        include __DIR__ . '/../../resources/views/cbt/cetak_label_meja.php';
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

        // Jadwal aktif hari ini
        $jadwalToday = $db->query("
            SELECT g.*, u.nama_ujian, u.kode_ujian, s.jam_mulai, s.jam_selesai, s.label_sesi
            FROM cbt_jadwal_grid g
            LEFT JOIN cbt_ujian u ON g.ujian_id = u.id
            LEFT JOIN cbt_jam_slot s ON (g.jam_slot = s.label_sesi OR g.jam_slot = s.urutan)
            WHERE g.tanggal = '{$todayDate}' OR (g.tanggal IS NULL AND g.hari = '" . self::getIndoDayName(date('w')) . "')
            ORDER BY s.jam_mulai ASC, g.kelas_nama ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Riwayat absensi hari ini
        $stmtRecent = $db->query("SELECT * FROM cbt_absensi WHERE tanggal = '{$todayDate}' ORDER BY waktu_scan DESC LIMIT 50");
        $recentAbsen = $stmtRecent ? $stmtRecent->fetchAll(PDO::FETCH_ASSOC) : [];

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
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];
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

        // 1a. Cari jika terdeteksi User ID / Siswa ID dari TOKEN Siakad (misal TOKEN:414:...)
        if ($extractedUserId) {
            $stmtS = $db->prepare("
                SELECT s.id, s.user_id, s.nis, s.nama, s.kelas_id, k.nama_kelas, k.tingkat, s.foto
                FROM siswa s
                LEFT JOIN kelas k ON s.kelas_id = k.id
                WHERE s.user_id = ? OR s.id = ?
                LIMIT 1
            ");
            $stmtS->execute([$extractedUserId, $extractedUserId]);
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
        }

        // 1b. Cari jika terdeteksi ID dari Kartu Kelas Jauh (misal CBT_KJ:1:...)
        if (!$siswa && $extractedKjId) {
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
        }

        // 1c. Cari di tabel siswa berdasarkan NIS, NISN, ID, user_id, username, atau qr_token users
        if (!$siswa) {
            $stmtS = $db->prepare("
                SELECT s.id, s.user_id, s.nis, s.nama, s.kelas_id, k.nama_kelas, k.tingkat, s.foto
                FROM siswa s
                LEFT JOIN kelas k ON s.kelas_id = k.id
                LEFT JOIN users u ON s.user_id = u.id
                WHERE s.nis = ? OR s.nisn = ? OR s.id = ? OR s.user_id = ? OR u.username = ? OR u.qr_token = ?
                   OR s.nis = ? OR s.id = ? OR u.qr_token = ?
                LIMIT 1
            ");
            $stmtS->execute([$cleanCode, $cleanCode, $cleanCode, $cleanCode, $cleanCode, $cleanCode, $rawCode, $rawCode, $rawCode]);
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
        }

        // 1d. Cari di tabel cbt_peserta_khusus (Kelas Jauh)
        if (!$siswa) {
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

        // Ambil seluruh jadwal ujian hari ini untuk kelas siswa tersebut
        $stmtJadwal = $db->prepare("
            SELECT g.*, u.nama_ujian, s.jam_mulai, s.jam_selesai, s.label_sesi
            FROM cbt_jadwal_grid g
            LEFT JOIN cbt_ujian u ON g.ujian_id = u.id
            LEFT JOIN cbt_jam_slot s ON (
                g.jam_slot = s.label_sesi 
                OR g.jam_slot = s.urutan 
                OR g.jam_slot = CONCAT(s.jam_mulai, ' - ', s.jam_selesai)
                OR g.jam_slot LIKE CONCAT('%', s.jam_mulai, '%')
            )
            WHERE (g.tanggal = ? OR (g.tanggal IS NULL AND g.hari = ?))
              AND (
                  g.kelas_nama = ? 
                  OR ? LIKE CONCAT('%', g.kelas_nama, '%') 
                  OR g.kelas_nama = 'Semua Kelas'
                  OR g.kelas_nama IS NULL
              )
            ORDER BY g.id ASC
        ");
        $stmtJadwal->execute([$todayDate, $curHari, $targetKelas, $targetKelas]);
        $allJadwalGrid = $stmtJadwal->fetchAll(PDO::FETCH_ASSOC);

        $activeJadwal = null;
        $upcomingJadwal = null;
        $allExpired = true;

        if (!empty($allJadwalGrid)) {
            foreach ($allJadwalGrid as $jg) {
                // Ekstraksi jam_mulai dan jam_selesai dengan cerdas
                $jMulai = !empty($jg['jam_mulai']) ? trim($jg['jam_mulai']) : null;
                $jSelesai = !empty($jg['jam_selesai']) ? trim($jg['jam_selesai']) : null;

                if ((empty($jMulai) || empty($jSelesai)) && !empty($jg['jam_slot'])) {
                    if (preg_match('/(\d{1,2}:\d{2})\s*[-–]\s*(\d{1,2}:\d{2})/', $jg['jam_slot'], $m)) {
                        $jMulai = sprintf('%05s', $m[1]);
                        $jSelesai = sprintf('%05s', $m[2]);
                    }
                }
                if (!empty($jMulai) && strlen($jMulai) === 5) $jMulai .= ':00';
                if (!empty($jSelesai) && strlen($jSelesai) === 5) $jSelesai .= ':00';

                $jg['parsed_jam_mulai'] = $jMulai;
                $jg['parsed_jam_selesai'] = $jSelesai;

                if (!empty($jMulai) && !empty($jSelesai)) {
                    if ($curTime >= $jMulai && $curTime <= $jSelesai) {
                        $activeJadwal = $jg;
                        $allExpired = false;
                        break;
                    } elseif ($curTime < $jMulai) {
                        $allExpired = false;
                        if (!$upcomingJadwal) {
                            $upcomingJadwal = $jg;
                        }
                    }
                } else {
                    $allExpired = false;
                }
            }

            // ATURAN KETAT JAM SESI (Permintaan User):
            // 1. Jika belum masuk jam sesi, tolak scan dan beri tahu jam mulainya!
            // 2. Jika sesi 1 belum habis, tidak bisa scan sesi 2!
            // 3. Jika seluruh sesi hari ini sudah selesai, tolak scan!
            if (!$activeJadwal) {
                if ($upcomingJadwal) {
                    $upMapel = !empty($upcomingJadwal['mapel_nama']) ? $upcomingJadwal['mapel_nama'] : (!empty($upcomingJadwal['nama_ujian']) ? $upcomingJadwal['nama_ujian'] : 'Ujian Selanjutnya');
                    $upSlot = !empty($upcomingJadwal['label_sesi']) ? $upcomingJadwal['label_sesi'] : (!empty($upcomingJadwal['jam_slot']) ? $upcomingJadwal['jam_slot'] : '');
                    $upJamMulai = !empty($upcomingJadwal['parsed_jam_mulai']) ? substr($upcomingJadwal['parsed_jam_mulai'], 0, 5) : 'jadwal';
                    
                    echo json_encode([
                        'success' => false,
                        'message' => "⚠️ Sesi Ujian Belum Dibuka! Jadwal sesi terdekat untuk kelas {$siswa['kelas_nama']} adalah {$upMapel} ({$upSlot}) yang baru dibuka pukul {$upJamMulai} WIB. Siswa belum dapat scan sebelum jam sesi dimulai."
                    ]);
                    exit;
                } elseif ($allExpired) {
                    echo json_encode([
                        'success' => false,
                        'message' => "⚠️ Waktu Sesi Ujian Telah Berakhir! Seluruh sesi ujian hari ini untuk kelas {$siswa['kelas_nama']} telah selesai. Silakan hubungi proktor / pengawas ujian."
                    ]);
                    exit;
                }
            }
        }

        $jadwal = $activeJadwal;
        if (!$jadwal) {
            echo json_encode([
                'success' => false,
                'message' => "Tidak ada sesi ujian aktif untuk kelas {$siswa['kelas_nama']} pada jam saat ini (" . substr($curTime, 0, 5) . " WIB)!"
            ]);
            exit;
        }

        $mapelNama = !empty($jadwal['mapel_nama']) ? $jadwal['mapel_nama'] : (!empty($jadwal['nama_ujian']) ? $jadwal['nama_ujian'] : 'Asesmen CBT Harian');
        $jamSlot = !empty($jadwal['label_sesi']) ? $jadwal['label_sesi'] : (!empty($jadwal['jam_slot']) ? $jadwal['jam_slot'] : 'Sesi 1');
        $topikId = (int)($jadwal['topik_id'] ?? 0);
        $ujianId = (int)($jadwal['ujian_id'] ?? 0);
        $ruangNama = trim($_POST['ruang_nama'] ?? ($jadwal['ruang'] ?? 'Gerbang / 1 Pintu'));

        // 3. Cek apakah sudah absen untuk mapel & tanggal ini
        $stmtCheck = $db->prepare("
            SELECT id, waktu_scan FROM cbt_absensi 
            WHERE tanggal = ? AND nis = ? AND mapel_nama = ?
        ");
        $stmtCheck->execute([$todayDate, $siswa['nis'], $mapelNama]);
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

        // 4. Catat ke cbt_absensi
        $stmtIns = $db->prepare("
            INSERT INTO cbt_absensi 
            (tanggal, siswa_id, nis, nama_siswa, kelas_nama, tipe_peserta, topik_id, ujian_id, mapel_nama, jam_slot, waktu_scan, status, metode_scan, petugas_nama, ruang_nama)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, 'QR_SCAN', ?, ?)
        ");
        $stmtIns->execute([
            $todayDate,
            $siswa['id'],
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
}
