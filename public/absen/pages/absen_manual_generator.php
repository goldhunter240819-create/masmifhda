<?php
include '../includes/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit;
}

$message = "";

// 2. Logic Generate Massal
if (isset($_POST['generate_massal'])) {
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
    
    // Ambil Pengaturan
    $p_res = mysqli_query($koneksi, "SELECT * FROM pengaturan_manual WHERE id=1");
    $p = mysqli_fetch_assoc($p_res);
    
    $m_m = strtotime($p['mulai_masuk']);
    $a_m = strtotime($p['akhir_masuk']);
    $m_k = strtotime($p['mulai_keluar']);
    $a_k = strtotime($p['akhir_keluar']);
    
    $last_day = date('t', strtotime("$tahun-$bulan-01"));
    $gurus = mysqli_query($koneksi, "SELECT id FROM users WHERE tipe = 'guru'");
    
    // Ambil Mapping Jadwal Manual
    $jadwal_manual_map = [];
    $res_j = mysqli_query($koneksi, "SELECT * FROM jadwal_manual");
    while($rj = mysqli_fetch_assoc($res_j)) {
        $jadwal_manual_map[$rj['user_id']][] = $rj['hari'];
    }

    $hari_ind = [1=>'Senin', 2=>'Selasa', 3=>'Rabu', 4=>'Kamis', 5=>'Jumat', 6=>'Sabtu', 7=>'Minggu'];

    mysqli_begin_transaction($koneksi);
    try {
        // Bersihkan data lama untuk bulan/tahun ini
        mysqli_query($koneksi, "DELETE FROM absensi_manual WHERE MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun'");
        
        while ($g = mysqli_fetch_assoc($gurus)) {
            $user_id = $g['id'];
            $user_schedule = isset($jadwal_manual_map[$user_id]) ? $jadwal_manual_map[$user_id] : [];

            for ($d = 1; $d <= $last_day; $d++) {
                $tgl = "$tahun-$bulan-" . str_pad($d, 2, '0', STR_PAD_LEFT);
                $h_angka = date('N', strtotime($tgl));
                
                if ($h_angka == 7) continue; // Skip Minggu
                
                $nama_hari = $hari_ind[$h_angka];
                $is_teaching = in_array($nama_hari, $user_schedule);

                if ($is_teaching) {
                    $jam_in = date('H:i:s', rand($m_m, $a_m));
                    $jam_out = date('H:i:s', rand($m_k, $a_k));
                    $status = "Hadir";
                    mysqli_query($koneksi, "INSERT INTO absensi_manual (user_id, tanggal, jam_masuk, jam_keluar, status) VALUES ('$user_id', '$tgl', '$jam_in', '$jam_out', '$status')");
                } else {
                    $status = "Tidak Ada Jam";
                    mysqli_query($koneksi, "INSERT INTO absensi_manual (user_id, tanggal, jam_masuk, jam_keluar, status) VALUES ('$user_id', '$tgl', NULL, NULL, '$status')");
                }
            }
        }
        mysqli_commit($koneksi);
        $message = "success|Generator berhasil memproses data semua guru berdasarkan Jadwal Fiktif.";
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $message = "error|Kegagalan sistem saat generate data.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generator Massal - <?php echo $app_name; ?></title>
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
            <div class="h-title">Generator Massal Fiktif</div>
            <div class="small text-muted-pro">Mesin pembuat data kehadiran otomatis.</div>
        </header>

        <div class="content-padding">

            <?php if ($message): 
                $m = explode('|', $message);
                echo "<script>Swal.fire({ icon: '".($m[0] == 'success' ? 'success' : 'error')."', title: 'Sistem', text: '".$m[1]."' });</script>";
            endif; ?>

            <div class="card-pro border-success border-opacity-25" style="border: 2px dashed rgba(14, 165, 233, 0.2);">
                <div class="d-flex align-items-center mb-4">
                    <div class="stat-icon bg-soft-green me-3 mb-0"><i class="bi bi-cpu"></i></div>
                    <div>
                        <h6 class="fw-bold mb-0">Mesin Pembuat Data Massal</h6>
                        <p class="small text-muted mb-0">Generator akan menyesuaikan jam berdasarkan "Jadwal Mengajar Fiktif" yang telah Anda atur.</p>
                    </div>
                </div>

                <form action="" method="POST" class="row g-4 p-3 bg-light rounded-3">
                    <div class="col-md-5">
                        <label class="form-label small fw-bold">Bulan yang ingin di-generate</label>
                        <select name="bulan" class="form-select form-select-lg" required>
                            <?php 
                            $bn = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
                            foreach ($bn as $k => $v): ?>
                                <option value="<?php echo $k; ?>" <?php echo date('m') == $k ? 'selected' : ''; ?>><?php echo $v; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Tahun</label>
                        <select name="tahun" class="form-select form-select-lg" required>
                            <?php for($i=date('Y'); $i>=2024; $i--): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" name="generate_massal" class="btn-pro btn-pro-primary w-100 py-3 shadow-sm" onclick="return confirm('Sistem akan menimpa data pada bulan tersebut. Lanjutkan?')">
                            <i class="bi bi-play-circle-fill me-2"></i> Jalankan Generator
                        </button>
                    </div>
                </form>

                <div class="mt-4 p-3 border rounded-3 bg-white">
                    <h6 class="small fw-bold text-dark mb-2"><i class="bi bi-info-circle me-2"></i>Aturan Generator:</h6>
                    <ul class="small text-secondary mb-0">
                        <li>Mengambil daftar semua guru yang terdaftar di sistem.</li>
                        <li>Memeriksa <strong>Jadwal Mengajar Fiktif</strong> masing-masing guru.</li>
                        <li>Jika ada jadwal: Membuat jam masuk & pulang acak sesuai konfigurasi waktu.</li>
                        <li>Jika tidak ada jadwal: Jam kosong dan status menjadi <strong>"Tidak Ada Jam"</strong>.</li>
                        <li>Melewati hari Minggu secara otomatis.</li>
                    </ul>
                </div>
            </div>
        </div>

        <footer class="footer-pro">
            Data Generator &copy; <?php echo date('Y'); ?>
        </footer>
    </main>
</div>

<script src="/public/absen/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

