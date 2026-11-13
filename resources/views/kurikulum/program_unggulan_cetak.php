<?php
use App\Core\Branding;

$brand = Branding::get();
$sekolah_nama = !empty($institusi['nama']) && $institusi['nama'] !== 'MADRASAH ALIYAH MIFTAHUL HUDA' && $institusi['nama'] !== 'MAS MIFTAHUL HUDA' ? $institusi['nama'] : 'MA MIFTAHUL HUDA MATHLA\'UL ANWAR';
$nama_yayasan = !empty($institusi['nama_yayasan']) ? $institusi['nama_yayasan'] : 'YAYASAN PONDOK PESANTREN MIFTAHUL HUDA MATHLA\'UL ANWAR';
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

$kamad_nama = $cfg['kamad_nama'] ?? 'Drs. Hamim Hasyim';
$kamad_nip = $cfg['kamad_nip'] ?? '-';
$waka_nama = $cfg['waka_nama'] ?? 'M AZIZ SULAIMAN, S.Pd';
$waka_nip = $cfg['waka_nip'] ?? '1804072704000002';

$kord_pesantren_nama = $cfg['kord_pesantren_nama'] ?? 'SAIPUDIN AZIZ, S.Pd';
$pembina_rohis_nama = $cfg['pembina_rohis_nama'] ?? 'DIDI HARTONO, S.Pd.I';
$kord_lifeskill_nama = $cfg['kord_lifeskill_nama'] ?? 'INDAH YUNITASARI, S.Pd';
$kord_digital_nama = $cfg['kord_digital_nama'] ?? 'SYAIFUL BAHRI, S.Pd';

$komite_nama = $cfg['komite_nama'] ?? 'H. AHMAD ROFIQ, S.Ag';
$pengawas_nama = $cfg['pengawas_nama'] ?? 'Drs. H. M. YUSUF, M.Pd.I';
$pengawas_nip = $cfg['pengawas_nip'] ?? '196805141994031002';

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
            margin: 1.5cm 2cm 1.5cm 2.2cm;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0 !important; background: #fff !important; }
            .page-break { page-break-before: always; }
            .container { box-shadow: none !important; margin: 0 !important; width: 100% !important; max-width: 100% !important; padding: 0 !important; }
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
            padding: 30px 10px;
        }
        .container {
            max-width: 210mm;
            min-height: 297mm;
            margin: 0 auto 30px auto;
            background: #ffffff;
            padding: 1.8cm 2cm 1.8cm 2.2cm;
            box-sizing: border-box;
            box-shadow: 0 10px 30px rgba(0,0,0,0.18);
            position: relative;
        }

        /* =====================================================================
           LUXURY & PRESTIGIOUS COVER STYLES
           ===================================================================== */
        .cover-outer-border {
            border: 3px solid #064e3b;
            padding: 6px;
            height: 260mm;
            box-sizing: border-box;
            position: relative;
            background: #ffffff;
        }
        .cover-inner-border {
            border: 1.5px solid #b45309;
            height: 100%;
            padding: 24px 22px;
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
            width: 28px;
            height: 28px;
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
            font-size: 9pt;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: #475569;
            margin-bottom: 2px;
        }
        .cover-inst-yayasan {
            font-size: 10pt;
            font-weight: 800;
            letter-spacing: 1px;
            color: #1e293b;
            margin-bottom: 2px;
        }
        .cover-inst-school {
            font-size: 13.5pt;
            font-weight: 900;
            letter-spacing: 1px;
            color: #064e3b;
            padding-bottom: 8px;
            border-bottom: 2px solid #b45309;
            display: inline-block;
            margin: 0 auto;
        }

        /* Crest Badge Logo */
        .cover-logo-seal {
            margin: 18px auto 14px auto;
            position: relative;
            display: inline-block;
        }
        .cover-logo-seal::before {
            content: '';
            position: absolute;
            top: -8px; left: -8px; right: -8px; bottom: -8px;
            border-radius: 50%;
            border: 2px dashed #b45309;
            opacity: 0.6;
        }
        .cover-logo-img {
            width: 105px;
            height: 105px;
            object-fit: contain;
            border-radius: 50%;
            background: #ffffff;
            box-shadow: 0 6px 18px rgba(6, 78, 59, 0.25);
            padding: 8px;
            border: 2.5px solid #064e3b;
        }

        /* Title Area */
        .cover-main-title {
            margin: 8px 0;
        }
        .cover-tag-pill {
            display: inline-block;
            background: #064e3b;
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 8.5pt;
            font-weight: 800;
            letter-spacing: 2px;
            padding: 4px 18px;
            border-radius: 999px;
            text-transform: uppercase;
            box-shadow: 0 3px 8px rgba(6,78,59,0.3);
            margin-bottom: 10px;
        }
        .cover-h1 {
            font-family: 'Cinzel', 'Times New Roman', serif;
            font-size: 21pt;
            font-weight: 900;
            color: #064e3b;
            letter-spacing: 1.5px;
            margin: 0 0 6px 0;
            text-transform: uppercase;
            line-height: 1.25;
            text-shadow: 0 1px 2px rgba(0,0,0,0.06);
        }
        .cover-h2 {
            font-size: 13pt;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: 1px;
            margin: 0 0 14px 0;
            text-transform: uppercase;
        }

        /* 3 Badges Program */
        .cover-pillars-wrap {
            display: flex;
            flex-direction: column;
            gap: 7px;
            max-width: 480px;
            margin: 0 auto 12px auto;
        }
        .cover-pillar-item {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-left: 4px solid #059669;
            padding: 5px 14px;
            border-radius: 6px;
            font-size: 9pt;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            box-shadow: 0 2px 5px rgba(0,0,0,0.04);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .cover-pillar-item:nth-child(2) { border-left-color: #d97706; }
        .cover-pillar-item:nth-child(3) { border-left-color: #0284c7; }

        .cover-year-badge {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 11pt;
            font-weight: 800;
            color: #064e3b;
            letter-spacing: 1px;
            margin-top: 6px;
            margin-bottom: 6px;
        }
        .cover-quote {
            font-size: 9pt;
            font-style: italic;
            color: #334155;
            max-width: 86%;
            margin: 0 auto;
            line-height: 1.4;
            padding: 6px 12px;
            background: rgba(255,255,255,0.7);
            border-radius: 6px;
            border: 1px dashed #cbd5e1;
        }

        .cover-footer-block {
            border-top: 2px solid #b45309;
            padding-top: 10px;
            text-transform: uppercase;
            line-height: 1.35;
        }
        .cover-footer-team {
            font-size: 9.5pt;
            font-weight: 800;
            color: #064e3b;
            letter-spacing: 0.8px;
        }
        .cover-footer-addr {
            font-size: 8.5pt;
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
            margin-bottom: 20px;
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
            font-size: 10.5pt;
            font-weight: 800;
            margin-top: 4px;
            text-transform: uppercase;
            color: #1e293b;
        }
        .doc-title-box .sk-no {
            font-size: 10pt;
            font-weight: 700;
            margin-top: 3px;
        }

        h2.bab-head {
            font-size: 11pt;
            font-weight: 900;
            text-transform: uppercase;
            border-bottom: 2px solid #064e3b;
            padding-bottom: 4px;
            margin-top: 18px;
            margin-bottom: 10px;
            color: #064e3b;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        h3.sub-head {
            font-size: 10.5pt;
            font-weight: 800;
            margin-top: 12px;
            margin-bottom: 5px;
            color: #0f172a;
        }
        p, li {
            text-align: justify;
            margin-top: 3px;
            margin-bottom: 5px;
            line-height: 1.45;
        }

        table.formal-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0 14px 0;
            font-size: 9.5pt;
        }
        table.formal-table th, table.formal-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            vertical-align: top;
        }
        table.formal-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 9pt;
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
            font-size: 10pt;
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
<!-- HALAMAN 1: COVER DEPAN LUXURY & PRESTISIUS                                -->
<!-- ========================================================================= -->
<div class="container">
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
<!-- HALAMAN 2: LEMBAR PENGESAHAN RESMI (4 TANDA TANGAN)                       -->
<!-- ========================================================================= -->
<div class="container page-break">
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

    <p style="text-indent: 30px;">
        Setelah melalui proses kajian komprehensif, penyelarasan kurikulum operasional madrasah, dan rapat pleno bersama Tim Pengembang Kurikulum serta Pengurus Komite Madrasah, maka dokumen <strong>Buku Pedoman dan Blueprint Tiga Program Unggulan (Madrasah Berbasis Pesantren & ROHIS, Madrasah Berbasis Life Skill, dan Madrasah Berbasis Digital)</strong> ini disahkan secara resmi untuk diberlakukan sebagai panduan operasional penyelenggaraan KBM, pembinaan kesiswaan, penyediaan fasilitas vokasi, dan tolok ukur penjaminan mutu di <?= htmlspecialchars($sekolah_nama) ?> pada Tahun Pelajaran <?= htmlspecialchars($tahun_ajaran) ?>.
    </p>

    <div style="margin-top: 30px;">
        <table class="ttd-grid-4">
            <tr>
                <td>
                    Disetujui oleh:<br>
                    <strong>Ketua Komite Madrasah</strong><br>
                    <?= htmlspecialchars($sekolah_nama) ?><br><br><br><br><br>
                    <strong><?= htmlspecialchars($komite_nama) ?></strong><br>
                    Ketua Komite
                </td>
                <td>
                    Ditetapkan di : <?= htmlspecialchars($kecamatan) ?><br>
                    Pada tanggal : <?= htmlspecialchars($tgl_sk_formatted) ?><br>
                    <strong>Kepala Madrasah Aliyah Miftahul Huda</strong><br><br><br><br><br>
                    <strong><?= htmlspecialchars($kamad_nama) ?></strong><br>
                    NIP. <?= htmlspecialchars($kamad_nip) ?>
                </td>
            </tr>
            <tr>
                <td style="padding-top: 35px;">
                    Penyusun:<br>
                    <strong>Ketua Tim Pengembang Kurikulum</strong><br>
                    Waka Kurikulum <?= htmlspecialchars($sekolah_nama) ?><br><br><br><br><br>
                    <strong><?= htmlspecialchars($waka_nama) ?></strong><br>
                    NIP. <?= htmlspecialchars($waka_nip) ?>
                </td>
                <td style="padding-top: 35px;">
                    Mengetahui / Mengesahkan:<br>
                    <strong>Pengawas Pembina Madrasah Ahli Madya</strong><br>
                    Kantor Kementerian Agama Kab. Lampung Barat<br><br><br><br><br>
                    <strong><?= htmlspecialchars($pengawas_nama) ?></strong><br>
                    NIP. <?= htmlspecialchars($pengawas_nip) ?>
                </td>
            </tr>
        </table>
    </div>
</div>

<!-- ========================================================================= -->
<!-- HALAMAN 3: SURAT KEPUTUSAN (SK) PENETAPAN RESMI                           -->
<!-- ========================================================================= -->
<div class="container page-break">
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

    <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt; margin-bottom: 8px;">
        <tr>
            <td style="width: 15%; vertical-align: top; font-weight: bold;">Menimbang</td>
            <td style="width: 3%; vertical-align: top;">:</td>
            <td style="width: 82%; text-align: justify;">
                <ol style="margin: 0; padding-left: 16px;">
                    <li>Bahwa dalam rangka menjawab tantangan zaman dan membekali lulusan dengan imtaq kokoh, kemandirian ekonomi, serta literasi teknologi modern, diperlukan program terarah dan berkesinambungan;</li>
                    <li>Bahwa perpaduan antara karakter kepesantrenan, pembinaan ROHIS, kecakapan vokasi life skill, dan otomasi madrasah digital merupakan pilar keunggulan MAS Miftahul Huda;</li>
                    <li>Bahwa berdasarkan pertimbangan huruf a dan b, perlu menetapkan Surat Keputusan Kepala Madrasah tentang Tiga Program Unggulan TP <?= htmlspecialchars($tahun_ajaran) ?>.</li>
                </ol>
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: top; padding-top: 4px;">Mengingat</td>
            <td style="vertical-align: top; padding-top: 4px;">:</td>
            <td style="text-align: justify; padding-top: 4px;">
                <ol style="margin: 0; padding-left: 16px;">
                    <li>Undang-Undang RI Nomor 20 Tahun 2003 tentang Sistem Pendidikan Nasional;</li>
                    <li>Undang-Undang RI Nomor 18 Tahun 2019 tentang Pesantren;</li>
                    <li>KMA Nomor 347 Tahun 2022 tentang Pedoman Implementasi Kurikulum Merdeka pada Madrasah;</li>
                    <li>KMA Nomor 450 Tahun 2024 tentang Pedoman Implementasi Kurikulum Madrasah;</li>
                    <li>Rencana Kerja Tahunan dan Kurikulum Operasional Madrasah (KOM) <?= htmlspecialchars($sekolah_nama) ?>.</li>
                </ol>
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: top; padding-top: 4px;">Memutuskan</td>
            <td style="vertical-align: top; padding-top: 4px;">:</td>
            <td style="padding-top: 4px; font-weight: bold;">
                KEPUTUSAN KEPALA MADRASAH ALIYAH MIFTAHUL HUDA TENTANG PENETAPAN TIGA PROGRAM UNGGULAN TAHUN PELAJARAN <?= htmlspecialchars($tahun_ajaran) ?>.
            </td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse; font-size: 9.5pt;">
        <tr>
            <td style="width: 15%; vertical-align: top; font-weight: bold;">KESATU</td>
            <td style="width: 3%; vertical-align: top;">:</td>
            <td style="text-align: justify;">
                Menetapkan Tiga Program Unggulan Resmi <?= htmlspecialchars($sekolah_nama) ?> yaitu: <strong>(1) Madrasah Berbasis Pesantren & ROHIS</strong>, <strong>(2) Madrasah Berbasis Life Skill</strong>, dan <strong>(3) Madrasah Berbasis Digital</strong>.
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: top; padding-top: 3px;">KEDUA</td>
            <td style="vertical-align: top; padding-top: 3px;">:</td>
            <td style="text-align: justify; padding-top: 3px;">
                Menunjuk koordinator dan pembina pelaksanaan program: Koordinator Pesantren (<strong><?= htmlspecialchars($kord_pesantren_nama) ?></strong>), Pembina ROHIS (<strong><?= htmlspecialchars($pembina_rohis_nama) ?></strong>), Koordinator Life Skill (<strong><?= htmlspecialchars($kord_lifeskill_nama) ?></strong>), dan Koordinator Digital & IT (<strong><?= htmlspecialchars($kord_digital_nama) ?></strong>).
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: top; padding-top: 3px;">KETIGA</td>
            <td style="vertical-align: top; padding-top: 3px;">:</td>
            <td style="text-align: justify; padding-top: 3px;">
                Segala biaya yang timbul akibat pelaksanaan keputusan ini dibebankan pada anggaran madrasah yang relevan dan bantuan yang sah tidak mengikat.
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: top; padding-top: 3px;">KEEMPAT</td>
            <td style="vertical-align: top; padding-top: 3px;">:</td>
            <td style="text-align: justify; padding-top: 3px;">
                Keputusan ini mulai berlaku sejak tanggal ditetapkan, dan apabila di kemudian hari terdapat kekeliruan akan diperbaiki sebagaimana mestinya.
            </td>
        </tr>
    </table>

    <div style="margin-top: 20px; float: right; width: 45%; text-align: center; font-size: 10pt;">
        Ditetapkan di : <?= htmlspecialchars($kecamatan) ?><br>
        Pada tanggal : <?= htmlspecialchars($tgl_sk_formatted) ?><br>
        <strong>Kepala Madrasah Aliyah Miftahul Huda</strong><br><br><br><br><br>
        <strong><?= htmlspecialchars($kamad_nama) ?></strong><br>
        NIP. <?= htmlspecialchars($kamad_nip) ?>
    </div>
    <div style="clear: both;"></div>
</div>

<!-- ========================================================================= -->
<!-- HALAMAN 4: KATA PENGANTAR & DAFTAR ISI RESMI                              -->
<!-- ========================================================================= -->
<div class="container page-break">
    <h2 class="bab-head"><span>KATA PENGANTAR KEPALA MADRASAH</span> <span>MAS MIFHDA</span></h2>
    <p style="text-indent: 30px;">
        Puji syukur kita panjatkan ke hadirat Allah SWT atas limpahan rahmat, taufiq, dan inayah-Nya, sehingga Dokumen Buku Pedoman & Blueprint <strong>Tiga Program Unggulan MAS Miftahul Huda</strong> Tahun Pelajaran <?= htmlspecialchars($tahun_ajaran) ?> ini dapat diselesaikan dengan baik.
    </p>
    <p style="text-indent: 30px;">
        Pendidikan madrasah hari ini menghadapi tantangan ganda: di satu sisi harus mempertahankan kedalaman spiritualitas dan keteladanan akhlak santri (imtaq), namun di sisi lain dituntut responsif terhadap kebutuhan kemandirian hidup (life skill) dan percepatan teknologi digital 5.0. Melalui perpaduan tiga pilar: <em>Madrasah Berbasis Pesantren & ROHIS</em>, <em>Madrasah Berbasis Life Skill</em>, dan <em>Madrasah Berbasis Digital</em>, MAS Miftahul Huda berkomitmen mencetak generasi unggul yang sholih, berdaya saing kerja, dan melek teknologi.
    </p>
    <p style="text-indent: 30px;">
        Kami menyampaikan apresiasi dan terima kasih setinggi-tingginya kepada seluruh Tim Pengembang Kurikulum, dewan guru, Komite Madrasah, serta Pengawas Pembina Kementerian Agama yang telah berkontribusi aktif. Semoga buku pedoman ini menjadi petunjuk arah operasional yang membawa barokah bagi seluruh civitas akademika madrasah.
    </p>

    <div style="margin-top: 15px; margin-bottom: 25px; text-align: right; font-size: 10pt;">
        <?= htmlspecialchars($kecamatan) ?>, <?= htmlspecialchars($tgl_sk_formatted) ?><br>
        <strong>Kepala Madrasah,</strong><br><br><br>
        <strong><?= htmlspecialchars($kamad_nama) ?></strong>
    </div>

    <h2 class="bab-head"><span>DAFTAR ISI BLUEPRINT PEDOMAN</span> <span>SISTEMATIKA</span></h2>
    <table style="width: 100%; border-collapse: collapse; font-size: 10pt; line-height: 1.6;">
        <tr><td><strong>HALAMAN JUDUL / COVER DEPAN</strong></td><td align="right"><strong>Hal. 1</strong></td></tr>
        <tr><td><strong>LEMBAR PENGESAHAN RESMI (4 TANDA TANGAN)</strong></td><td align="right"><strong>Hal. 2</strong></td></tr>
        <tr><td><strong>SURAT KEPUTUSAN (SK) KEPALA MADRASAH</strong></td><td align="right"><strong>Hal. 3</strong></td></tr>
        <tr><td><strong>KATA PENGANTAR & DAFTAR ISI</strong></td><td align="right"><strong>Hal. 4</strong></td></tr>
        <tr><td><strong>BAB I &bull; PENDAHULUAN & LANDASAN STRATEGIS</strong></td><td align="right"><strong>Hal. 5</strong></td></tr>
        <tr><td style="padding-left: 15px;">1.1 Latar Belakang & Urgensi Program</td><td align="right">Hal. 5</td></tr>
        <tr><td style="padding-left: 15px;">1.2 Visi, Misi, dan Tujuan Strategis Madrasah</td><td align="right">Hal. 5</td></tr>
        <tr><td style="padding-left: 15px;">1.3 Profil Standar Kompetensi Lulusan (SKL) 3 Pilar</td><td align="right">Hal. 5</td></tr>
        <tr><td><strong>BAB II &bull; DESAIN PROGRAM 1: MADRASAH PESANTREN & ROHIS</strong></td><td align="right"><strong>Hal. 6</strong></td></tr>
        <tr><td style="padding-left: 15px;">2.1 Program Tahfidz & Tahsin Al-Qur'an Terpadu</td><td align="right">Hal. 6</td></tr>
        <tr><td style="padding-left: 15px;">2.2 Pembinaan & Kaderisasi Organisasi ROHIS</td><td align="right">Hal. 6</td></tr>
        <tr><td style="padding-left: 15px;">2.3 Pembiasaan Amaliah Ubudiyah Harian & Munaqosyah</td><td align="right">Hal. 6</td></tr>
        <tr><td><strong>BAB III &bull; DESAIN PROGRAM 2: MADRASAH LIFE SKILL (VOKASI)</strong></td><td align="right"><strong>Hal. 7</strong></td></tr>
        <tr><td style="padding-left: 15px;">3.1 Bidang Keahlian Menjahit & Kriya Busana Muslim</td><td align="right">Hal. 7</td></tr>
        <tr><td style="padding-left: 15px;">3.2 Bidang Tata Boga & Kuliner Halal Santri</td><td align="right">Hal. 7</td></tr>
        <tr><td style="padding-left: 15px;">3.3 Bidang Teknisi Komputer & Instalasi Jaringan LAN</td><td align="right">Hal. 7</td></tr>
        <tr><td style="padding-left: 15px;">3.4 Expo Kewirausahaan & Gelar Karya Santri</td><td align="right">Hal. 7</td></tr>
        <tr><td><strong>BAB IV &bull; DESAIN PROGRAM 3: MADRASAH DIGITAL (SMART 5.0)</strong></td><td align="right"><strong>Hal. 8</strong></td></tr>
        <tr><td style="padding-left: 15px;">4.1 Ekosistem MASMIFHDA SYSTEM & Paperless Portal</td><td align="right">Hal. 8</td></tr>
        <tr><td style="padding-left: 15px;">4.2 Kurikulum Koding & Literasi Artificial Intelligence</td><td align="right">Hal. 8</td></tr>
        <tr><td style="padding-left: 15px;">4.3 Sistem CBT Mandiri & E-Library Terpadu</td><td align="right">Hal. 8</td></tr>
        <tr><td><strong>BAB V &bull; STRUKTUR TIM PENGELOLA & MATRIKS KPI</strong></td><td align="right"><strong>Hal. 9</strong></td></tr>
        <tr><td style="padding-left: 15px;">5.1 Struktur Organisasi & Uraian Tugas Pokok Tim</td><td align="right">Hal. 9</td></tr>
        <tr><td style="padding-left: 15px;">5.2 Key Performance Indicators (KPI) & Penjaminan Mutu</td><td align="right">Hal. 9</td></tr>
    </table>
</div>

<!-- ========================================================================= -->
<!-- HALAMAN 5: BAB I PENDAHULUAN & LANDASAN STRATEGIS                         -->
<!-- ========================================================================= -->
<div class="container page-break">
    <h2 class="bab-head"><span>BAB I &bull; PENDAHULUAN & LANDASAN STRATEGIS</span> <span>BAGIAN I</span></h2>

    <h3 class="sub-head">1.1 Latar Belakang & Urgensi Program</h3>
    <p>
        Pendidikan madrasah di era globalisasi menghadapi dinamika perubahan yang sangat cepat. Arus digitalisasi dan otomatisasi menuntut sumber daya manusia yang adaptif, melek teknologi, dan memiliki keterampilan aplikatif bernilai ekonomi. Di sisi lain, degradasi moral di kalangan remaja menjadi ancaman serius apabila tidak dibentengi oleh pondasi akidah dan amaliah Islam yang kokoh. MAS Miftahul Huda memformulasikan <strong>Tiga Program Unggulan</strong> sebagai langkah terobosan integratif yang memadukan keunggulan nilai kepesantrenan (Imtaq), kecakapan vokasi praktis (Life Skill), dan ekosistem madrasah cerdas tanpa kertas berbasis AI (Digital).
    </p>

    <h3 class="sub-head">1.2 Visi, Misi, dan Tujuan Strategis Madrasah</h3>
    <ul style="padding-left: 20px;">
        <li><strong>Visi:</strong> <em>"<?= htmlspecialchars($institusi['visi'] ?? 'Bertaqwa, Berakhlakul Karimah, Berprestasi Dan Berwawasan Lingkungan Hidup') ?>"</em>.</li>
        <li><strong>Misi:</strong> Menyelenggarakan pendidikan Islam berbasis nilai-nilai Qur'ani, membekali peserta didik dengan kecakapan vokasi aplikatif mandiri, serta mewujudkan tata kelola madrasah modern berbasis teknologi cerdas 5.0.</li>
        <li><strong>Tujuan Pokok:</strong> Mencapai 100% kelulusan santri yang hafal Al-Qur'an minimal Juz 30, menguasai 1 keahlian wirausaha bersertifikat, dan terampil mengoperasikan platform digital serta computational thinking.</li>
    </ul>

    <h3 class="sub-head">1.3 Profil Standar Kompetensi Lulusan (SKL) 3 Pilar</h3>
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

<!-- ========================================================================= -->
<!-- HALAMAN 6: BAB II PROGRAM MADRASAH BERBASIS PESANTREN & ROHIS              -->
<!-- ========================================================================= -->
<div class="container page-break">
    <h2 class="bab-head"><span>BAB II &bull; MADRASAH BERBASIS PESANTREN & ROHIS</span> <span>PROGRAM I</span></h2>

    <h3 class="sub-head">2.1 Program Tahfidz & Tahsin Al-Qur'an Terpadu</h3>
    <p>
        Program tahfidz dirancang untuk memastikan seluruh siswa memiliki hubungan batin dan hafalan yang kuat terhadap Kitab Suci Al-Qur'an:
    </p>
    <ul style="padding-left: 20px;">
        <li><strong>Target Reguler (Wajib Seluruh Siswa):</strong> Minimal mutqin Juz 30 saat kelulusan kelas XII. Bagi kelas akselerasi ditargetkan menguasai Juz 1 dan Juz 2.</li>
        <li><strong>Target Santri Asrama (Peminatan):</strong> Akselerasi 10 hingga 30 Juz dengan metode <em>Sabaq, Sabqi, dan Manzil</em> di bawah bimbingan ustadz/ustadzah huffadz asrama.</li>
        <li><strong>Metode & Evaluasi:</strong> Talaqqi hafalan setiap pagi pukul 06.45–07.30 WIB sebelum KBM dimulai, setor hafalan harian, muraja'ah akbar mingguan, serta Munaqosyah Terbuka pada akhir semester.</li>
    </ul>

    <h3 class="sub-head">2.2 Pembinaan & Kaderisasi Organisasi ROHIS (Kerohanian Islam)</h3>
    <p>
        Kerohanian Islam (ROHIS) menjadi motor penggerak dakwah dan pembentukan kepemimpinan religius santri di madrasah:
    </p>
    <table class="formal-table">
        <thead>
            <tr>
                <th style="width: 25%;">Aktivitas ROHIS</th>
                <th style="width: 45%;">Bentuk Kegiatan & Pembinaan</th>
                <th style="width: 30%;">Penanggung Jawab</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Mentoring Keislaman</strong></td>
                <td>Halaqah mingguan kelompok kecil berisi penguatan akidah, adab santri, kajian fiqih thaharah dan ibadah praktis.</td>
                <td>Pembina ROHIS & Mentor Senior</td>
            </tr>
            <tr>
                <td><strong>Kajian Keputrian</strong></td>
                <td>Kajian fiqih an-nisa, adab pergaulan muslimah, dan pelatihan keterampilan muslimah setiap hari Jumat siang.</td>
                <td>Ustadzah Pembina Keputrian</td>
            </tr>
            <tr>
                <td><strong>Kaderisasi Dai Muda</strong></td>
                <td>Latihan khutbah Jumat, kultum santri ba'da Dhuhr, retorika dakwah, dan penerjunan dai santri ke masyarakat saat Ramadhan.</td>
                <td>Koordinator Pesantren & Tim Bahasa</td>
            </tr>
            <tr>
                <td><strong>Peringatan Hari Besar (PHBI)</strong></td>
                <td>Penyelenggaraan Maulid Nabi, Isra' Mi'raj, Tahun Baru Hijriyah, pesantren kilat, dan santunan yatim piatu.</td>
                <td>Pengurus ROHIS Santri MAS Mifhda</td>
            </tr>
        </tbody>
    </table>

    <h3 class="sub-head">2.3 Pembiasaan Amaliah Ubudiyah Harian</h3>
    <p>
        Membentuk kebiasaan ibadah istiqomah melalui Sholat Dhuha berjamaah setiap pagi, Sholat Dhuhur berjamaah di masjid madrasah, pembacaan Ratibul Haddad dan Asmaul Husna, pembiasaan dzikir pagi-petang, serta budaya senyum, salam, sapa, sopan, santun (5S) terhadap bapak/ibu guru dan sesama santri.
    </p>
</div>

<!-- ========================================================================= -->
<!-- HALAMAN 7: BAB III PROGRAM MADRASAH BERBASIS LIFE SKILL                   -->
<!-- ========================================================================= -->
<div class="container page-break">
    <h2 class="bab-head"><span>BAB III &bull; MADRASAH BERBASIS LIFE SKILL (VOKASI)</span> <span>PROGRAM II</span></h2>

    <h3 class="sub-head">3.1 Filosofi & Tujuan Kemandirian Santri</h3>
    <p>
        Program Life Skill MAS Miftahul Huda dirancang agar setiap lulusan tidak hanya memiliki bekal ilmu agama dan akademik, melainkan memiliki keterampilan kerja terapan (vocational skills) yang dapat mendatangkan penghasilan mandiri atau menjadi modal berwirausaha saat melanjutkan studi maupun langsung berkarya.
    </p>

    <h3 class="sub-head">3.2 Tiga Peminatan Kejuruan Vokasi Terapan</h3>
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

    <h3 class="sub-head">3.3 Expo Kewirausahaan & Gelar Karya Santri (Market Day)</h3>
    <p>
        Setiap akhir semester, madrasah menyelenggarakan <strong>Expo Kewirausahaan & Gelar Karya Santri</strong>. Seluruh hasil karya peserta didik dari kelas menjahit, boga, dan komputer dipamerkan, dinilai oleh tim penguji eksternal, dan dipasarkan kepada wali santri serta masyarakat umum sebagai tolok ukur daya serap ekonomi nyata.
    </p>
</div>

<!-- ========================================================================= -->
<!-- HALAMAN 8: BAB IV PROGRAM MADRASAH BERBASIS DIGITAL                       -->
<!-- ========================================================================= -->
<div class="container page-break">
    <h2 class="bab-head"><span>BAB IV &bull; MADRASAH BERBASIS DIGITAL & AI 5.0</span> <span>PROGRAM III</span></h2>

    <h3 class="sub-head">4.1 Transformasi Ekosistem "MASMIFHDA SYSTEM" Terpadu</h3>
    <p>
        MAS Miftahul Huda telah menerapkan digitalisasi penuh pada seluruh lini operasional madrasah melalui arsitektur <strong>MASMIFHDA SYSTEM (Aula PWA Platform)</strong>:
    </p>
    <ul style="padding-left: 20px;">
        <li><strong>Portal Guru & Tendik (PWA Mobile):</strong> Manajemen presensi QR Code & deteksi wajah, jurnal mengajar real-time di kelas, upload foto KBM, e-kinerja, dan e-arsip dokumen kepegawaian.</li>
        <li><strong>Portal Siswa & E-Presensi:</strong> Akses kartu pelajar elektronik, rekap kehadiran harian, absensi kepulangan, riwayat nilai formatif, dan materi ajar mandiri.</li>
        <li><strong>Supervisi & Penjaminan Mutu:</strong> Modul supervisi kunjungan kelas digital oleh Kepala Madrasah dan Tim Supervisor dengan rekapitulasi skor otomatis 15 instrumen KBM.</li>
        <li><strong>Keuangan & Kasir Digital:</strong> Pembayaran SPP dan infaq santri dengan penerbitan kwitansi digital ber-QR Code valid serta pembukuan BKU bendahara otomatis.</li>
    </ul>

    <h3 class="sub-head">4.2 Mata Pelajaran Koding & Literasi Artificial Intelligence (AI)</h3>
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
                <td>Logika pemrograman, computational thinking, pembuatan algoritma flowchart, dasar HTML5, CSS3, dan etika berinternet sehat (cyber hygiene).</td>
                <td>Mampu membuat halaman website profil santri dan portofolio pribadi statis.</td>
            </tr>
            <tr>
                <td><strong>Kelas XI (Fase F)</strong></td>
                <td>Pemrograman Web interaktif (JavaScript dasar), pengenalan database, dan dasar Artificial Intelligence (Prompt Engineering & Pemanfaatan AI untuk edukasi).</td>
                <td>Mampu membuat formulir web terhubung database dan memanfaatkan tools AI secara etis.</td>
            </tr>
            <tr>
                <td><strong>Kelas XII (Fase F)</strong></td>
                <td>Dasar pemrograman Python, pengolahan data sederhana, pembuatan proyek otomasi kecil, dan literasi keamanan data digital madrasah.</td>
                <td>Proyek mini-aplikasi digital madrasah dan portofolio koding siswa.</td>
            </tr>
        </tbody>
    </table>

    <h3 class="sub-head">4.3 Penyelenggaraan CBT 100% Paperless & E-Library</h3>
    <p>
        Seluruh Asesmen Sumatif (STS, SAS, SAT, dan Ujian Madrasah) diselenggarakan secara 100% paperless menggunakan sistem Computer Based Test (CBT) mandiri dengan fitur acak soal otomatis, pembatasan browser, dan rekapitulasi nilai instan. Koleksi buku paket teks dan modul ajar didistribusikan melalui E-Library resmi madrasah.
    </p>
</div>

<!-- ========================================================================= -->
<!-- HALAMAN 9: BAB V STRUKTUR PENGELOLA, MATRIKS KPI & PENETAPAN              -->
<!-- ========================================================================= -->
<div class="container page-break">
    <h2 class="bab-head"><span>BAB V &bull; STRUKTUR TIM PENGELOLA & MATRIKS KPI</span> <span>BAGIAN AKHIR</span></h2>

    <h3 class="sub-head">5.1 Susunan Tim Pengembang & Pengelola Program</h3>
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

    <h3 class="sub-head">5.2 Key Performance Indicators (KPI) & Penjaminan Mutu:</h3>
    <ol style="padding-left: 20px; font-size: 9.5pt;">
        <li><strong>KPI Pesantren & ROHIS:</strong> 100% peserta didik lulusan tuntas hafalan mutqin minimal Juz 30 dan hadir tertib dalam ibadah ubudiyah serta mentoring ROHIS.</li>
        <li><strong>KPI Life Skill Vokasi:</strong> 100% peserta didik menguasai minimal 1 cabang kejuruan vokasi yang dibuktikan dengan karya riil pada Expo Gelar Karya Santri akhir semester.</li>
        <li><strong>KPI Madrasah Digital:</strong> 100% dewan guru tertib mengisi jurnal mengajar online, 100% ujian berlangsung paperless via CBT, dan tersedianya portofolio koding siswa.</li>
    </ol>

    <div style="margin-top: 25px; float: right; width: 45%; text-align: center; font-size: 10pt;">
        Ditetapkan di : <?= htmlspecialchars($kecamatan) ?><br>
        Pada tanggal : <?= htmlspecialchars($tgl_sk_formatted) ?><br>
        <strong>Kepala Madrasah Aliyah Miftahul Huda</strong><br><br><br><br><br>
        <strong><?= htmlspecialchars($kamad_nama) ?></strong><br>
        NIP. <?= htmlspecialchars($kamad_nip) ?>
    </div>
    <div style="clear: both;"></div>
</div>

</body>
</html>
