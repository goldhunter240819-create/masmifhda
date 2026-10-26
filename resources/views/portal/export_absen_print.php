<?php
error_reporting(0); // Suppress notices
include __DIR__ . '/../../../public/absen/includes/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    die("Akses ditolak.");
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role_id'];
$identitas = $_GET['identitas'];
$m = $_GET['m'];
$y = $_GET['y'];

$conn_absen = mysqli_connect("127.0.0.1", "root", "", "absen_db");
$tipe_absen = ($role == 2) ? 'guru' : 'siswa';

$q_user = mysqli_query($conn_absen, "SELECT * FROM users WHERE qr_code = '$identitas' AND tipe = '$tipe_absen'");
$u_absen = mysqli_fetch_assoc($q_user);

if (!$u_absen) die("Data user tidak ditemukan di sistem absensi.");

$absen_user_id = $u_absen['id'];
$q_recap = mysqli_query($conn_absen, "SELECT * FROM absensi WHERE user_id = '$absen_user_id' AND MONTH(tanggal) = '$m' AND YEAR(tanggal) = '$y' ORDER BY tanggal ASC");

$months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$month_name = $months[intval($m) - 1];

// Get School Info
$db_core = \App\Core\Database::connect('core');
$institusi = $db_core->query("SELECT * FROM institusi LIMIT 1")->fetch();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi - <?php echo $u_absen['nama']; ?></title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <style>
        body { font-family: 'Arial', sans-serif; padding: 40px; color: #333; line-height: 1.4; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; position: relative; }
        .header img { position: absolute; left: 0; top: 0; height: 80px; }
        .header h1 { margin: 0; font-size: 22px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 14px; }
        .info-table { width: 100%; margin-bottom: 30px; font-size: 14px; }
        .info-table td { padding: 5px 0; }
        .main-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .main-table th, .main-table td { border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 13px; }
        .main-table th { background-color: #f5f5f5; font-weight: bold; text-transform: uppercase; font-size: 11px; }
        .footer { margin-top: 50px; display: flex; justify-content: flex-end; }
        .signature { text-align: center; width: 200px; }
        .signature p { margin-bottom: 60px; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 25px; background: #ef4444; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">Cetak Laporan / Simpan PDF</button>
    </div>

    <div class="header">
        <?php if($institusi['logo']): ?>
            <img src="<?php echo base_url('uploads/logo/<?php echo $institusi['logo']; ?>'); ?>" alt="Logo">
        <?php endif; ?>
        <h1><?php echo strtoupper($institusi['nama']); ?></h1>
        <p><?php echo $institusi['alamat']; ?> | Telp: <?php echo $institusi['telepon'] ?: '-'; ?></p>
        <h2 style="margin-top: 20px; font-size: 18px; color: #555;">LAPORAN KEHADIRAN BULANAN</h2>
    </div>

    <table class="info-table">
        <tr>
            <td style="width: 130px;">Nama Lengkap</td>
            <td style="width: 20px;">:</td>
            <td style="font-weight: bold; font-size: 16px;"><?php echo $u_absen['nama']; ?></td>
            <td style="width: 130px;">Bulan</td>
            <td style="width: 20px;">:</td>
            <td style="font-weight: bold;"><?php echo $month_name . ' ' . $y; ?></td>
        </tr>
        <tr>
            <td>ID / NIP / NIS</td>
            <td>:</td>
            <td><?php echo $identitas; ?></td>
            <td>Status</td>
            <td>:</td>
            <td><?php echo strtoupper($tipe_absen); ?></td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 40px; text-align: center;">No</th>
                <th style="width: 150px;">Hari & Tanggal</th>
                <th style="text-align: center;">Jam Masuk</th>
                <th style="text-align: center;">Jam Pulang</th>
                <th style="text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while($row = mysqli_fetch_assoc($q_recap)): ?>
            <tr>
                <td style="text-align: center;"><?php echo $no++; ?></td>
                <td><?php echo date('D, d M Y', strtotime($row['tanggal'])); ?></td>
                <td style="text-align: center;"><?php echo $row['jam_masuk'] ?: '-'; ?></td>
                <td style="text-align: center;"><?php echo $row['jam_keluar'] ?: '-'; ?></td>
                <td style="text-align: center; font-weight: bold; color: <?php echo ($row['status'] == 'Hadir') ? '#15803d' : '#92400e'; ?>;">
                    <?php echo $row['status']; ?>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php if(mysqli_num_rows($q_recap) == 0): ?>
            <tr>
                <td colspan="5" style="text-align: center; padding: 30px; color: #999;">Tidak ada data absensi untuk periode ini.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <p>Dicetak pada: <?php echo date('d/m/Y H:i'); ?><br>Mengetahui,</p>
            <br><br>
            <div style="border-bottom: 1px solid #333; font-weight: bold;">Sistem Aula System</div>
            <span>Administrator</span>
        </div>
    </div>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>

