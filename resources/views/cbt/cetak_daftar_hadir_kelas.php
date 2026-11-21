<?php
$topikId = isset($_GET['topik_id']) ? (int)$_GET['topik_id'] : 0;
$topik = $topik ?? [];
$kelasList = $kelasList ?? [];
$selectedKelas = $selectedKelas ?? 'XII F.2';
$mapelList = $mapelList ?? [];
$selectedMapel = $selectedMapel ?? 'Bahasa Inggris';
$siswaKelas = $siswaKelas ?? [];
$totalSiswa = $totalSiswa ?? count($siswaKelas);
$totalSudahUjian = $totalSudahUjian ?? 0;
$totalBelumUjian = $totalBelumUjian ?? 0;
$belumUjianList = $belumUjianList ?? [];
$kkm = $kkm ?? 75;
$tanggal = $tanggal ?? date('Y-m-d');
$kop = $kop ?? [];
$inst = $inst ?? ['nama_kepala' => 'AHMAD RIFA\'I, M.Pd', 'nip_kepala' => '-', 'kota' => 'Lampung Barat'];

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
    <title>Daftar Hadir & Status Pengerjaan Siswa Kelas <?= htmlspecialchars($selectedKelas) ?> - <?= htmlspecialchars($selectedMapel) ?></title>
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
            max-width: 950px; 
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
            max-width: 950px; 
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
            font-size: 12.5pt;
            font-weight: 900;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-title p {
            font-size: 9pt;
            font-weight: 700;
            margin-top: 2px;
            color: #333;
        }

        /* Metadata */
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

        /* Alert Box Susulan */
        .susulan-alert-box {
            border: 1.5px solid #dc2626;
            background: #fef2f2;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 8.5pt;
            margin-bottom: 14px;
        }
        .susulan-alert-box strong {
            color: #b91c1c;
        }

        /* Table */
        .siswa-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 15px;
        }
        .siswa-table th {
            border: 1px solid #000;
            padding: 6px 4px;
            background: #f1f5f9;
            text-align: center;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 8pt;
        }
        .siswa-table td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: middle;
        }

        .badge-status {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 7.5pt;
            font-weight: 800;
            text-transform: uppercase;
        }
        .badge-sudah {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }
        .badge-belum {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
            font-weight: 900;
        }

        /* Signature */
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
            .siswa-table th {
                background: #f1f5f9 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .badge-sudah {
                background: #dcfce7 !important;
                color: #166534 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .badge-belum {
                background: #fee2e2 !important;
                color: #991b1b !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .susulan-alert-box {
                background: #fef2f2 !important;
                border: 1.5px solid #dc2626 !important;
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
            
            <label style="font-size: 0.8rem; font-weight: 800; color: #475569;">Kelas:</label>
            <select name="kelas" onchange="this.form.submit()" class="select-filter">
                <?php foreach ($kelasList as $kl): ?>
                    <option value="<?= htmlspecialchars($kl) ?>" <?= ($selectedKelas === $kl) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($kl) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label style="font-size: 0.8rem; font-weight: 800; color: #475569;">Mata Pelajaran:</label>
            <select name="mapel" onchange="this.form.submit()" class="select-filter">
                <?php foreach ($mapelList as $mp): ?>
                    <option value="<?= htmlspecialchars($mp) ?>" <?= ($selectedMapel === $mp) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($mp) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label style="font-size: 0.8rem; font-weight: 800; color: #475569;">Tanggal:</label>
            <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>" onchange="this.form.submit()" class="select-filter">
        </form>
    </div>

    <button type="button" onclick="window.print()" class="btn-ctrl btn-print">
        <i class="fa-solid fa-print"></i> Cetak Format Guru Mapel (A4)
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
        <h2>DAFTAR HADIR &amp; STATUS PENGERJAAN ASESMEN CBT (PER-KELAS)</h2>
        <p>Laporan Khusus Guru Mata Pelajaran &bull; <?= htmlspecialchars($topikNama) ?> (TA <?= htmlspecialchars($topikTA) ?>)</p>
    </div>

    <!-- METADATA KELAS & MAPEL -->
    <table class="meta-table">
        <tr>
            <td style="width: 18%; font-weight: 700;">Kelas</td>
            <td style="width: 2%;">:</td>
            <td style="width: 38%; font-weight: 800; font-size: 9.5pt; color: #1e3a8a;"><?= htmlspecialchars($selectedKelas) ?></td>
            <td style="width: 18%; font-weight: 700;">Tanggal Asesmen</td>
            <td style="width: 2%;">:</td>
            <td style="width: 22%; font-weight: 800;"><?= htmlspecialchars($curDayIndo) ?>, <?= htmlspecialchars($tglFormatIndo) ?></td>
        </tr>
        <tr>
            <td style="font-weight: 700;">Mata Pelajaran</td>
            <td>:</td>
            <td style="font-weight: 800; font-size: 9.5pt;"><?= htmlspecialchars($selectedMapel) ?></td>
            <td style="font-weight: 700;">KKM Kelulusan</td>
            <td>:</td>
            <td style="font-weight: 800;"><?= (int)$kkm ?></td>
        </tr>
        <tr>
            <td style="font-weight: 700;">Statistik Kelas</td>
            <td>:</td>
            <td colspan="4">
                Total Siswa: <strong><?= $totalSiswa ?></strong> &bull; 
                Sudah Mengerjakan: <strong style="color: #059669;"><?= $totalSudahUjian ?></strong> &bull; 
                Belum Ujian: <strong style="color: #dc2626;"><?= $totalBelumUjian ?></strong>
            </td>
        </tr>
    </table>

    <!-- ALERT SISWA BELUM UJIAN (PERLU SUSULAN) -->
    <?php if (!empty($belumUjianList)): ?>
        <div class="susulan-alert-box">
            <strong><i class="fa-solid fa-triangle-exclamation"></i> DAFTAR SISWA BELUM UJIAN (REKOMENDASI UJIAN SUSULAN):</strong>
            <div style="margin-top: 4px; line-height: 1.4;">
                <?= implode(', ', array_map(fn($nama, $i) => ($i + 1) . '. ' . htmlspecialchars($nama), $belumUjianList, array_keys($belumUjianList))) ?>
            </div>
            <div style="font-size: 7.5pt; color: #7f1d1d; margin-top: 4px; font-style: italic;">
                * Catatan untuk Guru Mapel / Wali Kelas: Siswa di atas belum terdata menyelesaikan ujian CBT pada mapel ini. Silakan arahkan untuk mengikuti sesi Ujian Susulan.
            </div>
        </div>
    <?php else: ?>
        <div style="border: 1.5px solid #10b981; background: #ecfdf5; border-radius: 6px; padding: 6px 12px; font-size: 8.5pt; margin-bottom: 14px; color: #065f46;">
            <strong><i class="fa-solid fa-circle-check"></i> ALHAMDULILLAH!</strong> Seluruh siswa kelas <?= htmlspecialchars($selectedKelas) ?> (100%) telah menyelesaikan asesmen untuk mata pelajaran ini.
        </div>
    <?php endif; ?>

    <!-- TABEL SISWA SE-KELAS -->
    <table class="siswa-table">
        <thead>
            <tr>
                <th style="width: 28px;">No</th>
                <th style="width: 95px;">NISN / NIS</th>
                <th>Nama Lengkap Siswa</th>
                <th style="width: 80px;">Ruang Ujian</th>
                <th style="width: 50px;">Meja</th>
                <th style="width: 140px;">Status Pengerjaan</th>
                <th style="width: 55px;">Nilai</th>
                <th style="width: 75px;">Selesai</th>
                <th style="width: 90px;">Paraf Siswa</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($siswaKelas)): ?>
                <tr>
                    <td colspan="9" style="text-align: center; padding: 25px; font-style: italic;">
                        Tidak ditemukan data siswa untuk kelas <?= htmlspecialchars($selectedKelas) ?>.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($siswaKelas as $i => $sw): ?>
                    <?php 
                    $isSudah = ($sw['status_ujian'] === 'Sudah Mengerjakan');
                    $isSedang = ($sw['status_ujian'] === 'Sedang Mengerjakan');
                    $isGenap = (($i + 1) % 2 === 0);
                    ?>
                    <tr>
                        <td style="text-align: center; font-weight: bold;"><?= $i + 1 ?></td>
                        <td style="text-align: center; font-family: monospace; font-size: 8pt;">
                            <?= htmlspecialchars($sw['nisn'] ?: ($sw['nis'] ?: '-')) ?>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($sw['nama_siswa']) ?></strong>
                        </td>
                        <td style="text-align: center; font-size: 8pt;">
                            <?= htmlspecialchars($sw['nama_ruang'] ?: '-') ?>
                        </td>
                        <td style="text-align: center; font-weight: bold;">
                            <?= htmlspecialchars($sw['nomor_meja'] ?: '-') ?>
                        </td>
                        <td style="text-align: center;">
                            <?php if ($isSudah): ?>
                                <span class="badge-status badge-sudah">
                                    <i class="fa-solid fa-check"></i> SUDAH UJIAN
                                </span>
                            <?php elseif ($isSedang): ?>
                                <span class="badge-status" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a;">
                                    <i class="fa-solid fa-spinner"></i> MENGERJAKAN
                                </span>
                            <?php else: ?>
                                <span class="badge-status badge-belum">
                                    <i class="fa-solid fa-xmark"></i> BELUM UJIAN
                                </span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center; font-weight: bold; font-size: 9pt; color: <?= ($isSudah && (float)$sw['nilai_akhir'] >= $kkm) ? '#059669' : (($isSudah) ? '#dc2626' : '#64748b') ?>;">
                            <?= htmlspecialchars($sw['nilai_akhir']) ?>
                        </td>
                        <td style="text-align: center; font-size: 7.5pt; font-family: monospace;">
                            <?= htmlspecialchars($sw['waktu_selesai']) ?>
                        </td>
                        <td style="height: 30px; vertical-align: bottom; padding-bottom: 2px;">
                            <?php if ($isGenap): ?>
                                <div style="text-align: right; font-size: 7.5pt; color: #444; padding-right: 10px;"><?= $i + 1 ?>. ..........</div>
                            <?php else: ?>
                                <div style="text-align: left; font-size: 7.5pt; color: #444; padding-left: 4px;"><?= $i + 1 ?>. ..........</div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- TANDA TANGAN (Guru Mapel & Kepala Madrasah) -->
    <div class="sig-container">
        <div class="sig-box">
            <div>Guru Mata Pelajaran,</div>
            <div style="font-weight: 700;"><?= htmlspecialchars($selectedMapel) ?></div>
            <div class="sig-space"></div>
            <div class="sig-name">( ........................................ )</div>
            <div>NIP. -</div>
        </div>

        <div class="sig-box">
            <div><?= htmlspecialchars($inst['kota'] ?? 'Lampung Barat') ?>, <?= htmlspecialchars($tglFormatIndo) ?></div>
            <div style="font-weight: 700;">Kepala Madrasah</div>
            <div class="sig-space"></div>
            <div class="sig-name"><?= htmlspecialchars($inst['nama_kepala'] ?? 'AHMAD RIFA\'I, M.Pd') ?></div>
            <div>NIP. <?= htmlspecialchars($inst['nip_kepala'] ?? '-') ?></div>
        </div>
    </div>
</div>

</body>
</html>
