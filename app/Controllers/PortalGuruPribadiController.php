<?php

namespace App\Controllers;

use App\Core\Database;

class PortalGuruPribadiController {

    private function getGuruInfo() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
            redirect('/login');
            exit;
        }

        $db_core = Database::connect('core');
        $guru = $db_core->query("SELECT id, nama, nip, foto FROM guru WHERE user_id = ".$_SESSION['user_id'])->fetch();
        
        if (!$guru) {
            echo "Akses ditolak. Anda tidak terdaftar sebagai Guru.";
            exit;
        }
        return $guru;
    }

    public function jurnal() {
        $guru = $this->getGuruInfo();
        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

        // Ambil Data Kelas & Mapel yang HANYA diampu oleh guru ini
        // (berdasarkan tabel mengajar di siakad_db)
        $kelasList = [];
        $mapelList = [];
        try {
            // Kelas yang diajarkan guru ini
            $stmt_k = $db_siakad->prepare("
                SELECT DISTINCT k.id, k.nama_kelas 
                FROM mengajar m
                JOIN kelas k ON k.id = m.kelas_id
                WHERE m.guru_id = ?
                ORDER BY k.tingkat, k.nama_kelas
            ");
            $stmt_k->execute([$guru['id']]);
            $kelasList = $stmt_k->fetchAll();

            // Mapel yang diajarkan guru ini
            $stmt_m = $db_siakad->prepare("
                SELECT DISTINCT mp.id, mp.nama_mapel
                FROM mengajar m
                JOIN mata_pelajaran mp ON mp.id = m.mapel_id
                WHERE m.guru_id = ?
                ORDER BY mp.nama_mapel
            ");
            $stmt_m->execute([$guru['id']]);
            $mapelList = $stmt_m->fetchAll();
        } catch (\Exception $e) {
            // fallback ke semua kelas/mapel jika tabel mengajar belum ada
            $kelasList = $db_core->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat, nama_kelas")->fetchAll();
            try {
                $mapelList = $db_siakad->query("SELECT id, nama_mapel FROM mata_pelajaran ORDER BY nama_mapel")->fetchAll();
            } catch (\Exception $e2) {
                $mapelList = $db_siakad->query("SELECT id, nama_mapel FROM mapel ORDER BY nama_mapel")->fetchAll();
            }
        }

        // Filter riwayat jurnal
        $filter_month = isset($_GET['m']) ? $_GET['m'] : date('m');
        $filter_year = isset($_GET['y']) ? $_GET['y'] : date('Y');

        // Ambil riwayat jurnal bulan ini
        $jurnal_riwayat = $db_siakad->query("
            SELECT j.*, 
                   (SELECT COUNT(*) FROM absensi_siswa a WHERE a.tanggal = j.tanggal AND a.kelas_id = j.kelas_id) as total_absen
            FROM jurnal_guru j 
            WHERE j.guru_id = ".$guru['id']." 
            AND MONTH(j.tanggal) = '$filter_month' AND YEAR(j.tanggal) = '$filter_year' 
            ORDER BY j.tanggal DESC, j.id DESC
        ")->fetchAll();

        // Join nama kelas dan mapel (dilakukan di PHP agar tidak query cross-db di mysql)
        $kelasMap = [];
        foreach($kelasList as $k) $kelasMap[$k['id']] = $k['nama_kelas'];
        $mapelMap = [];
        foreach($mapelList as $m) $mapelMap[$m['id']] = $m['nama_mapel'];

        foreach($jurnal_riwayat as &$j) {
            $j['nama_kelas'] = isset($kelasMap[$j['kelas_id']]) ? $kelasMap[$j['kelas_id']] : 'Kelas ID '.$j['kelas_id'];
            $j['nama_mapel'] = isset($mapelMap[$j['mapel_id']]) ? $mapelMap[$j['mapel_id']] : 'Mapel ID '.$j['mapel_id'];
        }

        $title = "Jurnal Mengajar Pribadi";
        
        $today = date('Y-m-d');
        $agendaToday = $db_siakad->query("SELECT * FROM kalender_akademik WHERE tanggal_mulai <= '$today' AND tanggal_selesai >= '$today' AND (tipe='libur' OR tipe='kegiatan' OR tipe='pulang_awal') LIMIT 1")->fetch();

        include __DIR__ . '/../../resources/views/portal/guru/jurnal_pribadi.php';
    }

    public function perangkat() {
        $guru = $this->getGuruInfo();
        $db_siakad = Database::connect('siakad');
        $db_core = Database::connect('core');
        
        $mapelList = [];
        try {
            $stmt = $db_siakad->prepare("
                SELECT mp.nama_mapel as nama, k.nama_kelas as kelas, m.kelas_id, m.mapel_id 
                FROM mengajar m
                JOIN kelas k ON k.id = m.kelas_id
                JOIN mata_pelajaran mp ON mp.id = m.mapel_id
                WHERE m.guru_id = ?
                ORDER BY k.tingkat, k.nama_kelas, mp.nama_mapel
            ");
            $stmt->execute([$guru['id']]);
            $rawList = $stmt->fetchAll();
            
            // Required document types
            $requiredDocs = ['Capaian Pembelajaran (CP)', 'Alur Tujuan Pembelajaran (ATP)', 'Modul Ajar', 'KKTP', 'Program Tahunan (Prota)', 'Program Semester (Promes)'];
            
            foreach($rawList as $row) {
                // Count uploaded required docs for this mapel and kelas
                $stmtCount = $db_core->prepare("SELECT COUNT(DISTINCT kategori) FROM guru_perangkat WHERE guru_id = ? AND mapel_nama = ? AND kelas_nama = ? AND kategori IN ('Capaian Pembelajaran (CP)', 'Alur Tujuan Pembelajaran (ATP)', 'Modul Ajar', 'KKTP', 'Program Tahunan (Prota)', 'Program Semester (Promes)')");
                $stmtCount->execute([$guru['id'], $row['nama'], $row['kelas']]);
                $uploadedCount = $stmtCount->fetchColumn();
                $bolong = count($requiredDocs) - $uploadedCount;

                $mapelList[] = [
                    'nama' => $row['nama'],
                    'kelas' => $row['kelas'],
                    'mapel_id' => $row['mapel_id'],
                    'kelas_id' => $row['kelas_id'],
                    'bolong' => max(0, $bolong)
                ];
            }
        } catch (\Exception $e) {
            // Fallback
        }

        $title = "Perangkat Pembelajaran - Portal Guru";
        include __DIR__ . '/../../resources/views/portal/guru/perangkat.php';
    }

    public function perangkatDetail() {
        $guru = $this->getGuruInfo();
        $db_siakad = Database::connect('siakad');
        $db_core = Database::connect('core');
        
        $kelas_id = intval($_GET['kelas'] ?? 0);
        $mapel_id = intval($_GET['mapel'] ?? 0);
        
        if (!$kelas_id || !$mapel_id) {
            header("Location: " . base_url('guru/perangkat'));
            exit;
        }

        // Get mapel list for dropdown
        $mapelList = [];
        $stmtList = $db_siakad->prepare("
            SELECT mp.nama_mapel as nama, k.nama_kelas as kelas, m.kelas_id, m.mapel_id 
            FROM mengajar m
            JOIN kelas k ON k.id = m.kelas_id
            JOIN mata_pelajaran mp ON mp.id = m.mapel_id
            WHERE m.guru_id = ?
            ORDER BY k.tingkat, k.nama_kelas, mp.nama_mapel
        ");
        $stmtList->execute([$guru['id']]);
        $mapelList = $stmtList->fetchAll();

        // Check if mapel/kelas is valid for this guru
        $isValid = false;
        $currentMapel = null;
        foreach($mapelList as $m) {
            if ($m['kelas_id'] == $kelas_id && $m['mapel_id'] == $mapel_id) {
                $isValid = true;
                $currentMapel = $m;
                break;
            }
        }
        
        if (!$isValid) {
            header("Location: " . base_url('guru/perangkat'));
            exit;
        }
        
        // Fetch other classes for the same subject
        $stmtOther = $db_siakad->prepare("
            SELECT DISTINCT k.id as kelas_id, k.nama_kelas as kelas, mp.id as mapel_id, mp.nama_mapel as nama 
            FROM mengajar m 
            JOIN kelas k ON k.id = m.kelas_id 
            JOIN mata_pelajaran mp ON mp.id = m.mapel_id 
            WHERE m.guru_id = ? AND mp.nama_mapel = ? AND k.id != ?
            ORDER BY k.nama_kelas ASC
        ");
        $stmtOther->execute([$guru['id'], $currentMapel['nama'], $kelas_id]);
        $otherClasses = $stmtOther->fetchAll();

        // Fetch documents
        $stmtDocs = $db_core->prepare("SELECT * FROM guru_perangkat WHERE guru_id = ? AND mapel_nama = ? AND kelas_nama = ? ORDER BY created_at DESC");
        $stmtDocs->execute([$guru['id'], $currentMapel['nama'], $currentMapel['kelas']]);
        $documentsRaw = $stmtDocs->fetchAll();

        $documents = [];
        foreach($documentsRaw as $doc) {
            if (!isset($documents[$doc['kategori']])) {
                $documents[$doc['kategori']] = [];
            }
            $documents[$doc['kategori']][] = $doc;
        }

        $requiredDocs = ['Capaian Pembelajaran (CP)', 'Alur Tujuan Pembelajaran (ATP)', 'Modul Ajar', 'KKTP', 'Program Tahunan (Prota)', 'Program Semester (Promes)'];
        $bolong = 0;
        foreach($requiredDocs as $req) {
            if (empty($documents[$req])) $bolong++;
        }

        $title = "Detail Perangkat - Portal Guru";
        include __DIR__ . '/../../resources/views/portal/guru/perangkat_detail.php';
    }

    public function uploadPerangkat() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $guru = $this->getGuruInfo();
            $db_siakad = Database::connect('siakad');
            $db_core = Database::connect('core');
            
            $doc_id = intval($_POST['doc_id'] ?? 0);
            $kelas_id = intval($_POST['kelas_id'] ?? 0);
            $mapel_id = intval($_POST['mapel_id'] ?? 0);
            $kategori = $_POST['jenis_perangkat'] ?? '';
            $nama_berkas = $_POST['judul'] ?? '';
            
            // Validate ownership
            $stmtCheck = $db_siakad->prepare("SELECT id FROM mengajar WHERE guru_id = ? AND kelas_id = ? AND mapel_id = ?");
            $stmtCheck->execute([$guru['id'], $kelas_id, $mapel_id]);
            if (!$stmtCheck->fetchColumn()) {
                header("Location: " . base_url('guru/perangkat'));
                exit;
            }
            
            // Get mapel & kelas nama
            $stmtNames = $db_siakad->prepare("SELECT mp.nama_mapel as nama, k.nama_kelas as kelas FROM kelas k JOIN mata_pelajaran mp ON mp.id = ? WHERE k.id = ?");
            $stmtNames->execute([$mapel_id, $kelas_id]);
            $names = $stmtNames->fetch();
            $mapel_nama = $names['nama'] ?? 'Umum';
            $kelas_nama = $names['kelas'] ?? 'Umum';

            if (isset($_FILES['file_perangkat']) && $_FILES['file_perangkat']['error'] == 0) {
                $ext = pathinfo($_FILES['file_perangkat']['name'], PATHINFO_EXTENSION);
                $allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
                if (in_array(strtolower($ext), $allowed)) {
                    $filename = 'perangkat_' . $guru['id'] . '_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                    $uploadDir = __DIR__ . '/../../public/uploads/guru_perangkat/';
                    
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    if (move_uploaded_file($_FILES['file_perangkat']['tmp_name'], $uploadDir . $filename)) {
                        if ($doc_id > 0) {
                            // Update existing doc
                            $stmtFind = $db_core->prepare("SELECT file_path FROM guru_perangkat WHERE id = ? AND guru_id = ?");
                            $stmtFind->execute([$doc_id, $guru['id']]);
                            $existing = $stmtFind->fetch();
                            if ($existing) {
                                @unlink($uploadDir . $existing['file_path']);
                            }
                            
                            $stmtUpd = $db_core->prepare("UPDATE guru_perangkat SET nama_berkas = ?, file_path = ?, status_review = 'Menunggu' WHERE id = ? AND guru_id = ?");
                            $stmtUpd->execute([$nama_berkas, $filename, $doc_id, $guru['id']]);
                        } else {
                            // Insert new doc
                            $stmtIns = $db_core->prepare("INSERT INTO guru_perangkat (guru_id, kategori, mapel_nama, kelas_nama, nama_berkas, file_path, status_review) VALUES (?, ?, ?, ?, ?, ?, 'Menunggu')");
                            $stmtIns->execute([$guru['id'], $kategori, $mapel_nama, $kelas_nama, $nama_berkas, $filename]);
                            
                            // Handle bulk upload to other classes if any
                            $kelas_lain = $_POST['kelas_lain'] ?? [];
                            if (!empty($kelas_lain)) {
                                foreach($kelas_lain as $kls_id) {
                                    $stmtKls = $db_siakad->prepare("SELECT nama_kelas FROM kelas WHERE id = ?");
                                    $stmtKls->execute([$kls_id]);
                                    $klsNama = $stmtKls->fetchColumn();
                                    
                                    if ($klsNama) {
                                        $new_filename = 'perangkat_' . $guru['id'] . '_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                                        if (copy($uploadDir . $filename, $uploadDir . $new_filename)) {
                                            $stmtIns->execute([$guru['id'], $kategori, $mapel_nama, $klsNama, $nama_berkas, $new_filename]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } else if ($doc_id > 0) {
                // User just updating title, no file uploaded
                $stmtUpd = $db_core->prepare("UPDATE guru_perangkat SET nama_berkas = ? WHERE id = ? AND guru_id = ?");
                $stmtUpd->execute([$nama_berkas, $doc_id, $guru['id']]);
            }
            
            header("Location: " . base_url('guru/perangkat/detail?kelas=' . $kelas_id . '&mapel=' . $mapel_id));
            exit;
        }
    }

    public function copyPerangkat() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $guru = $this->getGuruInfo();
            $db_siakad = Database::connect('siakad');
            $db_core = Database::connect('core');
            
            $doc_id = intval($_POST['doc_id'] ?? 0);
            $kelas_lain = $_POST['kelas_lain'] ?? [];
            
            if ($doc_id > 0 && !empty($kelas_lain)) {
                $stmtDoc = $db_core->prepare("SELECT * FROM guru_perangkat WHERE id = ? AND guru_id = ?");
                $stmtDoc->execute([$doc_id, $guru['id']]);
                $doc = $stmtDoc->fetch();
                
                if ($doc) {
                    $uploadDir = __DIR__ . '/../../public/uploads/guru_perangkat/';
                    $originalFile = $uploadDir . $doc['file_path'];
                    
                    if (file_exists($originalFile)) {
                        foreach($kelas_lain as $kls_id) {
                            $stmtKls = $db_siakad->prepare("SELECT nama_kelas FROM kelas WHERE id = ?");
                            $stmtKls->execute([$kls_id]);
                            $klsNama = $stmtKls->fetchColumn();
                            
                            if ($klsNama) {
                                $ext = pathinfo($doc['file_path'], PATHINFO_EXTENSION);
                                $filename = 'perangkat_' . $guru['id'] . '_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                                
                                if (copy($originalFile, $uploadDir . $filename)) {
                                    $stmtIns = $db_core->prepare("INSERT INTO guru_perangkat (guru_id, kategori, mapel_nama, kelas_nama, nama_berkas, file_path, status_review) VALUES (?, ?, ?, ?, ?, ?, 'Menunggu')");
                                    $stmtIns->execute([$guru['id'], $doc['kategori'], $doc['mapel_nama'], $klsNama, $doc['nama_berkas'], $filename]);
                                }
                            }
                        }
                    }
                    
                    // Redirect back
                    $stmtIds = $db_siakad->prepare("SELECT m.id as mapel_id, k.id as kelas_id FROM mata_pelajaran m JOIN kelas k ON k.nama_kelas = ? WHERE m.nama_mapel = ? LIMIT 1");
                    $stmtIds->execute([$doc['kelas_nama'], $doc['mapel_nama']]);
                    $ids = $stmtIds->fetch();
                    if ($ids) {
                        header("Location: " . base_url('guru/perangkat/detail?kelas=' . $ids['kelas_id'] . '&mapel=' . $ids['mapel_id']));
                        exit;
                    }
                }
            }
            header("Location: " . base_url('guru/perangkat'));
            exit;
        }
    }

    public function deletePerangkat($id) {
        $guru = $this->getGuruInfo();
        $db_siakad = Database::connect('siakad');
        $db_core = Database::connect('core');
        
        $stmtFind = $db_core->prepare("SELECT * FROM guru_perangkat WHERE id = ? AND guru_id = ?");
        $stmtFind->execute([$id, $guru['id']]);
        $doc = $stmtFind->fetch();
        
        if ($doc) {
            $uploadDir = __DIR__ . '/../../public/uploads/guru_perangkat/';
            @unlink($uploadDir . $doc['file_path']);
            
            $stmtDel = $db_core->prepare("DELETE FROM guru_perangkat WHERE id = ?");
            $stmtDel->execute([$id]);
            
            // We need mapel_id and kelas_id for redirection
            $stmtIds = $db_siakad->prepare("SELECT m.id as mapel_id, k.id as kelas_id FROM mata_pelajaran m, kelas k WHERE m.nama_mapel = ? AND k.nama_kelas = ?");
            $stmtIds->execute([$doc['mapel_nama'], $doc['kelas_nama']]);
            $ids = $stmtIds->fetch();
            
            if ($ids) {
                header("Location: " . base_url('guru/perangkat/detail?kelas=' . $ids['kelas_id'] . '&mapel=' . $ids['mapel_id']));
                exit;
            }
        }
        
        header("Location: " . base_url('guru/perangkat'));
        exit;
    }

    public function saveJurnal() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $guru = $this->getGuruInfo();
            $kelas_id = intval($_POST['kelas_id'] ?? 0);
            $mapel_id = intval($_POST['mapel_id'] ?? 0);
            $tanggal = $_POST['tanggal'] ?? '';
            $materi = $_POST['materi'] ?? '';
            $keterangan = $_POST['keterangan'] ?? '';
            
            if ($kelas_id > 0 && $mapel_id > 0 && !empty($tanggal) && !empty($materi)) {
                $db_siakad = Database::connect('siakad');
                
                // Validasi: apakah kelas & mapel ini memang diampu guru ini?
                $cek_izin = $db_siakad->prepare("SELECT id FROM mengajar WHERE guru_id = ? AND kelas_id = ? AND mapel_id = ? LIMIT 1");
                $cek_izin->execute([$guru['id'], $kelas_id, $mapel_id]);
                if (!$cek_izin->fetch()) {
                    redirect("/guru/jurnal?error=" . urlencode('Akses ditolak. Anda tidak mengampu kelas/mapel tersebut.'));
                    exit;
                }
                try {
                    $db_siakad->beginTransaction();
                    
                    // 1. Insert Jurnal
                    $stmt_jurnal = $db_siakad->prepare("INSERT INTO jurnal_guru (guru_id, kelas_id, mapel_id, tanggal, materi, keterangan) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt_jurnal->execute([$guru['id'], $kelas_id, $mapel_id, $tanggal, $materi, $keterangan]);
                    
                    // 2. Insert Absensi Siswa & Absensi Per Mapel
                    if (isset($_POST['absensi']) && is_array($_POST['absensi'])) {
                        $jurnal_id = $db_siakad->lastInsertId();
                        $stmt_absen = $db_siakad->prepare("INSERT INTO absensi_siswa (siswa_id, kelas_id, tanggal, status) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE status = VALUES(status)");
                        $stmt_permapel = $db_siakad->prepare("INSERT INTO absensi_permapel (jurnal_id, guru_id, siswa_id, kelas_id, mapel_id, tanggal, status) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE status = VALUES(status)");
                        foreach ($_POST['absensi'] as $siswa_id => $status_absen) {
                            // Jika guru mengisi Hadir, jangan timpa status harian (biarkan QR yang menentukan Hadir/Belum Scan)
                            // Hanya timpa status harian jika guru mengisi Sakit, Izin, atau Alpa
                            if (strtolower($status_absen) !== 'hadir' && strtolower($status_absen) !== 'h') {
                                $stmt_absen->execute([$siswa_id, $kelas_id, $tanggal, $status_absen]);
                            }
                            
                            try {
                                $stmt_permapel->execute([$jurnal_id, $guru['id'], $siswa_id, $kelas_id, $mapel_id, $tanggal, $status_absen]);
                            } catch (\Exception $exP) {}
                        }
                    }
                    
                    // 3. Catat Absen Guru di absensi_guru (Sebagai penanda bahwa hari ini sudah aktif mengajar)
                    // Cek apakah hari ini sudah ada log masuk
                    $cek_absen = $db_siakad->query("SELECT id FROM absensi_guru WHERE guru_id = ".$guru['id']." AND tanggal = '$tanggal'")->fetch();
                    if (!$cek_absen) {
                        $stmt_guru = $db_siakad->prepare("INSERT INTO absensi_guru (guru_id, tanggal, waktu_masuk, status) VALUES (?, ?, CURRENT_TIME(), 'Hadir')");
                        $stmt_guru->execute([$guru['id'], $tanggal]);
                    }

                    $db_siakad->commit();
                    redirect("/guru/jurnal?success=1");
                    exit;
                } catch (\PDOException $e) {
                    $db_siakad->rollBack();
                    redirect("/guru/jurnal?error=" . urlencode($e->getMessage()));
                    exit;
                }
            } else {
                redirect("/guru/jurnal?error=invalid_data");
                exit;
            }
        }
    }

    public function deleteJurnal($id) {
        $guru = $this->getGuruInfo();
        $db_siakad = Database::connect('siakad');
        
        $jurnal = $db_siakad->query("SELECT * FROM jurnal_guru WHERE id = " . intval($id) . " AND guru_id = " . $guru['id'])->fetch();
        if ($jurnal) {
            $db_siakad->query("DELETE FROM jurnal_guru WHERE id = " . intval($id));
            redirect("/guru/jurnal?success=delete");
        } else {
            redirect("/guru/jurnal?error=not_found");
        }
        exit;
    }

    public function absen() {
        $guru = $this->getGuruInfo();
        $db_siakad = Database::connect('siakad');

        // Filter riwayat absen
        $filter_month = isset($_GET['m']) ? $_GET['m'] : date('m');
        $filter_year = isset($_GET['y']) ? $_GET['y'] : date('Y');

        $riwayat_absen = $db_siakad->query("
            SELECT * FROM absensi_guru 
            WHERE guru_id = ".$guru['id']." 
            AND MONTH(tanggal) = '$filter_month' AND YEAR(tanggal) = '$filter_year' 
            ORDER BY tanggal DESC
        ")->fetchAll();

        // Rekapitulasi
        $rekap = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0];
        foreach($riwayat_absen as $a) {
            if(isset($rekap[$a['status']])) {
                $rekap[$a['status']]++;
            }
        }

        $title = "Riwayat Kehadiran Pribadi";
        
        include __DIR__ . '/../../resources/views/portal/guru/absen_pribadi.php';
    }

    public function getSiswaKelas() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $kelas_id = intval($_GET['kelas_id'] ?? 0);
        if ($kelas_id > 0) {
            $db_core = Database::connect('core');
            $siswa = $db_core->query("SELECT id, nama FROM siswa WHERE kelas_id = $kelas_id ORDER BY nama ASC")->fetchAll();
            echo json_encode($siswa);
        } else {
            echo json_encode([]);
        }
        exit;
    }

}
