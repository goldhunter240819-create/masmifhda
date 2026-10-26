<?php

namespace App\Controllers;

use App\Core\Database;
use PDO;

class SiakadTugasController {

    public function index() {
        $db = Database::connect('siakad');
        
        $tanggal_filter = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
        $kelas_id_filter = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : '';

        // Ambil daftar kelas untuk filter
        $kelas = $db->query("SELECT id, nama_kelas, tingkat FROM aula_db.kelas ORDER BY tingkat, nama_kelas")->fetchAll(PDO::FETCH_ASSOC);

        $where = "td.tanggal = ?";
        $params = [$tanggal_filter];
        
        if (!empty($kelas_id_filter)) {
            $where .= " AND td.kelas_id = ?";
            $params[] = $kelas_id_filter;
        }

        $stmt = $db->prepare("
            SELECT td.*, k.nama_kelas, m.nama_mapel, g.nama as nama_guru 
            FROM tugas_daring td
            JOIN aula_db.kelas k ON td.kelas_id = k.id
            JOIN mata_pelajaran m ON td.mata_pelajaran_id = m.id
            JOIN aula_db.guru g ON td.guru_id = g.id
            WHERE $where
            ORDER BY k.tingkat, k.nama_kelas, td.created_at DESC
        ");
        $stmt->execute($params);
        $tugas_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $title = "Data Tugas Daring - SIAKAD";
        $page_title = "Data Tugas Daring";
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/tugas_daring/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public function cetak() {
        $db = Database::connect('siakad');
        
        $tugas_id = isset($_GET['tugas_id']) ? (int)$_GET['tugas_id'] : 0;
        
        if ($tugas_id > 0) {
            $stmt = $db->prepare("
                SELECT td.*, m.nama_mapel, g.nama as nama_guru, k.nama_kelas 
                FROM tugas_daring td
                JOIN mata_pelajaran m ON td.mata_pelajaran_id = m.id
                JOIN aula_db.guru g ON td.guru_id = g.id
                JOIN aula_db.kelas k ON td.kelas_id = k.id
                WHERE td.id = ?
            ");
            $stmt->execute([$tugas_id]);
            $tugas_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($tugas_list)) die("Tugas tidak ditemukan.");
            
            $kelas_info = ['nama_kelas' => $tugas_list[0]['nama_kelas']];
            $tanggal_filter = $tugas_list[0]['tanggal'];
        } else {
            $tanggal_filter = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
            $kelas_id_filter = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : '';

            if (empty($kelas_id_filter)) {
                die("Silakan pilih kelas terlebih dahulu sebelum mencetak.");
            }

            // Ambil info kelas
            $stmt_k = $db->prepare("SELECT nama_kelas FROM aula_db.kelas WHERE id = ?");
            $stmt_k->execute([$kelas_id_filter]);
            $kelas_info = $stmt_k->fetch(PDO::FETCH_ASSOC);
            if (!$kelas_info) {
                die("Kelas tidak ditemukan.");
            }

            $stmt = $db->prepare("
                SELECT td.*, m.nama_mapel, g.nama as nama_guru 
                FROM tugas_daring td
                JOIN mata_pelajaran m ON td.mata_pelajaran_id = m.id
                JOIN aula_db.guru g ON td.guru_id = g.id
                WHERE td.tanggal = ? AND td.kelas_id = ?
                ORDER BY td.created_at ASC
            ");
            $stmt->execute([$tanggal_filter, $kelas_id_filter]);
            $tugas_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        include __DIR__ . '/../../resources/views/siakad/tugas_daring/cetak.php';
    }

}
