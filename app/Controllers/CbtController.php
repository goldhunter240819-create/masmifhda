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
     * Dapatkan daftar Mata Pelajaran Real dari tabel mata_pelajaran di database siakad
     */
    public static function getRealMapelList($db_siakad) {
        try {
            $list = $db_siakad->query("SELECT id, nama_mapel, kode_mapel FROM mata_pelajaran ORDER BY nama_mapel ASC")->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($list)) return $list;
        } catch (\Exception $e) {}

        // Fallback daftar mapel standar madrasah jika tabel kosong
        return [
            ['id' => 1, 'nama_mapel' => "Al-Qur'an Hadits"],
            ['id' => 2, 'nama_mapel' => 'Akidah Akhlak'],
            ['id' => 3, 'nama_mapel' => 'Fiqih'],
            ['id' => 4, 'nama_mapel' => 'Sejarah Kebudayaan Islam'],
            ['id' => 5, 'nama_mapel' => 'Bahasa Arab'],
            ['id' => 6, 'nama_mapel' => 'Bahasa Indonesia'],
            ['id' => 7, 'nama_mapel' => 'Matematika'],
            ['id' => 8, 'nama_mapel' => 'Bahasa Inggris'],
            ['id' => 9, 'nama_mapel' => 'Biologi'],
            ['id' => 10, 'nama_mapel' => 'Fisika'],
            ['id' => 11, 'nama_mapel' => 'Kimia'],
            ['id' => 12, 'nama_mapel' => 'Ekonomi'],
            ['id' => 13, 'nama_mapel' => 'Sosiologi'],
            ['id' => 14, 'nama_mapel' => 'Geografi'],
            ['id' => 15, 'nama_mapel' => 'Informatika']
        ];
    }

    /**
     * Dapatkan daftar Kelas Real dari tabel kelas di database core
     */
    public static function getRealKelasList($db_core) {
        try {
            $list = $db_core->query("SELECT id, nama_kelas, tingkat FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($list)) return $list;
        } catch (\Exception $e) {}

        return [
            ['id' => 1, 'nama_kelas' => 'X-IPA', 'tingkat' => 'X'],
            ['id' => 2, 'nama_kelas' => 'X-IPS', 'tingkat' => 'X'],
            ['id' => 3, 'nama_kelas' => 'XI-IPA', 'tingkat' => 'XI'],
            ['id' => 4, 'nama_kelas' => 'XI-IPS', 'tingkat' => 'XI'],
            ['id' => 5, 'nama_kelas' => 'XII-IPA', 'tingkat' => 'XII'],
            ['id' => 6, 'nama_kelas' => 'XII-IPS', 'tingkat' => 'XII']
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
     * Rekap Hasil Ujian Siswa Real
     */
    public static function hasil() {
        $ctx = self::getAuthContext();
        $db = $ctx['db_core'];

        $ujianList = $db->query("SELECT id, kode_ujian, nama_ujian, mapel_nama, kkm FROM cbt_ujian ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

        $selectedUjianId = isset($_GET['ujian_id']) ? (int)$_GET['ujian_id'] : 0;
        $selectedKelas = isset($_GET['kelas']) ? trim($_GET['kelas']) : '';
        $search = isset($_GET['q']) ? trim($_GET['q']) : '';

        // Daftar kelas real dari database kelas
        $kelasList = [];
        try {
            $kelasList = $db->query("SELECT DISTINCT nama_kelas FROM kelas ORDER BY nama_kelas ASC")->fetchAll(PDO::FETCH_COLUMN);
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

        // Ambil butir soal
        $stmtS = $db->prepare("SELECT id, nomor_urut, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, bobot FROM cbt_soal WHERE ujian_id = ? ORDER BY nomor_urut ASC, id ASC");
        $stmtS->execute([$ujian_id]);
        $soalList = $stmtS->fetchAll(PDO::FETCH_ASSOC);

        if (empty($soalList)) {
            echo "<script>alert('Paket ujian ini belum memiliki butir soal! Tambahkan butir soal terlebih dahulu.'); window.location.href='" . base_url('cbt/soal?ujian_id=' . $ujian_id) . "';</script>";
            exit;
        }

        // Cek identitas peserta (siswa login atau trial guru)
        $userId = (int)($_SESSION['user_id'] ?? 0);
        $roleId = (int)($_SESSION['role_id'] ?? 0);

        $pesertaInfo = null;

        if ($roleId === 3) {
            // Role Siswa: Cari data siswa di database core
            $stmtSiswa = $db->prepare("SELECT s.id, s.nis, s.nama, COALESCE(k.nama_kelas, 'Umum') as kelas_nama 
                                       FROM siswa s 
                                       LEFT JOIN kelas k ON s.kelas_id = k.id 
                                       WHERE s.user_id = ? LIMIT 1");
            $stmtSiswa->execute([$userId]);
            $realSiswa = $stmtSiswa->fetch(PDO::FETCH_ASSOC);

            if ($realSiswa) {
                // Cari atau buat record di cbt_peserta
                $stmtP = $db->prepare("SELECT * FROM cbt_peserta WHERE ujian_id = ? AND siswa_id = ? LIMIT 1");
                $stmtP->execute([$ujian_id, $realSiswa['id']]);
                $pesertaInfo = $stmtP->fetch(PDO::FETCH_ASSOC);

                if (!$pesertaInfo) {
                    $stmtIns = $db->prepare("INSERT INTO cbt_peserta (ujian_id, siswa_id, nis, nama_siswa, kelas_nama, waktu_mulai, status_pengerjaan) 
                                             VALUES (?, ?, ?, ?, ?, NOW(), 'Mengerjakan')");
                    $stmtIns->execute([$ujian_id, $realSiswa['id'], $realSiswa['nis'], $realSiswa['nama'], $realSiswa['kelas_nama']]);
                    $pesertaInfo = [
                        'id' => $db->lastInsertId(),
                        'ujian_id' => $ujian_id,
                        'siswa_id' => $realSiswa['id'],
                        'nis' => $realSiswa['nis'],
                        'nama_siswa' => $realSiswa['nama'],
                        'kelas_nama' => $realSiswa['kelas_nama']
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
                'kelas_nama' => ($ujianInfo['kelas_nama'] ?: 'Simulasi')
            ];
        }

        include __DIR__ . '/../../resources/views/cbt/kerjakan.php';
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

            // Ambil seluruh kunci jawaban dan bobot soal
            $stmtSoal = $db->prepare("SELECT id, nomor_urut, kunci_jawaban, bobot FROM cbt_soal WHERE ujian_id = ?");
            $stmtSoal->execute([$ujian_id]);
            $soals = $stmtSoal->fetchAll(PDO::FETCH_ASSOC);

            $totalBobot = 0;
            $poinPerolehan = 0;
            $jumlahBenar = 0;
            $jumlahSalah = 0;

            foreach ($soals as $s) {
                $bobot = (float)($s['bobot'] > 0 ? $s['bobot'] : 1.0);
                $totalBobot += $bobot;

                $userAns = strtoupper(trim($answers[$s['id']] ?? ''));
                $kunci = strtoupper(trim($s['kunci_jawaban']));

                if (!empty($userAns) && $userAns === $kunci) {
                    $poinPerolehan += $bobot;
                    $jumlahBenar++;
                } else {
                    $jumlahSalah++;
                }
            }

            $nilaiAkhir = ($totalBobot > 0) ? round(($poinPerolehan / $totalBobot) * 100, 1) : 0;

            // Simpan ke database jika peserta terdaftar
            if ($peserta_id > 0) {
                $stmtUp = $db->prepare("UPDATE cbt_peserta 
                                        SET waktu_selesai = NOW(), status_pengerjaan = 'Selesai', 
                                            jumlah_benar = ?, jumlah_salah = ?, nilai_akhir = ?, jawaban_json = ? 
                                        WHERE id = ?");
                $stmtUp->execute([$jumlahBenar, $jumlahSalah, $nilaiAkhir, json_encode($answers), $peserta_id]);
            } else {
                // Untuk sesi trial pengawas/guru, simpan sebagai sesi percobaan
                $namaPengawas = $_SESSION['nama'] ?? 'Guru (Trial)';
                $stmtIns = $db->prepare("INSERT INTO cbt_peserta (ujian_id, siswa_id, nis, nama_siswa, kelas_nama, waktu_mulai, waktu_selesai, status_pengerjaan, jumlah_benar, jumlah_salah, nilai_akhir, jawaban_json) 
                                         VALUES (?, NULL, 'TRIAL', ?, 'Simulasi', NOW() - INTERVAL 15 MINUTE, NOW(), 'Selesai', ?, ?, ?, ?)");
                $stmtIns->execute([$ujian_id, $namaPengawas, $jumlahBenar, $jumlahSalah, $nilaiAkhir, json_encode($answers)]);
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
}
