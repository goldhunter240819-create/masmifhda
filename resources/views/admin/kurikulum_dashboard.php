<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . base_url('login'));
    exit;
}
$adminNama = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Admin';
$adminInitial = strtoupper(substr($adminNama, 0, 2));
$active_page   = 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title = 'Waka Kurikulum'; include __DIR__ . '/_zamrud_head.php'; ?>
    <style>
        .summary-card {
            background: #fff;
            border-radius: var(--z-radius);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            border: 1px solid var(--z-border);
            transition: all 0.3s;
        }
        .summary-card:hover {
            border-color: var(--z-primary);
            box-shadow: 0 10px 15px -3px rgba(14, 165, 233, 0.1);
        }
        .summary-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .bg-blue { background: #eff6ff; color: #3b82f6; }
        .bg-rose { background: #fff1f2; color: #e11d48; }
        .bg-amber { background: #fffbeb; color: #f59e0b; }
        .bg-emerald { background: #ecfdf5; color: #10b981; }

        .summary-info h3 {
            font-size: 1.8rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            line-height: 1.2;
        }
        .summary-info p {
            margin: 0;
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 600;
        }
        
        /* Grid untuk action cards */
        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }
        .action-card {
            background: #fff;
            border: 1px solid var(--z-border);
            border-radius: var(--z-radius);
            padding: 1.25rem;
            text-align: center;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
        }
        .action-card:hover {
            border-color: var(--z-primary);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        .action-card i {
            width: 32px;
            height: 32px;
            color: var(--z-primary);
            margin-bottom: 0.75rem;
        }
        .action-card h4 {
            font-size: 1rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 0.25rem 0;
        }
        .action-card p {
            font-size: 0.8rem;
            color: #64748b;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="z-wrap">
        <?php include __DIR__ . '/_sidebar_kurikulum.php'; ?>
        
        <main class="z-main">
            <header class="z-header">
                <div class="z-header-left">
                    <button class="z-hamburger" id="zHamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
                    <span class="z-header-title">Dashboard Kurikulum</span>
                    <span class="z-badge">Modul Utama</span>
                </div>
                <div class="z-header-user">
                    <div class="z-user-info">
                        <div class="name"><?php echo $adminNama; ?></div>
                        <div class="role">Administrator</div>
                    </div>
                    <div class="z-avatar"><?php echo $adminInitial; ?></div>
                </div>
            </header>
            
            <div class="z-scroll">
                <div class="elite-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: var(--z-radius); margin-bottom: 2rem; color: white;">
                    <div>
                        <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                            <i data-lucide="layout-dashboard" style="color: rgba(255,255,255,0.8);"></i> Dashboard Kurikulum
                        </h1>
                        <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Pantau pelaksanaan KBM, kelengkapan administrasi guru, dan instrumen penilaian.</p>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:1.5rem; margin-bottom:1.5rem;">
                    <div style="background:white; border-radius:16px; padding:1.5rem; border:1px solid var(--z-border); box-shadow:0 4px 6px -1px rgba(0,0,0,0.02); display:flex; align-items:center; gap:1.25rem; cursor:pointer;" onclick="location.href='<?= base_url('admin/kurikulum/dalam-pengembangan') ?>'">
                        <div style="width:50px; height:50px; background:#eff6ff; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#3b82f6;">
                            <i data-lucide="users" style="width:24px;height:24px;"></i>
                        </div>
                        <div>
                            <div style="font-size:0.8rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Total Guru</div>
                            <div style="font-size:1.8rem; font-weight:800; color:#0f172a;">0</div>
                        </div>
                    </div>
                    
                    <div style="background:white; border-radius:16px; padding:1.5rem; border:1px solid var(--z-border); box-shadow:0 4px 6px -1px rgba(0,0,0,0.02); display:flex; align-items:center; gap:1.25rem; cursor:pointer;" onclick="location.href='<?= base_url('admin/kurikulum/dalam-pengembangan') ?>'">
                        <div style="width:50px; height:50px; background:#f0fdf4; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#22c55e;">
                            <i data-lucide="folder-check" style="width:24px;height:24px;"></i>
                        </div>
                        <div>
                            <div style="font-size:0.8rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Supervisi Administrasi</div>
                            <div style="font-size:1.8rem; font-weight:800; color:#0f172a;">0 <span style="font-size:1rem; color:#94a3b8; font-weight:600;">/ 0</span></div>
                        </div>
                    </div>
                    
                    <div style="background:white; border-radius:16px; padding:1.5rem; border:1px solid var(--z-border); box-shadow:0 4px 6px -1px rgba(0,0,0,0.02); display:flex; align-items:center; gap:1.25rem; cursor:pointer;" onclick="location.href='<?= base_url('admin/kurikulum/dalam-pengembangan') ?>'">
                        <div style="width:50px; height:50px; background:#fef2f2; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#ef4444;">
                            <i data-lucide="monitor-play" style="width:24px;height:24px;"></i>
                        </div>
                        <div>
                            <div style="font-size:0.8rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Supervisi Kelas</div>
                            <div style="font-size:1.8rem; font-weight:800; color:#0f172a;">0 <span style="font-size:1rem; color:#94a3b8; font-weight:600;">/ 0</span></div>
                        </div>
                    </div>
                </div>

                <div class="z-panel" style="background:white; border-radius:var(--z-radius); border:1px solid var(--z-border); overflow:hidden;">
                    <div class="z-panel-head" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--z-border); display: flex; justify-content: space-between; align-items: center;">
                        <div class="z-panel-title" style="font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                            <i data-lucide="clock" style="width:18px;height:18px;color:#64748b;"></i> Supervisi Kelas Terbaru
                        </div>
                        <a href="<?= base_url('admin/kurikulum/dalam-pengembangan') ?>" class="z-btn-primary" style="padding: 0.4rem 1rem; border-radius: 6px; font-size: 0.8rem; text-decoration: none;">Lihat Semua</a>
                    </div>
                    <div class="z-panel-body" style="padding:0;">
                        <div class="z-table-wrap">
                            <table class="z-table" style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #f8fafc; text-align: left; border-bottom: 1px solid var(--z-border);">
                                        <th style="padding: 1rem 1.5rem; font-size: 0.8rem; color: #64748b; text-transform: uppercase;">Tanggal</th>
                                        <th style="padding: 1rem 1.5rem; font-size: 0.8rem; color: #64748b; text-transform: uppercase;">Nama Guru</th>
                                        <th style="padding: 1rem 1.5rem; font-size: 0.8rem; color: #64748b; text-transform: uppercase;">Mata Pelajaran</th>
                                        <th style="padding: 1rem 1.5rem; font-size: 0.8rem; color: #64748b; text-transform: uppercase;">Kelas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="4" style="text-align:center; padding: 2rem; color: #64748b;">Belum ada data supervisi kelas.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
    <script>
        lucide.createIcons();
        function zToggleSidebar() {
            document.querySelector('.z-sidebar').classList.toggle('open');
        }
    </script>
</body>
</html>
