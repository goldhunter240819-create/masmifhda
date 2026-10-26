<?php
require_once 'koneksi.php';

$is_logged_in = isset($_SESSION['siswa_id']);
if (!$is_logged_in) {
    header("Location: login.php");
    exit;
}

$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saran'])) {
    // In real app, save to db
    // $stmt = $core_db->prepare("INSERT INTO kotak_saran (siswa_id, jenis, pesan, is_anonim) VALUES (...)");
    $success = true;
}

$page_title = 'Kotak Saran';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding-top: 3.5rem;">
    <div class="header-top">
        <a href="profil.php" style="color: white; font-size: 1.2rem; padding: 5px;"><i class="fa-solid fa-arrow-left"></i></a>
        <div class="greeting" style="flex: 1; text-align: center; padding-right: 25px;">
            <p style="margin: 0; font-size: 0.85rem; opacity: 0.9;">Suara Wali Murid</p>
            <h1 style="margin: 0; font-size: 1.2rem; font-weight: 700;">Kotak Saran</h1>
        </div>
    </div>
</div>

<div class="main-content">
    <?php if ($success): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Terkirim!',
                text: 'Terima kasih atas masukan Anda. Saran Anda sangat berarti untuk kemajuan madrasah.',
                confirmButtonColor: '#f59e0b'
            }).then(() => {
                window.location.href = 'profil.php';
            });
        </script>
    <?php endif; ?>

    <div class="data-card" style="margin-top: -2.5rem; z-index: 10;">
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="width: 70px; height: 70px; background: #fffbeb; color: #f59e0b; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 15px;">
                <i class="fa-solid fa-envelope-open-text"></i>
            </div>
            <h2 style="font-size: 1.1rem; color: #1e293b; margin: 0;">Sampaikan Saran & Kritik</h2>
            <p style="font-size: 0.85rem; color: #64748b; margin: 5px 0 0 0;">Bantu kami menjadi lebih baik! Identitas Anda bisa disembunyikan (Anonim).</p>
        </div>

        <form method="POST" action="">
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 5px;">Kategori</label>
                <select name="kategori" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; color: #1e293b; outline: none; background: #f8fafc;" required>
                    <option value="Saran Akademik">📚 Saran Akademik / Pembelajaran</option>
                    <option value="Fasilitas Sekolah">🏢 Fasilitas & Gedung Sekolah</option>
                    <option value="Kritik & Keluhan">⚠️ Kritik & Keluhan Khusus</option>
                    <option value="Lainnya">💡 Ide & Lainnya</option>
                </select>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 5px;">Pesan Anda</label>
                <textarea name="saran" rows="5" placeholder="Tulis masukan, ide, atau keluhan Anda di sini dengan bahasa yang baik..." style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; color: #1e293b; outline: none; background: #f8fafc; resize: none;" required></textarea>
            </div>

            <div style="margin-bottom: 25px; display: flex; align-items: center; gap: 10px; background: #f1f5f9; padding: 10px 12px; border-radius: 8px;">
                <input type="checkbox" name="anonim" id="anonim" value="1" style="width: 18px; height: 18px; accent-color: #f59e0b;">
                <label for="anonim" style="font-size: 0.85rem; font-weight: 600; color: #475569; cursor: pointer; flex: 1;">
                    Kirim sebagai Anonim (Rahasiakan nama saya)
                </label>
            </div>

            <button type="submit" style="width: 100%; background: #f59e0b; color: white; border: none; padding: 14px; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 8px;">
                <i class="fa-solid fa-paper-plane"></i> Kirim Saran Sekarang
            </button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
