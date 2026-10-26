<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\AcademicYear;
use PDO;
use Exception;

class PortalGuruKamadController {

    private function checkAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id']) || empty($_SESSION['is_kamad'])) {
            redirect('/login');
            exit;
        }
    }

    private function getStats($db_core, $db_siakad) {
        $total_guru = $db_core->query("SELECT COUNT(*) FROM guru")->fetchColumn();
        $total_siswa = $db_core->query("SELECT COUNT(*) FROM siswa WHERE status = 'Aktif'")->fetchColumn();
        $total_kelas = $db_core->query("SELECT COUNT(*) FROM kelas")->fetchColumn();

        $total_mapel = 0;
        try {
            $total_mapel = $db_siakad->query("SELECT COUNT(*) FROM mata_pelajaran")->fetchColumn();
            if ($total_mapel == 0) {
                $total_mapel = $db_siakad->query("SELECT COUNT(*) FROM mapel")->fetchColumn();
            }
        } catch (Exception $e) {
            try {
                $total_mapel = $db_siakad->query("SELECT COUNT(*) FROM mapel")->fetchColumn();
            } catch (Exception $ex) {}
        }

        return [
            'guru' => $total_guru,
            'siswa' => $total_siswa,
            'kelas' => $total_kelas,
            'mapel' => $total_mapel
        ];
    }

    public function dashboard() {
        $this->checkAuth();

        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

        $active_year = AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

        $stats = $this->getStats($db_core, $db_siakad);

        // Fetch classes list with student count for overview tab
        $kelasList = $db_core->query("SELECT id, nama_kelas, tingkat FROM kelas ORDER BY tingkat, nama_kelas")->fetchAll();
        foreach($kelasList as &$kls) {
            $kls['jumlah_siswa'] = $db_core->query("SELECT COUNT(DISTINCT r.siswa_id) FROM riwayat_kelas_siswa r JOIN tahun_ajaran ta ON r.tahun_ajaran_id = ta.id WHERE r.kelas_id = ".$kls['id']." AND ta.name = '$active_year_name'")->fetchColumn();
        }

        // Live stats for today
        $today = date('Y-m-d');
        
        // 1. Student Attendance Today
        $hadirSiswa = $db_siakad->query("SELECT COUNT(*) FROM absensi_siswa WHERE tanggal = '$today' AND status = 'Hadir'")->fetchColumn() ?: 0;
        $sakitSiswa = $db_siakad->query("SELECT COUNT(*) FROM absensi_siswa WHERE tanggal = '$today' AND status = 'Sakit'")->fetchColumn() ?: 0;
        $izinSiswa  = $db_siakad->query("SELECT COUNT(*) FROM absensi_siswa WHERE tanggal = '$today' AND status = 'Izin'")->fetchColumn() ?: 0;
        $alphaSiswa = $db_siakad->query("SELECT COUNT(*) FROM absensi_siswa WHERE tanggal = '$today' AND (status = 'Alpha' OR status = 'A')")->fetchColumn() ?: 0;
        $tidakHadirSiswa = $sakitSiswa + $izinSiswa + $alphaSiswa;
        $absenSiswaPersen = $stats['siswa'] > 0 ? round(($hadirSiswa / $stats['siswa']) * 100) : 0;

        // 2. Teacher Attendance Today
        $hadirGuru = $db_siakad->query("SELECT COUNT(*) FROM absensi_guru WHERE tanggal = '$today' AND status = 'Hadir'")->fetchColumn() ?: 0;
        $present_guru_ids = $db_siakad->query("SELECT DISTINCT guru_id FROM absensi_guru WHERE tanggal = '$today' AND status = 'Hadir'")->fetchAll(PDO::FETCH_COLUMN);
        
        $tidakHadirGuruList = [];
        if (!empty($present_guru_ids)) {
            $in_clause = implode(',', array_map('intval', $present_guru_ids));
            $tidakHadirGuruList = $db_core->query("SELECT id, nama, jabatan FROM guru WHERE id NOT IN ($in_clause) ORDER BY nama ASC")->fetchAll();
        } else {
            $tidakHadirGuruList = $db_core->query("SELECT id, nama, jabatan FROM guru ORDER BY nama ASC")->fetchAll();
        }
        $tidakHadirGuru = count($tidakHadirGuruList);

        // 3. Announcements
        $pengumumanList = $db_core->query("SELECT * FROM pengumuman ORDER BY tanggal_dibuat DESC, id DESC LIMIT 3")->fetchAll();

        // 4. Today's active classroom journals (Agenda Hari Ini)
        $jurnalsToday = $db_siakad->query("SELECT * FROM jurnal_guru WHERE tanggal = '$today' ORDER BY id DESC LIMIT 5")->fetchAll();
        if (!empty($jurnalsToday)) {
            // Fetch class names
            $kelasListRaw = $db_core->query("SELECT id, nama_kelas FROM kelas")->fetchAll();
            $kelasMap = [];
            foreach($kelasListRaw as $kls_raw) $kelasMap[$kls_raw['id']] = $kls_raw['nama_kelas'];

            // Fetch teacher names
            $guruIds = array_unique(array_column($jurnalsToday, 'guru_id'));
            $guruMap = [];
            if (!empty($guruIds)) {
                $inClause = implode(',', array_map('intval', $guruIds));
                $gurusRaw = $db_core->query("SELECT id, nama FROM guru WHERE id IN ($inClause)")->fetchAll();
                foreach($gurusRaw as $g) $guruMap[$g['id']] = $g['nama'];
            }

            // Fetch mapel names
            $mapelListRaw = [];
            try {
                $mapelListRaw = $db_siakad->query("SELECT id, nama_mapel FROM mata_pelajaran")->fetchAll();
                if (empty($mapelListRaw)) {
                    $mapelListRaw = $db_siakad->query("SELECT id, nama_mapel FROM mapel")->fetchAll();
                }
            } catch (Exception $e) {
                try {
                    $mapelListRaw = $db_siakad->query("SELECT id, nama_mapel FROM mapel")->fetchAll();
                } catch (Exception $ex) {}
            }
            $mapelMap = [];
            foreach($mapelListRaw as $m) $mapelMap[$m['id']] = $m['nama_mapel'];

            foreach($jurnalsToday as &$j) {
                $j['nama_guru'] = $guruMap[$j['guru_id']] ?? 'Guru ID '.$j['guru_id'];
                $j['nama_kelas'] = $kelasMap[$j['kelas_id']] ?? 'Kelas ID '.$j['kelas_id'];
                $j['nama_mapel'] = $mapelMap[$j['mapel_id']] ?? 'Mapel ID '.$j['mapel_id'];
            }
        }

        $title_page = "Dashboard Kepala Madrasah";
        $tab = 'overview';

        include __DIR__ . '/../../resources/views/portal/guru/kamad_dashboard.php';
    }

    public function program() {
        $this->checkAuth();

        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

        $stats = $this->getStats($db_core, $db_siakad);

        $title_page = "Program Kerja Kepala Madrasah";
        $tab = 'program';

        include __DIR__ . '/../../resources/views/portal/guru/kamad_dashboard.php';
    }

    public function monitoringJurnal() {
        $this->checkAuth();

        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

        $stats = $this->getStats($db_core, $db_siakad);

        $filter_tanggal_mulai = isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : date('Y-m-01');
        $filter_tanggal_sampai = isset($_GET['tanggal_sampai']) ? $_GET['tanggal_sampai'] : date('Y-m-t');
        $filter_kelas = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : 0;

        $kelasList = $db_core->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat, nama_kelas")->fetchAll();
        
        $mapelList = [];
        try {
            $mapelList = $db_siakad->query("SELECT id, nama_mapel FROM mata_pelajaran ORDER BY nama_mapel")->fetchAll();
            if (empty($mapelList)) {
                $mapelList = $db_siakad->query("SELECT id, nama_mapel FROM mapel ORDER BY nama_mapel")->fetchAll();
            }
        } catch (Exception $e) {
            try {
                $mapelList = $db_siakad->query("SELECT id, nama_mapel FROM mapel ORDER BY nama_mapel")->fetchAll();
            } catch (Exception $ex) {}
        }
        $mapelMap = [];
        foreach($mapelList as $m) $mapelMap[$m['id']] = $m['nama_mapel'];
        
        $kelasMap = [];
        foreach($kelasList as $k) $kelasMap[$k['id']] = $k['nama_kelas'];

        // Query Jurnal
        $query = "SELECT j.* FROM jurnal_guru j WHERE j.tanggal >= :tgl_mulai AND j.tanggal <= :tgl_sampai";
        $params = [
            'tgl_mulai' => $filter_tanggal_mulai,
            'tgl_sampai' => $filter_tanggal_sampai
        ];
        
        if ($filter_kelas > 0) {
            $query .= " AND j.kelas_id = :kid";
            $params['kid'] = $filter_kelas;
        }
        
        $query .= " ORDER BY j.tanggal DESC, j.id DESC";

        $stmt = $db_siakad->prepare($query);
        $stmt->execute($params);
        $jurnals = $stmt->fetchAll();

        // Ambil data absensi_siswa untuk jurnal yang ditampilkan
        $absen_map = [];
        $all_siswa_ids = [];
        $absen_records = [];

        if (!empty($jurnals)) {
            $absen_query = "SELECT a.siswa_id, a.kelas_id, a.tanggal, a.status 
                            FROM absensi_siswa a 
                            WHERE a.tanggal >= :tgl_mulai AND a.tanggal <= :tgl_sampai";
            $absen_params = [
                'tgl_mulai' => $filter_tanggal_mulai,
                'tgl_sampai' => $filter_tanggal_sampai
            ];
            if ($filter_kelas > 0) {
                $absen_query .= " AND a.kelas_id = :kid";
                $absen_params['kid'] = $filter_kelas;
            }
            $stmt_a = $db_siakad->prepare($absen_query);
            $stmt_a->execute($absen_params);
            $absen_records = $stmt_a->fetchAll();

            foreach($absen_records as $ar) {
                if (!empty($ar['siswa_id'])) {
                    $all_siswa_ids[] = $ar['siswa_id'];
                }
            }
        }

        // Ambil nama siswa dari core_db secara bulk
        $siswa_map = [];
        if (!empty($all_siswa_ids)) {
            $unique_siswa_ids = array_unique(array_filter($all_siswa_ids));
            if (!empty($unique_siswa_ids)) {
                $inSiswa = implode(',', array_map('intval', $unique_siswa_ids));
                $siswas = $db_core->query("SELECT id, nama FROM siswa WHERE id IN ($inSiswa)")->fetchAll();
                foreach($siswas as $s) {
                    $siswa_map[$s['id']] = $s['nama'];
                }
            }
        }

        // Kelompokkan absensi per tanggal & kelas_id
        foreach($absen_records as $ar) {
            $key = $ar['tanggal'] . '_' . $ar['kelas_id'];
            if (!isset($absen_map[$key])) {
                $absen_map[$key] = [
                    'hadir' => [],
                    'izin' => [],
                    'sakit' => [],
                    'alpa' => [],
                    'total' => 0
                ];
            }
            
            $st = trim($ar['status']);
            $st_lower = strtolower($st);
            $nama_siswa = isset($siswa_map[$ar['siswa_id']]) ? $siswa_map[$ar['siswa_id']] : 'Siswa #'.$ar['siswa_id'];
            
            $siswa_item = [
                'id' => $ar['siswa_id'],
                'nama' => $nama_siswa,
                'status' => $st
            ];
            
            if (in_array($st_lower, ['hadir', 'h'])) {
                $absen_map[$key]['hadir'][] = $siswa_item;
            } elseif (in_array($st_lower, ['izin', 'i'])) {
                $absen_map[$key]['izin'][] = $siswa_item;
            } elseif (in_array($st_lower, ['sakit', 's'])) {
                $absen_map[$key]['sakit'][] = $siswa_item;
            } else { // alpa, alpha, a
                $absen_map[$key]['alpa'][] = $siswa_item;
            }
            $absen_map[$key]['total']++;
        }

        // Get Teacher info from core
        $guru_ids = array_unique(array_column($jurnals, 'guru_id'));
        $guruMap = [];
        if (!empty($guru_ids)) {
            $inClause = implode(',', array_map('intval', $guru_ids));
            $gurus = $db_core->query("SELECT id, nama FROM guru WHERE id IN ($inClause)")->fetchAll();
            foreach($gurus as $g) {
                $guruMap[$g['id']] = $g['nama'];
            }
        }

        foreach($jurnals as &$j) {
            $j['nama_guru'] = isset($guruMap[$j['guru_id']]) ? $guruMap[$j['guru_id']] : 'Guru ID '.$j['guru_id'];
            $j['nama_kelas'] = isset($kelasMap[$j['kelas_id']]) ? $kelasMap[$j['kelas_id']] : 'Kelas ID '.$j['kelas_id'];
            $j['nama_mapel'] = isset($mapelMap[$j['mapel_id']]) ? $mapelMap[$j['mapel_id']] : 'Mapel ID '.$j['mapel_id'];

            $key = $j['tanggal'] . '_' . $j['kelas_id'];
            if (isset($absen_map[$key])) {
                $j['total_absen'] = $absen_map[$key]['total'];
                $j['absen_detail'] = $absen_map[$key];
            } else {
                $j['total_absen'] = 0;
                $j['absen_detail'] = [
                    'hadir' => [],
                    'izin' => [],
                    'sakit' => [],
                    'alpa' => [],
                    'total' => 0
                ];
            }
        }

        $title_page = "Monitor Jurnal Mengajar";
        $tab = 'jurnal';

        include __DIR__ . '/../../resources/views/portal/guru/kamad_dashboard.php';
    }

    public function monitoringNilai() {
        $this->checkAuth();

        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

        $stats = $this->getStats($db_core, $db_siakad);

        $active_year = AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
        $active_year_id = $active_year ? $active_year['id'] : 1;

        $kelasList = $db_core->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat, nama_kelas")->fetchAll();
        
        $mapelList = [];
        try {
            $mapelList = $db_siakad->query("SELECT id, nama_mapel FROM mata_pelajaran ORDER BY nama_mapel")->fetchAll();
            if (empty($mapelList)) {
                $mapelList = $db_siakad->query("SELECT id, nama_mapel FROM mapel ORDER BY nama_mapel")->fetchAll();
            }
        } catch (Exception $e) {
            try {
                $mapelList = $db_siakad->query("SELECT id, nama_mapel FROM mapel ORDER BY nama_mapel")->fetchAll();
            } catch (Exception $ex) {}
        }

        $selected_kelas_id = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : (count($kelasList) > 0 ? $kelasList[0]['id'] : null);
        $selected_mapel_id = isset($_GET['mapel_id']) ? intval($_GET['mapel_id']) : (count($mapelList) > 0 ? $mapelList[0]['id'] : null);

        $nilaiList = [];
        if ($selected_kelas_id && $selected_mapel_id) {
            $stmt_students = $db_core->prepare("
                SELECT DISTINCT s.id, s.nama, s.nis, s.nisn 
                FROM siswa s 
                JOIN riwayat_kelas_siswa r ON s.id = r.siswa_id 
                JOIN tahun_ajaran ta ON r.tahun_ajaran_id = ta.id 
                WHERE r.kelas_id = ? AND ta.name = ? 
                ORDER BY s.nama ASC
            ");
            $stmt_students->execute([$selected_kelas_id, $active_year_name]);
            $students_in_kelas = $stmt_students->fetchAll();

            $stmt_grades = $db_siakad->prepare("
                SELECT * FROM nilai 
                WHERE mapel_id = ? AND tahun_ajaran_id = ?
            ");
            $stmt_grades->execute([$selected_mapel_id, $active_year_id]);
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

        $title_page = "Monitor Nilai Rapor Siswa";
        $tab = 'nilai';

        include __DIR__ . '/../../resources/views/portal/guru/kamad_dashboard.php';
    }

    public function cetakProgram() {
        $this->checkAuth();

        $db_core = Database::connect('core');
        $active_year = AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

        // Profile, Kepsek/Kamad, Guru, Kelas, Siswa details
        $inst = $db_core->query("SELECT * FROM institusi LIMIT 1")->fetch();
        $kamad_profile = $db_core->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 LIMIT 1")->fetch();
        
        $gurus = $db_core->query("SELECT nama, nip, jabatan, status_kepegawaian FROM guru ORDER BY nama ASC")->fetchAll();
        
        $kelasList = $db_core->query("SELECT * FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll();
        foreach($kelasList as &$kls) {
            $kls['jumlah_siswa'] = $db_core->query("SELECT COUNT(DISTINCT r.siswa_id) FROM riwayat_kelas_siswa r JOIN tahun_ajaran ta ON r.tahun_ajaran_id = ta.id WHERE r.kelas_id = ".$kls['id']." AND ta.name = '$active_year_name'")->fetchColumn();
        }

        $total_siswa = $db_core->query("SELECT COUNT(*) FROM siswa WHERE status = 'Aktif'")->fetchColumn();

        include __DIR__ . '/../../resources/views/portal/guru/kamad_program_cetak.php';
    }
}
