<?php include __DIR__ . '/_layout_top.php'; ?>

<div style="background: #fff; border-radius: 20px; padding: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.03);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; font-size: 1.4rem; color: #1e293b;"><i data-lucide="briefcase" style="width: 24px; height: 24px; vertical-align: middle; color: #0ea5e9;"></i> Program Kerja OSIM</h2>
        <button onclick="document.getElementById('modalAdd').style.display='block'" class="e-btn" style="background: #0ea5e9; color: #fff; border: none; cursor: pointer;">
            <i data-lucide="plus"></i> Tambah Proker
        </button>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 15px; font-weight: 700; color: #475569;">Kegiatan</th>
                    <th style="padding: 15px; font-weight: 700; color: #475569;">Deskripsi</th>
                    <th style="padding: 15px; font-weight: 700; color: #475569;">Pelaksanaan</th>
                    <th style="padding: 15px; font-weight: 700; color: #475569;">Status</th>
                    <th style="padding: 15px; font-weight: 700; color: #475569; width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($proker)): ?>
                    <tr><td colspan="5" style="text-align: center; padding: 30px; color: #94a3b8;">Belum ada proker yang dibuat.</td></tr>
                <?php else: foreach ($proker as $p): ?>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 15px; font-weight: 700; color: #1e293b;"><?= htmlspecialchars($p['nama_kegiatan']) ?></td>
                    <td style="padding: 15px; color: #64748b; font-size: 0.9rem; max-width: 250px;"><?= htmlspecialchars($p['deskripsi']) ?></td>
                    <td style="padding: 15px; color: #1e293b; font-weight: 700; font-size: 0.9rem;">
                        <i data-lucide="calendar" style="width:14px;height:14px;vertical-align:text-bottom;color:#0ea5e9;"></i> 
                        <?= date('d M Y', strtotime($p['tanggal_pelaksanaan'])) ?>
                    </td>
                    <td style="padding: 15px;">
                        <?php 
                        $status_color = '#64748b'; $status_bg = '#f1f5f9';
                        if ($p['status'] == 'Draft') { $status_color = '#64748b'; $status_bg = '#f1f5f9'; }
                        if ($p['status'] == 'Persiapan') { $status_color = '#d97706'; $status_bg = '#fef3c7'; }
                        if ($p['status'] == 'Berjalan') { $status_color = '#2563eb'; $status_bg = '#dbeafe'; }
                        if ($p['status'] == 'Selesai') { $status_color = '#16a34a'; $status_bg = '#dcfce7'; }
                        ?>
                        <span style="background: <?= $status_bg ?>; color: <?= $status_color ?>; padding: 4px 10px; border-radius: 50px; font-size: 0.8rem; font-weight: 800; text-transform: uppercase;">
                            <?= htmlspecialchars($p['status']) ?>
                        </span>
                    </td>
                    <td style="padding: 15px; white-space: nowrap;">
                        <button onclick='editProker(<?= json_encode($p) ?>)' style="color: #f59e0b; background:none; border:none; padding: 5px; cursor:pointer;" title="Edit"><i data-lucide="edit" style="width: 18px; height: 18px;"></i></button>
                        <a href="<?= base_url('osim/proker/hapus?id=' . $p['id']) ?>" onclick="return confirm('Hapus proker ini?')" style="color: #ef4444; padding: 5px; text-decoration: none;" title="Hapus">
                            <i data-lucide="trash-2" style="width: 18px; height: 18px;"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah / Edit -->
<div id="modalAdd" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center;">
    <div style="background: #fff; width: 100%; max-width: 500px; margin: 10vh auto; border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0;" id="modalTitle">Tambah Proker</h3>
            <button onclick="document.getElementById('modalAdd').style.display='none'" style="background: none; border: none; cursor: pointer; color: #94a3b8;"><i data-lucide="x"></i></button>
        </div>
        <form action="<?= base_url('osim/proker/simpan') ?>" method="POST" id="formProker">
            <input type="hidden" name="id" id="proker_id">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Nama Kegiatan</label>
                <input type="text" name="nama_kegiatan" id="nama_kegiatan" placeholder="Contoh: Latihan Dasar Kepemimpinan (LDK)" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Deskripsi Singkat</label>
                <textarea name="deskripsi" id="deskripsi" rows="3" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;"></textarea>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Tanggal Pelaksanaan</label>
                <input type="date" name="tanggal_pelaksanaan" id="tanggal_pelaksanaan" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Status</label>
                <select name="status" id="status" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px;">
                    <option value="Draft">Draft</option>
                    <option value="Persiapan">Persiapan</option>
                    <option value="Berjalan">Berjalan</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </div>
            <div style="text-align: right;">
                <button type="button" onclick="document.getElementById('modalAdd').style.display='none'" class="e-btn" style="background: #f1f5f9; color: #64748b; border: none; cursor: pointer; margin-right: 10px;">Batal</button>
                <button type="submit" class="e-btn" style="background: #0ea5e9; color: #fff; border: none; cursor: pointer;">Simpan Proker</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('modalAdd').style.display = 'none';

    function editProker(data) {
        document.getElementById('modalTitle').textContent = 'Edit Proker';
        document.getElementById('proker_id').value = data.id;
        document.getElementById('nama_kegiatan').value = data.nama_kegiatan;
        document.getElementById('deskripsi').value = data.deskripsi;
        document.getElementById('tanggal_pelaksanaan').value = data.tanggal_pelaksanaan;
        document.getElementById('status').value = data.status;
        document.getElementById('modalAdd').style.display = 'flex';
    }
</script>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
