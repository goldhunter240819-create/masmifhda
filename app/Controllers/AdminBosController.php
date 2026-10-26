<?php
namespace App\Controllers;

use App\Core\Database;
use Exception;
use PDO;

class AdminBosController {
    
    private function checkAccess() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . base_url('login'));
            exit;
        }
        
        $user_id = intval($_SESSION['user_id']);
        $role_id = intval($_SESSION['role_id'] ?? 0);
        
        $db = Database::connect('core');
        
        // Super Admin & Developer langsung akses
        if (in_array($role_id, [1, 99])) {
            return $db;
        }
        
        // Cek app_admins untuk BOS
        $stmt = $db->prepare("SELECT id FROM app_admins WHERE user_id = ? AND app_code = 'bos'");
        $stmt->execute([$user_id]);
        if (!$stmt->fetch()) {
            echo "Akses Ditolak: Anda tidak memiliki akses ke Modul Keuangan BOS.";
            exit;
        }
        
        return $db;
    }

    public function overview() {
        $db = $this->checkAccess();
        
        // Ringkasan Pemasukan & Pengeluaran BKU
        $r_bku = $db->query("SELECT 
            SUM(penerimaan) as total_penerimaan,
            SUM(pengeluaran) as total_pengeluaran
            FROM bos_bku")->fetch(PDO::FETCH_ASSOC);
            
        $totalPenerimaan = $r_bku ? floatval($r_bku['total_penerimaan']) : 0;
        $totalPengeluaran = $r_bku ? floatval($r_bku['total_pengeluaran']) : 0;
        $saldoAkhir = $totalPenerimaan - $totalPengeluaran;
        
        // Ringkasan Pajak
        $r_pajak = $db->query("SELECT 
            SUM(pajak_ppn) as t_ppn,
            SUM(pajak_pph21) as t_pph21,
            SUM(pajak_pph22) as t_pph22,
            SUM(pajak_pph23) as t_pph23
            FROM bos_bku")->fetch(PDO::FETCH_ASSOC);
            
        $totalPajak = 0;
        if($r_pajak) {
            $totalPajak = floatval($r_pajak['t_ppn']) + floatval($r_pajak['t_pph21']) + floatval($r_pajak['t_pph22']) + floatval($r_pajak['t_pph23']);
        }

        // Serapan RKAS per SNP (Standar Nasional Pendidikan)
        $snp_list = $db->query("SELECT 
            snp_id, 
            SUM(total) as anggaran
            FROM bos_rkas 
            GROUP BY snp_id")->fetchAll(PDO::FETCH_ASSOC);
            
        // 8 Standar Nasional Pendidikan (hardcode mapping untuk visual)
        $standar_nasional = [
            1 => 'Standar Kompetensi Lulusan',
            2 => 'Standar Isi',
            3 => 'Standar Proses',
            4 => 'Standar Penilaian Pendidikan',
            5 => 'Standar Tenaga Kependidikan',
            6 => 'Standar Sarana dan Prasarana',
            7 => 'Standar Pengelolaan',
            8 => 'Standar Pembiayaan'
        ];

        $active_page = 'bos_overview';
        include __DIR__ . '/../../resources/views/admin/bos/overview.php';
    }

    public function rkas() {
        $db = $this->checkAccess();
        
        $tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : date('Y');
        
        $rkas_list = $db->query("SELECT * FROM bos_rkas WHERE tahun_anggaran = $tahun ORDER BY snp_id ASC, id DESC")->fetchAll(PDO::FETCH_ASSOC);
        
        $standar_nasional = [
            1 => 'Standar Kompetensi Lulusan',
            2 => 'Standar Isi',
            3 => 'Standar Proses',
            4 => 'Standar Penilaian Pendidikan',
            5 => 'Standar Tenaga Kependidikan',
            6 => 'Standar Sarana dan Prasarana',
            7 => 'Standar Pengelolaan',
            8 => 'Standar Pembiayaan'
        ];

        $active_page = 'bos_rkas';
        include __DIR__ . '/../../resources/views/admin/bos/rkas.php';
    }

    public function saveRkas() {
        $db = $this->checkAccess();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tahun_anggaran = intval($_POST['tahun_anggaran'] ?? date('Y'));
            $snp_id = intval($_POST['snp_id'] ?? 1);
            $kode_rekening = trim($_POST['kode_rekening'] ?? '');
            $uraian = trim($_POST['uraian'] ?? '');
            $volume = intval($_POST['volume'] ?? 1);
            $satuan = trim($_POST['satuan'] ?? '');
            $harga_satuan = floatval(str_replace(['Rp', '.', ' '], '', $_POST['harga_satuan'] ?? 0));
            $total = $volume * $harga_satuan;

            $stmt = $db->prepare("INSERT INTO bos_rkas (tahun_anggaran, snp_id, kode_rekening, uraian, volume, satuan, harga_satuan, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$tahun_anggaran, $snp_id, $kode_rekening, $uraian, $volume, $satuan, $harga_satuan, $total]);
            
            header("Location: " . base_url("admin/bos/rkas?tahun=$tahun_anggaran&msg=saved"));
            exit;
        }
    }

    public function deleteRkas($id) {
        $db = $this->checkAccess();
        
        $db->prepare("DELETE FROM bos_rkas WHERE id = ?")->execute([$id]);
        
        $ref = $_SERVER['HTTP_REFERER'] ?? base_url("admin/bos/rkas");
        header("Location: " . $ref);
        exit;
    }

    public function bku() {
        $db = $this->checkAccess();
        
        $bku_list = $db->query("SELECT b.*, r.uraian as rkas_uraian 
            FROM bos_bku b 
            LEFT JOIN bos_rkas r ON b.rkas_id = r.id 
            ORDER BY b.tanggal DESC, b.id DESC LIMIT 500")->fetchAll(PDO::FETCH_ASSOC);
            
        $rkas_dropdown = $db->query("SELECT id, uraian, total FROM bos_rkas ORDER BY tahun_anggaran DESC, snp_id ASC")->fetchAll(PDO::FETCH_ASSOC);

        $active_page = 'bos_bku';
        include __DIR__ . '/../../resources/views/admin/bos/bku.php';
    }

    public function saveBku() {
        $db = $this->checkAccess();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
            $no_bukti = trim($_POST['no_bukti'] ?? '');
            $uraian = trim($_POST['uraian'] ?? '');
            $tipe = $_POST['tipe'] ?? 'pengeluaran';
            
            $nominal = floatval(str_replace(['Rp', '.', ' '], '', $_POST['nominal'] ?? 0));
            $penerimaan = ($tipe == 'penerimaan') ? $nominal : 0;
            $pengeluaran = ($tipe == 'pengeluaran') ? $nominal : 0;
            
            $jenis_transaksi = $_POST['jenis_transaksi'] ?? 'Tunai';
            
            $pajak_ppn = floatval(str_replace(['Rp', '.', ' '], '', $_POST['pajak_ppn'] ?? 0));
            $pajak_pph21 = floatval(str_replace(['Rp', '.', ' '], '', $_POST['pajak_pph21'] ?? 0));
            $pajak_pph22 = floatval(str_replace(['Rp', '.', ' '], '', $_POST['pajak_pph22'] ?? 0));
            $pajak_pph23 = floatval(str_replace(['Rp', '.', ' '], '', $_POST['pajak_pph23'] ?? 0));
            
            $rkas_id = !empty($_POST['rkas_id']) ? intval($_POST['rkas_id']) : null;
            
            // Hitung Saldo Berjalan (sangat disederhanakan untuk MVP)
            $last_saldo = 0;
            $r = $db->query("SELECT saldo FROM bos_bku ORDER BY id DESC LIMIT 1")->fetch();
            if ($r) $last_saldo = floatval($r['saldo']);
            
            $saldo_baru = $last_saldo + $penerimaan - $pengeluaran;

            $stmt = $db->prepare("INSERT INTO bos_bku 
                (tanggal, no_bukti, uraian, penerimaan, pengeluaran, saldo, jenis_transaksi, pajak_ppn, pajak_pph21, pajak_pph22, pajak_pph23, rkas_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$tanggal, $no_bukti, $uraian, $penerimaan, $pengeluaran, $saldo_baru, $jenis_transaksi, $pajak_ppn, $pajak_pph21, $pajak_pph22, $pajak_pph23, $rkas_id]);
            
            header("Location: " . base_url("admin/bos/bku?msg=saved"));
            exit;
        }
    }

    public function deleteBku($id) {
        $db = $this->checkAccess();
        
        $db->prepare("DELETE FROM bos_bku WHERE id = ?")->execute([$id]);
        
        $ref = $_SERVER['HTTP_REFERER'] ?? base_url("admin/bos/bku");
        header("Location: " . $ref);
        exit;
    }

    public function pajak() {
        $db = $this->checkAccess();
        
        // Menampilkan hanya transaksi BKU yang memiliki nilai pajak > 0
        $pajak_list = $db->query("SELECT * FROM bos_bku 
            WHERE pajak_ppn > 0 OR pajak_pph21 > 0 OR pajak_pph22 > 0 OR pajak_pph23 > 0 
            ORDER BY tanggal DESC, id DESC LIMIT 500")->fetchAll(PDO::FETCH_ASSOC);

        $active_page = 'bos_pajak';
        include __DIR__ . '/../../resources/views/admin/bos/pajak.php';
    }

    public function pemasukan() {
        $db = $this->checkAccess();
        $bku_list = $db->query("SELECT b.*, r.uraian as rkas_uraian FROM bos_bku b LEFT JOIN bos_rkas r ON b.rkas_id = r.id WHERE b.penerimaan > 0 ORDER BY b.tanggal DESC, b.id DESC LIMIT 500")->fetchAll(PDO::FETCH_ASSOC);
        $active_page = 'bos_pemasukan';
        $page_title = 'Penerimaan Dana BOS';
        $page_icon = 'arrow-down-left';
        $page_color = '#0284c7';
        include __DIR__ . '/../../resources/views/admin/bos/buku_rinci.php';
    }

    public function pengeluaran() {
        $db = $this->checkAccess();
        $bku_list = $db->query("SELECT b.*, r.uraian as rkas_uraian FROM bos_bku b LEFT JOIN bos_rkas r ON b.rkas_id = r.id WHERE b.pengeluaran > 0 ORDER BY b.tanggal DESC, b.id DESC LIMIT 500")->fetchAll(PDO::FETCH_ASSOC);
        $active_page = 'bos_pengeluaran';
        $page_title = 'Belanja & Pengeluaran BOS';
        $page_icon = 'arrow-up-right';
        $page_color = '#ef4444';
        include __DIR__ . '/../../resources/views/admin/bos/buku_rinci.php';
    }

    public function bank() {
        $db = $this->checkAccess();
        $bku_list = $db->query("SELECT b.*, r.uraian as rkas_uraian FROM bos_bku b LEFT JOIN bos_rkas r ON b.rkas_id = r.id WHERE b.jenis_transaksi = 'Bank' ORDER BY b.tanggal DESC, b.id DESC LIMIT 500")->fetchAll(PDO::FETCH_ASSOC);
        $active_page = 'bos_bank';
        $page_title = 'Buku Pembantu Bank';
        $page_icon = 'building';
        $page_color = '#8b5cf6';
        include __DIR__ . '/../../resources/views/admin/bos/buku_rinci.php';
    }

    public function tunai() {
        $db = $this->checkAccess();
        $bku_list = $db->query("SELECT b.*, r.uraian as rkas_uraian FROM bos_bku b LEFT JOIN bos_rkas r ON b.rkas_id = r.id WHERE b.jenis_transaksi = 'Tunai' ORDER BY b.tanggal DESC, b.id DESC LIMIT 500")->fetchAll(PDO::FETCH_ASSOC);
        $active_page = 'bos_tunai';
        $page_title = 'Buku Pembantu Kas Tunai';
        $page_icon = 'wallet';
        $page_color = '#10b981';
        include __DIR__ . '/../../resources/views/admin/bos/buku_rinci.php';
    }

    public function akses() {
        $db = $this->checkAccess();
        
        // Hanya Super Admin & Root yang bisa mengelola hak akses
        if (!in_array($_SESSION['role_id'] ?? 0, [1, 99])) {
            header("Location: " . base_url('admin/bos'));
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if ($_POST['action'] == 'add_akses') {
                $user_id = intval($_POST['user_id'] ?? 0);
                if ($user_id > 0) {
                    $db->prepare("INSERT IGNORE INTO app_admins (user_id, app_code) VALUES (?, 'bos')")->execute([$user_id]);
                }
            } elseif ($_POST['action'] == 'del_akses') {
                $id = intval($_POST['id'] ?? 0);
                $user_id = intval($_POST['user_id'] ?? 0);
                if ($id > 0) {
                    $db->prepare("DELETE FROM app_admins WHERE id = ? AND app_code = 'bos'")->execute([$id]);
                }
                if ($user_id > 0) {
                    $db->prepare("DELETE FROM app_admins WHERE user_id = ? AND app_code = 'bos'")->execute([$user_id]);
                }
            }
            header("Location: " . base_url('admin/bos/akses'));
            exit;
        }

        // List guru yang bisa ditambahkan (kecuali admin & root)
        $users = $db->query("SELECT u.id, u.username, COALESCE(g.nama, u.username) as nama, r.name as role_name 
            FROM users u 
            LEFT JOIN guru g ON u.id = g.user_id 
            LEFT JOIN roles r ON u.role_id = r.id 
            WHERE u.role_id = 2 
            ORDER BY g.nama ASC, u.username ASC")->fetchAll(PDO::FETCH_ASSOC);

        // List admin bos saat ini
        $admins = $db->query("SELECT a.id, a.user_id, COALESCE(g.nama, u.username) as nama, u.username 
            FROM app_admins a 
            JOIN users u ON a.user_id = u.id 
            LEFT JOIN guru g ON u.id = g.user_id 
            WHERE a.app_code = 'bos'")->fetchAll(PDO::FETCH_ASSOC);

        $active_page = 'bos_akses';
        include __DIR__ . '/../../resources/views/admin/bos/akses.php';
    }
}
