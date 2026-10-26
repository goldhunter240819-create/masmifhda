<?php include __DIR__ . '/_layout_top.php'; ?>

<!-- Header Banner -->
<div class="elite-page-header" style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);">
    <div class="header-bg-shapes"></div>
    <div class="header-content-inner">
        <div>
            <span class="header-badge" style="background: rgba(255,255,255,0.2); color: #fff; border-color: rgba(255,255,255,0.3);">
                MODUL OSIM MADRASAH
            </span>
            <div class="header-text-premium">
                <h1 style="color:#fff; font-size: 2rem; margin: 0;"><i data-lucide="users" style="color:#fff; vertical-align: middle;"></i> Dashboard OSIM</h1>
                <p style="color: rgba(255,255,255,0.85); margin-top: 10px; font-size: 1.05rem;">
                    Sistem informasi manajemen Organisasi Siswa Intra Madrasah (Kepengurusan, Proker, & E-Proposal).
                </p>
            </div>
        </div>
        <div class="header-actions-premium" style="display:flex; gap:12px; margin-top: 15px;">
            <a href="<?= base_url('osim/proposal') ?>" class="e-btn" style="background: #fff; color: #0f766e; box-shadow: 0 10px 20px rgba(0,0,0,0.15);">
                <i data-lucide="file-plus"></i> Buat Proposal
            </a>
            <a href="<?= base_url('osim/pengaturan') ?>" class="e-btn e-btn-glass" style="color:#fff; border-color: rgba(255,255,255,0.4);">
                <i data-lucide="settings"></i> Pengaturan
            </a>
        </div>
    </div>
</div>

<!-- Kartu Statistik -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <!-- Pengurus -->
    <div style="background: #fff; border-radius: 20px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 20px;">
        <div style="width: 60px; height: 60px; border-radius: 16px; background: #f0fdfa; color: #0d9488; display: flex; align-items: center; justify-content: center;">
            <i data-lucide="users" style="width: 30px; height: 30px;"></i>
        </div>
        <div>
            <div style="font-size: 0.85rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Total Pengurus</div>
            <div style="font-size: 2rem; font-weight: 900; color: #0f172a;"><?= number_format($stats['total_pengurus']) ?></div>
        </div>
    </div>

    <!-- Proker Aktif -->
    <div style="background: #fff; border-radius: 20px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 20px;">
        <div style="width: 60px; height: 60px; border-radius: 16px; background: #fffbeb; color: #d97706; display: flex; align-items: center; justify-content: center;">
            <i data-lucide="activity" style="width: 30px; height: 30px;"></i>
        </div>
        <div>
            <div style="font-size: 0.85rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Proker Berjalan</div>
            <div style="font-size: 2rem; font-weight: 900; color: #0f172a;"><?= number_format($stats['proker_aktif']) ?></div>
        </div>
    </div>

    <!-- Proker Selesai -->
    <div style="background: #fff; border-radius: 20px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 20px;">
        <div style="width: 60px; height: 60px; border-radius: 16px; background: #f0fdf4; color: #16a34a; display: flex; align-items: center; justify-content: center;">
            <i data-lucide="check-circle" style="width: 30px; height: 30px;"></i>
        </div>
        <div>
            <div style="font-size: 0.85rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Proker Selesai</div>
            <div style="font-size: 2rem; font-weight: 900; color: #0f172a;"><?= number_format($stats['proker_selesai']) ?></div>
        </div>
    </div>
</div>

<!-- Main Modules Menu -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
    
    <a href="<?= base_url('osim/pengurus') ?>" style="background: #fff; padding: 25px; border-radius: 20px; text-decoration: none; border: 1px solid #e2e8f0; transition: 0.3s; display: block;" onmouseover="this.style.borderColor='#0d9488'; this.style.boxShadow='0 10px 25px rgba(13,148,136,0.1)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
        <div style="width: 50px; height: 50px; background: #f0fdfa; color: #0d9488; border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
            <i data-lucide="contact" style="width: 24px; height: 24px;"></i>
        </div>
        <h3 style="margin: 0 0 8px 0; color: #1e293b; font-size: 1.1rem;">Data Kepengurusan</h3>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; line-height: 1.5;">Kelola struktur organisasi, anggota OSIM, dan cetak kartu pengurus.</p>
    </a>

    <a href="<?= base_url('osim/proker') ?>" style="background: #fff; padding: 25px; border-radius: 20px; text-decoration: none; border: 1px solid #e2e8f0; transition: 0.3s; display: block;" onmouseover="this.style.borderColor='#0ea5e9'; this.style.boxShadow='0 10px 25px rgba(14,165,233,0.1)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
        <div style="width: 50px; height: 50px; background: #f0f9ff; color: #0ea5e9; border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
            <i data-lucide="briefcase" style="width: 24px; height: 24px;"></i>
        </div>
        <h3 style="margin: 0 0 8px 0; color: #1e293b; font-size: 1.1rem;">Program Kerja</h3>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; line-height: 1.5;">Tracking pelaksanaan program kerja setiap divisi OSIM.</p>
    </a>

    <a href="<?= base_url('osim/proposal') ?>" style="background: #fff; padding: 25px; border-radius: 20px; text-decoration: none; border: 1px solid #e2e8f0; transition: 0.3s; display: block;" onmouseover="this.style.borderColor='#8b5cf6'; this.style.boxShadow='0 10px 25px rgba(139,92,246,0.1)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
        <div style="width: 50px; height: 50px; background: #f5f3ff; color: #8b5cf6; border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
            <i data-lucide="file-text" style="width: 24px; height: 24px;"></i>
        </div>
        <h3 style="margin: 0 0 8px 0; color: #1e293b; font-size: 1.1rem;">E-Proposal & LPJ</h3>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; line-height: 1.5;">Generate proposal otomatis, approval pembina, dan arsip LPJ.</p>
    </a>

</div>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
