<?php
$page_title = 'Riwayat Mutasi Tabungan';
include __DIR__ . '/_layout_top.php';
?>

<!-- Header Banner Elite Sultan -->
<div class="elite-page-header" style="background: linear-gradient(135deg, #0f172a 0%, #334155 100%);">
    <div class="header-bg-shapes"></div>
    <div class="header-content-inner">
        <div>
            <span class="header-badge" style="background: rgba(56,189,248,0.2); color: #38bdf8; border-color: rgba(56,189,248,0.3);">
                LOG AUDIT TRANSAKSI
            </span>
            <div class="header-text-premium">
                <h1 style="color:#fff;"><i data-lucide="history" style="color:#38bdf8;"></i> Riwayat Mutasi & Buku Tabungan</h1>
                <p style="color: rgba(255,255,255,0.75);">Log catatan penyetoran dan penarikan saldo seluruh nasabah</p>
            </div>
        </div>
        <div class="header-actions-premium" style="display:flex; gap:12px; margin-top: 15px;">
            <a href="<?= base_url('admin/tabungan/kasir') ?>" class="e-btn" style="background: #fff; color: #0f172a;">
                <i data-lucide="plus"></i> Transaksi Baru
            </a>
        </div>
    </div>
</div>

<div class="z-panel" style="border-radius: 24px;">
    <div class="z-panel-head" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div class="z-panel-title">
            <i data-lucide="list-checks"></i>
            <span>Daftar Mutasi Transaksi (500 Terakhir)</span>
        </div>
        <div>
            <input type="text" id="filterMutasi" class="z-input" placeholder="Cari Siswa / Jenis / Keterangan..." style="width: 280px;">
        </div>
    </div>
    <div class="z-table-wrap">
        <table id="tableMutasi">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Waktu</th>
                    <th>Nasabah Siswa</th>
                    <th>Jenis</th>
                    <th style="text-align: right;">Nominal</th>
                    <th>Keterangan</th>
                    <th style="text-align: right;">Saldo Akhir</th>
                    <?php if($can_delete): ?><th style="text-align: center;">Aksi</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($mutasi_list)): ?>
                <tr>
                    <td colspan="<?= $can_delete ? '8' : '7' ?>" style="text-align: center; padding: 45px;">
                        <div style="color: var(--z-muted); margin-bottom: 10px;"><i data-lucide="inbox" style="width:44px;height:44px;opacity:0.3;"></i></div>
                        <div style="font-weight: 800; color: var(--z-text); font-size: 1.05rem;">Belum Ada Riwayat Mutasi</div>
                        <div style="font-size: 0.85rem; color: var(--z-muted);">Semua transaksi masuk dan keluar tabungan akan otomatis tercatat di sini</div>
                    </td>
                </tr>
                <?php else: $no=1; foreach($mutasi_list as $m): ?>
                <tr>
                    <td style="color: var(--z-muted); font-size: 0.82rem;"><?= $no++ ?></td>
                    <td style="color: var(--z-muted); font-size: 0.84rem;">
                        <strong><?= date('d/m/Y', strtotime($m['tanggal'])) ?></strong>
                        <span style="display: block; font-size: 0.75rem;"><?= date('H:i', strtotime($m['tanggal'])) ?></span>
                    </td>
                    <td>
                        <div style="font-weight: 800; color: var(--z-text);"><?= htmlspecialchars($m['nama']) ?></div>
                        <div style="font-size:0.75rem; color: var(--z-muted);">NIS: <?= htmlspecialchars($m['nis']) ?> &bull; <?= htmlspecialchars($m['nama_kelas'] ?? '-') ?></div>
                    </td>
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
                        <?= $m['jenis_mutasi']=='Setor'?'+':'-' ?> Rp <?= number_format($m['jumlah'], 0, ',', '.') ?>
                    </td>
                    <td style="color: var(--z-muted); font-size: 0.85rem;"><?= htmlspecialchars($m['keterangan'] ?: '-') ?></td>
                    <td style="text-align: right; font-weight: 800; color: var(--z-text); font-size: 0.92rem;">
                        Rp <?= number_format($m['saldo_sesudah'], 0, ',', '.') ?>
                    </td>
                    <?php if($can_delete): ?>
                    <td style="text-align: center;">
                        <button onclick="hapusMutasi(<?= $m['id'] ?>)" class="e-btn" style="background: #fee2e2; color: #ef4444; padding: 5px 10px; font-size: 0.75rem;">
                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i> Hapus
                        </button>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    
    var filterMutasi = document.getElementById('filterMutasi');
    if (filterMutasi) {
        filterMutasi.addEventListener('input', function() {
            var filter = this.value.toLowerCase();
            var rows = document.querySelectorAll('#tableMutasi tbody tr');
            rows.forEach(function(row) {
                var text = row.textContent.toLowerCase();
                row.style.display = text.indexOf(filter) > -1 ? '' : 'none';
            });
        });
    }
});

function hapusMutasi(id) {
    if(confirm('Yakin ingin menghapus transaksi ini? Saldo tabungan siswa akan otomatis disesuaikan (dikembalikan).')) {
        var formData = new FormData();
        formData.append('id', id);
        fetch('<?= base_url("admin/tabungan/mutasi/delete") ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Gagal: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghubungi server.');
        });
    }
}
</script>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
