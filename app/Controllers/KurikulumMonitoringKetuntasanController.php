<?php

namespace App\Controllers;

use App\Core\Database;

class KurikulumMonitoringKetuntasanController {
    
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /admin/login");
            exit;
        }

        $db_aula = Database::connect('aula');
        $db_siakad = Database::connect('siakad');
        
        // Fetch Guru
        $stmtGuru = $db_aula->query("SELECT id, nama FROM guru ORDER BY nama ASC");
        $guruList = $stmtGuru->fetchAll(\PDO::FETCH_ASSOC);
        
        // Fetch Kelas
        $stmtKelas = $db_siakad->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC");
        $kelasList = $stmtKelas->fetchAll(\PDO::FETCH_ASSOC);
        
        // Fetch Mapel
        $stmtMapel = $db_siakad->query("SELECT id, nama_mapel FROM mata_pelajaran ORDER BY nama_mapel ASC");
        $mapelList = $stmtMapel->fetchAll(\PDO::FETCH_ASSOC);

        // Filters
        $filter_guru = isset($_GET['guru_id']) ? (int)$_GET['guru_id'] : 0;
        $filter_kelas = isset($_GET['kelas_id']) ? (int)$_GET['kelas_id'] : 0;
        $filter_mapel = isset($_GET['mapel_id']) ? (int)$_GET['mapel_id'] : 0;
        $filter_evaluasi = isset($_GET['jenis_evaluasi']) ? $_GET['jenis_evaluasi'] : '';

        // Check if all filters are set to show data
        $is_filtered = ($filter_guru > 0 && $filter_kelas > 0 && $filter_mapel > 0 && $filter_evaluasi !== '');

        $data = [];
        if ($is_filtered) {
            // Data will be fetched here when fully implemented.
            // For now, it will just trigger the table UI.
        }

        // Include view directly (following Zamrud UI standalone shell rules)
        include __DIR__ . '/../../resources/views/admin/kurikulum_rekap_ketuntasan.php';
    }
}
