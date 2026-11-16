<?php

namespace App\Controllers;

use App\Core\Database;
use App\Controllers\CbtController;
use PDO;

class BankSoalController {
    
    private $db;
    private $db_core;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = Database::connect('siakad') ?: Database::connect('core');
        $this->db_core = Database::connect('core');

        self::ensureTables($this->db);
        if ($this->db_core !== $this->db) {
            self::ensureTables($this->db_core);
        }
    }

    /**
     * Pastikan tabel bs_topik, bs_paket, dan bs_soal ada di database beserta semua kolom terbarunya
     */
    public static function ensureTables($db) {
        if (!$db) return;
        try {
            // 1. bs_topik
            $db->exec("CREATE TABLE IF NOT EXISTS bs_topik (
                id INT AUTO_INCREMENT PRIMARY KEY,
                jenis VARCHAR(50) NOT NULL DEFAULT 'SAS',
                nama_topik VARCHAR(255) NOT NULL,
                keterangan TEXT NULL,
                tahun_ajaran VARCHAR(20) DEFAULT '2026/2027',
                semester VARCHAR(20) DEFAULT 'Ganjil',
                tgl_mulai DATE DEFAULT NULL,
                tgl_selesai DATE DEFAULT NULL,
                status VARCHAR(30) DEFAULT 'Terjadwal',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");

            // 2. bs_paket (Paket Mapel & Kelas per Topik)
            $db->exec("CREATE TABLE IF NOT EXISTS bs_paket (
                id INT AUTO_INCREMENT PRIMARY KEY,
                topik_id INT NOT NULL,
                mapel_nama VARCHAR(150) NOT NULL,
                kelas_nama VARCHAR(100) NOT NULL DEFAULT 'Semua Kelas',
                guru_id INT DEFAULT NULL,
                guru_nama VARCHAR(150) DEFAULT NULL,
                kkm INT DEFAULT 75,
                durasi_menit INT DEFAULT 90,
                is_locked TINYINT(1) DEFAULT 0,
                locked_at DATETIME DEFAULT NULL,
                locked_by VARCHAR(100) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_topik (topik_id),
                INDEX idx_mapel (mapel_nama)
            )");

            // 3. bs_soal (Butir Soal & Kunci Jawaban)
            $db->exec("CREATE TABLE IF NOT EXISTS bs_soal (
                id INT AUTO_INCREMENT PRIMARY KEY,
                topik_id INT NOT NULL,
                paket_id INT DEFAULT NULL,
                mapel_nama VARCHAR(150) NOT NULL,
                kelas_nama VARCHAR(100) DEFAULT NULL,
                nomor_urut INT DEFAULT 1,
                tipe_soal VARCHAR(50) NOT NULL DEFAULT 'sec_pg', 
                tingkat_kesulitan VARCHAR(20) DEFAULT 'Sedang',
                skor_maksimal DECIMAL(5,2) DEFAULT 2.00,
                pertanyaan LONGTEXT NOT NULL,
                opsi_a LONGTEXT DEFAULT NULL,
                opsi_b LONGTEXT DEFAULT NULL,
                opsi_c LONGTEXT DEFAULT NULL,
                opsi_d LONGTEXT DEFAULT NULL,
                opsi_e LONGTEXT DEFAULT NULL,
                kunci_jawaban TEXT DEFAULT NULL,
                konten_tambahan LONGTEXT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_topik_soal (topik_id),
                INDEX idx_paket_soal (paket_id),
                INDEX idx_mapel_soal (mapel_nama)
            )");

            // ═══════════════════════════════════════════════════════════════
            // AUTO MIGRATION: Pastikan kolom baru selalu ada di live database
            // ═══════════════════════════════════════════════════════════════
            $colsSoal = $db->query("DESCRIBE bs_soal")->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('paket_id', $colsSoal)) {
                $db->exec("ALTER TABLE bs_soal ADD COLUMN paket_id INT DEFAULT NULL AFTER topik_id");
                try { $db->exec("ALTER TABLE bs_soal ADD INDEX idx_paket_soal (paket_id)"); } catch (\Exception $e) {}
            }
            if (!in_array('kelas_nama', $colsSoal)) {
                $db->exec("ALTER TABLE bs_soal ADD COLUMN kelas_nama VARCHAR(100) DEFAULT NULL AFTER mapel_nama");
            }
            if (!in_array('nomor_urut', $colsSoal)) {
                $db->exec("ALTER TABLE bs_soal ADD COLUMN nomor_urut INT DEFAULT 1 AFTER kelas_nama");
            }
            if (!in_array('opsi_a', $colsSoal)) {
                $db->exec("ALTER TABLE bs_soal ADD COLUMN opsi_a LONGTEXT DEFAULT NULL AFTER pertanyaan");
            }
            if (!in_array('opsi_b', $colsSoal)) {
                $db->exec("ALTER TABLE bs_soal ADD COLUMN opsi_b LONGTEXT DEFAULT NULL AFTER opsi_a");
            }
            if (!in_array('opsi_c', $colsSoal)) {
                $db->exec("ALTER TABLE bs_soal ADD COLUMN opsi_c LONGTEXT DEFAULT NULL AFTER opsi_b");
            }
            if (!in_array('opsi_d', $colsSoal)) {
                $db->exec("ALTER TABLE bs_soal ADD COLUMN opsi_d LONGTEXT DEFAULT NULL AFTER opsi_c");
            }
            if (!in_array('opsi_e', $colsSoal)) {
                $db->exec("ALTER TABLE bs_soal ADD COLUMN opsi_e LONGTEXT DEFAULT NULL AFTER opsi_d");
            }
            if (!in_array('kunci_jawaban', $colsSoal)) {
                $db->exec("ALTER TABLE bs_soal ADD COLUMN kunci_jawaban TEXT DEFAULT NULL AFTER opsi_e");
            }

            // MIGRASI KOLOM BS_PAKET
            $colsPaket = $db->query("DESCRIBE bs_paket")->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('is_locked', $colsPaket)) {
                $db->exec("ALTER TABLE bs_paket ADD COLUMN is_locked TINYINT(1) DEFAULT 0");
            }
            if (!in_array('locked_at', $colsPaket)) {
                $db->exec("ALTER TABLE bs_paket ADD COLUMN locked_at DATETIME DEFAULT NULL");
            }
            if (!in_array('locked_by', $colsPaket)) {
                $db->exec("ALTER TABLE bs_paket ADD COLUMN locked_by VARCHAR(100) DEFAULT NULL");
            }
            if (!in_array('guru_id', $colsPaket)) {
                $db->exec("ALTER TABLE bs_paket ADD COLUMN guru_id INT DEFAULT NULL");
            }
            if (!in_array('guru_nama', $colsPaket)) {
                $db->exec("ALTER TABLE bs_paket ADD COLUMN guru_nama VARCHAR(150) DEFAULT NULL");
            }
            if (!in_array('kkm', $colsPaket)) {
                $db->exec("ALTER TABLE bs_paket ADD COLUMN kkm INT DEFAULT 75");
            }
            if (!in_array('durasi_menit', $colsPaket)) {
                $db->exec("ALTER TABLE bs_paket ADD COLUMN durasi_menit INT DEFAULT 90");
            }

            // MIGRASI KOLOM BS_TOPIK
            $colsTopik = $db->query("DESCRIBE bs_topik")->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('tgl_mulai', $colsTopik)) {
                $db->exec("ALTER TABLE bs_topik ADD COLUMN tgl_mulai DATE DEFAULT NULL");
            }
            if (!in_array('tgl_selesai', $colsTopik)) {
                $db->exec("ALTER TABLE bs_topik ADD COLUMN tgl_selesai DATE DEFAULT NULL");
            }
            if (!in_array('status', $colsTopik)) {
                $db->exec("ALTER TABLE bs_topik ADD COLUMN status VARCHAR(30) DEFAULT 'Terjadwal'");
            }
            if (!in_array('jenis', $colsTopik)) {
                $db->exec("ALTER TABLE bs_topik ADD COLUMN jenis VARCHAR(50) NOT NULL DEFAULT 'SAS'");
            }
        } catch (\Exception $e) {}
    }

    /**
     * Dashboard Utama Bank Soal
     * Menampilkan Card Topik Ujian atau Detail Paket Mapel di dalam Topik yang dipilih
     */
    public function dashboard() {
        if (!isset($_SESSION['user_id'])) {
            redirect('/login');
            exit;
        }

        $topikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0;

        // Ambil semua topik dari bs_topik
        $stmtTopik = $this->db->query("SELECT * FROM bs_topik ORDER BY (tgl_mulai IS NOT NULL) DESC, tgl_mulai ASC, id DESC");
        $allTopikList = $stmtTopik ? $stmtTopik->fetchAll(PDO::FETCH_ASSOC) : [];

        // Hitung statistik untuk masing-masing topik
        foreach ($allTopikList as &$tp) {
            $tId = (int)$tp['id'];
            $tp['jml_paket'] = (int)$this->db->query("SELECT COUNT(*) FROM bs_paket WHERE topik_id = {$tId}")->fetchColumn();
            $tp['jml_soal'] = (int)$this->db->query("SELECT COUNT(*) FROM bs_soal WHERE topik_id = {$tId}")->fetchColumn();
            $tp['jml_locked'] = (int)$this->db->query("SELECT COUNT(*) FROM bs_paket WHERE topik_id = {$tId} AND is_locked = 1")->fetchColumn();
        }
        unset($tp);

        // Hitung statistik global real
        $totalTopik = count($allTopikList);
        $totalPaket = (int)$this->db->query("SELECT COUNT(*) FROM bs_paket")->fetchColumn();
        $totalSoal = (int)$this->db->query("SELECT COUNT(*) FROM bs_soal")->fetchColumn();
        $totalLocked = (int)$this->db->query("SELECT COUNT(*) FROM bs_paket WHERE is_locked = 1")->fetchColumn();

        $selectedTopik = null;
        $paketList = [];

        if ($topikId > 0) {
            foreach ($allTopikList as $tp) {
                if ((int)$tp['id'] === $topikId) {
                    $selectedTopik = $tp;
                    break;
                }
            }

            if ($selectedTopik) {
                // Ambil daftar paket mapel untuk topik ini
                $stmtP = $this->db->prepare("SELECT * FROM bs_paket WHERE topik_id = ? ORDER BY mapel_nama ASC, kelas_nama ASC");
                $stmtP->execute([$topikId]);
                $paketList = $stmtP->fetchAll(PDO::FETCH_ASSOC);

                // JIKA BELUM ADA PAKET MAPEL, OTOMATIS LANGSUNG MUNCULKAN MAPEL YANG ADA PENGAMPUNYA!
                if (empty($paketList)) {
                    $this->syncFromPengampu($topikId);
                    $stmtP->execute([$topikId]);
                    $paketList = $stmtP->fetchAll(PDO::FETCH_ASSOC);
                }

                // Hitung detail butir soal & breakdown per jenis soal
                foreach ($paketList as &$pk) {
                    $pId = (int)$pk['id'];
                    $breakdown = [];
                    $totalButir = 0;
                    try {
                        $stmtB = $this->db->prepare("SELECT tipe_soal, COUNT(*) as jml FROM bs_soal WHERE paket_id = ? GROUP BY tipe_soal");
                        $stmtB->execute([$pId]);
                        foreach ($stmtB->fetchAll(PDO::FETCH_ASSOC) as $bRow) {
                            $breakdown[$bRow['tipe_soal']] = (int)$bRow['jml'];
                            $totalButir += (int)$bRow['jml'];
                        }
                    } catch (\Exception $e) {}
                    $pk['breakdown'] = $breakdown;
                    $pk['total_soal'] = $totalButir;
                }
                unset($pk);
            }
        }

        // Ambil data referensi untuk form modal
        $mapelList = CbtController::getRealMapelList($this->db, $this->db_core);
        $kelasList = CbtController::getRealKelasList($this->db_core, $this->db);

        $guruList = [];
        try {
            $guruList = $this->db_core->query("SELECT id, nama FROM guru ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}
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

        $page_title = $selectedTopik ? ("Bank Soal: " . $selectedTopik['nama_topik']) : "Dashboard Bank Soal";
        $active_page = "bank_soal";
        include __DIR__ . '/../../resources/views/bank_soal/dashboard.php';
    }

    /**
     * Simpan / Tambah Paket Mapel Soal
     */
    public function simpanPaket() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
            $topik_id = (int)($_POST['topik_id'] ?? 0);
            $mapel_nama = trim($_POST['mapel_nama'] ?? '');
            $kelas_nama = trim($_POST['kelas_nama'] ?? 'Semua Kelas');
            $guru_nama = trim($_POST['guru_nama'] ?? '');
            $kkm = !empty($_POST['kkm']) ? (int)$_POST['kkm'] : 75;
            $durasi_menit = !empty($_POST['durasi_menit']) ? (int)$_POST['durasi_menit'] : 90;

            if ($topik_id > 0 && !empty($mapel_nama)) {
                if ($id > 0) {
                    $stmt = $this->db->prepare("UPDATE bs_paket SET mapel_nama = ?, kelas_nama = ?, guru_nama = ?, kkm = ?, durasi_menit = ? WHERE id = ?");
                    $stmt->execute([$mapel_nama, $kelas_nama, $guru_nama, $kkm, $durasi_menit, $id]);
                } else {
                    $stmt = $this->db->prepare("INSERT INTO bs_paket (topik_id, mapel_nama, kelas_nama, guru_nama, kkm, durasi_menit, is_locked) VALUES (?, ?, ?, ?, ?, ?, 0)");
                    $stmt->execute([$topik_id, $mapel_nama, $kelas_nama, $guru_nama, $kkm, $durasi_menit]);
                }
                redirect("/bank_soal?topik_id={$topik_id}&msg=paket_saved");
                exit;
            }
        }
        redirect('/bank_soal');
    }

    /**
     * Hapus Paket Mapel & Soal di dalamnya
     */
    public function hapusPaket() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $paketId = (int)($_POST['paket_id'] ?? 0);
            $topikId = (int)($_POST['topik_id'] ?? 0);

            if ($paketId > 0) {
                // Cek apakah paket terkunci
                $stmtC = $this->db->prepare("SELECT is_locked FROM bs_paket WHERE id = ?");
                $stmtC->execute([$paketId]);
                $isLocked = (int)$stmtC->fetchColumn();

                if ($isLocked === 1) {
                    redirect("/bank_soal?topik_id={$topikId}&msg=error_locked");
                    exit;
                }

                $this->db->prepare("DELETE FROM bs_soal WHERE paket_id = ?")->execute([$paketId]);
                $this->db->prepare("DELETE FROM bs_paket WHERE id = ?")->execute([$paketId]);

                redirect("/bank_soal?topik_id={$topikId}&msg=paket_deleted");
                exit;
            }
        }
        redirect('/bank_soal');
    }

    /**
     * Kunci / Buka Kunci Paket Soal
     * Setelah dikunci, guru tidak bisa mengedit butir soal baik di portal maupun di APK
     */
    public function toggleLockPaket() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $paketId = (int)($_POST['paket_id'] ?? 0);
            $topikId = (int)($_POST['topik_id'] ?? 0);

            if ($paketId > 0) {
                $stmt = $this->db->prepare("SELECT is_locked, topik_id FROM bs_paket WHERE id = ?");
                $stmt->execute([$paketId]);
                $paket = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($paket) {
                    $curr = (int)$paket['is_locked'];
                    $newStatus = ($curr === 1) ? 0 : 1;
                    $now = ($newStatus === 1) ? date('Y-m-d H:i:s') : null;
                    $user = ($newStatus === 1) ? ($_SESSION['nama'] ?? 'Kurikulum / Admin') : null;

                    $stmtUp = $this->db->prepare("UPDATE bs_paket SET is_locked = ?, locked_at = ?, locked_by = ? WHERE id = ?");
                    $stmtUp->execute([$newStatus, $now, $user, $paketId]);

                    $tId = $topikId ?: $paket['topik_id'];
                    $msg = ($newStatus === 1) ? 'locked' : 'unlocked';
                    redirect("/bank_soal?topik_id={$tId}&msg={$msg}");
                    exit;
                }
            }
        }
        redirect('/bank_soal');
    }

    /**
     * Dapatkan kamus nama guru lengkap dari core dan siakad
     */
    public function getGuruDictionary() {
        $dict = [];
        try {
            $gCore = $this->db_core->query("SELECT id, nama FROM guru")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($gCore as $g) {
                if (!empty($g['nama'])) $dict[(int)$g['id']] = $g['nama'];
            }
        } catch (\Exception $e) {}

        try {
            $gSiakad = $this->db->query("SELECT id, nama_lengkap FROM guru")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($gSiakad as $g) {
                if (!empty($g['nama_lengkap']) && empty($dict[(int)$g['id']])) {
                    $dict[(int)$g['id']] = $g['nama_lengkap'];
                }
            }
        } catch (\Exception $e) {}

        // Fallback nama guru madrasah MAS Miftahul Huda
        $fallbacks = [
            4 => 'PUTRI NUR ANISA MEI, S.Pd',
            6 => 'M. Aziz Sulaiman, S.Pd',
            7 => 'SITI AISAH, S.Pd.I',
            8 => 'Zuli Astutik, S.Pd.I',
            9 => 'Siti Aisah, S.Pd.I',
            10 => 'Marlena, S.Pd.I',
            11 => 'Siti Roqaiyah, S.Pd.I',
            12 => 'Lilis Dwi Wati, S.Pd.I',
            13 => "Siti Mar'atus Sholehah, S.Pd",
            14 => 'M AZIZ SULAIMAN, S.Pd',
            15 => 'Samsul Arifin, M.Pd',
            16 => 'Siti Roqaiyah, S.Pd.I',
            17 => 'Didi Hartono, S.Pd.I',
            18 => 'Okta Kusuma W, S.Pd',
            37 => 'DIDI HARTONO, S.Pd.I',
            38 => 'M. Wahidin, S.Pd',
            39 => 'Drs. Hamim Hasyim',
            40 => 'SAIPUDIN AZIZ, S.Pd',
            41 => 'INDAH YUNITASARI, S.Pd',
            42 => 'M. Wahidin, S.Pd',
            43 => 'SYAIFUL BAHRI, S.Pd',
            44 => 'SAWALUDIN, S.Pd',
            45 => 'ERWANSAH, S.Pd',
            46 => 'ENDANG KURNIASIH, S.E.',
            47 => 'JULAINI, S.Pd',
            48 => 'SHOBIRIN'
        ];
        foreach ($fallbacks as $fid => $fname) {
            if (empty($dict[$fid])) {
                $dict[$fid] = $fname;
            }
        }
        return $dict;
    }

    /**
     * Otomatis ambil & buat paket mapel berdasarkan Guru Pengampu
     * Langsung muncul tanpa perlu menunggu jadwal ujian CBT selesai disetting
     */
    public function syncFromPengampu($topikId = null) {
        $topikId = $topikId ?: (isset($_POST['topik_id']) ? (int)$_POST['topik_id'] : (isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0));
        if ($topikId <= 0) {
            if (isset($_POST['topik_id']) || isset($_GET['topik_id'])) {
                redirect('/bank_soal');
                exit;
            }
            return 0;
        }

        $guruDict = $this->getGuruDictionary();

        // 1. Ambil dari tabel mengajar & jadwal_pelajaran yang memiliki guru_id
        $assigned = [];
        try {
            $sql = "
                SELECT DISTINCT m.mapel_id, mp.nama_mapel, m.guru_id
                FROM (
                    SELECT mapel_id, guru_id FROM mengajar WHERE guru_id IS NOT NULL AND guru_id > 0
                    UNION
                    SELECT mata_pelajaran_id as mapel_id, guru_id FROM jadwal_pelajaran WHERE guru_id IS NOT NULL AND guru_id > 0
                ) m
                JOIN mapel mp ON m.mapel_id = mp.id
                WHERE mp.kategori NOT IN ('Pembiasaan/Tambahan', 'Ekstrakurikuler')
                ORDER BY mp.nama_mapel ASC
            ";
            $assigned = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // 2. Pemetaan standar guru pengampu madrasah MAS Miftahul Huda
        $standardPengampu = [
            'Al-Qur\'an Hadits' => ['guru_id' => 14, 'guru_nama' => 'M AZIZ SULAIMAN, S.Pd'],
            'Akidah Akhlak' => ['guru_id' => 10, 'guru_nama' => 'Marlena, S.Pd.I'],
            'Fikih' => ['guru_id' => 7, 'guru_nama' => 'SITI AISAH, S.Pd.I'],
            'Sejarah Kebudayaan Islam' => ['guru_id' => 48, 'guru_nama' => 'SHOBIRIN'],
            'Bahasa Arab' => ['guru_id' => 37, 'guru_nama' => 'DIDI HARTONO, S.Pd.I'],
            'Bahasa Indonesia' => ['guru_id' => 41, 'guru_nama' => 'INDAH YUNITASARI, S.Pd'],
            'Matematika' => ['guru_id' => 14, 'guru_nama' => 'M AZIZ SULAIMAN, S.Pd'],
            'Ilmu Pengetahuan Alam Sosial' => ['guru_id' => 45, 'guru_nama' => 'ERWANSAH, S.Pd'],
            'Pendidikan Kewarganegaraan' => ['guru_id' => 44, 'guru_nama' => 'SAWALUDIN, S.Pd'],
            'Seni Budaya dan Prakarya' => ['guru_id' => 47, 'guru_nama' => 'JULAINI, S.Pd'],
            'Pendidikan Jasmani, Olahraga, dan Kesehatan' => ['guru_id' => 40, 'guru_nama' => 'SAIPUDIN AZIZ, S.Pd'],
            'Bahasa Inggris' => ['guru_id' => 42, 'guru_nama' => 'M. Wahidin, S.Pd'],
            'Informatika' => ['guru_id' => 14, 'guru_nama' => 'M AZIZ SULAIMAN, S.Pd'],
            'Sejarah Umum' => ['guru_id' => 43, 'guru_nama' => 'SYAIFUL BAHRI, S.Pd'],
            'Sosiologi' => ['guru_id' => 43, 'guru_nama' => 'SYAIFUL BAHRI, S.Pd'],
            'Geografi' => ['guru_id' => 43, 'guru_nama' => 'SYAIFUL BAHRI, S.Pd'],
            'Ekonomi' => ['guru_id' => 46, 'guru_nama' => 'ENDANG KURNIASIH, S.E.'],
            'Fisika' => ['guru_id' => 45, 'guru_nama' => 'ERWANSAH, S.Pd'],
            'Biologi' => ['guru_id' => 45, 'guru_nama' => 'ERWANSAH, S.Pd'],
            'Kimia' => ['guru_id' => 45, 'guru_nama' => 'ERWANSAH, S.Pd'],
            'Baca Tulis Qur\'an' => ['guru_id' => 14, 'guru_nama' => 'M AZIZ SULAIMAN, S.Pd'],
        ];

        $mapelsToInsert = [];
        foreach ($assigned as $row) {
            $mName = trim($row['nama_mapel']);
            $gId = (int)$row['guru_id'];
            $gName = $guruDict[$gId] ?? ($standardPengampu[$mName]['guru_nama'] ?? "Guru #{$gId}");
            if (!isset($mapelsToInsert[$mName])) {
                $mapelsToInsert[$mName] = [
                    'guru_id' => $gId,
                    'guru_nama' => $gName
                ];
            }
        }

        // Tambahkan mapel standar jika belum tercakup
        foreach ($standardPengampu as $defName => $defInfo) {
            if (!isset($mapelsToInsert[$defName])) {
                $mapelsToInsert[$defName] = $defInfo;
            }
        }

        $inserted = 0;
        $stmtCheck = $this->db->prepare("SELECT id FROM bs_paket WHERE topik_id = ? AND mapel_nama = ?");
        $stmtIns = $this->db->prepare("
            INSERT INTO bs_paket (topik_id, mapel_nama, kelas_nama, guru_id, guru_nama, kkm, durasi_menit, is_locked) 
            VALUES (?, ?, 'Semua Kelas', ?, ?, 75, 90, 0)
        ");

        foreach ($mapelsToInsert as $mName => $info) {
            $stmtCheck->execute([$topikId, $mName]);
            if (!$stmtCheck->fetchColumn()) {
                $stmtIns->execute([$topikId, $mName, $info['guru_id'], $info['guru_nama']]);
                $inserted++;
            }
        }

        if (isset($_POST['topik_id']) || isset($_GET['topik_id'])) {
            redirect("/bank_soal?topik_id={$topikId}&msg=synced_pengampu&count={$inserted}");
            exit;
        }

        return $inserted;
    }

    /**
     * Sinkronkan Paket Mapel dari Jadwal Ujian CBT yang sudah dibuat di /cbt/jadwal
     */
    public function syncFromJadwal() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $topikId = (int)($_POST['topik_id'] ?? 0);
            if ($topikId > 0) {
                // Ambil semua mapel unik dan kelas dari cbt_jadwal_grid
                $stmtJ = $this->db_core->prepare("
                    SELECT DISTINCT mapel_nama, kelas_nama, pengawas 
                    FROM cbt_jadwal_grid 
                    WHERE topik_id = ? AND mapel_nama IS NOT NULL AND mapel_nama != ''
                ");
                $stmtJ->execute([$topikId]);
                $scheduled = $stmtJ->fetchAll(PDO::FETCH_ASSOC);

                $inserted = 0;
                $stmtCheck = $this->db->prepare("SELECT id FROM bs_paket WHERE topik_id = ? AND mapel_nama = ? AND kelas_nama = ?");
                $stmtIns = $this->db->prepare("INSERT INTO bs_paket (topik_id, mapel_nama, kelas_nama, guru_nama, kkm, durasi_menit, is_locked) VALUES (?, ?, ?, ?, 75, 90, 0)");

                foreach ($scheduled as $sc) {
                    $stmtCheck->execute([$topikId, $sc['mapel_nama'], $sc['kelas_nama']]);
                    if (!$stmtCheck->fetchColumn()) {
                        $stmtIns->execute([$topikId, $sc['mapel_nama'], $sc['kelas_nama'], $sc['pengawas']]);
                        $inserted++;
                    }
                }

                redirect("/bank_soal?topik_id={$topikId}&msg=synced&count={$inserted}");
                exit;
            }
        }
        redirect('/bank_soal');
    }

    /**
     * Buat Contoh Dummy Realistis (Matematika: 50 Soal = 40 PG, 5 PG Kompleks, 5 Essay)
     * Sesuai permintaan eksplisit user untuk melihat preview detail jenis soal
     */
    public function generateDummy() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $topikId = (int)($_POST['topik_id'] ?? 0);
            if ($topikId === 0) {
                $tp = $this->db->query("SELECT id FROM bs_topik WHERE status = 'Aktif' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
                $topikId = $tp ? (int)$tp['id'] : 1;
            }

            // 1. Cek atau Buat Paket Matematika
            $stmtP = $this->db->prepare("SELECT id, kelas_nama FROM bs_paket WHERE topik_id = ? AND mapel_nama = 'Matematika' ORDER BY id ASC LIMIT 1");
            $stmtP->execute([$topikId]);
            $pktRow = $stmtP->fetch(PDO::FETCH_ASSOC);
            $paketId = $pktRow ? (int)$pktRow['id'] : 0;
            $kelasNamaTarget = $pktRow ? ($pktRow['kelas_nama'] ?: 'Semua Kelas') : 'Semua Kelas';

            if (!$paketId) {
                $stmtInsP = $this->db->prepare("INSERT INTO bs_paket (topik_id, mapel_nama, kelas_nama, guru_nama, kkm, durasi_menit, is_locked) VALUES (?, 'Matematika', 'Semua Kelas', 'M AZIZ SULAIMAN, S.Pd', 75, 90, 0)");
                $stmtInsP->execute([$topikId]);
                $paketId = $this->db->lastInsertId();
            }

            // Bersihkan butir soal lama di paket ini agar pas 50 soal
            $this->db->prepare("DELETE FROM bs_soal WHERE paket_id = ?")->execute([$paketId]);

            // 2. Masukkan 40 Butir Pilihan Ganda (PG)
            $samplePG = [
                ['Bentuk sederhana dari (2³ × 2⁴) : 2⁵ adalah...', '2', '4', '8', '16', '32', 'B'],
                ['Nilai dari logaritma ²log 16 + ²log 4 adalah...', '4', '5', '6', '8', '10', 'C'],
                ['Himpunan penyelesaian dari sistem persamaan 2x + y = 8 dan x - y = 1 adalah...', '{(3, 2)}', '{(2, 3)}', '{(4, 0)}', '{(1, 6)}', '{(5, -2)}', 'A'],
                ['Akar-akar dari persamaan kuadrat x² - 5x + 6 = 0 adalah...', 'x = 1 atau x = 6', 'x = 2 atau x = 3', 'x = -2 atau x = -3', 'x = -1 atau x = -6', 'x = 3 atau x = 5', 'B'],
                ['Fungsi f(x) = 2x² - 4x + 5 memiliki titik puncak pada koordinat...', '(1, 3)', '(2, 5)', '(-1, 11)', '(1, -3)', '(0, 5)', 'A'],
            ];

            $stmtSoal = $this->db->prepare("
                INSERT INTO bs_soal (topik_id, paket_id, mapel_nama, kelas_nama, nomor_urut, tipe_soal, tingkat_kesulitan, skor_maksimal, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, konten_tambahan)
                VALUES (?, ?, 'Matematika', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            for ($i = 1; $i <= 40; $i++) {
                $ref = $samplePG[($i - 1) % count($samplePG)];
                $pertanyaan = "Soal No. {$i}: " . $ref[0];
                $konten = json_encode(['kunci' => $ref[6], 'opsi' => ['A' => $ref[1], 'B' => $ref[2], 'C' => $ref[3], 'D' => $ref[4], 'E' => $ref[5]]]);
                $stmtSoal->execute([
                    $topikId, $paketId, $kelasNamaTarget, $i, 'sec_pg', ($i % 3 === 0 ? 'Sukar' : ($i % 2 === 0 ? 'Sedang' : 'Mudah')), 2.00,
                    $pertanyaan, $ref[1], $ref[2], $ref[3], $ref[4], $ref[5], $ref[6], $konten
                ]);
            }

            // 3. Masukkan 5 Butir PG Kompleks (PGK - Multi Jawaban)
            $samplePGK = [
                [
                    'Pilihlah seluruh pernyataan berikut yang BENAR mengenai sifat-sifat eksponen:',
                    [
                        ['teks' => 'a^m × a^n = a^(m+n)', 'is_kunci' => true],
                        ['teks' => '(a^m)^n = a^(m × n)', 'is_kunci' => true],
                        ['teks' => 'a^0 = 0 untuk a ≠ 0', 'is_kunci' => false],
                        ['teks' => 'a^(-n) = 1 / a^n', 'is_kunci' => true],
                    ],
                    json_encode(['A', 'B', 'D'])
                ],
                [
                    'Manakah dari fungsi-fungsi berikut yang merupakan fungsi kuadrat definit positif?',
                    [
                        ['teks' => 'f(x) = x² + 2x + 5', 'is_kunci' => true],
                        ['teks' => 'f(x) = 2x² - 4x + 7', 'is_kunci' => true],
                        ['teks' => 'f(x) = -x² + 3x - 4', 'is_kunci' => false],
                        ['teks' => 'f(x) = x² - 6x + 9', 'is_kunci' => false],
                    ],
                    json_encode(['A', 'B'])
                ],
                [
                    'Pernyataan yang tepat mengenai persamaan linear dua variabel 3x + 2y = 12 adalah:',
                    [
                        ['teks' => 'Memotong sumbu X di titik (4, 0)', 'is_kunci' => true],
                        ['teks' => 'Memotong sumbu Y di titik (0, 6)', 'is_kunci' => true],
                        ['teks' => 'Gradien garisnya adalah -3/2', 'is_kunci' => true],
                        ['teks' => 'Titik (2, 2) berada pada garis tersebut', 'is_kunci' => false],
                    ],
                    json_encode(['A', 'B', 'C'])
                ],
                [
                    'Pilihlah sudut-sudut istimewa yang memiliki nilai sinus positif:',
                    [
                        ['teks' => '30° (Kuadran I)', 'is_kunci' => true],
                        ['teks' => '150° (Kuadran II)', 'is_kunci' => true],
                        ['teks' => '210° (Kuadran III)', 'is_kunci' => false],
                        ['teks' => '330° (Kuadran IV)', 'is_kunci' => false],
                    ],
                    json_encode(['A', 'B'])
                ],
                [
                    'Tentukan pernyataan yang benar mengenai matriks persegi ordo 2x2 berinvers:',
                    [
                        ['teks' => 'Determinannya tidak sama dengan nol (det ≠ 0)', 'is_kunci' => true],
                        ['teks' => 'Perkalian matriks dengan inversnya menghasilkan matriks identitas', 'is_kunci' => true],
                        ['teks' => 'Matriks singular selalu memiliki matriks balikan', 'is_kunci' => false],
                        ['teks' => 'det(A⁻¹) = 1 / det(A)', 'is_kunci' => true],
                    ],
                    json_encode(['A', 'B', 'D'])
                ]
            ];

            for ($j = 0; $j < 5; $j++) {
                $noUrut = 41 + $j;
                $pgk = $samplePGK[$j];
                $konten = json_encode(['opsi' => $pgk[1], 'kunci_array' => $pgk[2]]);
                $stmtSoal->execute([
                    $topikId, $paketId, $kelasNamaTarget, $noUrut, 'sec_pgk', 'Sukar', 3.00,
                    "Soal No. {$noUrut} (PG Kompleks): " . $pgk[0],
                    $pgk[1][0]['teks'], $pgk[1][1]['teks'], $pgk[1][2]['teks'], $pgk[1][3]['teks'] ?? null, null,
                    $pgk[2], $konten
                ]);
            }

            // 4. Masukkan 5 Butir Essay (Uraian)
            $sampleEssay = [
                ['Tentukan nilai x yang memenuhi persamaan eksponen 3^(2x - 1) = 27!', 'Ubah 27 menjadi 3³. Maka 2x - 1 = 3 => 2x = 4 => x = 2. Skor maksimal jika langkah lengkap.'],
                ['Sebuah peluru ditembakkan ke atas dengan rumus ketinggian h(t) = 40t - 5t² meter. Tentukan tinggi maksimum yang dapat dicapai peluru tersebut!', 'Titik puncak t = -b/(2a) = -40 / (2 * -5) = 4 detik. h(4) = 40(4) - 5(16) = 160 - 80 = 80 meter.'],
                ['Diketahui deret aritmetika dengan suku pertama a = 5 dan beda b = 3. Tentukan jumlah 20 suku pertama (S20)!', 'S_n = n/2 * (2a + (n-1)b) => S20 = 10 * (10 + 19*3) = 10 * (10 + 57) = 10 * 67 = 670.'],
                ['Jelaskan langkah-langkah dalam menyelesaikan pertidaksamaan kuadrat x² - 4x - 5 ≤ 0!', '1. Faktorkan menjadi (x - 5)(x + 1) ≤ 0. 2. Pembuat nol x = 5 atau x = -1. 3. Uji titik pada garis bilangan. 4. HP = {-1 ≤ x ≤ 5}.'],
                ['Suatu modal sebesar Rp 10.000.000 dibungakan dengan bunga majemuk 5% per tahun. Hitunglah besar modal tersebut setelah 3 tahun!', 'M_n = M_0 * (1 + i)^n => M_3 = 10.000.000 * (1 + 0.05)³ = 10.000.000 * 1.157625 = Rp 11.576.250.']
            ];

            for ($k = 0; $k < 5; $k++) {
                $noUrut = 46 + $k;
                $ess = $sampleEssay[$k];
                $konten = json_encode(['rubrik' => $ess[1]]);
                $stmtSoal->execute([
                    $topikId, $paketId, $kelasNamaTarget, $noUrut, 'sec_essay', 'Sedang', 5.00,
                    "Soal No. {$noUrut} (Essay): " . $ess[0],
                    null, null, null, null, null,
                    $ess[1], $konten
                ]);
            }

            redirect("/bank_soal?topik_id={$topikId}&msg=dummy_created");
            exit;
        }
        redirect('/bank_soal');
    }

    /**
     * Kosongkan Bank Soal (Reset Data Soal & Paket)
     */
    public function resetData() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $topikId = isset($_POST['topik_id']) ? (int)$_POST['topik_id'] : 0;
            if ($topikId > 0) {
                $this->db->prepare("DELETE FROM bs_soal WHERE topik_id = ?")->execute([$topikId]);
                $this->db->prepare("DELETE FROM bs_paket WHERE topik_id = ?")->execute([$topikId]);
                redirect("/bank_soal?topik_id={$topikId}&msg=bank_reset");
                exit;
            } else {
                $this->db->exec("TRUNCATE TABLE bs_soal");
                $this->db->exec("TRUNCATE TABLE bs_paket");
                redirect("/bank_soal?msg=bank_reset_all");
                exit;
            }
        }
        redirect('/bank_soal');
    }

    /**
     * Tampilkan Daftar Soal pada Paket Tertentu
     */
    public function daftarSoal() {
        $paketId = isset($_GET['paket_id']) ? (int)$_GET['paket_id'] : 0;
        $topikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0;
        $mapelNama = isset($_GET['mapel']) ? trim($_GET['mapel']) : '';

        $paketInfo = null;
        if ($paketId > 0) {
            $stmtP = $this->db->prepare("SELECT * FROM bs_paket WHERE id = ?");
            $stmtP->execute([$paketId]);
            $paketInfo = $stmtP->fetch(PDO::FETCH_ASSOC);
            if ($paketInfo) {
                $topikId = (int)$paketInfo['topik_id'];
                $mapelNama = $paketInfo['mapel_nama'];
            }
        } elseif ($topikId > 0 && !empty($mapelNama)) {
            $stmtP = $this->db->prepare("SELECT * FROM bs_paket WHERE topik_id = ? AND mapel_nama = ? LIMIT 1");
            $stmtP->execute([$topikId, $mapelNama]);
            $paketInfo = $stmtP->fetch(PDO::FETCH_ASSOC);
            if ($paketInfo) {
                $paketId = (int)$paketInfo['id'];
            }
        }

        if ($topikId === 0) {
            redirect('/bank_soal');
            exit;
        }

        $stmtT = $this->db->prepare("SELECT * FROM bs_topik WHERE id = ?");
        $stmtT->execute([$topikId]);
        $topikInfo = $stmtT->fetch(PDO::FETCH_ASSOC);

        // Ambil semua butir soal
        if ($paketId > 0) {
            $stmtS = $this->db->prepare("SELECT * FROM bs_soal WHERE paket_id = ? ORDER BY nomor_urut ASC, id ASC");
            $stmtS->execute([$paketId]);
        } else {
            $stmtS = $this->db->prepare("SELECT * FROM bs_soal WHERE topik_id = ? AND mapel_nama = ? ORDER BY nomor_urut ASC, id ASC");
            $stmtS->execute([$topikId, $mapelNama]);
        }
        $soals = $stmtS->fetchAll(PDO::FETCH_ASSOC);

        // Breakdown jenis soal
        $breakdown = [];
        foreach ($soals as $s) {
            $t = $s['tipe_soal'];
            $breakdown[$t] = ($breakdown[$t] ?? 0) + 1;
        }

        $page_title = "Daftar Soal: " . ($mapelNama ?: 'Bank Soal');
        $active_page = "kelola_soal";
        include __DIR__ . '/../../resources/views/bank_soal/daftar_soal.php';
    }

    /**
     * Form Tambah / Edit Butir Soal
     */
    public function tambahSoal() {
        $paketId = isset($_GET['paket_id']) ? (int)$_GET['paket_id'] : 0;
        $topikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0;
        $mapelNama = isset($_GET['mapel']) ? trim($_GET['mapel']) : '';
        $editId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        $paketInfo = null;
        if ($paketId > 0) {
            $stmtP = $this->db->prepare("SELECT * FROM bs_paket WHERE id = ?");
            $stmtP->execute([$paketId]);
            $paketInfo = $stmtP->fetch(PDO::FETCH_ASSOC);
            if ($paketInfo) {
                $topikId = (int)$paketInfo['topik_id'];
                $mapelNama = $paketInfo['mapel_nama'];
            }
        }

        if ($paketInfo && !empty($paketInfo['is_locked'])) {
            redirect("/bank_soal/daftar_soal?paket_id={$paketId}&msg=error_locked");
            exit;
        }

        $stmtT = $this->db->prepare("SELECT * FROM bs_topik WHERE id = ?");
        $stmtT->execute([$topikId]);
        $topikInfo = $stmtT->fetch(PDO::FETCH_ASSOC);

        $soalEdit = null;
        if ($editId > 0) {
            $stmtE = $this->db->prepare("SELECT * FROM bs_soal WHERE id = ?");
            $stmtE->execute([$editId]);
            $soalEdit = $stmtE->fetch(PDO::FETCH_ASSOC);
        }

        $page_title = ($editId > 0 ? "Edit Butir Soal" : "Tambah Butir Soal") . " - " . $mapelNama;
        $active_page = "kelola_soal";
        include __DIR__ . '/../../resources/views/bank_soal/tambah_soal.php';
    }

    /**
     * Simpan / Perbarui Butir Soal
     */
    public function simpanSoal() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
            $topik_id = (int)($_POST['topik_id'] ?? 0);
            $paket_id = !empty($_POST['paket_id']) ? (int)$_POST['paket_id'] : null;
            $mapel_nama = trim($_POST['mapel_nama'] ?? '');
            $kelas_nama = trim($_POST['kelas_nama'] ?? '');
            $tipe_soal = trim($_POST['bentuk_soal'] ?? ($_POST['tipe_soal'] ?? 'sec_pg'));
            $tingkat_kesulitan = trim($_POST['tingkat_kesulitan'] ?? 'Sedang');
            $skor_maksimal = !empty($_POST['skor_maksimal']) ? (float)$_POST['skor_maksimal'] : 2.00;
            $pertanyaan = trim($_POST['pertanyaan'] ?? '');
            $nomor_urut = !empty($_POST['nomor_urut']) ? (int)$_POST['nomor_urut'] : 1;

            // Validasi kunci jika paket dikunci
            if ($paket_id) {
                $isLocked = (int)$this->db->query("SELECT is_locked FROM bs_paket WHERE id = {$paket_id}")->fetchColumn();
                if ($isLocked === 1) {
                    redirect("/bank_soal/daftar_soal?paket_id={$paket_id}&msg=error_locked");
                    exit;
                }
            }

            $opsi_a = $_POST['opsi_pg_A'] ?? ($_POST['opsi_a'] ?? null);
            $opsi_b = $_POST['opsi_pg_B'] ?? ($_POST['opsi_b'] ?? null);
            $opsi_c = $_POST['opsi_pg_C'] ?? ($_POST['opsi_c'] ?? null);
            $opsi_d = $_POST['opsi_pg_D'] ?? ($_POST['opsi_d'] ?? null);
            $opsi_e = $_POST['opsi_pg_E'] ?? ($_POST['opsi_e'] ?? null);
            $kunci_jawaban = '';
            $konten = [];

            if ($tipe_soal === 'sec_pg') {
                $kunci_jawaban = trim($_POST['kunci_pg'] ?? ($_POST['kunci_jawaban'] ?? 'A'));
                $konten['kunci'] = $kunci_jawaban;
                $konten['opsi'] = ['A' => $opsi_a, 'B' => $opsi_b, 'C' => $opsi_c, 'D' => $opsi_d, 'E' => $opsi_e];
            } elseif ($tipe_soal === 'sec_pgk') {
                $pernyataan = $_POST['pgk_pernyataan'] ?? [];
                $kunci = $_POST['kunci_pgk'] ?? [];
                $opsi = [];
                $kunciLabels = [];
                $letters = ['A', 'B', 'C', 'D', 'E', 'F'];
                foreach ($pernyataan as $idx => $teks) {
                    if (!empty(trim($teks))) {
                        $isKunci = in_array($idx, $kunci) || in_array($letters[$idx] ?? '', $kunci);
                        $opsi[] = ['teks' => $teks, 'is_kunci' => $isKunci];
                        if ($isKunci) {
                            $kunciLabels[] = $letters[$idx] ?? (string)($idx + 1);
                        }
                    }
                }
                $konten['opsi'] = $opsi;
                $konten['kunci_array'] = $kunciLabels;
                $kunci_jawaban = implode(', ', $kunciLabels);
            } elseif ($tipe_soal === 'sec_essay') {
                $kunci_jawaban = trim($_POST['rubrik'] ?? ($_POST['kunci_jawaban'] ?? ''));
                $konten['rubrik'] = $kunci_jawaban;
            } elseif ($tipe_soal === 'sec_bs') {
                $pernyataan = $_POST['bs_pernyataan'] ?? [];
                $jawaban = $_POST['bs_jawaban'] ?? [];
                $butir = [];
                foreach ($pernyataan as $idx => $teks) {
                    if (!empty(trim($teks))) {
                        $butir[] = ['teks' => $teks, 'kunci' => $jawaban[$idx] ?? 'Benar'];
                    }
                }
                $konten['butir'] = $butir;
                $kunci_jawaban = json_encode($jawaban);
            } elseif ($tipe_soal === 'sec_jodoh') {
                $premis = $_POST['jodoh_premis'] ?? [];
                $respon = $_POST['jodoh_respon'] ?? [];
                $pasangan = [];
                for ($p=0; $p<count($premis); $p++) {
                    if (!empty($premis[$p]) && !empty($respon[$p])) {
                        $pasangan[] = ['premis' => $premis[$p], 'respon' => $respon[$p]];
                    }
                }
                $konten['pasangan'] = $pasangan;
                $kunci_jawaban = json_encode($pasangan);
            }

            $konten_json = json_encode($konten);

            if ($id > 0) {
                $stmt = $this->db->prepare("
                    UPDATE bs_soal SET 
                        nomor_urut = ?, tipe_soal = ?, tingkat_kesulitan = ?, skor_maksimal = ?, pertanyaan = ?,
                        opsi_a = ?, opsi_b = ?, opsi_c = ?, opsi_d = ?, opsi_e = ?, kunci_jawaban = ?, konten_tambahan = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $nomor_urut, $tipe_soal, $tingkat_kesulitan, $skor_maksimal, $pertanyaan,
                    $opsi_a, $opsi_b, $opsi_c, $opsi_d, $opsi_e, $kunci_jawaban, $konten_json, $id
                ]);
            } else {
                $stmt = $this->db->prepare("
                    INSERT INTO bs_soal 
                    (topik_id, paket_id, mapel_nama, kelas_nama, nomor_urut, tipe_soal, tingkat_kesulitan, skor_maksimal, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, konten_tambahan) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $topik_id, $paket_id, $mapel_nama, $kelas_nama, $nomor_urut, $tipe_soal, $tingkat_kesulitan, $skor_maksimal, $pertanyaan,
                    $opsi_a, $opsi_b, $opsi_c, $opsi_d, $opsi_e, $kunci_jawaban, $konten_json
                ]);
            }

            $redirectUrl = $paket_id ? "/bank_soal/daftar_soal?paket_id={$paket_id}&msg=soal_saved" : "/bank_soal/daftar_soal?topik_id={$topik_id}&mapel=" . urlencode($mapel_nama) . "&msg=soal_saved";
            redirect($redirectUrl);
            exit;
        }
        redirect('/bank_soal');
    }

    /**
     * Hapus Butir Soal
     */
    public function hapusSoal() {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
        if ($id > 0) {
            $stmt = $this->db->prepare("SELECT topik_id, paket_id, mapel_nama FROM bs_soal WHERE id = ?");
            $stmt->execute([$id]);
            $soal = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($soal) {
                // Cek apakah paket terkunci
                if (!empty($soal['paket_id'])) {
                    $isLocked = (int)$this->db->query("SELECT is_locked FROM bs_paket WHERE id = " . (int)$soal['paket_id'])->fetchColumn();
                    if ($isLocked === 1) {
                        redirect("/bank_soal/daftar_soal?paket_id={$soal['paket_id']}&msg=error_locked");
                        exit;
                    }
                }

                $this->db->prepare("DELETE FROM bs_soal WHERE id = ?")->execute([$id]);
                $redirectUrl = !empty($soal['paket_id']) ? "/bank_soal/daftar_soal?paket_id={$soal['paket_id']}&msg=soal_deleted" : "/bank_soal/daftar_soal?topik_id={$soal['topik_id']}&mapel=" . urlencode($soal['mapel_nama']) . "&msg=soal_deleted";
                redirect($redirectUrl);
                exit;
            }
        }
        redirect('/bank_soal');
    }

    // --- Legacy Topic Actions (Kompatibilitas) ---
    public function indexTopik() {
        redirect('/cbt/topik');
    }
    public function simpanTopik() {
        CbtController::saveTopik();
    }
    public function hapusTopik() {
        CbtController::deleteTopik();
    }
    public function kelolaSoal() {
        $topikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0;
        redirect($topikId > 0 ? "/bank_soal?topik_id={$topikId}" : "/bank_soal");
    }
}
