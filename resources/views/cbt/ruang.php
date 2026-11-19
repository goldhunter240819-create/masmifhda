<?php
$pageTitle = 'Kelola Ruang Ujian CBT';
$active_menu = 'ruang';
?>
<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/header.php'; ?>

        <div class="z-scroll" style="padding: 0 0 3rem 0;">

            <?php if (isset($_GET['success'])): ?>
            <div style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 14px 20px; border-radius: 14px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px; font-weight: 700;">
                <i data-lucide="check-circle" style="width: 22px; height: 22px; color: #22c55e;"></i>
                <div>Data Ruang Ujian Berhasil Disimpan! Pola meja dan slot kursi otomatis diperbarui.</div>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['saved_pengawas'])): ?>
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 14px 20px; border-radius: 14px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px; font-weight: 700;">
                <i data-lucide="check-circle" style="width: 22px; height: 22px; color: #2563eb;"></i>
                <div>Tugas Pengawas Ruang Per-Sesi Berhasil Disimpan & Diterapkan ke Dashboard Guru!</div>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['deleted'])): ?>
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 20px; border-radius: 14px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px; font-weight: 700;">
                <i data-lucide="trash-2" style="width: 22px; height: 22px; color: #ef4444;"></i>
                <div>Ruang Ujian beserta denah tempat duduk berhasil dihapus.</div>
            </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-badge">MANAJEMEN RUANG & MEJA CBT</div>
                        <h1><i data-lucide="door-open"></i> Kelola Ruang Ujian</h1>
                        <p>Atur tata letak meja ujian (meja gandeng 2x atau meja tunggal 1x), baris ke samping dan ke belakang, serta penugasan pengawas per-sesi.</p>
                    </div>
                    <div class="header-actions-premium" style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <button type="button" onclick="bukaModalRuang()" class="e-btn e-btn-primary" style="background: #2563eb;">
                            <i data-lucide="plus-circle"></i> Tambah Ruang Ujian
                        </button>
                        <a href="<?php echo base_url('cbt/denah?topik_id=' . $selectedTopikId); ?>" class="e-btn e-btn-glass">
                            <i data-lucide="grid"></i> Atur Denah Tempat Duduk
                        </a>
                    </div>
                </div>
            </div>

            <!-- Topik Selector Bar -->
            <div class="card" style="padding: 1.1rem 1.4rem; border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; background: #ffffff; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); color: #2563eb; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(37,99,235,0.1);">
                        <i data-lucide="layers" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.72rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">TOPIK UJIAN CBT AKTIF</div>
                        <div style="font-weight: 800; color: #0f172a; font-size: 0.95rem;">Kelola Ruang & Pengawas Terisolasi Per-Topik</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <label style="font-size: 0.82rem; font-weight: 700; color: #475569;">Pilih Topik:</label>
                    <select onchange="window.location.href='<?php echo base_url('cbt/ruang?topik_id='); ?>' + this.value" class="input-sm" style="min-width: 280px; font-weight: 700; border-radius: 10px; border-color: #cbd5e1; background: #f8fafc; cursor: pointer; padding: 7px 12px;">
                        <?php foreach ($topikList as $tp): ?>
                        <option value="<?php echo $tp['id']; ?>" <?php echo ($selectedTopikId == $tp['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($tp['nama_topik']); ?> (<?php echo htmlspecialchars($tp['tahun_ajaran']); ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- KPI Stats Summary -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                <div class="card" style="padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="building"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 900; color: #0f172a; line-height: 1;"><?php echo $totalRuang; ?></div>
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-top: 4px;">Total Ruang Ujian</div>
                    </div>
                </div>

                <div class="card" style="padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #f0fdf4; color: #16a34a; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="armchair"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 900; color: #15803d; line-height: 1;"><?php echo $totalKapasitas; ?></div>
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-top: 4px;">Total Kapasitas Kursi</div>
                    </div>
                </div>

                <div class="card" style="padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="users"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 900; color: #4338ca; line-height: 1;"><?php echo $totalTerisi; ?></div>
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-top: 4px;">Siswa Teralokasi (Topik Ini)</div>
                    </div>
                </div>

                <div class="card" style="padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #fffbeb; color: #d97706; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="inbox"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 900; color: #b45309; line-height: 1;"><?php echo $totalKosong; ?></div>
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-top: 4px;">Kursi Kosong Tersedia</div>
                    </div>
                </div>
            </div>

            <!-- Table Card Ruangan -->
            <div class="card" style="border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden; background: #ffffff;">
                <div style="padding: 1.25rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                    <div style="font-weight: 800; font-size: 0.95rem; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                        <span>Daftar Ruang Ujian & Format Meja</span>
                        <span style="font-size: 0.75rem; background: #e0e7ff; color: #4338ca; padding: 2px 8px; border-radius: 100px; font-weight: 800;">
                            <?php echo count($ruangList); ?> Ruang
                        </span>
                    </div>
                </div>

                <div class="z-table-wrap">
                    <table class="z-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Kode & Nama Ruang</th>
                                <th>Pola Konfigurasi Meja</th>
                                <th>Kapasitas & Keterisian</th>
                                <th>Lokasi & Pengawas Per-Sesi</th>
                                <th style="width: 250px; text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ruangList)): ?>
                            <tr>
                                <td colspan="6" style="padding: 3rem 1.5rem; text-align: center; color: #94a3b8;">
                                    <i data-lucide="door-closed" style="width: 44px; height: 44px; margin-bottom: 10px; color: #cbd5e1;"></i>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: #475569;">Belum Ada Ruang Ujian</div>
                                    <div style="font-size: 0.78rem;">Klik tombol "Tambah Ruang Ujian" untuk mulai mengatur ruangan dan meja ujian.</div>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach($ruangList as $r): ?>
                                <tr>
                                    <td style="color: #94a3b8; font-weight: 600;"><?php echo $no++; ?></td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span style="background: #1e293b; color: white; font-weight: 800; font-size: 0.75rem; padding: 3px 8px; border-radius: 6px; font-family: monospace;">
                                                <?php echo htmlspecialchars($r['kode_ruang']); ?>
                                            </span>
                                            <div>
                                                <div style="font-weight: 800; color: #0f172a; font-size: 0.9rem;"><?php echo htmlspecialchars($r['nama_ruang']); ?></div>
                                                <div style="font-size: 0.72rem; color: #64748b;"><?php echo htmlspecialchars($r['keterangan'] ?: 'Ruang Ujian Standar'); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display: inline-flex; align-items: center; gap: 6px; background: #eff6ff; border: 1.5px solid #bfdbfe; color: #1d4ed8; padding: 4px 10px; border-radius: 8px; font-weight: 800; font-size: 0.85rem; font-family: monospace;">
                                            <i data-lucide="hash" style="width: 14px; height: 14px;"></i>
                                            <span><?php echo htmlspecialchars($r['kode_pola'] ?: "{$r['tipe_meja']} x {$r['baris_samping']} x {$r['baris_belakang']}"); ?></span>
                                        </div>
                                        <div style="font-size: 0.72rem; color: #64748b; margin-top: 3px;">
                                            <?php echo ((int)$r['tipe_meja'] === 2) ? '2 Meja Gandeng' : '1 Meja Mandiri'; ?> • <?php echo $r['baris_samping']; ?> Samping × <?php echo $r['baris_belakang']; ?> Belakang
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div style="font-weight: 800; font-size: 0.88rem; color: #0f172a;">
                                                <?php echo $r['terisi']; ?> / <?php echo $r['kapasitas']; ?> Kursi
                                            </div>
                                            <span style="font-size: 0.7rem; font-weight: 800; padding: 1px 6px; border-radius: 4px; <?php echo ($r['terisi'] >= $r['kapasitas']) ? 'background:#dcfce7;color:#15803d;' : 'background:#fef3c7;color:#b45309;'; ?>">
                                                <?php echo $r['persen']; ?>%
                                            </span>
                                        </div>
                                        <div style="width: 100%; max-width: 140px; height: 6px; background: #e2e8f0; border-radius: 100px; overflow: hidden; margin-top: 5px;">
                                            <div style="width: <?php echo $r['persen']; ?>%; height: 100%; background: <?php echo ($r['terisi'] >= $r['kapasitas']) ? '#22c55e' : '#3b82f6'; ?>;"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.74rem; color: #64748b; margin-bottom: 5px;">
                                            <i data-lucide="map-pin" style="width: 12px; height: 12px; display: inline-block; vertical-align: -1px;"></i>
                                            <strong><?php echo htmlspecialchars($r['lokasi'] ?: 'Madrasah'); ?></strong>
                                        </div>
                                        <?php if (!empty($r['pengawas_sesi'])): ?>
                                            <div style="display: flex; flex-direction: column; gap: 3px;">
                                                <?php foreach ($r['pengawas_sesi'] as $sesiLbl => $gNama): ?>
                                                <div style="font-size: 0.72rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 2px 7px; display: inline-flex; align-items: center; gap: 5px; width: fit-content;">
                                                    <span style="font-weight: 800; color: #2563eb;"><?php echo htmlspecialchars($sesiLbl); ?>:</span>
                                                    <span style="color: #1e293b; font-weight: 600;"><?php echo htmlspecialchars($gNama); ?></span>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <div style="font-size: 0.72rem; color: #94a3b8; font-style: italic;">
                                                Belum diatur pengawas sesi
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <button type="button" class="e-btn" style="background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; font-size: 0.75rem; font-weight: 800; padding: 6px 10px; border-radius: 8px;" onclick='bukaModalPengawas(<?php echo htmlspecialchars(json_encode($r), ENT_QUOTES, "UTF-8"); ?>)' title="Atur Pengawas Per-Sesi untuk Topik Ini">
                                            <i data-lucide="user-check" style="width: 13px; height: 13px;"></i> Pengawas
                                        </button>
                                        <a href="<?php echo base_url('cbt/denah?ruang_id=' . $r['id'] . '&topik_id=' . $selectedTopikId); ?>" class="e-btn" style="background: #2563eb; color: white; border: none; font-size: 0.75rem; font-weight: 800; padding: 6px 12px; border-radius: 8px; display: inline-flex; align-items: center; gap: 4px; margin-left: 4px;" title="Atur Denah Tempat Duduk Siswa">
                                            <i data-lucide="grid" style="width: 13px; height: 13px;"></i> Denah
                                        </a>
                                        <button type="button" class="e-btn" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; font-size: 0.75rem; font-weight: 700; padding: 6px 10px; border-radius: 8px; margin-left: 4px;" onclick='editRuang(<?php echo htmlspecialchars(json_encode($r), ENT_QUOTES, "UTF-8"); ?>)' title="Edit Ruangan">
                                            <i data-lucide="edit-3" style="width: 13px; height: 13px;"></i>
                                        </button>
                                        <form method="POST" action="<?php echo base_url('cbt/ruang/delete'); ?>" style="display: inline-block; margin-left: 4px;" onsubmit="return confirm('Yakin ingin menghapus ruang ini beserta seluruh denah tempat duduknya?')">
                                            <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
                                            <button type="submit" class="e-btn" style="background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; font-size: 0.75rem; padding: 6px 10px; border-radius: 8px;" title="Hapus Ruangan">
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

<!-- Modal Atur Pengawas Per-Sesi Ruang -->
<div id="modalPengawasRuang" class="cbt-modal-backdrop" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 20px; max-width: 540px; width: 100%; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); border: 1px solid #cbd5e1; overflow: hidden; animation: slideUp 0.25s ease-out;">
        <div style="padding: 1.25rem 1.5rem; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-weight: 800; font-size: 1.05rem; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="user-check" style="color: #38bdf8;"></i>
                <span>Atur Pengawas Per-Sesi</span>
            </div>
            <button type="button" onclick="tutupModalPengawas()" style="background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 1.25rem;">✕</button>
        </div>

        <form method="POST" action="<?php echo base_url('cbt/ruang/pengawas/save'); ?>" style="padding: 1.5rem;">
            <input type="hidden" name="topik_id" value="<?php echo $selectedTopikId; ?>">
            <input type="hidden" name="ruang_id" id="pengawasRuangId" value="0">

            <div style="background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 12px; padding: 12px 14px; margin-bottom: 1.25rem;">
                <div style="font-size: 0.72rem; font-weight: 800; color: #2563eb; text-transform: uppercase;">Ruang Ujian & Topik</div>
                <div id="modalPengawasRuangNama" style="font-size: 1.05rem; font-weight: 900; color: #0f172a;">-</div>
                <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">
                    Guru pengawas yang dipilih akan otomatis menerima penugasan di Dashboard Guru masing-masing.
                </div>
            </div>

            <div style="font-size: 0.8rem; font-weight: 800; color: #334155; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
                Alokasi Guru Pengawas Tiap Sesi Ujian:
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px; max-height: 340px; overflow-y: auto; padding-right: 4px; margin-bottom: 1.5rem;">
                <?php
                // Filter non-istirahat slots
                $activeSlots = [];
                if (!empty($slotList)) {
                    foreach ($slotList as $s) {
                        if (isset($s['is_istirahat']) && (int)$s['is_istirahat'] === 1) continue;
                        $activeSlots[] = [
                            'label' => $s['label_sesi'] ?: ('Sesi ' . $s['urutan']),
                            'jam' => $s['jam_mulai'] . ' - ' . $s['jam_selesai']
                        ];
                    }
                }
                if (empty($activeSlots)) {
                    $activeSlots = [
                        ['label' => 'Sesi 1', 'jam' => '08:00 - 09:00'],
                        ['label' => 'Sesi 2', 'jam' => '09:00 - 10:00'],
                        ['label' => 'Sesi 3', 'jam' => '10:10 - 11:10'],
                    ];
                }
                foreach ($activeSlots as $sl):
                    $fieldKey = preg_replace('/[^a-zA-Z0-9_]/', '_', $sl['label']);
                ?>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span style="font-weight: 800; color: #0f172a; font-size: 0.82rem;"><?php echo htmlspecialchars($sl['label']); ?></span>
                        <span style="font-size: 0.72rem; color: #64748b; font-family: monospace; background: #e2e8f0; padding: 2px 6px; border-radius: 4px;">
                            <?php echo htmlspecialchars($sl['jam']); ?>
                        </span>
                    </div>
                    <input type="hidden" name="pengawas[<?php echo htmlspecialchars($sl['label']); ?>][jam_slot]" value="<?php echo htmlspecialchars($sl['jam']); ?>">
                    <select name="pengawas[<?php echo htmlspecialchars($sl['label']); ?>][guru_nama]" id="select_pengawas_<?php echo $fieldKey; ?>" class="input-sm select-guru-sesi" style="width: 100%; border-radius: 8px;">
                        <option value="">-- Tidak Ada Pengawas / Kosongkan --</option>
                        <?php foreach ($guruList as $g): ?>
                        <option value="<?php echo htmlspecialchars($g['nama']); ?>">
                            <?php echo htmlspecialchars($g['nama']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endforeach; ?>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" onclick="tutupModalPengawas()" class="e-btn e-btn-secondary" style="padding: 8px 16px;">Batal</button>
                <button type="submit" class="e-btn e-btn-primary" style="background: #2563eb; padding: 8px 20px;">
                    <i data-lucide="save"></i> Simpan Pengawas Sesi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Form Tambah / Edit Ruang Ujian -->
<div id="modalRuang" class="cbt-modal-backdrop" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 20px; max-width: 520px; width: 100%; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); border: 1px solid #cbd5e1; overflow: hidden; animation: slideUp 0.25s ease-out;">
        <div style="padding: 1.25rem 1.5rem; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-weight: 800; font-size: 1.05rem; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="door-open" style="color: #38bdf8;"></i>
                <span id="modalRuangTitle">Tambah Ruang Ujian Baru</span>
            </div>
            <button type="button" onclick="tutupModalRuang()" style="background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 1.25rem;">✕</button>
        </div>

        <form method="POST" action="<?php echo base_url('cbt/ruang/save'); ?>" style="padding: 1.5rem;">
            <input type="hidden" name="id" id="inpRuangId" value="0">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                <div>
                    <label class="label-sm">Kode Ruang <span style="color: red;">*</span></label>
                    <input type="text" name="kode_ruang" id="inpKodeRuang" class="input-sm" placeholder="Contoh: R-01" required style="font-family: monospace; font-weight: 800;">
                </div>
                <div>
                    <label class="label-sm">Nama Ruang <span style="color: red;">*</span></label>
                    <input type="text" name="nama_ruang" id="inpNamaRuang" class="input-sm" placeholder="Contoh: Ruang 01" required>
                </div>
            </div>

            <!-- Konfigurasi Format Meja -->
            <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 14px; margin-bottom: 14px;">
                <div style="font-size: 0.8rem; font-weight: 800; color: #0f172a; margin-bottom: 10px; display: flex; align-items: center; gap: 6px;">
                    <i data-lucide="layout-grid" style="width: 15px; height: 15px; color: #2563eb;"></i> Format Pola Meja Ujian
                </div>

                <div style="margin-bottom: 10px;">
                    <label class="label-sm">Model Meja Siswa</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <label style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; border: 1.5px solid #cbd5e1; border-radius: 10px; cursor: pointer; background: white;" id="lblTipe1">
                            <input type="radio" name="tipe_meja" value="1" id="radTipe1" onchange="hitungKapasitas()">
                            <span style="font-size: 0.8rem; font-weight: 700;">1 Meja Mandiri</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; border: 1.5px solid #2563eb; border-radius: 10px; cursor: pointer; background: #eff6ff;" id="lblTipe2">
                            <input type="radio" name="tipe_meja" value="2" id="radTipe2" checked onchange="hitungKapasitas()">
                            <span style="font-size: 0.8rem; font-weight: 800; color: #1d4ed8;">2 Meja Gandeng</span>
                        </label>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label class="label-sm">Baris Samping (Kolom)</label>
                        <input type="number" name="baris_samping" id="inpBarisSamping" class="input-sm" value="3" min="1" max="10" required oninput="hitungKapasitas()">
                    </div>
                    <div>
                        <label class="label-sm">Baris Belakang (Baris)</label>
                        <input type="number" name="baris_belakang" id="inpBarisBelakang" class="input-sm" value="5" min="1" max="15" required oninput="hitungKapasitas()">
                    </div>
                </div>

                <!-- Kalkulator Preview Otomatis -->
                <div style="margin-top: 12px; background: #ffffff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 10px 14px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-size: 0.68rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Kode Pola Meja</div>
                        <div id="lblPreviewPola" style="font-size: 1.15rem; font-weight: 900; color: #2563eb; font-family: monospace;">2 x 3 x 5</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.68rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Kapasitas</div>
                        <div id="lblPreviewKapasitas" style="font-size: 1.15rem; font-weight: 900; color: #15803d;">30 Siswa</div>
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 12px;">
                <label class="label-sm">Lokasi Gedung / Lantai</label>
                <input type="text" name="lokasi" id="inpLokasi" class="input-sm" placeholder="Contoh: Gedung A Lt. 1">
            </div>

            <div style="margin-bottom: 16px;">
                <label class="label-sm">Catatan / Keterangan</label>
                <input type="text" name="keterangan" id="inpKeterangan" class="input-sm" placeholder="Catatan fasilitas ruang...">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" onclick="tutupModalRuang()" class="e-btn e-btn-secondary" style="padding: 8px 16px;">Batal</button>
                <button type="submit" class="e-btn e-btn-primary" style="background: #2563eb; padding: 8px 20px;">
                    <i data-lucide="save"></i> Simpan Ruang Ujian
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function hitungKapasitas() {
    const tipe = document.getElementById('radTipe2').checked ? 2 : 1;
    const samping = parseInt(document.getElementById('inpBarisSamping').value) || 1;
    const belakang = parseInt(document.getElementById('inpBarisBelakang').value) || 1;

    const total = tipe * samping * belakang;
    const pola = `${tipe} x ${samping} x ${belakang}`;

    document.getElementById('lblPreviewPola').textContent = pola;
    document.getElementById('lblPreviewKapasitas').textContent = `${total} Siswa`;

    if (tipe === 2) {
        document.getElementById('lblTipe2').style.borderColor = '#2563eb';
        document.getElementById('lblTipe2').style.background = '#eff6ff';
        document.getElementById('lblTipe1').style.borderColor = '#cbd5e1';
        document.getElementById('lblTipe1').style.background = '#ffffff';
    } else {
        document.getElementById('lblTipe1').style.borderColor = '#2563eb';
        document.getElementById('lblTipe1').style.background = '#eff6ff';
        document.getElementById('lblTipe2').style.borderColor = '#cbd5e1';
        document.getElementById('lblTipe2').style.background = '#ffffff';
    }
}

function bukaModalRuang() {
    document.getElementById('inpRuangId').value = '0';
    document.getElementById('modalRuangTitle').textContent = 'Tambah Ruang Ujian Baru';
    document.getElementById('inpKodeRuang').value = '';
    document.getElementById('inpNamaRuang').value = '';
    document.getElementById('radTipe2').checked = true;
    document.getElementById('inpBarisSamping').value = '3';
    document.getElementById('inpBarisBelakang').value = '5';
    document.getElementById('inpLokasi').value = '';
    document.getElementById('inpKeterangan').value = '';
    hitungKapasitas();

    document.getElementById('modalRuang').style.display = 'flex';
    if (window.lucide) lucide.createIcons();
}

function editRuang(r) {
    document.getElementById('inpRuangId').value = r.id;
    document.getElementById('modalRuangTitle').textContent = 'Edit Ruang Ujian: ' + r.nama_ruang;
    document.getElementById('inpKodeRuang').value = r.kode_ruang;
    document.getElementById('inpNamaRuang').value = r.nama_ruang;

    if (parseInt(r.tipe_meja) === 2) {
        document.getElementById('radTipe2').checked = true;
    } else {
        document.getElementById('radTipe1').checked = true;
    }

    document.getElementById('inpBarisSamping').value = r.baris_samping || 3;
    document.getElementById('inpBarisBelakang').value = r.baris_belakang || 5;
    document.getElementById('inpLokasi').value = r.lokasi || '';
    document.getElementById('inpKeterangan').value = r.keterangan || '';
    hitungKapasitas();

    document.getElementById('modalRuang').style.display = 'flex';
    if (window.lucide) lucide.createIcons();
}

function tutupModalRuang() {
    document.getElementById('modalRuang').style.display = 'none';
}

function bukaModalPengawas(r) {
    document.getElementById('pengawasRuangId').value = r.id;
    document.getElementById('modalPengawasRuangNama').textContent = r.nama_ruang + ' (' + r.kode_ruang + ')';

    // Reset all selects first
    const selects = document.querySelectorAll('.select-guru-sesi');
    selects.forEach(sel => {
        sel.value = '';
    });

    // Populate existing pengawas per sesi
    if (r.pengawas_sesi && typeof r.pengawas_sesi === 'object') {
        for (const [sesi, guru] of Object.entries(r.pengawas_sesi)) {
            const cleanKey = sesi.replace(/[^a-zA-Z0-9_]/g, '_');
            const el = document.getElementById('select_pengawas_' + cleanKey);
            if (el) {
                el.value = guru;
            }
        }
    }

    document.getElementById('modalPengawasRuang').style.display = 'flex';
    if (window.lucide) lucide.createIcons();
}

function tutupModalPengawas() {
    document.getElementById('modalPengawasRuang').style.display = 'none';
}
</script>
