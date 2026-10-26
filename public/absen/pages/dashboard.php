<?php
include '../includes/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit;
}

// Statistik
$jml_siswa = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users WHERE tipe = 'siswa'"))['total'];
$jml_guru = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users WHERE tipe = 'guru'"))['total'];
$tgl_today = date('Y-m-d');
$absensi_today = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM absensi WHERE DATE(tanggal) = '$tgl_today'"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo $app_name; ?></title>
    <link href="/public/absen/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/absen/assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=1.1">
    <link rel="icon" href="<?php echo $app_favicon; ?>">
</head>
<body>

<div class="wrapper">
    <?php include '../includes/sidebar.php'; ?>

    <main class="main-content">
        <header class="content-header">
            <div class="h-title">Dashboard Administrasi</div>
            <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted);"><?php echo date('d F Y'); ?></div>
        </header>

        <div class="content-padding">
            <div class="welcome-banner">
                <h1>Selamat Datang, Administrator!</h1>
                <p>Monitor aktivitas presensi harian seluruh civitas akademika GH-SSS secara real-time.</p>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card-pro" style="border-top: 4px solid var(--accent-color);">
                        <div class="text-muted small fw-bold text-uppercase mb-1">Total Siswa</div>
                        <h2 class="fw-bold mb-0"><?php echo $jml_siswa; ?></h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-pro" style="border-top: 4px solid var(--accent-color);">
                        <div class="text-muted small fw-bold text-uppercase mb-1">Total Guru</div>
                        <h2 class="fw-bold mb-0"><?php echo $jml_guru; ?></h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-pro" style="border-top: 4px solid #f59e0b;">
                        <div class="text-muted small fw-bold text-uppercase mb-1">Hadir Hari Ini</div>
                        <h2 class="fw-bold mb-0"><?php echo $absensi_today; ?></h2>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card-pro">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">Aktivitas Terbaru</h6>
                        <div class="table-responsive-pro">
                            <table class="table-pro">
                                <thead>
                                    <tr>
                                        <th>Nama Lengkap</th>
                                        <th>Kategori</th>
                                        <th class="text-end">Waktu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $res_recent = mysqli_query($koneksi, "SELECT a.*, u.nama FROM absensi a JOIN users u ON a.user_id = u.id ORDER BY a.id DESC LIMIT 5");
                                    while($r = mysqli_fetch_assoc($res_recent)): 
                                        $last_time = ($r['jam_keluar'] && $r['jam_keluar'] != '00:00:00') ? $r['jam_keluar'] : $r['jam_masuk'];
                                    ?>
                                    <tr>
                                        <td class="fw-semibold"><?php echo $r['nama']; ?></td>
                                        <td><span class="badge-pro bg-soft-green"><?php echo ucfirst($r['tipe']); ?></span></td>
                                        <td class="text-end font-monospace small"><?php echo $last_time ? date('H:i', strtotime($last_time)) : '-'; ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card-pro" style="background: var(--primary-gradient); color: white;">
                        <h6 class="fw-bold mb-3">Informasi Sistem</h6>
                        <p class="small opacity-75">Sistem Absensi QR terintegrasi dengan portal utama GH-SSS. Seluruh data pengguna disinkronkan secara otomatis.</p>
                        <hr class="opacity-25">
                        <div class="d-flex justify-content-between small">
                            <span>Status Server</span>
                            <span class="badge" style="background: rgba(255,255,255,0.2);">Online</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer-pro">
            Attendance QR System &copy; <?php echo date('Y'); ?> | GH-SSS Portal Integrated
        </footer>
    </main>
</div>

<script src="/public/absen/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
