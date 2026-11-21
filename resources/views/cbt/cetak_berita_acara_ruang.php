<?php
$topikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0;
$topik = $topik ?? [];
$allRuang = $allRuang ?? [];
$ruang = $ruang ?? [];
$ruangId = $ruangId ?? 0;
$tanggal = $tanggal ?? date('Y-m-d');
$sesiLabel = $sesiLabel ?? 'Sesi 1';
$pesertaList = $pesertaList ?? [];
$mapelNama = $mapelNama ?? 'Asesmen CBT';
$pengawasNama = $pengawasNama ?? 'Bapak / Ibu Pengawas';
$pengawasNip = $pengawasNip ?? '-';
$totalTerdaftar = $totalTerdaftar ?? count($pesertaList);
$totalHadir = $totalHadir ?? 0;
$totalTidakHadir = $totalTidakHadir ?? 0;
$kop = $kop ?? [];
$inst = $inst ?? ['nama_kepala' => 'Drs. Hamim Hasyim', 'nip_kepala' => '-', 'kota' => 'Lampung Barat'];

// Logo resmi kop surat
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

$topikNama = !empty($topik['nama_topik']) ? trim($topik['nama_topik']) : 'ASESMEN CBT';
$topikTA = !empty($topik['tahun_ajaran']) ? trim($topik['tahun_ajaran']) : '2026/2027';

$hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$curDayIndo = $hariIndo[date('w', strtotime($tanggal))];
$bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$tglFormatIndo = date('j', strtotime($tanggal)) . ' ' . $bulanIndo[(int)date('n', strtotime($tanggal))] . ' ' . date('Y', strtotime($tanggal));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara & Daftar Hadir - <?= htmlspecialchars($ruang['nama_ruang'] ?? 'Ruang') ?> (<?= htmlspecialchars($sesiLabel) ?>)</title>
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
            max-width: 920px; 
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
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); 
            color: white; 
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        .btn-back { 
            background: #f1f5f9; 
            color: #475569; 
            border: 1px solid #cbd5e1; 
        }
        .select-filter {
            background: #f8fafc;
            border: 1.5px solid #cbd5e1;
            padding: 7px 10px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.82rem;
            color: #0f172a;
            outline: none;
        }

        .paper-sheet { 
            background: #ffffff; 
            width: 100%; 
            max-width: 920px; 
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
            padding-bottom: 10px;
            margin-bottom: 14px;
            gap: 18px;
        }
        .kop-logo {
            width: 72px;
            height: 72px;
            object-fit: contain;
        }
        .kop-text {
            flex: 1;
            text-align: center;
        }
        .kop-yayasan {
            font-size: 12.5pt;
            font-weight: 800;
        }
        .kop-madrasah {
            font-size: 15pt;
            font-weight: 900;
            color: #000;
            margin: 2px 0;
            letter-spacing: 0.5px;
        }
        .kop-alamat {
            font-size: 8.5pt;
            color: #222;
            line-height: 1.3;
        }
        .kop-kontak {
            font-size: 8pt;
            color: #333;
        }

        .doc-title {
            text-align: center;
            margin-bottom: 14px;
        }
        .doc-title h2 {
            font-size: 12pt;
            font-weight: 900;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-title p {
            font-size: 9pt;
            font-weight: 700;
            margin-top: 2px;
        }

        /* Info Grid */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 12px;
        }
        .meta-table td {
            padding: 3px 4px;
            vertical-align: top;
        }

        /* Berita Acara Box */
        .ba-box {
            border: 1px solid #000;
            padding: 8px 12px;
            font-size: 8.5pt;
            line-height: 1.45;
            margin-bottom: 14px;
            background: #fafafa;
        }
        .ba-box-title {
            font-weight: 800;
            text-decoration: underline;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        /* Tabel Peserta */
        .peserta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 15px;
        }
        .peserta-table th {
            border: 1px solid #000;
            padding: 6px 4px;
            background: #f1f5f9;
            text-align: center;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 8pt;
        }
        .peserta-table td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: middle;
        }

        /* Tanda Tangan */
        .sig-container {
            display: flex;
            justify-content: space-between;
            font-size: 9pt;
            page-break-inside: avoid;
            margin-top: 15px;
        }
        .sig-box {
            width: 250px;
            text-align: center;
        }
        .sig-space {
            height: 60px;
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
            .peserta-table th {
                background: #f1f5f9 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            @page { 
                size: A4 portrait; 
                margin: 10mm 12mm; 
            }
        }
    </style>
</head>
<body>

<!-- Filter Bar (Screen Only) -->
<div class="no-print-bar">
    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
        <button type="button" onclick="history.back()" class="btn-ctrl btn-back">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </button>

        <form method="GET" action="" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
            <input type="hidden" name="topik_id" value="<?= $topikId ?>">
            
            <label style="font-size: 0.8rem; font-weight: 800; color: #475569;">Ruang:</label>
            <select name="ruang_id" onchange="this.form.submit()" class="select-filter">
                <?php foreach ($allRuang as $ar): ?>
                    <option value="<?= $ar['id'] ?>" <?= ((int)$ruangId === (int)$ar['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ar['nama_ruang']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label style="font-size: 0.8rem; font-weight: 800; color: #475569;">Sesi:</label>
            <select name="sesi" onchange="this.form.submit()" class="select-filter">
                <option value="Sesi 1" <?= ($sesiLabel === 'Sesi 1') ? 'selected' : '' ?>>Sesi 1</option>
                <option value="Sesi 2" <?= ($sesiLabel === 'Sesi 2') ? 'selected' : '' ?>>Sesi 2</option>
                <option value="Sesi 3" <?= ($sesiLabel === 'Sesi 3') ? 'selected' : '' ?>>Sesi 3</option>
            </select>

            <label style="font-size: 0.8rem; font-weight: 800; color: #475569;">Tanggal:</label>
            <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>" onchange="this.form.submit()" class="select-filter">
        </form>
    </div>

    <button type="button" onclick="window.print()" class="btn-ctrl btn-print">
        <i class="fa-solid fa-print"></i> Cetak Dokumen Panitia (A4)
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
        <h2>BERITA ACARA &amp; DAFTAR HADIR PESERTA RUANG UJIAN</h2>
        <p><?= htmlspecialchars($topikNama) ?> &bull; TAHUN PELAJARAN <?= htmlspecialchars($topikTA) ?></p>
    </div>

    <!-- METADATA RUANG & MAPEL -->
    <table class="meta-table">
        <tr>
            <td style="width: 18%; font-weight: 700;">Ruang Ujian</td>
            <td style="width: 2%;">:</td>
            <td style="width: 38%; font-weight: 800;"><?= htmlspecialchars($ruang['nama_ruang'] ?? 'Ruang Ujian') ?> (<?= htmlspecialchars($ruang['lokasi'] ?? 'MA Miftahul Huda') ?>)</td>
            <td style="width: 18%; font-weight: 700;">Hari / Tanggal</td>
            <td style="width: 2%;">:</td>
            <td style="width: 22%; font-weight: 800;"><?= htmlspecialchars($curDayIndo) ?>, <?= htmlspecialchars($tglFormatIndo) ?></td>
        </tr>
        <tr>
            <td style="font-weight: 700;">Mata Pelajaran</td>
            <td>:</td>
            <td style="font-weight: 800; color: #1e3a8a;"><?= htmlspecialchars($mapelNama) ?></td>
            <td style="font-weight: 700;">Sesi Ujian</td>
            <td>:</td>
            <td style="font-weight: 800;"><?= htmlspecialchars($sesiLabel) ?></td>
        </tr>
        <tr>
            <td style="font-weight: 700;">Pengawas Bertugas</td>
            <td>:</td>
            <td style="font-weight: 800;"><?= htmlspecialchars($pengawasNama) ?></td>
            <td style="font-weight: 700;">Statistik Peserta</td>
            <td>:</td>
            <td>
                <strong><?= $totalTerdaftar ?></strong> Terdaftar &bull; 
                <strong style="color: #059669;"><?= $totalHadir ?></strong> Hadir &bull; 
                <strong style="color: #dc2626;"><?= $totalTidakHadir ?></strong> Absen
            </td>
        </tr>
    </table>

    <!-- BERITA ACARA BOX -->
    <div class="ba-box">
        <div class="ba-box-title">I. BERITA ACARA PELAKSANAAN UJIAN</div>
        <div>
            Pada hari ini <strong><?= htmlspecialchars($curDayIndo) ?></strong> tanggal <strong><?= htmlspecialchars($tglFormatIndo) ?></strong>, 
            telah diselenggarakan Asesmen CBT untuk mata pelajaran <strong><?= htmlspecialchars($mapelNama) ?></strong> pada <strong><?= htmlspecialchars($sesiLabel) ?></strong> 
            yang bertempat di <strong><?= htmlspecialchars($ruang['nama_ruang'] ?? 'Ruang Ujian') ?></strong>. 
            Jumlah peserta yang terdaftar sebanyak <strong><?= $totalTerdaftar ?></strong> orang, hadir <strong><?= $totalHadir ?></strong> orang, 
            dan tidak hadir <strong><?= $totalTidakHadir ?></strong> orang.
        </div>
        <div style="margin-top: 4px;">
            <strong>Catatan / Kejadian Penting Selama Ujian:</strong> Pelaksanaan ujian berlangsung dengan tertib, lancar, dan aman sesuai dengan tata tertib yang berlaku.
        </div>
    </div>

    <!-- DAFTAR HADIR PESERTA -->
    <div style="font-weight: 800; font-size: 9pt; text-transform: uppercase; margin-bottom: 6px;">
        II. DAFTAR HADIR PESERTA UJIAN (RUANGAN <?= strtoupper(htmlspecialchars($ruang['nama_ruang'] ?? '')) ?>)
    </div>
    <table class="peserta-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 105px;">No. Peserta</th>
                <th style="width: 95px;">NISN / NIS</th>
                <th>Nama Lengkap Peserta</th>
                <th style="width: 70px;">Kelas</th>
                <th style="width: 60px;">No Meja</th>
                <th style="width: 70px;">Kehadiran</th>
                <th style="width: 130px;">Tanda Tangan Siswa</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pesertaList)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 25px; font-style: italic;">
                        Belum ada peserta yang dialokasikan di ruangan ini. Atur denah tempat duduk di menu Ruang & Tempat Duduk.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($pesertaList as $idx => $ps): ?>
                    <?php 
                    $isGenap = (($idx + 1) % 2 === 0);
                    ?>
                    <tr>
                        <td style="text-align: center; font-weight: bold;"><?= $idx + 1 ?></td>
                        <td style="text-align: center; font-family: monospace; font-size: 8pt; font-weight: bold;">
                            <?= htmlspecialchars($ps['no_peserta'] ?: '-') ?>
                        </td>
                        <td style="text-align: center; font-family: monospace; font-size: 8pt;">
                            <?= htmlspecialchars($ps['nisn'] ?: ($ps['nis'] ?: '-')) ?>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($ps['nama_siswa']) ?></strong>
                        </td>
                        <td style="text-align: center; font-size: 8pt;">
                            <?= htmlspecialchars($ps['kelas_nama'] ?: '-') ?>
                        </td>
                        <td style="text-align: center; font-weight: bold;">
                            <?= htmlspecialchars($ps['nomor_meja'] ?: '-') ?>
                        </td>
                        <td style="text-align: center; font-weight: 700; font-size: 8pt;">
                            <?= htmlspecialchars($ps['status_kehadiran'] ?? 'Hadir') ?>
                        </td>
                        <td style="height: 32px; vertical-align: bottom; padding-bottom: 2px;">
                            <?php if ($isGenap): ?>
                                <div style="text-align: right; font-size: 7.5pt; color: #333; padding-right: 15px;"><?= $idx + 1 ?>. ..........</div>
                            <?php else: ?>
                                <div style="text-align: left; font-size: 7.5pt; color: #333; padding-left: 5px;"><?= $idx + 1 ?>. ..........</div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- TANDA TANGAN (Pengawas Ruang & Kepala Madrasah) -->
    <div class="sig-container">
        <div class="sig-box">
            <div>Pengawas Ruang Ujian,</div>
            <div style="font-weight: 700;"><?= htmlspecialchars($ruang['nama_ruang'] ?? 'Ruang Ujian') ?></div>
            <div class="sig-space"></div>
            <div class="sig-name"><?= htmlspecialchars($pengawasNama) ?></div>
            <div>NIP. <?= htmlspecialchars($pengawasNip) ?></div>
        </div>

        <div class="sig-box">
            <div><?= htmlspecialchars($inst['kota'] ?? 'Lampung Barat') ?>, <?= htmlspecialchars($tglFormatIndo) ?></div>
            <div style="font-weight: 700;">Kepala Madrasah</div>
            <div class="sig-space"></div>
            <div class="sig-name"><?= htmlspecialchars($inst['nama_kepala'] ?? 'Drs. Hamim Hasyim') ?></div>
            <div>NIP. <?= htmlspecialchars($inst['nip_kepala'] ?? '-') ?></div>
        </div>
    </div>
</div>

</body>
</html>
