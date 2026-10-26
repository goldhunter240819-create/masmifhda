<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$roleName = "Pengguna";
if(isset($_SESSION['role_id'])) {
    if($_SESSION['role_id'] == 2) $roleName = "Guru / Tendik";
    elseif($_SESSION['role_id'] == 3) $roleName = "Siswa";
}
$profileUrl = "#";
if(isset($_SESSION['role_id'])) {
    if($_SESSION['role_id'] == 2) $profileUrl = "/profil-guru";
    elseif($_SESSION['role_id'] == 3) $profileUrl = "/profil-siswa";
}

// Attendance Logic
$conn_absen = mysqli_connect("127.0.0.1", "root", "", "absen_db");
$absen_msg = "Sistem absensi belum terhubung.";
$absen_status_today = "belum_absen";
$recap_data = [];

if ($conn_absen && isset($identitas)) {
    $tipe_absen = ($_SESSION['role_id'] == 2) ? 'guru' : 'siswa';
    $q_user = mysqli_query($conn_absen, "SELECT id FROM users WHERE qr_code = '$identitas' AND tipe = '$tipe_absen'");
    $u_absen = mysqli_fetch_assoc($q_user);
    
    if ($u_absen) {
        $absen_user_id = $u_absen['id'];
        $today = date('Y-m-d');
        $now = date('H:i:s');
        
        // Get Settings
        $q_set = mysqli_query($conn_absen, "SELECT * FROM pengaturan LIMIT 1");
        $sett = mysqli_fetch_assoc($q_set);
        
        // Check Today Attendance
        $q_today = mysqli_query($conn_absen, "SELECT * FROM absensi WHERE user_id = '$absen_user_id' AND tanggal = '$today'");
        $today_data = mysqli_fetch_assoc($q_today);
        
        if ($today_data) {
            if (isset($today_data['jam_keluar']) && $today_data['jam_keluar'] && $today_data['jam_keluar'] != '00:00:00') {
                $absen_msg = "Halo <b>$nama</b>, Anda sudah absen masuk & pulang hari ini. Selamat beristirahat!";
                $absen_status_today = "selesai";
            } else {
                $absen_msg = "Halo <b>$nama</b>, Anda sudah absen masuk hari ini. Selamat belajar & bekerja!";
                $absen_status_today = "masuk";
                
                // If it's time to go home
                if ($now >= $sett['mulai_keluar']) {
                    $absen_msg = "Halo <b>$nama</b>, Waktunya pulang! Jangan lupa untuk melakukan absen pulang.";
                    $absen_status_today = "waktunya_pulang";
                }
            }
        } else {
            // Haven't attended yet
            if ($now >= $sett['mulai_masuk'] && $now <= $sett['akhir_masuk']) {
                $absen_msg = "Halo <b>$nama</b>, Anda belum absen masuk pagi ini. Silakan lakukan absensi sekarang!";
                $absen_status_today = "belum_masuk";
            } else {
                $absen_msg = "Halo <b>$nama</b>, Belum masuk jam absensi atau sudah terlewat. Silakan hubungi admin jika ada kendala.";
                $absen_status_today = "diluar_jam";
            }
        }

        // Monthly Recap Filter
        $filter_month = isset($_GET['m']) ? $_GET['m'] : date('m');
        $filter_year = isset($_GET['y']) ? $_GET['y'] : date('Y');
        
        $q_recap = mysqli_query($conn_absen, "SELECT * FROM absensi WHERE user_id = '$absen_user_id' AND MONTH(tanggal) = '$filter_month' AND YEAR(tanggal) = '$filter_year' ORDER BY tanggal DESC");
        while($r = mysqli_fetch_assoc($q_recap)) {
            $recap_data[] = $r;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Saya - Aula System</title>
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

        .page-title { font-weight: 800; font-size: 24px; margin-bottom: 5px; }
        .page-subtitle { color: var(--text-muted); font-size: 14px; margin-bottom: 25px; }

        .absensi-grid { display: grid; grid-template-columns: 1fr 2.5fr; gap: 1.5rem; }
        @media (max-width: 992px) { .absensi-grid { grid-template-columns: 1fr; } }
        
        .qr-box { background: #fff; padding: 2rem; border-radius: 16px; text-align: center; border: 1px solid var(--border); box-shadow: 0 4px 24px rgba(7,31,20,0.04); }
        .table-card { background: #fff; border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 4px 24px rgba(7,31,20,0.04); padding: 1.5rem; overflow: hidden; }
        
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th { background: #f8fafc; padding: 12px 15px; text-align: left; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); border-bottom: 2px solid var(--border); }
        td { padding: 12px 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        
        .status-pill { padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; display: inline-block;}
        .status-success { background: #dcfce7; color: #16a34a; }
        .status-warning { background: #fef3c7; color: #d97706; }
        .status-danger { background: #fee2e2; color: #dc2626; }
        .status-primary { background: #e0e7ff; color: #4f46e5; }
        .status-muted { background: #f1f5f9; color: #94a3b8; }

        .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }

        .btn { padding: 8px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 0.85rem; border: none; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-dark { background: #0f172a; color: white; }

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
            <?php if(isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2): ?>
            <a href="<?php echo base_url('public/GHCBT/manager'); ?>" class="nav-item">
                <i data-lucide="monitor"></i>
                <span>GHCBT Admin</span>
            </a>
            <?php else: ?>
            <a href="<?php echo base_url('portal/ghcbt-siswa'); ?>" class="nav-item">
                <i data-lucide="monitor"></i>
                <span>GHCBT System</span>
            </a>
            <?php endif; ?>
            <a href="<?php echo base_url('absen-saya'); ?>" class="nav-item active">
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
            <a href="<?php echo base_url('elearning'); ?>" class="nav-item">
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
                <div style="font-weight: 700; color: var(--primary);">Data Kehadiran</div>
            </div>
            <div class="header-right" style="display: flex; align-items: center; gap: 15px;">
                <div style="text-align: right;">
                    <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-main);"><?php echo $nama; ?></div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo $roleName; ?></div>
                </div>
                <div style="width: 36px; height: 36px; border-radius: 50%; overflow: hidden; border: 2px solid var(--primary); background: white; display: flex; align-items: center; justify-content: center; font-weight: bold; color: var(--primary);">
                    <?php if(isset($foto) && $foto): ?>
                        <img src="<?php echo base_url('uploads/<?php echo ($_SESSION['role_id']==2?'guru':'siswa'); ?>/<?php echo $foto; ?>'); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <?php echo substr(htmlspecialchars($nama), 0, 1); ?>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <div class="content-scroll">
            <h1 class="page-title">Rekapitulasi Absensi</h1>
            <p class="page-subtitle">Pantau status kehadiran dan cetak laporan absensi Anda.</p>

            <!-- Status Banner -->
            <?php if($absen_status_today == "selesai"): ?>
                <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-left:4px solid #22c55e; border-radius:12px; padding:1.2rem; margin-bottom:1.5rem; display:flex; align-items:center; gap:15px;">
                    <i data-lucide="check-circle-2" style="width:28px; color:#16a34a; flex-shrink:0;"></i>
                    <div style="color:#166534;">
                        <strong style="display:block; font-size:1rem; margin-bottom:3px;">Absensi Selesai</strong>
                        <?php echo $absen_msg; ?>
                    </div>
                </div>
            <?php elseif($absen_status_today == "masuk"): ?>
                <div style="background:#eff6ff; border:1px solid #bfdbfe; border-left:4px solid #3b82f6; border-radius:12px; padding:1.2rem; margin-bottom:1.5rem; display:flex; align-items:center; gap:15px;">
                    <i data-lucide="info" style="width:28px; color:#2563eb; flex-shrink:0;"></i>
                    <div style="color:#1e3a8a;">
                        <strong style="display:block; font-size:1rem; margin-bottom:3px;">Sedang Aktif</strong>
                        <?php echo $absen_msg; ?>
                    </div>
                </div>
            <?php elseif($absen_status_today == "waktunya_pulang"): ?>
                <div style="background:#fffbeb; border:1px solid #fde68a; border-left:4px solid #f59e0b; border-radius:12px; padding:1.2rem; margin-bottom:1.5rem; display:flex; align-items:center; gap:15px;">
                    <i data-lucide="clock" style="width:28px; color:#d97706; flex-shrink:0;"></i>
                    <div style="color:#92400e;">
                        <strong style="display:block; font-size:1rem; margin-bottom:3px;">Waktunya Pulang</strong>
                        <?php echo $absen_msg; ?>
                    </div>
                </div>
            <?php else: ?>
                <div style="background:#fef2f2; border:1px solid #fecaca; border-left:4px solid #ef4444; border-radius:12px; padding:1.2rem; margin-bottom:1.5rem; display:flex; align-items:center; gap:15px;">
                    <i data-lucide="alert-circle" style="width:28px; color:#dc2626; flex-shrink:0;"></i>
                    <div style="color:#991b1b;">
                        <strong style="display:block; font-size:1rem; margin-bottom:3px;">Perhatian</strong>
                        <?php echo $absen_msg; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="absensi-grid">
                <!-- Identitas / QR Code -->
                <div>
                    <div class="qr-box">
                        <h3 style="margin:0 0 1rem 0; font-size:1.1rem; color:var(--text-main);">Identitas Absensi</h3>
                        
                        <?php
                            $ident_val = isset($identitas) ? $identitas : '-';
                        ?>
                        <script src="<?php echo base_url('public/assets/js/qrcode.min.js'); ?>"></script>
                        <div id="qrcode" style="display:flex; justify-content:center; margin-bottom:1rem;"></div>
                        <script>
                            new QRCode(document.getElementById("qrcode"), {
                                text: "<?php echo $ident_val; ?>",
                                width: 160,
                                height: 160,
                                colorDark : "#000000",
                                colorLight : "#ffffff",
                                correctLevel : QRCode.CorrectLevel.H
                            });
                            // Style the canvas/img created by QRCode
                            setTimeout(() => {
                                let qrImg = document.querySelector('#qrcode img');
                                if(qrImg) {
                                    qrImg.style.border = '8px solid #f8fafc';
                                    qrImg.style.borderRadius = '10px';
                                    qrImg.style.boxShadow = '0 2px 10px rgba(0,0,0,0.05)';
                                }
                            }, 100);
                        </script>
                        
                        <div style="font-family:monospace; font-size:1.2rem; font-weight:800; letter-spacing:2px; color:var(--primary); background:#f0f9ff; padding:8px; border-radius:8px;">
                            <?php echo htmlspecialchars($ident_val); ?>
                        </div>
                        <p style="font-size:0.8rem; color:var(--text-muted); margin-top:1rem;">Gunakan QR Code ini untuk tap di mesin absensi atau berikan nomor identitas.</p>
                    </div>
                </div>

                <!-- Rekap Bulanan -->
                <div>
                    <div class="table-card">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:10px;">
                            <h3 style="margin:0; font-size:1.1rem;">Rekapitulasi Bulan Ini</h3>
                            
                            <form action="" method="GET" style="display:flex; gap:10px;">
                                <select name="m" style="padding:6px 12px; border-radius:8px; border:1px solid var(--border); font-family:inherit; outline:none;">
                                    <?php
                                    $months = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
                                    $filter_month = isset($_GET['m']) ? $_GET['m'] : date('m');
                                    for($i=1; $i<=12; $i++):
                                        $m_val = str_pad($i, 2, "0", STR_PAD_LEFT);
                                        $sel = ($m_val == $filter_month) ? 'selected' : '';
                                    ?>
                                    <option value="<?php echo $m_val; ?>" <?php echo $sel; ?>><?php echo $months[$i-1]; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select name="y" style="padding:6px 12px; border-radius:8px; border:1px solid var(--border); font-family:inherit; outline:none;">
                                    <?php
                                    $filter_year = isset($_GET['y']) ? $_GET['y'] : date('Y');
                                    $y_now = date('Y');
                                    for($i=$y_now; $i>=$y_now-2; $i--):
                                        $sel = ($i == $filter_year) ? 'selected' : '';
                                    ?>
                                    <option value="<?php echo $i; ?>" <?php echo $sel; ?>><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="<?php echo base_url('portal/export-absen?identitas=<?php echo $identitas(isset($identitas) ? $identitas : ''); ?>&m=<?php echo $filter_month; ?>&y=<?php echo $filter_year; ?>&v=absen'); ?>" target="_blank" class="btn btn-dark"><i data-lucide="printer" style="width:14px;"></i> Cetak</a>
                            </form>
                        </div>

                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th style="text-align:center;">Status</th>
                                        <th style="text-align:center;">Jam Masuk</th>
                                        <th style="text-align:center;">Jam Keluar</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($recap_data)): ?>
                                    <tr><td colspan="5" style="text-align:center; padding:2rem; color:var(--text-muted);">Belum ada data absensi pada bulan ini.</td></tr>
                                    <?php else: foreach($recap_data as $r): ?>
                                    <tr>
                                        <td style="font-weight:600;"><?php echo date('d M Y', strtotime($r['tanggal'])); ?></td>
                                        <td style="text-align:center;">
                                            <?php 
                                            if($r['status'] == 'Hadir') echo '<span class="status-pill status-success">Hadir</span>';
                                            elseif($r['status'] == 'Terlambat') echo '<span class="status-pill status-warning">Terlambat</span>';
                                            elseif($r['status'] == 'Izin' || $r['status'] == 'Sakit') echo '<span class="status-pill status-primary">'.$r['status'].'</span>';
                                            elseif($r['status'] == 'Alpa') echo '<span class="status-pill status-danger">Alpa</span>';
                                            else echo '<span class="status-pill status-muted">'.$r['status'].'</span>';
                                            ?>
                                        </td>
                                        <td style="text-align:center; font-family:monospace;"><?php echo ($r['jam_masuk'] && $r['jam_masuk'] != '00:00:00') ? $r['jam_masuk'] : '-'; ?></td>
                                        <td style="text-align:center; font-family:monospace;"><?php echo ($r['jam_keluar'] && $r['jam_keluar'] != '00:00:00') ? $r['jam_keluar'] : '-'; ?></td>
                                        <td style="font-size:0.85rem; color:var(--text-muted);"><?php echo $r['keterangan'] ? $r['keterangan'] : '-'; ?></td>
                                    </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
        document.getElementById('sidebarOverlay').classList.toggle('active');
    }
    lucide.createIcons();
</script>
</body>
</html>



