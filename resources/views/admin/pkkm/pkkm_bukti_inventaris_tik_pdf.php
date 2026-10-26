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
$website = $webUrl;

$kepala_nama = $kamad['nama'] ?? 'SAMSUL ARIFIN, M.Pd';
$kepala_nip = $kamad['nip'] ?? '-';
$kecamatan = !empty($inst['kecamatan']) ? $inst['kecamatan'] : 'Way Tenong';
$kabupaten = !empty($inst['kota']) ? $inst['kota'] : 'Lampung Barat';


$bulanIndo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$tgl_indo = date('j') . ' ' . $bulanIndo[(int)date('n')] . ' ' . date('Y');

$isPengawas = isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 5;
$backUrl = $isPengawas ? '/pengawas/pkkm?tab=penilaian' : '/admin/pkkm/instrumen';
$backText = $isPengawas ? 'Kembali ke Penilaian' : 'Kembali ke Instrumen';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Fisik PKKM - Kartu Inventaris Sarana TIK - <?php echo htmlspecialchars($sekolah_nama); ?></title>
    <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($brand['favicon']); ?>">
    <link rel="stylesheet" href="/public/assets/css/plus-jakarta-sans.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 15mm 10mm 15mm;
        }

        body {
            background: #e2e8f0;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
            font-size: 9.5pt;
            line-height: 1.35;
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
        .bar-btn:hover { background: #f0f9ff; transform: translateY(-1px); }
        .bar-btn-secondary {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .bar-btn-secondary:hover { background: rgba(255, 255, 255, 0.25); color: #ffffff; }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 12mm 15mm;
            margin: 15px auto;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            box-sizing: border-box;
            position: relative;
        }

        /* Kop Surat Resmi Sesuai Pengaturan SIAKAD */
        .kop-surat-preview {
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 3px solid #000;
            padding-bottom: 7px;
            margin-bottom: 8px;
            position: relative;
        }
        .kop-surat-preview img {
            width: 68px;
            height: 68px;
            object-fit: contain;
            position: absolute;
            left: 5px;
        }
        .kop-text-preview {
            text-align: center;
            flex: 1;
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            padding: 0 70px;
        }
        .kop-text-preview h1 {
            margin: 0;
            font-size: <?php echo !empty($inst['ukuran_font_yayasan']) ? (int)$inst['ukuran_font_yayasan'] : 14; ?>px;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.25;
            color: #000;
        }
        .kop-text-preview h2 {
            margin: 2px 0 3px 0;
            font-size: <?php echo !empty($inst['ukuran_font_madrasah']) ? (int)$inst['ukuran_font_madrasah'] : 17; ?>px;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
            color: #000;
        }
        .kop-text-preview p {
            margin: 1px 0;
            font-size: 8.5pt;
            line-height: 1.3;
            color: #111;
        }

        .doc-title-wrap { text-align: center; margin-bottom: 12px; }
        .doc-title { font-size: 11.5pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; margin: 0; }
        .doc-nomor { font-size: 8.5pt; margin-top: 2px; color: #334155; }

        /* Metric Cards Infografis */
        .metric-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-bottom: 12px;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .metric-card {
            background: #f8fafc;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            padding: 8px 10px;
            text-align: center;
        }
        .metric-label { font-size: 0.68rem; font-weight: 700; color: #64748b; text-transform: uppercase; }
        .metric-value { font-size: 1.35rem; font-weight: 900; line-height: 1.1; margin: 3px 0 1px 0; }
        .metric-sub { font-size: 0.65rem; color: #0284c7; font-weight: 700; }

        /* Tabel Inventaris TIK */
        .inv-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 10px;
        }
        .inv-table th, .inv-table td {
            border: 1px solid #94a3b8;
            padding: 4px 6px;
            vertical-align: middle;
        }
        .inv-table th {
            background: #e2e8f0;
            font-weight: bold;
            text-align: center;
            color: #0f172a;
        }
        .badge-kondisi {
            display: inline-block;
            background: #e0f2fe;
            color: #0284c7;
            font-weight: 800;
            font-size: 7pt;
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid #7dd3fc;
            text-transform: uppercase;
        }

        /* Box Pemeliharaan & Verifikasi */
        .maintenance-box {
            background: #f0f9ff;
            border: 1px dashed #0284c7;
            border-radius: 8px;
            padding: 8px 12px;
            margin-bottom: 12px;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            font-size: 8pt;
        }

        /* Tanda Tangan */
        .ttd-wrap {
            margin-top: 10px;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            page-break-inside: avoid;
        }
        .ttd-box {
            width: 44%;
            text-align: center;
            font-size: 9pt;
            position: relative;
        }
        .ttd-space { height: 48px; }
        .ttd-name { font-weight: bold; text-decoration: underline; margin-bottom: 1px; }

        .stempel-badge {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) rotate(-8deg);
            width: 80px;
            height: 80px;
            border: 2px solid #0284c7;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #0284c7;
            opacity: 0.88;
            pointer-events: none;
            background: rgba(240, 253, 244, 0.4);
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            text-align: center;
            padding: 4px;
        }
        .stempel-text-top { font-size: 6px; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; }
        .stempel-star { font-size: 8px; line-height: 1; margin: 1px 0; }
        .stempel-text-bot { font-size: 5.5px; font-weight: 700; text-transform: uppercase; }

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

    <!-- Bar Navigasi & Cetak -->
    <div class="no-print-bar">
        <div class="bar-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2"/><line x1="8" x2="16" y1="6" y2="6"/><line x1="8" x2="16" y1="10" y2="10"/><line x1="8" x2="12" y1="14" y2="14"/></svg>
            <span>Bukti Fisik PKKM: Kartu Inventaris Sarana TIK & Lab</span>
            <span class="bar-badge">Indikator 1.4.3 & 2.5.1</span>
        </div>
        <div class="bar-actions">
            <a href="<?php echo $backUrl; ?>" class="bar-btn bar-btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                <?php echo $backText; ?>
            </a>
            <?php if (!$isPengawas): ?>
            <a href="/admin/pkkm/master-berkas" class="bar-btn bar-btn-secondary" title="Buka Daftar Berkas Master PKKM">
                📁 Daftar Berkas
            </a>
            <?php endif; ?>
            <button type="button" onclick="openKoleksiBuktiModal()" class="bar-btn bar-btn-secondary" title="Daftar Tautan Semua Bukti Fisik PKKM">
                📋 Link Bukti Kita
            </button>
            <button onclick="window.print()" class="bar-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Halaman Dokumen A4 -->
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

        <!-- JUDUL DOKUMEN -->
        <div class="doc-title-wrap">
            <h1 class="doc-title">KARTU INVENTARIS SARANA TEKNOLOGI INFORMASI & KOMUNIKASI (TIK)</h1>
            <div class="doc-nomor">Nomor Registrasi Aset: 421/INV-TIK/MIS-MH/2026 | Unit Kerja: Lab CBT & Ruang Guru</div>
        </div>

        <!-- STAT METRIC CARDS -->
        <div class="metric-grid">
            <div class="metric-card">
                <div class="metric-label">Total Unit TIK</div>
                <div class="metric-value" style="color: #0f172a;">32</div>
                <div class="metric-sub">Perangkat Aktif</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Kondisi Fisik</div>
                <div class="metric-value" style="color: #0284c7;">100%</div>
                <div class="metric-sub">Sangat Baik / Layak</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Kapasitas CBT/Lab</div>
                <div class="metric-value" style="color: #2563eb;">15 Client</div>
                <div class="metric-sub">+ 1 Dedicated Server</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Konektivitas LAN</div>
                <div class="metric-value" style="color: #7c3aed;">1 Gbps</div>
                <div class="metric-sub">Gigabit Fiber Ready</div>
            </div>
        </div>

        <!-- TABEL RINCIAN INVENTARIS SARANA TIK -->
        <table class="inv-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 14%;">Kode Barang</th>
                    <th>Nama & Spesifikasi Perangkat</th>
                    <th style="width: 15%;">Merk / Tipe</th>
                    <th style="width: 7%;">Jml</th>
                    <th style="width: 10%;">Kondisi</th>
                    <th style="width: 16%;">Lokasi / Ruangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td style="font-family: monospace; font-size: 7.5pt; text-align: center;">TIK-SRV-001</td>
                    <td><strong>Server Utama ANBK & Ujian Madrasah</strong><br><span style="font-size: 7.5pt; color: #475569;">Intel Core i7 Gen-11, 16GB DDR4, 512GB NVMe SSD, OS Ubuntu Server</span></td>
                    <td>Dell OptiPlex Pro</td>
                    <td style="text-align: center; font-weight: bold;">1 Unit</td>
                    <td style="text-align: center;"><span class="badge-kondisi">Sangat Baik</span></td>
                    <td>Ruang Server & Lab CBT</td>
                </tr>
                <tr>
                    <td style="text-align: center;">2</td>
                    <td style="font-family: monospace; font-size: 7.5pt; text-align: center;">TIK-PC-01-15</td>
                    <td><strong>PC Komputer Client Peserta Didik (Lab)</strong><br><span style="font-size: 7.5pt; color: #475569;">Intel Core i3, RAM 8GB DDR4, SSD 256GB, Layar 19.5" LED, Keyboard/Mouse</span></td>
                    <td>Lenovo / HP All-in-One</td>
                    <td style="text-align: center; font-weight: bold;">15 Unit</td>
                    <td style="text-align: center;"><span class="badge-kondisi">Sangat Baik</span></td>
                    <td>Laboratorium Komputer</td>
                </tr>
                <tr>
                    <td style="text-align: center;">3</td>
                    <td style="font-family: monospace; font-size: 7.5pt; text-align: center;">TIK-LPT-01-06</td>
                    <td><strong>Laptop Operasional Guru & Asesmen</strong><br><span style="font-size: 7.5pt; color: #475569;">Core i5 / Ryzen 5, 8GB RAM, SSD 512GB, Windows 11 Pro Edu</span></td>
                    <td>Lenovo ThinkBook 14</td>
                    <td style="text-align: center; font-weight: bold;">6 Unit</td>
                    <td style="text-align: center;"><span class="badge-kondisi">Sangat Baik</span></td>
                    <td>Ruang Guru & Tata Usaha</td>
                </tr>
                <tr>
                    <td style="text-align: center;">4</td>
                    <td style="font-family: monospace; font-size: 7.5pt; text-align: center;">TIK-PRJ-01-03</td>
                    <td><strong>Proyektor Digital Multimedia Presentasi</strong><br><span style="font-size: 7.5pt; color: #475569;">3.800 ANSI Lumens, 3LCD Tech, Resolusi XGA, Input HDMI / Wireless</span></td>
                    <td>Epson EB-X51 Series</td>
                    <td style="text-align: center; font-weight: bold;">3 Unit</td>
                    <td style="text-align: center;"><span class="badge-kondisi">Sangat Baik</span></td>
                    <td>Ruang Kelas V, VI & Aula</td>
                </tr>
                <tr>
                    <td style="text-align: center;">5</td>
                    <td style="font-family: monospace; font-size: 7.5pt; text-align: center;">TIK-PRT-01-02</td>
                    <td><strong>Printer Multifungsi (Print, Scan, Copy)</strong><br><span style="font-size: 7.5pt; color: #475569;">EcoTank High Yield, USB & WiFi Direct, Duplex Manual</span></td>
                    <td>Epson L3210 & Canon</td>
                    <td style="text-align: center; font-weight: bold;">2 Unit</td>
                    <td style="text-align: center;"><span class="badge-kondisi">Sangat Baik</span></td>
                    <td>Ruang Tata Usaha & Kamad</td>
                </tr>
                <tr>
                    <td style="text-align: center;">6</td>
                    <td style="font-family: monospace; font-size: 7.5pt; text-align: center;">TIK-NET-01-03</td>
                    <td><strong>Router Core & Switch Hub Gigabit</strong><br><span style="font-size: 7.5pt; color: #475569;">Gigabit 24-Port Switch 10/100/1000 + RouterOS Gateway MikroTik</span></td>
                    <td>Ruijie Reyee & MikroTik</td>
                    <td style="text-align: center; font-weight: bold;">3 Unit</td>
                    <td style="text-align: center;"><span class="badge-kondisi">Sangat Baik</span></td>
                    <td>Rack Server Utama</td>
                </tr>
                <tr>
                    <td style="text-align: center;">7</td>
                    <td style="font-family: monospace; font-size: 7.5pt; text-align: center;">TIK-PWR-01-02</td>
                    <td><strong>UPS Cadangan Daya (Power Backup Server)</strong><br><span style="font-size: 7.5pt; color: #475569;">1200VA / 600W, Stabilizer AVR Otomatis, Backup Time 25 Menit</span></td>
                    <td>ICA & Prolink Pro</td>
                    <td style="text-align: center; font-weight: bold;">2 Unit</td>
                    <td style="text-align: center;"><span class="badge-kondisi">Sangat Baik</span></td>
                    <td>Ruang Server & Proktor</td>
                </tr>
            </tbody>
        </table>

        <!-- LOG PEMELIHARAAN & STANDAR OPERASIONAL -->
        <div class="maintenance-box">
            <div style="font-weight: 800; color: #1e40af; font-size: 8.5pt; margin-bottom: 3px;">
                CATATAN PEMELIHARAAN & KALIBRASI PERANGKAT:
            </div>
            <div style="color: #1e3a8a; line-height: 1.35;">
                • Seluruh unit PC Client dan Server diinstal aplikasi <i>Safe Exam Browser (SEB)</i> & Browser ANBK resmi Pusmendik Kemendikbudristek.<br>
                • Pembersihan berkala debu hardware dan update patch keamanan OS dilaksanakan setiap triwulan oleh Penanggung Jawab TIK.<br>
                • Jaringan LAN & WiFi telah melalui uji stress-test simulasi asesmen 50 koneksi simultan dengan latensi stabil &lt; 15 ms.
            </div>
        </div>

        <!-- TANDA TANGAN & PENGESAHAN -->
        <div class="ttd-wrap">
            <div class="ttd-box">
                Mengetahui,<br>
                Penanggung Jawab Sarpras & TIK,
                <div class="ttd-space"></div>
                <div class="ttd-name">NUR HIDAYAH, S.Pd</div>
                <div style="font-size: 8pt; color: #64748b;">NIP. -</div>
            </div>

            <div class="ttd-box">
                Way Tenong, <?php echo $tgl_indo; ?><br>
                Kepala Madrasah,
                <div class="ttd-space"></div>
                <div class="ttd-name"><?php echo htmlspecialchars($kepala_nama); ?></div>
                <div style="font-size: 8pt; color: #64748b;">NIP. <?php echo htmlspecialchars($kepala_nip); ?></div>

                <!-- Stempel Resmi -->
                <div class="stempel-badge">
                    <span class="stempel-text-top">MIS MIFTAHUL HUDA</span>
                    <span class="stempel-star">★ KEMENAG ★</span>
                    <span class="stempel-text-bot">INVENTARIS VALID</span>
                </div>
            </div>
        </div>

    </div>

    <?php include (file_exists(__DIR__ . '/_pkkm_koleksi_modal.php') ? __DIR__ . '/_pkkm_koleksi_modal.php' : __DIR__ . '/pkkm/_pkkm_koleksi_modal.php'); ?>
</body>
</html>
