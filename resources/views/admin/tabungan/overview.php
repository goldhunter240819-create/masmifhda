<?php
$page_title = 'Dashboard Tabungan Siswa';
include __DIR__ . '/_layout_top.php';
?>

<!-- Header Banner Elite Sultan -->
<div class="elite-page-header" style="background: linear-gradient(135deg, #065f46 0%, #10b981 60%, #34d399 100%);">
    <div class="header-bg-shapes"></div>
    <div class="header-content-inner">
        <div>
            <span class="header-badge" style="background: rgba(255,255,255,0.2); color: #fff; border-color: rgba(255,255,255,0.3);">
                MODUL TABUNGAN MADRASAH
            </span>
            <div class="header-text-premium">
                <h1 style="color:#fff;"><i data-lucide="wallet-cards" style="color:#fff;"></i> Total Akumulasi Saldo Tabungan</h1>
                <div style="font-size: 2.4rem; font-weight: 900; color: #fff; margin-top: 8px; letter-spacing: -1px;">
                    Rp <?= number_format($totalSaldo, 0, ',', '.') ?>
                </div>
                <p style="color: rgba(255,255,255,0.85); margin-top: 6px;">Total dana simpanan nasabah aktif yang terdaftar di sistem madrasah</p>
            </div>
        </div>
        <div class="header-actions-premium" style="display:flex; gap:12px; margin-top: 15px;">
            <a href="<?= base_url('admin/tabungan/kasir') ?>" class="e-btn" style="background: #fff; color: #059669; box-shadow: 0 10px 20px rgba(0,0,0,0.15);">
                <i data-lucide="plus-circle"></i> Transaksi Kasir
            </a>
            <a href="<?= base_url('admin/tabungan/siswa') ?>" class="e-btn e-btn-glass" style="color:#fff; border-color: rgba(255,255,255,0.4);">
                <i data-lucide="users"></i> Kelola Rekening
            </a>
        </div>
    </div>
</div>

<!-- Kartu Statistik Utama -->
<div class="z-stats" style="grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px;">
    <!-- Nasabah Aktif -->
    <div class="z-stat" style="border-radius: 20px;">
        <div class="z-stat-left">
            <div class="z-stat-label">Nasabah Pemilik Rekening</div>
            <div class="z-stat-value"><?= number_format($totalNasabah) ?></div>
            <?php $persen = ($totalSiswa > 0) ? round(($totalNasabah / $totalSiswa) * 100, 1) : 0; ?>
            <div class="z-stat-trend text-muted" style="font-size: 0.78rem;">
                dari <?= number_format($totalSiswa) ?> Siswa (<?= $persen ?>% partisipasi)
            </div>
            <div style="width: 100%; height: 6px; background: #e2e8f0; border-radius: 6px; margin-top: 8px; overflow: hidden;">
                <div style="width: <?= $persen ?>%; height: 100%; background: #0ea5e9; border-radius: 6px;"></div>
            </div>
        </div>
        <div class="z-stat-icon zi-blue" style="width: 52px; height: 52px;">
            <i data-lucide="wallet-cards" style="width: 26px; height: 26px;"></i>
        </div>
    </div>

    <!-- Setoran Hari Ini -->
    <div class="z-stat" style="border-radius: 20px;">
        <div class="z-stat-left">
            <div class="z-stat-label">Setoran Masuk Hari Ini</div>
            <div class="z-stat-value" style="color: #10b981;">Rp <?= number_format($setoranHariIni, 0, ',', '.') ?></div>
            <div class="z-stat-trend" style="color: #10b981; font-weight: 700; margin-top: 8px;">
                <span class="pill pill-green" style="font-size: 0.7rem;">Hari Ini</span> Update Langsung
            </div>
        </div>
        <div class="z-stat-icon zi-green" style="width: 52px; height: 52px;">
            <i data-lucide="arrow-down-left" style="width: 26px; height: 26px;"></i>
        </div>
    </div>

    <!-- Penarikan Hari Ini -->
    <div class="z-stat" style="border-radius: 20px;">
        <div class="z-stat-left">
            <div class="z-stat-label">Penarikan Keluar Hari Ini</div>
            <div class="z-stat-value" style="color: #ef4444;">Rp <?= number_format($penarikanHariIni, 0, ',', '.') ?></div>
            <div class="z-stat-trend" style="color: #ef4444; font-weight: 700; margin-top: 8px;">
                <span class="pill pill-red" style="font-size: 0.7rem;"><?= $transaksiHariIni ?> Transaksi</span> Total aktivitas hari ini
            </div>
        </div>
        <div class="z-stat-icon zi-red" style="width: 52px; height: 52px;">
            <i data-lucide="arrow-up-right" style="width: 26px; height: 26px;"></i>
        </div>
    </div>
</div>

<!-- Panel Tabel Mutasi Terbaru -->
<div class="z-panel" style="border-radius: 24px;">
    <div class="z-panel-head" style="display: flex; justify-content: space-between; align-items: center;">
        <div class="z-panel-title">
            <i data-lucide="history"></i>
            <span>Mutasi Tabungan Terakhir (10 Aktivitas Terbaru)</span>
        </div>
        <a href="<?= base_url('admin/tabungan/mutasi') ?>" class="btn btn-ghost btn-sm" style="font-weight: 700;">Lihat Semua Riwayat</a>
    </div>
    <div class="z-table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Waktu & Tanggal</th>
                    <th>Nasabah Siswa</th>
                    <th>Kelas</th>
                    <th>Jenis</th>
                    <th style="text-align: right;">Nominal</th>
                    <th style="text-align: right;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($mutasi_terbaru)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px;">
                        <div style="color: var(--z-muted); margin-bottom: 8px;"><i data-lucide="inbox" style="width:36px;height:36px;opacity:0.4;"></i></div>
                        <div style="font-weight: 700; color: var(--z-text);">Belum ada transaksi mutasi</div>
                        <div style="font-size: 0.8rem; color: var(--z-muted);">Transaksi penyetoran atau penarikan akan tercatat otomatis di sini</div>
                    </td>
                </tr>
                <?php else: foreach($mutasi_terbaru as $m): ?>
                <tr>
                    <td style="color: var(--z-muted); font-size: 0.84rem;">
                        <strong><?= date('d/m/Y', strtotime($m['tanggal'])) ?></strong>
                        <span style="display: block; font-size: 0.76rem;"><?= date('H:i', strtotime($m['tanggal'])) ?></span>
                    </td>
                    <td>
                        <div style="font-weight: 800; color: var(--z-text);"><?= htmlspecialchars($m['nama']) ?></div>
                        <div style="font-size: 0.75rem; color: var(--z-muted);">NIS: <?= htmlspecialchars($m['nis']) ?></div>
                    </td>
                    <td><span class="pill pill-gray"><?= htmlspecialchars($m['nama_kelas'] ?? '-') ?></span></td>
                    <td>
                        <?php if($m['jenis_mutasi'] == 'Setor'): ?>
                            <span class="pill pill-green">
                                <i data-lucide="arrow-down-left" style="width:12px;height:12px;margin-right:3px;"></i>Setor
                            </span>
                        <?php else: ?>
                            <span class="pill pill-red">
                                <i data-lucide="arrow-up-right" style="width:12px;height:12px;margin-right:3px;"></i>Tarik
                            </span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: right; font-weight: 800; font-size: 0.95rem; color: <?= $m['jenis_mutasi']=='Setor'?'#10b981':'#ef4444' ?>;">
                        <?= $m['jenis_mutasi']=='Setor'?'+':'-' ?> Rp <?= number_format($m['jumlah'],0,',','.') ?>
                    </td>
                    <td style="text-align: right; color: var(--z-muted); font-size: 0.84rem;">
                        <?= htmlspecialchars($m['keterangan'] ?: '-') ?>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
