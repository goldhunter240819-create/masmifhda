<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];

// Cek apakah guru ini adalah Wali Kelas
$stmt_wk = $core_db->prepare("SELECT id, keterangan as nama_kelas FROM guru_tugas WHERE guru_id = :gid AND tugas = 'Wali Kelas' LIMIT 1");
$stmt_wk->execute(['gid' => $guru_id]);
$wali_kelas = $stmt_wk->fetch(PDO::FETCH_ASSOC);

if (!$wali_kelas) {
    header("Location: akses_ditolak.php");
    exit;
}

$nama_kelas_wk = $wali_kelas['nama_kelas'];

$page_title = 'Wali Kelas - ' . htmlspecialchars($nama_kelas_wk);
include 'header.php';

// Ambil daftar siswa
$stmt_siswa = $core_db->prepare("
    SELECT s.id, s.nama, s.nis, s.foto, k.nama_kelas 
    FROM siswa s 
    JOIN kelas k ON s.kelas_id = k.id 
    JOIN guru_tugas gt ON (gt.keterangan = CONCAT('Kelas ', k.nama_kelas) OR gt.keterangan = k.nama_kelas) 
    WHERE gt.guru_id = :gid AND gt.tugas = 'Wali Kelas' AND s.status = 'Aktif'
    ORDER BY s.nama ASC
");
$stmt_siswa->execute(['gid' => $guru_id]);
$siswa_list = $stmt_siswa->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="header" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); padding-top: 3.5rem;">
    <div class="header-top">
        <div class="greeting">
            <p>Daftar Siswa</p>
            <h1><?= htmlspecialchars($nama_kelas_wk) ?></h1>
        </div>
        <div class="avatar" style="color: #8b5cf6; background: white;">
            <i class="fa-solid fa-users"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10;">
        <h3 class="section-title" style="margin-top: 0; margin-bottom: 15px;"><i class="fa-solid fa-clipboard-user" style="color: #8b5cf6; margin-right: 5px;"></i> Pilih Siswa</h3>
        
        <?php if (empty($siswa_list)): ?>
            <div style="text-align: center; padding: 20px; color: #64748b;">
                <i class="fa-solid fa-users-slash" style="font-size: 2rem; opacity: 0.3; margin-bottom: 10px; display: block;"></i>
                <p style="font-weight: 600; font-size: 0.9rem;">Belum ada siswa di kelas ini.</p>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <?php foreach ($siswa_list as $s): ?>
                    <a href="wali_kelas_detail.php?siswa_id=<?= $s['id'] ?>" style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; text-decoration: none; color: inherit; transition: all 0.2s;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; background: #e2e8f0; display: flex; justify-content: center; align-items: center; color: #94a3b8; font-size: 1.2rem;">
                                <?php if (!empty($s['foto'])): ?>
                                    <img src="/public/uploads/siswa/<?= htmlspecialchars($s['foto']) ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.outerHTML='<i class=\'fa-solid fa-user\'></i>';">
                                <?php else: ?>
                                    <i class="fa-solid fa-user"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div style="font-weight: 700; font-size: 0.95rem; color: #1e293b;"><?= htmlspecialchars($s['nama']) ?></div>
                                <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">NIS: <?= htmlspecialchars($s['nis']) ?></div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right" style="color: #cbd5e1; font-size: 0.9rem;"></i>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <a href="wali_kelas.php" class="btn" style="display: block; text-align: center; background: #f1f5f9; color: #475569; padding: 12px; border-radius: 50px; font-weight: 600; text-decoration: none; margin-bottom: 20px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Menu Wali Kelas
    </a>
</div>

<?php include 'footer_guru.php'; ?>
