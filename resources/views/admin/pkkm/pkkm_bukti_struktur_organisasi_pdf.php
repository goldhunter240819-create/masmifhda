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
    <title>Bukti Fisik PKKM - Bagan Struktur Organisasi & Tupoksi - <?php echo htmlspecialchars($sekolah_nama); ?></title>
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

        .doc-title-wrap { text-align: center; margin-bottom: 10px; }
        .doc-title { font-size: 11pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; margin: 0; }
        .doc-nomor { font-size: 8.5pt; margin-top: 2px; color: #334155; }

        /* Bagan Organigram CSS */
        .chart-wrap {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: #f8fafc;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            padding: 10px 8px;
            margin-bottom: 10px;
        }

        .chart-node {
            background: #ffffff;
            border-radius: 6px;
            padding: 5px 8px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            display: inline-block;
        }
        .chart-node-role {
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }
        .chart-node-name {
            font-size: 0.74rem;
            font-weight: 700;
            margin-top: 2px;
            color: #0f172a;
            line-height: 1.2;
        }
        .chart-node-nip {
            font-size: 0.62rem;
            color: #64748b;
        }

        /* Tingkatan Warna Node */
        .node-top {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1.5px solid #7dd3fc;
        }
        .node-top .chart-node-role { color: #0c4a6e; }

        .node-kamad {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            border: 2px solid #0f172a;
            color: #ffffff;
            padding: 7px 16px;
            min-width: 220px;
            box-shadow: 0 4px 10px rgba(2, 132, 199, 0.25);
        }
        .node-kamad .chart-node-role { color: #bae6fd; font-size: 0.74rem; }
        .node-kamad .chart-node-name { color: #ffffff; font-size: 0.85rem; font-weight: 800; }
        .node-kamad .chart-node-nip { color: #e0f2fe; font-size: 0.66rem; }

        .node-staff {
            background: #f1f5f9;
            border: 1.5px solid #94a3b8;
            min-width: 140px;
        }
        .node-staff .chart-node-role { color: #334155; }

        .node-waka {
            background: #ffffff;
            border: 1.5px solid #3b82f6;
            flex: 1;
            min-width: 105px;
        }
        .node-waka .chart-node-role { color: #1d4ed8; font-size: 0.65rem; }

        .node-guru {
            background: #f8fafc;
            border: 1px dashed #64748b;
            width: 100%;
            padding: 6px;
        }
        .node-guru .chart-node-role { color: #475569; font-size: 0.7rem; }

        /* Garis Alur Konektor */
        .line-v {
            width: 2px;
            height: 10px;
            background: #94a3b8;
            margin: 0 auto;
        }
        .line-h-bar {
            height: 2px;
            background: #94a3b8;
            width: 82%;
            margin: 0 auto;
        }

        /* Tabel Uraian Tugas (Tupoksi) */
        .tupoksi-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 10px;
        }
        .tupoksi-table th, .tupoksi-table td {
            border: 1px solid #94a3b8;
            padding: 4px 6px;
            vertical-align: top;
        }
        .tupoksi-table th {
            background: #e2e8f0;
            font-weight: bold;
            text-align: center;
            color: #0f172a;
            font-size: 8.5pt;
        }
        .tupoksi-role { font-weight: bold; color: #0f172a; width: 22%; }
        .tupoksi-nama { width: 23%; color: #1e293b; font-weight: 600; }
        .tupoksi-desc { width: 55%; color: #334155; }

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
        .ttd-space { height: 50px; }
        .ttd-name { font-weight: bold; text-decoration: underline; margin-bottom: 1px; }

        .stempel-badge {
            position: absolute;
            top: 25px;
            left: 50%;
            transform: translateX(-50%) rotate(-7deg);
            width: 85px;
            height: 85px;
            border: 2.5px solid #0284c7;
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
        .stempel-text-top { font-size: 6.5px; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; }
        .stempel-star { font-size: 9px; line-height: 1; margin: 1px 0; }
        .stempel-text-bot { font-size: 6px; font-weight: 700; text-transform: uppercase; }

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

    <!-- Bar Navigasi & Download (Tidak ikut tercetak) -->
    <div class="no-print-bar">
        <div class="bar-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <span>Bukti Fisik PKKM: Bagan Struktur Organisasi & Tupoksi</span>
            <span class="bar-badge">Indikator 1.1.1 & 1.1.2</span>
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
            <h1 class="doc-title">BAGAN STRUKTUR ORGANISASI & PEMBAGIAN TUGAS (JOB DESCRIPTION)</h1>
            <div class="doc-nomor">Nomor SK Penetapan: 012/MIS-MH/SK.ORG/VII/2026 | Tahun Pelajaran 2026/2027</div>
        </div>

        <!-- BAGAN ORGANIGRAM VISUAL -->
        <div class="chart-wrap">
            <!-- Level 1: Yayasan & Komite -->
            <div style="display: flex; justify-content: space-around; align-items: center; margin-bottom: 2px;">
                <div class="chart-node node-top" style="min-width: 170px;">
                    <div class="chart-node-role">Yayasan Mathla'ul Anwar</div>
                    <div class="chart-node-name">Pengurus Cabang</div>
                </div>
                <div style="width: 40px; height: 1.5px; border-top: 1.5px dashed #94a3b8;"></div>
                <div class="chart-node node-top" style="min-width: 170px;">
                    <div class="chart-node-role">Komite Madrasah</div>
                    <div class="chart-node-name">H. Masrur, S.Pd</div>
                </div>
            </div>

            <!-- Konektor ke Kamad -->
            <div class="line-v" style="height: 8px;"></div>

            <!-- Level 2: Kepala Madrasah -->
            <div style="text-align: center;">
                <div class="chart-node node-kamad">
                    <div class="chart-node-role">Kepala Madrasah</div>
                    <div class="chart-node-name"><?php echo htmlspecialchars($kepala_nama); ?></div>
                    <div class="chart-node-nip">NIP: <?php echo htmlspecialchars($kepala_nip); ?></div>
                </div>
            </div>

            <!-- Konektor cabang TU & Bendahara -->
            <div class="line-v" style="height: 6px;"></div>
            <div style="display: flex; justify-content: center; gap: 40px; margin-bottom: 2px;">
                <div class="chart-node node-staff">
                    <div class="chart-node-role">Tata Usaha & Operator TIK</div>
                    <div class="chart-node-name">Rina Marlina, S.Kom</div>
                </div>
                <div class="chart-node node-staff">
                    <div class="chart-node-role">Bendahara Madrasah</div>
                    <div class="chart-node-name">Tri Wahyuni, S.Pd</div>
                </div>
            </div>

            <!-- Garis Horizontal pembagi 4 Waka -->
            <div class="line-v" style="height: 6px;"></div>
            <div class="line-h-bar"></div>
            <div style="display: flex; justify-content: space-between; width: 82%; margin: 0 auto; height: 6px;">
                <div class="line-v" style="height: 6px; margin: 0;"></div>
                <div class="line-v" style="height: 6px; margin: 0;"></div>
                <div class="line-v" style="height: 6px; margin: 0;"></div>
                <div class="line-v" style="height: 6px; margin: 0;"></div>
            </div>

            <!-- Level 3: 4 Wakil Kepala / Koordinator -->
            <div style="display: flex; gap: 8px; justify-content: space-between; margin-bottom: 6px;">
                <div class="chart-node node-waka">
                    <div class="chart-node-role">Waka Kurikulum</div>
                    <div class="chart-node-name">Ahmad Fauzi, S.Pd.I</div>
                </div>
                <div class="chart-node node-waka">
                    <div class="chart-node-role">Waka Kesiswaan</div>
                    <div class="chart-node-name">Siti Fatimah, S.Pd</div>
                </div>
                <div class="chart-node node-waka">
                    <div class="chart-node-role">Waka Sarpras</div>
                    <div class="chart-node-name">Nur Hidayah, S.Pd</div>
                </div>
                <div class="chart-node node-waka">
                    <div class="chart-node-role">Waka Humas</div>
                    <div class="chart-node-name">M. Yusuf, S.Pd.I</div>
                </div>
            </div>

            <!-- Konektor ke Guru & Siswa -->
            <div class="line-v" style="height: 6px;"></div>

            <!-- Level 4: Dewan Guru & Siswa -->
            <div class="chart-node node-guru">
                <div class="chart-node-role">Dewan Guru & Wali Kelas (Kelas I s.d VI) | Tenaga Kependidikan</div>
                <div style="font-size: 0.65rem; color: #64748b; margin-top: 2px;">
                    Membina seluruh Peserta Didik MIS Miftahul Huda Mathla'ul Anwar didampingi Paguyuban Wali Murid
                </div>
            </div>
        </div>

        <!-- MATRIKS URUSAN TUGAS POKOK & FUNGSI (TUPOKSI) -->
        <div style="margin-bottom: 4px; font-weight: bold; font-size: 9pt; color: #0f172a; text-transform: uppercase;">
            Ringkasan Pembagian Tugas Pokok & Fungsi (Tupoksi):
        </div>
        <table class="tupoksi-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Jabatan</th>
                    <th>Pejabat / Personalia</th>
                    <th>Uraian Tugas Pokok & Tanggung Jawab</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td class="tupoksi-role">Kepala Madrasah</td>
                    <td class="tupoksi-nama"><?php echo htmlspecialchars($kepala_nama); ?></td>
                    <td class="tupoksi-desc">Penanggung jawab utama manajerial, supervisi akademik, kepemimpinan kewirausahaan, serta pengambilan kebijakan strategis madrasah.</td>
                </tr>
                <tr>
                    <td style="text-align: center;">2</td>
                    <td class="tupoksi-role">Waka Kurikulum</td>
                    <td class="tupoksi-nama">Ahmad Fauzi, S.Pd.I</td>
                    <td class="tupoksi-desc">Mengelola Kurikulum Merdeka/K13, jadwal KBM, kalender pendidikan, evaluasi asesmen (ANBK/ASAS), dan pembagian beban mengajar guru.</td>
                </tr>
                <tr>
                    <td style="text-align: center;">3</td>
                    <td class="tupoksi-role">Waka Kesiswaan</td>
                    <td class="tupoksi-nama">Siti Fatimah, S.Pd</td>
                    <td class="tupoksi-desc">Mengkoordinasikan Matsama, PPDB online, pembinaan kedisiplinan dan akhlak siswa, kegiatan ekstrakurikuler, serta prestasi kompetisi.</td>
                </tr>
                <tr>
                    <td style="text-align: center;">4</td>
                    <td class="tupoksi-role">Waka Sarana Prasarana</td>
                    <td class="tupoksi-nama">Nur Hidayah, S.Pd</td>
                    <td class="tupoksi-desc">Menginventarisir aset, pemeliharaan lab komputer/TIK, sanitasi, gedung, serta ketersediaan media pembelajaran multimedia.</td>
                </tr>
                <tr>
                    <td style="text-align: center;">5</td>
                    <td class="tupoksi-role">Tata Usaha & TIK</td>
                    <td class="tupoksi-nama">Rina Marlina, S.Kom</td>
                    <td class="tupoksi-desc">Pengelolaan administrasi surat-menyurat, sinkronisasi data EMIS 4.0, Simpatika Kemenag, absensi digital, dan portal website madrasah.</td>
                </tr>
            </tbody>
        </table>

        <!-- PENGESAHAN & TANDA TANGAN -->
        <div class="ttd-wrap">
            <div class="ttd-box">
                Mengetahui,<br>
                Ketua Komite Madrasah,
                <div class="ttd-space"></div>
                <div class="ttd-name">H. MASRUR, S.Pd</div>
                <div style="font-size: 8pt; color: #64748b;">NIP. -</div>
            </div>

            <div class="ttd-box">
                Ditetapkan di: <?php echo htmlspecialchars($kabupaten); ?><br>
                Pada tanggal: <?php echo $tgl_indo; ?><br>
                Kepala Madrasah,
                <div class="ttd-space"></div>
                <div class="ttd-name"><?php echo htmlspecialchars($kepala_nama); ?></div>
                <div style="font-size: 8pt; color: #64748b;">NIP. <?php echo htmlspecialchars($kepala_nip); ?></div>

                <!-- Stempel Resmi Digital -->
                <div class="stempel-badge">
                    <span class="stempel-text-top">MIS MIFTAHUL HUDA</span>
                    <span class="stempel-star">★ KEMENAG ★</span>
                    <span class="stempel-text-bot">TERVALIDASI RESMI</span>
                </div>
            </div>
        </div>

    </div>

    <?php include (file_exists(__DIR__ . '/_pkkm_koleksi_modal.php') ? __DIR__ . '/_pkkm_koleksi_modal.php' : __DIR__ . '/pkkm/_pkkm_koleksi_modal.php'); ?>
</body>
</html>
