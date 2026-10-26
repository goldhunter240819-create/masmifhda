<?php

namespace App\Controllers;

use App\Core\Database;

class PortalGuruEkstraController {

    private function getGuruInfo() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
            redirect('/login');
            exit;
        }

        $db_core = Database::connect('core');
        $guru_id = $db_core->query("SELECT id, nama FROM guru WHERE user_id = ".$_SESSION['user_id'])->fetch();
        
        if (!$guru_id) {
            echo "Akses ditolak. Anda tidak terdaftar sebagai Guru.";
            exit;
        }
        return $guru_id;
    }

    private function verifyEkstra($guru_id, $nama_ekstra, $tahun_ajaran) {
        $db_core = Database::connect('core');
        $stmt = $db_core->prepare("SELECT id FROM guru_tugas WHERE guru_id = ? AND tugas = 'Pembina Ekstrakurikuler' AND keterangan = ? AND tahun_ajaran = ?");
        $stmt->execute([$guru_id, $nama_ekstra, $tahun_ajaran]);
        return $stmt->fetch() !== false;
    }

    public function index() {
        $guru = $this->getGuruInfo();
        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
        
        $db_core = Database::connect('core');
        $stmt = $db_core->prepare("SELECT keterangan as nama_ekstra FROM guru_tugas WHERE guru_id = ? AND tugas = 'Pembina Ekstrakurikuler' AND tahun_ajaran = ?");
        $stmt->execute([$guru['id'], $active_year_name]);
        $ekstraList = $stmt->fetchAll();

        $title_page = "Dashboard Pembina Ekstrakurikuler";
        include __DIR__ . '/../../resources/views/portal/guru/tugas_pembina_ekstra.php';
    }

    public function pengaturan() {
        $guru = $this->getGuruInfo();
        $nama_ekstra = isset($_GET['ekstra']) ? $_GET['ekstra'] : '';
        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
        
        if (!$this->verifyEkstra($guru['id'], $nama_ekstra, $active_year_name)) {
            echo "Akses ditolak. Anda bukan pembina ekstrakurikuler ini.";
            exit;
        }

        $db_siakad = Database::connect('siakad');
        $stmt = $db_siakad->prepare("SELECT * FROM ekstra_program WHERE guru_id = ? AND nama_ekstra = ? AND tahun_ajaran = ?");
        $stmt->execute([$guru['id'], $nama_ekstra, $active_year_name]);
        $program = $stmt->fetch();

        $title_page = "Program Ekstrakurikuler - " . htmlspecialchars($nama_ekstra);
        include __DIR__ . '/../../resources/views/portal/guru/tugas_ekstra_pengaturan.php';
    }

    public function savePengaturan() {
        $guru = $this->getGuruInfo();
        $nama_ekstra = $_POST['nama_ekstra'] ?? '';
        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
        
        if (!$this->verifyEkstra($guru['id'], $nama_ekstra, $active_year_name)) {
            echo "Akses ditolak.";
            exit;
        }

        $db_siakad = Database::connect('siakad');
        $jadwal_hari = $_POST['jadwal_hari'] ?? '';
        $jadwal_waktu = $_POST['jadwal_waktu'] ?? '';
        $target_pencapaian = $_POST['target_pencapaian'] ?? '';
        
        // Handle file upload later if needed.

        $stmt = $db_siakad->prepare("SELECT id FROM ekstra_program WHERE guru_id = ? AND nama_ekstra = ? AND tahun_ajaran = ?");
        $stmt->execute([$guru['id'], $nama_ekstra, $active_year_name]);
        if ($stmt->fetch()) {
            $upd = $db_siakad->prepare("UPDATE ekstra_program SET jadwal_hari = ?, jadwal_waktu = ?, target_pencapaian = ? WHERE guru_id = ? AND nama_ekstra = ? AND tahun_ajaran = ?");
            $upd->execute([$jadwal_hari, $jadwal_waktu, $target_pencapaian, $guru['id'], $nama_ekstra, $active_year_name]);
        } else {
            $ins = $db_siakad->prepare("INSERT INTO ekstra_program (nama_ekstra, guru_id, tahun_ajaran, semester, jadwal_hari, jadwal_waktu, target_pencapaian) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $ins->execute([$nama_ekstra, $guru['id'], $active_year_name, 'Ganjil', $jadwal_hari, $jadwal_waktu, $target_pencapaian]);
        }

        redirect("/guru/tugas_tambahan/pembina_ekstra/pengaturan?ekstra=" . urlencode($nama_ekstra) . "&success=1");
    }

    // Placeholder for other functions
    public function anggota() {
        $guru = $this->getGuruInfo();
        $nama_ekstra = isset($_GET['ekstra']) ? $_GET['ekstra'] : '';
        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
        
        if (!$this->verifyEkstra($guru['id'], $nama_ekstra, $active_year_name)) {
            echo "Akses ditolak.";
            exit;
        }

        $db_siakad = Database::connect('siakad');
        
        // Ambil daftar anggota
        $anggotaList = $db_siakad->query("SELECT ea.*, s.nama, s.nisn, k.nama_kelas 
                                        FROM ekstra_anggota ea 
                                        JOIN aula_db.siswa s ON ea.siswa_id = s.id 
                                        LEFT JOIN aula_db.kelas k ON s.kelas_id = k.id 
                                        WHERE ea.nama_ekstra = ".$db_siakad->quote($nama_ekstra)." 
                                        AND ea.tahun_ajaran = ".$db_siakad->quote($active_year_name)."
                                        ORDER BY k.nama_kelas ASC, s.nama ASC")->fetchAll();

        // Ambil daftar siswa untuk dropdown tambah (hanya yang belum bergabung di ekstra ini)
        $siswaIdsJoined = array_column($anggotaList, 'siswa_id');
        $notIn = count($siswaIdsJoined) > 0 ? "AND s.id NOT IN (" . implode(',', $siswaIdsJoined) . ")" : "";
        $siswaTersedia = $db_siakad->query("SELECT s.id, s.nama, s.nisn, k.nama_kelas 
                                            FROM aula_db.siswa s 
                                            LEFT JOIN aula_db.kelas k ON s.kelas_id = k.id 
                                            WHERE s.status IN ('Aktif') $notIn 
                                            ORDER BY k.nama_kelas ASC, s.nama ASC")->fetchAll();

        $title_page = "Anggota Ekstra - " . htmlspecialchars($nama_ekstra);
        include __DIR__ . '/../../resources/views/portal/guru/tugas_ekstra_anggota.php';
    }

    public function addAnggota() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $guru = $this->getGuruInfo();
            $nama_ekstra = $_POST['nama_ekstra'] ?? '';
            $active_year = \App\Core\AcademicYear::current();
            $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
            
            if (!$this->verifyEkstra($guru['id'], $nama_ekstra, $active_year_name)) {
                echo "Akses ditolak.";
                exit;
            }

            if (!empty($_POST['siswa_ids'])) {
                $db_siakad = Database::connect('siakad');
                $db_siakad->beginTransaction();
                try {
                    $stmt = $db_siakad->prepare("INSERT IGNORE INTO ekstra_anggota (nama_ekstra, siswa_id, tahun_ajaran) VALUES (?, ?, ?)");
                    foreach ($_POST['siswa_ids'] as $siswa_id) {
                        $stmt->execute([$nama_ekstra, $siswa_id, $active_year_name]);
                    }
                    $db_siakad->commit();
                } catch (\Exception $e) {
                    $db_siakad->rollBack();
                }
            }
            redirect("/guru/tugas_tambahan/pembina_ekstra/anggota?ekstra=" . urlencode($nama_ekstra) . "&success=add");
            exit;
        }
    }

    public function deleteAnggota($id) {
        $guru = $this->getGuruInfo();
        $nama_ekstra = isset($_GET['ekstra']) ? $_GET['ekstra'] : '';
        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
        
        if (!$this->verifyEkstra($guru['id'], $nama_ekstra, $active_year_name)) {
            echo "Akses ditolak.";
            exit;
        }

        $db_siakad = Database::connect('siakad');
        $stmt = $db_siakad->prepare("DELETE FROM ekstra_anggota WHERE id = ? AND nama_ekstra = ?");
        $stmt->execute([$id, $nama_ekstra]);
        
        redirect("/guru/tugas_tambahan/pembina_ekstra/anggota?ekstra=" . urlencode($nama_ekstra) . "&success=delete");
        exit;
    }
    public function jurnal() {
        $guru = $this->getGuruInfo();
        $nama_ekstra = isset($_GET['ekstra']) ? $_GET['ekstra'] : '';
        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
        
        if (!$this->verifyEkstra($guru['id'], $nama_ekstra, $active_year_name)) {
            echo "Akses ditolak.";
            exit;
        }

        $db_siakad = \App\Core\Database::connect('siakad');
        
        $jurnals = $db_siakad->query("SELECT * FROM ekstra_jurnal 
                                      WHERE nama_ekstra = ".$db_siakad->quote($nama_ekstra)." 
                                      AND tahun_ajaran = ".$db_siakad->quote($active_year_name)." 
                                      ORDER BY tanggal DESC, id DESC")->fetchAll();

        // Get total attendance for each journal
        foreach($jurnals as &$j) {
            $j['hadir'] = $db_siakad->query("SELECT COUNT(*) as t FROM ekstra_absen WHERE jurnal_id = ".$j['id']." AND status_kehadiran = 'Hadir'")->fetch()['t'];
            $j['sakit'] = $db_siakad->query("SELECT COUNT(*) as t FROM ekstra_absen WHERE jurnal_id = ".$j['id']." AND status_kehadiran = 'Sakit'")->fetch()['t'];
            $j['izin'] = $db_siakad->query("SELECT COUNT(*) as t FROM ekstra_absen WHERE jurnal_id = ".$j['id']." AND status_kehadiran = 'Izin'")->fetch()['t'];
            $j['alpa'] = $db_siakad->query("SELECT COUNT(*) as t FROM ekstra_absen WHERE jurnal_id = ".$j['id']." AND status_kehadiran = 'Alpa'")->fetch()['t'];
        }

        $title_page = "Jurnal Ekstra - " . htmlspecialchars($nama_ekstra);
        ob_start();
        include __DIR__ . '/../../resources/views/portal/guru/tugas_ekstra_jurnal.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public function formJurnal($id = null) {
        $guru = $this->getGuruInfo();
        $nama_ekstra = isset($_GET['ekstra']) ? $_GET['ekstra'] : '';
        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
        
        if (!$this->verifyEkstra($guru['id'], $nama_ekstra, $active_year_name)) {
            echo "Akses ditolak.";
            exit;
        }

        $db_siakad = \App\Core\Database::connect('siakad');
        
        $jurnal = null;
        $absen_map = [];
        if ($id) {
            $stmt = $db_siakad->prepare("SELECT * FROM ekstra_jurnal WHERE id = ? AND nama_ekstra = ?");
            $stmt->execute([$id, $nama_ekstra]);
            $jurnal = $stmt->fetch();
            if (!$jurnal) { echo "Jurnal tidak ditemukan."; exit; }
            
            $absenList = $db_siakad->query("SELECT siswa_id, status_kehadiran FROM ekstra_absen WHERE jurnal_id = $id")->fetchAll();
            foreach($absenList as $ab) {
                $absen_map[$ab['siswa_id']] = $ab['status_kehadiran'];
            }
        }

        // Ambil daftar anggota ekstra (Join dengan core_db.siswa)
        $anggotaList = $db_siakad->query("SELECT ea.siswa_id, s.nama, s.nisn, k.nama_kelas 
                                        FROM ekstra_anggota ea 
                                        JOIN aula_db.siswa s ON ea.siswa_id = s.id 
                                        LEFT JOIN aula_db.kelas k ON s.kelas_id = k.id 
                                        WHERE ea.nama_ekstra = ".$db_siakad->quote($nama_ekstra)." 
                                        AND ea.tahun_ajaran = ".$db_siakad->quote($active_year_name)."
                                        ORDER BY k.nama_kelas ASC, s.nama ASC")->fetchAll();

        $title_page = ($id ? "Edit Jurnal" : "Tambah Jurnal") . " - " . htmlspecialchars($nama_ekstra);
        ob_start();
        include __DIR__ . '/../../resources/views/portal/guru/tugas_ekstra_jurnal_form.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public function saveJurnal() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $guru = $this->getGuruInfo();
            $nama_ekstra = $_POST['nama_ekstra'] ?? '';
            $active_year = \App\Core\AcademicYear::current();
            $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
            
            if (!$this->verifyEkstra($guru['id'], $nama_ekstra, $active_year_name)) {
                echo "Akses ditolak.";
                exit;
            }

            $id = !empty($_POST['id']) ? $_POST['id'] : null;
            $tanggal = $_POST['tanggal'] ?? '';
            $materi = $_POST['materi'] ?? '';
            $absensi = $_POST['absensi'] ?? []; // array(siswa_id => status)
            
            $db_siakad = \App\Core\Database::connect('siakad');
            
            $dokumentasi = null;
            if ($id) {
                $jurnal_old = $db_siakad->query("SELECT dokumentasi FROM ekstra_jurnal WHERE id = " . intval($id))->fetch();
                $dokumentasi = $jurnal_old['dokumentasi'] ?? null;
            }

            // Handle file upload
            if (isset($_FILES['dokumentasi']) && $_FILES['dokumentasi']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/ekstra_jurnal/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                
                $ext = pathinfo($_FILES['dokumentasi']['name'], PATHINFO_EXTENSION);
                $filename = uniqid('jurnal_') . '.' . $ext;
                if (move_uploaded_file($_FILES['dokumentasi']['tmp_name'], $uploadDir . $filename)) {
                    // hapus foto lama jika ada
                    if ($dokumentasi && file_exists($uploadDir . $dokumentasi)) {
                        unlink($uploadDir . $dokumentasi);
                    }
                    $dokumentasi = $filename;
                }
            }

            $db_siakad->beginTransaction();
            try {
                if ($id) {
                    $stmt = $db_siakad->prepare("UPDATE ekstra_jurnal SET tanggal = ?, materi = ?, dokumentasi = ? WHERE id = ?");
                    $stmt->execute([$tanggal, $materi, $dokumentasi, $id]);
                    $jurnal_id = $id;
                    
                    // Hapus absensi lama
                    $db_siakad->query("DELETE FROM ekstra_absen WHERE jurnal_id = " . intval($jurnal_id));
                } else {
                    $stmt = $db_siakad->prepare("INSERT INTO ekstra_jurnal (nama_ekstra, tahun_ajaran, tanggal, materi, dokumentasi) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$nama_ekstra, $active_year_name, $tanggal, $materi, $dokumentasi]);
                    $jurnal_id = $db_siakad->lastInsertId();
                }

                // Insert absensi baru
                if (!empty($absensi)) {
                    $stmtAbsen = $db_siakad->prepare("INSERT INTO ekstra_absen (jurnal_id, siswa_id, status_kehadiran) VALUES (?, ?, ?)");
                    foreach ($absensi as $siswa_id => $status) {
                        $stmtAbsen->execute([$jurnal_id, $siswa_id, $status]);
                    }
                }
                
                $db_siakad->commit();
            } catch (\Exception $e) {
                $db_siakad->rollBack();
            }
            
            redirect("/guru/tugas_tambahan/pembina_ekstra/jurnal?ekstra=" . urlencode($nama_ekstra) . "&success=1");
            exit;
        }
    }

    public function deleteJurnal($id) {
        $guru = $this->getGuruInfo();
        $nama_ekstra = isset($_GET['ekstra']) ? $_GET['ekstra'] : '';
        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
        
        if (!$this->verifyEkstra($guru['id'], $nama_ekstra, $active_year_name)) {
            echo "Akses ditolak.";
            exit;
        }

        $db_siakad = \App\Core\Database::connect('siakad');
        
        // Hapus file dokumentasi
        $jurnal = $db_siakad->query("SELECT dokumentasi FROM ekstra_jurnal WHERE id = " . intval($id) . " AND nama_ekstra = ".$db_siakad->quote($nama_ekstra))->fetch();
        if ($jurnal && $jurnal['dokumentasi']) {
            $file_path = __DIR__ . '/../../public/uploads/ekstra_jurnal/' . $jurnal['dokumentasi'];
            if (file_exists($file_path)) unlink($file_path);
        }

        // Hapus absen lalu jurnal
        $db_siakad->query("DELETE FROM ekstra_absen WHERE jurnal_id = " . intval($id));
        $db_siakad->query("DELETE FROM ekstra_jurnal WHERE id = " . intval($id) . " AND nama_ekstra = ".$db_siakad->quote($nama_ekstra));

        redirect("/guru/tugas_tambahan/pembina_ekstra/jurnal?ekstra=" . urlencode($nama_ekstra) . "&success=delete");
        exit;
    }

    public function absen() { echo "Fitur Absensi Terpadu dengan Jurnal."; }
    public function saveAbsen() { }
    public function nilai() { echo "Penilaian Akhir - Coming Soon"; }
    public function saveNilai() { }

    public function cetakProgram() {
        $guru = $this->getGuruInfo();
        $nama_ekstra = isset($_GET['ekstra']) ? $_GET['ekstra'] : '';
        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
        
        if (!$this->verifyEkstra($guru['id'], $nama_ekstra, $active_year_name)) {
            echo "Akses ditolak. Anda bukan pembina ekstrakurikuler ini.";
            exit;
        }

        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');
        
        $inst = $db_core->query("SELECT * FROM institusi LIMIT 1")->fetch();
        $guru_detail = $db_core->query("SELECT * FROM guru WHERE id = " . intval($guru['id']))->fetch();
        
        $stmt = $db_siakad->prepare("SELECT * FROM ekstra_program WHERE guru_id = ? AND nama_ekstra = ? AND tahun_ajaran = ?");
        $stmt->execute([$guru['id'], $nama_ekstra, $active_year_name]);
        $program = $stmt->fetch();

        $total_anggota = $db_siakad->query("SELECT COUNT(*) as cnt FROM ekstra_anggota WHERE nama_ekstra = " . $db_siakad->quote($nama_ekstra) . " AND tahun_ajaran = " . $db_siakad->quote($active_year_name))->fetch()['cnt'];

        include __DIR__ . '/../../resources/views/portal/guru/tugas_ekstra_program_cetak.php';
    }

}
