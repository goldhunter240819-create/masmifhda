<?php
require_once 'koneksi.php';

header('Content-Type: application/json');

// Cek autentikasi
$is_guru = isset($_SESSION['guru_id']);
$is_siswa = isset($_SESSION['siswa_id']);

if (!$is_guru && !$is_siswa) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

if ($action === 'get_messages') {
    $other_id = isset($_GET['other_id']) ? intval($_GET['other_id']) : 0;
    
    if (!$other_id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
        exit;
    }

    $my_role = $is_guru ? 'guru' : 'siswa';
    $my_id = $is_guru ? $_SESSION['guru_id'] : $_SESSION['siswa_id'];
    
    $other_role = $is_guru ? 'siswa' : 'guru';

    // Ambil pesan antara my_id dan other_id
    $stmt = $core_db->prepare("
        SELECT * FROM pesan_chat 
        WHERE (pengirim_role = ? AND pengirim_id = ? AND penerima_role = ? AND penerima_id = ?)
           OR (pengirim_role = ? AND pengirim_id = ? AND penerima_role = ? AND penerima_id = ?)
        ORDER BY created_at ASC
    ");
    $stmt->execute([
        $my_role, $my_id, $other_role, $other_id,
        $other_role, $other_id, $my_role, $my_id
    ]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Update is_read = 1 untuk pesan yang dikirim oleh other ke my_id
    $stmt_read = $core_db->prepare("
        UPDATE pesan_chat 
        SET is_read = 1 
        WHERE pengirim_role = ? AND pengirim_id = ? AND penerima_role = ? AND penerima_id = ? AND is_read = 0
    ");
    $stmt_read->execute([$other_role, $other_id, $my_role, $my_id]);

    echo json_encode(['status' => 'success', 'data' => $messages]);
    exit;
}
elseif ($action === 'send_message') {
    $other_id = isset($_POST['other_id']) ? intval($_POST['other_id']) : 0;
    $pesan = isset($_POST['pesan']) ? trim($_POST['pesan']) : '';
    
    if (!$other_id || empty($pesan)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
        exit;
    }

    $my_role = $is_guru ? 'guru' : 'siswa';
    $my_id = $is_guru ? $_SESSION['guru_id'] : $_SESSION['siswa_id'];
    
    $other_role = $is_guru ? 'siswa' : 'guru';

    // Simpan pesan
    $stmt = $core_db->prepare("
        INSERT INTO pesan_chat (pengirim_role, pengirim_id, penerima_role, penerima_id, pesan) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$my_role, $my_id, $other_role, $other_id, $pesan]);
    
    // Kirim Notifikasi OneSignal
    $targetId = ($other_role === 'guru') ? "guru_" . $other_id : "siswa_" . $other_id;
    
    if ($is_guru) {
        $stmt_check = $core_db->prepare("SELECT COUNT(*) FROM guru_wali_siswa WHERE guru_id = ? AND siswa_id = ?");
        $stmt_check->execute([$my_id, $other_id]);
        $is_mentor = $stmt_check->fetchColumn() > 0;
        
        $senderName = $is_mentor ? "Guru Wali (Mentor)" : "Wali Kelas";
        $clickUrl = $is_mentor ? "/apk/chat_guru_wali.php" : "/apk/chat_siswa.php";
    } else {
        $senderName = "Siswa";
        $stmt_check = $core_db->prepare("SELECT COUNT(*) FROM guru_wali_siswa WHERE guru_id = ? AND siswa_id = ?");
        $stmt_check->execute([$other_id, $my_id]);
        $is_mentor = $stmt_check->fetchColumn() > 0;
        
        $clickUrl = "/apk/chat_guru.php?siswa_id={$my_id}" . ($is_mentor ? "&type=mentor" : "");
    }
    
    sendOneSignalNotification($targetId, "Pesan Baru dari " . $senderName, $pesan, ["url" => $clickUrl]);

    echo json_encode(['status' => 'success', 'message' => 'Pesan terkirim']);
    exit;
}
elseif ($action === 'check_unread') {
    $my_role = $is_guru ? 'guru' : 'siswa';
    $my_id = $is_guru ? $_SESSION['guru_id'] : $_SESSION['siswa_id'];
    
    $stmt = $core_db->prepare("
        SELECT COUNT(*) FROM pesan_chat 
        WHERE penerima_role = ? AND penerima_id = ? AND is_read = 0
    ");
    $stmt->execute([$my_role, $my_id]);
    $unread = $stmt->fetchColumn();

    // Mark as delivered since the user is polling
    $stmt_del = $core_db->prepare("
        UPDATE pesan_chat 
        SET is_delivered = 1 
        WHERE penerima_role = ? AND penerima_id = ? AND is_delivered = 0
    ");
    $stmt_del->execute([$my_role, $my_id]);

    echo json_encode(['status' => 'success', 'unread' => $unread]);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
