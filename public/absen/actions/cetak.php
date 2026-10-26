<?php
include '../includes/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

$where = "1=1";
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    $where = "id = '$id'";
} elseif (isset($_GET['tipe'])) {
    $tipe = mysqli_real_escape_string($koneksi, $_GET['tipe']);
    $where = "tipe = '$tipe'";
    if (isset($_GET['kelas']) && $_GET['kelas'] != '') {
        $kelas = mysqli_real_escape_string($koneksi, $_GET['kelas']);
        $where .= " AND kelas_mapel = '$kelas'";
    }
}

$query = "SELECT * FROM users WHERE $where ORDER BY tipe DESC, nama ASC";
$result = mysqli_query($koneksi, $query);

// Ambil Data Institusi dari aula_db untuk QR Siakad
$inst_res = mysqli_query($koneksi, "SELECT * FROM aula_db.institusi LIMIT 1");
$inst = mysqli_fetch_assoc($inst_res);
$namaKepala = $inst ? $inst['nama_kepala'] : 'Kepala Sekolah';
$thn = date('Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Smart ID Card Presensi</title>
    <link href="/public/absen/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/absen/assets/css/bootstrap-icons.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        body { 
            background: #f0f9ff; 
            background-image: radial-gradient(#e0f2fe 1.5px, transparent 1.5px);
            background-size: 24px 24px;
            padding: 20px; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
        }
        
        .print-area { 
            display: flex; 
            flex-wrap: wrap; 
            gap: 20px; 
            justify-content: center; 
        }
        
        .card-qr {
            width: 54mm; /* Layout KTP/Lanyard standard size width */
            min-height: 86mm; /* Layout KTP/Lanyard standard size height */
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
            position: relative;
            page-break-inside: avoid;
            overflow: hidden; 
            display: flex;
            flex-direction: column;
        }

        .header-card {
            background: linear-gradient(135deg, #0f172a 0%, #0369a1 100%);
            color: white;
            padding: 16px 10px;
            text-align: center;
        }
        
        .header-card h6 { margin: 0; font-weight: 800; font-size: 0.85rem; letter-spacing: 1px; color: #fff;}
        .header-card p { margin: 0; font-size: 0.60rem; opacity: 0.8; font-weight: 600; letter-spacing: 2px;}

        .qr-box { 
            margin: auto; 
            margin-top: 20px;
            padding: 12px;
            background: white;
            border: 2px dashed #94a3b8;
            border-radius: 16px;
            display: inline-block; 
            align-self: center; 
        }

        .qr-box img, .qr-box canvas { 
            margin: 0 auto; 
            display: block; 
            border-radius: 4px;
        }

        .card-body-print {
            padding: 15px;
            text-align: center;
        }

        .name { 
            font-weight: 800; 
            font-size: 0.95rem; 
            color: #0f172a; 
            line-height: 1.2; 
            text-transform: uppercase; 
        }
        
        .info { 
            font-size: 0.70rem; 
            color: #0284c7; 
            font-weight: 800; 
            margin-top: 6px; 
            margin-bottom: 0px; 
        }

        .qr-id { 
            background: #f8fafc; 
            padding: 10px; 
            font-family: monospace; 
            font-size: 0.75rem; 
            font-weight: 800; 
            color: #475569; 
            border-top: 2px dashed #e2e8f0; 
            margin-top: auto; 
            text-align: center;
        }

        @media print {
            body { 
                background: #fff; 
                padding: 0; 
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important; 
            }
            .no-print { display: none !important; }
            .print-area { gap: 10mm; padding: 5mm; justify-content: flex-start; }
            .card-qr { 
                box-shadow: none; 
                border: 2px solid #0f172a;
                margin-bottom: 5mm;
            }
            .header-card {
                 background: #0f172a !important; /* Force blackish color for better BW prints */
            }
        }
    </style>
</head>
<body>

<div class="container no-print my-4">
    <div class="card border-0 shadow-lg" style="border-radius: 16px; background: #0f172a; color: white;">
        <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div>
                <h5 class="mb-1 fw-bold text-info" style="letter-spacing: -0.5px;"><i class="bi bi-printer me-2"></i>Pusat Cetak Smart-Card</h5>
                <p class="mb-0 small" style="opacity: 0.85;">Tekan cetak untuk mendistribusikan kartu ID fisik Anggota dengan resolusi barcode terkalibrasi.</p>
            </div>
            <div class="text-nowrap">
                <button onclick="window.print()" class="btn btn-info fw-bold px-4 shadow-sm" style="border-radius: 10px;">
                     Cetak Sekarang
                </button>
                <button onclick="window.close()" class="btn btn-outline-light ms-2 fw-bold" style="border-radius: 10px;">Tutup Laman</button>
            </div>
        </div>
    </div>
</div>

<div class="print-area">
    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <div class="card-qr">
        <div class="header-card">
            <h6>ID CARD PRESENSI</h6>
            <p>SMART TRACKING SYSTEM</p>
        </div>
        
        <div class="qr-box" id="qr-<?php echo $row['id']; ?>"></div>
        
        <div class="card-body-print">
            <div class="name"><?php echo $row['nama']; ?></div>
            <p class="info"><?php echo ($row['tipe'] == 'siswa' ? 'PELAJAR' : 'GURU STAFF'); ?> • <?php echo $row['kelas_mapel']; ?></p>
        </div>
        
        <div class="qr-id">UID/<?php echo $row['qr_code']; ?></div>
        
        <?php 
        if ($row['tipe'] == 'guru') {
            $qr_content = "IDENTITAS GURU / TENDIK\nNama: " . $row['nama'] . "\nNIP: " . $row['qr_code'] . "\nTmpt Lahir: " . ($row['tempat_lahir'] ?: '-') . "\nTgl Lahir: " . ($row['tanggal_lahir'] ?: '-') . "\nJabatan: " . ($row['kelas_mapel'] ?: '-') . "\nAlamat: " . ($row['alamat'] ?: '-');
        } else {
            $qr_content = "NAMA: " . $row['nama'] . "\nNIS/NISN: " . ($row['nisn'] ?: '-') . "/" . $row['qr_code'] . "\nTTL: " . ($row['tempat_lahir'] ?: '-') . ", " . ($row['tanggal_lahir'] ?: '-') . "\nKELAS: " . ($row['kelas_mapel'] ?: '-') . "\nDISAHKAN OLEH: " . $namaKepala . " TAHUN " . $thn;
        }
        $qr_content_json = json_encode($qr_content);
        ?>
        <script>
            new QRCode(document.getElementById("qr-<?php echo $row['id']; ?>"), {
                text: <?php echo $qr_content_json; ?>,
                width: 130,
                height: 130,
                colorDark: "#020617",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.M
            });
        </script>
    </div>
    <?php endwhile; ?>
</div>

</body>
</html>
