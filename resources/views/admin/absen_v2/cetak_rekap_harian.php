<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Harian - <?php echo $tanggal; ?></title>
    <style>
        body { font-family: 'Plus Jakarta Sans', Arial, sans-serif; font-size: 12px; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table th, table td { border: 1px solid #000; padding: 8px; text-align: left; }
        table th { background-color: #f1f5f9; font-weight: bold; }
        .text-center { text-align: center; }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
        }
        .btn-print { background: #0ea5e9; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-bottom: 20px; font-weight: bold; }
    </style>
</head>
<body>

<div class="no-print" style="text-align: right;">
    <button onclick="window.print()" class="btn-print">Cetak Dokumen</button>
</div>

<div class="header">
    <h2>REKAP HARIAN ABSENSI SISWA</h2>
    <p>Tanggal: <?php echo date('d F Y', strtotime($tanggal)); ?> | Kelas: <?php echo $kelas_nama ?: 'Semua Kelas'; ?></p>
</div>

<table>
    <thead>
        <tr>
            <th class="text-center" width="5%">No</th>
            <th width="15%">NIS</th>
            <th width="35%">Nama Siswa</th>
            <th width="15%">Kelas</th>
            <th width="15%">Waktu Absen</th>
            <th width="15%">Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if(empty($absensi)): ?>
        <tr>
            <td colspan="6" class="text-center">Belum ada data absensi.</td>
        </tr>
        <?php else: ?>
        <?php $no=1; foreach($absensi as $row): ?>
        <tr>
            <td class="text-center"><?php echo $no++; ?></td>
            <td><?php echo htmlspecialchars($row['nis']); ?></td>
            <td><?php echo htmlspecialchars($row['nama']); ?></td>
            <td><?php echo htmlspecialchars($row['nama_kelas']); ?></td>
            <td><?php echo htmlspecialchars($row['jam_scan'] ?: '-'); ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
