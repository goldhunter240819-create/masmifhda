<?php
$pageTitle = 'Peserta & Pengawas CBT Kelas Jauh - MAS Miftahul Huda';
$active_menu = 'peserta_khusus';
$activeTab = $_GET['tab'] ?? 'siswa';
?>
<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/header.php'; ?>

        <div class="z-scroll" style="padding: 0 0 3rem 0;">

            <!-- Elite Header -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-badge">ISOLASI CBT & CABANG LUAR</div>
                        <h1><i data-lucide="users-2"></i> CBT Kelas Jauh / Cabang Luar</h1>
                        <p>Kelola siswa dan akun pengawas kelas jauh untuk ujian online. Data <strong>100% terisolasi dari SIAKAD</strong> (bebas tagihan keuangan/SPP, buku rapor reguler, dan guru induk), namun terintegrasi penuh dengan bank soal dan jadwal CBT.</p>
                    </div>
                    <div class="header-actions-premium" style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <?php if ($activeTab === 'pengawas'): ?>
                            <button type="button" onclick="bukaModalTambahPengawas()" class="e-btn e-btn-primary" style="background: #059669;">
                                <i data-lucide="user-plus"></i> Tambah Akun Pengawas
                            </button>
                        <?php else: ?>
                            <a href="<?php echo base_url('cbt/peserta_khusus/cetak' . (!empty($selectedTingkat) ? '?tingkat=' . urlencode($selectedTingkat) : '')); ?>" target="_blank" class="e-btn e-btn-glass">
                                <i data-lucide="printer"></i> Cetak Kartu Peserta
                            </a>
                            <button type="button" onclick="bukaModalImport()" class="e-btn e-btn-blue-light">
                                <i data-lucide="file-up"></i> Import Excel / CSV
                            </button>
                            <button type="button" onclick="bukaModalTambah()" class="e-btn e-btn-primary" style="background: #4f46e5;">
                                <i data-lucide="user-plus"></i> Tambah Siswa
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if (!empty($_GET['msg'])): ?>
            <div style="margin-bottom: 1.25rem;">
                <?php if ($_GET['msg'] === 'saved'): ?>
                    <div style="background: #dcfce7; border: 1px solid #86efac; color: #15803d; padding: 12px 16px; border-radius: 12px; font-weight: 700; font-size: 0.88rem; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i> Data berhasil disimpan ke database!
                    </div>
                <?php elseif ($_GET['msg'] === 'deleted'): ?>
                    <div style="background: #fee2e2; border: 1px solid #fca5a5; color: #b91c1c; padding: 12px 16px; border-radius: 12px; font-weight: 700; font-size: 0.88rem; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="trash-2" style="width: 18px; height: 18px;"></i> Data berhasil dihapus dari sistem.
                    </div>
                <?php elseif ($_GET['msg'] === 'imported'): ?>
                    <div style="background: #e0f2fe; border: 1px solid #7dd3fc; color: #0369a1; padding: 12px 16px; border-radius: 12px; font-weight: 700; font-size: 0.88rem; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="file-check" style="width: 18px; height: 18px;"></i> Sukses mengimport <?php echo (int)($_GET['count'] ?? 0); ?> peserta kelas jauh!
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Navigation Tabs (Siswa vs Pengawas) -->
            <div style="display: flex; gap: 10px; margin-bottom: 1.5rem; background: #e2e8f0; padding: 6px; border-radius: 14px; width: fit-content;">
                <a href="<?php echo base_url('cbt/peserta_khusus?tab=siswa'); ?>" style="display: inline-flex; align-items: center; gap: 8px; padding: 9px 18px; border-radius: 10px; font-weight: 800; font-size: 0.88rem; text-decoration: none; transition: 0.2s; <?php echo ($activeTab !== 'pengawas') ? 'background: #ffffff; color: #4f46e5; box-shadow: 0 2px 6px rgba(0,0,0,0.06);' : 'color: #64748b;'; ?>">
                    <i data-lucide="graduation-cap" style="width: 18px; height: 18px;"></i>
                    <span>Data Siswa Kelas Jauh</span>
                    <span style="background: <?php echo ($activeTab !== 'pengawas') ? '#e0e7ff' : '#cbd5e1'; ?>; color: <?php echo ($activeTab !== 'pengawas') ? '#4338ca' : '#475569'; ?>; padding: 2px 8px; border-radius: 20px; font-size: 0.72rem; font-weight: 800;">
                        <?php echo count($pesertaList); ?>
                    </span>
                </a>
                <a href="<?php echo base_url('cbt/peserta_khusus?tab=pengawas'); ?>" style="display: inline-flex; align-items: center; gap: 8px; padding: 9px 18px; border-radius: 10px; font-weight: 800; font-size: 0.88rem; text-decoration: none; transition: 0.2s; <?php echo ($activeTab === 'pengawas') ? 'background: #ffffff; color: #059669; box-shadow: 0 2px 6px rgba(0,0,0,0.06);' : 'color: #64748b;'; ?>">
                    <i data-lucide="shield-check" style="width: 18px; height: 18px;"></i>
                    <span>Akun Pengawas / Admin Kelas Jauh</span>
                    <span style="background: <?php echo ($activeTab === 'pengawas') ? '#dcfce7' : '#cbd5e1'; ?>; color: <?php echo ($activeTab === 'pengawas') ? '#15803d' : '#475569'; ?>; padding: 2px 8px; border-radius: 20px; font-size: 0.72rem; font-weight: 800;">
                        <?php echo count($pengawasKjList ?? []); ?>
                    </span>
                </a>
            </div>

            <?php if ($activeTab === 'pengawas'): ?>
                <!-- ================= TAB PENGAWAS KELAS JAUH ================= -->
                <!-- Info Alert Pengawas -->
                <div style="background: #ecfdf5; border: 1.5px solid #a7f3d0; border-radius: 16px; padding: 16px 20px; margin-bottom: 1.5rem; display: flex; align-items: flex-start; gap: 14px;">
                    <div style="background: #059669; color: #fff; width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                        <i data-lucide="user-check" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div style="font-size: 0.85rem; color: #065f46; line-height: 1.5;">
                        <strong style="font-size: 0.92rem; display: block; margin-bottom: 3px; color: #064e3b;">Akun Pengawas Mandiri Cabang Kelas Jauh</strong>
                        Akun di bawah ini dibuat khusus untuk guru/proktor pengawas di cabang kelas jauh (<strong>terisolasi dari SIAKAD Guru</strong>). 
                        Pengawas dapat login langsung di Aplikasi Guru (<code>apk/login.php</code>) menggunakan username & kata sandi di bawah. Saat berhasil masuk, pengawas otomatis diarahkan ke <strong>Portal Mandiri Pengawas Kelas Jauh</strong> untuk membuka pemindai kamera scan absensi 1 pintu dan melihat nilai rekap siswa cabang.
                    </div>
                </div>

                <!-- Table Pengawas -->
                <div class="card" style="border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden; background: #ffffff;">
                    <div style="padding: 1.25rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                        <div style="font-weight: 800; font-size: 0.95rem; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                            <span>Daftar Akun Pengawas Cabang</span>
                            <span style="font-size: 0.75rem; background: #dcfce7; color: #15803d; padding: 2px 8px; border-radius: 100px; font-weight: 800;">
                                <?php echo count($pengawasKjList ?? []); ?> Akun
                            </span>
                        </div>
                    </div>

                    <div class="z-table-wrap">
                        <table class="z-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px; text-align: center;">No</th>
                                    <th style="min-width: 200px;">Nama Pengawas / Proktor</th>
                                    <th style="width: 150px;">Username Login</th>
                                    <th style="width: 140px;">Kata Sandi</th>
                                    <th style="width: 160px;">Lokasi Cabang</th>
                                    <th style="width: 140px;">No. HP / WA</th>
                                    <th style="width: 100px; text-align: center;">Status</th>
                                    <th style="width: 110px; text-align: right;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pengawasKjList)): ?>
                                <tr>
                                    <td colspan="8" style="padding: 3rem 1.5rem; text-align: center; color: #94a3b8;">
                                        <i data-lucide="user-x" style="width: 42px; height: 42px; margin-bottom: 8px; color: #cbd5e1;"></i>
                                        <div style="font-size: 0.95rem; font-weight: 700; color: #475569;">Belum ada akun pengawas kelas jauh</div>
                                        <div style="font-size: 0.78rem;">Klik tombol <strong>+ Tambah Akun Pengawas</strong> di atas untuk membuat akun baru.</div>
                                    </td>
                                </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach($pengawasKjList as $pkj): ?>
                                    <tr>
                                        <td style="text-align: center; color: #64748b; font-weight: 700;">
                                            <?php echo $no++; ?>
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <div style="width: 38px; height: 38px; border-radius: 10px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; border: 1px solid #a7f3d0;">
                                                    <i data-lucide="user-check" style="width: 18px; height: 18px;"></i>
                                                </div>
                                                <div>
                                                    <div style="font-weight: 800; color: #0f172a; font-size: 0.92rem;">
                                                        <?php echo htmlspecialchars($pkj['nama']); ?>
                                                    </div>
                                                    <div style="font-size: 0.72rem; color: #64748b;">
                                                        ID Pengawas: <code>KJ-<?php echo str_pad($pkj['id'], 4, '0', STR_PAD_LEFT); ?></code>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="font-family: monospace; font-weight: 800; font-size: 0.9rem; color: #059669; background: #f0fdf4; padding: 3px 8px; border-radius: 6px; border: 1px solid #bbf7d0;">
                                                <?php echo htmlspecialchars($pkj['username']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span style="font-family: monospace; font-size: 0.85rem; color: #475569; background: #f8fafc; padding: 3px 8px; border-radius: 6px; border: 1px solid #e2e8f0;">
                                                <?php echo htmlspecialchars($pkj['password_plain'] ?: '••••••'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span style="font-weight: 700; color: #334155; font-size: 0.85rem;">
                                                <?php echo htmlspecialchars($pkj['lokasi_cabang'] ?: 'Kelas Jauh'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span style="font-size: 0.82rem; color: #64748b;">
                                                <?php echo htmlspecialchars($pkj['no_hp'] ?: '-'); ?>
                                            </span>
                                        </td>
                                        <td style="text-align: center;">
                                            <?php if (($pkj['status'] ?? 'Aktif') === 'Aktif'): ?>
                                                <span style="background: #dcfce7; color: #15803d; padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; border: 1px solid #86efac;">
                                                    Aktif
                                                </span>
                                            <?php else: ?>
                                                <span style="background: #fee2e2; color: #b91c1c; padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; border: 1px solid #fca5a5;">
                                                    Nonaktif
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="text-align: right;">
                                            <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                                <button type="button" onclick="bukaModalEditPengawas(<?php echo htmlspecialchars(json_encode($pkj), ENT_QUOTES, 'UTF-8'); ?>)" class="e-btn e-btn-glass" style="padding: 6px 10px; font-size: 0.78rem;" title="Edit Akun Pengawas">
                                                    <i data-lucide="edit-3" style="width: 14px; height: 14px; color: #0284c7;"></i>
                                                </button>
                                                <button type="button" onclick="hapusPengawas(<?php echo (int)$pkj['id']; ?>, '<?php echo htmlspecialchars(addslashes($pkj['nama'])); ?>')" class="e-btn e-btn-glass" style="padding: 6px 10px; font-size: 0.78rem; color: #ef4444;" title="Hapus Pengawas">
                                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php else: ?>
                <!-- ================= TAB DATA SISWA KELAS JAUH ================= -->
                <!-- Info Alert Isolasi Data -->
                <div style="background: #fffbeb; border: 1.5px solid #fde68a; border-radius: 16px; padding: 14px 18px; margin-bottom: 1.5rem; display: flex; align-items: flex-start; gap: 12px;">
                    <div style="background: #f59e0b; color: #fff; width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                        <i data-lucide="shield-check" style="width: 18px; height: 18px;"></i>
                    </div>
                    <div style="font-size: 0.82rem; color: #92400e; line-height: 1.5;">
                        <strong style="font-size: 0.88rem; display: block; margin-bottom: 2px; color: #78350f;">Privasi & Keamanan Tagihan Terjamin</strong>
                        Siswa pada menu ini <strong>hanya aktif untuk modul CBT Ujian</strong>. Mereka tidak akan pernah muncul pada tagihan SPP bendahara, kasir keuangan, presensi absensi harian, maupun ledger rapor reguler. Saat login di aplikasi, sistem akan langsung mengarahkan mereka ke <strong>Ruang Ujian CBT</strong>.
                    </div>
                </div>

                <!-- Stats Grid -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-bottom: 1.5rem;">
                    <div class="card" style="padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0; background: #ffffff;">
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Peserta Kelas Jauh</div>
                        <div style="font-size: 1.6rem; font-weight: 900; color: #0f172a; margin-top: 4px;"><?php echo count($pesertaList); ?></div>
                        <div style="font-size: 0.72rem; color: #10b981; font-weight: 700; margin-top: 4px;">Akun CBT Terdaftar</div>
                    </div>
                    <div class="card" style="padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0; background: #ffffff;">
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Tingkat Kelas X</div>
                        <div style="font-size: 1.6rem; font-weight: 900; color: #3b82f6; margin-top: 4px;"><?php echo $countX ?? 0; ?></div>
                        <div style="font-size: 0.72rem; color: #64748b;">Paket Ujian Kelas X</div>
                    </div>
                    <div class="card" style="padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0; background: #ffffff;">
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Tingkat Kelas XI</div>
                        <div style="font-size: 1.6rem; font-weight: 900; color: #8b5cf6; margin-top: 4px;"><?php echo $countXI ?? 0; ?></div>
                        <div style="font-size: 0.72rem; color: #64748b;">Paket Ujian Kelas XI</div>
                    </div>
                    <div class="card" style="padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0; background: #ffffff;">
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Tingkat Kelas XII</div>
                        <div style="font-size: 1.6rem; font-weight: 900; color: #ec4899; margin-top: 4px;"><?php echo $countXII ?? 0; ?></div>
                        <div style="font-size: 0.72rem; color: #64748b;">Paket Ujian Kelas XII</div>
                    </div>
                </div>

                <!-- Filter Card -->
                <div class="card" style="padding: 1.25rem 1.5rem; border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; background: #ffffff;">
                    <form method="GET" action="<?php echo base_url('cbt/peserta_khusus'); ?>" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
                        <input type="hidden" name="tab" value="siswa">
                        <div style="flex: 1; min-width: 200px;">
                            <label style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 6px; display: block;">Filter Tingkat Ujian</label>
                            <select name="tingkat" onchange="this.form.submit()" style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 0.88rem; font-weight: 600; color: #1e293b;">
                                <option value="">-- Semua Tingkat Kelas --</option>
                                <option value="Kelas X" <?php echo ($selectedTingkat === 'Kelas X') ? 'selected' : ''; ?>>Kelas X (Sepuluh)</option>
                                <option value="Kelas XI" <?php echo ($selectedTingkat === 'Kelas XI') ? 'selected' : ''; ?>>Kelas XI (Sebelas)</option>
                                <option value="Kelas XII" <?php echo ($selectedTingkat === 'Kelas XII') ? 'selected' : ''; ?>>Kelas XII (Dua Belas)</option>
                            </select>
                        </div>
                        <div style="flex: 2; min-width: 250px;">
                            <label style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 6px; display: block;">Cari Nama / No Peserta / Rombel</label>
                            <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Ketik nama atau nomor peserta..." style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 0.88rem;">
                        </div>
                        <div>
                            <button type="submit" class="e-btn e-btn-primary" style="padding: 10px 18px; font-size: 0.88rem;">
                                <i data-lucide="search"></i> Terapkan
                            </button>
                        </div>
                        <?php if (!empty($selectedTingkat) || !empty($search)): ?>
                        <div>
                            <a href="<?php echo base_url('cbt/peserta_khusus?tab=siswa'); ?>" class="e-btn e-btn-glass" style="color: #64748b; padding: 10px 14px; font-size: 0.88rem;">
                                Reset
                            </a>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Table Card Siswa -->
                <div class="card" style="border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden; background: #ffffff;">
                    <div style="padding: 1.25rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                        <div style="font-weight: 800; font-size: 0.95rem; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                            <span>Daftar Siswa Kelas Jauh</span>
                            <span style="font-size: 0.75rem; background: #e0e7ff; color: #4338ca; padding: 2px 8px; border-radius: 100px; font-weight: 800;">
                                <?php echo count($pesertaList); ?> Siswa
                            </span>
                        </div>
                    </div>

                    <div class="z-table-wrap">
                        <table class="z-table">
                            <thead>
                                <tr>
                                    <th style="width: 45px; text-align: center;">No</th>
                                    <th style="width: 140px;">No. Peserta / NIS</th>
                                    <th style="min-width: 200px;">Nama Siswa</th>
                                    <th style="width: 140px; text-align: center;">Tingkat Ujian</th>
                                    <th style="width: 150px;">Rombel / Cabang</th>
                                    <th style="width: 180px;">Akun Login (Username & Sandi)</th>
                                    <th style="width: 90px; text-align: center;">Status</th>
                                    <th style="width: 100px; text-align: right;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pesertaList)): ?>
                                <tr>
                                    <td colspan="8" style="padding: 3.5rem 1.5rem; text-align: center; color: #94a3b8;">
                                        <i data-lucide="users" style="width: 44px; height: 44px; margin-bottom: 10px; color: #cbd5e1;"></i>
                                        <div style="font-size: 0.95rem; font-weight: 700; color: #475569;">Belum ada peserta kelas jauh</div>
                                        <div style="font-size: 0.78rem;">Klik tombol <strong>Tambah Siswa</strong> atau <strong>Import Excel / CSV</strong> di atas untuk memasukkan data siswa.</div>
                                    </td>
                                </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach($pesertaList as $p): ?>
                                    <tr>
                                        <td style="text-align: center; color: #64748b; font-weight: 700;">
                                            <?php echo $no++; ?>
                                        </td>
                                        <td>
                                            <div style="font-family: monospace; font-weight: 800; font-size: 0.88rem; color: #0284c7;">
                                                <?php echo htmlspecialchars($p['no_peserta']); ?>
                                            </div>
                                            <?php if (!empty($p['nis'])): ?>
                                            <div style="font-size: 0.72rem; color: #64748b;">
                                                NIS: <?php echo htmlspecialchars($p['nis']); ?>
                                            </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <div style="width: 38px; height: 38px; border-radius: 10px; overflow: hidden; background: #e0e7ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid #c7d2fe;">
                                                    <?php if (!empty($p['foto'])): ?>
                                                        <img src="/public/uploads/siswa/<?php echo htmlspecialchars($p['foto']); ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.outerHTML='<i data-lucide=\'user\' style=\'width: 18px; height: 18px; color: #4f46e5;\'></i>';">
                                                    <?php else: ?>
                                                        <i data-lucide="user" style="width: 18px; height: 18px; color: #4f46e5;"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <div style="font-weight: 800; color: #0f172a; font-size: 0.92rem;">
                                                        <?php echo htmlspecialchars($p['nama_siswa']); ?>
                                                    </div>
                                                    <div style="font-size: 0.72rem; color: #64748b;">
                                                        Lokasi: <?php echo htmlspecialchars($p['lokasi_cabang'] ?: 'Kelas Jauh'); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="text-align: center;">
                                            <?php 
                                            $tBadge = 'background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd;';
                                            if ($p['tingkat'] === 'Kelas XI') $tBadge = 'background:#f5f3ff; color:#7c3aed; border:1px solid #ddd6fe;';
                                            if ($p['tingkat'] === 'Kelas XII') $tBadge = 'background:#fdf2f8; color:#be185d; border:1px solid #fbcfe8;';
                                            ?>
                                            <span style="display: inline-block; padding: 3px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; <?php echo $tBadge; ?>">
                                                <?php echo htmlspecialchars($p['tingkat']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span style="font-weight: 700; color: #334155; font-size: 0.85rem;">
                                                <?php echo htmlspecialchars($p['kelas_nama']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div style="font-family: monospace; font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                                                User: <span style="color:#2563eb;"><?php echo htmlspecialchars($p['username']); ?></span>
                                            </div>
                                            <div style="font-family: monospace; font-size: 0.75rem; color: #64748b;">
                                                Pass: <?php echo htmlspecialchars($p['password_plain'] ?: '••••••'); ?>
                                            </div>
                                        </td>
                                        <td style="text-align: center;">
                                            <?php if ($p['status'] === 'Aktif'): ?>
                                                <span style="background: #dcfce7; color: #15803d; padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; border: 1px solid #86efac;">
                                                    Aktif
                                                </span>
                                            <?php else: ?>
                                                <span style="background: #fee2e2; color: #b91c1c; padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; border: 1px solid #fca5a5;">
                                                    Nonaktif
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="text-align: right;">
                                            <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                                <button type="button" onclick="bukaModalEdit(<?php echo htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8'); ?>)" class="e-btn e-btn-glass" style="padding: 6px 10px; font-size: 0.78rem;" title="Edit Siswa">
                                                    <i data-lucide="edit-3" style="width: 14px; height: 14px; color: #4f46e5;"></i>
                                                </button>
                                                <a href="<?php echo base_url('cbt/peserta_khusus/delete?id=' . $p['id']); ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus peserta <?php echo addslashes($p['nama_siswa']); ?>?')" class="e-btn e-btn-glass" style="padding: 6px 10px; font-size: 0.78rem; color: #ef4444;" title="Hapus Siswa">
                                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- ================= MODAL TAMBAH / EDIT PENGAWAS ================= -->
<div id="modalFormPengawas" class="modal-overlay" onclick="if(event.target === this) this.classList.remove('open')">
    <div style="background: #ffffff; width: 90%; max-width: 500px; border-radius: 20px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.15); border: 1px solid #e2e8f0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 id="formModalTitlePengawas" style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="shield-check" style="width: 20px; height: 20px; color: #059669;"></i>
                Tambah Akun Pengawas Kelas Jauh
            </h3>
            <button type="button" onclick="document.getElementById('modalFormPengawas').classList.remove('open')" style="background: none; border: none; cursor: pointer; color: #94a3b8;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>

        <form method="POST" action="<?php echo base_url('cbt/pengawas_kj/save'); ?>">
            <input type="hidden" name="id" id="inpPkjId" value="0">

            <div style="margin-bottom: 12px;">
                <label style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 4px; display: block;">Nama Lengkap Pengawas / Proktor *</label>
                <input type="text" name="nama" id="inpPkjNama" required placeholder="Contoh: Ustadz M. Anwar, S.Pd" style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 0.88rem;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                <div>
                    <label style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 4px; display: block;">Username Login *</label>
                    <input type="text" name="username" id="inpPkjUsername" required placeholder="misal: adminkj" style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 0.88rem; font-family: monospace;">
                </div>
                <div>
                    <label style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 4px; display: block;">Kata Sandi *</label>
                    <input type="text" name="password" id="inpPkjPassword" required value="123456" placeholder="Password akun" style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 0.88rem; font-family: monospace;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                <div>
                    <label style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 4px; display: block;">Lokasi Cabang</label>
                    <input type="text" name="lokasi_cabang" id="inpPkjCabang" value="Cabang Kelas Jauh" placeholder="Nama cabang / pondok..." style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 0.88rem;">
                </div>
                <div>
                    <label style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 4px; display: block;">No. WhatsApp</label>
                    <input type="text" name="no_hp" id="inpPkjNoHp" placeholder="08xxxxxxxxxx" style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 0.88rem;">
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 4px; display: block;">Status Akun</label>
                <select name="status" id="inpPkjStatus" style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 0.88rem;">
                    <option value="Aktif">Aktif (Dapat Login & Scan Absensi)</option>
                    <option value="Nonaktif">Nonaktif (Akses Dinonaktifkan)</option>
                </select>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 1.5rem;">
                <button type="button" onclick="document.getElementById('modalFormPengawas').classList.remove('open')" class="e-btn e-btn-glass" style="padding: 10px 18px;">
                    Batal
                </button>
                <button type="submit" class="e-btn e-btn-primary" style="padding: 10px 22px; background: #059669;">
                    <i data-lucide="save"></i> Simpan Akun Pengawas
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL TAMBAH / EDIT SISWA ================= -->
<div id="modalFormPeserta" class="modal-overlay" onclick="if(event.target === this) this.classList.remove('open')">
    <div style="background: #ffffff; width: 90%; max-width: 540px; border-radius: 20px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.15); border: 1px solid #e2e8f0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 id="formModalTitle" style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="user-plus" style="width: 20px; height: 20px; color: #4f46e5;"></i>
                Tambah Peserta Kelas Jauh
            </h3>
            <button type="button" onclick="document.getElementById('modalFormPeserta').classList.remove('open')" style="background: none; border: none; cursor: pointer; color: #94a3b8;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>

        <form method="POST" action="<?php echo base_url('cbt/peserta_khusus/save'); ?>" enctype="multipart/form-data">
            <input type="hidden" name="id" id="inpId" value="0">

            <div style="margin-bottom: 12px;">
                <label style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 4px; display: block;">Nama Lengkap Siswa *</label>
                <input type="text" name="nama_siswa" id="inpNama" required placeholder="Nama siswa sesuai identitas..." style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 0.88rem;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                <div>
                    <label style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 4px; display: block;">No. Peserta CBT *</label>
                    <input type="text" name="no_peserta" id="inpNoPeserta" required placeholder="Contoh: KJ-26001" style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 0.88rem; font-family: monospace;">
                </div>
                <div>
                    <label style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 4px; display: block;">NIS Cabang (Opsional)</label>
                    <input type="text" name="nis" id="inpNis" placeholder="Nomor Induk Siswa..." style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 0.88rem;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                <div>
                    <label style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 4px; display: block;">Tingkat Ujian *</label>
                    <select name="tingkat" id="inpTingkat" required style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 0.88rem;">
                        <option value="Kelas X">Kelas X (Sepuluh)</option>
                        <option value="Kelas XI">Kelas XI (Sebelas)</option>
                        <option value="Kelas XII">Kelas XII (Dua Belas)</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 4px; display: block;">Rombel / Kelas CBT *</label>
                    <input type="text" name="kelas_nama" id="inpKelasNama" required value="X - Kelas Jauh" placeholder="Contoh: X - Kelas Jauh" style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 0.88rem;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                <div>
                    <label style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 4px; display: block;">Username Akun Ujian *</label>
                    <input type="text" name="username" id="inpUsername" required placeholder="Username unik..." style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 0.88rem; font-family: monospace;">
                </div>
                <div>
                    <label style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 4px; display: block;">Kata Sandi *</label>
                    <input type="text" name="password" id="inpPassword" required value="123456" placeholder="Password ujian..." style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 0.88rem; font-family: monospace;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                <div>
                    <label style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 4px; display: block;">Jenis Kelamin</label>
                    <select name="jenis_kelamin" id="inpJk" style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 0.88rem;">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 4px; display: block;">No. HP / WA</label>
                    <input type="text" name="no_hp" id="inpNoHp" placeholder="08xxxxxxxxxx" style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 0.88rem;">
                </div>
            </div>

            <div style="margin-bottom: 14px;">
                <label style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 4px; display: block;">Unggah Foto Siswa (3x4 Resmi)</label>
                <input type="file" name="foto" accept="image/*" style="width: 100%; padding: 8px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 0.82rem;">
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 1.5rem;">
                <button type="button" onclick="document.getElementById('modalFormPeserta').classList.remove('open')" class="e-btn e-btn-glass" style="padding: 10px 18px;">
                    Batal
                </button>
                <button type="submit" class="e-btn e-btn-primary" style="padding: 10px 22px; background: #4f46e5;">
                    <i data-lucide="save"></i> Simpan Peserta
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Import CSV / Excel -->
<div id="modalImport" class="modal-overlay" onclick="if(event.target === this) this.classList.remove('open')">
    <div style="background: #ffffff; width: 90%; max-width: 520px; border-radius: 20px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.15); border: 1px solid #e2e8f0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="file-up" style="width: 20px; height: 20px; color: #0284c7;"></i>
                Import Massal Siswa Kelas Jauh
            </h3>
            <button type="button" onclick="document.getElementById('modalImport').classList.remove('open')" style="background: none; border: none; cursor: pointer; color: #94a3b8;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>

        <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 12px 16px; margin-bottom: 1.25rem; font-size: 0.8rem; color: #475569;">
            <div style="font-weight: 800; color: #1e293b; margin-bottom: 4px;">Format File CSV:</div>
            Format kolom teks berurutan: <br>
            <code style="background:#e2e8f0; padding:2px 6px; border-radius:4px; font-weight:700;">No Peserta, Nama Siswa, Tingkat, Rombel, Username, Password</code>
            <div style="margin-top: 8px;">
                <a href="data:text/csv;charset=utf-8,No%20Peserta%2CNama%20Siswa%2CTingkat%2CRombel%2CUsername%2CPassword%0AKJ-26001%2CAhmad%20Baihaqi%2CKelas%20X%2CX%20-%20Kelas%20Jauh%2Ckj26001%2C123456%0AKJ-26002%2CSiti%20Aminah%2CKelas%20X%2CX%20-%20Kelas%20Jauh%2Ckj26002%2C123456" download="template_siswa_kelas_jauh.csv" style="color: #0284c7; font-weight: 800; text-decoration: underline;">
                    📥 Unduh Contoh Template CSV
                </a>
            </div>
        </div>

        <form method="POST" action="<?php echo base_url('cbt/peserta_khusus/import'); ?>" enctype="multipart/form-data">
            <div style="margin-bottom: 1.5rem;">
                <label style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 6px; display: block;">Pilih File CSV (.csv)</label>
                <input type="file" name="file_csv" accept=".csv" required style="width: 100%; padding: 10px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 0.85rem;">
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('modalImport').classList.remove('open')" class="e-btn e-btn-glass" style="padding: 10px 18px;">
                    Batal
                </button>
                <button type="submit" class="e-btn e-btn-primary" style="padding: 10px 22px; background: #0284c7;">
                    <i data-lucide="upload"></i> Proses Import
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ==================== HANDLER SISWA KELAS JAUH ====================
function bukaModalTambah() {
    document.getElementById('formModalTitle').innerHTML = '<i data-lucide="user-plus" style="width:20px;height:20px;color:#4f46e5;"></i> Tambah Siswa Kelas Jauh';
    document.getElementById('inpId').value = '0';
    document.getElementById('inpNama').value = '';
    document.getElementById('inpNoPeserta').value = '';
    document.getElementById('inpNis').value = '';
    document.getElementById('inpTingkat').value = 'Kelas X';
    document.getElementById('inpKelasNama').value = 'X - Kelas Jauh';
    document.getElementById('inpUsername').value = '';
    document.getElementById('inpPassword').value = '123456';
    if (document.getElementById('inpJk')) document.getElementById('inpJk').value = 'L';
    if (document.getElementById('inpNoHp')) document.getElementById('inpNoHp').value = '';
    document.getElementById('modalFormPeserta').classList.add('open');
    if (window.lucide) lucide.createIcons();
}

function bukaModalEdit(p) {
    document.getElementById('formModalTitle').innerHTML = '<i data-lucide="edit" style="width:20px;height:20px;color:#4f46e5;"></i> Edit Siswa Kelas Jauh';
    document.getElementById('inpId').value = p.id;
    document.getElementById('inpNama').value = p.nama_siswa;
    document.getElementById('inpNoPeserta').value = p.no_peserta;
    document.getElementById('inpNis').value = p.nis || '';
    document.getElementById('inpTingkat').value = p.tingkat;
    document.getElementById('inpKelasNama').value = p.kelas_nama;
    document.getElementById('inpUsername').value = p.username;
    document.getElementById('inpPassword').value = p.password_plain || '';
    if (document.getElementById('inpJk')) document.getElementById('inpJk').value = p.jenis_kelamin || 'L';
    if (document.getElementById('inpNoHp')) document.getElementById('inpNoHp').value = p.no_hp || '';
    document.getElementById('modalFormPeserta').classList.add('open');
    if (window.lucide) lucide.createIcons();
}

function bukaModalImport() {
    document.getElementById('modalImport').classList.add('open');
    if (window.lucide) lucide.createIcons();
}

// ==================== HANDLER PENGAWAS KELAS JAUH ====================
function bukaModalTambahPengawas() {
    document.getElementById('formModalTitlePengawas').innerHTML = '<i data-lucide="shield-check" style="width:20px;height:20px;color:#059669;"></i> Tambah Akun Pengawas Kelas Jauh';
    document.getElementById('inpPkjId').value = '0';
    document.getElementById('inpPkjNama').value = '';
    document.getElementById('inpPkjUsername').value = '';
    document.getElementById('inpPkjPassword').value = '123456';
    document.getElementById('inpPkjCabang').value = 'Cabang Kelas Jauh';
    document.getElementById('inpPkjNoHp').value = '';
    document.getElementById('inpPkjStatus').value = 'Aktif';
    document.getElementById('modalFormPengawas').classList.add('open');
    if (window.lucide) lucide.createIcons();
}

function bukaModalEditPengawas(pkj) {
    document.getElementById('formModalTitlePengawas').innerHTML = '<i data-lucide="edit-3" style="width:20px;height:20px;color:#059669;"></i> Edit Akun Pengawas Kelas Jauh';
    document.getElementById('inpPkjId').value = pkj.id;
    document.getElementById('inpPkjNama').value = pkj.nama;
    document.getElementById('inpPkjUsername').value = pkj.username;
    document.getElementById('inpPkjPassword').value = pkj.password_plain || '';
    document.getElementById('inpPkjCabang').value = pkj.lokasi_cabang || 'Cabang Kelas Jauh';
    document.getElementById('inpPkjNoHp').value = pkj.no_hp || '';
    document.getElementById('inpPkjStatus').value = pkj.status || 'Aktif';
    document.getElementById('modalFormPengawas').classList.add('open');
    if (window.lucide) lucide.createIcons();
}

function hapusPengawas(id, nama) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Hapus Akun Pengawas?',
            text: `Apakah Anda yakin ingin menghapus pengawas "${nama}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?php echo base_url('cbt/pengawas_kj/delete'); ?>';
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'id';
                inp.value = id;
                form.appendChild(inp);
                document.body.appendChild(form);
                form.submit();
            }
        });
    } else {
        if (confirm(`Apakah Anda yakin ingin menghapus akun pengawas "${nama}"?`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo base_url('cbt/pengawas_kj/delete'); ?>';
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'id';
            inp.value = id;
            form.appendChild(inp);
            document.body.appendChild(form);
            form.submit();
        }
    }
}

// Auto isi username dan rombel saat no peserta diketik
const noPesertaEl = document.getElementById('inpNoPeserta');
if (noPesertaEl) {
    noPesertaEl.addEventListener('input', function() {
        const val = this.value.trim().toLowerCase().replace(/[^a-z0-9]/g, '');
        const userInp = document.getElementById('inpUsername');
        if (userInp && document.getElementById('inpId').value === '0') {
            userInp.value = val;
        }
    });
}

const tingkatEl = document.getElementById('inpTingkat');
if (tingkatEl) {
    tingkatEl.addEventListener('change', function() {
        const t = this.value;
        const kInp = document.getElementById('inpKelasNama');
        if (document.getElementById('inpId').value === '0') {
            if (t === 'Kelas X') kInp.value = 'X - Kelas Jauh';
            else if (t === 'Kelas XI') kInp.value = 'XI - Kelas Jauh';
            else if (t === 'Kelas XII') kInp.value = 'XII - Kelas Jauh';
        }
    });
}
</script>
