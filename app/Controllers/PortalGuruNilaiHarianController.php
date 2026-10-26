<?php
namespace App\Controllers;

use App\Core\Database;

class PortalGuruNilaiHarianController {
    
    private static function guard() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id']) || $_SESSION['role_id'] != 2) {
            redirect('/login');
            exit;
        }
    }
    
    private static function getGuruId() {
        $db = Database::connect('core');
        $stmt = $db->prepare("SELECT id FROM guru WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $id = $stmt->fetchColumn();
        if (!$id) {
            echo "Akses ditolak. Anda bukan guru.";
            exit;
        }
        return $id;
    }
    
    private static function render($view, $data = []) {
        extract($data);
        $title_page = $title ?? 'Nilai Harian';
        
        include __DIR__ . '/../../resources/views/' . $view . '.php';
    }
    public static function index() {
        self::guard();
        $db = Database::connect('core');
        
        $guru_id = self::getGuruId();
        
        // Ambil riwayat nilai harian yang pernah diinput guru ini
        $riwayat = $db->query("
            SELECT nh.*, k.nama_kelas, m.nama_mapel, 
                   (SELECT COUNT(*) FROM nilai_harian_detail WHERE nilai_harian_id = nh.id) as jumlah_siswa,
                   (SELECT AVG(nilai) FROM nilai_harian_detail WHERE nilai_harian_id = nh.id) as rata_rata
            FROM nilai_harian nh
            JOIN kelas k ON nh.kelas_id = k.id
            JOIN mapel m ON nh.mapel_id = m.id
            WHERE nh.guru_id = $guru_id
            ORDER BY nh.tanggal DESC, nh.id DESC
        ")->fetchAll();
        
        self::render('portal/guru/nilai_harian_list', [
            'riwayat' => $riwayat,
            'title' => 'Rekap Nilai Harian'
        ]);
    }
    
    public static function detail($id) {
        self::guard();
        $db = Database::connect('core');
        $id = intval($id);
        $guru_id = self::getGuruId();
        
        $header = $db->query("
            SELECT nh.*, k.nama_kelas, m.nama_mapel
            FROM nilai_harian nh
            JOIN kelas k ON nh.kelas_id = k.id
            JOIN mapel m ON nh.mapel_id = m.id
            WHERE nh.id = $id AND nh.guru_id = $guru_id
        ")->fetch();
        
        if (!$header) {
            die("Data tidak ditemukan atau Anda tidak memiliki akses.");
        }
        
        // Use siakad_db if siswa data is there, but here we assume core_db has it based on context or we can join
        $siakad_db = Database::connect('siakad');
        
        // Fetch details
        $details = $db->query("
            SELECT nd.*, s.nama, s.nis 
            FROM nilai_harian_detail nd
            JOIN siswa s ON nd.siswa_id = s.id
            WHERE nd.nilai_harian_id = $id
            ORDER BY s.nama ASC
        ")->fetchAll();
        
        self::render('portal/guru/nilai_harian_detail', [
            'header' => $header,
            'details' => $details,
            'title' => 'Detail Nilai Harian'
        ]);
    }
    
    public static function delete($id) {
        self::guard();
        $db = Database::connect('core');
        $id = intval($id);
        $guru_id = self::getGuruId();
        
        // Verify ownership
        $check = $db->query("SELECT id FROM nilai_harian WHERE id = $id AND guru_id = $guru_id")->fetch();
        if ($check) {
            $db->prepare("DELETE FROM nilai_harian_detail WHERE nilai_harian_id = ?")->execute([$id]);
            $db->prepare("DELETE FROM nilai_harian WHERE id = ?")->execute([$id]);
        }
        
        redirect("/guru/nilai_harian?msg=deleted");
        exit;
    }
}
