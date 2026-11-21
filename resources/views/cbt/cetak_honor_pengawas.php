<?php
$topikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0;
$topik = $topik ?? [];
$rekapList = $rekapList ?? [];
$kop = $kop ?? [];
$inst = $inst ?? ['nama_kepala' => 'Drs. Hamim Hasyim', 'nip_kepala' => '-', 'kota' => 'Lampung Barat'];

// Logo resmi
$kopLogoUrl = '/assets/images/logo.png';
if (!empty($kop['logo'])) {
    $kopLogoUrl = function_exists('base_url') ? base_url('assets/images/' . $kop['logo']) : ('/assets/images/' . $kop['logo']);
} elseif (class_exists('\App\Core\Branding')) {
    try { 
        $kopLogoUrl = \App\Core\Branding::getInstitusiLogo(); 
    } catch (\Throwable $e) {}
}

$namaYayasan = $kop['nama_yayasan'] ?? 'YAYASAN PONDOK PESANTREN MIFTAHUL HUDA';
$namaMadrasah = $kop['nama_madrasah'] ?? 'MADRASAH ALIYAH MIFTAHUL HUDA';
$alamatJalan = $kop['alamat_jalan'] ?? 'Jl. Tirtomoyo, No. 083, Margorahayu, Kec. Sekincau';
$telepon = $kop['telepon'] ?? '';
$email = $kop['email'] ?? '';
$website = $kop['website'] ?? '';

$topikNama = !empty($topik['nama_topik']) ? trim($topik['nama_topik']) : 'ASESMEN CBT MADRASAH';
$topikTA = !empty($topik['tahun_ajaran']) ? trim($topik['tahun_ajaran']) : '2026/2027';

$totalSesi = 0;
foreach ($rekapList as $rk) {
    $totalSesi += (int)$rk['total_sesi'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Presensi Pengawas Ruang - <?= htmlspecialchars($topikNama) ?></title>
    <link rel="icon" type="image/png" href="<?= $kopLogoUrl ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            background: #0f172a; 
            font-family: 'Plus Jakarta Sans', Arial, sans-serif; 
            color: #0f172a; 
            padding: 20px; 
            min-height: 100vh;
        }

        .no-print-bar { 
            max-width: 900px; 
            margin: 0 auto 20px; 
            background: #ffffff; 
            padding: 12px 20px; 
            border-radius: 14px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.15); 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            gap: 15px; 
            flex-wrap: wrap;
            border: 1px solid #cbd5e1; 
        }
        .btn-ctrl { 
            padding: 9px 16px; 
            border-radius: 10px; 
            font-size: 0.85rem; 
            font-weight: 800; 
            text-decoration: none; 
            cursor: pointer; 
            border: none; 
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
            transition: all 0.2s;
        }
        .btn-print { 
            background: linear-gradient(135deg, #059669 0%, #047857 100%); 
            color: white; 
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
        }
        .btn-back { 
            background: #f1f5f9; 
            color: #475569; 
            border: 1px solid #cbd5e1; 
        }

        .paper-sheet { 
            background: #ffffff; 
            width: 100%; 
            max-width: 900px; 
            margin: 0 auto; 
            padding: 30px 40px; 
            border-radius: 6px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.25); 
            color: #000000;
        }

        /* KOP SURAT */
        .kop-wrapper {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 16px;
            gap: 20px;
        }
        .kop-logo {
            width: 75px;
            height: 75px;
            object-fit: contain;
        }
        .kop-text {
            flex: 1;
            text-align: center;
        }
        .kop-yayasan {
            font-size: 13pt;
            font-weight: 800;
            letter-spacing: 0.5px;
        }
        .kop-madrasah {
            font-size: 16pt;
            font-weight: 900;
            color: #000;
            margin: 2px 0;
            letter-spacing: 1px;
        }
        .kop-alamat {
            font-size: 9pt;
            color: #222;
            line-height: 1.3;
        }
        .kop-kontak {
            font-size: 8pt;
            color: #333;
            margin-top: 2px;
        }

        .doc-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .doc-title h2 {
            font-size: 13pt;
            font-weight: 900;
            text-decoration: underline;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .doc-title p {
            font-size: 9.5pt;
            font-weight: 700;
            margin-top: 3px;
        }

        /* Tabel Presensi */
        .honor-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 25px;
        }
        .honor-table th {
            border: 1.5px solid #000;
            padding: 8px 6px;
            background: #f1f5f9;
            text-align: center;
            font-weight: 800;
            text-transform: uppercase;
        }
        .honor-table td {
            border: 1px solid #000;
            padding: 7px 8px;
            vertical-align: middle;
        }

        /* Signature Block */
        .sig-container {
            display: flex;
            justify-content: space-between;
            font-size: 9.5pt;
            page-break-inside: avoid;
        }
        .sig-box {
            width: 250px;
            text-align: center;
        }
        .sig-space {
            height: 70px;
        }
        .sig-name {
            font-weight: 800;
            text-decoration: underline;
        }

        @media print {
            body { 
                background: #ffffff !important; 
                padding: 0 !important; 
                margin: 0 !important; 
            }
            .no-print-bar { 
                display: none !important; 
            }
            .paper-sheet { 
                box-shadow: none !important; 
                border: none !important; 
                padding: 0 !important; 
                width: 100% !important; 
                max-width: 100% !important; 
            }
            .honor-table th {
                background: #f1f5f9 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            @page { 
                size: A4 portrait; 
                margin: 12mm 15mm; 
            }
        }
    </style>
</head>
<body>

<div class="no-print-bar">
    <div style="display: flex; align-items: center; gap: 12px;">
        <button type="button" onclick="window.close(); if(history.length > 1) history.back();" class="btn-ctrl btn-back">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </button>
        <span style="font-weight: 800; font-size: 0.9rem; color: #0f172a;">
            Daftar Rekapitulasi Presensi Pengawas Ruang (A4)
        </span>
    </div>

    <button type="button" onclick="window.print()" class="btn-ctrl btn-print">
        <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
    </button>
</div>

<div class="paper-sheet">
    <!-- KOP RESMI -->
    <div class="kop-wrapper">
        <img src="<?= $kopLogoUrl ?>" class="kop-logo" alt="Logo">
        <div class="kop-text">
            <div class="kop-yayasan"><?= htmlspecialchars($namaYayasan) ?></div>
            <div class="kop-madrasah"><?= htmlspecialchars($namaMadrasah) ?></div>
            <div class="kop-alamat"><?= nl2br(htmlspecialchars($alamatJalan)) ?></div>
            <div class="kop-kontak">
                <?php if (!empty($telepon)): ?>Telp: <?= htmlspecialchars($telepon) ?> &bull; <?php endif; ?>
                <?php if (!empty($email)): ?>Email: <?= htmlspecialchars($email) ?> &bull; <?php endif; ?>
                Website: <?= htmlspecialchars($website ?: 'https://apk.masmifhda.sch.id') ?>
            </div>
        </div>
    </div>

    <!-- JUDUL -->
    <div class="doc-title">
        <h2>DAFTAR REKAPITULASI PRESENSI PENGAWAS RUANG UJIAN</h2>
        <p><?= htmlspecialchars($topikNama) ?> &bull; TAHUN PELAJARAN <?= htmlspecialchars($topikTA) ?></p>
    </div>

    <!-- TABEL PRESENSI -->
    <table class="honor-table">
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th>Nama Guru Pengawas</th>
                <th style="width: 130px;">NIP / ID</th>
                <th style="width: 130px;">Ruang Tugas</th>
                <th style="width: 120px;">Tanggal Tugas</th>
                <th style="width: 75px;">Jumlah Sesi</th>
                <th style="width: 100px;">Status</th>
                <th style="width: 110px;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rekapList)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 25px; font-style: italic;">
                        Belum ada data pengawas yang melakukan presensi pada periode/topik ini.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($rekapList as $k => $row): ?>
                    <tr>
                        <td style="text-align: center; font-weight: bold;"><?= $k + 1 ?></td>
                        <td>
                            <strong><?= htmlspecialchars($row['guru_nama']) ?></strong>
                        </td>
                        <td style="text-align: center; font-family: monospace; font-size: 8.5pt;">
                            <?= htmlspecialchars($row['guru_nip'] ?: '-') ?>
                        </td>
                        <td style="font-size: 8pt; text-align: center;">
                            <?= htmlspecialchars($row['daftar_ruang'] ?: '-') ?>
                        </td>
                        <td style="font-size: 8pt; text-align: center;">
                            <?= htmlspecialchars($row['daftar_tanggal'] ?? '-') ?>
                        </td>
                        <td style="text-align: center; font-weight: bold;">
                            <?= (int)$row['total_sesi'] ?> Sesi
                        </td>
                        <td style="text-align: center; font-size: 8pt; color: #047857; font-weight: bold;">
                            Hadir (Scan QR)
                        </td>
                        <td style="height: 38px; vertical-align: bottom; padding-bottom: 4px;">
                            <span style="font-size: 8pt; color: #555;"><?= $k + 1 ?>. </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <!-- Total Row -->
                <tr style="font-weight: bold; background: #f8fafc;">
                    <td colspan="5" style="text-align: right; padding: 8px;">TOTAL SESI TERAWASI:</td>
                    <td style="text-align: center; font-size: 9.5pt;"><?= $totalSesi ?> Sesi</td>
                    <td colspan="2" style="text-align: center; color: #047857;">Terdata Lengkap</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- TANDA TANGAN -->
    <div class="sig-container">
        <div class="sig-box">
            <div>Mengetahui,</div>
            <div style="font-weight: 700;">Ketua Panitia / Koordinator CBT</div>
            <div class="sig-space"></div>
            <div class="sig-name">( ........................................ )</div>
            <div>NIP. -</div>
        </div>

        <div class="sig-box">
            <div><?= htmlspecialchars($inst['kota'] ?? 'Lampung Barat') ?>, <?= date('d F Y') ?></div>
            <div style="font-weight: 700;">Kepala Madrasah</div>
            <div class="sig-space"></div>
            <div class="sig-name"><?= htmlspecialchars($inst['nama_kepala'] ?? 'Drs. Hamim Hasyim') ?></div>
            <div>NIP. <?= htmlspecialchars($inst['nip_kepala'] ?? '-') ?></div>
        </div>
    </div>
</div>

</body>
</html>
