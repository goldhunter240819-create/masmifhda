<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$initial = strtoupper(substr($nama, 0, 2));

$myApps = [];
if(isset($_SESSION['user_id'])) {
    $db_core_view = \App\Core\Database::connect('core');
    try {
        $res = $db_core_view->query("SELECT app_code FROM app_admins WHERE user_id = ".$_SESSION['user_id'])->fetchAll();
        foreach($res as $r) { $myApps[] = $r['app_code']; }
    } catch(\PDOException $e) {}
}

$db_c = \App\Core\Database::connect('core');
$inst = $db_c->query("SELECT * FROM institusi LIMIT 1")->fetch();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title_page; ?> - Aula System</title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . date('YmdH')); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/multi_dashboard.css?v=' . date('YmdH')); ?>">
    <style>
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
            overflow-x: auto;
            white-space: nowrap;
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
        .filter-select, .filter-input {
            background: #fff;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            padding: 8px 14px;
            font-weight: 700;
            color: #0f172a;
            font-size: 0.88rem;
            outline: none;
            cursor: pointer;
            transition: 0.2s;
        }
        .filter-select:focus, .filter-input:focus {
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
            min-height: 150px;
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
            
            <!-- Banner Kepala Madrasah -->
            <div class="hero-banner" style="background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%); border-radius: 20px; padding: 2.5rem; color: white; display: flex; align-items: center; justify-content: space-between; margin-top: 1.5rem; position: relative; overflow: hidden; box-shadow: 0 10px 25px rgba(16,185,129,0.15);">
                <div style="position: relative; z-index: 2;">
                    <span style="background: rgba(255,255,255,0.2); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; padding: 4px 12px; border-radius: 20px; display: inline-block; margin-bottom: 10px;">Manajemen Puncak Madrasah</span>
                    <h1 style="font-size: 2.2rem; font-weight: 850; margin: 0 0 8px 0; letter-spacing: -0.5px;">Kepala Madrasah (Kamad)</h1>
                    <p style="color: rgba(255, 255, 255, 0.9); font-size: 1rem; margin: 0; max-width: 650px; line-height: 1.6;">Selamat datang di Panel Utama Kepala Madrasah. Monitor keterlaksaan program pembelajaran guru, capaian nilai rapor siswa, dan kelola dokumen administrasi berstandar akreditasi secara digital.</p>
                </div>
                <i data-lucide="user-cog" style="position: absolute; right: 2rem; top: 50%; transform: translateY(-50%); width: 150px; height: 150px; color: rgba(255,255,255,0.06); z-index: 1;"></i>
            </div>

            <!-- Dashboard Statistics -->
            <div class="glass-card-row">
                <div class="glass-card">
                    <div class="glass-card-icon" style="background: #faf5ff; color: #9333ea;"><i data-lucide="users"></i></div>
                    <div>
                        <div class="glass-card-title">Guru & Tendik</div>
                        <div class="glass-card-value"><?php echo $stats['guru']; ?></div>
                    </div>
                </div>
                <div class="glass-card">
                    <div class="glass-card-icon" style="background: #fffbeb; color: #d97706;"><i data-lucide="graduation-cap"></i></div>
                    <div>
                        <div class="glass-card-title">Siswa Aktif</div>
                        <div class="glass-card-value"><?php echo $stats['siswa']; ?></div>
                    </div>
                </div>
                <div class="glass-card">
                    <div class="glass-card-icon" style="background: #eff6ff; color: #3b82f6;"><i data-lucide="layers"></i></div>
                    <div>
                        <div class="glass-card-title">Rombongan Belajar</div>
                        <div class="glass-card-value"><?php echo $stats['kelas']; ?></div>
                    </div>
                </div>
                <div class="glass-card">
                    <div class="glass-card-icon" style="background: #f0f9ff; color: #0ea5e9;"><i data-lucide="book-open"></i></div>
                    <div>
                        <div class="glass-card-title">Mata Pelajaran</div>
                        <div class="glass-card-value"><?php echo $stats['mapel']; ?></div>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="z-tab-nav">
                <a href="<?php echo base_url('guru/kamad/dashboard'); ?>" class="z-tab <?php echo $tab === 'overview' ? 'active' : ''; ?>"><i data-lucide="layout-grid"></i> Ringkasan Sekolah</a>
                <a href="<?php echo base_url('guru/kamad/program'); ?>" class="z-tab <?php echo $tab === 'program' ? 'active' : ''; ?>"><i data-lucide="award"></i> Program Kerja Kamad</a>
                <a href="<?php echo base_url('guru/kamad/monitoring/jurnal'); ?>" class="z-tab <?php echo $tab === 'jurnal' ? 'active' : ''; ?>"><i data-lucide="book-open"></i> Monitor Jurnal Guru</a>
                <a href="<?php echo base_url('guru/kamad/monitoring/nilai'); ?>" class="z-tab <?php echo $tab === 'nilai' ? 'active' : ''; ?>"><i data-lucide="file-spreadsheet"></i> Monitor Nilai Siswa</a>
            </div>

            <!-- Tab Contents -->
            <?php if($tab === 'overview'): ?>
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start;">
                    <div>
                        <!-- Presensi Hari Ini Widget -->
                        <div class="z-panel" style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 1.5rem; margin-bottom: 1.5rem;">
                            <h4 style="font-weight: 850; font-size: 1.15rem; color: #0f172a; margin: 0 0 1.25rem 0; display:flex; align-items:center; gap:8px;"><i data-lucide="check-square" style="color:#0ea5e9;"></i> Status Presensi Hari Ini (<?php echo date('d-m-Y'); ?>)</h4>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                                <!-- Presensi Siswa -->
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                        <span style="font-weight: 800; font-size: 0.88rem; color: #475569;">Presensi Siswa</span>
                                        <span style="font-weight: 850; font-size: 1.2rem; color: #0ea5e9;"><?php echo $absenSiswaPersen; ?>%</span>
                                    </div>
                                    <div style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden; margin-bottom: 12px;">
                                        <div style="width: <?php echo $absenSiswaPersen; ?>%; height: 100%; background: #0ea5e9; border-radius: 4px;"></div>
                                    </div>
                                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px; text-align: center;">
                                        <div style="background: #dcfce7; border-radius: 8px; padding: 6px 4px;">
                                            <div style="font-size: 0.65rem; font-weight: 800; color: #15803d; text-transform: uppercase;">Hadir</div>
                                            <div style="font-size: 0.95rem; font-weight: 850; color: #166534;"><?php echo $hadirSiswa; ?></div>
                                        </div>
                                        <div style="background: #e0f2fe; border-radius: 8px; padding: 6px 4px;">
                                            <div style="font-size: 0.65rem; font-weight: 800; color: #0369a1; text-transform: uppercase;">Izin</div>
                                            <div style="font-size: 0.95rem; font-weight: 850; color: #075985;"><?php echo $izinSiswa; ?></div>
                                        </div>
                                        <div style="background: #fef3c7; border-radius: 8px; padding: 6px 4px;">
                                            <div style="font-size: 0.65rem; font-weight: 800; color: #b45309; text-transform: uppercase;">Sakit</div>
                                            <div style="font-size: 0.95rem; font-weight: 850; color: #854d0e;"><?php echo $sakitSiswa; ?></div>
                                        </div>
                                        <div style="background: #fee2e2; border-radius: 8px; padding: 6px 4px;">
                                            <div style="font-size: 0.65rem; font-weight: 800; color: #b91c1c; text-transform: uppercase;">Alpha</div>
                                            <div style="font-size: 0.95rem; font-weight: 850; color: #991b1b;"><?php echo $alphaSiswa; ?></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Presensi Guru -->
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between;">
                                    <div>
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                            <span style="font-weight: 800; font-size: 0.88rem; color: #475569;">Kehadiran Guru</span>
                                            <span style="font-weight: 800; font-size: 0.88rem; color: #64748b;"><?php echo $hadirGuru; ?> / <?php echo $stats['guru']; ?> Hadir</span>
                                        </div>
                                        <div style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden; margin-bottom: 12px;">
                                            <div style="width: <?php echo $stats['guru'] > 0 ? round(($hadirGuru / $stats['guru']) * 100) : 0; ?>%; height: 100%; background: #6366f1; border-radius: 4px;"></div>
                                        </div>
                                    </div>
                                    
                                    <?php if ($tidakHadirGuru > 0): ?>
                                        <div style="font-size: 0.78rem; background: #fef2f2; border: 1px dashed #fca5a5; border-radius: 8px; padding: 6px 10px;">
                                            <strong style="color: #991b1b; display: flex; align-items: center; gap: 4px;"><i data-lucide="alert-circle" style="width:14px;height:14px;"></i> Belum Presensi (<?php echo $tidakHadirGuru; ?> Guru):</strong>
                                            <div style="display: flex; flex-wrap: wrap; gap: 4px; margin-top: 4px; max-height: 55px; overflow-y: auto;">
                                                <?php foreach ($tidakHadirGuruList as $tg): ?>
                                                    <span style="background: white; border: 1px solid #fee2e2; border-radius: 4px; padding: 1px 6px; font-weight: 700; color: #b91c1c; font-size: 0.7rem;"><?php echo htmlspecialchars($tg['nama']); ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div style="font-size: 0.78rem; background: #f0f9ff; border: 1px dashed #6ee7b7; border-radius: 8px; padding: 6px 10px; color: #0369a1; font-weight: 700; display: flex; align-items: center; gap: 4px; justify-content: center;">
                                            <i data-lucide="check-circle" style="width:14px;height:14px;"></i> Semua guru telah hadir hari ini!
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Rombongan Belajar Grid -->
                        <h3 style="font-weight: 850; font-size: 1.25rem; color: #0f172a; margin-bottom: 1rem;">Rombongan Belajar (Rombel)</h3>
                        <div class="waka-class-grid" style="margin-bottom: 2rem;">
                            <?php if(empty($kelasList)): ?>
                                <div style="padding: 2rem; text-align: center; color: #94a3b8; grid-column: span 3;">Belum ada data kelas yang terdaftar.</div>
                            <?php else: ?>
                                <?php foreach($kelasList as $k): ?>
                                    <div class="waka-class-card">
                                        <div>
                                            <h4 style="margin: 0 0 4px 0; font-size: 1.2rem; font-weight: 800; color: #0f172a;">Kelas <?php echo htmlspecialchars($k['nama_kelas']); ?></h4>
                                            <span style="font-size: 0.75rem; background: #f1f5f9; padding: 2px 8px; border-radius: 4px; color: #64748b; font-weight: 700; text-transform: uppercase;">Tingkat <?php echo htmlspecialchars($k['tingkat']); ?></span>
                                        </div>
                                        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:1.5rem; padding-top:1rem; border-top: 1px solid #f1f5f9;">
                                            <div style="font-size: 0.85rem; font-weight: 700; color: #0ea5e9; display:flex; align-items:center; gap:6px;"><i data-lucide="users" style="width:16px;height:16px;"></i> <?php echo $k['jumlah_siswa']; ?> Siswa</div>
                                            <a href="<?php echo base_url('guru/kamad/monitoring/nilai?kelas_id=' . $k['id']); ?>" class="z-btn" style="padding:4px 10px; font-size:0.75rem; background: #e0f2fe; color:#0369a1; text-decoration:none; border-radius:6px; font-weight:700;">Pantau Nilai</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <!-- Pengumuman Terkini Widget -->
                        <div class="z-panel" style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01);">
                            <h4 style="font-weight: 850; font-size: 1.1rem; color: #0f172a; margin: 0 0 1.25rem 0; display:flex; align-items:center; gap:8px;"><i data-lucide="megaphone" style="color:#f59e0b;"></i> Pengumuman Terkini</h4>
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                <?php if (empty($pengumumanList)): ?>
                                    <div style="padding: 1.5rem; text-align: center; color: #94a3b8; font-size: 0.85rem; border: 1px dashed #e2e8f0; border-radius: 12px; background: #f8fafc;">Belum ada pengumuman terbaru.</div>
                                <?php else: ?>
                                    <?php foreach ($pengumumanList as $p): ?>
                                        <div style="background: #faf5ff; border: 1px solid #f3e8ff; border-radius: 12px; padding: 12px 14px;">
                                            <div style="font-weight: 800; color: #7e22ce; font-size: 0.9rem; margin-bottom: 4px;"><?php echo htmlspecialchars($p['judul']); ?></div>
                                            <div style="font-size: 0.8rem; color: #475569; line-height: 1.5;"><?php echo nl2br(htmlspecialchars($p['pesan'])); ?></div>
                                            <div style="font-size: 0.68rem; color: #a21caf; font-weight: 700; margin-top: 8px; display: flex; align-items: center; gap: 4px;"><i data-lucide="clock" style="width:12px;height:12px;"></i> Tanggal: <?php echo date('d-m-Y', strtotime($p['tanggal_dibuat'] ?? 'now')); ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Aktivitas KBM Hari Ini Widget (Agenda) -->
                        <div class="z-panel" style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01);">
                            <h4 style="font-weight: 850; font-size: 1.1rem; color: #0f172a; margin: 0 0 1.25rem 0; display:flex; align-items:center; gap:8px;"><i data-lucide="calendar" style="color:#3b82f6;"></i> Aktivitas KBM Hari Ini</h4>
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                <?php if (empty($jurnalsToday)): ?>
                                    <div style="padding: 1.5rem; text-align: center; color: #94a3b8; font-size: 0.85rem; border: 1px dashed #e2e8f0; border-radius: 12px; background: #f8fafc;">Belum ada jurnal KBM masuk hari ini.</div>
                                <?php else: ?>
                                    <?php foreach ($jurnalsToday as $jt): ?>
                                        <div style="background: #eff6ff; border: 1px solid #dbeafe; border-radius: 12px; padding: 12px 14px; font-size: 0.8rem; line-height: 1.5;">
                                            <div style="display:flex; justify-content:space-between; margin-bottom:4px; align-items:center;">
                                                <strong style="color: #1e40af; font-size:0.85rem;"><?php echo htmlspecialchars($jt['nama_guru']); ?></strong>
                                                <span style="background: #dbeafe; color: #1e40af; font-weight: 800; padding: 2px 6px; border-radius: 6px; font-size: 0.68rem; text-transform: uppercase;">Kelas <?php echo htmlspecialchars($jt['nama_kelas']); ?></span>
                                            </div>
                                            <div style="color: #475569; font-weight: 700; font-size:0.78rem; margin-bottom: 4px;">Mapel: <?php echo htmlspecialchars($jt['nama_mapel']); ?></div>
                                            <div style="color: #64748b; font-style: italic;">Materi: "<?php echo htmlspecialchars(strlen($jt['materi']) > 80 ? substr($jt['materi'], 0, 77).'...' : $jt['materi']); ?>"</div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- School Profile Block -->
                        <div class="z-panel" style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01);">
                            <h4 style="font-weight: 850; font-size: 1.1rem; color: #0f172a; margin: 0 0 1.25rem 0; display:flex; align-items:center; gap:8px;"><i data-lucide="school" style="color:#0ea5e9;"></i> Profil Madrasah</h4>
                            <div style="display: flex; flex-direction: column; gap: 1rem; font-size: 0.88rem; color: #475569;">
                                <div>
                                    <label style="display: block; font-weight: 800; color: #94a3b8; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px;">Nama Lembaga</label>
                                    <span style="font-weight: 700; color: #0f172a;"><?php echo htmlspecialchars($inst['nama'] ?? 'MI Miftahul Huda'); ?></span>
                                </div>
                                <div>
                                    <label style="display: block; font-weight: 800; color: #94a3b8; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px;">Alamat</label>
                                    <span style="line-height: 1.4;"><?php echo htmlspecialchars(($inst['alamat'] ?? '-') . ', ' . ($inst['desa'] ?? '') . ', ' . ($inst['kecamatan'] ?? '') . ', ' . ($inst['kota'] ?? '') . ', ' . ($inst['provinsi'] ?? '')); ?></span>
                                </div>
                                <div>
                                    <label style="display: block; font-weight: 800; color: #94a3b8; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px;">Kontak & Surel</label>
                                    <span><?php echo htmlspecialchars($inst['telepon'] ?? '-'); ?> / <?php echo htmlspecialchars($inst['email'] ?? '-'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php elseif($tab === 'program'): ?>
                <div class="z-panel" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.8) 100%); backdrop-filter: blur(10px); border: 1px solid rgba(226, 232, 240, 0.8); border-radius: 20px; padding: 2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.02); margin-bottom: 2rem;">
                    <div style="display: flex; flex-direction: column; align-items: center; text-align: center; max-width: 700px; margin: 0 auto;">
                        <div style="width: 80px; height: 80px; background: #f0f9ff; color: #0ea5e9; border-radius: 24px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; box-shadow: 0 10px 20px rgba(16,185,129,0.1);">
                            <i data-lucide="award" style="width: 40px; height: 40px;"></i>
                        </div>
                        <h2 style="font-size: 1.6rem; font-weight: 850; color: #0f172a; margin: 0 0 10px 0; letter-spacing: -0.5px;">Buku Program Kerja Kepala Madrasah</h2>
                        <p style="color: #64748b; font-size: 1rem; line-height: 1.6; margin: 0 0 2rem 0;">
                            Dokumen lengkap Program Kerja Kepala Madrasah MI Miftahul Huda Mathla'ul Anwar Tahun Pelajaran 2026/2027 telah disusun sesuai standar akreditasi BAN-PDM dan Penilaian Kinerja Kepala Madrasah (PKKM). Dokumen ini memuat detail target kompetensi manajerial, kewirausahaan, supervisi akademik, pembinaan guru/tenaga kependidikan, serta lampiran penugasan resmi.
                        </p>
                        
                        <a href="<?php echo base_url('guru/kamad/program/cetak'); ?>" target="_blank" class="z-btn z-btn-primary" style="background: #0ea5e9; color: white; border: none; font-weight: bold; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; padding: 0.8rem 1.8rem; font-size: 1rem; border-radius: 12px; transition: 0.2s; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.25);">
                            <i data-lucide="printer"></i> Cetak Program Kerja Lengkap (PDF / A4)
                        </a>
                    </div>

                    <div style="margin-top: 3rem; border-top: 1px solid #e2e8f0; padding-top: 2rem;">
                        <h3 style="font-weight: 850; font-size: 1.15rem; color: #0f172a; margin: 0 0 1.5rem 0; text-align: center;">Daftar Konten Buku Program Kerja</h3>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
                            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; display: flex; gap: 12px;">
                                <div style="color: #0ea5e9; font-weight: 800; font-size: 1.1rem;">I</div>
                                <div>
                                    <h4 style="margin: 0 0 4px 0; font-weight: 800; color: #0f172a; font-size: 0.92rem;">Pendahuluan</h4>
                                    <p style="margin: 0; font-size: 0.8rem; color: #64748b; line-height: 1.45;">Latar belakang pentingnya tata kelola madrasah, dasar hukum lengkap undang-undang & keputusan dirjen kemenag, dan sasaran strategis.</p>
                                </div>
                            </div>
                            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; display: flex; gap: 12px;">
                                <div style="color: #0ea5e9; font-weight: 800; font-size: 1.1rem;">II</div>
                                <div>
                                    <h4 style="margin: 0 0 4px 0; font-weight: 800; color: #0f172a; font-size: 0.92rem;">Profil & Kondisi Madrasah</h4>
                                    <p style="margin: 0; font-size: 0.8rem; color: #64748b; line-height: 1.45;">Detail profil MI, data riil pendidik & tendik, keadaan rombongan belajar, serta jumlah siswa yang dimuat secara dinamis.</p>
                                </div>
                            </div>
                            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; display: flex; gap: 12px;">
                                <div style="color: #0ea5e9; font-weight: 800; font-size: 1.1rem;">III</div>
                                <div>
                                    <h4 style="margin: 0 0 4px 0; font-weight: 800; color: #0f172a; font-size: 0.92rem;">Matriks Program Kerja</h4>
                                    <p style="margin: 0; font-size: 0.8rem; color: #64748b; line-height: 1.45;">Tabel program terperinci untuk 3 pilar: Kompetensi Manajerial, Supervisi Akademik, Kewirausahaan, beserta jadwal rencana aksi bulanan.</p>
                                </div>
                            </div>
                            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; display: flex; gap: 12px;">
                                <div style="color: #0ea5e9; font-weight: 800; font-size: 1.1rem;">IV</div>
                                <div>
                                    <h4 style="margin: 0 0 4px 0; font-weight: 800; color: #0f172a; font-size: 0.92rem;">Program Supervisi Akademik</h4>
                                    <p style="margin: 0; font-size: 0.8rem; color: #64748b; line-height: 1.45;">Rencana pengawasan administrasi perencanaan (RPP/Modul), pelaksanaan mengajar di kelas, tindak lanjut, dan jadwal klinis guru.</p>
                                </div>
                            </div>
                            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; display: flex; gap: 12px;">
                                <div style="color: #0ea5e9; font-weight: 800; font-size: 1.1rem;">V</div>
                                <div>
                                    <h4 style="margin: 0 0 4px 0; font-weight: 800; color: #0f172a; font-size: 0.92rem;">Evaluasi & Penutup</h4>
                                    <p style="margin: 0; font-size: 0.8rem; color: #64748b; line-height: 1.45;">Mekanisme evaluasi ketercapaian program kerja secara berkala, hambatan, kesimpulan, serta lampiran penunjang.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif($tab === 'jurnal'): ?>
                <div class="filter-wrapper">
                    <form method="GET" action="<?php echo base_url('guru/kamad/monitoring/jurnal'); ?>" id="jurnalFilterForm" style="display: flex; flex-wrap: wrap; gap: 1rem; width: 100%; align-items: center;">
                        <div class="filter-group">
                            <label style="font-size: 0.85rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Mulai:</label>
                            <input type="date" name="tanggal_mulai" class="filter-input" value="<?php echo htmlspecialchars($filter_tanggal_mulai); ?>" onchange="document.getElementById('jurnalFilterForm').submit()">
                        </div>

                        <div class="filter-group">
                            <label style="font-size: 0.85rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Sampai:</label>
                            <input type="date" name="tanggal_sampai" class="filter-input" value="<?php echo htmlspecialchars($filter_tanggal_sampai); ?>" onchange="document.getElementById('jurnalFilterForm').submit()">
                        </div>

                        <div class="filter-group">
                            <label style="font-size: 0.85rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Kelas:</label>
                            <select name="kelas_id" class="filter-select" onchange="document.getElementById('jurnalFilterForm').submit()">
                                <option value="0">-- Semua Kelas --</option>
                                <?php foreach($kelasList as $k): ?>
                                    <option value="<?php echo $k['id']; ?>" <?php echo $filter_kelas == $k['id'] ? 'selected' : ''; ?>>Kelas <?php echo htmlspecialchars($k['nama_kelas']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>

                <div class="z-panel">
                    <div class="z-panel-head">
                        <div class="z-panel-title"><i data-lucide="book-open"></i> Rekap Jurnal Mengajar Harian Guru</div>
                    </div>
                    <div style="padding: 0; overflow-x: auto;">
                        <table class="waka-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px; text-align: center;">No</th>
                                    <th style="width: 110px;">Tanggal</th>
                                    <th>Guru</th>
                                    <th>Kelas & Mapel</th>
                                    <th>Materi Pelajaran</th>
                                    <th style="text-align: center; width: 180px;">Presensi Siswa</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($jurnals)): ?>
                                    <tr><td colspan="7" align="center" style="padding: 40px; color: #94a3b8;">Tidak ada data jurnal pengajaran untuk kriteria ini.</td></tr>
                                <?php else: ?>
                                    <?php $no=1; foreach($jurnals as $j): ?>
                                        <tr>
                                            <td align="center" style="font-weight: 700; color: #94a3b8;"><?php echo $no++; ?></td>
                                            <td><span style="font-weight: 700; color: #475569;"><?php echo date('d-m-Y', strtotime($j['tanggal'])); ?></span></td>
                                            <td><strong><?php echo htmlspecialchars($j['nama_guru']); ?></strong></td>
                                            <td>
                                                <span style="background: #f0f9ff; color: #0369a1; padding: 2px 8px; border-radius: 4px; font-weight: 700; font-size: 0.78rem; display:inline-block; margin-bottom:3px;">Kelas <?php echo htmlspecialchars($j['nama_kelas']); ?></span>
                                                <div style="font-weight:600; color:#334155; font-size:0.85rem;"><?php echo htmlspecialchars($j['nama_mapel']); ?></div>
                                            </td>
                                            <td><?php echo htmlspecialchars($j['materi']); ?></td>
                                            <td align="center">
                                                <?php 
                                                $ad = isset($j['absen_detail']) ? $j['absen_detail'] : ['hadir'=>[],'izin'=>[],'sakit'=>[],'alpa'=>[],'total'=>0];
                                                $cntHadir = count($ad['hadir']);
                                                $cntIzin = count($ad['izin']);
                                                $cntSakit = count($ad['sakit']);
                                                $cntAlpa = count($ad['alpa']);
                                                $jsonDetail = htmlspecialchars(json_encode([
                                                    'hadir' => $ad['hadir'],
                                                    'izin' => $ad['izin'],
                                                    'sakit' => $ad['sakit'],
                                                    'alpa' => $ad['alpa'],
                                                    'total' => $ad['total']
                                                ]), ENT_QUOTES, 'UTF-8');
                                                ?>
                                                <div onclick="openAbsenModal(<?= $jsonDetail ?>, '<?= htmlspecialchars($j['nama_kelas'], ENT_QUOTES) ?>', '<?= htmlspecialchars($j['nama_mapel'], ENT_QUOTES) ?>', '<?= date('d-m-Y', strtotime($j['tanggal'])) ?>', '<?= htmlspecialchars($j['nama_guru'], ENT_QUOTES) ?>')" 
                                                     class="absen-card-btn" 
                                                     title="Klik untuk melihat nama siswa" 
                                                     style="cursor:pointer; display:inline-flex; flex-direction:column; gap:4px; align-items:center; background:#ffffff; padding:6px 10px; border-radius:10px; border:1px solid #cbd5e1; box-shadow: 0 1px 3px rgba(0,0,0,0.04); transition:all 0.2s ease;">
                                                    <div style="display:flex; gap:3px; flex-wrap:nowrap; justify-content:center; align-items:center;">
                                                        <span style="background:#dcfce7; color:#15803d; border:1px solid #86efac; padding:2px 6px; border-radius:6px; font-size:0.7rem; font-weight:800;">
                                                            <?= $cntHadir ?> H
                                                        </span>
                                                        <span style="background:<?= $cntIzin > 0 ? '#dbeafe' : '#f1f5f9' ?>; color:<?= $cntIzin > 0 ? '#1d4ed8' : '#94a3b8' ?>; border:1px solid <?= $cntIzin > 0 ? '#93c5fd' : '#e2e8f0' ?>; padding:2px 6px; border-radius:6px; font-size:0.7rem; font-weight:800;">
                                                            <?= $cntIzin ?> I
                                                        </span>
                                                        <span style="background:<?= $cntSakit > 0 ? '#fef3c7' : '#f1f5f9' ?>; color:<?= $cntSakit > 0 ? '#b45309' : '#94a3b8' ?>; border:1px solid <?= $cntSakit > 0 ? '#fde047' : '#e2e8f0' ?>; padding:2px 6px; border-radius:6px; font-size:0.7rem; font-weight:800;">
                                                            <?= $cntSakit ?> S
                                                        </span>
                                                        <span style="background:<?= $cntAlpa > 0 ? '#fee2e2' : '#f1f5f9' ?>; color:<?= $cntAlpa > 0 ? '#b91c1c' : '#94a3b8' ?>; border:1px solid <?= $cntAlpa > 0 ? '#fca5a5' : '#e2e8f0' ?>; padding:2px 6px; border-radius:6px; font-size:0.7rem; font-weight:800;">
                                                            <?= $cntAlpa ?> A
                                                        </span>
                                                    </div>
                                                    <div style="font-size:0.7rem; font-weight:700; color:#0ea5e9; display:flex; align-items:center; gap:3px;">
                                                        <i data-lucide="eye" style="width:11px; height:11px;"></i> Lihat Nama
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span style="font-style: italic; color: #64748b;"><?php echo htmlspecialchars($j['keterangan'] ?: '-'); ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php elseif($tab === 'nilai'): ?>
                <div class="filter-wrapper">
                    <form method="GET" action="<?php echo base_url('guru/kamad/monitoring/nilai'); ?>" id="nilaiFilterForm" style="display: flex; flex-wrap: wrap; gap: 1rem; width: 100%; align-items: center;">
                        <div class="filter-group">
                            <label style="font-size: 0.85rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Kelas:</label>
                            <select name="kelas_id" class="filter-select" onchange="document.getElementById('nilaiFilterForm').submit()">
                                <?php foreach($kelasList as $k): ?>
                                    <option value="<?php echo $k['id']; ?>" <?php echo $selected_kelas_id == $k['id'] ? 'selected' : ''; ?>>Kelas <?php echo htmlspecialchars($k['nama_kelas']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label style="font-size: 0.85rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Mata Pelajaran:</label>
                            <select name="mapel_id" class="filter-select" onchange="document.getElementById('nilaiFilterForm').submit()">
                                <?php foreach($mapelList as $m): ?>
                                    <option value="<?php echo $m['id']; ?>" <?php echo $selected_mapel_id == $m['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($m['nama_mapel']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>

                <div class="z-panel">
                    <div class="z-panel-head">
                        <div class="z-panel-title"><i data-lucide="file-spreadsheet"></i> Rekapitulasi Rapor Siswa</div>
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
                                    <tr><td colspan="8" align="center" style="padding: 40px; color: #94a3b8;">Tidak ada data nilai atau belum ada siswa terdaftar di rombel ini.</td></tr>
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
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Modal Detail Presensi Siswa -->
<div id="modalDetailAbsen" class="z-modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15,23,42,0.65); backdrop-filter:blur(5px); z-index:99999; justify-content:center; align-items:center;">
    <div style="background:white; border-radius:20px; width:92%; max-width:680px; max-height:85vh; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 25px 50px -12px rgba(0,0,0,0.3); border:1px solid #e2e8f0; animation: modalPop 0.25s cubic-bezier(0.16, 1, 0.3, 1);">
        <!-- Modal Header -->
        <div style="padding: 20px 24px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h3 id="modalTitle" style="margin:0; font-size:1.15rem; font-weight:800; display:flex; align-items:center; gap:8px;">
                    <i data-lucide="users" style="color:#38bdf8; width:20px; height:20px;"></i> Presensi Siswa Kelas
                </h3>
                <div id="modalSubtitle" style="font-size:0.83rem; color:#94a3b8; margin-top:4px;"></div>
            </div>
            <button onclick="closeAbsenModal()" style="background:rgba(255,255,255,0.12); border:none; color:white; border-radius:50%; width:32px; height:32px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:0.2s;">
                <i data-lucide="x" style="width:18px; height:18px;"></i>
            </button>
        </div>

        <!-- Modal Stats Bar -->
        <div style="padding:14px 24px; background:#f8fafc; border-bottom:1px solid #e2e8f0; display:grid; grid-template-columns:repeat(4, 1fr); gap:10px;">
            <div onclick="filterModalCategory('hadir')" id="statCardHadir" class="stat-card-btn" style="background:#dcfce7; border:1.5px solid #86efac; border-radius:12px; padding:10px; text-align:center; cursor:pointer; transition:0.2s;">
                <div style="font-size:0.7rem; font-weight:800; color:#15803d; text-transform:uppercase;">Hadir</div>
                <div id="statCountHadir" style="font-size:1.25rem; font-weight:850; color:#166534; margin-top:2px;">0</div>
            </div>
            <div onclick="filterModalCategory('izin')" id="statCardIzin" class="stat-card-btn" style="background:#dbeafe; border:1.5px solid #93c5fd; border-radius:12px; padding:10px; text-align:center; cursor:pointer; transition:0.2s;">
                <div style="font-size:0.7rem; font-weight:800; color:#1d4ed8; text-transform:uppercase;">Izin</div>
                <div id="statCountIzin" style="font-size:1.25rem; font-weight:850; color:#1e40af; margin-top:2px;">0</div>
            </div>
            <div onclick="filterModalCategory('sakit')" id="statCardSakit" class="stat-card-btn" style="background:#fef3c7; border:1.5px solid #fde047; border-radius:12px; padding:10px; text-align:center; cursor:pointer; transition:0.2s;">
                <div style="font-size:0.7rem; font-weight:800; color:#b45309; text-transform:uppercase;">Sakit</div>
                <div id="statCountSakit" style="font-size:1.25rem; font-weight:850; color:#854d0e; margin-top:2px;">0</div>
            </div>
            <div onclick="filterModalCategory('alpa')" id="statCardAlpa" class="stat-card-btn" style="background:#fee2e2; border:1.5px solid #fca5a5; border-radius:12px; padding:10px; text-align:center; cursor:pointer; transition:0.2s;">
                <div style="font-size:0.7rem; font-weight:800; color:#b91c1c; text-transform:uppercase;">Alpa</div>
                <div id="statCountAlpa" style="font-size:1.25rem; font-weight:850; color:#991b1b; margin-top:2px;">0</div>
            </div>
        </div>

        <!-- Filter Controls & Search -->
        <div style="padding:12px 24px; background:#fff; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <div style="display:flex; gap:6px; flex-wrap:wrap;" id="filterTabButtons">
                <button onclick="filterModalCategory('all')" id="btnTabAll" class="modal-tab-btn active">Semua (<span id="countAll">0</span>)</button>
                <button onclick="filterModalCategory('hadir')" id="btnTabHadir" class="modal-tab-btn">Hadir (<span id="countHadir">0</span>)</button>
                <button onclick="filterModalCategory('izin')" id="btnTabIzin" class="modal-tab-btn">Izin (<span id="countIzin">0</span>)</button>
                <button onclick="filterModalCategory('sakit')" id="btnTabSakit" class="modal-tab-btn">Sakit (<span id="countSakit">0</span>)</button>
                <button onclick="filterModalCategory('alpa')" id="btnTabAlpa" class="modal-tab-btn">Alpa (<span id="countAlpa">0</span>)</button>
            </div>
            <div style="position:relative; width:180px;">
                <input type="text" id="inputSearchSiswa" onkeyup="renderSiswaList()" placeholder="Cari nama..." style="width:100%; padding:6px 10px 6px 28px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.8rem; outline:none;">
                <i data-lucide="search" style="position:absolute; left:8px; top:50%; transform:translateY(-50%); width:14px; height:14px; color:#94a3b8;"></i>
            </div>
        </div>

        <!-- Student List Container -->
        <div style="padding:16px 24px; overflow-y:auto; flex:1; background:#f8fafc;">
            <div id="modalSiswaList" style="display:flex; flex-direction:column; gap:8px;"></div>
        </div>
    </div>
</div>

<style>
    .absen-card-btn:hover {
        border-color: #0ea5e9 !important;
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.15) !important;
        transform: translateY(-1px);
    }
    .stat-card-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }
    .modal-tab-btn {
        padding: 5px 12px;
        border-radius: 20px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .modal-tab-btn:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .modal-tab-btn.active {
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
    }
    @keyframes modalPop {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
</style>

<script>
lucide.createIcons();

let currentAbsenData = null;
let currentFilterCategory = 'all';

function openAbsenModal(detail, kelas, mapel, tanggal, guru) {
    currentAbsenData = detail;
    currentFilterCategory = 'all';
    
    document.getElementById('modalTitle').innerHTML = '<i data-lucide="users" style="color:#38bdf8; width:20px; height:20px;"></i> Detail Presensi - Kelas ' + kelas;
    document.getElementById('modalSubtitle').innerText = mapel + ' • ' + guru + ' • Tanggal: ' + tanggal;
    
    const hCount = detail.hadir ? detail.hadir.length : 0;
    const iCount = detail.izin ? detail.izin.length : 0;
    const sCount = detail.sakit ? detail.sakit.length : 0;
    const aCount = detail.alpa ? detail.alpa.length : 0;
    const totalCount = detail.total || (hCount + iCount + sCount + aCount);

    document.getElementById('statCountHadir').innerText = hCount;
    document.getElementById('statCountIzin').innerText = iCount;
    document.getElementById('statCountSakit').innerText = sCount;
    document.getElementById('statCountAlpa').innerText = aCount;

    document.getElementById('countAll').innerText = totalCount;
    document.getElementById('countHadir').innerText = hCount;
    document.getElementById('countIzin').innerText = iCount;
    document.getElementById('countSakit').innerText = sCount;
    document.getElementById('countAlpa').innerText = aCount;

    document.getElementById('inputSearchSiswa').value = '';
    
    filterModalCategory('all');

    const modal = document.getElementById('modalDetailAbsen');
    modal.style.display = 'flex';
    if (window.lucide) lucide.createIcons();
}

function closeAbsenModal() {
    document.getElementById('modalDetailAbsen').style.display = 'none';
}

function filterModalCategory(cat) {
    currentFilterCategory = cat;

    const btnIds = {
        'all': 'btnTabAll',
        'hadir': 'btnTabHadir',
        'izin': 'btnTabIzin',
        'sakit': 'btnTabSakit',
        'alpa': 'btnTabAlpa'
    };

    Object.keys(btnIds).forEach(k => {
        const el = document.getElementById(btnIds[k]);
        if (el) {
            if (k === cat) el.classList.add('active');
            else el.classList.remove('active');
        }
    });

    renderSiswaList();
}

function renderSiswaList() {
    if (!currentAbsenData) return;
    const listContainer = document.getElementById('modalSiswaList');
    const searchQuery = document.getElementById('inputSearchSiswa').value.toLowerCase().trim();

    let items = [];

    if (currentFilterCategory === 'all') {
        if (currentAbsenData.hadir) items = items.concat(currentAbsenData.hadir.map(x => ({...x, category: 'hadir'})));
        if (currentAbsenData.izin) items = items.concat(currentAbsenData.izin.map(x => ({...x, category: 'izin'})));
        if (currentAbsenData.sakit) items = items.concat(currentAbsenData.sakit.map(x => ({...x, category: 'sakit'})));
        if (currentAbsenData.alpa) items = items.concat(currentAbsenData.alpa.map(x => ({...x, category: 'alpa'})));
    } else {
        const catArr = currentAbsenData[currentFilterCategory] || [];
        items = catArr.map(x => ({...x, category: currentFilterCategory}));
    }

    if (searchQuery) {
        items = items.filter(i => i.nama.toLowerCase().includes(searchQuery));
    }

    if (items.length === 0) {
        listContainer.innerHTML = `
            <div style="text-align:center; padding:30px; background:white; border-radius:12px; border:1px dashed #cbd5e1; color:#94a3b8;">
                <i data-lucide="user-x" style="width:32px; height:32px; margin-bottom:6px; color:#cbd5e1;"></i>
                <div style="font-weight:700; color:#64748b;">Tidak ada data siswa</div>
                <div style="font-size:0.8rem; color:#94a3b8;">Tidak ada siswa pada kategori ini.</div>
            </div>
        `;
        if (window.lucide) lucide.createIcons();
        return;
    }

    const badgeConfig = {
        'hadir': { bg: '#dcfce7', color: '#15803d', border: '#86efac', label: 'Hadir', icon: 'check-circle' },
        'izin':  { bg: '#dbeafe', color: '#1d4ed8', border: '#93c5fd', label: 'Izin', icon: 'file-text' },
        'sakit': { bg: '#fef3c7', color: '#b45309', border: '#fde047', label: 'Sakit', icon: 'alert-circle' },
        'alpa':  { bg: '#fee2e2', color: '#b91c1c', border: '#fca5a5', label: 'Alpa', icon: 'x-circle' }
    };

    let html = '';
    items.forEach((item, index) => {
        const cfg = badgeConfig[item.category] || badgeConfig['alpa'];
        html += `
            <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:10px 14px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 1px 2px rgba(0,0,0,0.02);">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:28px; height:28px; border-radius:50%; background:#f1f5f9; color:#64748b; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:0.75rem;">
                        ${index + 1}
                    </div>
                    <div>
                        <div style="font-weight:700; color:#0f172a; font-size:0.9rem;">${escapeHtml(item.nama)}</div>
                    </div>
                </div>
                <span style="background:${cfg.bg}; color:${cfg.color}; border:1px solid ${cfg.border}; padding:4px 10px; border-radius:20px; font-size:0.75rem; font-weight:800; display:inline-flex; align-items:center; gap:4px;">
                    <i data-lucide="${cfg.icon}" style="width:12px; height:12px;"></i> ${cfg.label}
                </span>
            </div>
        `;
    });

    listContainer.innerHTML = html;
    if (window.lucide) lucide.createIcons();
}

function escapeHtml(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

document.addEventListener('click', function(e) {
    const modal = document.getElementById('modalDetailAbsen');
    if (e.target === modal) {
        closeAbsenModal();
    }
});
</script>
</body>
</html>


