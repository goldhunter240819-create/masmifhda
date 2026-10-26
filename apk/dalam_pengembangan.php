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
        <div class="avatar" style="color: #0ea5e9; background: white;" onclick="window.history.back()">
            <i class="fa-solid fa-arrow-left"></i>
        </div>
    </div>
</div>
<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10; text-align: center; padding: 50px 20px; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 50vh;">
        <div style="width: 80px; height: 80px; background: #e0f2fe; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 24px; box-shadow: 0 10px 25px -5px rgba(14, 165, 233, 0.3);">
            <i class="fa-solid fa-person-digging" style="font-size: 2.5rem; color: #0ea5e9;"></i>
        </div>
        <h2 style="margin: 0 0 12px 0; color: #0f172a; font-size: 1.25rem; font-weight: 800;">Fitur Sedang Digarap!</h2>
        <p style="color: #64748b; margin: 0 0 24px 0; line-height: 1.6; font-size: 0.9rem; max-width: 250px;">
            Halaman ini masih dalam tahap pengembangan oleh tim IT. Kami sedang menyiapkan sesuatu yang keren untuk Anda.
        </p>
        <button onclick="window.history.back()" style="background: #0ea5e9; color: white; border: none; padding: 12px 24px; border-radius: 12px; font-weight: 700; font-size: 0.9rem; box-shadow: 0 4px 6px -1px rgba(14, 165, 233, 0.2); cursor: pointer; transition: all 0.3s ease;">
            Kembali ke Sebelumnya
        </button>
    </div>
</div>
<?php include 'footer_guru.php'; ?>
