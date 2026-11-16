<?php
$baseRoute = $baseRoute ?? (isset($_SESSION['role_id']) && in_array((int)$_SESSION['role_id'], [1, 99]) || !empty($_SESSION['is_kamad']) ? 'admin/manajemen-berkas' : 'guru/manajemen-berkas');
$active_menu = $active_menu ?? 'dashboard';

$nama = $_SESSION['nama'] ?? ($_SESSION['username'] ?? 'Pengguna');
$initial = strtoupper(substr($nama, 0, 2));

$roleId = isset($_SESSION['role_id']) ? (int)$_SESSION['role_id'] : 0;
$roleLabel = 'Guru / Staff';
if ($roleId === 1 || $roleId === 99) {
    $roleLabel = 'Administrator';
} elseif (!empty($_SESSION['is_kamad'])) {
    $roleLabel = 'Kepala Madrasah';
}

$logoUrl = \App\Core\Branding::getInstitusiLogo();
?>
<aside class="z-sidebar" id="zSidebar">
    <!-- Brand -->
    <div class="z-sidebar-logo">
        <div class="logo-icon" style="background: transparent; padding: 0;">
            <img src="<?php echo $logoUrl; ?>" alt="Logo" style="width: 32px; height: 32px; object-fit: contain; border-radius: 6px;">
        </div>
        <div>
            <div class="logo-text">MASMIFHDA</div>
            <div class="logo-sub" style="color:#0ea5e9;">Manajemen Berkas</div>
        </div>
        <button class="z-close-btn" onclick="zToggleSidebar()" style="display:none;background:none;border:none;color:white;position:absolute;right:1rem;">
            <i data-lucide="x"></i>
        </button>
    </div>

    <!-- Nav List -->
    <div class="z-sidebar-scroll">
        <nav class="z-nav">
            <div style="padding: 0.75rem 1rem 0 1rem;">
                <?php 
                $widgetPath = __DIR__ . '/../partials/academic_year_widget.php';
                if (file_exists($widgetPath)) {
                    include $widgetPath;
                }
                ?>
            </div>

            <div class="z-nav-label">Menu Utama</div>

            <!-- Dashboard -->
            <a href="<?php echo base_url($baseRoute . '/dashboard'); ?>" class="z-nav-item <?php echo $active_menu === 'dashboard' ? 'active' : ''; ?>">
                <i data-lucide="layout-dashboard"></i>
                <span>Dashboard</span>
            </a>

            <!-- Berkas Perangkat Pembelajaran -->
            <a href="<?php echo base_url($baseRoute . '/perangkat'); ?>" class="z-nav-item <?php echo $active_menu === 'perangkat' ? 'active' : ''; ?>">
                <i data-lucide="book-marked"></i>
                <span>Perangkat Pembelajaran</span>
            </a>

            <!-- Berkas Pribadi -->
            <a href="<?php echo base_url($baseRoute . '/pribadi'); ?>" class="z-nav-item <?php echo $active_menu === 'pribadi' ? 'active' : ''; ?>">
                <i data-lucide="file-badge"></i>
                <span>Berkas Pribadi</span>
            </a>
        </nav>
    </div>
</aside>
