<?php include __DIR__ . '/_layout_top.php'; ?>

<div class="elite-page-header" style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);">
    <div class="header-bg-shapes"></div>
    <div class="header-content-inner">
        <div>
            <span class="header-badge" style="background: rgba(255,255,255,0.2); color: #fff; border-color: rgba(255,255,255,0.3);">
                MODUL E-VOTING OSIM
            </span>
            <div class="header-text-premium">
                <h1 style="color:#fff; font-size: 2rem; margin: 0;"><i data-lucide="users" style="color:#fff; vertical-align: middle;"></i> Data Kandidat</h1>
                <p style="color: rgba(255,255,255,0.85); margin-top: 10px; font-size: 1.05rem;">
                    Kelola data pasangan calon ketua dan wakil ketua OSIM.
                </p>
            </div>
        </div>
        <div class="header-actions-premium" style="display:flex; gap:12px; margin-top: 15px;">
            <button onclick="openModal()" class="e-btn" style="background: #fff; color: #4f46e5; box-shadow: 0 10px 20px rgba(0,0,0,0.15); border: none; cursor: pointer;">
                <i data-lucide="plus-circle"></i> Tambah Kandidat
            </button>
            <a href="<?= base_url('osim/evoting') ?>" class="e-btn e-btn-glass" style="color:#fff; border-color: rgba(255,255,255,0.4);">
                <i data-lucide="layout-dashboard"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>

<div class="z-panel" style="border-radius: 24px; padding: 25px;">
    <?php if(empty($kandidat)): ?>
        <div style="text-align: center; padding: 60px 20px; color: #64748b;">
            <i data-lucide="users" style="width: 64px; height: 64px; opacity: 0.3; margin-bottom: 15px;"></i>
            <h3>Belum Ada Kandidat</h3>
            <p>Silakan tambahkan data pasangan calon pertama Anda.</p>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
            <?php foreach($kandidat as $k): ?>
                <div style="border: 1px solid #e2e8f0; border-radius: 20px; overflow: hidden; background: #fff; position: relative; <?= (isset($k['status']) && $k['status'] == 'nonaktif') ? 'opacity: 0.6; filter: grayscale(100%);' : '' ?>">
                    <div style="position: absolute; top: 15px; right: 15px; display: flex; gap: 5px;">
                        <form method="POST" action="<?= base_url('osim/evoting/kandidat/toggle') ?>" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $k['id'] ?>">
                            <button type="submit" class="btn btn-sm" style="background: <?= (!isset($k['status']) || $k['status'] == 'aktif') ? '#10b981' : '#cbd5e1' ?>; color: #fff; border-radius: 8px; padding: 5px 10px;" title="<?= (!isset($k['status']) || $k['status'] == 'aktif') ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                <i data-lucide="<?= (!isset($k['status']) || $k['status'] == 'aktif') ? 'toggle-right' : 'toggle-left' ?>" style="width: 14px;"></i>
                            </button>
                        </form>
                        <button onclick="editKandidat(<?= htmlspecialchars(json_encode($k)) ?>)" class="btn btn-sm" style="background: #f1f5f9; color: #3b82f6; border-radius: 8px; padding: 5px 10px;"><i data-lucide="edit" style="width: 14px;"></i></button>
                        <a href="<?= base_url('osim/evoting/kandidat/hapus?id='.$k['id']) ?>" onclick="return confirm('Yakin ingin menghapus kandidat ini?')" class="btn btn-sm" style="background: #f1f5f9; color: #ef4444; border-radius: 8px; padding: 5px 10px;"><i data-lucide="trash-2" style="width: 14px;"></i></a>
                    </div>
                    <div style="background: #f8fafc; padding: 30px 20px; text-align: center; border-bottom: 1px solid #e2e8f0;">
                        <?php if($k['foto']): ?>
                            <img src="<?= base_url('public/uploads/osim/'.$k['foto']) ?>" style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%; box-shadow: 0 10px 20px rgba(0,0,0,0.1); margin-bottom: 15px;">
                        <?php else: ?>
                            <div style="width: 120px; height: 120px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto;">
                                <i data-lucide="image" style="width: 40px; height: 40px; color: #94a3b8;"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div style="display: inline-block; background: #4f46e5; color: #fff; font-size: 0.8rem; font-weight: 800; padding: 3px 10px; border-radius: 20px; margin-bottom: 10px;">
                            PASLON <?= $k['nomor_urut'] ?>
                        </div>
                        <div style="font-size: 1.1rem; font-weight: 900; color: #0f172a; margin-bottom: 5px;">
                            <?= htmlspecialchars($k['nama_ketua']) ?>
                        </div>
                        <div style="font-size: 0.85rem; color: #64748b; font-weight: 700; margin-bottom: 10px;">
                            Wakil: <?= htmlspecialchars($k['nama_wakil']) ?>
                        </div>
                    </div>
                    <div style="padding: 20px;">
                        <div style="font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 5px;">Visi</div>
                        <div style="font-size: 0.85rem; color: #475569; margin-bottom: 15px; line-height: 1.5;"><?= nl2br(htmlspecialchars($k['visi'])) ?></div>
                        
                        <div style="font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 5px;">Misi</div>
                        <div style="font-size: 0.85rem; color: #475569; line-height: 1.5;"><?= nl2br(htmlspecialchars($k['misi'])) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Form -->
<div class="z-modal" id="modalKandidat" style="display: none; position: fixed; top:0; left:0; right:0; bottom:0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: #fff; border-radius: 24px; width: 100%; max-width: 600px; max-height: 90vh; overflow-y: auto;">
        <form action="<?= base_url('osim/evoting/kandidat/simpan') ?>" method="POST" enctype="multipart/form-data">
            <div style="padding: 20px 25px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 1.2rem;" id="modalTitle">Tambah Kandidat Baru</h3>
                <button type="button" onclick="closeModal()" style="background: none; border: none; cursor: pointer; color: #64748b;"><i data-lucide="x"></i></button>
            </div>
            <div style="padding: 25px;">
                <input type="hidden" name="id" id="inp_id">
                <input type="hidden" name="foto_lama" id="inp_foto_lama">
                
                <div class="z-form-group" style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 700; font-size: 0.85rem;">Nomor Urut</label>
                    <input type="number" name="nomor_urut" id="inp_nomor_urut" required class="z-input" style="width: 100%; padding: 10px; border-radius: 10px; border: 1px solid #cbd5e1;">
                </div>
                
                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 700; font-size: 0.85rem;">Nama Ketua OSIM</label>
                        <input type="text" name="nama_ketua" id="inp_nama_ketua" required class="z-input" style="width: 100%; padding: 10px; border-radius: 10px; border: 1px solid #cbd5e1;">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 700; font-size: 0.85rem;">Nama Wakil Ketua</label>
                        <input type="text" name="nama_wakil" id="inp_nama_wakil" required class="z-input" style="width: 100%; padding: 10px; border-radius: 10px; border: 1px solid #cbd5e1;">
                    </div>
                </div>

                <div class="z-form-group" style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 700; font-size: 0.85rem;">Visi</label>
                    <textarea name="visi" id="inp_visi" rows="3" required class="z-input" style="width: 100%; padding: 10px; border-radius: 10px; border: 1px solid #cbd5e1;"></textarea>
                </div>

                <div class="z-form-group" style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 700; font-size: 0.85rem;">Misi</label>
                    <textarea name="misi" id="inp_misi" rows="4" required class="z-input" style="width: 100%; padding: 10px; border-radius: 10px; border: 1px solid #cbd5e1;"></textarea>
                </div>

                <div class="z-form-group" style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 700; font-size: 0.85rem;">Foto Paslon (Opsional)</label>
                    <input type="file" name="foto" accept="image/*" class="z-input" style="width: 100%; padding: 10px; border-radius: 10px; border: 1px solid #cbd5e1;">
                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 5px;">Abaikan jika tidak ingin mengubah foto. Format: JPG/PNG, maksimal 2MB.</div>
                </div>
            </div>
            <div style="padding: 20px 25px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px; background: #f8fafc; border-bottom-left-radius: 24px; border-bottom-right-radius: 24px;">
                <button type="button" onclick="closeModal()" class="btn" style="background: #fff; color: #475569; border: 1px solid #cbd5e1; border-radius: 12px; font-weight: 700; padding: 10px 20px;">Batal</button>
                <button type="submit" class="btn" style="background: #4f46e5; color: #fff; border: none; border-radius: 12px; font-weight: 700; padding: 10px 20px;">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<script>
const modal = document.getElementById('modalKandidat');

function openModal() {
    document.getElementById('modalTitle').innerText = "Tambah Kandidat Baru";
    document.getElementById('inp_id').value = "";
    document.getElementById('inp_nomor_urut').value = "";
    document.getElementById('inp_nama_ketua').value = "";
    document.getElementById('inp_nama_wakil').value = "";
    document.getElementById('inp_visi').value = "";
    document.getElementById('inp_misi').value = "";
    document.getElementById('inp_foto_lama').value = "";
    modal.style.display = "flex";
}

function editKandidat(data) {
    document.getElementById('modalTitle').innerText = "Edit Kandidat Paslon " + data.nomor_urut;
    document.getElementById('inp_id').value = data.id;
    document.getElementById('inp_nomor_urut').value = data.nomor_urut;
    document.getElementById('inp_nama_ketua').value = data.nama_ketua;
    document.getElementById('inp_nama_wakil').value = data.nama_wakil;
    document.getElementById('inp_visi').value = data.visi;
    document.getElementById('inp_misi').value = data.misi;
    document.getElementById('inp_foto_lama').value = data.foto;
    modal.style.display = "flex";
}

function closeModal() {
    modal.style.display = "none";
}
</script>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
