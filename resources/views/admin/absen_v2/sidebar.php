<?php
$_s_uri = $_SERVER['REQUEST_URI'];

function is_active($uri_fragment) {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $uri = str_replace('_', '-', $uri); // Normalize underscores to hyphens just in case
    $fragment = str_replace('_', '-', $uri_fragment);
    return strpos($uri, $fragment) !== false;
}

$is_siswa_open = is_active('admin/absen-v2/rekap') || (is_active('admin/absen-v2/ketidakhadiran') && !is_active('guru'));
$is_guru_open = is_active('admin/absen-guru-v2/rekap') || is_active('admin/absen-guru-v2/ketidakhadiran') || is_active('admin/absen-guru-v2/input-manual') || is_active('absen-guru-v2/scanner');
$is_mesin_open = is_active('admin/absen-v2/pengaturan') || is_active('admin/absen-v2/akses-scanner');
?>

<aside class="z-sidebar" id="zSidebar">
    <!-- Brand -->
    <div class="z-sidebar-logo">
        <div class="logo-icon" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
            <i data-lucide="qr-code"></i>
        </div>
        <div>
            <div class="logo-text">Absen V2</div>
            <div class="logo-sub" style="color:#0ea5e9;">Portal Admin</div>
        </div>
        <button class="z-close-btn" onclick="zToggleSidebar()" style="display:none;background:none;border:none;color:white;position:absolute;right:1rem;"><i data-lucide="x"></i></button>
    </div>

    <!-- Nav -->
    <div class="z-sidebar-scroll">
        <nav class="z-nav">
        <div style="padding: 1rem 1rem 0 1rem;">
            <?php include __DIR__ . '/../../partials/academic_year_widget.php'; ?>
        </div>
        
        <div class="z-nav-label">Menu Utama</div>
        
        <a href="<?php echo base_url('admin/absen_v2'); ?>" class="z-nav-item <?php echo ($_s_uri == base_url('admin/absen_v2') || $_s_uri == base_url('admin/absen_guru_v2')) ? 'active' : ''; ?>">
            <i data-lucide="layout-dashboard"></i> Dashboard Absensi
        </a>
        
        <!-- Dropdown Siswa -->
        <div class="z-nav-dropdown <?php echo $is_siswa_open ? 'open' : ''; ?>">
            <div class="z-nav-dropdown-toggle <?php echo $is_siswa_open ? 'active' : ''; ?>" onclick="var p = this.parentElement; document.querySelectorAll('.z-nav-dropdown').forEach(function(d){ if(d !== p) d.classList.remove('open'); }); p.classList.toggle('open');">
                <div class="dt-left">
                    <i data-lucide="users"></i>
                    Absensi Siswa
                </div>
                <i data-lucide="chevron-down" class="dt-icon"></i>
            </div>
            <div class="z-nav-dropdown-menu">
                <a href="<?php echo base_url('admin/absen_v2/rekap-harian'); ?>" class="z-nav-item <?php echo is_active('admin/absen_v2/rekap-harian') ? 'active' : ''; ?>">
                    <i data-lucide="calendar"></i> Rekap Harian Siswa
                </a>
                
                <a href="<?php echo base_url('admin/absen_v2/rekap-bulanan'); ?>" class="z-nav-item <?php echo is_active('admin/absen_v2/rekap-bulanan') ? 'active' : ''; ?>">
                    <i data-lucide="file-spreadsheet"></i> Rekap Bulanan Siswa
                </a>
                
                <a href="<?php echo base_url('admin/absen_v2/rekap-detail'); ?>" class="z-nav-item <?php echo is_active('admin/absen_v2/rekap-detail') ? 'active' : ''; ?>">
                    <i data-lucide="git-merge"></i> Rekap Detail (Timeline)
                </a>
                
                <a href="<?php echo base_url('admin/absen_v2/ketidakhadiran'); ?>" class="z-nav-item <?php echo is_active('admin/absen_v2/ketidakhadiran') && !is_active('guru') ? 'active' : ''; ?>">
                    <i data-lucide="alert-triangle"></i> Data Ketidakhadiran
                </a>
            </div>
        </div>

        <!-- Dropdown Guru -->
        <div class="z-nav-dropdown <?php echo $is_guru_open ? 'open' : ''; ?>">
            <div class="z-nav-dropdown-toggle <?php echo $is_guru_open ? 'active' : ''; ?>" onclick="var p = this.parentElement; document.querySelectorAll('.z-nav-dropdown').forEach(function(d){ if(d !== p) d.classList.remove('open'); }); p.classList.toggle('open');">
                <div class="dt-left">
                    <i data-lucide="briefcase"></i>
                    Absensi Guru
                </div>
                <i data-lucide="chevron-down" class="dt-icon"></i>
            </div>
            <div class="z-nav-dropdown-menu">
                <a href="<?php echo base_url('admin/absen_guru_v2/rekap-harian'); ?>" class="z-nav-item <?php echo is_active('absen_guru_v2/rekap-harian') ? 'active' : ''; ?>">
                    <i data-lucide="calendar"></i> Rekap Harian Guru
                </a>
                
                <a href="<?php echo base_url('admin/absen_guru_v2/rekap-bulanan'); ?>" class="z-nav-item <?php echo is_active('absen_guru_v2/rekap-bulanan') ? 'active' : ''; ?>">
                    <i data-lucide="file-spreadsheet"></i> Rekap Bulanan Guru
                </a>
                
                <a href="<?php echo base_url('admin/absen_guru_v2/input-manual'); ?>" class="z-nav-item <?php echo is_active('absen_guru_v2/input-manual') ? 'active' : ''; ?>">
                    <i data-lucide="clipboard-edit"></i> Input Manual Guru
                </a>
            </div>
        </div>

        <!-- Dropdown Pengaturan -->
        <div class="z-nav-dropdown <?php echo $is_mesin_open ? 'open' : ''; ?>">
            <div class="z-nav-dropdown-toggle <?php echo $is_mesin_open ? 'active' : ''; ?>" onclick="var p = this.parentElement; document.querySelectorAll('.z-nav-dropdown').forEach(function(d){ if(d !== p) d.classList.remove('open'); }); p.classList.toggle('open');">
                <div class="dt-left">
                    <i data-lucide="settings"></i>
                    Pengaturan Mesin
                </div>
                <i data-lucide="chevron-down" class="dt-icon"></i>
            </div>
            <div class="z-nav-dropdown-menu">
                <a href="<?php echo base_url('admin/absen_v2/pengaturan'); ?>" class="z-nav-item <?php echo (is_active('pengaturan') && is_active('absen_v2')) ? 'active' : ''; ?>">
                    <i data-lucide="clock"></i> Konfigurasi Jam
                </a>
                
                <a href="<?php echo base_url('admin/absen_v2/akses-scanner'); ?>" class="z-nav-item <?php echo is_active('akses-scanner') ? 'active' : ''; ?>">
                    <i data-lucide="shield-check"></i> Akses Scanner
                </a>
            </div>
        </div>
        
        </nav>
    </div>
</aside>
