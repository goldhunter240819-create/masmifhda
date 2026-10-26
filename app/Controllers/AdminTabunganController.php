<?php
namespace App\Controllers;

use App\Core\Database;
use Exception;
use PDO;

class AdminTabunganController {
    
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
        
        // Cek app_admins untuk tabungan
        $stmt = $db->prepare("SELECT id FROM app_admins WHERE user_id = ? AND app_code = 'tabungan'");
        $stmt->execute([$user_id]);
        if (!$stmt->fetch()) {
            echo "Akses Ditolak: Anda tidak memiliki akses ke Modul Tabungan.";
            exit;
        }
        
        return $db;
    }

    public function overview() {
        $db = $this->checkAccess();
        
        // Total Saldo
        $r_saldo = $db->query("SELECT SUM(saldo) as t FROM keuangan_tabungan")->fetch();
        $totalSaldo = $r_saldo ? floatval($r_saldo['t']) : 0;
        
        // Total Nasabah / Rekening Aktif
        $r_nasabah = $db->query("SELECT COUNT(*) as c FROM keuangan_tabungan")->fetch();
        $totalNasabah = $r_nasabah ? intval($r_nasabah['c']) : 0;

        // Total Siswa Madrasah
        $r_siswa = $db->query("SELECT COUNT(*) as c FROM siswa")->fetch();
        $totalSiswa = $r_siswa ? intval($r_siswa['c']) : 0;
        
        // Total Mutasi Hari Ini
        $r_mutasi = $db->query("SELECT 
            SUM(CASE WHEN jenis_mutasi = 'Setor' THEN jumlah ELSE 0 END) as setoran,
            SUM(CASE WHEN jenis_mutasi = 'Tarik' THEN jumlah ELSE 0 END) as penarikan,
            COUNT(*) as total_tx
            FROM keuangan_tabungan_mutasi WHERE DATE(tanggal) = CURDATE()")->fetch();
            
        $setoranHariIni = $r_mutasi ? floatval($r_mutasi['setoran']) : 0;
        $penarikanHariIni = $r_mutasi ? floatval($r_mutasi['penarikan']) : 0;
        $transaksiHariIni = $r_mutasi ? intval($r_mutasi['total_tx']) : 0;
        
        // Riwayat Terbaru
        $mutasi_terbaru = $db->query("SELECT m.*, s.nama, s.nis, k.nama_kelas 
            FROM keuangan_tabungan_mutasi m 
            JOIN siswa s ON m.siswa_id = s.id 
            LEFT JOIN kelas k ON s.kelas_id = k.id 
            ORDER BY m.id DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

        $active_page = 'overview';
        include __DIR__ . '/../../resources/views/admin/tabungan/overview.php';
    }

    public function kasir() {
        $db = $this->checkAccess();
        
        $message = '';
        $status = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $siswa_id = intval($_POST['siswa_id'] ?? 0);
            $jenis = $_POST['jenis_mutasi'] ?? 'Setor';
            $nominal = floatval($_POST['nominal'] ?? 0);
            $keterangan = trim($_POST['keterangan'] ?? '');
            
            if ($siswa_id <= 0 || $nominal <= 0) {
                $status = 'error';
                $message = 'Pilih siswa dan masukkan nominal yang valid!';
            } else {
                try {
                    $db->beginTransaction();
                    
                    // Cek tabungan
                    $stmt_cek = $db->prepare("SELECT id, saldo FROM keuangan_tabungan WHERE siswa_id = ? FOR UPDATE");
                    $stmt_cek->execute([$siswa_id]);
                    $t = $stmt_cek->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$t) {
                        throw new Exception("Siswa ini belum membuka rekening tabungan! Silakan aktifkan rekening terlebih dahulu di menu Rekening Siswa.");
                    }
                    $tabungan_id = $t['id'];
                    $saldo_sekarang = floatval($t['saldo']);
                    
                    if ($jenis === 'Tarik' && $saldo_sekarang < $nominal) {
                        throw new Exception("Saldo tidak mencukupi! Saldo saat ini: Rp " . number_format($saldo_sekarang, 0, ',', '.'));
                    }
                    
                    $saldo_baru = ($jenis === 'Setor') ? ($saldo_sekarang + $nominal) : ($saldo_sekarang - $nominal);
                    
                    // Insert mutasi
                    $stmt_mutasi = $db->prepare("INSERT INTO keuangan_tabungan_mutasi (siswa_id, jenis_mutasi, jumlah, saldo_sebelum, saldo_sesudah, tanggal, keterangan) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
                    $stmt_mutasi->execute([$siswa_id, $jenis, $nominal, $saldo_sekarang, $saldo_baru, $keterangan]);
                    
                    // Update saldo
                    if ($jenis === 'Setor') {
                        $db->prepare("UPDATE keuangan_tabungan SET saldo = saldo + ? WHERE id = ?")->execute([$nominal, $tabungan_id]);
                    } else {
                        $db->prepare("UPDATE keuangan_tabungan SET saldo = saldo - ? WHERE id = ?")->execute([$nominal, $tabungan_id]);
                    }
                    
                    $db->commit();

                    try {
                        require_once __DIR__ . '/../Services/OneSignalService.php';
                        $verb = ($jenis === 'Setor') ? 'Setoran' : 'Penarikan';
                        $nominal_rp = "Rp " . number_format($nominal, 0, ',', '.');
                        $notif_title = "Mutasi Tabungan: " . $verb;
                        $notif_msg = "Terdapat transaksi $verb tabungan sebesar $nominal_rp pada rekening siswa. Saldo saat ini: Rp " . number_format($saldo_baru, 0, ',', '.');
                        \App\Services\OneSignalService::sendNotification(
                            $notif_title, 
                            $notif_msg, 
                            "https://mismifhda.my.id/apk/index.php",
                            [$siswa_id]
                        );
                    } catch (\Exception $e) {}

                    $status = 'success';
                    $message = "Berhasil memproses $jenis Rp " . number_format($nominal, 0, ',', '.');
                    
                } catch (Exception $e) {
                    if ($db->inTransaction()) {
                        $db->rollBack();
                    }
                    $status = 'error';
                    $message = $e->getMessage();
                }
            }
        }
        
        $active_page = 'kasir';
        include __DIR__ . '/../../resources/views/admin/tabungan/kasir.php';
    }

    public function siswa() {
        $db = $this->checkAccess();
        
        $message = '';
        $status = '';
        
        $filter_kelas = intval($_GET['kelas_id'] ?? 0);
        
        // Handle POST untuk Buka Rekening Baru atau Tutup Rekening
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if ($_POST['action'] === 'buka_rekening') {
                $siswa_id = intval($_POST['siswa_id'] ?? 0);
                $setoran_awal = floatval($_POST['setoran_awal'] ?? 0);
                
                if ($siswa_id > 0) {
                    $cek = $db->prepare("SELECT id FROM keuangan_tabungan WHERE siswa_id = ?");
                    $cek->execute([$siswa_id]);
                    if ($cek->fetch()) {
                        $status = 'error';
                        $message = 'Siswa tersebut sudah memiliki rekening tabungan!';
                    } else {
                        $db->prepare("INSERT INTO keuangan_tabungan (siswa_id, saldo) VALUES (?, ?)")
                           ->execute([$siswa_id, $setoran_awal]);
                           
                        if ($setoran_awal > 0) {
                            $db->prepare("INSERT INTO keuangan_tabungan_mutasi (siswa_id, jenis_mutasi, jumlah, saldo_sebelum, saldo_sesudah, tanggal, keterangan) VALUES (?, 'Setor', ?, 0, ?, NOW(), 'Setoran Awal Pembukaan Rekening')")
                               ->execute([$siswa_id, $setoran_awal, $setoran_awal]);
                        }
                        
                        try {
                            require_once __DIR__ . '/../Services/OneSignalService.php';
                            \App\Services\OneSignalService::sendNotification(
                                "💳 Rekening Tabungan Aktif", 
                                "Selamat! Rekening Tabungan Madrasah Anda telah berhasil diaktifkan.", 
                                "https://mismifhda.my.id/apk/index.php",
                                [$siswa_id]
                            );
                        } catch (\Exception $e) {}

                        $status = 'success';
                        $message = 'Rekening tabungan siswa berhasil dibuka!';
                    }
                }
                
                if (isset($_POST['ajax']) && $_POST['ajax'] == 1) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => $status,
                        'message' => $message,
                        'no_rek' => 'TBG-' . str_pad($siswa_id, 5, '0', STR_PAD_LEFT),
                        'transaksi_url' => base_url('admin/tabungan/kasir?siswa_id=' . $siswa_id),
                        'siswa_id' => $siswa_id,
                        'nama' => $_POST['siswa_nama'] ?? ''
                    ]);
                    exit;
                }
            } elseif ($_POST['action'] === 'buka_semua_kelas') {
                $kelas_target = intval($_POST['kelas_target'] ?? 0);
                if ($kelas_target > 0) {
                    $sw = $db->prepare("SELECT s.id FROM siswa s LEFT JOIN keuangan_tabungan t ON s.id = t.siswa_id WHERE s.kelas_id = ? AND t.id IS NULL");
                    $sw->execute([$kelas_target]);
                    $list = $sw->fetchAll(PDO::FETCH_ASSOC);
                    $c = 0;
                    $ins = $db->prepare("INSERT INTO keuangan_tabungan (siswa_id, saldo) VALUES (?, 0)");
                    foreach ($list as $row) {
                        $ins->execute([$row['id']]);
                        $c++;
                    }
                    $status = 'success';
                    $message = "Berhasil membuka rekening untuk $c siswa di kelas tersebut!";
                }
            } elseif ($_POST['action'] === 'tutup_rekening') {
                $siswa_id = intval($_POST['siswa_id'] ?? 0);
                if ($siswa_id > 0) {
                    $cek = $db->prepare("SELECT saldo FROM keuangan_tabungan WHERE siswa_id = ?");
                    $cek->execute([$siswa_id]);
                    $row = $cek->fetch();
                    if ($row && floatval($row['saldo']) > 0) {
                        $status = 'error';
                        $message = 'Gagal menutup rekening: Saldo masih tersisa Rp ' . number_format($row['saldo'], 0, ',', '.') . '. Silakan tarik saldo terlebih dahulu di Kasir!';
                    } else {
                        $db->prepare("DELETE FROM keuangan_tabungan WHERE siswa_id = ?")->execute([$siswa_id]);
                        $status = 'success';
                        $message = 'Rekening tabungan berhasil ditutup.';
                    }
                }
            }
        }
        
        // Ambil daftar kelas untuk filter
        $kelas_list = $db->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC")->fetchAll(PDO::FETCH_ASSOC);
        
        // Ambil seluruh siswa (baik yang sudah buka rekening maupun belum) berdasarkan filter kelas
        $sql = "SELECT s.id as siswa_id, s.nama, s.nis, k.id as kelas_id, k.nama_kelas, t.id as tabungan_id, t.saldo 
            FROM siswa s 
            LEFT JOIN kelas k ON s.kelas_id = k.id 
            LEFT JOIN keuangan_tabungan t ON s.id = t.siswa_id ";
        $params = [];
        if ($filter_kelas > 0) {
            $sql .= " WHERE s.kelas_id = ? ";
            $params[] = $filter_kelas;
        }
        $sql .= " ORDER BY k.nama_kelas ASC, s.nama ASC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $siswa_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        $active_page = 'siswa';
        include __DIR__ . '/../../resources/views/admin/tabungan/siswa.php';
    }

    public function mutasi() {
        $db = $this->checkAccess();
        
        $role_id = $_SESSION['role_id'] ?? 0;
        $can_delete = in_array($role_id, [1, 99]);
        if (!$can_delete) {
            $cekDel = $db->prepare("SELECT can_delete FROM app_admins WHERE user_id = ? AND app_code = 'tabungan'");
            $cekDel->execute([$_SESSION['user_id']]);
            $adm = $cekDel->fetch();
            if ($adm && $adm['can_delete'] == 1) $can_delete = true;
        }
        
        $mutasi_list = $db->query("SELECT m.*, s.nama, s.nis, k.nama_kelas 
            FROM keuangan_tabungan_mutasi m 
            JOIN siswa s ON m.siswa_id = s.id 
            LEFT JOIN kelas k ON s.kelas_id = k.id 
            ORDER BY m.tanggal DESC LIMIT 500")->fetchAll(PDO::FETCH_ASSOC);
            
        $active_page = 'mutasi';
        include __DIR__ . '/../../resources/views/admin/tabungan/mutasi.php';
    }
    
    public function deleteMutasi() {
        $db = $this->checkAccess();
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = intval($_POST['id'] ?? 0);
                if ($id <= 0) throw new Exception("ID tidak valid!");
                
                $role_id = $_SESSION['role_id'] ?? 0;
                $can_delete = in_array($role_id, [1, 99]);
                if (!$can_delete) {
                    $cekDel = $db->prepare("SELECT can_delete FROM app_admins WHERE user_id = ? AND app_code = 'tabungan'");
                    $cekDel->execute([$_SESSION['user_id']]);
                    $adm = $cekDel->fetch();
                    if (!$adm || $adm['can_delete'] != 1) {
                        throw new Exception("Anda tidak memiliki hak akses untuk menghapus transaksi ini!");
                    }
                }
                
                $db->beginTransaction();
                
                $stmt = $db->prepare("SELECT * FROM keuangan_tabungan_mutasi WHERE id = ? FOR UPDATE");
                $stmt->execute([$id]);
                $mutasi = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$mutasi) throw new Exception("Data mutasi tidak ditemukan!");
                
                // Kembalikan saldo siswa
                if ($mutasi['jenis_mutasi'] === 'Setor') {
                    $db->prepare("UPDATE keuangan_tabungan SET saldo = saldo - ? WHERE siswa_id = ?")->execute([$mutasi['jumlah'], $mutasi['siswa_id']]);
                } else if ($mutasi['jenis_mutasi'] === 'Tarik') {
                    $db->prepare("UPDATE keuangan_tabungan SET saldo = saldo + ? WHERE siswa_id = ?")->execute([$mutasi['jumlah'], $mutasi['siswa_id']]);
                }
                
                // Hapus data mutasi
                $db->prepare("DELETE FROM keuangan_tabungan_mutasi WHERE id = ?")->execute([$id]);
                
                $db->commit();
                echo json_encode(['success' => true, 'message' => 'Transaksi berhasil dihapus dan saldo telah disesuaikan.']);
            } catch (Exception $e) {
                $db->rollBack();
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }
    }
    
    public function akses() {
        $db = $this->checkAccess();
        
        // Hanya Super Admin & Root yang bisa mengelola hak akses
        if (!in_array($_SESSION['role_id'] ?? 0, [1, 99])) {
            header("Location: " . base_url('admin/tabungan'));
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if ($_POST['action'] == 'add_akses') {
                $user_id = intval($_POST['user_id'] ?? 0);
                if ($user_id > 0) {
                    $db->prepare("INSERT IGNORE INTO app_admins (user_id, app_code) VALUES (?, 'tabungan')")->execute([$user_id]);
                    try {
                        $db->prepare("INSERT IGNORE INTO keuangan_tabungan_admin (user_id) VALUES (?)")->execute([$user_id]);
                    } catch(\Exception $e) {}
                }
            } elseif ($_POST['action'] == 'del_akses') {
                $id = intval($_POST['id'] ?? 0);
                $user_id = intval($_POST['user_id'] ?? 0);
                if ($id > 0) {
                    $db->prepare("DELETE FROM app_admins WHERE id = ? AND app_code = 'tabungan'")->execute([$id]);
                }
                if ($user_id > 0) {
                    $db->prepare("DELETE FROM app_admins WHERE user_id = ? AND app_code = 'tabungan'")->execute([$user_id]);
                    try {
                        $db->prepare("DELETE FROM keuangan_tabungan_admin WHERE user_id = ?")->execute([$user_id]);
                    } catch(\Exception $e) {}
                }
            } elseif ($_POST['action'] == 'toggle_delete') {
                $id = intval($_POST['id'] ?? 0);
                $val = intval($_POST['val'] ?? 0);
                if ($id > 0) {
                    $db->prepare("UPDATE app_admins SET can_delete = ? WHERE id = ?")->execute([$val, $id]);
                }
                echo json_encode(['success' => true]);
                exit;
            }
            
            header("Location: " . base_url('admin/tabungan/akses'));
            exit;
        }

        // List guru yang bisa ditambahkan (kecuali admin & root) -> gunakan tabel roles, bukan user_roles
        $users = $db->query("SELECT u.id, u.username, COALESCE(g.nama, u.username) as nama, r.name as role_name 
            FROM users u 
            LEFT JOIN guru g ON u.id = g.user_id 
            LEFT JOIN roles r ON u.role_id = r.id 
            WHERE u.role_id = 2 
            ORDER BY g.nama ASC, u.username ASC")->fetchAll(PDO::FETCH_ASSOC);

        // List admin tabungan saat ini
        $admins = $db->query("SELECT a.id, a.user_id, a.can_delete, COALESCE(g.nama, u.username) as nama, u.username 
            FROM app_admins a 
            JOIN users u ON a.user_id = u.id 
            LEFT JOIN guru g ON u.id = g.user_id 
            WHERE a.app_code = 'tabungan'
            ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);

        $active_page = 'akses';
        include __DIR__ . '/../../resources/views/admin/tabungan/akses.php';
    }
}
