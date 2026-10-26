<?php
include '../includes/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit;
}

// Set parameter default
$sel_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$sel_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$sel_user = isset($_GET['user_id']) ? $_GET['user_id'] : '';

$last_day = date('t', strtotime("$sel_tahun-$sel_bulan-01"));

// Data Semua Guru untuk Dropdown
$res_guru = mysqli_query($koneksi, "SELECT id, nama, kelas_mapel, qr_code FROM users WHERE tipe = 'guru' ORDER BY nama ASC");
$gurus_data = [];
$selected_user_data = null;
while ($u = mysqli_fetch_assoc($res_guru)) {
    $gurus_data[] = $u;
    if ($sel_user == $u['id']) {
        $selected_user_data = $u;
    }
}

// Mengambil Data Absensi Manual
$absensi_hari = [];
if ($sel_user != '') {
    $q_absen = "SELECT DAY(tanggal) as tgl, jam_masuk, jam_keluar, status FROM absensi_manual 
                WHERE user_id = '$sel_user' AND MONTH(tanggal) = '$sel_bulan' AND YEAR(tanggal) = '$sel_tahun'";
    $res_absen = mysqli_query($koneksi, $q_absen);
    while($row = mysqli_fetch_assoc($res_absen)) {
        $absensi_hari[$row['tgl']] = $row;
    }
}

$nama_bulan_arr = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Fiktif - <?php echo $app_name; ?></title>
    <link href="/public/absen/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/absen/assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=1.1">
    <link rel="icon" href="<?php echo $app_favicon; ?>">
    <style>
        .slip-header { border-bottom: 2px solid var(--border-color); margin-bottom: 20px; padding-bottom: 10px; }
        .internal-label { 
            background: rgba(239, 68, 68, 0.1); 
            color: #ef4444; 
            padding: 4px 12px; 
            border-radius: 20px; 
            font-size: 0.65rem; 
            font-weight: 800; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            display: inline-block; 
            margin-bottom: 10px; 
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        .table-slip th, .table-slip td { padding: 8px 12px; border: 1px solid var(--border-color); font-size: 0.85rem; background: transparent; color: var(--text-primary); }
        .table-slip th { background: var(--hover-bg); color: var(--text-secondary); font-weight: 800; }
        .weekend td { background-color: rgba(239, 68, 68, 0.05) !important; color: #ef4444 !important; }
        .table-info-custom { background: transparent !important; }
        .table-info-custom td { color: var(--text-primary) !important; background: transparent !important; }
        
        @media print {
            .no-print, .sidebar { display: none !important; }
            .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; display: block !important; }
            .card-pro:not(.no-print) { 
                box-shadow: none !important; 
                border: none !important; 
                overflow: visible !important; 
                display: block !important; 
                padding: 0 !important; 
                width: 100% !important;
                margin: 0 !important;
            }
            .table-slip th, .table-slip td { border: 1px solid #000 !important; padding: 5px 8px !important; font-size: 9.5pt !important; line-height: 1.2 !important; }
            .internal-label { border: 1px solid #000 !important; color: #000; background: transparent !important; margin-bottom: 5px !important; padding: 4px 12px !important; font-size: 0.7rem !important; }
            .slip-header { margin-bottom: 12px !important; padding-bottom: 8px !important; }
            .slip-header h5 { font-size: 13pt !important; margin-bottom: 4px !important; }
            .row.mb-4 { margin-bottom: 15px !important; }
            .table-info-custom td { padding-top: 4px !important; padding-bottom: 4px !important; font-size: 10pt !important; }
            body { background: white !important; font-size: 10pt !important; }
            @page { margin: 1cm; }
        }
    </style>
</head>
<body>

<div class="wrapper">
    <?php include '../includes/sidebar.php'; ?>

    <main class="main-content">
        <header class="content-header no-print">
            <div class="h-title">Rekap Data Fiktif</div>
            <button onclick="window.print()" class="btn-pro btn-pro-outline py-1 px-3" <?php echo !$selected_user_data ? 'disabled' : ''; ?>>
                <i class="bi bi-printer"></i> Cetak Laporan
            </button>
        </header>

        <div class="content-padding">

            <div class="card-pro no-print mb-4">
                <form action="" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Pilih Guru</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">-- Pilih Anggota --</option>
                            <?php foreach ($gurus_data as $u): ?>
                                <option value="<?php echo $u['id']; ?>" <?php echo $sel_user == $u['id'] ? 'selected' : ''; ?>>
                                    <?php echo $u['nama']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Bulan</label>
                        <select name="bulan" class="form-select">
                            <?php foreach ($nama_bulan_arr as $k => $v): ?>
                                <option value="<?php echo $k; ?>" <?php echo $sel_bulan == $k ? 'selected' : ''; ?>><?php echo $v; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Tahun</label>
                        <select name="tahun" class="form-select">
                            <?php for($i=date('Y'); $i>=2024; $i--): ?>
                                <option value="<?php echo $i; ?>" <?php echo $sel_tahun == $i ? 'selected' : ''; ?>><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn-pro btn-pro-primary w-100">Tampilkan Data</button>
                    </div>
                </form>
            </div>

            <?php if ($selected_user_data): ?>
            <div class="card-pro p-5">
                <div class="text-center">
                    <div class="internal-label">LAPORAN INTERNAL ADMINISTRATIF</div>
                </div>
                <div class="slip-header text-center">
                    <h5 class="fw-bold mb-0 text-dark">RINCIAN KEHADIRAN GURU</h5>
                    <p class="mb-0 text-muted small">Periode: <?php echo $nama_bulan_arr[$sel_bulan] . " " . $sel_tahun; ?></p>
                </div>
                
                <div class="row mb-4">
                    <div class="col-8">
                        <table class="table table-sm table-borderless w-auto small mb-0 table-info-custom">
                            <tr><td class="text-secondary fw-bold" style="width: 140px;">Nama Lengkap</td><td>:</td><td class="fw-bold text-dark text-uppercase"><?php echo $selected_user_data['nama']; ?></td></tr>
                        </table>
                    </div>
                    <div class="col-4 text-end">
                        <table class="table table-sm table-borderless ms-auto w-auto small mb-0 table-info-custom">
                            <tr><td class="text-secondary fw-bold">QR Token</td><td>:</td><td class="font-monospace fw-bold"><?php echo $selected_user_data['qr_code']; ?></td></tr>
                        </table>
                    </div>
                </div>

                <table class="table table-slip w-100 mb-4">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">TGL</th>
                            <th class="text-center">HARI</th>
                            <th class="text-center">MASUK</th>
                            <th class="text-center">PULANG</th>
                            <th class="text-center">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_hadir = 0;
                        for($d=1; $d<=$last_day; $d++): 
                            $date_s = "$sel_tahun-$sel_bulan-" . str_pad($d, 2, '0', STR_PAD_LEFT);
                            $h_num = date('N', strtotime($date_s));
                            $is_sun = ($h_num == 7);
                            $h_name = ['1'=>'Senin','2'=>'Selasa','3'=>'Rabu','4'=>'Kamis','5'=>'Jumat','6'=>'Sabtu','7'=>'Minggu'][$h_num];
                            
                            $data = isset($absensi_hari[$d]) ? $absensi_hari[$d] : null;
                            $jam_in = ($data && $data['jam_masuk']) ? date('H:i', strtotime($data['jam_masuk'])) : '--:--';
                            $jam_out = ($data && $data['jam_keluar']) ? date('H:i', strtotime($data['jam_keluar'])) : '--:--';
                            $status = $data ? $data['status'] : '-';
                            if($status == 'Hadir') $total_hadir++;
                        ?>
                        <tr class="<?php echo $is_sun ? 'weekend' : ''; ?>">
                            <td class="text-center fw-bold"><?php echo $d; ?></td>
                            <td class="text-center"><?php echo $h_name; ?></td>
                            <td class="text-center font-monospace <?php echo $data && $status=='Hadir' ? 'text-primary fw-bold' : 'text-muted'; ?>"><?php echo $jam_in; ?></td>
                            <td class="text-center font-monospace <?php echo $data && $status=='Hadir' ? 'text-danger fw-bold' : 'text-muted'; ?>"><?php echo $jam_out; ?></td>
                            <td class="text-center fw-bold <?php echo $status=='Hadir' ? 'text-success' : 'text-secondary opacity-50'; ?>">
                                <?php if($is_sun): ?> Libur <?php else: echo $status; endif; ?>
                            </td>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
                
            </div>
            <?php else: ?>
            <div class="card-pro text-center py-5 opacity-50 no-print">
                <i class="bi bi-file-earmark-spreadsheet display-1 text-secondary mb-3"></i>
                <h5 class="fw-bold">Pilih data untuk ditampilkan</h5>
            </div>
            <?php endif; ?>
        </div>

        <footer class="footer-pro">
            Manual Reports &copy; <?php echo date('Y'); ?>
        </footer>
    </main>
</div>

<script src="/public/absen/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
