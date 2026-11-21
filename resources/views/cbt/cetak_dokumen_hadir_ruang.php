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
    <title>Daftar Hadir - <?= htmlspecialchars($ruang['nama_ruang'] ?? 'Ruang') ?> (<?= htmlspecialchars($sesiLabel) ?>)</title>
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
            max-width: 900px; 
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
            background: linear-gradient(135deg, #059669 0%, #047857 100%); 
            color: white; 
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.35);
        }
        .btn-print:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(5, 150, 105, 0.45);
        }
        .btn-back { 
            background: #f1f5f9; 
            color: #475569; 
            border: 1px solid #cbd5e1; 
        }
        .btn-switch {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
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
            padding-bottom: 10px;
            margin-bottom: 14px;
            gap: 16px;
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
            letter-spacing: 0.5px;
        }
        .kop-madrasah {
            font-size: 15pt;
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
            margin-bottom: 16px;
        }
        .doc-title h2 {
            font-size: 13pt;
            font-weight: 900;
            text-decoration: underline;
            letter-spacing: 0.5px;
        }
        .doc-title p {
            font-size: 9.5pt;
            font-weight: 800;
            margin-top: 3px;
            text-transform: uppercase;
        }

        /* Tabel Parameter */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 9pt;
        }
        .info-table td {
            padding: 3px 6px;
            vertical-align: top;
        }
        .info-table .lbl {
            width: 135px;
            font-weight: 700;
            color: #111;
        }
        .info-table .sep {
            width: 10px;
            text-align: center;
            font-weight: 700;
        }
        .info-table .val {
            font-weight: 800;
            color: #000;
        }

        /* Tabel Siswa */
        .peserta-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 24px;
            font-size: 8.5pt;
        }
        .peserta-table th, .peserta-table td {
            border: 1px solid #000000;
            padding: 6px 8px;
            vertical-align: middle;
        }
        .peserta-table th {
            background-color: #f1f5f9;
            font-weight: 800;
            text-align: center;
            text-transform: uppercase;
            font-size: 8pt;
            letter-spacing: 0.3px;
        }
        .peserta-table td.center {
            text-align: center;
        }
        .peserta-table td.mono {
            font-family: monospace;
            font-size: 8pt;
        }

        /* Scan Time Text In Table */
        .scan-time-text {
            font-family: monospace;
            font-weight: 800;
            font-size: 8pt;
            color: #000000;
        }
        .scan-method-text {
            font-size: 7pt;
            color: #555555;
            display: block;
        }
        .status-badge-print {
            font-weight: 800;
            font-size: 8pt;
            text-transform: uppercase;
        }
        .status-hadir {
            color: #15803d;
        }
        .status-absen {
            color: #b91c1c;
        }

        /* Signature */
        .sig-container {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .sig-box {
            width: 260px;
            text-align: center;
            font-size: 9pt;
            line-height: 1.4;
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
            }
            .no-print-bar { 
                display: none !important; 
            }
            .paper-sheet { 
                box-shadow: none !important; 
                padding: 8mm 12mm !important; 
                max-width: 100% !important; 
            }
            .peserta-table th {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            @page {
                size: A4 portrait;
                margin: 8mm;
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
        <a href="<?= $bu('cbt/ruang/berita-acara/cetak-ba?topik_id=' . $topikId . '&ruang_id=' . $ruangId . '&tanggal=' . $tanggal . '&sesi=' . urlencode($sesiLabel)) ?>" class="btn-ctrl btn-switch">
            <i class="fa-solid fa-file-signature"></i> Lihat Berita Acara
        </a>
    </div>

    <div style="display: flex; align-items: center; gap: 10px;">
        <span style="font-size: 0.8rem; font-weight: 700; color: #64748b;">Format: A4 Portrait (Jam Scan Presensi)</span>
        <button onclick="window.print()" class="btn-ctrl btn-print">
            <i class="fa-solid fa-print"></i> Cetak Daftar Hadir Ruang (A4)
        </button>
    </div>
</div>

<!-- Lembar Dokumen Daftar Hadir A4 -->
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
        <h2>DAFTAR HADIR PESERTA RUANG UJIAN CBT</h2>
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
            <td class="lbl">Sesi Ujian</td>
            <td class="sep">:</td>
            <td class="val"><?= htmlspecialchars($sesiLabel) ?> (<?= htmlspecialchars($jamSesi) ?>)</td>
            <td class="lbl" style="padding-left: 20px;">Mata Pelajaran</td>
            <td class="sep">:</td>
            <td class="val"><?= htmlspecialchars($mapelNama) ?></td>
        </tr>
        <tr>
            <td class="lbl">Pengawas Ruang</td>
            <td class="sep">:</td>
            <td class="val"><?= htmlspecialchars($pengawasNama) ?></td>
            <td class="lbl" style="padding-left: 20px;">Statistik Peserta</td>
            <td class="sep">:</td>
            <td class="val"><?= $totalTerdaftar ?> Terdaftar • <?= $totalHadir ?> Hadir • <?= $totalTidakHadir ?> Tidak Hadir</td>
        </tr>
    </table>

    <!-- TABEL DAFTAR HADIR (JAM SCAN KARTU SISWA) -->
    <table class="peserta-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 55px;">No Meja</th>
                <th style="width: 100px;">No. Peserta</th>
                <th style="width: 90px;">NISN / NIS</th>
                <th>Nama Lengkap Peserta</th>
                <th style="width: 65px;">Kelas</th>
                <th style="width: 135px;">Waktu Scan Presensi</th>
                <th style="width: 85px;">Status</th>
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
                    <tr>
                        <td class="center" style="font-weight: bold;"><?= $idx + 1 ?></td>
                        <td class="center" style="font-weight: 800;"><?= htmlspecialchars($ps['nomor_meja'] ?: '-') ?></td>
                        <td class="center mono" style="font-weight: bold;">
                            <?= htmlspecialchars($ps['no_peserta'] ?? ($ps['nis'] ?? '-')) ?>
                        </td>
                        <td class="center mono">
                            <?= htmlspecialchars($ps['nisn'] ?: ($ps['nis'] ?: '-')) ?>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($ps['nama_siswa']) ?></strong>
                        </td>
                        <td class="center" style="font-weight: 600;">
                            <?= htmlspecialchars($ps['kelas_nama'] ?: '-') ?>
                        </td>
                        <td class="center">
                            <?php if ($ps['is_hadir']): ?>
                                <span class="scan-time-text"><?= htmlspecialchars($ps['waktu_scan']) ?></span>
                                <span class="scan-method-text">(<?= htmlspecialchars($ps['metode_scan']) ?>)</span>
                            <?php else: ?>
                                <span style="color: #991b1b; font-weight: 700; font-size: 7.5pt;">Belum Scan</span>
                            <?php endif; ?>
                        </td>
                        <td class="center">
                            <?php if ($ps['is_hadir']): ?>
                                <span class="status-badge-print status-hadir">HADIR</span>
                            <?php else: ?>
                                <span class="status-badge-print status-absen">TIDAK HADIR</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

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
