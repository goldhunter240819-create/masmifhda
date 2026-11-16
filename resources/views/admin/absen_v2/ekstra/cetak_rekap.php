<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Presensi Ekstrakurikuler - <?php echo htmlspecialchars($selected_ekstra); ?></title>
    <link rel="icon" type="image/png" href="<?php echo base_url('uploads/kop/logo.png'); ?>">
    <!-- Lucide Icons -->
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 10mm;
        }
        * {
            box-sizing: border-box;
            font-family: 'Times New Roman', Times, serif;
        }
        body {
            margin: 0;
            padding: 0;
            background: #f1f5f9;
            color: #000;
            font-size: 9pt;
        }
        
        .no-print {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background: #1e293b;
            color: white;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .btn-print {
            background: #10b981;
            color: white;
            border: none;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-back {
            background: #64748b;
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .print-container {
            width: 297mm;
            max-width: 100%;
            margin: 15px auto;
            background: white;
            padding: 8mm 12mm;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        /* KOP SURAT */
        .header-kop {
            text-align: center;
            border-bottom: 2.5px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
            position: relative;
        }
        .header-kop img {
            width: 65px;
            height: auto;
            position: absolute;
            left: 5px;
            top: 2px;
        }
        .header-text {
            line-height: 1.25;
        }
        .header-text h3 {
            margin: 0;
            font-size: 11pt;
            font-weight: normal;
            letter-spacing: 0.5px;
        }
        .header-text h2 {
            margin: 2px 0;
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .header-text p {
            margin: 1px 0;
            font-size: 8pt;
        }

        .report-title {
            text-align: center;
            font-size: 11.5pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin-top: 10px;
            margin-bottom: 3px;
        }
        .report-sub {
            text-align: center;
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 12px;
        }

        .info-meta {
            width: 100%;
            margin-bottom: 8px;
            font-size: 8.5pt;
        }
        .info-meta td {
            padding: 2px 4px;
        }

        /* TABEL */
        table.tbl-matrix {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 15px;
        }
        table.tbl-matrix th, table.tbl-matrix td {
            border: 1px solid #000;
            padding: 3.5px 2px;
            text-align: center;
            vertical-align: middle;
        }
        table.tbl-matrix th {
            background-color: #f1f5f9 !important;
            font-weight: bold;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .tbl-matrix .col-nama {
            text-align: left;
            padding-left: 6px;
        }

        /* Tanda Tangan */
        .ttd-box {
            width: 100%;
            margin-top: 15px;
            font-size: 9pt;
            page-break-inside: avoid;
        }
        .ttd-box td {
            text-align: center;
            vertical-align: top;
            width: 50%;
            line-height: 1.35;
        }

        @media print {
            body { background: white; }
            .no-print { display: none !important; }
            .print-container {
                width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

<?php
    $dbCore = \App\Core\Database::connect('core');
    $kop = null;
    try {
        $kop = $dbCore->query("SELECT * FROM institusi LIMIT 1")->fetch();
    } catch (\Exception $e) {}
    if (!$kop) {
        try {
            $kop = $dbCore->query("SELECT * FROM pengaturan_kop_surat LIMIT 1")->fetch();
        } catch (\Exception $e) {}
    }

    $logoSrc = ($kop && !empty($kop['logo'])) ? base_url('uploads/' . $kop['logo']) : base_url('uploads/kop/logo.png');
    $namaYayasan = $kop['nama_yayasan'] ?? 'YAYASAN PONDOK PESANTREN MIFTAHUL HUDA MATHLA\'UL ANWAR';
    $namaMadrasah = $kop['nama'] ?? ($kop['nama_madrasah'] ?? 'MA MIFTAHUL HUDA MATHLA\'UL ANWAR');
    $alamatJalan = $kop['alamat'] ?? ($kop['alamat_jalan'] ?? 'Jl. Tirtomoyo No. 083, Margorahayu, Kec. Way Tenong, Kab. Lampung Barat');
    $telepon = $kop['telepon'] ?? '081938240819';
    $email = $kop['email'] ?? 'masmiftahulhuda083@gmail.com';

    $months_indo = ['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'];
    $tgl_cetak = date('d') . ' ' . ($months_indo[date('m')] ?? date('F')) . ' ' . date('Y');
?>

<!-- TOOLBAR NO-PRINT -->
<div class="no-print">
    <div style="display:flex;align-items:center;gap:12px;">
        <span style="font-weight:bold;font-size:14px;">Laporan Presensi Ekstrakurikuler</span>
        <span style="background:rgba(255,255,255,0.2);padding:2px 10px;border-radius:10px;font-size:12px;">
            <?php echo htmlspecialchars($selected_ekstra); ?>
        </span>
    </div>
    <div style="display:flex;gap:10px;">
        <button class="btn-print" onclick="window.print()">
            <i data-lucide="printer" style="width:16px;"></i> Cetak Dokumen (A4 Landscape)
        </button>
        <a href="<?php echo base_url('admin/absen_v2/ekstra/rekap?ekstra=' . urlencode($selected_ekstra)); ?>" class="btn-back">
            <i data-lucide="arrow-left" style="width:16px;"></i> Kembali
        </a>
    </div>
</div>

<div class="print-container">
    <!-- KOP MADRASAH -->
    <div class="header-kop">
        <img src="<?php echo $logoSrc; ?>" alt="Logo" onerror="this.src='<?php echo base_url('uploads/kop/logo.png'); ?>'; this.onerror=function(){this.style.display='none'};">
        <div class="header-text">
            <h3><?php echo htmlspecialchars($namaYayasan); ?></h3>
            <h2><?php echo htmlspecialchars($namaMadrasah); ?></h2>
            <p><?php echo nl2br(htmlspecialchars($alamatJalan)); ?></p>
            <p>Telp: <?php echo htmlspecialchars($telepon); ?> | Email: <?php echo htmlspecialchars($email); ?></p>
        </div>
    </div>

    <div class="report-title">
        REKAPITULASI PRESENSI KEGIATAN EKSTRAKURIKULER
    </div>
    <div class="report-sub">
        CABANG: <?php echo strtoupper(htmlspecialchars($selected_ekstra)); ?> • TAHUN AJARAN <?php echo htmlspecialchars($tahun_ajaran); ?> (SEMESTER <?php echo strtoupper(htmlspecialchars($semester)); ?>)
    </div>

    <table class="info-meta">
        <tr>
            <td style="width:18%;"><strong>Cabang Ekstrakurikuler</strong></td>
            <td style="width:2%;">:</td>
            <td style="width:40%;"><strong><?php echo htmlspecialchars($selected_ekstra); ?></strong></td>
            <td style="width:18%;"><strong>Jadwal Latihan</strong></td>
            <td style="width:2%;">:</td>
            <td style="width:20%;"><?php echo htmlspecialchars(($ekstraDetail['jadwal_hari'] ?? 'Sabtu') . ', ' . ($ekstraDetail['jadwal_waktu'] ?? '14.00 - 16.00 WIB')); ?></td>
        </tr>
        <tr>
            <td><strong>Guru Pembina</strong></td>
            <td>:</td>
            <td><?php echo htmlspecialchars($ekstraDetail['nama_pembina'] ?? '-'); ?></td>
            <td><strong>Total Pertemuan</strong></td>
            <td>:</td>
            <td><?php echo count($daftarPertemuan); ?> Pertemuan</td>
        </tr>
    </table>

    <!-- TABEL MATRIKS PRESENSI -->
    <table class="tbl-matrix">
        <thead>
            <tr>
                <th rowspan="2" style="width: 25px;">No</th>
                <th rowspan="2" style="width: 65px;">NIS</th>
                <th rowspan="2" class="col-nama" style="min-width: 160px;">Nama Siswa</th>
                <th rowspan="2" style="width: 55px;">Kelas</th>
                
                <?php if (!empty($daftarPertemuan)): ?>
                    <th colspan="<?php echo count($daftarPertemuan); ?>">
                        Pertemuan & Tanggal Latihan
                    </th>
                <?php endif; ?>

                <th colspan="4" style="width: 80px;">Rekap</th>
                <th rowspan="2" style="width: 45px;">% Hdr</th>
            </tr>
            <tr>
                <?php foreach($daftarPertemuan as $p): ?>
                    <th style="font-size: 6.5pt; width: 22px; line-height: 1.1; padding: 2px 1px;">
                        P-<?php echo $p['pertemuan_ke'] ?: '1'; ?><br>
                        <?php echo date('d/m', strtotime($p['tanggal'])); ?>
                    </th>
                <?php endforeach; ?>
                <th style="width: 20px;">H</th>
                <th style="width: 20px;">S</th>
                <th style="width: 20px;">I</th>
                <th style="width: 20px;">A</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rekapSiswa)): ?>
                <tr>
                    <td colspan="<?php echo (5 + count($daftarPertemuan) + 5); ?>" style="padding: 15px; text-align: center; color: #64748b;">
                        Tidak ada data anggota atau kegiatan presensi yang tercatat.
                    </td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach($rekapSiswa as $rs): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($rs['nis'] ?: '-'); ?></td>
                    <td class="col-nama"><strong><?php echo htmlspecialchars($rs['nama']); ?></strong></td>
                    <td><?php echo htmlspecialchars($rs['nama_kelas']); ?></td>

                    <?php foreach($daftarPertemuan as $p): 
                        $st = $rs['harian'][$p['id']] ?? '-';
                    ?>
                        <td style="<?php echo $st === 'H' ? 'font-weight:bold;' : ($st === 'A' ? 'background:#fee2e2;font-weight:bold;' : ''); ?>">
                            <?php echo $st !== '-' ? $st : ''; ?>
                        </td>
                    <?php endforeach; ?>

                    <td style="font-weight: bold;"><?php echo $rs['hadir']; ?></td>
                    <td><?php echo $rs['sakit'] ?: ''; ?></td>
                    <td><?php echo $rs['izin'] ?: ''; ?></td>
                    <td style="<?php echo $rs['alpa'] > 0 ? 'font-weight:bold;color:#b91c1c;' : ''; ?>">
                        <?php echo $rs['alpa'] ?: ''; ?>
                    </td>
                    <td style="font-weight: bold;">
                        <?php echo $rs['persentase']; ?>%
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="font-size: 7.5pt; margin-bottom: 10px; color: #334155;">
        <em>* Keterangan: H = Hadir, S = Sakit, I = Izin, A = Alpa (Tanpa Keterangan). Dokumen dicetak resmi dari Sistem Absen V2 MAS MIFTAHUL HUDA.</em>
    </div>

    <!-- TANDA TANGAN -->
    <table class="ttd-box">
        <tr>
            <td>
                Mengetahui,<br>
                Kepala Madrasah<br><br><br><br><br>
                <strong><u><?php echo htmlspecialchars($kamadNama); ?></u></strong><br>
                NIP. <?php echo htmlspecialchars($kamadNip); ?>
            </td>
            <td>
                Way Tenong, <?php echo $tgl_cetak; ?><br>
                Pembina Ekstrakurikuler,<br><br><br><br><br>
                <strong><u><?php echo htmlspecialchars($ekstraDetail['nama_pembina'] ?? '( ............................................ )'); ?></u></strong><br>
                NIP. <?php echo htmlspecialchars($ekstraDetail['nip_pembina'] ?? '-'); ?>
            </td>
        </tr>
    </table>
</div>

<script>
    lucide.createIcons();
</script>
</body>
</html>
