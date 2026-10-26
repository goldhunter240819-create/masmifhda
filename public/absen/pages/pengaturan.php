<?php
include '../includes/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit;
}

$message = "";

// Mengubah Pengaturan
if (isset($_POST['simpan'])) {
    $m_masuk = mysqli_real_escape_string($koneksi, $_POST['mulai_masuk']);
    $a_masuk = mysqli_real_escape_string($koneksi, $_POST['akhir_masuk']);
    $m_keluar = mysqli_real_escape_string($koneksi, $_POST['mulai_keluar']);
    $a_keluar = mysqli_real_escape_string($koneksi, $_POST['akhir_keluar']);
    
    $query = "UPDATE pengaturan SET mulai_masuk='$m_masuk', akhir_masuk='$a_masuk', mulai_keluar='$m_keluar', akhir_keluar='$a_keluar' WHERE id=1";
    if (mysqli_query($koneksi, $query)) {
        $message = "success|Pengaturan rentang jam absensi berhasil diperbarui.";
    } else {
        $message = "error|Gagal menyimpan pengaturan ke basis data.";
    }
}

// Mengambil Data
$result = mysqli_query($koneksi, "SELECT * FROM pengaturan WHERE id=1");
$row = mysqli_fetch_assoc($result);

// Default jika kosong/error saat awal buat
if(!$row) {
    $row = [
        'mulai_masuk' => '06:00:00',
        'akhir_masuk' => '08:00:00',
        'mulai_keluar' => '14:00:00',
        'akhir_keluar' => '17:00:00'
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rentang Jam Absensi - <?php echo $app_name; ?></title>
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
            <div class="h-title">Penyesuaian Waktu Pemindai</div>
            <div class="small text-muted-pro">Manajemen rentang waktu kehadiran otomatis.</div>
        </header>

        <div class="content-padding">

            <?php if ($message): 
                $m = explode('|', $message);
                echo "<script>Swal.fire({ icon: '".($m[0] == 'success' ? 'success' : 'error')."', title: '".($m[0] == 'success' ? 'Berhasil' : 'Gagal')."', text: '".$m[1]."', confirmButtonText: 'Tutup', confirmButtonColor: '#0ea5e9' });</script>";
            endif; ?>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card-pro h-100">
                        <div class="d-flex align-items-center mb-4">
                            <div class="stat-icon bg-soft-blue me-3 mb-0"><i class="bi bi-clock-history"></i></div>
                            <div>
                                <h6 class="fw-bold mb-0">Formulir Konfigurasi Waktu</h6>
                                <div class="small text-muted">Gunakan format 24 Jam (JJ:MM)</div>
                            </div>
                        </div>
                        
                        <form action="" method="POST">
                            <div class="border rounded-3 p-3 mb-4 bg-light">
                                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-box-arrow-in-right me-2 text-success"></i>Rentang Absen Masuk</h6>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-secondary">Dimulai Pukul</label>
                                        <input type="time" name="mulai_masuk" class="form-control" value="<?php echo substr($row['mulai_masuk'], 0, 5); ?>" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-secondary">Berakhir Pukul</label>
                                        <input type="time" name="akhir_masuk" class="form-control" value="<?php echo substr($row['akhir_masuk'], 0, 5); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="border rounded-3 p-3 mb-4 bg-light">
                                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-box-arrow-right me-2 text-danger"></i>Rentang Absen Pulang</h6>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-secondary">Dimulai Pukul</label>
                                        <input type="time" name="mulai_keluar" class="form-control" value="<?php echo substr($row['mulai_keluar'], 0, 5); ?>" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-secondary">Berakhir Pukul</label>
                                        <input type="time" name="akhir_keluar" class="form-control" value="<?php echo substr($row['akhir_keluar'], 0, 5); ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" name="simpan" class="btn-pro btn-pro-primary w-100 py-3">
                                <i class="bi bi-save me-2"></i> Terapkan Pengaturan Aturan
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card-pro h-100 border bg-white">
                        <h6 class="fw-bold mb-3"><i class="bi bi-info-circle-fill me-2 text-primary"></i>Panduan Logika Sistem</h6>
                        <ul class="text-secondary ps-3 mb-4" style="font-size: 0.9rem; line-height: 1.8;">
                            <li>Sistem pemindai QR AI akan <strong class="text-dark">mendeteksi otomatis</strong> jenis kehadiran (Masuk/Pulang) berdasarkan jadwal yang Anda simpan di sini.</li>
                            <li>Jika siswa melakukan pen-scan-an pada waktu antara ujung batas masuk hingga awal batas pulang (Jeda Siang), maka AI akan menolaknya dan berkata: <em class="text-dark">"Waktu yang ditentukan tidak sesuai"</em>.</li>
                            <li>Jika siswa melakukan pen-scan-an setelah rentang masuk dan pulang benar-benar selesai (Sore/Malam), maka AI akan menolaknya dengan berkata: <em class="text-dark">"Absen telah ditutup"</em>.</li>
                        </ul>
                    </div>
                </div>
            </div>
            
        </div>

        <footer class="footer-pro">
            Time Configuration &copy; <?php echo date('Y'); ?>
        </footer>
    </main>
</div>

<script src="/public/absen/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>


