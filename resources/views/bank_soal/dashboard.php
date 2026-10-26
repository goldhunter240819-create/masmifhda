<?php
// Ensure this is accessed through the controller, if not set dummy vars
if (!isset($page_title)) $page_title = "Dashboard Bank Soal";
if (!isset($active_page)) $active_page = "dashboard";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - MASMIFHDA</title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>?v=<?php echo time(); ?>">
    <link href="<?php echo base_url('public/assets/css/zamrud.css'); ?>" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { display: flex; align-items: center; gap: 1.5rem; padding: 1.5rem; }
        .stat-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .stat-icon.blue { background: #e0f2fe; color: #0284c7; }
        .stat-icon.green { background: #dcfce7; color: #16a34a; }
        .stat-icon.purple { background: #f3e8ff; color: #9333ea; }
        .stat-info h3 { margin: 0; font-size: 1.8rem; font-weight: 800; color: #0f172a; line-height: 1.2; }
        .stat-info p { margin: 0; font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .welcome-hero::before {
            content: ''; position: absolute; inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.15' fill-rule='evenodd'%3E%3Ccircle cx='3' cy='3' r='3'/%3E%3Ccircle cx='13' cy='13' r='3'/%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none; opacity: 0.5;
        }
    </style>
</head>
<body class="z-bg">

<div class="z-wrap">
    <?php include __DIR__ . '/_sidebar.php'; ?>

    <main class="z-main">
        <?php 
        $page_badge = 'Bank Soal';
        include __DIR__ . '/../siakad/_siakad_header.php'; 
        ?>

        <div class="z-content">
            <!-- Hero Banner -->
            <div class="elite-page-header welcome-hero" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: var(--z-radius, 12px); margin-bottom: 2rem; color: white; position: relative; overflow: hidden;">
                <div style="position: relative; z-index: 1;">
                    <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="database" style="color: rgba(255,255,255,0.8);"></i> Bank Soal
                    </h1>
                    <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Manajemen repositori soal terpusat untuk ujian dan latihan.</p>
                </div>
            </div>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="z-card stat-card">
                    <div class="stat-icon blue"><i data-lucide="library"></i></div>
                    <div class="stat-info">
                        <h3>12</h3>
                        <p>Total Topik</p>
                    </div>
                </div>
                <div class="z-card stat-card">
                    <div class="stat-icon green"><i data-lucide="file-question"></i></div>
                    <div class="stat-info">
                        <h3>145</h3>
                        <p>Total Soal</p>
                    </div>
                </div>
                <div class="z-card stat-card">
                    <div class="stat-icon purple"><i data-lucide="file-check-2"></i></div>
                    <div class="stat-info">
                        <h3>3</h3>
                        <p>Digunakan di CBT</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="z-card" style="padding: 1.5rem;">
                <h3 style="margin: 0 0 1rem 0; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="zap" style="color: #eab308; width: 20px; height: 20px;"></i> Akses Cepat
                </h3>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="<?php echo base_url('bank_soal/topik'); ?>" class="btn btn-primary" style="justify-content: center;">
                        <i data-lucide="plus"></i> Tambah Topik Baru
                    </a>
                    <a href="<?php echo base_url('bank_soal/kelola_soal'); ?>" class="btn btn-outline" style="justify-content: center;">
                        <i data-lucide="pen-line"></i> Buat Soal
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    lucide.createIcons();
    function zToggleSidebar() {
        document.querySelector('.z-sidebar').classList.toggle('open');
    }
</script>
</body>
</html>
