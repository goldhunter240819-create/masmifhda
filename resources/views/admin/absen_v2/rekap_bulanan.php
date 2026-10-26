<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="z-main">
        <header class="z-header" style="height: 80px; padding: 0 2rem; background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100;">
            <div class="z-header-left" style="display:flex;align-items:center;gap:15px;">
                <button class="z-hamburger" onclick="zToggleSidebar()" style="background:none;border:none;cursor:pointer;color:#64748b;">
                    <i data-lucide="menu"></i>
                </button>
                <div class="header-breadcrumb" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;">
                    <span style="font-weight:800;letter-spacing:1px;font-size:0.8rem;color:#0ea5e9;">ABSEN V2</span>
                    <i data-lucide="chevron-right" style="width:14px;color:#cbd5e1;"></i>
                    <span style="font-weight:700;color:#1e293b;">Rekap Absensi (Rentang Tanggal)</span>
                </div>
            </div>
            <div class="z-header-right" style="display:flex;align-items:center;gap:1.5rem;">
                <!-- PORTAL HUB BUTTON -->
                <a href="<?php echo base_url('portal'); ?>" style="display:flex;align-items:center;gap:8px;padding:6px 16px;border-radius:100px;border:1px solid #bfdbfe;background:#eff6ff;color:#3b82f6;font-size:0.75rem;font-weight:800;text-decoration:none;transition:0.2s;white-space:nowrap;">
                    <i data-lucide="layout-grid" style="width:14px;height:14px;"></i> Portal HUB
                </a>

                <div class="z-header-user" style="display:flex;align-items:center;gap:12px;cursor:pointer;">
                    <div class="z-user-info" style="text-align: right;">
                        <div class="name" style="font-weight:800;font-size:0.85rem;color:#0f172a;line-height:1.2;"><?php echo isset($nama) ? $nama : 'Administrator'; ?></div>
                        <div class="role" style="font-size:0.7rem;color:#64748b;">Administrator</div>
                    </div>
                    <?php $usr_initial = isset($initial) && !empty($initial) ? $initial : (isset($nama) ? strtoupper(substr($nama,0,2)) : 'AD'); ?>
                    <div class="z-avatar-premium" style="width:36px;height:36px;color:white;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;background:linear-gradient(135deg, #0ea5e9, #0284c7);box-shadow:0 4px 12px rgba(14,165,233,0.2);"><?php echo $usr_initial; ?></div>
                </div>
                
                <!-- LOGOUT BUTTON -->
                <a href="<?php echo base_url('logout'); ?>" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:10px;background:#fef2f2;color:#ef4444;text-decoration:none;transition:0.2s;border:1px solid #fee2e2;">
                    <i data-lucide="log-out" style="width:18px;height:18px;"></i>
                </a>
            </div>
        </header>

        <div class="z-scroll" style="padding: 2rem;">
            <!-- Welcome Hero (Elite Page Header) -->
            <div class="elite-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: 16px; margin-bottom: 2rem; color: white; position: relative; overflow: hidden;">
                <div class="header-bg-shapes" style="position: absolute; top: 0; right: 0; bottom: 0; left: 0; background-image: radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.15) 0%, transparent 40%), radial-gradient(circle at 20% 80%, rgba(0, 0, 0, 0.1) 0%, transparent 40%); z-index: 1;"></div>
                <div style="position: absolute; inset: 0; opacity: 0.15; pointer-events: none; background: url('data:image/svg+xml,%3Csvg width=\'24\' height=\'24\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Ccircle cx=\'2\' cy=\'2\' r=\'1.5\' fill=\'white\' /%3E%3C/svg%3E'); z-index: 1;"></div>
                <div style="position: relative; z-index: 2;">
                    <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="file-spreadsheet" style="color: rgba(255,255,255,0.8);"></i> Rekap Bulanan Absensi
                    </h1>
                    <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Lihat statistik kehadiran siswa dalam rentang waktu tertentu.</p>
                </div>
            </div>

            <?php
            // Perhitungan Persentase & Data Statistik
            $rekap_stats = $rekap; // copy
            $total_siswa_valid = 0;
            $sum_persentase = 0;

            foreach ($rekap_stats as &$s) {
                $hadir = $s['rekap']['Hadir'] + $s['rekap']['Terlambat'];
                $sakit = $s['rekap']['Sakit'];
                $izin = $s['rekap']['Izin'];
                $alpa = $s['rekap']['Alpa'];
                $bolos = $s['rekap']['Bolos'];
                
                $total_rekam = $hadir + $sakit + $izin + $alpa + $bolos;
                $s['persentase'] = $total_rekam > 0 ? round(($hadir / $total_rekam) * 100, 1) : 0;
                $s['total_rekam'] = $total_rekam;
                $s['total_hadir'] = $hadir;

                if ($total_rekam > 0) {
                    $total_siswa_valid++;
                    $sum_persentase += $s['persentase'];
                }
            }
            unset($s);

            $avg_persentase = $total_siswa_valid > 0 ? round($sum_persentase / $total_siswa_valid, 1) : 0;

            // Sort untuk Top 5 Paling Rajin
            usort($rekap_stats, function($a, $b) {
                if ($b['persentase'] == $a['persentase']) {
                    return strcmp($a['avg_jam_masuk'], $b['avg_jam_masuk']); // Paling pagi di atas
                }
                return $b['persentase'] <=> $a['persentase'];
            });
            $top_5 = array_slice(array_filter($rekap_stats, function($item) { return $item['total_hadir'] > 0; }), 0, 5);

            // Sort untuk Bottom 5 (Siswa Perlu Perhatian)
            $bottom_5_list = $rekap_stats;
            usort($bottom_5_list, function($a, $b) {
                if ($a['persentase'] == $b['persentase']) {
                    return strcmp($b['avg_jam_masuk'], $a['avg_jam_masuk']); // Paling siang di atas
                }
                return $a['persentase'] <=> $b['persentase']; // Persentase terkecil di atas
            });
            $bottom_5 = array_slice(array_filter($bottom_5_list, function($item) { return $item['total_rekam'] > 0 && $item['persentase'] < 100; }), 0, 5);
            ?>

            <!-- Widget Statistik -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                <div style="background: white; border-radius: 16px; padding: 1.5rem; border: 1px solid #e0e7ff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border-top: 4px solid #6366f1;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: #e0e7ff; color: #4f46e5; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="bar-chart-2"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b;">Kesimpulan Kehadiran</h3>
                    </div>
                    <div style="font-size: 2.5rem; font-weight: 800; color: #4f46e5; margin-bottom: 5px;"><?php echo $avg_persentase; ?>%</div>
                    <p style="margin: 0; color: #64748b; font-size: 0.85rem; line-height: 1.5;">Rata-rata persentase kehadiran siswa pada rentang waktu ini. <?php echo $avg_persentase >= 90 ? '<span style="color:#10b981; font-weight:700;">Sangat Baik!</span>' : ($avg_persentase >= 75 ? '<span style="color:#f59e0b; font-weight:700;">Cukup Baik.</span>' : '<span style="color:#ef4444; font-weight:700;">Perlu Perhatian.</span>'); ?></p>
                </div>

                <div style="background: white; border-radius: 16px; padding: 1.5rem; border: 1px solid #fce7f3; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border-top: 4px solid #ec4899;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: #fce7f3; color: #db2777; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="award"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b;">Top 5 Siswa Paling Rajin 🌟</h3>
                    </div>
                    <?php if(empty($top_5)): ?>
                        <div style="color: #94a3b8; font-size: 0.9rem;">Belum ada data kehadiran.</div>
                    <?php else: ?>
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <?php foreach($top_5 as $idx => $t): ?>
                                <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px dashed #f1f5f9;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 24px; height: 24px; border-radius: 50%; background: <?php echo $idx==0 ? '#fef08a' : '#f1f5f9'; ?>; color: <?php echo $idx==0 ? '#ca8a04' : '#64748b'; ?>; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800;"><?php echo $idx+1; ?></div>
                                        <div style="font-size: 0.9rem; font-weight: 600; color: #334155;"><?php echo htmlspecialchars($t['nama']); ?> <span style="font-size: 0.75rem; color: #94a3b8; font-weight: normal;">(<?php echo htmlspecialchars($t['nama_kelas']); ?>)</span></div>
                                    </div>
                                    <div style="font-size: 0.9rem; font-weight: 800; color: #059669;"><?php echo $t['persentase']; ?>%</div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div style="background: white; border-radius: 16px; padding: 1.5rem; border: 1px solid #fee2e2; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border-top: 4px solid #ef4444;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="alert-triangle"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b;">5 Terbawah (Perlu Perhatian) ⚠️</h3>
                    </div>
                    <?php if(empty($bottom_5)): ?>
                        <div style="color: #94a3b8; font-size: 0.9rem;">Belum ada data siswa yang sering absen.</div>
                    <?php else: ?>
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <?php foreach($bottom_5 as $idx => $t): ?>
                                <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px dashed #f1f5f9;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 24px; height: 24px; border-radius: 50%; background: #fef2f2; color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800;"><?php echo $idx+1; ?></div>
                                        <div style="font-size: 0.9rem; font-weight: 600; color: #334155;"><?php echo htmlspecialchars($t['nama']); ?> <span style="font-size: 0.75rem; color: #94a3b8; font-weight: normal;">(<?php echo htmlspecialchars($t['nama_kelas']); ?>)</span></div>
                                    </div>
                                    <div style="font-size: 0.9rem; font-weight: 800; color: #dc2626;"><?php echo $t['persentase']; ?>%</div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div style="background:white; border-radius:16px; padding:1.5rem; border:1px solid #f1f5f9; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                    <form method="GET" style="display:flex; gap:10px;">
                        <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none;" required>
                        <span style="display:flex; align-items:center; color:#64748b; font-weight:600;">s/d</span>
                        <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none;" required>
                        <select name="kelas_id" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none;">
                            <option value="">-- Semua Kelas --</option>
                            <?php foreach($kelasList as $k): ?>
                            <option value="<?php echo $k['id']; ?>" <?php echo $kelas_id == $k['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($k['nama_kelas']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" style="padding:8px 16px; background:#0ea5e9; color:white; border:none; border-radius:8px; font-weight:700; cursor:pointer;">Filter</button>
                        <a href="?start_date=<?php echo urlencode($start_date); ?>&end_date=<?php echo urlencode($end_date); ?>&kelas_id=<?php echo urlencode($kelas_id); ?>&cetak=1" target="_blank" style="padding:8px 16px; background:#10b981; color:white; border:none; border-radius:8px; font-weight:700; cursor:pointer; text-decoration:none; display:flex; align-items:center; gap:6px;">
                            <i data-lucide="printer" style="width:18px; height:18px;"></i> Cetak
                        </a>
                        <a href="?start_date=<?php echo urlencode($start_date); ?>&end_date=<?php echo urlencode($end_date); ?>&kelas_id=<?php echo urlencode($kelas_id); ?>&cetak_detail=1" target="_blank" style="padding:8px 16px; background:#f59e0b; color:white; border:none; border-radius:8px; font-weight:700; cursor:pointer; text-decoration:none; display:flex; align-items:center; gap:6px;">
                            <i data-lucide="printer" style="width:18px; height:18px;"></i> Cetak Detail
                        </a>
                    </form>
                </div>

                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; min-width:800px;">
                        <thead>
                            <tr style="background:#f8fafc; border-bottom:2px solid #e2e8f0; text-align:left;">
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase;">No</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase;">NIS</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase;">Nama Siswa</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase;">Kelas</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase; text-align:center;">Hadir</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase; text-align:center;">Sakit</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase; text-align:center;">Izin</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase; text-align:center;">Alpa</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase; text-align:center;">Bolos</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase; text-align:center;">Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rekap)): ?>
                            <tr>
                                <td colspan="9" style="padding:24px; text-align:center; color:#94a3b8; font-weight:600;">Data siswa tidak ditemukan.</td>
                            </tr>
                            <?php else: ?>
                                <?php 
                                $no=1; 
                                foreach($rekap as $r): 
                                    $hadir_val = $r['rekap']['Hadir'] + $r['rekap']['Terlambat'];
                                    $tot_val = $hadir_val + $r['rekap']['Sakit'] + $r['rekap']['Izin'] + $r['rekap']['Alpa'] + $r['rekap']['Bolos'];
                                    $pct_val = $tot_val > 0 ? round(($hadir_val / $tot_val) * 100, 1) : 0;
                                ?>
                                <tr style="border-bottom:1px solid #f1f5f9;">
                                    <td style="padding:12px 16px; color:#475569;"><?php echo $no++; ?></td>
                                    <td style="padding:12px 16px; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($r['nis']); ?></td>
                                    <td style="padding:12px 16px; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($r['nama']); ?></td>
                                    <td style="padding:12px 16px; color:#475569;"><?php echo htmlspecialchars($r['nama_kelas']); ?></td>
                                    <td style="padding:12px 16px; text-align:center; font-weight:700; color:#15803d; background:#f0fdf4;"><?php echo $hadir_val; ?></td>
                                    <td style="padding:12px 16px; text-align:center; font-weight:700; color:#ca8a04; background:#fefce8;"><?php echo $r['rekap']['Sakit']; ?></td>
                                    <td style="padding:12px 16px; text-align:center; font-weight:700; color:#1d4ed8; background:#eff6ff;"><?php echo $r['rekap']['Izin']; ?></td>
                                    <td style="padding:12px 16px; text-align:center; font-weight:700; color:#b91c1c; background:#fef2f2;"><?php echo $r['rekap']['Alpa']; ?></td>
                                    <td style="padding:12px 16px; text-align:center; font-weight:700; color:#7f1d1d; background:#fee2e2;"><?php echo $r['rekap']['Bolos']; ?></td>
                                    <td style="padding:12px 16px; text-align:center; font-weight:800; color:<?php echo $pct_val >= 90 ? '#059669' : ($pct_val >= 75 ? '#d97706' : '#dc2626'); ?>;"><?php echo $pct_val; ?>%</td>
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
<script>
    lucide.createIcons();
    function zToggleSidebar() {
        document.getElementById('zSidebar').classList.toggle('active');
    }
</script>

