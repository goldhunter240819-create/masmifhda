<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar d-none d-lg-flex">
    <div class="sidebar-brand">
        <div style="width: 35px; height: 35px; background: var(--primary-gradient); border-radius: 10px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(var(--primary-rgb), 0.3);">
            <?php if (isset($app_logo) && $app_logo): ?>
                <img src="<?php echo $app_logo; ?>" alt="Logo" style="height: 20px;">
            <?php else: ?>
                <i data-lucide="qr-code" style="color: white; width: 20px; height: 20px;"></i>
            <?php endif; ?>
        </div>
        <div style="display: flex; flex-direction: column; line-height: 1.2;">
            <span><?php echo isset($app_name) ? $app_name : 'GH-SSS'; ?></span>
            <span style="font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">Absensi QR</span>
        </div>
    </div>
    
    <div class="sidebar-scroll">
        <div class="nav-category">Main Menu</div>
        <a class="nav-item-pro <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>" href="/public/absen/pages/dashboard.php">
            <i data-lucide="layout-dashboard"></i> <span>Dashboard</span>
        </a>

        <div class="nav-category">Administrasi Data</div>
        
        <!-- Data Master -->
        <div class="nav-dropdown-pro <?php echo in_array($current_page, ['data_guru.php', 'data_siswa.php', 'jadwal.php', 'pengaturan.php']) ? 'show' : ''; ?>">
            <a href="javascript:void(0)" onclick="this.parentElement.classList.toggle('show')" class="nav-item-pro">
                <i data-lucide="database"></i> 
                <span>Data Master</span>
                <i data-lucide="chevron-right" class="chevron-pro"></i>
            </a>
            <div class="nav-dropdown-content-pro">
                <a class="nav-dropdown-item-pro <?php echo $current_page == 'data_guru.php' ? 'active' : ''; ?>" href="/public/absen/pages/data_guru.php">Data Guru</a>
                <a class="nav-dropdown-item-pro <?php echo $current_page == 'data_siswa.php' ? 'active' : ''; ?>" href="/public/absen/pages/data_siswa.php">Data Siswa</a>
                <a class="nav-dropdown-item-pro <?php echo $current_page == 'jadwal.php' ? 'active' : ''; ?>" href="/public/absen/pages/jadwal.php">Jadwal Mengajar</a>
                <a class="nav-dropdown-item-pro <?php echo $current_page == 'pengaturan.php' ? 'active' : ''; ?>" href="/public/absen/pages/pengaturan.php">Penyesuaian Jam</a>
            </div>
        </div>

        <a class="nav-item-pro <?php echo $current_page == 'scan.php' ? 'active' : ''; ?>" href="/public/absen/pages/scan.php">
            <i data-lucide="scan-qr-code"></i> <span>Pemindai QR</span>
        </a>

        <!-- Pusat Laporan -->
        <div class="nav-dropdown-pro <?php echo in_array($current_page, ['rekap.php', 'rekap_individu.php']) ? 'show' : ''; ?>">
            <a href="javascript:void(0)" onclick="this.parentElement.classList.toggle('show')" class="nav-item-pro">
                <i data-lucide="file-text"></i> 
                <span>Pusat Laporan</span>
                <i data-lucide="chevron-right" class="chevron-pro"></i>
            </a>
            <div class="nav-dropdown-content-pro">
                <a class="nav-dropdown-item-pro <?php echo $current_page == 'rekap.php' ? 'active' : ''; ?>" href="/public/absen/pages/rekap.php">Rekapan Institusi</a>
                <a class="nav-dropdown-item-pro <?php echo $current_page == 'rekap_individu.php' ? 'active' : ''; ?>" href="/public/absen/pages/rekap_individu.php">Slip Individu</a>
            </div>
        </div>

        <div class="nav-category">Fitur Tambahan</div>
        <!-- Absen Manual -->
        <div class="nav-dropdown-pro <?php echo in_array($current_page, ['absen_manual_pengaturan.php', 'absen_manual_jadwal.php', 'absen_manual_generator.php', 'absen_manual_rekap.php']) ? 'show' : ''; ?>">
            <a href="javascript:void(0)" onclick="this.parentElement.classList.toggle('show')" class="nav-item-pro">
                <i data-lucide="edit-3"></i> 
                <span>Absen Manual Guru</span>
                <i data-lucide="chevron-right" class="chevron-pro"></i>
            </a>
            <div class="nav-dropdown-content-pro">
                <a class="nav-dropdown-item-pro <?php echo $current_page == 'absen_manual_pengaturan.php' ? 'active' : ''; ?>" href="/public/absen/pages/absen_manual_pengaturan.php">Konfigurasi Waktu</a>
                <a class="nav-dropdown-item-pro <?php echo $current_page == 'absen_manual_jadwal.php' ? 'active' : ''; ?>" href="/public/absen/pages/absen_manual_jadwal.php">Jadwal Mengajar</a>
                <a class="nav-dropdown-item-pro <?php echo $current_page == 'absen_manual_generator.php' ? 'active' : ''; ?>" href="/public/absen/pages/absen_manual_generator.php">Generator Massal</a>
                <a class="nav-dropdown-item-pro <?php echo $current_page == 'absen_manual_rekap.php' ? 'active' : ''; ?>" href="/public/absen/pages/absen_manual_rekap.php">Rekap Data Fiktif</a>
            </div>
        </div>

        <div class="nav-category">Sistem</div>
        <a class="nav-item-pro text-danger" href="/public/absen/actions/logout.php" style="margin-top: auto;">
            <i data-lucide="log-out"></i> <span>Keluar Sistem</span>
        </a>
    </div>
</div>

<!-- Mobile Header & Sidebar (Updated Style) -->
<div class="d-lg-none bg-white p-3 border-bottom d-flex justify-content-between align-items-center position-sticky top-0 z-3 shadow-sm no-print">
    <div class="fw-bold text-dark" style="font-size: 1rem;"><?php echo isset($app_name) ? $app_name : 'GH-SSS'; ?></div>
    <button class="btn btn-outline-success btn-sm border-0" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
        <i data-lucide="menu"></i>
    </button>
</div>

<div class="offcanvas offcanvas-start" id="mobileSidebar" style="width: 280px; background-color: var(--sidebar-bg); color: #fff;">
    <div class="offcanvas-header border-bottom border-white border-opacity-10">
        <h6 class="offcanvas-title fw-bold">Menu Navigasi</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="nav-category">Main</div>
        <a class="nav-item-pro" href="/public/absen/pages/dashboard.php">Dashboard</a>
        <div class="nav-category">Data</div>
        <a class="nav-item-pro" href="/public/absen/pages/data_guru.php">Data Guru</a>
        <a class="nav-item-pro" href="/public/absen/pages/data_siswa.php">Data Siswa</a>
        <a class="nav-item-pro" href="/public/absen/pages/scan.php">Pemindai QR</a>
        <div class="nav-category">Account</div>
        <a class="nav-item-pro text-danger" href="/public/absen/actions/logout.php">Keluar</a>
    </div>
</div>

<script src="/public/assets/js/lucide.min.js"></script>
<script>
    lucide.createIcons();
</script>
