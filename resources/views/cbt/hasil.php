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
                        <a href="<?php echo base_url('cbt/hasil/cetak?topik_id=' . ($selectedTopikId ?? 0) . '&mapel=' . urlencode($selectedMapel ?? '') . '&ujian_id=' . ($selectedUjianId ?? '') . '&kelas=' . urlencode($selectedKelas ?? '') . '&sort=' . urlencode($sort ?? 'nilai_desc') . '&q=' . urlencode($search ?? '')); ?>" target="_blank" class="e-btn e-btn-glass" title="Buka Dokumen Rekap Nilai Resmi Kemenag A4">
                            <i data-lucide="printer"></i> Cetak Rekap Nilai
                        </a>
                        <a href="<?php echo base_url('cbt/analisis?topik_id=' . ($selectedTopikId ?? 0) . '&ujian_id=' . ($selectedUjianId ?? '')); ?>" class="e-btn e-btn-blue-light">
                            <i data-lucide="bar-chart-2"></i> Analisis Butir Soal
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filter Panel -->
            <div class="card" style="padding: 1.25rem 1.5rem; border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; background: #ffffff;">
                <form method="GET" action="<?php echo base_url('cbt/hasil'); ?>" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                    <div style="flex: 1.6; min-width: 200px;">
                        <label class="label-sm">Topik Ujian</label>
                        <select name="topik_id" class="input-sm" onchange="if(document.getElementById('selMapelHasil')) document.getElementById('selMapelHasil').value=''; this.form.submit()">
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
                        <select name="mapel" id="selMapelHasil" class="input-sm" onchange="this.form.submit()">
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
                        <select name="kelas" class="input-sm" onchange="this.form.submit()">
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
                        <select name="sort" class="input-sm" onchange="this.form.submit()">
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
                            <input type="text" name="q" class="input-sm" placeholder="Ketik nama siswa..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                            <button type="submit" class="e-btn e-btn-primary" style="padding: 7px 12px; background: #4f46e5;" title="Cari Data">
                                <i data-lucide="search" style="width: 14px; height: 14px;"></i>
                            </button>
                            <?php if (!empty($selectedTopikId) || !empty($selectedMapel) || !empty($selectedKelas) || !empty($search) || ($sort !== 'nilai_desc')): ?>
                                <a href="<?php echo base_url('cbt/hasil'); ?>" class="e-btn" style="padding: 7px 12px; background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; display: inline-flex; align-items: center; justify-content: center;" title="Reset Semua Filter">
                                    <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i>
                                </a>
                            <?php endif; ?>
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
                                <th style="width: 130px; text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pesertaList)): ?>
                            <tr>
                                <td colspan="9" style="padding: 3rem 1.5rem; text-align: center; color: #94a3b8;">
                                    <i data-lucide="folder-search" style="width: 44px; height: 44px; margin-bottom: 10px; color: #cbd5e1;"></i>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: #475569;">Tidak ada data hasil ujian yang sesuai dengan filter</div>
                                    <div style="font-size: 0.78rem; margin-bottom: 14px;">Silakan sesuaikan pilihan Topik, Mata Pelajaran, atau Kelas yang dipilih.</div>
                                    <a href="<?php echo base_url('cbt/hasil'); ?>" class="e-btn e-btn-primary" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 800; font-size: 0.8rem; padding: 7px 16px; background: #4f46e5;">
                                        <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i> Reset Semua Filter
                                    </a>
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

<style>
@keyframes popInModal {
    0% { transform: scale(0.92); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
</style>

<script>
function bukaModalReopen(pesertaId, namaSiswa, paketJudul) {
    document.getElementById('reopenPesertaId').value = pesertaId;
    document.getElementById('reopenNamaSiswa').textContent = namaSiswa;
    document.getElementById('reopenPaketJudul').textContent = paketJudul || 'Paket Ujian CBT';
    document.getElementById('modalReopenExam').style.display = 'flex';
}

function tutupModalReopen() {
    document.getElementById('modalReopenExam').style.display = 'none';
}
</script>


