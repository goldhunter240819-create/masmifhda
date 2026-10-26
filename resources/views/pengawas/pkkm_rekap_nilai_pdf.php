<?php
use App\Core\Branding;

$brand = Branding::get();
$logoUrl = Branding::getInstitusiLogo();

$sekolah_nama = $inst['nama'] ?? 'MIS MIFTAHUL HUDA MATHLA\'UL ANWAR';
$npsn = $inst['npsn'] ?? '60705617';
$nsm = $inst['nsm'] ?? '111218040009';
$alamat = $inst['alamat'] ?? 'Jl. Tirtomoyo No. 083, Margorahayu';
$kecamatan = $inst['kecamatan'] ?? 'Way Tenong';
$kabupaten = $inst['kota'] ?? 'Lampung Barat';
$provinsi = $inst['provinsi'] ?? 'Lampung';
$kodepos = $inst['kodepos'] ?? '34884';

$kepala_nama = $kamad['nama'] ?? 'SAMSUL ARIFIN, M.Pd';
$kepala_nip = $kamad['nip'] ?? '-';

$p1_nama = $pengawas['nama'] ?? 'H. AHMAD SYARIF, M.Pd.I';
$p1_nip = $pengawas['nip'] ?? '197405122002121004';
$p1_ttd = $pengawas['ttd_digital'] ?? null;

$p2_nama = $pengawas2['nama'] ?? 'DRA. HJ. SITI NURHAYATI, M.Pd';
$p2_nip = $pengawas2['nip'] ?? '197803152005012003';
$p2_ttd = $pengawas2['ttd_digital'] ?? null;

$bulanIndo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$tgl_indo = date('j') . ' ' . $bulanIndo[(int)date('n')] . ' ' . date('Y');

// Hitung total keseluruhan
$total_ind_all = 0;
$total_skor_riil = 0;
$total_skor_maks = 0;
foreach ($komponens as $k) {
    $total_ind_all += $k['total_ind'] ?? 0;
    $total_skor_riil += $k['skor_perolehan'] ?? 0;
    $total_skor_maks += $k['skor_maks'] ?? 0;
}
$nkkm = $total_skor_maks > 0 ? round(($total_skor_riil / $total_skor_maks) * 100, 2) : 0;

$predikat = 'Kurang';
if ($nkkm >= 91) {
    $predikat = 'AMAT BAIK (A)';
} elseif ($nkkm >= 76) {
    $predikat = 'BAIK (B)';
} elseif ($nkkm >= 61) {
    $predikat = 'CUKUP (C)';
} elseif ($nkkm >= 51) {
    $predikat = 'SEDANG (D)';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Penilaian Kinerja Kepala Madrasah (PKKM) - <?php echo htmlspecialchars($sekolah_nama); ?></title>
    <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($brand['favicon']); ?>">
    <link rel="stylesheet" href="/public/assets/css/plus-jakarta-sans.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 15mm 10mm 15mm;
        }

        body {
            background: #e2e8f0;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.35;
            color: #0f172a;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .no-print-bar {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            color: #ffffff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .bar-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 0.95rem;
        }
        .bar-btn {
            background: #ffffff;
            color: #0f172a;
            border: none;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .bar-btn:hover { background: #f0fdf4; }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 12mm 16mm;
            margin: 15px auto;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            box-sizing: border-box;
            position: relative;
        }

        /* Kop Surat */
        .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 2px; }
        .kop-logo { width: 75px; text-align: center; vertical-align: middle; }
        .kop-logo img { width: 70px; height: auto; max-height: 75px; object-fit: contain; }
        .kop-text { text-align: center; line-height: 1.2; padding-left: 10px; }
        .kop-text .inst-kemenag { font-size: 10.5pt; font-weight: bold; }
        .kop-text .inst-yayasan { font-size: 11.5pt; font-weight: bold; }
        .kop-text .inst-nama { font-size: 13.5pt; font-weight: bold; margin: 2px 0; color: #0f172a; }
        .kop-text .inst-status { font-size: 8.5pt; font-weight: bold; letter-spacing: 0.3px; }
        .kop-text .inst-kontak { font-size: 8pt; font-style: italic; margin-top: 2px; }
        .kop-line { border-top: 3px solid #000; border-bottom: 1px solid #000; height: 2px; margin: 3px 0 12px 0; }

        .doc-title-wrap { text-align: center; margin-bottom: 12px; }
        .doc-title { font-size: 11.5pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; margin: 0; }
        .doc-nomor { font-size: 9pt; margin-top: 2px; color: #334155; }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 12px;
        }
        .meta-table td {
            padding: 2.5px 4px;
            vertical-align: top;
        }

        /* Table Rekap Skor */
        .rekap-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 12px;
        }
        .rekap-table th, .rekap-table td {
            border: 1px solid #0f172a;
            padding: 5px 8px;
            vertical-align: middle;
        }
        .rekap-table th {
            background: #e2e8f0;
            font-weight: bold;
            text-align: center;
        }

        /* Final Score Box */
        .score-summary-box {
            border: 2px solid #0284c7;
            background: #f0fdf4;
            border-radius: 8px;
            padding: 10px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .score-summary-val {
            font-size: 1.45rem;
            font-weight: 900;
            color: #0f172a;
        }

        /* Tanda Tangan */
        .ttd-wrap {
            margin-top: 15px;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            page-break-inside: avoid;
        }
        .ttd-box {
            width: 44%;
            text-align: center;
            font-size: 9pt;
            position: relative;
        }
        .ttd-space { height: 50px; }
        .ttd-name { font-weight: bold; text-decoration: underline; margin-bottom: 1px; }

        @media print {
            body { background: #ffffff !important; }
            .no-print-bar { display: none !important; }
            .page {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                min-height: auto !important;
            }
        }
    </style>
</head>
<body>

    <!-- Bar Navigasi Cetak -->
    <div class="no-print-bar">
        <div class="bar-title">
            <span>Rekapitulasi Penilaian Kinerja Kepala Madrasah (PKKM)</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="/pengawas/pkkm" class="bar-btn" style="background: rgba(255,255,255,0.2); color:#fff;">
                &larr; Kembali ke Worksheet
            </a>
            <button onclick="window.print()" class="bar-btn">
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Halaman Dokumen A4 -->
    <div class="page">

        <!-- KOP SURAT MADRASAH -->
        <table class="kop-table">
            <tr>
                <td class="kop-logo">
                    <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Logo">
                </td>
                <td class="kop-text">
                    <div class="inst-kemenag">KEMENTERIAN AGAMA REPUBLIK INDONESIA</div>
                    <div class="inst-yayasan">KANTOR KEMENTERIAN AGAMA KABUPATEN LAMPUNG BARAT</div>
                    <div class="inst-nama"><?php echo strtoupper(htmlspecialchars($sekolah_nama)); ?></div>
                    <div class="inst-status">NSM: <?php echo htmlspecialchars($nsm); ?> | NPSN: <?php echo htmlspecialchars($npsn); ?> | TERAKREDITASI B</div>
                    <div class="inst-kontak">
                        Alamat: <?php echo htmlspecialchars($alamat . ', Kec. ' . $kecamatan . ', ' . $kabupaten . ' - ' . $provinsi . ' (' . $kodepos . ')'); ?>
                    </div>
                </td>
            </tr>
        </table>
        <div class="kop-line"></div>

        <!-- JUDUL DOKUMEN -->
        <div class="doc-title-wrap">
            <h1 class="doc-title">BERITA ACARA & REKAPITULASI HASIL PENILAIAN KINERJA KEPALA MADRASAH (PKKM)</h1>
            <div class="doc-nomor">Nomor: B-421.2/PKKM-LAMBAR/<?php echo date('Y'); ?> | Tahun Pelajaran 2026/2027</div>
        </div>

        <!-- DATA IDENTITAS -->
        <table class="meta-table">
            <tr>
                <td style="width: 25%; font-weight: bold;">Nama Kepala Madrasah</td>
                <td style="width: 2%;">:</td>
                <td style="width: 73%; font-weight: bold;"><?php echo htmlspecialchars($kepala_nama); ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">NIP</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($kepala_nip); ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Nama Madrasah</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($sekolah_nama); ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Alamat Madrasah</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($alamat . ', Kec. ' . $kecamatan . ', ' . $kabupaten); ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Periode Penilaian</td>
                <td>:</td>
                <td>Tahun ke-1, 2, dan 3 (Tahun Pelajaran 2026/2027)</td>
            </tr>
        </table>

        <!-- TABEL REKAPITULASI PER KOMPONEN -->
        <table class="rekap-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Komponen Penilaian Kinerja</th>
                    <th style="width: 14%;">Jml Indikator</th>
                    <th style="width: 14%;">Skor Maks</th>
                    <th style="width: 15%;">Skor Perolehan</th>
                    <th style="width: 14%;">Ketercapaian</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($komponens as $k): ?>
                <tr>
                    <td style="text-align: center;"><?php echo $no++; ?></td>
                    <td><strong><?php echo htmlspecialchars($k['nama'] ?? $k['nama_komponen'] ?? ('Komponen ' . ($k['urutan'] ?? ''))); ?></strong></td>
                    <td style="text-align: center;"><?php echo $k['total_ind']; ?></td>
                    <td style="text-align: center;"><?php echo $k['skor_maks']; ?></td>
                    <td style="text-align: center; font-weight: bold;"><?php echo $k['skor_perolehan']; ?></td>
                    <td style="text-align: center; font-weight: bold; color: #0f172a;"><?php echo $k['nilai_komponen']; ?>%</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background: #f1f5f9; font-weight: bold;">
                    <td colspan="2" style="text-align: center;">TOTAL PEROLEHAN</td>
                    <td style="text-align: center;"><?php echo $total_ind_all; ?></td>
                    <td style="text-align: center;"><?php echo $total_skor_maks; ?></td>
                    <td style="text-align: center; color: #0f172a;"><?php echo $total_skor_riil; ?></td>
                    <td style="text-align: center; color: #0f172a;"><?php echo $nkkm; ?>%</td>
                </tr>
            </tfoot>
        </table>

        <!-- HASIL AKHIR & PREDIKAT -->
        <div class="score-summary-box">
            <div>
                <div style="font-size: 0.75rem; font-weight: 700; color: #1e40af; text-transform: uppercase;">
                    Nilai Kinerja Kepala Madrasah (NKKM):
                </div>
                <div class="score-summary-val"><?php echo $nkkm; ?> / 100</div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.75rem; font-weight: 700; color: #1e40af; text-transform: uppercase;">
                    Predikat Hasil Penilaian:
                </div>
                <div style="font-size: 1.25rem; font-weight: 900; color: #1e3a8a; text-transform: uppercase;">
                    <?php echo $predikat; ?>
                </div>
            </div>
        </div>

        <!-- PERNYATAAN BERITA ACARA -->
        <div style="font-size: 8.5pt; text-align: justify; line-height: 1.4; margin-bottom: 10px;">
            Berdasarkan hasil telaah dokumen bukti fisik, wawancara, observasi lingkungan, dan konfirmasi data SIM/EMIS pada hari ini, Tim Penilai Kinerja Kepala Madrasah menyatakan bahwa seluruh bukti yang diajukan telah diverifikasi keabsahannya dan Kepala Madrasah yang bersangkutan berhak memperoleh predikat kinerja sebagaimana tercantum di atas.
        </div>

        <!-- TANDA TANGAN 3 PIHAK -->
        <div style="page-break-inside: avoid; margin-top: 10px;">
            <!-- Tanggal & Lokasi Kanan Atas -->
            <div style="text-align: right; font-size: 8.5pt; margin-bottom: 4px; padding-right: 15px;">
                <?php echo htmlspecialchars($kabupaten); ?>, <?php echo $tgl_indo; ?>
            </div>

            <div style="text-align: center; font-size: 8.5pt; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                Tim Penilai Kinerja Kepala Madrasah (Pengawas),
            </div>

            <!-- BARIS 1: PENILAI 1 (KIRI) & PENILAI 2 (KANAN) -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 18px;">
                <!-- Penilai 1 (Kiri) -->
                <div style="width: 46%; text-align: center; font-size: 8.5pt;">
                    <div style="font-weight: 600; color: #334155; margin-bottom: 2px;">Penilai 1 (Ketua Tim Asesor),</div>
                    <div style="height: 52px; display: flex; align-items: center; justify-content: center; margin-bottom: 2px;">
                        <?php if (!empty($p1_ttd)): ?>
                            <img src="<?php echo htmlspecialchars($p1_ttd); ?>" style="max-height: 50px; max-width: 130px; object-fit: contain;" alt="TTD Penilai 1">
                        <?php else: ?>
                            <div style="height: 48px;"></div>
                        <?php endif; ?>
                    </div>
                    <div class="ttd-name" style="font-size: 8.5pt;"><?php echo htmlspecialchars($p1_nama); ?></div>
                    <div style="font-size: 7.5pt; color: #64748b;">NIP. <?php echo htmlspecialchars($p1_nip); ?></div>
                </div>

                <!-- Penilai 2 (Kanan) -->
                <div style="width: 46%; text-align: center; font-size: 8.5pt;">
                    <div style="font-weight: 600; color: #334155; margin-bottom: 2px;">Penilai 2 (Anggota Tim Asesor),</div>
                    <div style="height: 52px; display: flex; align-items: center; justify-content: center; margin-bottom: 2px;">
                        <?php if (!empty($p2_ttd)): ?>
                            <img src="<?php echo htmlspecialchars($p2_ttd); ?>" style="max-height: 50px; max-width: 130px; object-fit: contain;" alt="TTD Penilai 2">
                        <?php else: ?>
                            <div style="height: 48px;"></div>
                        <?php endif; ?>
                    </div>
                    <div class="ttd-name" style="font-size: 8.5pt;"><?php echo htmlspecialchars($p2_nama); ?></div>
                    <div style="font-size: 7.5pt; color: #64748b;">NIP. <?php echo htmlspecialchars($p2_nip); ?></div>
                </div>
            </div>

            <!-- BARIS 2: KEPALA MADRASAH (TENGAH BAWAH) -->
            <div style="display: flex; justify-content: center; align-items: center;">
                <div style="width: 55%; text-align: center; font-size: 8.5pt;">
                    <div style="font-size: 8pt; color: #64748b; margin-bottom: 1px;">Mengetahui,</div>
                    <div style="font-weight: 600; color: #334155; margin-bottom: 2px;">Kepala Madrasah yang Dinilai,</div>
                    <div style="height: 48px; display: flex; align-items: center; justify-content: center; margin-bottom: 2px;">
                        <div style="height: 45px;"></div>
                    </div>
                    <div class="ttd-name" style="font-size: 8.5pt;"><?php echo htmlspecialchars($kepala_nama); ?></div>
                    <div style="font-size: 7.5pt; color: #64748b;">NIP. <?php echo htmlspecialchars($kepala_nip); ?></div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
