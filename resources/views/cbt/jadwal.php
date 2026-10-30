<?php
$pageTitle = 'Jadwal Ujian CBT';
$active_menu = 'jadwal';

$selectedHari = $_GET['hari'] ?? 'Semua';
$displayDays = ($selectedHari === 'Semua') ? $hari_list : [$selectedHari];
$currentView = $_GET['view'] ?? 'grid';
?>
<style>
    .schedule-wrapper {
        width: 100%;
        overflow-x: auto;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f8fafc;
    }
    .schedule-wrapper::-webkit-scrollbar { height: 10px; }
    .schedule-wrapper::-webkit-scrollbar-track { background: #f8fafc; border-radius: 10px; }
    .schedule-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; border: 2px solid #f8fafc; }
    
    .master-schedule-table {
        width: max-content;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 100%;
    }
    .master-schedule-table th, .master-schedule-table td {
        border-right: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
        padding: 8px 12px;
        vertical-align: middle;
    }
    .master-schedule-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-align: center;
        position: sticky;
        top: 0;
        z-index: 10;
        box-shadow: 0 1px 0 #e2e8f0;
    }
    .hari-header {
        background: #0ea5e9 !important;
        color: #fff !important;
        font-size: 0.9rem !important;
        padding: 12px !important;
        border-right: 1px solid rgba(255,255,255,0.2) !important;
        letter-spacing: 1px;
    }
    .kelas-header {
        min-width: 140px;
        background: #f1f5f9 !important;
    }
    .kelas-name { margin-bottom: 4px; font-weight: 900; color: #1e293b; font-size: 0.82rem; }
    .btn-atur {
        display: inline-block;
        background: #e0e7ff;
        color: #4f46e5;
        font-size: 0.65rem;
        padding: 2px 8px;
        border-radius: 10px;
        text-decoration: none;
        transition: 0.2s;
        cursor: pointer;
        border: none;
        font-weight: 700;
    }
    .btn-atur:hover { background: #4f46e5; color: #fff; }

    /* Sticky columns */
    .sticky-col-1, .sticky-col-2 {
        position: sticky;
        left: 0;
        background: #f8fafc;
        z-index: 20;
    }
    .sticky-col-2 { left: 45px; border-right: 2px solid #cbd5e1 !important; }
    
    .master-schedule-table thead .sticky-col-1, 
    .master-schedule-table thead .sticky-col-2 {
        z-index: 30;
    }

    .cell-empty {
        background: #fff;
        text-align: center;
        color: #cbd5e1;
        cursor: pointer;
        transition: all 0.15s ease;
        height: 58px;
    }
    .cell-empty:hover {
        background: #f0f9ff;
        color: #0ea5e9;
    }
    .cell-filled {
        background: #f0fdf4;
        border-left: 3px solid #10b981;
        transition: 0.2s;
        cursor: pointer;
        position: relative;
        height: 58px;
    }
    .cell-filled:hover {
        background: #dcfce7;
        transform: scale(1.02);
        box-shadow: 0 4px 10px rgba(0,0,0,0.06);
        z-index: 5;
    }
    
    .mapel-name {
        display: block;
        font-weight: 800;
        color: #0f172a;
        font-size: 0.76rem;
        line-height: 1.2;
        margin-bottom: 2px;
    }
    .guru-name {
        display: flex;
        align-items: center;
        gap: 3px;
        font-size: 0.65rem;
        color: #64748b;
        font-weight: 600;
        line-height: 1.1;
    }

    /* Tab bar */
    .tab-hari-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.15s ease;
        color: #64748b;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
    }
    .tab-hari-pill.active {
        background: #0ea5e9;
        color: #ffffff;
        border-color: #0284c7;
        box-shadow: 0 2px 8px rgba(14, 165, 233, 0.3);
    }
</style>

<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/header.php'; ?>

        <div class="z-scroll" style="padding: 1.5rem 2rem 3rem;">

            <!-- Alerts -->
            <?php if(isset($_GET['msg'])): ?>
                <?php if($_GET['msg'] === 'saved'): ?>
                    <div style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                        <i data-lucide="check-circle" style="width: 20px; height: 20px; color: #22c55e;"></i> Jadwal ujian berhasil dipasang ke slot kelas!
                    </div>
                <?php elseif($_GET['msg'] === 'deleted'): ?>
                    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                        <i data-lucide="trash-2" style="width: 20px; height: 20px; color: #ef4444;"></i> Jadwal ujian berhasil dihapus dari slot.
                    </div>
                <?php elseif($_GET['msg'] === 'slot_saved'): ?>
                    <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                        <i data-lucide="clock" style="width: 20px; height: 20px; color: #3b82f6;"></i> Pengaturan custom slot jam ujian berhasil disimpan!
                    </div>
                <?php elseif($_GET['msg'] === 'slot_deleted'): ?>
                    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                        <i data-lucide="trash-2" style="width: 20px; height: 20px; color: #ef4444;"></i> Slot jam berhasil dihapus.
                    </div>
                <?php elseif($_GET['msg'] === 'slot_reset'): ?>
                    <div style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                        <i data-lucide="refresh-cw" style="width: 20px; height: 20px; color: #22c55e;"></i> Slot jam ujian berhasil dikembalikan ke format standar!
                    </div>
                <?php elseif($_GET['msg'] === 'bulk_saved'): ?>
                    <div style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                        <i data-lucide="check-circle" style="width: 20px; height: 20px; color: #22c55e;"></i> Jadwal ujian kelas berhasil diperbarui secara massal!
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Elite Page Header -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-badge">JADWAL & SESI CBT</div>
                        <h1><i data-lucide="calendar"></i> Master Jadwal Ujian CBT</h1>
                        <p>Matriks jadwal pelaksanaan ujian CBT terpadu per kelas, sesi jam, dan hari pelaksanaan. Bebas atur dan custom jam sesi ujian sesuai jadwal madrasah.</p>
                    </div>
                    <div class="header-actions-premium" style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <!-- Tombol Custom Jam -->
                        <button type="button" onclick="openModalAturJam()" class="e-btn" style="background:#ffffff; color:#0f172a; font-weight:700; border:1px solid #cbd5e1; box-shadow: 0 4px 10px rgba(0,0,0,0.04);">
                            <i data-lucide="clock" style="color: #0ea5e9;"></i> Atur Jam / Slot Waktu
                        </button>
                        <!-- Tombol Tambah Jadwal -->
                        <button type="button" onclick="openJadwalGridModal()" class="e-btn e-btn-glass">
                            <i data-lucide="plus-circle"></i> Pasang Jadwal Ujian
                        </button>
                        <!-- Rilis Token -->
                        <a href="<?php echo base_url('cbt/token'); ?>" class="e-btn e-btn-blue-light">
                            <i data-lucide="key"></i> Rilis Token
                        </a>
                    </div>
                </div>
            </div>

            <!-- Toolbar: Filter Hari & View Switcher -->
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom: 1.25rem;">
                <!-- Filter Tab Hari -->
                <div style="display:flex; gap:6px; flex-wrap:wrap; align-items:center;">
                    <span style="font-size:0.8rem; font-weight:800; color:#64748b; margin-right:4px;">HARI:</span>
                    <a href="?hari=Semua&view=<?php echo $currentView; ?>" class="tab-hari-pill <?php echo ($selectedHari === 'Semua') ? 'active' : ''; ?>">
                        <i data-lucide="layers" style="width:14px;height:14px;"></i> Semua Hari
                    </a>
                    <?php foreach($hari_list as $h): ?>
                    <a href="?hari=<?php echo $h; ?>&view=<?php echo $currentView; ?>" class="tab-hari-pill <?php echo ($selectedHari === $h) ? 'active' : ''; ?>">
                        <?php echo $h; ?>
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- View Switcher -->
                <div style="display:flex; gap:6px; align-items:center; background:#f1f5f9; padding:4px; border-radius:10px;">
                    <a href="?hari=<?php echo $selectedHari; ?>&view=grid" style="padding:6px 12px; border-radius:8px; font-size:0.78rem; font-weight:700; text-decoration:none; display:flex; align-items:center; gap:5px; <?php echo ($currentView === 'grid') ? 'background:#fff; color:#0f172a; box-shadow:0 2px 4px rgba(0,0,0,0.06);' : 'color:#64748b;'; ?>">
                        <i data-lucide="grid" style="width:14px;height:14px;"></i> Matriks Jadwal
                    </a>
                    <a href="?hari=<?php echo $selectedHari; ?>&view=list" style="padding:6px 12px; border-radius:8px; font-size:0.78rem; font-weight:700; text-decoration:none; display:flex; align-items:center; gap:5px; <?php echo ($currentView === 'list') ? 'background:#fff; color:#0f172a; box-shadow:0 2px 4px rgba(0,0,0,0.06);' : 'color:#64748b;'; ?>">
                        <i data-lucide="list" style="width:14px;height:14px;"></i> Daftar Sesi
                    </a>
                </div>
            </div>

            <?php if($currentView === 'grid'): ?>
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- TAMPILAN MATRIKS JADWAL (PERSIS MODEL JADWAL PELAJARAN)          -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <div class="schedule-wrapper">
                <table class="master-schedule-table">
                    <thead>
                        <tr>
                            <th rowspan="2" class="sticky-col-1" style="width:45px;">NO</th>
                            <th rowspan="2" class="sticky-col-2" style="width:130px;">WAKTU</th>
                            <?php foreach($displayDays as $h): ?>
                                <th colspan="<?php echo count($kelasList); ?>" class="hari-header"><?php echo strtoupper($h); ?></th>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <?php foreach($displayDays as $h): ?>
                                <?php foreach($kelasList as $k): ?>
                                    <th class="kelas-header">
                                        <div class="kelas-name"><?php echo htmlspecialchars($k['nama_kelas']); ?></div>
                                        <button type="button" onclick="openAturKelasModal('<?php echo htmlspecialchars($k['nama_kelas']); ?>', <?php echo $k['id']; ?>)" class="btn-atur" title="Edit Jadwal Kelas Ini">[ATUR]</button>
                                    </th>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($slotList)): ?>
                            <tr>
                                <td colspan="<?php echo 2 + (count($displayDays) * count($kelasList)); ?>" style="text-align:center; padding:3rem; color:#94a3b8;">
                                    Belum ada slot waktu ujian yang ditentukan. Klik <strong>"Atur Jam / Slot Waktu"</strong> di atas untuk mengatur jam ujian.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach($slotList as $slot): 
                                $jamStr = substr($slot['jam_mulai'], 0, 5) . ' - ' . substr($slot['jam_selesai'], 0, 5);
                                $isBreak = !empty($slot['is_istirahat']);
                            ?>
                            <tr style="<?php echo $isBreak ? 'background:#f8fafc;' : ''; ?>">
                                <!-- Col 1: NO -->
                                <td class="sticky-col-1 text-center" style="font-size:0.8rem; font-weight:800; color:#475569; text-align:center;">
                                    <?php echo $no++; ?>
                                </td>
                                <!-- Col 2: WAKTU -->
                                <td class="sticky-col-2 text-center" style="font-size:0.75rem; font-weight:800; font-family:'JetBrains Mono', monospace; color:#0ea5e9; text-align:center; white-space:nowrap;">
                                    <div><?php echo htmlspecialchars($jamStr); ?></div>
                                    <div style="font-size:0.65rem; color:#94a3b8; font-family:sans-serif;"><?php echo htmlspecialchars($slot['label_sesi']); ?></div>
                                </td>

                                <?php if($isBreak): ?>
                                    <!-- Jam Istirahat spans across all class columns -->
                                    <td colspan="<?php echo count($displayDays) * count($kelasList); ?>" style="text-align:center; color:#94a3b8; font-weight:800; font-size:0.78rem; letter-spacing:2px; background:#f8fafc;">
                                        ☕ <?php echo strtoupper($slot['label_sesi']); ?>
                                    </td>
                                <?php else: ?>
                                    <!-- Normal Exam Slots for each Day and each Class -->
                                    <?php foreach($displayDays as $h): ?>
                                        <?php foreach($kelasList as $k): ?>
                                            <?php 
                                            // Look up matching exam in this day, slot, and class
                                            $cell = $jadwal_master[$h][$jamStr][$k['id']] ?? ($jadwal_master[$h][$jamStr][$k['nama_kelas']] ?? null);
                                            ?>
                                            <?php if($cell): ?>
                                            <td class="cell-filled" onclick="openDetailModal(<?php echo htmlspecialchars(json_encode($cell), ENT_QUOTES, 'UTF-8'); ?>)" title="Klik untuk lihat detail / hapus">
                                                <span class="mapel-name"><?php echo htmlspecialchars($cell['mapel_nama']); ?></span>
                                                <span class="guru-name">
                                                    <i data-lucide="user" style="width:10px;height:10px;display:inline;"></i>
                                                    <?php echo htmlspecialchars(!empty($cell['pengawas']) ? $cell['pengawas'] : ($cell['ruang'] ?: 'Pengawas')); ?>
                                                </span>
                                                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:2px;">
                                                    <span style="font-size:0.62rem; color:#059669; font-weight:700;"><?php echo $cell['durasi_menit']; ?> mnt</span>
                                                    <span style="font-size:0.6rem; background:#dcfce7; color:#166534; padding:1px 4px; border-radius:4px; font-weight:800;">
                                                        <?php echo htmlspecialchars($cell['ujian_status'] ?? 'Aktif'); ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <?php else: ?>
                                            <td class="cell-empty" onclick="quickSchedule('<?php echo $h; ?>', '<?php echo $jamStr; ?>', '<?php echo htmlspecialchars($k['nama_kelas']); ?>', <?php echo $k['id']; ?>)" title="Pasang jadwal untuk Kelas <?php echo htmlspecialchars($k['nama_kelas']); ?> pada <?php echo $h; ?>, <?php echo $jamStr; ?>">
                                                -
                                            </td>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php else: ?>
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- TAMPILAN LIST / DAFTAR SESI UJIAN                               -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <div class="card" style="border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden; background: #ffffff;">
                <div style="padding: 1.25rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div style="font-weight: 800; font-size: 0.95rem; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                        <span>Daftar Paket & Sesi Ujian Aktif</span>
                        <span style="font-size: 0.75rem; background: #e0e7ff; color: #4338ca; padding: 2px 8px; border-radius: 100px; font-weight: 800;">
                            <?php echo count($ujianList); ?> Sesi
                        </span>
                    </div>
                </div>

                <div class="z-table-wrap">
                    <table class="z-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Kode & Nama Ujian</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelas Peserta</th>
                                <th>Waktu Pelaksanaan</th>
                                <th>Durasi</th>
                                <th>Status</th>
                                <th style="text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ujianList)): ?>
                            <tr>
                                <td colspan="8" style="padding: 3rem 1.5rem; text-align: center; color: #94a3b8;">
                                    <i data-lucide="calendar-x" style="width: 44px; height: 44px; margin-bottom: 10px; color: #cbd5e1;"></i>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: #475569;">Belum ada jadwal ujian yang dibuat</div>
                                    <div style="font-size: 0.78rem;">Klik tombol "Pasang Jadwal Ujian" di atas untuk menambahkan sesi ujian.</div>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach($ujianList as $u): 
                                    $st = $u['status'] ?? 'Draft';
                                    $stBg = ($st === 'Aktif') ? '#dcfce7' : (($st === 'Selesai') ? '#f1f5f9' : '#fef3c7');
                                    $stCol = ($st === 'Aktif') ? '#15803d' : (($st === 'Selesai') ? '#475569' : '#b45309');
                                ?>
                                <tr>
                                    <td style="color: #94a3b8; font-weight: 600;"><?php echo $no++; ?></td>
                                    <td>
                                        <div style="font-weight: 800; color: #0f172a; font-size: 0.88rem;"><?php echo htmlspecialchars($u['nama_ujian']); ?></div>
                                        <div style="font-size: 0.72rem; color: #64748b; font-family: monospace;">Kode: <?php echo htmlspecialchars($u['kode_ujian']); ?></div>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.76rem; font-weight: 700; color: #1e293b;">
                                            <?php echo htmlspecialchars($u['mapel_nama']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.72rem; font-weight: 700; padding: 3px 8px; border-radius: 6px; background: #e0e7ff; color: #4338ca;">
                                            <?php echo htmlspecialchars($u['kelas_nama'] ?: 'Semua Kelas'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.78rem; font-weight: 700; color: #1e293b;">
                                            Mulai: <?php echo !empty($u['tgl_mulai']) ? date('d M Y, H:i', strtotime($u['tgl_mulai'])) : '-'; ?>
                                        </div>
                                        <div style="font-size: 0.72rem; color: #64748b;">
                                            Selesai: <?php echo !empty($u['tgl_selesai']) ? date('d M Y, H:i', strtotime($u['tgl_selesai'])) : '-'; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="font-weight: 700; font-size: 0.82rem; color: #0f172a;"><?php echo $u['durasi_menit']; ?> mnt</span>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.72rem; font-weight: 800; padding: 3px 9px; border-radius: 6px; background: <?php echo $stBg; ?>; color: <?php echo $stCol; ?>;">
                                            <?php echo htmlspecialchars($st); ?>
                                        </span>
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <!-- Toggle Status Aktif / Selesai -->
                                        <form method="POST" action="<?php echo base_url('cbt/jadwal/toggle-status'); ?>" style="display: inline-block;">
                                            <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                            <input type="hidden" name="current_status" value="<?php echo $st; ?>">
                                            <?php if ($st === 'Aktif'): ?>
                                            <button type="submit" class="e-btn e-btn-dark" style="padding: 4px 8px; font-size: 0.72rem; background: #fee2e2; color: #b91c1c; border-color: #fecaca;" title="Nonaktifkan / Selesai">
                                                <i data-lucide="stop-circle" style="width: 13px; height: 13px;"></i> Tutup
                                            </button>
                                            <?php else: ?>
                                            <button type="submit" class="e-btn e-btn-primary" style="padding: 4px 8px; font-size: 0.72rem; background: #16a34a;" title="Aktifkan Ujian Sekarang">
                                                <i data-lucide="play-circle" style="width: 13px; height: 13px;"></i> Aktifkan
                                            </button>
                                            <?php endif; ?>
                                        </form>

                                        <!-- Hapus Jadwal -->
                                        <form method="POST" action="<?php echo base_url('cbt/jadwal/delete'); ?>" style="display: inline-block;" onsubmit="return confirm('Hapus jadwal ujian ini?')">
                                            <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                            <button type="submit" class="e-btn e-btn-dark" style="padding: 4px 8px; font-size: 0.72rem; background: #f1f5f9; color: #64748b;" title="Hapus">
                                                <i data-lucide="trash-2" style="width: 13px; height: 13px;"></i>
                                            </button>
                                        </form>
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

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- MODAL 1: ATUR JAM / SLOT WAKTU (CUSTOM JAM FITUR)               -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<div id="modalAturJam" class="modal-overlay" onclick="if(event.target === this) this.classList.remove('open')">
    <div style="background: #ffffff; width: 92%; max-width: 650px; border-radius: 20px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.18); border: 1px solid #e2e8f0; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="clock" style="width: 22px; height: 22px; color: #0ea5e9;"></i>
                Pengaturan Jam & Sesi Ujian CBT
            </h3>
            <button type="button" onclick="document.getElementById('modalAturJam').classList.remove('open')" style="background: none; border: none; cursor: pointer; color: #94a3b8;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>

        <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 1.25rem;">
            Atur dan sesuaikan jam pelaksanaan ujian sesuai kebutuhan jadwal madrasah. Anda dapat menambah slot sesi baru, mengedit jam mulai-selesai, atau menandai jam istirahat.
        </p>

        <!-- Tabel Daftar Slot Jam yang ada -->
        <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 1.5rem;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.82rem;">
                <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                    <tr>
                        <th style="padding: 10px 12px; text-align: center; width: 40px;">No</th>
                        <th style="padding: 10px 12px; text-align: left;">Label Sesi</th>
                        <th style="padding: 10px 12px; text-align: center;">Waktu (Mulai - Selesai)</th>
                        <th style="padding: 10px 12px; text-align: center;">Tipe</th>
                        <th style="padding: 10px 12px; text-align: center; width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($slotList)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px; color: #94a3b8;">Belum ada slot waktu.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($slotList as $sl): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9; <?php echo !empty($sl['is_istirahat']) ? 'background: #f8fafc;' : ''; ?>">
                            <td style="padding: 10px 12px; text-align: center; font-weight: 700; color: #64748b;"><?php echo $sl['urutan']; ?></td>
                            <td style="padding: 10px 12px; font-weight: 700; color: #1e293b;"><?php echo htmlspecialchars($sl['label_sesi']); ?></td>
                            <td style="padding: 10px 12px; text-align: center; font-family: monospace; font-weight: 700; color: #0ea5e9;">
                                <?php echo substr($sl['jam_mulai'], 0, 5); ?> - <?php echo substr($sl['jam_selesai'], 0, 5); ?>
                            </td>
                            <td style="padding: 10px 12px; text-align: center;">
                                <?php if (!empty($sl['is_istirahat'])): ?>
                                    <span style="background: #fef3c7; color: #b45309; padding: 2px 8px; border-radius: 6px; font-size: 0.72rem; font-weight: 700;">Istirahat</span>
                                <?php else: ?>
                                    <span style="background: #e0f2fe; color: #0284c7; padding: 2px 8px; border-radius: 6px; font-size: 0.72rem; font-weight: 700;">Ujian</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 10px 12px; text-align: center;">
                                <button type="button" onclick="editJamSlot(<?php echo htmlspecialchars(json_encode($sl)); ?>)" style="background: none; border: none; cursor: pointer; color: #3b82f6; padding: 2px 5px;" title="Edit">
                                    <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
                                </button>
                                <form method="POST" action="<?php echo base_url('cbt/jadwal/slot/delete'); ?>" style="display: inline-block;" onsubmit="return confirm('Hapus slot jam ini?')">
                                    <input type="hidden" name="id" value="<?php echo $sl['id']; ?>">
                                    <button type="submit" style="background: none; border: none; cursor: pointer; color: #ef4444; padding: 2px 5px;" title="Hapus">
                                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Form Tambah / Edit Slot Jam -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem;">
            <div style="font-weight: 800; font-size: 0.9rem; color: #0f172a; margin-bottom: 0.75rem;" id="formSlotTitle">
                ➕ Tambah Slot Waktu Ujian Baru
            </div>
            <form method="POST" action="<?php echo base_url('cbt/jadwal/slot/save'); ?>" id="formSlotJam">
                <input type="hidden" name="id" id="slot_id" value="0">
                <div style="display: grid; grid-template-columns: 80px 1fr 1fr 1fr; gap: 10px; margin-bottom: 1rem;">
                    <div>
                        <label class="label-sm">Urutan</label>
                        <input type="number" name="urutan" id="slot_urutan" class="input-sm" value="<?php echo count($slotList) + 1; ?>" min="1" required>
                    </div>
                    <div>
                        <label class="label-sm">Label Sesi</label>
                        <input type="text" name="label_sesi" id="slot_label" class="input-sm" placeholder="Contoh: Sesi 1" required>
                    </div>
                    <div>
                        <label class="label-sm">Jam Mulai</label>
                        <input type="time" name="jam_mulai" id="slot_jam_mulai" class="input-sm" required>
                    </div>
                    <div>
                        <label class="label-sm">Jam Selesai</label>
                        <input type="time" name="jam_selesai" id="slot_jam_selesai" class="input-sm" required>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <label style="display: flex; align-items: center; gap: 6px; font-size: 0.8rem; font-weight: 600; color: #475569; cursor: pointer;">
                        <input type="checkbox" name="is_istirahat" id="slot_is_istirahat" value="1">
                        Tandai sebagai slot istirahat / ishoma
                    </label>

                    <div style="display: flex; gap: 8px;">
                        <button type="button" onclick="resetFormSlot()" class="e-btn e-btn-dark" style="padding: 6px 12px; font-size: 0.78rem;">
                            Batal
                        </button>
                        <button type="submit" class="e-btn e-btn-primary" style="padding: 6px 16px; font-size: 0.78rem; background: #0ea5e9;">
                            <i data-lucide="check" style="width: 14px; height: 14px;"></i> Simpan Slot
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div style="margin-top: 1.25rem; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 1rem;">
            <form method="POST" action="<?php echo base_url('cbt/jadwal/slot/reset'); ?>" onsubmit="return confirm('Kembalikan seluruh slot waktu ke pengaturan standar?')">
                <button type="submit" style="background: none; border: none; color: #64748b; font-size: 0.75rem; font-weight: 700; cursor: pointer; text-decoration: underline;">
                    <i data-lucide="rotate-ccw" style="width: 12px; height: 12px; display: inline; vertical-align: -1px;"></i> Reset ke Slot Standar
                </button>
            </form>
            <button type="button" onclick="document.getElementById('modalAturJam').classList.remove('open')" class="e-btn e-btn-dark" style="padding: 7px 16px;">
                Selesai
            </button>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- MODAL 2: PASANG JADWAL UJIAN KE SLOT GRID                       -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<div id="modalJadwalGrid" class="modal-overlay" onclick="if(event.target === this) this.classList.remove('open')">
    <div style="background: #ffffff; width: 90%; max-width: 520px; border-radius: 20px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.18); border: 1px solid #e2e8f0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 id="modalGridTitle" style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="calendar-plus" style="width: 20px; height: 20px; color: #0ea5e9;"></i>
                Pasang Jadwal Ujian
            </h3>
            <button type="button" onclick="document.getElementById('modalJadwalGrid').classList.remove('open')" style="background: none; border: none; cursor: pointer; color: #94a3b8;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>

        <form method="POST" action="<?php echo base_url('cbt/jadwal/grid/save'); ?>">
            <div style="margin-bottom: 1rem;">
                <label class="label-sm">Pilih Paket Ujian <span style="color:#ef4444;">*</span></label>
                <select name="ujian_id" id="grid_ujian_id" class="input-sm" required>
                    <option value="">-- Pilih Paket Ujian --</option>
                    <?php foreach($ujianList as $uj): ?>
                        <option value="<?php echo $uj['id']; ?>">
                            <?php echo htmlspecialchars($uj['nama_ujian'] . ' (' . $uj['mapel_nama'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 1rem;">
                <div>
                    <label class="label-sm">Hari Pelaksanaan <span style="color:#ef4444;">*</span></label>
                    <select name="hari" id="grid_hari" class="input-sm" required>
                        <?php foreach($hari_list as $h): ?>
                            <option value="<?php echo $h; ?>"><?php echo $h; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="label-sm">Slot Jam Waktu <span style="color:#ef4444;">*</span></label>
                    <select name="jam_slot" id="grid_jam_slot" class="input-sm" required>
                        <?php foreach($slotList as $sl): 
                            if (!empty($sl['is_istirahat'])) continue;
                            $str = substr($sl['jam_mulai'], 0, 5) . ' - ' . substr($sl['jam_selesai'], 0, 5);
                        ?>
                            <option value="<?php echo $str; ?>">
                                <?php echo $str; ?> (<?php echo htmlspecialchars($sl['label_sesi']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 1rem;">
                <div>
                    <label class="label-sm">Kelas Peserta <span style="color:#ef4444;">*</span></label>
                    <select name="kelas_nama" id="grid_kelas_nama" class="input-sm" required onchange="syncKelasId(this)">
                        <?php foreach($kelasList as $k): ?>
                            <option value="<?php echo htmlspecialchars($k['nama_kelas']); ?>" data-id="<?php echo $k['id']; ?>">
                                <?php echo htmlspecialchars($k['nama_kelas']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="kelas_id" id="grid_kelas_id" value="<?php echo $kelasList[0]['id'] ?? 1; ?>">
                </div>
                <div>
                    <label class="label-sm">Ruang Ujian (Opsional)</label>
                    <input type="text" name="ruang" id="grid_ruang" class="input-sm" placeholder="Contoh: Lab Komputer 1">
                </div>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label class="label-sm">Guru Pengawas / Pengampu (Opsional)</label>
                <input type="text" name="pengawas" id="grid_pengawas" class="input-sm" list="listGuruPengawas" placeholder="Pilih atau ketik nama pengawas...">
                <datalist id="listGuruPengawas">
                    <?php foreach($guruList as $g): ?>
                        <option value="<?php echo htmlspecialchars($g['nama']); ?>"></option>
                    <?php endforeach; ?>
                </datalist>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" onclick="document.getElementById('modalJadwalGrid').classList.remove('open')" class="e-btn e-btn-dark">
                    Batal
                </button>
                <button type="submit" class="e-btn e-btn-primary" style="background: #0ea5e9;">
                    <i data-lucide="check" style="width: 14px; height: 14px;"></i> Pasang ke Matriks
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- MODAL 3: ATUR JADWAL KELAS (TOMBOL [ATUR] DI HEADER KELAS)       -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<div id="modalAturKelas" class="modal-overlay" onclick="if(event.target === this) this.classList.remove('open')">
    <div style="background: #ffffff; width: 92%; max-width: 780px; border-radius: 20px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.18); border: 1px solid #e2e8f0; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="sliders" style="width: 22px; height: 22px; color: #4f46e5;"></i>
                Atur Jadwal Ujian Kelas: <span id="aturKelasTitle" style="color:#0ea5e9;">-</span>
            </h3>
            <button type="button" onclick="document.getElementById('modalAturKelas').classList.remove('open')" style="background: none; border: none; cursor: pointer; color: #94a3b8;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>

        <form method="POST" action="<?php echo base_url('cbt/jadwal/kelas/save-bulk'); ?>">
            <input type="hidden" name="kelas_nama" id="atur_bulk_kelas_nama" value="">
            <input type="hidden" name="kelas_id" id="atur_bulk_kelas_id" value="">

            <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 1.25rem;">
                Tentukan paket ujian dan pengawas untuk masing-masing slot waktu kelas ini. Pilih <em>-- Kosongkan --</em> jika pada jam tersebut tidak ada ujian.
            </p>

            <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 1.25rem;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.82rem;">
                    <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                        <tr>
                            <th style="padding: 10px 12px; text-align: left; width: 100px;">Hari</th>
                            <th style="padding: 10px 12px; text-align: left; width: 120px;">Slot Waktu</th>
                            <th style="padding: 10px 12px; text-align: left;">Paket Ujian</th>
                            <th style="padding: 10px 12px; text-align: left; width: 160px;">Pengawas</th>
                        </tr>
                    </thead>
                    <tbody id="aturKelasTbody">
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" onclick="document.getElementById('modalAturKelas').classList.remove('open')" class="e-btn e-btn-dark">
                    Batal
                </button>
                <button type="submit" class="e-btn e-btn-primary" style="background: #4f46e5;">
                    <i data-lucide="save" style="width: 14px; height: 14px;"></i> Simpan Semua Jadwal Kelas
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- MODAL 4: DETAIL & HAPUS JADWAL SESI TERPILIH                   -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<div id="modalDetailSlot" class="modal-overlay" onclick="if(event.target === this) this.classList.remove('open')">
    <div style="background: #ffffff; width: 90%; max-width: 440px; border-radius: 20px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.18); border: 1px solid #e2e8f0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="info" style="width: 20px; height: 20px; color: #10b981;"></i>
                Detail Sesi Terjadwal
            </h3>
            <button type="button" onclick="document.getElementById('modalDetailSlot').classList.remove('open')" style="background: none; border: none; cursor: pointer; color: #94a3b8;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>

        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 14px; margin-bottom: 1.25rem;">
            <div id="detailMapel" style="font-size: 1.1rem; font-weight: 900; color: #065f46; margin-bottom: 4px;">-</div>
            <div id="detailNamaUjian" style="font-size: 0.85rem; font-weight: 700; color: #1e293b;">-</div>
            <div id="detailKode" style="font-size: 0.72rem; color: #64748b; font-family: monospace; margin-top: 2px;">-</div>
        </div>

        <div style="font-size: 0.85rem; color: #475569; display: flex; flex-direction: column; gap: 8px; margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px;">
                <span style="color:#94a3b8;">Kelas:</span>
                <strong id="detailKelas" style="color:#0f172a;">-</strong>
            </div>
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px;">
                <span style="color:#94a3b8;">Waktu:</span>
                <strong id="detailWaktu" style="color:#0ea5e9;">-</strong>
            </div>
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px;">
                <span style="color:#94a3b8;">Pengawas:</span>
                <strong id="detailPengawas" style="color:#0f172a;">-</strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color:#94a3b8;">Ruang:</span>
                <strong id="detailRuang" style="color:#0f172a;">-</strong>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; gap: 8px;">
            <form method="POST" action="<?php echo base_url('cbt/jadwal/grid/delete'); ?>" onsubmit="return confirm('Hapus sesi ujian ini dari jadwal kelas?')">
                <input type="hidden" name="id" id="detailDeleteId" value="">
                <button type="submit" class="e-btn e-btn-dark" style="background:#fee2e2; color:#b91c1c; border-color:#fecaca;">
                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i> Hapus Jadwal
                </button>
            </form>
            <button type="button" onclick="document.getElementById('modalDetailSlot').classList.remove('open')" class="e-btn e-btn-primary" style="background: #10b981;">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    // Data global untuk JS
    const JADWAL_MASTER = <?php echo json_encode($jadwal_master); ?>;
    const UJIAN_LIST = <?php echo json_encode($ujianList); ?>;
    const SLOT_LIST = <?php echo json_encode($slotList); ?>;
    const HARI_LIST = <?php echo json_encode($hari_list); ?>;

    // ─── Modal 1: Custom Jam ───
    function openModalAturJam() {
        document.getElementById('modalAturJam').classList.add('open');
        resetFormSlot();
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function editJamSlot(data) {
        document.getElementById('slot_id').value = data.id;
        document.getElementById('slot_urutan').value = data.urutan;
        document.getElementById('slot_label').value = data.label_sesi;
        document.getElementById('slot_jam_mulai').value = data.jam_mulai.substring(0, 5);
        document.getElementById('slot_jam_selesai').value = data.jam_selesai.substring(0, 5);
        document.getElementById('slot_is_istirahat').checked = (parseInt(data.is_istirahat) === 1);
        document.getElementById('formSlotTitle').innerHTML = '✏️ Edit Slot: ' + data.label_sesi;
    }

    function resetFormSlot() {
        document.getElementById('slot_id').value = '0';
        document.getElementById('slot_urutan').value = SLOT_LIST.length + 1;
        document.getElementById('slot_label').value = '';
        document.getElementById('slot_jam_mulai').value = '';
        document.getElementById('slot_jam_selesai').value = '';
        document.getElementById('slot_is_istirahat').checked = false;
        document.getElementById('formSlotTitle').innerHTML = '➕ Tambah Slot Waktu Ujian Baru';
    }

    // ─── Modal 2: Pasang Jadwal Quick ───
    function openJadwalGridModal() {
        document.getElementById('modalJadwalGrid').classList.add('open');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function quickSchedule(hari, jamSlot, kelasNama, kelasId) {
        document.getElementById('grid_hari').value = hari;
        document.getElementById('grid_jam_slot').value = jamSlot;
        document.getElementById('grid_kelas_nama').value = kelasNama;
        document.getElementById('grid_kelas_id').value = kelasId;
        document.getElementById('modalGridTitle').innerHTML = '<i data-lucide="calendar-plus" style="width:20px;height:20px;color:#0ea5e9;"></i> Pasang Jadwal: ' + kelasNama + ' (' + hari + ')';
        document.getElementById('modalJadwalGrid').classList.add('open');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function syncKelasId(selectEl) {
        const opt = selectEl.options[selectEl.selectedIndex];
        const kId = opt.getAttribute('data-id') || 1;
        document.getElementById('grid_kelas_id').value = kId;
    }

    // ─── Modal 3: Atur Jadwal Kelas [ATUR] ───
    function openAturKelasModal(kelasNama, kelasId) {
        document.getElementById('aturKelasTitle').innerText = kelasNama;
        document.getElementById('atur_bulk_kelas_nama').value = kelasNama;
        document.getElementById('atur_bulk_kelas_id').value = kelasId;

        const tbody = document.getElementById('aturKelasTbody');
        tbody.innerHTML = '';

        let rowIdx = 0;
        HARI_LIST.forEach(hari => {
            SLOT_LIST.forEach(slot => {
                if (parseInt(slot.is_istirahat) === 1) return; // Lewati istirahat

                const jamStr = slot.jam_mulai.substring(0, 5) + ' - ' + slot.jam_selesai.substring(0, 5);
                const currentExam = (JADWAL_MASTER[hari] && JADWAL_MASTER[hari][jamStr] && JADWAL_MASTER[hari][jamStr][kelasNama]) 
                                    ? JADWAL_MASTER[hari][jamStr][kelasNama] : null;

                const selectedUjianId = currentExam ? currentExam.ujian_id : 0;
                const currentPengawas = currentExam ? (currentExam.pengawas || '') : '';

                let optionsUjian = '<option value="0">-- Kosongkan Slot Ini --</option>';
                UJIAN_LIST.forEach(uj => {
                    const sel = (parseInt(uj.id) === parseInt(selectedUjianId)) ? 'selected' : '';
                    optionsUjian += `<option value="${uj.id}" ${sel}>${uj.nama_ujian} (${uj.mapel_nama})</option>`;
                });

                const tr = document.createElement('tr');
                tr.style.borderBottom = '1px solid #f1f5f9';
                tr.innerHTML = `
                    <td style="padding:8px 12px; font-weight:700; color:#1e293b;">
                        ${hari}
                        <input type="hidden" name="schedules[${rowIdx}][hari]" value="${hari}">
                    </td>
                    <td style="padding:8px 12px; font-family:monospace; font-weight:700; color:#0ea5e9;">
                        ${jamStr}
                        <input type="hidden" name="schedules[${rowIdx}][jam_slot]" value="${jamStr}">
                    </td>
                    <td style="padding:8px 12px;">
                        <select name="schedules[${rowIdx}][ujian_id]" class="input-sm" style="width:100%; font-size:0.8rem;">
                            ${optionsUjian}
                        </select>
                    </td>
                    <td style="padding:8px 12px;">
                        <input type="text" name="schedules[${rowIdx}][pengawas]" value="${currentPengawas}" class="input-sm" style="width:100%; font-size:0.8rem;" placeholder="Pengawas">
                    </td>
                `;
                tbody.appendChild(tr);
                rowIdx++;
            });
        });

        document.getElementById('modalAturKelas').classList.add('open');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    // ─── Modal 4: Detail Slot ───
    function openDetailModal(data) {
        document.getElementById('detailMapel').innerText = data.mapel_nama || '-';
        document.getElementById('detailNamaUjian').innerText = data.nama_ujian || '-';
        document.getElementById('detailKode').innerText = 'Kode: ' + (data.kode_ujian || '-');
        document.getElementById('detailKelas').innerText = data.kelas_nama || '-';
        document.getElementById('detailWaktu').innerText = (data.hari || '') + ', ' + (data.jam_slot || '');
        document.getElementById('detailPengawas').innerText = data.pengawas || '-';
        document.getElementById('detailRuang').innerText = data.ruang || '-';
        document.getElementById('detailDeleteId').value = data.id || '';

        document.getElementById('modalDetailSlot').classList.add('open');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    // Init Lucide
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
