<?php
$bulanIndo = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$tglCetak = date('d') . ' ' . ($bulanIndo[(int)date('n')] ?? date('F')) . ' ' . date('Y');

$namaInstitusi = 'MADRASAH ALIYAH MIFTAHUL HUDA';
$namaKepala = 'AHMAD RIFA\'I, M.Pd';
$nipKepala = '-';
$logoUrl = function_exists('base_url') ? base_url('public/uploads/logo/logo.png') : '/public/uploads/logo/logo.png';
$faviconUrl = $logoUrl;
if (class_exists('\App\Core\Branding')) {
    try {
        $logoUrl = \App\Core\Branding::getInstitusiLogo();
        $faviconUrl = \App\Core\Branding::getInstitusiFavicon();
    } catch (\Throwable $e) {}
}
$kemenagLogo = function_exists('base_url') ? base_url('public/assets/img/kemenag-logo.png') : '/public/assets/img/kemenag-logo.png';

$namaUjian = !empty($currentUjian['nama_ujian']) ? $currentUjian['nama_ujian'] : 'Asesmen CBT Siswa';
$mapelNama = !empty($currentUjian['mapel_nama']) ? $currentUjian['mapel_nama'] : (!empty($paket['mapel_nama']) ? $paket['mapel_nama'] : 'Semua Mapel');
$topikNama = !empty($currentUjian['topik_nama']) ? $currentUjian['topik_nama'] : (!empty($topik['nama_topik']) ? $topik['nama_topik'] : 'Simulasi CBT');
$kelasLabel = !empty($selectedKelas) ? $selectedKelas : (!empty($currentUjian['kelas_nama']) ? $currentUjian['kelas_nama'] : 'Semua Kelas');
$kkmNilai = (int)($currentUjian['kkm'] ?? ($kkmUjian ?? 75));
$durasiMenit = (int)($currentUjian['durasi_menit'] ?? 90);
$guruNama = !empty($currentUjian['guru_nama']) ? $currentUjian['guru_nama'] : (!empty($_SESSION['guru_nama']) ? $_SESSION['guru_nama'] : 'Proktor CBT');

// Sort label description
$sortLabels = [
    'nilai_desc' => 'Nilai Tertinggi ke Terendah (Rank)',
    'nilai_asc'  => 'Nilai Terendah ke Tertinggi',
    'nama_asc'   => 'Nama Siswa (A - Z)',
    'nama_desc'  => 'Nama Siswa (Z - A)',
    'nis_asc'    => 'Nomor Peserta / NIS (Urut)',
    'waktu_desc' => 'Waktu Selesai Ujian'
];
$activeSortLabel = $sortLabels[$sort ?? 'nilai_desc'] ?? 'Nilai Tertinggi';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Nilai CBT - <?= htmlspecialchars($mapelNama . ' (' . $kelasLabel . ')') ?></title>
    <link rel="icon" type="image/png" href="<?= $faviconUrl ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            background: #f1f5f9;
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            color: #0f172a;
            padding: 20px;
        }

        /* Controls Bar (Layar Saja) */
        .no-print-bar {
            max-width: 950px;
            margin: 0 auto 20px;
            background: #ffffff;
            padding: 14px 20px;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border: 1px solid #cbd5e1;
        }
        .btn-ctrl {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: 0.2s;
        }
        .btn-print {
            background: #2563eb;
            color: white;
            box-shadow: 0 3px 10px rgba(37, 99, 235, 0.3);
        }
        .btn-print:hover { background: #1d4ed8; }
        .btn-back {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }
        .btn-back:hover { background: #e2e8f0; }

        /* Lembar Dokumen Kertas A4 */
        .paper-sheet {
            max-width: 950px;
            margin: 0 auto;
            background: #ffffff;
            padding: 25mm 20mm 20mm;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            min-height: 297mm;
            position: relative;
        }

        /* Kop Surat Resmi */
        .kop-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px double #0f172a;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .kop-logo {
            width: 75px;
            height: 75px;
            object-fit: contain;
            flex-shrink: 0;
        }
        .kop-title-box {
            flex: 1;
            text-align: center;
            padding: 0 15px;
        }
        .kop-title-box h4 {
            font-size: 0.88rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #1e293b;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .kop-title-box h2 {
            font-size: 1.25rem;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 0.3px;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        .kop-title-box p {
            font-size: 0.72rem;
            color: #475569;
            line-height: 1.35;
        }

        /* Judul Dokumen */
        .doc-title-box {
            text-align: center;
            margin-bottom: 18px;
        }
        .doc-title-box h3 {
            font-size: 1.05rem;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: underline;
            margin-bottom: 3px;
        }
        .doc-title-box p {
            font-size: 0.78rem;
            color: #475569;
            font-weight: 700;
        }

        /* Metadata Info Grid */
        .meta-table {
            width: 100%;
            margin-bottom: 16px;
            border-collapse: collapse;
            font-size: 0.8rem;
        }
        .meta-table td {
            padding: 4px 8px;
            vertical-align: top;
        }
        .meta-lbl {
            width: 18%;
            color: #475569;
            font-weight: 700;
        }
        .meta-sep {
            width: 2%;
            font-weight: 800;
            color: #0f172a;
        }
        .meta-val {
            width: 30%;
            font-weight: 800;
            color: #0f172a;
        }

        /* Summary Stats Badge Box */
        .summary-stats-box {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 8px;
            margin-bottom: 18px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 12px;
            text-align: center;
        }
        .stat-item-box {
            border-right: 1px solid #e2e8f0;
            padding: 2px 4px;
        }
        .stat-item-box:last-child { border-right: none; }
        .stat-item-num {
            font-size: 1.05rem;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.1;
        }
        .stat-item-lbl {
            font-size: 0.65rem;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* Tabel Data Nilai Siswa */
        .nilai-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 0.78rem;
        }
        .nilai-table th, .nilai-table td {
            border: 1px solid #0f172a;
            padding: 6px 7px;
            vertical-align: middle;
        }
        .nilai-table th {
            background: #e2e8f0;
            color: #0f172a;
            font-weight: 800;
            text-align: center;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .nilai-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .center-col { text-align: center; }
        .right-col { text-align: right; }
        .badge-status-print {
            font-weight: 800;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
        }
        .print-tuntas {
            color: #15803d;
        }
        .print-remedial {
            color: #b91c1c;
        }
        .print-proses {
            color: #b45309;
        }
        .badge-kj-tag {
            background: #fdf2f8;
            color: #db2777;
            font-size: 0.62rem;
            font-weight: 800;
            padding: 1px 5px;
            border-radius: 4px;
            border: 1px solid #fbcfe8;
            margin-left: 4px;
        }

        /* Kolom Tanda Tangan */
        .sign-wrapper {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            page-break-inside: avoid;
            font-size: 0.82rem;
        }
        .sign-col {
            text-align: center;
            width: 250px;
        }
        .sign-space {
            height: 70px;
        }
        .sign-name {
            font-weight: 800;
            color: #0f172a;
            text-decoration: underline;
        }
        .sign-nip {
            font-size: 0.75rem;
            color: #475569;
            margin-top: 2px;
        }

        /* Print Media Query */
        @media print {
            body {
                background: white !important;
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
                min-height: auto !important;
            }
            .nilai-table th {
                background: #e2e8f0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .summary-stats-box {
                background: #f8fafc !important;
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

<!-- Control Bar di Layar -->
<div class="no-print-bar">
    <div style="display: flex; align-items: center; gap: 10px;">
        <button type="button" onclick="history.back()" class="btn-ctrl btn-back">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </button>
        <span style="font-size: 0.8rem; font-weight: 700; color: #64748b;">
            Urutan: <b><?= htmlspecialchars($activeSortLabel) ?></b> • Siswa: <b style="color: #0f172a;"><?= count($pesertaList) ?> Terdata</b>
        </span>
    </div>
    <div style="display: flex; align-items: center; gap: 10px;">
        <button type="button" onclick="downloadPdfDirect()" id="btnDirectDownload" class="btn-ctrl" style="background: #059669; color: white; box-shadow: 0 3px 10px rgba(5, 150, 105, 0.3);">
            <i class="fa-solid fa-download"></i> Download File PDF
        </button>
        <button type="button" onclick="window.print()" class="btn-ctrl btn-print">
            <i class="fa-solid fa-print"></i> Cetak / Print A4
        </button>
    </div>
</div>

<!-- Lembar Cetak Kertas -->
<div class="paper-sheet">

    <!-- Kop Surat Resmi -->
    <div class="kop-header">
        <img src="<?= $logoUrl ?>" class="kop-logo" onerror="this.src='/public/uploads/logo/logo.png'" alt="Logo Madrasah">
        <div class="kop-title-box">
            <h4>KEMENTERIAN AGAMA REPUBLIK INDONESIA</h4>
            <h2><?= htmlspecialchars($namaInstitusi) ?></h2>
            <p>STATUS TERAKREDITASI • NSM: 131218040003 • NPSN: 69988085</p>
            <p>Jalan Raya Miftahul Huda, Sindangjaya, Kec. Mangunjaya, Kab. Pangandaran / Lampung Barat</p>
        </div>
        <img src="<?= $kemenagLogo ?>" class="kop-logo" onerror="this.style.visibility='hidden'" alt="Logo Kemenag">
    </div>

    <!-- Judul Dokumen -->
    <div class="doc-title-box">
        <h3>REKAPITULASI HASIL ASESMEN CBT & DAFTAR NILAI SISWA</h3>
        <p>TAHUN AJARAN 2026/2027</p>
    </div>

    <!-- Metadata Informasi -->
    <table class="meta-table">
        <tr>
            <td class="meta-lbl">Mata Pelajaran</td>
            <td class="meta-sep">:</td>
            <td class="meta-val"><?= htmlspecialchars($mapelNama) ?></td>

            <td class="meta-lbl">Kelas / Rombel</td>
            <td class="meta-sep">:</td>
            <td class="meta-val"><?= htmlspecialchars($kelasLabel) ?></td>
        </tr>
        <tr>
            <td class="meta-lbl">Topik Asesmen</td>
            <td class="meta-sep">:</td>
            <td class="meta-val"><?= htmlspecialchars($topikNama) ?></td>

            <td class="meta-lbl">Standar KKM</td>
            <td class="meta-sep">:</td>
            <td class="meta-val"><?= $kkmNilai ?> Poin</td>
        </tr>
        <tr>
            <td class="meta-lbl">Alokasi Waktu</td>
            <td class="meta-sep">:</td>
            <td class="meta-val"><?= $durasiMenit ?> Menit</td>

            <td class="meta-lbl">Tanggal Cetak</td>
            <td class="meta-sep">:</td>
            <td class="meta-val"><?= $tglCetak ?></td>
        </tr>
    </table>

    <!-- Ringkasan Statistik -->
    <div class="summary-stats-box">
        <div class="stat-item-box">
            <div class="stat-item-num"><?= count($pesertaList) ?></div>
            <div class="stat-item-lbl">Total Siswa</div>
        </div>
        <div class="stat-item-box">
            <div class="stat-item-num"><?= $totalSelesai ?></div>
            <div class="stat-item-lbl">Selesai</div>
        </div>
        <div class="stat-item-box">
            <div class="stat-item-num"><?= number_format($rataRata, 1) ?></div>
            <div class="stat-item-lbl">Rata-Rata</div>
        </div>
        <div class="stat-item-box">
            <div class="stat-item-num" style="color: #15803d;"><?= number_format($maxNilai ?? ($maxScore ?? 0), 1) ?></div>
            <div class="stat-item-lbl">Tertinggi</div>
        </div>
        <div class="stat-item-box">
            <div class="stat-item-num" style="color: #b91c1c;"><?= number_format($minNilai ?? ($minScore ?? 0), 1) ?></div>
            <div class="stat-item-lbl">Terendah</div>
        </div>
        <div class="stat-item-box">
            <div class="stat-item-num" style="color: #2563eb;"><?= $persenTuntas ?>%</div>
            <div class="stat-item-lbl">Tuntas (<?= $tuntasCount ?>)</div>
        </div>
    </div>

    <!-- Tabel Daftar Nilai Siswa -->
    <table class="nilai-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 14%;">No Peserta / NIS</th>
                <th style="width: 28%;">Nama Lengkap Siswa</th>
                <th style="width: 12%;">Kelas</th>
                <th style="width: 7%;">Benar</th>
                <th style="width: 7%;">Salah</th>
                <th style="width: 7%;">Kosong</th>
                <th style="width: 9%;">Nilai Akhir</th>
                <th style="width: 12%;">Ketuntasan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pesertaList)): ?>
                <tr>
                    <td colspan="9" class="center-col" style="padding: 20px; color: #64748b; font-style: italic;">
                        Tidak ada data hasil ujian yang sesuai dengan kriteria filter.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($pesertaList as $idx => $p): ?>
                    <?php
                    $isSelesai = ($p['status_pengerjaan'] === 'Selesai');
                    $isTerkunci = in_array($p['status_pengerjaan'] ?? '', ['Terkunci', 'Pelanggaran']);
                    $nilai = (float)($p['nilai_akhir'] ?? 0);
                    $isTuntas = ($nilai >= $kkmNilai);
                    $isKJ = (stripos($p['kelas_nama'] ?? '', 'Jauh') !== false || stripos($p['nama_siswa'] ?? '', 'Kelas Jauh') !== false);

                    $benar = (int)($p['jumlah_benar'] ?? 0);
                    $salah = (int)($p['jumlah_salah'] ?? 0);
                    $tidakJawab = (int)($p['jumlah_tidak_jawab'] ?? 0);
                    $totalSoal = (int)($p['total_soal_ujian'] ?? 0);
                    if ($totalSoal <= 0) $totalSoal = max(20, $benar + $salah);
                    if ($tidakJawab == 0 && ($benar + $salah) < $totalSoal) {
                        $tidakJawab = $totalSoal - ($benar + $salah);
                    }
                    ?>
                    <tr>
                        <td class="center-col"><?= $idx + 1 ?></td>
                        <td class="center-col font-mono" style="font-family: 'JetBrains Mono', monospace; font-weight: 700;">
                            <?= htmlspecialchars($p['nis'] ?: '-') ?>
                        </td>
                        <td style="font-weight: 700; color: #0f172a;">
                            <?= htmlspecialchars($p['nama_siswa']) ?>
                            <?php if ($isKJ): ?>
                                <span class="badge-kj-tag">Kelas Jauh</span>
                            <?php endif; ?>
                        </td>
                        <td class="center-col"><?= htmlspecialchars($p['kelas_nama'] ?: '-') ?></td>
                        <td class="center-col" style="color: #15803d; font-weight: 700;"><?= $isSelesai ? $benar : '-' ?></td>
                        <td class="center-col" style="color: #b91c1c; font-weight: 700;"><?= $isSelesai ? $salah : '-' ?></td>
                        <td class="center-col" style="color: #64748b; font-weight: 700;"><?= $isSelesai ? $tidakJawab : '-' ?></td>
                        <td class="center-col" style="font-weight: 900; font-size: 0.88rem; color: <?= $isSelesai ? ($isTuntas ? '#15803d' : '#b91c1c') : '#64748b' ?>;">
                            <?= number_format($nilai, 1) ?>
                        </td>
                        <td class="center-col">
                            <?php if ($isSelesai): ?>
                                <span class="badge-status-print <?= $isTuntas ? 'print-tuntas' : 'print-remedial' ?>">
                                    <?= $isTuntas ? 'TUNTAS' : 'REMEDIAL' ?>
                                </span>
                            <?php elseif ($isTerkunci): ?>
                                <span class="badge-status-print print-remedial">TERKUNCI</span>
                            <?php else: ?>
                                <span class="badge-status-print print-proses">PROSES</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tanda Tangan Resmi -->
    <div class="sign-wrapper">
        <div class="sign-col">
            <div>Mengetahui,</div>
            <div style="font-weight: 700;">Kepala Madrasah</div>
            <div class="sign-space"></div>
            <div class="sign-name"><?= htmlspecialchars($namaKepala) ?></div>
            <div class="sign-nip">NIP. <?= htmlspecialchars($nipKepala) ?></div>
        </div>

        <div class="sign-col">
            <div><?= htmlspecialchars($kotaCetak ?? 'Lampung Barat') ?>, <?= $tglCetak ?></div>
            <div style="font-weight: 700;">Guru Pengampu / Proktor CBT</div>
            <div class="sign-space"></div>
            <div class="sign-name"><?= htmlspecialchars($guruNama) ?></div>
            <div class="sign-nip">NIP. -</div>
        </div>
    </div>

</div>

<script>
function downloadPdfDirect() {
    const btn = document.getElementById('btnDirectDownload');
    if (btn) btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Membuat File PDF...';
    
    const element = document.querySelector('.paper-sheet');
    const safeTitle = '<?= addslashes(preg_replace('/[^A-Za-z0-9_-]/', '_', "Rekap_Nilai_{$mapelNama}_{$kelasLabel}")) ?>';
    const opt = {
        margin: [8, 8, 8, 8],
        filename: safeTitle + '.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true, logging: false },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    if (typeof html2pdf !== 'undefined') {
        html2pdf().set(opt).from(element).save().then(() => {
            if (btn) btn.innerHTML = '<i class="fa-solid fa-check"></i> File Terunduh!';
            setTimeout(() => {
                if (btn) btn.innerHTML = '<i class="fa-solid fa-download"></i> Download File PDF';
            }, 3000);
        }).catch(err => {
            console.error(err);
            window.print();
        });
    } else {
        window.print();
    }
}

window.addEventListener('load', function() {
    <?php if (!empty($_GET['download'])): ?>
    setTimeout(() => {
        downloadPdfDirect();
    }, 500);
    <?php elseif (!empty($_GET['autoprint'])): ?>
    setTimeout(() => {
        window.print();
    }, 400);
    <?php endif; ?>
});
</script>

</body>
</html>
