<?php
$nama = $_SESSION['nama'] ?? ($_SESSION['username'] ?? 'Pengguna');
$initial = strtoupper(substr($nama, 0, 2));

$roleId = isset($_SESSION['role_id']) ? (int)$_SESSION['role_id'] : 0;
$roleLabel = 'Guru / Staff';
if ($roleId === 1 || $roleId === 99) {
    $roleLabel = 'Administrator';
} elseif (!empty($_SESSION['is_kamad'])) {
    $roleLabel = 'Kepala Madrasah';
}

$pageTitle = $pageTitle ?? 'Dashboard';
?>
<header class="z-header" style="height: 74px; padding: 0 1.75rem; background: rgba(255,255,255,0.85); backdrop-filter: blur(20px); border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100;">
    <div class="z-header-left" style="display: flex; align-items: center; gap: 14px;">
        <button class="z-hamburger" id="zHamburger" onclick="zToggleSidebar()" style="background: none; border: none; cursor: pointer; color: #1e293b; display: flex; align-items: center; padding: 6px; border-radius: 8px;">
            <i data-lucide="menu" style="width: 20px; height: 20px;"></i>
        </button>
        <div class="header-breadcrumb" style="display: flex; align-items: center; gap: 8px; font-size: 0.86rem;">
            <a href="<?php echo base_url('portal'); ?>" class="parent" style="color: #0ea5e9; font-weight: 800; text-decoration: none; display: flex; align-items: center; gap: 4px;">
                <i data-lucide="home" style="width: 14px; height: 14px;"></i> Portal
            </a>
            <i data-lucide="chevron-right" class="sep" style="width: 13px; color: #cbd5e1;"></i>
            <span style="color: #64748b; font-weight: 600;">Manajemen Berkas</span>
            <i data-lucide="chevron-right" class="sep" style="width: 13px; color: #cbd5e1;"></i>
            <span class="current" style="font-weight: 700; color: #1e293b;"><?php echo htmlspecialchars($pageTitle); ?></span>
        </div>
    </div>
    <div class="z-header-right" style="display: flex; align-items: center; gap: 1.5rem;">
        <div class="header-status-pill" style="display: flex; align-items: center; gap: 8px; background: #f0fdf4; padding: 6px 14px; border-radius: 100px; border: 1px solid #dcfce7; font-size: 0.75rem; font-weight: 700; color: #15803d;">
            <div class="dot pulse" style="width: 8px; height: 8px; background: #22c55e; border-radius: 50%;"></div>
            <span>System Online</span>
        </div>
        <div class="z-header-user" style="display: flex; align-items: center; gap: 10px;">
            <div class="z-avatar-premium" style="width: 36px; height: 36px; background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; box-shadow: 0 4px 12px rgba(14,165,233,0.2);">
                <?php echo $initial; ?>
            </div>
            <div class="z-user-info" style="display: flex; flex-direction: column; text-align: left;">
                <div class="name" style="font-weight: 800; font-size: 0.88rem; color: #0f172a; line-height: 1.2;">
                    <?php echo htmlspecialchars($nama); ?>
                </div>
                <div class="role" style="font-size: 0.72rem; color: #64748b; font-weight: 600;">
                    <?php echo $roleLabel; ?>
                </div>
            </div>
        </div>
    </div>
</header>

