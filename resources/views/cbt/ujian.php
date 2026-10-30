<?php
$pageTitle = 'Kelola Paket Ujian';
$active_menu = 'ujian';
?>
<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/header.php'; ?>

        <div class="z-scroll" style="padding: 1.5rem 2rem 3rem;">

            <!-- Alerts -->
            <?php if(isset($_GET['msg']) && $_GET['msg'] === 'saved'): ?>
            <div style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                <i data-lucide="check-circle" style="width: 20px; height: 20px; color: #22c55e;"></i> Paket ujian berhasil disimpan!
            </div>
            <?php endif; ?>

            <?php if(isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                <i data-lucide="trash-2" style="width: 20px; height: 20px; color: #ef4444;"></i> Paket ujian dan butir soal terkait berhasil dihapus.
            </div>
            <?php endif; ?>

            <!-- Elite Page Header -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-badge">BANK & PAKET SOAL</div>
                        <h1><i data-lucide="file-question"></i> Kelola Paket Ujian & Bank Soal</h1>
                        <p>Buat paket ujian baru, susun butir soal pilihan ganda, tarik soal dari bank soal pusat, dan konfigurasi bobot nilai.</p>
                    </div>
                    <div class="header-actions-premium" style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <button type="button" onclick="openUjianModal()" class="e-btn e-btn-glass">
                            <i data-lucide="plus-circle"></i> Tambah Ujian Baru
                        </button>
                        <a href="<?php echo base_url('bank_soal'); ?>" class="e-btn e-btn-blue-light">
                            <i data-lucide="database"></i> Buka Bank Soal Pusat
                        </a>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card" style="border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden; background: #ffffff;">
                <div style="padding: 1.25rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div style="font-weight: 800; font-size: 0.95rem; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                        <span>Daftar Paket Ujian</span>
                        <span style="font-size: 0.75rem; background: #e0e7ff; color: #4338ca; padding: 2px 8px; border-radius: 100px; font-weight: 800;">
                            <?php echo count($ujianList); ?> Paket
                        </span>
                    </div>
                </div>

                <div class="z-table-wrap">
                    <table class="z-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Kode Ujian</th>
                                <th>Nama Paket Ujian</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelas Sasaran</th>
                                <th>Jumlah Soal</th>
                                <th>KKM</th>
                                <th>Status</th>
                                <th style="text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ujianList)): ?>
                            <tr>
                                <td colspan="9" style="padding: 3rem 1.5rem; text-align: center; color: #94a3b8;">
                                    <i data-lucide="folder-search" style="width: 44px; height: 44px; margin-bottom: 10px; color: #cbd5e1;"></i>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: #475569;">Belum ada paket ujian yang dibuat</div>
                                    <div style="font-size: 0.78rem;">Klik tombol "Tambah Ujian Baru" untuk membuat ujian dan menginput butir soal.</div>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach($ujianList as $u): 
                                    $st = $u['status'] ?? 'Draft';
                                    $stBg = ($st === 'Aktif') ? '#dcfce7' : (($st === 'Selesai') ? '#f1f5f9' : '#fef3c7');
                                    $stCol = ($st === 'Aktif') ? '#15803d' : (($st === 'Selesai') ? '#475569' : '#b45309');
                                    $jmlSoal = (int)($u['jumlah_soal'] ?? 0);
                                ?>
                                <tr>
                                    <td style="color: #94a3b8; font-weight: 600;"><?php echo $no++; ?></td>
                                    <td>
                                        <span style="font-family: monospace; font-weight: 700; color: #4338ca; background: #eef2ff; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem;">
                                            <?php echo htmlspecialchars($u['kode_ujian']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 800; color: #0f172a; font-size: 0.9rem;"><?php echo htmlspecialchars($u['nama_ujian']); ?></div>
                                        <div style="font-size: 0.72rem; color: #64748b;">
                                            Durasi: <?php echo $u['durasi_menit']; ?> Menit &bull; Acak Soal: <?php echo $u['acak_soal'] ? 'Ya' : 'Tidak'; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: #1e293b;"><?php echo htmlspecialchars($u['mapel_nama']); ?></div>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.74rem; font-weight: 700; background: #e0e7ff; color: #4338ca; padding: 2px 8px; border-radius: 6px;">
                                            <?php echo htmlspecialchars($u['kelas_nama'] ?: 'Semua Kelas'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo base_url('cbt/soal?ujian_id=' . $u['id']); ?>" style="text-decoration: none; font-weight: 800; font-size: 0.8rem; background: #f0fdf4; color: #166534; padding: 3px 10px; border-radius: 100px; border: 1px solid #bbf7d0; display: inline-flex; align-items: center; gap: 4px;">
                                            <i data-lucide="file-text" style="width: 12px; height: 12px;"></i> <?php echo $jmlSoal; ?> Soal
                                        </a>
                                    </td>
                                    <td>
                                        <span style="font-weight: 800; color: #0f172a; font-size: 0.82rem;"><?php echo number_format($u['kkm'], 0); ?></span>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.72rem; font-weight: 800; padding: 3px 9px; border-radius: 6px; background: <?php echo $stBg; ?>; color: <?php echo $stCol; ?>;">
                                            <?php echo htmlspecialchars($st); ?>
                                        </span>
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <!-- Tombol Kelola / Tambah Soal -->
                                        <a href="<?php echo base_url('cbt/soal?ujian_id=' . $u['id']); ?>" class="e-btn e-btn-primary" style="padding: 5px 11px; font-size: 0.74rem; background: #4f46e5;" title="Kelola & Tambah Soal">
                                            <i data-lucide="plus-circle" style="width: 13px; height: 13px;"></i> Tambah Soal
                                        </a>

                                        <!-- Edit Ujian Modal -->
                                        <button type="button" onclick="editUjianModal(<?php echo htmlspecialchars(json_encode($u)); ?>)" class="e-btn e-btn-blue-light" style="padding: 5px 9px; font-size: 0.74rem;" title="Edit Pengaturan Ujian">
                                            <i data-lucide="settings" style="width: 13px; height: 13px;"></i>
                                        </button>

                                        <!-- Hapus Ujian -->
                                        <form method="POST" action="<?php echo base_url('cbt/ujian/delete'); ?>" style="display: inline-block;" onsubmit="return confirm('Hapus paket ujian ini beserta seluruh butir soalnya?')">
                                            <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                            <button type="submit" class="e-btn e-btn-dark" style="padding: 5px 9px; font-size: 0.74rem; background: #fee2e2; color: #b91c1c; border-color: #fecaca;" title="Hapus Paket">
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

<!-- Modal Tambah / Edit Paket Ujian -->
<div id="modalUjian" class="modal-overlay" onclick="if(event.target === this) this.classList.remove('open')">
    <div style="background: #ffffff; width: 90%; max-width: 580px; border-radius: 20px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.15); border: 1px solid #e2e8f0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 id="modalUjianTitle" style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="plus-circle" style="width: 20px; height: 20px; color: #4f46e5;"></i>
                Tambah Paket Ujian Baru
            </h3>
            <button type="button" onclick="document.getElementById('modalUjian').classList.remove('open')" style="background: none; border: none; cursor: pointer; color: #94a3b8;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>

        <form method="POST" action="<?php echo base_url('cbt/ujian/save'); ?>">
            <input type="hidden" name="id" id="form_ujian_id" value="">

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 12px; margin-bottom: 1rem;">
                <div>
                    <label class="label-sm">Kode Ujian <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="kode_ujian" id="form_kode_ujian" class="input-sm" placeholder="UJN-001" required>
                </div>
                <div>
                    <label class="label-sm">Nama Paket Ujian <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="nama_ujian" id="form_nama_ujian" class="input-sm" placeholder="Contoh: Penilaian Akhir Semester Ganjil" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 12px; margin-bottom: 1rem;">
                <div>
                    <label class="label-sm">Mata Pelajaran <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="mapel_nama" id="form_mapel_nama" class="input-sm" placeholder="Contoh: Matematika, Fiqih, dll." required>
                </div>
                <div>
                    <label class="label-sm">Kelas Sasaran</label>
                    <input type="text" name="kelas_nama" id="form_kelas_nama" class="input-sm" placeholder="Semua Kelas / X-IPA / XI">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 1rem;">
                <div>
                    <label class="label-sm">Durasi (Menit)</label>
                    <input type="number" name="durasi_menit" id="form_durasi" class="input-sm" value="90" min="10" max="300" required>
                </div>
                <div>
                    <label class="label-sm">Nilai KKM</label>
                    <input type="number" name="kkm" id="form_kkm" class="input-sm" value="75" min="0" max="100" required>
                </div>
                <div>
                    <label class="label-sm">Status Ujian</label>
                    <select name="status" id="form_status" class="input-sm">
                        <option value="Draft">Draft</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 1.5rem; background: #f8fafc; padding: 10px 14px; border-radius: 10px; border: 1px solid #e2e8f0;">
                <label style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; font-weight: 700; color: #334155; cursor: pointer;">
                    <input type="checkbox" name="acak_soal" id="form_acak_soal" value="1" checked> Acak Urutan Soal
                </label>
                <label style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; font-weight: 700; color: #334155; cursor: pointer;">
                    <input type="checkbox" name="acak_opsi" id="form_acak_opsi" value="1" checked> Acak Opsi Pilihan (A-E)
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" onclick="document.getElementById('modalUjian').classList.remove('open')" class="e-btn e-btn-dark">
                    Batal
                </button>
                <button type="submit" class="e-btn e-btn-primary" style="background: #4f46e5;">
                    Simpan Paket Ujian
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openUjianModal() {
        document.getElementById('form_ujian_id').value = '';
        document.getElementById('form_kode_ujian').value = 'UJN-' + Math.floor(100 + Math.random() * 900);
        document.getElementById('form_nama_ujian').value = '';
        document.getElementById('form_mapel_nama').value = '';
        document.getElementById('form_kelas_nama').value = 'Semua Kelas';
        document.getElementById('form_durasi').value = '90';
        document.getElementById('form_kkm').value = '75';
        document.getElementById('form_status').value = 'Draft';
        document.getElementById('form_acak_soal').checked = true;
        document.getElementById('form_acak_opsi').checked = true;
        document.getElementById('modalUjianTitle').innerHTML = '<i data-lucide="plus-circle" style="width:20px;height:20px;color:#4f46e5;"></i> Tambah Paket Ujian Baru';
        document.getElementById('modalUjian').classList.add('open');
        lucide.createIcons();
    }

    function editUjianModal(data) {
        document.getElementById('form_ujian_id').value = data.id;
        document.getElementById('form_kode_ujian').value = data.kode_ujian;
        document.getElementById('form_nama_ujian').value = data.nama_ujian;
        document.getElementById('form_mapel_nama').value = data.mapel_nama;
        document.getElementById('form_kelas_nama').value = data.kelas_nama || 'Semua Kelas';
        document.getElementById('form_durasi').value = data.durasi_menit;
        document.getElementById('form_kkm').value = data.kkm;
        document.getElementById('form_status').value = data.status;
        document.getElementById('form_acak_soal').checked = (data.acak_soal == 1);
        document.getElementById('form_acak_opsi').checked = (data.acak_opsi == 1);
        document.getElementById('modalUjianTitle').innerHTML = '<i data-lucide="settings" style="width:20px;height:20px;color:#4f46e5;"></i> Edit Paket Ujian: ' + data.kode_ujian;
        document.getElementById('modalUjian').classList.add('open');
        lucide.createIcons();
    }
</script>
