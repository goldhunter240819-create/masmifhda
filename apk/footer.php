<?php
$current_page = basename($_SERVER['PHP_SELF']);
$current_tab = $_GET['tab'] ?? '';

$host = $_SERVER['HTTP_HOST'] ?? '';
$is_online_server = (strpos($host, 'apk.masmifhda.sch.id') !== false);
// Offline CBT: Host bukan apk.masmifhda.sch.id, atau mode cbt standalone aktif
$is_offline_cbt = !$is_online_server || !empty($_SESSION['is_cbt_only']) || (defined('CBT_STANDALONE_MODE') && CBT_STANDALONE_MODE);
?>
<?php if ($is_offline_cbt): ?>
        <!-- BOTTOM NAVIGATION KHUSUS CBT OFFLINE / STANDALONE (3 MENU: BERANDA, RIWAYAT PENGERJAAN, PROFIL) -->
        <div class="bottom-nav" style="left: 50%; transform: translateX(-50%); border-top: 1px solid #e2e8f0; box-shadow: 0 -5px 15px rgba(0,0,0,0.05); display: flex; justify-content: space-around; align-items: center; height: auto; min-height: 70px; padding-top: 5px; padding-bottom: calc(15px + env(safe-area-inset-bottom)); z-index: 9999; background: #ffffff;">
            <a href="cbt.php" class="nav-item <?= ($current_page == 'cbt.php' && $current_tab !== 'riwayat') ? 'active' : '' ?>" style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: <?= ($current_page == 'cbt.php' && $current_tab !== 'riwayat') ? '#2563eb' : '#64748b' ?>;">
                <i class="fa-solid fa-house" style="font-size: 1.25rem; margin-bottom: 4px;"></i>
                <span style="font-size: 0.72rem; font-weight: 700;">Beranda</span>
            </a>
            <a href="cbt.php?tab=riwayat" class="nav-item <?= ($current_page == 'cbt.php' && $current_tab === 'riwayat') ? 'active' : '' ?>" style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: <?= ($current_page == 'cbt.php' && $current_tab === 'riwayat') ? '#2563eb' : '#64748b' ?>;">
                <i class="fa-solid fa-clock-rotate-left" style="font-size: 1.25rem; margin-bottom: 4px;"></i>
                <span style="font-size: 0.72rem; font-weight: 700;">Riwayat Pengerjaan</span>
            </a>
            <a href="profil.php" class="nav-item <?= ($current_page == 'profil.php' || $current_page == 'profil_cbt.php') ? 'active' : '' ?>" style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: <?= ($current_page == 'profil.php' || $current_page == 'profil_cbt.php') ? '#2563eb' : '#64748b' ?>;">
                <i class="fa-solid fa-user" style="font-size: 1.25rem; margin-bottom: 4px;"></i>
                <span style="font-size: 0.72rem; font-weight: 700;">Profil</span>
            </a>
        </div>
<?php else: ?>
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
<?php endif; ?>
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
    <?php 
    $currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
    // Jangan jalankan background polling chat jika sedang berada di ruang ujian CBT agar server sangat ringan
    if (isset($_SESSION['siswa_id']) && empty($_SESSION['is_cbt_only']) && $currentScript !== 'cbt.php' && $currentScript !== 'cbt_scan.php'): 
    ?>
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
        setInterval(checkUnreadChat, 20000); // Ringankan polling menjadi setiap 20 detik
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

        function konfirmasiKeluarCbt() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Keluar Ruang CBT?',
                    text: 'Pastikan seluruh lembar jawaban ujian Anda telah dikumpulkan sebelum keluar.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Keluar Akun',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'logout.php';
                    }
                });
            } else {
                if (confirm('Keluar dari ruang ujian CBT? Pastikan seluruh lembar jawaban telah dikumpulkan.')) {
                    window.location.href = 'logout.php';
                }
            }
        }
    </script>

    <?php
    $curPageApk = basename($_SERVER['PHP_SELF'] ?? '');
    $isCbtOfflineOnly = ($curPageApk === 'cbt.php') && !empty($is_standalone_env) && empty($isAdminPreview) && (!empty($_SESSION['siswa_id']) || !empty($_SESSION['is_cbt_only']));
    $forceFsApk = true;
    if ($isCbtOfflineOnly && isset($core_db) && $core_db) {
        try {
            $fsVal = $core_db->query("SELECT setting_value FROM cbt_pengaturan WHERE setting_key = 'force_fullscreen' LIMIT 1")->fetchColumn();
            if ($fsVal !== false && $fsVal !== null) {
                $forceFsApk = ($fsVal === '1');
            }
        } catch (\Throwable $e) {}
    }
    if ($isCbtOfflineOnly && $forceFsApk):
    ?>
    <!-- Popup Wajib Fullscreen Khusus Beranda CBT Offline -->
    <div id="fsStudentOverlay" style="display: none; position: fixed; inset: 0; z-index: 999999; background: rgba(15, 23, 42, 0.9); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); align-items: center; justify-content: center; padding: 20px;">
        <div style="background: #ffffff; border-radius: 22px; max-width: 400px; width: 100%; padding: 26px 22px; text-align: center; box-shadow: 0 25px 50px rgba(0,0,0,0.45); border: 2px solid #ef4444;">
            <div style="width: 66px; height: 66px; border-radius: 50%; background: #fef2f2; color: #dc2626; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; font-size: 1.8rem;">
                <i class="fa-solid fa-expand"></i>
            </div>
            <h3 style="margin: 0 0 8px; font-size: 1.15rem; font-weight: 800; color: #0f172a;">Wajib Mode Layar Penuh!</h3>
            <p style="margin: 0 0 14px; font-size: 0.84rem; color: #475569; line-height: 1.5;">
                Seluruh halaman siswa wajib menggunakan mode <b>Layar Penuh (Fullscreen)</b>. Jika tidak diaktifkan, Anda akan dikeluarkan otomatis.
            </p>
            <div style="margin-bottom: 16px; padding: 9px 12px; border-radius: 10px; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; font-size: 0.82rem; font-weight: 800;">
                Otomatis keluar dalam <span id="fsStudentSec">12</span> detik jika tidak diaktifkan!
            </div>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <button type="button" onclick="activateStudentFullscreen()" style="width: 100%; padding: 13px 16px; border: none; border-radius: 12px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #fff; font-weight: 800; font-size: 0.92rem; cursor: pointer; box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);">
                    <i class="fa-solid fa-maximize"></i> Aktifkan Layar Penuh Sekarang
                </button>
                <a href="logout.php" style="display: block; width: 100%; padding: 10px; border-radius: 10px; background: #f1f5f9; color: #475569; font-weight: 700; font-size: 0.82rem; text-decoration: none;">
                    Keluar Aplikasi
                </a>
            </div>
        </div>
    </div>
    <script>
        (function() {
            const isNativeBridge = (typeof Android !== 'undefined' || typeof AndroidOneSignal !== 'undefined');
            const fsSupported = !!(document.documentElement.requestFullscreen && document.fullscreenEnabled);
            if (isNativeBridge || !fsSupported) return;

            const overlay = document.getElementById('fsStudentOverlay');
            const secEl = document.getElementById('fsStudentSec');
            let kickTimer = null;
            let kickLeft = 12;

            function stopKick() {
                if (kickTimer) { clearInterval(kickTimer); kickTimer = null; }
            }

            function startKick(seconds = 12) {
                stopKick();
                kickLeft = seconds;
                if (secEl) secEl.textContent = kickLeft;
                kickTimer = setInterval(() => {
                    kickLeft--;
                    if (secEl) secEl.textContent = Math.max(0, kickLeft);
                    if (kickLeft <= 0) {
                        clearInterval(kickTimer);
                        window.location.href = 'logout.php';
                    }
                }, 1000);
            }

            let hadStudentFs = false;
            function syncStudentFs(isFromExit) {
                if (document.fullscreenElement) {
                    hadStudentFs = true;
                    stopKick();
                    if (overlay) overlay.style.display = 'none';
                } else if (hadStudentFs && isFromExit) {
                    if (overlay) overlay.style.display = 'flex';
                    startKick(7);
                } else {
                    if (overlay) overlay.style.display = 'none';
                }
            }

            window.activateStudentFullscreen = function() {
                const el = document.documentElement;
                if (el.requestFullscreen) {
                    el.requestFullscreen().then(() => {
                        hadStudentFs = true;
                        stopKick();
                        if (overlay) overlay.style.display = 'none';
                    }).catch(() => {});
                }
            };

            ['click', 'touchend', 'keydown'].forEach(evt => {
                document.addEventListener(evt, function() {
                    if (!hadStudentFs && !document.fullscreenElement) {
                        window.activateStudentFullscreen();
                    }
                }, { capture: true, passive: true });
            });

            document.addEventListener('fullscreenchange', function() {
                syncStudentFs(!document.fullscreenElement);
            });
            window.addEventListener('DOMContentLoaded', function() {
                window.activateStudentFullscreen();
                syncStudentFs(false);
            });
        })();
    </script>
    <?php endif; ?>
</body>
</html>
