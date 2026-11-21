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

            // 2. bs_paket (Paket Mapel & Tingkat per Topik)
            $db->exec("CREATE TABLE IF NOT EXISTS bs_paket (
                id INT AUTO_INCREMENT PRIMARY KEY,
                topik_id INT NOT NULL,
                mapel_nama VARCHAR(150) NOT NULL,
                kelas_nama VARCHAR(100) NOT NULL DEFAULT 'Kelas X',
                kelas_sasaran VARCHAR(255) DEFAULT NULL,
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
            if (!in_array('kelas_sasaran', $colsPaket)) {
                $db->exec("ALTER TABLE bs_paket ADD COLUMN kelas_sasaran VARCHAR(255) DEFAULT NULL AFTER kelas_nama");
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

            // Bersihkan data MI lama
            self::cleanMILeftovers($db);
        } catch (\Exception $e) {}
    }

    /**
     * Bersihkan sisa-sisa data MI (Madrasah Ibtidaiyah) agar tidak pernah muncul lagi di MAS MIFTAHUL HUDA
     */
    public static function cleanMILeftovers($db) {
        if (!$db) return;
        try {
            // 1. Hapus paket yang guru atau mapelnya merupakan data MI lama (SD/MI)
            // Catatan: ARI ROMADLON, S.Pd adalah guru resmi MA Miftahul Huda (Akidah Akhlak & Dirosah)
            $db->exec("DELETE FROM bs_paket WHERE 
                guru_nama LIKE '%Marlena%' OR 
                guru_nama LIKE '%Zuli%' OR 
                guru_nama LIKE '%Astutik%' OR 
                guru_nama LIKE '%Lilis%' OR 
                guru_nama LIKE '%Samsul%' OR 
                guru_nama LIKE '%Roqaiyah%' OR 
                guru_nama LIKE '%Shoha%' OR 
                guru_nama LIKE '%Asatidz%' OR 
                guru_nama LIKE '%Okta Kusuma%' OR 
                mapel_nama LIKE '%Baca Tulis Qur\'an%' OR 
                mapel_nama LIKE '%BTQ%' OR 
                mapel_nama LIKE '%Ilmu Pengetahuan Alam Sosial%' OR 
                mapel_nama LIKE '%IPAS%' OR 
                mapel_nama LIKE '%Seni Budaya dan Prakarya%' OR 
                mapel_nama LIKE '%SBdP%' OR 
                kelas_nama = 'Semua Kelas' OR
                kelas_nama IS NULL OR
                kelas_nama = ''
            ");

            // 2. Hapus butir soal yang mapelnya data MI lama
            $db->exec("DELETE FROM bs_soal WHERE 
                mapel_nama LIKE '%Baca Tulis Qur\'an%' OR 
                mapel_nama LIKE '%BTQ%' OR 
                mapel_nama LIKE '%Ilmu Pengetahuan Alam Sosial%' OR 
                mapel_nama LIKE '%IPAS%' OR 
                mapel_nama LIKE '%Seni Budaya dan Prakarya%' OR 
                mapel_nama LIKE '%SBdP%' OR 
                kelas_nama = 'Semua Kelas' OR
                kelas_nama IS NULL OR
                kelas_nama = ''
            ");

            // 3. Hapus orphaned butir soal yang paket_id nya sudah tidak ada di bs_paket
            $db->exec("DELETE FROM bs_soal WHERE paket_id IS NOT NULL AND paket_id NOT IN (SELECT id FROM bs_paket)");
        } catch (\Exception $e) {}
    }

    /**
     * Konversi nama kelas / tingkat rombel menjadi label Tingkat Ujian resmi (Kelas X, Kelas XI, Kelas XII)
     */
    public static function determineTingkat($namaKelas, $tingkat = null) {
        $nk = trim((string)$namaKelas);
        $t = (int)$tingkat;

        if ($t === 10 || stripos($nk, 'X ') === 0 || $nk === 'X' || stripos($nk, 'Kelas X') !== false || stripos($nk, '10') !== false) {
            return 'Kelas X';
        }
        if ($t === 11 || stripos($nk, 'XI ') === 0 || $nk === 'XI' || stripos($nk, 'Kelas XI') !== false || stripos($nk, '11') !== false) {
            return 'Kelas XI';
        }
        if ($t === 12 || stripos($nk, 'XII ') === 0 || $nk === 'XII' || stripos($nk, 'Kelas XII') !== false || stripos($nk, '12') !== false) {
            return 'Kelas XII';
        }
        return !empty($nk) ? $nk : 'Kelas X';
    }

    /**
     * Standard resmi 19 Mata Pelajaran MAS Miftahul Huda beserta Guru Pengampu MAS
     */
    public static function getOfficialMasMapelList() {
        return [
            'Al-Qur\'an Hadits' => ['guru_id' => 39, 'guru_nama' => 'Drs. Hamim Hasyim'],
            'Akidah Akhlak' => ['guru_id' => 10, 'guru_nama' => 'ARI ROMADLON, S.Pd'],
            'Aqidah Akhlak' => ['guru_id' => 10, 'guru_nama' => 'ARI ROMADLON, S.Pd'],
            'Fikih' => ['guru_id' => 7, 'guru_nama' => 'SITI AISAH, S.Pd.I'],
            'Sejarah Kebudayaan Islam (SKI)' => ['guru_id' => 48, 'guru_nama' => 'SHOBIRIN'],
            'Bahasa Arab' => ['guru_id' => 37, 'guru_nama' => 'DIDI HARTONO, S.Pd.I'],
            'Bahasa Indonesia' => ['guru_id' => 41, 'guru_nama' => 'INDAH YUNITASARI, S.Pd'],
            'Bahasa Inggris' => ['guru_id' => 42, 'guru_nama' => 'M. Wahidin, S.Pd'],
            'Matematika' => ['guru_id' => 14, 'guru_nama' => 'M AZIZ SULAIMAN, S.Pd'],
            'Informatika' => ['guru_id' => 14, 'guru_nama' => 'M AZIZ SULAIMAN, S.Pd'],
            'Fisika' => ['guru_id' => 45, 'guru_nama' => 'ERWANSAH, S.Pd'],
            'Kimia' => ['guru_id' => 45, 'guru_nama' => 'ERWANSAH, S.Pd'],
            'Biologi' => ['guru_id' => 45, 'guru_nama' => 'ERWANSAH, S.Pd'],
            'Sosiologi' => ['guru_id' => 43, 'guru_nama' => 'SYAIFUL BAHRI, S.Pd'],
            'Geografi' => ['guru_id' => 43, 'guru_nama' => 'SYAIFUL BAHRI, S.Pd'],
            'Ekonomi' => ['guru_id' => 46, 'guru_nama' => 'ENDANG KURNIASIH, S.E.'],
            'Sejarah Umum' => ['guru_id' => 43, 'guru_nama' => 'SYAIFUL BAHRI, S.Pd'],
            'Pendidikan Pancasila & Kewarganegaraan (PPKn)' => ['guru_id' => 44, 'guru_nama' => 'SAWALUDIN, S.Pd'],
            'Pendidikan Jasmani, Olahraga & Kesehatan (PJOK)' => ['guru_id' => 40, 'guru_nama' => 'SAIPUDIN AZIZ, S.Pd'],
            'Seni Budaya' => ['guru_id' => 47, 'guru_nama' => 'JULAINI, S.Pd'],
        ];
    }

    /**
     * Dashboard Bank Soal Saya khusus untuk Guru Pengampu
     * Menampilkan paket mapel dan kelas yang diampu guru yang sedang login
     */
    public function guruIndex() {
        if (!isset($_SESSION['user_id'])) {
            redirect('/login');
            exit;
        }

        $userId = (int)$_SESSION['user_id'];
        $roleId = isset($_SESSION['role_id']) ? (int)$_SESSION['role_id'] : 0;

        // Ambil data guru dari user_id
        $guru = $this->db_core->query("SELECT id, nama FROM guru WHERE user_id = {$userId} LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        $guruId = $guru ? (int)$guru['id'] : 0;
        $guruNama = $guru ? trim($guru['nama']) : ($_SESSION['nama'] ?? '');

        // 1. Ambil daftar Topik Ujian
        $topikList = $this->db->query("SELECT * FROM bs_topik ORDER BY (status = 'Aktif') DESC, id DESC")->fetchAll(PDO::FETCH_ASSOC);
        if (empty($topikList)) {
            $topikList = [
                ['id' => 1, 'nama_topik' => 'Sumatif Akhir Semester (SAS) Ganjil 2026/2027', 'jenis' => 'SAS', 'status' => 'Aktif']
            ];
        }

        $selectedTopikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : (int)$topikList[0]['id'];

        // Cek apakah ada paket legacy rombel lama, jika ya sinkronkan ke Tingkat
        $hasLegacy = (bool)$this->db->query("SELECT COUNT(*) FROM bs_paket WHERE topik_id = {$selectedTopikId} AND kelas_nama NOT IN ('Kelas X', 'Kelas XI', 'Kelas XII')")->fetchColumn();
        if ($hasLegacy) {
            $this->syncFromPengampu($selectedTopikId, true);
        }

        // 2. Ambil paket yang diampu guru ini untuk topik yang dipilih
        $stmtP = $this->db->prepare("
            SELECT * FROM bs_paket 
            WHERE topik_id = ? 
              AND (guru_id = ? OR guru_nama = ? OR guru_nama LIKE ?)
            ORDER BY kelas_nama ASC, mapel_nama ASC
        ");
        $stmtP->execute([$selectedTopikId, $guruId, $guruNama, '%' . $guruNama . '%']);
        $myPackages = $stmtP->fetchAll(PDO::FETCH_ASSOC);

        // Jika masih kosong, coba sinkronkan dari mengajar jika ada
        if (empty($myPackages)) {
            $this->syncFromPengampu($selectedTopikId, true);
            $stmtP->execute([$selectedTopikId, $guruId, $guruNama, '%' . $guruNama . '%']);
            $myPackages = $stmtP->fetchAll(PDO::FETCH_ASSOC);
        }

        // Hitung total butir soal dan rincian per jenis soal
        $totalMySoal = 0;
        $totalMyLocked = 0;
        foreach ($myPackages as &$pk) {
            $pId = (int)$pk['id'];
            $breakdown = [];
            $tot = 0;
            try {
                $stmtB = $this->db->prepare("SELECT tipe_soal, COUNT(*) as jml FROM bs_soal WHERE paket_id = ? GROUP BY tipe_soal");
                $stmtB->execute([$pId]);
                foreach ($stmtB->fetchAll(PDO::FETCH_ASSOC) as $br) {
                    $breakdown[$br['tipe_soal']] = (int)$br['jml'];
                    $tot += (int)$br['jml'];
                }
            } catch (\Exception $e) {}
            $pk['total_soal'] = $tot;
            $pk['breakdown'] = $breakdown;
            $totalMySoal += $tot;
            if (!empty($pk['is_locked'])) {
                $totalMyLocked++;
            }
        }
        unset($pk);

        include __DIR__ . '/../../resources/views/portal/guru/bank_soal_index.php';
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

        // Jika Guru mengakses, arahkan ke Bank Soal Guru
        if (isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 2) {
            $this->guruIndex();
            return;
        }

        $topikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0;

        // Ambil semua topik dari bs_topik
        $stmtTopik = $this->db->query("SELECT * FROM bs_topik ORDER BY (tgl_mulai IS NOT NULL) DESC, tgl_mulai ASC, id DESC");
        $allTopikList = $stmtTopik ? $stmtTopik->fetchAll(PDO::FETCH_ASSOC) : [];

        // Pastikan sisa data MI dibersihkan seketika
        self::cleanMILeftovers($this->db);
        if ($this->db_core !== $this->db) {
            self::cleanMILeftovers($this->db_core);
        }

        // Otomatis bersihkan orphan bs_paket jika ada topik lama/terhapus
        try {
            $this->db->exec("DELETE FROM bs_paket WHERE topik_id NOT IN (SELECT id FROM bs_topik) OR topik_id IS NULL OR topik_id = 0");
            if ($this->db_core !== $this->db) {
                $this->db_core->exec("DELETE FROM bs_paket WHERE topik_id NOT IN (SELECT id FROM bs_topik) OR topik_id IS NULL OR topik_id = 0");
            }
        } catch (\Exception $e) {}

        // Hitung statistik untuk masing-masing topik
        foreach ($allTopikList as &$tp) {
            $tId = (int)$tp['id'];
            $tp['jml_paket'] = (int)$this->db->query("SELECT COUNT(*) FROM bs_paket WHERE topik_id = {$tId}")->fetchColumn();
            $tp['jml_soal'] = (int)$this->db->query("SELECT COUNT(*) FROM bs_soal WHERE topik_id = {$tId}")->fetchColumn();
            $tp['jml_locked'] = (int)$this->db->query("SELECT COUNT(*) FROM bs_paket WHERE topik_id = {$tId} AND is_locked = 1")->fetchColumn();
        }
        unset($tp);

        // Hitung statistik global real (sinkron 100% dengan total dari topik yang ada)
        $totalTopik = count($allTopikList);
        $totalPaket = array_sum(array_column($allTopikList, 'jml_paket'));
        $totalSoal = array_sum(array_column($allTopikList, 'jml_soal'));
        $totalLocked = array_sum(array_column($allTopikList, 'jml_locked'));

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
                // Pastikan sisa data MI dibersihkan seketika
                self::cleanMILeftovers($this->db);
                if ($this->db_core !== $this->db) {
                    self::cleanMILeftovers($this->db_core);
                }

                // Ambil daftar paket mapel untuk topik ini
                $stmtP = $this->db->prepare("SELECT * FROM bs_paket WHERE topik_id = ? ORDER BY kelas_nama ASC, mapel_nama ASC");
                $stmtP->execute([$topikId]);
                $paketList = $stmtP->fetchAll(PDO::FETCH_ASSOC);

                // Cek apakah masih ada paket rombel lama (bukan 'Kelas X', 'Kelas XI', 'Kelas XII')
                $hasLegacyPaket = false;
                foreach ($paketList as $p) {
                    if (!in_array($p['kelas_nama'], ['Kelas X', 'Kelas XI', 'Kelas XII'])) {
                        $hasLegacyPaket = true;
                        break;
                    }
                }

                // Cek apakah Akidah Akhlak / Aqidah Akhlak sudah ada di paketList
                $hasAkidah = false;
                foreach ($paketList as $p) {
                    if (stripos($p['mapel_nama'], 'akidah') !== false || stripos($p['mapel_nama'], 'aqidah') !== false) {
                        $hasAkidah = true;
                        break;
                    }
                }

                // JIKA BELUM ADA PAKET MAPEL / MASIH ADA PAKET ROMBEL LAMA / AKIDAH BELUM TERSINKRON, OTOMATIS LANGSUNG SINKRONKAN!
                if (empty($paketList) || $hasLegacyPaket || !$hasAkidah) {
                    $this->syncFromPengampu($topikId, true);
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
                ['id' => 7, 'nama' => 'SITI AISAH, S.Pd.I'],
                ['id' => 14, 'nama' => 'M AZIZ SULAIMAN, S.Pd'],
                ['id' => 37, 'nama' => 'DIDI HARTONO, S.Pd.I'],
                ['id' => 39, 'nama' => 'Drs. Hamim Hasyim'],
                ['id' => 40, 'nama' => 'SAIPUDIN AZIZ, S.Pd'],
                ['id' => 41, 'nama' => 'INDAH YUNITASARI, S.Pd'],
                ['id' => 42, 'nama' => 'M. Wahidin, S.Pd'],
                ['id' => 43, 'nama' => 'SYAIFUL BAHRI, S.Pd'],
                ['id' => 44, 'nama' => 'SAWALUDIN, S.Pd'],
                ['id' => 45, 'nama' => 'ERWANSAH, S.Pd'],
                ['id' => 46, 'nama' => 'ENDANG KURNIASIH, S.E.'],
                ['id' => 47, 'nama' => 'JULAINI, S.Pd'],
                ['id' => 48, 'nama' => 'SHOBIRIN']
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
            $kelas_nama = trim($_POST['kelas_nama'] ?? 'Kelas X');
            $kelas_sasaran = trim($_POST['kelas_sasaran'] ?? '');
            if (empty($kelas_sasaran)) {
                if ($kelas_nama === 'Kelas X') $kelas_sasaran = 'X E.1';
                elseif ($kelas_nama === 'Kelas XI') $kelas_sasaran = 'XI F.1';
                elseif ($kelas_nama === 'Kelas XII') $kelas_sasaran = 'XII F.1, XII F.2';
            }
            $guru_nama = trim($_POST['guru_nama'] ?? '');
            $kkm = !empty($_POST['kkm']) ? (int)$_POST['kkm'] : 75;
            $durasi_menit = !empty($_POST['durasi_menit']) ? (int)$_POST['durasi_menit'] : 90;

            if ($topik_id > 0 && !empty($mapel_nama)) {
                if ($id > 0) {
                    $stmt = $this->db->prepare("UPDATE bs_paket SET mapel_nama = ?, kelas_nama = ?, kelas_sasaran = ?, guru_nama = ?, kkm = ?, durasi_menit = ? WHERE id = ?");
                    $stmt->execute([$mapel_nama, $kelas_nama, $kelas_sasaran, $guru_nama, $kkm, $durasi_menit, $id]);
                } else {
                    $stmt = $this->db->prepare("INSERT INTO bs_paket (topik_id, mapel_nama, kelas_nama, kelas_sasaran, guru_nama, kkm, durasi_menit, is_locked) VALUES (?, ?, ?, ?, ?, ?, ?, 0)");
                    $stmt->execute([$topik_id, $mapel_nama, $kelas_nama, $kelas_sasaran, $guru_nama, $kkm, $durasi_menit]);
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
     * Dapatkan kamus nama guru resmi MAS MIFTAHUL HUDA
     */
    public function getGuruDictionary() {
        $dict = [];
        try {
            $gCore = $this->db_core->query("SELECT id, nama FROM guru ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($gCore as $g) {
                if (!empty($g['nama'])) $dict[(int)$g['id']] = $g['nama'];
            }
        } catch (\Exception $e) {}

        // Fallback 13 Guru Resmi MAS Miftahul Huda
        $fallbacks = [
            7 => 'SITI AISAH, S.Pd.I',
            14 => 'M AZIZ SULAIMAN, S.Pd',
            37 => 'DIDI HARTONO, S.Pd.I',
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
     * Otomatis ambil & buat paket mapel berdasarkan penugasan mengajar per kelas dari tabel mengajar
     * Setiap kelas memiliki paket soalnya masing-masing sesuai guru pengampu riilnya
     */
    public function syncFromPengampu($topikId = null, $isInternal = false) {
        $topikId = $topikId ?: (isset($_POST['topik_id']) ? (int)$_POST['topik_id'] : (isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0));
        if ($topikId <= 0) {
            if (!$isInternal && (isset($_POST['topik_id']) || isset($_GET['topik_id']))) {
                redirect('/bank_soal');
                exit;
            }
            return 0;
        }

        // 1. Bersihkan sisa data MI dan paket legacy 'Semua Kelas'
        self::cleanMILeftovers($this->db);
        if ($this->db_core !== $this->db) {
            self::cleanMILeftovers($this->db_core);
        }

        // Hapus paket legacy yang kelas sasaran masih 'Semua Kelas' serta Bahasa Arab Peminatan
        $this->db->prepare("DELETE FROM bs_paket WHERE topik_id = ? AND (kelas_nama = 'Semua Kelas' OR kelas_nama IS NULL OR kelas_nama = '' OR mapel_nama LIKE '%Peminatan%')")->execute([$topikId]);
        $this->db->prepare("DELETE FROM bs_soal WHERE topik_id = ? AND mapel_nama LIKE '%Peminatan%'")->execute([$topikId]);
        if ($this->db_core !== $this->db) {
            $this->db_core->prepare("DELETE FROM bs_paket WHERE topik_id = ? AND (kelas_nama = 'Semua Kelas' OR kelas_nama IS NULL OR kelas_nama = '' OR mapel_nama LIKE '%Peminatan%')")->execute([$topikId]);
            $this->db_core->prepare("DELETE FROM bs_soal WHERE topik_id = ? AND mapel_nama LIKE '%Peminatan%'")->execute([$topikId]);
        }

        // 2. Ambil referensi nama guru dari database core & siakad
        $guruMap = [];
        try {
            $gRows = $this->db_core->query("SELECT id, nama FROM guru")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($gRows as $gr) {
                if (!empty($gr['nama'])) $guruMap[(int)$gr['id']] = trim($gr['nama']);
            }
        } catch (\Exception $e) {}
        try {
            $gSiakadRows = $this->db->query("SELECT id, COALESCE(nama_lengkap, nama) as nama FROM guru")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($gSiakadRows as $gr) {
                if (empty($guruMap[(int)$gr['id']]) && !empty($gr['nama'])) {
                    $guruMap[(int)$gr['id']] = trim($gr['nama']);
                }
            }
        } catch (\Exception $e) {}

        // Ambil penugasan mengajar riil dari tabel mengajar
        $assignments = [];
        try {
            $thn_id = 2;
            $smt = 1;
            if (class_exists('\App\Core\AcademicYear')) {
                $activeYear = \App\Core\AcademicYear::current();
                $thn_id = $activeYear['id'] ?? 2;
                $smt = $activeYear['semester'] ?? 1;
            }

            $sql = "
                SELECT DISTINCT mp.nama_mapel, k.nama_kelas, m.guru_id
                FROM mengajar m
                JOIN kelas k ON m.kelas_id = k.id
                JOIN mapel mp ON m.mapel_id = mp.id
                WHERE m.guru_id IS NOT NULL AND m.guru_id > 0
                  AND (m.tahun_ajaran_id = :thn_id OR m.tahun_ajaran_id IS NULL)
                  AND (m.semester = :smt OR m.semester IS NULL OR m.semester = 0)
                  AND mp.nama_mapel NOT IN ('Upacara', 'Senam', 'Pramuka', 'Sholat Dhuha', 'Juz Amma - Asmaul Husna', 'BTQ', 'IPAS', 'SBdP', 'Baca Tulis Qur\'an', 'Ilmu Pengetahuan Alam Sosial', 'Seni Budaya dan Prakarya')
                  AND mp.nama_mapel NOT LIKE '%Peminatan%'
                ORDER BY mp.nama_mapel ASC, k.nama_kelas ASC
            ";
            $stmtM = $this->db->prepare($sql);
            $stmtM->execute([':thn_id' => $thn_id, ':smt' => $smt]);
            $assignments = $stmtM->fetchAll(PDO::FETCH_ASSOC);

            // Jika kosong dengan filter TA/Semester, ambil tanpa filter TA/Semester
            if (empty($assignments)) {
                $sqlFallback = "
                    SELECT DISTINCT mp.nama_mapel, k.nama_kelas, m.guru_id
                    FROM mengajar m
                    JOIN kelas k ON m.kelas_id = k.id
                    JOIN mapel mp ON m.mapel_id = mp.id
                    WHERE m.guru_id IS NOT NULL AND m.guru_id > 0
                      AND mp.nama_mapel NOT IN ('Upacara', 'Senam', 'Pramuka', 'Sholat Dhuha', 'Juz Amma - Asmaul Husna', 'BTQ', 'IPAS', 'SBdP', 'Baca Tulis Qur\'an', 'Ilmu Pengetahuan Alam Sosial', 'Seni Budaya dan Prakarya')
                  AND mp.nama_mapel NOT LIKE '%Peminatan%'
                   AND mp.nama_mapel NOT LIKE '%Peminatan%'
                    ORDER BY mp.nama_mapel ASC, k.nama_kelas ASC
                ";
                $assignments = $this->db->query($sqlFallback)->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (\Exception $e) {}

        // Fallback jika tabel mengajar kosong
        if (empty($assignments)) {
            $masMapel = self::getOfficialMasMapelList();
            $tingkatList = [
                'Kelas X' => ['X E.1'],
                'Kelas XI' => ['XI F.1'],
                'Kelas XII' => ['XII F.1', 'XII F.2']
            ];
            foreach ($masMapel as $mName => $info) {
                foreach ($tingkatList as $tKls => $sasList) {
                    $key = $mName . '||' . $tKls;
                    $grouped[$key] = [
                        'mapel_nama' => $mName,
                        'kelas_nama' => $tKls,
                        'sasaran_list' => $sasList,
                        'guru_id' => $info['guru_id'],
                        'guru_nama' => $info['guru_nama']
                    ];
                }
            }
        } else {
            // Group assignments by (Mapel + Tingkat)
            foreach ($assignments as $asg) {
                $mName = trim($asg['nama_mapel']);
                if ($mName === 'Aqidah Akhlak') $mName = 'Akidah Akhlak';
                if ($mName === 'Al-Qur\'an Hadist') $mName = 'Al-Qur\'an Hadits';
                if ($mName === 'Penjaskes') $mName = 'Pendidikan Jasmani, Olahraga & Kesehatan (PJOK)';
                if ($mName === 'PPKn') $mName = 'Pendidikan Pancasila & Kewarganegaraan (PPKn)';

                $kName = trim($asg['nama_kelas']);
                $tingkat = $asg['tingkat'] ?? null;
                $tingkatLabel = self::determineTingkat($kName, $tingkat);

                $key = $mName . '||' . $tingkatLabel;
                if (!isset($grouped[$key])) {
                    $gId = (int)$asg['guru_id'];
                    $gNama = $guruMap[$gId] ?? '';
                    if (empty($gNama) && (stripos($mName, 'akidah') !== false || stripos($mName, 'aqidah') !== false)) {
                        $gNama = 'ARI ROMADLON, S.Pd';
                        $gId = 10;
                    }
                    $grouped[$key] = [
                        'mapel_nama' => $mName,
                        'kelas_nama' => $tingkatLabel,
                        'sasaran_list' => [],
                        'guru_id' => $gId,
                        'guru_nama' => $gNama
                    ];
                }
                if (!empty($kName) && !in_array($kName, $grouped[$key]['sasaran_list'])) {
                    $grouped[$key]['sasaran_list'][] = $kName;
                }
            }
        }

        // Pastikan Akidah Akhlak terdaftar untuk setiap tingkat sasaran MAS Miftahul Huda
        $tingkatAkidahList = [
            'Kelas X' => ['X E.1'],
            'Kelas XI' => ['XI F.1'],
            'Kelas XII' => ['XII F.1', 'XII F.2']
        ];
        foreach ($tingkatAkidahList as $tKls => $sasList) {
            $key = 'Akidah Akhlak||' . $tKls;
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'mapel_nama' => 'Akidah Akhlak',
                    'kelas_nama' => $tKls,
                    'sasaran_list' => $sasList,
                    'guru_id' => 10,
                    'guru_nama' => 'ARI ROMADLON, S.Pd'
                ];
            }
        }

        $inserted = 0;
        $stmtCheck = $this->db->prepare("SELECT id FROM bs_paket WHERE topik_id = ? AND mapel_nama = ? AND kelas_nama = ?");
        $stmtIns = $this->db->prepare("
            INSERT INTO bs_paket (topik_id, mapel_nama, kelas_nama, kelas_sasaran, guru_id, guru_nama, kkm, durasi_menit, is_locked) 
            VALUES (?, ?, ?, ?, ?, ?, 75, 90, 0)
        ");
        $stmtUpd = $this->db->prepare("UPDATE bs_paket SET kelas_sasaran = ?, guru_id = ?, guru_nama = ? WHERE id = ?");

        foreach ($grouped as $item) {
            $mName = $item['mapel_nama'];
            $kName = $item['kelas_nama'];
            $sasaranStr = implode(', ', $item['sasaran_list']);
            $gId = $item['guru_id'];
            $gName = $item['guru_nama'];

            $stmtCheck->execute([$topikId, $mName, $kName]);
            $existingId = $stmtCheck->fetchColumn();
            if (!$existingId) {
                $stmtIns->execute([$topikId, $mName, $kName, $sasaranStr, $gId, $gName]);
                $inserted++;
            } else {
                $stmtUpd->execute([$sasaranStr, $gId, $gName, $existingId]);
            }
        }

        // Pindahkan butir soal dari paket lama (rombel) ke paket Tingkat yang baru, lalu bersihkan paket rombel legacy
        try {
            $oldPakets = $this->db->query("SELECT id, mapel_nama, kelas_nama FROM bs_paket WHERE topik_id = {$topikId} AND kelas_nama NOT IN ('Kelas X', 'Kelas XI', 'Kelas XII')")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($oldPakets as $op) {
                $tingkat = self::determineTingkat($op['kelas_nama']);
                $findTingkatId = $this->db->query("SELECT id FROM bs_paket WHERE topik_id = {$topikId} AND mapel_nama = " . $this->db->quote($op['mapel_nama']) . " AND kelas_nama = " . $this->db->quote($tingkat))->fetchColumn();
                if ($findTingkatId) {
                    $this->db->exec("UPDATE bs_soal SET paket_id = {$findTingkatId}, kelas_nama = " . $this->db->quote($tingkat) . " WHERE paket_id = {$op['id']}");
                }
                $this->db->exec("DELETE FROM bs_paket WHERE id = {$op['id']}");
            }
        } catch (\Exception $e) {}

        if (!$isInternal && (isset($_POST['topik_id']) || isset($_GET['topik_id']))) {
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
     * Buat Contoh Dummy Realistis (Matematika, Biologi, Bahasa Arab: 10 Butir AKM per Mapel)
     * Sesuai permintaan user: Hapus data lama & buat tepat 10 soal dengan 5 model soal AKM
     */
    public function generateDummy() {
        $topikId = (int)($_POST['topik_id'] ?? $_GET['topik_id'] ?? 0);
        if ($topikId === 0) {
            $tp = $this->db->query("SELECT id FROM bs_topik WHERE nama_topik LIKE '%SIMULASI%' ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            if (!$tp) {
                $tp = $this->db->query("SELECT id FROM bs_topik WHERE status = 'Aktif' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            }
            $topikId = $tp ? (int)$tp['id'] : 1;
        }

        // 1. Hapus semua data butir soal lama di topik simulasi ini agar bersih total
        try {
            $this->db->prepare("DELETE FROM bs_soal WHERE topik_id = ?")->execute([$topikId]);
            if ($this->db_core !== $this->db) {
                $this->db_core->prepare("DELETE FROM bs_soal WHERE topik_id = ?")->execute([$topikId]);
            }
            $uIds = $this->db_core->query("SELECT id FROM cbt_ujian WHERE topik_id = {$topikId}")->fetchAll(PDO::FETCH_COLUMN);
            if (!empty($uIds)) {
                $inU = implode(',', array_map('intval', $uIds));
                $this->db_core->exec("DELETE FROM cbt_soal WHERE ujian_id IN ({$inU})");
                $this->db_core->exec("DELETE FROM cbt_peserta_jawaban WHERE ujian_id IN ({$inU})");
                $this->db_core->exec("DELETE FROM cbt_jawaban WHERE ujian_id IN ({$inU})");
                $this->db_core->exec("DELETE FROM cbt_peserta WHERE ujian_id IN ({$inU})");
            }
        } catch (\Exception $e) {}

        // 2. Panggil CbtDummyGenerator untuk generate 10 butir soal Matematika (5 model soal AKM)
        if (class_exists('\\App\\Services\\CbtDummyGenerator')) {
            \App\Services\CbtDummyGenerator::generateAll($topikId);
        }

        // 3. Panggil CbtBiologiArabGenerator untuk generate 10 butir Biologi & 10 butir Bahasa Arab (5 model soal AKM)
        if (class_exists('\\App\\Services\\CbtBiologiArabGenerator')) {
            \App\Services\CbtBiologiArabGenerator::generateAll($topikId);
        }

        // 4. Sinkronkan langsung dengan jadwal dan ujian CBT
        try {
            \App\Controllers\CbtController::syncJadwalWithUjian($this->db_core, $this->db, $topikId);
        } catch (\Exception $e) {}

        redirect(base_url("bank_soal?topik_id={$topikId}&msg=dummy_created"));
        exit;
    }

    /**
     * Kosongkan Bank Soal (Reset Data Soal & Paket)
     */
    public function resetData() {
        $topikId = isset($_REQUEST['topik_id']) ? (int)$_REQUEST['topik_id'] : 0;
        if ($topikId > 0) {
            $this->db->prepare("DELETE FROM bs_soal WHERE topik_id = ?")->execute([$topikId]);
            $this->db->prepare("DELETE FROM bs_paket WHERE topik_id = ?")->execute([$topikId]);
            redirect(base_url("bank_soal?topik_id={$topikId}&msg=bank_reset"));
            exit;
        } else {
            $this->db->exec("TRUNCATE TABLE bs_soal");
            $this->db->exec("TRUNCATE TABLE bs_paket");
            redirect(base_url("bank_soal?msg=bank_reset_all"));
            exit;
        }
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

        // Ambil daftar paket sumber yang memiliki butir soal (untuk modal Salin Soal)
        $sourcePaketList = [];
        try {
            $stmtSrc = $this->db->prepare("
                SELECT p.id, p.mapel_nama, p.kelas_nama, p.kelas_sasaran, t.nama_topik, t.jenis as topik_jenis,
                       (SELECT COUNT(*) FROM bs_soal WHERE paket_id = p.id) as jml_soal
                FROM bs_paket p
                JOIN bs_topik t ON p.topik_id = t.id
                WHERE (SELECT COUNT(*) FROM bs_soal WHERE paket_id = p.id) > 0
                  AND p.id != ?
                ORDER BY (p.mapel_nama = ?) DESC, t.id DESC, p.kelas_nama ASC
            ");
            $stmtSrc->execute([$paketId, $mapelNama]);
            $sourcePaketList = $stmtSrc->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

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

    /**
     * Salin butir soal dari paket sumber terpilih ke paket tujuan
     */
    public function copySoal() {
        $targetPaketId = isset($_POST['target_paket_id']) ? (int)$_POST['target_paket_id'] : 0;
        $sourcePaketId = isset($_POST['source_paket_id']) ? (int)$_POST['source_paket_id'] : 0;
        $mode = $_POST['mode'] ?? 'append';

        if ($targetPaketId <= 0 || $sourcePaketId <= 0 || $targetPaketId === $sourcePaketId) {
            redirect("/bank_soal/daftar_soal?paket_id={$targetPaketId}&msg=error_invalid_copy");
            exit;
        }

        // Cek target paket & apakah terkunci
        $stmtT = $this->db->prepare("SELECT * FROM bs_paket WHERE id = ?");
        $stmtT->execute([$targetPaketId]);
        $targetPaket = $stmtT->fetch(PDO::FETCH_ASSOC);

        if (!$targetPaket || !empty($targetPaket['is_locked'])) {
            redirect("/bank_soal/daftar_soal?paket_id={$targetPaketId}&msg=error_locked");
            exit;
        }

        // Ambil soal dari paket sumber
        $stmtSrc = $this->db->prepare("SELECT * FROM bs_soal WHERE paket_id = ? ORDER BY nomor_urut ASC, id ASC");
        $stmtSrc->execute([$sourcePaketId]);
        $sourceSoals = $stmtSrc->fetchAll(PDO::FETCH_ASSOC);

        if (empty($sourceSoals)) {
            redirect("/bank_soal/daftar_soal?paket_id={$targetPaketId}&msg=error_source_empty");
            exit;
        }

        if ($mode === 'overwrite') {
            $this->db->prepare("DELETE FROM bs_soal WHERE paket_id = ?")->execute([$targetPaketId]);
            if ($this->db_core !== $this->db) {
                $this->db_core->prepare("DELETE FROM bs_soal WHERE paket_id = ?")->execute([$targetPaketId]);
            }
            $startNo = 0;
        } else {
            $startNo = (int)$this->db->query("SELECT MAX(nomor_urut) FROM bs_soal WHERE paket_id = {$targetPaketId}")->fetchColumn();
        }

        $stmtIns = $this->db->prepare("
            INSERT INTO bs_soal (
                topik_id, paket_id, mapel_nama, kelas_nama, nomor_urut, 
                tipe_soal, tingkat_kesulitan, skor_maksimal, pertanyaan, 
                opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, konten_tambahan
            ) VALUES (
                ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, 
                ?, ?, ?, ?, ?, ?, ?
            )
        ");

        $copied = 0;
        foreach ($sourceSoals as $s) {
            $startNo++;
            $stmtIns->execute([
                $targetPaket['topik_id'],
                $targetPaketId,
                $targetPaket['mapel_nama'],
                $targetPaket['kelas_nama'],
                $startNo,
                $s['tipe_soal'],
                $s['tingkat_kesulitan'] ?? 'Sedang',
                $s['skor_maksimal'] ?? 2.00,
                $s['pertanyaan'],
                $s['opsi_a'] ?? null,
                $s['opsi_b'] ?? null,
                $s['opsi_c'] ?? null,
                $s['opsi_d'] ?? null,
                $s['opsi_e'] ?? null,
                $s['kunci_jawaban'] ?? null,
                $s['konten_tambahan'] ?? null
            ]);
            $copied++;
        }

        redirect("/bank_soal/daftar_soal?paket_id={$targetPaketId}&msg=soal_copied&count={$copied}");
        exit;
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
