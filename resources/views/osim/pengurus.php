<?php include __DIR__ . '/_layout_top.php'; ?>

<div style="background: #fff; border-radius: 20px; padding: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.03);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; font-size: 1.4rem; color: #1e293b;"><i data-lucide="contact" style="width: 24px; height: 24px; vertical-align: middle; color: #0d9488;"></i> Data Kepengurusan OSIM</h2>
        <button onclick="document.getElementById('modalAdd').style.display='block'" class="e-btn" style="background: #0d9488; color: #fff; border: none; cursor: pointer;">
            <i data-lucide="plus"></i> Tambah Pengurus
        </button>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 15px; font-weight: 700; color: #475569;">Siswa</th>
                    <th style="padding: 15px; font-weight: 700; color: #475569;">NIS / Kelas</th>
                    <th style="padding: 15px; font-weight: 700; color: #475569;">Jabatan</th>
                    <th style="padding: 15px; font-weight: 700; color: #475569;">Periode</th>
                    <th style="padding: 15px; font-weight: 700; color: #475569; width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pengurus)): ?>
                    <tr><td colspan="5" style="text-align: center; padding: 30px; color: #94a3b8;">Belum ada data pengurus.</td></tr>
                <?php else: foreach ($pengurus as $p): ?>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 15px; font-weight: 700; color: #1e293b;"><?= htmlspecialchars($p['nama']) ?></td>
                    <td style="padding: 15px; color: #64748b; font-size: 0.9rem;"><?= htmlspecialchars($p['nis']) ?> - <?= htmlspecialchars($p['nama_kelas'] ?? '-') ?></td>
                    <td style="padding: 15px;">
                        <span style="background: #f0fdfa; color: #0d9488; padding: 4px 10px; border-radius: 50px; font-size: 0.8rem; font-weight: 700;">
                            <?= htmlspecialchars($p['jabatan']) ?>
                        </span>
                    </td>
                    <td style="padding: 15px; color: #64748b; font-weight: 700;"><?= htmlspecialchars($p['periode']) ?></td>
                    <td style="padding: 15px;">
                        <a href="<?= base_url('osim/pengurus/hapus?id=' . $p['id']) ?>" onclick="return confirm('Hapus data ini?')" style="color: #ef4444; padding: 5px; text-decoration: none;" title="Hapus">
                            <i data-lucide="trash-2" style="width: 18px; height: 18px;"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div id="modalAdd" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center;">
    <div style="background: #fff; width: 100%; max-width: 500px; margin: 10vh auto; border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0;">Tambah Pengurus</h3>
            <button onclick="document.getElementById('modalAdd').style.display='none'" style="background: none; border: none; cursor: pointer; color: #94a3b8;"><i data-lucide="x"></i></button>
        </div>
        <form action="<?= base_url('osim/pengurus/simpan') ?>" method="POST">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Pilih Siswa</label>
                <select name="siswa_id" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px;">
                    <option value="">-- Pilih Siswa --</option>
                    <?php foreach($siswa_list as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama'] . ' ('.$s['nis'].')') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Jabatan</label>
                <input type="text" name="jabatan" placeholder="Contoh: Ketua OSIM / Sekbid Olahraga" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Periode Masa Bakti</label>
                <input type="text" name="periode" placeholder="Contoh: 2026/2027" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;">
            </div>
            <div style="text-align: right;">
                <button type="button" onclick="document.getElementById('modalAdd').style.display='none'" class="e-btn" style="background: #f1f5f9; color: #64748b; border: none; cursor: pointer; margin-right: 10px;">Batal</button>
                <button type="submit" class="e-btn" style="background: #0d9488; color: #fff; border: none; cursor: pointer;">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('modalAdd').style.display = 'none';
</script>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
