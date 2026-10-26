<?php
$_sidebar_logo = \App\Core\Branding::getInstitusiLogo();
?>
<aside class="z-sidebar" id="zSidebar">
    <!-- Brand Area -->
    <div class="z-sidebar-logo" style="padding: 1.5rem 1rem;">
        <div class="logo-icon" style="background: white; overflow: hidden; padding: 2px;">
            <img src="<?php echo $_sidebar_logo; ?>" alt="Logo Institusi" style="width:100%;height:100%;object-fit:contain;">
        </div>
        <div>
            <div class="logo-text">MA Miftahul Huda</div>
            <div class="logo-sub" style="color:#0ea5e9;">BANK SOAL</div>
        </div>
        <button class="z-close-btn" onclick="zToggleSidebar()" style="display:none;background:none;border:none;color:white;position:absolute;right:1rem;"><i data-lucide="x"></i></button>
    </div>

    <!-- Scrollable Nav Area -->
    <div class="z-sidebar-scroll">
        <nav class="z-nav" style="padding: 0.5rem 0.75rem;">
            <?php include __DIR__ . '/../partials/academic_year_widget.php'; ?>
            <a href="<?php echo base_url('bank_soal'); ?>" class="z-nav-item <?php echo ($active_page == 'dashboard') ? 'active' : ''; ?>">
                <i data-lucide="layout-dashboard"></i> Dashboard
            </a>

            <div class="z-nav-label" style="font-size: 0.65rem; font-weight: 800; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 1px; margin: 15px 15px 5px 15px;">Manajemen</div>
            
            <a href="<?php echo base_url('bank_soal/topik'); ?>" class="z-nav-item <?php echo ($active_page == 'topik') ? 'active' : ''; ?>">
                <i data-lucide="library"></i> Topik Ujian
            </a>
            
            <a href="<?php echo base_url('bank_soal/kelola_soal'); ?>" class="z-nav-item <?php echo ($active_page == 'kelola_soal') ? 'active' : ''; ?>">
                <i data-lucide="file-question"></i> Kelola Soal
            </a>
        </nav>
    </div>

    <!-- Footer Action Area -->
    <div class="z-sidebar-foot">
        <div style="padding: 10px; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; text-align: center;">
            <div style="font-size: 0.7rem; color: rgba(255,255,255,0.6); margin-bottom: 5px;">Bank Soal v1.0</div>
        </div>
    </div>
</aside>
