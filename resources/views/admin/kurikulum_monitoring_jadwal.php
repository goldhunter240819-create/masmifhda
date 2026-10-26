<?php
/* Monitoring Jadwal Pelajaran - Waka Kurikulum */
$adminNama = $_SESSION['nama'] ?? 'Admin';
$adminInitial = strtoupper(substr($adminNama, 0, 2));
$active_page = 'monitoring_jadwal';
$am = 'kurikulum';
$ap = 'monitoring_jadwal';
$current_label = 'Waka Kurikulum';

// Fetch Real Data from Database
$db_siakad = \App\Core\Database::connect('siakad');

// 1. Fetch all kelas
$stmtKelas = $db_siakad->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat, nama_kelas");
$kelas = $stmtKelas->fetchAll();

// 2. Fetch jadwal pelajaran (join with mapel and guru across databases)
$taObj = \App\Core\AcademicYear::current();
$ta = $taObj['id'] ?? 1;
$queryJadwal = "
    SELECT 
        jp.hari, 
        CONCAT(TIME_FORMAT(jp.jam_mulai, '%H:%i'), ' - ', TIME_FORMAT(jp.jam_selesai, '%H:%i')) as jam,
        jp.kelas_id,
        m.nama_mapel,
        g.nama as nama_guru
    FROM jadwal_pelajaran jp
    JOIN mapel m ON jp.mata_pelajaran_id = m.id
    LEFT JOIN aula_db.guru g ON jp.guru_id = g.id
    WHERE jp.tahun_ajaran_id = :ta
    ORDER BY jp.jam_mulai
";
$stmtJadwal = $db_siakad->prepare($queryJadwal);
$stmtJadwal->execute(['ta' => $ta]);
$allJadwal = $stmtJadwal->fetchAll();

// Process the raw data into structured array
$jadwal_master = [];
$jam_list_raw = [];
$hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

foreach ($allJadwal as $row) {
    $hari = $row['hari'];
    $jam = $row['jam'];
    $jam_list_raw[$hari][$jam] = true;
    
    $jadwal_master[$hari][$jam][$row['kelas_id']] = [
        'nama_mapel' => $row['nama_mapel'],
        'nama_guru'  => $row['nama_guru'] ?: 'Belum diatur'
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Jadwal Pelajaran - Waka Kurikulum</title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>?v=<?php echo time(); ?>">
    <!-- Zamrud CSS -->
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . time()); ?>">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .schedule-wrapper {
            width: 100%;
            overflow-x: auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            border: 1px solid var(--z-border);
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
            border-right: 1px solid var(--z-border);
            border-bottom: 1px solid var(--z-border);
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
            box-shadow: 0 1px 0 var(--z-border);
        }
        .hari-header {
            background: var(--z-primary) !important;
            color: #fff !important;
            font-size: 0.9rem !important;
            padding: 12px !important;
            border-right: 1px solid rgba(255,255,255,0.2) !important;
        }
        .kelas-header {
            width: 160px;
            min-width: 160px;
            max-width: 160px;
            background: #f1f5f9 !important;
        }
        .kelas-name { margin-bottom: 4px; font-weight: 900; color: #1e293b; }

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
            width: 160px;
            max-width: 160px;
            overflow: hidden;
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
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .guru-name {
            display: block;
            font-size: 0.65rem;
            color: #64748b;
            font-weight: 600;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Colors based on Hari just for visual variation */
        .hari-Senin .cell-filled { background: #f0f9ff; border-left-color: #3b82f6; }
        .hari-Selasa .cell-filled { background: #fdf4ff; border-left-color: #d946ef; }
        .hari-Rabu .cell-filled { background: #fffbeb; border-left-color: #f59e0b; }
        .hari-Kamis .cell-filled { background: #f0fdf4; border-left-color: #10b981; }
        .hari-Jumat .cell-filled { background: #fff1f2; border-left-color: #f43f5e; }
        .hari-Sabtu .cell-filled { background: #f8fafc; border-left-color: #94a3b8; }
    </style>
</head>
<body>
    <div class="z-wrap">
        <?php include __DIR__ . '/_sidebar_kurikulum.php'; ?>
        
        <main class="z-main">
            <header class="z-header">
                <div class="z-header-left">
                    <button class="z-hamburger" id="zHamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
                    <span class="z-header-title">Monitoring Jadwal</span>
                </div>
                <div class="z-header-user">
                    <div class="z-user-info">
                        <div class="name"><?php echo $adminNama; ?></div>
                        <div class="role">Administrator</div>
                    </div>
                    <div class="z-avatar"><?php echo $adminInitial; ?></div>
                </div>
            </header>
            
            <div class="z-scroll">
                
                <div class="elite-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: var(--z-radius); margin-bottom: 2rem; color: white;">
                    <div>
                        <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                            <i data-lucide="calendar" style="color: rgba(255,255,255,0.8);"></i> Monitoring Jadwal Pelajaran
                        </h1>
                        <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Pantau alokasi jadwal pelajaran harian per rombel kelas.</p>
                    </div>
                </div>

                <?php foreach($hari_list as $h): ?>
                <div style="background: var(--z-primary); padding: 12px 20px; border-radius: 12px 12px 0 0; color: white; font-weight: 800; font-size: 1.1rem; text-transform: uppercase; margin-top: 2rem; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="calendar"></i> HARI <?php echo strtoupper($h); ?>
                </div>
                <div class="schedule-wrapper" style="border-radius: 0 0 12px 12px; margin-bottom: 0;">
                    <table class="master-schedule-table">
                        <thead>
                            <tr>
                                <th class="sticky-col-1" style="width:40px;">NO</th>
                                <th class="sticky-col-2" style="width:120px;">WAKTU</th>
                                <?php foreach($kelas as $k): ?>
                                    <th class="kelas-header">
                                        <div class="kelas-name"><?php echo htmlspecialchars($k['nama_kelas']); ?></div>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $jam_list = isset($jam_list_raw[$h]) ? array_keys($jam_list_raw[$h]) : [];
                            sort($jam_list);
                            if(empty($jam_list)): 
                            ?>
                                <tr><td colspan="<?php echo 2 + count($kelas); ?>" style="padding: 3rem; color: #94a3b8; text-align: center;">Belum ada jadwal yang diatur untuk hari ini.</td></tr>
                            <?php else: ?>
                                <?php $no = 1; foreach($jam_list as $jam): ?>
                                <tr>
                                    <td class="sticky-col-1 text-center" style="font-size: 0.8rem; font-weight: 800; color:#475569; text-align:center;">
                                        <?php echo (strpos($jam, 'ISTIRAHAT') !== false) ? '-' : $no++; ?>
                                    </td>
                                    <td class="sticky-col-2 text-center" style="font-size: 0.75rem; font-weight: 800; font-family: monospace; color:var(--z-primary); text-align:center;">
                                        <?php echo htmlspecialchars($jam); ?>
                                    </td>
                                    
                                    <?php if(strpos($jam, 'ISTIRAHAT') !== false): ?>
                                        <td colspan="<?php echo count($kelas); ?>" style="background: #f8fafc; text-align: center; font-weight: 800; color: #64748b; letter-spacing: 2px;">
                                            ISTIRAHAT
                                        </td>
                                    <?php else: ?>
                                        <?php foreach($kelas as $k): ?>
                                            <?php 
                                            $cell = $jadwal_master[$h][$jam][$k['id']] ?? null;
                                            if($cell): 
                                            ?>
                                            <td class="cell-filled hari-<?php echo str_replace("'", '', $h); ?>" title="<?php echo htmlspecialchars($cell['nama_mapel'] . ' - ' . $cell['nama_guru']); ?>">
                                                <span class="mapel-name"><?php echo htmlspecialchars($cell['nama_mapel']); ?></span>
                                                <span class="guru-name"><i data-lucide="user" style="width:10px;height:10px;display:inline;"></i> <?php echo htmlspecialchars($cell['nama_guru']); ?></span>
                                            </td>
                                            <?php else: ?>
                                            <td class="cell-empty">-</td>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php endforeach; ?>

            </div>
        </main>
    </div>
    <script>
        lucide.createIcons();
        function zToggleSidebar() {
            document.querySelector('.z-sidebar').classList.toggle('open');
        }
    </script>
</body>
</html>
