<?php
require_once 'koneksi.php';

$is_logged_in = isset($_SESSION['siswa_id']) || isset($_SESSION['guru_id']);
if (!$is_logged_in) {
    header("Location: login.php");
    exit;
}

$page_title = 'Perpustakaan Digital';
include 'header.php';

// Ambil data buku
$stmt_buku = $core_db->query("SELECT id, judul, deskripsi, cover_image, file_pdf FROM buku_bacaan ORDER BY id DESC");
$buku_list = $stmt_buku->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="header" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); padding-top: 3.5rem;">
    <div class="header-top">
        <a href="<?= isset($_SESSION['guru_id']) ? 'profil_guru.php' : 'profil.php' ?>" style="color: white; font-size: 1.2rem; padding: 5px;"><i class="fa-solid fa-arrow-left"></i></a>
        <div class="greeting" style="flex: 1; text-align: center; padding-right: 25px;">
            <p style="margin: 0; font-size: 0.85rem; opacity: 0.9;">E-Library</p>
            <h1 style="margin: 0; font-size: 1.2rem; font-weight: 700;">Perpustakaan Digital</h1>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10;">
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="width: 70px; height: 70px; background: #eff6ff; color: #3b82f6; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 15px;">
                <i class="fa-solid fa-book-open-reader"></i>
            </div>
            <h2 style="font-size: 1.1rem; color: #1e293b; margin: 0;">Koleksi Buku & Panduan</h2>
            <p style="font-size: 0.85rem; color: #64748b; margin: 5px 0 0 0;">Baca berbagai modul, panduan, dan buku menarik secara langsung dari HP.</p>
        </div>


        <?php if (empty($buku_list)): ?>
            <div style="text-align: center; padding: 20px; color: #64748b;">
                <i class="fa-solid fa-book-open" style="font-size: 2.5rem; opacity: 0.2; margin-bottom: 10px;"></i>
                <p>Belum ada buku yang tersedia saat ini.</p>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(1, 1fr); gap: 15px;">
                <?php foreach ($buku_list as $buku): ?>
                    <a href="pdf_viewer.php?id=<?= $buku['id'] ?>" style="display: flex; align-items: center; gap: 15px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; text-decoration: none; color: inherit;">
                        <div style="width: 60px; height: 80px; background: #e2e8f0; border-radius: 8px; overflow: hidden; flex-shrink: 0; display: flex; justify-content: center; align-items: center;">
                            <?php if (!empty($buku['cover_image'])): ?>
                                <img src="../public/uploads/buku/<?= htmlspecialchars($buku['cover_image']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <i class="fa-solid fa-file-pdf" style="font-size: 2rem; color: #94a3b8;"></i>
                            <?php endif; ?>
                        </div>
                        <div style="flex: 1;">
                            <h3 style="margin: 0 0 5px 0; font-size: 0.95rem; color: #0f172a; font-weight: 700; line-height: 1.3;"><?= htmlspecialchars($buku['judul']) ?></h3>
                            <p style="margin: 0; font-size: 0.75rem; color: #64748b; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= htmlspecialchars($buku['deskripsi']) ?></p>
                            <div style="margin-top: 8px; font-size: 0.7rem; font-weight: 700; color: #3b82f6;"><i class="fa-solid fa-book-open-reader" style="margin-right: 5px;"></i> Baca Sekarang</div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
    </div>
</div>

<?php 
if (isset($_SESSION['guru_id'])) {
    include 'footer_guru.php'; 
} else {
    include 'footer.php';
}
?>
