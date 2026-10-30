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

        $currentGuruId = 0;
        if (!$isAdminOrKamad) {
            $stmtG = $db_core->prepare("SELECT id FROM guru WHERE user_id = ? LIMIT 1");
            $stmtG->execute([$_SESSION['user_id']]);
            $currentGuruId = (int)$stmtG->fetchColumn();
        }

        $baseRoute = $isAdminOrKamad ? 'admin/manajemen-berkas' : 'guru/manajemen-berkas';

        return [
            'db_core' => $db_core,
            'db_siakad' => $db_siakad,
            'roleId' => $roleId,
            'isKamad' => $isKamad,
            'isAdminOrKamad' => $isAdminOrKamad,
            'currentGuruId' => $currentGuruId,
            'baseRoute' => $baseRoute
        ];
    }

    /**
     * Dashboard: Statistik, Overview Perangkat Terbaru & Berkas Pribadi Terbaru
     */
    public static function index() {
        $ctx = self::getAuthContext();
        $db_core = $ctx['db_core'];
        $db_siakad = $ctx['db_siakad'];
        $isAdminOrKamad = $ctx['isAdminOrKamad'];
        $currentGuruId = $ctx['currentGuruId'];
        $baseRoute = $ctx['baseRoute'];

        // Daftar Guru
        $guruList = [];
        try {
            $guruList = $db_core->query("SELECT id, nama, nip, foto, status_kepegawaian FROM guru ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // Ambil Perangkat Pembelajaran
        $perangkatList = [];
        try {
            $qCore = "SELECT gp.*, g.nama as nama_guru, g.nip as nip_guru, g.foto as foto_guru, 'core' as source
                      FROM guru_perangkat gp
                      LEFT JOIN guru g ON g.id = gp.guru_id
                      WHERE 1=1";
            $pCore = [];
            if (!$isAdminOrKamad) {
                $qCore .= " AND gp.guru_id = :gid";
                $pCore[':gid'] = $currentGuruId;
            }
            $qCore .= " ORDER BY gp.created_at DESC";
            $stmtC = $db_core->prepare($qCore);
            $stmtC->execute($pCore);
            $perangkatList = $stmtC->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // Ambil data dari siakad perangkat_pembelajaran jika ada
        try {
            $qSiakad = "SELECT p.id, p.guru_id, p.jenis_perangkat as kategori, m.nama_mapel, k.nama_kelas, 
                               p.judul as nama_berkas, p.nama_file as file_path, 'Disetujui' as status_review, 
                               p.created_at, g.nama as nama_guru, g.nip as nip_guru, g.foto as foto_guru, 'siakad' as source
                        FROM perangkat_pembelajaran p
                        LEFT JOIN aula_db.guru g ON g.id = p.guru_id
                        LEFT JOIN mata_pelajaran m ON m.id = p.mapel_id
                        LEFT JOIN aula_db.kelas k ON k.id = p.kelas_id
                        WHERE 1=1";
            $pSiakad = [];
            if (!$isAdminOrKamad) {
                $qSiakad .= " AND p.guru_id = :gid";
                $pSiakad[':gid'] = $currentGuruId;
            }
            $qSiakad .= " ORDER BY p.id DESC";
            $stmtS = $db_siakad->prepare($qSiakad);
            $stmtS->execute($pSiakad);
            $siakadPerangkat = $stmtS->fetchAll(PDO::FETCH_ASSOC);
            $perangkatList = array_merge($perangkatList, $siakadPerangkat);
        } catch (\Exception $e) {}

        // Ambil Berkas Pribadi Guru
        $berkasPribadiList = [];
        try {
            $qBerkas = "SELECT gb.*, g.nama as nama_guru, g.nip as nip_guru, g.foto as foto_guru, g.status_kepegawaian
                        FROM guru_berkas gb
                        LEFT JOIN guru g ON g.id = gb.guru_id
                        WHERE 1=1";
            $pBerkas = [];
            if (!$isAdminOrKamad) {
                $qBerkas .= " AND gb.guru_id = :gid";
                $pBerkas[':gid'] = $currentGuruId;
            }
            $qBerkas .= " ORDER BY gb.created_at DESC";
            $stmtB = $db_core->prepare($qBerkas);
            $stmtB->execute($pBerkas);
            $berkasPribadiList = $stmtB->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // Hitung Statistik KPI
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

        $title = "Dashboard - Manajemen Berkas MAS MIFTAHUL HUDA";
        ob_start();
        include __DIR__ . '/../../resources/views/manajemen_berkas/dashboard.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Halaman Menu: Berkas Perangkat Pembelajaran
     */
    public static function perangkat() {
        $ctx = self::getAuthContext();
        $db_core = $ctx['db_core'];
        $db_siakad = $ctx['db_siakad'];
        $isAdminOrKamad = $ctx['isAdminOrKamad'];
        $currentGuruId = $ctx['currentGuruId'];
        $baseRoute = $ctx['baseRoute'];

        $filter_guru = isset($_GET['guru_id']) ? intval($_GET['guru_id']) : 0;
        $filter_kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';
        $filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';
        $search = isset($_GET['q']) ? trim($_GET['q']) : '';

        // Daftar Guru
        $guruList = [];
        try {
            $guruList = $db_core->query("SELECT id, nama, nip, foto, status_kepegawaian FROM guru ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // Ambil Data Perangkat Pembelajaran
        $perangkatList = [];
        try {
            $qCore = "SELECT gp.*, g.nama as nama_guru, g.nip as nip_guru, g.foto as foto_guru, 'core' as source
                      FROM guru_perangkat gp
                      LEFT JOIN guru g ON g.id = gp.guru_id
                      WHERE 1=1";
            $pCore = [];
            if (!$isAdminOrKamad) {
                $qCore .= " AND gp.guru_id = :gid";
                $pCore[':gid'] = $currentGuruId;
            } elseif ($filter_guru > 0) {
                $qCore .= " AND gp.guru_id = :gid";
                $pCore[':gid'] = $filter_guru;
            }

            if (!empty($filter_kategori)) {
                $qCore .= " AND gp.kategori LIKE :kat";
                $pCore[':kat'] = "%$filter_kategori%";
            }
            if (!empty($filter_status)) {
                $qCore .= " AND gp.status_review = :st";
                $pCore[':st'] = $filter_status;
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

        // Ambil data siakad jika ada
        try {
            $qSiakad = "SELECT p.id, p.guru_id, p.jenis_perangkat as kategori, m.nama_mapel, k.nama_kelas, 
                               p.judul as nama_berkas, p.nama_file as file_path, 'Disetujui' as status_review, 
                               p.created_at, g.nama as nama_guru, g.nip as nip_guru, g.foto as foto_guru, 'siakad' as source
                        FROM perangkat_pembelajaran p
                        LEFT JOIN aula_db.guru g ON g.id = p.guru_id
                        LEFT JOIN mata_pelajaran m ON m.id = p.mapel_id
                        LEFT JOIN aula_db.kelas k ON k.id = p.kelas_id
                        WHERE 1=1";
            $pSiakad = [];
            if (!$isAdminOrKamad) {
                $qSiakad .= " AND p.guru_id = :gid";
                $pSiakad[':gid'] = $currentGuruId;
            } elseif ($filter_guru > 0) {
                $qSiakad .= " AND p.guru_id = :gid";
                $pSiakad[':gid'] = $filter_guru;
            }

            if (!empty($filter_kategori)) {
                $qSiakad .= " AND p.jenis_perangkat LIKE :kat";
                $pSiakad[':kat'] = "%$filter_kategori%";
            }
            if (!empty($filter_status) && $filter_status !== 'Disetujui') {
                $qSiakad .= " AND 1=0"; // status siakad default disetujui
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

        $title = "Perangkat Pembelajaran - Manajemen Berkas MAS MIFTAHUL HUDA";
        ob_start();
        include __DIR__ . '/../../resources/views/manajemen_berkas/perangkat.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Halaman Menu: Berkas Pribadi Guru
     */
    public static function pribadi() {
        $ctx = self::getAuthContext();
        $db_core = $ctx['db_core'];
        $isAdminOrKamad = $ctx['isAdminOrKamad'];
        $currentGuruId = $ctx['currentGuruId'];
        $baseRoute = $ctx['baseRoute'];

        $filter_guru = isset($_GET['guru_id']) ? intval($_GET['guru_id']) : 0;
        $filter_kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';
        $search = isset($_GET['q']) ? trim($_GET['q']) : '';

        // Daftar Guru
        $guruList = [];
        try {
            $guruList = $db_core->query("SELECT id, nama, nip, foto, status_kepegawaian FROM guru ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        // Ambil Data Berkas Pribadi
        $berkasPribadiList = [];
        try {
            $qBerkas = "SELECT gb.*, g.nama as nama_guru, g.nip as nip_guru, g.foto as foto_guru, g.status_kepegawaian
                        FROM guru_berkas gb
                        LEFT JOIN guru g ON g.id = gb.guru_id
                        WHERE 1=1";
            $pBerkas = [];
            if (!$isAdminOrKamad) {
                $qBerkas .= " AND gb.guru_id = :gid";
                $pBerkas[':gid'] = $currentGuruId;
            } elseif ($filter_guru > 0) {
                $qBerkas .= " AND gb.guru_id = :gid";
                $pBerkas[':gid'] = $filter_guru;
            }

            if (!empty($filter_kategori)) {
                $qBerkas .= " AND gb.kategori LIKE :kat";
                $pBerkas[':kat'] = "%$filter_kategori%";
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

        $title = "Berkas Pribadi - Manajemen Berkas MAS MIFTAHUL HUDA";
        ob_start();
        include __DIR__ . '/../../resources/views/manajemen_berkas/pribadi.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    /**
     * Upload Berkas Pribadi
     */
    public static function uploadPribadi() {
        $ctx = self::getAuthContext();
        $db_core = $ctx['db_core'];
        $isAdminOrKamad = $ctx['isAdminOrKamad'];
        $baseRoute = $ctx['baseRoute'];

        $guru_id = 0;
        if (isset($_POST['guru_id']) && $isAdminOrKamad) {
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

                    redirect('/' . $baseRoute . '/pribadi?msg=uploaded');
                    exit;
                }
            }
        }

        redirect('/' . $baseRoute . '/pribadi?err=upload_failed');
        exit;
    }

    /**
     * Hapus Berkas Pribadi
     */
    public static function deletePribadi() {
        $ctx = self::getAuthContext();
        $db_core = $ctx['db_core'];
        $isAdminOrKamad = $ctx['isAdminOrKamad'];
        $baseRoute = $ctx['baseRoute'];

        $id = intval($_POST['id'] ?? 0);

        $stmt = $db_core->prepare("SELECT * FROM guru_berkas WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            if (!$isAdminOrKamad) {
                $stmtG = $db_core->prepare("SELECT id FROM guru WHERE user_id = ?");
                $stmtG->execute([$_SESSION['user_id']]);
                $myGid = (int)$stmtG->fetchColumn();
                if ($row['guru_id'] !== $myGid) {
                    redirect('/' . $baseRoute . '/pribadi?err=unauthorized');
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

        redirect('/' . $baseRoute . '/pribadi?msg=deleted');
        exit;
    }

    /**
     * Update Status Review Perangkat (Admin/Kamad)
     */
    public static function updateStatusReview() {
        $ctx = self::getAuthContext();
        $db_core = $ctx['db_core'];
        $isAdminOrKamad = $ctx['isAdminOrKamad'];
        $baseRoute = $ctx['baseRoute'];

        if (!$isAdminOrKamad) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                echo json_encode(['status' => false, 'message' => 'Unauthorized']);
                exit;
            }
            redirect('/' . $baseRoute . '/perangkat?err=unauthorized');
            exit;
        }

        $id = intval($_POST['id'] ?? 0);
        $status = trim($_POST['status_review'] ?? ($_POST['status'] ?? 'Menunggu'));
        $catatan = trim($_POST['catatan_review'] ?? ($_POST['catatan'] ?? ''));

        $stmt = $db_core->prepare("UPDATE guru_perangkat SET status_review = ?, catatan_review = ? WHERE id = ?");
        $stmt->execute([$status, $catatan, $id]);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            echo json_encode(['status' => true, 'message' => 'Status review berhasil disimpan']);
            exit;
        }

        redirect('/' . $baseRoute . '/perangkat?msg=status_updated');
        exit;
    }

    /**
     * Fallback untuk backward-compatibility dengan route guruIndex
     */
    public static function guruIndex() {
        self::index();
    }
}
