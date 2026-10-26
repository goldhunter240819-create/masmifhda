<?php
require_once 'koneksi.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$token = isset($_GET['token']) ? $_GET['token'] : '';

$salt = "M1ft4hulHud4_S3cur3";
$expected_token = substr(md5($id . $salt), 0, 10);

$isValid = false;
$paymentData = null;

if ($id > 0 && $token === $expected_token) {
    // Cari data pembayaran
    try {
        $stmt = $core_db->prepare("
            SELECT p.*, s.nama, s.nis, c.nama_kelas as kelas
            FROM keuangan_komite_pembayaran p
            JOIN siswa s ON p.siswa_id = s.id
            LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
            LEFT JOIN kelas c ON rks.kelas_id = c.id
            WHERE p.id = :id
        ");
        $stmt->execute(['id' => $id]);
        $paymentData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($paymentData) {
            $isValid = true;
        }
    } catch (PDOException $e) {
        // Database error
    }
}

function formatTanggalIndonesia($tanggal) {
    if (empty($tanggal) || $tanggal === '0000-00-00') return '-';
    $bulanIndo = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $timestamp = strtotime($tanggal);
    return date('d', $timestamp) . ' ' . $bulanIndo[(int)date('m', $timestamp)] . ' ' . date('Y', $timestamp);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Kwitansi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .verification-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            width: 100%;
            max-width: 450px;
            overflow: hidden;
            text-align: center;
            border: 1px solid #f1f5f9;
        }
        .header {
            padding: 30px 20px 20px;
        }
        .icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 35px;
        }
        .valid-icon {
            background: #dcfce7;
            color: #16a34a;
            box-shadow: 0 0 0 10px #f0fdf4;
        }
        .invalid-icon {
            background: #fee2e2;
            color: #ef4444;
            box-shadow: 0 0 0 10px #fef2f2;
        }
        .status-title {
            font-size: 1.5rem;
            font-weight: 800;
            margin: 0 0 5px 0;
        }
        .valid-title {
            color: #16a34a;
        }
        .invalid-title {
            color: #ef4444;
        }
        .status-desc {
            color: #64748b;
            font-size: 0.95rem;
            margin: 0;
        }
        .details-container {
            background: #f8fafc;
            border-top: 1px dashed #cbd5e1;
            border-bottom: 1px dashed #cbd5e1;
            padding: 25px;
            text-align: left;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            align-items: flex-start;
        }
        .detail-row:last-child {
            margin-bottom: 0;
        }
        .detail-label {
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 500;
            width: 40%;
        }
        .detail-value {
            color: #0f172a;
            font-size: 0.9rem;
            font-weight: 700;
            width: 60%;
            text-align: right;
            word-break: break-word;
        }
        .amount-box {
            background: #10b981;
            color: white;
            padding: 15px;
            margin: 20px;
            border-radius: 12px;
            font-size: 1.5rem;
            font-weight: 800;
        }
        .footer {
            padding: 20px;
            font-size: 0.8rem;
            color: #94a3b8;
        }
    </style>
</head>
<body>

<div class="verification-card">
    <?php if ($isValid && $paymentData): ?>
        <div class="header">
            <div class="icon-wrapper valid-icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h1 class="status-title valid-title">Kwitansi Valid!</h1>
            <p class="status-desc">Dokumen ini resmi dikeluarkan oleh sistem.</p>
        </div>

        <div class="amount-box">
            Rp <?php echo number_format($paymentData['jumlah'], 0, ',', '.'); ?>
        </div>

        <div class="details-container">
            <div class="detail-row">
                <span class="detail-label">No. Kwitansi</span>
                <span class="detail-value">KW-<?php echo str_pad($paymentData['id'], 5, '0', STR_PAD_LEFT); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Nama Siswa</span>
                <span class="detail-value"><?php echo htmlspecialchars($paymentData['nama']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">NIS / Kelas</span>
                <span class="detail-value"><?php echo htmlspecialchars($paymentData['nis']); ?> / <?php echo htmlspecialchars($paymentData['kelas']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Pembayaran</span>
                <span class="detail-value"><?php echo htmlspecialchars($paymentData['jenis_pembayaran']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Tanggal Bayar</span>
                <span class="detail-value"><?php echo formatTanggalIndonesia($paymentData['tanggal_bayar']); ?></span>
            </div>
        </div>
    <?php else: ?>
        <div class="header">
            <div class="icon-wrapper invalid-icon">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h1 class="status-title invalid-title">Kwitansi Tidak Valid!</h1>
            <p class="status-desc" style="margin-top: 10px;">Data pembayaran tidak ditemukan atau URL rusak. Hati-hati terhadap pemalsuan dokumen!</p>
        </div>
    <?php endif; ?>

    <div class="footer">
        Sistem Keuangan - MAS Mifhda
    </div>
</div>

</body>
</html>
