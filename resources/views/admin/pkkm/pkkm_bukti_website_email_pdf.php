<?php
use App\Core\Branding;

$brand = Branding::get();
$logoUrl = !empty($inst['logo']) ? '/public/uploads/logo/' . $inst['logo'] : Branding::getInstitusiLogo();

$sekolah_nama = !empty($inst['nama']) ? $inst['nama'] : 'Madrasah Aliyah MIFTAHUL HUDA';
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
$email = $emailAddr;

$kepala_nama = $kamad['nama'] ?? 'SAMSUL ARIFIN, M.Pd';
$kepala_nip = $kamad['nip'] ?? '-';
$kecamatan = !empty($inst['kecamatan']) ? $inst['kecamatan'] : 'Way Tenong';
$kabupaten = !empty($inst['kota']) ? $inst['kota'] : 'Lampung Barat';


// Format tanggal Indonesia
$bulanIndo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$tgl_indo = date('j') . ' ' . $bulanIndo[(int)date('n')] . ' ' . date('Y');

$isPengawas = isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 5;
$backUrl = $isPengawas ? '/pengawas/pkkm?tab=penilaian' : '/admin/pkkm/instrumen';
$backText = $isPengawas ? 'Kembali ke Penilaian' : 'Kembali ke Instrumen';

$screenshotPath = __DIR__ . '/../../public/uploads/website/screenshot_landing.png';
$hasScreenshot = file_exists($screenshotPath);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Fisik PKKM - Website & Email Resmi - <?php echo htmlspecialchars($sekolah_nama); ?></title>
    <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($brand['favicon']); ?>">
    <link rel="stylesheet" href="/public/assets/css/plus-jakarta-sans.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 14mm 10mm 14mm;
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            background: #e2e8f0;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #0f172a;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Top Action Bar (Hanya Tampil di Layar) */
        .no-print-bar {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            color: #ffffff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .bar-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 0.92rem;
        }
        .bar-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 0.72rem;
            letter-spacing: 0.5px;
        }
        .bar-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }
        .bar-btn {
            background: #ffffff;
            color: #0f172a;
            border: none;
            padding: 7px 14px;
            border-radius: 7px;
            font-weight: 700;
            font-size: 0.82rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        }
        .bar-btn:hover {
            background: #f0fdf4;
            transform: translateY(-1px);
        }
        .bar-btn-secondary {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .bar-btn-secondary:hover {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        /* Lembar Kertas A4 Pas 1 Halaman */
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 12mm 15mm 12mm 15mm;
            margin: 15px auto;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            position: relative;
            box-sizing: border-box;
        }

        /* Kop Surat Resmi Sesuai Pengaturan SIAKAD */
        .kop-surat-preview {
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 14px;
            position: relative;
        }
        .kop-surat-preview img {
            width: 72px;
            height: 72px;
            object-fit: contain;
            position: absolute;
            left: 5px;
        }
        .kop-text-preview {
            text-align: center;
            flex: 1;
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            padding: 0 75px;
        }
        .kop-text-preview h1 {
            margin: 0;
            font-size: <?php echo !empty($inst['ukuran_font_yayasan']) ? (int)$inst['ukuran_font_yayasan'] : 15; ?>px;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.25;
            color: #000;
        }
        .kop-text-preview h2 {
            margin: 2px 0 3px 0;
            font-size: <?php echo !empty($inst['ukuran_font_madrasah']) ? (int)$inst['ukuran_font_madrasah'] : 18; ?>px;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
            color: #000;
        }
        .kop-text-preview p {
            margin: 1px 0;
            font-size: 8.8pt;
            line-height: 1.35;
            color: #111;
        }

        /* Judul Dokumen (Bukan Surat, Tanpa Nomor) */
        .doc-title-wrap {
            text-align: center;
            margin-bottom: 10px;
        }
        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .doc-sub {
            font-size: 8.8pt;
            color: #64748b;
            font-weight: 600;
            margin-top: 2px;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }

        /* Info Grid Ringkas Website & Email */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 10px;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .info-box {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 8px 12px;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .info-box.web {
            border-left: 4px solid #0284c7;
            background: #ecfdf5;
        }
        .info-box.mail {
            border-left: 4px solid #2563eb;
            background: #eff6ff;
        }
        .info-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.72rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-val-link {
            font-size: 0.88rem;
            font-weight: 700;
            color: #1e3a8a;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .info-val-link.mail {
            color: #1d4ed8;
        }
        .info-val-link:hover {
            text-decoration: underline;
        }
        .info-status {
            font-size: 0.68rem;
            color: #64748b;
        }

        /* Browser Mockup Frame untuk Screenshot */
        .browser-mockup {
            border: 1.5px solid #cbd5e1;
            border-radius: 9px;
            overflow: hidden;
            background: #ffffff;
            margin-bottom: 8px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.06);
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .browser-header {
            background: #f1f5f9;
            padding: 6px 12px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .browser-dots {
            display: flex;
            gap: 4px;
        }
        .browser-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }
        .browser-dot.red { background: #ef4444; }
        .browser-dot.yellow { background: #f59e0b; }
        .browser-dot.green { background: #38bdf8; }
        .browser-url-bar {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 3px 10px;
            font-size: 0.72rem;
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex: 1;
        }
        .url-text-link {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #1e40af;
            font-weight: 700;
            font-family: monospace;
            text-decoration: none;
        }
        .url-text-link:hover {
            text-decoration: underline;
        }
        .badge-live-ssl {
            background: #dcfce7;
            color: #15803d;
            font-size: 0.62rem;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 999px;
            border: 1px solid #bbf7d0;
        }

        .browser-body {
            position: relative;
            background: #06090f;
            min-height: 440px;
            max-height: 470px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .screenshot-img {
            width: 100%;
            height: 460px;
            object-fit: cover;
            object-position: top center;
            display: block;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        .screenshot-img:hover {
            opacity: 0.96;
        }

        /* Fallback Mockup Tampilan Landing Page */
        .landing-mock-content {
            width: 100%;
            height: 100%;
            min-height: 440px;
            padding: 24px 28px;
            background: radial-gradient(ellipse 80% 50% at 50% -10%, rgba(16,185,129,0.22) 0%, transparent 70%), #06090f;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-decoration: none;
        }
        .mock-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .mock-brand {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .mock-brand img {
            width: 28px;
            height: 28px;
            background: #fff;
            border-radius: 6px;
            padding: 2px;
        }
        .mock-brand-name {
            font-weight: 800;
            font-size: 0.85rem;
            color: #fff;
        }
        .mock-brand-name span {
            color: #34d399;
        }
        .mock-nav-links {
            display: flex;
            gap: 10px;
            font-size: 0.68rem;
            color: #94a3b8;
        }
        .mock-hero-text {
            text-align: center;
            margin: 8px 0;
        }
        .mock-hero-badge {
            display: inline-block;
            background: rgba(16,185,129,0.15);
            border: 1px solid rgba(16,185,129,0.3);
            color: #34d399;
            font-size: 0.62rem;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .mock-hero-title {
            font-size: 1.15rem;
            font-weight: 900;
            line-height: 1.2;
            color: #ffffff;
            margin: 0;
        }
        .mock-hero-sub {
            font-size: 0.7rem;
            color: #94a3b8;
            margin-top: 3px;
        }
        .mock-modules-bar {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            padding-top: 8px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }
        .mock-mod-item {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 6px;
            padding: 6px;
            text-align: center;
        }
        .mock-mod-icon {
            font-size: 0.85rem;
            margin-bottom: 2px;
        }
        .mock-mod-label {
            font-size: 0.65rem;
            font-weight: 700;
            color: #e2e8f0;
        }

        .browser-footer-note {
            padding: 5px 12px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            font-size: 0.72rem;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Box Keterangan Email Sederhana */
        .email-simple-box {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 8px 14px;
            background: #f8fafc;
            margin-bottom: 10px;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            font-size: 0.76rem;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .email-simple-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #dbeafe;
            color: #1d4ed8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }



        /* Modal Upload Overlay */
        #uploadModal {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.6);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .upload-modal-card {
            background: #ffffff;
            border-radius: 12px;
            width: 90%;
            max-width: 460px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        /* Media Print */
        @media print {
            body {
                background: #ffffff;
                color: #000000;
            }
            .no-print-bar {
                display: none !important;
            }
            .page {
                width: 100%;
                min-height: auto;
                max-height: none;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
            .browser-mockup, .info-box, .email-simple-box {
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- TOP TOOLBAR (Khusus Tampilan Layar) -->
    <div class="no-print-bar">
        <div class="bar-title">
            <span>📄 Dokumen Bukti Fisik PKKM</span>
            <span class="bar-badge">Indikator 1.4.3 & 1.6.1</span>
        </div>
        <div class="bar-actions">
            <a href="<?php echo $backUrl; ?>" class="bar-btn bar-btn-secondary">
                ⬅ <?php echo $backText; ?>
            </a>
            <a href="/admin/pkkm/master-berkas" class="bar-btn bar-btn-secondary" title="Buka Daftar Berkas Master PKKM">
                📁 Daftar Berkas
            </a>
            <button type="button" onclick="openKoleksiBuktiModal()" class="bar-btn bar-btn-secondary" title="Daftar Tautan Semua Bukti Fisik PKKM">
                📋 Link Bukti Kita
            </button>
            <a href="https://masmifhda.sch.id" target="_blank" class="bar-btn bar-btn-secondary" title="Buka website di tab baru">
                🌐 Kunjungi Website Live ↗
            </a>
            <button type="button" class="bar-btn bar-btn-secondary" onclick="openUploadModal()" title="Upload Screenshot Beranda Anda">
                📷 Upload Screenshot
            </button>
            <button type="button" onclick="window.print()" class="bar-btn" title="Cetak atau simpan sebagai file PDF 1 lembar">
                🖨️ Cetak / Simpan PDF (1 Halaman)
            </button>
        </div>
    </div>

    <!-- MODAL UPLOAD SCREENSHOT -->
    <div id="uploadModal">
        <div class="upload-modal-card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <h6 style="margin:0; font-weight:800; font-size:1rem; color:#0f172a;">Upload Screenshot Landing Page</h6>
                <button type="button" onclick="closeUploadModal()" style="border:none; background:transparent; font-size:1.2rem; cursor:pointer;">&times;</button>
            </div>
            <p style="font-size:0.8rem; color:#64748b; margin-bottom:14px;">
                Pilih file gambar screenshot beranda <code>masmifhda.sch.id</code> (PNG / JPG / WEBP). Gambar akan langsung tampil di dalam frame browser di bawah ini.
            </p>
            <form action="/pengawas/pkkm/upload-website-screenshot" method="POST" enctype="multipart/form-data">
                <input type="file" name="screenshot" accept="image/*" required style="width:100%; font-size:0.85rem; margin-bottom:16px; padding:8px; border:1px solid #cbd5e1; border-radius:6px;">
                <div style="display:flex; justify-content:flex-end; gap:8px;">
                    <button type="button" onclick="closeUploadModal()" style="padding:7px 14px; border-radius:6px; border:1px solid #cbd5e1; background:#f8fafc; font-size:0.82rem; cursor:pointer;">Batal</button>
                    <button type="submit" style="padding:7px 16px; border-radius:6px; border:none; background:#0284c7; color:#fff; font-weight:700; font-size:0.82rem; cursor:pointer;">Simpan & Tampilkan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================================================== -->
    <!-- LEMBAR DOKUMEN A4: PAS 1 HALAMAN TANPA SURAT         -->
    <!-- ==================================================== -->
    <div class="page">
        <!-- KOP SURAT MADRASAH RESMI (SESUAI PENGATURAN SIAKAD) -->
        <div class="kop-surat-preview">
            <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Logo Madrasah">
            <div class="kop-text-preview">
                <?php if (!empty($nama_yayasan)): ?>
                    <h1><?php echo htmlspecialchars($nama_yayasan); ?></h1>
                <?php endif; ?>
                <h2><?php echo htmlspecialchars($sekolah_nama); ?></h2>
                <p><?php echo htmlspecialchars($alamatLengkap); ?></p>
                <p>Website: <?php echo htmlspecialchars($webUrl); ?> | Email: <?php echo htmlspecialchars($emailAddr); ?></p>
            </div>
        </div>

            <!-- JUDUL BUKTI FISIK (BUKAN SURAT, TANPA NOMOR) -->
            <div class="doc-title-wrap">
                <div class="doc-title">BUKTI FISIK DOKUMENTASI WEBSITE & EMAIL RESMI</div>
                <div class="doc-sub">Standar Penilaian Kinerja Kepala Madrasah (PKKM) &bull; Indikator 1.4.3 & 1.6.1</div>
            </div>

            <!-- INFO IDENTITAS LINK & DOMAIN CEPAT -->
            <div class="info-grid">
                <div class="info-box web">
                    <div class="info-header">
                        <span>🌐 Website Resmi Madrasah</span>
                        <span class="badge-live-ssl">SSL / HTTPS Aktif</span>
                    </div>
                    <div>
                        <a href="https://masmifhda.sch.id" target="_blank" class="info-val-link" title="Klik untuk membuka website">
                            https://masmifhda.sch.id <span style="font-size:0.75rem;">↗</span>
                        </a>
                    </div>
                    <div class="info-status">
                        Domain resmi kemenag/sekolah berlisensi <strong>.sch.id</strong> aktif 24 jam.
                    </div>
                </div>

                <div class="info-box mail">
                    <div class="info-header">
                        <span>✉️ Email Kedinasan Madrasah</span>
                        <span style="color:#2563eb; font-weight:700; font-size:0.62rem;">Terverifikasi</span>
                    </div>
                    <div>
                        <a href="mailto:<?php echo htmlspecialchars($email); ?>" class="info-val-link mail" title="Klik untuk mengirim email">
                            <?php echo htmlspecialchars($email); ?> <span style="font-size:0.75rem;">↗</span>
                        </a>
                    </div>
                    <div class="info-status">
                        Kanal persuratan resmi, akun EMIS 4.0, SIMPATIKA, dan layanan madrasah.
                    </div>
                </div>
            </div>

            <!-- TANGKAPAN LAYAR (SCREENSHOT) BROWSER MOCKUP -->
            <div class="browser-mockup">
                <div class="browser-header">
                    <div class="browser-dots">
                        <div class="browser-dot red"></div>
                        <div class="browser-dot yellow"></div>
                        <div class="browser-dot green"></div>
                    </div>
                    <div class="browser-url-bar">
                        <a href="https://masmifhda.sch.id" target="_blank" class="url-text-link" title="Klik untuk mengunjungi website langsung">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color:#38bdf8;"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <span>https://masmifhda.sch.id</span>
                            <span style="color:#0284c7; font-size:0.68rem; margin-left:4px;">↗</span>
                        </a>
                        <span class="badge-live-ssl">Live &bull; Terhubung</span>
                    </div>
                </div>

                <div class="browser-body">
                    <?php if ($hasScreenshot): ?>
                        <a href="https://masmifhda.sch.id" target="_blank" style="display:block; width:100%; text-decoration:none;" title="Klik untuk buka website masmifhda.sch.id">
                            <img src="/public/uploads/website/screenshot_landing.png?v=<?php echo filemtime($screenshotPath); ?>" alt="Screenshot Landing Page masmifhda.sch.id" class="screenshot-img">
                        </a>
                    <?php else: ?>
                        <!-- Tampilan Preview Visual Landing Page Asli MIS Miftahul Huda -->
                        <a href="https://masmifhda.sch.id" target="_blank" class="landing-mock-content" title="Klik untuk membuka website masmifhda.sch.id">
                            <div class="mock-nav">
                                <div class="mock-brand">
                                    <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Logo">
                                    <div class="mock-brand-name">MIS <span>MIFTAHUL HUDA</span></div>
                                </div>
                                <div class="mock-nav-links">
                                    <span>Beranda</span>
                                    <span>Profil</span>
                                    <span>Akademik</span>
                                    <span>PPDB</span>
                                    <span style="color:#34d399; font-weight:700;">Portal Masuk ↗</span>
                                </div>
                            </div>

                            <div class="mock-hero-text">
                                <div class="mock-hero-badge">Portal Digital Madrasah Terintegrasi</div>
                                <h4 class="mock-hero-title">Madrasah Maju, Bermutu, dan Berkelanjutan</h4>
                                <p class="mock-hero-sub">Pusat Informasi & Layanan Digital Resmi MIS Miftahul Huda Mathla'ul Anwar</p>
                            </div>

                            <div class="mock-modules-bar">
                                <div class="mock-mod-item">
                                    <div class="mock-mod-icon">📊</div>
                                    <div class="mock-mod-label">Portal SIAKAD</div>
                                </div>
                                <div class="mock-mod-item">
                                    <div class="mock-mod-icon">📝</div>
                                    <div class="mock-mod-label">SPMB Online</div>
                                </div>
                                <div class="mock-mod-item">
                                    <div class="mock-mod-icon">💻</div>
                                    <div class="mock-mod-label">CBT Ujian</div>
                                </div>
                                <div class="mock-mod-item">
                                    <div class="mock-mod-icon">📚</div>
                                    <div class="mock-mod-label">E-Learning</div>
                                </div>
                            </div>
                        </a>
                    <?php endif; ?>
                </div>

                <div class="browser-footer-note">
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle; margin-right:3px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        Tangkapan layar beranda website madrasah yang dapat diakses publik, santri, dan dewan guru secara real-time.
                    </span>
                    <a href="https://masmifhda.sch.id" target="_blank" style="color:#0284c7; text-decoration:none; font-weight:700;">Kunjungi Website ↗</a>
                </div>
            </div>

            <!-- KETERANGAN EMAIL BIASA AJA -->
            <div class="email-simple-box">
                <div class="email-simple-icon">✉️</div>
                <div style="flex:1;">
                    <div style="font-weight:700; color:#0f172a; margin-bottom:1px;">
                        Email Resmi: <a href="mailto:<?php echo htmlspecialchars($email); ?>" style="color:#1d4ed8; text-decoration:none;"><?php echo htmlspecialchars($email); ?></a>
                    </div>
                    <div style="font-size:0.72rem; color:#64748b; line-height:1.35;">
                        Digunakan secara aktif oleh pimpinan dan operator madrasah untuk korespondensi resmi Kementerian Agama, EMIS 4.0, SIMPATIKA, RDM, dan layanan publik.
                    </div>
                </div>
            </div>

            <!-- FOOTER DOKUMEN RINGKAS -->
            <div style="margin-top: 14px; padding-top: 8px; border-top: 1px dashed #cbd5e1; font-size: 0.72rem; color: #64748b; display: flex; justify-content: space-between; align-items: center; font-family: 'Plus Jakarta Sans', system-ui, sans-serif;">
                <span>Dokumen Bukti Fisik Digital &bull; MIS Miftahul Huda Mathla'ul Anwar</span>
                <span style="font-weight:600; color:#0284c7;">https://masmifhda.sch.id</span>
            </div>
    </div>

    <script>
        function openUploadModal() {
            document.getElementById('uploadModal').style.display = 'flex';
        }
        function closeUploadModal() {
            document.getElementById('uploadModal').style.display = 'none';
        }
        // Tutup modal jika klik di luar box
        window.onclick = function(event) {
            let modal = document.getElementById('uploadModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
    <?php include (file_exists(__DIR__ . '/_pkkm_koleksi_modal.php') ? __DIR__ . '/_pkkm_koleksi_modal.php' : __DIR__ . '/pkkm/_pkkm_koleksi_modal.php'); ?>
</body>
</html>
