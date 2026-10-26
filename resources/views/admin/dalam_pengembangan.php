<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . base_url('login'));
    exit;
}
$adminNama = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Admin';
$adminInitial = strtoupper(substr($adminNama, 0, 2));
$active_module = 'kurikulum';
$active_page   = 'pengembangan';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title = 'Dalam Pengembangan'; include __DIR__ . '/_zamrud_head.php'; ?>
    <style>
        .dev-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 60vh;
            text-align: center;
            padding: 2rem;
        }
        .dev-icon {
            width: 80px;
            height: 80px;
            background: #fffbeb;
            color: #f59e0b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.2);
        }
        .dev-icon i {
            width: 40px;
            height: 40px;
        }
        .dev-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }
        .dev-desc {
            font-size: 1rem;
            color: #64748b;
            max-width: 500px;
            line-height: 1.6;
            margin-bottom: 2rem;
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
                    <span class="z-header-title">Status Modul</span>
                    <span class="z-badge">Waka Kurikulum</span>
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
                <div class="z-page-head">
                    <div class="dev-container">
                        <div class="dev-icon">
                            <i data-lucide="cone"></i>
                        </div>
                        <h2 class="dev-title">Sedang Dalam Pengembangan!</h2>
                        <p class="dev-desc">Modul ini saat ini sedang dalam tahap pengembangan dan sinkronisasi dengan aplikasi utama. Harap kembali lagi nanti.</p>
                        <a href="<?php echo base_url('admin/kurikulum'); ?>" class="z-btn-primary" style="text-decoration:none; padding: 0.6rem 1.2rem; border-radius: 8px;">
                            <i data-lucide="arrow-left" style="width:16px;height:16px;"></i> Kembali ke Dashboard
                        </a>
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
