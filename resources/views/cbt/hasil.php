<?php
$pageTitle = 'Hasil Ujian CBT';
$active_menu = 'hasil';
?>
<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/header.php'; ?>

        <div class="z-scroll" style="padding: 0 0 3rem 0;">

            <!-- Alert Buka Ujian Kembali -->
            <?php if (isset($_GET['reopened'])): ?>
            <div style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 14px 20px; border-radius: 14px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px; font-weight: 700; box-shadow: 0 4px 12px rgba(34,197,94,0.1);">
                <i data-lucide="check-circle" style="width: 22px; height: 22px; color: #22c55e; flex-shrink: 0;"></i>
                <div>
                    <div style="font-size: 0.95rem;">Sesi Ujian Siswa Berhasil Dibuka Kembali!</div>
                    <div style="font-size: 0.82rem; font-weight: 500; color: #15803d; margin-top: 2px;">Status pengerjaan siswa telah diaktifkan kembali dan pelanggaran direset. Siswa dapat langsung masuk ke ruang ujian untuk melanjutkan.</div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Elite Page Header -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-badge">EVALUASI & PENILAIAN</div>
                        <h1><i data-lucide="award"></i> Rekap Hasil Ujian Siswa</h1>
                        <p>Monitoring skor perolehan siswa, analisis ketuntasan terhadap KKM, dan rekapitulasi nilai ujian berbasis komputer.</p>
                    </div>
                    <div class="header-actions-premium" style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <a href="<?php echo base_url('cbt/hasil/cetak?scope=' . urlencode($scope ?? 'sesi_ini') . '&topik_id=' . ($selectedTopikId ?? 0) . '&mapel=' . urlencode($selectedMapel ?? '') . '&ujian_id=' . ($selectedUjianId ?? '') . '&kelas=' . urlencode($selectedKelas ?? '') . '&sort=' . urlencode($sort ?? 'nilai_desc') . '&q=' . urlencode($search ?? '')); ?>" id="btnCetakRekap" target="_blank" class="e-btn e-btn-glass" title="Buka Dokumen Rekap Nilai Resmi Kemenag A4">
                            <i data-lucide="printer"></i> Download PDF / Cetak (A4)
                        </a>
                        <a href="<?php echo base_url('cbt/analisis?topik_id=' . ($selectedTopikId ?? 0) . '&ujian_id=' . ($selectedUjianId ?? '')); ?>" id="btnAnalisisButir" class="e-btn e-btn-blue-light">
                            <i data-lucide="bar-chart-2"></i> Analisis Butir Soal
                        </a>
                    </div>
                </div>
            </div>

            <!-- Live Realtime & Scope Switcher Bar -->
            <div style="background: #ffffff; border-radius: 18px; border: 1px solid #e2e8f0; padding: 1rem 1.25rem; margin-bottom: 1.25rem; box-shadow: 0 4px 14px rgba(0,0,0,0.03); display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <!-- Scope Buttons -->
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;" id="scopeBtnGroup">
                        <span style="font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-right: 4px;">Fokus Data:</span>
                        
                        <button type="button" class="cbt-scope-pill <?php echo ($scope === 'sesi_ini') ? 'active' : ''; ?>" onclick="switchScope('sesi_ini')" id="scopePill-sesi_ini" style="padding: 7px 14px; border-radius: 99px; font-size: 0.82rem; font-weight: 800; border: 1.5px solid <?php echo ($scope === 'sesi_ini') ? '#0284c7' : '#cbd5e1'; ?>; background: <?php echo ($scope === 'sesi_ini') ? '#e0f2fe' : '#ffffff'; ?>; color: <?php echo ($scope === 'sesi_ini') ? '#0369a1' : '#475569'; ?>; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.15s ease;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #0284c7; animation: pulseDot 1.5s infinite;"></span>
                            🎯 Sesi Berjalan (Realtime)
                        </button>

                        <button type="button" class="cbt-scope-pill <?php echo ($scope === 'hari_ini') ? 'active' : ''; ?>" onclick="switchScope('hari_ini')" id="scopePill-hari_ini" style="padding: 7px 14px; border-radius: 99px; font-size: 0.82rem; font-weight: 800; border: 1.5px solid <?php echo ($scope === 'hari_ini') ? '#059669' : '#cbd5e1'; ?>; background: <?php echo ($scope === 'hari_ini') ? '#ecfdf5' : '#ffffff'; ?>; color: <?php echo ($scope === 'hari_ini') ? '#065f46' : '#475569'; ?>; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.15s ease;">
                            <i data-lucide="calendar" style="width: 14px; height: 14px;"></i>
                            📅 Seluruh Hari Ini
                        </button>

                        <button type="button" class="cbt-scope-pill <?php echo ($scope === 'semua') ? 'active' : ''; ?>" onclick="switchScope('semua')" id="scopePill-semua" style="padding: 7px 14px; border-radius: 99px; font-size: 0.82rem; font-weight: 800; border: 1.5px solid <?php echo ($scope === 'semua') ? '#6366f1' : '#cbd5e1'; ?>; background: <?php echo ($scope === 'semua') ? '#eef2ff' : '#ffffff'; ?>; color: <?php echo ($scope === 'semua') ? '#4338ca' : '#475569'; ?>; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.15s ease;">
                            <i data-lucide="layers" style="width: 14px; height: 14px;"></i>
                            🌐 Semua Riwayat
                        </button>

                        <?php if (!empty($allSlots)): ?>
                            <span style="color: #cbd5e1; font-weight: 300;">|</span>
                            <?php foreach ($allSlots as $sl): 
                                $sLabel = $sl['label_sesi'];
                                $isThisSesiActive = ($scope === $sLabel);
                            ?>
                            <button type="button" class="cbt-scope-pill <?php echo $isThisSesiActive ? 'active' : ''; ?>" onclick="switchScope('<?php echo htmlspecialchars($sLabel); ?>')" id="scopePill-<?php echo preg_replace('/[^a-zA-Z0-9]/', '', $sLabel); ?>" style="padding: 6px 12px; border-radius: 99px; font-size: 0.78rem; font-weight: 700; border: 1.5px solid <?php echo $isThisSesiActive ? '#0284c7' : '#e2e8f0'; ?>; background: <?php echo $isThisSesiActive ? '#e0f2fe' : '#ffffff'; ?>; color: <?php echo $isThisSesiActive ? '#0369a1' : '#64748b'; ?>; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                ⏱️ <?php echo htmlspecialchars($sLabel); ?>
                            </button>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Realtime Indicator & Refresh Controls -->
                    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 6px; background: #f0fdf4; border: 1.5px solid #86efac; padding: 5px 12px; border-radius: 99px; font-size: 0.75rem; font-weight: 800; color: #166534;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #22c55e; animation: pulseDot 1.2s infinite;"></span>
                            <span id="lblLiveStatus">LIVE REALTIME</span>
                            <span style="color: #15803d; font-weight: 500; margin-left: 2px;">• <span id="lblServerTime"><?php echo date('H:i:s'); ?></span></span>
                        </div>

                        <button type="button" onclick="toggleAutoRefresh()" id="btnAutoRefresh" class="e-btn" style="padding: 6px 12px; font-size: 0.75rem; font-weight: 800; border-radius: 8px; border: 1.5px solid #cbd5e1; background: #ffffff; color: #334155; display: inline-flex; align-items: center; gap: 4px; cursor: pointer;" title="Nyalakan / Jeda Pembaruan Otomatis">
                            <i data-lucide="pause-circle" id="iconAutoRefresh" style="width: 14px; height: 14px; color: #0284c7;"></i>
                            <span id="lblBtnAutoRefresh">Auto 5s: AKTIF</span>
                        </button>

                        <button type="button" onclick="applyFilterAjax(false)" class="e-btn" style="padding: 6px 12px; font-size: 0.75rem; font-weight: 800; border-radius: 8px; border: 1.5px solid #0284c7; background: #e0f2fe; color: #0369a1; display: inline-flex; align-items: center; gap: 4px; cursor: pointer;" title="Segarkan Data Sekarang">
                            <i data-lucide="refresh-cw" id="iconManualRefresh" style="width: 14px; height: 14px;"></i>
                            <span>Segarkan</span>
                        </button>
                    </div>
                </div>

                <!-- Active Sesi Description Alert Banner -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px 14px; display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: #334155;">
                        <i data-lucide="info" style="width: 16px; height: 16px; color: #0284c7; flex-shrink: 0;"></i>
                        <span id="lblSesiInfoBanner">
                            <b>Fokus Aktif:</b> <?php echo htmlspecialchars($sesiInfo ?: 'Sesi Berjalan Hari Ini'); ?>
                        </span>
                    </div>
                    <?php if ($scope === 'sesi_ini'): ?>
                    <span style="font-size: 0.72rem; color: #0284c7; background: #e0f2fe; padding: 3px 10px; border-radius: 99px; font-weight: 800;">
                        Otomatis menyesuaikan jam & mapel ujian saat ini
                    </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Filter Panel -->
            <div class="card" style="padding: 1.25rem 1.5rem; border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; background: #ffffff;">
                <form method="GET" action="<?php echo base_url('cbt/hasil'); ?>" id="formFilterHasil" onsubmit="event.preventDefault(); applyFilterAjax(false);" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                    <div style="flex: 1.6; min-width: 200px;">
                        <label class="label-sm">Topik Ujian</label>
                        <select name="topik_id" id="selTopikHasil" class="input-sm" onchange="applyFilterAjax(true)">
                            <option value="0">-- Semua Topik Ujian --</option>
                            <?php foreach($topikList as $tp): ?>
                                <option value="<?php echo $tp['id']; ?>" <?php echo ($selectedTopikId == $tp['id']) ? 'selected' : ''; ?>>
                                    [<?php echo htmlspecialchars($tp['jenis_topik'] ?: ($tp['jenis'] ?? 'Ujian')); ?>] <?php echo htmlspecialchars($tp['nama_topik']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="flex: 1.3; min-width: 170px;">
                        <label class="label-sm">Mata Pelajaran</label>
                        <select name="mapel" id="selMapelHasil" class="input-sm" onchange="applyFilterAjax(false)">
                            <option value="">-- Semua Mapel --</option>
                            <?php foreach($mapelList as $mp): ?>
                                <option value="<?php echo htmlspecialchars($mp); ?>" <?php echo ($selectedMapel === $mp) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($mp); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="flex: 1; min-width: 140px;">
                        <label class="label-sm">Filter Kelas</label>
                        <select name="kelas" id="selKelasHasil" class="input-sm" onchange="applyFilterAjax(false)">
                            <option value="">-- Semua Kelas --</option>
                            <?php if (!empty($kelasListReguler)): ?>
                                <optgroup label="🏛️ Kelas Reguler (Madrasah)">
                                    <?php foreach($kelasListReguler as $kls): ?>
                                        <option value="<?php echo htmlspecialchars($kls); ?>" <?php echo ($selectedKelas === $kls) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($kls); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endif; ?>
                            <?php if (!empty($kelasListJauh)): ?>
                                <optgroup label="📡 Kelas Jauh (Khusus CBT)">
                                    <?php foreach($kelasListJauh as $kls): ?>
                                        <option value="<?php echo htmlspecialchars($kls); ?>" <?php echo ($selectedKelas === $kls) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($kls); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endif; ?>
                            <?php if (empty($kelasListReguler) && empty($kelasListJauh)): ?>
                                <?php foreach($kelasList as $kls): ?>
                                    <option value="<?php echo htmlspecialchars($kls); ?>" <?php echo ($selectedKelas === $kls) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($kls); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div style="flex: 1.2; min-width: 170px;">
                        <label class="label-sm">Urutan Data Siswa</label>
                        <select name="sort" id="selSortHasil" class="input-sm" onchange="applyFilterAjax(false)">
                            <option value="nilai_desc" <?php echo (($sort ?? 'nilai_desc') === 'nilai_desc') ? 'selected' : ''; ?>>Nilai Tertinggi (Rank)</option>
                            <option value="nilai_asc" <?php echo (($sort ?? '') === 'nilai_asc') ? 'selected' : ''; ?>>Nilai Terendah</option>
                            <option value="nama_asc" <?php echo (($sort ?? '') === 'nama_asc') ? 'selected' : ''; ?>>Nama Siswa (A - Z)</option>
                            <option value="nama_desc" <?php echo (($sort ?? '') === 'nama_desc') ? 'selected' : ''; ?>>Nama Siswa (Z - A)</option>
                            <option value="nis_asc" <?php echo (($sort ?? '') === 'nis_asc') ? 'selected' : ''; ?>>Nomor Peserta / NIS</option>
                            <option value="waktu_desc" <?php echo (($sort ?? '') === 'waktu_desc') ? 'selected' : ''; ?>>Waktu Selesai (Terbaru)</option>
                        </select>
                    </div>

                    <div style="flex: 1.5; min-width: 180px;">
                        <label class="label-sm">Cari Nama / NIS Siswa</label>
                        <div style="display: flex; gap: 6px;">
                            <input type="text" name="q" id="inpSearchHasil" class="input-sm" placeholder="Ketik nama siswa..." value="<?php echo htmlspecialchars($search ?? ''); ?>" oninput="onSearchInput(this)">
                            <button type="submit" class="e-btn e-btn-primary" style="padding: 7px 12px; background: #4f46e5;" title="Cari Data">
                                <i data-lucide="search" style="width: 14px; height: 14px;"></i>
                            </button>
                            <a href="<?php echo base_url('cbt/hasil'); ?>" id="btnResetFilter" onclick="resetFilterAjax(event)" class="e-btn" style="padding: 7px 12px; background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; display: <?php echo (!empty($selectedTopikId) || !empty($selectedMapel) || !empty($selectedKelas) || !empty($search) || ($sort !== 'nilai_desc')) ? 'inline-flex' : 'none'; ?>; align-items: center; justify-content: center;" title="Reset Semua Filter">
                                <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Mini Summary Stats for Selected Exam -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                <div class="card" style="padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="users"></i>
                    </div>
                    <div>
                        <div id="statTotalPeserta" style="font-size: 1.5rem; font-weight: 900; color: #0f172a; line-height: 1;"><?php echo count($pesertaList); ?></div>
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-top: 4px;">Peserta Terdata</div>
                    </div>
                </div>

                <div class="card" style="padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #f0fdf4; color: #16a34a; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="trending-up"></i>
                    </div>
                    <div>
                        <div id="statRataRata" style="font-size: 1.5rem; font-weight: 900; color: #15803d; line-height: 1;"><?php echo number_format($rataRata, 1); ?></div>
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-top: 4px;">Rata-Rata Nilai</div>
                    </div>
                </div>

                <div class="card" style="padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="check-check"></i>
                    </div>
                    <div>
                        <div id="statPersenTuntas" style="font-size: 1.5rem; font-weight: 900; color: #4338ca; line-height: 1;"><?php echo $persenTuntas; ?>%</div>
                        <div id="statKkmLabel" style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-top: 4px;">Ketuntasan (KKM: <?php echo $kkmUjian; ?>)</div>
                    </div>
                </div>

                <div class="card" style="padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #fef2f2; color: #dc2626; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="alert-circle"></i>
                    </div>
                    <div>
                        <div id="statRemedial" style="font-size: 1.5rem; font-weight: 900; color: #b91c1c; line-height: 1;"><?php echo $jumlahRemedial; ?></div>
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-top: 4px;">Perlu Remedial</div>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card" style="border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden; background: #ffffff;">
                <div style="padding: 1.25rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div style="font-weight: 800; font-size: 0.95rem; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                        <span>Daftar Nilai Siswa</span>
                        <span id="badgeTotalSiswa" style="font-size: 0.75rem; background: #e0e7ff; color: #4338ca; padding: 2px 8px; border-radius: 100px; font-weight: 800;">
                            <?php echo count($pesertaList); ?> Siswa
                        </span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <button type="button" onclick="bukaModalReopenSemuaHasil()" class="e-btn" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff; border: none; padding: 6px 14px; font-size: 0.78rem; font-weight: 800; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 8px rgba(2,132,199,0.25); cursor: pointer;" title="Buka Ujian untuk Seluruh Siswa Selesai / Terkunci">
                            <i data-lucide="unlock" style="width: 14px; height: 14px;"></i> Buka Semua Ujian
                        </button>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span style="font-size: 0.78rem; font-weight: 700; color: #64748b;">Tampilkan:</span>
                            <select id="selLimitHasil" onchange="applyFilterAjax(false)" style="padding: 5px 10px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 0.8rem; font-weight: 700; color: #1e293b; background: #ffffff; cursor: pointer;">
                                <option value="30" <?php echo (($limit ?? 30) == 30) ? 'selected' : ''; ?>>30 Siswa (Default)</option>
                                <option value="50" <?php echo (($limit ?? 30) == 50) ? 'selected' : ''; ?>>50 Siswa</option>
                                <option value="100" <?php echo (($limit ?? 30) == 100) ? 'selected' : ''; ?>>100 Siswa</option>
                                <option value="all" <?php echo (($limit ?? 30) === 0 || ($limit ?? 30) === 'all') ? 'selected' : ''; ?>>Semua Siswa</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="z-table-wrap">
                    <table class="z-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>NIS & Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Paket Ujian</th>
                                <th>Jawaban Benar</th>
                                <th>Jawaban Salah</th>
                                <th>Tidak Dijawab</th>
                                <th>Nilai Akhir</th>
                                <th>Status Kelulusan</th>
                                <th style="width: 130px; text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="hasilTableBody">
                            <?php
                            $displayList = $displayPesertaList ?? (($limit > 0 && count($pesertaList) > $limit) ? array_slice($pesertaList, 0, $limit) : $pesertaList);
                            if (empty($displayList)): ?>
                            <tr>
                                <td colspan="10" style="padding: 3rem 1.5rem; text-align: center; color: #94a3b8;">
                                    <i data-lucide="folder-search" style="width: 44px; height: 44px; margin-bottom: 10px; color: #cbd5e1;"></i>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: #475569;">Tidak ada data hasil ujian yang sesuai dengan filter</div>
                                    <div style="font-size: 0.78rem; margin-bottom: 14px;">Silakan sesuaikan pilihan Topik, Mata Pelajaran, atau Kelas yang dipilih.</div>
                                    <a href="<?php echo base_url('cbt/hasil'); ?>" class="e-btn e-btn-primary" onclick="resetFilterAjax(event)" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 800; font-size: 0.8rem; padding: 7px 16px; background: #4f46e5;">
                                        <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i> Reset Semua Filter
                                    </a>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach($displayList as $p): 
                                    $isSelesai = ($p['status_pengerjaan'] === 'Selesai');
                                    $isTuntas = ($isSelesai && $p['nilai_akhir'] >= $kkmUjian);
                                    $benar = (int)($p['jumlah_benar'] ?? 0);
                                    $salah = (int)($p['jumlah_salah'] ?? 0);
                                    $tidakJawab = (int)($p['jumlah_tidak_jawab'] ?? 0);
                                    $totalSoal = (int)($p['total_soal_ujian'] ?? 0);
                                    if ($totalSoal <= 0) $totalSoal = max(20, $benar + $salah);
                                    if ($tidakJawab == 0 && ($benar + $salah) < $totalSoal) {
                                        $tidakJawab = $totalSoal - ($benar + $salah);
                                    }
                                ?>
                                <tr>
                                    <td style="color: #94a3b8; font-weight: 600;"><?php echo $no++; ?></td>
                                    <td>
                                        <div style="font-weight: 800; color: #0f172a; font-size: 0.88rem;"><?php echo htmlspecialchars($p['nama_siswa']); ?></div>
                                        <div style="font-size: 0.72rem; color: #64748b; font-family: monospace;">NIS: <?php echo htmlspecialchars($p['nis'] ?: '-'); ?></div>
                                    </td>
                                    <td>
                                        <?php $isKJ = (stripos($p['kelas_nama'] ?? '', 'jauh') !== false); ?>
                                        <span style="font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 6px; <?php echo $isKJ ? 'background: #fef3c7; color: #b45309; border: 1px solid #fde68a;' : 'background: #e0e7ff; color: #4338ca;'; ?>">
                                            <?php echo htmlspecialchars($p['kelas_nama'] ?: '-'); ?>
                                            <?php if ($isKJ): ?>
                                                <span style="font-size: 0.65rem; background: #b45309; color: #fff; padding: 1px 4px; border-radius: 3px; margin-left: 2px;">KJ</span>
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: #1e293b; font-size: 0.82rem;"><?php echo htmlspecialchars($p['nama_ujian'] ?? 'Ujian'); ?></div>
                                    </td>
                                    <td>
                                        <?php if ($isSelesai): ?>
                                        <span style="font-weight: 800; color: #15803d; background: #dcfce7; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">
                                            <?php echo $benar; ?> Benar
                                        </span>
                                        <?php else: ?>
                                        <span style="color: #94a3b8; font-size: 0.8rem;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($isSelesai): ?>
                                        <span style="font-weight: 800; color: #b91c1c; background: #fee2e2; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">
                                            <?php echo $salah; ?> Salah
                                        </span>
                                        <?php else: ?>
                                        <span style="color: #94a3b8; font-size: 0.8rem;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($isSelesai): ?>
                                        <span style="font-weight: 800; color: #64748b; background: #f1f5f9; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">
                                            <?php echo $tidakJawab; ?> Kosong
                                        </span>
                                        <?php else: ?>
                                        <span style="color: #94a3b8; font-size: 0.8rem;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($isSelesai): ?>
                                        <div style="font-size: 1.25rem; font-weight: 900; color: <?php echo $isTuntas ? '#15803d' : '#b91c1c'; ?>;">
                                            <?php echo number_format($p['nilai_akhir'], 1); ?>
                                        </div>
                                        <?php else: ?>
                                        <span style="color: #94a3b8; font-size: 0.84rem; font-weight: 700;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($isSelesai): ?>
                                        <span style="font-size: 0.75rem; font-weight: 800; padding: 3px 10px; border-radius: 6px; <?php echo $isTuntas ? 'background:#dcfce7;color:#15803d;' : 'background:#fee2e2;color:#b91c1c;'; ?>">
                                            <?php echo $isTuntas ? 'Tuntas' : 'Remedial'; ?>
                                        </span>
                                        <?php else: ?>
                                        <span style="font-size: 0.72rem; font-weight: 700; padding: 3px 8px; border-radius: 6px; background: #f1f5f9; color: #64748b;">
                                            Belum Mengerjakan
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <?php if ($isSelesai || in_array($p['status_pengerjaan'] ?? '', ['Sedang Mengerjakan', 'Mengerjakan', 'Terkunci', 'Pelanggaran'])): ?>
                                            <button type="button" class="e-btn" style="background: #0ea5e9; color: white; border: none; font-size: 0.75rem; font-weight: 800; padding: 6px 14px; border-radius: 8px; display: inline-flex; align-items: center; gap: 5px; box-shadow: 0 2px 6px rgba(14,165,233,0.25); cursor: pointer;" title="Buka Sesi Ujian Kembali" onclick="bukaModalReopen(<?php echo (int)($p['id'] ?? 0); ?>, '<?php echo htmlspecialchars(addslashes($p['nama_siswa'] ?? 'Siswa')); ?>', '<?php echo htmlspecialchars(addslashes($p['nama_ujian'] ?? 'Paket Ujian')); ?>')">
                                                <i data-lucide="rotate-ccw" style="width: 13px; height: 13px;"></i> Buka Ujian
                                            </button>
                                        <?php else: ?>
                                            <span style="color: #cbd5e1; font-size: 0.8rem; font-weight: 700;">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php
                $hasMore = count($pesertaList) > count($displayList);
                ?>
                <div id="wrapLoadMoreHasil" style="padding: 12px 1.5rem; background: #f8fafc; border-top: 1px solid #f1f5f9; display: <?php echo $hasMore ? 'flex' : 'none'; ?>; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div id="infoRowsCount" style="font-size: 0.8rem; color: #64748b; font-weight: 600;">
                        Menampilkan <b id="lblDisplayCount"><?php echo count($displayList); ?></b> dari <b id="lblTotalCount"><?php echo count($pesertaList); ?></b> siswa
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <button type="button" id="btnLoadMoreHasil" onclick="loadMoreHasil()" class="e-btn" style="padding: 6px 14px; font-size: 0.78rem; font-weight: 800; background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                            <i data-lucide="chevron-down" style="width: 14px; height: 14px;"></i> +30 Tampilkan Lebih Banyak
                        </button>
                        <button type="button" onclick="showAllHasil()" class="e-btn" style="padding: 6px 12px; font-size: 0.78rem; font-weight: 700; background: #ffffff; color: #475569; border: 1px solid #cbd5e1; border-radius: 8px; cursor: pointer;">
                            Tampilkan Semua
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Buka Ujian Kembali Modern -->
<div id="modalReopenExam" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); z-index: 9999; justify-content: center; align-items: center; padding: 18px;">
    <div style="background: #ffffff; border-radius: 20px; width: 100%; max-width: 440px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #e2e8f0; overflow: hidden; animation: popInModal 0.25s cubic-bezier(0.16, 1, 0.3, 1);">
        <form method="POST" action="<?php echo base_url('cbt/hasil/buka-ujian'); ?>" id="formReopenExam" style="margin: 0;">
            <input type="hidden" name="peserta_id" id="reopenPesertaId" value="0">
            
            <!-- Header -->
            <div style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); padding: 22px 24px; color: #ffffff; display: flex; align-items: center; gap: 14px;">
                <div style="width: 46px; height: 46px; border-radius: 12px; background: rgba(255, 255, 255, 0.2); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                    🔓
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800;">Buka Ujian Kembali</h3>
                    <p style="margin: 3px 0 0; font-size: 0.78rem; color: #bae6fd;">Izinkan siswa melanjutkan atau mengulang sesi ujian</p>
                </div>
            </div>

            <!-- Body -->
            <div style="padding: 20px 24px;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 14px; margin-bottom: 16px;">
                    <div style="font-size: 0.72rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Nama Siswa:</div>
                    <div id="reopenNamaSiswa" style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin-top: 2px;">-</div>
                    <div id="reopenPaketJudul" style="font-size: 0.78rem; color: #0284c7; font-weight: 600; margin-top: 4px;">-</div>
                </div>

                <div style="font-size: 0.8rem; font-weight: 800; color: #334155; margin-bottom: 10px;">Pilih Metode Pembukaan Ujian:</div>

                <!-- Opsi 1: Lanjutkan -->
                <label style="display: flex; gap: 12px; align-items: flex-start; padding: 12px 14px; border: 2px solid #0ea5e9; background: #f0f9ff; border-radius: 12px; margin-bottom: 10px; cursor: pointer;">
                    <input type="radio" name="mode" value="lanjutkan" checked style="margin-top: 3px; accent-color: #0ea5e9;">
                    <div>
                        <div style="font-weight: 800; font-size: 0.88rem; color: #0369a1;">Lanjutkan Pengerjaan (Direkomendasikan)</div>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px; line-height: 1.4;">Jawaban yang sudah dijawab siswa tetap tersimpan. Siswa dapat melanjutkan soal yang belum selesai.</div>
                    </div>
                </label>

                <!-- Opsi 2: Ulang dari Awal -->
                <label style="display: flex; gap: 12px; align-items: flex-start; padding: 12px 14px; border: 1.5px solid #e2e8f0; background: #ffffff; border-radius: 12px; cursor: pointer;">
                    <input type="radio" name="mode" value="ulang" style="margin-top: 3px; accent-color: #ef4444;">
                    <div>
                        <div style="font-weight: 800; font-size: 0.88rem; color: #dc2626;">Ulang dari Awal (Reset Jawaban)</div>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px; line-height: 1.4;">Seluruh jawaban sebelumnya akan dihapus dan lembar ujian dimulai dari nomor 1.</div>
                    </div>
                </label>
            </div>

            <!-- Footer -->
            <div style="padding: 0 24px 24px; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="tutupModalReopen()" style="padding: 10px 18px; border-radius: 10px; border: 1.5px solid #cbd5e1; background: #ffffff; color: #475569; font-weight: 700; font-size: 0.82rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 10px 20px; border-radius: 10px; border: none; background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: #ffffff; font-weight: 800; font-size: 0.85rem; cursor: pointer; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.35);">
                    Buka Akses Ujian
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Buka SEMUA Ujian Siswa di Hasil Ujian -->
<div id="modalReopenSemuaHasil" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); z-index: 9999; justify-content: center; align-items: center; padding: 18px;">
    <div style="background: #ffffff; border-radius: 20px; width: 100%; max-width: 480px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #e2e8f0; overflow: hidden; animation: popInModal 0.25s cubic-bezier(0.16, 1, 0.3, 1);">
        <form method="POST" action="<?php echo base_url('cbt/hasil/buka-ujian'); ?>" style="margin: 0;">
            <input type="hidden" name="buka_semua" value="1">
            <input type="hidden" name="peserta_id" value="0">
            <input type="hidden" name="tanggal" value="<?php echo ($scope === 'semua') ? 'all' : date('Y-m-d'); ?>">
            
            <div style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); padding: 22px 24px; color: #ffffff; display: flex; align-items: center; gap: 14px;">
                <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(255, 255, 255, 0.2); display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                    🔓
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800;">Buka Semua Ujian Siswa</h3>
                    <p style="margin: 3px 0 0; font-size: 0.78rem; color: #bae6fd;">Aktifkan kembali ujian bagi seluruh siswa yang selesai / terkunci</p>
                </div>
            </div>

            <div style="padding: 20px 24px;">
                <div style="background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 12px; padding: 14px; margin-bottom: 16px; display: flex; gap: 10px; align-items: flex-start;">
                    <i data-lucide="info" style="width: 20px; height: 20px; color: #2563eb; flex-shrink: 0; margin-top: 2px;"></i>
                    <div style="font-size: 0.8rem; color: #1e40af; line-height: 1.45;">
                        Tindakan ini akan mengaktifkan kembali lembar ujian bagi seluruh siswa yang telah berstatus <b>Selesai</b>, <b>Terkunci</b>, atau <b>Pelanggaran</b> pada sesi/tanggal ini agar dapat melanjutkan ujian.
                    </div>
                </div>

                <div style="font-size: 0.82rem; font-weight: 800; color: #334155; margin-bottom: 10px;">Pilih Mode Pembukaan:</div>

                <label style="display: flex; gap: 12px; align-items: flex-start; padding: 14px; border: 2px solid #0ea5e9; background: #f0f9ff; border-radius: 12px; margin-bottom: 10px; cursor: pointer;">
                    <input type="radio" name="mode" value="lanjutkan" checked style="margin-top: 3px; accent-color: #0ea5e9;">
                    <div>
                        <div style="font-weight: 800; font-size: 0.88rem; color: #0369a1;">Lanjutkan Pengerjaan (Direkomendasikan)</div>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 3px; line-height: 1.4;">Jawaban siswa sebelumnya tetap tersimpan rapi. Siswa dapat meneruskan soal yang belum selesai.</div>
                    </div>
                </label>

                <label style="display: flex; gap: 12px; align-items: flex-start; padding: 14px; border: 1.5px solid #e2e8f0; background: #ffffff; border-radius: 12px; cursor: pointer;">
                    <input type="radio" name="mode" value="ulang" style="margin-top: 3px; accent-color: #ef4444;">
                    <div>
                        <div style="font-weight: 800; font-size: 0.88rem; color: #dc2626;">Ulang dari Awal (Reset Total Jawaban)</div>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 3px; line-height: 1.4;">Seluruh jawaban sebelumnya dikosongkan dan waktu pengerjaan diulang dari awal.</div>
                    </div>
                </label>
            </div>

            <div style="padding: 0 24px 24px; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="tutupModalReopenSemuaHasil()" style="padding: 10px 18px; border-radius: 10px; border: 1.5px solid #cbd5e1; background: #ffffff; color: #475569; font-weight: 700; font-size: 0.82rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 10px 22px; border-radius: 10px; border: none; background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: #ffffff; font-weight: 800; font-size: 0.85rem; cursor: pointer; box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35);">
                    Buka Semua Ujian Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes popInModal {
    0% { transform: scale(0.92); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
@keyframes pulseDot {
    0% { transform: scale(0.95); opacity: 0.7; }
    50% { transform: scale(1.2); opacity: 1; }
    100% { transform: scale(0.95); opacity: 0.7; }
}
.cbt-scope-pill:hover {
    filter: brightness(0.97);
}
</style>

<script>
let currentScope = '<?php echo htmlspecialchars($scope ?? "sesi_ini"); ?>';
let autoRefreshActive = true;
let autoRefreshTimer = null;

function bukaModalReopen(pesertaId, namaSiswa, paketJudul) {
    document.getElementById('reopenPesertaId').value = pesertaId;
    document.getElementById('reopenNamaSiswa').textContent = namaSiswa;
    document.getElementById('reopenPaketJudul').textContent = paketJudul || 'Paket Ujian CBT';
    document.getElementById('modalReopenExam').style.display = 'flex';
}

function tutupModalReopen() {
    document.getElementById('modalReopenExam').style.display = 'none';
}

function bukaModalReopenSemuaHasil() {
    document.getElementById('modalReopenSemuaHasil').style.display = 'flex';
}

function tutupModalReopenSemuaHasil() {
    document.getElementById('modalReopenSemuaHasil').style.display = 'none';
}

function toggleAutoRefresh() {
    autoRefreshActive = !autoRefreshActive;
    const lbl = document.getElementById('lblBtnAutoRefresh');
    const icon = document.getElementById('iconAutoRefresh');
    const liveStatus = document.getElementById('lblLiveStatus');
    if (autoRefreshActive) {
        if (lbl) lbl.textContent = 'Auto 5s: AKTIF';
        if (icon) icon.setAttribute('data-lucide', 'pause-circle');
        if (liveStatus) liveStatus.textContent = 'LIVE REALTIME';
    } else {
        if (lbl) lbl.textContent = 'Auto: DIJEDA';
        if (icon) icon.setAttribute('data-lucide', 'play-circle');
        if (liveStatus) liveStatus.textContent = 'JEDA';
    }
    if (window.lucide) lucide.createIcons();
}

function switchScope(newScope) {
    currentScope = newScope;
    // Update active pill styling
    document.querySelectorAll('.cbt-scope-pill').forEach(btn => {
        btn.classList.remove('active');
        btn.style.background = '#ffffff';
        btn.style.borderColor = '#cbd5e1';
        btn.style.color = '#475569';
    });
    const key = newScope.replace(/[^a-zA-Z0-9]/g, '');
    const activePill = document.getElementById('scopePill-' + key);
    if (activePill) {
        activePill.classList.add('active');
        activePill.style.background = '#e0f2fe';
        activePill.style.borderColor = '#0284c7';
        activePill.style.color = '#0369a1';
    }
    applyFilterAjax(false);
}

// ================= AJAX FILTER & REALTIME SYSTEM =================
let currentLimitHasil = <?php echo (int)($limit ?? 30); ?>;
let searchTimer = null;
let ajaxAbortCtrl = null;

function applyFilterAjax(isTopikChange, isSilent = false) {
    if (isTopikChange) {
        const selMapel = document.getElementById('selMapelHasil');
        if (selMapel) selMapel.value = '';
    }

    const topikId = document.getElementById('selTopikHasil') ? document.getElementById('selTopikHasil').value : 0;
    const mapel = document.getElementById('selMapelHasil') ? document.getElementById('selMapelHasil').value : '';
    const kelas = document.getElementById('selKelasHasil') ? document.getElementById('selKelasHasil').value : '';
    const sort = document.getElementById('selSortHasil') ? document.getElementById('selSortHasil').value : 'nilai_desc';
    const q = document.getElementById('inpSearchHasil') ? document.getElementById('inpSearchHasil').value.trim() : '';
    const limit = document.getElementById('selLimitHasil') ? document.getElementById('selLimitHasil').value : '30';

    currentLimitHasil = (limit === 'all') ? 9999 : parseInt(limit || 30);

    const tbody = document.getElementById('hasilTableBody');
    if (tbody && !isSilent) {
        tbody.style.opacity = '0.35';
        tbody.style.transition = 'opacity 0.15s ease';
    }

    const btnReset = document.getElementById('btnResetFilter');
    if (btnReset) {
        const hasFilter = (currentScope !== 'sesi_ini' || topikId !== '0' || mapel !== '' || kelas !== '' || q !== '' || sort !== 'nilai_desc');
        btnReset.style.display = hasFilter ? 'inline-flex' : 'none';
    }

    if (ajaxAbortCtrl) {
        ajaxAbortCtrl.abort();
    }
    ajaxAbortCtrl = new AbortController();

    const params = new URLSearchParams({
        scope: currentScope,
        topik_id: topikId,
        mapel: mapel,
        kelas: kelas,
        sort: sort,
        q: q,
        limit: limit,
        ajax: '1'
    });

    // Update browser URL silently without full reload
    const cleanParams = new URLSearchParams(params);
    cleanParams.delete('ajax');
    const newUrl = '<?php echo base_url("cbt/hasil"); ?>' + '?' + cleanParams.toString();
    window.history.replaceState({}, '', newUrl);

    fetch('<?php echo base_url("cbt/hasil"); ?>?' + params.toString(), {
        signal: ajaxAbortCtrl.signal,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) return;

        // Update stats
        const elTotal = document.getElementById('statTotalPeserta');
        if (elTotal) elTotal.textContent = data.total_peserta;

        const elRata = document.getElementById('statRataRata');
        if (elRata) elRata.textContent = data.rata_rata;

        const elTuntas = document.getElementById('statPersenTuntas');
        if (elTuntas) elTuntas.textContent = data.persen_tuntas + '%';

        const elKkm = document.getElementById('statKkmLabel');
        if (elKkm) elKkm.textContent = 'Ketuntasan (KKM: ' + data.kkm + ')';

        const elRem = document.getElementById('statRemedial');
        if (elRem) elRem.textContent = data.jumlah_remedial;

        const badgeTotal = document.getElementById('badgeTotalSiswa');
        if (badgeTotal) badgeTotal.textContent = data.total_peserta + ' Siswa';

        // Update server time & info banner
        const elTime = document.getElementById('lblServerTime');
        if (elTime && data.server_time) elTime.textContent = data.server_time;

        const elBanner = document.getElementById('lblSesiInfoBanner');
        if (elBanner && data.sesi_info) {
            elBanner.innerHTML = '<b>Fokus Aktif:</b> ' + data.sesi_info;
        }

        // Update Cetak & Analisis Links
        const btnCetak = document.getElementById('btnCetakRekap');
        if (btnCetak && data.cetak_url) btnCetak.href = data.cetak_url;

        const btnAnalisis = document.getElementById('btnAnalisisButir');
        if (btnAnalisis && data.analisis_url) btnAnalisis.href = data.analisis_url;

        // If Topik changed, re-populate Mapel options
        if (isTopikChange && data.mapel_list) {
            const selMapel = document.getElementById('selMapelHasil');
            if (selMapel) {
                const prev = selMapel.value;
                selMapel.innerHTML = '<option value="">-- Semua Mapel --</option>';
                data.mapel_list.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m;
                    opt.textContent = m;
                    if (m === prev) opt.selected = true;
                    selMapel.appendChild(opt);
                });
            }
        }

        // Update Table Rows
        if (tbody) {
            tbody.innerHTML = data.table_html;
            tbody.style.opacity = '1';
        }

        // Update Load More footer
        const wrapMore = document.getElementById('wrapLoadMoreHasil');
        if (wrapMore) {
            const dispCount = data.display_count || data.total_peserta;
            const totCount = data.total_peserta;
            const elDisp = document.getElementById('lblDisplayCount');
            if (elDisp) elDisp.textContent = dispCount;
            const elTot = document.getElementById('lblTotalCount');
            if (elTot) elTot.textContent = totCount;

            if (totCount > dispCount) {
                wrapMore.style.display = 'flex';
            } else {
                wrapMore.style.display = 'none';
            }
        }

        // Re-init lucide icons
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
        }
    })
    .catch(err => {
        if (err.name === 'AbortError') return;
        if (tbody) tbody.style.opacity = '1';
    });
}

// Inisialisasi Realtime Polling setiap 5 detik
autoRefreshTimer = setInterval(() => {
    if (autoRefreshActive && !document.hidden) {
        applyFilterAjax(false, true);
    }
}, 5000);
        const elTotal = document.getElementById('statTotalPeserta');
        if (elTotal) elTotal.textContent = data.total_peserta;

        const elRata = document.getElementById('statRataRata');
        if (elRata) elRata.textContent = data.rata_rata;

        const elTuntas = document.getElementById('statPersenTuntas');
        if (elTuntas) elTuntas.textContent = data.persen_tuntas + '%';

        const elKkm = document.getElementById('statKkmLabel');
        if (elKkm) elKkm.textContent = 'Ketuntasan (KKM: ' + data.kkm + ')';

        const elRem = document.getElementById('statRemedial');
        if (elRem) elRem.textContent = data.jumlah_remedial;

        const badgeTotal = document.getElementById('badgeTotalSiswa');
        if (badgeTotal) badgeTotal.textContent = data.total_peserta + ' Siswa';

        // Update Cetak & Analisis Links
        const btnCetak = document.getElementById('btnCetakRekap');
        if (btnCetak && data.cetak_url) btnCetak.href = data.cetak_url;

        const btnAnalisis = document.getElementById('btnAnalisisButir');
        if (btnAnalisis && data.analisis_url) btnAnalisis.href = data.analisis_url;

        // If Topik changed, re-populate Mapel options
        if (isTopikChange && data.mapel_list) {
            const selMapel = document.getElementById('selMapelHasil');
            if (selMapel) {
                const prev = selMapel.value;
                selMapel.innerHTML = '<option value="">-- Semua Mapel --</option>';
                data.mapel_list.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m;
                    opt.textContent = m;
                    if (m === prev) opt.selected = true;
                    selMapel.appendChild(opt);
                });
            }
        }

        // Update Table Rows
        if (tbody) {
            tbody.innerHTML = data.table_html;
            tbody.style.opacity = '1';
        }

        // Update Load More footer
        const wrapMore = document.getElementById('wrapLoadMoreHasil');
        if (wrapMore) {
            const dispCount = data.display_count || data.total_peserta;
            const totCount = data.total_peserta;
            const elDisp = document.getElementById('lblDisplayCount');
            if (elDisp) elDisp.textContent = dispCount;
            const elTot = document.getElementById('lblTotalCount');
            if (elTot) elTot.textContent = totCount;

            if (totCount > dispCount) {
                wrapMore.style.display = 'flex';
            } else {
                wrapMore.style.display = 'none';
            }
        }

        // Re-init lucide icons
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
        }
    })
    .catch(err => {
        if (err.name === 'AbortError') return;
        if (tbody) tbody.style.opacity = '1';
    });
}

function onSearchInput(input) {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        applyFilterAjax(false);
    }, 300);
}

function resetFilterAjax(event) {
    if (event) event.preventDefault();
    const selTopik = document.getElementById('selTopikHasil');
    if (selTopik) selTopik.value = '0';
    const selMapel = document.getElementById('selMapelHasil');
    if (selMapel) selMapel.value = '';
    const selKelas = document.getElementById('selKelasHasil');
    if (selKelas) selKelas.value = '';
    const selSort = document.getElementById('selSortHasil');
    if (selSort) selSort.value = 'nilai_desc';
    const inpSearch = document.getElementById('inpSearchHasil');
    if (inpSearch) inpSearch.value = '';
    const selLimit = document.getElementById('selLimitHasil');
    if (selLimit) selLimit.value = '30';
    applyFilterAjax(true);
}

function loadMoreHasil() {
    currentLimitHasil += 30;
    const selLimit = document.getElementById('selLimitHasil');
    if (selLimit) {
        for (let i = 0; i < selLimit.options.length; i++) {
            if (selLimit.options[i].value == currentLimitHasil) {
                selLimit.selectedIndex = i;
                break;
            }
        }
    }

    const btnMore = document.getElementById('btnLoadMoreHasil');
    if (btnMore) {
        btnMore.innerHTML = '<span style="display:inline-block; animation:spin 0.6s linear infinite;">⏳</span> Memuat...';
    }

    const topikId = document.getElementById('selTopikHasil') ? document.getElementById('selTopikHasil').value : 0;
    const mapel = document.getElementById('selMapelHasil') ? document.getElementById('selMapelHasil').value : '';
    const kelas = document.getElementById('selKelasHasil') ? document.getElementById('selKelasHasil').value : '';
    const sort = document.getElementById('selSortHasil') ? document.getElementById('selSortHasil').value : 'nilai_desc';
    const q = document.getElementById('inpSearchHasil') ? document.getElementById('inpSearchHasil').value.trim() : '';

    const params = new URLSearchParams({
        topik_id: topikId,
        mapel: mapel,
        kelas: kelas,
        sort: sort,
        q: q,
        limit: currentLimitHasil,
        ajax: '1'
    });

    fetch('<?php echo base_url("cbt/hasil"); ?>?' + params.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) return;
        const tbody = document.getElementById('hasilTableBody');
        if (tbody) tbody.innerHTML = data.table_html;

        const wrapMore = document.getElementById('wrapLoadMoreHasil');
        if (wrapMore) {
            const dispCount = data.display_count || data.total_peserta;
            const totCount = data.total_peserta;
            const elDisp = document.getElementById('lblDisplayCount');
            if (elDisp) elDisp.textContent = dispCount;
            const elTot = document.getElementById('lblTotalCount');
            if (elTot) elTot.textContent = totCount;
            wrapMore.style.display = (totCount > dispCount) ? 'flex' : 'none';
        }

        if (btnMore) {
            btnMore.innerHTML = '<i data-lucide="chevron-down" style="width: 14px; height: 14px;"></i> +30 Tampilkan Lebih Banyak';
        }

        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
        }
    })
    .catch(() => {
        if (btnMore) {
            btnMore.innerHTML = '<i data-lucide="chevron-down" style="width: 14px; height: 14px;"></i> +30 Tampilkan Lebih Banyak';
        }
    });
}

function showAllHasil() {
    const selLimit = document.getElementById('selLimitHasil');
    if (selLimit) selLimit.value = 'all';
    applyFilterAjax(false);
}
</script>


