<?php
use App\Core\Branding;

$brand = Branding::get();
$logoUrl = !empty($inst['logo']) ? '/public/uploads/logo/' . $inst['logo'] : Branding::getInstitusiLogo();

$sekolah_nama = !empty($inst['nama']) ? $inst['nama'] : 'MADRASAH ALIYAH MIFTAHUL HUDA';
$nama_yayasan = !empty($inst['nama_yayasan']) ? $inst['nama_yayasan'] : 'YAYASAN PONDOK PESANTREN MIFTAHUL HUDA MATHLA\'UL ANWAR';

$alamatParts = [];
if (!empty($inst['alamat'])) $alamatParts[] = $inst['alamat'];
if (!empty($inst['desa'])) $alamatParts[] = 'Ds. ' . $inst['desa'];
if (!empty($inst['kecamatan'])) $alamatParts[] = 'Kec. ' . $inst['kecamatan'];
if (!empty($inst['kota'])) $alamatParts[] = $inst['kota'];
if (!empty($inst['provinsi'])) $alamatParts[] = 'Prov. ' . $inst['provinsi'];
if (!empty($inst['kodepos'])) $alamatParts[] = 'KP. ' . $inst['kodepos'];
$alamatLengkap = implode(', ', $alamatParts);
if (empty($alamatLengkap)) {
    $alamatLengkap = 'Jl. Tirtomoyo No. 083, Margorahayu, Ds. Tambak Jaya, Kec. Way Tenong, Lampung Barat, Prov. Lampung, KP. 34884';
}

$webUrl = !empty($inst['website']) ? $inst['website'] : 'masmifhda.sch.id';
$emailAddr = !empty($inst['email']) ? $inst['email'] : 'mimiftahulhuda083@gmail.com';
$kecamatan = !empty($inst['kecamatan']) ? $inst['kecamatan'] : 'Way Tenong';

$kepala_nama = $kamad['nama'] ?? 'SAMSUL ARIFIN, M.Pd';
$kepala_nip = $kamad['nip'] ?? '-';

$bulanIndo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$tgl_indo = date('j') . ' ' . $bulanIndo[(int)date('n')] . ' ' . date('Y');

$isPengawas = isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 5;
$backUrl = $isPengawas ? '/pengawas/pkkm?tab=penilaian' : '/admin/pkkm/instrumen';
$backText = $isPengawas ? 'Kembali ke Penilaian Pengawas' : 'Kembali ke Instrumen PKKM';

$activeTab = $_GET['tab'] ?? 'hierarki';
$filter_guru = isset($_GET['guru_id']) ? intval($_GET['guru_id']) : 0;
$filter_mapel = isset($_GET['mapel_id']) ? intval($_GET['mapel_id']) : 0;
$filter_jenis = isset($_GET['jenis']) ? trim($_GET['jenis']) : '';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Audit & Monitoring Perangkat Pembelajaran Guru — Bukti Fisik PKKM</title>
    <link rel="icon" type="image/png" href="<?= Branding::getInstitusiFavicon() ?>">
    <link rel="stylesheet" href="/public/assets/css/plus-jakarta-sans.css">
    <script src="/public/assets/js/lucide.min.js"></script>
    <style>
        :root {
            --primary: #0284c7;
            --primary-dark: #0284c7;
            --primary-light: #f0f9ff;
            --secondary: #0284c7;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
            --border-color: #e2e8f0;
            --card-bg: #ffffff;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 0;
            background: #f8fafc;
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            color: #1e293b;
            font-size: 13.5px;
            line-height: 1.5;
        }

        /* Top Action Bar */
        .top-navbar {
            background: #0f172a;
            color: #ffffff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            flex-wrap: wrap;
        }
        .top-nav-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .top-nav-title {
            font-weight: 700;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .pkkm-badge {
            background: #0ea5e9;
            color: #ffffff;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            padding: 3px 8px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .top-nav-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 0.82rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.15s ease;
        }
        .nav-btn-primary { background: #0284c7; color: #ffffff; }
        .nav-btn-primary:hover { background: #0284c7; color: #ffffff; }
        .nav-btn-secondary { background: #334155; color: #f8fafc; }
        .nav-btn-secondary:hover { background: #475569; color: #ffffff; }
        .nav-btn-outline { background: transparent; border: 1px solid #475569; color: #cbd5e1; }
        .nav-btn-outline:hover { background: rgba(255,255,255,0.08); color: #fff; }

        /* Main Wrapper */
        .main-wrapper {
            max-width: 1380px;
            margin: 24px auto 60px auto;
            padding: 0 20px;
        }

        /* Hero Banner */
        .hero-banner {
            background: linear-gradient(135deg, #0369a1 0%, #0284c7 50%, #0f766e 100%);
            border-radius: 14px;
            padding: 24px 28px;
            color: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(6, 95, 70, 0.25);
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }
        .hero-banner::after {
            content: '';
            position: absolute;
            right: -30px;
            bottom: -30px;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .hero-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }
        .hero-title-area {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .hero-logo {
            width: 60px;
            height: 60px;
            background: #ffffff;
            border-radius: 12px;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .hero-logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .hero-title {
            font-size: 1.35rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.3px;
        }
        .hero-sub {
            font-size: 0.88rem;
            color: #e0f2fe;
            margin: 4px 0 0 0;
        }
        .hero-verified-badge {
            background: rgba(255,255,255,0.16);
            border: 1px solid rgba(255,255,255,0.3);
            backdrop-filter: blur(8px);
            padding: 8px 14px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.82rem;
            font-weight: 700;
        }

        /* Stat Metrics Bar */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
            margin-top: 20px;
        }
        .metric-card {
            background: rgba(255,255,255,0.14);
            border: 1px solid rgba(255,255,255,0.22);
            backdrop-filter: blur(8px);
            border-radius: 10px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            color: #ffffff;
        }
        .metric-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .metric-val {
            font-size: 1.4rem;
            font-weight: 800;
            line-height: 1.1;
        }
        .metric-lbl {
            font-size: 0.75rem;
            color: #e0f2fe;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        /* Navigation Tabs */
        .view-tabs-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 4px;
            flex-wrap: wrap;
        }
        .view-tabs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 8px 8px 0 0;
            font-size: 0.88rem;
            font-weight: 700;
            color: #64748b;
            text-decoration: none;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-bottom: none;
            transition: all 0.2s ease;
        }
        .tab-btn:hover {
            color: #0284c7;
            background: #ffffff;
        }
        .tab-btn.active {
            color: #0284c7;
            background: #ffffff;
            border-color: #0284c7;
            border-bottom: 3px solid #0284c7;
            margin-bottom: -6px;
            box-shadow: 0 -2px 8px rgba(0,0,0,0.04);
        }
        .tab-counter {
            background: #e2e8f0;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 800;
            padding: 2px 7px;
            border-radius: 12px;
        }
        .tab-btn.active .tab-counter {
            background: #f0f9ff;
            color: #0284c7;
        }

        /* Filter Section */
        .filter-panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
            margin-bottom: 24px;
        }
        .filter-form {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .filter-group {
            flex: 1;
            min-width: 170px;
        }
        .filter-select, .filter-input {
            width: 100%;
            padding: 9px 12px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-family: inherit;
            font-size: 0.85rem;
            color: #1e293b;
            background: #f8fafc;
            transition: all 0.2s;
        }
        .filter-select:focus, .filter-input:focus {
            outline: none;
            border-color: #0ea5e9;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }
        .filter-btn-submit {
            padding: 9px 18px;
            background: #0284c7;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.15s;
        }
        .filter-btn-submit:hover { background: #0284c7; }
        .filter-btn-reset {
            padding: 9px 14px;
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .filter-btn-reset:hover { background: #e2e8f0; color: #1e293b; }

        /* Teacher Card (Hierarchy View) */
        .teacher-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            margin-bottom: 24px;
            overflow: hidden;
            transition: border-color 0.2s;
        }
        .teacher-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 14px rgba(0,0,0,0.06);
        }
        .teacher-card-head {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        .teacher-info {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .teacher-avatar {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.1rem;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(2,132,199,0.25);
        }
        .teacher-name {
            font-size: 1.05rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            line-height: 1.25;
        }
        .teacher-nip {
            font-size: 0.78rem;
            color: #64748b;
            margin-top: 3px;
        }
        .teacher-meta-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .docs-count-pill {
            background: #f0f9ff;
            color: #0369a1;
            border: 1px solid #bae6fd;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Mapel Section inside Teacher */
        .mapel-group {
            padding: 16px 20px 20px 20px;
            border-bottom: 1px dashed #e2e8f0;
        }
        .mapel-group:last-child {
            border-bottom: none;
        }
        .mapel-group-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 12px;
            background: #f1f5f9;
            padding: 8px 14px;
            border-radius: 8px;
            border-left: 4px solid #0284c7;
        }
        .mapel-title-text {
            font-size: 0.92rem;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .mapel-badge {
            background: #ffffff;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
        }

        /* Document Table inside Mapel */
        .doc-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.84rem;
        }
        .doc-table th {
            background: #ffffff;
            color: #64748b;
            font-weight: 700;
            text-align: left;
            padding: 8px 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .doc-table tr:hover td {
            background: #f8fafc;
        }

        /* Badges for Jenis Perangkat */
        .badge-jenis {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.2px;
            white-space: nowrap;
        }
        .badge-modul { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .badge-atp   { background: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; }
        .badge-prota { background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff; }
        .badge-kktp  { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .badge-other { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

        /* Action Buttons: Direct PDF Open */
        .btn-open-pdf {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #0284c7;
            color: #ffffff;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.15s ease;
            box-shadow: 0 1px 3px rgba(2,132,199,0.25);
            white-space: nowrap;
        }
        .btn-open-pdf:hover {
            background: #0284c7;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 3px 6px rgba(2,132,199,0.3);
        }
        .btn-preview-modal {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #334155;
            color: #f8fafc;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: background 0.15s ease;
            white-space: nowrap;
        }
        .btn-preview-modal:hover {
            background: #1e293b;
            color: #ffffff;
        }

        /* Unified Table View (Tab 2) */
        .flat-table-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            overflow: hidden;
            margin-bottom: 24px;
        }
        .flat-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }
        .flat-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            text-align: left;
            padding: 12px 14px;
            border-bottom: 2px solid #e2e8f0;
            font-size: 0.78rem;
            text-transform: uppercase;
        }
        .flat-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .flat-table tr:hover td {
            background: #f8fafc;
        }

        /* Modal PDF Viewer */
        .pdf-modal-backdrop {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(4px);
            z-index: 2000;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .pdf-modal-box {
            background: #ffffff;
            width: 100%;
            max-width: 1050px;
            height: 90vh;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.35);
        }
        .pdf-modal-header {
            background: #0f172a;
            color: #ffffff;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .pdf-modal-body {
            flex: 1;
            background: #475569;
            position: relative;
        }

        /* A4 Printable Sheet (Tab 3) */
        .printable-sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 12mm 14mm;
            color: #0f172a;
        }
        .sheet-outer-frame {
            border: 2px solid #0284c7;
            padding: 4px;
            height: 100%;
        }
        .sheet-inner-frame {
            border: 1px solid #0ea5e9;
            padding: 18px 20px;
            height: 100%;
        }
        .kop-box {
            display: flex;
            align-items: center;
            gap: 16px;
            border-bottom: 3px double #0284c7;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .kop-box img {
            width: 70px;
            height: 70px;
            object-fit: contain;
            flex-shrink: 0;
        }
        .kop-text {
            flex: 1;
            text-align: center;
        }
        .kop-text h1 {
            font-size: 11pt;
            font-weight: 700;
            color: #0369a1;
            margin: 0;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .kop-text h2 {
            font-size: 14pt;
            font-weight: 800;
            color: #0f172a;
            margin: 2px 0;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }
        .kop-text p {
            font-size: 8pt;
            color: #334155;
            margin: 1px 0;
            line-height: 1.35;
        }
        .ttd-box-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin-top: 30px;
            font-size: 9pt;
            text-align: center;
            page-break-inside: avoid;
        }

        /* Print Media Styles */
        @media print {
            body { background: #ffffff !important; }
            .top-navbar, .hero-banner, .view-tabs-container, .filter-panel, .teacher-card, .flat-table-card, .pdf-modal-backdrop {
                display: none !important;
            }
            .printable-sheet {
                display: block !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                width: 100% !important;
            }
            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>

    <!-- TOP NAVIGATION / ACTION BAR -->
    <header class="top-navbar">
        <div class="top-nav-left">
            <div class="top-nav-title">
                <i data-lucide="shield-check" style="color: #38bdf8; width: 22px; height: 22px;"></i>
                <span>Audit & Monitoring Perangkat Pembelajaran Guru</span>
            </div>
            <span class="pkkm-badge">Bukti Fisik PKKM 2026</span>
        </div>

        <div class="top-nav-actions">
            <a href="<?= $backUrl ?>" class="nav-btn nav-btn-outline">
                <i data-lucide="arrow-left" style="width: 15px; height: 15px;"></i>
                <span><?= $backText ?></span>
            </a>

            <button type="button" onclick="window.print()" class="nav-btn nav-btn-secondary">
                <i data-lucide="printer" style="width: 15px; height: 15px;"></i>
                <span>Cetak / PDF</span>
            </button>
        </div>
    </header>

    <div class="main-wrapper">

        <!-- HERO BANNER DOKUMEN DIGITAL PKKM -->
        <div class="hero-banner">
            <div class="hero-header">
                <div class="hero-title-area">
                    <div class="hero-logo">
                        <img src="<?= $logoUrl ?>" alt="Logo Institusi">
                    </div>
                    <div>
                        <h1 class="hero-title"><?= htmlspecialchars($sekolah_nama) ?></h1>
                        <p class="hero-sub">
                            Portal Verifikasi Dokumen Digital Guru — Komponen 1 & 2 (Standar Proses & Kurikulum) PKKM
                        </p>
                    </div>
                </div>

                <div class="hero-verified-badge">
                    <i data-lucide="check-circle-2" style="color: #38bdf8; width: 18px; height: 18px;"></i>
                    <span>Tersimpan di Cloud Server — Dapat Dibuka Langsung</span>
                </div>
            </div>

            <!-- EXECUTIVE METRICS -->
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-icon-box">
                        <i data-lucide="folder-check" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <div class="metric-val"><?= number_format($stats['total']) ?></div>
                        <div class="metric-lbl">Total Berkas PDF</div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon-box">
                        <i data-lucide="users" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <div class="metric-val"><?= $stats['guru_count'] ?></div>
                        <div class="metric-lbl">Guru Pengajar</div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon-box">
                        <i data-lucide="book-open" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <div class="metric-val"><?= number_format($stats['modul']) ?></div>
                        <div class="metric-lbl">Modul Ajar / RPP</div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon-box">
                        <i data-lucide="git-merge" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <div class="metric-val"><?= number_format($stats['atp']) ?></div>
                        <div class="metric-lbl">Silabus & ATP</div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon-box">
                        <i data-lucide="calendar" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <div class="metric-val"><?= number_format($stats['prota']) ?></div>
                        <div class="metric-lbl">Prota & Promes</div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon-box">
                        <i data-lucide="target" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <div class="metric-val"><?= number_format($stats['kktp']) ?></div>
                        <div class="metric-lbl">KKTP & Capaian</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- VIEW MODE TABS -->
        <div class="view-tabs-container">
            <div class="view-tabs">
                <a href="?tab=hierarki<?= $filter_guru ? '&guru_id='.$filter_guru : '' ?><?= $filter_mapel ? '&mapel_id='.$filter_mapel : '' ?><?= $filter_jenis ? '&jenis='.urlencode($filter_jenis) : '' ?><?= $q ? '&q='.urlencode($q) : '' ?>" 
                   class="tab-btn <?= ($activeTab === 'hierarki') ? 'active' : '' ?>">
                    <i data-lucide="layout-grid" style="width: 16px; height: 16px;"></i>
                    <span>Tampilan Per Guru & Mapel</span>
                    <span class="tab-counter"><?= count($groupedGurus) ?> Guru</span>
                </a>

                <a href="?tab=tabel<?= $filter_guru ? '&guru_id='.$filter_guru : '' ?><?= $filter_mapel ? '&mapel_id='.$filter_mapel : '' ?><?= $filter_jenis ? '&jenis='.urlencode($filter_jenis) : '' ?><?= $q ? '&q='.urlencode($q) : '' ?>" 
                   class="tab-btn <?= ($activeTab === 'tabel') ? 'active' : '' ?>">
                    <i data-lucide="list" style="width: 16px; height: 16px;"></i>
                    <span>Tabel Lengkap Semua Berkas</span>
                    <span class="tab-counter"><?= count($katalogPerangkat) ?> Berkas</span>
                </a>

                <a href="?tab=rekap<?= $filter_guru ? '&guru_id='.$filter_guru : '' ?>" 
                   class="tab-btn <?= ($activeTab === 'rekap') ? 'active' : '' ?>">
                    <i data-lucide="file-check" style="width: 16px; height: 16px;"></i>
                    <span>Lembar Rekap & Pengesahan A4</span>
                </a>
            </div>

            <div style="font-size: 0.82rem; color: #64748b; display: flex; align-items: center; gap: 6px;">
                <i data-lucide="info" style="width: 14px; height: 14px; color: #0284c7;"></i>
                <span>Klik tombol <strong>Buka PDF</strong> untuk memverifikasi dokumen fisik secara langsung.</span>
            </div>
        </div>

        <!-- FILTER & SEARCH PANEL (Hanya di mode Hierarki dan Tabel) -->
        <?php if($activeTab !== 'rekap'): ?>
        <div class="filter-panel">
            <form method="GET" action="/admin/pkkm/cetak-bukti/perangkat-guru" class="filter-form">
                <input type="hidden" name="tab" value="<?= htmlspecialchars($activeTab) ?>">

                <!-- Filter Guru -->
                <div class="filter-group">
                    <select name="guru_id" class="filter-select">
                        <option value="0">-- Semua Guru Pengajar (<?= count($gurus) ?>) --</option>
                        <?php foreach($gurus as $g): ?>
                            <option value="<?= $g['id'] ?>" <?= ($filter_guru == $g['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($g['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filter Mapel -->
                <div class="filter-group">
                    <select name="mapel_id" class="filter-select">
                        <option value="0">-- Semua Mata Pelajaran --</option>
                        <?php foreach($daftarMapel as $m): ?>
                            <option value="<?= $m['id'] ?>" <?= ($filter_mapel == $m['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['nama_mapel']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filter Jenis Perangkat -->
                <div class="filter-group">
                    <select name="jenis" class="filter-select">
                        <option value="">-- Semua Kategori Perangkat --</option>
                        <option value="Modul Ajar" <?= ($filter_jenis === 'Modul Ajar') ? 'selected' : '' ?>>Modul Ajar / RPP</option>
                        <option value="Alur Tujuan Pembelajaran (ATP)" <?= ($filter_jenis === 'Alur Tujuan Pembelajaran (ATP)') ? 'selected' : '' ?>>Alur Tujuan Pembelajaran (ATP)</option>
                        <option value="Capaian Pembelajaran (CP)" <?= ($filter_jenis === 'Capaian Pembelajaran (CP)') ? 'selected' : '' ?>>Capaian Pembelajaran (CP)</option>
                        <option value="Program Tahunan (Prota)" <?= ($filter_jenis === 'Program Tahunan (Prota)') ? 'selected' : '' ?>>Program Tahunan (Prota)</option>
                        <option value="Program Semester (Promes)" <?= ($filter_jenis === 'Program Semester (Promes)') ? 'selected' : '' ?>>Program Semester (Promes)</option>
                        <option value="KKTP" <?= ($filter_jenis === 'KKTP') ? 'selected' : '' ?>>KKTP</option>
                        <option value="Silabus / ATP" <?= ($filter_jenis === 'Silabus / ATP') ? 'selected' : '' ?>>Silabus</option>
                    </select>
                </div>

                <!-- Cari Text -->
                <div class="filter-group" style="flex: 1.5; min-width: 220px;">
                    <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari nama guru, mapel, judul, atau nama file..." class="filter-input">
                </div>

                <!-- Tombol Submit & Reset -->
                <button type="submit" class="filter-btn-submit">
                    <i data-lucide="filter" style="width: 14px; height: 14px;"></i> Saring
                </button>

                <?php if($filter_guru || $filter_mapel || $filter_jenis || $q): ?>
                    <a href="?tab=<?= htmlspecialchars($activeTab) ?>" class="filter-btn-reset">
                        <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i> Reset
                    </a>
                <?php endif; ?>
            </form>
        </div>
        <?php endif; ?>

        <!-- ========================================== -->
        <!-- TAB 1: TAMPILAN HIERARKI PER GURU & MAPEL -->
        <!-- ========================================== -->
        <?php if($activeTab === 'hierarki'): ?>
            <?php if(empty($groupedGurus)): ?>
                <div style="background:#fff; border-radius:12px; padding:60px 20px; text-align:center; border:1px solid #e2e8f0;">
                    <i data-lucide="folder-x" style="width:50px; height:50px; color:#94a3b8; stroke-width:1.5; margin-bottom:12px;"></i>
                    <h3 style="margin:0 0 6px 0; color:#475569;">Tidak Ditemukan Berkas</h3>
                    <p style="margin:0; color:#94a3b8; font-size:0.9rem;">Tidak ada dokumen perangkat yang cocok dengan kriteria filter yang Anda tentukan.</p>
                </div>
            <?php else: ?>
                <?php foreach($groupedGurus as $g): ?>
                <div class="teacher-card">
                    <!-- Teacher Head -->
                    <div class="teacher-card-head">
                        <div class="teacher-info">
                            <div class="teacher-avatar">
                                <?= strtoupper(substr($g['nama'], 0, 1)) ?>
                            </div>
                            <div>
                                <h3 class="teacher-name"><?= htmlspecialchars($g['nama']) ?></h3>
                                <div class="teacher-nip">
                                    NIP: <strong><?= htmlspecialchars($g['nip']) ?></strong> &bull; 
                                    Mengampu: <strong><?= count($g['mapel_list']) ?> Mata Pelajaran/Kelas</strong>
                                </div>
                            </div>
                        </div>

                        <div class="teacher-meta-right">
                            <span class="docs-count-pill">
                                <i data-lucide="file-text" style="width: 14px; height: 14px;"></i>
                                <?= $g['total_docs'] ?> Berkas Perangkat
                            </span>
                        </div>
                    </div>

                    <!-- Groups of Mapel & Kelas -->
                    <?php foreach($g['mapel_list'] as $mapelKey => $mGroup): ?>
                    <div class="mapel-group">
                        <div class="mapel-group-title">
                            <div class="mapel-title-text">
                                <i data-lucide="book" style="width: 16px; height: 16px; color: #0284c7;"></i>
                                <span><?= htmlspecialchars($mGroup['nama_mapel']) ?></span>
                                <span style="color:#64748b; font-weight:500;">(Kelas <?= htmlspecialchars($mGroup['nama_kelas']) ?>)</span>
                            </div>
                            <span class="mapel-badge"><?= count($mGroup['docs']) ?> Dokumen</span>
                        </div>

                        <div style="overflow-x: auto;">
                            <table class="doc-table">
                                <thead>
                                    <tr>
                                        <th style="width: 40px; text-align: center;">No</th>
                                        <th style="width: 180px;">Kategori Perangkat</th>
                                        <th>Nama Perangkat & Berkas Asli</th>
                                        <th style="width: 130px;">Waktu Unggah</th>
                                        <th style="width: 110px; text-align: center;">Status</th>
                                        <th style="width: 220px; text-align: center;">Aksi Buka Berkas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $dNo = 1; foreach($mGroup['docs'] as $doc): 
                                        $jenis = $doc['jenis_perangkat'] ?? 'Lainnya';
                                        $badgeClass = 'badge-other';
                                        if (stripos($jenis, 'modul') !== false || stripos($jenis, 'rpp') !== false) $badgeClass = 'badge-modul';
                                        elseif (stripos($jenis, 'atp') !== false || stripos($jenis, 'silabus') !== false) $badgeClass = 'badge-atp';
                                        elseif (stripos($jenis, 'prota') !== false || stripos($jenis, 'promes') !== false) $badgeClass = 'badge-prota';
                                        elseif (stripos($jenis, 'kktp') !== false || stripos($jenis, 'capaian') !== false) $badgeClass = 'badge-kktp';
                                        
                                        $status = $doc['status'] ?: 'Menunggu';
                                        $statusColor = ($status === 'Disetujui') ? '#0284c7' : '#d97706';
                                        $statusBg = ($status === 'Disetujui') ? '#f0f9ff' : '#fffbeb';
                                        $statusBorder = ($status === 'Disetujui') ? '#bae6fd' : '#fde68a';
                                    ?>
                                    <tr>
                                        <td align="center" style="color: #64748b; font-weight: 600;"><?= $dNo++ ?></td>
                                        <td>
                                            <span class="badge-jenis <?= $badgeClass ?>">
                                                <?= htmlspecialchars($jenis) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div style="font-weight: 700; color: #0f172a; font-size: 0.9rem;">
                                                <?= htmlspecialchars($doc['judul'] ?: $doc['nama_file']) ?>
                                            </div>
                                            <div style="font-size: 0.74rem; color: #64748b; display: flex; align-items: center; gap: 4px; margin-top: 3px;">
                                                <i data-lucide="file" style="width: 12px; height: 12px; color: #ef4444;"></i>
                                                <span><?= htmlspecialchars($doc['nama_file']) ?></span>
                                                <span style="color:#cbd5e1;">&bull;</span>
                                                <span style="color:#0284c7; font-weight:600;">✓ PDF Asli Server</span>
                                            </div>
                                        </td>
                                        <td style="font-size: 0.78rem; color: #64748b; white-space: nowrap;">
                                            <?= date('d M Y', strtotime($doc['created_at'])) ?><br>
                                            <span style="color:#94a3b8; font-size:0.72rem;"><?= date('H:i', strtotime($doc['created_at'])) ?> WIB</span>
                                        </td>
                                        <td align="center">
                                            <span style="background:<?= $statusBg ?>; color:<?= $statusColor ?>; border:1px solid <?= $statusBorder ?>; padding:3px 8px; border-radius:6px; font-size:0.72rem; font-weight:700;">
                                                <?= htmlspecialchars($status) ?>
                                            </span>
                                        </td>
                                        <td align="center">
                                            <div style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                                <!-- Tombol Langsung Buka File PDF di Tab Baru -->
                                                <a href="<?= htmlspecialchars($doc['file_path']) ?>" target="_blank" class="btn-open-pdf" title="Buka PDF di Tab Baru">
                                                    <i data-lucide="external-link" style="width: 13px; height: 13px;"></i>
                                                    <span>Buka File PDF</span>
                                                </a>

                                                <!-- Tombol Preview Cepat di Modal -->
                                                <button type="button" class="btn-preview-modal" onclick="openPdfModal('<?= htmlspecialchars($doc['file_path']) ?>', '<?= htmlspecialchars(addslashes($doc['judul'] ?: $doc['nama_file'])) ?>', '<?= htmlspecialchars(addslashes($g['nama'])) ?> - <?= htmlspecialchars(addslashes($mGroup['nama_mapel'])) ?>')">
                                                    <i data-lucide="eye" style="width: 13px; height: 13px;"></i>
                                                    <span>Preview</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>

        <!-- ============================================== -->
        <!-- TAB 2: TABEL LENGKAP SEMUA BERKAS (ALA KURIKULUM) -->
        <!-- ============================================== -->
        <?php if($activeTab === 'tabel'): ?>
            <div class="flat-table-card">
                <div style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
                    <div style="font-weight: 700; color: #0f172a; font-size: 0.95rem; display:flex; align-items:center; gap:8px;">
                        <i data-lucide="list" style="color: #0284c7;"></i>
                        <span>Daftar Seluruh Dokumen Perangkat Pembelajaran Guru</span>
                    </div>
                    <div style="font-size: 0.82rem; color: #64748b;">
                        Menampilkan <strong><?= count($katalogPerangkat) ?></strong> berkas
                    </div>
                </div>

                <div style="overflow-x: auto;">
                    <table class="flat-table">
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align: center;">No</th>
                                <th>Perangkat & File</th>
                                <th>Guru Pengunggah</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelas</th>
                                <th style="text-align: center;">Status</th>
                                <th>Waktu Unggah</th>
                                <th style="width: 220px; text-align: center;">Aksi Buka Berkas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($katalogPerangkat)): ?>
                                <tr>
                                    <td colspan="8" align="center" style="padding: 50px; color: #94a3b8;">
                                        Tidak ditemukan dokumen perangkat pembelajaran yang sesuai dengan kriteria filter.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $tNo = 1; foreach($katalogPerangkat as $row): 
                                    $jenis = $row['jenis_perangkat'] ?? 'Lainnya';
                                    $badgeClass = 'badge-other';
                                    if (stripos($jenis, 'modul') !== false || stripos($jenis, 'rpp') !== false) $badgeClass = 'badge-modul';
                                    elseif (stripos($jenis, 'atp') !== false || stripos($jenis, 'silabus') !== false) $badgeClass = 'badge-atp';
                                    elseif (stripos($jenis, 'prota') !== false || stripos($jenis, 'promes') !== false) $badgeClass = 'badge-prota';
                                    elseif (stripos($jenis, 'kktp') !== false || stripos($jenis, 'capaian') !== false) $badgeClass = 'badge-kktp';
                                    
                                    $status = $row['status'] ?: 'Menunggu';
                                    $statusColor = ($status === 'Disetujui') ? '#0284c7' : '#d97706';
                                    $statusBg = ($status === 'Disetujui') ? '#f0f9ff' : '#fffbeb';
                                    $statusBorder = ($status === 'Disetujui') ? '#bae6fd' : '#fde68a';
                                ?>
                                <tr>
                                    <td align="center" style="color: #64748b; font-weight: 600;"><?= $tNo++ ?></td>
                                    <td>
                                        <div style="font-weight: 700; color: #0f172a; font-size: 0.9rem;">
                                            <?= htmlspecialchars($row['judul'] ?: $row['nama_file']) ?>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 6px; margin-top: 3px; flex-wrap: wrap;">
                                            <span class="badge-jenis <?= $badgeClass ?>">
                                                <?= htmlspecialchars($jenis) ?>
                                            </span>
                                            <span style="font-size: 0.74rem; color: #64748b; display: inline-flex; align-items: center; gap: 3px;">
                                                <i data-lucide="file-text" style="width: 12px; height: 12px; color: #ef4444;"></i>
                                                <?= htmlspecialchars($row['nama_file']) ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($row['nama_guru']) ?></div>
                                        <div style="font-size: 0.74rem; color: #94a3b8;">NIP: <?= htmlspecialchars($row['nip_guru'] ?: '-') ?></div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #334155;"><?= htmlspecialchars($row['nama_mapel'] ?? '-') ?></div>
                                    </td>
                                    <td>
                                        <span style="display: inline-block; padding: 2px 7px; border-radius: 6px; background: #f1f5f9; font-weight: 700; font-size: 0.76rem; color: #475569;">
                                            Kelas <?= htmlspecialchars($row['nama_kelas'] ?? '-') ?>
                                        </span>
                                    </td>
                                    <td align="center">
                                        <span style="background:<?= $statusBg ?>; color:<?= $statusColor ?>; border:1px solid <?= $statusBorder ?>; padding:3px 8px; border-radius:6px; font-size:0.72rem; font-weight:700;">
                                            <?= htmlspecialchars($status) ?>
                                        </span>
                                    </td>
                                    <td style="font-size: 0.78rem; color: #64748b; white-space: nowrap;">
                                        <?= date('d M Y', strtotime($row['created_at'])) ?><br>
                                        <span style="font-size:0.72rem; color:#94a3b8;"><?= date('H:i', strtotime($row['created_at'])) ?> WIB</span>
                                    </td>
                                    <td align="center">
                                        <div style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                            <a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank" class="btn-open-pdf">
                                                <i data-lucide="external-link" style="width: 13px; height: 13px;"></i>
                                                <span>Buka File PDF</span>
                                            </a>
                                            <button type="button" class="btn-preview-modal" onclick="openPdfModal('<?= htmlspecialchars($row['file_path']) ?>', '<?= htmlspecialchars(addslashes($row['judul'] ?: $row['nama_file'])) ?>', '<?= htmlspecialchars(addslashes($row['nama_guru'])) ?> - <?= htmlspecialchars(addslashes($row['nama_mapel'])) ?>')">
                                                <i data-lucide="eye" style="width: 13px; height: 13px;"></i>
                                                <span>Preview</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- ============================================== -->
        <!-- TAB 3: LEMBAR CETAK REKAPITULASI & PENGESAHAN -->
        <!-- ============================================== -->
        <?php if($activeTab === 'rekap'): ?>
            <div class="printable-sheet">
                <div class="sheet-outer-frame">
                    <div class="sheet-inner-frame">
                        <!-- Kop Surat Resmi -->
                        <div class="kop-box">
                            <img src="<?= $logoUrl ?>" alt="Logo Madrasah">
                            <div class="kop-text">
                                <h1><?= htmlspecialchars($nama_yayasan); ?></h1>
                                <h2><?= htmlspecialchars($sekolah_nama); ?></h2>
                                <p><?= htmlspecialchars($alamatLengkap); ?></p>
                                <p>Website: <?= htmlspecialchars($webUrl); ?> | Email: <?= htmlspecialchars($emailAddr); ?></p>
                            </div>
                        </div>

                        <!-- Judul Lembar -->
                        <div style="text-align: center; margin-bottom: 16px; padding: 8px 12px; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px;">
                            <h3 style="margin: 0; font-size: 11pt; font-weight: 800; color: #0369a1; text-transform: uppercase;">
                                LEMBAR REKAPITULASI & VERIFIKASI PERANGKAT PEMBELAJARAN GURU
                            </h3>
                            <p style="margin: 3px 0 0 0; font-size: 8.5pt; color: #475569;">
                                Bukti Fisik Penilaian Kinerja Kepala Madrasah (PKKM) — Komponen 1 & 2
                            </p>
                        </div>

                        <!-- Tabel Matriks Guru -->
                        <table style="width: 100%; border-collapse: collapse; font-size: 8pt; margin-bottom: 16px;">
                            <thead>
                                <tr style="background: #f1f5f9; color: #0f172a;">
                                    <th style="border: 1px solid #94a3b8; padding: 6px; width: 30px;">No</th>
                                    <th style="border: 1px solid #94a3b8; padding: 6px; text-align: left;">Nama Guru Pengajar</th>
                                    <th style="border: 1px solid #94a3b8; padding: 6px; width: 80px;">NIP</th>
                                    <th style="border: 1px solid #94a3b8; padding: 6px; text-align: left;">Mata Pelajaran & Kelas</th>
                                    <th style="border: 1px solid #94a3b8; padding: 6px; width: 50px;">Modul / RPP</th>
                                    <th style="border: 1px solid #94a3b8; padding: 6px; width: 45px;">Silabus / ATP</th>
                                    <th style="border: 1px solid #94a3b8; padding: 6px; width: 50px;">Prota / Promes</th>
                                    <th style="border: 1px solid #94a3b8; padding: 6px; width: 45px;">KKTP / CP</th>
                                    <th style="border: 1px solid #94a3b8; padding: 6px; width: 50px; background: #e2e8f0;">Total</th>
                                    <th style="border: 1px solid #94a3b8; padding: 6px; width: 75px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $rNo = 1; foreach($matriksGuru as $mg): ?>
                                <tr>
                                    <td align="center" style="border: 1px solid #cbd5e1; padding: 5px;"><?= $rNo++ ?></td>
                                    <td style="border: 1px solid #cbd5e1; padding: 5px;"><strong><?= htmlspecialchars($mg['nama']) ?></strong></td>
                                    <td align="center" style="border: 1px solid #cbd5e1; padding: 5px;"><?= htmlspecialchars($mg['nip']) ?></td>
                                    <td style="border: 1px solid #cbd5e1; padding: 5px; font-size: 7.5pt;"><?= htmlspecialchars($mg['mapel']) ?> (<?= htmlspecialchars($mg['kelas']) ?>)</td>
                                    <td align="center" style="border: 1px solid #cbd5e1; padding: 5px; font-weight: 700;"><?= $mg['modul_cnt'] ?></td>
                                    <td align="center" style="border: 1px solid #cbd5e1; padding: 5px;"><?= $mg['atp_cnt'] ?></td>
                                    <td align="center" style="border: 1px solid #cbd5e1; padding: 5px;"><?= $mg['prota_cnt'] ?></td>
                                    <td align="center" style="border: 1px solid #cbd5e1; padding: 5px;"><?= $mg['kktp_cnt'] ?></td>
                                    <td align="center" style="border: 1px solid #cbd5e1; padding: 5px; background: #f0f9ff; font-weight: 700; color: #0369a1;"><?= $mg['total'] ?></td>
                                    <td align="center" style="border: 1px solid #cbd5e1; padding: 5px;">
                                        <span style="background:#f0f9ff; color:#0369a1; font-size:7pt; font-weight:700; padding:2px 5px; border-radius:3px;">Lengkap</span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <!-- Catatan & Pengesahan -->
                        <div style="font-size: 7.8pt; color: #475569; border-left: 3px solid #0ea5e9; padding-left: 8px; margin-bottom: 20px;">
                            <strong>Catatan Penelaahan Asesor/Pengawas:</strong> Seluruh berkas digital RPP, Modul Ajar, Silabus/ATP, Prota, Promes, dan KKTP guru di atas telah tersimpan secara resmi pada server MAS Miftahul Huda dan dapat dibuka serta ditelaah secara daring melalui portal audit sistem PKKM.
                        </div>

                        <div class="ttd-box-container">
                            <div>
                                <p style="margin: 0 0 50px 0;">
                                    Asesor / Pengawas Madrasah,<br>
                                    Kemenag Kabupaten Lampung Barat
                                </p>
                                <p style="margin: 0; font-weight: 700; text-decoration: underline;">
                                    ............................................................
                                </p>
                                <p style="margin: 2px 0 0 0;">NIP. ....................................................</p>
                            </div>

                            <div>
                                <p style="margin: 0 0 50px 0;">
                                    <?= htmlspecialchars($kecamatan); ?>, <?= $tgl_indo; ?><br>
                                    Kepala Madrasah,
                                </p>
                                <p style="margin: 0; font-weight: 700; text-decoration: underline;">
                                    <?= htmlspecialchars($kepala_nama); ?>
                                </p>
                                <p style="margin: 2px 0 0 0;">NIP. <?= htmlspecialchars($kepala_nip); ?></p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <!-- MODAL PDF VIEWER POPUP -->
    <div id="pdfViewerModal" class="pdf-modal-backdrop">
        <div class="pdf-modal-box">
            <div class="pdf-modal-header">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i data-lucide="file-text" style="color: #38bdf8; width: 20px; height: 20px;"></i>
                    <div>
                        <div id="pdfModalTitle" style="font-weight: 700; font-size: 0.95rem;">-</div>
                        <div id="pdfModalSub" style="font-size: 0.75rem; color: #94a3b8;">-</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <a id="pdfModalNewTab" href="#" target="_blank" class="nav-btn nav-btn-primary" style="padding: 6px 12px; font-size: 0.78rem;">
                        <i data-lucide="external-link" style="width: 14px; height: 14px;"></i> Buka Tab Baru
                    </a>
                    <button onclick="closePdfModal()" style="background: none; border: none; color: #cbd5e1; font-size: 1.6rem; cursor: pointer; line-height: 1;">&times;</button>
                </div>
            </div>
            <div class="pdf-modal-body">
                <iframe id="pdfIframe" src="" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/_pkkm_koleksi_modal.php'; ?>

    <script>
        function openPdfModal(url, title, sub) {
            document.getElementById('pdfModalTitle').innerText = title;
            document.getElementById('pdfModalSub').innerText = sub || 'Berkas Perangkat Pembelajaran Asli Guru';
            document.getElementById('pdfModalNewTab').href = url;
            document.getElementById('pdfIframe').src = url;
            document.getElementById('pdfViewerModal').style.display = 'flex';
        }

        function closePdfModal() {
            document.getElementById('pdfIframe').src = '';
            document.getElementById('pdfViewerModal').style.display = 'none';
        }

        window.addEventListener('click', function(e) {
            if (e.target.id === 'pdfViewerModal') {
                closePdfModal();
            }
        });

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
</body>
</html>
