<?php
$title = "Jadwal Pelajaran";
$sp = strpos($_SERVER['REQUEST_URI'], base_url('admin/siakad')) !== false ? base_url('admin/siakad') : base_url('siakad');
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
        /* Custom scrollbar */
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
    }
    .kelas-header {
        min-width: 140px;
        background: #f1f5f9 !important;
    }
    .kelas-name { margin-bottom: 4px; font-weight: 900; color: #1e293b; }
    .btn-atur {
        display: inline-block;
        background: #e0e7ff;
        color: #4f46e5;
        font-size: 0.65rem;
        padding: 2px 8px;
        border-radius: 10px;
        text-decoration: none;
        transition: 0.2s;
    }
    .btn-atur:hover { background: #4f46e5; color: #fff; }

    /* Sticky columns */
    .sticky-col-1, .sticky-col-2 {
        position: sticky;
        left: 0;
        background: #f8fafc;
        z-index: 20;
    }
    .sticky-col-2 { left: 40px; border-right: 2px solid #cbd5e1 !important; }
    
    .master-schedule-table thead .sticky-col-1, 
    .master-schedule-table thead .sticky-col-2 {
        z-index: 30; /* Above regular headers and other sticky cols */
    }

    .cell-empty {
        background: #fff;
        text-align: center;
        color: #cbd5e1;
    }
    .cell-filled {
        background: #f0fdf4;
        border-left: 3px solid #10b981;
        transition: 0.2s;
        cursor: default;
    }
    .cell-filled:hover {
        background: #dcfce7;
        transform: scale(1.02);
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        z-index: 5;
        position: relative;
    }
    
    .mapel-name {
        display: block;
        font-weight: 800;
        color: #0f172a;
        font-size: 0.75rem;
        line-height: 1.2;
        margin-bottom: 3px;
    }
    .guru-name {
        display: block;
        font-size: 0.65rem;
        color: #64748b;
        font-weight: 600;
        line-height: 1.1;
    }

    /* Colors based on Hari just for visual variation */
    .hari-Senin .cell-filled { background: #f0f9ff; border-left-color: #3b82f6; }
    .hari-Selasa .cell-filled { background: #fdf4ff; border-left-color: #d946ef; }
    .hari-Rabu .cell-filled { background: #fffbeb; border-left-color: #f59e0b; }
    .hari-Kamis .cell-filled { background: #f0fdf4; border-left-color: #10b981; }
    .hari-Jumat .cell-filled { background: #fff1f2; border-left-color: #f43f5e; }
    .hari-Sabtu .cell-filled { background: #faf5ff; border-left-color: #8b5cf6; }
</style>

<div class="siakad-container">
    <?php include __DIR__ . '/../../sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../../_siakad_header.php'; ?>

        <div class="z-main-content">
        <div class="z-page-head-row">
            <div>
                <h1>Master Jadwal Pelajaran</h1>
                <p>Jadwal konsolidasi untuk Tahun Ajaran <?php echo htmlspecialchars($activeYear['name']); ?> Semester <?php echo htmlspecialchars($semester); ?>.</p>
            </div>
        </div>

        <div class="schedule-wrapper">
            <table class="master-schedule-table">
                <thead>
                    <tr>
                        <th rowspan="2" class="sticky-col-1" style="width:40px;">NO</th>
                        <th rowspan="2" class="sticky-col-2" style="width:120px;">WAKTU</th>
                        <?php foreach($hari_list as $h): ?>
                            <th colspan="<?php echo count($kelas); ?>" class="hari-header"><?php echo strtoupper($h); ?></th>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <?php foreach($hari_list as $h): ?>
                            <?php foreach($kelas as $k): ?>
                                <th class="kelas-header">
                                    <div class="kelas-name"><?php echo htmlspecialchars($k['nama_kelas']); ?></div>
                                    <a href="<?php echo $sp; ?>/jadwal/edit/<?php echo $k['id']; ?>" class="btn-atur" title="Edit Jadwal Kelas Ini">[Atur]</a>
                                </th>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($jam_list)): ?>
                        <tr><td colspan="<?php echo 2 + (count($hari_list) * count($kelas)); ?>" class="text-center" style="padding: 3rem; color: #94a3b8;">Belum ada jadwal yang diatur untuk semester ini.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach($jam_list as $jam): ?>
                        <tr>
                            <td class="sticky-col-1 text-center" style="font-size: 0.8rem; font-weight: 800; color:#475569;"><?php echo $no++; ?></td>
                            <td class="sticky-col-2 text-center" style="font-size: 0.75rem; font-weight: 800; font-family:'JetBrains Mono', monospace; color:#0ea5e9;">
                                <?php echo htmlspecialchars($jam); ?>
                            </td>
                            
                            <?php foreach($hari_list as $h): ?>
                                <?php foreach($kelas as $k): ?>
                                    <?php 
                                    $cell = $jadwal_master[$h][$jam][$k['id']] ?? null;
                                    if($cell): 
                                    ?>
                                    <td class="cell-filled hari-<?php echo str_replace("'", '', $h); ?>">
                                        <span class="mapel-name"><?php echo htmlspecialchars($cell['nama_mapel']); ?></span>
                                        <span class="guru-name"><i data-lucide="user" style="width:10px;height:10px;display:inline;"></i> <?php echo htmlspecialchars($cell['nama_guru']); ?></span>
                                    </td>
                                    <?php else: ?>
                                    <td class="cell-empty">-</td>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>

<script>
    // Initialize lucide icons if not already done by parent layout
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
