<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Nilai Siswa</title>
    <?php 
        $logoUrl = !empty($institusi['logo']) ? \App\Core\Helper::url('/uploads/logo/' . $institusi['logo']) : \App\Core\Helper::url('/assets/images/logo.png'); 
    ?>
    <link rel="icon" type="image/png" href="<?php echo $logoUrl; ?>">
    <style>
        @page {
            size: 21.59cm 33.02cm portrait; /* F4 size portrait */
            margin: 1cm; /* standard margin for portrait */
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 13px;
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
        
        /* Kop Surat */
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }
        .kop-surat .logo {
            width: 60px;
            height: auto;
            margin-right: 15px;
        }
        .kop-surat .kop-text {
            flex: 1;
            text-align: center;
            padding-right: 110px; /* offset for logo to keep text centered */
        }
        .kop-surat .kop-text h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .kop-surat .kop-text h2 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-surat .kop-text p {
            margin: 3px 0 0 0;
            font-size: 13px;
        }

        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 10px 0;
            text-decoration: underline;
        }

        .info-table {
            width: 100%;
            margin-bottom: 5px;
            font-size: 11px;
        }
        .info-table td {
            padding: 1px 3px;
            vertical-align: top;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 11px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 2px 4px;
            vertical-align: middle;
        }
        .data-table th {
            text-align: center;
            font-weight: bold;
            background-color: #f2f2f2;
        }

        .signature-section {
            width: 100%;
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
        }
        .signature-box {
            width: 300px;
            text-align: center;
        }
        .signature-space {
            height: 50px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body onload="window.print()">
    
    <div class="no-print" style="text-align: center; margin: 20px 0; background: #f8f9fa; padding: 15px; border: 1px solid #ddd;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer; background: #0d6efd; color: #fff; border: none; border-radius: 5px;">Cetak Dokumen</button>
        <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">Gunakan pengaturan kertas F4 (Folio) / Legal dan nonaktifkan Header & Footer pada pengaturan printer Anda.</p>
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
                <h2 style="font-size: <?= intval(($institusi['kop_font_yayasan'] ?? 14) * 0.8) ?>px;"><?= htmlspecialchars($institusi['yayasan'] ?? '') ?></h2>
                <h1 style="font-size: <?= intval(($institusi['kop_font_nama'] ?? 24) * 0.75) ?>px;"><?= htmlspecialchars($institusi['nama'] ?? '') ?></h1>
                <p style="font-size: 11px; margin-top:2px;"><?= htmlspecialchars($fullAddress ?: 'Jl. Raya Pendidikan No. 123') ?></p>
                <p style="font-size: 11px; margin-top:1px;">Website: <?= htmlspecialchars($institusi['website'] ?? '-') ?> | Email: <?= htmlspecialchars($institusi['email'] ?? '-') ?></p>
            </div>
        </div>

        <div class="title">DAFTAR NILAI SISWA</div>

        <table class="info-table">
            <tr>
                <td style="width: 15%;">Mata Pelajaran</td>
                <td style="width: 2%;">:</td>
                <td style="width: 33%; font-weight: bold;"><?= htmlspecialchars($mapel_info['nama_mapel']) ?></td>
                
                <td style="width: 15%;">Tahun Ajaran</td>
                <td style="width: 2%;">:</td>
                <td style="width: 33%;"><?= htmlspecialchars($activeYear['name'] ?? $institusi['tahun_ajaran_id']) ?></td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>:</td>
                <td style="font-weight: bold;"><?= htmlspecialchars($kelas['nama_kelas']) ?></td>
                
                <td>Semester</td>
                <td>:</td>
                <td><?= htmlspecialchars($semester) ?></td>
            </tr>
            <tr>
                <td>Guru Pengampu</td>
                <td>:</td>
                <td><?= htmlspecialchars($mapel_info['guru_pengampu']) ?></td>
                
                <td>Wali Kelas</td>
                <td>:</td>
                <td><?= htmlspecialchars($kelas['wali_kelas'] ?: '-') ?></td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 40px;" rowspan="2">No</th>
                    <th style="width: 80px;" rowspan="2">NIS</th>
                    <th rowspan="2">Nama Lengkap Siswa</th>
                    <th colspan="<?= count($ph_list) ?>">Penilaian Harian (PH)</th>
                    <th colspan="2">Evaluasi Sumatif</th>
                    <th rowspan="2" style="width: 70px;">Nilai Rapor</th>
                </tr>
                <tr>
                    <?php foreach($ph_list as $ph): ?>
                        <th style="width: 60px;"><?= htmlspecialchars(str_replace('PH ', 'PH', $ph)) ?></th>
                    <?php endforeach; ?>
                    <th style="width: 60px;">PTS</th>
                    <th style="width: 60px;">PAS</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($siswa_list)): ?>
                <tr>
                    <td colspan="<?= 4 + count($ph_list) + 2 ?>" style="text-align: center; height: 50px;">Tidak ada data siswa aktif.</td>
                </tr>
                <?php else: ?>
                    <?php foreach($siswa_list as $idx => $s): ?>
                    <tr>
                        <td style="text-align: center;"><?= $idx + 1 ?></td>
                        <td style="text-align: center;"><?= htmlspecialchars($s['nis']) ?></td>
                        <td><?= htmlspecialchars($s['nama']) ?></td>
                        
                        <?php foreach ($ph_list as $ph): ?>
                            <?php 
                                $val = isset($nilai_grouped[$s['id']][$ph]) ? $nilai_grouped[$s['id']][$ph] : '';
                                $is_red = ($val !== '' && (float)$val < (float)$kkm) ? 'color: red;' : '';
                            ?>
                            <td style="text-align: center; font-weight: bold; <?= $is_red ?>"><?= $val !== '' ? $val : '-' ?></td>
                        <?php endforeach; ?>
                        
                        <?php 
                            $pts = isset($nilai_grouped[$s['id']]['PTS']) ? $nilai_grouped[$s['id']]['PTS'] : '';
                            $pas = isset($nilai_grouped[$s['id']]['PAS']) ? $nilai_grouped[$s['id']]['PAS'] : '';
                            $rapor = isset($nilai_grouped[$s['id']]['RAPOR']) ? $nilai_grouped[$s['id']]['RAPOR'] : '';

                            if ($rapor === '') {
                                $sum_ph = 0;
                                $count_komponen = count($ph_list) + 1;
                                foreach ($ph_list as $ph) {
                                    $val = isset($nilai_grouped[$s['id']][$ph]) ? $nilai_grouped[$s['id']][$ph] : '';
                                    if ($val !== '') {
                                        $sum_ph += (float)$val;
                                    }
                                }
                                if ($pts !== '') {
                                    $sum_ph += (float)$pts;
                                }
                                
                                $bobotHarian = (float)($institusi['bobot_harian'] ?? 50) / 100;
                                $bobotPas = (float)($institusi['bobot_pas'] ?? 50) / 100;

                                $rata_rata = $sum_ph / $count_komponen;
                                $pas_val = $pas !== '' ? (float)$pas : 0;
                                $rapor = round(($rata_rata * $bobotHarian) + ($pas_val * $bobotPas));
                            }
                            
                            $is_pts_red = ($pts !== '' && (float)$pts < (float)$kkm) ? 'color: red;' : '';
                            $is_pas_red = ($pas !== '' && (float)$pas < (float)$kkm) ? 'color: red;' : '';
                            $is_rapor_red = ($rapor !== '' && (float)$rapor < (float)$kkm) ? 'color: red;' : '';
                        ?>
                        <td style="text-align: center; font-weight: bold; <?= $is_pts_red ?>"><?= $pts !== '' ? $pts : '-' ?></td>
                        <td style="text-align: center; font-weight: bold; <?= $is_pas_red ?>"><?= $pas !== '' ? $pas : '-' ?></td>
                        <td style="text-align: center; font-weight: bold; <?= $is_rapor_red ?>"><?= $rapor !== '' ? $rapor : '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div style="margin-bottom: 20px;">
            <p style="font-weight: bold; margin-bottom: 5px; font-size: 11px;">Keterangan Materi:</p>
            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                <?php foreach($ph_list as $ph): ?>
                <tr>
                    <td style="width: 60px; font-weight: bold; border: 1px solid #000; padding: 2px 4px; text-align: center;"><?= htmlspecialchars(str_replace('PH ', 'PH', $ph)) ?></td>
                    <td style="border: 1px solid #000; padding: 2px 4px; <?= empty($ph_materi[$ph]) ? 'font-style: italic; color: #666;' : '' ?>">
                        <?= !empty($ph_materi[$ph]) ? htmlspecialchars($ph_materi[$ph]) : '-' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <p>Mengetahui,<br>Kepala Madrasah</p>
                <div class="signature-space"></div>
                <p style="font-weight: bold; text-decoration: underline; margin-bottom: 0;"><?= htmlspecialchars($institusi['nama_kepala']) ?></p>
                <p style="margin-top: 3px;">&nbsp;</p>
            </div>
            
            <div class="signature-box">
                <p><?= htmlspecialchars($institusi['kota']) ?>, ..................................<br>Guru Mata Pelajaran</p>
                <div class="signature-space"></div>
                <p style="font-weight: bold; text-decoration: underline; margin-bottom: 0;"><?= htmlspecialchars($mapel_info['guru_pengampu']) ?></p>
                <p style="margin-top: 3px;">&nbsp;</p>
            </div>
        </div>
    </div>
</body>
</html>
