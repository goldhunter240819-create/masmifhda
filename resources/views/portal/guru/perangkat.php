<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$roleName = "Guru / Tendik";
if(!empty($_SESSION['is_kamad'])) $roleName = "Kepala Madrasah";
$initial = strtoupper(substr($nama, 0, 2));

$myApps = [];
if(isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2 && isset($_SESSION['user_id'])) {
    $db_core = \App\Core\Database::connect('core');
    $uid = $_SESSION['user_id'];
    try {
        $res = $db_core->query("SELECT app_code FROM app_admins WHERE user_id = $uid")->fetchAll();
        foreach($res as $r) {
            $myApps[] = $r['app_code'];
        }
    } catch(\PDOException $e) {}
}

// Dummy data removed, $mapelList is now injected by PortalGuruPribadiController::perangkat()
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perangkat Pembelajaran - Portal Guru</title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . date('YmdH')); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/multi_dashboard.css?v=' . date('YmdH')); ?>">
</head>
<body>

<div class="siakad-container">

<?php include __DIR__ . '/../sidebar_multi_guru.php'; ?>

    <div class="z-main">

<?php include __DIR__ . '/../header_multi_guru.php'; ?>

        <div class="z-scroll" style="padding: 0 var(--z-gap) var(--z-gap);">
            
            <!-- Hero Banner Premium -->
            <div class="elite-page-header welcome-hero" style="background: var(--z-primary-gradient); padding: 2rem 2.5rem; border-radius: var(--z-radius, 12px); margin-bottom: 2rem; color: white; position: relative; overflow: hidden;">
                <div style="position: relative; z-index: 1;">
                    <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="book-open" style="color: rgba(255,255,255,0.8);"></i> Perangkat Pembelajaran
                    </h1>
                    <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Upload dan kelola dokumen Perangkat Pembelajaran Anda secara digital.</p>
                </div>
            </div>

            <?php
            // Group mapelList by Tingkat
            $groupedMapel = [];
            foreach($mapelList as $mapel) {
                $kelasName = trim($mapel['kelas']);
                $tingkat = 'Lainnya';
                if (preg_match('/^([IVX]+|\d+)/i', $kelasName, $matches)) {
                    $tingkat = strtoupper($matches[1]);
                }
                $groupedMapel[$tingkat][] = $mapel;
            }
            ?>
            
            <?php foreach($groupedMapel as $tingkat => $mapels): ?>
            <div style="margin-bottom: 2.5rem;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 1.25rem;">
                    <div style="width: 8px; height: 24px; background: var(--z-primary); border-radius: 4px;"></div>
                    <h2 style="margin: 0; font-size: 1.25rem; font-weight: 800; color: #0f172a;">Tingkat <?php echo $tingkat; ?></h2>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
                    
                    <?php foreach($mapels as $mapel): ?>
                    <a href="<?php echo base_url('guru/perangkat/detail?kelas=' . $mapel['kelas_id'] . '&mapel=' . $mapel['mapel_id']); ?>" class="z-card" style="position: relative; padding: 1.5rem; display: flex; flex-direction: column; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.03); text-decoration: none; color: inherit;">
                        
                        <?php if($mapel['bolong'] > 0): ?>
                        <!-- Red Badge at Top Center -->
                        <div style="position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: #ef4444; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; display: flex; align-items: center; gap: 4px; box-shadow: 0 4px 6px rgba(239, 68, 68, 0.3);">
                            <i data-lucide="bell" style="width:12px;height:12px;"></i> <?php echo $mapel['bolong']; ?> Bolong
                        </div>
                        <?php endif; ?>

                        <!-- Icon -->
                        <div style="width: 50px; height: 50px; border-radius: 14px; background: #f0f9ff; color: #0ea5e9; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                            <i data-lucide="book-open" style="width:24px;height:24px;"></i>
                        </div>

                        <!-- Content -->
                        <div style="flex: 1;">
                            <h3 style="margin: 0 0 5px 0; font-size: 1.15rem; color: #0f172a; font-weight: 800;"><?php echo $mapel['nama']; ?></h3>
                            <p style="margin: 0; color: #64748b; font-size: 0.85rem;">Kelas: <?php echo $mapel['kelas']; ?></p>
                        </div>

                        <!-- Arrow -->
                        <div style="display: flex; justify-content: flex-end; margin-top: 10px;">
                            <div style="width: 28px; height: 28px; border-radius: 50%; background: #f1f5f9; color: #94a3b8; display: flex; align-items: center; justify-content: center; transition: 0.2s;">
                                <i data-lucide="chevron-right" style="width:16px;height:16px;"></i>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>

                </div>
            </div>
            <?php endforeach; ?>

        </div><!-- /z-scroll -->
    </div><!-- /z-main -->
</div><!-- /siakad-container -->

<?php include __DIR__ . '/../footer_multi_guru.php'; ?>
