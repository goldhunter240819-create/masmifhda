<?php
$pageTitle = 'Analisis Butir Soal CBT';
$active_menu = 'analisis';
?>
<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/header.php'; ?>

        <div class="z-scroll" style="padding: 1.5rem 2rem 3rem;">

            <!-- Elite Page Header -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-badge">PSIKOMETRI & EVALUASI INSTRUMEN</div>
                        <h1><i data-lucide="bar-chart-2"></i> Analisis Butir Soal Ujian</h1>
                        <p>Evaluasi tingkat kesukaran ($P$), indeks daya pembeda ($D$), pola sebaran opsi jawaban (distraktor), dan status rekomendasi kelayakan soal.</p>
                    </div>
                    <div class="header-actions-premium" style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <button type="button" onclick="window.print()" class="e-btn e-btn-glass">
                            <i data-lucide="printer"></i> Cetak Analisis Butir
                        </button>
                        <a href="<?php echo base_url('cbt/hasil?ujian_id=' . ($selectedUjianId ?? '')); ?>" class="e-btn e-btn-blue-light">
                            <i data-lucide="award"></i> Rekap Nilai Siswa
                        </a>
                        <a href="<?php echo base_url('cbt/soal?ujian_id=' . ($selectedUjianId ?? '')); ?>" class="e-btn e-btn-primary" style="background: #4f46e5;">
                            <i data-lucide="edit-3"></i> Edit Soal Ujian
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filter Paket Ujian -->
            <div class="card" style="padding: 1.25rem 1.5rem; border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; background: #ffffff;">
                <form method="GET" action="<?php echo base_url('cbt/analisis'); ?>" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
                    <div style="flex: 2; min-width: 250px;">
                        <label class="label-sm" style="font-weight: 800; color: #1e293b; margin-bottom: 6px; display: block;">
                            <i data-lucide="filter" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;"></i> Pilih Paket Ujian yang Dianalisis
                        </label>
                        <select name="ujian_id" class="input-sm" onchange="this.form.submit()" style="font-size: 0.9rem; font-weight: 600; padding: 8px 12px;">
                            <?php foreach($ujianList as $uj): ?>
                                <option value="<?php echo $uj['id']; ?>" <?php echo ($selectedUjianId == $uj['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($uj['nama_ujian'] . ' — ' . $uj['mapel_nama'] . ' (' . ($uj['kelas_nama'] ?: 'Semua Kelas') . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if ($currentUjian): ?>
                    <div style="flex: 3; min-width: 280px; display: flex; gap: 10px; flex-wrap: wrap; background: #f8fafc; padding: 10px 14px; border-radius: 12px; border: 1px solid #e2e8f0;">
                        <div>
                            <div style="font-size: 0.7rem; color: #64748b; font-weight: 700;">Mata Pelajaran</div>
                            <div style="font-size: 0.85rem; color: #0f172a; font-weight: 800;"><?php echo htmlspecialchars($currentUjian['mapel_nama']); ?></div>
                        </div>
                        <div style="border-left: 1px solid #cbd5e1; padding-left: 10px;">
                            <div style="font-size: 0.7rem; color: #64748b; font-weight: 700;">KKM</div>
                            <div style="font-size: 0.85rem; color: #4338ca; font-weight: 800;"><?php echo $currentUjian['kkm']; ?></div>
                        </div>
                        <div style="border-left: 1px solid #cbd5e1; padding-left: 10px;">
                            <div style="font-size: 0.7rem; color: #64748b; font-weight: 700;">Durasi</div>
                            <div style="font-size: 0.85rem; color: #0f172a; font-weight: 800;"><?php echo $currentUjian['durasi_menit']; ?> mnt</div>
                        </div>
                        <div style="border-left: 1px solid #cbd5e1; padding-left: 10px;">
                            <div style="font-size: 0.7rem; color: #64748b; font-weight: 700;">Total Soal</div>
                            <div style="font-size: 0.85rem; color: #0284c7; font-weight: 800;"><?php echo count($analisisSoal); ?> Butir</div>
                        </div>
                        <div style="border-left: 1px solid #cbd5e1; padding-left: 10px;">
                            <div style="font-size: 0.7rem; color: #64748b; font-weight: 700;">Sampel Siswa</div>
                            <div style="font-size: 0.85rem; color: #15803d; font-weight: 800;"><?php echo $totalPesertaUjian; ?> Peserta</div>
                        </div>
                    </div>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Ringkasan Statistik Ujian (Psychometric KPI Cards) -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                <div class="card" style="padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="target"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.45rem; font-weight: 900; color: #0f172a; line-height: 1;"><?php echo number_format($rataRata, 1); ?></div>
                        <div style="font-size: 0.74rem; font-weight: 700; color: #64748b; margin-top: 4px;">Mean / Rata-Rata Skor</div>
                    </div>
                </div>

                <div class="card" style="padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #f0fdf4; color: #16a34a; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="arrow-up-circle"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.45rem; font-weight: 900; color: #15803d; line-height: 1;"><?php echo number_format($nilaiMaks, 1); ?></div>
                        <div style="font-size: 0.74rem; font-weight: 700; color: #64748b; margin-top: 4px;">Skor Tertinggi Siswa</div>
                    </div>
                </div>

                <div class="card" style="padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #fef2f2; color: #dc2626; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="arrow-down-circle"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.45rem; font-weight: 900; color: #b91c1c; line-height: 1;"><?php echo number_format($nilaiMin, 1); ?></div>
                        <div style="font-size: 0.74rem; font-weight: 700; color: #64748b; margin-top: 4px;">Skor Terendah Siswa</div>
                    </div>
                </div>

                <div class="card" style="padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="activity"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.45rem; font-weight: 900; color: #4338ca; line-height: 1;"><?php echo number_format($standarDeviasi, 2); ?></div>
                        <div style="font-size: 0.74rem; font-weight: 700; color: #64748b; margin-top: 4px;">Standar Deviasi ($\sigma$)</div>
                    </div>
                </div>

                <div class="card" style="padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #fffbeb; color: #d97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="shield-check"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.45rem; font-weight: 900; color: #b45309; line-height: 1;"><?php echo number_format($reliabilitasKR20, 2); ?></div>
                        <div style="font-size: 0.74rem; font-weight: 700; color: #64748b; margin-top: 4px;">Reliabilitas (KR-20)</div>
                    </div>
                </div>
            </div>

            <!-- Petunjuk Pedoman Analisis Psikometri (Panduan Guru) -->
            <div class="card" style="border-radius: 16px; border: 1px solid #e2e8f0; background: #ffffff; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;">
                <div style="font-weight: 800; font-size: 0.92rem; color: #1e293b; display: flex; align-items: center; gap: 8px; margin-bottom: 0.75rem;">
                    <i data-lucide="info" style="width: 18px; height: 18px; color: #4f46e5;"></i>
                    Panduan & Parameter Baku Analisis Butir Soal
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; font-size: 0.8rem; color: #475569;">
                    <div style="background: #f8fafc; padding: 10px 14px; border-radius: 10px; border-left: 3px solid #0ea5e9;">
                        <strong style="color: #0369a1;">1. Tingkat Kesukaran (P)</strong>:
                        <ul style="margin: 4px 0 0 1rem; padding: 0;">
                            <li>$P < 0.30$ = <strong>Sukar</strong> (Proporsi jawaban benar sedikit)</li>
                            <li>$0.30 \le P \le 0.70$ = <strong>Sedang / Ideal</strong> (Sangat dianjurkan)</li>
                            <li>$P > 0.70$ = <strong>Mudah</strong> (Hampir semua menjawab benar)</li>
                        </ul>
                    </div>

                    <div style="background: #f8fafc; padding: 10px 14px; border-radius: 10px; border-left: 3px solid #16a34a;">
                        <strong style="color: #15803d;">2. Daya Pembeda (D)</strong>:
                        <ul style="margin: 4px 0 0 1rem; padding: 0;">
                            <li>$D \ge 0.40$ = <strong>Sangat Baik</strong> (Membedakan kelompok atas/bawah)</li>
                            <li>$0.30 \le D < 0.40$ = <strong>Baik</strong> (Dapat langsung digunakan)</li>
                            <li>$0.20 \le D < 0.30$ = <strong>Cukup</strong> (Perlu sedikit perbaikan)</li>
                            <li>$D < 0.20$ = <strong>Buruk / Dibuang</strong> (Tidak membedakan siswa)</li>
                        </ul>
                    </div>

                    <div style="background: #f8fafc; padding: 10px 14px; border-radius: 10px; border-left: 3px solid #8b5cf6;">
                        <strong style="color: #6d28d9;">3. Efektivitas Pengecoh (Distraktor)</strong>:
                        <ul style="margin: 4px 0 0 1rem; padding: 0;">
                            <li>Setiap opsi salah minimal dipilih oleh $\ge 5\%$ peserta.</li>
                            <li>Kelompok siswa bawah lebih banyak terkecoh dibanding kelompok atas.</li>
                            <li>Opsi yang sama sekali tidak dipilih = distraktor mati (perlu diganti).</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tabel Analisis Butir Soal -->
            <div class="card" style="border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden; background: #ffffff;">
                <div style="padding: 1.25rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div style="font-weight: 800; font-size: 0.95rem; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                        <span>Matriks Analisis Butir Soal</span>
                        <span style="font-size: 0.75rem; background: #e0e7ff; color: #4338ca; padding: 2px 8px; border-radius: 100px; font-weight: 800;">
                            <?php echo count($analisisSoal); ?> Butir Soal
                        </span>
                    </div>
                    <div style="font-size: 0.8rem; color: #64748b;">
                        Sampel Analisis: <strong><?php echo $totalPesertaUjian; ?> Siswa</strong>
                    </div>
                </div>

                <div class="z-table-wrap">
                    <table class="z-table">
                        <thead>
                            <tr>
                                <th style="width: 45px; text-align: center;">No</th>
                                <th style="min-width: 220px;">Pertanyaan Soal</th>
                                <th style="width: 60px; text-align: center;">Kunci</th>
                                <th style="width: 80px; text-align: center;">Benar / Salah</th>
                                <th style="width: 120px; text-align: center;">Tingkat Kesukaran</th>
                                <th style="width: 120px; text-align: center;">Daya Pembeda</th>
                                <th style="min-width: 180px; text-align: center;">Sebaran Opsi (A - E)</th>
                                <th style="width: 130px; text-align: center;">Rekomendasi</th>
                                <th style="width: 70px; text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($analisisSoal)): ?>
                            <tr>
                                <td colspan="9" style="padding: 3rem 1.5rem; text-align: center; color: #94a3b8;">
                                    <i data-lucide="file-question" style="width: 44px; height: 44px; margin-bottom: 10px; color: #cbd5e1;"></i>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: #475569;">Belum ada butir soal atau peserta pada paket ujian ini</div>
                                    <div style="font-size: 0.78rem;">Tambahkan butir soal terlebih dahulu di menu Kelola Ujian & Soal.</div>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach($analisisSoal as $s): 
                                    $pVal = $s['tingkat_kesukaran'];
                                    $dVal = $s['daya_beda'];

                                    // Klasifikasi Tingkat Kesukaran & Daya Pembeda
                                    if ($totalPesertaUjian == 0) {
                                        $tkLabel = 'Menunggu';
                                        $tkBadge = 'background:#f1f5f9; color:#64748b; border:1px solid #cbd5e1;';
                                        $dpLabel = 'Menunggu';
                                        $dpBadge = 'background:#f1f5f9; color:#64748b; border:1px solid #cbd5e1;';
                                        $rekomendasi = 'Menunggu Peserta';
                                        $rekBadge = 'background:#f1f5f9; color:#64748b;';
                                    } else {
                                        if ($pVal < 0.30) {
                                            $tkLabel = 'Sukar';
                                            $tkBadge = 'background:#fee2e2; color:#b91c1c; border:1px solid #fecaca;';
                                        } elseif ($pVal <= 0.70) {
                                            $tkLabel = 'Sedang';
                                            $tkBadge = 'background:#dcfce7; color:#15803d; border:1px solid #bbf7d0;';
                                        } else {
                                            $tkLabel = 'Mudah';
                                            $tkBadge = 'background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd;';
                                        }

                                        if ($dVal >= 0.40) {
                                            $dpLabel = 'Sangat Baik';
                                            $dpBadge = 'background:#dcfce7; color:#15803d; border:1px solid #bbf7d0;';
                                        } elseif ($dVal >= 0.30) {
                                            $dpLabel = 'Baik';
                                            $dpBadge = 'background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd;';
                                        } elseif ($dVal >= 0.20) {
                                            $dpLabel = 'Cukup';
                                            $dpBadge = 'background:#fef3c7; color:#b45309; border:1px solid #fde68a;';
                                        } else {
                                            $dpLabel = 'Buruk';
                                            $dpBadge = 'background:#fee2e2; color:#b91c1c; border:1px solid #fecaca;';
                                        }

                                        $rekomendasi = $s['rekomendasi'];
                                        $rekBadge = 'background:#dcfce7; color:#15803d;';
                                        if ($rekomendasi === 'Dibuang') {
                                            $rekBadge = 'background:#fee2e2; color:#b91c1c;';
                                        } elseif (strpos($rekomendasi, 'Revisi') !== false) {
                                            $rekBadge = 'background:#fef3c7; color:#b45309;';
                                        }
                                    }
                                ?>
                                <tr>
                                    <td style="text-align: center; color: #64748b; font-weight: 700;">
                                        <?php echo $s['nomor_urut']; ?>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: #0f172a; font-size: 0.88rem; line-height: 1.4;">
                                            <?php echo htmlspecialchars(mb_strimwidth(strip_tags($s['pertanyaan']), 0, 75, '...')); ?>
                                        </div>
                                        <div style="font-size: 0.72rem; color: #64748b; margin-top: 2px;">
                                            Bobot: <?php echo $s['bobot']; ?> poin
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span style="font-weight: 900; font-size: 0.9rem; background: #e0e7ff; color: #4338ca; width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px;">
                                            <?php echo htmlspecialchars($s['kunci_jawaban']); ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center; font-size: 0.82rem;">
                                        <span style="color: #16a34a; font-weight: 800;"><?php echo $s['jumlah_benar']; ?> B</span>
                                        <span style="color: #cbd5e1; margin: 0 4px;">/</span>
                                        <span style="color: #dc2626; font-weight: 800;"><?php echo $s['jumlah_salah']; ?> S</span>
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="font-weight: 800; font-size: 0.88rem; color: #0f172a;"><?php echo number_format($pVal, 2); ?></div>
                                        <span style="display: inline-block; font-size: 0.7rem; font-weight: 700; padding: 2px 8px; border-radius: 6px; margin-top: 2px; <?php echo $tkBadge; ?>">
                                            <?php echo $tkLabel; ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="font-weight: 800; font-size: 0.88rem; color: #0f172a;"><?php echo number_format($dVal, 2); ?></div>
                                        <span style="display: inline-block; font-size: 0.7rem; font-weight: 700; padding: 2px 8px; border-radius: 6px; margin-top: 2px; <?php echo $dpBadge; ?>">
                                            <?php echo $dpLabel; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <!-- Mini Distribution Bars / Badges -->
                                        <div style="display: flex; gap: 4px; justify-content: center; align-items: center;">
                                            <?php 
                                            foreach (['A', 'B', 'C', 'D', 'E'] as $opt): 
                                                $countOpt = $s['sebaran'][$opt] ?? 0;
                                                $isKey = ($opt === $s['kunci_jawaban']);
                                            ?>
                                                <div title="Opsi <?php echo $opt; ?>: <?php echo $countOpt; ?> siswa" style="display: flex; flex-direction: column; align-items: center; font-size: 0.68rem; min-width: 28px; padding: 2px 4px; border-radius: 6px; <?php echo $isKey ? 'background:#dcfce7; border:1px solid #16a34a; color:#15803d; font-weight:800;' : 'background:#f1f5f9; color:#475569; font-weight:600;'; ?>">
                                                    <span><?php echo $opt; ?></span>
                                                    <span style="font-weight: 800; font-size: 0.72rem;"><?php echo $countOpt; ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span style="display: inline-block; font-size: 0.72rem; font-weight: 800; padding: 3px 10px; border-radius: 8px; <?php echo $rekBadge; ?>">
                                            <?php echo htmlspecialchars($rekomendasi); ?>
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <button type="button" class="e-btn e-btn-blue-light" style="padding: 5px 8px; font-size: 0.72rem;" onclick="bukaModalDetail(<?php echo htmlspecialchars(json_encode($s)); ?>)" title="Lihat Teks Lengkap Soal">
                                            <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                        </button>
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

<!-- Modal Detail Butir Soal -->
<div id="modalDetailSoal" class="modal-overlay" onclick="if(event.target === this) this.classList.remove('open')">
    <div style="background: #ffffff; width: 90%; max-width: 600px; border-radius: 20px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.15); border: 1px solid #e2e8f0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="file-text" style="width: 20px; height: 20px; color: #4f46e5;"></i>
                <span id="dtlHeader">Detail Butir Soal #</span>
            </h3>
            <button type="button" onclick="document.getElementById('modalDetailSoal').classList.remove('open')" style="background: none; border: none; cursor: pointer; color: #94a3b8;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>

        <div style="background: #f8fafc; border-radius: 12px; padding: 1rem 1.25rem; border: 1px solid #e2e8f0; margin-bottom: 1.25rem;">
            <div style="font-size: 0.72rem; font-weight: 800; color: #64748b; margin-bottom: 6px;">TEKS PERTANYAAN:</div>
            <div id="dtlPertanyaan" style="font-size: 0.95rem; font-weight: 600; color: #0f172a; line-height: 1.5;"></div>
        </div>

        <div style="margin-bottom: 1.25rem;">
            <div style="font-size: 0.75rem; font-weight: 800; color: #64748b; margin-bottom: 8px;">DISTRIBUSI OPSI JAWABAN:</div>
            <div id="dtlOpsiList" style="display: flex; flex-direction: column; gap: 8px;">
                <!-- Filled dynamically by JavaScript -->
            </div>
        </div>

        <div style="background: #eff6ff; border-radius: 12px; padding: 10px 14px; border: 1px solid #bfdbfe; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <span style="font-size: 0.72rem; color: #1e40af; font-weight: 700;">Status Rekomendasi:</span>
                <span id="dtlRekomendasi" style="font-size: 0.82rem; font-weight: 900; color: #1e3a8a; margin-left: 6px;"></span>
            </div>
            <button type="button" onclick="document.getElementById('modalDetailSoal').classList.remove('open')" class="e-btn e-btn-dark" style="padding: 6px 14px; font-size: 0.75rem;">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
function bukaModalDetail(s) {
    document.getElementById('dtlHeader').textContent = 'Detail Butir Soal #' + s.nomor_urut;
    document.getElementById('dtlPertanyaan').innerHTML = s.pertanyaan.replace(/\n/g, '<br>');
    document.getElementById('dtlRekomendasi').textContent = s.rekomendasi;

    const listContainer = document.getElementById('dtlOpsiList');
    listContainer.innerHTML = '';

    const total = (s.jumlah_benar + s.jumlah_salah) || 1;
    const opts = ['A', 'B', 'C', 'D', 'E'];

    opts.forEach(k => {
        const val = s['opsi_' + k.toLowerCase()];
        if (!val) return;

        const isKunci = (k === s.kunci_jawaban);
        const count = s.sebaran[k] || 0;
        const pct = Math.round((count / total) * 100);

        const row = document.createElement('div');
        row.style.display = 'flex';
        row.style.alignItems = 'center';
        row.style.justifyContent = 'space-between';
        row.style.padding = '8px 12px';
        row.style.borderRadius = '10px';
        row.style.border = isKunci ? '1.5px solid #16a34a' : '1px solid #e2e8f0';
        row.style.background = isKunci ? '#f0fdf4' : '#ffffff';

        row.innerHTML = `
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-weight: 900; width: 24px; height: 24px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.8rem; ${isKunci ? 'background:#16a34a; color:#fff;' : 'background:#f1f5f9; color:#475569;'}">${k}</span>
                <span style="font-size: 0.85rem; font-weight: ${isKunci ? '800' : '500'}; color: #0f172a;">${val}</span>
                ${isKunci ? '<span style="font-size: 0.7rem; font-weight: 800; background: #bbf7d0; color: #15803d; padding: 1px 6px; border-radius: 4px;">KUNCI BENAR</span>' : ''}
            </div>
            <div style="text-align: right; font-size: 0.78rem; font-weight: 800; color: ${isKunci ? '#15803d' : '#64748b'};">
                ${count} siswa (${pct}%)
            </div>
        `;
        listContainer.appendChild(row);
    });

    document.getElementById('modalDetailSoal').classList.add('open');
    if (window.lucide) lucide.createIcons();
}
</script>

