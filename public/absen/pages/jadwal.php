<?php
include '../includes/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit;
}

$message = "";

// Simpan/Update Jadwal
if (isset($_POST['simpan_jadwal'])) {
    $user_id = $_POST['user_id'];
    $hari_terpilih = isset($_POST['hari']) ? $_POST['hari'] : [];
    
    // Hapus jadwal lama untuk user ini
    mysqli_query($koneksi, "DELETE FROM jadwal WHERE user_id = '$user_id'");
    
    // Insert jadwal baru
    if (!empty($hari_terpilih)) {
        foreach ($hari_terpilih as $hari) {
            $hari = mysqli_real_escape_string($koneksi, $hari);
            mysqli_query($koneksi, "INSERT INTO jadwal (user_id, hari) VALUES ('$user_id', '$hari')");
        }
    }
    $message = "success|Jadwal berhasil diperbarui.";
}

// Ambil Data Guru
$query_guru = "SELECT * FROM users WHERE tipe = 'guru' ORDER BY nama ASC";
$result_guru = mysqli_query($koneksi, $query_guru);

// Ambil Mapping Jadwal
$jadwal_map = [];
$res_jadwal = mysqli_query($koneksi, "SELECT * FROM jadwal");
while ($j = mysqli_fetch_assoc($res_jadwal)) {
    $jadwal_map[$j['user_id']][] = $j['hari'];
}

$hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Mengajar - <?php echo $app_name; ?></title>
    <link href="/public/absen/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/absen/assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=1.1">
    <link rel="icon" href="<?php echo $app_favicon; ?>">
    <script src="/public/absen/assets/js/sweetalert2.all.min.js"></script>
    <style>
        .hari-badge { font-size: 0.75rem; padding: 4px 8px; border-radius: 6px; margin-right: 4px; display: inline-block; margin-bottom: 4px; }
        .bg-soft-primary { background-color: rgba(14, 165, 233, 0.1); color: var(--primary); border: 1px solid rgba(14, 165, 233, 0.2); }
        .bg-soft-secondary { background-color: #f8f9fa; color: #6c757d; border: 1px solid #dee2e6; }
    </style>
</head>
<body>

<div class="wrapper">
    <?php include '../includes/sidebar.php'; ?>

    <main class="main-content">
        <header class="content-header">
            <div class="h-title">Manajemen Jadwal Mengajar</div>
            <div class="small text-muted-pro">Atur hari mengajar untuk tenaga pengajar (Guru).</div>
        </header>

        <div class="content-padding">

            <?php if ($message): 
                $m = explode('|', $message);
                echo "<script>Swal.fire({ icon: '".($m[0] == 'success' ? 'success' : 'error')."', title: 'Informasi', text: '".$m[1]."' });</script>";
            endif; ?>

            <div class="card-pro p-0 overflow-hidden shadow-sm">
                <div class="table-responsive-pro">
                    <table class="table-pro">
                        <thead>
                            <tr>
                                <th class="ps-4" style="width: 50px;">No</th>
                                <th>Nama Guru</th>
                                <th>Bidang / Mata Pelajaran</th>
                                <th>Hari Mengajar</th>
                                <th class="text-center pe-4" style="width: 120px;">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while($row = mysqli_fetch_assoc($result_guru)): ?>
                            <tr>
                                <td class="ps-4 text-secondary"><?php echo $no++; ?></td>
                                <td><div class="fw-bold text-dark"><?php echo $row['nama']; ?></div></td>
                                <td><?php echo $row['kelas_mapel']; ?></td>
                                <td>
                                    <?php 
                                    if (isset($jadwal_map[$row['id']])) {
                                        foreach ($hari_list as $h) {
                                            $active = in_array($h, $jadwal_map[$row['id']]);
                                            echo '<span class="hari-badge '.($active ? 'bg-soft-primary' : 'bg-soft-secondary').'">'.$h.'</span>';
                                        }
                                    } else {
                                        echo '<span class="text-muted small">Belum diatur</span>';
                                    }
                                    ?>
                                </td>
                                <td class="text-center pe-4">
                                    <button class="btn-pro btn-pro-outline px-3 py-1" onclick="editJadwal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['nama']); ?>', <?php echo isset($jadwal_map[$row['id']]) ? json_encode($jadwal_map[$row['id']]) : '[]'; ?>)">
                                        <i class="bi bi-pencil-square me-1"></i> Edit
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if (mysqli_num_rows($result_guru) == 0): ?>
                            <tr>
                                <td colspan="5" class="py-5 text-center text-muted-pro italic">Belum ada data guru.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <footer class="footer-pro">
            Schedule Management &copy; <?php echo date('Y'); ?>
        </footer>
    </main>
</div>

<!-- Modal Edit Jadwal -->
<div class="modal fade" id="modalJadwal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-pro">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Pengaturan Hari Mengajar</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body p-4">
                    <p class="mb-3 text-secondary">Silakan centang hari di mana <strong id="namaGuruDisplay" class="text-dark"></strong> memiliki jadwal mengajar.</p>
                    <input type="hidden" name="user_id" id="userInputId">
                    
                    <div class="row g-3">
                        <?php foreach($hari_list as $h): ?>
                        <div class="col-6">
                            <div class="form-check p-3 border rounded-3 hover-bg-light" style="cursor:pointer;" onclick="toggleCheck('check<?php echo $h; ?>')">
                                <input class="form-check-input" type="checkbox" name="hari[]" value="<?php echo $h; ?>" id="check<?php echo $h; ?>" style="pointer-events: none;">
                                <label class="form-check-label fw-bold ms-2" style="cursor:pointer; pointer-events: none;">
                                    <?php echo $h; ?>
                                </label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 p-3">
                    <button type="button" class="btn-pro btn-pro-outline" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="simpan_jadwal" class="btn-pro btn-pro-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editJadwal(id, nama, jadwal) {
    document.getElementById('userInputId').value = id;
    document.getElementById('namaGuruDisplay').innerText = nama;
    
    // Reset checks
    const checks = document.querySelectorAll('.form-check-input');
    checks.forEach(c => c.checked = false);
    
    // Set checks based on current jadwal
    jadwal.forEach(h => {
        const cb = document.getElementById('check' + h);
        if(cb) cb.checked = true;
    });
    
    new bootstrap.Modal(document.getElementById('modalJadwal')).show();
}

function toggleCheck(id) {
    const cb = document.getElementById(id);
    cb.checked = !cb.checked;
}
</script>
<script src="/public/absen/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

