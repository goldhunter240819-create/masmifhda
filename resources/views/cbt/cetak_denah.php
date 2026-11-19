<?php
$bulanIndo = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$tglCetak = date('d') . ' ' . ($bulanIndo[(int)date('n')] ?? date('F')) . ' ' . date('Y');

$namaInstitusi = 'MADRASAH ALIYAH MIFTAHUL HUDA';
$namaKepala = 'Drs. Hamim Hasyim';
$nipKepala = '-';
$logoUrl = function_exists('base_url') ? base_url('public/uploads/logo/logo.png') : '/public/uploads/logo/logo.png';
$faviconUrl = $logoUrl;
if (class_exists('\App\Core\Branding')) {
    try { 
        $logoUrl = \App\Core\Branding::getInstitusiLogo(); 
        $faviconUrl = \App\Core\Branding::getInstitusiFavicon();
    } catch (\Throwable $e) {}
}
$kemenagLogo = function_exists('base_url') ? base_url('public/assets/img/kemenag-logo.png') : '/public/assets/img/kemenag-logo.png';

$tipeMeja = (int)($ruang['tipe_meja'] ?? 1);
$samping = (int)($ruang['baris_samping'] ?? 0);
$belakang = (int)($ruang['baris_belakang'] ?? 0);
if ($samping <= 0 || $belakang <= 0) {
    $polaParts = explode('x', strtolower($ruang['kode_pola'] ?? ''));
    if (count($polaParts) === 3) {
        $tipeMeja = (int)trim($polaParts[0]) ?: 1;
        $samping = (int)trim($polaParts[1]) ?: 6;
        $belakang = (int)trim($polaParts[2]) ?: 5;
    } else {
        $tipeMeja = $tipeMeja ?: 1;
        $samping = $samping ?: 6;
        $belakang = $belakang ?: 5;
    }
}

$mejaByBaris = [];
foreach ($mejaList as $m) {
    $b = (int)$m['posisi_baris'];
    if (!isset($mejaByBaris[$b])) $mejaByBaris[$b] = [];
    $mejaByBaris[$b][] = $m;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Denah Ruang Ujian - <?= htmlspecialchars($ruang['nama_ruang']) ?></title>
    <link rel="icon" type="image/png" href="<?= $faviconUrl ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&family=JetBrains+Mono:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f1f5f9; font-family: 'Plus Jakarta Sans', Arial, sans-serif; color: #0f172a; padding: 20px; }
        .no-print-bar { max-width: 950px; margin: 0 auto 20px; background: white; padding: 14px 20px; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); display: flex; align-items: center; justify-content: space-between; border: 1px solid #cbd5e1; }
        .btn-ctrl { padding: 9px 18px; border-radius: 10px; font-size: 0.85rem; font-weight: 800; text-decoration: none; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-print { background: #2563eb; color: white; }
        .btn-back { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        .paper-sheet { max-width: 950px; margin: 0 auto; background: white; padding: 20mm 15mm; border-radius: 4px; box-shadow: 0 5px 25px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; }
        .kop-header { display: flex; align-items: center; justify-content: space-between; border-bottom: 3px double #0f172a; padding-bottom: 10px; margin-bottom: 14px; }
        .kop-logo { width: 70px; height: 70px; object-fit: contain; }
        .kop-title-box { flex: 1; text-align: center; padding: 0 15px; }
        .kop-title-box h4 { font-size: 0.85rem; font-weight: 800; color: #1e293b; text-transform: uppercase; margin-bottom: 2px; }
        .kop-title-box h2 { font-size: 1.2rem; font-weight: 900; color: #0f172a; text-transform: uppercase; margin-bottom: 2px; }
        .kop-title-box p { font-size: 0.7rem; color: #475569; }
        .doc-title-box { text-align: center; margin-bottom: 14px; }
        .doc-title-box h3 { font-size: 1rem; font-weight: 900; text-transform: uppercase; text-decoration: underline; margin-bottom: 2px; }
        .meta-table { width: 100%; margin-bottom: 14px; font-size: 0.78rem; border-collapse: collapse; }
        .meta-table td { padding: 3px 6px; }
        .papan-tulis-banner { background: #064e3b; color: white; border: 2px solid #022c22; border-radius: 6px; text-align: center; padding: 8px; font-weight: 900; font-size: 0.85rem; letter-spacing: 2px; margin-bottom: 18px; }
        .denah-grid { display: flex; flex-direction: column; gap: 14px; margin-bottom: 25px; }
        .denah-row { display: flex; justify-content: center; gap: <?= ($tipeMeja === 1) ? '10px' : '18px' ?>; flex-wrap: nowrap; }
        .meja-blok { display: flex; gap: 4px; border: 1.5px solid #0f172a; padding: 4px; border-radius: 6px; background: #f8fafc; }
        .kursi-box { width: 120px; height: 72px; border: 1px solid #475569; border-radius: 4px; padding: 4px 6px; display: flex; flex-direction: column; justify-content: space-between; background: white; }
        .kursi-head { display: flex; justify-content: space-between; align-items: center; }
        .kursi-num { font-size: 0.65rem; font-weight: 900; background: #0f172a; color: white; padding: 1px 4px; border-radius: 3px; font-family: monospace; }
        .kursi-side { font-size: 0.58rem; font-weight: 800; color: #64748b; }
        .kursi-nama { font-weight: 800; font-size: 0.7rem; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.15; margin: 2px 0; }
        .kursi-kelas { font-size: 0.62rem; font-weight: 800; color: #1e40af; }
        .kursi-kosong { flex: 1; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; color: #94a3b8; font-style: italic; }
        .sign-wrapper { display: flex; justify-content: space-between; margin-top: 25px; page-break-inside: avoid; font-size: 0.8rem; }
        .sign-col { text-align: center; width: 220px; }
        .sign-space { height: 60px; }
        .sign-name { font-weight: 800; text-decoration: underline; }
        @media print {
            body { background: white !important; padding: 0 !important; margin: 0 !important; }
            .no-print-bar { display: none !important; }
            .paper-sheet { box-shadow: none !important; border: none !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
            .papan-tulis-banner { background: #064e3b !important; color: white !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .kursi-num { background: #0f172a !important; color: white !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            @page { size: A4 portrait; margin: 12mm 15mm; }
        }
    </style>
</head>
<body>

<div class="no-print-bar">
    <div style="display: flex; align-items: center; gap: 10px;">
        <button type="button" onclick="history.back()" class="btn-ctrl btn-back"><i class="fa-solid fa-arrow-left"></i> Kembali</button>
        <span style="font-size: 0.85rem; font-weight: 700;">Denah Ruang: <b><?= htmlspecialchars($ruang['nama_ruang']) ?> (<?= htmlspecialchars($ruang['kode_pola']) ?>)</b></span>
    </div>
    <button type="button" onclick="window.print()" class="btn-ctrl btn-print"><i class="fa-solid fa-print"></i> Cetak Dokumen A4</button>
</div>

<div class="paper-sheet">
    <div class="kop-header">
        <img src="<?= $logoUrl ?>" class="kop-logo" onerror="this.src='/public/uploads/logo/logo.png'" alt="Logo Madrasah">
        <div class="kop-title-box">
            <h4>KEMENTERIAN AGAMA REPUBLIK INDONESIA</h4>
            <h2><?= htmlspecialchars($namaInstitusi) ?></h2>
            <p>STATUS TERAKREDITASI • NSM: 131218040003 • NPSN: 69988085</p>
            <p>Jalan Raya Sindangjaya, Kec. Mangunjaya, Kab. Pangandaran / Lampung Barat</p>
        </div>
        <img src="<?= $kemenagLogo ?>" class="kop-logo" onerror="this.style.visibility='hidden'" alt="Logo Kemenag">
    </div>

    <div class="doc-title-box">
        <h3>DENAH TEMPAT DUDUK PESERTA ASESMEN CBT</h3>
        <p style="font-size: 0.75rem; font-weight: 700; color: #475569;">TAHUN AJARAN 2026/2027</p>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 15%; font-weight: 700; color: #475569;">Ruang Ujian</td>
            <td style="width: 2%;">:</td>
            <td style="width: 33%; font-weight: 800;"><?= htmlspecialchars($ruang['nama_ruang'] . ' (' . $ruang['kode_ruang'] . ')') ?></td>
            <td style="width: 15%; font-weight: 700; color: #475569;">Format Meja</td>
            <td style="width: 2%;">:</td>
            <td style="width: 33%; font-weight: 800; font-family: monospace;"><?= htmlspecialchars($ruang['kode_pola']) ?> (Kapasitas <?= $ruang['kapasitas'] ?>)</td>
        </tr>
        <tr>
            <td style="font-weight: 700; color: #475569;">Lokasi Gedung</td>
            <td>:</td>
            <td style="font-weight: 800;"><?= htmlspecialchars($ruang['lokasi'] ?: 'Madrasah') ?></td>
            <td style="font-weight: 700; color: #475569;">Pengawas Ruang</td>
            <td>:</td>
            <td style="font-weight: 800;"><?= htmlspecialchars($ruang['pengawas'] ?: 'Pengawas CBT') ?></td>
        </tr>
    </table>

    <!-- Papan Tulis & Posisi Depan -->
    <div class="papan-tulis-banner">
        📋 PAPAN TULIS & MEJA PENGAWAS (BAGIAN DEPAN)
    </div>

    <!-- Denah Grid -->
    <div class="denah-grid">
        <?php for ($b = 1; $b <= $belakang; $b++): ?>
            <div class="denah-row">
                <?php
                $mRow = $mejaByBaris[$b] ?? [];
                $mejaByKolom = [];
                foreach ($mRow as $mr) {
                    $k = (int)$mr['posisi_kolom'];
                    if (!isset($mejaByKolom[$k])) $mejaByKolom[$k] = [];
                    $mejaByKolom[$k][] = $mr;
                }
                ?>
                <?php for ($k = 1; $k <= $samping; $k++): ?>
                    <?php if ($tipeMeja === 1): ?>
                        <?php
                        $seat = ($mejaByKolom[$k] ?? [])[0] ?? null;
                        if (!$seat) continue;
                        $hasSiswa = !empty($seat['siswa_id']);
                        ?>
                        <div class="kursi-box" style="border: 1.5px solid #0f172a;">
                            <div class="kursi-head">
                                <span class="kursi-num">#<?= str_pad($seat['nomor_meja'], 2, '0', STR_PAD_LEFT) ?></span>
                            </div>
                            <?php if ($hasSiswa): ?>
                                <div class="kursi-nama"><?= htmlspecialchars($seat['nama_siswa']) ?></div>
                                <div class="kursi-kelas"><?= htmlspecialchars($seat['kelas_nama'] ?: 'X') ?> • <?= htmlspecialchars($seat['nis'] ?: '-') ?></div>
                            <?php else: ?>
                                <div class="kursi-kosong">Kosong</div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <?php $kMejas = $mejaByKolom[$k] ?? []; ?>
                        <div class="meja-blok">
                            <?php foreach ($kMejas as $seat): ?>
                                <?php $hasSiswa = !empty($seat['siswa_id']); ?>
                                <div class="kursi-box">
                                    <div class="kursi-head">
                                        <span class="kursi-num">#<?= str_pad($seat['nomor_meja'], 2, '0', STR_PAD_LEFT) ?></span>
                                        <span class="kursi-side"><?= ($seat['posisi_sisi'] === 'L') ? 'Kiri' : 'Kanan' ?></span>
                                    </div>
                                    <?php if ($hasSiswa): ?>
                                        <div class="kursi-nama"><?= htmlspecialchars($seat['nama_siswa']) ?></div>
                                        <div class="kursi-kelas"><?= htmlspecialchars($seat['kelas_nama'] ?: 'X') ?> • <?= htmlspecialchars($seat['nis'] ?: '-') ?></div>
                                    <?php else: ?>
                                        <div class="kursi-kosong">Kosong</div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        <?php endfor; ?>
    </div>

    <div style="text-align: center; font-size: 0.68rem; font-weight: 800; color: #64748b; border-top: 1px dashed #cbd5e1; padding-top: 8px;">
        BAGIAN BELAKANG RUANGAN
    </div>

    <div class="sign-wrapper">
        <div class="sign-col">
            <div>Mengetahui,</div>
            <div style="font-weight: 700;">Kepala Madrasah</div>
            <div class="sign-space"></div>
            <div class="sign-name"><?= htmlspecialchars($namaKepala) ?></div>
            <div style="font-size: 0.72rem; color: #475569;">NIP. <?= htmlspecialchars($nipKepala) ?></div>
        </div>
        <div class="sign-col">
            <div>Lampung Barat, <?= $tglCetak ?></div>
            <div style="font-weight: 700;">Pengawas Ruang Ujian</div>
            <div class="sign-space"></div>
            <div class="sign-name"><?= htmlspecialchars($ruang['pengawas'] ?: 'Pengawas Ruang') ?></div>
            <div style="font-size: 0.72rem; color: #475569;">NIP. -</div>
        </div>
    </div>
</div>

</body>
</html>
