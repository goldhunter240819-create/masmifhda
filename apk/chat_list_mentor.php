<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];

// Cek apakah guru ini punya anak asuh
$stmt_asuh = $core_db->prepare("SELECT COUNT(*) FROM guru_wali_siswa WHERE guru_id = :gid");
$stmt_asuh->execute(['gid' => $guru_id]);
$punya_anak_asuh = $stmt_asuh->fetchColumn() > 0;

if (!$punya_anak_asuh) {
    header("Location: akses_ditolak.php");
    exit;
}

$page_title = 'Daftar Obrolan';
include 'header.php';

// Ambil daftar siswa beserta obrolan terakhir
$stmt_siswa = $core_db->prepare("
    SELECT s.id, s.nama, s.foto, k.nama_kelas,
        (SELECT pesan FROM pesan_chat 
         WHERE (pengirim_role='siswa' AND pengirim_id=s.id AND penerima_role='guru' AND penerima_id=:gid)
            OR (pengirim_role='guru' AND pengirim_id=:gid AND penerima_role='siswa' AND penerima_id=s.id)
         ORDER BY created_at DESC LIMIT 1) as last_message,
        (SELECT created_at FROM pesan_chat 
         WHERE (pengirim_role='siswa' AND pengirim_id=s.id AND penerima_role='guru' AND penerima_id=:gid)
            OR (pengirim_role='guru' AND pengirim_id=:gid AND penerima_role='siswa' AND penerima_id=s.id)
         ORDER BY created_at DESC LIMIT 1) as last_time,
        (SELECT COUNT(*) FROM pesan_chat 
         WHERE pengirim_role='siswa' AND pengirim_id=s.id AND penerima_role='guru' AND penerima_id=:gid AND is_read=0) as unread
    FROM guru_wali_siswa gws
    JOIN siswa s ON gws.siswa_id = s.id 
    LEFT JOIN kelas k ON s.kelas_id = k.id 
    WHERE gws.guru_id = :gid AND s.status = 'Aktif'
    ORDER BY last_time DESC, s.nama ASC
");
$stmt_siswa->execute(['gid' => $guru_id]);
$siswa_list = $stmt_siswa->fetchAll(PDO::FETCH_ASSOC);

?>

<style>
.chat-list-item {
    display: flex;
    align-items: center;
    padding: 15px;
    background: #fff;
    border-bottom: 1px solid #f1f5f9;
    text-decoration: none;
    color: inherit;
    transition: background 0.2s;
}
.chat-list-item:hover {
    background: #f8fafc;
}
.chat-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #e2e8f0;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    margin-right: 15px;
    flex-shrink: 0;
    color: #94a3b8;
    font-size: 1.2rem;
}
.chat-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.chat-content {
    flex: 1;
    overflow: hidden;
}
.chat-header-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin-bottom: 3px;
}
.chat-name {
    font-weight: 700;
    font-size: 1rem;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.chat-time {
    font-size: 0.75rem;
    color: #94a3b8;
    white-space: nowrap;
    margin-left: 10px;
}
.chat-message-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.chat-preview {
    font-size: 0.85rem;
    color: #64748b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.chat-badge {
    background: #10b981;
    color: white;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 10px;
    min-width: 18px;
    text-align: center;
    margin-left: 10px;
}
</style>

<div class="header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
    <div class="header-top">
        <div class="greeting">
            <p>Pusat Pesan</p>
            <h1>Daftar Obrolan</h1>
        </div>
        <div class="avatar" style="color: #10b981; background: white;">
            <i class="fa-solid fa-comments"></i>
        </div>
    </div>
</div>

<div class="main-content" style="padding: 0; background: #fff;">
    <?php if (empty($siswa_list)): ?>
        <div style="text-align: center; padding: 40px 20px; color: #64748b;">
            <i class="fa-solid fa-inbox" style="font-size: 3rem; opacity: 0.3; margin-bottom: 15px; display: block;"></i>
            <p style="font-weight: 600; font-size: 0.95rem;">Belum ada siswa/wali murid di kelas ini.</p>
        </div>
    <?php else: ?>
        <div>
            <?php foreach ($siswa_list as $s): ?>
                <?php
                    $last_msg = $s['last_message'] ?: "Belum ada pesan. Ketuk untuk mulai obrolan.";
                    $time_str = "";
                    if ($s['last_time']) {
                        $dt = new DateTime($s['last_time']);
                        $now = new DateTime();
                        if ($dt->format('Y-m-d') === $now->format('Y-m-d')) {
                            $time_str = $dt->format('H:i');
                        } else if ($dt->format('Y-m-d') === $now->modify('-1 day')->format('Y-m-d')) {
                            $time_str = "Kemarin";
                        } else {
                            $time_str = $dt->format('d/m/y');
                        }
                    }
                ?>
                <a href="chat_guru.php?siswa_id=<?= $s['id'] ?>&type=mentor" class="chat-list-item">
                    <div class="chat-avatar">
                        <?php if (!empty($s['foto'])): ?>
                            <img src="/public/uploads/siswa/<?= htmlspecialchars($s['foto']) ?>" onerror="this.onerror=null; this.outerHTML='<i class=\'fa-solid fa-user\'></i>';">
                        <?php else: ?>
                            <i class="fa-solid fa-user"></i>
                        <?php endif; ?>
                    </div>
                    <div class="chat-content">
                        <div class="chat-header-row">
                            <div class="chat-name"><?= htmlspecialchars($s['nama']) ?></div>
                            <div class="chat-time" style="<?= $s['unread'] > 0 ? 'color: #10b981; font-weight: 600;' : '' ?>"><?= $time_str ?></div>
                        </div>
                        <div class="chat-message-row">
                            <div class="chat-preview" style="<?= $s['unread'] > 0 ? 'color: #1e293b; font-weight: 600;' : '' ?>"><?= htmlspecialchars(mb_strimwidth($last_msg, 0, 40, "...")) ?></div>
                            <?php if ($s['unread'] > 0): ?>
                                <div class="chat-badge"><?= $s['unread'] > 99 ? '99+' : $s['unread'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <div style="padding: 20px;">
        <a href="guru_dashboard.php" class="btn" style="display: block; text-align: center; background: #f1f5f9; color: #475569; padding: 12px; border-radius: 50px; font-weight: 600; text-decoration: none;">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
        </a>
    </div>
</div>

<?php include 'footer_guru.php'; ?>
