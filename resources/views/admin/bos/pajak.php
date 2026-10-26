<?php
$page_title = 'Buku Pembantu Pajak';
include __DIR__ . '/_layout_top.php';
?>

<div class="z-panel" style="border-radius: 20px;">
    <div class="z-panel-head" style="display: flex; justify-content: space-between; align-items: center;">
        <div class="z-panel-title">
            <i data-lucide="percent"></i> 
            <span>Buku Pembantu Pajak BOS</span>
        </div>
        <div style="font-size: 0.85rem; color: #64748b;">
            Tercatat otomatis dari input BKU
        </div>
    </div>
    <div class="z-panel-body">
        <div class="z-table-wrap">
            <table style="font-size: 0.85rem;">
                <thead>
                    <tr>
                        <th style="width: 100px;">Tanggal</th>
                        <th>No Bukti / Referensi</th>
                        <th>Uraian Transaksi</th>
                        <th style="text-align: right;">PPN</th>
                        <th style="text-align: right;">PPh 21</th>
                        <th style="text-align: right;">PPh 22</th>
                        <th style="text-align: right;">PPh 23</th>
                        <th style="text-align: right;">Total Pajak</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $t_ppn = 0; $t_pph21 = 0; $t_pph22 = 0; $t_pph23 = 0;
                    if(empty($pajak_list)): ?>
                        <tr><td colspan="8" style="text-align:center; padding: 30px; color: #94a3b8;">Belum ada riwayat pemungutan pajak.</td></tr>
                    <?php else: 
                        foreach($pajak_list as $p): 
                            $ppn = floatval($p['pajak_ppn']);
                            $pph21 = floatval($p['pajak_pph21']);
                            $pph22 = floatval($p['pajak_pph22']);
                            $pph23 = floatval($p['pajak_pph23']);
                            $total = $ppn + $pph21 + $pph22 + $pph23;
                            
                            $t_ppn += $ppn; $t_pph21 += $pph21; $t_pph22 += $pph22; $t_pph23 += $pph23;
                    ?>
                    <tr>
                        <td style="font-weight: 600; color: #475569;"><?= date('d/m/Y', strtotime($p['tanggal'])) ?></td>
                        <td><span class="pill pill-gray"><?= htmlspecialchars($p['no_bukti'] ?: '-') ?></span></td>
                        <td>
                            <div style="font-weight: 700; color: #0f172a;"><?= htmlspecialchars($p['uraian']) ?></div>
                        </td>
                        <td style="text-align: right; color: #0284c7;"><?= $ppn > 0 ? 'Rp '.number_format($ppn, 0, ',', '.') : '-' ?></td>
                        <td style="text-align: right; color: #f59e0b;"><?= $pph21 > 0 ? 'Rp '.number_format($pph21, 0, ',', '.') : '-' ?></td>
                        <td style="text-align: right; color: #d97706;"><?= $pph22 > 0 ? 'Rp '.number_format($pph22, 0, ',', '.') : '-' ?></td>
                        <td style="text-align: right; color: #b45309;"><?= $pph23 > 0 ? 'Rp '.number_format($pph23, 0, ',', '.') : '-' ?></td>
                        <td style="text-align: right; font-weight: 800; color: #059669;">Rp <?= number_format($total, 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
                <tfoot>
                    <tr style="background: #f8fafc;">
                        <th colspan="3" style="text-align: right; font-weight: 800;">TOTAL AKUMULASI PAJAK</th>
                        <th style="text-align: right; font-weight: 700; color: #0284c7;">Rp <?= number_format($t_ppn, 0, ',', '.') ?></th>
                        <th style="text-align: right; font-weight: 700; color: #f59e0b;">Rp <?= number_format($t_pph21, 0, ',', '.') ?></th>
                        <th style="text-align: right; font-weight: 700; color: #d97706;">Rp <?= number_format($t_pph22, 0, ',', '.') ?></th>
                        <th style="text-align: right; font-weight: 700; color: #b45309;">Rp <?= number_format($t_pph23, 0, ',', '.') ?></th>
                        <th style="text-align: right; font-weight: 900; font-size: 1.1rem; color: #059669;">Rp <?= number_format($t_ppn + $t_pph21 + $t_pph22 + $t_pph23, 0, ',', '.') ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
