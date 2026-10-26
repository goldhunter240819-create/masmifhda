<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];
$siswa_id = isset($_GET['siswa_id']) ? intval($_GET['siswa_id']) : 0;

if (!$siswa_id) {
    echo "<script>alert('Pilih siswa untuk mulai chat.'); window.location.href='wali_kelas.php';</script>";
    exit;
}

$type = isset($_GET['type']) ? $_GET['type'] : 'wali';

if ($type === 'mentor') {
    // Cek Guru Wali (Mentor)
    $stmt_gw = $core_db->prepare("SELECT id FROM guru_wali_siswa WHERE guru_id = :gid AND siswa_id = :sid LIMIT 1");
    $stmt_gw->execute(['gid' => $guru_id, 'sid' => $siswa_id]);
    if (!$stmt_gw->fetch()) {
        echo "<script>alert('Siswa ini bukan anak asuh Anda.'); window.location.href='guru_dashboard.php';</script>";
        exit;
    }
} else {
    // Cek Wali Kelas
    $stmt_wk = $core_db->prepare("SELECT id FROM guru_tugas WHERE guru_id = :gid AND tugas = 'Wali Kelas' LIMIT 1");
    $stmt_wk->execute(['gid' => $guru_id]);
    if (!$stmt_wk->fetch()) {
        echo "<script>alert('Akses Ditolak.'); window.location.href='guru_dashboard.php';</script>";
        exit;
    }
}

// Ambil info siswa
$stmt_siswa = $core_db->prepare("SELECT nama, foto FROM siswa WHERE id = :sid LIMIT 1");
$stmt_siswa->execute(['sid' => $siswa_id]);
$siswa = $stmt_siswa->fetch(PDO::FETCH_ASSOC);

if (!$siswa) {
    echo "<script>alert('Siswa tidak ditemukan.'); window.location.href='wali_kelas.php';</script>";
    exit;
}

// Tidak menggunakan header_guru.php agar layout chat bisa full-screen layaknya WhatsApp

// Ambil logo institusi
$stmt_inst = $core_db->query("SELECT logo FROM institusi LIMIT 1");
$inst = $stmt_inst->fetch(PDO::FETCH_ASSOC);
$bg_logo = !empty($inst['logo']) ? "/public/uploads/logo/" . $inst['logo'] : "/public/assets/images/logo.png";

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Chat dengan Orang Tua - <?= htmlspecialchars($siswa['nama']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .chat-header {
            background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
            color: white;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 10;
        }
        .chat-header .back-btn {
            color: white;
            text-decoration: none;
            font-size: 1.2rem;
            padding: 5px;
        }
        .chat-header .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8b5cf6;
        }
        .chat-header .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .chat-header .info h2 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
        }
        .chat-header .info p {
            margin: 0;
            font-size: 0.75rem;
            opacity: 0.8;
        }
        .chat-body {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
            background-color: #f1f5f9;
            background-image: linear-gradient(rgba(241, 245, 249, 0.93), rgba(241, 245, 249, 0.93)), url('<?= htmlspecialchars($bg_logo) ?>');
            background-repeat: no-repeat;
            background-position: center;
            background-size: 250px;
            background-attachment: fixed;
        }
        .bubble {
            max-width: 75%;
            padding: 10px 14px;
            border-radius: 18px;
            font-size: 0.9rem;
            line-height: 1.4;
            position: relative;
            word-wrap: break-word;
        }
        .bubble.received {
            align-self: flex-start;
            background: white;
            color: #1e293b;
            border-bottom-left-radius: 4px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .bubble.sent {
            align-self: flex-end;
            background: #dcfce7; /* Soft WA green */
            color: #1e293b;
            border-bottom-right-radius: 4px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .bubble .time {
            font-size: 0.65rem;
            opacity: 0.7;
            text-align: right;
            margin-top: 5px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 4px;
        }
        .chat-footer {
            background: white;
            padding: 10px 15px;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .chat-footer input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #cbd5e1;
            border-radius: 25px;
            outline: none;
            font-size: 0.95rem;
            background: #f8fafc;
        }
        .chat-footer input:focus {
            border-color: #8b5cf6;
        }
        .chat-footer button {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #8b5cf6;
            color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            cursor: pointer;
            flex-shrink: 0;
            transition: transform 0.1s;
        }
        .chat-footer button:active {
            transform: scale(0.95);
        }
    </style>
</head>
<body>

<div class="chat-header">
    <a href="wali_kelas_detail.php?siswa_id=<?= $siswa_id ?>" class="back-btn"><i class="fa-solid fa-arrow-left"></i></a>
    <div class="avatar">
        <?php if (!empty($siswa['foto'])): ?>
            <img src="/public/uploads/siswa/<?= htmlspecialchars($siswa['foto']) ?>" onerror="this.style.display='none';">
        <?php else: ?>
            <i class="fa-solid fa-user"></i>
        <?php endif; ?>
    </div>
    <div class="info">
        <h2>Orang Tua / Wali</h2>
        <p>Ananda <?= htmlspecialchars($siswa['nama']) ?></p>
    </div>
</div>

<div class="chat-body" id="chatBody">
    <div style="text-align: center; margin-bottom: 10px;">
        <span style="background: #e2e8f0; color: #475569; padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">Enkripsi Ujung-ke-Ujung</span>
    </div>
    <!-- Messages will be loaded here -->
</div>

<div class="chat-footer">
    <input type="text" id="messageInput" placeholder="Ketik pesan..." autocomplete="off">
    <button id="sendBtn" onclick="sendMessage()"><i class="fa-solid fa-paper-plane"></i></button>
</div>

<script>
    const otherId = <?= $siswa_id ?>;
    const chatBody = document.getElementById('chatBody');
    const messageInput = document.getElementById('messageInput');
    
    // Kirim dengan tombol Enter
    messageInput.addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            event.preventDefault();
            sendMessage();
        }
    });

    function formatTime(dateStr) {
        const d = new Date(dateStr);
        return d.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    }

    let lastMessageData = '';

    function loadMessages() {
        fetch('api_chat.php?action=get_messages&other_id=' + otherId)
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success') {
                const msgs = res.data;
                const currentDataStr = JSON.stringify(msgs);
                if (currentDataStr !== lastMessageData) {
                    let html = '<div style="text-align: center; margin-bottom: 10px;"><span style="background: #e2e8f0; color: #475569; padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">Pesan Resmi Sekolah</span></div>';
                    
                    msgs.forEach(m => {
                        const isSent = (m.pengirim_role === 'guru');
                        const bubbleClass = isSent ? 'sent' : 'received';
                        let tick = '';
                        if(isSent) {
                            if (m.is_read == 1) {
                                tick = '<i class="fa-solid fa-check-double" style="color: #3b82f6;"></i>';
                            } else if (m.is_delivered == 1) {
                                tick = '<i class="fa-solid fa-check-double" style="color: #94a3b8;"></i>';
                            } else {
                                tick = '<i class="fa-solid fa-check" style="color: #94a3b8;"></i>';
                            }
                        }
                        html += `
                            <div class="bubble ${bubbleClass}">
                                ${m.pesan}
                                <span class="time">${formatTime(m.created_at)} ${tick}</span>
                            </div>
                        `;
                    });
                    
                    chatBody.innerHTML = html;
                    chatBody.scrollTop = chatBody.scrollHeight;
                    lastMessageData = currentDataStr;
                }
            }
        });
    }

    function sendMessage() {
        const text = messageInput.value.trim();
        if (!text) return;
        
        messageInput.value = '';
        messageInput.focus();

        const formData = new FormData();
        formData.append('action', 'send_message');
        formData.append('other_id', otherId);
        formData.append('pesan', text);

        fetch('api_chat.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success') {
                loadMessages();
            }
        });
    }

    // Load initial messages
    loadMessages();
    // Poll every 1.5 seconds for real-time feel
    setInterval(loadMessages, 1500);
</script>

</body>
</html>
