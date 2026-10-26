<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$initial = strtoupper(substr($nama, 0, 2));

// get myApps (if needed for sidebar)
$myApps = [];
if(isset($_SESSION['user_id'])) {
    $db_core_view = \App\Core\Database::connect('core');
    try {
        $res = $db_core_view->query("SELECT app_code FROM app_admins WHERE user_id = ".$_SESSION['user_id'])->fetchAll();
        foreach($res as $r) { $myApps[] = $r['app_code']; }
    } catch(\PDOException $e) {}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waka Kurikulum - Aula System</title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . date('YmdH')); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/multi_dashboard.css?v=' . date('YmdH')); ?>">
    <style>
        /* Modern Premium Design Enhancements */
        .glass-card-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
            margin-top: 1.5rem;
            margin-bottom: 2rem;
        }
        .glass-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 20px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.02);
            transition: all 0.3s ease;
        }
        .glass-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 30px rgba(14, 165, 233, 0.08);
            border-color: rgba(14, 165, 233, 0.3);
        }
        .glass-card-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }
        .glass-card-title {
            font-size: 0.82rem;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .glass-card-value {
            font-size: 1.6rem;
            font-weight: 850;
            color: #0f172a;
        }

        .z-tab-nav {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 2rem;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 0px;
        }
        .z-tab {
            font-weight: 800;
            color: #64748b;
            text-decoration: none;
            padding: 0.75rem 0.5rem;
            border-bottom: 3px solid transparent;
            transition: all 0.25s ease;
            font-size: 0.92rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .z-tab:hover {
            color: #0f172a;
        }
        .z-tab.active {
            color: #0ea5e9;
            border-bottom-color: #0ea5e9;
        }

        /* Filter Controls */
        .filter-wrapper {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .filter-select {
            background: #fff;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            padding: 8px 16px;
            font-weight: 700;
            color: #0f172a;
            font-size: 0.88rem;
            outline: none;
            cursor: pointer;
            transition: 0.2s;
            min-width: 200px;
        }
        .filter-select:focus {
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
        }

        /* Tables & Lists */
        .waka-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }
        .waka-table th {
            background: #f8fafc;
            padding: 1rem 1.25rem;
            text-align: left;
            font-size: 0.78rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
        }
        .waka-table td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9rem;
            color: #334155;
            vertical-align: middle;
        }
        .waka-table tr:hover td {
            background: #f8fafc;
        }

        /* Grade Badges */
        .grade-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            font-weight: 800;
            font-size: 0.88rem;
        }
        .grade-a { background: #dcfce7; color: #15803d; }
        .grade-b { background: #e0f2fe; color: #0369a1; }
        .grade-c { background: #fef3c7; color: #b45309; }
        .grade-d { background: #fee2e2; color: #b91c1c; }

        /* Grid Class Card */
        .waka-class-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 1.5rem;
        }
        .waka-class-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01);
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 160px;
        }
        .waka-class-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: #0ea5e9;
        }
        .waka-class-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.06);
            border-color: #0ea5e9;
        }
    </style>
</head>
<body>
<div class="siakad-container">
    <?php include __DIR__ . '/../sidebar_multi_guru.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/../header_multi_guru.php'; ?>

        <div class="z-scroll" style="padding: 0 var(--z-gap) var(--z-gap);">
            
            <!-- Banner Waka Kurikulum -->
            <div class="hero-banner" style="background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%); border-radius: 20px; padding: 2.5rem; color: white; display: flex; align-items: center; justify-content: space-between; margin-top: 1.5rem; position: relative; overflow: hidden; box-shadow: 0 10px 25px rgba(16,185,129,0.15);">
                <div style="position: relative; z-index: 2;">
                    <span style="background: rgba(255,255,255,0.2); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; padding: 4px 12px; border-radius: 20px; display: inline-block; margin-bottom: 10px;">Tugas Tambahan Struktural</span>
                    <h1 style="font-size: 2.2rem; font-weight: 850; margin: 0 0 8px 0; letter-spacing: -0.5px;">Waka Bidang Kurikulum</h1>
                    <p style="color: rgba(255, 255, 255, 0.9); font-size: 1rem; margin: 0; max-width: 600px; line-height: 1.6;">Selamat datang di Pusat Kontrol Kurikulum Madrasah. Monitor daftar mata pelajaran, jadwal pembelajaran di setiap tingkatan, dan rekapitulasi nilai raport siswa.</p>
                </div>
                <i data-lucide="book-open-check" style="position: absolute; right: 2rem; top: 50%; transform: translateY(-50%); width: 150px; height: 150px; color: rgba(255,255,255,0.06); z-index: 1;"></i>
            </div>

            <!-- Dashboard Statistics -->
            <div class="glass-card-row">
                <div class="glass-card">
                    <div class="glass-card-icon" style="background: #f0f9ff; color: #0ea5e9;"><i data-lucide="book-open"></i></div>
                    <div>
                        <div class="glass-card-title">Mata Pelajaran</div>
                        <div class="glass-card-value"><?php echo $total_mapel; ?></div>
                    </div>
                </div>
                <div class="glass-card">
                    <div class="glass-card-icon" style="background: #eff6ff; color: #3b82f6;"><i data-lucide="layers"></i></div>
                    <div>
                        <div class="glass-card-title">Total Kelas</div>
                        <div class="glass-card-value"><?php echo $total_kelas; ?></div>
                    </div>
                </div>
                <div class="glass-card">
                    <div class="glass-card-icon" style="background: #faf5ff; color: #9333ea;"><i data-lucide="users"></i></div>
                    <div>
                        <div class="glass-card-title">Tenaga Pendidik</div>
                        <div class="glass-card-value"><?php echo $total_guru; ?></div>
                    </div>
                </div>
                <div class="glass-card">
                    <div class="glass-card-icon" style="background: #fffbeb; color: #d97706;"><i data-lucide="graduation-cap"></i></div>
                    <div>
                        <div class="glass-card-title">Siswa Aktif</div>
                        <div class="glass-card-value"><?php echo $total_siswa; ?></div>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="z-tab-nav">
                <a href="?tab=overview" class="z-tab <?php echo $tab === 'overview' ? 'active' : ''; ?>"><i data-lucide="layout-grid"></i> Overview Madrasah</a>
                <a href="?tab=mapel" class="z-tab <?php echo $tab === 'mapel' ? 'active' : ''; ?>"><i data-lucide="book"></i> Mata Pelajaran</a>
                <a href="?tab=jadwal" class="z-tab <?php echo $tab === 'jadwal' ? 'active' : ''; ?>"><i data-lucide="calendar"></i> Jadwal Pelajaran</a>
                <a href="?tab=nilai" class="z-tab <?php echo $tab === 'nilai' ? 'active' : ''; ?>"><i data-lucide="file-spreadsheet"></i> Monitoring Nilai</a>
                <a href="?tab=program" class="z-tab <?php echo $tab === 'program' ? 'active' : ''; ?>"><i data-lucide="award"></i> Program Kerja</a>
            </div>

            <!-- Tab Contents -->
            <?php if($tab === 'overview'): ?>
                <h3 style="font-weight: 850; font-size: 1.25rem; color: #0f172a; margin-bottom: 1rem;">Daftar Kelas & Kapasitas</h3>
                <div class="waka-class-grid">
                    <?php foreach($kelasList as $k): ?>
                        <div class="waka-class-card">
                            <div>
                                <h4 style="margin: 0 0 4px 0; font-size: 1.2rem; font-weight: 800; color: #0f172a;">Kelas <?php echo htmlspecialchars($k['nama_kelas']); ?></h4>
                                <span style="font-size: 0.75rem; background: #f1f5f9; padding: 2px 8px; border-radius: 4px; color: #64748b; font-weight: 700; text-transform: uppercase;">Tingkat <?php echo htmlspecialchars($k['tingkat']); ?></span>
                            </div>
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:1.5rem; padding-top:1rem; border-top: 1px solid #f1f5f9;">
                                <div style="font-size: 0.85rem; font-weight: 700; color: #0ea5e9; display:flex; align-items:center; gap:6px;"><i data-lucide="users" style="width:16px;height:16px;"></i> <?php echo $k['jumlah_siswa']; ?> Siswa</div>
                                <a href="?tab=jadwal&kelas_jadwal_id=<?php echo $k['id']; ?>" class="z-btn" style="padding:4px 10px; font-size:0.75rem; background: #e0f2fe; color:#0369a1; text-decoration:none; border-radius:6px; font-weight:700;">Lihat Jadwal</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php elseif($tab === 'mapel'): ?>
                <div class="z-panel">
                    <div class="z-panel-head">
                        <div class="z-panel-title"><i data-lucide="list"></i> Daftar Mata Pelajaran Terdaftar</div>
                    </div>
                    <div style="padding: 0; overflow-x: auto;">
                        <table class="waka-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px; text-align: center;">No</th>
                                    <th>Kode Pelajaran</th>
                                    <th>Nama Mata Pelajaran</th>
                                    <th>Kategori</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($mapelList)): ?>
                                    <tr><td colspan="4" align="center" style="padding: 40px; color: #94a3b8;">Belum ada data mata pelajaran.</td></tr>
                                <?php else: ?>
                                    <?php $no=1; foreach($mapelList as $m): ?>
                                        <tr>
                                            <td align="center" style="font-weight: 700; color: #94a3b8;"><?php echo $no++; ?></td>
                                            <td><span style="font-family: monospace; font-weight: 700; background: #f1f5f9; padding: 2px 8px; border-radius: 4px; color: #475569;"><?php echo htmlspecialchars($m['kode_mapel']); ?></span></td>
                                            <td><strong><?php echo htmlspecialchars($m['nama_mapel']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($m['kategori'] ?? 'Umum'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php elseif($tab === 'jadwal'): ?>
                <div class="filter-wrapper">
                    <div class="filter-group">
                        <label style="font-size: 0.85rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Pilih Kelas:</label>
                        <form method="GET" action="" id="jadwalFilterForm">
                            <input type="hidden" name="tab" value="jadwal">
                            <select name="kelas_jadwal_id" class="filter-select" onchange="document.getElementById('jadwalFilterForm').submit()">
                                <?php foreach($kelasList as $k): ?>
                                    <option value="<?php echo $k['id']; ?>" <?php echo $selected_kelas_jadwal == $k['id'] ? 'selected' : ''; ?>>Kelas <?php echo htmlspecialchars($k['nama_kelas']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>
                    <div>
                        <span style="font-size:0.8rem; background: #e0f7fa; border:1px solid #b2ebf2; color:#006064; padding:4px 10px; border-radius:6px; font-weight:700;"><i data-lucide="info" style="width:14px;height:14px;vertical-align:-3px;margin-right:4px;"></i>Tahun Ajaran: <?php echo htmlspecialchars($active_year_name); ?></span>
                    </div>
                </div>

                <?php foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari): ?>
                    <div class="z-panel" style="margin-bottom: 1.5rem;">
                        <div class="z-panel-head" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 0.75rem 1.25rem;">
                            <div class="z-panel-title" style="font-weight: 850; color: #0f172a; font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">
                                <span style="width: 8px; height: 8px; background: #0ea5e9; border-radius: 50%;"></span>
                                Hari <?php echo $hari; ?>
                            </div>
                        </div>
                        <div style="padding: 0; overflow-x: auto;">
                            <?php if(empty($jadwal_mapel[$hari])): ?>
                                <div style="padding: 2rem; text-align: center; color: #94a3b8; font-size: 0.85rem; font-style: italic;">
                                    Tidak ada jadwal pelajaran untuk hari ini.
                                </div>
                            <?php else: ?>
                                <table class="waka-table" style="border: none; box-shadow: none; border-radius: 0;">
                                    <thead>
                                        <tr style="border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
                                            <th style="padding: 0.75rem 1.25rem; text-align: left; font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase; width: 150px;">Waktu</th>
                                            <th style="padding: 0.75rem 1.25rem; text-align: left; font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Mata Pelajaran</th>
                                            <th style="padding: 0.75rem 1.25rem; text-align: left; font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Guru Pengampu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($jadwal_mapel[$hari] as $j): ?>
                                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                                <td style="padding: 0.75rem 1.25rem; font-size: 0.85rem; color: #334155; font-family: monospace; font-weight: 700;">
                                                    <?php echo date('H:i', strtotime($j['jam_mulai'])); ?> - <?php echo date('H:i', strtotime($j['jam_selesai'])); ?>
                                                </td>
                                                <td style="padding: 0.75rem 1.25rem; font-size: 0.88rem; font-weight: 700; color: #0f172a;">
                                                    <?php echo htmlspecialchars($j['nama_mapel']); ?>
                                                </td>
                                                <td style="padding: 0.75rem 1.25rem; font-size: 0.88rem; color: #475569;">
                                                    <?php echo htmlspecialchars($j['nama_guru']); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php elseif($tab === 'nilai'): ?>
                <div class="filter-wrapper">
                    <form method="GET" action="" id="nilaiFilterForm" style="display: flex; flex-wrap: wrap; gap: 1rem; width: 100%; align-items: center;">
                        <input type="hidden" name="tab" value="nilai">
                        
                        <div class="filter-group">
                            <label style="font-size: 0.85rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Kelas:</label>
                            <select name="kelas_nilai_id" class="filter-select" onchange="document.getElementById('nilaiFilterForm').submit()">
                                <?php foreach($kelasList as $k): ?>
                                    <option value="<?php echo $k['id']; ?>" <?php echo $selected_kelas_nilai == $k['id'] ? 'selected' : ''; ?>>Kelas <?php echo htmlspecialchars($k['nama_kelas']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label style="font-size: 0.85rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Mata Pelajaran:</label>
                            <select name="mapel_nilai_id" class="filter-select" onchange="document.getElementById('nilaiFilterForm').submit()">
                                <?php foreach($mapelList as $m): ?>
                                    <option value="<?php echo $m['id']; ?>" <?php echo $selected_mapel_nilai == $m['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($m['nama_mapel']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>

                <div class="z-panel">
                    <div class="z-panel-head">
                        <div class="z-panel-title"><i data-lucide="table"></i> Rekap Nilai Harian Siswa</div>
                    </div>
                    <div style="padding: 0; overflow-x: auto;">
                        <table class="waka-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px; text-align: center;">No</th>
                                    <th>Nama Siswa</th>
                                    <th>NIS</th>
                                    <th style="text-align: center; width: 100px;">Nilai Tugas</th>
                                    <th style="text-align: center; width: 100px;">Nilai UTS</th>
                                    <th style="text-align: center; width: 100px;">Nilai UAS</th>
                                    <th style="text-align: center; width: 120px;">Nilai Akhir</th>
                                    <th style="text-align: center; width: 80px;">Predikat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($nilaiList)): ?>
                                    <tr><td colspan="8" align="center" style="padding: 40px; color: #94a3b8;">Tidak ada data nilai atau belum ada siswa terdaftar di kelas ini.</td></tr>
                                <?php else: ?>
                                    <?php $no=1; foreach($nilaiList as $n): 
                                        $na = $n['nilai_akhir'];
                                        $grade_class = 'grade-d';
                                        $grade_text = 'D';
                                        if ($na >= 85) { $grade_class = 'grade-a'; $grade_text = 'A'; }
                                        elseif ($na >= 75) { $grade_class = 'grade-b'; $grade_text = 'B'; }
                                        elseif ($na >= 60) { $grade_class = 'grade-c'; $grade_text = 'C'; }
                                    ?>
                                        <tr>
                                            <td align="center" style="font-weight: 700; color: #94a3b8;"><?php echo $no++; ?></td>
                                            <td><strong><?php echo htmlspecialchars($n['nama']); ?></strong></td>
                                            <td><span style="font-family: monospace; font-size: 0.82rem;"><?php echo htmlspecialchars($n['nis'] ?: '-'); ?></span></td>
                                            <td align="center" style="font-weight: 600;"><?php echo $n['nilai_tugas']; ?></td>
                                            <td align="center" style="font-weight: 600;"><?php echo $n['nilai_uts']; ?></td>
                                            <td align="center" style="font-weight: 600;"><?php echo $n['nilai_uas']; ?></td>
                                            <td align="center" style="font-weight: 800; color: #0ea5e9;"><?php echo round($na, 2); ?></td>
                                            <td align="center">
                                                <span class="grade-badge <?php echo $grade_class; ?>"><?php echo $grade_text; ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php elseif($tab === 'program'): ?>
                <div class="z-panel" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%); backdrop-filter: blur(10px); border: 1px solid rgba(226, 232, 240, 0.8); border-radius: 20px; padding: 2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.02); margin-bottom: 2rem;">
                    <div style="display: flex; flex-direction: column; align-items: center; text-align: center; max-width: 700px; margin: 0 auto;">
                        <div style="width: 80px; height: 80px; background: #f0f9ff; color: #0ea5e9; border-radius: 24px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; box-shadow: 0 10px 20px rgba(16,185,129,0.1);">
                            <i data-lucide="award" style="width: 40px; height: 40px;"></i>
                        </div>
                        <h2 style="font-size: 1.6rem; font-weight: 850; color: #0f172a; margin: 0 0 10px 0; letter-spacing: -0.5px;">Buku Program Kerja Waka Kurikulum</h2>
                        <p style="color: #64748b; font-size: 1rem; line-height: 1.6; margin: 0 0 2rem 0;">
                            Dokumen lengkap Program Kerja Wakil Kepala Madrasah Bidang Kurikulum MI Miftahul Huda Mathla'ul Anwar Tahun Pelajaran 2026/2027 telah disusun sesuai standar akreditasi dan supervisi pengawas. Dokumen ini memuat detail Pendahuluan, Profil Lembaga, Program Kerja Tahunan/Semesteran/Bulanan, Pengelolaan Pembelajaran, Supervisi Akademik, dan Lampiran lengkap.
                        </p>
                        
                        <a href="<?php echo base_url('guru/tugas_tambahan/waka_kurikulum/cetak'); ?>" target="_blank" class="z-btn z-btn-primary" style="background: #0ea5e9; color: white; border: none; font-weight: bold; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; padding: 0.8rem 1.8rem; font-size: 1rem; border-radius: 12px; transition: 0.2s; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.25);">
                            <i data-lucide="printer"></i> Cetak Program Kerja Lengkap (PDF / A4)
                        </a>
                    </div>

                    <div style="margin-top: 3rem; border-top: 1px solid #e2e8f0; padding-top: 2rem;">
                        <h3 style="font-weight: 850; font-size: 1.15rem; color: #0f172a; margin: 0 0 1.5rem 0; text-align: center;">Daftar Konten Buku Program Kerja</h3>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; display: flex; gap: 12px;">
                                <div style="color: #0ea5e9; font-weight: 800; font-size: 1.1rem;">I</div>
                                <div>
                                    <h4 style="margin: 0 0 4px 0; font-weight: 800; color: #0f172a; font-size: 0.92rem;">Pendahuluan</h4>
                                    <p style="margin: 0; font-size: 0.8rem; color: #64748b;">Latar belakang, dasar hukum kurikulum, tujuan dan sasaran program.</p>
                                </div>
                            </div>
                            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; display: flex; gap: 12px;">
                                <div style="color: #0ea5e9; font-weight: 800; font-size: 1.1rem;">II</div>
                                <div>
                                    <h4 style="margin: 0 0 4px 0; font-weight: 800; color: #0f172a; font-size: 0.92rem;">Profil & Kondisi Madrasah</h4>
                                    <p style="margin: 0; font-size: 0.8rem; color: #64748b;">Informasi lembaga, daftar pendidik & tendik, data rombel dan peserta didik.</p>
                                </div>
                            </div>
                            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; display: flex; gap: 12px;">
                                <div style="color: #0ea5e9; font-weight: 800; font-size: 1.1rem;">III</div>
                                <div>
                                    <h4 style="margin: 0 0 4px 0; font-weight: 800; color: #0f172a; font-size: 0.92rem;">Program Kerja Utama</h4>
                                    <p style="margin: 0; font-size: 0.8rem; color: #64748b;">Tabel program tahunan (akreditasi), program semester, dan program bulanan.</p>
                                </div>
                            </div>
                            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; display: flex; gap: 12px;">
                                <div style="color: #0ea5e9; font-weight: 800; font-size: 1.1rem;">IV</div>
                                <div>
                                    <h4 style="margin: 0 0 4px 0; font-weight: 800; color: #0f172a; font-size: 0.92rem;">Pengelolaan Pembelajaran</h4>
                                    <p style="margin: 0; font-size: 0.8rem; color: #64748b;">Penyusunan jadwal, pembagian tugas mengajar, dan perangkat administrasi guru.</p>
                                </div>
                            </div>
                            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; display: flex; gap: 12px;">
                                <div style="color: #0ea5e9; font-weight: 800; font-size: 1.1rem;">V</div>
                                <div>
                                    <h4 style="margin: 0 0 4px 0; font-weight: 800; color: #0f172a; font-size: 0.92rem;">Penilaian & Asesmen</h4>
                                    <p style="margin: 0; font-size: 0.8rem; color: #64748b;">Asesmen diagnostik, formatif, sumatif (PTS/PAS/PAT) dan analisis belajar.</p>
                                </div>
                            </div>
                            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; display: flex; gap: 12px;">
                                <div style="color: #0ea5e9; font-weight: 800; font-size: 1.1rem;">VI</div>
                                <div>
                                    <h4 style="margin: 0 0 4px 0; font-weight: 800; color: #0f172a; font-size: 0.92rem;">Supervisi Akademik</h4>
                                    <p style="margin: 0; font-size: 0.8rem; color: #64748b;">Tujuan supervisi, jadwal supervisi, instrumen, dan tindak lanjut.</p>
                                </div>
                            </div>
                            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; display: flex; gap: 12px;">
                                <div style="color: #0ea5e9; font-weight: 800; font-size: 1.1rem;">VII</div>
                                <div>
                                    <h4 style="margin: 0 0 4px 0; font-weight: 800; color: #0f172a; font-size: 0.92rem;">Pengembangan Kompetensi</h4>
                                    <p style="margin: 0; font-size: 0.8rem; color: #64748b;">KKG/MGMP, workshop internal, pengembangan media pembelajaran.</p>
                                </div>
                            </div>
                            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; display: flex; gap: 12px;">
                                <div style="color: #0ea5e9; font-weight: 800; font-size: 1.1rem;">VIII</div>
                                <div>
                                    <h4 style="margin: 0 0 4px 0; font-weight: 800; color: #0f172a; font-size: 0.92rem;">Monitoring & Evaluasi</h4>
                                    <p style="margin: 0; font-size: 0.8rem; color: #64748b;">Monitoring administrasi guru, kehadiran guru, dan evaluasi program kurikulum.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
</body>
</html>


