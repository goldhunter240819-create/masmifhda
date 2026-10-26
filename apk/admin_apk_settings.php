<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$is_admin = false;
if (isset($_SESSION['user_id'])) {
    try {
        $stmt_cek = $core_db->prepare("SELECT id FROM app_admins WHERE user_id = ? AND app_code = 'portal'");
        $stmt_cek->execute([$_SESSION['user_id']]);
        if ($stmt_cek->fetch()) {
            $is_admin = true;
        } else {
            // Also check role_id = 1 or 99
            $stmt_role = $core_db->prepare("SELECT role_id FROM users WHERE id = ?");
            $stmt_role->execute([$_SESSION['user_id']]);
            $role_id = $stmt_role->fetchColumn();
            if ($role_id == 1 || $role_id == 99) {
                $is_admin = true;
            }
        }
    } catch(Exception $e) {}
}

if (!$is_admin) {
    header("Location: akses_ditolak.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pengaturan APK - Aula System</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { margin: 0; font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .header { background: linear-gradient(135deg, #d946ef, #c026d3); color: white; padding: 20px; display: flex; align-items: center; gap: 15px; position: sticky; top: 0; z-index: 10; box-shadow: 0 4px 15px rgba(217, 70, 239, 0.2); border-bottom-left-radius: 20px; border-bottom-right-radius: 20px; }
        .back-btn { color: white; text-decoration: none; font-size: 20px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.2); border-radius: 12px; }
        .header h1 { margin: 0; font-size: 1.1rem; font-weight: 700; }
        .content { padding: 30px 20px; text-align: center; }
        .coming-soon-card { background: white; border-radius: 24px; padding: 40px 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .icon-circle { width: 80px; height: 80px; background: #fdf4ff; color: #d946ef; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 20px; }
        .coming-soon-card h2 { margin: 0 0 10px 0; font-size: 1.4rem; color: #0f172a; font-weight: 800; }
        .coming-soon-card p { margin: 0 0 25px 0; color: #64748b; font-size: 0.95rem; line-height: 1.6; }
    </style>
</head>
<body>

<div class="header">
    <a href="aplikasi_saya.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i></a>
    <h1>Pengaturan APK</h1>
</div>

<div class="content">
    <div class="coming-soon-card">
        <div class="icon-circle">
            <i class="fa-solid fa-mobile-screen-button"></i>
        </div>
        <h2>Dalam Pengembangan</h2>
        <p>Halaman ini nantinya akan digunakan untuk mengatur Layout, Tema, Banner, dan konfigurasi lainnya pada Aplikasi Mobile (APK) Siswa dan Guru secara dinamis langsung dari Portal.</p>
        <a href="aplikasi_saya.php" style="display: inline-block; background: #d946ef; color: white; text-decoration: none; padding: 12px 24px; border-radius: 12px; font-weight: 700; box-shadow: 0 4px 15px rgba(217, 70, 239, 0.3);">Kembali ke Menu</a>
    </div>
</div>

</body>
</html>
