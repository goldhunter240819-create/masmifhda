<?php

namespace App\Controllers;

use App\Core\Database;

class SiakadDashboardController {

    public static function index() {
        if (!isset($_SESSION['role_id'])) { redirect('/login'); exit; }
        
        $role = $_SESSION['role_id'];
        $user_id = $_SESSION['user_id'];
        $is_admin = false;
        
        if ($role == 1 || $role == 99) {
            $is_admin = true;
        } else {
            $db = Database::connect('core');
            try {
                $cek = $db->query("SELECT id FROM app_admins WHERE user_id = $user_id AND app_code = 'siakad'")->fetch();
                if ($cek) $is_admin = true;
            } catch (\PDOException $e) {}
        }

        $controller = new self();
        if ($is_admin) {
            $controller->dashboardAdmin();
        } else if ($role == 2) {
            $controller->dashboardGuru();
        } else if ($role == 3) {
            $controller->dashboardSiswa();
        } else {
            die("Role tidak dikenali.");
        }
    }


    public function dashboardAdmin() {
        $db = Database::connect('core');
        $dbS = Database::connect('siakad');

        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

        $active_year_id = $active_year ? $active_year['id'] : 1;

        // Count ALL students that belong to this academic year (by name, all semesters)
        // This includes Alumni who graduated from this year, and students in riwayat
        $totalSiswa = $db->query("
            SELECT COUNT(DISTINCT s.id) FROM siswa s
            LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id
            LEFT JOIN tahun_ajaran ta ON ta.id = rks.tahun_ajaran_id AND ta.name = '$active_year_name'
            WHERE s.status IN ('Aktif', 'Alumni')
            AND (s.tahun_ajaran = '$active_year_name' OR ta.id IS NOT NULL)
        ")->fetchColumn();
        $totalGuru  = $db->query("SELECT COUNT(*) FROM guru")->fetchColumn();
        $totalKelas = $db->query("SELECT COUNT(*) FROM kelas")->fetchColumn();
        $totalMapel = $dbS->query("SELECT COUNT(*) FROM mapel")->fetchColumn();
        
        // Data Akademik: Absensi Hari Ini
        $today = date('Y-m-d');
        $hadir = $dbS->query("SELECT COUNT(*) FROM absensi_siswa WHERE tanggal = '$today' AND status = 'Hadir'")->fetchColumn();
        $tidakHadir = $dbS->query("SELECT COUNT(*) FROM absensi_siswa WHERE tanggal = '$today' AND status != 'Hadir'")->fetchColumn();
        $absensiPersen = $totalSiswa > 0 ? round(($hadir / $totalSiswa) * 100) : 0;

        // Aktivitas Akademik Terbaru
        $activities = [];
        
        // 1. Log dari Audit (Filter yang berkaitan dengan SIAKAD/Akademik)
        $logs = $db->query("SELECT a.*, u.username as nama_user FROM audit_logs a LEFT JOIN users u ON a.user_id = u.id WHERE a.action LIKE '%siswa%' OR a.action LIKE '%nilai%' OR a.action LIKE '%kelas%' ORDER BY created_at DESC LIMIT 5")->fetchAll();
        foreach ($logs as $l) {
            $activities[] = [
                'icon' => 'activity',
                'text' => $l['action'],
                'sub'  => $l['details'],
                'time' => $l['created_at']
            ];
        }
        
        // 2. Siswa yang baru ditambahkan (berdasarkan ID tertinggi)
        $newStudents = $db->query("SELECT nama, nis FROM siswa WHERE status IN ('Aktif', 'Alumni') AND tahun_ajaran = '$active_year_name' ORDER BY id DESC LIMIT 3")->fetchAll();
        foreach ($newStudents as $s) {
            $activities[] = [
                'icon' => 'user-plus',
                'text' => "Siswa Baru: " . $s['nama'],
                'sub'  => "NIS: " . $s['nis'],
                'time' => date('Y-m-d H:i:s') // Karena tidak ada created_at, kita asumsikan baru
            ];
        }

        // 3. Absensi terbaru
        $recentAbsen = $dbS->query("SELECT a.*, s.nama_lengkap as nama FROM absensi_siswa a JOIN siswa s ON a.siswa_id = s.id ORDER BY a.created_at DESC LIMIT 3")->fetchAll();
        foreach ($recentAbsen as $ra) {
            $activities[] = [
                'icon' => 'check-square',
                'text' => "Presensi: " . $ra['nama'],
                'sub'  => "Status: " . $ra['status'] . " (Tgl: " . $ra['tanggal'] . ")",
                'time' => $ra['created_at']
            ];
        }
        
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });
        $activities = array_slice($activities, 0, 6);

        $nama    = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Administrator';
        $initial = strtoupper(substr($nama, 0, 2));

        $title = "Dashboard SIAKAD - MI MIFTAHUL HUDA";
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/dashboard_admin.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public function dashboardGuru() {
        $nama    = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Guru';
        $initial = strtoupper(substr($nama, 0, 2));

        $title = "Dashboard Guru - SIAKAD";
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/dashboard_guru.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public function dashboardSiswa() {
        $nama    = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Siswa';
        $initial = strtoupper(substr($nama, 0, 2));

        $title = "Dashboard Siswa - SIAKAD";
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/dashboard_siswa.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }
}
