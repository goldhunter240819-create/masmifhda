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
            <div style="background:white; border-radius:16px; padding:1.5rem; border:1px solid #f1f5f9; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                    <form method="GET" style="display:flex; gap:10px; align-items: center; flex-wrap: wrap; width: 100%;">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <i data-lucide="calendar" style="width:16px; height:16px; color:#64748b;"></i>
                            <span style="color:#64748b; font-size:0.85rem; font-weight:600;">Bulan:</span>
                        </div>
                        <select name="bulan" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-size:0.85rem; font-weight:600;">
                            <?php 
                            $bulans = ['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'];
                            foreach($bulans as $num => $namaBulan): ?>
                                <option value="<?php echo $num; ?>" <?php echo $bulan == $num ? 'selected' : ''; ?>><?php echo $namaBulan; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span style="color:#64748b; font-size:0.85rem; margin-left: 10px; font-weight:600;">Tahun:</span>
                        <select name="tahun" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-size:0.85rem; font-weight:600;">
                            <?php for($y=date('Y'); $y>=2024; $y--): ?>
                                <option value="<?php echo $y; ?>" <?php echo $tahun == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                            <?php endfor; ?>
                        </select>
                        
                        <span style="color:#64748b; font-size:0.85rem; margin-left: 10px; font-weight:600;">Jabatan:</span>
                        <select name="jabatan" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-size:0.85rem; min-width: 150px; font-weight:600;">
                            <option value="">Semua Jabatan</option>
                            <?php foreach($jabatanList ?? [] as $jab): ?>
                                <option value="<?php echo htmlspecialchars($jab); ?>" <?php echo (isset($jabatan_filter) && $jabatan_filter == $jab) ? 'selected' : ''; ?>><?php echo htmlspecialchars($jab); ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                        <span style="color:#64748b; font-size:0.85rem; margin-left: 10px; font-weight:600;">Guru:</span>
                        <select name="guru" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-size:0.85rem; min-width: 150px; font-weight:600;">
                            <option value="">Semua Guru</option>
                            <?php foreach($allGuru ?? [] as $gItem): ?>
                                <option value="<?php echo $gItem['id']; ?>" <?php echo (isset($guru_filter) && $guru_filter == $gItem['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($gItem['nama']); ?></option>
                            <?php endforeach; ?>
                        </select>

                        <button type="submit" style="padding:8px 16px; background:#3b82f6; color:white; border:none; border-radius:8px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:5px; font-size:0.85rem; margin-left: auto;">
                            <i data-lucide="filter" style="width:14px; height:14px;"></i> Tampilkan
                        </button>
                        
                        <button type="submit" name="cetak" value="1" formtarget="_blank" style="padding:8px 16px; background:white; color:#1e293b; border:1px solid #cbd5e1; border-radius:8px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:5px; font-size:0.85rem;">
                            <i data-lucide="printer" style="width:14px; height:14px;"></i> Cetak Laporan
                        </button>
                    </form>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding: 12px 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; color: #3b82f6; font-size:0.95rem;">
                        <i data-lucide="layout-list" style="width: 18px; height: 18px;"></i>
                        Rekap Absensi Guru &mdash; <?php echo $bulans[$bulan]; ?> <?php echo $tahun; ?>
                    </div>
                    <div style="color: #3b82f6; font-weight: 700; font-size: 0.9rem;">
                        <?php echo count($rekap); ?> Guru
                    </div>
                </div>

                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; min-width:900px;">
                        <thead>
                            <tr style="background:#f8fafc; border-bottom:2px solid #e2e8f0; text-align:left;">
                                <th style="padding:12px 16px; color:#64748b; font-size:0.75rem; font-weight:800; text-transform:uppercase;">No</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.75rem; font-weight:800; text-transform:uppercase;">Nama Guru</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.75rem; font-weight:800; text-transform:uppercase;">Jabatan</th>
                                <th style="padding:12px 16px; color:#16a34a; font-size:0.75rem; font-weight:800; text-transform:uppercase; text-align:center;">Hadir</th>
                                <th style="padding:12px 16px; color:#d97706; font-size:0.75rem; font-weight:800; text-transform:uppercase; text-align:center;">Terlambat</th>
                                <th style="padding:12px 16px; color:#2563eb; font-size:0.75rem; font-weight:800; text-transform:uppercase; text-align:center;">Sakit</th>
                                <th style="padding:12px 16px; color:#9333ea; font-size:0.75rem; font-weight:800; text-transform:uppercase; text-align:center;">Izin</th>
                                <th style="padding:12px 16px; color:#dc2626; font-size:0.75rem; font-weight:800; text-transform:uppercase; text-align:center;">Alpa</th>
                                <th style="padding:12px 16px; color:#475569; font-size:0.75rem; font-weight:800; text-transform:uppercase; text-align:center;">Total</th>
                                <th style="padding:12px 16px; color:#64748b; font-size:0.75rem; font-weight:800; text-transform:uppercase; text-align:center;">Aksi</th>
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
                                <tr style="border-bottom:1px solid #f1f5f9; font-size: 0.85rem;">
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
                                        <a href="?bulan=<?php echo $bulan; ?>&tahun=<?php echo $tahun; ?>&cetak_detail=1&guru_id=<?php echo $r['id']; ?>" target="_blank" style="display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; background:#eff6ff; color:#3b82f6; border-radius:10px; transition:all 0.2s; border:1px solid #bfdbfe; cursor:pointer; text-decoration:none;" title="Cetak Rekap Guru">
                                            <i data-lucide="printer" style="width:18px; height:18px;"></i>
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
<script>
    lucide.createIcons();
    function zToggleSidebar() {
        document.getElementById('zSidebar').classList.toggle('active');
    }
</script>
