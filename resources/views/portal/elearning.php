<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$role_id = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : 0;
$roleName = "Siswa";
$profileUrl = '/profil-siswa';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Learning - <?php echo $nama; ?></title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
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
        
        .sidebar { width: var(--sidebar-width); background-color: var(--sidebar-bg); color: var(--sidebar-text); display: flex; flex-direction: column; transition: all 0.3s ease; position: relative; z-index: 100; }
        .sidebar-header { height: var(--header-height); display: flex; align-items: center; justify-content: center; border-bottom: 1px solid rgba(255, 255, 255, 0.1); font-size: 1.25rem; font-weight: 700; color: #fff; gap: 10px; }
        .sidebar-scroll { flex: 1; overflow-y: auto; padding: 1rem 0; }
        .sidebar-scroll::-webkit-scrollbar { width: 5px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: rgba(0,0,0,0.1); }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }
        .nav-category { font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: rgba(255, 255, 255, 0.6); padding: 1rem 1.5rem 0.5rem; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 0.75rem 1.5rem; color: var(--sidebar-text); text-decoration: none; transition: 0.2s; font-size: 0.95rem; }
        .nav-item:hover { background-color: var(--sidebar-hover); color: #fff; }
        .nav-item.active { background-color: var(--sidebar-active); color: #fff; border-left: 4px solid #fff; }
        .nav-item i { width: 18px; height: 18px; opacity: 0.8; }
        .nav-item.active i { opacity: 1; }
        
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .header { height: var(--header-height); background-color: transparent; border-bottom: none; display: flex; align-items: center; justify-content: space-between; padding: 0 1.5rem; z-index: 10; }
        .header-left { display: flex; align-items: center; gap: 15px; }
        .menu-toggle { display: none; background: none; border: none; color: var(--text-main); cursor: pointer; padding: 5px; }
        .content-scroll { flex: 1; overflow-y: auto; padding: 1.5rem; }

        .premium-banner { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); border-radius: 24px; padding: 40px 35px; color: white; margin-bottom: 30px; position: relative; overflow: hidden; }
        .welcome-title { font-weight: 800; font-size: 32px; margin-bottom: 10px; position: relative; z-index: 10; }
        .welcome-subtitle { font-size: 16px; opacity: 0.95; margin: 0; font-weight: 500; position: relative; z-index: 10; }
        .badge-wrap { margin-top: 25px; display: flex; gap: 12px; flex-wrap: wrap; position: relative; z-index: 10; }
        .badge-capsule { background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(5px); padding: 6px 18px; border-radius: 50px; font-size: 13px; font-weight: 600; border: 1px solid rgba(255,255,255,0.3); display: flex; align-items: center; gap: 8px; }

        .section-title { font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; }
        .exam-table-card { background: white; border-radius: 16px; border: 1px solid var(--border); overflow: hidden; margin-bottom: 2rem; box-shadow: 0 4px 24px rgba(7,31,20,0.04); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; padding: 15px; text-align: left; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); border-bottom: 1px solid var(--border); }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        
        .btn-ujian { padding: 8px 16px; border-radius: 8px; font-weight: 700; text-decoration: none; font-size: 0.85rem; transition: 0.2s; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; border: none; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-warning { background: #f59e0b; color: white; }
        
        .status-pill { padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; }
        .status-success { background: #dcfce7; color: #0ea5e9; }
        .status-danger { background: #fee2e2; color: #ef4444; }
        .status-muted { background: #f1f5f9; color: #94a3b8; }

        .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }

        @media (max-width: 768px) {
            .menu-toggle { display: block !important; }
            .sidebar { position: fixed; left: -250px; height: 100vh; }
            .sidebar.active { left: 0; }
            .sidebar-overlay.active { display: block; }
            .header-right > div:first-child { display: none !important; }
        }
    </style>
</head>
<body>

<div class="wrapper">
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
            <a href="<?php echo base_url('portal/ghcbt-siswa'); ?>" class="nav-item">
                <i data-lucide="monitor"></i>
                <span>GHCBT System</span>
            </a>
            <a href="<?php echo base_url('absen-saya'); ?>" class="nav-item">
                <i data-lucide="calendar-check"></i>
                <span>Absen Saya</span>
            </a>
            <a href="<?php echo base_url('keuangan'); ?>" class="nav-item">
                <i data-lucide="wallet"></i>
                <span>Keuangan Saya</span>
            </a>
            <a href="javascript:void(0)" onclick="alert('Coming Soon')" class="nav-item">
                <i data-lucide="file-spreadsheet"></i>
                <span>E-Raport</span>
            </a>
            <a href="<?php echo base_url('elearning'); ?>" class="nav-item active">
                <i data-lucide="video"></i>
                <span>E-Learning (Tugas)</span>
            </a>

            <div class="nav-category">Lainnya</div>
            <a href="javascript:void(0)" onclick="alert('Coming Soon')" class="nav-item">
                <i data-lucide="settings"></i>
                <span>Pengaturan Akun</span>
            </a>
            
            <a href="<?php echo base_url('logout'); ?>" class="nav-item text-danger" style="margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.5rem; color: #fca5a5 !important;">
                <i data-lucide="log-out"></i>
                <span>Keluar (Logout)</span>
            </a>
        </div>
    </aside>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <main class="main-content">
        <header class="header">
            <div class="header-left">
                <button class="menu-toggle" onclick="toggleSidebar()"><i data-lucide="menu"></i></button>
                <div style="font-weight: 700; color: var(--primary);">Pusat Materi dan Tugas Digital</div>
            </div>
            <div class="header-right" style="display: flex; align-items: center; gap: 15px;">
                <div style="text-align: right;">
                    <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-main);"><?php echo $nama; ?></div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo $roleName; ?></div>
                </div>
                <div style="width: 36px; height: 36px; border-radius: 50%; overflow: hidden; border: 2px solid var(--primary); background: #0ea5e9; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                    <?php if (isset($foto) && $foto): ?>
                        <img src="<?php echo base_url('uploads/siswa/<?php echo $foto; ?>'); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <?php echo substr(htmlspecialchars($nama), 0, 1); ?>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <div class="content-scroll">
            <div class="premium-banner">
                <h1 class="welcome-title">Pusat E-Learning</h1>
                <p class="welcome-subtitle">Akses materi dan kerjakan tugas secara digital dari sini.</p>
                <div class="badge-wrap">
                    <span class="badge-capsule"><i data-lucide="book-open" style="width:14px;"></i> Terhubung ke SIAKAD</span>
                </div>
            </div>

            <div class="section-title"><i data-lucide="file-video" style="color: var(--primary);"></i> Daftar Materi Terbaru</div>
            <div class="exam-table-card">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th>Judul Materi</th>
                                <th>Mata Pelajaran</th>
                                <th>Guru Pengajar</th>
                                <th>Tipe</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            if(empty($materi)): ?>
                                <tr><td colspan="6" style="text-align:center; color:var(--text-muted); padding:3rem;">Tidak ada materi terbaru.</td></tr>
                            <?php else: foreach($materi as $m): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td style="font-weight: 700; color: var(--primary);"><?php echo htmlspecialchars($m['judul']); ?></td>
                                    <td><?php echo htmlspecialchars($m['nama_mapel']); ?></td>
                                    <td><?php echo htmlspecialchars($m['nama_guru']); ?></td>
                                    <td><span class="status-pill status-muted"><?php echo htmlspecialchars($m['tipe']); ?></span></td>
                                    <td>
                                        <?php 
                                        $is_video = false;
                                        $embed_url = '';
                                        if ($m['is_link']) {
                                            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $m['file_path'], $match)) {
                                                $is_video = true;
                                                $embed_url = "https://www.youtube.com/embed/" . $match[1];
                                            }
                                            elseif (preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/', $m['file_path'], $match)) {
                                                $is_video = true;
                                                $embed_url = "https://drive.google.com/file/d/" . $match[1] . "/preview";
                                            }
                                            elseif (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $m['file_path'], $match)) {
                                                $is_video = true;
                                                $embed_url = "https://drive.google.com/file/d/" . $match[1] . "/preview";
                                            }
                                        }
                                        if($m['is_link']): 
                                            if($is_video): ?>
                                                <button onclick="playVideo('<?php echo $embed_url; ?>')" class="btn-ujian btn-primary"><i data-lucide="play-circle" style="width:14px;"></i> Putar Video</button>
                                            <?php else: ?>
                                                <a href="<?php echo htmlspecialchars($m['file_path']); ?>" target="_blank" class="btn-ujian btn-primary"><i data-lucide="external-link" style="width:14px;"></i> Buka Link</a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <a href="<?php echo base_url('uploads/elearning/<?php echo htmlspecialchars($m['file_path']); ?>'); ?>" target="_blank" class="btn-ujian btn-warning"><i data-lucide="download" style="width:14px;"></i> Unduh</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="section-title"><i data-lucide="clipboard-list" style="color: var(--primary);"></i> Daftar Tugas & Kuis</div>
            <div class="exam-table-card">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th>Nama Tugas</th>
                                <th>Batas Waktu</th>
                                <th>Mata Pelajaran</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            if(empty($tugas)): ?>
                                <tr><td colspan="6" style="text-align:center; color:var(--text-muted); padding:3rem;">Belum ada tugas.</td></tr>
                            <?php else: foreach($tugas as $t): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td style="font-weight: 700;"><?php echo htmlspecialchars($t['judul']); ?></td>
                                    <td style="font-size: 0.85rem; color: #ef4444; font-weight: bold;"><?php echo date('d/m/Y H:i', strtotime($t['tgl_tutup'])); ?></td>
                                    <td><?php echo htmlspecialchars($t['nama_mapel']); ?></td>
                                    <td>
                                        <?php if($t['status_kumpul']): ?>
                                            <span class="status-pill status-success">Dikumpulkan</span>
                                        <?php else: ?>
                                            <span class="status-pill status-danger">Belum</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo base_url('elearning/tugas/detail/<?php echo $t['id']; ?>'); ?>" class="btn-ujian btn-primary"><i data-lucide="eye" style="width:14px;"></i> Detail</a>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal Pemutar Video Premium -->
<div id="videoModal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(7, 31, 20, 0.85); backdrop-filter: blur(8px); align-items:center; justify-content:center;">
    <div style="position:relative; width:90%; max-width:800px; background:#000; border-radius:16px; overflow:hidden; box-shadow:0 20px 50px rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1);">
        <button onclick="closeVideoModal()" style="position:absolute; right:15px; top:15px; background:rgba(255,255,255,0.2); border:none; border-radius:50%; width:36px; height:36px; color:#fff; font-size:20px; cursor:pointer; display:flex; align-items:center; justify-content:center; z-index:10; transition: 0.2s;">&times;</button>
        <div style="position:relative; padding-bottom:56.25%; height:0; overflow:hidden;">
            <iframe id="videoPlayer" style="position:absolute; top:0; left:0; width:100%; height:100%; border:none;" allowfullscreen allow="autoplay; encrypted-media"></iframe>
        </div>
    </div>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
        document.getElementById('sidebarOverlay').classList.toggle('active');
    }
    
    function playVideo(url) {
        var modal = document.getElementById('videoModal');
        var player = document.getElementById('videoPlayer');
        player.src = url;
        modal.style.display = 'flex';
    }
    
    function closeVideoModal() {
        var modal = document.getElementById('videoModal');
        var player = document.getElementById('videoPlayer');
        player.src = '';
        modal.style.display = 'none';
    }

    lucide.createIcons();
</script>
</body>
</html>


