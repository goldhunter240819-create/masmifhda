<?php
$selectedTopik = $selectedTopik ?? [];
$paketList = $paketList ?? [];
$selectedKelas = $selectedKelas ?? 'Semua';
$kop = $kop ?? [];
$inst = $inst ?? ['nama_kepala' => 'AHMAD RIFA\'I, M.Pd', 'nip_kepala' => '-', 'kota' => 'Sekincau, Lampung Barat'];

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
$alamatJalan = $kop['alamat_jalan'] ?? 'Jl. Tirtomoyo, No. 083, Margorahayu, Kec. Sekincau, Kab. Lampung Barat';
$telepon = $kop['telepon'] ?? '';
$email = $kop['email'] ?? '';
$website = $kop['website'] ?? 'masmifhda.sch.id';

$topikNama = !empty($selectedTopik['nama_topik']) ? trim($selectedTopik['nama_topik']) : 'ASESMEN MADRASAH';
$tahunAjaran = !empty($selectedTopik['tahun_ajaran']) ? trim($selectedTopik['tahun_ajaran']) : '2026/2027';
$semester = !empty($selectedTopik['semester']) ? trim($selectedTopik['semester']) : 'Ganjil';

$totalPaket = count($paketList);
$totalSoal = array_sum(array_column($paketList, 'total_soal'));
$totalLocked = 0;
foreach ($paketList as $p) {
    if (!empty($p['is_locked'])) $totalLocked++;
}
$totalTerbuka = $totalPaket - $totalLocked;

$tanggalCetak = date('Y-m-d');
$bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$tglFormatIndo = date('j', strtotime($tanggalCetak)) . ' ' . $bulanIndo[(int)date('n', strtotime($tanggalCetak))] . ' ' . date('Y', strtotime($tanggalCetak));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Bank Soal: <?= htmlspecialchars($topikNama) ?> - MAS MIFTAHUL HUDA</title>
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
            padding: 14px 20px; 
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
            padding: 9px 18px; 
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
        .btn-back { 
            background: #f1f5f9; 
            color: #475569; 
            border: 1px solid #cbd5e1; 
        }
        .btn-back:hover { background: #e2e8f0; }

        .paper-sheet { 
            background: #ffffff; 
            width: 100%; 
            max-width: 950px; 
            margin: 0 auto; 
            padding: 35px 45px; 
            border-radius: 6px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.3); 
            color: #000000;
        }

        .kop-wrapper {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 16px;
            gap: 16px;
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
        .kop-kemenag {
            font-size: 10.5pt;
            font-weight: 700;
        }
        .kop-yayasan {
            font-size: 11pt;
            font-weight: 800;
            text-transform: uppercase;
        }
        .kop-madrasah {
            font-size: 14pt;
            font-weight: 900;
            color: #000;
            margin: 2px 0;
            text-transform: uppercase;
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
            font-size: 9.5pt;
            font-weight: 700;
            margin-top: 3px;
            color: #111;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 14px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }
        .meta-table td {
            padding: 5px 6px;
            vertical-align: top;
        }

        /* Summary Boxes */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 16px;
        }
        .summary-card {
            border: 1px solid #000;
            padding: 8px 10px;
            text-align: center;
            background: #f8fafc;
            border-radius: 4px;
        }
        .summary-num {
            font-size: 14pt;
            font-weight: 900;
            color: #000;
            line-height: 1;
        }
        .summary-lbl {
            font-size: 7.5pt;
            font-weight: 800;
            text-transform: uppercase;
            margin-top: 4px;
            color: #334155;
        }

        /* Table */
        .rekap-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 20px;
        }
        .rekap-table th {
            border: 1px solid #000;
            padding: 6px 4px;
            background: #f1f5f9;
            text-align: center;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 8pt;
        }
        .rekap-table td {
            border: 1px solid #000;
            padding: 6px 6px;
            vertical-align: middle;
        }

        .badge-locked {
            font-weight: 800;
            color: #92400e;
            background: #fef3c7;
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid #fde68a;
            font-size: 7.5pt;
            display: inline-block;
        }
        .badge-open {
            font-weight: 800;
            color: #047857;
            background: #ecfdf5;
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid #a7f3d0;
            font-size: 7.5pt;
            display: inline-block;
        }

        .ttd-wrapper {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
            font-size: 9pt;
        }
        .ttd-box {
            width: 250px;
            text-align: center;
        }
        .ttd-space {
            height: 65px;
        }
        .ttd-name {
            font-weight: 800;
            text-decoration: underline;
        }

        @media print {
            body { background: #ffffff !important; padding: 0 !important; }
            .no-print-bar { display: none !important; }
            .paper-sheet { box-shadow: none !important; padding: 15mm !important; max-width: 100% !important; }
            @page { size: A4; margin: 10mm; }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <a href="javascript:history.back()" class="btn-ctrl btn-back">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Bank Soal
        </a>
        <button type="button" onclick="window.print()" class="btn-ctrl btn-print">
            <i class="fa-solid fa-print"></i> Cetak / Simpan PDF (A4)
        </button>
    </div>

    <div class="paper-sheet">
        <div class="kop-wrapper">
            <img src="<?= $kopLogoUrl ?>" alt="Logo" class="kop-logo" onerror="this.style.display='none'">
            <div class="kop-text">
                <div class="kop-kemenag">KEMENTERIAN AGAMA REPUBLIK INDONESIA</div>
                <div class="kop-yayasan"><?= htmlspecialchars($namaYayasan) ?></div>
                <div class="kop-madrasah"><?= htmlspecialchars($namaMadrasah) ?></div>
                <div class="kop-alamat"><?= htmlspecialchars($alamatJalan) ?></div>
                <div class="kop-kontak">
                    <?php if(!empty($telepon)): ?>Telp: <?= htmlspecialchars($telepon) ?> | <?php endif; ?>
                    <?php if(!empty($email)): ?>Email: <?= htmlspecialchars($email) ?> | <?php endif; ?>
                    Website: <?= htmlspecialchars($website) ?>
                </div>
            </div>
        </div>

        <div class="doc-title">
            <h2>REKAPITULASI BANK SOAL ASESMEN MADRASAH</h2>
            <p><?= htmlspecialchars($topikNama) ?> TAHUN AJARAN <?= htmlspecialchars($tahunAjaran) ?></p>
        </div>

        <table class="meta-table">
            <tr>
                <td style="width: 18%; font-weight: 700;">Topik Asesmen</td>
                <td style="width: 3%;">:</td>
                <td style="width: 35%; font-weight: 800;"><?= htmlspecialchars($topikNama) ?></td>
                <td style="width: 18%; font-weight: 700;">Tahun Ajaran / Smt</td>
                <td style="width: 3%;">:</td>
                <td style="width: 23%; font-weight: 800;"><?= htmlspecialchars($tahunAjaran) ?> (<?= htmlspecialchars($semester) ?>)</td>
            </tr>
            <tr>
                <td style="font-weight: 700;">Tanggal Pelaksanaan</td>
                <td>:</td>
                <td>
                    <?= !empty($selectedTopik['tgl_mulai']) ? date('d M Y', strtotime($selectedTopik['tgl_mulai'])) : '-' ?> s/d
                    <?= !empty($selectedTopik['tgl_selesai']) ? date('d M Y', strtotime($selectedTopik['tgl_selesai'])) : '-' ?>
                </td>
                <td style="font-weight: 700;">Tanggal Cetak</td>
                <td>:</td>
                <td><?= $tglFormatIndo ?></td>
            </tr>
        </table>

        <!-- Ringkasan Statistik -->
        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-num"><?= $totalPaket ?></div>
                <div class="summary-lbl">Total Paket Mapel</div>
            </div>
            <div class="summary-card">
                <div class="summary-num"><?= number_format($totalSoal, 0, ',', '.') ?></div>
                <div class="summary-lbl">Total Butir Soal</div>
            </div>
            <div class="summary-card">
                <div class="summary-num" style="color: #b45309;"><?= $totalLocked ?></div>
                <div class="summary-lbl">Terkunci (Final)</div>
            </div>
            <div class="summary-card">
                <div class="summary-num" style="color: #047857;"><?= $totalTerbuka ?></div>
                <div class="summary-lbl">Terbuka (Draf)</div>
            </div>
        </div>

        <!-- Tabel Rekap -->
        <table class="rekap-table">
            <thead>
                <tr>
                    <th style="width: 35px;">No</th>
                    <th>Mata Pelajaran</th>
                    <th style="width: 160px;">Guru Pengampu</th>
                    <th style="width: 90px;">Tingkat</th>
                    <th style="width: 80px;">Rombel</th>
                    <th style="width: 70px;">Jumlah Soal</th>
                    <th style="width: 110px;">Rincian</th>
                    <th style="width: 110px;">Status Kunci</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($paketList)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px; color: #94a3b8;">
                            Belum ada paket mata pelajaran.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($paketList as $pk): 
                        $isLocked = !empty($pk['is_locked']);
                        $bd = $pk['breakdown'] ?? [];
                        $tot = $pk['total_soal'] ?? 0;
                    ?>
                        <tr>
                            <td style="text-align: center; font-weight: 700;"><?= $no++ ?></td>
                            <td style="font-weight: 800;"><?= htmlspecialchars($pk['mapel_nama']) ?></td>
                            <td><?= htmlspecialchars($pk['guru_nama'] ?: '-') ?></td>
                            <td style="text-align: center; font-weight: 700;"><?= htmlspecialchars($pk['kelas_nama'] ?: 'Kelas X') ?></td>
                            <td style="text-align: center; font-size: 8pt;"><?= htmlspecialchars($pk['kelas_sasaran'] ?: '-') ?></td>
                            <td style="text-align: center; font-weight: 900; font-size: 9.5pt;"><?= $tot ?></td>
                            <td style="font-size: 7.5pt; line-height: 1.3;">
                                <?php if (!empty($bd['sec_pg'])): ?><?= $bd['sec_pg'] ?> PG, <?php endif; ?>
                                <?php if (!empty($bd['sec_pgk'])): ?><?= $bd['sec_pgk'] ?> PGK, <?php endif; ?>
                                <?php if (!empty($bd['sec_essay'])): ?><?= $bd['sec_essay'] ?> Essay, <?php endif; ?>
                                <?php if (empty($bd)): ?>-<?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if ($isLocked): ?>
                                    <span class="badge-locked">Terkunci (Final)</span>
                                    <?php if (!empty($pk['locked_at'])): ?>
                                        <div style="font-size: 6.5pt; color: #64748b; margin-top: 2px;">
                                            <?= date('d/m/Y H:i', strtotime($pk['locked_at'])) ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge-open">Terbuka</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Tanda Tangan Pengesahan -->
        <div class="ttd-wrapper">
            <div class="ttd-box">
                <div>Mengetahui,</div>
                <div>Kepala Madrasah</div>
                <div class="ttd-space"></div>
                <div class="ttd-name"><?= htmlspecialchars($inst['nama_kepala']) ?></div>
                <div>NIP. <?= htmlspecialchars($inst['nip_kepala']) ?></div>
            </div>

            <div class="ttd-box">
                <div><?= htmlspecialchars($inst['kota']) ?>, <?= $tglFormatIndo ?></div>
                <div>Waka Kurikulum / Panitia CBT</div>
                <div class="ttd-space"></div>
                <div class="ttd-name">......................................................</div>
                <div>NIP. -</div>
            </div>
        </div>
    </div>

</body>
</html>
