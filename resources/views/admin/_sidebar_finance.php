<?php
// Shared Admin Finance Sidebar
// Requires: $adminNama (string), $active_page (string)
$active_page = isset($active_page) ? $active_page : 'overview';
$adminNama   = isset($adminNama)   ? $adminNama   : 'Administrator';
$adminInitial = strtoupper(substr($adminNama, 0, 2));
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <i data-lucide="shield-check"></i>
        <span>Aula System Admin</span>
    </div>
    <div class="sidebar-scroll">
        <?php include __DIR__ . '/../partials/academic_year_widget.php'; ?>
        <div class="nav-category">Navigasi</div>
        <a href="<?php echo base_url('portal'); ?>" class="nav-item">
            <i data-lucide="home"></i>
            <span>Portal Utama</span>
        </a>

        <div class="nav-category">Modul Keuangan</div>
        <?php if(in_array('all', $allowed_menus) || in_array('overview', $allowed_menus)): ?>
        <a href="<?php echo base_url('admin/finance'); ?>" class="nav-item <?php echo $active_page === 'overview'  ? 'active' : ''; ?>">
            <i data-lucide="bar-chart-2"></i>
            <span>Overview</span>
        </a>
        <?php endif; ?>
        <?php if(in_array('all', $allowed_menus) || in_array('tagihan', $allowed_menus)): ?>
        <a href="<?php echo base_url('admin/finance/tagihan'); ?>" class="nav-item <?php echo $active_page === 'tagihan' ? 'active' : ''; ?>">
            <i data-lucide="receipt"></i>
            <span>Tagihan Siswa</span>
        </a>
        <?php endif; ?>
        <?php if(in_array('all', $allowed_menus) || in_array('gaji', $allowed_menus)): ?>
        <a href="<?php echo base_url('admin/finance/gaji'); ?>" class="nav-item <?php echo $active_page === 'gaji'    ? 'active' : ''; ?>">
            <i data-lucide="banknote"></i>
            <span>Gaji &amp; Insentif</span>
        </a>
        <?php endif; ?>
        <?php if(in_array('all', $allowed_menus) || in_array('laporan', $allowed_menus)): ?>
        <a href="<?php echo base_url('admin/finance/laporan'); ?>" class="nav-item <?php echo $active_page === 'laporan' ? 'active' : ''; ?>">
            <i data-lucide="book-open"></i>
            <span>Laporan Kas</span>
        </a>
        <?php endif; ?>

        <?php if(in_array('all', $allowed_menus) || in_array('biaya', $allowed_menus) || in_array('akses_bendahara', $allowed_menus)): ?>
        <div class="nav-category">Pengaturan</div>
        <?php endif; ?>
        
        <?php if(in_array('all', $allowed_menus) || in_array('biaya', $allowed_menus)): ?>
        <a href="<?php echo base_url('admin/finance/biaya'); ?>" class="nav-item <?php echo $active_page === 'biaya'   ? 'active' : ''; ?>">
            <i data-lucide="sliders"></i>
            <span>Komponen Biaya</span>
        </a>
        <?php endif; ?>

        <?php if(in_array('all', $allowed_menus) || in_array('akses_bendahara', $allowed_menus)): ?>
        <a href="<?php echo base_url('admin/finance/akses'); ?>" class="nav-item <?php echo $active_page === 'akses_bendahara' ? 'active' : ''; ?>">
            <i data-lucide="shield-check"></i>
            <span>Akses Bendahara</span>
        </a>
        <?php endif; ?>

        <div class="nav-category">Akun</div>
        <a href="<?php echo base_url('logout'); ?>" class="nav-item" style="color: #fca5a5 !important;">
            <i data-lucide="log-out"></i>
            <span>Keluar</span>
        </a>
    </div>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

