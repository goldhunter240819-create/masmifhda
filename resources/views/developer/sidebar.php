<!-- Developer Sidebar Component -->
<div class="sidebar-wrapper">
    <div class="card" style="background: #0f172a; border-color: #1e293b;">
        <div style="display: flex; flex-direction: column; gap: 0.3rem;">
            
            <div style="padding: 1rem; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.05); margin-bottom: 1rem;">
                <div class="dev-pulse-ring" style="width: 50px; height: 50px; background: rgba(14, 165, 233, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">
                    <i data-lucide="terminal" style="color: #0ea5e9; width: 24px; height: 24px;"></i>
                </div>
                <h4 style="color: white; margin: 0; font-size: 0.9rem; text-shadow: 0 0 10px rgba(16,185,129,0.5);">GOD MODE</h4>
                <p style="color: #64748b; font-size: 0.7rem; margin: 0;">System Control Center</p>
            </div>

            <!-- Main Dashboard Link -->
            <a href="<?php echo base_url('dev-panel'); ?>" class="menu-dropdown <?php echo (!isset($active_menu) || $active_menu == 'dashboard') ? 'open active-main' : ''; ?>" style="text-decoration: none; display: flex; align-items: center; padding: 12px 15px; margin: 0 10px; border-radius: 8px; color: <?php echo (!isset($active_menu) || $active_menu == 'dashboard') ? '#0ea5e9' : '#cbd5e1'; ?>; background: <?php echo (!isset($active_menu) || $active_menu == 'dashboard') ? 'rgba(14, 165, 233, 0.1)' : 'transparent'; ?>; transition: all 0.3s ease;">
                <i data-lucide="layout-dashboard" style="width:18px;height:18px;margin-right:10px;"></i>
                <span style="font-weight: 500; font-size: 0.9rem;">Dashboard</span>
            </a>

            <!-- Dropdown Section: Sistem & Koneksi -->
            <div class="menu-dropdown <?php echo (isset($active_menu) && in_array($active_menu, ['monitor', 'tunnels'])) ? 'open' : ''; ?>">
                <div class="dropdown-header">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <i data-lucide="cpu" style="width:16px;height:16px;"></i>
                        <span>Sistem & Koneksi</span>
                    </div>
                    <i data-lucide="chevron-right" class="chevron-icon" style="width:14px;height:14px;"></i>
                </div>
                <div class="dropdown-body">
                    <a href="<?php echo base_url('dev-panel/monitor'); ?>" class="submenu-item <?php echo (isset($active_menu) && $active_menu == 'monitor') ? 'active' : ''; ?>">
                        <i data-lucide="activity"></i>
                        <span>System Monitor</span>
                    </a>
                    <a href="<?php echo base_url('dev-panel/tunnels'); ?>" class="submenu-item <?php echo (isset($active_menu) && $active_menu == 'tunnels') ? 'active' : ''; ?>">
                        <i data-lucide="globe"></i>
                        <span>Network Tunnels</span>
                    </a>
                    <a href="<?php echo base_url('dev-panel/apk-devices'); ?>" class="submenu-item <?php echo (isset($active_menu) && $active_menu == 'apk_devices') ? 'active' : ''; ?>">
                        <i data-lucide="smartphone"></i>
                        <span>APK Device Manager</span>
                    </a>
                    <a href="<?php echo base_url('dev-panel/apk-preview'); ?>" class="submenu-item <?php echo (isset($active_menu) && $active_menu == 'apk_preview') ? 'active' : ''; ?>">
                        <i data-lucide="monitor-smartphone"></i>
                        <span>APK Phone Emulator</span>
                    </a>
                </div>
            </div>

            <!-- Dropdown Section: Keamanan & Akses -->
            <div class="menu-dropdown <?php echo (isset($active_menu) && in_array($active_menu, ['users', 'delegation', 'access'])) ? 'open' : ''; ?>">
                <div class="dropdown-header">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <i data-lucide="shield" style="width:16px;height:16px;"></i>
                        <span>Keamanan & Akses</span>
                    </div>
                    <i data-lucide="chevron-right" class="chevron-icon" style="width:14px;height:14px;"></i>
                </div>
                <div class="dropdown-body">
                    <a href="<?php echo base_url('dev-panel/users'); ?>" class="submenu-item <?php echo (isset($active_menu) && $active_menu == 'users') ? 'active' : ''; ?>">
                        <i data-lucide="users"></i>
                        <span>Master Admin</span>
                    </a>
                    <a href="<?php echo base_url('dev-panel/delegation'); ?>" class="submenu-item <?php echo (isset($active_menu) && $active_menu == 'delegation') ? 'active' : ''; ?>">
                        <i data-lucide="network"></i>
                        <span>App Delegation</span>
                    </a>
                    <a href="<?php echo base_url('dev-panel/access'); ?>" class="submenu-item <?php echo (isset($active_menu) && $active_menu == 'access') ? 'active' : ''; ?>">
                        <i data-lucide="shield-check"></i>
                        <span>Matrix Hak Akses</span>
                    </a>
                </div>
            </div>

            <!-- Dropdown Section: Aplikasi & Portal -->
            <div class="menu-dropdown <?php echo (isset($active_menu) && in_array($active_menu, ['branding', 'portal_cards', 'licenses'])) ? 'open' : ''; ?>">
                <div class="dropdown-header">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <i data-lucide="sliders" style="width:16px;height:16px;"></i>
                        <span>Aplikasi & Portal</span>
                    </div>
                    <i data-lucide="chevron-right" class="chevron-icon" style="width:14px;height:14px;"></i>
                </div>
                <div class="dropdown-body">
                    <a href="<?php echo base_url('dev-panel/branding'); ?>" class="submenu-item <?php echo (isset($active_menu) && $active_menu == 'branding') ? 'active' : ''; ?>">
                        <i data-lucide="palette"></i>
                        <span>App Branding</span>
                    </a>
                    <a href="<?php echo base_url('dev-panel/portal-cards'); ?>" class="submenu-item <?php echo (isset($active_menu) && $active_menu == 'portal_cards') ? 'active' : ''; ?>">
                        <i data-lucide="layout-dashboard"></i>
                        <span>Portal Card Manager</span>
                    </a>
                    <a href="<?php echo base_url('dev-panel/licenses'); ?>" class="submenu-item <?php echo (isset($active_menu) && $active_menu == 'licenses') ? 'active' : ''; ?>">
                        <i data-lucide="key"></i>
                        <span>Keymaster Pro</span>
                    </a>
                </div>
            </div>

            <div style="margin: 10px 0; border-top: 1px solid rgba(255,255,255,0.05); opacity: 0.5;"></div>

            <!-- MOU -->
            <a href="<?php echo base_url('dev-panel/mou'); ?>" class="menu-item <?php echo (isset($active_menu) && $active_menu == 'mou') ? 'active' : ''; ?>">
                <i data-lucide="file-check-2"></i> 
                <span>MOU Kerjasama</span>
            </a>

            <!-- App View (Toggle) -->
            <a href="<?php echo base_url('portal'); ?>" class="menu-item" style="color: #0ea5e9; border-left: none; padding-left: 1rem;">
                <i data-lucide="eye"></i> 
                <span>Preview App</span>
            </a>
            
        </div>
    </div>
</div>

<style>
    /* GLOBAL PREMIUM DEV MODE STYLES */
    body {
        background: #090e17 !important;
        color: #e2e8f0 !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow-x: hidden;
    }

    .container {
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .siakad-container {
        display: flex;
        min-height: 100vh;
        background: transparent !important;
    }

    .sidebar-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        width: 280px;
        height: 100vh;
        z-index: 1001;
        padding: 1.5rem;
        box-sizing: border-box;
    }

    .sidebar-wrapper .card {
        background: rgba(10, 15, 30, 0.7) !important;
        border: 1px solid rgba(255, 255, 255, 0.03) !important;
        height: 100%;
        margin: 0;
        overflow-y: auto;
        scrollbar-width: none; /* Hide scrollbar for Firefox */
    }
    .sidebar-wrapper .card::-webkit-scrollbar {
        display: none; /* Hide scrollbar for Chrome, Safari and Opera */
    }

    .siakad-main {
        margin-left: 280px;
        width: calc(100% - 280px);
        padding: 2rem !important; /* Normal padding */
        box-sizing: border-box;
        background: radial-gradient(circle at top left, rgba(14, 165, 233, 0.05), transparent 50%),
                    radial-gradient(circle at bottom right, rgba(59, 130, 246, 0.05), transparent 50%) !important;
        min-height: calc(100vh - 100px);
    }

    .card {
        background: rgba(15, 23, 42, 0.6) !important;
        backdrop-filter: blur(16px) !important;
        -webkit-backdrop-filter: blur(16px) !important;
        border: 1px solid rgba(255, 255, 255, 0.05) !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3) !important;
        transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 40px rgba(14, 165, 233, 0.1) !important;
        border-color: rgba(14, 165, 233, 0.2) !important;
    }

    .menu-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 0.65rem 1rem;
        font-size: 0.85rem;
        text-decoration: none;
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        color: #64748b;
        margin-bottom: 3px;
        border-left: 3px solid transparent;
    }

    .menu-item i {
        width: 16px;
        height: 16px;
        transition: transform 0.25s ease;
    }

    .menu-item:hover {
        color: #0ea5e9 !important;
        background: rgba(14, 165, 233, 0.04);
        transform: translateX(3px);
    }

    .menu-item:hover i {
        transform: scale(1.1);
    }

    .menu-item > * {
        z-index: 1;
        position: relative;
    }

    .menu-item.active {
        background: rgba(14, 165, 233, 0.08) !important;
        color: #0ea5e9 !important;
        border-left-color: #0ea5e9;
        border-top: 1px solid rgba(14, 165, 233, 0.1) !important;
        border-right: 1px solid rgba(14, 165, 233, 0.1) !important;
        border-bottom: 1px solid rgba(14, 165, 233, 0.1) !important;
        font-weight: 600;
        box-shadow: 0 0 15px rgba(14, 165, 233, 0.05);
    }

    .dev-pulse-ring {
        animation: pulse-ring 2s infinite cubic-bezier(0.4, 0, 0.6, 1);
    }

    @keyframes pulse-ring {
        0% { box-shadow: 0 0 0 0 rgba(14, 165, 233, 0.4); }
        70% { box-shadow: 0 0 0 15px rgba(14, 165, 233, 0); }
        100% { box-shadow: 0 0 0 0 rgba(14, 165, 233, 0); }
    }

    /* Form Controls */
    .form-control, select, input[type="text"], input[type="password"], input[type="email"], input[type="date"], input[type="file"] {
        background: rgba(15, 23, 42, 0.5) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: #e2e8f0 !important;
        transition: all 0.3s ease;
    }
    .form-control:focus, input:focus, select:focus {
        outline: none;
        border-color: #0ea5e9 !important;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.2) !important;
    }

    /* Tables */
    table { width: 100%; border-collapse: collapse; }
    table th {
        background: rgba(255, 255, 255, 0.03) !important;
        color: #94a3b8 !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    table td {
        color: #e2e8f0 !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
        transition: background 0.2s ease;
    }
    table tr:hover td {
        background: rgba(14, 165, 233, 0.05) !important;
    }

    /* Premium Checkbox Toggle */
    .custom-checkbox {
        display: inline-block;
        position: relative;
        cursor: pointer;
        width: 40px;
        height: 22px;
    }
    .custom-checkbox input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .check-slider {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: rgba(255,255,255,0.1);
        border-radius: 34px;
        transition: .3s;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.3);
    }
    .check-slider:before {
        position: absolute;
        content: "";
        height: 16px; width: 16px;
        left: 3px; bottom: 3px;
        background-color: #94a3b8;
        border-radius: 50%;
        transition: .3s;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .custom-checkbox input:checked + .check-slider {
        background-color: rgba(14, 165, 233, 0.2);
        border: 1px solid rgba(14, 165, 233, 0.5);
    }
    .custom-checkbox input:checked + .check-slider:before {
        transform: translateX(18px);
        background-color: #0ea5e9;
        box-shadow: 0 0 10px rgba(16,185,129,0.8);
    }

    /* Buttons */
    .btn-primary {
        background: linear-gradient(135deg, #0ea5e9, #0284c7) !important;
        border: none !important;
        box-shadow: 0 4px 15px rgba(14, 165, 233, 0.3) !important;
        transition: all 0.3s ease !important;
        color: white !important;
        display: inline-flex; align-items: center; gap: 8px;
        cursor: pointer;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(14, 165, 233, 0.5) !important;
    }
    
    /* Scrollbar */
    ::-webkit-scrollbar { width: 8px; height: 8px; }
    ::-webkit-scrollbar-track { background: #0f172a; }
    ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #0ea5e9; }

    /* Dropdown CSS */
    .menu-dropdown {
        display: flex;
        flex-direction: column;
        margin-bottom: 5px;
    }
    
    .dropdown-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.65rem 1rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: #cbd5e1;
        cursor: pointer;
        border-radius: 10px;
        transition: all 0.2s ease;
        user-select: none;
    }
    
    .dropdown-header:hover {
        background: rgba(255, 255, 255, 0.03);
        color: #0ea5e9;
    }
    
    .dropdown-header i {
        width: 16px;
        height: 16px;
    }
    
    .chevron-icon {
        transition: transform 0.25s ease;
        color: #475569;
    }
    
    .menu-dropdown.open .chevron-icon {
        transform: rotate(90deg);
        color: #0ea5e9;
    }
    
    .dropdown-body {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        padding-left: 0.5rem;
        display: flex;
        flex-direction: column;
    }
    
    .menu-dropdown.open .dropdown-body {
        max-height: 200px;
        margin-top: 2px;
    }
    
    .submenu-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 0.6rem 1rem 0.6rem 1.8rem;
        font-size: 0.82rem;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 500;
        color: #64748b;
        transition: all 0.2s;
        border-left: 2px solid transparent;
        margin-bottom: 2px;
    }
    
    .submenu-item i {
        width: 14px;
        height: 14px;
        transition: transform 0.2s;
    }
    
    .submenu-item:hover {
        color: #0ea5e9 !important;
        background: rgba(14, 165, 233, 0.03);
        transform: translateX(3px);
    }
    
    .submenu-item:hover i {
        transform: scale(1.1);
    }
    
    .submenu-item.active {
        background: rgba(14, 165, 233, 0.06) !important;
        color: #0ea5e9 !important;
        border-left-color: #0ea5e9;
        font-weight: 600;
    }
</style>

<script>
document.querySelectorAll('.dropdown-header').forEach(header => {
    header.addEventListener('click', () => {
        const parent = header.parentElement;
        const isOpen = parent.classList.contains('open');
        
        // Close all dropdowns (Accordion behavior)
        document.querySelectorAll('.menu-dropdown').forEach(d => {
            d.classList.remove('open');
        });
        
        // Open the clicked one if it was closed
        if (!isOpen) {
            parent.classList.add('open');
        }
    });
});
</script>

