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

$page_title = 'Buku Wali Kelas';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); padding-top: 3.5rem;">
    <div class="header-top">
        <div class="greeting">
            <p>Buku Wali Kelas</p>
            <h1><?= htmlspecialchars($nama_kelas_wk) ?></h1>
        </div>
        <div class="avatar" style="color: #8b5cf6; background: white;">
            <i class="fa-solid fa-book-open"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10;">
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
            
            <a href="wali_kelas_siswa.php" style="display: flex; flex-direction: column; align-items: center; justify-content: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 10px; text-decoration: none; color: inherit; transition: all 0.2s;">
                <div style="width: 50px; height: 50px; background: #e0e7ff; color: #4f46e5; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 1.5rem; margin-bottom: 10px;">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div style="font-weight: 700; font-size: 0.9rem; text-align: center; color: #1e293b;">Data Siswa</div>
                <div style="font-size: 0.7rem; color: #64748b; text-align: center; margin-top: 5px;">Biodata & Jurnal Anak</div>
            </a>

            <a href="wali_kelas_jurnal.php" style="display: flex; flex-direction: column; align-items: center; justify-content: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 10px; text-decoration: none; color: inherit; transition: all 0.2s;">
                <div style="width: 50px; height: 50px; background: #fef3c7; color: #d97706; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 1.5rem; margin-bottom: 10px;">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
                <div style="font-weight: 700; font-size: 0.9rem; text-align: center; color: #1e293b;">Jurnal Kelas</div>
                <div style="font-size: 0.7rem; color: #64748b; text-align: center; margin-top: 5px;">Kejadian & Tindak Lanjut</div>
            </a>

            <a href="wali_kelas_absen.php" style="display: flex; flex-direction: column; align-items: center; justify-content: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 10px; text-decoration: none; color: inherit; transition: all 0.2s;">
                <div style="width: 50px; height: 50px; background: #dcfce7; color: #16a34a; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 1.5rem; margin-bottom: 10px;">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div style="font-weight: 700; font-size: 0.9rem; text-align: center; color: #1e293b;">Rekap Absensi</div>
                <div style="font-size: 0.7rem; color: #64748b; text-align: center; margin-top: 5px;">Pantau Kehadiran Kelas</div>
            </a>

            <a href="wali_kelas_program.php" style="display: flex; flex-direction: column; align-items: center; justify-content: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 10px; text-decoration: none; color: inherit; transition: all 0.2s;">
                <div style="width: 50px; height: 50px; background: #e0f2fe; color: #0284c7; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 1.5rem; margin-bottom: 10px;">
                    <i class="fa-solid fa-list-check"></i>
                </div>
                <div style="font-weight: 700; font-size: 0.9rem; text-align: center; color: #1e293b;">Program Kelas</div>
                <div style="font-size: 0.7rem; color: #64748b; text-align: center; margin-top: 5px;">Pengaturan & Cetak</div>
            </a>

        </div>

    </div>
    
    <a href="guru_dashboard.php" class="btn" style="display: block; text-align: center; background: #f1f5f9; color: #475569; padding: 12px; border-radius: 50px; font-weight: 600; text-decoration: none; margin-bottom: 20px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
    </a>
</div>

<?php include 'footer_guru.php'; ?>
