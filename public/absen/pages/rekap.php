<?php
include '../includes/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit;
}

// Filter Dasar
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'siswa';
$filter_kelas = isset($_GET['kelas']) ? $_GET['kelas'] : '';

// Nama-nama bulan baku
$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

// Jumlah hari
$last_day = date('t', strtotime("$tahun-$bulan-01"));

// List Kelas
$list_kelas = mysqli_query($koneksi, "SELECT DISTINCT kelas_mapel FROM users WHERE tipe = 'siswa' ORDER BY kelas_mapel ASC");

// Ambil data users sesuai tab & kelas
$sql_users = "SELECT * FROM users WHERE tipe = '$tab'";
if ($tab == 'siswa' && $filter_kelas != '') {
    $sql_users .= " AND kelas_mapel = '" . mysqli_real_escape_string($koneksi, $filter_kelas) . "'";
}
$sql_users .= " ORDER BY nama ASC";
$res_users = mysqli_query($koneksi, $sql_users);

// Mapping data absensi
$absensi_map = [];
$sql_absensi = "SELECT user_id, DAY(tanggal) as tgl, status FROM absensi 
                WHERE MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND tipe = '$tab'";
$res_absensi = mysqli_query($koneksi, $sql_absensi);
while ($row = mysqli_fetch_assoc($res_absensi)) {
    $absensi_map[$row['user_id']][$row['tgl']] = $row['status'];
}

// Mapping Jadwal (Khusus Guru)
$jadwal_map = [];
if ($tab == 'guru') {
    $res_jadwal = mysqli_query($koneksi, "SELECT * FROM jadwal");
    while ($j = mysqli_fetch_assoc($res_jadwal)) {
        $jadwal_map[$j['user_id']][] = $j['hari'];
    }
}

$hari_ini_ind = [
    1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 
    4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Bulanan - <?php echo $app_name; ?></title>
    <link href="/public/absen/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/absen/assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=1.1">
    <link rel="icon" href="<?php echo $app_favicon; ?>">
    <script src="/public/assets/js/qrcode.min.js"></script>
    <style>
        .rekap-grid { font-size: 0.7rem; border-collapse: collapse; width: 100%; color: var(--text-primary); table-layout: auto; }
        .rekap-grid th, .rekap-grid td { 
            border: 1px solid var(--border-color);
            padding: 6px 2px;
            text-align: center;
        }
        .rekap-grid .name-col { 
            text-align: left; 
            padding-left: 10px; 
            background: var(--bg-card); 
            position: sticky; 
            left: 0; 
            z-index: 5;
            box-shadow: 4px 0 8px rgba(0,0,0,0.1);
            border-right: 2px solid var(--border-color) !important;
            min-width: 140px;
        }
        .rekap-grid tr:hover .name-col { background: var(--hover-bg); }
        .hadir-cell { background-color: rgba(14, 165, 233, 0.1); color: var(--success); font-weight: 700; }
        .tj-cell { background-color: rgba(100, 116, 139, 0.1); color: #64748b; font-weight: 700; }
        .total-badge { font-weight: 800; color: var(--accent); }
        .rekap-grid .tot-col { background: var(--hover-bg); font-weight: 800; border-left: 2px solid var(--border-color); }
        .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        
        @media print {
            @page { size: landscape; margin: 0.6cm; }
            .no-print, .sidebar { display: none !important; }
            .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; overflow: visible !important; }
            body { background: #fff !important; }
            .card-pro { border: none !important; box-shadow: none !important; padding: 0 !important; margin: 5px 0 0 0 !important; width: 100% !important; }
            .rekap-grid { font-size: 0.48rem !important; width: 100% !important; border: 1px solid #000; border-collapse: collapse !important; table-layout: fixed !important; margin-top: 10px !important; }
            .rekap-grid th, .rekap-grid td { border: 1px solid #000 !important; color: #000 !important; padding: 3px 0 !important; line-height: 1.2 !important; word-wrap: break-word; overflow: hidden; }
            .rekap-grid .name-col { width: 100px !important; min-width: 100px !important; max-width: 100px !important; position: static !important; background: transparent !important; box-shadow: none !important; font-size: 0.45rem !important; border-right: 1px solid #000 !important; padding-left: 2px !important; }
            .rekap-grid .no-col { width: 28px !important; }
            .rekap-grid .day-col { width: auto !important; }
            .rekap-grid .tot-col { width: 32px !important; border-left: 1px solid #000 !important; background: #f8fafc !important; }
            .hadir-cell { background-color: #f0fdf4 !important; color: #000 !important; -webkit-print-color-adjust: exact; }
            .tj-cell { background-color: #f8fafc !important; color: #000 !important; -webkit-print-color-adjust: exact; }
            .row { margin-left: 0 !important; margin-right: 0 !important; }
            .text-dark, .text-muted, .text-secondary, .text-primary, p, span, h5, h6 { color: #000 !important; }
            .print-line { border-top: 2px solid #000 !important; opacity: 1 !important; margin-top: 30px !important; margin-bottom: 20px !important; display: block !important; }
        }
    </style>
</head>
<body>

<div class="wrapper">
    <?php include '../includes/sidebar.php'; ?>

    <main class="main-content">
        <header class="content-header no-print">
            <div class="h-title">Laporan Rekapitulasi Kehadiran</div>
            <button onclick="window.print()" class="btn-pro btn-pro-outline py-1 px-3">
                <i class="bi bi-printer"></i> Cetak Laporan
            </button>
        </header>

        <div class="content-padding">

            <div class="card-pro no-print mb-4">
                <form action="" method="GET" class="row g-3">
                    <input type="hidden" name="tab" value="<?php echo $tab; ?>">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Bulan Pelaporan</label>
                        <select name="bulan" class="form-select">
                            <?php foreach ($nama_bulan as $k => $v): ?>
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
                    <?php if ($tab == 'siswa'): ?>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Parameter Kelas</label>
                        <select name="kelas" class="form-select">
                            <option value="">Seluruh Kelas</option>
                            <?php while($k = mysqli_fetch_assoc($list_kelas)): ?>
                                <option value="<?php echo $k['kelas_mapel']; ?>" <?php echo $filter_kelas == $k['kelas_mapel'] ? 'selected' : ''; ?>><?php echo $k['kelas_mapel']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn-pro btn-pro-primary w-100">Buka Laporan</button>
                    </div>
                </form>
            </div>

            <div class="mb-4 no-print d-flex gap-2">
                <a href="?tab=siswa&bulan=<?php echo $bulan; ?>&tahun=<?php echo $tahun; ?>" class="btn-pro <?php echo $tab == 'siswa' ? 'btn-pro-primary active' : 'bg-white border text-secondary'; ?> px-4">
                    Rekapitulasi Siswa
                </a>
                <a href="?tab=guru&bulan=<?php echo $bulan; ?>&tahun=<?php echo $tahun; ?>" class="btn-pro <?php echo $tab == 'guru' ? 'btn-pro-primary active' : 'bg-white border text-secondary'; ?> px-4">
                    Rekapitulasi Guru
                </a>
            </div>

            <div class="card-pro p-4 p-md-5 shadow-sm">
                <div class="text-center mb-4">
                    <h5 class="fw-bold text-primary mb-1">LAPORAN KEHADIRAN PERIODE <?php echo strtoupper($nama_bulan[$bulan]); ?> <?php echo $tahun; ?></h5>
                    <p class="text-muted-pro small mb-0">Subyek: <?php echo strtoupper($tab); ?> <?php echo ($filter_kelas != '') ? "| " . $filter_kelas : ""; ?></p>
                </div>
                <div class="table-responsive border rounded-3">
                    <table class="rekap-grid">
                        <thead>
                            <tr class="bg-light">
                                <th class="no-col" style="background: var(--hover-bg);">No</th>
                                <th class="name-col border-end text-uppercase">Nama Anggota</th>
                                <?php for($d=1; $d<=$last_day; $d++): ?>
                                    <th class="day-col"><?php echo $d; ?></th>
                                <?php endfor; ?>
                                <th class="border-start bg-soft-blue tot-col">TOT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while($u = mysqli_fetch_assoc($res_users)): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td class="name-col border-end fw-bold"><?php echo $u['nama']; ?></td>
                                <?php 
                                $total_hadir = 0;
                                for($d=1; $d<=$last_day; $d++): 
                                    $status_day = isset($absensi_map[$u['id']][$d]) ? $absensi_map[$u['id']][$d] : null;
                                    
                                    // Logika tambahan: Jika tidak scan tapi tidak ada jadwal
                                    if (!$status_day && $tab == 'guru') {
                                        $date_str = "$tahun-$bulan-" . str_pad($d, 2, '0', STR_PAD_LEFT);
                                        $h_angka = date('N', strtotime($date_str));
                                        $n_hari = $hari_ini_ind[$h_angka];
                                        
                                        if ($n_hari != 'Minggu') { // Minggu abaikan
                                            if (!isset($jadwal_map[$u['id']]) || !in_array($n_hari, $jadwal_map[$u['id']])) {
                                                $status_day = 'Tidak Ada Jam';
                                            }
                                        }
                                    }

                                    if ($status_day == 'Hadir') $total_hadir++;
                                    
                                    $cell_class = '';
                                    $display_text = '•';
                                    
                                    if ($status_day == 'Hadir') {
                                        $cell_class = 'hadir-cell';
                                        $display_text = 'H';
                                    } elseif ($status_day == 'Tidak Ada Jam') {
                                        $cell_class = 'tj-cell';
                                        $display_text = 'TJ';
                                    }
                                ?>
                                    <td class="<?php echo $cell_class; ?>">
                                        <?php echo $display_text; ?>
                                    </td>
                                <?php endfor; ?>
                                <td class="bg-light border-start total-badge"><?php echo $total_hadir; ?></td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if (mysqli_num_rows($res_users) == 0): ?>
                            <tr>
                                <td colspan="<?php echo $last_day + 3; ?>" class="py-5 text-center text-muted-pro italic">Parameter tidak menemukan data yang relevan.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Legend -->
                <div class="mt-4 small d-flex gap-4 justify-content-center text-secondary">
                    <span class="d-flex align-items-center gap-2"><div class="rounded-circle" style="width:10px; height:10px; background:var(--success); border:1px solid var(--success);"></div> <strong>H</strong> = Hadir</span>
                    <span class="d-flex align-items-center gap-2"><div class="rounded-circle" style="width:10px; height:10px; background:#64748b; border:1px solid #64748b;"></div> <strong>TJ</strong> = Tidak Ada Jam</span>
                    <span class="d-flex align-items-center gap-2"><span class="fw-bold fs-5" style="line-height:1;">•</span> = Belum Absen</span>
                </div>
            </div>

            <div class="d-none d-print-block mt-4">
                <div class="row">
                    <div class="col-8"></div>
                    <div class="col-4 text-center" style="color: #000 !important;">
                        <p class="small mb-1" style="color: #000 !important;"><?php echo $sys_cfg['tempat_cetak'] ?: 'Kota Anda'; ?>, <?php echo date('d M Y'); ?><br>Staf Administrasi,</p>
                        
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
                        <p class="small" style="color: #000 !important;">Electronic Attendance System</p>
                    </div>
                </div>
            </div>
            <hr class="d-none d-print-block print-line">
            
        </div>

        <footer class="footer-pro">
            Report Center &copy; <?php echo date('Y'); ?>
        </footer>
    </main>
</div>

<script src="/public/absen/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

