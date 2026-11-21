<?php
$pageTitle = 'Ujian Selesai - Hasil Perolehan';
$logoUrl = \App\Core\Branding::getInstitusiLogo();
$isTuntas = ((float)$pesertaInfo['nilai_akhir'] >= (float)($ujianInfo['kkm'] ?? 75));
$showScore = isset($showScore) ? (bool)$showScore : true;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            background: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1.5rem;
            margin: 0;
        }
        .result-card {
            background: #ffffff;
            border-radius: 24px;
            border: 1px solid #e2e8f0;
            width: 100%;
            max-width: 540px;
            padding: 2.5rem;
            text-align: center;
            box-shadow: 0 20px 45px -10px rgba(0,0,0,0.08);
        }
        .score-circle {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            margin: 1.5rem auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: <?php echo $isTuntas ? '#f0fdf4' : '#fef2f2'; ?>;
            border: 4px solid <?php echo $isTuntas ? '#86efac' : '#fecaca'; ?>;
            color: <?php echo $isTuntas ? '#15803d' : '#b91c1c'; ?>;
        }
        .score-num {
            font-size: 2.75rem;
            font-weight: 900;
            line-height: 1;
        }
        .score-label {
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 4px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin: 1.5rem 0;
        }
        .stat-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px;
            border-radius: 12px;
        }
        .stat-box .val {
            font-size: 1.35rem;
            font-weight: 800;
        }
        .stat-box .lab {
            font-size: 0.74rem;
            color: #64748b;
            font-weight: 600;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    <div class="result-card">
        <img src="<?php echo $logoUrl; ?>" alt="Logo" style="width: 48px; height: 48px; object-fit: contain; margin-bottom: 12px;">
        <h2 style="font-size: 1.4rem; font-weight: 900; margin-bottom: 4px; color: #0f172a;">Ujian Telah Selesai</h2>
        <div style="font-size: 0.88rem; color: #64748b; font-weight: 600;">
            <?php echo htmlspecialchars($ujianInfo['nama_ujian']); ?> &bull; <?php echo htmlspecialchars($ujianInfo['mapel_nama']); ?>
        </div>

        <?php if ($showScore): ?>
        <div class="score-circle">
            <div class="score-num"><?php echo number_format($pesertaInfo['nilai_akhir'], 0); ?></div>
            <div class="score-label">Nilai Akhir</div>
        </div>
        <?php else: ?>
        <div class="score-circle" style="width: 110px; height: 110px; margin: 1.25rem auto;">
            <i data-lucide="<?php echo $isTuntas ? 'check-circle-2' : 'alert-circle'; ?>" style="width: 44px; height: 44px;"></i>
            <div class="score-label" style="margin-top: 6px;"><?php echo $isTuntas ? 'TUNTAS' : 'REMEDIAL'; ?></div>
        </div>
        <?php endif; ?>

        <div style="margin-bottom: 1.5rem;">
            <span style="display: inline-block; padding: 6px 18px; border-radius: 100px; font-weight: 800; font-size: 0.85rem; <?php echo $isTuntas ? 'background:#dcfce7; color:#15803d; border:1px solid #bbf7d0;' : 'background:#fee2e2; color:#b91c1c; border:1px solid #fecaca;'; ?>">
                <?php echo $isTuntas ? '✓ TUNTAS (Memenuhi KKM: ' . ($ujianInfo['kkm'] ?? 75) . ')' : '✕ PERLU REMEDIAL (KKM: ' . ($ujianInfo['kkm'] ?? 75) . ')'; ?>
            </span>
        </div>

        <?php if ($showScore): ?>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="val" style="color: #16a34a;"><?php echo $pesertaInfo['jumlah_benar']; ?></div>
                <div class="lab">Jawaban Benar</div>
            </div>
            <div class="stat-box">
                <div class="val" style="color: #dc2626;"><?php echo $pesertaInfo['jumlah_salah']; ?></div>
                <div class="lab">Jawaban Salah</div>
            </div>
        </div>
        <?php else: ?>
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 14px; margin: 1.25rem 0; font-size: 0.8rem; color: #475569; line-height: 1.5; display: flex; align-items: center; gap: 10px; text-align: left;">
            <i data-lucide="shield-check" style="width: 22px; height: 22px; color: #0ea5e9; flex-shrink: 0;"></i>
            <div>
                <b>Skor Numerik Disembunyikan:</b> Lembar jawaban telah tersimpan rapi. Nilai numerik resmi dan arahan pelaksanaan remedial akan disampaikan oleh guru pengampu mapel.
            </div>
        </div>
        <?php endif; ?>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 16px; margin-bottom: 1.5rem; text-align: left; font-size: 0.82rem;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                <span style="color: #64748b;">Nama Siswa:</span>
                <strong style="color: #0f172a;"><?php echo htmlspecialchars($pesertaInfo['nama_siswa']); ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                <span style="color: #64748b;">NIS:</span>
                <strong style="color: #0f172a; font-family: monospace;"><?php echo htmlspecialchars($pesertaInfo['nis'] ?: '-'); ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: #64748b;">Kelas:</span>
                <strong style="color: #0f172a;"><?php echo htmlspecialchars($pesertaInfo['kelas_nama'] ?: '-'); ?></strong>
            </div>
        </div>

        <?php
            $apkCbtUrl = function_exists('base_url') ? base_url('apk/cbt.php') : ((strpos($_SERVER['REQUEST_URI'] ?? '', '/cbt/') !== false) ? '/cbt/apk/cbt.php' : '/apk/cbt.php');
        ?>
        <div style="display: flex; gap: 10px; justify-content: center; flex-direction: column; align-items: stretch; margin-top: 1rem;">
            <a href="<?php echo htmlspecialchars($apkCbtUrl); ?>" style="padding: 12px 20px; border-radius: 14px; background: linear-gradient(135deg, #10b981, #0284c7); color: white; text-decoration: none; font-weight: 800; font-size: 0.95rem; display: inline-flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 14px rgba(16,185,129,0.35);">
                <i data-lucide="smartphone" style="width: 18px; height: 18px;"></i> Kembali ke APK Siswa
            </a>
            <p style="margin: 6px 0 0 0; font-size: 0.75rem; color: #94a3b8; text-align: center;">
                Jawaban Anda telah tersimpan dengan aman di server CBT MAS MIFTAHUL HUDA.
            </p>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>

