<?php
require_once 'koneksi.php';

// Data Institusi
$nama_madrasah = 'Madrasah Aliyah Miftahul Huda (MAS Mifhda)';
$nama_yayasan = 'YAYASAN PONDOK PESANTREN MIFTAHUL HUDA MATHLA\'UL ANWAR';
$nsm = '131218040003';
$npsn = '69988776';
$alamat = 'Margorahayu, Ds. Tambak Jaya, Kec. Way Tenong, Kab. Lampung Barat, Lampung 34884';
$website = 'apk.masmifhda.sch.id';
$email = 'masmiftahulhuda@gmail.com';
$kamad_nama = 'AHMAD RIFA\'I, M.Pd';
$kamad_nip = '-';
$waka_nama = 'Drs. Hamim Hasyim';
$waka_nip = '-';
$it_nama = 'M AZIZ SULAIMAN, S.Pd';
$versi_apk = 'v26.2.2 (Build 2026.11 - Modern Edition)';

$favicon_url = '/public/assets/images/logo.png';
if (isset($core_db)) {
    try {
        $stmt_inst = $core_db->query("SELECT * FROM institusi LIMIT 1");
        $inst = $stmt_inst->fetch(PDO::FETCH_ASSOC);
        if ($inst) {
            if (!empty($inst['nama'])) $nama_madrasah = $inst['nama'];
            if (!empty($inst['nama_yayasan'])) $nama_yayasan = $inst['nama_yayasan'];
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
    <link rel="stylesheet" href="<?= ($baseFolder ? $baseFolder : '') ?>/public/assets/css/plus-jakarta-sans.css">
    <link rel="stylesheet" href="<?= ($baseFolder ? $baseFolder : '') ?>/public/assets/css/all.min.css">
    
    <style>
        :root {
            --primary: #0284c7;
            --primary-dark: #0369a1;
            --primary-light: #e0f2fe;
            --secondary: #059669;
            --secondary-dark: #047857;
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
            background: #334155;
            color: var(--gray-text);
            line-height: 1.55;
            font-size: 13.5px;
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
            box-shadow: 0 4px 15px rgba(0,0,0,0.25);
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
            padding: 10px 22px;
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
            box-shadow: 0 10px 40px rgba(0,0,0,0.35);
            border-radius: 12px;
            overflow: hidden;
        }

        .page-sheet {
            padding: 45px 55px;
            background: white;
            position: relative;
            min-height: 1100px;
            box-sizing: border-box;
        }

        /* ==================== COVER PAGE ==================== */
        .cover-page {
            min-height: 980px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 40%, #f0f9ff 100%);
            border-bottom: 3px double #cbd5e1;
            position: relative;
            overflow: hidden;
            box-sizing: border-box;
            padding: 35px 50px 25px 50px;
        }
        .cover-decor {
            position: absolute;
            top: -150px;
            right: -150px;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(2,132,199,0.18) 0%, rgba(2,132,199,0) 70%);
        }
        .cover-decor-2 {
            position: absolute;
            bottom: -100px;
            left: -100px;
            width: 380px;
            height: 380px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(5,150,105,0.18) 0%, rgba(5,150,105,0) 70%);
        }
        .cover-header {
            text-align: center;
            position: relative;
            z-index: 2;
        }
        .cover-title-box {
            text-align: center;
            margin: 12px 0 6px 0;
            position: relative;
            z-index: 2;
        }
        .badge-edition {
            display: inline-block;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: white;
            padding: 4px 14px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 8px;
            box-shadow: 0 4px 10px rgba(2,132,199,0.25);
        }
        .cover-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.7rem;
            font-weight: 900;
            color: #0f172a;
            margin: 0 0 6px 0;
            line-height: 1.22;
            text-transform: uppercase;
            letter-spacing: -0.3px;
        }
        .cover-subtitle {
            font-size: 0.95rem;
            color: #0369a1;
            font-weight: 700;
            margin: 0;
            line-height: 1.35;
        }
        .cover-desc {
            font-size: 0.8rem;
            color: var(--gray-sub);
            max-width: 550px;
            margin: 6px auto 0;
            line-height: 1.45;
        }

        /* =====================================================================
           VECTOR SMARTPHONE MOCKUPS (COMPACT 1-PAGE A4 FIT)
           ===================================================================== */
        .cover-mockup-wrap {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 18px;
            margin: 10px 0;
            position: relative;
            z-index: 2;
        }
        .v-phone {
            width: 175px;
            background: #0f172a;
            border-radius: 26px;
            padding: 6px;
            box-shadow: 0 16px 36px rgba(15,23,42,0.3), 0 0 0 1.5px #334155;
            position: relative;
            transition: transform 0.3s;
        }
        .v-phone-notch {
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            width: 55px;
            height: 11px;
            background: #0f172a;
            border-radius: 8px;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }
        .v-phone-camera {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #1e293b;
            border: 1px solid #334155;
        }
        .v-phone-screen {
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            font-size: 8px;
            height: 280px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            color: #0f172a;
        }
        .v-status-bar {
            height: 18px;
            padding: 2px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 7px;
            font-weight: 700;
            color: #0f172a;
            background: #f8fafc;
        }
        .v-status-icons {
            display: flex;
            gap: 3px;
            font-size: 6.5px;
        }
        .v-app-bar {
            background: linear-gradient(135deg, #064e3b 0%, #059669 100%);
            color: white;
            padding: 6px 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .v-user-info {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .v-user-avatar {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: white;
            color: #064e3b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 8px;
        }
        .v-card-absen {
            margin: 5px 6px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 8px;
            padding: 5px 8px;
            text-align: left;
        }
        .v-chip-green {
            background: #059669;
            color: white;
            padding: 1.5px 5px;
            border-radius: 999px;
            font-weight: 800;
            font-size: 6.5px;
            display: inline-flex;
            align-items: center;
            gap: 2px;
        }
        .v-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 4px;
            padding: 0 6px;
            margin: 4px 0;
            text-align: center;
        }
        .v-tile {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 4px 2px;
        }
        .v-tile i { font-size: 9px; margin-bottom: 1px; display: block; }
        .v-tile span { font-size: 6px; font-weight: 700; color: #475569; display: block; }

        .v-bottom-bar {
            height: 30px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-around;
            padding: 0 4px;
            color: #94a3b8;
            font-size: 9px;
        }
        .v-bottom-bar .active { color: #0284c7; }
        .v-bottom-center-btn {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(2,132,199,0.4);
            transform: translateY(-3px);
            font-size: 8px;
        }

        .cover-footer {
            border-top: 1.5px solid #e2e8f0;
            padding-top: 10px;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        /* TYPOGRAPHY & SECTIONS */
        .chapter-title {
            font-size: 1.3rem;
            font-weight: 800;
            color: #0f172a;
            border-bottom: 2.5px solid var(--primary);
            padding-bottom: 8px;
            margin-top: 0;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .chapter-title span {
            background: var(--primary);
            color: white;
            font-size: 0.78rem;
            padding: 3px 10px;
            border-radius: 6px;
            letter-spacing: 1px;
            font-weight: 900;
        }
        .subchapter-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: #1e293b;
            margin-top: 22px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .subchapter-title i { color: var(--primary); }

        .step-box {
            display: flex;
            gap: 15px;
            margin-bottom: 14px;
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 16px;
        }
        .step-num {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            font-weight: 800;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .step-content h4 {
            margin: 0 0 3px 0;
            font-size: 0.95rem;
            color: #0f172a;
        }
        .step-content p {
            margin: 0;
            color: var(--gray-sub);
            font-size: 0.86rem;
            line-height: 1.5;
        }

        .alert-box {
            padding: 12px 16px;
            border-radius: 8px;
            margin: 14px 0;
            font-size: 0.86rem;
            line-height: 1.5;
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }
        .alert-box.warning {
            background: #fefce8;
            border-left: 4px solid #f59e0b;
            color: #854d0e;
        }
        .alert-box.success {
            background: #f0fdf4;
            border-left: 4px solid #10b981;
            color: #166534;
        }
        .alert-box.info {
            background: #eff6ff;
            border-left: 4px solid #0284c7;
            color: #1e40af;
        }

        .doc-table {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0;
            font-size: 0.86rem;
        }
        .doc-table th, .doc-table td {
            border: 1px solid var(--border);
            padding: 8px 12px;
            text-align: left;
            vertical-align: top;
        }
        .doc-table th {
            background: #f1f5f9;
            color: #0f172a;
            font-weight: 800;
        }
        .badge-tag {
            display: inline-block;
            background: #e2e8f0;
            color: #334155;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 4px;
        }

        /* Large Showcase Mockup Container */
        .showcase-mockup-box {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1.5px solid #cbd5e1;
            border-radius: 14px;
            padding: 20px;
            margin: 16px 0;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: inset 0 2px 6px rgba(0,0,0,0.04);
        }
        .figure-caption-modern {
            text-align: center;
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 8px;
            font-weight: 600;
            font-style: italic;
        }

        @media print {
            body { background: white !important; }
            .top-navbar { display: none !important; }
            .doc-wrapper { max-width: 100% !important; margin: 0 !important; box-shadow: none !important; border-radius: 0 !important; }
            .page-sheet { padding: 8mm 14mm !important; min-height: auto !important; box-sizing: border-box !important; }
            .page-break { page-break-before: always; break-before: page; }
            .cover-page {
                height: 250mm !important;
                max-height: 250mm !important;
                min-height: 250mm !important;
                padding: 6mm 12mm !important;
                box-sizing: border-box !important;
                overflow: hidden !important;
                page-break-after: auto !important;
                break-after: auto !important;
            }
            .cover-page .v-phone {
                width: 165px !important;
            }
            .cover-page .v-phone-screen {
                height: 255px !important;
            }
            @page { size: A4 portrait; margin: 8mm 10mm 10mm 10mm; }
        }
    </style>
</head>
<body>

<!-- TOP ACTION BAR -->
<div class="top-navbar">
    <div style="display: flex; align-items: center; gap: 15px;">
        <a href="<?= htmlspecialchars($back_url) ?>" class="btn-nav">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Aplikasi
        </a>
        <div style="font-size: 0.95rem; font-weight: 700;">
            Buku Panduan Aplikasi Mobile &bull; <span style="color: #38bdf8;"><?= htmlspecialchars($nama_madrasah) ?></span>
        </div>
    </div>
    <div>
        <button onclick="window.print()" class="btn-print">
            <i class="fa-solid fa-file-pdf"></i> Unduh / Cetak PDF (A4)
        </button>
    </div>
</div>

<div class="doc-wrapper">

    <!-- ===================================================================== -->
    <!-- HALAMAN 1: COVER DEPAN PREMIUM DENGAN VECTOR UI MOCKUPS               -->
    <!-- ===================================================================== -->
    <div class="page-sheet cover-page">
        <div class="cover-decor"></div>
        <div class="cover-decor-2"></div>

        <div class="cover-header">
            <div style="display: flex; align-items: center; justify-content: center; gap: 20px; margin-bottom: 12px;">
                <img src="/public/logo_kemenag.png" alt="Logo Kemenag" style="height: 60px; object-fit: contain;" onerror="this.style.display='none'">
                <img src="<?= htmlspecialchars($favicon_url) ?>" alt="Logo Madrasah" style="height: 65px; object-fit: contain;">
            </div>
            <div style="font-size: 0.85rem; font-weight: 800; color: #047857; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 3px;">
                KEMENTERIAN AGAMA REPUBLIK INDONESIA
            </div>
            <div style="font-size: 1.05rem; font-weight: 900; color: #0f172a; text-transform: uppercase; letter-spacing: 1px;">
                <?= htmlspecialchars($nama_madrasah) ?>
            </div>
            <div style="font-size: 0.78rem; color: #64748b; font-weight: 700;">
                NSM: <?= htmlspecialchars($nsm) ?> &bull; NPSN: <?= htmlspecialchars($npsn) ?>
            </div>
        </div>

        <div class="cover-title-box">
            <div class="badge-edition">Edisi Resmi &bull; TP 2026/2027</div>
            <h1 class="cover-title">
                BUKU PANDUAN PENGGUNAAN<br>APLIKASI MOBILE (APK)
            </h1>
            <p class="cover-subtitle">
                User Manual Portal Digital Guru, Tenaga Kependidikan & Siswa (MASMIFHDA PWA)
            </p>
            <p class="cover-desc">
                Panduan komprehensif operasional presensi QR & Wajah, jurnal KBM mengajar real-time, supervisi kunjungan kelas, kasir keuangan madrasah, dan hub aplikasi delegasi.
            </p>
        </div>

        <!-- PURE VECTOR SMARTPHONE SHOWCASE MOCKUPS (DUAL PORTAL: GURU & SISWA) -->
        <div class="cover-mockup-wrap">
            <!-- Mockup Phone 1: PORTAL GURU & TENDIK -->
            <div class="v-phone">
                <div class="v-phone-notch"><div class="v-phone-camera"></div></div>
                <div class="v-phone-screen">
                    <div>
                        <div class="v-status-bar">
                            <span>07:15</span>
                            <div class="v-status-icons">
                                <i class="fa-solid fa-signal"></i>
                                <i class="fa-solid fa-wifi"></i>
                                <i class="fa-solid fa-battery-full"></i>
                            </div>
                        </div>
                        <div style="background: #064e3b; color: white; padding: 2px 8px; font-size: 7px; font-weight: 800; text-align: center; letter-spacing: 0.5px;">
                            📱 PORTAL GURU & TENDIK
                        </div>
                        <div class="v-app-bar">
                            <div class="v-user-info">
                                <div class="v-user-avatar" style="background:#ecfdf5; color:#059669;"><i class="fa-solid fa-user-tie"></i></div>
                                <div style="text-align: left;">
                                    <div style="font-weight: 800; font-size: 8.5px;">DEWAN GURU</div>
                                    <div style="font-size: 7px; opacity: 0.9;">Pendidik & Guru Mapel</div>
                                </div>
                            </div>
                            <span class="v-chip-green" style="font-size: 6.5px;">Online</span>
                        </div>
                        <div class="v-card-absen">
                            <div class="v-chip-green"><i class="fa-solid fa-circle-check"></i> Sudah Hadir</div>
                            <div style="font-size: 11px; font-weight: 900; color: #064e3b; margin-top: 3px;">06:48 WIB</div>
                            <div style="font-size: 7.5px; color: #047857;"><i class="fa-solid fa-location-dot"></i> Pos Gerbang Madrasah</div>
                        </div>
                        <div class="v-grid-4">
                            <div class="v-tile"><i class="fa-solid fa-book-open" style="color: #0284c7;"></i><span>Jurnal</span></div>
                            <div class="v-tile"><i class="fa-solid fa-calendar-days" style="color: #059669;"></i><span>Jadwal</span></div>
                            <div class="v-tile"><i class="fa-solid fa-clipboard-check" style="color: #d97706;"></i><span>Supervisi</span></div>
                            <div class="v-tile"><i class="fa-solid fa-layer-group" style="color: #7c3aed;"></i><span>Aplikasi</span></div>
                        </div>
                        <div style="margin: 0 10px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px 8px; text-align: left;">
                            <div style="font-size: 7.5px; font-weight: 800; color: #0284c7;">AGENDA KBM &bull; KELAS X E.1</div>
                            <div style="font-size: 8px; font-weight: 800; color: #0f172a;">Informatika & Koding</div>
                            <div style="font-size: 7px; color: #64748b;">Jam 1-2 (07.30 - 09.00 WIB)</div>
                        </div>
                    </div>
                    <div class="v-bottom-bar">
                        <i class="fa-solid fa-house active"></i>
                        <i class="fa-solid fa-book-bookmark"></i>
                        <div class="v-bottom-center-btn"><i class="fa-solid fa-qrcode"></i></div>
                        <i class="fa-solid fa-folder-closed"></i>
                        <i class="fa-solid fa-user"></i>
                    </div>
                </div>
            </div>

            <!-- Mockup Phone 2: PORTAL SISWA & SANTRI -->
            <div class="v-phone" style="transform: translateY(12px);">
                <div class="v-phone-notch"><div class="v-phone-camera"></div></div>
                <div class="v-phone-screen">
                    <div>
                        <div class="v-status-bar">
                            <span>07:15</span>
                            <div class="v-status-icons">
                                <i class="fa-solid fa-wifi"></i>
                                <i class="fa-solid fa-battery-full"></i>
                            </div>
                        </div>
                        <div style="background: #0284c7; color: white; padding: 2px 8px; font-size: 7px; font-weight: 800; text-align: center; letter-spacing: 0.5px;">
                            📱 PORTAL SISWA & SANTRI
                        </div>
                        <div style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: white; padding: 8px 10px; display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 7px;">
                                <div style="width: 24px; height: 24px; border-radius: 50%; background: white; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 10px;"><i class="fa-solid fa-user-graduate"></i></div>
                                <div style="text-align: left;">
                                    <div style="font-weight: 800; font-size: 8.5px;">MUHAMMAD SANTRI</div>
                                    <div style="font-size: 7px; opacity: 0.9;">Kelas X &bull; NISN: 10816858</div>
                                </div>
                            </div>
                            <span style="background: rgba(255,255,255,0.25); color: white; padding: 1px 5px; border-radius: 999px; font-size: 6.5px; font-weight: 700;">Aktif</span>
                        </div>
                        <!-- Kartu Pelajar Digital Mini -->
                        <div style="margin: 6px 8px; background: #eff6ff; border: 1.2px solid #bfdbfe; border-radius: 8px; padding: 6px 8px; text-align: left;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 7px; font-weight: 800; color: #1e40af;"><i class="fa-solid fa-id-card"></i> E-KTA KARTU PELAJAR</span>
                                <span style="font-size: 6.5px; background: #10b981; color: white; padding: 1px 4px; border-radius: 4px; font-weight: 700;">QR Ready</span>
                            </div>
                            <div style="font-size: 8px; font-weight: 800; color: #0f172a; margin-top: 2px;">Presensi: Hadir di Kelas</div>
                        </div>
                        <!-- 6 Layanan Siswa -->
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 4px; padding: 0 8px; text-align: center;">
                            <div style="background: #fee2e2; border-radius: 6px; padding: 5px 2px;">
                                <i class="fa-solid fa-file-invoice-dollar" style="color: #ef4444; font-size: 10px;"></i>
                                <span style="font-size: 6.5px; font-weight: 700; color: #991b1b; display: block; margin-top: 1px;">Keuangan</span>
                            </div>
                            <div style="background: #ecfdf5; border-radius: 6px; padding: 5px 2px;">
                                <i class="fa-solid fa-calendar-check" style="color: #10b981; font-size: 10px;"></i>
                                <span style="font-size: 6.5px; font-weight: 700; color: #065f46; display: block; margin-top: 1px;">Absensi</span>
                            </div>
                            <div style="background: #fffbeb; border-radius: 6px; padding: 5px 2px;">
                                <i class="fa-solid fa-wallet" style="color: #f59e0b; font-size: 10px;"></i>
                                <span style="font-size: 6.5px; font-weight: 700; color: #92400e; display: block; margin-top: 1px;">Tabungan</span>
                            </div>
                            <div style="background: #f0fdf4; border-radius: 6px; padding: 5px 2px;">
                                <i class="fa-solid fa-comment-dots" style="color: #10b981; font-size: 10px;"></i>
                                <span style="font-size: 6.5px; font-weight: 700; color: #065f46; display: block; margin-top: 1px;">Wali Kelas</span>
                            </div>
                            <div style="background: #fdf2f8; border-radius: 6px; padding: 5px 2px;">
                                <i class="fa-solid fa-user-tie" style="color: #ec4899; font-size: 10px;"></i>
                                <span style="font-size: 6.5px; font-weight: 700; color: #9d174d; display: block; margin-top: 1px;">Mentor</span>
                            </div>
                            <div style="background: #eff6ff; border-radius: 6px; padding: 5px 2px;">
                                <i class="fa-solid fa-book-open-reader" style="color: #3b82f6; font-size: 10px;"></i>
                                <span style="font-size: 6.5px; font-weight: 700; color: #1e40af; display: block; margin-top: 1px;">E-Library</span>
                            </div>
                        </div>
                    </div>
                    <div class="v-bottom-bar">
                        <i class="fa-solid fa-house active"></i>
                        <i class="fa-solid fa-calendar-check"></i>
                        <div class="v-bottom-center-btn"><i class="fa-solid fa-qrcode"></i></div>
                        <i class="fa-solid fa-comments"></i>
                        <i class="fa-solid fa-user"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="cover-footer">
            <div style="font-size: 0.85rem; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 1px;">
                PUSAT DATA & INFORMASI DIGITAL MADRASAH
            </div>
            <div style="font-size: 0.78rem; color: #64748b; margin-top: 3px;">
                <?= htmlspecialchars($alamat) ?><br>
                Portal Sistem: <strong><?= htmlspecialchars($website) ?></strong> &bull; Versi Dokumen: <?= htmlspecialchars($versi_apk) ?>
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- HALAMAN 2: LEMBAR PENGESAHAN DOKUMEN MANUAL BOOK                      -->
    <!-- ===================================================================== -->
    <div class="page-sheet page-break">
        <h2 style="text-align: center; font-size: 1.35rem; color: #0f172a; margin-top: 0; text-transform: uppercase; letter-spacing: 0.5px;">
            LEMBAR PENGESAHAN RESMI DOKUMEN
        </h2>
        <div style="width: 60px; height: 3px; background: #0284c7; margin: 10px auto 25px auto;"></div>

        <p style="text-align: justify; font-size: 0.9rem; line-height: 1.7; color: #334155; text-indent: 30px;">
            Buku Panduan Operasional Aplikasi Mobile (User Manual Book) <strong><?= htmlspecialchars($nama_madrasah) ?></strong> ini disusun sebagai panduan teknis operasional pemanfaatan ekosistem platform digital bagi seluruh Dewan Guru, Tenaga Kependidikan, Tim Supervisor Akademik, Wali Kelas, Bendahara, Pembina Organisasi, dan Peserta Didik untuk Tahun Pelajaran 2026/2027.
        </p>

        <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 18px 24px; margin: 25px 0;">
            <table style="width: 100%; border: none; font-size: 0.88rem; line-height: 1.8;">
                <tr>
                    <td style="width: 170px; font-weight: 700; color: #475569;">Nama Aplikasi</td>
                    <td style="width: 15px;">:</td>
                    <td><strong>MASMIFHDA SYSTEM (Aula PWA Mobile Portal)</strong></td>
                </tr>
                <tr>
                    <td style="font-weight: 700; color: #475569;">Teknologi</td>
                    <td>:</td>
                    <td>Progressive Web Application (PWA) &bull; Cloud Real-time Server</td>
                </tr>
                <tr>
                    <td style="font-weight: 700; color: #475569;">Alamat Akses</td>
                    <td>:</td>
                    <td><a href="https://<?= htmlspecialchars($website) ?>/apk" style="color: #0284c7; font-weight: 700;">https://<?= htmlspecialchars($website) ?>/apk</a></td>
                </tr>
                <tr>
                    <td style="font-weight: 700; color: #475569;">Penyusun</td>
                    <td>:</td>
                    <td>Tim Pengembang IT & Kurikulum <?= htmlspecialchars($nama_madrasah) ?></td>
                </tr>
                <tr>
                    <td style="font-weight: 700; color: #475569;">Masa Berlaku</td>
                    <td>:</td>
                    <td>Tahun Pelajaran 2026/2027 (Edisi Pembaharuan November 2026)</td>
                </tr>
            </table>
        </div>

        <!-- Tanda Tangan 3 Kolom Bersih Tanpa NIP Sesuai Arahan -->
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-top: 55px; text-align: center;">
            <div>
                <div style="font-size: 0.85rem; color: #64748b;">Penyusun / Pengembang:</div>
                <div style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin-top: 4px;">Tim IT Madrasah</div>
                <div style="height: 85px;"></div>
                <div style="font-size: 0.95rem; font-weight: 800; color: #0f172a; text-decoration: underline;">M AZIZ SULAIMAN, S.Pd</div>
            </div>
            <div>
                <div style="font-size: 0.85rem; color: #64748b;">Memeriksa / Menyetujui:</div>
                <div style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin-top: 4px;">Waka Kurikulum</div>
                <div style="height: 85px;"></div>
                <div style="font-size: 0.95rem; font-weight: 800; color: #0f172a; text-decoration: underline;">Drs. Hamim Hasyim</div>
            </div>
            <div>
                <div style="font-size: 0.85rem; color: #64748b;">Mengetahui / Mengesahkan:</div>
                <div style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin-top: 4px;">Kepala Madrasah</div>
                <div style="height: 85px;"></div>
                <div style="font-size: 0.95rem; font-weight: 800; color: #0f172a; text-decoration: underline;">AHMAD RIFA'I, M.Pd</div>
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- HALAMAN 3: DAFTAR ISI & PETA NAVIGASI EKOSISTEM MOBILE                -->
    <!-- ===================================================================== -->
    <div class="page-sheet page-break">
        <h2 class="chapter-title">
            <span>DAFTAR ISI</span> SISTEMATIKA BUKU PANDUAN PENGGUNAAN
        </h2>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem; line-height: 1.8;">
            <tr><td><strong>HALAMAN JUDUL / COVER DEPAN PREMIUM</strong></td><td align="right"><strong>Hal. 1</strong></td></tr>
            <tr><td><strong>LEMBAR PENGESAHAN RESMI DOKUMEN</strong></td><td align="right"><strong>Hal. 2</strong></td></tr>
            <tr><td><strong>DAFTAR ISI & PETA EKOSISTEM APLIKASI</strong></td><td align="right"><strong>Hal. 3</strong></td></tr>
            <tr><td><strong>BAB I &bull; PENDAHULUAN & CARA INSTALASI PWA (ANDROID & IOS)</strong></td><td align="right"><strong>Hal. 4</strong></td></tr>
            <tr><td style="padding-left: 18px;">1.1 Pengenalan Teknologi PWA (Progressive Web Application)</td><td align="right">Hal. 4</td></tr>
            <tr><td style="padding-left: 18px;">1.2 Langkah Install ke Layar Utama Smartphone</td><td align="right">Hal. 4</td></tr>
            <tr><td><strong>BAB II &bull; OTENTIKASI & KEAMANAN AKUN PENGGUNA</strong></td><td align="right"><strong>Hal. 5</strong></td></tr>
            <tr><td style="padding-left: 18px;">2.1 Login Menggunakan NIP / NIK / Username</td><td align="right">Hal. 5</td></tr>
            <tr><td style="padding-left: 18px;">2.2 Fitur Auto-Login & Prosedur Ganti Password</td><td align="right">Hal. 5</td></tr>
            <tr><td><strong>BAB III &bull; ANATOMI DASHBOARD BERANDA GURU & PRESENSI</strong></td><td align="right"><strong>Hal. 6</strong></td></tr>
            <tr><td style="padding-left: 18px;">3.1 Kartu Status Kehadiran Datang & Pulang Real-time</td><td align="right">Hal. 6</td></tr>
            <tr><td style="padding-left: 18px;">3.2 Statistik KBM Bulanan & Jadwal Pelajaran Hari Ini</td><td align="right">Hal. 6</td></tr>
            <tr><td style="padding-left: 18px;">3.3 Navigasi 5 Menu Bawah (Dock Bar)</td><td align="right">Hal. 6</td></tr>
            <tr><td><strong>BAB IV &bull; MODUL JURNAL MENGAJAR DIGITAL (KBM) KELAS</strong></td><td align="right"><strong>Hal. 7</strong></td></tr>
            <tr><td style="padding-left: 18px;">4.1 Pengisian Jurnal Per Jam Pelajaran & Materi Pokok</td><td align="right">Hal. 7</td></tr>
            <tr><td style="padding-left: 18px;">4.2 Pencatatan Kehadiran Siswa (S/I/A) & Upload Foto Kelas</td><td align="right">Hal. 7</td></tr>
            <tr><td><strong>BAB V &bull; HUB "APLIKASI SAYA" & HAK AKSES DELEGASI</strong></td><td align="right"><strong>Hal. 8</strong></td></tr>
            <tr><td style="padding-left: 18px;">5.1 Kategori Akademik: Nilai Harian, Tugas Daring & E-Library</td><td align="right">Hal. 8</td></tr>
            <tr><td style="padding-left: 18px;">5.2 Kategori Keuangan: Kasir SPP, Tabungan, BKU Bendahara</td><td align="right">Hal. 8</td></tr>
            <tr><td><strong>BAB VI &bull; MODUL SUPERVISI KUNJUNGAN KELAS AKADEMIK</strong></td><td align="right"><strong>Hal. 9</strong></td></tr>
            <tr><td style="padding-left: 18px;">6.1 Alur Kerja Supervisor (Kepala Madrasah & Tim Penilai)</td><td align="right">Hal. 9</td></tr>
            <tr><td style="padding-left: 18px;">6.2 Pengisian Rubrik 15 Instrumen & Rekomendasi Solusi</td><td align="right">Hal. 9</td></tr>
            <tr><td><strong>BAB VII &bull; KASIR KEUANGAN (SPP) & PRESENSI SCANNER QR</strong></td><td align="right"><strong>Hal. 10</strong></td></tr>
            <tr><td style="padding-left: 18px;">7.1 Penerimaan Pembayaran & E-Kwitansi Ber-QR Code</td><td align="right">Hal. 10</td></tr>
            <tr><td style="padding-left: 18px;">7.2 Scanner Kehadiran Siswa & Guru di Gerbang Madrasah</td><td align="right">Hal. 10</td></tr>
            <tr><td><strong>BAB VIII &bull; MANAJEMEN CLOUD E-ARSIP BERKAS GURU & PKKM</strong></td><td align="right"><strong>Hal. 11</strong></td></tr>
            <tr><td style="padding-left: 18px;">8.1 Upload Dokumen Pribadi & Perangkat Kurikulum Merdeka</td><td align="right">Hal. 11</td></tr>
            <tr><td><strong>BAB IX &bull; PUSAT BANTUAN, FAQ & EMERGENCY TROUBLESHOOTING</strong></td><td align="right"><strong>Hal. 12</strong></td></tr>
            <tr><td style="padding-left: 18px;">9.1 Penanganan Izin Kamera, Reset Password & Helpdesk IT</td><td align="right">Hal. 12</td></tr>
        </table>
    </div>

    <!-- ===================================================================== -->
    <!-- HALAMAN 4: BAB I PENDAHULUAN & ARSITEKTUR PWA                         -->
    <!-- ===================================================================== -->
    <div class="page-sheet page-break">
        <h2 class="chapter-title">
            <span>BAB I</span> PENDAHULUAN & CARA INSTALASI PWA (ANDROID & IOS)
        </h2>

        <p style="text-align: justify;">
            Aplikasi Mobile <strong><?= htmlspecialchars($nama_madrasah) ?></strong> dikembangkan dengan teknologi <em>Progressive Web Application</em> (PWA). PWA menghadirkan performa secepat aplikasi native Android/iOS tanpa mengharuskan pengguna mengunduh file APK berukuran puluhan megabyte melalui toko aplikasi. Aplikasi ini sangat ringan, hemat kuota internet, dan terhubung secara otomatis ke database cloud madrasah.
        </p>

        <h3 class="subchapter-title"><i class="fa-solid fa-mobile-screen-button"></i> 1.1 Panduan Instalasi di Smartphone Android (Google Chrome)</h3>
        <div class="step-box">
            <div class="step-num">1</div>
            <div class="step-content">
                <h4>Buka Alamat Resmi di Chrome</h4>
                <p>Ketikkan URL resmi portal pada bilah browser Chrome smartphone Anda: <strong>https://<?= htmlspecialchars($website) ?>/apk</strong></p>
            </div>
        </div>
        <div class="step-box">
            <div class="step-num">2</div>
            <div class="step-content">
                <h4>Ketuk Tombol Menu Titik Tiga [⋮] di Kanan Atas</h4>
                <p>Pilih opsi <strong>"Tambahkan ke Layar Utama" (Add to Home screen)</strong> atau ketuk banner pop-up <em>"Install Aplikasi MAS Mifhda"</em> yang muncul di bagian bawah layar.</p>
            </div>
        </div>
        <div class="step-box">
            <div class="step-num">3</div>
            <div class="step-content">
                <h4>Konfirmasi dan Buka Icon Aplikasi</h4>
                <p>Icon MAS Mifhda otomatis tercipta di layar beranda HP Anda. Buka aplikasi untuk menikmati antarmuka layar penuh (fullscreen) tanpa bilah URL browser.</p>
            </div>
        </div>

        <h3 class="subchapter-title"><i class="fa-brands fa-apple"></i> 1.2 Panduan Instalasi di iPhone / iPad (Safari)</h3>
        <div class="step-box">
            <div class="step-num">1</div>
            <div class="step-content">
                <h4>Buka URL di Browser Safari</h4>
                <p>Akses <strong>https://<?= htmlspecialchars($website) ?>/apk</strong> melalui browser bawaan Apple (Safari).</p>
            </div>
        </div>
        <div class="step-box">
            <div class="step-num">2</div>
            <div class="step-content">
                <h4>Ketuk Tombol Share (Ikon Kotak dengan Panah ke Atas [↑])</h4>
                <p>Gulir menu ke bawah lalu pilih opsi <strong>"Add to Home Screen" (Tambahkan ke Layar Utama)</strong>.</p>
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- HALAMAN 5: BAB II OTENTIKASI & KEAMANAN AKUN                          -->
    <!-- ===================================================================== -->
    <div class="page-sheet page-break">
        <h2 class="chapter-title">
            <span>BAB II</span> OTENTIKASI & KEAMANAN AKUN PENGGUNA
        </h2>

        <p style="text-align: justify;">
            Sistem menerapkan otentikasi ketat berbasis peran (Role-Based Access Control) yang melindungi kerahasiaan data nilai, keuangan, dan arsip kepegawaian madrasah.
        </p>

        <h3 class="subchapter-title"><i class="fa-solid fa-key"></i> 2.1 Tata Cara Login Guru & Tenaga Kependidikan</h3>
        <ul style="line-height: 1.8;">
            <li><strong>Username / NIP / NUPTK:</strong> Masukkan nomor NIP atau Username yang telah didaftarkan oleh Administrator Kurikulum.</li>
            <li><strong>Password Akun:</strong> Masukkan kata sandi rahasia Anda. (Saat pertama kali dibagikan, password default diberikan oleh Waka Kurikulum).</li>
            <li><strong>Opsi "Ingat Saya":</strong> Aktifkan tanda centang ini agar sesi login Anda tersimpan di perangkat pribadi dan Anda tidak perlu mengetikkan password berulang kali.</li>
        </ul>

        <div class="alert-box warning">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>
                <strong>Himbauan Keamanan:</strong> Demi mencegah penyalahgunaan hak akses, jangan pernah meminjamkan akun Anda kepada pihak lain dan hindari login di perangkat bersama tanpa melakukan Logout setelah selesai.
            </div>
        </div>

        <h3 class="subchapter-title"><i class="fa-solid fa-lock"></i> 2.2 Prosedur Penggantian Kata Sandi Mandiri</h3>
        <p>Setiap guru dapat memperbarui password sewaktu-waktu melalui menu:</p>
        <div class="step-box">
            <div class="step-num"><i class="fa-solid fa-shield-halved"></i></div>
            <div class="step-content">
                <h4>Buka Tab Menu Profil &rarr; Keamanan Akun</h4>
                <p>Masukkan password lama Anda, lalu buat password baru minimal 6 karakter kombinasi huruf dan angka. Simpan perubahan untuk mengaktifkan password baru.</p>
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- HALAMAN 6: BAB III ANATOMI DASHBOARD BERANDA GURU                     -->
    <!-- ===================================================================== -->
    <div class="page-sheet page-break">
        <h2 class="chapter-title">
            <span>BAB III</span> ANATOMI DASHBOARD BERANDA GURU & PRESENSI
        </h2>

        <p style="text-align: justify;">
            Dashboard Beranda Guru (`guru_dashboard.php`) dirancang intuitif untuk memberikan ringkasan status harian seorang pendidik dalam satu pandangan layar.
        </p>

        <!-- VECTOR UI BLUEPRINT MOCKUP (REPLACING OLD SCREENSHOT) -->
        <div class="showcase-mockup-box">
            <div class="v-phone" style="width: 250px;">
                <div class="v-phone-notch"><div class="v-phone-camera"></div></div>
                <div class="v-phone-screen" style="height: 420px;">
                    <div>
                        <div class="v-status-bar">
                            <span>07:15</span>
                            <div class="v-status-icons"><i class="fa-solid fa-wifi"></i><i class="fa-solid fa-battery-full"></i></div>
                        </div>
                        <div class="v-app-bar">
                            <div class="v-user-info">
                                <div class="v-user-avatar"><i class="fa-solid fa-user-tie"></i></div>
                                <div style="text-align: left;">
                                    <div style="font-weight: 800; font-size: 10px;">Drs. Hamim Hasyim</div>
                                    <div style="font-size: 8px; opacity: 0.9;">Kepala Madrasah</div>
                                </div>
                            </div>
                            <span class="v-chip-green">Online</span>
                        </div>
                        <div class="v-card-absen">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span class="v-chip-green"><i class="fa-solid fa-check"></i> Sudah Hadir</span>
                                <span style="font-size: 7.5px; color: #64748b;">13 Nov 2026</span>
                            </div>
                            <div style="font-size: 12px; font-weight: 900; color: #064e3b; margin: 3px 0;">06:48 WIB</div>
                            <div style="font-size: 7.5px; color: #059669;"><i class="fa-solid fa-circle-dot"></i> Presensi Tepat Waktu (Pos Gerbang)</div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px; padding: 0 10px; margin-bottom: 6px;">
                            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 6px; text-align: left;">
                                <div style="font-size: 11px; font-weight: 900; color: #166534;">22 Hari</div>
                                <div style="font-size: 7.5px; color: #15803d;">Kehadiran Bulan Ini</div>
                            </div>
                            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 6px; text-align: left;">
                                <div style="font-size: 11px; font-weight: 900; color: #1e40af;">18 Jurnal</div>
                                <div style="font-size: 7.5px; color: #1d4ed8;">KBM Terverifikasi</div>
                            </div>
                        </div>
                        <div class="v-grid-4">
                            <div class="v-tile"><i class="fa-solid fa-book-open" style="color: #0284c7;"></i><span>Jurnal</span></div>
                            <div class="v-tile"><i class="fa-solid fa-calendar" style="color: #059669;"></i><span>Jadwal</span></div>
                            <div class="v-tile"><i class="fa-solid fa-user-check" style="color: #d97706;"></i><span>Supervisi</span></div>
                            <div class="v-tile"><i class="fa-solid fa-layer-group" style="color: #7c3aed;"></i><span>Aplikasi</span></div>
                        </div>
                    </div>
                    <div class="v-bottom-bar">
                        <i class="fa-solid fa-house active"></i>
                        <i class="fa-solid fa-book-bookmark"></i>
                        <div class="v-bottom-center-btn"><i class="fa-solid fa-qrcode"></i></div>
                        <i class="fa-solid fa-folder-closed"></i>
                        <i class="fa-solid fa-user"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="figure-caption-modern">Gambar 3.1: Desain Antarmuka Beranda Guru MASMIFHDA PWA dengan Ringkasan Kehadiran & Jadwal</div>

        <h3 class="subchapter-title"><i class="fa-solid fa-compass"></i> 3.1 Fungsi 5 Menu Bawah (Bottom Dock)</h3>
        <table class="doc-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Menu</th>
                    <th>Fungsi Pokok</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>1. Beranda</strong></td>
                    <td>Menampilkan kartu absen hari ini, rekap bulanan, dan jadwal mengajar aktif.</td>
                </tr>
                <tr>
                    <td><strong>2. Jurnal</strong></td>
                    <td>Pencatatan agenda mengajar KBM kelas, absensi siswa S/I/A, dan upload foto suasana kelas.</td>
                </tr>
                <tr>
                    <td><strong>3. Tombol Tengah (Aplikasi)</strong></td>
                    <td>Pintu gerbang cepat menuju hub seluruh sistem (Supervisi, Nilai, Keuangan, Kasir, Berkas).</td>
                </tr>
                <tr>
                    <td><strong>4. Berkas</strong></td>
                    <td>Cloud arsip pribadi: SK mengajar, ijazah, RPP/Modul Ajar, dan dokumen PKKM.</td>
                </tr>
                <tr>
                    <td><strong>5. Profil</strong></td>
                    <td>Pengaturan akun pribadi, penggantian foto profil, ubah kata sandi, dan unduh panduan.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- ===================================================================== -->
    <!-- HALAMAN 7: BAB IV MODUL JURNAL MENGAJAR DIGITAL                       -->
    <!-- ===================================================================== -->
    <div class="page-sheet page-break">
        <h2 class="chapter-title">
            <span>BAB IV</span> MODUL JURNAL MENGAJAR DIGITAL (KBM) KELAS
        </h2>

        <p style="text-align: justify;">
            Modul Jurnal Mengajar Digital berfungsi menggantikan buku jurnal kertas konvensional. Setiap pendidik diwajibkan mencatat pelaksanaan KBM langsung di kelas secara real-time.
        </p>

        <h3 class="subchapter-title"><i class="fa-solid fa-pen-to-square"></i> 4.1 Langkah-Langkah Pengisian Jurnal</h3>
        <div class="step-box">
            <div class="step-num">1</div>
            <div class="step-content">
                <h4>Pilih Jadwal Mengajar atau Tambah Jurnal</h4>
                <p>Ketuk tombol <strong>"Tambah Jurnal Mengajar"</strong>. Pilih Kelas yang diajar (misal: <em>X E.1</em>), Mata Pelajaran (misal: <em>Informatika</em>), dan Jam Ke-.</p>
            </div>
        </div>
        <div class="step-box">
            <div class="step-num">2</div>
            <div class="step-content">
                <h4>Tuliskan Materi Pokok & Capaian Pembelajaran</h4>
                <p>Isikan ringkasan materi KBM yang disampaikan sesuai Alur Tujuan Pembelajaran (ATP) dan modul ajar yang telah disusun.</p>
            </div>
        </div>
        <div class="step-box">
            <div class="step-num">3</div>
            <div class="step-content">
                <h4>Tandai Kehadiran Siswa di Kelas</h4>
                <p>Pilih siswa yang <strong>Sakit (S)</strong>, <strong>Izin (I)</strong>, atau <strong>Alpa (A)</strong>. Siswa yang tidak ditandai secara otomatis tercatat <strong>Hadir (H)</strong>.</p>
            </div>
        </div>
        <div class="step-box">
            <div class="step-num">4</div>
            <div class="step-content">
                <h4>Ambil Foto Dokumentasi Suasana KBM & Simpan</h4>
                <p>Gunakan kamera smartphone untuk memotret aktivitas belajar mengajar di kelas, lalu ketuk tombol hijau <strong>"Simpan Jurnal"</strong>.</p>
            </div>
        </div>

        <div class="alert-box success">
            <i class="fa-solid fa-circle-check"></i>
            <div>
                <strong>Sinkronisasi Instan ke Waka Kurikulum:</strong> Jurnal yang disimpan langsung muncul pada dashboard monitoring Kurikulum madrasah dan menjadi bukti fisik resmi untuk penilaian kinerja guru (PKKM) dan supervisi akademik.
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- HALAMAN 8: BAB V HUB "APLIKASI SAYA" & HAK AKSES DELEGASI             -->
    <!-- ===================================================================== -->
    <div class="page-sheet page-break">
        <h2 class="chapter-title">
            <span>BAB V</span> HUB "APLIKASI SAYA" & HAK AKSES DELEGASI
        </h2>

        <p style="text-align: justify;">
            Menu <strong>"Aplikasi Saya"</strong> (`aplikasi_saya.php`) adalah hub ekosistem cerdas yang mengumpulkan seluruh modul operasional madrasah. Ikon modul yang tampil di layar Anda disesuaikan secara otomatis berdasarkan tugas tambahan (delegasi) yang ditetapkan oleh Kepala Madrasah.
        </p>

        <h3 class="subchapter-title"><i class="fa-solid fa-layer-group"></i> 5.1 Pembagian Kategori Aplikasi & Delegasi</h3>
        <table class="doc-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Kategori</th>
                    <th style="width: 30%;">Nama Modul</th>
                    <th>Sasaran & Wewenang</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="3"><strong>1. Akademik & Pengajaran</strong></td>
                    <td><span class="badge-tag">Nilai Harian</span></td>
                    <td>Input nilai asesmen formatif dan sumatif lingkup materi siswa per kelas.</td>
                </tr>
                <tr>
                    <td><span class="badge-tag">Supervisi Kelas</span></td>
                    <td>Akses khusus bagi Kepala Madrasah & Guru yang ditunjuk sebagai Tim Supervisor KBM.</td>
                </tr>
                <tr>
                    <td><span class="badge-tag">E-Library</span></td>
                    <td>Akses perpustakaan digital madrasah, buku teks digital Kemenag, dan modul ajar.</td>
                </tr>
                <tr>
                    <td rowspan="3"><strong>2. Keuangan & Kasir</strong></td>
                    <td><span class="badge-tag">Kasir SPP & Infaq</span></td>
                    <td>Penerimaan pembayaran SPP bulanan santri dengan e-kwitansi QR Code resmi.</td>
                </tr>
                <tr>
                    <td><span class="badge-tag">Kasir Tabungan</span></td>
                    <td>Layanan setor dan tarik tabungan siswa di bawah pengawasan bendahara madrasah.</td>
                </tr>
                <tr>
                    <td><span class="badge-tag">BKU Bendahara</span></td>
                    <td>Buku Kas Umum untuk pembukuan arus kas masuk dan kas keluar madrasah.</td>
                </tr>
                <tr>
                    <td rowspan="2"><strong>3. Presensi & Scanner</strong></td>
                    <td><span class="badge-tag">Scan Siswa</span></td>
                    <td>Kamera pemindai barcode kartu pelajar siswa di gerbang kedatangan dan kepulangan.</td>
                </tr>
                <tr>
                    <td><span class="badge-tag">Scan Guru</span></td>
                    <td>Kamera scanner presensi datang dan pulang dewan guru serta staf tata usaha.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- ===================================================================== -->
    <!-- HALAMAN 9: BAB VI MODUL SUPERVISI KUNJUNGAN KELAS AKADEMIK            -->
    <!-- ===================================================================== -->
    <div class="page-sheet page-break">
        <h2 class="chapter-title">
            <span>BAB VI</span> MODUL SUPERVISI KUNJUNGAN KELAS AKADEMIK
        </h2>

        <p style="text-align: justify;">
            Modul Supervisi Kunjungan Kelas (`wakakur_supervisi_kunjungan.php`) adalah instrumen penjaminan mutu KBM internal madrasah. Fitur ini dapat diakses oleh Kepala Madrasah dan seluruh guru yang telah ditetapkan dalam SK Tim Supervisi Pembelajaran.
        </p>

        <h3 class="subchapter-title"><i class="fa-solid fa-user-check"></i> 6.1 Alur Kerja Penilaian Supervisor di Kelas</h3>
        <div class="step-box">
            <div class="step-num">1</div>
            <div class="step-content">
                <h4>Buka Menu Supervisi Kelas pada Aplikasi Saya</h4>
                <p>Supervisor membuka daftar guru madrasah. Ketuk nama guru sasaran yang sedang diobservasi di kelas.</p>
            </div>
        </div>
        <div class="step-box">
            <div class="step-num">2</div>
            <div class="step-content">
                <h4>Isi 15 Indikator Rubrik Pengamatan Pembelajaran</h4>
                <p>Supervisor memberikan skor 1 hingga 4 pada instrumen: kegiatan pendahuluan, penguasaan materi ajar, pemanfaatan media digital/IT, diferensiasi belajar, interaksi santri, dan penutupan.</p>
            </div>
        </div>
        <div class="step-box">
            <div class="step-num">3</div>
            <div class="step-content">
                <h4>Ketikkan Catatan Tindak Lanjut & Rekomendasi</h4>
                <p>Berikan umpan balik konstruktif mengenai kelebihan KBM guru dan aspek yang perlu ditingkatkan untuk perbaikan berkelanjutan.</p>
            </div>
        </div>
        <div class="step-box">
            <div class="step-num">4</div>
            <div class="step-content">
                <h4>Simpan Penilaian Digital</h4>
                <p>Skor otomatis dikalkulasi menjadi nilai akhir (0–100) dengan predikat mutu (Amat Baik, Baik, Cukup), serta terangkum langsung pada Laporan Hasil Supervisi Kepala Madrasah.</p>
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- HALAMAN 10: BAB VII KASIR KEUANGAN (SPP) & PRESENSI SCANNER QR        -->
    <!-- ===================================================================== -->
    <div class="page-sheet page-break">
        <h2 class="chapter-title">
            <span>BAB VII</span> KASIR KEUANGAN (SPP) & PRESENSI SCANNER QR
        </h2>

        <h3 class="subchapter-title"><i class="fa-solid fa-receipt"></i> 7.1 Operasional Kasir SPP & Kwitansi Digital QR</h3>
        <p>
            Pembayaran iuran madrasah (SPP, DSP, infaq pembangunan) kini tercatat otomatis tanpa buku manual:
        </p>
        <ul style="line-height: 1.8;">
            <li>Petugas kasir mencari nama atau NISN siswa.</li>
            <li>Sistem menampilkan tagihan aktif dan riwayat bulan-bulan yang telah lunas.</li>
            <li>Pilih bulan yang dibayarkan, masukkan nominal tunai/transfer, lalu ketuk <strong>"Proses Pembayaran"</strong>.</li>
            <li>Sistem langsung menerbitkan <strong>E-Kwitansi Resmi ber-QR Code</strong> yang dapat dicetak via printer bluetooth thermal atau dibagikan langsung ke WhatsApp wali santri.</li>
        </ul>

        <h3 class="subchapter-title"><i class="fa-solid fa-camera-rotate"></i> 7.2 Operasional Scanner Presensi di Gerbang Madrasah</h3>
        <p>
            Petugas piket gerbang mengoperasikan scanner presensi:
        </p>
        <ul style="line-height: 1.8;">
            <li>Siswa mengarahkan Kartu Pelajar Elektronik (QR Code) ke arah kamera scanner.</li>
            <li>Sistem melakukan verifikasi dalam waktu &plusmn; 0.5 detik, mengeluarkan bunyi konfirmasi (beep), dan menampilkan foto serta status siswa.</li>
            <li>Jam kehadiran dan kepulangan otomatis terkirim ke server database madrasah secara real-time.</li>
        </ul>
    </div>

    <!-- ===================================================================== -->
    <!-- HALAMAN 11: BAB VIII MANAJEMEN CLOUD E-ARSIP BERKAS GURU              -->
    <!-- ===================================================================== -->
    <div class="page-sheet page-break">
        <h2 class="chapter-title">
            <span>BAB VIII</span> MANAJEMEN CLOUD E-ARSIP BERKAS GURU & PKKM
        </h2>

        <p style="text-align: justify;">
            MAS Mifhda menyediakan ruang penyimpanan berkas awan (Cloud E-Arsip) mandiri bagi setiap tenaga pendidik. Dokumen administrasi tersimpan rapi dan dapat diunduh kapan saja saat visitasi Pengawas Kemenag, Akreditasi BAN-PDM, maupun Penilaian Kinerja Kepala Madrasah (PKKM).
        </p>

        <h3 class="subchapter-title"><i class="fa-solid fa-folder-tree"></i> 8.1 Klasifikasi Berkas yang Diunggah</h3>
        <table class="doc-table">
            <thead>
                <tr>
                    <th style="width: 30%;">Klasifikasi Dokumen</th>
                    <th>Rincian Berkas (Format PDF / JPG)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Dokumen Kepegawaian Pribadi</strong></td>
                    <td>Scan SK Pembagian Tugas Mengajar, Ijazah Terakhir, Akta Mengajar / Sertifikat Pendidik (Serdik), KTP, KK, dan NPWP.</td>
                </tr>
                <tr>
                    <td><strong>Perangkat Pembelajaran Merdeka</strong></td>
                    <td>Capaian Pembelajaran (CP), Alur Tujuan Pembelajaran (ATP), Modul Ajar (RPP), Program Tahunan (Prota), Program Semester (Promes), dan KKTP.</td>
                </tr>
                <tr>
                    <td><strong>Instrumen Evaluasi & Asesmen</strong></td>
                    <td>Kisi-kisi soal, naskah asesmen formatif/sumatif, rubrik penilaian kinerja, dan buku rekapitulasi nilai siswa.</td>
                </tr>
                <tr>
                    <td><strong>Pengembangan Keprofesian (PKB)</strong></td>
                    <td>Sertifikat workshop/diklat IKM, bukti kegiatan MGMP/KKM, dan karya publikasi ilmiah guru.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- ===================================================================== -->
    <!-- HALAMAN 12: BAB IX PUSAT BANTUAN, FAQ & EMERGENCY TROUBLESHOOTING     -->
    <!-- ===================================================================== -->
    <div class="page-sheet page-break">
        <h2 class="chapter-title">
            <span>BAB IX</span> PUSAT BANTUAN, FAQ & EMERGENCY TROUBLESHOOTING
        </h2>

        <div style="margin-bottom: 16px;">
            <h4 style="color: #0f172a; margin: 0 0 4px 0; font-size: 0.95rem;">
                <i class="fa-solid fa-circle-question" style="color: #0284c7;"></i> Q: Mengapa kamera scanner tidak terbuka saat ingin scan QR Code presensi?
            </h4>
            <p style="margin: 0; color: #475569; font-size: 0.88rem; line-height: 1.6;">
                <strong>A:</strong> Pastikan Anda telah memberikan izin (permission) akses kamera pada browser Chrome/Safari Anda. Ketuk ikon gembok pada bilah URL browser &rarr; pilih <em>Permissions / Izin</em> &rarr; aktifkan kamera ke posisi <strong>"Izinkan" (Allow)</strong>.
            </p>
        </div>

        <div style="margin-bottom: 16px;">
            <h4 style="color: #0f172a; margin: 0 0 4px 0; font-size: 0.95rem;">
                <i class="fa-solid fa-circle-question" style="color: #0284c7;"></i> Q: Bagaimana jika guru lupa kata sandi akun?
            </h4>
            <p style="margin: 0; color: #475569; font-size: 0.88rem; line-height: 1.6;">
                <strong>A:</strong> Silakan hubungi Waka Kurikulum atau Tim Admin IT Madrasah. Admin dapat mereset kata sandi Anda ke default dalam hitungan detik melalui panel pengelolaan akun.
            </p>
        </div>

        <div style="margin-bottom: 16px;">
            <h4 style="color: #0f172a; margin: 0 0 4px 0; font-size: 0.95rem;">
                <i class="fa-solid fa-circle-question" style="color: #0284c7;"></i> Q: Bagaimana cara mencetak buku panduan ini menjadi file PDF langsung dari HP?
            </h4>
            <p style="margin: 0; color: #475569; font-size: 0.88rem; line-height: 1.6;">
                <strong>A:</strong> Ketuk tombol hijau <strong>"Unduh / Cetak PDF (A4)"</strong> di bagian atas layar. Pada jendela printer yang muncul, ganti tujuan printer menjadi <strong>"Save as PDF" (Simpan sebagai PDF)</strong>, pastikan ukuran kertas diatur ke <strong>A4</strong>, lalu ketuk Simpan. Dokumen resmi akan terunduh ke folder dokumen smartphone Anda.
            </p>
        </div>

        <div style="background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 12px; padding: 18px; margin-top: 30px; text-align: center;">
            <div style="width: 48px; height: 48px; background: #ecfdf5; color: #059669; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; margin: 0 auto 8px auto;">
                <i class="fa-solid fa-headset"></i>
            </div>
            <h3 style="margin: 0 0 4px 0; font-size: 1rem; color: #0f172a;">Layanan Bantuan & IT Support Madrasah</h3>
            <p style="font-size: 0.84rem; color: #64748b; margin: 0 0 12px 0;">Untuk bantuan teknis dan pendampingan implementasi, hubungi narahubung resmi:</p>
            <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap; font-size: 0.86rem;">
                <span style="background: white; border: 1px solid #cbd5e1; padding: 6px 14px; border-radius: 8px; font-weight: 700; color: #065f46;">
                    <i class="fa-brands fa-whatsapp" style="color: #10b981;"></i> Helpdesk IT: 0819-3824-0819
                </span>
                <span style="background: white; border: 1px solid #cbd5e1; padding: 6px 14px; border-radius: 8px; font-weight: 700; color: #1e40af;">
                    <i class="fa-solid fa-envelope" style="color: #0284c7;"></i> <?= htmlspecialchars($email) ?>
                </span>
            </div>
        </div>

        <div style="text-align: center; margin-top: 35px; font-size: 0.78rem; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 12px;">
            &copy; 2026 <?= htmlspecialchars($nama_madrasah) ?> &bull; Sistem Digital Terintegrasi MASMIFHDA Platform &bull; Hak Cipta Dilindungi Undang-Undang
        </div>
    </div>

</div>

<script>
    // Shortcut Ctrl+P untuk print
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
            e.preventDefault();
            window.print();
        }
    });
</script>
</body>
</html>
