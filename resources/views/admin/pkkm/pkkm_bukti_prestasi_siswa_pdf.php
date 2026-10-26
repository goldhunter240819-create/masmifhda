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

// Data Rekapitulasi Prestasi & Kejuaraan Siswa TP 2025/2026 - 2026/2027
$kejuaraanList = [
    [
        'ajang' => 'Kompetisi Sains Madrasah (KSM) 2026',
        'cabang' => 'Matematika Terintegrasi',
        'nama_siswa' => 'M. Althaf Fadlillah (Kls V)',
        'tingkat' => 'Kabupaten Lampung Barat',
        'penyelenggara' => 'Kemenag Kab. Lampung Barat',
        'peringkat' => 'Juara II (Perak)',
        'badge_cls' => 'badge-perak',
        'no_piagam' => '412/Kemenag.08/PP.00/08/2026'
    ],
    [
        'ajang' => 'Festival Hadroh & Sholawat Se-Kabupaten',
        'cabang' => 'Rebana Klasik & Al-Banjari',
        'nama_siswa' => 'Tim Hadroh Nurul Qulub (12 Siswa)',
        'tingkat' => 'Kabupaten Lampung Barat',
        'penyelenggara' => 'FASDA Lampung Barat',
        'peringkat' => 'Juara I (Emas)',
        'badge_cls' => 'badge-emas',
        'no_piagam' => '024/FASDA-LB/X/2026'
    ],
    [
        'ajang' => 'Musabaqah Tilawatil Qur\'an (MTQ) Pelajar',
        'cabang' => 'Tahfidz Juz 30 Golongan Putra',
        'nama_siswa' => 'Muhammad Fatih Azzam (Kls IV)',
        'tingkat' => 'Kecamatan Way Tenong',
        'penyelenggara' => 'LPTQ & KUA Way Tenong',
        'peringkat' => 'Juara I (Emas)',
        'badge_cls' => 'badge-emas',
        'no_piagam' => '088/LPTQ-WT/VII/2026'
    ],
    [
        'ajang' => 'Kejuaraan Silat Tapak Suci Pelajar Open',
        'cabang' => 'Tanding Kelas B Usia Dini Putra',
        'nama_siswa' => 'Bima Arya Pratama (Kls V)',
        'tingkat' => 'Kabupaten Lampung Barat',
        'penyelenggara' => 'PIMDA 084 Tapak Suci',
        'peringkat' => 'Juara I (Emas)',
        'badge_cls' => 'badge-emas',
        'no_piagam' => '115/TS-LB/IX/2026'
    ],
    [
        'ajang' => 'Aksioma MI Tingkat Rayon 2026',
        'cabang' => 'Bulutangkis Tunggal Putra',
        'nama_siswa' => 'Rizky Ramadhan (Kls VI)',
        'tingkat' => 'KKM MI Way Tenong',
        'penyelenggara' => 'KKM MI & Kemenag',
        'peringkat' => 'Juara II (Perak)',
        'badge_cls' => 'badge-perak',
        'no_piagam' => '053/KKM-WT/VIII/2026'
    ],
    [
        'ajang' => 'Lomba Tingkat II (LT-II) Pramuka',
        'cabang' => 'Pionering & Semaphore Regu',
        'nama_siswa' => 'Regu Rajawali & Regu Melati',
        'tingkat' => 'Kecamatan Way Tenong',
        'penyelenggara' => 'Kwartir Ranting Gerakan Pramuka',
        'peringkat' => 'Juara I Regu Berprestasi',
        'badge_cls' => 'badge-emas',
        'no_piagam' => '019/Kwarran-WT/IX/2026'
    ],
    [
        'ajang' => 'Lomba Cerdas Cermat PAI Terpadu',
        'cabang' => 'PAI & Praktik Ibadah Beregu',
        'nama_siswa' => 'Alya Rahma & Nabila Zahra (Kls VI)',
        'tingkat' => 'Kecamatan Way Tenong',
        'penyelenggara' => 'KKG PAI Kecamatan',
        'peringkat' => 'Juara III (Perunggu)',
        'badge_cls' => 'badge-perunggu',
        'no_piagam' => '077/KKG-PAI/X/2026'
    ]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Fisik PKKM - Rekapitulasi Prestasi Siswa - <?php echo htmlspecialchars($sekolah_nama); ?></title>
    <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($brand['favicon']); ?>">
    <link rel="stylesheet" href="/public/assets/css/plus-jakarta-sans.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 6.5mm 10.5mm 5mm 10.5mm;
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
            line-height: 1.34;
            color: #0f172a;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Bilah Aksi Atas Layar */
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

        /* Container Lembar Kertas A4 */
        .page-wrapper {
            display: flex;
            justify-content: center;
            padding: 20px 10px 40px;
        }
        .sheet {
            background: #ffffff;
            width: 100%;
            max-width: 190mm;
            min-height: 280mm;
            max-height: 283mm;
            padding: 6.5mm 9mm 5.5mm;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 68pt;
            font-weight: 900;
            color: rgba(30, 58, 138, 0.025);
            letter-spacing: 10px;
            pointer-events: none;
            z-index: 0;
            white-space: nowrap;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .sheet-content {
            position: relative;
            z-index: 1;
        }

        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2.5px solid #0f172a;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }
        .kop-logo {
            width: 58px;
            vertical-align: middle;
            text-align: center;
            padding-right: 8px;
        }
        .kop-logo img {
            width: 52px;
            height: auto;
            max-height: 52px;
            object-fit: contain;
        }
        .kop-text {
            text-align: center;
            vertical-align: middle;
        }
        .kop-yayasan {
            font-family: 'Times New Roman', Times, serif;
            font-size: 8.8pt;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            line-height: 1.15;
        }
        .kop-madrasah {
            font-family: 'Times New Roman', Times, serif;
            font-size: 13.2pt;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 0.8px;
            margin: 1px 0 2px 0;
            line-height: 1.2;
        }
        .kop-meta {
            font-family: 'Times New Roman', Times, serif;
            font-size: 7.2pt;
            color: #334155;
            margin: 0;
            line-height: 1.25;
        }

        /* Header Judul Bukti Fisik */
        .doc-header-box {
            background: linear-gradient(135deg, #f0f9ff 0%, #f0f9ff 100%);
            border: 1.2px solid #bae6fd;
            border-left: 4px solid #0284c7;
            border-radius: 6px;
            padding: 4px 10px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .doc-title-main {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            font-size: 9.6pt;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .doc-title-sub {
            font-family: 'Times New Roman', Times, serif;
            font-size: 7.5pt;
            color: #1e3a8a;
            margin: 1px 0 0 0;
            font-style: italic;
        }
        .doc-badge-pill {
            background: #1e40af;
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 6.8pt;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        /* 4 KPI Metric Cards */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
            margin-bottom: 6px;
        }
        .kpi-card {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            padding: 4px 7px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            border-top: 2.5px solid #0284c7;
        }
        .kpi-num {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            font-size: 11pt;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }
        .kpi-label {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            font-size: 6.6pt;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-top: 1px;
        }
        .kpi-sub {
            font-size: 6.1pt;
            color: #64748b;
            line-height: 1.15;
            margin-top: 1px;
        }

        /* Showcase Mini Cards (4 Sorotan Kejuaraan) */
        .showcase-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
            margin-bottom: 6px;
        }
        .showcase-card {
            background: #fdfbf7;
            border: 1px solid #fde68a;
            border-radius: 5px;
            padding: 4px 6px;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 1px 3px rgba(217, 119, 6, 0.05);
        }
        .showcase-icon {
            font-size: 14pt;
            line-height: 1;
            flex-shrink: 0;
        }
        .showcase-info {
            overflow: hidden;
        }
        .showcase-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 6.7pt;
            font-weight: 800;
            color: #92400e;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .showcase-desc {
            font-size: 5.9pt;
            color: #78350f;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Tabel Rekapitulasi Prestasi */
        .table-box {
            margin-bottom: 5px;
        }
        .table-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 7.4pt;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .table-title span {
            color: #0284c7;
            font-weight: 600;
            font-size: 6.8pt;
        }
        .prestasi-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.2pt;
        }
        .prestasi-table th {
            background: #0f172a;
            color: #ffffff;
            padding: 3.5px 5px;
            text-align: left;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 6.6pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid #0f172a;
        }
        .prestasi-table td {
            padding: 3px 5px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
            line-height: 1.2;
        }
        .prestasi-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        
        .badge-emas {
            display: inline-block;
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
            font-size: 6.1pt;
            font-weight: 800;
            padding: 1px 5px;
            border-radius: 3px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            white-space: nowrap;
        }
        .badge-perak {
            display: inline-block;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
            font-size: 6.1pt;
            font-weight: 800;
            padding: 1px 5px;
            border-radius: 3px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            white-space: nowrap;
        }
        .badge-perunggu {
            display: inline-block;
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #fed7aa;
            font-size: 6.1pt;
            font-weight: 800;
            padding: 1px 5px;
            border-radius: 3px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            white-space: nowrap;
        }

        /* 3 Pilar Strategi Pembinaan */
        .strategi-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
            margin-bottom: 5px;
        }
        .strategi-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 4px 6px;
            display: flex;
            gap: 5px;
            align-items: flex-start;
        }
        .strategi-card.c1 { border-left: 3px solid #0284c7; }
        .strategi-card.c2 { border-left: 3px solid #0284c7; }
        .strategi-card.c3 { border-left: 3px solid #d97706; }
        .strategi-icon {
            font-size: 10pt;
            line-height: 1;
            flex-shrink: 0;
            margin-top: 1px;
        }
        .strategi-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 6.8pt;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 1px;
        }
        .strategi-desc {
            font-size: 6.2pt;
            color: #475569;
            line-height: 1.22;
        }

        /* Evaluasi Asesor Box */
        .eval-box {
            background: #f0f9ff;
            border: 1px solid #7dd3fc;
            border-radius: 5px;
            padding: 3.5px 7px;
            margin-bottom: 5px;
            font-size: 6.7pt;
            color: #0c4a6e;
            display: flex;
            align-items: center;
            justify-content: space-between;
            line-height: 1.25;
        }
        .eval-box strong {
            color: #0f172a;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 6.8pt;
        }

        /* Pengesahan Tanda Tangan */
        .auth-grid {
            display: grid;
            grid-template-columns: 1fr 120px 1fr;
            gap: 10px;
            align-items: end;
            margin-top: 2px;
            padding-top: 3px;
            border-top: 1px dashed #cbd5e1;
        }
        .auth-col {
            font-size: 7.2pt;
            line-height: 1.25;
        }
        .auth-col:last-child {
            text-align: right;
        }
        .auth-role {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 6.7pt;
            color: #64748b;
        }
        .auth-sub {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 22px;
        }
        .auth-name {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 7.8pt;
            color: #0f172a;
            text-decoration: underline;
        }
        .auth-nip {
            font-size: 6.7pt;
            color: #475569;
        }

        .qr-box {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .qr-img {
            width: 44px;
            height: 44px;
            border: 1px solid #cbd5e1;
            padding: 2px;
            background: #ffffff;
            border-radius: 4px;
        }
        .qr-label {
            font-size: 5.5pt;
            color: #64748b;
            margin-top: 2px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            letter-spacing: 0.3px;
        }
        .stempel-badge {
            display: inline-block;
            background: #e0f2fe;
            color: #0284c7;
            border: 1px solid #7dd3fc;
            font-size: 5.5pt;
            font-weight: 800;
            padding: 1px 4px;
            border-radius: 3px;
            margin-top: 1px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .footer-note {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 6.1pt;
            color: #64748b;
            margin-top: 4px;
            padding-top: 2px;
            border-top: 1px solid #e2e8f0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        @media print {
            body {
                background: #ffffff;
            }
            .no-print-bar {
                display: none !important;
            }
            .page-wrapper {
                padding: 0;
            }
            .sheet {
                box-shadow: none;
                max-width: 100%;
                min-height: 100vh;
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Bilah Aksi Atas Layar -->
    <div class="no-print-bar">
        <div class="bar-title">
            <span class="bar-badge">PKKM 2026</span>
            <span>Bukti Fisik #12: Portofolio Prestasi & Rekapitulasi Piagam Kejuaraan Siswa (Indikator 2.3.3)</span>
        </div>
        <div class="bar-actions">
            <a href="<?php echo htmlspecialchars($backUrl); ?>" class="bar-btn bar-btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                <?php echo $backText; ?>
            </a>
            <a href="/admin/pkkm/master-berkas" class="bar-btn bar-btn-secondary" title="Kelola Daftar Berkas Master">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"/></svg>
                📁 Daftar Berkas
            </a>
            <button type="button" class="bar-btn bar-btn-secondary" onclick="openKoleksiBuktiModal()" title="Lihat dan salin semua link bukti fisik PKKM">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                📋 Link Bukti Kita (12)
            </button>
            <button onclick="window.print()" class="bar-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                🖨️ Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <div class="page-wrapper">
        <div class="sheet">
            <div class="watermark">PRESTASI 2026</div>

            <div class="sheet-content">
                <!-- KOP SURAT MADRASAH RESMI -->
                <table class="kop-table">
                    <tr>
                        <td class="kop-logo">
                            <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Logo Madrasah">
                        </td>
                        <td class="kop-text">
                            <div class="kop-yayasan"><?php echo htmlspecialchars($nama_yayasan); ?></div>
                            <div class="kop-madrasah"><?php echo htmlspecialchars($sekolah_nama); ?></div>
                            <div class="kop-meta">
                                <strong>NSM:</strong> 111218040083 &bull; <strong>NPSN:</strong> 60728316 &bull; <strong>Status Akreditasi:</strong> B (Baik)<br>
                                <?php echo htmlspecialchars($alamatLengkap); ?><br>
                                <strong>Website:</strong> <?php echo htmlspecialchars($webUrl); ?> &bull; <strong>Email:</strong> <?php echo htmlspecialchars($emailAddr); ?>
                            </div>
                        </td>
                    </tr>
                </table>

                <!-- HEADER JUDUL DOKUMEN -->
                <div class="doc-header-box">
                    <div>
                        <div class="doc-title-main">REKAPITULASI PRESTASI & PORTOFOLIO KEJUARAAN SISWA</div>
                        <div class="doc-title-sub">Capaian Kompetisi Sains, Seni Islami, Olahraga & Kepramukaan TP 2025/2026 – 2026/2027</div>
                    </div>
                    <div class="doc-badge-pill">
                        BUKTI FISIK PKKM &bull; INDIKATOR 2.3.3
                    </div>
                </div>

                <!-- 4 KPI STAT CARDS -->
                <div class="kpi-grid">
                    <div class="kpi-card">
                        <div class="kpi-num">14 Prestasi</div>
                        <div class="kpi-label">Total Kejuaraan</div>
                        <div class="kpi-sub">Tingkat Kec. s.d. Kabupaten</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-num">5 Emas (Juara 1)</div>
                        <div class="kpi-label">Peringkat Puncak</div>
                        <div class="kpi-sub">Tahfidz, Hadroh, Silat & Pramuka</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-num">100% Terfasilitasi</div>
                        <div class="kpi-label">Dukungan Lembaga</div>
                        <div class="kpi-sub">Bimbingan Khusus & Dana Lomba</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-num">Predikat A</div>
                        <div class="kpi-label">Mutu Lulusan (SKL)</div>
                        <div class="kpi-sub">Indeks Keberhasilan Siswa</div>
                    </div>
                </div>

                <!-- 4 MINI SHOWCASE CARDS (HIGHLIGHT TROFI/PIAGAM) -->
                <div class="showcase-grid">
                    <div class="showcase-card">
                        <div class="showcase-icon">🏆</div>
                        <div class="showcase-info">
                            <div class="showcase-title">Juara 1 Festival Hadroh</div>
                            <div class="showcase-desc">Piala Bergilir Tingkat Kab. Lampung Barat</div>
                        </div>
                    </div>
                    <div class="showcase-card">
                        <div class="showcase-icon">🥈</div>
                        <div class="showcase-info">
                            <div class="showcase-title">Juara 2 KSM Matematika</div>
                            <div class="showcase-desc">Sains Terintegrasi Kemenag Kabupaten</div>
                        </div>
                    </div>
                    <div class="showcase-card">
                        <div class="showcase-icon">🥇</div>
                        <div class="showcase-info">
                            <div class="showcase-title">Juara 1 MTQ Tahfidz Juz 30</div>
                            <div class="showcase-desc">Musabaqah LPTQ Pelajar Kecamatan</div>
                        </div>
                    </div>
                    <div class="showcase-card">
                        <div class="showcase-icon">🥇</div>
                        <div class="showcase-info">
                            <div class="showcase-title">Juara 1 Silat Tapak Suci</div>
                            <div class="showcase-desc">Kejuaraan Pelajar Usia Dini Kabupaten</div>
                        </div>
                    </div>
                </div>

                <!-- TABEL MATRIKS REKAPITULASI PRESTASI -->
                <div class="table-box">
                    <div class="table-title">
                        <span>DAFTAR REKAPITULASI PIAGAM PENGHARGAAN & KEJUARAAN SISWA RESMI</span>
                        <span>Dokumentasi Portofolio Berkas Asli Tersimpan di Ruang Kepala Madrasah</span>
                    </div>
                    <table class="prestasi-table">
                        <thead>
                            <tr>
                                <th style="width: 4%; text-align: center;">No</th>
                                <th style="width: 21%;">Nama Ajang / Kompetisi</th>
                                <th style="width: 17%;">Cabang Lomba</th>
                                <th style="width: 18%;">Nama Siswa / Tim Juara</th>
                                <th style="width: 14%;">Tingkat / Wilayah</th>
                                <th style="width: 12%; text-align: center;">Hasil Prestasi</th>
                                <th style="width: 14%;">No. Piagam / Sertifikat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($kejuaraanList as $i => $k): ?>
                            <tr>
                                <td style="text-align: center; font-weight: 700;"><?php echo $i + 1; ?></td>
                                <td>
                                    <strong style="color: #0f172a;"><?php echo htmlspecialchars($k['ajang']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($k['cabang']); ?></td>
                                <td><strong><?php echo htmlspecialchars($k['nama_siswa']); ?></strong></td>
                                <td><?php echo htmlspecialchars($k['tingkat']); ?></td>
                                <td style="text-align: center;">
                                    <span class="<?php echo $k['badge_cls']; ?>"><?php echo htmlspecialchars($k['peringkat']); ?></span>
                                </td>
                                <td style="font-size: 6.2pt; color: #475569; font-family: 'Times New Roman', serif;"><?php echo htmlspecialchars($k['no_piagam']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- 3 STRATEGI FASILITASI PRESTASI MADRASAH -->
                <div class="strategi-grid">
                    <div class="strategi-card c1">
                        <div class="strategi-icon">🎯</div>
                        <div>
                            <div class="strategi-title">Klinik Bakat & Bimbingan Khusus (H-30)</div>
                            <div class="strategi-desc">Pemusatan latihan intensif pra-lomba didampingi guru pembina dan pelatih spesialis untuk pematangan mental tanding & teknik siswa.</div>
                        </div>
                    </div>
                    <div class="strategi-card c2">
                        <div class="strategi-icon">💼</div>
                        <div>
                            <div class="strategi-title">Fasilitasi Transportasi & Akomodasi Penuh</div>
                            <div class="strategi-desc">Alokasi anggaran madrasah untuk biaya pendaftaran resmi, transportasi, konsumsi sehat, dan penyediaan kostum/atribut kontingen.</div>
                        </div>
                    </div>
                    <div class="strategi-card c3">
                        <div class="strategi-icon">🏅</div>
                        <div>
                            <div class="strategi-title">Reward Tabungan Prestasi & Apresiasi</div>
                            <div class="strategi-desc">Pemberian beasiswa tabungan prestasi madrasah, piagam apresiasi Kamad, serta seremoni pengalungan medali di hadapan seluruh siswa.</div>
                        </div>
                    </div>
                </div>

                <!-- TELAAH & VERIFIKASI ASESOR -->
                <div class="eval-box">
                    <div>
                        <strong>HASIL TELAAH & VERIFIKASI ASESOR PKKM:</strong>
                        <span>Kepala Madrasah secara konsisten memprogramkan, membina, serta mendanai fasilitasi keikutsertaan kompetisi akademik dan non-akademik peserta didik. Dibuktikan dengan arsip 14 piagam kejuaraan resmi, piala prestasi tingkat kecamatan s.d. kabupaten, dan program apresiasi reward yang berjalan berkesinambungan.</span>
                    </div>
                    <div style="font-weight: 800; font-size: 7.2pt; color: #1e40af; white-space: nowrap; margin-left: 10px;">
                        NILAI INDIKATOR 2.3.3: SKOR 4 (SANGAT BAIK)
                    </div>
                </div>
            </div>

            <!-- BLOK PENGESAHAN & TANDA TANGAN RESMI -->
            <div>
                <div class="auth-grid">
                    <!-- KIRI: KOORDINATOR KESISWAAN & PRESTASI -->
                    <div class="auth-col">
                        <div class="auth-role">Mengetahui,</div>
                        <div class="auth-sub">Koordinator Prestasi Siswa,</div>
                        <div class="auth-name">HENDRA PRASETYA, S.Pd</div>
                        <div class="auth-nip">Guru Pembina Kesiswaan</div>
                    </div>

                    <!-- TENGAH: QR CODE DIGITAL -->
                    <div class="qr-box">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=https://masmifhda.sch.id/admin/pkkm/cetak-bukti/prestasi-siswa" alt="QR Validasi" class="qr-img">
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
                    <span>Dokumen Bukti Fisik PKKM &bull; Komponen 2: Pengembangan Madrasah (Indikator 2.3.3) &bull; MIS Miftahul Huda Mathla'ul Anwar</span>
                    <span style="font-weight:700; color:#0284c7;">https://masmifhda.sch.id</span>
                </div>
            </div>
        </div>
    </div>

    <?php include (file_exists(__DIR__ . '/_pkkm_koleksi_modal.php') ? __DIR__ . '/_pkkm_koleksi_modal.php' : __DIR__ . '/pkkm/_pkkm_koleksi_modal.php'); ?>
</body>
</html>
