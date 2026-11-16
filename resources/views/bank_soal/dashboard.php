<?php
$page_title = $selectedTopik ? ("Bank Soal: " . $selectedTopik['nama_topik']) : "Dashboard Bank Soal";
$active_menu = "bank_soal";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - MAS MIFTAHUL HUDA</title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>?v=<?php echo time(); ?>">
    <link href="<?php echo base_url('public/assets/css/zamrud.css'); ?>" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .stats-grid-bs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }
        .stat-card-bs {
            background: #ffffff;
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card-bs:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        }
        .stat-icon-bs {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .stat-icon-bs.blue { background: #e0f2fe; color: #0284c7; }
        .stat-icon-bs.green { background: #dcfce7; color: #16a34a; }
        .stat-icon-bs.purple { background: #f3e8ff; color: #9333ea; }
        .stat-icon-bs.amber { background: #fef3c7; color: #d97706; }
        .stat-val-bs { font-size: 1.7rem; font-weight: 800; color: #0f172a; line-height: 1.2; }
        .stat-label-bs { font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Topik Cards Grid */
        .topik-grid-bs {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .topik-card-bs {
            background: #ffffff;
            border-radius: 18px;
            border: 1.5px solid #e2e8f0;
            padding: 1.5rem;
            box-shadow: 0 6px 20px rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }
        .topik-card-bs:hover {
            transform: translateY(-4px);
            border-color: #0ea5e9;
            box-shadow: 0 12px 30px rgba(14, 165, 233, 0.12);
        }
        .topik-card-bs.active-topik {
            border-color: #0ea5e9;
            background: linear-gradient(180deg, #f0f9ff 0%, #ffffff 100%);
        }
        .topik-badge-jenis {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-sas { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .badge-sts { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
        .badge-um { background: #fdf4ff; color: #c026d3; border: 1px solid #f5d0fe; }
        .badge-to { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }

        /* Detail Pills Jenis Soal */
        .pill-jenis {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 700;
            margin-right: 4px;
            margin-bottom: 4px;
        }
        .pill-pg { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .pill-pgk { background: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff; }
        .pill-essay { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .pill-bs { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .pill-jodoh { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

        /* Modal overlay */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.open { display: flex; }
    </style>
</head>
<body class="z-bg">

<div class="z-wrap">
    <?php include __DIR__ . '/_sidebar.php'; ?>

    <main class="z-main">
        <?php 
        $page_badge = 'Bank Soal';
        include __DIR__ . '/../siakad/_siakad_header.php'; 
        ?>

        <div class="z-content" style="padding: 1.5rem 2rem 3rem;">

            <!-- Alerts Notifikasi -->
            <?php if(isset($_GET['msg'])): ?>
                <?php if($_GET['msg'] === 'locked'): ?>
                    <div style="background: #fef3c7; border: 1px solid #fde68a; color: #92400e; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                        <i data-lucide="lock" style="width: 20px; height: 20px; color: #d97706;"></i> Soal berhasil dikunci! Guru di APK maupun web tidak dapat lagi mengedit butir soal.
                    </div>
                <?php elseif($_GET['msg'] === 'unlocked'): ?>
                    <div style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                        <i data-lucide="unlock" style="width: 20px; height: 20px; color: #22c55e;"></i> Kunci soal berhasil dibuka! Guru dapat kembali mengedit dan menyempurnakan butir soal.
                    </div>
                <?php elseif($_GET['msg'] === 'paket_saved'): ?>
                    <div style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                        <i data-lucide="check-circle" style="width: 20px; height: 20px; color: #22c55e;"></i> Paket mata pelajaran baru berhasil ditambahkan ke Bank Soal!
                    </div>
                <?php elseif($_GET['msg'] === 'paket_deleted'): ?>
                    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                        <i data-lucide="trash-2" style="width: 20px; height: 20px; color: #ef4444;"></i> Paket mata pelajaran beserta seluruh butir soalnya berhasil dihapus.
                    </div>
                <?php elseif($_GET['msg'] === 'dummy_created'): ?>
                    <div style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                        <i data-lucide="check-circle" style="width: 20px; height: 20px; color: #22c55e;"></i> Contoh paket Matematika 50 soal (40 PG, 5 PG Kompleks, 5 Essay) berhasil dibuat lengkap dengan kunci jawaban!
                    </div>
                <?php elseif($_GET['msg'] === 'bank_reset' || $_GET['msg'] === 'bank_reset_all'): ?>
                    <div style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                        <i data-lucide="sparkles" style="width: 20px; height: 20px; color: #22c55e;"></i> Data Bank Soal berhasil dikosongkan dan direset bersih!
                    </div>
                <?php elseif($_GET['msg'] === 'error_locked'): ?>
                    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                        <i data-lucide="alert-triangle" style="width: 20px; height: 20px; color: #ef4444;"></i> Paket soal ini sedang terkunci! Buka kunci terlebih dahulu jika ingin mengedit atau menghapus.
                    </div>
                <?php elseif($_GET['msg'] === 'synced'): ?>
                    <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                        <i data-lucide="refresh-cw" style="width: 20px; height: 20px; color: #3b82f6;"></i> Berhasil menyinkronkan <?php echo (int)($_GET['count'] ?? 0); ?> mata pelajaran dari Jadwal CBT!
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (!$selectedTopik): ?>
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- STATE 1: DASHBOARD UTAMA BANK SOAL (CARD TOPIK UJIAN)            -->
            <!-- ═══════════════════════════════════════════════════════════════ -->

            <!-- Hero Banner -->
            <div style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); padding: 2rem 2.5rem; border-radius: 20px; margin-bottom: 2rem; color: #ffffff; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(2, 132, 199, 0.2);">
                <div style="position: relative; z-index: 1; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
                    <div>
                        <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.18); padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; letter-spacing: 0.5px; margin-bottom: 8px;">
                            <i data-lucide="database" style="width: 14px; height: 14px;"></i> REPOSITORI BANK SOAL TERPADU
                        </div>
                        <h1 style="font-size: 1.85rem; font-weight: 900; margin: 0 0 0.5rem 0; letter-spacing: -0.5px;">Bank Soal Madrasah</h1>
                        <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.92rem; max-width: 650px;">
                            Pilih salah satu Topik Ujian di bawah untuk mengelola mata pelajaran, detail butir soal (PG, PG Kompleks, Essay), serta status kunci soal guru.
                        </p>
                    </div>

                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button type="button" onclick="openModalTambahTopik()" class="btn btn-primary" style="background: #ffffff; color: #0369a1; font-weight: 800; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                            <i data-lucide="plus"></i> Tambah Topik Baru
                        </button>
                        <a href="<?php echo base_url('cbt/jadwal'); ?>" class="btn btn-outline" style="background: rgba(255,255,255,0.15); color: #ffffff; border-color: rgba(255,255,255,0.3); font-weight: 700;">
                            <i data-lucide="calendar"></i> Master Jadwal CBT
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Real (Bukan Hardcoded!) -->
            <div class="stats-grid-bs">
                <div class="stat-card-bs">
                    <div class="stat-icon-bs blue"><i data-lucide="layers"></i></div>
                    <div>
                        <div class="stat-val-bs"><?php echo $totalTopik; ?></div>
                        <div class="stat-label-bs">Total Topik Ujian</div>
                    </div>
                </div>
                <div class="stat-card-bs">
                    <div class="stat-icon-bs green"><i data-lucide="book-open"></i></div>
                    <div>
                        <div class="stat-val-bs"><?php echo $totalPaket; ?></div>
                        <div class="stat-label-bs">Paket Mapel Terdaftar</div>
                    </div>
                </div>
                <div class="stat-card-bs">
                    <div class="stat-icon-bs purple"><i data-lucide="file-question"></i></div>
                    <div>
                        <div class="stat-val-bs"><?php echo $totalSoal; ?></div>
                        <div class="stat-label-bs">Total Butir Soal</div>
                    </div>
                </div>
                <div class="stat-card-bs">
                    <div class="stat-icon-bs amber"><i data-lucide="lock"></i></div>
                    <div>
                        <div class="stat-val-bs"><?php echo $totalLocked; ?></div>
                        <div class="stat-label-bs">Paket Soal Terkunci</div>
                    </div>
                </div>
            </div>

            <!-- Header Grid Topik Ujian -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 10px;">
                <div>
                    <h2 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="folder-check" style="color: #0ea5e9; width: 22px; height: 22px;"></i>
                        Pilih Topik Ujian
                    </h2>
                    <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px;">
                        Klik pada kartu topik untuk melihat daftar mapel, rincian jenis butir soal, dan mengelola bank soal.
                    </div>
                </div>

                <!-- Tombol Kosongkan / Reset Global -->
                <button type="button" onclick="confirmResetAll()" style="background: none; border: none; color: #ef4444; font-size: 0.78rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i> Kosongkan / Reset Semua Soal
                </button>
            </div>

            <!-- Grid Card Topik Ujian -->
            <?php if (empty($allTopikList)): ?>
                <div style="background: #ffffff; border-radius: 16px; border: 1.5px dashed #cbd5e1; padding: 4rem 2rem; text-align: center; color: #64748b;">
                    <i data-lucide="layers" style="width: 48px; height: 48px; color: #94a3b8; margin-bottom: 12px;"></i>
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: #1e293b; margin: 0 0 6px 0;">Belum Ada Topik Ujian</h3>
                    <p style="font-size: 0.85rem; margin: 0 0 1.25rem 0;">Buat topik ujian baru seperti SAS, STS, atau Ujian Madrasah untuk mulai mengisi bank soal.</p>
                    <button type="button" onclick="openModalTambahTopik()" class="btn btn-primary">
                        <i data-lucide="plus"></i> Tambah Topik Sekarang
                    </button>
                </div>
            <?php else: ?>
                <div class="topik-grid-bs">
                    <?php foreach($allTopikList as $tp): 
                        $jenis = strtoupper($tp['jenis'] ?: 'SAS');
                        $bClass = 'badge-sas';
                        if ($jenis === 'STS' || $jenis === 'PTS') $bClass = 'badge-sts';
                        elseif ($jenis === 'UM') $bClass = 'badge-um';
                        elseif ($jenis === 'TRY OUT' || $jenis === 'TO') $bClass = 'badge-to';

                        $st = $tp['status'] ?: 'Terjadwal';
                        $stBg = ($st === 'Aktif') ? '#dcfce7' : (($st === 'Selesai') ? '#f1f5f9' : '#e0f2fe');
                        $stCol = ($st === 'Aktif') ? '#15803d' : (($st === 'Selesai') ? '#64748b' : '#0369a1');

                        $tMulai = !empty($tp['tgl_mulai']) ? date('d M Y', strtotime($tp['tgl_mulai'])) : null;
                        $tSelesai = !empty($tp['tgl_selesai']) ? date('d M Y', strtotime($tp['tgl_selesai'])) : null;
                    ?>
                    <div class="topik-card-bs <?php echo ($st === 'Aktif') ? 'active-topik' : ''; ?>">
                        <div>
                            <!-- Header Card: Jenis & Status -->
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <span class="topik-badge-jenis <?php echo $bClass; ?>">
                                    <?php echo htmlspecialchars($jenis); ?> &bull; <?php echo htmlspecialchars($tp['semester'] ?? 'Ganjil'); ?>
                                </span>
                                <span style="font-size: 0.72rem; font-weight: 800; padding: 3px 9px; border-radius: 20px; background: <?php echo $stBg; ?>; color: <?php echo $stCol; ?>;">
                                    <?php echo htmlspecialchars($st); ?>
                                </span>
                            </div>

                            <!-- Title Topik -->
                            <h3 style="margin: 0 0 8px 0; font-size: 1.15rem; font-weight: 800; color: #0f172a; line-height: 1.35;">
                                <?php echo htmlspecialchars($tp['nama_topik']); ?>
                            </h3>

                            <!-- Tanggal Pelaksanaan -->
                            <div style="display: flex; align-items: center; gap: 6px; font-size: 0.8rem; color: #475569; font-weight: 600; margin-bottom: 14px;">
                                <i data-lucide="calendar" style="width: 14px; height: 14px; color: #0ea5e9;"></i>
                                <?php if ($tMulai): ?>
                                    <span><?php echo $tMulai; ?> <?php echo $tSelesai ? 's/d ' . $tSelesai : ''; ?></span>
                                <?php else: ?>
                                    <span style="color: #94a3b8; font-style: italic;">Tanggal belum diset</span>
                                <?php endif; ?>
                            </div>

                            <!-- Stat Summary Card -->
                            <div style="background: #f8fafc; border-radius: 12px; padding: 10px 14px; margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: center; border: 1px solid #e2e8f0;">
                                <div>
                                    <div style="font-size: 1.1rem; font-weight: 800; color: #0f172a;"><?php echo $tp['jml_paket']; ?></div>
                                    <div style="font-size: 0.68rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Paket Mapel</div>
                                </div>
                                <div style="text-align: center;">
                                    <div style="font-size: 1.1rem; font-weight: 800; color: #0284c7;"><?php echo $tp['jml_soal']; ?></div>
                                    <div style="font-size: 0.68rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Total Soal</div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 1.1rem; font-weight: 800; color: <?php echo ($tp['jml_locked'] > 0) ? '#d97706' : '#16a34a'; ?>;">
                                        <?php echo $tp['jml_locked']; ?> / <?php echo $tp['jml_paket']; ?>
                                    </div>
                                    <div style="font-size: 0.68rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Terkunci</div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Buka Bank Soal Topik -->
                        <a href="?topik_id=<?php echo $tp['id']; ?>" class="btn btn-primary" style="width: 100%; justify-content: center; font-weight: 800; font-size: 0.88rem; background: linear-gradient(135deg, #0ea5e9, #0284c7); border: none; padding: 10px 16px; border-radius: 10px; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.25);">
                            <i data-lucide="folder-open"></i> Buka Bank Soal Topik &rarr;
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php else: ?>
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- STATE 2: DETAIL PAKET MAPEL & BUTIR SOAL TOPIK TERPILIH          -->
            <!-- ═══════════════════════════════════════════════════════════════ -->

            <!-- Breadcrumb Navigation -->
            <div style="display: flex; align-items: center; gap: 8px; font-size: 0.85rem; font-weight: 700; margin-bottom: 1.25rem;">
                <a href="<?php echo base_url('bank_soal'); ?>" style="color: #64748b; text-decoration: none; display: flex; align-items: center; gap: 5px;">
                    <i data-lucide="arrow-left" style="width: 15px; height: 15px;"></i> Kembali ke Semua Topik
                </a>
                <span style="color: #cbd5e1;">/</span>
                <span style="color: #0ea5e9;"><?php echo htmlspecialchars($selectedTopik['nama_topik']); ?></span>
            </div>

            <!-- Topik Info Banner Card -->
            <div style="background: #ffffff; border-radius: 20px; border: 1.5px solid #e0f2fe; padding: 1.75rem 2rem; margin-bottom: 2rem; box-shadow: 0 6px 25px rgba(14, 165, 233, 0.08); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.25rem;">
                <div style="display: flex; align-items: center; gap: 18px; flex: 1; min-width: 320px;">
                    <div style="width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, #0ea5e9, #0284c7); color: #fff; display: flex; align-items: center; justify-content: center; box-shadow: 0 6px 16px rgba(14, 165, 233, 0.3); flex-shrink: 0;">
                        <i data-lucide="layers" style="width: 28px; height: 28px;"></i>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px; flex-wrap: wrap;">
                            <span class="topik-badge-jenis badge-sas">
                                [<?php echo htmlspecialchars($selectedTopik['jenis'] ?: 'SAS'); ?>]
                            </span>
                            <span style="font-size: 0.75rem; font-weight: 800; background: #ecfdf5; color: #059669; padding: 3px 10px; border-radius: 20px; border: 1px solid #a7f3d0;">
                                <?php echo htmlspecialchars($selectedTopik['status'] ?: 'Aktif'); ?>
                            </span>
                            <span style="font-size: 0.75rem; font-weight: 700; color: #64748b;">
                                <?php echo htmlspecialchars($selectedTopik['semester'] ?? 'Ganjil'); ?> <?php echo htmlspecialchars($selectedTopik['tahun_ajaran'] ?: '2026/2027'); ?>
                            </span>
                        </div>
                        <h2 style="margin: 0 0 6px 0; font-size: 1.35rem; font-weight: 900; color: #0f172a;">
                            <?php echo htmlspecialchars($selectedTopik['nama_topik']); ?>
                        </h2>
                        <div style="display: flex; align-items: center; gap: 6px; font-size: 0.82rem; color: #475569; font-weight: 600;">
                            <i data-lucide="calendar" style="width: 14px; height: 14px; color: #0ea5e9;"></i>
                            <?php if (!empty($selectedTopik['tgl_mulai'])): ?>
                                Mulai: <strong><?php echo date('d M Y', strtotime($selectedTopik['tgl_mulai'])); ?></strong>
                                <?php echo !empty($selectedTopik['tgl_selesai']) ? 's/d <strong>' . date('d M Y', strtotime($selectedTopik['tgl_selesai'])) . '</strong>' : ''; ?>
                            <?php else: ?>
                                <span style="color: #94a3b8; font-style: italic;">Periode tanggal belum ditentukan</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Action Toolbar for Topic -->
                <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                    <button type="button" onclick="openModalTambahPaket()" class="btn btn-primary" style="font-weight: 800; background: #0ea5e9; border: none; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);">
                        <i data-lucide="plus-circle"></i> Tambah Paket Mapel
                    </button>

                    <form method="POST" action="<?php echo base_url('bank_soal/paket/sync-pengampu'); ?>" style="display: inline;">
                        <input type="hidden" name="topik_id" value="<?php echo $selectedTopik['id']; ?>">
                        <button type="submit" class="btn btn-outline" style="border-color: #0284c7; color: #0284c7; background: #f0f9ff; font-weight: 800;" title="Otomatis muat daftar mapel madrasah beserta guru pengampunya tanpa perlu nunggu jadwal CBT">
                            <i data-lucide="users" style="width: 15px; height: 15px; color: #0284c7;"></i> Muat Mapel Pengampu
                        </button>
                    </form>

                    <form method="POST" action="<?php echo base_url('bank_soal/paket/sync-jadwal'); ?>" style="display: inline;">
                        <input type="hidden" name="topik_id" value="<?php echo $selectedTopik['id']; ?>">
                        <button type="submit" class="btn btn-outline" style="border-color: #cbd5e1; color: #1e293b; font-weight: 700;" title="Otomatis buatkan paket mapel dari jadwal CBT yang sudah disetting">
                            <i data-lucide="calendar" style="width: 15px; height: 15px; color: #64748b;"></i> Ambil dari Jadwal CBT
                        </button>
                    </form>

                    <form method="POST" action="<?php echo base_url('bank_soal/generate-dummy'); ?>" style="display: inline;" onsubmit="return confirm('Buat contoh paket Matematika 50 Soal (40 PG, 5 PG Kompleks, 5 Essay)?')">
                        <input type="hidden" name="topik_id" value="<?php echo $selectedTopik['id']; ?>">
                        <button type="submit" class="btn btn-outline" style="background: #fdf4ff; border-color: #f5d0fe; color: #9333ea; font-weight: 800;" title="Contoh data dummy seperti permintaan user">
                            <i data-lucide="sparkles" style="width: 15px; height: 15px; color: #c026d3;"></i> Buat Contoh Dummy (Matematika 50 Soal)
                        </button>
                    </form>

                    <form method="POST" action="<?php echo base_url('bank_soal/reset-data'); ?>" style="display: inline;" onsubmit="return confirm('Kosongkan semua paket dan butir soal pada topik ini?')">
                        <input type="hidden" name="topik_id" value="<?php echo $selectedTopik['id']; ?>">
                        <button type="submit" class="btn btn-outline" style="background: #fef2f2; border-color: #fecaca; color: #ef4444; font-weight: 700;" title="Kosongkan bank soal topik ini">
                            <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tabel Daftar Paket Mapel & Kelas -->
            <?php
            $uniqueClasses = [];
            foreach ($paketList as $p) {
                $cName = trim($p['kelas_nama'] ?? '');
                if ($cName && !in_array($cName, $uniqueClasses)) {
                    $uniqueClasses[] = $cName;
                }
            }
            sort($uniqueClasses);
            ?>
            <div class="z-card" style="padding: 0; border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden; background: #ffffff; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
                <div style="padding: 1.25rem 1.75rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <h3 style="margin: 0; font-size: 1rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                            <i data-lucide="book-copy" style="color: #0ea5e9; width: 18px; height: 18px;"></i>
                            Daftar Mata Pelajaran & Status Kunci Soal
                        </h3>
                        <div style="font-size: 0.78rem; color: #64748b; margin-top: 2px;">
                            Paket soal tersusun per kelas sesuai guru pengampu riil di SIAKAD. Soal yang berstatus <strong>Terkunci</strong> tidak dapat diedit lagi oleh guru.
                        </div>
                    </div>
                    <div style="font-size: 0.8rem; font-weight: 800; color: #0284c7; background: #e0f2fe; padding: 4px 12px; border-radius: 20px;">
                        Total: <span id="totalVisibleCount"><?php echo count($paketList); ?></span> / <?php echo count($paketList); ?> Paket Mapel
                    </div>
                </div>

                <!-- Filter Bar: Tab Kelas & Live Search -->
                <div style="padding: 0.85rem 1.75rem; background: #ffffff; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <!-- Filter Kelas Tabs -->
                    <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;" id="kelasFilterTabs">
                        <span style="font-size: 0.78rem; font-weight: 800; color: #64748b; margin-right: 4px; display: flex; align-items: center; gap: 4px;">
                            <i data-lucide="filter" style="width: 14px; height: 14px;"></i> Filter Kelas:
                        </span>
                        <button type="button" class="btn-filter-kelas active" data-filter="all" onclick="setKelasFilter('all', this)" style="padding: 5px 12px; border-radius: 8px; font-size: 0.78rem; font-weight: 800; border: 1.5px solid #0ea5e9; background: #0ea5e9; color: #ffffff; cursor: pointer; transition: all 0.2s;">
                            Semua (<?php echo count($paketList); ?>)
                        </button>
                        <?php foreach ($uniqueClasses as $uc): 
                            $countKls = count(array_filter($paketList, fn($p) => trim($p['kelas_nama']) === $uc));
                        ?>
                        <button type="button" class="btn-filter-kelas" data-filter="<?php echo htmlspecialchars($uc); ?>" onclick="setKelasFilter('<?php echo htmlspecialchars($uc); ?>', this)" style="padding: 5px 12px; border-radius: 8px; font-size: 0.78rem; font-weight: 800; border: 1.5px solid #cbd5e1; background: #f8fafc; color: #334155; cursor: pointer; transition: all 0.2s;">
                            <?php echo htmlspecialchars($uc); ?> (<?php echo $countKls; ?>)
                        </button>
                        <?php endforeach; ?>
                    </div>

                    <!-- Live Search Input -->
                    <div style="position: relative; min-width: 240px; flex: 1; max-width: 300px;">
                        <i data-lucide="search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: #94a3b8;"></i>
                        <input type="text" id="searchMapelSoal" onkeyup="filterBankSoalTable()" placeholder="Cari mapel atau nama guru..." style="width: 100%; padding: 6px 12px 6px 32px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 0.8rem; outline: none; transition: border-color 0.2s;">
                    </div>
                </div>

                <div class="z-table-wrap">
                    <table class="z-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">No</th>
                                <th>Mata Pelajaran & Guru Pengampu</th>
                                <th style="width: 140px;">Kelas Sasaran</th>
                                <th style="width: 110px; text-align: center;">Total Soal</th>
                                <th>Rincian Jenis Soal</th>
                                <th style="width: 160px; text-align: center;">Status Kunci</th>
                                <th style="width: 220px; text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="bankSoalTableBody">
                            <?php if (empty($paketList)): ?>
                            <tr>
                                <td colspan="7" style="padding: 4rem 1.5rem; text-align: center; color: #94a3b8;">
                                    <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 10px; color: #cbd5e1;"></i>
                                    <div style="font-size: 1rem; font-weight: 800; color: #475569;">Belum ada paket mapel di topik ini</div>
                                    <div style="font-size: 0.82rem; margin-top: 4px;">Gunakan tombol <strong>"Muat Mapel Pengampu"</strong> di atas untuk otomatis menyusun paket soal per kelas dari data mengajar madrasah.</div>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach($paketList as $pk): 
                                    $isLocked = !empty($pk['is_locked']);
                                    $bd = $pk['breakdown'] ?? [];
                                    $tot = $pk['total_soal'] ?? 0;
                                    $kls = trim($pk['kelas_nama'] ?? '');
                                    
                                    // Warna Badge per Kelas
                                    $klsBg = '#e0e7ff';
                                    $klsCol = '#4338ca';
                                    $klsBorder = '#c7d2fe';
                                    if (strpos($kls, 'X E') !== false || $kls === 'X') {
                                        $klsBg = '#eff6ff'; $klsCol = '#1d4ed8'; $klsBorder = '#bfdbfe';
                                    } elseif (strpos($kls, 'XI F') !== false || $kls === 'XI') {
                                        $klsBg = '#ecfdf5'; $klsCol = '#047857'; $klsBorder = '#a7f3d0';
                                    } elseif (strpos($kls, 'XII F.1') !== false) {
                                        $klsBg = '#f5f3ff'; $klsCol = '#6d28d9'; $klsBorder = '#ddd6fe';
                                    } elseif (strpos($kls, 'XII F.2') !== false) {
                                        $klsBg = '#fffbeb'; $klsCol = '#b45309'; $klsBorder = '#fde68a';
                                    }
                                ?>
                                <tr class="row-paket-mapel" data-kelas="<?php echo htmlspecialchars($kls); ?>" data-search="<?php echo htmlspecialchars(strtolower(($pk['mapel_nama'] ?? '') . ' ' . ($pk['guru_nama'] ?? '') . ' ' . $kls)); ?>">
                                    <td class="row-number-cell" style="text-align: center; color: #94a3b8; font-weight: 700;"><?php echo $no++; ?></td>
                                    <td>
                                        <div style="font-weight: 900; color: #0f172a; font-size: 0.95rem;">
                                            <?php echo htmlspecialchars($pk['mapel_nama']); ?>
                                        </div>
                                        <div style="font-size: 0.75rem; color: #475569; display: flex; align-items: center; gap: 4px; margin-top: 3px;">
                                            <span style="display: inline-flex; align-items: center; gap: 5px; background: #f8fafc; padding: 2px 8px; border-radius: 6px; font-weight: 700; color: #334155; border: 1px solid #e2e8f0;">
                                                <i data-lucide="user-check" style="width: 12px; height: 12px; color: #059669;"></i>
                                                <span>Pengampu: <strong><?php echo htmlspecialchars($pk['guru_nama'] ?: 'Belum Ada Pengampu'); ?></strong></span>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.8rem; font-weight: 800; background: <?php echo $klsBg; ?>; color: <?php echo $klsCol; ?>; border: 1px solid <?php echo $klsBorder; ?>; padding: 4px 10px; border-radius: 8px; display: inline-flex; align-items: center; gap: 5px;">
                                            <i data-lucide="graduation-cap" style="width: 13px; height: 13px;"></i>
                                            <?php echo htmlspecialchars($kls ?: 'X E.1'); ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="font-size: 1.15rem; font-weight: 900; color: <?php echo ($tot > 0) ? '#0f172a' : '#94a3b8'; ?>;">
                                            <?php echo $tot; ?>
                                        </div>
                                        <div style="font-size: 0.65rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Butir Soal</div>
                                    </td>
                                    <td>
                                        <!-- Detail Jenis Soal (Sesuai Permintaan User) -->
                                        <?php if ($tot === 0): ?>
                                            <span style="font-size: 0.75rem; color: #94a3b8; font-style: italic;">
                                                Belum ada butir soal diinput
                                            </span>
                                        <?php else: ?>
                                            <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                                <?php if (!empty($bd['sec_pg'])): ?>
                                                    <span class="pill-jenis pill-pg" title="Pilihan Ganda">
                                                        <i data-lucide="check-circle-2" style="width: 11px; height: 11px;"></i>
                                                        <strong><?php echo $bd['sec_pg']; ?></strong> PG
                                                    </span>
                                                <?php endif; ?>

                                                <?php if (!empty($bd['sec_pgk'])): ?>
                                                    <span class="pill-jenis pill-pgk" title="Pilihan Ganda Kompleks (Multi-Jawaban)">
                                                        <i data-lucide="check-square" style="width: 11px; height: 11px;"></i>
                                                        <strong><?php echo $bd['sec_pgk']; ?></strong> PG Kompleks
                                                    </span>
                                                <?php endif; ?>

                                                <?php if (!empty($bd['sec_essay'])): ?>
                                                    <span class="pill-jenis pill-essay" title="Essay / Uraian">
                                                        <i data-lucide="file-text" style="width: 11px; height: 11px;"></i>
                                                        <strong><?php echo $bd['sec_essay']; ?></strong> Essay
                                                    </span>
                                                <?php endif; ?>

                                                <?php if (!empty($bd['sec_bs'])): ?>
                                                    <span class="pill-jenis pill-bs" title="Benar / Salah">
                                                        <strong><?php echo $bd['sec_bs']; ?></strong> B-S
                                                    </span>
                                                <?php endif; ?>

                                                <?php if (!empty($bd['sec_jodoh'])): ?>
                                                    <span class="pill-jenis pill-jodoh" title="Menjodohkan">
                                                        <strong><?php echo $bd['sec_jodoh']; ?></strong> Jodohkan
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <!-- Status Kunci -->
                                        <?php if ($isLocked): ?>
                                            <div style="display: inline-flex; flex-direction: column; align-items: center; gap: 2px;">
                                                <span style="font-size: 0.75rem; font-weight: 800; background: #fef3c7; color: #b45309; padding: 4px 10px; border-radius: 20px; border: 1px solid #fde68a; display: inline-flex; align-items: center; gap: 4px;">
                                                    <i data-lucide="lock" style="width: 12px; height: 12px;"></i> Terkunci (Final)
                                                </span>
                                                <?php if (!empty($pk['locked_at'])): ?>
                                                    <span style="font-size: 0.65rem; color: #94a3b8; font-weight: 600;">
                                                        <?php echo date('d/m/Y H:i', strtotime($pk['locked_at'])); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span style="font-size: 0.75rem; font-weight: 800; background: #ecfdf5; color: #047857; padding: 4px 10px; border-radius: 20px; border: 1px solid #a7f3d0; display: inline-flex; align-items: center; gap: 4px;">
                                                <i data-lucide="unlock" style="width: 12px; height: 12px;"></i> Terbuka
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <div style="display: inline-flex; gap: 6px; align-items: center;">
                                            <!-- Tombol Kunci / Buka Kunci -->
                                            <form method="POST" action="<?php echo base_url('bank_soal/paket/toggle-lock'); ?>" style="display: inline;" onsubmit="return confirm('<?php echo $isLocked ? 'Buka kunci soal ini agar guru dapat mengedit kembali?' : 'Kunci soal ini? Setelah dikunci, guru tidak dapat mengedit lagi butir soal.'; ?>')">
                                                <input type="hidden" name="paket_id" value="<?php echo $pk['id']; ?>">
                                                <input type="hidden" name="topik_id" value="<?php echo $selectedTopik['id']; ?>">
                                                <?php if ($isLocked): ?>
                                                    <button type="submit" class="btn btn-outline" style="padding: 6px 10px; font-size: 0.75rem; font-weight: 700; color: #475569; border-color: #cbd5e1;" title="Buka Kunci Soal">
                                                        <i data-lucide="unlock" style="width: 13px; height: 13px; color: #22c55e;"></i> Buka Kunci
                                                    </button>
                                                <?php else: ?>
                                                    <button type="submit" class="btn btn-outline" style="padding: 6px 10px; font-size: 0.75rem; font-weight: 700; background: #fffbeb; color: #b45309; border-color: #fde68a;" title="Kunci Soal (Cegah Edit Guru)">
                                                        <i data-lucide="lock" style="width: 13px; height: 13px; color: #d97706;"></i> Kunci Soal
                                                    </button>
                                                <?php endif; ?>
                                            </form>

                                            <!-- Tombol Kelola / Lihat Soal -->
                                            <?php if ($isLocked): ?>
                                                <a href="<?php echo base_url('bank_soal/daftar_soal?paket_id=' . $pk['id']); ?>" class="btn btn-outline" style="padding: 6px 12px; font-size: 0.75rem; font-weight: 800; background: #f0fdf4; color: #166534; border-color: #bbf7d0;" title="Lihat Butir Soal (Mode Baca Saja)">
                                                    <i data-lucide="eye" style="width: 13px; height: 13px;"></i> Lihat Soal
                                                </a>
                                            <?php else: ?>
                                                <a href="<?php echo base_url('bank_soal/daftar_soal?paket_id=' . $pk['id']); ?>" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.75rem; font-weight: 800; background: #0ea5e9; border: none;" title="Kelola / Input Butir Soal & Kunci">
                                                    <i data-lucide="edit-3" style="width: 13px; height: 13px;"></i> Kelola Soal
                                                </a>
                                            <?php endif; ?>

                                            <!-- Hapus Paket (hanya jika belum terkunci) -->
                                            <?php if (!$isLocked): ?>
                                                <form method="POST" action="<?php echo base_url('bank_soal/paket/hapus'); ?>" style="display: inline;" onsubmit="return confirm('Hapus paket mapel ini beserta seluruh butir soalnya?')">
                                                    <input type="hidden" name="paket_id" value="<?php echo $pk['id']; ?>">
                                                    <input type="hidden" name="topik_id" value="<?php echo $selectedTopik['id']; ?>">
                                                    <button type="submit" style="background: none; border: none; cursor: pointer; color: #94a3b8; padding: 4px;" title="Hapus Paket">
                                                        <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <tr id="rowEmptyFilter" style="display: none;">
                                    <td colspan="7" style="padding: 3.5rem 1.5rem; text-align: center; color: #94a3b8;">
                                        <i data-lucide="search-x" style="width: 36px; height: 36px; margin-bottom: 8px; color: #cbd5e1;"></i>
                                        <div style="font-size: 0.95rem; font-weight: 800; color: #475569;">Tidak ada mata pelajaran yang cocok</div>
                                        <div style="font-size: 0.8rem; margin-top: 4px;">Coba pilih filter kelas lain atau hapus kata kunci pencarian.</div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php endif; ?>

        </div>
    </main>
</div>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- MODAL: TAMBAH PAKET MAPEL BARU                                  -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<div id="modalTambahPaket" class="modal-overlay" onclick="if(event.target === this) this.classList.remove('open')">
    <div style="background: #ffffff; width: 90%; max-width: 520px; border-radius: 20px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.18); border: 1px solid #e2e8f0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="book-plus" style="width: 22px; height: 22px; color: #0ea5e9;"></i>
                Tambah Paket Mapel Baru
            </h3>
            <button type="button" onclick="document.getElementById('modalTambahPaket').classList.remove('open')" style="background: none; border: none; cursor: pointer; color: #94a3b8;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>

        <form method="POST" action="<?php echo base_url('bank_soal/paket/simpan'); ?>">
            <input type="hidden" name="topik_id" value="<?php echo $selectedTopik['id'] ?? 1; ?>">

            <div style="margin-bottom: 1rem;">
                <label class="label-sm" style="display: block; font-weight: 700; font-size: 0.8rem; margin-bottom: 4px;">Mata Pelajaran <span style="color:#ef4444;">*</span></label>
                <select name="mapel_nama" class="z-input" required style="width: 100%;">
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    <?php foreach($mapelList as $mp): ?>
                        <option value="<?php echo htmlspecialchars($mp['nama_mapel']); ?>">
                            <?php echo htmlspecialchars($mp['nama_mapel']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 1rem;">
                <div>
                    <label class="label-sm" style="display: block; font-weight: 700; font-size: 0.8rem; margin-bottom: 4px;">Kelas Sasaran <span style="color:#ef4444;">*</span></label>
                    <select name="kelas_nama" class="z-input" required style="width: 100%;">
                        <option value="">-- Pilih Kelas Sasaran --</option>
                        <?php foreach($kelasList as $k): ?>
                            <option value="<?php echo htmlspecialchars($k['nama_kelas']); ?>">
                                <?php echo htmlspecialchars($k['nama_kelas']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="label-sm" style="display: block; font-weight: 700; font-size: 0.8rem; margin-bottom: 4px;">KKM</label>
                    <input type="number" name="kkm" class="z-input" value="75" min="0" max="100" style="width: 100%;">
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label class="label-sm" style="display: block; font-weight: 700; font-size: 0.8rem; margin-bottom: 4px;">Guru Pengampu / Pembuat Soal</label>
                <select name="guru_nama" class="z-input" style="width: 100%;">
                    <option value="">-- Pilih Guru Pengampu --</option>
                    <?php foreach($guruList as $g): ?>
                        <option value="<?php echo htmlspecialchars($g['nama']); ?>">
                            <?php echo htmlspecialchars($g['nama']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" onclick="document.getElementById('modalTambahPaket').classList.remove('open')" class="btn btn-outline">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary" style="background: #0ea5e9; border: none;">
                    <i data-lucide="check"></i> Simpan Paket Mapel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- MODAL: TAMBAH TOPIK UJIAN BARU                                  -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<div id="modalTambahTopik" class="modal-overlay" onclick="if(event.target === this) this.classList.remove('open')">
    <div style="background: #ffffff; width: 90%; max-width: 520px; border-radius: 20px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.18); border: 1px solid #e2e8f0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="folder-plus" style="width: 22px; height: 22px; color: #0ea5e9;"></i>
                Tambah Topik Ujian Baru
            </h3>
            <button type="button" onclick="document.getElementById('modalTambahTopik').classList.remove('open')" style="background: none; border: none; cursor: pointer; color: #94a3b8;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>

        <form method="POST" action="<?php echo base_url('cbt/topik/save'); ?>">
            <div style="margin-bottom: 1rem;">
                <label class="label-sm" style="display: block; font-weight: 700; font-size: 0.8rem; margin-bottom: 4px;">Nama Topik / Kegiatan Ujian <span style="color:#ef4444;">*</span></label>
                <input type="text" name="nama_topik" class="z-input" placeholder="Contoh: Penilaian Akhir Semester (PAS) Ganjil 2026/2027" required style="width: 100%;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 1rem;">
                <div>
                    <label class="label-sm" style="display: block; font-weight: 700; font-size: 0.8rem; margin-bottom: 4px;">Jenis Ujian <span style="color:#ef4444;">*</span></label>
                    <select name="jenis" class="z-input" required style="width: 100%;">
                        <option value="SAS">SAS (Sumatif Akhir Semester)</option>
                        <option value="STS">STS (Sumatif Tengah Semester)</option>
                        <option value="UM">UM (Ujian Madrasah)</option>
                        <option value="Try Out">Try Out / Simulasi</option>
                        <option value="PH">PH (Penilaian Harian)</option>
                        <option value="PAS">PAS (Penilaian Akhir Semester)</option>
                    </select>
                </div>
                <div>
                    <label class="label-sm" style="display: block; font-weight: 700; font-size: 0.8rem; margin-bottom: 4px;">Tahun Ajaran <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="tahun_ajaran" class="z-input" value="2026/2027" required style="width: 100%;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 1rem;">
                <div>
                    <label class="label-sm" style="display: block; font-weight: 700; font-size: 0.8rem; margin-bottom: 4px;">Tanggal Mulai</label>
                    <input type="date" name="tgl_mulai" class="z-input" style="width: 100%;">
                </div>
                <div>
                    <label class="label-sm" style="display: block; font-weight: 700; font-size: 0.8rem; margin-bottom: 4px;">Tanggal Selesai</label>
                    <input type="date" name="tgl_selesai" class="z-input" style="width: 100%;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" onclick="document.getElementById('modalTambahTopik').classList.remove('open')" class="btn btn-outline">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary" style="background: #0ea5e9; border: none;">
                    <i data-lucide="check"></i> Simpan Topik
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    lucide.createIcons();

    let currentKelasFilter = 'all';

    function setKelasFilter(kelas, btn) {
        currentKelasFilter = kelas;
        document.querySelectorAll('.btn-filter-kelas').forEach(b => {
            b.style.background = '#f8fafc';
            b.style.color = '#334155';
            b.style.borderColor = '#cbd5e1';
            b.classList.remove('active');
        });
        btn.style.background = '#0ea5e9';
        btn.style.color = '#ffffff';
        btn.style.borderColor = '#0ea5e9';
        btn.classList.add('active');
        filterBankSoalTable();
    }

    function filterBankSoalTable() {
        const q = (document.getElementById('searchMapelSoal')?.value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.row-paket-mapel');
        let visibleCount = 0;

        rows.forEach(r => {
            const rKelas = (r.getAttribute('data-kelas') || '').trim();
            const rSearch = (r.getAttribute('data-search') || '').trim();

            const matchKelas = (currentKelasFilter === 'all') || (rKelas === currentKelasFilter);
            const matchSearch = (!q) || rSearch.includes(q);

            if (matchKelas && matchSearch) {
                r.style.display = '';
                visibleCount++;
            } else {
                r.style.display = 'none';
            }
        });

        const countSpan = document.getElementById('totalVisibleCount');
        if (countSpan) countSpan.textContent = visibleCount;

        const emptyRow = document.getElementById('rowEmptyFilter');
        if (emptyRow) {
            emptyRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
        }
    }

    function openModalTambahPaket() {
        document.getElementById('modalTambahPaket').classList.add('open');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function openModalTambahTopik() {
        document.getElementById('modalTambahTopik').classList.add('open');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function confirmResetAll() {
        if (confirm('Apakah Anda yakin ingin mengosongkan SELURUH Bank Soal (semua paket mapel & butir soal)? Tindakan ini tidak dapat dibatalkan.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo base_url('bank_soal/reset-data'); ?>';
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Tangani parameter notifikasi
    (function() {
        const urlParams = new URLSearchParams(window.location.search);
        const msg = urlParams.get('msg');
        if (msg && typeof Swal !== 'undefined') {
            if (msg === 'synced_pengampu') {
                const count = urlParams.get('count') || '0';
                Swal.fire({
                    icon: 'success',
                    title: 'Mapel & Pengampu Dimuat!',
                    text: 'Berhasil memuat ' + count + ' mata pelajaran beserta guru pengampunya.',
                    timer: 2500,
                    showConfirmButton: false
                });
            } else if (msg === 'synced') {
                const count = urlParams.get('count') || '0';
                Swal.fire({
                    icon: 'success',
                    title: 'Sinkronisasi Jadwal Berhasil!',
                    text: 'Berhasil mengimpor ' + count + ' paket mapel dari jadwal CBT.',
                    timer: 2500,
                    showConfirmButton: false
                });
            } else if (msg === 'dummy_created') {
                Swal.fire({
                    icon: 'success',
                    title: 'Contoh Dummy Matematika Berhasil Dibuat!',
                    text: '50 butir soal (40 PG, 5 PG Kompleks, 5 Essay) berhasil digenerate.',
                    timer: 3000,
                    showConfirmButton: false
                });
            } else if (msg === 'locked') {
                Swal.fire({
                    icon: 'info',
                    title: 'Paket Soal Terkunci!',
                    text: 'Paket soal berhasil dikunci. Guru tidak dapat mengedit butir soal lagi.',
                    timer: 2500,
                    showConfirmButton: false
                });
            } else if (msg === 'unlocked') {
                Swal.fire({
                    icon: 'success',
                    title: 'Kunci Dibuka!',
                    text: 'Paket soal telah dibuka kunci. Guru dapat mengedit butir soal kembali.',
                    timer: 2500,
                    showConfirmButton: false
                });
            }
        }
    })();
</script>
</body>
</html>
