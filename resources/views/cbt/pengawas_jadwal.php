<?php
$pageTitle = 'Jadwal Pengawas Ruang CBT';
$active_menu = 'pengawas_jadwal';

$topikList = $topikList ?? [];
$selectedTopikId = (int)($selectedTopikId ?? 0);
$topikAktif = $topikAktif ?? null;
$ruangList = $ruangList ?? [];
$slotList = $slotList ?? [];
$guruList = $guruList ?? [];
$scheduleDays = $scheduleDays ?? [];
$displayScheduleDays = $displayScheduleDays ?? array_values($scheduleDays);
$jadwalMatrix = $jadwalMatrix ?? [];
$selectedFilter = $_GET['filter_tgl'] ?? ($_GET['hari'] ?? 'Semua');
$mapelByDayAndSlot = $mapelByDayAndSlot ?? [];

$topikNama = !empty($topikAktif['nama_topik']) ? $topikAktif['nama_topik'] : 'SIMULASI NEW CBT';
$topikTA = !empty($topikAktif['tahun_ajaran']) ? $topikAktif['tahun_ajaran'] : '2026/2027';
?>
<style>
    .schedule-wrapper {
        width: 100%;
        overflow-x: auto;
        background: #fff;
        border-radius: 18px;
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
        padding: 10px 14px;
        vertical-align: middle;
    }
    .master-schedule-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.78rem;
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
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
        color: #fff !important;
        font-size: 0.95rem !important;
        padding: 12px 18px !important;
        letter-spacing: 0.5px;
        font-weight: 800;
        text-align: left !important;
    }
    .ruang-col-header {
        min-width: 220px;
        background: #f1f5f9 !important;
        color: #0f172a !important;
    }
    .ruang-title { font-weight: 900; font-size: 0.88rem; color: #0f172a; }
    .ruang-sub { font-size: 0.68rem; color: #64748b; font-weight: 700; margin-top: 2px; }

    /* Sticky columns */
    .sticky-sesi-col {
        position: sticky;
        left: 0;
        background: #f8fafc;
        z-index: 20;
        min-width: 160px;
        border-right: 2px solid #cbd5e1 !important;
    }
    .master-schedule-table thead .sticky-sesi-col {
        z-index: 30;
    }

    .pengawas-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 10px 12px;
        border: 1px solid #e2e8f0;
        border-left: 4px solid #0284c7;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        transition: all 0.2s;
        position: relative;
    }
    .pengawas-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(2, 132, 199, 0.12);
        border-color: #93c5fd;
    }
    .cell-empty-assign {
        background: #fafafa;
        border: 1.5px dashed #cbd5e1;
        border-radius: 10px;
        padding: 12px 10px;
        text-align: center;
        color: #94a3b8;
        font-size: 0.75rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
    }
    .cell-empty-assign:hover {
        background: #eff6ff;
        border-color: #3b82f6;
        color: #1d4ed8;
    }
</style>

<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/header.php'; ?>

        <div class="z-scroll" style="padding: 0 0 3rem 0;">

            <?php if (isset($_GET['saved'])): ?>
            <div style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 14px 20px; border-radius: 14px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px; font-weight: 700;">
                <i class="fa-solid fa-circle-check" style="font-size: 1.25rem; color: #22c55e;"></i>
                <div>Penugasan Pengawas Ruang Berhasil Disimpan & Diterapkan!</div>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['deleted'])): ?>
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 20px; border-radius: 14px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px; font-weight: 700;">
                <i class="fa-solid fa-trash-can" style="font-size: 1.25rem; color: #ef4444;"></i>
                <div>Penugasan Pengawas Ruang Berhasil Dihapus.</div>
            </div>
            <?php endif; ?>

            <!-- Page Header Banner -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-badge" style="background: rgba(2, 132, 199, 0.15); color: #0284c7; border: 1px solid rgba(2, 132, 199, 0.3);">
                            <i class="fa-solid fa-calendar-check"></i> JADWAL MATRIKS PENGAWAS
                        </div>
                        <h1><i class="fa-solid fa-chalkboard-user" style="color: #0284c7;"></i> Jadwal Pengawas Ruang CBT</h1>
                        <p>Atur penugasan guru pengawas per-ruang dan per-sesi secara terisolasi per topik dan tanggal resmi (mendukung ujian multi-minggu).</p>
                    </div>
                    <div class="header-actions-premium" style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <button type="button" onclick="bukaModalTugaskan()" class="btn-head-primary" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                            <i class="fa-solid fa-user-plus"></i> Tugaskan Pengawas
                        </button>
                        <a href="<?= function_exists('base_url') ? base_url('cbt/pengawas/scan') : '/cbt/pengawas/scan' ?>" class="btn-head-outline" style="border-color: #10b981; color: #10b981;">
                            <i class="fa-solid fa-camera"></i> Scan Presensi Pengawas
                        </a>
                        <a href="<?= function_exists('base_url') ? base_url('cbt/pengawas/rekap') : '/cbt/pengawas/rekap' ?>" class="btn-head-outline">
                            <i class="fa-solid fa-file-invoice-dollar"></i> Rekap & Honorarium
                        </a>
                        <a href="<?= function_exists('base_url') ? base_url('cbt/ruang/berita-acara?topik_id=' . $selectedTopikId) : '/cbt/ruang/berita-acara' ?>" class="btn-head-outline">
                            <i class="fa-solid fa-print"></i> Berita Acara Ruang
                        </a>
                    </div>
                </div>
            </div>

            <!-- Topik Selector Bar -->
            <div style="background: #ffffff; border-radius: 16px; padding: 1.25rem 1.5rem; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.72rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Topik Asesmen Aktif (Terisolasi):</div>
                        <div style="font-size: 1.05rem; font-weight: 900; color: #0f172a;"><?= htmlspecialchars($topikNama) ?> (<?= htmlspecialchars($topikTA) ?>)</div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 10px;">
                    <label for="topikSelector" style="font-size: 0.8rem; font-weight: 800; color: #475569;">Ganti Topik:</label>
                    <select id="topikSelector" onchange="window.location.href='<?= function_exists('base_url') ? base_url('cbt/pengawas/jadwal') : '/cbt/pengawas/jadwal' ?>?topik_id=' + this.value" style="padding: 8px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-weight: 700; font-size: 0.85rem; color: #0f172a; min-width: 250px;">
                        <?php foreach ($topikList as $tp): ?>
                            <option value="<?= $tp['id'] ?>" <?= ((int)$selectedTopikId === (int)$tp['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tp['nama_topik']) ?> (<?= htmlspecialchars($tp['tahun_ajaran']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Date & Day Filter Tabs -->
            <div style="display: flex; align-items: center; gap: 8px; overflow-x: auto; padding-bottom: 8px; margin-bottom: 1.25rem; scrollbar-width: none;">
                <a href="<?= function_exists('base_url') ? base_url('cbt/pengawas/jadwal?topik_id=' . $selectedTopikId . '&filter_tgl=Semua') : '/cbt/pengawas/jadwal?topik_id=' . $selectedTopikId . '&filter_tgl=Semua' ?>" 
                   style="white-space: nowrap; padding: 7px 18px; border-radius: 20px; font-size: 0.78rem; font-weight: 800; text-decoration: none; transition: 0.2s; border: 1px solid <?= ($selectedFilter === 'Semua') ? '#0284c7; background: #0284c7; color: white;' : '#cbd5e1; background: white; color: #475569;' ?>">
                   <i class="fa-solid fa-calendar"></i> Semua Hari & Tanggal
                </a>

                <?php foreach ($scheduleDays as $k => $dayItem): ?>
                    <?php 
                    $isActive = ($selectedFilter === (string)$k || $selectedFilter === $dayItem['hari'] || $selectedFilter === $dayItem['tanggal']);
                    ?>
                    <a href="<?= function_exists('base_url') ? base_url('cbt/pengawas/jadwal?topik_id=' . $selectedTopikId . '&filter_tgl=' . urlencode($k)) : '/cbt/pengawas/jadwal?topik_id=' . $selectedTopikId . '&filter_tgl=' . urlencode($k) ?>" 
                       style="white-space: nowrap; padding: 7px 16px; border-radius: 20px; font-size: 0.78rem; font-weight: 800; text-decoration: none; transition: 0.2s; border: 1px solid <?= $isActive ? '#0284c7; background: #0284c7; color: white;' : '#cbd5e1; background: white; color: #475569;' ?>">
                       <?= htmlspecialchars($dayItem['label']) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Master Matrix Schedule Table -->
            <div class="schedule-wrapper">
                <table class="master-schedule-table">
                    <thead>
                        <tr>
                            <th class="sticky-sesi-col" style="min-width: 160px;">Sesi / Waktu</th>
                            <?php foreach ($ruangList as $rg): ?>
                            <th class="ruang-col-header">
                                <div class="ruang-title"><?= htmlspecialchars($rg['nama_ruang']) ?></div>
                                <div class="ruang-sub">Kapasitas: <?= (int)$rg['kapasitas'] ?> Kursi</div>
                            </th>
                            <?php endforeach; ?>
                            <?php if (empty($ruangList)): ?>
                            <th class="ruang-col-header">Belum Ada Ruang</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($displayScheduleDays as $dayItem): ?>
                            <?php
                            $dKey = $dayItem['key'];
                            $dHari = $dayItem['hari'];
                            $dTgl = $dayItem['tanggal'];
                            $dLabel = $dayItem['label'];
                            $dMapel = $dayItem['mapel_list'];
                            ?>
                            <!-- Baris Header Hari & Tanggal -->
                            <tr>
                                <td colspan="<?= max(2, count($ruangList) + 1) ?>" class="hari-header">
                                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <i class="fa-solid fa-calendar-day" style="font-size: 1.1rem;"></i>
                                            <span style="font-size: 1rem; font-weight: 900;"><?= htmlspecialchars($dLabel) ?></span>
                                            <?php if (!empty($dTgl)): ?>
                                                <span style="background: rgba(255,255,255,0.25); padding: 2px 8px; border-radius: 8px; font-size: 0.72rem; font-family: monospace;">
                                                    <?= htmlspecialchars($dTgl) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <?php if (!empty($dMapel)): ?>
                                                <span style="font-size: 0.72rem; background: rgba(0,0,0,0.2); padding: 3px 10px; border-radius: 12px; font-weight: 700;">
                                                    <i class="fa-solid fa-book-open"></i> <?= htmlspecialchars(implode(', ', array_slice($dMapel, 0, 3))) ?><?= count($dMapel) > 3 ? ' +' . (count($dMapel) - 3) : '' ?>
                                                </span>
                                            <?php endif; ?>
                                            <span style="font-size: 0.72rem; background: rgba(255,255,255,0.25); padding: 3px 10px; border-radius: 12px; font-weight: 700;">
                                                <?= count($ruangList) ?> Ruang Ujian
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <!-- Baris Per Sesi (Deduplikasi, tampil 1 kali per label sesi) -->
                            <?php foreach ($slotList as $slot): ?>
                                <?php
                                $sLabel = $slot['label_sesi'] ?? ('Sesi ' . ($slot['urutan'] ?? 1));
                                $jRange = (!empty($slot['jam_mulai']) && !empty($slot['jam_selesai'])) ? (substr($slot['jam_mulai'], 0, 5) . ' - ' . substr($slot['jam_selesai'], 0, 5) . ' WIB') : ($slot['jam_slot'] ?? '');
                                ?>
                                <tr>
                                    <td class="sticky-sesi-col" style="background: #f8fafc;">
                                        <div style="font-weight: 900; color: #0f172a; font-size: 0.88rem;"><?= htmlspecialchars($sLabel) ?></div>
                                        <div style="font-size: 0.7rem; color: #0284c7; font-weight: 700; margin-top: 3px;">
                                            <i class="fa-solid fa-clock" style="font-size: 0.65rem;"></i> <?= htmlspecialchars($jRange) ?>
                                        </div>
                                    </td>

                                    <?php foreach ($ruangList as $rg): ?>
                                        <?php
                                        $rId = (int)$rg['id'];
                                        // Cari item dengan date key terlebih dahulu, lalu fallback ke day key
                                        $item = $jadwalMatrix[$dKey][$sLabel][$rId] ?? ($jadwalMatrix[$dHari][$sLabel][$rId] ?? null);
                                        ?>
                                        <td>
                                            <?php if ($item): ?>
                                                <div class="pengawas-card">
                                                    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 6px;">
                                                        <div style="min-width: 0; flex: 1;">
                                                            <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 3px;">
                                                                <span style="background: #e0f2fe; color: #0284c7; font-size: 0.62rem; font-weight: 800; padding: 1px 6px; border-radius: 8px;">PENGAWAS</span>
                                                            </div>
                                                            <div style="font-weight: 800; font-size: 0.82rem; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($item['guru_nama']) ?>">
                                                                <?= htmlspecialchars($item['guru_nama']) ?>
                                                            </div>
                                                            <div style="font-size: 0.68rem; color: #64748b; margin-top: 2px;">
                                                                NIP: <?= htmlspecialchars($item['guru_nip'] ?: '-') ?>
                                                            </div>
                                                        </div>
                                                        <div style="display: flex; align-items: center; gap: 4px;">
                                                            <button type="button" onclick="editPenugasan(<?= htmlspecialchars(json_encode($item)) ?>)" style="background: #f1f5f9; border: none; color: #475569; width: 26px; height: 26px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Ubah Pengawas">
                                                                <i class="fa-solid fa-pen" style="font-size: 0.72rem;"></i>
                                                            </button>
                                                            <button type="button" onclick="hapusPenugasan(<?= (int)$item['id'] ?>)" style="background: #fee2e2; border: none; color: #ef4444; width: 26px; height: 26px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Hapus">
                                                                <i class="fa-solid fa-trash-can" style="font-size: 0.72rem;"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="cell-empty-assign" onclick="pilihSlotKosong('<?= htmlspecialchars($dHari) ?>', '<?= htmlspecialchars($dTgl ?: '') ?>', '<?= htmlspecialchars($sLabel) ?>', '<?= htmlspecialchars($jRange) ?>', <?= $rId ?>, '<?= htmlspecialchars($rg['nama_ruang']) ?>')">
                                                    <i class="fa-solid fa-user-plus" style="font-size: 0.9rem; margin: 0 auto 3px; display: block; color: #94a3b8;"></i>
                                                    <span>+ Tugaskan</span>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>

                                    <?php if (empty($ruangList)): ?>
                                        <td style="text-align: center; color: #94a3b8; font-size: 0.8rem;">
                                            Belum ada data ruangan. Tambah ruang ujian terlebih dahulu.
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<!-- Modal Form Penugasan Pengawas -->
<div id="modalPenugasan" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); z-index: 9999; backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 1rem;">
    <div style="background: #ffffff; border-radius: 20px; width: 100%; max-width: 520px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow: hidden; animation: popIn 0.2s ease;">
        <div style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); padding: 18px 24px; color: white; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-user-check" style="font-size: 1.25rem;"></i>
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800;" id="modalTitle">Tugaskan Pengawas Ruang</h3>
            </div>
            <button type="button" onclick="tutupModalPenugasan()" style="background: rgba(255,255,255,0.2); border: none; color: white; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="formPenugasan" method="POST" action="<?= function_exists('base_url') ? base_url('cbt/pengawas/jadwal/save') : '/cbt/pengawas/jadwal/save' ?>" style="padding: 24px;">
            <input type="hidden" name="id" id="inputJadwalId" value="0">
            <input type="hidden" name="topik_id" value="<?= $selectedTopikId ?>">
            <input type="hidden" name="ruang_id" id="inputRuangId" value="0">
            <input type="hidden" name="jam_slot" id="inputJamSlot" value="">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                <div>
                    <label style="display: block; font-size: 0.78rem; font-weight: 800; color: #475569; margin-bottom: 5px;">Tanggal Ujian:</label>
                    <input type="date" name="tanggal" id="inputTanggal" onchange="autoDetectHari(this.value)" style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-weight: 700; font-size: 0.85rem; color: #0f172a;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.78rem; font-weight: 800; color: #475569; margin-bottom: 5px;">Hari Ujian:</label>
                    <input type="text" name="hari" id="inputHari" readonly style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1.5px solid #e2e8f0; background: #f8fafc; font-weight: 700; font-size: 0.85rem; color: #334155;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                <div>
                    <label style="display: block; font-size: 0.78rem; font-weight: 800; color: #475569; margin-bottom: 5px;">Sesi Ujian:</label>
                    <select name="sesi_label" id="inputSesiLabel" style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-weight: 700; font-size: 0.85rem; color: #0f172a;">
                        <?php foreach ($slotList as $sl): ?>
                            <option value="<?= htmlspecialchars($sl['label_sesi']) ?>"><?= htmlspecialchars($sl['label_sesi']) ?> (<?= substr($sl['jam_mulai'], 0, 5) ?> - <?= substr($sl['jam_selesai'], 0, 5) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.78rem; font-weight: 800; color: #475569; margin-bottom: 5px;">Ruang Ujian:</label>
                    <input type="text" id="displayRuangNama" readonly style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1.5px solid #e2e8f0; background: #f8fafc; font-weight: 700; font-size: 0.85rem; color: #334155;">
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.78rem; font-weight: 800; color: #475569; margin-bottom: 5px;">Pilih Guru Pengawas Ruang:</label>
                <select name="guru_id" id="inputGuruId" required style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 2px solid #0284c7; font-weight: 700; font-size: 0.9rem; color: #0f172a;">
                    <option value="">-- Pilih Nama Pengawas --</option>
                    <?php foreach ($guruList as $g): ?>
                        <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['nama']) ?> (NIP: <?= htmlspecialchars($g['nip'] ?: '-') ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="tutupModalPenugasan()" style="padding: 10px 18px; border-radius: 10px; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; font-weight: 800; font-size: 0.85rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 10px 22px; border-radius: 10px; background: #0284c7; color: white; border: none; font-weight: 800; font-size: 0.85rem; cursor: pointer;">
                    <i class="fa-solid fa-check"></i> Simpan Penugasan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function autoDetectHari(dateStr) {
    if (!dateStr) return;
    const hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const d = new Date(dateStr);
    const dayName = hariIndo[d.getDay()];
    document.getElementById('inputHari').value = dayName;
}

function pilihSlotKosong(hari, tanggal, sesi, jamRange, ruangId, ruangNama) {
    document.getElementById('modalTitle').innerText = 'Tugaskan Pengawas - ' + ruangNama;
    document.getElementById('inputJadwalId').value = '0';
    document.getElementById('inputHari').value = hari || 'Senin';
    document.getElementById('inputTanggal').value = tanggal || '';
    if (tanggal) {
        autoDetectHari(tanggal);
    }
    document.getElementById('inputSesiLabel').value = sesi;
    document.getElementById('inputJamSlot').value = jamRange;
    document.getElementById('inputRuangId').value = ruangId;
    document.getElementById('displayRuangNama').value = ruangNama;
    document.getElementById('inputGuruId').value = '';

    const modal = document.getElementById('modalPenugasan');
    modal.style.display = 'flex';
}

function editPenugasan(item) {
    document.getElementById('modalTitle').innerText = 'Ubah Pengawas - ' + (item.ruang_nama || 'Ruang');
    document.getElementById('inputJadwalId').value = item.id;
    document.getElementById('inputHari').value = item.hari;
    document.getElementById('inputTanggal').value = item.tanggal || '';
    document.getElementById('inputSesiLabel').value = item.sesi_label;
    document.getElementById('inputJamSlot').value = item.jam_slot || '';
    document.getElementById('inputRuangId').value = item.ruang_id;
    document.getElementById('displayRuangNama').value = item.ruang_nama || ('Ruang ' + item.ruang_id);
    document.getElementById('inputGuruId').value = item.guru_id || '';

    const modal = document.getElementById('modalPenugasan');
    modal.style.display = 'flex';
}

function bukaModalTugaskan() {
    pilihSlotKosong('Senin', '', 'Sesi 1', '08:00 - 09:00 WIB', <?= !empty($ruangList) ? (int)$ruangList[0]['id'] : 1 ?>, '<?= !empty($ruangList) ? addslashes($ruangList[0]['nama_ruang']) : 'Ruang 01' ?>');
}

function tutupModalPenugasan() {
    document.getElementById('modalPenugasan').style.display = 'none';
}

function hapusPenugasan(id) {
    if (confirm('Apakah Anda yakin ingin menghapus penugasan pengawas ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= function_exists('base_url') ? base_url('cbt/pengawas/jadwal/delete') : '/cbt/pengawas/jadwal/delete' ?>';

        const inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = 'id';
        inputId.value = id;
        form.appendChild(inputId);

        const inputTopik = document.createElement('input');
        inputTopik.type = 'hidden';
        inputTopik.name = 'topik_id';
        inputTopik.value = '<?= $selectedTopikId ?>';
        form.appendChild(inputTopik);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
