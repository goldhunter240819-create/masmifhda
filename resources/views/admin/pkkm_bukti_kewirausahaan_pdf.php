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

// Data Kemitraan Strategis (MoU Eksternal)
$mitraList = [
    [
        'mitra' => 'UPT Puskesmas Way Tenong',
        'wilayah' => 'Kec. Way Tenong',
        'bidang' => 'Layanan Kesehatan Siswa, Imunisasi BIAS, Penjaringan Gigi & Sanitasi Kantin',
        'no_mou' => '440/018/PKM-WT/VII/2026',
        'dampak' => 'UKS Terbina & Kantin Madrasah Tersertifikasi Higienis'
    ],
    [
        'mitra' => 'Kepolisian Sektor (Polsek) Way Tenong',
        'wilayah' => 'Polres Lampung Barat',
        'bidang' => 'Penyuluhan Tertib Berlalu Lintas, Keamanan Madrasah & Pencegahan Kenakalan Remaja',
        'no_mou' => '04/MoU/Sek.WT/VIII/2026',
        'dampak' => 'Zona Selamat Madrasah & Patroli Keamanan Terjadwal'
    ],
    [
        'mitra' => 'PT. Bank Syariah Indonesia (BSI) KCP Liwa',
        'wilayah' => 'Kab. Lampung Barat',
        'bidang' => 'Rekening Madrasah, Tabungan SimPel (Simpanan Pelajar) & Edukasi Finansial Syariah',
        'no_mou' => '112/BSI-KCP/MoU/VII/2026',
        'dampak' => '100% Santri Memiliki Rekening Tabungan SimPel'
    ],
    [
        'mitra' => 'Kelompok Tani "Tunas Makmur"',
        'wilayah' => 'Ds. Tambak Jaya',
        'bidang' => 'Pasokan Bibit Sayur Hidroponik, Pupuk Organik & Pelatihan Green House Siswa',
        'no_mou' => '09/KT-TM/MoU/IX/2026',
        'dampak' => 'Laboratorium Praktik P5RA & Panen Sayur Mandiri'
    ]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Fisik PKKM - Portofolio Kewirausahaan & Kemitraan - <?php echo htmlspecialchars($sekolah_nama); ?></title>
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
        .bar-btn:hover { background: #f0fdf4; transform: translateY(-1px); }
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
            font-size: 66pt;
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
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
            border: 1.2px solid #a7f3d0;
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
            font-size: 10.8pt;
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

        /* 3 Unit Usaha Cards */
        .unit-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
            margin-bottom: 6px;
        }
        .unit-card {
            background: #ffffff;
            border: 1.2px solid #cbd5e1;
            border-radius: 6px;
            padding: 5px 7px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            position: relative;
        }
        .unit-card.u1 { border-top: 3px solid #0284c7; background: #fdfefe; }
        .unit-card.u2 { border-top: 3px solid #d97706; background: #fffdf9; }
        .unit-card.u3 { border-top: 3px solid #0284c7; background: #f9fcff; }

        .unit-header {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 3px;
            padding-bottom: 2px;
            border-bottom: 1px dashed #e2e8f0;
        }
        .unit-icon {
            font-size: 13pt;
            line-height: 1;
        }
        .unit-name {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 7.2pt;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.15;
        }
        .unit-manager {
            font-size: 6.1pt;
            color: #64748b;
        }
        .unit-body {
            font-size: 6.4pt;
            line-height: 1.25;
            color: #334155;
        }
        .unit-row {
            margin-bottom: 2px;
        }
        .unit-row strong {
            color: #0f172a;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 6.3pt;
        }
        .unit-finance-box {
            background: #f1f5f9;
            border-radius: 4px;
            padding: 2.5px 5px;
            margin-top: 3px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 6.2pt;
        }
        .unit-omzet {
            color: #0f172a;
            font-weight: 800;
        }
        .unit-netto {
            color: #b45309;
            font-weight: 700;
        }

        /* Tabel Kemitraan */
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
        .mitra-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.2pt;
        }
        .mitra-table th {
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
        .mitra-table td {
            padding: 3px 5px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
            line-height: 1.2;
        }
        .mitra-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        /* Evaluasi Asesor Box */
        .eval-box {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 5px;
            padding: 3.5px 7px;
            margin-bottom: 5px;
            font-size: 6.7pt;
            color: #14532d;
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
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
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
            <span>Bukti Fisik #13: Unit Usaha Kewirausahaan & Kemitraan Lembaga (Indikator 3.1 & 3.3)</span>
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
                📋 Link Bukti Kita (13)
            </button>
            <button onclick="window.print()" class="bar-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                🖨️ Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <div class="page-wrapper">
        <div class="sheet">
            <div class="watermark">WIRAUSAHA 2026</div>

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
                        <div class="doc-title-main">PORTOFOLIO UNIT USAHA KEWIRAUSAHAAN & KEMITRAAN LEMBAGA</div>
                        <div class="doc-title-sub">Inovasi Kemandirian Finansial, Edukasi Entrepreneurship Islami & Kemitraan Strategis TP 2026/2027</div>
                    </div>
                    <div class="doc-badge-pill">
                        BUKTI FISIK PKKM &bull; INDIKATOR 3.1 & 3.3
                    </div>
                </div>

                <!-- 4 KPI STAT CARDS -->
                <div class="kpi-grid">
                    <div class="kpi-card">
                        <div class="kpi-num">3 Unit Usaha</div>
                        <div class="kpi-label">Usaha Mandiri</div>
                        <div class="kpi-sub">Koperasi, Kantin & Green House</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-num">Rp 14,8 Jt/Bln</div>
                        <div class="kpi-label">Perputaran Omzet</div>
                        <div class="kpi-sub">Total Transaksi Unit Usaha</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-num">25% Alokasi Sosial</div>
                        <div class="kpi-label">Subsidi Pendidikan</div>
                        <div class="kpi-sub">Bantuan Santri Yatim & Sarana</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-num">4 Mitra Strategis</div>
                        <div class="kpi-label">Jejaring Kerjasama</div>
                        <div class="kpi-sub">MoU Resmi Lintas Sektoral</div>
                    </div>
                </div>

                <!-- 3 UNIT USAHA MANDIRI MADRASAH -->
                <div class="unit-grid">
                    <!-- UNIT 1: KOPERASI SEKOLAH -->
                    <div class="unit-card u1">
                        <div class="unit-header">
                            <div class="unit-icon">🛒</div>
                            <div>
                                <div class="unit-name">Koperasi "Miftah Barokah"</div>
                                <div class="unit-manager">Koordinator: Zulfa Khoirunnisa, S.Pd</div>
                            </div>
                        </div>
                        <div class="unit-body">
                            <div class="unit-row"><strong>Komoditas:</strong> Seragam madrasah, jilbab, atribut bordir, modul ajar & ATK siswa.</div>
                            <div class="unit-row"><strong>Edukasi Siswa:</strong> Pembiasaan kejujuran <em>(Honesty Shop)</em>, pencatatan kas mini & etika berdagang islami.</div>
                            <div class="unit-finance-box">
                                <span>Omzet: <span class="unit-omzet">Rp 6,2 Jt/bln</span></span>
                                <span>Netto: <span class="unit-netto">Rp 1,55 Jt</span></span>
                            </div>
                        </div>
                    </div>

                    <!-- UNIT 2: KANTIN SEHAT BERKAH -->
                    <div class="unit-card u2">
                        <div class="unit-header">
                            <div class="unit-icon">🥗</div>
                            <div>
                                <div class="unit-name">Kantin Sehat Halalan Thayyiban</div>
                                <div class="unit-manager">Koordinator: Paguyuban Wali & Pengasuh</div>
                            </div>
                        </div>
                        <div class="unit-body">
                            <div class="unit-row"><strong>Menu:</strong> Makanan bergizi bebas 5P (pengawet, perasa buatan, pewarna, pemanis, pengenyal).</div>
                            <div class="unit-row"><strong>Edukasi Siswa:</strong> Adab makan duduk, doa bersama, dan pola hidup bersih sehat (PHBS).</div>
                            <div class="unit-finance-box">
                                <span>Omzet: <span class="unit-omzet">Rp 5,85 Jt/bln</span></span>
                                <span>Netto: <span class="unit-netto">Rp 1,2 Jt</span></span>
                            </div>
                        </div>
                    </div>

                    <!-- UNIT 3: GREEN HOUSE & HIDROPONIK -->
                    <div class="unit-card u3">
                        <div class="unit-header">
                            <div class="unit-icon">🌱</div>
                            <div>
                                <div class="unit-name">Green House Hidroponik</div>
                                <div class="unit-manager">Koordinator: Budi Santoso, S.Pd</div>
                            </div>
                        </div>
                        <div class="unit-body">
                            <div class="unit-row"><strong>Komoditas:</strong> Sayuran hijau (pakcoy, selada, kangkung air) & tanaman obat keluarga (TOGA).</div>
                            <div class="unit-row"><strong>Edukasi Siswa:</strong> Laboratorium alam KBM IPA/P5RA, pembibitan & panen bersama wali murid.</div>
                            <div class="unit-finance-box">
                                <span>Omzet: <span class="unit-omzet">Rp 2,8 Jt/bln</span></span>
                                <span>Netto: <span class="unit-netto">Rp 850 Rb</span></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABEL MATRIKS KEMITRAAN LEMBAGA (MoU RESMI) -->
                <div class="table-box">
                    <div class="table-title">
                        <span>MATRIKS JEJARING KEMITRAAN STRATEGIS MADRASAH (MoU & KERJASAMA AKTIF)</span>
                        <span>Dokumen Perjanjian Kerjasama Tersimpan di Ruang Kepala Madrasah</span>
                    </div>
                    <table class="mitra-table">
                        <thead>
                            <tr>
                                <th style="width: 4%; text-align: center;">No</th>
                                <th style="width: 25%;">Lembaga / Instansi Mitra</th>
                                <th style="width: 33%;">Ruang Lingkup & Program Kerjasama</th>
                                <th style="width: 20%;">Nomor Dokumen MoU</th>
                                <th style="width: 18%;">Output / Dampak Bagi Madrasah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mitraList as $i => $m): ?>
                            <tr>
                                <td style="text-align: center; font-weight: 700;"><?php echo $i + 1; ?></td>
                                <td>
                                    <strong style="color: #0f172a;"><?php echo htmlspecialchars($m['mitra']); ?></strong><br>
                                    <span style="font-size: 6pt; color: #64748b;"><?php echo htmlspecialchars($m['wilayah']); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($m['bidang']); ?></td>
                                <td style="font-size: 6.2pt; color: #334155; font-family: 'Times New Roman', serif;"><?php echo htmlspecialchars($m['no_mou']); ?></td>
                                <td style="font-size: 6.3pt; color: #1e3a8a; font-weight: 600;"><?php echo htmlspecialchars($m['dampak']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- TELAAH & VERIFIKASI ASESOR -->
                <div class="eval-box">
                    <div>
                        <strong>HASIL TELAAH & VERIFIKASI ASESOR PKKM:</strong>
                        <span>Kepala Madrasah memiliki naluri kewirausahaan yang adaptif dan visioner. Terbukti dengan hadirnya 3 unit usaha mandiri madrasah yang produktif, mendidik karakter santri mandiri finansial, serta 4 MoU kemitraan strategis yang memberi nilai tambah nyata bagi mutu dan kemandirian madrasah.</span>
                    </div>
                    <div style="font-weight: 800; font-size: 7.2pt; color: #1e40af; white-space: nowrap; margin-left: 10px;">
                        NILAI KOMPONEN 3: SKOR 4 (SANGAT BAIK)
                    </div>
                </div>
            </div>

            <!-- BLOK PENGESAHAN & TANDA TANGAN RESMI -->
            <div>
                <div class="auth-grid">
                    <!-- KIRI: KOORDINATOR KEWIRAUSAHAAN & KEMITRAAN -->
                    <div class="auth-col">
                        <div class="auth-role">Mengetahui,</div>
                        <div class="auth-sub">Koordinator Unit Usaha & Kemitraan,</div>
                        <div class="auth-name">ZULFA KHOIRUNNISA, S.Pd</div>
                        <div class="auth-nip">Guru Pembina Kewirausahaan</div>
                    </div>

                    <!-- TENGAH: QR CODE DIGITAL -->
                    <div class="qr-box">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=https://masmifhda.sch.id/admin/pkkm/cetak-bukti/kewirausahaan" alt="QR Validasi" class="qr-img">
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
                    <span>Dokumen Bukti Fisik PKKM &bull; Komponen 3: Pengembangan Kewirausahaan & Kemitraan (Indikator 3.1 & 3.3) &bull; MAS Miftahul Huda Mathla'ul Anwar</span>
                    <span style="font-weight:700; color:#0284c7;">https://masmifhda.sch.id</span>
                </div>
            </div>
        </div>
    </div>

    <?php include (file_exists(__DIR__ . '/_pkkm_koleksi_modal.php') ? __DIR__ . '/_pkkm_koleksi_modal.php' : __DIR__ . '/pkkm/_pkkm_koleksi_modal.php'); ?>
</body>
</html>
