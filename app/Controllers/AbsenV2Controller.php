<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Branding;

class AbsenV2Controller {
    
    public static function dashboard() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            redirect('/login');
            exit;
        }

        $nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Administrator';
        $initial = strtoupper(substr($nama, 0, 2));
        $title = "Dashboard Absensi V2";

        // Ambil data untuk statistik
        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');
        
        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

        $sql_siswa = "SELECT COUNT(DISTINCT s.id) FROM siswa s LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id AND rks.tahun_ajaran_id IN (SELECT id FROM tahun_ajaran WHERE name = '$active_year_name') WHERE s.status IN ('Aktif', 'Alumni') AND (rks.id IS NOT NULL OR s.tahun_ajaran = '$active_year_name')";
        $totalSiswa = $db_core->query($sql_siswa)->fetchColumn();
        
        $totalGuru = $db_core->query("SELECT COUNT(*) FROM guru")->fetchColumn();
        
        // Data Absensi Siswa Hari Ini
        $today = date('Y-m-d');
        $absenStats = $db_siakad->query("SELECT status, COUNT(*) as count FROM absensi_siswa WHERE tanggal = '$today' GROUP BY status")->fetchAll(\PDO::FETCH_KEY_PAIR);
        
        $hadir = isset($absenStats['Hadir']) ? $absenStats['Hadir'] : 0;
        $sakit = isset($absenStats['Sakit']) ? $absenStats['Sakit'] : 0;
        $izin = isset($absenStats['Izin']) ? $absenStats['Izin'] : 0;
        $alpa = isset($absenStats['Alpa']) ? $absenStats['Alpa'] : 0;
        $bolos = isset($absenStats['Bolos']) ? $absenStats['Bolos'] : 0;
        $totalAbsen = $hadir + $sakit + $izin + $alpa + $bolos;

        // Data Absensi Guru Hari Ini
        $absenGuruStats = $db_siakad->query("SELECT status, COUNT(*) as count FROM absensi_guru WHERE tanggal = '$today' GROUP BY status")->fetchAll(\PDO::FETCH_KEY_PAIR);
        $hadir_guru = isset($absenGuruStats['Hadir']) ? $absenGuruStats['Hadir'] : 0;
        $sakit_guru = isset($absenGuruStats['Sakit']) ? $absenGuruStats['Sakit'] : 0;
        $izin_guru = isset($absenGuruStats['Izin']) ? $absenGuruStats['Izin'] : 0;
        $alpa_guru = isset($absenGuruStats['Alpa']) ? $absenGuruStats['Alpa'] : 0;
        $bolos_guru = isset($absenGuruStats['Bolos']) ? $absenGuruStats['Bolos'] : 0;
        $totalAbsen_guru = $hadir_guru + $sakit_guru + $izin_guru + $alpa_guru + $bolos_guru;
        
        // Dummy Activities (bisa disesuaikan nanti dengan log absen real-time)
        $activities = [
            ['icon' => 'check-circle', 'text' => 'Sistem Absensi Aktif', 'sub' => 'V2 Engine berjalan dengan baik', 'time' => date('Y-m-d H:i:s')],
        ];

        ob_start();
        include __DIR__ . '/../../resources/views/admin/absen_v2/dashboard.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public static function scanner() {
        if (!isset($_SESSION['user_id'])) { redirect('/login'); exit; }
        
        if (!in_array($_SESSION['role_id'], [1, 99])) {
            // Check if they are a scanner admin
            $is_scanner = false;
            if (isset($_SESSION['guru_id'])) {
                $db_core = Database::connect('core');
                $cek = $db_core->prepare("SELECT id FROM absen_akses_scanner WHERE guru_id = ?");
                $cek->execute([$_SESSION['guru_id']]);
                if ($cek->fetch()) $is_scanner = true;
            }
            if (!$is_scanner) { redirect('/login'); exit; }
        }
        
        $title = "Scanner Absensi QR V2";
        include __DIR__ . '/../../resources/views/admin/absen_v2/scanner.php';
    }

    public static function processScan() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
            exit;
        }

        if (!in_array($_SESSION['role_id'], [1, 99])) {
            $is_scanner = false;
            if (isset($_SESSION['guru_id'])) {
                $db_core = Database::connect('core');
                $cek = $db_core->prepare("SELECT id FROM absen_akses_scanner WHERE guru_id = ? AND akses_siswa = 1");
                $cek->execute([$_SESSION['guru_id']]);
                if ($cek->fetch()) $is_scanner = true;
            }
            if (!$is_scanner) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
                exit;
            }
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $raw_qr_text = isset($input['nis']) ? trim($input['nis']) : '';
        $nis = $raw_qr_text;

        // Support untuk QR berisi NIS:xxxx atau USERNAME:xxxx
        if (preg_match('/NIS(?:\/NISN)?:\s*([a-zA-Z0-9\-\_]+)/i', $raw_qr_text, $matches)) {
            $nis = trim($matches[1]);
        }
        
        $username = '';
        if (preg_match('/USERNAME:\s*([a-zA-Z0-9\-\_]+)/i', $raw_qr_text, $matches)) {
            $username = trim($matches[1]);
        }
        
        // Debugging: Log incoming data
        file_put_contents(__DIR__ . '/../../scanner_debug.log', "[" . date('Y-m-d H:i:s') . "] Raw Input: " . $raw_qr_text . "\nNIS Extracted: " . $nis . "\nUsername Extracted: " . $username . "\n", FILE_APPEND);

        if (empty($nis) && empty($username)) {
            $msg = 'QR Code tidak terbaca atau format tidak dikenali.';
            file_put_contents(__DIR__ . '/../../scanner_debug.log', "[" . date('Y-m-d H:i:s') . "] Failed: " . $msg . "\n", FILE_APPEND);
            echo json_encode(['success' => false, 'message' => $msg]);
            exit;
        }

        try {
            $db_core = Database::connect('core');
            $db_siakad = Database::connect('siakad');
            
            $siswa = false;

            // 1. Cari Siswa berdasarkan Username (Format Baru Cetak ID Card)
            if (!empty($username)) {
                $stmt_user = $db_core->prepare("SELECT id FROM users WHERE username = ?");
                $stmt_user->execute([$username]);
                $user = $stmt_user->fetch(\PDO::FETCH_ASSOC);
                
                if ($user) {
                    // FIX: added user_id
                    $stmt_siswa = $db_core->prepare("SELECT id, nis, nama, kelas_id, user_id FROM siswa WHERE user_id = ?");
                    $stmt_siswa->execute([$user['id']]);
                    $siswa = $stmt_siswa->fetch(\PDO::FETCH_ASSOC);
                }
            }

            // 2. Jika tidak ketemu, cari berdasarkan NIS (Format Lama)
            if (!$siswa && !empty($nis)) {
                // FIX: added user_id
                $stmt = $db_core->prepare("SELECT id, nis, nama, kelas_id, user_id FROM siswa WHERE nis = ?");
                $stmt->execute([$nis]);
                $siswa = $stmt->fetch(\PDO::FETCH_ASSOC);

                if (!$siswa) {
                    $jsonTest = json_decode($nis, true);
                    if(is_array($jsonTest) && isset($jsonTest['nis'])) {
                        $nis_json = trim($jsonTest['nis']);
                        $stmt->execute([$nis_json]);
                        $siswa = $stmt->fetch(\PDO::FETCH_ASSOC);
                    }
                }
            }

            if (!$siswa) {
                $msg = 'Data siswa tidak ditemukan di database.';
                file_put_contents(__DIR__ . '/../../scanner_debug.log', "[" . date('Y-m-d H:i:s') . "] Failed: " . $msg . "\n", FILE_APPEND);
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }

            // Cari nama kelas untuk feedback (Optional)
            $nama_kelas = "-";
            if ($siswa['kelas_id']) {
                $stmtKelas = $db_core->prepare("SELECT nama_kelas FROM kelas WHERE id = ?");
                $stmtKelas->execute([$siswa['kelas_id']]);
                $kelasInfo = $stmtKelas->fetch();
                if ($kelasInfo) $nama_kelas = $kelasInfo['nama_kelas'];
            }
            
            $today = date('Y-m-d');
            
            // Cek Kalender Akademik (Libur & Pulang Awal)
            $cekAgenda = $db_siakad->query("SELECT tipe, keterangan, jam_mulai_kosong FROM kalender_akademik WHERE tanggal_mulai <= '$today' AND tanggal_selesai >= '$today' AND (tipe = 'libur' OR tipe = 'pulang_awal') LIMIT 1")->fetch();
            if ($cekAgenda && $cekAgenda['tipe'] == 'libur') {
                $msg = 'Hari ini Libur: ' . $cekAgenda['keterangan'] . '. Tidak perlu scan absensi.';
                file_put_contents(__DIR__ . '/../../scanner_debug.log', "[" . date('Y-m-d H:i:s') . "] Failed: " . $msg . "\n", FILE_APPEND);
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }

            // 2. Tentukan Mode Masuk atau Pulang
            $jam_sekarang = date('H:i:s');
            
            // Ambil pengaturan jam
            $stmtJam = $db_core->prepare("SELECT kunci, nilai FROM absensi_pengaturan");
            $stmtJam->execute();
            $pengaturan = $stmtJam->fetchAll(\PDO::FETCH_KEY_PAIR);
            
            $batas_terlambat = isset($pengaturan['batas_terlambat']) ? $pengaturan['batas_terlambat'] : '07:15:00';
            $jam_pulang_biasa = isset($pengaturan['jam_pulang']) ? $pengaturan['jam_pulang'] : '14:00:00';
            $jam_pulang_jumat = isset($pengaturan['jam_pulang_jumat']) ? $pengaturan['jam_pulang_jumat'] : '11:00:00';
            $jam_pulang_sabtu = isset($pengaturan['jam_pulang_sabtu']) ? $pengaturan['jam_pulang_sabtu'] : '12:00:00';
            $jam_sholat_mulai = isset($pengaturan['jam_sholat_mulai']) ? $pengaturan['jam_sholat_mulai'] : '11:30:00';
            $jam_sholat_akhir = isset($pengaturan['jam_sholat_akhir']) ? $pengaturan['jam_sholat_akhir'] : '13:30:00';
            
            // Cek apakah hari ini Jumat atau Sabtu
            $is_jumat = (date('w') == 5);
            $is_sabtu = (date('w') == 6);
            
            if ($is_jumat) {
                $jam_pulang_mulai = $jam_pulang_jumat;
            } elseif ($is_sabtu) {
                $jam_pulang_mulai = $jam_pulang_sabtu;
            } else {
                $jam_pulang_mulai = $jam_pulang_biasa;
            }
            
            if (isset($cekAgenda) && $cekAgenda && $cekAgenda['tipe'] == 'pulang_awal' && !empty($cekAgenda['jam_mulai_kosong'])) {
                $jam_pulang_mulai = $cekAgenda['jam_mulai_kosong'];
            }
            
            if ($jam_sekarang >= $jam_pulang_mulai) {
                $mode = 'pulang';
            } elseif ($jam_sekarang >= $jam_sholat_mulai && $jam_sekarang <= $jam_sholat_akhir) {
                // FIX: hari sabtu jadikan pulang
                $mode = $is_sabtu ? 'pulang' : 'sholat';
            } else {
                $mode = 'masuk';
            }
            
            $status_kehadiran = 'Hadir'; // Default jika masuk pagi sebelum batas
            if ($mode === 'masuk' && $jam_sekarang > $batas_terlambat) {
                $status_kehadiran = 'Terlambat';
            }
            
            $siswa_id = $siswa['id'];
            $kelas_id = $siswa['kelas_id'] ?: 0;
            
            $pesan_notif = "";
            $judul_notif = "Info Kehadiran";
            $pesan_sukses = "";
            
            if ($mode === 'sholat') {
                $stmtCekSholat = $db_core->prepare("SELECT id FROM absensi_sholat WHERE tanggal = ? AND siswa_id = ?");
                $stmtCekSholat->execute([$today, $siswa_id]);
                
                if ($stmtCekSholat->fetch()) {
                    echo json_encode(['success' => true, 'message' => 'Sudah absen sholat sebelumnya.', 'data' => ['nama' => $siswa['nama'], 'kelas' => $nama_kelas, 'status' => 'Sudah Sholat']]);
                    exit;
                }

                $stmtInsertSholat = $db_core->prepare("INSERT INTO absensi_sholat (siswa_id, tanggal, jam_scan, status) VALUES (?, ?, ?, 'Hadir')");
                $stmtInsertSholat->execute([$siswa_id, $today, $jam_sekarang]);

                echo json_encode(['success' => true, 'message' => 'Berhasil absen sholat!', 'data' => ['nama' => $siswa['nama'], 'kelas' => $nama_kelas, 'status' => 'Hadir Sholat']]);
                exit; // Langsung keluar karena tidak perlu update presensi masuk/pulang
            }
            
            // Cek data presensi hari ini
            $stmtPresensiCek = $db_core->prepare("SELECT id, jam_masuk, jam_pulang FROM presensi WHERE user_id = ? AND tanggal = ? AND role_id = 3");
            $stmtPresensiCek->execute([$siswa['user_id'], $today]);
            $presensi = $stmtPresensiCek->fetch();
            
            $pesan_notif = "";
            $judul_notif = "Info Kehadiran";
            $pesan_sukses = "";
            
            if ($mode === 'masuk') {
                if ($presensi && $presensi['jam_masuk']) {
                    echo json_encode(['success' => true, 'message' => 'Sudah absen masuk sebelumnya.', 'data' => ['nama' => $siswa['nama'], 'kelas' => $nama_kelas, 'status' => 'Sudah Masuk']]);
                    exit;
                }
                
                if ($presensi) {
                    $stmtUpdatePresensi = $db_core->prepare("UPDATE presensi SET jam_masuk = ?, status = ? WHERE id = ?");
                    $stmtUpdatePresensi->execute([$jam_sekarang, $status_kehadiran, $presensi['id']]);
                } else {
                    $stmtInsertPresensi = $db_core->prepare("INSERT INTO presensi (user_id, role_id, tanggal, jam_masuk, status, tahun_ajaran_id, semester) VALUES (?, 3, ?, ?, ?, 1, 1)");
                    $stmtInsertPresensi->execute([$siswa['user_id'], $today, $jam_sekarang, $status_kehadiran]);
                }
                
                $pesan_sukses = 'Berhasil absen masuk.';
                $pesan_notif = "Siswa " . $siswa['nama'] . " telah Hadir di sekolah pada pukul " . date('H:i');
                if ($status_kehadiran == 'Terlambat') {
                    $pesan_notif = "Siswa " . $siswa['nama'] . " tiba TERLAMBAT di sekolah pada pukul " . date('H:i');
                }
                
                // Update tabel pusat absensi_siswa menjadi Hadir/Terlambat
                $stmtCek = $db_siakad->prepare("SELECT id FROM absensi_siswa WHERE tanggal = ? AND siswa_id = ?");
                $stmtCek->execute([$today, $siswa_id]);
                if (!$stmtCek->fetch()) {
                    $stmtInsertPusat = $db_siakad->prepare("INSERT INTO absensi_siswa (tanggal, siswa_id, kelas_id, status, created_at) VALUES (?, ?, ?, ?, NOW())");
                    $stmtInsertPusat->execute([$today, $siswa_id, $kelas_id, $status_kehadiran]);
                }
                
            } else {
                // Mode Pulang
                if ($presensi && $presensi['jam_pulang']) {
                    echo json_encode(['success' => true, 'message' => 'Sudah absen pulang sebelumnya.', 'data' => ['nama' => $siswa['nama'], 'kelas' => $nama_kelas, 'status' => 'Sudah Pulang']]);
                    exit;
                }
                
                if ($presensi) {
                    $stmtUpdatePresensi = $db_core->prepare("UPDATE presensi SET jam_pulang = ? WHERE id = ?");
                    $stmtUpdatePresensi->execute([$jam_sekarang, $presensi['id']]);
                } else {
                    $stmtInsertPresensi = $db_core->prepare("INSERT INTO presensi (user_id, role_id, tanggal, jam_pulang, status, tahun_ajaran_id, semester) VALUES (?, 3, ?, ?, 'Hadir', 1, 1)");
                    $stmtInsertPresensi->execute([$siswa['user_id'], $today, $jam_sekarang]);
                }
                
                $pesan_sukses = 'Berhasil absen pulang.';
                $pesan_notif = "Siswa " . $siswa['nama'] . " telah Pulang dari sekolah pada pukul " . date('H:i');
                $judul_notif = "Info Kepulangan";
            }
            
            // --- Kirim Notifikasi Personal ---
            require_once __DIR__ . '/../Services/OneSignalService.php';
            try {
                \App\Services\OneSignalService::sendNotification($judul_notif, $pesan_notif, null, $siswa['user_id']);
            } catch (\Exception $e) {}
            // ---------------------------------

            echo json_encode([
                'success' => true, 
                'message' => $pesan_sukses,
                'data' => [
                    'nama' => $siswa['nama'],
                    'kelas' => $nama_kelas,
                    'status' => strtoupper($mode)
                ]
            ]);
            file_put_contents(__DIR__ . '/../../scanner_debug.log', "[" . date('Y-m-d H:i:s') . "] Success for NIS " . $nis . "\n", FILE_APPEND);

        } catch (\PDOException $e) {
            $msg = 'Database Error: ' . $e->getMessage();
            file_put_contents(__DIR__ . '/../../scanner_debug.log', "[" . date('Y-m-d H:i:s') . "] Failed Exception: " . $msg . "\n", FILE_APPEND);
            echo json_encode(['success' => false, 'message' => $msg]);
        }
    }

    public static function scannerSholat() {
        if (!isset($_SESSION['user_id'])) { redirect('/login'); exit; }
        
        if (!in_array($_SESSION['role_id'], [1, 2, 99])) {
            $is_scanner = false;
            if (isset($_SESSION['guru_id'])) {
                $db_core = Database::connect('core');
                $cek = $db_core->prepare("SELECT id FROM absen_akses_scanner WHERE guru_id = ?");
                $cek->execute([$_SESSION['guru_id']]);
                if ($cek->fetch()) $is_scanner = true;
            }
            if (!$is_scanner) { redirect('/login'); exit; }
        }
        $title = "Scanner Sholat Dhuhur";
        include __DIR__ . '/../../resources/views/admin/absen_v2/scanner_sholat.php';
    }

    public static function processScanSholat() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
            exit;
        }

        if (!in_array($_SESSION['role_id'], [1, 2, 99])) {
            $is_scanner = false;
            if (isset($_SESSION['guru_id'])) {
                $db_core = Database::connect('core');
                $cek = $db_core->prepare("SELECT id FROM absen_akses_scanner WHERE guru_id = ?");
                $cek->execute([$_SESSION['guru_id']]);
                if ($cek->fetch()) $is_scanner = true;
            }
            if (!$is_scanner) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
                exit;
            }
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $raw_qr_text = isset($input['nis']) ? trim($input['nis']) : '';
        $nis = $raw_qr_text;

        if (preg_match('/NIS(?:\/NISN)?:\s*([a-zA-Z0-9\-\_]+)/i', $raw_qr_text, $matches)) {
            $nis = trim($matches[1]);
        }
        $username = '';
        if (preg_match('/USERNAME:\s*([a-zA-Z0-9\-\_]+)/i', $raw_qr_text, $matches)) {
            $username = trim($matches[1]);
        }
        
        if (empty($nis) && empty($username)) {
            echo json_encode(['success' => false, 'message' => 'QR Code tidak terbaca.']);
            exit;
        }

        try {
            $db_core = \App\Core\Database::connect('core');
            $siswa = false;

            if (!empty($username)) {
                $stmt_user = $db_core->prepare("SELECT id FROM users WHERE username = ?");
                $stmt_user->execute([$username]);
                $user = $stmt_user->fetch(\PDO::FETCH_ASSOC);
                
                if ($user) {
                    $stmt_siswa = $db_core->prepare("SELECT id, nis, nama, kelas_id, user_id FROM siswa WHERE user_id = ?");
                    $stmt_siswa->execute([$user['id']]);
                    $siswa = $stmt_siswa->fetch(\PDO::FETCH_ASSOC);
                }
            }

            if (!$siswa && !empty($nis)) {
                $stmt = $db_core->prepare("SELECT id, nis, nama, kelas_id, user_id FROM siswa WHERE nis = ?");
                $stmt->execute([$nis]);
                $siswa = $stmt->fetch(\PDO::FETCH_ASSOC);

                if (!$siswa) {
                    $jsonTest = json_decode($nis, true);
                    if(is_array($jsonTest) && isset($jsonTest['nis'])) {
                        $nis_json = trim($jsonTest['nis']);
                        $stmt->execute([$nis_json]);
                        $siswa = $stmt->fetch(\PDO::FETCH_ASSOC);
                    }
                }
            }

            if (!$siswa) {
                echo json_encode(['success' => false, 'message' => 'Data siswa tidak ditemukan.']);
                exit;
            }

            $today = date('Y-m-d');
            
            // Cek Kalender Akademik (Libur)
            $db_siakad = Database::connect('siakad');
            $cekLibur = $db_siakad->query("SELECT tipe, keterangan FROM kalender_akademik WHERE tanggal_mulai <= '$today' AND tanggal_selesai >= '$today' AND tipe = 'libur' LIMIT 1")->fetch();
            if ($cekLibur) {
                $msg = 'Hari ini Libur: ' . $cekLibur['keterangan'] . '. Tidak perlu scan absensi sholat.';
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            
            $jam = date('H:i:s');
            
            // FIX: HARI SABTU
            if (date('w') == 6) {
                $stmtPresensiCek = $db_core->prepare("SELECT id, jam_pulang FROM presensi WHERE user_id = ? AND tanggal = ? AND role_id = 3");
                $stmtPresensiCek->execute([$siswa['user_id'], $today]);
                $presensi = $stmtPresensiCek->fetch();

                if ($presensi && $presensi['jam_pulang']) {
                    echo json_encode(['success' => true, 'message' => 'Sudah absen pulang sebelumnya.', 'data' => ['nama' => $siswa['nama'], 'status' => 'Sudah Pulang']]);
                    exit;
                }
                
                if ($presensi) {
                    $stmtUpdatePresensi = $db_core->prepare("UPDATE presensi SET jam_pulang = ? WHERE id = ?");
                    $stmtUpdatePresensi->execute([$jam, $presensi['id']]);
                } else {
                    $stmtInsertPresensi = $db_core->prepare("INSERT INTO presensi (user_id, role_id, tanggal, jam_pulang, status, tahun_ajaran_id, semester) VALUES (?, 3, ?, ?, 'Hadir', 1, 1)");
                    $stmtInsertPresensi->execute([$siswa['user_id'], $today, $jam]);
                }
                
                echo json_encode(['success' => true, 'message' => 'Berhasil absen pulang (Sabtu)!', 'data' => ['nama' => $siswa['nama'], 'status' => 'Pulang']]);
                exit;
            }
            
            $stmtCek = $db_core->prepare("SELECT id FROM absensi_sholat WHERE tanggal = ? AND siswa_id = ?");
            $stmtCek->execute([$today, $siswa['id']]);
            
            if ($stmtCek->fetch()) {
                echo json_encode(['success' => true, 'message' => 'Sudah absen sholat sebelumnya.', 'data' => ['nama' => $siswa['nama'], 'status' => 'Sudah Hadir']]);
                exit;
            }

            $stmtInsert = $db_core->prepare("INSERT INTO absensi_sholat (siswa_id, tanggal, jam_scan, status) VALUES (?, ?, ?, 'Hadir')");
            $stmtInsert->execute([$siswa['id'], $today, $jam]);

            echo json_encode(['success' => true, 'message' => 'Berhasil absen sholat!', 'data' => ['nama' => $siswa['nama'], 'status' => 'Hadir']]);

        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()]);
        }
    }
    public static function rekapHarian() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            redirect('/login');
            exit;
        }

        $tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
        $kelas_id = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : '';

        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

        $kelasList = $db_core->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC")->fetchAll();

        // Query data absensi
        $sql = "SELECT a.*, s.nama, s.nis, COALESCE(k_hist.nama_kelas, k.nama_kelas) as nama_kelas, COALESCE(rks.kelas_id, s.kelas_id) as actual_kelas_id 
                FROM absensi_siswa a 
                JOIN aula_db.siswa s ON a.siswa_id = s.id 
                LEFT JOIN aula_db.riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                    AND rks.tahun_ajaran_id IN (
                        SELECT id FROM aula_db.tahun_ajaran WHERE name = '$active_year_name'
                    )
                LEFT JOIN aula_db.kelas k ON s.kelas_id = k.id 
                LEFT JOIN aula_db.kelas k_hist ON rks.kelas_id = k_hist.id
                WHERE a.tanggal = :tanggal 
                AND s.status IN ('Aktif', 'Alumni') 
                AND (rks.id IS NOT NULL OR s.tahun_ajaran = '$active_year_name')";
        
        $params = [':tanggal' => $tanggal];

        if (!empty($kelas_id)) {
            $sql .= " AND COALESCE(rks.kelas_id, s.kelas_id) = :kelas_id";
            $params[':kelas_id'] = $kelas_id;
        }

        $sql .= " ORDER BY nama_kelas ASC, s.nama ASC";

        $stmt = $db_siakad->prepare($sql);
        $stmt->execute($params);
        $absensi = $stmt->fetchAll();

        $title = "Rekap Harian Absensi";
        $initial = substr($_SESSION['nama'], 0, 1);
        $nama = $_SESSION['nama'];

        $kelas_nama = 'Semua Kelas';
        if (!empty($kelas_id)) {
            $k_find = array_filter($kelasList, function($k) use ($kelas_id) { return $k['id'] == $kelas_id; });
            if (!empty($k_find)) $kelas_nama = reset($k_find)['nama_kelas'];
        }

        if (isset($_GET['cetak']) && $_GET['cetak'] == 1) {
            include __DIR__ . '/../../resources/views/admin/absen_v2/cetak_rekap_harian.php';
            exit;
        }

        ob_start();
        include __DIR__ . '/../../resources/views/admin/absen_v2/rekap_harian.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

        public static function rekapBulanan() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            redirect('/login');
            exit;
        }

        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
        $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');
        $kelas_id = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : '';

        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

        $kelasList = $db_core->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC")->fetchAll();

        $where = "WHERE s.status IN ('Aktif', 'Alumni') AND (rks.id IS NOT NULL OR s.tahun_ajaran = '$active_year_name')";
        if (!empty($kelas_id)) {
            $where .= " AND COALESCE(rks.kelas_id, s.kelas_id) = " . intval($kelas_id);
        }

        $sqlSiswa = "SELECT s.id, s.nama, s.nis, s.user_id, COALESCE(rks.kelas_id, s.kelas_id) as kelas_id, COALESCE(k_hist.nama_kelas, k.nama_kelas) as nama_kelas 
                     FROM aula_db.siswa s 
                     LEFT JOIN aula_db.riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                         AND rks.tahun_ajaran_id IN (
                             SELECT id FROM aula_db.tahun_ajaran WHERE name = '$active_year_name'
                         )
                     LEFT JOIN aula_db.kelas k ON s.kelas_id = k.id 
                     LEFT JOIN aula_db.kelas k_hist ON rks.kelas_id = k_hist.id 
                     $where
                     ORDER BY COALESCE(k_hist.nama_kelas, k.nama_kelas) ASC, s.nama ASC";
                     
        $siswaList = $db_core->query($sqlSiswa)->fetchAll();

        // Get holidays from kalender_akademik
        $stmtLibur = $db_siakad->query("SELECT tanggal_mulai, tanggal_selesai FROM kalender_akademik WHERE tipe = 'libur'");
        $liburList = $stmtLibur->fetchAll();
        $libur_dates = [];
        foreach($liburList as $l) {
            $t = strtotime($l['tanggal_mulai']);
            $e = strtotime($l['tanggal_selesai']);
            if (!$e) $e = $t;
            while($t <= $e) {
                $libur_dates[] = date('Y-m-d', $t);
                $t = strtotime('+1 day', $t);
            }
        }

        // Generate date range
        $dates_range = [];
        $current = strtotime($start_date);
        $end = strtotime($end_date);
        while ($current <= $end) {
            $dt_str = date('Y-m-d', $current);
            if (date('w', $current) != 0 && !in_array($dt_str, $libur_dates)) { // Skip Sunday & Holidays
                $dates_range[] = $dt_str;
            }
            $current = strtotime('+1 day', $current);
        }

        // Get recap per siswa
        $rekap = [];
        foreach ($siswaList as $s) {
            $stmt = $db_siakad->prepare("SELECT status, COUNT(*) as total FROM absensi_siswa WHERE siswa_id = ? AND tanggal >= ? AND tanggal <= ? GROUP BY status");
            $stmt->execute([$s['id'], $start_date, $end_date]);
            $res = $stmt->fetchAll();
            
            $r = ['Hadir'=>0, 'Sakit'=>0, 'Izin'=>0, 'Alpa'=>0, 'Terlambat'=>0, 'Bolos'=>0];
            foreach ($res as $row) {
                $r[$row['status']] = $row['total'];
            }
            $s['rekap'] = $r;

            $stmt_detail = $db_siakad->prepare("SELECT tanggal, status FROM absensi_siswa WHERE siswa_id = ? AND tanggal >= ? AND tanggal <= ?");
            $stmt_detail->execute([$s['id'], $start_date, $end_date]);
            $detail_rows = $stmt_detail->fetchAll();

            $absen_harian = [];
            foreach ($dates_range as $dt) {
                $absen_harian[$dt] = '-';
            }
            foreach ($detail_rows as $row) {
                if (isset($absen_harian[$row['tanggal']])) {
                    $absen_harian[$row['tanggal']] = $row['status'];
                }
            }
            $s['harian'] = $absen_harian;
            
            // Get class name
            $kls = array_filter($kelasList, function($k) use ($s) { return $k['id'] == $s['kelas_id']; });
            $kls = reset($kls);
            $s['nama_kelas'] = $kls ? $kls['nama_kelas'] : '-';

            // Get avg jam masuk
            $s['avg_jam_masuk'] = '23:59:59';
            if (!empty($s['user_id'])) {
                $stmt_avg = $db_core->prepare("SELECT SEC_TO_TIME(AVG(TIME_TO_SEC(jam_masuk))) as avg_masuk FROM presensi WHERE user_id = ? AND tanggal >= ? AND tanggal <= ? AND jam_masuk IS NOT NULL");
                $stmt_avg->execute([$s['user_id'], $start_date, $end_date]);
                $avg_res = $stmt_avg->fetch();
                if ($avg_res && $avg_res['avg_masuk']) {
                    $s['avg_jam_masuk'] = $avg_res['avg_masuk'];
                }
            }
            
            $rekap[] = $s;
        }

        $title = "Rekap Absensi (Rentang Tanggal)";
        $initial = substr($_SESSION['nama'], 0, 1);
        $nama = $_SESSION['nama'];

        $kelas_nama = 'Semua Kelas';
        if (!empty($kelas_id)) {
            $k_find = array_filter($kelasList, function($k) use ($kelas_id) { return $k['id'] == $kelas_id; });
            if (!empty($k_find)) $kelas_nama = reset($k_find)['nama_kelas'];
        }
        
        $periode_nama = date('d M Y', strtotime($start_date)) . ' s/d ' . date('d M Y', strtotime($end_date));

        if (isset($_GET['cetak']) && $_GET['cetak'] == 1) {
            include __DIR__ . '/../../resources/views/admin/absen_v2/cetak_rekap_bulanan.php';
            exit;
        }

        if (isset($_GET['cetak_detail']) && $_GET['cetak_detail'] == 1) {
            include __DIR__ . '/../../resources/views/admin/absen_v2/cetak_rekap_bulanan_detail.php';
            exit;
        }

        ob_start();
        include __DIR__ . '/../../resources/views/admin/absen_v2/rekap_bulanan.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public static function ketidakhadiran() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            redirect('/login');
            exit;
        }

        $tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
        
        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

        // All siswa who are not present
        $sql = "SELECT a.*, s.nama, s.nis, COALESCE(k_hist.nama_kelas, k.nama_kelas) as nama_kelas 
                FROM absensi_siswa a 
                JOIN aula_db.siswa s ON a.siswa_id = s.id 
                LEFT JOIN aula_db.riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                    AND rks.tahun_ajaran_id IN (
                        SELECT id FROM aula_db.tahun_ajaran WHERE name = ?
                    )
                LEFT JOIN aula_db.kelas k ON s.kelas_id = k.id 
                LEFT JOIN aula_db.kelas k_hist ON rks.kelas_id = k_hist.id 
                WHERE a.tanggal = ? AND a.status IN ('Sakit', 'Izin', 'Alpa')
                AND s.status IN ('Aktif', 'Alumni') 
                AND (rks.id IS NOT NULL OR s.tahun_ajaran = ?)
                ORDER BY COALESCE(k_hist.nama_kelas, k.nama_kelas) ASC, s.nama ASC";
        
        $stmt = $db_siakad->prepare($sql);
        $stmt->execute([$active_year_name, $tanggal, $active_year_name]);
        $absensi = $stmt->fetchAll();

        // Get all students to find those who have NO record at all (Alpa by default)
        // This is complex, but let's assume cron job creates 'Alpa' records, 
        // or we just show those who are explicitly marked as Sakit/Izin/Alpa.
        
        $title = "Data Ketidakhadiran";
        $initial = substr($_SESSION['nama'], 0, 1);
        $nama = $_SESSION['nama'];

        ob_start();
        include __DIR__ . '/../../resources/views/admin/absen_v2/ketidakhadiran.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public static function pengaturan() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            redirect('/login');
            exit;
        }

        $db_core = Database::connect('core');
        
        // Ensure table exists
        $db_core->exec("CREATE TABLE IF NOT EXISTS absensi_pengaturan (
            id INT AUTO_INCREMENT PRIMARY KEY,
            kunci VARCHAR(50) NOT NULL UNIQUE,
            nilai VARCHAR(255) NULL
        )");

        // Default values
        $settings = [
            'jam_masuk' => '07:00',
            'batas_terlambat' => '07:15',
            'jam_pulang' => '15:00',
            'jam_pulang_jumat' => '11:00',
            'jam_pulang_sabtu' => '12:00',
            'jam_sholat_mulai' => '11:30',
            'jam_sholat_akhir' => '13:30',
            'teks_suara_masuk' => 'Halo [nama], Anda berhasil absen masuk',
            'teks_suara_pulang' => 'Terima kasih [nama], hati-hati di jalan',
            'teks_suara_sholat' => 'Halo [nama], Anda berhasil absen sholat',
            'teks_suara_gagal' => 'Maaf [nama], Anda sudah absen sebelumnya'
        ];

        $res = $db_core->query("SELECT * FROM absensi_pengaturan")->fetchAll();
        foreach ($res as $row) {
            $settings[$row['kunci']] = $row['nilai'];
        }

        // Guru Settings
        $db_core->exec("CREATE TABLE IF NOT EXISTS absensi_pengaturan_guru (
            id INT AUTO_INCREMENT PRIMARY KEY,
            kunci VARCHAR(50) NOT NULL UNIQUE,
            nilai VARCHAR(255) NULL
        )");

        $settings_guru = [
            'jam_masuk' => '07:00:00',
            'batas_terlambat' => '07:30:00',
            'jam_pulang' => '14:30:00',
            'jam_pulang_jumat' => '11:00:00',
            'jam_pulang_sabtu' => '12:00:00',
            'teks_suara_masuk' => 'Halo [nama], Anda berhasil absen masuk',
            'teks_suara_pulang' => 'Terima kasih [nama], hati-hati di jalan',
            'teks_suara_sholat' => 'Halo [nama], Anda berhasil absen sholat',
            'teks_suara_gagal' => 'Maaf [nama], Anda sudah absen sebelumnya'
        ];

        $res_guru = $db_core->query("SELECT * FROM absensi_pengaturan_guru")->fetchAll();
        foreach ($res_guru as $row) {
            $settings_guru[$row['kunci']] = $row['nilai'];
        }

        $title = "Konfigurasi Jam Absensi";
        $initial = substr($_SESSION['nama'], 0, 1);
        $nama = $_SESSION['nama'];

        ob_start();
        include __DIR__ . '/../../resources/views/admin/absen_v2/pengaturan.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public static function aksesScanner() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            redirect('/login');
            exit;
        }

        $db_core = Database::connect('core');
        
        $sql = "SELECT g.id, g.nama, g.nip, 
                    COALESCE((SELECT akses_siswa FROM absen_akses_scanner WHERE guru_id = g.id), 0) as akses_siswa,
                    COALESCE((SELECT akses_guru FROM absen_akses_scanner WHERE guru_id = g.id), 0) as akses_guru,
                    COALESCE((SELECT akses_izin FROM absen_akses_scanner WHERE guru_id = g.id), 0) as akses_izin
                FROM guru g ORDER BY g.nama ASC";
        $guruList = $db_core->query($sql)->fetchAll();

        $title = "Akses Scanner Absensi";
        $initial = substr($_SESSION['nama'], 0, 1);
        $nama = $_SESSION['nama'];

        ob_start();
        include __DIR__ . '/../../resources/views/admin/absen_v2/akses_scanner.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public static function saveAksesScanner() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit;
        }

        $guru_id = isset($_POST['guru_id']) ? intval($_POST['guru_id']) : 0;
        $status = isset($_POST['status']) ? intval($_POST['status']) : 0;
        $tipe = isset($_POST['tipe']) ? $_POST['tipe'] : ''; // 'siswa' or 'guru' or 'izin'

        if ($guru_id > 0 && in_array($tipe, ['siswa', 'guru', 'izin'])) {
            $db_core = Database::connect('core');
            
        }
    }

    public static function savePengaturan() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            echo "Unauthorized"; exit;
        }

        $db_core = Database::connect('core');
        
        $keys = ['jam_masuk', 'batas_terlambat', 'jam_pulang', 'jam_pulang_jumat', 'jam_pulang_sabtu', 'jam_sholat_mulai', 'jam_sholat_akhir', 'teks_suara_masuk', 'teks_suara_pulang', 'teks_suara_sholat', 'teks_suara_gagal'];
        foreach ($keys as $k) {
            if (isset($_POST[$k])) {
                $v = $_POST[$k];
                $stmt = $db_core->prepare("INSERT INTO absensi_pengaturan (kunci, nilai) VALUES (?, ?) ON DUPLICATE KEY UPDATE nilai = ?");
                $stmt->execute([$k, $v, $v]);
            }
        }

        $keys_guru = ['jam_masuk', 'batas_terlambat', 'jam_pulang', 'jam_pulang_jumat', 'jam_pulang_sabtu', 'teks_suara_masuk', 'teks_suara_pulang', 'teks_suara_sholat', 'teks_suara_gagal'];
        foreach ($keys_guru as $k) {
            $guru_key = 'guru_' . $k;
            if (isset($_POST[$guru_key])) {
                $v = $_POST[$guru_key];
                $stmt = $db_core->prepare("INSERT INTO absensi_pengaturan_guru (kunci, nilai) VALUES (?, ?) ON DUPLICATE KEY UPDATE nilai = ?");
                $stmt->execute([$k, $v, $v]);
            }
        }

        redirect('/admin/absen_v2/pengaturan?msg=success');
        exit;
    }

    public static function rekapDetail() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            redirect('/login');
            exit;
        }

        $tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
        $kelas_id = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : '';

        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
        $active_year_id = $active_year ? $active_year['id'] : 1;

        $kelasList = $db_core->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC")->fetchAll();
        
        if (empty($kelas_id) && !empty($kelasList)) {
            $kelas_id = $kelasList[0]['id'];
        }

        $kelas_nama = 'Semua Kelas';
        if (!empty($kelas_id)) {
            $k_find = array_filter($kelasList, function($k) use ($kelas_id) { return $k['id'] == $kelas_id; });
            if (!empty($k_find)) $kelas_nama = reset($k_find)['nama_kelas'];
        }

        $dayOfWeek = date('N', strtotime($tanggal));
        $days = [1=>'Senin', 2=>'Selasa', 3=>'Rabu', 4=>'Kamis', 5=>'Jumat', 6=>'Sabtu', 7=>'Minggu'];
        $hari_indo = $days[$dayOfWeek];

        // Ambil Mapel berdasarkan Jurnal yang betul-betul diisi guru pada hari itu
        // Jika guru telat ngisi, urutannya tetap mengacu pada jam resmi di jadwal pelajaran. 
        // Jika mapel tidak ada di jadwal (mapel tambahan), pakai waktu pengisian.
        $sqlJadwal = "SELECT jp.mata_pelajaran_id as mapel_id, mp.nama_mapel, mp.kode_mapel, 
                             MIN(jp.jam_mulai) as jam_mulai,
                             MAX(jp.jam_selesai) as jam_selesai,
                             MIN(jg.created_at) as waktu_isi
                      FROM jadwal_pelajaran jp
                      JOIN mata_pelajaran mp ON jp.mata_pelajaran_id = mp.id
                      LEFT JOIN jurnal_guru jg ON jg.mapel_id = jp.mata_pelajaran_id AND jg.kelas_id = jp.kelas_id AND jg.tanggal = ?
                      WHERE jp.kelas_id = ? AND jp.hari = ? AND jp.tahun_ajaran_id = ?
                      GROUP BY jp.mata_pelajaran_id, mp.nama_mapel, mp.kode_mapel
                      
                      UNION
                      
                      SELECT jg.mapel_id, mp.nama_mapel, mp.kode_mapel, 
                             MIN(TIME(jg.created_at)) as jam_mulai,
                             MAX(TIME(jg.created_at)) as jam_selesai,
                             MIN(jg.created_at) as waktu_isi
                      FROM jurnal_guru jg 
                      JOIN mata_pelajaran mp ON jg.mapel_id = mp.id 
                      LEFT JOIN jadwal_pelajaran jp ON jp.mata_pelajaran_id = jg.mapel_id AND jp.kelas_id = jg.kelas_id AND jp.hari = ? AND jp.tahun_ajaran_id = ?
                      WHERE jg.kelas_id = ? AND jg.tanggal = ? AND jp.id IS NULL
                      GROUP BY jg.mapel_id, mp.nama_mapel, mp.kode_mapel 
                      
                      ORDER BY jam_mulai ASC";
        $stmtJadwal = $db_siakad->prepare($sqlJadwal);
        $stmtJadwal->execute([$tanggal, $kelas_id, $hari_indo, $active_year_id, $hari_indo, $active_year_id, $kelas_id, $tanggal]);
        $jadwal = $stmtJadwal->fetchAll();

        // Ambil Siswa di kelas tersebut
        $where = "WHERE s.status IN ('Aktif', 'Alumni') AND (rks.id IS NOT NULL OR s.tahun_ajaran = '$active_year_name')";
        $where .= " AND COALESCE(rks.kelas_id, s.kelas_id) = " . intval($kelas_id);

        $sqlSiswa = "SELECT s.id, s.user_id, s.nama, s.nis 
                     FROM aula_db.siswa s 
                     LEFT JOIN aula_db.riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                         AND rks.tahun_ajaran_id IN (
                             SELECT id FROM aula_db.tahun_ajaran WHERE name = '$active_year_name'
                         )
                     $where
                     ORDER BY s.nama ASC";
        $siswaList = $db_core->query($sqlSiswa)->fetchAll();

        $rekap = [];
        foreach ($siswaList as $s) {
            $timeline = [];
            
            // 1. Gerbang Masuk
            $stmtPresensi = $db_core->prepare("SELECT jam_masuk, jam_pulang, status FROM presensi WHERE user_id = ? AND tanggal = ?");
            $stmtPresensi->execute([$s['user_id'], $tanggal]);
            $presensi = $stmtPresensi->fetch();
            
            $timeline['gate_masuk'] = $presensi && $presensi['jam_masuk'] ? $presensi['jam_masuk'] : '-';
            
            // 2. Mapel
            $timeline['mapels'] = [];
            foreach ($jadwal as $j) {
                $stmtMapel = $db_siakad->prepare("SELECT status FROM absensi_permapel WHERE siswa_id = ? AND mapel_id = ? AND tanggal = ?");
                $stmtMapel->execute([$s['id'], $j['mapel_id'], $tanggal]);
                $absenMapel = $stmtMapel->fetch();
                
                if ($absenMapel && !empty($absenMapel['status'])) {
                    $timeline['mapels'][] = $absenMapel['status'];
                } else {
                    if (empty($j['waktu_isi'])) {
                        // Guru belum mengisi jurnal
                        $timeline['mapels'][] = '-';
                    } else {
                        // Fallback: Jika jurnal mapel diisi oleh guru, namun absensi_permapel siswa ini belum/tidak tercatat
                        $stmtPusatCek = $db_siakad->prepare("SELECT status FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ?");
                        $stmtPusatCek->execute([$s['id'], $tanggal]);
                        $pusatCek = $stmtPusatCek->fetch();
                        
                        if ($pusatCek && !empty($pusatCek['status'])) {
                            $timeline['mapels'][] = $pusatCek['status'];
                        } else {
                            $timeline['mapels'][] = 'Hadir';
                        }
                    }
                }
            }
            
            // 3. Sholat
            $stmtSholat = $db_core->prepare("SELECT status, jam_scan FROM absensi_sholat WHERE siswa_id = ? AND tanggal = ?");
            $stmtSholat->execute([$s['id'], $tanggal]);
            $sholat = $stmtSholat->fetch();
            $timeline['sholat'] = $sholat ? 'Hadir ('.$sholat['jam_scan'].')' : '-';
            
            // 4. Gerbang Pulang
            $timeline['gate_pulang'] = $presensi && $presensi['jam_pulang'] ? $presensi['jam_pulang'] : '-';
            
            // 5. Status Akhir Pusat
            $stmtPusat = $db_siakad->prepare("SELECT status FROM absensi_siswa WHERE siswa_id = ? AND tanggal = ?");
            $stmtPusat->execute([$s['id'], $tanggal]);
            $pusat = $stmtPusat->fetch();
            $status_akhir = $pusat ? $pusat['status'] : '-';

            // Cek apakah hari ini sudah lewat jam pulang (asumsi jam 15:00 batasnya)
            $isPastDate = (strtotime($tanggal) < strtotime(date('Y-m-d')));
            $isAfterSchool = ($tanggal == date('Y-m-d') && date('H:i') >= '15:00');
            $isPastPulang = ($isPastDate || $isAfterSchool);

            // Override logika: Jika tidak Sakit/Izin/Alpa mutlak, cek kelengkapan
            if (!in_array($status_akhir, ['Sakit', 'Izin', 'S', 'I', 'Alpa', 'Alpha', 'A'])) {
                $isBolos = false;
                $isAlpa = false;
                
                $adaMapelHadir = false;
                $adaMapelAlpa = false;
                $adaMapelBolos = false;
                
                foreach ($timeline['mapels'] as $m) {
                    if (in_array($m, ['Hadir', 'H', 'Izin', 'I', 'Sakit', 'S'])) {
                        $adaMapelHadir = true;
                    }
                    if (in_array($m, ['Alpa', 'A', 'Alpha'])) {
                        $adaMapelAlpa = true;
                    }
                    if (in_array($m, ['Bolos', 'B'])) {
                        $adaMapelBolos = true;
                    }
                }
                
                if ($adaMapelHadir) {
                    if ($timeline['gate_masuk'] == '-') {
                        $isBolos = true; // masuk gerbang belum scan, tapi mapel hadir = Bolos
                    } elseif ($adaMapelAlpa) {
                        $isBolos = true; // masuk gerbang sudah, ada mapel alpha = Bolos
                    } elseif ($timeline['gate_pulang'] == '-' && $isPastPulang) {
                        $isBolos = true; // masuk gerbang sudah, mapel lengkap, belum scan pulang & lewat jam 15 = Bolos
                    }
                } else {
                    // tidak ada mapel hadir sama sekali
                    if ($adaMapelAlpa) {
                        $isAlpa = true;
                        if ($timeline['gate_masuk'] != '-') {
                            $isBolos = true; // scan gerbang sudah, tapi alpha semua mapel = Bolos (kabur)
                            $isAlpa = false;
                        }
                    }
                }
                
                if ($isBolos) {
                    $status_akhir = 'Bolos';
                } elseif ($isAlpa) {
                    $status_akhir = 'Alpa';
                } else {
                    // Jika tidak bolos dan tidak alpa (dan bukan izin/sakit)
                    // Set Hadir jika memang ada mapel hadir atau sudah scan pulang
                    if ($adaMapelHadir || ($timeline['gate_masuk'] != '-' && $timeline['gate_pulang'] != '-')) {
                        $status_akhir = 'Hadir';
                    } else if (empty($status_akhir)) {
                        $status_akhir = '-';
                    }
                }
            } else {
                // Mutlak Izin/Sakit/Alpa dari pusat, biarkan saja
                // Tapi kalau Alpha murni dan ada absen masuk gerbang -> Bolos
                if (in_array($status_akhir, ['Alpa', 'Alpha', 'A']) && $timeline['gate_masuk'] != '-') {
                    $status_akhir = 'Bolos';
                }
            }

            $timeline['status_akhir'] = $status_akhir;

            $s['timeline'] = $timeline;
            $rekap[] = $s;
        }

        $title = "Rekap Detail Absensi";
        $initial = substr($_SESSION['nama'], 0, 1);
        $nama = $_SESSION['nama'];

        if (isset($_GET['cetak']) && $_GET['cetak'] == 1) {
            include __DIR__ . '/../../resources/views/admin/absen_v2/cetak_rekap_detail.php';
            exit;
        }

        ob_start();
        include __DIR__ . '/../../resources/views/admin/absen_v2/rekap_detail.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public static function autoGenerateAlpaSiswa($start_date, $end_date) {
        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');
        
        $start = new \DateTime($start_date);
        $end = new \DateTime($end_date);
        $end->modify('+1 day'); // include end date
        
        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);
        
        // Ambil semua siswa aktif
        $stmt_siswa = $db_core->prepare("SELECT id, kelas_id FROM siswa WHERE status = 'Aktif'");
        $stmt_siswa->execute();
        $siswa_aktif = $stmt_siswa->fetchAll(\PDO::FETCH_ASSOC);
        
        if (empty($siswa_aktif)) return;
        
        $today_obj = new \DateTime(date('Y-m-d'));
        
        foreach ($period as $date) {
            $dt_str = $date->format('Y-m-d');
            
            if ($date > $today_obj) continue;
            
            // Skip hari Minggu (0)
            if ($date->format('w') == 0) continue;
            
            // Ambil id siswa yang sudah ada absennya di tanggal ini
            $stmt_absen = $db_siakad->prepare("SELECT siswa_id FROM absensi_siswa WHERE tanggal = ?");
            $stmt_absen->execute([$dt_str]);
            $sudah_absen = $stmt_absen->fetchAll(\PDO::FETCH_COLUMN);
            
            $belum_absen = [];
            foreach ($siswa_aktif as $siswa) {
                if (!in_array($siswa['id'], $sudah_absen)) {
                    $belum_absen[] = $siswa;
                }
            }
            
            if (!empty($belum_absen)) {
                $values = [];
                $params = [];
                foreach ($belum_absen as $siswa) {
                    $values[] = "(?, ?, ?, 'Alpa', NOW())";
                    $params[] = $siswa['id'];
                    $params[] = $siswa['kelas_id'] ?? 0;
                    $params[] = $dt_str;
                }
                
                // Chunk the inserts to avoid too many placeholders limit (65535 in some MySQL versions)
                $chunked_values = array_chunk($values, 1000);
                $chunked_params = array_chunk($params, 3000); // 3 params per value
                
                foreach ($chunked_values as $index => $chunk) {
                    $sql = "INSERT INTO absensi_siswa (siswa_id, kelas_id, tanggal, status, created_at) VALUES " . implode(", ", $chunk) . " ON DUPLICATE KEY UPDATE id=id";
                    $stmt_insert = $db_siakad->prepare($sql);
                    $stmt_insert->execute($chunked_params[$index]);
                }
            }
        }
    }
}
