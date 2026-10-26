<?php
include '../includes/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit;
}

$message = "";

// Tambah Admin
if (isset($_POST['tambah'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];
    
    // Cek duplikasi username
    $cek = mysqli_query($koneksi, "SELECT id FROM admin WHERE username = '$username'");
    if(mysqli_num_rows($cek) > 0) {
        $message = "error|Username sudah digunakan oleh admin lain.";
    } else {
        $hash_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO admin (username, password) VALUES ('$username', '$hash_password')";
        if (mysqli_query($koneksi, $query)) {
            $message = "success|Admin baru berhasil ditambahkan.";
        } else {
            $message = "error|Gagal menambahkan admin sistem.";
        }
    }
}

// Hapus Admin
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    // Cegah admin menghapus dirinya sendiri saat sedang login
    $q_del = mysqli_query($koneksi, "SELECT username FROM admin WHERE id = '$id'");
    if(mysqli_num_rows($q_del) > 0) {
        $r_del = mysqli_fetch_assoc($q_del);
        if($r_del['username'] == $_SESSION['admin']) {
             header("Location: admin.php?msg=self_delete");
             exit;
        } else {
             mysqli_query($koneksi, "DELETE FROM admin WHERE id = '$id'");
             header("Location: admin.php?msg=deleted");
             exit;
        }
    }
}

$query = "SELECT * FROM admin ORDER BY id ASC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Admin - <?php echo $app_name; ?></title>
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
            <div class="h-title">Manajemen Administrator</div>
            <button class="btn-pro btn-pro-primary py-1 px-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-person-plus"></i> Tambah Admin
            </button>
        </header>

        <div class="content-padding">

            <?php if (isset($_GET['msg'])): ?>
                <?php if ($_GET['msg'] == 'deleted'): ?>
                    <script>Swal.fire({ icon: 'success', title: 'Terhapus', text: 'Admin telah dihapus dari sistem.', showConfirmButton: false, timer: 2000 });</script>
                <?php elseif ($_GET['msg'] == 'self_delete'): ?>
                    <script>Swal.fire({ icon: 'error', title: 'Aksi Ditolak', text: 'Anda tidak dapat menghapus akun Anda sendiri saat ini sedang terpakai.' });</script>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($message): 
                $m = explode('|', $message);
                echo "<script>Swal.fire({ icon: '".($m[0] == 'success' ? 'success' : 'error')."', title: '".($m[0] == 'success' ? 'Berhasil' : 'Kegagalan')."', text: '".$m[1]."' });</script>";
            endif; ?>

            <div class="card-pro">
                <div class="table-responsive-pro">
                    <table class="table-pro">
                        <thead>
                            <tr>
                                <th class="ps-4" style="width: 50px;">No</th>
                                <th>Username Log-In</th>
                                <th>Status Keamanan</th>
                                <th class="text-center pe-4" style="width: 120px;">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="ps-4 text-secondary"><?php echo $no++; ?></td>
                                <td>
                                    <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                        <div class="bg-soft-blue d-inline-flex p-2 rounded-circle">
                                            <i class="bi bi-shield-lock-fill text-primary" style="font-size: 1rem;"></i>
                                        </div>
                                        <?php echo $row['username']; ?>
                                        <?php if($row['username'] == $_SESSION['admin']) echo '<span class="badge bg-primary ms-2" style="font-size:0.7em;">Anda</span>'; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-pro bg-light border text-success">
                                        <i class="bi bi-check-circle-fill me-1"></i> Bcrypt Hashed
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <a href="?hapus=<?php echo $row['id']; ?>" class="btn-pro btn-pro-outline px-2 py-1 text-danger" title="Hapus Akses" onclick="return confirm('Apakah Anda yakin ingin menghapus admin ini?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <footer class="footer-pro mt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <span>Attendance Pro &copy; <?php echo date('Y'); ?></span>
                    <span>Keamanan Sistem</span>
                </div>
            </footer>
        </div>

        <footer class="footer-pro">
            Management &copy; <?php echo date('Y'); ?>
        </footer>
    </main>
</div>

<!-- Modal Form Input Pro -->
<div class="modal fade" id="modalTambah">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-pro">
            <div class="modal-header">
                <h6 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2 text-primary"></i>Registrasi Admin Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Username Baru</label>
                        <input type="text" name="username" class="form-control" placeholder="Contoh: staff_admin1" required autocomplete="off">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-secondary">Kata Sandi Kuat</label>
                        <input type="password" name="password" class="form-control" placeholder="Gunakan kombinasi huruf dan angka" required>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 p-3">
                    <button type="button" class="btn-pro btn-pro-outline" data-bs-dismiss="modal">Batalkan</button>
                    <button type="submit" name="tambah" class="btn-pro btn-pro-primary">Simpan Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/public/absen/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
