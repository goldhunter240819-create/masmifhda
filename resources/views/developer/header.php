<nav class="navbar navbar-home" style="margin-bottom: 2rem; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-radius:18px; padding:0.8rem 1.5rem; display:flex; align-items:center; justify-content:space-between; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.05); box-sizing: border-box;">
    <a href="<?php echo base_url('dev-panel'); ?>" style="display:flex; align-items:center; gap:12px; text-decoration:none;">
        <div class="dev-pulse-ring" style="width: 35px; height: 35px; background: rgba(14, 165, 233, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
            <i data-lucide="terminal" style="color: #0ea5e9; width: 18px; height: 18px;"></i>
        </div>
        <span style="font-size:1.3rem; font-weight:800; color:#f8fafc; letter-spacing:-0.5px; text-shadow: 0 0 10px rgba(16,185,129,0.3);">GOD MODE</span>
    </a>

    <!-- User Actions -->
    <div style="display:flex; align-items:center; gap:1rem;">
        <?php if (isset($_SESSION['user_id'])): ?>
            <div style="background:rgba(255,255,255,0.05); padding:6px 16px 6px 6px; border-radius:100px; display:flex; align-items:center; gap:10px; color:#cbd5e1; font-weight:600; font-size:0.85rem; border: 1px solid rgba(255,255,255,0.1);">
                <div style="width:30px; height:30px; background:linear-gradient(135deg, #0ea5e9, #0284c7); border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow: 0 0 10px rgba(16,185,129,0.5);">
                    <i data-lucide="user" style="width:16px; color: white;"></i>
                </div>
                <span>SUPER DEV</span>
            </div>
            
            <a href="<?php echo base_url('logout'); ?>" style="background:rgba(239, 68, 68, 0.1); color:#f87171; border: 1px solid rgba(239, 68, 68, 0.2); width:38px; height:38px; border-radius:50%; display:flex; align-items:center; justify-content:center; transition:0.3s; box-shadow: 0 0 10px rgba(239,68,68,0.1);" onmouseover="this.style.background='#ef4444'; this.style.color='#fff'; this.style.boxShadow='0 0 15px rgba(239,68,68,0.5)';" onmouseout="this.style.background='rgba(239, 68, 68, 0.1)'; this.style.color='#f87171'; this.style.boxShadow='0 0 10px rgba(239,68,68,0.1)';" title="Logout dari God Mode">
                <i data-lucide="power" style="width:16px;"></i>
            </a>
        <?php endif; ?>
    </div>
</nav>

