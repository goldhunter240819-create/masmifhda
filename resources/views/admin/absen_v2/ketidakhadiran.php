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
                    <span style="font-weight:700;color:#1e293b;">Data Ketidakhadiran</span>
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
                        <i data-lucide="alert-triangle" style="color: rgba(255,255,255,0.8);"></i> Data Ketidakhadiran
                    </h1>
                    <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Pantau daftar siswa yang berhalangan hadir pada hari ini.</p>
                </div>
            </div>

            <div style="background:white; border-radius:16px; padding:1.5rem; border:1px solid #f1f5f9; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                    <form method="GET" style="display:flex; gap:10px;">
                        <input type="date" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none;">
                        <button type="submit" style="padding:8px 16px; background:#1e293b; color:white; border:none; border-radius:8px; font-weight:700; cursor:pointer;">Filter Tanggal</button>
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
                                <th style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:700; text-transform:uppercase;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($absensi)): ?>
                            <tr>
                                <td colspan="5" style="padding:24px; text-align:center; color:#94a3b8; font-weight:600;">Tidak ada catatan ketidakhadiran (Sakit/Izin/Alpa) pada tanggal ini.</td>
                            </tr>
                            <?php else: ?>
                                <?php $no=1; foreach($absensi as $a): ?>
                                <tr style="border-bottom:1px solid #f1f5f9;">
                                    <td style="padding:12px 16px; color:#475569;"><?php echo $no++; ?></td>
                                    <td style="padding:12px 16px; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($a['nis']); ?></td>
                                    <td style="padding:12px 16px; font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($a['nama']); ?></td>
                                    <td style="padding:12px 16px; color:#475569;"><?php echo htmlspecialchars($a['nama_kelas']); ?></td>
                                    <td style="padding:12px 16px;">
                                        <?php if ($a['status'] == 'Sakit'): ?>
                                            <span style="background:#fefce8; color:#ca8a04; padding:4px 10px; border-radius:100px; font-size:0.75rem; font-weight:700;">Sakit</span>
                                        <?php elseif ($a['status'] == 'Izin'): ?>
                                            <span style="background:#eff6ff; color:#1d4ed8; padding:4px 10px; border-radius:100px; font-size:0.75rem; font-weight:700;">Izin</span>
                                        <?php elseif ($a['status'] == 'Alpa'): ?>
                                            <span style="background:#fef2f2; color:#b91c1c; padding:4px 10px; border-radius:100px; font-size:0.75rem; font-weight:700;">Alpa</span>
                                        <?php else: ?>
                                            <span style="background:#f1f5f9; color:#475569; padding:4px 10px; border-radius:100px; font-size:0.75rem; font-weight:700;"><?php echo htmlspecialchars($a['status']); ?></span>
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
<script>
    lucide.createIcons();
    function zToggleSidebar() {
        document.getElementById('zSidebar').classList.toggle('active');
    }
</script>

