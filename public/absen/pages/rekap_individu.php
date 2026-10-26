<?php
include '../includes/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit;
}

// Set parameter default
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$tipe = isset($_GET['tipe']) ? $_GET['tipe'] : 'guru';
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';

// Nama bulan
$nama_bulan_arr = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

$last_day = date('t', strtotime("$tahun-$bulan-01"));

// Data Semua User untuk Dropdown
$res_all_users = mysqli_query($koneksi, "SELECT id, nama, tipe, qr_code, kelas_mapel FROM users ORDER BY nama ASC");
$users_data = [];
$selected_user_data = null;

while ($u = mysqli_fetch_assoc($res_all_users)) {
    $users_data[] = $u;
    if ($user_id == $u['id']) {
        $selected_user_data = $u;
    }
}

// Mengambil Data Presensi Jika Ada User Terpilih
$absensi_hari = [];
if ($user_id != '') {
    $q_absen = "SELECT DAY(tanggal) as tgl, jam_masuk, jam_keluar, status FROM absensi 
                WHERE user_id = '$user_id' AND MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun'";
    $res_absen = mysqli_query($koneksi, $q_absen);
    while($row = mysqli_fetch_assoc($res_absen)) {
        $absensi_hari[$row['tgl']] = [
            'masuk' => $row['jam_masuk'],
            'keluar' => $row['jam_keluar'],
            'status' => $row['status']
        ];
    }
}

// Mapping Jadwal untuk User Terpilih
$user_jadwal = [];
if ($selected_user_data && $selected_user_data['tipe'] == 'guru') {
    $uid = $selected_user_data['id'];
    $res_j = mysqli_query($koneksi, "SELECT hari FROM jadwal WHERE user_id = '$uid'");
    while($rj = mysqli_fetch_assoc($res_j)) {
        $user_jadwal[] = $rj['hari'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Individu - <?php echo $app_name; ?></title>
    <link href="/public/absen/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/absen/assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=1.1">
    <link rel="icon" href="<?php echo $app_favicon; ?>">
    <script src="/public/assets/js/qrcode.min.js"></script>
    <style>
        .slip-header { border-bottom: 3px double var(--border-color); margin-bottom: 20px; padding-bottom: 10px; }
        .table-slip th, .table-slip td { padding: 8px 12px; border: 1px solid var(--border-color); font-size: 0.85rem; }
        .table-slip th { background: var(--hover-bg); text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); }
        .weekend td { background-color: rgba(239, 68, 68, 0.1) !important; color: #ef4444; }
        .row-data:nth-child(even) td { background-color: var(--hover-bg); }
        .table-slip td { color: var(--text-primary); background-color: transparent; }
        
        .table { --bs-table-bg: transparent; --bs-table-color: var(--text-primary); }
        
        @media print {
            @page { size: portrait; margin: 0.8cm; }
            .no-print, .sidebar { display: none !important; }
            .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
            .logo-header { height: 60px; width: auto; }
            .bg-dark { background-color: #000 !important; }
            .text-white { color: #fff !important; }
            .text-dark, .text-muted, .text-secondary, .text-primary, p, span, h5, h6 { color: #000 !important; }
            .print-line { border-top: 2px solid #000 !important; opacity: 1 !important; margin-top: 30px !important; margin-bottom: 20px !important; display: block !important; }
            .slip-header { border-bottom: 2px solid #000 !important; margin-bottom: 15px !important; }
            .table-slip { width: 100% !important; border-collapse: collapse !important; table-layout: auto !important; }
            .table-slip th, .table-slip td { border: 1px solid #000 !important; color: #000 !important; padding: 6px 4px !important; font-size: 0.75rem !important; }
            .table-slip th { background: #f8fafc !important; -webkit-print-color-adjust: exact; }
            .weekend td { background-color: #fef2f2 !important; color: #000 !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>

<div class="wrapper">
    <?php include '../includes/sidebar.php'; ?>

    <main class="main-content">
        <header class="content-header no-print">
            <div class="h-title">Cetak Slip Kehadiran Individu</div>
            <button onclick="window.print()" class="btn-pro btn-pro-outline py-1 px-3" <?php echo !$selected_user_data ? 'disabled' : ''; ?>>
                <i class="bi bi-printer"></i> Cetak Slip
            </button>
        </header>

        <div class="content-padding">

            <div class="card-pro no-print mb-4">
                <form action="" method="GET" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Kategori Anggota</label>
                        <select name="tipe" id="tipe-select" class="form-select" onchange="filterUserDropdown()">
                            <option value="guru" <?php echo $tipe == 'guru' ? 'selected' : ''; ?>>T. Pengajar (Guru)</option>
                            <option value="siswa" <?php echo $tipe == 'siswa' ? 'selected' : ''; ?>>P. Didik (Siswa)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Pilih Nama Lengkap</label>
                        <select name="user_id" id="user-select" class="form-select" required>
                            <option value="">-- Pilih Anggota --</option>
                            <?php foreach ($users_data as $u): ?>
                                <option class="user-opt" data-tipe="<?php echo $u['tipe']; ?>" value="<?php echo $u['id']; ?>" <?php echo $user_id == $u['id'] ? 'selected' : ''; ?>>
                                    <?php echo $u['nama']; ?> (<?php echo $u['kelas_mapel']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Bulan</label>
                        <select name="bulan" class="form-select">
                            <?php foreach ($nama_bulan_arr as $k => $v): ?>
                                <option value="<?php echo $k; ?>" <?php echo $bulan == $k ? 'selected' : ''; ?>><?php echo $v; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Tahun</label>
                        <select name="tahun" class="form-select">
                            <?php for($i=date('Y'); $i>=2024; $i--): ?>
                                <option value="<?php echo $i; ?>" <?php echo $tahun == $i ? 'selected' : ''; ?>><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn-pro btn-pro-primary w-100">Buka Daftar</button>
                    </div>
                </form>
            </div>

            <?php if ($selected_user_data): ?>
            <div class="card-pro p-5">
                <div class="slip-header text-center">
                    <h5 class="fw-bold mb-1 text-dark" style="letter-spacing: 1px;">SLIP RINCIAN KEHADIRAN DIGITAL</h5>
                    <p class="mb-0 small fw-bold text-dark">ATTENDANCE PRO RECORD SYSTEM</p>
                </div>
                
                <div class="row mb-4">
                    <div class="col-8">
                        <table class="table table-sm table-borderless w-auto small mb-0">
                            <tr>
                                <td class="text-secondary fw-bold" style="width: 140px;">Nama Lengkap</td>
                                <td style="width: 10px;">:</td>
                                <td class="fw-bold text-dark text-uppercase"><?php echo $selected_user_data['nama']; ?></td>
                            </tr>
                            <tr>
                                <td class="text-secondary fw-bold">ID Sinkronisasi</td>
                                <td>:</td>
                                <td class="font-monospace text-primary fw-bold"><?php echo $selected_user_data['qr_code']; ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-4 text-end">
                        <table class="table table-sm table-borderless ms-auto w-auto small mb-0">
                            <tr>
                                <td class="text-secondary fw-bold">Periode Bulan</td>
                                <td>:</td>
                                <td class="fw-bold text-dark text-uppercase"><?php echo $nama_bulan_arr[$bulan] . " " . $tahun; ?></td>
                            </tr>
                            <tr>
                                <td class="text-secondary fw-bold">Tanggal Cetak</td>
                                <td>:</td>
                                <td><?php echo date('d M Y'); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <table class="table table-slip w-100 mb-4">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">TGL</th>
                            <th class="text-center">HARI</th>
                            <th class="text-center" style="width: 150px;">JAM MASUK</th>
                            <th class="text-center" style="width: 150px;">JAM KELUAR</th>
                            <th class="text-center">KETERANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_hadir = 0;
                        for($d=1; $d<=$last_day; $d++): 
                            $date_str = "$tahun-$bulan-" . str_pad($d, 2, '0', STR_PAD_LEFT);
                            $hari_angka = date('N', strtotime($date_str));
                            $nama_hari = ['1' => 'Senin', '2' => 'Selasa', '3' => 'Rabu', '4' => 'Kamis', '5' => 'Jumat', '6' => 'Sabtu', '7' => 'Minggu'][$hari_angka];
                            
                            $is_weekend = ($hari_angka == 7); // Hanya Minggu
                            $row_class = $is_weekend ? 'weekend' : 'row-data';
                            
                            $jam_in = '--:--';
                            $jam_out = '--:--';
                            $status_str = '-';
                            
                            if (isset($absensi_hari[$d])) {
                                $data_H = $absensi_hari[$d];
                                
                                if($data_H['masuk'] && $data_H['masuk'] != '00:00:00'){
                                    $jam_in = date('H:i', strtotime($data_H['masuk']));
                                }
                                
                                if($data_H['keluar'] && $data_H['keluar'] != '00:00:00'){
                                    $jam_out = date('H:i', strtotime($data_H['keluar']));
                                }
                                
                                $status_str = '<span class="fw-bold text-success"><i class="bi bi-check-circle-fill me-1"></i>HADIR</span>';
                                $total_hadir++;
                            } else {
                            // Cek jika guru tapi tidak ada jadwal
                            if (!$is_weekend && $selected_user_data && $selected_user_data['tipe'] == 'guru') {
                                if (!in_array($nama_hari, $user_jadwal)) {
                                    $status_str = '<span class="fw-bold text-secondary text-opacity-75"><i class="bi bi-clock-history me-1"></i>TDK ADA JAM</span>';
                                } else {
                                    $status_str = '<span class="text-danger fw-bold">TIDAK ADA DATA</span>';
                                }
                            } else {
                                if($is_weekend) {
                                    $status_str = 'Libur Akhir Pekan';
                                } else {
                                    $status_str = '<span class="text-danger fw-bold">TIDAK ADA DATA</span>';
                                }
                            }
                        }
                        ?>
                        <tr class="<?php echo $row_class; ?>">
                            <td class="text-center fw-bold"><?php echo str_pad($d, 2, '0', STR_PAD_LEFT); ?></td>
                            <td class="text-center"><?php echo $nama_hari; ?></td>
                            <td class="text-center font-monospace fw-bold text-primary"><?php echo $jam_in; ?></td>
                            <td class="text-center font-monospace fw-bold text-danger"><?php echo $jam_out; ?></td>
                            <td class="text-center"><?php echo $status_str; ?></td>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
                
                <div class="row pt-2 align-items-center">
                    <div class="col-8">
                        <div class="bg-light p-3 border rounded-3 d-inline-block">
                            <span class="small fw-bold text-secondary">Akm. Kehadiran Tertulis:</span> 
                            <span class="fs-5 fw-bold ms-2 text-dark"><?php echo $total_hadir; ?> Hari</span>
                        </div>
                    </div>
                </div>
                <div class="d-none d-print-block mt-4">
                    <div class="row">
                        <div class="col-8">
                            <div class="mt-3">
                                <div class="d-flex gap-3 small" style="color: #000 !important;">
                                    <span class="d-flex align-items-center gap-1">Hadir (H)</span>
                                    <span class="d-flex align-items-center gap-1">Tidak Ada Jam (TJ)</span>
                                    <span class="d-flex align-items-center gap-1">Belum Ada Keterangan (•)</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 text-center mt-3 mt-lg-0" style="color: #000 !important;">
                            <p class="mb-1 small" style="color: #000 !important;"><?php echo $sys_cfg['tempat_cetak'] ?: 'Kota Anda'; ?>, <?php echo date('d M Y'); ?><br>Disahkan oleh,</p>
                            
                            <?php 
                                $admin_str = $sys_cfg['nama_admin'] ?: 'Administrator';
                                $qr_data = urlencode("Dokumen Valid. Dicetak oleh: " . $admin_str . " pada " . date('d M Y H:i'));
                        <div id="qrcode" style="display:flex; justify-content:center; margin:12px 0;"></div>
                        <script>
                            new QRCode(document.getElementById("qrcode"), {
                                text: "<?php echo urldecode($qr_data); ?>",
                                width: 65,
                                height: 65,
                                correctLevel : QRCode.CorrectLevel.L
                            });
                        </script>
                            
                            <p class="fw-bold mb-0 text-uppercase" style="color: #000 !important;"><?php echo $sys_cfg['nama_admin'] ?: 'Administrator'; ?></p>
                            <p class="small" style="color: #000 !important;">Admin Absensi / Personalia</p>
                        </div>
                    </div>
                </div>
                <hr class="d-none d-print-block print-line">
            <?php else: ?>
            <div class="card-pro text-center py-5 opacity-50 no-print">
                <i class="bi bi-file-person display-1 text-secondary mb-3"></i>
                <h5 class="fw-bold text-dark">Data Tidak Ditentukan</h5>
                <p class="small text-muted">Silakan pilih kategori dan nama anggota, lalu klik Buka Daftar.</p>
            </div>
            <?php endif; ?>
            
        </div>

        <footer class="footer-pro">
            Personal Reports &copy; <?php echo date('Y'); ?>
        </footer>
    </main>
</div>

<script>
function filterUserDropdown(init = false) {
    const tipeSelect = document.getElementById('tipe-select').value;
    const userSelect = document.getElementById('user-select');
    const options = userSelect.getElementsByClassName('user-opt');
    const currentValue = userSelect.value;
    
    let currentStillValid = false;
    
    // Perlihatkan hanya opsi dengan data-tipe yang sesuai
    for (let i = 0; i < options.length; i++) {
        if (options[i].getAttribute('data-tipe') === tipeSelect) {
            options[i].style.display = '';
            if (options[i].value === currentValue && currentValue !== "") {
                currentStillValid = true;
            }
        } else {
            options[i].style.display = 'none';
        }
    }
    
    // Hanya reset jika nilai saat ini tidak valid atau bukan saat inisialisasi awal
    if (!currentStillValid && !init) {
        userSelect.value = "";
    }
}

// Jalankan seketika saat halamann dimuat untuk menyingkronkan tipe dengan UI
document.addEventListener('DOMContentLoaded', function() {
    filterUserDropdown(true);
});

</script>

<script src="/public/absen/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
