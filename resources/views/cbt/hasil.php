<?php
$pageTitle = 'Hasil Ujian CBT';
$active_menu = 'hasil';
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
                        <div class="header-badge">EVALUASI & PENILAIAN</div>
                        <h1><i data-lucide="award"></i> Rekap Hasil Ujian Siswa</h1>
                        <p>Monitoring skor perolehan siswa, analisis ketuntasan terhadap KKM, dan rekapitulasi nilai ujian berbasis komputer.</p>
                    </div>
                    <div class="header-actions-premium" style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <button type="button" onclick="window.print()" class="e-btn e-btn-glass">
                            <i data-lucide="printer"></i> Cetak Rekap Nilai
                        </button>
                        <a href="<?php echo base_url('cbt/analisis?ujian_id=' . ($selectedUjianId ?? '')); ?>" class="e-btn e-btn-blue-light">
                            <i data-lucide="bar-chart-2"></i> Analisis Butir Soal
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filter Panel -->
            <div class="card" style="padding: 1.25rem 1.5rem; border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; background: #ffffff;">
                <form method="GET" action="<?php echo base_url('cbt/hasil'); ?>" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                    <div style="flex: 2; min-width: 220px;">
                        <label class="label-sm">Pilih Paket Ujian</label>
                        <select name="ujian_id" class="input-sm" onchange="this.form.submit()">
                            <option value="0">-- Semua Paket Ujian --</option>
                            <?php foreach($ujianList as $uj): ?>
                                <option value="<?php echo $uj['id']; ?>" <?php echo ($selectedUjianId == $uj['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($uj['nama_ujian'] . ' (' . $uj['mapel_nama'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="flex: 1; min-width: 150px;">
                        <label class="label-sm">Filter Kelas</label>
                        <select name="kelas" class="input-sm" onchange="this.form.submit()">
                            <option value="">-- Semua Kelas --</option>
                            <?php foreach($kelasList as $kls): ?>
                                <option value="<?php echo htmlspecialchars($kls); ?>" <?php echo ($selectedKelas === $kls) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($kls); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="flex: 1.5; min-width: 180px;">
                        <label class="label-sm">Cari Nama / NIS Siswa</label>
                        <div style="display: flex; gap: 6px;">
                            <input type="text" name="q" class="input-sm" placeholder="Ketik nama siswa..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                            <button type="submit" class="e-btn e-btn-primary" style="padding: 7px 12px; background: #4f46e5;">
                                <i data-lucide="search" style="width: 14px; height: 14px;"></i>
                            </button>
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
                        <div style="font-size: 1.5rem; font-weight: 900; color: #0f172a; line-height: 1;"><?php echo count($pesertaList); ?></div>
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-top: 4px;">Peserta Terdata</div>
                    </div>
                </div>

                <div class="card" style="padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #f0fdf4; color: #16a34a; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="trending-up"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 900; color: #15803d; line-height: 1;"><?php echo number_format($rataRata, 1); ?></div>
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-top: 4px;">Rata-Rata Nilai</div>
                    </div>
                </div>

                <div class="card" style="padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="check-check"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 900; color: #4338ca; line-height: 1;"><?php echo $persenTuntas; ?>%</div>
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-top: 4px;">Ketuntasan (KKM: <?php echo $kkmUjian; ?>)</div>
                    </div>
                </div>

                <div class="card" style="padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #fef2f2; color: #dc2626; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="alert-circle"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 900; color: #b91c1c; line-height: 1;"><?php echo $jumlahRemedial; ?></div>
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-top: 4px;">Perlu Remedial</div>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card" style="border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden; background: #ffffff;">
                <div style="padding: 1.25rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                    <div style="font-weight: 800; font-size: 0.95rem; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                        <span>Daftar Nilai Siswa</span>
                        <span style="font-size: 0.75rem; background: #e0e7ff; color: #4338ca; padding: 2px 8px; border-radius: 100px; font-weight: 800;">
                            <?php echo count($pesertaList); ?> Siswa
                        </span>
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
                                <th>Nilai Akhir</th>
                                <th>Status Kelulusan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pesertaList)): ?>
                            <tr>
                                <td colspan="8" style="padding: 3rem 1.5rem; text-align: center; color: #94a3b8;">
                                    <i data-lucide="folder-search" style="width: 44px; height: 44px; margin-bottom: 10px; color: #cbd5e1;"></i>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: #475569;">Tidak ada data hasil ujian</div>
                                    <div style="font-size: 0.78rem;">Pilih paket ujian lain atau tunggu siswa menyelesaikan ujian.</div>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach($pesertaList as $p): 
                                    $isSelesai = ($p['status_pengerjaan'] === 'Selesai');
                                    $isTuntas = ($isSelesai && $p['nilai_akhir'] >= $kkmUjian);
                                ?>
                                <tr>
                                    <td style="color: #94a3b8; font-weight: 600;"><?php echo $no++; ?></td>
                                    <td>
                                        <div style="font-weight: 800; color: #0f172a; font-size: 0.88rem;"><?php echo htmlspecialchars($p['nama_siswa']); ?></div>
                                        <div style="font-size: 0.72rem; color: #64748b; font-family: monospace;">NIS: <?php echo htmlspecialchars($p['nis'] ?: '-'); ?></div>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 6px; background: #e0e7ff; color: #4338ca;">
                                            <?php echo htmlspecialchars($p['kelas_nama'] ?: '-'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: #1e293b; font-size: 0.82rem;"><?php echo htmlspecialchars($p['nama_ujian'] ?? 'Ujian'); ?></div>
                                    </td>
                                    <td>
                                        <?php if ($isSelesai): ?>
                                        <span style="font-weight: 800; color: #15803d; background: #dcfce7; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">
                                            <?php echo $p['jumlah_benar']; ?> Benar
                                        </span>
                                        <?php else: ?>
                                        <span style="color: #94a3b8; font-size: 0.8rem;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($isSelesai): ?>
                                        <span style="font-weight: 800; color: #b91c1c; background: #fee2e2; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">
                                            <?php echo $p['jumlah_salah']; ?> Salah
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

