<?php
$pageTitle = 'Topik Ujian CBT - MAS MIFTAHUL HUDA';
$active_menu = 'topik';

$search = trim($_GET['q'] ?? '');
$filterJenis = trim($_GET['jenis'] ?? '');
$filteredList = $topikList ?? [];

if (!empty($search)) {
    $filteredList = array_filter($filteredList, function($t) use ($search) {
        return stripos($t['nama_topik'], $search) !== false || stripos($t['jenis'], $search) !== false;
    });
}
if (!empty($filterJenis)) {
    $filteredList = array_filter($filteredList, function($t) use ($filterJenis) {
        return strtoupper($t['jenis']) === strtoupper($filterJenis);
    });
}

function formatTanggalIndo($dateStr) {
    if (empty($dateStr)) return '-';
    $bulanIndo = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $time = strtotime($dateStr);
    $d = date('j', $time);
    $m = (int)date('n', $time);
    $y = date('Y', $time);
    return "$d " . ($bulanIndo[$m] ?? '') . " $y";
}
?>

<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/header.php'; ?>

        <div class="z-scroll" style="padding: 0 0 3rem 0;">

            <!-- Alerts -->
            <?php if(isset($_GET['msg'])): ?>
                <?php if($_GET['msg'] === 'saved'): ?>
                    <div style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                        <i data-lucide="check-circle" style="width: 20px; height: 20px; color: #22c55e;"></i> Topik ujian berhasil disimpan dan diperbarui!
                    </div>
                <?php elseif($_GET['msg'] === 'deleted'): ?>
                    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                        <i data-lucide="trash-2" style="width: 20px; height: 20px; color: #ef4444;"></i> Topik ujian beserta paket soal, jadwal ujian, dan riwayat terkait berhasil dihapus bersih dari sistem!
                    </div>
                <?php elseif($_GET['msg'] === 'exam_reset'): ?>
                    <div style="background: #fff7ed; border: 1.5px solid #fdba74; color: #9a3412; padding: 14px 20px; border-radius: 14px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px; font-weight: 700; box-shadow: 0 4px 15px rgba(234, 88, 12, 0.08);">
                        <i data-lucide="check-circle" style="width: 22px; height: 22px; color: #ea580c; flex-shrink: 0;"></i>
                        <div>
                            <span>Hasil & simulasi pengerjaan siswa pada topik ini berhasil direset (<?php echo (int)($_GET['count'] ?? 0); ?> pengerjaan dibersihkan). Siswa dapat mengerjakan ujian kembali dari awal.</span>
                            <div style="font-size: 0.78rem; color: #166534; font-weight: 800; margin-top: 3px;">
                                🛡️ BANK SOAL 100% AMAN: Seluruh butir soal di Bank Soal tidak dihapus dan tetap utuh!
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Elite Page Header -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-badge">MASTER TOPIK UJIAN</div>
                        <h1><i data-lucide="layers"></i> Topik Ujian & Agenda Asesmen</h1>
                        <p>Kelola topik ujian madrasah (SAS, STS, Ujian Madrasah, Try Out, dll), tentukan tanggal mulai & selesai pelaksanaan, lalu hubungkan dengan jadwal ujian.</p>
                    </div>
                    <div class="header-actions-premium" style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <button type="button" onclick="openModalTambahTopik()" class="e-btn e-btn-glass" style="background: #ffffff; color: #0284c7; font-weight: 800; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                            <i data-lucide="plus-circle"></i> + Tambah Topik Ujian Baru
                        </button>
                        <a href="<?php echo base_url('cbt/jadwal'); ?>" class="e-btn e-btn-blue-light" style="font-weight: 700;">
                            <i data-lucide="calendar"></i> Master Jadwal Ujian
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Bar -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                <div style="background: #ffffff; padding: 1.25rem 1.5rem; border-radius: 14px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i data-lucide="layers" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.72rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Total Topik Ujian</div>
                        <div style="font-size: 1.5rem; font-weight: 900; color: #0f172a; line-height: 1.2;"><?php echo count($topikList); ?></div>
                    </div>
                </div>

                <div style="background: #ffffff; padding: 1.25rem 1.5rem; border-radius: 14px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i data-lucide="check-circle-2" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.72rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Topik Aktif</div>
                        <div style="font-size: 1.5rem; font-weight: 900; color: #0f172a; line-height: 1.2;">
                            <?php 
                            $cntAktif = count(array_filter($topikList, fn($t) => ($t['status'] ?? 'Aktif') === 'Aktif'));
                            echo $cntAktif;
                            ?>
                        </div>
                    </div>
                </div>

                <div style="background: #ffffff; padding: 1.25rem 1.5rem; border-radius: 14px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #f5f3ff; color: #7c3aed; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i data-lucide="calendar-check" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.72rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Total Sesi Terjadwal</div>
                        <div style="font-size: 1.5rem; font-weight: 900; color: #0f172a; line-height: 1.2;">
                            <?php 
                            $totalSesi = array_sum(array_column($topikList, 'jml_jadwal'));
                            echo $totalSesi;
                            ?> Sesi
                        </div>
                    </div>
                </div>

                <div style="background: #ffffff; padding: 1.25rem 1.5rem; border-radius: 14px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #fff7ed; color: #ea580c; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i data-lucide="graduation-cap" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.72rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Tahun Ajaran</div>
                        <div style="font-size: 1.3rem; font-weight: 900; color: #0f172a; line-height: 1.2;">2026/2027</div>
                    </div>
                </div>
            </div>

            <!-- Toolbar & Filter -->
            <div style="background: #ffffff; padding: 1rem 1.5rem; border-radius: 14px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <form method="GET" action="<?php echo base_url('cbt/topik'); ?>" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center; flex: 1;">
                    <div style="position: relative; flex: 1; min-width: 220px; max-width: 380px;">
                        <i data-lucide="search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #94a3b8;"></i>
                        <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari nama topik ujian..." style="width: 100%; padding: 8px 12px 8px 36px; border-radius: 10px; border: 1.5px solid #e2e8f0; font-size: 0.85rem; outline: none; transition: 0.2s;">
                    </div>

                    <select name="jenis" onchange="this.form.submit()" style="padding: 8px 14px; border-radius: 10px; border: 1.5px solid #e2e8f0; font-size: 0.85rem; font-weight: 700; color: #334155; outline: none;">
                        <option value="">-- Semua Jenis Ujian --</option>
                        <option value="SAS" <?php echo ($filterJenis === 'SAS') ? 'selected' : ''; ?>>SAS (Sumatif Akhir Semester)</option>
                        <option value="STS" <?php echo ($filterJenis === 'STS') ? 'selected' : ''; ?>>STS (Sumatif Tengah Semester)</option>
                        <option value="UM" <?php echo ($filterJenis === 'UM') ? 'selected' : ''; ?>>UM (Ujian Madrasah)</option>
                        <option value="Try Out" <?php echo ($filterJenis === 'Try Out') ? 'selected' : ''; ?>>Try Out Ujian</option>
                        <option value="PH" <?php echo ($filterJenis === 'PH') ? 'selected' : ''; ?>>PH (Penilaian Harian)</option>
                    </select>

                    <button type="submit" class="e-btn" style="background: #0ea5e9; color: #fff; font-weight: 700; border: none; padding: 8px 16px; border-radius: 10px; font-size: 0.85rem;">
                        Filter
                    </button>
                    <?php if (!empty($search) || !empty($filterJenis)): ?>
                        <a href="<?php echo base_url('cbt/topik'); ?>" style="font-size: 0.82rem; font-weight: 700; color: #ef4444; text-decoration: none; padding: 8px 12px;">Reset</a>
                    <?php endif; ?>
                </form>

                <button type="button" onclick="openModalTambahTopik()" class="e-btn" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: #fff; font-weight: 700; border: none; padding: 9px 18px; border-radius: 10px; font-size: 0.85rem; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.25);">
                    <i data-lucide="plus" style="width: 16px; height: 16px;"></i> Buat Topik Ujian
                </button>
            </div>

            <!-- Table of Topics -->
            <div class="card" style="border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden; background: #ffffff; box-shadow: 0 10px 30px rgba(0,0,0,0.02);">
                <div style="padding: 1.25rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div style="font-weight: 800; font-size: 0.95rem; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                        <span>Daftar Topik Ujian & Agenda Asesmen</span>
                        <span style="font-size: 0.75rem; background: #e0f2fe; color: #0284c7; padding: 2px 8px; border-radius: 100px; font-weight: 800;">
                            <?php echo count($filteredList); ?> Topik
                        </span>
                    </div>
                    <div style="font-size: 0.78rem; color: #64748b; font-weight: 600;">
                        Pilih tombol <strong>"Atur Jadwal"</strong> pada topik untuk menyusun sesi pelaksanaan ujian.
                    </div>
                </div>

                <div class="z-table-wrap">
                    <table class="z-table">
                        <thead>
                            <tr>
                                <th style="width: 45px; text-align: center;">No</th>
                                <th style="width: 90px; text-align: center;">Jenis</th>
                                <th>Nama Topik Ujian</th>
                                <th style="width: 240px;">Periode / Mulai Ujian</th>
                                <th style="width: 140px; text-align: center;">Semester & TA</th>
                                <th style="width: 130px; text-align: center;">Sesi Terjadwal</th>
                                <th style="width: 110px; text-align: center;">Status</th>
                                <th style="text-align: right; width: 230px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($filteredList)): ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 3.5rem 1rem;">
                                        <div style="width: 54px; height: 54px; border-radius: 14px; background: #f1f5f9; color: #94a3b8; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                                            <i data-lucide="layers" style="width: 28px; height: 28px;"></i>
                                        </div>
                                        <div style="font-size: 1rem; font-weight: 800; color: #1e293b; margin-bottom: 4px;">Belum Ada Topik Ujian</div>
                                        <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 1.25rem;">Buat topik ujian baru seperti SAS, STS, atau Ujian Madrasah untuk mulai menjadwalkan CBT.</div>
                                        <button type="button" onclick="openModalTambahTopik()" class="e-btn" style="background: #0ea5e9; color: #fff; font-weight: 700; border: none; padding: 8px 18px; border-radius: 10px; font-size: 0.85rem;">
                                            + Buat Topik Ujian Pertama
                                        </button>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php 
                                $no = 1;
                                foreach ($filteredList as $row): 
                                    $jenis = strtoupper($row['jenis'] ?? 'SAS');
                                    
                                    // Badge styling
                                    $badgeBg = '#e0f2fe'; $badgeColor = '#0284c7'; $badgeBorder = '#bae6fd';
                                    if ($jenis === 'SAS') {
                                        $badgeBg = '#ecfdf5'; $badgeColor = '#059669'; $badgeBorder = '#a7f3d0';
                                    } elseif ($jenis === 'STS') {
                                        $badgeBg = '#e0f2fe'; $badgeColor = '#0284c7'; $badgeBorder = '#bae6fd';
                                    } elseif ($jenis === 'UM' || strpos($jenis, 'MADRASAH') !== false) {
                                        $badgeBg = '#f5f3ff'; $badgeColor = '#7c3aed'; $badgeBorder = '#ddd6fe';
                                    } elseif ($jenis === 'TRY OUT') {
                                        $badgeBg = '#fff7ed'; $badgeColor = '#ea580c'; $badgeBorder = '#fed7aa';
                                    }

                                    $hasDate = !empty($row['tgl_mulai']);
                                    $tglMulaiStr = $row['tgl_mulai'] ? formatTanggalIndo($row['tgl_mulai']) : 'Belum Ditentukan';
                                    $tglSelesaiStr = $row['tgl_selesai'] ? formatTanggalIndo($row['tgl_selesai']) : '-';
                                    
                                    $statusRow = $row['status'] ?? 'Aktif';
                                    $statusBadgeClass = 'background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;';
                                    if ($statusRow === 'Selesai') {
                                        $statusBadgeClass = 'background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;';
                                    } elseif ($statusRow === 'Terjadwal') {
                                        $statusBadgeClass = 'background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;';
                                    }
                                ?>
                                    <tr>
                                        <td style="text-align: center; font-weight: 700; color: #94a3b8;"><?php echo $no++; ?></td>
                                        
                                        <!-- Jenis Badge -->
                                        <td style="text-align: center;">
                                            <span style="display: inline-block; padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; background: <?php echo $badgeBg; ?>; color: <?php echo $badgeColor; ?>; border: 1px solid <?php echo $badgeBorder; ?>;">
                                                <?php echo htmlspecialchars($jenis); ?>
                                            </span>
                                        </td>

                                        <!-- Nama Topik & Keterangan -->
                                        <td>
                                            <div style="font-weight: 800; font-size: 0.92rem; color: #0f172a; margin-bottom: 2px;">
                                                <?php echo htmlspecialchars($row['nama_topik']); ?>
                                            </div>
                                            <?php if (!empty($row['keterangan'])): ?>
                                                <div style="font-size: 0.75rem; color: #64748b; line-height: 1.3;">
                                                    <?php echo htmlspecialchars($row['keterangan']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Periode & Mulai Ujian -->
                                        <td>
                                            <?php if ($hasDate): ?>
                                                <div style="display: flex; align-items: center; gap: 6px; font-weight: 800; font-size: 0.84rem; color: #0f172a; margin-bottom: 2px;">
                                                    <i data-lucide="calendar" style="width: 14px; height: 14px; color: #0ea5e9;"></i>
                                                    <span>Mulai: <?php echo $tglMulaiStr; ?></span>
                                                </div>
                                                <div style="font-size: 0.72rem; color: #64748b; padding-left: 20px;">
                                                    Sampai: <?php echo $tglSelesaiStr; ?>
                                                </div>
                                            <?php else: ?>
                                                <span style="font-size: 0.78rem; color: #94a3b8; font-style: italic;">
                                                    <i data-lucide="clock" style="width: 12px; height: 12px; display: inline-block; vertical-align: middle;"></i> Belum diatur tanggalnya
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Semester & TA -->
                                        <td style="text-align: center;">
                                            <div style="font-weight: 800; font-size: 0.82rem; color: #1e293b;">
                                                <?php echo htmlspecialchars($row['semester'] ?? 'Ganjil'); ?>
                                            </div>
                                            <div style="font-size: 0.72rem; color: #64748b;">
                                                <?php echo htmlspecialchars($row['tahun_ajaran'] ?? '2026/2027'); ?>
                                            </div>
                                        </td>

                                        <!-- Sesi Terjadwal -->
                                        <td style="text-align: center;">
                                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 800; background: <?php echo ($row['jml_jadwal'] > 0) ? '#f0fdf4' : '#f8fafc'; ?>; color: <?php echo ($row['jml_jadwal'] > 0) ? '#166534' : '#94a3b8'; ?>; border: 1px solid <?php echo ($row['jml_jadwal'] > 0) ? '#bbf7d0' : '#e2e8f0'; ?>;">
                                                <i data-lucide="calendar-check" style="width: 12px; height: 12px;"></i>
                                                <?php echo $row['jml_jadwal']; ?> Sesi
                                            </span>
                                        </td>

                                        <!-- Status -->
                                        <td style="text-align: center;">
                                            <span style="display: inline-block; padding: 3px 8px; border-radius: 6px; font-size: 0.72rem; font-weight: 800; <?php echo $statusBadgeClass; ?>">
                                                <?php echo htmlspecialchars($statusRow); ?>
                                            </span>
                                        </td>

                                        <!-- Aksi -->
                                        <td style="text-align: right;">
                                            <div style="display: inline-flex; align-items: center; gap: 6px;">
                                                <!-- Tombol Atur Jadwal (Utama) -->
                                                <a href="<?php echo base_url('cbt/jadwal?topik_id=' . $row['id']); ?>" class="e-btn" style="background: #0ea5e9; color: #fff; font-size: 0.78rem; font-weight: 800; padding: 6px 12px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 2px 6px rgba(14, 165, 233, 0.25);" title="Buka dan Atur Jadwal Ujian Topik Ini">
                                                    <i data-lucide="calendar" style="width: 13px; height: 13px;"></i> Atur Jadwal
                                                </a>

                                                <!-- Tombol Reset Ujian Siswa (Untuk Simulasi Ulang, Bank Soal Tetap Aman) -->
                                                <button type="button" onclick="openModalResetTopik(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars(addslashes($row['nama_topik'])); ?>')" class="e-btn" style="background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa; padding: 6px 10px; border-radius: 8px; font-size: 0.78rem; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;" title="Reset Hasil & Simulasi Ujian Siswa (Bank Soal Tetap Aman)">
                                                    <i data-lucide="rotate-ccw" style="width: 13px; height: 13px;"></i> Reset
                                                </button>

                                                <!-- Edit Topik -->
                                                <button type="button" onclick='openModalEditTopik(<?php echo json_encode($row); ?>)' class="e-btn" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 6px 9px; border-radius: 8px; font-size: 0.78rem; cursor: pointer;" title="Edit Topik">
                                                    <i data-lucide="edit-3" style="width: 13px; height: 13px;"></i>
                                                </button>

                                                <!-- Hapus Topik -->
                                                <button type="button" onclick="konfirmasiHapusTopik(<?php echo (int)$row['id']; ?>, '<?php echo htmlspecialchars(addslashes($row['nama_topik'])); ?>')" class="e-btn" style="background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; padding: 6px 9px; border-radius: 8px; font-size: 0.78rem; cursor: pointer;" title="Hapus Topik Ujian">
                                                    <i data-lucide="trash-2" style="width: 13px; height: 13px;"></i>
                                                </button>
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
    </div>
</div>

<!-- Modal Tambah / Edit Topik Ujian -->
<div id="modalTopik" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:1.5rem;">
    <div style="background:#fff; width:100%; max-width:540px; border-radius:18px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); overflow:hidden; border:1px solid #e2e8f0; animation: modalPop 0.2s ease-out;">
        
        <!-- Header Modal -->
        <div style="padding:1.25rem 1.5rem; background:linear-gradient(135deg, #0ea5e9, #0284c7); color:#fff; display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:36px; height:36px; border-radius:10px; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center;">
                    <i data-lucide="layers" style="width:20px; height:20px;"></i>
                </div>
                <div>
                    <h3 id="modalTopikTitle" style="margin:0; font-size:1.05rem; font-weight:800;">Tambah Topik Ujian Baru</h3>
                    <p style="margin:0; font-size:0.75rem; color:rgba(255,255,255,0.85);">Tentukan nama agenda ujian dan tanggal pelaksanaan</p>
                </div>
            </div>
            <button type="button" onclick="closeModalTopik()" style="background:none; border:none; color:#fff; cursor:pointer; padding:4px;">
                <i data-lucide="x" style="width:20px; height:20px;"></i>
            </button>
        </div>

        <!-- Form Body -->
        <form method="POST" action="<?php echo base_url('cbt/topik/save'); ?>" style="padding:1.5rem;">
            <input type="hidden" name="id" id="topikFormId" value="0">
            <input type="hidden" name="redirect_to" value="topik">

            <!-- Jenis Ujian -->
            <div style="margin-bottom: 1.15rem;">
                <label style="display:block; font-size:0.8rem; font-weight:800; color:#334155; margin-bottom:6px;">
                    Jenis / Kategori Ujian <span style="color:#ef4444;">*</span>
                </label>
                <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:8px;">
                    <button type="button" onclick="setJenisPill('SAS')" class="btn-jenis-pill" data-jenis="SAS" style="padding:4px 10px; border-radius:8px; font-size:0.75rem; font-weight:800; border:1px solid #cbd5e1; background:#f8fafc; cursor:pointer;">SAS</button>
                    <button type="button" onclick="setJenisPill('STS')" class="btn-jenis-pill" data-jenis="STS" style="padding:4px 10px; border-radius:8px; font-size:0.75rem; font-weight:800; border:1px solid #cbd5e1; background:#f8fafc; cursor:pointer;">STS</button>
                    <button type="button" onclick="setJenisPill('UM')" class="btn-jenis-pill" data-jenis="UM" style="padding:4px 10px; border-radius:8px; font-size:0.75rem; font-weight:800; border:1px solid #cbd5e1; background:#f8fafc; cursor:pointer;">Ujian Madrasah (UM)</button>
                    <button type="button" onclick="setJenisPill('Try Out')" class="btn-jenis-pill" data-jenis="Try Out" style="padding:4px 10px; border-radius:8px; font-size:0.75rem; font-weight:800; border:1px solid #cbd5e1; background:#f8fafc; cursor:pointer;">Try Out</button>
                    <button type="button" onclick="setJenisPill('PH')" class="btn-jenis-pill" data-jenis="PH" style="padding:4px 10px; border-radius:8px; font-size:0.75rem; font-weight:800; border:1px solid #cbd5e1; background:#f8fafc; cursor:pointer;">PH</button>
                </div>
                <input type="text" name="jenis" id="topikFormJenis" required placeholder="Contoh: SAS, STS, UM, Try Out..." style="width:100%; padding:9px 12px; border-radius:10px; border:1.5px solid #cbd5e1; font-size:0.88rem; font-weight:700; color:#0f172a; outline:none;">
            </div>

            <!-- Nama Topik Ujian -->
            <div style="margin-bottom: 1.15rem;">
                <label style="display:block; font-size:0.8rem; font-weight:800; color:#334155; margin-bottom:6px;">
                    Nama Lengkap Topik Ujian <span style="color:#ef4444;">*</span>
                </label>
                <input type="text" name="nama_topik" id="topikFormNama" required placeholder="Misal: Sumatif Akhir Semester (SAS) Ganjil 2026/2027" style="width:100%; padding:9px 12px; border-radius:10px; border:1.5px solid #cbd5e1; font-size:0.88rem; font-weight:700; color:#0f172a; outline:none;">
            </div>

            <!-- Periode: Tanggal Mulai & Selesai (PENTING) -->
            <div style="background:#f8fafc; padding:12px 14px; border-radius:12px; border:1px solid #e2e8f0; margin-bottom:1.15rem;">
                <div style="font-size:0.75rem; font-weight:800; color:#0284c7; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px; display:flex; align-items:center; gap:6px;">
                    <i data-lucide="calendar" style="width:14px; height:14px;"></i> Periode Pelaksanaan (Mulai Kapan Ujiannya)
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                    <div>
                        <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">
                            Tanggal Mulai Ujian:
                        </label>
                        <input type="date" name="tgl_mulai" id="topikFormTglMulai" style="width:100%; padding:8px 10px; border-radius:8px; border:1.5px solid #cbd5e1; font-size:0.85rem; font-weight:700; color:#0f172a; outline:none;">
                    </div>
                    <div>
                        <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">
                            Tanggal Selesai Ujian:
                        </label>
                        <input type="date" name="tgl_selesai" id="topikFormTglSelesai" style="width:100%; padding:8px 10px; border-radius:8px; border:1.5px solid #cbd5e1; font-size:0.85rem; font-weight:700; color:#0f172a; outline:none;">
                    </div>
                </div>
            </div>

            <!-- Tahun Ajaran & Semester & Status -->
            <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:10px; margin-bottom:1.15rem;">
                <div>
                    <label style="display:block; font-size:0.78rem; font-weight:800; color:#334155; margin-bottom:4px;">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" id="topikFormTahun" value="2026/2027" style="width:100%; padding:8px 10px; border-radius:8px; border:1.5px solid #cbd5e1; font-size:0.82rem; font-weight:700; color:#0f172a; outline:none;">
                </div>
                <div>
                    <label style="display:block; font-size:0.78rem; font-weight:800; color:#334155; margin-bottom:4px;">Semester</label>
                    <select name="semester" id="topikFormSemester" style="width:100%; padding:8px 10px; border-radius:8px; border:1.5px solid #cbd5e1; font-size:0.82rem; font-weight:700; color:#0f172a; outline:none;">
                        <option value="Ganjil">Ganjil</option>
                        <option value="Genap">Genap</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:0.78rem; font-weight:800; color:#334155; margin-bottom:4px;">Status</label>
                    <select name="status" id="topikFormStatus" style="width:100%; padding:8px 10px; border-radius:8px; border:1.5px solid #cbd5e1; font-size:0.82rem; font-weight:700; color:#0f172a; outline:none;">
                        <option value="Aktif">Aktif</option>
                        <option value="Terjadwal">Terjadwal</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Arsip">Arsip</option>
                    </select>
                </div>
            </div>

            <!-- Keterangan -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display:block; font-size:0.78rem; font-weight:800; color:#334155; margin-bottom:4px;">Keterangan / Catatan Tambahan</label>
                <textarea name="keterangan" id="topikFormKeterangan" rows="2" placeholder="Catatan opsional mengenai petunjuk pelaksanaan asesmen..." style="width:100%; padding:8px 12px; border-radius:8px; border:1.5px solid #cbd5e1; font-size:0.82rem; color:#0f172a; outline:none; resize:vertical;"></textarea>
            </div>

            <!-- Action Buttons -->
            <div style="display:flex; justify-content:flex-end; gap:8px;">
                <button type="button" onclick="closeModalTopik()" style="background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; padding:9px 16px; border-radius:10px; font-weight:700; font-size:0.85rem; cursor:pointer;">
                    Batal
                </button>
                <button type="submit" style="background:#0ea5e9; color:#fff; border:none; padding:9px 20px; border-radius:10px; font-weight:800; font-size:0.85rem; cursor:pointer; box-shadow:0 4px 12px rgba(14,165,233,0.3);">
                    Simpan Topik Ujian
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Reset Ujian Topik -->
<div id="modalResetTopik" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(15,23,42,0.6); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(4px);">
    <div style="background:#ffffff; width:92%; max-width:480px; border-radius:20px; padding:1.75rem; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); border:1px solid #e2e8f0; animation:modalPop 0.2s ease-out;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:#9a3412; display:flex; align-items:center; gap:8px;">
                <i data-lucide="rotate-ccw" style="width:22px; height:22px; color:#ea580c;"></i>
                Reset Simulasi Ujian Topik
            </h3>
            <button type="button" onclick="closeModalResetTopik()" style="background:none; border:none; cursor:pointer; color:#94a3b8; font-size:1.2rem; line-height:1;">
                &times;
            </button>
        </div>

        <form method="POST" action="<?php echo base_url('cbt/topik/reset'); ?>">
            <input type="hidden" name="id" id="resetTopikId" value="">
            <input type="hidden" name="redirect_to" value="topik">

            <div style="background:#fff7ed; border:1px solid #fed7aa; border-radius:12px; padding:14px; margin-bottom:1.25rem;">
                <div style="font-size:0.75rem; font-weight:800; color:#c2410c; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px;">Topik yang Akan Direset:</div>
                <div id="resetTopikNama" style="font-size:1.05rem; font-weight:900; color:#9a3412;">-</div>
            </div>

            <p style="font-size:0.85rem; color:#475569; line-height:1.6; margin-bottom:1rem;">
                Tindakan ini akan <strong>membersihkan seluruh riwayat pengerjaan siswa, jawaban, perolehan nilai, dan status selesai</strong> pada ujian-ujian di topik ini, sehingga Anda atau siswa dapat melakukan simulasi pengerjaan ulang dari awal.
            </p>

            <div style="background:#f0fdf4; border:1.5px solid #86efac; border-radius:10px; padding:10px 14px; margin-bottom:1.5rem; font-size:0.82rem; color:#166534; display:flex; align-items:center; gap:10px;">
                <i data-lucide="shield-check" style="width:22px; height:22px; color:#16a34a; flex-shrink:0;"></i>
                <span><strong>Bank Soal 100% Aman:</strong> Seluruh butir soal, paket soal, dan kunci jawaban di Bank Soal <u>tidak akan dihapus</u>!</span>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="closeModalResetTopik()" style="background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; padding:9px 18px; border-radius:10px; font-weight:700; font-size:0.85rem; cursor:pointer;">
                    Batal
                </button>
                <button type="submit" style="background:linear-gradient(135deg, #ea580c 0%, #c2410c 100%); color:#fff; border:none; padding:9px 20px; border-radius:10px; font-weight:800; font-size:0.85rem; cursor:pointer; display:flex; align-items:center; gap:6px; box-shadow:0 4px 12px rgba(234,88,12,0.25);">
                    <i data-lucide="rotate-ccw" style="width:14px; height:14px;"></i> Ya, Reset Hasil Ujian
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalResetTopik(id, nama) {
        document.getElementById('resetTopikId').value = id;
        document.getElementById('resetTopikNama').innerText = nama;
        document.getElementById('modalResetTopik').style.display = 'flex';
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function closeModalResetTopik() {
        document.getElementById('modalResetTopik').style.display = 'none';
    }
    function openModalTambahTopik() {
        document.getElementById('modalTopikTitle').innerText = 'Tambah Topik Ujian Baru';
        document.getElementById('topikFormId').value = '0';
        document.getElementById('topikFormJenis').value = 'SAS';
        document.getElementById('topikFormNama').value = '';
        document.getElementById('topikFormTglMulai').value = '';
        document.getElementById('topikFormTglSelesai').value = '';
        document.getElementById('topikFormTahun').value = '2026/2027';
        document.getElementById('topikFormSemester').value = 'Ganjil';
        document.getElementById('topikFormStatus').value = 'Aktif';
        document.getElementById('topikFormKeterangan').value = '';
        highlightJenisPill('SAS');
        document.getElementById('modalTopik').style.display = 'flex';
    }

    function openModalEditTopik(data) {
        document.getElementById('modalTopikTitle').innerText = 'Edit Topik Ujian: ' + (data.nama_topik || '');
        document.getElementById('topikFormId').value = data.id || '0';
        document.getElementById('topikFormJenis').value = data.jenis || 'SAS';
        document.getElementById('topikFormNama').value = data.nama_topik || '';
        document.getElementById('topikFormTglMulai').value = data.tgl_mulai || '';
        document.getElementById('topikFormTglSelesai').value = data.tgl_selesai || '';
        document.getElementById('topikFormTahun').value = data.tahun_ajaran || '2026/2027';
        document.getElementById('topikFormSemester').value = data.semester || 'Ganjil';
        document.getElementById('topikFormStatus').value = data.status || 'Aktif';
        document.getElementById('topikFormKeterangan').value = data.keterangan || '';
        highlightJenisPill(data.jenis);
        document.getElementById('modalTopik').style.display = 'flex';
    }

    function closeModalTopik() {
        document.getElementById('modalTopik').style.display = 'none';
    }

    function setJenisPill(jenis) {
        document.getElementById('topikFormJenis').value = jenis;
        highlightJenisPill(jenis);

        // Auto suggestion nama topik jika masih kosong
        const namaInput = document.getElementById('topikFormNama');
        if (!namaInput.value || namaInput.value.startsWith('Sumatif') || namaInput.value.startsWith('Ujian')) {
            const ta = document.getElementById('topikFormTahun').value || '2026/2027';
            const sem = document.getElementById('topikFormSemester').value || 'Ganjil';
            if (jenis === 'SAS') {
                namaInput.value = `Sumatif Akhir Semester (SAS) ${sem} ${ta}`;
            } else if (jenis === 'STS') {
                namaInput.value = `Sumatif Tengah Semester (STS) ${sem} ${ta}`;
            } else if (jenis === 'UM') {
                namaInput.value = `Ujian Madrasah (UM) ${ta}`;
            } else if (jenis === 'Try Out') {
                namaInput.value = `Try Out Ujian Madrasah ${ta}`;
            }
        }
    }

    function highlightJenisPill(jenis) {
        document.querySelectorAll('.btn-jenis-pill').forEach(btn => {
            if (btn.getAttribute('data-jenis').toUpperCase() === (jenis || '').toUpperCase()) {
                btn.style.background = '#0ea5e9';
                btn.style.color = '#ffffff';
                btn.style.borderColor = '#0284c7';
            } else {
                btn.style.background = '#f8fafc';
                btn.style.color = '#334155';
                btn.style.borderColor = '#cbd5e1';
            }
        });
    }

    // Hapus Topik dengan SweetAlert2
    function konfirmasiHapusTopik(id, nama) {
        const doDelete = () => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo base_url('cbt/topik/delete'); ?>';
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'id';
            input.value = id;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus Topik Ujian?',
                html: `Apakah Anda yakin ingin menghapus topik <b>"${nama}"</b>?<br><span style="font-size:0.8rem;color:#ef4444;">Paket ujian, jadwal matriks, dan absensi terkait topik ini akan ikut dibersihkan.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus Topik!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) doDelete();
            });
        } else {
            if (confirm(`Apakah Anda yakin ingin menghapus topik ujian "${nama}"? Paket ujian dan sesi jadwal terkait juga akan dibersihkan.`)) {
                doDelete();
            }
        }
    }

    // Close on outside click
    window.onclick = function(event) {
        const m = document.getElementById('modalTopik');
        if (event.target === m) closeModalTopik();
        const mReset = document.getElementById('modalResetTopik');
        if (event.target === mReset) closeModalResetTopik();
    }
</script>
