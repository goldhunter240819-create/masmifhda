<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Ambil judul halaman dari variabel $page_title jika ada
$title = isset($page_title) ? $page_title : 'Portal Wali Murid';

// Cek Maintenance Mode
$apk_settings_file = __DIR__ . '/apk_settings.json';
if (file_exists($apk_settings_file)) {
    $apk_settings = json_decode(file_get_contents($apk_settings_file), true);
    if (isset($apk_settings['maintenance_mode']) && $apk_settings['maintenance_mode'] === true) {
        // Jika halaman saat ini bukan halaman login atau logout, blokir akses
        $current_script = basename($_SERVER['SCRIPT_NAME']);
        if (!in_array($current_script, ['login.php', 'logout.php'])) {
            echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"><title>Sedang Perbaikan - MAS Mifhda</title><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"><style>body{margin:0;padding:0;font-family:\'Plus Jakarta Sans\',sans-serif;background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);color:#fff;display:flex;align-items:center;justify-content:center;min-height:100vh;overflow:hidden;text-align:center;position:relative;}.bg-blobs{position:absolute;top:0;left:0;width:100%;height:100%;overflow:hidden;z-index:0;}.blob{position:absolute;filter:blur(60px);opacity:0.4;animation:float 8s ease-in-out infinite;}.blob-1{width:300px;height:300px;background:#0ea5e9;border-radius:50%;top:-100px;left:-100px;animation-delay:0s;}.blob-2{width:250px;height:250px;background:#10b981;border-radius:50%;bottom:-50px;right:-50px;animation-delay:2s;}.blob-3{width:200px;height:200px;background:#8b5cf6;border-radius:50%;top:50%;left:50%;transform:translate(-50%,-50%);animation-delay:4s;}@keyframes float{0%,100%{transform:translateY(0) scale(1);}50%{transform:translateY(-20px) scale(1.05);}}.content-box{position:relative;z-index:1;background:rgba(255,255,255,0.05);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.1);padding:40px 30px;border-radius:24px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.5);max-width:400px;width:90%;}.icon-wrapper{position:relative;width:100px;height:100px;margin:0 auto 25px;}.gear{position:absolute;color:#0ea5e9;animation:spin 4s linear infinite;}.gear-1{font-size:3.5rem;top:10px;left:10px;}.gear-2{font-size:2rem;bottom:15px;right:15px;color:#10b981;animation:spin-reverse 3s linear infinite;}.gear-3{font-size:1.5rem;top:20px;right:10px;color:#facc15;animation:spin 5s linear infinite;}@keyframes spin{100%{transform:rotate(360deg);}}@keyframes spin-reverse{100%{transform:rotate(-360deg);}}h1{margin:0 0 10px;font-size:1.8rem;font-weight:800;background:linear-gradient(to right,#38bdf8,#34d399);-webkit-background-clip:text;-webkit-text-fill-color:transparent;}p{margin:0 0 25px;color:#cbd5e1;font-size:0.95rem;line-height:1.6;}.progress-container{width:100%;height:6px;background:rgba(255,255,255,0.1);border-radius:10px;overflow:hidden;margin-bottom:20px;}.progress-bar{height:100%;width:50%;background:linear-gradient(90deg,#0ea5e9,#10b981,#0ea5e9);background-size:200% 100%;animation:loading 2s linear infinite, fill 10s ease-in-out infinite alternate;}@keyframes loading{0%{background-position:100% 0;}100%{background-position:-100% 0;}}@keyframes fill{0%{width:30%;}100%{width:80%;}}.btn-refresh{background:rgba(255,255,255,0.1);color:#fff;border:1px solid rgba(255,255,255,0.2);padding:12px 24px;border-radius:12px;font-size:0.9rem;font-weight:600;cursor:pointer;transition:all 0.3s;display:inline-flex;align-items:center;gap:8px;text-decoration:none;}.btn-refresh:hover{background:rgba(255,255,255,0.2);transform:translateY(-2px);}.badge{display:inline-block;padding:5px 12px;background:rgba(245,158,11,0.2);color:#fcd34d;border-radius:100px;font-size:0.75rem;font-weight:800;letter-spacing:1px;text-transform:uppercase;margin-bottom:15px;}</style></head><body><div class="bg-blobs"><div class="blob blob-1"></div><div class="blob blob-2"></div><div class="blob blob-3"></div></div><div class="content-box"><div class="badge">Sistem Sedang Diperbarui</div><div class="icon-wrapper"><i class="fa-solid fa-gear gear gear-1"></i><i class="fa-solid fa-gear gear gear-2"></i><i class="fa-solid fa-gear gear gear-3"></i></div><h1>Under Maintenance</h1><p>Halo ges! Saat ini kami sedang melakukan peningkatan sistem dan <b>membangun fitur-fitur baru yang lebih keren</b> untuk Anda. Harap bersabar ya!</p><div class="progress-container"><div class="progress-bar"></div></div><a href="javascript:window.location.reload();" class="btn-refresh"><i class="fa-solid fa-rotate-right"></i> Cek Ulang Sekarang</a></div></body></html>';
            exit;
        }
    }
}

// Fetch favicon and school info
$favicon_url = '/public/uploads/logo/logo_1781854511.png'; // default
$school_name = 'MAS Mifhda';
if (isset($core_db)) {
    try {
        $stmt_inst = $core_db->query("SELECT logo, nama_institusi FROM institusi LIMIT 1");
        $inst = $stmt_inst->fetch(PDO::FETCH_ASSOC);
        if ($inst) {
            if (!empty($inst['logo'])) {
                $favicon_url = '/public/uploads/logo/' . $inst['logo'];
            }
            if (!empty($inst['nama_institusi'])) {
                $school_name = $inst['nama_institusi'];
            }
        }
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#0ea5e9">
    <title><?= htmlspecialchars($title) ?> - MAS Mifhda</title>
    <link rel="icon" type="image/png" href="<?= htmlspecialchars($favicon_url) ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #10b981;
            --primary-light: #34d399;
            --secondary: #0ea5e9;
            --danger: #EF4444;
            --warning: #f59e0b;
            --bg-color: #f3f6f9;
            --card-bg: rgba(255, 255, 255, 0.9);
            --text-main: #0f172a;
            --text-muted: #64748b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        html { font-size: 14px; }

        body {
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
            min-height: 100vh;
            color: var(--text-main);
            display: flex;
            justify-content: center;
        }

        .mobile-wrapper {
            width: 100%; max-width: 480px; background: #fff; min-height: 100vh; position: relative;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow-x: hidden; display: flex; flex-direction: column;
        }

        .header {
            padding: 4rem 1.5rem 2rem; background: linear-gradient(135deg, var(--primary) 0%, #059669 100%); color: white;
            border-bottom-left-radius: 35px; border-bottom-right-radius: 35px; box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);
            position: relative; overflow: hidden;
        }
        .header::before { content: ''; position: absolute; top: -50%; right: -20%; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%; }

        .header-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; }
        .greeting h1 { font-size: 1.35rem; font-weight: 800; line-height: 1.2; }
        .greeting p { font-size: 0.85rem; opacity: 0.9; margin-bottom: 3px; }
        .avatar { width: 50px; height: 50px; background: white; border-radius: 16px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1.4rem; box-shadow: 0 8px 15px rgba(0,0,0,0.1); }

        .main-content { padding: 1.25rem; flex-grow: 1; padding-bottom: 100px; background: var(--bg-color); }

        .student-info-card { background: white; border-radius: 20px; padding: 1.25rem; display: flex; align-items: center; gap: 15px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); margin-bottom: 1.5rem; margin-top: -2.5rem; position: relative; z-index: 10; }
        .student-icon { width: 45px; height: 45px; background: #ecfdf5; color: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
        .student-details h3 { font-size: 1rem; color: var(--text-main); }
        .student-details p { font-size: 0.8rem; color: var(--text-muted); }

        .section-title { font-size: 1rem; font-weight: 800; margin-bottom: 0.75rem; color: var(--text-main); }
        .menu-grid { display: grid; grid-template-columns: 1fr; gap: 1rem; }

        .data-card { background: white; border-radius: 20px; padding: 1.5rem; box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.05); border: 1px solid rgba(0,0,0,0.03); position: relative; overflow: hidden; margin-bottom: 1rem; }
        .data-card::before { content: ''; position: absolute; left: 0; top: 0; height: 100%; width: 6px; }
        .card-finance::before { background: var(--secondary); }
        .card-attendance::before { background: var(--primary); }
        .card-warning::before { background: var(--warning); }
        .card-danger::before { background: var(--danger); }

        .card-header { display: flex; align-items: center; gap: 10px; margin-bottom: 1rem; border-bottom: 1px dashed #e2e8f0; padding-bottom: 0.75rem; }
        .card-icon { width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
        .icon-finance { background: #e0f2fe; color: var(--secondary); }
        .icon-attendance { background: #ecfdf5; color: var(--primary); }
        .icon-warning { background: #fef3c7; color: var(--warning); }
        .icon-danger { background: #fee2e2; color: var(--danger); }
        .icon-gray { background: #f1f5f9; color: var(--text-muted); }

        .card-title { font-weight: 700; font-size: 1rem; color: var(--text-main); flex-grow: 1; }
        .card-action { font-size: 0.8rem; color: var(--primary); font-weight: 700; cursor: pointer; text-decoration: underline; }

        .data-row { display: flex; justify-content: space-between; margin-bottom: 0.6rem; font-size: 0.9rem; }
        .data-label { color: var(--text-muted); }
        .data-value { font-weight: 700; color: var(--text-main); text-align: right; }
        
        .value-green { color: var(--primary); }
        .value-red { color: var(--danger); }
        .value-orange { color: var(--warning); }
        
        .list-item { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
        .list-item:last-child { border-bottom: none; padding-bottom: 0; }
        .list-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #f8fafc; color: var(--text-muted); margin-right: 15px; }
        .list-content { flex-grow: 1; }
        .list-title { font-weight: 700; font-size: 0.95rem; color: var(--text-main); }
        .list-sub { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
        .list-amount { font-weight: 800; font-size: 0.95rem; }

        .bottom-nav { position: fixed; bottom: 0; width: 100%; max-width: 480px; background: white; display: flex; justify-content: space-around; padding: 1rem 1rem 1.8rem; box-shadow: 0 -10px 25px -5px rgba(0, 0, 0, 0.05); border-top-left-radius: 25px; border-top-right-radius: 25px; z-index: 50; padding-bottom: calc(1.8rem + env(safe-area-inset-bottom)); }
        .nav-item { text-align: center; color: #94a3b8; text-decoration: none; font-size: 0.75rem; font-weight: 600; transition: color 0.3s ease; display: flex; flex-direction: column; align-items: center; gap: 0.4rem; cursor: pointer; }
        .nav-item.active { color: var(--primary); }
        .nav-item i { font-size: 1.3rem; transition: transform 0.2s ease; }
        .nav-item:hover i { transform: translateY(-3px); color: var(--primary); }

        .btn-primary { background: var(--primary); color: white; border: none; padding: 12px; border-radius: 12px; font-weight: 700; width: 100%; cursor: pointer; display: inline-flex; justify-content: center; align-items: center; gap: 8px; text-decoration: none; }
        .btn-logout { margin-top: 1rem; width: 100%; padding: 12px; border-radius: 15px; border: 1px solid #fecaca; background: #fef2f2; color: var(--danger); font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none; transition: all 0.2s; }
        .btn-logout:hover { background: var(--danger); color: white; }
    </style>
    
    <?php
    // ONESIGNAL PRIVACY BRIDGE
    // Script ini berfungsi untuk memberi tahu APK siapa yang sedang login
    $os_user_id = '';
    if (isset($_SESSION['siswa_id'])) {
        $os_user_id = 'siswa_' . $_SESSION['siswa_id'];
    } elseif (isset($_SESSION['guru_id'])) {
        $os_user_id = 'guru_' . $_SESSION['guru_id'];
    }
    
    if ($os_user_id !== ''):
    ?>
    <script>
        // Memanggil fungsi dari Android Studio (JavascriptInterface)
        if (typeof AndroidOneSignal !== "undefined") {
            try {
                AndroidOneSignal.loginOneSignal("<?= $os_user_id ?>");
                console.log("OneSignal Login triggered for: <?= $os_user_id ?>");
            } catch(e) {}
        }
    </script>
    <?php endif; ?>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmLogout(e, url, message = 'Keluar Aplikasi?') {
            e.preventDefault();
            Swal.fire({
                title: message,
                text: "Anda yakin ingin keluar dari aplikasi?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    container: 'my-swal-container'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }
    </script>
    <?php if (isset($success_msg) || isset($error_msg)): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Modern Floating Toast
            const Toast = Swal.mixin({
                toast: true,
                position: 'top',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                background: '#ffffff',
                color: '#1e293b',
                customClass: {
                    popup: 'modern-toast'
                },
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            // Tambahkan CSS custom untuk notifikasi melayang
            const style = document.createElement('style');
            style.innerHTML = `
                .modern-toast {
                    border-radius: 16px !important;
                    padding: 12px 20px !important;
                    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1) !important;
                    border: 1px solid rgba(0,0,0,0.05) !important;
                    margin-top: 15px !important;
                    font-family: 'Plus Jakarta Sans', sans-serif !important;
                    font-size: 0.95rem !important;
                    font-weight: 600 !important;
                }
            `;
            document.head.appendChild(style);

            function findAndHideDiv(text) {
                const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null, false);
                let node;
                while (node = walker.nextNode()) {
                    if (node.nodeValue.includes(text)) {
                        let parent = node.parentElement;
                        // Cari container alert terdekat (biasanya punya padding dan border-radius inline)
                        while(parent && parent.tagName !== 'BODY') {
                            if(parent.style.padding && (parent.style.borderRadius || parent.style.marginBottom)) {
                                parent.style.display = 'none';
                                break;
                            }
                            parent = parent.parentElement;
                        }
                    }
                }
            }

            <?php if (isset($success_msg)): ?>
            setTimeout(() => {
                Toast.fire({
                    icon: 'success',
                    title: <?= json_encode($success_msg) ?>
                });
                findAndHideDiv(<?= json_encode($success_msg) ?>);
            }, 100);
            <?php endif; ?>

            <?php if (isset($error_msg)): ?>
            setTimeout(() => {
                Toast.fire({
                    icon: 'error',
                    title: <?= json_encode($error_msg) ?>
                });
                findAndHideDiv(<?= json_encode($error_msg) ?>);
            }, 100);
            <?php endif; ?>
        });
    </script>
    <?php endif; ?>
</head>
<body>
    <div class="mobile-wrapper">
        <!-- GLOBAL APP BAR / SCHOOL IDENTITY -->
        <div style="position: absolute; top: 20px; left: 20px; z-index: 100; display: flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.2); padding: 4px 12px 4px 6px; border-radius: 30px; backdrop-filter: blur(5px); box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid rgba(255,255,255,0.3);">
            <img src="<?= htmlspecialchars($favicon_url) ?>" style="height: 22px; width: 22px; border-radius: 50%; background: white; padding: 2px; object-fit: contain;">
            <span style="color: white; font-weight: 700; font-size: 0.85rem; text-shadow: 0 1px 3px rgba(0,0,0,0.3); letter-spacing: 0.3px;"><?= htmlspecialchars($school_name) ?></span>
        </div>
