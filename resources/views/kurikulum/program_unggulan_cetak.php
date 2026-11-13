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

$tahun_ajaran = $tahun_ajaran ?? '2026/2027';
$semester = $semester ?? 'Ganjil';
$kamad_nama = $kamad['nama'] ?? 'Drs. Hamim Hasyim';
$kamad_nip = $kamad['nip'] ?? '-';
$waka_nama = $ketua_panitia['nama'] ?? 'M AZIZ SULAIMAN, S.Pd';
$waka_nip = $ketua_panitia['nip'] ?? '1804072704000002';
$pengawas_nama = $pengawas['nama'] ?? 'Drs. H. M. YUSUF, M.Pd.I';
$pengawas_nip = $pengawas['nip'] ?? '196805141994031002';

$sk_nomor = '421.3/088/MAS-MH/SK-PROG/VII/2026';
$tgl_penetapan = '15 Juli 2026';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Pedoman 3 Program Unggulan TP <?= htmlspecialchars($tahun_ajaran) ?> - <?= htmlspecialchars($sekolah_nama) ?></title>
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
            .container { box-shadow: none !important; margin: 0 !important; width: 100% !important; max-width: 100% !important; }
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            background: #e2e8f0;
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
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 24px;
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
            margin-bottom: 24px;
        }
        .doc-title h1 {
            font-size: 13pt;
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
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1.5px solid #000;
            padding-bottom: 4px;
            margin-top: 22px;
            margin-bottom: 12px;
        }
        h3.sub-heading {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 14px;
            margin-bottom: 6px;
        }
        p, li {
            text-align: justify;
            margin-top: 4px;
            margin-bottom: 8px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0 18px 0;
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
            margin-top: 30px;
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
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
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
        🖨️ Cetak / Simpan Dokumen (PDF)
    </button>
    <a href="/kurikulum/program-unggulan" class="btn-back">
        ⬅ Kembali ke Dashboard
    </a>
</div>

<!-- ========================================================================= -->
<!-- HALAMAN 1: KOP, SK PENETAPAN & BAB I                                     -->
<!-- ========================================================================= -->
<div class="container">
    
    <!-- KOP SURAT RESMI -->
    <div class="kop-surat">
        <table class="kop-table">
            <tr>
                <td style="width: 80px; text-align: center;">
                    <img src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo" style="width: 75px; height: 75px; object-fit: contain;">
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

    <!-- JUDUL DOKUMEN -->
    <div class="doc-title">
        <h1>SURAT KEPUTUSAN KEPALA MADRASAH ALIYAH MIFTAHUL HUDA</h1>
        <div class="sk-no">Nomor: <?= htmlspecialchars($sk_nomor) ?></div>
        <div class="sub-title">TENTANG<br>PENETAPAN BLUEPRINT DAN PEDOMAN IMPLEMENTASI TIGA PROGRAM UNGGULAN<br>(MADRASAH PESANTREN, MADRASAH LIFE SKILL, DAN MADRASAH DIGITAL)<br>TAHUN PELAJARAN <?= htmlspecialchars($tahun_ajaran) ?></div>
    </div>

    <!-- KONSIDERANS SK -->
    <table style="width: 100%; border-collapse: collapse; font-size: 10.5pt; margin-bottom: 14px;">
        <tr>
            <td style="width: 15%; vertical-align: top; font-weight: bold;">Menimbang</td>
            <td style="width: 3%; vertical-align: top;">:</td>
            <td style="width: 82%; text-align: justify;">
                <ol style="margin: 0; padding-left: 18px;">
                    <li>Bahwa dalam rangka merespons dinamika era globalisasi, revolusi industri 5.0, serta tuntutan kemandirian generasi muda Islam, diperlukan akselerasi mutu pendidikan madrasah yang adaptif dan komprehensif;</li>
                    <li>Bahwa integrasi keilmuan Imtaq, keterampilan kemandirian (life skill), dan teknologi digital merupakan pilar fundamental dalam melahirkan lulusan yang berkepribadian Qur'ani, mandiri, dan berdaya saing;</li>
                    <li>Bahwa berdasarkan pertimbangan huruf a dan b, perlu menetapkan Keputusan Kepala Madrasah tentang Tiga Program Unggulan MAS Miftahul Huda Tahun Pelajaran <?= htmlspecialchars($tahun_ajaran) ?>.</li>
                </ol>
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: top; padding-top: 6px;">Mengingat</td>
            <td style="vertical-align: top; padding-top: 6px;">:</td>
            <td style="text-align: justify; padding-top: 6px;">
                <ol style="margin: 0; padding-left: 18px;">
                    <li>Undang-Undang Republik Indonesia Nomor 20 Tahun 2003 tentang Sistem Pendidikan Nasional;</li>
                    <li>Undang-Undang Republik Indonesia Nomor 18 Tahun 2019 tentang Pesantren;</li>
                    <li>Peraturan Menteri Agama RI Nomor 90 Tahun 2013 tentang Penyelenggaraan Pendidikan Madrasah;</li>
                    <li>KMA Nomor 347 Tahun 2022 tentang Pedoman Implementasi Kurikulum Merdeka pada Madrasah;</li>
                    <li>Rencana Strategis (Renstra) dan Kurikulum Operasional Madrasah (KOM) MAS Miftahul Huda.</li>
                </ol>
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: top; padding-top: 6px;">Memutuskan</td>
            <td style="vertical-align: top; padding-top: 6px;">:</td>
            <td style="padding-top: 6px; font-weight: bold;">
                KEPUTUSAN KEPALA MAS MIFTAHUL HUDA TENTANG IMPLEMENTASI TIGA PROGRAM UNGGULAN MADRASAH TP <?= htmlspecialchars($tahun_ajaran) ?>.
            </td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse; font-size: 10.5pt;">
        <tr>
            <td style="width: 15%; vertical-align: top; font-weight: bold;">KESATU</td>
            <td style="width: 3%; vertical-align: top;">:</td>
            <td style="text-align: justify;">
                Menetapkan Tiga Program Unggulan Resmi pada <?= htmlspecialchars($sekolah_nama) ?> yang meliputi: <strong>(1) Madrasah Berbasis Pesantren</strong>, <strong>(2) Madrasah Berbasis Life Skill</strong>, dan <strong>(3) Madrasah Berbasis Digital</strong>.
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: top; padding-top: 4px;">KEDUA</td>
            <td style="vertical-align: top; padding-top: 4px;">:</td>
            <td style="text-align: justify; padding-top: 4px;">
                Tiga Program Unggulan sebagaimana Diktum KESATU menjadi acuan operasional KBM, ekstrakurikuler, pembiasaan karakter, alokasi sarana prasarana, serta sistem evaluasi kelulusan santri/siswa.
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: top; padding-top: 4px;">KETIGA</td>
            <td style="vertical-align: top; padding-top: 4px;">:</td>
            <td style="text-align: justify; padding-top: 4px;">
                Segala biaya yang timbul akibat pelaksanaan keputusan ini dibebankan pada Rencana Anggaran Pendapatan dan Belanja Madrasah (RAPBM) serta sumber dana lain yang sah dan halal.
            </td>
        </tr>
    </table>

</div>

<!-- ========================================================================= -->
<!-- HALAMAN 2: BAB I & BLUEPRINT PROGRAM 1 (PESANTREN)                        -->
<!-- ========================================================================= -->
<div class="container page-break">

    <h2 class="bab-heading">BAB I: PENDAHULUAN & VISI STRATEGIS</h2>
    <p>
        Pendidikan Islam di tingkat Madrasah Aliyah dituntut tidak hanya mentransfer pengetahuan kognitif semata, melainkan juga harus mampu membangun integritas moral-spiritual, memberikan bekal kecakapan hidup (life skill) yang mandiri, serta membekali peserta didik dengan literasi digital yang mumpuni. MAS Miftahul Huda, yang tumbuh dan mengakar di lingkungan Pondok Pesantren Mathla'ul Anwar, memiliki keunggulan komparatif dan historis untuk menyatukan tiga pilar peradaban modern: <em>Kekokohan Aqidah-Syariah</em>, <em>Kreativitas Kemandirian Ekonomi</em>, dan <em>Kecanggihan Teknologi Informasi</em>.
    </p>

    <h3 class="sub-heading">Visi Program Unggulan</h3>
    <p>
        <em>"Terwujudnya Generasi Santri-Pelajar yang Berakhlak Mulia, Mutafaqqih Fiddin, Mandiri Berdaya Saing melalui Keterampilan Vokasi, dan Unggul dalam Ekosistem Teknologi Digital 5.0."</em>
    </p>

    <h2 class="bab-heading">BAB II: RANCANG BANGUN TIGA PROGRAM UNGGULAN</h2>

    <h3 class="sub-heading">PROGRAM 1: MADRASAH BERBASIS PESANTREN (ISLAMIC BOARDING EXCELLENCE)</h3>
    <p>
        Program Madrasah Berbasis Pesantren dirancang untuk mempertahankan dan mengembangkan tradisi keilmuan Islam <em>Ahlussunnah wal Jama'ah</em> yang bersanad. Program ini mengintegrasikan kurikulum Kementerian Agama dengan kurikulum salafiyah pesantren melalui empat pilar pokok:
    </p>

    <ol style="padding-left: 20px;">
        <li>
            <strong>Tahfidz & Tahsin Al-Qur'an Standar Mutqin:</strong>
            <p>
                Setiap peserta didik ditargetkan menyelesaikan hafalan minimal 3 Juz (Juz 30, Juz 1, dan Juz 2) dengan kaidah tajwid dan fashahah makharijul huruf yang tepat. Bagi santri kelas peminatan asrama, dibuka kelas akselerasi tahfidz 10 hingga 30 Juz. Evaluasi hafalan dilaksanakan melalui sima'an berkala, muraja'ah yaumiyyah, dan Munaqosyah Uji Publik resmi.
            </p>
        </li>
        <li>
            <strong>Penguasaan Turats & Kitab Kuning Klasik:</strong>
            <p>
                Membekali santri kemampuan membaca kitab gundul (fathul kutub) dengan tata bahasa Arab mendalam. Silabus kurikulum kitab kuning disusun secara berjenjang:
            </p>
            <ul style="padding-left: 20px; font-size: 10pt;">
                <li><em>Bidang Nahwu & Shorof:</em> Matan Al-Ajurumiyyah, Nadhom Al-Imrithi, dan Al-Amtsilah At-Tashrifiyyah.</li>
                <li><em>Bidang Fiqih & Ibadah:</em> Safinatun Naja, Sullamut Taufiq, dan Fathul Qorib Al-Mujib.</li>
                <li><em>Bidang Akhlak & Tasawuf:</em> Taisirul Khalaq, Akhlaq Lil Banin/Banat, dan Ta'limul Muta'allim.</li>
                <li><em>Bidang Hadits & Tauhid:</em> Al-Arba'in An-Nawawiyyah, Riyadhus Shalihin, dan Aqidatul Awwam.</li>
            </ul>
        </li>
        <li>
            <strong>Muhadatsah 3 Bahasa & Retorika Dakwah (Khitobah):</strong>
            <p>
                Penerapan <em>Bi'ah Lughawiyyah</em> (Lingkungan Berbahasa) aktif dengan pembiasaan Bahasa Arab dan Bahasa Inggris. Dilengkapi latihan khutbah Jumat, pidato tiga bahasa, dan pelatihan jurnalistik dakwah.
            </p>
        </li>
        <li>
            <strong>Pembiasaan Ubudiyah & Akhlakul Karimah:</strong>
            <p>
                Pembudayaan sholat fardhu berjamaah tepat waktu, Qiyamul Lail harian, Sholat Dhuha bersama, pembacaan Ratibul Haddad, Maulid Simtudduror, serta pendampingan adab santri kepada orang tua, guru, dan sesama.
            </p>
        </li>
    </ol>

</div>

<!-- ========================================================================= -->
<!-- HALAMAN 3: BLUEPRINT PROGRAM 2 (LIFE SKILL) & PROGRAM 3 (DIGITAL)         -->
<!-- ========================================================================= -->
<div class="container page-break">

    <h3 class="sub-heading">PROGRAM 2: MADRASAH BERBASIS LIFE SKILL (VOKASI & KEMANDIRIAN SANTRI)</h3>
    <p>
        Program Life Skill dihadirkan untuk menjawab tantangan bonus demografi dan tingginya angka pengangguran usia muda. MAS Miftahul Huda membekali seluruh santri dengan keterampilan vokasional konkret yang bernilai ekonomis tinggi. Setiap siswa wajib memilih minimal 1 dari 5 bidang kejuruan terapan:
    </p>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25%;">Divisi Kejuruan</th>
                <th style="width: 45%;">Materi & Keterampilan Praktik</th>
                <th style="width: 30%;">Target Output / Produk Nyata</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>1. Agribisnis Terpadu & Greenhouse</strong></td>
                <td>Teknik hidroponik NFT/DFT, nutrisi AB Mix, budidaya ikan air tawar kolam bioflok (nila & lele), pembuatan pupuk kompos/organik.</td>
                <td>Panen sayuran segar mingguan, bibit ikan unggul, serta pasokan pangan asrama pesantren mandiri.</td>
            </tr>
            <tr>
                <td><strong>2. Desain Grafis & Digital Marketing</strong></td>
                <td>Desain branding logo, spanduk, kemasan produk, teknik fotografi produk HP/kamera, copywriting iklan, etalase Shopee/TikTok Shop.</td>
                <td>Portofolio portofolio komersil siswa, jasa desain madrasah, dan pengelolaan akun UMKM mandiri.</td>
            </tr>
            <tr>
                <td><strong>3. Tata Boga & Pangan Halal Santri</strong></td>
                <td>Pembuatan aneka roti/bakery, olahan keripik khas Lampung, pengemasan vakum higienis, pengenalan sertifikasi halal LPPOM MUI & P-IRT.</td>
                <td>Unit usaha kantin mandiri santri, penjualan kue/roti halal, serta sertifikat kecakapan boga.</td>
            </tr>
            <tr>
                <td><strong>4. Teknisi Hardware & Elektronika</strong></td>
                <td>Perakitan PC komputer, instalasi OS Windows/Linux, penggantian pasta processor, troubleshooting LAN/Wi-Fi, perbaikan instalasi listrik.</td>
                <td>Tim teknisi madrasah mandiri, jasa servis laptop warga sekitar, dan sertifikasi teknisi pemula.</td>
            </tr>
            <tr>
                <td><strong>5. Tata Busana & Kriya Sablon</strong></td>
                <td>Pengukuran badan, pembuatan pola, pengoperasian mesin jahit, pembuatan jilbab, gamis santri, dan sablon digital/manual kaos.</td>
                <td>Seragam kegiatan santri mandiri, tote bag, merchandise kaligrafi, dan busana muslim siap pakai.</td>
            </tr>
        </tbody>
    </table>

    <h3 class="sub-heading">PROGRAM 3: MADRASAH BERBASIS DIGITAL (SMART DIGITAL MADRASAH 5.0)</h3>
    <p>
        Program Madrasah Digital mentransformasi seluruh ekosistem pembelajaran dan tata kelola madrasah menjadi modern, terintegrasi, efisien, dan <em>paperless</em> berbasis cloud dan kecerdasan buatan (AI):
    </p>

    <ol style="padding-left: 20px;">
        <li>
            <strong>Platform Terpadu MASMIFHDA SYSTEM:</strong>
            Pusat otomasi administrasi digital mencakup: Portal Guru, Portal Siswa, Presensi Elektronik QR/Face Scan, Jurnal Mengajar Harian Real-time, E-Kinerja PTK, serta Raport Digital. Seluruh rekaman tersimpan aman dan terintegrasi antar departemen.
        </li>
        <li>
            <strong>Mata Pelajaran Koding & Literasi Kecerdasan Buatan (AI):</strong>
            Penerapan kurikulum koding wajib bagi siswa (Computational Thinking, HTML5, CSS3, logika pemrograman dasar, dan pengantar Python). Pelatihan etika pemanfaatan AI (Prompt Engineering) untuk riset edukatif dan penyelesaian masalah.
        </li>
        <li>
            <strong>Asesmen Berbasis Komputer (CBT) & Paperless Exam:</strong>
            Ujian STS, PAS, dan PAT diselenggarakan 100% digital tanpa menggunakan kertas, meminimalkan anggaran cetak naskah, sistem acak butir soal anti-curang, dan analisis butir soal seketika.
        </li>
        <li>
            <strong>Perpustakaan Digital (E-Library) & Smart Classroom:</strong>
            Akses ratusan referensi buku mata pelajaran, ensiklopedia sains, serta ribuan manuskrip kitab kuning digital melalui gawai pintar dan komputer lab madrasah.
        </li>
    </ol>

</div>

<!-- ========================================================================= -->
<!-- HALAMAN 4: STRUKTUR PENGELOLA, KPI, ANGGARAN & LEMBAR PENGESAHAN           -->
<!-- ========================================================================= -->
<div class="container page-break">

    <h2 class="bab-heading">BAB III: STRUKTUR TIM PENGEMBANG & TANGGUNG JAWAB</h2>
    
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 25%;">Posisi Kepanitiaan</th>
                <th style="width: 32%;">Nama Personel & NIP</th>
                <th style="width: 35%;">Tugas Pokok & Wewenang</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center">1</td>
                <td><strong>Penanggung Jawab Utama</strong></td>
                <td><strong><?= htmlspecialchars($kamad_nama) ?></strong><br>NIP: <?= htmlspecialchars($kamad_nip) ?></td>
                <td>Pengarah kebijakan makro, penjaminan mutu madrasah, dan legalitas kerjasama eksternal.</td>
            </tr>
            <tr>
                <td align="center">2</td>
                <td><strong>Ketua Pelaksana Kurikulum</strong></td>
                <td><strong><?= htmlspecialchars($waka_nama) ?></strong><br>NIP: <?= htmlspecialchars($waka_nip) ?></td>
                <td>Penyusunan silabus terintegrasi, jadwal KBM peminatan, monitoring dan evaluasi pembelajaran.</td>
            </tr>
            <tr>
                <td align="center">3</td>
                <td><strong>Kordinator Madrasah Pesantren</strong></td>
                <td><strong>SAIPUDIN AZIZ, S.Pd</strong><br>Waka Kesiswaan</td>
                <td>Pengawasan halaqah tahfidz, kurikulum kitab turats, jadwal muhadatsah dan pembiasaan ibadah.</td>
            </tr>
            <tr>
                <td align="center">4</td>
                <td><strong>Kordinator Madrasah Life Skill</strong></td>
                <td><strong>INDAH YUNITASARI, S.Pd</strong><br>Bendahara Madrasah</td>
                <td>Manajemen jadwal praktikum vokasi, pengadaan sarana bengkel kerja, dan pelaksanaan expo santri.</td>
            </tr>
            <tr>
                <td align="center">5</td>
                <td><strong>Kordinator Madrasah Digital & IT</strong></td>
                <td><strong>SYAIFUL BAHRI, S.Pd</strong><br>Pembina Laboratorium IT</td>
                <td>Pemeliharaan server MASMIFHDA SYSTEM, lab komputer, infrastruktur Wi-Fi, dan pelatihan koding.</td>
            </tr>
        </tbody>
    </table>

    <h2 class="bab-heading">BAB IV: INDIKATOR KEBERHASILAN (KPI) & PENUTUP</h2>
    <p>
        Keberhasilan implementasi program ini dievaluasi secara berkala pada setiap akhir semester berdasarkan Key Performance Indicators (KPI):
    </p>
    <ul style="padding-left: 20px; font-size: 10pt;">
        <li>100% lulusan MAS Miftahul Huda hafal Al-Qur'an minimal 3 Juz dan lulus ujian munaqosyah kitab kuning dasar.</li>
        <li>100% peserta didik memiliki minimal 1 keahlian life skill terapan yang dibuktikan dengan portofolio dan sertifikat kompetensi.</li>
        <li>100% tata kelola administrasi penilaian, presensi, jurnal mengajar, dan ujian berbasis paperless pada MASMIFHDA SYSTEM.</li>
        <li>Terselenggaranya <strong>Gelar Karya & Expo Entrepreneur Santri</strong> minimal 1 kali setiap tahun ajaran.</li>
    </ul>

    <!-- LEMBAR PENGESAHAN DOKUMEN -->
    <div style="margin-top: 30px;">
        <table class="ttd-table">
            <tr>
                <td style="width: 50%;">
                    Menyetujui,<br>
                    <strong>Ketua Komite Madrasah</strong><br>
                    MAS Miftahul Huda<br><br><br><br><br>
                    <strong>H. AHMAD ROFIQ, S.Ag</strong><br>
                    Ketua Komite
                </td>
                <td style="width: 50%;">
                    Ditetapkan di : <?= htmlspecialchars($kecamatan) ?><br>
                    Pada tanggal : <?= htmlspecialchars($tgl_penetapan) ?><br>
                    <strong>Kepala Madrasah Aliyah Miftahul Huda</strong><br><br><br><br><br>
                    <strong><?= htmlspecialchars($kamad_nama) ?></strong><br>
                    NIP. <?= htmlspecialchars($kamad_nip) ?>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top: 35px;">
                    Mengetahui / Mengesahkan:<br>
                    <strong>Pengawas Pembina Madrasah Ahli Madya</strong><br>
                    Kantor Kementerian Agama Kabupaten Lampung Barat<br><br><br><br><br>
                    <strong><?= htmlspecialchars($pengawas_nama) ?></strong><br>
                    NIP. <?= htmlspecialchars($pengawas_nip) ?>
                </td>
            </tr>
        </table>
    </div>

</div>

</body>
</html>
