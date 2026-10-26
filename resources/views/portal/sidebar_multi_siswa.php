    <!-- â”€â”€ FLOATING SIDEBAR â”€â”€ -->
    <aside class="z-sidebar" id="zSidebar">
        <div class="z-sidebar-logo">
            <div class="logo-icon">
                <img src="<?php echo \App\Core\Branding::getInstitusiLogo(); ?>" alt="Logo Institusi" style="width:34px;height:34px;object-fit:contain;border-radius:6px;">
            </div>
            <div>
                <div class="logo-text">Aula System</div>
                <div class="logo-sub">Portal Siswa</div>
            </div>
        </div>

        <div class="z-sidebar-scroll">
            <?php include __DIR__ . '/../partials/academic_year_widget.php'; ?>
            <div class="z-nav-cat">Menu Utama</div>
            <a href="<?php echo base_url('siswa/dashboard'); ?>" class="z-nav-item active">
                <i data-lucide="layout-dashboard"></i> Dashboard
            </a>
            <a href="<?php echo base_url('siswa/profil-siswa'); ?>" class="z-nav-item">
                <i data-lucide="user"></i> Profil Saya
            </a>

            <div class="z-nav-cat">Aplikasi</div>
            <a href="<?php echo base_url('portal/ghcbt-siswa'); ?>" class="z-nav-item">
                <i data-lucide="monitor-play"></i> GHCBT System
            </a>
        </div>

        <div class="z-sidebar-user">
            <div class="z-sidebar-av"><?php echo $initial; ?></div>
            <div class="z-sidebar-info">
                <div class="sn"><?php echo $nama; ?></div>
                <div class="sr">Siswa</div>
            </div>
            <a href="<?php echo base_url('logout'); ?>" class="z-sidebar-logout" title="Logout">
                <i data-lucide="log-out"></i>
            </a>
        </div>
    </aside>
    <div class="z-overlay" id="zOverlay" onclick="document.getElementById('zSidebar').classList.remove('open');this.classList.remove('open');"></div>

