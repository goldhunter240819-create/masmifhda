<?php
$_s_uri = $_SERVER['REQUEST_URI'];
$sp_base = base_url('admin/apk-settings');

$_sidebar_logo = \App\Core\Branding::getInstitusiLogo();
?>
<!-- APK Settings Sidebar (Zamrud Theme) -->
<aside class="z-sidebar" id="zSidebar">
    <!-- Brand -->
    <div class="z-brand">
        <div class="z-logo-box">
            <img src="<?php echo $_sidebar_logo; ?>" alt="Logo Institusi" style="width:100%;height:100%;object-fit:contain;">
        </div>
        <div>
            <div style="font-weight:900;font-size:1rem;color:#fff;letter-spacing:-.5px;line-height:1.1;">Platform Digital</div>
            <div style="font-size:.62rem;color:#d946ef;font-weight:800;text-transform:uppercase;letter-spacing:1px;">PENGATURAN APK</div>
        </div>
        <button class="z-close-btn" onclick="zToggleSidebar()"><i data-lucide="x"></i></button>
    </div>

    <!-- Nav -->
    <div class="z-sidebar-scroll">
        <nav class="z-nav" style="padding: 1rem 0;">
            <div class="z-nav-label" style="margin-bottom: 0.5rem;">Pengaturan APK</div>

            <a href="<?php echo $sp_base; ?>" class="z-nav-item <?php echo ($_s_uri == $sp_base || $_s_uri == $sp_base . '/') ? 'active' : ''; ?>">
                <i data-lucide="layout-dashboard"></i> Dashboard
            </a>

            <a href="<?php echo $sp_base; ?>/aplikasi" class="z-nav-item <?php echo strpos($_s_uri, 'aplikasi') !== false ? 'active' : ''; ?>">
                <i data-lucide="smartphone"></i> Pengaturan Aplikasi
            </a>

            <a href="<?php echo $sp_base; ?>/fitur" class="z-nav-item <?php echo strpos($_s_uri, 'fitur') !== false ? 'active' : ''; ?>">
                <i data-lucide="toggle-right"></i> Pengaturan Fitur
            </a>
        </nav>
    </div>

    <!-- Foot -->
    <div class="z-sidebar-foot">
        <a href="<?php echo base_url('portal'); ?>" class="z-nav-item" style="background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.9); margin: 0; border: 1px solid rgba(255,255,255,0.1); justify-content: center;">
            <i data-lucide="arrow-left"></i> Ke Portal Hub
        </a>
    </div>
</aside>
