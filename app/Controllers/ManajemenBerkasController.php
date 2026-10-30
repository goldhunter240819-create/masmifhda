<?php

namespace App\Controllers;

use App\Core\Database;
use PDO;

class ManajemenBerkasController {

    private static function ensureTables($db_core) {
        try {
            $db_core->exec("CREATE TABLE IF NOT EXISTS guru_perangkat (
                id INT AUTO_INCREMENT PRIMARY KEY,
                guru_id INT NOT NULL,
                kategori VARCHAR(100) DEFAULT NULL,
                mapel_nama VARCHAR(150) DEFAULT NULL,
                kelas_nama VARCHAR(100) DEFAULT NULL,
                nama_berkas VARCHAR(255) NOT NULL,
                file_path VARCHAR(255) NOT NULL,
                status_review VARCHAR(50) DEFAULT 'Menunggu',
                catatan_review TEXT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (guru_id)
            )");

            $db_core->exec("CREATE TABLE IF NOT EXISTS guru_berkas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                guru_id INT NOT NULL,
                nama_berkas VARCHAR(255) NOT NULL,
                kategori VARCHAR(100) DEFAULT 'Umum',
                file_path VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (guru_id)
            )");
        } catch (\Exception $e) {}
    }

    /**
     * Dispatcher utama: Admin/Kamad -> monitoring; Guru -> portal pribadi
     */
    public static function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            redirect('/login');
            exit;
        }

        $roleId = isset($_SESSION['role_id']) ? (int)$_SESSION['role_id'] : 0;
        $isKamad = !empty($_SESSION['is_kamad']);

        // Jika Guru biasa (bukan Kamad/Admin/Dev), arahkan ke guru view
        if ($roleId === 2 && !$isKamad) {
            self::guruIndex();
            return;
        }

        // Monitoring Admin, Kamad, dan Developer
        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');
        self::ensureTables($db_core);

        $filter_guru = isset($_GET['guru_id']) ? intval($_GET['guru_id']) : 0;
        $filter_kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';
        $filter_tab = isset($_GET['tab']) ? trim($_GET['tab']) : 'perangkat';
        $search = isset($_GET['q']) ? trim($_GET['q']) : '';

        // Daftar Guru Lengkap
        $guruList = [];
        try {
            $guruList = $db_core->query("SELECT id, nama, nip, foto, status_kepegawaian FROM guru ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // Map Guru
        $guruMap = [];
        foreach ($guruList as $g) {
            $guruMap[$g['id']] = $g;
        }

        // 1. Ambil Data Perangkat Pembelajaran (gabungan guru_perangkat di core dan perangkat_pembelajaran di siakad jika ada)
        $perangkatList = [];
        try {
            $qCore = "SELECT gp.*, g.nama as nama_guru, g.nip as nip_guru, g.foto as foto_guru, 'core' as source
                      FROM guru_perangkat gp
                      LEFT JOIN guru g ON g.id = gp.guru_id
                      WHERE 1=1";
            $pCore = [];
            if ($filter_guru > 0) {
                $qCore .= " AND gp.guru_id = :gid";
                $pCore[':gid'] = $filter_guru;
            }
            if (!empty($filter_kategori)) {
                $qCore .= " AND gp.kategori LIKE :kat";
                $pCore[':kat'] = "%$filter_kategori%";
            }
            if (!empty($search)) {
                $qCore .= " AND (gp.nama_berkas LIKE :q OR g.nama LIKE :q OR gp.mapel_nama LIKE :q)";
                $pCore[':q'] = "%$search%";
            }
            $qCore .= " ORDER BY gp.created_at DESC";
            $stmtC = $db_core->prepare($qCore);
            $stmtC->execute($pCore);
            $perangkatList = $stmtC->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // Ambil juga dari siakad.perangkat_pembelajaran jika ada
        try {
            $qSiakad = "SELECT p.id, p.guru_id, p.jenis_perangkat as kategori, m.nama_mapel, k.nama_kelas, 
                               p.judul as nama_berkas, p.nama_file as file_path, 'Disetujui' as status_review, 
                               p.created_at, g.nama as nama_guru, g.nip as nip_guru, g.foto as foto_guru, 'siakad' as source
                        FROM perangkat_pembelajaran p
                        LEFT JOIN aula_db.guru g ON g.id = p.guru_id
                        LEFT JOIN mapel m ON m.id = p.mapel_id
                        LEFT JOIN aula_db.kelas k ON k.id = p.kelas_id
                        WHERE 1=1";
            $pSiakad = [];
            if ($filter_guru > 0) {
                $qSiakad .= " AND p.guru_id = :gid";
                $pSiakad[':gid'] = $filter_guru;
            }
            if (!empty($filter_kategori)) {
                $qSiakad .= " AND p.jenis_perangkat LIKE :kat";
                $pSiakad[':kat'] = "%$filter_kategori%";
            }
            if (!empty($search)) {
                $qSiakad .= " AND (p.judul LIKE :q OR g.nama LIKE :q OR m.nama_mapel LIKE :q)";
                $pSiakad[':q'] = "%$search%";
            }
            $qSiakad .= " ORDER BY p.id DESC";
            $stmtS = $db_siakad->prepare($qSiakad);
            $stmtS->execute($pSiakad);
            $siakadPerangkat = $stmtS->fetchAll(PDO::FETCH_ASSOC);
            $perangkatList = array_merge($perangkatList, $siakadPerangkat);
        } catch (\Exception $e) {}

        // 2. Ambil Data Berkas Pribadi Guru (dari guru_berkas di aula_db)
        $berkasPribadiList = [];
        try {
            $qBerkas = "SELECT gb.*, g.nama as nama_guru, g.nip as nip_guru, g.foto as foto_guru, g.status_kepegawaian
                        FROM guru_berkas gb
                        LEFT JOIN guru g ON g.id = gb.guru_id
                        WHERE 1=1";
            $pBerkas = [];
            if ($filter_guru > 0) {
                $qBerkas .= " AND gb.guru_id = :gid";
                $pBerkas[':gid'] = $filter_guru;
            }
            if (!empty($search)) {
                $qBerkas .= " AND (gb.nama_berkas LIKE :q OR g.nama LIKE :q)";
                $pBerkas[':q'] = "%$search%";
            }
            $qBerkas .= " ORDER BY gb.created_at DESC";
            $stmtB = $db_core->prepare($qBerkas);
            $stmtB->execute($pBerkas);
            $berkasPribadiList = $stmtB->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // 3. Statistik Global
        $totalGuru = count($guruList);
        $totalPerangkat = count($perangkatList);
        $totalBerkasPribadi = count($berkasPribadiList);

        $gurusWithDocs = [];
        foreach ($perangkatList as $p) {
            $gurusWithDocs[$p['guru_id']] = true;
        }
        foreach ($berkasPribadiList as $bp) {
            $gurusWithDocs[$bp['guru_id']] = true;
        }
        $guruAktifCount = count($gurusWithDocs);
        $persenKelengkapan = $totalGuru > 0 ? round(($guruAktifCount / $totalGuru) * 100) : 0;

        $title_page = "Monitoring Berkas Guru";
        include __DIR__ . '/../../resources/views/admin/manajemen_berkas_guru.php';
    }

    /**
     * Tampilan untuk Guru melihat & mengelola perangkat serta berkas pribadinya
     */
    public static function guruIndex() {
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

        // Cari guru berdasarkan user_id
        $stmtG = $db_core->prepare("SELECT * FROM guru WHERE user_id = :uid LIMIT 1");
        $stmtG->execute([':uid' => $_SESSION['user_id']]);
        $guru = $stmtG->fetch(PDO::FETCH_ASSOC);

        if (!$guru) {
            // Fallback jika login bukan via guru
            echo "Akun Anda tidak terhubung dengan data guru. Silakan hubungi admin.";
            exit;
        }

        $guru_id = $guru['id'];
        $active_tab = isset($_GET['tab']) ? trim($_GET['tab']) : 'perangkat';

        // 1. Ambil Perangkat Pembelajaran Guru ini
        $myPerangkat = [];
        try {
            $stmtP = $db_core->prepare("SELECT * FROM guru_perangkat WHERE guru_id = ? ORDER BY created_at DESC");
            $stmtP->execute([$guru_id]);
            $myPerangkat = $stmtP->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // 2. Ambil Berkas Pribadi Guru ini
        $myBerkasPribadi = [];
        try {
            $stmtBP = $db_core->prepare("SELECT * FROM guru_berkas WHERE guru_id = ? ORDER BY created_at DESC");
            $stmtBP->execute([$guru_id]);
            $myBerkasPribadi = $stmtBP->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // 3. Ambil Mapel & Kelas yang diampu oleh guru ini
        $mapelKelasList = [];
        try {
            $stmtJ = $db_siakad->prepare("
                SELECT DISTINCT m.nama_mapel, k.nama_kelas, m.id as mapel_id, k.id as kelas_id
                FROM (
                    SELECT kelas_id, mapel_id as mapel FROM mengajar WHERE guru_id = :gid
                    UNION
                    SELECT kelas_id, mata_pelajaran_id as mapel FROM jadwal_pelajaran WHERE guru_id = :gid
                ) jp
                JOIN mata_pelajaran m ON jp.mapel = m.id
                JOIN aula_db.kelas k ON jp.kelas_id = k.id
                ORDER BY k.nama_kelas ASC, m.nama_mapel ASC
            ");
            $stmtJ->execute([':gid' => $guru_id]);
            $mapelKelasList = $stmtJ->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $kategoriList = [
            'Capaian Pembelajaran (CP)',
            'Alur Tujuan Pembelajaran (ATP)',
            'Modul Ajar / RPP',
            'KKTP',
            'Program Tahunan (Prota)',
            'Program Semester (Promes)',
            'Silabus',
            'Bahan Ajar / Materi'
        ];

        $title_page = "Manajemen Berkas Saya";
        include __DIR__ . '/../../resources/views/portal/guru/manajemen_berkas.php';
    }

    /**
     * Upload berkas pribadi oleh guru
     */
    public static function uploadPribadi() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) { redirect('/login'); exit; }

        $db_core = Database::connect('core');
        self::ensureTables($db_core);

        $guru_id = 0;
        if (isset($_POST['guru_id']) && (in_array($_SESSION['role_id'], [1, 99]) || !empty($_SESSION['is_kamad']))) {
            $guru_id = intval($_POST['guru_id']);
        } else {
            $stmt = $db_core->prepare("SELECT id FROM guru WHERE user_id = ? LIMIT 1");
            $stmt->execute([$_SESSION['user_id']]);
            $guru_id = (int)$stmt->fetchColumn();
        }

        $nama_berkas = trim($_POST['nama_berkas'] ?? '');
        $kategori = trim($_POST['kategori'] ?? 'Umum');

        if ($guru_id > 0 && !empty($nama_berkas) && isset($_FILES['berkas_file']) && $_FILES['berkas_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/guru_berkas/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $ext = strtolower(pathinfo($_FILES['berkas_file']['name'], PATHINFO_EXTENSION));
            $allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'];

            if (in_array($ext, $allowed)) {
                $filename = 'berkas_' . $guru_id . '_' . time() . '_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES['berkas_file']['tmp_name'], $uploadDir . $filename)) {
                    $ins = $db_core->prepare("INSERT INTO guru_berkas (guru_id, nama_berkas, kategori, file_path, created_at) VALUES (?, ?, ?, ?, NOW())");
                    $ins->execute([$guru_id, $nama_berkas, $kategori, $filename]);

                    $target = (in_array($_SESSION['role_id'], [1, 99]) || !empty($_SESSION['is_kamad']))
                        ? '/admin/manajemen-berkas?tab=pribadi&msg=uploaded'
                        : '/guru/manajemen-berkas?tab=pribadi&msg=uploaded';
                    redirect($target);
                    exit;
                }
            }
        }

        $target = (in_array($_SESSION['role_id'], [1, 99]) || !empty($_SESSION['is_kamad']))
            ? '/admin/manajemen-berkas?tab=pribadi&err=upload_failed'
            : '/guru/manajemen-berkas?tab=pribadi&err=upload_failed';
        redirect($target);
        exit;
    }

    /**
     * Hapus berkas pribadi
     */
    public static function deletePribadi() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) { redirect('/login'); exit; }

        $db_core = Database::connect('core');
        $id = intval($_POST['id'] ?? 0);

        $isAdmin = (in_array($_SESSION['role_id'], [1, 99]) || !empty($_SESSION['is_kamad']));
        
        $stmt = $db_core->prepare("SELECT * FROM guru_berkas WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            if (!$isAdmin) {
                $stmtG = $db_core->prepare("SELECT id FROM guru WHERE user_id = ?");
                $stmtG->execute([$_SESSION['user_id']]);
                $myGid = (int)$stmtG->fetchColumn();
                if ($row['guru_id'] !== $myGid) {
                    redirect('/guru/manajemen-berkas?err=unauthorized');
                    exit;
                }
            }

            $filepath = __DIR__ . '/../../public/uploads/guru_berkas/' . $row['file_path'];
            if (file_exists($filepath)) {
                @unlink($filepath);
            }

            $del = $db_core->prepare("DELETE FROM guru_berkas WHERE id = ?");
            $del->execute([$id]);
        }

        $target = $isAdmin ? '/admin/manajemen-berkas?tab=pribadi&msg=deleted' : '/guru/manajemen-berkas?tab=pribadi&msg=deleted';
        redirect($target);
        exit;
    }

    /**
     * Update status review perangkat oleh Admin/Kamad (AJAX atau Form POST)
     */
    public static function updateStatusReview() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id']) || (!in_array($_SESSION['role_id'], [1, 99]) && empty($_SESSION['is_kamad']))) {
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $id = intval($_POST['id'] ?? 0);
        $status = trim($_POST['status'] ?? 'Menunggu');
        $catatan = trim($_POST['catatan'] ?? '');

        $db_core = Database::connect('core');
        $stmt = $db_core->prepare("UPDATE guru_perangkat SET status_review = ?, catatan_review = ? WHERE id = ?");
        $stmt->execute([$status, $catatan, $id]);

        echo json_encode(['status' => true, 'message' => 'Status review diperbarui']);
        exit;
    }
}
