<?php
$sekolah_nama = $inst['nama'] ?? 'MI MIFTAHUL HUDA MATHLA\'UL ANWAR';
$kepala_nama = $inst['nama_kepala'] ?? 'SAMSUL ARIFIN, M.Pd';
$pembina_nama = $guru['nama'] ?? 'Pembina Ekstrakurikuler';
$pembina_nip = $guru_detail['nip'] ?? '-';
$gender = $guru_detail['jenis_kelamin'] ?? 'L';
$tahun_ajaran = $active_year_name ?? '2026/2027';

$pembina_putra = ($gender == 'L') ? $pembina_nama : 'AHMAD RIFA\'I, M.Pd';
$pembina_putri = ($gender == 'P') ? $pembina_nama : 'ZULI ASTUTIK, S.Pd.I';

$hari_latihan = $program['jadwal_hari'] ?? 'Sabtu';
$waktu_latihan = $program['jadwal_waktu'] ?? '14.00 - 16.00 WIB';

$kecamatan = $inst['kecamatan'] ?? 'Way Tenong';
$kabupaten = $inst['kota'] ?? 'Lampung Barat';
$provinsi = $inst['provinsi'] ?? 'Lampung';

// Detect extracurricular type
$nama_ekstra = isset($nama_ekstra) ? $nama_ekstra : ($_GET['ekstra'] ?? 'Pramuka');
$is_voli = (stripos($nama_ekstra, 'voli') !== false || stripos($nama_ekstra, 'volly') !== false);
$is_pramuka = (stripos($nama_ekstra, 'pramuka') !== false || stripos($nama_ekstra, 'praja') !== false);
$is_generic = (!$is_voli && !$is_pramuka);

$sasaran_anggota = $total_anggota > 0 ? $total_anggota . " Anggota Aktif" : ($is_pramuka ? "Peserta Didik Kelas I - VI" : "Peserta Didik Kelas IV - VI");
$target_pencapaian_kustom = $program['target_pencapaian'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Program Kerja <?php echo htmlspecialchars($nama_ekstra ?: 'Pramuka'); ?> - <?php echo htmlspecialchars($sekolah_nama); ?></title>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <style>
        /* Base Page Setup for Skripsi Standar */
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
            background: #0f172a;
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
            background: #0284c7;
            color: white;
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
            background: #0369a1;
        }

        /* Skripsi Margin: 4cm Left, 3cm Top, 3cm Right, 3.5cm Bottom */
        .page {
            width: 210mm;
            height: 297mm;
            padding: 3cm 3cm 3.5cm 4cm;
            margin: 20px auto;
            background: white;
            box-sizing: border-box;
            box-shadow: 0 0 15px rgba(0,0,0,0.15);
            position: relative;
            page-break-after: always;
            overflow: hidden;
        }
        
        /* Modern Minimalist Cover Page Styling (Plus Jakarta Sans) */
        .cover-modern {
            height: 100%;
            width: 100%;
            font-family: 'Plus Jakarta Sans', sans-serif;
            position: relative;
            box-sizing: border-box;
        }
        
        .cover-curves {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }
        
        .cover-frame {
            border: none;
            padding: 20px 0;
            height: 100%;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            z-index: 2;
            background: transparent;
        }
        
        .cover-header-badge {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 5px;
        }
        
        .cover-header-badge span {
            background: #f0fdf4;
            color: #16a34a;
            font-size: 8pt;
            font-weight: 800;
            letter-spacing: 3px;
            padding: 8px 20px;
            border-radius: 6px;
            border: 1px solid #d1fae5;
            text-transform: uppercase;
        }
        
        .cover-title-container {
            text-align: center;
            margin: 20px 0;
        }
        
        .cover-title-primary {
            font-size: 26pt;
            font-weight: 800;
            color: #14532d; /* Deep emerald green */
            letter-spacing: 4px;
            margin: 0;
            line-height: 1.2;
        }
        
        .cover-title-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin: 18px 0;
        }
        
        .divider-line {
            width: 80px;
            height: 2px;
            background: linear-gradient(to right, transparent, #15803d);
        }
        
        .cover-title-divider .divider-line:last-child {
            background: linear-gradient(to left, transparent, #ca8a04);
        }
        
        .divider-dot {
            width: 8px;
            height: 8px;
            background: #15803d;
            border-radius: 50%;
        }
        
        .cover-title-secondary {
            font-size: 16pt;
            font-weight: 700;
            color: #ca8a04; /* Rich gold */
            letter-spacing: 2px;
            margin: 0 0 8px 0;
            text-transform: uppercase;
        }
        
        .cover-title-desc {
            margin: 0 auto;
            text-align: center !important;
            text-indent: 0 !important;
            line-height: 1.4;
            max-width: 95%;
        }
        
        .cover-desc-prefix {
            font-size: 9pt;
            font-weight: 600;
            color: #64748b;
            letter-spacing: 2px;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        
        .cover-desc-school {
            font-size: 12pt;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        
        .cover-logo-container {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        
        .cover-logo-ring {
            position: relative;
            width: 140px;
            height: 140px;
            border: 2px solid #d1fae5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            padding: 12px;
            box-shadow: 0 10px 25px rgba(4, 120, 87, 0.05);
        }
        
        .cover-logo-ring::before {
            content: '';
            position: absolute;
            top: -6px; left: -6px; right: -6px; bottom: -6px;
            border: 1px dashed #0284c7;
            border-radius: 50%;
            opacity: 0.6;
        }
        
        .cover-logo-ring img {
            max-height: 100px;
            max-width: 100px;
            object-fit: contain;
        }
        
        .cover-logo-placeholder {
            width: 90px;
            height: 90px;
            border: 2px dashed #cbd5e1;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
        }
        
        .cover-meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            background: #f0fdf4; /* Light sage green background */
            border: 1px solid #d1fae5;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            width: 100%;
            box-sizing: border-box;
            text-align: left;
        }
        
        .meta-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .meta-item:nth-child(odd) {
            border-right: 1px solid #d1fae5;
            padding-right: 15px;
        }
        
        .meta-item:nth-child(even) {
            padding-left: 15px;
        }
        
        .meta-label {
            font-size: 8pt;
            font-weight: 700;
            color: #0284c7;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
        
        .meta-value {
            font-size: 10.5pt;
            font-weight: 700;
            color: #14532d;
            text-transform: uppercase;
        }
        
        .cover-footer-modern {
            text-align: center;
            width: 100%;
            text-indent: 0 !important;
        }
        
        .footer-school {
            font-size: 11pt;
            font-weight: 800;
            color: #14532d;
            margin: 0 0 5px 0;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            text-indent: 0 !important;
        }
        
        .footer-region {
            font-size: 8pt;
            font-weight: 600;
            color: #475569;
            margin: 0 0 10px 0;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            text-indent: 0 !important;
            line-height: 1.4;
        }
        
        .footer-badge {
            display: inline-block;
            background: #15803d;
            color: #ffffff;
            font-size: 8pt;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 5px 16px;
            border-radius: 20px;
            border: 1px solid #14532d;
            text-transform: uppercase;
            box-shadow: 0 2px 4px rgba(21, 128, 61, 0.15);
        }

        /* Document Typography Skripsi */
        h1.chapter-title {
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            margin-top: 0;
            margin-bottom: 2rem;
            line-height: 1.4;
        }
        
        h2.section-title {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }
        
        p {
            text-align: justify;
            text-indent: 1.25cm;
            margin: 0 0 12px 0;
            font-size: 12pt;
        }
        
        p.no-indent {
            text-indent: 0;
        }

        ol, ul {
            margin: 0 0 12px 0;
            padding-left: 1.25cm;
            text-align: justify;
        }
        
        li {
            margin-bottom: 6px;
        }

        table.formal-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
            font-size: 11pt;
            line-height: 1.25;
        }
        
        table.formal-table th, table.formal-table td {
            border: 1px solid black;
            padding: 4px 8px;
            text-align: left;
            vertical-align: top;
        }
        
        table.formal-table th {
            font-weight: bold;
            text-align: center;
            background: #f8fafc;
        }
        
        /* Signature Block */
        .sig-block {
            margin-top: 3cm;
            width: 100%;
            display: flex;
            justify-content: space-between;
        }
        
        .sig-column {
            width: 45%;
            text-align: center;
        }
        
        .sig-space {
            height: 2cm;
        }

        /* Page Numbering - Skripsi Style */
        .num-bottom-center {
            position: absolute;
            bottom: 1.5cm;
            left: 0;
            right: 0;
            text-align: center;
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            font-weight: normal;
        }

        .num-top-right {
            position: absolute;
            top: 1.5cm;
            right: 3cm;
            text-align: right;
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            font-weight: normal;
        }
        
        /* Print media styles */
        @media print {
            body {
                background: white;
            }
            .no-print-bar {
                display: none !important;
            }
            .page {
                margin: 0;
                box-shadow: none;
                width: 210mm;
                height: 297mm;
                padding: 3cm 3cm 3.5cm 4cm !important;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Print Action Bar -->
    <div class="no-print-bar">
        <div style="display:flex; align-items:center; gap:10px;">
            <i data-lucide="printer" style="color:#0284c7;"></i>
            <span style="font-weight:700; font-size:0.95rem;">Program Kerja Pembina <?php echo htmlspecialchars($nama_ekstra ?: 'Pramuka'); ?> - Standard Skripsi (A4)</span>
        </div>
        <button onclick="window.print()" class="btn-print">
            <i data-lucide="printer" style="width:16px;height:16px;"></i> Cetak Dokumen (A4)
        </button>
    </div>

    <!-- PAGE 1: COVER (MODERN INTERACTIVE STYLE) -->
    <div class="page" style="position: relative; overflow: hidden; padding: 3cm 3cm 3cm 4cm;">
        <!-- SVG curves in background -->
        <div class="cover-curves">
            <svg width="100%" height="100%" viewBox="0 0 800 1100" fill="none" xmlns="http://www.w3.org/2000/svg" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1;">
                <!-- Top Right Curved Shapes -->
                <path d="M550 0C620 120 700 180 800 200V0H550Z" fill="url(#gradTop1)" opacity="0.85"/>
                <path d="M480 0C580 160 680 240 800 270V0H480Z" fill="url(#gradTop2)" opacity="0.3"/>
                
                <!-- Elegant thin line curves (orbit paths) -->
                <path d="M300 -50C450 150 650 300 900 350" stroke="#0ea5e9" stroke-width="2" stroke-dasharray="8 4" opacity="0.4"/>
                <path d="M250 -50C420 180 620 350 900 420" stroke="#ca8a04" stroke-width="1.5" opacity="0.3"/>
                <path d="M200 -50C390 210 590 400 900 490" stroke="#075985" stroke-width="1" opacity="0.2"/>

                <!-- Bottom Left Curved Shapes -->
                <path d="M0 900C100 920 180 980 250 1100H0V900Z" fill="url(#gradBot1)" opacity="0.85"/>
                <path d="M0 800C120 830 220 920 320 1100H0V800Z" fill="url(#gradBot2)" opacity="0.3"/>
                
                <path d="M-100 700C150 750 300 900 450 1150" stroke="#0ea5e9" stroke-width="2" stroke-dasharray="8 4" opacity="0.4"/>
                <path d="M-100 630C180 700 350 880 520 1150" stroke="#ca8a04" stroke-width="1.5" opacity="0.3"/>
                <path d="M-100 560C210 650 400 850 600 1150" stroke="#075985" stroke-width="1" opacity="0.2"/>

                <defs>
                    <linearGradient id="gradTop1" x1="550" y1="0" x2="800" y2="200" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#14532d"/>
                        <stop offset="1" stop-color="#075985"/>
                    </linearGradient>
                    <linearGradient id="gradTop2" x1="480" y1="0" x2="800" y2="270" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#34d399" stop-opacity="0.5"/>
                        <stop offset="1" stop-color="#0284c7" stop-opacity="0.1"/>
                    </linearGradient>
                    <linearGradient id="gradBot1" x1="0" y1="900" x2="250" y2="1100" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#075985"/>
                        <stop offset="1" stop-color="#15803d"/>
                    </linearGradient>
                    <linearGradient id="gradBot2" x1="0" y1="800" x2="320" y2="1100" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#ca8a04" stop-opacity="0.4"/>
                        <stop offset="1" stop-color="#eab308" stop-opacity="0.1"/>
                    </linearGradient>
                </defs>
            </svg>
        </div>

        <div class="cover-modern">
            <div class="cover-frame">
                
                <div class="cover-header-badge">
                    <span>Dokumen Administrasi Madrasah</span>
                </div>
                
                <div class="cover-title-container">
                    <h1 class="cover-title-primary">PROGRAM KERJA</h1>
                    <div class="cover-title-divider">
                        <span class="divider-line"></span>
                        <span class="divider-dot"></span>
                        <span class="divider-line"></span>
                    </div>
                    <h2 class="cover-title-secondary">PEMBINA <?php echo htmlspecialchars(strtoupper($nama_ekstra ?: 'Pramuka')); ?></h2>
                    <div class="cover-title-desc">
                        <div class="cover-desc-prefix"><?php echo $is_pramuka ? 'Gugus Depan Pangkalan' : 'Pangkalan Madrasah'; ?></div>
                        <div class="cover-desc-school"><?php echo htmlspecialchars($sekolah_nama); ?></div>
                    </div>
                </div>
                
                <div class="cover-logo-container">
                    <div class="cover-logo-ring">
                        <?php if (!empty($inst['logo'])): ?>
                            <img src="<?php echo base_url('uploads/logo/<?php echo $inst['logo']; ?>'); ?>" alt="Logo Sekolah">
                        <?php else: ?>
                            <div class="cover-logo-placeholder">
                                <i data-lucide="shield-check" style="width:40px;height:40px;color:#0284c7;"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="cover-meta-grid">
                    <div class="meta-item">
                        <span class="meta-label">PROGRAM</span>
                        <span class="meta-value">EKSTRAKURIKULER <?php echo htmlspecialchars($nama_ekstra ?: 'Pramuka'); ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">TAHUN AJARAN</span>
                        <span class="meta-value"><?php echo htmlspecialchars($tahun_ajaran); ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">TIM PENYUSUN</span>
                        <span class="meta-value"><?php echo htmlspecialchars($pembina_nama); ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">SATUAN KERJA</span>
                        <span class="meta-value"><?php echo $is_pramuka ? 'MABIGUS PANGKALAN' : 'TIM PEMBINA MADRASAH'; ?></span>
                    </div>
                </div>
                
                <div class="cover-footer-modern">
                    <h3 class="footer-school"><?php echo htmlspecialchars($sekolah_nama); ?></h3>
                    <div class="footer-region">KECAMATAN <?php echo htmlspecialchars(strtoupper($kecamatan)); ?> Â· KABUPATEN <?php echo htmlspecialchars(strtoupper($kabupaten)); ?> Â· PROVINSI <?php echo htmlspecialchars(strtoupper($provinsi)); ?></div>
                    <div class="footer-badge">KEMENTERIAN AGAMA REPUBLIK INDONESIA</div>
                </div>
                
            </div>
        </div>
    </div>    <?php if ($is_pramuka): ?>
    <!-- ================= PRAMUKA TEMPLATE ================= -->

    <!-- PAGE 2: LEMBAR PENGESAHAN -->
    <div class="page">
        <div class="num-bottom-center">ii</div>
        <h1 class="chapter-title" style="margin-top: 1cm;">Lembar Pengesahan</h1>
        
        <p class="no-indent">Setelah memperhatikan saran, pertimbangan, dan masukan dari Majelis Pembimbing Gugus Depan, maka Program Kerja Pembina Pramuka Gugus Depan Pangkalan <?php echo htmlspecialchars($sekolah_nama); ?> Tahun Pelajaran <?php echo htmlspecialchars($tahun_ajaran); ?> ini dinyatakan sah and berlaku mulai tanggal ditetapkan.</p>
        
        <div style="margin-top: 2cm; text-align: right; padding-right: 1.5cm;">
            Ditetapkan di : Tambak Jaya<br>
            Pada tanggal : <?php echo date('d F Y'); ?>
        </div>

        <div class="sig-block">
            <div class="sig-column">
                Mengetahui,<br>
                <strong>Kamad / Ka. Mabigus</strong>
                <div class="sig-space"></div>
                <u><strong><?php echo htmlspecialchars($kepala_nama); ?></strong></u><br>
                NIP. <?php echo !empty($inst['nip_kepala']) ? htmlspecialchars($inst['nip_kepala']) : '-'; ?>
            </div>
            <div class="sig-column">
                <br>
                <strong>Pembina Pramuka</strong>
                <div class="sig-space"></div>
                <u><strong><?php echo htmlspecialchars($pembina_nama); ?></strong></u><br>
                NIP. <?php echo htmlspecialchars($pembina_nip); ?>
            </div>
        </div>
    </div>

    <!-- PAGE 3: KATA PENGANTAR -->
    <div class="page">
        <div class="num-bottom-center">iii</div>
        <h1 class="chapter-title">Kata Pengantar</h1>
        <p>Puji syukur kami panjatkan ke hadirat Allah SWT, karena berkat limpahan rahmat, taufik, serta hidayah-Nya, penyusunan Program Kerja Pembina Pramuka Gugus Depan Pangkalan <?php echo htmlspecialchars($sekolah_nama); ?> Tahun Pelajaran <?php echo htmlspecialchars($tahun_ajaran); ?> dapat diselesaikan dengan baik.</p>
        <p>Program kerja ini merupakan acuan formal dalam pelaksanaan program latihan rutin, manajemen keanggotaan, serta pembinaan karakter kepramukaan bagi peserta didik. Fokus pembinaan diarahkan pada penguatan karakter disiplin, kemandirian, gotong-royong, serta penanaman Dasa Dharma dan Tri Satya yang selaras dengan Kurikulum Merdeka.</p>
        <p>Kami menyadari bahwa program kerja ini tidak akan terlaksana tanpa adanya dukungan sinergis dari Majelis Pembimbing Gugus Depan, rekan-rekan pembina, dan seluruh wali murid. Oleh karena itu, kritik dan saran konstruktif sangat kami harapkan demi penyempurnaan program latihan ini di masa yang akan datang.</p>
        
        <div style="margin-top: 3cm; text-align: right; padding-right: 1.5cm;">
            Tambak Jaya, <?php echo date('F Y'); ?><br><br>
            <strong>Tim Pembina</strong>
        </div>
    </div>

    <!-- PAGE 4: DAFTAR ISI -->
    <div class="page">
        <div class="num-bottom-center">iv</div>
        <h1 class="chapter-title">Daftar Isi</h1>
        <div style="margin: 2cm 0; line-height: 2; font-size: 11pt;">
            <div style="display:flex; justify-content:space-between;"><span>Halaman Awal (Cover, Pengesahan, Kata Pengantar, Daftar Isi)</span><span>i</span></div>
            <div style="display:flex; justify-content:space-between;"><span>Daftar Isi</span><span>iv</span></div>
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB I PENDAHULUAN</span><span>1</span></div>
            <div style="display:flex; justify-content:space-between; padding-left:1.5cm;"><span>A. Latar Belakang</span><span>1</span></div>
            <div style="display:flex; justify-content:space-between; padding-left:1.5cm;"><span>B. Dasar Hukum</span><span>1</span></div>
            <div style="display:flex; justify-content:space-between; padding-left:1.5cm;"><span>C. Tujuan Program</span><span>2</span></div>
            <div style="display:flex; justify-content:space-between; padding-left:1.5cm;"><span>D. Sasaran Program</span><span>2</span></div>
            
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB II PROFIL GUGUS DEPAN</span><span>3</span></div>
            
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB III TUJUAN DAN TARGET PEMBINAAN</span><span>4</span></div>
            
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB IV PROGRAM LATIHAN RUTIN</span><span>5</span></div>
            
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB V MATERI PEMBINAAN</span><span>7</span></div>
            
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB VI PROGRAM KEGIATAN KHUSUS</span><span>8</span></div>
            
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB VII PENILAIAN</span><span>9</span></div>
            
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB VIII SARANA DAN PRASARANA</span><span>10</span></div>
            
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB IX PEMBIAYAAN</span><span>11</span></div>
            
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB X PENUTUP</span><span>12</span></div>
        </div>
    </div>

    <!-- PAGE 5: BAB I PENDAHULUAN - HAL 1 -->
    <div class="page">
        <div class="num-bottom-center">1</div>
        <h1 class="chapter-title">BAB I<br>PENDAHULUAN</h1>
        
        <h2 class="section-title">A. Latar Belakang</h2>
        <p>Gerakan Pramuka merupakan wadah pembinaan kepramukaan bagi anak dan pemuda Indonesia yang berupaya membentuk generasi penerus bangsa yang disiplin, berintegritas, mandiri, dan tangguh. Pendidikan kepramukaan diselenggarakan sebagai bagian integratif dari kurikulum pendidikan nasional guna menumbuhkan nilai-nilai kepribadian luhur, budi pekerti mulia, serta kecakapan hidup (*life skills*).</p>
        <p>Dalam rangka menyinergikan pembinaan karakter peserta didik di lingkungan madrasah dengan Kurikulum Merdeka, Gugus Depan Pangkalan <?php echo htmlspecialchars($sekolah_nama); ?> memformulasikan program kerja ini sebagai panduan operasional latihan mingguan. Hal ini penting guna mewujudkan tujuan utama pendidikan karakter kebangsaan.</p>
        
        <h2 class="section-title">B. Dasar Hukum</h2>
        <ol style="margin-left: 0.5cm;">
            <li>Undang-Undang Nomor 12 Tahun 2010 tentang Gerakan Pramuka;</li>
            <li>Anggaran Dasar dan Anggaran Rumah Tangga (AD/ART) Gerakan Pramuka;</li>
            <li>Kurikulum Merdeka dan Permendikbudristek mengenai Proyek Penguatan Profil Pelajar Pancasila (P5);</li>
            <li>Rencana Program Kerja Madrasah Gugus Depan Pangkalan <?php echo htmlspecialchars($sekolah_nama); ?>.</li>
        </ol>
    </div>

    <!-- PAGE 6: BAB I PENDAHULUAN - HAL 2 -->
    <div class="page">
        <div class="num-top-right">2</div>
        
        <h2 class="section-title" style="margin-top: 0;">C. Tujuan Program</h2>
        <p><strong>1. Tujuan Umum:</strong></p>
        <p>Membentuk peserta didik yang beriman, bertakwa, berakhlak mulia, disiplin, mandiri, patriotik, serta bertanggung jawab terhadap bangsa dan negara.</p>
        <p><strong>2. Tujuan Khusus:</strong></p>
        <ul style="margin-left: 0.5cm;">
            <li>Menanamkan nilai-nilai luhur Dasa Dharma dan Tri Satya dalam kehidupan sehari-hari;</li>
            <li>Melatih kemandirian dan daya juang peserta didik melalui kegiatan alam terbuka;</li>
            <li>Menumbuhkan sikap gotong royong, solidaritas sosial, dan peduli lingkungan;</li>
            <li>Mengembangkan keterampilan dasar kepramukaan (pioneering, morse, kompas, dll.).</li>
        </ul>
        
        <h2 class="section-title">D. Sasaran Program</h2>
        <p class="no-indent">Sasaran utama dari program pembinaan kepramukaan ini adalah seluruh peserta didik kelas Iâ€“VI yang terdaftar sebagai anggota aktif Gugus Depan Pangkalan <?php echo htmlspecialchars($sekolah_nama); ?>.</p>
    </div>

    <!-- PAGE 7: BAB II PROFIL GUGUS DEPAN -->
    <div class="page">
        <div class="num-bottom-center">3</div>
        <h1 class="chapter-title">BAB II<br>PROFIL GUGUS DEPAN</h1>
        
        <h2 class="section-title">A. Identitas Gugus Depan</h2>
        <p class="no-indent">Profil and identitas pangkalan kepramukaan Gugus Depan <?php echo htmlspecialchars($sekolah_nama); ?> adalah sebagai berikut:</p>
        
        <table class="formal-table">
            <tr><td style="width: 35%;">Nama Pangkalan</td><td><?php echo htmlspecialchars($sekolah_nama); ?></td></tr>
            <tr><td>Alamat Resmi</td><td><?php echo htmlspecialchars($inst['alamat'] ?? '-'); ?></td></tr>
            <tr><td>Desa/Kecamatan</td><td><?php echo htmlspecialchars(($inst['desa'] ?? '-') . ' / ' . ($inst['kecamatan'] ?? '-')); ?></td></tr>
            <tr><td>Kota / Kabupaten</td><td><?php echo htmlspecialchars($inst['kota'] ?? '-'); ?></td></tr>
            <tr><td>Provinsi</td><td><?php echo htmlspecialchars($inst['provinsi'] ?? '-'); ?></td></tr>
            <tr><td>Tahun Ajaran</td><td><?php echo htmlspecialchars($tahun_ajaran); ?></td></tr>
            <tr><td>Jadwal Latihan</td><td>Hari <?php echo htmlspecialchars($hari_latihan); ?>, Pukul <?php echo htmlspecialchars($waktu_latihan); ?></td></tr>
            <tr><td>Jumlah Anggota Aktif</td><td><?php echo htmlspecialchars($sasaran_anggota); ?></td></tr>
        </table>
        
        <h2 class="section-title">B. Struktur Organisasi</h2>
        <p>Guna menjamin kelancaran tata kelola dan operasional organisasi gugus depan, dibentuk struktur kepengurusan dengan komposisi utama sebagai berikut:</p>
        <ul style="margin-left: 0.5cm;">
            <li><strong>Ketua Mabigus (Kamad):</strong> <?php echo htmlspecialchars($kepala_nama); ?></li>
            <li><strong>Pembina Satuan Putra:</strong> <?php echo htmlspecialchars($pembina_putra); ?></li>
            <li><strong>Pembina Satuan Putri:</strong> <?php echo htmlspecialchars($pembina_putri); ?></li>
            <li><strong>Dewan Pasukan Siaga:</strong> Koordinator perwakilan barung siaga putra & putri.</li>
            <li><strong>Dewan Pasukan Penggalang:</strong> Koordinator pratama regu penggalang putra & putri.</li>
        </ul>
    </div>

    <!-- PAGE 8: BAB III TUJUAN DAN TARGET PEMBINAAN -->
    <div class="page">
        <div class="num-bottom-center">4</div>
        <h1 class="chapter-title">BAB III<br>TUJUAN DAN TARGET PEMBINAAN</h1>
        
        <p>Dalam rangka menyelenggarakan pembinaan yang efektif dan efisien, target capaian kompetensi dasar peserta didik dibagi berdasarkan jenjang tingkat perkembangan usia sebagai berikut:</p>
        
        <h2 class="section-title">A. Golongan Siaga Mula (Kelas I - II)</h2>
        <p><strong>Fokus Pembinaan:</strong> Penanaman kedisiplinan dasar, kebersihan diri/lingkungan, latihan kemandirian di rumah, serta keberanian tampil di depan umum.</p>
        
        <h2 class="section-title">B. Golongan Siaga Bantu/Tata (Kelas III - IV)</h2>
        <p><strong>Fokus Pembinaan:</strong> Penguatan kerja sama kelompok (Barung), latihan kepemimpinan sederhana, serta penguasaan keterampilan dasar pramuka siaga (tali temali sederhana, tanda jejak).</p>
        
        <h2 class="section-title">C. Golongan Penggalang Ramu/Rakit (Kelas V - VI)</h2>
        <p><strong>Fokus Pembinaan:</strong> Pembentukan karakter kepemimpinan tim (Regu), tanggung jawab sosial, serta penguasaan keterampilan penggalang lanjutan (pioneering tiang bendera, kompas, morse, semaphore).</p>

        <?php if (!empty($target_pencapaian_kustom)): ?>
            <div style="background:#f8fafc; border:1px dashed #cbd5e1; padding:15px; margin-top:20px; border-radius:8px;">
                <div style="font-weight:bold; font-size:11pt; margin-bottom:8px; text-transform:uppercase;">Catatan Target Khusus Semester Ini (Input Pembina):</div>
                <div style="font-size:11pt; line-height:1.5; white-space: pre-wrap;"><?php echo htmlspecialchars($target_pencapaian_kustom); ?></div>
            </div>
        <?php endif; ?>
    </div>

    <!-- PAGE 9: BAB IV PROGRAM LATIHAN RUTIN (SEMESTER GANJIL) -->
    <div class="page">
        <div class="num-bottom-center">5</div>
        <h1 class="chapter-title">BAB IV<br>PROGRAM LATIHAN RUTIN</h1>
        
        <h2 class="section-title">A. Rincian Latihan Rutin Semester Ganjil</h2>
        <table class="formal-table" style="font-size: 9.5pt; line-height: 1.3;">
            <thead>
                <tr>
                    <th style="width: 15%;">Pertemuan</th>
                    <th style="width: 50%;">Materi Latihan</th>
                    <th style="width: 35%;">Target Kompetensi</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Pertemuan 1</td><td>Pengenalan Pramuka & Orientasi</td><td>Memahami sejarah pramuka singkat</td></tr>
                <tr><td>Pertemuan 2</td><td>Salam Pramuka & Penggunaan Atribut</td><td>Memahami etika berpakaian pramuka</td></tr>
                <tr><td>Pertemuan 3</td><td>Lambang Pramuka (Tunas Kelapa)</td><td>Mengerti arti filosofis tunas kelapa</td></tr>
                <tr><td>Pertemuan 4</td><td>Tri Satya</td><td>Menghafal & mengamalkan janji Tri Satya</td></tr>
                <tr><td>Pertemuan 5</td><td>Dasa Dharma</td><td>Menghafal & mengamalkan 10 Dasa Dharma</td></tr>
                <tr><td>Pertemuan 6</td><td>Baris Berbaris Dasar (PBB)</td><td>Menguasai hadap kiri, kanan, balik kanan</td></tr>
                <tr><td>Pertemuan 7</td><td>Tepuk Pramuka & Yel-yel Regu</td><td>Kekompakan tim dan kreativitas</td></tr>
                <tr><td>Pertemuan 8</td><td>Permainan Edukatif (Wide Game)</td><td>Kerjasama tim dan ketangkasan fisik</td></tr>
                <tr><td>Pertemuan 9</td><td>Simpul Mati & Simpul Hidup</td><td>Menguasai tali temali dasar pengikat</td></tr>
                <tr><td>Pertemuan 10</td><td>Simpul Pangkal & Simpul Jangkar</td><td>Dapat membuat ikatan pada tiang kayu</td></tr>
                <tr><td>Pertemuan 11</td><td>Sandi Morse Dasar</td><td>Dapat menulis morse abjad dasar A-Z</td></tr>
                <tr><td>Pertemuan 12</td><td>Sandi Semaphore Dasar</td><td>Dapat memperagakan isyarat A-M</td></tr>
                <tr><td>Pertemuan 13</td><td>Penjelajahan Lingkungan Madrasah</td><td>Ketaatan pada tanda jejak buatan</td></tr>
                <tr><td>Pertemuan 14</td><td>Kebersihan Lingkungan (Bakti Sosial)</td><td>Kepedulian terhadap lingkungan sekitar</td></tr>
                <tr><td>Pertemuan 15</td><td>Lomba Ketangkasan Regu Internal</td><td>Menguji keterampilan yang telah dipelajari</td></tr>
                <tr><td>Pertemuan 16</td><td>Evaluasi Semester & Ujian SKU</td><td>Penilaian kenaikan tingkat/pemberian TKK</td></tr>
            </tbody>
        </table>
    </div>

    <!-- PAGE 10: BAB IV PROGRAM LATIHAN RUTIN (SEMESTER GENAP) -->
    <div class="page">
        <div class="num-top-right">6</div>
        
        <h2 class="section-title" style="margin-top: 0;">B. Rincian Latihan Rutin Semester Genap</h2>
        <p class="no-indent">Program latihan semester genap difokuskan pada pemantapan ketangkasan lapangan serta persiapan pelantikan dan perkemahan:</p>
        
        <table class="formal-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Pertemuan</th>
                    <th style="width: 75%;">Materi &amp; Deskripsi Latihan</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Pertemuan 17â€“20</td><td><strong>Tali-temali Lanjutan & PBB Lanjutan</strong><br>Penyusunan tandu darurat (pioneering) dan baris-berbaris dengan tongkat.</td></tr>
                <tr><td>Pertemuan 21â€“24</td><td><strong>Pertolongan Pertama Sederhana (PPGD) & Kompas</strong><br>Penanganan luka ringan, pembidaian, serta membaca arah mata angin menggunakan kompas bidik.</td></tr>
                <tr><td>Pertemuan 25â€“28</td><td><strong>Peta Pita & Pioneering Sederhana</strong><br>Membuat peta perjalanan dan merakit bangunan kreatif kaki tiga dari bambu/tongkat.</td></tr>
                <tr><td>Pertemuan 29â€“31</td><td><strong>Hasta Karya, Kewirausahaan, dan Bakti Sosial</strong><br>Membuat kerajinan tangan bernilai jual serta melakukan aksi bersih fasilitas sosial.</td></tr>
                <tr><td>Pertemuan 32</td><td><strong>Ujian Kenaikan Tingkat Akhir Tahun</strong><br>Pengisian lembar SKU final untuk kenaikan golongan.</td></tr>
                <tr><td>Kegiatan Puncak</td><td><strong>Perkemahan Sabtu-Minggu (Persami) & Pelantikan</strong><br>Pelaksanaan perkemahan evaluasi akhir tahun pelajaran sekaligus pelantikan kenaikan tingkat/penggalang.</td></tr>
            </tbody>
        </table>
    </div>

    <!-- PAGE 11: BAB V MATERI PEMBINAAN -->
    <div class="page">
        <div class="num-bottom-center">7</div>
        <h1 class="chapter-title">BAB V<br>MATERI PEMBINAAN</h1>
        
        <h2 class="section-title">A. Nilai Kepramukaan</h2>
        <p>Materi inti pembinaan akhlak didasarkan pada pemahaman dan pengamalan janji **Tri Satya** serta ketentuan moral **Dasa Dharma** Pramuka dalam interaksi sehari-hari.</p>
        
        <h2 class="section-title">B. Keterampilan Dasar Kepramukaan</h2>
        <ul style="margin-left: 0.5cm;">
            <li><strong>PBB (Peraturan Baris Berbaris):</strong> Pembiasaan sikap jasmani yang tegap dan tangkas, rasa disiplin, serta tanggung jawab kelompok.</li>
            <li><strong>Sandi & Isyarat:</strong> Sandi morse (tiupan peluit/bendera) dan semaphore (isyarat bendera tangan).</li>
            <li><strong>Tali-Temali:</strong> Penggunaan simpul mati, pangkal, jangkar, tiang, serta ikatan silang/palang dalam pioneering.</li>
        </ul>
        
        <h2 class="section-title">C. Karakter Islami (Ciri Khas Madrasah)</h2>
        <p>Sebagai lembaga pendidikan Islam, pembinaan pramuka di <?php echo htmlspecialchars($sekolah_nama); ?> diintegrasikan dengan nilai akhlak karimah, yang meliputi:</p>
        <ol style="margin-left: 0.5cm;">
            <li>Membiasakan shalat berjamaah tepat waktu saat kegiatan latihan dan perkemahan.</li>
            <li>Menjaga adab kesopanan kepada guru, rekan sebaya, serta orang tua di rumah.</li>
            <li>Mengutamakan nilai kejujuran (*Siddiq*) dan tanggung jawab (*Amanah*).</li>
        </ol>
        
        <h2 class="section-title">D. Keterampilan Hidup (Life Skills)</h2>
        <p class="no-indent">Peserta didik diajarkan cara menjaga kebersihan diri, merapikan perlengkapan belajar secara mandiri, bergotong royong membersihkan tempat ibadah, serta mengelola sampah organik/anorganik secara bijak.</p>
    </div>

    <!-- PAGE 12: BAB VI s.d. X - HAL 1 -->
    <div class="page">
        <div class="num-bottom-center">8</div>
        <h1 class="chapter-title">BAB VI s.d. X<br>KEGIATAN KHUSUS, PENILAIAN, DAN PENUTUP</h1>
        
        <h2 class="section-title">A. Program Kegiatan Khusus</h2>
        <p class="no-indent">Program khusus pangkalan Gugus Depan meliputi:</p>
        <ul style="margin-left: 0.5cm;">
            <li><strong>Persami (Perkemahan Sabtu Minggu):</strong> Diadakan minimal 1 kali per semester untuk mengevaluasi SKU lapangan.</li>
            <li><strong>Hari Pramuka (14 Agustus):</strong> Upacara bendera dan pawai parade kebesaran.</li>
            <li><strong>Bakti Sosial Kemasyarakatan:</strong> Pembagian paket sembako/bersih fasilitas umum desa.</li>
        </ul>

        <h2 class="section-title">B. Mekanisme Penilaian</h2>
        <p>Penilaian anggota pramuka dilakukan secara komprehensif mencakup 3 aspek utama:</p>
        <ol style="margin-left: 0.5cm;">
            <li><strong>Aspek Sikap:</strong> Kedisiplinan kehadiran, tanggung jawab tugas regu, dan gotong royong.</li>
            <li><strong>Aspek Keterampilan:</strong> Kelulusan uji simpul, kecakapan baris-berbaris, dan pembacaan isyarat sandi.</li>
            <li><strong>Aspek Keaktifan:</strong> Presentase kehadiran latihan rutin mingguan.</li>
        </ol>
    </div>

    <!-- PAGE 13: BAB VI s.d. X - HAL 2 -->
    <div class="page">
        <div class="num-top-right">9</div>

        <h2 class="section-title" style="margin-top: 0;">C. Sarana &amp; Prasarana Pendukung</h2>
        <p class="no-indent">Fasilitas pendukung latihan rutin pangkalan meliputi: Tenda dome/pramuka, tongkat bambu standar, tali nilon, peluit pembina, bendera semaphore, kompas bidik, serta buku SKU Siaga & Penggalang.</p>

        <h2 class="section-title">D. Pembiayaan Kegiatan</h2>
        <p class="no-indent">Sumber anggaran operasional pembinaan kepramukaan bersumber dari dana **Bantuan Operasional Sekolah (BOS)**, **Komite Madrasah**, serta sumbangan donatur resmi lainnya yang dialokasikan berdasarkan Rencana Anggaran Biaya (RAB) kegiatan tahunan.</p>

        <h2 class="section-title">E. Penutup</h2>
        <p class="no-indent">Demikian Program Kerja Pembina Pramuka Gugus Depan Pangkalan <?php echo htmlspecialchars($sekolah_nama); ?> ini kami susun. Keberhasilan pelaksanaan program kerja ini sangat bergantung pada kerjasama aktif seluruh pihak terkait demi tercapainya generasi muda yang berkarakter kokoh dan berakhlak mulia.</p>
    </div>
    <?php elseif ($is_voli): ?>
    <!-- ================= BOLA VOLI MINI TEMPLATE ================= -->

    <!-- PAGE 2: LEMBAR PENGESAHAN -->
    <div class="page">
        <div class="num-bottom-center">ii</div>
        <h1 class="chapter-title" style="margin-top: 1cm;">Lembar Pengesahan</h1>
        
        <p class="no-indent">Setelah memperhatikan saran, pertimbangan, dan masukan dari Komite Madrasah dan pihak terkait, maka Program Pembinaan Ekstrakurikuler Bola Voli Mini <?php echo htmlspecialchars($sekolah_nama); ?> Tahun Pelajaran <?php echo htmlspecialchars($tahun_ajaran); ?> ini dinyatakan sah dan berlaku mulai tanggal ditetapkan.</p>
        
        <div style="margin-top: 2cm; text-align: right; padding-right: 1.5cm;">
            Ditetapkan di : Tambak Jaya<br>
            Pada tanggal : <?php echo date('d F Y'); ?>
        </div>

        <div class="sig-block">
            <div class="sig-column">
                Mengetahui,<br>
                <strong>Kepala Madrasah</strong>
                <div class="sig-space"></div>
                <u><strong><?php echo htmlspecialchars($kepala_nama); ?></strong></u><br>
                NIP. <?php echo !empty($inst['nip_kepala']) ? htmlspecialchars($inst['nip_kepala']) : '-'; ?>
            </div>
            <div class="sig-column">
                <br>
                <strong>Pembina Bola Voli Mini</strong>
                <div class="sig-space"></div>
                <u><strong><?php echo htmlspecialchars($pembina_nama); ?></strong></u><br>
                NIP. <?php echo htmlspecialchars($pembina_nip); ?>
            </div>
        </div>
    </div>

    <!-- PAGE 3: KATA PENGANTAR -->
    <div class="page">
        <div class="num-bottom-center">iii</div>
        <h1 class="chapter-title">Kata Pengantar</h1>
        <p>Puji syukur kami panjatkan ke hadirat Allah SWT, karena berkat limpahan rahmat, taufik, serta hidayah-Nya, penyusunan Program Pembinaan Ekstrakurikuler Bola Voli Mini <?php echo htmlspecialchars($sekolah_nama); ?> Tahun Pelajaran <?php echo htmlspecialchars($tahun_ajaran); ?> dapat diselesaikan dengan baik.</p>
        <p>Program pembinaan ini disusun sebagai pedoman dalam pelaksanaan kegiatan latihan rutin, pembinaan bakat olahraga, serta penanaman karakter sportivitas, disiplin, dan tanggung jawab peserta didik. Olahraga bola voli mini merupakan sarana yang efektif untuk melatih kebugaran jasmani sekaligus melatih kerja sama tim.</p>
        <p>Kami menyadari bahwa keberhasilan program ini memerlukan dukungan sinergis dari kepala madrasah, guru, pembina, serta wali murid. Oleh karena itu, masukan dan saran sangat kami harapkan demi kesempurnaan latihan di masa mendatang.</p>
        
        <div style="margin-top: 3cm; text-align: right; padding-right: 1.5cm;">
            Tambak Jaya, <?php echo date('F Y'); ?><br><br>
            <strong>Tim Pembina</strong>
        </div>
    </div>

    <!-- PAGE 4: DAFTAR ISI -->
    <div class="page">
        <div class="num-bottom-center">iv</div>
        <h1 class="chapter-title">Daftar Isi</h1>
        <div style="margin: 2cm 0; line-height: 2; font-size: 11pt;">
            <div style="display:flex; justify-content:space-between;"><span>Halaman Awal (Cover, Pengesahan, Kata Pengantar, Daftar Isi)</span><span>i</span></div>
            <div style="display:flex; justify-content:space-between;"><span>Daftar Isi</span><span>iv</span></div>
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB I PENDAHULUAN</span><span>1</span></div>
            <div style="display:flex; justify-content:space-between; padding-left:1.5cm;"><span>A. Latar Belakang</span><span>1</span></div>
            <div style="display:flex; justify-content:space-between; padding-left:1.5cm;"><span>B. Dasar Hukum</span><span>1</span></div>
            <div style="display:flex; justify-content:space-between; padding-left:1.5cm;"><span>C. Tujuan</span><span>2</span></div>
            <div style="display:flex; justify-content:space-between; padding-left:1.5cm;"><span>D. Sasaran</span><span>2</span></div>
            
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB II VISI, MISI DAN TARGET</span><span>3</span></div>
            
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB III PROGRAM LATIHAN</span><span>4</span></div>
            
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB IV METODE LATIHAN</span><span>6</span></div>
            
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB V SARANA DAN PRASARANA</span><span>7</span></div>
            
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB VI PENILAIAN</span><span>8</span></div>
            
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB VII KEJUARAAN DAN PRESTASI</span><span>9</span></div>
            
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB VIII PENUTUP</span><span>10</span></div>
        </div>
    </div>

    <!-- PAGE 5: BAB I PENDAHULUAN - HAL 1 -->
    <div class="page">
        <div class="num-bottom-center">1</div>
        <h1 class="chapter-title">BAB I<br>PENDAHULUAN</h1>
        
        <h2 class="section-title">A. Latar Belakang</h2>
        <p>Ekstrakurikuler Bola Voli Mini merupakan salah satu kegiatan pengembangan diri yang bertujuan untuk menyalurkan minat dan bakat peserta didik dalam bidang olahraga. Melalui kegiatan ini peserta didik diharapkan mampu mengembangkan kemampuan fisik, keterampilan bermain bola voli, sikap sportivitas, kerja sama tim, disiplin, serta tanggung jawab.</p>
        <p>Kegiatan ekstrakurikuler bola voli mini juga menjadi sarana pembinaan prestasi peserta didik sehingga mampu berpartisipasi dalam berbagai kompetisi baik di tingkat madrasah, kecamatan, kabupaten, maupun tingkat yang lebih tinggi.</p>
        
        <h2 class="section-title">B. Dasar Hukum</h2>
        <ol style="margin-left: 0.5cm;">
            <li>Undang-Undang Nomor 20 Tahun 2003 tentang Sistem Pendidikan Nasional;</li>
            <li>Undang-Undang Nomor 11 Tahun 2022 tentang Keolahragaan;</li>
            <li>Permendikbud tentang Kegiatan Ekstrakurikuler;</li>
            <li>Program Kerja MI Miftahul Huda Mathla'ul Anwar;</li>
            <li>Keputusan Kepala Madrasah tentang Pembina Ekstrakurikuler.</li>
        </ol>
    </div>

    <!-- PAGE 6: BAB I PENDAHULUAN - HAL 2 -->
    <div class="page">
        <div class="num-top-right">2</div>
        
        <h2 class="section-title" style="margin-top: 0;">C. Tujuan Program</h2>
        <p><strong>1. Tujuan Umum:</strong></p>
        <p>Mengembangkan potensi peserta didik dalam bidang olahraga bola voli serta membentuk karakter yang sehat, disiplin, bertanggung jawab, dan sportif.</p>
        <p><strong>2. Tujuan Khusus:</strong></p>
        <ul style="margin-left: 0.5cm;">
            <li>Mengenalkan teknik dasar permainan bola voli mini;</li>
            <li>Melatih kebugaran jasmani peserta didik;</li>
            <li>Menumbuhkan sikap kerja sama dan sportivitas;</li>
            <li>Mengembangkan bakat dan prestasi olahraga;</li>
            <li>Mempersiapkan peserta didik mengikuti perlombaan.</li>
        </ul>
        
        <h2 class="section-title">D. Sasaran</h2>
        <p class="no-indent">Sasaran utama kegiatan ekstrakurikuler ini adalah peserta didik kelas IV, V, dan VI <?php echo htmlspecialchars($sekolah_nama); ?> yang memiliki minat, bakat, serta komitmen tinggi dalam olahraga bola voli.</p>
    </div>

    <!-- PAGE 7: BAB II VISI, MISI DAN TARGET -->
    <div class="page">
        <div class="num-bottom-center">3</div>
        <h1 class="chapter-title">BAB II<br>VISI, MISI DAN TARGET</h1>
        
        <h2 class="section-title">A. Visi</h2>
        <p>Terwujudnya peserta didik yang sehat, disiplin, sportif, dan berprestasi melalui kegiatan bola voli mini.</p>
        
        <h2 class="section-title">B. Misi</h2>
        <ol style="margin-left: 0.5cm;">
            <li>Menyelenggarakan latihan yang terprogram dan berkelanjutan;</li>
            <li>Menumbuhkan semangat kompetisi yang sehat;</li>
            <li>Meningkatkan kemampuan teknik dan fisik peserta didik;</li>
            <li>Membentuk karakter sportif dan bertanggung jawab.</li>
        </ol>

        <h2 class="section-title">C. Target Capaian</h2>
        <p><strong>1. Jangka Pendek:</strong></p>
        <p>Memahami aturan dasar permainan bola voli mini serta menguasai teknik passing bawah dan passing atas dengan baik.</p>
        <p><strong>2. Jangka Menengah:</strong></p>
        <p>Mampu bermain secara kompak dalam tim dan aktif berpartisipasi dalam pertandingan tingkat madrasah.</p>
        <p><strong>3. Jangka Panjang:</strong></p>
        <p>Meraih prestasi membanggakan pada ajang kompetisi olahraga resmi tingkat kecamatan maupun kabupaten.</p>
    </div>

    <!-- PAGE 8: BAB III PROGRAM LATIHAN (GANJIL) -->
    <div class="page">
        <div class="num-bottom-center">4</div>
        <h1 class="chapter-title">BAB III<br>PROGRAM LATIHAN</h1>
        
        <h2 class="section-title">A. Program Latihan Semester Ganjil</h2>
        <table class="formal-table" style="font-size: 9.5pt; line-height: 1.3;">
            <thead>
                <tr>
                    <th style="width: 15%;">Pertemuan</th>
                    <th style="width: 45%;">Materi Latihan</th>
                    <th style="width: 40%;">Target Kompetensi</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Pertemuan 1</td><td>Pengenalan Bola Voli Mini</td><td>Memahami lapangan, net, dan ukuran bola voli mini</td></tr>
                <tr><td>Pertemuan 2</td><td>Peraturan Dasar Permainan</td><td>Memahami aturan servis, rotasi dasar, dan perhitungan skor</td></tr>
                <tr><td>Pertemuan 3</td><td>Latihan Kebugaran Jasmani</td><td>Meningkatkan kelincahan kaki, refleks, dan kekuatan fisik dasar</td></tr>
                <tr><td>Pertemuan 4</td><td>Passing Bawah</td><td>Menguasai posisi kuda-kuda dan perkenaan bola pada lengan</td></tr>
                <tr><td>Pertemuan 5</td><td>Passing Bawah Lanjutan</td><td>Melatih akurasi arah pantulan bola secara berpasangan</td></tr>
                <tr><td>Pertemuan 6</td><td>Passing Atas</td><td>Memahami posisi jari tangan membentuk mangkuk di atas dahi</td></tr>
                <tr><td>Pertemuan 7</td><td>Passing Atas Lanjutan</td><td>Melatih ketepatan mengumpan bola tinggi untuk penyerang</td></tr>
                <tr><td>Pertemuan 8</td><td>Servis Bawah</td><td>Melatih teknik ayunan lengan dan ketepatan memukul bola</td></tr>
                <tr><td>Pertemuan 9</td><td>Servis Bawah Lanjutan</td><td>Melatih konsistensi servis bawah agar menyeberangi net masuk lapangan</td></tr>
                <tr><td>Pertemuan 10</td><td>Servis Atas Dasar</td><td>Mengenal teknik perkenaan bola saat melompat/melempar bola atas</td></tr>
                <tr><td>Pertemuan 11</td><td>Kombinasi Passing dan Servis</td><td>Latihan menerima servis lawan lalu dioper ke pengumpan</td></tr>
                <tr><td>Pertemuan 12</td><td>Rotasi Pemain</td><td>Dapat berpindah posisi searah jarum jam saat memenangkan servis</td></tr>
                <tr><td>Pertemuan 13</td><td>Kerja Sama Tim</td><td>Latihan koordinasi komunikasi antar pemain di dalam lapangan</td></tr>
                <tr><td>Pertemuan 14</td><td>Simulasi Pertandingan</td><td>Menerapkan seluruh aturan formal dalam tanding internal</td></tr>
                <tr><td>Pertemuan 15</td><td>Turnamen Internal</td><td>Menguji mental bertanding secara berkelompok antar regu</td></tr>
                <tr><td>Pertemuan 16</td><td>Evaluasi Semester</td><td>Penilaian kelulusan kompetensi dasar fisik & teknik</td></tr>
            </tbody>
        </table>
    </div>

    <!-- PAGE 9: BAB III PROGRAM LATIHAN (GENAP) -->
    <div class="page">
        <div class="num-top-right">5</div>
        
        <h2 class="section-title" style="margin-top: 0;">B. Program Latihan Semester Genap</h2>
        <table class="formal-table" style="font-size: 9.5pt; line-height: 1.3;">
            <thead>
                <tr>
                    <th style="width: 15%;">Pertemuan</th>
                    <th style="width: 45%;">Materi Latihan</th>
                    <th style="width: 40%;">Target Kompetensi</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Pertemuan 17</td><td>Penguatan Teknik Dasar</td><td>Penyegaran teknik passing bawah, atas, dan servis</td></tr>
                <tr><td>Pertemuan 18</td><td>Smash Dasar</td><td>Latihan langkah awalan, lompatan, dan ayunan tangan memukul</td></tr>
                <tr><td>Pertemuan 19</td><td>Smash Lanjutan</td><td>Melatih ketepatan memukul bola lambung di atas net dengan keras</td></tr>
                <tr><td>Pertemuan 20</td><td>Blocking Dasar</td><td>Melatih penempatan posisi tangan di atas net untuk menahan smash</td></tr>
                <tr><td>Pertemuan 21</td><td>Kombinasi Teknik</td><td>Latihan alur permainan lengkap: Terima Bola - Umpan - Smash</td></tr>
                <tr><td>Pertemuan 22</td><td>Strategi Permainan</td><td>Mengatur pertahanan dari serangan smash lawan</td></tr>
                <tr><td>Pertemuan 23</td><td>Formasi Tim</td><td>Menyusun penempatan pemain sesuai keahlian (tosser/spiker)</td></tr>
                <tr><td>Pertemuan 24</td><td>Latihan Fisik</td><td>Meningkatkan lompatan tegak (*vertical jump*) dan kecepatan</td></tr>
                <tr><td>Pertemuan 25</td><td>Simulasi Kompetisi</td><td>Tanding dengan simulasi sistem gugur</td></tr>
                <tr><td>Pertemuan 26</td><td>Sparing Internal</td><td>Latihan tanding antar tim inti madrasah</td></tr>
                <tr><td>Pertemuan 27</td><td>Sparing Antar Sekolah</td><td>Mengukur kemampuan tim dengan bertanding melawan sekolah lain</td></tr>
                <tr><td>Pertemuan 28</td><td>Persiapan Lomba</td><td>Pemantapan mental juara dan koordinasi akhir formasi tim</td></tr>
                <tr><td>Pertemuan 29</td><td>Seleksi Tim</td><td>Penetapan daftar pemain inti untuk dikirim ke perlombaan</td></tr>
                <tr><td>Pertemuan 30</td><td>Uji Kemampuan</td><td>Ujian akhir praktik kemampuan teknik individu</td></tr>
                <tr><td>Pertemuan 31</td><td>Turnamen Internal</td><td>Turnamen penutup tahun pelajaran sebagai apresiasi latihan</td></tr>
                <tr><td>Pertemuan 32</td><td>Evaluasi Tahunan</td><td>Penilaian rapor ekstrakurikuler serta penyusunan laporan</td></tr>
            </tbody>
        </table>
    </div>

    <!-- PAGE 10: BAB IV & BAB V -->
    <div class="page">
        <div class="num-bottom-center">6</div>
        <h1 class="chapter-title">BAB IV &amp; BAB V<br>METODE LATIHAN DAN SARANA PRASARANA</h1>
        
        <h2 class="section-title">A. Metode Latihan</h2>
        <p class="no-indent">Metodologi yang digunakan pembina dalam mendidik peserta didik bola voli mini meliputi:</p>
        <ul style="margin-left: 0.5cm;">
            <li><strong>Ceramah dan Demonstrasi:</strong> Menjelaskan teori dasar dan memperagakan gerakan tubuh secara langsung;</li>
            <li><strong>Latihan Individu:</strong> Latihan mandiri untuk meningkatkan memori otot perorangan;</li>
            <li><strong>Latihan Berpasangan:</strong> Melatih refleks kontrol arah bola bolak-balik;</li>
            <li><strong>Latihan Kelompok:</strong> Mengatur pola koordinasi formasi taktis tim;</li>
            <li><strong>Simulasi Pertandingan:</strong> Melatih mental di bawah tekanan atmosfer kompetisi;</li>
            <li><strong>Evaluasi Berkala:</strong> Memberikan koreksi performa individu di tiap akhir pekan latihan.</li>
        </ul>

        <h2 class="section-title">B. Sarana dan Prasarana Pendukung</h2>
        <table class="formal-table">
            <thead>
                <tr>
                    <th style="width: 10%; text-align:center;">No</th>
                    <th style="width: 50%;">Sarana / Prasarana</th>
                    <th style="width: 40%;">Status Ketersediaan</th>
                </tr>
            </thead>
            <tbody>
                <tr><td style="text-align:center;">1</td><td>Bola Voli Mini (Ukuran 4)</td><td>Ada / Layak Pakai</td></tr>
                <tr><td style="text-align:center;">2</td><td>Net Voli Standar Mini</td><td>Ada / Baik</td></tr>
                <tr><td style="text-align:center;">3</td><td>Tiang Net Voli (Bisa disetel tingginya)</td><td>Ada / Kokoh</td></tr>
                <tr><td style="text-align:center;">4</td><td>Lapangan Bola Voli Out-door</td><td>Ada / Memadai</td></tr>
                <tr><td style="text-align:center;">5</td><td>Peluit Pembina</td><td>Ada / Baik</td></tr>
                <tr><td style="text-align:center;">6</td><td>Rompi Latihan Tim (2 warna berbeda)</td><td>Ada / Cukup</td></tr>
                <tr><td style="text-align:center;">7</td><td>Papan Skor Manual</td><td>Ada / Baik</td></tr>
                <tr><td style="text-align:center;">8</td><td>Kotak Obat Pertolongan Pertama (P3K)</td><td>Ada / Lengkap</td></tr>
            </tbody>
        </table>
    </div>

    <!-- PAGE 11: BAB VI & VII -->
    <div class="page">
        <div class="num-bottom-center">7</div>
        <h1 class="chapter-title">BAB VI &amp; BAB VII<br>PENILAIAN DAN RENCANA PRESTASI</h1>
        
        <h2 class="section-title">A. Aspek Penilaian</h2>
        <p class="no-indent">Evaluasi kelulusan pembinaan ekstrakurikuler dinilai dari 3 aspek utama:</p>
        <ol style="margin-left: 0.5cm;">
            <li><strong>Aspek Sikap:</strong> Kedisiplinan waktu latihan, kepatuhan instruksi pembina, tanggung jawab merawat alat, serta sportivitas tanding.</li>
            <li><strong>Aspek Keterampilan:</strong> Penguasaan praktik passing bawah, passing atas, kualitas pukulan servis, serta akurasi smash & block dasar.</li>
            <li><strong>Aspek Kehadiran:</strong> Persentase kehadiran kehadiran minimum 75% dari total latihan rutin mingguan.</li>
        </ol>

        <h2 class="section-title">B. Program Kejuaraan dan Rencana Prestasi</h2>
        <p class="no-indent">Guna menyalurkan bakat kompetitif, tim utama madrasah diproyeksikan untuk ikut serta dalam:</p>
        <ul style="margin-left: 0.5cm;">
            <li>Olimpiade Olahraga Siswa Nasional (O2SN) jenjang sekolah dasar/madrasah ibtidaiyah;</li>
            <li>Turnamen persahabatan antar sekolah dasar se-Kecamatan Way Tenong;</li>
            <li>Pekan Olahraga Kecamatan (Porcam) Lampung Barat;</li>
            <li>Kompetisi bola voli undangan resmi yang diselenggarakan instansi terkait.</li>
        </ul>
    </div>

    <!-- PAGE 12: BAB VIII PENUTUP -->
    <div class="page">
        <div class="num-bottom-center">8</div>
        <h1 class="chapter-title">BAB VIII<br>PENUTUP</h1>
        
        <p class="no-indent">Program Pembinaan Ekstrakurikuler Bola Voli Mini ini disusun sebagai pedoman pelaksanaan kegiatan ekstrakurikuler di <?php echo htmlspecialchars($sekolah_nama); ?>. Keberhasilan pelaksanaan program latihan ini sangat bergantung pada komitmen aktif dari peserta didik, dukungan fasilitas dari madrasah, serta kerja sama yang sinergis dari pembina dan wali murid.</p>
        <p>Melalui perencanaan latihan yang matang, diharapkan dapat terbentuk generasi muda madrasah yang memiliki kebugaran fisik prima, karakter sportivitas yang kokoh, berintegritas tinggi, serta mampu mengharumkan nama baik madrasah melalui prestasi gemilang di bidang olahraga.</p>
        
        <div style="margin-top: 3cm; text-align: right; padding-right: 1.5cm;">
            Tambak Jaya, <?php echo date('d F Y'); ?><br><br>
            <strong>Tim Pembina Olahraga</strong>
        </div>
  <?php else: ?>
<!-- ================= GENERIC TEMPLATE ================= -->

    <!-- PAGE 2: LEMBAR PENGESAHAN -->
    <div class="page">
        <div class="num-bottom-center">ii</div>
        <h1 class="chapter-title" style="margin-top: 1cm;">Lembar Pengesahan</h1>
        
        <p class="no-indent">Setelah memperhatikan saran, pertimbangan, dan masukan dari Komite Madrasah dan pihak terkait, maka Program Pembinaan Ekstrakurikuler <?php echo htmlspecialchars($nama_ekstra); ?> <?php echo htmlspecialchars($sekolah_nama); ?> Tahun Pelajaran <?php echo htmlspecialchars($tahun_ajaran); ?> ini dinyatakan sah dan berlaku mulai tanggal ditetapkan.</p>
        
        <div style="margin-top: 2cm; text-align: right; padding-right: 1.5cm;">
            Ditetapkan di : Tambak Jaya<br>
            Pada tanggal : <?php echo date('d F Y'); ?>
        </div>

        <div class="sig-block">
            <div class="sig-column">
                Mengetahui,<br>
                <strong>Kepala Madrasah</strong>
                <div class="sig-space"></div>
                <u><strong><?php echo htmlspecialchars($kepala_nama); ?></strong></u><br>
                NIP. <?php echo !empty($inst['nip_kepala']) ? htmlspecialchars($inst['nip_kepala']) : '-'; ?>
            </div>
            <div class="sig-column">
                <br>
                <strong>Pembina <?php echo htmlspecialchars($nama_ekstra); ?></strong>
                <div class="sig-space"></div>
                <u><strong><?php echo htmlspecialchars($pembina_nama); ?></strong></u><br>
                NIP. <?php echo htmlspecialchars($pembina_nip); ?>
            </div>
        </div>
    </div>

    <!-- PAGE 3: KATA PENGANTAR -->
    <div class="page">
        <div class="num-bottom-center">iii</div>
        <h1 class="chapter-title">Kata Pengantar</h1>
        <p>Puji syukur kami panjatkan ke hadirat Allah SWT, karena berkat limpahan rahmat, taufik, serta hidayah-Nya, penyusunan Program Pembinaan Ekstrakurikuler <?php echo htmlspecialchars($nama_ekstra); ?> <?php echo htmlspecialchars($sekolah_nama); ?> Tahun Pelajaran <?php echo htmlspecialchars($tahun_ajaran); ?> dapat diselesaikan dengan baik.</p>
        <p>Program pembinaan ini disusun sebagai pedoman dalam pelaksanaan kegiatan latihan rutin, pembinaan bakat bidang ini, serta penanaman karakter sportivitas, disiplin, dan tanggung jawab peserta didik. Olahraga ekstrakurikuler mini merupakan sarana yang efektif untuk melatih kebugaran jasmani sekaligus melatih kerja sama tim.</p>
        <p>Kami menyadari bahwa keberhasilan program ini memerlukan dukungan sinergis dari kepala madrasah, guru, pembina, serta wali murid. Oleh karena itu, masukan dan saran sangat kami harapkan demi kesempurnaan latihan di masa mendatang.</p>
        
        <div style="margin-top: 3cm; text-align: right; padding-right: 1.5cm;">
            Tambak Jaya, <?php echo date('F Y'); ?><br><br>
            <strong>Tim Pembina</strong>
        </div>
    </div>

    <!-- PAGE 4: DAFTAR ISI -->
    <div class="page">
        <div class="num-bottom-center">iv</div>
        <h1 class="chapter-title">Daftar Isi</h1>
        <div style="margin: 2cm 0; line-height: 2; font-size: 11pt;">
            <div style="display:flex; justify-content:space-between;"><span>Halaman Awal (Cover, Pengesahan, Kata Pengantar, Daftar Isi)</span><span>i</span></div>
            <div style="display:flex; justify-content:space-between;"><span>Daftar Isi</span><span>iv</span></div>
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB I PENDAHULUAN</span><span>1</span></div>
            <div style="display:flex; justify-content:space-between; padding-left:1.5cm;"><span>A. Latar Belakang</span><span>1</span></div>
            <div style="display:flex; justify-content:space-between; padding-left:1.5cm;"><span>B. Dasar Hukum</span><span>1</span></div>
            <div style="display:flex; justify-content:space-between; padding-left:1.5cm;"><span>C. Tujuan</span><span>2</span></div>
            <div style="display:flex; justify-content:space-between; padding-left:1.5cm;"><span>D. Sasaran</span><span>2</span></div>
            
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB II VISI, MISI DAN TARGET</span><span>3</span></div>
            
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB III PROGRAM LATIHAN</span><span>4</span></div>
            
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB IV METODE LATIHAN</span><span>6</span></div>
            
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB V SARANA DAN PRASARANA</span><span>7</span></div>
            
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB VI PENILAIAN</span><span>8</span></div>
            
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB VII KEJUARAAN DAN PRESTASI</span><span>9</span></div>
            
            <div style="display:flex; justify-content:space-between; font-weight:bold; margin-top:10px;"><span>BAB VIII PENUTUP</span><span>10</span></div>
        </div>
    </div>

    <!-- PAGE 5: BAB I PENDAHULUAN - HAL 1 -->
    <div class="page">
        <div class="num-bottom-center">1</div>
        <h1 class="chapter-title">BAB I<br>PENDAHULUAN</h1>
        
        <h2 class="section-title">A. Latar Belakang</h2>
        <p>Ekstrakurikuler <?php echo htmlspecialchars($nama_ekstra); ?> merupakan salah satu kegiatan pengembangan diri yang bertujuan untuk menyalurkan minat dan bakat peserta didik dalam bidang bidang ini. Melalui kegiatan ini peserta didik diharapkan mampu mengembangkan kemampuan fisik, keterampilan bermain ekstrakurikuler, sikap sportivitas, kerja sama tim, disiplin, serta tanggung jawab.</p>
        <p>Kegiatan ekstrakurikuler ekstrakurikuler mini juga menjadi sarana pembinaan prestasi peserta didik sehingga mampu berpartisipasi dalam berbagai kompetisi baik di tingkat madrasah, kecamatan, kabupaten, maupun tingkat yang lebih tinggi.</p>
        
        <h2 class="section-title">B. Dasar Hukum</h2>
        <ol style="margin-left: 0.5cm;">
            <li>Undang-Undang Nomor 20 Tahun 2003 tentang Sistem Pendidikan Nasional;</li>
            <li>Undang-Undang Nomor 11 Tahun 2022 tentang Kebidang inian;</li>
            <li>Permendikbud tentang Kegiatan Ekstrakurikuler;</li>
            <li>Program Kerja MI Miftahul Huda Mathla'ul Anwar;</li>
            <li>Keputusan Kepala Madrasah tentang Pembina Ekstrakurikuler.</li>
        </ol>
    </div>

    <!-- PAGE 6: BAB I PENDAHULUAN - HAL 2 -->
    <div class="page">
        <div class="num-top-right">2</div>
        
        <h2 class="section-title" style="margin-top: 0;">C. Tujuan Program</h2>
        <p><strong>1. Tujuan Umum:</strong></p>
        <p>Mengembangkan potensi peserta didik dalam bidang kegiatan ekstrakurikuler ini serta membentuk karakter yang sehat, disiplin, bertanggung jawab, dan sportif.</p>
        <p><strong>2. Tujuan Khusus:</strong></p>
        <ul style="margin-left: 0.5cm;">
            <li>Mengenalkan teknik dasar permainan ekstrakurikuler mini;</li>
            <li>Melatih kebugaran jasmani peserta didik;</li>
            <li>Menumbuhkan sikap kerja sama dan sportivitas;</li>
            <li>Mengembangkan bakat dan prestasi bidang ini;</li>
            <li>Mempersiapkan peserta didik mengikuti perlombaan.</li>
        </ul>
        
        <h2 class="section-title">D. Sasaran</h2>
        <p class="no-indent">Sasaran utama kegiatan ekstrakurikuler ini adalah peserta didik kelas IV, V, dan VI <?php echo htmlspecialchars($sekolah_nama); ?> yang memiliki minat, bakat, serta komitmen tinggi dalam kegiatan ekstrakurikuler ini.</p>
    </div>

    <!-- PAGE 7: BAB II VISI, MISI DAN TARGET -->
    <div class="page">
        <div class="num-bottom-center">3</div>
        <h1 class="chapter-title">BAB II<br>VISI, MISI DAN TARGET</h1>
        
        <h2 class="section-title">A. Visi</h2>
        <p>Terwujudnya peserta didik yang sehat, disiplin, sportif, dan berprestasi melalui kegiatan ekstrakurikuler mini.</p>
        
        <h2 class="section-title">B. Misi</h2>
        <ol style="margin-left: 0.5cm;">
            <li>Menyelenggarakan latihan yang terprogram dan berkelanjutan;</li>
            <li>Menumbuhkan semangat kompetisi yang sehat;</li>
            <li>Meningkatkan kemampuan teknik dan fisik peserta didik;</li>
            <li>Membentuk karakter sportif dan bertanggung jawab.</li>
        </ol>

        <h2 class="section-title">C. Target Capaian</h2>
        <p><strong>1. Jangka Pendek:</strong></p>
        <p>Memahami aturan dasar permainan ekstrakurikuler mini serta menguasai teknik materi dasar ekstrakurikuler dengan baik.</p>
        <p><strong>2. Jangka Menengah:</strong></p>
        <p>Mampu menguasai materi lanjutan dan aktif berpartisipasi dalam pertandingan tingkat madrasah.</p>
        <p><strong>3. Jangka Panjang:</strong></p>
        <p>Meraih prestasi membanggakan pada ajang kompetisi bidang ini resmi tingkat kecamatan maupun kabupaten.</p>
    </div>

    <!-- PAGE 8: BAB III PROGRAM LATIHAN (GANJIL) -->
    <div class="page">
        <div class="num-bottom-center">4</div>
        <h1 class="chapter-title">BAB III<br>PROGRAM LATIHAN</h1>
        
        <h2 class="section-title">A. Program Latihan Semester Ganjil</h2>
        <table class="formal-table" style="font-size: 9.5pt; line-height: 1.3;">
            <thead>
                <tr>
                    <th style="width: 15%;">Pertemuan</th>
                    <th style="width: 45%;">Materi Latihan</th>
                    <th style="width: 40%;">Target Kompetensi</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Pertemuan 1</td><td>Pengenalan <?php echo htmlspecialchars($nama_ekstra); ?></td><td>Memahami lapangan, net, dan ukuran ekstrakurikuler mini</td></tr>
                <tr><td>Pertemuan 2</td><td>Peraturan Dasar Permainan</td><td>Memahami aturan servis, rotasi dasar, dan perhitungan skor</td></tr>
                <tr><td>Pertemuan 3</td><td>Latihan Kebugaran Jasmani</td><td>Meningkatkan kelincahan kaki, refleks, dan kekuatan fisik dasar</td></tr>
                <tr><td>Pertemuan 4</td><td>Materi Dasar 1</td><td>Menguasai posisi kuda-kuda dan perkenaan bola pada lengan</td></tr>
                <tr><td>Pertemuan 5</td><td>Materi Dasar 1 Lanjutan</td><td>Melatih akurasi arah pantulan bola secara berpasangan</td></tr>
                <tr><td>Pertemuan 6</td><td>Materi Dasar 2</td><td>Memahami posisi jari tangan membentuk mangkuk di atas dahi</td></tr>
                <tr><td>Pertemuan 7</td><td>Materi Dasar 2 Lanjutan</td><td>Melatih ketepatan mengumpan bola tinggi untuk penyerang</td></tr>
                <tr><td>Pertemuan 8</td><td>Materi Lanjutan 1</td><td>Melatih teknik ayunan lengan dan ketepatan memukul bola</td></tr>
                <tr><td>Pertemuan 9</td><td>Materi Lanjutan 1 Lanjutan</td><td>Melatih konsistensi servis bawah agar menyeberangi net masuk lapangan</td></tr>
                <tr><td>Pertemuan 10</td><td>Materi Lanjutan 2</td><td>Mengenal teknik perkenaan bola saat melompat/melempar bola atas</td></tr>
                <tr><td>Pertemuan 11</td><td>Kombinasi Passing dan Servis</td><td>Latihan menerima servis lawan lalu dioper ke pengumpan</td></tr>
                <tr><td>Pertemuan 12</td><td>Rotasi Pemain</td><td>Dapat berpindah posisi searah jarum jam saat memenangkan servis</td></tr>
                <tr><td>Pertemuan 13</td><td>Kerja Sama Tim</td><td>Latihan koordinasi komunikasi antar pemain di dalam lapangan</td></tr>
                <tr><td>Pertemuan 14</td><td>Simulasi Pertandingan</td><td>Menerapkan seluruh aturan formal dalam tanding internal</td></tr>
                <tr><td>Pertemuan 15</td><td>Turnamen Internal</td><td>Menguji mental bertanding secara berkelompok antar regu</td></tr>
                <tr><td>Pertemuan 16</td><td>Evaluasi Semester</td><td>Penilaian kelulusan kompetensi dasar fisik & teknik</td></tr>
            </tbody>
        </table>
    </div>

    <!-- PAGE 9: BAB III PROGRAM LATIHAN (GENAP) -->
    <div class="page">
        <div class="num-top-right">5</div>
        
        <h2 class="section-title" style="margin-top: 0;">B. Program Latihan Semester Genap</h2>
        <table class="formal-table" style="font-size: 9.5pt; line-height: 1.3;">
            <thead>
                <tr>
                    <th style="width: 15%;">Pertemuan</th>
                    <th style="width: 45%;">Materi Latihan</th>
                    <th style="width: 40%;">Target Kompetensi</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Pertemuan 17</td><td>Penguatan Teknik Dasar</td><td>Penyegaran teknik passing bawah, atas, dan servis</td></tr>
                <tr><td>Pertemuan 18</td><td>Praktik Keterampilan Dasar</td><td>Latihan langkah awalan, lompatan, dan ayunan tangan memukul</td></tr>
                <tr><td>Pertemuan 19</td><td>Smash Lanjutan</td><td>Melatih ketepatan memukul bola lambung di atas net dengan keras</td></tr>
                <tr><td>Pertemuan 20</td><td>Praktik Keterampilan Lanjutan</td><td>Melatih penempatan posisi tangan di atas net untuk menahan smash</td></tr>
                <tr><td>Pertemuan 21</td><td>Kombinasi Teknik</td><td>Latihan alur permainan lengkap: Terima Bola - Umpan - Smash</td></tr>
                <tr><td>Pertemuan 22</td><td>Strategi Permainan</td><td>Mengatur pertahanan dari serangan smash lawan</td></tr>
                <tr><td>Pertemuan 23</td><td>Formasi Tim</td><td>Menyusun penempatan pemain sesuai keahlian (tosser/spiker)</td></tr>
                <tr><td>Pertemuan 24</td><td>Latihan Fisik</td><td>Meningkatkan lompatan tegak (*vertical jump*) dan kecepatan</td></tr>
                <tr><td>Pertemuan 25</td><td>Simulasi Kompetisi</td><td>Tanding dengan simulasi sistem gugur</td></tr>
                <tr><td>Pertemuan 26</td><td>Sparing Internal</td><td>Latihan tanding antar tim inti madrasah</td></tr>
                <tr><td>Pertemuan 27</td><td>Sparing Antar Sekolah</td><td>Mengukur kemampuan tim dengan bertanding melawan sekolah lain</td></tr>
                <tr><td>Pertemuan 28</td><td>Persiapan Lomba</td><td>Pemantapan mental juara dan koordinasi akhir formasi tim</td></tr>
                <tr><td>Pertemuan 29</td><td>Seleksi Tim</td><td>Penetapan daftar pemain inti untuk dikirim ke perlombaan</td></tr>
                <tr><td>Pertemuan 30</td><td>Uji Kemampuan</td><td>Ujian akhir praktik kemampuan teknik individu</td></tr>
                <tr><td>Pertemuan 31</td><td>Turnamen Internal</td><td>Turnamen penutup tahun pelajaran sebagai apresiasi latihan</td></tr>
                <tr><td>Pertemuan 32</td><td>Evaluasi Tahunan</td><td>Penilaian rapor ekstrakurikuler serta penyusunan laporan</td></tr>
            </tbody>
        </table>
    </div>

    <!-- PAGE 10: BAB IV & BAB V -->
    <div class="page">
        <div class="num-bottom-center">6</div>
        <h1 class="chapter-title">BAB IV &amp; BAB V<br>METODE LATIHAN DAN SARANA PRASARANA</h1>
        
        <h2 class="section-title">A. Metode Latihan</h2>
        <p class="no-indent">Metodologi yang digunakan pembina dalam mendidik peserta didik ekstrakurikuler mini meliputi:</p>
        <ul style="margin-left: 0.5cm;">
            <li><strong>Ceramah dan Demonstrasi:</strong> Menjelaskan teori dasar dan memperagakan gerakan tubuh secara langsung;</li>
            <li><strong>Latihan Individu:</strong> Latihan mandiri untuk meningkatkan memori otot perorangan;</li>
            <li><strong>Latihan Berpasangan:</strong> Melatih refleks kontrol arah bola bolak-balik;</li>
            <li><strong>Latihan Kelompok:</strong> Mengatur pola koordinasi formasi taktis tim;</li>
            <li><strong>Simulasi Pertandingan:</strong> Melatih mental di bawah tekanan atmosfer kompetisi;</li>
            <li><strong>Evaluasi Berkala:</strong> Memberikan koreksi performa individu di tiap akhir pekan latihan.</li>
        </ul>

        <h2 class="section-title">B. Sarana dan Prasarana Pendukung</h2>
        <table class="formal-table">
            <thead>
                <tr>
                    <th style="width: 10%; text-align:center;">No</th>
                    <th style="width: 50%;">Sarana / Prasarana</th>
                    <th style="width: 40%;">Status Ketersediaan</th>
                </tr>
            </thead>
            <tbody>
                <tr><td style="text-align:center;">1</td><td><?php echo htmlspecialchars($nama_ekstra); ?> (Ukuran 4)</td><td>Ada / Layak Pakai</td></tr>
                <tr><td style="text-align:center;">2</td><td>Peralatan Pendukung 1</td><td>Ada / Baik</td></tr>
                <tr><td style="text-align:center;">3</td><td>Peralatan Pendukung 2</td><td>Ada / Kokoh</td></tr>
                <tr><td style="text-align:center;">4</td><td>Lapangan <?php echo htmlspecialchars($nama_ekstra); ?> Out-door</td><td>Ada / Memadai</td></tr>
                <tr><td style="text-align:center;">5</td><td>Peluit Pembina</td><td>Ada / Baik</td></tr>
                <tr><td style="text-align:center;">6</td><td>Rompi Latihan Tim (2 warna berbeda)</td><td>Ada / Cukup</td></tr>
                <tr><td style="text-align:center;">7</td><td>Papan Skor Manual</td><td>Ada / Baik</td></tr>
                <tr><td style="text-align:center;">8</td><td>Kotak Obat Pertolongan Pertama (P3K)</td><td>Ada / Lengkap</td></tr>
            </tbody>
        </table>
    </div>

    <!-- PAGE 11: BAB VI & VII -->
    <div class="page">
        <div class="num-bottom-center">7</div>
        <h1 class="chapter-title">BAB VI &amp; BAB VII<br>PENILAIAN DAN RENCANA PRESTASI</h1>
        
        <h2 class="section-title">A. Aspek Penilaian</h2>
        <p class="no-indent">Evaluasi kelulusan pembinaan ekstrakurikuler dinilai dari 3 aspek utama:</p>
        <ol style="margin-left: 0.5cm;">
            <li><strong>Aspek Sikap:</strong> Kedisiplinan waktu latihan, kepatuhan instruksi pembina, tanggung jawab merawat alat, serta sportivitas tanding.</li>
            <li><strong>Aspek Keterampilan:</strong> Penguasaan praktik passing bawah, passing atas, kualitas pukulan servis, serta akurasi smash & block dasar.</li>
            <li><strong>Aspek Kehadiran:</strong> Persentase kehadiran kehadiran minimum 75% dari total latihan rutin mingguan.</li>
        </ol>

        <h2 class="section-title">B. Program Kejuaraan dan Rencana Prestasi</h2>
        <p class="no-indent">Guna menyalurkan bakat kompetitif, tim utama madrasah diproyeksikan untuk ikut serta dalam:</p>
        <ul style="margin-left: 0.5cm;">
            <li>Olimpiade Olahraga Siswa Nasional (O2SN) jenjang sekolah dasar/madrasah ibtidaiyah;</li>
            <li>Turnamen persahabatan antar sekolah dasar se-Kecamatan Way Tenong;</li>
            <li>Pekan Olahraga Kecamatan (Porcam) Lampung Barat;</li>
            <li>Kompetisi ekstrakurikuler undangan resmi yang diselenggarakan instansi terkait.</li>
        </ul>
    </div>

    <!-- PAGE 12: BAB VIII PENUTUP -->
    <div class="page">
        <div class="num-bottom-center">8</div>
        <h1 class="chapter-title">BAB VIII<br>PENUTUP</h1>
        
        <p class="no-indent">Program Pembinaan Ekstrakurikuler <?php echo htmlspecialchars($nama_ekstra); ?> ini disusun sebagai pedoman pelaksanaan kegiatan ekstrakurikuler di <?php echo htmlspecialchars($sekolah_nama); ?>. Keberhasilan pelaksanaan program latihan ini sangat bergantung pada komitmen aktif dari peserta didik, dukungan fasilitas dari madrasah, serta kerja sama yang sinergis dari pembina dan wali murid.</p>
        <p>Melalui perencanaan latihan yang matang, diharapkan dapat terbentuk generasi muda madrasah yang memiliki kebugaran fisik prima, karakter sportivitas yang kokoh, berintegritas tinggi, serta mampu mengharumkan nama baik madrasah melalui prestasi gemilang di bidang bidang ini.</p>
        
        <div style="margin-top: 3cm; text-align: right; padding-right: 1.5cm;">
            Tambak Jaya, <?php echo date('d F Y'); ?><br><br>
            <strong>Tim Pembina</strong>
        </div>
    </div>
<?php endif; ?>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>

