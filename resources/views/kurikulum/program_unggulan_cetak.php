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
if (!empty($institusi['kota'])) $alamatParts[] = 'Kab. ' . $institusi['kota'];
if (!empty($institusi['provinsi'])) $alamatParts[] = 'Prov. ' . $institusi['provinsi'];
$alamatLengkap = implode(', ', $alamatParts);
if (empty($alamatLengkap)) {
    $alamatLengkap = 'Jl. Tirtomoyo No. 083, Margorahayu, Ds. Tambak Jaya, Kec. Way Tenong, Kab. Lampung Barat, Prov. Lampung';
}

$webUrl = !empty($institusi['website']) ? $institusi['website'] : 'masmifhda.sch.id';
$emailAddr = !empty($institusi['email']) ? $institusi['email'] : 'masmiftahulhuda@gmail.com';
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
    <style>
        @page {
            size: A4 portrait;
            margin: 2cm 2cm 2cm 2.5cm;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0 !important; background: #fff !important; }
            .page-break { page-break-before: always; }
            .container { box-shadow: none !important; margin: 0 !important; width: 100% !important; max-width: 100% !important; padding: 0 !important; }
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            background: #cbd5e1;
            margin: 0;
            padding: 30px 10px;
        }
        .container {
            max-width: 210mm;
            min-height: 297mm;
            margin: 0 auto 30px auto;
            background: #ffffff;
            padding: 2.2cm 2.2cm 2.2cm 2.8cm;
            box-sizing: border-box;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            position: relative;
        }
        /* COVER PAGE STYLES */
        .cover-wrapper {
            border: 4px double #064e3b;
            padding: 25px 20px;
            min-height: 252mm;
            box-sizing: border-box;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .cover-header {
            text-transform: uppercase;
            font-weight: bold;
            font-size: 11pt;
            letter-spacing: 1px;
            color: #0f172a;
            line-height: 1.4;
        }
        .cover-title {
            margin: 30px 0;
        }
        .cover-title h1 {
            font-size: 18pt;
            font-weight: 900;
            color: #064e3b;
            margin: 0 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .cover-title h2 {
            font-size: 13pt;
            font-weight: bold;
            color: #1e293b;
            margin: 0 0 14px 0;
            text-transform: uppercase;
        }
        .cover-title p {
            font-size: 10.5pt;
            font-style: italic;
            color: #334155;
            max-width: 80%;
            margin: 0 auto;
            line-height: 1.4;
            text-align: center;
        }
        .cover-footer {
            text-transform: uppercase;
            font-weight: bold;
            font-size: 10.5pt;
            color: #0f172a;
            line-height: 1.45;
            border-top: 2px solid #064e3b;
            padding-top: 15px;
        }
        /* STANDARD DOCUMENT STYLES */
        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 22px;
            text-align: center;
        }
        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }
        .kop-table td {
            vertical-align: middle;
        }
        .kop-yayasan {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .kop-madrasah {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 2px 0;
            letter-spacing: 0.5px;
        }
        .kop-status {
            font-size: 9.5pt;
            font-weight: bold;
            margin: 0;
        }
        .kop-alamat {
            font-size: 8.5pt;
            margin-top: 3px;
            line-height: 1.3;
        }
        .doc-title {
            text-align: center;
            margin-bottom: 22px;
        }
        .doc-title h1 {
            font-size: 12.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            text-decoration: underline;
        }
        .doc-title .sub-title {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 4px;
            text-transform: uppercase;
        }
        .doc-title .sk-no {
            font-size: 10.5pt;
            margin-top: 3px;
        }
        h2.bab-heading {
            font-size: 11.5pt;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1.5px solid #000;
            padding-bottom: 4px;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        h3.sub-heading {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 12px;
            margin-bottom: 6px;
        }
        p, li {
            text-align: justify;
            margin-top: 4px;
            margin-bottom: 6px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 16px 0;
            font-size: 10pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }
        table.data-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .ttd-table td {
            vertical-align: top;
            text-align: center;
            font-size: 10.5pt;
        }
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
        .btn-print:hover {
            background: #047857;
        }
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

<!-- FLOATING PRINT CONTROLS -->
<div class="floating-bar no-print">
    <button onclick="window.print()" class="btn-print">
        🖨️ Cetak Dokumen PDF (A4)
    </button>
    <a href="/kurikulum/program-unggulan" class="btn-back">
        ⬅ Kembali ke Halaman Utama
    </a>
</div>

<!-- ========================================================================= -->
<!-- HALAMAN 1: COVER DEPAN RESMI                                             -->
<!-- ========================================================================= -->
<div class="container">
    <div class="cover-wrapper">
        <div class="cover-header">
            KEMENTERIAN AGAMA REPUBLIK INDONESIA<br>
            KANTOR KEMENTERIAN AGAMA KABUPATEN LAMPUNG BARAT<br>
            <?= htmlspecialchars($nama_yayasan) ?><br>
            <strong style="font-size: 13pt; color: #064e3b;"><?= htmlspecialchars($sekolah_nama) ?></strong>
        </div>

        <div>
            <div style="margin: 25px auto;">
                <img src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo Madrasah" style="width: 125px; height: 125px; object-fit: contain;">
            </div>

            <div class="cover-title">
                <h1>BUKU PEDOMAN & BLUEPRINT<br>TIGA PROGRAM UNGGULAN</h1>
                <h2>MADRASAH ALIYAH MIFTAHUL HUDA</h2>
                <div style="font-size: 12pt; font-weight: bold; color: #0f172a; margin-bottom: 8px;">
                    1. MADRASAH BERBASIS PESANTREN & ROHIS<br>
                    2. MADRASAH BERBASIS LIFE SKILL<br>
                    3. MADRASAH BERBASIS DIGITAL
                </div>
                <div style="font-size: 11pt; font-weight: bold; margin-bottom: 12px;">
                    TAHUN PELAJARAN <?= htmlspecialchars($tahun_ajaran) ?>
                </div>
                <p>
                    "Harmonisasi Pendidikan Karakter Qur'ani, Kemandirian Wirausaha Santri melalui Kejuruan Vokasi, serta Penguasaan Teknologi Cerdas Berbasis Sistem Modern Menuju Era AI 5.0"
                </p>
            </div>
        </div>

        <div class="cover-footer">
            TIM PENGEMBANG KURIKULUM & INOVASI MADRASAH<br>
            MADRASAH ALIYAH MIFTAHUL HUDA<br>
            KECAMATAN WAY TENONG, KABUPATEN LAMPUNG BARAT<br>
            PROVINSI LAMPUNG &bull; TAHUN 2026
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- HALAMAN 2: LEMBAR PENGESAHAN RESMI                                       -->
<!-- ========================================================================= -->
<div class="container page-break">
    <div class="kop-surat">
        <table class="kop-table">
            <tr>
                <td style="width: 75px; text-align: center;">
                    <img src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo" style="width: 70px; height: 70px; object-fit: contain;">
                </td>
                <td style="text-align: center; padding: 0 10px;">
                    <div class="kop-yayasan"><?= htmlspecialchars($nama_yayasan) ?></div>
                    <div class="kop-madrasah"><?= htmlspecialchars($sekolah_nama) ?></div>
                    <div class="kop-status">NSM : 131218040003 &nbsp;|&nbsp; NPSN : 10816823 &nbsp;|&nbsp; STATUS : TERAKREDITASI</div>
                    <div class="kop-alamat">
                        <?= htmlspecialchars($alamatLengkap) ?><br>
                        Website: <?= htmlspecialchars($webUrl) ?> &nbsp;|&nbsp; Email: <?= htmlspecialchars($emailAddr) ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="doc-title">
        <h1>LEMBAR PENGESAHAN</h1>
        <div class="sub-title">DOKUMEN PEDOMAN & BLUEPRINT TIGA PROGRAM UNGGULAN<br><?= htmlspecialchars($sekolah_nama) ?><br>TAHUN PELAJARAN <?= htmlspecialchars($tahun_ajaran) ?></div>
    </div>

    <p style="text-indent: 30px;">
        Setelah melalui proses kajian, penyelarasan kurikulum, dan rapat pleno dewan guru bersama Tim Pengembang Kurikulum serta Pengurus Komite Madrasah, maka dokumen <strong>Buku Pedoman dan Blueprint Tiga Program Unggulan (Madrasah Berbasis Pesantren & ROHIS, Madrasah Berbasis Life Skill, dan Madrasah Berbasis Digital)</strong> ini secara resmi disahkan untuk dipergunakan sebagai panduan operasional KBM, pembinaan kesiswaan, alokasi sarana, dan evaluasi capaian mutu di <?= htmlspecialchars($sekolah_nama) ?> pada Tahun Pelajaran <?= htmlspecialchars($tahun_ajaran) ?>.
    </p>

    <div style="margin-top: 35px;">
        <table class="ttd-table">
            <tr>
                <td style="width: 50%;">
                    Disetujui oleh:<br>
                    <strong>Ketua Komite Madrasah</strong><br>
                    MAS Miftahul Huda<br><br><br><br><br>
                    <strong><?= htmlspecialchars($komite_nama) ?></strong><br>
                    Ketua Komite
                </td>
                <td style="width: 50%;">
                    Ditetapkan di : <?= htmlspecialchars($kecamatan) ?><br>
                    Pada tanggal : <?= htmlspecialchars($tgl_sk_formatted) ?><br>
                    <strong>Kepala Madrasah Aliyah Miftahul Huda</strong><br><br><br><br><br>
                    <strong><?= htmlspecialchars($kamad_nama) ?></strong><br>
                    NIP. <?= htmlspecialchars($kamad_nip) ?>
                </td>
            </tr>
            <tr>
                <td style="padding-top: 40px;">
                    Penyusun:<br>
                    <strong>Ketua Tim Pengembang Kurikulum</strong><br>
                    Waka Kurikulum MAS Miftahul Huda<br><br><br><br><br>
                    <strong><?= htmlspecialchars($waka_nama) ?></strong><br>
                    NIP. <?= htmlspecialchars($waka_nip) ?>
                </td>
                <td style="padding-top: 40px;">
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
<!-- HALAMAN 3: SURAT KEPUTUSAN (SK) PENETAPAN KEPALA MADRASAH                -->
<!-- ========================================================================= -->
<div class="container page-break">
    <div class="kop-surat">
        <table class="kop-table">
            <tr>
                <td style="width: 75px; text-align: center;">
                    <img src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo" style="width: 70px; height: 70px; object-fit: contain;">
                </td>
                <td style="text-align: center; padding: 0 10px;">
                    <div class="kop-yayasan"><?= htmlspecialchars($nama_yayasan) ?></div>
                    <div class="kop-madrasah"><?= htmlspecialchars($sekolah_nama) ?></div>
                    <div class="kop-status">NSM : 131218040003 &nbsp;|&nbsp; NPSN : 10816823 &nbsp;|&nbsp; STATUS : TERAKREDITASI</div>
                    <div class="kop-alamat">
                        <?= htmlspecialchars($alamatLengkap) ?><br>
                        Website: <?= htmlspecialchars($webUrl) ?> &nbsp;|&nbsp; Email: <?= htmlspecialchars($emailAddr) ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="doc-title">
        <h1>SURAT KEPUTUSAN KEPALA MADRASAH ALIYAH MIFTAHUL HUDA</h1>
        <div class="sk-no">Nomor: <?= htmlspecialchars($nomor_sk) ?></div>
        <div class="sub-title">TENTANG<br>PENETAPAN TIGA PROGRAM UNGGULAN MADRASAH<br>TAHUN PELAJARAN <?= htmlspecialchars($tahun_ajaran) ?></div>
    </div>

    <table style="width: 100%; border-collapse: collapse; font-size: 10pt; margin-bottom: 12px;">
        <tr>
            <td style="width: 15%; vertical-align: top; font-weight: bold;">Menimbang</td>
            <td style="width: 3%; vertical-align: top;">:</td>
            <td style="width: 82%; text-align: justify;">
                <ol style="margin: 0; padding-left: 16px;">
                    <li>Bahwa dalam rangka menjawab tantangan zaman dan membekali lulusan dengan imtaq kokoh, kemandirian ekonomi, dan literasi teknologi modern, diperlukan program terarah dan berkesinambungan;</li>
                    <li>Bahwa perpaduan antara karakter kepesantrenan, organisasi kerohanian Islam (ROHIS), keterampilan life skill, dan otomasi madrasah digital merupakan pilar keunggulan MAS Miftahul Huda;</li>
                    <li>Bahwa berdasarkan pertimbangan huruf a dan b, perlu menetapkan Surat Keputusan Kepala Madrasah tentang Tiga Program Unggulan TP <?= htmlspecialchars($tahun_ajaran) ?>.</li>
                </ol>
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: top; padding-top: 6px;">Mengingat</td>
            <td style="vertical-align: top; padding-top: 6px;">:</td>
            <td style="text-align: justify; padding-top: 6px;">
                <ol style="margin: 0; padding-left: 16px;">
                    <li>Undang-Undang RI Nomor 20 Tahun 2003 tentang Sistem Pendidikan Nasional;</li>
                    <li>Undang-Undang RI Nomor 18 Tahun 2019 tentang Pesantren;</li>
                    <li>KMA Nomor 347 Tahun 2022 tentang Pedoman Implementasi Kurikulum Merdeka pada Madrasah;</li>
                    <li>Rencana Kerja Tahunan dan Kurikulum Operasional Madrasah (KOM) MAS Miftahul Huda.</li>
                </ol>
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: top; padding-top: 6px;">Memutuskan</td>
            <td style="vertical-align: top; padding-top: 6px;">:</td>
            <td style="padding-top: 6px; font-weight: bold;">
                KEPUTUSAN KEPALA MADRASAH ALIYAH MIFTAHUL HUDA TENTANG PENETAPAN TIGA PROGRAM UNGGULAN TAHUN PELAJARAN <?= htmlspecialchars($tahun_ajaran) ?>.
            </td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse; font-size: 10pt;">
        <tr>
            <td style="width: 15%; vertical-align: top; font-weight: bold;">KESATU</td>
            <td style="width: 3%; vertical-align: top;">:</td>
            <td style="text-align: justify;">
                Menetapkan Tiga Program Unggulan Resmi MAS Miftahul Huda yaitu: <strong>(1) Madrasah Berbasis Pesantren & ROHIS</strong>, <strong>(2) Madrasah Berbasis Life Skill</strong>, dan <strong>(3) Madrasah Berbasis Digital</strong>.
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: top; padding-top: 4px;">KEDUA</td>
            <td style="vertical-align: top; padding-top: 4px;">:</td>
            <td style="text-align: justify; padding-top: 4px;">
                Menunjuk koordinator dan pembina pelaksanaan program: Koordinator Pesantren (<strong><?= htmlspecialchars($kord_pesantren_nama) ?></strong>), Pembina ROHIS (<strong><?= htmlspecialchars($pembina_rohis_nama) ?></strong>), Koordinator Life Skill (<strong><?= htmlspecialchars($kord_lifeskill_nama) ?></strong>), dan Koordinator Digital (<strong><?= htmlspecialchars($kord_digital_nama) ?></strong>).
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: top; padding-top: 4px;">KETIGA</td>
            <td style="vertical-align: top; padding-top: 4px;">:</td>
            <td style="text-align: justify; padding-top: 4px;">
                Keputusan ini berlaku sejak tanggal ditetapkan, dan apabila terdapat kekeliruan akan diperbaiki sebagaimana mestinya.
            </td>
        </tr>
    </table>

    <div style="margin-top: 25px; float: right; width: 45%; text-align: center; font-size: 10.5pt;">
        Ditetapkan di : <?= htmlspecialchars($kecamatan) ?><br>
        Pada tanggal : <?= htmlspecialchars($tgl_sk_formatted) ?><br>
        <strong>Kepala Madrasah Aliyah Miftahul Huda</strong><br><br><br><br><br>
        <strong><?= htmlspecialchars($kamad_nama) ?></strong><br>
        NIP. <?= htmlspecialchars($kamad_nip) ?>
    </div>
    <div style="clear: both;"></div>
</div>

<!-- ========================================================================= -->
<!-- HALAMAN 4: BLUEPRINT TIGA PROGRAM (RINGKAS & TEPAT SESUAI ARAHAN)        -->
<!-- ========================================================================= -->
<div class="container page-break">
    <h2 class="bab-heading">RANCANG BANGUN & DESAIN TIGA PROGRAM UNGGULAN</h2>

    <h3 class="sub-heading">1. PROGRAM MADRASAH BERBASIS PESANTREN & ROHIS</h3>
    <p>
        Program ini bertujuan memperkuat karakter religius, pembinaan akhlakul karimah, serta kepemimpinan dakwah santri melalui sinergi pesantren dan organisasi Kerohanian Islam (ROHIS):
    </p>
    <ul style="padding-left: 20px; font-size: 10pt;">
        <li>
            <strong>Tahfidz & Tahsin Al-Qur'an Terpadu:</strong>
            Target capaian minimal mutqin 3 Juz (Juz 30, Juz 1, dan Juz 2) bagi peserta didik reguler, serta kelas akselerasi 10–30 Juz bagi santri peminatan asrama melalui sistem talaqqi, muraja'ah terjadwal, dan munaqosyah resmi.
        </li>
        <li>
            <strong>Pembinaan & Aktivitas ROHIS (Kerohanian Islam):</strong>
            Mentoring adab dan keislaman mingguan, kajian keputrian, pembinaan dai muda santri, peringatan hari besar Islam (PHBI), tadarus bersama, serta kaderisasi dakwah santri teladan.
        </li>
        <li>
            <strong>Pembiasaan Ubudiyah & Amaliah Harian:</strong>
            Sholat fardhu berjamaah di masjid, Sholat Dhuha harian, Qiyamul Lail (Tahajjud), pembacaan Ratibul Haddad, dzikir pagi-petang, serta pendampingan adab santri kepada guru dan orang tua.
        </li>
    </ul>

    <h3 class="sub-heading">2. PROGRAM MADRASAH BERBASIS LIFE SKILL (VOKASI & MANDIRI)</h3>
    <p>
        Membekali setiap peserta didik dengan keahlian vokasional praktis bernilai ekonomi tinggi agar mandiri secara finansial dan siap berwirausaha:
    </p>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 28%;">Bidang Vokasi</th>
                <th style="width: 44%;">Fokus Keterampilan Terapan</th>
                <th style="width: 28%;">Target Luaran / Produk</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Desain Grafis & Digital Marketing</strong></td>
                <td>Desain branding logo, kemasan produk UMKM, poster promosi, fotografi produk gawai/kamera, copywriting, etalase marketplace.</td>
                <td>Portofolio karya komersil siswa & jasa desain madrasah.</td>
            </tr>
            <tr>
                <td><strong>Tata Boga & Olahan Pangan Halal</strong></td>
                <td>Produksi aneka kue/bakery madrasah, kuliner nusantara, pengemasan kedap udara higienis, serta edukasi sertifikasi halal.</td>
                <td>Kantin mandiri santri, penjualan kue halal, & sertifikat kecakapan.</td>
            </tr>
            <tr>
                <td><strong>Teknisi Komputer & Elektronika</strong></td>
                <td>Perakitan PC komputer, maintenance software/hardware ringan, instalasi jaringan Wi-Fi/LAN, dan instalasi kelistrikan dasar.</td>
                <td>Tim teknisi madrasah & jasa perbaikan laptop warga.</td>
            </tr>
            <tr>
                <td><strong>Tata Busana & Kriya Kreatif</strong></td>
                <td>Pembuatan pola, menjahit busana muslim santri, jilbab, gamis, dan bordir/sablon digital seragam madrasah.</td>
                <td>Seragam kegiatan santri & cinderamata madrasah mandiri.</td>
            </tr>
        </tbody>
    </table>

    <h3 class="sub-heading">3. PROGRAM MADRASAH BERBASIS DIGITAL (SMART SCHOOL 5.0)</h3>
    <p>
        Transformasi digital cerdas menyeluruh pada KBM dan tata kelola madrasah berbasis cloud, paperless, dan AI:
    </p>
    <ul style="padding-left: 20px; font-size: 10pt;">
        <li>
            <strong>MASMIFHDA SYSTEM Terpadu:</strong>
            Portal Guru, Portal Siswa, Presensi Scanner QR/Face, Jurnal Mengajar Real-time, E-Kinerja, dan E-Rapor Digital yang terintegrasi.
        </li>
        <li>
            <strong>Mata Pelajaran Koding & Literasi AI:</strong>
            Pembelajaran computational thinking, pemrograman web (HTML/CSS/JS), dasar Python, dan etika pemanfaatan Artificial Intelligence secara bijak.
        </li>
        <li>
            <strong>CBT & Asesmen Paperless 100%:</strong>
            Ujian STS, PAS, dan PAT digital tanpa kertas, acak soal otomatis, sistem anti-curang, dan rekapitulasi nilai otomatis.
        </li>
        <li>
            <strong>E-Library & Repositori Digital:</strong>
            Perpustakaan digital terintegrasi e-book resmi dan bahan ajar interaktif multi-perangkat.
        </li>
    </ul>
</div>

<!-- ========================================================================= -->
<!-- HALAMAN 5: STRUKTUR PENGELOLA & MATRIKS AKSI                             -->
<!-- ========================================================================= -->
<div class="container page-break">
    <h2 class="bab-heading">STRUKTUR TIM PENGELOLA & TARGET CAPAIAN (KPI)</h2>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 27%;">Jabatan Tim Program</th>
                <th style="width: 33%;">Nama Personel</th>
                <th style="width: 32%;">Tanggung Jawab Pokok</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center">1</td>
                <td><strong>Penanggung Jawab</strong></td>
                <td><strong><?= htmlspecialchars($kamad_nama) ?></strong><br>Kepala Madrasah</td>
                <td>Pengarah kebijakan makro, mutu madrasah, dan legalitas.</td>
            </tr>
            <tr>
                <td align="center">2</td>
                <td><strong>Ketua Pengembang</strong></td>
                <td><strong><?= htmlspecialchars($waka_nama) ?></strong><br>Waka Kurikulum</td>
                <td>Penyusunan kurikulum terpadu, monitoring KBM & evaluasi.</td>
            </tr>
            <tr>
                <td align="center">3</td>
                <td><strong>Kordinator Pesantren</strong></td>
                <td><strong><?= htmlspecialchars($kord_pesantren_nama) ?></strong><br>Waka Kesiswaan</td>
                <td>Pengawasan halaqah tahfidz & pembiasaan ubudiyah santri.</td>
            </tr>
            <tr>
                <td align="center">4</td>
                <td><strong>Pembina ROHIS</strong></td>
                <td><strong><?= htmlspecialchars($pembina_rohis_nama) ?></strong><br>Pembina Keagamaan</td>
                <td>Mentoring kajian keislaman, tadarus & kaderisasi dakwah santri.</td>
            </tr>
            <tr>
                <td align="center">5</td>
                <td><strong>Kordinator Life Skill</strong></td>
                <td><strong><?= htmlspecialchars($kord_lifeskill_nama) ?></strong><br>Bendahara Madrasah</td>
                <td>Jadwal praktikum vokasi, pengadaan sarana & expo karya siswa.</td>
            </tr>
            <tr>
                <td align="center">6</td>
                <td><strong>Kordinator Digital & IT</strong></td>
                <td><strong><?= htmlspecialchars($kord_digital_nama) ?></strong><br>Pembina Lab IT</td>
                <td>Infrastruktur MASMIFHDA SYSTEM, lab komputer & CBT paperless.</td>
            </tr>
        </tbody>
    </table>

    <h3 class="sub-heading">Key Performance Indicators (KPI) & Target Capaian:</h3>
    <ol style="padding-left: 20px; font-size: 10pt;">
        <li>100% peserta didik tuntas hafalan minimal Juz 30 dan aktif dalam kegiatan keislaman ROHIS.</li>
        <li>100% siswa kelas X, XI, XII menguasai minimal 1 keahlian life skill yang dibuktikan dengan portofolio produk nyata.</li>
        <li>100% penyelenggaraan ujian, presensi, jurnal mengajar, dan raport terdigitalisasi penuh tanpa kertas pada MASMIFHDA SYSTEM.</li>
        <li>Terselenggaranya <strong>Gelar Karya & Expo Entrepreneur Santri</strong> setiap akhir semester.</li>
    </ol>

    <div style="margin-top: 35px; float: right; width: 45%; text-align: center; font-size: 10.5pt;">
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