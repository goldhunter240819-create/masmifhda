<?php
$pageTitle = 'Rekap Absensi Ujian CBT';
$active_menu = 'absensi_rekap';
?>
<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/header.php'; ?>

        <div class="z-scroll" style="padding: 0 0 3rem 0;">

            <!-- Header -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-badge">MONITORING KEHADIRAN CBT</div>
                        <h1><i data-lucide="clipboard-check"></i> Rekap Absensi Siswa CBT</h1>
                        <p>Monitoring data kehadiran siswa pada setiap sesi ujian, riwayat pemindaian barcode 1 pintu, dan kepatuhan jadwal pelaksanaan.</p>
                    </div>
                    <div class="header-actions-premium" style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <a href="<?php echo base_url('cbt/scan'); ?>" class="e-btn e-btn-primary" style="background: #2563eb;">
                            <i data-lucide="qr-code"></i> Buka Scanner 1 Pintu
                        </a>
                        <button type="button" onclick="window.print()" class="e-btn e-btn-glass">
                            <i data-lucide="printer"></i> Cetak Laporan Kehadiran
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="card" style="padding: 1.25rem 1.5rem; border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; background: #ffffff;">
                <form method="GET" action="<?php echo base_url('cbt/absensi'); ?>" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                    <div>
                        <label class="label-sm">Tanggal Ujian</label>
                        <input type="date" name="tanggal" value="<?php echo htmlspecialchars($selectedTanggal); ?>" class="input-sm" onchange="this.form.submit()">
                    </div>

                    <div style="min-width: 160px;">
                        <label class="label-sm">Filter Kelas</label>
                        <select name="kelas" class="input-sm" onchange="this.form.submit()">
                            <option value="">-- Semua Kelas --</option>
                            <?php foreach ($kelasList as $k): ?>
                                <option value="<?php echo htmlspecialchars($k); ?>" <?php echo ($selectedKelas === $k) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($k); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="min-width: 200px;">
                        <label class="label-sm">Mata Pelajaran</label>
                        <select name="mapel" class="input-sm" onchange="this.form.submit()">
                            <option value="">-- Semua Mapel --</option>
                            <?php foreach ($mapelList as $m): ?>
                                <option value="<?php echo htmlspecialchars($m); ?>" <?php echo ($selectedMapel === $m) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($m); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="margin-top: 18px;">
                        <a href="<?php echo base_url('cbt/absensi'); ?>" class="e-btn e-btn-secondary" style="padding: 7px 14px;">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="card" style="border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden; background: #ffffff;">
                <div style="padding: 1.25rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                    <div style="font-weight: 800; font-size: 0.95rem; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                        <span>Daftar Siswa Terabsen</span>
                        <span style="font-size: 0.75rem; background: #dcfce7; color: #15803d; padding: 2px 8px; border-radius: 100px; font-weight: 800;">
                            <?php echo count($absensiRows); ?> Kehadiran
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
                                <th>Mata Pelajaran & Sesi</th>
                                <th>Waktu Scan Absensi</th>
                                <th>Status</th>
                                <th>Tipe Peserta</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($absensiRows)): ?>
                            <tr>
                                <td colspan="7" style="padding: 3rem 1.5rem; text-align: center; color: #94a3b8;">
                                    <i data-lucide="clipboard-x" style="width: 44px; height: 44px; margin-bottom: 10px; color: #cbd5e1;"></i>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: #475569;">Belum Ada Data Absensi CBT</div>
                                    <div style="font-size: 0.78rem;">Siswa belum melakukan scan pada tanggal atau filter yang dipilih.</div>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($absensiRows as $row): ?>
                                <tr>
                                    <td style="color: #94a3b8; font-weight: 600;"><?php echo $no++; ?></td>
                                    <td>
                                        <div style="font-weight: 800; color: #0f172a; font-size: 0.88rem;"><?php echo htmlspecialchars($row['nama_siswa']); ?></div>
                                        <div style="font-size: 0.72rem; color: #64748b; font-family: monospace;">NIS: <?php echo htmlspecialchars($row['nis']); ?></div>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 6px; background: #e0e7ff; color: #4338ca;">
                                            <?php echo htmlspecialchars($row['kelas_nama']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: #0f172a; font-size: 0.85rem;"><?php echo htmlspecialchars($row['mapel_nama']); ?></div>
                                        <div style="font-size: 0.72rem; color: #64748b;"><?php echo htmlspecialchars($row['jam_slot']); ?></div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 800; font-size: 0.82rem; color: #15803d; font-family: monospace;">
                                            <?php echo date('H:i:s', strtotime($row['waktu_scan'])); ?> WIB
                                        </div>
                                        <div style="font-size: 0.7rem; color: #64748b;"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></div>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.72rem; font-weight: 800; padding: 3px 8px; border-radius: 6px; <?php echo ($row['status'] === 'Hadir') ? 'background:#dcfce7;color:#15803d;' : 'background:#fef3c7;color:#b45309;'; ?>">
                                            <?php echo htmlspecialchars($row['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($row['tipe_peserta'] === 'khusus'): ?>
                                            <span style="font-size: 0.7rem; font-weight: 800; padding: 2px 6px; border-radius: 4px; background: #fdf2f8; color: #db2777; border: 1px solid #fbcfe8;">
                                                Kelas Jauh
                                            </span>
                                        <?php else: ?>
                                            <span style="font-size: 0.7rem; font-weight: 700; color: #64748b;">
                                                Reguler Siakad
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
