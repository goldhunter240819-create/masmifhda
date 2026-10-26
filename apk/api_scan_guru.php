<?php
date_default_timezone_set('Asia/Jakarta');
require_once 'koneksi.php';
header('Content-Type: application/json');

if (!isset($_SESSION['guru_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit;
}

// Cek akses scanner
$stmt_scan = $core_db->prepare("SELECT id FROM absen_akses_scanner WHERE guru_id = ? AND akses_guru = 1");
$stmt_scan->execute([$_SESSION['guru_id']]);
if (!$stmt_scan->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$raw_qr_text = isset($input['nis']) ? trim($input['nis']) : '';
$nip = $raw_qr_text;

if (preg_match('/NIP(?:\/NUPTK)?:\s*([a-zA-Z0-9\-\_]+)/i', $raw_qr_text, $matches)) {
    $nip = trim($matches[1]);
}
$username = '';
if (preg_match('/USERNAME:\s*([a-zA-Z0-9\-\_]+)/i', $raw_qr_text, $matches)) {
    $username = trim($matches[1]);
}
$user_id = null;
if (preg_match('/^TOKEN:([0-9]+):/i', $raw_qr_text, $matches)) {
    $user_id = trim($matches[1]);
}

if (empty($nip) && empty($username) && empty($user_id)) {
    echo json_encode(['success' => false, 'message' => 'QR Code tidak terbaca atau format tidak dikenali.']);
    exit;
}

try {
    $guru = false;

    if (!empty($user_id)) {
        $stmt_guru = $core_db->prepare("SELECT id, nip, nama, jenis_kelamin FROM guru WHERE user_id = ?");
        $stmt_guru->execute([$user_id]);
        $guru = $stmt_guru->fetch(PDO::FETCH_ASSOC);
    }

    if (!$guru && !empty($username)) {
        $stmt_user = $core_db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt_user->execute([$username]);
        $user = $stmt_user->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $stmt_guru = $core_db->prepare("SELECT id, nip, nama, jenis_kelamin FROM guru WHERE user_id = ?");
            $stmt_guru->execute([$user['id']]);
            $guru = $stmt_guru->fetch(PDO::FETCH_ASSOC);
        }
    }

    if (!$guru && !empty($nip)) {
        $stmt = $core_db->prepare("SELECT id, nip, nama, jenis_kelamin FROM guru WHERE nip = ?");
        $stmt->execute([$nip]);
        $guru = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$guru) {
            $jsonTest = json_decode($nip, true);
            if(is_array($jsonTest) && isset($jsonTest['nip'])) {
                $nip_json = trim($jsonTest['nip']);
                $stmt->execute([$nip_json]);
                $guru = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }
    }

    if (!$guru) {
        echo json_encode(['success' => false, 'message' => 'Data guru tidak ditemukan di database.']);
        exit;
    }

    $today = date('Y-m-d');
    $jam_sekarang = date('H:i:s');
    
    // Ambil pengaturan jam guru
    $stmtJam = $core_db->prepare("SELECT kunci, nilai FROM absensi_pengaturan_guru");
    $stmtJam->execute();
    $pengaturan = $stmtJam->fetchAll(PDO::FETCH_KEY_PAIR);
    
    $batas_terlambat = isset($pengaturan['batas_terlambat']) ? $pengaturan['batas_terlambat'] : '07:30:00';
    $jam_pulang_biasa = isset($pengaturan['jam_pulang']) ? $pengaturan['jam_pulang'] : '14:30:00';
    $jam_pulang_jumat = isset($pengaturan['jam_pulang_jumat']) ? $pengaturan['jam_pulang_jumat'] : '11:00:00';
    
    $is_jumat = (date('w') == 5);
    $jam_pulang_mulai = $is_jumat ? $jam_pulang_jumat : $jam_pulang_biasa;
    
    if ($jam_sekarang >= $jam_pulang_mulai) {
        $mode = 'pulang';
    } else {
        $mode = 'masuk';
    }
    
    $status_kehadiran = 'Hadir';
    if ($mode === 'masuk' && $jam_sekarang > $batas_terlambat) {
        $status_kehadiran = 'Terlambat';
    }
    
    $stmtCek = $siakad_db->prepare("SELECT id, status, waktu_masuk, waktu_keluar FROM absensi_guru WHERE tanggal = ? AND guru_id = ?");
    $stmtCek->execute([$today, $guru['id']]);
    $absenAda = $stmtCek->fetch();

    if ($mode === 'masuk') {
        if ($absenAda && $absenAda['waktu_masuk']) {
            echo json_encode(['success' => false, 'message' => 'Sudah absen masuk.', 'data' => ['nama' => $guru['nama'], 'kelas' => 'Guru', 'status' => 'Sudah Masuk', 'jk' => $guru['jenis_kelamin']]]);
            exit;
        }
        
        if ($absenAda) {
            $stmtUpdate = $siakad_db->prepare("UPDATE absensi_guru SET waktu_masuk = ?, status = ? WHERE id = ?");
            $stmtUpdate->execute([$jam_sekarang, $status_kehadiran, $absenAda['id']]);
        } else {
            $stmtInsert = $siakad_db->prepare("INSERT INTO absensi_guru (guru_id, tanggal, waktu_masuk, status, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmtInsert->execute([$guru['id'], $today, $jam_sekarang, $status_kehadiran]);
        }
        
        sendOneSignalNotification('guru_' . $guru['id'], 'Absensi Sukses', "Halo {$guru['nama']}, Anda berhasil absen masuk pada pukul {$jam_sekarang}. Status: {$status_kehadiran}");
        
        echo json_encode(['success' => true, 'message' => 'Berhasil absen masuk.', 'data' => ['nama' => $guru['nama'], 'kelas' => 'Guru', 'status' => $status_kehadiran, 'jk' => $guru['jenis_kelamin']]]);
    
    } elseif ($mode === 'pulang') {
        if ($absenAda && $absenAda['waktu_keluar']) {
            echo json_encode(['success' => false, 'message' => 'Sudah absen pulang.', 'data' => ['nama' => $guru['nama'], 'kelas' => 'Guru', 'status' => 'Sudah Pulang', 'jk' => $guru['jenis_kelamin']]]);
            exit;
        }
        
        if ($absenAda) {
            $stmtUpdate = $siakad_db->prepare("UPDATE absensi_guru SET waktu_keluar = ? WHERE id = ?");
            $stmtUpdate->execute([$jam_sekarang, $absenAda['id']]);
        } else {
            $stmtInsert = $siakad_db->prepare("INSERT INTO absensi_guru (guru_id, tanggal, waktu_masuk, waktu_keluar, status, created_at) VALUES (?, ?, NULL, ?, 'Alpa', NOW())");
            $stmtInsert->execute([$guru['id'], $today, $jam_sekarang]);
        }
        
        sendOneSignalNotification('guru_' . $guru['id'], 'Absen Pulang', "Halo {$guru['nama']}, Anda berhasil absen pulang pada pukul {$jam_sekarang}. Hati-hati di jalan!");
        
        echo json_encode(['success' => true, 'message' => 'Berhasil absen pulang.', 'data' => ['nama' => $guru['nama'], 'kelas' => 'Guru', 'status' => 'Pulang', 'jk' => $guru['jenis_kelamin']]]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}
