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
<!-- SweetAlert2 Modern -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    /* Standard Elite Header Buttons */
    .e-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 18px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        border: none;
        line-height: 1.2;
    }
    .e-btn-primary {
        background: #ffffff;
        color: #0284c7;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.9);
    }
    .e-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        background: #f8fafc;
        color: #0369a1;
    }
    .e-btn-glass {
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .e-btn-glass:hover {
        background: rgba(255, 255, 255, 0.3);
        color: #ffffff;
        transform: translateY(-2px);
    }

    .filter-pill-tab {
        white-space: nowrap;
        padding: 7px 16px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.2s;
        cursor: pointer;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
    }
    .filter-pill-tab.active {
        background: #0284c7 !important;
        color: #ffffff !important;
        border-color: #0284c7 !important;
        box-shadow: 0 3px 10px rgba(2, 132, 199, 0.3);
    }

    .swal2-popup.cbt-swal-popup {
        border-radius: 22px !important;
        font-family: 'Plus Jakarta Sans', system-ui, sans-serif !important;
        padding: 1.75rem !important;
    }
    .swal2-title {
        font-weight: 800 !important;
        color: #0f172a !important;
    }
</style>

<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/header.php'; ?>

        <div class="z-scroll" style="padding: 0 0 3rem 0;">

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
                    <div class="header-actions-premium" style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button type="button" onclick="bukaModalTugaskan()" class="e-btn e-btn-primary">
                            <i class="fa-solid fa-user-plus"></i> Tugaskan Pengawas
                        </button>
                        <a href="<?= function_exists('base_url') ? base_url('cbt/pengawas/scan') : '/cbt/pengawas/scan' ?>" class="e-btn e-btn-glass">
                            <i class="fa-solid fa-id-badge"></i> Scan Presensi Pengawas
                        </a>
                        <a href="<?= function_exists('base_url') ? base_url('cbt/pengawas/rekap') : '/cbt/pengawas/rekap' ?>" class="e-btn e-btn-glass">
                            <i class="fa-solid fa-clipboard-check"></i> Rekap Presensi Pengawas
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

            <!-- Date & Day Filter Tabs (Instant Client-Side Filtering - ZERO Reload) -->
            <div style="display: flex; align-items: center; gap: 8px; overflow-x: auto; padding-bottom: 8px; margin-bottom: 1.25rem; scrollbar-width: none;">
                <button type="button" class="filter-pill-tab <?= ($selectedFilter === 'Semua') ? 'active' : '' ?>" onclick="filterDayClient(this, 'Semua')">
                   <i class="fa-solid fa-calendar"></i> Semua Hari & Tanggal
                </button>

                <?php foreach ($scheduleDays as $k => $dayItem): ?>
                    <?php 
                    $isActive = ($selectedFilter === (string)$k || $selectedFilter === $dayItem['hari'] || $selectedFilter === $dayItem['tanggal']);
                    ?>
                    <button type="button" class="filter-pill-tab <?= $isActive ? 'active' : '' ?>" onclick="filterDayClient(this, '<?= htmlspecialchars($k) ?>')">
                       <?= htmlspecialchars($dayItem['label']) ?>
                    </button>
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
                            $dKeyHash = md5($dKey);
                            ?>
                            <!-- Baris Header Hari & Tanggal -->
                            <tr class="day-group-header day-key-<?= $dKeyHash ?>" data-daykey="<?= htmlspecialchars($dKey) ?>" data-hari="<?= htmlspecialchars($dHari) ?>" data-tgl="<?= htmlspecialchars($dTgl ?: '') ?>">
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

                            <!-- Baris Per Sesi -->
                            <?php foreach ($slotList as $slot): ?>
                                <?php
                                $sLabel = $slot['label_sesi'] ?? ('Sesi ' . ($slot['urutan'] ?? 1));
                                $jRange = (!empty($slot['jam_mulai']) && !empty($slot['jam_selesai'])) ? (substr($slot['jam_mulai'], 0, 5) . ' - ' . substr($slot['jam_selesai'], 0, 5) . ' WIB') : ($slot['jam_slot'] ?? '');
                                ?>
                                <tr class="day-group-row day-key-<?= $dKeyHash ?>" data-daykey="<?= htmlspecialchars($dKey) ?>" data-hari="<?= htmlspecialchars($dHari) ?>" data-tgl="<?= htmlspecialchars($dTgl ?: '') ?>">
                                    <td class="sticky-sesi-col" style="background: #f8fafc;">
                                        <div style="font-weight: 900; color: #0f172a; font-size: 0.88rem;"><?= htmlspecialchars($sLabel) ?></div>
                                        <div style="font-size: 0.7rem; color: #0284c7; font-weight: 700; margin-top: 3px;">
                                            <i class="fa-solid fa-clock" style="font-size: 0.65rem;"></i> <?= htmlspecialchars($jRange) ?>
                                        </div>
                                    </td>

                                    <?php foreach ($ruangList as $rg): ?>
                                        <?php
                                        $rId = (int)$rg['id'];
                                        $cellId = 'cell-' . md5($dKey . '::' . $sLabel . '::' . $rId);
                                        $item = $jadwalMatrix[$dKey][$sLabel][$rId] ?? ($jadwalMatrix[$dHari][$sLabel][$rId] ?? null);
                                        ?>
                                        <td id="<?= $cellId ?>" 
                                            data-dkey="<?= htmlspecialchars($dKey) ?>"
                                            data-hari="<?= htmlspecialchars($dHari) ?>"
                                            data-tgl="<?= htmlspecialchars($dTgl ?: '') ?>"
                                            data-sesi="<?= htmlspecialchars($sLabel) ?>"
                                            data-jam="<?= htmlspecialchars($jRange) ?>"
                                            data-ruangid="<?= $rId ?>"
                                            data-ruangnama="<?= htmlspecialchars($rg['nama_ruang']) ?>">
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
                                                            <button type="button" onclick="editPenugasanAjax('<?= $cellId ?>', <?= htmlspecialchars(json_encode($item)) ?>)" style="background: #f1f5f9; border: none; color: #475569; width: 26px; height: 26px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Ubah Pengawas">
                                                                <i class="fa-solid fa-pen" style="font-size: 0.72rem;"></i>
                                                            </button>
                                                            <button type="button" onclick="hapusPenugasanAjax('<?= $cellId ?>', <?= (int)$item['id'] ?>, '<?= addslashes($item['guru_nama']) ?>', '<?= htmlspecialchars($sLabel) ?>', '<?= htmlspecialchars($rg['nama_ruang']) ?>')" style="background: #fee2e2; border: none; color: #ef4444; width: 26px; height: 26px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Hapus">
                                                                <i class="fa-solid fa-trash-can" style="font-size: 0.72rem;"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="cell-empty-assign" onclick="pilihSlotKosongAjax('<?= $cellId ?>')">
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

<!-- Modal Form Penugasan Pengawas (AJAX Powered) -->
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

        <form id="formPenugasan" onsubmit="submitPenugasanAjax(event)" style="padding: 24px;">
            <input type="hidden" name="id" id="inputJadwalId" value="0">
            <input type="hidden" name="topik_id" value="<?= $selectedTopikId ?>">
            <input type="hidden" name="ruang_id" id="inputRuangId" value="0">
            <input type="hidden" name="jam_slot" id="inputJamSlot" value="">
            <input type="hidden" id="currentCellId" value="">

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
                <button type="submit" id="btnSubmitModal" style="padding: 10px 22px; border-radius: 10px; background: #0284c7; color: white; border: none; font-weight: 800; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-check"></i> <span>Simpan Penugasan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 1800,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

function autoDetectHari(dateStr) {
    if (!dateStr) return;
    const hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const d = new Date(dateStr);
    const dayName = hariIndo[d.getDay()];
    document.getElementById('inputHari').value = dayName;
}

// Instant Client-side Tab Filtering (No Page Reload)
function filterDayClient(btnEl, targetKey) {
    document.querySelectorAll('.filter-pill-tab').forEach(el => el.classList.remove('active'));
    btnEl.classList.add('active');

    const headers = document.querySelectorAll('.day-group-header');
    const rows = document.querySelectorAll('.day-group-row');

    if (targetKey === 'Semua') {
        headers.forEach(el => el.style.display = '');
        rows.forEach(el => el.style.display = '');
        return;
    }

    headers.forEach(el => {
        const dKey = el.getAttribute('data-daykey');
        const hari = el.getAttribute('data-hari');
        const tgl = el.getAttribute('data-tgl');
        if (dKey === targetKey || hari === targetKey || tgl === targetKey) {
            el.style.display = '';
        } else {
            el.style.display = 'none';
        }
    });

    rows.forEach(el => {
        const dKey = el.getAttribute('data-daykey');
        const hari = el.getAttribute('data-hari');
        const tgl = el.getAttribute('data-tgl');
        if (dKey === targetKey || hari === targetKey || tgl === targetKey) {
            el.style.display = '';
        } else {
            el.style.display = 'none';
        }
    });
}

function pilihSlotKosongAjax(cellId) {
    const cell = document.getElementById(cellId);
    if (!cell) return;

    const hari = cell.getAttribute('data-hari') || 'Senin';
    const tgl = cell.getAttribute('data-tgl') || '';
    const sesi = cell.getAttribute('data-sesi') || 'Sesi 1';
    const jam = cell.getAttribute('data-jam') || '';
    const ruangId = cell.getAttribute('data-ruangid') || 0;
    const ruangNama = cell.getAttribute('data-ruangnama') || 'Ruang';

    document.getElementById('modalTitle').innerText = 'Tugaskan Pengawas - ' + ruangNama;
    document.getElementById('currentCellId').value = cellId;
    document.getElementById('inputJadwalId').value = '0';
    document.getElementById('inputHari').value = hari;
    document.getElementById('inputTanggal').value = tgl;
    if (tgl) autoDetectHari(tgl);

    document.getElementById('inputSesiLabel').value = sesi;
    document.getElementById('inputJamSlot').value = jam;
    document.getElementById('inputRuangId').value = ruangId;
    document.getElementById('displayRuangNama').value = ruangNama;
    document.getElementById('inputGuruId').value = '';

    const modal = document.getElementById('modalPenugasan');
    modal.style.display = 'flex';
}

function editPenugasanAjax(cellId, item) {
    document.getElementById('modalTitle').innerText = 'Ubah Pengawas - ' + (item.ruang_nama || 'Ruang');
    document.getElementById('currentCellId').value = cellId;
    document.getElementById('inputJadwalId').value = item.id;
    document.getElementById('inputHari').value = item.hari;
    document.getElementById('inputTanggal').value = item.tanggal || '';
    if (item.tanggal) autoDetectHari(item.tanggal);

    document.getElementById('inputSesiLabel').value = item.sesi_label;
    document.getElementById('inputJamSlot').value = item.jam_slot || '';
    document.getElementById('inputRuangId').value = item.ruang_id;
    document.getElementById('displayRuangNama').value = item.ruang_nama || ('Ruang ' + item.ruang_id);
    document.getElementById('inputGuruId').value = item.guru_id || '';

    const modal = document.getElementById('modalPenugasan');
    modal.style.display = 'flex';
}

function bukaModalTugaskan() {
    const firstCell = document.querySelector('.cell-empty-assign');
    if (firstCell && firstCell.parentElement) {
        pilihSlotKosongAjax(firstCell.parentElement.id);
    } else {
        const allCells = document.querySelectorAll('td[id^="cell-"]');
        if (allCells.length > 0) {
            pilihSlotKosongAjax(allCells[0].id);
        }
    }
}

function tutupModalPenugasan() {
    document.getElementById('modalPenugasan').style.display = 'none';
}

// AJAX SUBMIT (ZERO RELOAD)
function submitPenugasanAjax(e) {
    e.preventDefault();
    const form = document.getElementById('formPenugasan');
    const submitBtn = document.getElementById('btnSubmitModal');
    const origHtml = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
    submitBtn.disabled = true;

    const cellId = document.getElementById('currentCellId').value;
    const saveUrl = '<?= function_exists('base_url') ? base_url('cbt/pengawas/jadwal/save') : '/cbt/pengawas/jadwal/save' ?>';
    const formData = new FormData(form);

    fetch(saveUrl, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        submitBtn.innerHTML = origHtml;
        submitBtn.disabled = false;

        if (data.success) {
            tutupModalPenugasan();
            Toast.fire({
                icon: 'success',
                title: 'Penugasan Pengawas Disimpan!'
            });

            // Update DOM cell in-place
            if (cellId) {
                const targetCell = document.getElementById(cellId);
                if (targetCell) {
                    const itemData = {
                        id: data.id,
                        topik_id: data.topik_id,
                        hari: data.hari,
                        tanggal: data.tanggal,
                        sesi_label: data.sesi_label,
                        jam_slot: data.jam_slot,
                        ruang_id: data.ruang_id,
                        ruang_nama: data.ruang_nama,
                        guru_id: data.guru_id,
                        guru_nama: data.guru_nama,
                        guru_nip: data.guru_nip
                    };

                    targetCell.innerHTML = `
                        <div class="pengawas-card" style="animation: popIn 0.25s ease;">
                            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 6px;">
                                <div style="min-width: 0; flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 3px;">
                                        <span style="background: #e0f2fe; color: #0284c7; font-size: 0.62rem; font-weight: 800; padding: 1px 6px; border-radius: 8px;">PENGAWAS</span>
                                    </div>
                                    <div style="font-weight: 800; font-size: 0.82rem; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${data.guru_nama}">
                                        ${data.guru_nama}
                                    </div>
                                    <div style="font-size: 0.68rem; color: #64748b; margin-top: 2px;">
                                        NIP: ${data.guru_nip || '-'}
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 4px;">
                                    <button type="button" onclick='editPenugasanAjax("${cellId}", ${JSON.stringify(itemData)})' style="background: #f1f5f9; border: none; color: #475569; width: 26px; height: 26px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Ubah Pengawas">
                                        <i class="fa-solid fa-pen" style="font-size: 0.72rem;"></i>
                                    </button>
                                    <button type="button" onclick="hapusPenugasanAjax('${cellId}', ${data.id}, '${data.guru_nama.replace(/'/g, "\\'")}', '${data.sesi_label}', '${data.ruang_nama}')" style="background: #fee2e2; border: none; color: #ef4444; width: 26px; height: 26px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Hapus">
                                        <i class="fa-solid fa-trash-can" style="font-size: 0.72rem;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                }
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menyimpan',
                text: data.message || 'Terjadi kesalahan sistem.'
            });
        }
    })
    .catch(err => {
        submitBtn.innerHTML = origHtml;
        submitBtn.disabled = false;
        Swal.fire({
            icon: 'error',
            title: 'Koneksi Terganggu',
            text: 'Gagal menghubungi server CBT.'
        });
    });
}

// AJAX DELETE (ZERO RELOAD)
function hapusPenugasanAjax(cellId, id, guruNama, sesi, ruangNama) {
    Swal.fire({
        title: 'Hapus Penugasan?',
        html: `Apakah Anda yakin ingin menghapus penugasan <strong>${guruNama}</strong> di <strong>${ruangNama}</strong> (${sesi})?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fa-solid fa-trash-can"></i> Ya, Hapus',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'cbt-swal-popup'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const delUrl = '<?= function_exists('base_url') ? base_url('cbt/pengawas/jadwal/delete') : '/cbt/pengawas/jadwal/delete' ?>';
            const formData = new FormData();
            formData.append('id', id);
            formData.append('topik_id', '<?= $selectedTopikId ?>');

            fetch(delUrl, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Toast.fire({
                        icon: 'success',
                        title: 'Penugasan Berhasil Dihapus'
                    });

                    const targetCell = document.getElementById(cellId);
                    if (targetCell) {
                        targetCell.innerHTML = `
                            <div class="cell-empty-assign" onclick="pilihSlotKosongAjax('${cellId}')" style="animation: popIn 0.2s ease;">
                                <i class="fa-solid fa-user-plus" style="font-size: 0.9rem; margin: 0 auto 3px; display: block; color: #94a3b8;"></i>
                                <span>+ Tugaskan</span>
                            </div>
                        `;
                    }
                }
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Tidak dapat menghapus data.'
                });
            });
        }
    });
}
</script>
