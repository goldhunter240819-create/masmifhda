<?php
/* _siakad_header.php â€” Top Header Bar untuk modul SIAKAD */
$_sh_title = isset($page_title) ? $page_title : 'SIAKAD';
$_sh_badge = isset($page_badge) ? $page_badge : 'SIAKAD';
$_sh_nama  = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$_sh_words = explode(' ', trim($_sh_nama));
$_sh_init  = strtoupper(substr($_sh_words[0], 0, 1) . (isset($_sh_words[1]) ? substr($_sh_words[1], 0, 1) : ''));
$_sh_role  = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : 0;
$_sh_label = ($_sh_role == 1 || $_sh_role == 99) ? 'Administrator' : ($_sh_role == 2 ? 'Guru' : 'Siswa');
?>
<header class="z-header" style="position:sticky;top:var(--z-gap);z-index:200;margin-bottom:var(--z-gap);">
    <div class="z-header-left" style="display: flex; align-items: center; gap: 10px;">
        <button class="z-hamburger" style="display:flex;" onclick="zToggleSidebar()">
            <i data-lucide="menu"></i>
        </button>
        <div style="display:flex; align-items:center; gap: 8px; font-weight: 700; font-size: 0.95rem;">
            <span style="color: #10b981; text-transform: uppercase; letter-spacing: 1px;"><?php echo htmlspecialchars($_sh_badge); ?></span>
            <i data-lucide="chevron-right" style="width: 16px; height: 16px; color: #94a3b8;"></i>
            <span style="color: #1e293b;"><?php echo htmlspecialchars($_sh_title); ?></span>
        </div>
    </div>
    <div class="z-header-user" style="gap: 15px; display: flex; align-items: center;">
        <!-- System Online Indicator -->
        <div style="display: flex; align-items: center; gap: 6px; padding: 6px 12px; background: #ecfdf5; color: #059669; border-radius: 20px; font-size: 0.75rem; font-weight: 700; border: 1px solid #a7f3d0;">
            <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);"></span>
            System Online
        </div>
        
        <a href="<?php echo function_exists('base_url') ? base_url('portal') : '/portal'; ?>" style="background: #eff6ff; color: #2563eb; padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 0.85rem; display: flex; align-items: center; gap: 6px; text-decoration: none; border: 1px solid #bfdbfe; transition: 0.2s;" onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
            <i data-lucide="layout-grid" style="width: 16px; height: 16px;"></i> Portal HUB
        </a>
        <div style="width: 1px; height: 24px; background: #e2e8f0; margin: 0 5px;"></div>
        <div class="z-user-info" style="text-align:right;">
            <div class="name" style="font-weight:800;font-size:0.85rem;color:#0f172a;line-height:1.2;"><?php echo htmlspecialchars($_sh_nama); ?></div>
            <div class="role" style="font-size:0.7rem;color:#64748b;"><?php echo $_sh_label; ?></div>
        </div>
        <div class="z-avatar" style="width:36px;height:36px;background:linear-gradient(135deg,#10b981,#059669);color:white;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;box-shadow:0 4px 12px rgba(16,185,129,0.3);"><?php echo $_sh_init; ?></div>
        <a href="<?php echo function_exists('base_url') ? base_url('logout') : '/logout'; ?>" title="Logout" style="background: rgba(239,68,68,.1); color: #f87171; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: 0.2s;" onmouseover="this.style.background='rgba(239,68,68,.2)'" onmouseout="this.style.background='rgba(239,68,68,.1)'">
            <i data-lucide="log-out" style="width: 18px; height: 18px;"></i>
        </a>
    </div>
</header>
