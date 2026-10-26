<?php
require_once 'koneksi.php';
if (!isset($_SESSION['guru_id'])) { header("Location: login.php"); exit; }

$page_title = 'Dalam Pengembangan';
include 'header.php';
?>
<div class="header" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
    <div class="header-top">
        <div class="greeting">
            <p>Fitur Baru</p>
            <h1>Segera Hadir</h1>
        </div>
        <div class="avatar" style="color: #0ea5e9; background: white;" onclick="window.location.href='aplikasi_saya.php'">
            <i class="fa-solid fa-arrow-left"></i>
        </div>
    </div>
</div>
<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10; text-align: center; padding: 40px 20px;">
        <i class="fa-solid fa-person-digging" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 20px;"></i>
        <h2 style="margin: 0 0 10px 0; color: #1e293b;">Dalam Pengembangan</h2>
        <p style="color: #64748b; margin: 0; line-height: 1.5;">Halaman Monitor BOS sedang dalam tahap pengembangan dan akan segera tersedia.</p>
    </div>
</div>
<?php include 'footer_guru.php'; ?>
