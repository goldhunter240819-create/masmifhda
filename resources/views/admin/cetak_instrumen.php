<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Instrumen PKKM - <?php echo htmlspecialchars($identitas['nama_madrasah']); ?></title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1.2cm 1cm;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
            font-size: 11pt;
            line-height: 1.35;
        }

        .no-print {
            background: #f1f5f9;
            border-bottom: 1px solid #cbd5e1;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: Arial, sans-serif;
            font-size: 13px;
        }
        .btn-print {
            background: #0284c7;
            color: white;
            padding: 6px 16px;
            border-radius: 6px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-back {
            background: #64748b;
            color: white;
            padding: 6px 14px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
            tr {
                page-break-inside: avoid;
            }
        }

        .title-block {
            text-align: center;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        .title-block h3 {
            margin: 0 0 4px 0;
            font-size: 12pt;
            letter-spacing: 0.5px;
        }
        .title-block h2 {
            margin: 0;
            font-size: 13pt;
            letter-spacing: 0.5px;
        }

        .petunjuk-list {
            margin: 0 0 1rem 0;
            padding-left: 20px;
            font-size: 10pt;
            text-align: justify;
        }
        .petunjuk-list li {
            margin-bottom: 3px;
        }

        .identitas-table {
            width: 100%;
            margin-bottom: 1rem;
            font-size: 10pt;
            border-collapse: collapse;
        }
        .identitas-table td {
            padding: 2px 4px;
            vertical-align: top;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
        }
        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }
        .main-table th {
            text-align: center;
            font-weight: bold;
            background: #f4f4f4;
        }
        .komponen-row {
            background: #e5e5e5 !important;
            font-weight: bold;
            font-size: 10pt;
        }

        .check-box {
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            vertical-align: middle !important;
        }

        .ttd-container {
            width: 100%;
            margin-top: 2rem;
            display: flex;
            justify-content: space-between;
            font-size: 10.5pt;
            page-break-inside: avoid;
        }
        .ttd-box {
            width: 40%;
            text-align: left;
        }
    </style>
</head>
<body>

    <div class="no-print">
        <div>
            <strong>Mode Pratinjau Cetak Instrumen PKKM</strong> &bull; Ukuran Kertas: A4 Landscape
        </div>
        <div style="display:flex; gap:8px;">
            <button onclick="window.print()" class="btn-print">&#128438; Cetak Sekarang</button>
            <button onclick="window.close()" class="btn-back">Tutup</button>
        </div>
    </div>

    <div style="padding: 10px 15px;">
        <div class="title-block">
            <h3>PETUNJUK PENGISIAN INSTRUMEN</h3>
            <h2>PENILAIAN KINERJA TAHUN 1, 2 DAN 3 KEPALA MADRASAH</h2>
        </div>

        <ol class="petunjuk-list">
            <li>Anda diminta menilai kinerja kepala madrasah dari aspek pelaksanaan tugas pokok, komitmen dalam melaksanakan tugas dan hasil kerja yang tercermin pada Indikator Kerja dan Data Otentik yang ada.</li>
            <li>Setiap indikator yang dinilai dinyatakan dalam pernyataan/pertanyaan yang disertai dengan bukti fisik yang diharapkan yang menggambarkan perilaku kepala madrasah dalam melaksanakan tugas pokoknya.</li>
            <li>Anda diminta menentukan salah satu angka yang ada pada kolom Hasil Kerja (1, 2, 3, atau 4) dengan cara memberikan tanda <strong>ceklis (v)</strong> setelah menilai bukti otentik yang menjadi dasar penilaian perilaku kepala madrasah.</li>
            <li>Pemberian skor indikator kinerja untuk setiap unsur tugas utama dilakukan dengan ketentuan sebagai berikut:
                <br><strong>Skor 4</strong> diberikan apabila kepala madrasah mampu menunjukkan bukti-bukti yang <strong>LENGKAP dan SANGAT MEYAKINKAN</strong> bahwa kepala madrasah yang bersangkutan berkinerja sesuai dengan masing-masing kriteria komponen yang dinilai.
                <br><strong>Skor 3</strong> diberikan apabila kepala madrasah mampu menunjukkan bukti-bukti yang <strong>LENGKAP dan CUKUP MEYAKINKAN</strong> bahwa kepala madrasah yang bersangkutan berkinerja sesuai dengan masing-masing kriteria komponen yang dinilai.
                <br><strong>Skor 2</strong> diberikan apabila kepala madrasah mampu menunjukkan bukti-bukti yang <strong>KURANG LENGKAP dan CUKUP MEYAKINKAN</strong> bahwa kepala madrasah yang bersangkutan berkinerja sesuai dengan masing-masing kriteria komponen yang dinilai.
                <br><strong>Skor 1</strong> diberikan apabila ditemukan bukti-bukti yang <strong>SANGAT TERBATAS dan KURANG MEYAKINKAN atau TIDAK DITEMUKAN</strong> bahwa kepala madrasah yang bersangkutan berkinerja sesuai dengan masing-masing kriteria komponen yang dinilai.
            </li>
        </ol>

        <table class="identitas-table">
            <tr>
                <td style="width:130px;">Nama Kamad</td>
                <td style="width:10px;">:</td>
                <td style="width:38%;"><?php echo htmlspecialchars($identitas['nama_kamad']); ?></td>
                <td style="width:130px;">Nama Penilai</td>
                <td style="width:10px;">:</td>
                <td><?php echo htmlspecialchars($identitas['nama_penilai']); ?></td>
            </tr>
            <tr>
                <td>N I P</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($identitas['nip_kamad'] ?: '-'); ?></td>
                <td>N I P</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($identitas['nip_penilai'] ?: '-'); ?></td>
            </tr>
            <tr>
                <td>Nama Madrasah</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($identitas['nama_madrasah']); ?></td>
                <td>Jabatan Penilai</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($identitas['jabatan_penilai']); ?></td>
            </tr>
            <tr>
                <td>Alamat Madrasah</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($identitas['alamat_madrasah']); ?></td>
                <td>Tgl Penilaian</td>
                <td>:</td>
                <td><?php echo date('d F Y', strtotime($identitas['tgl_penilaian'])); ?></td>
            </tr>
        </table>

        <table class="main-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width:30px;">No</th>
                    <th rowspan="2" style="width:18%;">Unsur Tugas Utama</th>
                    <th rowspan="2" style="width:23%;">Indikator</th>
                    <th rowspan="2" style="width:28%;">Data yang Diharapkan</th>
                    <th rowspan="2" style="width:13%;">Penggalian Data</th>
                    <th colspan="4" style="width:90px;">Hasil Kinerja</th>
                    <th rowspan="2" style="width:45px;">Skor Akhir</th>
                </tr>
                <tr>
                    <th style="width:22px;">1</th>
                    <th style="width:22px;">2</th>
                    <th style="width:22px;">3</th>
                    <th style="width:22px;">4</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($groupedKomponen as $kompId => $kData): ?>
                    <tr class="komponen-row">
                        <td colspan="10" style="padding: 6px 8px;">
                            <?php echo htmlspecialchars($kData['nama_komponen']); ?>
                        </td>
                    </tr>

                    <?php 
                    $uNo = 1;
                    foreach ($kData['sub'] as $subId => $sData): 
                        $rowCount = count($sData['items']);
                        $firstRow = true;
                        foreach ($sData['items'] as $item):
                            $currSkor = (int)($item['skor'] ?? 0);
                    ?>
                    <tr>
                        <?php if ($firstRow): ?>
                            <td rowspan="<?php echo $rowCount; ?>" style="text-align:center; vertical-align:middle; font-weight:bold;">
                                <?php echo $sData['sub_urutan'] ?: $uNo; ?>
                            </td>
                            <td rowspan="<?php echo $rowCount; ?>" style="vertical-align:middle; font-weight:bold;">
                                <?php echo htmlspecialchars($sData['nama_sub']); ?>
                            </td>
                        <?php endif; ?>

                        <td>
                            <strong><?php echo htmlspecialchars($item['kode']); ?></strong> &nbsp;
                            <?php echo htmlspecialchars($item['indikator']); ?>
                        </td>

                        <td style="white-space:pre-line;">
                            <?php echo htmlspecialchars($item['bukti_fisik']); ?>
                        </td>

                        <td>
                            <?php 
                            $tags = explode(',', $item['penggalian_data']);
                            foreach ($tags as $t) {
                                echo "&bull; " . htmlspecialchars(trim($t)) . "<br>";
                            }
                            ?>
                        </td>

                        <td class="check-box"><?php echo $currSkor === 1 ? 'v' : ''; ?></td>
                        <td class="check-box"><?php echo $currSkor === 2 ? 'v' : ''; ?></td>
                        <td class="check-box"><?php echo $currSkor === 3 ? 'v' : ''; ?></td>
                        <td class="check-box"><?php echo $currSkor === 4 ? 'v' : ''; ?></td>

                        <td style="text-align:center; font-weight:bold; vertical-align:middle;">
                            <?php echo $currSkor > 0 ? $currSkor : '0'; ?>
                        </td>
                    </tr>
                    <?php 
                        $firstRow = false;
                        endforeach; 
                        $uNo++;
                    endforeach; 
                    ?>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- TANDA TANGAN -->
        <div class="ttd-container">
            <div class="ttd-box">
                <br>
                Kepala Madrasah yang dinilai,
                <br><br><br><br><br>
                <strong><u><?php echo htmlspecialchars($identitas['nama_kamad']); ?></u></strong><br>
                NIP. <?php echo htmlspecialchars($identitas['nip_kamad'] ?: '-'); ?>
            </div>

            <div class="ttd-box" style="text-align:left; margin-left:auto;">
                <?php echo htmlspecialchars($identitas['tempat_penilaian']); ?>, <?php echo date('d F Y', strtotime($identitas['tgl_penilaian'])); ?><br>
                Penilai,
                <br><br><br><br><br>
                <strong><u><?php echo htmlspecialchars($identitas['nama_penilai']); ?></u></strong><br>
                NIP. <?php echo htmlspecialchars($identitas['nip_penilai'] ?: '-'); ?>
            </div>
        </div>
    </div>

</body>
</html>
