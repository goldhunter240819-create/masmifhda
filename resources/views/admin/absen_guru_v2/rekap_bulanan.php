<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="z-main">
        <header class="z-header" style="height: 80px; padding: 0 2rem; background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100;">
            <div class="z-header-left" style="display:flex;align-items:center;gap:15px;">
                <button class="z-hamburger" onclick="zToggleSidebar()" style="background:none;border:none;cursor:pointer;color:#64748b;">
                    <i data-lucide="menu"></i>
                </button>
                <div class="header-breadcrumb" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;">
                    <span style="font-weight:800;letter-spacing:1px;font-size:0.8rem;color:#0ea5e9;">ADMIN PORTAL</span>
                    <i data-lucide="chevron-right" style="width:14px;color:#cbd5e1;"></i>
                    <span style="font-weight:700;color:#1e293b;">Rekap Bulanan Absensi Guru</span>
                </div>
            </div>
            <div class="z-header-right" style="display:flex;align-items:center;gap:1.5rem;">
                <div style="display:flex;align-items:center;gap:8px;padding:6px 16px;border-radius:100px;border:1px solid #bbf7d0;background:#f0fdf4;color:#16a34a;font-size:0.75rem;font-weight:700;">
                    <div style="width:6px;height:6px;background:#16a34a;border-radius:50%;"></div> System Online
                </div>
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
                    <div class="z-avatar-premium" style="width:36px;height:36px;color:white;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;background:linear-gradient(135deg, #10b981, #059669);box-shadow:0 4px 12px rgba(16,185,129,0.2);"><?php echo $usr_initial; ?></div>
                </div>
                
                <!-- LOGOUT BUTTON -->
                <a href="<?php echo base_url('logout'); ?>" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:10px;background:#fef2f2;color:#ef4444;text-decoration:none;transition:0.2s;border:1px solid #fee2e2;">
                    <i data-lucide="log-out" style="width:18px;height:18px;"></i>
                </a>
            </div>
        </header>

        <div class="z-scroll" style="padding: 2rem;">
            
            <?php 
            $bulans = ['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'];
            $namaBulan = $bulans[$bulan] ?? $bulan;
            ?>

            <!-- Flash Message Banner -->
            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'generate_success'): ?>
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-left: 5px solid #10b981; border-radius: 12px; padding: 1.1rem 1.3rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03);">
                    <i data-lucide="check-circle-2" style="width: 24px; height: 24px; color: #16a34a; flex-shrink: 0;"></i>
                    <div style="font-size: 0.9rem; color: #166534; font-weight: 600;">
                        <strong>Alhamdulillah Berhasil!</strong> Seluruh absensi guru untuk bulan <strong><?php echo $namaBulan; ?> <?php echo $tahun; ?></strong> berhasil di-generate secara otomatis sebanyak <strong><?php echo intval($_GET['total'] ?? 0); ?> data kehadiran</strong> (<?php echo intval($_GET['hari'] ?? 0); ?> hari aktif KBM).
                    </div>
                </div>
            <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'auto_alpa_success'): ?>
                <div style="background: #fef2f2; border: 1px solid #fecaca; border-left: 5px solid #ef4444; border-radius: 12px; padding: 1.1rem 1.3rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03);">
                    <i data-lucide="alert-octagon" style="width: 24px; height: 24px; color: #dc2626; flex-shrink: 0;"></i>
                    <div style="font-size: 0.9rem; color: #991b1b; font-weight: 600;">
                        <strong>Auto-Alpa Berhasil!</strong> Sebanyak <strong><?php echo intval($_GET['count'] ?? 0); ?> status ALPA</strong> berhasil ditandai di database untuk guru yang terjadwal hadir tetapi kosong pada bulan <?php echo $namaBulan; ?> <?php echo $tahun; ?>. (Guru yang tidak ada jam mengajar tetap aman).
                    </div>
                </div>
            <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'error'): ?>
                <div style="background: #fef2f2; border: 1px solid #fecaca; border-left: 5px solid #ef4444; border-radius: 12px; padding: 1.1rem 1.3rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px;">
                    <i data-lucide="alert-circle" style="width: 24px; height: 24px; color: #dc2626; flex-shrink: 0;"></i>
                    <div style="font-size: 0.9rem; color: #991b1b; font-weight: 600;">
                        <strong>Terjadi Kendala:</strong> <?php echo htmlspecialchars($_GET['err'] ?? 'Gagal memproses data.'); ?>
                    </div>
                </div>
            <?php endif; ?>

            <div style="background:white; border-radius:16px; padding:1.5rem; border:1px solid #f1f5f9; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                    <form method="GET" action="/admin/absen_guru_v2/rekap-bulanan" id="formRekapBulanan" style="display:flex; gap:10px; align-items: center; flex-wrap: wrap; width: 100%;">
                        <?php
                        $prev_b_num = (int)$bulan - 1;
                        $prev_y_num = (int)$tahun;
                        if ($prev_b_num < 1) { $prev_b_num = 12; $prev_y_num--; }
                        $prev_b_str = sprintf('%02d', $prev_b_num);

                        $next_b_num = (int)$bulan + 1;
                        $next_y_num = (int)$tahun;
                        if ($next_b_num > 12) { $next_b_num = 1; $next_y_num++; }
                        $next_b_str = sprintf('%02d', $next_b_num);
                        ?>

                        <!-- Tombol Geser Bulan Sebelumnya -->
                        <a href="?bulan=<?= $prev_b_str ?>&tahun=<?= $prev_y_num ?>&jabatan=<?= urlencode($jabatan_filter ?? '') ?>&guru=<?= urlencode($guru_filter ?? '') ?>" style="width:34px; height:34px; border:1px solid #cbd5e1; border-radius:8px; background:white; display:inline-flex; align-items:center; justify-content:center; color:#475569; text-decoration:none;" title="Geser ke Bulan Sebelumnya">
                            <i data-lucide="chevron-left" style="width:16px; height:16px;"></i>
                        </a>

                        <div style="display:flex; align-items:center; gap:6px;">
                            <i data-lucide="calendar" style="width:16px; height:16px; color:#64748b;"></i>
                            <span style="color:#64748b; font-size:0.83rem; font-weight:600;">Bulan:</span>
                        </div>
                        <select name="bulan" id="selBulan" onchange="this.form.submit()" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-size:0.85rem; font-weight:600; cursor:pointer;">
                            <?php foreach($bulans as $num => $nb): ?>
                                <option value="<?php echo $num; ?>" <?php echo $bulan == $num ? 'selected' : ''; ?>><?php echo $nb; ?></option>
                            <?php endforeach; ?>
                        </select>

                        <span style="color:#64748b; font-size:0.83rem; margin-left: 2px; font-weight:600;">Tahun:</span>
                        <select name="tahun" id="selTahun" onchange="this.form.submit()" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-size:0.85rem; font-weight:600; cursor:pointer;">
                            <?php for($y=date('Y'); $y>=2024; $y--): ?>
                                <option value="<?php echo $y; ?>" <?php echo $tahun == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                            <?php endfor; ?>
                        </select>

                        <!-- Tombol Geser Bulan Berikutnya -->
                        <a href="?bulan=<?= $next_b_str ?>&tahun=<?= $next_y_num ?>&jabatan=<?= urlencode($jabatan_filter ?? '') ?>&guru=<?= urlencode($guru_filter ?? '') ?>" style="width:34px; height:34px; border:1px solid #cbd5e1; border-radius:8px; background:white; display:inline-flex; align-items:center; justify-content:center; color:#475569; text-decoration:none;" title="Geser ke Bulan Berikutnya">
                            <i data-lucide="chevron-right" style="width:16px; height:16px;"></i>
                        </a>
                        
                        <span style="color:#64748b; font-size:0.83rem; margin-left: 6px; font-weight:600;">Jabatan:</span>
                        <select name="jabatan" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-size:0.85rem; min-width: 140px; font-weight:600;">
                            <option value="">Semua Jabatan</option>
                            <?php foreach($jabatanList ?? [] as $jab): ?>
                                <option value="<?php echo htmlspecialchars($jab); ?>" <?php echo (isset($jabatan_filter) && $jabatan_filter == $jab) ? 'selected' : ''; ?>><?php echo htmlspecialchars($jab); ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                        <span style="color:#64748b; font-size:0.83rem; margin-left: 6px; font-weight:600;">Guru:</span>
                        <select name="guru" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-size:0.85rem; min-width: 140px; font-weight:600;">
                            <option value="">Semua Guru</option>
                            <?php foreach($allGuru ?? [] as $gItem): ?>
                                <option value="<?php echo $gItem['id']; ?>" <?php echo (isset($guru_filter) && $guru_filter == $gItem['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($gItem['nama']); ?></option>
                            <?php endforeach; ?>
                        </select>

                        <!-- Action Buttons Group -->
                        <div style="display:flex; align-items:center; gap:8px; margin-left:auto; flex-wrap:wrap;">
                            <!-- Tombol Tampilkan -->
                            <button type="submit" style="padding:8px 14px; background:#0ea5e9; color:white; border:none; border-radius:8px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:5px; font-size:0.83rem; transition:0.2s;">
                                <i data-lucide="filter" style="width:14px; height:14px;"></i> Tampilkan
                            </button>
                            
                            <!-- Tombol Cetak Ringkas (Portrait) -->
                            <button type="submit" name="cetak" value="1" formtarget="_blank" style="padding:8px 14px; background:white; color:#1e293b; border:1.5px solid #cbd5e1; border-radius:8px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:5px; font-size:0.83rem; transition:0.2s;" title="Cetak Rekap Ringkasan Total Bulanan">
                                <i data-lucide="printer" style="width:14px; height:14px;"></i> Cetak Ringkas
                            </button>

                            <!-- Tombol Cetak Detail Global 1-31 Matriks (Landscape) -->
                            <button type="submit" name="cetak_global" value="1" formtarget="_blank" style="padding:8px 14px; background:linear-gradient(135deg, #10b981 0%, #059669 100%); color:white; border:none; border-radius:8px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:5px; font-size:0.83rem; box-shadow:0 2px 6px rgba(16,185,129,0.25); transition:0.2s;" title="Cetak Matriks Harian Tanggal 1 s.d. 31 Seluruh Guru (Kayak Siswa)">
                                <i data-lucide="calendar-days" style="width:14px; height:14px;"></i> Cetak Detail Global (1-31)
                            </button>

                            <!-- Tombol Auto Alpa Yang Kosong Bulan Ini -->
                            <button type="button" onclick="confirmAutoAlpaBulan()" style="padding:8px 14px; background:linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color:white; border:none; border-radius:8px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:5px; font-size:0.83rem; box-shadow:0 2px 6px rgba(239,68,68,0.25); transition:0.2s;" title="Tandai Alpa otomatis di database untuk guru yang terjadwal hadir tetapi kosong">
                                <i data-lucide="user-x" style="width:14px; height:14px;"></i> Auto Alpa Kosong
                            </button>

                            <!-- Tombol Hadirkan 1 Bulan Khusus Guru Sertifikasi -->
                            <button type="button" onclick="generateBulanDirect('sertifikasi', 'Khusus Semua Guru Sertifikasi')" style="padding:8px 14px; background:linear-gradient(135deg, #f59e0b 0%, #b45309 100%); color:white; border:none; border-radius:8px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:5px; font-size:0.83rem; box-shadow:0 2px 6px rgba(245,158,11,0.25); transition:0.2s;" title="Generate Kehadiran 1 Bulan Penuh untuk Guru Sertifikasi Saja">
                                <i data-lucide="award" style="width:14px; height:14px;"></i> 1 Bulan Sertifikasi
                            </button>

                            <!-- Tombol Hadirkan 1 Bulan Semua Guru Penuh -->
                            <button type="button" onclick="generateBulanDirect('semua', 'Semua Dewan Guru Madrasah')" style="padding:8px 14px; background:linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color:white; border:none; border-radius:8px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:5px; font-size:0.83rem; box-shadow:0 2px 6px rgba(59,130,246,0.25); transition:0.2s;" title="Generate Kehadiran 1 Bulan Penuh untuk Semua Dewan Guru">
                                <i data-lucide="users" style="width:14px; height:14px;"></i> 1 Bulan Semua Guru
                            </button>

                            <!-- Tombol Modal Generate 1 Bulan (Opsi Lengkap) -->
                            <button type="button" onclick="openModalGenerateBulan()" style="padding:8px 14px; background:linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color:white; border:none; border-radius:8px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:5px; font-size:0.83rem; box-shadow:0 2px 6px rgba(139,92,246,0.25); transition:0.2s;" title="Pilihan mode kombinasi, jam, atau opsi alpa 1 bulan">
                                <i data-lucide="calendar-days" style="width:14px; height:14px;"></i> Opsi Bulan
                            </button>
                        </div>
                    </form>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding: 12px 20px; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;">
                    <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; color: #0284c7; font-size:0.95rem;">
                        <i data-lucide="layout-list" style="width: 18px; height: 18px;"></i>
                        Rekap Absensi Guru &mdash; <?php echo $namaBulan; ?> <?php echo $tahun; ?>
                    </div>
                    <div style="display:flex; align-items:center; gap:12px;">
                        <span style="font-size:0.8rem; color:#64748b;">Standar Kehadiran: Guru Sertifikasi (Hadir Setiap Hari), Non-Sertifikasi (Sesuai Jam)</span>
                        <div style="background:#eff6ff; color:#2563eb; padding:4px 12px; border-radius:100px; font-weight: 800; font-size: 0.85rem; border:1px solid #bfdbfe;">
                            <?php echo count($rekap); ?> Guru Terdaftar
                        </div>
                    </div>
                </div>

                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; min-width:900px;">
                        <thead>
                            <tr style="background:#f8fafc; border-bottom:2px solid #e2e8f0; text-align:left;">
                                <th style="padding:12px 16px; color:#64748b; font-size:0.75rem; font-weight:800; text-transform:uppercase; width:45px;">No</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.75rem; font-weight:800; text-transform:uppercase;">Nama Guru</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.75rem; font-weight:800; text-transform:uppercase;">Jabatan</th>
                                <th style="padding:12px 16px; color:#16a34a; font-size:0.75rem; font-weight:800; text-transform:uppercase; text-align:center;">Hadir</th>
                                <th style="padding:12px 16px; color:#d97706; font-size:0.75rem; font-weight:800; text-transform:uppercase; text-align:center;">Terlambat</th>
                                <th style="padding:12px 16px; color:#2563eb; font-size:0.75rem; font-weight:800; text-transform:uppercase; text-align:center;">Sakit</th>
                                <th style="padding:12px 16px; color:#9333ea; font-size:0.75rem; font-weight:800; text-transform:uppercase; text-align:center;">Izin</th>
                                <th style="padding:12px 16px; color:#dc2626; font-size:0.75rem; font-weight:800; text-transform:uppercase; text-align:center;">Alpa</th>
                                <th style="padding:12px 16px; color:#475569; font-size:0.75rem; font-weight:800; text-transform:uppercase; text-align:center;">Total</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.75rem; font-weight:800; text-transform:uppercase; text-align:center; width:90px;">Cetak 1 Guru</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rekap)): ?>
                            <tr>
                                <td colspan="10" style="padding:24px; text-align:center; color:#94a3b8; font-weight:600;">Data Guru tidak ditemukan.</td>
                            </tr>
                            <?php else: ?>
                                <?php $no=1; foreach($rekap as $r): 
                                    $hadir = $r['rekap']['Hadir'] ?? 0;
                                    $terlambat = $r['rekap']['Terlambat'] ?? 0;
                                    $sakit = $r['rekap']['Sakit'] ?? 0;
                                    $izin = $r['rekap']['Izin'] ?? 0;
                                    $alpa = $r['rekap']['Alpa'] ?? 0;
                                    $total = $hadir + $terlambat + $sakit + $izin + $alpa;
                                ?>
                                <tr style="border-bottom:1px solid #f1f5f9; font-size: 0.85rem;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                    <td style="padding:12px 16px; color:#475569; font-weight: 500;"><?php echo $no++; ?></td>
                                    <td style="padding:12px 16px; font-weight:700; color:#0f172a;"><?php echo htmlspecialchars($r['nama']); ?></td>
                                    <td style="padding:12px 16px; color:#64748b; font-weight: 500;"><?php echo htmlspecialchars($r['jabatan'] ?? 'Guru'); ?></td>
                                    
                                    <td style="padding:12px 16px; text-align:center; font-weight:800; color:#22c55e;"><?php echo $hadir; ?></td>
                                    <td style="padding:12px 16px; text-align:center; font-weight:800; color:#f59e0b;"><?php echo $terlambat; ?></td>
                                    <td style="padding:12px 16px; text-align:center; font-weight:800; color:#3b82f6;"><?php echo $sakit; ?></td>
                                    <td style="padding:12px 16px; text-align:center; font-weight:800; color:#a855f7;"><?php echo $izin; ?></td>
                                    <td style="padding:12px 16px; text-align:center; font-weight:800; color:#ef4444;"><?php echo $alpa; ?></td>
                                    <td style="padding:12px 16px; text-align:center; font-weight:800; color:#0f172a;"><?php echo $total; ?></td>
                                    
                                    <td style="padding:12px 16px; text-align:center;">
                                        <a href="?bulan=<?php echo $bulan; ?>&tahun=<?php echo $tahun; ?>&cetak_detail=1&guru_id=<?php echo $r['id']; ?>" target="_blank" style="display:inline-flex; align-items:center; justify-content:center; gap:4px; padding:6px 10px; background:#eff6ff; color:#3b82f6; border-radius:8px; transition:all 0.2s; border:1px solid #bfdbfe; cursor:pointer; text-decoration:none; font-size:0.75rem; font-weight:700;" title="Cetak Riwayat Absensi 1 Bulan untuk <?php echo htmlspecialchars($r['nama']); ?>">
                                            <i data-lucide="printer" style="width:14px; height:14px;"></i> Cetak
                                        </a>
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

<!-- Form Auto Alpa Bulan Ini -->
<form id="formAutoAlpaBulan" method="POST" action="/admin/absen_guru_v2/auto-alpa" style="display:none;">
    <input type="hidden" name="bulan" id="alpaBulan" value="<?php echo htmlspecialchars($bulan); ?>">
    <input type="hidden" name="tahun" id="alpaTahun" value="<?php echo htmlspecialchars($tahun); ?>">
</form>

<!-- Modal Generate 1 Bulan Penuh -->
<div id="modalGenBulan" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:15px;">
    <div style="background:white; border-radius:18px; width:100%; max-width:540px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.2); overflow:hidden; animation:zoomIn 0.2s ease;">
        <div style="padding:18px 24px; background:linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color:white; display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:36px; height:36px; border-radius:10px; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center;">
                    <i data-lucide="sparkles" style="width:20px; height:20px;"></i>
                </div>
                <div>
                    <h3 style="margin:0; font-size:1.1rem; font-weight:800;">Generate Hadir 1 Bulan Penuh</h3>
                    <p style="margin:0; font-size:0.78rem; opacity:0.9;">Otomatisasi kehadiran guru bulanan madrasah</p>
                </div>
            </div>
            <button type="button" onclick="closeModalGenerateBulan()" style="background:none; border:none; color:white; font-size:1.4rem; cursor:pointer; line-height:1;">&times;</button>
        </div>

        <form method="POST" action="/admin/absen_guru_v2/generate-bulan" style="padding:22px 24px;">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
                <div>
                    <label style="display:block; font-size:0.78rem; font-weight:700; color:#475569; margin-bottom:5px;">Bulan Target</label>
                    <select name="bulan" id="modalBulan" style="width:100%; padding:9px 12px; border:1.5px solid #cbd5e1; border-radius:10px; font-weight:600; font-size:0.88rem; outline:none;">
                        <?php foreach($bulans as $num => $nb): ?>
                            <option value="<?php echo $num; ?>" <?php echo $bulan == $num ? 'selected' : ''; ?>><?php echo $nb; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:0.78rem; font-weight:700; color:#475569; margin-bottom:5px;">Tahun</label>
                    <select name="tahun" id="modalTahun" style="width:100%; padding:9px 12px; border:1.5px solid #cbd5e1; border-radius:10px; font-weight:600; font-size:0.88rem; outline:none;">
                        <?php for($y=date('Y'); $y>=2024; $y--): ?>
                            <option value="<?php echo $y; ?>" <?php echo $tahun == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:0.78rem; font-weight:700; color:#475569; margin-bottom:8px;">Pilih Mode Generate Kehadiran:</label>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <label style="display:flex; align-items:flex-start; gap:10px; padding:10px 12px; border:1.5px solid #6366f1; background:#f5f3ff; border-radius:10px; cursor:pointer;">
                        <input type="radio" name="mode" value="kombinasi" checked style="margin-top:3px; accent-color:#6366f1;">
                        <div>
                            <div style="font-weight:700; font-size:0.88rem; color:#4338ca;">⚡ Kombinasi Otomatis (Rekomendasi Madrasah)</div>
                            <div style="font-size:0.75rem; color:#6b7280; line-height:1.35;">Guru sertifikasi hadir setiap hari kerja (Senin-Sabtu), guru non-sertifikasi hadir saat ada jam mengajar.</div>
                        </div>
                    </label>

                    <label style="display:flex; align-items:flex-start; gap:10px; padding:10px 12px; border:1.5px solid #e2e8f0; background:#ffffff; border-radius:10px; cursor:pointer;">
                        <input type="radio" name="mode" value="sertifikasi" style="margin-top:3px; accent-color:#f59e0b;">
                        <div>
                            <div style="font-weight:700; font-size:0.88rem; color:#b45309;">🏆 Khusus Semua Guru Sertifikasi</div>
                            <div style="font-size:0.75rem; color:#6b7280; line-height:1.35;">Hanya guru bersertifikasi yang dihadirkan setiap hari kerja sebulan penuh. Guru lain tidak disentuh.</div>
                        </div>
                    </label>

                    <label style="display:flex; align-items:flex-start; gap:10px; padding:10px 12px; border:1.5px solid #e2e8f0; background:#ffffff; border-radius:10px; cursor:pointer;">
                        <input type="radio" name="mode" value="jam" style="margin-top:3px; accent-color:#10b981;">
                        <div>
                            <div style="font-weight:700; font-size:0.88rem; color:#065f46;">📅 Guru Sesuai Jam Mengajar</div>
                            <div style="font-size:0.75rem; color:#6b7280; line-height:1.35;">Guru dihadirkan HANYA pada hari di mana mereka memiliki jadwal jam mengajar di hari tersebut.</div>
                        </div>
                    </label>

                    <label style="display:flex; align-items:flex-start; gap:10px; padding:10px 12px; border:1.5px solid #e2e8f0; background:#ffffff; border-radius:10px; cursor:pointer;">
                        <input type="radio" name="mode" value="semua" style="margin-top:3px; accent-color:#0ea5e9;">
                        <div>
                            <div style="font-weight:700; font-size:0.88rem; color:#0369a1;">👥 Semua Guru Madrasah (Penuh)</div>
                            <div style="font-size:0.75rem; color:#6b7280; line-height:1.35;">Seluruh guru madrasah dihadirkan setiap hari kerja aktif (Senin-Sabtu) sebulan penuh.</div>
                        </div>
                    </label>
                </div>
            </div>

            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:10px 12px; margin-bottom:18px;">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:0.8rem; font-weight:700; color:#334155;">
                    <input type="checkbox" name="isi_alpa" value="1" style="accent-color:#ef4444; width:16px; height:16px;">
                    <span>Tandai ALPA untuk guru yang terjadwal tapi kosong pada tanggal yang sudah lewat</span>
                </label>
                <div style="font-size:0.73rem; color:#64748b; margin-left:24px; margin-top:2px;">
                    *Guru yang TIDAK ADA JAM mengajar tetap aman (tidak dialpakan).
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="closeModalGenerateBulan()" style="padding:9px 16px; background:#f1f5f9; color:#475569; border:none; border-radius:10px; font-weight:700; font-size:0.83rem; cursor:pointer;">Batal</button>
                <button type="submit" style="padding:9px 20px; background:linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color:white; border:none; border-radius:10px; font-weight:700; font-size:0.83rem; cursor:pointer; display:inline-flex; align-items:center; gap:6px; box-shadow:0 3px 8px rgba(99,102,241,0.3);">
                    <i data-lucide="sparkles" style="width:14px; height:14px;"></i> Proses Generate
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    lucide.createIcons();
    function zToggleSidebar() {
        document.getElementById('zSidebar').classList.toggle('active');
    }

    function openModalGenerateBulan() {
        const b = document.getElementById('selBulan').value;
        const t = document.getElementById('selTahun').value;
        document.getElementById('modalBulan').value = b;
        document.getElementById('modalTahun').value = t;
        document.getElementById('modalGenBulan').style.display = 'flex';
        lucide.createIcons();
    }

    function closeModalGenerateBulan() {
        document.getElementById('modalGenBulan').style.display = 'none';
    }

    function generateBulanDirect(mode, modeLabel) {
        const b = document.getElementById('selBulan').value;
        const t = document.getElementById('selTahun').value;
        const bulanText = document.getElementById('selBulan').options[document.getElementById('selBulan').selectedIndex].text;
        
        const pesan = `Konfirmasi Generate Kehadiran 1 Bulan Penuh (${bulanText} ${t}):\n\n` +
                      `Target: ${modeLabel}\n` +
                      `Standar Waktu: Masuk 07:00-07:30 (acak), Pulang 13:15-13:30 (acak)\n` +
                      `Hari Minggu dan Kalender Libur otomatis dilewati.\n\n` +
                      `Lanjutkan proses otomatisasi kehadiran bulanan?`;
                      
        if (confirm(pesan)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/absen_guru_v2/generate-bulan';
            
            const inB = document.createElement('input'); inB.type = 'hidden'; inB.name = 'bulan'; inB.value = b; form.appendChild(inB);
            const inY = document.createElement('input'); inY.type = 'hidden'; inY.name = 'tahun'; inY.value = t; form.appendChild(inY);
            const inM = document.createElement('input'); inM.type = 'hidden'; inM.name = 'mode'; inM.value = mode; form.appendChild(inM);
            
            document.body.appendChild(form);
            form.submit();
        }
    }

    function confirmAutoAlpaBulan() {
        const b = document.getElementById('selBulan').value;
        const t = document.getElementById('selTahun').value;
        const bulanText = document.getElementById('selBulan').options[document.getElementById('selBulan').selectedIndex].text;
        
        const pesan = `Tandai status ALPA resmi di database untuk guru yang terjadwal hadir tetapi kosong pada bulan ${bulanText} ${t}?\n\n` +
                      `Aturan Auto-Alpa:\n` +
                      `• Guru Sertifikasi: Wajib hadir setiap hari kerja (Senin - Sabtu)\n` +
                      `• Guru Non-Sertifikasi: Wajib hadir saat ADA JAM MENGAJAR di hari tersebut\n` +
                      `• Guru yang TIDAK ADA JAM MENGAJAR TETAP AMAN (TIDAK DIALPAKAN)\n` +
                      `• Hanya berlaku untuk tanggal yang sudah lewat / hari ini\n\n` +
                      `Lanjutkan proses Auto-Alpa?`;

        if (confirm(pesan)) {
            document.getElementById('alpaBulan').value = b;
            document.getElementById('alpaTahun').value = t;
            document.getElementById('formAutoAlpaBulan').submit();
        }
    }
</script>
