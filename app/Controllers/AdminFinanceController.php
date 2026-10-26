<?php

namespace App\Controllers;

use App\Core\Database;

class AdminFinanceController {

    // Guard: role 1/99 atau role 2 dengan delegasi 'keuangan'
    // Memiliki pengecekan tambahan untuk menu spesifik jika bukan superadmin
    private static function guard($required_menu = '') {
        if (!isset($_SESSION['role_id'])) {
            redirect('/');
            exit;
        }
        $role_id = $_SESSION['role_id'];
        if (in_array($role_id, array(1, 99))) return; // Superadmin bebas
        
        if ($role_id == 2 && isset($_SESSION['user_id'])) {
            $db = Database::connect('core');
            $uid = $_SESSION['user_id'];
            
            // 1. Cek apakah Guru ini adalah App Admin (Delegasi penuh)
            $is_app_admin = $db->query("SELECT id FROM app_admins WHERE user_id = $uid AND app_code = 'keuangan'")->fetch();
            if ($is_app_admin) {
                return; // Berikan akses penuh
            }
            
            // 2. Jika bukan App Admin penuh, cek apakah dia Bendahara yang ditunjuk di dalam Keuangan
            $guru = $db->query("SELECT id FROM guru WHERE user_id = $uid")->fetch();
            if ($guru) {
                $akses = $db->query("SELECT akses_menu FROM keuangan_akses WHERE guru_id = {$guru['id']}")->fetch();
                if ($akses) {
                    // Kalau di halaman overview, izinkan masuk
                    if ($required_menu === '') return;
                    
                    if (!empty($akses['akses_menu'])) {
                        $allowed_menus = json_decode($akses['akses_menu'], true);
                        if (is_array($allowed_menus) && in_array($required_menu, $allowed_menus)) {
                            return; // Diizinkan sesuai hak akses menu
                        }
                    }
                    redirect('/admin/finance?msg=no_access');
                    exit;
                }
            }
        }
        
        // Tolak akses jika tidak memenuhi syarat
        redirect('/');
        exit;
    }

    // Shared render helper
    private static function render($view, $data = array(), $required_menu = '') {
        self::guard($required_menu);
        $data['adminNama']    = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Administrator';
        $data['adminInitial'] = strtoupper(substr($data['adminNama'], 0, 2));
        $data['active_module'] = 'finance';
        
        // Pass allowed menus to the view so sidebar can hide menus
        $allowed_menus = ['all']; // Default for superadmin
        if (isset($_SESSION['role_id']) && !in_array($_SESSION['role_id'], [1, 99]) && isset($_SESSION['user_id'])) {
            $db = Database::connect('core');
            $uid = $_SESSION['user_id'];
            $guru = $db->query("SELECT id FROM guru WHERE user_id = $uid")->fetch();
            if ($guru) {
                $akses = $db->query("SELECT akses_menu FROM keuangan_akses WHERE guru_id = {$guru['id']}")->fetch();
                if ($akses && !empty($akses['akses_menu'])) {
                    $allowed_menus = json_decode($akses['akses_menu'], true) ?: [];
                } else {
                    $allowed_menus = [];
                }
            } else {
                $allowed_menus = [];
            }
        }
        $data['allowed_menus'] = $allowed_menus;
        
        extract($data);
        include __DIR__ . '/../../resources/views/admin/' . $view . '.php';
    }

    // Real: Transaksi terakhir
    private static function getTransactions() {
        $db = Database::connect('core');
        return $db->query("
            SELECT t.tanggal_bayar as tanggal, t.keterangan, t.jenis, t.jumlah 
            FROM keuangan_transaksi t 
            ORDER BY t.tanggal_bayar DESC LIMIT 20
        ")->fetchAll();
    }

    public static function syncTagihanMaster($rule_id = null) {
        $db = Database::connect('core');
        $new_tagihan_notifs = [];
        self::autoRolloverJatuhTempo();
        
        // 1. Ambil Jenis Tagihan yang "Wajib" (Diurutkan agar target per-Siswa / per-Individu menimpa target per-Kelas / Semua)
        $rules = $db->query("
            SELECT * FROM keuangan_komite_jenis 
            WHERE sifat = 'Wajib' 
            ORDER BY CASE target_tipe 
                WHEN 'Semua' THEN 1 
                WHEN 'Kelas' THEN 2 
                WHEN 'Siswa' THEN 3 
                WHEN 'Individu' THEN 4 
                ELSE 5 
            END ASC, id ASC
        ")->fetchAll();

        $valid = []; 
        $rule_nominal = []; 
        foreach ($rules as $r) {
            $name = trim($r["nama_tagihan"]);
            $thn_id = $r["tahun_ajaran_id"];
            if (!isset($valid[$name])) $valid[$name] = [];
            if (!isset($valid[$name][$thn_id])) $valid[$name][$thn_id] = [];
            
            if ($r["target_tipe"] == "Semua") {
                $siswas = $db->query("SELECT DISTINCT siswa_id as id FROM riwayat_kelas_siswa WHERE tahun_ajaran_id = " . intval($thn_id))->fetchAll();
                foreach ($siswas as $s) {
                    $valid[$name][$thn_id][] = $s["id"];
                    $rule_nominal[$name][$thn_id][$s["id"]] = $r["nominal"];
                }
            } elseif ($r["target_tipe"] == "Kelas") {
                if (!empty($r["target_kelas_id"])) {
                    $cids = explode(",", $r["target_kelas_id"]);
                    $cids_clean = [];
                    foreach ($cids as $cid) {
                        $cid = intval($cid);
                        if ($cid > 0) $cids_clean[] = $cid;
                    }
                    if (!empty($cids_clean)) {
                        $in_clause = implode(",", $cids_clean);
                        $siswas = $db->query("SELECT DISTINCT siswa_id as id FROM riwayat_kelas_siswa WHERE tahun_ajaran_id = " . intval($thn_id) . " AND kelas_id IN (" . $in_clause . ")")->fetchAll();
                        foreach ($siswas as $s) {
                            $valid[$name][$thn_id][] = $s["id"];
                            $rule_nominal[$name][$thn_id][$s["id"]] = $r["nominal"];
                        }
                    }
                }
            } elseif ($r["target_tipe"] == "Siswa" || $r["target_tipe"] == "Individu") {
                if (!empty($r["target_siswa_id"])) {
                    $ids = explode(",", $r["target_siswa_id"]);
                    foreach ($ids as $id) {
                        $valid[$name][$thn_id][] = $id;
                        $rule_nominal[$name][$thn_id][$id] = $r["nominal"];
                    }
                }
            }
        }

        // Hapus orphans (tagihan Wajib yang target siswanya sudah dihapus dari semua rule dengan nama tersebut)
        $existing_tagihan = $db->query("SELECT id, siswa_id, nama_tagihan, tahun_ajaran_id, status FROM keuangan_tagihan")->fetchAll();
        foreach ($existing_tagihan as $t) {
            $name = trim($t['nama_tagihan']);
            $thn_id = $t['tahun_ajaran_id'];
            $sid = $t['siswa_id'];
            
            $should_delete = false;
            if (!isset($valid[$name]) || !isset($valid[$name][$thn_id])) {
                // Rule no longer exists (deleted or renamed)
                $should_delete = true;
            } elseif (!in_array($sid, $valid[$name][$thn_id])) {
                // Rule exists but student is no longer targeted
                $should_delete = true;
            }

            // Only delete if it hasn't been paid yet to avoid destroying payment history
            if ($should_delete && $t['status'] == 'Belum Bayar') {
                $db->prepare("DELETE FROM keuangan_tagihan WHERE id = ?")->execute([$t['id']]);
            }
        }

        // Insert atau Update tagihan
        $activeYear = \App\Core\AcademicYear::current();
        $smt = $activeYear['semester'] ?? 1;

        foreach ($valid as $name => $thn_array) {
            foreach ($thn_array as $thn_id => $siswa_ids) {
                $siswa_ids = array_unique($siswa_ids);
                foreach ($siswa_ids as $sid) {
                    $nominal = $rule_nominal[$name][$thn_id][$sid];
                    
                    $existing = $db->prepare("SELECT id, status FROM keuangan_tagihan WHERE siswa_id = ? AND TRIM(nama_tagihan) = ? AND tahun_ajaran_id = ?");
                    $existing->execute([$sid, $name, $thn_id]);
                    $ext = $existing->fetch();
                    
                    if (!$ext) {
                        $today = date('Y-m-d');
                        $y = (int)date('Y');
                        $jt_dates = [
                            "$y-03-31",
                            "$y-06-15",
                            "$y-09-30",
                            "$y-12-15",
                            ($y+1)."-03-31"
                        ];
                        $jt = $jt_dates[0];
                        foreach ($jt_dates as $d) {
                            if ($today <= $d) {
                                $jt = $d;
                                break;
                            }
                        }

                        $stmtIns = $db->prepare("INSERT INTO keuangan_tagihan (siswa_id, nama_tagihan, jumlah_tagihan, status, jatuh_tempo, tahun_ajaran_id, semester) VALUES (?, ?, ?, 'Belum Bayar', ?, ?, ?)");
                        $stmtIns->execute([$sid, $name, $nominal, $jt, $thn_id, $smt]);
                        
                        // Kumpulkan ID siswa untuk ditembak notifikasi
                        if (!isset($new_tagihan_notifs[$name])) {
                            $new_tagihan_notifs[$name] = [
                                'nominal' => $nominal,
                                'siswa_ids' => []
                            ];
                        }
                        $new_tagihan_notifs[$name]['siswa_ids'][] = $sid;
                    } else {
                        // Update nominal jika berubah di master
                        $db->prepare("UPDATE keuangan_tagihan SET jumlah_tagihan = ? WHERE id = ?")->execute([$nominal, $ext['id']]);
                        
                        // Recalculate status pembayaran
                        $stmtPay = $db->prepare("SELECT SUM(jumlah) as total FROM keuangan_komite_pembayaran WHERE siswa_id = ? AND TRIM(jenis_pembayaran) = ?");
                        $stmtPay->execute([$sid, $name]);
                        $pay = $stmtPay->fetch();
                        $totalBayar = $pay['total'] ? floatval($pay['total']) : 0;
                        
                        $statusBaru = 'Belum Bayar';
                        if ($totalBayar >= $nominal) {
                            $statusBaru = 'Lunas';
                        } elseif ($totalBayar > 0) {
                            $statusBaru = 'Menyicil';
                        }
                        
                        if ($statusBaru !== $ext['status'] || empty($ext['status'])) {
                            $db->prepare("UPDATE keuangan_tagihan SET status = ? WHERE id = ?")->execute([$statusBaru, $ext['id']]);
                        }
                    }
                }
            }
        }
        
        // --- TEMBAKAN NOTIFIKASI MASSAL (BULK PUSH NOTIFICATION) ---
        if (!empty($new_tagihan_notifs)) {
            require_once __DIR__ . '/../Services/OneSignalService.php';
            foreach ($new_tagihan_notifs as $namaTagihan => $data) {
                if (!empty($data['siswa_ids'])) {
                    $nominal_rp = "Rp " . number_format($data['nominal'], 0, ',', '.');
                    $title = "Tagihan Baru: " . $namaTagihan;
                    $msg = "Ada tagihan baru sebesar " . $nominal_rp . " telah ditambahkan ke akun siswa. Silakan cek menu Keuangan.";
                    // OneSignal bulk target bisa mengirim ratusan/ribuan alias sekaligus
                    \App\Services\OneSignalService::sendNotification(
                        $title, 
                        $msg, 
                        "https://mismifhda.my.id/apk/index.php?page=keuangan",
                        $data['siswa_ids']
                    );
                }
            }
        }
    }

    private static function getAllowedCategories() {
        if (!isset($_SESSION['role_id'])) return [];
        $role_id = $_SESSION['role_id'];
        if (in_array($role_id, [1, 99])) {
            return ['ALL'];
        }
        
        $db = Database::connect('core');
        $uid = $_SESSION['user_id'];
        
        // Ambil guru_id
        $guru = $db->query("SELECT id FROM guru WHERE user_id = $uid")->fetch();
        if ($guru) {
            $cats = $db->query("SELECT nama_kategori FROM keuangan_komite_kategori WHERE guru_id = {$guru['id']}")->fetchAll(\PDO::FETCH_COLUMN);
            if (!empty($cats)) {
                return $cats;
            }
        }
        
        // Cek apakah App Admin penuh
        $is_app_admin = $db->query("SELECT id FROM app_admins WHERE user_id = $uid AND app_code = 'keuangan'")->fetch();
        if ($is_app_admin) {
            return ['ALL'];
        }
        
        return [];
    }

    private static function getTagihan() {
        $db = Database::connect('core');
        self::autoRolloverJatuhTempo();
        $activeYear = \App\Core\AcademicYear::current();
        $thn_id = $activeYear['id'];
        $smt = $activeYear['semester'];
        
        $allowed_cats = self::getAllowedCategories();
        $whereClause = "";
        if (!in_array('ALL', $allowed_cats)) {
            if (empty($allowed_cats)) {
                $whereClause = " AND 1=0";
            } else {
                $quoted_cats = implode(',', array_map([$db, 'quote'], $allowed_cats));
                $whereClause = " AND t.nama_tagihan IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori IN ($quoted_cats))";
            }
        }
        
        return $db->query("
            SELECT s.id as siswa_id, s.nama, s.nis, COALESCE(k.nama_kelas, '-') as kelas, 
                   t.id, t.nama_tagihan as tagihan, t.jumlah_tagihan as jml, 
                   t.status, t.jatuh_tempo as tgl_bayar,
                   (SELECT SUM(jumlah) FROM keuangan_komite_pembayaran p WHERE p.siswa_id = t.siswa_id AND p.jenis_pembayaran = t.nama_tagihan) as terbayar
            FROM keuangan_tagihan t
            JOIN siswa s ON t.siswa_id = s.id
            LEFT JOIN kelas k ON s.kelas_id = k.id
            WHERE ((t.tahun_ajaran_id = $thn_id AND t.semester = $smt) OR t.status != 'Lunas') $whereClause
            ORDER BY s.nama ASC, CASE WHEN t.status = 'Menyicil' THEN 1 WHEN t.status = 'Belum Bayar' THEN 2 ELSE 3 END ASC
        ")->fetchAll();
    }

    // Real: Gaji guru (Join dari guru dan tabel keuangan_gaji)
    private static function getGaji() {
        $db = Database::connect('core');
        $bulanSekarang = date('F Y');
        $gurus = $db->query("SELECT id, nama, nip FROM guru WHERE is_active = 1")->fetchAll();
        $gaji = $db->query("SELECT * FROM keuangan_gaji WHERE bulan = '$bulanSekarang'")->fetchAll();
        
        $gaji_map = [];
        foreach($gaji as $gj) {
            $gaji_map[$gj['guru_id']] = $gj;
        }

        $mapped = [];
        foreach($gurus as $g) {
            if (isset($gaji_map[$g['id']])) {
                $gj = $gaji_map[$g['id']];
                $mapped[] = [
                    'nama' => $g['nama'], 'nip' => $g['nip'], 'jabatan' => 'Guru',
                    'gaji_pokok' => $gj['gaji_pokok'], 'insentif' => $gj['tunjangan'], 
                    'potongan' => $gj['potongan'], 'status' => $gj['status'], 'bulan' => $gj['bulan']
                ];
            } else {
                $mapped[] = [
                    'nama' => $g['nama'], 'nip' => $g['nip'], 'jabatan' => 'Guru',
                    'gaji_pokok' => 4000000, 'insentif' => 500000, 'potongan' => 100000, 
                    'status' => 'Proses', 'bulan' => $bulanSekarang
                ];
            }
        }
        return $mapped;
    }

    // Real: Komponen biaya
    private static function getBiaya() {
        $db = Database::connect('core');
        return $db->query("
            SELECT id, nama_biaya as nama, nominal as jml, periode as berlaku, 
                   berlaku_untuk, is_active as aktif
            FROM keuangan_komponen
        ")->fetchAll();
    }

    // Controller: Overview
    public static function overview() {
        $db = Database::connect('core');
        $allowed_cats = self::getAllowedCategories();
        $selected_kat = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';
        
        // 1. Hitung Pemasukan dan Pengeluaran
        $wherePembayaran = "";
        $whereManualMasuk = " WHERE jenis='Pemasukan'";
        $whereManualKeluar = " WHERE jenis='Pengeluaran'";
        
        if (!empty($selected_kat)) {
            if (!in_array('ALL', $allowed_cats) && !in_array($selected_kat, $allowed_cats)) {
                $wherePembayaran = " WHERE 1=0";
                $whereManualMasuk = " WHERE 1=0";
                $whereManualKeluar = " WHERE 1=0";
            } else {
                $kat_q = $db->quote($selected_kat);
                $wherePembayaran = " WHERE jenis_pembayaran IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori = $kat_q)";
                $whereManualMasuk = " WHERE jenis='Pemasukan' AND kategori = $kat_q";
                $whereManualKeluar = " WHERE jenis='Pengeluaran' AND kategori = $kat_q";
            }
        } elseif (!in_array('ALL', $allowed_cats)) {
            if (empty($allowed_cats)) {
                $wherePembayaran = " WHERE 1=0";
                $whereManualMasuk = " WHERE 1=0";
                $whereManualKeluar = " WHERE 1=0";
            } else {
                $quoted_cats = implode(',', array_map([$db, 'quote'], $allowed_cats));
                $wherePembayaran = " WHERE jenis_pembayaran IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori IN ($quoted_cats))";
                $whereManualMasuk = " WHERE jenis='Pemasukan' AND kategori IN ($quoted_cats)";
                $whereManualKeluar = " WHERE jenis='Pengeluaran' AND kategori IN ($quoted_cats)";
            }
        }
        
        $r_siswa = $db->query("SELECT SUM(jumlah) as total FROM keuangan_komite_pembayaran" . $wherePembayaran)->fetch();
        $totalSiswa = $r_siswa ? floatval($r_siswa['total']) : 0;
        
        $r_manual_in = $db->query("SELECT SUM(jumlah) as total FROM keuangan_komite_transaksi" . $whereManualMasuk)->fetch();
        $totalManualIn = $r_manual_in ? floatval($r_manual_in['total']) : 0;
        
        $r_manual_out = $db->query("SELECT SUM(jumlah) as total FROM keuangan_komite_transaksi" . $whereManualKeluar)->fetch();
        $totalManualOut = $r_manual_out ? floatval($r_manual_out['total']) : 0;
        
        $totalPemasukan = $totalSiswa + $totalManualIn;
        $totalPengeluaran = $totalManualOut;
        $saldo = $totalPemasukan - $totalPengeluaran;
        
        // 2. Hitung Tagihan Belum Bayar
        $whereTagihan = "";
        if (!empty($selected_kat)) {
            if (!in_array('ALL', $allowed_cats) && !in_array($selected_kat, $allowed_cats)) {
                $whereTagihan = " WHERE 1=0";
            } else {
                $kat_q = $db->quote($selected_kat);
                $whereTagihan = " WHERE nama_tagihan IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori = $kat_q)";
            }
        } elseif (!in_array('ALL', $allowed_cats)) {
            if (empty($allowed_cats)) {
                $whereTagihan = " WHERE 1=0";
            } else {
                $quoted_cats = implode(',', array_map([$db, 'quote'], $allowed_cats));
                $whereTagihan = " WHERE nama_tagihan IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori IN ($quoted_cats))";
            }
        }
        $r_tagihan = $db->query("SELECT COUNT(*) as cnt FROM keuangan_tagihan" . $whereTagihan . (empty($whereTagihan) ? " WHERE status != 'Lunas'" : " AND status != 'Lunas'"))->fetch();
        $tagihanBelumBayar = $r_tagihan ? intval($r_tagihan['cnt']) : 0;
        
        // 3. Ambil Transaksi Terbaru (Pemasukan + Pengeluaran gabungan)
        $wherePemb = "1=1";
        $whereTrans = "1=1";
        if (!empty($selected_kat)) {
            if (!in_array('ALL', $allowed_cats) && !in_array($selected_kat, $allowed_cats)) {
                $wherePemb = "1=0";
                $whereTrans = "1=0";
            } else {
                $kat_q = $db->quote($selected_kat);
                $wherePemb = "jenis_pembayaran IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori = $kat_q)";
                $whereTrans = "kategori = $kat_q";
            }
        } elseif (!in_array('ALL', $allowed_cats)) {
            if (empty($allowed_cats)) {
                $wherePemb = "1=0";
                $whereTrans = "1=0";
            } else {
                $quoted_cats = implode(',', array_map([$db, 'quote'], $allowed_cats));
                $wherePemb = "jenis_pembayaran IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori IN ($quoted_cats))";
                $whereTrans = "kategori IN ($quoted_cats)";
            }
        }
        
        $pemasukanSiswa = $db->query("
            SELECT k.tanggal_bayar as tanggal, CONCAT('Pembayaran ', k.jenis_pembayaran, ' - ', s.nama) as keterangan, 'Pemasukan' as jenis, k.jumlah 
            FROM keuangan_komite_pembayaran k
            JOIN siswa s ON k.siswa_id = s.id
            WHERE $wherePemb
            ORDER BY k.tanggal_bayar DESC LIMIT 20
        ")->fetchAll();
        
        $transaksiManual = $db->query("
            SELECT t.tanggal_transaksi as tanggal, t.keterangan, t.jenis, t.jumlah 
            FROM keuangan_komite_transaksi t
            WHERE $whereTrans
            ORDER BY t.tanggal_transaksi DESC LIMIT 20
        ")->fetchAll();
        
        $allTrans = array_merge($pemasukanSiswa, $transaksiManual);
        usort($allTrans, function($a, $b) {
            return strtotime($b['tanggal']) - strtotime($a['tanggal']);
        });
        $transaksi = array_slice($allTrans, 0, 20);

        // Kategori list for filter dropdown
        if (!in_array('ALL', $allowed_cats)) {
            if (empty($allowed_cats)) {
                $kategoriList = [];
            } else {
                $quoted_cats = implode(',', array_map([$db, 'quote'], $allowed_cats));
                $kategoriList = $db->query("SELECT nama_kategori as kategori FROM keuangan_komite_kategori WHERE nama_kategori IN ($quoted_cats) ORDER BY nama_kategori ASC")->fetchAll();
            }
        } else {
            $kategoriList = $db->query("SELECT nama_kategori as kategori FROM keuangan_komite_kategori ORDER BY nama_kategori ASC")->fetchAll();
        }
        
        self::render('finance_dashboard', [
            'transaksi'           => $transaksi,
            'totalPemasukan'      => $totalPemasukan,
            'totalPengeluaran'    => $totalPengeluaran,
            'saldo'               => $saldo,
            'tagihanBelumBayar'   => $tagihanBelumBayar,
            'kategoriList'        => $kategoriList,
            'selected_kategori'   => $selected_kat,
            'active_page'         => 'overview',
        ], 'overview');
    }

    // Controller: Tagihan Siswa
    public static function tagihan() {
        // Otomatis disinkronisasi saat master tagihan disimpan, namun sebagai backup kita jalankan juga di sini
        self::syncTagihanMaster();
        
        $filter_kelas  = isset($_GET['kelas'])  ? trim($_GET['kelas'])  : '';
        $filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';

        $db = Database::connect('core');
        $kelasList = $db->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat, nama_kelas")->fetchAll();

        $tagihan = self::getTagihan();

        // Group tagihan by student
        $grouped = array();
        foreach ($tagihan as $t) {
            $terbayar = isset($t['terbayar']) ? floatval($t['terbayar']) : 0;
            $sisa     = floatval($t['jml']) - $terbayar;
            $row = array(
                'id'          => $t['id'],
                'jenis'       => $t['tagihan'],
                'jumlah'      => floatval($t['jml']),
                'terbayar'    => $terbayar,
                'sisa'        => $sisa,
                'jatuh_tempo' => $t['tgl_bayar'],
                'status'      => $t['status'],
            );

            $sid = $t['siswa_id'];
            if (!isset($grouped[$sid])) {
                $grouped[$sid] = array(
                    'siswa_id' => $sid,
                    'nama'     => $t['nama'],
                    'nis'      => $t['nis'],
                    'kelas'    => $t['kelas'],
                    'tagihan'  => array(),
                );
            }

            // Filter per status if needed
            if ($filter_status && $t['status'] !== $filter_status) continue;
            $grouped[$sid]['tagihan'][] = $row;
        }

        // Filter per kelas
        if ($filter_kelas) {
            $grouped = array_filter($grouped, function($s) use ($filter_kelas) {
                return stripos($s['kelas'], $filter_kelas) !== false;
            });
        }

        // Remove students with no tagihan after filter
        $grouped = array_values(array_filter($grouped, function($s) {
            return count($s['tagihan']) > 0;
        }));

        self::render('finance_tagihan', array(
            'grouped'       => $grouped,
            'kelasList'     => $kelasList,
            'filter_kelas'  => $filter_kelas,
            'filter_status' => $filter_status,
            'active_page'   => 'tagihan',
        ), 'tagihan');
    }

    // Controller: Gaji & Insentif
    public static function gaji() {
        $raw = self::getGaji();
        $mapped = array();
        foreach ($raw as $g) {
            $mapped[] = array(
                'nama' => $g['nama'], 'jabatan' => $g['jabatan'],
                'gaji_pokok' => $g['gaji_pokok'], 'tunjangan' => $g['insentif'],
                'bulan' => date('F Y'), 'status' => ($g['status']==='Dibayar' ? 'Sudah Dibayar' : 'Proses'),
            );
        }
        self::render('finance_gaji', array(
            'gaji'       => $mapped,
            'active_page'=> 'gaji',
        ), 'gaji');
    }

    // Controller: Laporan Kas
    public static function laporan() {
        $tagihan = self::getTagihan();
        $gaji    = self::getGaji();
        $totalPemasukan = 0; $totalPengeluaran = 0;
        foreach ($tagihan as $t) { if ($t['status'] === 'Lunas') $totalPemasukan += $t['jml']; }
        foreach ($gaji as $g) { $totalPengeluaran += $g['gaji_pokok'] + $g['insentif'] - $g['potongan']; }
        $per_bulan = array(
            array('bulan'=>date('M'),'pemasukan'=>$totalPemasukan,'pengeluaran'=>$totalPengeluaran),
        );
        self::render('finance_laporan', array(
            'totalPemasukan'  => $totalPemasukan,
            'totalPengeluaran'=> $totalPengeluaran,
            'saldo'           => $totalPemasukan - $totalPengeluaran,
            'per_bulan'       => $per_bulan,
            'active_page'     => 'laporan',
        ), 'laporan');
    }

    // Controller: Komponen Biaya
    public static function biaya() {
        $raw = self::getBiaya();
        $mapped = array();
        foreach ($raw as $b) {
            $mapped[] = array(
                'id' => $b['id'],
                'nama'=> $b['nama'],'nominal'=>$b['jml'],
                'periode'=>$b['berlaku'],'berlaku_untuk'=>$b['berlaku_untuk'],
                'status'=> $b['aktif'] ? 'Aktif' : 'Nonaktif',
            );
        }
        self::render('finance_biaya', array(
            'biaya_list'  => $mapped,
            'active_page' => 'biaya',
        ), 'biaya');
    }

    // --- MODUL BOS ---
    public static function bosPemasukan() {
        $db = Database::connect('core');
        $data = $db->query("SELECT id, tanggal_transaksi as tanggal, keterangan, kategori as sumber, jumlah FROM keuangan_bos_transaksi WHERE jenis='Pemasukan' ORDER BY tanggal_transaksi DESC")->fetchAll();
        self::render('finance_bos_pemasukan', ['pemasukan' => $data, 'active_page' => 'bos_pemasukan'], 'bos_pemasukan');
    }

    public static function bosPengeluaran() {
        $db = Database::connect('core');
        $data = $db->query("SELECT id, tanggal_transaksi as tanggal, keterangan, kategori, jumlah, '' as bukti FROM keuangan_bos_transaksi WHERE jenis='Pengeluaran' ORDER BY tanggal_transaksi DESC")->fetchAll();
        self::render('finance_bos_pengeluaran', ['pengeluaran' => $data, 'active_page' => 'bos_pengeluaran'], 'bos_pengeluaran');
    }

    public static function bosLaporan() {
        self::render('finance_bos_laporan', ['active_page' => 'bos_laporan'], 'bos_laporan');
    }

    // --- MODUL KOMITE ---
    public static function komitePemasukan() {
        $db = Database::connect('core');
        $allowed_cats = self::getAllowedCategories();
        $uid = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
        
        $whereTrans = "jenis='Pemasukan'";
        $wherePemb = "1=1";
        
        if (!in_array('ALL', $allowed_cats)) {
            if (empty($allowed_cats)) {
                $whereTrans = "1=0";
                $wherePemb = "1=0";
            } else {
                $quoted_cats = implode(',', array_map([$db, 'quote'], $allowed_cats));
                $whereTrans = "jenis='Pemasukan' AND (kategori IN ($quoted_cats) OR petugas_id = $uid)";
                $wherePemb = "k.jenis_pembayaran IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori IN ($quoted_cats))";
            }
        }
        
        $transaksi = $db->query("SELECT id, tanggal_transaksi as tanggal, keterangan, kategori as sumber, jumlah, 'Manual' as tipe FROM keuangan_komite_transaksi WHERE $whereTrans ORDER BY tanggal_transaksi DESC")->fetchAll();
        
        $pembayaran = $db->query("
            SELECT k.id, k.tanggal_bayar as tanggal, CONCAT('Pembayaran ', k.jenis_pembayaran, ': ', s.nama) as keterangan, 'Siswa' as sumber, k.jumlah, 'Siswa' as tipe
            FROM keuangan_komite_pembayaran k
            JOIN siswa s ON k.siswa_id = s.id
            WHERE $wherePemb
            ORDER BY k.tanggal_bayar DESC
        ")->fetchAll();
        
        $data = array_merge($transaksi, $pembayaran);
        usort($data, function($a, $b) { return strtotime($b['tanggal']) - strtotime($a['tanggal']); });
        
        $kategoriList = [];
        if (in_array('ALL', $allowed_cats)) {
            $kategoriList = $db->query("SELECT nama_kategori FROM keuangan_komite_kategori ORDER BY nama_kategori ASC")->fetchAll(\PDO::FETCH_COLUMN);
        } else {
            $kategoriList = $allowed_cats;
        }
        
        self::render('finance_komite_pemasukan', ['pemasukan' => $data, 'kategoriList' => $kategoriList, 'active_page' => 'komite_pemasukan'], 'komite_pemasukan');
    }

    public static function komitePengeluaran() {
        $db = Database::connect('core');
        $allowed_cats = self::getAllowedCategories();
        $uid = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
        
        $whereTrans = "jenis='Pengeluaran'";
        if (!in_array('ALL', $allowed_cats)) {
            if (empty($allowed_cats)) {
                $whereTrans = "1=0";
            } else {
                $quoted_cats = implode(',', array_map([$db, 'quote'], $allowed_cats));
                $whereTrans = "jenis='Pengeluaran' AND (kategori IN ($quoted_cats) OR petugas_id = $uid)";
            }
        }
        
        $data = $db->query("SELECT id, tanggal_transaksi as tanggal, keterangan, kategori, jumlah, '' as bukti FROM keuangan_komite_transaksi WHERE $whereTrans ORDER BY tanggal_transaksi DESC")->fetchAll();
        
        $kategoriList = [];
        if (in_array('ALL', $allowed_cats)) {
            $kategoriList = $db->query("SELECT nama_kategori FROM keuangan_komite_kategori ORDER BY nama_kategori ASC")->fetchAll(\PDO::FETCH_COLUMN);
        } else {
            $kategoriList = $allowed_cats;
        }
        
        self::render('finance_komite_pengeluaran', ['pengeluaran' => $data, 'kategoriList' => $kategoriList, 'active_page' => 'komite_pengeluaran'], 'komite_pengeluaran');
    }

    // --- Kategori Tagihan ---
    public static function komiteKategori() {
        self::guard('komite_jenis');
        $db = Database::connect('core');
        $kategoriList = $db->query("
            SELECT k.*, g.nama as petugas_nama 
            FROM keuangan_komite_kategori k 
            LEFT JOIN guru g ON k.guru_id = g.id 
            ORDER BY k.nama_kategori ASC
        ")->fetchAll();
        $gurus = $db->query("SELECT id, nama, nip FROM guru ORDER BY nama ASC")->fetchAll();
        
        self::render('finance_komite_kategori', [
            'kategoriList' => $kategoriList, 
            'gurus' => $gurus,
            'active_page' => 'komite_kategori'
        ], 'komite_jenis');
    }

    public static function saveKomiteKategori() {
        self::guard('komite_jenis');
        $db = Database::connect('core');
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $nama_kategori = isset($_POST['nama_kategori']) ? trim($_POST['nama_kategori']) : '';
        $guru_id = isset($_POST['guru_id']) && intval($_POST['guru_id']) > 0 ? intval($_POST['guru_id']) : null;

        if (!empty($nama_kategori)) {
            // Check existence
            $exists = $db->prepare("SELECT id FROM keuangan_komite_kategori WHERE nama_kategori = ? AND id != ?");
            $exists->execute([$nama_kategori, $id]);
            if (!$exists->fetch()) {
                if ($id > 0) {
                    $stmt = $db->prepare("UPDATE keuangan_komite_kategori SET nama_kategori = ?, guru_id = ? WHERE id = ?");
                    $stmt->execute([$nama_kategori, $guru_id, $id]);
                } else {
                    $stmt = $db->prepare("INSERT INTO keuangan_komite_kategori (nama_kategori, guru_id) VALUES (?, ?)");
                    $stmt->execute([$nama_kategori, $guru_id]);
                }
                
                // Sinkronisasi hak akses pembayaran guru
                self::syncAllAksesPembayaran();
            }
        }
        redirect('/admin/finance/komite/kategori?msg=saved');
        exit;
    }

    public static function deleteKomiteKategori($id) {
        self::guard('komite_jenis');
        $db = Database::connect('core');
        $db->prepare("DELETE FROM keuangan_komite_kategori WHERE id = ?")->execute([$id]);
        self::syncAllAksesPembayaran();
        redirect('/admin/finance/komite/kategori?msg=deleted');
        exit;
    }

    public static function komiteJenisTagihan() {
        $db = Database::connect('core');
        $rawJenis = $db->query("
            SELECT j.*, k.nama_kelas, ta.name as nama_tahun
            FROM keuangan_komite_jenis j
            LEFT JOIN kelas k ON j.target_kelas_id = k.id
            LEFT JOIN tahun_ajaran ta ON j.tahun_ajaran_id = ta.id
            ORDER BY j.id DESC
        ")->fetchAll();
        
        $allSiswasRiwayat = $db->query("
            SELECT s.id, s.nama, s.nis, r.kelas_id, r.tahun_ajaran_id
            FROM riwayat_kelas_siswa r
            JOIN siswa s ON r.siswa_id = s.id
            ORDER BY s.nama ASC
        ")->fetchAll();
        
        $allSiswasRaw = $db->query("SELECT id, nama, nis, kelas_id FROM siswa")->fetchAll();
        $siswaLookup = [];
        foreach($allSiswasRaw as $s) { $siswaLookup[$s['id']] = $s['nama']; }
        
        $tahunAjaranList = $db->query("SELECT id, name FROM tahun_ajaran ORDER BY name DESC")->fetchAll();

        $kelasList = $db->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat, nama_kelas")->fetchAll();
        $kelasListLookup = [];
        foreach($kelasList as $k) { $kelasListLookup[$k['id']] = $k['nama_kelas']; }

        $jenisList = [];
        foreach($rawJenis as $j) {
            $names = [];
            if($j['target_tipe'] == 'Siswa' && !empty($j['target_siswa_id'])) {
                $ids = explode(',', $j['target_siswa_id']);
                foreach($ids as $id) {
                    if(isset($siswaLookup[$id])) $names[] = $siswaLookup[$id];
                }
                $j['nama_siswa'] = implode(', ', $names);
            } elseif ($j['target_tipe'] == 'Kelas' && !empty($j['target_kelas_id'])) {
                $ids = explode(',', $j['target_kelas_id']);
                $knames = [];
                foreach($ids as $id) {
                    if(isset($kelasListLookup[$id])) $knames[] = $kelasListLookup[$id];
                }
                $j['nama_kelas'] = implode(', ', $knames);
            }
            $jenisList[] = $j;
        }
        $kategoriList = $db->query("SELECT nama_kategori as kategori FROM keuangan_komite_kategori ORDER BY nama_kategori ASC")->fetchAll();
        
        self::render('finance_komite_jenis', [
            'kategoriList' => $kategoriList,
            'jenisList' => $jenisList,
            'kelasList' => $kelasList,
            'siswas' => $allSiswasRiwayat,
            'allSiswasRaw' => $allSiswasRaw,
            'tahunAjaranList' => $tahunAjaranList,
            'active_page' => 'komite_jenis'
        ], 'komite_jenis');
    }

    public static function komitePembayaranSiswa() {
        self::autoRolloverJatuhTempo();
        $db = Database::connect('core');
        $kelas_id = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : 0;
        
        $allowed_cats = self::getAllowedCategories();
        
        $kelasList = $db->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat, nama_kelas")->fetchAll();
        
        // Filter jenisTagihan by allowed categories
        if (!in_array('ALL', $allowed_cats)) {
            if (empty($allowed_cats)) {
                $jenisTagihan = [];
            } else {
                $quoted_cats = implode(',', array_map([$db, 'quote'], $allowed_cats));
                $jenisTagihan = $db->query("SELECT * FROM keuangan_komite_jenis WHERE kategori IN ($quoted_cats) ORDER BY nama_tagihan ASC")->fetchAll();
            }
        } else {
            $jenisTagihan = $db->query("SELECT * FROM keuangan_komite_jenis ORDER BY nama_tagihan ASC")->fetchAll();
        }

        $allSiswaList = $db->query("SELECT id, nama, nis, kelas_id FROM siswa ORDER BY nama ASC")->fetchAll();

        // Siswas untuk dropdown modal (berdasarkan filter kelas jika ada)
        if ($kelas_id > 0) {
            $siswas = $db->query("SELECT id, nis, nama FROM siswa WHERE kelas_id = $kelas_id ORDER BY nama ASC")->fetchAll();
        } else {
            $siswas = $allSiswaList;
        }

        // Pembayaran: tampilkan semua atau filter per kelas & allowed categories
        $whereClause = " WHERE 1=1";
        if ($kelas_id > 0) {
            $whereClause .= " AND s.kelas_id = " . intval($kelas_id);
        }
        if (!in_array('ALL', $allowed_cats)) {
            if (empty($allowed_cats)) {
                $whereClause .= " AND 1=0";
            } else {
                $quoted_cats = implode(',', array_map([$db, 'quote'], $allowed_cats));
                $whereClause .= " AND k.jenis_pembayaran IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori IN ($quoted_cats))";
            }
        }

        $sql = "
            SELECT k.id as bayar_id, k.jenis_pembayaran, k.periode, k.jumlah, k.tanggal_bayar,
                   s.nama, s.nis, c.nama_kelas as kelas, t.status as status_tagihan
            FROM keuangan_komite_pembayaran k
            JOIN siswa s ON k.siswa_id = s.id
            LEFT JOIN kelas c ON s.kelas_id = c.id
            LEFT JOIN keuangan_tagihan t ON (k.siswa_id = t.siswa_id AND TRIM(k.jenis_pembayaran) = TRIM(t.nama_tagihan))
            $whereClause
            ORDER BY k.tanggal_bayar DESC, k.id DESC
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        
        $pembayaran = [];
        foreach ($stmt->fetchAll() as $r) {
            $pembayaran[] = [
                'id'      => $r['bayar_id'],
                'tanggal' => $r['tanggal_bayar'],
                'nama'    => $r['nama'],
                'nis'     => $r['nis'],
                'kelas'   => $r['kelas'],
                'jumlah'  => $r['jumlah'],
                'jenis'   => $r['jenis_pembayaran'],
                'periode' => $r['periode'],
                'status'  => (!empty($r['status_tagihan'])) ? $r['status_tagihan'] : 'Belum Bayar'
            ];
        }
        
        // Ambil data tagihan & pembayaran untuk validasi input di frontend (filtered)
        $whereTagihanData = "";
        if (!in_array('ALL', $allowed_cats)) {
            if (empty($allowed_cats)) {
                $whereTagihanData = " WHERE 1=0";
            } else {
                $quoted_cats = implode(',', array_map([$db, 'quote'], $allowed_cats));
                $whereTagihanData = " WHERE t.nama_tagihan IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori IN ($quoted_cats))";
            }
        }
        $tagihanData = $db->query("
            SELECT t.siswa_id, t.nama_tagihan, t.jumlah_tagihan,
                   (SELECT SUM(jumlah) FROM keuangan_komite_pembayaran p WHERE p.siswa_id = t.siswa_id AND p.jenis_pembayaran = t.nama_tagihan) as terbayar
            FROM keuangan_tagihan t
            $whereTagihanData
        ")->fetchAll();

        $role_id = $_SESSION['role_id'] ?? 0;
        $can_delete = in_array($role_id, [1, 99]);
        if (!$can_delete) {
            $is_app_admin = $db->prepare("SELECT id FROM app_admins WHERE user_id = ? AND app_code = 'keuangan'");
            $is_app_admin->execute([$_SESSION['user_id']]);
            if ($is_app_admin->fetch()) {
                $can_delete = true;
            } else {
                if (isset($_SESSION['guru_id'])) {
                    $cekDel = $db->prepare("SELECT can_delete FROM keuangan_akses WHERE guru_id = ?");
                    $cekDel->execute([$_SESSION['guru_id']]);
                    $adm = $cekDel->fetch();
                    if ($adm && $adm['can_delete'] == 1) $can_delete = true;
                }
            }
        }

        self::render('finance_komite_pembayaran_siswa', [
            'pembayaran'   => $pembayaran,
            'can_delete'   => $can_delete,
            'kelasList'    => $kelasList,
            'kelas_id'     => $kelas_id,
            'siswas'       => $siswas,
            'jenisTagihan' => $jenisTagihan,
            'allSiswaList' => $allSiswaList,
            'tagihanData'  => $tagihanData,
            'active_page'  => 'komite_pembayaran_siswa'
        ], 'komite_pembayaran_siswa');
    }

    public static function autoRolloverJatuhTempo() {
        $db = Database::connect('core');
        $today = date('Y-m-d');
        $y = (int)date('Y');
        $jt_dates = [
            "$y-03-31",
            "$y-06-15",
            "$y-09-30",
            "$y-12-15",
            ($y+1)."-03-31"
        ];
        $next_jt = $jt_dates[0];
        foreach ($jt_dates as $d) {
            if ($today <= $d) {
                $next_jt = $d;
                break;
            }
        }
        $db->prepare("UPDATE keuangan_tagihan SET jatuh_tempo = ? WHERE jatuh_tempo < ? AND status != 'Lunas'")->execute([$next_jt, $today]);
    }

    public static function komiteLaporan() {
        $db = Database::connect('core');

        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
        $end_date   = isset($_GET['end_date']) ? $_GET['end_date'] : '';
        $kategori   = isset($_GET['kategori']) ? $_GET['kategori'] : '';
        $petugas_id = isset($_GET['petugas_id']) ? $_GET['petugas_id'] : '';

        $allowed_cats = self::getAllowedCategories();

        $wherePembayaran = " WHERE 1=1";
        $whereTransaksiMasuk = " WHERE jenis='Pemasukan'";
        $whereTransaksiKeluar = " WHERE jenis='Pengeluaran'";

        if (!empty($start_date) && !empty($end_date)) {
            $wherePembayaran .= " AND DATE(tanggal_bayar) >= '$start_date' AND DATE(tanggal_bayar) <= '$end_date'";
            $whereTransaksiMasuk .= " AND DATE(tanggal_transaksi) >= '$start_date' AND DATE(tanggal_transaksi) <= '$end_date'";
            $whereTransaksiKeluar .= " AND DATE(tanggal_transaksi) >= '$start_date' AND DATE(tanggal_transaksi) <= '$end_date'";
        }

        if (!empty($petugas_id)) {
            $p_val = intval($petugas_id);
            $wherePembayaran .= " AND (petugas_id = $p_val OR (petugas_id IS NULL AND jenis_pembayaran IN (
                SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori IN (
                    SELECT nama_kategori FROM keuangan_komite_kategori WHERE guru_id IN (
                        SELECT id FROM guru WHERE user_id = $p_val
                    )
                )
            )))";
            $whereTransaksiMasuk .= " AND (petugas_id = $p_val OR (petugas_id IS NULL AND kategori IN (
                SELECT nama_kategori FROM keuangan_komite_kategori WHERE guru_id IN (
                    SELECT id FROM guru WHERE user_id = $p_val
                )
            )))";
            $whereTransaksiKeluar .= " AND (petugas_id = $p_val OR (petugas_id IS NULL AND kategori IN (
                SELECT nama_kategori FROM keuangan_komite_kategori WHERE guru_id IN (
                    SELECT id FROM guru WHERE user_id = $p_val
                )
            )))";
        }

        if (!empty($kategori)) {
            if (!in_array('ALL', $allowed_cats) && !in_array($kategori, $allowed_cats)) {
                $wherePembayaran .= " AND 1=0";
                $whereTransaksiMasuk .= " AND 1=0";
                $whereTransaksiKeluar .= " AND 1=0";
            } else {
                $kategori_q = $db->quote($kategori);
                $wherePembayaran .= " AND jenis_pembayaran IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori = $kategori_q)";
                $whereTransaksiMasuk .= " AND kategori = $kategori_q";
                $whereTransaksiKeluar .= " AND kategori = $kategori_q";
            }
        } elseif (!in_array('ALL', $allowed_cats)) {
            if (empty($allowed_cats)) {
                $wherePembayaran .= " AND 1=0";
                $whereTransaksiMasuk .= " AND 1=0";
                $whereTransaksiKeluar .= " AND 1=0";
            } else {
                $quoted_cats = implode(',', array_map([$db, 'quote'], $allowed_cats));
                $wherePembayaran .= " AND jenis_pembayaran IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori IN ($quoted_cats))";
                $whereTransaksiMasuk .= " AND kategori IN ($quoted_cats)";
                $whereTransaksiKeluar .= " AND kategori IN ($quoted_cats)";
            }
        }

        // Total pemasukan dari pembayaran siswa
        $r1 = $db->query("SELECT SUM(jumlah) as total FROM keuangan_komite_pembayaran $wherePembayaran")->fetch();
        $totalSiswa = $r1 ? floatval($r1['total']) : 0;
        // Total pemasukan manual
        $r2 = $db->query("SELECT SUM(jumlah) as total FROM keuangan_komite_transaksi $whereTransaksiMasuk")->fetch();
        $totalManual = $r2 ? floatval($r2['total']) : 0;
        // Total pengeluaran manual
        $r3 = $db->query("SELECT SUM(jumlah) as total FROM keuangan_komite_transaksi $whereTransaksiKeluar")->fetch();
        $totalKeluar = $r3 ? floatval($r3['total']) : 0;

        $totalMasuk   = floatval($totalSiswa) + floatval($totalManual);
        $saldo        = $totalMasuk - floatval($totalKeluar);

        // Kategori Summary
        $kategoriSummaryRawSiswa = $db->query("
            SELECT COALESCE(kj.kategori, 'Lainnya') as kategori, SUM(k.jumlah) as total
            FROM keuangan_komite_pembayaran k
            LEFT JOIN (SELECT DISTINCT nama_tagihan, kategori FROM keuangan_komite_jenis) kj ON k.jenis_pembayaran = kj.nama_tagihan
            $wherePembayaran
            GROUP BY COALESCE(kj.kategori, 'Lainnya')
        ")->fetchAll();

        $kategoriSummaryRawManual = $db->query("
            SELECT kategori, SUM(jumlah) as total
            FROM keuangan_komite_transaksi
            $whereTransaksiMasuk
            GROUP BY kategori
        ")->fetchAll();

        $kategoriSummary = [];
        foreach($kategoriSummaryRawSiswa as $row) {
            $cat = $row['kategori'] ?: 'Lainnya';
            if(!isset($kategoriSummary[$cat])) $kategoriSummary[$cat] = 0;
            $kategoriSummary[$cat] += floatval($row['total']);
        }
        foreach($kategoriSummaryRawManual as $row) {
            $cat = $row['kategori'] ?: 'Lainnya';
            if(!isset($kategoriSummary[$cat])) $kategoriSummary[$cat] = 0;
            $kategoriSummary[$cat] += floatval($row['total']);
        }
        arsort($kategoriSummary);

        // Tagihan summary
        $whereTagihanStats = "";
        if (!in_array('ALL', $allowed_cats)) {
            if (empty($allowed_cats)) {
                $whereTagihanStats = " WHERE 1=0";
            } else {
                $quoted_cats = implode(',', array_map([$db, 'quote'], $allowed_cats));
                $whereTagihanStats = " WHERE nama_tagihan IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori IN ($quoted_cats))";
            }
        }
        $tagihanStats = $db->query("
            SELECT status, COUNT(*) as jml, SUM(jumlah_tagihan) as total 
            FROM keuangan_tagihan 
            $whereTagihanStats
            GROUP BY status
        ")->fetchAll();

        // Pemasukan 6 bulan terakhir
        $whereBulanan = " WHERE tanggal_bayar >= DATE_SUB(NOW(), INTERVAL 6 MONTH)";
        if (!in_array('ALL', $allowed_cats)) {
            if (empty($allowed_cats)) {
                $whereBulanan .= " AND 1=0";
            } else {
                $quoted_cats = implode(',', array_map([$db, 'quote'], $allowed_cats));
                $whereBulanan .= " AND jenis_pembayaran IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori IN ($quoted_cats))";
            }
        }
        $bulanan = $db->query("
            SELECT DATE_FORMAT(tanggal_bayar,'%Y-%m') as bln, SUM(jumlah) as total
            FROM keuangan_komite_pembayaran
            $whereBulanan
            GROUP BY bln
            ORDER BY bln ASC
        ")->fetchAll();

        // Filter kategori dropdown
        if (!in_array('ALL', $allowed_cats)) {
            if (empty($allowed_cats)) {
                $kategoriList = [];
            } else {
                $quoted_cats = implode(',', array_map([$db, 'quote'], $allowed_cats));
                $kategoriList = $db->query("SELECT nama_kategori as kategori FROM keuangan_komite_kategori WHERE nama_kategori IN ($quoted_cats) ORDER BY nama_kategori ASC")->fetchAll();
            }
        } else {
            $kategoriList = $db->query("SELECT nama_kategori as kategori FROM keuangan_komite_kategori ORDER BY nama_kategori ASC")->fetchAll();
        }

        $petugasList = $db->query("SELECT u.id, COALESCE(g.nama, u.username) as nama FROM users u LEFT JOIN guru g ON u.id = g.user_id WHERE u.role_id IN (1, 99) OR u.id IN (SELECT user_id FROM guru WHERE id IN (SELECT DISTINCT guru_id FROM keuangan_komite_kategori)) ORDER BY nama ASC")->fetchAll();

        // Mapping kategori ke petugas_id (user_id)
        $kategoriMapping = $db->query("
            SELECT kk.nama_kategori as kategori, u.id as user_id 
            FROM keuangan_komite_kategori kk
            JOIN guru g ON kk.guru_id = g.id
            JOIN users u ON g.user_id = u.id
        ")->fetchAll();

        self::render('finance_komite_laporan', [
            'start_date'    => $start_date,
            'end_date'      => $end_date,
            'kategori'      => $kategori,
            'petugas_id'    => $petugas_id,
            'kategoriList'  => $kategoriList,
            'petugasList'   => $petugasList,
            'kategoriMapping' => $kategoriMapping,
            'kategoriSummary' => $kategoriSummary,
            'totalMasuk'    => $totalMasuk,
            'totalKeluar'   => floatval($totalKeluar),
            'saldo'         => $saldo,
            'tagihanStats'  => $tagihanStats,
            'bulanan'       => $bulanan,
            'is_restricted' => !in_array('ALL', $allowed_cats),
            'allowed_cats'  => $allowed_cats,
            'active_page'   => 'komite_laporan'
        ], 'komite_laporan');
    }

    public static function cetakLaporan() {
        self::guard('komite_laporan');
        $db = Database::connect('core');

        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
        $end_date   = isset($_GET['end_date']) ? $_GET['end_date'] : '';
        $kategori   = isset($_GET['kategori']) ? $_GET['kategori'] : '';
        $petugas_id = isset($_GET['petugas_id']) ? $_GET['petugas_id'] : '';

        $allowed_cats = self::getAllowedCategories();

        $wherePembayaran = " WHERE 1=1";
        $whereTransaksiMasuk = " WHERE t.jenis='Pemasukan'";
        $whereTransaksiKeluar = " WHERE t.jenis='Pengeluaran'";

        if (!empty($start_date) && !empty($end_date)) {
            $wherePembayaran .= " AND DATE(k.tanggal_bayar) >= '$start_date' AND DATE(k.tanggal_bayar) <= '$end_date'";
            $whereTransaksiMasuk .= " AND DATE(t.tanggal_transaksi) >= '$start_date' AND DATE(t.tanggal_transaksi) <= '$end_date'";
            $whereTransaksiKeluar .= " AND DATE(t.tanggal_transaksi) >= '$start_date' AND DATE(t.tanggal_transaksi) <= '$end_date'";
        }

        if (!empty($petugas_id)) {
            $p_val = intval($petugas_id);
            $wherePembayaran .= " AND (k.petugas_id = $p_val OR (k.petugas_id IS NULL AND k.jenis_pembayaran IN (
                SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori IN (
                    SELECT nama_kategori FROM keuangan_komite_kategori WHERE guru_id IN (
                        SELECT id FROM guru WHERE user_id = $p_val
                    )
                )
            )))";
            $whereTransaksiMasuk .= " AND (t.petugas_id = $p_val OR (t.petugas_id IS NULL AND t.kategori IN (
                SELECT nama_kategori FROM keuangan_komite_kategori WHERE guru_id IN (
                    SELECT id FROM guru WHERE user_id = $p_val
                )
            )))";
            $whereTransaksiKeluar .= " AND (t.petugas_id = $p_val OR (t.petugas_id IS NULL AND t.kategori IN (
                SELECT nama_kategori FROM keuangan_komite_kategori WHERE guru_id IN (
                    SELECT id FROM guru WHERE user_id = $p_val
                )
            )))";
        }

        if (!empty($kategori)) {
            if (!in_array('ALL', $allowed_cats) && !in_array($kategori, $allowed_cats)) {
                $wherePembayaran .= " AND 1=0";
                $whereTransaksiMasuk .= " AND 1=0";
                $whereTransaksiKeluar .= " AND 1=0";
            } else {
                $kategori_q = $db->quote($kategori);
                $wherePembayaran .= " AND k.jenis_pembayaran IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori = $kategori_q)";
                $whereTransaksiMasuk .= " AND t.kategori = $kategori_q";
                $whereTransaksiKeluar .= " AND t.kategori = $kategori_q";
            }
        } elseif (!in_array('ALL', $allowed_cats)) {
            if (empty($allowed_cats)) {
                $wherePembayaran .= " AND 1=0";
                $whereTransaksiMasuk .= " AND 1=0";
                $whereTransaksiKeluar .= " AND 1=0";
            } else {
                $quoted_cats = implode(',', array_map([$db, 'quote'], $allowed_cats));
                $wherePembayaran .= " AND k.jenis_pembayaran IN (SELECT nama_tagihan FROM keuangan_komite_jenis WHERE kategori IN ($quoted_cats))";
                $whereTransaksiMasuk .= " AND t.kategori IN ($quoted_cats)";
                $whereTransaksiKeluar .= " AND t.kategori IN ($quoted_cats)";
            }
        }

        // Pemasukan Siswa
        $pemasukanSiswa = $db->query("
            SELECT k.tanggal_bayar as tanggal, 
                   CONCAT('Pembayaran Tagihan (', k.jenis_pembayaran, ') - ', s.nama) as keterangan, 
                   COALESCE(kj.kategori, 'Lainnya') as kategori, 
                   k.jumlah, 
                   COALESCE(gu.nama, u.username, gu_cat.nama, '-') as petugas 
            FROM keuangan_komite_pembayaran k
            JOIN siswa s ON k.siswa_id = s.id
            LEFT JOIN (SELECT DISTINCT nama_tagihan, kategori FROM keuangan_komite_jenis) kj ON k.jenis_pembayaran = kj.nama_tagihan
            LEFT JOIN users u ON k.petugas_id = u.id
            LEFT JOIN guru gu ON u.id = gu.user_id
            LEFT JOIN keuangan_komite_kategori kk ON kj.kategori = kk.nama_kategori
            LEFT JOIN guru gu_cat ON kk.guru_id = gu_cat.id
            $wherePembayaran
            ORDER BY k.tanggal_bayar ASC
        ")->fetchAll();

        // Pemasukan Manual
        $pemasukanManual = $db->query("
            SELECT t.tanggal_transaksi as tanggal, 
                   t.keterangan, 
                   t.kategori, 
                   t.jumlah, 
                   COALESCE(gu.nama, u.username, gu_cat.nama, '-') as petugas 
            FROM keuangan_komite_transaksi t
            LEFT JOIN users u ON t.petugas_id = u.id
            LEFT JOIN guru gu ON u.id = gu.user_id
            LEFT JOIN keuangan_komite_kategori kk ON t.kategori = kk.nama_kategori
            LEFT JOIN guru gu_cat ON kk.guru_id = gu_cat.id
            $whereTransaksiMasuk
            ORDER BY t.tanggal_transaksi ASC
        ")->fetchAll();

        $detailPemasukan = array_merge($pemasukanSiswa, $pemasukanManual);
        usort($detailPemasukan, function($a, $b) {
            return strtotime($a['tanggal']) - strtotime($b['tanggal']);
        });

        // Pengeluaran Manual
        $detailPengeluaran = $db->query("
            SELECT t.tanggal_transaksi as tanggal, 
                   t.keterangan, 
                   t.kategori, 
                   t.jumlah, 
                   COALESCE(gu.nama, u.username, gu_cat.nama, '-') as petugas 
            FROM keuangan_komite_transaksi t
            LEFT JOIN users u ON t.petugas_id = u.id
            LEFT JOIN guru gu ON u.id = gu.user_id
            LEFT JOIN keuangan_komite_kategori kk ON t.kategori = kk.nama_kategori
            LEFT JOIN guru gu_cat ON kk.guru_id = gu_cat.id
            $whereTransaksiKeluar
            ORDER BY t.tanggal_transaksi ASC
        ")->fetchAll();

        $totalMasuk = array_sum(array_column($detailPemasukan, 'jumlah'));
        $totalKeluar = array_sum(array_column($detailPengeluaran, 'jumlah'));
        $saldo = $totalMasuk - $totalKeluar;

        $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();

        $petugas_nama = 'Semua Petugas';
        if (!empty($petugas_id)) {
            $p = $db->query("SELECT COALESCE(g.nama, u.username) as nama FROM users u LEFT JOIN guru g ON u.id = g.user_id WHERE u.id = " . intval($petugas_id))->fetch();
            if ($p) $petugas_nama = $p['nama'];
        }

        // TTD Bendahara Dinamis
        $bendahara_nama = '';
        $bendahara_nip = '';
        $active_cat = '';
        if (!empty($kategori)) {
            $active_cat = $kategori;
        } elseif (!in_array('ALL', $allowed_cats) && count($allowed_cats) === 1) {
            $active_cat = reset($allowed_cats);
        }

        if (!empty($active_cat)) {
            $guru_cat = $db->query("
                SELECT g.nama, g.nip 
                FROM keuangan_komite_kategori kk
                JOIN guru g ON kk.guru_id = g.id
                WHERE kk.nama_kategori = " . $db->quote($active_cat)
            )->fetch();
            if ($guru_cat) {
                $bendahara_nama = $guru_cat['nama'];
                $bendahara_nip = $guru_cat['nip'] ?: '';
            }
        }

        if (empty($bendahara_nama) && !empty($petugas_id)) {
            $p_guru = $db->query("
                SELECT g.nama, g.nip 
                FROM users u
                LEFT JOIN guru g ON u.id = g.user_id
                WHERE u.id = " . intval($petugas_id)
            )->fetch();
            if ($p_guru) {
                $bendahara_nama = $p_guru['nama'];
                $bendahara_nip = $p_guru['nip'] ?: '';
            }
        }

        if (empty($bendahara_nama) && isset($_SESSION['role_id']) && !in_array($_SESSION['role_id'], [1, 99])) {
            $bendahara_nama = $_SESSION['nama'] ?? '';
            $u_guru = $db->query("SELECT nip FROM guru WHERE user_id = " . intval($_SESSION['user_id']))->fetch();
            if ($u_guru) {
                $bendahara_nip = $u_guru['nip'] ?: '';
            }
        }

        // Title suffix
        $title_suffix = 'KOMITE & LAINNYA';
        if (!empty($kategori)) {
            $title_suffix = strtoupper($kategori);
        } elseif (!in_array('ALL', $allowed_cats) && count($allowed_cats) === 1) {
            $title_suffix = strtoupper(reset($allowed_cats));
        }

        extract([
            'inst' => $inst,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'kategori' => $kategori,
            'petugas_nama' => $petugas_nama,
            'detailPemasukan' => $detailPemasukan,
            'detailPengeluaran' => $detailPengeluaran,
            'totalMasuk' => $totalMasuk,
            'totalKeluar' => $totalKeluar,
            'saldo' => $saldo,
            'allowed_cats' => $allowed_cats,
            'is_restricted' => !in_array('ALL', $allowed_cats),
            'bendahara_nama' => $bendahara_nama,
            'bendahara_nip' => $bendahara_nip,
            'title_suffix' => $title_suffix
        ]);
        
        include __DIR__ . '/../../resources/views/admin/finance_komite_laporan_cetak.php';
    }

    // Alias lama
    public static function index() {
        self::overview();
    }

    // --- MANAJEMEN AKSES BENDAHARA ---
    public static function aksesBendahara() {
        $db = Database::connect('core');
        self::syncAllAksesPembayaran();
        
        // Ambil data akses yang sudah ada dan kategori tanggung jawabnya
        $aksesRaw = $db->query("
            SELECT a.*, g.nama, g.nip, g.user_id,
                   (SELECT GROUP_CONCAT(nama_kategori ORDER BY nama_kategori ASC SEPARATOR ', ') 
                    FROM keuangan_komite_kategori 
                    WHERE guru_id = a.guru_id) as kategori_tanggung_jawab
            FROM keuangan_akses a 
            JOIN guru g ON a.guru_id = g.id 
            ORDER BY g.nama ASC
        ")->fetchAll();
        
        $aksesList = [];
        foreach ($aksesRaw as $a) {
            $a['akses_pembayaran_arr'] = json_decode($a['akses_pembayaran'], true) ?: [];
            $a['akses_menu_arr'] = json_decode($a['akses_menu'], true) ?: [];
            $aksesList[] = $a;
        }
        
        // Menu Admin/Kasir yang tersedia (BOS dihapus karena tidak relevan)
        $availableMenus = [
            'overview' => 'Overview',
            'tagihan' => 'Tagihan Siswa',
            'gaji' => 'Gaji & Insentif',
            'laporan' => 'Laporan Kas',
            'biaya' => 'Komponen Biaya',
            'komite_pemasukan' => 'Komite Pemasukan',
            'komite_pengeluaran' => 'Komite Pengeluaran',
            'komite_kategori' => 'Master Kategori',
            'komite_jenis' => 'Jenis Tagihan',
            'komite_pembayaran_siswa' => 'Pembayaran Siswa',
            'komite_laporan' => 'Komite Laporan',
            'kasir_riwayat' => 'Kasir - Riwayat',
            'kasir_rekap' => 'Kasir - Rekap'
        ];

        self::render('finance_akses', [
            'aksesList' => $aksesList,
            'availableMenus' => $availableMenus,
            'active_page' => 'akses_bendahara'
        ], 'akses_bendahara');
    }

    public static function saveAksesBendahara() {
        self::guard('akses_bendahara'); // Hanya superadmin atau yang punya akses ini
        $db = Database::connect('core');
        
        $guru_id = isset($_POST['guru_id']) ? intval($_POST['guru_id']) : 0;
        $akses_pembayaran = isset($_POST['akses_pembayaran']) ? json_encode($_POST['akses_pembayaran']) : '[]';
        $akses_menu = isset($_POST['akses_menu']) ? json_encode($_POST['akses_menu']) : '[]';
        
        if ($guru_id > 0) {
            // Cek apakah sudah ada
            $cek = $db->prepare("SELECT id FROM keuangan_akses WHERE guru_id = ?");
            $cek->execute([$guru_id]);
            if ($cek->fetch()) {
                $stmt = $db->prepare("UPDATE keuangan_akses SET akses_menu = ? WHERE guru_id = ?");
                $stmt->execute([$akses_menu, $guru_id]);
            } else {
                $stmt = $db->prepare("INSERT INTO keuangan_akses (guru_id, akses_pembayaran, akses_menu) VALUES (?, '[]', ?)");
                $stmt->execute([$guru_id, $akses_menu]);
            }
            self::syncAllAksesPembayaran();
        }
        
        redirect('/admin/finance/akses?msg=saved');
        exit;
    }

    public static function deleteAksesBendahara($id) {
        self::guard('akses_bendahara');
        $db = Database::connect('core');
        $stmt = $db->prepare("DELETE FROM keuangan_akses WHERE id = ?");
        $stmt->execute([$id]);
        redirect('/admin/finance/akses?msg=deleted');
        exit;
    }

    public static function toggleDeleteAkses() {
        self::guard('akses_bendahara');
        header('Content-Type: application/json');
        $db = Database::connect('core');
        
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $val = isset($_POST['val']) ? intval($_POST['val']) : 0;
        
        if ($user_id > 0) {
            $guru = $db->query("SELECT id FROM guru WHERE user_id = $user_id")->fetch();
            if ($guru) {
                $db->prepare("UPDATE keuangan_akses SET can_delete = ? WHERE guru_id = ?")->execute([$val, $guru['id']]);
                // Remove from app_admins if mistakenly added by previous bug
                $db->prepare("DELETE FROM app_admins WHERE user_id = ? AND app_code = 'keuangan' AND can_delete IS NOT NULL")->execute([$user_id]);
            }
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User ID invalid']);
        }
        exit;
    }

    // â”€â”€â”€ API Controllers (POST/GET) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    public static function saveBiaya() {
        self::guard();
        $db = Database::connect('core');
        
        $nama = isset($_POST['nama_biaya']) ? $_POST['nama_biaya'] : '';
        $nominal = isset($_POST['nominal']) ? $_POST['nominal'] : 0;
        $periode = isset($_POST['periode']) ? $_POST['periode'] : 'Sekali Bayar';
        $berlaku_untuk = isset($_POST['berlaku_untuk']) ? $_POST['berlaku_untuk'] : 'Semua Siswa';
        
        $stmt = $db->prepare("INSERT INTO keuangan_komponen (nama_biaya, nominal, periode, berlaku_untuk, is_active) VALUES (?, ?, ?, ?, 1)");
        $stmt->execute([$nama, $nominal, $periode, $berlaku_untuk]);

        \App\Core\AuditLog::write("Komponen Biaya", "Menambah komponen biaya: $nama");

        redirect('/admin/finance/biaya?msg=success');
        exit;
    }

    public static function deleteBiaya($id) {
        self::guard();
        $db = Database::connect('core');
        $stmt = $db->prepare("DELETE FROM keuangan_komponen WHERE id = ?");
        $stmt->execute([$id]);
        redirect('/admin/finance/biaya?msg=deleted');
        exit;
    }



    public static function bayarGaji() {
        self::guard();
        $db = Database::connect('core');
        
        $nip = isset($_POST['nip']) ? $_POST['nip'] : '';
        
        // Find guru
        $guru = $db->prepare("SELECT id, nama FROM guru WHERE nip = ?");
        $guru->execute([$nip]);
        $g = $guru->fetch();
        
        if ($g) {
            $gaji_pokok = 4000000;
            $tunjangan = 500000;
            $potongan = 100000;
            $total = $gaji_pokok + $tunjangan - $potongan;
            
            $stmt = $db->prepare("INSERT INTO keuangan_gaji (guru_id, bulan, gaji_pokok, tunjangan, potongan, total, status, tanggal_bayar, keterangan) VALUES (?, ?, ?, ?, ?, ?, 'Sudah Dibayar', ?, 'Pembayaran Gaji')");
            $stmt->execute([$g['id'], date('F Y'), $gaji_pokok, $tunjangan, $potongan, $total, date('Y-m-d')]);
            
            // Insert to transaction
            $stmt2 = $db->prepare("INSERT INTO keuangan_transaksi (tanggal_bayar, keterangan, jenis, jumlah) VALUES (?, ?, 'Pengeluaran', ?)");
            $stmt2->execute([date('Y-m-d'), "Pembayaran Gaji: " . $g['nama'], $total]);
        }

        redirect('/admin/finance/gaji?msg=paid');
        exit;
    }

    public static function bayarTagihan() {
        self::guard();
        $db = Database::connect('core');
        $id = isset($_POST['tagihan_id']) ? intval($_POST['tagihan_id']) : 0;
        $jumlah_bayar = isset($_POST['jumlah_bayar']) ? floatval($_POST['jumlah_bayar']) : 0;
        
        if ($id > 0 && $jumlah_bayar > 0) {
            $t = $db->query("SELECT * FROM keuangan_tagihan WHERE id = $id")->fetch();
            if ($t && $t['status'] !== 'Lunas') {
                // 1. Catat sebagai cicilan/pelunasan
                $petugas_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
                $stmt = $db->prepare("INSERT INTO keuangan_komite_pembayaran (siswa_id, jenis_pembayaran, periode, jumlah, tanggal_bayar, petugas_id) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$t['siswa_id'], $t['nama_tagihan'], '-', $jumlah_bayar, date('Y-m-d'), $petugas_id]);

                // 2. Hitung total terbayar lalu tentukan status
                $paid = $db->query("SELECT SUM(jumlah) as total FROM keuangan_komite_pembayaran WHERE siswa_id = {$t['siswa_id']} AND jenis_pembayaran = '{$t['nama_tagihan']}'")->fetch();
                $totalBayar = $paid['total'] ? floatval($paid['total']) : 0;
                $statusBaru = ($totalBayar >= floatval($t['jumlah_tagihan'])) ? 'Lunas' : 'Menyicil';
                $db->prepare("UPDATE keuangan_tagihan SET status = ? WHERE id = ?")->execute([$statusBaru, $id]);

                // 3. Catat ke transaksi umum
                $ket = ($statusBaru === 'Lunas') ? "Pelunasan Tagihan: " : "Pembayaran Cicilan: ";
                $stmt2 = $db->prepare("INSERT INTO keuangan_transaksi (tanggal_bayar, keterangan, jenis, jumlah) VALUES (?, ?, 'Pemasukan', ?)");
                $stmt2->execute([date('Y-m-d'), $ket . $t['nama_tagihan'], $jumlah_bayar]);

                // --- Kirim Notifikasi Personal ---
                require_once __DIR__ . '/../Services/OneSignalService.php';
                $rp_jumlah = 'Rp ' . number_format($jumlah_bayar, 0, ',', '.');
                $judul_notif = "Terima Kasih";
                $pesan_notif = $ket . $t['nama_tagihan'] . " sebesar " . $rp_jumlah . " telah kami terima. Status saat ini: " . $statusBaru;
                \App\Services\OneSignalService::sendNotification($judul_notif, $pesan_notif, null, $t['siswa_id']);
                // ---------------------------------

                redirect('/admin/finance/tagihan?msg=success');
                exit;
            }
        }
        redirect('/admin/finance/tagihan?msg=error');
        exit;
    }

    public static function updateTagihan() {
        self::guard();
        $db = Database::connect('core');
        $id = isset($_POST['tagihan_id']) ? intval($_POST['tagihan_id']) : 0;
        $nama_tagihan = isset($_POST['nama_tagihan']) ? trim($_POST['nama_tagihan']) : '';
        $jumlah_tagihan = isset($_POST['jumlah_tagihan']) ? floatval($_POST['jumlah_tagihan']) : 0;
        $jatuh_tempo = isset($_POST['jatuh_tempo']) && !empty($_POST['jatuh_tempo']) ? $_POST['jatuh_tempo'] : null;

        if ($id > 0 && !empty($nama_tagihan) && $jumlah_tagihan > 0) {
            $t = $db->query("SELECT * FROM keuangan_tagihan WHERE id = $id")->fetch();
            if ($t) {
                // Determine new status based on existing payments
                $paid = $db->query("SELECT SUM(jumlah) as total FROM keuangan_komite_pembayaran WHERE siswa_id = {$t['siswa_id']} AND jenis_pembayaran = '{$t['nama_tagihan']}'")->fetch();
                $totalBayar = $paid['total'] ? floatval($paid['total']) : 0;
                
                $statusBaru = 'Belum Bayar';
                if ($totalBayar > 0) {
                    $statusBaru = ($totalBayar >= $jumlah_tagihan) ? 'Lunas' : 'Menyicil';
                }

                $stmt = $db->prepare("UPDATE keuangan_tagihan SET nama_tagihan = ?, jumlah_tagihan = ?, jatuh_tempo = ?, status = ? WHERE id = ?");
                $stmt->execute([$nama_tagihan, $jumlah_tagihan, $jatuh_tempo, $statusBaru, $id]);

                // Also update the jenis_pembayaran string in keuangan_komite_pembayaran if nama_tagihan changed
                if ($nama_tagihan !== $t['nama_tagihan']) {
                    $db->prepare("UPDATE keuangan_komite_pembayaran SET jenis_pembayaran = ? WHERE siswa_id = ? AND jenis_pembayaran = ?")
                       ->execute([$nama_tagihan, $t['siswa_id'], $t['nama_tagihan']]);
                }

                redirect('/admin/finance/tagihan?msg=updated');
                exit;
            }
        }
        redirect('/admin/finance/tagihan?msg=error');
        exit;
    }

    public static function cetakKwitansi($id) {
        self::guard();
        $db = Database::connect('core');
        $id = intval($id);
        
        $p = $db->query("
            SELECT p.*, s.nama, s.nis, c.nama_kelas as kelas, t.status as status_tagihan
            FROM keuangan_komite_pembayaran p
            JOIN siswa s ON p.siswa_id = s.id
            LEFT JOIN kelas c ON s.kelas_id = c.id
            LEFT JOIN keuangan_tagihan t ON (p.siswa_id = t.siswa_id AND TRIM(p.jenis_pembayaran) = TRIM(t.nama_tagihan))
            WHERE p.id = $id
        ")->fetch();
        
        if (!$p) die("Data tidak ditemukan.");

        // Ambil daftar tagihan siswa untuk rekap
        $tagihanList = $db->query("
            SELECT t.*,
                   (SELECT SUM(jumlah) FROM keuangan_komite_pembayaran p WHERE p.siswa_id = t.siswa_id AND p.jenis_pembayaran = t.nama_tagihan) as terbayar
            FROM keuangan_tagihan t
            WHERE t.siswa_id = {$p['siswa_id']}
            ORDER BY t.status ASC, t.nama_tagihan ASC
        ")->fetchAll();

        $totalSisa = 0;
        foreach ($tagihanList as &$row) {
            $row['terbayar'] = $row['terbayar'] ? floatval($row['terbayar']) : 0;
            $row['sisa']     = floatval($row['jumlah_tagihan']) - $row['terbayar'];
            if ($row['sisa'] > 0) {
                $totalSisa += $row['sisa'];
            }
        }
        $sisa = $totalSisa;
        
        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();

        self::render('finance_kwitansi', [
            'p' => $p,
            'sisa' => $sisa,
            'tagihanList' => $tagihanList,
            'institusi' => $institusi,
            'db' => $db
        ]);
    }

    public static function verifikasiKwitansiPublic($id) {
        $salt = "M1ft4hulHud4_S3cur3";
        $expected_token = substr(md5($id . $salt), 0, 10);
        $provided_token = isset($_GET['token']) ? $_GET['token'] : '';

        if ($provided_token !== $expected_token) {
            die("<div style='font-family:sans-serif; text-align:center; padding: 50px;'><h2>Akses Ditolak</h2><p>Link verifikasi tidak valid atau telah dimanipulasi. Pastikan Anda melakukan scan langsung dari QR Code resmi.</p></div>");
        }

        $db = Database::connect('core');
        $id = intval($id);
        
        $p = $db->query("
            SELECT p.*, s.nama, s.nis, c.nama_kelas as kelas, t.status as status_tagihan
            FROM keuangan_komite_pembayaran p
            JOIN siswa s ON p.siswa_id = s.id
            LEFT JOIN kelas c ON s.kelas_id = c.id
            LEFT JOIN keuangan_tagihan t ON (p.siswa_id = t.siswa_id AND TRIM(p.jenis_pembayaran) = TRIM(t.nama_tagihan))
            WHERE p.id = $id
        ")->fetch();
        
        if (!$p) {
            die("<div style='font-family:sans-serif; text-align:center; padding: 50px;'><h2>Galat: Kwitansi Tidak Ditemukan</h2><p>Bukti pembayaran ini tidak valid atau tidak tercatat di sistem sekolah.</p></div>");
        }

        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();

        // Resolve Bendahara Dinamis dari Kategori
        $resolvedBendahara = '';
        $resolvedNip = '';
        try {
            $stmtKat = $db->prepare("SELECT kategori FROM keuangan_komite_jenis WHERE nama_tagihan = ? LIMIT 1");
            $stmtKat->execute([$p['jenis_pembayaran']]);
            $katRow = $stmtKat->fetch();
            if ($katRow) {
                $kategori = $katRow['kategori'];
                $stmtGuru = $db->prepare("
                    SELECT g.nama, g.nip 
                    FROM keuangan_komite_kategori kk
                    JOIN guru g ON kk.guru_id = g.id
                    WHERE kk.nama_kategori = ? LIMIT 1
                ");
                $stmtGuru->execute([$kategori]);
                $guruRow = $stmtGuru->fetch();
                if ($guruRow) {
                    $resolvedBendahara = $guruRow['nama'];
                    $resolvedNip = $guruRow['nip'] ?: '';
                }
            }
            if (empty($resolvedBendahara)) {
                $aksesQuery = $db->query("
                    SELECT a.akses_pembayaran, g.nama, g.nip 
                    FROM keuangan_akses a 
                    JOIN guru g ON a.guru_id = g.id
                ")->fetchAll();
                
                foreach ($aksesQuery as $ak) {
                    $arr = json_decode($ak['akses_pembayaran'], true) ?: [];
                    foreach ($arr as $item) {
                        if (strcasecmp(trim($item), trim($p['jenis_pembayaran'])) === 0) {
                            $resolvedBendahara = $ak['nama'];
                            $resolvedNip = $ak['nip'] ?: '';
                            break 2;
                        }
                    }
                }
            }
        } catch (\Exception $e) {}

        $adminNama = !empty($resolvedBendahara) ? $resolvedBendahara : 'Bendahara Madrasah';

        include __DIR__ . '/../../resources/views/admin/finance_kwitansi_verifikasi.php';
    }

    public static function cetakSuratTagihan($id) {
        self::guard();
        $db = Database::connect('core');
        $id = intval($id);

        $t = $db->query("
            SELECT t.*, s.nama, s.nis, c.nama_kelas as kelas,
                   (SELECT SUM(jumlah) FROM keuangan_komite_pembayaran p WHERE p.siswa_id = t.siswa_id AND p.jenis_pembayaran = t.nama_tagihan) as terbayar
            FROM keuangan_tagihan t
            JOIN siswa s ON t.siswa_id = s.id
            LEFT JOIN kelas c ON s.kelas_id = c.id
            WHERE t.id = $id
        ")->fetch();

        if (!$t) die("Data tidak ditemukan.");
        $terbayar = $t['terbayar'] ? floatval($t['terbayar']) : 0;
        $sisa = floatval($t['jumlah_tagihan']) - $terbayar;

        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();

        self::render('finance_surat_tagihan', ['t' => $t, 'terbayar' => $terbayar, 'sisa' => $sisa, 'institusi' => $institusi]);
    }

    public static function cetakSuratTagihanSiswa($siswa_id) {
        self::guard();
        $db = Database::connect('core');
        $siswa_id = intval($siswa_id);

        $siswa = $db->query("
            SELECT s.*, c.nama_kelas as kelas FROM siswa s
            LEFT JOIN kelas c ON s.kelas_id = c.id
            WHERE s.id = $siswa_id
        ")->fetch();
        if (!$siswa) die("Data siswa tidak ditemukan.");

        $tagihanList = $db->query("
            SELECT t.*,
                   (SELECT SUM(jumlah) FROM keuangan_komite_pembayaran p WHERE p.siswa_id = t.siswa_id AND p.jenis_pembayaran = t.nama_tagihan) as terbayar
            FROM keuangan_tagihan t
            WHERE t.siswa_id = $siswa_id
            ORDER BY t.status ASC, t.nama_tagihan ASC
        ")->fetchAll();

        $totalTagihan = 0; $totalTerbayar = 0; $totalSisa = 0;
        foreach ($tagihanList as &$row) {
            $row['terbayar'] = $row['terbayar'] ? floatval($row['terbayar']) : 0;
            $row['sisa']     = floatval($row['jumlah_tagihan']) - $row['terbayar'];
            $totalTagihan  += floatval($row['jumlah_tagihan']);
            $totalTerbayar += $row['terbayar'];
            $totalSisa     += $row['sisa'];
        }

        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();

        // Enkripsi siswa_id untuk link verifikasi QR Code
        $key = "M1ft4hulHud4_Tag1han_S3cur3_K3y";
        $method = "AES-128-ECB";
        $code = base64_encode(openssl_encrypt($siswa_id, $method, $key));
        $verif_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/verify-invoice?code=" . urlencode($code);
        $qr_api_url = "https://api.qrserver.com/v1/create-qr-code/?size=90x90&data=" . urlencode($verif_url);

        self::render('finance_surat_tagihan_siswa', [
            'siswa'        => $siswa,
            'tagihanList'  => $tagihanList,
            'totalTagihan' => $totalTagihan,
            'totalTerbayar'=> $totalTerbayar,
            'totalSisa'    => $totalSisa,
            'institusi'    => $institusi,
            'adminNama'    => $_SESSION['nama'] ?? 'Bendahara',
            'qr_api_url'   => $qr_api_url
        ]);
    }

    public static function verifikasiTagihanPublic() {
        $code = isset($_GET['code']) ? $_GET['code'] : '';
        $key = "M1ft4hulHud4_Tag1han_S3cur3_K3y";
        $method = "AES-128-ECB";
        $decrypted = openssl_decrypt(base64_decode($code), $method, $key);
        
        if (!$decrypted || !is_numeric($decrypted)) {
            die("<div style='font-family:sans-serif; text-align:center; padding: 50px;'><h2>Akses Ditolak</h2><p>Link verifikasi tidak valid atau telah dimanipulasi.</p></div>");
        }
        
        $siswa_id = intval($decrypted);
        $db = Database::connect('core');
        
        $siswa = $db->query("
            SELECT s.*, c.nama_kelas as kelas FROM siswa s
            LEFT JOIN kelas c ON s.kelas_id = c.id
            WHERE s.id = $siswa_id
        ")->fetch();
        
        if (!$siswa) {
            die("<div style='font-family:sans-serif; text-align:center; padding: 50px;'><h2>Galat: Siswa Tidak Ditemukan</h2><p>Surat tagihan ini tidak valid.</p></div>");
        }
        
        $tagihanList = $db->query("
            SELECT t.*,
                   (SELECT SUM(jumlah) FROM keuangan_komite_pembayaran p WHERE p.siswa_id = t.siswa_id AND p.jenis_pembayaran = t.nama_tagihan) as terbayar
            FROM keuangan_tagihan t
            WHERE t.siswa_id = $siswa_id
            ORDER BY t.status ASC, t.nama_tagihan ASC
        ")->fetchAll();
        
        $totalTagihan = 0; $totalTerbayar = 0; $totalSisa = 0;
        foreach ($tagihanList as &$row) {
            $row['terbayar'] = $row['terbayar'] ? floatval($row['terbayar']) : 0;
            $row['sisa']     = floatval($row['jumlah_tagihan']) - $row['terbayar'];
            $totalTagihan  += floatval($row['jumlah_tagihan']);
            $totalTerbayar += $row['terbayar'];
            $totalSisa     += $row['sisa'];
        }
        
        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();
        
        include __DIR__ . '/../../resources/views/admin/finance_tagihan_verifikasi.php';
    }

    public static function saveKomitePembayaran() {
        self::guard();
        $db = Database::connect('core');
        
        $kelas_id = isset($_POST['kelas_id']) ? intval($_POST['kelas_id']) : 0;
        $siswa_id = isset($_POST['siswa_id']) ? intval($_POST['siswa_id']) : 0;
        $jenis = isset($_POST['jenis_pembayaran']) ? $_POST['jenis_pembayaran'] : '';
        $jenis_periode_value = isset($_POST['jenis_periode_value']) ? $_POST['jenis_periode_value'] : '';
        $jumlah = isset($_POST['jumlah']) ? floatval($_POST['jumlah']) : 0;
        $tanggal_bayar = isset($_POST['tanggal_bayar']) ? $_POST['tanggal_bayar'] : date('Y-m-d');
        
        if ($jenis_periode_value === 'tahunan') {
            $periode = isset($_POST['periode_tahunan']) ? $_POST['periode_tahunan'] : '';
        } else {
            $periode = isset($_POST['periode_bulanan']) ? $_POST['periode_bulanan'] : '';
            if ($periode) {
                $periode = date('F Y', strtotime($periode . '-01'));
            }
        }

        if ($siswa_id > 0 && $jumlah > 0) {
            // VALIDASI: Otorisasi Kategori
            $allowed_cats = self::getAllowedCategories();
            if (!in_array('ALL', $allowed_cats)) {
                $checkCat = $db->prepare("SELECT kategori FROM keuangan_komite_jenis WHERE nama_tagihan = ?");
                $checkCat->execute([$jenis]);
                $catRow = $checkCat->fetch();
                if (!$catRow || !in_array($catRow['kategori'], $allowed_cats)) {
                    redirect('/admin/finance/komite/pembayaran-siswa?kelas_id=' . $kelas_id . '&msg=unauthorized_category');
                    exit;
                }
            }

            // VALIDASI: Pastikan tidak melebihi sisa tagihan
            $tagihanQ = $db->prepare("SELECT id, jumlah_tagihan FROM keuangan_tagihan WHERE siswa_id = ? AND nama_tagihan = ?");
            $tagihanQ->execute([$siswa_id, $jenis]);
            $tCheck = $tagihanQ->fetch();
            if ($tCheck) {
                $paidQ = $db->prepare("SELECT COALESCE(SUM(jumlah), 0) as total FROM keuangan_komite_pembayaran WHERE siswa_id = ? AND jenis_pembayaran = ?");
                $paidQ->execute([$siswa_id, $jenis]);
                $sudahBayar = floatval($paidQ->fetch()['total']);
                $sisa = floatval($tCheck['jumlah_tagihan']) - $sudahBayar;
                if ($jumlah > $sisa) {
                    redirect('/admin/finance/komite/pembayaran-siswa?kelas_id=' . $kelas_id . '&msg=overpaid&sisa=' . number_format($sisa, 0, '', ''));
                    exit;
                }
            }

            $petugas_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
            $stmt = $db->prepare("INSERT INTO keuangan_komite_pembayaran (siswa_id, jenis_pembayaran, periode, jumlah, tanggal_bayar, petugas_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$siswa_id, $jenis, $periode, $jumlah, $tanggal_bayar, $petugas_id]);
            $bayar_id = $db->lastInsertId();

            // SINKRONISASI CERDAS: Hitung total bayar vs total tagihan agar status (Lunas/Menyicil) akurat
            $tagihan = $db->prepare("SELECT id, jumlah_tagihan FROM keuangan_tagihan WHERE siswa_id = ? AND nama_tagihan = ?");
            $tagihan->execute([$siswa_id, $jenis]);
            $t = $tagihan->fetch();
            if ($t) {
                $paid = $db->prepare("SELECT SUM(jumlah) as total FROM keuangan_komite_pembayaran WHERE siswa_id = ? AND jenis_pembayaran = ?");
                $paid->execute([$siswa_id, $jenis]);
                $totalBayar = floatval($paid->fetch()['total']);
                $statusBaru = ($totalBayar >= floatval($t['jumlah_tagihan'])) ? 'Lunas' : 'Menyicil';
                $db->prepare("UPDATE keuangan_tagihan SET status = ? WHERE id = ?")->execute([$statusBaru, $t['id']]);
            }

            // Optional: insert into transaction as well
            $stmt2 = $db->prepare("INSERT INTO keuangan_transaksi (tanggal_bayar, keterangan, jenis, jumlah) VALUES (?, ?, 'Pemasukan', ?)");
            $stmt2->execute([$tanggal_bayar, "Pembayaran Kas ($jenis): Periode $periode", $jumlah]);
            
            // --- Kirim Notifikasi Personal ---
            require_once __DIR__ . '/../Services/OneSignalService.php';
            $rp_jumlah = 'Rp ' . number_format($jumlah, 0, ',', '.');
            $judul_notif = "Pembayaran Berhasil";
            $pesan_notif = "Terima kasih, pembayaran " . $jenis . " sebesar " . $rp_jumlah . " telah kami terima.";
            if (isset($statusBaru)) {
                $pesan_notif .= " Status saat ini: " . $statusBaru;
            }
            \App\Services\OneSignalService::sendNotification($judul_notif, $pesan_notif, null, $siswa_id);
            // ---------------------------------

            redirect('/admin/finance/komite/pembayaran-siswa?kelas_id=' . $kelas_id . '&msg=success&last_id=' . $bayar_id);
            exit;
        }
    }

    public static function deleteKomitePembayaran() {
        header('Content-Type: application/json');
        self::guard();
        $db = Database::connect('core');
        
        $role_id = $_SESSION['role_id'] ?? 0;
        $can_delete = in_array($role_id, [1, 99]);
        if (!$can_delete) {
            // Check if super admin
            $is_app_admin = $db->prepare("SELECT id FROM app_admins WHERE user_id = ? AND app_code = 'keuangan'");
            $is_app_admin->execute([$_SESSION['user_id']]);
            if ($is_app_admin->fetch()) {
                $can_delete = true;
            } else {
                // Check keuangan_akses
                if (isset($_SESSION['guru_id'])) {
                    $cekDel = $db->prepare("SELECT can_delete FROM keuangan_akses WHERE guru_id = ?");
                    $cekDel->execute([$_SESSION['guru_id']]);
                    $adm = $cekDel->fetch();
                    if ($adm && $adm['can_delete'] == 1) $can_delete = true;
                }
            }
        }
        
        if (!$can_delete) {
            echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki hak akses hapus!']);
            exit;
        }

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id > 0) {
            try {
                $db->beginTransaction();
                
                $stmt = $db->prepare("SELECT * FROM keuangan_komite_pembayaran WHERE id = ? FOR UPDATE");
                $stmt->execute([$id]);
                $p = $stmt->fetch();
                
                if (!$p) throw new Exception("Pembayaran tidak ditemukan.");
                
                $siswa_id = $p['siswa_id'];
                $jenis = $p['jenis_pembayaran'];
                
                $db->prepare("DELETE FROM keuangan_komite_pembayaran WHERE id = ?")->execute([$id]);
                
                $tagihan = $db->prepare("SELECT id, jumlah_tagihan FROM keuangan_tagihan WHERE siswa_id = ? AND nama_tagihan = ?");
                $tagihan->execute([$siswa_id, $jenis]);
                $t = $tagihan->fetch();
                if ($t) {
                    $paid = $db->prepare("SELECT COALESCE(SUM(jumlah), 0) as total FROM keuangan_komite_pembayaran WHERE siswa_id = ? AND jenis_pembayaran = ?");
                    $paid->execute([$siswa_id, $jenis]);
                    $totalBayar = floatval($paid->fetch()['total']);
                    
                    $statusBaru = 'Belum Bayar';
                    if ($totalBayar > 0) {
                        $statusBaru = ($totalBayar >= floatval($t['jumlah_tagihan'])) ? 'Lunas' : 'Menyicil';
                    }
                    $db->prepare("UPDATE keuangan_tagihan SET status = ? WHERE id = ?")->execute([$statusBaru, $t['id']]);
                }
                
                $keterangan = "Pembayaran Kas ({$p['jenis_pembayaran']}): Periode {$p['periode']}";
                $db->prepare("DELETE FROM keuangan_transaksi WHERE tanggal_bayar = ? AND jenis = 'Pemasukan' AND jumlah = ? AND keterangan = ? LIMIT 1")
                   ->execute([$p['tanggal_bayar'], $p['jumlah'], $keterangan]);
                
                $db->commit();
                echo json_encode(['success' => true, 'message' => 'Pembayaran berhasil dihapus.']);
            } catch (\Exception $e) {
                $db->rollBack();
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ID tidak valid.']);
        }
        exit;
    }

    public static function saveKomiteJenisTagihan() {
        self::guard();
        $db = Database::connect('core');
        $id = isset($_POST['jenis_id']) ? intval($_POST['jenis_id']) : 0;
        $nama_tagihan = isset($_POST['nama_tagihan']) ? $_POST['nama_tagihan'] : '';
        $jenis_periode = isset($_POST['jenis_periode']) ? $_POST['jenis_periode'] : 'Bulanan';
        $nominal = isset($_POST['nominal']) ? floatval($_POST['nominal']) : 0;
        $sifat = isset($_POST['sifat']) ? $_POST['sifat'] : 'Wajib';
        $target_tipe = isset($_POST['target_tipe']) ? $_POST['target_tipe'] : 'Semua';
        $tahun_ajaran_id = isset($_POST['tahun_ajaran_id']) ? intval($_POST['tahun_ajaran_id']) : null;
        $kategori = isset($_POST['kategori']) && !empty($_POST['kategori']) ? $_POST['kategori'] : 'Lainnya';
        
        $target_kelas_id = null;
        if(($target_tipe == 'Kelas') && isset($_POST['target_kelas_ids'])) {
            $target_kelas_id = implode(',', $_POST['target_kelas_ids']);
        }
        
        $target_siswa_id = null;
        // Accept 'Siswa' or 'Individu' since our target logic can handle both formats if implemented with comma-separated IDs
        if(($target_tipe == 'Siswa' || $target_tipe == 'Individu') && isset($_POST['target_siswa_ids'])) {
            $target_siswa_id = implode(',', $_POST['target_siswa_ids']);
        }

        if (!empty($nama_tagihan) && $nominal >= 0 && $tahun_ajaran_id) {
            if ($id > 0) {
                // Get existing data so we don't wipe out targets
                $existing = $db->query("SELECT nama_tagihan, target_tipe, target_kelas_id, target_siswa_id FROM keuangan_komite_jenis WHERE id = $id")->fetch();
                if ($existing) {
                    if ($target_tipe == $existing['target_tipe']) {
                        if (empty($target_kelas_id)) $target_kelas_id = $existing['target_kelas_id'];
                        if (empty($target_siswa_id)) $target_siswa_id = $existing['target_siswa_id'];
                    }
                    
                    // Jika nama tagihan berubah, update juga di tabel riwayat dan tagihan
                    $old_name = trim($existing['nama_tagihan']);
                    $new_name = trim($nama_tagihan);
                    if ($old_name !== $new_name) {
                        // 1. Update nama_tagihan di keuangan_tagihan
                        $db->prepare("UPDATE keuangan_tagihan SET nama_tagihan = ? WHERE TRIM(nama_tagihan) = ? AND tahun_ajaran_id = ?")->execute([$new_name, $old_name, $tahun_ajaran_id]);
                        
                        // 2. Update jenis_pembayaran di riwayat
                        $db->prepare("UPDATE keuangan_komite_pembayaran SET jenis_pembayaran = ? WHERE TRIM(jenis_pembayaran) = ?")->execute([$new_name, $old_name]);
                        
                        // 3. Update akses_pembayaran bendahara
                        $all_akses = $db->query("SELECT id, akses_pembayaran FROM keuangan_akses")->fetchAll();
                        foreach ($all_akses as $ak) {
                            $akses_arr = json_decode($ak['akses_pembayaran'], true) ?: [];
                            $trimmed_arr = array_map('trim', $akses_arr);
                            
                            $idx = array_search($old_name, $trimmed_arr);
                            if ($idx !== false) {
                                $akses_arr[$idx] = $new_name;
                                $db->prepare("UPDATE keuangan_akses SET akses_pembayaran = ? WHERE id = ?")->execute([json_encode(array_values($akses_arr)), $ak['id']]);
                            }
                        }
                    }
                }
                
                $stmt = $db->prepare("UPDATE keuangan_komite_jenis SET nama_tagihan = ?, jenis_periode = ?, nominal = ?, sifat = ?, target_tipe = ?, target_kelas_id = ?, target_siswa_id = ?, tahun_ajaran_id = ?, kategori = ? WHERE id = ?");
                $stmt->execute([$nama_tagihan, $jenis_periode, $nominal, $sifat, $target_tipe, $target_kelas_id, $target_siswa_id, $tahun_ajaran_id, $kategori, $id]);
                $saved_id = $id;
            } else {
                $stmt = $db->prepare("INSERT INTO keuangan_komite_jenis (nama_tagihan, jenis_periode, nominal, sifat, target_tipe, target_kelas_id, target_siswa_id, tahun_ajaran_id, kategori) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nama_tagihan, $jenis_periode, $nominal, $sifat, $target_tipe, $target_kelas_id, $target_siswa_id, $tahun_ajaran_id, $kategori]);
                $saved_id = $db->lastInsertId();
            }
            
            // Auto Sync setelah menyimpan Master Tagihan
            self::syncTagihanMaster($saved_id);
            self::syncAllAksesPembayaran();
        }
        redirect('/admin/finance/komite/jenis-tagihan?msg=saved');
        exit;
    }

    public static function deleteKomiteJenisTagihan($id) {
        self::guard();
        if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            redirect('/admin/finance/komite/jenis-tagihan?msg=forbidden');
            exit;
        }

        $db = Database::connect('core');
        
        // Ambil nama tagihan dan tahun_ajaran_id sebelum dihapus
        $jenis = $db->prepare("SELECT nama_tagihan, tahun_ajaran_id FROM keuangan_komite_jenis WHERE id = ?");
        $jenis->execute([$id]);
        $row = $jenis->fetch();
        
        $stmt = $db->prepare("DELETE FROM keuangan_komite_jenis WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($row) {
            // Cek apakah masih ada rule lain dengan nama dan tahun ajaran yang sama
            $check = $db->prepare("SELECT count(*) as cnt FROM keuangan_komite_jenis WHERE TRIM(nama_tagihan) = ? AND tahun_ajaran_id = ?");
            $check->execute([trim($row['nama_tagihan']), $row['tahun_ajaran_id']]);
            
            if ($check->fetch()['cnt'] == 0) {
                // Jika ini adalah rule TERAKHIR untuk nama tagihan tersebut, hapus bersih semua tagihannya!
                $db->prepare("DELETE FROM keuangan_tagihan WHERE TRIM(nama_tagihan) = ? AND tahun_ajaran_id = ?")->execute([trim($row['nama_tagihan']), $row['tahun_ajaran_id']]);
            }
        }
        
        // Biarkan sinkronisasi master yang membersihkan orphans secara cerdas
        self::syncTagihanMaster();
        self::syncAllAksesPembayaran();
        
        redirect('/admin/finance/komite/jenis-tagihan?msg=deleted');
        exit;
    }
    public static function saveBosTransaksi() {
        self::guard();
        $db = Database::connect('core');
        $jenis = isset($_POST['jenis']) ? $_POST['jenis'] : 'Pemasukan';
        $tanggal = isset($_POST['tanggal_transaksi']) ? $_POST['tanggal_transaksi'] : date('Y-m-d');
        $keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : '';
        $kategori = isset($_POST['kategori']) ? $_POST['kategori'] : '';
        $jumlah = isset($_POST['jumlah']) ? floatval($_POST['jumlah']) : 0;
        
        if ($jumlah > 0 && !empty($keterangan)) {
            $stmt = $db->prepare("INSERT INTO keuangan_bos_transaksi (tanggal_transaksi, keterangan, jenis, kategori, jumlah) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$tanggal, $keterangan, $jenis, $kategori, $jumlah]);
        }
        $r = $jenis == 'Pemasukan' ? 'pemasukan' : 'pengeluaran';
        redirect('/admin/finance/bos/' . $r . '?msg=saved');
        exit;
    }

    public static function deleteBosTransaksi($id) {
        self::guard();
        $db = Database::connect('core');
        $t = $db->query("SELECT jenis FROM keuangan_bos_transaksi WHERE id = " . intval($id))->fetch();
        if ($t) {
            $db->prepare("DELETE FROM keuangan_bos_transaksi WHERE id = ?")->execute([$id]);
            $r = $t['jenis'] == 'Pemasukan' ? 'pemasukan' : 'pengeluaran';
            redirect('/admin/finance/bos/' . $r . '?msg=deleted');
        } else {
            redirect('/admin/finance/bos/pemasukan');
        }
        exit;
    }

    public static function saveKomiteTransaksi() {
        self::guard();
        $db = Database::connect('core');
        $jenis = isset($_POST['jenis']) ? $_POST['jenis'] : 'Pemasukan';
        $tanggal = isset($_POST['tanggal_transaksi']) ? $_POST['tanggal_transaksi'] : date('Y-m-d');
        $keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : '';
        $kategori = isset($_POST['kategori']) ? $_POST['kategori'] : '';
        $jumlah = isset($_POST['jumlah']) ? floatval($_POST['jumlah']) : 0;
        
        $sumber_dana = isset($_POST['sumber_dana']) ? trim($_POST['sumber_dana']) : '';
        if (!empty($sumber_dana)) {
            $keterangan = "[" . $sumber_dana . "] " . $keterangan;
        }

        $allowed_cats = self::getAllowedCategories();
        if (!in_array('ALL', $allowed_cats)) {
            if (!in_array($kategori, $allowed_cats)) {
                $r = $jenis == 'Pemasukan' ? 'pemasukan' : 'pengeluaran';
                redirect('/admin/finance/komite/' . $r . '?msg=unauthorized_category');
                exit;
            }
        }
        
        if ($jumlah > 0 && !empty($keterangan)) {
            $petugas_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
            $stmt = $db->prepare("INSERT INTO keuangan_komite_transaksi (tanggal_transaksi, keterangan, jenis, kategori, jumlah, petugas_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$tanggal, $keterangan, $jenis, $kategori, $jumlah, $petugas_id]);
        }
        $r = $jenis == 'Pemasukan' ? 'pemasukan' : 'pengeluaran';
        redirect('/admin/finance/komite/' . $r . '?msg=saved');
        exit;
    }

    public static function deleteKomiteTransaksi($id) {
        self::guard();
        $db = Database::connect('core');
        $t = $db->query("SELECT jenis, kategori FROM keuangan_komite_transaksi WHERE id = " . intval($id))->fetch();
        if ($t) {
            $allowed_cats = self::getAllowedCategories();
            if (!in_array('ALL', $allowed_cats)) {
                if (!in_array($t['kategori'], $allowed_cats)) {
                    $r = $t['jenis'] == 'Pemasukan' ? 'pemasukan' : 'pengeluaran';
                    redirect('/admin/finance/komite/' . $r . '?msg=unauthorized_category');
                    exit;
                }
            }
            $db->prepare("DELETE FROM keuangan_komite_transaksi WHERE id = ?")->execute([$id]);
            $r = $t['jenis'] == 'Pemasukan' ? 'pemasukan' : 'pengeluaran';
            redirect('/admin/finance/komite/' . $r . '?msg=deleted');
        } else {
            redirect('/admin/finance/komite/pemasukan');
        }
        exit;
    }

    public static function syncAllAksesPembayaran() {
        $db = Database::connect('core');
        
        // 1. Ambil semua kategori dan guru_id (penanggung jawab)
        $kategoriGurus = $db->query("SELECT nama_kategori, guru_id FROM keuangan_komite_kategori WHERE guru_id IS NOT NULL")->fetchAll();
        
        // Map guru_id => list of categories they manage
        $guruCats = [];
        foreach ($kategoriGurus as $kg) {
            $guruCats[$kg['guru_id']][] = $kg['nama_kategori'];
        }
        
        // Hapus guru dari keuangan_akses jika mereka TIDAK LAGI menjadi penanggung jawab kategori apapun
        if (empty($guruCats)) {
            $db->query("DELETE FROM keuangan_akses");
        } else {
            $valid_guru_ids = array_keys($guruCats);
            $placeholders = implode(',', array_fill(0, count($valid_guru_ids), '?'));
            $db->prepare("DELETE FROM keuangan_akses WHERE guru_id NOT IN ($placeholders)")->execute($valid_guru_ids);
        }
        
        // Tambahkan guru penanggung jawab yang belum terdaftar di keuangan_akses
        foreach ($guruCats as $guru_id => $cats) {
            $cek = $db->prepare("SELECT id FROM keuangan_akses WHERE guru_id = ?");
            $cek->execute([$guru_id]);
            if (!$cek->fetch()) {
                // Beri akses menu default agar mereka bisa masuk aplikasi keuangan
                $akses_menu = json_encode(["overview", "tagihan", "komite_pemasukan", "komite_pengeluaran", "komite_pembayaran_siswa", "komite_laporan", "komite_kategori", "komite_jenis"]);
                $db->prepare("INSERT INTO keuangan_akses (guru_id, akses_pembayaran, akses_menu) VALUES (?, '[]', ?)")->execute([$guru_id, $akses_menu]);
            }
        }
        
        // 2. Ambil semua jenis tagihan
        $jenisList = $db->query("SELECT nama_tagihan, kategori FROM keuangan_komite_jenis")->fetchAll();
        
        // Map guru_id => list of auto tagihans
        $guruAutoTagihans = [];
        foreach ($jenisList as $j) {
            $kat = $j['kategori'];
            foreach ($guruCats as $guru_id => $cats) {
                if (in_array($kat, $cats)) {
                    $guruAutoTagihans[$guru_id][] = $j['nama_tagihan'];
                }
            }
        }
        
        // 3. Update keuangan_akses for each guru
        $allAkses = $db->query("SELECT id, guru_id, akses_pembayaran FROM keuangan_akses")->fetchAll();
        foreach ($allAkses as $ak) {
            $guru_id = $ak['guru_id'];
            $current_pemb = json_decode($ak['akses_pembayaran'], true) ?: [];
            
            // Auto tagihans for this guru
            $auto = isset($guruAutoTagihans[$guru_id]) ? $guruAutoTagihans[$guru_id] : [];
            
            // Gabungkan, lalu filter out tagihans that belong to categories managed by OTHER gurus
            $new_pemb = array_unique(array_merge($current_pemb, $auto));
            $filtered_pemb = [];
            foreach ($new_pemb as $t_name) {
                $t_kat = null;
                foreach ($jenisList as $jl) {
                    if (trim($jl['nama_tagihan']) === trim($t_name)) {
                        $t_kat = $jl['kategori'];
                        break;
                    }
                }
                
                if ($t_kat) {
                    // Cari siapa penanggung jawab kategori ini
                    $owner_guru_id = null;
                    foreach ($kategoriGurus as $kg) {
                        if ($kg['nama_kategori'] === $t_kat) {
                            $owner_guru_id = $kg['guru_id'];
                            break;
                        }
                    }
                    
                    if ($owner_guru_id && $owner_guru_id != $guru_id) {
                        // Kategori ini di-manage oleh guru lain, jadi guru ini tidak boleh punya akses
                        continue;
                    }
                }
                $filtered_pemb[] = $t_name;
            }
            
            $db->prepare("UPDATE keuangan_akses SET akses_pembayaran = ? WHERE id = ?")->execute([json_encode(array_values($filtered_pemb)), $ak['id']]);
        }
    }
}
