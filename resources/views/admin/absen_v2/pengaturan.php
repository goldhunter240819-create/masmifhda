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
                    <span style="font-weight:700;color:#1e293b;">Konfigurasi Jam</span>
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
                        <i data-lucide="settings" style="color: rgba(255,255,255,0.8);"></i> Pengaturan Mesin Absensi
                    </h1>
                    <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Atur jam buka dan tutup absensi, serta parameter mesin lainnya.</p>
                </div>
            </div>

            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
            <div style="background:#dcfce7; border:1px solid #86efac; color:#15803d; padding:1rem 1.5rem; border-radius:12px; margin-bottom:1.5rem; display:flex; align-items:center; gap:10px; font-weight:700;">
                <i data-lucide="check-circle" style="width:20px; height:20px;"></i> Pengaturan berhasil disimpan!
            </div>
            <?php endif; ?>

            <div style="background:white; border-radius:16px; padding:2rem; border:1px solid #f1f5f9; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
                
                <form action="<?php echo base_url('admin/absen_v2/pengaturan/save'); ?>" method="POST">
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem;">
                        
                        <!-- KOLOM SISWA -->
                        <div style="background: #f8fafc; padding: 1.5rem; border-radius: 12px; border: 1px solid #e2e8f0;">
                            <h2 style="margin:0 0 1.5rem 0; font-size:1.2rem; font-weight:800; color:#1e293b; display:flex; align-items:center; gap:8px;">
                                <i data-lucide="users" style="color:#3b82f6;"></i> Pengaturan Siswa
                            </h2>

                            <div style="margin-bottom: 1.5rem;">
                                <label style="display:block; font-weight:700; font-size:0.9rem; color:#475569; margin-bottom:8px;">Jam Masuk</label>
                                <input type="time" name="jam_masuk" value="<?php echo htmlspecialchars($settings['jam_masuk']); ?>" required style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
                                <div style="font-size:0.75rem; color:#94a3b8; margin-top:6px;">Waktu normal siswa masuk sekolah.</div>
                            </div>
                            
                            <div style="margin-bottom: 1.5rem;">
                                <label style="display:block; font-weight:700; font-size:0.9rem; color:#475569; margin-bottom:8px;">Batas Toleransi Terlambat</label>
                                <input type="time" name="batas_terlambat" value="<?php echo htmlspecialchars($settings['batas_terlambat']); ?>" required style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
                                <div style="font-size:0.75rem; color:#94a3b8; margin-top:6px;">Siswa yang absen masuk setelah jam ini akan otomatis berstatus Terlambat.</div>
                            </div>
                            
                            <div style="margin-bottom: 1.5rem; display: flex; gap: 1rem;">
                                <div style="flex: 1;">
                                    <label style="display:block; font-weight:700; font-size:0.9rem; color:#475569; margin-bottom:8px;">Jam Sholat Dhuhur Mulai</label>
                                    <input type="time" name="jam_sholat_mulai" value="<?php echo htmlspecialchars($settings['jam_sholat_mulai']); ?>" required style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
                                </div>
                                <div style="flex: 1;">
                                    <label style="display:block; font-weight:700; font-size:0.9rem; color:#475569; margin-bottom:8px;">Jam Sholat Selesai</label>
                                    <input type="time" name="jam_sholat_akhir" value="<?php echo htmlspecialchars($settings['jam_sholat_akhir']); ?>" required style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
                                </div>
                            </div>
                            
                            <div style="margin-bottom: 2rem; display: flex; gap: 1rem;">
                                <div style="flex: 1;">
                                    <label style="display:block; font-weight:700; font-size:0.9rem; color:#475569; margin-bottom:8px;">Pulang (Normal)</label>
                                    <input type="time" name="jam_pulang" value="<?php echo htmlspecialchars($settings['jam_pulang']); ?>" required style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
                                </div>
                                <div style="flex: 1;">
                                    <label style="display:block; font-weight:700; font-size:0.9rem; color:#475569; margin-bottom:8px;">Pulang (Jumat)</label>
                                    <input type="time" name="jam_pulang_jumat" value="<?php echo htmlspecialchars($settings['jam_pulang_jumat']); ?>" required style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
                                </div>
                                <div style="flex: 1;">
                                    <label style="display:block; font-weight:700; font-size:0.9rem; color:#475569; margin-bottom:8px;">Pulang (Sabtu)</label>
                                    <input type="time" name="jam_pulang_sabtu" value="<?php echo htmlspecialchars($settings['jam_pulang_sabtu']); ?>" required style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
                                </div>
                            </div>

                            <h3 style="margin:0 0 1rem 0; font-size:1rem; font-weight:800; color:#334155; display:flex; align-items:center; gap:8px;">
                                <i data-lucide="mic" style="color:#64748b; width:18px;"></i> Suara AI (Siswa)
                            </h3>
                            
                            <div style="margin-bottom: 1rem;">
                                <label style="display:block; font-weight:700; font-size:0.85rem; color:#475569; margin-bottom:8px;">Masuk</label>
                                <input type="text" name="teks_suara_masuk" value="<?php echo htmlspecialchars($settings['teks_suara_masuk']); ?>" required style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-size: 0.85rem;">
                            </div>
                            <div style="margin-bottom: 1rem;">
                                <label style="display:block; font-weight:700; font-size:0.85rem; color:#475569; margin-bottom:8px;">Pulang</label>
                                <input type="text" name="teks_suara_pulang" value="<?php echo htmlspecialchars($settings['teks_suara_pulang']); ?>" required style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-size: 0.85rem;">
                            </div>
                            <div style="margin-bottom: 1rem;">
                                <label style="display:block; font-weight:700; font-size:0.85rem; color:#475569; margin-bottom:8px;">Sholat</label>
                                <input type="text" name="teks_suara_sholat" value="<?php echo htmlspecialchars($settings['teks_suara_sholat']); ?>" required style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-size: 0.85rem;">
                            </div>
                            <div>
                                <label style="display:block; font-weight:700; font-size:0.85rem; color:#ef4444; margin-bottom:8px;">Gagal (Sudah Absen)</label>
                                <input type="text" name="teks_suara_gagal" value="<?php echo htmlspecialchars($settings['teks_suara_gagal']); ?>" required style="width:100%; padding:8px 12px; border:1px solid #fca5a5; background: #fef2f2; border-radius:8px; outline:none; font-size: 0.85rem; color: #b91c1c;">
                            </div>
                        </div>

                        <!-- KOLOM GURU -->
                        <div style="background: #f8fafc; padding: 1.5rem; border-radius: 12px; border: 1px solid #e2e8f0;">
                            <h2 style="margin:0 0 1.5rem 0; font-size:1.2rem; font-weight:800; color:#1e293b; display:flex; align-items:center; gap:8px;">
                                <i data-lucide="briefcase" style="color:#0ea5e9;"></i> Pengaturan Guru
                            </h2>

                            <div style="margin-bottom: 1.5rem;">
                                <label style="display:block; font-weight:700; font-size:0.9rem; color:#475569; margin-bottom:8px;">Jam Masuk</label>
                                <input type="time" name="guru_jam_masuk" value="<?php echo htmlspecialchars($settings_guru['jam_masuk']); ?>" required style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
                                <div style="font-size:0.75rem; color:#94a3b8; margin-top:6px;">Waktu normal guru datang.</div>
                            </div>
                            
                            <div style="margin-bottom: 1.5rem;">
                                <label style="display:block; font-weight:700; font-size:0.9rem; color:#475569; margin-bottom:8px;">Batas Toleransi Terlambat</label>
                                <input type="time" name="guru_batas_terlambat" value="<?php echo htmlspecialchars($settings_guru['batas_terlambat']); ?>" required style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
                                <div style="font-size:0.75rem; color:#94a3b8; margin-top:6px;">Guru yang absen setelah jam ini akan berstatus Terlambat.</div>
                            </div>
                            
                            <div style="margin-bottom: 2rem; display: flex; gap: 1rem;">
                                <div style="flex: 1;">
                                    <label style="display:block; font-weight:700; font-size:0.9rem; color:#475569; margin-bottom:8px;">Pulang (Normal)</label>
                                    <input type="time" name="guru_jam_pulang" value="<?php echo htmlspecialchars($settings_guru['jam_pulang']); ?>" required style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
                                </div>
                                <div style="flex: 1;">
                                    <label style="display:block; font-weight:700; font-size:0.9rem; color:#475569; margin-bottom:8px;">Pulang (Jumat)</label>
                                    <input type="time" name="guru_jam_pulang_jumat" value="<?php echo htmlspecialchars($settings_guru['jam_pulang_jumat']); ?>" required style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
                                </div>
                                <div style="flex: 1;">
                                    <label style="display:block; font-weight:700; font-size:0.9rem; color:#475569; margin-bottom:8px;">Pulang (Sabtu)</label>
                                    <input type="time" name="guru_jam_pulang_sabtu" value="<?php echo htmlspecialchars($settings_guru['jam_pulang_sabtu']); ?>" required style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
                                </div>
                            </div>

                            <h3 style="margin:0 0 1rem 0; font-size:1rem; font-weight:800; color:#334155; display:flex; align-items:center; gap:8px;">
                                <i data-lucide="mic" style="color:#64748b; width:18px;"></i> Suara AI (Guru)
                            </h3>
                            
                            <div style="margin-bottom: 1rem;">
                                <label style="display:block; font-weight:700; font-size:0.85rem; color:#475569; margin-bottom:8px;">Masuk</label>
                                <input type="text" name="guru_teks_suara_masuk" value="<?php echo htmlspecialchars($settings_guru['teks_suara_masuk']); ?>" required style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-size: 0.85rem;">
                            </div>
                            <div style="margin-bottom: 1rem;">
                                <label style="display:block; font-weight:700; font-size:0.85rem; color:#475569; margin-bottom:8px;">Pulang</label>
                                <input type="text" name="guru_teks_suara_pulang" value="<?php echo htmlspecialchars($settings_guru['teks_suara_pulang']); ?>" required style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-size: 0.85rem;">
                            </div>
                            <div style="margin-bottom: 1rem;">
                                <label style="display:block; font-weight:700; font-size:0.85rem; color:#475569; margin-bottom:8px;">Sholat (Kosongkan jika tidak ada)</label>
                                <input type="text" name="guru_teks_suara_sholat" value="<?php echo htmlspecialchars($settings_guru['teks_suara_sholat']); ?>" style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-size: 0.85rem;">
                            </div>
                            <div>
                                <label style="display:block; font-weight:700; font-size:0.85rem; color:#ef4444; margin-bottom:8px;">Gagal (Sudah Absen)</label>
                                <input type="text" name="guru_teks_suara_gagal" value="<?php echo htmlspecialchars($settings_guru['teks_suara_gagal']); ?>" required style="width:100%; padding:8px 12px; border:1px solid #fca5a5; background: #fef2f2; border-radius:8px; outline:none; font-size: 0.85rem; color: #b91c1c;">
                            </div>
                        </div>
                    </div>

                    <div style="display:flex; justify-content:flex-end; margin-top: 2rem;">
                        <button type="submit" style="padding:14px 28px; background:linear-gradient(135deg, #0ea5e9, #0284c7); color:white; border:none; border-radius:10px; font-weight:800; font-size:1rem; cursor:pointer; display:flex; align-items:center; gap:8px; box-shadow: 0 4px 12px rgba(14,165,233,0.2);">
                            <i data-lucide="save" style="width:20px;height:20px;"></i> Simpan Semua Pengaturan
                        </button>
                    </div>
                </form>
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

