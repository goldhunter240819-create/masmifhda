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
                $label = '';
                if ($status_dt == 'Hadir') $label = 'H';
                elseif ($status_dt == 'Sakit') $label = 'S';
                elseif ($status_dt == 'Izin') $label = 'I';
                elseif ($status_dt == 'Alpa') $label = 'A';
                elseif ($status_dt == 'Bolos') $label = 'B';
                elseif ($status_dt == 'Terlambat') $label = 'T';
            ?>
                <td class="text-center" style="<?php echo $label == 'A' ? 'color: red;' : ($label == 'B' ? 'color: darkred; font-weight: bold;' : ''); ?>"><?php echo $label; ?></td>
            <?php endforeach; ?>
            <td class="text-center"><?php echo $row['rekap']['Hadir']; ?></td>
            <td class="text-center"><?php echo $row['rekap']['Sakit']; ?></td>
            <td class="text-center"><?php echo $row['rekap']['Izin']; ?></td>
            <td class="text-center"><?php echo $row['rekap']['Alpa']; ?></td>
            <td class="text-center"><?php echo $row['rekap']['Bolos']; ?></td>
            <td class="text-center"><?php echo $row['rekap']['Terlambat']; ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
