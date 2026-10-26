<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$role_id = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : 0;
$roleName = "Pengguna";
if($role_id == 2) $roleName = "Guru / Tendik";
elseif($role_id == 3) $roleName = "Siswa";

$profileUrl = ($role_id == 2) ? '/profil-guru' : '/profil-siswa';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <!-- Font -->
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <!-- Icons -->
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <style>
        :root {
            --primary: #0284c7;
            --primary-hover: #075985;
            --sidebar-bg: #071f14;
            --sidebar-hover: rgba(255, 255, 255, 0.05);
            --sidebar-text: #e2e8f0;
            --sidebar-active: #0ea5e9;
            --bg-body: #eef2f0;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --sidebar-width: 252px;
            --header-height: 70px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-body); color: var(--text-main); overflow: hidden; }
        .wrapper { display: flex; width: 100%; height: 100vh; overflow: hidden; }
        
        /* --- SIDEBAR --- */
        .sidebar { width: var(--sidebar-width); background-color: var(--sidebar-bg); color: var(--sidebar-text); display: flex; flex-direction: column; transition: all 0.3s ease; position: relative; z-index: 100; }
        .sidebar-header { height: var(--header-height); display: flex; align-items: center; justify-content: center; border-bottom: 1px solid rgba(255, 255, 255, 0.1); font-size: 1.25rem; font-weight: 700; letter-spacing: 1px; color: #fff; gap: 10px; }
        .sidebar-scroll { flex: 1; overflow-y: auto; padding: 1rem 0; }
        .nav-category { font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: rgba(255, 255, 255, 0.6); padding: 1rem 1.5rem 0.5rem; letter-spacing: 1px; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 0.75rem 1.5rem; color: var(--sidebar-text); text-decoration: none; transition: 0.2s; font-size: 0.95rem; }
        .nav-item:hover { background-color: var(--sidebar-hover); color: #fff; }
        .nav-item.active { background-color: var(--sidebar-active); color: #fff; border-left: 4px solid #fff; }
        .nav-item i { width: 18px; height: 18px; opacity: 0.8; }
        
        .nav-dropdown { position: relative; }
        .nav-dropdown-content { display: none; background: rgba(0,0,0,0.15); flex-direction: column; }
        .nav-dropdown.open .nav-dropdown-content { display: flex; }
        .nav-dropdown-item { padding: 0.6rem 1.5rem 0.6rem 3.5rem; color: rgba(255,255,255,0.7); text-decoration: none; font-size: 0.85rem; transition: 0.2s; border-left: 3px solid transparent; }
        .nav-dropdown-item:hover { color: #fff; background: rgba(255,255,255,0.05); border-left-color: var(--sidebar-active); }
        .nav-dropdown-item.active { color: var(--sidebar-active); font-weight: 700; }
        .chevron { margin-left: auto; transition: 0.3s; width: 14px !important; height: 14px !important; opacity: 0.5; }
        .nav-dropdown.open .chevron { transform: rotate(90deg); opacity: 1; }

        .sidebar-footer { padding: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.1); }

        /* --- MAIN --- */
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; position: relative; }
        .header { height: var(--header-height); background-color: transparent; border-bottom: none; display: flex; align-items: center; justify-content: space-between; padding: 0 1.5rem; z-index: 10; }
        .content-scroll { flex: 1; overflow-y: auto; padding: 2rem; display: flex; align-items: center; justify-content: center; }

        .status-card { background: white; padding: 4rem; border-radius: 24px; text-align: center; max-width: 600px; box-shadow: 0 4px 24px rgba(7,31,20,0.04); border: 1px solid var(--border); }
        .icon-box { width: 80px; height: 80px; background: #f0f9ff; color: #0ea5e9; border-radius: 24px; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; }
        
        .progress-bar { width: 100%; height: 6px; background: #f1f5f9; border-radius: 10px; margin: 2rem 0; overflow: hidden; position: relative; }
        .progress-fill { position: absolute; height: 100%; width: 30%; background: var(--sidebar-active); border-radius: 10px; animation: loading 2s infinite ease-in-out; }
        @keyframes loading { 0% { left: -30%; } 100% { left: 100%; } }
    </style>
</head>
<body>

<div class="wrapper">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i data-lucide="graduation-cap"></i> Aula System Portal
        </div>
        
        <div class="sidebar-scroll">
            <div class="nav-category">Main Menu</div>
            <a href="<?php echo base_url(''); ?>" class="nav-item">
                <i data-lucide="layout-dashboard"></i>
                <span>Dashboard Multi</span>
            </a>
            <a href="<?php echo $profileUrl; ?>" class="nav-item">
                <i data-lucide="user"></i>
                <span>Profil Saya</span>
            </a>

            <div class="nav-category">Aplikasi Terintegrasi</div>
            <?php if($role_id == 2): ?>
            <div class="nav-dropdown open">
                <a href="javascript:void(0)" class="nav-item active" onclick="toggleDropdown(this)">
                    <i data-lucide="monitor-play"></i>
                    <span>GHCBT System</span>
                    <i data-lucide="chevron-right" class="chevron"></i>
                </a>
                <div class="nav-dropdown-content" style="display: flex;">
                    <a href="<?php echo base_url('portal/evaluasi'); ?>" class="nav-dropdown-item <?php echo $title_page == 'Evaluasi' ? 'active' : ''; ?>">Evaluasi</a>
                    <a href="<?php echo base_url('portal/hasil-ujian'); ?>" class="nav-dropdown-item <?php echo $title_page == 'Hasil Ujian' ? 'active' : ''; ?>">Hasil Ujian</a>
                    <a href="<?php echo base_url('portal/analisis-soal'); ?>" class="nav-dropdown-item <?php echo $title_page == 'Analisis Butir Soal' ? 'active' : ''; ?>">Analisis Butir Soal</a>
                </div>
            </div>
            <?php else: ?>
            <a href="<?php echo base_url('portal/ghcbt-siswa'); ?>" class="nav-item active">
                <i data-lucide="monitor-play"></i>
                <span>GHCBT System</span>
            </a>
            <?php endif; ?>
            
            <a href="<?php echo base_url('admin/absen_v2'); ?>" class="nav-item">
                <i data-lucide="qr-code"></i>
                <span>Absensi QR V2</span>
            </a>
            <a href="#" onclick="alert('Coming Soon')" class="nav-item">
                <i data-lucide="file-text"></i>
                <span>E-Raport</span>
            </a>
            <a href="#" onclick="alert('Coming Soon')" class="nav-item">
                <i data-lucide="video"></i>
                <span>E-Learning (Tugas)</span>
            </a>
        </div>

        <div class="sidebar-footer">
            <a href="<?php echo base_url('logout'); ?>" class="nav-item" style="color: #fca5a5;">
                <i data-lucide="log-out"></i>
                <span>Keluar Sistem</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header class="header">
            <div style="font-weight: 700; color: var(--primary);"><?php echo $title_page; ?></div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="text-align: right; line-height: 1.2;">
                    <div style="font-weight: 700; font-size: 0.9rem;"><?php echo $nama; ?></div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo $roleName; ?></div>
                </div>
                <div style="width: 38px; height: 38px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800;">
                    <?php echo substr($nama, 0, 1); ?>
                </div>
            </div>
        </header>

        <div class="content-scroll">
            <div class="status-card">
                <div class="icon-box">
                    <i data-lucide="code-2" style="width: 40px; height: 40px;"></i>
                </div>
                <h1 style="font-size: 1.8rem; margin-bottom: 1rem;">Fitur Sedang Dikembangkan</h1>
                <p style="color: var(--text-muted); line-height: 1.6;">
                    Halaman <strong><?php echo $title_page; ?></strong> saat ini sedang dalam proses integrasi sistem oleh pengembang Aula System.
                </p>
                <div class="progress-bar">
                    <div class="progress-fill"></div>
                </div>
                <p style="font-size: 0.8rem; color: #94a3b8;">
                    Mohon tunggu pembaruan otomatis pada versi berikutnya.
                </p>
                <div style="margin-top: 3rem;">
                    <a href="<?php echo base_url(''); ?>" style="background: var(--primary); color: white; padding: 12px 25px; border-radius: 10px; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 8px;">
                        <i data-lucide="home" style="width: 18px;"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    lucide.createIcons();
    function toggleDropdown(el) {
        el.parentElement.classList.toggle('open');
        const content = el.nextElementSibling;
        content.style.display = el.parentElement.classList.contains('open') ? 'flex' : 'none';
    }
</script>

</body>
</html>


