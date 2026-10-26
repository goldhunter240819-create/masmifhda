<?php
$page_title = 'Dashboard BOS';
include __DIR__ . '/_layout_top.php';
?>

<!-- Header Banner Elite Sultan -->
<div class="elite-page-header" style="background: linear-gradient(135deg, #059669 0%, #10b981 60%, #34d399 100%);">
    <div class="header-bg-shapes"></div>
    <div class="header-content-inner">
        <div>
            <span class="header-badge" style="background: rgba(255,255,255,0.2); color: #fff; border-color: rgba(255,255,255,0.3);">
                MODUL KEUANGAN BOS
            </span>
            <div class="header-text-premium">
                <h1 style="color:#fff;"><i data-lucide="landmark" style="color:#fff;"></i> Saldo Berjalan (BKU)</h1>
                <div style="font-size: 2.4rem; font-weight: 900; color: #fff; margin-top: 8px; letter-spacing: -1px;">
                    Rp <?= number_format($saldoAkhir, 0, ',', '.') ?>
                </div>
                <p style="color: rgba(255,255,255,0.85); margin-top: 6px;">Total penerimaan dikurangi pengeluaran riil berdasarkan Buku Kas Umum.</p>
            </div>
        </div>
        <div class="header-actions-premium" style="display:flex; gap:12px; margin-top: 15px;">
            <a href="<?= base_url('admin/bos/bku') ?>" class="e-btn" style="background: #fff; color: #059669; box-shadow: 0 10px 20px rgba(0,0,0,0.15);">
                <i data-lucide="book-open"></i> Isi BKU
            </a>
            <a href="<?= base_url('admin/bos/rkas') ?>" class="e-btn e-btn-glass" style="color:#fff; border-color: rgba(255,255,255,0.4);">
                <i data-lucide="file-spreadsheet"></i> Susun RKAS
            </a>
        </div>
    </div>
</div>

<!-- Kartu Statistik Utama -->
<div class="z-stats" style="grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px;">
    <!-- Penerimaan BKU -->
    <div class="z-stat" style="border-radius: 20px;">
        <div class="z-stat-left">
            <div class="z-stat-label">Total Penerimaan Dana BOS</div>
            <div class="z-stat-value" style="color: #0284c7;">Rp <?= number_format($totalPenerimaan, 0, ',', '.') ?></div>
        </div>
        <div class="z-stat-icon zi-blue" style="width: 52px; height: 52px;">
            <i data-lucide="arrow-down-left" style="width: 26px; height: 26px;"></i>
        </div>
    </div>

    <!-- Pengeluaran BKU -->
    <div class="z-stat" style="border-radius: 20px;">
        <div class="z-stat-left">
            <div class="z-stat-label">Total Pengeluaran Belanja</div>
            <div class="z-stat-value" style="color: #ef4444;">Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></div>
        </div>
        <div class="z-stat-icon zi-red" style="width: 52px; height: 52px;">
            <i data-lucide="arrow-up-right" style="width: 26px; height: 26px;"></i>
        </div>
    </div>

    <!-- Pajak BKU -->
    <div class="z-stat" style="border-radius: 20px;">
        <div class="z-stat-left">
            <div class="z-stat-label">Total Pemungutan Pajak</div>
            <div class="z-stat-value" style="color: #f59e0b;">Rp <?= number_format($totalPajak, 0, ',', '.') ?></div>
            <div class="z-stat-trend" style="font-size: 0.78rem;">PPN, PPh 21/22/23</div>
        </div>
        <div class="z-stat-icon zi-amber" style="width: 52px; height: 52px;">
            <i data-lucide="percent" style="width: 26px; height: 26px;"></i>
        </div>
    </div>
</div>

<!-- Serapan RKAS (8 SNP) -->
<div class="z-panel" style="border-radius: 24px;">
    <div class="z-panel-head">
        <div class="z-panel-title">
            <i data-lucide="pie-chart"></i>
            <span>Rencana Anggaran (RKAS) per 8 Standar Nasional</span>
        </div>
    </div>
    <div class="z-panel-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 15px;">
            <?php 
            $snp_map = [];
            foreach($snp_list as $s) { $snp_map[$s['snp_id']] = $s['anggaran']; }

            foreach($standar_nasional as $id => $nama_snp): 
                $anggaran = isset($snp_map[$id]) ? floatval($snp_map[$id]) : 0;
            ?>
            <div style="padding: 15px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc;">
                <div style="font-size: 0.75rem; color: #64748b; font-weight: 800; text-transform: uppercase;">Standar <?= $id ?></div>
                <div style="font-weight: 700; color: #0f172a; margin-top: 4px; margin-bottom: 8px; font-size: 0.9rem; line-height: 1.3; min-height: 2.6rem;"><?= $nama_snp ?></div>
                <div style="font-size: 1.1rem; font-weight: 900; color: #059669;">Rp <?= number_format($anggaran, 0, ',', '.') ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() { lucide.createIcons(); });
</script>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
