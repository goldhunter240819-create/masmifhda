<?php include __DIR__ . '/_layout_top.php'; ?>

<div style="background: #fff; border-radius: 20px; padding: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.03);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; font-size: 1.4rem; color: #1e293b;"><i data-lucide="file-text" style="width: 24px; height: 24px; vertical-align: middle; color: #8b5cf6;"></i> E-Proposal & LPJ</h2>
        <a href="<?= base_url('osim/proposal/templates') ?>" class="e-btn" style="background: #8b5cf6; color: #fff; border: none; cursor: pointer; text-decoration: none; padding: 10px 20px; border-radius: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px;">
            <i data-lucide="plus"></i> Buat Proposal
        </a>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 15px; font-weight: 700; color: #475569;">Judul Proposal</th>
                    <th style="padding: 15px; font-weight: 700; color: #475569;">Waktu & Tempat</th>
                    <th style="padding: 15px; font-weight: 700; color: #475569;">Tanggal Dibuat</th>
                    <th style="padding: 15px; font-weight: 700; color: #475569;">Status Approval</th>
                    <th style="padding: 15px; font-weight: 700; color: #475569; width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($proposal)): ?>
                    <tr><td colspan="5" style="text-align: center; padding: 30px; color: #94a3b8;">Belum ada proposal yang dibuat.</td></tr>
                <?php else: foreach ($proposal as $p): ?>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 15px; font-weight: 700; color: #1e293b;"><?= htmlspecialchars($p['judul']) ?></td>
                    <td style="padding: 15px; color: #64748b; font-size: 0.9rem; max-width: 200px;"><?= htmlspecialchars($p['waktu_tempat']) ?></td>
                    <td style="padding: 15px; color: #1e293b; font-weight: 700; font-size: 0.9rem;">
                        <i data-lucide="clock" style="width:14px;height:14px;vertical-align:text-bottom;color:#8b5cf6;"></i> 
                        <?= date('d M Y', strtotime($p['created_at'])) ?>
                    </td>
                    <td style="padding: 15px;">
                        <?php 
                        $status_color = '#64748b'; $status_bg = '#f1f5f9';
                        if ($p['status'] == 'Draft') { $status_color = '#64748b'; $status_bg = '#f1f5f9'; }
                        if ($p['status'] == 'Diajukan') { $status_color = '#0ea5e9'; $status_bg = '#e0f2fe'; }
                        if (strpos($p['status'], 'Disetujui') !== false) { $status_color = '#16a34a'; $status_bg = '#dcfce7'; }
                        if ($p['status'] == 'Ditolak') { $status_color = '#ef4444'; $status_bg = '#fee2e2'; }
                        ?>
                        <span style="background: <?= $status_bg ?>; color: <?= $status_color ?>; padding: 4px 10px; border-radius: 50px; font-size: 0.8rem; font-weight: 800;">
                            <?= htmlspecialchars($p['status']) ?>
                        </span>
                    </td>
                    <td style="padding: 15px; white-space: nowrap;">
                        <a href="<?= base_url('osim/proposal/hapus?id=' . $p['id']) ?>" onclick="return confirm('Hapus proposal ini?')" style="color: #ef4444; padding: 5px; text-decoration: none;" title="Hapus">
                            <i data-lucide="trash-2" style="width: 18px; height: 18px;"></i>
                        </a>
                    </td>
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
<div id="modalAdd" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #fff; width: 100%; max-width: 600px; max-height: 90vh; overflow-y: auto; border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; position: sticky; top: 0; background: #fff; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;">
            <h3 style="margin: 0;" id="modalTitle">Buat E-Proposal Baru</h3>
            <button onclick="document.getElementById('modalAdd').style.display='none'" style="background: none; border: none; cursor: pointer; color: #94a3b8;"><i data-lucide="x"></i></button>
        </div>
        <form action="<?= base_url('osim/proposal/simpan') ?>" method="POST" id="formProposal">
            <input type="hidden" name="id" id="proposal_id">
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Judul Kegiatan</label>
                <input type="text" name="judul" id="judul" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Latar Belakang</label>
                <textarea name="latar_belakang" id="latar_belakang" rows="3" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;"></textarea>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Tujuan Kegiatan</label>
                <textarea name="tujuan" id="tujuan" rows="3" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;"></textarea>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Waktu & Tempat</label>
                <input type="text" name="waktu_tempat" id="waktu_tempat" placeholder="Contoh: 12 Agustus 2026, Aula Madrasah" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Susunan Panitia</label>
                <textarea name="susunan_panitia" id="susunan_panitia" rows="3" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;"></textarea>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Rencana Anggaran Biaya (RAB)</label>
                <textarea name="rab" id="rab" rows="3" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;"></textarea>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Status Approval</label>
                <select name="status" id="status" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px;">
                    <option value="Draft">Draft (Belum diajukan)</option>
                    <option value="Diajukan">Ajukan ke Pembina</option>
                    <option value="Disetujui Pembina">Disetujui Pembina</option>
                    <option value="Disetujui Kepala Madrasah">Disetujui Kepala Madrasah</option>
                    <option value="Ditolak">Ditolak / Revisi</option>
                </select>
            </div>

            <div style="text-align: right;">
                <button type="button" onclick="document.getElementById('modalAdd').style.display='none'" class="e-btn" style="background: #f1f5f9; color: #64748b; border: none; cursor: pointer; margin-right: 10px;">Batal</button>
                <button type="submit" class="e-btn" style="background: #8b5cf6; color: #fff; border: none; cursor: pointer;">Simpan Proposal</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('modalAdd').style.display = 'none';

    function editProposal(data) {
        document.getElementById('modalTitle').textContent = 'Edit Proposal';
        document.getElementById('proposal_id').value = data.id;
        document.getElementById('judul').value = data.judul;
        document.getElementById('latar_belakang').value = data.latar_belakang;
        document.getElementById('tujuan').value = data.tujuan;
        document.getElementById('waktu_tempat').value = data.waktu_tempat;
        document.getElementById('susunan_panitia').value = data.susunan_panitia;
        document.getElementById('rab').value = data.rab;
        document.getElementById('status').value = data.status;
        document.getElementById('modalAdd').style.display = 'flex';
    }
</script>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
