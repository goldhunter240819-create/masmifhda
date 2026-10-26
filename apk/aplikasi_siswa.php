<?php
require_once 'koneksi.php';

if (!isset($_SESSION['siswa_id'])) {
    header("Location: login.php");
    exit;
}

$page_title = 'Aplikasi Siswa';

// Cek apakah punya Guru Wali (Mentor)
$stmt_gw = $core_db->prepare("SELECT COUNT(*) FROM guru_wali_siswa WHERE siswa_id = ?");
$stmt_gw->execute([$_SESSION['siswa_id']]);
$punya_mentor = $stmt_gw->fetchColumn() > 0;

include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
    <div class="header-top">
        <div class="greeting">
            <p>Fitur Utama</p>
            <h1>Aplikasi Siswa</h1>
        </div>
        <div class="avatar" style="color: #0ea5e9; background: white;">
            <i class="fa-solid fa-layer-group"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10;">
        <h3 class="section-title" style="margin-top: 0; margin-bottom: 15px;">Daftar Layanan</h3>
        
        <div style="margin-bottom: 24px;">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
                <!-- Keuangan -->
                <a href="tagihan.php" style="display: flex; flex-direction: column; align-items: center; justify-content: center; background: white; padding: 15px 10px; border-radius: 16px; text-decoration: none; color: inherit; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e2e8f0; text-align: center; gap: 8px;">
                    <div style="width: 45px; height: 45px; border-radius: 14px; background: #fee2e2; color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                    <h3 style="margin: 0; font-size: 0.75rem; color: #1e293b; font-weight: 700; line-height: 1.2;">Keuangan</h3>
                </a>
                
                <!-- Absensi -->
                <a href="absen.php" style="display: flex; flex-direction: column; align-items: center; justify-content: center; background: white; padding: 15px 10px; border-radius: 16px; text-decoration: none; color: inherit; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e2e8f0; text-align: center; gap: 8px;">
                    <div style="width: 45px; height: 45px; border-radius: 14px; background: #ecfdf5; color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <h3 style="margin: 0; font-size: 0.75rem; color: #1e293b; font-weight: 700; line-height: 1.2;">Absensi</h3>
                </a>
                
                <!-- Tabungan -->
                <a href="tabungan.php" style="display: flex; flex-direction: column; align-items: center; justify-content: center; background: white; padding: 15px 10px; border-radius: 16px; text-decoration: none; color: inherit; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e2e8f0; text-align: center; gap: 8px;">
                    <div style="width: 45px; height: 45px; border-radius: 14px; background: #fffbeb; color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <h3 style="margin: 0; font-size: 0.75rem; color: #1e293b; font-weight: 700; line-height: 1.2;">Tabungan</h3>
                </a>
                
                
                <!-- Chat Wali Kelas -->
                <a href="chat_siswa.php" style="display: flex; flex-direction: column; align-items: center; justify-content: center; background: white; padding: 15px 10px; border-radius: 16px; text-decoration: none; color: inherit; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e2e8f0; text-align: center; gap: 8px; position: relative;">
                    <div style="width: 45px; height: 45px; border-radius: 14px; background: #ecfdf5; color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 20px; position: relative;">
                        <i class="fa-solid fa-comment-dots"></i>
                        <div class="fab-badge chat-badge-indicator" style="display: none; position: absolute; top: -5px; right: -5px; font-size: 0.55rem; min-width: 14px; height: 14px; padding: 0 3px; border: 1px solid white;">0</div>
                    </div>
                    <h3 style="margin: 0; font-size: 0.75rem; color: #1e293b; font-weight: 700; line-height: 1.2;">Chat Wali Kelas</h3>
                </a>

                <?php if ($punya_mentor): ?>
                <!-- Chat Guru Wali (Mentor) -->
                <a href="chat_guru_wali.php" style="display: flex; flex-direction: column; align-items: center; justify-content: center; background: white; padding: 15px 10px; border-radius: 16px; text-decoration: none; color: inherit; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e2e8f0; text-align: center; gap: 8px; position: relative;">
                    <div style="width: 45px; height: 45px; border-radius: 14px; background: #fdf2f8; color: #ec4899; display: flex; align-items: center; justify-content: center; font-size: 20px; position: relative;">
                        <i class="fa-solid fa-user-tie"></i>
                        <div class="fab-badge chat-badge-indicator-mentor" style="display: none; position: absolute; top: -5px; right: -5px; font-size: 0.55rem; min-width: 14px; height: 14px; padding: 0 3px; border: 1px solid white;">0</div>
                    </div>
                    <h3 style="margin: 0; font-size: 0.75rem; color: #1e293b; font-weight: 700; line-height: 1.2;">Chat Mentor</h3>
                </a>
                <?php endif; ?>

                <!-- Perpus Digital -->
                <a href="https://e-library.masmifhda.sch.id/" style="display: flex; flex-direction: column; align-items: center; justify-content: center; background: white; padding: 15px 10px; border-radius: 16px; text-decoration: none; color: inherit; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e2e8f0; text-align: center; gap: 8px;">
                    <div style="width: 45px; height: 45px; border-radius: 14px; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-book-open-reader"></i>
                    </div>
                    <h3 style="margin: 0; font-size: 0.75rem; color: #1e293b; font-weight: 700; line-height: 1.2;">Perpus Digital</h3>
                </a>

            </div>
        </div>
        
    </div>
</div>

<?php include 'footer.php'; ?>
