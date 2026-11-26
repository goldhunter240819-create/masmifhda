<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Rekap Nilai - <?php echo htmlspecialchars($inst['nama'] ?? 'Madrasah'); ?></title>
    <?php if (!empty($inst['logo'])): ?>
    <link rel="icon" href="<?php echo base_url('public/uploads/logo/' . $inst['logo']); ?>" type="image/x-icon">
    <?php endif; ?>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; color: #000; margin: 0; padding: 20px; }
        .kop-surat { display: flex; align-items: center; justify-content: center; border-bottom: 3px solid #000; padding-bottom: 8px; margin-bottom: 14px; position: relative; }
        .kop-surat img { width: 70px; height: 70px; object-fit: contain; position: absolute; left: 0; }
        .kop-text { text-align: center; flex: 1; color: #000; }
        .kop-text h1 { margin: 0; font-size: <?php echo htmlspecialchars($inst['ukuran_font_yayasan'] ?? 15); ?>px; font-weight: bold; text-transform: uppercase; line-height: 1.2; }
        .kop-text h2 { margin: 2px 0; font-size: <?php echo htmlspecialchars($inst['ukuran_font_madrasah'] ?? 17); ?>px; font-weight: bold; text-transform: uppercase; line-height: 1.2; }
        .kop-text p { margin: 0; font-size: 10px; line-height: 1.35; }
        .title { text-align: center; font-weight: bold; margin-bottom: 10px; font-size: 12pt; text-transform: uppercase; }
        .info-table { width: 100%; margin-bottom: 10px; font-size: 10pt; }
        .info-table td { padding: 2px 0; vertical-align: top; }
        .data-table { width: 100%; border-collapse: collapse; font-size: 10pt; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 5px 6px; }
        .data-table th { text-align: center; font-weight: bold; background: #f3f4f6; }
        .signature-table { width: 100%; margin-top: 24px; font-size: 10pt; border: none; }
        .signature-table td { border: none; text-align: center; vertical-align: top; width: 50%; }
        .btn-print { display: block; margin: 0 auto 14px auto; padding: 9px 20px; background: #0284c7; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: bold; }
        @media print {
            .no-print { display: none !important; }
            @page { size: landscape; margin: 10mm; }
            body { padding: 0; font-size: 9pt; }
            .data-table { font-size: 8pt; }
            .data-table th, .data-table td { padding: 2px 4px; }
            .kop-surat img { width: 58px; height: 58px; }
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="btn-print no-print">Cetak Rekap Nilai</button>

    <div class="kop-surat">
        <?php if (!empty($inst['logo'])): ?>
            <img src="<?php echo base_url('public/uploads/logo/' . $inst['logo']); ?>" alt="Logo">
        <?php endif; ?>
        <div class="kop-text">
            <?php if (!empty($inst['nama_yayasan'])): ?>
            <h1><?php echo htmlspecialchars($inst['nama_yayasan']); ?></h1>
            <?php endif; ?>
            <h2><?php echo htmlspecialchars($inst['nama'] ?? 'MA MIFTAHUL HUDA'); ?></h2>
            <?php
                $alamatParts = [];
                if (!empty($inst['alamat'])) $alamatParts[] = $inst['alamat'];
                if (!empty($inst['desa'])) $alamatParts[] = 'Ds. ' . $inst['desa'];
                if (!empty($inst['kecamatan'])) $alamatParts[] = 'Kec. ' . $inst['kecamatan'];
                if (!empty($inst['kota'])) $alamatParts[] = $inst['kota'];
                if (!empty($inst['provinsi'])) $alamatParts[] = 'Prov. ' . $inst['provinsi'];
                if (!empty($inst['kodepos'])) $alamatParts[] = 'KP. ' . $inst['kodepos'];
            ?>
            <?php if (!empty($alamatParts)): ?><p><?php echo htmlspecialchars(implode(', ', $alamatParts)); ?></p><?php endif; ?>
            <p>Website: <?php echo htmlspecialchars($inst['website'] ?? '-'); ?> | Email: <?php echo htmlspecialchars($inst['email'] ?? '-'); ?></p>
        </div>
    </div>

    <div class="title">REKAPITULASI NILAI SISWA</div>

    <table class="info-table">
        <tr>
            <td style="width: 130px;">Mata Pelajaran</td><td style="width: 10px;">:</td><td><strong><?php echo htmlspecialchars($mapel); ?></strong></td>
            <td style="width: 120px;">Tahun Ajaran</td><td style="width: 10px;">:</td><td style="width: 160px;"><strong><?php echo htmlspecialchars($tahun_ajaran); ?></strong></td>
        </tr>
        <tr>
            <td>Kelas</td><td>:</td><td><strong><?php echo htmlspecialchars($kelas); ?></strong></td>
            <td>Semester</td><td>:</td><td><strong><?php echo htmlspecialchars($semester); ?></strong></td>
        </tr>
        <tr>
            <td>Guru Pengampu</td><td>:</td><td><?php echo htmlspecialchars($guru); ?></td>
            <td>Wali Kelas</td><td>:</td><td><?php echo htmlspecialchars($wali_kelas); ?></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 30px;">No</th>
                <th rowspan="2" style="width: 80px;">NIS</th>
                <th rowspan="2">Nama Lengkap Siswa</th>
                <?php if (count($evaluasi_list) > 0): ?>
                    <th colspan="<?php echo count($evaluasi_list); ?>">Penilaian Harian (PH)</th>
                <?php endif; ?>
                <th colspan="2">Evaluasi Sumatif</th>
                <th rowspan="2" style="width: 70px;">Nilai Rapor<br>(KKM: <?php echo $kkm; ?>)</th>
            </tr>
            <tr>
                <?php foreach ($evaluasi_list as $idx => $ev): ?>
                    <th style="width: 42px;">PH<?php echo $idx + 1; ?></th>
                <?php endforeach; ?>
                <th style="width: 42px;">PTS</th>
                <th style="width: 42px;">PAS</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($siswa_list as $s): ?>
            <tr>
                <td align="center"><?php echo $no++; ?></td>
                <td align="center"><?php echo htmlspecialchars($s['nis'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($s['nama']); ?></td>
                <?php foreach ($evaluasi_list as $ev): ?>
                    <td align="center"><?php echo htmlspecialchars((string)$s['nilai_harian'][$ev['id']]); ?></td>
                <?php endforeach; ?>
                <td align="center"><?php echo htmlspecialchars((string)$s['nilai_pts']); ?></td>
                <td align="center"><?php echo htmlspecialchars((string)$s['nilai_pas']); ?></td>
                <td align="center"><strong><?php echo htmlspecialchars((string)$s['nilai_rapor']); ?></strong></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td>
                Mengetahui,<br>Kepala Madrasah
                <br><br><br><br>
                <strong><u><?php echo htmlspecialchars($inst['nama_kepala'] ?? 'Kepala Madrasah'); ?></u></strong>
            </td>
            <td>
                <?php echo htmlspecialchars($inst['kota'] ?? ''); ?>, <?php echo date('d F Y'); ?><br>Guru Mata Pelajaran
                <br><br><br><br>
                <strong><u><?php echo htmlspecialchars($guru); ?></u></strong>
            </td>
        </tr>
    </table>
</body>
</html>
