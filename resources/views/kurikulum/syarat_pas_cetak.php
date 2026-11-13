<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Verifikasi Syarat Mengikuti PAS - Kelas <?= htmlspecialchars($pasData['kelas']['nama_kelas'] ?? '') ?></title>
    <?php 
        $logoUrl = !empty($institusi['logo']) ? \App\Core\Helper::url('/uploads/logo/' . $institusi['logo']) : \App\Core\Helper::url('/assets/images/logo.png'); 
    ?>
    <link rel="icon" type="image/png" href="<?php echo $logoUrl; ?>">
    <style>
        @page {
            size: 21.59cm 33.02cm portrait; /* F4 Folio portrait */
            margin: 8mm 10mm 6mm 10mm;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }

        /* Kop Surat Standard */
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 2px double #000;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }
        .kop-surat .logo {
            width: 55px;
            height: auto;
            margin-right: 12px;
        }
        .kop-surat .kop-text {
            flex: 1;
            text-align: center;
            padding-right: 67px;
        }
        .kop-surat .kop-text h2 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .kop-surat .kop-text h1 {
            margin: 1px 0 0 0;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-surat .kop-text p {
            margin: 2px 0 0 0;
            font-size: 9.5px;
            line-height: 1.25;
        }

        .title {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 6px 0 2px 0;
            text-decoration: underline;
        }
        .subtitle {
            text-align: center;
            font-size: 10px;
            margin-bottom: 8px;
            font-style: italic;
        }

        .info-table {
            width: 100%;
            margin-bottom: 8px;
            font-size: 10.5px;
            line-height: 1.25;
        }
        .info-table td {
            padding: 1.5px 3px;
            vertical-align: top;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10px;
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
        }

        .status-layak {
            font-weight: bold;
            color: #047857;
        }
        .status-tidak {
            font-weight: bold;
            color: #b91c1c;
        }

        .summary-box {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
            font-size: 10px;
            border: 1px dashed #666;
            padding: 5px 10px;
            background: #fafafa;
        }

        .signature-section {
            width: 100%;
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            font-size: 10.5px;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 200px;
            text-align: center;
        }
        .signature-space {
            height: 45px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="text-align: center; margin: 15px 0; background: #f8f9fa; padding: 12px; border: 1px solid #ddd;">
        <button onclick="window.print()" style="padding: 8px 20px; font-size: 14px; cursor: pointer; background: #0284c7; color: #fff; border: none; border-radius: 6px; font-weight: bold;">
            Cetak Dokumen
        </button>
        <button onclick="window.close()" style="padding: 8px 15px; font-size: 14px; cursor: pointer; background: #64748b; color: #fff; border: none; border-radius: 6px; margin-left: 8px;">
            Tutup
        </button>
        <p style="margin: 5px 0 0 0; font-size: 11px; color: #666;">Gunakan pengaturan kertas F4 (Folio) atau Legal Portrait dan nonaktifkan opsi Header & Footer browser.</p>
    </div>

    <div class="container">
        <!-- KOP SURAT -->
        <div class="kop-surat">
            <?php 
                $alamat = $institusi['alamat'] ?? '';
                $desa = !empty($institusi['desa']) ? 'Ds. ' . $institusi['desa'] : '';
                $kecamatan = !empty($institusi['kecamatan']) ? 'Kec. ' . $institusi['kecamatan'] : '';
                $kota = !empty($institusi['kota']) ? 'Kab. ' . $institusi['kota'] : (!empty($institusi['kabupaten']) ? 'Kab. ' . $institusi['kabupaten'] : '');
                $provinsi = !empty($institusi['provinsi']) ? 'Prov. ' . $institusi['provinsi'] : '';
                $kodepos = !empty($institusi['kodepos']) ? 'KP. ' . $institusi['kodepos'] : '';
                
                $fullAddressArray = array_filter([$alamat, $desa, $kecamatan, $kota, $provinsi, $kodepos]);
                $fullAddress = implode(', ', $fullAddressArray);
            ?>
            <img src="<?= $logoUrl ?>" alt="Logo" class="logo">
            <div class="kop-text">
                <h2><?= htmlspecialchars($institusi['yayasan'] ?? '') ?></h2>
                <h1><?= htmlspecialchars($institusi['nama'] ?? '') ?></h1>
                <p><?= htmlspecialchars($fullAddress ?: 'Jl. Raya Pendidikan No. 123') ?></p>
                <p>Website: <?= htmlspecialchars($institusi['website'] ?? '-') ?> | Email: <?= htmlspecialchars($institusi['email'] ?? '-') ?></p>
            </div>
        </div>

        <div class="title">REKAPITULASI VERIFIKASI KELAYAKAN PESERTA PAS</div>
        <div class="subtitle">Syarat Mengikuti Penilaian Akhir Semester (PAS): Kelengkapan Nilai PH + PTS Wajib 100%</div>

        <?php 
            $kls = $pasData['kelas'];
            $st = $pasData['stat'];
        ?>
        <table class="info-table">
            <tr>
                <td style="width: 14%;">Kelas</td>
                <td style="width: 2%;">:</td>
                <td style="width: 34%; font-weight: bold;"><?= htmlspecialchars($kls['nama_kelas']) ?></td>

                <td style="width: 16%;">Tahun Ajaran</td>
                <td style="width: 2%;">:</td>
                <td style="width: 32%;"><?= htmlspecialchars($activeYear['name'] ?? $tahun_ajaran_id) ?></td>
            </tr>
            <tr>
                <td>Wali Kelas</td>
                <td>:</td>
                <td><?= htmlspecialchars($kls['wali_kelas']) ?></td>

                <td>Semester</td>
                <td>:</td>
                <td><?= htmlspecialchars($semester) ?></td>
            </tr>
        </table>

        <!-- Summary -->
        <div class="summary-box">
            <div>Total Siswa: <strong><?= $st['total_siswa'] ?> Orang</strong></div>
            <div>&bull;</div>
            <div>Memenuhi Syarat (Boleh Ikut PAS): <strong style="color: #047857;"><?= $st['layak'] ?> Orang (<?= $st['total_siswa'] > 0 ? round(($st['layak']/$st['total_siswa'])*100) : 0 ?>%)</strong></div>
            <div>&bull;</div>
            <div>Belum Memenuhi Syarat (Tertahan): <strong style="color: #b91c1c;"><?= $st['tidak_layak'] ?> Orang (<?= $st['total_siswa'] > 0 ? round(($st['tidak_layak']/$st['total_siswa'])*100) : 0 ?>%)</strong></div>
        </div>

        <!-- Tabel Siswa -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th style="width: 75px;">NIS</th>
                    <th>Nama Lengkap Siswa</th>
                    <th style="width: 35px;">L/P</th>
                    <th style="width: 70px;">Tagihan</th>
                    <th style="width: 70px;">Terisi</th>
                    <th style="width: 60px;">Kelengkapan</th>
                    <th style="width: 140px;">Status Syarat PAS</th>
                    <th style="width: 180px;">Rincian Mapel Kosong</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($pasData['siswa_data'] as $s): ?>
                <tr>
                    <td style="text-align: center;"><?= $no++ ?></td>
                    <td style="text-align: center; font-family: monospace;"><?= htmlspecialchars($s['nis']) ?></td>
                    <td><strong><?= htmlspecialchars($s['nama']) ?></strong></td>
                    <td style="text-align: center;"><?= htmlspecialchars($s['jk']) ?></td>
                    <td style="text-align: center;"><?= $s['total_tagihan'] ?> Nilai</td>
                    <td style="text-align: center;"><?= $s['total_terisi'] ?> Nilai</td>
                    <td style="text-align: center; font-weight: bold;"><?= $s['persen'] ?>%</td>
                    <td style="text-align: center;" class="<?= $s['is_layak'] ? 'status-layak' : 'status-tidak' ?>">
                        <?= $s['is_layak'] ? 'MEMENUHI SYARAT' : 'TIDAK DIPERBOLEHKAN' ?>
                    </td>
                    <td style="font-size: 10px;">
                        <?php if ($s['is_layak']): ?>
                            <span style="color: #047857;">Lengkap 100%</span>
                        <?php else: ?>
                            <?php 
                                $txt = [];
                                foreach ($s['tagihan_kosong'] as $tk) {
                                    $txt[] = $tk['mapel'] . ' (' . $tk['jenis'] . ')';
                                }
                                echo '<span style="color: #b91c1c;">' . htmlspecialchars(implode(', ', $txt)) . '</span>';
                            ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                Mengetahui,<br>
                Kepala Madrasah
                <div class="signature-space"></div>
                <strong><?= htmlspecialchars($institusi['kepala_madrasah'] ?? 'Kepala Madrasah') ?></strong><br>
                NIP. <?= htmlspecialchars($institusi['nip_kepala_madrasah'] ?? '-') ?>
            </div>

            <div class="signature-box">
                Waka Kurikulum
                <div class="signature-space"></div>
                <strong><?= htmlspecialchars($institusi['waka_kurikulum'] ?? 'Waka Kurikulum') ?></strong><br>
                NIP. -
            </div>

            <div class="signature-box">
                <?= htmlspecialchars($institusi['kota'] ?? 'Madrasah') ?>, <?= date('d/m/Y') ?><br>
                Wali Kelas
                <div class="signature-space"></div>
                <strong><?= htmlspecialchars($kls['wali_kelas']) ?></strong><br>
                NIP. -
            </div>
        </div>
    </div>

</body>
</html>
