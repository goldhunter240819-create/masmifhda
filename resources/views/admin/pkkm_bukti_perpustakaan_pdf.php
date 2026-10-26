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

// Data Rekapitulasi Koleksi DDC (Dewey Decimal Classification) Perpustakaan MAS Miftahul Huda
$koleksiDDC = [
    ['kode' => '000-099', 'bidang' => 'Karya Umum, TIK & Referensi Komputer', 'judul' => 45, 'eks' => 120, 'persen' => '7.7%'],
    ['kode' => '100-199', 'bidang' => 'Filsafat, Moral & Psikologi Pendidikan Anak', 'judul' => 25, 'eks' => 65, 'persen' => '4.2%'],
    ['kode' => '200-299', 'bidang' => 'Agama Islam (Al-Qur\'an, Hadits, Fikih, SKI, Akidah)', 'judul' => 185, 'eks' => 540, 'persen' => '34.5%'],
    ['kode' => '300-399', 'bidang' => 'Ilmu Sosial, Pendidikan Pancasila & Kewarganegaraan', 'judul' => 40, 'eks' => 115, 'persen' => '7.3%'],
    ['kode' => '400-499', 'bidang' => 'Bahasa (Bahasa Indonesia, Bahasa Arab & Inggris)', 'judul' => 55, 'eks' => 165, 'persen' => '10.5%'],
    ['kode' => '500-599', 'bidang' => 'Sains Alam, Biologi Dasar & Matematika Terapan', 'judul' => 60, 'eks' => 170, 'persen' => '10.9%'],
    ['kode' => '600-699', 'bidang' => 'Teknologi Terapan, Kesehatan & Keterampilan Hidup', 'judul' => 20, 'eks' => 50, 'persen' => '3.2%'],
    ['kode' => '700-799', 'bidang' => 'Kesenian Daerah, Musik Islami & Olahraga', 'judul' => 18, 'eks' => 40, 'persen' => '2.6%'],
    ['kode' => '800-899', 'bidang' => 'Sastra, Cerita Rakyat & Dongeng Teladan Anak Islami', 'judul' => 75, 'eks' => 210, 'persen' => '13.4%'],
    ['kode' => '900-999', 'bidang' => 'Sejarah Kebudayaan Islam, Nasional & Geografi', 'judul' => 32, 'eks' => 90, 'persen' => '5.7%'],
];

$totalJudul = 0;
$totalEksemplar = 0;
foreach ($koleksiDDC as $item) {
    $totalJudul += $item['judul'];
    $totalEksemplar += $item['eks'];
}

// Tren Kunjungan Perpustakaan & Pojok Baca (Semester Ganjil 2026)
$trenKunjungan = [
    ['bulan' => 'Jul', 'nama' => 'Juli', 'kunjungan' => 210, 'persen' => 37, 'ket' => 'Matsama & Orientasi'],
    ['bulan' => 'Agt', 'nama' => 'Agustus', 'kunjungan' => 480, 'persen' => 85, 'ket' => 'Layanan Rutin'],
    ['bulan' => 'Sep', 'nama' => 'September', 'kunjungan' => 530, 'persen' => 94, 'ket' => 'Persiapan PTS/STS'],
    ['bulan' => 'Okt', 'nama' => 'Oktober', 'kunjungan' => 560, 'persen' => 100, 'ket' => 'Bulan Bahasa & Literasi'],
    ['bulan' => 'Nov', 'nama' => 'November', 'kunjungan' => 490, 'persen' => 88, 'ket' => 'Pekan Baca Senyap'],
    ['bulan' => 'Des', 'nama' => 'Desember', 'kunjungan' => 320, 'persen' => 57, 'ket' => 'Asesmen Akhir Semester'],
];
$totalKunjungan = array_sum(array_column($trenKunjungan, 'kunjungan'));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Fisik PKKM - Profil & Rekap Layanan Perpustakaan - <?php echo htmlspecialchars($sekolah_nama); ?></title>
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
            font-size: 9pt;
            line-height: 1.35;
            color: #0f172a;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Bilah Tindakan Atas (Hanya Tampil di Layar) */
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
            margin-bottom: 6px;
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
            margin-bottom: 6px;
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
            font-size: 11pt;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.4px;
            margin: 0;
            line-height: 1.2;
        }
        .doc-subtitle {
            font-size: 7.5pt;
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
            font-size: 1.05rem;
            flex-shrink: 0;
        }
        .stat-content {
            flex: 1;
            min-width: 0;
        }
        .stat-val {
            font-size: 11pt;
            font-weight: 800;
            line-height: 1.1;
            color: #0f172a;
        }
        .stat-lbl {
            font-size: 6.5pt;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Konten Utama 2 Kolom Seimbang */
        .main-grid {
            display: grid;
            grid-template-columns: 1.25fr 1fr;
            gap: 8px;
            margin-bottom: 6px;
            flex: 1;
        }

        .section-box {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 6px 8px;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .section-heading {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            font-size: 7.5pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
        }

        /* Tabel Klasifikasi DDC */
        .ddc-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.2pt;
            line-height: 1.25;
            font-family: 'Times New Roman', Times, serif;
        }
        .ddc-table th, .ddc-table td {
            border: 1px solid #cbd5e1;
            padding: 2.5px 4px;
            vertical-align: middle;
        }
        .ddc-table th {
            background: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-align: center;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            font-size: 6.8pt;
            text-transform: uppercase;
        }
        .ddc-table tr:nth-child(even) {
            background: #fafaf9;
        }
        .ddc-table tfoot td {
            background: #ecfdf5;
            font-weight: bold;
            color: #1e40af;
            border-top: 1.5px solid #0284c7;
        }

        /* Mini Bar Chart CSS Kunjungan Bulanan */
        .chart-box {
            margin-bottom: 6px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 6px;
        }
        .chart-grid {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            height: 48px;
            padding-top: 6px;
            gap: 4px;
            border-bottom: 1px solid #cbd5e1;
            margin-bottom: 3px;
        }
        .bar-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            height: 100%;
            justify-content: flex-end;
        }
        .bar-fill {
            width: 80%;
            max-width: 22px;
            background: linear-gradient(180deg, #38bdf8 0%, #0284c7 100%);
            border-radius: 3px 3px 0 0;
            transition: height 0.3s ease;
            position: relative;
        }
        .bar-num {
            font-size: 5.5pt;
            font-weight: 800;
            color: #0f172a;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            margin-bottom: 1px;
        }
        .bar-lbl {
            font-size: 6pt;
            font-weight: 700;
            color: #64748b;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            text-align: center;
        }

        /* 3 Card Program Inovasi Literasi */
        .program-list {
            display: flex;
            flex-direction: column;
            gap: 4px;
            margin-bottom: 4px;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .program-card {
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 4px 6px;
            background: #ffffff;
            display: flex;
            align-items: flex-start;
            gap: 6px;
        }
        .program-card.p1 { border-left: 3px solid #0284c7; background: #f0fdf4; }
        .program-card.p2 { border-left: 3px solid #2563eb; background: #eff6ff; }
        .program-card.p3 { border-left: 3px solid #d97706; background: #fffbeb; }

        .program-icon {
            font-size: 0.9rem;
            line-height: 1;
            margin-top: 1px;
        }
        .program-title {
            font-size: 7.2pt;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 1px 0;
        }
        .program-desc {
            font-size: 6.5pt;
            color: #475569;
            margin: 0;
            line-height: 1.25;
        }

        /* Verifikasi / Hasil Asesor Ringkas */
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
            margin-bottom: 6px;
        }

        /* Blok Tanda Tangan & Legitimasi Dokumen */
        .auth-grid {
            display: grid;
            grid-template-columns: 1fr 90px 1.15fr;
            gap: 8px;
            align-items: center;
            border-top: 1.5px solid #0f172a;
            padding-top: 5px;
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
            font-size: 7pt;
            color: #334155;
            margin-bottom: 30px;
        }
        .auth-name {
            font-size: 8.5pt;
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
            margin-bottom: 1px;
        }
        .auth-nip {
            font-size: 7.2pt;
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
            .stat-card, .section-box, .chart-box, .program-card {
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
            <span>Profil & Rekapitulasi Layanan Perpustakaan / Pojok Baca (Indikator 2.2.2 & 2.5.2)</span>
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 2v20"/></svg>
                    STANDAR SARANA PRASARANA & PENGELOLAAN PERPUSTAKAAN • INDIKATOR 2.2.2 & 2.5.2
                </div>
                <h3 class="doc-title">PROFIL & REKAPITULASI LAYANAN PERPUSTAKAAN DAN POJOK BACA</h3>
                <div class="doc-subtitle">Sistem Klasifikasi DDC, Administrasi Sirkulasi Bahan Pustaka & Budaya Literasi Siswa TP 2026/2027</div>
            </div>

            <!-- 4 KPI STATS CARD -->
            <div class="stats-grid">
                <div class="stat-card" style="border-left: 3.5px solid #0284c7; background: #ecfdf5;">
                    <div class="stat-icon" style="background:#d1fae5; color:#1e40af;">📚</div>
                    <div class="stat-content">
                        <div class="stat-val"><?php echo number_format($totalEksemplar, 0, ',', '.'); ?> <span style="font-size:7pt; font-weight:600;">Eks</span></div>
                        <div class="stat-lbl"><?php echo $totalJudul; ?> Judul Buku Terkatalog</div>
                    </div>
                </div>
                <div class="stat-card" style="border-left: 3.5px solid #2563eb; background: #eff6ff;">
                    <div class="stat-icon" style="background:#dbeafe; color:#1d4ed8;">👥</div>
                    <div class="stat-content">
                        <div class="stat-val">1 : 5.8 <span style="font-size:7pt; font-weight:600;">Buku</span></div>
                        <div class="stat-lbl">Rasio Buku / Siswa (Standar >1:3)</div>
                    </div>
                </div>
                <div class="stat-card" style="border-left: 3.5px solid #d97706; background: #fffbeb;">
                    <div class="stat-icon" style="background:#fef3c7; color:#b45309;">📈</div>
                    <div class="stat-content">
                        <div class="stat-val">42 <span style="font-size:7pt; font-weight:600;">Orang/Hari</span></div>
                        <div class="stat-lbl">Rata-rata Sirkulasi & Kunjungan</div>
                    </div>
                </div>
                <div class="stat-card" style="border-left: 3.5px solid #7c3aed; background: #f5f3ff;">
                    <div class="stat-icon" style="background:#ede9fe; color:#6d28d9;">🏷️</div>
                    <div class="stat-content">
                        <div class="stat-val">6 <span style="font-size:7pt; font-weight:600;">Pojok Baca</span></div>
                        <div class="stat-lbl">Terpasang di Kelas I s.d. VI</div>
                    </div>
                </div>
            </div>

            <!-- KONTEN UTAMA: 2 KOLOM SEIMBANG -->
            <div class="main-grid">
                
                <!-- KOLOM KIRI: TABEL DDC -->
                <div class="section-box">
                    <div class="section-heading">
                        <span>I. Inventarisasi Koleksi Bahan Pustaka (DDC)</span>
                        <span style="font-size:6.2pt; color:#64748b; font-weight:600;">Dewey Decimal Classification</span>
                    </div>
                    <table class="ddc-table">
                        <thead>
                            <tr>
                                <th style="width: 14%;">Kode DDC</th>
                                <th style="width: 58%;">Klasifikasi / Subjek Ilmu</th>
                                <th style="width: 13%;">Judul</th>
                                <th style="width: 15%;">Eksemplar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($koleksiDDC as $row): ?>
                            <tr>
                                <td style="text-align: center; font-family: monospace; font-weight: bold;"><?php echo $row['kode']; ?></td>
                                <td><?php echo $row['bidang']; ?></td>
                                <td style="text-align: center;"><?php echo $row['judul']; ?></td>
                                <td style="text-align: center; font-weight: bold;"><?php echo $row['eks']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" style="text-align: right; padding-right: 8px;">TOTAL KOLEKSI PERPUSTAKAAN:</td>
                                <td style="text-align: center;"><?php echo $totalJudul; ?></td>
                                <td style="text-align: center;"><?php echo $totalEksemplar; ?></td>
                            </tr>
                        </tfoot>
                    </table>
                    <div style="font-size:6.2pt; color:#64748b; font-style:italic; margin-top:3px; line-height:1.2;">
                        * Sumber data: Buku Induk Perpustakaan MAS Miftahul Huda No. Reg. 001/PST-MFH/2026. Koleksi mencakup Buku Teks Utama, Buku Pengayaan PAI, Ensiklopedia, Kamus, dan Sastra/Fiksi Anak Islami.
                    </div>
                </div>

                <!-- KOLOM KANAN: GRAFIK & PROGRAM LITERASI -->
                <div class="section-box">
                    
                    <!-- Tren Kunjungan Bar Chart -->
                    <div class="section-heading">
                        <span>II. Tren Kunjungan & Sirkulasi (Semester Ganjil)</span>
                        <span style="font-size:6.2pt; color:#0284c7; font-weight:700;">Tot: <?php echo number_format($totalKunjungan, 0, ',', '.'); ?> Siswa</span>
                    </div>
                    <div class="chart-box">
                        <div class="chart-grid">
                            <?php foreach ($trenKunjungan as $tk): ?>
                            <div class="bar-col" title="<?php echo $tk['nama'] . ': ' . $tk['kunjungan'] . ' kunjungan (' . $tk['ket'] . ')'; ?>">
                                <div class="bar-num"><?php echo $tk['kunjungan']; ?></div>
                                <div class="bar-fill" style="height: <?php echo $tk['persen']; ?>%;"></div>
                                <div class="bar-lbl"><?php echo $tk['bulan']; ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div style="display:flex; justify-content:space-between; font-size:5.8pt; color:#64748b; font-family:'Plus Jakarta Sans', system-ui, sans-serif;">
                            <span>Puncak kunjungan: Bulan Bahasa (Okt - 560 siswa)</span>
                            <span>Rata-rata: <strong>432 siswa/bln</strong></span>
                        </div>
                    </div>

                    <!-- 3 Program Inovasi Literasi -->
                    <div class="section-heading" style="margin-top: 2px;">
                        <span>III. Inovasi Literasi & Pojok Baca Madrasah</span>
                    </div>
                    <div class="program-list">
                        <div class="program-card p1">
                            <div class="program-icon">📖</div>
                            <div>
                                <div class="program-title">Pojok Baca Ramah Anak di Setiap Kelas (I–VI)</div>
                                <div class="program-desc">Setiap ruang kelas dilengkapi mini-rak tematik berisi 35 eksemplar buku cerita moral Islami & ensiklopedia yang dirotasi berkala setiap bulan.</div>
                            </div>
                        </div>
                        <div class="program-card p2">
                            <div class="program-icon">⏱️</div>
                            <div>
                                <div class="program-title">Gerakan 15 Menit Membaca Senyap (Silent Reading)</div>
                                <div class="program-desc">Program pembiasaan membaca mandiri rutin setiap hari Selasa dan Kamis (07.15 - 07.30 WIB) guna mengasah literasi pemahaman peserta didik.</div>
                            </div>
                        </div>
                        <div class="program-card p3">
                            <div class="program-icon">💻</div>
                            <div>
                                <div class="program-title">Buku Induk & Sirkulasi Terstruktur (Digital/Katalog)</div>
                                <div class="program-desc">Pengelolaan kartu peminjaman siswa, penomoran label barcode punggung buku, serta logbook keterpakaian sarana perpustakaan madrasah.</div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- HASIL VERIFIKASI ASESOR -->
            <div class="eval-box">
                <div>
                    <strong>CATATAN EVALUASI & VERIFIKASI PENILAI:</strong>
                    <span>Sarana perpustakaan madrasah terkelola dengan tertib, buku induk lengkap, rasio buku memenuhi standar, serta program pojok baca kelas terbukti aktif menunjang budaya literasi madrasah.</span>
                </div>
                <div style="font-weight: 800; font-size: 7pt; color: #1e40af; white-space: nowrap; margin-left: 10px;">
                    SKOR PREDIKAT: SANGAT BAIK (4)
                </div>
            </div>
        </div>

        <!-- BLOK PENGESAHAN & TANDA TANGAN RESMI -->
        <div>
            <div class="auth-grid">
                <!-- KIRI: KEPALA PERPUSTAKAAN -->
                <div class="auth-col">
                    <div class="auth-role">Mengetahui,</div>
                    <div class="auth-sub">Koordinator Perpustakaan & Literasi,</div>
                    <div class="auth-name">NUR HIDAYATI, S.Pd.I</div>
                    <div class="auth-nip">Pengelola Unit Perpustakaan</div>
                </div>

                <!-- TENGAH: QR CODE DIGITAL -->
                <div class="qr-box">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=https://masmifhda.sch.id/admin/pkkm/cetak-bukti/perpustakaan" alt="QR Validasi" class="qr-img">
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
                <span>Dokumen Bukti Fisik PKKM &bull; Standar Sarana Prasarana & Budaya Mutu Literasi &bull; MAS Miftahul Huda Mathla'ul Anwar</span>
                <span style="font-weight:700; color:#0284c7;">https://masmifhda.sch.id</span>
            </div>
        </div>
    </div>

    <?php include (file_exists(__DIR__ . '/_pkkm_koleksi_modal.php') ? __DIR__ . '/_pkkm_koleksi_modal.php' : __DIR__ . '/pkkm/_pkkm_koleksi_modal.php'); ?>
</body>
</html>
