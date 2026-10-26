<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$initial = strtoupper(substr($nama, 0, 2));

// get myApps (if needed for sidebar)
$myApps = [];
if(isset($_SESSION['user_id'])) {
    $db_core_view = \App\Core\Database::connect('core');
    try {
        $res = $db_core_view->query("SELECT app_code FROM app_admins WHERE user_id = ".$_SESSION['user_id'])->fetchAll();
        foreach($res as $r) { $myApps[] = $r['app_code']; }
    } catch(\PDOException $e) {}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title_page; ?> - Aula System</title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . date('YmdH')); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/multi_dashboard.css?v=' . date('YmdH')); ?>">
    <style>
        .class-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; margin-top: 1.5rem; }
        .class-card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; transition: all 0.2s ease; cursor: pointer; text-decoration: none; color: inherit; display: block; }
        .class-card:hover { transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0,0,0,0.05); border-color: #8b5cf6; }
        .cc-head { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; }
        .cc-icon { width: 48px; height: 48px; border-radius: 12px; background: #ede9fe; color: #8b5cf6; display: flex; align-items: center; justify-content: center; }
        .cc-badge { background: #f1f5f9; color: #64748b; font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 20px; }
        .cc-title { font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; }
        .cc-sub { font-size: 0.85rem; color: #64748b; margin: 0; }
        .cc-foot { margin-top: 1.25rem; padding-top: 1.25rem; border-top: 1px dashed #e2e8f0; display: flex; align-items: center; justify-content: space-between; font-size: 0.85rem; font-weight: 600; color: #8b5cf6; }
    </style>
</head>
<body>
<div class="siakad-container">
    <?php include __DIR__ . '/../sidebar_multi_guru.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/../header_multi_guru.php'; ?>

        <div class="z-scroll" style="padding: 0 var(--z-gap) var(--z-gap);">
            
            <div class="hero-banner" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); border-radius: 16px; padding: 2.5rem 2rem; color: white; display: flex; align-items: center; justify-content: space-between; margin-top: 1.5rem; position: relative; overflow: hidden; box-shadow: 0 10px 25px rgba(139,92,246,0.2);">
                <div style="position: relative; z-index: 2;">
                    <?php 
                        $hour = date('H');
                        $greeting = ($hour < 11) ? 'Selamat Pagi' : (($hour < 15) ? 'Selamat Siang' : (($hour < 18) ? 'Selamat Sore' : 'Selamat Malam'));
                    ?>
                    <h1 style="font-size: 2rem; font-weight: 800; margin: 0 0 8px 0;"><?php echo $greeting; ?>, <?php echo htmlspecialchars($nama); ?>!</h1>
                    <p style="color: rgba(255, 255, 255, 0.9); font-size: 1rem; margin: 0 0 15px 0; max-width: 500px; line-height: 1.6;">Selamat datang di Dashboard Pembina Ekstrakurikuler. Kelola kegiatan, peserta, dan nilai ekstrakurikuler dengan mudah dan efisien.</p>
                    <a href="#ekstra-list" class="btn btn-primary" style="background: white; color: #6d28d9; border: none; font-weight: bold; display: inline-flex; align-items: center; gap: 8px; text-decoration:none; padding: 10px 20px; border-radius: 8px;">
                        <i data-lucide="book-open" style="width: 16px; height: 16px;"></i> Program Pembina
                    </a>
                </div>
                <i data-lucide="medal" style="position: absolute; right: 2rem; top: 50%; transform: translateY(-50%); width: 140px; height: 140px; color: rgba(255,255,255,0.05); z-index: 1;"></i>
            </div>

            <?php if(empty($ekstraList)): ?>
                <div class="z-panel" style="text-align:center; padding:4rem 2rem; margin-top:1.5rem; color:#64748b;">
                    <i data-lucide="award" style="width:48px; height:48px; margin-bottom:1rem; opacity:0.3; display:inline-block;"></i>
                    <h3 style="color:#0f172a; margin:0 0 10px 0;">Belum Ada Data Ekstrakurikuler</h3>
                    <p style="margin:0;">Anda belum ditautkan ke kegiatan ekstrakurikuler apapun secara valid.</p>
                </div>
            <?php else: ?>
                <!-- Summary Cards Row -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1.5rem;">
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; display: flex; align-items: center; gap: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                        <div style="width: 48px; height: 48px; background: #ede9fe; color: #8b5cf6; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="award" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.8rem; color: #64748b; font-weight: 700;">Total Ekstrakurikuler</div>
                            <div style="font-size: 1.4rem; font-weight: 800; color: #0f172a;"><?php echo count($ekstraList); ?></div>
                        </div>
                    </div>
                    
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; display: flex; align-items: center; gap: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                        <div style="width: 48px; height: 48px; background: #eff6ff; color: #3b82f6; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="users" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.8rem; color: #64748b; font-weight: 700;">Total Peserta Didik</div>
                            <div style="font-size: 1.4rem; font-weight: 800; color: #0f172a;">-</div>
                        </div>
                    </div>

                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; display: flex; align-items: center; gap: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                        <div style="width: 48px; height: 48px; background: #f0f9ff; color: #0ea5e9; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="check-circle" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.8rem; color: #64748b; font-weight: 700;">Status Penilaian</div>
                            <div style="font-size: 1.4rem; font-weight: 800; color: #0f172a;">Belum</div>
                        </div>
                    </div>
                </div>

                <!-- Judul Ekstrakurikuler yang Diampu -->
                <h3 id="ekstra-list" style="margin: 2rem 0 1rem 0; font-size: 1.2rem; font-weight: 800; color: #0f172a;">Kegiatan Ekstrakurikuler yang Dibina</h3>

                <div class="class-grid" style="margin-top: 0;">
                    <?php foreach($ekstraList as $k): ?>
                    <div class="class-card">
                        <a href="<?php echo base_url('guru/tugas_tambahan/pembina_ekstra/pengaturan?ekstra=<?php echo urlencode($k['nama_ekstra']); ?>'); ?>" style="text-decoration:none; color:inherit; display:block;">
                            <div class="cc-head">
                                <div class="cc-icon"><i data-lucide="medal" style="width:24px;height:24px;"></i></div>
                                <div class="cc-badge">Tahun <?php echo htmlspecialchars($active_year_name); ?></div>
                            </div>
                            <h2 class="cc-title"><?php echo htmlspecialchars($k['nama_ekstra']); ?></h2>
                            <p class="cc-sub" style="margin-bottom: 1.25rem;">Kegiatan Pengembangan Diri</p>
                            
                            <!-- Info Tambahan -->
                            <div style="background:#f8fafc; border-radius:10px; padding:12px; margin-bottom:1rem; border:1px solid #e2e8f0;">
                                <div style="font-size:0.75rem; font-weight:800; color:#64748b; text-transform:uppercase; margin-bottom:8px;">Manajemen Ekstrakurikuler</div>
                                <div style="font-size:0.8rem; color:#94a3b8; font-style:italic;">Klik untuk menyusun Jadwal dan Program Kerja.</div>
                            </div>
                        </a>
                        
                        <div style="display:flex; gap:10px;">
                            <a href="<?php echo base_url('guru/tugas_tambahan/pembina_ekstra/anggota?ekstra=<?php echo urlencode($k['nama_ekstra']); ?>'); ?>" class="z-btn" style="flex:1; text-align:center; padding:0.6rem; font-size:0.8rem; font-weight:700; background:#f1f5f9; color:#0f172a; text-decoration:none; border-radius:8px; transition:0.2s;"><i data-lucide="users" style="width:16px;height:16px;vertical-align:-3px;margin-right:4px;"></i> Anggota</a>
                            <a href="<?php echo base_url('guru/tugas_tambahan/pembina_ekstra/jurnal?ekstra=<?php echo urlencode($k['nama_ekstra']); ?>'); ?>" class="z-btn" style="flex:1; text-align:center; padding:0.6rem; font-size:0.8rem; font-weight:700; background:#0ea5e9; color:white; text-decoration:none; border-radius:8px; transition:0.2s;"><i data-lucide="book-open" style="width:16px;height:16px;vertical-align:-3px;margin-right:4px;"></i> Jurnal</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
</body>
</html>


