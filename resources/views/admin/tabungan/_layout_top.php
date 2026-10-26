<!DOCTYPE html><html lang="id"><head>
<?php include __DIR__.'/../_zamrud_head.php'; ?>
</head><body>
<div class="z-wrap">
<?php
$active_module = 'tabungan';
$adminNama = $_SESSION['nama'] ?? 'Administrator';
$adminInitial = strtoupper(substr($adminNama, 0, 1));
include __DIR__.'/../_zamrud_sidebar.php';
?>
<main class="z-main">
<header class="z-header">
    <div class="z-header-left">
        <button class="z-hamburger" id="zHamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
        <span class="z-header-title"><?= $page_title ?? 'Tabungan' ?></span>
        <span class="z-badge">Admin</span>
    </div>
    <div class="z-header-user">
        <div class="z-user-info">
            <div class="name"><?= htmlspecialchars($_SESSION['nama'] ?? 'Administrator') ?></div>
            <div class="role">Pengelola Tabungan</div>
        </div>
        <div class="z-avatar"><?= strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1)) ?></div>
    </div>
</header>
<div class="z-scroll" style="padding: 1.5rem;">
