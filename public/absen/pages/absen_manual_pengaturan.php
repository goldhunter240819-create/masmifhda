<?php
include '../includes/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit;
}

$message = "";

// Mengubah Pengaturan Pelacakan Manual
if (isset($_POST['simpan'])) {
    $m_masuk = mysqli_real_escape_string($koneksi, $_POST['mulai_masuk']);
    $a_masuk = mysqli_real_escape_string($koneksi, $_POST['akhir_masuk']);
    $m_keluar = mysqli_real_escape_string($koneksi, $_POST['mulai_keluar']);
    $a_keluar = mysqli_real_escape_string($koneksi, $_POST['akhir_keluar']);
    
    $query = "UPDATE pengaturan_manual SET mulai_masuk='$m_masuk', akhir_masuk='$a_masuk', mulai_keluar='$m_keluar', akhir_keluar='$a_keluar' WHERE id=1";
    if (mysqli_query($koneksi, $query)) {
        $message = "success|Pengaturan rentang waktu fiktif berhasil diperbarui.";
    } else {
        $message = "error|Gagal menyimpan pengaturan.";
    }
}

// Mengambil Data
$result = mysqli_query($koneksi, "SELECT * FROM pengaturan_manual WHERE id=1");
$row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penyesuaian Jam (Manual) - <?php echo $app_name; ?></title>
    <link href="/public/absen/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/absen/assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=1.1">
    <link rel="icon" href="<?php echo $app_favicon; ?>">
    <script src="/public/absen/assets/js/sweetalert2.all.min.js"></script>
</head>
<body>

<div class="wrapper">
    <?php include '../includes/sidebar.php'; ?>

    <main class="main-content">
        <header class="content-header">
            <div class="h-title">Konfigurasi Waktu Fiktif</div>
            <div class="small text-muted-pro">Manajemen parameter acak data manual.</div>
        </header>

        <div class="content-padding">

            <?php if ($message): 
                $m = explode('|', $message);
                echo "<script>Swal.fire({ icon: '".($m[0] == 'success' ? 'success' : 'error')."', title: 'Sistem', text: '".$m[1]."' });</script>";
            endif; ?>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card-pro">
                        <form action="" method="POST">
                            <div class="mb-4">
                                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-box-arrow-in-right me-2 text-success"></i>Range Jam Masuk</h6>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Dari</label>
                                        <input type="time" name="mulai_masuk" class="form-control" value="<?php echo substr($row['mulai_masuk'], 0, 5); ?>" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Sampai</label>
                                        <input type="time" name="akhir_masuk" class="form-control" value="<?php echo substr($row['akhir_masuk'], 0, 5); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-5">
                                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-box-arrow-right me-2 text-danger"></i>Range Jam Pulang</h6>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Dari</label>
                                        <input type="time" name="mulai_keluar" class="form-control" value="<?php echo substr($row['mulai_keluar'], 0, 5); ?>" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold">Sampai</label>
                                        <input type="time" name="akhir_keluar" class="form-control" value="<?php echo substr($row['akhir_keluar'], 0, 5); ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" name="simpan" class="btn-pro btn-pro-primary w-100 py-3">
                                <i class="bi bi-save me-2"></i> Simpan Konfigurasi
                            </button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card-pro bg-light border-0">
                        <h6 class="fw-bold mb-3"><i class="bi bi-info-circle-fill me-2 text-primary"></i>Cara Kerja</h6>
                        <p class="small text-secondary" style="line-height: 1.7;">
                            Nilai yang Anda masukkan di sini akan digunakan oleh <strong>Generator Massal</strong> sebagai batas bawah dan batas atas. <br><br>
                            Sistem akan memilih waktu secara <strong>acak (random)</strong> di antara rentang tersebut untuk setiap entry data fiktif agar terlihat alami dan tidak seragam.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer-pro">
            Manual Settings &copy; <?php echo date('Y'); ?>
        </footer>
    </main>
</div>

<script src="/public/absen/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
