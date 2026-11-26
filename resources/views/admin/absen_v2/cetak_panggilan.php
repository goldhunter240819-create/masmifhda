<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Panggilan Orang Tua - <?php echo htmlspecialchars($siswa['nama']); ?></title>
    <?php if (!empty($inst['logo'])): ?>
    <link rel="icon" href="<?php echo base_url('public/uploads/logo/' . $inst['logo']); ?>" type="image/x-icon">
    <?php endif; ?>
    <style>
        @page { size: 21.59cm 33.02cm portrait; margin: 1.5cm 2cm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; color: #000; margin: 0; padding: 24px; line-height: 1.5; }
        .sheet { max-width: 760px; margin: 0 auto; }
        .kop-surat { display: flex; align-items: center; justify-content: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 16px; position: relative; }
        .kop-surat img { width: 76px; height: 76px; object-fit: contain; position: absolute; left: 0; }
        .kop-text { text-align: center; flex: 1; }
        .kop-text h1 { margin: 0; font-size: <?php echo htmlspecialchars($inst['ukuran_font_yayasan'] ?? 15); ?>px; font-weight: bold; text-transform: uppercase; line-height: 1.2; }
        .kop-text h2 { margin: 2px 0; font-size: <?php echo htmlspecialchars($inst['ukuran_font_madrasah'] ?? 17); ?>px; font-weight: bold; text-transform: uppercase; line-height: 1.2; }
        .kop-text p { margin: 0; font-size: 10pt; line-height: 1.35; }
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .meta-table td { padding: 2px 0; vertical-align: top; }
        .info-box { margin: 10px 0 14px 28px; }
        .info-box td { padding: 2px 6px 2px 0; vertical-align: top; }
        .lampiran-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10.5pt; }
        .lampiran-table th, .lampiran-table td { border: 1px solid #000; padding: 5px 8px; }
        .lampiran-table th { background: #f3f4f6; text-align: center; }
        .ttd-table { width: 100%; margin-top: 28px; border-collapse: collapse; }
        .ttd-table td { width: 50%; text-align: center; vertical-align: top; }
        .btn-print { display: block; margin: 0 auto 18px auto; padding: 10px 22px; background: #0284c7; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 13px; }
        .page-break { page-break-before: always; margin-top: 28px; border-top: 1px dashed #94a3b8; padding-top: 18px; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            .page-break { border-top: none; padding-top: 0; margin-top: 0; }
        }
    </style>
</head>
<body>
<div class="sheet">
    <button onclick="window.print()" class="btn-print no-print">Cetak Surat Panggilan</button>

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
            ?>
            <?php if (!empty($alamatParts)): ?><p><?php echo htmlspecialchars(implode(', ', $alamatParts)); ?></p><?php endif; ?>
            <p>Website: <?php echo htmlspecialchars($inst['website'] ?? '-'); ?> | Email: <?php echo htmlspecialchars($inst['email'] ?? '-'); ?></p>
        </div>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width:90px;">Nomor</td><td style="width:14px;">:</td><td><?php echo htmlspecialchars($nomor_surat); ?></td>
            <td style="text-align:right;"><?php echo htmlspecialchars($inst['kota'] ?? ''); ?>, <?php echo date('d F Y'); ?></td>
        </tr>
        <tr><td>Lampiran</td><td>:</td><td colspan="2">1 (Satu) Lembar Rincian Ketidakhadiran</td></tr>
        <tr><td>Perihal</td><td>:</td><td colspan="2"><strong>Panggilan Orang Tua / Wali Siswa</strong></td></tr>
    </table>

    <p style="margin: 14px 0 8px 0;">
        Kepada Yth.<br>
        <strong>Bapak/Ibu Orang Tua / Wali Siswa</strong><br>
        di Tempat
    </p>

    <p style="text-align: justify; margin: 10px 0;">
        <em>Assalamu'alaikum Warahmatullahi Wabarakatuh.</em><br>
        Dengan hormat, sehubungan dengan hasil rekapitulasi absensi siswa pada periode
        <strong><?php echo date('d/m/Y', strtotime($start_date)); ?> s/d <?php echo date('d/m/Y', strtotime($end_date)); ?></strong>,
        kami sampaikan bahwa putra/putri Bapak/Ibu atas nama:
    </p>

    <table class="info-box">
        <tr><td style="width:150px;">Nama Siswa</td><td>:</td><td><strong><?php echo htmlspecialchars($siswa['nama']); ?></strong></td></tr>
        <tr><td>NIS</td><td>:</td><td><?php echo htmlspecialchars($siswa['nis']); ?></td></tr>
        <tr><td>Kelas</td><td>:</td><td><?php echo htmlspecialchars($siswa['nama_kelas'] ?? '-'); ?></td></tr>
        <tr><td>Wali Kelas</td><td>:</td><td><?php echo htmlspecialchars($wali_kelas); ?></td></tr>
        <tr><td>Jumlah Alpa</td><td>:</td><td><strong><?php echo (int)$rekap['Alpa']; ?> hari</strong></td></tr>
        <tr><td>Jumlah Bolos</td><td>:</td><td><strong><?php echo (int)$rekap['Bolos']; ?> hari</strong></td></tr>
    </table>

    <p style="text-align: justify; margin: 10px 0;">
        Tercatat memiliki ketidakhadiran tanpa keterangan (Alpa/Bolos) sebanyak <strong><?php echo (int)($rekap['Alpa'] + $rekap['Bolos']); ?> kali</strong>.
        Untuk itu kami mengharap kehadiran Bapak/Ibu Orang Tua/Wali Siswa guna melakukan konsultasi dan pembinaan bersama pihak madrasah pada:
    </p>

    <table class="info-box">
        <tr><td style="width:150px;">Hari / Tanggal</td><td>:</td><td><strong><?php echo $jadwal_panggilan !== '' ? htmlspecialchars($jadwal_panggilan) : '................................................'; ?></strong></td></tr>
        <tr><td>Waktu</td><td>:</td><td><?php echo htmlspecialchars($jam_panggilan); ?></td></tr>
        <tr><td>Tempat</td><td>:</td><td><?php echo htmlspecialchars($tempat_panggilan); ?></td></tr>
        <tr><td>Keperluan</td><td>:</td><td>Pembinaan kedisiplinan dan kehadiran siswa</td></tr>
    </table>

    <p style="text-align: justify; margin: 10px 0;">
        Mengingat pentingnya hal tersebut demi kelancaran proses belajar siswa, kami sangat mengharapkan kehadiran Bapak/Ibu tepat pada waktunya.
        Atas perhatian dan kerja samanya kami ucapkan terima kasih.<br>
        <em>Wassalamu'alaikum Warahmatullahi Wabarakatuh.</em>
    </p>

    <table class="ttd-table">
        <tr>
            <td>
                Mengetahui,<br>Kepala Madrasah
                <br><br><br><br>
                <strong><u><?php echo htmlspecialchars($inst['nama_kepala'] ?? 'Kepala Madrasah'); ?></u></strong>
            </td>
            <td>
                Guru BK / Kesiswaan
                <br><br><br><br><br>
                <strong><u>..........................................</u></strong>
            </td>
        </tr>
    </table>

    <!-- Lampiran Rincian Tanggal -->
    <div class="page-break">
        <h3 style="margin:0 0 6px 0; text-align:center; text-transform:uppercase; font-size:12pt;">Lampiran Rincian Ketidakhadiran Siswa</h3>
        <p style="margin:0 0 10px 0; text-align:center; font-size:10.5pt;">
            Nama: <strong><?php echo htmlspecialchars($siswa['nama']); ?></strong> (<?php echo htmlspecialchars($siswa['nama_kelas'] ?? '-'); ?>) &bull;
            Periode: <?php echo date('d/m/Y', strtotime($start_date)); ?> s/d <?php echo date('d/m/Y', strtotime($end_date)); ?>
        </p>

        <table class="lampiran-table">
            <thead>
                <tr>
                    <th style="width:45px;">No</th>
                    <th>Hari, Tanggal</th>
                    <th style="width:140px;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($alpa_bolos_dates)): ?>
                    <tr><td colspan="3" align="center">Tidak ada catatan Alpa atau Bolos pada periode ini.</td></tr>
                <?php else:
                    $hariMap = ['Sun'=>'Minggu','Mon'=>'Senin','Tue'=>'Selasa','Wed'=>'Rabu','Thu'=>'Kamis','Fri'=>'Jumat','Sat'=>'Sabtu'];
                    $no = 1;
                    foreach ($alpa_bolos_dates as $item):
                        $hari = $hariMap[date('D', strtotime($item['tanggal']))] ?? '';
                ?>
                    <tr>
                        <td align="center"><?php echo $no++; ?></td>
                        <td><?php echo $hari . ', ' . date('d/m/Y', strtotime($item['tanggal'])); ?></td>
                        <td align="center"><strong><?php echo htmlspecialchars($item['status']); ?></strong></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
