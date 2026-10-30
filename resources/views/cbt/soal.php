<?php
$pageTitle = 'Kelola Soal Ujian';
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
                <i data-lucide="check-circle" style="width: 20px; height: 20px; color: #22c55e;"></i> Butir soal berhasil disimpan!
            </div>
            <?php endif; ?>

            <?php if(isset($_GET['msg']) && $_GET['msg'] === 'imported'): ?>
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                <i data-lucide="check-check" style="width: 20px; height: 20px; color: #3b82f6;"></i> Soal berhasil diimpor dari Bank Soal!
            </div>
            <?php endif; ?>

            <?php if(isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                <i data-lucide="trash-2" style="width: 20px; height: 20px; color: #ef4444;"></i> Butir soal telah dihapus.
            </div>
            <?php endif; ?>

            <!-- Elite Page Header -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-badge"><?php echo htmlspecialchars($ujianInfo['kode_ujian']); ?> &bull; <?php echo htmlspecialchars($ujianInfo['mapel_nama']); ?></div>
                        <h1><i data-lucide="file-edit"></i> <?php echo htmlspecialchars($ujianInfo['nama_ujian']); ?></h1>
                        <p>Total Butir Soal: <strong><?php echo count($soalList); ?> Soal</strong> &bull; Durasi: <?php echo $ujianInfo['durasi_menit']; ?> Menit &bull; KKM: <?php echo number_format($ujianInfo['kkm'], 0); ?></p>
                    </div>
                    <div class="header-actions-premium" style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <button type="button" onclick="document.getElementById('modalTambahSoal').classList.add('open')" class="e-btn e-btn-glass">
                            <i data-lucide="plus-circle"></i> Tambah Soal Manual
                        </button>
                        <button type="button" onclick="document.getElementById('modalImportBank').classList.add('open')" class="e-btn e-btn-blue-light">
                            <i data-lucide="import"></i> Tarik dari Bank Soal
                        </button>
                        <a href="<?php echo base_url('cbt/ujian'); ?>" class="e-btn e-btn-dark">
                            <i data-lucide="arrow-left"></i> Kembali ke Paket
                        </a>
                    </div>
                </div>
            </div>

            <!-- List Butir Soal -->
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                <?php if (empty($soalList)): ?>
                    <div class="card" style="padding: 3.5rem 1.5rem; text-align: center; color: #94a3b8; border-radius: 18px;">
                        <i data-lucide="help-circle" style="width: 48px; height: 48px; margin-bottom: 12px; color: #cbd5e1;"></i>
                        <h3 style="margin: 0 0 6px 0; font-size: 1.1rem; color: #1e293b;">Belum Ada Butir Soal</h3>
                        <p style="margin: 0 0 1.25rem 0; font-size: 0.85rem;">Tambahkan butir soal secara manual atau impor dari Bank Soal Pusat.</p>
                        <div style="display: flex; justify-content: center; gap: 10px;">
                            <button type="button" onclick="document.getElementById('modalTambahSoal').classList.add('open')" class="e-btn e-btn-primary" style="background: #4f46e5;">
                                <i data-lucide="plus-circle"></i> Tambah Soal Pertama
                            </button>
                            <button type="button" onclick="document.getElementById('modalImportBank').classList.add('open')" class="e-btn e-btn-blue-light">
                                <i data-lucide="import"></i> Impor dari Bank Soal
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <?php $no = 1; foreach($soalList as $s): ?>
                    <div class="card" style="padding: 1.5rem; border-radius: 18px; border: 1px solid #e2e8f0; background: #ffffff;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="width: 32px; height: 32px; border-radius: 8px; background: #4f46e5; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.9rem;">
                                    <?php echo $no++; ?>
                                </span>
                                <span style="font-size: 0.75rem; font-weight: 700; background: #f1f5f9; color: #64748b; padding: 3px 8px; border-radius: 6px;">
                                    Bobot: <?php echo $s['bobot']; ?> Poin
                                </span>
                                <span style="font-size: 0.75rem; font-weight: 800; background: #dcfce7; color: #15803d; padding: 3px 10px; border-radius: 6px;">
                                    Kunci: <?php echo htmlspecialchars($s['kunci_jawaban']); ?>
                                </span>
                            </div>
                            <div>
                                <form method="POST" action="<?php echo base_url('cbt/soal/delete'); ?>" style="margin: 0;" onsubmit="return confirm('Hapus nomor soal ini?')">
                                    <input type="hidden" name="soal_id" value="<?php echo $s['id']; ?>">
                                    <input type="hidden" name="ujian_id" value="<?php echo $ujianInfo['id']; ?>">
                                    <button type="submit" class="e-btn e-btn-dark" style="padding: 4px 8px; font-size: 0.72rem; background: #fee2e2; color: #b91c1c; border-color: #fecaca;" title="Hapus Soal">
                                        <i data-lucide="trash-2" style="width: 13px; height: 13px;"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Isi Pertanyaan -->
                        <div style="font-size: 0.95rem; color: #1e293b; line-height: 1.6; margin-bottom: 1.25rem; font-weight: 600;">
                            <?php echo nl2br(htmlspecialchars($s['pertanyaan'])); ?>
                        </div>

                        <!-- Opsi A - E -->
                        <div style="display: grid; grid-template-columns: 1fr; gap: 8px;">
                            <?php 
                            $opsi = ['A' => $s['opsi_a'], 'B' => $s['opsi_b'], 'C' => $s['opsi_c'], 'D' => $s['opsi_d'], 'E' => $s['opsi_e']];
                            foreach($opsi as $k => $v): 
                                if (empty(trim($v ?? ''))) continue;
                                $isKunci = (strtoupper($s['kunci_jawaban']) === $k);
                            ?>
                            <div style="padding: 8px 12px; border-radius: 10px; border: 1px solid <?php echo $isKunci ? '#86efac' : '#e2e8f0'; ?>; background: <?php echo $isKunci ? '#f0fdf4' : '#f8fafc'; ?>; display: flex; align-items: center; gap: 10px;">
                                <span style="width: 24px; height: 24px; border-radius: 6px; background: <?php echo $isKunci ? '#22c55e' : '#cbd5e1'; ?>; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.75rem;">
                                    <?php echo $k; ?>
                                </span>
                                <span style="font-size: 0.88rem; color: <?php echo $isKunci ? '#166534' : '#334155'; ?>; font-weight: <?php echo $isKunci ? '700' : '500'; ?>;">
                                    <?php echo htmlspecialchars($v); ?>
                                </span>
                                <?php if($isKunci): ?>
                                    <span style="margin-left: auto; font-size: 0.72rem; font-weight: 800; color: #15803d; background: #dcfce7; padding: 2px 8px; border-radius: 4px;">Kunci Benar</span>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<!-- Modal Tambah Butir Soal Manual -->
<div id="modalTambahSoal" class="modal-overlay" onclick="if(event.target === this) this.classList.remove('open')">
    <div style="background: #ffffff; width: 92%; max-width: 650px; border-radius: 20px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.15); border: 1px solid #e2e8f0; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="plus-circle" style="width: 20px; height: 20px; color: #4f46e5;"></i>
                Tambah Butir Soal Pilihan Ganda
            </h3>
            <button type="button" onclick="document.getElementById('modalTambahSoal').classList.remove('open')" style="background: none; border: none; cursor: pointer; color: #94a3b8;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>

        <form method="POST" action="<?php echo base_url('cbt/soal/save'); ?>">
            <input type="hidden" name="ujian_id" value="<?php echo $ujianInfo['id']; ?>">

            <div style="margin-bottom: 1rem;">
                <label class="label-sm">Pertanyaan / Butir Soal <span style="color:#ef4444;">*</span></label>
                <textarea name="pertanyaan" class="input-sm z-textarea" style="min-height: 90px;" placeholder="Tuliskan teks pertanyaan di sini..." required></textarea>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 1.25rem;">
                <div>
                    <label class="label-sm">Pilihan Jawaban A <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="opsi_a" class="input-sm" placeholder="Teks opsi A" required>
                </div>
                <div>
                    <label class="label-sm">Pilihan Jawaban B <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="opsi_b" class="input-sm" placeholder="Teks opsi B" required>
                </div>
                <div>
                    <label class="label-sm">Pilihan Jawaban C</label>
                    <input type="text" name="opsi_c" class="input-sm" placeholder="Teks opsi C (opsional)">
                </div>
                <div>
                    <label class="label-sm">Pilihan Jawaban D</label>
                    <input type="text" name="opsi_d" class="input-sm" placeholder="Teks opsi D (opsional)">
                </div>
                <div>
                    <label class="label-sm">Pilihan Jawaban E</label>
                    <input type="text" name="opsi_e" class="input-sm" placeholder="Teks opsi E (opsional)">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 1.5rem;">
                <div>
                    <label class="label-sm">Kunci Jawaban Benar <span style="color:#ef4444;">*</span></label>
                    <select name="kunci_jawaban" class="input-sm" required>
                        <option value="A">Opsi A</option>
                        <option value="B">Opsi B</option>
                        <option value="C">Opsi C</option>
                        <option value="D">Opsi D</option>
                        <option value="E">Opsi E</option>
                    </select>
                </div>
                <div>
                    <label class="label-sm">Bobot Skor Poin</label>
                    <input type="number" step="0.5" name="bobot" class="input-sm" value="1.0" min="0.5" max="10">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" onclick="document.getElementById('modalTambahSoal').classList.remove('open')" class="e-btn e-btn-dark">
                    Batal
                </button>
                <button type="submit" class="e-btn e-btn-primary" style="background: #4f46e5;">
                    Simpan Butir Soal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Impor dari Bank Soal -->
<div id="modalImportBank" class="modal-overlay" onclick="if(event.target === this) this.classList.remove('open')">
    <div style="background: #ffffff; width: 90%; max-width: 540px; border-radius: 20px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.15); border: 1px solid #e2e8f0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="database" style="width: 20px; height: 20px; color: #0ea5e9;"></i>
                Tarik Soal dari Bank Soal
            </h3>
            <button type="button" onclick="document.getElementById('modalImportBank').classList.remove('open')" style="background: none; border: none; cursor: pointer; color: #94a3b8;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>

        <form method="POST" action="<?php echo base_url('cbt/soal/import-bank'); ?>">
            <input type="hidden" name="ujian_id" value="<?php echo $ujianInfo['id']; ?>">

            <div style="margin-bottom: 1.25rem;">
                <label class="label-sm">Pilih Topik Bank Soal</label>
                <select name="topik_id" class="input-sm" required>
                    <option value="">-- Pilih Topik Soal --</option>
                    <?php foreach($bankTopiks as $bt): ?>
                        <option value="<?php echo $bt['id']; ?>"><?php echo htmlspecialchars($bt['nama_topik'] . ' (' . ($bt['jenis'] ?? 'Umum') . ')'); ?></option>
                    <?php endforeach; ?>
                </select>
                <div style="font-size: 0.72rem; color: #64748b; margin-top: 4px;">Seluruh soal pilihan ganda dari topik yang dipilih akan disalin ke ujian ini.</div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" onclick="document.getElementById('modalImportBank').classList.remove('open')" class="e-btn e-btn-dark">
                    Batal
                </button>
                <button type="submit" class="e-btn e-btn-primary" style="background: #0ea5e9;">
                    <i data-lucide="download" style="width: 14px; height: 14px;"></i> Impor Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

