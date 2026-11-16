<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'Guru');
$roleName = "Guru / Tendik";
if (!empty($_SESSION['is_kamad'])) $roleName = "Kepala Madrasah";
$initial = strtoupper(substr($nama, 0, 2));

$page_title = 'Bank Soal Saya';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Soal Saya - MAS MIFTAHUL HUDA</title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . time()); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/multi_dashboard.css?v=' . time()); ?>">
    <style>
        .guru-bs-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1.5px solid #e2e8f0;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.25s ease;
        }
        .guru-bs-card:hover {
            transform: translateY(-3px);
            border-color: #0ea5e9;
            box-shadow: 0 10px 25px rgba(14, 165, 233, 0.1);
        }
        .pill-jenis {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 700;
        }
        .pill-pg { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .pill-pgk { background: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff; }
        .pill-essay { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .pill-bs { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

        .stat-card-guru {
            background: #ffffff;
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }
    </style>
</head>
<body>
<div class="siakad-container">

    <?php include __DIR__ . '/../sidebar_multi_guru.php'; ?>

    <!-- ── MAIN AREA ── -->
    <div class="z-main">

        <?php include __DIR__ . '/../header_multi_guru.php'; ?>

        <!-- Scrollable Content -->
        <div class="z-scroll" style="padding: 0 var(--z-gap) var(--z-gap);">

            <!-- Hero Banner -->
            <div class="elite-page-header" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); padding: 2rem 2.5rem; border-radius: var(--z-radius, 12px); margin-bottom: 2rem; color: white; position: relative; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(2, 132, 199, 0.35);">
                <div style="position: relative; z-index: 2;">
                    <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.18); padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; letter-spacing: 0.5px; margin-bottom: 8px;">
                        <i data-lucide="layers" style="width: 14px; height: 14px;"></i> RUANG GURU &bull; REPOSITORI SOAL
                    </div>
                    <h1 style="font-size: 1.85rem; font-weight: 900; margin: 0 0 0.5rem 0; letter-spacing: -0.5px;">
                        Bank Soal Saya
                    </h1>
                    <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem; max-width: 700px;">
                        Kelola dan input butir soal (Pilihan Ganda, PG Kompleks, Essay) beserta kunci jawaban untuk mata pelajaran dan kelas yang Anda ampu.
                    </p>
                </div>
            </div>

            <!-- Stats Mini -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
                <div class="stat-card-guru">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i data-lucide="book-open"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.7rem; font-weight: 800; color: #0f172a; line-height: 1.2;"><?php echo count($myPackages); ?></div>
                        <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Paket Mapel Diampu</div>
                    </div>
                </div>

                <div class="stat-card-guru">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #f3e8ff; color: #9333ea; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i data-lucide="file-question"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.7rem; font-weight: 800; color: #0f172a; line-height: 1.2;"><?php echo $totalMySoal; ?></div>
                        <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Total Butir Soal Terinput</div>
                    </div>
                </div>

                <div class="stat-card-guru">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i data-lucide="lock"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.7rem; font-weight: 800; color: #0f172a; line-height: 1.2;"><?php echo $totalMyLocked; ?> / <?php echo count($myPackages); ?></div>
                        <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Paket Terkunci (Final)</div>
                    </div>
                </div>
            </div>

            <!-- Filter Topik Ujian -->
            <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 1.25rem 1.75rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i data-lucide="filter" style="color: #0ea5e9; width: 20px; height: 20px;"></i>
                    <div>
                        <div style="font-weight: 800; font-size: 0.95rem; color: #0f172a;">Pilih Topik Ujian:</div>
                        <div style="font-size: 0.78rem; color: #64748b;">Menampilkan paket soal untuk kegiatan ujian yang dipilih</div>
                    </div>
                </div>

                <form method="GET" action="" style="display: flex; gap: 10px; align-items: center;">
                    <select name="topik_id" class="z-input" onchange="this.form.submit()" style="font-weight: 700; min-width: 280px; padding: 8px 14px; border-radius: 10px;">
                        <?php foreach($topikList as $tp): ?>
                            <option value="<?php echo $tp['id']; ?>" <?php echo ((int)$tp['id'] === $selectedTopikId) ? 'selected' : ''; ?>>
                                [<?php echo htmlspecialchars($tp['jenis'] ?: 'SAS'); ?>] <?php echo htmlspecialchars($tp['nama_topik']); ?> (<?php echo htmlspecialchars($tp['status'] ?: 'Terjadwal'); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <!-- Grid Kartu Paket Mapel Diampu -->
            <?php if (empty($myPackages)): ?>
                <div style="background: #ffffff; border-radius: 18px; border: 1.5px dashed #cbd5e1; padding: 4rem 2rem; text-align: center; color: #64748b;">
                    <i data-lucide="inbox" style="width: 48px; height: 48px; color: #94a3b8; margin-bottom: 12px;"></i>
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: #1e293b; margin: 0 0 6px 0;">Belum Ada Paket Mapel</h3>
                    <p style="font-size: 0.85rem; margin: 0;">
                        Tidak ditemukan penugasan mengajar untuk akun Anda pada topik ujian ini. Pastikan jadwal mengajar sudah diatur di SIAKAD.
                    </p>
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                    <?php foreach($myPackages as $pk): 
                        $isLocked = !empty($pk['is_locked']);
                        $kls = trim($pk['kelas_nama'] ?: 'X E.1');
                        $tot = $pk['total_soal'] ?? 0;
                        $bd = $pk['breakdown'] ?? [];

                        // Warna Badge Kelas
                        $klsBg = '#eff6ff'; $klsCol = '#1d4ed8'; $klsBorder = '#bfdbfe';
                        if (strpos($kls, 'XI F') !== false) {
                            $klsBg = '#ecfdf5'; $klsCol = '#047857'; $klsBorder = '#a7f3d0';
                        } elseif (strpos($kls, 'XII F.1') !== false) {
                            $klsBg = '#f5f3ff'; $klsCol = '#6d28d9'; $klsBorder = '#ddd6fe';
                        } elseif (strpos($kls, 'XII F.2') !== false) {
                            $klsBg = '#fffbeb'; $klsCol = '#b45309'; $klsBorder = '#fde68a';
                        }
                    ?>
                    <div class="guru-bs-card">
                        <div>
                            <!-- Header Card: Kelas & Status Kunci -->
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <span style="font-size: 0.8rem; font-weight: 800; background: <?php echo $klsBg; ?>; color: <?php echo $klsCol; ?>; border: 1px solid <?php echo $klsBorder; ?>; padding: 4px 10px; border-radius: 8px; display: inline-flex; align-items: center; gap: 5px;">
                                    <i data-lucide="graduation-cap" style="width: 13px; height: 13px;"></i>
                                    Kelas: <?php echo htmlspecialchars($kls); ?>
                                </span>

                                <?php if ($isLocked): ?>
                                    <span style="font-size: 0.72rem; font-weight: 800; background: #fef3c7; color: #b45309; padding: 4px 10px; border-radius: 20px; border: 1px solid #fde68a; display: inline-flex; align-items: center; gap: 4px;">
                                        <i data-lucide="lock" style="width: 12px; height: 12px;"></i> Terkunci (Final)
                                    </span>
                                <?php else: ?>
                                    <span style="font-size: 0.72rem; font-weight: 800; background: #ecfdf5; color: #047857; padding: 4px 10px; border-radius: 20px; border: 1px solid #a7f3d0; display: inline-flex; align-items: center; gap: 4px;">
                                        <i data-lucide="unlock" style="width: 12px; height: 12px;"></i> Terbuka (Bisa Diedit)
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Mapel Title -->
                            <h3 style="margin: 0 0 6px 0; font-size: 1.2rem; font-weight: 900; color: #0f172a; line-height: 1.3;">
                                <?php echo htmlspecialchars($pk['mapel_nama']); ?>
                            </h3>

                            <div style="font-size: 0.78rem; color: #64748b; font-weight: 600; margin-bottom: 14px;">
                                Pengampu: <strong><?php echo htmlspecialchars($pk['guru_nama'] ?: $nama); ?></strong>
                            </div>

                            <!-- Summary Box -->
                            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 14px; margin-bottom: 1.25rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                    <span style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Total Butir Soal</span>
                                    <span style="font-size: 1.15rem; font-weight: 900; color: #0f172a;"><?php echo $tot; ?> <span style="font-size: 0.75rem; font-weight: 600; color: #94a3b8;">soal</span></span>
                                </div>

                                <!-- Breakdown Pills -->
                                <?php if ($tot > 0): ?>
                                    <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                        <?php if (!empty($bd['sec_pg'])): ?>
                                            <span class="pill-jenis pill-pg">
                                                <i data-lucide="check-circle-2" style="width: 11px; height: 11px;"></i>
                                                <strong><?php echo $bd['sec_pg']; ?></strong> PG
                                            </span>
                                        <?php endif; ?>

                                        <?php if (!empty($bd['sec_pgk'])): ?>
                                            <span class="pill-jenis pill-pgk">
                                                <i data-lucide="check-square" style="width: 11px; height: 11px;"></i>
                                                <strong><?php echo $bd['sec_pgk']; ?></strong> PGK
                                            </span>
                                        <?php endif; ?>

                                        <?php if (!empty($bd['sec_essay'])): ?>
                                            <span class="pill-jenis pill-essay">
                                                <i data-lucide="file-text" style="width: 11px; height: 11px;"></i>
                                                <strong><?php echo $bd['sec_essay']; ?></strong> Esai
                                            </span>
                                        <?php endif; ?>

                                        <?php if (!empty($bd['sec_bs'])): ?>
                                            <span class="pill-jenis pill-bs">
                                                <strong><?php echo $bd['sec_bs']; ?></strong> B-S
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div style="font-size: 0.75rem; color: #94a3b8; font-style: italic;">
                                        Belum ada butir soal diinput
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <?php if ($isLocked): ?>
                            <a href="<?php echo base_url('bank_soal/daftar_soal?paket_id=' . $pk['id']); ?>" class="btn btn-outline" style="width: 100%; justify-content: center; font-weight: 800; font-size: 0.88rem; border-color: #cbd5e1; color: #475569; padding: 10px 16px; border-radius: 10px;">
                                <i data-lucide="eye"></i> Lihat Soal (Terkunci)
                            </a>
                        <?php else: ?>
                            <a href="<?php echo base_url('bank_soal/daftar_soal?paket_id=' . $pk['id']); ?>" class="btn btn-primary" style="width: 100%; justify-content: center; font-weight: 800; font-size: 0.88rem; background: linear-gradient(135deg, #0ea5e9, #0284c7); border: none; padding: 10px 16px; border-radius: 10px; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.25);">
                                <i data-lucide="edit-3"></i> Kelola Butir Soal &rarr;
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Info Alert Bawah -->
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 14px; padding: 14px 18px; display: flex; align-items: center; gap: 12px; color: #1e40af; font-size: 0.85rem; font-weight: 600;">
                <i data-lucide="info" style="width: 20px; height: 20px; color: #0284c7; flex-shrink: 0;"></i>
                <div>
                    <strong>Informasi Kunci Soal:</strong> Ketika paket soal berstatus <em>Terkunci (Final)</em> oleh Kurikulum/Admin, butir soal tidak dapat ditambah, diedit, atau dihapus baik dari Web Portal maupun Aplikasi Mobile (APK).
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
</body>
</html>
