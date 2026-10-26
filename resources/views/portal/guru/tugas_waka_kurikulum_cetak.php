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

$kepala_nama = $inst['nama_kepala'] ?? 'SAMSUL ARIFIN, M.Pd';
$kepala_nip = $inst['nip_kepala'] ?? '-';
$waka_nama = $guru_detail['nama'] ?? 'Waka Kurikulum';
$waka_nip = $guru_detail['nip'] ?? '-';

$tahun_ajaran = $active_year_name ?? '2026/2027';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Kerja Waka Kurikulum - <?php echo htmlspecialchars($sekolah_nama); ?></title>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <style>
        /* CSS Print A4 Setup with Skripsi Margins */
        @page { 
            size: A4; 
            margin: 0; 
        }
        body {
            background: #cbd5e1;
            margin: 0;
            padding: 0;
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
        }
        
        .no-print-bar {
            background: #0284c7;
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
            color: #0284c7;
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
            padding: 3cm 3cm 3.5cm 4cm; /* Skripsi Margins: Left 4cm, Top 3cm, Right 3cm, Bottom 3.5cm */
            margin: 20px auto;
            background: white;
            box-sizing: border-box;
            box-shadow: 0 0 15px rgba(0,0,0,0.15);
            position: relative;
            page-break-after: always;
        }
        
        @media print {
            body { background: white; }
            .no-print-bar { display: none; }
            .page {
                margin: 0;
                box-shadow: none;
                width: 210mm;
                height: 297mm;
                page-break-after: always;
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
        }
        h1 { font-size: 16pt; text-transform: uppercase; }
        h2 { font-size: 14pt; text-transform: uppercase; margin-bottom: 25px; }
        h3 { font-size: 12pt; text-align: left; text-transform: uppercase; margin-top: 20px; }
        h4 { font-size: 12pt; text-align: left; margin-left: 20px; margin-top: 10px; font-weight: bold; }
        
        p {
            text-align: justify;
            text-indent: 1.25cm;
            margin: 0 0 10px 0;
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
        
        li {
            margin-bottom: 5px;
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
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 18pt;
            font-weight: bold;
            line-height: 1.4;
            color: #14532d;
            text-transform: uppercase;
        }
        .cover-subtitle {
            font-size: 14pt;
            font-weight: bold;
            color: #ca8a04;
            margin-top: 10px;
        }

        /* Signature Blocks */
        .sign-table {
            width: 100%;
            margin-top: 50px;
            border-collapse: collapse;
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
            <strong style="font-size: 1.1rem;">Program Kerja Waka Kurikulum - Preview Cetak</strong>
            <span style="font-size: 0.8rem; opacity: 0.8; margin-left: 15px;">Tip: Aktifkan "Background Graphics" dan setel Margin: Default di browser Anda.</span>
        </div>
        <button class="btn-print" onclick="window.print()"><i data-lucide="printer" style="width:16px;height:16px;"></i> Cetak / Simpan PDF</button>
    </div>

    <!-- Halaman 1: Cover -->
    <div class="page">
        <div class="cover-container" style="padding: 20px 0; height: 100%; display: flex; flex-direction: column; justify-content: space-between; min-height: 220mm;">
            <div>
                <span style="border: 2px solid #14532d; padding: 6px 20px; font-size: 10pt; font-weight: bold; letter-spacing: 2px; color: #14532d; border-radius: 4px;">DOKUMEN ADMINISTRASI MADRASAH</span>
                <div class="cover-title" style="margin-top: 50px;">
                    PROGRAM KERJA<br>WAKIL KEPALA MADRASAH<br>BIDANG KURIKULUM
                </div>
                <div class="cover-subtitle">
                    TAHUN PELAJARAN <?php echo htmlspecialchars($tahun_ajaran); ?>
                </div>
            </div>
            
            <div>
                <img src="<?php echo \App\Core\Branding::getInstitusiLogo(); ?>" class="cover-logo" alt="Logo Madrasah">
            </div>

            <div style="font-family: 'Plus Jakarta Sans', sans-serif;">
                <p class="no-indent" style="text-align: center; font-weight: bold; font-size: 14pt; margin: 0; color: #14532d;">MI MIFTAHUL HUDA MATHLA'UL ANWAR</p>
                <p class="no-indent" style="text-align: center; font-size: 10pt; color: #475569; margin-top: 5px; line-height: 1.4;">
                    NPSN: <?php echo htmlspecialchars($npsn); ?> | NSM: <?php echo htmlspecialchars($nsm); ?><br>
                    <?php echo htmlspecialchars($alamat); ?>, Kec. <?php echo htmlspecialchars($kecamatan); ?>, <?php echo htmlspecialchars($kabupaten); ?> - <?php echo htmlspecialchars($provinsi); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Halaman 2: Lembar Pengesahan -->
    <div class="page">
        <h1>LEMBAR PENGESAHAN</h1>
        <br><br>
        <p class="no-indent" style="text-align: center;">Program Kerja Wakil Kepala Madrasah Bidang Kurikulum MI Miftahul Huda Mathla'ul Anwar Tahun Pelajaran <?php echo htmlspecialchars($tahun_ajaran); ?> ini telah diperiksa, disetujui, dan disahkan untuk dijadikan pedoman dalam pelaksanaan kurikulum dan proses belajar mengajar di madrasah.</p>
        
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
                    Kepala Madrasah<br><br><br><br><br>
                    <strong><?php echo htmlspecialchars($kepala_nama); ?></strong><br>
                    NIP. <?php echo htmlspecialchars($kepala_nip ?: '-'); ?>
                </td>
                <td>
                    Yang Mengajukan,<br>
                    Waka Bidang Kurikulum<br><br><br><br><br>
                    <strong><?php echo htmlspecialchars($waka_nama); ?></strong><br>
                    NIP. <?php echo htmlspecialchars($waka_nip ?: '-'); ?>
                </td>
            </tr>
        </table>
    </div>

    <!-- Halaman 3: Kata Pengantar -->
    <div class="page">
        <h1>KATA PENGANTAR</h1>
        <br>
        <p>Bismillahirrohmanirrohim. Puji syukur senantiasa penulis panjatkan kehadirat Allah SWT, yang telah melimpahkan rahmat, taufik, serta hidayah-Nya, sehingga program kerja Wakil Kepala Madrasah Bidang Kurikulum MI Miftahul Huda Mathla'ul Anwar untuk Tahun Pelajaran <?php echo htmlspecialchars($tahun_ajaran); ?> ini dapat diselesaikan dengan baik.</p>
        <p>Program kerja ini disusun sebagai acuan operasional dan arah kebijakan akademis dalam mengelola proses pembelajaran di madrasah selama satu tahun pelajaran. Fokus utama dari program kerja ini adalah peningkatan mutu proses dan hasil belajar peserta didik, pengembangan kompetensi profesional guru, serta penyempurnaan manajemen kurikulum madrasah secara terintegrasi.</p>
        <p>Penulis menyadari bahwa keberhasilan pelaksanaan program kerja ini sangat bergantung pada komitmen, kerja sama, dan dedikasi seluruh elemen di madrasah, mulai dari Kepala Madrasah, rekan-rekan pendidik, tenaga kependidikan, komite madrasah, hingga peran aktif orang tua siswa. Saran dan kritik yang membangun senantiasa diharapkan demi penyempurnaan kurikulum madrasah di masa mendatang.</p>
        <p>Semoga program kerja ini bermanfaat dan diridhoi oleh Allah SWT sebagai amal ibadah kita bersama dalam upaya mencerdaskan generasi bangsa yang berakhlak mulia.</p>
        
        <table class="sign-table">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%;">
                    Way Tenong, <?php echo date('d F Y'); ?><br>
                    Waka Kurikulum<br><br><br><br>
                    <strong><?php echo htmlspecialchars($waka_nama); ?></strong>
                </td>
            </tr>
        </table>
    </div>

    <!-- Halaman 4: Daftar Isi -->
    <div class="page">
        <h1>DAFTAR ISI</h1>
        <br><br>
        <ul class="toc-list">
            <li class="toc-item">
                <span class="toc-name">HALAMAN JUDUL / COVER</span><span class="toc-page">i</span>
            </li>
            <li class="toc-item">
                <span class="toc-name">LEMBAR PENGESAHAN</span><span class="toc-page">ii</span>
            </li>
            <li class="toc-item">
                <span class="toc-name">KATA PENGANTAR</span><span class="toc-page">iii</span>
            </li>
            <li class="toc-item">
                <span class="toc-name">DAFTAR ISI</span><span class="toc-page">iv</span>
            </li>
            <li class="toc-item">
                <span class="toc-name">BAB I. PENDAHULUAN</span><span class="toc-page">1</span>
            </li>
            <li class="toc-subitem">
                <span class="toc-name">A. Latar Belakang</span><span class="toc-page">1</span>
            </li>
            <li class="toc-subitem">
                <span class="toc-name">B. Dasar Hukum</span><span class="toc-page">2</span>
            </li>
            <li class="toc-subitem">
                <span class="toc-name">C. Tujuan Program</span><span class="toc-page">2</span>
            </li>
            <li class="toc-subitem">
                <span class="toc-name">D. Sasaran Program</span><span class="toc-page">3</span>
            </li>
            <li class="toc-item">
                <span class="toc-name">BAB II. PROFIL DAN KONDISI MADRASAH</span><span class="toc-page">4</span>
            </li>
            <li class="toc-subitem">
                <span class="toc-name">A. Profil Umum Madrasah</span><span class="toc-page">4</span>
            </li>
            <li class="toc-subitem">
                <span class="toc-name">B. Keadaan Guru dan Tenaga Kependidikan</span><span class="toc-page">4</span>
            </li>
            <li class="toc-subitem">
                <span class="toc-name">C. Rombongan Belajar dan Keadaan Siswa</span><span class="toc-page">5</span>
            </li>
            <li class="toc-subitem">
                <span class="toc-name">D. Analisis Kondisi Akademik</span><span class="toc-page">5</span>
            </li>
            <li class="toc-item">
                <span class="toc-name">BAB III. RENCANA PROGRAM KERJA KURIKULUM</span><span class="toc-page">6</span>
            </li>
            <li class="toc-subitem">
                <span class="toc-name">A. Rencana Kegiatan Tahunan</span><span class="toc-page">6</span>
            </li>
            <li class="toc-subitem">
                <span class="toc-name">B. Program Kerja Semesteran</span><span class="toc-page">8</span>
            </li>
            <li class="toc-subitem">
                <span class="toc-name">C. Program Kegiatan Bulanan</span><span class="toc-page">9</span>
            </li>
            <li class="toc-item">
                <span class="toc-name">BAB IV. PENGELOLAAN PEMBELAJARAN</span><span class="toc-page">11</span>
            </li>
            <li class="toc-item">
                <span class="toc-name">BAB V. SISTEM PENILAIAN DAN ASESMEN</span><span class="toc-page">13</span>
            </li>
            <li class="toc-item">
                <span class="toc-name">BAB VI. SUPERVISI AKADEMIK GURU</span><span class="toc-page">15</span>
            </li>
            <li class="toc-item">
                <span class="toc-name">BAB VII. PENGEMBANGAN SDM PENDIDIK</span><span class="toc-page">17</span>
            </li>
            <li class="toc-item">
                <span class="toc-name">BAB VIII. MONITORING, EVALUASI & TINDAK LANJUT</span><span class="toc-page">18</span>
            </li>
            <li class="toc-item">
                <span class="toc-name">BAB IX. PENUTUP</span><span class="toc-page">20</span>
            </li>
            <li class="toc-item">
                <span class="toc-name">LAMPIRAN-LAMPIRAN</span><span class="toc-page">21</span>
            </li>
        </ul>
    </div>

    <!-- Halaman 5: BAB I Pendahuluan -->
    <div class="page">
        <h1>BAB I<br>PENDAHULUAN</h1>
        <br>
        <h3>A. Latar Belakang</h3>
        <p>Pendidikan merupakan pilar utama pembangunan sumber daya manusia yang berkarakter, cerdas, dan religius. Madrasah sebagai institusi pendidikan Islam memiliki peran ganda yang strategis, yakni membina kemampuan intelektual akademis sekaligus membimbing spiritualitas dan akhlak mulia siswa. Keberhasilan misi ini sangat ditentukan oleh bagaimana kurikulum di madrasah tersebut direncanakan, dikelola, dievaluasi, dan dikembangkan secara berkesinambungan.</p>
        <p>Kurikulum adalah jantung dari proses pendidikan di sekolah. Pengelolaan kurikulum yang efektif menjamin proses belajar mengajar (KBM) berjalan secara terukur dan berkualitas. Sebagai langkah taktis dan strategis, Wakil Kepala Madrasah Bidang Kurikulum bertanggung jawab menyusun arah program akademis madrasah yang adaptif terhadap perkembangan teknologi dan regulasi terkini tanpa meninggalkan nilai-nilai dasar keislaman.</p>
        <p>MI Miftahul Huda Mathla'ul Anwar sebagai salah satu madrasah di lingkungan Kementerian Agama berkomitmen tinggi meningkatkan mutu pembelajaran setiap tahunnya. Atas dasar itulah, Program Kerja Bidang Kurikulum ini disusun untuk dijadikan rujukan baku bagi guru dalam menyusun administrasi ajar, melaksanakan penilaian, serta melakukan tindak lanjut supervisi secara terarah selama Tahun Pelajaran <?php echo htmlspecialchars($tahun_ajaran); ?>.</p>

        <h3>B. Dasar Hukum</h3>
        <p class="no-indent">Penyusunan Program Kerja Kurikulum ini berlandaskan pada regulasi dan dasar hukum berikut:</p>
        <ol>
            <li>Undang-Undang Nomor 20 Tahun 2003 tentang Sistem Pendidikan Nasional.</li>
            <li>Peraturan Pemerintah Republik Indonesia Nomor 57 Tahun 2021 tentang Standar Nasional Pendidikan sebagaimana telah diubah dengan PP Nomor 4 Tahun 2022.</li>
            <li>Peraturan Menteri Agama (PMA) Republik Indonesia yang relevan mengenai pengelolaan pendidikan di madrasah.</li>
            <li>Keputusan Menteri Agama (KMA) Nomor 347 Tahun 2022 tentang Pedoman Implementasi Kurikulum Merdeka pada Madrasah.</li>
            <li>Keputusan Kepala MI Miftahul Huda Mathla'ul Anwar tentang Pembagian Tugas Mengajar Guru Tahun Pelajaran <?php echo htmlspecialchars($tahun_ajaran); ?>.</li>
        </ol>
    </div>

    <!-- Halaman 6: BAB I Pendahuluan (Lanjutan) -->
    <div class="page">
        <h3>C. Tujuan Program</h3>
        <p class="no-indent"><strong>Tujuan Umum:</strong></p>
        <p>Tujuan umum penyusunan program kerja ini adalah untuk meningkatkan efektivitas proses pembelajaran dan mutu kelulusan siswa secara komprehensif, baik dari aspek akademis kognitif maupun nilai spiritual keagamaan.</p>
        <p class="no-indent"><strong>Tujuan Khusus:</strong></p>
        <ol>
            <li>Mengoptimalkan implementasi Kurikulum Merdeka dan Kurikulum 2013 di madrasah secara terstruktur.</li>
            <li>Meningkatkan kualitas perencanaan pembelajaran guru melalui verifikasi perangkat ajar secara berkala.</li>
            <li>Meningkatkan validitas dan reliabilitas sistem penilaian (asesmen diagnostik, formatif, sumatif, PTS, PAS, dan PAT).</li>
            <li>Meningkatkan prestasi akademik peserta didik dalam ajang kompetensi sains dan keagamaan.</li>
            <li>Melaksanakan supervisi klinis klinis secara terjadwal untuk meningkatkan kompetensi profesional pendidik.</li>
        </ol>

        <h3>D. Sasaran Program</h3>
        <p class="no-indent">Sasaran utama dari pelaksanaan program kerja kurikulum ini adalah:</p>
        <ol>
            <li><strong>Tenaga Pendidik (Guru):</strong> Meningkatkan disiplin administratif ajar serta kepatuhan KBM berbasis IT.</li>
            <li><strong>Wali Kelas:</strong> Mengoptimalkan pembinaan karakter siswa dan koordinasi laporan raport wali kelas.</li>
            <li><strong>Peserta Didik:</strong> Terlaksananya hak belajar siswa dengan suasana madrasah yang kondusif dan aktif.</li>
            <li><strong>Tenaga Kependidikan:</strong> Sinergi administrasi nilai, surat menyurat, dan data pokok EMIS.</li>
        </ol>
    </div>

    <!-- Halaman 7: BAB II Profil dan Kondisi Madrasah -->
    <div class="page">
        <h1>BAB II<br>PROFIL DAN KONDISI MADRASAH</h1>
        <br>
        <h3>A. Profil Umum Madrasah</h3>
        <p class="no-indent">Profil identitas MI Miftahul Huda Mathla'ul Anwar secara rinci disajikan sebagai berikut:</p>
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
                <td style="font-weight: bold;">Alamat</td>
                <td><?php echo htmlspecialchars($alamat); ?>, Kec. <?php echo htmlspecialchars($kecamatan); ?>, <?php echo htmlspecialchars($kabupaten); ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Surel / Website</td>
                <td><?php echo htmlspecialchars($email); ?> / <?php echo htmlspecialchars($website); ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Kepala Madrasah</td>
                <td><?php echo htmlspecialchars($kepala_nama); ?></td>
            </tr>
        </table>

        <h3>B. Keadaan Guru dan Tenaga Kependidikan</h3>
        <p class="no-indent">Saat ini, madrasah diperkuat oleh tenaga pendidik yang berdedikasi tinggi. Daftar pendidik aktif adalah sebagai berikut:</p>
        <table class="waka-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Nama Lengkap</th>
                    <th>NIP / Identitas</th>
                    <th>Jabatan Tugas</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($gurus as $g): ?>
                    <tr>
                        <td align="center"><?php echo $no++; ?></td>
                        <td><strong><?php echo htmlspecialchars($g['nama']); ?></strong></td>
                        <td><?php echo htmlspecialchars($g['nip'] ?: '-'); ?></td>
                        <td><?php echo htmlspecialchars($g['jabatan'] ?: 'Guru'); ?></td>
                        <td align="center"><?php echo htmlspecialchars($g['status_kepegawaian'] ?? 'Honorer'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Halaman 8: BAB II Profil dan Kondisi Madrasah (Lanjutan) -->
    <div class="page">
        <h3>C. Rombongan Belajar dan Keadaan Siswa</h3>
        <p class="no-indent">Distribusi rombongan belajar (rombel) dan sebaran kapasitas siswa pada Tahun Pelajaran <?php echo htmlspecialchars($tahun_ajaran); ?> adalah:</p>
        <table class="waka-table">
            <thead>
                <tr>
                    <th style="width: 10%;">No</th>
                    <th>Nama Rombongan Belajar (Rombel)</th>
                    <th>Tingkat Kelas</th>
                    <th style="text-align: center;">Jumlah Peserta Didik</th>
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

        <h3>D. Analisis Kondisi Akademik</h3>
        <p>Berdasarkan hasil evaluasi mutu akademik tahun lalu, kondisi pembelajaran di MI Miftahul Huda Mathla'ul Anwar menunjukkan tren peningkatan yang positif. Mayoritas siswa telah mencapai Kriteria Ketercapaian Tujuan Pembelajaran (KKTP) dengan baik. Namun, madrasah terus menghadapi tantangan adaptasi teknologi digital dalam proses asesmen mandiri.</p>
        <p>Dari sisi pendidik, kompetensi penyusunan Modul Ajar Kurikulum Merdeka masih memerlukan workshop internal secara berkala agar pemetaan tujuan pembelajaran (TP) dan alur tujuan pembelajaran (ATP) dapat diimplementasikan secara maksimal di dalam kelas. Hal ini mendasari program supervisi klinis waka kurikulum pada tahun ajaran ini.</p>
    </div>

    <!-- Halaman 9: BAB III Rencana Program Kerja Kurikulum -->
    <div class="page" style="padding: 2.5cm 2.5cm 3cm 3.5cm; font-size: 10pt;">
        <h1>BAB III<br>RENCANA PROGRAM KERJA KURIKULUM</h1>
        <br>
        <h3>A. Rencana Kegiatan Tahunan (Format Akreditasi & Pengawas)</h3>
        <table class="waka-table" style="font-size: 8.5pt;">
            <thead>
                <tr>
                    <th style="width: 15%;">Program / Tujuan</th>
                    <th style="width: 25%;">Kegiatan Operasional</th>
                    <th style="width: 12%;">Waktu</th>
                    <th style="width: 15%;">Penanggung Jawab</th>
                    <th style="width: 20%;">Indikator Keberhasilan</th>
                    <th style="width: 13%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Penyusunan Perangkat Kurikulum</strong></td>
                    <td>Rapat kerja pendidik, penyusunan kalender pendidikan, struktur KBM, jadwal ajar, dan administrasi program semester.</td>
                    <td>Juli 2026</td>
                    <td>Waka Kurikulum & Kepala Madrasah</td>
                    <td>Tersedianya jadwal pelajaran yang valid dan kalender madrasah terdistribusi.</td>
                    <td>Awal Tahun Pelajaran</td>
                </tr>
                <tr>
                    <td><strong>Peningkatan Mutu Administrasi Guru</strong></td>
                    <td>Verifikasi dan pengesahan dokumen ATP, Modul Ajar, Prota, Promes, dan KKTP.</td>
                    <td>Agustus 2026</td>
                    <td>Waka Kurikulum</td>
                    <td>100% guru memiliki perangkat ajar yang disahkan oleh Kepala Madrasah.</td>
                    <td>Administrasi Guru</td>
                </tr>
                <tr>
                    <td><strong>Supervisi Klinis & Akademik</strong></td>
                    <td>Observasi proses pembelajaran guru di kelas menggunakan instrumen supervisi formal.</td>
                    <td>September 2026</td>
                    <td>Kepala Madrasah & Waka Kurikulum</td>
                    <td>Peningkatan kualitas pengajaran berbasis student-centered di kelas.</td>
                    <td>Supervisi Kelas</td>
                </tr>
                <tr>
                    <td><strong>Evaluasi Tengah Semester Ganjil</strong></td>
                    <td>Penyelenggaraan Penilaian Tengah Semester (PTS) Ganjil dan rekapitulasi data nilai harian.</td>
                    <td>Oktober 2026</td>
                    <td>Panitia Ujian & Guru</td>
                    <td>Terlaksananya ujian dengan tertib dan nilai ter-input di sistem.</td>
                    <td>Asesmen Sumatif</td>
                </tr>
                <tr>
                    <td><strong>Asesmen Akhir Semester Ganjil</strong></td>
                    <td>Pelaksanaan Penilaian Akhir Semester (PAS) Ganjil dan pencetakan Rapor Digital Madrasah (RDM).</td>
                    <td>Desember 2026</td>
                    <td>Panitia PAS & Wali Kelas</td>
                    <td>Pembagian Rapor tepat waktu dan pelaporan hasil belajar siswa ke orang tua.</td>
                    <td>Akhir Semester</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Halaman 10: BAB III Rencana Program Kerja Kurikulum (Lanjutan) -->
    <div class="page" style="padding: 2.5cm 2.5cm 3cm 3.5cm; font-size: 10pt;">
        <table class="waka-table" style="font-size: 8.5pt;">
            <thead>
                <tr>
                    <th style="width: 15%;">Program / Tujuan</th>
                    <th style="width: 25%;">Kegiatan Operasional</th>
                    <th style="width: 12%;">Waktu</th>
                    <th style="width: 15%;">Penanggung Jawab</th>
                    <th style="width: 20%;">Indikator Keberhasilan</th>
                    <th style="width: 13%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Evaluasi Akademik Semester I</strong></td>
                    <td>Rapat koordinasi guru mengevaluasi ketuntasan materi ajar semester ganjil dan persiapan KBM semester genap.</td>
                    <td>Januari 2027</td>
                    <td>Waka Kurikulum & Guru</td>
                    <td>Tersedianya rencana perbaikan (remedial/pengayaan) pengajaran.</td>
                    <td>Awal Semester II</td>
                </tr>
                <tr>
                    <td><strong>Asesmen Akhir Semester Genap</strong></td>
                    <td>Pelaksanaan Penilaian Akhir Tahun (PAT) serta penentuan Kenaikan Kelas peserta didik.</td>
                    <td>Mei - Juni 2027</td>
                    <td>Panitia PAT & Wali Kelas</td>
                    <td>Terlaksananya PAT tertib dan rapat pleno kenaikan kelas terlaksana valid.</td>
                    <td>Kenaikan Kelas</td>
                </tr>
                <tr>
                    <td><strong>Laporan Pertanggungjawaban</strong></td>
                    <td>Penyusunan dokumen laporan rekapitulasi tahunan kurikulum madrasah ke Kemenag Kabupaten.</td>
                    <td>Juni 2027</td>
                    <td>Waka Kurikulum</td>
                    <td>Dokumen fisik laporan tahunan diserahkan dan disahkan oleh pengawas.</td>
                    <td>Akhir Tahun Pelajaran</td>
                </tr>
            </tbody>
        </table>

        <h3>B. Program Kerja Semesteran</h3>
        <p class="no-indent"><strong>Semester Ganjil (Juli - Desember):</strong></p>
        <p>Kegiatan berfokus pada penataan dokumen dasar kurikulum, pembagian rombongan belajar baru, orientasi wali kelas, penguatan media KBM interaktif, supervisi administrasi akademik guru tahap I, dan asesmen akhir semester ganjil.</p>
        <p class="no-indent"><strong>Semester Genap (Januari - Juni):</strong></p>
        <p>Kegiatan berfokus pada tindak lanjut supervisi akademik, monitoring KBM pasca libur semester, asesmen tengah semester genap, persiapan ujian kelulusan kelas akhir, Penilaian Akhir Tahun (PAT), dan penentuan kenaikan kelas.</p>
    </div>

    <!-- Halaman 11: BAB III Rencana Program Kerja Kurikulum (Lanjutan 2) -->
    <div class="page">
        <h3>C. Rencana Program Bulanan (Tahun Pelajaran <?php echo htmlspecialchars($tahun_ajaran); ?>)</h3>
        <table class="waka-table">
            <thead>
                <tr>
                    <th style="width: 20%;">Bulan</th>
                    <th>Uraian Rencana Kegiatan Bidang Kurikulum</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center"><strong>Juli 2026</strong></td>
                    <td>Penyusunan pembagian tugas mengajar guru, kalender akademik madrasah, jadwal mata pelajaran mingguan, dan pengadaan buku ajar utama.</td>
                </tr>
                <tr>
                    <td align="center"><strong>Agustus 2026</strong></td>
                    <td>Verifikasi administrasi perencanaan pembelajaran (Prota, Promes, ATP, Modul Ajar), rapat komite kurikulum madrasah.</td>
                </tr>
                <tr>
                    <td align="center"><strong>September 2026</strong></td>
                    <td>Pelaksanaan program supervisi akademik kelas tahap I dan pengumpulan laporan berkala KBM bulanan.</td>
                </tr>
                <tr>
                    <td align="center"><strong>Oktober 2026</strong></td>
                    <td>Penyelenggaraan Penilaian Tengah Semester (PTS) Ganjil, pembagian hasil belajar mid-semester ganjil.</td>
                </tr>
                <tr>
                    <td align="center"><strong>November 2026</strong></td>
                    <td>Pengayaan materi ajar pasca PTS, pendampingan guru dalam pengisian Rapor Digital Madrasah (RDM).</td>
                </tr>
                <tr>
                    <td align="center"><strong>Desember 2026</strong></td>
                    <td>Penilaian Akhir Semester (PAS) Ganjil, pengolahan nilai raport, pembagian rapor semester ganjil, pelaporan EMIS semester ganjil.</td>
                </tr>
                <tr>
                    <td align="center"><strong>Januari 2027</strong></td>
                    <td>Evaluasi hasil KBM semester ganjil, pembagian jadwal mengajar semester genap, verifikasi dokumen modul ajar semester II.</td>
                </tr>
                <tr>
                    <td align="center"><strong>Februari 2027</strong></td>
                    <td>Pelaksanaan supervisi pengajaran guru kelas tahap II dan monitoring kemajuan belajar siswa.</td>
                </tr>
                <tr>
                    <td align="center"><strong>Maret 2027</strong></td>
                    <td>Pelaksanaan PTS Semester Genap, pembagian raport mid-semester genap, pendampingan ujian siswa kelas akhir.</td>
                </tr>
                <tr>
                    <td align="center"><strong>April 2027</strong></td>
                    <td>Try Out persiapan asesmen madrasah kelas VI, pengayaan khusus materi ujian tingkat akhir.</td>
                </tr>
                <tr>
                    <td align="center"><strong>Mei 2027</strong></td>
                    <td>Penyelenggaraan Asesmen Madrasah kelas VI, persiapan Penilaian Akhir Tahun (PAT) kelas I - V.</td>
                </tr>
                <tr>
                    <td align="center"><strong>Juni 2027</strong></td>
                    <td>Penilaian Akhir Tahun (PAT) kelas I - V, rapat pleno kenaikan kelas, pengisian RDM, pembagian Rapor kenaikan kelas, penyusunan LPJ tahunan.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Halaman 12: BAB IV Pengelolaan Pembelajaran -->
    <div class="page">
        <h1>BAB IV<br>PENGELOLAAN PEMBELAJARAN</h1>
        <br>
        <h3>A. Penyusunan Jadwal Pelajaran</h3>
        <p>Jadwal pelajaran di MI Miftahul Huda Mathla'ul Anwar disusun dengan prinsip keseimbangan beban belajar per hari dan keadilan alokasi waktu mata pelajaran umum dan keagamaan. Proses perancangan jadwal dilakukan secara digital dan mengacu pada beban jam wajib di struktur kurikulum madrasah. Jadwal pelajaran dibagikan kepada pendidik, peserta didik, serta ditempel di setiap ruang kelas sebelum tahun ajaran baru dimulai.</p>
        
        <h3>B. Pembagian Tugas Mengajar</h3>
        <p>Pembagian tugas mengajar guru ditetapkan melalui Keputusan Kepala Madrasah (SK Pembagian Tugas) pada setiap awal semester. Dasar pertimbangan pembagian tugas ini disesuaikan dengan kualifikasi akademis, sertifikat pendidik (bila ada), linieritas mata pelajaran, serta kompetensi khusus pendidik yang bersangkutan. Pendidik wajib melatih diri agar cakap mengampu rombel yang telah dipercayakan kepadanya.</p>

        <h3>C. Pengelolaan Perangkat Pembelajaran</h3>
        <p class="no-indent">Untuk menjamin efisiensi KBM di kelas, seluruh guru MI Miftahul Huda Mathla'ul Anwar wajib memiliki dan menyusun perangkat pembelajaran lengkap di awal tahun ajaran. Dokumen administrasi guru yang wajib disiapkan adalah:</p>
        <ol>
            <li><strong>Alur Tujuan Pembelajaran (ATP):</strong> Alur pemetaan kompetensi belajar siswa yang runut.</li>
            <li><strong>Modul Ajar / Rencana Pelaksanaan Pembelajaran (RPP):</strong> Rencana praktis pelaksanaan KBM per topik bahasan.</li>
            <li><strong>Program Semester (Promes):</strong> Rencana distribusi materi pelajaran per minggu.</li>
            <li><strong>Kriteria Ketercapaian Tujuan Pembelajaran (KKTP):</strong> Kriteria batas nilai kelulusan siswa per mapel.</li>
            <li><strong>Instrumen Penilaian:</strong> Bank soal, rubrik evaluasi sikap, dan petunjuk rubrik penilaian praktikum.</li>
        </ol>

        <h3>D. Monitoring Pelaksanaan Pembelajaran</h3>
        <p>Monitoring KBM dilakukan secara berkala oleh Waka Kurikulum bersama Kepala Madrasah. Bentuk monitoring mencakup pencatatan kehadiran pendidik di kelas melalui jurnal mengajar guru harian, penyerahan berkas berkala administrasi ajar, serta supervisi kelas secara berkala.</p>
    </div>

    <!-- Halaman 13: BAB V Program Penilaian dan Asesmen -->
    <div class="page">
        <h1>BAB V<br>SISTEM PENILAIAN DAN ASESMEN</h1>
        <br>
        <h3>A. Asesmen Diagnostik</h3>
        <p>Asesmen diagnostik dilaksanakan di awal tahun ajaran atau sebelum guru memulai bab pembahasan baru. Asesmen ini bertujuan mendiagnosis kesiapan belajar, gaya belajar, minat, serta tingkat pemahaman awal siswa. Hasil asesmen ini digunakan guru kelas untuk melakukan diferensiasi proses pembelajaran demi keadilan hak belajar siswa.</p>

        <h3>B. Asesmen Formatif</h3>
        <p>Asesmen formatif dilaksanakan terintegrasi selama proses pembelajaran berlangsung di setiap akhir sub-bab. Bentuk asesmen formatif dapat berupa penugasan harian, kuis singkat, observasi keaktifan diskusi kelompok, dan penugasan proyek sederhana. Fokus formatif adalah perbaikan proses KBM secara real-time, bukan hanya penentuan nilai raport.</p>

        <h3>C. Asesmen Sumatif</h3>
        <p>Asesmen sumatif dilakukan di akhir unit materi ajar (penilaian harian sumatif), tengah semester (PTS), akhir semester (PAS), dan akhir tahun ajaran (PAT). Hasil asesmen sumatif diakumulasikan sebagai nilai murni pengetahuan dan keterampilan siswa di buku Rapor Digital Madrasah (RDM).</p>

        <h3>D. Penilaian Tengah Semester (PTS)</h3>
        <p>PTS dilaksanakan serentak pada pertengahan semester ganjil dan semester genap. Ujian ini mencakup materi ajar yang telah diselesaikan selama 8-9 minggu KBM. Nilai PTS dilaporkan dalam bentuk buku laporan hasil belajar tengah semester kepada orang tua.</p>

        <h3>E. Penilaian Akhir Semester (PAS)</h3>
        <p>PAS diselenggarakan di akhir semester ganjil. Ujian ini menguji pemahaman menyeluruh siswa terhadap seluruh kompetensi dasar atau tujuan pembelajaran yang diajarkan pada semester pertama.</p>

        <h3>F. Penilaian Akhir Tahun (PAT)</h3>
        <p>PAT dilaksanakan di penghujung semester genap. Ujian PAT menjadi salah satu komponen krusial dalam kriteria penentuan kenaikan kelas siswa dari tingkat I - V.</p>

        <h3>G. Analisis Hasil Belajar</h3>
        <p>Setiap guru wajib melakukan analisis hasil belajar pasca ujian sumatif. Analisis ini bermanfaat untuk memetakan daya serap siswa, mendeteksi materi yang sulit dipahami secara massal, serta merancang program remedial bagi siswa di bawah KKTP dan program pengayaan bagi siswa berprestasi.</p>
    </div>

    <!-- Halaman 14: BAB VI Supervisi Akademik -->
    <div class="page">
        <h1>BAB VI<br>SUPERVISI AKADEMIK GURU</h1>
        <br>
        <h3>A. Tujuan Supervisi</h3>
        <p>Supervisi akademik merupakan program pembinaan berkelanjutan yang dilakukan oleh Kepala Madrasah didampingi Waka Kurikulum. Tujuan utama supervisi bukanlah untuk menilai kinerja guru secara administratif belaka, melainkan memberikan bimbingan klinis demi peningkatan kualitas mengajar guru, memperbaiki kelemahan pengajaran di kelas, serta mendorong inovasi metode belajar guru madrasah.</p>

        <h3>B. Rencana Jadwal Supervisi Guru</h3>
        <p class="no-indent">Program supervisi guru di MI Miftahul Huda Mathla'ul Anwar dilaksanakan sebanyak dua kali (tahap I pada semester ganjil dan tahap II pada semester genap) dengan pembagian jadwal umum sebagai berikut:</p>
        <table class="waka-table">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th>Sasaran Guru Kelas</th>
                    <th>Tahap I (Ganjil)</th>
                    <th>Tahap II (Genap)</th>
                    <th>Fokus Observasi Kelas</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center">1</td>
                    <td>Guru Kelas Tingkat Bawah (I - III)</td>
                    <td>September 2026</td>
                    <td>Februari 2027</td>
                    <td>Metode calistung interaktif dan manajemen kelas ramah anak.</td>
                </tr>
                <tr>
                    <td align="center">2</td>
                    <td>Guru Kelas Tingkat Atas (IV - VI)</td>
                    <td>Oktober 2026</td>
                    <td>Maret 2027</td>
                    <td>Pembelajaran HOTS dan pemanfaatan media ajar berbasis IT.</td>
                </tr>
                <tr>
                    <td align="center">3</td>
                    <td>Guru Mata Pelajaran Keagamaan (Pendidikan Agama Islam)</td>
                    <td>September 2026</td>
                    <td>Februari 2027</td>
                    <td>Kefasihan membaca dalil hadits/Al-Qur'an dan praktik ibadah.</td>
                </tr>
            </tbody>
        </table>

        <h3>C. Instrumen Supervisi</h3>
        <p>Supervisi akademik mengacu pada instrumen formal baku yang memuat aspek: (1) kelengkapan administrasi ajar (Modul Ajar/RPP, KKTP, Kriteria Penilaian), (2) observasi langsung guru saat mengajar di kelas (kegiatan pendahuluan, inti, dan penutup), serta (3) pengelolaan kelas dan respons keaktifan belajar siswa.</p>

        <h3>D. Tindak Lanjut Supervisi</h3>
        <p>Hasil supervisi didokumentasikan dan ditindaklanjuti melalui diskusi pasca observasi secara persuasif. Pendidik yang memerlukan pembinaan khusus akan dilibatkan dalam pelatihan kompetensi internal (workshop madrasah) atau program KKG kecamatan.</p>
    </div>

    <!-- Halaman 15: BAB VII Pengembangan Kompetensi Guru -->
    <div class="page">
        <h1>BAB VII<br>PENGEMBANGAN KOMPETENSI GURU</h1>
        <br>
        <h3>A. Workshop Internal Madrasah</h3>
        <p>Madrasah menyelenggarakan workshop internal di awal tahun ajaran yang memfokuskan peningkatan kemampuan guru dalam menyusun perangkat pembelajaran berbasis kurikulum nasional terbaru. Workshop ini difasilitasi oleh pengawas madrasah dan guru senior yang telah lulus diklat pembina kurikulum.</p>

        <h3>B. Forum KKG (Kelompok Kerja Guru)</h3>
        <p>Setiap guru di MI Miftahul Huda Mathla'ul Anwas diwajibkan bergabung aktif dalam kegiatan KKG/MGMP tingkat kecamatan. Forum KKG menjadi sarana efektif bagi guru kelas untuk berdiskusi, bertukar modul ajar inovatif, menyusun bank soal bersama, dan berkolaborasi memecahkan hambatan ajar kelas.</p>

        <h3>C. Pelatihan Kurikulum</h3>
        <p>Madrasah mengirim perwakilan guru secara berkala untuk mengikuti pelatihan formal Kurikulum Merdeka yang diselenggarakan oleh Kementerian Agama maupun Balai Diklat Keagamaan setempat. Perwakilan guru yang dikirim wajib melakukan diseminasi (pengimbasan ilmu) kepada rekan guru di madrasah.</p>

        <h3>D. Pengembangan Media Pembelajaran</h3>
        <p>Pendidik didorong aktif merancang media pembelajaran berbasis multimedia (seperti video YouTube pembelajaran, bahan tayang presentasi interaktif, dan kuis digital berbasis game). Pemanfaatan media pembelajaran interaktif terbukti meningkatkan minat fokus belajar siswa MI.</p>
    </div>

    <!-- Halaman 16: BAB VIII Monitoring dan Evaluasi -->
    <div class="page">
        <h1>BAB VIII<br>MONITORING DAN EVALUASI</h1>
        <br>
        <h3>A. Monitoring Administrasi Guru</h3>
        <p>Waka Kurikulum melakukan verifikasi administrasi guru secara berkala setiap akhir triwulan. Penilaian administrasi meliputi ketertiban guru memperbarui jurnal harian, kesesuaian materi ajar dengan Rencana Program Bulanan, serta kelengkapan dokumen instrumen asesmen ajar.</p>

        <h3>B. Monitoring Kehadiran Guru</h3>
        <p>Disiplin kehadiran guru di dalam kelas dipantau secara ketat melalui absensi sidik jari (fingerprint) dan rekapitulasi jurnal harian kelas yang diisi oleh ketua kelas. Guru yang berhalangan hadir wajib melampirkan surat izin serta menitipkan tugas belajar mandiri yang terstruktur bagi siswa agar kelas tidak kosong.</p>

        <h3>C. Monitoring Proses Pembelajaran</h3>
        <p>Proses monitoring pembelajaran di dalam kelas ditujukan untuk menjamin kesesuaian materi ajar yang diajarkan oleh guru dengan alur tujuan pembelajaran (ATP) yang telah disepakati. Hal ini meminimalkan terjadinya mismatch kurikulum.</p>

        <h3>D. Evaluasi Program Kurikulum</h3>
        <p>Rapat evaluasi kurikulum madrasah secara makro dilaksanakan di akhir semester ganjil dan genap. Rapat pleno evaluasi ini membedah ketercapaian tujuan kurikulum, daya serap siswa, penanganan remedial siswa, hambatan KBM IT, serta persiapan perbaikan kurikulum di tahun ajaran berikutnya.</p>
    </div>

    <!-- Halaman 17: BAB IX Penutup -->
    <div class="page">
        <h1>BAB IX<br>PENUTUP</h1>
        <br><br><br>
        <p>Program Kerja Wakil Kepala Madrasah Bidang Kurikulum MI Miftahul Huda Mathla'ul Anwar untuk Tahun Pelajaran <?php echo htmlspecialchars($tahun_ajaran); ?> ini disusun sebagai pedoman sistematis bagi seluruh pemangku kepentingan akademis di lingkungan madrasah. Melalui perencanaan kurikulum yang matang, pelaksanaan yang konsisten, supervisi yang konstruktif, serta monitoring berkala, diharapkan mutu pendidikan di madrasah dapat terus ditingkatkan secara berkelanjutan.</p>
        <p>Keberhasilan pelaksanaan seluruh program akademik ini menuntut dedikasi tinggi dari seluruh pendidik, tenaga kependidikan, serta dukungan penuh dari jajaran komite madrasah. Semoga Allah SWT senantiasa meridhoi kerja keras kita dalam mendidik generasi penerus bangsa yang bertakwa dan berakhlak mulia di MI Miftahul Huda Mathla'ul Anwar.</p>
        
        <br><br><br>
        <table class="sign-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    Kepala Madrasah<br><br><br><br><br>
                    <strong><?php echo htmlspecialchars($kepala_nama); ?></strong><br>
                    NIP. <?php echo htmlspecialchars($kepala_nip ?: '-'); ?>
                </td>
                <td>
                    Way Tenong, <?php echo date('d F Y'); ?><br>
                    Waka Bidang Kurikulum<br><br><br><br><br>
                    <strong><?php echo htmlspecialchars($waka_nama); ?></strong><br>
                    NIP. <?php echo htmlspecialchars($waka_nip ?: '-'); ?>
                </td>
            </tr>
        </table>
    </div>

    <!-- Halaman 18: Lampiran Divider -->
    <div class="page" style="display: flex; align-items: center; justify-content: center;">
        <div style="text-align: center; border: 4px double #000; padding: 50px 100px;">
            <h1 style="font-size: 28pt; margin: 0;">LAMPIRAN-LAMPIRAN</h1>
            <p class="no-indent" style="text-align: center; margin-top: 15px; font-size: 14pt; font-style: italic;">Program Kerja Wakil Kepala Madrasah Bidang Kurikulum<br>Tahun Pelajaran <?php echo htmlspecialchars($tahun_ajaran); ?></p>
        </div>
    </div>

    <!-- Halaman 19: Lampiran - Format Monitoring Administrasi Guru -->
    <div class="page">
        <h2>LAMPIRAN I: FORMAT MONITORING ADMINISTRASI GURU</h2>
        <br>
        <table class="waka-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No</th>
                    <th style="width: 35%;">Aspek Administrasi Guru yang Diverifikasi</th>
                    <th style="width: 15%; text-align: center;">Ketersediaan (Ada/Tidak)</th>
                    <th style="width: 15%; text-align: center;">Kesesuaian (Sesuai/Tidak)</th>
                    <th>Catatan & Rekomendasi Waka Kurikulum</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center">1</td>
                    <td>Alur Tujuan Pembelajaran (ATP) / Silabus Mapel</td>
                    <td align="center">Ada</td>
                    <td align="center">Sesuai</td>
                    <td>Pemetaan tujuan pembelajaran runut.</td>
                </tr>
                <tr>
                    <td align="center">2</td>
                    <td>Modul Ajar Kurikulum Merdeka / RPP</td>
                    <td align="center">Ada</td>
                    <td align="center">Sesuai</td>
                    <td>Langkah KBM interaktif perlu diperjelas.</td>
                </tr>
                <tr>
                    <td align="center">3</td>
                    <td>Program Tahunan (Prota) & Program Semester (Promes)</td>
                    <td align="center">Ada</td>
                    <td align="center">Sesuai</td>
                    <td>Alokasi minggu efektif telah disinkronkan.</td>
                </tr>
                <tr>
                    <td align="center">4</td>
                    <td>Kriteria Ketercapaian Tujuan Pembelajaran (KKTP) / KKM</td>
                    <td align="center">Ada</td>
                    <td align="center">Sesuai</td>
                    <td>Batas nilai tuntas mapel sudah dipublikasikan.</td>
                </tr>
                <tr>
                    <td align="center">5</td>
                    <td>Buku Nilai (Rekap Nilai Harian & Sumatif)</td>
                    <td align="center">Ada</td>
                    <td align="center">Sesuai</td>
                    <td>Rutin melakukan analisis hasil penilaian.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Halaman 20: Lampiran - Rencana Kalender & Jadwal Supervisi -->
    <div class="page">
        <h2>LAMPIRAN II: RINCIAN JADWAL SUPERVISI AKADEMIK GURU</h2>
        <br>
        <table class="waka-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No</th>
                    <th>Nama Guru Kelas / Mapel</th>
                    <th>Jabatan Guru</th>
                    <th style="text-align: center;">Jadwal Supervisi</th>
                    <th>Supervisor</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($gurus as $g): ?>
                    <tr>
                        <td align="center"><?php echo $no++; ?></td>
                        <td><strong><?php echo htmlspecialchars($g['nama']); ?></strong></td>
                        <td><?php echo htmlspecialchars($g['jabatan'] ?: 'Guru'); ?></td>
                        <td align="center"><?php echo $no % 2 == 0 ? 'September 2026' : 'Oktober 2026'; ?></td>
                        <td><?php echo htmlspecialchars($kepala_nama); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>

