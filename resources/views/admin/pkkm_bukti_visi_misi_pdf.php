<?php
if (!class_exists('App\Core\Branding') && file_exists(__DIR__ . '/../../Core/Branding.php')) {
    require_once __DIR__ . '/../../Core/Branding.php';
}
use App\Core\Branding;

$brand = class_exists('App\Core\Branding') ? Branding::get() : ['favicon' => '/favicon.ico'];
$logoUrl = !empty($inst['logo']) ? '/public/uploads/logo/' . $inst['logo'] : (class_exists('App\Core\Branding') ? Branding::getInstitusiLogo() : '/public/assets/images/logo.png');

$sekolah_nama = !empty($inst['nama']) ? $inst['nama'] : 'MADRASAH ALIYAH MIFTAHUL HUDA';
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

$activeTab = $_GET['tab'] ?? 'dokumen';

// Official Visi & Misi from Document / Database
$visiText = !empty($inst['visi']) ? $inst['visi'] : 'Bertaqwa, Berakhlakul Karimah, Berprestasi Dan Berwawasan Lingkungan Hidup';
$misiText = !empty($inst['misi']) ? $inst['misi'] : 'Membangun Citra Madrasah yang Inspiratif, Berkarakter, Berprestasi, dan Ramah Lingkungan';

// Data 6 Misi Strategis Madrasah (Official)
$misiList = [
    [
        'no' => 1,
        'title' => 'Pembentukan Karakter & Ketakwaan',
        'desc' => 'Membentuk karakter siswa yang taat beribadah dan bertaqwa kepada Allah SWT.',
        'target' => '100% Santri',
        'realisasi' => 'Sholat Dhuha harian, Dzuhur berjamaah, hafalan doa harian & Tahfidz Juz 30.'
    ],
    [
        'no' => 2,
        'title' => 'Pendidikan Akhlakul Karimah',
        'desc' => 'Mewujudkan proses pendidikan yang menghasilkan lulusan berakhlaqul karimah.',
        'target' => 'Pembiasaan Harian',
        'realisasi' => 'Budaya 5S (Senyum, Salam, Sapa, Sopan, Santun) dan kajian kitab akhlak adab santri.'
    ],
    [
        'no' => 3,
        'title' => 'Prestasi Akademik & Non-Akademik',
        'desc' => 'Mewujudkan proses pendidikan yang menghasilkan lulusan berprestasi di bidang akademik dan non akademik.',
        'target' => 'Target Juara KSM/Aksara',
        'realisasi' => '14 Raihan Piagam Juara (KSM Sains, MTQ, Hadroh, Silat, Pramuka tingkat KKM & Kab).'
    ],
    [
        'no' => 4,
        'title' => 'Pembelajaran Inovatif & E-Learning',
        'desc' => 'Mewujudkan proses pembelajaran yang inovatif dan inspiratif dengan berbagai macam pendekatan.',
        'target' => 'Smart Madrasah Digital',
        'realisasi' => 'Platform CBT ANBK terpadu, modul ajar digital, dan portal SIAKAD online.'
    ],
    [
        'no' => 5,
        'title' => 'Penilaian Akuntabel & Transparan',
        'desc' => 'Terwujudnya mekanisme penilaian dalam bentuk Akuntabel, Transparan, dan dapat diakses oleh masyarakat luas.',
        'target' => 'Raport Digital (RDM)',
        'realisasi' => 'Sinkronisasi RDM Kemenag, rekap nilai transparan, dan akses pantau nilai oleh wali santri.'
    ],
    [
        'no' => 6,
        'title' => 'Lingkungan Asri & Ramah Lingkungan',
        'desc' => 'Terwujudnya lingkungan madrasah yang bersih dan nyaman serta menyenangkan.',
        'target' => 'Eco-Madrasah Hijau',
        'realisasi' => 'Taman madrasah asri, program piket harian, sanitasi bersih, dan kawasan bebas asap rokok.'
    ]
];

// 4 Sasaran Mutu & Tujuan Keunggulan
$sasaranMutu = [
    [
        'pilar' => 'Pilar I : Iman, Taqwa & Akhlakul Karimah',
        'sub' => 'Menyiapkan peserta didik menjadi insan beriman, bertaqwa, berakhlak mulia, dan berbudi pekerti luhur.',
        'color' => '#0284c7',
        'bg' => '#f0f9ff',
        'border' => '#bae6fd'
    ],
    [
        'pilar' => 'Pilar II : Daya Saing Lulusan & Prestasi',
        'sub' => 'Menghasilkan lulusan berdaya saing imtaq & iptek yang siap diterima di MTs/SMP favorit tujuan.',
        'color' => '#2563eb',
        'bg' => '#eff6ff',
        'border' => '#bfdbfe'
    ],
    [
        'pilar' => 'Pilar III : Mutu PBM & Teknologi Informatika',
        'sub' => 'Terlaksananya PBM tepat waktu, pembelajaran IT modern, penilaian sikap/pengetahuan, dan informasi web.',
        'color' => '#d97706',
        'bg' => '#fffbeb',
        'border' => '#fde68a'
    ],
    [
        'pilar' => 'Pilar IV : Budaya Islami & Ramah Lingkungan',
        'sub' => 'Terwujudnya kehidupan sekolah yang bersih, indah, berbudaya islami, aman, nyaman, dan menyenangkan.',
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
    <title>Monitoring & Bukti Visi Misi - <?php echo htmlspecialchars($sekolah_nama); ?></title>
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
            background: #f1f5f9;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            font-size: 8.5pt;
            line-height: 1.4;
            color: #0f172a;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Top Action Bar (Screen Only) */
        .no-print-bar {
            background: linear-gradient(135deg, #0369a1 0%, #0284c7 50%, #0369a1 100%);
            color: #ffffff;
            padding: 14px 24px;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 4px 20px rgba(0,0,0,0.18);
        }
        .bar-top-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        .bar-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 1rem;
        }
        .bar-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 0.72rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-weight: 800;
        }
        .bar-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        .bar-btn {
            background: #ffffff;
            color: #0369a1;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.82rem;
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
        .bar-btn-danger {
            background: rgba(239, 68, 68, 0.2);
            color: #fecaca;
            border: 1px solid rgba(239, 68, 68, 0.4);
        }
        .bar-btn-danger:hover { background: #ef4444; color: #fff; }

        /* Sub-Navbar Tabs */
        .tab-nav-row {
            display: flex;
            gap: 8px;
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px solid rgba(255,255,255,0.18);
            overflow-x: auto;
        }
        .tab-btn {
            padding: 7px 16px;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 700;
            text-decoration: none;
            color: rgba(255,255,255,0.8);
            background: transparent;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }
        .tab-btn:hover {
            color: #fff;
            background: rgba(255,255,255,0.12);
        }
        .tab-btn.active {
            color: #0369a1;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
        }

        /* Notifications */
        .alert-bar {
            max-width: 1050px;
            margin: 15px auto 0 auto;
            padding: 12px 18px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .alert-success { background: #f0f9ff; border: 1px solid #bae6fd; color: #0369a1; }
        .alert-info { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e3a8a; }

        /* Content Wrappers */
        .content-wrap {
            max-width: 1050px;
            margin: 20px auto 40px auto;
            padding: 0 15px;
        }

        /* ---------------- TAB 1: DOKUMEN CETAK A4 ---------------- */
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 7mm 10mm;
            margin: 0 auto;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
        }
        .outer-frame {
            border: 2px solid #0369a1;
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
        .kop-surat-preview {
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 2.5px solid #0369a1;
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
            color: #0369a1;
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
        .doc-header-banner {
            text-align: center;
            margin-bottom: 8px;
        }
        .sk-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f0f9ff;
            color: #0369a1;
            border: 1px solid #7dd3fc;
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
            color: #0369a1;
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
        .visi-plaque {
            background: linear-gradient(135deg, #0369a1 0%, #0284c7 50%, #0369a1 100%);
            border: 1.5px solid #d97706;
            border-radius: 9px;
            padding: 9px 14px;
            color: #ffffff;
            text-align: center;
            margin-bottom: 8px;
            position: relative;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.15);
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
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 6.8pt;
            font-weight: 700;
            color: #ffffff;
        }
        .section-box {
            margin-bottom: 8px;
        }
        .section-box-title {
            font-size: 8.5pt;
            font-weight: 800;
            color: #0369a1;
            text-transform: uppercase;
            border-bottom: 1.5px solid #0284c7;
            padding-bottom: 2px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .misi-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5px 8px;
        }
        .misi-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 4px 7px;
            display: flex;
            align-items: flex-start;
            gap: 6px;
        }
        .misi-num {
            background: #0369a1;
            color: #ffffff;
            width: 16px;
            height: 16px;
            border-radius: 4px;
            font-size: 6.8pt;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }
        .misi-title {
            font-size: 7.4pt;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 1px;
        }
        .misi-desc {
            font-size: 6.7pt;
            color: #334155;
            line-height: 1.25;
        }
        .mutu-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
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
        .meta-notice-box {
            background: #f0f9ff;
            border: 1px dashed #7dd3fc;
            border-radius: 6px;
            padding: 4px 8px;
            font-size: 6.8pt;
            color: #0c4a6e;
            line-height: 1.3;
            margin-bottom: 7px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
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
        .auth-space { height: 38px; }
        .auth-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 8.5pt;
            color: #0f172a;
        }
        .auth-nip { font-size: 7.5pt; color: #475569; }
        .qr-center-box {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
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
            color: #0284c7;
            margin-top: 2px;
            text-transform: uppercase;
        }
        .qr-serial { font-size: 5.2pt; color: #64748b; font-family: monospace; }
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
            text-align: center;
            transform: rotate(-12deg);
            background: rgba(240, 253, 244, 0.75);
            pointer-events: none;
            z-index: 2;
        }
        .stempel-top { font-size: 5.2pt; font-weight: 900; }
        .stempel-mid { font-size: 5.8pt; font-weight: 900; color: #0284c7; margin: 1px 0; }
        .stempel-bot { font-size: 5.2pt; font-weight: 900; }

        /* ---------------- TAB 2: MATRIKS KETERCAPAIAN ---------------- */
        .dash-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }
        .dash-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 4px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .dash-sub {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 18px;
        }
        .matrix-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }
        .matrix-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            padding: 10px 14px;
            text-align: left;
            border-bottom: 2px solid #e2e8f0;
        }
        .matrix-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }
        .badge-pill-success {
            background: #f0f9ff;
            color: #0284c7;
            border: 1px solid #bae6fd;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* ---------------- TAB 3: TELAAH & CATATAN PENGAWAS ---------------- */
        .eval-box {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #cbd5e1;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
        }
        .eval-criteria-item {
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            margin-bottom: 10px;
            background: #f8fafc;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .eval-criteria-item input[type="checkbox"] {
            margin-top: 3px;
            width: 18px;
            height: 18px;
            accent-color: #0284c7;
            cursor: pointer;
        }
        .eval-criteria-label {
            font-size: 0.88rem;
            font-weight: 700;
            color: #0f172a;
            cursor: pointer;
        }
        .eval-criteria-sub {
            font-size: 0.78rem;
            color: #64748b;
            margin-top: 2px;
        }

        @media print {
            body { background: #ffffff !important; }
            .no-print-bar, .tab-nav-row, .alert-bar { display: none !important; }
            .tab-pane-monitoring, .tab-pane-pengawas { display: none !important; }
            .tab-pane-dokumen { display: block !important; }
            .content-wrap { padding: 0 !important; margin: 0 !important; max-width: 100% !important; }
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
        <div class="bar-top-row">
            <div class="bar-title">
                <span class="bar-badge">PORTAL MONITORING PKKM</span>
                <span>Bukti & Monitoring Visi, Misi & Sasaran Mutu (Indikator 1.2.1 & 2.1.2)</span>
            </div>
            <div class="bar-actions">
                <!-- Status Keterkaitan PKKM -->
                <?php if($is_pkkm_linked): ?>
                    <span class="bar-badge" style="background:#0ea5e9; color:#fff; display:inline-flex; align-items:center; gap:5px;">
                        ✓ Tertaut di PKKM (<?php echo $linked_count; ?> Berkas)
                    </span>
                    <form action="/admin/pkkm/cetak-bukti/visi-misi/detach" method="POST" style="margin:0;" onsubmit="return confirm('Lepas tautan berkas ini dari instrumen PKKM?')">
                        <button type="submit" class="bar-btn bar-btn-danger" title="Lepas dari instrumen PKKM">
                            Lepas Tautan
                        </button>
                    </form>
                <?php else: ?>
                    <span class="bar-badge" style="background:#f59e0b; color:#fff;">
                        Belum Tertaut PKKM
                    </span>
                    <a href="/admin/pkkm/cetak-bukti/visi-misi/attach" class="bar-btn" style="background:#0ea5e9; color:#fff;" title="Tautkan otomatis ke Instrumen PKKM 1.2.1 & Master Berkas">
                        + Tautkan ke PKKM 1.2.1
                    </a>
                <?php endif; ?>

                <a href="<?php echo $backUrl; ?>" class="bar-btn bar-btn-secondary">
                    ← <?php echo $backText; ?>
                </a>
                <a href="/admin/pkkm/master-berkas" class="bar-btn bar-btn-secondary" title="Buka Daftar Berkas Master PKKM">
                    Daftar Berkas
                </a>
                <button onclick="window.print()" class="bar-btn">
                    🖨️ Cetak / PDF A4
                </button>
            </div>
        </div>

        <!-- Tab Navigasi 3 Mode -->
        <div class="tab-nav-row">
            <a href="?tab=dokumen" class="tab-btn <?php echo $activeTab === 'dokumen' ? 'active' : ''; ?>">
                📜 Dokumen SK & Plang (A4 Cetak)
            </a>
            <a href="?tab=matriks" class="tab-btn <?php echo $activeTab === 'matriks' ? 'active' : ''; ?>">
                📊 Matriks Ketercapaian & Publikasi Digital
            </a>
            <a href="?tab=pengawas" class="tab-btn <?php echo $activeTab === 'pengawas' ? 'active' : ''; ?>">
                🔍 Lembar Telaah & Catatan Pengawas
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi Feedback -->
    <?php if(isset($_GET['msg']) && $_GET['msg'] === 'attached'): ?>
        <div class="alert-bar alert-success">
            <div><strong>Sukses!</strong> Bukti fisik Visi Misi berhasil ditautkan otomatis ke Instrumen PKKM 1.2.1 dan Master Berkas.</div>
            <a href="?tab=<?php echo $activeTab; ?>" style="color:#0369a1; text-decoration:none; font-weight:800;">✕</a>
        </div>
    <?php elseif(isset($_GET['msg']) && $_GET['msg'] === 'detached'): ?>
        <div class="alert-bar alert-info">
            <div>Tautan bukti visi misi telah dilepas dari berkas instrumen PKKM.</div>
            <a href="?tab=<?php echo $activeTab; ?>" style="color:#1e3a8a; text-decoration:none; font-weight:800;">✕</a>
        </div>
    <?php elseif(isset($_GET['msg']) && $_GET['msg'] === 'saved'): ?>
        <div class="alert-bar alert-success">
            <div><strong>Tersimpan!</strong> Catatan telaah dan hasil verifikasi pengawas berhasil diperbarui.</div>
            <a href="?tab=<?php echo $activeTab; ?>" style="color:#0369a1; text-decoration:none; font-weight:800;">✕</a>
        </div>
    <?php endif; ?>

    <div class="content-wrap">

        <!-- ==================== TAB 1: DOKUMEN CETAK A4 ==================== -->
        <div class="tab-pane-dokumen" style="display: <?php echo $activeTab === 'dokumen' ? 'block' : 'none'; ?>;">
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
                                Surat Keputusan Kepala Madrasah Nomor: B-012/MI.083/PP.00.1/07/2026
                            </div>
                            <h3 class="main-display-title">VISI, MISI, TUJUAN & SASARAN MUTU MADRASAH</h3>
                            <div class="main-display-sub">KOMITMEN PENYELENGGARAAN PENDIDIKAN TAHUN PELAJARAN 2026/2027</div>
                        </div>

                        <!-- PLANG VISI UTAMA MADRASAH -->
                        <div class="visi-plaque">
                            <div class="visi-top-tag">
                                ★ Visi Utama Madrasah Aliyah ★
                            </div>
                            <div class="visi-statement">
                                “<?php echo htmlspecialchars($visiText); ?>”
                            </div>
                            <div class="visi-chips-row">
                                <span class="visi-chip">1. Bertaqwa</span>
                                <span class="visi-chip">2. Berakhlakul Karimah</span>
                                <span class="visi-chip">3. Berprestasi</span>
                                <span class="visi-chip">4. Berwawasan Lingkungan</span>
                            </div>
                        </div>

                        <!-- 6 PILAR MISI STRATEGIS MADRASAH -->
                        <div class="section-box">
                            <div class="section-box-title">
                                <span>Misi Strategis Madrasah (6 Pilar Implementasi)</span>
                            </div>
                            <div class="misi-grid">
                                <?php foreach($misiList as $m): ?>
                                    <div class="misi-card">
                                        <div class="misi-num"><?php echo $m['no']; ?></div>
                                        <div>
                                            <div class="misi-title"><?php echo htmlspecialchars($m['title']); ?></div>
                                            <div class="misi-desc"><?php echo htmlspecialchars($m['desc']); ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- 4 SASARAN MUTU & TARGET LULUSAN -->
                        <div class="section-box">
                            <div class="section-box-title">
                                <span>4 Sasaran Mutu & Horizon Keunggulan Lulusan (SKL)</span>
                            </div>
                            <div class="mutu-grid">
                                <?php foreach($sasaranMutu as $s): ?>
                                    <div class="mutu-card" style="background: <?php echo $s['bg']; ?>; border-color: <?php echo $s['border']; ?>;">
                                        <div class="mutu-tag" style="color: <?php echo $s['color']; ?>;">
                                            <span><?php echo htmlspecialchars($s['pilar']); ?></span>
                                        </div>
                                        <div class="mutu-desc"><?php echo htmlspecialchars($s['sub']); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- NOTULEN & SOSIALISASI NOTE BOX -->
                        <div class="meta-notice-box">
                            <div>
                                <strong>Keterbukaan & Konsensus Kelembagaan:</strong> Visi, Misi, dan Sasaran Mutu ini telah disosialisasikan, dipajang pada plang display dinding serambi lobi madrasah, dipublikasikan di portal resmi masmifhda.sch.id, dan disepakati bersama dalam Rapat Pleno Dewan Pendidik bersama Komite Madrasah pada 11 Juli 2026.
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
                                    <span class="stempel-top">MAS MIFTAHUL HUDA</span>
                                    <span class="stempel-mid">★ KEMENAG ★</span>
                                    <span class="stempel-bot">LEMBAGA VALID</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== TAB 2: MATRIKS KETERCAPAIAN & PUBLIKASI ==================== -->
        <div class="tab-pane-monitoring" style="display: <?php echo $activeTab === 'matriks' ? 'block' : 'none'; ?>;">
            
            <!-- Saluran Publikasi Digital -->
            <div class="dash-card">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                    <div>
                        <h2 class="dash-title">🌐 Saluran Publikasi & Sosialisasi Visi Misi</h2>
                        <div class="dash-sub">Bukti konkret bahwa visi-misi madrasah telah tersosialisasi secara terbuka dan mudah diakses oleh seluruh pemangku kepentingan.</div>
                    </div>
                    <a href="/profil.php#visimisi" target="_blank" class="bar-btn" style="background:#0284c7; color:#fff;">
                        ↗ Buka Halaman Publik Visi Misi
                    </a>
                </div>

                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:14px; margin-top:10px;">
                    <div style="padding:14px; border-radius:12px; background:#f0f9ff; border:1px solid #bae6fd;">
                        <div style="font-weight:800; color:#0369a1; font-size:0.9rem;">1. Website Resmi Madrasah</div>
                        <div style="font-size:0.8rem; color:#0284c7; margin-top:4px;">Tayang di <code>masmifhda.sch.id/profil.php#visimisi</code> dapat diakses publik 24/7.</div>
                        <span class="badge-pill-success" style="margin-top:8px;">Terverifikasi Online</span>
                    </div>

                    <div style="padding:14px; border-radius:12px; background:#eff6ff; border:1px solid #bfdbfe;">
                        <div style="font-weight:800; color:#1e3a8a; font-size:0.9rem;">2. Display Plang Dinding Serambi</div>
                        <div style="font-size:0.8rem; color:#1d4ed8; margin-top:4px;">Dipasang pigura plang eksekutif di lobi utama depan ruang guru dan kepala madrasah.</div>
                        <span class="badge-pill-success" style="margin-top:8px;">Terpasang Fisik</span>
                    </div>

                    <div style="padding:14px; border-radius:12px; background:#fef3c7; border:1px solid #fde68a;">
                        <div style="font-weight:800; color:#92400e; font-size:0.9rem;">3. Berita Acara Rapat Pleno</div>
                        <div style="font-size:0.8rem; color:#b45309; margin-top:4px;">Ditandatangani Kamad, Komite, dan Dewan Guru pada 11 Juli 2026.</div>
                        <span class="badge-pill-success" style="margin-top:8px;">Ada Notulen & Daftar Hadir</span>
                    </div>

                    <div style="padding:14px; border-radius:12px; background:#faf5ff; border:1px solid #e9d5ff;">
                        <div style="font-weight:800; color:#6b21a8; font-size:0.9rem;">4. Brosur & Twibbon SPMB</div>
                        <div style="font-size:0.8rem; color:#7c3aed; margin-top:4px;">Tercantum pada materi sosialisasi penerimaan santri baru tahun pelajaran 2026/2027.</div>
                        <span class="badge-pill-success" style="margin-top:8px;">Terdokumentasi</span>
                    </div>
                </div>
            </div>

            <!-- Matriks Ketercapaian Misi -->
            <div class="dash-card">
                <h2 class="dash-title">📊 Matriks Evaluasi & Ketercapaian Misi (6 Pilar)</h2>
                <div class="dash-sub">Pemantauan implementasi program kerja madrasah terhadap target misi yang telah ditetapkan.</div>

                <div style="overflow-x:auto;">
                    <table class="matrix-table">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="30%">Pilar Misi Strategis</th>
                                <th width="20%">Target Kinerja</th>
                                <th width="35%">Realisasi Bukti Konkret</th>
                                <th width="10%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($misiList as $m): ?>
                                <tr>
                                    <td style="font-weight:800; color:#0369a1;"><?php echo $m['no']; ?></td>
                                    <td>
                                        <div style="font-weight:700; color:#0f172a;"><?php echo htmlspecialchars($m['title']); ?></div>
                                        <div style="font-size:0.78rem; color:#64748b;"><?php echo htmlspecialchars($m['desc']); ?></div>
                                    </td>
                                    <td><span style="font-weight:700; color:#2563eb;"><?php echo htmlspecialchars($m['target']); ?></span></td>
                                    <td style="color:#334155;"><?php echo htmlspecialchars($m['realisasi']); ?></td>
                                    <td><span class="badge-pill-success">Tercapai (A)</span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- ==================== TAB 3: LEMBAR TELAAH & CATATAN PENGAWAS ==================== -->
        <div class="tab-pane-pengawas" style="display: <?php echo $activeTab === 'pengawas' ? 'block' : 'none'; ?>;">
            <div class="dash-card">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:14px; margin-bottom:16px;">
                    <div>
                        <h2 class="dash-title">🔍 Lembar Telaah & Verifikasi Asesor Pengawas PKKM</h2>
                        <div class="dash-sub">Instrumen penilaian dan catatan pembinaan pengawas madrasah untuk Indikator 1.2.1 dan Komponen 1 (Usaha Pengembangan Madrasah).</div>
                    </div>
                    <?php if(!empty($savedNotes['updated_at'])): ?>
                        <div style="font-size:0.78rem; color:#64748b; background:#f1f5f9; padding:6px 12px; border-radius:8px;">
                            Terakhir diverifikasi: <strong><?php echo date('d M Y H:i', strtotime($savedNotes['updated_at'])); ?></strong>
                        </div>
                    <?php endif; ?>
                </div>

                <form action="/admin/pkkm/cetak-bukti/visi-misi/simpan-catatan" method="POST">
                    
                    <!-- Checklist Telaah Asesor -->
                    <div style="margin-bottom:20px;">
                        <label style="font-weight:800; font-size:0.9rem; color:#0f172a; margin-bottom:10px; display:block;">
                            1. Checklist Pemenuhan Bukti Fisik Visi, Misi & Sasaran Mutu:
                        </label>

                        <?php
                        $chkItems = [
                            'sk_kamad' => [
                                'title' => 'SK Penetapan Kepala Madrasah tentang Visi, Misi & Sasaran Mutu',
                                'sub' => 'Dokumen resmi tertanggal 11 Juli 2026 nomor B-012/MI.083/PP.00.1/07/2026 lengkap tanda tangan dan stempel.'
                            ],
                            'berita_acara' => [
                                'title' => 'Berita Acara & Daftar Hadir Rapat Perumusan / Peninjauan Visi Misi',
                                'sub' => 'Dihadiri dewan pendidik, tenaga kependidikan, pengurus yayasan, dan ketua komite madrasah.'
                            ],
                            'keselarasan_kurikulum' => [
                                'title' => 'Keselarasan Visi Misi dengan Dokumen Kurikulum Operasional Madrasah (KOM)',
                                'sub' => 'Tercermin dalam rancangan pembelajaran, kokurikuler P5-PPRA, dan pembiasaan ibadah harian.'
                            ],
                            'publikasi_terbuka' => [
                                'title' => 'Publikasi dan Sosialisasi Terbuka Kepada Warga Madrasah',
                                'sub' => 'Tersedia plang pajang visual di lobi, website publik masmifhda.sch.id, dan media sosial resmi.'
                            ],
                            'evaluasi_target' => [
                                'title' => 'Matriks Sasaran Mutu dan Evaluasi Ketercapaian Target Lulusan',
                                'sub' => 'Target keagamaan (Tahfidz Juz 30), akademik (KSM), dan budaya ramah lingkungan terukur jelas.'
                            ]
                        ];
                        $checkedMap = $savedNotes['checklist'] ?? ['sk_kamad' => '1', 'berita_acara' => '1', 'keselarasan_kurikulum' => '1', 'publikasi_terbuka' => '1', 'evaluasi_target' => '1'];
                        ?>

                        <?php foreach($chkItems as $k => $c): ?>
                            <div class="eval-criteria-item">
                                <input type="checkbox" name="chk[<?php echo $k; ?>]" id="chk_<?php echo $k; ?>" value="1" <?php echo !empty($checkedMap[$k]) ? 'checked' : ''; ?>>
                                <label for="chk_<?php echo $k; ?>">
                                    <div class="eval-criteria-label"><?php echo $c['title']; ?></div>
                                    <div class="eval-criteria-sub"><?php echo $c['sub']; ?></div>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Input Catatan Pembinaan Pengawas -->
                    <div style="margin-bottom:20px;">
                        <label for="catatan_pengawas" style="font-weight:800; font-size:0.9rem; color:#0f172a; margin-bottom:6px; display:block;">
                            2. Catatan Rekomendasi & Hasil Pembinaan Pengawas:
                        </label>
                        <textarea name="catatan" id="catatan_pengawas" rows="4" style="width:100%; border:1px solid #cbd5e1; border-radius:10px; padding:12px; font-family:inherit; font-size:0.88rem; line-height:1.5;" placeholder="Tuliskan catatan apresiasi, temuan, atau arahan peningkatan mutu pelaksanaan visi misi..."><?php echo htmlspecialchars($savedNotes['catatan'] ?? "Rumusan visi, misi, dan sasaran mutu madrasah sangat relevan, selaras dengan ruh Kurikulum Merdeka dan nilai pesantren. Sosialisasi melalui website digital dan display plang lobi terlaksana dengan sangat baik. Direkomendasikan untuk terus mempertahankan pembiasaan sholat dhuha dan target tahfidz juz 30."); ?></textarea>
                    </div>

                    <!-- Penilaian & Rekomendasi Skor -->
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
                        <div>
                            <div style="font-weight:800; color:#0f172a; font-size:0.9rem;">Rekomendasi Skor Asesor (Indikator 1.2.1):</div>
                            <div style="font-size:0.78rem; color:#64748b;">Standar penilaian kinerja kepala madrasah skala 1 s.d. 4.</div>
                        </div>

                        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                            <select name="skor_rekomendasi" style="padding:8px 14px; border-radius:8px; border:1px solid #0284c7; font-weight:800; color:#0369a1; font-size:0.9rem;">
                                <?php $curSkor = $savedNotes['skor_rekomendasi'] ?? '4'; ?>
                                <option value="4" <?php echo $curSkor == '4' ? 'selected':''; ?>>Skor 4 - Amat Baik / Sangat Lengkap</option>
                                <option value="3" <?php echo $curSkor == '3' ? 'selected':''; ?>>Skor 3 - Baik / Lengkap</option>
                                <option value="2" <?php echo $curSkor == '2' ? 'selected':''; ?>>Skor 2 - Cukup</option>
                                <option value="1" <?php echo $curSkor == '1' ? 'selected':''; ?>>Skor 1 - Kurang</option>
                            </select>

                            <label style="display:inline-flex; align-items:center; gap:6px; font-size:0.82rem; font-weight:700; color:#334155; cursor:pointer;">
                                <input type="checkbox" name="simpan_skor_pkkm" value="1" checked style="accent-color:#0284c7;">
                                Simpan juga skor ke Tabel Nilai PKKM
                            </label>
                        </div>
                    </div>

                    <!-- Tim Penilai & Submit Button -->
                    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <span style="font-size:0.82rem; color:#64748b;">Asesor Penilai:</span>
                            <select name="nama_penilai" style="padding:6px 12px; border-radius:8px; border:1px solid #cbd5e1; font-size:0.82rem; font-weight:700;">
                                <?php if(!empty($pengawas_list)): ?>
                                    <?php foreach($pengawas_list as $pg): ?>
                                        <option value="<?php echo htmlspecialchars($pg['nama']); ?> (<?php echo $pg['peran'] === 'penilai_1' ? 'Penilai 1' : 'Penilai 2'; ?>)">
                                            <?php echo htmlspecialchars($pg['nama']); ?> - <?php echo $pg['peran'] === 'penilai_1' ? 'Penilai 1 (Ketua Tim)' : 'Penilai 2 (Anggota)'; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="Pengawas Madrasah Kemenag">Drs. H. Pengawas Madrasah, M.Pd.I (Pengawas Kemenag)</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <button type="submit" class="bar-btn" style="background:#0284c7; color:#fff; padding:10px 22px; font-size:0.88rem;">
                            💾 Simpan Verifikasi & Catatan Pengawas
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>

    <?php include __DIR__ . '/_pkkm_koleksi_modal.php'; ?>
</body>
</html>
