<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$roleName = "Administrator";
if(isset($_SESSION['role_id'])) {
    if($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 99) $roleName = "Super Admin";
    elseif($_SESSION['role_id'] == 2) $roleName = "Pengajar";
    elseif($_SESSION['role_id'] == 3) $roleName = "Siswa";
}
$initial = strtoupper(substr($nama, 0, 2));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Pengaturan APK - Aula System'; ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>?v=<?php echo time(); ?>">
    <!-- Zamrud Design System -->
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . time()); ?>">
    <!-- Lucide Icons -->
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <style>
        :root {
            --z-primary-gradient: linear-gradient(90deg, #8b5cf6, #d946ef);
            --z-primary: #d946ef;
        }
        body { background: var(--z-bg); font-family: 'Plus Jakarta Sans', sans-serif; margin: 0; }
        .siakad-container { display: flex; width: 100%; min-height: 100vh; }
        .z-header-right { display: flex; align-items: center; gap: 2rem; }
    </style>
</head>
<body>
    <div class="siakad-container">
        <?php include __DIR__ . '/sidebar.php'; ?>
        
        <div class="z-main">
            <!-- Header -->
            <header class="z-header" style="height: 80px; padding: 0 2rem; background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100;">
                <div style="display:flex; align-items:center; gap:15px;">
                    <button class="z-mobile-menu" onclick="document.getElementById('zSidebar').classList.add('open')" style="background:none; border:none; cursor:pointer;">
                        <i data-lucide="menu"></i>
                    </button>
                    <div style="font-weight: 700; color: #64748b; display: flex; align-items: center; gap: 8px;">
                        <span style="color: #0ea5e9;">Pengaturan APK</span>
                        <i data-lucide="chevron-right" style="width: 16px; color: #cbd5e1;"></i>
                        <span style="color: #0f172a;"><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></span>
                    </div>
                </div>
                
                <div class="z-header-right">
                    <div style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <div style="text-align: right; line-height: 1.2;">
                            <div style="font-weight: 700; color: #0f172a; font-size: 0.85rem;"><?php echo $nama; ?></div>
                            <div style="font-size: 0.7rem; color: #64748b; font-weight: 600;"><?php echo $roleName; ?></div>
                        </div>
                        <div style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, #8b5cf6, #d946ef); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; box-shadow: 0 4px 10px rgba(217,70,239,0.3);">
                            <?php echo $initial; ?>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <div style="padding: 2rem;">
                <?php echo isset($content) ? $content : ''; ?>
            </div>
        </div>
    </div>
    <script>
        lucide.createIcons();
        function zToggleSidebar() {
            document.getElementById('zSidebar').classList.toggle('open');
        }
    </script>
</body>
</html>
