<div class="siakad-container">
    <?php 
    include __DIR__ . '/sidebar.php'; 
    
    if (!function_exists('singkatMapel')) {
        function singkatMapel($nama) {
            $exceptions = ['TIK', 'PJOK', 'PKN', 'IPA', 'IPS'];
            if (in_array(strtoupper($nama), $exceptions)) return strtoupper($nama);
            $words = explode(' ', trim($nama));
            if (count($words) > 1) {
                $singkat = '';
                foreach($words as $w) {
                    if (strlen($w) > 0) $singkat .= strtoupper(substr($w,0,1));
                }
                return $singkat;
            }
            return strlen($nama) > 5 ? substr($nama, 0, 3) . '.' : $nama;
        }
    }

    if (!function_exists('singkatStatus')) {
        function singkatStatus($status) {
            if (empty($status) || $status == '-') return '-';
            if (strtolower($status) == 'terlambat') return 'T';
            return strtoupper(substr($status, 0, 1));
        }
    }
    ?>
    <div class="z-main">
        <header class="z-header" style="height: 80px; padding: 0 2rem; background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100;">
            <div class="z-header-left" style="display:flex;align-items:center;gap:15px;">
                <button class="z-hamburger" onclick="zToggleSidebar()" style="background:none;border:none;cursor:pointer;color:#64748b;">
                    <i data-lucide="menu"></i>
                </button>
                <div class="header-breadcrumb" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;">
                    <span style="font-weight:800;letter-spacing:1px;font-size:0.8rem;color:#0ea5e9;">ABSEN V2</span>
                    <i data-lucide="chevron-right" style="width:14px;color:#cbd5e1;"></i>
                    <span style="font-weight:700;color:#1e293b;">Rekap Detail (Timeline)</span>
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
                        <i data-lucide="git-merge" style="color: rgba(255,255,255,0.8);"></i> Jejak Absensi Siswa
                    </h1>
                    <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Lacak dan pantau jam berapa siswa melakukan absensi secara detail.</p>
                </div>
            </div>

            <div style="background:white; border-radius:16px; padding:1.5rem; border:1px solid #f1f5f9; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                    <form method="GET" style="display:flex; gap:10px;">
                        <input type="date" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none;">
                        <select name="kelas_id" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none;" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach($kelasList as $k): ?>
                            <option value="<?php echo $k['id']; ?>" <?php echo $kelas_id == $k['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($k['nama_kelas']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" style="padding:8px 16px; background:#0ea5e9; color:white; border:none; border-radius:8px; font-weight:700; cursor:pointer;">Filter</button>
                        <a href="?tanggal=<?php echo urlencode($tanggal); ?>&kelas_id=<?php echo urlencode($kelas_id); ?>&cetak=1" target="_blank" style="padding:8px 16px; background:#10b981; color:white; border:none; border-radius:8px; font-weight:700; cursor:pointer; text-decoration:none; display:flex; align-items:center; gap:6px;">
                            <i data-lucide="printer" style="width:18px; height:18px;"></i> Cetak
                        </a>
                    </form>
                </div>

                <!-- Info Legend Banner -->
                <div style="background:#f0f9ff; border:1px solid #bae6fd; border-radius:12px; padding:12px 16px; margin-bottom:1.25rem; font-size:0.82rem; color:#0369a1; display:flex; flex-wrap:wrap; gap:15px; align-items:center;">
                    <div style="display:flex; align-items:center; gap:6px; font-weight:800;">
                        <i data-lucide="info" style="width:16px; height:16px; color:#0ea5e9;"></i> Panduan Kolom Timeline:
                    </div>
                    <div style="display:flex; gap:12px; flex-wrap:wrap; font-weight:600;">
                        <span><strong style="color:#0f172a;">• Masuk / Pulang:</strong> Jam scan QR di gerbang (<em style="color:#64748b;">'-' jika tidak scan gerbang</em>)</span>
                        <span><strong style="color:#0f172a;">• Mapel:</strong> Presensi kelas dari Jurnal Mengajar Guru</span>
                        <span><strong style="color:#0f172a;">• Sholat:</strong> Presensi Sholat Dhuhur</span>
                        <span><strong style="color:#0f172a;">• Status Pusat:</strong> Status akhir kehadiran siswa</span>
                    </div>
                </div>

                <div style="overflow-x:auto; max-height:70vh; position:relative; border-radius:12px; border:1px solid #e2e8f0;">
                    <?php 
                        $hari_ini_rekap = date('w', strtotime($tanggal));
                        $show_sholat = !in_array($hari_ini_rekap, [5, 6]); 
                    ?>
                    <table style="width:100%; border-collapse:separate; border-spacing:0; min-width:1000px;">
                        <thead style="position:sticky; top:0; z-index:20; background:#f8fafc;">
                            <tr style="background:#f8fafc; text-align:left;">
                                <th rowspan="2" style="padding:12px 10px; color:#64748b; font-size:0.8rem; font-weight:800; text-transform:uppercase; border-bottom:1px solid #cbd5e1; border-right:1px solid #e2e8f0; vertical-align:middle; background:#f8fafc; position:sticky; top:0; z-index:20; width:1%;">No</th>
                                <th rowspan="2" style="padding:12px 16px; color:#64748b; font-size:0.8rem; font-weight:800; text-transform:uppercase; border-bottom:1px solid #cbd5e1; border-right:1px solid #e2e8f0; vertical-align:middle; background:#f8fafc; position:sticky; top:0; z-index:20; white-space:nowrap;">Nama Siswa</th>
                                <th colspan="<?php echo count($jadwal) + ($show_sholat ? 4 : 3); ?>" style="padding:10px 16px; color:#0ea5e9; font-size:0.8rem; font-weight:850; text-transform:uppercase; border-bottom:1px solid #cbd5e1; text-align:center; background:#f0f9ff; position:sticky; top:0; z-index:20;">
                                    TIMELINE KEHADIRAN (<?php echo date('d F Y', strtotime($tanggal)); ?>)
                                </th>
                            </tr>
                            <tr style="background:#f8fafc; text-align:center;">
                                <th style="padding:10px 8px; color:#475569; font-size:0.75rem; font-weight:800; border-bottom:2px solid #cbd5e1; border-right:1px solid #e2e8f0; background:#f8fafc; position:sticky; top:37px; z-index:20;">
                                    <i data-lucide="log-in" style="width:14px; height:14px; display:block; margin:0 auto 2px; color:#10b981;"></i> Masuk
                                </th>
                                <?php foreach($jadwal as $i => $j): ?>
                                    <th style="padding:10px 8px; color:#475569; font-size:0.75rem; font-weight:800; border-bottom:2px solid #cbd5e1; border-right:1px solid #e2e8f0; background:#f8fafc; position:sticky; top:37px; z-index:20; min-width:50px; text-align:center;" title="<?php echo htmlspecialchars($j['nama_mapel']); ?>">
                                        <?php echo htmlspecialchars($j['kode_mapel']); ?><br>
                                        <span style="font-weight:600; font-size:10px; color:#0ea5e9;">
                                            <?php 
                                            echo substr($j['jam_mulai'],0,5); 
                                            if (!empty($j['jam_selesai']) && substr($j['jam_selesai'],0,5) != substr($j['jam_mulai'],0,5)) {
                                                echo '-' . substr($j['jam_selesai'],0,5);
                                            }
                                            ?>
                                        </span>
                                    </th>
                                <?php endforeach; ?>
                                <?php if($show_sholat): ?>
                                <th style="padding:10px 8px; color:#475569; font-size:0.75rem; font-weight:800; border-bottom:2px solid #cbd5e1; border-right:1px solid #e2e8f0; background:#f8fafc; position:sticky; top:37px; z-index:20;">
                                    <i data-lucide="moon" style="width:14px; height:14px; display:block; margin:0 auto 2px; color:#8b5cf6;"></i> Sholat
                                </th>
                                <?php endif; ?>
                                <th style="padding:10px 8px; color:#475569; font-size:0.75rem; font-weight:800; border-bottom:2px solid #cbd5e1; border-right:1px solid #e2e8f0; background:#f8fafc; position:sticky; top:37px; z-index:20;">
                                    <i data-lucide="log-out" style="width:14px; height:14px; display:block; margin:0 auto 2px; color:#ef4444;"></i> Pulang
                                </th>
                                <th style="padding:10px 8px; color:#0f172a; font-size:0.75rem; font-weight:850; border-bottom:2px solid #cbd5e1; background:#e2e8f0; position:sticky; top:37px; z-index:20;">
                                    STATUS PUSAT
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rekap)): ?>
                            <tr>
                                <td colspan="<?php echo count($jadwal) + ($show_sholat ? 6 : 5); ?>" style="padding:30px; text-align:center; color:#94a3b8; font-weight:600; background:white;">Tidak ada data siswa ditemukan.</td>
                            </tr>
                            <?php else: ?>
                                <?php $no=1; foreach($rekap as $r): ?>
                                <tr style="background:white;">
                                    <td style="padding:10px; color:#475569; border-bottom:1px solid #f1f5f9; border-right:1px solid #f1f5f9; text-align:center; font-weight:700; width:1%;"><?php echo $no++; ?></td>
                                    <td style="padding:10px 16px; font-weight:700; color:#0f172a; border-bottom:1px solid #f1f5f9; border-right:1px solid #f1f5f9; white-space:nowrap;"><?php echo htmlspecialchars($r['nama']); ?></td>
                                    
                                    <!-- Gate Masuk -->
                                    <td style="padding:10px; text-align:center; font-size:0.85rem; border-bottom:1px solid #f1f5f9; border-right:1px solid #f1f5f9; color: <?php echo $r['timeline']['gate_masuk'] != '-' ? '#15803d' : '#94a3b8'; ?>; font-weight: <?php echo $r['timeline']['gate_masuk'] != '-' ? '800' : '500'; ?>;">
                                        <?php echo $r['timeline']['gate_masuk'] != '-' ? $r['timeline']['gate_masuk'] : '<span title="Siswa tidak scan QR di pintu gerbang">-</span>'; ?>
                                    </td>
                                    
                                    <!-- Mapels -->
                                    <?php foreach($r['timeline']['mapels'] as $m): ?>
                                    <td style="padding:10px; text-align:center; font-size:0.85rem; border-bottom:1px solid #f1f5f9; border-right:1px solid #f1f5f9; 
                                        <?php 
                                            if($m == 'Hadir') echo 'color:#15803d; font-weight:800; background:#f0fdf4;';
                                            elseif($m == 'Alpa') echo 'color:#ffffff; font-weight:800; background:#ef4444;';
                                            elseif($m == 'Sakit') echo 'color:#ffffff; font-weight:800; background:#eab308;';
                                            elseif($m == 'Izin') echo 'color:#ffffff; font-weight:800; background:#3b82f6;';
                                            else echo 'color:#94a3b8;';
                                        ?>
                                    " title="<?php echo htmlspecialchars($m); ?>">
                                        <?php echo singkatStatus($m); ?>
                                    </td>
                                    <?php endforeach; ?>
                                    
                                    <!-- Sholat -->
                                    <?php if($show_sholat): ?>
                                    <td style="padding:10px; text-align:center; font-size:0.85rem; border-bottom:1px solid #f1f5f9; border-right:1px solid #f1f5f9; color: <?php echo $r['timeline']['sholat'] != '-' ? '#15803d' : '#94a3b8'; ?>; font-weight: <?php echo $r['timeline']['sholat'] != '-' ? '800' : '500'; ?>;">
                                        <?php echo $r['timeline']['sholat'] != '-' ? $r['timeline']['sholat'] : '<span title="Belum/tidak scan presensi sholat">-</span>'; ?>
                                    </td>
                                    <?php endif; ?>
                                    
                                    <!-- Gate Pulang -->
                                    <td style="padding:10px; text-align:center; font-size:0.85rem; border-bottom:1px solid #f1f5f9; border-right:1px solid #f1f5f9; color: <?php echo $r['timeline']['gate_pulang'] != '-' ? '#15803d' : '#94a3b8'; ?>; font-weight: <?php echo $r['timeline']['gate_pulang'] != '-' ? '800' : '500'; ?>;">
                                        <?php echo $r['timeline']['gate_pulang'] != '-' ? $r['timeline']['gate_pulang'] : '<span title="Siswa tidak scan QR pulang di pintu gerbang">-</span>'; ?>
                                    </td>
                                    
                                    <!-- Status Pusat -->
                                    <td style="padding:10px; text-align:center; font-size:0.85rem; border-bottom:1px solid #f1f5f9; font-weight:850;
                                        <?php 
                                            if(in_array($r['timeline']['status_akhir'], ['Hadir','Terlambat'])) echo 'color:#15803d; background:#f0fdf4;';
                                            elseif(in_array($r['timeline']['status_akhir'], ['Alpa','Bolos'])) echo 'color:#ffffff; background:#ef4444;';
                                            elseif($r['timeline']['status_akhir'] == 'Sakit') echo 'color:#ffffff; background:#eab308;';
                                            elseif($r['timeline']['status_akhir'] == 'Izin') echo 'color:#ffffff; background:#3b82f6;';
                                            else echo 'color:#94a3b8;';
                                        ?>
                                    " title="<?php echo htmlspecialchars($r['timeline']['status_akhir']); ?>">
                                        <?php echo singkatStatus($r['timeline']['status_akhir']); ?>
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
