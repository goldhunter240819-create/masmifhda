<?php
$db = \App\Core\Database::connect('core');
$inst = $db->query("SELECT nama FROM institusi LIMIT 1")->fetch();
$nama_institusi = $inst ? strtoupper($inst['nama']) : 'NAMA INSTITUSI';
$_sidebar_logo = \App\Core\Branding::getInstitusiLogo();
$_s_uri = $_SERVER['REQUEST_URI'];
$_s_nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'Pengguna');
$_s_init = strtoupper(substr($_s_nama, 0, 2));
?>
<!-- PIP Sidebar (Zamrud Theme - sama dengan Siakad) -->
<aside class="z-sidebar" id="zSidebar">

    <!-- Brand -->
    <div class="z-brand">
        <div class="z-logo-box">
            <img src="<?php echo $_sidebar_logo; ?>" alt="Logo Institusi" style="width:100%;height:100%;object-fit:contain;">
        </div>
        <div>
            <div style="font-weight:900;font-size:1rem;color:#fff;letter-spacing:-.5px;line-height:1.1;"><?php echo htmlspecialchars($nama_institusi); ?></div>
            <div style="font-size:.62rem;color:#0ea5e9;font-weight:800;text-transform:uppercase;letter-spacing:1.5px;">ADMIN PIP</div>
        </div>
        <button class="z-close-btn" onclick="zToggleSidebar()"><i data-lucide="x"></i></button>
    </div>

    <!-- Nav -->
    <nav class="z-nav" style="padding: 1rem;">
        <div class="z-nav-label">Menu Utama</div>
        
        <a href="<?php echo base_url('admin/pip/dashboard'); ?>" class="z-nav-item <?php echo strpos($_s_uri, '/dashboard') !== false ? 'active' : ''; ?>">
            <i data-lucide="layout-dashboard"></i> Dashboard PIP
        </a>
        
        <a href="<?php echo base_url('admin/pip/konfirmasi'); ?>" class="z-nav-item <?php echo strpos($_s_uri, '/konfirmasi') !== false ? 'active' : ''; ?>">
            <i data-lucide="check-square"></i> Data Konfirmasi
        </a>

        <a href="<?php echo base_url('admin/pip/kehilangan'); ?>" class="z-nav-item <?php echo strpos($_s_uri, '/kehilangan') !== false ? 'active' : ''; ?>">
            <i data-lucide="file-warning"></i> Surat Kehilangan
        </a>

        <a href="<?php echo base_url('admin/pip/kuasa'); ?>" class="z-nav-item <?php echo strpos($_s_uri, '/kuasa') !== false ? 'active' : ''; ?>">
            <i data-lucide="file-key-2"></i> Surat Kuasa
        </a>

        <div class="z-nav-label" style="margin-top:.75rem;">Navigasi</div>
        
        <a href="<?php echo base_url('portal'); ?>" class="z-nav-item">
            <i data-lucide="home"></i> Portal Utama
        </a>
    </nav>
    
    <!-- User Card -->
    <div class="z-user-card">
        <div class="z-uc-av" style="background:linear-gradient(135deg,#0ea5e9,#0284c7);color:#fff;">
            <?php echo $_s_init; ?>
        </div>
        <div class="z-uc-info">
            <div class="z-uc-name"><?php echo $_s_nama; ?></div>
            <div class="z-uc-role">Administrator</div>
        </div>
        <a href="<?php echo base_url('logout'); ?>"
           style="background:rgba(239,68,68,.1);color:#f87171;width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;transition:.2s;flex-shrink:0;"
           onmouseover="this.style.background='rgba(239,68,68,.2)'"
           onmouseout="this.style.background='rgba(239,68,68,.1)'"
           title="Logout">
            <i data-lucide="log-out" style="width:15px;height:15px;"></i>
        </a>
    </div>
</aside>
