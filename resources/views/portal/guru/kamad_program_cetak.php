<?php
$sekolah_nama = $inst['nama'] ?? 'MI MIFTAHUL HUDA MATHLA\'UL ANWAR';
$npsn = $inst['npsn'] ?? '60705617';
$nsm = $inst['nsm'] ?? '111218040009';
$alamat = $inst['alamat'] ?? 'Jl. Tirtomoyo, No. 083, Margorahayu';
$kecamatan = $inst['kecamatan'] ?? 'Way Tenong';
$kabupaten = $inst['kota'] ?? 'Lampung Barat';
$provinsi = $inst['provinsi'] ?? 'Lampung';
$kodepos = $inst['kodepos'] ?? '34884';
$email = $inst['email'] ?? 'mimiftahulhuda083@gmail.com';
$website = $inst['website'] ?? 'mismifhda.sch.id';

$kepala_nama = $kamad_profile['nama'] ?? 'SAMSUL ARIFIN, M.Pd';
$kepala_nip = $kamad_profile['nip'] ?? '-';
$tahun_ajaran = $active_year_name ?? '2026/2027';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Kerja Kepala Madrasah - <?php echo htmlspecialchars($sekolah_nama); ?></title>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <style>
        /* CSS Print A4 Setup with Academic Margins */
        @page { 
            size: A4; 
            margin: 3cm 3cm 3.5cm 4cm; /* Top: 3cm, Right: 3cm, Bottom: 3.5cm, Left: 4cm */
        }
        body {
            background: #cbd5e1;
            margin: 0;
            padding: 0;
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
        }
        
        .no-print-bar {
            background: #0369a1;
            color: white;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            font-family: system-ui, -apple-system, sans-serif;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .btn-print {
            background: white;
            color: #0369a1;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            transition: 0.2s;
        }
        
        .btn-print:hover {
            background: #f0fdf4;
            transform: scale(1.02);
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 3cm 3cm 3.5cm 4cm; /* Standard margins: Left: 4cm, Top: 3cm, Right: 3cm, Bottom: 3.5cm */
            margin: 20px auto;
            background: white;
            box-sizing: border-box;
            box-shadow: 0 0 15px rgba(0,0,0,0.15);
            position: relative;
        }
        .page-break {
            page-break-after: always;
            break-after: always;
        }
        
        @media print {
            body { 
                background: white; 
            }
            .no-print-bar { 
                display: none; 
            }
            .page {
                margin: 0;
                padding: 0;
                box-shadow: none;
                width: 100%;
                min-height: 0;
                background: transparent;
                page-break-after: always;
                break-after: always;
            }
        }

        /* Typography & Structure Styles */
        h1, h2, h3, h4 {
            font-family: "Times New Roman", Times, serif;
            color: #000;
            font-weight: bold;
            text-align: center;
            margin: 0 0 15px 0;
            line-height: 1.3;
            page-break-after: avoid;
            break-after: avoid;
        }
        h1 { font-size: 16pt; text-transform: uppercase; }
        h2 { font-size: 14pt; text-transform: uppercase; margin-bottom: 25px; }
        h3 { font-size: 12pt; text-align: left; text-transform: uppercase; margin-top: 20px; }
        h4 { font-size: 12pt; text-align: left; margin-left: 20px; margin-top: 10px; font-weight: bold; }
        
        p {
            text-align: justify;
            text-indent: 1.25cm;
            margin: 0 0 12px 0;
            font-size: 12pt;
        }
        
        .no-indent {
            text-indent: 0;
        }
        
        ol, ul {
            text-align: justify;
            margin: 0 0 15px 0;
            padding-left: 1.25cm;
        }
        ol li::marker {
            font-weight: bold;
        }
        
        li {
            margin-bottom: 5px;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        /* Cover Specifics */
        .cover-container {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            text-align: center;
        }
        .cover-logo {
            width: 150px;
            height: 150px;
            object-fit: contain;
            margin: 40px auto;
            display: block;
        }
        .cover-title {
            font-family: 'Times New Roman', serif;
            font-size: 18pt;
            font-weight: bold;
            line-height: 1.4;
            color: #000;
            text-transform: uppercase;
        }
        .cover-subtitle {
            font-size: 14pt;
            font-weight: bold;
            color: #000;
            margin-top: 10px;
        }

        /* Signature Blocks */
        .sign-table {
            width: 100%;
            margin-top: 50px;
            border-collapse: collapse;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .sign-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
            border: none !important;
            font-size: 12pt;
        }

        /* Standard Data Tables */
        table.waka-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
            font-size: 10.5pt;
            page-break-inside: auto;
        }
        table.waka-table tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }
        thead {
            display: table-header-group;
        }
        .swot-table {
            page-break-inside: avoid;
            break-inside: avoid;
        }
        table.waka-table th, table.waka-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: middle;
        }
        table.waka-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        /* Table of Contents dot leaders */
        .toc-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .toc-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .toc-subitem {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 8px;
            padding-left: 20px;
            font-weight: normal;
        }
        .toc-name {
            position: relative;
            overflow: hidden;
            flex-grow: 1;
        }
        .toc-name::after {
            content: " ......................................................................................................................................................................................................................................................";
            position: absolute;
            white-space: nowrap;
        }
        .toc-page {
            padding-left: 10px;
            flex-shrink: 0;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- Bar Kontrol Atas -->
    <div class="no-print-bar">
        <div>
            <strong style="font-size: 1.1rem;">Buku Program Kerja Kepala Madrasah - Cetak Kontinu</strong>
            <span style="font-size: 0.8rem; opacity: 0.8; margin-left: 15px;">Tip: Halaman mengalir secara alami untuk menghemat kertas dan menjaga kerapian cetak.</span>
        </div>
        <button class="btn-print" onclick="window.print()"><i data-lucide="printer" style="width:16px;height:16px;"></i> Cetak / Simpan PDF</button>
    </div>

    <!-- Cover -->
    <div class="page page-break">
        <div class="cover-container" style="padding: 20px 0; height: 100%; display: flex; flex-direction: column; justify-content: space-between; min-height: 220mm;">
            <div>
                <span style="border: 2px solid #000; padding: 6px 20px; font-size: 10pt; font-weight: bold; letter-spacing: 2px; color: #000; border-radius: 4px;">DOKUMEN PENGELOLAAN MADRASAH</span>
                <div class="cover-title" style="margin-top: 50px;">
                    PROGRAM KERJA TAHUNAN<br>KEPALA MADRASAH
                </div>
                <div class="cover-subtitle">
                    TAHUN PELAJARAN <?php echo htmlspecialchars($tahun_ajaran); ?>
                </div>
            </div>
            
            <div>
                <img src="<?php echo \App\Core\Branding::getInstitusiLogo(); ?>" class="cover-logo" alt="Logo Madrasah">
            </div>

            <div style="font-family: 'Times New Roman', serif;">
                <p class="no-indent" style="text-align: center; font-weight: bold; font-size: 14pt; margin: 0; color: #000;">MI MIFTAHUL HUDA MATHLA'UL ANWAR</p>
                <p class="no-indent" style="text-align: center; font-size: 10pt; color: #000; margin-top: 5px; line-height: 1.4;">
                    NPSN: <?php echo htmlspecialchars($npsn); ?> | NSM: <?php echo htmlspecialchars($nsm); ?><br>
                    <?php echo htmlspecialchars($alamat); ?>, Kec. <?php echo htmlspecialchars($kecamatan); ?>, <?php echo htmlspecialchars($kabupaten); ?> - <?php echo htmlspecialchars($provinsi); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Lembar Pengesahan -->
    <div class="page page-break">
        <h1>LEMBAR PENGESAHAN</h1>
        <br><br>
        <p class="no-indent" style="text-align: center;">Program Kerja Kepala Madrasah MI Miftahul Huda Mathla'ul Anwar Tahun Pelajaran <?php echo htmlspecialchars($tahun_ajaran); ?> ini telah diperiksa, dievaluasi, dan disetujui bersama oleh unsur Komite Madrasah dan Pengawas Kemenag untuk diimplementasikan secara terarah di madrasah.</p>
        
        <br><br>
        <table style="width: 100%; margin-top: 40px;">
            <tr>
                <td style="width: 15%; font-weight: bold;">Disahkan di</td>
                <td style="width: 85%;">: Margorahayu</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Pada Tanggal</td>
                <td>: <?php echo date('d F Y'); ?></td>
            </tr>
        </table>
        
        <br><br>
        <table class="sign-table" style="margin-top: 50px;">
            <tr>
                <td>
                    Menyetujui,<br>
                    Ketua Komite Madrasah<br><br><br><br><br>
                    <strong>HASAN BASRI</strong><br>
                    NIP. -
                </td>
                <td>
                    Mengesahkan,<br>
                    Kepala Madrasah<br><br><br><br><br>
                    <strong><?php echo htmlspecialchars($kepala_nama); ?></strong><br>
                    NIP. <?php echo htmlspecialchars($kepala_nip ?: '-'); ?>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center; padding-top: 40px;">
                    Mengetahui,<br>
                    Pengawas Madrasah Kantor Kemenag<br><br><br><br><br>
                    <strong>H. AHMAD SYARIF, M.Pd.I</strong><br>
                    NIP. 197405122002121004
                </td>
            </tr>
        </table>
    </div>

    <!-- Kata Pengantar -->
    <div class="page page-break">
        <h1>KATA PENGANTAR</h1>
        <br>
        <p>Bismillahirrohmanirrohim. Puji syukur senantiasa kami panjatkan kehadirat Allah SWT, yang telah melimpahkan rahmat, taufik, serta hidayah-Nya, sehingga program kerja Kepala Madrasah MI Miftahul Huda Mathla'ul Anwar untuk Tahun Pelajaran <?php echo htmlspecialchars($tahun_ajaran); ?> ini dapat diselesaikan dengan baik.</p>
        <p>Program kerja ini disusun sebagai acuan operasional dan arah kebijakan akademis dalam mengelola proses pembelajaran di madrasah selama satu tahun pelajaran. Fokus utama dari program kerja ini adalah peningkatan mutu proses dan hasil belajar peserta didik, pengembangan kompetensi profesional guru, serta penyempurnaan manajemen kurikulum madrasah secara terintegrasi.</p>
        <p>Penulis menyadari bahwa keberhasilan pelaksanaan program kerja ini sangat bergantung pada komitmen, kerja sama, dan dedikasi seluruh elemen di madrasah, mulai dari Kepala Madrasah, rekan-rekan pendidik, tenaga kependidikan, komite madrasah, hingga peran aktif orang tua siswa. Saran dan kritik yang membangun senantiasa diharapkan demi penyempurnaan kurikulum madrasah di masa mendatang.</p>
        <p>Semoga program kerja ini bermanfaat dan diridhoi oleh Allah SWT sebagai amal ibadah kita bersama dalam upaya mencerdaskan generasi bangsa yang berakhlak mulia.</p>
        
        <table class="sign-table">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%;">
                    Way Tenong, <?php echo date('d F Y'); ?><br>
                    Kepala Madrasah<br><br><br><br>
                    <strong><?php echo htmlspecialchars($kepala_nama); ?></strong>
                </td>
            </tr>
        </table>
    </div>

    <!-- Daftar Isi -->
    <div class="page page-break">
        <h1>DAFTAR ISI</h1>
        <br><br>
        <ul class="toc-list">
            <li class="toc-item"><span class="toc-name">COVER / HALAMAN AWAL</span><span class="toc-page">i</span></li>
            <li class="toc-item"><span class="toc-name">LEMBAR PENGESAHAN</span><span class="toc-page">ii</span></li>
            <li class="toc-item"><span class="toc-name">KATA PENGANTAR</span><span class="toc-page">iii</span></li>
            <li class="toc-item"><span class="toc-name">DAFTAR ISI</span><span class="toc-page">iv</span></li>
            <li class="toc-item"><span class="toc-name">BAB I. PENDAHULUAN</span><span class="toc-page">1</span></li>
            <li class="toc-subitem"><span class="toc-name">A. Latar Belakang</span><span class="toc-page">1</span></li>
            <li class="toc-subitem"><span class="toc-name">B. Dasar Hukum</span><span class="toc-page">2</span></li>
            <li class="toc-subitem"><span class="toc-name">C. Maksud & Tujuan Program</span><span class="toc-page">2</span></li>
            <li class="toc-subitem"><span class="toc-name">E. Sasaran & F. Ruang Lingkup Program</span><span class="toc-page">3</span></li>
            
            <li class="toc-item"><span class="toc-name">BAB II. PROFIL MADRASAH</span><span class="toc-page">4</span></li>
            <li class="toc-subitem"><span class="toc-name">A. Identitas & B. Sejarah Singkat</span><span class="toc-page">4</span></li>
            <li class="toc-subitem"><span class="toc-name">C. Visi, Misi, dan Tujuan Madrasah</span><span class="toc-page">5</span></li>
            <li class="toc-subitem"><span class="toc-name">F. Struktur Organisasi</span><span class="toc-page">5</span></li>
            <li class="toc-subitem"><span class="toc-name">G. Data Pendidik, Peserta Didik, & Sarpras</span><span class="toc-page">6</span></li>
            
            <li class="toc-item"><span class="toc-name">BAB III. ANALISIS KONDISI MADRASAH</span><span class="toc-page">9</span></li>
            <li class="toc-subitem"><span class="toc-name">A. Analisis 8 Standar Nasional Pendidikan (SNP)</span><span class="toc-page">9</span></li>
            <li class="toc-subitem"><span class="toc-name">H. Analisis SWOT Madrasah</span><span class="toc-page">11</span></li>
            
            <li class="toc-item"><span class="toc-name">BAB IV. PROGRAM KERJA KEPALA MADRASAH</span><span class="toc-page">13</span></li>
            <li class="toc-subitem"><span class="toc-name">A. Program Bidang Kurikulum</span><span class="toc-page">13</span></li>
            <li class="toc-subitem"><span class="toc-name">B. Program Bidang Kesiswaan</span><span class="toc-page">15</span></li>
            <li class="toc-subitem"><span class="toc-name">C. Program Bidang Sarana dan Prasarana</span><span class="toc-page">17</span></li>
            <li class="toc-subitem"><span class="toc-name">D. Program Bidang Humas</span><span class="toc-page">18</span></li>
            <li class="toc-subitem"><span class="toc-name">E. Program Bidang Keuangan</span><span class="toc-page">19</span></li>
            <li class="toc-subitem"><span class="toc-name">F. Program Bidang Ketenagaan</span><span class="toc-page">20</span></li>
            
            <li class="toc-item"><span class="toc-name">BAB V. PROGRAM SUPERVISI</span><span class="toc-page">22</span></li>
            <li class="toc-subitem"><span class="toc-name">A. Supervisi Akademik & Administrasi Guru</span><span class="toc-page">22</span></li>
            <li class="toc-subitem"><span class="toc-name">C. Supervisi Manajerial & Tindak Lanjut</span><span class="toc-page">23</span></li>
            
            <li class="toc-item"><span class="toc-name">BAB VI. PROGRAM PENGEMBANGAN MADRASAH</span><span class="toc-page">25</span></li>
            <li class="toc-item"><span class="toc-name">BAB VII. MONITORING DAN EVALUASI</span><span class="toc-page">28</span></li>
            <li class="toc-item"><span class="toc-name">BAB VIII. PENUTUP</span><span class="toc-page">30</span></li>
            <li class="toc-item"><span class="toc-name">LAMPIRAN-LAMPIRAN (Format Lampiran 1 - 15)</span><span class="toc-page">31</span></li>
        </ul>
    </div>

    <!-- BAB I PENDAHULUAN -->
    <div class="page page-break">
        <h1>BAB I<br>PENDAHULUAN</h1>
        <br>
        <h3>A. Latar Belakang</h3>
        <p>Pendidikan merupakan pilar utama pembangunan sumber daya manusia yang berkarakter, cerdas, mandiri, dan berakhlak mulia. Madrasah Ibtidaiyah (MI) Miftahul Huda Mathla'ul Anwar sebagai salah satu satuan pendidikan formal berciri khas Islam mengemban tugas penting melahirkan generasi Rabbani yang cakap IPTEK sekaligus tangguh dalam IMTAK.</p>
        <p>Sebagai kepala lembaga pendidikan, Kepala Madrasah memegang peranan krusial dalam memimpin, mengelola, menyupervisi, serta mengembangkan mutu madrasah. Oleh karena itu, program kerja tahunan disusun agar seluruh target peningkatan mutu pendidikan dapat direalisasikan dengan matang, transparan, dan akuntabel di hadapan pemangku kepentingan.</p>
        <p>Tuntutan akreditasi dan audit internal kementerian menghendaki tata pamong madrasah terdokumentasi dengan baik. Kehadiran program kerja ini berfungsi sebagai instrumen monitoring sekaligus pedoman bertindak operasional sepanjang tahun pelajaran bergulir.</p>

        <h3>B. Dasar Hukum</h3>
        <p class="no-indent">Penyusunan program kerja tahunan ini mengacu pada regulasi formal berikut:</p>
        <ol>
            <li>Undang-Undang RI Nomor 20 Tahun 2003 tentang Sistem Pendidikan Nasional.</li>
            <li>Peraturan Pemerintah Nomor 57 Tahun 2021 tentang Standar Nasional Pendidikan sebagaimana telah diubah dengan PP Nomor 4 Tahun 2022.</li>
            <li>Keputusan Menteri Agama (KMA) Nomor 347 Tahun 2022 tentang Pedoman Implementasi Kurikulum Merdeka pada Madrasah.</li>
            <li>Peraturan Menteri Agama Nomor 58 Tahun 2017 tentang Kepala Madrasah.</li>
            <li>Program Kerja Madrasah dan Rencana Kegiatan dan Anggaran Madrasah (RKAM) MI Miftahul Huda Mathla'ul Anwar.</li>
        </ol>

        <h3>C. Maksud Program</h3>
        <p>Program kerja ini dimaksudkan untuk memberikan panduan tata pamong operasional bagi Kepala Madrasah dan jajaran wakil kepala madrasah (Waka) dalam melaksanakan tugas pokok harian. Hal ini ditujukan untuk membangun iklim kerja yang profesional, harmonis, serta terintegrasi antar bidang.</p>

        <h3>D. Tujuan Program</h3>
        <p class="no-indent">Tujuan penyusunan program kerja ini meliputi:</p>
        <ol>
            <li>Mengoptimalkan implementasi kurikulum merdeka dan kurikulum 2013 di madrasah secara terstruktur.</li>
            <li>Menjamin kelancaran, ketertiban, dan kedisiplinan proses belajar mengajar (KBM).</li>
            <li>Meningkatkan rata-rata nilai kompetensi rapor siswa serta prestasi dalam ajang sains KSM.</li>
            <li>Mempertahankan akuntabilitas administrasi keuangan BOS dan komite madrasah.</li>
        </ol>

        <h3>E. Sasaran Program</h3>
        <p>Sasaran program kerja Kepala Madrasah adalah seluruh guru kelas/mata pelajaran, wali kelas, tenaga kependidikan (tata usaha), peserta didik kelas I - VI, pengurus komite madrasah, dan orang tua/wali murid.</p>

        <h3>F. Ruang Lingkup Program</h3>
        <p>Ruang lingkup program ini mencakup pemenuhan 8 Standar Nasional Pendidikan (SNP), supervisi akademis dan manajerial, pembinaan ekstrakurikuler kesiswaan, program peningkatan kesejahteraan pendidik, digitalisasi sistem pembelajaran, hubungan masyarakat, serta evaluasi program akhir tahun.</p>
    </div>

    <!-- BAB II PROFIL MADRASAH -->
    <div class="page page-break">
        <h1>BAB II<br>PROFIL MADRASAH</h1>
        <br>
        <h3>A. Identitas Madrasah</h3>
        <table class="waka-table">
            <tr>
                <td style="width: 35%; font-weight: bold;">Nama Madrasah</td>
                <td><?php echo htmlspecialchars($sekolah_nama); ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">NPSN / NSM</td>
                <td><?php echo htmlspecialchars($npsn); ?> / <?php echo htmlspecialchars($nsm); ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Alamat Lengkap</td>
                <td><?php echo htmlspecialchars($alamat); ?>, Kec. <?php echo htmlspecialchars($kecamatan); ?>, <?php echo htmlspecialchars($kabupaten); ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Kontak / Surel</td>
                <td><?php echo htmlspecialchars($email); ?> / <?php echo htmlspecialchars($website); ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Kepala Madrasah</td>
                <td><?php echo htmlspecialchars($kepala_nama); ?></td>
            </tr>
        </table>

        <h3>B. Sejarah Singkat Madrasah</h3>
        <p>MI Miftahul Huda Mathla'ul Anwar didirikan atas inisiasi tokoh agama dan tokoh masyarakat setempat untuk memfasilitasi kebutuhan pendidikan dasar berciri khas Islam. Seiring perkembangan waktu, madrasah mengalami transformasi infrastruktur dan manajemen profesional hingga diakui sebagai salah satu lembaga andalan dalam pembinaan karakter anak.</p>

        <h3>C. Visi, Misi, dan Tujuan Madrasah</h3>
        <p class="no-indent"><strong>Visi:</strong> "Terwujudnya Generasi yang Berakhlakul Karimah, Cerdas, Terampil, dan Unggul dalam Prestasi Keagamaan."</p>
        <p class="no-indent"><strong>Misi:</strong></p>
        <ul>
            <li>Menyelenggarakan proses pembelajaran keagamaan secara konsisten dan terarah.</li>
            <li>Membina kedisiplinan dan sopan santun berbasis nilai-nilai akhlak mulia.</li>
            <li>Mengembangkan kompetensi IPTEK guru dan siswa secara kreatif.</li>
            <li>Membiasakan budaya cinta lingkungan bersih di lingkungan madrasah.</li>
        </ul>
        <p class="no-indent"><strong>Tujuan Madrasah:</strong> Meningkatkan kompetensi kelulusan siswa, membekali hafalan Juz 30 bagi lulusan, dan mengantarkan prestasi lulusan madrasah ke jenjang lanjutan terbaik.</p>

        <h3>D. Struktur Organisasi Madrasah</h3>
        <p>Struktur organisasi MI Miftahul Huda Mathla'ul Anwar beranggotakan unsur pimpinan, komite, unit administrasi tata usaha, koordinator kurikulum, kesiswaan, sarana prasarana, humas, serta pembina ekstrakurikuler. Pembagian kewenangan diatur secara jelas guna menghindari tumpang tindih tugas.</p>

        <h3>E. Data Guru, Rombel & Peserta Didik</h3>
        <p class="no-indent">Berikut data ketenagaan aktif madrasah:</p>
        <table class="waka-table" style="font-size: 9pt;">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Nama Lengkap</th>
                    <th>Identitas / NIP</th>
                    <th>Tugas Utama</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($gurus as $g): ?>
                    <tr>
                        <td align="center"><?php echo $no++; ?></td>
                        <td><strong><?php echo htmlspecialchars($g['nama']); ?></strong></td>
                        <td><?php echo htmlspecialchars($g['nip'] ?: '-'); ?></td>
                        <td><?php echo htmlspecialchars($g['jabatan'] ?: 'Pendidik'); ?></td>
                        <td align="center"><?php echo htmlspecialchars($g['status_kepegawaian'] ?? 'Honorer'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p class="no-indent">Sebaran rombongan belajar (rombel) tahun pelajaran berjalan:</p>
        <table class="waka-table" style="font-size: 9pt;">
            <thead>
                <tr>
                    <th style="width: 10%;">No</th>
                    <th>Nama Rombel / Kelas</th>
                    <th>Tingkat</th>
                    <th style="text-align: center;">Jumlah Siswa</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($kelasList as $k): ?>
                    <tr>
                        <td align="center"><?php echo $no++; ?></td>
                        <td><strong>Kelas <?php echo htmlspecialchars($k['nama_kelas']); ?></strong></td>
                        <td align="center">Tingkat <?php echo htmlspecialchars($k['tingkat']); ?></td>
                        <td align="center" style="font-weight: bold;"><?php echo $k['jumlah_siswa']; ?> Siswa</td>
                    </tr>
                <?php endforeach; ?>
                <tr style="background: #f2f2f2; font-weight: bold;">
                    <td colspan="3" align="right">TOTAL KESELURUHAN SISWA AKTIF:</td>
                    <td align="center"><?php echo $total_siswa; ?> Siswa</td>
                </tr>
            </tbody>
        </table>

        <h3>F. Data Sarana dan Prasarana (Sarpras)</h3>
        <table class="waka-table" style="font-size: 9.5pt;">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th>Nama Prasarana / Ruangan</th>
                    <th style="text-align: center; width: 15%;">Jumlah</th>
                    <th style="text-align: center; width: 20%;">Kondisi Baik</th>
                    <th style="text-align: center; width: 20%;">Kondisi Rusak</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center">1</td>
                    <td>Ruang Kelas Pembelajaran</td>
                    <td align="center">6 Ruang</td>
                    <td align="center">6</td>
                    <td align="center">0</td>
                    <td>Digunakan untuk KBM kelas I - VI</td>
                </tr>
                <tr>
                    <td align="center">2</td>
                    <td>Ruang Kantor Pimpinan & Guru</td>
                    <td align="center">1 Ruang</td>
                    <td align="center">1</td>
                    <td align="center">0</td>
                    <td>Kondisi ber-AC dan representatif</td>
                </tr>
                <tr>
                    <td align="center">3</td>
                    <td>Perpustakaan Madrasah</td>
                    <td align="center">1 Ruang</td>
                    <td align="center">1</td>
                    <td align="center">0</td>
                    <td>Koleksi buku kurikulum dan umum</td>
                </tr>
                <tr>
                    <td align="center">4</td>
                    <td>Kantin Sehat Siswa</td>
                    <td align="center">1 Unit</td>
                    <td align="center">1</td>
                    <td align="center">0</td>
                    <td>Menyediakan makanan sehat higienis</td>
                </tr>
                <tr>
                    <td align="center">5</td>
                    <td>Fasilitas Toilet / MCK</td>
                    <td align="center">4 Unit</td>
                    <td align="center">3</td>
                    <td align="center">1</td>
                    <td>Memerlukan sedikit perbaikan sanitasi</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- BAB III ANALISIS KONDISI MADRASAH -->
    <div class="page page-break">
        <h1>BAB III<br>ANALISIS KONDISI MADRASAH</h1>
        <br>
        <h3>A. Analisis 8 Standar Nasional Pendidikan (SNP)</h3>
        <ol>
            <li><strong>Standar Isi:</strong> KTSP dan dokumen kurikulum merdeka telah tersusun rapi, namun pengembangan instrumen muatan lokal keagamaan perlu ditingkatkan porsinya.</li>
            <li><strong>Standar Proses:</strong> Pelaksanaan pembelajaran guru sebagian besar telah berbasis IT. Penggunaan proyektor interaktif di kelas perlu ditingkatkan secara merata.</li>
            <li><strong>Standar Penilaian:</strong> Asesmen formatif dan sumatif berjalan digital melalui portal GHCBT. Kemandirian penyusunan bank soal HOTS guru perlu terus dilatih.</li>
            <li><strong>Standar PTK:</strong> Kualifikasi akademik pendidik telah 90% S1. Pelatihan eksternal guru perlu ditingkatkan agar memperbaharui kompetensi metodologis.</li>
            <li><strong>Standar Sarana dan Prasarana:</strong> Pemenuhan laboratorium komputer mandiri telah siap, pemeliharaan sanitasi MCK perlu perhatian berkala.</li>
            <li><strong>Standar Pengelolaan:</strong> Transparansi tata pamong berjalan baik melalui penyusunan Rencana Kerja Tahunan yang terbuka.</li>
            <li><strong>Standar Pembiayaan:</strong> Sumber dana madrasah bertumpu pada BOS APBN dan donasi sukarela. Pengelolaan keuangan diinput di sistem e-RKAM secara tertib.</li>
            <li><strong>Standar Kompetensi Lulusan (SKL):</strong> Rata-rata nilai rapor kelulusan meningkat, pemantapan tahfidz juz 30 terus dikawal ketat.</li>
        </ol>

        <h3>B. Analisis SWOT Madrasah</h3>
        <table class="waka-table swot-table" style="font-size:9.5pt;">
            <thead>
                <tr>
                    <th style="width: 50%;">KEKUATAN (STRENGTHS)</th>
                    <th style="width: 50%;">WEAKNESSES (WEAKNESSES)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <ul>
                            <li>Ciri khas keagamaan Islam menjadi pilar kepercayaan masyarakat.</li>
                            <li>Kualifikasi akademik pendidik mayoritas sarjana (S1).</li>
                            <li>Infrastruktur internet dan portal ajar digital (Aula System) sudah berjalan.</li>
                        </ul>
                    </td>
                    <td>
                        <ul>
                            <li>Ketergantungan keuangan yang dominan pada dana BOS APBN.</li>
                            <li>Keterbatasan pelatihan guru kelas bersertifikasi tingkat regional.</li>
                            <li>Perawatan sanitasi toilet memerlukan biaya tak terduga.</li>
                        </ul>
                    </td>
                </tr>
                <tr style="background:#f2f2f2;">
                    <th>PELUANG (OPPORTUNITIES)</th>
                    <th>ANCAMAN (THREATS)</th>
                </tr>
                <tr>
                    <td>
                        <ul>
                            <li>Dukungan aktif komite madrasah dan wali murid.</li>
                            <li>Peluang menjalin kemitraan beasiswa dengan donatur eksternal.</li>
                            <li>PPDB stabil dan peminat tinggi di tingkat wilayah.</li>
                        </ul>
                    </td>
                    <td>
                        <ul>
                            <li>Persaingan dengan SD/MI swasta berfasilitas modern.</li>
                            <li>Dampak negatif gadget di luar jam pengawasan sekolah.</li>
                            <li>Standar akreditasi BAN-PDM yang menuntut kelayakan berkas fisik.</li>
                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- BAB IV PROGRAM KERJA KEPALA MADRASAH -->
    <div class="page page-break">
        <h1>BAB IV<br>PROGRAM KERJA KEPALA MADRASAH</h1>
        <br>
        <h3>A. Program Bidang Kurikulum</h3>
        <ol>
            <li><strong>Penyusunan Kalender Pendidikan:</strong> Menyusun kaldik sekolah yang sinkron dengan kalender Kemenag Provinsi guna memetakan minggu efektif, jadwal ujian, dan libur akademik.</li>
            <li><strong>Penyusunan Jadwal Pelajaran:</strong> Mendistribusikan beban jam mengajar wajib minimal 24 jam tatap muka untuk persyaratan sertifikasi profesional guru kelas secara merata.</li>
            <li><strong>Penyusunan Struktur Kurikulum:</strong> Menerapkan KMA 347 Tahun 2022 tentang pedoman Kurikulum Merdeka pada kelas I dan IV serta kurikulum 2013 pada tingkat lainnya.</li>
            <li><strong>Supervisi Akademik & Monitoring:</strong> Menjadwalkan pengawasan langsung ajar di kelas serta mendisiplinkan pengisian jurnal ajar harian guru di portal sekolah.</li>
            <li><strong>Penguatan Literasi/Numerasi:</strong> Membiasakan membaca buku non-pelajaran 15 menit sebelum KBM (Silent Reading) dan latihan teka-teki logika hitung cepat (flashcard).</li>
            <li><strong>Analisis Hasil Belajar:</strong> Mewajibkan guru melakukan analisis daya serap siswa pasca ujian sumatif harian guna merancang kelas remedial dan pengayaan.</li>
        </ol>

        <h3>B. Program Bidang Kesiswaan</h3>
        <ol>
            <li><strong>Pembinaan Karakter & Disiplin:</strong> Menerapkan tata tertib berpakaian rapi, budaya salam 5S, larangan gadget, serta sanksi edukatif pelanggaran kedisiplinan kelas.</li>
            <li><strong>Pengembangan Ekstrakurikuler:</strong> Memfasilitasi kegiatan ekskul Pramuka (Wajib), seni rebana hadroh, olahraga bulu tangkis, pencak silat, dan pembinaan olimpiade sains (KSM).</li>
            <li><strong>Kegiatan Keagamaan Rutin:</strong> Shalat dhuha/dzuhur berjamaah, pembacaan asmaul husna, hafalan juz 30, serta peringatan hari besar islam (PHBI) dan nasional (PHBN).</li>
        </ol>

        <h3>C. Program Bidang Sarana dan Prasarana</h3>
        <ol>
            <li><strong>Inventarisasi Aset Madrasah:</strong> Pencatatan seluruh mebeler dan peralatan kelas ke buku induk barang serta penempelan barcode inventaris digital.</li>
            <li><strong>Pemeliharaan Gedung & Sanitasi:</strong> Melakukan perbaikan atap bocor, pengecatan dinding kelas, pembersihan toilet siswa secara harian, dan perawatan unit lab komputer.</li>
            <li><strong>Pengadaan Media Pembelajaran:</strong> Menyediakan proyektor portable, penambahan bandwidth router WiFi, serta melengkapi koleksi buku perpustakaan.</li>
        </ol>

        <h3>D. Program Bidang Humas</h3>
        <ol>
            <li><strong>Kerja Sama Komite & Wali Murid:</strong> Mengadakan forum silaturahmi berkala pasca ujian tengah semester (PTS) guna menyerahkan raport mid-semester dan mendiskusikan kemajuan anak.</li>
            <li><strong>Kerja Sama Kemitraan:</strong> Koordinasi berkala imunisasi dengan Puskesmas, sosialisasi bahaya bullying dengan kepolisian, serta promosi madrasah di media sosial resmi.</li>
        </ol>

        <h3>E. Program Bidang Keuangan</h3>
        <ol>
            <li><strong>RKAM & Dana BOS:</strong> Merancang RKAM secara akuntabel menggunakan aplikasi e-RKAM, pengawasan kas masuk-keluar secara ketat, serta pengunggahan LPJ BOS online tepat waktu.</li>
        </ol>

        <h3>F. Program Bidang Ketenagaan</h3>
        <ol>
            <li><strong>Pembinaan Guru:</strong> Melakukan supervisi manajerial tata usaha, penilaian kinerja guru (PKG) tahunan, workshop ajar internal, dan penerapan 5 Nilai Budaya Kerja Kemenag.</li>
        </ol>
    </div>

    <!-- BAB V PROGRAM SUPERVISI -->
    <div class="page page-break">
        <h1>BAB V<br>PROGRAM SUPERVISI</h1>
        <br>
        <h3>A. Supervisi Akademik & Administrasi Guru</h3>
        <p>Supervisi akademik diarahkan sebagai pembinaan profesional guru. Aspek utama supervisi adalah memeriksa ketersediaan Prota, Promes, ATP, Modul Ajar, serta kriteria KKTP sebelum guru mengajar di awal semester. Pengawasan dilakukan oleh Kepala Madrasah secara klinis.</p>

        <h3>B. Observasi Proses Pembelajaran di Kelas</h3>
        <p>Observasi kelas dilaksanakan terjadwal (tahap I di bulan September dan tahap II di bulan Februari). Kepala Madrasah mengamati interaksi guru-siswa, pemanfaatan media ajar IT, serta pengelolaan kelas ramah anak.</p>

        <h3>C. Supervisi Manajerial</h3>
        <p>Supervisi manajerial ditujukan kepada para Waka, Bendahara, dan Staf TU untuk meninjau ketertiban berkas disposisi surat masuk/keluar, arsip kuitansi BOS, ketertiban buku induk siswa, absensi kehadiran guru, serta buku rekap aset madrasah.</p>

        <h3>D. Tindak Lanjut Supervisi</h3>
        <p>Hasil supervisi didokumentasikan. Terhadap temuan guru yang kurang menguasai media IT akan dibina melalui pendampingan rekan sejawat (peer tutoring), sedangkan guru berkinerja  akan diberikan piagam penghargaan.</p>
        
        <h3>E. Jadwal Pelaksanaan Supervisi Guru Kelas</h3>
        <table class="waka-table" style="font-size:9.5pt;">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th>Nama Pendidik</th>
                    <th>Kelas Sasaran</th>
                    <th>Jadwal Semester I</th>
                    <th>Jadwal Semester II</th>
                    <th>Supervisor</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($gurus as $g): ?>
                    <?php if($g['nama'] !== $kepala_nama): ?>
                        <tr>
                            <td align="center"><?php echo $no++; ?></td>
                            <td><strong><?php echo htmlspecialchars($g['nama']); ?></strong></td>
                            <td>Kelas Mengajar</td>
                            <td align="center">September</td>
                            <td align="center">Februari</td>
                            <td>Kepala Madrasah</td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- BAB VI PROGRAM PENGEMBANGAN MADRASAH -->
    <div class="page page-break">
        <h1>BAB VI<br>PROGRAM PENGEMBANGAN MADRASAH</h1>
        <br>
        <h3>A. Pengembangan Literasi & Numerasi</h3>
        <p>Pengembangan literasi dilakukan dengan melengkapi sudut baca (Reading Corner) di setiap ruang kelas dan memperkaya buku bacaan fiksi/non-fiksi di perpustakaan madrasah. Pengembangan numerasi dikemas dalam game logika hitung cepat di awal pelajaran.</p>

        <h3>B. Penguatan Pendidikan Karakter (PPK) & Keagamaan</h3>
        <p>PPK berbasis keagamaan diwujudkan melalui pembiasaan shalat dhuha bersama, shalat dzuhur berjamaah, pembacaan asmaul husna, hafalan juz 30, serta pembentukan karakter disiplin, mandiri, dan cinta tanah air.</p>

        <h3>C. Digitalisasi Madrasah</h3>
        <p>Digitalisasi madrasah mencakup pemanfaatan portal smart school digital (Aula System) untuk input nilai rapor RDM, absensi guru, pengisian jurnal mengajar harian guru, ujian berbasis komputer CBT, dan persuratan digital.</p>

        <h3>D. Pengembangan Kemitraan</h3>
        <p>Meningkatkan kerja sama kemitraan beasiswa pendidikan bagi siswa berprestasi dan kurang mampu dengan yayasan donasi eksternal serta dinas terkait.</p>
    </div>

    <!-- BAB VII MONITORING DAN EVALUASI -->
    <div class="page page-break">
        <h1>BAB VII<br>MONITORING DAN EVALUASI</h1>
        <br>
        <h3>A. Monitoring Program Kerja</h3>
        <p>Monitoring program dilakukan sepanjang tahun pelajaran oleh tim penjamin mutu internal madrasah guna memantau kesesuaian antara rencana program dengan realisasi fisik di lapangan.</p>

        <h3>B. Evaluasi Bulanan & Semesteran</h3>
        <p>Evaluasi berkala diselenggarakan melalui rapat dinas guru setiap akhir bulan untuk membahas hambatan KBM, sedangkan evaluasi semesteran fokus pada ketuntasan pencapaian kurikulum, kelulusan ujian sumatif, dan penyerahan laporan keuangan BOS.</p>

        <h3>C. Evaluasi Tahunan & Tindak Lanjut (RTL)</h3>
        <p>Di akhir tahun pelajaran, diselenggarakan rapat pleno evaluasi ketercapaian program tahun berjalan. Program yang belum terealisasi akan dianalisis kendalanya untuk dijadikan bahan perbaikan rencana program tahun depan dan disusun ke dalam dokumen Rencana Tindak Lanjut (RTL).</p>
    </div>

    <!-- BAB VIII PENUTUP -->
    <div class="page page-break">
        <h1>BAB VIII<br>PENUTUP</h1>
        <br>
        <h3>A. Kesimpulan</h3>
        <p>Program Kerja Kepala Madrasah MI Miftahul Huda Mathla'ul Anwar Tahun Pelajaran <?php echo htmlspecialchars($tahun_ajaran); ?> merupakan kerangka acuan kerja manajemen puncak madrasah yang komprehensif. Keberhasilan pelaksanaan program kerja ini sangat bergantung pada kedisiplinan, komitmen bersama, komunikasi harmonis, serta dukungan dari komite madrasah, wali murid, yayasan, dan bimbingan pengawas Kantor Kementerian Agama.</p>

        <h3>B. Harapan</h3>
        <p>Kami berharap program ini dapat diimplementasikan secara optimal demi mewujudkan cita-cita madrasah yang mandiri berprestasi, berdaya saing global, serta senantiasa mencetak lulusan berakhlak mulia.</p>

        <br><br>
        <table class="sign-table">
            <tr>
                <td>
                    Menyetujui,<br>
                    Ketua Komite Madrasah<br><br><br><br><br>
                    <strong>HASAN BASRI</strong>
                </td>
                <td>
                    Way Tenong, <?php echo date('d F Y'); ?><br>
                    Kepala Madrasah<br><br><br><br><br>
                    <strong><?php echo htmlspecialchars($kepala_nama); ?></strong><br>
                    NIP. <?php echo htmlspecialchars($kepala_nip ?: '-'); ?>
                </td>
            </tr>
        </table>
    </div>

    <!-- LAMPIRAN -->
    <div class="page">
        <h1>LAMPIRAN-LAMPIRAN</h1>
        <br><br><br>
        <p class="no-indent" style="text-align: center; font-weight: bold; font-size: 14pt;">DAFTAR LAMPIRAN DOKUMEN PENUNJANG PROGRAM KERJA</p>
        <br><br>
        <ol style="padding-left: 2cm; font-size: 12pt; line-height: 2.2;">
            <li>SK Tim Manajemen Madrasah & Struktur Organisasi.</li>
            <li>Kalender Pendidikan Tahun Pelajaran <?php echo htmlspecialchars($tahun_ajaran); ?>.</li>
            <li>Struktur Kurikulum & Jadwal Pelajaran Mingguan Rinci.</li>
            <li>Keputusan Kepala Madrasah tentang Pembagian Tugas Mengajar Guru.</li>
            <li>Jadwal Supervisi Akademik & Supervisi Manajerial.</li>
            <li>Instrumen Penilaian Kinerja Guru (PKG) & Format Monitoring Jurnal.</li>
            <li>Buku Laporan Sarana Prasarana & Rekap Keadaan Peserta Didik.</li>
            <li>Dokumen Program Kerja Waka Bidang Kurikulum, Kesiswaan, Sarpras & Humas.</li>
        </ol>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>


