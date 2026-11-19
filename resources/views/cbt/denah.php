<?php
$pageTitle = 'Denah Tempat Duduk Siswa CBT';
$active_menu = 'denah';
?>
<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/header.php'; ?>

        <div class="z-scroll" style="padding: 0 0 3rem 0;">

            <?php if (isset($_GET['assigned'])): ?>
            <div style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 14px 20px; border-radius: 14px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px; font-weight: 700;">
                <i data-lucide="check-circle" style="width: 22px; height: 22px; color: #22c55e;"></i>
                <div>Berhasil menata <?php echo (int)$_GET['assigned']; ?> siswa ke dalam kursi ruangan ini!</div>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['reset'])): ?>
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 14px 20px; border-radius: 14px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px; font-weight: 700;">
                <i data-lucide="info" style="width: 22px; height: 22px; color: #3b82f6;"></i>
                <div>Seluruh kursi di ruangan ini telah dikosongkan. Siswa siap ditata ulang.</div>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['err'])): ?>
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 20px; border-radius: 14px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px; font-weight: 700;">
                <i data-lucide="alert-circle" style="width: 22px; height: 22px; color: #ef4444;"></i>
                <div><?php echo htmlspecialchars($_GET['err']); ?></div>
            </div>
            <?php endif; ?>

            <!-- Elite Header -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-badge">LAYOUT & SEATING ARRANGEMENT</div>
                        <h1><i data-lucide="grid"></i> Denah Tempat Duduk Siswa</h1>
                        <p>Visualisasi tata letak meja ujian dengan penempatan acak silang tingkatan (anti-contek), cetak denah A4 pintu ruang, dan cetak label nomor meja.</p>
                    </div>
                    <div class="header-actions-premium" style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <?php if ($currentRuang): ?>
                        <a href="<?php echo base_url('cbt/denah/cetak?ruang_id=' . $currentRuang['id']); ?>" target="_blank" class="e-btn e-btn-glass">
                            <i data-lucide="printer"></i> Cetak Denah A4
                        </a>
                        <a href="<?php echo base_url('cbt/denah/cetak-label?ruang_id=' . $currentRuang['id']); ?>" target="_blank" class="e-btn e-btn-glass">
                            <i data-lucide="tag"></i> Cetak Label Meja
                        </a>
                        <?php endif; ?>
                        <a href="<?php echo base_url('cbt/ruang'); ?>" class="e-btn e-btn-blue-light">
                            <i data-lucide="door-open"></i> Kelola Ruangan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Selector Ruang & Toolbar Aksi -->
            <div class="card" style="padding: 1.25rem 1.5rem; border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; background: #ffffff;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <!-- Dropdown Pilih Ruang -->
                    <div style="display: flex; align-items: center; gap: 10px; min-width: 280px;">
                        <label class="label-sm" style="margin-bottom: 0; white-space: nowrap; font-weight: 800;">Pilih Ruangan:</label>
                        <select onchange="location.href='<?php echo base_url('cbt/denah?ruang_id='); ?>' + this.value" class="input-sm" style="font-weight: 800; color: #0f172a; min-width: 220px;">
                            <?php foreach ($ruangList as $rg): ?>
                                <option value="<?php echo $rg['id']; ?>" <?php echo ((int)$rg['id'] === $selectedRuangId) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($rg['kode_ruang'] . ' - ' . $rg['nama_ruang'] . ' (' . ($rg['kode_pola'] ?: 'Meja') . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Tombol Action Penataan Siswa -->
                    <?php if ($currentRuang): ?>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <!-- Tombol Auto Generate Denah -->
                        <div style="position: relative; display: inline-block;">
                            <button type="button" class="e-btn e-btn-primary" style="background: #2563eb;" onclick="toggleDropdownGenerate()">
                                <i data-lucide="shuffle"></i> Tata Siswa Otomatis <i data-lucide="chevron-down" style="width: 14px; height: 14px;"></i>
                            </button>
                            <div id="dropdownGenerate" style="display: none; position: absolute; right: 0; top: 110%; background: white; border: 1px solid #cbd5e1; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); z-index: 100; width: 280px; padding: 6px 0;">
                                <form method="POST" action="<?php echo base_url('cbt/denah/generate'); ?>" style="margin: 0;">
                                    <input type="hidden" name="ruang_id" value="<?php echo $currentRuang['id']; ?>">
                                    <input type="hidden" name="mode" value="smart_cross">
                                    <button type="submit" style="width: 100%; text-align: left; padding: 10px 16px; background: none; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 0.82rem; font-weight: 800; color: #1e293b;">
                                        <span style="background: #eff6ff; color: #2563eb; width: 26px; height: 26px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">🔀</span>
                                        <div>
                                            <div>Acak Silang Tingkatan (Rekomendasi)</div>
                                            <div style="font-size: 0.7rem; font-weight: 500; color: #64748b;">Tingkat X, XI, XII tidak bersebelahan</div>
                                        </div>
                                    </button>
                                </form>
                                <div style="height: 1px; background: #f1f5f9; margin: 4px 0;"></div>
                                <form method="POST" action="<?php echo base_url('cbt/denah/generate'); ?>" style="margin: 0;">
                                    <input type="hidden" name="ruang_id" value="<?php echo $currentRuang['id']; ?>">
                                    <input type="hidden" name="mode" value="full_random">
                                    <button type="submit" style="width: 100%; text-align: left; padding: 8px 16px; background: none; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 0.82rem; font-weight: 700; color: #334155;">
                                        <span style="background: #f8fafc; color: #475569; width: 26px; height: 26px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">🎲</span>
                                        <div>Full Random (Acak Bebas)</div>
                                    </button>
                                </form>
                                <form method="POST" action="<?php echo base_url('cbt/denah/generate'); ?>" style="margin: 0;">
                                    <input type="hidden" name="ruang_id" value="<?php echo $currentRuang['id']; ?>">
                                    <input type="hidden" name="mode" value="sequential">
                                    <button type="submit" style="width: 100%; text-align: left; padding: 8px 16px; background: none; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 0.82rem; font-weight: 700; color: #334155;">
                                        <span style="background: #f8fafc; color: #475569; width: 26px; height: 26px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">📑</span>
                                        <div>Urut Rombel & NIS</div>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Tombol Kosongkan Kursi -->
                        <form method="POST" action="<?php echo base_url('cbt/denah/reset'); ?>" style="margin: 0;" onsubmit="return confirm('Kosongkan semua tempat duduk di ruangan ini?')">
                            <input type="hidden" name="ruang_id" value="<?php echo $currentRuang['id']; ?>">
                            <button type="submit" class="e-btn e-btn-secondary" style="border: 1px solid #cbd5e1;">
                                <i data-lucide="rotate-ccw"></i> Kosongkan Kursi
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Info Badges Ruang Terpilih -->
                <?php if ($currentRuang): ?>
                <div style="margin-top: 14px; padding-top: 12px; border-top: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                    <div style="display: flex; align-items: center; gap: 12px; font-size: 0.8rem; font-weight: 700;">
                        <span>Pola Meja: <b style="font-family: monospace; color: #2563eb; font-size: 0.95rem;"><?php echo htmlspecialchars($currentRuang['kode_pola']); ?></b></span>
                        <span>&bull;</span>
                        <span>Kapasitas: <b><?php echo $currentRuang['kapasitas']; ?> Kursi</b></span>
                        <span>&bull;</span>
                        <span>Pengawas: <b><?php echo htmlspecialchars($currentRuang['pengawas'] ?: 'Pengawas CBT'); ?></b></span>
                    </div>

                    <div style="display: flex; gap: 8px; font-size: 0.72rem; font-weight: 800;">
                        <span style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 3px 8px; border-radius: 6px;">
                            🔵 Kelas X: <?php echo $tingkatStats[10]; ?>
                        </span>
                        <span style="background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; padding: 3px 8px; border-radius: 6px;">
                            🟢 Kelas XI: <?php echo $tingkatStats[11]; ?>
                        </span>
                        <span style="background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff; padding: 3px 8px; border-radius: 6px;">
                            🟣 Kelas XII: <?php echo $tingkatStats[12]; ?>
                        </span>
                        <span style="background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; padding: 3px 8px; border-radius: 6px;">
                            ⚪ Kosong: <?php echo $tingkatStats['kosong']; ?>
                        </span>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <?php if (!$currentRuang): ?>
                <div style="background: white; border-radius: 16px; padding: 50px 20px; text-align: center; border: 1px dashed #cbd5e1;">
                    <i data-lucide="door-closed" style="width: 48px; height: 48px; color: #94a3b8; margin-bottom: 12px;"></i>
                    <h3 style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin-bottom: 6px;">Belum Ada Ruang Ujian</h3>
                    <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 16px;">Silakan buat ruang ujian terlebih dahulu di menu Kelola Ruang.</p>
                    <a href="<?php echo base_url('cbt/ruang'); ?>" class="e-btn e-btn-primary" style="background: #2563eb;">Kelola Ruang Ujian</a>
                </div>
            <?php else: ?>

            <!-- VISUAL DENAH TEMPAT DUDUK -->
            <div class="card" style="border-radius: 20px; border: 1.5px solid #cbd5e1; background: #ffffff; padding: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.04);">

                <!-- Papan Tulis & Posisi Pengawas Depan -->
                <div style="max-width: 600px; margin: 0 auto 2.5rem; text-align: center;">
                    <div style="background: linear-gradient(135deg, #064e3b 0%, #047857 100%); color: white; border: 3px solid #022c22; border-radius: 10px; padding: 12px; font-weight: 900; font-size: 0.95rem; letter-spacing: 2px; box-shadow: 0 4px 15px rgba(4,120,87,0.3); display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <span>📋 PAPAN TULIS & MEJA PENGAWAS</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 8px; font-size: 0.72rem; font-weight: 800; color: #64748b;">
                        <span>🚪 PINTU MASUK</span>
                        <span>BAGIAN DEPAN RUANGAN</span>
                        <span>JENDELA LUAR</span>
                    </div>
                </div>

                <!-- Grid Denah Meja -->
                <?php
                $tipeMeja = (int)$currentRuang['tipe_meja'];
                $samping = (int)$currentRuang['baris_samping'];
                $belakang = (int)$currentRuang['baris_belakang'];

                // Kelompokkan meja per baris belakang
                $mejaByBaris = [];
                foreach ($mejaList as $m) {
                    $b = (int)$m['posisi_baris'];
                    if (!isset($mejaByBaris[$b])) {
                        $mejaByBaris[$b] = [];
                    }
                    $mejaByBaris[$b][] = $m;
                }
                ?>

                <div style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 1100px; margin: 0 auto;">
                    <?php for ($b = 1; $b <= $belakang; $b++): ?>
                        <div style="display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap;">
                            <?php
                            // Ambil meja untuk baris ini
                            $mRow = $mejaByBaris[$b] ?? [];
                            $mejaByKolom = [];
                            foreach ($mRow as $mr) {
                                $k = (int)$mr['posisi_kolom'];
                                if (!isset($mejaByKolom[$k])) {
                                    $mejaByKolom[$k] = [];
                                }
                                $mejaByKolom[$k][] = $mr;
                            }

                            for ($k = 1; $k <= $samping; $k++):
                                $kMejas = $mejaByKolom[$k] ?? [];
                            ?>
                                <!-- Blok Meja (Gandeng atau Tunggal) -->
                                <div style="display: flex; gap: 4px; background: #f8fafc; border: 1.5px solid #e2e8f0; padding: 6px; border-radius: 14px; box-shadow: 0 2px 6px rgba(0,0,0,0.03);">
                                    <?php foreach ($kMejas as $seat): ?>
                                        <?php
                                        $hasSiswa = !empty($seat['siswa_id']);
                                        $t = (int)($seat['tingkat'] ?? 0);
                                        $badgeBg = '#eff6ff'; $badgeColor = '#1d4ed8'; $badgeBorder = '#bfdbfe';
                                        if ($t === 11) { $badgeBg = '#f0fdf4'; $badgeColor = '#15803d'; $badgeBorder = '#bbf7d0'; }
                                        elseif ($t === 12) { $badgeBg = '#faf5ff'; $badgeColor = '#7e22ce'; $badgeBorder = '#e9d5ff'; }
                                        ?>
                                        <!-- Kursi Item -->
                                        <div style="width: 135px; min-height: 110px; background: <?php echo $hasSiswa ? '#ffffff' : '#f8fafc'; ?>; border: 1.5px <?php echo $hasSiswa ? 'solid #cbd5e1' : 'dashed #cbd5e1'; ?>; border-radius: 10px; padding: 8px; display: flex; flex-direction: column; justify-content: space-between; position: relative;">
                                            <!-- Header Nomor Meja -->
                                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                                <span style="font-size: 0.7rem; font-weight: 900; background: #1e293b; color: white; padding: 1px 6px; border-radius: 4px; font-family: monospace;">
                                                    #<?php echo str_pad($seat['nomor_meja'], 2, '0', STR_PAD_LEFT); ?>
                                                </span>
                                                <?php if ($tipeMeja === 2): ?>
                                                    <span style="font-size: 0.65rem; font-weight: 800; color: #94a3b8;">
                                                        <?php echo ($seat['posisi_sisi'] === 'L') ? 'Kiri' : 'Kanan'; ?>
                                                    </span>
                                                <?php endif; ?>
                                                <?php if ($hasSiswa): ?>
                                                    <form method="POST" action="<?php echo base_url('cbt/denah/remove-seat'); ?>" style="margin: 0;" onsubmit="return confirm('Lepas siswa ini dari kursi?')">
                                                        <input type="hidden" name="ruang_id" value="<?php echo $currentRuang['id']; ?>">
                                                        <input type="hidden" name="slot_id" value="<?php echo $seat['id']; ?>">
                                                        <button type="submit" style="background: none; border: none; color: #94a3b8; cursor: pointer; padding: 0;" title="Lepas Siswa">✕</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Body Siswa -->
                                            <?php if ($hasSiswa): ?>
                                                <div style="margin: 6px 0;">
                                                    <div style="font-weight: 800; font-size: 0.8rem; color: #0f172a; line-height: 1.25; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?php echo htmlspecialchars($seat['nama_siswa']); ?>">
                                                        <?php echo htmlspecialchars($seat['nama_siswa']); ?>
                                                    </div>
                                                    <div style="font-size: 0.68rem; font-family: monospace; color: #64748b; margin-top: 2px;">
                                                        NIS: <?php echo htmlspecialchars($seat['nis'] ?: '-'); ?>
                                                    </div>
                                                </div>

                                                <!-- Badge Tingkat & Rombel -->
                                                <div>
                                                    <span style="display: block; text-align: center; font-size: 0.65rem; font-weight: 800; padding: 2px 4px; border-radius: 4px; background: <?php echo $badgeBg; ?>; color: <?php echo $badgeColor; ?>; border: 1px solid <?php echo $badgeBorder; ?>;">
                                                        <?php echo htmlspecialchars($seat['kelas_nama'] ?: 'Kelas X'); ?>
                                                    </span>
                                                </div>
                                            <?php else: ?>
                                                <!-- Kursi Kosong -->
                                                <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer;" onclick="bukaModalPilihSiswa(<?php echo $seat['id']; ?>, <?php echo $seat['nomor_meja']; ?>)">
                                                    <div style="font-size: 1rem; color: #cbd5e1; margin-bottom: 2px;">+</div>
                                                    <span style="font-size: 0.68rem; font-weight: 700; color: #94a3b8;">Kosong</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endfor; ?>
                        </div>
                    <?php endfor; ?>
                </div>

                <!-- Bagian Belakang Ruang -->
                <div style="text-align: center; margin-top: 2.5rem; font-size: 0.72rem; font-weight: 800; color: #94a3b8; border-top: 1.5px dashed #e2e8f0; padding-top: 14px;">
                    BAGIAN BELAKANG RUANGAN
                </div>

            </div>

            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Modal Pilih Siswa Manual -->
<div id="modalPilihSiswa" class="cbt-modal-backdrop" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 20px; max-width: 480px; width: 100%; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); border: 1px solid #cbd5e1; overflow: hidden; animation: slideUp 0.25s ease-out;">
        <div style="padding: 1.25rem 1.5rem; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-weight: 800; font-size: 1rem; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="user-plus" style="color: #38bdf8;"></i>
                <span id="lblTitlePilihSiswa">Pilih Siswa untuk Kursi #01</span>
            </div>
            <button type="button" onclick="tutupModalPilihSiswa()" style="background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 1.25rem;">✕</button>
        </div>

        <form method="POST" action="<?php echo base_url('cbt/denah/assign-manual'); ?>" style="padding: 1.5rem;">
            <input type="hidden" name="ruang_id" value="<?php echo $currentRuang['id'] ?? 0; ?>">
            <input type="hidden" name="slot_id" id="inpAssignSlotId" value="0">

            <div style="margin-bottom: 14px;">
                <label class="label-sm">Pilih Siswa Reguler Siakad (Belum Duduk):</label>
                <select name="siswa_id" id="inpSelectSiswa" class="input-sm" required style="width: 100%; font-size: 0.82rem; font-weight: 700;">
                    <option value="">-- Pilih Siswa --</option>
                    <?php foreach ($unassignedSiswa as $us): ?>
                        <option value="<?php echo $us['id']; ?>">
                            [<?php echo htmlspecialchars($us['nama_kelas'] ?: 'Umum'); ?>] <?php echo htmlspecialchars($us['nama'] . ' (' . ($us['nis'] ?: '-') . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" onclick="tutupModalPilihSiswa()" class="e-btn e-btn-secondary" style="padding: 8px 16px;">Batal</button>
                <button type="submit" class="e-btn e-btn-primary" style="background: #2563eb; padding: 8px 20px;">
                    Tempatkan Siswa
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleDropdownGenerate() {
    const el = document.getElementById('dropdownGenerate');
    el.style.display = (el.style.display === 'none' || el.style.display === '') ? 'block' : 'none';
}

document.addEventListener('click', function(e) {
    const dd = document.getElementById('dropdownGenerate');
    if (dd && !dd.contains(e.target) && !e.target.closest('[onclick="toggleDropdownGenerate()"]')) {
        dd.style.display = 'none';
    }
});

function bukaModalPilihSiswa(slotId, noMeja) {
    document.getElementById('inpAssignSlotId').value = slotId;
    document.getElementById('lblTitlePilihSiswa').textContent = 'Pilih Siswa untuk Kursi #' + String(noMeja).padStart(2, '0');
    document.getElementById('modalPilihSiswa').style.display = 'flex';
    if (window.lucide) lucide.createIcons();
}

function tutupModalPilihSiswa() {
    document.getElementById('modalPilihSiswa').style.display = 'none';
}
</script>
