<?php
date_default_timezone_set('Asia/Jakarta');
require_once 'koneksi.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

// --- CEK STATUS EVOTING ---
$stmtStatus = $core_db->query("SELECT setting_value FROM osim_settings WHERE setting_key = 'evoting_status'");
$evoting_status = $stmtStatus->fetchColumn() ?: 'closed';

if ($evoting_status != 'open' && $action != 'monitor') {
    echo json_encode(['success' => false, 'message' => 'Tempat Pemungutan Suara (TPS) sedang ditutup.']);
    exit;
}

if ($action == 'login') {
    $input = json_decode(file_get_contents('php://input'), true);
    $raw_qr_text = trim($input['nis'] ?? '');

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
        echo json_encode(['success' => false, 'message' => 'QR Code tidak terbaca.']);
        exit;
    }

    $pemilih = false;
    $tipe = 'siswa';
    $panggilan = '';

    // Cek Siswa
    if (!empty($user_id)) {
        $stmt_siswa = $core_db->prepare("SELECT id, nis, nama, kelas_id, user_id FROM siswa WHERE user_id = ?");
        $stmt_siswa->execute([$user_id]);
        $pemilih = $stmt_siswa->fetch(PDO::FETCH_ASSOC);
    }
    if (!$pemilih && !empty($username)) {
        $stmt_user = $core_db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt_user->execute([$username]);
        $user = $stmt_user->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $stmt_siswa = $core_db->prepare("SELECT id, nis, nama, kelas_id, user_id FROM siswa WHERE user_id = ?");
            $stmt_siswa->execute([$user['id']]);
            $pemilih = $stmt_siswa->fetch(PDO::FETCH_ASSOC);
        }
    }
    if (!$pemilih && !empty($nis)) {
        $stmt = $core_db->prepare("SELECT id, nis, nama, kelas_id, user_id FROM siswa WHERE nis = ?");
        $stmt->execute([$nis]);
        $pemilih = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$pemilih) {
            $jsonTest = json_decode($nis, true);
            if(is_array($jsonTest) && isset($jsonTest['nis'])) {
                $nis_json = trim($jsonTest['nis']);
                $stmt->execute([$nis_json]);
                $pemilih = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }
    }

    // Cek Guru jika siswa tidak ditemukan
    if (!$pemilih) {
        if (!empty($user_id)) {
            $stmt_guru = $core_db->prepare("SELECT id, nip as nis, nama, jenis_kelamin, user_id FROM guru WHERE user_id = ?");
            $stmt_guru->execute([$user_id]);
            $pemilih = $stmt_guru->fetch(PDO::FETCH_ASSOC);
        }
        if (!$pemilih && !empty($username)) {
            $stmt_user = $core_db->prepare("SELECT id FROM users WHERE username = ?");
            $stmt_user->execute([$username]);
            $user = $stmt_user->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $stmt_guru = $core_db->prepare("SELECT id, nip as nis, nama, jenis_kelamin, user_id FROM guru WHERE user_id = ?");
                $stmt_guru->execute([$user['id']]);
                $pemilih = $stmt_guru->fetch(PDO::FETCH_ASSOC);
            }
        }
        if (!$pemilih && !empty($nis)) {
            $stmt_guru = $core_db->prepare("SELECT id, nip as nis, nama, jenis_kelamin, user_id FROM guru WHERE nip = ?");
            $stmt_guru->execute([$nis]);
            $pemilih = $stmt_guru->fetch(PDO::FETCH_ASSOC);
            if (!$pemilih) {
                $jsonTest = json_decode($nis, true);
                if(is_array($jsonTest) && isset($jsonTest['nip'])) {
                    $nip_json = trim($jsonTest['nip']);
                    $stmt_guru->execute([$nip_json]);
                    $pemilih = $stmt_guru->fetch(PDO::FETCH_ASSOC);
                } elseif(is_array($jsonTest) && isset($jsonTest['nis'])) {
                    $nis_json = trim($jsonTest['nis']);
                    $stmt_guru->execute([$nis_json]);
                    $pemilih = $stmt_guru->fetch(PDO::FETCH_ASSOC);
                }
            }
        }
        if ($pemilih) {
            $tipe = 'guru';
            $panggilan = ($pemilih['jenis_kelamin'] == 'P' || $pemilih['jenis_kelamin'] == 'Perempuan') ? 'Ibu ' : 'Bapak ';
        }
    }

    if (!$pemilih) {
        echo json_encode(['success' => false, 'message' => 'Data pemilih tidak ditemukan!']);
        exit;
    }

    // Pastikan tabel osim_pemilih_log memiliki kolom tipe_pemilih
    try {
        $core_db->exec("ALTER TABLE osim_pemilih_log ADD COLUMN tipe_pemilih VARCHAR(20) DEFAULT 'siswa'");
    } catch (\Exception $e) {}

    // Fix data lama: update record guru yang sebelumnya tersimpan sebagai 'siswa'
    try {
        $core_db->exec("
            UPDATE osim_pemilih_log 
            SET tipe_pemilih = 'guru' 
            WHERE tipe_pemilih = 'siswa' 
            AND siswa_id IN (SELECT id FROM guru)
            AND siswa_id NOT IN (SELECT id FROM siswa)
        ");
    } catch (\Exception $e) {}

    // Cek apakah sudah memilih
    $stmtCek = $core_db->prepare("SELECT id FROM osim_pemilih_log WHERE siswa_id = ? AND tipe_pemilih = ?");
    $stmtCek->execute([$pemilih['id'], $tipe]);
    if ($stmtCek->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Maaf, ' . $panggilan . $pemilih['nama'] . ' sudah menggunakan hak suaranya!']);
        exit;
    }

    // Buat session voting
    $_SESSION['evoting_siswa_id'] = $pemilih['id']; // We keep the session variable name for compatibility
    $_SESSION['evoting_siswa_nama'] = $pemilih['nama'];
    $_SESSION['evoting_tipe_pemilih'] = $tipe;

    echo json_encode(['success' => true, 'message' => 'Berhasil. Mengalihkan ke Bilik Suara...', 'nama' => $pemilih['nama'], 'panggilan' => $panggilan]);
    exit;
}

elseif ($action == 'vote') {
    if (!isset($_SESSION['evoting_siswa_id'])) {
        echo json_encode(['success' => false, 'message' => 'Sesi habis atau tidak valid. Silakan scan ulang!']);
        exit;
    }

    $siswa_id = $_SESSION['evoting_siswa_id'];
    $tipe_pemilih = $_SESSION['evoting_tipe_pemilih'] ?? 'siswa';
    $kandidat_id = intval($_POST['kandidat_id'] ?? 0);

    if ($kandidat_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Kandidat tidak valid.']);
        exit;
    }

    // Cek lagi untuk keamanan ganda
    $stmtCek = $core_db->prepare("SELECT id FROM osim_pemilih_log WHERE siswa_id = ? AND tipe_pemilih = ?");
    $stmtCek->execute([$siswa_id, $tipe_pemilih]);
    if ($stmtCek->fetch()) {
        unset($_SESSION['evoting_siswa_id']);
        unset($_SESSION['evoting_siswa_nama']);
        unset($_SESSION['evoting_tipe_pemilih']);
        echo json_encode(['success' => false, 'message' => 'Anda sudah memilih.']);
        exit;
    }

    try {
        $core_db->beginTransaction();

        // 1. Simpan Suara Anonim
        $stmtSuara = $core_db->prepare("INSERT INTO osim_suara (kandidat_id, created_at) VALUES (?, NOW())");
        $stmtSuara->execute([$kandidat_id]);

        // 2. Simpan Log Pemilih (supaya terbaca oleh Monitor & Mencegah double vote)
        $stmtLog = $core_db->prepare("INSERT INTO osim_pemilih_log (siswa_id, tipe_pemilih, waktu, terbaca) VALUES (?, ?, NOW(), 0)");
        $stmtLog->execute([$siswa_id, $tipe_pemilih]);

        $core_db->commit();

        unset($_SESSION['evoting_siswa_id']);
        unset($_SESSION['evoting_siswa_nama']);
        unset($_SESSION['evoting_tipe_pemilih']);

        echo json_encode(['success' => true, 'message' => 'Suara berhasil disimpan!']);
    } catch (Exception $e) {
        $core_db->rollBack();
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan suara: ' . $e->getMessage()]);
    }
    exit;
}

elseif ($action == 'monitor') {
    // Cek status kunci kotak suara
    $stmtLock = $core_db->prepare("SELECT setting_value FROM osim_settings WHERE setting_key = 'tps_kotak_locked'");
    $stmtLock->execute();
    $lockData = $stmtLock->fetch(PDO::FETCH_ASSOC);
    $is_locked = true;
    if ($lockData) {
        $is_locked = ($lockData['setting_value'] == '1');
    } else {
        $core_db->query("INSERT IGNORE INTO osim_settings (setting_key, setting_value) VALUES ('tps_kotak_locked', '1')");
    }

    // Dipanggil setiap 2 detik oleh Monitor untuk melihat adakah pemilih baru
    $stmtNew = $core_db->query("
        SELECT l.id, l.tipe_pemilih,
               CASE WHEN l.tipe_pemilih = 'guru' THEN g.nama ELSE s.nama END as nama,
               CASE WHEN l.tipe_pemilih = 'guru' THEN g.jenis_kelamin ELSE s.jenis_kelamin END as jenis_kelamin
        FROM osim_pemilih_log l 
        LEFT JOIN siswa s ON l.siswa_id = s.id AND l.tipe_pemilih = 'siswa'
        LEFT JOIN guru g ON l.siswa_id = g.id AND l.tipe_pemilih = 'guru'
        WHERE l.terbaca = 0 
        ORDER BY l.waktu ASC LIMIT 1
    ");
    $newVote = $stmtNew->fetch(PDO::FETCH_ASSOC);

    if ($newVote) {
        // Tandai sudah dibaca monitor
        $stmtUpdate = $core_db->prepare("UPDATE osim_pemilih_log SET terbaca = 1 WHERE id = ?");
        $stmtUpdate->execute([$newVote['id']]);

        // Ambil total suara terbaru
        $total = $core_db->query("SELECT COUNT(*) FROM osim_suara")->fetchColumn();
        
        // Atur panggilan
        $panggilan = '';
        if ($newVote['tipe_pemilih'] == 'guru') {
            $panggilan = ($newVote['jenis_kelamin'] == 'P' || $newVote['jenis_kelamin'] == 'Perempuan') ? 'Ibu ' : 'Bapak ';
        }
        $nama_lengkap = $panggilan . $newVote['nama'];

        echo json_encode([
            'success' => true, 
            'new_vote' => true, 
            'nama' => $nama_lengkap, 
            'total_suara' => $total,
            'is_locked' => $is_locked
        ]);
    } else {
        echo json_encode([
            'success' => true, 
            'new_vote' => false,
            'is_locked' => $is_locked
        ]);
    }
    exit;
}
elseif ($action == 'toggle_lock') {
    $stmtLock = $core_db->prepare("SELECT setting_value FROM osim_settings WHERE setting_key = 'tps_kotak_locked'");
    $stmtLock->execute();
    $lockData = $stmtLock->fetch(PDO::FETCH_ASSOC);
    $newVal = '1';
    if ($lockData && $lockData['setting_value'] == '1') {
        $newVal = '0';
    }
    $core_db->prepare("UPDATE osim_settings SET setting_value = ? WHERE setting_key = 'tps_kotak_locked'")->execute([$newVal]);
    
    echo json_encode(['success' => true, 'is_locked' => ($newVal == '1')]);
    exit;
}

elseif ($action == 'buka_kotak') {
    // Ambil data semua kandidat beserta perolehan suara
    $stmtKandidat = $core_db->query("
        SELECT k.id, k.nomor_urut, k.nama_ketua, k.nama_wakil, k.foto,
               (SELECT COUNT(*) FROM osim_suara s WHERE s.kandidat_id = k.id) as perolehan
        FROM osim_kandidat k
        WHERE k.status = 'aktif'
        ORDER BY k.nomor_urut ASC
    ");
    $hasil = $stmtKandidat->fetchAll(PDO::FETCH_ASSOC);
    $total = $core_db->query("SELECT COUNT(*) FROM osim_suara")->fetchColumn();

    echo json_encode(['success' => true, 'kandidat' => $hasil, 'total_suara' => intval($total)]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
