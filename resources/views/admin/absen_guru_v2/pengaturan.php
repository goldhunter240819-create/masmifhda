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
                        <div class="name" style="font-weight:800;font-size:0.85rem;color:#0f172a;line-height:1.2;"><?php echo $nama; ?></div>
                        <div class="role" style="font-size:0.7rem;color:#64748b;">Administrator</div>
                    </div>
                    <div class="z-avatar-premium" style="width:36px;height:36px;color:white;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;box-shadow:0 4px 12px rgba(16,185,129,0.2);"><?php echo $initial; ?></div>
                </div>
                
                <!-- LOGOUT BUTTON -->
                <a href="<?php echo base_url('logout'); ?>" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:10px;background:#fef2f2;color:#ef4444;text-decoration:none;transition:0.2s;border:1px solid #fee2e2;">
                    <i data-lucide="log-out" style="width:18px;height:18px;"></i>
                </a>
            </div>
        </header>

        <div class="z-scroll" style="padding: 2rem;">
            
            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
            <div style="background:#dcfce7; border:1px solid #86efac; color:#15803d; padding:1rem 1.5rem; border-radius:12px; margin-bottom:1.5rem; display:flex; align-items:center; gap:10px; font-weight:700;">
                <i data-lucide="check-circle" style="width:20px; height:20px;"></i> Pengaturan berhasil disimpan!
            </div>
            <?php endif; ?>

            <div style="background:white; border-radius:16px; padding:2rem; border:1px solid #f1f5f9; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); max-width: 600px;">
                <h2 style="margin:0 0 1.5rem 0; font-size:1.2rem; font-weight:800; color:#1e293b; display:flex; align-items:center; gap:8px;">
                    <i data-lucide="settings" style="color:#475569;"></i> Pengaturan Jam Absensi Guru
                </h2>
                
                <form action="<?php echo base_url('admin/absen_guru_v2/pengaturan/save'); ?>" method="POST" id="formPengaturan">
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display:block; font-weight:700; font-size:0.9rem; color:#475569; margin-bottom:8px;">Jam Masuk</label>
                        <input type="time" name="jam_masuk" value="<?php echo htmlspecialchars($pengaturan['jam_masuk']); ?>" required style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
                        <div style="font-size:0.75rem; color:#94a3b8; margin-top:6px;">Waktu normal guru masuk mengajar.</div>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display:block; font-weight:700; font-size:0.9rem; color:#475569; margin-bottom:8px;">Batas Toleransi Terlambat</label>
                        <input type="time" name="batas_terlambat" value="<?php echo htmlspecialchars($pengaturan['batas_terlambat']); ?>" required style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
                        <div style="font-size:0.75rem; color:#94a3b8; margin-top:6px;">Guru yang absen masuk setelah jam ini akan otomatis berstatus Terlambat.</div>
                    </div>
                    
                    <div style="margin-bottom: 2rem; display: flex; gap: 1rem;">
                        <div style="flex: 1;">
                            <label style="display:block; font-weight:700; font-size:0.9rem; color:#475569; margin-bottom:8px;">Jam Pulang (Normal)</label>
                            <input type="time" name="jam_pulang" value="<?php echo htmlspecialchars($pengaturan['jam_pulang']); ?>" required style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
                            <div style="font-size:0.75rem; color:#94a3b8; margin-top:6px;">Scanner otomatis menjadi Mode Pulang.</div>
                        </div>
                        <div style="flex: 1;">
                            <label style="display:block; font-weight:700; font-size:0.9rem; color:#475569; margin-bottom:8px;">Jam Pulang (Khusus Jumat)</label>
                            <input type="time" name="jam_pulang_jumat" value="<?php echo htmlspecialchars($pengaturan['jam_pulang_jumat']); ?>" required style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
                            <div style="font-size:0.75rem; color:#94a3b8; margin-top:6px;">Waktu pulang khusus di hari Jumat.</div>
                        </div>
                        <div style="flex: 1;">
                            <label style="display:block; font-weight:700; font-size:0.9rem; color:#475569; margin-bottom:8px;">Jam Pulang (Khusus Sabtu)</label>
                            <input type="time" name="jam_pulang_sabtu" value="<?php echo htmlspecialchars($pengaturan['jam_pulang_sabtu']); ?>" required style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
                            <div style="font-size:0.75rem; color:#94a3b8; margin-top:6px;">Waktu pulang khusus di hari Sabtu.</div>
                        </div>
                    </div>

                    <h2 style="margin:2.5rem 0 1.5rem 0; font-size:1.2rem; font-weight:800; color:#1e293b; display:flex; align-items:center; gap:8px;">
                        <i data-lucide="mic" style="color:#475569;"></i> Konfigurasi Suara AI (Notifikasi)
                    </h2>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display:block; font-weight:700; font-size:0.9rem; color:#475569; margin-bottom:8px;">Teks Suara Absen Masuk</label>
                        <input type="text" name="teks_suara_masuk" value="<?php echo htmlspecialchars($pengaturan['teks_suara_masuk']); ?>" required style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
                        <div style="font-size:0.75rem; color:#94a3b8; margin-top:6px;">Gunakan <b>[nama]</b> untuk menyebutkan nama guru. Contoh: <i>Halo [nama], Anda berhasil absen masuk.</i></div>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display:block; font-weight:700; font-size:0.9rem; color:#475569; margin-bottom:8px;">Teks Suara Absen Pulang</label>
                        <input type="text" name="teks_suara_pulang" value="<?php echo htmlspecialchars($pengaturan['teks_suara_pulang']); ?>" required style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
                        <div style="font-size:0.75rem; color:#94a3b8; margin-top:6px;">Gunakan <b>[nama]</b> untuk menyebutkan nama guru. Contoh: <i>Terima kasih [nama], hati-hati di jalan.</i></div>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display:block; font-weight:700; font-size:0.9rem; color:#475569; margin-bottom:8px;">Teks Suara Absen Sholat</label>
                        <input type="text" name="teks_suara_sholat" value="<?php echo htmlspecialchars($pengaturan['teks_suara_sholat']); ?>" required style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
                        <div style="font-size:0.75rem; color:#94a3b8; margin-top:6px;">Gunakan <b>[nama]</b> untuk menyebutkan nama guru. Contoh: <i>Halo [nama], Anda berhasil absen sholat.</i></div>
                    </div>
                    
                    <div style="margin-bottom: 2rem;">
                        <label style="display:block; font-weight:700; font-size:0.9rem; color:#ef4444; margin-bottom:8px;">Teks Suara Gagal (Sudah Absen)</label>
                        <input type="text" name="teks_suara_gagal" value="<?php echo htmlspecialchars($pengaturan['teks_suara_gagal']); ?>" required style="width:100%; padding:10px 14px; border:1px solid #fca5a5; background: #fef2f2; border-radius:8px; outline:none; font-family:inherit; color: #b91c1c;">
                        <div style="font-size:0.75rem; color:#94a3b8; margin-top:6px;">Suara jika double scan. Contoh: <i>Maaf [nama], Anda sudah absen sebelumnya.</i></div>
                    </div>

                    <div style="display:flex; justify-content:flex-end;">
                        <button type="button" onclick="simpanPengaturan(this)" style="padding:12px 24px; background:#0ea5e9; color:white; border:none; border-radius:10px; font-weight:700; font-size:0.9rem; cursor:pointer; display:flex; align-items:center; gap:8px;">
                            <i data-lucide="save" style="width:18px;height:18px;"></i> Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
            
            <script>
            function simpanPengaturan(btn) {
                let form = document.getElementById('formPengaturan');
                let formData = new FormData(form);
                let originalText = btn.innerHTML;
                btn.innerHTML = '<i data-lucide="loader-2" class="lucide-spin"></i> Menyimpan...';
                lucide.createIcons();
                
                fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        alert(data.message);
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(err => {
                    alert('Terjadi kesalahan jaringan');
                })
                .finally(() => {
                    btn.innerHTML = originalText;
                    lucide.createIcons();
                });
            }
            </script>
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

