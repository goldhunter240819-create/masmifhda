        <!-- Floating Header -->
        <header class="z-header">
            <div class="z-header-left">
                <button class="z-hamburger" onclick="document.getElementById('zSidebar').classList.toggle('open');document.getElementById('zOverlay').classList.toggle('open');">
                    <i data-lucide="menu"></i>
                </button>
                <span class="z-header-title">Portal Terpadu</span>
                <span class="z-badge"><?php echo $roleName; ?></span>
            </div>
            <div class="z-header-user">
                <div class="z-user-info">
                    <div class="name"><?php echo $nama; ?></div>
                    <div class="role"><?php echo $roleName; ?></div>
                </div>
                <div class="z-avatar"><?php echo $initial; ?></div>
            </div>
        </header>
