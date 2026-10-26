<?php
$page_title = 'Rencana Kegiatan & Anggaran Sekolah (RKAS)';
include __DIR__ . '/_layout_top.php';
?>

<div class="z-panel" style="border-radius: 20px;">
    <div class="z-panel-head" style="display: flex; justify-content: space-between; align-items: center;">
        <div class="z-panel-title">
            <i data-lucide="file-spreadsheet"></i> 
            <span>Data RKAS Tahun <?= htmlspecialchars($tahun) ?></span>
        </div>
        <div>
            <form method="GET" style="display: inline-block; margin-right: 10px;">
                <select name="tahun" class="e-input" style="width: 120px; padding: 6px 12px; font-weight: 700;" onchange="this.form.submit()">
                    <?php for($y = date('Y') + 1; $y >= 2020; $y--): ?>
                        <option value="<?= $y ?>" <?= ($tahun == $y) ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </form>
            <button class="e-btn" style="background: #0ea5e9; color: #fff;" onclick="document.getElementById('modalTambahRKAS').style.display='flex'">
                <i data-lucide="plus"></i> Tambah Rencana
            </button>
        </div>
    </div>
    <div class="z-panel-body">
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'saved'): ?>
            <div style="padding: 12px 20px; background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; border-radius: 10px; margin-bottom: 20px; font-weight: 700; font-size: 0.9rem;">
                ✅ Data RKAS berhasil disimpan!
            </div>
        <?php endif; ?>

        <div class="z-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Standar Nasional</th>
                        <th>Kode Rekening</th>
                        <th>Uraian Kegiatan / Barang</th>
                        <th style="text-align: right;">Volume</th>
                        <th style="text-align: right;">Harga Satuan</th>
                        <th style="text-align: right;">Total Biaya</th>
                        <th>Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grandTotal = 0;
                    if(empty($rkas_list)): ?>
                        <tr><td colspan="7" style="text-align:center; padding: 30px; color: #94a3b8;">Belum ada data RKAS untuk tahun ini.</td></tr>
                    <?php else: 
                        foreach($rkas_list as $r): 
                            $grandTotal += floatval($r['total']);
                    ?>
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: #0284c7;">Standar <?= $r['snp_id'] ?></div>
                            <div style="font-size: 0.75rem; color: #64748b;"><?= $standar_nasional[$r['snp_id']] ?></div>
                        </td>
                        <td><span class="pill pill-gray"><?= htmlspecialchars($r['kode_rekening'] ?: '-') ?></span></td>
                        <td style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($r['uraian']) ?></td>
                        <td style="text-align: right;"><?= $r['volume'] ?> <span style="font-size: 0.75rem; color: #94a3b8;"><?= htmlspecialchars($r['satuan']) ?></span></td>
                        <td style="text-align: right; color: #64748b;">Rp <?= number_format($r['harga_satuan'], 0, ',', '.') ?></td>
                        <td style="text-align: right; font-weight: 800; color: #059669; font-size: 1.05rem;">Rp <?= number_format($r['total'], 0, ',', '.') ?></td>
                        <td>
                            <a href="<?= base_url('admin/bos/rkas/delete/' . $r['id']) ?>" onclick="return confirm('Yakin menghapus rencana ini?')" class="btn btn-ghost btn-sm" style="color: #ef4444; padding: 5px;">
                                <i data-lucide="trash-2" style="width:16px;height:16px;"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
                <tfoot>
                    <tr style="background: #f8fafc;">
                        <th colspan="5" style="text-align: right; font-weight: 800; font-size: 1.1rem; color: #1e293b;">TOTAL RKAS TAHUN <?= $tahun ?></th>
                        <th style="text-align: right; font-weight: 900; font-size: 1.25rem; color: #059669;">Rp <?= number_format($grandTotal, 0, ',', '.') ?></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah RKAS -->
<div id="modalTambahRKAS" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="background: #fff; width: 100%; max-width: 600px; border-radius: 20px; box-shadow: 0 25px 50px rgba(0,0,0,0.15); overflow: hidden;">
        <div style="padding: 20px 25px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800; color: #0f172a;">Tambah Rencana Anggaran</h3>
            <button onclick="document.getElementById('modalTambahRKAS').style.display='none'" style="background: none; border: none; cursor: pointer; color: #94a3b8;"><i data-lucide="x"></i></button>
        </div>
        <div style="padding: 25px;">
            <form action="<?= base_url('admin/bos/rkas/save') ?>" method="POST">
                <input type="hidden" name="tahun_anggaran" value="<?= $tahun ?>">
                
                <div style="margin-bottom: 15px;">
                    <label class="e-label">Standar Nasional Pendidikan</label>
                    <select name="snp_id" class="e-input" required>
                        <?php foreach($standar_nasional as $id => $nama): ?>
                            <option value="<?= $id ?>"><?= $id ?>. <?= $nama ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label class="e-label">Kode Rekening</label>
                        <input type="text" name="kode_rekening" class="e-input" placeholder="Opsional">
                    </div>
                    <div>
                        <label class="e-label">Uraian / Nama Kegiatan</label>
                        <input type="text" name="uraian" class="e-input" required placeholder="Contoh: Pembelian Buku K-Merdeka">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 2fr; gap: 15px; margin-bottom: 25px;">
                    <div>
                        <label class="e-label">Volume</label>
                        <input type="number" name="volume" class="e-input" value="1" required min="1">
                    </div>
                    <div>
                        <label class="e-label">Satuan</label>
                        <input type="text" name="satuan" class="e-input" placeholder="Eks, Paket, Ls" required>
                    </div>
                    <div>
                        <label class="e-label">Harga Satuan (Rp)</label>
                        <input type="text" name="harga_satuan" class="e-input" required placeholder="150000" onkeyup="this.value=formatRupiah(this.value)">
                    </div>
                </div>

                <div style="text-align: right;">
                    <button type="button" class="btn btn-ghost" onclick="document.getElementById('modalTambahRKAS').style.display='none'">Batal</button>
                    <button type="submit" class="e-btn" style="background: #0ea5e9; color: #fff; margin-left: 10px;">Simpan RKAS</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() { lucide.createIcons(); });

function formatRupiah(angka) {
    var number_string = angka.replace(/[^,\d]/g, '').toString(),
    split = number_string.split(','),
    sisa = split[0].length % 3,
    rupiah = split[0].substr(0, sisa),
    ribuan = split[0].substr(sisa).match(/\d{3}/gi);
    if(ribuan){
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return rupiah;
}
</script>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
