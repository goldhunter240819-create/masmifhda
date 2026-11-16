<style>
    .z-avatar-premium { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); }
    .header-breadcrumb .parent { color: #0ea5e9; }

    .nav-tabs-ekstra {
        display: flex;
        gap: 10px;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 2px;
    }
    .tab-btn {
        padding: 10px 20px;
        border-radius: 10px 10px 0 0;
        font-weight: 800;
        font-size: 0.9rem;
        cursor: pointer;
        border: none;
        background: transparent;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    .tab-btn.active {
        background: white;
        color: #0284c7;
        border: 2px solid #e2e8f0;
        border-bottom-color: white;
        margin-bottom: -4px;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.02);
    }
    .tab-pane {
        display: none;
    }
    .tab-pane.active {
        display: block;
    }

    .tbl-data th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        padding: 10px 14px;
        border-bottom: 2px solid #e2e8f0;
    }
    .tbl-data td {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
        vertical-align: middle;
    }
    .tbl-data tr:hover td {
        background: #fafafa;
    }

    /* Modal Styles */
    .ekstra-modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(3px);
    }
    .ekstra-modal.show {
        display: flex;
    }
    .ekstra-modal-content {
        background: white;
        border-radius: 20px;
        width: 100%;
        max-width: 580px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    }
</style>

<div class="siakad-container">
    <?php include __DIR__ . '/../sidebar.php'; ?>
    
    <div class="z-main">
        <?php $page_badge = 'Admin Portal'; include __DIR__.'/../../../siakad/_siakad_header.php'; ?>

        <div class="z-scroll" style="padding: 1.5rem 2rem;">

            <!-- Alerts -->
            <?php if (isset($_GET['msg'])): ?>
                <?php if ($_GET['msg'] === 'cabang_saved'): ?>
                    <div style="background:#dcfce7;border:1px solid #86efac;border-radius:12px;padding:1rem 1.5rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:10px;color:#15803d;font-weight:700;">
                        <i data-lucide="check-circle" style="width:20px;"></i> Data Cabang Ekstrakurikuler berhasil disimpan!
                    </div>
                <?php elseif ($_GET['msg'] === 'cabang_deleted'): ?>
                    <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:12px;padding:1rem 1.5rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:10px;color:#b91c1c;font-weight:700;">
                        <i data-lucide="trash-2" style="width:20px;"></i> Cabang Ekstrakurikuler berhasil dihapus!
                    </div>
                <?php elseif ($_GET['msg'] === 'anggota_added'): ?>
                    <div style="background:#dcfce7;border:1px solid #86efac;border-radius:12px;padding:1rem 1.5rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:10px;color:#15803d;font-weight:700;">
                        <i data-lucide="user-check" style="width:20px;"></i> Anggota siswa berhasil ditambahkan ke ekstrakurikuler!
                    </div>
                <?php elseif ($_GET['msg'] === 'anggota_deleted'): ?>
                    <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:12px;padding:1rem 1.5rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:10px;color:#b91c1c;font-weight:700;">
                        <i data-lucide="user-minus" style="width:20px;"></i> Anggota siswa berhasil dikeluarkan dari ekstrakurikuler!
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- TABS NAVIGATION -->
            <div class="nav-tabs-ekstra">
                <button class="tab-btn active" onclick="switchTab('tab-anggota', this)">
                    <i data-lucide="users" style="width:18px;"></i> Daftar Anggota Siswa
                </button>
                <button class="tab-btn" onclick="switchTab('tab-cabang', this)">
                    <i data-lucide="award" style="width:18px;"></i> Cabang Ekstrakurikuler & Pembina
                </button>
            </div>

            <!-- TAB 1: KELOLA ANGGOTA SISWA -->
            <div id="tab-anggota" class="tab-pane active">
                <div style="background:white;border-radius:16px;padding:1.25rem 1.5rem;border:1px solid #f1f5f9;margin-bottom:1.5rem;box-shadow:0 4px 15px rgba(0,0,0,0.02);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:15px;">
                    <form method="GET" action="<?php echo base_url('admin/absen_v2/ekstra/anggota'); ?>" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <i data-lucide="award" style="color:#0ea5e9;width:20px;"></i>
                            <span style="font-weight:800;font-size:0.85rem;color:#0f172a;">Pilih Ekstrakurikuler:</span>
                        </div>
                        <select name="ekstra" onchange="this.form.submit()" style="border:1.5px solid #cbd5e1;border-radius:10px;padding:7px 14px;font-weight:700;color:#0f172a;font-size:0.85rem;background:#fff;outline:none;min-width:220px;">
                            <?php foreach($cabangList as $c): ?>
                                <option value="<?php echo htmlspecialchars($c['nama_ekstra']); ?>" <?php echo $selected_ekstra === $c['nama_ekstra'] ? 'selected' : ''; ?>>
                                    🏆 <?php echo htmlspecialchars($c['nama_ekstra']); ?> (<?php echo $c['total_anggota']; ?> Siswa)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>

                    <div style="display:flex;gap:10px;">
                        <button type="button" onclick="openModalTambahAnggota()" style="background:linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);color:white;border:none;padding:9px 18px;border-radius:10px;font-weight:800;font-size:0.85rem;cursor:pointer;display:inline-flex;align-items:center;gap:8px;box-shadow:0 4px 12px rgba(2,132,199,0.3);">
                            <i data-lucide="user-plus" style="width:16px;"></i> + Tambah Anggota Siswa
                        </button>
                        <a href="<?php echo base_url('admin/absen_v2/ekstra?ekstra=' . urlencode($selected_ekstra)); ?>" style="background:#f1f5f9;color:#334155;text-decoration:none;padding:9px 16px;border-radius:10px;font-weight:700;font-size:0.85rem;display:inline-flex;align-items:center;gap:6px;border:1px solid #e2e8f0;">
                            <i data-lucide="clipboard-pen" style="width:16px;color:#0284c7;"></i> Input Presensi
                        </a>
                    </div>
                </div>

                <div style="background:white;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.02);">
                    <div style="padding:1.25rem 1.5rem;background:#f8fafc;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
                        <div>
                            <h3 style="margin:0;font-size:1.05rem;font-weight:800;color:#0f172a;">
                                Daftar Anggota Siswa — <?php echo htmlspecialchars($selected_ekstra); ?>
                            </h3>
                            <div style="font-size:0.8rem;color:#64748b;font-weight:600;margin-top:2px;">
                                Tahun Ajaran <?php echo $active_year_name; ?> (<?php echo $active_semester; ?>)
                            </div>
                        </div>
                        <span style="background:#e0f2fe;color:#0369a1;font-weight:800;font-size:0.85rem;padding:6px 14px;border-radius:100px;">
                            <?php echo count($anggotaList); ?> Anggota Terdaftar
                        </span>
                    </div>

                    <?php if (empty($anggotaList)): ?>
                        <div style="text-align:center;padding:3.5rem 1rem;color:#64748b;">
                            <i data-lucide="users" style="width:48px;height:48px;color:#cbd5e1;margin-bottom:10px;"></i>
                            <h4 style="margin:0 0 6px 0;font-weight:800;color:#334155;">Belum Ada Anggota Siswa</h4>
                            <p style="margin:0 0 1.25rem 0;font-size:0.85rem;">Klik tombol di bawah untuk menambahkan siswa ke cabang ekstra ini.</p>
                            <button type="button" onclick="openModalTambahAnggota()" style="background:#0284c7;color:white;border:none;padding:9px 18px;border-radius:10px;font-weight:800;font-size:0.85rem;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                                <i data-lucide="user-plus" style="width:16px;"></i> + Tambah Siswa Sekarang
                            </button>
                        </div>
                    <?php else: ?>
                        <div style="overflow-x:auto;">
                            <table class="tbl-data" style="width:100%;border-collapse:collapse;">
                                <thead>
                                    <tr>
                                        <th style="width:50px;text-align:center;">No</th>
                                        <th style="width:120px;">NIS</th>
                                        <th>Nama Lengkap Siswa</th>
                                        <th style="width:120px;text-align:center;">Kelas</th>
                                        <th style="width:130px;text-align:center;">Jenis Kelamin</th>
                                        <th style="width:110px;text-align:center;">Status</th>
                                        <th style="width:90px;text-align:center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach($anggotaList as $ag): ?>
                                    <tr>
                                        <td style="text-align:center;color:#94a3b8;font-weight:700;"><?php echo $no++; ?></td>
                                        <td style="font-weight:700;color:#334155;"><?php echo htmlspecialchars($ag['nis'] ?: '-'); ?></td>
                                        <td>
                                            <div style="font-weight:800;color:#0f172a;font-size:0.9rem;"><?php echo htmlspecialchars($ag['nama']); ?></div>
                                            <div style="font-size:0.75rem;color:#94a3b8;">NISN: <?php echo htmlspecialchars($ag['nisn'] ?: '-'); ?></div>
                                        </td>
                                        <td style="text-align:center;">
                                            <span style="background:#f1f5f9;color:#334155;font-weight:800;font-size:0.8rem;padding:4px 10px;border-radius:6px;border:1px solid #e2e8f0;">
                                                <?php echo htmlspecialchars($ag['nama_kelas']); ?>
                                            </span>
                                        </td>
                                        <td style="text-align:center;color:#475569;font-weight:600;">
                                            <?php echo htmlspecialchars($ag['jenis_kelamin'] == 'L' ? 'Laki-laki' : ($ag['jenis_kelamin'] == 'P' ? 'Perempuan' : '-')); ?>
                                        </td>
                                        <td style="text-align:center;">
                                            <span style="background:#dcfce7;color:#15803d;padding:3px 10px;border-radius:100px;font-weight:800;font-size:0.75rem;">
                                                Aktif
                                            </span>
                                        </td>
                                        <td style="text-align:center;">
                                            <form method="POST" action="<?php echo base_url('admin/absen_v2/ekstra/delete-anggota'); ?>" onsubmit="return confirm('Keluarkan siswa ini dari ekstrakurikuler?');" style="display:inline;">
                                                <input type="hidden" name="id" value="<?php echo $ag['id']; ?>">
                                                <input type="hidden" name="nama_ekstra" value="<?php echo htmlspecialchars($selected_ekstra); ?>">
                                                <button type="submit" style="background:#fef2f2;color:#ef4444;border:1px solid #fecaca;padding:6px 10px;border-radius:8px;cursor:pointer;font-size:0.8rem;" title="Keluarkan Siswa">
                                                    <i data-lucide="trash-2" style="width:14px;height:14px;vertical-align:middle;"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- TAB 2: KELOLA CABANG EKSTRAKURIKULER & PEMBINA -->
            <div id="tab-cabang" class="tab-pane">
                <div style="background:white;border-radius:16px;padding:1.25rem 1.5rem;border:1px solid #f1f5f9;margin-bottom:1.5rem;box-shadow:0 4px 15px rgba(0,0,0,0.02);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:15px;">
                    <div>
                        <h3 style="margin:0;font-size:1.1rem;font-weight:800;color:#0f172a;">Master Cabang Ekstrakurikuler</h3>
                        <div style="font-size:0.8rem;color:#64748b;">Kelola daftar cabang, pembina, serta jadwal kegiatan ekstrakurikuler</div>
                    </div>
                    <button type="button" onclick="openModalTambahCabang()" style="background:linear-gradient(135deg, #10b981 0%, #059669 100%);color:white;border:none;padding:9px 18px;border-radius:10px;font-weight:800;font-size:0.85rem;cursor:pointer;display:inline-flex;align-items:center;gap:8px;box-shadow:0 4px 12px rgba(16,185,129,0.3);">
                        <i data-lucide="plus" style="width:16px;"></i> + Tambah Cabang Ekstra Baru
                    </button>
                </div>

                <div style="background:white;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.02);">
                    <div style="overflow-x:auto;">
                        <table class="tbl-data" style="width:100%;border-collapse:collapse;">
                            <thead>
                                <tr>
                                    <th style="width:50px;text-align:center;">No</th>
                                    <th>Nama Cabang Ekstrakurikuler</th>
                                    <th>Guru Pembina</th>
                                    <th>Jadwal Kegiatan</th>
                                    <th style="width:110px;text-align:center;">Anggota</th>
                                    <th style="width:110px;text-align:center;">Pertemuan</th>
                                    <th style="width:120px;text-align:center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $cbNo = 1; foreach($cabangList as $c): ?>
                                <tr>
                                    <td style="text-align:center;color:#94a3b8;font-weight:700;"><?php echo $cbNo++; ?></td>
                                    <td>
                                        <div style="font-weight:800;color:#0f172a;font-size:0.95rem;">
                                            🏆 <?php echo htmlspecialchars($c['nama_ekstra']); ?>
                                        </div>
                                        <?php if (!empty($c['target_pencapaian'])): ?>
                                            <div style="font-size:0.75rem;color:#64748b;margin-top:2px;"><?php echo htmlspecialchars($c['target_pencapaian']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-weight:700;color:#0284c7;">
                                            <?php echo htmlspecialchars($c['nama_pembina'] ?? 'Belum Ditentukan'); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight:600;color:#334155;">
                                            <i data-lucide="calendar" style="width:13px;vertical-align:middle;color:#64748b;"></i>
                                            <?php echo htmlspecialchars($c['jadwal_hari'] ?? 'Sabtu'); ?>
                                        </div>
                                        <div style="font-size:0.75rem;color:#64748b;">
                                            <i data-lucide="clock" style="width:13px;vertical-align:middle;color:#64748b;"></i>
                                            <?php echo htmlspecialchars($c['jadwal_waktu'] ?? '14.00 - 16.00 WIB'); ?>
                                        </div>
                                    </td>
                                    <td style="text-align:center;">
                                        <span style="background:#e0f2fe;color:#0369a1;font-weight:900;padding:4px 10px;border-radius:8px;font-size:0.8rem;">
                                            <?php echo $c['total_anggota']; ?> Siswa
                                        </span>
                                    </td>
                                    <td style="text-align:center;">
                                        <span style="background:#fef3c7;color:#92400e;font-weight:900;padding:4px 10px;border-radius:8px;font-size:0.8rem;">
                                            <?php echo $c['total_pertemuan']; ?> Pertemuan
                                        </span>
                                    </td>
                                    <td style="text-align:center;">
                                        <div style="display:flex;justify-content:center;gap:6px;">
                                            <button type="button" onclick='editCabang(<?php echo json_encode($c); ?>)' style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;padding:6px 10px;border-radius:8px;cursor:pointer;" title="Edit Cabang">
                                                <i data-lucide="pencil" style="width:14px;height:14px;vertical-align:middle;"></i>
                                            </button>
                                            <form method="POST" action="<?php echo base_url('admin/absen_v2/ekstra/delete-cabang'); ?>" onsubmit="return confirm('Apakah Anda yakin ingin menghapus cabang ekstra ini berserta seluruh riwayatnya?');" style="display:inline;">
                                                <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                                                <button type="submit" style="background:#fef2f2;color:#ef4444;border:1px solid #fecaca;padding:6px 10px;border-radius:8px;cursor:pointer;" title="Hapus Cabang">
                                                    <i data-lucide="trash-2" style="width:14px;height:14px;vertical-align:middle;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- MODAL TAMBAH ANGGOTA SISWA (MULTI-SELECT PER KELAS) -->
<div id="modalTambahAnggota" class="ekstra-modal">
    <div class="ekstra-modal-content">
        <div style="padding:1.25rem 1.5rem;background:#f8fafc;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:10px;">
                <i data-lucide="user-plus" style="color:#0ea5e9;width:20px;"></i>
                <h3 style="margin:0;font-size:1.05rem;font-weight:800;color:#0f172a;">
                    Tambah Anggota Siswa — <?php echo htmlspecialchars($selected_ekstra); ?>
                </h3>
            </div>
            <button type="button" onclick="closeModal('modalTambahAnggota')" style="background:none;border:none;color:#64748b;cursor:pointer;font-size:1.2rem;">
                <i data-lucide="x"></i>
            </button>
        </div>

        <form method="POST" action="<?php echo base_url('admin/absen_v2/ekstra/add-anggota'); ?>" style="padding:1.5rem;">
            <input type="hidden" name="nama_ekstra" value="<?php echo htmlspecialchars($selected_ekstra); ?>">

            <div style="margin-bottom:1.25rem;">
                <label style="display:block;font-size:0.75rem;font-weight:800;color:#475569;margin-bottom:6px;text-transform:uppercase;">
                    Pilih Kelas
                </label>
                <select id="selectKelasAnggota" onchange="loadSiswaKelas(this.value)" style="width:100%;border:1.5px solid #cbd5e1;border-radius:10px;padding:9px 12px;font-weight:700;font-size:0.9rem;background:#fff;outline:none;">
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach($kelasList as $kl): ?>
                        <option value="<?php echo $kl['id']; ?>"><?php echo htmlspecialchars($kl['nama_kelas']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Loader / Container Daftar Siswa -->
            <div id="boxDaftarSiswa" style="display:none;margin-bottom:1.5rem;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                    <label style="font-size:0.75rem;font-weight:800;color:#475569;text-transform:uppercase;">
                        Checklist Siswa yang Bergabung:
                    </label>
                    <button type="button" onclick="toggleSelectAllSiswa()" style="background:#f1f5f9;border:1px solid #cbd5e1;padding:3px 10px;border-radius:6px;font-size:0.75rem;font-weight:700;color:#334155;cursor:pointer;">
                        Pilih Semua
                    </button>
                </div>

                <div id="listSiswaContainer" style="max-height:260px;overflow-y:auto;border:1.5px solid #e2e8f0;border-radius:10px;padding:10px;background:#f8fafc;">
                    <!-- Diisi via AJAX -->
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;border-top:1px solid #f1f5f9;padding-top:1.25rem;">
                <button type="button" onclick="closeModal('modalTambahAnggota')" style="background:#f1f5f9;color:#475569;border:1px solid #cbd5e1;padding:9px 18px;border-radius:10px;font-weight:700;font-size:0.85rem;cursor:pointer;">
                    Batal
                </button>
                <button type="submit" style="background:#0284c7;color:white;border:none;padding:9px 22px;border-radius:10px;font-weight:800;font-size:0.85rem;cursor:pointer;box-shadow:0 4px 12px rgba(2,132,199,0.3);">
                    Tambahkan ke Ekstrakurikuler
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TAMBAH / EDIT CABANG EKSTRA -->
<div id="modalCabang" class="ekstra-modal">
    <div class="ekstra-modal-content">
        <div style="padding:1.25rem 1.5rem;background:#f8fafc;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:10px;">
                <i data-lucide="award" style="color:#0ea5e9;width:20px;"></i>
                <h3 id="modalCabangTitle" style="margin:0;font-size:1.05rem;font-weight:800;color:#0f172a;">
                    Tambah Cabang Ekstrakurikuler
                </h3>
            </div>
            <button type="button" onclick="closeModal('modalCabang')" style="background:none;border:none;color:#64748b;cursor:pointer;font-size:1.2rem;">
                <i data-lucide="x"></i>
            </button>
        </div>

        <form method="POST" action="<?php echo base_url('admin/absen_v2/ekstra/save-cabang'); ?>" style="padding:1.5rem;">
            <input type="hidden" name="id" id="cabang_id" value="">

            <div style="margin-bottom:1.25rem;">
                <label style="display:block;font-size:0.75rem;font-weight:800;color:#475569;margin-bottom:6px;text-transform:uppercase;">
                    Nama Cabang Ekstrakurikuler
                </label>
                <input type="text" name="nama_ekstra" id="cabang_nama" required placeholder="Contoh: Pramuka Penegak, Futsal, Seni Musik" style="width:100%;border:1.5px solid #cbd5e1;border-radius:10px;padding:9px 12px;font-weight:700;font-size:0.9rem;background:#fff;outline:none;">
            </div>

            <div style="margin-bottom:1.25rem;">
                <label style="display:block;font-size:0.75rem;font-weight:800;color:#475569;margin-bottom:6px;text-transform:uppercase;">
                    Guru Pembina
                </label>
                <select name="guru_id" id="cabang_guru_id" style="width:100%;border:1.5px solid #cbd5e1;border-radius:10px;padding:9px 12px;font-weight:700;font-size:0.85rem;background:#fff;outline:none;">
                    <option value="0">-- Pilih Guru Pembina --</option>
                    <?php foreach($guruList as $g): ?>
                        <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['nama']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:1.25rem;">
                <div>
                    <label style="display:block;font-size:0.75rem;font-weight:800;color:#475569;margin-bottom:6px;text-transform:uppercase;">
                        Jadwal Hari
                    </label>
                    <select name="jadwal_hari" id="cabang_hari" style="width:100%;border:1.5px solid #cbd5e1;border-radius:10px;padding:9px 12px;font-weight:700;font-size:0.85rem;background:#fff;outline:none;">
                        <option value="Sabtu">Sabtu</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Senin">Senin</option>
                        <option value="Minggu">Minggu</option>
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:0.75rem;font-weight:800;color:#475569;margin-bottom:6px;text-transform:uppercase;">
                        Waktu / Jam
                    </label>
                    <input type="text" name="jadwal_waktu" id="cabang_waktu" value="14.00 - 16.00 WIB" placeholder="14.00 - 16.00 WIB" style="width:100%;border:1.5px solid #cbd5e1;border-radius:10px;padding:9px 12px;font-weight:700;font-size:0.85rem;background:#fff;outline:none;">
                </div>
            </div>

            <div style="margin-bottom:1.5rem;">
                <label style="display:block;font-size:0.75rem;font-weight:800;color:#475569;margin-bottom:6px;text-transform:uppercase;">
                    Target Pencapaian / Deskripsi (Opsional)
                </label>
                <textarea name="target_pencapaian" id="cabang_target" rows="2" placeholder="Tujuan atau capaian program ekstra..." style="width:100%;border:1.5px solid #cbd5e1;border-radius:10px;padding:8px 12px;font-size:0.85rem;background:#fff;outline:none;resize:vertical;"></textarea>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;border-top:1px solid #f1f5f9;padding-top:1.25rem;">
                <button type="button" onclick="closeModal('modalCabang')" style="background:#f1f5f9;color:#475569;border:1px solid #cbd5e1;padding:9px 18px;border-radius:10px;font-weight:700;font-size:0.85rem;cursor:pointer;">
                    Batal
                </button>
                <button type="submit" style="background:#0284c7;color:white;border:none;padding:9px 22px;border-radius:10px;font-weight:800;font-size:0.85rem;cursor:pointer;box-shadow:0 4px 12px rgba(2,132,199,0.3);">
                    Simpan Cabang
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function switchTab(tabId, btn) {
    document.querySelectorAll('.tab-pane').forEach(function(tp) {
        tp.classList.remove('active');
    });
    document.querySelectorAll('.tab-btn').forEach(function(tb) {
        tb.classList.remove('active');
    });
    document.getElementById(tabId).classList.add('active');
    btn.classList.add('active');
}

function openModal(modalId) {
    document.getElementById(modalId).classList.add('show');
}
function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('show');
}

function openModalTambahAnggota() {
    document.getElementById('selectKelasAnggota').value = '';
    document.getElementById('boxDaftarSiswa').style.display = 'none';
    document.getElementById('listSiswaContainer').innerHTML = '';
    openModal('modalTambahAnggota');
}

function openModalTambahCabang() {
    document.getElementById('modalCabangTitle').innerText = 'Tambah Cabang Ekstrakurikuler';
    document.getElementById('cabang_id').value = '';
    document.getElementById('cabang_nama').value = '';
    document.getElementById('cabang_guru_id').value = '0';
    document.getElementById('cabang_hari').value = 'Sabtu';
    document.getElementById('cabang_waktu').value = '14.00 - 16.00 WIB';
    document.getElementById('cabang_target').value = '';
    openModal('modalCabang');
}

function editCabang(data) {
    document.getElementById('modalCabangTitle').innerText = 'Edit Cabang Ekstrakurikuler';
    document.getElementById('cabang_id').value = data.id;
    document.getElementById('cabang_nama').value = data.nama_ekstra;
    document.getElementById('cabang_guru_id').value = data.guru_id || '0';
    document.getElementById('cabang_hari').value = data.jadwal_hari || 'Sabtu';
    document.getElementById('cabang_waktu').value = data.jadwal_waktu || '14.00 - 16.00 WIB';
    document.getElementById('cabang_target').value = data.target_pencapaian || '';
    openModal('modalCabang');
}

function loadSiswaKelas(kelasId) {
    if (!kelasId) {
        document.getElementById('boxDaftarSiswa').style.display = 'none';
        return;
    }
    const ekstra = <?php echo json_encode($selected_ekstra); ?>;
    const container = document.getElementById('listSiswaContainer');
    container.innerHTML = '<div style="text-align:center;padding:15px;color:#64748b;font-weight:700;">Memuat data siswa...</div>';
    document.getElementById('boxDaftarSiswa').style.display = 'block';

    fetch('<?php echo base_url("api/absen_v2/ekstra/siswa-by-kelas"); ?>?kelas_id=' + kelasId + '&ekstra=' + encodeURIComponent(ekstra))
        .then(response => response.json())
        .then(res => {
            if (res.success && res.data.length > 0) {
                let html = '';
                res.data.forEach(s => {
                    html += `
                        <label style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:8px;background:#fff;margin-bottom:6px;cursor:pointer;border:1px solid #e2e8f0;">
                            <input type="checkbox" name="siswa_ids[]" value="${s.id}" class="chk-siswa" style="width:16px;height:16px;accent-color:#0284c7;">
                            <div style="flex:1;">
                                <div style="font-weight:800;font-size:0.85rem;color:#0f172a;">${s.nama}</div>
                                <div style="font-size:0.75rem;color:#64748b;">NIS: ${s.nis || '-'} • JK: ${s.jenis_kelamin || '-'}</div>
                            </div>
                        </label>
                    `;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = '<div style="text-align:center;padding:20px;color:#94a3b8;font-size:0.85rem;">Semua siswa di kelas ini sudah terdaftar di cabang ekstra ini.</div>';
            }
        })
        .catch(err => {
            container.innerHTML = '<div style="text-align:center;padding:15px;color:#ef4444;">Gagal memuat data siswa.</div>';
        });
}

function toggleSelectAllSiswa() {
    const checkboxes = document.querySelectorAll('.chk-siswa');
    if (checkboxes.length === 0) return;
    const allChecked = Array.from(checkboxes).every(c => c.checked);
    checkboxes.forEach(c => c.checked = !allChecked);
}
</script>
