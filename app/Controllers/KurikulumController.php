<?php
namespace App\Controllers;

class KurikulumController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . base_url('login'));
            exit;
        }
        self::ensureSupervisiTables();
        self::ensureMonitoringTables();
    }

    public function dashboard() {
        $active_module = 'kurikulum';
        $active_page   = 'overview';

        $db = \App\Core\Database::connect('core');
        $db_siakad = \App\Core\Database::connect('siakad');

        // 1. Stat Total Guru (MAS Miftahul Huda real count)
        $statTotalGuru = (int) $db->query("SELECT COUNT(*) FROM guru")->fetchColumn();

        // 2. Stat Supervisi Administrasi
        $statAdminLengkap = (int) $db->query("
            SELECT COUNT(DISTINCT pegawai_id) 
            FROM kurikulum_supervisi_administrasi 
            WHERE status = 'Dievaluasi' OR (nilai IS NOT NULL AND nilai > 0)
        ")->fetchColumn();

        // 3. Stat Supervisi Kelas
        $statSupervisiKelas = (int) $db->query("
            SELECT COUNT(DISTINCT guru_id) 
            FROM kurikulum_supervisi_kelas 
            WHERE status = 'Dievaluasi' OR (nilai IS NOT NULL AND nilai > 0)
        ")->fetchColumn();

        // 4. Mapel dictionary from siakad and core
        $mapelList = [];
        try {
            $mapelList = $db_siakad->query("SELECT id, nama_mapel FROM mapel")->fetchAll(\PDO::FETCH_KEY_PAIR);
        } catch (\Exception $e) {}
        try {
            $coreMapels = $db->query("SELECT id, nama_mapel FROM mapel")->fetchAll(\PDO::FETCH_KEY_PAIR);
            foreach ($coreMapels as $mid => $mname) {
                if (!isset($mapelList[$mid])) $mapelList[$mid] = $mname;
            }
        } catch (\Exception $e) {}

        // 5. Supervisi Kelas Terbaru
        $stmtRecent = $db->query("
            SELECT ksk.id, ksk.tanggal, ksk.guru_id, ksk.kelas_id, ksk.mapel_id, ksk.status, ksk.nilai,
                   g.nama as nama_guru, k.nama_kelas
            FROM kurikulum_supervisi_kelas ksk
            LEFT JOIN guru g ON ksk.guru_id = g.id
            LEFT JOIN kelas k ON ksk.kelas_id = k.id
            ORDER BY ksk.tanggal DESC, ksk.id DESC
            LIMIT 5
        ");
        $recentRows = $stmtRecent ? $stmtRecent->fetchAll(\PDO::FETCH_ASSOC) : [];

        $recentSupervisi = [];
        foreach ($recentRows as $r) {
            $m_nama = $mapelList[$r['mapel_id']] ?? ('Mapel #' . $r['mapel_id']);
            $recentSupervisi[] = [
                'id' => $r['id'],
                'tanggal_supervisi' => $r['tanggal'],
                'nama_guru' => $r['nama_guru'] ?: 'Guru',
                'mata_pelajaran' => $m_nama,
                'kelas' => $r['nama_kelas'] ?: '-',
                'nilai' => $r['nilai'],
                'status' => $r['status']
            ];
        }

        require_once __DIR__ . '/../../resources/views/kurikulum/dashboard.php';
    }

    public function dashboardSupervisi() {
        $db = \App\Core\Database::connect('core');
        $db->exec("CREATE TABLE IF NOT EXISTS kurikulum_supervisi_universal (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tipe_supervisi VARCHAR(100) NOT NULL,
            guru_id INT NOT NULL,
            kelas_id INT NULL,
            mapel_id INT NULL,
            supervisor_id INT NOT NULL,
            tanggal DATE NOT NULL,
            keterangan TEXT,
            status VARCHAR(50) DEFAULT 'Jadwal',
            nilai FLOAT NULL,
            catatan_evaluasi TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        $active_module = 'kurikulum';
        $active_page   = 'supervisi_dashboard';
        require_once __DIR__ . '/../../resources/views/kurikulum/supervisi_dashboard.php';
    }

    public function rekapitulasiSupervisi() {
        $active_module = 'kurikulum';
        $active_page   = 'supervisi_rekapitulasi';
        $db = \App\Core\Database::connect('core');
        
        $gurus = $db->query("SELECT id, nama, nip FROM guru ORDER BY nama ASC")->fetchAll();
        $totalGuru = count($gurus);
        
        $universalTypes = [
            'Asesmen', 'Pengelolaan Kelas', 'Literasi', 'Numerasi',
            'Pendidikan Karakter', 'Kurikulum Berbasis Cinta',
            'Deep Learning', 'Teknologi Digitalisasi', 'Ekstrakurikuler'
        ];
        
        $rekap = [];
        // Administrasi
        $adminCount = $db->query("SELECT COUNT(DISTINCT COALESCE(pegawai_id, guru_id)) FROM kurikulum_supervisi_administrasi WHERE status = 'Dievaluasi' OR (nilai IS NOT NULL AND nilai > 0) OR (nilai_akhir IS NOT NULL AND nilai_akhir > 0)")->fetchColumn() ?: 0;
        $rekap[] = ['kategori' => 'Administrasi', 'total' => $adminCount, 'persen' => $totalGuru > 0 ? round(($adminCount/$totalGuru)*100) : 0, 'color' => 'bg-primary'];
        
        // Kelas
        $kelasCount = $db->query("SELECT COUNT(DISTINCT guru_id) FROM kurikulum_supervisi_kelas WHERE status = 'Dievaluasi' OR (nilai IS NOT NULL AND nilai > 0) OR (nilai_akhir IS NOT NULL AND nilai_akhir > 0)")->fetchColumn() ?: 0;
        $rekap[] = ['kategori' => 'Proses Pembelajaran', 'total' => $kelasCount, 'persen' => $totalGuru > 0 ? round(($kelasCount/$totalGuru)*100) : 0, 'color' => 'bg-warning'];
        
        // Universal
        $colors = ['bg-indigo', 'bg-teal', 'bg-pink', 'bg-primary', 'bg-rose', 'bg-pink', 'bg-purple', 'bg-success', 'bg-warning'];
        foreach($universalTypes as $i => $utype) {
            $count = $db->query("SELECT COUNT(DISTINCT guru_id) FROM kurikulum_supervisi_universal WHERE status = 'Dievaluasi' AND tipe_supervisi = '$utype'")->fetchColumn() ?: 0;
            $rekap[] = ['kategori' => $utype, 'total' => $count, 'persen' => $totalGuru > 0 ? round(($count/$totalGuru)*100) : 0, 'color' => $colors[$i % count($colors)]];
        }

        // Tabel Guru Detail
        $guruDetail = [];
        foreach($gurus as $g) {
            $detail = ['nama' => $g['nama'], 'nip' => $g['nip'], 'completed' => 0];
            
            // Cek admin
            $isAdmin = $db->query("SELECT id FROM kurikulum_supervisi_administrasi WHERE (pegawai_id = {$g['id']} OR guru_id = {$g['id']}) AND (status = 'Dievaluasi' OR (nilai IS NOT NULL AND nilai > 0) OR (nilai_akhir IS NOT NULL AND nilai_akhir > 0)) LIMIT 1")->fetchColumn();
            if($isAdmin) $detail['completed']++;
            
            // Cek kelas
            $isKelas = $db->query("SELECT id FROM kurikulum_supervisi_kelas WHERE guru_id = {$g['id']} AND (status = 'Dievaluasi' OR (nilai IS NOT NULL AND nilai > 0) OR (nilai_akhir IS NOT NULL AND nilai_akhir > 0)) LIMIT 1")->fetchColumn();
            if($isKelas) $detail['completed']++;
            
            // Cek universal
            $uCount = $db->query("SELECT COUNT(DISTINCT tipe_supervisi) FROM kurikulum_supervisi_universal WHERE guru_id = {$g['id']} AND status = 'Dievaluasi'")->fetchColumn() ?: 0;
            $detail['completed'] += $uCount;
            
            $detail['total_wajib'] = 2 + count($universalTypes);
            $detail['persen'] = round(($detail['completed'] / $detail['total_wajib']) * 100);
            
            $guruDetail[] = $detail;
        }

        require_once __DIR__ . '/../../resources/views/kurikulum/supervisi_rekapitulasi.php';
    }

    public function cetakBukuProgramTahunan() {
        $db = \App\Core\Database::connect('core');
        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();
        $legalitas = self::getSupervisiLegalitas($db);
        extract($legalitas);
        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch() ?: [];
        $gurus = $db->query("SELECT * FROM guru ORDER BY nama ASC")->fetchAll();
        $ta = $db->query("SELECT name FROM tahun_ajaran ORDER BY id DESC LIMIT 1")->fetchColumn() ?: '2026/2027';
        
        require_once __DIR__ . '/../../resources/views/kurikulum/supervisi_buku_program_cetak.php';
    }

    public function supervisiUniversal($tipe) {
        $tipeAsli = str_replace('-', ' ', ucwords(strtolower($tipe), '-'));
        $active_module = 'kurikulum';
        $active_page   = 'supervisi_universal_' . strtolower(str_replace('-', '_', $tipe));
        
        $db = \App\Core\Database::connect('core');
        $siakad_db = \App\Core\Database::connect('siakad');
        
        $jadwal_supervisi = [];
        try {
            $stmt_jadwal = $db->prepare("
                SELECT sk.*, g.nama as nama_guru, sp.nama as nama_supervisor 
                FROM kurikulum_supervisi_universal sk
                JOIN guru g ON sk.guru_id = g.id
                JOIN guru sp ON sk.supervisor_id = sp.id
                WHERE sk.tipe_supervisi = ?
                ORDER BY sk.tanggal DESC
            ");
            $stmt_jadwal->execute([$tipeAsli]);
            $jadwal_supervisi = $stmt_jadwal->fetchAll();
            
            foreach($jadwal_supervisi as &$j) {
                if ($j['mapel_id']) {
                    $j['nama_mapel'] = $siakad_db->query("SELECT nama_mapel FROM mapel WHERE id = " . $j['mapel_id'])->fetchColumn() ?: '-';
                } else {
                    $j['nama_mapel'] = 'Umum';
                }
                
                if ($j['kelas_id']) {
                    $j['nama_kelas'] = $siakad_db->query("SELECT nama_kelas FROM kelas WHERE id = " . $j['kelas_id'])->fetchColumn() ?: '-';
                } else {
                    $j['nama_kelas'] = 'Umum';
                }
            }
        } catch (\PDOException $e) {}

        $gurus = $db->query("SELECT id, nama FROM guru ORDER BY nama ASC")->fetchAll();
        $kelas = $siakad_db->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC")->fetchAll();
        $mapels = $siakad_db->query("SELECT id, nama_mapel FROM mapel ORDER BY nama_mapel ASC")->fetchAll();

        $tipe = $tipeAsli;
        require_once __DIR__ . '/../../resources/views/kurikulum/supervisi_universal.php';
    }

    public function storeSupervisiUniversal($tipe) {
        $tipeAsli = str_replace('-', ' ', ucwords(strtolower($tipe), '-'));
        $db = \App\Core\Database::connect('core');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $guru_id = $_POST['guru_id'];
            $kelas_id = !empty($_POST['kelas_id']) ? $_POST['kelas_id'] : null;
            $mapel_id = !empty($_POST['mapel_id']) ? $_POST['mapel_id'] : null;
            $supervisor_id = $_POST['supervisor_id'];
            $tanggal = $_POST['tanggal'];
            $keterangan = $_POST['keterangan'] ?? '';

            try {
                $stmt = $db->prepare("INSERT INTO kurikulum_supervisi_universal (tipe_supervisi, guru_id, kelas_id, mapel_id, supervisor_id, tanggal, keterangan, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Jadwal')");
                $stmt->execute([$tipeAsli, $guru_id, $kelas_id, $mapel_id, $supervisor_id, $tanggal, $keterangan]);
                $_SESSION['sweetalert'] = ['title' => 'Sukses!', 'text' => 'Jadwal berhasil ditambahkan', 'icon' => 'success'];
            } catch (\PDOException $e) {
                $_SESSION['sweetalert'] = ['title' => 'Gagal!', 'text' => 'Terjadi kesalahan sistem: ' . $e->getMessage(), 'icon' => 'error'];
            }
            header('Location: /kurikulum/supervisi/universal/' . strtolower(str_replace(' ', '-', $tipe)));
            exit;
        }
    }

    public function updateSupervisiUniversal($tipe) {
        $tipeAsli = str_replace('-', ' ', ucwords(strtolower($tipe), '-'));
        $db = \App\Core\Database::connect('core');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $guru_id = $_POST['guru_id'];
            $kelas_id = !empty($_POST['kelas_id']) ? $_POST['kelas_id'] : null;
            $mapel_id = !empty($_POST['mapel_id']) ? $_POST['mapel_id'] : null;
            $supervisor_id = $_POST['supervisor_id'];
            $tanggal = $_POST['tanggal'];
            $keterangan = $_POST['keterangan'] ?? '';

            try {
                $stmt = $db->prepare("UPDATE kurikulum_supervisi_universal SET guru_id = ?, kelas_id = ?, mapel_id = ?, supervisor_id = ?, tanggal = ?, keterangan = ? WHERE id = ? AND tipe_supervisi = ?");
                $stmt->execute([$guru_id, $kelas_id, $mapel_id, $supervisor_id, $tanggal, $keterangan, $id, $tipeAsli]);
                $_SESSION['sweetalert'] = ['title' => 'Sukses!', 'text' => 'Jadwal berhasil diupdate', 'icon' => 'success'];
            } catch (\PDOException $e) {
                $_SESSION['sweetalert'] = ['title' => 'Gagal!', 'text' => 'Terjadi kesalahan sistem: ' . $e->getMessage(), 'icon' => 'error'];
            }
            header('Location: /kurikulum/supervisi/universal/' . strtolower(str_replace(' ', '-', $tipe)));
            exit;
        }
    }

    public function deleteSupervisiUniversal($tipe) {
        $db = \App\Core\Database::connect('core');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            try {
                $stmt = $db->prepare("DELETE FROM kurikulum_supervisi_universal WHERE id = ?");
                $stmt->execute([$id]);
                // Delete hasil
                $db->prepare("DELETE FROM kurikulum_supervisi_hasil WHERE tipe_supervisi = ? AND supervisi_id = ?")->execute([$tipe, $id]);
                $_SESSION['sweetalert'] = ['title' => 'Dihapus!', 'text' => 'Jadwal berhasil dihapus', 'icon' => 'success'];
            } catch (\PDOException $e) {
                $_SESSION['sweetalert'] = ['title' => 'Gagal!', 'text' => 'Terjadi kesalahan sistem', 'icon' => 'error'];
            }
            header('Location: /kurikulum/supervisi/universal/' . strtolower(str_replace(' ', '-', $tipe)));
            exit;
        }
    }

    public function evaluasiSupervisiUniversal($tipe) {
        $tipeAsli = str_replace('-', ' ', ucwords(strtolower($tipe), '-'));
        $db = \App\Core\Database::connect('core');
        $siakad_db = \App\Core\Database::connect('siakad');
        
        $id = $_GET['id'] ?? null;
        if (!$id) { header("Location: /kurikulum/supervisi/universal/" . strtolower(str_replace(' ', '-', $tipe))); exit; }

        $supervisi = $db->query("SELECT sk.*, g.nama as nama_guru, sp.nama as nama_supervisor FROM kurikulum_supervisi_universal sk JOIN guru g ON sk.guru_id = g.id JOIN guru sp ON sk.supervisor_id = sp.id WHERE sk.id = $id AND sk.tipe_supervisi = '$tipeAsli'")->fetch();
        if($supervisi) {
            $supervisi['nama_mapel'] = $supervisi['mapel_id'] ? $siakad_db->query("SELECT nama_mapel FROM mapel WHERE id = " . $supervisi['mapel_id'])->fetchColumn() : null;
            $supervisi['nama_kelas'] = $supervisi['kelas_id'] ? $siakad_db->query("SELECT nama_kelas FROM kelas WHERE id = " . $supervisi['kelas_id'])->fetchColumn() : null;
        }

        $instrumen = $db->query("SELECT * FROM kurikulum_instrumen_supervisi WHERE tipe_supervisi = '$tipeAsli' ORDER BY CAST(kode AS UNSIGNED) ASC, id ASC")->fetchAll();
        $hasil_raw = $db->query("SELECT instrumen_id, skor FROM kurikulum_supervisi_hasil WHERE tipe_supervisi = '$tipeAsli' AND supervisi_id = $id")->fetchAll(\PDO::FETCH_ASSOC);
        $hasil = [];
        foreach($hasil_raw as $h) {
            $hasil[$h['instrumen_id']] = $h['skor'];
        }

        $active_module = 'kurikulum';
        $active_page   = 'supervisi_universal_' . strtolower(str_replace('-', '_', $tipe));
        $actionUrl = '/kurikulum/supervisi/universal/' . strtolower(str_replace(' ', '-', $tipe)) . '/evaluasi';
        $backUrl = '/kurikulum/supervisi/universal/' . strtolower(str_replace(' ', '-', $tipe));
        
        $tipe = $tipeAsli;
        require_once __DIR__ . '/../../resources/views/kurikulum/supervisi_evaluasi.php';
    }

    public function storeEvaluasiSupervisiUniversal($tipe) {
        $tipeAsli = str_replace('-', ' ', ucwords(strtolower($tipe), '-'));
        $db = \App\Core\Database::connect('core');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $skor = $_POST['skor'] ?? [];
            $catatan_evaluasi = $_POST['catatan_evaluasi'] ?? '';
            
            try {
                $db->beginTransaction();
                
                $db->query("DELETE FROM kurikulum_supervisi_hasil WHERE tipe_supervisi = '$tipeAsli' AND supervisi_id = $id");
                
                $stmt_hasil = $db->prepare("INSERT INTO kurikulum_supervisi_hasil (tipe_supervisi, supervisi_id, instrumen_id, skor) VALUES (?, ?, ?, ?)");
                
                $total_skor = 0;
                foreach($skor as $ins_id => $s) {
                    $stmt_hasil->execute([$tipeAsli, $id, $ins_id, $s]);
                    $total_skor += $s;
                }
                
                $instrumen = $db->query("SELECT id, bobot_maksimal FROM kurikulum_instrumen_supervisi WHERE tipe_supervisi = '$tipeAsli'")->fetchAll();
                $total_maksimal = 0;
                foreach($instrumen as $ins) {
                    $total_maksimal += $ins['bobot_maksimal'];
                }
                
                $nilai_akhir = 0;
                if ($total_maksimal > 0) {
                    $nilai_akhir = round(($total_skor / $total_maksimal) * 100, 2);
                }
                
                $stmt_update = $db->prepare("UPDATE kurikulum_supervisi_universal SET nilai = ?, catatan_evaluasi = ?, status = 'Dievaluasi' WHERE id = ?");
                $stmt_update->execute([$nilai_akhir, $catatan_evaluasi, $id]);
                
                $db->commit();
                $_SESSION['sweetalert'] = ['title' => 'Sukses!', 'text' => 'Hasil evaluasi berhasil disimpan', 'icon' => 'success'];
                
            } catch (\PDOException $e) {
                $db->rollBack();
                $_SESSION['sweetalert'] = ['title' => 'Gagal!', 'text' => 'Terjadi kesalahan: ' . $e->getMessage(), 'icon' => 'error'];
            }
            
            header('Location: /kurikulum/supervisi/universal/' . strtolower(str_replace(' ', '-', $tipe)));
            exit;
        }
    }

    public function rilisSupervisiUniversal($tipe) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['id']) || !isset($_POST['is_released'])) {
                echo json_encode(['success' => false, 'message' => 'Missing params']);
                exit;
            }
            $id = $_POST['id'];
            $is_released = $_POST['is_released'];
            $db = \App\Core\Database::connect('core');
            try {
                $stmt = $db->prepare("UPDATE kurikulum_supervisi_universal SET is_released = ? WHERE id = ?");
                $stmt->execute([$is_released, $id]);
                echo json_encode(['success' => true]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }
        echo json_encode(['success' => false, 'message' => 'Invalid method']);
        exit;
    }

    public function rilisHasilSupervisiUniversal($tipe) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['id']) || !isset($_POST['is_hasil_released'])) {
                echo json_encode(['success' => false, 'message' => 'Missing params']);
                exit;
            }
            $id = $_POST['id'];
            $is_released = $_POST['is_hasil_released'];
            $db = \App\Core\Database::connect('core');
            try {
                $stmt = $db->prepare("UPDATE kurikulum_supervisi_universal SET is_hasil_released = ? WHERE id = ?");
                $stmt->execute([$is_released, $id]);
                echo json_encode(['success' => true]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }
        echo json_encode(['success' => false, 'message' => 'Invalid method']);
        exit;
    }

    public function cetakSupervisiUniversal($tipe) {
        $tipeAsli = str_replace('-', ' ', ucwords(strtolower($tipe), '-'));
        $db = \App\Core\Database::connect('core');
        $siakad_db = \App\Core\Database::connect('siakad');
        
        $id = $_GET['id'] ?? null;
        if (!$id) { header("Location: /kurikulum/supervisi/universal/" . strtolower(str_replace(' ', '-', $tipe))); exit; }

        $supervisi = $db->query("SELECT sk.*, g.nama as nama_guru, sp.nama as nama_supervisor FROM kurikulum_supervisi_universal sk JOIN guru g ON sk.guru_id = g.id JOIN guru sp ON sk.supervisor_id = sp.id WHERE sk.id = $id AND sk.tipe_supervisi = '$tipeAsli'")->fetch();
        if($supervisi) {
            $supervisi['nama_mapel'] = $supervisi['mapel_id'] ? $siakad_db->query("SELECT nama_mapel FROM mapel WHERE id = " . $supervisi['mapel_id'])->fetchColumn() : null;
            $supervisi['nama_kelas'] = $supervisi['kelas_id'] ? $siakad_db->query("SELECT nama_kelas FROM kelas WHERE id = " . $supervisi['kelas_id'])->fetchColumn() : null;
        }

        $instrumen = $db->query("SELECT * FROM kurikulum_instrumen_supervisi WHERE tipe_supervisi = '$tipeAsli' ORDER BY CAST(kode AS UNSIGNED) ASC, id ASC")->fetchAll();
        $hasil_raw = $db->query("SELECT instrumen_id, skor FROM kurikulum_supervisi_hasil WHERE tipe_supervisi = '$tipeAsli' AND supervisi_id = $id")->fetchAll(\PDO::FETCH_ASSOC);
        $hasil = [];
        foreach($hasil_raw as $h) {
            $hasil[$h['instrumen_id']] = $h['skor'];
        }

        $institusi = $siakad_db->query("SELECT * FROM institusi LIMIT 1")->fetch();
        $tipe = $tipeAsli;
        require_once __DIR__ . '/../../resources/views/kurikulum/supervisi_cetak.php';
    }

    public function programSupervisi() {
        $active_module = 'kurikulum';
        $active_page   = 'program_supervisi';
        
        $db = \App\Core\Database::connect('core');
        $programs = $db->query("SELECT * FROM kurikulum_supervisi_program ORDER BY id DESC")->fetchAll();
        $ta_list = $db->query("SELECT * FROM tahun_ajaran ORDER BY name DESC")->fetchAll();

        require_once __DIR__ . '/../../resources/views/kurikulum/supervisi_program.php';
    }

    public function storeProgramSupervisi() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $db = \App\Core\Database::connect('core');
                $judul = $_POST['judul'];
                $tahun_ajaran_id = $_POST['tahun_ajaran_id'];
                $semester = $_POST['semester'];
                $pendahuluan = $_POST['pendahuluan'] ?? '';
                $tujuan = $_POST['tujuan'] ?? '';
                $sasaran = $_POST['sasaran'] ?? '';
                $ruang_lingkup = $_POST['ruang_lingkup'] ?? '';
                $jadwal_pelaksanaan = $_POST['jadwal_pelaksanaan'] ?? '';
                
                $file_path = '';
                if (isset($_FILES['file_program']) && $_FILES['file_program']['error'] == 0) {
                    $uploadDir = __DIR__ . '/../../public/uploads/supervisi/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $fileExtension = pathinfo($_FILES['file_program']['name'], PATHINFO_EXTENSION);
                    $filename = 'program_' . time() . '.' . $fileExtension;
                    if (move_uploaded_file($_FILES['file_program']['tmp_name'], $uploadDir . $filename)) {
                        $file_path = $filename;
                    }
                }

                $tanggal = !empty($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d');
                $created_at = $tanggal . ' ' . date('H:i:s');

                $stmt = $db->prepare("INSERT INTO kurikulum_supervisi_program (judul, tahun_ajaran_id, semester, file_path, pendahuluan, tujuan, sasaran, ruang_lingkup, jadwal_pelaksanaan, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$judul, $tahun_ajaran_id, $semester, $file_path, $pendahuluan, $tujuan, $sasaran, $ruang_lingkup, $jadwal_pelaksanaan, $created_at]);
                
                header('Location: ' . base_url('kurikulum/supervisi/program'));
                exit;
            } catch (\Exception $e) {
                die("Error saving data: " . $e->getMessage());
            }
        }
    }

    public function updateProgramSupervisi() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            try {
                $db = \App\Core\Database::connect('core');
                $id = $_POST['id'];
                $judul = $_POST['judul'];
                $tahun_ajaran_id = $_POST['tahun_ajaran_id'];
                $semester = $_POST['semester'];
                $pendahuluan = $_POST['pendahuluan'] ?? '';
                $tujuan = $_POST['tujuan'] ?? '';
                $sasaran = $_POST['sasaran'] ?? '';
                $ruang_lingkup = $_POST['ruang_lingkup'] ?? '';
                $jadwal_pelaksanaan = $_POST['jadwal_pelaksanaan'] ?? '';
                
                $program = $db->query("SELECT * FROM kurikulum_supervisi_program WHERE id = " . intval($id))->fetch();
                $file_path = $program['file_path'];

                if (isset($_FILES['file_program']) && $_FILES['file_program']['error'] == 0) {
                    $uploadDir = __DIR__ . '/../../public/uploads/supervisi/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $fileExtension = pathinfo($_FILES['file_program']['name'], PATHINFO_EXTENSION);
                    $filename = 'program_' . time() . '.' . $fileExtension;
                    if (move_uploaded_file($_FILES['file_program']['tmp_name'], $uploadDir . $filename)) {
                        if ($file_path && file_exists($uploadDir . $file_path)) {
                            unlink($uploadDir . $file_path);
                        }
                        $file_path = $filename;
                    }
                }

                $created_at_sql = "";
                $params = [$judul, $tahun_ajaran_id, $semester, $file_path, $pendahuluan, $tujuan, $sasaran, $ruang_lingkup, $jadwal_pelaksanaan];
                if (!empty($_POST['tanggal'])) {
                    $created_at_sql = ", created_at = ?";
                    $params[] = $_POST['tanggal'] . ' ' . date('H:i:s');
                }
                $params[] = $id;

                $stmt = $db->prepare("UPDATE kurikulum_supervisi_program SET judul = ?, tahun_ajaran_id = ?, semester = ?, file_path = ?, pendahuluan = ?, tujuan = ?, sasaran = ?, ruang_lingkup = ?, jadwal_pelaksanaan = ? {$created_at_sql} WHERE id = ?");
                $stmt->execute($params);
                
                header('Location: ' . base_url('kurikulum/supervisi/program'));
                exit;
            } catch (\Exception $e) {
                die("Error: " . $e->getMessage());
            }
        }
    }

    public function deleteProgramSupervisi() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            try {
                $db = \App\Core\Database::connect('core');
                $id = $_POST['id'];
                
                $program = $db->query("SELECT * FROM kurikulum_supervisi_program WHERE id = " . intval($id))->fetch();
                if ($program && $program['file_path']) {
                    $uploadDir = __DIR__ . '/../../public/uploads/supervisi/';
                    if (file_exists($uploadDir . $program['file_path'])) {
                        unlink($uploadDir . $program['file_path']);
                    }
                }

                $stmt = $db->prepare("DELETE FROM kurikulum_supervisi_program WHERE id = ?");
                $stmt->execute([$id]);
                header('Location: ' . base_url('kurikulum/supervisi/program'));
                exit;
            } catch (\Exception $e) {
                die("Error: " . $e->getMessage());
            }
        }
    }

    public function cetakProgramSupervisi() {
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: ' . base_url('kurikulum/supervisi/program')); exit; }
        
        $db = \App\Core\Database::connect('core');
        $program = $db->query("SELECT p.*, ta.name as ta_nama FROM kurikulum_supervisi_program p LEFT JOIN tahun_ajaran ta ON p.tahun_ajaran_id = ta.id WHERE p.id = " . intval($id))->fetch();
        if (!$program) { header('Location: ' . base_url('kurikulum/supervisi/program')); exit; }
        
        $legalitas = self::getSupervisiLegalitas($db);
        extract($legalitas);
        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch() ?: [];

        $countGuru = 12;
        $countSiswa = 140;
        try {
            $cg = $db->query("SELECT COUNT(*) FROM guru")->fetchColumn();
            if ($cg > 0) $countGuru = (int)$cg;
        } catch (\Throwable $e) {}
        try {
            $siakad_db = \App\Core\Database::connect('siakad');
            $cs = $siakad_db->query("SELECT COUNT(*) FROM siswa WHERE status = 'Aktif'")->fetchColumn();
            if ($cs > 0) $countSiswa = (int)$cs;
        } catch (\Throwable $e) {}

        require_once __DIR__ . '/../../resources/views/kurikulum/supervisi_program_cetak.php';
    }

    public function berkasPribadi() {
        $active_module = 'kurikulum';
        $active_page   = 'berkas_pribadi';
        
        $db = \App\Core\Database::connect('core');
        
        if (isset($_GET['guru_id'])) {
            $guru_id = intval($_GET['guru_id']);
            $guru_nama = $db->query("SELECT nama FROM guru WHERE id = $guru_id")->fetchColumn();
            
            // Fetch dari tabel guru_berkas yang benar sesuai dengan apk mobile
            $berkas_list = [];
            try {
                $berkas_list = $db->query("SELECT * FROM guru_berkas WHERE guru_id = $guru_id ORDER BY id DESC")->fetchAll();
            } catch (\Exception $e) {}
            
            require_once __DIR__ . '/../../resources/views/kurikulum/berkas_pribadi_detail.php';
        } else {
            $guru_list = $db->query("SELECT id, nama as nama_lengkap FROM guru ORDER BY nama ASC")->fetchAll();
            require_once __DIR__ . '/../../resources/views/kurikulum/berkas_pribadi.php';
        }
    }

    public function berkasPerangkat() {
        $active_module = 'kurikulum';
        $active_page   = 'berkas_perangkat';

        $db_siakad = \App\Core\Database::connect('siakad');
        $db_core   = \App\Core\Database::connect('core');

        // Parameter filter
        $filter_guru   = isset($_GET['guru_id']) ? intval($_GET['guru_id']) : 0;
        $filter_jenis  = isset($_GET['jenis']) ? trim($_GET['jenis']) : '';
        $filter_kelas  = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : 0;
        $filter_mapel  = isset($_GET['mapel_id']) ? intval($_GET['mapel_id']) : 0;
        $filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';
        $q             = isset($_GET['q']) ? trim($_GET['q']) : '';

        // Dropdowns
        $guru_list  = $db_core->query("SELECT id, nama as nama_lengkap, nip FROM guru ORDER BY nama ASC")->fetchAll();
        $kelas_list = $db_core->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll();
        
        $mapel_list = [];
        try {
            $mapel_list = $db_siakad->query("SELECT id, nama_mapel FROM mapel ORDER BY nama_mapel ASC")->fetchAll();
        } catch (\Exception $e) {
            try {
                $mapel_list = $db_siakad->query("SELECT id, nama_mapel FROM mata_pelajaran ORDER BY nama_mapel ASC")->fetchAll();
            } catch (\Exception $ex) {}
        }

        $jenis_list = [];
        try {
            $jenis_list = $db_siakad->query("SELECT DISTINCT jenis_perangkat FROM perangkat_pembelajaran WHERE jenis_perangkat IS NOT NULL AND jenis_perangkat != '' ORDER BY jenis_perangkat ASC")->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\Exception $e) {}

        // Statistik Cepat
        $stats = [
            'total'        => 0,
            'modul_rpp'    => 0,
            'atp_silabus'  => 0,
            'prota_promes' => 0,
            'kktp_cp'      => 0,
            'disetujui'    => 0,
            'menunggu'     => 0,
            'revisi'       => 0,
            'guru_count'   => 0
        ];

        try {
            $stats['total']        = (int)$db_siakad->query("SELECT COUNT(*) FROM perangkat_pembelajaran")->fetchColumn();
            $stats['modul_rpp']    = (int)$db_siakad->query("SELECT COUNT(*) FROM perangkat_pembelajaran WHERE jenis_perangkat LIKE '%Modul%' OR jenis_perangkat LIKE '%RPP%'")->fetchColumn();
            $stats['atp_silabus']  = (int)$db_siakad->query("SELECT COUNT(*) FROM perangkat_pembelajaran WHERE jenis_perangkat LIKE '%ATP%' OR jenis_perangkat LIKE '%Silabus%'")->fetchColumn();
            $stats['prota_promes'] = (int)$db_siakad->query("SELECT COUNT(*) FROM perangkat_pembelajaran WHERE jenis_perangkat LIKE '%Prota%' OR jenis_perangkat LIKE '%Promes%'")->fetchColumn();
            $stats['kktp_cp']      = (int)$db_siakad->query("SELECT COUNT(*) FROM perangkat_pembelajaran WHERE jenis_perangkat LIKE '%KKTP%' OR jenis_perangkat LIKE '%Capaian%'")->fetchColumn();
            $stats['disetujui']    = (int)$db_siakad->query("SELECT COUNT(*) FROM perangkat_pembelajaran WHERE status = 'Disetujui'")->fetchColumn();
            $stats['menunggu']     = (int)$db_siakad->query("SELECT COUNT(*) FROM perangkat_pembelajaran WHERE status = 'Menunggu'")->fetchColumn();
            $stats['revisi']       = (int)$db_siakad->query("SELECT COUNT(*) FROM perangkat_pembelajaran WHERE status = 'Revisi'")->fetchColumn();
            $stats['guru_count']   = (int)$db_siakad->query("SELECT COUNT(DISTINCT guru_id) FROM perangkat_pembelajaran")->fetchColumn();
        } catch (\Exception $e) {}

        // Susun Query Filter
        $where = ["1=1"];
        $params = [];

        if ($filter_guru > 0) {
            $where[] = "p.guru_id = :guru_id";
            $params[':guru_id'] = $filter_guru;
        }

        if (!empty($filter_jenis)) {
            $where[] = "p.jenis_perangkat = :jenis";
            $params[':jenis'] = $filter_jenis;
        }

        if ($filter_kelas > 0) {
            $where[] = "p.kelas_id = :kelas_id";
            $params[':kelas_id'] = $filter_kelas;
        }

        if ($filter_mapel > 0) {
            $where[] = "p.mapel_id = :mapel_id";
            $params[':mapel_id'] = $filter_mapel;
        }

        if (!empty($filter_status)) {
            $where[] = "p.status = :status";
            $params[':status'] = $filter_status;
        }

        if (!empty($q)) {
            $where[] = "(p.judul LIKE :q OR p.nama_file LIKE :q OR g.nama LIKE :q OR m.nama_mapel LIKE :q OR k.nama_kelas LIKE :q)";
            $params[':q'] = "%$q%";
        }

        $whereClause = implode(" AND ", $where);

        // Hitung Total Data Terfilter & Paginasi
        $count_stmt = $db_siakad->prepare("
            SELECT COUNT(*) 
            FROM perangkat_pembelajaran p
            LEFT JOIN core_db.guru g ON g.id = p.guru_id
            LEFT JOIN mapel m ON m.id = p.mapel_id
            LEFT JOIN core_db.kelas k ON k.id = p.kelas_id
            WHERE $whereClause
        ");
        $count_stmt->execute($params);
        $total_filtered = (int)$count_stmt->fetchColumn();

        $page     = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $per_page = 25;
        $total_pages = ceil($total_filtered / $per_page);
        if ($page > $total_pages && $total_pages > 0) $page = $total_pages;
        $offset   = ($page - 1) * $per_page;

        $stmt = $db_siakad->prepare("
            SELECT p.*, g.nama as nama_guru, g.nip as nip_guru, g.foto as foto_guru,
                   m.nama_mapel, k.nama_kelas
            FROM perangkat_pembelajaran p
            LEFT JOIN core_db.guru g ON g.id = p.guru_id
            LEFT JOIN mapel m ON m.id = p.mapel_id
            LEFT JOIN core_db.kelas k ON k.id = p.kelas_id
            WHERE $whereClause
            ORDER BY p.id DESC
            LIMIT $per_page OFFSET $offset
        ");
        $stmt->execute($params);
        $perangkat_list = $stmt->fetchAll();

        // Cek status tautan monitoring pengawas ke PKKM
        $is_pkkm_linked = false;
        try {
            $is_pkkm_linked = (bool)$db_core->query("SELECT id FROM pkkm_master_berkas_file WHERE url = '/admin/pkkm/cetak-bukti/perangkat-guru' LIMIT 1")->fetch();
        } catch (\Exception $e) {}

        require_once __DIR__ . '/../../resources/views/kurikulum/berkas_perangkat.php';
    }

    public function uploadBerkasPerangkat() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db_siakad = \App\Core\Database::connect('siakad');
            $guru_id         = intval($_POST['guru_id'] ?? 0);
            $kelas_id        = intval($_POST['kelas_id'] ?? 0);
            $mapel_id        = intval($_POST['mapel_id'] ?? 0);
            $jenis_perangkat = trim($_POST['jenis_perangkat'] ?? '');
            $judul           = trim($_POST['judul'] ?? '');
            $status          = in_array($_POST['status'] ?? '', ['Disetujui', 'Menunggu', 'Revisi']) ? $_POST['status'] : 'Disetujui';

            if (empty($guru_id) || empty($jenis_perangkat) || empty($judul)) {
                header("Location: /kurikulum/berkas/perangkat?err=" . urlencode("Semua kolom wajib diisi."));
                exit;
            }

            if (isset($_FILES['file_perangkat']) && $_FILES['file_perangkat']['error'] === UPLOAD_ERR_OK) {
                $fileName = $_FILES['file_perangkat']['name'];
                $fileTmp  = $_FILES['file_perangkat']['tmp_name'];
                $ext      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if (!in_array($ext, ['pdf', 'doc', 'docx', 'zip', 'rar'])) {
                    header("Location: /kurikulum/berkas/perangkat?err=" . urlencode("Format file harus berupa PDF, Word, atau Arsip."));
                    exit;
                }

                $newFileName = md5(time() . '_' . $fileName) . '.' . $ext;
                $uploadDir   = __DIR__ . '/../../public/uploads/perangkat/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $destPath = $uploadDir . $newFileName;
                $dbPath   = '/public/uploads/perangkat/' . $newFileName;

                if (move_uploaded_file($fileTmp, $destPath)) {
                    $stmt = $db_siakad->prepare("INSERT INTO perangkat_pembelajaran (guru_id, mapel_id, kelas_id, jenis_perangkat, judul, nama_file, file_path, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                    $stmt->execute([$guru_id, $mapel_id, $kelas_id, $jenis_perangkat, $judul, $fileName, $dbPath, $status]);
                    header('Location: ' . base_url('kurikulum/berkas/perangkat?msg=uploaded'));
                    exit;
                } else {
                    header("Location: /kurikulum/berkas/perangkat?err=" . urlencode("Gagal mengunggah file ke server."));
                    exit;
                }
            } else {
                header("Location: /kurikulum/berkas/perangkat?err=" . urlencode("Harap pilih file yang akan diunggah."));
                exit;
            }
        }
    }

    public function updateStatusPerangkat() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id       = intval($_POST['id'] ?? 0);
            $status   = in_array($_POST['status'] ?? '', ['Disetujui', 'Menunggu', 'Revisi']) ? $_POST['status'] : 'Disetujui';
            $komentar = trim($_POST['komentar_kamad'] ?? '');

            if ($id > 0) {
                $db_siakad = \App\Core\Database::connect('siakad');
                $stmt = $db_siakad->prepare("UPDATE perangkat_pembelajaran SET status = ?, komentar_kamad = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$status, $komentar, $id]);
                
                $ref = $_SERVER['HTTP_REFERER'] ?? '/kurikulum/berkas/perangkat';
                $separator = (strpos($ref, '?') !== false) ? '&' : '?';
                header("Location: {$ref}{$separator}msg=status_updated");
                exit;
            }
        }
        header('Location: ' . base_url('kurikulum/berkas/perangkat'));
        exit;
    }

    public function deleteBerkasPerangkat() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id'] ?? 0);
            if ($id > 0) {
                $db_siakad = \App\Core\Database::connect('siakad');
                $stmt = $db_siakad->prepare("DELETE FROM perangkat_pembelajaran WHERE id = ?");
                $stmt->execute([$id]);

                $ref = $_SERVER['HTTP_REFERER'] ?? '/kurikulum/berkas/perangkat';
                $separator = (strpos($ref, '?') !== false) ? '&' : '?';
                header("Location: {$ref}{$separator}msg=deleted");
                exit;
            }
        }
        header('Location: ' . base_url('kurikulum/berkas/perangkat'));
        exit;
    }

    public function syncPerangkatToPkkm() {
        try {
            $db_core   = \App\Core\Database::connect('core');
            $db_siakad = \App\Core\Database::connect('siakad');

            $existing = $db_core->query("SELECT master_berkas_id, url FROM pkkm_master_berkas_file")->fetchAll(\PDO::FETCH_ASSOC);
            $existMap = [];
            foreach ($existing as $e) {
                $existMap[$e['master_berkas_id'] . '_' . $e['url']] = true;
            }

            $perangkatList = $db_siakad->query("SELECT * FROM perangkat_pembelajaran")->fetchAll(\PDO::FETCH_ASSOC);
            $syncedCount = 0;

            $insertStmt = $db_core->prepare("INSERT INTO pkkm_master_berkas_file (master_berkas_id, tipe, url, nama_file_asli, created_at) VALUES (?, 'file', ?, ?, ?)");

            foreach ($perangkatList as $p) {
                $targetMasters = [173]; // Master Berkas ID 173: "Perangkat pembelajaran"
                $jenis = strtolower($p['jenis_perangkat'] ?? '');
                $judul = strtolower($p['judul'] ?? '');

                if (strpos($jenis, 'modul') !== false || strpos($jenis, 'rpp') !== false || strpos($judul, 'rpp') !== false || strpos($judul, 'modul') !== false) {
                    $targetMasters[] = 207; // Master Berkas ID 207: "RPP"
                    $targetMasters[] = 208; // Master Berkas ID 208: "RPP yang memuat..."
                }

                if (strpos($jenis, 'silabus') !== false || strpos($jenis, 'atp') !== false || strpos($judul, 'silabus') !== false || strpos($judul, 'atp') !== false) {
                    $targetMasters[] = 69; // Master Berkas ID 69: "Dokumen pengembangan silabus"
                }

                $targetMasters = array_unique($targetMasters);

                foreach ($targetMasters as $mid) {
                    $key = $mid . '_' . $p['file_path'];
                    if (!isset($existMap[$key])) {
                        $label = ($p['judul'] ? $p['judul'] . ' - ' : '') . $p['nama_file'];
                        $insertStmt->execute([$mid, $p['file_path'], $label, $p['created_at'] ?: date('Y-m-d H:i:s')]);
                        $existMap[$key] = true;
                        $syncedCount++;
                    }
                }
            }

            header("Location: /kurikulum/berkas/perangkat?msg=synced_pkkm&count=" . $syncedCount);
            exit;
        } catch (\Exception $e) {
            header("Location: /kurikulum/berkas/perangkat?err=" . urlencode($e->getMessage()));
            exit;
        }
    }

    public function unsyncPerangkatFromPkkm() {
        try {
            $db_core = \App\Core\Database::connect('core');
            $stmt = $db_core->prepare("DELETE FROM pkkm_master_berkas_file WHERE url LIKE '/public/uploads/perangkat/%'");
            $stmt->execute();
            $deletedCount = $stmt->rowCount();

            header("Location: /kurikulum/berkas/perangkat?msg=unsynced_pkkm&count=" . $deletedCount);
            exit;
        } catch (\Exception $e) {
            header("Location: /kurikulum/berkas/perangkat?err=" . urlencode($e->getMessage()));
            exit;
        }
    }

    public function pasangLinkPkkm() {
        try {
            $db_core = \App\Core\Database::connect('core');
            $targetMasters = [69, 153, 173, 207, 208];
            $url = '/admin/pkkm/cetak-bukti/perangkat-guru';
            $nama_file = 'Display Monitoring & Arsip Digital Perangkat Guru (RPP, Modul Ajar, Silabus)';

            $attached = 0;
            foreach ($targetMasters as $mid) {
                $exists = $db_core->query("SELECT id FROM pkkm_master_berkas_file WHERE master_berkas_id = $mid AND url = '$url'")->fetch();
                if (!$exists) {
                    $stmt = $db_core->prepare("INSERT INTO pkkm_master_berkas_file (master_berkas_id, tipe, url, nama_file_asli, created_at) VALUES (?, 'link', ?, ?, NOW())");
                    $stmt->execute([$mid, $url, $nama_file]);
                    $attached++;
                }
            }

            header("Location: /kurikulum/berkas/perangkat?msg=link_attached&count=" . $attached);
            exit;
        } catch (\Exception $e) {
            header("Location: /kurikulum/berkas/perangkat?err=" . urlencode($e->getMessage()));
            exit;
        }
    }

    public function lepasLinkPkkm() {
        try {
            $db_core = \App\Core\Database::connect('core');
            $url = '/admin/pkkm/cetak-bukti/perangkat-guru';
            $stmt = $db_core->prepare("DELETE FROM pkkm_master_berkas_file WHERE url = ?");
            $stmt->execute([$url]);
            $del = $stmt->rowCount();

            header("Location: /kurikulum/berkas/perangkat?msg=link_detached&count=" . $del);
            exit;
        } catch (\Exception $e) {
            header("Location: /kurikulum/berkas/perangkat?err=" . urlencode($e->getMessage()));
            exit;
        }
    }

    public function supervisiAdministrasi() {
        $active_module = 'kurikulum';
        $active_page   = 'supervisi_administrasi';
        
        $pegawai = [];
        $jadwal_supervisi = [];
        try {
            $db = \App\Core\Database::connect('core');
            $stmt = $db->query("SELECT id, nama as nama_lengkap FROM guru ORDER BY nama ASC");
            $pegawai = $stmt->fetchAll();
            
            // Get records
            $stmt_jadwal = $db->query("
                SELECT sa.*, 
                       COALESCE(sa.pegawai_id, sa.guru_id) as pegawai_id,
                       COALESCE(sa.supervisor_id, sa.penilai_id) as supervisor_id,
                       COALESCE(sa.tanggal, sa.tanggal_supervisi) as tanggal,
                       COALESCE(sa.nilai, sa.nilai_akhir, 0) as nilai,
                       IF((sa.nilai IS NOT NULL AND sa.nilai > 0) OR (sa.nilai_akhir IS NOT NULL AND sa.nilai_akhir > 0), 'Dievaluasi', COALESCE(sa.status, 'Jadwal')) as status,
                       COALESCE(g.nama, 'Pegawai') as nama_pegawai, 
                       COALESCE(sp.nama, sa.penilai_nama, 'Supervisor') as nama_supervisor 
                FROM kurikulum_supervisi_administrasi sa
                LEFT JOIN guru g ON COALESCE(sa.pegawai_id, sa.guru_id) = g.id
                LEFT JOIN guru sp ON COALESCE(sa.supervisor_id, sa.penilai_id) = sp.id
                ORDER BY COALESCE(sa.tanggal, sa.tanggal_supervisi) DESC, sa.id DESC
            ");
            $jadwal_supervisi = $stmt_jadwal ? $stmt_jadwal->fetchAll() : [];
        } catch (\Exception $e) {
        }
        
        require_once __DIR__ . '/../../resources/views/kurikulum/supervisi_administrasi.php';
    }

    public function storeSupervisiAdministrasi() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $db = \App\Core\Database::connect('core');
                $pegawai_id = (int)$_POST['pegawai_id'];
                $supervisor_id = (int)$_POST['supervisor_id'];
                $tanggal = $_POST['tanggal'];
                $keterangan = $_POST['keterangan'] ?? '';

                // Proteksi duplikasi: Jika guru & tanggal yang sama sudah terdaftar
                $cekDobel = $db->prepare("SELECT id FROM kurikulum_supervisi_administrasi WHERE (pegawai_id = ? OR guru_id = ?) AND (tanggal = ? OR tanggal_supervisi = ?) LIMIT 1");
                $cekDobel->execute([$pegawai_id, $pegawai_id, $tanggal, $tanggal]);
                if ($cekDobel->fetchColumn()) {
                    if (session_status() === PHP_SESSION_NONE) session_start();
                    $_SESSION['alert'] = ['type' => 'warning', 'message' => 'Jadwal supervisi administrasi untuk guru dan tanggal tersebut sudah terdaftar!'];
                    header('Location: ' . base_url('kurikulum/supervisi/administrasi'));
                    exit;
                }

                $spNama = $db->query("SELECT nama FROM guru WHERE id = $supervisor_id")->fetchColumn() ?: 'Supervisor';

                $stmt = $db->prepare("INSERT INTO kurikulum_supervisi_administrasi (pegawai_id, guru_id, supervisor_id, penilai_id, penilai_nama, tanggal, tanggal_supervisi, keterangan, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Jadwal')");
                $stmt->execute([
                    $pegawai_id,
                    $pegawai_id,
                    $supervisor_id,
                    $supervisor_id,
                    $spNama,
                    $tanggal,
                    $tanggal,
                    $keterangan
                ]);
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['alert'] = ['type' => 'success', 'message' => 'Jadwal supervisi administrasi berhasil ditambahkan!'];
                header('Location: ' . base_url('kurikulum/supervisi/administrasi'));
                exit;
            } catch (\Exception $e) {
                die("Error saving data: " . $e->getMessage());
            }
        }
    }

    public function updateSupervisiAdministrasi() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            try {
                $db = \App\Core\Database::connect('core');
                $pegawai_id = (int)$_POST['pegawai_id'];
                $supervisor_id = (int)$_POST['supervisor_id'];
                $tanggal = $_POST['tanggal'];
                $keterangan = $_POST['keterangan'] ?? '';
                $id = (int)$_POST['id'];

                $spNama = $db->query("SELECT nama FROM guru WHERE id = $supervisor_id")->fetchColumn() ?: 'Supervisor';

                $stmt = $db->prepare("UPDATE kurikulum_supervisi_administrasi SET pegawai_id = ?, guru_id = ?, supervisor_id = ?, penilai_id = ?, penilai_nama = ?, tanggal = ?, tanggal_supervisi = ?, keterangan = ? WHERE id = ?");
                $stmt->execute([$pegawai_id, $pegawai_id, $supervisor_id, $supervisor_id, $spNama, $tanggal, $tanggal, $keterangan, $id]);
                header('Location: ' . base_url('kurikulum/supervisi/administrasi'));
                exit;
            } catch (\Exception $e) {
                die("Error: " . $e->getMessage());
            }
        }
    }

    public function deleteSupervisiAdministrasi() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            try {
                $db = \App\Core\Database::connect('core');
                $stmt = $db->prepare("DELETE FROM kurikulum_supervisi_administrasi WHERE id = ?");
                $stmt->execute([$id]);
                try {
                    $db->prepare("DELETE FROM kurikulum_supervisi_hasil WHERE tipe_supervisi = 'Administrasi' AND supervisi_id = ?")->execute([$id]);
                } catch (\Throwable $e) {}
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['alert'] = ['type' => 'success', 'message' => 'Data supervisi administrasi berhasil dihapus!'];
                header('Location: ' . base_url('kurikulum/supervisi/administrasi'));
                exit;
            } catch (\Exception $e) {
                die("Error: " . $e->getMessage());
            }
        }
    }

    public function evaluasiSupervisiAdministrasiForm() {
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: ' . base_url('kurikulum/supervisi/administrasi')); exit; }
        
        $db = \App\Core\Database::connect('core');
        $supervisi = $db->query("
            SELECT sa.*, 
                   COALESCE(sa.pegawai_id, sa.guru_id) as pegawai_id,
                   COALESCE(sa.supervisor_id, sa.penilai_id) as supervisor_id,
                   COALESCE(sa.tanggal, sa.tanggal_supervisi) as tanggal,
                   COALESCE(sa.nilai, sa.nilai_akhir, 0) as nilai,
                   COALESCE(g.nama, 'Pegawai') as nama_pegawai, 
                   COALESCE(g.nip, '-') as nip_guru, 
                   COALESCE(sp.nama, sa.penilai_nama, 'Supervisor') as nama_supervisor, 
                   COALESCE(sp.nip, '-') as nip_supervisor 
            FROM kurikulum_supervisi_administrasi sa 
            LEFT JOIN guru g ON COALESCE(sa.pegawai_id, sa.guru_id) = g.id 
            LEFT JOIN guru sp ON COALESCE(sa.supervisor_id, sa.penilai_id) = sp.id 
            WHERE sa.id = $id
        ")->fetch();
        
        $instrumen = $db->query("SELECT * FROM kurikulum_instrumen_supervisi WHERE tipe_supervisi = 'Administrasi' ORDER BY CAST(kode AS UNSIGNED) ASC, id ASC")->fetchAll();
        $hasil_raw = $db->query("SELECT instrumen_id, skor FROM kurikulum_supervisi_hasil WHERE tipe_supervisi = 'Administrasi' AND supervisi_id = $id")->fetchAll(\PDO::FETCH_ASSOC);
        $hasil = [];
        foreach($hasil_raw as $r) { $hasil[$r['instrumen_id']] = $r['skor']; }

        $active_module = 'kurikulum';
        $active_page   = 'supervisi_administrasi';
        $tipe = 'Administrasi';
        require_once __DIR__ . '/../../resources/views/kurikulum/supervisi_evaluasi.php';
    }

    public function storeEvaluasiSupervisiAdministrasi() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $db = \App\Core\Database::connect('core');
            $db->beginTransaction();
            try {
                $db->query("DELETE FROM kurikulum_supervisi_hasil WHERE tipe_supervisi = 'Administrasi' AND supervisi_id = $id");
                $stmt_hasil = $db->prepare("INSERT INTO kurikulum_supervisi_hasil (tipe_supervisi, supervisi_id, instrumen_id, skor) VALUES ('Administrasi', ?, ?, ?)");
                
                $totalSkor = 0;
                $maxSkor = 0;
                
                $instrumen = $db->query("SELECT id, bobot_maksimal FROM kurikulum_instrumen_supervisi WHERE tipe_supervisi = 'Administrasi'")->fetchAll();
                foreach($instrumen as $ins) {
                    $maxSkor += $ins['bobot_maksimal'];
                }

                if (isset($_POST['skor']) && is_array($_POST['skor'])) {
                    foreach ($_POST['skor'] as $ins_id => $skor) {
                        $stmt_hasil->execute([$id, $ins_id, $skor]);
                        $totalSkor += $skor;
                    }
                }
                
                $nilaiAkhir = ($maxSkor > 0) ? round(($totalSkor / $maxSkor) * 100) : 0;
                $catatan = $_POST['catatan_evaluasi'] ?? '';
                
                $stmt_update = $db->prepare("UPDATE kurikulum_supervisi_administrasi SET nilai = ?, nilai_akhir = ?, catatan_evaluasi = ?, catatan = ?, status = 'Dievaluasi' WHERE id = ?");
                $stmt_update->execute([$nilaiAkhir, $nilaiAkhir, $catatan, $catatan, $id]);
                
                $db->commit();
                header('Location: ' . base_url('kurikulum/supervisi/administrasi'));
                exit;
            } catch (\Exception $e) {
                $db->rollBack();
                die("Error: " . $e->getMessage());
            }
        }
    }

    public function cetakSupervisiAdministrasi() {
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: ' . base_url('kurikulum/supervisi/administrasi')); exit; }
        
        $db = \App\Core\Database::connect('core');
        $supervisi = $db->query("
            SELECT sa.*, 
                   COALESCE(sa.pegawai_id, sa.guru_id) as pegawai_id,
                   COALESCE(sa.supervisor_id, sa.penilai_id) as supervisor_id,
                   COALESCE(sa.tanggal, sa.tanggal_supervisi) as tanggal,
                   COALESCE(sa.nilai, sa.nilai_akhir, 0) as nilai,
                   COALESCE(g.nama, 'Pegawai') as nama_pegawai, 
                   COALESCE(g.nip, '-') as nip_guru, 
                   COALESCE(sp.nama, sa.penilai_nama, 'Supervisor') as nama_supervisor, 
                   COALESCE(sp.nip, '-') as nip_supervisor 
            FROM kurikulum_supervisi_administrasi sa 
            LEFT JOIN guru g ON COALESCE(sa.pegawai_id, sa.guru_id) = g.id 
            LEFT JOIN guru sp ON COALESCE(sa.supervisor_id, sa.penilai_id) = sp.id 
            WHERE sa.id = $id
        ")->fetch();
        
        $instrumen = $db->query("SELECT * FROM kurikulum_instrumen_supervisi WHERE tipe_supervisi = 'Administrasi' ORDER BY CAST(kode AS UNSIGNED) ASC, id ASC")->fetchAll();
        $hasil_raw = $db->query("SELECT instrumen_id, skor FROM kurikulum_supervisi_hasil WHERE tipe_supervisi = 'Administrasi' AND supervisi_id = $id")->fetchAll(\PDO::FETCH_ASSOC);
        $hasil = [];
        foreach($hasil_raw as $r) { $hasil[$r['instrumen_id']] = $r['skor']; }
        
        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();

        $tipe = 'Administrasi';
        require_once __DIR__ . '/../../resources/views/kurikulum/supervisi_cetak.php';
    }

    public function rilisSupervisiAdministrasi() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['id']) || !isset($_POST['is_released'])) {
                echo json_encode(['success' => false, 'message' => 'Missing params']);
                exit;
            }
            $id = $_POST['id'];
            $is_released = $_POST['is_released'];
            $db = \App\Core\Database::connect('core');
            try {
                $stmt = $db->prepare("UPDATE kurikulum_supervisi_administrasi SET is_released = ? WHERE id = ?");
                $stmt->execute([$is_released, $id]);
                echo json_encode(['success' => true]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }
        echo json_encode(['success' => false, 'message' => 'Invalid method']);
        exit;
    }

    public function rilisHasilSupervisiAdministrasi() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['id']) || !isset($_POST['is_hasil_released'])) {
                echo json_encode(['success' => false, 'message' => 'Missing params']);
                exit;
            }
            $id = $_POST['id'];
            $is_released = $_POST['is_hasil_released'];
            $db = \App\Core\Database::connect('core');
            try {
                $stmt = $db->prepare("UPDATE kurikulum_supervisi_administrasi SET is_hasil_released = ? WHERE id = ?");
                $stmt->execute([$is_released, $id]);
                echo json_encode(['success' => true]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }
        echo json_encode(['success' => false, 'message' => 'Invalid method']);
        exit;
    }

    public function supervisiKelas() {
        $active_module = 'kurikulum';
        $active_page   = 'supervisi_kelas';
        
        $gurus = [];
        $jadwal_supervisi = [];
        try {
            $db = \App\Core\Database::connect('core');
            $stmt = $db->query("SELECT id, nama as nama_lengkap FROM guru ORDER BY nama ASC");
            $gurus = $stmt->fetchAll();
            
            $stmt_jadwal = $db->query("
                SELECT sk.*, 
                       COALESCE(sk.tanggal, sk.tanggal_supervisi) as tanggal,
                       COALESCE(sk.nilai, sk.nilai_akhir, 0) as nilai,
                       IF((sk.nilai IS NOT NULL AND sk.nilai > 0) OR (sk.nilai_akhir IS NOT NULL AND sk.nilai_akhir > 0), 'Dievaluasi', COALESCE(sk.status, 'Jadwal')) as status,
                       COALESCE(g.nama, 'Guru') as nama_guru, 
                       COALESCE(sp.nama, sk.penilai_nama, 'Supervisor') as nama_supervisor 
                FROM kurikulum_supervisi_kelas sk
                LEFT JOIN guru g ON sk.guru_id = g.id
                LEFT JOIN guru sp ON sk.supervisor_id = sp.id
                ORDER BY COALESCE(sk.tanggal, sk.tanggal_supervisi) DESC, sk.id DESC
            ");
            $jadwal_supervisi = $stmt_jadwal ? $stmt_jadwal->fetchAll() : [];

            $siakad_db = \App\Core\Database::connect('siakad');
            $mapels = [];
            $kelasList = [];
            try {
                $mapels = $siakad_db->query("SELECT id, nama_mapel FROM mapel")->fetchAll(\PDO::FETCH_KEY_PAIR) ?: [];
                $kelasList = $siakad_db->query("SELECT id, nama_kelas FROM kelas")->fetchAll(\PDO::FETCH_KEY_PAIR) ?: [];
            } catch (\Throwable $e) {}

            foreach($jadwal_supervisi as &$j) {
                $j['nama_mapel'] = !empty($j['mata_pelajaran']) ? $j['mata_pelajaran'] : ($mapels[$j['mapel_id'] ?? 0] ?? 'Umum');
                $j['nama_kelas'] = !empty($j['kelas']) ? $j['kelas'] : ($kelasList[$j['kelas_id'] ?? 0] ?? 'Umum');
            }
            unset($j);
        } catch (\Exception $e) {
        }
        
        require_once __DIR__ . '/../../resources/views/kurikulum/supervisi_kelas.php';
    }

    public function rilisSupervisiKelas() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['id']) || !isset($_POST['is_released'])) {
                echo json_encode(['success' => false, 'message' => 'Missing params']);
                exit;
            }
            $id = $_POST['id'];
            $is_released = $_POST['is_released'];
            $db = \App\Core\Database::connect('core');
            try {
                $stmt = $db->prepare("UPDATE kurikulum_supervisi_kelas SET is_released = ? WHERE id = ?");
                $stmt->execute([$is_released, $id]);
                echo json_encode(['success' => true]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }
        echo json_encode(['success' => false, 'message' => 'Invalid method']);
        exit;
    }

    public function rilisHasilSupervisiKelas() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['id']) || !isset($_POST['is_hasil_released'])) {
                echo json_encode(['success' => false, 'message' => 'Missing params']);
                exit;
            }
            $id = $_POST['id'];
            $is_released = $_POST['is_hasil_released'];
            $db = \App\Core\Database::connect('core');
            try {
                $stmt = $db->prepare("UPDATE kurikulum_supervisi_kelas SET is_hasil_released = ? WHERE id = ?");
                $stmt->execute([$is_released, $id]);
                echo json_encode(['success' => true]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }
        echo json_encode(['success' => false, 'message' => 'Invalid method']);
        exit;
    }

    public function supervisiKelasMobile() {
        $active_module = 'kurikulum';
        $active_page   = 'supervisi_kelas';
        
        $gurus = [];
        $jadwal_supervisi = [];
        try {
            $db = \App\Core\Database::connect('core');
            $stmt = $db->query("SELECT id, nama as nama_lengkap FROM guru ORDER BY nama ASC");
            $gurus = $stmt->fetchAll();
            
            if (session_status() === PHP_SESSION_NONE) { session_start(); }
            $guru_id = $_SESSION['guru_id'] ?? 0;

            $stmt_jadwal = $db->prepare("
                SELECT sk.*, 
                       COALESCE(sk.tanggal, sk.tanggal_supervisi) as tanggal,
                       COALESCE(sk.nilai, sk.nilai_akhir, 0) as nilai,
                       IF((sk.nilai IS NOT NULL AND sk.nilai > 0) OR (sk.nilai_akhir IS NOT NULL AND sk.nilai_akhir > 0), 'Dievaluasi', COALESCE(sk.status, 'Jadwal')) as status,
                       COALESCE(g.nama, 'Guru') as nama_guru, 
                       COALESCE(sp.nama, sk.penilai_nama, 'Supervisor') as nama_supervisor 
                FROM kurikulum_supervisi_kelas sk
                LEFT JOIN guru g ON sk.guru_id = g.id
                LEFT JOIN guru sp ON sk.supervisor_id = sp.id
                WHERE sk.supervisor_id = ?
                ORDER BY COALESCE(sk.tanggal, sk.tanggal_supervisi) DESC, sk.id DESC
            ");
            $stmt_jadwal->execute([$guru_id]);
            $jadwal_supervisi = $stmt_jadwal->fetchAll();

            $siakad_db = \App\Core\Database::connect('siakad');
            $mapels = [];
            $kelasList = [];
            try {
                $mapels = $siakad_db->query("SELECT id, nama_mapel FROM mapel")->fetchAll(\PDO::FETCH_KEY_PAIR) ?: [];
                $kelasList = $siakad_db->query("SELECT id, nama_kelas FROM kelas")->fetchAll(\PDO::FETCH_KEY_PAIR) ?: [];
            } catch (\Throwable $e) {}

            foreach($jadwal_supervisi as &$j) {
                $j['nama_mapel'] = !empty($j['mata_pelajaran']) ? $j['mata_pelajaran'] : ($mapels[$j['mapel_id'] ?? 0] ?? 'Mapel ID: ' . ($j['mapel_id'] ?? 0));
                $j['nama_kelas'] = !empty($j['kelas']) ? $j['kelas'] : ($kelasList[$j['kelas_id'] ?? 0] ?? 'Kelas ID: ' . ($j['kelas_id'] ?? 0));
            }
            unset($j);
        } catch (\Exception $e) {
        }
        
        require_once __DIR__ . '/../../resources/views/kurikulum/supervisi_kelas_mobile.php';
    }

    public function storeSupervisiKelas() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $db = \App\Core\Database::connect('core');
                $siakad_db = \App\Core\Database::connect('siakad');
                
                $guru_id = (int)$_POST['guru_id'];
                $kelas_id = !empty($_POST['kelas_id']) ? (int)$_POST['kelas_id'] : null;
                $mapel_id = !empty($_POST['mapel_id']) ? (int)$_POST['mapel_id'] : null;
                $supervisor_id = (int)$_POST['supervisor_id'];
                $tanggal = $_POST['tanggal'];
                $keterangan = $_POST['keterangan'] ?? '';

                // Proteksi duplikasi: Jika guru & tanggal yang sama sudah terdaftar, cegah insert dobel
                $cekDobel = $db->prepare("SELECT id FROM kurikulum_supervisi_kelas WHERE guru_id = ? AND (tanggal = ? OR tanggal_supervisi = ?) LIMIT 1");
                $cekDobel->execute([$guru_id, $tanggal, $tanggal]);
                if ($cekDobel->fetchColumn()) {
                    if (session_status() === PHP_SESSION_NONE) session_start();
                    $_SESSION['alert'] = ['type' => 'warning', 'message' => 'Jadwal supervisi kelas untuk guru dan tanggal tersebut sudah terdaftar!'];
                    header('Location: ' . base_url('kurikulum/supervisi/kelas'));
                    exit;
                }

                $spNama = $db->query("SELECT nama FROM guru WHERE id = $supervisor_id")->fetchColumn() ?: 'Supervisor';
                $namaMapel = $mapel_id ? ($siakad_db->query("SELECT nama_mapel FROM mapel WHERE id = $mapel_id")->fetchColumn() ?: '') : '';
                $namaKelas = $kelas_id ? ($siakad_db->query("SELECT nama_kelas FROM kelas WHERE id = $kelas_id")->fetchColumn() ?: '') : '';

                $stmt = $db->prepare("INSERT INTO kurikulum_supervisi_kelas (guru_id, kelas_id, mapel_id, mata_pelajaran, kelas, supervisor_id, penilai_nama, tanggal, tanggal_supervisi, keterangan, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Jadwal')");
                $stmt->execute([
                    $guru_id,
                    $kelas_id,
                    $mapel_id,
                    $namaMapel,
                    $namaKelas,
                    $supervisor_id,
                    $spNama,
                    $tanggal,
                    $tanggal,
                    $keterangan
                ]);
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['alert'] = ['type' => 'success', 'message' => 'Jadwal supervisi kelas berhasil ditambahkan!'];
                header('Location: ' . base_url('kurikulum/supervisi/kelas'));
                exit;
            } catch (\Exception $e) {
                die("Error saving data: " . $e->getMessage());
            }
        }
    }

    public function updateSupervisiKelas() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            try {
                $db = \App\Core\Database::connect('core');
                $siakad_db = \App\Core\Database::connect('siakad');

                $id = (int)$_POST['id'];
                $guru_id = (int)$_POST['guru_id'];
                $kelas_id = !empty($_POST['kelas_id']) ? (int)$_POST['kelas_id'] : null;
                $mapel_id = !empty($_POST['mapel_id']) ? (int)$_POST['mapel_id'] : null;
                $supervisor_id = (int)$_POST['supervisor_id'];
                $tanggal = $_POST['tanggal'];
                $keterangan = $_POST['keterangan'] ?? '';

                $spNama = $db->query("SELECT nama FROM guru WHERE id = $supervisor_id")->fetchColumn() ?: 'Supervisor';
                $namaMapel = $mapel_id ? ($siakad_db->query("SELECT nama_mapel FROM mapel WHERE id = $mapel_id")->fetchColumn() ?: '') : '';
                $namaKelas = $kelas_id ? ($siakad_db->query("SELECT nama_kelas FROM kelas WHERE id = $kelas_id")->fetchColumn() ?: '') : '';

                $stmt = $db->prepare("UPDATE kurikulum_supervisi_kelas SET guru_id = ?, kelas_id = ?, mapel_id = ?, mata_pelajaran = ?, kelas = ?, supervisor_id = ?, penilai_nama = ?, tanggal = ?, tanggal_supervisi = ?, keterangan = ? WHERE id = ?");
                $stmt->execute([
                    $guru_id,
                    $kelas_id,
                    $mapel_id,
                    $namaMapel,
                    $namaKelas,
                    $supervisor_id,
                    $spNama,
                    $tanggal,
                    $tanggal,
                    $keterangan,
                    $id
                ]);
                header('Location: ' . base_url('kurikulum/supervisi/kelas'));
                exit;
            } catch (\Exception $e) {
                die("Error: " . $e->getMessage());
            }
        }
    }

    public function deleteSupervisiKelas() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            try {
                $db = \App\Core\Database::connect('core');
                $stmt = $db->prepare("DELETE FROM kurikulum_supervisi_kelas WHERE id = ?");
                $stmt->execute([$id]);
                try {
                    $db->prepare("DELETE FROM kurikulum_supervisi_hasil WHERE tipe_supervisi = 'Kelas' AND supervisi_id = ?")->execute([$id]);
                } catch (\Throwable $e) {}
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['alert'] = ['type' => 'success', 'message' => 'Data supervisi kelas berhasil dihapus!'];
                header('Location: ' . base_url('kurikulum/supervisi/kelas'));
                exit;
            } catch (\Exception $e) {
                die("Error: " . $e->getMessage());
            }
        }
    }

    public function evaluasiSupervisiKelasForm() {
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: ' . base_url('kurikulum/supervisi/kelas')); exit; }
        
        $db = \App\Core\Database::connect('core');
        $supervisi = $db->query("
            SELECT sk.*, 
                   COALESCE(sk.tanggal, sk.tanggal_supervisi) as tanggal,
                   COALESCE(sk.nilai, sk.nilai_akhir, 0) as nilai,
                   COALESCE(g.nama, 'Guru') as nama_guru, 
                   COALESCE(g.nip, '-') as nip_guru, 
                   COALESCE(sp.nama, sk.penilai_nama, 'Supervisor') as nama_supervisor, 
                   COALESCE(sp.nip, '-') as nip_supervisor 
            FROM kurikulum_supervisi_kelas sk 
            LEFT JOIN guru g ON sk.guru_id = g.id 
            LEFT JOIN guru sp ON sk.supervisor_id = sp.id 
            WHERE sk.id = $id
        ")->fetch();
        
        $siakad_db = \App\Core\Database::connect('siakad');
        $supervisi['nama_mapel'] = !empty($supervisi['mata_pelajaran']) ? $supervisi['mata_pelajaran'] : ($siakad_db->query("SELECT nama_mapel FROM mapel WHERE id = " . ($supervisi['mapel_id'] ?? 0))->fetchColumn() ?: '-');
        $supervisi['nama_kelas'] = !empty($supervisi['kelas']) ? $supervisi['kelas'] : ($siakad_db->query("SELECT nama_kelas FROM kelas WHERE id = " . ($supervisi['kelas_id'] ?? 0))->fetchColumn() ?: '-');

        $instrumen = $db->query("SELECT * FROM kurikulum_instrumen_supervisi WHERE tipe_supervisi = 'Kelas' ORDER BY CAST(kode AS UNSIGNED) ASC, id ASC")->fetchAll();
        $hasil_raw = $db->query("SELECT instrumen_id, skor FROM kurikulum_supervisi_hasil WHERE tipe_supervisi = 'Kelas' AND supervisi_id = $id")->fetchAll(\PDO::FETCH_ASSOC);
        $hasil = [];
        foreach($hasil_raw as $r) { $hasil[$r['instrumen_id']] = $r['skor']; }

        $active_module = 'kurikulum';
        $active_page   = 'supervisi_kelas';
        $tipe = 'Kelas';
        require_once __DIR__ . '/../../resources/views/kurikulum/supervisi_evaluasi.php';
    }

    public function evaluasiSupervisiKelasMobile() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $guru_id = $_SESSION['guru_id'] ?? 0;
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: ' . base_url('kurikulum/supervisi/kelas/mobile')); exit; }
        
        $db = \App\Core\Database::connect('core');
        $supervisi = $db->query("
            SELECT sk.*, 
                   COALESCE(sk.tanggal, sk.tanggal_supervisi) as tanggal,
                   COALESCE(sk.nilai, sk.nilai_akhir, 0) as nilai,
                   COALESCE(g.nama, 'Guru') as nama_guru, 
                   COALESCE(sp.nama, sk.penilai_nama, 'Supervisor') as nama_supervisor 
            FROM kurikulum_supervisi_kelas sk 
            LEFT JOIN guru g ON sk.guru_id = g.id 
            LEFT JOIN guru sp ON sk.supervisor_id = sp.id 
            WHERE sk.id = $id AND sk.supervisor_id = $guru_id
        ")->fetch();
        
        if (!$supervisi || $supervisi['status'] === 'Dievaluasi') { 
            header('Location: ' . base_url('kurikulum/supervisi/kelas/mobile')); 
            exit; 
        }
        
        $siakad_db = \App\Core\Database::connect('siakad');
        $supervisi['nama_mapel'] = !empty($supervisi['mata_pelajaran']) ? $supervisi['mata_pelajaran'] : ($siakad_db->query("SELECT nama_mapel FROM mapel WHERE id = " . ($supervisi['mapel_id'] ?? 0))->fetchColumn() ?: '-');
        $supervisi['nama_kelas'] = !empty($supervisi['kelas']) ? $supervisi['kelas'] : ($siakad_db->query("SELECT nama_kelas FROM kelas WHERE id = " . ($supervisi['kelas_id'] ?? 0))->fetchColumn() ?: '-');

        $instrumen = $db->query("SELECT * FROM kurikulum_instrumen_supervisi WHERE tipe_supervisi = 'Kelas' ORDER BY id ASC")->fetchAll();
        $hasil_raw = $db->query("SELECT instrumen_id, skor FROM kurikulum_supervisi_hasil WHERE tipe_supervisi = 'Kelas' AND supervisi_id = $id")->fetchAll(\PDO::FETCH_ASSOC);
        $hasil = [];
        foreach($hasil_raw as $r) { $hasil[$r['instrumen_id']] = $r['skor']; }

        $tipe = 'Kelas';
        require_once __DIR__ . '/../../resources/views/kurikulum/supervisi_evaluasi_mobile.php';
    }

    public function storeEvaluasiSupervisiKelas() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $db = \App\Core\Database::connect('core');
            $db->beginTransaction();
            try {
                $db->query("DELETE FROM kurikulum_supervisi_hasil WHERE tipe_supervisi = 'Kelas' AND supervisi_id = $id");
                $stmt_hasil = $db->prepare("INSERT INTO kurikulum_supervisi_hasil (tipe_supervisi, supervisi_id, instrumen_id, skor) VALUES ('Kelas', ?, ?, ?)");
                
                $totalSkor = 0;
                $maxSkor = 0;
                
                $instrumen = $db->query("SELECT id, bobot_maksimal FROM kurikulum_instrumen_supervisi WHERE tipe_supervisi = 'Kelas'")->fetchAll();
                foreach($instrumen as $ins) {
                    $maxSkor += $ins['bobot_maksimal'];
                }

                if (isset($_POST['skor']) && is_array($_POST['skor'])) {
                    foreach ($_POST['skor'] as $ins_id => $skor) {
                        $stmt_hasil->execute([$id, $ins_id, $skor]);
                        $totalSkor += $skor;
                    }
                }
                
                $nilaiAkhir = ($maxSkor > 0) ? round(($totalSkor / $maxSkor) * 100) : 0;
                $catatan = $_POST['catatan_evaluasi'] ?? '';
                
                // Pastikan kolom tindak lanjut siap di tabel
                $stmt_col = $db->query("SHOW COLUMNS FROM kurikulum_supervisi_kelas LIKE 'tindak_lanjut'");
                if (!$stmt_col->fetch()) {
                    $db->exec("ALTER TABLE kurikulum_supervisi_kelas ADD COLUMN tindak_lanjut TEXT NULL");
                    $db->exec("ALTER TABLE kurikulum_supervisi_kelas ADD COLUMN tindak_lanjut_jenis VARCHAR(100) NULL");
                    $db->exec("ALTER TABLE kurikulum_supervisi_kelas ADD COLUMN tindak_lanjut_status VARCHAR(50) DEFAULT 'Belum Terlaksana'");
                    $db->exec("ALTER TABLE kurikulum_supervisi_kelas ADD COLUMN tindak_lanjut_deadline VARCHAR(50) NULL");
                    $db->exec("ALTER TABLE kurikulum_supervisi_kelas ADD COLUMN tindak_lanjut_realisasi TEXT NULL");
                }

                $tl_jenis     = trim($_POST['tindak_lanjut_jenis'] ?? '');
                $tl_deskripsi = trim($_POST['tindak_lanjut'] ?? '');
                $tl_deadline  = trim($_POST['tindak_lanjut_deadline'] ?? '');
                $tl_status    = trim($_POST['tindak_lanjut_status'] ?? 'Dalam Proses');
                $tl_realisasi = trim($_POST['tindak_lanjut_realisasi'] ?? '');

                if (!empty($tl_deskripsi) || !empty($tl_jenis)) {
                    $stmt_update = $db->prepare("UPDATE kurikulum_supervisi_kelas SET nilai = ?, nilai_akhir = ?, catatan_evaluasi = ?, catatan = ?, tindak_lanjut_jenis = ?, tindak_lanjut = ?, tindak_lanjut_deadline = ?, tindak_lanjut_status = ?, tindak_lanjut_realisasi = ?, status = 'Dievaluasi' WHERE id = ?");
                    $stmt_update->execute([$nilaiAkhir, $nilaiAkhir, $catatan, $catatan, $tl_jenis, $tl_deskripsi, $tl_deadline, $tl_status, $tl_realisasi, $id]);
                } else {
                    $stmt_update = $db->prepare("UPDATE kurikulum_supervisi_kelas SET nilai = ?, nilai_akhir = ?, catatan_evaluasi = ?, catatan = ?, status = 'Dievaluasi' WHERE id = ?");
                    $stmt_update->execute([$nilaiAkhir, $nilaiAkhir, $catatan, $catatan, $id]);
                }
                
                $db->commit();
                
                if (isset($_POST['source']) && $_POST['source'] === 'mobile') {
                    header('Location: ' . base_url('kurikulum/supervisi/kelas/mobile'));
                } else {
                    header('Location: ' . base_url('kurikulum/supervisi/kelas'));
                }
                exit;
            } catch (\Exception $e) {
                $db->rollBack();
                die("Error: " . $e->getMessage());
            }
        }
    }

    public function cetakSupervisiKelas() {
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: ' . base_url('kurikulum/supervisi/kelas')); exit; }
        
        $db = \App\Core\Database::connect('core');
        $supervisi = $db->query("
            SELECT sk.*, 
                   COALESCE(sk.tanggal, sk.tanggal_supervisi) as tanggal,
                   COALESCE(sk.nilai, sk.nilai_akhir, 0) as nilai,
                   COALESCE(g.nama, 'Guru') as nama_guru, 
                   COALESCE(g.nip, '-') as nip_guru, 
                   COALESCE(sp.nama, sk.penilai_nama, 'Supervisor') as nama_supervisor, 
                   COALESCE(sp.nip, '-') as nip_supervisor 
            FROM kurikulum_supervisi_kelas sk 
            LEFT JOIN guru g ON sk.guru_id = g.id 
            LEFT JOIN guru sp ON sk.supervisor_id = sp.id 
            WHERE sk.id = $id
        ")->fetch();
        
        $siakad_db = \App\Core\Database::connect('siakad');
        $supervisi['nama_mapel'] = !empty($supervisi['mata_pelajaran']) ? $supervisi['mata_pelajaran'] : ($siakad_db->query("SELECT nama_mapel FROM mapel WHERE id = " . ($supervisi['mapel_id'] ?? 0))->fetchColumn() ?: '-');
        $supervisi['nama_kelas'] = !empty($supervisi['kelas']) ? $supervisi['kelas'] : ($siakad_db->query("SELECT nama_kelas FROM kelas WHERE id = " . ($supervisi['kelas_id'] ?? 0))->fetchColumn() ?: '-');

        $instrumen = $db->query("SELECT * FROM kurikulum_instrumen_supervisi WHERE tipe_supervisi = 'Kelas' ORDER BY CAST(kode AS UNSIGNED) ASC, id ASC")->fetchAll();
        $hasil_raw = $db->query("SELECT instrumen_id, skor FROM kurikulum_supervisi_hasil WHERE tipe_supervisi = 'Kelas' AND supervisi_id = $id")->fetchAll(\PDO::FETCH_ASSOC);
        $hasil = [];
        foreach($hasil_raw as $r) { $hasil[$r['instrumen_id']] = $r['skor']; }
        
        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();

        $tipe = 'Kelas';
        require_once __DIR__ . '/../../resources/views/kurikulum/supervisi_cetak.php';
    }

    public function cetakSemuaSupervisiKelas() {
        $db = \App\Core\Database::connect('core');
        $siakad_db = \App\Core\Database::connect('siakad');

        $mapels = [];
        try {
            $mapels = $siakad_db->query("SELECT id, nama_mapel FROM mapel")->fetchAll(\PDO::FETCH_KEY_PAIR) ?: [];
        } catch (\Throwable $e) {}

        $kelasList = [];
        try {
            $kelasList = $siakad_db->query("SELECT id, nama_kelas FROM kelas")->fetchAll(\PDO::FETCH_KEY_PAIR) ?: [];
        } catch (\Throwable $e) {}

        $daftar_supervisi = $db->query("
            SELECT sk.*, 
                   COALESCE(sk.tanggal, sk.tanggal_supervisi) as tanggal,
                   COALESCE(sk.nilai, sk.nilai_akhir, 0) as nilai,
                   COALESCE(g.nama, 'Guru') as nama_guru, 
                   COALESCE(g.nip, '-') as nip_guru, 
                   COALESCE(sp.nama, sk.penilai_nama, 'Supervisor') as nama_supervisor, 
                   COALESCE(sp.nip, '-') as nip_supervisor 
            FROM kurikulum_supervisi_kelas sk 
            JOIN guru g ON sk.guru_id = g.id 
            LEFT JOIN guru sp ON sk.supervisor_id = sp.id 
            ORDER BY COALESCE(sk.tanggal, sk.tanggal_supervisi) DESC, sk.id DESC
        ")->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($daftar_supervisi as &$s) {
            $s['nama_mapel'] = !empty($s['mata_pelajaran']) ? $s['mata_pelajaran'] : ($mapels[$s['mapel_id'] ?? 0] ?? '-');
            $s['nama_kelas'] = !empty($s['kelas']) ? $s['kelas'] : ($kelasList[$s['kelas_id'] ?? 0] ?? '-');
        }
        unset($s);

        $instrumen = $db->query("SELECT * FROM kurikulum_instrumen_supervisi WHERE tipe_supervisi = 'Kelas' ORDER BY CAST(kode AS UNSIGNED) ASC, id ASC")->fetchAll(\PDO::FETCH_ASSOC);
        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(\PDO::FETCH_ASSOC) ?: [];
        
        $all_hasil_raw = $db->query("SELECT supervisi_id, instrumen_id, skor FROM kurikulum_supervisi_hasil WHERE tipe_supervisi = 'Kelas'")->fetchAll(\PDO::FETCH_ASSOC);
        $all_hasil = [];
        foreach ($all_hasil_raw as $hr) {
            $all_hasil[$hr['supervisi_id']][$hr['instrumen_id']] = $hr['skor'];
        }

        $legalitas = self::getSupervisiLegalitas($db);
        extract($legalitas);

        $tipe = 'Kelas';
        require_once __DIR__ . '/../../resources/views/kurikulum/supervisi_cetak_semua.php';
    }

    public function cetakJadwalSupervisiKelas() {
        $db = \App\Core\Database::connect('core');
        $siakad_db = \App\Core\Database::connect('siakad');

        $mapels = [];
        try {
            $mapels = $siakad_db->query("SELECT id, nama_mapel FROM mapel")->fetchAll(\PDO::FETCH_KEY_PAIR) ?: [];
        } catch (\Throwable $e) {}

        $kelasList = [];
        try {
            $kelasList = $siakad_db->query("SELECT id, nama_kelas FROM kelas")->fetchAll(\PDO::FETCH_KEY_PAIR) ?: [];
        } catch (\Throwable $e) {}

        $daftar_supervisi = $db->query("
            SELECT sk.*, 
                   COALESCE(sk.tanggal, sk.tanggal_supervisi) as tanggal,
                   g.nama as nama_guru, 
                   g.nip as nip_guru, 
                   COALESCE(sp.nama, sk.penilai_nama, 'Supervisor') as nama_supervisor, 
                   COALESCE(sp.nip, '-') as nip_supervisor 
            FROM kurikulum_supervisi_kelas sk 
            JOIN guru g ON sk.guru_id = g.id 
            LEFT JOIN guru sp ON sk.supervisor_id = sp.id 
            ORDER BY COALESCE(sk.tanggal, sk.tanggal_supervisi) ASC, sk.id ASC
        ")->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($daftar_supervisi as &$s) {
            $s['nama_mapel'] = !empty($s['mata_pelajaran']) ? $s['mata_pelajaran'] : ($mapels[$s['mapel_id'] ?? 0] ?? '-');
            $s['nama_kelas'] = !empty($s['kelas']) ? $s['kelas'] : ($kelasList[$s['kelas_id'] ?? 0] ?? '-');
        }
        unset($s);

        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(\PDO::FETCH_ASSOC) ?: [];
        $legalitas = self::getSupervisiLegalitas($db);
        extract($legalitas);

        // Ambil info tahun ajaran & semester aktif atau default
        $ta_aktif = '2026/2027';
        try {
            $ta_aktif_db = $db->query("SELECT name FROM tahun_ajaran WHERE is_active = 1 LIMIT 1")->fetchColumn();
            if ($ta_aktif_db) $ta_aktif = $ta_aktif_db;
        } catch (\Throwable $e) {}
        
        $tahun_ajaran = $ta_aktif;
        $semester = 'Ganjil';

        require_once __DIR__ . '/../../resources/views/kurikulum/supervisi_jadwal_cetak.php';
    }
    
    public function getGuruKelas() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['guru_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request']);
            return;
        }
        $guru_id = (int)$_GET['guru_id'];
        try {
            $db = \App\Core\Database::connect('siakad');
            $core_db = \App\Core\Database::connect('core');
            $ta = $core_db->query("SELECT id FROM tahun_ajaran WHERE is_active = 1 LIMIT 1")->fetch();
            $ta_id = $ta ? $ta['id'] : 1;

            $stmt = $db->prepare("SELECT DISTINCT k.id, k.nama_kelas 
                                  FROM jadwal_pelajaran j 
                                  JOIN kelas k ON j.kelas_id = k.id 
                                  WHERE j.guru_id = ? AND j.tahun_ajaran_id = ?");
            $stmt->execute([$guru_id, $ta_id]);
            $kelas = $stmt->fetchAll();
            if (empty($kelas)) {
                $kelas = $db->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat ASC, id ASC")->fetchAll(\PDO::FETCH_ASSOC);
            }
            header('Content-Type: application/json');
            echo json_encode(['kelas' => $kelas]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getGuruKelasMapel() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['guru_id']) || !isset($_GET['kelas_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request']);
            return;
        }
        $guru_id = (int)$_GET['guru_id'];
        $kelas_id = (int)$_GET['kelas_id'];
        try {
            $db = \App\Core\Database::connect('siakad');
            $core_db = \App\Core\Database::connect('core');
            $ta = $core_db->query("SELECT id FROM tahun_ajaran WHERE is_active = 1 LIMIT 1")->fetch();
            $ta_id = $ta ? $ta['id'] : 1;

            $stmt = $db->prepare("SELECT DISTINCT m.id, m.nama_mapel 
                                  FROM jadwal_pelajaran j 
                                  JOIN mapel m ON j.mata_pelajaran_id = m.id 
                                  WHERE j.guru_id = ? AND j.kelas_id = ? AND j.tahun_ajaran_id = ?");
            $stmt->execute([$guru_id, $kelas_id, $ta_id]);
            $mapel = $stmt->fetchAll();
            if (empty($mapel)) {
                $mapel = $db->query("SELECT id, nama_mapel FROM mapel ORDER BY nama_mapel ASC")->fetchAll(\PDO::FETCH_ASSOC);
            }
            header('Content-Type: application/json');
            echo json_encode(['mapel' => $mapel]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function supervisiInstrumen() {
        $active_module = 'kurikulum';
        $active_page   = 'supervisi_instrumen';
        
        $instrumen = [];
        try {
            $db = \App\Core\Database::connect('core');
            $stmt = $db->query("SELECT * FROM kurikulum_instrumen_supervisi ORDER BY tipe_supervisi ASC, CAST(kode AS UNSIGNED) ASC, id ASC");
            $instrumen = $stmt->fetchAll();
        } catch (\Exception $e) {
            // Ignore if table doesn't exist yet
        }
        
        require_once __DIR__ . '/../../resources/views/kurikulum/supervisi_instrumen.php';
    }

    public function storeSupervisiInstrumen() {
        $db = \App\Core\Database::connect('core');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipe_supervisi = trim($_POST['tipe_supervisi'] ?? 'Kelas');
            $kategori = trim($_POST['kategori'] ?? 'Lain-lain');
            $kode = trim($_POST['kode'] ?? '');
            $aspek = trim($_POST['aspek'] ?? '');
            $bobot = (int)($_POST['bobot_maksimal'] ?? $_POST['bobot'] ?? 5);

            try {
                $stmt = $db->prepare("INSERT INTO kurikulum_instrumen_supervisi (tipe_supervisi, kategori, kode, aspek, bobot_maksimal) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$tipe_supervisi, $kategori, $kode, $aspek, $bobot]);
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['alert'] = ['type' => 'success', 'message' => 'Instrumen berhasil ditambahkan!'];
                $_SESSION['sweetalert'] = ['title' => 'Sukses!', 'text' => 'Instrumen berhasil ditambahkan', 'icon' => 'success'];
            } catch (\Exception $e) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['alert'] = ['type' => 'error', 'message' => 'Gagal menambahkan instrumen: ' . $e->getMessage()];
                $_SESSION['sweetalert'] = ['title' => 'Gagal!', 'text' => 'Gagal menambahkan instrumen', 'icon' => 'error'];
            }
            header('Location: ' . base_url('kurikulum/supervisi/instrumen'));
            exit;
        }
    }

    public function updateSupervisiInstrumen() {
        $db = \App\Core\Database::connect('core');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $tipe_supervisi = trim($_POST['tipe_supervisi'] ?? 'Kelas');
            $kategori = trim($_POST['kategori'] ?? 'Lain-lain');
            $kode = trim($_POST['kode'] ?? '');
            $aspek = trim($_POST['aspek'] ?? '');
            $bobot = (int)($_POST['bobot_maksimal'] ?? $_POST['bobot'] ?? 5);

            if ($id > 0 && !empty($aspek)) {
                try {
                    $stmt = $db->prepare("UPDATE kurikulum_instrumen_supervisi SET tipe_supervisi = ?, kategori = ?, kode = ?, aspek = ?, bobot_maksimal = ? WHERE id = ?");
                    $stmt->execute([$tipe_supervisi, $kategori, $kode, $aspek, $bobot, $id]);
                    if (session_status() === PHP_SESSION_NONE) session_start();
                    $_SESSION['alert'] = ['type' => 'success', 'message' => 'Instrumen berhasil diperbarui! Nilai hasil evaluasi tetap aman tersimpan.'];
                    $_SESSION['sweetalert'] = ['title' => 'Sukses!', 'text' => 'Instrumen berhasil diperbarui! Nilai hasil evaluasi tetap aman tersimpan.', 'icon' => 'success'];
                } catch (\Exception $e) {
                    if (session_status() === PHP_SESSION_NONE) session_start();
                    $_SESSION['alert'] = ['type' => 'error', 'message' => 'Gagal memperbarui instrumen: ' . $e->getMessage()];
                    $_SESSION['sweetalert'] = ['title' => 'Gagal!', 'text' => 'Gagal memperbarui instrumen', 'icon' => 'error'];
                }
            }
            header('Location: ' . base_url('kurikulum/supervisi/instrumen'));
            exit;
        }
    }

    public function deleteSupervisiInstrumen() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id > 0) {
                try {
                    $db = \App\Core\Database::connect('core');
                    // Cek apakah sudah ada hasil nilai yang terikat dengan instrumen ini
                    $usedCount = (int)$db->query("SELECT COUNT(*) FROM kurikulum_supervisi_hasil WHERE instrumen_id = " . $id)->fetchColumn();
                    if ($usedCount > 0) {
                        if (session_status() === PHP_SESSION_NONE) session_start();
                        $_SESSION['alert'] = ['type' => 'warning', 'message' => "Instrumen ID $id tidak dapat dihapus karena sudah memiliki $usedCount data nilai evaluasi guru! Gunakan fitur EDIT agar nilai guru tidak hilang."];
                        $_SESSION['sweetalert'] = ['title' => 'Peringatan!', 'text' => "Instrumen ini sudah memiliki $usedCount data penilaian guru! Silakan gunakan fitur Edit untuk mengubah teks tanpa menghapus nilai.", 'icon' => 'warning'];
                    } else {
                        $stmt = $db->prepare("DELETE FROM kurikulum_instrumen_supervisi WHERE id = ?");
                        $stmt->execute([$id]);
                        if (session_status() === PHP_SESSION_NONE) session_start();
                        $_SESSION['alert'] = ['type' => 'success', 'message' => 'Instrumen berhasil dihapus!'];
                        $_SESSION['sweetalert'] = ['title' => 'Sukses!', 'text' => 'Instrumen berhasil dihapus!', 'icon' => 'success'];
                    }
                } catch (\Exception $e) {
                    if (session_status() === PHP_SESSION_NONE) session_start();
                    $_SESSION['alert'] = ['type' => 'error', 'message' => 'Gagal menghapus instrumen: ' . $e->getMessage()];
                }
            }
        }
        header('Location: ' . base_url('kurikulum/supervisi/instrumen'));
        exit;
    }

    public function supervisiTindakLanjut() {
        $active_module = 'kurikulum';
        $active_page   = 'supervisi_tindak_lanjut';
        
        $db = \App\Core\Database::connect('core');
        
        // Auto migration kolom tindak lanjut jika belum ada
        try {
            $stmt = $db->query("SHOW COLUMNS FROM kurikulum_supervisi_kelas LIKE 'tindak_lanjut'");
            if ($stmt->rowCount() == 0) {
                $db->exec("ALTER TABLE kurikulum_supervisi_kelas ADD COLUMN tindak_lanjut TEXT NULL");
                $db->exec("ALTER TABLE kurikulum_supervisi_kelas ADD COLUMN tindak_lanjut_jenis VARCHAR(100) NULL");
                $db->exec("ALTER TABLE kurikulum_supervisi_kelas ADD COLUMN tindak_lanjut_status VARCHAR(50) DEFAULT 'Belum Terlaksana'");
                $db->exec("ALTER TABLE kurikulum_supervisi_kelas ADD COLUMN tindak_lanjut_deadline VARCHAR(50) NULL");
                $db->exec("ALTER TABLE kurikulum_supervisi_kelas ADD COLUMN tindak_lanjut_realisasi TEXT NULL");
            }
        } catch (\Exception $e) {}

        // Ambil data guru yang disupervisi
        $daftar_supervisi = [];
        try {
            $stmt = $db->query("
                SELECT sk.*, 
                       COALESCE(sk.tanggal, sk.tanggal_supervisi) as tanggal,
                       COALESCE(sk.nilai, sk.nilai_akhir, 0) as nilai,
                       COALESCE(g.nama, 'Guru') as nama_guru, 
                       COALESCE(g.nip, '-') as guru_nip, 
                       COALESCE(sp.nama, sk.penilai_nama, 'Supervisor') as nama_supervisor
                FROM kurikulum_supervisi_kelas sk
                JOIN guru g ON sk.guru_id = g.id
                LEFT JOIN guru sp ON sk.supervisor_id = sp.id
                ORDER BY COALESCE(sk.tanggal, sk.tanggal_supervisi) DESC, sk.id DESC
            ");
            $daftar_supervisi = $stmt ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];

            $siakad_db = \App\Core\Database::connect('siakad');
            $mapels = [];
            $kelasList = [];
            try {
                $mapels = $siakad_db->query("SELECT id, nama_mapel FROM mapel")->fetchAll(\PDO::FETCH_KEY_PAIR) ?: [];
                $kelasList = $siakad_db->query("SELECT id, nama_kelas FROM kelas")->fetchAll(\PDO::FETCH_KEY_PAIR) ?: [];
            } catch (\Throwable $e) {}

            foreach($daftar_supervisi as &$s) {
                $s['nama_mapel'] = !empty($s['mata_pelajaran']) ? $s['mata_pelajaran'] : ($mapels[$s['mapel_id'] ?? 0] ?? 'Mapel ID: ' . ($s['mapel_id'] ?? 0));
                $s['nama_kelas'] = !empty($s['kelas']) ? $s['kelas'] : ($kelasList[$s['kelas_id'] ?? 0] ?? 'Kelas ID: ' . ($s['kelas_id'] ?? 0));
            }
            unset($s);
        } catch (\Exception $e) {}

        require_once __DIR__ . '/../../resources/views/kurikulum/supervisi_tindak_lanjut.php';
    }

    public function updateSupervisiTindakLanjut() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $jenis = trim($_POST['tindak_lanjut_jenis'] ?? '');
            $deskripsi = trim($_POST['tindak_lanjut'] ?? '');
            $deadline = trim($_POST['tindak_lanjut_deadline'] ?? '');
            $status = trim($_POST['tindak_lanjut_status'] ?? 'Belum Terlaksana');
            $realisasi = trim($_POST['tindak_lanjut_realisasi'] ?? '');

            if ($id > 0) {
                try {
                    $db = \App\Core\Database::connect('core');
                    $stmt = $db->prepare("
                        UPDATE kurikulum_supervisi_kelas 
                        SET tindak_lanjut_jenis = ?, tindak_lanjut = ?, tindak_lanjut_deadline = ?, tindak_lanjut_status = ?, tindak_lanjut_realisasi = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$jenis, $deskripsi, $deadline, $status, $realisasi, $id]);
                    $_SESSION['alert'] = ['type' => 'success', 'message' => 'Rencana tindak lanjut berhasil diperbarui!'];
                } catch (\Exception $e) {
                    $_SESSION['alert'] = ['type' => 'error', 'message' => 'Gagal memperbarui: ' . $e->getMessage()];
                }
            }
            header('Location: ' . base_url('kurikulum/supervisi/tindak-lanjut'));
            exit;
        }
    }

    public function cetakRtlSupervisi() {
        $db = \App\Core\Database::connect('core');
        $legalitas = self::getSupervisiLegalitas($db);
        extract($legalitas);
        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(\PDO::FETCH_ASSOC) ?: [];

        $daftar_supervisi = $db->query("
            SELECT sk.*, 
                   COALESCE(sk.tanggal, sk.tanggal_supervisi) as tanggal,
                   COALESCE(sk.nilai, sk.nilai_akhir, 0) as nilai,
                   COALESCE(g.nama, 'Guru') as nama_guru, 
                   COALESCE(g.nip, '-') as guru_nip, 
                   COALESCE(sp.nama, sk.penilai_nama, 'Supervisor') as nama_supervisor
            FROM kurikulum_supervisi_kelas sk
            JOIN guru g ON sk.guru_id = g.id
            LEFT JOIN guru sp ON sk.supervisor_id = sp.id
            ORDER BY COALESCE(sk.tanggal, sk.tanggal_supervisi) DESC, sk.id DESC
        ")->fetchAll(\PDO::FETCH_ASSOC);

        try {
            $siakad_db = \App\Core\Database::connect('siakad');
            $mapels = $siakad_db->query("SELECT id, nama_mapel FROM mapel")->fetchAll(\PDO::FETCH_KEY_PAIR) ?: [];
            $kelasList = $siakad_db->query("SELECT id, nama_kelas FROM kelas")->fetchAll(\PDO::FETCH_KEY_PAIR) ?: [];

            foreach($daftar_supervisi as &$s) {
                $s['nama_mapel'] = !empty($s['mata_pelajaran']) ? $s['mata_pelajaran'] : ($mapels[$s['mapel_id'] ?? 0] ?? '-');
                $s['nama_kelas'] = !empty($s['kelas']) ? $s['kelas'] : ($kelasList[$s['kelas_id'] ?? 0] ?? '-');
            }
            unset($s);
        } catch (\Exception $e) {}

        require_once __DIR__ . '/../../resources/views/kurikulum/supervisi_tindak_lanjut_cetak.php';
    }

    public static function getSupervisiReportDataset() {
        $db = \App\Core\Database::connect('core');
        $legalitas = self::getSupervisiLegalitas($db);
        extract($legalitas);
        $inst = $institusi;

        // Data supervisi real dari database
        $supervisiData = [];
        try {
            $stmt = $db->query("
                SELECT sk.*, 
                       COALESCE(sk.tanggal, sk.tanggal_supervisi) as tanggal,
                       COALESCE(sk.nilai, sk.nilai_akhir, 0) as nilai,
                       IF((sk.nilai IS NOT NULL AND sk.nilai > 0) OR (sk.nilai_akhir IS NOT NULL AND sk.nilai_akhir > 0), 'Dievaluasi', COALESCE(sk.status, 'Jadwal')) as status,
                       COALESCE(g.nama, 'Guru') as nama_guru, 
                       COALESCE(g.nip, '-') as guru_nip, 
                       COALESCE(sp.nama, sk.penilai_nama, 'Supervisor') as nama_supervisor
                FROM kurikulum_supervisi_kelas sk
                JOIN guru g ON sk.guru_id = g.id
                LEFT JOIN guru sp ON sk.supervisor_id = sp.id
                ORDER BY COALESCE(sk.tanggal, sk.tanggal_supervisi) DESC, sk.id DESC
            ");
            $supervisiData = $stmt ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];

            $siakad_db = \App\Core\Database::connect('siakad');
            $mapels = [];
            $kelasList = [];
            try {
                $mapels = $siakad_db->query("SELECT id, nama_mapel FROM mapel")->fetchAll(\PDO::FETCH_KEY_PAIR) ?: [];
                $kelasList = $siakad_db->query("SELECT id, nama_kelas FROM kelas")->fetchAll(\PDO::FETCH_KEY_PAIR) ?: [];
            } catch (\Throwable $e) {}

            foreach ($supervisiData as &$s) {
                $s['nama_mapel'] = !empty($s['mata_pelajaran']) ? $s['mata_pelajaran'] : ($mapels[$s['mapel_id'] ?? 0] ?? 'Guru Kelas');
                $s['nama_kelas'] = !empty($s['kelas']) ? $s['kelas'] : ($kelasList[$s['kelas_id'] ?? 0] ?? '-');
            }
            unset($s);
        } catch (\Exception $e) {}

        $matriksSupervisi = [];
        $usedGuruNames = [];

        if (!empty($supervisiData)) {
            foreach ($supervisiData as $sd) {
                $gNama = trim($sd['nama_guru'] ?? '');
                if (empty($gNama) || in_array($gNama, $usedGuruNames)) continue;
                $usedGuruNames[] = $gNama;
                
                $skorVal = !empty($sd['nilai']) ? (float)$sd['nilai'] : 0;
                $mapelInfo = !empty($sd['nama_mapel']) ? $sd['nama_mapel'] : 'Guru Kelas';
                if (!empty($sd['nama_kelas']) && $sd['nama_kelas'] !== '-') {
                    $mapelInfo .= ' (Kls ' . $sd['nama_kelas'] . ')';
                }

                $tl = trim($sd['tindak_lanjut'] ?? '');
                $tl_jenis = trim($sd['tindak_lanjut_jenis'] ?? '');
                if (empty($tl)) {
                    if ($skorVal < 75) {
                        $tl_jenis = 'Bimbingan Klinis';
                        $tl = 'Bimbingan Klinis Pengelolaan Kelas & Pelatihan Modul Ajar di MOOC PINTAR Kemenag';
                    } elseif ($skorVal < 85) {
                        $tl_jenis = 'Bimbingan Klinis';
                        $tl = 'Optimalisasi Alat Peraga Interaktif & Refleksi Pembelajaran Berkelanjutan';
                    } else {
                        $tl_jenis = 'Kombel KKG';
                        $tl = 'Penguatan Pembelajaran Berdiferensiasi & Berbagi Praktik Baik di Komunitas Belajar (KKG)';
                    }
                }

                // Ambil data skor per aspek dari tabel hasil jika tersedia
                $hasilAspek = [];
                try {
                    $stmtH = $db->prepare("SELECT instrumen_id, skor FROM kurikulum_supervisi_hasil WHERE supervisi_id = ? AND tipe_supervisi = 'Kelas'");
                    $stmtH->execute([(int)$sd['id']]);
                    $hasilAspek = $stmtH->fetchAll(\PDO::FETCH_KEY_PAIR);
                } catch (\Throwable $e) {}

                // Hitung skor per kategori dari instrumen
                $skorPerKategori = ['persiapan' => [], 'pendahuluan' => [], 'inti' => [], 'penutup' => []];
                if (!empty($hasilAspek)) {
                    try {
                        $instrumenList = $db->query("SELECT id, kode FROM kurikulum_instrumen_supervisi WHERE tipe_supervisi = 'Kelas' ORDER BY CAST(kode AS UNSIGNED) ASC, id ASC")->fetchAll(\PDO::FETCH_ASSOC);
                        foreach ($instrumenList as $instr) {
                            $kode = strtoupper($instr['kode'] ?? '');
                            $sid = $instr['id'];
                            $val = isset($hasilAspek[$sid]) ? (float)$hasilAspek[$sid] : 0;
                            if (strpos($kode, 'A') === 0 || strpos($kode, 'I.') === 0) {
                                $skorPerKategori['persiapan'][] = $val;
                            } elseif (strpos($kode, 'B') === 0 || strpos($kode, 'II.') === 0) {
                                $skorPerKategori['pendahuluan'][] = $val;
                            } elseif (strpos($kode, 'C') === 0 || strpos($kode, 'III.') === 0) {
                                $skorPerKategori['inti'][] = $val;
                            } elseif (strpos($kode, 'D') === 0 || strpos($kode, 'IV.') === 0) {
                                $skorPerKategori['penutup'][] = $val;
                            }
                        }
                    } catch (\Throwable $e) {}
                }

                $avgFunc = function($arr) { return count($arr) > 0 ? round(array_sum($arr) / count($arr) * 20, 1) : 0; };
                $pPersiapan = !empty($skorPerKategori['persiapan']) ? $avgFunc($skorPerKategori['persiapan']) : $skorVal;
                $pPendahuluan = !empty($skorPerKategori['pendahuluan']) ? $avgFunc($skorPerKategori['pendahuluan']) : $skorVal;
                $pInti = !empty($skorPerKategori['inti']) ? $avgFunc($skorPerKategori['inti']) : $skorVal;
                $pPenutup = !empty($skorPerKategori['penutup']) ? $avgFunc($skorPerKategori['penutup']) : $skorVal;

                // Catatan kekuatan & kelemahan otomatis berdasarkan skor
                $kekuatan = !empty($sd['catatan_evaluasi']) ? $sd['catatan_evaluasi'] : 'Penguasaan materi pembelajaran sangat runtut dan interaktif, suara tertib.';
                $kelemahan = 'Perlu variasi instrumen asesmen formatif saat pembelajaran berlangsung.';

                $matriksSupervisi[] = [
                    'id' => (int)($sd['id'] ?? 0),
                    'nama' => $gNama,
                    'nip' => $sd['guru_nip'] ?: '-',
                    'mapel' => $mapelInfo,
                    'supervisor' => $sd['nama_supervisor'] ?? $kamad['nama'],
                    'tanggal' => $sd['tanggal'] ?? date('Y-m-d'),
                    'persiapan' => $pPersiapan,
                    'pendahuluan' => $pPendahuluan,
                    'inti' => $pInti,
                    'penutup' => $pPenutup,
                    'nilai_asli' => $skorVal,
                    'rata' => $skorVal,
                    'predikat' => $skorVal >= 90 ? 'Amat Baik (A)' : ($skorVal >= 80 ? 'Baik (B)' : ($skorVal >= 70 ? 'Cukup (C)' : 'Kurang (D)')),
                    'kekuatan' => $kekuatan,
                    'kelemahan' => $kelemahan,
                    'catatan' => !empty($sd['catatan_evaluasi']) ? $sd['catatan_evaluasi'] : 'Penguasaan materi pembelajaran sangat runtut dan interaktif, suara tertib.',
                    'tindak_lanjut_jenis' => $tl_jenis,
                    'tindak_lanjut' => $tl,
                    'tindak_lanjut_deadline' => $sd['tindak_lanjut_deadline'] ?? 'Semester Ganjil 2026/2027',
                    'tindak_lanjut_status' => $sd['tindak_lanjut_status'] ?? 'Belum Terlaksana',
                    'tindak_lanjut_realisasi' => $sd['tindak_lanjut_realisasi'] ?? ''
                ];
            }
        }

        $totalSkor = 0; $sumPersiapan = 0; $sumPendahuluan = 0; $sumInti = 0; $sumPenutup = 0;
        $countAmatBaik = 0; $countBaik = 0;

        foreach ($matriksSupervisi as &$g) {
            $rata = round($g['rata'], 1);
            $totalSkor += $rata;
            $sumPersiapan += $g['persiapan'];
            $sumPendahuluan += $g['pendahuluan'];
            $sumInti += $g['inti'];
            $sumPenutup += $g['penutup'];
            if ($rata >= 90) $countAmatBaik++; else $countBaik++;
        }
        unset($g);

        $cnt = max(1, count($matriksSupervisi));
        $rataRataTotal = round($totalSkor / $cnt, 1);
        $avgPersiapan = round($sumPersiapan / $cnt, 1);
        $avgPendahuluan = round($sumPendahuluan / $cnt, 1);
        $avgInti = round($sumInti / $cnt, 1);
        $avgPenutup = round($sumPenutup / $cnt, 1);

        return [
            'institusi' => $inst,
            'kamad' => $kamad,
            'penanggung_jawab' => $penanggung_jawab,
            'ketua_panitia' => $ketua_panitia,
            'ketua_tim' => $ketua_tim,
            'sekretaris' => $sekretaris,
            'pengawas' => $pengawas,
            'sk_nomor' => $sk_nomor,
            'sk_tanggal' => $sk_tanggal,
            'config' => $config,
            'tim_supervisi' => $timSupervisi,
            'timSupervisi' => $timSupervisi,
            'matriksSupervisi' => $matriksSupervisi,
            'totalGuru' => $cnt,
            'rataRataTotal' => $rataRataTotal,
            'avgPersiapan' => $avgPersiapan,
            'avgPendahuluan' => $avgPendahuluan,
            'avgInti' => $avgInti,
            'avgPenutup' => $avgPenutup,
            'predikatRataRata' => $rataRataTotal >= 90 ? 'Amat Baik (A)' : ($rataRataTotal >= 80 ? 'Baik (B)' : 'Cukup (C)'),
            'countAmatBaik' => $countAmatBaik,
            'countBaik' => $countBaik,
            'persenTuntas' => 100,
            'tahun_ajaran' => '2026/2027',
            'semester' => 'Ganjil'
        ];
    }

    public function laporanHasilSupervisi() {
        $active_module = 'kurikulum';
        $active_page   = 'supervisi_laporan_hasil';
        $data = self::getSupervisiReportDataset();
        extract($data);
        $isPrintMode = false;
        require __DIR__ . '/../../resources/views/kurikulum/supervisi_laporan_hasil.php';
    }

    public function cetakLaporanHasilSupervisi() {
        $data = self::getSupervisiReportDataset();
        extract($data);
        $isPrintMode = true;
        require __DIR__ . '/../../resources/views/kurikulum/supervisi_laporan_hasil.php';
    }

    public function laporanTindakLanjutSupervisi() {
        $active_module = 'kurikulum';
        $active_page   = 'supervisi_laporan_tindak_lanjut';
        $data = self::getSupervisiReportDataset();
        extract($data);
        $isPrintMode = false;
        require __DIR__ . '/../../resources/views/kurikulum/supervisi_laporan_tindak_lanjut.php';
    }

    public function cetakLaporanTindakLanjutSupervisi() {
        $data = self::getSupervisiReportDataset();
        extract($data);
        $isPrintMode = true;
        require __DIR__ . '/../../resources/views/kurikulum/supervisi_laporan_tindak_lanjut.php';
    }

    public static function getSupervisiLegalitas($db = null) {
        if (!$db) $db = \App\Core\Database::connect('core');
        self::ensureTimSupervisiTables($db);

        $institusi = [];
        try {
            $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {}

        $defaultConfig = [
            'tahun_ajaran' => '2026/2027',
            'semester' => 'Ganjil',
            'nomor_sk' => 'MA.10.04/PP.01.1/045/SK-TIM-SPV/VII/2026',
            'tanggal_sk' => '2026-07-15',
            'kamad_nama' => 'Drs. Hamim Hasyim',
            'kamad_nip' => '-',
            'kamad_jabatan' => 'Kepala Madrasah (Penanggung Jawab)',
            'pengawas_nama' => 'Drs. H. M. YUSUF, M.Pd.I',
            'pengawas_nip' => '196805141994031002',
            'pengawas_jabatan' => 'Pengawas Madrasah Ahli Madya Kemenag Kab. Lampung Barat',
            'tanggal_laporan' => '2026-11-11'
        ];
        $config = $defaultConfig;
        try {
            $res = $db->query("SELECT * FROM kurikulum_supervisi_config ORDER BY id DESC LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
            if ($res) $config = array_merge($defaultConfig, array_filter($res));
        } catch (\Throwable $e) {}

        $kamad = [
            'nama' => !empty($config['kamad_nama']) ? $config['kamad_nama'] : 'Drs. Hamim Hasyim',
            'nip' => !empty($config['kamad_nip']) ? $config['kamad_nip'] : '-',
            'jabatan' => !empty($config['kamad_jabatan']) ? $config['kamad_jabatan'] : 'Kepala Madrasah (Penanggung Jawab)'
        ];

        $pengawas = [
            'nama' => !empty($config['pengawas_nama']) ? $config['pengawas_nama'] : 'Drs. H. M. YUSUF, M.Pd.I',
            'nip' => !empty($config['pengawas_nip']) ? $config['pengawas_nip'] : '196805141994031002',
            'jabatan' => !empty($config['pengawas_jabatan']) ? $config['pengawas_jabatan'] : 'Pengawas Madrasah Ahli Madya Kemenag Kab. Lampung Barat'
        ];

        $timSupervisi = [];
        try {
            $timSupervisi = $db->query("SELECT * FROM kurikulum_supervisi_tim ORDER BY urutan ASC, id ASC")->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {}

        // 4 STRUKTUR BAKU KEPANITIAAN SUPERVISI
        // 1. Penanggung Jawab (Kepala Madrasah)
        $penanggung_jawab = $kamad;

        // 2. Ketua Panitia
        $ketua_panitia = [
            'nama' => 'M AZIZ SULAIMAN, S.Pd',
            'nip' => '1804072704000002',
            'jabatan' => 'Waka Kurikulum',
            'peran' => 'Ketua Panitia'
        ];

        // 3. Sekretaris
        $sekretaris = [
            'nama' => 'INDAH YUNITASARI, S.Pd',
            'nip' => '',
            'jabatan' => 'Guru',
            'peran' => 'Sekretaris'
        ];

        // 4. Supervisor (Observer KBM)
        $supervisor_utama = [
            'nama' => 'SAIPUDIN AZIZ, S.Pd',
            'nip' => '',
            'jabatan' => 'Guru',
            'peran' => 'Supervisor'
        ];
        $supervisors_list = [];

        if (!empty($timSupervisi)) {
            $found_pj = false;
            $found_ketua = false;
            $found_sek = false;

            foreach ($timSupervisi as $ts) {
                $p = strtolower(trim($ts['peran_tim'] ?? ''));
                $j = strtolower(trim($ts['jabatan_madrasah'] ?? ''));
                $u = (int)($ts['urutan'] ?? 0);
                $nipClean = (!empty($ts['nip']) && $ts['nip'] !== '-') ? $ts['nip'] : '';

                // Penanggung Jawab
                if (!$found_pj && (strpos($p, 'penanggung jawab') !== false || strpos($p, 'penanggungjawab') !== false || $u === 1)) {
                    $penanggung_jawab = [
                        'nama' => $ts['nama'],
                        'nip' => $nipClean,
                        'jabatan' => !empty($ts['jabatan_madrasah']) ? $ts['jabatan_madrasah'] : 'Kepala Madrasah',
                        'peran' => !empty($ts['peran_tim']) ? $ts['peran_tim'] : 'Penanggung Jawab'
                    ];
                    $kamad = $penanggung_jawab;
                    $found_pj = true;
                    continue;
                }

                // Ketua Panitia / Ketua Tim Pelaksana
                if (!$found_ketua && (strpos($p, 'ketua') !== false || $u === 2)) {
                    $ketua_panitia = [
                        'nama' => $ts['nama'],
                        'nip' => $nipClean,
                        'jabatan' => !empty($ts['jabatan_madrasah']) ? $ts['jabatan_madrasah'] : 'Guru',
                        'peran' => !empty($ts['peran_tim']) ? $ts['peran_tim'] : 'Ketua Panitia'
                    ];
                    $found_ketua = true;
                    continue;
                }

                // Sekretaris
                if (!$found_sek && (strpos($p, 'sekretaris') !== false || $u === 3)) {
                    $sekretaris = [
                        'nama' => $ts['nama'],
                        'nip' => $nipClean,
                        'jabatan' => !empty($ts['jabatan_madrasah']) ? $ts['jabatan_madrasah'] : 'Guru',
                        'peran' => !empty($ts['peran_tim']) ? $ts['peran_tim'] : 'Sekretaris'
                    ];
                    $found_sek = true;
                    continue;
                }

                // Supervisor / Observer
                if (strpos($p, 'supervisor') !== false || strpos($p, 'observer') !== false || $u >= 4) {
                    $sup_item = [
                        'nama' => $ts['nama'],
                        'nip' => $nipClean,
                        'jabatan' => !empty($ts['jabatan_madrasah']) ? $ts['jabatan_madrasah'] : 'Guru',
                        'peran' => !empty($ts['peran_tim']) ? $ts['peran_tim'] : 'Supervisor'
                    ];
                    if (empty($supervisors_list)) {
                        $supervisor_utama = $sup_item;
                    }
                    $supervisors_list[] = $sup_item;
                }
            }
        }

        // Backwards compatibility
        $ketua_tim = $ketua_panitia;

        return [
            'institusi' => $institusi,
            'config' => $config,
            'kamad' => $kamad,
            'penanggung_jawab' => $penanggung_jawab,
            'pengawas' => $pengawas,
            'ketua_panitia' => $ketua_panitia,
            'ketua_tim' => $ketua_tim,
            'sekretaris' => $sekretaris,
            'supervisor' => $supervisor_utama,
            'supervisors' => $supervisors_list,
            'tim_supervisi' => $timSupervisi,
            'timSupervisi' => $timSupervisi,
            'sk_nomor' => $config['nomor_sk'] ?? 'MA.10.04/PP.01.1/045/SK-TIM-SPV/VII/2026',
            'sk_tanggal' => $config['tanggal_sk'] ?? '2026-07-15',
            'tahun_ajaran' => $config['tahun_ajaran'] ?? '2026/2027',
            'semester' => $config['semester'] ?? 'Ganjil'
        ];
    }

    public static function ensureSupervisiTables($db = null) {
        if (!$db) $db = \App\Core\Database::connect('core');
        try {
            $db->exec("CREATE TABLE IF NOT EXISTS `kurikulum_supervisi_config` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `tahun_ajaran` VARCHAR(20) NOT NULL DEFAULT '2026/2027',
                `semester` VARCHAR(20) NOT NULL DEFAULT 'Ganjil',
                `nomor_sk` VARCHAR(100) DEFAULT 'MA.10.04/PP.01.1/045/SK-TIM-SPV/VII/2026',
                `tanggal_sk` DATE DEFAULT '2026-07-15',
                `kamad_nama` VARCHAR(150) DEFAULT 'Drs. Hamim Hasyim',
                `kamad_nip` VARCHAR(50) DEFAULT '-',
                `kamad_jabatan` VARCHAR(100) DEFAULT 'Kepala Madrasah (Penanggung Jawab)',
                `pengawas_nama` VARCHAR(150) DEFAULT 'Drs. H. M. YUSUF, M.Pd.I',
                `pengawas_nip` VARCHAR(50) DEFAULT '196805141994031002',
                `pengawas_jabatan` VARCHAR(150) DEFAULT 'Pengawas Madrasah Ahli Madya Kemenag Kab. Lampung Barat',
                `tanggal_laporan` DATE NULL DEFAULT '2026-11-12',
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $cnt = (int)$db->query("SELECT COUNT(*) FROM `kurikulum_supervisi_config`")->fetchColumn();
            if ($cnt === 0) {
                $db->exec("INSERT INTO `kurikulum_supervisi_config` 
                    (`tahun_ajaran`, `semester`, `nomor_sk`, `tanggal_sk`, `kamad_nama`, `kamad_nip`, `kamad_jabatan`, `pengawas_nama`, `pengawas_nip`, `pengawas_jabatan`, `tanggal_laporan`) 
                    VALUES 
                    ('2026/2027', 'Ganjil', 'MA.10.04/PP.01.1/045/SK-TIM-SPV/VII/2026', '2026-07-15', 'Drs. Hamim Hasyim', '-', 'Kepala Madrasah (Penanggung Jawab)', 'Drs. H. M. YUSUF, M.Pd.I', '196805141994031002', 'Pengawas Madrasah Ahli Madya Kemenag Kab. Lampung Barat', '2026-11-12')");
            }

            try {
                $db->exec("ALTER TABLE `kurikulum_supervisi_config` ADD COLUMN `tanggal_laporan` DATE NULL DEFAULT '2026-11-12'");
            } catch (\Throwable $e) {}

            $db->exec("CREATE TABLE IF NOT EXISTS `kurikulum_supervisi_tim` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `tahun_ajaran` VARCHAR(20) NOT NULL DEFAULT '2026/2027',
                `semester` VARCHAR(20) NOT NULL DEFAULT 'Ganjil',
                `guru_id` INT NULL,
                `nama` VARCHAR(150) NOT NULL,
                `nip` VARCHAR(50) DEFAULT '-',
                `jabatan_madrasah` VARCHAR(100) DEFAULT 'Guru',
                `peran_tim` VARCHAR(100) NOT NULL DEFAULT 'Supervisor / Observer KBM',
                `tugas_fokus` VARCHAR(255) DEFAULT 'Observasi KBM & Perangkat Ajar',
                `urutan` INT DEFAULT 1,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $cntT = (int)$db->query("SELECT COUNT(*) FROM `kurikulum_supervisi_tim`")->fetchColumn();
            if ($cntT === 0) {
                $db->exec("INSERT INTO `kurikulum_supervisi_tim` 
                    (`tahun_ajaran`, `semester`, `guru_id`, `nama`, `nip`, `jabatan_madrasah`, `peran_tim`, `tugas_fokus`, `urutan`) 
                    VALUES 
                    ('2026/2027', 'Ganjil', 39, 'Drs. Hamim Hasyim', '-', 'Kepala Madrasah', 'Penanggung Jawab', 'Pengarah, Penanggung Jawab Mutu & Monev Keseluruhan KBM', 1),
                    ('2026/2027', 'Ganjil', 14, 'M AZIZ SULAIMAN, S.Pd', '1804072704000002', 'Waka Kurikulum / Guru Madya', 'Ketua Panitia', 'Koordinasi Jadwal, Verifikasi Administrasi Guru & Analisis Hasil', 2),
                    ('2026/2027', 'Ganjil', 41, 'INDAH YUNITASARI, S.Pd', '-', 'Guru / Bendahara', 'Sekretaris', 'Notulensi Coaching, Administrasi Rekapitulasi & Rapor Supervisi', 3),
                    ('2026/2027', 'Ganjil', 40, 'SAIPUDIN AZIZ, S.Pd', '-', 'Waka Kesiswaan / Guru', 'Supervisor', 'Observasi KBM Tatap Muka Kelas, Penilaian 25 Indikator KBM & Bimbingan Klinis', 4),
                    ('2026/2027', 'Ganjil', 43, 'SYAIFUL BAHRI, S.Pd', '-', 'Guru / Pembina IT', 'Supervisor', 'Observasi Pembelajaran Digital, Deep Learning & Media Interaktif', 5)");
            }

            // Pastikan baris tim dengan fokus tugas kosong terisi otomatis
            $db->exec("UPDATE `kurikulum_supervisi_tim` SET `tugas_fokus` = 'Koordinasi Jadwal Supervisi, Verifikasi Administrasi Guru & Analisis Hasil Observasi' WHERE (`tugas_fokus` IS NULL OR TRIM(`tugas_fokus`) = '') AND LOWER(`peran_tim`) LIKE '%ketua%'");
            $db->exec("UPDATE `kurikulum_supervisi_tim` SET `tugas_fokus` = 'Notulensi Coaching, Administrasi Instrumen & Rekapitulasi Laporan RTL' WHERE (`tugas_fokus` IS NULL OR TRIM(`tugas_fokus`) = '') AND LOWER(`peran_tim`) LIKE '%sekretaris%'");
            $db->exec("UPDATE `kurikulum_supervisi_tim` SET `tugas_fokus` = 'Observasi KBM Tatap Muka Kelas, Penilaian 25 Indikator KBM & Bimbingan Klinis' WHERE (`tugas_fokus` IS NULL OR TRIM(`tugas_fokus`) = '') AND (LOWER(`peran_tim`) LIKE '%supervisor%' OR LOWER(`peran_tim`) LIKE '%observer%')");
            $db->exec("UPDATE `kurikulum_supervisi_tim` SET `tugas_fokus` = 'Pengarah, Penanggung Jawab Mutu & Monev Keseluruhan KBM' WHERE (`tugas_fokus` IS NULL OR TRIM(`tugas_fokus`) = '') AND LOWER(`peran_tim`) LIKE '%penanggung%'");

            // Program supervisi table
            $db->exec("CREATE TABLE IF NOT EXISTS `kurikulum_supervisi_program` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `judul` varchar(255) NOT NULL,
                `tahun_ajaran_id` int(11) DEFAULT NULL,
                `semester` varchar(20) DEFAULT 'Ganjil',
                `file_path` varchar(255) DEFAULT NULL,
                `pendahuluan` text DEFAULT NULL,
                `tujuan` text DEFAULT NULL,
                `sasaran` text DEFAULT NULL,
                `ruang_lingkup` text DEFAULT NULL,
                `jadwal_pelaksanaan` text DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

            // Universal supervisi table
            $db->exec("CREATE TABLE IF NOT EXISTS `kurikulum_supervisi_universal` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `tipe_supervisi` VARCHAR(100) NOT NULL,
                `guru_id` INT NOT NULL,
                `kelas_id` INT NULL,
                `mapel_id` INT NULL,
                `supervisor_id` INT NOT NULL,
                `tanggal` DATE NOT NULL,
                `keterangan` TEXT,
                `status` VARCHAR(50) DEFAULT 'Jadwal',
                `is_released` TINYINT(1) DEFAULT 0,
                `is_hasil_released` TINYINT(1) DEFAULT 0,
                `nilai` FLOAT NULL,
                `catatan_evaluasi` TEXT,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

            // Master Instrumen table
            $db->exec("CREATE TABLE IF NOT EXISTS `kurikulum_instrumen_supervisi` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `tipe_supervisi` VARCHAR(100) NOT NULL,
                `kategori` VARCHAR(100) NOT NULL,
                `kode` VARCHAR(50) NOT NULL,
                `aspek` TEXT NOT NULL,
                `bobot_maksimal` INT DEFAULT 4,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

            // Hasil Nilai Instrumen table
            $db->exec("CREATE TABLE IF NOT EXISTS `kurikulum_supervisi_hasil` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `tipe_supervisi` VARCHAR(100) NOT NULL,
                `supervisi_id` INT NOT NULL,
                `instrumen_id` INT NOT NULL,
                `skor` INT NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY `idx_lookup` (`tipe_supervisi`, `supervisi_id`, `instrumen_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

            // Ensure columns for Administrasi
            $ensureAdm = [
                'pegawai_id' => 'INT NULL',
                'supervisor_id' => 'INT NULL',
                'tanggal' => 'DATE NULL',
                'nilai' => 'FLOAT NULL',
                'catatan_evaluasi' => 'TEXT NULL',
                'status' => "VARCHAR(50) DEFAULT 'Jadwal'",
                'is_released' => 'TINYINT(1) DEFAULT 0',
                'is_hasil_released' => 'TINYINT(1) DEFAULT 0'
            ];
            foreach ($ensureAdm as $col => $def) {
                try { $db->exec("ALTER TABLE `kurikulum_supervisi_administrasi` ADD COLUMN `$col` $def"); } catch (\Throwable $e) {}
            }

            // Ensure columns for Kelas
            $ensureKls = [
                'tanggal' => 'DATE NULL',
                'nilai' => 'FLOAT NULL',
                'catatan_evaluasi' => 'TEXT NULL',
                'status' => "VARCHAR(50) DEFAULT 'Jadwal'",
                'kelas_id' => 'INT NULL',
                'mapel_id' => 'INT NULL',
                'supervisor_id' => 'INT NULL',
                'is_released' => 'TINYINT(1) DEFAULT 0',
                'is_hasil_released' => 'TINYINT(1) DEFAULT 0',
                'tindak_lanjut_jenis' => 'VARCHAR(100) NULL',
                'tindak_lanjut' => 'TEXT NULL',
                'tindak_lanjut_deadline' => 'VARCHAR(50) NULL',
                'tindak_lanjut_status' => "VARCHAR(50) DEFAULT 'Belum Terlaksana'",
                'tindak_lanjut_realisasi' => 'TEXT NULL'
            ];
            foreach ($ensureKls as $col => $def) {
                try { $db->exec("ALTER TABLE `kurikulum_supervisi_kelas` ADD COLUMN `$col` $def"); } catch (\Throwable $e) {}
            }

            // Ensure columns for Universal
            try { $db->exec("ALTER TABLE `kurikulum_supervisi_universal` ADD COLUMN `is_released` TINYINT(1) DEFAULT 0"); } catch (\Throwable $e) {}
            try { $db->exec("ALTER TABLE `kurikulum_supervisi_universal` ADD COLUMN `is_hasil_released` TINYINT(1) DEFAULT 0"); } catch (\Throwable $e) {}
        } catch (\Throwable $e) {}
    }

    public static function ensureMonitoringTables($db = null) {
        if (!$db) $db = \App\Core\Database::connect('core');
        try {
            // Ensure bobot institusi
            try { $db->exec("ALTER TABLE `institusi` ADD COLUMN `bobot_harian` INT DEFAULT 50"); } catch (\Throwable $e) {}
            try { $db->exec("ALTER TABLE `institusi` ADD COLUMN `bobot_pas` INT DEFAULT 50"); } catch (\Throwable $e) {}

            // Ensure kurikulum_kkm table
            $db->exec("CREATE TABLE IF NOT EXISTS `kurikulum_kkm` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `tahun_ajaran_id` INT NULL,
                `mapel_id` INT NOT NULL,
                `tingkat` INT NOT NULL,
                `nilai_kkm` INT NOT NULL DEFAULT 75,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY `kkm_mapel_tingkat` (`tahun_ajaran_id`, `mapel_id`, `tingkat`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // Ensure view_nilai_harian_unified exists
            $db->exec("CREATE OR REPLACE ALGORITHM=UNDEFINED VIEW `view_nilai_harian_unified` AS
                SELECT 
                    nhd.id,
                    nhd.siswa_id,
                    nh.mapel_id,
                    nh.kelas_id,
                    nh.guru_id,
                    nh.tahun_ajaran_id,
                    nh.semester,
                    TRIM(SUBSTRING_INDEX(nh.judul_materi, ' - ', 1)) as jenis_evaluasi,
                    CASE 
                        WHEN nh.judul_materi LIKE '% - %' THEN TRIM(SUBSTRING(nh.judul_materi, LOCATE(' - ', nh.judul_materi) + 3))
                        ELSE nh.judul_materi
                    END as materi,
                    nhd.nilai,
                    nh.tanggal,
                    nh.created_at
                FROM nilai_harian nh
                JOIN nilai_harian_detail nhd ON nhd.nilai_harian_id = nh.id");
        } catch (\Throwable $e) {
            error_log("ensureMonitoringTables failed: " . $e->getMessage());
        }
    }

    private static function ensureTimSupervisiTables($db) {
        self::ensureSupervisiTables($db);
    }

    public function timSupervisi() {
        $active_module = 'kurikulum';
        $active_page   = 'supervisi_tim';
        $db = \App\Core\Database::connect('core');
        $legalitas = self::getSupervisiLegalitas($db);
        extract($legalitas);

        $daftarGuru = [];
        try {
            $daftarGuru = $db->query("SELECT id, nama, nip, jabatan, is_kamad FROM guru ORDER BY nama ASC")->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {}

        require __DIR__ . '/../../resources/views/kurikulum/supervisi_tim.php';
    }

    public static function ensureProgramUnggulanTables($db = null) {
        if (!$db) $db = \App\Core\Database::connect('core');
        try {
            $db->exec("CREATE TABLE IF NOT EXISTS `kurikulum_program_unggulan_config` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `nomor_sk` VARCHAR(100) DEFAULT '421.3/088/MAS-MH/SK-PROG/VII/2026',
                `tanggal_sk` DATE DEFAULT '2026-07-15',
                `tahun_ajaran` VARCHAR(20) DEFAULT '2026/2027',
                `semester` VARCHAR(20) DEFAULT 'Ganjil',
                `kamad_id` INT NULL,
                `kamad_nama` VARCHAR(150) DEFAULT 'Drs. Hamim Hasyim',
                `kamad_nip` VARCHAR(50) DEFAULT '-',
                `waka_id` INT NULL,
                `waka_nama` VARCHAR(150) DEFAULT 'M AZIZ SULAIMAN, S.Pd',
                `waka_nip` VARCHAR(50) DEFAULT '1804072704000002',
                `kord_pesantren_id` INT NULL,
                `kord_pesantren_nama` VARCHAR(150) DEFAULT 'SAIPUDIN AZIZ, S.Pd',
                `pembina_rohis_id` INT NULL,
                `pembina_rohis_nama` VARCHAR(150) DEFAULT 'DIDI HARTONO, S.Pd.I',
                `kord_lifeskill_id` INT NULL,
                `kord_lifeskill_nama` VARCHAR(150) DEFAULT 'INDAH YUNITASARI, S.Pd',
                `kord_digital_id` INT NULL,
                `kord_digital_nama` VARCHAR(150) DEFAULT 'SYAIFUL BAHRI, S.Pd',
                `komite_nama` VARCHAR(150) DEFAULT 'H. AHMAD ROFIQ, S.Ag',
                `pengawas_nama` VARCHAR(150) DEFAULT 'Drs. H. M. YUSUF, M.Pd.I',
                `pengawas_nip` VARCHAR(50) DEFAULT '196805141994031002',
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $cnt = (int)$db->query("SELECT COUNT(*) FROM `kurikulum_program_unggulan_config`")->fetchColumn();
            if ($cnt === 0) {
                $db->exec("INSERT INTO `kurikulum_program_unggulan_config` 
                    (nomor_sk, tanggal_sk, tahun_ajaran, semester, kamad_nama, waka_nama, kord_pesantren_nama, pembina_rohis_nama, kord_lifeskill_nama, kord_digital_nama, komite_nama, pengawas_nama, pengawas_nip)
                    VALUES 
                    ('421.3/088/MAS-MH/SK-PROG/VII/2026', '2026-07-15', '2026/2027', 'Ganjil', 'AHMAD RIFA\'I, M.Pd', 'Drs. Hamim Hasyim', 'SAIPUDIN AZIZ, S.Pd', 'DIDI HARTONO, S.Pd.I', 'INDAH YUNITASARI, S.Pd', 'SYAIFUL BAHRI, S.Pd', 'IMAM SYAFE\'I', '', '')");
            }

            $cols = $db->query("SHOW COLUMNS FROM `kurikulum_program_unggulan_config`")->fetchAll(\PDO::FETCH_COLUMN);
            if (!in_array('jadwal_santri_json', $cols)) {
                $db->exec("ALTER TABLE `kurikulum_program_unggulan_config` ADD COLUMN `jadwal_santri_json` LONGTEXT NULL AFTER `pengawas_nip`");
            }
            if (!in_array('sop_config_json', $cols)) {
                $db->exec("ALTER TABLE `kurikulum_program_unggulan_config` ADD COLUMN `sop_config_json` LONGTEXT NULL AFTER `jadwal_santri_json`");
            }
        } catch (\Throwable $e) {
            error_log("ensureProgramUnggulanTables failed: " . $e->getMessage());
        }
    }

    public static function getProgramUnggulanConfig($db = null) {
        if (!$db) $db = \App\Core\Database::connect('core');
        self::ensureProgramUnggulanTables($db);

        $default = [
            'nomor_sk' => '421.3/088/MAS-MH/SK-PROG/VII/2026',
            'tanggal_sk' => '2026-07-15',
            'tahun_ajaran' => '2026/2027',
            'semester' => 'Ganjil',
            'kamad_id' => null,
            'kamad_nama' => 'AHMAD RIFA\'I, M.Pd',
            'kamad_nip' => '-',
            'waka_id' => null,
            'waka_nama' => 'Drs. Hamim Hasyim',
            'waka_nip' => '-',
            'kord_pesantren_id' => null,
            'kord_pesantren_nama' => 'SAIPUDIN AZIZ, S.Pd',
            'pembina_rohis_id' => null,
            'pembina_rohis_nama' => 'DIDI HARTONO, S.Pd.I',
            'kord_lifeskill_id' => null,
            'kord_lifeskill_nama' => 'INDAH YUNITASARI, S.Pd',
            'kord_digital_id' => null,
            'kord_digital_nama' => 'SYAIFUL BAHRI, S.Pd',
            'komite_nama' => 'IMAM SYAFE\'I',
            'pengawas_nama' => '',
            'pengawas_nip' => '',
            'jadwal_santri_json' => null,
            'sop_config_json' => null
        ];

        try {
            $row = $db->query("SELECT * FROM kurikulum_program_unggulan_config ORDER BY id DESC LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
            if ($row) {
                return array_merge($default, $row);
            }
        } catch (\Throwable $e) {}

        return $default;
    }

    public function programUnggulan() {
        $active_module = 'kurikulum';
        $active_page   = 'program_unggulan';
        $db = \App\Core\Database::connect('core');
        $legalitas = self::getSupervisiLegalitas($db);
        extract($legalitas);

        $cfgProg = self::getProgramUnggulanConfig($db);

        $daftarGuru = [];
        try {
            $daftarGuru = $db->query("SELECT id, nama, nip, jabatan, is_kamad FROM guru ORDER BY nama ASC")->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {}

        require __DIR__ . '/../../resources/views/kurikulum/program_unggulan.php';
    }

    public function cetakProgramUnggulan() {
        $db = \App\Core\Database::connect('core');
        $legalitas = self::getSupervisiLegalitas($db);
        extract($legalitas);

        $cfgProg = self::getProgramUnggulanConfig($db);

        $daftarGuru = [];
        try {
            $daftarGuru = $db->query("SELECT id, nama, nip, jabatan, is_kamad FROM guru ORDER BY nama ASC")->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {}

        require __DIR__ . '/../../resources/views/kurikulum/program_unggulan_cetak.php';
    }

    public function updateProgramUnggulanConfig() {
        $db = \App\Core\Database::connect('core');
        self::ensureProgramUnggulanTables($db);

        $nomor_sk = trim($_POST['nomor_sk'] ?? '421.3/088/MAS-MH/SK-PROG/VII/2026');
        $tanggal_sk = trim($_POST['tanggal_sk'] ?? '2026-07-15');
        $tahun_ajaran = trim($_POST['tahun_ajaran'] ?? '2026/2027');
        $semester = trim($_POST['semester'] ?? 'Ganjil');

        $kamad_id = !empty($_POST['kamad_id']) ? (int)$_POST['kamad_id'] : null;
        $kamad_nama = trim($_POST['kamad_nama'] ?? 'AHMAD RIFA\'I, M.Pd');
        $kamad_nip = trim($_POST['kamad_nip'] ?? '-');

        $waka_id = !empty($_POST['waka_id']) ? (int)$_POST['waka_id'] : null;
        $waka_nama = trim($_POST['waka_nama'] ?? 'Drs. Hamim Hasyim');
        $waka_nip = trim($_POST['waka_nip'] ?? '-');

        $kord_pesantren_id = !empty($_POST['kord_pesantren_id']) ? (int)$_POST['kord_pesantren_id'] : null;
        $kord_pesantren_nama = trim($_POST['kord_pesantren_nama'] ?? 'SAIPUDIN AZIZ, S.Pd');

        $pembina_rohis_id = !empty($_POST['pembina_rohis_id']) ? (int)$_POST['pembina_rohis_id'] : null;
        $pembina_rohis_nama = trim($_POST['pembina_rohis_nama'] ?? 'DIDI HARTONO, S.Pd.I');

        $kord_lifeskill_id = !empty($_POST['kord_lifeskill_id']) ? (int)$_POST['kord_lifeskill_id'] : null;
        $kord_lifeskill_nama = trim($_POST['kord_lifeskill_nama'] ?? 'INDAH YUNITASARI, S.Pd');

        $kord_digital_id = !empty($_POST['kord_digital_id']) ? (int)$_POST['kord_digital_id'] : null;
        $kord_digital_nama = trim($_POST['kord_digital_nama'] ?? 'SYAIFUL BAHRI, S.Pd');

        $komite_nama = trim($_POST['komite_nama'] ?? 'IMAM SYAFE\'I');
        $pengawas_nama = trim($_POST['pengawas_nama'] ?? '');
        $pengawas_nip = trim($_POST['pengawas_nip'] ?? '');

        // JADWAL RUTINITAS SANTRI JSON
        $jadwal_santri_json = null;
        if (!empty($_POST['jadwal_santri_json']) && trim($_POST['jadwal_santri_json']) !== '') {
            $jadwal_santri_json = trim($_POST['jadwal_santri_json']);
        } elseif (!empty($_POST['jadwal']) && is_array($_POST['jadwal'])) {
            $cleanJadwal = [];
            foreach ($_POST['jadwal'] as $row) {
                if (!empty($row['waktu']) || !empty($row['kegiatan'])) {
                    $cleanJadwal[] = [
                        'hari' => trim($row['hari'] ?? 'Setiap Hari'),
                        'waktu' => trim($row['waktu'] ?? ''),
                        'pilar' => trim($row['pilar'] ?? 'Pesantren'),
                        'kegiatan' => trim($row['kegiatan'] ?? ''),
                        'pj' => trim($row['pj'] ?? '')
                    ];
                }
            }
            if (!empty($cleanJadwal)) {
                $jadwal_santri_json = json_encode($cleanJadwal, JSON_UNESCAPED_UNICODE);
            }
        }

        // SOP SERTIFIKASI MUTU JSON
        $sop_config_json = null;
        if (!empty($_POST['sop_config_json']) && trim($_POST['sop_config_json']) !== '') {
            $sop_config_json = trim($_POST['sop_config_json']);
        } elseif (!empty($_POST['sop']) && is_array($_POST['sop'])) {
            $cleanSop = [
                'tahfidz_target' => trim($_POST['sop']['tahfidz_target'] ?? 'Juz 30 Mutqin (Plus Juz 1 & 2 Pilihan)'),
                'tahfidz_kelulusan' => trim($_POST['sop']['tahfidz_kelulusan'] ?? 'Minimal Nilai 80 (Predikat Jayyid Jiddan) - Tasmi\' Bil Ghaib 1 Majelis'),
                'tahfidz_output' => trim($_POST['sop']['tahfidz_output'] ?? 'Syahadah Tahfidz Al-Qur\'an Resmi & Sertifikat Munaqosyah Kemenag/Madrasah'),
                'tahfidz_reward' => trim($_POST['sop']['tahfidz_reward'] ?? 'Beasiswa Bebas SPP & Apresiasi Wisuda Tahfidz bagi santri Mumtaz (90-100)'),
                'lifeskill_presensi' => trim($_POST['sop']['lifeskill_presensi'] ?? 'Kehadiran Praktik Vokasi Minimal 85% Jam Efektif'),
                'lifeskill_ujian' => trim($_POST['sop']['lifeskill_ujian'] ?? 'Uji Petik Hasil Karya Nyata Mandiri (Busana Siap Pakai / Masakan Higienis / Perakitan PC & LAN)'),
                'lifeskill_expo' => trim($_POST['sop']['lifeskill_expo'] ?? 'Expo Gelar Karya Santri (Market Day & Pameran Terbuka Akhir Semester)'),
                'lifeskill_output' => trim($_POST['sop']['lifeskill_output'] ?? 'Sertifikat Uji Kompetensi Kejuruan (Life Skill Certificate) Berstandar Industri'),
                'digital_ujian' => trim($_POST['sop']['digital_ujian'] ?? '100% Ujian Paperless via MASMIFHDA CBT & Transkrip Portofolio Koding Web'),
                'digital_output' => trim($_POST['sop']['digital_output'] ?? 'Sertifikat Literasi Digital, Pemrograman Dasar & Etika Kecerdasan Buatan (AI)')
            ];
            $sop_config_json = json_encode($cleanSop, JSON_UNESCAPED_UNICODE);
        }

        try {
            $exists = $db->query("SELECT id FROM kurikulum_program_unggulan_config LIMIT 1")->fetchColumn();
            if ($exists) {
                $stmt = $db->prepare("UPDATE kurikulum_program_unggulan_config SET
                    nomor_sk = :nomor_sk,
                    tanggal_sk = :tanggal_sk,
                    tahun_ajaran = :tahun_ajaran,
                    semester = :semester,
                    kamad_id = :kamad_id,
                    kamad_nama = :kamad_nama,
                    kamad_nip = :kamad_nip,
                    waka_id = :waka_id,
                    waka_nama = :waka_nama,
                    waka_nip = :waka_nip,
                    kord_pesantren_id = :kord_pesantren_id,
                    kord_pesantren_nama = :kord_pesantren_nama,
                    pembina_rohis_id = :pembina_rohis_id,
                    pembina_rohis_nama = :pembina_rohis_nama,
                    kord_lifeskill_id = :kord_lifeskill_id,
                    kord_lifeskill_nama = :kord_lifeskill_nama,
                    kord_digital_id = :kord_digital_id,
                    kord_digital_nama = :kord_digital_nama,
                    komite_nama = :komite_nama,
                    pengawas_nama = :pengawas_nama,
                    pengawas_nip = :pengawas_nip,
                    jadwal_santri_json = :jadwal_santri_json,
                    sop_config_json = :sop_config_json
                    WHERE id = :id");
                $stmt->execute([
                    ':nomor_sk' => $nomor_sk,
                    ':tanggal_sk' => $tanggal_sk,
                    ':tahun_ajaran' => $tahun_ajaran,
                    ':semester' => $semester,
                    ':kamad_id' => $kamad_id,
                    ':kamad_nama' => $kamad_nama,
                    ':kamad_nip' => $kamad_nip,
                    ':waka_id' => $waka_id,
                    ':waka_nama' => $waka_nama,
                    ':waka_nip' => $waka_nip,
                    ':kord_pesantren_id' => $kord_pesantren_id,
                    ':kord_pesantren_nama' => $kord_pesantren_nama,
                    ':pembina_rohis_id' => $pembina_rohis_id,
                    ':pembina_rohis_nama' => $pembina_rohis_nama,
                    ':kord_lifeskill_id' => $kord_lifeskill_id,
                    ':kord_lifeskill_nama' => $kord_lifeskill_nama,
                    ':kord_digital_id' => $kord_digital_id,
                    ':kord_digital_nama' => $kord_digital_nama,
                    ':komite_nama' => $komite_nama,
                    ':pengawas_nama' => $pengawas_nama,
                    ':pengawas_nip' => $pengawas_nip,
                    ':jadwal_santri_json' => $jadwal_santri_json,
                    ':sop_config_json' => $sop_config_json,
                    ':id' => $exists
                ]);
            } else {
                $stmt = $db->prepare("INSERT INTO kurikulum_program_unggulan_config
                    (nomor_sk, tanggal_sk, tahun_ajaran, semester, kamad_id, kamad_nama, kamad_nip, waka_id, waka_nama, waka_nip, kord_pesantren_id, kord_pesantren_nama, pembina_rohis_id, pembina_rohis_nama, kord_lifeskill_id, kord_lifeskill_nama, kord_digital_id, kord_digital_nama, komite_nama, pengawas_nama, pengawas_nip, jadwal_santri_json, sop_config_json)
                    VALUES
                    (:nomor_sk, :tanggal_sk, :tahun_ajaran, :semester, :kamad_id, :kamad_nama, :kamad_nip, :waka_id, :waka_nama, :waka_nip, :kord_pesantren_id, :kord_pesantren_nama, :pembina_rohis_id, :pembina_rohis_nama, :kord_lifeskill_id, :kord_lifeskill_nama, :kord_digital_id, :kord_digital_nama, :komite_nama, :pengawas_nama, :pengawas_nip, :jadwal_santri_json, :sop_config_json)");
                $stmt->execute([
                    ':nomor_sk' => $nomor_sk,
                    ':tanggal_sk' => $tanggal_sk,
                    ':tahun_ajaran' => $tahun_ajaran,
                    ':semester' => $semester,
                    ':kamad_id' => $kamad_id,
                    ':kamad_nama' => $kamad_nama,
                    ':kamad_nip' => $kamad_nip,
                    ':waka_id' => $waka_id,
                    ':waka_nama' => $waka_nama,
                    ':waka_nip' => $waka_nip,
                    ':kord_pesantren_id' => $kord_pesantren_id,
                    ':kord_pesantren_nama' => $kord_pesantren_nama,
                    ':pembina_rohis_id' => $pembina_rohis_id,
                    ':pembina_rohis_nama' => $pembina_rohis_nama,
                    ':kord_lifeskill_id' => $kord_lifeskill_id,
                    ':kord_lifeskill_nama' => $kord_lifeskill_nama,
                    ':kord_digital_id' => $kord_digital_id,
                    ':kord_digital_nama' => $kord_digital_nama,
                    ':komite_nama' => $komite_nama,
                    ':pengawas_nama' => $pengawas_nama,
                    ':pengawas_nip' => $pengawas_nip,
                    ':jadwal_santri_json' => $jadwal_santri_json,
                    ':sop_config_json' => $sop_config_json
                ]);
            }
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['flash_msg'] = 'Pengaturan Tim & Program Unggulan berhasil diperbarui!';
        } catch (\Throwable $e) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['flash_err'] = 'Gagal menyimpan pengaturan: ' . $e->getMessage();
        }

        header('Location: ' . base_url('kurikulum/program-unggulan'));
        exit;
    }

    public function updateTimSupervisiConfig() {
        $db = \App\Core\Database::connect('core');
        self::ensureTimSupervisiTables($db);
        $nomor_sk = trim($_POST['nomor_sk'] ?? '');
        $tanggal_sk = trim($_POST['tanggal_sk'] ?? '2026-07-15');
        $tanggal_laporan = trim($_POST['tanggal_laporan'] ?? '2026-11-11');
        $kamad_nama = trim($_POST['kamad_nama'] ?? '');
        $kamad_nip = trim($_POST['kamad_nip'] ?? '-');
        $kamad_jabatan = trim($_POST['kamad_jabatan'] ?? 'Kepala Madrasah / Supervisor Utama');
        $pengawas_nama = trim($_POST['pengawas_nama'] ?? '');
        $pengawas_nip = trim($_POST['pengawas_nip'] ?? '-');
        $pengawas_jabatan = trim($_POST['pengawas_jabatan'] ?? 'Pengawas Madrasah Ahli Madya Kemenag');
        $tahun_ajaran = trim($_POST['tahun_ajaran'] ?? '2026/2027');
        $semester = trim($_POST['semester'] ?? 'Ganjil');

        try {
            $exists = $db->query("SELECT id FROM kurikulum_supervisi_config LIMIT 1")->fetchColumn();
            if ($exists) {
                $stmt = $db->prepare("UPDATE kurikulum_supervisi_config SET 
                    nomor_sk = :nomor_sk,
                    tanggal_sk = :tanggal_sk,
                    tanggal_laporan = :tanggal_laporan,
                    kamad_nama = :kamad_nama,
                    kamad_nip = :kamad_nip,
                    kamad_jabatan = :kamad_jabatan,
                    pengawas_nama = :pengawas_nama,
                    pengawas_nip = :pengawas_nip,
                    pengawas_jabatan = :pengawas_jabatan,
                    tahun_ajaran = :tahun_ajaran,
                    semester = :semester
                    WHERE id = :id");
                $stmt->execute([
                    ':nomor_sk' => $nomor_sk,
                    ':tanggal_sk' => $tanggal_sk,
                    ':tanggal_laporan' => $tanggal_laporan,
                    ':kamad_nama' => $kamad_nama,
                    ':kamad_nip' => $kamad_nip,
                    ':kamad_jabatan' => $kamad_jabatan,
                    ':pengawas_nama' => $pengawas_nama,
                    ':pengawas_nip' => $pengawas_nip,
                    ':pengawas_jabatan' => $pengawas_jabatan,
                    ':tahun_ajaran' => $tahun_ajaran,
                    ':semester' => $semester,
                    ':id' => $exists
                ]);
            } else {
                $stmt = $db->prepare("INSERT INTO kurikulum_supervisi_config 
                    (nomor_sk, tanggal_sk, tanggal_laporan, kamad_nama, kamad_nip, kamad_jabatan, pengawas_nama, pengawas_nip, pengawas_jabatan, tahun_ajaran, semester)
                    VALUES 
                    (:nomor_sk, :tanggal_sk, :tanggal_laporan, :kamad_nama, :kamad_nip, :kamad_jabatan, :pengawas_nama, :pengawas_nip, :pengawas_jabatan, :tahun_ajaran, :semester)");
                $stmt->execute([
                    ':nomor_sk' => $nomor_sk,
                    ':tanggal_sk' => $tanggal_sk,
                    ':tanggal_laporan' => $tanggal_laporan,
                    ':kamad_nama' => $kamad_nama,
                    ':kamad_nip' => $kamad_nip,
                    ':kamad_jabatan' => $kamad_jabatan,
                    ':pengawas_nama' => $pengawas_nama,
                    ':pengawas_nip' => $pengawas_nip,
                    ':pengawas_jabatan' => $pengawas_jabatan,
                    ':tahun_ajaran' => $tahun_ajaran,
                    ':semester' => $semester
                ]);
            }
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['flash_msg'] = 'Pengaturan SK & Personel Tim Supervisi berhasil disimpan!';
        } catch (\Throwable $e) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['flash_err'] = 'Gagal menyimpan pengaturan: ' . $e->getMessage();
        }

        header('Location: ' . base_url('kurikulum/supervisi/tim'));
        exit;
    }

    public function addTimSupervisiMember() {
        $db = \App\Core\Database::connect('core');
        self::ensureTimSupervisiTables($db);
        $guru_id = !empty($_POST['guru_id']) ? (int)$_POST['guru_id'] : null;
        $nama = trim($_POST['nama'] ?? '');
        $nip = trim($_POST['nip'] ?? '-');
        $jabatan_madrasah = trim($_POST['jabatan_madrasah'] ?? 'Guru');
        $peran_tim = trim($_POST['peran_tim'] ?? 'Supervisor');
        $tugas_fokus = trim($_POST['tugas_fokus'] ?? '');
        $urutan = !empty($_POST['urutan']) ? (int)$_POST['urutan'] : 99;

        if ($guru_id) {
            $g = $db->query("SELECT nama, nip, jabatan, is_kamad FROM guru WHERE id = " . $guru_id)->fetch(\PDO::FETCH_ASSOC);
            if ($g) {
                if (empty($nama)) $nama = $g['nama'];
                if (($nip === '-' || empty($nip)) && !empty($g['nip'])) $nip = $g['nip'];
                if (($jabatan_madrasah === 'Guru' || empty($jabatan_madrasah)) && !empty($g['jabatan'])) {
                    $jabatan_madrasah = $g['jabatan'];
                }
            }
        }

        if (empty($tugas_fokus)) {
            $p = strtolower($peran_tim);
            if (strpos($p, 'penanggung') !== false || strpos($p, 'utama') !== false) {
                $tugas_fokus = 'Pengarah, Penanggung Jawab Mutu & Monev Keseluruhan KBM';
            } elseif (strpos($p, 'ketua') !== false) {
                $tugas_fokus = 'Koordinasi Jadwal Supervisi, Verifikasi Administrasi Guru & Analisis Hasil Observasi';
            } elseif (strpos($p, 'sekretaris') !== false) {
                $tugas_fokus = 'Notulensi Coaching, Administrasi Instrumen & Rekapitulasi Laporan RTL';
            } elseif (strpos($p, 'supervisor') !== false || strpos($p, 'observer') !== false) {
                $tugas_fokus = 'Observasi KBM Tatap Muka Kelas, Penilaian 25 Indikator KBM & Bimbingan Klinis';
            } else {
                $tugas_fokus = 'Observasi Pembelajaran Kelas & Pendampingan Perangkat Ajar';
            }
        }

        try {
            if (!empty($nama)) {
                $stmt = $db->prepare("INSERT INTO kurikulum_supervisi_tim 
                    (tahun_ajaran, semester, guru_id, nama, nip, jabatan_madrasah, peran_tim, tugas_fokus, urutan)
                    VALUES 
                    ('2026/2027', 'Ganjil', :guru_id, :nama, :nip, :jabatan, :peran, :fokus, :urutan)");
                $stmt->execute([
                    ':guru_id' => $guru_id,
                    ':nama' => $nama,
                    ':nip' => $nip,
                    ':jabatan' => $jabatan_madrasah,
                    ':peran' => $peran_tim,
                    ':fokus' => $tugas_fokus,
                    ':urutan' => $urutan
                ]);
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['flash_msg'] = 'Anggota tim supervisi berhasil ditambahkan!';
            }
        } catch (\Throwable $e) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['flash_err'] = 'Gagal menambahkan anggota tim: ' . $e->getMessage();
        }

        header('Location: ' . base_url('kurikulum/supervisi/tim'));
        exit;
    }

    public function updateTimSupervisiMember() {
        $db = \App\Core\Database::connect('core');
        self::ensureTimSupervisiTables($db);
        $id = (int)($_POST['id'] ?? 0);
        $guru_id = !empty($_POST['guru_id']) ? (int)$_POST['guru_id'] : null;
        $nama = trim($_POST['nama'] ?? '');
        $nip = trim($_POST['nip'] ?? '-');
        $jabatan_madrasah = trim($_POST['jabatan_madrasah'] ?? 'Guru');
        $peran_tim = trim($_POST['peran_tim'] ?? 'Supervisor');
        $tugas_fokus = trim($_POST['tugas_fokus'] ?? '');
        $urutan = !empty($_POST['urutan']) ? (int)$_POST['urutan'] : 1;

        if ($guru_id) {
            $g = $db->query("SELECT nama, nip, jabatan, is_kamad FROM guru WHERE id = " . $guru_id)->fetch(\PDO::FETCH_ASSOC);
            if ($g) {
                if (empty($nama)) $nama = $g['nama'];
                if (($nip === '-' || empty($nip)) && !empty($g['nip'])) $nip = $g['nip'];
                if (($jabatan_madrasah === 'Guru' || empty($jabatan_madrasah)) && !empty($g['jabatan'])) {
                    $jabatan_madrasah = $g['jabatan'];
                }
            }
        }

        if (empty($tugas_fokus)) {
            $p = strtolower($peran_tim);
            if (strpos($p, 'penanggung') !== false || strpos($p, 'utama') !== false) {
                $tugas_fokus = 'Pengarah, Penanggung Jawab Mutu & Monev Keseluruhan KBM';
            } elseif (strpos($p, 'ketua') !== false) {
                $tugas_fokus = 'Koordinasi Jadwal Supervisi, Verifikasi Administrasi Guru & Analisis Hasil Observasi';
            } elseif (strpos($p, 'sekretaris') !== false) {
                $tugas_fokus = 'Notulensi Coaching, Administrasi Instrumen & Rekapitulasi Laporan RTL';
            } elseif (strpos($p, 'supervisor') !== false || strpos($p, 'observer') !== false) {
                $tugas_fokus = 'Observasi KBM Tatap Muka Kelas, Penilaian 25 Indikator KBM & Bimbingan Klinis';
            } else {
                $tugas_fokus = 'Observasi Pembelajaran Kelas & Pendampingan Perangkat Ajar';
            }
        }

        try {
            if ($id > 0 && !empty($nama)) {
                $stmt = $db->prepare("UPDATE kurikulum_supervisi_tim SET 
                    guru_id = :guru_id,
                    nama = :nama,
                    nip = :nip,
                    jabatan_madrasah = :jabatan,
                    peran_tim = :peran,
                    tugas_fokus = :fokus,
                    urutan = :urutan
                    WHERE id = :id");
                $stmt->execute([
                    ':guru_id' => $guru_id,
                    ':nama' => $nama,
                    ':nip' => $nip,
                    ':jabatan' => $jabatan_madrasah,
                    ':peran' => $peran_tim,
                    ':fokus' => $tugas_fokus,
                    ':urutan' => $urutan,
                    ':id' => $id
                ]);
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['flash_msg'] = 'Data anggota tim supervisi berhasil diperbarui!';
            }
        } catch (\Throwable $e) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['flash_err'] = 'Gagal memperbarui anggota tim: ' . $e->getMessage();
        }

        header('Location: ' . base_url('kurikulum/supervisi/tim'));
        exit;
    }

    public function deleteTimSupervisiMember() {
        $db = \App\Core\Database::connect('core');
        self::ensureTimSupervisiTables($db);
        $id = (int)($_POST['id'] ?? 0);
        try {
            if ($id > 0) {
                $db->exec("DELETE FROM kurikulum_supervisi_tim WHERE id = " . $id);
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['flash_msg'] = 'Anggota tim supervisi berhasil dihapus!';
            }
        } catch (\Throwable $e) {}
        header('Location: ' . base_url('kurikulum/supervisi/tim'));
        exit;
    }

    public function cetakSKTimSupervisi() {
        $db = \App\Core\Database::connect('core');
        $legalitas = self::getSupervisiLegalitas($db);
        extract($legalitas);

        require __DIR__ . '/../../resources/views/kurikulum/supervisi_tim_sk_cetak.php';
    }

    // ─── AKADEMIK & MONITORING SUITE ──────────────────────────────────────────

    public function jadwalPelajaran() {
        $active_module = 'kurikulum';
        $active_page   = 'kurikulum_jadwal';
        $db = \App\Core\Database::connect('core');
        $db_siakad = \App\Core\Database::connect('siakad');

        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? 2;

        $kelas = $db->query("SELECT * FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll();
        $guruList = $db->query("SELECT id, nama FROM guru ORDER BY nama ASC")->fetchAll();
        $guruMap = [];
        foreach ($guruList as $g) $guruMap[$g['id']] = $g['nama'];

        $mapelList = [];
        try {
            $siakadMapels = $db_siakad->query("SELECT id, nama_mapel FROM mapel")->fetchAll();
            foreach ($siakadMapels as $sm) $mapelList[$sm['id']] = $sm['nama_mapel'];
        } catch (\Exception $e) {}
        try {
            $coreMapels = $db->query("SELECT id, nama_mapel FROM mapel")->fetchAll();
            foreach ($coreMapels as $cm) {
                if (!isset($mapelList[$cm['id']])) $mapelList[$cm['id']] = $cm['nama_mapel'];
            }
        } catch (\Exception $e) {}

        // Query jadwal from siakad
        $stmt_jadwal = $db_siakad->prepare("
            SELECT id, tahun_ajaran_id, semester, kelas_id, hari, jam_mulai, jam_selesai,
                   mata_pelajaran_id as mapel_id, guru_id
            FROM jadwal_pelajaran
            WHERE tahun_ajaran_id = ?
            ORDER BY jam_mulai ASC
        ");
        $stmt_jadwal->execute([$tahun_ajaran_id]);
        $jadwal_raw = $stmt_jadwal->fetchAll();
        if (empty($jadwal_raw)) {
            $jadwal_raw = $db_siakad->query("SELECT id, tahun_ajaran_id, semester, kelas_id, hari, jam_mulai, jam_selesai, mata_pelajaran_id as mapel_id, guru_id FROM jadwal_pelajaran ORDER BY jam_mulai ASC")->fetchAll();
        }

        $jadwal_master = [];
        $jam_list = [];
        $hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        foreach ($jadwal_raw as $j) {
            $h = $j['hari'];
            if ($h == "Jum'at" || $h == 'Jum`at') $h = 'Jumat';
            $jam = substr($j['jam_mulai'], 0, 5) . ' - ' . substr($j['jam_selesai'], 0, 5);
            $j['nama_mapel'] = $mapelList[$j['mapel_id']] ?? ('Mapel #' . $j['mapel_id']);
            $j['nama_guru'] = $guruMap[$j['guru_id']] ?? ('Guru #' . $j['guru_id']);
            $jadwal_master[$h][$jam][$j['kelas_id']] = $j;
            if (!in_array($jam, $jam_list)) {
                $jam_list[] = $jam;
            }
        }
        sort($jam_list);

        require_once __DIR__ . '/../../resources/views/kurikulum/jadwal.php';
    }

    public function monitoringJurnal() {
        $active_module = 'kurikulum';
        $active_page   = 'kurikulum_monitoring_jurnal';
        $db = \App\Core\Database::connect('core');
        $db_siakad = \App\Core\Database::connect('siakad');

        $filter_tanggal_mulai = $_GET['tanggal_mulai'] ?? date('Y-m-01');
        $filter_tanggal_akhir = $_GET['tanggal_akhir'] ?? date('Y-m-d');
        $filter_kelas = $_GET['kelas_id'] ?? '';
        $filter_guru = $_GET['guru_id'] ?? '';
        $filter_mapel = $_GET['mapel_id'] ?? '';

        $kelasList = $db->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll();
        $guruList = $db->query("SELECT id, nama FROM guru ORDER BY nama ASC")->fetchAll();
        $guruMap = [];
        foreach ($guruList as $g) $guruMap[$g['id']] = $g['nama'];

        $mapelList = [];
        try {
            $mapels = $db_siakad->query("SELECT id, nama_mapel FROM mapel ORDER BY nama_mapel ASC")->fetchAll();
            $mapelList = $mapels;
        } catch (\Exception $e) {}
        if (empty($mapelList)) {
            try {
                $mapelList = $db->query("SELECT id, nama_mapel FROM mapel ORDER BY nama_mapel ASC")->fetchAll();
            } catch (\Exception $e) {}
        }
        $mapelMap = [];
        foreach ($mapelList as $m) $mapelMap[$m['id']] = $m['nama_mapel'];
        $kelasMap = [];
        foreach ($kelasList as $k) $kelasMap[$k['id']] = $k['nama_kelas'];

        $where = ["j.tanggal >= '$filter_tanggal_mulai' AND j.tanggal <= '$filter_tanggal_akhir'"];
        if (!empty($filter_kelas)) $where[] = "j.kelas_id = " . intval($filter_kelas);
        if (!empty($filter_guru))  $where[] = "j.guru_id = " . intval($filter_guru);
        if (!empty($filter_mapel)) $where[] = "j.mapel_id = " . intval($filter_mapel);
        $whereClause = implode(" AND ", $where);

        $jurnalList = $db_siakad->query("SELECT j.* FROM jurnal_guru j WHERE $whereClause ORDER BY j.tanggal DESC, j.id DESC LIMIT 100")->fetchAll();

        foreach ($jurnalList as &$r) {
            $j_id = $r['id'];
            $r['nama_guru'] = $guruMap[$r['guru_id']] ?? ('Guru #' . $r['guru_id']);
            $r['nama_mapel'] = $mapelMap[$r['mapel_id']] ?? ('Mapel #' . $r['mapel_id']);
            $r['nama_kelas'] = $kelasMap[$r['kelas_id']] ?? ('Kelas #' . $r['kelas_id']);

            // Absensi counts
            $stmt_absen = $db_siakad->prepare("SELECT status, COUNT(*) as jml FROM absensi_permapel WHERE jurnal_id = ? GROUP BY status");
            $stmt_absen->execute([$j_id]);
            $counts = $stmt_absen->fetchAll(\PDO::FETCH_KEY_PAIR);

            $r['hadir'] = $counts['Hadir'] ?? 0;
            $r['sakit'] = $counts['Sakit'] ?? 0;
            $r['izin']  = $counts['Izin'] ?? 0;
            $r['alpha'] = ($counts['Alpha'] ?? 0) + ($counts['Alpa'] ?? 0) + ($counts['Bolos'] ?? 0);

            // Absent students
            $stmt_absent = $db_siakad->prepare("SELECT ap.status, ap.siswa_id FROM absensi_permapel ap WHERE ap.jurnal_id = ? AND ap.status != 'Hadir'");
            $stmt_absent->execute([$j_id]);
            $absent_rows = $stmt_absent->fetchAll(\PDO::FETCH_ASSOC);

            // Map student names from core
            $absent_list = [];
            foreach ($absent_rows as $ab) {
                $s_name = $db->query("SELECT nama FROM siswa WHERE id = " . intval($ab['siswa_id']))->fetchColumn();
                $absent_list[] = [
                    'status' => $ab['status'],
                    'siswa_id' => $ab['siswa_id'],
                    'nama' => $s_name ?: ('Siswa #' . $ab['siswa_id'])
                ];
            }
            $r['absent_list'] = $absent_list;
        }
        unset($r);

        require_once __DIR__ . '/../../resources/views/kurikulum/monitoring_jurnal.php';
    }

    public function monitoringNilai() {
        $active_module = 'kurikulum';
        $active_page   = 'kurikulum_monitoring_nilai';
        $db = \App\Core\Database::connect('core');
        $db_siakad = \App\Core\Database::connect('siakad');
        self::ensureMonitoringTables($db);

        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? 2;
        $semester = $institusi['semester'] ?? 'Ganjil';

        $kelas_id = $_GET['kelas_id'] ?? null;
        $guru_id = $_GET['guru_id'] ?? null;

        $kelas_list = $db->query("SELECT * FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll(\PDO::FETCH_ASSOC);
        $guru_list = $db->query("SELECT * FROM guru ORDER BY nama ASC")->fetchAll(\PDO::FETCH_ASSOC);
        $guruMap = [];
        foreach ($guru_list as $g) $guruMap[$g['id']] = $g['nama'];
        $kelasMap = [];
        foreach ($kelas_list as $k) $kelasMap[$k['id']] = $k['nama_kelas'];

        $mapelList = [];
        try {
            $siakadMapels = $db_siakad->query("SELECT id, nama_mapel FROM mapel")->fetchAll();
            foreach ($siakadMapels as $sm) $mapelList[$sm['id']] = $sm['nama_mapel'];
        } catch (\Exception $e) {}

        $monitoring_data = [];
        if ($kelas_id || $guru_id) {
            $query = "SELECT DISTINCT kelas_id, mata_pelajaran_id as mapel_id, guru_id 
                      FROM jadwal_pelajaran WHERE 1=1 ";
            $params = [];
            if ($kelas_id) {
                $query .= " AND kelas_id = ?";
                $params[] = $kelas_id;
            }
            if ($guru_id) {
                $query .= " AND guru_id = ?";
                $params[] = $guru_id;
            }
            $query .= " ORDER BY kelas_id ASC";
            $stmt_j = $db_siakad->prepare($query);
            $stmt_j->execute($params);
            $jadwals = $stmt_j->fetchAll(\PDO::FETCH_ASSOC);

            // Total active students per class in core
            $kelas_totals = [];
            $stmt_s = $db->query("SELECT kelas_id, COUNT(*) as total FROM siswa WHERE status = 'Aktif' GROUP BY kelas_id");
            foreach ($stmt_s->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $kelas_totals[$row['kelas_id']] = $row['total'];
            }

            // Evaluations from unified view in core
            $stmt_n = $db->prepare("
                SELECT kelas_id, mapel_id, jenis_evaluasi, COUNT(DISTINCT siswa_id) as total_dinilai, MAX(materi) as materi
                FROM view_nilai_harian_unified
                WHERE (tahun_ajaran_id = ? OR tahun_ajaran_id IS NULL)
                GROUP BY kelas_id, mapel_id, jenis_evaluasi
            ");
            $stmt_n->execute([$tahun_ajaran_id]);
            $nilai_raw = $stmt_n->fetchAll(\PDO::FETCH_ASSOC);

            $nilai_grouped = [];
            foreach ($nilai_raw as $nr) {
                $nilai_grouped[$nr['kelas_id']][$nr['mapel_id']][$nr['jenis_evaluasi']] = [
                    'total_dinilai' => $nr['total_dinilai'],
                    'materi' => $nr['materi']
                ];
            }

            foreach ($jadwals as $j) {
                $k_id = $j['kelas_id'];
                $m_id = $j['mapel_id'];
                $total_siswa = $kelas_totals[$k_id] ?? 0;

                $evaluasi_list = [];
                if (isset($nilai_grouped[$k_id][$m_id])) {
                    foreach ($nilai_grouped[$k_id][$m_id] as $jenis => $data) {
                        $evaluasi_list[] = [
                            'jenis' => $jenis,
                            'total_dinilai' => $data['total_dinilai'],
                            'materi' => $data['materi']
                        ];
                    }
                }

                $monitoring_data[] = [
                    'kelas_id' => $k_id,
                    'mapel_id' => $m_id,
                    'kelas' => $kelasMap[$k_id] ?? ('Kelas #' . $k_id),
                    'mapel' => $mapelList[$m_id] ?? ('Mapel #' . $m_id),
                    'guru' => $guruMap[$j['guru_id']] ?? ('Guru #' . $j['guru_id']),
                    'evaluasi' => $evaluasi_list,
                    'total_siswa' => $total_siswa
                ];
            }
        }

        require_once __DIR__ . '/../../resources/views/kurikulum/monitoring_nilai.php';
    }

    public function simpanBobotNilai() {
        $db = \App\Core\Database::connect('core');
        $bobot_harian = isset($_POST['bobot_harian']) ? intval($_POST['bobot_harian']) : 50;
        $bobot_pas = isset($_POST['bobot_pas']) ? intval($_POST['bobot_pas']) : 50;
        $stmt = $db->prepare("UPDATE institusi SET bobot_harian = ?, bobot_pas = ?");
        $stmt->execute([$bobot_harian, $bobot_pas]);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Bobot berhasil disimpan']);
        exit;
    }

    public function monitoringNilaiDetail() {
        $active_module = 'kurikulum';
        $active_page   = 'kurikulum_monitoring_nilai';
        $db = \App\Core\Database::connect('core');
        $db_siakad = \App\Core\Database::connect('siakad');
        self::ensureMonitoringTables($db);

        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? 2;
        $semester = $institusi['semester'] ?? 'Ganjil';

        $kelas_id = (int)($_GET['kelas_id'] ?? 0);
        $mapel_id = (int)($_GET['mapel_id'] ?? 0);

        if (!$kelas_id || !$mapel_id) {
            die("Kelas dan Mapel harus dipilih.");
        }

        // Kelas
        $stmt_k = $db->prepare("SELECT * FROM kelas WHERE id = ?");
        $stmt_k->execute([$kelas_id]);
        $kelas = $stmt_k->fetch(\PDO::FETCH_ASSOC);

        // Wali Kelas from guru_tugas
        $stmt_wali = $db->prepare("SELECT g.nama FROM guru_tugas gt JOIN guru g ON gt.guru_id = g.id WHERE gt.tugas = 'Wali Kelas' AND gt.keterangan LIKE ? LIMIT 1");
        $stmt_wali->execute(['%' . ($kelas['nama_kelas'] ?? '') . '%']);
        $wali = $stmt_wali->fetchColumn();
        $kelas['wali_kelas'] = $wali ?: '-';

        // Mapel & Guru Pengampu
        $stmt_j = $db_siakad->prepare("SELECT guru_id FROM jadwal_pelajaran WHERE kelas_id = ? AND mata_pelajaran_id = ? LIMIT 1");
        $stmt_j->execute([$kelas_id, $mapel_id]);
        $guru_id = $stmt_j->fetchColumn();
        $guru_nama = $guru_id ? $db->query("SELECT nama FROM guru WHERE id = " . intval($guru_id))->fetchColumn() : '-';

        $mapel_nama = $db_siakad->query("SELECT nama_mapel FROM mapel WHERE id = " . $mapel_id)->fetchColumn();
        if (!$mapel_nama) $mapel_nama = $db->query("SELECT nama_mapel FROM mapel WHERE id = " . $mapel_id)->fetchColumn();

        $mapel_info = [
            'nama_mapel' => $mapel_nama ?: ('Mapel #' . $mapel_id),
            'guru_pengampu' => $guru_nama ?: '-'
        ];

        // KKM
        $tingkat = (int)($kelas['tingkat'] ?? 10);
        $stmt_kkm = $db->prepare("SELECT nilai_kkm FROM kurikulum_kkm WHERE (tahun_ajaran_id = ? OR tahun_ajaran_id IS NULL) AND mapel_id = ? AND tingkat = ?");
        $stmt_kkm->execute([$tahun_ajaran_id, $mapel_id, $tingkat]);
        $kkm = $stmt_kkm->fetchColumn() ?: 75;

        // Active students
        $stmt_s = $db->prepare("SELECT id, nis, nama FROM siswa WHERE kelas_id = ? AND status = 'Aktif' ORDER BY nama ASC");
        $stmt_s->execute([$kelas_id]);
        $siswa_list = $stmt_s->fetchAll(\PDO::FETCH_ASSOC);

        // Evaluasi list from view_nilai_harian_unified
        $stmt_ev = $db->prepare("
            SELECT jenis_evaluasi, MAX(materi) as materi 
            FROM view_nilai_harian_unified 
            WHERE kelas_id = ? AND mapel_id = ? AND (tahun_ajaran_id = ? OR tahun_ajaran_id IS NULL)
            GROUP BY jenis_evaluasi ORDER BY jenis_evaluasi ASC
        ");
        $stmt_ev->execute([$kelas_id, $mapel_id, $tahun_ajaran_id]);
        $ph_raw = $stmt_ev->fetchAll(\PDO::FETCH_ASSOC);

        $ph_list = [];
        $ph_materi = [];
        foreach ($ph_raw as $ph) {
            $ph_list[] = $ph['jenis_evaluasi'];
            if (!empty($ph['materi'])) $ph_materi[$ph['jenis_evaluasi']] = $ph['materi'];
        }
        if (empty($ph_list)) {
            $ph_list = ['PH 1'];
        }

        // Grades grouped
        $stmt_val = $db->prepare("
            SELECT siswa_id, jenis_evaluasi, nilai 
            FROM view_nilai_harian_unified 
            WHERE kelas_id = ? AND mapel_id = ? AND (tahun_ajaran_id = ? OR tahun_ajaran_id IS NULL)
        ");
        $stmt_val->execute([$kelas_id, $mapel_id, $tahun_ajaran_id]);
        $nilai_grouped = [];
        foreach ($stmt_val->fetchAll(\PDO::FETCH_ASSOC) as $n) {
            $nilai_grouped[$n['siswa_id']][$n['jenis_evaluasi']] = $n['nilai'];
        }

        require_once __DIR__ . '/../../resources/views/kurikulum/monitoring_nilai_detail.php';
    }

    public function monitoringNilaiCetak() {
        $db = \App\Core\Database::connect('core');
        $db_siakad = \App\Core\Database::connect('siakad');
        self::ensureMonitoringTables($db);

        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? 2;
        $semester = $institusi['semester'] ?? 'Ganjil';

        $kelas_id = (int)($_GET['kelas_id'] ?? 0);
        $mapel_id = (int)($_GET['mapel_id'] ?? 0);

        if (!$kelas_id || !$mapel_id) die("Kelas dan Mapel harus dipilih.");

        $stmt_k = $db->prepare("SELECT * FROM kelas WHERE id = ?");
        $stmt_k->execute([$kelas_id]);
        $kelas = $stmt_k->fetch(\PDO::FETCH_ASSOC);

        $stmt_wali = $db->prepare("SELECT g.nama FROM guru_tugas gt JOIN guru g ON gt.guru_id = g.id WHERE gt.tugas = 'Wali Kelas' AND gt.keterangan LIKE ? LIMIT 1");
        $stmt_wali->execute(['%' . ($kelas['nama_kelas'] ?? '') . '%']);
        $wali = $stmt_wali->fetchColumn();
        $kelas['wali_kelas'] = $wali ?: '-';

        $stmt_j = $db_siakad->prepare("SELECT guru_id FROM jadwal_pelajaran WHERE kelas_id = ? AND mata_pelajaran_id = ? LIMIT 1");
        $stmt_j->execute([$kelas_id, $mapel_id]);
        $guru_id = $stmt_j->fetchColumn();
        $guru_nama = $guru_id ? $db->query("SELECT nama FROM guru WHERE id = " . intval($guru_id))->fetchColumn() : '-';

        $mapel_nama = $db_siakad->query("SELECT nama_mapel FROM mapel WHERE id = " . $mapel_id)->fetchColumn();
        if (!$mapel_nama) $mapel_nama = $db->query("SELECT nama_mapel FROM mapel WHERE id = " . $mapel_id)->fetchColumn();

        $mapel_info = [
            'nama_mapel' => $mapel_nama ?: ('Mapel #' . $mapel_id),
            'guru_pengampu' => $guru_nama ?: '-'
        ];

        $tingkat = (int)($kelas['tingkat'] ?? 10);
        $kkm = $db->query("SELECT nilai_kkm FROM kurikulum_kkm WHERE mapel_id = $mapel_id AND tingkat = $tingkat")->fetchColumn() ?: 75;

        $stmt_s = $db->prepare("SELECT id, nis, nama, jenis_kelamin FROM siswa WHERE kelas_id = ? AND status = 'Aktif' ORDER BY nama ASC");
        $stmt_s->execute([$kelas_id]);
        $siswa_list = $stmt_s->fetchAll(\PDO::FETCH_ASSOC);

        $stmt_ev = $db->prepare("SELECT jenis_evaluasi, MAX(materi) as materi FROM view_nilai_harian_unified WHERE kelas_id = ? AND mapel_id = ? GROUP BY jenis_evaluasi ORDER BY jenis_evaluasi ASC");
        $stmt_ev->execute([$kelas_id, $mapel_id]);
        $ph_raw = $stmt_ev->fetchAll(\PDO::FETCH_ASSOC);
        $ph_list = [];
        $ph_materi = [];
        foreach ($ph_raw as $ph) {
            $ph_list[] = $ph['jenis_evaluasi'];
            if (!empty($ph['materi'])) $ph_materi[$ph['jenis_evaluasi']] = $ph['materi'];
        }
        if (empty($ph_list)) $ph_list = ['PH 1'];

        $stmt_val = $db->prepare("SELECT siswa_id, jenis_evaluasi, nilai FROM view_nilai_harian_unified WHERE kelas_id = ? AND mapel_id = ?");
        $stmt_val->execute([$kelas_id, $mapel_id]);
        $nilai_grouped = [];
        foreach ($stmt_val->fetchAll(\PDO::FETCH_ASSOC) as $n) {
            $nilai_grouped[$n['siswa_id']][$n['jenis_evaluasi']] = $n['nilai'];
        }

        require_once __DIR__ . '/../../resources/views/kurikulum/monitoring_nilai_cetak.php';
    }

    public function monitoringNilaiGlobal() {
        $db = \App\Core\Database::connect('core');
        $db_siakad = \App\Core\Database::connect('siakad');
        self::ensureMonitoringTables($db);

        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? 2;
        $semester = $institusi['semester'] ?? 'Ganjil';

        $kelas_list = $db->query("SELECT * FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll(\PDO::FETCH_ASSOC);
        $guru_list = $db->query("SELECT * FROM guru ORDER BY nama ASC")->fetchAll(\PDO::FETCH_ASSOC);
        $guruMap = [];
        foreach ($guru_list as $g) $guruMap[$g['id']] = $g['nama'];

        $mapelList = [];
        try {
            $mapelList = $db_siakad->query("SELECT id, nama_mapel FROM mapel ORDER BY nama_mapel ASC")->fetchAll(\PDO::FETCH_KEY_PAIR);
        } catch (\Exception $e) {}

        // Global progress matrix
        $jadwal_raw = $db_siakad->query("
            SELECT DISTINCT kelas_id, mata_pelajaran_id as mapel_id, guru_id 
            FROM jadwal_pelajaran
            ORDER BY kelas_id ASC
        ")->fetchAll(\PDO::FETCH_ASSOC);

        $global_data = [];
        foreach ($jadwal_raw as $j) {
            $k_id = $j['kelas_id'];
            $m_id = $j['mapel_id'];
            $total_siswa = (int)$db->query("SELECT COUNT(*) FROM siswa WHERE kelas_id = $k_id AND status = 'Aktif'")->fetchColumn();
            $dinilai = (int)$db->query("SELECT COUNT(DISTINCT siswa_id) FROM view_nilai_harian_unified WHERE kelas_id = $k_id AND mapel_id = $m_id")->fetchColumn();
            $m_nama = $mapelList[$m_id] ?? ('Mapel #' . $m_id);
            $k_nama = $db->query("SELECT nama_kelas FROM kelas WHERE id = $k_id")->fetchColumn() ?: ('Kelas #' . $k_id);

            $global_data[] = [
                'kelas' => $k_nama,
                'mapel' => $m_nama,
                'guru' => $guruMap[$j['guru_id']] ?? '-',
                'total_siswa' => $total_siswa,
                'total_dinilai' => $dinilai,
                'persen' => $total_siswa > 0 ? min(100, round(($dinilai / $total_siswa) * 100)) : 0
            ];
        }

        require_once __DIR__ . '/../../resources/views/kurikulum/monitoring_nilai_global.php';
    }

    public static function getKelayakanPasData($db, $tahun_ajaran_id, $semester, $kelas_id) {
        if (!$kelas_id) return null;
        self::ensureMonitoringTables($db);
        $db_siakad = \App\Core\Database::connect('siakad');

        // Kelas
        $stmt_k = $db->prepare("SELECT * FROM kelas WHERE id = ?");
        $stmt_k->execute([$kelas_id]);
        $kelas = $stmt_k->fetch(\PDO::FETCH_ASSOC);
        if (!$kelas) return null;

        // Wali Kelas
        $stmt_wali = $db->prepare("SELECT g.nama FROM guru_tugas gt JOIN guru g ON gt.guru_id = g.id WHERE gt.tugas = 'Wali Kelas' AND gt.keterangan LIKE ? LIMIT 1");
        $stmt_wali->execute(['%' . ($kelas['nama_kelas'] ?? '') . '%']);
        $kelas['wali_kelas'] = $stmt_wali->fetchColumn() ?: '-';

        // Siswa aktif
        $stmt_s = $db->prepare("SELECT id, nis, nama, jenis_kelamin FROM siswa WHERE kelas_id = ? AND status = 'Aktif' ORDER BY nama ASC");
        $stmt_s->execute([$kelas_id]);
        $siswa_list = $stmt_s->fetchAll(\PDO::FETCH_ASSOC);

        // Mapel list from jadwal
        $stmt_m = $db_siakad->prepare("
            SELECT DISTINCT j.mata_pelajaran_id as mapel_id, j.guru_id
            FROM jadwal_pelajaran j
            WHERE j.kelas_id = ?
        ");
        $stmt_m->execute([$kelas_id]);
        $jadwal_mapels = $stmt_m->fetchAll(\PDO::FETCH_ASSOC);

        $mapelList = [];
        try {
            $mapelList = $db_siakad->query("SELECT id, nama_mapel FROM mapel")->fetchAll(\PDO::FETCH_KEY_PAIR);
        } catch (\Exception $e) {}

        $mapel_list = [];
        foreach ($jadwal_mapels as $jm) {
            $m_id = $jm['mapel_id'];
            $g_nama = $db->query("SELECT nama FROM guru WHERE id = " . intval($jm['guru_id']))->fetchColumn();
            $mapel_list[] = [
                'mapel_id' => $m_id,
                'nama_mapel' => $mapelList[$m_id] ?? ('Mapel #' . $m_id),
                'nama_guru' => $g_nama ?: '-'
            ];
        }

        // Target evaluasi per mapel
        $target_evaluasi = [];
        foreach ($mapel_list as $mp) {
            $m_id = $mp['mapel_id'];
            $stmt_ev = $db->prepare("SELECT DISTINCT jenis_evaluasi FROM view_nilai_harian_unified WHERE kelas_id = ? AND mapel_id = ?");
            $stmt_ev->execute([$kelas_id, $m_id]);
            $evals = $stmt_ev->fetchAll(\PDO::FETCH_COLUMN) ?: [];
            if (empty($evals)) {
                $evals = ['PH 1', 'PTS'];
            } else {
                if (!in_array('PTS', $evals)) $evals[] = 'PTS';
            }
            $target_evaluasi[$m_id] = $evals;
        }

        // Grades map
        $stmt_n = $db->prepare("SELECT siswa_id, mapel_id, jenis_evaluasi, nilai FROM view_nilai_harian_unified WHERE kelas_id = ?");
        $stmt_n->execute([$kelas_id]);
        $nilai_map = [];
        foreach ($stmt_n->fetchAll(\PDO::FETCH_ASSOC) as $n) {
            $nilai_map[$n['siswa_id']][$n['mapel_id']][$n['jenis_evaluasi']] = $n['nilai'];
        }

        $siswa_data = [];
        $stat = [
            'total_siswa' => count($siswa_list),
            'layak' => 0,
            'tidak_layak' => 0,
            'total_tagihan_global' => 0,
            'total_terisi_global' => 0
        ];

        foreach ($siswa_list as $s) {
            $s_id = $s['id'];
            $total_tagihan = 0;
            $total_terisi = 0;
            $tagihan_kosong = [];
            $rincian_mapel = [];

            foreach ($mapel_list as $mp) {
                $m_id = $mp['mapel_id'];
                $evals = $target_evaluasi[$m_id] ?? ['PH 1', 'PTS'];

                $items = [];
                foreach ($evals as $ev) {
                    $total_tagihan++;
                    $val = $nilai_map[$s_id][$m_id][$ev] ?? null;
                    $is_filled = ($val !== null && $val !== '' && is_numeric($val));

                    if ($is_filled) {
                        $total_terisi++;
                        $items[] = ['eval' => $ev, 'terisi' => true, 'nilai' => $val];
                    } else {
                        $items[] = ['eval' => $ev, 'terisi' => false, 'nilai' => null];
                        $tagihan_kosong[] = [
                            'mapel_id' => $m_id,
                            'mapel' => $mp['nama_mapel'],
                            'guru' => $mp['nama_guru'],
                            'eval' => $ev,
                            'jenis' => $ev
                        ];
                    }
                }

                $rincian_mapel[] = [
                    'mapel_id' => $m_id,
                    'mapel' => $mp['nama_mapel'],
                    'guru' => $mp['nama_guru'],
                    'items' => $items
                ];
            }

            $persen = $total_tagihan > 0 ? round(($total_terisi / $total_tagihan) * 100) : 0;
            $is_layak = ($total_tagihan > 0 && $total_terisi >= $total_tagihan);

            if ($is_layak) {
                $stat['layak']++;
            } else {
                $stat['tidak_layak']++;
            }
            $stat['total_tagihan_global'] += $total_tagihan;
            $stat['total_terisi_global'] += $total_terisi;

            $siswa_data[] = [
                'id' => $s_id,
                'nis' => $s['nis'],
                'nama' => $s['nama'],
                'jk' => $s['jenis_kelamin'],
                'total_tagihan' => $total_tagihan,
                'total_terisi' => $total_terisi,
                'total_kosong' => count($tagihan_kosong),
                'persen' => $persen,
                'is_layak' => $is_layak,
                'tagihan_kosong' => $tagihan_kosong,
                'rincian_mapel' => $rincian_mapel
            ];
        }

        $stat['persen_kesiapan_kelas'] = ($stat['total_siswa'] > 0) ? round(($stat['layak'] / $stat['total_siswa']) * 100) : 0;

        return [
            'kelas' => $kelas,
            'mapel_list' => $mapel_list,
            'target_evaluasi' => $target_evaluasi,
            'siswa_data' => $siswa_data,
            'stat' => $stat
        ];
    }

    public function syaratPas() {
        $active_module = 'kurikulum';
        $active_page   = 'kurikulum_syarat_pas';
        $db = \App\Core\Database::connect('core');

        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? 2;
        $semester = $institusi['semester'] ?? 'Ganjil';

        $kelas_list = $db->query("SELECT * FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll(\PDO::FETCH_ASSOC);
        $kelas_id = $_GET['kelas_id'] ?? ($kelas_list[0]['id'] ?? 1);
        $filter_status = $_GET['status'] ?? 'all';
        $search = $_GET['q'] ?? '';

        $pasData = self::getKelayakanPasData($db, $tahun_ajaran_id, $semester, $kelas_id);
        $display_siswa = $pasData['siswa_data'] ?? [];
        if ($filter_status === 'layak') {
            $display_siswa = array_values(array_filter($display_siswa, fn($s) => $s['is_layak']));
        } elseif ($filter_status === 'tidak_layak') {
            $display_siswa = array_values(array_filter($display_siswa, fn($s) => !$s['is_layak']));
        }
        if (!empty($search)) {
            $display_siswa = array_values(array_filter($display_siswa, fn($s) => stripos($s['nama'], $search) !== false || stripos($s['nis'], $search) !== false));
        }

        require_once __DIR__ . '/../../resources/views/kurikulum/syarat_pas.php';
    }

    public function syaratPasCetak() {
        $db = \App\Core\Database::connect('core');
        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? 2;
        $semester = $institusi['semester'] ?? 'Ganjil';

        $kelas_id = (int)($_GET['kelas_id'] ?? 0);
        $pasData = self::getKelayakanPasData($db, $tahun_ajaran_id, $semester, $kelas_id);
        if (!$pasData) die("Data kelas tidak valid.");

        require_once __DIR__ . '/../../resources/views/kurikulum/syarat_pas_cetak.php';
    }

    public function syaratPasCetakTagihan() {
        $db = \App\Core\Database::connect('core');
        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? 2;
        $semester = $institusi['semester'] ?? 'Ganjil';

        $kelas_id = (int)($_GET['kelas_id'] ?? 0);
        $pasData = self::getKelayakanPasData($db, $tahun_ajaran_id, $semester, $kelas_id);
        if (!$pasData) die("Data kelas tidak valid.");

        $filter_siswa_id = isset($_GET['siswa_id']) ? (int)$_GET['siswa_id'] : null;

        require_once __DIR__ . '/../../resources/views/kurikulum/syarat_pas_cetak_tagihan.php';
    }

    public function monitoringKkm() {
        $active_module = 'kurikulum';
        $active_page   = 'kurikulum_kkm';
        $db = \App\Core\Database::connect('core');
        $db_siakad = \App\Core\Database::connect('siakad');
        self::ensureMonitoringTables($db);

        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? 2;

        $mapels = [];
        try {
            $mapels = $db_siakad->query("SELECT id, nama_mapel, kelompok FROM mapel ORDER BY kelompok ASC, nama_mapel ASC")->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}
        if (empty($mapels)) {
            $mapels = $db->query("SELECT id, nama_mapel, 'Umum' as kelompok FROM mapel ORDER BY nama_mapel ASC")->fetchAll(\PDO::FETCH_ASSOC);
        }

        $stmt_kkm = $db->prepare("SELECT mapel_id, tingkat, nilai_kkm FROM kurikulum_kkm WHERE (tahun_ajaran_id = ? OR tahun_ajaran_id IS NULL)");
        $stmt_kkm->execute([$tahun_ajaran_id]);
        $kkm_raw = $stmt_kkm->fetchAll(\PDO::FETCH_ASSOC);

        $kkm_data = [];
        foreach ($kkm_raw as $kr) {
            $kkm_data[$kr['mapel_id']][$kr['tingkat']] = $kr['nilai_kkm'];
        }

        require_once __DIR__ . '/../../resources/views/kurikulum/monitoring_kkm.php';
    }

    public function monitoringKkmSave() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $db = \App\Core\Database::connect('core');
        self::ensureMonitoringTables($db);
        $db_siakad = \App\Core\Database::connect('siakad');

        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? 2;

        $kkm_input = $_POST['kkm'] ?? [];
        $bobot_harian = isset($_POST['bobot_harian']) ? intval($_POST['bobot_harian']) : 50;
        $bobot_pas = isset($_POST['bobot_pas']) ? intval($_POST['bobot_pas']) : 50;

        try {
            $db->beginTransaction();

            $stmt_bobot = $db->prepare("UPDATE institusi SET bobot_harian = ?, bobot_pas = ?");
            $stmt_bobot->execute([$bobot_harian, $bobot_pas]);

            $stmt = $db->prepare("
                INSERT INTO kurikulum_kkm (tahun_ajaran_id, mapel_id, tingkat, nilai_kkm) 
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE nilai_kkm = VALUES(nilai_kkm)
            ");

            foreach ($kkm_input as $mapel_id => $tingkat_data) {
                foreach ($tingkat_data as $tingkat => $nilai_kkm) {
                    if ($nilai_kkm !== '' && is_numeric($nilai_kkm)) {
                        $stmt->execute([$tahun_ajaran_id, $mapel_id, $tingkat, $nilai_kkm]);
                    }
                }
            }

            $db->commit();
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Data KKM & Bobot berhasil disimpan']);
        } catch (\Exception $e) {
            $db->rollBack();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
        exit;
    }

    public function rekapKetuntasan() {
        $active_module = 'kurikulum';
        $active_page   = 'kurikulum_rekap_ketuntasan';
        $db = \App\Core\Database::connect('core');
        $db_siakad = \App\Core\Database::connect('siakad');
        self::ensureMonitoringTables($db);

        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();
        $tahun_ajaran_id = $institusi['tahun_ajaran_id'] ?? 2;

        $guru_list = $db->query("SELECT id, nama FROM guru ORDER BY nama ASC")->fetchAll(\PDO::FETCH_ASSOC);
        $kelas_list = $db->query("SELECT id, nama_kelas, tingkat FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll(\PDO::FETCH_ASSOC);

        $mapel_list = [];
        try {
            $mapel_list = $db_siakad->query("SELECT id, nama_mapel FROM mapel ORDER BY nama_mapel ASC")->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {}

        $guru_id = $_GET['guru_id'] ?? null;
        $kelas_id = $_GET['kelas_id'] ?? null;
        $mapel_id = $_GET['mapel_id'] ?? null;
        $jenis_evaluasi = $_GET['jenis_evaluasi'] ?? null;

        $kkm_nilai = null;
        $summary = ['total' => 0, 'tuntas' => 0, 'remedial' => 0, 'persentase' => 0];
        $nilai_data = [];

        if ($kelas_id && $mapel_id && $jenis_evaluasi) {
            $kls = $db->query("SELECT tingkat FROM kelas WHERE id = " . intval($kelas_id))->fetch();
            $tingkat = (int)($kls['tingkat'] ?? 10);

            $stmt_kkm = $db->prepare("SELECT nilai_kkm FROM kurikulum_kkm WHERE mapel_id = ? AND tingkat = ?");
            $stmt_kkm->execute([$mapel_id, $tingkat]);
            $kkm_nilai = $stmt_kkm->fetchColumn() ?: 75;

            $stmt_s = $db->prepare("SELECT id, nis, nama FROM siswa WHERE kelas_id = ? AND status = 'Aktif' ORDER BY nama ASC");
            $stmt_s->execute([$kelas_id]);
            $siswa = $stmt_s->fetchAll(\PDO::FETCH_ASSOC);

            $stmt_n = $db->prepare("SELECT siswa_id, nilai FROM view_nilai_harian_unified WHERE kelas_id = ? AND mapel_id = ? AND jenis_evaluasi = ?");
            $stmt_n->execute([$kelas_id, $mapel_id, $jenis_evaluasi]);
            $scores = $stmt_n->fetchAll(\PDO::FETCH_KEY_PAIR);

            foreach ($siswa as $s) {
                $val = $scores[$s['id']] ?? null;
                $is_tuntas = ($val !== null) ? ((float)$val >= (float)$kkm_nilai) : null;
                if ($val !== null) {
                    $summary['total']++;
                    if ($is_tuntas) $summary['tuntas']++;
                    else $summary['remedial']++;
                }
                $nilai_data[] = [
                    'id' => $s['id'],
                    'nis' => $s['nis'],
                    'nama' => $s['nama'],
                    'nilai' => $val,
                    'is_tuntas' => $is_tuntas
                ];
            }
            $summary['persentase'] = $summary['total'] > 0 ? round(($summary['tuntas'] / $summary['total']) * 100) : 0;
        }

        require_once __DIR__ . '/../../resources/views/kurikulum/rekap_ketuntasan.php';
    }

    public function apiJenisEvaluasi() {
        $db = \App\Core\Database::connect('core');
        $kelas_id = $_GET['kelas_id'] ?? 0;
        $mapel_id = $_GET['mapel_id'] ?? 0;

        $evals = [];
        if ($kelas_id && $mapel_id) {
            $stmt = $db->prepare("SELECT DISTINCT jenis_evaluasi FROM view_nilai_harian_unified WHERE kelas_id = ? AND mapel_id = ? ORDER BY jenis_evaluasi ASC");
            $stmt->execute([$kelas_id, $mapel_id]);
            $evals = $stmt->fetchAll(\PDO::FETCH_COLUMN) ?: [];
        }
        if (empty($evals)) {
            $evals = ['PH 1', 'PH 2', 'PTS', 'PAS'];
        }

        header('Content-Type: application/json');
        echo json_encode(['evaluasi' => $evals]);
        exit;
    }
}
