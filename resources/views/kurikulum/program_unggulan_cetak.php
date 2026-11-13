<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

use App\Core\Branding;

$brand = Branding::get();
$sekolah_nama = 'MA MIFTAHUL HUDA MATHLA\'UL ANWAR';
$nama_yayasan = 'YAYASAN PONDOK PESANTREN MIFTAHUL HUDA MATHLA\'UL ANWAR';
$logoUrl = !empty($institusi['logo']) ? '/public/uploads/logo/' . $institusi['logo'] : Branding::getInstitusiLogo();
$logoKemenag = '/public/logo_kemenag.png';

$alamatLengkap = 'Jl. Tirtomoyo, No. 083, Margorahayu, Ds. Tambak Jaya, Kec. Way Tenong, Kab. Lampung Barat, Prov. Lampung';
if (!empty($institusi['alamat']) && !empty($institusi['desa'])) {
    $alamatLengkap = 'Jl. Tirtomoyo, No. 083, Margorahayu, Ds. ' . $institusi['desa'] . ', Kec. ' . ($institusi['kecamatan'] ?? 'Way Tenong') . ', Kab. ' . ($institusi['kota'] ?? 'Lampung Barat') . ', Prov. ' . ($institusi['provinsi'] ?? 'Lampung');
}

$nsm = '111218040009';
$npsn = '10648392';
$webUrl = !empty($institusi['website']) ? $institusi['website'] : 'masmifhda.sch.id';
$emailAddr = 'masmifhda083@gmail.com';
$telepon = '081938240819';
$kecamatan = !empty($institusi['kecamatan']) ? $institusi['kecamatan'] : 'Way Tenong';

$cfg = $cfgProg ?? [];
$nomor_sk = $cfg['nomor_sk'] ?? '421.3/088/MAS-MH/SK-PROG/VII/2026';
$tanggal_sk = $cfg['tanggal_sk'] ?? '2026-07-15';
$tahun_ajaran = $cfg['tahun_ajaran'] ?? '2026/2027';
$semester = $cfg['semester'] ?? 'Ganjil';

$kamad_nama = !empty($cfg['kamad_nama']) && $cfg['kamad_nama'] !== 'Drs. Hamim Hasyim' ? $cfg['kamad_nama'] : 'AHMAD RIFA\'I, M.Pd';
$kamad_nip = $cfg['kamad_nip'] ?? '-';
$waka_nama = !empty($cfg['waka_nama']) && $cfg['waka_nama'] !== 'M AZIZ SULAIMAN, S.Pd' ? $cfg['waka_nama'] : 'Drs. Hamim Hasyim';
$waka_nip = $cfg['waka_nip'] ?? '-';

$kord_pesantren_nama = $cfg['kord_pesantren_nama'] ?? 'SAIPUDIN AZIZ, S.Pd';
$pembina_rohis_nama = $cfg['pembina_rohis_nama'] ?? 'DIDI HARTONO, S.Pd.I';
$kord_lifeskill_nama = $cfg['kord_lifeskill_nama'] ?? 'INDAH YUNITASARI, S.Pd';
$kord_digital_nama = $cfg['kord_digital_nama'] ?? 'SYAIFUL BAHRI, S.Pd';

$bulanIndo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$tgl_obj = strtotime($tanggal_sk);
$tgl_sk_formatted = date('j', $tgl_obj) . ' ' . $bulanIndo[(int)date('n', $tgl_obj)] . ' ' . date('Y', $tgl_obj);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Pedoman 3 Program Unggulan - <?= htmlspecialchars($sekolah_nama) ?></title>
    <link rel="icon" type="image/png" href="<?= htmlspecialchars($brand['favicon']) ?>">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 portrait;
            margin: 0; /* Container handles padding precisely to eliminate blank overflow sheets */
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10.5pt;
            line-height: 1.45;
            color: #0f172a;
            background: #cbd5e1;
            margin: 0;
            padding: 25px 10px;
        }

        /* Standard 1-Page A4 Sheet Container */
        .container {
            width: 210mm;
            max-width: 210mm;
            height: 297mm;
            max-height: 297mm;
            min-height: 297mm;
            margin: 0 auto 25px auto;
            background: #ffffff;
            padding: 1.6cm 2cm 1.5cm 2.2cm;
            box-sizing: border-box;
            box-shadow: 0 10px 30px rgba(0,0,0,0.18);
            position: relative;
            overflow: hidden;
            page-break-after: always;
            break-after: page;
        }

        @media print {
            .no-print { display: none !important; }
            html, body {
                width: 210mm !important;
                height: auto !important;
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .container {
                width: 210mm !important;
                max-width: 210mm !important;
                height: 296mm !important;
                max-height: 296mm !important;
                min-height: 296mm !important;
                padding: 1.5cm 2cm 1.4cm 2.2cm !important;
                margin: 0 !important;
                box-shadow: none !important;
                box-sizing: border-box !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                page-break-after: always !important;
                break-after: page !important;
                overflow: hidden !important;
            }
            .container:last-child {
                page-break-after: auto !important;
                break-after: auto !important;
            }
            .cover-container {
                padding: 1cm 1.4cm !important;
                height: 296mm !important;
                max-height: 296mm !important;
            }
        }

        /* =====================================================================
           LUXURY & PRESTIGIOUS COVER STYLES
           ===================================================================== */
        .cover-container {
            padding: 1.1cm 1.5cm;
        }
        .cover-outer-border {
            border: 3px solid #064e3b;
            padding: 5px;
            height: 100%;
            max-height: 100%;
            box-sizing: border-box;
            position: relative;
            background: #ffffff;
        }
        .cover-inner-border {
            border: 1.5px solid #b45309;
            height: 100%;
            padding: 20px 20px 16px 20px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
            position: relative;
            background: radial-gradient(circle at 50% 25%, #ffffff 0%, #fafffc 60%, #f0fdf4 100%);
        }
        /* Corner Ornaments */
        .corner-ornament {
            position: absolute;
            width: 26px;
            height: 26px;
            pointer-events: none;
        }
        .corner-tl { top: -2px; left: -2px; border-top: 4px solid #b45309; border-left: 4px solid #b45309; }
        .corner-tr { top: -2px; right: -2px; border-top: 4px solid #b45309; border-right: 4px solid #b45309; }
        .corner-bl { bottom: -2px; left: -2px; border-bottom: 4px solid #b45309; border-left: 4px solid #b45309; }
        .corner-br { bottom: -2px; right: -2px; border-bottom: 4px solid #b45309; border-right: 4px solid #b45309; }

        .cover-inst-header {
            text-transform: uppercase;
            line-height: 1.35;
        }
        .cover-inst-sub {
            font-size: 8.5pt;
            font-weight: 700;
            letter-spacing: 1.2px;
            color: #475569;
            margin-bottom: 2px;
        }
        .cover-inst-yayasan {
            font-size: 9.5pt;
            font-weight: 800;
            letter-spacing: 0.8px;
            color: #1e293b;
            margin-bottom: 2px;
        }
        .cover-inst-school {
            font-size: 13pt;
            font-weight: 900;
            letter-spacing: 0.8px;
            color: #064e3b;
            padding-bottom: 6px;
            border-bottom: 2px solid #b45309;
            display: inline-block;
            margin: 0 auto;
        }

        /* Crest Badge Logo */
        .cover-logo-seal {
            margin: 12px auto 8px auto;
            position: relative;
            display: inline-block;
        }
        .cover-logo-seal::before {
            content: '';
            position: absolute;
            top: -6px; left: -6px; right: -6px; bottom: -6px;
            border-radius: 50%;
            border: 2px dashed #b45309;
            opacity: 0.6;
        }
        .cover-logo-img {
            width: 95px;
            height: 95px;
            object-fit: contain;
            border-radius: 50%;
            background: #ffffff;
            box-shadow: 0 5px 15px rgba(6, 78, 59, 0.22);
            padding: 6px;
            border: 2.5px solid #064e3b;
        }

        /* Title Area */
        .cover-main-title {
            margin: 4px 0;
        }
        .cover-tag-pill {
            display: inline-block;
            background: #064e3b;
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 8pt;
            font-weight: 800;
            letter-spacing: 2px;
            padding: 3px 16px;
            border-radius: 999px;
            text-transform: uppercase;
            box-shadow: 0 3px 8px rgba(6,78,59,0.3);
            margin-bottom: 8px;
        }
        .cover-h1 {
            font-family: 'Cinzel', 'Times New Roman', serif;
            font-size: 19pt;
            font-weight: 900;
            color: #064e3b;
            letter-spacing: 1.2px;
            margin: 0 0 5px 0;
            text-transform: uppercase;
            line-height: 1.22;
        }
        .cover-h2 {
            font-size: 12pt;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: 0.8px;
            margin: 0 0 10px 0;
            text-transform: uppercase;
        }

        /* 3 Badges Program */
        .cover-pillars-wrap {
            display: flex;
            flex-direction: column;
            gap: 6px;
            max-width: 460px;
            margin: 0 auto 8px auto;
        }
        .cover-pillar-item {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-left: 4px solid #059669;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 8.5pt;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .cover-pillar-item:nth-child(2) { border-left-color: #d97706; }
        .cover-pillar-item:nth-child(3) { border-left-color: #0284c7; }

        .cover-year-badge {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 10.5pt;
            font-weight: 800;
            color: #064e3b;
            letter-spacing: 1px;
            margin-top: 4px;
            margin-bottom: 5px;
        }
        .cover-quote {
            font-size: 8.5pt;
            font-style: italic;
            color: #334155;
            max-width: 90%;
            margin: 0 auto;
            line-height: 1.35;
            padding: 5px 10px;
            background: rgba(255,255,255,0.7);
            border-radius: 6px;
            border: 1px dashed #cbd5e1;
        }

        .cover-footer-block {
            border-top: 2px solid #b45309;
            padding-top: 8px;
            text-transform: uppercase;
            line-height: 1.3;
        }
        .cover-footer-team {
            font-size: 9pt;
            font-weight: 800;
            color: #064e3b;
            letter-spacing: 0.6px;
        }
        .cover-footer-addr {
            font-size: 8pt;
            color: #475569;
            font-weight: 700;
            margin-top: 2px;
        }

        /* =====================================================================
           OFFICIAL KOP SURAT STYLES (DUAL LOGO KEMENAG & MADRASAH)
           ===================================================================== */
        .kop-surat-resmi {
            border-bottom: 3px double #000;
            padding-bottom: 4px;
            margin-bottom: 16px;
        }
        .kop-table-wrap {
            width: 100%;
            border-collapse: collapse;
        }
        .kop-table-wrap td {
            vertical-align: middle;
        }
        .kop-logo-cell {
            width: 62px;
            text-align: center;
        }
        .kop-logo-cell img {
            width: 55px;
            height: 55px;
            object-fit: contain;
        }
        .kop-text-cell {
            text-align: center;
            padding: 0 6px;
        }
        .kop-title-yayasan {
            font-size: 8.2pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            margin: 0;
            color: #000;
            line-height: 1.25;
            white-space: nowrap;
        }
        .kop-title-sekolah {
            font-size: 11pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin: 1.5px 0 1px 0;
            color: #000;
            line-height: 1.2;
            white-space: nowrap;
        }
        .kop-meta-resmi {
            font-size: 7.2pt;
            font-weight: 700;
            margin: 1px 0;
            color: #000;
            letter-spacing: 0.2px;
            white-space: nowrap;
        }
        .kop-alamat-resmi {
            font-size: 6.8pt;
            margin-top: 1px;
            line-height: 1.25;
            color: #111;
        }

        /* =====================================================================
           BODY TYPOGRAPHY & DATA TABLES
           ===================================================================== */
        .doc-title-box {
            text-align: center;
            margin-bottom: 16px;
        }
        .doc-title-box h1 {
            font-size: 12pt;
            font-weight: 900;
            text-transform: uppercase;
            margin: 0;
            text-decoration: underline;
            color: #000;
        }
        .doc-title-box .sub-title {
            font-size: 10pt;
            font-weight: 800;
            margin-top: 4px;
            text-transform: uppercase;
            color: #1e293b;
        }
        .doc-title-box .sk-no {
            font-size: 9.5pt;
            font-weight: 700;
            margin-top: 3px;
        }

        h2.bab-head {
            font-size: 10.5pt;
            font-weight: 900;
            text-transform: uppercase;
            border-bottom: 2px solid #064e3b;
            padding-bottom: 3px;
            margin-top: 14px;
            margin-bottom: 10px;
            color: #064e3b;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        h3.sub-head {
            font-size: 10pt;
            font-weight: 800;
            margin-top: 12px;
            margin-bottom: 4px;
            color: #0f172a;
        }

        /* NUMBERING TEXT INDENTATION STYLES (SESUAI ARAHAN USER: TEKS AGAK MASUK) */
        .section-body {
            margin-left: 20px;
            margin-bottom: 12px;
        }
        .section-body p {
            text-align: justify;
            text-indent: 20px;
            margin-top: 3px;
            margin-bottom: 6px;
            line-height: 1.5;
        }
        .section-body ul, .section-body ol {
            padding-left: 18px;
            margin-top: 3px;
            margin-bottom: 6px;
            line-height: 1.5;
        }
        .section-body li {
            text-align: justify;
            margin-bottom: 4px;
        }

        table.formal-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0 10px 0;
            font-size: 9pt;
        }
        table.formal-table th, table.formal-table td {
            border: 1px solid #000;
            padding: 4px 7px;
            vertical-align: top;
        }
        table.formal-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 8.5pt;
        }

        .ttd-grid-4 {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .ttd-grid-4 td {
            vertical-align: top;
            text-align: center;
            font-size: 9.5pt;
            width: 50%;
        }

        /* Floating Top Bar for Print */
        .floating-bar {
            position: fixed;
            top: 15px;
            right: 20px;
            z-index: 99999;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 8px 16px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.18);
            display: flex;
            gap: 10px;
            align-items: center;
            font-family: sans-serif;
        }
        .btn-print {
            background: #059669;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print:hover { background: #047857; }
        .btn-back {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
            padding: 8px 14px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

<!-- FLOATING CONTROLS -->
<div class="floating-bar no-print">
    <button onclick="window.print()" class="btn-print">
        🖨️ Cetak Dokumen PDF (A4)
    </button>
    <a href="/kurikulum/program-unggulan" class="btn-back">
        ⬅ Kembali ke Halaman Utama
    </a>
</div>

<!-- ========================================================================= -->
<!-- HALAMAN 1: COVER DEPAN LUXURY & PRESTISIUS (PAS 1 LEMBAR A4)              -->
<!-- ========================================================================= -->
<div class="container cover-container">
    <div class="cover-outer-border">
        <div class="corner-ornament corner-tl"></div>
        <div class="corner-ornament corner-tr"></div>
        <div class="corner-ornament corner-bl"></div>
        <div class="corner-ornament corner-br"></div>

        <div class="cover-inner-border">
            <!-- Header Kementerian & Yayasan -->
            <div class="cover-inst-header">
                <div class="cover-inst-sub">KEMENTERIAN AGAMA REPUBLIK INDONESIA</div>
                <div class="cover-inst-sub">KANTOR KEMENTERIAN AGAMA KABUPATEN LAMPUNG BARAT</div>
                <div class="cover-inst-yayasan"><?= htmlspecialchars($nama_yayasan) ?></div>
                <div class="cover-inst-school"><?= htmlspecialchars($sekolah_nama) ?></div>
            </div>

            <!-- Logo Seal -->
            <div class="cover-logo-seal">
                <img src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo Madrasah" class="cover-logo-img">
            </div>

            <!-- Judul Utama & Blueprint -->
            <div class="cover-main-title">
                <div class="cover-tag-pill">BLUEPRINT & PEDOMAN MUTU KURIKULUM</div>
                <h1 class="cover-h1">TIGA PROGRAM UNGGULAN</h1>
                <div class="cover-h2"><?= htmlspecialchars($sekolah_nama) ?></div>

                <div class="cover-pillars-wrap">
                    <div class="cover-pillar-item">
                        <span>1. MADRASAH BERBASIS PESANTREN & ROHIS</span>
                        <span style="color:#059669;">★ IMTAQ</span>
                    </div>
                    <div class="cover-pillar-item">
                        <span>2. MADRASAH BERBASIS LIFE SKILL (VOKASI)</span>
                        <span style="color:#d97706;">★ MANDIRI</span>
                    </div>
                    <div class="cover-pillar-item">
                        <span>3. MADRASAH BERBASIS DIGITAL & AI 5.0</span>
                        <span style="color:#0284c7;">★ SMART</span>
                    </div>
                </div>

                <div class="cover-year-badge">
                    TAHUN PELAJARAN <?= htmlspecialchars($tahun_ajaran) ?>
                </div>

                <div class="cover-quote">
                    "Harmonisasi Pendidikan Karakter Qur'ani, Kemandirian Wirausaha Santri melalui Kejuruan Vokasi Terapan, serta Penguasaan Teknologi Cerdas Terintegrasi Menuju Era AI 5.0"
                </div>
            </div>

            <!-- Penerbit Dokumen -->
            <div class="cover-footer-block">
                <div class="cover-footer-team">TIM PENGEMBANG KURIKULUM & PENJAMINAN MUTU PENDIDIKAN</div>
                <div class="cover-footer-team"><?= htmlspecialchars($sekolah_nama) ?></div>
                <div class="cover-footer-addr">KECAMATAN WAY TENONG, KABUPATEN LAMPUNG BARAT, PROVINSI LAMPUNG &bull; TAHUN 2026</div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- HALAMAN 2: LEMBAR PENGESAHAN RESMI (2 TTD: KAMAD & WAKAKUR)               -->
<!-- ========================================================================= -->
<div class="container">
    <!-- KOP SURAT RESMI 2 LOGO -->
    <div class="kop-surat-resmi">
        <table class="kop-table-wrap">
            <tr>
                <td class="kop-logo-cell">
                    <img src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo Madrasah">
                </td>
                <td class="kop-text-cell">
                    <div class="kop-title-yayasan"><?= htmlspecialchars($nama_yayasan) ?></div>
                    <div class="kop-title-sekolah"><?= htmlspecialchars($sekolah_nama) ?></div>
                    <div class="kop-meta-resmi">NSM : <?= htmlspecialchars($nsm) ?> &nbsp;|&nbsp; NPSN : <?= htmlspecialchars($npsn) ?> &nbsp;|&nbsp; STATUS : TERAKREDITASI B</div>
                    <div class="kop-alamat-resmi">
                        <?= htmlspecialchars($alamatLengkap) ?><br>
                        Website: <?= htmlspecialchars($webUrl) ?> &nbsp;|&nbsp; Email: <?= htmlspecialchars($emailAddr) ?> &nbsp;|&nbsp; Telp: <?= htmlspecialchars($telepon) ?>
                    </div>
                </td>
                <td class="kop-logo-cell">
                    <img src="<?= htmlspecialchars($logoKemenag) ?>" alt="Logo Kemenag" onerror="this.style.visibility='hidden'">
                </td>
            </tr>
        </table>
    </div>

    <div class="doc-title-box">
        <h1>LEMBAR PENGESAHAN</h1>
        <div class="sub-title">DOKUMEN PEDOMAN & BLUEPRINT TIGA PROGRAM UNGGULAN<br><?= htmlspecialchars($sekolah_nama) ?><br>TAHUN PELAJARAN <?= htmlspecialchars($tahun_ajaran) ?></div>
    </div>

    <p style="text-indent: 30px; text-align: justify; line-height: 1.6; font-size: 10.2pt;">
        Setelah melalui proses kajian komprehensif, penyelarasan kurikulum operasional madrasah, dan rapat pleno bersama Tim Pengembang Kurikulum serta Pimpinan Madrasah, maka dokumen <strong>Buku Pedoman dan Blueprint Tiga Program Unggulan (Madrasah Berbasis Pesantren & ROHIS, Madrasah Berbasis Life Skill, dan Madrasah Berbasis Digital)</strong> ini disahkan secara resmi untuk diberlakukan sebagai panduan operasional penyelenggaraan KBM, pembinaan kesiswaan, penyediaan fasilitas vokasi, dan tolok ukur penjaminan mutu di <?= htmlspecialchars($sekolah_nama) ?> pada Tahun Pelajaran <?= htmlspecialchars($tahun_ajaran) ?>.
    </p>

    <!-- TANDA TANGAN RESMI 2 PIHAK: WAKA KURIKULUM (PENYUSUN) & KEPALA MADRASAH (MENGESAHKAN) -->
    <div style="margin-top: 55px;">
        <table style="width: 100%; text-align: center; font-size: 10.5pt; line-height: 1.55;">
            <tr>
                <td style="width: 50%; vertical-align: top; padding: 0 25px;">
                    Penyusun:<br>
                    <strong>Ketua Tim Pengembang Kurikulum</strong><br>
                    Waka Kurikulum<br><br><br><br><br><br>
                    <strong style="text-decoration: underline; font-size: 11pt;"><?= htmlspecialchars($waka_nama) ?></strong>
                </td>
                <td style="width: 50%; vertical-align: top; padding: 0 25px;">
                    Ditetapkan di : <?= htmlspecialchars($kecamatan) ?><br>
                    Pada tanggal : <?= htmlspecialchars($tgl_sk_formatted) ?><br>
                    <strong>Kepala Madrasah Aliyah Miftahul Huda</strong><br><br><br><br><br><br>
                    <strong style="text-decoration: underline; font-size: 11pt;"><?= htmlspecialchars($kamad_nama) ?></strong>
                </td>
            </tr>
        </table>
    </div>
</div>

<!-- ========================================================================= -->
<!-- HALAMAN 3: SURAT KEPUTUSAN (SK) PENETAPAN RESMI                           -->
<!-- ========================================================================= -->
<div class="container">
    <div class="kop-surat-resmi">
        <table class="kop-table-wrap">
            <tr>
                <td class="kop-logo-cell">
                    <img src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo Madrasah">
                </td>
                <td class="kop-text-cell">
                    <div class="kop-title-yayasan"><?= htmlspecialchars($nama_yayasan) ?></div>
                    <div class="kop-title-sekolah"><?= htmlspecialchars($sekolah_nama) ?></div>
                    <div class="kop-meta-resmi">NSM : <?= htmlspecialchars($nsm) ?> &nbsp;|&nbsp; NPSN : <?= htmlspecialchars($npsn) ?> &nbsp;|&nbsp; STATUS : TERAKREDITASI B</div>
                    <div class="kop-alamat-resmi">
                        <?= htmlspecialchars($alamatLengkap) ?><br>
                        Website: <?= htmlspecialchars($webUrl) ?> &nbsp;|&nbsp; Email: <?= htmlspecialchars($emailAddr) ?> &nbsp;|&nbsp; Telp: <?= htmlspecialchars($telepon) ?>
                    </div>
                </td>
                <td class="kop-logo-cell">
                    <img src="<?= htmlspecialchars($logoKemenag) ?>" alt="Logo Kemenag" onerror="this.style.visibility='hidden'">
                </td>
            </tr>
        </table>
    </div>

    <div class="doc-title-box">
        <h1>SURAT KEPUTUSAN KEPALA MADRASAH ALIYAH MIFTAHUL HUDA</h1>
        <div class="sk-no">Nomor: <?= htmlspecialchars($nomor_sk) ?></div>
        <div class="sub-title">TENTANG<br>PENETAPAN TIGA PROGRAM UNGGULAN MADRASAH<br>TAHUN PELAJARAN <?= htmlspecialchars($tahun_ajaran) ?></div>
    </div>

    <table style="width: 100%; border-collapse: collapse; font-size: 9pt; margin-bottom: 6px;">
        <tr>
            <td style="width: 15%; vertical-align: top; font-weight: bold;">Menimbang</td>
            <td style="width: 3%; vertical-align: top;">:</td>
            <td style="width: 82%; text-align: justify;">
                <ol style="margin: 0; padding-left: 14px;">
                    <li>Bahwa dalam rangka menjawab tantangan zaman dan membekali lulusan dengan imtaq kokoh, kemandirian ekonomi, serta literasi teknologi modern, diperlukan program terarah dan berkesinambungan;</li>
                    <li>Bahwa perpaduan antara karakter kepesantrenan, pembinaan ROHIS, kecakapan vokasi life skill, dan otomasi madrasah digital merupakan pilar keunggulan MAS Miftahul Huda;</li>
                    <li>Bahwa berdasarkan pertimbangan huruf a dan b, perlu menetapkan Surat Keputusan Kepala Madrasah tentang Tiga Program Unggulan TP <?= htmlspecialchars($tahun_ajaran) ?>.</li>
                </ol>
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: top; padding-top: 3px;">Mengingat</td>
            <td style="vertical-align: top; padding-top: 3px;">:</td>
            <td style="text-align: justify; padding-top: 3px;">
                <ol style="margin: 0; padding-left: 14px;">
                    <li>Undang-Undang RI Nomor 20 Tahun 2003 tentang Sistem Pendidikan Nasional;</li>
                    <li>Undang-Undang RI Nomor 18 Tahun 2019 tentang Pesantren;</li>
                    <li>KMA Nomor 347 Tahun 2022 tentang Pedoman Implementasi Kurikulum Merdeka pada Madrasah;</li>
                    <li>KMA Nomor 450 Tahun 2024 tentang Pedoman Implementasi Kurikulum Madrasah;</li>
                    <li>Rencana Kerja Tahunan dan Kurikulum Operasional Madrasah (KOM) <?= htmlspecialchars($sekolah_nama) ?>.</li>
                </ol>
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: top; padding-top: 3px;">Memutuskan</td>
            <td style="vertical-align: top; padding-top: 3px;">:</td>
            <td style="padding-top: 3px; font-weight: bold;">
                KEPUTUSAN KEPALA MADRASAH ALIYAH MIFTAHUL HUDA TENTANG PENETAPAN TIGA PROGRAM UNGGULAN TAHUN PELAJARAN <?= htmlspecialchars($tahun_ajaran) ?>.
            </td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse; font-size: 9pt;">
        <tr>
            <td style="width: 15%; vertical-align: top; font-weight: bold;">KESATU</td>
            <td style="width: 3%; vertical-align: top;">:</td>
            <td style="text-align: justify;">
                Menetapkan Tiga Program Unggulan Resmi <?= htmlspecialchars($sekolah_nama) ?> yaitu: <strong>(1) Madrasah Berbasis Pesantren & ROHIS</strong>, <strong>(2) Madrasah Berbasis Life Skill</strong>, dan <strong>(3) Madrasah Berbasis Digital</strong>.
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: top; padding-top: 2px;">KEDUA</td>
            <td style="vertical-align: top; padding-top: 2px;">:</td>
            <td style="text-align: justify; padding-top: 2px;">
                Menunjuk koordinator dan pembina pelaksanaan program: Koordinator Pesantren (<strong><?= htmlspecialchars($kord_pesantren_nama) ?></strong>), Pembina ROHIS (<strong><?= htmlspecialchars($pembina_rohis_nama) ?></strong>), Koordinator Life Skill (<strong><?= htmlspecialchars($kord_lifeskill_nama) ?></strong>), dan Koordinator Digital & IT (<strong><?= htmlspecialchars($kord_digital_nama) ?></strong>).
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: top; padding-top: 2px;">KETIGA</td>
            <td style="vertical-align: top; padding-top: 2px;">:</td>
            <td style="text-align: justify; padding-top: 2px;">
                Segala biaya yang timbul akibat pelaksanaan keputusan ini dibebankan pada anggaran madrasah yang relevan dan bantuan yang sah tidak mengikat.
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: top; padding-top: 2px;">KEEMPAT</td>
            <td style="vertical-align: top; padding-top: 2px;">:</td>
            <td style="text-align: justify; padding-top: 2px;">
                Keputusan ini mulai berlaku sejak tanggal ditetapkan, dan apabila di kemudian hari terdapat kekeliruan akan diperbaiki sebagaimana mestinya.
            </td>
        </tr>
    </table>

    <div style="margin-top: 20px; float: right; width: 45%; text-align: center; font-size: 9.8pt;">
        Ditetapkan di : <?= htmlspecialchars($kecamatan) ?><br>
        Pada tanggal : <?= htmlspecialchars($tgl_sk_formatted) ?><br>
        <strong>Kepala Madrasah Aliyah Miftahul Huda</strong><br><br><br><br><br>
        <strong style="text-decoration: underline; font-size: 10.5pt;"><?= htmlspecialchars($kamad_nama) ?></strong>
    </div>
    <div style="clear: both;"></div>
</div>

<!-- ========================================================================= -->
<!-- HALAMAN 4: KATA PENGANTAR RESMI KEPALA MADRASAH (LEMBAR MANDIRI)          -->
<!-- ========================================================================= -->
<div class="container">
    <h2 class="bab-head"><span>KATA PENGANTAR KEPALA MADRASAH</span> <span>MAS MIFHDA</span></h2>
    
    <div style="margin-top: 25px; line-height: 1.7; font-size: 10.5pt;">
        <p style="text-indent: 35px; text-align: justify; margin-bottom: 14px;">
            Puji syukur senantiasa kita panjatkan ke hadirat Allah SWT, Rabb semesta alam, yang telah melimpahkan taufiq, hidayah, serta kekuatan lahir dan batin kepada segenap civitas akademika <?= htmlspecialchars($sekolah_nama) ?>, sehingga Dokumen Buku Pedoman & Blueprint <strong>Tiga Program Unggulan</strong> Tahun Pelajaran <?= htmlspecialchars($tahun_ajaran) ?> ini dapat tersusun dan diterbitkan dengan baik.
        </p>
        <p style="text-indent: 35px; text-align: justify; margin-bottom: 14px;">
            Dinamika peradaban abad ke-21 menuntut lembaga pendidikan madrasah untuk melakukan reorientasi kurikulum yang kontekstual dan futuristik. Madrasah tidak boleh hanya berpuas diri menjadi benteng penjaga moral keagamaan (imtaq), melainkan harus tampil di garda terdepan melahirkan generasi yang memiliki kemandirian finansial melalui kecakapan vokasi praktis (life skill), serta mahir menaklukkan disrupsi teknologi melalui penguasaan literasi digital dan kecerdasan buatan (artificial intelligence).
        </p>
        <p style="text-indent: 35px; text-align: justify; margin-bottom: 14px;">
            Melalui Buku Pedoman ini, MAS Miftahul Huda menetapkan standar arah kebijakan mutu pembelajaran tiga pilar terpadu: <strong>(1) Madrasah Berbasis Pesantren & ROHIS</strong>, <strong>(2) Madrasah Berbasis Life Skill</strong>, dan <strong>(3) Madrasah Berbasis Digital (MASMIFHDA SYSTEM)</strong>. Ketiga pilar ini saling menguatkan, mengakar pada tradisi keilmuan Islam Ahlussunnah wal Jama'ah, namun berbuah nyata dalam kecakapan hidup santri di tengah masyarakat.
        </p>
        <p style="text-indent: 35px; text-align: justify; margin-bottom: 14px;">
            Kami menyampaikan apresiasi dan penghargaan setinggi-tingginya kepada seluruh Tim Pengembang Kurikulum, Dewan Guru, Pengurus Komite Madrasah, serta Pengawas Pembina Kementerian Agama Kabupaten Lampung Barat atas bimbingan dan dedikasinya. Semoga Allah SWT memberkahi setiap langkah ikhtiar kita bersama.
        </p>
    </div>

    <div style="margin-top: 45px; float: right; width: 45%; text-align: center; font-size: 10pt;">
        <?= htmlspecialchars($kecamatan) ?>, <?= htmlspecialchars($tgl_sk_formatted) ?><br>
        <strong>Kepala Madrasah,</strong><br><br><br><br><br>
        <strong style="text-decoration: underline; font-size: 10.5pt;"><?= htmlspecialchars($kamad_nama) ?></strong>
    </div>
    <div style="clear: both;"></div>
</div>

<!-- ========================================================================= -->
<!-- HALAMAN 5: DAFTAR ISI BLUEPRINT PEDOMAN (LEMBAR MANDIRI)                  -->
<!-- ========================================================================= -->
<div class="container">
    <h2 class="bab-head"><span>DAFTAR ISI BLUEPRINT PEDOMAN MUTU</span> <span>SISTEMATIKA</span></h2>

    <div style="margin-top: 20px;">
        <table style="width: 100%; border-collapse: collapse; font-size: 10pt; line-height: 2.1;">
            <tr>
                <td><strong>HALAMAN JUDUL / COVER DEPAN RESMI</strong></td>
                <td style="text-align: right; width: 80px;"><strong>Hal. 1</strong></td>
            </tr>
            <tr>
                <td><strong>LEMBAR PENGESAHAN RESMI (4 TANDA TANGAN)</strong></td>
                <td style="text-align: right;"><strong>Hal. 2</strong></td>
            </tr>
            <tr>
                <td><strong>SURAT KEPUTUSAN (SK) KEPALA MADRASAH</strong></td>
                <td style="text-align: right;"><strong>Hal. 3</strong></td>
            </tr>
            <tr>
                <td><strong>KATA PENGANTAR KEPALA MADRASAH</strong></td>
                <td style="text-align: right;"><strong>Hal. 4</strong></td>
            </tr>
            <tr>
                <td><strong>DAFTAR ISI BLUEPRINT PEDOMAN MUTU</strong></td>
                <td style="text-align: right;"><strong>Hal. 5</strong></td>
            </tr>
            <tr>
                <td><strong>BAB I &bull; PENDAHULUAN & LANDASAN STRATEGIS</strong></td>
                <td style="text-align: right;"><strong>Hal. 6</strong></td>
            </tr>
            <tr>
                <td style="padding-left: 20px; color: #334155;">1.1 Latar Belakang & Urgensi Tiga Program Unggulan</td>
                <td style="text-align: right; color: #334155;">Hal. 6</td>
            </tr>
            <tr>
                <td style="padding-left: 20px; color: #334155;">1.2 Visi, Misi, dan Tujuan Strategis Madrasah</td>
                <td style="text-align: right; color: #334155;">Hal. 6</td>
            </tr>
            <tr>
                <td style="padding-left: 20px; color: #334155;">1.3 Profil Standar Kompetensi Lulusan (SKL) Tiga Pilar</td>
                <td style="text-align: right; color: #334155;">Hal. 6</td>
            </tr>
            <tr>
                <td><strong>BAB II &bull; DESAIN PROGRAM 1: MADRASAH PESANTREN & ROHIS</strong></td>
                <td style="text-align: right;"><strong>Hal. 7</strong></td>
            </tr>
            <tr>
                <td style="padding-left: 20px; color: #334155;">2.1 Program Tahfidz & Tahsin Al-Qur'an Terpadu</td>
                <td style="text-align: right; color: #334155;">Hal. 7</td>
            </tr>
            <tr>
                <td style="padding-left: 20px; color: #334155;">2.2 Pembinaan & Kaderisasi Organisasi ROHIS Santri</td>
                <td style="text-align: right; color: #334155;">Hal. 7</td>
            </tr>
            <tr>
                <td style="padding-left: 20px; color: #334155;">2.3 Pembiasaan Amaliah Ubudiyah Harian & Munaqosyah</td>
                <td style="text-align: right; color: #334155;">Hal. 7</td>
            </tr>
            <tr>
                <td><strong>BAB III &bull; DESAIN PROGRAM 2: MADRASAH LIFE SKILL (VOKASI)</strong></td>
                <td style="text-align: right;"><strong>Hal. 8</strong></td>
            </tr>
            <tr>
                <td style="padding-left: 20px; color: #334155;">3.1 Bidang Keahlian Menjahit & Kriya Busana Muslim</td>
                <td style="text-align: right; color: #334155;">Hal. 8</td>
            </tr>
            <tr>
                <td style="padding-left: 20px; color: #334155;">3.2 Bidang Tata Boga & Kuliner Halal Santri</td>
                <td style="text-align: right; color: #334155;">Hal. 8</td>
            </tr>
            <tr>
                <td style="padding-left: 20px; color: #334155;">3.3 Bidang Teknisi Komputer & Instalasi Jaringan LAN</td>
                <td style="text-align: right; color: #334155;">Hal. 8</td>
            </tr>
            <tr>
                <td style="padding-left: 20px; color: #334155;">3.4 Expo Kewirausahaan & Gelar Karya Santri (Market Day)</td>
                <td style="text-align: right; color: #334155;">Hal. 8</td>
            </tr>
            <tr>
                <td><strong>BAB IV &bull; DESAIN PROGRAM 3: MADRASAH DIGITAL (SMART 5.0)</strong></td>
                <td style="text-align: right;"><strong>Hal. 9</strong></td>
            </tr>
            <tr>
                <td style="padding-left: 20px; color: #334155;">4.1 Ekosistem MASMIFHDA SYSTEM & Paperless Portal</td>
                <td style="text-align: right; color: #334155;">Hal. 9</td>
            </tr>
            <tr>
                <td style="padding-left: 20px; color: #334155;">4.2 Kurikulum Koding & Literasi Artificial Intelligence (AI)</td>
                <td style="text-align: right; color: #334155;">Hal. 9</td>
            </tr>
            <tr>
                <td style="padding-left: 20px; color: #334155;">4.3 Sistem CBT Mandiri 100% Paperless & E-Library</td>
                <td style="text-align: right; color: #334155;">Hal. 9</td>
            </tr>
            <tr>
                <td><strong>BAB V &bull; STRUKTUR TIM PENGELOLA & MATRIKS KPI</strong></td>
                <td style="text-align: right;"><strong>Hal. 10</strong></td>
            </tr>
            <tr>
                <td style="padding-left: 20px; color: #334155;">5.1 Susunan Tim Pengembang & Pengelola Tiga Program</td>
                <td style="text-align: right; color: #334155;">Hal. 10</td>
            </tr>
            <tr>
                <td style="padding-left: 20px; color: #334155;">5.2 Key Performance Indicators (KPI) & Penjaminan Mutu</td>
                <td style="text-align: right; color: #334155;">Hal. 10</td>
            </tr>
        </table>
    </div>
</div>

<!-- ========================================================================= -->
<!-- HALAMAN 6: BAB I PENDAHULUAN & LANDASAN STRATEGIS                         -->
<!-- ========================================================================= -->
<div class="container">
    <h2 class="bab-head"><span>BAB I &bull; PENDAHULUAN & LANDASAN STRATEGIS</span> <span>BAGIAN I</span></h2>

    <h3 class="sub-head">1.1 Latar Belakang & Urgensi Program</h3>
    <div class="section-body">
        <p>
            Pendidikan madrasah di era globalisasi menghadapi dinamika perubahan yang sangat cepat. Arus digitalisasi dan otomatisasi menuntut sumber daya manusia yang adaptif, melek teknologi, dan memiliki keterampilan aplikatif bernilai ekonomi. Di sisi lain, degradasi moral di kalangan remaja menjadi ancaman serius apabila tidak dibentengi oleh pondasi akidah dan amaliah Islam yang kokoh. MAS Miftahul Huda memformulasikan <strong>Tiga Program Unggulan</strong> sebagai langkah terobosan integratif yang memadukan keunggulan nilai kepesantrenan (Imtaq), kecakapan vokasi praktis (Life Skill), dan ekosistem madrasah cerdas tanpa kertas berbasis AI (Digital).
        </p>
    </div>

    <h3 class="sub-head">1.2 Visi, Misi, dan Tujuan Strategis Madrasah</h3>
    <div class="section-body">
        <ul>
            <li><strong>Visi:</strong> <em>"<?= htmlspecialchars($institusi['visi'] ?? 'Bertaqwa, Berakhlakul Karimah, Berprestasi Dan Berwawasan Lingkungan Hidup') ?>"</em>.</li>
            <li><strong>Misi:</strong> Menyelenggarakan pendidikan Islam berbasis nilai-nilai Qur'ani, membekali peserta didik dengan kecakapan vokasi aplikatif mandiri, serta mewujudkan tata kelola madrasah modern berbasis teknologi cerdas 5.0.</li>
            <li><strong>Tujuan Pokok:</strong> Mencapai 100% kelulusan santri yang hafal Al-Qur'an minimal Juz 30, menguasai 1 keahlian wirausaha bersertifikat, dan terampil mengoperasikan platform digital serta computational thinking.</li>
        </ul>
    </div>

    <h3 class="sub-head">1.3 Profil Standar Kompetensi Lulusan (SKL) 3 Pilar</h3>
    <div class="section-body">
        <table class="formal-table">
            <thead>
                <tr>
                    <th style="width: 28%;">Dimensi Karakter</th>
                    <th style="width: 44%;">Indikator Kemampuan & Perilaku</th>
                    <th style="width: 28%;">Bentuk Bukti Kelulusan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Pilar 1: Karakter Santri & ROHIS</strong></td>
                    <td>Hafalan Al-Qur'an mutqin Juz 30 (plus Juz 1 & 2), tertib sholat berjamaah tepat waktu, pembiasaan Dhuha & Tahajjud, santun berakhlak, dan aktif dalam dakwah kesiswaan.</td>
                    <td>Syahadah Tahfidz & Sertifikat Munaqosyah Resmi.</td>
                </tr>
                <tr>
                    <td><strong>Pilar 2: Kecakapan Hidup (Life Skill)</strong></td>
                    <td>Menguasai teknik dasar salah satu bidang: Menjahit Busana Muslim, Tata Boga Kuliner Halal, atau Teknisi Komputer & Jaringan, serta bermental wirausaha.</td>
                    <td>Portofolio Produk Nyata & Sertifikat Uji Kompetensi.</td>
                </tr>
                <tr>
                    <td><strong>Pilar 3: Literasi Digital & AI</strong></td>
                    <td>Cakap mengoperasikan MASMIFHDA SYSTEM, memahami etika kecerdasan buatan (AI), mahir membuat program web dasar, dan terbiasa ujian CBT paperless.</td>
                    <td>Sertifikat Literasi Koding & Transkrip Portofolio Digital.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ========================================================================= -->
<!-- HALAMAN 7: BAB II PROGRAM MADRASAH BERBASIS PESANTREN & ROHIS              -->
<!-- ========================================================================= -->
<div class="container">
    <h2 class="bab-head"><span>BAB II &bull; MADRASAH BERBASIS PESANTREN & ROHIS</span> <span>PROGRAM I</span></h2>

    <h3 class="sub-head">2.1 Program Tahfidz & Tahsin Al-Qur'an Terpadu</h3>
    <div class="section-body">
        <p>
            Program tahfidz dirancang untuk memastikan seluruh siswa memiliki hubungan batin dan hafalan yang kuat terhadap Kitab Suci Al-Qur'an:
        </p>
        <ul>
            <li><strong>Target Reguler (Wajib Seluruh Siswa):</strong> Minimal mutqin Juz 30 saat kelulusan kelas XII. Bagi kelas peminatan ditargetkan menguasai Juz 1 dan Juz 2.</li>
            <li><strong>Target Santri Asrama (Peminatan Khusus):</strong> Akselerasi 10 hingga 30 Juz dengan metode <em>Sabaq, Sabqi, dan Manzil</em> di bawah bimbingan ustadz/ustadzah huffadz asrama.</li>
            <li><strong>Metode & Evaluasi:</strong> Setor hafalan harian pukul 06.45–07.30 WIB, muraja'ah akbar mingguan, serta Munaqosyah Terbuka pada akhir semester.</li>
        </ul>
    </div>

    <h3 class="sub-head">2.2 Pembinaan & Kaderisasi Organisasi ROHIS (Kerohanian Islam)</h3>
    <div class="section-body">
        <p>
            Kerohanian Islam (ROHIS) menjadi motor penggerak dakwah dan pembentukan kepemimpinan religius santri di madrasah:
        </p>
        <table class="formal-table">
            <thead>
                <tr>
                    <th style="width: 26%;">Aktivitas ROHIS</th>
                    <th style="width: 44%;">Bentuk Kegiatan & Pembinaan</th>
                    <th style="width: 30%;">Penanggung Jawab</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Mentoring Keislaman</strong></td>
                    <td>Halaqah mingguan kelompok kecil berisi penguatan akidah, adab santri, fiqih thaharah dan ibadah harian.</td>
                    <td>Pembina ROHIS & Mentor Senior</td>
                </tr>
                <tr>
                    <td><strong>Kajian Keputrian</strong></td>
                    <td>Kajian fiqih an-nisa, adab pergaulan muslimah, dan keterampilan santri putri setiap hari Jumat.</td>
                    <td>Ustadzah Pembina Keputrian</td>
                </tr>
                <tr>
                    <td><strong>Kaderisasi Dai Muda</strong></td>
                    <td>Latihan khutbah Jumat, kultum santri ba'da Dhuhr, dan penerjunan dai santri ke masyarakat saat Ramadhan.</td>
                    <td>Koordinator Pesantren & Tim Bahasa</td>
                </tr>
                <tr>
                    <td><strong>Peringatan Hari Besar (PHBI)</strong></td>
                    <td>Penyelenggaraan Maulid Nabi, Isra' Mi'raj, Tahun Baru Hijriyah, pesantren kilat, dan bakti sosial santri.</td>
                    <td>Pengurus ROHIS Santri MAS Mifhda</td>
                </tr>
            </tbody>
        </table>
    </div>

    <h3 class="sub-head">2.3 Pembiasaan Amaliah Ubudiyah Harian</h3>
    <div class="section-body">
        <p>
            Membentuk kebiasaan ibadah istiqomah melalui Sholat Dhuha berjamaah setiap pagi, Sholat Dhuhur berjamaah di masjid madrasah, pembacaan Ratibul Haddad dan Asmaul Husna, pembiasaan dzikir pagi-petang, serta budaya senyum, salam, sapa, sopan, santun (5S) terhadap bapak/ibu guru dan sesama santri.
        </p>
    </div>
</div>

<!-- ========================================================================= -->
<!-- HALAMAN 8: BAB III PROGRAM MADRASAH BERBASIS LIFE SKILL                   -->
<!-- ========================================================================= -->
<div class="container">
    <h2 class="bab-head"><span>BAB III &bull; MADRASAH BERBASIS LIFE SKILL (VOKASI)</span> <span>PROGRAM II</span></h2>

    <h3 class="sub-head">3.1 Filosofi & Tujuan Kemandirian Santri</h3>
    <div class="section-body">
        <p>
            Program Life Skill MAS Miftahul Huda dirancang agar setiap lulusan tidak hanya memiliki bekal ilmu agama dan akademik, melainkan memiliki keterampilan kerja terapan (vocational skills) yang dapat mendatangkan penghasilan mandiri atau menjadi modal berwirausaha saat melanjutkan studi maupun langsung berkarya.
        </p>
    </div>

    <h3 class="sub-head">3.2 Tiga Peminatan Kejuruan Vokasi Terapan</h3>
    <div class="section-body">
        <table class="formal-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Bidang Keahlian</th>
                    <th style="width: 45%;">Silabus Pembelajaran Praktik</th>
                    <th style="width: 30%;">Output Portofolio Nyata</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>1. Menjahit & Kriya Busana Muslim</strong>
                    </td>
                    <td>
                        &bull; Pengenalan mesin jahit manual dan mesin dinamo cepat.<br>
                        &bull; Teknik pengukuran badan dan pembuatan pola dasar busana.<br>
                        &bull; Pemotongan bahan kain, obras tepi, dan jahit lurus.<br>
                        &bull; Pembuatan jilbab instan santri, gamis muslimah, baju koko, dan seragam madrasah.
                    </td>
                    <td>
                        1 stel seragam santri buatan sendiri, produk jilbab syar'i, dan sertifikat kelulusan uji kecakapan menjahit.
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>2. Tata Boga & Olahan Pangan Halal</strong>
                    </td>
                    <td>
                        &bull; Sanitasi dapur, hygiene pengolahan pangan, dan konsep halal food.<br>
                        &bull; Pembuatan aneka roti, pastry, bolu kukus, dan bakery asrama.<br>
                        &bull; Pengolahan kuliner khas Lampung Barat (kopi olahan, keripik, abon).<br>
                        &bull; Teknik pengemasan higienis vakum dan pelabelan komposisi.
                    </td>
                    <td>
                        Produk kue kemasan siap edar di kantin madrasah, market day mingguan, dan sertifikat kecakapan tata boga.
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>3. Teknisi Komputer & Jaringan LAN</strong>
                    </td>
                    <td>
                        &bull; Pengenalan arsitektur hardware PC, motherboard, processor, dan RAM.<br>
                        &bull; Praktik perakitan personal computer (PC) dan maintenance laptop.<br>
                        &bull; Instalasi sistem operasi Windows/Linux dan instalasi software aplikasi.<br>
                        &bull; Crimping kabel UTP RJ-45, konfigurasi switch, router, dan Wi-Fi hotspot.
                    </td>
                    <td>
                        Tim teknisi mandiri lab komputer madrasah & unit jasa servis laptop/PC bagi masyarakat sekitar.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <h3 class="sub-head">3.3 Expo Kewirausahaan & Gelar Karya Santri (Market Day)</h3>
    <div class="section-body">
        <p>
            Setiap akhir semester, madrasah menyelenggarakan <strong>Expo Kewirausahaan & Gelar Karya Santri</strong>. Seluruh hasil karya peserta didik dari kelas menjahit, boga, dan komputer dipamerkan, dinilai oleh tim penguji eksternal, dan dipasarkan kepada wali santri serta masyarakat umum sebagai tolok ukur daya serap ekonomi nyata.
        </p>
    </div>
</div>

<!-- ========================================================================= -->
<!-- HALAMAN 9: BAB IV PROGRAM MADRASAH BERBASIS DIGITAL                       -->
<!-- ========================================================================= -->
<div class="container">
    <h2 class="bab-head"><span>BAB IV &bull; MADRASAH BERBASIS DIGITAL & AI 5.0</span> <span>PROGRAM III</span></h2>

    <h3 class="sub-head">4.1 Transformasi Ekosistem "MASMIFHDA SYSTEM" Terpadu</h3>
    <div class="section-body">
        <p>
            MAS Miftahul Huda telah menerapkan digitalisasi penuh pada seluruh lini operasional madrasah melalui arsitektur <strong>MASMIFHDA SYSTEM (Aula PWA Platform)</strong>:
        </p>
        <ul>
            <li><strong>Portal Guru & Tendik (PWA Mobile):</strong> Manajemen presensi QR Code & deteksi wajah, jurnal mengajar real-time di kelas, upload foto KBM, e-kinerja, dan e-arsip dokumen kepegawaian.</li>
            <li><strong>Portal Siswa & E-Presensi:</strong> Akses kartu pelajar elektronik, rekap kehadiran harian, absensi kepulangan, riwayat nilai formatif, dan materi ajar mandiri.</li>
            <li><strong>Supervisi & Penjaminan Mutu:</strong> Modul supervisi kunjungan kelas digital oleh Kepala Madrasah dan Tim Supervisor dengan rekapitulasi skor otomatis 15 instrumen KBM.</li>
            <li><strong>Keuangan & Kasir Digital:</strong> Pembayaran SPP dan infaq santri dengan penerbitan kwitansi digital ber-QR Code valid serta pembukuan BKU bendahara otomatis.</li>
        </ul>
    </div>

    <h3 class="sub-head">4.2 Mata Pelajaran Koding & Literasi Artificial Intelligence (AI)</h3>
    <div class="section-body">
        <table class="formal-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Tingkat Kelas</th>
                    <th style="width: 45%;">Materi Koding & Literasi Digital</th>
                    <th style="width: 30%;">Target Capaian Pembelajaran</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Kelas X (Fase E)</strong></td>
                    <td>Logika pemrograman, computational thinking, algoritma flowchart, dasar HTML5, CSS3, dan etika berinternet sehat (cyber hygiene).</td>
                    <td>Mampu membuat halaman website profil santri dan portofolio pribadi statis.</td>
                </tr>
                <tr>
                    <td><strong>Kelas XI (Fase F)</strong></td>
                    <td>Pemrograman Web interaktif (JavaScript dasar), pengenalan database, dan dasar Artificial Intelligence (Prompt Engineering & AI Tools edukasi).</td>
                    <td>Mampu membuat formulir web terhubung database dan memanfaatkan tools AI secara etis.</td>
                </tr>
                <tr>
                    <td><strong>Kelas XII (Fase F)</strong></td>
                    <td>Dasar pemrograman Python, pengolahan data sederhana, pembuatan proyek otomasi kecil, dan literasi keamanan data digital madrasah.</td>
                    <td>Proyek mini-aplikasi digital madrasah dan portofolio koding siswa.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <h3 class="sub-head">4.3 Penyelenggaraan CBT 100% Paperless & E-Library</h3>
    <div class="section-body">
        <p>
            Seluruh Asesmen Sumatif (STS, SAS, SAT, dan Ujian Madrasah) diselenggarakan secara 100% paperless menggunakan sistem Computer Based Test (CBT) mandiri dengan fitur acak soal otomatis, pembatasan browser, dan rekapitulasi nilai instan. Koleksi buku paket teks dan modul ajar didistribusikan melalui E-Library resmi madrasah.
        </p>
    </div>
</div>

<!-- ========================================================================= -->
<!-- HALAMAN 10: BAB V STRUKTUR PENGELOLA, MATRIKS KPI & PENETAPAN             -->
<!-- ========================================================================= -->
<div class="container">
    <h2 class="bab-head"><span>BAB V &bull; STRUKTUR TIM PENGELOLA & MATRIKS KPI</span> <span>BAGIAN AKHIR</span></h2>

    <h3 class="sub-head">5.1 Susunan Tim Pengembang & Pengelola Program</h3>
    <div class="section-body">
        <table class="formal-table">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th style="width: 26%;">Jabatan Tim Program</th>
                    <th style="width: 33%;">Nama Pejabat / Guru</th>
                    <th style="width: 33%;">Fokus Tanggung Jawab</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center">1</td>
                    <td><strong>Penanggung Jawab</strong></td>
                    <td><strong><?= htmlspecialchars($kamad_nama) ?></strong><br>Kepala Madrasah</td>
                    <td>Penetapan regulasi SK, alokasi anggaran, dan koordinasi dengan Kemenag.</td>
                </tr>
                <tr>
                    <td align="center">2</td>
                    <td><strong>Ketua Pengembang</strong></td>
                    <td><strong><?= htmlspecialchars($waka_nama) ?></strong><br>Waka Kurikulum</td>
                    <td>Penyusunan kurikulum operasional 3 pilar, monitoring KBM, & evaluasi berkala.</td>
                </tr>
                <tr>
                    <td align="center">3</td>
                    <td><strong>Koordinator Pesantren</strong></td>
                    <td><strong><?= htmlspecialchars($kord_pesantren_nama) ?></strong><br>Waka Kesiswaan</td>
                    <td>Tata tertib santri, pengawasan halaqah tahfidz pagi, & munaqosyah.</td>
                </tr>
                <tr>
                    <td align="center">4</td>
                    <td><strong>Pembina ROHIS</strong></td>
                    <td><strong><?= htmlspecialchars($pembina_rohis_nama) ?></strong><br>Pembina Keagamaan</td>
                    <td>Mentoring adab, kajian keputrian, peringatan PHBI, & dai santri.</td>
                </tr>
                <tr>
                    <td align="center">5</td>
                    <td><strong>Koordinator Life Skill</strong></td>
                    <td><strong><?= htmlspecialchars($kord_lifeskill_nama) ?></strong><br>Bendahara Madrasah</td>
                    <td>Jadwal praktik vokasi menjahit, tata boga, komputer, dan expo santri.</td>
                </tr>
                <tr>
                    <td align="center">6</td>
                    <td><strong>Koordinator Digital & IT</strong></td>
                    <td><strong><?= htmlspecialchars($kord_digital_nama) ?></strong><br>Pembina Lab Komputer</td>
                    <td>Server MASMIFHDA SYSTEM, lab komputer, mapel koding, & CBT paperless.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <h3 class="sub-head">5.2 Key Performance Indicators (KPI) & Penjaminan Mutu:</h3>
    <div class="section-body">
        <ol style="font-size: 9pt; padding-left: 18px; line-height: 1.45;">
            <li><strong>KPI Pesantren & ROHIS:</strong> 100% peserta didik lulusan tuntas hafalan mutqin minimal Juz 30 dan hadir tertib dalam ibadah ubudiyah serta mentoring ROHIS.</li>
            <li><strong>KPI Life Skill Vokasi:</strong> 100% peserta didik menguasai minimal 1 cabang kejuruan vokasi yang dibuktikan dengan karya riil pada Expo Gelar Karya Santri akhir semester.</li>
            <li><strong>KPI Madrasah Digital:</strong> 100% dewan guru tertib mengisi jurnal mengajar online, 100% ujian berlangsung paperless via CBT, dan tersedianya portofolio koding siswa.</li>
        </ol>
    </div>

    <div style="margin-top: 18px; float: right; width: 45%; text-align: center; font-size: 9.8pt;">
        Ditetapkan di : <?= htmlspecialchars($kecamatan) ?><br>
        Pada tanggal : <?= htmlspecialchars($tgl_sk_formatted) ?><br>
        <strong>Kepala Madrasah Aliyah Miftahul Huda</strong><br><br><br><br><br>
        <strong style="text-decoration: underline; font-size: 10.5pt;"><?= htmlspecialchars($kamad_nama) ?></strong>
    </div>
    <div style="clear: both;"></div>
</div>

</body>
</html>
