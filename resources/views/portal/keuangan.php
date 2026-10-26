<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
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
        
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            position: relative;
            z-index: 100;
        }

        .sidebar-header {
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 1px;
            color: #fff;
            gap: 10px;
        }

        .sidebar-scroll { flex: 1; overflow-y: auto; padding: 1rem 0; }
        .nav-category { font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: rgba(255, 255, 255, 0.6); padding: 1rem 1.5rem 0.5rem; letter-spacing: 1px; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 0.75rem 1.5rem; color: var(--sidebar-text); text-decoration: none; transition: 0.2s; font-size: 0.95rem; }
        .nav-item:hover { background-color: var(--sidebar-hover); color: #fff; }
        .nav-item.active { background-color: var(--sidebar-active); color: #fff; border-left: 4px solid #fff; }
        .nav-item i { width: 18px; height: 18px; opacity: 0.8; }
        .nav-item.active i { opacity: 1; }
        
        .nav-dropdown { position: relative; }
        .nav-dropdown-content { display: none; background: rgba(0,0,0,0.15); flex-direction: column; }
        .nav-dropdown.open .nav-dropdown-content { display: flex; }
        .nav-dropdown-item { padding: 0.6rem 1.5rem 0.6rem 3.5rem; color: rgba(255,255,255,0.7); text-decoration: none; font-size: 0.85rem; transition: 0.2s; border-left: 3px solid transparent; }
        .nav-dropdown-item:hover { color: #fff; background: rgba(255,255,255,0.05); border-left-color: var(--sidebar-active); }
        .chevron { margin-left: auto; transition: 0.3s; width: 14px !important; height: 14px !important; opacity: 0.5; }
        .nav-dropdown.open .chevron { transform: rotate(90deg); opacity: 1; }

        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 90; }

        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .header { height: var(--header-height); background-color: transparent; border-bottom: none; display: flex; align-items: center; justify-content: space-between; padding: 0 1.5rem; z-index: 10; }
        .header-left { display: flex; align-items: center; gap: 15px; }
        .menu-toggle { display: none; background: none; border: none; color: var(--text-main); cursor: pointer; padding: 5px; }
        .header-title { font-size: 1.1rem; font-weight: 600; }
        
        .content-scroll { flex: 1; overflow-y: auto; padding: 1.5rem; }

        .finance-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .finance-card { background: white; padding: 1.5rem; border-radius: 16px; border: 1px solid var(--border); display: flex; align-items: center; gap: 15px; box-shadow: 0 4px 24px rgba(7,31,20,0.04); }
        .finance-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
        .icon-blue { background: #eff6ff; color: #3b82f6; }
        .icon-green { background: #f0fdf4; color: #0ea5e9; }
        .icon-red { background: #fef2f2; color: #ef4444; }
        .finance-info h4 { font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px; }
        .finance-info .value { font-size: 1.25rem; font-weight: 800; color: var(--text-main); }

        .panel { background: white; border-radius: 16px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 4px 24px rgba(7,31,20,0.04); }
        .panel-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        .panel-title { font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 10px; }
        
        .table-responsive { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; padding: 12px 20px; text-align: left; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); border-bottom: 1px solid var(--border); }
        td { padding: 15px 20px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
        .status-pill { padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; }
        .status-success { background: #dcfce7; color: #0ea5e9; }

        @media (max-width: 768px) {
            .menu-toggle { display: block !important; }
            .sidebar { position: fixed; left: -250px; height: 100vh; box-shadow: 10px 0 30px rgba(0,0,0,0.1); }
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
            <?php if($_SESSION['role_id'] == 2): ?>
            <div class="nav-dropdown">
                <a href="javascript:void(0)" class="nav-item" onclick="this.parentElement.classList.toggle('open')">
                    <i data-lucide="monitor"></i>
                    <span>GHCBT System</span>
                    <i data-lucide="chevron-right" class="chevron"></i>
                </a>
                <div class="nav-dropdown-content">
                    <a href="<?php echo base_url('portal/evaluasi'); ?>" class="nav-dropdown-item">Evaluasi</a>
                    <a href="<?php echo base_url('portal/hasil-ujian'); ?>" class="nav-dropdown-item">Hasil Ujian</a>
                    <a href="<?php echo base_url('portal/analisis-soal'); ?>" class="nav-dropdown-item">Analisis Butir Soal</a>
                </div>
            </div>
            <?php else: ?>
            <a href="<?php echo base_url('portal/ghcbt-siswa'); ?>" class="nav-item">
                <i data-lucide="monitor"></i>
                <span>GHCBT System</span>
            </a>
            <?php endif; ?>

            <a href="<?php echo base_url('absen-saya'); ?>" class="nav-item">
                <i data-lucide="calendar-check"></i>
                <span>Absen Saya</span>
            </a>
            <a href="<?php echo base_url('keuangan'); ?>" class="nav-item active">
                <i data-lucide="wallet"></i>
                <span>Keuangan Saya</span>
            </a>
            <a href="javascript:void(0)" onclick="alert('Coming Soon')" class="nav-item">
                <i data-lucide="file-spreadsheet"></i>
                <span>E-Raport</span>
            </a>
            <a href="javascript:void(0)" onclick="alert('Coming Soon')" class="nav-item">
                <i data-lucide="video"></i>
                <span>E-Learning</span>
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
                <button class="menu-toggle" onclick="toggleSidebar()">
                    <i data-lucide="menu"></i>
                </button>
                <div class="header-title">Informasi Keuangan</div>
            </div>
            <div class="header-right" style="display: flex; align-items: center; gap: 15px;">
                <div style="text-align: right;">
                    <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-main);"><?php echo $nama; ?></div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo $roleName; ?></div>
                </div>
                <div style="width: 36px; height: 36px; border-radius: 50%; overflow: hidden; border: 2px solid var(--primary); background: #0ea5e9; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                    <?php if ($foto): ?>
                        <img src="<?php echo ($_SESSION['role_id'] == 2 ? '/uploads/guru/' : '/uploads/siswa/') . $foto; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <?php echo substr(htmlspecialchars($nama), 0, 1); ?>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <div class="content-scroll">
            <?php if($_SESSION['role_id'] == 2): ?>
                <!-- GURU VIEW -->
                <div class="finance-grid">
                    <div class="finance-card">
                        <div class="finance-icon icon-blue"><i data-lucide="banknote"></i></div>
                        <div class="finance-info">
                            <h4>Gaji Pokok</h4>
                            <div class="value">Rp <?php echo number_format($finance['total_gaji'], 0, ',', '.'); ?></div>
                        </div>
                    </div>
                    <div class="finance-card">
                        <div class="finance-icon icon-green"><i data-lucide="trending-up"></i></div>
                        <div class="finance-info">
                            <h4>Insentif</h4>
                            <div class="value">Rp <?php echo number_format($finance['insentif'], 0, ',', '.'); ?></div>
                        </div>
                    </div>
                    <div class="finance-card">
                        <div class="finance-icon icon-red"><i data-lucide="minus-circle"></i></div>
                        <div class="finance-info">
                            <h4>Potongan</h4>
                            <div class="value">Rp <?php echo number_format($finance['potongan'], 0, ',', '.'); ?></div>
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title"><i data-lucide="history"></i> Riwayat Slip Gaji</div>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Bulan / Periode</th>
                                    <th>Total Diterima</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($finance['history'] as $h): ?>
                                <tr>
                                    <td style="font-weight: 700;"><?php echo $h['bulan']; ?></td>
                                    <td style="color: var(--primary); font-weight: 800;">Rp <?php echo number_format($h['total'], 0, ',', '.'); ?></td>
                                    <td><span class="status-pill status-success"><?php echo $h['status']; ?></span></td>
                                    <td><button style="background: none; border: 1px solid var(--border); padding: 5px 10px; border-radius: 6px; cursor: pointer; font-size: 0.8rem;"><i data-lucide="download" style="width:14px; vertical-align: middle;"></i> PDF</button></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php else: ?>
                <!-- SISWA VIEW -->
                <div class="finance-grid">
                    <div class="finance-card">
                        <div class="finance-icon icon-blue"><i data-lucide="clipboard-list"></i></div>
                        <div class="finance-info">
                            <h4>Total Tagihan</h4>
                            <div class="value">Rp <?php echo number_format($finance['total_tagihan'], 0, ',', '.'); ?></div>
                        </div>
                    </div>
                    <div class="finance-card">
                        <div class="finance-icon icon-green"><i data-lucide="check-circle"></i></div>
                        <div class="finance-info">
                            <h4>Terbayar</h4>
                            <div class="value">Rp <?php echo number_format($finance['terbayar'], 0, ',', '.'); ?></div>
                        </div>
                    </div>
                    <div class="finance-card">
                        <div class="finance-icon icon-red"><i data-lucide="alert-circle"></i></div>
                        <div class="finance-info">
                            <h4>Sisa Tunggakan</h4>
                            <div class="value">Rp <?php echo number_format($finance['sisa'], 0, ',', '.'); ?></div>
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title"><i data-lucide="history"></i> Riwayat Pembayaran SPP</div>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($finance['history'] as $h): ?>
                                <tr>
                                    <td><?php echo date('d M Y', strtotime($h['tgl'])); ?></td>
                                    <td style="font-weight: 700;"><?php echo $h['ket']; ?></td>
                                    <td style="color: var(--primary); font-weight: 800;">Rp <?php echo number_format($h['jml'], 0, ',', '.'); ?></td>
                                    <td><span class="status-pill status-success"><?php echo $h['status']; ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
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


