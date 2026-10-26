    <!-- â”€â”€ FLOATING SIDEBAR â”€â”€ -->
    <aside class="z-sidebar" id="zSidebar">
        <div class="z-sidebar-logo">
            <div class="logo-icon">
                <img src="<?php echo \App\Core\Branding::getInstitusiLogo(); ?>" alt="Logo Institusi" style="width:34px;height:34px;object-fit:contain;border-radius:6px;">
            </div>
            <div>
                <div class="logo-text">Aula System</div>
                <div class="logo-sub">Portal Terpadu</div>
            </div>
        </div>

        <div class="z-sidebar-scroll">
            <?php include __DIR__ . '/../partials/academic_year_widget.php'; ?>
            <div class="z-nav-cat">Menu Utama</div>
            <a href="<?php echo base_url(''); ?>" class="z-nav-item active">
                <i data-lucide="layout-dashboard"></i> Dashboard
            </a>
            <a href="<?php echo $profileUrl; ?>" class="z-nav-item">
                <i data-lucide="user"></i> Profil Saya
            </a>

            <div class="z-nav-cat">Aplikasi</div>

            <?php if(isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2): ?>
            <div class="z-nav-dropdown">
                <div class="z-nav-dropdown-header" onclick="this.parentElement.classList.toggle('open')">
                    <div class="left"><i data-lucide="monitor"></i> GHCBT System</div>
                    <i data-lucide="chevron-right" class="chev" style="width:14px;height:14px;"></i>
                </div>
                <div class="z-nav-subnav">
                    <a href="<?php echo base_url('portal/evaluasi'); ?>" class="z-nav-subnav-item">Evaluasi</a>
                    <a href="<?php echo base_url('portal/hasil-ujian'); ?>" class="z-nav-subnav-item">Hasil Ujian</a>
                    <a href="<?php echo base_url('portal/analisis-soal'); ?>" class="z-nav-subnav-item">Analisis Soal</a>
                    <?php if(in_array('cbt', $myApps)): ?>
                        <a href="<?php echo base_url('exam'); ?>" class="z-nav-subnav-item" style="color:#0ea5e9; font-weight:bold;"><i data-lucide="settings" style="width:12px;height:12px;vertical-align:-2px;"></i> Panel Admin CBT</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
            <a href="<?php echo base_url('portal/ghcbt-siswa'); ?>" class="z-nav-item">
                <i data-lucide="monitor-play"></i> GHCBT System
            </a>
            <?php endif; ?>

            <?php if(isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 99])): ?>
            <a href="<?php echo base_url('absen-saya'); ?>" class="z-nav-item">
                <i data-lucide="calendar-check"></i> Absen Saya
            </a>
            <a href="<?php echo base_url('keuangan'); ?>" class="z-nav-item">
                <i data-lucide="wallet"></i> Keuangan Saya
            </a>
            <a href="<?php echo base_url('siakad/raport'); ?>" class="z-nav-item">
                <i data-lucide="file-spreadsheet"></i> E-Raport
            </a>
            <a href="<?php echo base_url('elearning'); ?>" class="z-nav-item">
                <i data-lucide="video"></i> E-Learning
            </a>

            <div class="z-nav-divider"></div>
            <div class="z-nav-cat">Sistem</div>
            <a href="javascript:void(0)" onclick="alert('Coming Soon')" class="z-nav-item">
                <i data-lucide="settings"></i> Pengaturan
            </a>
            <a href="<?php echo base_url('siakad'); ?>" class="z-nav-item" style="color: #0ea5e9;">
                <i data-lucide="graduation-cap"></i> SIAKAD Admin
            </a>
            <a href="<?php echo base_url('piagam'); ?>" class="z-nav-item" style="color: #0ea5e9;">
                <i data-lucide="award"></i> Piagam Digital Admin
            </a>
            <a href="<?php echo base_url('admin/absen_v2'); ?>" class="z-nav-item" style="color: #0ea5e9;">

                <i data-lucide="qr-code"></i> Absen V2 Admin
            </a>
            <a href="<?php echo base_url('exam'); ?>" class="z-nav-item" style="color: #0ea5e9;">
                <i data-lucide="monitor"></i> GHCBT Admin (Old)
            </a>
            <a href="<?php echo base_url('siakad/raport'); ?>" class="z-nav-item" style="color: #0ea5e9;">
                <i data-lucide="file-spreadsheet"></i> E-Raport Admin
            </a>
            <a href="<?php echo base_url('elearning'); ?>" class="z-nav-item" style="color: #0ea5e9;">
                <i data-lucide="video"></i> E-Learning Admin
            </a>
            <?php endif; ?>
        </div>

        <?php if(!empty($_SESSION['is_kamad']) && (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1)): ?>
        <div style="padding: 0 15px 15px 15px;">
            <a href="<?php echo base_url('switch-role?to=guru'); ?>" class="z-nav-item" style="background:linear-gradient(to right, #0369a1, #064e3b); color:#6ee7b7; border:1px solid #075985; font-weight:800; justify-content:center; padding:12px;">
                <i data-lucide="user-square" style="color:#34d399;"></i> Kembali ke Guru
            </a>
        </div>
        <?php endif; ?>

        <div class="z-sidebar-user">
            <div class="z-sidebar-av"><?php echo $initial; ?></div>
            <div class="z-sidebar-info">
                <div class="sn"><?php echo $nama; ?></div>
                <div class="sr"><?php echo $roleName; ?></div>
            </div>
            <a href="<?php echo base_url('logout'); ?>" class="z-sidebar-logout" title="Logout">
                <i data-lucide="log-out"></i>
            </a>
        </div>
    </aside>
    <div class="z-overlay" id="zOverlay" onclick="document.getElementById('zSidebar').classList.remove('open');this.classList.remove('open');"></div>


