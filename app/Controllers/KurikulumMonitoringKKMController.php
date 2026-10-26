<?php

namespace App\Controllers;

use App\Core\Database;

class KurikulumMonitoringKKMController {
    
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /admin/login");
            exit;
        }

        $title_page = "Monitoring KKM";
        $db_siakad = Database::connect('siakad');
        
        // Fetch all mata pelajaran
        $stmt = $db_siakad->query("SELECT id, nama_mapel, kelompok, kkm_kelas_10, kkm_kelas_11, kkm_kelas_12 FROM mata_pelajaran ORDER BY kelompok ASC, nama_mapel ASC");
        $mapels = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Group by kelompok
        $grouped_mapels = [];
        foreach ($mapels as $m) {
            $kel = $m['kelompok'] ?: 'Lainnya';
            $kel = "Kelompok " . $kel;
            if (!isset($grouped_mapels[$kel])) {
                $grouped_mapels[$kel] = [];
            }
            $grouped_mapels[$kel][] = $m;
        }

        include __DIR__ . '/../../resources/views/admin/kurikulum_monitoring_kkm.php';
    }

    public function save() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $raw_data = file_get_contents('php://input');
        $data = json_decode($raw_data, true);

        if (!$data || !is_array($data)) {
            echo json_encode(['success' => false, 'message' => 'Invalid data format']);
            exit;
        }

        $db_siakad = Database::connect('siakad');
        
        try {
            $db_siakad->beginTransaction();
            
            $stmt = $db_siakad->prepare("UPDATE mata_pelajaran SET kkm_kelas_10 = :k10, kkm_kelas_11 = :k11, kkm_kelas_12 = :k12 WHERE id = :id");
            
            foreach ($data as $item) {
                if (!isset($item['id'])) continue;
                
                $k10 = (isset($item['kkm_10']) && $item['kkm_10'] !== '') ? intval($item['kkm_10']) : null;
                $k11 = (isset($item['kkm_11']) && $item['kkm_11'] !== '') ? intval($item['kkm_11']) : null;
                $k12 = (isset($item['kkm_12']) && $item['kkm_12'] !== '') ? intval($item['kkm_12']) : null;
                
                $stmt->execute([
                    ':k10' => $k10,
                    ':k11' => $k11,
                    ':k12' => $k12,
                    ':id' => intval($item['id'])
                ]);
            }
            
            $db_siakad->commit();
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Semua nilai KKM berhasil disimpan!']);
        } catch (\Exception $e) {
            $db_siakad->rollBack();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan KKM: ' . $e->getMessage()]);
        }
        exit;
    }
}
