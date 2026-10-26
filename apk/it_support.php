<?php
require_once 'koneksi.php';

$is_guru = isset($_SESSION['guru_id']);
$is_siswa = isset($_SESSION['siswa_id']);

if (!$is_guru && !$is_siswa) {
    header("Location: login.php");
    exit;
}

$page_title = 'Hubungi IT Support';
$success_msg = '';
$error_msg = '';

$nama_pelapor = '';
$user_id = 0;
$user_type = 'guru';

if ($is_guru) {
    $stmt = $core_db->prepare("SELECT nama FROM guru WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $_SESSION['guru_id']]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $nama_pelapor = $data['nama'] ?? 'Guru';
    $user_id = $_SESSION['guru_id'];
    $user_type = 'guru';
} else {
    $stmt = $core_db->prepare("SELECT nama FROM siswa WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $_SESSION['siswa_id']]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $nama_pelapor = $data['nama'] ?? 'Siswa';
    $user_id = $_SESSION['siswa_id'];
    $user_type = 'siswa';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $kategori = $_POST['kategori'] ?? 'Lainnya';

    if (!empty($judul) && !empty($deskripsi)) {
        try {
            $stmt = $core_db->prepare("INSERT INTO it_tickets (user_id, user_type, nama_pelapor, judul, deskripsi, kategori) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $user_type, $nama_pelapor, $judul, $deskripsi, $kategori]);
            $success_msg = 'Laporan Anda berhasil dikirim! Tim IT akan merespons segera.';
        } catch (Exception $e) {
            $error_msg = 'Gagal mengirim laporan. Silakan coba lagi.';
        }
    } else {
        $error_msg = 'Judul dan deskripsi tidak boleh kosong.';
    }
}

// Ambil riwayat tiket user ini
$tikets = [];
try {
    $stmt = $core_db->prepare("SELECT * FROM it_tickets WHERE user_id = ? AND user_type = ? ORDER BY dibuat_pada DESC LIMIT 10");
    $stmt->execute([$user_id, $user_type]);
    $tikets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
    <div class="header-top">
        <div class="greeting">
            <p>Bantuan Aplikasi</p>
            <h1>IT Support</h1>
        </div>
        <div class="avatar" style="color: #6366f1; background: white;">
            <i class="fa-solid fa-headset"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <?php if (!empty($success_msg)): ?>
        <div style="background: #ecfdf5; border-left: 4px solid #10b981; padding: 15px; border-radius: 8px; margin-top: -20px; margin-bottom: 20px; z-index: 10; position: relative;">
            <p style="margin:0; color:#065f46; font-size:0.9rem; font-weight:600;"><i class="fa-solid fa-check-circle"></i> <?= $success_msg ?></p>
        </div>
    <?php endif; ?>
    <?php if (!empty($error_msg)): ?>
        <div style="background: #fee2e2; border-left: 4px solid #ef4444; padding: 15px; border-radius: 8px; margin-top: -20px; margin-bottom: 20px; z-index: 10; position: relative;">
            <p style="margin:0; color:#b91c1c; font-size:0.9rem; font-weight:600;"><i class="fa-solid fa-circle-exclamation"></i> <?= $error_msg ?></p>
        </div>
    <?php endif; ?>

    <!-- Form Laporan Baru -->
    <div class="data-card" style="<?= empty($success_msg) && empty($error_msg) ? 'margin-top: -2.5rem; z-index: 10;' : '' ?>">
        <h3 class="section-title" style="margin-top: 0;"><i class="fa-solid fa-ticket" style="color: #6366f1;"></i> Buat Laporan Baru</h3>

        <form method="POST">
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight:600; font-size:0.85rem; color:#475569; margin-bottom:6px;">Nama Pelapor</label>
                <input type="text" value="<?= htmlspecialchars($nama_pelapor) ?>" disabled style="width:100%; padding:10px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:0.9rem; background:#f8fafc; color:#64748b; box-sizing:border-box;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight:600; font-size:0.85rem; color:#475569; margin-bottom:6px;">Kategori Masalah <span style="color:red">*</span></label>
                <select name="kategori" style="width:100%; padding:10px 12px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.9rem; background:#f8fafc; box-sizing:border-box;">
                    <option value="Bug Aplikasi">🐛 Bug / Error Aplikasi</option>
                    <option value="Akun Login">🔑 Masalah Akun & Login</option>
                    <option value="Data Tidak Sesuai">📋 Data Tidak Sesuai / Salah</option>
                    <option value="Fitur Baru">💡 Saran Fitur Baru</option>
                    <option value="Lainnya">💬 Lainnya</option>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight:600; font-size:0.85rem; color:#475569; margin-bottom:6px;">Judul Laporan <span style="color:red">*</span></label>
                <input type="text" name="judul" required placeholder="Contoh: Tidak bisa login setelah ganti password" style="width:100%; padding:10px 12px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.9rem; background:#f8fafc; box-sizing:border-box;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display:block; font-weight:600; font-size:0.85rem; color:#475569; margin-bottom:6px;">Deskripsi Masalah <span style="color:red">*</span></label>
                <textarea name="deskripsi" required rows="4" placeholder="Jelaskan masalah Anda secara detail. Kapan terjadi? Di halaman mana? Apa yang Anda lakukan sebelumnya?" style="width:100%; padding:10px 12px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.9rem; background:#f8fafc; resize:vertical; box-sizing:border-box;"></textarea>
            </div>

            <button type="submit" style="width:100%; background: linear-gradient(135deg, #6366f1, #4f46e5); color:white; border:none; padding:14px; border-radius:8px; font-size:1rem; font-weight:bold; cursor:pointer;">
                <i class="fa-solid fa-paper-plane"></i> Kirim Laporan
            </button>
        </form>
    </div>

    <!-- Riwayat Tiket -->
    <?php if (!empty($tikets)): ?>
    <div class="data-card" style="margin-top: 15px;">
        <h3 class="section-title" style="margin-top: 0;"><i class="fa-solid fa-clock-rotate-left" style="color: #6366f1;"></i> Riwayat Laporan Saya</h3>
        <div style="display:flex; flex-direction:column; gap:10px;">
            <?php foreach ($tikets as $t): 
                $status_color = ['Baru' => '#f59e0b', 'Diproses' => '#3b82f6', 'Selesai' => '#10b981'][$t['status']] ?? '#94a3b8';
                $status_bg = ['Baru' => '#fffbeb', 'Diproses' => '#eff6ff', 'Selesai' => '#ecfdf5'][$t['status']] ?? '#f8fafc';
            ?>
            <div style="border:1px solid #e2e8f0; border-radius:10px; padding:14px; background:white;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
                    <div style="font-weight:700; font-size:0.9rem; color:#1e293b; flex:1; margin-right:10px;"><?= htmlspecialchars($t['judul']) ?></div>
                    <span style="background:<?= $status_bg ?>; color:<?= $status_color ?>; padding:3px 8px; border-radius:20px; font-size:0.7rem; font-weight:700; white-space:nowrap;"><?= $t['status'] ?></span>
                </div>
                <div style="font-size:0.75rem; color:#94a3b8; margin-bottom:6px;">
                    <i class="fa-solid fa-tag"></i> <?= htmlspecialchars($t['kategori']) ?> &nbsp;|&nbsp; 
                    <i class="fa-regular fa-clock"></i> <?= date('d M Y, H:i', strtotime($t['dibuat_pada'])) ?>
                </div>
                <?php if (!empty($t['balasan'])): ?>
                <div style="background:#eff6ff; border-left:3px solid #3b82f6; padding:10px; border-radius:6px; margin-top:8px;">
                    <div style="font-size:0.7rem; font-weight:700; color:#3b82f6; margin-bottom:4px;"><i class="fa-solid fa-reply"></i> Balasan Tim IT:</div>
                    <p style="margin:0; font-size:0.82rem; color:#1e293b;"><?= nl2br(htmlspecialchars($t['balasan'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <a href="<?= $is_guru ? 'profil_guru.php' : 'profil.php' ?>" style="display:block; text-align:center; background:#f1f5f9; color:#475569; padding:12px; border-radius:50px; font-weight:600; text-decoration:none; margin-bottom:20px; margin-top:15px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Profil
    </a>
</div>

<?php include $is_guru ? 'footer_guru.php' : 'footer.php'; ?>
