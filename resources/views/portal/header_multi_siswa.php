        <!-- Floating Header -->
        <header class="z-header">
            <div class="z-header-left">
                <button class="z-hamburger" onclick="document.getElementById('zSidebar').classList.toggle('open');document.getElementById('zOverlay').classList.toggle('open');">
                    <i data-lucide="menu"></i>
                </button>
                <span class="z-header-title">Portal Siswa</span>
                <span class="z-badge">Siswa</span>
            </div>
            <div class="z-header-user">
                <div class="z-user-info">
                    <div class="name"><?php echo $nama; ?></div>
                    <div class="role">Siswa</div>
                </div>
                <div class="z-avatar">
                    <?php if(!empty($userFoto)): ?>
                        <img src="<?php echo $userFoto; ?>" alt="Profile" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">
                    <?php else: ?>
                        <?php echo $initial; ?>
                    <?php endif; ?>
                </div>
            </div>
        </header>
