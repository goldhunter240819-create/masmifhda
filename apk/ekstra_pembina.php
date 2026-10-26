<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];

// Cek Ekstra yang dibina
$stmt = $core_db->prepare("SELECT id, keterangan as nama_ekstra, tahun_ajaran FROM guru_tugas WHERE guru_id = :gid AND tugas = 'Pembina Ekstrakurikuler' ORDER BY tahun_ajaran DESC");
$stmt->execute(['gid' => $guru_id]);
$ekstra_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($ekstra_list)) {
    header("Location: akses_ditolak.php");
    exit;
}

$page_title = 'Pembina Ekstrakurikuler';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%); padding-top: 3.5rem;">
    <div class="header-top">
        <div class="greeting">
            <p>Menu Pembina</p>
            <h1>Ekstrakurikuler</h1>
        </div>
        <div class="avatar" style="color: #10b981; background: white;">
            <i class="fa-solid fa-medal"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10;">
        <h3 class="section-title" style="margin-bottom: 15px;"><i class="fa-solid fa-list-check" style="color: #10b981; margin-right: 5px;"></i> Daftar Ekstrakurikuler</h3>
        
        <?php foreach ($ekstra_list as $ekstra): ?>
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <div>
                        <div style="font-weight: 800; font-size: 1.1rem; color: #1e293b;"><?= htmlspecialchars($ekstra['nama_ekstra']) ?></div>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 3px;">Tahun Ajaran: <?= htmlspecialchars($ekstra['tahun_ajaran']) ?></div>
                    </div>
                    <div style="width: 40px; height: 40px; background: #d1fae5; color: #059669; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 1.2rem;">
                        <i class="fa-solid fa-futbol"></i>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px;">
                    <a href="ekstra_anggota.php?ekstra=<?= urlencode($ekstra['nama_ekstra']) ?>&ta=<?= urlencode($ekstra['tahun_ajaran']) ?>" style="display: flex; flex-direction: column; align-items: center; justify-content: center; background: white; border: 1px solid #cbd5e1; padding: 8px 5px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; color: #475569; text-decoration: none;">
                        <i class="fa-solid fa-users" style="font-size: 1.2rem; margin-bottom: 3px; color: #3b82f6;"></i> Anggota
                    </a>
                    <a href="ekstra_jurnal.php?ekstra=<?= urlencode($ekstra['nama_ekstra']) ?>&ta=<?= urlencode($ekstra['tahun_ajaran']) ?>" style="display: flex; flex-direction: column; align-items: center; justify-content: center; background: white; border: 1px solid #cbd5e1; padding: 8px 5px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; color: #475569; text-decoration: none;">
                        <i class="fa-solid fa-book" style="font-size: 1.2rem; margin-bottom: 3px; color: #f59e0b;"></i> Jurnal
                    </a>
                    <a href="ekstra_nilai.php?ekstra=<?= urlencode($ekstra['nama_ekstra']) ?>&ta=<?= urlencode($ekstra['tahun_ajaran']) ?>" style="display: flex; flex-direction: column; align-items: center; justify-content: center; background: #10b981; border: 1px solid #059669; padding: 8px 5px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; color: white; text-decoration: none;">
                        <i class="fa-solid fa-star" style="font-size: 1.2rem; margin-bottom: 3px;"></i> Nilai
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <a href="guru_dashboard.php" class="btn" style="display: block; text-align: center; background: #f1f5f9; color: #475569; padding: 12px; border-radius: 50px; font-weight: 600; text-decoration: none; margin-bottom: 20px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
    </a>
</div>

<?php include 'footer_guru.php'; ?>
