<?php

namespace App\Controllers;

use App\Core\Database;
use PDO;

class CbtController {

    /**
     * Pastikan tabel-tabel modul CBT Online tersedia di database core.
     * Buat dan isi data sampel awal jika tabel masih kosong.
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
                kunci_jawaban VARCHAR(5) NOT NULL,
                bobot DECIMAL(5,2) DEFAULT 1.00,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (ujian_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

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

            // 4. Tabel Peserta & Riwayat Pengerjaan
            $db->exec("CREATE TABLE IF NOT EXISTS cbt_peserta (
                id INT AUTO_INCREMENT PRIMARY KEY,
                ujian_id INT NOT NULL,
                siswa_id INT DEFAULT NULL,
                nis VARCHAR(50) DEFAULT NULL,
                nama_siswa VARCHAR(150) NOT NULL,
                kelas_nama VARCHAR(100) DEFAULT NULL,
                waktu_mulai DATETIME DEFAULT NULL,
                waktu_selesai DATETIME DEFAULT NULL,
                status_pengerjaan VARCHAR(50) DEFAULT 'Selesai',
                jumlah_benar INT DEFAULT 0,
                jumlah_salah INT DEFAULT 0,
                nilai_akhir DECIMAL(5,2) DEFAULT 0.00,
                jawaban_json LONGTEXT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (ujian_id),
                INDEX (nis)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // Cek apakah tabel cbt_ujian masih kosong, jika ya buatkan seed data realistis
            $checkCount = $db->query("SELECT COUNT(*) FROM cbt_ujian")->fetchColumn();
            if ($checkCount == 0) {
                // Seed Ujian 1: Aktif
                $stmtU1 = $db->prepare("INSERT INTO cbt_ujian (kode_ujian, nama_ujian, mapel_nama, kelas_nama, durasi_menit, tgl_mulai, tgl_selesai, kkm, acak_soal, acak_opsi, status) 
                                        VALUES (?, ?, ?, ?, ?, NOW() - INTERVAL 1 HOUR, NOW() + INTERVAL 2 DAY, ?, 1, 1, 'Aktif')");
                $stmtU1->execute(['UJN-MAT-01', 'Penilaian Akhir Semester (PAS) Ganjil', 'Matematika Wajib', 'X-IPA', 90, 75]);
                $u1Id = $db->lastInsertId();

                // Seed Ujian 2: Draft
                $stmtU2 = $db->prepare("INSERT INTO cbt_ujian (kode_ujian, nama_ujian, mapel_nama, kelas_nama, durasi_menit, tgl_mulai, tgl_selesai, kkm, acak_soal, acak_opsi, status) 
                                        VALUES (?, ?, ?, ?, ?, NOW() + INTERVAL 1 DAY, NOW() + INTERVAL 3 DAY, ?, 1, 1, 'Draft')");
                $stmtU2->execute(['UJN-FIQ-02', 'Asesmen Sumatif Tengah Semester', 'Pendidikan Agama Islam & Fiqih', 'XI-IPS', 60, 75]);
                $u2Id = $db->lastInsertId();

                // Seed Soal untuk Ujian 1 (5 butir soal pilihan ganda)
                $soalSeeds = [
                    [
                        'no' => 1,
                        'q' => 'Berapakah himpunan penyelesaian dari persamaan kuadrat x² - 5x + 6 = 0?',
                        'a' => '{1, 6}',
                        'b' => '{2, 3}',
                        'c' => '{-2, -3}',
                        'd' => '{-1, 6}',
                        'e' => '{0, 6}',
                        'kunci' => 'B',
                        'bobot' => 1.00
                    ],
                    [
                        'no' => 2,
                        'q' => 'Diketahui f(x) = 2x + 3 dan g(x) = x² - 1. Tentukan nilai dari fungsi komposisi (f ∘ g)(2)!',
                        'a' => '9',
                        'b' => '7',
                        'c' => '11',
                        'd' => '5',
                        'e' => '15',
                        'kunci' => 'A',
                        'bobot' => 1.00
                    ],
                    [
                        'no' => 3,
                        'q' => 'Sebuah dadu bermata enam seimbang dilempar satu kali. Peluang munculnya mata dadu prima ganjil adalah...',
                        'a' => '1/6',
                        'b' => '1/3',
                        'c' => '1/2',
                        'd' => '2/3',
                        'e' => '5/6',
                        'kunci' => 'B',
                        'bobot' => 1.00
                    ],
                    [
                        'no' => 4,
                        'q' => 'Nilai limit dari lim_{x→0} (sin 4x / 2x) adalah...',
                        'a' => '0',
                        'b' => '1',
                        'c' => '2',
                        'd' => '4',
                        'e' => '1/2',
                        'kunci' => 'C',
                        'bobot' => 1.00
                    ],
                    [
                        'no' => 5,
                        'q' => 'Diketahui barisan aritmetika dengan suku pertama a = 4 dan beda b = 3. Tentukan suku ke-10 (U10)!',
                        'a' => '28',
                        'b' => '31',
                        'c' => '34',
                        'd' => '30',
                        'e' => '37',
                        'kunci' => 'B',
                        'bobot' => 1.00
                    ],
                ];

                $stmtSoal = $db->prepare("INSERT INTO cbt_soal (ujian_id, nomor_urut, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, bobot) 
                                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                foreach ($soalSeeds as $s) {
                    $stmtSoal->execute([
                        $u1Id, $s['no'], $s['q'], $s['a'], $s['b'], $s['c'], $s['d'], $s['e'], $s['kunci'], $s['bobot']
                    ]);
                }

                // Seed Token Aktif
                $stmtToken = $db->prepare("INSERT INTO cbt_token (token, ujian_id, masa_berlaku_menit, status, created_at) 
                                          VALUES (?, ?, 120, 'Aktif', NOW())");
                $stmtToken->execute(['MAS982', $u1Id]);

                // Seed Peserta Siswa & Hasil Ujian untuk Ujian 1
                $pesertaSeeds = [
                    ['nis' => '20241001', 'nama' => 'Ahmad Fauzi Rahman', 'kelas' => 'X-IPA', 'b' => 5, 's' => 0, 'nilai' => 100.0, 'ans' => ['1'=>'B','2'=>'A','3'=>'B','4'=>'C','5'=>'B']],
                    ['nis' => '20241002', 'nama' => 'Siti Nur Aisyah', 'kelas' => 'X-IPA', 'b' => 4, 's' => 1, 'nilai' => 80.0, 'ans' => ['1'=>'B','2'=>'A','3'=>'B','4'=>'C','5'=>'A']],
                    ['nis' => '20241003', 'nama' => 'Muhammad Rizky Pratama', 'kelas' => 'X-IPA', 'b' => 4, 's' => 1, 'nilai' => 80.0, 'ans' => ['1'=>'B','2'=>'A','3'=>'A','4'=>'C','5'=>'B']],
                    ['nis' => '20241004', 'nama' => 'Dewi Anggraini', 'kelas' => 'X-IPA', 'b' => 4, 's' => 1, 'nilai' => 80.0, 'ans' => ['1'=>'B','2'=>'B','3'=>'B','4'=>'C','5'=>'B']],
                    ['nis' => '20241005', 'nama' => 'Budi Santoso', 'kelas' => 'X-IPA', 'b' => 3, 's' => 2, 'nilai' => 60.0, 'ans' => ['1'=>'B','2'=>'C','3'=>'B','4'=>'A','5'=>'B']],
                    ['nis' => '20241006', 'nama' => 'Nurul Hidayati', 'kelas' => 'X-IPA', 'b' => 3, 's' => 2, 'nilai' => 60.0, 'ans' => ['1'=>'A','2'=>'A','3'=>'C','4'=>'C','5'=>'B']],
                    ['nis' => '20241007', 'nama' => 'Fajar Sidik Permana', 'kelas' => 'X-IPA', 'b' => 2, 's' => 3, 'nilai' => 40.0, 'ans' => ['1'=>'C','2'=>'B','3'=>'A','4'=>'C','5'=>'A']],
                    ['nis' => '20241008', 'nama' => 'Rina Kartika Sari', 'kelas' => 'X-IPA', 'b' => 5, 's' => 0, 'nilai' => 100.0, 'ans' => ['1'=>'B','2'=>'A','3'=>'B','4'=>'C','5'=>'B']]
                ];

                $stmtPeserta = $db->prepare("INSERT INTO cbt_peserta (ujian_id, siswa_id, nis, nama_siswa, kelas_nama, waktu_mulai, waktu_selesai, status_pengerjaan, jumlah_benar, jumlah_salah, nilai_akhir, jawaban_json) 
                                             VALUES (?, NULL, ?, ?, ?, NOW() - INTERVAL 45 MINUTE, NOW() - INTERVAL 5 MINUTE, 'Selesai', ?, ?, ?, ?)");
                foreach ($pesertaSeeds as $p) {
                    $stmtPeserta->execute([
                        $u1Id, $p['nis'], $p['nama'], $p['kelas'], $p['b'], $p['s'], $p['nilai'], json_encode($p['ans'])
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Silence exception or log if needed
        }
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
     * Dashboard CBT Online
     */
    public static function dashboard() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];

        // Ambil metrik ringkasan
        $totalUjian = (int)$db->query("SELECT COUNT(*) FROM cbt_ujian")->fetchColumn();
        $totalSoal = (int)$db->query("SELECT COUNT(*) FROM cbt_soal")->fetchColumn();
        $totalPeserta = (int)$db->query("SELECT COUNT(*) FROM cbt_peserta")->fetchColumn();
        $totalPesertaSelesai = (int)$db->query("SELECT COUNT(*) FROM cbt_peserta WHERE status_pengerjaan = 'Selesai'")->fetchColumn();

        // Token saat ini
        $stmtTok = $db->query("SELECT * FROM cbt_token WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1");
        $tokenAktif = $stmtTok->fetch(PDO::FETCH_ASSOC) ?: null;

        // Daftar Ujian
        $ujianList = $db->query("SELECT * FROM cbt_ujian ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

        // Peserta Selesai Terbaru
        $stmtP = $db->query("SELECT p.*, u.nama_ujian 
                             FROM cbt_peserta p 
                             LEFT JOIN cbt_ujian u ON u.id = p.ujian_id 
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

        $ujianList = $db->query("SELECT * FROM cbt_ujian ORDER BY (tgl_mulai IS NOT NULL) DESC, tgl_mulai DESC, created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

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
     * Toggle Status Ujian (Aktif <-> Tutup/Draft)
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
     * Kelola Paket Ujian
     */
    public static function ujian() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];

        $ujianList = $db->query("SELECT u.*, 
                                 (SELECT COUNT(*) FROM cbt_soal WHERE ujian_id = u.id) AS jml_soal,
                                 (SELECT COUNT(*) FROM cbt_peserta WHERE ujian_id = u.id) AS jml_peserta 
                                 FROM cbt_ujian u 
                                 ORDER BY u.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

        $title = "Kelola Paket Ujian CBT - MAS MIFTAHUL HUDA";
        ob_start();
        include __DIR__ . '/../../resources/views/cbt/ujian.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Simpan / Perbarui Paket Ujian
     */
    public static function saveUjian() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
            $kode_ujian = trim($_POST['kode_ujian'] ?? ('UJN-' . strtoupper(substr(uniqid(), -4))));
            $nama_ujian = trim($_POST['nama_ujian'] ?? 'Ujian Baru');
            $mapel_nama = trim($_POST['mapel_nama'] ?? 'Umum');
            $kelas_nama = trim($_POST['kelas_nama'] ?? '');
            $durasi_menit = !empty($_POST['durasi_menit']) ? (int)$_POST['durasi_menit'] : 90;
            $kkm = !empty($_POST['kkm']) ? (int)$_POST['kkm'] : 75;
            $status = !empty($_POST['status']) ? trim($_POST['status']) : 'Draft';
            $acak_soal = isset($_POST['acak_soal']) ? 1 : 0;
            $acak_opsi = isset($_POST['acak_opsi']) ? 1 : 0;

            if ($id > 0) {
                $stmt = $db->prepare("UPDATE cbt_ujian 
                                      SET kode_ujian = ?, nama_ujian = ?, mapel_nama = ?, kelas_nama = ?, durasi_menit = ?, kkm = ?, status = ?, acak_soal = ?, acak_opsi = ? 
                                      WHERE id = ?");
                $stmt->execute([$kode_ujian, $nama_ujian, $mapel_nama, $kelas_nama, $durasi_menit, $kkm, $status, $acak_soal, $acak_opsi, $id]);
            } else {
                $stmt = $db->prepare("INSERT INTO cbt_ujian (kode_ujian, nama_ujian, mapel_nama, kelas_nama, durasi_menit, kkm, status, acak_soal, acak_opsi) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$kode_ujian, $nama_ujian, $mapel_nama, $kelas_nama, $durasi_menit, $kkm, $status, $acak_soal, $acak_opsi]);
            }
            redirect('/cbt/ujian?saved=1');
            exit;
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
            // Ambil ujian pertama jika ada
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

        // Ambil daftar butir soal
        $stmtS = $db->prepare("SELECT * FROM cbt_soal WHERE ujian_id = ? ORDER BY nomor_urut ASC, id ASC");
        $stmtS->execute([$ujian_id]);
        $soalList = $stmtS->fetchAll(PDO::FETCH_ASSOC);

        // Ambil daftar topik bank soal dari database siakad jika ada
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
     * Simpan / Perbarui Butir Soal
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
                // Hitung nomor urut berikutnya
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
                // Ambil soal-soal PG dari Bank Soal
                try {
                    $stmtBS = $db_siakad->prepare("SELECT * FROM bs_soal WHERE topik_id = ?");
                    $stmtBS->execute([$topik_id]);
                    $bankItems = $stmtBS->fetchAll(PDO::FETCH_ASSOC);

                    // Ambil nomor urut tertinggi saat ini
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
     * Manajemen Token Ujian
     */
    public static function token() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];

        // Ambil token aktif saat ini
        $stmtTok = $db->query("SELECT * FROM cbt_token WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1");
        $tokenAktif = $stmtTok->fetch(PDO::FETCH_ASSOC) ?: null;

        // Riwayat token sebelumnya
        $tokenHistory = $db->query("SELECT * FROM cbt_token ORDER BY id DESC LIMIT 25")->fetchAll(PDO::FETCH_ASSOC);

        $title = "Token Ujian CBT - MAS MIFTAHUL HUDA";
        ob_start();
        include __DIR__ . '/../../resources/views/cbt/token.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Generate / Rilis Token Baru
     */
    public static function generateToken() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Nonaktifkan token lama
            $db->exec("UPDATE cbt_token SET status = 'Expired' WHERE status = 'Aktif'");

            // Buat token 6 karakter alfanumerik yang mudah dibaca (tanpa angka 0, O, 1, I)
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
            $tokenStr = '';
            for ($i = 0; $i < 6; $i++) {
                $tokenStr .= $chars[rand(0, strlen($chars) - 1)];
            }

            $stmt = $db->prepare("INSERT INTO cbt_token (token, masa_berlaku_menit, status, created_at) VALUES (?, 120, 'Aktif', NOW())");
            $stmt->execute([$tokenStr]);

            redirect('/cbt/token?success=1');
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
            redirect('/cbt/token?stopped=1');
            exit;
        }
        redirect('/cbt/token');
    }

    /**
     * Rekap Hasil Ujian Siswa
     */
    public static function hasil() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];

        $ujianList = $db->query("SELECT id, kode_ujian, nama_ujian, mapel_nama, kkm FROM cbt_ujian ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

        $selectedUjianId = isset($_GET['ujian_id']) ? (int)$_GET['ujian_id'] : 0;
        $selectedKelas = isset($_GET['kelas']) ? trim($_GET['kelas']) : '';
        $search = isset($_GET['q']) ? trim($_GET['q']) : '';

        // Ambil daftar kelas unik dari peserta
        $kelasList = [];
        try {
            $kelasList = $db->query("SELECT DISTINCT kelas_nama FROM cbt_peserta WHERE kelas_nama IS NOT NULL AND kelas_nama != '' ORDER BY kelas_nama ASC")->fetchAll(PDO::FETCH_COLUMN);
        } catch (\Exception $e) {}

        // Query Peserta
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

        $sql .= " ORDER BY p.nilai_akhir DESC, p.nama_siswa ASC";
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

        // Kalkulasi Statistik
        $totalPeserta = count($pesertaList);
        $totalNilai = 0;
        $tuntasCount = 0;
        $jumlahRemedial = 0;

        foreach ($pesertaList as $p) {
            $nilai = (float)$p['nilai_akhir'];
            $totalNilai += $nilai;
            if ($nilai >= $kkmUjian) {
                $tuntasCount++;
            } else {
                $jumlahRemedial++;
            }
        }

        $rataRata = $totalPeserta > 0 ? ($totalNilai / $totalPeserta) : 0;
        $persenTuntas = $totalPeserta > 0 ? round(($tuntasCount / $totalPeserta) * 100) : 0;

        $title = "Hasil Ujian CBT - MAS MIFTAHUL HUDA";
        ob_start();
        include __DIR__ . '/../../resources/views/cbt/hasil.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Analisis Butir Soal (Tingkat Kesukaran, Daya Pembeda, Pengecoh, KR-20)
     */
    public static function analisis() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];

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

        // Ambil seluruh butir soal ujian
        $stmtSoal = $db->prepare("SELECT * FROM cbt_soal WHERE ujian_id = ? ORDER BY nomor_urut ASC, id ASC");
        $stmtSoal->execute([$selectedUjianId]);
        $soalRows = $stmtSoal->fetchAll(PDO::FETCH_ASSOC);

        // Ambil data seluruh peserta ujian beserta jawaban_json
        $stmtPeserta = $db->prepare("SELECT id, nis, nama_siswa, nilai_akhir, jawaban_json FROM cbt_peserta WHERE ujian_id = ? ORDER BY nilai_akhir DESC");
        $stmtPeserta->execute([$selectedUjianId]);
        $pesertaRows = $stmtPeserta->fetchAll(PDO::FETCH_ASSOC);

        $totalPesertaUjian = count($pesertaRows);

        // Hitung statistik skor peserta
        $scores = [];
        $totalNilai = 0;
        $nilaiMaks = 0;
        $nilaiMin = 100;

        foreach ($pesertaRows as $p) {
            $sc = (float)$p['nilai_akhir'];
            $scores[] = $sc;
            $totalNilai += $sc;
            if ($sc > $nilaiMaks) $nilaiMaks = $sc;
            if ($sc < $nilaiMin) $nilaiMin = $sc;
        }

        if (empty($scores)) {
            $nilaiMin = 0;
        }

        $rataRata = $totalPesertaUjian > 0 ? ($totalNilai / $totalPesertaUjian) : 0;

        // Standar Deviasi
        $variance = 0;
        if ($totalPesertaUjian > 0) {
            $sumSqDiff = 0;
            foreach ($scores as $s) {
                $sumSqDiff += pow($s - $rataRata, 2);
            }
            $variance = $sumSqDiff / $totalPesertaUjian;
        }
        $standarDeviasi = sqrt($variance);

        // Bagi kelompok atas dan bawah untuk perhitungan Daya Pembeda (D)
        // Standar psikometri: 27% atas dan 27% bawah, atau 50% jika peserta <= 6
        $kGroup = ($totalPesertaUjian <= 6) ? max(1, (int)round($totalPesertaUjian / 2)) : max(1, (int)round(0.27 * $totalPesertaUjian));
        $kelompokAtas = array_slice($pesertaRows, 0, $kGroup);
        $kelompokBawah = array_slice($pesertaRows, max(0, $totalPesertaUjian - $kGroup), $kGroup);

        $analisisSoal = [];
        $sumPQ = 0;

        foreach ($soalRows as $s) {
            $soalNo = (string)$s['nomor_urut'];
            $kunci = strtoupper(trim($s['kunci_jawaban']));

            $jmlBenar = 0;
            $jmlSalah = 0;
            $sebaran = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0];

            // Hitung sebaran jawaban peserta
            foreach ($pesertaRows as $p) {
                $ansData = !empty($p['jawaban_json']) ? json_decode($p['jawaban_json'], true) : [];
                $ans = strtoupper(trim($ansData[$soalNo] ?? ($ansData[$s['id']] ?? '')));
                if (isset($sebaran[$ans])) {
                    $sebaran[$ans]++;
                }
                if ($ans === $kunci) {
                    $jmlBenar++;
                } else {
                    $jmlSalah++;
                }
            }

            // Tingkat Kesukaran (P)
            $pVal = $totalPesertaUjian > 0 ? ($jmlBenar / $totalPesertaUjian) : 0;
            $qVal = 1 - $pVal;
            $sumPQ += ($pVal * $qVal);

            // Daya Pembeda (D)
            $benarAtas = 0;
            foreach ($kelompokAtas as $pa) {
                $ansData = !empty($pa['jawaban_json']) ? json_decode($pa['jawaban_json'], true) : [];
                $ans = strtoupper(trim($ansData[$soalNo] ?? ($ansData[$s['id']] ?? '')));
                if ($ans === $kunci) $benarAtas++;
            }

            $benarBawah = 0;
            foreach ($kelompokBawah as $pb) {
                $ansData = !empty($pb['jawaban_json']) ? json_decode($pb['jawaban_json'], true) : [];
                $ans = strtoupper(trim($ansData[$soalNo] ?? ($ansData[$s['id']] ?? '')));
                if ($ans === $kunci) $benarBawah++;
            }

            $dVal = $kGroup > 0 ? (($benarAtas - $benarBawah) / $kGroup) : 0;

            // Efektivitas Pengecoh & Rekomendasi
            $hasDeadDistractor = false;
            foreach (['A', 'B', 'C', 'D', 'E'] as $opt) {
                if ($opt !== $kunci && !empty($s['opsi_' . strtolower($opt)])) {
                    if ($sebaran[$opt] == 0 && $totalPesertaUjian >= 5) {
                        $hasDeadDistractor = true;
                    }
                }
            }

            if ($dVal < 0.20 || $dVal < 0) {
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

        // Reliabilitas KR-20
        $jmlItem = count($soalRows);
        $reliabilitasKR20 = 0.85; // Default standar tinggi
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
}

