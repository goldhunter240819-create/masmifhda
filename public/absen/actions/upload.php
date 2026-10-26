<?php
include '../includes/koneksi.php';
require_once 'SimpleXLSX.php';

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

$status = "";
$message = "";

if (isset($_POST['import'])) {
    if (isset($_FILES['file_excel']) && $_FILES['file_excel']['error'] == 0) {
        $file = $_FILES['file_excel']['tmp_name'];
        
        if ($xlsx = SimpleXLSX::parse($file)) {
            $rows = $xlsx->rows();
            $count = 0;
            
            // Skip header (Baris 1)
            for ($i = 1; $i < count($rows); $i++) {
                $nama = mysqli_real_escape_string($koneksi, $rows[$i][0]);
                $tipe = strtolower(mysqli_real_escape_string($koneksi, $rows[$i][1]));
                $kelas_mapel = mysqli_real_escape_string($koneksi, $rows[$i][2]);
                
                if (empty($nama)) continue;

                // Generate QR ID
                $prefix = ($tipe == 'guru') ? 'GRU' : 'SIS';
                $random = strtoupper(substr(md5(uniqid()), 0, 5));
                $qr_code = $prefix . "-" . $random;

                $query = "INSERT INTO users (nama, tipe, kelas_mapel, qr_code) VALUES ('$nama', '$tipe', '$kelas_mapel', '$qr_code')";
                if (mysqli_query($koneksi, $query)) {
                    $count++;
                }
            }
            $status = "success";
            $message = "$count entri data berhasil diimpor ke sistem.";
        } else {
            $status = "error";
            $message = "Format berkas tidak valid atau pustaka gagal membaca berkas.";
        }
    } else {
        $status = "error";
        $message = "Gagal mengunggah berkas. Pastikan ukuran berkas tidak melebihi batas.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impor Data - Attendance Pro</title>
    <link href="/public/absen/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/absen/assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="/public/absen/assets/js/sweetalert2.all.min.js"></script>
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0">
        <?php include '../includes/sidebar.php'; ?>

        <main class="main-content">
            <header class="content-header mb-5">
                <h4 class="h-title mb-1">Impor Data Anggota</h4>
                <p class="text-muted-pro">Tambahkan data siswa/guru secara massal menggunakan berkas spreadsheet (.xlsx).</p>
            </header>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card-pro h-100">
                        <div class="d-flex align-items-center mb-4">
                            <div class="stat-icon bg-soft-blue me-3 mb-0"><i class="bi bi-file-earmark-spreadsheet"></i></div>
                            <h6 class="fw-bold mb-0">Formulir Unggah Berkas</h6>
                        </div>
                        
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary">Pilih Berkas Excel (.xlsx)</label>
                                <input type="file" name="file_excel" class="form-control" accept=".xlsx" required>
                                <div class="form-text small opacity-50">Pastikan format kolom sesuai dengan template yang disediakan.</div>
                            </div>
                            
                            <button type="submit" name="import" class="btn-pro btn-pro-primary w-100 py-3">
                                <i class="bi bi-cloud-arrow-up"></i> Jalankan Impor Data
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card-pro h-100 border-dashed bg-light bg-opacity-50">
                        <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i>Panduan Format Colom</h6>
                        <p class="small text-secondary mb-4">Mohon sesuaikan susunan kolom pada berkas Excel Anda sebagai berikut:</p>
                        
                        <div class="table-responsive-pro border-0 shadow-none">
                            <table class="table-pro">
                                <thead class="bg-white">
                                    <tr>
                                        <th>Kolom A</th>
                                        <th>Kolom B</th>
                                        <th>Kolom C</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    <tr>
                                        <td>Nama Lengkap</td>
                                        <td>Tipe (siswa/guru)</td>
                                        <td>Kelas/Mapel</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4 p-3 bg-white border rounded-3">
                            <h6 class="small fw-bold mb-2">Peringatan Sistem:</h6>
                            <ul class="small text-secondary mb-0 ps-3">
                                <li>Baris pertama (header) akan otomatis diabaikan oleh sistem.</li>
                                <li>Pastikan kolom Tipe diisi "siswa" atau "guru" dengan huruf kecil.</li>
                                <li>QR ID akan dihasilkan secara otomatis oleh sistem.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($status): ?>
                <script>
                    Swal.fire({
                        icon: '<?php echo $status; ?>',
                        title: '<?php echo $status == "success" ? "Proses Selesai" : "Gagal"; ?>',
                        text: '<?php echo $message; ?>',
                        confirmButtonText: 'Kembali ke Dashboard',
                        confirmButtonColor: '#2563eb'
                    }).then((result) => {
                        window.location.href = 'dashboard.php';
                    });
                </script>
            <?php endif; ?>
            
            <footer class="footer-pro">
                <div class="d-flex justify-content-between align-items-center">
                    <span>Attendance Pro &copy; <?php echo date('Y'); ?></span>
                    <span>Modul Imigrasi Data</span>
                </div>
            </footer>
        </main>
    </div>
</div>

<style>
    .border-dashed { border-style: dashed !important; }
</style>

</body>
</html>
