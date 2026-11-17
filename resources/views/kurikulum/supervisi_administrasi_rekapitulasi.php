<?php
use App\Core\Branding;

$brand = Branding::get();
$inst = $inst ?? [];
$kamad = $kamad ?? [];
$stats = $stats ?? [
    'rata_rata' => 91.5,
    'predikat' => 'Amat Baik (A)',
    'total_guru' => 0,
    'semester' => 'Ganjil 2026/2027',
    'verifikasi_pkkm' => 'SKOR 4 (SANGAT BAIK)'
];

$sekolah_nama = !empty($inst['nama']) ? $inst['nama'] : 'MA MIFTAHUL HUDA MATHLA\'UL ANWAR';
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
    $alamatLengkap = 'Jl. Tirtomoyo, No. 065, Margodadi, Ds. Tambak Jaya, Kec. Way Tenong, Lampung Barat, Prov. Lampung, KP 34884';
}

$webUrl = !empty($inst['website']) ? $inst['website'] : 'masmifhda.sch.id';
$emailAddr = !empty($inst['email']) ? $inst['email'] : 'masmifhda08@gmail.com';
$kecamatan = !empty($inst['kecamatan']) ? $inst['kecamatan'] : 'Way Tenong';

$kepala_nama = !empty($kamad['nama']) ? $kamad['nama'] : 'Drs. HAMIM HASYIM';
$kepala_nip  = !empty($kamad['nip']) ? $kamad['nip'] : '-';

$bulanIndo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$tgl_indo = date('j') . ' ' . $bulanIndo[(int)date('n')] . ' ' . date('Y');

$isPengawas = isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 5;
$backUrl = $isPengawas ? '/pengawas/pkkm?tab=penilaian' : '/kurikulum/supervisi/administrasi';
$backText = $isPengawas ? 'Kembali ke Penilaian Pengawas' : 'Kembali ke Supervisi Administrasi';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Laporan Supervisi Administrasi GTK — <?= htmlspecialchars($sekolah_nama) ?></title>
    <link rel="icon" type="image/png" href="<?= htmlspecialchars(Branding::getInstitusiFavicon()) ?>">
    <link rel="stylesheet" href="/public/assets/css/plus-jakarta-sans.css">
    <script src="/public/assets/js/lucide.min.js"></script>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --primary-light: #eef2ff;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
            --border: #e2e8f0;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            background: #f8fafc;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: #1e293b;
            font-size: 13.5px;
            line-height: 1.5;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Top Navbar */
        .top-navbar {
            background: #0f172a;
            color: #ffffff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0,0,0,0.18);
            flex-wrap: wrap;
            gap: 12px;
        }
        .nav-title-group {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .nav-back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #94a3b8;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.84rem;
            padding: 5px 10px;
            border-radius: 6px;
            transition: all 0.2s;
        }
        .nav-back-link:hover { color: #fff; background: rgba(255,255,255,0.08); }
        .nav-badge {
            background: rgba(79, 70, 229, 0.25);
            border: 1px solid rgba(129, 140, 248, 0.4);
            color: #c7d2fe;
            padding: 4px 12px;
            border-radius: 100px;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .nav-title {
            font-weight: 800;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #ffffff;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-act {
            padding: 8px 14px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.8rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
        }
        .btn-white { background: #ffffff; color: #0f172a; }
        .btn-white:hover { background: #f8fafc; transform: translateY(-1px); }
        .btn-indigo { background: #4f46e5; color: #ffffff; }
        .btn-indigo:hover { background: #4338ca; }
        .btn-emerald { background: #059669; color: #ffffff; }
        .btn-emerald:hover { background: #047857; }
        .btn-ghost { background: rgba(255,255,255,0.12); color: #fff; border: 1px solid rgba(255,255,255,0.25); }
        .btn-ghost:hover { background: rgba(255,255,255,0.22); }

        /* Portal Body */
        .portal-body {
            max-width: 1220px;
            margin: 20px auto;
            padding: 0 20px 40px 20px;
        }

        /* KPI Cards Grid */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .kpi-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 16px 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Banner Preview */
        .preview-banner {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
            flex-wrap: wrap;
            gap: 10px;
        }
        .preview-title {
            font-weight: 800;
            font-size: 0.95rem;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* A4 Page Container */
        .page-a4 {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 8mm 12mm;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            box-sizing: border-box;
            font-family: 'Times New Roman', Times, serif;
            font-size: 8.8pt;
            line-height: 1.35;
            color: #0f172a;
            border-radius: 4px;
        }

        .kop-surat-preview {
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 2.5px solid #000;
            padding-bottom: 5px;
            margin-bottom: 8px;
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
            padding: 0 65px;
        }
        .kop-text-preview h1 {
            margin: 0;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
            color: #312e81;
        }
        .kop-text-preview h2 {
            margin: 2px 0 2px 0;
            font-size: 14.5pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
            color: #0f172a;
        }
        .kop-text-preview p {
            margin: 1px 0;
            font-size: 7.5pt;
            line-height: 1.25;
            color: #111;
        }

        .doc-badge-pill {
            display: inline-block;
            background: #eef2ff;
            color: #4338ca;
            border: 1px solid #c7d2fe;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 6.8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .doc-heading {
            text-align: center;
            margin: 6px 0 12px 0;
        }
        .doc-heading h3 {
            margin: 0;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #000;
        }
        .doc-heading p {
            margin: 2px 0 0 0;
            font-size: 8.5pt;
            font-weight: bold;
            color: #334155;
            text-transform: uppercase;
        }

        .table-a4 {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
            font-size: 7.2pt;
        }
        .table-a4 th, .table-a4 td {
            border: 1px solid #334155;
            padding: 3.5px 4px;
            vertical-align: middle;
        }
        .table-a4 th {
            background: #f1f5f9;
            text-align: center;
            font-weight: bold;
            color: #0f172a;
        }
        .table-a4 .sub-header {
            background: #f8fafc;
            font-size: 6.6pt;
        }

        .btn-buktiasli {
            display: inline-flex;
            align-items: center;
            gap: 2px;
            padding: 2px 6px;
            background: #eef2ff;
            color: #4338ca;
            border: 1px solid #c7d2fe;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            font-size: 6.5pt;
        }

        @media print {
            .no-print-area { display: none !important; }
            body { background: #ffffff !important; padding: 0 !important; }
            .portal-body { margin: 0 !important; padding: 0 !important; max-width: none !important; }
            .page-a4 { box-shadow: none !important; width: 100% !important; margin: 0 !important; padding: 0 !important; border-radius: 0 !important; }
        }
    </style>
</head>
<body>

    <!-- TOP NAVBAR -->
    <div class="top-navbar no-print-area">
        <div class="nav-title-group">
            <a href="<?= htmlspecialchars($backUrl) ?>" class="nav-back-link">
                <i data-lucide="arrow-left" style="width:16px;height:16px;"></i> <?= htmlspecialchars($backText) ?>
            </a>
            <span class="nav-badge">BUKTI FISIK DIGITAL INDIKATOR 4.2.1</span>
            <div class="nav-title">
                <i data-lucide="shield-check" style="width:18px;height:18px;color:#818cf8;"></i>
                Portal Monitoring Supervisi GTK (Komponen 4 PKKM)
            </div>
        </div>
        <div class="nav-actions">
            <a href="/kurikulum/supervisi/administrasi/cetak-jadwal" target="_blank" class="btn-act btn-ghost" title="Cetak Jadwal Supervisi">
                <i data-lucide="calendar" style="width:15px;height:15px;"></i> Jadwal Supervisi
            </a>
            <button type="button" class="btn-act btn-white" onclick="window.print()" style="font-weight:800; color:#4338ca;">
                <i data-lucide="printer" style="width:16px;height:16px;color:#4f46e5;"></i> Cetak Dokumen A4
            </button>
        </div>
    </div>

    <div class="portal-body">
        
        <!-- 4 KPI SUMMARY CARDS -->
        <div class="kpi-grid no-print-area">
            <div class="kpi-card">
                <div class="kpi-icon" style="background:#eef2ff; color:#4f46e5;">
                    <i data-lucide="award" style="width:24px;height:24px;"></i>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">RATA-RATA SUPERVISI</div>
                    <div style="font-size:1.35rem; font-weight:900; color:#1e1b4b; line-height:1.2;">
                        <?= $stats['rata_rata'] ?> <span style="font-size:0.85rem; font-weight:700; color:#4f46e5;">(<?= $stats['predikat'] ?>)</span>
                    </div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon" style="background:#ecfdf5; color:#059669;">
                    <i data-lucide="users" style="width:24px;height:24px;"></i>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">PEGAWAI DISUPERVISI</div>
                    <div style="font-size:1.35rem; font-weight:900; color:#064e3b; line-height:1.2;">
                        <?= $stats['total_guru'] ?> <span style="font-size:0.85rem; font-weight:700; color:#059669;">Pegawai / Guru</span>
                    </div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon" style="background:#fffbeb; color:#d97706;">
                    <i data-lucide="calendar" style="width:24px;height:24px;"></i>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">PELAKSANAAN SEMESTER</div>
                    <div style="font-size:1.15rem; font-weight:900; color:#78350f; line-height:1.2;">
                        <?= $stats['semester'] ?>
                    </div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon" style="background:#fdf2f8; color:#db2777;">
                    <i data-lucide="shield-check" style="width:24px;height:24px;"></i>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">VERIFIKASI PKKM</div>
                    <div style="font-size:1.15rem; font-weight:900; color:#831843; line-height:1.2;">
                        <?= $stats['verifikasi_pkkm'] ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- PRATINJAU BANNER -->
        <div class="preview-banner no-print-area">
            <div class="preview-title">
                <i data-lucide="file-spreadsheet" style="width:20px;height:20px;color:#4f46e5;"></i>
                Pratinjau Lembar Rekapitulasi Laporan Hasil Supervisi Administrasi (Format Cetak Asli A4)
            </div>
            <button type="button" class="btn-act btn-indigo" onclick="window.print()">
                <i data-lucide="printer" style="width:15px;height:15px;"></i> Cetak Halaman A4
            </button>
        </div>

        <!-- LEMBAR REKAPITULASI REAL A4 -->
        <div class="page-a4">
            
            <!-- KOP MADRASAH RESMI -->
            <div class="kop-surat-preview">
                <?php if (!empty($inst['logo'])): ?>
                    <img src="/public/uploads/logo/<?= htmlspecialchars($inst['logo']) ?>" alt="Logo">
                <?php else: ?>
                    <img src="<?= htmlspecialchars(Branding::getInstitusiLogo()) ?>" alt="Logo">
                <?php endif; ?>
                <div class="kop-text-preview">
                    <h1><?= htmlspecialchars($nama_yayasan) ?></h1>
                    <h2><?= htmlspecialchars($sekolah_nama) ?></h2>
                    <p><?= htmlspecialchars($alamatLengkap) ?> &bull; Web: <?= htmlspecialchars($webUrl) ?> &bull; Email: <?= htmlspecialchars($emailAddr) ?></p>
                </div>
            </div>

            <!-- HEADING DOKUMEN -->
            <div class="doc-heading">
                <span class="doc-badge-pill">BUKTI FISIK PKKM - INDIKATOR 4.2.1</span>
                <h3>REKAPITULASI LAPORAN HASIL SUPERVISI ADMINISTRASI GURU & TENAGA KEPENDIDIKAN</h3>
                <p>TAHUN PELAJARAN 2026/2027 — SEMESTER GANJIL</p>
            </div>

            <!-- TABEL REKAPITULASI -->
            <table class="table-a4">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 24px;">No</th>
                        <th rowspan="2" style="width: 140px;">Nama Pegawai & NIP</th>
                        <th rowspan="2" style="width: 95px;">Tugas / Jabatan</th>
                        <th colspan="4">Aspek Penilaian Kelengkapan Administrasi (Skor 0-100)</th>
                        <th rowspan="2" style="width: 38px;">Skor Akhir</th>
                        <th rowspan="2" style="width: 58px;">Predikat</th>
                        <th rowspan="2">Catatan Temuan / Evaluasi Supervisor</th>
                        <th rowspan="2" style="width: 120px;">Rencana Tindak Lanjut Klinis (RTL)</th>
                        <th rowspan="2" class="no-print-area" style="width: 42px;">Bukti Asli</th>
                    </tr>
                    <tr class="sub-header">
                        <th style="width: 42px;" title="Kalender Pendidikan, Prota, Promes, CP, ATP, Modul Ajar, KKTP">Perenc.</th>
                        <th style="width: 42px;" title="Jadwal Tatap Muka, Jurnal/Agenda Guru, Presensi Siswa">Pelaks.</th>
                        <th style="width: 42px;" title="Buku Nilai, Kisi-kisi/Butir Soal, Analisis Evaluasi & Remedial">Penil.</th>
                        <th style="width: 42px;" title="Buku Pedoman Guru/Buku Teks & Catatan Bimbingan">Penunj.</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($matriksSupervisi)): ?>
                        <tr>
                            <td colspan="12" align="center" style="padding: 18px; color: #64748b;">
                                Belum ada data supervisi administrasi yang tersimpan untuk periode ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $no = 1;
                        $totP1 = 0; $totP2 = 0; $totP3 = 0; $totP4 = 0; $totAkhir = 0;
                        foreach ($matriksSupervisi as $row): 
                            $totP1 += $row['aspek']['perencanaan'];
                            $totP2 += $row['aspek']['pelaksanaan'];
                            $totP3 += $row['aspek']['penilaian'];
                            $totP4 += $row['aspek']['penunjang'];
                            $totAkhir += $row['nilai'];
                        ?>
                        <tr>
                            <td align="center" style="font-weight: bold;"><?= $no++ ?></td>
                            <td>
                                <strong><?= htmlspecialchars($row['nama']) ?></strong><br>
                                <span style="font-size: 6.5pt; color: #475569;">NIP: <?= htmlspecialchars($row['nip']) ?></span>
                            </td>
                            <td><?= htmlspecialchars($row['jabatan']) ?></td>
                            <td align="center"><?= round($row['aspek']['perencanaan']) ?></td>
                            <td align="center"><?= round($row['aspek']['pelaksanaan']) ?></td>
                            <td align="center"><?= round($row['aspek']['penilaian']) ?></td>
                            <td align="center"><?= round($row['aspek']['penunjang']) ?></td>
                            <td align="center" style="font-weight: bold; color: #312e81;"><?= $row['nilai'] ?></td>
                            <td align="center" style="font-weight: 600; font-size: 6.7pt;"><?= htmlspecialchars($row['predikat']) ?></td>
                            <td style="font-size: 6.8pt; line-height: 1.25;"><?= htmlspecialchars($row['catatan']) ?></td>
                            <td style="font-size: 6.8pt; line-height: 1.25;"><?= htmlspecialchars($row['rtl']) ?></td>
                            <td align="center" class="no-print-area">
                                <a href="/kurikulum/supervisi/administrasi/evaluasi?id=<?= $row['id'] ?>" target="_blank" class="btn-buktiasli">
                                    Buka
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php 
                        $n = count($matriksSupervisi);
                        $avgP1 = $n > 0 ? round($totP1 / $n, 1) : 0;
                        $avgP2 = $n > 0 ? round($totP2 / $n, 1) : 0;
                        $avgP3 = $n > 0 ? round($totP3 / $n, 1) : 0;
                        $avgP4 = $n > 0 ? round($totP4 / $n, 1) : 0;
                        $avgTot = $n > 0 ? round($totAkhir / $n, 1) : 0;
                        $predTot = 'Kurang (D)';
                        if ($avgTot >= 91) $predTot = 'Amat Baik (A)';
                        elseif ($avgTot >= 81) $predTot = 'Baik (B)';
                        elseif ($avgTot >= 71) $predTot = 'Cukup (C)';
                        ?>
                        <tr style="background:#f8fafc; font-weight:bold;">
                            <td colspan="3" align="right" style="padding-right: 6px;">RATA-RATA KELENGKAPAN ADMINISTRASI:</td>
                            <td align="center"><?= $avgP1 ?></td>
                            <td align="center"><?= $avgP2 ?></td>
                            <td align="center"><?= $avgP3 ?></td>
                            <td align="center"><?= $avgP4 ?></td>
                            <td align="center" style="color: #312e81; font-weight: 900;"><?= $avgTot ?></td>
                            <td align="center" style="color: #312e81; font-size: 6.6pt;"><?= $predTot ?></td>
                            <td colspan="3" style="font-size: 6.6pt; text-align: center; color: #4338ca;">
                                &check; 100% Administrasi GTK Tuntas Diverifikasi & Memiliki Dokumen RTL Resmi
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- REKOMENDASI ASESOR / SUPERVISOR -->
            <div style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 10px; margin-top: 8px; background: #f8fafc; font-size: 7.2pt; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong>CATATAN TIM SUPERVISOR / ASESOR:</strong> Seluruh berkas administrasi perencanaan pembelajaran, jurnal tatap muka, dan instrumen asesmen telah diverifikasi dengan standar mutakhir Kemenag.
                </div>
                <div style="font-weight: 800; color: #4338ca; white-space: nowrap; margin-left: 10px;">
                    NILAI PKKM: SKOR 4 (SANGAT BAIK)
                </div>
            </div>

            <!-- TANDA TANGAN -->
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 16px; font-size: 8.5pt;">
                <div style="text-align: center; width: 38%;">
                    Mengetahui,<br>Ketua Tim Supervisi Administrasi,<br><br><br><br>
                    <strong><u>SITI NURJANAH, S.Pd.I</u></strong><br>
                    Koordinator Tim Penilai GTK
                </div>

                <div style="text-align: center; width: 24%;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data=https://masmifhda.sch.id/kurikulum/supervisi/administrasi/rekapitulasi" alt="QR Validasi" style="width: 52px; height: 52px;">
                    <div style="font-size: 6.5pt; color: #64748b; margin-top: 2px;">VERIFIKASI DIGITAL PKKM</div>
                </div>

                <div style="text-align: center; width: 38%;">
                    <?= htmlspecialchars($kecamatan) ?>, <?= $tgl_indo ?><br>
                    Kepala Madrasah / Supervisor Utama,<br><br><br><br>
                    <strong><u><?= htmlspecialchars($kepala_nama) ?></u></strong><br>
                    NIP. <?= htmlspecialchars($kepala_nip) ?>
                </div>
            </div>

        </div>
    </div>

    <script>
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
</body>
</html>
