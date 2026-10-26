        <!-- Floating Header -->
        <header class="z-header">
            <div class="z-header-left">
                <button class="z-hamburger" onclick="document.getElementById('zSidebar').classList.toggle('open');document.getElementById('zOverlay').classList.toggle('open');">
                    <i data-lucide="menu"></i>
                </button>
                <span class="z-header-title">Portal Guru</span>
                <?php if(!empty($_SESSION['is_kamad'])): ?>
                    <span class="z-badge" style="background: linear-gradient(135deg, #fefce8, #fef08a); color: #b45309; border: 1px solid #fde047; box-shadow: 0 0 10px rgba(234, 179, 8, 0.2); font-weight: 800;">KAMAD</span>
                <?php else: ?>
                    <span class="z-badge">Guru</span>
                <?php endif; ?>
            </div>
            <div class="z-header-right" style="display: flex; align-items: center; gap: 15px;">
                <a href="<?php echo base_url('portal'); ?>" style="display:flex;align-items:center;gap:8px;padding:6px 16px;border-radius:100px;border:1px solid var(--z-primary);background:var(--z-primary-light);color:var(--z-primary);font-size:0.75rem;font-weight:800;text-decoration:none;transition:0.2s;white-space:nowrap;">
                    <i data-lucide="layout-grid" style="width:14px;height:14px;"></i> Portal HUB
                </a>
                
                <div class="z-header-user" style="display:flex;align-items:center;gap:12px;cursor:pointer;">
                    <div class="z-user-info" style="text-align:right;">
                        <div class="name" style="font-weight:800;font-size:0.85rem;color:var(--z-text);line-height:1.2;"><?php echo $nama; ?></div>
                        <div class="role" style="font-size:0.7rem;color:var(--z-muted); <?php echo !empty($_SESSION['is_kamad']) ? 'color: #d97706; font-weight: 800;' : ''; ?>"><?php echo !empty($_SESSION['is_kamad']) ? 'Kepala Madrasah' : 'Guru / Tendik'; ?></div>
                    </div>
                    <div class="z-avatar" style="width:36px;height:36px;background:var(--z-primary-gradient);color:white;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                        <?php if(!empty($userFoto)): ?>
                            <img src="<?php echo $userFoto; ?>" alt="Profile" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">
                        <?php else: ?>
                            <?php echo $initial; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <a href="<?php echo base_url('logout'); ?>" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:10px;background:#fef2f2;color:#ef4444;text-decoration:none;transition:0.2s;border:1px solid #fee2e2;" title="Logout">
                    <i data-lucide="log-out" style="width:18px;height:18px;"></i>
                </a>
            </div>
        </header>
