<?php
$topikId = isset($topikId) ? (int)$topikId : 0;
$topik = $topik ?? [];
$allRuang = $allRuang ?? [];
$ruang = $ruang ?? [];
$ruangId = $ruangId ?? 0;
$tanggal = $tanggal ?? date('Y-m-d');
$sesiLabel = $sesiLabel ?? 'Sesi 1';
$pesertaList = $pesertaList ?? [];
$tidakHadirList = $tidakHadirList ?? [];
$mapelNama = $mapelNama ?? 'Asesmen CBT';
$jamSesi = $jamSesi ?? '08:00 - 09:00 WIB';
$pengawasNama = $pengawasNama ?? 'Bapak / Ibu Pengawas';
$pengawasNip = $pengawasNip ?? '-';
$totalTerdaftar = $totalTerdaftar ?? count($pesertaList);
$totalHadir = $totalHadir ?? 0;
$totalTidakHadir = $totalTidakHadir ?? 0;
$kop = $kop ?? [];
$inst = $inst ?? ['nama_kepala' => 'Drs. Hamim Hasyim', 'nip_kepala' => '-', 'kota' => 'Lampung Barat'];

// Base URL helper
$bu = function($path = '') {
    return function_exists('base_url') ? base_url($path) : '/' . ltrim($path, '/');
};

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
    <title>Berita Acara Ujian - <?= htmlspecialchars($ruang['nama_ruang'] ?? 'Ruang') ?> (<?= htmlspecialchars($sesiLabel) ?>)</title>
    <link rel="icon" type="image/png" href="<?= $kopLogoUrl ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            background: #0f172a; 
            font-family: 'Plus Jakarta Sans', Arial, sans-serif; 
            color: #0f172a; 
            padding: 24px; 
            min-height: 100vh;
        }

        .no-print-bar { 
            max-width: 820px; 
            margin: 0 auto 20px; 
            background: #ffffff; 
            padding: 12px 20px; 
            border-radius: 14px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.2); 
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
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); 
            color: white; 
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.35);
        }
        .btn-print:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(2, 132, 199, 0.45);
        }
        .btn-back { 
            background: #f1f5f9; 
            color: #475569; 
            border: 1px solid #cbd5e1; 
        }
        .btn-switch {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .paper-sheet { 
            background: #ffffff; 
            width: 100%; 
            max-width: 820px; 
            margin: 0 auto; 
            padding: 35px 45px; 
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
            gap: 18px;
        }
        .kop-logo {
            width: 76px;
            height: 76px;
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
            font-size: 15.5pt;
            font-weight: 900;
            color: #000;
            margin: 2px 0;
            letter-spacing: 0.8px;
        }
        .kop-alamat {
            font-size: 8.5pt;
            color: #222;
            line-height: 1.35;
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
        }
        .doc-title p {
            font-size: 10pt;
            font-weight: 800;
            margin-top: 3px;
            text-transform: uppercase;
        }

        /* Tabel Parameter */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9.5pt;
        }
        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .info-table .lbl {
            width: 150px;
            font-weight: 700;
            color: #111;
        }
        .info-table .sep {
            width: 12px;
            text-align: center;
            font-weight: 700;
        }
        .info-table .val {
            font-weight: 800;
            color: #000;
        }

        /* Section Block */
        .section-box {
            margin-bottom: 18px;
            font-size: 9.5pt;
            line-height: 1.6;
        }
        .section-head {
            font-weight: 800;
            font-size: 9.5pt;
            text-transform: uppercase;
            margin-bottom: 6px;
            color: #000;
        }
        .section-content {
            text-align: justify;
        }

        /* Stat Table */
        .stat-badge-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 16px 0;
            font-size: 9.5pt;
        }
        .stat-badge-table th, .stat-badge-table td {
            border: 1px solid #000;
            padding: 7px 12px;
            text-align: center;
        }
        .stat-badge-table th {
            background: #f1f5f9;
            font-weight: 800;
        }
        .stat-badge-table td {
            font-weight: 700;
        }

        /* Tabel Absen */
        .table-absen-list {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 8.5pt;
        }
        .table-absen-list th, .table-absen-list td {
            border: 1px solid #000;
            padding: 5px 8px;
        }
        .table-absen-list th {
            background: #f8fafc;
            font-weight: 800;
            text-align: center;
        }

        /* Signature */
        .sig-container {
            display: flex;
            justify-content: space-between;
            margin-top: 35px;
            page-break-inside: avoid;
        }
        .sig-box {
            width: 250px;
            text-align: center;
            font-size: 9.5pt;
            line-height: 1.4;
        }
        .sig-space {
            height: 65px;
        }
        .sig-name {
            font-weight: 800;
            text-decoration: underline;
        }

        @media print {
            body { 
                background: #ffffff !important; 
                padding: 0 !important; 
            }
            .no-print-bar { 
                display: none !important; 
            }
            .paper-sheet { 
                box-shadow: none !important; 
                padding: 10mm 15mm !important; 
                max-width: 100% !important; 
            }
            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>

<!-- Floating Control Bar -->
<div class="no-print-bar">
    <div style="display: flex; align-items: center; gap: 10px;">
        <a href="<?= $bu('cbt/ruang/berita-acara?topik_id=' . $topikId . '&ruang_id=' . $ruangId . '&tanggal=' . $tanggal . '&sesi=' . urlencode($sesiLabel)) ?>" class="btn-ctrl btn-back">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
        </a>
        <a href="<?= $bu('cbt/ruang/berita-acara/cetak-hadir-ruang?topik_id=' . $topikId . '&ruang_id=' . $ruangId . '&tanggal=' . $tanggal . '&sesi=' . urlencode($sesiLabel)) ?>" class="btn-ctrl btn-switch">
            <i class="fa-solid fa-clipboard-user"></i> Lihat Daftar Hadir
        </a>
    </div>

    <div style="display: flex; align-items: center; gap: 10px;">
        <span style="font-size: 0.8rem; font-weight: 700; color: #64748b;">Format: A4 Portrait</span>
        <button onclick="window.print()" class="btn-ctrl btn-print">
            <i class="fa-solid fa-print"></i> Cetak Berita Acara (A4)
        </button>
    </div>
</div>

<!-- Lembar Berita Acara A4 -->
<div class="paper-sheet">
    
    <!-- KOP RESMI -->
    <div class="kop-wrapper">
        <img src="<?= $kopLogoUrl ?>" alt="Logo" class="kop-logo">
        <div class="kop-text">
            <div class="kop-yayasan"><?= htmlspecialchars($namaYayasan) ?></div>
            <div class="kop-madrasah"><?= htmlspecialchars($namaMadrasah) ?></div>
            <div class="kop-alamat">
                <?= htmlspecialchars($alamatJalan) ?><br>
                <?php if ($telepon): ?>Telp: <?= htmlspecialchars($telepon) ?> | <?php endif; ?>
                <?php if ($email): ?>Email: <?= htmlspecialchars($email) ?> | <?php endif; ?>
                <?php if ($website): ?>Website: <?= htmlspecialchars($website) ?><?php endif; ?>
            </div>
        </div>
    </div>

    <!-- JUDUL DOKUMEN -->
    <div class="doc-title">
        <h2>BERITA ACARA PELAKSANAAN UJIAN CBT</h2>
        <p><?= htmlspecialchars($topikNama) ?> • TAHUN PELAJARAN <?= htmlspecialchars($topikTA) ?></p>
    </div>

    <!-- PARAMETER PELAKSANAAN -->
    <table class="info-table">
        <tr>
            <td class="lbl">Ruang Ujian</td>
            <td class="sep">:</td>
            <td class="val"><?= htmlspecialchars($ruang['nama_ruang'] ?? 'Ruang 01') ?></td>
            <td class="lbl" style="padding-left: 20px;">Hari / Tanggal</td>
            <td class="sep">:</td>
            <td class="val"><?= htmlspecialchars($curDayIndo) ?>, <?= htmlspecialchars($tglFormatIndo) ?></td>
        </tr>
        <tr>
            <td class="lbl">Sesi Pelaksanaan</td>
            <td class="sep">:</td>
            <td class="val"><?= htmlspecialchars($sesiLabel) ?> (<?= htmlspecialchars($jamSesi) ?>)</td>
            <td class="lbl" style="padding-left: 20px;">Mata Pelajaran</td>
            <td class="sep">:</td>
            <td class="val"><?= htmlspecialchars($mapelNama) ?></td>
        </tr>
        <tr>
            <td class="lbl">Pengawas Bertugas</td>
            <td class="sep">:</td>
            <td class="val"><?= htmlspecialchars($pengawasNama) ?></td>
            <td class="lbl" style="padding-left: 20px;">NIP Pengawas</td>
            <td class="sep">:</td>
            <td class="val"><?= htmlspecialchars($pengawasNip) ?></td>
        </tr>
    </table>

    <!-- I. KETERANGAN PELAKSANAAN -->
    <div class="section-box">
        <div class="section-head">I. KETERANGAN PELAKSANAAN ASESMEN CBT</div>
        <div class="section-content">
            Pada hari ini <strong><?= $curDayIndo ?></strong> tanggal <strong><?= htmlspecialchars($tglFormatIndo) ?></strong>, telah diselenggarakan Asesmen CBT untuk mata pelajaran <strong><?= htmlspecialchars($mapelNama) ?></strong> pada <strong><?= htmlspecialchars($sesiLabel) ?></strong> yang bertempat di <strong><?= htmlspecialchars($ruang['nama_ruang'] ?? 'Ruang Ujian') ?></strong>. Pelaksanaan ujian menggunakan sistem Computer Based Test (CBT) dengan autentikasi presensi kartu siswa terverifikasi.
        </div>
    </div>

    <!-- II. STATISTIK KEHADIRAN -->
    <div class="section-box">
        <div class="section-head">II. STATISTIK KEHADIRAN PESERTA RUANG UJIAN</div>
        <table class="stat-badge-table">
            <thead>
                <tr>
                    <th style="width: 33.3%;">Peserta Terdaftar</th>
                    <th style="width: 33.3%;">Peserta Hadir (Terscan)</th>
                    <th style="width: 33.3%;">Peserta Tidak Hadir</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-size: 12pt; font-weight: 800;"><?= $totalTerdaftar ?> Orang</td>
                    <td style="font-size: 12pt; font-weight: 800; color: #166534;"><?= $totalHadir ?> Orang</td>
                    <td style="font-size: 12pt; font-weight: 800; color: #991b1b;"><?= $totalTidakHadir ?> Orang</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- III. DAFTAR PESERTA TIDAK HADIR -->
    <div class="section-box">
        <div class="section-head">III. DAFTAR PESERTA YANG TIDAK HADIR</div>
        <?php if (empty($tidakHadirList)): ?>
            <div style="font-style: italic; color: #333; padding: 4px 0;">
                - Nihil (Seluruh peserta terdaftar hadir lengkap mengikuti ujian CBT) -
            </div>
        <?php else: ?>
            <table class="table-absen-list">
                <thead>
                    <tr>
                        <th style="width: 35px;">No</th>
                        <th style="width: 100px;">No. Meja</th>
                        <th style="width: 110px;">No. Peserta / NIS</th>
                        <th>Nama Lengkap Siswa</th>
                        <th style="width: 80px;">Kelas</th>
                        <th style="width: 140px;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tidakHadirList as $n => $th): ?>
                        <tr>
                            <td style="text-align: center;"><?= $n + 1 ?></td>
                            <td style="text-align: center; font-weight: bold;"><?= htmlspecialchars($th['nomor_meja'] ?: '-') ?></td>
                            <td style="text-align: center; font-family: monospace;"><?= htmlspecialchars($th['no_peserta'] ?? ($th['nis'] ?? '-')) ?></td>
                            <td><strong><?= htmlspecialchars($th['nama_siswa']) ?></strong></td>
                            <td style="text-align: center;"><?= htmlspecialchars($th['kelas_nama'] ?: '-') ?></td>
                            <td style="text-align: center; font-style: italic; color: #666;">Tanpa Keterangan / Sakit</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- IV. CATATAN / KEJADIAN PENTING -->
    <div class="section-box">
        <div class="section-head">IV. CATATAN / KEJADIAN PENTING SELAMA ASESMEN</div>
        <div class="section-content" style="border: 1px dashed #999; padding: 10px 14px; border-radius: 4px; background: #fafafa; min-height: 48px;">
            Pelaksanaan Asesmen CBT pada ruangan ini berlangsung dengan tertib, lancar, dan aman sesuai dengan seluruh ketentuan dan tata tertib yang berlaku.
        </div>
    </div>

    <!-- PENGESAHAN / TANDA TANGAN -->
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
