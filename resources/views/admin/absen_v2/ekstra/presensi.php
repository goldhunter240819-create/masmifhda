<style>
    .z-avatar-premium { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); }
    .header-breadcrumb .parent { color: #0ea5e9; }
    
    .ekstra-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    .ekstra-card-header {
        padding: 1.25rem 1.5rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }
    .ekstra-card-body {
        padding: 1.5rem;
    }
    
    .status-btn-group {
        display: flex;
        gap: 6px;
    }
    .status-radio-label {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 14px;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
        border: 1.5px solid #e2e8f0;
        background: #ffffff;
        color: #64748b;
        transition: all 0.15s ease;
        user-select: none;
    }
    .status-radio-label:hover {
        border-color: #cbd5e1;
    }
    .status-radio-input {
        display: none;
    }
    /* Hadir */
    .status-radio-input[value="H"]:checked + .status-radio-label {
        background: #dcfce7;
        color: #15803d;
        border-color: #86efac;
    }
    /* Sakit */
    .status-radio-input[value="S"]:checked + .status-radio-label {
        background: #dbeafe;
        color: #1e40af;
        border-color: #93c5fd;
    }
    /* Izin */
    .status-radio-input[value="I"]:checked + .status-radio-label {
        background: #f3e8ff;
        color: #7e22ce;
        border-color: #d8b4fe;
    }
    /* Alpa */
    .status-radio-input[value="A"]:checked + .status-radio-label {
        background: #fee2e2;
        color: #b91c1c;
        border-color: #fca5a5;
    }
    
    .table-presensi th {
        background: #f1f5f9;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.85rem 1rem;
        border-bottom: 2px solid #e2e8f0;
    }
    .table-presensi td {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 0.85rem;
    }
    .table-presensi tr:hover td {
        background: #f8fafc;
    }
</style>

<div class="siakad-container">
    <?php include __DIR__ . '/../sidebar.php'; ?>
    
    <div class="z-main">
        <?php $page_badge = 'Admin Portal'; include __DIR__.'/../../../siakad/_siakad_header.php'; ?>

        <div class="z-scroll" style="padding: 1.5rem 2rem;">

            <!-- Alerts -->
            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'saved'): ?>
            <div style="background:#dcfce7;border:1px solid #86efac;border-radius:12px;padding:1rem 1.5rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:10px;color:#15803d;font-weight:700;">
                <i data-lucide="check-circle" style="width:20px;"></i>
                Presensi dan Jurnal Kegiatan Ekstrakurikuler berhasil disimpan!
            </div>
            <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
            <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:12px;padding:1rem 1.5rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:10px;color:#b91c1c;font-weight:700;">
                <i data-lucide="trash-2" style="width:20px;"></i>
                Riwayat pertemuan dan data presensi berhasil dihapus!
            </div>
            <?php endif; ?>

            <!-- Header Filter & Quick Action -->
            <div style="background:white;border-radius:16px;padding:1.25rem 1.5rem;border:1px solid #f1f5f9;margin-bottom:1.5rem;box-shadow:0 4px 15px rgba(0,0,0,0.02);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:15px;">
                <form method="GET" action="<?php echo base_url('admin/absen_v2/ekstra'); ?>" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;flex:1;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <i data-lucide="award" style="color:#0ea5e9;width:20px;"></i>
                        <span style="font-weight:800;font-size:0.9rem;color:#0f172a;">Cabang Ekstra:</span>
                    </div>
                    <select name="ekstra" onchange="this.form.submit()" style="border:1.5px solid #cbd5e1;border-radius:10px;padding:7px 14px;font-weight:700;color:#0f172a;font-size:0.85rem;background:#fff;outline:none;min-width:200px;">
                        <?php foreach($ekstraList as $el): ?>
                            <option value="<?php echo htmlspecialchars($el['nama_ekstra']); ?>" <?php echo $selected_ekstra === $el['nama_ekstra'] ? 'selected' : ''; ?>>
                                🏆 <?php echo htmlspecialchars($el['nama_ekstra']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <div style="display:flex;align-items:center;gap:8px;margin-left:10px;">
                        <i data-lucide="calendar" style="color:#64748b;width:18px;"></i>
                        <span style="font-weight:700;font-size:0.85rem;color:#475569;">Tanggal:</span>
                    </div>
                    <input type="date" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>" onchange="this.form.submit()" style="border:1.5px solid #cbd5e1;border-radius:10px;padding:7px 12px;font-weight:700;color:#0f172a;font-size:0.85rem;outline:none;">
                    
                    <button type="submit" style="background:#0ea5e9;color:white;border:none;padding:8px 16px;border-radius:10px;font-weight:700;font-size:0.85rem;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                        <i data-lucide="search" style="width:14px;"></i> Tampilkan
                    </button>
                </form>

                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <a href="<?php echo base_url('admin/absen_v2/ekstra/seed-dummy'); ?>" onclick="return confirm('Generate data dummy ROHIS (10 pertemuan & 30 siswa) sekarang?');" style="background:#fef3c7;color:#b45309;text-decoration:none;padding:8px 14px;border-radius:10px;font-weight:800;font-size:0.85rem;display:inline-flex;align-items:center;gap:6px;border:1px solid #fde68a;">
                        <i data-lucide="sparkles" style="width:16px;"></i> + Dummy ROHIS (10 Ptm)
                    </a>
                    <a href="<?php echo base_url('admin/absen_v2/ekstra/rekap?ekstra=' . urlencode($selected_ekstra)); ?>" style="background:#f1f5f9;color:#334155;text-decoration:none;padding:8px 16px;border-radius:10px;font-weight:700;font-size:0.85rem;display:inline-flex;align-items:center;gap:6px;border:1px solid #e2e8f0;">
                        <i data-lucide="file-spreadsheet" style="width:16px;color:#0284c7;"></i> Rekap Presensi
                    </a>
                    <a href="<?php echo base_url('admin/absen_v2/ekstra/anggota?ekstra=' . urlencode($selected_ekstra)); ?>" style="background:#0284c7;color:white;text-decoration:none;padding:8px 16px;border-radius:10px;font-weight:700;font-size:0.85rem;display:inline-flex;align-items:center;gap:6px;">
                        <i data-lucide="users" style="width:16px;"></i> Kelola Anggota
                    </a>
                </div>
            </div>

            <!-- Banner Info Cabang Ekstra -->
            <div style="background:linear-gradient(135deg, #0284c7 0%, #0369a1 100%);color:white;border-radius:16px;padding:1.5rem 2rem;margin-bottom:1.5rem;box-shadow:0 6px 20px rgba(2,132,199,0.2);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px;">
                <div>
                    <div style="font-size:0.75rem;font-weight:800;letter-spacing:1px;text-transform:uppercase;opacity:0.85;margin-bottom:4px;">
                        EKSTRAKURIKULER • TAHUN AJARAN <?php echo $active_year_name; ?> (<?php echo $active_semester; ?>)
                    </div>
                    <h2 style="margin:0 0 8px 0;font-size:1.6rem;font-weight:900;">
                        🏆 <?php echo htmlspecialchars($selected_ekstra); ?>
                    </h2>
                    <div style="display:flex;align-items:center;gap:20px;font-size:0.85rem;opacity:0.95;flex-wrap:wrap;">
                        <span><i data-lucide="user-check" style="width:16px;display:inline;vertical-align:middle;margin-right:4px;"></i> <strong>Pembina:</strong> <?php echo htmlspecialchars($ekstraDetail['nama_pembina'] ?? 'Belum ditentukan'); ?></span>
                        <span><i data-lucide="clock" style="width:16px;display:inline;vertical-align:middle;margin-right:4px;"></i> <strong>Jadwal:</strong> <?php echo htmlspecialchars(($ekstraDetail['jadwal_hari'] ?? 'Sabtu') . ', ' . ($ekstraDetail['jadwal_waktu'] ?? '14.00 - 16.00 WIB')); ?></span>
                    </div>
                </div>
                <div style="display:flex;gap:15px;text-align:right;">
                    <div style="background:rgba(255,255,255,0.15);padding:10px 18px;border-radius:12px;border:1px solid rgba(255,255,255,0.2);">
                        <div style="font-size:1.6rem;font-weight:900;line-height:1;"><?php echo count($anggotaList); ?></div>
                        <div style="font-size:0.7rem;font-weight:800;letter-spacing:0.5px;text-transform:uppercase;opacity:0.85;margin-top:4px;">Anggota Aktif</div>
                    </div>
                    <div style="background:rgba(255,255,255,0.15);padding:10px 18px;border-radius:12px;border:1px solid rgba(255,255,255,0.2);">
                        <div style="font-size:1.6rem;font-weight:900;line-height:1;"><?php echo $pertemuan_ke; ?></div>
                        <div style="font-size:0.7rem;font-weight:800;letter-spacing:0.5px;text-transform:uppercase;opacity:0.85;margin-top:4px;">Pertemuan Ke</div>
                    </div>
                </div>
            </div>

            <!-- FORM PRESENSI KEGIATAN -->
            <form method="POST" action="<?php echo base_url('admin/absen_v2/ekstra/save-presensi'); ?>" enctype="multipart/form-data">
                <input type="hidden" name="nama_ekstra" value="<?php echo htmlspecialchars($selected_ekstra); ?>">
                <input type="hidden" name="jurnal_id" value="<?php echo $currentJurnal ? htmlspecialchars($currentJurnal['id']) : ''; ?>">

                <div class="ekstra-card">
                    <div class="ekstra-card-header">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <i data-lucide="clipboard-pen" style="color:#0ea5e9;width:20px;"></i>
                            <h3 style="margin:0;font-size:0.95rem;font-weight:800;color:#0f172a;">
                                Form Jurnal & Presensi Pertemuan — <?php echo date('d F Y', strtotime($tanggal)); ?>
                            </h3>
                            <?php if ($currentJurnal): ?>
                                <span style="background:#e0f2fe;color:#0284c7;font-size:0.75rem;padding:3px 10px;border-radius:100px;font-weight:800;">
                                    Mode Edit
                                </span>
                            <?php endif; ?>
                        </div>
                        <div style="font-size:0.8rem;color:#64748b;font-weight:600;">
                            Isi materi latihan & checklist kehadiran siswa di bawah ini
                        </div>
                    </div>

                    <div class="ekstra-card-body">
                        <!-- Baris Input Materi, Jam, Pertemuan -->
                        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:15px;margin-bottom:1.5rem;background:#f8fafc;padding:1.25rem;border-radius:12px;border:1px solid #e2e8f0;">
                            <div>
                                <label style="display:block;font-size:0.75rem;font-weight:800;color:#475569;margin-bottom:6px;text-transform:uppercase;">
                                    Tanggal Pertemuan
                                </label>
                                <input type="date" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>" required style="width:100%;border:1.5px solid #cbd5e1;border-radius:8px;padding:8px 12px;font-weight:700;font-size:0.85rem;background:#fff;">
                            </div>
                            <div>
                                <label style="display:block;font-size:0.75rem;font-weight:800;color:#475569;margin-bottom:6px;text-transform:uppercase;">
                                    Pertemuan Ke-
                                </label>
                                <input type="number" name="pertemuan_ke" value="<?php echo $pertemuan_ke; ?>" min="1" required style="width:100%;border:1.5px solid #cbd5e1;border-radius:8px;padding:8px 12px;font-weight:700;font-size:0.85rem;background:#fff;">
                            </div>
                            <div>
                                <label style="display:block;font-size:0.75rem;font-weight:800;color:#475569;margin-bottom:6px;text-transform:uppercase;">
                                    Jam Mulai & Selesai
                                </label>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <input type="text" name="jam_mulai" value="<?php echo htmlspecialchars($currentJurnal['jam_mulai'] ?? '14:00'); ?>" placeholder="14:00" style="width:50%;border:1.5px solid #cbd5e1;border-radius:8px;padding:8px 10px;font-weight:700;font-size:0.85rem;background:#fff;text-align:center;">
                                    <span style="color:#94a3b8;font-weight:700;">s/d</span>
                                    <input type="text" name="jam_selesai" value="<?php echo htmlspecialchars($currentJurnal['jam_selesai'] ?? '16:00'); ?>" placeholder="16:00" style="width:50%;border:1.5px solid #cbd5e1;border-radius:8px;padding:8px 10px;font-weight:700;font-size:0.85rem;background:#fff;text-align:center;">
                                </div>
                            </div>
                            <div>
                                <label style="display:block;font-size:0.75rem;font-weight:800;color:#475569;margin-bottom:6px;text-transform:uppercase;">
                                    Foto Dokumentasi (Opsional)
                                </label>
                                <input type="file" name="dokumentasi" accept="image/*,.pdf" style="width:100%;border:1.5px solid #cbd5e1;border-radius:8px;padding:5px 8px;font-size:0.8rem;background:#fff;">
                                <?php if (!empty($currentJurnal['dokumentasi'])): ?>
                                    <div style="margin-top:4px;font-size:0.75rem;color:#0284c7;font-weight:700;">
                                        <i data-lucide="image" style="width:14px;vertical-align:middle;"></i> File tersimpan: 
                                        <a href="<?php echo base_url('uploads/ekstra_jurnal/' . $currentJurnal['dokumentasi']); ?>" target="_blank" style="color:#0284c7;text-decoration:underline;">Lihat File</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Materi / Topik Latihan -->
                        <div style="margin-bottom:1.5rem;">
                            <label style="display:block;font-size:0.75rem;font-weight:800;color:#475569;margin-bottom:6px;text-transform:uppercase;">
                                Materi / Topik Kegiatan Latihan
                            </label>
                            <input type="text" name="materi" value="<?php echo htmlspecialchars($currentJurnal['materi'] ?? 'Latihan Rutin & Pemantapan Materi Ekstrakurikuler'); ?>" required placeholder="Contoh: Latihan Baris Berbaris (PBB) dan Tali Temali" style="width:100%;border:1.5px solid #cbd5e1;border-radius:8px;padding:10px 14px;font-weight:700;font-size:0.9rem;background:#fff;outline:none;">
                        </div>

                        <!-- Toolbar Tombol Cepat Presensi -->
                        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:1rem;padding-bottom:10px;border-bottom:1px solid #f1f5f9;">
                            <div style="font-weight:800;color:#0f172a;font-size:0.95rem;display:flex;align-items:center;gap:8px;">
                                <i data-lucide="users" style="color:#0ea5e9;width:18px;"></i>
                                Daftar Anggota Ekstrakurikuler (<?php echo count($anggotaList); ?> Siswa)
                            </div>
                            <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                <span style="font-size:0.75rem;font-weight:700;color:#64748b;align-self:center;margin-right:4px;">Quick Action:</span>
                                <button type="button" onclick="setAllStatus('H')" style="background:#dcfce7;color:#15803d;border:1px solid #86efac;padding:5px 12px;border-radius:8px;font-weight:800;font-size:0.75rem;cursor:pointer;">
                                    ✅ Hadirkan Semua
                                </button>
                                <button type="button" onclick="setAllStatus('S')" style="background:#dbeafe;color:#1e40af;border:1px solid #93c5fd;padding:5px 12px;border-radius:8px;font-weight:800;font-size:0.75rem;cursor:pointer;">
                                    🤒 Sakit Semua
                                </button>
                                <button type="button" onclick="setAllStatus('I')" style="background:#f3e8ff;color:#7e22ce;border:1px solid #d8b4fe;padding:5px 12px;border-radius:8px;font-weight:800;font-size:0.75rem;cursor:pointer;">
                                    📝 Izin Semua
                                </button>
                                <button type="button" onclick="setAllStatus('A')" style="background:#fee2e2;color:#b91c1c;border:1px solid #fca5a5;padding:5px 12px;border-radius:8px;font-weight:800;font-size:0.75rem;cursor:pointer;">
                                    ❌ Alpa Semua
                                </button>
                            </div>
                        </div>

                        <!-- TABEL ANGGOTA & STATUS ABSEN -->
                        <?php if (empty($anggotaList)): ?>
                            <div style="text-align:center;padding:3rem 1rem;background:#f8fafc;border-radius:12px;border:2px dashed #cbd5e1;">
                                <i data-lucide="user-x" style="width:48px;height:48px;color:#94a3b8;margin-bottom:10px;"></i>
                                <h4 style="margin:0 0 6px 0;font-weight:800;color:#334155;font-size:1.1rem;">Belum Ada Anggota Siswa Terdaftar</h4>
                                <p style="margin:0 0 1.25rem 0;color:#64748b;font-size:0.85rem;">
                                    Cabang ekstrakurikuler <strong><?php echo htmlspecialchars($selected_ekstra); ?></strong> belum memiliki anggota siswa.
                                </p>
                                <a href="<?php echo base_url('admin/absen_v2/ekstra/anggota?ekstra=' . urlencode($selected_ekstra)); ?>" style="background:#0284c7;color:white;text-decoration:none;padding:10px 20px;border-radius:10px;font-weight:800;font-size:0.85rem;display:inline-flex;align-items:center;gap:8px;">
                                    <i data-lucide="user-plus" style="width:16px;"></i> + Tambah Anggota Siswa Sekarang
                                </a>
                            </div>
                        <?php else: ?>
                            <div style="overflow-x:auto;border-radius:12px;border:1px solid #e2e8f0;margin-bottom:1.5rem;">
                                <table class="table-presensi" style="width:100%;border-collapse:collapse;">
                                    <thead>
                                        <tr>
                                            <th style="width:50px;text-align:center;">No</th>
                                            <th style="width:120px;">NIS</th>
                                            <th>Nama Lengkap Siswa</th>
                                            <th style="width:110px;text-align:center;">Kelas</th>
                                            <th style="width:280px;text-align:center;">Status Kehadiran</th>
                                            <th style="width:200px;">Catatan (Opsional)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $no = 1; 
                                        foreach($anggotaList as $ag): 
                                            $sid = $ag['siswa_id'];
                                            // Default: jika ada di absenMap pakai itu, jika baru default 'H' (Hadir)
                                            $currSt = $absenMap[$sid] ?? 'H';
                                            $currCat = $catatanMap[$sid] ?? '';
                                        ?>
                                        <tr>
                                            <td style="text-align:center;font-weight:700;color:#94a3b8;"><?php echo $no++; ?></td>
                                            <td style="font-weight:700;color:#334155;"><?php echo htmlspecialchars($ag['nis'] ?: '-'); ?></td>
                                            <td>
                                                <div style="font-weight:800;color:#0f172a;"><?php echo htmlspecialchars($ag['nama']); ?></div>
                                                <div style="font-size:0.75rem;color:#64748b;"><?php echo htmlspecialchars($ag['jenis_kelamin'] == 'L' ? 'Laki-laki' : ($ag['jenis_kelamin'] == 'P' ? 'Perempuan' : '')); ?></div>
                                            </td>
                                            <td style="text-align:center;">
                                                <span style="background:#f1f5f9;color:#334155;padding:4px 10px;border-radius:8px;font-weight:800;font-size:0.8rem;border:1px solid #e2e8f0;">
                                                    <?php echo htmlspecialchars($ag['nama_kelas']); ?>
                                                </span>
                                            </td>
                                            <td style="text-align:center;">
                                                <div class="status-btn-group" style="justify-content:center;">
                                                    <label>
                                                        <input type="radio" name="absensi[<?php echo $sid; ?>]" value="H" class="status-radio-input" <?php echo $currSt === 'H' ? 'checked' : ''; ?>>
                                                        <span class="status-radio-label">H</span>
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="absensi[<?php echo $sid; ?>]" value="S" class="status-radio-input" <?php echo $currSt === 'S' ? 'checked' : ''; ?>>
                                                        <span class="status-radio-label">S</span>
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="absensi[<?php echo $sid; ?>]" value="I" class="status-radio-input" <?php echo $currSt === 'I' ? 'checked' : ''; ?>>
                                                        <span class="status-radio-label">I</span>
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="absensi[<?php echo $sid; ?>]" value="A" class="status-radio-input" <?php echo $currSt === 'A' ? 'checked' : ''; ?>>
                                                        <span class="status-radio-label">A</span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" name="catatan[<?php echo $sid; ?>]" value="<?php echo htmlspecialchars($currCat); ?>" placeholder="Catatan..." style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:6px 10px;font-size:0.8rem;background:#fff;outline:none;">
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Tombol Simpan Presensi -->
                            <div style="display:flex;justify-content:flex-end;gap:12px;align-items:center;">
                                <button type="submit" style="background:linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);color:white;border:none;padding:12px 28px;border-radius:12px;font-weight:800;font-size:0.95rem;cursor:pointer;display:inline-flex;align-items:center;gap:8px;box-shadow:0 4px 15px rgba(2,132,199,0.3);transition:0.2s;">
                                    <i data-lucide="save" style="width:18px;"></i> Simpan Presensi Pertemuan Ini
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </form>

            <!-- RIWAYAT PERTEMUAN YANG SUDAH DIINPUT -->
            <div class="ekstra-card" style="margin-top:2rem;">
                <div class="ekstra-card-header">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <i data-lucide="history" style="color:#0ea5e9;width:20px;"></i>
                        <h3 style="margin:0;font-size:0.95rem;font-weight:800;color:#0f172a;">
                            Riwayat Jurnal & Presensi Pertemuan Terakhir — <?php echo htmlspecialchars($selected_ekstra); ?>
                        </h3>
                    </div>
                    <span style="font-size:0.8rem;color:#64748b;font-weight:700;">
                        <?php echo count($riwayatJurnal); ?> Pertemuan Tersimpan
                    </span>
                </div>
                <div class="ekstra-card-body" style="padding:0;">
                    <?php if (empty($riwayatJurnal)): ?>
                        <div style="text-align:center;padding:2.5rem;color:#94a3b8;font-size:0.9rem;">
                            <i data-lucide="calendar-x" style="width:36px;height:36px;margin-bottom:8px;opacity:0.5;"></i>
                            <div>Belum ada riwayat pertemuan/jurnal untuk cabang ekstrakurikuler ini.</div>
                        </div>
                    <?php else: ?>
                        <div style="overflow-x:auto;">
                            <table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
                                <thead>
                                    <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;text-align:left;">
                                        <th style="padding:10px 16px;font-size:0.75rem;font-weight:800;color:#64748b;text-transform:uppercase;width:50px;">No</th>
                                        <th style="padding:10px 16px;font-size:0.75rem;font-weight:800;color:#64748b;text-transform:uppercase;width:120px;">Tanggal</th>
                                        <th style="padding:10px 16px;font-size:0.75rem;font-weight:800;color:#64748b;text-transform:uppercase;width:90px;text-align:center;">Ptm Ke</th>
                                        <th style="padding:10px 16px;font-size:0.75rem;font-weight:800;color:#64748b;text-transform:uppercase;">Materi / Kegiatan</th>
                                        <th style="padding:10px 16px;font-size:0.75rem;font-weight:800;color:#64748b;text-transform:uppercase;text-align:center;width:180px;">Kehadiran Siswa</th>
                                        <th style="padding:10px 16px;font-size:0.75rem;font-weight:800;color:#64748b;text-transform:uppercase;text-align:center;width:120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $rjNo = 1; foreach($riwayatJurnal as $rj): ?>
                                    <tr style="border-bottom:1px solid #f1f5f9;">
                                        <td style="padding:12px 16px;color:#94a3b8;font-weight:700;"><?php echo $rjNo++; ?></td>
                                        <td style="padding:12px 16px;font-weight:800;color:#1e293b;white-space:nowrap;">
                                            <?php echo date('d/m/Y', strtotime($rj['tanggal'])); ?>
                                            <div style="font-size:0.7rem;color:#64748b;font-weight:600;">
                                                <?php echo htmlspecialchars(($rj['jam_mulai'] ?? '14:00') . ' - ' . ($rj['jam_selesai'] ?? '16:00')); ?>
                                            </div>
                                        </td>
                                        <td style="padding:12px 16px;text-align:center;">
                                            <span style="background:#e0f2fe;color:#0284c7;font-weight:900;padding:4px 10px;border-radius:8px;font-size:0.8rem;">
                                                Ke-<?php echo htmlspecialchars($rj['pertemuan_ke'] ?? '1'); ?>
                                            </span>
                                        </td>
                                        <td style="padding:12px 16px;font-weight:600;color:#334155;">
                                            <?php echo htmlspecialchars($rj['materi']); ?>
                                            <?php if (!empty($rj['dokumentasi'])): ?>
                                                <div style="margin-top:4px;">
                                                    <a href="<?php echo base_url('uploads/ekstra_jurnal/' . $rj['dokumentasi']); ?>" target="_blank" style="font-size:0.75rem;color:#0ea5e9;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                                                        <i data-lucide="image" style="width:12px;"></i> Foto Kegiatan
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding:12px 16px;text-align:center;">
                                            <div style="display:flex;justify-content:center;gap:4px;font-size:0.75rem;font-weight:800;">
                                                <span style="background:#dcfce7;color:#15803d;padding:2px 8px;border-radius:6px;" title="Hadir">H: <?php echo intval($rj['tot_hadir']); ?></span>
                                                <span style="background:#dbeafe;color:#1e40af;padding:2px 8px;border-radius:6px;" title="Sakit">S: <?php echo intval($rj['tot_sakit']); ?></span>
                                                <span style="background:#f3e8ff;color:#7e22ce;padding:2px 8px;border-radius:6px;" title="Izin">I: <?php echo intval($rj['tot_izin']); ?></span>
                                                <span style="background:#fee2e2;color:#b91c1c;padding:2px 8px;border-radius:6px;" title="Alpa">A: <?php echo intval($rj['tot_alpa']); ?></span>
                                            </div>
                                        </td>
                                        <td style="padding:12px 16px;text-align:center;">
                                            <div style="display:flex;justify-content:center;gap:6px;">
                                                <a href="<?php echo base_url('admin/absen_v2/ekstra?ekstra=' . urlencode($selected_ekstra) . '&jurnal_id=' . $rj['id']); ?>" style="background:#eff6ff;color:#2563eb;padding:5px 8px;border-radius:6px;text-decoration:none;font-weight:700;font-size:0.75rem;border:1px solid #bfdbfe;" title="Edit Presensi">
                                                    <i data-lucide="pencil" style="width:13px;height:13px;vertical-align:middle;"></i>
                                                </a>
                                                <form method="POST" action="<?php echo base_url('admin/absen_v2/ekstra/delete-presensi'); ?>" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pertemuan ini?');" style="display:inline;">
                                                    <input type="hidden" name="jurnal_id" value="<?php echo $rj['id']; ?>">
                                                    <input type="hidden" name="nama_ekstra" value="<?php echo htmlspecialchars($selected_ekstra); ?>">
                                                    <button type="submit" style="background:#fef2f2;color:#ef4444;border:1px solid #fecaca;padding:5px 8px;border-radius:6px;cursor:pointer;font-size:0.75rem;" title="Hapus Pertemuan">
                                                        <i data-lucide="trash-2" style="width:13px;height:13px;vertical-align:middle;"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function setAllStatus(status) {
    document.querySelectorAll('.status-radio-input[value="' + status + '"]').forEach(function(r) {
        r.checked = true;
    });
}
</script>
