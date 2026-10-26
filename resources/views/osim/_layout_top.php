<!DOCTYPE html><html lang="id"><head>
<?php include __DIR__.'/../admin/_zamrud_head.php'; ?>
<style>
    .elite-page-header {
        padding: 30px;
        border-radius: 24px;
        position: relative;
        overflow: hidden;
        margin-bottom: 30px;
        color: #fff;
    }
    .header-bg-shapes {
        position: absolute;
        top: -50%; left: -50%; right: -50%; bottom: -50%;
        background: radial-gradient(circle at top right, rgba(255,255,255,0.15), transparent 40%),
                    radial-gradient(circle at bottom left, rgba(0,0,0,0.1), transparent 40%);
        z-index: 0;
        pointer-events: none;
    }
    .header-content-inner {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    .header-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 800;
        margin-bottom: 15px;
        border: 1px solid rgba(255,255,255,0.3);
    }
    .e-btn {
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }
    .e-btn-glass {
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255,255,255,0.2);
    }
    .e-btn-glass:hover {
        background: rgba(255,255,255,0.25);
    }
    @media print {
        .hide-on-print { display: none !important; }
    }
</style>
</head><body>
<div class="z-wrap">
<?php
$active_module = 'osim';
$adminNama = $_SESSION['nama'] ?? 'Administrator';
$adminInitial = strtoupper(substr($adminNama, 0, 1));
include __DIR__.'/../admin/_zamrud_sidebar.php';
?>
<main class="z-main">
<header class="z-header">
    <div class="z-header-left">
        <button class="z-hamburger" id="zHamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
        <span class="z-header-title"><?= $page_title ?? 'OSIM Portal' ?></span>
        <span class="z-badge">Admin</span>
    </div>
    <div class="z-header-user">
        <div class="z-user-info">
            <div class="name"><?= htmlspecialchars($_SESSION['nama'] ?? 'Administrator') ?></div>
            <div class="role">Pengelola OSIM</div>
        </div>
        <div class="z-avatar"><?= strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1)) ?></div>
    </div>
</header>
<div class="z-scroll" style="padding: 1.5rem;">
