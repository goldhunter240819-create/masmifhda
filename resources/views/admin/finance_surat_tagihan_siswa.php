<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Tagihan - <?php echo $siswa['nama']; ?></title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 20px; font-size: 13px; background: #f0f0f0; }
        .surat { width: 740px; margin: 0 auto; background: #fff; padding: 40px; border: 1px solid #ddd; }
        .header { display: flex; border-bottom: 3px solid #16a34a; padding-bottom: 15px; margin-bottom: 20px; align-items: center; }
        .logo { width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; margin-right: 15px; flex-shrink: 0; }
        .logo img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .school-info { flex: 1; text-align: center; margin-right: 80px; /* Balance the logo width for true center */ }
        .school-name { font-size: 20px; font-weight: bold; color: #16a34a; margin: 0 0 5px 0; text-transform: uppercase; }
        .school-sub { font-size: 12px; color: #475569; margin: 0; line-height: 1.4; }
        .surat-title { text-align: center; margin: 18px 0; }
        .surat-title h2 { font-size: 16px; font-weight: bold; text-transform: uppercase; border-bottom: 2px solid #333; display: inline-block; padding-bottom: 3px; margin: 0; }
        .siswa-info { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 12px 18px; margin: 15px 0; }
        .siswa-info table { width: 100%; border-collapse: collapse; }
        .siswa-info td { padding: 3px 6px; font-size: 13px; }
        .siswa-info td:first-child { width: 130px; font-weight: 600; color: #166534; }
        .tagihan-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .tagihan-table th { background: #16a34a; color: white; padding: 8px 10px; font-size: 12px; text-align: left; }
        .tagihan-table td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; font-size: 13px; }
        .tagihan-table tr.total-row td { font-weight: bold; background: #f0fdf4; border-top: 2px solid #16a34a; }
        .pill { padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .pill-green { background: #dcfce7; color: #16a34a; }
        .pill-amber { background: #fef3c7; color: #d97706; }
        .pill-blue  { background: #dbeafe; color: #2563eb; }
        .sisa-box { background: #fef2f2; border: 2px solid #dc2626; border-radius: 8px; padding: 14px 18px; margin: 15px 0; }
        .lunas-box { background: #f0fdf4; border: 2px solid #16a34a; border-radius: 8px; padding: 14px 18px; margin: 15px 0; color: #15803d; font-weight: bold; font-size: 15px; text-align: center; }
        .footer-sign { display: flex; justify-content: flex-end; margin-top: 35px; }
        .sign-box { text-align: center; width: 220px; }
        .sign-space { height: 70px; }
        @media print {
            body { background: white; padding: 0; }
            .surat { border: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="no-print" style="margin-bottom: 15px; text-align: center;">
    <button onclick="window.print()" style="padding: 10px 24px; background: #16a34a; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; margin-right: 8px;">ðŸ–¨ Cetak Sekarang</button>
    <button onclick="window.close()" style="padding: 10px 20px; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer; color:#333;">âœ– Tutup Tab</button>
</div>

<?php
$bulanIndo = [
    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
    'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
    'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
    'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
];
$tanggalCetak = date('d ') . $bulanIndo[date('F')] . date(' Y');
?>

<div class="surat">
    <div class="header">
        <div class="logo">
            <?php if(!empty($institusi['logo']) && file_exists(__DIR__.'/../../../public/uploads/logo/'.$institusi['logo'])): ?>
                <img src="<?php echo base_url('public/uploads/logo/' . $institusi['logo']); ?>">
            <?php else: ?>
                LOGO
            <?php endif; ?>
        </div>
        <div class="school-info">
            <p class="school-name"><?php echo isset($institusi['nama']) ? strtoupper($institusi['nama']) : 'NAMA SEKOLAH'; ?></p>
            <p class="school-sub">
                <?php echo isset($institusi['alamat']) ? $institusi['alamat'] : ''; ?>
                <?php echo isset($institusi['desa']) ? ', '.$institusi['desa'] : ''; ?>
                <?php echo isset($institusi['kecamatan']) ? ', Kec. '.$institusi['kecamatan'] : ''; ?>
                <?php echo isset($institusi['kota']) ? ', '.$institusi['kota'] : ''; ?>
                <?php echo isset($institusi['kodepos']) ? ' '.$institusi['kodepos'] : ''; ?><br>
                <?php if(!empty($institusi['telepon'])): ?> Telp: <?php echo $institusi['telepon']; ?> &nbsp;|&nbsp; <?php endif; ?>
                <?php if(!empty($institusi['email'])): ?> Email: <?php echo $institusi['email']; ?> <?php endif; ?>
            </p>
        </div>
    </div>

    <div class="surat-title">
        <h2>Surat Tagihan Pembayaran Siswa</h2>
        <div style="font-size:11px;color:#64748b;margin-top:5px;">No: ST-<?php echo str_pad($siswa['id'], 4,'0',STR_PAD_LEFT); ?> / <?php echo date('Y'); ?> &nbsp;&bull;&nbsp; Dicetak: <?php echo $tanggalCetak; ?></div>
    </div>

    <p style="line-height: 1.5; margin-bottom: 5px;">Kepada Yth.<br><strong>Wali Murid / Orang Tua dari <?php echo $siswa['nama']; ?></strong><br>di tempat</p>

    <div class="siswa-info">
        <table>
            <tr><td>Nama Siswa</td><td>: <strong><?php echo $siswa['nama']; ?></strong></td></tr>
            <tr><td>NIS</td><td>: <?php echo $siswa['nis']; ?></td></tr>
            <tr><td>Kelas</td><td>: <?php echo $siswa['kelas']; ?></td></tr>
        </table>
    </div>

    <p style="margin-bottom:8px;">Berikut adalah rincian tagihan yang perlu diselesaikan:</p>

    <table class="tagihan-table">
        <thead>
            <tr>
                <th style="width:35%;">Jenis Tagihan</th>
                <th style="text-align:right;">Nominal</th>
                <th style="text-align:right;">Sudah Dibayar</th>
                <th style="text-align:right;">Sisa</th>
                <th style="text-align:center;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($tagihanList as $t): ?>
            <tr>
                <td>
                    <?php echo $t['nama_tagihan']; ?>
                    <div style="font-size:10px;color:#94a3b8;">JT: <?php echo date('d/m/Y', strtotime($t['jatuh_tempo'])); ?></div>
                </td>
                <td style="text-align:right;">Rp <?php echo number_format($t['jumlah_tagihan'],0,',','.'); ?></td>
                <td style="text-align:right;color:#16a34a;"><?php echo $t['terbayar'] > 0 ? 'Rp '.number_format($t['terbayar'],0,',','.') : '-'; ?></td>
                <td style="text-align:right;">
                    <?php if($t['status'] === 'Lunas'): ?>
                        <span style="color:#16a34a;font-weight:700;">LUNAS</span>
                    <?php else: ?>
                        <strong style="color:#dc2626;">Rp <?php echo number_format($t['sisa'],0,',','.'); ?></strong>
                    <?php endif; ?>
                </td>
                <td style="text-align:center;">
                    <span class="pill <?php echo $t['status']==='Lunas'?'pill-green':($t['status']==='Menyicil'?'pill-blue':'pill-amber'); ?>">
                        <?php echo $t['status']; ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td>TOTAL</td>
                <td style="text-align:right;">Rp <?php echo number_format($totalTagihan,0,',','.'); ?></td>
                <td style="text-align:right;color:#16a34a;">Rp <?php echo number_format($totalTerbayar,0,',','.'); ?></td>
                <td style="text-align:right;color:#dc2626;">Rp <?php echo number_format($totalSisa,0,',','.'); ?></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <?php if($totalSisa <= 0): ?>
    <div class="lunas-box">âœ… Seluruh tagihan telah LUNAS. Terima kasih atas kepercayaan Bapak/Ibu.</div>
    <?php else: ?>
    <div class="sisa-box">
        <div style="color:#dc2626;font-weight:bold;font-size:13px;">Total yang Masih Harus Dibayar:</div>
        <div style="color:#dc2626;font-weight:bold;font-size:22px;margin-top:4px;">Rp <?php echo number_format($totalSisa,0,',','.'); ?>,-</div>
        <div style="font-size:11px;color:#991b1b;margin-top:5px;">Mohon segera melakukan pembayaran ke Bendahara sesuai jatuh tempo masing-masing tagihan.</div>
    </div>
    <?php endif; ?>

    <p style="font-size:12px;">Demikian surat tagihan ini kami sampaikan. Atas perhatian dan kerja samanya kami ucapkan terima kasih.</p>

    <div class="footer-sign" style="display: flex; justify-content: flex-end; margin-top: 35px; align-items: flex-end;">
        <div style="margin-right: 40px; text-align: center; font-size: 10px; color: #64748b; line-height: 1.2;" class="no-print">
            <img src="<?php echo $qr_api_url; ?>" alt="QR Verification" style="border: 1px solid #e2e8f0; border-radius: 4px; padding: 4px; background: white;"><br>
            Pindai untuk Verifikasi<br>Dokumen Sah
        </div>
        <div class="sign-box">
            <p style="margin:0;"><?php echo $tanggalCetak; ?></p>
            <p>Bendahara,</p>
            <div class="sign-space" style="height: 35px;"></div>
            <p style="margin:0;border-top:1px solid #333;padding-top:3px;"><strong><?php echo htmlspecialchars($adminNama); ?></strong></p>
        </div>
    </div>
</div>

</body>
</html>
