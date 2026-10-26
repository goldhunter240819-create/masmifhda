<?php

namespace App\Controllers;

use App\Core\Database;

class SiakadGuruWaliController {

    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Hanya Admin/Kepala Madrasah yang bisa melihat menu ini
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            redirect('/login');
            exit;
        }

        $db_core = Database::connect('core');

        // Ambil daftar guru
        $gurus = $db_core->query("SELECT id, nama, nip, no_hp FROM guru ORDER BY nama ASC")->fetchAll();
        
        // Ambil daftar mapping guru_wali_siswa
        $mappings = $db_core->query("SELECT * FROM guru_wali_siswa")->fetchAll();
        
        // Kelompokkan siswa berdasarkan guru
        $guru_siswa_map = [];
        $assigned_siswa_ids = [];
        foreach($mappings as $m) {
            $guru_siswa_map[$m['guru_id']][] = $m['siswa_id'];
            $assigned_siswa_ids[] = $m['siswa_id'];
        }
        
        // Ambil data siswa untuk lookup nama & kelas
        // Optimization: Ambil semua siswa lalu map, atau ambil yang diperlukan. Kita ambil semua yang status aktif
        $siswas = $db_core->query("
            SELECT s.id, s.nama, s.nis, s.kelas_id, k.nama_kelas 
            FROM siswa s 
            LEFT JOIN kelas k ON s.kelas_id = k.id 
            WHERE s.status = 'Aktif'
            ORDER BY k.tingkat ASC, k.nama_kelas ASC, s.nama ASC
        ")->fetchAll();
        
        $siswa_map = [];
        foreach($siswas as $s) {
            $siswa_map[$s['id']] = $s;
        }
        
        // Tambahkan jumlah anak asuh ke setiap guru
        foreach($gurus as &$g) {
            $g['jumlah_anak_asuh'] = isset($guru_siswa_map[$g['id']]) ? count($guru_siswa_map[$g['id']]) : 0;
        }
        unset($g);

        $selected_guru_id = isset($_GET['guru_id']) ? intval($_GET['guru_id']) : 0;
        
        $anak_asuh_list = [];
        if ($selected_guru_id > 0 && isset($guru_siswa_map[$selected_guru_id])) {
            foreach($guru_siswa_map[$selected_guru_id] as $sid) {
                if(isset($siswa_map[$sid])) {
                    $anak_asuh_list[] = [
                        'mapping_id' => $this->getMappingId($mappings, $selected_guru_id, $sid),
                        'siswa' => $siswa_map[$sid]
                    ];
                }
            }
        }
        
        // Filter: hanya siswa yang BELUM memiliki guru wali yang bisa dipilih
        $available_siswas = array_filter($siswas, function($s) use ($assigned_siswa_ids) {
            return !in_array($s['id'], $assigned_siswa_ids);
        });
        
        // Ambil daftar kelas unik untuk filter di view
        $kelas_list = [];
        foreach($available_siswas as $as) {
            $kname = $as['nama_kelas'] ?: 'Tanpa Kelas';
            $kelas_list[$kname] = $as['kelas_id'] ?: 0;
        }
        ksort($kelas_list);

        $title_page = "Manajemen Guru Wali";
        
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/guru_wali.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }
    
    private function getMappingId($mappings, $guru_id, $siswa_id) {
        foreach($mappings as $m) {
            if($m['guru_id'] == $guru_id && $m['siswa_id'] == $siswa_id) return $m['id'];
        }
        return 0;
    }

    public function assign() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }

        $guru_id = isset($_POST['guru_id']) ? intval($_POST['guru_id']) : 0;
        $siswa_ids = isset($_POST['siswa_ids']) ? $_POST['siswa_ids'] : [];

        if ($guru_id <= 0 || empty($siswa_ids)) {
            echo json_encode(['status' => 'error', 'message' => 'Pilih guru dan minimal satu siswa']);
            exit;
        }

        $db = Database::connect('core');
        $successCount = 0;

        try {
            $db->beginTransaction();
            $stmt = $db->prepare("INSERT IGNORE INTO guru_wali_siswa (guru_id, siswa_id) VALUES (?, ?)");
            foreach($siswa_ids as $sid) {
                $sid = intval($sid);
                if($sid > 0) {
                    $stmt->execute([$guru_id, $sid]);
                    if($stmt->rowCount() > 0) $successCount++;
                }
            }
            $db->commit();
            echo json_encode(['status' => 'success', 'message' => "Berhasil menambahkan $successCount anak asuh."]);
        } catch (\Exception $e) {
            $db->rollBack();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function unassign() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }

        $mapping_id = isset($_POST['mapping_id']) ? intval($_POST['mapping_id']) : 0;
        
        if ($mapping_id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
            exit;
        }

        $db = Database::connect('core');
        try {
            $stmt = $db->prepare("DELETE FROM guru_wali_siswa WHERE id = ?");
            $stmt->execute([$mapping_id]);
            echo json_encode(['status' => 'success', 'message' => 'Anak asuh berhasil dihapus dari daftar.']);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
}
