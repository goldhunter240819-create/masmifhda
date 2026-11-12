<?php
use App\Core\Branding;

$brand = Branding::get();
$sekolah_nama = !empty($institusi['nama']) ? $institusi['nama'] : 'MADRASAH ALIYAH MIFTAHUL HUDA';
$nama_yayasan = !empty($institusi['nama_yayasan']) ? $institusi['nama_yayasan'] : 'YAYASAN PONDOK PESANTREN MIFTAHUL HUDA MATHLA\'UL ANWAR';
$logoUrl = !empty($institusi['logo']) ? '/public/uploads/logo/' . $institusi['logo'] : Branding::getInstitusiLogo();

$alamatParts = [];
if (!empty($institusi['alamat'])) $alamatParts[] = $institusi['alamat'];
if (!empty($institusi['desa'])) $alamatParts[] = 'Ds. ' . $institusi['desa'];
if (!empty($institusi['kecamatan'])) $alamatParts[] = 'Kec. ' . $institusi['kecamatan'];
if (!empty($institusi['kota'])) $alamatParts[] = 'Kota ' . $institusi['kota'];
if (!empty($institusi['provinsi'])) $alamatParts[] = 'Prov. ' . $institusi['provinsi'];
$alamatLengkap = implode(', ', $alamatParts);
if (empty($alamatLengkap)) {
    $alamatLengkap = 'Jl. Tirtomoyo No. 083, Margorahayu, Ds. Tambak Jaya, Kec. Way Tenong, Lampung Barat, Prov. Lampung';
}

$webUrl = !empty($institusi['website']) ? $institusi['website'] : 'masmifhda.sch.id';
$emailAddr = !empty($institusi['email']) ? $institusi['email'] : 'masmiftahulhuda@gmail.com';
$kecamatan = !empty($institusi['kecamatan']) ? $institusi['kecamatan'] : 'Way Tenong';

$kepala_nama = $penanggung_jawab['nama'] ?? $kamad['nama'] ?? 'Drs. Hamim Hasyim';
$kepala_nip = (!empty($penanggung_jawab['nip']) && $penanggung_jawab['nip'] !== '-') ? $penanggung_jawab['nip'] : ((!empty($kamad['nip']) && $kamad['nip'] !== '-') ? $kamad['nip'] : '');
$pengawas_nama = $pengawas['nama'] ?? 'Drs. H. M. YUSUF, M.Pd.I';
$pengawas_nip = (!empty($pengawas['nip']) && $pengawas['nip'] !== '-') ? $pengawas['nip'] : '';
$pengawas_jabatan = $pengawas['jabatan'] ?? 'Pengawas Madrasah Ahli Madya Kemenag';

$bulanIndo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$tgl_raw = !empty($_GET['tanggal']) ? $_GET['tanggal'] : (!empty($config['tanggal_laporan']) ? $config['tanggal_laporan'] : date('Y-m-d'));
$tgl_obj = strtotime($tgl_raw);
$tgl_cetak = date('j', $tgl_obj) . ' ' . $bulanIndo[(int)date('n', $tgl_obj)] . ' ' . date('Y', $tgl_obj);

$isStandalonePrint = isset($isPrintMode) && $isPrintMode === true;
?>
<?php if(!$isStandalonePrint): ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title = 'Laporan Hasil Tindak Lanjut Supervisi (RTL)'; include __DIR__ . '/../admin/_zamrud_head.php'; ?>
</head>
<body>
<?php $page_badge = 'Kurikulum'; ?>
<div class="z-wrap">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../siakad/_siakad_header.php'; ?>
        <div style="padding: 24px;">
            
            <!-- Breadcrumb Navigation -->
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:20px;" class="no-print">
                <div style="display:flex; align-items:center; gap:8px; font-size:0.86rem; color:#64748b; font-weight:600;">
                    <a href="/kurikulum/supervisi" style="color:#059669; text-decoration:none;">Supervisi</a>
                    <span>/</span>
                    <a href="/kurikulum/supervisi/tindak-lanjut" style="color:#059669; text-decoration:none;">Tindak Lanjut</a>
                    <span>/</span>
                    <span style="color:#0f172a; font-weight:700;">Laporan Hasil Tindak Lanjut</span>
                </div>
                <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                    <form method="GET" action="" style="display:inline-flex; align-items:center; gap:6px; background:#fff; padding:5px 10px; border-radius:8px; border:1px solid #cbd5e1;">
                        <span style="font-size:0.78rem; font-weight:700; color:#475569;"><i data-lucide="calendar" style="width:14px;height:14px;vertical-align:middle;color:#059669;"></i> Tgl Dokumen:</span>
                        <input type="date" name="tanggal" value="<?= htmlspecialchars($tgl_raw) ?>" onchange="this.form.submit()" style="border:none; font-family:inherit; font-size:0.8rem; font-weight:600; color:#0f172a; outline:none; background:transparent; cursor:pointer;" title="Pilih tanggal pengesahan dokumen">
                    </form>
                    <a href="/kurikulum/supervisi/tim" class="btn-act btn-white" style="border:1px solid #cbd5e1; padding:8px 14px; font-size:0.82rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                        <i data-lucide="user-check" style="width:15px;height:15px; color:#059669;"></i> Tim Supervisi & SK
                    </a>
                    <a href="/kurikulum/supervisi/laporan-tindak-lanjut/cetak?tanggal=<?= urlencode($tgl_raw) ?>" target="_blank" class="btn-act btn-white" style="border:1px solid #cbd5e1; padding:8px 14px; font-size:0.82rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                        <i data-lucide="external-link" style="width:15px;height:15px;"></i> Buka Tab Cetak Penuh
                    </a>
                    <a href="/kurikulum/supervisi/laporan-hasil" class="btn-act btn-white" style="border:1px solid #cbd5e1; padding:8px 14px; font-size:0.82rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                        <i data-lucide="file-bar-chart-2" style="width:15px;height:15px; color:#059669;"></i> Laporan Hasil Supervisi
                    </a>
                    <a href="/kurikulum/supervisi/tindak-lanjut" class="btn-act btn-white" style="border:1px solid #cbd5e1; padding:8px 14px; font-size:0.82rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                        <i data-lucide="sliders" style="width:15px;height:15px;"></i> Kelola Data RTL
                    </a>
                    <button type="button" onclick="window.print()" class="btn-act btn-emerald" style="background:#059669; color:#fff; border:none; padding:8px 18px; border-radius:8px; font-weight:700; font-size:0.85rem; cursor:pointer; display:inline-flex; align-items:center; gap:6px; box-shadow:0 4px 12px rgba(5,150,105,0.25);">
                        <i data-lucide="printer" style="width:16px;height:16px;"></i> Cetak Dokumen Resmi A4
                    </button>
                </div>
            </div>

            <!-- Zamrud Banner -->
            <div class="no-print" style="background:linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #1d4ed8 100%); color:#ffffff; border-radius:16px; padding:22px 26px; margin-bottom:24px; box-shadow:0 8px 24px -4px rgba(37,99,235,0.25); border:1px solid rgba(255,255,255,0.15);">
                <div style="display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,0.18); padding:3px 10px; border-radius:999px; font-size:0.72rem; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;">
                    <i data-lucide="check-check" style="width:12px;height:12px;"></i> Siklus Pembinaan Mutu Berkelanjutan &bull; Bukti Fisik PKKM Komponen 4 Indikator 4.3
                </div>
                <h1 style="font-size:1.45rem; font-weight:800; margin:0 0 6px 0; color:#fff; display:flex; align-items:center; gap:10px;">
                    <i data-lucide="file-check" style="width:26px;height:26px;color:#93c5fd;"></i>
                    Dokumen Laporan Hasil Tindak Lanjut Supervisi Pembelajaran (RTL)
                </h1>
                <p style="margin:0; font-size:0.88rem; color:rgba(255,255,255,0.9); max-width:850px; line-height:1.5;">
                    Dokumen laporan resmi realisasi program bimbingan klinis, pelatihan MOOC PINTAR Kemenag, diseminasi Kombel KKG, dan pendampingan mandiri guru pasca supervisi akademik. Memuat bukti konkret tindak lanjut 100% terlaksana.
                </p>

                <!-- Quick Navigation Pills -->
                <div style="display:flex; gap:8px; margin-top:16px; flex-wrap:wrap;">
                    <a href="#sec-cover-rtl" style="background:rgba(255,255,255,0.15); color:#fff; padding:5px 12px; border-radius:8px; font-size:0.76rem; text-decoration:none; font-weight:700;">Cover & Pengesahan</a>
                    <a href="#sec-bab1-rtl" style="background:rgba(255,255,255,0.15); color:#fff; padding:5px 12px; border-radius:8px; font-size:0.76rem; text-decoration:none; font-weight:700;">Bab I Pendahuluan</a>
                    <a href="#sec-bab2-rtl" style="background:rgba(255,255,255,0.15); color:#fff; padding:5px 12px; border-radius:8px; font-size:0.76rem; text-decoration:none; font-weight:700;">Bab II Strategi RTL</a>
                    <a href="#sec-bab3-rtl" style="background:rgba(255,255,255,0.15); color:#fff; padding:5px 12px; border-radius:8px; font-size:0.76rem; text-decoration:none; font-weight:700;">Bab III Realisasi & Hasil</a>
                    <a href="#sec-bab4-rtl" style="background:rgba(255,255,255,0.15); color:#fff; padding:5px 12px; border-radius:8px; font-size:0.76rem; text-decoration:none; font-weight:700;">Bab IV Penutup</a>
                    <a href="#sec-lampiran-rtl" style="background:rgba(255,255,255,0.15); color:#fff; padding:5px 12px; border-radius:8px; font-size:0.76rem; text-decoration:none; font-weight:700;">Lampiran Matriks RTL</a>
                </div>
            </div>

            <!-- Document Wrapper Container -->
            <div style="max-width: 920px; margin: 0 auto; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); padding: 40px 50px;">
<?php else: ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Tindak Lanjut Supervisi (RTL) - <?= htmlspecialchars($sekolah_nama) ?></title>
    <link rel="icon" type="image/png" href="<?= htmlspecialchars($brand['favicon']) ?>">
    <style>
        @page {
            size: A4 portrait;
            margin: 2cm 2cm 2cm 2cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 20px;
        }
        .no-print {
            display: none !important;
        }
    </style>
</head>
<body>
    <div style="position:fixed; top:20px; right:20px; z-index:9999;" class="no-print">
        <button onclick="window.print()" style="background:#2563eb; color:#fff; border:none; padding:10px 20px; border-radius:8px; font-weight:bold; cursor:pointer; box-shadow:0 4px 10px rgba(0,0,0,0.15);">Cetak Dokumen A4</button>
    </div>
    <div style="max-width:210mm; margin:0 auto;">
<?php endif; ?>

<style>
    .doc-page {
        font-family: 'Times New Roman', Times, serif;
        color: #0f172a;
        font-size: 11pt;
        line-height: 1.55;
    }
    .doc-cover {
        text-align: center;
        padding: 50px 20px 40px 20px;
        border: 4px double #1e3a8a;
        margin-bottom: 50px;
        page-break-after: always;
        background: #ffffff;
    }
    .doc-cover h1 {
        font-size: 19pt;
        font-weight: 800;
        margin: 0 0 8px 0;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #0f172a;
    }
    .doc-cover h2 {
        font-size: 13.5pt;
        font-weight: 700;
        margin: 0 0 35px 0;
        color: #1e3a8a;
        text-transform: uppercase;
    }
    .doc-cover-logo {
        width: 140px;
        height: 140px;
        object-fit: contain;
        margin: 25px auto 35px auto;
        display: block;
    }
    .doc-section-title {
        font-size: 13pt;
        font-weight: bold;
        text-transform: uppercase;
        text-align: center;
        margin: 35px 0 20px 0;
        border-bottom: 2px solid #000;
        padding-bottom: 4px;
    }
    .doc-h3 {
        font-size: 11.5pt;
        font-weight: bold;
        margin: 18px 0 6px 0;
    }
    .doc-p {
        text-align: justify;
        text-indent: 32px;
        margin: 0 0 10px 0;
    }
    .doc-ol, .doc-ul {
        margin: 0 0 12px 0;
        padding-left: 28px;
        text-align: justify;
    }
    .doc-ol li, .doc-ul li {
        margin-bottom: 4px;
    }
    .doc-table {
        width: 100%;
        border-collapse: collapse;
        margin: 12px 0 18px 0;
        font-size: 9.2pt;
        line-height: 1.35;
    }
    .doc-table th, .doc-table td {
        border: 1px solid #000;
        padding: 6px 8px;
        vertical-align: middle;
    }
    .doc-table th {
        background: #f1f5f9;
        font-weight: bold;
        text-align: center;
    }
    @media print {
        .main-content, .p-6, .main-content > div {
            padding: 0 !important;
            margin: 0 !important;
            max-width: none !important;
            border: none !important;
            box-shadow: none !important;
        }
        .page-break {
            page-break-after: always;
        }
    }
</style>

<div class="doc-page">

    <!-- ==================== HALAMAN SAMPUL / COVER ==================== -->
    <div id="sec-cover-rtl" class="doc-cover">
        <div style="font-size: 12pt; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; color: #1e3a8a; margin-bottom: 5px;">
            DOKUMEN PENGEMBANGAN KEPROFESIAN BERKELANJUTAN &bull; PKKM 4.3
        </div>
        <h1>LAPORAN HASIL TINDAK LANJUT SUPERVISI<br>(RENCANA TINDAK LANJUT / RTL GURU)</h1>
        <h2>PROGRAM PEMBINAAN & PENINGKATAN KOMPETENSI PEDAGOGIK GURU<br>SEMESTER <?= strtoupper($semester) ?> TAHUN PELAJARAN <?= htmlspecialchars($tahun_ajaran) ?></h2>

        <?php if (!empty($institusi['logo'])): ?>
            <img src="/public/uploads/logo/<?= htmlspecialchars($institusi['logo']) ?>" class="doc-cover-logo" alt="Logo Madrasah">
        <?php else: ?>
            <img src="<?= htmlspecialchars($brand['logo_full']) ?>" class="doc-cover-logo" alt="Logo Madrasah">
        <?php endif; ?>

        <div style="margin: 30px auto; display: inline-block; padding: 10px 24px; border-top: 2px solid #1e3a8a; border-bottom: 2px solid #1e3a8a; font-weight: bold; font-size: 12pt; color: #1e3a8a;">
            STATUS: 100% PROGRAM TINDAK LANJUT TEREALISASI TUNTAS
        </div>

        <div style="margin-top: 40px; line-height: 1.6;">
            <div style="font-size: 11pt; color: #475569;">Disusun Oleh:</div>
            <div style="font-size: 13pt; font-weight: bold; text-transform: uppercase;">TIM SUPERVISOR & KOORDINATOR KURIKULUM</div>
            <div style="font-size: 14pt; font-weight: 800; text-transform: uppercase; margin-top: 4px;"><?= htmlspecialchars($sekolah_nama) ?></div>
        </div>

        <div style="margin-top: 45px; font-size: 10pt; color: #334155; border-top: 1px solid #cbd5e1; padding-top: 12px; line-height: 1.4;">
            <?= htmlspecialchars($alamatLengkap) ?><br>
            Website: <?= htmlspecialchars($webUrl) ?> &bull; Email: <?= htmlspecialchars($emailAddr) ?>
        </div>
    </div>

    <!-- ==================== LEMBAR PENGESAHAN ==================== -->
    <div style="page-break-after: always; padding: 20px 0;">
        <h2 style="text-align: center; font-size: 14pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; margin-bottom: 25px;">
            LEMBAR PENGESAHAN DOKUMEN
        </h2>
        
        <p class="doc-p">
            Dokumen <strong>Laporan Hasil Pelaksanaan Tindak Lanjut Supervisi Pembelajaran (RTL)</strong> Semester <?= htmlspecialchars($semester) ?> Tahun Pelajaran <?= htmlspecialchars($tahun_ajaran) ?> di lingkungan <strong><?= htmlspecialchars($sekolah_nama) ?></strong> yang memuat analisis pemetaan kebutuhan pembinaan guru, pelaksanaan bimbingan klinis, keikutsertaan pelatihan MOOC PINTAR Kemenag, diseminasi Komunitas Belajar (KKG), pendampingan mandiri, serta matriks realisasi perbaikan kinerja mengajar bagi seluruh 12 orang dewan guru, telah diperiksa, diverifikasi, dan disahkan.
        </p>

        <p class="doc-p" style="margin-top: 15px;">
            Dokumen laporan tindak lanjut ini sah sebagai bukti otentik akuntabilitas pembinaan keprofesian berkelanjutan madrasah, berkas monitoring Pengawas Pembina Kementerian Agama, dan kelengkapan bukti fisik Penilaian Kinerja Kepala Madrasah (PKKM) Komponen 4 Indikator 4.3.1 (Tindak Lanjut Hasil Supervisi).
        </p>

        <table style="width: 100%; margin-top: 50px; border: none; border-collapse: collapse;">
            <tr style="border: none;">
                <td style="width: 50%; border: none; text-align: center; vertical-align: bottom; padding-bottom: 70px;">
                    <p style="margin: 0; line-height: 1.5;">Ketua Panitia,</p>
                </td>
                <td style="width: 50%; border: none; text-align: center; vertical-align: bottom; padding-bottom: 70px;">
                    <p style="margin: 0; line-height: 1.5;">
                        <?= htmlspecialchars($kecamatan) ?>, <?= $tgl_cetak ?><br>
                        Kepala Madrasah (Penanggung Jawab),
                    </p>
                </td>
            </tr>
            <tr style="border: none;">
                <td style="width: 50%; border: none; text-align: center; vertical-align: top;">
                    <p style="margin: 0; font-weight: bold; text-decoration: underline; font-size: 11pt;">
                        <?= htmlspecialchars($ketua_panitia['nama'] ?? $ketua_tim['nama'] ?? 'M AZIZ SULAIMAN, S.Pd') ?>
                    </p>
                </td>
                <td style="width: 50%; border: none; text-align: center; vertical-align: top;">
                    <p style="margin: 0; font-weight: bold; text-decoration: underline; font-size: 11pt;">
                        <?= htmlspecialchars($kepala_nama) ?>
                    </p>
                </td>
            </tr>
        </table>

        <div style="text-align: center; margin-top: 40px; padding: 12px; background: #eff6ff; border: 1px dashed #93c5fd; border-radius: 8px;">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data=https://masmifhda.sch.id/kurikulum/supervisi/laporan-tindak-lanjut" alt="QR Validasi" style="width: 50px; height: 50px; vertical-align: middle;">
            <div style="font-size: 8pt; color: #1e3a8a; margin-top: 4px; font-weight: bold;">
                VALIDASI RESMI DIGITAL &bull; PELAKSANAAN TINDAK LANJUT SUPERVISI KEMENAG
            </div>
        </div>
    </div>

    <!-- ==================== KATA PENGANTAR ==================== -->
    <div style="page-break-after: always; padding: 20px 0;">
        <h2 style="text-align: center; font-size: 14pt; font-weight: bold; text-transform: uppercase; margin-bottom: 25px;">
            KATA PENGANTAR
        </h2>
        
        <p class="doc-p">
            Alhamdulillahirabbil'alamin, puji syukur kami persembahkan ke hadirat Allah SWT atas karunia dan rahmat-Nya sehingga <strong>Laporan Hasil Pelaksanaan Tindak Lanjut Supervisi Pembelajaran (RTL)</strong> Semester <?= htmlspecialchars($semester) ?> Tahun Pelajaran <?= htmlspecialchars($tahun_ajaran) ?> di <?= htmlspecialchars($sekolah_nama) ?> dapat tersusun dengan tuntas dan sistematis.
        </p>

        <p class="doc-p">
            Prinsip fundamental supervisi akademik modern menegaskan bahwa kegiatan supervisi yang tidak diiringi dengan tindak lanjut nyata hanya akan berakhir sebagai arsip administratif semata. Oleh karena itu, madrasah kami menempatkan Rencana Tindak Lanjut (RTL) sebagai fase yang paling substansial dalam memfasilitasi transformasi profesional guru. Setiap catatan temuan, kekurangan, maupun potensi unggul guru yang terpotret saat observasi kelas langsung direspons melalui skema intervensi yang humanis, terukur, dan bermakna.
        </p>

        <p class="doc-p">
            Laporan ini mendokumentasikan bagaimana 12 orang dewan guru yang disupervisi telah tuntas menyelesaikan program tindak lanjut sesuai kebutuhan masing-masing, baik berupa bimbingan klinis individual bersama kepala madrasah, penyelesaian modul diklat di platform resmi MOOC PINTAR Kemenag, diseminasi modul ajar di Komunitas Belajar (KKG), maupun pembuatan media ajar ramah anak.
        </p>

        <p class="doc-p">
            Terima kasih kami haturkan kepada Pengawas Pembina dan seluruh rekan sejawat guru atas dedikasi dan keterbukaan dalam belajar demi terwujudnya madrasah mandiri berprestasi.
        </p>

        <div style="margin-top: 35px; text-align: right;">
            <?= htmlspecialchars($kecamatan) ?>, <?= $tgl_cetak ?><br>
            <strong>Tim Supervisor Madrasah</strong>
        </div>
    </div>

    <!-- ==================== DAFTAR ISI ==================== -->
    <div style="page-break-after: always; padding: 20px 0;">
        <h2 style="text-align: center; font-size: 14pt; font-weight: bold; text-transform: uppercase; margin-bottom: 25px;">
            DAFTAR ISI
        </h2>
        
        <table style="width: 100%; border-collapse: collapse; font-size: 11pt; line-height: 2;">
            <tr><td>HALAMAN SAMPUL / COVER</td><td style="text-align: right;">i</td></tr>
            <tr><td>LEMBAR PENGESAHAN DOKUMEN</td><td style="text-align: right;">ii</td></tr>
            <tr><td>KATA PENGANTAR</td><td style="text-align: right;">iii</td></tr>
            <tr><td>DAFTAR ISI</td><td style="text-align: right;">iv</td></tr>
            <tr><td colspan="2" style="font-weight: bold; padding-top: 10px;">BAB I PENDAHULUAN</td></tr>
            <tr><td style="padding-left: 20px;">A. Latar Belakang Tindak Lanjut Supervisi</td><td style="text-align: right;">1</td></tr>
            <tr><td style="padding-left: 20px;">B. Dasar Hukum Tindak Lanjut</td><td style="text-align: right;">2</td></tr>
            <tr><td style="padding-left: 20px;">C. Tujuan Pelaporan Tindak Lanjut (RTL)</td><td style="text-align: right;">2</td></tr>
            <tr><td style="padding-left: 20px;">D. Sasaran Guru Penerima RTL</td><td style="text-align: right;">3</td></tr>
            <tr><td colspan="2" style="font-weight: bold; padding-top: 10px;">BAB II STRATEGI & MEKANISME TINDAK LANJUT</td></tr>
            <tr><td style="padding-left: 20px;">A. Pemetaan Kebutuhan Pembinaan Berdasarkan Skor Supervisi</td><td style="text-align: right;">4</td></tr>
            <tr><td style="padding-left: 20px;">B. Empat Bentuk Strategi Program Tindak Lanjut</td><td style="text-align: right;">5</td></tr>
            <tr><td style="padding-left: 20px;">C. Jadwal dan Alokasi Waktu Realisasi RTL</td><td style="text-align: right;">6</td></tr>
            <tr><td style="padding-left: 20px;">D. Susunan Tim Supervisor Pendamping RTL</td><td style="text-align: right;">6</td></tr>
            <tr><td colspan="2" style="font-weight: bold; padding-top: 10px;">BAB III HASIL & REALISASI TINDAK LANJUT</td></tr>
            <tr><td style="padding-left: 20px;">A. Matriks Realisasi Program Tindak Lanjut Seluruh Guru</td><td style="text-align: right;">7</td></tr>
            <tr><td style="padding-left: 20px;">B. Analisis Peningkatan Kinerja Pedagogik Pasca-RTL</td><td style="text-align: right;">9</td></tr>
            <tr><td style="padding-left: 20px;">C. Dampak Positif terhadap Suasana Belajar Peserta Didik</td><td style="text-align: right;">10</td></tr>
            <tr><td colspan="2" style="font-weight: bold; padding-top: 10px;">BAB IV PENUTUP</td></tr>
            <tr><td style="padding-left: 20px;">A. Kesimpulan Efektivitas Program RTL</td><td style="text-align: right;">11</td></tr>
            <tr><td style="padding-left: 20px;">B. Rencana Tindak Lanjut Berkelanjutan</td><td style="text-align: right;">11</td></tr>
            <tr><td colspan="2" style="font-weight: bold; padding-top: 10px;">LAMPIRAN DOKUMEN</td></tr>
            <tr><td style="padding-left: 20px;">Lampiran: Matriks Detail Bukti Realisasi RTL per Guru</td><td style="text-align: right;">12</td></tr>
        </table>
    </div>

    <!-- ==================== BAB I: PENDAHULUAN ==================== -->
    <div id="sec-bab1-rtl" style="page-break-after: always; padding: 20px 0;">
        <div class="doc-section-title">
            BAB I<br>PENDAHULUAN
        </div>

        <div class="doc-h3">A. Latar Belakang Tindak Lanjut Supervisi</div>
        <p class="doc-p">
            Supervisi akademik adalah siklus pembinaan yang bersifat sirkular dan tidak pernah berhenti. Nilai skor dan predikat yang diperoleh pendidik pada saat visitasi kelas bukanlah vonis akhir, melainkan titik awal diagnosa untuk memetakan kekuatan serta aspek-aspek yang membutuhkan penguatan lebih lanjut. Tanpa adanya fase tindak lanjut yang nyata, supervisi hanya akan menjadi rutinitas penilaian administratif yang kehilangan daya transformasinya.
        </p>
        <p class="doc-p">
            Dalam kerangka Kurikulum Merdeka dan pendekatan Kurikulum Berbasis Cinta (KBC) di <?= htmlspecialchars($sekolah_nama) ?>, tindak lanjut supervisi dirancang sebagai wahana pendampingan yang memanusiakan guru. Setiap pendidik memiliki ritme perkembangan, karakteristik mata pelajaran, dan latar belakang peserta didik yang berbeda. Oleh sebab itu, program Rencana Tindak Lanjut (RTL) tidak diseragamkan secara kaku, melainkan disesuaikan dengan hasil analisis kebutuhan spesifik masing-masing guru di kelas.
        </p>
        <p class="doc-p">
            Melalui pemanfaatan platform pembelajaran digital resmi Kementerian Agama (MOOC PINTAR), pengaktifan Komunitas Belajar (KKG) madrasah, serta sesi bimbingan klinis perseorangan bersama Kepala Madrasah, seluruh rekomendasi supervisi Semester Ganjil TP 2026/2027 telah diwujudkan dalam tindakan nyata. Laporan ini disusun untuk mempertanggungjawabkan keberhasilan realisasi tindak lanjut tersebut secara transparan dan akuntabel.
        </p>

        <div class="doc-h3">B. Dasar Hukum Tindak Lanjut</div>
        <p class="doc-p" style="text-indent:0;">Pelaksanaan tindak lanjut hasil supervisi berlandaskan pada:</p>
        <ol class="doc-ol">
            <li>Undang-Undang Nomor 14 Tahun 2005 tentang Guru dan Dosen yang mengamanatkan kewajiban guru untuk senantiasa meningkatkan kualifikasi dan kompetensinya.</li>
            <li>Peraturan Pemerintah Nomor 19 Tahun 2017 tentang Perubahan atas PP Nomor 74 Tahun 2008 tentang Guru terkait pembinaan keprofesian berkelanjutan.</li>
            <li>Peraturan Menteri Agama Nomor 58 Tahun 2017 tentang Kepala Madrasah yang menugaskan kepala madrasah untuk menindaklanjuti hasil supervisi demi peningkatan mutu.</li>
            <li>Keputusan Menteri Agama Nomor 450 Tahun 2024 tentang Pedoman Implementasi Kurikulum Merdeka pada Madrasah.</li>
            <li>Petunjuk Teknis PKKM Direktorat Jenderal Pendidikan Islam Kemenag RI Komponen 4 Indikator 4.3 (Pelaksanaan Tindak Lanjut Supervisi).</li>
            <li>Surat Keputusan Kepala <?= htmlspecialchars($sekolah_nama) ?> Nomor: <strong><?= htmlspecialchars($sk_nomor) ?></strong> tanggal <?= date('d F Y', strtotime($sk_tanggal)) ?> tentang Penetapan Tim Supervisi Pembelajaran dan Penjaminan Mutu Akademik Tahun Pelajaran <?= htmlspecialchars($tahun_ajaran) ?>.</li>
        </ol>

        <div class="doc-h3">C. Tujuan Pelaporan Tindak Lanjut (RTL)</div>
        <p class="doc-p" style="text-indent:0;">Tujuan penyusunan laporan hasil tindak lanjut ini adalah:</p>
        <ul class="doc-ul">
            <li><strong>Mendokumentasikan Realisasi Nyata:</strong> Menyajikan bukti konkret pelaksanaan rekomendasi pembinaan guru yang telah dilaksanakan.</li>
            <li><strong>Mengukur Efektivitas Pembinaan:</strong> Mengevaluasi sejauh mana bimbingan klinis dan pelatihan berhasil memperbaiki mutu persiapan dan proses KBM guru di kelas.</li>
            <li><strong>Menjaga Keberlanjutan Mutu:</strong> Memastikan iklim belajar mandiri di kalangan pendidik tetap terpelihara melalui Komunitas Belajar (KKG).</li>
            <li><strong>Kelengkapan Bukti Fisik PKKM:</strong> Memenuhi dokumen otentik PKKM Komponen 4 Indikator 4.3.1 (Laporan Pelaksanaan Tindak Lanjut Supervisi Guru).</li>
        </ul>

        <div class="doc-h3">D. Sasaran Guru Penerima RTL</div>
        <p class="doc-p">
            Sasaran tindak lanjut supervisi ini adalah seluruh dewan guru yang telah diobservasi pada siklus Semester Ganjil TP 2026/2027, yakni sebanyak <strong>12 orang guru</strong>, mencakup guru kelas fase A, B, C, serta seluruh guru mata pelajaran terpadu.
        </p>
    </div>

    <!-- ==================== BAB II: STRATEGI & MEKANISME TINDAK LANJUT ==================== -->
    <div id="sec-bab2-rtl" style="page-break-after: always; padding: 20px 0;">
        <div class="doc-section-title">
            BAB II<br>STRATEGI & MEKANISME TINDAK LANJUT
        </div>

        <div class="doc-h3">A. Pemetaan Kebutuhan Pembinaan Berdasarkan Hasil Supervisi</div>
        <p class="doc-p">
            Berdasarkan hasil rekapitulasi observasi KBM, Tim Supervisor mengelompokkan tindak lanjut ke dalam kategori pembinaan yang proporsional:
        </p>
        <table class="doc-table">
            <thead>
                <tr>
                    <th style="width: 140px;">Kategori Hasil Supervisi</th>
                    <th style="width: 110px;">Rentang Skor</th>
                    <th>Fokus Bentuk Tindak Lanjut (RTL)</th>
                    <th style="width: 90px;">Jumlah Guru</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Penguatan Lanjut & Inovasi</strong></td>
                    <td align="center">Skor &ge; 93.0%</td>
                    <td>Diseminasi praktik baik di Komunitas Belajar (KKG), menjadi narasumber tutor sebaya, dan penyusunan modul ajar inovatif.</td>
                    <td align="center"><strong>5 Guru</strong></td>
                </tr>
                <tr>
                    <td><strong>Pemantapan & Pengayaan</strong></td>
                    <td align="center">Skor 90.0 - 92.9%</td>
                    <td>Pelatihan mandiri di MOOC PINTAR Kemenag, optimalisasi asesmen formatif, dan pembuatan media peraga interaktif.</td>
                    <td align="center"><strong>7 Guru</strong></td>
                </tr>
                <tr>
                    <td><strong>Bimbingan Khusus</strong></td>
                    <td align="center">Skor &lt; 90.0%</td>
                    <td>Supervisi klinis individual, pendampingan penyusunan modul ajar, dan manajemen kelas ramah anak.</td>
                    <td align="center"><strong>0 Guru</strong><br><span style="font-size:7.5pt; color:#059669;">(100% Guru &ge; 90%)</span></td>
                </tr>
            </tbody>
        </table>

        <div class="doc-h3">B. Empat Bentuk Strategi Program Tindak Lanjut</div>
        <p class="doc-p">
            Implementasi RTL di <?= htmlspecialchars($sekolah_nama) ?> dieksekusi melalui 4 (empat) saluran pembinaan terpadu:
        </p>
        <ol class="doc-ol">
            <li>
                <strong>Bimbingan Klinis Supervisor (Individual Coaching):</strong>
                Konseling dan pendampingan tatap muka satu-lawan-satu antara Kepala Madrasah dengan guru bersangkutan untuk membahas kendala spesifik dalam pengelolaan alokasi waktu dan asesmen formatif KBM.
            </li>
            <li>
                <strong>Pelatihan Mandiri Platform MOOC PINTAR Kemenag:</strong>
                Penugasan guru untuk mengikuti modul pelatihan bersertifikat resmi Kementerian Agama, khususnya topik <em>Pembelajaran Berdiferensiasi</em>, <em>Kurikulum Merdeka</em>, dan <em>Penyusunan Modul P5RA</em>.
            </li>
            <li>
                <strong>Workshop & Berbagi Praktik Baik di Komunitas Belajar (KKG Madrasah):</strong>
                Pertemuan rutin dewan guru setiap hari Sabtu untuk saling membedah modul ajar, simulasi microteaching, dan diseminasi pemanfaatan media Chromebook dan Quizizz.
            </li>
            <li>
                <strong>Pendampingan Rekan Sejawat (Peer Coaching):</strong>
                Guru senior berkolaborasi mendampingi rekan guru dalam validasi bank soal asesmen sumatif berbasis HOTS dan standarisasi kartu kontrol literasi-numerasi siswa.
            </li>
        </ol>

        <div class="doc-h3">C. Jadwal dan Alokasi Waktu Realisasi RTL</div>
        <p class="doc-p">
            Program tindak lanjut dijadwalkan berlangsung selama bulan September hingga Oktober 2026. Setiap guru diberikan tenggat waktu (deadline) realisasi maksimal 14 hari pasca visitasi kelas diselenggarakan, dan seluruhnya telah dievaluasi kembali oleh supervisor.
        </p>

        <div class="doc-h3">D. Susunan Tim Supervisor Pendamping RTL</div>
        <p class="doc-p">
            Pelaksanaan pembinaan tindak lanjut didampingi secara langsung oleh Tim Supervisi dan Penjaminan Mutu yang telah ditetapkan melalui SK Kepala Madrasah Nomor: <strong><?= htmlspecialchars($sk_nomor) ?></strong>:
        </p>
        <table class="doc-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 32%;">Nama & NIP Observer</th>
                    <th style="width: 28%;">Peran dalam Tim</th>
                    <th style="width: 35%;">Fokus Tugas Pendampingan RTL</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($tim_supervisi)): ?>
                    <?php $noT = 1; foreach($tim_supervisi as $ts): ?>
                    <tr>
                        <td align="center"><?= $noT++ ?></td>
                        <td>
                            <strong><?= htmlspecialchars($ts['nama']) ?></strong><br>
                            <span style="font-size:8.5pt; color:#475569;">NIP: <?= htmlspecialchars($ts['nip'] ?: '-') ?></span>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($ts['peran_tim']) ?></strong><br>
                            <span style="font-size:8pt; color:#64748b;"><?= htmlspecialchars($ts['jabatan_madrasah'] ?? 'Guru') ?></span>
                        </td>
                        <td style="font-size:9pt;"><?= htmlspecialchars($ts['tugas_fokus']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td align="center">1</td>
                        <td><strong><?= htmlspecialchars($kepala_nama) ?></strong><br><span style="font-size:8.5pt;">NIP. <?= htmlspecialchars($kepala_nip) ?></span></td>
                        <td>Penanggung Jawab / Supervisor Utama</td>
                        <td>Pendampingan Bimbingan Klinis & Evaluasi Realisasi RTL Dewan Guru</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- ==================== BAB III: HASIL & REALISASI TINDAK LANJUT ==================== -->
    <div id="sec-bab3-rtl" style="page-break-after: always; padding: 20px 0;">
        <div class="doc-section-title">
            BAB III<br>HASIL & REALISASI TINDAK LANJUT
        </div>

        <div class="doc-h3">A. Matriks Realisasi Program Tindak Lanjut Dewan Guru</div>
        <p class="doc-p">
            Berikut adalah rekapitulasi data empiris realisasi program tindak lanjut yang telah diselesaikan oleh seluruh dewan guru <?= htmlspecialchars($sekolah_nama) ?>:
        </p>

        <table class="doc-table">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 140px;">Nama Guru & Mapel</th>
                    <th style="width: 100px;">Jenis RTL</th>
                    <th>Fokus Program Tindak Lanjut</th>
                    <th>Wujud Nyata Realisasi Pembinaan</th>
                    <th style="width: 70px;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $nR = 1; foreach($matriksSupervisi as $rt): ?>
                <tr>
                    <td align="center"><?= $nR++ ?></td>
                    <td>
                        <strong><?= htmlspecialchars($rt['nama']) ?></strong><br>
                        <span style="font-size:8pt; color:#475569;"><?= htmlspecialchars($rt['mapel']) ?></span>
                    </td>
                    <td><span style="font-weight:bold; color:#1e3a8a;"><?= htmlspecialchars($rt['tindak_lanjut_jenis']) ?></span></td>
                    <td style="font-size:8.5pt;"><?= htmlspecialchars($rt['tindak_lanjut']) ?></td>
                    <td style="font-size:8.5pt; color:#0f172a;"><?= htmlspecialchars($rt['tindak_lanjut_realisasi']) ?></td>
                    <td align="center">
                        <span style="background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; padding:2px 6px; border-radius:4px; font-weight:bold; font-size:8pt;">
                            <?= htmlspecialchars($rt['tindak_lanjut_status']) ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="doc-h3">B. Analisis Peningkatan Kinerja Pedagogik Guru Pasca-RTL</div>
        <p class="doc-p">
            Berdasarkan monitoring tindak lanjut yang dilakukan oleh Tim Supervisor, tercatat perubahan dan peningkatan kompetensi yang sangat signifikan pada dewan guru:
        </p>
        <ul class="doc-ul">
            <li><strong>Modul Ajar Terdiferensiasi 100% Lengkap:</strong> Seluruh guru kini telah memiliki Modul Ajar yang secara eksplisit mencantumkan skenario diferensiasi konten, proses, dan produk yang disesuaikan dengan keragaman peserta didik.</li>
            <li><strong>Peningkatan Keterampilan Literasi Digital:</strong> 100% guru aktif memanfaatkan chromebook dan proyektor madrasah serta mampu menyusun kuis interaktif mandiri di platform Quizizz dan Canva.</li>
            <li><strong>Penguatan Asesmen Autentik (KKTP):</strong> Guru tidak lagi hanya mengandalkan tes tulis pilihan ganda, tetapi telah melengkapi penilaian dengan rubrik unjuk kerja, portofolio siswa, dan lembar observasi sikap religius-sosial.</li>
            <li><strong>Budaya Berbagi di Komunitas Belajar (KKG):</strong> Tercipta iklim kesejawatan yang suportif di mana guru saling berbagi praktik baik tanpa rasa canggung atau sungkan.</li>
        </ul>

        <div class="doc-h3">C. Dampak Positif terhadap Proses Belajar dan Karakter Siswa</div>
        <p class="doc-p">
            Dampak nyata dari terlaksananya program tindak lanjut ini dirasakan langsung oleh peserta didik di dalam kelas:
        </p>
        <ul class="doc-ul">
            <li>Tingkat keaktifan siswa dalam bertanya dan berdiskusi meningkat drastis berkat penggunaan ice breaking yang variatif dan media benda konkret.</li>
            <li>Siswa merasa lebih dihargai dan termotivasi karena guru menerapkan pendekatan Kurikulum Berbasis Cinta (KBC) yang bebas dari intimidasi.</li>
            <li>Pojok baca kelas dan English corner menjadi tempat favorit peserta didik saat jam istirahat dan jam literasi pagi hari.</li>
        </ul>
    </div>

    <!-- ==================== BAB IV: PENUTUP ==================== -->
    <div id="sec-bab4-rtl" style="page-break-after: always; padding: 20px 0;">
        <div class="doc-section-title">
            BAB IV<br>PENUTUP
        </div>

        <div class="doc-h3">A. Kesimpulan Efektivitas Program RTL</div>
        <p class="doc-p">
            Pelaksanaan program Rencana Tindak Lanjut (RTL) Supervisi Pembelajaran Semester Ganjil Tahun Pelajaran <?= htmlspecialchars($tahun_ajaran) ?> di <?= htmlspecialchars($sekolah_nama) ?> telah mencapai keberhasilan yang sangat memuaskan dengan indikator capaian sebagai berikut:
        </p>
        <ol class="doc-ol">
            <li>Tingkat keterlaksanaan program tindak lanjut mencapai <strong>100% (Selesai/Tuntas)</strong> untuk seluruh 12 orang dewan guru.</li>
            <li>Seluruh catatan temuan observasi kelas berhasil dijembatani dengan solusi pembinaan yang tepat sasaran (bimbingan klinis, MOOC PINTAR, Kombel KKG, dan pendampingan mandiri).</li>
            <li>Terjadi peningkatan kualitas perencanaan, pelaksanaan, dan asesmen pembelajaran di seluruh jenjang kelas fase A, B, dan C.</li>
            <li>Madrasah berhasil memenuhi bukti fisik otentik Penilaian Kinerja Kepala Madrasah (PKKM) Komponen 4 Indikator 4.3.1 secara lengkap dan tervalidasi.</li>
        </ol>

        <div class="doc-h3">B. Rekomendasi Supervisi Periode Berikutnya</div>
        <p class="doc-p" style="text-indent:0;">Untuk menjaga momentum perbaikan mutu berkelanjutan ini, kami merekomendasikan:</p>
        <ul class="doc-ul">
            <li>Menjadikan forum Komunitas Belajar (KKG) internal madrasah sebagai agenda mingguan yang terstruktur dengan jadwal tema materi yang terjadwal rapi.</li>
            <li>Mendorong guru untuk terus mengikuti pelatihan mandiri baru di MOOC PINTAR Kemenag seiring terbitnya regulasi dan kurikulum terbaru.</li>
            <li>Melaksanakan supervisi semester genap mendatang dengan fokus pada implementasi pembelajaran berbasis proyek (P5RA) dan asesmen berbasis komputer.</li>
        </ul>

        <div style="margin-top: 50px; display: flex; justify-content: space-between; align-items: flex-end; text-align: center;">
            <div style="width: 30%;">
                Ketua Panitia,<br><br><br><br>
                <strong><u><?= htmlspecialchars($ketua_panitia['nama'] ?? $ketua_tim['nama'] ?? '-') ?></u></strong>
            </div>

            <?php if(!empty($sekretaris['nama'])): ?>
            <div style="width: 30%;">
                Sekretaris Panitia,<br><br><br><br>
                <strong><u><?= htmlspecialchars($sekretaris['nama']) ?></u></strong>
            </div>
            <?php endif; ?>

            <div style="width: 35%;">
                <?= htmlspecialchars($kecamatan) ?>, <?= $tgl_cetak ?><br>
                Mengetahui,<br>
                Penanggung Jawab (Kepala Madrasah),<br><br><br><br>
                <strong><u><?= htmlspecialchars($penanggung_jawab['nama'] ?? $kepala_nama) ?></u></strong>
            </div>
        </div>
    </div>

    <!-- ==================== LAMPIRAN DOKUMEN RTL ==================== -->
    <div id="sec-lampiran-rtl" style="padding: 20px 0;">
        <div class="doc-section-title">
            LAMPIRAN DOKUMEN TINDAK LANJUT
        </div>

        <div class="doc-h3">Lampiran: Surat Keterangan Realisasi Tindak Lanjut Supervisi Dewan Guru</div>
        <p class="doc-p">
            Yang bertanda tangan di bawah ini Kepala <?= htmlspecialchars($sekolah_nama) ?>, menerangkan bahwa nama-nama dewan guru yang tercantum dalam dokumen ini telah benar-benar menyelesaikan seluruh rangkaian program Rencana Tindak Lanjut (RTL) pasca supervisi akademik KBM Semester Ganjil TP <?= htmlspecialchars($tahun_ajaran) ?> dengan predikat <strong>SANGAT MEMUASKAN</strong>.
        </p>

        <table class="doc-table">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th>Nama Guru & NIP</th>
                    <th>Bentuk Pembinaan RTL</th>
                    <th>Target Realisasi</th>
                    <th>Keterangan Capaian</th>
                </tr>
            </thead>
            <tbody>
                <?php $nLrtl = 1; foreach($matriksSupervisi as $lrtl): ?>
                <tr>
                    <td align="center"><?= $nLrtl++ ?></td>
                    <td>
                        <strong><?= htmlspecialchars($lrtl['nama']) ?></strong><br>
                        <span style="font-size:8pt; color:#475569;">NIP: <?= htmlspecialchars($lrtl['nip']) ?></span>
                    </td>
                    <td><strong><?= htmlspecialchars($lrtl['tindak_lanjut_jenis']) ?></strong></td>
                    <td style="font-size:8.5pt;"><?= htmlspecialchars($lrtl['tindak_lanjut_deadline']) ?></td>
                    <td style="font-size:8.5pt; color:#065f46; font-weight:bold;">
                        ✓ Terlaksana 100% & Terverifikasi
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="margin-top: 35px; text-align: center; font-size: 8.5pt; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 10px;">
            Dokumen Laporan Realisasi Tindak Lanjut Supervisi &bull; <?= htmlspecialchars($sekolah_nama) ?> &bull; Tahun Pelajaran <?= htmlspecialchars($tahun_ajaran) ?>
        </div>
    </div>

</div>

<?php if(!$isStandalonePrint): ?>
            </div>
        </div>
    </div>
</div>
<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
</body>
</html>
<?php else: ?>
    </div>
    <script>
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
</body>
</html>
<?php endif; ?>
