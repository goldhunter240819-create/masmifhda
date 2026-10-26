<?php
$title = $title ?? 'Dashboard E-BK | MASMIFHDA';
$active_page = $activeMenu ?? 'bk_dashboard';
$adminNama = $_SESSION['nama'] ?? 'Admin';
$adminInitial = strtoupper(substr($adminNama, 0, 2));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>?v=<?php echo time(); ?>">
    <!-- Zamrud CSS -->
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . time()); ?>">
    <!-- Lucide Icons -->
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
</head>
<body>
    <div class="z-wrap">
        <?php include __DIR__ . '/../admin/_sidebar_bk.php'; ?>
        <main class="z-main">
            <header class="z-header">
                <div class="z-header-left">
                    <button class="z-hamburger" id="zHamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
                    <span class="z-header-title">Dashboard E-BK</span>
                </div>
                <div class="z-header-user">
                    <div class="z-user-info">
                        <div class="name"><?php echo $adminNama; ?></div>
                        <div class="role">Bimbingan Konseling</div>
                    </div>
                </div>
            </header>
            
            <div class="z-content">
                <div class="elite-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: var(--z-radius, 12px); margin-bottom: 2rem; color: white;">
                    <div>
                        <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                            <i data-lucide="shield" style="color: rgba(255,255,255,0.8);"></i> Dashboard E-BK & Kedisiplinan
                        </h1>
                        <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Pantau poin kedisiplinan dan prestasi siswa tahun ajaran ini.</p>
                    </div>
                </div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Top 5 Pelanggar -->
    <div class="z-panel" style="margin-bottom: 0;">
        <div class="z-panel-head">
            <h3 class="z-panel-title">Siswa Poin Tertinggi (Top 5)</h3>
        </div>
        <div class="z-panel-body" style="padding: 0;">
            <table class="z-table">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th style="text-align: right;">Total Poin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($topPelanggar)): ?>
                        <tr><td colspan="3" style="text-align:center; padding: 2rem;">Belum ada data pelanggaran tahun ini.</td></tr>
                    <?php else: ?>
                        <?php foreach($topPelanggar as $p): ?>
                        <tr>
                            <td style="font-weight: 600; color: #b91c1c;"><?php echo htmlspecialchars($p['nama_siswa']); ?></td>
                            <td><?php echo htmlspecialchars($p['nama_kelas']); ?></td>
                            <td style="text-align: right; font-weight: bold;"><?php echo $p['total_poin']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Poin Masuk Terbaru -->
    <div class="z-panel" style="margin-bottom: 0;">
        <div class="z-panel-head">
            <h3 class="z-panel-title">Riwayat Poin Terbaru</h3>
        </div>
        <div class="z-panel-body" style="padding: 0;">
            <table class="z-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Siswa</th>
                        <th>Kategori</th>
                        <th style="text-align: right;">Poin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentLog)): ?>
                        <tr><td colspan="4" style="text-align:center; padding: 2rem;">Belum ada log terbaru.</td></tr>
                    <?php else: ?>
                        <?php foreach($recentLog as $r): ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($r['tanggal'])); ?></td>
                            <td><?php echo htmlspecialchars($r['nama_siswa']); ?></td>
                            <td><?php echo htmlspecialchars($r['nama_kategori']); ?></td>
                            <td style="text-align: right; font-weight: bold; color: <?php echo $r['tipe'] == 'pelanggaran' ? '#b91c1c' : '#15803d'; ?>;">
                                <?php echo $r['tipe'] == 'pelanggaran' ? '+' : '-'; ?><?php echo $r['poin']; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide) lucide.createIcons();
        });

        function zToggleSidebar() {
            const sidebar = document.querySelector('.z-sidebar');
            if (sidebar) sidebar.classList.toggle('open');
        }
    </script>
</body>
</html>
