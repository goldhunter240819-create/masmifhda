<?php
namespace App\Controllers;

use App\Core\Database;
use PDO;

class PortalTabunganController {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header("Location: " . base_url('login'));
            exit;
        }

        $user_id = intval($_SESSION['user_id']);
        $role_id = intval($_SESSION['role_id'] ?? 0);

        // Jika Admin (1, 99) langsung arahkan ke dashboard Admin Tabungan
        if (in_array($role_id, [1, 99])) {
            header("Location: " . base_url('admin/tabungan'));
            exit;
        }

        $db = Database::connect();

        // Cek apakah Guru/Petugas ini terdaftar sebagai Admin Tabungan
        $cekAdmin = $db->prepare("SELECT id FROM keuangan_tabungan_admin WHERE user_id = ?");
        $cekAdmin->execute([$user_id]);
        if ($cekAdmin->fetch()) {
            header("Location: " . base_url('admin/tabungan'));
            exit;
        }

        // Khusus Siswa (Role 3)
        $stmtSiswa = $db->prepare("SELECT s.*, k.nama_kelas FROM siswa s LEFT JOIN kelas k ON s.kelas_id = k.id WHERE s.user_id = ? ORDER BY s.id DESC LIMIT 1");
        $stmtSiswa->execute([$user_id]);
        $siswa = $stmtSiswa->fetch(PDO::FETCH_ASSOC);

        $tabungan = null;
        $mutasi_list = [];

        if ($siswa) {
            $stmtTab = $db->prepare("SELECT * FROM keuangan_tabungan WHERE siswa_id = ?");
            $stmtTab->execute([$siswa['id']]);
            $tabungan = $stmtTab->fetch(PDO::FETCH_ASSOC);

            if ($tabungan) {
                $stmtMut = $db->prepare("SELECT * FROM keuangan_tabungan_mutasi WHERE siswa_id = ? ORDER BY id DESC LIMIT 100");
                $stmtMut->execute([$siswa['id']]);
                $mutasi_list = $stmtMut->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        include __DIR__ . '/../../resources/views/portal/tabungan_siswa.php';
    }
}
