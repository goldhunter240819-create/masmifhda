<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];

// Cek apakah guru ini punya anak asuh
$stmt_asuh = $core_db->prepare("
    SELECT COUNT(*) 
    FROM guru_wali_siswa 
    WHERE guru_id = :gid
");
$stmt_asuh->execute(['gid' => $guru_id]);
$punya_anak_asuh = $stmt_asuh->fetchColumn() > 0;

$action = isset($_GET['action']) ? $_GET['action'] : 'daftar';

// Set title and subtitle based on action
$page_title = 'Guru Wali (Mentor)';
$subtitle = 'Daftar Anak Asuh';
$header_icon = 'fa-users';
$dest_url = 'guru_wali_detail.php';

if ($action === 'pantau') {
    $page_title = 'Pantau Akademik';
    $subtitle = 'Pilih Anak Asuh';
    $header_icon = 'fa-chart-line';
    $dest_url = 'guru_wali_akademik.php';
} elseif ($action === 'catatan') {
    // Redirect directly to the global journal page instead of student list
    header("Location: guru_wali_jurnal.php");
    exit;
}

include 'header.php';

// Ambil daftar anak asuh
$stmt_siswa = $core_db->prepare("
    SELECT s.id, s.nama, s.nis, s.foto, k.nama_kelas 
    FROM guru_wali_siswa gws
    JOIN aula_db.siswa s ON gws.siswa_id = s.id 
    LEFT JOIN aula_db.kelas k ON s.kelas_id = k.id 
    WHERE gws.guru_id = :gid AND s.status = 'Aktif'
    ORDER BY s.nama ASC
");
$stmt_siswa->execute(['gid' => $guru_id]);
$siswa_list = $stmt_siswa->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="header" style="background: linear-gradient(135deg, #ec4899 0%, #be185d 100%); padding-top: 3.5rem;">
    <div class="header-top">
        <div class="greeting">
            <p><?= htmlspecialchars($subtitle) ?></p>
            <h1><?= htmlspecialchars($page_title) ?></h1>
        </div>
        <div class="avatar" style="color: #ec4899; background: white;">
            <i class="fa-solid <?= $header_icon ?>"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10;">
        <h3 class="section-title" style="margin-top: 0; margin-bottom: 15px;"><i class="fa-solid fa-clipboard-user" style="color: #ec4899; margin-right: 5px;"></i> Pilih Anak Asuh</h3>
        
        <?php if (!$punya_anak_asuh): ?>
            <div style="text-align: center; padding: 20px; color: #64748b;">
                <i class="fa-solid fa-users-slash" style="font-size: 2rem; opacity: 0.3; margin-bottom: 10px; display: block;"></i>
                <p style="font-weight: 600; font-size: 0.9rem;">Anda belum memiliki anak asuh.</p>
                <p style="font-size: 0.8rem; margin-top: 5px;">Silakan hubungi Admin SIAKAD untuk pengaturan anak asuh.</p>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <?php foreach ($siswa_list as $s): ?>
                    <a href="<?= $dest_url ?>?siswa_id=<?= $s['id'] ?>" style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #fdf2f8; border: 1px solid #fbcfe8; border-radius: 12px; text-decoration: none; color: inherit; transition: all 0.2s;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; background: #fce7f3; display: flex; justify-content: center; align-items: center; color: #db2777; font-size: 1.2rem;">
                                <?php if (!empty($s['foto'])): ?>
                                    <img src="/public/uploads/siswa/<?= htmlspecialchars($s['foto']) ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.outerHTML='<i class=\'fa-solid fa-user\'></i>';">
                                <?php else: ?>
                                    <i class="fa-solid fa-user"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div style="font-weight: 700; font-size: 0.95rem; color: #831843;"><?= htmlspecialchars($s['nama']) ?></div>
                                <div style="font-size: 0.75rem; color: #be185d; margin-top: 2px;">NIS: <?= htmlspecialchars($s['nis']) ?> &bull; <?= htmlspecialchars($s['nama_kelas']) ?></div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right" style="color: #f472b6; font-size: 0.9rem;"></i>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <a href="guru_dashboard.php" class="btn" style="display: block; text-align: center; background: #f1f5f9; color: #475569; padding: 12px; border-radius: 50px; font-weight: 600; text-decoration: none; margin-top: 20px; margin-bottom: 20px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
    </a>
</div>

<?php include 'footer_guru.php'; ?>
