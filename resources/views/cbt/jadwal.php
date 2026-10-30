<?php
$pageTitle = 'Jadwal Ujian CBT';
$active_menu = 'jadwal';
?>
<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/header.php'; ?>

        <div class="z-scroll" style="padding: 1.5rem 2rem 3rem;">

            <!-- Alerts -->
            <?php if(isset($_GET['msg']) && $_GET['msg'] === 'saved'): ?>
            <div style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                <i data-lucide="check-circle" style="width: 20px; height: 20px; color: #22c55e;"></i> Jadwal ujian berhasil disimpan!
            </div>
            <?php endif; ?>

            <?php if(isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                <i data-lucide="trash-2" style="width: 20px; height: 20px; color: #ef4444;"></i> Jadwal ujian berhasil dihapus.
            </div>
            <?php endif; ?>

            <!-- Elite Page Header -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-badge">JADWAL & SESI</div>
                        <h1><i data-lucide="calendar"></i> Jadwal Pelaksanaan Ujian CBT</h1>
                        <p>Atur jadwal waktu mulai, batas akhir pengerjaan, durasi sesi, dan kelas peserta ujian berbasis komputer.</p>
                    </div>
                    <div class="header-actions-premium" style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <button type="button" onclick="openJadwalModal()" class="e-btn e-btn-glass">
                            <i data-lucide="plus-circle"></i> Jadwalkan Ujian Baru
                        </button>
                        <a href="<?php echo base_url('cbt/token'); ?>" class="e-btn e-btn-blue-light">
                            <i data-lucide="key"></i> Rilis Token
                        </a>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card" style="border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden; background: #ffffff;">
                <div style="padding: 1.25rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div style="font-weight: 800; font-size: 0.95rem; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                        <span>Daftar Jadwal Ujian Aktif & Terjadwal</span>
                        <span style="font-size: 0.75rem; background: #e0e7ff; color: #4338ca; padding: 2px 8px; border-radius: 100px; font-weight: 800;">
                            <?php echo count($ujianList); ?> Sesi
                        </span>
                    </div>
                </div>

                <div class="z-table-wrap">
                    <table class="z-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Kode & Nama Ujian</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelas Peserta</th>
                                <th>Waktu Pelaksanaan</th>
                                <th>Durasi</th>
                                <th>Status</th>
                                <th style="text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ujianList)): ?>
                            <tr>
                                <td colspan="8" style="padding: 3rem 1.5rem; text-align: center; color: #94a3b8;">
                                    <i data-lucide="calendar-x" style="width: 44px; height: 44px; margin-bottom: 10px; color: #cbd5e1;"></i>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: #475569;">Belum ada jadwal ujian yang dibuat</div>
                                    <div style="font-size: 0.78rem;">Klik tombol "Jadwalkan Ujian Baru" di atas untuk menambahkan sesi ujian.</div>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach($ujianList as $u): 
                                    $st = $u['status'] ?? 'Draft';
                                    $stBg = ($st === 'Aktif') ? '#dcfce7' : (($st === 'Selesai') ? '#f1f5f9' : '#fef3c7');
                                    $stCol = ($st === 'Aktif') ? '#15803d' : (($st === 'Selesai') ? '#475569' : '#b45309');
                                ?>
                                <tr>
                                    <td style="color: #94a3b8; font-weight: 600;"><?php echo $no++; ?></td>
                                    <td>
                                        <div style="font-weight: 800; color: #0f172a; font-size: 0.88rem;"><?php echo htmlspecialchars($u['nama_ujian']); ?></div>
                                        <div style="font-size: 0.72rem; color: #64748b; font-family: monospace;">Kode: <?php echo htmlspecialchars($u['kode_ujian']); ?></div>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.76rem; font-weight: 700; color: #1e293b;">
                                            <?php echo htmlspecialchars($u['mapel_nama']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.72rem; font-weight: 700; padding: 3px 8px; border-radius: 6px; background: #e0e7ff; color: #4338ca;">
                                            <?php echo htmlspecialchars($u['kelas_nama'] ?: 'Semua Kelas'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.78rem; font-weight: 700; color: #1e293b;">
                                            Mulai: <?php echo !empty($u['tgl_mulai']) ? date('d M Y, H:i', strtotime($u['tgl_mulai'])) : '-'; ?>
                                        </div>
                                        <div style="font-size: 0.72rem; color: #64748b;">
                                            Selesai: <?php echo !empty($u['tgl_selesai']) ? date('d M Y, H:i', strtotime($u['tgl_selesai'])) : '-'; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="font-weight: 700; font-size: 0.82rem; color: #0f172a;"><?php echo $u['durasi_menit']; ?> mnt</span>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.72rem; font-weight: 800; padding: 3px 9px; border-radius: 6px; background: <?php echo $stBg; ?>; color: <?php echo $stCol; ?>;">
                                            <?php echo htmlspecialchars($st); ?>
                                        </span>
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <!-- Toggle Status Aktif / Selesai -->
                                        <form method="POST" action="<?php echo base_url('cbt/jadwal/toggle-status'); ?>" style="display: inline-block;">
                                            <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                            <input type="hidden" name="current_status" value="<?php echo $st; ?>">
                                            <?php if ($st === 'Aktif'): ?>
                                            <button type="submit" class="e-btn e-btn-dark" style="padding: 4px 8px; font-size: 0.72rem; background: #fee2e2; color: #b91c1c; border-color: #fecaca;" title="Nonaktifkan / Selesai">
                                                <i data-lucide="stop-circle" style="width: 13px; height: 13px;"></i> Tutup
                                            </button>
                                            <?php else: ?>
                                            <button type="submit" class="e-btn e-btn-primary" style="padding: 4px 8px; font-size: 0.72rem; background: #16a34a;" title="Aktifkan Ujian Sekarang">
                                                <i data-lucide="play-circle" style="width: 13px; height: 13px;"></i> Aktifkan
                                            </button>
                                            <?php endif; ?>
                                        </form>

                                        <!-- Edit Jadwal Modal Trigger -->
                                        <button type="button" onclick="editJadwalModal(<?php echo htmlspecialchars(json_encode($u)); ?>)" class="e-btn e-btn-blue-light" style="padding: 4px 8px; font-size: 0.72rem;" title="Edit Waktu">
                                            <i data-lucide="clock" style="width: 13px; height: 13px;"></i>
                                        </button>

                                        <!-- Hapus Jadwal -->
                                        <form method="POST" action="<?php echo base_url('cbt/jadwal/delete'); ?>" style="display: inline-block;" onsubmit="return confirm('Hapus jadwal ujian ini?')">
                                            <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                            <button type="submit" class="e-btn e-btn-dark" style="padding: 4px 8px; font-size: 0.72rem; background: #f1f5f9; color: #64748b;" title="Hapus">
                                                <i data-lucide="trash-2" style="width: 13px; height: 13px;"></i>
                                            </button>
                                        </form>
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
</div>

<!-- Modal Jadwalkan Ujian Baru / Edit -->
<div id="modalJadwal" class="modal-overlay" onclick="if(event.target === this) this.classList.remove('open')">
    <div style="background: #ffffff; width: 90%; max-width: 520px; border-radius: 20px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.15); border: 1px solid #e2e8f0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 id="modalJadwalTitle" style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="calendar-plus" style="width: 20px; height: 20px; color: #4f46e5;"></i>
                Jadwalkan Sesi Ujian
            </h3>
            <button type="button" onclick="document.getElementById('modalJadwal').classList.remove('open')" style="background: none; border: none; cursor: pointer; color: #94a3b8;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>

        <form method="POST" action="<?php echo base_url('cbt/jadwal/save'); ?>">
            <input type="hidden" name="id" id="jadwal_id" value="">

            <div style="margin-bottom: 1rem;">
                <label class="label-sm">Pilih Paket Ujian <span style="color:#ef4444;">*</span></label>
                <select name="ujian_id" id="jadwal_ujian_id" class="input-sm" required>
                    <option value="">-- Pilih Paket Ujian --</option>
                    <?php foreach($ujianList as $uj): ?>
                        <option value="<?php echo $uj['id']; ?>"><?php echo htmlspecialchars($uj['nama_ujian'] . ' (' . $uj['mapel_nama'] . ')'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 1rem;">
                <div>
                    <label class="label-sm">Waktu Mulai <span style="color:#ef4444;">*</span></label>
                    <input type="datetime-local" name="tgl_mulai" id="jadwal_tgl_mulai" class="input-sm" required>
                </div>
                <div>
                    <label class="label-sm">Waktu Selesai <span style="color:#ef4444;">*</span></label>
                    <input type="datetime-local" name="tgl_selesai" id="jadwal_tgl_selesai" class="input-sm" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 1.5rem;">
                <div>
                    <label class="label-sm">Durasi Ujian (Menit) <span style="color:#ef4444;">*</span></label>
                    <input type="number" name="durasi_menit" id="jadwal_durasi" class="input-sm" value="90" min="10" max="300" required>
                </div>
                <div>
                    <label class="label-sm">Status Awal</label>
                    <select name="status" id="jadwal_status" class="input-sm">
                        <option value="Aktif">Aktif (Langsung Buka)</option>
                        <option value="Draft">Draft (Terjadwal)</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" onclick="document.getElementById('modalJadwal').classList.remove('open')" class="e-btn e-btn-dark">
                    Batal
                </button>
                <button type="submit" class="e-btn e-btn-primary" style="background: #4f46e5;">
                    Simpan Jadwal Ujian
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openJadwalModal() {
        document.getElementById('jadwal_id').value = '';
        document.getElementById('modalJadwalTitle').innerHTML = '<i data-lucide="calendar-plus" style="width:20px;height:20px;color:#4f46e5;"></i> Jadwalkan Sesi Ujian';
        document.getElementById('jadwal_durasi').value = '90';
        document.getElementById('jadwal_status').value = 'Aktif';
        document.getElementById('modalJadwal').classList.add('open');
        lucide.createIcons();
    }

    function editJadwalModal(data) {
        document.getElementById('jadwal_id').value = data.id;
        document.getElementById('jadwal_ujian_id').value = data.id;
        document.getElementById('jadwal_durasi').value = data.durasi_menit;
        document.getElementById('jadwal_status').value = data.status;
        if (data.tgl_mulai) {
            document.getElementById('jadwal_tgl_mulai').value = data.tgl_mulai.replace(' ', 'T').substring(0, 16);
        }
        if (data.tgl_selesai) {
            document.getElementById('jadwal_tgl_selesai').value = data.tgl_selesai.replace(' ', 'T').substring(0, 16);
        }
        document.getElementById('modalJadwalTitle').innerHTML = '<i data-lucide="edit-3" style="width:20px;height:20px;color:#4f46e5;"></i> Edit Jadwal: ' + data.nama_ujian;
        document.getElementById('modalJadwal').classList.add('open');
        lucide.createIcons();
    }
</script>

