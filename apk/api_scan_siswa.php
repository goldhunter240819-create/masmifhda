<?php
date_default_timezone_set('Asia/Jakarta');
require_once 'koneksi.php';
header('Content-Type: application/json');

if (!isset($_SESSION['guru_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit;
}

// Cek akses scanner
$stmt_scan = $core_db->prepare("SELECT id FROM absen_akses_scanner WHERE guru_id = ? AND akses_siswa = 1");
$stmt_scan->execute([$_SESSION['guru_id']]);
if (!$stmt_scan->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit;
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
$user_id = null;
if (preg_match('/^TOKEN:([0-9]+):/i', $raw_qr_text, $matches)) {
    $user_id = trim($matches[1]);
}

if (empty($nis) && empty($username) && empty($user_id)) {
    echo json_encode(['success' => false, 'message' => 'QR Code tidak terbaca atau format tidak dikenali.']);
    exit;
}

try {
    $siswa = false;

    if (!empty($user_id)) {
        $stmt_siswa = $core_db->prepare("SELECT id, nis, nama, kelas_id, user_id FROM siswa WHERE user_id = ?");
        $stmt_siswa->execute([$user_id]);
        $siswa = $stmt_siswa->fetch(PDO::FETCH_ASSOC);
    }

    if (!$siswa && !empty($username)) {
        $stmt_user = $core_db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt_user->execute([$username]);
        $user = $stmt_user->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $stmt_siswa = $core_db->prepare("SELECT id, nis, nama, kelas_id, user_id FROM siswa WHERE user_id = ?");
            $stmt_siswa->execute([$user['id']]);
            $siswa = $stmt_siswa->fetch(PDO::FETCH_ASSOC);
        }
    }

    if (!$siswa && !empty($nis)) {
        $stmt = $core_db->prepare("SELECT id, nis, nama, kelas_id, user_id FROM siswa WHERE nis = ?");
        $stmt->execute([$nis]);
        $siswa = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$siswa) {
            $jsonTest = json_decode($nis, true);
            if(is_array($jsonTest) && isset($jsonTest['nis'])) {
                $nis_json = trim($jsonTest['nis']);
                $stmt->execute([$nis_json]);
                $siswa = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }
    }

    if (!$siswa) {
        echo json_encode(['success' => false, 'message' => 'Data siswa tidak ditemukan di database.']);
        exit;
    }

    $nama_kelas = "-";
    if ($siswa['kelas_id']) {
        $stmtKelas = $core_db->prepare("SELECT nama_kelas FROM kelas WHERE id = ?");
        $stmtKelas->execute([$siswa['kelas_id']]);
        $kelasInfo = $stmtKelas->fetch();
        if ($kelasInfo) $nama_kelas = $kelasInfo['nama_kelas'];
    }

    $today = date('Y-m-d');
    $jam_sekarang = date('H:i:s');
    
    $stmtJam = $core_db->prepare("SELECT kunci, nilai FROM absensi_pengaturan");
    $stmtJam->execute();
    $pengaturan = $stmtJam->fetchAll(PDO::FETCH_KEY_PAIR);
    
    $batas_terlambat = isset($pengaturan['batas_terlambat']) ? $pengaturan['batas_terlambat'] : '07:15:00';
    $jam_pulang_biasa = isset($pengaturan['jam_pulang']) ? $pengaturan['jam_pulang'] : '14:00:00';
    $jam_pulang_jumat = isset($pengaturan['jam_pulang_jumat']) ? $pengaturan['jam_pulang_jumat'] : '11:00:00';
    $jam_pulang_sabtu = isset($pengaturan['jam_pulang_sabtu']) ? $pengaturan['jam_pulang_sabtu'] : '12:00:00';
    $jam_sholat_mulai = isset($pengaturan['jam_sholat_mulai']) ? $pengaturan['jam_sholat_mulai'] : '11:30:00';
    $jam_sholat_akhir = isset($pengaturan['jam_sholat_akhir']) ? $pengaturan['jam_sholat_akhir'] : '13:30:00';
    
    $hari_ini = date('w');
    $is_jumat = ($hari_ini == 5);
    $is_sabtu = ($hari_ini == 6);
    
    if ($is_jumat) {
        $jam_pulang_mulai = $jam_pulang_jumat;
    } elseif ($is_sabtu) {
        $jam_pulang_mulai = $jam_pulang_sabtu;
    } else {
        $jam_pulang_mulai = $jam_pulang_biasa;
    }
    
    if ($jam_sekarang >= $jam_pulang_mulai) {
        $mode = 'pulang';
    } elseif ($jam_sekarang >= $jam_sholat_mulai && $jam_sekarang <= $jam_sholat_akhir && !$is_jumat && !$is_sabtu) {
        $mode = 'sholat';
    } else {
        $mode = 'masuk';
    }
    
    $status_kehadiran = 'Hadir';
    if ($mode === 'masuk' && $jam_sekarang > $batas_terlambat) {
        $status_kehadiran = 'Terlambat';
    }
    
    if ($mode === 'sholat') {
        $stmtCekSholat = $core_db->prepare("SELECT id FROM absensi_sholat WHERE tanggal = ? AND siswa_id = ?");
        $stmtCekSholat->execute([$today, $siswa['id']]);
        if ($stmtCekSholat->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Sudah absen sholat.', 'data' => ['nama' => $siswa['nama'], 'kelas' => $nama_kelas, 'status' => 'Sudah Sholat']]);
            exit;
        }
        $stmtInsertSholat = $core_db->prepare("INSERT INTO absensi_sholat (siswa_id, tanggal, jam_scan, status) VALUES (?, ?, ?, 'Hadir')");
        $stmtInsertSholat->execute([$siswa['id'], $today, $jam_sekarang]);
        echo json_encode(['success' => true, 'message' => 'Berhasil absen sholat.', 'data' => ['nama' => $siswa['nama'], 'kelas' => $nama_kelas, 'status' => 'Hadir Sholat']]);
        exit;
    }
    
    $stmtPresensiCek = $core_db->prepare("SELECT id, jam_masuk, jam_pulang FROM presensi WHERE user_id = ? AND tanggal = ? AND role_id = 3");
    $stmtPresensiCek->execute([$siswa['user_id'], $today]);
    $absenAda = $stmtPresensiCek->fetch();
    $kelas = $nama_kelas;
    
    if ($mode === 'masuk') {
        if ($absenAda && $absenAda['jam_masuk']) {
            echo json_encode(['success' => false, 'message' => 'Sudah absen masuk sebelumnya.', 'data' => ['nama' => $siswa['nama'], 'kelas' => $kelas, 'status' => 'Sudah Masuk']]);
            exit;
        }
        if ($absenAda) {
            $stmtUpdate = $core_db->prepare("UPDATE presensi SET jam_masuk = ?, status = ? WHERE id = ?");
            $stmtUpdate->execute([$jam_sekarang, $status_kehadiran, $absenAda['id']]);
        } else {
            $stmtInsert = $core_db->prepare("INSERT INTO presensi (user_id, role_id, tanggal, jam_masuk, status, created_at, tahun_ajaran_id, semester) VALUES (?, 3, ?, ?, ?, NOW(), 1, 1)");
            $stmtInsert->execute([$siswa['user_id'], $today, $jam_sekarang, $status_kehadiran]);
        }
        
        sinkronisasi_status_pusat($siswa['id'], $today, $core_db, $siakad_db);
        
        sendOneSignalNotification('siswa_' . $siswa['id'], 'Absensi Sukses', "Halo {$siswa['nama']}, Anda berhasil absen masuk pada pukul {$jam_sekarang}. Status: {$status_kehadiran}");
        
        echo json_encode(['success' => true, 'message' => 'Berhasil absen masuk.', 'data' => ['nama' => $siswa['nama'], 'kelas' => $kelas, 'status' => $status_kehadiran]]);
    } elseif ($mode === 'pulang') {
        if ($absenAda && $absenAda['jam_pulang']) {
            echo json_encode(['success' => false, 'message' => 'Sudah absen pulang.', 'data' => ['nama' => $siswa['nama'], 'kelas' => $kelas, 'status' => 'Sudah Pulang']]);
            exit;
        }
        
        if ($absenAda) {
            $stmtUpdate = $core_db->prepare("UPDATE presensi SET jam_pulang = ? WHERE id = ?");
            $stmtUpdate->execute([$jam_sekarang, $absenAda['id']]);
        } else {
            $stmtInsert = $core_db->prepare("INSERT INTO presensi (user_id, role_id, tanggal, jam_masuk, jam_pulang, status, created_at, tahun_ajaran_id, semester) VALUES (?, 3, ?, NULL, ?, 'Alfa', NOW(), 1, 1)");
            $stmtInsert->execute([$siswa['user_id'], $today, $jam_sekarang]);
        }
        
        sinkronisasi_status_pusat($siswa['id'], $today, $core_db, $siakad_db);
        
        sendOneSignalNotification('siswa_' . $siswa['id'], 'Absen Pulang', "Halo {$siswa['nama']}, Anda berhasil absen pulang pada pukul {$jam_sekarang}. Hati-hati di jalan!");
        
        echo json_encode(['success' => true, 'message' => 'Berhasil absen pulang.', 'data' => ['nama' => $siswa['nama'], 'kelas' => $kelas, 'status' => 'Pulang']]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}
