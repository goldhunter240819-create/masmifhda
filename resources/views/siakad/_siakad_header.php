<?php
/**
 * _siakad_header.php — Zamrud Floating Header untuk halaman SIAKAD
 * Variabel opsional: $page_title, $page_badge
 */
$_sh_title = isset($page_title) ? $page_title : (isset($title) ? explode(' - ', $title)[0] : 'SIAKAD');
$_sh_badge = isset($page_badge) ? $page_badge : 'Siakad';
$_sh_nama  = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'Pengguna');
$_sh_init  = strtoupper(substr($_sh_nama, 0, 2));
$_sh_role  = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : 0;
$_sh_label = ($_sh_role == 1 || $_sh_role == 99) ? 'Administrator' : ($_sh_role == 2 ? 'Guru' : 'Siswa');
?>
<header class="z-header" style="position:sticky;top:var(--z-gap);z-index:200;margin-bottom:var(--z-gap);">
    <div class="z-header-left">
        <button class="z-hamburger" style="display:flex;" onclick="zToggleSidebar()">
            <i data-lucide="menu"></i>
        </button>
        <span class="z-header-title"><?php echo htmlspecialchars($_sh_title); ?></span>
        <span class="z-badge"><?php echo htmlspecialchars($_sh_badge); ?></span>
    </div>
    <div class="z-header-right" style="display:flex;align-items:center;gap:1.5rem;">
        <!-- PORTAL HUB BUTTON -->
        <a href="<?php echo base_url('portal'); ?>" style="display:flex;align-items:center;gap:8px;padding:6px 16px;border-radius:100px;border:1px solid #bfdbfe;background:#eff6ff;color:#3b82f6;font-size:0.75rem;font-weight:800;text-decoration:none;transition:0.2s;white-space:nowrap;">
            <i data-lucide="layout-grid" style="width:14px;height:14px;"></i> Portal HUB
        </a>

        <div class="z-header-user" style="display:flex;align-items:center;gap:12px;cursor:pointer;">
            <div class="z-user-info" style="text-align:right;">
                <div class="name" style="font-weight:800;font-size:0.85rem;color:#0f172a;line-height:1.2;"><?php echo htmlspecialchars($_sh_nama); ?></div>
                <div class="role" style="font-size:0.7rem;color:#64748b;"><?php echo $_sh_label; ?></div>
            </div>
            <div class="z-avatar" style="width:36px;height:36px;background:linear-gradient(135deg,#0ea5e9,#0284c7);color:white;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;box-shadow:0 4px 12px rgba(14,165,233,0.2);"><?php echo $_sh_init; ?></div>
        </div>

        <!-- LOGOUT BUTTON -->
        <a href="<?php echo base_url('logout'); ?>" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:10px;background:#fef2f2;color:#ef4444;text-decoration:none;transition:0.2s;border:1px solid #fee2e2;">
            <i data-lucide="log-out" style="width:18px;height:18px;"></i>
        </a>
    </div>
</header>

<script>
    // Global JS function to mirror PHP's base_url() behavior
    if (typeof window.base_url !== 'function') {
        window.base_url = function(path = '') {
            const base = '<?php echo base_url(); ?>';
            return base.replace(/\/$/, '') + '/' + path.replace(/^\//, '');
        };
    }
</script>
