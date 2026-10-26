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

// Data Rencana & Rekapitulasi Ekstrakurikuler TP 2026/2027
$ekskulList = [
    [
        'nama' => 'Gerakan Pramuka (Gudep 04.083 - 04.084)',
        'kategori' => 'Wajib (Kls III–VI)',
        'pembina' => 'Hendra Prasetya, S.Pd & Siti Nurjanah, S.Pd.I',
        'jadwal' => 'Jumat, 14.00–16.00 WIB',
        'peserta' => '48 L / 46 P (94 Siswa)',
        'target' => 'SKU Siaga/Penggalang, Lomba Tingkat (LT-II), dan Perkemahan Akbar'
    ],
    [
        'nama' => 'Tahfidz & Tilawatil Qur\'an',
        'kategori' => 'Pilihan (Kls I–VI)',
        'pembina' => 'Nur Hidayati, S.Pd.I & Faridah, S.Pd.I',
        'jadwal' => 'Sabtu, 08.00–09.30 WIB',
        'peserta' => '26 L / 38 P (64 Siswa)',
        'target' => 'Hafalan Juz 30 & Juz 1, Kaidah Tajwid, Murottal, Persiapan MTQ'
    ],
    [
        'nama' => 'Seni Hadroh & Sholawat Al-Banjari',
        'kategori' => 'Pilihan (Kls III–VI)',
        'pembina' => 'Ahmad Rifai, S.Pd & M. Agil Al-Ghozali',
        'jadwal' => 'Rabu, 14.30–16.00 WIB',
        'peserta' => '18 L / 14 P (32 Siswa)',
        'target' => 'Kuasai Variasi Ketukan Banjari & Bass, Pengisi Rutin PHBI/Wisuda'
    ],
    [
        'nama' => 'Pencak Silat Tapak Suci Putra Muhammadiyah',
        'kategori' => 'Pilihan (Kls III–VI)',
        'pembina' => 'Budi Santoso, S.Pd & Pelatih IPSI Kab.',
        'jadwal' => 'Sabtu, 14.00–15.30 WIB',
        'peserta' => '25 L / 15 P (40 Siswa)',
        'target' => 'Jurus Seni Tunggal, Tanding Pra-Remaja, Seleksi O2SN / AKSIOMA'
    ],
    [
        'nama' => 'Futsal & Olahraga Prestasi (Badminton)',
        'kategori' => 'Pilihan (Kls IV–VI)',
        'pembina' => 'Hendra Prasetya, S.Pd',
        'jadwal' => 'Kamis, 15.00–16.30 WIB',
        'peserta' => '34 L / 14 P (48 Siswa)',
        'target' => 'Teknik Passing/Dribbling, Kebugaran Fisik, Sparing Antar-Madrasah'
    ],
    [
        'nama' => 'Dokter Kecil & Kader UKS Sehat',
        'kategori' => 'Pilihan (Kls IV–VI)',
        'pembina' => 'Dewi Lestari, S.Pd & Tim Puskesmas',
        'jadwal' => 'Selasa, 14.00–15.30 WIB',
        'peserta' => '8 L / 16 P (24 Siswa)',
        'target' => 'Kader PHBS, P3K Cepat Madrasah, Pemeriksaan Sanitasi Berkala'
    ]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Fisik PKKM - Program Kerja & Rekap Ekstrakurikuler - <?php echo htmlspecialchars($sekolah_nama); ?></title>
    <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($brand['favicon']); ?>">
    <link rel="stylesheet" href="/public/assets/css/plus-jakarta-sans.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 7mm 11mm 5mm 11mm;
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

        /* Bilah Aksi Atas (Screen Only) */
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
            padding: 7mm 9mm 6mm;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        /* Watermark Background */
        .watermark {
            position: absolute;
            top: 52%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 72pt;
            font-weight: 900;
            color: rgba(30, 58, 138, 0.025);
            letter-spacing: 12px;
            pointer-events: none;
            z-index: 0;
            white-space: nowrap;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .sheet-content {
            position: relative;
            z-index: 1;
        }

        /* Kop Surat Resmi */
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
            font-size: 9.8pt;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .doc-title-sub {
            font-family: 'Times New Roman', Times, serif;
            font-size: 7.6pt;
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
            margin-bottom: 7px;
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
            font-size: 6.2pt;
            color: #64748b;
            line-height: 1.15;
            margin-top: 1px;
        }

        /* Tabel Matriks Ekstrakurikuler */
        .table-box {
            margin-bottom: 6px;
        }
        .table-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 7.6pt;
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
        .ekskul-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.3pt;
        }
        .ekskul-table th {
            background: #0f172a;
            color: #ffffff;
            padding: 4px 5px;
            text-align: left;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 6.7pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid #0f172a;
        }
        .ekskul-table td {
            padding: 3.5px 5px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
            line-height: 1.22;
        }
        .ekskul-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .ekskul-badge-wajib {
            display: inline-block;
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            font-size: 6.2pt;
            font-weight: 700;
            padding: 1px 4px;
            border-radius: 3px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .ekskul-badge-pilihan {
            display: inline-block;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            font-size: 6.2pt;
            font-weight: 700;
            padding: 1px 4px;
            border-radius: 3px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* 3 Pilar Pembinaan Minat Bakat */
        .pilar-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
            margin-bottom: 6px;
        }
        .pilar-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 5px 7px;
            display: flex;
            gap: 6px;
            align-items: flex-start;
        }
        .pilar-card.c1 { border-left: 3px solid #0284c7; }
        .pilar-card.c2 { border-left: 3px solid #d97706; }
        .pilar-card.c3 { border-left: 3px solid #0284c7; }
        .pilar-icon {
            font-size: 11pt;
            line-height: 1;
            flex-shrink: 0;
            margin-top: 1px;
        }
        .pilar-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 6.9pt;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 1.5px;
        }
        .pilar-desc {
            font-size: 6.3pt;
            color: #475569;
            line-height: 1.25;
        }

        /* Evaluasi Asesor Box */
        .eval-box {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 5px;
            padding: 4px 8px;
            margin-bottom: 6px;
            font-size: 6.8pt;
            color: #14532d;
            display: flex;
            align-items: center;
            justify-content: space-between;
            line-height: 1.25;
        }
        .eval-box strong {
            color: #0f172a;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 6.9pt;
        }

        /* Pengesahan Tanda Tangan */
        .auth-grid {
            display: grid;
            grid-template-columns: 1fr 120px 1fr;
            gap: 10px;
            align-items: end;
            margin-top: 2px;
            padding-top: 4px;
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
            font-size: 6.8pt;
            color: #64748b;
        }
        .auth-sub {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 24px;
        }
        .auth-name {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 7.8pt;
            color: #0f172a;
            text-decoration: underline;
        }
        .auth-nip {
            font-size: 6.8pt;
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
            font-size: 5.6pt;
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
            font-size: 5.6pt;
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
            font-size: 6.2pt;
            color: #64748b;
            margin-top: 5px;
            padding-top: 3px;
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
            <span>Bukti Fisik #11: Program Ekstrakurikuler & Pembinaan Bakat Siswa (Indikator 2.6.2)</span>
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
                📋 Link Bukti Kita (11)
            </button>
            <button onclick="window.print()" class="bar-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                🖨️ Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <div class="page-wrapper">
        <div class="sheet">
            <div class="watermark">EKSKUL 2026</div>

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
                        <div class="doc-title-main">PROGRAM KERJA & REKAPITULASI PEMBINAAN EKSTRAKURIKULER</div>
                        <div class="doc-title-sub">Penyaluran Bakat, Minat, dan Karakter Kepemimpinan & Religiusitas Siswa TP 2026/2027</div>
                    </div>
                    <div class="doc-badge-pill">
                        BUKTI FISIK PKKM &bull; INDIKATOR 2.6.2
                    </div>
                </div>

                <!-- 4 KPI STAT CARDS -->
                <div class="kpi-grid">
                    <div class="kpi-card">
                        <div class="kpi-num">6 Cabang</div>
                        <div class="kpi-label">Ekstrakurikuler</div>
                        <div class="kpi-sub">1 Wajib + 5 Pilihan Minat</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-num">100%</div>
                        <div class="kpi-label">Partisipasi Siswa</div>
                        <div class="kpi-sub">142 Siswa Aktif Terwadahi</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-num">Terjadwal</div>
                        <div class="kpi-label">Siklus Latihan Rutin</div>
                        <div class="kpi-sub">Selasa s.d. Sabtu Berkala</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-num">98.2%</div>
                        <div class="kpi-label">Presensi Pembina</div>
                        <div class="kpi-sub">8 Pembina / Pelatih Ahli</div>
                    </div>
                </div>

                <!-- TABEL MATRIKS PROGRAM & PESERTA EKSKUL -->
                <div class="table-box">
                    <div class="table-title">
                        <span>MATRIKS JADWAL, PEMBINA, DAN CAPAIAN EKSTRAKURIKULER MADRASAH</span>
                        <span>SK Kepala Madrasah No. 042/MI.MH/SK/VII/2026</span>
                    </div>
                    <table class="ekskul-table">
                        <thead>
                            <tr>
                                <th style="width: 4%; text-align: center;">No</th>
                                <th style="width: 23%;">Cabang Ekskul & Gugus</th>
                                <th style="width: 14%;">Kategori & Sasaran</th>
                                <th style="width: 20%;">Pembina & Pelatih</th>
                                <th style="width: 15%;">Hari & Waktu</th>
                                <th style="width: 11%; text-align: center;">Peserta</th>
                                <th style="width: 13%;">Target Capaian Utama</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ekskulList as $i => $e): ?>
                            <tr>
                                <td style="text-align: center; font-weight: 700;"><?php echo $i + 1; ?></td>
                                <td>
                                    <strong style="color: #0f172a;"><?php echo htmlspecialchars($e['nama']); ?></strong>
                                </td>
                                <td>
                                    <?php if (strpos($e['kategori'], 'Wajib') !== false): ?>
                                        <span class="ekskul-badge-wajib"><?php echo htmlspecialchars($e['kategori']); ?></span>
                                    <?php else: ?>
                                        <span class="ekskul-badge-pilihan"><?php echo htmlspecialchars($e['kategori']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($e['pembina']); ?></td>
                                <td><?php echo htmlspecialchars($e['jadwal']); ?></td>
                                <td style="text-align: center; font-size: 6.8pt; font-weight: 600;"><?php echo htmlspecialchars($e['peserta']); ?></td>
                                <td style="font-size: 6.8pt; color: #334155;"><?php echo htmlspecialchars($e['target']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- 3 PILAR PROGRAM PEMBINAAN MINAT & BAKAT -->
                <div class="pilar-grid">
                    <div class="pilar-card c1">
                        <div class="pilar-icon">📋</div>
                        <div>
                            <div class="pilar-title">Jurnal Presensi & Penilaian Berkala</div>
                            <div class="pilar-desc">Monitoring absensi mingguan guru pembina & siswa, catatan perkembangan kepribadian, serta rekap nilai ekstrakurikuler masuk RDM (Rapor Digital Madrasah).</div>
                        </div>
                    </div>
                    <div class="pilar-card c2">
                        <div class="pilar-icon">🎪</div>
                        <div>
                            <div class="pilar-title">Pentas Seni, Gelar Karya & Perkemahan</div>
                            <div class="pilar-desc">Wadah unjuk kreasi tahunan melalui pentas hadroh peringatan PHBI, persami gudep pramuka, bazaar karya siaga, dan wisuda akbar juz 'amma terbuka.</div>
                        </div>
                    </div>
                    <div class="pilar-card c3">
                        <div class="pilar-icon">🏆</div>
                        <div>
                            <div class="pilar-title">Fasilitasi Kompetisi & Reward Prestasi</div>
                            <div class="pilar-desc">Dukungan penuh akomodasi & pembinaan intensif delegasi madrasah pada ajang KSM, AKSIOMA, PORSENI, LT-II Pramuka, dan kejuaraan tingkat kabupaten.</div>
                        </div>
                    </div>
                </div>

                <!-- TELAAH & VERIFIKASI ASESOR -->
                <div class="eval-box">
                    <div>
                        <strong>HASIL TELAAH & VERIFIKASI ASESOR PKKM:</strong>
                        <span>Program pengembangan potensi diri dan minat bakat peserta didik terencana komprehensif, memiliki regulasi SK pembina, jadwal latihan mingguan yang tertib, serta pembinaan intensif yang berdampak nyata pada karakter dan prestasi santri.</span>
                    </div>
                    <div style="font-weight: 800; font-size: 7.2pt; color: #1e40af; white-space: nowrap; margin-left: 10px;">
                        NILAI INDIKATOR 2.6.2: SKOR 4 (SANGAT BAIK)
                    </div>
                </div>
            </div>

            <!-- BLOK PENGESAHAN & TANDA TANGAN RESMI -->
            <div>
                <div class="auth-grid">
                    <!-- KIRI: KOORDINATOR KESISWAAN -->
                    <div class="auth-col">
                        <div class="auth-role">Mengetahui,</div>
                        <div class="auth-sub">Koordinator Kesiswaan & Ekskul,</div>
                        <div class="auth-name">HENDRA PRASETYA, S.Pd</div>
                        <div class="auth-nip">Guru Pembina Kesiswaan</div>
                    </div>

                    <!-- TENGAH: QR CODE DIGITAL -->
                    <div class="qr-box">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=https://masmifhda.sch.id/admin/pkkm/cetak-bukti/ekstrakurikuler" alt="QR Validasi" class="qr-img">
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
                    <span>Dokumen Bukti Fisik PKKM &bull; Komponen 2: Pengembangan Madrasah (Indikator 2.6.2) &bull; MIS Miftahul Huda Mathla'ul Anwar</span>
                    <span style="font-weight:700; color:#0284c7;">https://masmifhda.sch.id</span>
                </div>
            </div>
        </div>
    </div>

    <?php include (file_exists(__DIR__ . '/_pkkm_koleksi_modal.php') ? __DIR__ . '/_pkkm_koleksi_modal.php' : __DIR__ . '/pkkm/_pkkm_koleksi_modal.php'); ?>
</body>
</html>
