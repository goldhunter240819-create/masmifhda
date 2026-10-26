<?php
$current_page = basename($_SERVER['PHP_SELF']);
$is_admin_tabungan = false;
$is_admin_keuangan = false;
$is_admin_absensi = false;

if (!isset($_SESSION['user_id']) && isset($_SESSION['guru_id']) && isset($core_db)) {
    try {
        $stmt_g = $core_db->prepare("SELECT user_id FROM guru WHERE id = ?");
        $stmt_g->execute([$_SESSION['guru_id']]);
        $uid_rec = $stmt_g->fetchColumn();
        if ($uid_rec) $_SESSION['user_id'] = $uid_rec;
    } catch(Exception $e) {}
}

if (isset($_SESSION['user_id']) && isset($core_db)) {
    try {
        $stmt_cek_tab = $core_db->prepare("SELECT app_code FROM app_admins WHERE user_id = ? AND app_code IN ('tabungan', 'keuangan', 'absensi')");
        $stmt_cek_tab->execute([$_SESSION['user_id']]);
        $akses = $stmt_cek_tab->fetchAll(PDO::FETCH_COLUMN);
        
        if (in_array('tabungan', $akses)) {
            $is_admin_tabungan = true;
        }
        if (in_array('keuangan', $akses)) {
            $is_admin_keuangan = true;
        }
        if (in_array('absensi', $akses)) {
            $is_admin_absensi = true;
        }
    } catch(Exception $e) {}

    if (!$is_admin_tabungan) {
        try {
            $cek_kta = $core_db->prepare("SELECT id FROM keuangan_tabungan_admin WHERE user_id = ?");
            $cek_kta->execute([$_SESSION['user_id']]);
            if ($cek_kta->fetch()) $is_admin_tabungan = true;
        } catch(Exception $e) {}
    }

    if (!$is_admin_keuangan) {
        try {
            $cek_bendahara = $core_db->prepare("SELECT ka.id FROM keuangan_akses ka JOIN guru g ON ka.guru_id = g.id WHERE g.user_id = ? OR ka.guru_id = ?");
            $cek_bendahara->execute([$_SESSION['user_id'], $_SESSION['guru_id'] ?? 0]);
            if ($cek_bendahara->fetch()) {
                $is_admin_keuangan = true;
            }
        } catch(Exception $e) {}
    }
}


$is_wali_kelas = false;
$is_has_tugas = false;
if (isset($_SESSION['guru_id']) && isset($core_db)) {
    try {
        $stmt_tugas = $core_db->prepare("SELECT COUNT(*) FROM guru_tugas WHERE guru_id = ?");
        $stmt_tugas->execute([$_SESSION['guru_id']]);
        if ($stmt_tugas->fetchColumn() > 0) {
            $is_has_tugas = true;
        }
        
        $stmt_wk = $core_db->prepare("SELECT id FROM guru_tugas WHERE guru_id = ? AND tugas = 'Wali Kelas' LIMIT 1");
        $stmt_wk->execute([$_SESSION['guru_id']]);
        if ($stmt_wk->fetch()) {
            $is_wali_kelas = true;
        }

        $stmt_pe = $core_db->prepare("SELECT id FROM guru_tugas WHERE guru_id = ? AND tugas = 'Pembina Ekstrakurikuler' LIMIT 1");
        $stmt_pe->execute([$_SESSION['guru_id']]);
        if ($stmt_pe->fetch()) {
            $is_pembina_ekstra = true;
        } else {
            $is_pembina_ekstra = false;
        }
    } catch(Exception $e) {}
}

$has_aplikasi = false;
if ($is_admin_tabungan || $is_admin_keuangan || $is_admin_absensi || $is_wali_kelas || $is_pembina_ekstra) {
    $has_aplikasi = true;
} else if (isset($_SESSION['guru_id']) && isset($core_db)) {
    try {
        $stmt_scan = $core_db->prepare("SELECT akses_siswa, akses_guru FROM absen_akses_scanner WHERE guru_id = ?");
        $stmt_scan->execute([$_SESSION['guru_id']]);
        $scan_data = $stmt_scan->fetch();
        if ($scan_data && ($scan_data['akses_siswa'] > 0 || $scan_data['akses_guru'] > 0)) {
            $has_aplikasi = true;
        }

        if (!$has_aplikasi && isset($_SESSION['user_id'])) {
            $stmt_bos = $core_db->prepare("SELECT id FROM app_admins WHERE user_id = ? AND app_code = 'bos'");
            $stmt_bos->execute([$_SESSION['user_id']]);
            if ($stmt_bos->fetch()) $has_aplikasi = true;
        }

        if (!$has_aplikasi) {
            $stmt_g = $core_db->prepare("SELECT is_kamad FROM guru WHERE id = ?");
            $stmt_g->execute([$_SESSION['guru_id']]);
            if ($stmt_g->fetchColumn() == 1) $has_aplikasi = true;
        }

        if (!$has_aplikasi) {
            $stmt_w = $core_db->prepare("SELECT id FROM guru_tugas WHERE guru_id = ? AND (tugas LIKE '%Kurikulum%' OR tugas LIKE '%Waka Kurikulum%') LIMIT 1");
            $stmt_w->execute([$_SESSION['guru_id']]);
            if ($stmt_w->fetch()) $has_aplikasi = true;
        }
    } catch(Exception $e) {}
}
?>
        <!-- BOTTOM NAVIGATION -->
        <div class="bottom-nav" style="left: 50%; transform: translateX(-50%); border-top: 1px solid #e2e8f0; box-shadow: 0 -5px 15px rgba(0,0,0,0.05); display: flex; justify-content: space-around; align-items: center; height: auto; min-height: 75px; padding-top: 5px; padding-bottom: calc(25px + env(safe-area-inset-bottom)); z-index: 9999;">
            <a href="guru_dashboard.php" class="nav-item <?= ($current_page == 'guru_dashboard.php') ? 'active' : '' ?>" style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; height: 100%;">
                <i class="fa-solid fa-house" style="font-size: 1.2rem; margin-bottom: 4px;"></i>
                <span style="font-size: 0.65rem; font-weight: 500;">Beranda</span>
            </a>
            <a href="jurnal_mengajar.php" class="nav-item <?= ($current_page == 'jurnal_mengajar.php') ? 'active' : '' ?>" style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; height: 100%;">
                <i class="fa-solid fa-book-open" style="font-size: 1.2rem; margin-bottom: 4px;"></i>
                <span style="font-size: 0.65rem; font-weight: 500;">Jurnal</span>
            </a>
            
            <?php if ($has_aplikasi): ?>
            <a href="aplikasi_saya.php" style="position: relative; flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; height: 100%;">
                <div style="background: linear-gradient(135deg, #0ea5e9, #0284c7); width: 68px; height: 68px; border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; box-shadow: 0 6px 15px rgba(2, 132, 199, 0.4); border: 4px solid #f8fafc; z-index: 20; position: absolute; top: -28px;">
                    <i class="fa-solid fa-layer-group" style="font-size: 1.6rem;"></i>
                </div>
                <span style="font-size: 0.65rem; font-weight: 700; color: #0284c7; margin-top: 42px;">Aplikasi</span>
            </a>
            <?php endif; ?>

            <a href="berkas_guru.php" class="nav-item <?= ($current_page == 'berkas_guru.php') ? 'active' : '' ?>" style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; height: 100%;">
                <i class="fa-solid fa-folder-open" style="font-size: 1.2rem; margin-bottom: 4px;"></i>
                <span style="font-size: 0.65rem; font-weight: 500;">Berkas</span>
            </a>
            <a href="profil_guru.php" class="nav-item <?= ($current_page == 'profil_guru.php') ? 'active' : '' ?>" style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; position: relative; height: 100%;">
                <i class="fa-solid fa-user" style="font-size: 1.2rem; margin-bottom: 4px;"></i>
                <?php if ($is_wali_kelas): ?>
                <div class="fab-badge chat-badge-indicator" style="display: none; position: absolute; top: 10px; right: 15px; font-size: 0.55rem; min-width: 14px; height: 14px; padding: 0 3px; border: 1px solid white;">0</div>
                <?php endif; ?>
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
    <?php if (isset($_SESSION['guru_id']) && $is_wali_kelas): ?>
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
