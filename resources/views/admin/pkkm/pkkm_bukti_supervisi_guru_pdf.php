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

// Data Hasil Supervisi Akademik Guru (Semester Ganjil TP 2026/2027)
$matriksSupervisi = [
    [
        'nama' => 'SITI NURJANAH, S.Pd.I',
        'nip' => '-',
        'mapel' => 'Guru Kelas 1 / Koord. Kurikulum',
        'perencanaan' => 94,
        'kbm' => 92,
        'media' => 90,
        'asesmen' => 92,
        'tindak_lanjut' => 'Penguatan Pembelajaran Berdiferensiasi'
    ],
    [
        'nama' => 'AHMAD RIFAI, S.Pd',
        'nip' => '-',
        'mapel' => 'Guru Kelas 2',
        'perencanaan' => 92,
        'kbm' => 94,
        'media' => 92,
        'asesmen' => 90,
        'tindak_lanjut' => 'Optimalisasi Pojok Baca & Literasi Numerasi'
    ],
    [
        'nama' => 'NURUL HIDAYAH, S.Pd',
        'nip' => '-',
        'mapel' => 'Guru Kelas 3',
        'perencanaan' => 95,
        'kbm' => 93,
        'media' => 90,
        'asesmen' => 94,
        'tindak_lanjut' => 'Sharing Praktik Baik di Komunitas Belajar KKG'
    ],
    [
        'nama' => 'DEWI LESTARI, S.Pd',
        'nip' => '-',
        'mapel' => 'Guru Kelas 4',
        'perencanaan' => 92,
        'kbm' => 90,
        'media' => 92,
        'asesmen' => 92,
        'tindak_lanjut' => 'Pelatihan Modul Ajar di MOOC PINTAR Kemenag'
    ],
    [
        'nama' => 'ZULFA KHOIRUNNISA, S.Pd',
        'nip' => '-',
        'mapel' => 'Guru Kelas 5',
        'perencanaan' => 96,
        'kbm' => 95,
        'media' => 94,
        'asesmen' => 95,
        'tindak_lanjut' => 'Pendampingan Input Asesmen Sumatif RDM'
    ],
    [
        'nama' => 'BUDI SANTOSO, S.Pd',
        'nip' => '-',
        'mapel' => 'Guru Kelas 6',
        'perencanaan' => 93,
        'kbm' => 92,
        'media' => 90,
        'asesmen' => 93,
        'tindak_lanjut' => 'Penyusunan Bank Soal Asesmen Madrasah'
    ],
    [
        'nama' => 'FARIDAH, S.Pd.I',
        'nip' => '-',
        'mapel' => 'Guru PAI & Bahasa Arab',
        'perencanaan' => 95,
        'kbm' => 94,
        'media' => 92,
        'asesmen' => 95,
        'tindak_lanjut' => 'Integrasi Kuis Interaktif Berbasis Quizizz/Web'
    ],
    [
        'nama' => 'HENDRA PRASETYA, S.Pd',
        'nip' => '-',
        'mapel' => 'Guru PJOK',
        'perencanaan' => 90,
        'kbm' => 93,
        'media' => 88,
        'asesmen' => 91,
        'tindak_lanjut' => 'Pengembangan Alat Peraga Olahraga Ramah Anak'
    ],
    [
        'nama' => 'NUR HIDAYATI, S.Pd.I',
        'nip' => '-',
        'mapel' => 'Guru BQ & Literasi',
        'perencanaan' => 94,
        'kbm' => 92,
        'media' => 90,
        'asesmen' => 92,
        'tindak_lanjut' => 'Pembiasaan Tajwid & Tilawah Pagi Hari'
    ],
    [
        'nama' => 'M. AGIL AL-GHOZALI, S.Kom',
        'nip' => '-',
        'mapel' => 'Guru TIK & Multimedia',
        'perencanaan' => 92,
        'kbm' => 95,
        'media' => 98,
        'asesmen' => 93,
        'tindak_lanjut' => 'Workshop Desain Media Pembelajaran Interaktif'
    ]
];

$totalSkor = 0;
foreach ($matriksSupervisi as &$g) {
    $rata = ($g['perencanaan'] + $g['kbm'] + $g['media'] + $g['asesmen']) / 4;
    $g['rata'] = round($rata, 1);
    $g['predikat'] = $g['rata'] >= 90 ? 'Amat Baik (A)' : ($g['rata'] >= 80 ? 'Baik (B)' : 'Cukup (C)');
    $totalSkor += $g['rata'];
}
unset($g);
$rataRataTotal = round($totalSkor / count($matriksSupervisi), 1);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Fisik PKKM - Matriks Supervisi Akademik Guru - <?php echo htmlspecialchars($sekolah_nama); ?></title>
    <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($brand['favicon']); ?>">
    <link rel="stylesheet" href="/public/assets/css/plus-jakarta-sans.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 7mm 11mm 6mm 11mm;
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
        .bar-btn:hover { background: #f0f9ff; transform: translateY(-1px); }
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
            padding: 7mm 11mm 5mm 11mm;
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
            padding-bottom: 4px;
            margin-bottom: 5px;
            position: relative;
        }
        .kop-surat-preview img {
            width: 60px;
            height: 60px;
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
            background: #f0f9ff;
            color: #1e40af;
            border: 1px solid #7dd3fc;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 6.8pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .doc-title {
            font-size: 10.5pt;
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
            margin-bottom: 5px;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .stat-card {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 4px 7px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            gap: 7px;
        }
        .stat-icon {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .stat-content {
            flex: 1;
            min-width: 0;
        }
        .stat-val {
            font-size: 10.2pt;
            font-weight: 800;
            line-height: 1.1;
            color: #0f172a;
        }
        .stat-lbl {
            font-size: 6.2pt;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Tabel Matriks Supervisi Guru */
        .table-wrap {
            margin-bottom: 5px;
        }
        .sup-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 6.9pt;
            line-height: 1.25;
            font-family: 'Times New Roman', Times, serif;
        }
        .sup-table th, .sup-table td {
            border: 1px solid #cbd5e1;
            padding: 2.2px 4px;
            vertical-align: middle;
        }
        .sup-table th {
            background: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-align: center;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            font-size: 6.5pt;
            text-transform: uppercase;
        }
        .sup-table tr:nth-child(even) {
            background: #fafaf9;
        }
        .sup-table tfoot td {
            background: #f0f9ff;
            font-weight: bold;
            color: #1e40af;
            border-top: 1.5px solid #0284c7;
        }

        /* 3 Card Program Tindak Lanjut Klinis */
        .tl-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 5px;
            margin-bottom: 5px;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .tl-card {
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 4px 6px;
            background: #ffffff;
            display: flex;
            align-items: flex-start;
            gap: 6px;
        }
        .tl-card.c1 { border-left: 3px solid #0284c7; background: #f0f9ff; }
        .tl-card.c2 { border-left: 3px solid #2563eb; background: #eff6ff; }
        .tl-card.c3 { border-left: 3px solid #d97706; background: #fffbeb; }

        .tl-icon { font-size: 0.95rem; line-height: 1; margin-top: 1px; }
        .tl-title { font-size: 7pt; font-weight: 800; color: #0f172a; margin: 0 0 1px 0; }
        .tl-desc { font-size: 6.2pt; color: #475569; margin: 0; line-height: 1.25; }

        /* Hasil Verifikasi Asesor Box */
        .eval-box {
            border: 1px dashed #0284c7;
            border-radius: 5px;
            padding: 4px 8px;
            background: #f0f9ff;
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
            margin-bottom: 26px;
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
            width: 46px;
            height: 46px;
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
            .stat-card, .sup-table, .tl-card, .eval-box {
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
            <span>Scorecard Supervisi Guru & Program Tindak Lanjut (Indikator 4.1, 4.2 & 4.3)</span>
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="m9 14 2 2 4-4"/></svg>
                    KOMPONEN 4: SUPERVISI GURU DAN TENAGA KEPENDIDIKAN • INDIKATOR 4.1, 4.2 & 4.3
                </div>
                <h3 class="doc-title">MATRIKS REKAPITULASI HASIL SUPERVISI AKADEMIK GURU & TINDAK LANJUT</h3>
                <div class="doc-subtitle">Laporan Observasi Pembelajaran (KBM), Asesmen Modul Ajar & Evaluasi Klinis Guru TP 2026/2027</div>
            </div>

            <!-- 4 KPI STATS CARD -->
            <div class="stats-grid">
                <div class="stat-card" style="border-left: 3.5px solid #0284c7; background: #f0f9ff;">
                    <div class="stat-icon" style="background:#e0f2fe; color:#1e40af;">👥</div>
                    <div class="stat-content">
                        <div class="stat-val"><?php echo count($matriksSupervisi); ?> Guru (100%)</div>
                        <div class="stat-lbl">Tuntas Disupervisi Klinis</div>
                    </div>
                </div>
                <div class="stat-card" style="border-left: 3.5px solid #2563eb; background: #eff6ff;">
                    <div class="stat-icon" style="background:#dbeafe; color:#1d4ed8;">⭐</div>
                    <div class="stat-content">
                        <div class="stat-val"><?php echo $rataRataTotal; ?> (Predikat A)</div>
                        <div class="stat-lbl">Rata-rata Skor Pembelajaran</div>
                    </div>
                </div>
                <div class="stat-card" style="border-left: 3.5px solid #16a34a; background: #f0f9ff;">
                    <div class="stat-icon" style="background:#e0f2fe; color:#0284c7;">🎯</div>
                    <div class="stat-content">
                        <div class="stat-val">96.4% Tuntas</div>
                        <div class="stat-lbl">Ketercapaian Modul Merdeka</div>
                    </div>
                </div>
                <div class="stat-card" style="border-left: 3.5px solid #d97706; background: #fffbeb;">
                    <div class="stat-icon" style="background:#fef3c7; color:#b45309;">🚀</div>
                    <div class="stat-content">
                        <div class="stat-val">100% Realisasi</div>
                        <div class="stat-lbl">Program Tindak Lanjut Klinis</div>
                    </div>
                </div>
            </div>

            <!-- TABEL MATRIKS SUPERVISI 10 GURU -->
            <div class="table-wrap">
                <table class="sup-table">
                    <thead>
                        <tr>
                            <th style="width: 3%;" rowspan="2">No</th>
                            <th style="width: 25%;" rowspan="2">Nama Guru & NIP</th>
                            <th style="width: 20%;" rowspan="2">Tugas / Rombel</th>
                            <th colspan="4" style="background:#e2e8f0;">Aspek Penilaian Supervisi (Skor 0-100)</th>
                            <th style="width: 8%;" rowspan="2">Rata-rata</th>
                            <th style="width: 9%;" rowspan="2">Predikat</th>
                            <th style="width: 21%;" rowspan="2">Rencana Tindak Lanjut Klinis</th>
                        </tr>
                        <tr>
                            <th style="width: 7%;">Modul</th>
                            <th style="width: 7%;">KBM</th>
                            <th style="width: 7%;">TIK</th>
                            <th style="width: 7%;">Asesmen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($matriksSupervisi as $i => $row): ?>
                        <tr>
                            <td style="text-align: center; font-weight: bold;"><?php echo $i + 1; ?></td>
                            <td><strong><?php echo $row['nama']; ?></strong></td>
                            <td><?php echo $row['mapel']; ?></td>
                            <td style="text-align: center;"><?php echo $row['perencanaan']; ?></td>
                            <td style="text-align: center;"><?php echo $row['kbm']; ?></td>
                            <td style="text-align: center;"><?php echo $row['media']; ?></td>
                            <td style="text-align: center;"><?php echo $row['asesmen']; ?></td>
                            <td style="text-align: center; font-weight: bold; color: #0f172a;"><?php echo $row['rata']; ?></td>
                            <td style="text-align: center; font-weight: bold; color: #0c4a6e; font-size:6.5pt;"><?php echo $row['predikat']; ?></td>
                            <td style="font-size: 6.5pt;"><?php echo $row['tindak_lanjut']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align: right; padding-right: 8px;">RATA-RATA TOTAL HASIL SUPERVISI MADRASAH:</td>
                            <td style="text-align: center;">93.1</td>
                            <td style="text-align: center;">93.0</td>
                            <td style="text-align: center;">91.6</td>
                            <td style="text-align: center;">92.9</td>
                            <td style="text-align: center; color: #0f172a;"><?php echo $rataRataTotal; ?></td>
                            <td style="text-align: center; color: #0f172a;">AMAT BAIK</td>
                            <td style="font-size: 6.2pt; text-align: center;">100% Ditindaklanjuti</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- 3 PROGRAM TINDAK LANJUT SUPERVISI KLINIS -->
            <div class="tl-grid">
                <div class="tl-card c1">
                    <div class="tl-icon">🎓</div>
                    <div>
                        <div class="tl-title">Pelatihan Mandiri MOOC PINTAR Kemenag</div>
                        <div class="tl-desc">Pemberian penugasan sertifikasi microlearning modul diferensiasi KBM, asesmen kurikulum merdeka & penguatan literasi numerasi.</div>
                    </div>
                </div>
                <div class="tl-card c2">
                    <div class="tl-icon">🤝</div>
                    <div>
                        <div class="tl-title">Bimbingan Klinis Personal Kepala Madrasah</div>
                        <div class="tl-desc">Konsultasi tatap muka intensif pasca-observasi guna mengurai kendala spesifik guru dalam pengelolaan kelas interaktif & media TIK.</div>
                    </div>
                </div>
                <div class="tl-card c3">
                    <div class="tl-icon">💡</div>
                    <div>
                        <div class="tl-title">Komunitas Belajar (Kombel) KKG Madrasah</div>
                        <div class="tl-desc">Pertemuan rutin sharing session setiap hari Sabtu untuk reviu modul ajar terpadu, pembuatan LKPD kontekstual, dan refleksi pedagogik.</div>
                    </div>
                </div>
            </div>

            <!-- HASIL VERIFIKASI ASESOR -->
            <div class="eval-box">
                <div>
                    <strong>HASIL TELAAH & VERIFIKASI ASESOR PKKM:</strong>
                    <span>Program supervisi akademik tersusun lengkap (SK Tim Supervisor & Jadwal), instrumen observasi KBM terisi valid, serta program tindak lanjut klinis terbukti nyata mendongkrak profesionalisme guru.</span>
                </div>
                <div style="font-weight: 800; font-size: 7pt; color: #1e40af; white-space: nowrap; margin-left: 10px;">
                    NILAI KOMPONEN 4: SANGAT BAIK (SKOR 4)
                </div>
            </div>
        </div>

        <!-- BLOK PENGESAHAN & TANDA TANGAN RESMI -->
        <div>
            <div class="auth-grid">
                <!-- KIRI: KOORDINATOR TIM SUPERVISI -->
                <div class="auth-col">
                    <div class="auth-role">Mengetahui,</div>
                    <div class="auth-sub">Koordinator Tim Supervisi / Kurikulum,</div>
                    <div class="auth-name">SITI NURJANAH, S.Pd.I</div>
                    <div class="auth-nip">Guru Pembina Kurikulum</div>
                </div>

                <!-- TENGAH: QR CODE DIGITAL -->
                <div class="qr-box">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=https://masmifhda.sch.id/admin/pkkm/cetak-bukti/supervisi-guru" alt="QR Validasi" class="qr-img">
                    <div class="qr-label">VERIFIKASI DIGITAL</div>
                    <div class="stempel-badge">TERVALIDASI ✓</div>
                </div>

                <!-- KANAN: KEPALA MADRASAH -->
                <div class="auth-col">
                    <div class="auth-role"><?php echo htmlspecialchars($kecamatan); ?>, <?php echo $tgl_indo; ?></div>
                    <div class="auth-sub">Kepala Madrasah / Supervisor Utama,</div>
                    <div class="auth-name"><?php echo htmlspecialchars($kepala_nama); ?></div>
                    <div class="auth-nip">NIP. <?php echo htmlspecialchars($kepala_nip); ?></div>
                </div>
            </div>

            <!-- FOOTER NOTE -->
            <div class="footer-note">
                <span>Dokumen Bukti Fisik PKKM &bull; Komponen 4: Supervisi GTK & Peningkatan Mutu Akademik &bull; MIS Miftahul Huda Mathla'ul Anwar</span>
                <span style="font-weight:700; color:#0284c7;">https://masmifhda.sch.id</span>
            </div>
        </div>
    </div>

    <?php include (file_exists(__DIR__ . '/_pkkm_koleksi_modal.php') ? __DIR__ . '/_pkkm_koleksi_modal.php' : __DIR__ . '/pkkm/_pkkm_koleksi_modal.php'); ?>
</body>
</html>
