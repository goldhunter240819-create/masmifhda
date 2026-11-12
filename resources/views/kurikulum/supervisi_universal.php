<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title='Supervisi ' . $tipe; include __DIR__.'/../admin/_zamrud_head.php'; ?>
    <style>
        .modal-large { max-width: 700px !important; }
    </style>
</head>
<body>
<?php $page_badge = 'Kurikulum'; ?>
<div class="z-wrap">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../siakad/_siakad_header.php'; ?>
        
        <div class="elite-page-header">
            <div class="header-bg-shapes"></div>
            <div class="header-content-inner">
                <div class="header-text-premium">
                    <div class="header-badge">SUPERVISI KOMPREHENSIF</div>
                    <h1><i data-lucide="layout-list"></i> Supervisi <?= htmlspecialchars($tipe) ?></h1>
                    <p>Manajemen jadwal dan pelaksanaan evaluasi supervisi kategori <?= htmlspecialchars($tipe) ?>.</p>
                </div>
                <div class="header-actions-premium">
                    <button class="e-btn e-btn-primary" onclick="openNew()">
                        <i data-lucide="plus"></i> Jadwalkan Supervisi
                    </button>
                </div>
            </div>
        </div>
        
        <div class="z-panel" style="margin-top: 1.5rem;">
            <div class="z-panel-head">
                <div class="z-panel-title">
                    <i data-lucide="list"></i> Jadwal & Hasil Supervisi <?= htmlspecialchars($tipe) ?>
                </div>
            </div>
            
            <div style="padding: 0; overflow-x: auto;">
                <table class="z-table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">No</th>
                            <th>Tanggal</th>
                            <th>Nama Guru</th>
                            <th>Kelas/Mapel (Opsional)</th>
                            <th>Supervisor</th>
                            <th style="text-align: center;">Status</th>
                            <th style="text-align: center;">Nilai Akhir</th>
                            <th style="width: 100px; text-align: center;">Rilis Jadwal</th>
                            <th style="width: 100px; text-align: center;">Rilis Hasil</th>
                            <th style="width: 120px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($jadwal_supervisi)): ?>
                        <tr>
                            <td colspan="8" align="center" style="padding: 2rem;">
                                <div style="color: var(--z-muted);">
                                    <i data-lucide="calendar-x" style="width:48px;height:48px;opacity:0.3;margin-bottom:1rem;"></i>
                                    <p>Belum ada jadwal supervisi <?= htmlspecialchars($tipe) ?> yang diinput.</p>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php $no=1; foreach($jadwal_supervisi as $j): ?>
                            <tr>
                                <td align="center"><?= $no++ ?></td>
                                <td><?= date('d M Y', strtotime($j['tanggal'])) ?></td>
                                <td style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($j['nama_guru']) ?></td>
                                <td>
                                    <?php if($j['nama_kelas'] !== 'Umum' || $j['nama_mapel'] !== 'Umum'): ?>
                                        <?= htmlspecialchars($j['nama_kelas']) ?> - <?= htmlspecialchars($j['nama_mapel']) ?>
                                    <?php else: ?>
                                        <span style="color:#94a3b8; font-style:italic;">Tidak Spesifik</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($j['nama_supervisor']) ?></td>
                                <td align="center">
                                    <?php if($j['status'] == 'Jadwal'): ?>
                                        <span class="z-badge bg-warning text-warning-dark"><i data-lucide="clock" style="width:12px;height:12px;"></i> Terjadwal</span>
                                    <?php else: ?>
                                        <span class="z-badge bg-success text-success-dark"><i data-lucide="check-circle" style="width:12px;height:12px;"></i> Dievaluasi</span>
                                    <?php endif; ?>
                                </td>
                                <td align="center">
                                    <?php if($j['nilai'] !== null): ?>
                                        <div style="font-weight: 800; font-size:1.1rem; color:#10b981;"><?= $j['nilai'] ?></div>
                                    <?php else: ?>
                                        <span style="color: #94a3b8;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td align="center" style="vertical-align:middle;">
                                    <label class="z-switch" style="margin:0;">
                                        <input type="checkbox" onchange="toggleRilis(<?= $j['id'] ?>, this.checked)" <?= (isset($j['is_released']) && $j['is_released']) ? 'checked' : '' ?>>
                                        <span class="z-slider"></span>
                                    </label>
                                </td>
                                <td align="center" style="vertical-align:middle;">
                                    <?php if($j['status'] == 'Dievaluasi'): ?>
                                    <label class="z-switch" style="margin:0;">
                                        <input type="checkbox" onchange="toggleRilisHasil(<?= $j['id'] ?>, this.checked)" <?= (isset($j['is_hasil_released']) && $j['is_hasil_released']) ? 'checked' : '' ?>>
                                        <span class="z-slider"></span>
                                    </label>
                                    <?php else: ?>
                                    <span style="font-size:0.75rem; color:#94a3b8;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td align="center">
                                    <div style="display:flex; gap:0.25rem; justify-content:center;">
                                        <?php if($j['status'] == 'Jadwal'): ?>
                                            <a href="/kurikulum/supervisi/universal/<?= strtolower(str_replace(' ', '-', $tipe)) ?>/evaluasi?id=<?= $j['id'] ?>" class="e-btn e-btn-outline" style="padding:0.3rem 0.5rem; color:#10b981; border-color:#10b981;" title="Mulai Evaluasi">
                                                <i data-lucide="clipboard-check" style="width:16px;height:16px;"></i>
                                            </a>
                                            <button onclick="editJadwal(<?= $j['id'] ?>, <?= $j['guru_id'] ?>, <?= $j['kelas_id'] ?: 'null' ?>, <?= $j['mapel_id'] ?: 'null' ?>, <?= $j['supervisor_id'] ?>, '<?= $j['tanggal'] ?>', '<?= htmlspecialchars($j['keterangan'] ?? '', ENT_QUOTES) ?>')" class="e-btn e-btn-outline" style="padding:0.3rem 0.5rem; color:#3b82f6; border-color:#3b82f6;" title="Edit Jadwal">
                                                <i data-lucide="edit" style="width:16px;height:16px;"></i>
                                            </button>
                                        <?php else: ?>
                                            <a href="/kurikulum/supervisi/universal/<?= strtolower(str_replace(' ', '-', $tipe)) ?>/cetak?id=<?= $j['id'] ?>" target="_blank" class="e-btn e-btn-outline" style="padding:0.3rem 0.5rem; color:#8b5cf6; border-color:#8b5cf6;" title="Cetak Hasil">
                                                <i data-lucide="printer" style="width:16px;height:16px;"></i>
                                            </a>
                                            <a href="/kurikulum/supervisi/universal/<?= strtolower(str_replace(' ', '-', $tipe)) ?>/evaluasi?id=<?= $j['id'] ?>" class="e-btn e-btn-outline" style="padding:0.3rem 0.5rem; color:#3b82f6; border-color:#3b82f6;" title="Lihat/Edit Penilaian">
                                                <i data-lucide="eye" style="width:16px;height:16px;"></i>
                                            </a>
                                        <?php endif; ?>
                                        <button onclick="deleteJadwal(<?= $j['id'] ?>)" class="e-btn e-btn-outline" style="padding:0.3rem 0.5rem; color:#ef4444; border-color:#ef4444;" title="Hapus">
                                            <i data-lucide="trash-2" style="width:16px;height:16px;"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Modal Penjadwalan -->
<div id="modalJadwal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); z-index:99999; justify-content:center; align-items:center;">
    <div class="modal-large" style="background:#fff; border-radius:12px; width:95%; max-height:90vh; overflow-y:auto; box-shadow:0 10px 25px rgba(0,0,0,0.2); animation: modalIn 0.3s ease;">
        <div style="display:flex; justify-content:space-between; align-items:center; padding:1.5rem; border-bottom:1px solid #e2e8f0;">
            <h3 id="modalTitle" style="margin:0; font-size:1.2rem; display:flex; align-items:center; gap:0.5rem; color:#1e293b;"><i data-lucide="calendar-plus" style="color:#10b981;"></i> Jadwalkan Supervisi <?= htmlspecialchars($tipe) ?></h3>
            <button onclick="document.getElementById('modalJadwal').style.display='none'" style="background:none; border:none; cursor:pointer; font-size:1.5rem; color:#64748b;">&times;</button>
        </div>
        <form id="jadwalForm" action="/kurikulum/supervisi/universal/<?= strtolower(str_replace(' ', '-', $tipe)) ?>" method="POST">
            <input type="hidden" name="id" id="jadwal_id">
            <div style="padding:1.5rem; display:flex; flex-direction:column; gap:1.25rem;">
                
                <div style="display:flex; gap:1rem;">
                    <div style="flex:1;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">Pilih Guru</label>
                        <select name="guru_id" class="e-input" style="width:100%; padding:0.75rem;" required>
                            <option value="">- Pilih Guru -</option>
                            <?php foreach($gurus as $g): ?>
                                <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="flex:1;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">Tanggal Pelaksanaan</label>
                        <input type="date" name="tanggal" class="e-input" style="width:100%; padding:0.75rem;" required>
                    </div>
                </div>

                <div style="display:flex; gap:1rem;">
                    <div style="flex:1;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">Kelas (Opsional)</label>
                        <select name="kelas_id" class="e-input" style="width:100%; padding:0.75rem;">
                            <option value="">- Umum / Tidak Spesifik Kelas -</option>
                            <?php foreach($kelas as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kelas']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="flex:1;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">Mata Pelajaran (Opsional)</label>
                        <select name="mapel_id" class="e-input" style="width:100%; padding:0.75rem;">
                            <option value="">- Umum / Tidak Spesifik Mapel -</option>
                            <?php foreach($mapels as $m): ?>
                                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nama_mapel']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">Pilih Supervisor</label>
                    <select name="supervisor_id" class="e-input" style="width:100%; padding:0.75rem;" required>
                        <option value="">- Pilih Supervisor -</option>
                        <?php foreach($gurus as $g): ?>
                            <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">Keterangan Tambahan</label>
                    <textarea name="keterangan" class="e-input" rows="3" style="width:100%; padding:0.75rem;" placeholder="Materi / Topik / Fokus Supervisi"></textarea>
                </div>

            </div>
            <div style="padding:1.5rem; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:1rem; background:#f8fafc; border-bottom-left-radius:12px; border-bottom-right-radius:12px;">
                <button type="button" onclick="document.getElementById('modalJadwal').style.display='none'" style="padding:0.75rem 1.5rem; border:1px solid #cbd5e1; background:#fff; border-radius:8px; cursor:pointer; font-weight:600; color:#475569;">Batal</button>
                <button type="submit" style="padding:0.75rem 1.5rem; background:#10b981; color:#fff; border:none; border-radius:8px; cursor:pointer; font-weight:600; box-shadow:0 4px 6px -1px rgba(16,185,129,0.2);">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>

<form id="deleteForm" action="/kurikulum/supervisi/universal/<?= strtolower(str_replace(' ', '-', $tipe)) ?>/delete" method="POST" style="display:none;">
    <input type="hidden" name="id" id="delete_id">
</form>

<style>
    @keyframes modalIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
<script>
    if (typeof lucide !== 'undefined') lucide.createIcons();

    function openNew() {
        document.getElementById('jadwalForm').reset();
        document.getElementById('jadwal_id').value = '';
        document.getElementById('jadwalForm').action = '/kurikulum/supervisi/universal/<?= strtolower(str_replace(' ', '-', $tipe)) ?>';
        document.getElementById('modalTitle').innerHTML = '<i data-lucide="calendar-plus" style="color:#10b981;"></i> Jadwalkan Supervisi <?= htmlspecialchars($tipe) ?>';
        document.getElementById('modalJadwal').style.display='flex';
        lucide.createIcons();
    }

    function editJadwal(id, guru_id, kelas_id, mapel_id, supervisor_id, tanggal, keterangan) {
        document.getElementById('jadwal_id').value = id;
        document.getElementById('jadwalForm').action = '/kurikulum/supervisi/universal/<?= strtolower(str_replace(' ', '-', $tipe)) ?>/update';
        document.getElementById('modalTitle').innerHTML = '<i data-lucide="edit" style="color:#3b82f6;"></i> Edit Jadwal Supervisi';
        
        document.querySelector('select[name="guru_id"]').value = guru_id;
        document.querySelector('select[name="kelas_id"]').value = kelas_id || '';
        document.querySelector('select[name="mapel_id"]').value = mapel_id || '';
        document.querySelector('select[name="supervisor_id"]').value = supervisor_id;
        document.querySelector('input[name="tanggal"]').value = tanggal;
        document.querySelector('textarea[name="keterangan"]').value = keterangan;
        
        document.getElementById('modalJadwal').style.display='flex';
        lucide.createIcons();
    }

    function deleteJadwal(id) {
        if(confirm('Apakah Anda yakin ingin menghapus jadwal supervisi ini beserta data penilaiannya?')) {
            document.getElementById('delete_id').value = id;
            document.getElementById('deleteForm').submit();
        }
    }

    const baseUrlRaw = window.location.href.split('/kurikulum')[0];

    function toggleRilis(id, isReleased) {
        fetch(`${baseUrlRaw}/kurikulum/supervisi/universal/<?= strtolower(str_replace(' ', '-', $tipe)) ?>/rilis`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&is_released=${isReleased ? 1 : 0}`
        }).then(res => res.json()).then(data => {
            if(!data.success) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Update',
                    text: data.message || 'Unknown error',
                    confirmButtonColor: '#ef4444'
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Status rilis jadwal berhasil diubah',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        }).catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Koneksi Gagal',
                text: 'Terjadi masalah pada jaringan atau server.',
                confirmButtonColor: '#ef4444'
            }).then(() => location.reload());
        });
    }

    function toggleRilisHasil(id, isReleased) {
        fetch(`${baseUrlRaw}/kurikulum/supervisi/universal/<?= strtolower(str_replace(' ', '-', $tipe)) ?>/rilis-hasil`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&is_hasil_released=${isReleased ? 1 : 0}`
        }).then(res => res.json()).then(data => {
            if(!data.success) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Update',
                    text: data.message || 'Unknown error',
                    confirmButtonColor: '#ef4444'
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Status rilis hasil berhasil diubah',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        }).catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Koneksi Gagal',
                text: 'Terjadi masalah pada jaringan atau server.',
                confirmButtonColor: '#ef4444'
            }).then(() => location.reload());
        });
    }
</script>
</body>
</html>
