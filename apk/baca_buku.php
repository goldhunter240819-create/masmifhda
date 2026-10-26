<?php
// baca_buku.php
require_once 'koneksi.php';

if (!isset($_GET['id'])) {
    die("ID Buku tidak valid.");
}

$id = intval($_GET['id']);
$stmt = $core_db->prepare("SELECT * FROM perpus_ebook_v2 WHERE id = ?");
$stmt->execute([$id]);
$buku = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$buku) {
    die("Buku tidak ditemukan.");
}

// Tambah statistik view
$core_db->query("UPDATE perpus_ebook_v2 SET unduhan = unduhan + 1 WHERE id = $id");

$page_title = $buku['judul'];
include 'header.php';
?>

<style>
.viewer-container {
    height: calc(100vh - 120px);
    width: 100%;
    border-radius: 16px;
    overflow: hidden;
    background: #000;
}
iframe {
    width: 100%;
    height: 100%;
    border: none;
}
/* Sembunyikan bottom-nav bawaan header/footer apk jika publik */
.bottom-nav {
    display: none !important;
}
</style>

<div class="header" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); padding-bottom: 2rem;">
    <div class="header-top">
        <a href="perpus_digital.php" style="width: 45px; height: 45px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div class="greeting" style="flex-grow: 1; margin-left: 15px; text-align: right;">
            <p style="margin:0; opacity:0.9;">Membaca Buku</p>
            <h1 style="font-size: 1.1rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 250px; float: right; margin:0; line-height:1.2;"><?= htmlspecialchars($buku['judul']) ?></h1>
        </div>
    </div>
</div>

<div class="main-content" style="padding: 0; background: #000;">
    <div class="viewer-container" style="margin-top: -1.5rem; position: relative; z-index: 10; border-radius: 20px 20px 0 0;">
        <?php 
        $pdf_path = '/public/uploads/buku/' . $buku['file_pdf'];
        ?>
        <iframe src="<?= htmlspecialchars($pdf_path) ?>"></iframe>
    </div>
</div>

<!-- Footer Kosong karena PDF Viewer full height -->
</div> <!-- end mobile-wrapper -->
</body>
</html>
