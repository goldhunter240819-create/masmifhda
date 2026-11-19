<?php
$namaInstitusi = 'MADRASAH ALIYAH MIFTAHUL HUDA';
$logoUrl = function_exists('base_url') ? base_url('public/uploads/logo/logo.png') : '/public/uploads/logo/logo.png';
if (class_exists('\App\Core\Branding')) {
    try { $logoUrl = \App\Core\Branding::getInstitusiLogo(); } catch (\Throwable $e) {}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Meja Peserta - <?= htmlspecialchars($ruang['nama_ruang']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800;900&family=JetBrains+Mono:wght@800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f1f5f9; font-family: 'Plus Jakarta Sans', Arial, sans-serif; color: #0f172a; padding: 20px; }
        .no-print-bar { max-width: 900px; margin: 0 auto 20px; background: white; padding: 14px 20px; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); display: flex; align-items: center; justify-content: space-between; border: 1px solid #cbd5e1; }
        .btn-ctrl { padding: 9px 18px; border-radius: 10px; font-size: 0.85rem; font-weight: 800; text-decoration: none; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-print { background: #2563eb; color: white; }
        .btn-back { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        .label-grid { max-width: 900px; margin: 0 auto; display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
        .meja-card { background: white; border: 2px dashed #0f172a; border-radius: 12px; padding: 14px 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); page-break-inside: avoid; display: flex; flex-direction: column; justify-content: space-between; height: 180px; position: relative; }
        .card-top { display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #0f172a; padding-bottom: 8px; margin-bottom: 8px; }
        .card-logo { width: 34px; height: 34px; object-fit: contain; }
        .card-inst { font-size: 0.72rem; font-weight: 800; color: #0f172a; text-transform: uppercase; }
        .card-ruang { font-size: 0.65rem; font-weight: 700; color: #475569; }
        .no-meja-badge { background: #0f172a; color: white; font-family: 'JetBrains Mono', monospace; font-size: 1.15rem; font-weight: 900; padding: 4px 12px; border-radius: 8px; }
        .siswa-nama { font-size: 1.05rem; font-weight: 900; color: #0f172a; line-height: 1.2; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .siswa-meta { font-size: 0.78rem; font-weight: 700; color: #475569; display: flex; gap: 10px; align-items: center; }
        .card-footer { border-top: 1px solid #e2e8f0; padding-top: 6px; display: flex; justify-content: space-between; align-items: center; font-size: 0.65rem; color: #64748b; font-weight: 700; }
        @media print {
            body { background: white !important; padding: 0 !important; margin: 0 !important; }
            .no-print-bar { display: none !important; }
            .label-grid { max-width: 100% !important; gap: 10px !important; }
            .meja-card { border: 2px dashed #000000 !important; box-shadow: none !important; }
            .no-meja-badge { background: #000000 !important; color: white !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            @page { size: A4 portrait; margin: 10mm; }
        }
    </style>
</head>
<body>

<div class="no-print-bar">
    <div style="display: flex; align-items: center; gap: 10px;">
        <button type="button" onclick="history.back()" class="btn-ctrl btn-back"><i class="fa-solid fa-arrow-left"></i> Kembali</button>
        <span style="font-size: 0.85rem; font-weight: 700;">Cetak Label Meja: <b><?= htmlspecialchars($ruang['nama_ruang']) ?> (<?= count($mejaList) ?> Label)</b></span>
    </div>
    <button type="button" onclick="window.print()" class="btn-ctrl btn-print"><i class="fa-solid fa-print"></i> Cetak Label Meja A4</button>
</div>

<div class="label-grid">
    <?php if (empty($mejaList)): ?>
        <div style="grid-column: span 2; text-align: center; padding: 40px; color: #94a3b8; background: white; border-radius: 12px;">
            Belum ada siswa yang ditempatkan di kursi ruangan ini. Silakan tata siswa terlebih dahulu.
        </div>
    <?php else: ?>
        <?php foreach ($mejaList as $m): ?>
            <div class="meja-card">
                <div class="card-top">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <img src="<?= $logoUrl ?>" class="card-logo" onerror="this.src='/public/uploads/logo/logo.png'">
                        <div>
                            <div class="card-inst"><?= htmlspecialchars($namaInstitusi) ?></div>
                            <div class="card-ruang">KARTU MEJA • <?= htmlspecialchars($ruang['nama_ruang']) ?></div>
                        </div>
                    </div>
                    <div class="no-meja-badge">MEJA <?= str_pad($m['nomor_meja'], 2, '0', STR_PAD_LEFT) ?></div>
                </div>

                <div>
                    <div class="siswa-nama" title="<?= htmlspecialchars($m['nama_siswa']) ?>"><?= htmlspecialchars($m['nama_siswa']) ?></div>
                    <div class="siswa-meta">
                        <span>NIS: <b style="font-family: monospace; color: #0f172a;"><?= htmlspecialchars($m['nis'] ?: '-') ?></b></span>
                        <span>&bull;</span>
                        <span>Kelas: <b style="color: #2563eb;"><?= htmlspecialchars($m['kelas_nama'] ?: 'X') ?></b></span>
                    </div>
                </div>

                <div class="card-footer">
                    <span>ASESMEN CBT 2026/2027</span>
                    <span>TEMPEL PADA MEJA PESERTA</span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
