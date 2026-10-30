<?php

namespace App\Controllers;

use App\Core\Database;
use PDO;

class PortalGuruBendaharaController {

    private static function ensureTables($db) {
        try {
            $db->exec("CREATE TABLE IF NOT EXISTS keuangan_komite_kategori (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nama_kategori VARCHAR(150) NOT NULL,
                guru_id INT DEFAULT NULL,
                tahun_ajaran VARCHAR(50) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (guru_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $db->exec("CREATE TABLE IF NOT EXISTS keuangan_komite_jenis (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nama_tagihan VARCHAR(150) NOT NULL,
                kategori VARCHAR(150) NOT NULL,
                nominal DECIMAL(15,2) DEFAULT 0,
                kelas_id INT DEFAULT NULL,
                tahun_ajaran VARCHAR(50) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (kategori)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $db->exec("CREATE TABLE IF NOT EXISTS keuangan_komite_pembayaran (
                id INT AUTO_INCREMENT PRIMARY KEY,
                siswa_id INT NOT NULL,
                jenis_pembayaran VARCHAR(150) NOT NULL,
                jumlah DECIMAL(15,2) DEFAULT 0,
                tanggal_bayar DATE DEFAULT NULL,
                metode VARCHAR(50) DEFAULT 'Tunai',
                penerima VARCHAR(100) DEFAULT NULL,
                bukti VARCHAR(255) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (siswa_id),
                INDEX (jenis_pembayaran)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $db->exec("CREATE TABLE IF NOT EXISTS keuangan_komite_transaksi (
                id INT AUTO_INCREMENT PRIMARY KEY,
                jenis VARCHAR(50) NOT NULL,
                kategori VARCHAR(150) NOT NULL,
                jumlah DECIMAL(15,2) DEFAULT 0,
                keterangan TEXT NOT NULL,
                tanggal_transaksi DATE DEFAULT NULL,
                petugas_id INT DEFAULT NULL,
                user_id INT DEFAULT NULL,
                bukti VARCHAR(255) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (kategori),
                INDEX (jenis)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $db->exec("CREATE TABLE IF NOT EXISTS keuangan_tagihan (
                id INT AUTO_INCREMENT PRIMARY KEY,
                siswa_id INT NOT NULL,
                nama_tagihan VARCHAR(150) NOT NULL,
                jumlah_tagihan DECIMAL(15,2) DEFAULT 0,
                status VARCHAR(50) DEFAULT 'Belum Bayar',
                tahun_ajaran VARCHAR(50) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (siswa_id),
                INDEX (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $db->exec("CREATE TABLE IF NOT EXISTS keuangan_transaksi (
                id INT AUTO_INCREMENT PRIMARY KEY,
                tanggal_bayar DATE DEFAULT NULL,
                keterangan TEXT NOT NULL,
                jenis VARCHAR(50) NOT NULL,
                jumlah DECIMAL(15,2) DEFAULT 0,
                guru_id INT DEFAULT NULL,
                bukti VARCHAR(255) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        } catch (\Exception $e) {}
    }

    private static function checkAccess() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        $db = Database::connect('core');
        self::ensureTables($db);

        $roleId = isset($_SESSION['role_id']) ? (int)$_SESSION['role_id'] : 0;
        $isKamad = !empty($_SESSION['is_kamad']);
        $isAdmin = ($roleId === 1 || $roleId === 99 || $isKamad);

        $guru_id = 0;
        $categories = [];

        if (!$isAdmin) {
            $stmt = $db->prepare("SELECT id FROM guru WHERE user_id = ? LIMIT 1");
            $stmt->execute([$_SESSION['user_id']]);
            $guru = $stmt->fetch();
            if ($guru) {
                $guru_id = (int)$guru['id'];
                $categories = $db->query("SELECT nama_kategori FROM keuangan_komite_kategori WHERE guru_id = {$guru_id}")->fetchAll(PDO::FETCH_COLUMN);

                if (empty($categories)) {
                    // Cek di guru_tugas jika terdaftar sebagai Bendahara
                    $hasTask = $db->query("SELECT COUNT(*) FROM guru_tugas WHERE guru_id = {$guru_id} AND tugas LIKE '%Bendahara%'")->fetchColumn();
                    if ($hasTask > 0) {
                        $categories = $db->query("SELECT DISTINCT nama_kategori FROM keuangan_komite_kategori")->fetchAll(PDO::FETCH_COLUMN);
                    }
                }
            }
        } else {
            // Admin / Kamad dapat memonitor seluruh kategori
            $categories = $db->query("SELECT DISTINCT nama_kategori FROM keuangan_komite_kategori")->fetchAll(PDO::FETCH_COLUMN);
        }

        // Jika master kategori belum ada, sediakan kategori umum pos keuangan
        if (empty($categories)) {
            $categories = ['Komite', 'SPP', 'Kegiatan Madrasah', 'Infaq / Shodaqoh'];
        }

        return [
            'guru_id' => $guru_id,
            'categories' => array_values(array_unique($categories)),
            'isAdmin' => $isAdmin
        ];
    }

    public static function bku() {
        $access = self::checkAccess();
        $categories = $access['categories'];
        
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
        $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
        $selected_kat = isset($_GET['kategori']) ? $_GET['kategori'] : '';

        if ($selected_kat !== '' && in_array($selected_kat, $categories)) {
            $quoted_cats = "'$selected_kat'";
            $filtered_cats = [$selected_kat];
        } else {
            $quoted_cats = implode(',', array_map(function($c) { return "'$c'"; }, $categories));
            $filtered_cats = $categories;
            $selected_kat = '';
        }

        $filterDatePemb = "";
        $filterDateTrans = "";
        if ($start_date && $end_date) {
            $filterDatePemb = " AND DATE(tanggal_bayar) >= '$start_date' AND DATE(tanggal_bayar) <= '$end_date'";
            $filterDateTrans = " AND DATE(tanggal_transaksi) >= '$start_date' AND DATE(tanggal_transaksi) <= '$end_date'";
        }

        $db = Database::connect('core');

        $wherePembayaran = " WHERE jenis_pembayaran IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori IN ($quoted_cats)) $filterDatePemb";
        $whereManualMasuk = " WHERE jenis='Pemasukan' AND (kategori IN ($quoted_cats) OR kategori IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori IN ($quoted_cats))) $filterDateTrans";
        $whereManualKeluar = " WHERE jenis='Pengeluaran' AND (kategori IN ($quoted_cats) OR kategori IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori IN ($quoted_cats))) $filterDateTrans";

        $r_siswa = $db->query("SELECT SUM(jumlah) as total FROM keuangan_komite_pembayaran" . $wherePembayaran)->fetch();
        $r_manual_in = $db->query("SELECT SUM(jumlah) as total FROM keuangan_komite_transaksi" . $whereManualMasuk)->fetch();
        $r_manual_out = $db->query("SELECT SUM(jumlah) as total FROM keuangan_komite_transaksi" . $whereManualKeluar)->fetch();

        $totalPemasukan = floatval($r_siswa['total'] ?? 0) + floatval($r_manual_in['total'] ?? 0);
        $totalPengeluaran = floatval($r_manual_out['total'] ?? 0);
        $saldo = $totalPemasukan - $totalPengeluaran;

        // Hitung status tagihan siswa
        $whereTagihan = " WHERE nama_tagihan IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori IN ($quoted_cats))";
        
        $tagihanLunas = $db->query("SELECT COUNT(*) as cnt, SUM(jumlah_tagihan) as total FROM keuangan_tagihan $whereTagihan AND status = 'Lunas'")->fetch();
        $tagihanMenyicil = $db->query("SELECT COUNT(*) as cnt, SUM(jumlah_tagihan) as total FROM keuangan_tagihan $whereTagihan AND status = 'Menyicil'")->fetch();
        $tagihanBelumBayar = $db->query("SELECT COUNT(*) as cnt, SUM(jumlah_tagihan) as total FROM keuangan_tagihan $whereTagihan AND status = 'Belum Bayar'")->fetch();

        // Tren Bulanan (6 Bulan Terakhir)
        $trenData = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = date('m', strtotime("-$i months"));
            $y = date('Y', strtotime("-$i months"));
            $label = date('F Y', strtotime("-$i months"));
            
            $whereBulanPemb = " WHERE jenis_pembayaran IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori IN ($quoted_cats)) AND MONTH(tanggal_bayar) = '$m' AND YEAR(tanggal_bayar) = '$y'";
            $whereBulanTrans = " WHERE jenis='Pemasukan' AND (kategori IN ($quoted_cats) OR kategori IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori IN ($quoted_cats))) AND MONTH(tanggal_transaksi) = '$m' AND YEAR(tanggal_transaksi) = '$y'";
            
            $r1 = $db->query("SELECT SUM(jumlah) as t FROM keuangan_komite_pembayaran $whereBulanPemb")->fetch();
            $r2 = $db->query("SELECT SUM(jumlah) as t FROM keuangan_komite_transaksi $whereBulanTrans")->fetch();
            
            $trenData[] = [
                'bulan' => $label,
                'pemasukan' => floatval($r1['t'] ?? 0) + floatval($r2['t'] ?? 0)
            ];
        }

        // Breakdown per Kategori
        $kategoriSummaryRawSiswa = $db->query("
            SELECT COALESCE(kj.kategori, 'Lainnya') as kategori, SUM(k.jumlah) as total
            FROM keuangan_komite_pembayaran k
            LEFT JOIN (SELECT DISTINCT nama_tagihan, kategori FROM keuangan_komite_jenis) kj ON k.jenis_pembayaran = kj.nama_tagihan
            $wherePembayaran
            GROUP BY COALESCE(kj.kategori, 'Lainnya')
        ")->fetchAll();

        $kategoriSummaryRawManualIn = $db->query("
            SELECT COALESCE(kj.kategori, t.kategori, 'Lainnya') as kategori, SUM(t.jumlah) as total
            FROM (SELECT * FROM keuangan_komite_transaksi $whereManualMasuk) t
            LEFT JOIN (SELECT DISTINCT nama_tagihan, kategori FROM keuangan_komite_jenis) kj ON t.kategori = kj.nama_tagihan
            GROUP BY COALESCE(kj.kategori, t.kategori, 'Lainnya')
        ")->fetchAll();
        
        $kategoriSummaryRawManualOut = $db->query("
            SELECT COALESCE(kj.kategori, t.kategori, 'Lainnya') as kategori, SUM(t.jumlah) as total
            FROM (SELECT * FROM keuangan_komite_transaksi $whereManualKeluar) t
            LEFT JOIN (SELECT DISTINCT nama_tagihan, kategori FROM keuangan_komite_jenis) kj ON t.kategori = kj.nama_tagihan
            GROUP BY COALESCE(kj.kategori, t.kategori, 'Lainnya')
        ")->fetchAll();

        $kategoriSummary = [];
        foreach($filtered_cats as $cat) {
            $kategoriSummary[$cat] = ['pemasukan' => 0, 'pengeluaran' => 0, 'saldo' => 0];
        }
        
        foreach($kategoriSummaryRawSiswa as $row) {
            $cat = $row['kategori'] ?: 'Lainnya';
            if(!isset($kategoriSummary[$cat])) $kategoriSummary[$cat] = ['pemasukan' => 0, 'pengeluaran' => 0, 'saldo' => 0];
            $kategoriSummary[$cat]['pemasukan'] += floatval($row['total']);
        }
        foreach($kategoriSummaryRawManualIn as $row) {
            $cat = $row['kategori'] ?: 'Lainnya';
            if(!isset($kategoriSummary[$cat])) $kategoriSummary[$cat] = ['pemasukan' => 0, 'pengeluaran' => 0, 'saldo' => 0];
            $kategoriSummary[$cat]['pemasukan'] += floatval($row['total']);
        }
        foreach($kategoriSummaryRawManualOut as $row) {
            $cat = $row['kategori'] ?: 'Lainnya';
            if(!isset($kategoriSummary[$cat])) $kategoriSummary[$cat] = ['pemasukan' => 0, 'pengeluaran' => 0, 'saldo' => 0];
            $kategoriSummary[$cat]['pengeluaran'] += floatval($row['total']);
        }
        
        foreach($kategoriSummary as $cat => &$data) {
            $data['saldo'] = $data['pemasukan'] - $data['pengeluaran'];
        }
        unset($data);

        $viewData = [
            'categories' => $categories,
            'filtered_cats' => $filtered_cats,
            'selected_kat' => $selected_kat,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'saldo' => $saldo,
            'tagihanLunas' => $tagihanLunas,
            'tagihanMenyicil' => $tagihanMenyicil,
            'tagihanBelumBayar' => $tagihanBelumBayar,
            'trenData' => $trenData,
            'kategoriSummary' => $kategoriSummary,
            'active_page' => 'bendahara_bku'
        ];

        self::render('bendahara_bku', $viewData);
    }

    public static function transaksi() {
        $access = self::checkAccess();
        $categories = $access['categories'];
        
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
        $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
        $selected_kat = isset($_GET['kategori']) ? $_GET['kategori'] : '';
        $tipe = isset($_GET['tipe']) ? $_GET['tipe'] : '';

        if ($selected_kat !== '' && in_array($selected_kat, $categories)) {
            $quoted_cats = "'$selected_kat'";
            $filtered_cats = [$selected_kat];
        } else {
            $quoted_cats = implode(',', array_map(function($c) { return "'$c'"; }, $categories));
            $filtered_cats = $categories;
            $selected_kat = '';
        }

        $filterDatePemb = "";
        $filterDateTrans = "";
        if ($start_date && $end_date) {
            $filterDatePemb = " AND DATE(tanggal_bayar) >= '$start_date' AND DATE(tanggal_bayar) <= '$end_date'";
            $filterDateTrans = " AND DATE(tanggal_transaksi) >= '$start_date' AND DATE(tanggal_transaksi) <= '$end_date'";
        }

        $db = Database::connect('core');

        $whereTrans = "(kategori IN ($quoted_cats) OR kategori IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori IN ($quoted_cats))) $filterDateTrans";
        $wherePemb = "jenis_pembayaran IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori IN ($quoted_cats)) $filterDatePemb";

        if ($tipe == 'Pemasukan') {
            $whereTrans .= " AND jenis='Pemasukan'";
        } else if ($tipe == 'Pengeluaran') {
            $whereTrans .= " AND jenis='Pengeluaran'";
            $wherePemb .= " AND 1=0";
        }

        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $filterSearchTrans = "";
        $filterSearchPemb = "";
        
        if (!empty($search)) {
            $search_q = $db->quote('%' . $search . '%');
            $filterSearchTrans = " AND (keterangan LIKE $search_q OR kategori LIKE $search_q)";
            $filterSearchPemb = " AND (s.nama LIKE $search_q OR k.jenis_pembayaran LIKE $search_q)";
        }

        $transaksi = $db->query("
            SELECT t.id, t.tanggal_transaksi as tanggal, t.keterangan, NULL as nama_siswa, t.kategori as sumber, t.jumlah, t.jenis as tipe, 'manual' as sumber_transaksi, t.bukti
            FROM keuangan_komite_transaksi t
            WHERE $whereTrans $filterSearchTrans
            UNION ALL
            SELECT k.id, k.tanggal_bayar as tanggal, CONCAT('Pembayaran ', k.jenis_pembayaran) as keterangan, s.nama as nama_siswa, k.jenis_pembayaran as sumber, k.jumlah, 'Pemasukan' as tipe, 'siswa' as sumber_transaksi, NULL as bukti
            FROM keuangan_komite_pembayaran k
            JOIN siswa s ON k.siswa_id = s.id
            WHERE $wherePemb $filterSearchPemb
            ORDER BY tanggal DESC
        ")->fetchAll();

        $viewData = [
            'categories' => $categories,
            'selected_kat' => $selected_kat,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'tipe' => $tipe,
            'search' => $search,
            'transaksi' => $transaksi,
            'active_page' => 'bendahara_transaksi'
        ];

        self::render('bendahara_transaksi', $viewData);
    }

    public static function saveTransaksi() {
        $access = self::checkAccess();
        $categories = $access['categories'];
        $guru_id = $access['guru_id'];
        $db = Database::connect('core');

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $jenis = isset($_POST['jenis']) && in_array($_POST['jenis'], ['Pemasukan', 'Pengeluaran']) ? $_POST['jenis'] : 'Pemasukan';
        $tanggal = !empty($_POST['tanggal_transaksi']) ? $_POST['tanggal_transaksi'] : date('Y-m-d');
        $keterangan = isset($_POST['keterangan']) ? trim($_POST['keterangan']) : '';
        $kategori = isset($_POST['kategori']) ? trim($_POST['kategori']) : '';
        
        $raw_jumlah = isset($_POST['jumlah']) ? preg_replace('/[^0-9]/', '', $_POST['jumlah']) : 0;
        $jumlah = floatval($raw_jumlah);

        if (!in_array($kategori, $categories)) {
            header("Location: /guru/bendahara/transaksi?msg=unauthorized");
            exit;
        }

        if ($jumlah <= 0 || empty($keterangan)) {
            header("Location: /guru/bendahara/transaksi?msg=invalid_input");
            exit;
        }

        $petugas_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

        // Handle upload bukti struk
        $bukti_filename = '';
        if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../public/uploads/finance/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $ext = strtolower(pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'pdf', 'webp'])) {
                $bukti_filename = 'bukti_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                move_uploaded_file($_FILES['bukti']['tmp_name'], $upload_dir . $bukti_filename);
            }
        }

        if ($id > 0) {
            // Edit Mode
            $old = $db->query("SELECT keterangan, tanggal_transaksi, jenis, jumlah, bukti, kategori FROM keuangan_komite_transaksi WHERE id = $id")->fetch();
            if (!$old || !in_array($old['kategori'], $categories)) {
                header("Location: /guru/bendahara/transaksi?msg=unauthorized");
                exit;
            }

            if (!empty($bukti_filename)) {
                if (!empty($old['bukti'])) {
                    @unlink(__DIR__ . '/../../public/uploads/finance/' . $old['bukti']);
                }
                $stmt = $db->prepare("UPDATE keuangan_komite_transaksi SET tanggal_transaksi = ?, keterangan = ?, jenis = ?, kategori = ?, jumlah = ?, petugas_id = ?, bukti = ? WHERE id = ?");
                $stmt->execute([$tanggal, $keterangan, $jenis, $kategori, $jumlah, $petugas_id, $bukti_filename, $id]);
            } else {
                $stmt = $db->prepare("UPDATE keuangan_komite_transaksi SET tanggal_transaksi = ?, keterangan = ?, jenis = ?, kategori = ?, jumlah = ?, petugas_id = ? WHERE id = ?");
                $stmt->execute([$tanggal, $keterangan, $jenis, $kategori, $jumlah, $petugas_id, $id]);
            }

            // Sync update to keuangan_transaksi
            $keterangan_sync = "[$kategori] $keterangan";
            $stmt_sync = $db->prepare("UPDATE keuangan_transaksi SET jumlah = ?, tanggal_bayar = ?, keterangan = ? WHERE jenis = ? AND jumlah = ? AND tanggal_bayar = ? AND keterangan LIKE ? LIMIT 1");
            $stmt_sync->execute([$jumlah, $tanggal, $keterangan_sync, $old['jenis'], $old['jumlah'], $old['tanggal_transaksi'], '%' . $old['keterangan'] . '%']);

            header("Location: /guru/bendahara/transaksi?msg=updated");
            exit;
        } else {
            // New Insert Mode
            $stmt = $db->prepare("INSERT INTO keuangan_komite_transaksi (tanggal_transaksi, keterangan, jenis, kategori, jumlah, petugas_id, bukti) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$tanggal, $keterangan, $jenis, $kategori, $jumlah, $petugas_id, $bukti_filename]);

            // Sync insert to keuangan_transaksi
            $keterangan_sync = "[$kategori] $keterangan";
            $stmt_sync_insert = $db->prepare("INSERT INTO keuangan_transaksi (tanggal_bayar, keterangan, jenis, jumlah, guru_id, bukti) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_sync_insert->execute([$tanggal, $keterangan_sync, $jenis, $jumlah, $guru_id, $bukti_filename]);

            header("Location: /guru/bendahara/transaksi?msg=added");
            exit;
        }
    }

    public static function deleteTransaksi($id) {
        $access = self::checkAccess();
        $categories = $access['categories'];
        
        $db = Database::connect('core');
        $id = intval($id);
        
        $t = $db->query("SELECT jenis, kategori, keterangan, tanggal_transaksi, jumlah, bukti FROM keuangan_komite_transaksi WHERE id = $id")->fetch();
        if ($t) {
            if (!in_array($t['kategori'], $categories)) {
                header("Location: /guru/bendahara/transaksi?msg=unauthorized");
                exit;
            }

            if (!empty($t['bukti'])) {
                $filePath = __DIR__ . '/../../public/uploads/finance/' . $t['bukti'];
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            // Sync delete from keuangan_transaksi
            $stmt_sync = $db->prepare("DELETE FROM keuangan_transaksi WHERE jenis = ? AND jumlah = ? AND tanggal_bayar = ? AND keterangan LIKE ? LIMIT 1");
            $stmt_sync->execute([$t['jenis'], $t['jumlah'], $t['tanggal_transaksi'], '%' . $t['keterangan'] . '%']);

            $db->prepare("DELETE FROM keuangan_komite_transaksi WHERE id = ?")->execute([$id]);

            header("Location: /guru/bendahara/transaksi?msg=deleted");
            exit;
        }

        header("Location: /guru/bendahara/transaksi");
        exit;
    }

    public static function deletePembayaranSiswa($id) {
        $access = self::checkAccess();
        $categories = $access['categories'];
        
        $db = Database::connect('core');
        $id = intval($id);
        
        $p = $db->query("SELECT p.*, s.nama as nama_siswa FROM keuangan_komite_pembayaran p JOIN siswa s ON p.siswa_id = s.id WHERE p.id = $id")->fetch();
        if ($p) {
            $quoted_cats = implode(',', array_map(function($c) { return "'$c'"; }, $categories));
            $is_allowed = $db->query("SELECT 1 FROM keuangan_komite_jenis WHERE nama_tagihan = ".$db->quote($p['jenis_pembayaran'])." AND kategori IN ($quoted_cats)")->fetch();
            if (!$is_allowed && !in_array($p['jenis_pembayaran'], $categories)) {
                header("Location: /guru/bendahara/transaksi?msg=unauthorized");
                exit;
            }

            $siswa_id = $p['siswa_id'];
            $jenis = $p['jenis_pembayaran'];

            $db->prepare("DELETE FROM keuangan_komite_pembayaran WHERE id = ?")->execute([$id]);

            // Sync delete from kas umum keuangan_transaksi
            $stmt_s = $db->prepare("SELECT s.nama, k.nama_kelas FROM siswa s LEFT JOIN kelas k ON k.id = s.kelas_id WHERE s.id = ?");
            $stmt_s->execute([$siswa_id]);
            $siswa_data = $stmt_s->fetch();
            $nama_siswa = $siswa_data ? $siswa_data['nama'] : 'Siswa';
            $nama_kelas = $siswa_data && $siswa_data['nama_kelas'] ? $siswa_data['nama_kelas'] : '-';
            $keterangan_trx = "[" . $jenis . "] " . $nama_siswa . " (" . $nama_kelas . ")";

            $db->prepare("DELETE FROM keuangan_transaksi WHERE keterangan = ? AND jumlah = ? AND tanggal_bayar = ? AND jenis = 'Pemasukan' LIMIT 1")
               ->execute([$keterangan_trx, $p['jumlah'], $p['tanggal_bayar']]);

            // Recalculate status in keuangan_tagihan
            $tagihan = $db->query("SELECT * FROM keuangan_tagihan WHERE siswa_id = $siswa_id AND nama_tagihan = ".$db->quote($jenis))->fetch();
            if ($tagihan) {
                $terbayar = $db->query("SELECT COALESCE(SUM(jumlah), 0) FROM keuangan_komite_pembayaran WHERE siswa_id = $siswa_id AND jenis_pembayaran = ".$db->quote($jenis))->fetchColumn();
                if ($terbayar <= 0) {
                    $db->prepare("UPDATE keuangan_tagihan SET status = 'Belum Bayar' WHERE id = ?")->execute([$tagihan['id']]);
                } elseif ($terbayar >= $tagihan['jumlah_tagihan']) {
                    $db->prepare("UPDATE keuangan_tagihan SET status = 'Lunas' WHERE id = ?")->execute([$tagihan['id']]);
                } else {
                    $db->prepare("UPDATE keuangan_tagihan SET status = 'Menyicil' WHERE id = ?")->execute([$tagihan['id']]);
                }
            }

            header("Location: /guru/bendahara/transaksi?msg=deleted_siswa");
            exit;
        }

        header("Location: /guru/bendahara/transaksi");
        exit;
    }

    public static function updatePembayaranSiswa() {
        $access = self::checkAccess();
        $categories = $access['categories'];
        
        $db = Database::connect('core');
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $tanggal = !empty($_POST['tanggal_bayar']) ? $_POST['tanggal_bayar'] : date('Y-m-d');
        $raw_jumlah = isset($_POST['jumlah']) ? preg_replace('/[^0-9]/', '', $_POST['jumlah']) : 0;
        $jumlah = floatval($raw_jumlah);

        $p = $db->query("SELECT * FROM keuangan_komite_pembayaran WHERE id = $id")->fetch();
        if (!$p) {
            header("Location: /guru/bendahara/transaksi?msg=not_found");
            exit;
        }

        $quoted_cats = implode(',', array_map(function($c) { return "'$c'"; }, $categories));
        $is_allowed = $db->query("SELECT 1 FROM keuangan_komite_jenis WHERE nama_tagihan = ".$db->quote($p['jenis_pembayaran'])." AND kategori IN ($quoted_cats)")->fetch();
        if (!$is_allowed && !in_array($p['jenis_pembayaran'], $categories)) {
            header("Location: /guru/bendahara/transaksi?msg=unauthorized");
            exit;
        }

        if ($jumlah <= 0) {
            header("Location: /guru/bendahara/transaksi?msg=invalid_input");
            exit;
        }

        $db->prepare("UPDATE keuangan_komite_pembayaran SET tanggal_bayar = ?, jumlah = ? WHERE id = ?")
           ->execute([$tanggal, $jumlah, $id]);

        // Sync update to keuangan_transaksi
        $siswa_id = $p['siswa_id'];
        $jenis = $p['jenis_pembayaran'];
        $stmt_s = $db->prepare("SELECT s.nama, k.nama_kelas FROM siswa s LEFT JOIN kelas k ON k.id = s.kelas_id WHERE s.id = ?");
        $stmt_s->execute([$siswa_id]);
        $siswa_data = $stmt_s->fetch();
        $nama_siswa = $siswa_data ? $siswa_data['nama'] : 'Siswa';
        $nama_kelas = $siswa_data && $siswa_data['nama_kelas'] ? $siswa_data['nama_kelas'] : '-';
        $keterangan_trx = "[" . $jenis . "] " . $nama_siswa . " (" . $nama_kelas . ")";

        $db->prepare("UPDATE keuangan_transaksi SET jumlah = ?, tanggal_bayar = ? WHERE keterangan = ? AND jumlah = ? AND tanggal_bayar = ? AND jenis = 'Pemasukan' LIMIT 1")
           ->execute([$jumlah, $tanggal, $keterangan_trx, $p['jumlah'], $p['tanggal_bayar']]);

        // Recalculate status in keuangan_tagihan
        $tagihan = $db->query("SELECT * FROM keuangan_tagihan WHERE siswa_id = $siswa_id AND nama_tagihan = ".$db->quote($jenis))->fetch();
        if ($tagihan) {
            $terbayar = $db->query("SELECT COALESCE(SUM(jumlah), 0) FROM keuangan_komite_pembayaran WHERE siswa_id = $siswa_id AND jenis_pembayaran = ".$db->quote($jenis))->fetchColumn();
            if ($terbayar <= 0) {
                $db->prepare("UPDATE keuangan_tagihan SET status = 'Belum Bayar' WHERE id = ?")->execute([$tagihan['id']]);
            } elseif ($terbayar >= $tagihan['jumlah_tagihan']) {
                $db->prepare("UPDATE keuangan_tagihan SET status = 'Lunas' WHERE id = ?")->execute([$tagihan['id']]);
            } else {
                $db->prepare("UPDATE keuangan_tagihan SET status = 'Menyicil' WHERE id = ?")->execute([$tagihan['id']]);
            }
        }

        header("Location: /guru/bendahara/transaksi?msg=updated_siswa");
        exit;
    }

    private static function render($view, $data = []) {
        extract($data);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $guru_profile = \App\Core\Database::connect('core')->query("SELECT * FROM guru WHERE user_id = " . intval($_SESSION['user_id'] ?? 0))->fetch();
        $nama = $guru_profile['nama'] ?? ($_SESSION['nama'] ?? ($_SESSION['username'] ?? 'User'));
        $initial = strtoupper(substr($nama, 0, 2));

        include __DIR__ . '/../../resources/views/portal/guru/' . $view . '.php';
    }
}

