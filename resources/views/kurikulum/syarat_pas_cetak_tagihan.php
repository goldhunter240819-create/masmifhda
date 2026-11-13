<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lembar Tagihan Nilai Susulan Syarat PAS</title>
    <?php 
        $logoUrl = !empty($institusi['logo']) ? \App\Core\Helper::url('/uploads/logo/' . $institusi['logo']) : \App\Core\Helper::url('/assets/images/logo.png'); 
    ?>
    <link rel="icon" type="image/png" href="<?php echo $logoUrl; ?>">
    <style>
        @page {
            size: 21.59cm 33.02cm portrait; /* F4 Folio portrait */
            margin: 8mm 10mm 6mm 10mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10.5pt;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .slip-item {
            page-break-after: always;
            page-break-inside: avoid;
            margin-bottom: 20px;
        }
        .slip-item:last-child {
            page-break-after: auto;
        }

        /* Kop Surat Standard & Compact */
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 2px double #000;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }
        .kop-surat .logo {
            width: 52px;
            height: auto;
            margin-right: 12px;
        }
        .kop-surat .kop-text {
            flex: 1;
            text-align: center;
            padding-right: 64px;
        }
        .kop-surat .kop-text h2 {
            margin: 0;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .kop-surat .kop-text h1 {
            margin: 1px 0 0 0;
            font-size: 16.5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-surat .kop-text p {
            margin: 1px 0 0 0;
            font-size: 9.5px;
            line-height: 1.25;
        }

        .title {
            text-align: center;
            font-size: 12.5px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 4px 0 1px 0;
            text-decoration: underline;
        }
        .subtitle {
            text-align: center;
            font-size: 9.5px;
            margin-bottom: 6px;
            font-style: italic;
            color: #333;
        }

        .alert-box {
            background: #fff5f5;
            border: 1px solid #feb2b2;
            padding: 4px 8px;
            margin-bottom: 6px;
            font-size: 9px;
            line-height: 1.35;
            border-radius: 4px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 6px;
            font-size: 10px;
            line-height: 1.25;
        }
        .info-table td {
            padding: 1.5px 3px;
            vertical-align: top;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 9.5px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 3px 5px;
            vertical-align: middle;
        }
        .data-table th {
            text-align: center;
            font-weight: bold;
            background-color: #f2f2f2;
            font-size: 9.5px;
        }

        .signature-section {
            width: 100%;
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 210px;
            text-align: center;
        }
        .signature-space {
            height: 38px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="text-align: center; margin: 12px 0; background: #f8f9fa; padding: 10px; border: 1px solid #ddd;">
        <button onclick="window.print()" style="padding: 7px 18px; font-size: 13px; cursor: pointer; background: #dc2626; color: #fff; border: none; border-radius: 6px; font-weight: bold;">
            Cetak Lembar Tagihan
        </button>
        <button onclick="window.close()" style="padding: 7px 14px; font-size: 13px; cursor: pointer; background: #64748b; color: #fff; border: none; border-radius: 6px; margin-left: 8px;">
            Tutup
        </button>
        <p style="margin: 4px 0 0 0; font-size: 11px; color: #666;">
            Format cetak didesain pas <strong>1 lembar (Single Page)</strong> untuk tiap siswa.
        </p>
    </div>

    <?php if (empty($tagihan_siswa)): ?>
    <div style="text-align: center; padding: 50px 20px;">
        <h2 style="color: #059669;">Alhamdulillah! Semua Nilai Lengkap 100%</h2>
        <p>Tidak ada siswa di kelas ini yang memiliki tagihan nilai kosong untuk syarat mengikuti PAS.</p>
    </div>
    <?php else: ?>

    <?php 
        $alamat = $institusi['alamat'] ?? '';
        $desa = !empty($institusi['desa']) ? 'Ds. ' . $institusi['desa'] : '';
        $kecamatan = !empty($institusi['kecamatan']) ? 'Kec. ' . $institusi['kecamatan'] : '';
        $kota = !empty($institusi['kota']) ? 'Kab. ' . $institusi['kota'] : (!empty($institusi['kabupaten']) ? 'Kab. ' . $institusi['kabupaten'] : '');
        $provinsi = !empty($institusi['provinsi']) ? 'Prov. ' . $institusi['provinsi'] : '';
        $kodepos = !empty($institusi['kodepos']) ? 'KP. ' . $institusi['kodepos'] : '';
        
        $fullAddressArray = array_filter([$alamat, $desa, $kecamatan, $kota, $provinsi, $kodepos]);
        $fullAddress = implode(', ', $fullAddressArray);

        $kls = $pasData['kelas'];
    ?>

    <?php foreach ($tagihan_siswa as $s): 
        // Kelompokkan tagihan kosong per mata pelajaran agar tabel ringkas & pas 1 lembar
        $mapel_grouped = [];
        foreach ($s['tagihan_kosong'] as $tk) {
            $mKey = $tk['mapel'];
            if (!isset($mapel_grouped[$mKey])) {
                $mapel_grouped[$mKey] = [
                    'mapel' => $tk['mapel'],
                    'guru' => $tk['guru'],
                    'komponen' => []
                ];
            }
            $mapel_grouped[$mKey]['komponen'][] = $tk['jenis'];
        }
    ?>
    <div class="slip-item">
        <!-- KOP SURAT -->
        <div class="kop-surat">
            <img src="<?= $logoUrl ?>" alt="Logo" class="logo">
            <div class="kop-text">
                <h2><?= htmlspecialchars($institusi['yayasan'] ?? '') ?></h2>
                <h1><?= htmlspecialchars($institusi['nama'] ?? '') ?></h1>
                <p><?= htmlspecialchars($fullAddress ?: 'Jl. Raya Pendidikan No. 123') ?></p>
                <p>Website: <?= htmlspecialchars($institusi['website'] ?? '-') ?> | Email: <?= htmlspecialchars($institusi['email'] ?? '-') ?></p>
            </div>
        </div>

        <div class="title">LEMBAR KENDALI TAGIHAN NILAI SUSULAN SISWA</div>
        <div class="subtitle">Syarat Wajib Kelayakan Mengikuti Penilaian Akhir Semester (PAS)</div>

        <div class="alert-box">
            <strong>Pemberitahuan:</strong> Siswa di bawah ini <strong>BELUM DIPERBOLEHKAN</strong> mengikuti Penilaian Akhir Semester (PAS) karena terdapat <strong><?= $s['total_kosong'] ?> komponen nilai</strong> yang belum terisi (kelengkapan baru <?= $s['persen'] ?>% dari syarat 100%). Segera selesaikan tugas/ujian susulan kepada guru pengampu terkait sebelum pelaksanaan PAS.
        </div>

        <table class="info-table">
            <tr>
                <td style="width: 15%;">Nama Lengkap</td>
                <td style="width: 2%;">:</td>
                <td style="width: 38%; font-weight: bold; font-size: 11px;"><?= htmlspecialchars($s['nama']) ?></td>

                <td style="width: 15%;">Kelas / Semester</td>
                <td style="width: 2%;">:</td>
                <td style="width: 28%; font-weight: bold;"><?= htmlspecialchars($kls['nama_kelas']) ?> / <?= htmlspecialchars($semester) ?></td>
            </tr>
            <tr>
                <td>NIS</td>
                <td>:</td>
                <td><?= htmlspecialchars($s['nis']) ?></td>

                <td>Tahun Ajaran</td>
                <td>:</td>
                <td><?= htmlspecialchars($activeYear['name'] ?? $tahun_ajaran_id) ?></td>
            </tr>
            <tr>
                <td>Wali Kelas</td>
                <td>:</td>
                <td><?= htmlspecialchars($kls['wali_kelas']) ?></td>

                <td>Status PAS</td>
                <td>:</td>
                <td style="color: #b91c1c; font-weight: bold;">TIDAK DIPERBOLEHKAN (Tertahan)</td>
            </tr>
        </table>

        <!-- Tabel Tagihan Mapel (Dikelompokkan Per Mapel agar Ringkas) -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 28px;">No</th>
                    <th>Mata Pelajaran</th>
                    <th style="width: 130px;">Komponen Tagihan Kosong</th>
                    <th>Guru Pengampu</th>
                    <th style="width: 90px;">Status</th>
                    <th style="width: 130px;">Paraf Guru Pengampu</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($mapel_grouped as $mg): ?>
                <tr>
                    <td style="text-align: center;"><?= $no++ ?></td>
                    <td><strong><?= htmlspecialchars($mg['mapel']) ?></strong></td>
                    <td style="text-align: center; font-weight: bold; color: #b91c1c;">
                        <?= htmlspecialchars(implode(', ', $mg['komponen'])) ?>
                    </td>
                    <td><?= htmlspecialchars($mg['guru']) ?></td>
                    <td style="text-align: center; color: #b91c1c; font-weight: bold; font-size: 9px;">BELUM ADA NILAI</td>
                    <td style="height: 24px; vertical-align: bottom; text-align: center; font-size: 8.5px; color: #777;">
                        ( .................................... )
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="font-size: 8.5px; font-style: italic; color: #555; margin-top: 3px;">
            * Lembar kendali ini wajib diserahkan kembali ke Waka Kurikulum / Panitia PAS setelah seluruh guru pengampu membubuhkan paraf pengesahan nilai susulan.
        </div>

        <!-- Tanda Tangan -->
        <div class="signature-section">
            <div class="signature-box">
                Mengetahui Orang Tua / Wali,<br>
                <div class="signature-space"></div>
                ( .................................................... )
            </div>

            <div class="signature-box">
                Siswa yang bersangkutan,<br>
                <div class="signature-space"></div>
                <strong><?= htmlspecialchars($s['nama']) ?></strong>
            </div>

            <div class="signature-box">
                <?= htmlspecialchars($institusi['kota'] ?? 'Madrasah') ?>, <?= date('d/m/Y') ?><br>
                Wali Kelas <?= htmlspecialchars($kls['nama_kelas']) ?>
                <div class="signature-space"></div>
                <strong><?= htmlspecialchars($kls['wali_kelas']) ?></strong>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <?php endif; ?>

</body>
</html>
