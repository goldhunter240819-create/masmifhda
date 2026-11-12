<?php
require_once 'koneksi.php';

// Data Institusi
$nama_madrasah = 'Madrasah Aliyah Miftahul Huda (MAS Mifhda)';
$nsm = '131218040003';
$npsn = '10816858';
$alamat = 'Margorahayu, Ds. Tambak Jaya, Kec. Way Tenong, Kab. Lampung Barat, Lampung';
$website = 'apk.masmifhda.sch.id';
$email = 'masmiftahulhuda@gmail.com';
$kamad_nama = 'Drs. Hamim Hasyim';
$kamad_nip = '-';
$versi_apk = 'v26.2.2 (Build 2026.11)';

$favicon_url = '/public/uploads/logo/logo_1781854511.png';
if (isset($core_db)) {
    try {
        $stmt_inst = $core_db->query("SELECT * FROM institusi LIMIT 1");
        $inst = $stmt_inst->fetch(PDO::FETCH_ASSOC);
        if ($inst) {
            if (!empty($inst['nama'])) $nama_madrasah = $inst['nama'];
            if (!empty($inst['nsm'])) $nsm = $inst['nsm'];
            if (!empty($inst['npsn'])) $npsn = $inst['npsn'];
            if (!empty($inst['alamat'])) $alamat = $inst['alamat'];
            if (!empty($inst['logo'])) $favicon_url = '/public/uploads/logo/' . $inst['logo'];
        }
    } catch(Exception $e) {}
}

$back_url = 'guru_dashboard.php';
if (!isset($_SESSION['guru_id']) && isset($_SESSION['siswa_id'])) {
    $back_url = 'aplikasi_siswa.php';
} elseif (!isset($_SESSION['guru_id']) && !isset($_SESSION['siswa_id'])) {
    $back_url = 'login.php';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Buku Panduan Penggunaan Aplikasi Mobile - <?= htmlspecialchars($nama_madrasah) ?></title>
    <link rel="shortcut icon" href="<?= htmlspecialchars($favicon_url) ?>" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,700;0,900;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #0284c7;
            --primary-dark: #0369a1;
            --primary-light: #e0f2fe;
            --secondary: #059669;
            --accent: #f59e0b;
            --dark: #0f172a;
            --gray-text: #334155;
            --gray-sub: #64748b;
            --border: #e2e8f0;
            --bg-page: #f8fafc;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #475569;
            color: var(--gray-text);
            line-height: 1.6;
            font-size: 14px;
        }

        /* Top Floating Navbar (Hanya Tampil di Layar) */
        .top-navbar {
            position: sticky;
            top: 0;
            z-index: 9999;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(8px);
            color: white;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .top-navbar a.btn-nav {
            color: #e2e8f0;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 8px;
            background: rgba(255,255,255,0.1);
            transition: 0.2s;
        }
        .top-navbar a.btn-nav:hover {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .top-navbar .btn-print {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(16,185,129,0.35);
            transition: all 0.2s;
        }
        .top-navbar .btn-print:hover {
            transform: translateY(-1px);
            filter: brightness(1.1);
        }

        /* Container Dokumen A4 */
        .doc-wrapper {
            max-width: 860px;
            margin: 25px auto;
            background: white;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            border-radius: 12px;
            overflow: hidden;
        }

        .page-sheet {
            padding: 50px 60px;
            background: white;
            position: relative;
        }

        /* COVER PAGE */
        .cover-page {
            min-height: 1050px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 45%, #eff6ff 100%);
            border-bottom: 3px double #cbd5e1;
            position: relative;
            overflow: hidden;
        }
        .cover-decor {
            position: absolute;
            top: -150px;
            right: -150px;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(2,132,199,0.15) 0%, rgba(2,132,199,0) 70%);
        }
        .cover-decor-2 {
            position: absolute;
            bottom: -100px;
            left: -100px;
            width: 350px;
            height: 350px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(5,150,105,0.15) 0%, rgba(5,150,105,0) 70%);
        }
        .cover-header {
            text-align: center;
            position: relative;
            z-index: 2;
        }
        .cover-title-box {
            text-align: center;
            margin: 40px 0;
            position: relative;
            z-index: 2;
        }
        .badge-edition {
            display: inline-block;
            background: #0284c7;
            color: white;
            padding: 6px 16px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .cover-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 2.3rem;
            font-weight: 900;
            color: #0f172a;
            margin: 0 0 12px 0;
            line-height: 1.25;
            text-transform: uppercase;
        }
        .cover-subtitle {
            font-size: 1.15rem;
            color: #0369a1;
            font-weight: 700;
            margin: 0;
            line-height: 1.4;
        }
        .cover-desc {
            font-size: 0.92rem;
            color: var(--gray-sub);
            max-width: 600px;
            margin: 15px auto 0;
        }
        .cover-mockup {
            text-align: center;
            margin: 20px 0;
            display: flex;
            justify-content: center;
            gap: 20px;
            position: relative;
            z-index: 2;
        }
        .phone-frame {
            width: 170px;
            border-radius: 20px;
            border: 5px solid #1e293b;
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
            overflow: hidden;
            background: #fff;
        }
        .phone-frame img {
            width: 100%;
            height: auto;
            display: block;
        }
        .cover-footer {
            border-top: 2px solid #e2e8f0;
            padding-top: 20px;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        /* PENGESAHAN */
        .pengesahan-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
        }

        /* DAFTAR ISI */
        .toc-list {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }
        .toc-item {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dotted #cbd5e1;
            font-size: 0.95rem;
        }
        .toc-title {
            font-weight: 700;
            color: #0f172a;
        }
        .toc-sub {
            padding-left: 20px;
            font-weight: 500;
            color: #475569;
            font-size: 0.88rem;
        }

        /* BAB & KONTEN */
        .chapter-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
            border-left: 6px solid var(--primary);
            padding-left: 15px;
            margin: 40px 0 20px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .subchapter-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0369a1;
            margin: 25px 0 12px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .step-box {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px;
        }
        .step-num {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        .step-content {
            flex: 1;
        }
        .step-content h4 {
            margin: 0 0 4px 0;
            font-size: 0.95rem;
            color: #0f172a;
            font-weight: 700;
        }
        .step-content p {
            margin: 0;
            font-size: 0.86rem;
            color: #475569;
            line-height: 1.5;
        }

        /* Gambar Ilustrasi / Screenshot */
        .figure-box {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        .figure-box img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: 1px solid #cbd5e1;
            display: inline-block;
        }
        .figure-caption {
            font-size: 0.82rem;
            color: #64748b;
            font-weight: 600;
            margin-top: 10px;
            font-style: italic;
        }

        .badge-tag {
            display: inline-block;
            background: #e0f2fe;
            color: #0284c7;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 0.76rem;
            font-weight: 700;
        }

        .alert-box {
            background: #eff6ff;
            border-left: 4px solid #0284c7;
            padding: 12px 16px;
            border-radius: 0 8px 8px 0;
            margin: 15px 0;
            font-size: 0.88rem;
        }
        .alert-box.success {
            background: #ecfdf5;
            border-color: #059669;
            color: #065f46;
        }
        .alert-box.warning {
            background: #fffbeb;
            border-color: #f59e0b;
            color: #92400e;
        }

        /* Table */
        .doc-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 0.86rem;
        }
        .doc-table th, .doc-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            text-align: left;
        }
        .doc-table th {
            background: #f1f5f9;
            font-weight: 700;
            color: #0f172a;
        }

        /* PRINT MEDIA QUERIES */
        @media print {
            body {
                background: white;
            }
            .top-navbar {
                display: none !important;
            }
            .doc-wrapper {
                max-width: 100% !important;
                margin: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
            .page-sheet {
                padding: 10mm 15mm !important;
            }
            .page-break {
                page-break-before: always;
                break-before: page;
            }
            .cover-page {
                min-height: 100vh !important;
            }
            @page {
                size: A4 portrait;
                margin: 12mm 15mm 15mm 15mm;
            }
        }
    </style>
</head>
<body>

<!-- TOP FLOATING NAVBAR -->
<div class="top-navbar">
    <div style="display: flex; align-items: center; gap: 15px;">
        <a href="<?= htmlspecialchars($back_url) ?>" class="btn-nav">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Aplikasi
        </a>
        <div style="display: flex; flex-direction: column;">
            <span style="font-weight: 700; font-size: 0.95rem; line-height: 1.2;">Buku Panduan Aplikasi Mobile</span>
            <span style="font-size: 0.72rem; color: #94a3b8;"><?= htmlspecialchars($nama_madrasah) ?></span>
        </div>
    </div>
    <div style="display: flex; align-items: center; gap: 10px;">
        <button onclick="window.print()" class="btn-print">
            <i class="fa-solid fa-file-pdf"></i> Unduh / Cetak PDF (A4)
        </button>
    </div>
</div>

<div class="doc-wrapper">

    <!-- ================= HALAMAN 1: COVER RESMI ================= -->
    <div class="page-sheet cover-page">
        <div class="cover-decor"></div>
        <div class="cover-decor-2"></div>

        <div class="cover-header">
            <div style="display: flex; align-items: center; justify-content: center; gap: 20px; margin-bottom: 15px;">
                <img src="/public/logo_kemenag.png" alt="Logo Kemenag" style="height: 65px; object-fit: contain;" onerror="this.style.display='none'">
                <img src="<?= htmlspecialchars($favicon_url) ?>" alt="Logo Madrasah" style="height: 70px; object-fit: contain;">
            </div>
            <div style="font-size: 0.85rem; font-weight: 800; color: #047857; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 4px;">
                KEMENTERIAN AGAMA REPUBLIK INDONESIA
            </div>
            <div style="font-size: 1.05rem; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 1px;">
                <?= htmlspecialchars($nama_madrasah) ?>
            </div>
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;">
                NSM: <?= htmlspecialchars($nsm) ?> &bull; NPSN: <?= htmlspecialchars($npsn) ?>
            </div>
        </div>

        <div class="cover-title-box">
            <div class="badge-edition">Edisi Resmi &bull; TP 2026/2027</div>
            <h1 class="cover-title">
                BUKU PANDUAN PENGGUNAAN<br>APLIKASI MOBILE (APK)
            </h1>
            <p class="cover-subtitle">
                User Manual Portal Digital Guru, Tenaga Kependidikan & Siswa (Aula PWA)
            </p>
            <p class="cover-desc">
                Panduan komprehensif operasional fitur presensi QR, jurnal KBM mengajar, input nilai harian, manajemen berkas kepegawaian, kasir keuangan madrasah, dan hub aplikasi delegasi.
            </p>
        </div>

        <!-- Visual Preview Frame Mockup -->
        <div class="cover-mockup">
            <div class="phone-frame">
                <img src="/public/assets/panduan/panduan_dashboard_guru.jpg" alt="Preview Dashboard Guru">
            </div>
            <div class="phone-frame" style="transform: translateY(15px);">
                <img src="/public/assets/panduan/panduan_aplikasi_saya.jpg" alt="Preview Aplikasi Saya">
            </div>
        </div>

        <div class="cover-footer">
            <div style="font-size: 0.85rem; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 1px;">
                PUSAT DATA & INFORMASI DIGITAL MADRASAH
            </div>
            <div style="font-size: 0.78rem; color: #64748b; margin-top: 4px;">
                <?= htmlspecialchars($alamat) ?><br>
                Portal Sistem: <strong><?= htmlspecialchars($website) ?></strong> &bull; Versi Dokumen: <?= htmlspecialchars($versi_apk) ?>
            </div>
        </div>
    </div>

    <!-- ================= HALAMAN 2: LEMBAR PENGESAHAN & DAFTAR ISI ================= -->
    <div class="page-sheet page-break">
        <h2 style="text-align: center; font-size: 1.4rem; color: #0f172a; margin-top: 0; text-transform: uppercase; letter-spacing: 0.5px;">
            LEMBAR PENGESAHAN DOKUMEN
        </h2>
        <div style="width: 60px; height: 3px; background: #0284c7; margin: 10px auto 25px auto;"></div>

        <p style="text-align: justify; font-size: 0.9rem; line-height: 1.7; color: #334155;">
            Buku Panduan Operasional Aplikasi Mobile (User Manual Book) <strong><?= htmlspecialchars($nama_madrasah) ?></strong> ini disusun sebagai pedoman resmi tata cara penggunaan platform digital sekolah bagi seluruh Dewan Guru, Tenaga Kependidikan, Wali Kelas, Bendahara, Pembina, dan Siswa untuk Tahun Ajaran 2026/2027.
        </p>

        <div class="pengesahan-box">
            <table style="width: 100%; border: none; font-size: 0.88rem;">
                <tr>
                    <td style="width: 160px; font-weight: 700; color: #475569;">Nama Aplikasi</td>
                    <td style="width: 15px;">:</td>
                    <td><strong>Aula Mobile Portal (MAS Mifhda Digital Platform)</strong></td>
                </tr>
                <tr>
                    <td style="font-weight: 700; color: #475569;">Teknologi</td>
                    <td>:</td>
                    <td>Progressive Web Application (PWA) &bull; Standar Cloud Kemenag</td>
                </tr>
                <tr>
                    <td style="font-weight: 700; color: #475569;">Alamat Akses</td>
                    <td>:</td>
                    <td><a href="https://<?= htmlspecialchars($website) ?>/apk" style="color: #0284c7; font-weight: 700;">https://<?= htmlspecialchars($website) ?>/apk</a></td>
                </tr>
                <tr>
                    <td style="font-weight: 700; color: #475569;">Penyusun</td>
                    <td>:</td>
                    <td>Tim Pengembang IT & Waka Kurikulum <?= htmlspecialchars($nama_madrasah) ?></td>
                </tr>
                <tr>
                    <td style="font-weight: 700; color: #475569;">Tanggal Ditetapkan</td>
                    <td>:</td>
                    <td>Lampung Barat, 12 November 2026</td>
                </tr>
            </table>
        </div>

        <!-- Tanda Tangan -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 40px; text-align: center;">
            <div>
                <div style="font-size: 0.85rem; color: #64748b;">Mengetahui / Mengesahkan:</div>
                <div style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin-top: 4px;">Kepala Madrasah</div>
                <div style="height: 75px; display: flex; align-items: center; justify-content: center;">
                    <img src="/public/stempelma.png" alt="Stempel" style="height: 65px; opacity: 0.85;" onerror="this.style.display='none'">
                    <img src="/public/ttdkamad.png" alt="TTD" style="height: 60px; margin-left: -40px;" onerror="this.style.display='none'">
                </div>
                <div style="font-size: 0.95rem; font-weight: 800; color: #0f172a; text-decoration: underline;"><?= htmlspecialchars($kamad_nama) ?></div>
                <div style="font-size: 0.8rem; color: #64748b;">NIP. <?= htmlspecialchars($kamad_nip) ?></div>
            </div>
            <div>
                <div style="font-size: 0.85rem; color: #64748b;">Penyusun Dokumen:</div>
                <div style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin-top: 4px;">Waka Kurikulum / Tim IT</div>
                <div style="height: 75px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-signature" style="font-size: 2.5rem; color: #cbd5e1;"></i>
                </div>
                <div style="font-size: 0.95rem; font-weight: 800; color: #0f172a; text-decoration: underline;">M AZIZ SULAIMAN, S.Pd</div>
                <div style="font-size: 0.8rem; color: #64748b;">NIP. 1804072704000002</div>
            </div>
        </div>

        <div style="margin-top: 45px;">
            <h3 style="font-size: 1.15rem; color: #0f172a; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px;">
                <i class="fa-solid fa-list-ol" style="color: #0284c7; margin-right: 8px;"></i> DAFTAR ISI MANUAL BOOK
            </h3>
            <ul class="toc-list">
                <li class="toc-item"><span class="toc-title">BAB I &bull; PENDAHULUAN & CARA AKSES APLIKASI</span> <span>Hal. 3</span></li>
                <li class="toc-item"><span class="toc-sub">1.1 Pengenalan Teknologi PWA (Progressive Web App)</span> <span>Hal. 3</span></li>
                <li class="toc-item"><span class="toc-sub">1.2 Cara Install ke Layar Utama HP (Add to Home Screen)</span> <span>Hal. 3</span></li>
                <li class="toc-item"><span class="toc-sub">1.3 Login Akun & Fitur Ganti Password</span> <span>Hal. 3</span></li>
                <li class="toc-item"><span class="toc-title">BAB II &bull; DASHBOARD BERANDA GURU</span> <span>Hal. 4</span></li>
                <li class="toc-item"><span class="toc-sub">2.1 Anatomi Antarmuka Beranda Guru</span> <span>Hal. 4</span></li>
                <li class="toc-item"><span class="toc-sub">2.2 Kartu Ringkasan Aktivitas (Absensi Datang & Pulang)</span> <span>Hal. 4</span></li>
                <li class="toc-item"><span class="toc-sub">2.3 Menu Jadwal Mengajar & Media Sosial Madrasah</span> <span>Hal. 4</span></li>
                <li class="toc-item"><span class="toc-sub">2.4 Navigasi Bar Menu Bawah (5 Menu Utama)</span> <span>Hal. 5</span></li>
                <li class="toc-item"><span class="toc-title">BAB III &bull; JURNAL MENGAJAR DIGITAL (KBM)</span> <span>Hal. 6</span></li>
                <li class="toc-item"><span class="toc-sub">3.1 Pengisian Jurnal Mengajar Harian</span> <span>Hal. 6</span></li>
                <li class="toc-item"><span class="toc-sub">3.2 Pencatatan Kehadiran Siswa di Kelas & Foto KBM</span> <span>Hal. 6</span></li>
                <li class="toc-item"><span class="toc-title">BAB IV &bull; HUB APLIKASI SAYA (ADMIN DELEGASI)</span> <span>Hal. 7</span></li>
                <li class="toc-item"><span class="toc-sub">4.1 Modul Akademik (Rekap Absen, Nilai Harian, E-Library)</span> <span>Hal. 7</span></li>
                <li class="toc-item"><span class="toc-sub">4.2 Modul Keuangan (Kasir SPP, Tabungan, BKU Bendahara, BOS)</span> <span>Hal. 8</span></li>
                <li class="toc-item"><span class="toc-sub">4.3 Modul Absensi & Perizinan (Scanner Siswa & Guru)</span> <span>Hal. 8</span></li>
                <li class="toc-item"><span class="toc-title">BAB V &bull; MANAJEMEN BERKAS GURU & PROFIL</span> <span>Hal. 9</span></li>
                <li class="toc-item"><span class="toc-title">BAB VI &bull; PUSAT BANTUAN & PEMECAHAN MASALAH (FAQ)</span> <span>Hal. 10</span></li>
            </ul>
        </div>
    </div>

    <!-- ================= HALAMAN 3: BAB I PENDAHULUAN & AKSES ================= -->
    <div class="page-sheet page-break">
        <h2 class="chapter-title">
            <span>BAB I</span> PENDAHULUAN & CARA AKSES APLIKASI
        </h2>

        <p style="text-align: justify;">
            Aplikasi Mobile <strong><?= htmlspecialchars($nama_madrasah) ?></strong> dibangun berbasis teknologi <em>Progressive Web Application</em> (PWA). Sistem ini memberikan kecepatan akses seperti aplikasi native Android/iOS tanpa perlu mendownload file APK besar melalui Google Play Store.
        </p>

        <h3 class="subchapter-title"><i class="fa-solid fa-mobile-screen-button"></i> 1.1 Cara Membuka & Menginstall ke Layar Utama HP</h3>
        <p>Aplikasi dapat diinstall hanya dalam 3 langkah mudah:</p>

        <div class="step-box">
            <div class="step-num">1</div>
            <div class="step-content">
                <h4>Buka Browser Chrome / Safari di HP</h4>
                <p>Ketikkan alamat resmi portal pada browser smartphone Anda: <strong>https://<?= htmlspecialchars($website) ?>/apk</strong></p>
            </div>
        </div>

        <div class="step-box">
            <div class="step-num">2</div>
            <div class="step-content">
                <h4>Pilih "Tambahkan ke Layar Utama" (Add to Home Screen)</h4>
                <p>Pada browser Google Chrome (Android), ketuk menu titik tiga [⋮] di kanan atas, lalu pilih <strong>"Tambahkan ke Layar Utama"</strong> atau <strong>"Install Aplikasi"</strong>. Untuk pengguna iPhone (Safari), ketuk tombol Share [↑] lalu pilih <strong>"Add to Home Screen"</strong>.</p>
            </div>
        </div>

        <div class="step-box">
            <div class="step-num">3</div>
            <div class="step-content">
                <h4>Buka Icon Aplikasi di HP Anda</h4>
                <p>Icon MAS Mifhda akan otomatis muncul di layar utama smartphone Anda. Anda kini dapat membukanya secara instan dengan tampilan layar penuh (full-screen).</p>
            </div>
        </div>

        <h3 class="subchapter-title"><i class="fa-solid fa-key"></i> 1.2 Cara Login Akun Guru & Tendik</h3>
        <p>Setiap pendidik dan tenaga kependidikan telah diberikan kredensial resmi:</p>
        <ul style="line-height: 1.8;">
            <li><strong>Username / NIP:</strong> Masukkan NIP, NUPTK, atau Nama Pengguna yang telah didaftarkan oleh Admin Madrasah.</li>
            <li><strong>Password:</strong> Masukkan kata sandi akun Anda (default: nomor kontak/kombinasi resmi dari waka).</li>
            <li><strong>Ingat Saya (Auto-Login):</strong> Centang opsi agar sesi tetap aktif dan tidak perlu login berulang kali setiap membuka aplikasi.</li>
        </ul>

        <div class="alert-box warning">
            <i class="fa-solid fa-triangle-exclamation"></i> <strong>Penting:</strong> Demi keamanan data madrasah, segera lakukan perubahan password setelah Anda pertama kali berhasil login melalui menu <strong>Profil &rarr; Ubah Password</strong>.
        </div>
    </div>

    <!-- ================= HALAMAN 4: BAB II DASHBOARD BERANDA GURU ================= -->
    <div class="page-sheet page-break">
        <h2 class="chapter-title">
            <span>BAB II</span> DASHBOARD BERANDA GURU
        </h2>

        <p style="text-align: justify;">
            Halaman Beranda Guru (`guru_dashboard.php`) adalah pusat kendali aktivitas harian pendidik. Di halaman ini, guru dapat melihat status presensi, statistik mengajar, jadwal pelajaran hari ini, dan pengumuman madrasah.
        </p>

        <!-- Figure Screenshot Dashboard -->
        <div class="figure-box">
            <img src="/public/assets/panduan/panduan_dashboard_guru.jpg" alt="Tampilan Dashboard Guru" style="max-height: 380px;">
            <div class="figure-caption">Gambar 2.1: Antarmuka Dashboard Guru pada Portal Mobile MAS Mifhda</div>
        </div>

        <h3 class="subchapter-title"><i class="fa-solid fa-table-cells"></i> 2.1 Penjelasan Komponen Antarmuka Beranda</h3>
        <table class="doc-table">
            <thead>
                <tr>
                    <th style="width: 35%;">Bagian Antarmuka</th>
                    <th>Fungsi & Penjelasan Detail</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>1. Header Profil & Keamanan</strong></td>
                    <td>Menampilkan nama lengkap guru yang sedang aktif, foto profil, tanggal sistem hari ini, serta indikator keamanan bertuliskan <em>"Akses Data Terenkripsi Aman"</em>.</td>
                </tr>
                <tr>
                    <td><strong>2. Kartu Absen Hari Ini</strong></td>
                    <td>Indikator status presensi guru hari ini. Jika belum absen, kartu berwarna oranye <strong>"Belum Absen"</strong>. Jika sudah melakukan scan QR, kartu otomatis berubah hijau <strong>"Sudah Absen"</strong> disertai jam datang dan jam pulang.</td>
                </tr>
                <tr>
                    <td><strong>3. Statistik Kehadiran Bulanan</strong></td>
                    <td>Menampilkan akumulasi jumlah hari kerja Anda hadir di madrasah pada bulan berjalan (contoh: <em>18 hari Hadir Bulan Ini</em>).</td>
                </tr>
                <tr>
                    <td><strong>4. Indikator Isi Jurnal</strong></td>
                    <td>Menghitung berapa kali Anda telah mencatatkan agenda Kegiatan Belajar Mengajar (KBM) di kelas pada bulan ini.</td>
                </tr>
                <tr>
                    <td><strong>5. Tombol "Lihat Jadwal Mengajar"</strong></td>
                    <td>Membuka modal pop-up jadwal pelajaran interaktif Anda untuk hari ini dan hari berikutnya (lengkap dengan jam mulai, mata pelajaran, dan kelas).</td>
                </tr>
                <tr>
                    <td><strong>6. Berita & Media Sosial Madrasah</strong></td>
                    <td>Tautan langsung menuju berita resmi website madrasah serta kanal media sosial resmi MAS Mifhda (Instagram, TikTok, Facebook, YouTube, Threads).</td>
                </tr>
            </tbody>
        </table>

        <h3 class="subchapter-title"><i class="fa-solid fa-compass"></i> 2.2 Navigasi 5 Menu Bawah (Bottom Bar)</h3>
        <p>Di bagian bawah layar selalu tersedia 5 tombol navigasi utama:</p>
        <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; text-align: center; margin-top: 10px;">
            <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 5px;">
                <i class="fa-solid fa-house" style="color: #0284c7; font-size: 1.2rem;"></i>
                <div style="font-weight: 700; font-size: 0.75rem; margin-top: 4px;">Beranda</div>
            </div>
            <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 5px;">
                <i class="fa-solid fa-book-open" style="color: #059669; font-size: 1.2rem;"></i>
                <div style="font-weight: 700; font-size: 0.75rem; margin-top: 4px;">Jurnal</div>
            </div>
            <div style="background: #e0f2fe; border: 2px solid #0284c7; border-radius: 8px; padding: 10px 5px;">
                <i class="fa-solid fa-layer-group" style="color: #0284c7; font-size: 1.2rem;"></i>
                <div style="font-weight: 800; font-size: 0.75rem; margin-top: 4px; color: #0369a1;">Aplikasi</div>
            </div>
            <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 5px;">
                <i class="fa-solid fa-folder-open" style="color: #f59e0b; font-size: 1.2rem;"></i>
                <div style="font-weight: 700; font-size: 0.75rem; margin-top: 4px;">Berkas</div>
            </div>
            <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 5px;">
                <i class="fa-solid fa-user" style="color: #64748b; font-size: 1.2rem;"></i>
                <div style="font-weight: 700; font-size: 0.75rem; margin-top: 4px;">Profil</div>
            </div>
        </div>
    </div>

    <!-- ================= HALAMAN 5: BAB III JURNAL MENGAJAR DIGITAL ================= -->
    <div class="page-sheet page-break">
        <h2 class="chapter-title">
            <span>BAB III</span> JURNAL MENGAJAR DIGITAL (KBM)
        </h2>

        <p style="text-align: justify;">
            Modul Jurnal Mengajar merupakan pengganti buku agenda fisik guru. Setiap selesai melaksanakan kegiatan pembelajaran tatap muka di kelas, guru diwajibkan mengisikan jurnal ini melalui aplikasi.
        </p>

        <h3 class="subchapter-title"><i class="fa-solid fa-pen-to-square"></i> 3.1 Langkah Pengisian Jurnal Mengajar</h3>

        <div class="step-box">
            <div class="step-num">1</div>
            <div class="step-content">
                <h4>Ketuk Menu "Jurnal" di Navigasi Bawah</h4>
                <p>Pilih tombol <strong>"Tambah Jurnal Mengajar"</strong> atau pilih jam KBM yang ada pada jadwal hari ini.</p>
            </div>
        </div>

        <div class="step-box">
            <div class="step-num">2</div>
            <div class="step-content">
                <h4>Pilih Kelas, Mapel & Jam Pelajaran</h4>
                <p>Tentukan kelas yang diajar (misal: <em>X E.1</em>), mata pelajaran (misal: <em>Informatika</em>), dan jam ke- (misal: <em>Jam 1 - 2</em>).</p>
            </div>
        </div>

        <div class="step-box">
            <div class="step-num">3</div>
            <div class="step-content">
                <h4>Isi Materi Pokok & Tujuan Pembelajaran</h4>
                <p>Tuliskan ringkasan materi materi pembelajaran yang disampaikan sesuai dengan Alur Tujuan Pembelajaran (ATP) dan modul ajar Anda.</p>
            </div>
        </div>

        <div class="step-box">
            <div class="step-num">4</div>
            <div class="step-content">
                <h4>Tandai Kehadiran Siswa di Kelas</h4>
                <p>Tandai jika ada siswa yang <strong>Sakit (S)</strong>, <strong>Izin (I)</strong>, atau <strong>Alpa (A)</strong>. Siswa yang tidak ditandai otomatis dihitung <strong>Hadir (H)</strong>.</p>
            </div>
        </div>

        <div class="step-box">
            <div class="step-num">5</div>
            <div class="step-content">
                <h4>Unggah Foto Dokumentasi KBM & Simpan</h4>
                <p>Ambil foto suasana belajar di kelas menggunakan kamera HP Anda, lalu ketuk tombol <strong>"Simpan Jurnal"</strong>.</p>
            </div>
        </div>

        <div class="alert-box success">
            <i class="fa-solid fa-circle-check"></i> <strong>Otomatis Terhubung ke Waka Kurikulum:</strong> Data jurnal yang Anda simpan langsung masuk ke dashboard monitoring Waka Kurikulum dan terhitung sebagai bukti fisik kinerja pembelajaran guru untuk supervisi akademik.
        </div>
    </div>

    <!-- ================= HALAMAN 6: BAB IV HUB APLIKASI SAYA ================= -->
    <div class="page-sheet page-break">
        <h2 class="chapter-title">
            <span>BAB IV</span> HUB APLIKASI SAYA (ADMIN DELEGASI)
        </h2>

        <p style="text-align: justify;">
            Menu <strong>"Aplikasi Saya"</strong> (`aplikasi_saya.php`) adalah pintu gerbang menuju seluruh sistem terpadu madrasah. Ikon-ikon aplikasi yang muncul disesuaikan dengan tugas tambahan dan hak akses delegasi yang diberikan kepada Anda oleh Kepala Madrasah.
        </p>

        <!-- Figure Screenshot Aplikasi Saya -->
        <div class="figure-box">
            <img src="/public/assets/panduan/panduan_aplikasi_saya.jpg" alt="Tampilan Aplikasi Saya" style="max-height: 380px;">
            <div class="figure-caption">Gambar 4.1: Daftar Grid Aplikasi Berdasarkan Kategori Akses Delegasi</div>
        </div>

        <h3 class="subchapter-title"><i class="fa-solid fa-graduation-cap"></i> 4.1 Kategori Akademik & Pribadi</h3>
        <table class="doc-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Aplikasi</th>
                    <th>Fungsi & Petunjuk Penggunaan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="badge-tag">Rekap Absen</span></td>
                    <td>Melihat riwayat kehadiran bulanan Anda sendiri, rekap jam datang, jam pulang, serta akumulasi keterlambatan.</td>
                </tr>
                <tr>
                    <td><span class="badge-tag">Nilai Harian</span></td>
                    <td>Memasukkan nilai asesmen formatif dan sumatif lingkup materi siswa per kelas dan mata pelajaran.</td>
                </tr>
                <tr>
                    <td><span class="badge-tag">Tugas Daring</span></td>
                    <td>Membuat soal penugasan online (E-Learning) yang dapat dikerjakan siswa melalui smartphone mereka.</td>
                </tr>
                <tr>
                    <td><span class="badge-tag">E-Library</span></td>
                    <td>Mengakses perpustakaan digital madrasah, membaca buku teks, modul ajar, dan referensi pelajaran berformat PDF.</td>
                </tr>
            </tbody>
        </table>

        <h3 class="subchapter-title"><i class="fa-solid fa-sack-dollar"></i> 4.2 Kategori Keuangan & Tabungan</h3>
        <table class="doc-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Aplikasi</th>
                    <th>Fungsi & Petunjuk Penggunaan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="badge-tag">Kasir Tab.</span></td>
                    <td>Melayani transaksi tabungan siswa (setor tunai, tarik tunai, dan cetak mutasi saldo tabungan).</td>
                </tr>
                <tr>
                    <td><span class="badge-tag">Kasir (SPP)</span></td>
                    <td>Menerima pembayaran SPP, infaq, dan DSP siswa dengan sistem kwitansi digital resmi ber-QR Code.</td>
                </tr>
                <tr>
                    <td><span class="badge-tag">BKU Bend.</span></td>
                    <td>Buku Kas Umum Bendahara Madrasah untuk mencatat arus transaksi kas masuk dan keluar secara real-time.</td>
                </tr>
                <tr>
                    <td><span class="badge-tag">Bend. BOS</span></td>
                    <td>Pengelolaan dan pembukuan anggaran Bantuan Operasional Sekolah (BOS) sesuai komponen juknis resmi.</td>
                </tr>
            </tbody>
        </table>

        <h3 class="subchapter-title"><i class="fa-solid fa-qrcode"></i> 4.3 Kategori Absensi & Perizinan</h3>
        <table class="doc-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Aplikasi</th>
                    <th>Fungsi & Petunjuk Penggunaan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="badge-tag">Scan Siswa</span></td>
                    <td>Digunakan oleh guru piket gerbang untuk memindai kartu barcode/QR siswa saat kedatangan dan kepulangan.</td>
                </tr>
                <tr>
                    <td><span class="badge-tag">Scan Guru</span></td>
                    <td>Kamera scanner presensi kehadiran dewan guru dan tenaga kependidikan di pos piket madrasah.</td>
                </tr>
                <tr>
                    <td><span class="badge-tag">Persetujuan Izin</span></td>
                    <td>Memverifikasi dan menyetujui pengajuan izin tidak masuk siswa atau guru yang dilampiri surat dokter / bukti.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- ================= HALAMAN 7: BAB V BERKAS GURU & PROFIL ================= -->
    <div class="page-sheet page-break">
        <h2 class="chapter-title">
            <span>BAB V</span> MANAJEMEN BERKAS GURU & PROFIL
        </h2>

        <p style="text-align: justify;">
            Portal ini dilengkapi dengan sistem <em>Cloud E-Arsip</em> mandiri untuk setiap pendidik. Seluruh berkas administrasi disimpan dengan aman di server madrasah sehingga siap digunakan sewaktu-waktu untuk akreditasi, PKKM, maupun kenaikan pangkat.
        </p>

        <h3 class="subchapter-title"><i class="fa-solid fa-folder-tree"></i> 5.1 Menu Berkas Guru (Dokumen Pribadi & Perangkat)</h3>
        <ul style="line-height: 1.8;">
            <li><strong>Berkas Pribadi Guru:</strong> Tempat mengunggah scan SK Pembagian Tugas Mengajar, Ijazah Terakhir, Sertifikat Pendidik (Serdik), KTP, KK, dan NPWP berformat PDF.</li>
            <li><strong>Perangkat Pembelajaran:</strong> Tempat mengunggah dokumen Kurikulum Merdeka (Capaian Pembelajaran/CP, Alur Tujuan Pembelajaran/ATP, Modul Ajar, Prota, Promes, dan KKTP).</li>
            <li><strong>Buku Saku Guru:</strong> Dokumen panduan tata tertib pendidik, kode etik guru madrasah, dan visi-misi madrasah.</li>
            <li><strong>Kalender Akademik:</strong> Jadwal operasional madrasah, pekan efektif KBM, jadwal asesmen tengah semester, dan hari libur nasional.</li>
        </ul>

        <h3 class="subchapter-title"><i class="fa-solid fa-user-gear"></i> 5.2 Pengaturan Akun & Keamanan di Menu Profil</h3>
        <p>Pada tab menu <strong>Profil</strong> (`profil_guru.php`), Anda dapat melakukan:</p>
        <div class="step-box">
            <div class="step-num"><i class="fa-solid fa-camera"></i></div>
            <div class="step-content">
                <h4>Ganti Foto Profil</h4>
                <p>Ketuk pada lingkaran avatar foto untuk mengunggah foto formal terbaru Anda (disarankan foto berseragam rapi).</p>
            </div>
        </div>
        <div class="step-box">
            <div class="step-num"><i class="fa-solid fa-lock"></i></div>
            <div class="step-content">
                <h4>Ubah Kata Sandi (Password)</h4>
                <p>Masukkan kata sandi lama, lalu ketikkan kata sandi baru minimal 6 karakter. Pastikan Anda mengingat kata sandi baru tersebut.</p>
            </div>
        </div>
        <div class="step-box">
            <div class="step-num"><i class="fa-solid fa-file-pdf"></i></div>
            <div class="step-content">
                <h4>Buku Panduan Aplikasi (PDF)</h4>
                <p>Tautan cepat untuk membuka dan mendownload kembali dokumen panduan ini kapan saja langsung dari HP Anda.</p>
            </div>
        </div>
    </div>

    <!-- ================= HALAMAN 8: BAB VI FAQ & TROUBLESHOOTING ================= -->
    <div class="page-sheet page-break">
        <h2 class="chapter-title">
            <span>BAB VI</span> PUSAT BANTUAN & PEMECAHAN MASALAH (FAQ)
        </h2>

        <div style="margin-bottom: 20px;">
            <h4 style="color: #0f172a; margin: 0 0 5px 0; font-size: 0.95rem;">
                <i class="fa-solid fa-circle-question" style="color: #0284c7;"></i> Q: Mengapa kamera scanner barcode tidak bisa menyala saat ingin scan presensi?
            </h4>
            <p style="margin: 0; color: #475569; font-size: 0.88rem; line-height: 1.6;">
                <strong>A:</strong> Pastikan Anda telah memberikan izin kamera pada browser smartphone Anda. Ketuk icon gembok di pojok kiri atas bilah alamat URL browser &rarr; pilih <em>Permissions / Izin</em> &rarr; aktifkan izin <em>Camera</em> ke posisi "Izinkan" (Allow).
            </p>
        </div>

        <div style="margin-bottom: 20px;">
            <h4 style="color: #0f172a; margin: 0 0 5px 0; font-size: 0.95rem;">
                <i class="fa-solid fa-circle-question" style="color: #0284c7;"></i> Q: Bagaimana jika saya lupa kata sandi akun?
            </h4>
            <p style="margin: 0; color: #475569; font-size: 0.88rem; line-height: 1.6;">
                <strong>A:</strong> Hubungi Operator IT Madrasah atau Waka Kurikulum. Tim admin dapat me-reset kata sandi Anda ke pengaturan standar melalui panel admin sistem dalam hitungan detik.
            </p>
        </div>

        <div style="margin-bottom: 20px;">
            <h4 style="color: #0f172a; margin: 0 0 5px 0; font-size: 0.95rem;">
                <i class="fa-solid fa-circle-question" style="color: #0284c7;"></i> Q: Apakah aplikasi bisa diakses di laptop / komputer?
            </h4>
            <p style="margin: 0; color: #475569; font-size: 0.88rem; line-height: 1.6;">
                <strong>A:</strong> Ya, seluruh sistem dapat diakses secara sempurna baik di smartphone Android/iOS, tablet, maupun komputer/laptop melalui browser dengan membuka link <strong>https://<?= htmlspecialchars($website) ?></strong>.
            </p>
        </div>

        <div style="margin-bottom: 20px;">
            <h4 style="color: #0f172a; margin: 0 0 5px 0; font-size: 0.95rem;">
                <i class="fa-solid fa-circle-question" style="color: #0284c7;"></i> Q: Bagaimana cara mencetak buku panduan ini menjadi file PDF di HP Android/iPhone?
            </h4>
            <p style="margin: 0; color: #475569; font-size: 0.88rem; line-height: 1.6;">
                <strong>A:</strong> Ketuk tombol hijau <strong>"Unduh / Cetak PDF (A4)"</strong> di bagian atas halaman ini. Pada jendela print yang muncul, ubah pilihan printer menjadi <strong>"Save as PDF" (Simpan sebagai PDF)</strong>, pastikan ukuran kertas dipilih <strong>A4</strong>, lalu ketuk tombol Simpan. File PDF resmi akan langsung tersimpan di folder Download HP Anda.
            </p>
        </div>

        <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 12px; padding: 20px; margin-top: 35px; text-align: center;">
            <div style="width: 50px; height: 50px; background: #ecfdf5; color: #059669; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 10px auto;">
                <i class="fa-solid fa-headset"></i>
            </div>
            <h3 style="margin: 0 0 5px 0; font-size: 1.05rem; color: #0f172a;">Layanan Bantuan & IT Support Madrasah</h3>
            <p style="font-size: 0.85rem; color: #64748b; margin: 0 0 15px 0;">Jika memerlukan pendampingan teknis atau panduan lebih lanjut, silakan hubungi tim kami:</p>
            <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap; font-size: 0.88rem;">
                <span style="background: white; border: 1px solid #cbd5e1; padding: 6px 14px; border-radius: 8px; font-weight: 600;">
                    <i class="fa-brands fa-whatsapp" style="color: #10b981;"></i> Helpdesk: 0819-3824-0819
                </span>
                <span style="background: white; border: 1px solid #cbd5e1; padding: 6px 14px; border-radius: 8px; font-weight: 600;">
                    <i class="fa-solid fa-envelope" style="color: #0284c7;"></i> <?= htmlspecialchars($email) ?>
                </span>
            </div>
        </div>

        <div style="text-align: center; margin-top: 40px; font-size: 0.78rem; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 15px;">
            &copy; 2026 <?= htmlspecialchars($nama_madrasah) ?> &bull; Hak Cipta Dilindungi Undang-Undang &bull; Sistem Digital Aula Platform
        </div>
    </div>

</div>

<script>
    // Shortcut tombol keyboard Ctrl+P untuk print
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
            e.preventDefault();
            window.print();
        }
    });
</script>
</body>
</html>

