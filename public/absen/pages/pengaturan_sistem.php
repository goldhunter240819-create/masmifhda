<?php
include '../includes/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit;
}

$message = "";

if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_aplikasi']);
    $tempat_cetak = mysqli_real_escape_string($koneksi, $_POST['tempat_cetak']);
    $nama_admin = mysqli_real_escape_string($koneksi, $_POST['nama_admin']);
    
    // Handle Logo Upload
    $logo_sql = "";
    if ($_FILES['logo']['name'] != "") {
        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $logo_name = "logo_" . time() . "." . $ext;
        move_uploaded_file($_FILES['logo']['tmp_name'], "assets/uploads/" . $logo_name);
        $logo_sql = ", logo = '$logo_name'";
    }

    // Handle Favicon Upload
    $favicon_sql = "";
    if ($_FILES['favicon']['name'] != "") {
        $ext = pathinfo($_FILES['favicon']['name'], PATHINFO_EXTENSION);
        $favicon_name = "favicon_" . time() . "." . $ext;
        move_uploaded_file($_FILES['favicon']['tmp_name'], "assets/uploads/" . $favicon_name);
        $favicon_sql = ", favicon = '$favicon_name'";
    }

    // Handle TTD Upload
    $ttd_sql = "";
    if ($_FILES['ttd_admin']['name'] != "") {
        $ext = pathinfo($_FILES['ttd_admin']['name'], PATHINFO_EXTENSION);
        $ttd_name = "ttd_" . time() . "." . $ext;
        move_uploaded_file($_FILES['ttd_admin']['tmp_name'], "assets/uploads/" . $ttd_name);
        $ttd_sql = ", ttd_admin = '$ttd_name'";
    }

    $update = "UPDATE pengaturan_sistem SET nama_aplikasi = '$nama', tempat_cetak = '$tempat_cetak', nama_admin = '$nama_admin' $logo_sql $favicon_sql $ttd_sql WHERE id = 1";
    if (mysqli_query($koneksi, $update)) {
        // Refresh sys_cfg
        $_SESSION['msg'] = "success|Pengaturan sistem berhasil diperbarui.";
        header("Location: pengaturan_sistem.php");
        exit;
    } else {
        $message = "error|Gagal memperbarui pengaturan.";
    }
}

if (isset($_SESSION['msg'])) {
    $message = $_SESSION['msg'];
    unset($_SESSION['msg']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Aplikasi - <?php echo $app_name; ?></title>
    <link href="/public/absen/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/absen/assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=1.1">
    <link rel="icon" href="<?php echo $app_favicon; ?>">
    <script src="/public/absen/assets/js/sweetalert2.all.min.js"></script>
    <style>
        .preview-box {
            width: 100px; height: 100px;
            border: 2px dashed var(--border-color);
            border-radius: var(--radius-md);
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; background: var(--hover-bg); margin-bottom: 1rem;
        }
        .preview-box img { max-width: 100%; max-height: 100%; object-fit: contain; }
    </style>
</head>
<body>

<div class="wrapper">
    <?php include '../includes/sidebar.php'; ?>

    <main class="main-content">
        <header class="content-header">
            <div class="h-title">Pengaturan Identitas Sistem</div>
            <div class="small text-muted-pro">Kelola branding dan informasi institusi.</div>
        </header>

        <div class="content-padding">

            <?php if ($message): 
                $m = explode('|', $message);
                echo "<script>Swal.fire({ icon: '".($m[0] == 'success' ? 'success' : 'error')."', title: 'Sistem', text: '".$m[1]."' });</script>";
            endif; ?>

            <div class="card-pro">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row g-4">
                        <!-- Identitas Nama -->
                        <div class="col-12">
                            <label class="form-label small fw-bold">Nama Aplikasi / Sekolah</label>
                            <input type="text" name="nama_aplikasi" class="form-control form-control-lg" value="<?php echo $sys_cfg['nama_aplikasi']; ?>" required>
                            <div class="form-text small">Nama ini akan muncul di sidebar, halaman login, dan judul tab.</div>
                        </div>

                        <!-- Logo -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Logo Aplikasi</label>
                            <div class="preview-box">
                                <?php if ($app_logo): ?>
                                    <img src="<?php echo $app_logo; ?>" alt="Logo Preview">
                                <?php else: ?>
                                    <i class="bi bi-image text-muted fs-1"></i>
                                <?php endif; ?>
                            </div>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                            <div class="form-text small">Format: PNG/JPG. Disarankan rasio kotak atau memanjang.</div>
                        </div>

                        <!-- Favicon -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Favicon (Ikon Tab)</label>
                            <div class="preview-box">
                                <?php if ($app_favicon): ?>
                                    <img src="<?php echo $app_favicon; ?>" alt="Favicon Preview">
                                <?php else: ?>
                                    <i class="bi bi-app text-muted fs-1"></i>
                                <?php endif; ?>
                            </div>
                            <input type="file" name="favicon" class="form-control" accept="image/*">
                            <div class="form-text small">Disarankan ukuran 32x32 atau 64x64 piksel.</div>
                        </div>

                        <div class="col-12 mt-4 pt-4 border-top">
                            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-printer me-2"></i>Pengaturan Cetak Laporan</h6>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tempat Cetak Laporan</label>
                            <input type="text" name="tempat_cetak" class="form-control" value="<?php echo $sys_cfg['tempat_cetak']; ?>" placeholder="Cth: Jakarta">
                            <div class="form-text small">Lokasi yang tertera di sebelah tanggal laporan.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Lengkap Admin (Penanda Tangan)</label>
                            <input type="text" name="nama_admin" class="form-control" value="<?php echo $sys_cfg['nama_admin']; ?>" placeholder="Nama Lengkap & Gelar">
                        </div>

                        <!-- <div class="col-12">
                            <label class="form-label small fw-bold">TTD Digital Admin</label>
                            <input type="file" name="ttd_admin" class="form-control" accept="image/png">
                            <div class="form-text small text-danger">Diabaikan. Sistem sekarang menggunakan validasi QR Code Otomatis.</div>
                        </div> -->

                        <div class="col-12 pt-3">
                            <button type="submit" name="simpan" class="btn-pro btn-pro-primary w-100 py-3">
                                <i class="bi bi-save2 me-2"></i> Perbarui Identitas Sistem
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <footer class="footer-pro">
            App Settings &copy; <?php echo date('Y'); ?>
        </footer>
    </main>
</div>

<script src="/public/absen/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
