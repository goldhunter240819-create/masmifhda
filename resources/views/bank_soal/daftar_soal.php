<?php
$page_title = "Daftar Soal: " . ($mapelNama ?: 'Bank Soal');
$active_menu = "bank_soal";
$isLocked = !empty($paketInfo['is_locked']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - MAS MIFTAHUL HUDA</title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>?v=<?php echo time(); ?>">
    <link href="<?php echo base_url('public/assets/css/zamrud.css'); ?>" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .pill-jenis-lg {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 700;
        }
        .pill-pg { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .pill-pgk { background: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff; }
        .pill-essay { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .pill-bs { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .pill-jodoh { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

        .btn-action-soal {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            border-radius: 8px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-action-soal.view { background: #f0fdf4; color: #166534; border-color: #bbf7d0; }
        .btn-action-soal.view:hover { background: #dcfce7; }
        .btn-action-soal.edit { background: #e0f2fe; color: #0284c7; border-color: #bae6fd; }
        .btn-action-soal.edit:hover { background: #bae6fd; }
        .btn-action-soal.delete { background: #fee2e2; color: #ef4444; border-color: #fecaca; }
        .btn-action-soal.delete:hover { background: #fecaca; }

        /* Modal Preview */
        .modal-overlay {
            position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px); z-index: 9999; display: none;
            align-items: center; justify-content: center;
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

        <div class="z-content" style="padding: 0 0 3rem 0;">

            <!-- Alerts -->
            <?php if(isset($_GET['msg'])): ?>
                <?php if($_GET['msg'] === 'soal_saved'): ?>
                    <div style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                        <i data-lucide="check-circle" style="width: 20px; height: 20px; color: #22c55e;"></i> Butir soal dan kunci jawaban berhasil disimpan!
                    </div>
                <?php elseif($_GET['msg'] === 'soal_deleted'): ?>
                    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                        <i data-lucide="trash-2" style="width: 20px; height: 20px; color: #ef4444;"></i> Butir soal berhasil dihapus dari paket.
                    </div>
                <?php elseif($_GET['msg'] === 'soal_copied'): ?>
                    <div style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                        <i data-lucide="check-circle" style="width: 20px; height: 20px; color: #22c55e;"></i> Berhasil menyalin <?php echo (int)($_GET['count'] ?? 0); ?> butir soal ke paket ini!
                    </div>
                <?php elseif($_GET['msg'] === 'error_locked'): ?>
                    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                        <i data-lucide="lock" style="width: 20px; height: 20px; color: #ef4444;"></i> Soal ini sedang terkunci! Buka kunci paket terlebih dahulu untuk menambah atau mengedit soal.
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php
            $isGuruUser = isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 2;
            $backUrl = $isGuruUser 
                ? base_url('guru/bank-soal') 
                : base_url('bank_soal?topik_id=' . ($topikInfo['id'] ?? 1));
            $backLabel = $isGuruUser 
                ? 'Kembali ke Bank Soal Saya' 
                : 'Kembali ke Bank Soal Topik';
            ?>

            <!-- Header Card Paket Soal -->
            <div style="background: #ffffff; border-radius: 20px; border: 1.5px solid #e0f2fe; padding: 1.5rem 2rem; margin-bottom: 1.75rem; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
                <!-- Breadcrumb Terintegrasi di Dalam Card -->
                <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 1rem; margin-bottom: 1.25rem; border-bottom: 1px solid #f1f5f9; flex-wrap: wrap; gap: 10px;">
                    <div style="display: flex; align-items: center; gap: 8px; font-size: 0.83rem; font-weight: 700; flex-wrap: wrap;">
                        <a href="<?php echo $backUrl; ?>" style="color: #0284c7; background: #f0f9ff; padding: 5px 12px; border-radius: 8px; border: 1px solid #bae6fd; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: 0.2s; font-size: 0.8rem;">
                            <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i> <?php echo $backLabel; ?>
                        </a>
                        <span style="color: #cbd5e1;">/</span>
                        <span style="color: #64748b;"><?php echo htmlspecialchars($topikInfo['nama_topik'] ?? 'Topik Ujian'); ?></span>
                        <span style="color: #cbd5e1;">/</span>
                        <span style="color: #0ea5e9; font-weight: 800;"><?php echo htmlspecialchars($mapelNama); ?></span>
                    </div>

                    <div style="font-size: 0.78rem; color: #64748b; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; background: #f8fafc; padding: 4px 10px; border-radius: 6px; border: 1px solid #e2e8f0;">
                        <i data-lucide="shield-check" style="width: 14px; height: 14px; color: #10b981;"></i> CBT Repository
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.25rem;">
                <div>
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px; flex-wrap: wrap;">
                        <span class="pill-jenis-lg" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;">
                            [<?php echo htmlspecialchars($topikInfo['jenis'] ?? 'SAS'); ?>]
                        </span>
                        <span style="font-size: 0.82rem; font-weight: 800; background: #e0e7ff; color: #4338ca; padding: 3px 10px; border-radius: 8px;">
                            <?php echo htmlspecialchars($paketInfo['kelas_nama'] ?? 'Kelas X'); ?>
                        </span>
                        <?php if (!empty($paketInfo['kelas_sasaran'])): ?>
                            <span style="font-size: 0.82rem; font-weight: 700; background: #f1f5f9; color: #334155; padding: 3px 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                Sasaran: <strong><?php echo htmlspecialchars($paketInfo['kelas_sasaran']); ?></strong>
                            </span>
                        <?php endif; ?>
                        <?php if ($isLocked): ?>
                            <span style="font-size: 0.82rem; font-weight: 800; background: #fef3c7; color: #b45309; padding: 3px 12px; border-radius: 20px; border: 1px solid #fde68a; display: inline-flex; align-items: center; gap: 4px;">
                                <i data-lucide="lock" style="width: 13px; height: 13px;"></i> Terkunci (Final)
                            </span>
                        <?php else: ?>
                            <span style="font-size: 0.82rem; font-weight: 800; background: #ecfdf5; color: #059669; padding: 3px 12px; border-radius: 20px; border: 1px solid #a7f3d0; display: inline-flex; align-items: center; gap: 4px;">
                                <i data-lucide="unlock" style="width: 13px; height: 13px;"></i> Terbuka (Bisa Diedit Guru)
                            </span>
                        <?php endif; ?>
                    </div>

                    <h1 style="margin: 0 0 6px 0; font-size: 1.6rem; font-weight: 900; color: #0f172a;">
                        Kelola Soal: <?php echo htmlspecialchars($mapelNama); ?>
                    </h1>
                    
                    <!-- Breakdown Pills -->
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 8px;">
                        <span style="font-size: 0.82rem; font-weight: 800; color: #0f172a;">
                            Total: <?php echo count($soals); ?> Butir Soal
                        </span>
                        <span style="color: #cbd5e1;">&bull;</span>
                        <span class="pill-jenis-lg pill-pg">
                            <i data-lucide="check-circle-2" style="width: 13px; height: 13px;"></i>
                            <strong><?php echo $breakdown['sec_pg'] ?? 0; ?></strong> PG
                        </span>
                        <span class="pill-jenis-lg pill-pgk">
                            <i data-lucide="check-square" style="width: 13px; height: 13px;"></i>
                            <strong><?php echo $breakdown['sec_pgk'] ?? 0; ?></strong> PG Kompleks
                        </span>
                        <span class="pill-jenis-lg pill-essay">
                            <i data-lucide="file-text" style="width: 13px; height: 13px;"></i>
                            <strong><?php echo $breakdown['sec_essay'] ?? 0; ?></strong> Essay / Uraian
                        </span>
                        <?php if(!empty($breakdown['sec_bs'])): ?>
                            <span class="pill-jenis-lg pill-bs">
                                <strong><?php echo $breakdown['sec_bs']; ?></strong> Benar-Salah
                            </span>
                        <?php endif; ?>
                        <?php if(!empty($breakdown['sec_jodoh'])): ?>
                            <span class="pill-jenis-lg pill-jodoh">
                                <strong><?php echo $breakdown['sec_jodoh']; ?></strong> Menjodohkan
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Actions Header -->
                <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                    <!-- Tombol Kunci / Buka Kunci (Hanya Admin / Kurikulum) -->
                    <?php if ($paketInfo && !$isGuruUser): ?>
                    <form method="POST" action="<?php echo base_url('bank_soal/paket/toggle-lock'); ?>" style="display: inline;" onsubmit="return confirm('<?php echo $isLocked ? 'Buka kunci soal ini?' : 'Kunci soal ini agar guru tidak dapat mengedit lagi?'; ?>')">
                        <input type="hidden" name="paket_id" value="<?php echo $paketInfo['id']; ?>">
                        <input type="hidden" name="topik_id" value="<?php echo $topikInfo['id']; ?>">
                        <?php if ($isLocked): ?>
                            <button type="submit" class="btn btn-outline" style="border-color: #cbd5e1; color: #1e293b; font-weight: 700;">
                                <i data-lucide="unlock" style="color: #22c55e;"></i> Buka Kunci Soal
                            </button>
                        <?php else: ?>
                            <button type="submit" class="btn btn-outline" style="background: #fffbeb; color: #b45309; border-color: #fde68a; font-weight: 700;">
                                <i data-lucide="lock" style="color: #d97706;"></i> Kunci Soal Sekarang
                            </button>
                        <?php endif; ?>
                    </form>
                    <?php endif; ?>

                    <!-- Tombol Cetak Naskah & Kunci Jawaban -->
                    <?php if ($paketInfo): ?>
                        <a href="<?php echo base_url('bank_soal/cetak_soal?paket_id=' . $paketInfo['id']); ?>" target="_blank" class="btn btn-outline" style="background: #ffffff; border-color: #bae6fd; color: #0284c7; font-weight: 800; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;" title="Cetak Naskah Butir Soal Lengkap dengan Kunci Jawaban (PDF)">
                            <i data-lucide="printer" style="width: 15px; height: 15px; color: #0284c7;"></i>
                            <span>Cetak Soal & Kunci (PDF)</span>
                        </a>
                    <?php endif; ?>

                    <!-- Tombol Salin Soal -->
                    <?php if (!$isLocked && $paketInfo): ?>
                        <button type="button" onclick="openModalCopySoal()" class="btn btn-outline" style="background: #f0fdf4; border-color: #86efac; color: #166534; font-weight: 800; display: inline-flex; align-items: center; gap: 6px;">
                            <i data-lucide="copy" style="width: 15px; height: 15px; color: #16a34a;"></i>
                            <span>Salin Soal dari Paket Lain</span>
                        </button>
                    <?php endif; ?>

                    <!-- Tombol Tambah Soal -->
                    <?php if ($isLocked): ?>
                        <button type="button" class="btn" style="background: #e2e8f0; color: #94a3b8; cursor: not-allowed; font-weight: 700;" title="Soal terkunci, buka kunci untuk menambah butir soal">
                            <i data-lucide="lock" style="width: 15px; height: 15px;"></i> Tambah Soal (Terkunci)
                        </button>
                    <?php else: ?>
                        <a href="<?php echo base_url('bank_soal/tambah_soal?paket_id=' . ($paketInfo['id'] ?? '') . '&topik_id=' . ($topikInfo['id'] ?? 1) . '&mapel=' . urlencode($mapelNama)); ?>" class="btn btn-primary" style="background: #0ea5e9; border: none; font-weight: 800; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);">
                            <i data-lucide="plus"></i> Tambah Butir Soal Baru
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            </div>

            <!-- Tabel Daftar Butir Soal -->
            <div class="z-card" style="padding: 0; border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden; background: #ffffff; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
                <div style="padding: 1.25rem 1.75rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div style="font-weight: 800; font-size: 0.95rem; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                        <span>Daftar Butir Soal & Kunci Jawaban</span>
                        <span style="font-size: 0.75rem; background: #e0f2fe; color: #0284c7; padding: 2px 8px; border-radius: 100px; font-weight: 800;">
                            <?php echo count($soals); ?> Butir
                        </span>
                    </div>

                    <?php if ($isLocked): ?>
                        <div style="font-size: 0.78rem; font-weight: 700; color: #b45309; background: #fef3c7; padding: 4px 12px; border-radius: 20px; border: 1px solid #fde68a; display: flex; align-items: center; gap: 5px;">
                            <i data-lucide="shield-alert" style="width: 14px; height: 14px;"></i> Mode Baca Saja (Terkunci)
                        </div>
                    <?php endif; ?>
                </div>

                <div class="z-table-wrap">
                    <table id="tabelSoal" class="z-table" style="width:100%;">
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">No</th>
                                <th style="width: 140px;">Tipe Soal</th>
                                <th>Pertanyaan & Cuplikan Soal</th>
                                <th>Kunci Jawaban</th>
                                <th style="text-align: center; width: 80px;">Skor</th>
                                <th style="text-align: right; width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($soals)): ?>
                            <tr>
                                <td colspan="6" style="padding: 4rem 1.5rem; text-align: center; color: #94a3b8;">
                                    <i data-lucide="file-question" style="width: 48px; height: 48px; margin-bottom: 10px; color: #cbd5e1;"></i>
                                    <div style="font-size: 1rem; font-weight: 800; color: #475569;">Belum ada butir soal pada paket ini</div>
                                    <div style="font-size: 0.82rem; margin-top: 4px;">Klik tombol "Tambah Butir Soal Baru" di atas untuk mulai memasukkan soal, atau salin dari paket mapel lain.</div>
                                    <?php if (!$isLocked && $paketInfo): ?>
                                    <div style="margin-top: 14px; display: flex; justify-content: center; gap: 10px; flex-wrap: wrap;">
                                        <a href="<?php echo base_url('bank_soal/tambah_soal?paket_id=' . ($paketInfo['id'] ?? '') . '&topik_id=' . ($topikInfo['id'] ?? 1) . '&mapel=' . urlencode($mapelNama)); ?>" class="btn btn-primary" style="font-size: 0.82rem; font-weight: 800; background: #0ea5e9; border: none;">
                                            <i data-lucide="plus"></i> Tambah Butir Soal Baru
                                        </a>
                                        <button type="button" onclick="openModalCopySoal()" class="btn btn-outline" style="background: #f0fdf4; border-color: #86efac; color: #166534; font-size: 0.82rem; font-weight: 800;">
                                            <i data-lucide="copy" style="width: 14px; height: 14px; color: #16a34a;"></i> Salin dari Paket Lain
                                        </button>
                                    </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php 
                                $no = 1;
                                foreach($soals as $s): 
                                    $t = $s['tipe_soal'];
                                    $tLabel = 'Pilihan Ganda';
                                    $tClass = 'pill-pg';
                                    if ($t === 'sec_pgk') { $tLabel = 'PG Kompleks'; $tClass = 'pill-pgk'; }
                                    elseif ($t === 'sec_essay') { $tLabel = 'Essay (Uraian)'; $tClass = 'pill-essay'; }
                                    elseif ($t === 'sec_bs') { $tLabel = 'Benar / Salah'; $tClass = 'pill-bs'; }
                                    elseif ($t === 'sec_jodoh') { $tLabel = 'Menjodohkan'; $tClass = 'pill-jodoh'; }

                                    $cleanPertanyaan = strip_tags($s['pertanyaan']);
                                    if (strlen($cleanPertanyaan) > 120) {
                                        $cleanPertanyaan = substr($cleanPertanyaan, 0, 120) . '...';
                                    }
                                ?>
                                <tr>
                                    <td style="text-align: center; font-weight: 800; color: #64748b;"><?php echo $s['nomor_urut'] ?: $no++; ?></td>
                                    <td>
                                        <span class="pill-jenis-lg <?php echo $tClass; ?>" style="font-size: 0.72rem;">
                                            <?php echo $tLabel; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: #1e293b; font-size: 0.88rem; line-height: 1.4;">
                                            <?php echo htmlspecialchars($cleanPertanyaan); ?>
                                        </div>
                                        <div style="font-size: 0.72rem; color: #64748b; margin-top: 3px;">
                                            Tingkat: <span style="font-weight: 700;"><?php echo htmlspecialchars($s['tingkat_kesulitan'] ?: 'Sedang'); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($t === 'sec_pg'): ?>
                                            <span style="font-size: 0.82rem; font-weight: 900; background: #ecfdf5; color: #059669; padding: 3px 10px; border-radius: 6px; border: 1px solid #a7f3d0;">
                                                Kunci: <?php echo htmlspecialchars($s['kunci_jawaban'] ?: '-'); ?>
                                            </span>
                                        <?php elseif ($t === 'sec_pgk'): ?>
                                            <span style="font-size: 0.78rem; font-weight: 800; background: #f3e8ff; color: #7e22ce; padding: 3px 8px; border-radius: 6px; border: 1px solid #e9d5ff;">
                                                Kunci: [<?php echo htmlspecialchars($s['kunci_jawaban'] ?: '-'); ?>]
                                            </span>
                                        <?php elseif ($t === 'sec_essay'): ?>
                                            <span style="font-size: 0.72rem; font-weight: 700; color: #475569; background: #f1f5f9; padding: 2px 8px; border-radius: 6px; max-width: 220px; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                Pedoman: <?php echo htmlspecialchars($s['kunci_jawaban'] ?: 'Rubrik penilaian'); ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="font-size: 0.75rem; color: #64748b;">
                                                <?php echo htmlspecialchars(substr($s['kunci_jawaban'], 0, 30)); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center; font-weight: 800; color: #0ea5e9;">
                                        <?php echo (float)$s['skor_maksimal']; ?>
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <div style="display: inline-flex; gap: 4px; align-items: center;">
                                            <!-- Preview Soal -->
                                            <button type="button" class="btn-action-soal view" onclick="previewSoal(<?php echo htmlspecialchars(json_encode($s), ENT_QUOTES, 'UTF-8'); ?>)" title="Lihat Tampilan Soal">
                                                <i data-lucide="eye" style="width: 15px; height: 15px;"></i>
                                            </button>

                                            <!-- Edit Soal (hanya jika tidak terkunci) -->
                                            <?php if (!$isLocked): ?>
                                                <a href="<?php echo base_url('bank_soal/tambah_soal?id=' . $s['id'] . '&paket_id=' . ($paketInfo['id'] ?? '') . '&topik_id=' . ($topikInfo['id'] ?? 1) . '&mapel=' . urlencode($mapelNama)); ?>" class="btn-action-soal edit" title="Edit Butir Soal">
                                                    <i data-lucide="edit-3" style="width: 15px; height: 15px;"></i>
                                                </a>
                                                <form method="POST" action="<?php echo base_url('bank_soal/hapus_soal'); ?>" style="display: inline;" onsubmit="return confirm('Hapus butir soal no. <?php echo $s['nomor_urut']; ?> ini?')">
                                                    <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                                                    <button type="submit" class="btn-action-soal delete" title="Hapus Butir Soal">
                                                        <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- Modal Preview Soal -->
<div id="modalPreviewSoal" class="modal-overlay" onclick="if(event.target === this) this.classList.remove('open')">
    <div style="background: #ffffff; width: 92%; max-width: 650px; border-radius: 20px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.18); border: 1px solid #e2e8f0; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span id="prevTipeBadge" class="pill-jenis-lg pill-pg">PG</span>
                <span id="prevTingkat" style="font-size: 0.75rem; font-weight: 700; color: #64748b;">Tingkat: Sedang</span>
            </div>
            <button type="button" onclick="document.getElementById('modalPreviewSoal').classList.remove('open')" style="background: none; border: none; cursor: pointer; color: #94a3b8;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>

        <div style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 1.25rem; line-height: 1.5;" id="prevPertanyaan">
            -
        </div>

        <div id="prevOpsiContainer" style="margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 8px;">
            <!-- Opsi populated by JS -->
        </div>

        <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <span style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Kunci Jawaban:</span>
                <div id="prevKunci" style="font-size: 1rem; font-weight: 900; color: #059669; margin-top: 2px;">-</div>
            </div>
            <div style="text-align: right;">
                <span style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Bobot Skor:</span>
                <div id="prevSkor" style="font-size: 1.1rem; font-weight: 900; color: #0ea5e9; margin-top: 2px;">-</div>
            </div>
        </div>

        <div style="margin-top: 1.25rem; display: flex; justify-content: flex-end;">
            <button type="button" onclick="document.getElementById('modalPreviewSoal').classList.remove('open')" class="btn btn-outline">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();

    function previewSoal(data) {
        document.getElementById('prevPertanyaan').innerHTML = data.pertanyaan || '';
        document.getElementById('prevTingkat').innerText = 'Tingkat: ' + (data.tingkat_kesulitan || 'Sedang');
        document.getElementById('prevKunci').innerText = data.kunci_jawaban || '-';
        document.getElementById('prevSkor').innerText = data.skor_maksimal || '2';

        const badge = document.getElementById('prevTipeBadge');
        const container = document.getElementById('prevOpsiContainer');
        container.innerHTML = '';

        if (data.tipe_soal === 'sec_pg') {
            badge.className = 'pill-jenis-lg pill-pg';
            badge.innerText = 'Pilihan Ganda';
            const ops = ['A', 'B', 'C', 'D', 'E'];
            ops.forEach(o => {
                const fName = 'opsi_' + o.toLowerCase();
                if (data[fName]) {
                    const isK = (data.kunci_jawaban && data.kunci_jawaban.trim().toUpperCase() === o);
                    const div = document.createElement('div');
                    div.style.cssText = 'padding: 8px 14px; border-radius: 10px; border: 1.5px solid ' + (isK ? '#86efac; background: #f0fdf4;' : '#e2e8f0; background: #fff;') + ' font-size: 0.88rem; display: flex; align-items: center; gap: 8px;';
                    div.innerHTML = '<strong style="color:' + (isK ? '#15803d' : '#0f172a') + ';">' + o + '.</strong> <span>' + data[fName] + '</span>' + (isK ? ' <span style="margin-left: auto; font-size: 0.72rem; font-weight: 800; background: #dcfce7; color: #166534; padding: 2px 6px; border-radius: 4px;">KUNCI</span>' : '');
                    container.appendChild(div);
                }
            });
        } else if (data.tipe_soal === 'sec_pgk') {
            badge.className = 'pill-jenis-lg pill-pgk';
            badge.innerText = 'PG Kompleks';
            try {
                const kObj = JSON.parse(data.konten_tambahan || '{}');
                if (kObj.opsi && Array.isArray(kObj.opsi)) {
                    kObj.opsi.forEach((op, idx) => {
                        const div = document.createElement('div');
                        const isK = op.is_kunci;
                        div.style.cssText = 'padding: 8px 14px; border-radius: 10px; border: 1.5px solid ' + (isK ? '#d8b4fe; background: #faf5ff;' : '#e2e8f0; background: #fff;') + ' font-size: 0.88rem; display: flex; align-items: center; gap: 8px;';
                        div.innerHTML = '<input type="checkbox" ' + (isK ? 'checked' : '') + ' disabled> <span>' + (op.teks || '') + '</span>' + (isK ? ' <span style="margin-left: auto; font-size: 0.72rem; font-weight: 800; background: #f3e8ff; color: #7e22ce; padding: 2px 6px; border-radius: 4px;">KUNCI</span>' : '');
                        container.appendChild(div);
                    });
                }
            } catch(e) {}
        } else if (data.tipe_soal === 'sec_bs') {
            badge.className = 'pill-jenis-lg pill-bs';
            badge.innerText = 'Benar / Salah';
            try {
                const kObj = JSON.parse(data.konten_tambahan || '{}');
                if (kObj.butir && Array.isArray(kObj.butir)) {
                    let tableHtml = '<table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; margin-top: 6px;"><tr style="background:#f1f5f9; font-weight:700;"><th style="padding:6px 10px; text-align:left; border:1px solid #e2e8f0;">Pernyataan</th><th style="padding:6px 10px; text-align:center; width:90px; border:1px solid #e2e8f0;">Kunci</th></tr>';
                    kObj.butir.forEach(b => {
                        const isB = (b.kunci === 'Benar');
                        tableHtml += `<tr><td style="padding:8px 10px; border:1px solid #e2e8f0;">${b.teks}</td><td style="padding:8px 10px; text-align:center; border:1px solid #e2e8f0; font-weight:800; color:${isB ? '#16a34a' : '#dc2626'}; background:${isB ? '#f0fdf4' : '#fef2f2'};">${b.kunci}</td></tr>`;
                    });
                    tableHtml += '</table>';
                    container.innerHTML = tableHtml;
                }
            } catch(e) {}
        } else if (data.tipe_soal === 'sec_jodoh') {
            badge.className = 'pill-jenis-lg pill-jodoh';
            badge.innerText = 'Menjodohkan';
            try {
                const kObj = JSON.parse(data.konten_tambahan || '{}');
                if (kObj.pasangan && Array.isArray(kObj.pasangan)) {
                    let tableHtml = '<table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; margin-top: 6px;"><tr style="background:#f1f5f9; font-weight:700;"><th style="padding:6px 10px; text-align:left; border:1px solid #e2e8f0;">Premis (Kiri)</th><th style="padding:6px 10px; text-align:center; width:40px; border:1px solid #e2e8f0;">➜</th><th style="padding:6px 10px; text-align:left; border:1px solid #e2e8f0;">Respon (Kanan)</th></tr>';
                    kObj.pasangan.forEach(p => {
                        tableHtml += `<tr><td style="padding:8px 10px; border:1px solid #e2e8f0; font-weight:600;">${p.premis}</td><td style="padding:8px 10px; text-align:center; border:1px solid #e2e8f0; color:#64748b;">➜</td><td style="padding:8px 10px; border:1px solid #e2e8f0; font-weight:700; color:#0284c7; background:#f0f9ff;">${p.respon}</td></tr>`;
                    });
                    tableHtml += '</table>';
                    container.innerHTML = tableHtml;
                }
            } catch(e) {}
        } else if (data.tipe_soal === 'sec_essay' || data.tipe_soal === 'sec_isian') {
            badge.className = 'pill-jenis-lg pill-essay';
            badge.innerText = (data.tipe_soal === 'sec_isian') ? 'Isian Singkat' : 'Essay (Uraian)';
            container.innerHTML = '<div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 12px; font-size: 0.85rem; color: #475569;"><strong>Kunci Jawaban / Rubrik:</strong> ' + (data.kunci_jawaban || '-') + '</div>';
        }

        document.getElementById('modalPreviewSoal').classList.add('open');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function openModalCopySoal() {
        const modal = document.getElementById('modalCopySoal');
        if (modal) {
            modal.classList.add('open');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }
</script>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- MODAL: SALIN BUTIR SOAL DARI PAKET LAIN                          -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<div id="modalCopySoal" class="modal-overlay" onclick="if(event.target === this) this.classList.remove('open')">
    <div style="background: #ffffff; width: 92%; max-width: 540px; border-radius: 20px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.18); border: 1px solid #e2e8f0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 900; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="copy" style="width: 20px; height: 20px; color: #16a34a;"></i>
                Salin Butir Soal ke Paket Ini
            </h3>
            <button type="button" onclick="document.getElementById('modalCopySoal').classList.remove('open')" style="background: none; border: none; cursor: pointer; color: #94a3b8; padding: 4px;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 14px; margin-bottom: 1.25rem; font-size: 0.82rem; color: #475569;">
            <div>Target Tujuan: <strong><?php echo htmlspecialchars($mapelNama); ?> (<?php echo htmlspecialchars($paketInfo['kelas_nama'] ?? ''); ?>)</strong></div>
            <div style="color: #64748b; font-size: 0.76rem; margin-top: 2px;">Seluruh butir soal, opsi pilihan ganda, dan kunci jawaban dari paket sumber akan diduplikasi secara otomatis.</div>
        </div>

        <?php if(empty($sourcePaketList)): ?>
            <div style="padding: 2rem 1rem; text-align: center; color: #64748b;">
                <i data-lucide="inbox" style="width: 40px; height: 40px; color: #cbd5e1; margin-bottom: 8px;"></i>
                <div style="font-weight: 800; color: #334155; font-size: 0.95rem;">Belum Ada Paket Sumber yang Memiliki Soal</div>
                <div style="font-size: 0.8rem; margin-top: 4px;">Belum ada paket mapel lain yang terisi butir soal untuk disalin. Silakan buat butir soal baru terlebih dahulu.</div>
            </div>
            <div style="display: flex; justify-content: flex-end; margin-top: 1rem;">
                <button type="button" onclick="document.getElementById('modalCopySoal').classList.remove('open')" class="btn btn-outline">Tutup</button>
            </div>
        <?php else: ?>
            <form method="POST" action="<?php echo base_url('bank_soal/soal/copy'); ?>" onsubmit="return confirm('Salin butir soal dari paket sumber terpilih ke paket ini?')">
                <input type="hidden" name="target_paket_id" value="<?php echo $paketInfo['id'] ?? 0; ?>">

                <div style="margin-bottom: 1.2rem;">
                    <label style="display: block; font-size: 0.82rem; font-weight: 800; color: #1e293b; margin-bottom: 6px;">
                        Pilih Paket Soal Sumber: *
                    </label>
                    <select name="source_paket_id" id="source_paket_id" required style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 0.85rem; font-weight: 600; background: #ffffff;">
                        <?php foreach($sourcePaketList as $src): ?>
                            <option value="<?php echo $src['id']; ?>">
                                [<?php echo htmlspecialchars($src['topik_jenis'] ?: 'SAS'); ?>] <?php echo htmlspecialchars($src['mapel_nama']); ?> - <?php echo htmlspecialchars($src['kelas_nama']); ?> (<?php echo $src['jml_soal']; ?> Soal)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.82rem; font-weight: 800; color: #1e293b; margin-bottom: 6px;">
                        Metode Penyalinan:
                    </label>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <label style="display: flex; align-items: flex-start; gap: 10px; padding: 10px 12px; border: 1.5px solid #e2e8f0; border-radius: 10px; cursor: pointer; background: #ffffff;">
                            <input type="radio" name="mode" value="append" checked style="margin-top: 3px;">
                            <div>
                                <div style="font-weight: 800; font-size: 0.85rem; color: #0f172a;">Tambahkan (Append)</div>
                                <div style="font-size: 0.74rem; color: #64748b;">Soal yang sudah ada di paket ini tetap tersimpan, soal baru akan ditambahkan di nomor berikutnya.</div>
                            </div>
                        </label>
                        <label style="display: flex; align-items: flex-start; gap: 10px; padding: 10px 12px; border: 1.5px solid #fee2e2; border-radius: 10px; cursor: pointer; background: #fff5f5;">
                            <input type="radio" name="mode" value="overwrite" style="margin-top: 3px;">
                            <div>
                                <div style="font-weight: 800; font-size: 0.85rem; color: #b91c1c;">Gantikan Seluruhnya (Overwrite)</div>
                                <div style="font-size: 0.74rem; color: #ef4444;">Hapus seluruh soal di paket ini terlebih dahulu dan gantikan persis seperti paket sumber.</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" onclick="document.getElementById('modalCopySoal').classList.remove('open')" class="btn btn-outline" style="border-color: #cbd5e1; font-weight: 700;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="background: #16a34a; border: none; font-weight: 800; display: inline-flex; align-items: center; gap: 6px;">
                        <i data-lucide="copy" style="width: 15px; height: 15px;"></i> Salin Sekarang
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
