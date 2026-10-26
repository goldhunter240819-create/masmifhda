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
$kecamatan = !empty($inst['kecamatan']) ? $inst['kecamatan'] : 'Way Tenong';
$kabupaten = !empty($inst['kota']) ? $inst['kota'] : 'Lampung Barat';

$kepala_nama = $kamad['nama'] ?? 'SAMSUL ARIFIN, M.Pd';
$kepala_nip = $kamad['nip'] ?? '-';
$komite_nama = $inst['nama_komite'] ?? 'H. MASRUR, S.Pd';

$bulanIndo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$tgl_indo = date('j') . ' ' . $bulanIndo[(int)date('n')] . ' ' . date('Y');

$isPengawas = isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 5;
$backUrl = $isPengawas ? '/pengawas/pkkm?tab=penilaian' : '/admin/pkkm/instrumen';
$backText = $isPengawas ? 'Kembali ke Penilaian' : 'Kembali ke Instrumen';

// Data Misi Strategis Madrasah
$misiList = [
    [
        'no' => 1,
        'title' => 'Pembelajaran Keagamaan Terarah & Istiqomah',
        'desc' => 'Menyelenggarakan proses pembelajaran keagamaan dan pembiasaan ibadah praktis (shalat dhuha, shalat zhuhur berjamaah, dan tadarrus) secara konsisten dan terarah.'
    ],
    [
        'no' => 2,
        'title' => 'Pembinaan Karakter & Akhlakul Karimah',
        'desc' => 'Membina kedisiplinan, sopan santun, keteladanan, adab islami, serta kepedulian sosial peserta didik berbasis nilai Rahmatan Lil \'Alamin.'
    ],
    [
        'no' => 3,
        'title' => 'Inovasi Pembelajaran Aktif & Pemanfaatan IPTEK',
        'desc' => 'Mengembangkan pembelajaran aktif, inovatif, kreatif, dan menyenangkan melalui integrasi sarana media TIK digital bagi pendidik serta peserta didik.'
    ],
    [
        'no' => 4,
        'title' => 'Pengembangan Minat, Bakat & Prestasi Kesiswaan',
        'desc' => 'Mengoptimalkan pembinaan potensi kesiswaan melalui kegiatan ekstrakurikuler kepanduan pramuka, kesenian Islam, olahraga, dan bimbingan kompetisi (KSM/AKSIOMA).'
    ],
    [
        'no' => 5,
        'title' => 'Madrasah Asri, Sehat & Sinergi Kemitraan',
        'desc' => 'Membudayakan lingkungan madrasah yang bersih, asri, sehat, dan ramah anak, serta menjalin kemitraan harmonis bersama orang tua, komite, dan masyarakat.'
    ]
];

// 4 Sasaran Mutu Lulusan
$sasaranMutu = [
    [
        'pilar' => 'Pilar I : Pondasi Akidah & Ibadah',
        'sub' => 'Disiplin shalat fardhu 5 waktu, hafal bacaan shalat dengan tartil, hafal 30 doa harian, dan berbakti kepada orang tua serta guru.',
        'color' => '#0284c7',
        'bg' => '#ecfdf5',
        'border' => '#a7f3d0'
    ],
    [
        'pilar' => 'Pilar II : Tahfidz Qur\'an Juz 30',
        'sub' => 'Tuntas hafalan minimal Juz 30 secara mutqin dengan makharijul huruf yang fasih, dan memiliki sertifikat tahfidz saat wisuda kelulusan.',
        'color' => '#2563eb',
        'bg' => '#eff6ff',
        'border' => '#bfdbfe'
    ],
    [
        'pilar' => 'Pilar III : Akademik & Prestasi 100%',
        'sub' => 'Lulus 100% Asesmen Madrasah (UM), meraih kejuaraan di tingkat kecamatan/kabupaten, dan berdaya saing masuk MTs/SMP unggulan.',
        'color' => '#d97706',
        'bg' => '#fffbeb',
        'border' => '#fde68a'
    ],
    [
        'pilar' => 'Pilar IV : Literasi Sains & Digital',
        'sub' => 'Terampil mengoperasikan komputer lab untuk asesmen digital (ANBK/CBT) serta aktif memanfaatkan buku perpustakaan & pojok baca kelas.',
        'color' => '#4f46e5',
        'bg' => '#eef2ff',
        'border' => '#c7d2fe'
    ]
];

// QR Verification URL
$verifyUrl = "https://masmifhda.sch.id/verifikasi/pkkm-visi-misi-2026?auth=" . md5($sekolah_nama . 'VISI-MISI-2026');
$qrCodeSrc = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&margin=0&data=" . urlencode($verifyUrl);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Visi, Misi & Sasaran Mutu - <?php echo htmlspecialchars($sekolah_nama); ?></title>
    <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($brand['favicon']); ?>">
    <link rel="stylesheet" href="/public/assets/css/plus-jakarta-sans.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 7mm 10mm 7mm 10mm;
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            background: #e2e8f0;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
            font-size: 8.5pt;
            line-height: 1.3;
            color: #0f172a;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .no-print-bar {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            color: #ffffff;
            padding: 12px 24px;
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
            font-size: 0.95rem;
        }
        .bar-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }
        .bar-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .bar-btn {
            background: #ffffff;
            color: #0f172a;
            border: none;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        }
        .bar-btn:hover { background: #f0fdf4; transform: translateY(-1px); }
        .bar-btn-secondary {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .bar-btn-secondary:hover { background: rgba(255, 255, 255, 0.25); color: #ffffff; }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 7mm 10mm;
            margin: 15px auto;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            position: relative;
        }

        /* Bingkai Eksekutif Plang Kelembagaan */
        .outer-frame {
            border: 2px solid #0f172a;
            padding: 3px;
            border-radius: 10px;
            background: #ffffff;
            position: relative;
        }
        .inner-frame {
            border: 1px solid #d97706;
            padding: 8px 12px;
            border-radius: 7px;
            background: #ffffff;
        }

        /* Kop Surat Resmi Sesuai Standar Madrasah */
        .kop-surat-preview {
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 2.5px solid #0f172a;
            padding-bottom: 5px;
            margin-bottom: 6px;
            position: relative;
        }
        .kop-surat-preview img {
            width: 62px;
            height: 62px;
            object-fit: contain;
            position: absolute;
            left: 5px;
        }
        .kop-text-preview {
            text-align: center;
            flex: 1;
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            padding: 0 65px;
        }
        .kop-text-preview h1 {
            margin: 0;
            font-size: <?php echo !empty($inst['ukuran_font_yayasan']) ? (int)$inst['ukuran_font_yayasan'] : 13; ?>px;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
            color: #0f172a;
        }
        .kop-text-preview h2 {
            margin: 1px 0 2px 0;
            font-size: <?php echo !empty($inst['ukuran_font_madrasah']) ? (int)$inst['ukuran_font_madrasah'] : 16; ?>px;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
            color: #0f172a;
        }
        .kop-text-preview p {
            margin: 1px 0;
            font-size: 8pt;
            line-height: 1.25;
            color: #111;
        }

        /* Header Dokumen / Plang Title */
        .doc-header-banner {
            text-align: center;
            margin-bottom: 8px;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .sk-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f0fdf4;
            color: #1e40af;
            border: 1px solid #86efac;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 6.8pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }
        .main-display-title {
            font-size: 12pt;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin: 0;
            line-height: 1.2;
        }
        .main-display-sub {
            font-size: 8.5pt;
            font-weight: 700;
            color: #0f172a;
            margin-top: 1px;
        }

        /* 3D Acrylic Executive Wall Plaque Card for VISI */
        .visi-plaque {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1e40af 100%);
            border: 1.5px solid #d97706;
            border-radius: 9px;
            padding: 9px 14px;
            color: #ffffff;
            text-align: center;
            margin-bottom: 8px;
            position: relative;
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.15);
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .visi-top-tag {
            font-size: 7.2pt;
            font-weight: 800;
            color: #fef08a;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-bottom: 3px;
        }
        .visi-statement {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12.5pt;
            font-weight: bold;
            font-style: italic;
            line-height: 1.35;
            color: #ffffff;
            margin: 0 0 6px 0;
            text-shadow: 0 1px 3px rgba(0,0,0,0.3);
            padding: 0 15px;
        }
        .visi-chips-row {
            display: flex;
            justify-content: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        .visi-chip {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #f0fdf4;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 6.8pt;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        /* Section Container */
        .section-panel {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 6px 10px;
            margin-bottom: 7px;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .panel-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 4px;
            margin-bottom: 5px;
            border-bottom: 1.5px solid #f1f5f9;
        }
        .panel-title-text {
            font-size: 8pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .panel-badge-pill {
            background: #f1f5f9;
            color: #475569;
            font-size: 6.5pt;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
        }

        /* Misi 5 Items List Grid */
        .misi-list-wrap {
            display: flex;
            flex-direction: column;
            gap: 4.5px;
        }
        .misi-item-card {
            display: flex;
            gap: 8px;
            align-items: flex-start;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #0284c7;
            border-radius: 6px;
            padding: 4px 8px;
        }
        .misi-num-badge {
            background: #0284c7;
            color: #ffffff;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7pt;
            font-weight: 900;
            flex-shrink: 0;
            margin-top: 1px;
        }
        .misi-content {
            flex: 1;
        }
        .misi-item-title {
            font-size: 7.4pt;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }
        .misi-item-desc {
            font-size: 6.8pt;
            color: #334155;
            line-height: 1.25;
            margin-top: 1px;
        }

        /* 4 Sasaran Mutu Bento Grid */
        .mutu-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 5px;
        }
        .mutu-card {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 5px 8px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }
        .mutu-tag {
            font-size: 7pt;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 2px;
        }
        .mutu-desc {
            font-size: 6.7pt;
            color: #334155;
            line-height: 1.25;
            font-weight: 500;
        }

        /* Sosialisasi & Notulen Note Box */
        .meta-notice-box {
            background: #f0fdf4;
            border: 1px dashed #86efac;
            border-radius: 6px;
            padding: 4px 8px;
            font-size: 6.8pt;
            color: #166534;
            line-height: 1.3;
            margin-bottom: 7px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }

        /* Signatures & Legitimacy Wrap */
        .auth-wrap {
            display: grid;
            grid-template-columns: 1.15fr 0.9fr 1.15fr;
            gap: 6px;
            align-items: center;
            margin-top: 4px;
            padding-top: 5px;
            border-top: 1.5px dashed #cbd5e1;
            font-family: 'Times New Roman', Times, serif;
        }
        .auth-box {
            text-align: center;
            font-size: 8pt;
            line-height: 1.25;
            position: relative;
        }
        .auth-role {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
            color: #0f172a;
        }
        .auth-space {
            height: 38px;
        }
        .auth-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 8.5pt;
            color: #0f172a;
        }
        .auth-nip {
            font-size: 7.5pt;
            color: #475569;
        }

        /* QR Center Box */
        .qr-center-box {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .qr-img {
            width: 54px;
            height: 54px;
            border: 1px solid #cbd5e1;
            padding: 2px;
            border-radius: 6px;
            background: #ffffff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.06);
        }
        .qr-tag {
            font-size: 5.8pt;
            font-weight: 700;
            color: #1e3a8a;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .qr-serial {
            font-size: 5.2pt;
            color: #64748b;
            font-family: monospace;
        }

        /* Stempel Cap Digital */
        .stempel-badge {
            position: absolute;
            right: 15px;
            bottom: 4px;
            width: 74px;
            height: 74px;
            border: 2px dashed #0284c7;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #0284c7;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            text-align: center;
            transform: rotate(-12deg);
            background: rgba(240, 253, 244, 0.75);
            pointer-events: none;
            z-index: 2;
        }
        .stempel-top { font-size: 5.2pt; font-weight: 900; letter-spacing: 0.4px; }
        .stempel-mid { font-size: 5.8pt; font-weight: 900; color: #1e3a8a; margin: 1px 0; }
        .stempel-bot { font-size: 5.2pt; font-weight: 900; letter-spacing: 0.3px; }

        @media print {
            body { background: #ffffff !important; }
            .no-print-bar { display: none !important; }
            .page {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                min-height: auto !important;
            }
        }
    </style>
</head>
<body>

    <!-- Bilah Atas / Action Bar (Hanya tampil di layar) -->
    <div class="no-print-bar">
        <div class="bar-title">
            <span class="bar-badge">BUKTI FISIK PKKM 2026</span>
            <span>Display Visi, Misi & Sasaran Mutu Madrasah (Indikator 1.2.1 & 2.1.2)</span>
        </div>
        <div class="bar-actions">
            <a href="<?php echo $backUrl; ?>" class="bar-btn bar-btn-secondary">
                ← <?php echo $backText; ?>
            </a>
            <a href="/admin/pkkm/master-berkas" class="bar-btn bar-btn-secondary" title="Buka Daftar Berkas Master PKKM">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                Daftar Berkas
            </a>
            <button type="button" onclick="openKoleksiBuktiModal()" class="bar-btn bar-btn-secondary" title="Daftar Tautan Semua Bukti Fisik PKKM">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                Link Bukti Kita
            </button>
            <button onclick="window.print()" class="bar-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                Cetak / Simpan PDF (A4)
            </button>
        </div>
    </div>

    <!-- Lembar Halaman A4 -->
    <div class="page">
        <div class="outer-frame">
            <div class="inner-frame">

                <!-- KOP RESMI MADRASAH -->
                <div class="kop-surat-preview">
                    <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Logo Madrasah">
                    <div class="kop-text-preview">
                        <h1><?php echo htmlspecialchars($nama_yayasan); ?></h1>
                        <h2><?php echo htmlspecialchars($sekolah_nama); ?></h2>
                        <p>
                            <strong>NSM:</strong> <?php echo htmlspecialchars($inst['nsm'] ?? '111218040083'); ?> &nbsp;|&nbsp; 
                            <strong>NPSN:</strong> <?php echo htmlspecialchars($inst['npsn'] ?? '60705445'); ?> &nbsp;|&nbsp; 
                            <strong>AKREDITASI:</strong> B (BAN-S/M)
                        </p>
                        <p><?php echo htmlspecialchars($alamatLengkap); ?></p>
                        <p>Website: <?php echo htmlspecialchars($webUrl); ?> &nbsp;|&nbsp; Email: <?php echo htmlspecialchars($emailAddr); ?></p>
                    </div>
                </div>

                <!-- HEADER DOKUMEN / PLANG PENETAPAN -->
                <div class="doc-header-banner">
                    <div class="sk-badge">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 12 2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
                        Surat Keputusan Kepala Madrasah Nomor: B-012/MI.083/PP.00.1/07/2026
                    </div>
                    <h3 class="main-display-title">VISI, MISI, TUJUAN & SASARAN MUTU MADRASAH</h3>
                    <div class="main-display-sub">TAHUN PELAJARAN 2026/2027 • TAHUN PENILAIAN PKKM 2026</div>
                </div>

                <!-- VISI MADRASAH (EXECUTIVE 3D ACRYLIC PLAQUE) -->
                <div class="visi-plaque">
                    <div class="visi-top-tag">
                        ★ &nbsp; VISI UTAMA MADRASAH &nbsp; ★
                    </div>
                    <div class="visi-statement">
                        "Terwujudnya Generasi yang Berakhlakul Karimah, Cerdas, Terampil, dan Unggul dalam Prestasi Keagamaan."
                    </div>
                    <div class="visi-chips-row">
                        <span class="visi-chip">1. Berakhlakul Karimah (Adab & Karakter)</span>
                        <span class="visi-chip">2. Cerdas (Literasi & Numerasi Unggul)</span>
                        <span class="visi-chip">3. Terampil (Kecakapan Hidup & TIK)</span>
                        <span class="visi-chip">4. Unggul Prestasi (Tahfidz & Sains)</span>
                    </div>
                </div>

                <!-- MISI STRATEGIS MADRASAH (5 BUTIR PRIORITAS) -->
                <div class="section-panel">
                    <div class="panel-header-row">
                        <div class="panel-title-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Misi Strategis Madrasah
                        </div>
                        <div class="panel-badge-pill">Arah Kebijakan KMA 450 & Kurikulum Madrasah</div>
                    </div>

                    <div class="misi-list-wrap">
                        <?php foreach ($misiList as $m): ?>
                            <div class="misi-item-card">
                                <div class="misi-num-badge"><?php echo $m['no']; ?></div>
                                <div class="misi-content">
                                    <div class="misi-item-title"><?php echo $m['title']; ?></div>
                                    <div class="misi-item-desc"><?php echo $m['desc']; ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 4 SASARAN MUTU LULUSAN (OUTPUT TARGET PESERTA DIDIK) -->
                <div class="section-panel" style="padding-bottom: 5px;">
                    <div class="panel-header-row">
                        <div class="panel-title-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                            Sasaran Mutu Kelulusan & Target Keunggulan Siswa
                        </div>
                        <div class="panel-badge-pill">Standar Kompetensi Lulusan (SKL)</div>
                    </div>

                    <div class="mutu-grid">
                        <?php foreach ($sasaranMutu as $s): ?>
                            <div class="mutu-card" style="background: <?php echo $s['bg']; ?>; border-color: <?php echo $s['border']; ?>;">
                                <div class="mutu-tag" style="color: <?php echo $s['color']; ?>;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    <?php echo $s['pilar']; ?>
                                </div>
                                <div class="mutu-desc"><?php echo $s['sub']; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- NOTA KONSENSUS & SOSIALISASI DISPLAY -->
                <div class="meta-notice-box">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                    <div>
                        <strong>Keterbukaan & Konsensus Kelembagaan:</strong> Visi, Misi, dan Sasaran Mutu ini telah disosialisasikan, dipajang pada plang display dinding serambi lobi madrasah, dan disepakati bersama dalam Rapat Pleno Dewan Pendidik bersama Komite Madrasah pada 11 Juli 2026 sebagai arah RKM, RKTM, dan Dokumen Kurikulum (KOM) Tahun Pelajaran 2026/2027.
                    </div>
                </div>

                <!-- PENGESAHAN, LEGALITAS & TANDA TANGAN -->
                <div class="auth-wrap">
                    <div class="auth-box">
                        <div>Menyetujui & Mengetahui,</div>
                        <div class="auth-role">Ketua Komite Madrasah,</div>
                        <div class="auth-space"></div>
                        <div class="auth-name"><?php echo htmlspecialchars($komite_nama); ?></div>
                        <div class="auth-nip">Perwakilan Wali Murid & Masyarakat</div>
                    </div>

                    <div class="qr-center-box">
                        <img src="<?php echo $qrCodeSrc; ?>" class="qr-img" alt="QR Verifikasi PKKM">
                        <div class="qr-tag">Validasi Dokumen PKKM</div>
                        <div class="qr-serial">REF: SK-VISI-2026-MFHDA</div>
                    </div>

                    <div class="auth-box">
                        <div>Ditetapkan di: <?php echo htmlspecialchars($kecamatan); ?></div>
                        <div>Pada tanggal: <?php echo $tgl_indo; ?></div>
                        <div class="auth-role">Kepala Madrasah,</div>
                        <div class="auth-space"></div>
                        <div class="auth-name"><?php echo htmlspecialchars($kepala_nama); ?></div>
                        <div class="auth-nip">NIP. <?php echo htmlspecialchars($kepala_nip); ?></div>

                        <!-- STEMPEL RESMI DIGITAL -->
                        <div class="stempel-badge">
                            <span class="stempel-top">MIS MIFTAHUL HUDA</span>
                            <span class="stempel-mid">★ KEMENAG ★</span>
                            <span class="stempel-bot">LEMBAGA VALID</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php include (file_exists(__DIR__ . '/_pkkm_koleksi_modal.php') ? __DIR__ . '/_pkkm_koleksi_modal.php' : __DIR__ . '/pkkm/_pkkm_koleksi_modal.php'); ?>
</body>
</html>
