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

$kepala_nama = $kamad['nama'] ?? 'SAMSUL ARIFIN, M.Pd';
$kepala_nip = $kamad['nip'] ?? '-';

$bulanIndo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$tgl_indo = date('j') . ' ' . $bulanIndo[(int)date('n')] . ' ' . date('Y');

$isPengawas = isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 5;
$backUrl = $isPengawas ? '/pengawas/pkkm?tab=penilaian' : '/admin/pkkm/instrumen';
$backText = $isPengawas ? 'Kembali ke Penilaian' : 'Kembali ke Instrumen';

// Data 6 Kuadran Dokumentasi Lingkungan Fisik Real Madrasah
$kuadranLingkungan = [
    [
        'no' => 1,
        'zona' => 'Pagar Permanen & Gerbang Pengaman Utama',
        'icon' => '🛡️',
        'color' => '#0284c7',
        'bg' => '#ecfdf5',
        'border' => '#86efac',
        'foto' => '/public/uploads/website/hero1_1779991701.jpeg',
        'spesifikasi' => 'Pagar keliling permanen mengitari seluruh area madrasah. Gerbang besi dengan pos piket kontrol ketat guna menjamin keselamatan peserta didik.',
        'status' => 'Sangat Aman & Terkendali',
        'skor' => 'Kondisi 100% Baik'
    ],
    [
        'no' => 2,
        'zona' => 'Halaman Upacara, Olahraga & Taman Rindang',
        'icon' => '🌳',
        'color' => '#16a34a',
        'bg' => '#f0fdf4',
        'border' => '#bbf7d0',
        'foto' => '/public/uploads/website/hero2_1779991701.jpeg',
        'spesifikasi' => 'Halaman paving block bebas genangan air, dilengkapi deretan pohon peneduh rindang dan tanaman hijau penyejuk iklim mikro pembelajaran.',
        'status' => 'Asri, Bersih & Teduh',
        'skor' => 'Bebas Genangan Air'
    ],
    [
        'no' => 3,
        'zona' => 'Ruang Belajar Ramah Anak & Ventilasi Silang',
        'icon' => '🏫',
        'color' => '#2563eb',
        'bg' => '#eff6ff',
        'border' => '#bfdbfe',
        'foto' => '/public/uploads/website/hero3_1779991701.jpeg',
        'spesifikasi' => 'Rasio bukaan ventilasi dan jendela >20% luas lantai (pencahayaan alami maksimal), pojok baca terawat, meja-kursi ergonomis dan tertata rapi.',
        'status' => 'Kondusif & Bebas Polusi',
        'skor' => 'Ventilasi Silang Alami'
    ],
    [
        'no' => 4,
        'zona' => 'Fasilitas Sanitasi & Wastafel Air Mengalir Rombel',
        'icon' => '🚰',
        'color' => '#0284c7',
        'bg' => '#f0f9ff',
        'border' => '#bae6fd',
        'foto' => null,
        'spesifikasi' => 'Tersedia wastafel cuci tangan air bersih mengalir di depan tiap rombel (Kelas 1–6) dilengkapi sabun cair antiseptik dan saluran limbah tertutup (SPAL).',
        'status' => 'Higienis & Berfungsi Penuh',
        'skor' => '6 Titik Rombel Aktif'
    ],
    [
        'no' => 5,
        'zona' => 'Mushola Madrasah & Area Wudhu Terpisah',
        'icon' => '🕌',
        'color' => '#7c3aed',
        'bg' => '#f5f3ff',
        'border' => '#ddd6fe',
        'foto' => null,
        'spesifikasi' => 'Sarana ibadah sholat dhuha & dzuhur berjamaah pembiasaan karakter. Tempat wudhu bersih terpisah putra-putri, karpet suci dan sirkulasi sejuk.',
        'status' => 'Bersih, Suci & Nyaman',
        'skor' => 'Wudhu Putra-Putri Pisah'
    ],
    [
        'no' => 6,
        'zona' => 'Pengelolaan Sampah Terpilah & Drainase Tertutup',
        'icon' => '♻️',
        'color' => '#d97706',
        'bg' => '#fffbeb',
        'border' => '#fde68a',
        'foto' => null,
        'spesifikasi' => 'Tempat sampah terpilah (Organik & Anorganik) di tiap selasar kelas. Saluran drainase air lancar bebas jentik nyamuk dan program rutin Jumat Bersih.',
        'status' => 'Bebas Bau & Terpilah',
        'skor' => 'Bebas Jentik & Sampah'
    ]
];

// Parameter Evaluasi Madrasah Sehat Kemenag/Kemenkes
$checklistSanitasi = [
    ['param' => 'Ketersediaan air bersih mengalir & sarana cuci tangan sabun (CTPS)', 'standar' => 'Air mengalir di tiap kelas', 'capaian' => '100% Terpenuhi (6 Rombel)', 'nilai' => 'Sangat Baik (4)'],
    ['param' => 'Ventilasi udara silang & pencahayaan alami ruang kelas', 'standar' => 'Bukaan > 20% luas lantai', 'capaian' => 'Sirkulasi Alami Optimal', 'nilai' => 'Sangat Baik (4)'],
    ['param' => 'Sistem drainase madrasah & pemilahan tempat sampah', 'standar' => 'Saluran tertutup & terpilah', 'capaian' => 'Lancar & Bebas Jentik', 'nilai' => 'Sangat Baik (4)'],
    ['param' => 'Keamanan lingkungan belajar dari ancaman luar & kecelakaan fisik', 'standar' => 'Pagar keliling & gerbang piket', 'capaian' => 'Zero Accident / Aman', 'nilai' => 'Sangat Baik (4)'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Fisik PKKM - Portofolio Lingkungan Sehat, Asri & Ramah Anak - <?php echo htmlspecialchars($sekolah_nama); ?></title>
    <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($brand['favicon']); ?>">
    <link rel="stylesheet" href="/public/assets/css/plus-jakarta-sans.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 12mm 8mm 12mm;
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            background: #e2e8f0;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
            font-size: 8.8pt;
            line-height: 1.35;
            color: #0f172a;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Bilah Aksi Atas (Hanya Tampil di Layar) */
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
        .bar-btn:hover { background: #f0fdf4; transform: translateY(-1px); }
        .bar-btn-secondary {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .bar-btn-secondary:hover { background: rgba(255, 255, 255, 0.25); color: #ffffff; }

        /* Lembar Halaman A4 Pas 1 Lembar */
        .page {
            width: 210mm;
            min-height: 297mm;
            max-height: 297mm;
            padding: 8mm 12mm 6mm 12mm;
            margin: 12px auto;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            box-sizing: border-box;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Kop Surat Resmi Sesuai Standar SIAKAD */
        .kop-surat-preview {
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 2.5px solid #000;
            padding-bottom: 5px;
            margin-bottom: 5px;
            position: relative;
        }
        .kop-surat-preview img {
            width: 62px;
            height: 62px;
            object-fit: contain;
            position: absolute;
            left: 2px;
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

        /* Header Judul Bukti Fisik */
        .doc-header-wrap {
            text-align: center;
            margin-bottom: 5px;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .doc-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #f0fdf4;
            color: #1e40af;
            border: 1px solid #86efac;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 6.8pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .doc-title {
            font-size: 10.8pt;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.4px;
            margin: 0;
            line-height: 1.2;
        }
        .doc-subtitle {
            font-size: 7.2pt;
            color: #475569;
            font-weight: 600;
            margin-top: 1px;
        }

        /* 4 KPI Metric Stat Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
            margin-bottom: 6px;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .stat-card {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 5px 8px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .stat-icon {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .stat-content {
            flex: 1;
            min-width: 0;
        }
        .stat-val {
            font-size: 10.5pt;
            font-weight: 800;
            line-height: 1.1;
            color: #0f172a;
        }
        .stat-lbl {
            font-size: 6.4pt;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Grid 6 Kuadran Visual Lingkungan */
        .kuadran-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
            margin-bottom: 6px;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .kuadran-card {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 5px 7px;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }
        .kuadran-top {
            display: flex;
            align-items: flex-start;
            gap: 6px;
            margin-bottom: 3px;
        }
        .kuadran-icon-wrap {
            width: 26px;
            height: 26px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            flex-shrink: 0;
        }
        .kuadran-title {
            font-size: 7.2pt;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            margin: 0;
        }
        .kuadran-desc {
            font-size: 6.3pt;
            color: #475569;
            line-height: 1.3;
            margin: 2px 0 4px 0;
        }
        .kuadran-badges {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px dashed #e2e8f0;
            padding-top: 3px;
            margin-top: auto;
        }
        .badge-status {
            font-size: 5.8pt;
            font-weight: 800;
            padding: 1px 5px;
            border-radius: 4px;
        }
        .badge-metric {
            font-size: 5.8pt;
            font-weight: 700;
            color: #64748b;
        }

        /* Tabel Checklist Parameter Sanitasi */
        .param-table-wrap {
            margin-bottom: 6px;
        }
        .param-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7pt;
            line-height: 1.25;
            font-family: 'Times New Roman', Times, serif;
        }
        .param-table th, .param-table td {
            border: 1px solid #cbd5e1;
            padding: 2.5px 5px;
            vertical-align: middle;
        }
        .param-table th {
            background: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-align: center;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            font-size: 6.6pt;
            text-transform: uppercase;
        }
        .param-table tr:nth-child(even) {
            background: #fafaf9;
        }

        /* Hasil Verifikasi Asesor Box */
        .eval-box {
            border: 1px dashed #0284c7;
            border-radius: 5px;
            padding: 4px 8px;
            background: #f0fdf4;
            font-size: 6.8pt;
            color: #0f172a;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }

        /* Blok Tanda Tangan & Legitimasi Dokumen */
        .auth-grid {
            display: grid;
            grid-template-columns: 1fr 90px 1.15fr;
            gap: 8px;
            align-items: center;
            border-top: 1.5px solid #0f172a;
            padding-top: 4px;
            font-family: 'Times New Roman', Times, serif;
        }
        .auth-col {
            text-align: center;
        }
        .auth-role {
            font-size: 7.8pt;
            font-weight: bold;
            margin-bottom: 1px;
            color: #0f172a;
        }
        .auth-sub {
            font-size: 6.8pt;
            color: #334155;
            margin-bottom: 28px;
        }
        .auth-name {
            font-size: 8.5pt;
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
            margin-bottom: 1px;
        }
        .auth-nip {
            font-size: 7pt;
            color: #475569;
        }

        /* QR Center Box */
        .qr-box {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .qr-img {
            width: 48px;
            height: 48px;
            border: 1px solid #cbd5e1;
            padding: 2px;
            border-radius: 5px;
            background: #ffffff;
        }
        .qr-label {
            font-size: 5.5pt;
            font-weight: 800;
            color: #1e3a8a;
            margin-top: 2px;
            letter-spacing: 0.3px;
        }

        /* Stempel Cap Digital */
        .stempel-badge {
            display: inline-block;
            border: 1.5px solid #0284c7;
            border-radius: 4px;
            padding: 1px 6px;
            color: #0284c7;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            font-size: 5.5pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-top: 1px;
            background: rgba(240, 253, 244, 0.7);
        }

        /* Footer Halaman */
        .footer-note {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 6.5pt;
            color: #64748b;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            border-top: 1px dashed #cbd5e1;
            padding-top: 3px;
            margin-top: 2px;
        }

        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }
            .no-print-bar {
                display: none !important;
            }
            .page {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 6mm 10mm 4mm 10mm !important;
                width: 100% !important;
                min-height: auto !important;
                max-height: none !important;
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
            }
            .stat-card, .kuadran-card, .eval-box {
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- BILAH AKSI ATAS (TAMPIL DI LAYAR) -->
    <div class="no-print-bar">
        <div class="bar-title">
            <span class="bar-badge">BUKTI FISIK PKKM 2026</span>
            <span>Portofolio Lingkungan Sehat, Asri & Ramah Anak (Indikator 2.3.2 & 2.5.1)</span>
        </div>
        <div class="bar-actions">
            <a href="<?php echo $backUrl; ?>" class="bar-btn bar-btn-secondary">
                ← <?php echo $backText; ?>
            </a>
            <a href="/admin/pkkm/master-berkas" class="bar-btn bar-btn-secondary" title="Buka Daftar Berkas Master PKKM">
                📁 Daftar Berkas
            </a>
            <button type="button" onclick="openKoleksiBuktiModal()" class="bar-btn bar-btn-secondary" title="Daftar Tautan Semua Bukti Fisik PKKM">
                📋 Link Bukti Kita
            </button>
            <button type="button" onclick="window.print()" class="bar-btn" title="Cetak atau simpan sebagai dokumen PDF 1 lembar A4">
                🖨️ Cetak / Simpan PDF (1 Halaman)
            </button>
        </div>
    </div>

    <!-- LEMBAR HALAMAN A4 BUKTI FISIK 1 LEMBAR PAS -->
    <div class="page">
        <div>
            <!-- KOP RESMI MADRASAH SESUAI SIAKAD -->
            <div class="kop-surat-preview">
                <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Logo Madrasah">
                <div class="kop-text-preview">
                    <h1><?php echo htmlspecialchars($nama_yayasan); ?></h1>
                    <h2><?php echo htmlspecialchars($sekolah_nama); ?></h2>
                    <p>
                        <strong>NSM:</strong> <?php echo htmlspecialchars($inst['nsm'] ?? '111218040083'); ?> &nbsp;|&nbsp; 
                        <strong>NPSN:</strong> <?php echo htmlspecialchars($inst['npsn'] ?? '60705445'); ?> &nbsp;|&nbsp; 
                        <strong>STATUS:</strong> TERAKREDITASI B (BAN-S/M)
                    </p>
                    <p><?php echo htmlspecialchars($alamatLengkap); ?></p>
                </div>
            </div>

            <!-- HEADER JUDUL DOKUMEN -->
            <div class="doc-header-wrap">
                <div class="doc-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    STANDAR SARANA PRASARANA & MANAJEMEN LINGKUNGAN HIDUP • INDIKATOR 2.3.2 & 2.5.1
                </div>
                <h3 class="doc-title">PORTOFOLIO DOKUMENTASI REAL LINGKUNGAN MADRASAH ASRI, BERSIH, RINDANG & AMAN</h3>
                <div class="doc-subtitle">Laporan Faktual Sarpras, Sanitasi Air Mengalir, Ventilasi Sehat & Lingkungan Belajar Ramah Anak TP 2026/2027</div>
            </div>

            <!-- 4 KPI STATS CARD -->
            <div class="stats-grid">
                <div class="stat-card" style="border-left: 3.5px solid #0284c7; background: #ecfdf5;">
                    <div class="stat-icon" style="background:#d1fae5; color:#1e40af;">🌿</div>
                    <div class="stat-content">
                        <div class="stat-val">PREDIKAT A</div>
                        <div class="stat-lbl">Sanitasi Madrasah Sehat</div>
                    </div>
                </div>
                <div class="stat-card" style="border-left: 3.5px solid #0284c7; background: #f0f9ff;">
                    <div class="stat-icon" style="background:#e0f2fe; color:#0369a1;">💧</div>
                    <div class="stat-content">
                        <div class="stat-val">100% AKTIF</div>
                        <div class="stat-lbl">Air Bersih & Wastafel Rombel</div>
                    </div>
                </div>
                <div class="stat-card" style="border-left: 3.5px solid #16a34a; background: #f0fdf4;">
                    <div class="stat-icon" style="background:#dcfce7; color:#15803d;">🛡️</div>
                    <div class="stat-content">
                        <div class="stat-val">ZONA AMAN</div>
                        <div class="stat-lbl">Zero Accident & Pagar Keliling</div>
                    </div>
                </div>
                <div class="stat-card" style="border-left: 3.5px solid #2563eb; background: #eff6ff;">
                    <div class="stat-icon" style="background:#dbeafe; color:#1d4ed8;">🌬️</div>
                    <div class="stat-content">
                        <div class="stat-val">> 20% LUAS</div>
                        <div class="stat-lbl">Ventilasi Silang Alami Kelas</div>
                    </div>
                </div>
            </div>

            <!-- GALERI 6 KUADRAN ZONA LINGKUNGAN MADRASAH -->
            <div class="kuadran-grid">
                <?php foreach ($kuadranLingkungan as $k): ?>
                <div class="kuadran-card" style="border-top: 3px solid <?php echo $k['color']; ?>;">
                    <div>
                        <div class="kuadran-top">
                            <div class="kuadran-icon-wrap" style="background: <?php echo $k['bg']; ?>; color: <?php echo $k['color']; ?>; border: 1px solid <?php echo $k['border']; ?>;">
                                <?php echo $k['icon']; ?>
                            </div>
                            <div style="flex:1;">
                                <h4 class="kuadran-title"><?php echo $k['no'] . '. ' . $k['zona']; ?></h4>
                            </div>
                        </div>
                        <p class="kuadran-desc"><?php echo $k['spesifikasi']; ?></p>
                    </div>
                    <div class="kuadran-badges">
                        <span class="badge-status" style="background: <?php echo $k['bg']; ?>; color: <?php echo $k['color']; ?>; border: 1px solid <?php echo $k['border']; ?>;">
                            ✓ <?php echo $k['status']; ?>
                        </span>
                        <span class="badge-metric"><?php echo $k['skor']; ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- TABEL CHECKLIST PARAMETER LINGKUNGAN MADRASAH SEHAT -->
            <div class="param-table-wrap">
                <table class="param-table">
                    <thead>
                        <tr>
                            <th style="width: 4%;">No</th>
                            <th style="width: 44%;">Parameter Asesmen Sanitasi & Keamanan Lingkungan</th>
                            <th style="width: 24%;">Standar Kemenag / Kemenkes</th>
                            <th style="width: 16%;">Hasil Faktual Lapangan</th>
                            <th style="width: 12%;">Skor Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($checklistSanitasi as $idx => $cs): ?>
                        <tr>
                            <td style="text-align: center; font-weight: bold;"><?php echo $idx + 1; ?></td>
                            <td><strong><?php echo $cs['param']; ?></strong></td>
                            <td style="text-align: center;"><?php echo $cs['standar']; ?></td>
                            <td style="text-align: center; color: #1e3a8a; font-weight: bold;"><?php echo $cs['capaian']; ?></td>
                            <td style="text-align: center; font-weight: bold; color: #1e40af;"><?php echo $cs['nilai']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- HASIL VERIFIKASI ASESOR -->
            <div class="eval-box">
                <div>
                    <strong>HASIL OBSERVASI & TELAAH LAPANGAN PENILAI:</strong>
                    <span>Madrasah terbukti nyata menciptakan suasana belajar yang asri, bersih, rindang, aman, bebas polusi dan menyenangkan bagi seluruh peserta didik. Sarana sanitasi air mengalir dan pengelolaan limbah terpelihara sangat baik.</span>
                </div>
                <div style="font-weight: 800; font-size: 7pt; color: #1e40af; white-space: nowrap; margin-left: 10px;">
                    NILAI INDIKATOR: SANGAT BAIK (4)
                </div>
            </div>
        </div>

        <!-- BLOK PENGESAHAN & TANDA TANGAN RESMI -->
        <div>
            <div class="auth-grid">
                <!-- KIRI: KOORDINATOR SARPRAS & LINGKUNGAN -->
                <div class="auth-col">
                    <div class="auth-role">Mengetahui,</div>
                    <div class="auth-sub">Koordinator Sarpras & Lingkungan Hidup,</div>
                    <div class="auth-name">M. FAHRURROZI, S.Pd</div>
                    <div class="auth-nip">Pengelola Sarana Prasarana</div>
                </div>

                <!-- TENGAH: QR CODE DIGITAL -->
                <div class="qr-box">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=https://masmifhda.sch.id/admin/pkkm/cetak-bukti/lingkungan-sehat" alt="QR Validasi" class="qr-img">
                    <div class="qr-label">VERIFIKASI DIGITAL</div>
                    <div class="stempel-badge">TERVALIDASI ✓</div>
                </div>

                <!-- KANAN: KEPALA MADRASAH -->
                <div class="auth-col">
                    <div class="auth-role"><?php echo htmlspecialchars($kecamatan); ?>, <?php echo $tgl_indo; ?></div>
                    <div class="auth-sub">Kepala Madrasah,</div>
                    <div class="auth-name"><?php echo htmlspecialchars($kepala_nama); ?></div>
                    <div class="auth-nip">NIP. <?php echo htmlspecialchars($kepala_nip); ?></div>
                </div>
            </div>

            <!-- FOOTER NOTE -->
            <div class="footer-note">
                <span>Dokumen Bukti Fisik PKKM &bull; Standar Sarana Prasarana & Budaya Lingkungan Sehat &bull; MAS Miftahul Huda Mathla'ul Anwar</span>
                <span style="font-weight:700; color:#0284c7;">https://masmifhda.sch.id</span>
            </div>
        </div>
    </div>

    <?php include (file_exists(__DIR__ . '/_pkkm_koleksi_modal.php') ? __DIR__ . '/_pkkm_koleksi_modal.php' : __DIR__ . '/pkkm/_pkkm_koleksi_modal.php'); ?>
</body>
</html>
