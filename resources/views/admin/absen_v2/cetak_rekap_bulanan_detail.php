<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Absensi - <?php echo $periode_nama; ?></title>
    <style>
        body { font-family: 'Plus Jakarta Sans', Arial, sans-serif; font-size: 10px; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table th, table td { border: 1px solid #000; padding: 4px; text-align: left; font-size: 10px; }
        table th { background-color: #f1f5f9; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
            @page { size: landscape; margin: 10mm; }
        }
        .btn-print { background: #0ea5e9; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-bottom: 20px; font-weight: bold; }
    </style>
</head>
<body>

<div class="no-print" style="text-align: right;">
    <button onclick="window.print()" class="btn-print">Cetak Dokumen</button>
</div>

<div class="header">
    <h2>REKAP ABSENSI SISWA</h2>
    <p>Periode: <?php echo $periode_nama; ?> | Kelas: <?php echo $kelas_nama ?: 'Semua Kelas'; ?></p>
</div>

<table>
    <thead>
        <tr>
            <th width="3%" class="text-center">No</th>
            <th width="6%">NIS</th>
            <th width="15%">Nama Siswa</th>
            <th width="5%">Kelas</th>
            <?php foreach($dates_range as $dt): ?>
                <th width="2%"><?php echo date('d', strtotime($dt)); ?></th>
            <?php endforeach; ?>
            <th width="3%" class="text-center">Hadir</th>
            <th width="3%" class="text-center">Sakit</th>
            <th width="3%" class="text-center">Izin</th>
            <th width="3%" class="text-center">Alpa</th>
            <th width="3%" class="text-center">Bolos</th>
            <th width="3%" class="text-center">Telat</th>
        </tr>
    </thead>
    <tbody>
        <?php if(empty($rekap)): ?>
        <tr>
            <td colspan="<?php echo 10 + count($dates_range); ?>" class="text-center">Belum ada data absensi.</td>
        </tr>
        <?php else: ?>
        <?php $no=1; foreach($rekap as $row): ?>
        <tr>
            <td class="text-center"><?php echo $no++; ?></td>
            <td><?php echo htmlspecialchars($row['nis']); ?></td>
            <td><?php echo htmlspecialchars($row['nama']); ?></td>
            <td><?php echo htmlspecialchars($row['nama_kelas']); ?></td>
            <?php foreach($dates_range as $dt): 
                $status_dt = $row['harian'][$dt];
                $label = '-';
                $color_style = 'color: #94a3b8;';
                if ($status_dt == 'Hadir') {
                    $label = 'H';
                    $color_style = 'color: #16a34a; font-weight: bold;';
                } elseif ($status_dt == 'Sakit') {
                    $label = 'S';
                    $color_style = 'color: #ca8a04; font-weight: bold;';
                } elseif ($status_dt == 'Izin') {
                    $label = 'I';
                    $color_style = 'color: #2563eb; font-weight: bold;';
                } elseif ($status_dt == 'Alpa') {
                    $label = 'A';
                    $color_style = 'color: #dc2626; font-weight: bold;';
                } elseif ($status_dt == 'Bolos') {
                    $label = 'B';
                    $color_style = 'color: #7f1d1d; font-weight: bold;';
                } elseif ($status_dt == 'Terlambat') {
                    $label = 'T';
                    $color_style = 'color: #ea580c; font-weight: bold;';
                }
            ?>
                <td class="text-center" style="<?php echo $color_style; ?>"><?php echo $label; ?></td>
            <?php endforeach; ?>
            <td class="text-center" style="font-weight: 600;"><?php echo $row['rekap']['Hadir']; ?></td>
            <td class="text-center" style="font-weight: 600;"><?php echo $row['rekap']['Sakit']; ?></td>
            <td class="text-center" style="font-weight: 600;"><?php echo $row['rekap']['Izin']; ?></td>
            <td class="text-center" style="font-weight: 700; color: #dc2626;"><?php echo $row['rekap']['Alpa']; ?></td>
            <td class="text-center" style="font-weight: 700; color: #7f1d1d;"><?php echo $row['rekap']['Bolos']; ?></td>
            <td class="text-center" style="font-weight: 600;"><?php echo $row['rekap']['Terlambat']; ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<div style="margin-top: 15px; font-size: 9px; color: #475569; display: flex; justify-content: space-between; align-items: flex-start;">
    <div>
        <strong>Keterangan:</strong> 
        <span style="color: #16a34a; font-weight: bold;">H</span> = Hadir | 
        <span style="color: #ca8a04; font-weight: bold;">S</span> = Sakit | 
        <span style="color: #2563eb; font-weight: bold;">I</span> = Izin | 
        <span style="color: #dc2626; font-weight: bold;">A</span> = Alpa (Tidak Hadir) | 
        <span style="color: #7f1d1d; font-weight: bold;">B</span> = Bolos | 
        <span style="color: #ea580c; font-weight: bold;">T</span> = Terlambat | 
        <span style="color: #94a3b8;">-</span> = Hari Belum Lewat / Libur
    </div>
    <div style="text-align: right; font-style: italic;">
        Dicetak pada: <?php echo date('d-m-Y H:i:s'); ?> WIB | Sistem SI-AKAD MASMIFHDA
    </div>
</div>

<div style="margin-top: 30px; display: flex; justify-content: flex-end;">
    <div style="text-align: center; width: 220px; font-size: 10px;">
        <p style="margin: 0;">Way Tenong, <?php echo date('d') . ' ' . [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][(int)date('m')] . ' ' . date('Y'); ?></p>
        <p style="margin: 4px 0 60px 0;">Kepala Madrasah,</p>
        <p style="margin: 0; font-weight: bold; text-decoration: underline;">AHMAD RIFA'I, M.Pd</p>
        <p style="margin: 2px 0 0 0;">NIP. -</p>
    </div>
</div>

</body>
</html>
