<?php
$pageTitle = 'Jadwal Pengawas Ruang CBT';
$active_menu = 'pengawas_jadwal';

$selectedHari = $_GET['hari'] ?? 'Semua';
$hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
if ($selectedHari !== 'Semua' && !in_array($selectedHari, $hariList)) {
    $selectedHari = 'Semua';
}
$displayDays = ($selectedHari === 'Semua') ? $hariList : [$selectedHari];
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
        min-width: 200px;
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
        min-width: 150px;
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
                <i data-lucide="check-circle" style="width: 22px; height: 22px; color: #22c55e;"></i>
                <div>Penugasan Pengawas Ruang Berhasil Disimpan & Diterapkan!</div>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['deleted'])): ?>
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 20px; border-radius: 14px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px; font-weight: 700;">
                <i data-lucide="trash-2" style="width: 22px; height: 22px; color: #ef4444;"></i>
                <div>Penugasan Pengawas Ruang Berhasil Dihapus.</div>
            </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-badge">PROKTOR & PANITIA CBT</div>
                        <h1><i data-lucide="user-check"></i> Jadwal Pengawas Ruang</h1>
                        <p>Atur jadwal penugasan guru pengawas per-ruang dan per-sesi secara terisolasi per topik. Dilengkapi scan presensi dan rekap honorarium.</p>
                    </div>
                    <div class="header-actions-premium" style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <button type="button" onclick="bukaModalPenugasan()" class="e-btn e-btn-primary" style="background: #2563eb;">
                            <i data-lucide="plus-circle"></i> Tugaskan Pengawas
                        </button>
                        <a href="<?php echo base_url('cbt/pengawas/scan'); ?>" class="e-btn e-btn-glass" style="background: #059669; border-color: #059669;">
                            <i data-lucide="scan-face"></i> Scan Presensi Pengawas
                        </a>
                        <a href="<?php echo base_url('cbt/pengawas/rekap?topik_id=' . $selectedTopikId); ?>" class="e-btn e-btn-glass">
                            <i data-lucide="coins"></i> Rekap & Honorarium
                        </a>
                        <a href="<?php echo base_url('cbt/ruang/berita-acara?topik_id=' . $selectedTopikId); ?>" class="e-btn e-btn-glass">
                            <i data-lucide="file-check-2"></i> Berita Acara Ruang
                        </a>
                    </div>
                </div>
            </div>

            <!-- Topik Selector Bar -->
            <div class="card" style="padding: 1.1rem 1.4rem; border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; background: #ffffff; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); color: #2563eb; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(37,99,235,0.1);">
                        <i data-lucide="layers" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.72rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">TOPIK ASESMEN AKTIF</div>
                        <div style="font-weight: 800; color: #0f172a; font-size: 0.95rem;">Jadwal Pengawas Terintegrasi Per-Topik</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <label style="font-size: 0.82rem; font-weight: 700; color: #475569;">Pilih Topik:</label>
                    <select onchange="window.location.href='<?php echo base_url('cbt/pengawas/jadwal?topik_id='); ?>' + this.value + '&hari=<?php echo urlencode($selectedHari); ?>'" class="input-sm" style="min-width: 280px; font-weight: 700; border-radius: 10px; border-color: #cbd5e1; background: #f8fafc; cursor: pointer; padding: 7px 12px;">
                        <?php foreach ($topikList as $tp): ?>
                        <option value="<?php echo $tp['id']; ?>" <?php echo ($selectedTopikId == $tp['id']) ? 'selected' : ''; ?>>
                            [<?php echo htmlspecialchars($tp['jenis'] ?? $tp['jenis_topik'] ?? 'Ujian'); ?>] <?php echo htmlspecialchars($tp['nama_topik']); ?> (<?php echo htmlspecialchars($tp['tahun_ajaran']); ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Hari Filter Tabs -->
            <div style="display: flex; gap: 8px; margin-bottom: 1.5rem; overflow-x: auto; padding-bottom: 4px;">
                <a href="<?php echo base_url('cbt/pengawas/jadwal?topik_id=' . $selectedTopikId . '&hari=Semua'); ?>" 
                   style="padding: 7px 18px; border-radius: 20px; font-size: 0.78rem; font-weight: 800; text-decoration: none; transition: 0.2s; border: 1px solid <?= ($selectedHari === 'Semua') ? '#0284c7; background: #0284c7; color: white;' : '#cbd5e1; background: white; color: #475569;' ?>">
                   Semua Hari
                </a>
                <?php foreach ($hariList as $h): ?>
                <a href="<?php echo base_url('cbt/pengawas/jadwal?topik_id=' . $selectedTopikId . '&hari=' . urlencode($h)); ?>" 
                   style="padding: 7px 18px; border-radius: 20px; font-size: 0.78rem; font-weight: 800; text-decoration: none; transition: 0.2s; border: 1px solid <?= ($selectedHari === $h) ? '#0284c7; background: #0284c7; color: white;' : '#cbd5e1; background: white; color: #475569;' ?>">
                   <?= $h ?>
                </a>
                <?php endforeach; ?>
            </div>

            <!-- Master Matrix Schedule -->
            <div class="schedule-wrapper">
                <table class="master-schedule-table">
                    <thead>
                        <tr>
                            <th class="sticky-sesi-col" style="min-width: 140px;">Sesi / Waktu</th>
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
                        <?php foreach ($displayDays as $day): ?>
                            <!-- Baris Header Hari -->
                            <tr>
                                <td colspan="<?= max(2, count($ruangList) + 1) ?>" class="hari-header">
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <i data-lucide="calendar-days" style="width: 18px; height: 18px;"></i>
                                            <span>Hari <?= $day ?></span>
                                        </div>
                                        <span style="font-size: 0.72rem; background: rgba(255,255,255,0.25); padding: 3px 10px; border-radius: 12px; font-weight: 700;">
                                            <?= count($ruangList) ?> Ruang Ujian
                                        </span>
                                    </div>
                                </td>
                            </tr>

                            <!-- Baris Per Sesi -->
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
                                        $item = $jadwalMatrix[$day][$sLabel][$rId] ?? null;
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
                                                                <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
                                                            </button>
                                                            <button type="button" onclick="hapusPenugasan(<?= (int)$item['id'] ?>)" style="background: #fee2e2; border: none; color: #ef4444; width: 26px; height: 26px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Hapus">
                                                                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="cell-empty-assign" onclick="pilihSlotKosong('<?= $day ?>', '<?= htmlspecialchars($sLabel) ?>', '<?= htmlspecialchars($jRange) ?>', <?= $rId ?>, '<?= htmlspecialchars($rg['nama_ruang']) ?>')">
                                                    <i data-lucide="user-plus" style="width: 15px; height: 15px; margin: 0 auto 3px; display: block; color: #94a3b8;"></i>
                                                    <span>+ Tugaskan</span>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>

                                    <?php if (empty($ruangList)): ?>
                                        <td style="text-align: center; color: #94a3b8; font-size: 0.8rem;">
                                            Silakan tambahkan Ruang Ujian di menu Kelola Ruang terlebih dahulu.
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
<div id="modalPenugasanPengawas" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 99999; backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 15px;">
    <div style="background: #ffffff; border-radius: 20px; width: 100%; max-width: 500px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow: hidden; animation: popIn 0.25s ease;">
        <div style="padding: 16px 20px; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: white; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <i data-lucide="user-check" style="width: 20px; height: 20px;"></i>
                <h3 style="margin: 0; font-size: 1.05rem; font-weight: 800;" id="modalFormTitle">Tugaskan Pengawas Ruang</h3>
            </div>
            <button type="button" onclick="tutupModalPenugasan()" style="background: none; border: none; color: white; cursor: pointer; font-size: 1.2rem;">
                <i data-lucide="x"></i>
            </button>
        </div>

        <form action="<?php echo base_url('cbt/pengawas/jadwal/save'); ?>" method="POST" style="padding: 20px;">
            <input type="hidden" name="topik_id" value="<?= $selectedTopikId ?>">
            <input type="hidden" name="id" id="inputJadwalId" value="0">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                <div>
                    <label style="display: block; font-size: 0.78rem; font-weight: 800; color: #475569; margin-bottom: 5px;">Hari Ujian</label>
                    <select name="hari" id="inputHari" required style="width: 100%; padding: 8px 12px; border-radius: 10px; border: 1px solid #cbd5e1; font-weight: 700; background: #f8fafc;">
                        <?php foreach ($hariList as $h): ?>
                        <option value="<?= $h ?>"><?= $h ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.78rem; font-weight: 800; color: #475569; margin-bottom: 5px;">Sesi Ujian</label>
                    <select name="sesi_label" id="inputSesi" required style="width: 100%; padding: 8px 12px; border-radius: 10px; border: 1px solid #cbd5e1; font-weight: 700; background: #f8fafc;">
                        <?php foreach ($slotList as $sl): ?>
                        <?php $sLbl = $sl['label_sesi'] ?? ('Sesi ' . ($sl['urutan'] ?? 1)); ?>
                        <option value="<?= htmlspecialchars($sLbl) ?>"><?= htmlspecialchars($sLbl) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 0.78rem; font-weight: 800; color: #475569; margin-bottom: 5px;">Ruang Ujian</label>
                <select name="ruang_id" id="inputRuang" required style="width: 100%; padding: 8px 12px; border-radius: 10px; border: 1px solid #cbd5e1; font-weight: 700; background: #f8fafc;">
                    <?php foreach ($ruangList as $rg): ?>
                    <option value="<?= $rg['id'] ?>"><?= htmlspecialchars($rg['nama_ruang']) ?> (Kapasitas: <?= (int)$rg['kapasitas'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 0.78rem; font-weight: 800; color: #475569; margin-bottom: 5px;">Guru / Pengawas yang Bertugas</label>
                <select name="guru_id" id="inputGuru" required style="width: 100%; padding: 8px 12px; border-radius: 10px; border: 1px solid #cbd5e1; font-weight: 700; background: #f8fafc;">
                    <option value="">-- Pilih Guru / Pengawas --</option>
                    <?php foreach ($guruList as $g): ?>
                    <option value="<?= $g['id'] ?>" data-nama="<?= htmlspecialchars($g['nama']) ?>" data-nip="<?= htmlspecialchars($g['nip'] ?? '-') ?>">
                        <?= htmlspecialchars($g['nama']) ?> (NIP: <?= htmlspecialchars($g['nip'] ?: '-') ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px;">
                <button type="button" onclick="tutupModalPenugasan()" style="padding: 9px 16px; border-radius: 10px; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 700; cursor: pointer; color: #475569;">
                    Batal
                </button>
                <button type="submit" style="padding: 9px 20px; border-radius: 10px; border: none; background: #0284c7; color: white; font-weight: 800; cursor: pointer; box-shadow: 0 4px 10px rgba(2, 132, 199, 0.25);">
                    Simpan Penugasan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function bukaModalPenugasan() {
    document.getElementById('inputJadwalId').value = 0;
    document.getElementById('modalFormTitle').textContent = 'Tugaskan Pengawas Ruang';
    document.getElementById('modalPenugasanPengawas').style.display = 'flex';
}

function tutupModalPenugasan() {
    document.getElementById('modalPenugasanPengawas').style.display = 'none';
}

function pilihSlotKosong(hari, sesi, jRange, ruangId, ruangNama) {
    document.getElementById('inputJadwalId').value = 0;
    document.getElementById('inputHari').value = hari;
    document.getElementById('inputSesi').value = sesi;
    document.getElementById('inputRuang').value = ruangId;
    document.getElementById('modalFormTitle').textContent = 'Tugaskan: ' + ruangNama + ' (' + hari + ', ' + sesi + ')';
    document.getElementById('modalPenugasanPengawas').style.display = 'flex';
}

function editPenugasan(item) {
    document.getElementById('inputJadwalId').value = item.id;
    document.getElementById('inputHari').value = item.hari;
    document.getElementById('inputSesi').value = item.sesi_label;
    document.getElementById('inputRuang').value = item.ruang_id;
    document.getElementById('inputGuru').value = item.guru_id;
    document.getElementById('modalFormTitle').textContent = 'Ubah Pengawas: ' + (item.ruang_nama || 'Ruang');
    document.getElementById('modalPenugasanPengawas').style.display = 'flex';
}

function hapusPenugasan(id) {
    Swal.fire({
        title: 'Hapus Penugasan?',
        text: 'Apakah Anda yakin ingin menghapus jadwal pengawas ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo base_url("cbt/pengawas/jadwal/delete"); ?>';
            const inId = document.createElement('input');
            inId.type = 'hidden';
            inId.name = 'id';
            inId.value = id;
            form.appendChild(inId);
            const inT = document.createElement('input');
            inT.type = 'hidden';
            inT.name = 'topik_id';
            inT.value = '<?= $selectedTopikId ?>';
            form.appendChild(inT);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
