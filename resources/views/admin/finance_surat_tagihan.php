<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Tagihan - <?php echo $t['nama']; ?></title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 20px; font-size: 13px; background: #f0f0f0; }
        .surat { width: 740px; margin: 0 auto; background: #fff; padding: 40px; border: 1px solid #ddd; }
        .header { display: flex; border-bottom: 3px solid #1d4ed8; padding-bottom: 15px; margin-bottom: 25px; align-items: center; }
        .logo { width: 70px; height: 70px; border-radius: 50%; background: #dbeafe; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem; color: #1d4ed8; border: 2px solid #1d4ed8; }
        .school-info { margin-left: 15px; }
        .school-name { font-size: 18px; font-weight: bold; color: #1d4ed8; margin: 0 0 3px 0; }
        .school-sub { font-size: 11px; color: #64748b; margin: 0; }
        .surat-title { text-align: center; margin: 20px 0; }
        .surat-title h2 { font-size: 16px; font-weight: bold; text-transform: uppercase; border-bottom: 2px solid #333; display: inline-block; padding-bottom: 4px; margin: 0; }
        .surat-title .no { font-size: 11px; color: #64748b; margin-top: 6px; }
        .intro { margin: 20px 0; line-height: 1.8; }
        .siswa-info { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 15px 20px; margin: 20px 0; }
        .siswa-info table { width: 100%; border-collapse: collapse; }
        .siswa-info td { padding: 4px 6px; font-size: 13px; }
        .siswa-info td:first-child { width: 150px; font-weight: 600; color: #475569; }
        .tagihan-box { margin: 20px 0; }
        .tagihan-box table { width: 100%; border-collapse: collapse; }
        .tagihan-box th { background: #1d4ed8; color: white; padding: 8px 12px; font-size: 12px; text-align: left; }
        .tagihan-box td { padding: 8px 12px; border-bottom: 1px solid #e2e8f0; font-size: 13px; }
        .tagihan-box tr:last-child td { font-weight: bold; background: #f8fafc; }
        .sisa-box { background: #fef2f2; border: 2px solid #dc2626; border-radius: 8px; padding: 15px 20px; margin: 20px 0; }
        .sisa-label { color: #dc2626; font-weight: bold; font-size: 14px; }
        .sisa-amount { color: #dc2626; font-weight: bold; font-size: 22px; }
        .lunas-box { background: #f0fdf4; border: 2px solid #16a34a; border-radius: 8px; padding: 15px 20px; margin: 20px 0; color: #15803d; font-weight: bold; font-size: 15px; text-align: center; }
        .footer-sign { display: flex; justify-content: flex-end; margin-top: 40px; }
        .sign-box { text-align: center; width: 200px; }
        .sign-space { height: 65px; }
        @media print {
            body { background: white; padding: 0; }
            .surat { border: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="no-print" style="margin-bottom: 15px; text-align: center;">
    <button onclick="window.print()" style="padding: 10px 24px; background: #1d4ed8; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; margin-right: 8px;">ðŸ–¨ Cetak Sekarang</button>
    <button onclick="window.history.back()" style="padding: 10px 20px; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer;">â† Kembali</button>
</div>

<div class="surat">
    <div class="header">
        <div class="logo">
            <?php if(!empty($institusi['logo']) && file_exists(__DIR__.'/../../../public/uploads/logo/'.$institusi['logo'])): ?>
                <img src="<?php echo base_url('public/uploads/logo/' . $institusi['logo']); ?>" style="max-width:100%; max-height:100%; border-radius:50%;">
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
                <?php echo isset($institusi['kodepos']) ? ' '.$institusi['kodepos'] : ''; ?>
            </p>
        </div>
    </div>

    <div class="surat-title">
        <h2>Surat Tagihan Pembayaran</h2>
        <div class="no">No: ST-<?php echo str_pad($t['id'], 5, '0', STR_PAD_LEFT); ?> / <?php echo date('Y'); ?></div>
    </div>

    <div class="intro">
        <p>Kepada Yth.<br><strong><?php echo $t['nama']; ?></strong><br>Wali murid / Orang tua siswa di tempat</p>
        <p>Dengan hormat, bersama surat ini kami sampaikan informasi tagihan pembayaran yang perlu segera diselesaikan:</p>
    </div>

    <div class="siswa-info">
        <table>
            <tr><td>Nama Siswa</td><td>: <strong><?php echo $t['nama']; ?></strong></td></tr>
            <tr><td>NIS</td><td>: <?php echo $t['nis']; ?></td></tr>
            <tr><td>Kelas</td><td>: <?php echo $t['kelas']; ?></td></tr>
            <tr><td>Jenis Tagihan</td><td>: <?php echo $t['nama_tagihan']; ?></td></tr>
            <tr><td>Jatuh Tempo</td><td>: <?php echo date('d F Y', strtotime($t['jatuh_tempo'])); ?></td></tr>
        </table>
    </div>

    <div class="tagihan-box">
        <table>
            <thead>
                <tr><th>Keterangan</th><th style="text-align:right;">Jumlah</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $t['nama_tagihan']; ?></td>
                    <td style="text-align:right;">Rp <?php echo number_format($t['jumlah_tagihan'],0,',','.'); ?></td>
                </tr>
                <?php if($terbayar > 0): ?>
                <tr>
                    <td style="color:#16a34a;">Sudah Dibayar</td>
                    <td style="text-align:right; color:#16a34a;">- Rp <?php echo number_format($terbayar,0,',','.'); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td>Total yang Harus Dibayar</td>
                    <td style="text-align:right;">Rp <?php echo number_format($sisa,0,',','.'); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <?php if($sisa <= 0): ?>
    <div class="lunas-box">âœ… Tagihan ini telah LUNAS sepenuhnya. Terima kasih atas kepercayaan Bapak/Ibu.</div>
    <?php else: ?>
    <div class="sisa-box">
        <div class="sisa-label">Total Sisa Tagihan yang Harus Dibayar:</div>
        <div class="sisa-amount">Rp <?php echo number_format($sisa,0,',','.'); ?>,-</div>
        <?php if($t['status'] === 'Menyicil'): ?>
        <div style="font-size:11px; color:#991b1b; margin-top:6px;">* Pembayaran sudah diterima sebagian. Mohon segera melunasi sisa tagihan sebelum jatuh tempo.</div>
        <?php else: ?>
        <div style="font-size:11px; color:#991b1b; margin-top:6px;">* Mohon segera melakukan pembayaran ke Bendahara Sekolah sebelum tanggal jatuh tempo.</div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <p>Demikian surat tagihan ini kami sampaikan. Atas perhatian dan kerja sama Bapak/Ibu, kami ucapkan terima kasih.</p>

    <div class="footer-sign">
        <div class="sign-box">
            <p><?php echo date('d F Y'); ?></p>
            <p>Bendahara Sekolah,</p>
            <div class="sign-space"></div>
            <p><strong><?php echo $adminNama; ?></strong></p>
        </div>
    </div>
</div>

</body>
</html>
