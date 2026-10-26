<?php

namespace App\Controllers;

use App\Core\Database;

class PortalGuruTugasController {
    
    public function waliKelas() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
            redirect('/login');
            exit;
        }

        $db_core = Database::connect('core');
        $guru_id = $db_core->query("SELECT id FROM guru WHERE user_id = ".$_SESSION['user_id'])->fetchColumn();
        
        if (!$guru_id) {
            echo "Akses ditolak. Anda tidak terdaftar sebagai Guru.";
            exit;
        }

        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

        // Ambil semua tugas wali kelas (Keterangan menyimpan format 'Kelas [Nama]')
        $tugasWaliKelas = $db_core->query("SELECT keterangan FROM guru_tugas WHERE guru_id = $guru_id AND tugas = 'Wali Kelas' AND tahun_ajaran = '$active_year_name'")->fetchAll(\PDO::FETCH_COLUMN);

        if (empty($tugasWaliKelas)) {
            echo "Anda tidak memiliki penugasan sebagai Wali Kelas.";
            exit;
        }

        $nama_kelas_array = [];
        foreach($tugasWaliKelas as $ket) {
            // Bersihkan teks "Kelas " dari keterangan untuk matching dengan tabel kelas
            $nama = str_replace('Kelas ', '', trim($ket));
            if(!empty($nama)) $nama_kelas_array[] = $nama;
        }

        $kelasList = [];
        if(!empty($nama_kelas_array)) {
            $placeholders = str_repeat('?,', count($nama_kelas_array) - 1) . '?';
            $stmt = $db_core->prepare("SELECT * FROM kelas WHERE nama_kelas IN ($placeholders) ORDER BY nama_kelas ASC");
            $stmt->execute($nama_kelas_array);
            $kelasList = $stmt->fetchAll();

            $db_siakad = Database::connect('siakad');
            $today = date('Y-m-d');
            $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
            foreach($kelasList as &$kls) {
                $siswaCount = $db_core->query("SELECT COUNT(DISTINCT r.siswa_id) FROM riwayat_kelas_siswa r JOIN tahun_ajaran ta ON r.tahun_ajaran_id = ta.id WHERE r.kelas_id = ".$kls['id']." AND ta.name = '$active_year_name'")->fetchColumn();
                $kls['jumlah_siswa'] = $siswaCount;
                
                $absenStats = $db_siakad->query("SELECT status, COUNT(*) as count FROM absensi WHERE kelas_id = ".$kls['id']." AND tanggal = '$today' GROUP BY status")->fetchAll(\PDO::FETCH_KEY_PAIR);
                $kls['absen_today'] = $absenStats;
            }
        }

        $title_page = "Wali Kelas";
        include __DIR__ . '/../../resources/views/portal/guru/tugas_wali_kelas.php';
    }

    public function detailWaliKelas() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
            redirect('/login');
            exit;
        }

        if (!isset($_GET['id'])) {
            redirect('/guru/tugas_tambahan/wali_kelas');
            exit;
        }
        $kelas_id = intval($_GET['id']);

        $db_core = Database::connect('core');
        $guru_id = $db_core->query("SELECT id FROM guru WHERE user_id = ".$_SESSION['user_id'])->fetchColumn();
        
        if (!$guru_id) {
            echo "Akses ditolak. Anda tidak terdaftar sebagai Guru.";
            exit;
        }

        // Ambil info kelas
        $stmt = $db_core->prepare("SELECT * FROM kelas WHERE id = ?");
        $stmt->execute([$kelas_id]);
        $kelasInfo = $stmt->fetch();

        if (!$kelasInfo) {
            echo "Data kelas tidak ditemukan.";
            exit;
        }

        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

        // Verifikasi bahwa guru ini benar-benar wali kelas dari kelas ini di tahun ajaran aktif
        $keteranganTarget = "Kelas " . $kelasInfo['nama_kelas'];
        $stmt = $db_core->prepare("SELECT id FROM guru_tugas WHERE guru_id = ? AND tugas = 'Wali Kelas' AND keterangan = ? AND tahun_ajaran = ?");
        $stmt->execute([$guru_id, $keteranganTarget, $active_year_name]);
        $cekWali = $stmt->fetch();

        if (!$cekWali) {
            echo "Akses ditolak. Anda bukan Wali Kelas untuk kelas ini.";
            exit;
        }

        // Ambil tahun ajaran aktif
        $tahun_ajaran = $db_core->query("SELECT name FROM tahun_ajaran WHERE is_active = 1 LIMIT 1")->fetchColumn();
        if(!$tahun_ajaran) $tahun_ajaran = date('Y') . '/' . (date('Y')+1);

        // Ambil data siswa dari DB Core berdasarkan riwayat tahun ajaran aktif
        $active_year_name_query = $active_year ? $active_year['name'] : '2025/2026';
        $stmt = $db_core->prepare("SELECT DISTINCT s.id, s.nama, s.nis, s.nisn, s.jenis_kelamin, s.foto FROM siswa s JOIN riwayat_kelas_siswa r ON s.id = r.siswa_id JOIN tahun_ajaran ta ON r.tahun_ajaran_id = ta.id WHERE r.kelas_id = ? AND ta.name = ? ORDER BY s.nama ASC");
        $stmt->execute([$kelas_id, $active_year_name_query]);
        $siswaList = $stmt->fetchAll();

        $title_page = "Detail Kelas - " . $kelasInfo['nama_kelas'];
        include __DIR__ . '/../../resources/views/portal/guru/tugas_wali_kelas_detail.php';
    }

    public function absensiWaliKelas() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
            redirect('/login');
            exit;
        }

        if (!isset($_GET['id'])) {
            redirect('/guru/tugas_tambahan/wali_kelas');
            exit;
        }
        $kelas_id = intval($_GET['id']);
        $tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

        $db_core = Database::connect('core');
        $guru_id = $db_core->query("SELECT id FROM guru WHERE user_id = ".$_SESSION['user_id'])->fetchColumn();
        
        // Verifikasi kelas & wali
        $stmt = $db_core->prepare("SELECT * FROM kelas WHERE id = ?");
        $stmt->execute([$kelas_id]);
        $kelasInfo = $stmt->fetch();

        if (!$kelasInfo) {
            echo "Data kelas tidak ditemukan.";
            exit;
        }

        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

        $keteranganTarget = "Kelas " . $kelasInfo['nama_kelas'];
        $stmt = $db_core->prepare("SELECT id FROM guru_tugas WHERE guru_id = ? AND tugas = 'Wali Kelas' AND keterangan = ? AND tahun_ajaran = ?");
        $stmt->execute([$guru_id, $keteranganTarget, $active_year_name]);
        if (!$stmt->fetch()) {
            echo "Akses ditolak. Anda bukan Wali Kelas untuk kelas ini.";
            exit;
        }

        $tahun_ajaran = $db_core->query("SELECT name FROM tahun_ajaran WHERE is_active = 1 LIMIT 1")->fetchColumn();
        if(!$tahun_ajaran) $tahun_ajaran = date('Y') . '/' . (date('Y')+1);

        $active_year_name_query = $active_year ? $active_year['name'] : '2025/2026';
        $stmt = $db_core->prepare("SELECT DISTINCT s.id, s.nama, s.nis, s.nisn, s.jenis_kelamin, s.foto FROM siswa s JOIN riwayat_kelas_siswa r ON s.id = r.siswa_id JOIN tahun_ajaran ta ON r.tahun_ajaran_id = ta.id WHERE r.kelas_id = ? AND ta.name = ? ORDER BY s.nama ASC");
        $stmt->execute([$kelas_id, $active_year_name_query]);
        $siswaList = $stmt->fetchAll();

        // Ambil data absensi untuk tanggal ini
        $db_siakad = Database::connect('siakad');
        $stmt = $db_siakad->prepare("SELECT siswa_id, status, keterangan FROM absensi WHERE kelas_id = ? AND tanggal = ?");
        $stmt->execute([$kelas_id, $tanggal]);
        $absenDataRaw = $stmt->fetchAll();
        
        $absenMap = [];
        foreach($absenDataRaw as $a) {
            $absenMap[$a['siswa_id']] = ['status' => $a['status'], 'keterangan' => $a['keterangan']];
        }

        // Handle POST save
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $absen_post = isset($_POST['absen']) ? $_POST['absen'] : [];
            
            try {
                $db_siakad->beginTransaction();
                foreach($siswaList as $s) {
                    $sid = $s['id'];
                    $status = isset($absen_post[$sid]) ? $absen_post[$sid] : 'H';
                    
                    if(isset($absenMap[$sid])) {
                        $upd = $db_siakad->prepare("UPDATE absensi SET status = ? WHERE siswa_id = ? AND tanggal = ?");
                        $upd->execute([$status, $sid, $tanggal]);
                    } else {
                        $ins = $db_siakad->prepare("INSERT INTO absensi (siswa_id, kelas_id, tahun_ajaran, tanggal, status) VALUES (?, ?, ?, ?, ?)");
                        $ins->execute([$sid, $kelas_id, $tahun_ajaran, $tanggal, $status]);
                    }
                }
                $db_siakad->commit();
                redirect("/guru/tugas_tambahan/wali_kelas/absensi?id=$kelas_id&tanggal=$tanggal&success=1");
                exit;
            } catch(\Exception $e) {
                $db_siakad->rollBack();
                $error_msg = "Gagal menyimpan absensi: " . $e->getMessage();
            }
        }

        $title_page = "Absensi - " . $kelasInfo['nama_kelas'];
        include __DIR__ . '/../../resources/views/portal/guru/tugas_wali_kelas_absensi.php';
    }
    public function pembinaEkstra() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
            redirect('/login');
            exit;
        }

        $db_core = Database::connect('core');
        $guru_id = $db_core->query("SELECT id FROM guru WHERE user_id = ".$_SESSION['user_id'])->fetchColumn();
        
        if (!$guru_id) {
            echo "Akses ditolak. Anda tidak terdaftar sebagai Guru.";
            exit;
        }

        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

        // Ambil semua tugas pembina ekstrakurikuler
        $tugasEkstra = $db_core->query("SELECT keterangan FROM guru_tugas WHERE guru_id = $guru_id AND tugas = 'Pembina Ekstrakurikuler' AND tahun_ajaran = '$active_year_name'")->fetchAll(\PDO::FETCH_COLUMN);

        if (empty($tugasEkstra)) {
            echo "Anda tidak memiliki penugasan sebagai Pembina Ekstrakurikuler.";
            exit;
        }

        $ekstraList = [];
        foreach($tugasEkstra as $nama_ekstra) {
            if(!empty(trim($nama_ekstra))) {
                $ekstraList[] = ['nama_ekstra' => trim($nama_ekstra)];
            }
        }

        $title_page = "Pembina Ekstrakurikuler";
        include __DIR__ . '/../../resources/views/portal/guru/tugas_pembina_ekstra.php';
    }

    public function wakaKurikulum() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
            redirect('/login');
            exit;
        }

        $db_core = Database::connect('core');
        $guru_id = $db_core->query("SELECT id FROM guru WHERE user_id = ".$_SESSION['user_id'])->fetchColumn();
        
        if (!$guru_id) {
            echo "Akses ditolak. Anda tidak terdaftar sebagai Guru.";
            exit;
        }

        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
        $active_year_id = $active_year ? $active_year['id'] : 1;

        // Check if teacher is assigned Waka Kurikulum
        $wakaCheck = $db_core->query("SELECT id FROM guru_tugas WHERE guru_id = $guru_id AND tugas = 'Waka Kurikulum' AND tahun_ajaran = '$active_year_name'")->fetch();

        if (!$wakaCheck) {
            echo "Akses ditolak. Anda tidak ditugaskan sebagai Waka Kurikulum pada tahun ajaran aktif.";
            exit;
        }

        $db_siakad = Database::connect('siakad');
        
        // Tab parameter
        $tab = isset($_GET['tab']) ? $_GET['tab'] : 'overview';

        // Stats
        $total_mapel = $db_siakad->query("SELECT COUNT(*) FROM mapel")->fetchColumn();
        $total_kelas = $db_core->query("SELECT COUNT(*) FROM kelas")->fetchColumn();
        $total_guru = $db_core->query("SELECT COUNT(*) FROM guru")->fetchColumn();
        $total_siswa = $db_core->query("SELECT COUNT(*) FROM siswa WHERE status = 'Aktif'")->fetchColumn();

        // 1. Overview Tab
        $kelasList = $db_core->query("SELECT * FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll();

        // Get student count for each class in active academic year
        foreach($kelasList as &$kls) {
            $kls['jumlah_siswa'] = $db_core->query("SELECT COUNT(DISTINCT r.siswa_id) FROM riwayat_kelas_siswa r JOIN tahun_ajaran ta ON r.tahun_ajaran_id = ta.id WHERE r.kelas_id = ".$kls['id']." AND ta.name = '$active_year_name'")->fetchColumn();
        }

        // 2. Mapel Tab
        $mapelList = $db_siakad->query("SELECT * FROM mapel ORDER BY nama_mapel ASC")->fetchAll();

        // 3. Jadwal Tab
        $selected_kelas_jadwal = isset($_GET['kelas_jadwal_id']) ? intval($_GET['kelas_jadwal_id']) : (count($kelasList) > 0 ? $kelasList[0]['id'] : null);
        $jadwal_raw = [];
        if ($selected_kelas_jadwal) {
            $stmt_jadwal = $db_siakad->prepare("
                SELECT jp.*, m.nama_mapel, g.nama as nama_guru 
                FROM jadwal_pelajaran jp
                JOIN mata_pelajaran m ON jp.mata_pelajaran_id = m.id
                JOIN aula_db.guru g ON jp.guru_id = g.id
                WHERE jp.kelas_id = ? AND jp.tahun_ajaran_id = ?
                ORDER BY jp.hari ASC, jp.jam_mulai ASC
            ");
            $stmt_jadwal->execute([$selected_kelas_jadwal, $active_year_id]);
            $jadwal_raw = $stmt_jadwal->fetchAll();
        }

        $jadwal_mapel = [];
        foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari) {
            $jadwal_mapel[$hari] = array_filter($jadwal_raw, function($j) use ($hari) { return $j['hari'] == $hari; });
        }

        // 4. Nilai Tab
        $selected_kelas_nilai = isset($_GET['kelas_nilai_id']) ? intval($_GET['kelas_nilai_id']) : (count($kelasList) > 0 ? $kelasList[0]['id'] : null);
        $selected_mapel_nilai = isset($_GET['mapel_nilai_id']) ? intval($_GET['mapel_nilai_id']) : (count($mapelList) > 0 ? $mapelList[0]['id'] : null);
        
        $nilaiList = [];
        if ($selected_kelas_nilai && $selected_mapel_nilai) {
            $stmt_students = $db_core->prepare("
                SELECT DISTINCT s.id, s.nama, s.nis, s.nisn 
                FROM siswa s 
                JOIN riwayat_kelas_siswa r ON s.id = r.siswa_id 
                JOIN tahun_ajaran ta ON r.tahun_ajaran_id = ta.id 
                WHERE r.kelas_id = ? AND ta.name = ? 
                ORDER BY s.nama ASC
            ");
            $stmt_students->execute([$selected_kelas_nilai, $active_year_name]);
            $students_in_kelas = $stmt_students->fetchAll();

            $stmt_grades = $db_siakad->prepare("
                SELECT * FROM nilai 
                WHERE mapel_id = ? AND tahun_ajaran_id = ?
            ");
            $stmt_grades->execute([$selected_mapel_nilai, $active_year_id]);
            $grades_raw = $stmt_grades->fetchAll();

            $grade_map = [];
            foreach($grades_raw as $g) {
                $grade_map[$g['siswa_id']] = $g;
            }

            foreach($students_in_kelas as $std) {
                $sid = $std['id'];
                $g_data = isset($grade_map[$sid]) ? $grade_map[$sid] : null;
                $nilaiList[] = [
                    'siswa_id' => $sid,
                    'nama' => $std['nama'],
                    'nis' => $std['nis'],
                    'nilai_tugas' => $g_data ? $g_data['nilai_tugas'] : 0,
                    'nilai_uts' => $g_data ? $g_data['nilai_uts'] : 0,
                    'nilai_uas' => $g_data ? $g_data['nilai_uas'] : 0,
                    'nilai_akhir' => $g_data ? $g_data['nilai_akhir'] : 0,
                ];
            }
        }

        $title_page = "Waka Kurikulum";
        include __DIR__ . '/../../resources/views/portal/guru/tugas_waka_kurikulum.php';
    }

    public function cetakProgramWakaKurikulum() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
            redirect('/login');
            exit;
        }

        $db_core = Database::connect('core');
        $guru_id = $db_core->query("SELECT id FROM guru WHERE user_id = ".$_SESSION['user_id'])->fetchColumn();
        
        if (!$guru_id) {
            echo "Akses ditolak. Anda tidak terdaftar sebagai Guru.";
            exit;
        }

        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
        $active_year_id = $active_year ? $active_year['id'] : 1;

        // Check if teacher is assigned Waka Kurikulum
        $wakaCheck = $db_core->query("SELECT id FROM guru_tugas WHERE guru_id = $guru_id AND tugas = 'Waka Kurikulum' AND tahun_ajaran = '$active_year_name'")->fetch();

        if (!$wakaCheck) {
            echo "Akses ditolak. Anda tidak ditugaskan sebagai Waka Kurikulum pada tahun ajaran aktif.";
            exit;
        }

        // Get basic profile information
        $inst = $db_core->query("SELECT * FROM institusi LIMIT 1")->fetch();
        $guru_detail = $db_core->query("SELECT * FROM guru WHERE id = " . intval($guru_id))->fetch();
        $kepsek = $db_core->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 LIMIT 1")->fetch();

        // Get list of teachers & staff
        $gurus = $db_core->query("SELECT nama, nip, jabatan, status_kepegawaian FROM guru ORDER BY nama ASC")->fetchAll();

        // Get list of classes (rombel) and count students
        $kelasList = $db_core->query("SELECT * FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll();
        foreach($kelasList as &$kls) {
            $kls['jumlah_siswa'] = $db_core->query("SELECT COUNT(DISTINCT r.siswa_id) FROM riwayat_kelas_siswa r JOIN tahun_ajaran ta ON r.tahun_ajaran_id = ta.id WHERE r.kelas_id = ".$kls['id']." AND ta.name = '$active_year_name'")->fetchColumn();
        }

        // Get total active students
        $total_siswa = $db_core->query("SELECT COUNT(*) FROM siswa WHERE status = 'Aktif'")->fetchColumn();

        include __DIR__ . '/../../resources/views/portal/guru/tugas_waka_kurikulum_cetak.php';
    }
}
