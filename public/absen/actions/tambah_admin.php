<?php
include '../includes/koneksi.php';

// Menjaga agar hanya admin sah yang bisa mendaftarkan admin baru (Bisa dihapus if-nya kalau sedang terkunci total)
if (!isset($_SESSION['admin'])) {
    die("<h2 style='font-family:sans-serif; text-align:center; padding-top:50px; color:#ef4444;'>Akses Ditolak. Anda harus Login terlebih dahulu.</h2>");
}

$pesan = "";
if (isset($_POST['tambah_admin'])) {
    $usernew = mysqli_real_escape_string($koneksi, $_POST['username']);
    $passnew = $_POST['password'];
    
    // Proses pembuatan skema Hash keamanan BCRYPT
    $hash_password = password_hash($passnew, PASSWORD_DEFAULT);
    
    // Cek duplikasi username
    $cek = mysqli_query($koneksi, "SELECT id FROM admin WHERE username = '$usernew'");
    if(mysqli_num_rows($cek) > 0) {
        $pesan = '<div class="alert alert-warning fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> Username sudah dipakai orang lain!</div>';
    } else {
        $insert = mysqli_query($koneksi, "INSERT INTO admin (username, password) VALUES ('$usernew', '$hash_password')");
        if($insert) {
            $pesan = '<div class="alert alert-success fw-bold"><i class="bi bi-check-circle-fill"></i> Admin "'.$usernew.'" sukses diaktifkan!</div>';
        } else {
            $pesan = '<div class="alert alert-danger fw-bold"><i class="bi bi-x-circle-fill"></i> Terjadi kesalahan Server.</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Admin - Attendance Pro</title>
    <link href="/public/absen/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/absen/assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">

<div class="card-pro shadow-lg" style="max-width: 450px; width: 100%;">
    <div class="text-center mb-4">
        <h4 class="fw-bold h-title">Registrasi Admin Baru</h4>
        <p class="text-muted-pro small mb-0">Tambah staff operator rahasia ke dalam Panel Admin.</p>
    </div>
    
    <?php echo $pesan; ?>
    
    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label small fw-bold">Username Login Baru</label>
            <input type="text" name="username" class="form-control" placeholder="Contoh: kepsek_01" required autocomplete="off">
        </div>
        <div class="mb-4">
            <label class="form-label small fw-bold">Password Login</label>
            <input type="password" name="password" class="form-control" placeholder="Masukan sandi yang kuat" required>
        </div>
        
        <button type="submit" name="tambah_admin" class="btn-pro btn-pro-primary w-100 mb-3">
            <i class="bi bi-person-plus-fill me-2"></i> Tambah Akses Admin
        </button>
        <a href="dashboard.php" class="btn btn-outline-secondary w-100 fw-bold border-0">Kembali ke Dasbor</a>
    </form>
</div>

</body>
</html>
