<?php
$page_title = 'Dashboard';
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
ob_start();
?>
<div class="elite-page-header" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
    <div class="header-bg-shapes"></div>
    <div class="header-content-inner">
        <div class="header-text-premium">
            <div class="header-badge" style="display:inline-block; background:rgba(255,255,255,0.2); border:1px solid rgba(255,255,255,0.3); color:white; padding:4px 12px; border-radius:100px; font-size:0.7rem; font-weight:800; margin-bottom:10px;">APK SETTINGS</div>
            <h1><i data-lucide="layout-dashboard"></i> Selamat Datang, <?php echo $nama; ?>! 👋</h1>
            <p>Pusat kontrol konfigurasi Aplikasi Mobile Siswa dan Guru. Sesuaikan tampilan dan pengalaman pengguna secara terpusat.</p>
        </div>
        <div class="header-actions-premium">
            <a href="<?php echo base_url('admin/apk-settings/aplikasi'); ?>" class="e-btn e-btn-primary" style="color: #4f46e5;">
                <i data-lucide="settings"></i> Mulai Konfigurasi
            </a>
        </div>
    </div>
</div>

<div class="z-grid2" style="gap:1.5rem;">
    <div class="z-panel" style="padding: 1.5rem; text-align: center;">
        <i data-lucide="smartphone" style="width: 48px; height: 48px; color: #8b5cf6; margin-bottom: 1rem;"></i>
        <h3 style="margin-top:0; font-weight: 800; color: #0f172a;">Aplikasi Mobile Siap</h3>
        <p style="color: #64748b; font-size: 0.85rem; line-height: 1.5;">Pengaturan tema dan delegasi fitur yang diterapkan di sini akan otomatis mensinkronisasi tampilan di aplikasi Android dan iOS pengguna.</p>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
