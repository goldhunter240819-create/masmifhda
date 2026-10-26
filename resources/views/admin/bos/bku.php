<?php
$page_title = 'Buku Kas Umum (BKU)';
include __DIR__ . '/_layout_top.php';
?>

<div class="z-panel" style="border-radius: 20px;">
    <div class="z-panel-head" style="display: flex; justify-content: space-between; align-items: center;">
        <div class="z-panel-title">
            <i data-lucide="book-open"></i> 
            <span>Buku Kas Umum & Pajak Berjalan</span>
        </div>
        <div>
            <button class="e-btn" style="background: #0ea5e9; color: #fff;" onclick="document.getElementById('modalTambahBKU').style.display='flex'">
                <i data-lucide="plus"></i> Catat Transaksi
            </button>
        </div>
    </div>
    <div class="z-panel-body">
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'saved'): ?>
            <div style="padding: 12px 20px; background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; border-radius: 10px; margin-bottom: 20px; font-weight: 700; font-size: 0.9rem;">
                ✅ Transaksi BKU berhasil disimpan!
            </div>
        <?php endif; ?>

        <div class="z-table-wrap">
            <table style="font-size: 0.85rem;">
                <thead>
                    <tr>
                        <th style="width: 100px;">Tanggal</th>
                        <th>No Bukti</th>
                        <th>Uraian Transaksi</th>
                        <th style="text-align: right;">Penerimaan</th>
                        <th style="text-align: right;">Pengeluaran</th>
                        <th style="text-align: right;">Saldo</th>
                        <th style="text-align: right;">Pajak (Total)</th>
                        <th>Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(empty($bku_list)): ?>
                        <tr><td colspan="8" style="text-align:center; padding: 30px; color: #94a3b8;">Belum ada riwayat transaksi di Buku Kas Umum.</td></tr>
                    <?php else: 
                        foreach($bku_list as $b): 
                            $tot_pajak = floatval($b['pajak_ppn']) + floatval($b['pajak_pph21']) + floatval($b['pajak_pph22']) + floatval($b['pajak_pph23']);
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
                        <td style="text-align: right; color: #f59e0b; font-weight: 600;">
                            <?= ($tot_pajak > 0) ? 'Rp ' . number_format($tot_pajak, 0, ',', '.') : '-' ?>
                        </td>
                        <td>
                            <a href="<?= base_url('admin/bos/bku/delete/' . $b['id']) ?>" onclick="return confirm('Peringatan: Menghapus transaksi ini tidak akan mengubah histori saldo secara otomatis. Yakin hapus?')" class="btn btn-ghost btn-sm" style="color: #ef4444; padding: 5px;">
                                <i data-lucide="trash-2" style="width:16px;height:16px;"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah BKU -->
<div id="modalTambahBKU" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="background: #fff; width: 100%; max-width: 650px; border-radius: 20px; box-shadow: 0 25px 50px rgba(0,0,0,0.15); overflow: hidden; display: flex; flex-direction: column; max-height: 90vh;">
        <div style="padding: 20px 25px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800; color: #0f172a;">Input Transaksi BKU</h3>
            <button onclick="document.getElementById('modalTambahBKU').style.display='none'" style="background: none; border: none; cursor: pointer; color: #94a3b8;"><i data-lucide="x"></i></button>
        </div>
        <div style="padding: 25px; overflow-y: auto;">
            <form action="<?= base_url('admin/bos/bku/save') ?>" method="POST">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label class="e-label">Tanggal Transaksi</label>
                        <input type="date" name="tanggal" class="e-input" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div>
                        <label class="e-label">No Bukti (Kwitansi/Faktur)</label>
                        <input type="text" name="no_bukti" class="e-input" placeholder="Contoh: BKT-001">
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label class="e-label">Uraian Transaksi</label>
                    <textarea name="uraian" class="e-input" rows="2" required placeholder="Contoh: Pencairan Dana BOS Tahap 1 atau Pembayaran Alat Tulis"></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                    <div>
                        <label class="e-label">Jenis Kas</label>
                        <select name="jenis_transaksi" class="e-input" required>
                            <option value="Tunai">Tunai / Kas di Tangan</option>
                            <option value="Bank">Transfer / Kas di Bank</option>
                        </select>
                    </div>
                    <div>
                        <label class="e-label">Tipe Transaksi</label>
                        <select name="tipe" class="e-input" required onchange="toggleRKAS(this.value)">
                            <option value="pengeluaran">Pengeluaran (Belanja)</option>
                            <option value="penerimaan">Penerimaan (Pemasukan BOS)</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 20px; padding: 15px; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;" id="box_nominal">
                    <label class="e-label" style="font-size: 0.9rem;">Nominal (Rp)</label>
                    <input type="text" name="nominal" class="e-input" style="font-size: 1.25rem; font-weight: 800; color: #0f172a;" required placeholder="0" onkeyup="this.value=formatRupiah(this.value)">
                    
                    <div id="rkas_selector" style="margin-top: 15px;">
                        <label class="e-label">Tautkan ke RKAS (Realisasi Belanja)</label>
                        <select name="rkas_id" class="e-input">
                            <option value="">-- Tidak ditautkan (Tanpa RKAS) --</option>
                            <?php foreach($rkas_dropdown as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['uraian']) ?> (Anggaran: Rp <?= number_format($r['total'],0,',','.') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Input Pajak (Disembunyikan jika penerimaan) -->
                <div id="box_pajak" style="margin-bottom: 20px;">
                    <div style="font-size: 0.8rem; font-weight: 800; color: #64748b; margin-bottom: 10px; text-transform: uppercase;">Potongan Pajak (Opsional)</div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label class="e-label" style="font-size: 0.75rem;">PPN (Rp)</label>
                            <input type="text" name="pajak_ppn" class="e-input e-input-sm" placeholder="0" onkeyup="this.value=formatRupiah(this.value)">
                        </div>
                        <div>
                            <label class="e-label" style="font-size: 0.75rem;">PPh 21 (Rp)</label>
                            <input type="text" name="pajak_pph21" class="e-input e-input-sm" placeholder="0" onkeyup="this.value=formatRupiah(this.value)">
                        </div>
                        <div>
                            <label class="e-label" style="font-size: 0.75rem;">PPh 22 (Rp)</label>
                            <input type="text" name="pajak_pph22" class="e-input e-input-sm" placeholder="0" onkeyup="this.value=formatRupiah(this.value)">
                        </div>
                        <div>
                            <label class="e-label" style="font-size: 0.75rem;">PPh 23 (Rp)</label>
                            <input type="text" name="pajak_pph23" class="e-input e-input-sm" placeholder="0" onkeyup="this.value=formatRupiah(this.value)">
                        </div>
                    </div>
                </div>

                <div style="text-align: right; padding-top: 15px; border-top: 1px solid #f1f5f9;">
                    <button type="button" class="btn btn-ghost" onclick="document.getElementById('modalTambahBKU').style.display='none'">Batal</button>
                    <button type="submit" class="e-btn" style="background: #0ea5e9; color: #fff; margin-left: 10px;">Simpan Transaksi</button>
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

function toggleRKAS(tipe) {
    if (tipe === 'penerimaan') {
        document.getElementById('rkas_selector').style.display = 'none';
        document.getElementById('box_pajak').style.display = 'none';
    } else {
        document.getElementById('rkas_selector').style.display = 'block';
        document.getElementById('box_pajak').style.display = 'block';
    }
}
</script>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
