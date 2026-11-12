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
if (!empty($institusi['kota'])) $alamatParts[] = $institusi['kota'];
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
    <?php $page_title = 'Laporan Hasil Supervisi Pembelajaran'; include __DIR__ . '/../admin/_zamrud_head.php'; ?>
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
                    <span style="color:#0f172a; font-weight:700;">Laporan Hasil Supervisi</span>
                </div>
                <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                    <form method="GET" action="" style="display:inline-flex; align-items:center; gap:6px; background:#fff; padding:5px 10px; border-radius:8px; border:1px solid #cbd5e1;">
                        <span style="font-size:0.78rem; font-weight:700; color:#475569;"><i data-lucide="calendar" style="width:14px;height:14px;vertical-align:middle;color:#059669;"></i> Tgl Dokumen:</span>
                        <input type="date" name="tanggal" value="<?= htmlspecialchars($tgl_raw) ?>" onchange="this.form.submit()" style="border:none; font-family:inherit; font-size:0.8rem; font-weight:600; color:#0f172a; outline:none; background:transparent; cursor:pointer;" title="Pilih tanggal pengesahan dokumen">
                    </form>
                    <a href="/kurikulum/supervisi/tim" class="btn-act btn-white" style="border:1px solid #cbd5e1; padding:8px 14px; font-size:0.82rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                        <i data-lucide="user-check" style="width:15px;height:15px; color:#059669;"></i> Tim Supervisi & SK
                    </a>
                    <a href="/kurikulum/supervisi/laporan-hasil/cetak?tanggal=<?= urlencode($tgl_raw) ?>" target="_blank" class="btn-act btn-white" style="border:1px solid #cbd5e1; padding:8px 14px; font-size:0.82rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                        <i data-lucide="external-link" style="width:15px;height:15px;"></i> Buka Tab Cetak Penuh
                    </a>
                    <a href="/kurikulum/supervisi/laporan-tindak-lanjut" class="btn-act btn-white" style="border:1px solid #cbd5e1; padding:8px 14px; font-size:0.82rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                        <i data-lucide="arrow-right-circle" style="width:15px;height:15px; color:#2563eb;"></i> Laporan Tindak Lanjut (RTL)
                    </a>
                    <button type="button" onclick="window.print()" class="btn-act btn-emerald" style="background:#059669; color:#fff; border:none; padding:8px 18px; border-radius:8px; font-weight:700; font-size:0.85rem; cursor:pointer; display:inline-flex; align-items:center; gap:6px; box-shadow:0 4px 12px rgba(5,150,105,0.25);">
                        <i data-lucide="printer" style="width:16px;height:16px;"></i> Cetak Dokumen Resmi A4
                    </button>
                </div>
            </div>

            <!-- Zamrud Banner -->
            <div class="no-print" style="background:linear-gradient(135deg, #064e3b 0%, #047857 50%, #065f46 100%); color:#ffffff; border-radius:16px; padding:22px 26px; margin-bottom:24px; box-shadow:0 8px 24px -4px rgba(6,78,59,0.25); border:1px solid rgba(255,255,255,0.15);">
                <div style="display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,0.18); padding:3px 10px; border-radius:999px; font-size:0.72rem; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;">
                    <i data-lucide="sparkles" style="width:12px;height:12px;"></i> Standar Juknis Supervisi Kemenag &bull; Bukti Fisik PKKM 4.2.1 Butir b
                </div>
                <h1 style="font-size:1.45rem; font-weight:800; margin:0 0 6px 0; color:#fff; display:flex; align-items:center; gap:10px;">
                    <i data-lucide="file-bar-chart-2" style="width:26px;height:26px;color:#34d399;"></i>
                    Dokumen Laporan Hasil Supervisi Pembelajaran (Akademik Guru)
                </h1>
                <p style="margin:0; font-size:0.88rem; color:rgba(255,255,255,0.9); max-width:850px; line-height:1.5;">
                    Naskah dokumen pelaporan resmi hasil observasi kelas dan telaah perencanaan KBM dewan guru semester ganjil TP 2026/2027. Disusun lengkap dari Halaman Judul, Lembar Pengesahan, Bab I Pendahuluan sampai dengan Bab IV Penutup dan Lampiran Matriks Nilai.
                </p>

                <!-- Quick Navigation Pills -->
                <div style="display:flex; gap:8px; margin-top:16px; flex-wrap:wrap;">
                    <a href="#sec-cover" style="background:rgba(255,255,255,0.15); color:#fff; padding:5px 12px; border-radius:8px; font-size:0.76rem; text-decoration:none; font-weight:700;">Cover & Pengesahan</a>
                    <a href="#sec-bab1" style="background:rgba(255,255,255,0.15); color:#fff; padding:5px 12px; border-radius:8px; font-size:0.76rem; text-decoration:none; font-weight:700;">Bab I Pendahuluan</a>
                    <a href="#sec-bab2" style="background:rgba(255,255,255,0.15); color:#fff; padding:5px 12px; border-radius:8px; font-size:0.76rem; text-decoration:none; font-weight:700;">Bab II Pelaksanaan</a>
                    <a href="#sec-bab3" style="background:rgba(255,255,255,0.15); color:#fff; padding:5px 12px; border-radius:8px; font-size:0.76rem; text-decoration:none; font-weight:700;">Bab III Hasil & Pembahasan</a>
                    <a href="#sec-bab4" style="background:rgba(255,255,255,0.15); color:#fff; padding:5px 12px; border-radius:8px; font-size:0.76rem; text-decoration:none; font-weight:700;">Bab IV Penutup</a>
                    <a href="#sec-lampiran" style="background:rgba(255,255,255,0.15); color:#fff; padding:5px 12px; border-radius:8px; font-size:0.76rem; text-decoration:none; font-weight:700;">Lampiran Rekapitulasi</a>
                </div>
            </div>

            <!-- Document Wrapper Container -->
            <div style="max-width: 920px; margin: 0 auto; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); padding: 40px 50px;">
<?php else: ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Supervisi Pembelajaran - <?= htmlspecialchars($sekolah_nama) ?></title>
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
        <button onclick="window.print()" style="background:#059669; color:#fff; border:none; padding:10px 20px; border-radius:8px; font-weight:bold; cursor:pointer; box-shadow:0 4px 10px rgba(0,0,0,0.15);">Cetak Dokumen A4</button>
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
        border: 4px double #000;
        margin-bottom: 50px;
        page-break-after: always;
        background: #ffffff;
    }
    .doc-cover h1 {
        font-size: 20pt;
        font-weight: 800;
        margin: 0 0 8px 0;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #000;
    }
    .doc-cover h2 {
        font-size: 14pt;
        font-weight: 700;
        margin: 0 0 35px 0;
        color: #334155;
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
        font-size: 9.5pt;
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
    <div id="sec-cover" class="doc-cover">
        <div style="font-size: 12pt; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; color: #475569; margin-bottom: 5px;">
            DOKUMEN PENJAMINAN MUTU PENDIDIKAN &bull; PKKM KOMPONEN 4
        </div>
        <h1>LAPORAN HASIL SUPERVISI PEMBELAJARAN<br>(AKADEMIK GURU)</h1>
        <h2>OBSERVASI KELAS & TELAAH MODUL AJAR KURIKULUM MERDEKA<br>SEMESTER <?= strtoupper($semester) ?> TAHUN PELAJARAN <?= htmlspecialchars($tahun_ajaran) ?></h2>

        <?php if (!empty($institusi['logo'])): ?>
            <img src="/public/uploads/logo/<?= htmlspecialchars($institusi['logo']) ?>" class="doc-cover-logo" alt="Logo Madrasah">
        <?php else: ?>
            <img src="<?= htmlspecialchars($brand['logo_full']) ?>" class="doc-cover-logo" alt="Logo Madrasah">
        <?php endif; ?>

        <div style="margin: 30px auto; display: inline-block; padding: 10px 24px; border-top: 2px solid #000; border-bottom: 2px solid #000; font-weight: bold; font-size: 12pt;">
            STATUS: 100% GURU TUNTAS DISUPERVISI &bull; KATEGORI AMAT BAIK
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
            Dokumen <strong>Laporan Hasil Pelaksanaan Supervisi Pembelajaran (Akademik Guru)</strong> Semester <?= htmlspecialchars($semester) ?> Tahun Pelajaran <?= htmlspecialchars($tahun_ajaran) ?> di lingkungan <strong><?= htmlspecialchars($sekolah_nama) ?></strong> yang meliputi telaah dokumen administrasi perencanaan pembelajaran (Modul Ajar/RPP), pelaksanaan observasi tatap muka proses KBM di kelas dengan 25 indikator terstandarisasi, analisis capaian mutu per aspek, serta rumusan catatan rekomendasi pembinaan guru, telah diperiksa, dievaluasi, dan disahkan sebagai bukti fisik otentik pelaksanaan tugas manajerial dan supervisi kepala madrasah.
        </p>

        <p class="doc-p" style="margin-top: 15px;">
            Dokumen laporan ini sah digunakan sebagai arsip portofolio penjaminan mutu madrasah, berkas audit Pengawas Madrasah Kementerian Agama, dan kelengkapan berkas fisik Penilaian Kinerja Kepala Madrasah (PKKM) Komponen 4 Indikator 4.2.1 Butir b.
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

        <div style="text-align: center; margin-top: 40px; padding: 12px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px;">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data=https://masmifhda.sch.id/kurikulum/supervisi/laporan-hasil" alt="QR Validasi" style="width: 50px; height: 50px; vertical-align: middle;">
            <div style="font-size: 8pt; color: #475569; margin-top: 4px; font-weight: bold;">
                VALIDASI RESMI DIGITAL &bull; KEMENTERIAN AGAMA KABUPATEN LAMPUNG BARAT
            </div>
        </div>
    </div>

    <!-- ==================== KATA PENGANTAR ==================== -->
    <div style="page-break-after: always; padding: 20px 0;">
        <h2 style="text-align: center; font-size: 14pt; font-weight: bold; text-transform: uppercase; margin-bottom: 25px;">
            KATA PENGANTAR
        </h2>
        
        <p class="doc-p">
            Puji dan syukur senantiasa kita panjatkan ke hadirat Allah SWT, Tuhan Yang Maha Kuasa, atas limpahan rahmat, taufik, dan inayah-Nya sehingga penyusunan <strong>Laporan Hasil Supervisi Pembelajaran (Akademik Guru) Semester <?= htmlspecialchars($semester) ?> Tahun Pelajaran <?= htmlspecialchars($tahun_ajaran) ?></strong> di <?= htmlspecialchars($sekolah_nama) ?> ini dapat diselesaikan dengan baik dan tepat waktu.
        </p>

        <p class="doc-p">
            Supervisi akademik merupakan salah satu pilar penjaminan mutu yang sangat strategis dalam ekosistem madrasah. Melalui supervisi yang bersifat kolaboratif, humanis, dan berorientasi pada pengembangan kompetensi pendidik (Kurikulum Berbasis Cinta), kepala madrasah dan tim supervisor mendampingi dewan guru untuk memastikan proses Kegiatan Belajar Mengajar (KBM) terlaksana secara terencana, berpusat pada peserta didik (student-centered), serta mengakomodasi pembelajaran berdiferensiasi sebagaimana amanat Kurikulum Merdeka.
        </p>

        <p class="doc-p">
            Laporan ini menyajikan potret komprehensif mengenai tingkat ketercapaian 4 kategori standar observasi kelas (Persiapan Mengajar, Kegiatan Pendahuluan, Kegiatan Inti Pembelajaran, dan Kegiatan Penutup) yang mencakup 25 indikator terstandarisasi bagi 12 orang dewan guru. Data yang dihimpun secara objektif ini diharapkan menjadi pijakan nyata dalam penyusunan Rencana Tindak Lanjut (RTL) demi peningkatan kualitas lulusan madrasah secara berkelanjutan.
        </p>

        <p class="doc-p">
            Ucapan terima kasih yang tulus kami sampaikan kepada Pengawas Madrasah Pembina, seluruh Dewan Guru, Staf Tata Usaha, dan Komite Madrasah atas dedikasi dan kerja sama yang harmonis. Semoga laporan ini membawa kemaslahatan nyata bagi kemajuan pendidikan di madrasah kita tercinta.
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
            <tr><td style="padding-left: 20px;">A. Latar Belakang</td><td style="text-align: right;">1</td></tr>
            <tr><td style="padding-left: 20px;">B. Landasan Hukum</td><td style="text-align: right;">2</td></tr>
            <tr><td style="padding-left: 20px;">C. Tujuan Pelaporan Supervisi</td><td style="text-align: right;">2</td></tr>
            <tr><td style="padding-left: 20px;">D. Sasaran Supervisi Pembelajaran</td><td style="text-align: right;">3</td></tr>
            <tr><td style="padding-left: 20px;">E. Ruang Lingkup Supervisi</td><td style="text-align: right;">3</td></tr>
            <tr><td colspan="2" style="font-weight: bold; padding-top: 10px;">BAB II KERANGKA KERJA & METODOLOGI SUPERVISI</td></tr>
            <tr><td style="padding-left: 20px;">A. Pendekatan dan Teknik Supervisi</td><td style="text-align: right;">4</td></tr>
            <tr><td style="padding-left: 20px;">B. Tim Supervisor Madrasah</td><td style="text-align: right;">4</td></tr>
            <tr><td style="padding-left: 20px;">C. Jadwal dan Waktu Pelaksanaan Visitasi</td><td style="text-align: right;">5</td></tr>
            <tr><td style="padding-left: 20px;">D. Instrumen Penilaian dan Rubrik Observasi KBM</td><td style="text-align: right;">6</td></tr>
            <tr><td colspan="2" style="font-weight: bold; padding-top: 10px;">BAB III HASIL SUPERVISI & ANALISIS PEMBAHASAN</td></tr>
            <tr><td style="padding-left: 20px;">A. Rekapitulasi Nilai Supervisi Akademik Dewan Guru</td><td style="text-align: right;">7</td></tr>
            <tr><td style="padding-left: 20px;">B. Analisis Capaian Mutu per Kategori Aspek KBM</td><td style="text-align: right;">9</td></tr>
            <tr><td style="padding-left: 20px;">C. Analisis Temuan Kekuatan / Praktik Baik (Best Practice) Guru</td><td style="text-align: right;">10</td></tr>
            <tr><td style="padding-left: 20px;">D. Analisis Kendala dan Aspek yang Perlu Ditingkatkan</td><td style="text-align: right;">11</td></tr>
            <tr><td colspan="2" style="font-weight: bold; padding-top: 10px;">BAB IV PENUTUP</td></tr>
            <tr><td style="padding-left: 20px;">A. Kesimpulan</td><td style="text-align: right;">12</td></tr>
            <tr><td style="padding-left: 20px;">B. Rekomendasi Tindak Lanjut</td><td style="text-align: right;">12</td></tr>
            <tr><td colspan="2" style="font-weight: bold; padding-top: 10px;">LAMPIRAN-LAMPIRAN</td></tr>
            <tr><td style="padding-left: 20px;">Lampiran 1: Matriks Nilai Observasi KBM Guru Lengkap</td><td style="text-align: right;">13</td></tr>
            <tr><td style="padding-left: 20px;">Lampiran 2: Format Blangko Asli Instrumen Observasi (25 Butir)</td><td style="text-align: right;">14</td></tr>
        </table>
    </div>

    <!-- ==================== BAB I: PENDAHULUAN ==================== -->
    <div id="sec-bab1" style="page-break-after: always; padding: 20px 0;">
        <div class="doc-section-title">
            BAB I<br>PENDAHULUAN
        </div>

        <div class="doc-h3">A. Latar Belakang</div>
        <p class="doc-p">
            Pendidikan madrasah pada era transformasi digital dan implementasi Kurikulum Merdeka menuntut pergeseran paradigma pembelajaran yang mendasar. Pendidik tidak lagi diposisikan sebagai satu-satunya sumber pengetahuan, melainkan sebagai fasilitator yang menginspirasi, mendampingi, dan menumbuhkembangkan potensi kodrat alam dan kodrat zaman peserta didik. Kualitas proses Kegiatan Belajar Mengajar (KBM) yang berlangsung di dalam ruang kelas menjadi tolok ukur utama keberhasilan madrasah dalam mencetak generasi yang berilmu amaliah, beramal ilmiah, serta berakhlak mulia.
        </p>
        <p class="doc-p">
            Untuk memastikan bahwa standar proses pembelajaran terpelihara dengan prima, diperlukan sistem pengawasan dan pendampingan internal yang terencana, terstruktur, dan berkesinambungan. Supervisi akademik yang dilaksanakan oleh Kepala Madrasah bersama Tim Supervisor bukanlah sarana untuk mencari kesalahan (inspeksi otokratis), melainkan sebuah layanan bantuan profesional (clinical supervision) yang humanis. Pendekatan ini selaras dengan prinsip Kurikulum Berbasis Cinta (KBC) yang diterapkan di <?= htmlspecialchars($sekolah_nama) ?>, di mana pembinaan guru dilakukan atas dasar empati, keteladanan, dan semangat kebersamaan demi kemajuan bersama.
        </p>
        <p class="doc-p">
            Pelaksanaan supervisi pembelajaran Semester <?= htmlspecialchars($semester) ?> Tahun Pelajaran <?= htmlspecialchars($tahun_ajaran) ?> telah tuntas diselenggarakan untuk seluruh dewan guru (100%). Seluruh rangkaian tahapan, mulai dari telaah kelengkapan Modul Ajar/RPP, observasi pelaksanaan pembelajaran langsung di kelas, hingga refleksi pasca-observasi telah terdokumentasikan secara digital dan fisik. Laporan ini disusun sebagai bentuk akuntabilitas publik dan evaluasi internal madrasah dalam rangka penjaminan mutu serta pemenuhan bukti fisik otentik Penilaian Kinerja Kepala Madrasah (PKKM).
        </p>

        <div class="doc-h3">B. Landasan Hukum</div>
        <p class="doc-p" style="text-indent:0;">Penyusunan program dan laporan hasil supervisi ini didasarkan pada regulasi perundang-undangan pendidikan nasional dan Kementerian Agama RI:</p>
        <ol class="doc-ol">
            <li>Undang-Undang Republik Indonesia Nomor 20 Tahun 2003 tentang Sistem Pendidikan Nasional.</li>
            <li>Undang-Undang Republik Indonesia Nomor 14 Tahun 2005 tentang Guru dan Dosen.</li>
            <li>Peraturan Pemerintah Nomor 57 Tahun 2021 tentang Standar Nasional Pendidikan sebagaimana telah diubah dengan Peraturan Pemerintah Nomor 4 Tahun 2022.</li>
            <li>Peraturan Menteri Agama (PMA) Republik Indonesia Nomor 58 Tahun 2017 tentang Kepala Madrasah yang menegaskan fungsi kepala madrasah sebagai supervisor pembelajaran.</li>
            <li>Keputusan Menteri Agama (KMA) Republik Indonesia Nomor 450 Tahun 2024 tentang Pedoman Implementasi Kurikulum Merdeka pada Madrasah.</li>
            <li>Keputusan Direktur Jenderal Pendidikan Islam Kementerian Agama RI tentang Petunjuk Teknis Supervisi Pembelajaran pada Madrasah.</li>
            <li>Keputusan Direktur Jenderal Pendidikan Islam Kementerian Agama RI tentang Petunjuk Teknis Penilaian Kinerja Kepala Madrasah (PKKM).</li>
            <li>Surat Keputusan (SK) Kepala Madrasah Nomor <?= htmlspecialchars($sk_nomor ?? 'MA.10.04/PP.01.1/045/SK-TIM-SPV/VII/2026') ?> tentang Penetapan Tim Supervisi Pembelajaran dan Penilai Internal TP <?= htmlspecialchars($tahun_ajaran) ?>.</li>
            <li>Rencana Kerja Tahunan (RKT) dan Kurikulum Operasional Madrasah (KOM) <?= htmlspecialchars($sekolah_nama) ?> Tahun Pelajaran <?= htmlspecialchars($tahun_ajaran) ?>.</li>
        </ol>

        <div class="doc-h3">C. Tujuan Pelaporan Supervisi</div>
        <p class="doc-p" style="text-indent:0;">Tujuan utama disusunnya laporan hasil supervisi pembelajaran ini adalah:</p>
        <ul class="doc-ul">
            <li><strong>Menyajikan Data Faktual Capaian Guru:</strong> Mendokumentasikan skor riil dan predikat ketercapaian 4 aspek standar observasi kelas bagi seluruh dewan guru.</li>
            <li><strong>Identifikasi Praktik Baik (Best Practice):</strong> Menginventarisir metode pembelajaran kreatif, media TIK inovatif, dan diferensiasi materi yang berhasil dikembangkan guru untuk didiseminasikan dalam Komunitas Belajar (KKG).</li>
            <li><strong>Mendeteksi Akar Permasalahan KBM:</strong> Menemukan kendala pedagogik maupun sarana yang dialami pendidik di kelas sebagai dasar pemberian solusi yang tepat.</li>
            <li><strong>Landasan Penyusunan RTL:</strong> Menjadi rujukan utama penyusunan program Rencana Tindak Lanjut (RTL) yang terarah (bimbingan klinis, pelatihan MOOC PINTAR, workshop, dan pendampingan sejawat).</li>
            <li><strong>Pemenuhan Bukti Fisik PKKM:</strong> Menjadi bukti fisik otentik Komponen 4 Indikator 4.2.1 Butir b (Laporan Hasil Supervisi) yang terverifikasi pengawas.</li>
        </ul>

        <div class="doc-h3">D. Sasaran Supervisi Pembelajaran</div>
        <p class="doc-p">
            Sasaran pelaksanaan supervisi pembelajaran ini mencakup seluruh tenaga pendidik di <?= htmlspecialchars($sekolah_nama) ?>, yaitu sebanyak <strong><?= $totalGuru ?> orang guru</strong> yang terdiri atas Guru Kelas (Fase A, Fase B, dan Fase C), Guru Pendidikan Agama Islam (PAI), Guru Bahasa Arab, Guru Pendidikan Jasmani (PJOK), Guru Baca Tulis Al-Qur'an (BQ), Guru Bahasa Inggris, serta Guru TIK/Multimedia.
        </p>

        <div class="doc-h3">E. Ruang Lingkup Supervisi</div>
        <p class="doc-p">
            Ruang lingkup supervisi pembelajaran difokuskan pada 2 (dua) instrumen utama: (1) <strong>Telaah Administrasi Perencanaan Pembelajaran</strong> (Analisis Minggu Efektif, ATP, kelengkapan Modul Ajar/RPP, buku nilai, dan jurnal KBM); serta (2) <strong>Observasi Tatap Muka Pelaksanaan KBM di Kelas</strong> yang mengukur 25 indikator terstandarisasi dalam 4 kategori aspek (Persiapan Mengajar, Kegiatan Pendahuluan, Kegiatan Inti Pembelajaran, dan Kegiatan Penutup).
        </p>
    </div>

    <!-- ==================== BAB II: KERANGKA KERJA & METODOLOGI ==================== -->
    <div id="sec-bab2" style="page-break-after: always; padding: 20px 0;">
        <div class="doc-section-title">
            BAB II<br>KERANGKA KERJA & METODOLOGI SUPERVISI
        </div>

        <div class="doc-h3">A. Pendekatan dan Teknik Supervisi</div>
        <p class="doc-p">
            Pelaksanaan supervisi di <?= htmlspecialchars($sekolah_nama) ?> menerapkan pendekatan <strong>Supervisi Kolaboratif dan Humanis</strong> yang terbagi dalam tiga siklus terpadu:
        </p>
        <ol class="doc-ol">
            <li><strong>Pra-Observasi (Pre-Observation Conference):</strong> Pertemuan awal antara supervisor dan guru untuk membahas kesiapan Modul Ajar, tujuan KBM yang ingin dicapai, metode yang digunakan, serta fokus pengamatan yang disepakati.</li>
            <li><strong>Observasi Kelas (Classroom Observation):</strong> Pengamatan langsung supervisor di dalam kelas saat guru melaksanakan KBM. Supervisor mencatat fakta empiris, interaksi siswa, penggunaan media ajar, dan mengisikan skor pada 25 butir instrumen tanpa menginterupsi jalannya pelajaran.</li>
            <li><strong>Pasca-Observasi (Post-Observation Conference):</strong> Dialog reflektif pasca-mengajar di mana guru diajak mengevaluasi sendiri kelebihan dan kendala yang dihadapi, disusul pemberian umpan balik konstruktif dan rekomendasi perbaikan dari supervisor.</li>
        </ol>

        <div class="doc-h3">B. Tim Supervisor Madrasah</div>
        <p class="doc-p">
            Supervisi akademik dilaksanakan oleh Tim Supervisor internal madrasah yang dibentuk melalui Surat Keputusan (SK) Kepala Madrasah Nomor <strong><?= htmlspecialchars($sk_nomor ?? 'MA.10.04/PP.01.1/045/SK-TIM-SPV/VII/2026') ?></strong>, dengan susunan tim dan pembagian tugas pengawasan sebagai berikut:
        </p>
        <table class="doc-table">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th style="width: 32%;">Nama & NIP Observer</th>
                    <th style="width: 28%;">Peran dalam Tim</th>
                    <th style="width: 35%;">Fokus Tugas Pengawasan</th>
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
                        <td>Seluruh Dewan Guru Kelas & Guru Mapel</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="doc-h3">C. Jadwal Pelaksanaan Supervisi</div>
        <p class="doc-p">
            Visitasi observasi kelas Semester Ganjil TP 2026/2027 telah dijadwalkan secara transparan dan dilaksanakan mulai pekan kedua September hingga pekan pertama Oktober 2026. Jadwal dirancang agar tidak mengganggu ketertiban kalender akademik madrasah dan memberikan kesempatan bagi guru mempersiapkan modul ajar terbaiknya.
        </p>

        <div class="doc-h3">D. Instrumen Penilaian dan Rubrik Observasi (25 Butir)</div>
        <p class="doc-p">
            Setiap pendidik diobservasi menggunakan instrumen rubrik penilaian berbobot 25 indikator terstandarisasi dengan skala skor 1 sampai dengan 5 (Skor Maksimal = 125). Pembagian kategori aspek mencakup:
        </p>
        <ul class="doc-ul">
            <li><strong>Kategori I. Persiapan Mengajar (6 Indikator / Skor Maks 30):</strong> Analisis Minggu Efektif/Prota/Promes, ATP, Modul Ajar/RPP, Media Peraga, Daftar Nilai Asesmen, dan Buku Hadir/Jurnal KBM.</li>
            <li><strong>Kategori II. Kegiatan Pendahuluan (5 Indikator / Skor Maks 25):</strong> Kesiapan dan kebersihan siswa, penyampaian tujuan kompetensi, penyampaian alur asesmen, apersepsi kontekstual, dan asesmen awal diagnostik (pretes).</li>
            <li><strong>Kategori III. Kegiatan Inti Pembelajaran (11 Indikator / Skor Maks 55):</strong> Penyampaian materi kontekstual, pemanfaatan media ajar interaktif/TIK, penguasaan materi, contoh nyata, pembelajaran aktif berdiferensiasi, ketepatan metode KBM, manajemen alokasi waktu, fasilitas ruang kelas, keaktifan siswa bertanya/menalar, asesmen formatif saat KBM, dan umpan balik penguatan respon siswa.</li>
            <li><strong>Kategori IV. Kegiatan Penutup (3 Indikator / Skor Maks 15):</strong> Bimbingan menyimpulkan materi bersama siswa, postes/refleksi akhir ketercapaian tujuan, dan pemberian tindak lanjut penugasan bermakna serta penguatan karakter Islami.</li>
        </ul>

        <p class="doc-p" style="margin-top: 8px;">
            Penetapan Kualifikasi Nilai Akhir (Nilai = [Total Skor / 125] x 100) berpedoman pada kriteria standar:
            <br>&bull; <strong>Amat Baik (A):</strong> 91.0 s.d 100.0% &nbsp;&nbsp;|&nbsp;&nbsp; &bull; <strong>Baik (B):</strong> 81.0 s.d 90.9%
            <br>&bull; <strong>Cukup (C):</strong> 71.0 s.d 80.9% &nbsp;&nbsp;|&nbsp;&nbsp; &bull; <strong>Kurang (D):</strong> &lt; 71.0%
        </p>
    </div>

    <!-- ==================== BAB III: HASIL SUPERVISI & PEMBAHASAN ==================== -->
    <div id="sec-bab3" style="page-break-after: always; padding: 20px 0;">
        <div class="doc-section-title">
            BAB III<br>HASIL SUPERVISI & ANALISIS PEMBAHASAN
        </div>

        <div class="doc-h3">A. Rekapitulasi Nilai Hasil Observasi KBM Dewan Guru</div>
        <p class="doc-p">
            Berdasarkan visitasi observasi KBM tatap muka yang dilakukan terhadap 12 orang dewan guru pada Semester Ganjil TP 2026/2027, diperoleh data rekapitulasi nilai komprehensif sebagai berikut:
        </p>

        <table class="doc-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 25px;">No</th>
                    <th rowspan="2">Nama Guru & NIP</th>
                    <th rowspan="2">Mata Pelajaran & Kelas</th>
                    <th colspan="4">Skor per Aspek (%)</th>
                    <th rowspan="2" style="width: 48px;">Nilai Akhir</th>
                    <th rowspan="2" style="width: 80px;">Predikat</th>
                </tr>
                <tr>
                    <th style="width: 32px;">P1</th>
                    <th style="width: 32px;">P2</th>
                    <th style="width: 32px;">P3</th>
                    <th style="width: 32px;">P4</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                foreach($matriksSupervisi as $g): 
                ?>
                <tr>
                    <td align="center"><?= $no++ ?></td>
                    <td>
                        <strong><?= htmlspecialchars($g['nama']) ?></strong><br>
                        <span style="font-size: 8pt; color: #475569;">NIP: <?= htmlspecialchars($g['nip']) ?></span>
                    </td>
                    <td><?= htmlspecialchars($g['mapel']) ?></td>
                    <td align="center"><?= $g['persiapan'] ?></td>
                    <td align="center"><?= $g['pendahuluan'] ?></td>
                    <td align="center"><?= $g['inti'] ?></td>
                    <td align="center"><?= $g['penutup'] ?></td>
                    <td align="center" style="font-weight: bold; color: #064e3b;"><?= $g['rata'] ?></td>
                    <td align="center" style="font-size: 8.5pt; font-weight: bold;">
                        <?= $g['predikat'] ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background: #f8fafc; font-weight: bold;">
                    <td colspan="3" align="right" style="padding-right: 8px;">RATA-RATA KESELURUHAN DEWAN GURU:</td>
                    <td align="center"><?= $avgPersiapan ?></td>
                    <td align="center"><?= $avgPendahuluan ?></td>
                    <td align="center"><?= $avgInti ?></td>
                    <td align="center"><?= $avgPenutup ?></td>
                    <td align="center" style="font-size: 11pt; color: #064e3b; font-weight: 800;"><?= $rataRataTotal ?></td>
                    <td align="center" style="color: #064e3b; font-size: 9pt;"><?= $predikatRataRata ?></td>
                </tr>
            </tfoot>
        </table>

        <div style="font-size: 8.5pt; color: #475569; margin-top: -8px; margin-bottom: 16px;">
            <em>Keterangan Aspek: P1 = Persiapan Mengajar (6 Indikator) &bull; P2 = Kegiatan Pendahuluan (5 Indikator) &bull; P3 = Kegiatan Inti KBM (11 Indikator) &bull; P4 = Kegiatan Penutup (3 Indikator).</em>
        </div>

        <div class="doc-h3">B. Analisis Capaian Mutu per Kategori Aspek KBM</div>
        <p class="doc-p">
            Dari data rekapitulasi di atas, seluruh aspek observasi menunjukkan tingkat pencapaian di atas ambang batas standar (Kriteria Minimal Baik &ge; 80.0%). Rincian analisis ketercapaian masing-masing kategori dijabarkan sebagai berikut:
        </p>

        <ol class="doc-ol">
            <li>
                <strong>Aspek Persiapan Mengajar (Rata-rata: <?= $avgPersiapan ?>% &bull; Amat Baik):</strong>
                Seluruh guru (100%) telah melengkapi perangkat ajar utama, meliputi Analisis Minggu Efektif, ATP, Modul Ajar/RPP, buku nilai asesmen, dan daftar hadir siswa. Guru telah mengadopsi struktur Kurikulum Merdeka dengan rumusan Capaian Pembelajaran (CP) yang dijabarkan ke Alur Tujuan Pembelajaran (ATP) secara logis.
            </li>
            <li>
                <strong>Aspek Kegiatan Pendahuluan (Rata-rata: <?= $avgPendahuluan ?>% &bull; Amat Baik):</strong>
                Kesiapan kelas, kebersihan, kerapian pakaian, dan pengkondisian psikologis peserta didik berlangsung sangat baik. Guru secara konsisten membuka pelajaran dengan salam, doa bersama, menyanyikan lagu nasional/mars madrasah, serta melakukan apersepsi kontekstual yang mengaitkan materi dengan kehidupan sehari-hari anak.
            </li>
            <li>
                <strong>Aspek Kegiatan Inti Pembelajaran (Rata-rata: <?= $avgInti ?>% &bull; Amat Baik):</strong>
                Kegiatan inti merupakan aspek dengan indikator terbanyak (11 butir). Pendidik menunjukkan penguasaan materi yang matang, penerapan model pembelajaran kooperatif aktif, pemanfaatan alat peraga benda konkrit dan perangkat digital (Chromebook / Proyektor), serta memfasilitasi diferensiasi belajar siswa.
            </li>
            <li>
                <strong>Aspek Kegiatan Penutup (Rata-rata: <?= $avgPenutup ?>% &bull; Amat Baik):</strong>
                Guru membimbing siswa merangkum poin-poin esensial materi, menyelenggarakan postes kuis interaktif, menyampaikan pesan moral penguatan karakter Islami, serta memberikan penugasan terstruktur yang bermakna.
            </li>
        </ol>

        <div class="doc-h3">C. Analisis Temuan Kekuatan / Praktik Baik (Best Practice) Dewan Guru</div>
        <p class="doc-p">
            Selama proses pengamatan KBM di kelas, Tim Supervisor mencatat sejumlah kekuatan dan praktik baik yang sangat menonjol pada dewan guru <?= htmlspecialchars($sekolah_nama) ?>:
        </p>
        <ul class="doc-ul">
            <li><strong>Penerapan Kurikulum Berbasis Cinta (KBC):</strong> Interaksi guru dan murid berlangsung dengan nada tutur santun, penuh kehangatan, apresiatif, dan tanpa hukuman fisik maupun verbal (kelas ramah anak).</li>
            <li><strong>Pemanfaatan Sarana Digital & Multimedia:</strong> Guru TIK, Bahasa Arab, PAI, dan Guru Kelas aktif mengintegrasikan media Chromebook madrasah, kuis interaktif Quizizz, serta visualisasi video edukatif interaktif.</li>
            <li><strong>Pengelolaan Pojok Baca Kelas:</strong> Guru kelas rendah (Kelas 1, 2, dan 3) berhasil mengoptimalkan pojok baca kelas sehingga gerakan literasi dan minat baca anak usia dini berkembang pesat.</li>
            <li><strong>Pembelajaran Berdiferensiasi:</strong> Guru telah mengakomodasi keragaman gaya belajar siswa (visual, auditori, kinestetik) melalui lembar aktivitas bertingkat dan penugasan kelompok yang fleksibel.</li>
        </ul>

        <div class="doc-h3">D. Analisis Kendala dan Aspek yang Perlu Ditingkatkan</div>
        <p class="doc-p">
            Meskipun secara umum meraih predikat Amat Baik, terdapat beberapa aspek operasional yang memerlukan penyempurnaan pada siklus tindak lanjut:
        </p>
        <ul class="doc-ul">
            <li><strong>Variasi Instrumen Asesmen Formatif:</strong> Sebagian guru masih mengandalkan pertanyaan lisan secara klasikal dan perlu diperkaya dengan lembar observasi mandiri (self-assessment) serta rubrik checklist formatif harian.</li>
            <li><strong>Pengelolaan Alokasi Waktu:</strong> Pada beberapa sesi pembelajaran tematik, kegiatan inti berlangsung melebihi perkiraan sehingga fase refleksi penutup terkesan terburu-buru.</li>
            <li><strong>Pengembangan Soal Bernalar Kritis (HOTS):</strong> Diperlukan pembiasaan penyusunan instrumen soal kontekstual berbasis studi kasus, terutama bagi kelas atas dalam persiapan menghadapi Asesmen Madrasah (AM).</li>
        </ul>
    </div>

    <!-- ==================== BAB IV: PENUTUP ==================== -->
    <div id="sec-bab4" style="page-break-after: always; padding: 20px 0;">
        <div class="doc-section-title">
            BAB IV<br>PENUTUP
        </div>

        <div class="doc-h3">A. Kesimpulan</div>
        <p class="doc-p">
            Berdasarkan keseluruhan rangkaian kegiatan supervisi akademik yang dilaksanakan pada Semester Ganjil Tahun Pelajaran <?= htmlspecialchars($tahun_ajaran) ?> di <?= htmlspecialchars($sekolah_nama) ?>, dapat diambil kesimpulan sebagai berikut:
        </p>
        <ol class="doc-ol">
            <li>Pelaksanaan supervisi pembelajaran telah terlaksana secara menyeluruh (100%) menjangkau seluruh 12 orang dewan guru tanpa kendala yang berarti.</li>
            <li>Tingkat ketercapaian kompetensi pedagogik guru dalam KBM berada pada kategori <strong>AMAT BAIK</strong> dengan rata-rata skor perolehan sebesar <strong><?= $rataRataTotal ?>%</strong>.</li>
            <li>Seluruh 4 kategori aspek penilaian (Persiapan Mengajar <?= $avgPersiapan ?>%, Pendahuluan <?= $avgPendahuluan ?>%, Inti Pembelajaran <?= $avgInti ?>%, dan Penutup <?= $avgPenutup ?>%) berada di atas ambang batas mutu yang ditetapkan.</li>
            <li>Iklim pembelajaran madrasah terpelihara kondusif, interaktif, religius, dan ramah anak dengan implementasi Kurikulum Merdeka yang kontekstual.</li>
        </ol>

        <div class="doc-h3">B. Rekomendasi</div>
        <p class="doc-p" style="text-indent:0;">Guna mempertahankan dan meningkatkan mutu pembelajaran di masa mendatang, Tim Supervisor merekomendasikan langkah strategis berikut:</p>
        <ul class="doc-ul">
            <li><strong>Bagi Dewan Guru:</strong> Segera menindaklanjuti catatan rekomendasi hasil supervisi melalui keikutsertaan aktif dalam program bimbingan klinis, pelatihan MOOC PINTAR Kemenag, dan forum Komunitas Belajar (KKG) madrasah.</li>
            <li><strong>Bagi Tim Kurikulum:</strong> Menyelenggarakan In-House Training (IHT) berkala dengan tema penulisan modul ajar berdiferensiasi dan pembuatan rubrik asesmen autentik (KKTP).</li>
            <li><strong>Bagi Kepala Madrasah:</strong> Melakukan pemantauan realisasi program Rencana Tindak Lanjut (RTL) secara berkala dan memastikan ketersediaan sarana prasarana pendukung KBM digital.</li>
            <li><strong>Bagi Pengawas Madrasah:</strong> Terus memberikan bimbingan, supervisi manajerial, dan arahan kebijakan strategis demi kemajuan mutu madrasah.</li>
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

    <!-- ==================== LAMPIRAN DOKUMEN ==================== -->
    <div id="sec-lampiran" style="padding: 20px 0;">
        <div class="doc-section-title">
            LAMPIRAN DOKUMEN SUPERVISI
        </div>

        <div class="doc-h3">Lampiran 1: Matriks Catatan Temuan & Rekomendasi Pembinaan Dewan Guru</div>
        <table class="doc-table">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 150px;">Nama Guru & Mapel</th>
                    <th style="width: 50px;">Nilai</th>
                    <th>Catatan Kekuatan & Temuan Lapangan</th>
                    <th>Rekomendasi Tindak Lanjut (RTL)</th>
                </tr>
            </thead>
            <tbody>
                <?php $nLamp = 1; foreach($matriksSupervisi as $lm): ?>
                <tr>
                    <td align="center"><?= $nLamp++ ?></td>
                    <td>
                        <strong><?= htmlspecialchars($lm['nama']) ?></strong><br>
                        <span style="font-size:8pt; color:#475569;"><?= htmlspecialchars($lm['mapel']) ?></span>
                    </td>
                    <td align="center" style="font-weight:bold; color:#064e3b;"><?= $lm['rata'] ?></td>
                    <td style="font-size: 8.5pt;"><?= htmlspecialchars($lm['kekuatan'] ?? $lm['catatan']) ?></td>
                    <td style="font-size: 8.5pt;">
                        <strong><?= htmlspecialchars($lm['tindak_lanjut_jenis']) ?>:</strong><br>
                        <?= htmlspecialchars($lm['tindak_lanjut']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="margin-top: 35px; text-align: center; font-size: 8.5pt; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 10px;">
            Dokumen Laporan Hasil Supervisi Pembelajaran &bull; <?= htmlspecialchars($sekolah_nama) ?> &bull; Tahun Pelajaran <?= htmlspecialchars($tahun_ajaran) ?>
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
