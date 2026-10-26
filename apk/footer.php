<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
        <!-- BOTTOM NAVIGATION -->
        <div class="bottom-nav" style="left: 50%; transform: translateX(-50%); border-top: 1px solid #e2e8f0; box-shadow: 0 -5px 15px rgba(0,0,0,0.05); display: flex; justify-content: space-around; align-items: center; height: auto; min-height: 75px; padding-top: 5px; padding-bottom: calc(25px + env(safe-area-inset-bottom)); z-index: 9999;">
            <a href="index.php" class="nav-item <?= ($current_page == 'index.php') ? 'active' : '' ?>" style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; height: 100%;">
                <i class="fa-solid fa-house" style="font-size: 1.2rem; margin-bottom: 4px;"></i>
                <span style="font-size: 0.65rem; font-weight: 500;">Beranda</span>
            </a>
            <a href="jadwal_pelajaran.php" class="nav-item <?= ($current_page == 'jadwal_pelajaran.php') ? 'active' : '' ?>" style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; height: 100%;">
                <i class="fa-solid fa-calendar-alt" style="font-size: 1.2rem; margin-bottom: 4px;"></i>
                <span style="font-size: 0.65rem; font-weight: 500;">Jadwal</span>
            </a>
            
            <a href="aplikasi_siswa.php" style="position: relative; flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; height: 100%;">
                <div style="background: linear-gradient(135deg, #0ea5e9, #0284c7); width: 68px; height: 68px; border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; box-shadow: 0 6px 15px rgba(2, 132, 199, 0.4); border: 4px solid #f8fafc; z-index: 20; position: absolute; top: -28px;">
                    <i class="fa-solid fa-layer-group" style="font-size: 1.6rem;"></i>
                </div>
                <span style="font-size: 0.65rem; font-weight: 700; color: #0284c7; margin-top: 42px;">Aplikasi</span>
            </a>

            <a href="siswa_tugas_mandiri.php" class="nav-item <?= ($current_page == 'siswa_tugas_mandiri.php') ? 'active' : '' ?>" style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; height: 100%;">
                <i class="fa-solid fa-laptop-file" style="font-size: 1.2rem; margin-bottom: 4px;"></i>
                <span style="font-size: 0.65rem; font-weight: 500;">E-Learning</span>
            </a>
            <a href="profil.php" class="nav-item <?= ($current_page == 'profil.php') ? 'active' : '' ?>" style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; position: relative; height: 100%;">
                <i class="fa-solid fa-user" style="font-size: 1.2rem; margin-bottom: 4px;"></i>
                <div class="fab-badge chat-badge-indicator" style="display: none; position: absolute; top: 10px; right: 15px; font-size: 0.55rem; min-width: 14px; height: 14px; padding: 0 3px; border: 1px solid white;">0</div>
                <span style="font-size: 0.65rem; font-weight: 500;">Profil</span>
            </a>
        </div>
    </div> <!-- .mobile-wrapper -->

    <style>
    .fab-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #ef4444;
        color: white;
        font-size: 0.75rem;
        font-weight: 800;
        min-width: 22px;
        height: 22px;
        border-radius: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 4px;
        border: 2px solid white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    </style>
    <?php if (isset($_SESSION['siswa_id'])): ?>
    <script>
        function checkUnreadChat() {
            fetch('api_chat.php?action=check_unread')
            .then(res => res.json())
            .then(data => {
                let badges = document.querySelectorAll('.chat-badge-indicator');
                if(data.status === 'success' && data.unread > 0) {
                    let text = data.unread > 99 ? '99+' : data.unread;
                    badges.forEach(b => {
                        b.style.display = 'flex';
                        b.innerText = text;
                    });
                } else {
                    badges.forEach(b => b.style.display = 'none');
                }
            }).catch(e => console.log(e));
        }
        checkUnreadChat();
        setInterval(checkUnreadChat, 5000);
    </script>
    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fix footer naik ke atas keyboard
            let originalHeight = window.innerHeight;
            let bottomNav = document.querySelector('.bottom-nav');
            if (bottomNav) {
                window.addEventListener('resize', function() {
                    if (window.innerHeight < originalHeight - 100) {
                        bottomNav.style.display = 'none';
                    } else {
                        bottomNav.style.display = 'flex';
                    }
                });
            }
        });
    </script>
    <script>
        // Anti-logout Ping
        setInterval(function() {
            fetch('koneksi.php').catch(function() {});
        }, 15 * 60 * 1000); // 15 menit
    </script>
</body>
</html>
