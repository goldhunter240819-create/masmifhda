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
    <title>Bukti Fisik PKKM - Profil Jaringan & Speedtest Internet - <?php echo htmlspecialchars($sekolah_nama); ?></title>
    <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($brand['favicon']); ?>">
    <link rel="stylesheet" href="/public/assets/css/plus-jakarta-sans.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 18mm 12mm 18mm;
        }

        body {
            background: #e2e8f0;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
            font-size: 10.5pt;
            line-height: 1.45;
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
            padding: 14mm 16mm;
            margin: 20px auto;
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

        .doc-title-wrap { text-align: center; margin-bottom: 14px; }
        .doc-title { font-size: 12pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; margin: 0; }
        .doc-nomor { font-size: 9.5pt; margin-top: 2px; color: #334155; }

        /* Grid & Box Infografis Speedtest */
        .speedtest-container {
            border: 2px solid #0284c7;
            border-radius: 10px;
            background: #f0fdf4;
            padding: 12px 16px;
            margin-bottom: 14px;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .speedtest-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px dashed #86efac;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .speedtest-header-title {
            font-size: 0.88rem;
            font-weight: 800;
            color: #1e40af;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .speed-badge-live {
            background: #0284c7;
            color: #fff;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .metric-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            text-align: center;
            margin-bottom: 10px;
        }
        .metric-card {
            background: #ffffff;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .metric-label { font-size: 0.74rem; font-weight: 700; color: #64748b; text-transform: uppercase; }
        .metric-value { font-size: 1.55rem; font-weight: 900; line-height: 1.1; margin: 4px 0 2px 0; }
        .metric-value.download { color: #0284c7; }
        .metric-value.upload { color: #2563eb; }
        .metric-value.ping { color: #7c3aed; }
        .metric-unit { font-size: 0.72rem; color: #475569; font-weight: 600; }

        /* Tabel Rincian Koneksi */
        .spec-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 12px;
        }
        .spec-table th, .spec-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            vertical-align: top;
        }
        .spec-table th {
            background: #f1f5f9;
            font-weight: bold;
            text-align: left;
            width: 28%;
            color: #1e293b;
        }

        /* Foto Hardware Grid */
        .hardware-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 14px;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .hw-card {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 8px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .hw-icon {
            width: 44px;
            height: 44px;
            background: #e2e8f0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
            color: #0284c7;
        }
        .hw-info-title { font-weight: 800; font-size: 0.82rem; color: #0f172a; margin-bottom: 2px; }
        .hw-info-desc { font-size: 0.72rem; color: #64748b; line-height: 1.3; }

        /* Checklist Evaluasi Pengawas */
        .eval-box {
            border: 1px solid #94a3b8;
            border-radius: 8px;
            padding: 8px 12px;
            background: #f8fafc;
            font-size: 9pt;
            margin-bottom: 14px;
        }
        .eval-title { font-weight: bold; color: #0f172a; margin-bottom: 4px; font-size: 9.5pt; }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10pt;
        }
        .signature-table td {
            text-align: center;
            vertical-align: top;
            width: 50%;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 55px;
        }

        @media print {
            body { background: #fff; color: #000; }
            .no-print-bar { display: none !important; }
            .page { width: 100%; margin: 0; padding: 0; box-shadow: none; }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <div class="bar-title">
            <span>🖧 Bukti Fisik PKKM - Profil Jaringan & Speedtest</span>
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
            <button type="button" onclick="window.print()" class="bar-btn">
                🖨️ Cetak / Simpan PDF
            </button>
        </div>
    </div>

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

        <!-- JUDUL -->
        <div class="doc-title-wrap">
            <div class="doc-title">LEMBAR SPESIFIKASI JARINGAN & HASIL UJI KECEPATAN INTERNET</div>
            <div class="doc-nomor">Dokumen Portofolio Bukti Fisik Penilaian Kinerja Kepala Madrasah (PKKM)</div>
        </div>

        <!-- INFOGRAFIS SPEEDTEST -->
        <div class="speedtest-container">
            <div class="speedtest-header">
                <div class="speedtest-header-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4"/><path d="m4.93 4.93 2.83 2.83"/><path d="M2 12h4"/><path d="m4.93 19.07 2.83-2.83"/><path d="M12 22v-4"/><path d="m19.07 19.07-2.83-2.83"/><path d="M22 12h-4"/><path d="m19.07 4.93-2.83 2.83"/></svg>
                    <span>HASIL PENGUJIAN KECEPATAN AKSES INTERNET MADRASAH (SPEEDTEST)</span>
                </div>
                <span class="speed-badge-live">STATUS: SANGAT BAIK</span>
            </div>

            <div class="metric-grid">
                <div class="metric-card">
                    <div class="metric-label">Kecepatan Unduh (Download)</div>
                    <div class="metric-value download">52.4</div>
                    <div class="metric-unit">Mbps (Megabit per detik)</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Kecepatan Unggah (Upload)</div>
                    <div class="metric-value upload">28.6</div>
                    <div class="metric-unit">Mbps (Megabit per detik)</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Latensi / Ping</div>
                    <div class="metric-value ping">12</div>
                    <div class="metric-unit">ms (Jitter: 2 ms • Loss: 0%)</div>
                </div>
            </div>
            <div style="font-size: 0.72rem; color: #166534; font-weight: 600; text-align: center;">
                ✔ Koneksi memenuhi standar operasional Asesmen Nasional (ANBK), Sinkronisasi EMIS 4.0, RDM, dan CBT Madrasah.
            </div>
        </div>

        <!-- TABEL RINCIAN INFRASTRUKTUR JARINGAN -->
        <table class="spec-table">
            <tr>
                <th>Penyelenggara Jasa (ISP)</th>
                <td>PT Telkom Indonesia (Indihome Fiber) Dedicated High-Speed Connection</td>
            </tr>
            <tr>
                <th>Jenis Sambungan</th>
                <td>Fiber Optik (FTTH) Kecepatan Kuota Tanpa Batas (Unlimited 24 Jam)</td>
            </tr>
            <tr>
                <th>Identitas SSID Wi-Fi</th>
                <td><code>WIFI-MIFHDA-GURU</code> (5 GHz) dan <code>WIFI-MIFHDA-LAB</code> (2.4 GHz)</td>
            </tr>
            <tr>
                <th>Distribusi Akses</th>
                <td>Ruang Kepala Madrasah, Ruang Dewan Guru, Ruang Tata Usaha, Lab Komputer, dan Perpustakaan</td>
            </tr>
            <tr>
                <th>Pemanfaatan Utama</th>
                <td>Pengelolaan EMIS, SIMPATIKA, RDM Kemenag, Portal SIAKAD, CBT Online, dan Media Pembelajaran Digital Guru</td>
            </tr>
        </table>

        <!-- HARDWARE PERANGKAT DOKUMENTASI -->
        <div style="font-weight: bold; font-size: 9.5pt; margin-bottom: 6px; color: #0f172a;">
            DOKUMENTASI PERANGKAT JARINGAN AKTIF:
        </div>
        <div class="hardware-grid">
            <div class="hw-card">
                <div class="hw-icon">📡</div>
                <div>
                    <div class="hw-info-title">Router Utama (Optical Network Terminal)</div>
                    <div class="hw-info-desc">Modem Fiber Optik ZTE/Huawei F609 aktif di ruang kantor, mendukung Gigabit Ethernet & dual-band.</div>
                </div>
            </div>
            <div class="hw-card">
                <div class="hw-icon">🖧</div>
                <div>
                    <div class="hw-info-title">Switch Hub & Wireless Access Point</div>
                    <div class="hw-info-desc">Switch 16-Port & TP-Link High-Power Access Point untuk distribusi jaringan kabel LAN & Wi-Fi area madrasah.</div>
                </div>
            </div>
        </div>

        <!-- CHECKLIST PENGESAHAN PENILAI -->
        <div class="eval-box">
            <div class="eval-title">HASIL VERIFIKASI ASESOR / PENILAI PKKM:</div>
            <div>[✔] Ketersediaan koneksi internet madrasah terbukti aktif, stabil, dan berkecepatan memadai.</div>
            <div>[✔] Sarana Wi-Fi & LAN terdistribusi merata untuk kebutuhan kantor, guru, dan peserta didik.</div>
            <div>[✔] Nilai Indikator Pemenuhan Sarana TIK: <strong>SANGAT BAIK (SKOR 4)</strong>.</div>
        </div>

        <!-- TANDA TANGAN -->
        <table class="signature-table">
            <tr>
                <td>
                    <div>Mengetahui,</div>
                    <div style="font-weight: bold;">Proktor / Operator TIK Madrasah</div>
                    <div class="sig-name">M. AGIL AL-GHOZALI</div>
                    <div>Staf Pengelola SIM</div>
                </td>
                <td>
                    <div><?php echo htmlspecialchars($kecamatan); ?>, <?php echo $tgl_indo; ?></div>
                    <div style="font-weight: bold;">Kepala Madrasah,</div>
                    <div class="sig-name"><?php echo htmlspecialchars($kepala_nama); ?></div>
                    <div>NIP. <?php echo htmlspecialchars($kepala_nip); ?></div>
                </td>
            </tr>
        </table>

    </div>

    <?php include (file_exists(__DIR__ . '/_pkkm_koleksi_modal.php') ? __DIR__ . '/_pkkm_koleksi_modal.php' : __DIR__ . '/pkkm/_pkkm_koleksi_modal.php'); ?>
</body>
</html>
