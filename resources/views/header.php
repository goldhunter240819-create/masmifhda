<nav class="navbar navbar-home" style="background:var(--z-sidebar-bg); border-radius:18px; padding:0.8rem 1.5rem; margin-top:1rem; margin-left:2rem; margin-right:2rem; display:flex; align-items:center; justify-content:space-between; box-shadow:var(--shadow-soft);">
    <a href="<?php echo base_url(''); ?>" style="display:flex; align-items:center; gap:12px; text-decoration:none;">
        <?php
        $_header_logo = isset($site_logo) ? $site_logo : \App\Core\Branding::getInstitusiLogo();
        ?>
        <img src="<?php echo $_header_logo; ?>" alt="Logo" style="height:35px; width:auto; border-radius:6px; object-fit:contain;">
        <span style="font-size:1.3rem; font-weight:800; color:#fff; letter-spacing:-0.5px;"><?php echo isset($site_name) ? $site_name : 'Aula System'; ?></span>
    </a>

    <div class="nav-links" style="display:flex; gap:2.5rem; position:absolute; left:50%; transform:translateX(-50%);">
        <?php if (isset($_SESSION['user_id'])): 
            $currYear = \App\Core\AcademicYear::current();
            $allYears = \App\Core\AcademicYear::getAll();
        ?>
        <div style="position:relative; display:flex; align-items:center;">
            <select onchange="window.location.href="<?php echo base_url('switch-academic-year.php?id='); ?>" + this.value" style="background:rgba(255,255,255,0.1); color:#fff; border:1px solid rgba(255,255,255,0.2); border-radius:100px; padding:6px 30px 6px 16px; appearance:none; font-weight:700; font-size:0.85rem; outline:none; cursor:pointer;">
                <?php foreach($allYears as $y): ?>
                    <option value="<?= $y['id'] ?>" <?= $currYear['id'] == $y['id'] ? 'selected' : '' ?> style="color:#1e293b;"><?= htmlspecialchars($y['name']) ?> (Smt <?= $y['semester'] ?>)</option>
                <?php endforeach; ?>
                <option value="0" style="color:#1e293b;">Default Aktif</option>
            </select>
            <i data-lucide="chevron-down" style="width:14px; color:#fff; position:absolute; right:12px; pointer-events:none;"></i>
        </div>
        <?php endif; ?>
    </div>

    <!-- User Actions -->
    <div style="display:flex; align-items:center; gap:1rem;">
        <?php if(!empty($_SESSION['is_kamad']) && (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1)): ?>
            <a href="<?php echo base_url('switch-role?to=guru'); ?>" style="background:linear-gradient(135deg, #0369a1, #064e3b); color:#6ee7b7; border:1px solid #075985; padding:6px 14px; border-radius:100px; display:flex; align-items:center; gap:8px; font-weight:700; font-size:0.8rem; text-decoration:none; transition:0.2s;" onmouseover="this.style.filter='brightness(1.1)';" onmouseout="this.style.filter='none';">
                <i data-lucide="user-square" style="width:16px;"></i> Kembali ke Guru
            </a>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_id'])): ?>
            <div style="background:rgba(255,255,255,0.1); padding:6px 16px 6px 6px; border-radius:100px; display:flex; align-items:center; gap:10px; color:#fff; font-weight:600; font-size:0.85rem;">
                <div style="width:30px; height:30px; background:var(--z-primary); border-radius:50%; display:flex; align-items:center; justify-content:center;">
                    <i data-lucide="user" style="width:16px;"></i>
                </div>
                <span><?php echo isset($_SESSION['nama']) ? $_SESSION['nama'] : $_SESSION['username']; ?></span>
            </div>
            <?php if (!isset($hide_logout) || !$hide_logout): ?>
            <a href="<?php echo base_url('logout'); ?>" style="background:rgba(225,29,72,0.15); color:#fca5a5; width:38px; height:38px; border-radius:50%; display:flex; align-items:center; justify-content:center; transition:0.2s;" onmouseover="this.style.background='#e11d48'; this.style.color='#fff';" onmouseout="this.style.background='rgba(225,29,72,0.15)'; this.style.color='#fca5a5';" title="Logout">
                <i data-lucide="log-out" style="width:18px;"></i>
            </a>
            <?php endif; ?>
        <?php else: ?>
            <a href="<?php echo base_url('login'); ?>" style="background:var(--z-primary); padding:8px 20px; border-radius:12px; display:flex; align-items:center; gap:8px; color:#fff; font-weight:700; font-size:0.85rem; text-decoration:none; transition:0.2s;" onmouseover="this.style.filter='brightness(1.1)';" onmouseout="this.style.filter='none';">
                <i data-lucide="log-in" style="width:16px;"></i>
                <span>Login System</span>
            </a>
        <?php endif; ?>
    </div>
</nav>


