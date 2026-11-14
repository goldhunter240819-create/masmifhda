<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title='Supervisi Administrasi'; include __DIR__.'/../admin/_zamrud_head.php'; ?>
</head>
<body>
<?php $page_badge = 'Kurikulum'; ?>
<div class="z-wrap">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../siakad/_siakad_header.php'; ?>
        
        <!-- MULAI KONTEN ANDA DI SINI -->
        <div class="elite-page-header">
            <div class="header-bg-shapes"></div>
            <div class="header-content-inner">
                <div class="header-text-premium">
                    <div class="header-badge">SUPERVISI</div>
                    <h1><i data-lucide="file-text"></i> Supervisi Administrasi</h1>
                    <p>Pemantauan dan kelengkapan administrasi guru dan tendik.</p>
                </div>
                <div class="header-actions-premium" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                    <a href="/kurikulum/supervisi/laporan-hasil" class="e-btn" style="background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.35); color: #fff; text-decoration: none;" title="Buka Dokumen Laporan Hasil Supervisi Bab I - IV">
                        <i data-lucide="file-bar-chart-2"></i> Laporan Hasil
                    </a>
                    <a href="/kurikulum/supervisi/tindak-lanjut" class="e-btn" style="background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.35); color: #fff; text-decoration: none;" title="Buka Rencana Tindak Lanjut (RTL) Pasca Supervisi">
                        <i data-lucide="arrow-right-circle"></i> Tindak Lanjut (RTL)
                    </a>
                    <a href="/kurikulum/supervisi/administrasi/cetak-jadwal" target="_blank" class="e-btn" style="background: #059669; border: 1px solid rgba(255,255,255,0.5); color: #fff; text-decoration: none; font-weight:700; box-shadow:0 4px 12px rgba(5,150,105,0.35);" title="Cetak Jadwal Pelaksanaan Supervisi Administrasi (1 Lembar Naskah)">
                        <i data-lucide="calendar"></i> Cetak Jadwal
                    </a>
                    <a href="/kurikulum/supervisi/administrasi/cetak-semua" target="_blank" class="e-btn" style="background: #0284c7; border: 1px solid rgba(255,255,255,0.5); color: #fff; text-decoration: none; font-weight:700; box-shadow:0 4px 12px rgba(2,132,199,0.35);" title="Cetak Seluruh Lembar Instrumen Supervisi Administrasi Sekaligus">
                        <i data-lucide="printer"></i> Cetak Semua
                    </a>
                    <button class="e-btn e-btn-primary" onclick="openNew()">
                        <i data-lucide="plus-circle"></i> Jadwalkan Supervisi
                    </button>
                </div>
            </div>
        </div>
        
        <div class="z-panel" style="margin-top: 1.5rem;">
            <div class="z-panel-head" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                <div class="z-panel-title">
                    <i data-lucide="list"></i> Daftar Supervisi Administrasi
                </div>
                <div style="display:flex; gap:8px; align-items:center;">
                    <a href="/kurikulum/supervisi/administrasi/cetak-jadwal" target="_blank" class="e-btn e-btn-outline" style="font-size: 0.82rem; padding: 0.35rem 0.85rem; display: inline-flex; align-items: center; gap: 6px; color:#059669; border-color:#059669; background:rgba(5,150,105,0.06); text-decoration:none; font-weight:700;" title="Cetak Jadwal Supervisi (1 Lembar)">
                        <i data-lucide="calendar" style="width:15px;height:15px;"></i> Cetak Jadwal (1 Lembar)
                    </a>
                    <a href="/kurikulum/supervisi/administrasi/cetak-semua" target="_blank" class="e-btn e-btn-outline" style="font-size: 0.82rem; padding: 0.35rem 0.85rem; display: inline-flex; align-items: center; gap: 6px; color:#0284c7; border-color:#0284c7; background:rgba(2,132,199,0.06); text-decoration:none; font-weight:700;" title="Cetak Seluruh Lembar Instrumen Supervisi Sekaligus">
                        <i data-lucide="printer" style="width:15px;height:15px;"></i> Cetak Semua Lembar Supervisi
                    </a>
                </div>
            </div>
            <div style="padding: 0; overflow-x: auto;">
                <table class="z-table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">No</th>
                            <th>Nama Pegawai</th>
                            <th>Jabatan</th>
                            <th>Tanggal Supervisi</th>
                            <th>Status & Nilai</th>
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
                                    <i data-lucide="folder-search" style="width:48px;height:48px;opacity:0.3;margin-bottom:1rem;"></i>
                                    <p>Belum ada data supervisi administrasi.</p>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php 
                            $no = 1;
                            foreach($jadwal_supervisi as $row): 
                            ?>
                            <tr>
                                <td align="center" style="vertical-align:top; padding-top:1.2rem;"><?= $no++ ?></td>
                                <td style="vertical-align:top; padding-top:1.2rem;">
                                    <div style="font-weight:600; color:#1e293b;"><?= htmlspecialchars($row['nama_pegawai']) ?></div>
                                    <div style="font-size:0.8rem; color:#64748b; margin-top:0.2rem;">Supervisor: <?= htmlspecialchars($row['nama_supervisor']) ?></div>
                                </td>
                                <td style="vertical-align:top; padding-top:1.2rem;">Guru/Tendik</td>
                                <td style="vertical-align:top; padding-top:1.2rem;"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                <td style="vertical-align:top; padding-top:1.2rem;">
                                    <?php if($row['status'] == 'Jadwal'): ?>
                                        <span class="z-badge z-badge-warning">Belum Dinilai</span>
                                    <?php else: 
                                        $nilai = $row['nilai'] ?? 0;
                                        if($nilai >= 91) { $grade = 'Amat Baik'; $gcolor = '#10b981'; $gbg = '#d1fae5'; }
                                        elseif($nilai >= 81) { $grade = 'Baik'; $gcolor = '#3b82f6'; $gbg = '#dbeafe'; }
                                        elseif($nilai >= 71) { $grade = 'Cukup'; $gcolor = '#f59e0b'; $gbg = '#fef3c7'; }
                                        else { $grade = 'Kurang'; $gcolor = '#ef4444'; $gbg = '#fee2e2'; }
                                    ?>
                                        <div style="display:flex; flex-direction:column; gap:0.25rem;">
                                            <span class="z-badge z-badge-success">Sudah Dinilai</span>
                                            <span style="font-size:0.8rem; font-weight:700; color:<?= $gcolor ?>; background:<?= $gbg ?>; padding:0.2rem 0.5rem; border-radius:12px; display:inline-block; width:fit-content; border:1px solid <?= $gcolor ?>;">Nilai: <?= $nilai ?> (<?= $grade ?>)</span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td align="center" style="vertical-align:middle;">
                                    <label class="z-switch" style="margin:0;">
                                        <input type="checkbox" onchange="toggleRilis(<?= $row['id'] ?>, this.checked)" <?= (isset($row['is_released']) && $row['is_released']) ? 'checked' : '' ?>>
                                        <span class="z-slider"></span>
                                    </label>
                                </td>
                                <td align="center" style="vertical-align:middle;">
                                    <?php if($row['status'] == 'Dievaluasi'): ?>
                                    <label class="z-switch" style="margin:0;">
                                        <input type="checkbox" onchange="toggleRilisHasil(<?= $row['id'] ?>, this.checked)" <?= (isset($row['is_hasil_released']) && $row['is_hasil_released']) ? 'checked' : '' ?>>
                                        <span class="z-slider"></span>
                                    </label>
                                    <?php else: ?>
                                    <span style="font-size:0.75rem; color:#94a3b8;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td align="center" style="vertical-align:top; padding-top:1.2rem;">
                                    <div style="display:flex; gap:0.25rem; justify-content:center;">
                                        <button onclick="openEvaluasi(<?= $row['id'] ?>)" class="e-btn e-btn-outline" style="padding:0.3rem 0.5rem; color:#10b981; border-color:rgba(16,185,129,0.2); background:rgba(16,185,129,0.05);" title="Evaluasi"><i data-lucide="clipboard-check" style="width:16px;height:16px;"></i></button>
                                        <?php if($row['status'] == 'Dievaluasi'): ?>
                                        <button onclick="cetakJadwal(<?= $row['id'] ?>)" class="e-btn e-btn-outline" style="padding:0.3rem 0.5rem; color:#8b5cf6; border-color:rgba(139,92,246,0.2); background:rgba(139,92,246,0.05);" title="Cetak"><i data-lucide="printer" style="width:16px;height:16px;"></i></button>
                                        <?php endif; ?>
                                        <button onclick="editJadwal(<?= $row['id'] ?>, <?= (int)($row['pegawai_id'] ?? $row['guru_id'] ?? 0) ?>, <?= (int)($row['supervisor_id'] ?? $row['penilai_id'] ?? 0) ?>, '<?= $row['tanggal'] ?? $row['tanggal_supervisi'] ?? '' ?>', `<?= htmlspecialchars($row['keterangan'] ?? '') ?>`)" class="e-btn e-btn-outline" style="padding:0.3rem 0.5rem; color:#3b82f6; border-color:rgba(59,130,246,0.2); background:rgba(59,130,246,0.05);" title="Edit"><i data-lucide="edit" style="width:16px;height:16px;"></i></button>
                                        <button onclick="deleteJadwal(<?= $row['id'] ?>)" class="e-btn e-btn-outline" style="padding:0.3rem 0.5rem; color:#ef4444; border-color:rgba(239,68,68,0.2); background:rgba(239,68,68,0.05);" title="Hapus"><i data-lucide="trash-2" style="width:16px;height:16px;"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- AKHIR KONTEN ANDA -->

    </div>
</div>

<!-- Modal Jadwal Supervisi -->
<div id="modalJadwal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); z-index:99999; justify-content:center; align-items:center;">
    <div style="background:#fff; border-radius:12px; width:100%; max-width:500px; box-shadow:0 10px 25px rgba(0,0,0,0.2); animation: modalIn 0.3s ease;">
        <div style="display:flex; justify-content:space-between; align-items:center; padding:1.5rem; border-bottom:1px solid #e2e8f0;">
            <h3 id="modalTitle" style="margin:0; font-size:1.2rem; display:flex; align-items:center; gap:0.5rem; color:#1e293b;"><i data-lucide="plus-circle" style="color:#10b981;"></i> Jadwalkan Supervisi Administrasi</h3>
            <button onclick="document.getElementById('modalJadwal').style.display='none'" style="background:none; border:none; cursor:pointer; font-size:1.5rem; color:#64748b;">&times;</button>
        </div>
        <form id="jadwalForm" action="/kurikulum/supervisi/administrasi" method="POST" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){btn.disabled=true; btn.textContent='Menyimpan...';}">
            <input type="hidden" name="id" id="jadwal_id">
            <div style="padding:1.5rem; display:flex; flex-direction:column; gap:1rem;">
                <div>
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">Pilih Pegawai (Guru/Tendik)</label>
                    <select name="pegawai_id" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:8px; background:#fff; font-family:inherit;" required>
                        <option value="">-- Pilih Pegawai --</option>
                        <?php if(isset($pegawai) && is_array($pegawai)): ?>
                            <?php foreach($pegawai as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama_lengkap']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div style="display:flex; gap:1rem;">
                    <div style="flex:1;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">Tanggal Supervisi</label>
                        <input type="date" name="tanggal" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit;" required>
                    </div>
                    <div style="flex:1;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">Supervisor</label>
                        <select name="supervisor_id" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:8px; background:#fff; font-family:inherit;" required>
                            <option value="">-- Pilih Supervisor --</option>
                            <?php if(isset($pegawai) && is_array($pegawai)): ?>
                                <?php foreach($pegawai as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama_lengkap']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">Keterangan Tambahan (Opsional)</label>
                    <textarea name="keterangan" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:8px; min-height:80px; font-family:inherit;" placeholder="Tuliskan catatan atau fokus supervisi..."></textarea>
                </div>
            </div>
            <div style="padding:1.5rem; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:1rem; background:#f8fafc; border-bottom-left-radius:12px; border-bottom-right-radius:12px;">
                <button type="button" onclick="document.getElementById('modalJadwal').style.display='none'" style="padding:0.75rem 1.5rem; border:1px solid #cbd5e1; background:#fff; border-radius:8px; cursor:pointer; font-weight:600; color:#475569;">Batal</button>
                <button type="submit" style="padding:0.75rem 1.5rem; background:#10b981; color:#fff; border:none; border-radius:8px; cursor:pointer; font-weight:600; box-shadow:0 4px 6px -1px rgba(16,185,129,0.2);">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>

<form id="deleteForm" action="/kurikulum/supervisi/administrasi/delete" method="POST" style="display:none;">
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

    const baseUrl = window.location.href.split('/kurikulum')[0];

    function openNew() {
        document.getElementById('jadwalForm').reset();
        document.getElementById('jadwal_id').value = '';
        document.getElementById('jadwalForm').action = '/kurikulum/supervisi/administrasi';
        document.getElementById('modalTitle').innerHTML = '<i data-lucide="plus-circle" style="color:#10b981;"></i> Jadwalkan Supervisi Administrasi';
        document.getElementById('modalJadwal').style.display='flex';
    }

    function editJadwal(id, pegawai_id, supervisor_id, tanggal, keterangan) {
        document.getElementById('jadwal_id').value = id;
        document.getElementById('jadwalForm').action = '/kurikulum/supervisi/administrasi/update';
        document.getElementById('modalTitle').innerHTML = '<i data-lucide="edit" style="color:#3b82f6;"></i> Edit Jadwal Supervisi';
        
        document.querySelector('select[name="pegawai_id"]').value = pegawai_id;
        document.querySelector('input[name="tanggal"]').value = tanggal;
        document.querySelector('select[name="supervisor_id"]').value = supervisor_id;
        document.querySelector('textarea[name="keterangan"]').value = keterangan;
        
        document.getElementById('modalJadwal').style.display='flex';
    }

    function openEvaluasi(id) {
        window.location.href = baseUrl + '/kurikulum/supervisi/administrasi/evaluasi?id=' + id;
    }

    function cetakJadwal(id) {
        window.open(baseUrl + '/kurikulum/supervisi/administrasi/cetak?id=' + id, '_blank');
    }

    function deleteJadwal(id) {
        if(confirm('Apakah Anda yakin ingin menghapus jadwal supervisi ini?')) {
            document.getElementById('delete_id').value = id;
            document.getElementById('deleteForm').submit();
        }
    }

    function toggleRilis(id, isReleased) {
        fetch(`${baseUrl}/kurikulum/supervisi/administrasi/rilis`, {
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
        fetch(`${baseUrl}/kurikulum/supervisi/administrasi/rilis-hasil`, {
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
