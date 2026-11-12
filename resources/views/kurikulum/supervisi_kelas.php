<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title='Supervisi Kelas'; include __DIR__.'/../admin/_zamrud_head.php'; ?>
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
                    <h1><i data-lucide="users"></i> Supervisi Kelas</h1>
                    <p>Pemantauan dan evaluasi kegiatan belajar mengajar di dalam kelas.</p>
                </div>
                <div class="header-actions-premium" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                    <a href="/kurikulum/supervisi/laporan-hasil" class="e-btn" style="background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.35); color: #fff; text-decoration: none;" title="Buka Dokumen Laporan Hasil Supervisi Bab I - IV">
                        <i data-lucide="file-bar-chart-2"></i> Laporan Hasil
                    </a>
                    <a href="/kurikulum/supervisi/tindak-lanjut" class="e-btn" style="background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.35); color: #fff; text-decoration: none;" title="Buka Rencana Tindak Lanjut (RTL) Pasca Supervisi">
                        <i data-lucide="arrow-right-circle"></i> Tindak Lanjut (RTL)
                    </a>
                    <a href="/admin/pkkm/cetak-bukti/supervisi-guru" target="_blank" class="e-btn" style="background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.35); color: #fff; text-decoration: none;" title="Buka Rekapitulasi & Scorecard Supervisi Guru Resmi untuk Asesor PKKM Komponen 4">
                        <i data-lucide="award"></i> Cetak Bukti PKKM (Komponen 4)
                    </a>
                    <a href="/kurikulum/supervisi/kelas/cetak-jadwal" target="_blank" class="e-btn" style="background: #059669; border: 1px solid rgba(255,255,255,0.5); color: #fff; text-decoration: none; font-weight:700; box-shadow:0 4px 12px rgba(5,150,105,0.35);" title="Cetak Jadwal Pelaksanaan Supervisi Guru (1 Lembar Naskah)">
                        <i data-lucide="calendar"></i> Cetak Jadwal
                    </a>
                    <a href="/kurikulum/supervisi/kelas/cetak-semua" target="_blank" class="e-btn" style="background: #0284c7; border: 1px solid rgba(255,255,255,0.5); color: #fff; text-decoration: none; font-weight:700; box-shadow:0 4px 12px rgba(2,132,199,0.35);" title="Cetak Seluruh Lembar Instrumen Supervisi Guru Sekaligus">
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
                    <i data-lucide="list"></i> Daftar Supervisi Kelas
                </div>
                <div style="display:flex; gap:8px; align-items:center;">
                    <a href="/kurikulum/supervisi/kelas/cetak-jadwal" target="_blank" class="e-btn e-btn-outline" style="font-size: 0.82rem; padding: 0.35rem 0.85rem; display: inline-flex; align-items: center; gap: 6px; color:#059669; border-color:#059669; background:rgba(5,150,105,0.06); text-decoration:none; font-weight:700;" title="Cetak Jadwal Supervisi (1 Lembar)">
                        <i data-lucide="calendar" style="width:15px;height:15px;"></i> Cetak Jadwal (1 Lembar)
                    </a>
                    <a href="/kurikulum/supervisi/kelas/cetak-semua" target="_blank" class="e-btn e-btn-outline" style="font-size: 0.82rem; padding: 0.35rem 0.85rem; display: inline-flex; align-items: center; gap: 6px; color:#0284c7; border-color:#0284c7; background:rgba(2,132,199,0.06); text-decoration:none; font-weight:700;" title="Cetak Seluruh Lembar Instrumen Supervisi Sekaligus">
                        <i data-lucide="printer" style="width:15px;height:15px;"></i> Cetak Semua Lembar Supervisi
                    </a>
                </div>
            </div>
            <div style="padding: 0; overflow-x: auto;">
                <table class="z-table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">No</th>
                            <th>Nama Guru</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
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
                            <td colspan="7" align="center" style="padding: 2rem;">
                                <div style="color: var(--z-muted);">
                                    <i data-lucide="monitor-off" style="width:48px;height:48px;opacity:0.3;margin-bottom:1rem;"></i>
                                    <p>Belum ada data supervisi kelas.</p>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php 
                            $no = 1;
                            // Fetch kelas and mapel names from siakad DB just for display, or we can use the ID directly if we join. 
                            // Since we didn't join in the controller to siakad_db, we can do it here or simply display the ID for now.
                            // Actually let's fetch mapel and kelas names inside the controller. Wait, I'll just show the names.
                            foreach($jadwal_supervisi as $row): 
                            ?>
                            <tr>
                                <td align="center" style="vertical-align:top; padding-top:1.2rem;"><?= $no++ ?></td>
                                <td style="vertical-align:top; padding-top:1.2rem;">
                                    <div style="font-weight:600; color:#1e293b;"><?= htmlspecialchars($row['nama_guru']) ?></div>
                                    <div style="font-size:0.8rem; color:#64748b; margin-top:0.2rem;">Supervisor: <?= htmlspecialchars($row['nama_supervisor']) ?></div>
                                </td>
                                <td style="vertical-align:top; padding-top:1.2rem;"><?= htmlspecialchars($row['nama_mapel']) ?></td>
                                <td style="vertical-align:top; padding-top:1.2rem;"><?= htmlspecialchars($row['nama_kelas']) ?></td>
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
                                        <button onclick="editJadwal(<?= $row['id'] ?>, <?= $row['guru_id'] ?>, <?= $row['kelas_id'] ?>, <?= $row['mapel_id'] ?>, <?= $row['supervisor_id'] ?>, '<?= $row['tanggal'] ?>', `<?= htmlspecialchars($row['keterangan'] ?? '') ?>`)" class="e-btn e-btn-outline" style="padding:0.3rem 0.5rem; color:#3b82f6; border-color:rgba(59,130,246,0.2); background:rgba(59,130,246,0.05);" title="Edit"><i data-lucide="edit" style="width:16px;height:16px;"></i></button>
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

<!-- Modal Jadwal Supervisi Kelas -->
<div id="modalJadwal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); z-index:99999; justify-content:center; align-items:center;">
    <div style="background:#fff; border-radius:12px; width:100%; max-width:500px; box-shadow:0 10px 25px rgba(0,0,0,0.2); animation: modalIn 0.3s ease;">
        <div style="display:flex; justify-content:space-between; align-items:center; padding:1.5rem; border-bottom:1px solid #e2e8f0;">
            <h3 id="modalTitle" style="margin:0; font-size:1.2rem; display:flex; align-items:center; gap:0.5rem; color:#1e293b;"><i data-lucide="plus-circle" style="color:#10b981;"></i> Jadwalkan Supervisi Kelas</h3>
            <button onclick="document.getElementById('modalJadwal').style.display='none'" style="background:none; border:none; cursor:pointer; font-size:1.5rem; color:#64748b;">&times;</button>
        </div>
        <form id="jadwalForm" action="/kurikulum/supervisi/kelas" method="POST">
            <input type="hidden" name="id" id="jadwal_id">
            <div style="padding:1.5rem; display:flex; flex-direction:column; gap:1rem;">
                <div>
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">Pilih Guru</label>
                    <select name="guru_id" id="guru_id" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:8px; background:#fff; font-family:inherit;" required onchange="loadKelas(this.value)">
                        <option value="">-- Pilih Guru --</option>
                        <?php if(isset($gurus) && is_array($gurus)): ?>
                            <?php foreach($gurus as $g): ?>
                                <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['nama_lengkap']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div style="display:flex; gap:1rem;">
                    <div style="flex:1;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">Kelas</label>
                        <select name="kelas_id" id="kelas_id" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit; background:#fff;" required disabled onchange="loadMapel(this.value)">
                            <option value="">-- Pilih Kelas --</option>
                        </select>
                    </div>
                    <div style="flex:1;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">Mata Pelajaran</label>
                        <select name="mapel_id" id="mapel_id" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit; background:#fff;" required disabled>
                            <option value="">-- Pilih Mapel --</option>
                        </select>
                    </div>
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
                            <?php if(isset($gurus) && is_array($gurus)): ?>
                                <?php foreach($gurus as $g): ?>
                                    <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['nama_lengkap']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">Catatan (Opsional)</label>
                    <textarea name="keterangan" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:8px; min-height:80px; font-family:inherit;" placeholder="Tuliskan fokus observasi..."></textarea>
                </div>
            </div>
            <div style="padding:1.5rem; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:1rem; background:#f8fafc; border-bottom-left-radius:12px; border-bottom-right-radius:12px;">
                <button type="button" onclick="document.getElementById('modalJadwal').style.display='none'" style="padding:0.75rem 1.5rem; border:1px solid #cbd5e1; background:#fff; border-radius:8px; cursor:pointer; font-weight:600; color:#475569;">Batal</button>
                <button type="submit" style="padding:0.75rem 1.5rem; background:#10b981; color:#fff; border:none; border-radius:8px; cursor:pointer; font-weight:600; box-shadow:0 4px 6px -1px rgba(16,185,129,0.2);">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>

<form id="deleteForm" action="/kurikulum/supervisi/kelas/delete" method="POST" style="display:none;">
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

    function toggleRilis(id, isReleased) {
        fetch(`${baseUrl}/kurikulum/supervisi/kelas/rilis`, {
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
        fetch(`${baseUrl}/kurikulum/supervisi/kelas/rilis-hasil`, {
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

    function openNew() {
        document.getElementById('jadwalForm').reset();
        document.getElementById('jadwal_id').value = '';
        document.getElementById('jadwalForm').action = '/kurikulum/supervisi/kelas';
        document.getElementById('modalTitle').innerHTML = '<i data-lucide="plus-circle" style="color:#10b981;"></i> Jadwalkan Supervisi Kelas';
        document.getElementById('kelas_id').disabled = true;
        document.getElementById('mapel_id').disabled = true;
        document.getElementById('modalJadwal').style.display='flex';
    }

    function loadKelas(guruId, callback = null) {
        const kelasSelect = document.getElementById('kelas_id');
        const mapelSelect = document.getElementById('mapel_id');
        
        kelasSelect.innerHTML = '<option value="">-- Pilih Kelas --</option>';
        mapelSelect.innerHTML = '<option value="">-- Pilih Mapel --</option>';
        
        kelasSelect.disabled = true;
        mapelSelect.disabled = true;
        
        if (!guruId) return;
        
        fetch(`${baseUrl}/kurikulum/supervisi/api/guru-kelas?guru_id=${guruId}`)
            .then(res => res.json())
            .then(data => {
                if (data.kelas && data.kelas.length > 0) {
                    data.kelas.forEach(k => {
                        const opt = document.createElement('option');
                        opt.value = k.id;
                        opt.textContent = k.nama_kelas;
                        kelasSelect.appendChild(opt);
                    });
                    kelasSelect.disabled = false;
                    if(callback) callback();
                }
            })
            .catch(err => {
                console.error('Error fetching kelas:', err);
                alert('Gagal mengambil data kelas');
            });
    }

    function loadMapel(kelasId, callback = null) {
        const guruId = document.getElementById('guru_id').value;
        const mapelSelect = document.getElementById('mapel_id');
        
        mapelSelect.innerHTML = '<option value="">-- Pilih Mapel --</option>';
        mapelSelect.disabled = true;
        
        if (!kelasId || !guruId) return;
        
        fetch(`${baseUrl}/kurikulum/supervisi/api/guru-kelas-mapel?guru_id=${guruId}&kelas_id=${kelasId}`)
            .then(res => res.json())
            .then(data => {
                if (data.mapel && data.mapel.length > 0) {
                    data.mapel.forEach(m => {
                        const opt = document.createElement('option');
                        opt.value = m.id;
                        opt.textContent = m.nama_mapel;
                        mapelSelect.appendChild(opt);
                    });
                    mapelSelect.disabled = false;
                    if(callback) callback();
                }
            })
            .catch(err => {
                console.error('Error fetching mapel:', err);
                alert('Gagal mengambil data mata pelajaran');
            });
    }

    function editJadwal(id, guru_id, kelas_id, mapel_id, supervisor_id, tanggal, keterangan) {
        document.getElementById('jadwal_id').value = id;
        document.getElementById('jadwalForm').action = '/kurikulum/supervisi/kelas/update';
        document.getElementById('modalTitle').innerHTML = '<i data-lucide="edit" style="color:#3b82f6;"></i> Edit Jadwal Supervisi Kelas';
        
        document.getElementById('guru_id').value = guru_id;
        document.querySelector('input[name="tanggal"]').value = tanggal;
        document.querySelector('select[name="supervisor_id"]').value = supervisor_id;
        document.querySelector('textarea[name="keterangan"]').value = keterangan;
        
        loadKelas(guru_id, function() {
            document.getElementById('kelas_id').value = kelas_id;
            loadMapel(kelas_id, function() {
                document.getElementById('mapel_id').value = mapel_id;
            });
        });
        
        document.getElementById('modalJadwal').style.display='flex';
    }

    function openEvaluasi(id) {
        window.location.href = baseUrl + '/kurikulum/supervisi/kelas/evaluasi?id=' + id;
    }

    function cetakJadwal(id) {
        window.open(baseUrl + '/kurikulum/supervisi/kelas/cetak?id=' + id, '_blank');
    }

    function deleteJadwal(id) {
        Swal.fire({
            title: 'Hapus Jadwal?',
            text: "Apakah Anda yakin ingin menghapus jadwal supervisi ini? Data tidak dapat dikembalikan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i data-lucide="trash-2" style="width:16px; height:16px; vertical-align:middle; margin-right:5px;"></i> Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete_id').value = id;
                document.getElementById('deleteForm').submit();
            }
        });
    }
</script>
</body>
</html>
