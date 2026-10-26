<?php
$apk_settings_file = __DIR__ . '/../../../apk/apk_settings.json';
$elearning_enabled = true;
if (file_exists($apk_settings_file)) {
    $apk_settings = json_decode(file_get_contents($apk_settings_file), true);
    if (isset($apk_settings['elearning_enabled']) && !$apk_settings['elearning_enabled']) {
        $elearning_enabled = false;
    }
}
?>
<div class="siakad-container">
    <?php include __DIR__ . '/../sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../_siakad_header.php'; ?>

        <?php if (!$elearning_enabled): ?>
        <div style="padding:100px 20px; text-align:center;">
            <div style="background:white;padding:40px;border-radius:16px;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);display:inline-block;max-width:450px;">
                <i data-lucide="settings" style="width:48px;height:48px;color:#cbd5e1;margin-bottom:15px;display:inline-block;"></i>
                <h2 style="color:#0f172a;margin-bottom:10px;font-size:1.4rem;font-weight:700;">Fitur Sedang Dipelihara</h2>
                <p style="color:#64748b;margin:0;line-height:1.5;">Maaf, fitur E-Learning (Tugas Daring) sedang dinonaktifkan sementara oleh Admin untuk keperluan maintenance atau libur panjang.</p>
            </div>
        </div>
        <?php else: ?>
    <div class="z-main">
        <?php include __DIR__ . '/../_siakad_header.php'; ?>

        <div class="elite-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: var(--z-radius, 12px); margin-bottom: 2rem; color: white;">
            <div>
                <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                    <i data-lucide="laptop" style="color: rgba(255,255,255,0.8);"></i> Data Tugas Daring
                </h1>
                <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Lihat dan cetak instruksi tugas yang diberikan guru untuk siswa di pondok.</p>
            </div>
        </div>

        <div class="z-card mb-4" style="padding: 1.5rem;">
            <div class="z-card-header" style="margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #f1f5f9;">
                <h3 class="z-card-title" style="margin:0; font-size: 1.1rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;"><i data-lucide="filter"></i> Filter Pencarian</h3>
            </div>
            <div class="z-card-body">
                <form method="GET" action="" class="z-form-group" style="display:flex; gap:15px; align-items:flex-end; flex-wrap:wrap; margin-bottom:0;">
                    <div>
                        <label style="display:block; margin-bottom:5px; font-size:0.9rem; font-weight:600;">Pilih Tanggal</label>
                        <input type="date" name="tanggal" class="z-input" value="<?= htmlspecialchars($tanggal_filter) ?>">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:5px; font-size:0.9rem; font-weight:600;">Pilih Kelas</label>
                        <select name="kelas_id" class="z-input" style="min-width: 200px;">
                            <option value="">Semua Kelas</option>
                            <?php foreach ($kelas as $k): ?>
                                <option value="<?= $k['id'] ?>" <?= ($kelas_id_filter == $k['id']) ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kelas']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="z-btn z-btn-primary"><i data-lucide="search"></i> Tampilkan</button>
                        
                        <?php if (!empty($kelas_id_filter)): ?>
                            <a href="<?= base_url('admin/siakad/tugas-daring/cetak?tanggal=' . $tanggal_filter . '&kelas_id=' . $kelas_id_filter) ?>" target="_blank" class="z-btn" style="background:#10b981; color:white; border-color:#059669; margin-left:10px;"><i data-lucide="printer"></i> Cetak PDF</a>
                        <?php else: ?>
                            <button type="button" onclick="alert('Silakan pilih salah satu kelas (misal: XE.1) di menu dropdown, lalu klik Tampilkan untuk memunculkan fitur cetak.');" class="z-btn" style="background:#d1d5db; color:#6b7280; border-color:#d1d5db; margin-left:10px; cursor:not-allowed;"><i data-lucide="printer"></i> Cetak PDF</button>
                        <?php endif; ?>
                    </div>
                </form>
                <?php if (empty($kelas_id_filter)): ?>
                <p style="margin-top:15px; font-size:0.85rem; color:#f59e0b; margin-bottom:0; font-weight:600;"><i data-lucide="info" style="width:16px;height:16px;vertical-align:middle; margin-right:5px;"></i> Tombol cetak belum aktif. Pilih kelas spesifik lalu klik Tampilkan terlebih dahulu.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="z-card" style="padding: 1.5rem;">
            <div class="z-card-header" style="margin-bottom: 1.5rem;">
                <h3 class="z-card-title" style="margin:0; font-size: 1.1rem; font-weight: 700; color: #1e293b;">Daftar Tugas Daring - <?= date('d M Y', strtotime($tanggal_filter)) ?></h3>
            </div>
            <div class="table-responsive">
                <table class="z-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th width="5%" style="text-align: center;">No</th>
                            <th width="15%">Kelas</th>
                            <th width="20%">Guru</th>
                            <th width="15%">Mapel</th>
                            <th width="35%">Judul & Instruksi</th>
                            <th width="10%" style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tugas_list)): ?>
                            <tr><td colspan="6" style="text-align: center; color: #94a3b8; padding: 2rem;">Tidak ada data tugas untuk tanggal/kelas ini.</td></tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($tugas_list as $t): ?>
                            <tr>
                                <td style="text-align: center;"><?= $no++ ?></td>
                                <td><span style="background: #e0f2fe; color: #0284c7; padding: 4px 10px; border-radius: 100px; font-size: 0.75rem; font-weight: 800; border: 1px solid #bae6fd;"><?= htmlspecialchars($t['nama_kelas']) ?></span></td>
                                <td style="font-weight: 600; color: #334155;"><?= htmlspecialchars($t['nama_guru']) ?></td>
                                <td><?= htmlspecialchars($t['nama_mapel']) ?></td>
                                <td>
                                    <strong style="color: #1e293b; font-size:0.9rem; display:block; margin-bottom: 3px;"><?= htmlspecialchars($t['judul']) ?></strong>
                                    <?php if (!empty($t['batas_waktu'])): ?>
                                        <div style="font-size: 0.8rem; color: #ef4444; font-weight: 600; margin-bottom: 4px; display: flex; align-items: center; gap: 4px;">
                                            <i data-lucide="clock" style="width: 14px; height: 14px;"></i> Batas Pengumpulan: <?= date('d M Y', strtotime($t['batas_waktu'])) ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <a href="javascript:void(0)" onclick="toggleInstruksi(<?= $t['id'] ?>)" style="font-size: 0.75rem; color: #3b82f6; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                        <i data-lucide="eye" style="width:12px;height:12px;"></i> Lihat Instruksi
                                    </a>

                                    <div id="instruksi-<?= $t['id'] ?>" style="display: none; font-size: 0.85rem; color: #475569; background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px dashed #cbd5e1; white-space: pre-wrap; line-height: 1.5; margin-top: 10px;"><?= htmlspecialchars($t['instruksi']) ?></div>
                                </td>
                                <td style="text-align: center;">
                                    <a href="<?= base_url('admin/siakad/tugas-daring/cetak?tugas_id=' . $t['id']) ?>" target="_blank" class="z-btn z-btn-sm" style="background:#10b981; color:white; border:none; display:inline-flex; align-items:center; gap:5px;"><i data-lucide="printer" style="width:14px;height:14px;"></i> Cetak</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <?php endif; ?>

    </div>
</div>

<script>
function toggleInstruksi(id) {
    var el = document.getElementById('instruksi-' + id);
    if (el.style.display === 'none') {
        el.style.display = 'block';
    } else {
        el.style.display = 'none';
    }
}
</script>
