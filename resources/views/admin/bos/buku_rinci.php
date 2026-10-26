<?php
// $page_title, $page_icon, $page_color di-set dari controller
include __DIR__ . '/_layout_top.php';
?>

<div class="z-panel" style="border-radius: 20px;">
    <div class="z-panel-head" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid <?= $page_color ?>;">
        <div class="z-panel-title">
            <i data-lucide="<?= $page_icon ?>" style="color: <?= $page_color ?>;"></i> 
            <span style="color: <?= $page_color ?>; font-weight: 800;"><?= $page_title ?></span>
        </div>
        <div style="font-size: 0.85rem; color: #64748b;">
            Tersinkronisasi dengan Buku Kas Umum
        </div>
    </div>
    <div class="z-panel-body">
        <div class="z-table-wrap">
            <table style="font-size: 0.85rem;">
                <thead>
                    <tr>
                        <th style="width: 100px;">Tanggal</th>
                        <th>No Bukti</th>
                        <th>Uraian Transaksi</th>
                        <th style="text-align: right;">Penerimaan</th>
                        <th style="text-align: right;">Pengeluaran</th>
                        <th style="text-align: right;">Saldo BKU</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $t_masuk = 0; $t_keluar = 0;
                    if(empty($bku_list)): ?>
                        <tr><td colspan="6" style="text-align:center; padding: 30px; color: #94a3b8;">Belum ada riwayat transaksi.</td></tr>
                    <?php else: 
                        foreach($bku_list as $b): 
                            $t_masuk += floatval($b['penerimaan']);
                            $t_keluar += floatval($b['pengeluaran']);
                    ?>
                    <tr>
                        <td style="font-weight: 600; color: #475569;"><?= date('d/m/Y', strtotime($b['tanggal'])) ?></td>
                        <td><span class="pill pill-gray"><?= htmlspecialchars($b['no_bukti'] ?: '-') ?></span></td>
                        <td>
                            <div style="font-weight: 700; color: #0f172a;"><?= htmlspecialchars($b['uraian']) ?></div>
                            <?php if($b['rkas_id']): ?>
                            <div style="font-size: 0.72rem; color: #0ea5e9; font-weight: 600; margin-top: 3px;">
                                🔗 <?= htmlspecialchars($b['rkas_uraian']) ?>
                            </div>
                            <?php endif; ?>
                            <div style="font-size: 0.72rem; color: #64748b; margin-top: 2px;">
                                Via <?= $b['jenis_transaksi'] ?>
                            </div>
                        </td>
                        <td style="text-align: right; color: #0284c7; font-weight: 700;">
                            <?= ($b['penerimaan'] > 0) ? 'Rp ' . number_format($b['penerimaan'], 0, ',', '.') : '-' ?>
                        </td>
                        <td style="text-align: right; color: #ef4444; font-weight: 700;">
                            <?= ($b['pengeluaran'] > 0) ? 'Rp ' . number_format($b['pengeluaran'], 0, ',', '.') : '-' ?>
                        </td>
                        <td style="text-align: right; font-weight: 800; color: #059669; font-size: 0.95rem;">
                            Rp <?= number_format($b['saldo'], 0, ',', '.') ?>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
                <tfoot>
                    <tr style="background: #f8fafc;">
                        <th colspan="3" style="text-align: right; font-weight: 800;">TOTAL AKUMULASI PADA RINCIAN INI</th>
                        <th style="text-align: right; font-weight: 700; color: #0284c7;">Rp <?= number_format($t_masuk, 0, ',', '.') ?></th>
                        <th style="text-align: right; font-weight: 700; color: #ef4444;">Rp <?= number_format($t_keluar, 0, ',', '.') ?></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() { lucide.createIcons(); });
</script>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
