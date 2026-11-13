<?php use App\Core\Helper; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title = 'Kelayakan Peserta PAS'; include __DIR__ . '/../admin/_zamrud_head.php'; ?>
</head>
<body>
<?php $page_badge = 'Kurikulum'; ?>
<div class="z-wrap">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../siakad/_siakad_header.php'; ?>

<div class="modern-page-header" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); padding: 24px; border-radius: 18px; margin-bottom: 25px; box-shadow: 0 4px 15px rgba(2, 132, 199, 0.2);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; width: 100%;">
        <div>
            <h1 class="mph-title" style="color: white; font-size: 1.6rem; font-weight: 800; margin: 0 0 6px 0; display: flex; align-items: center; gap: 10px;">
                <i data-lucide="shield-check" style="color: #bae6fd; width: 28px; height: 28px;"></i> Kelayakan Peserta PAS
            </h1>
            <p class="mph-subtitle" style="color: rgba(255,255,255,0.9); font-size: 0.92rem; margin: 0;">
                Syarat Mutlak: Siswa wajib <strong>100% lengkap seluruh nilai PH + PTS</strong>. Jika ada 1 nilai kosong, siswa tidak diperbolehkan mengikuti PAS.
            </p>
        </div>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <a href="<?= Helper::url('/kurikulum/monitoring-nilai') ?>" class="btn" style="color: white; border: 1px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.12); display: inline-flex; align-items: center; gap: 6px; font-weight: 600; padding: 8px 16px; border-radius: 8px; text-decoration: none;">
                <i data-lucide="bar-chart-2" style="width: 16px; height: 16px;"></i> Progres Guru Mapel
            </a>
            <?php if (!empty($pasData['kelas'])): ?>
            <a href="<?= Helper::url('/kurikulum/syarat-pas/cetak?kelas_id=' . $pasData['kelas']['id']) ?>" target="_blank" class="btn" style="color: white; border: 1px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.2); display: inline-flex; align-items: center; gap: 6px; font-weight: 600; padding: 8px 16px; border-radius: 8px; text-decoration: none;">
                <i data-lucide="printer" style="width: 16px; height: 16px;"></i> Cetak Rekap Kelas
            </a>
            <a href="<?= Helper::url('/kurikulum/syarat-pas/cetak-tagihan?kelas_id=' . $pasData['kelas']['id']) ?>" target="_blank" class="btn" style="color: white; background: #dc2626; border: 1px solid #b91c1c; display: inline-flex; align-items: center; gap: 6px; font-weight: 700; padding: 8px 16px; border-radius: 8px; text-decoration: none; box-shadow: 0 2px 8px rgba(220,38,38,0.3);">
                <i data-lucide="file-warning" style="width: 16px; height: 16px;"></i> Cetak Lembar Tagihan Nilai
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Filter Box -->
<div class="z-card" style="margin-bottom: 25px; padding: 20px;">
    <form id="filterForm" method="GET" action="<?= Helper::url('/kurikulum/syarat-pas') ?>" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 200px;">
            <label style="font-size: 0.8rem; font-weight: 700; color: var(--z-muted); margin-bottom: 8px; display: block;">Pilih Kelas</label>
            <select name="kelas_id" id="filterKelas" onchange="this.form.submit()" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none; font-weight: 600; cursor: pointer;">
                <?php foreach($kelas_list as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= $kelas_id == $k['id'] ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kelas']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="flex: 1; min-width: 200px;">
            <label style="font-size: 0.8rem; font-weight: 700; color: var(--z-muted); margin-bottom: 8px; display: block;">Status Kelayakan</label>
            <select name="status" id="selectStatus" onchange="applyInstantFilter()" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none; font-weight: 500; cursor: pointer;">
                <option value="all" <?= $filter_status == 'all' ? 'selected' : '' ?>>-- Semua Siswa --</option>
                <option value="layak" <?= $filter_status == 'layak' ? 'selected' : '' ?>>✅ Hanya yang Boleh Ikut PAS (100%)</option>
                <option value="tidak_layak" <?= $filter_status == 'tidak_layak' ? 'selected' : '' ?>>⛔ Hanya yang Belum Boleh Ikut PAS (&lt; 100%)</option>
            </select>
        </div>

        <div style="flex: 1.5; min-width: 240px;">
            <label style="font-size: 0.8rem; font-weight: 700; color: var(--z-muted); margin-bottom: 8px; display: block;">Cari Siswa</label>
            <div style="position: relative;">
                <input type="text" name="q" id="searchSiswaInput" value="<?= htmlspecialchars($search) ?>" oninput="applyInstantFilter()" placeholder="Ketik nama atau NIS siswa..." style="width: 100%; padding: 10px 35px 10px 12px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                <i data-lucide="search" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--z-muted); pointer-events: none;"></i>
            </div>
        </div>

        <div>
            <button type="submit" class="btn btn-primary" style="padding: 10px 22px; border-radius: 8px; font-weight: 700; display: flex; align-items: center; gap: 8px; height: 42px;">
                <i data-lucide="refresh-cw" style="width: 16px; height: 16px;"></i> Muat Ulang
            </button>
        </div>
    </form>
</div>

<?php if (!$pasData || empty($pasData['kelas'])): ?>
<div class="z-card" style="text-align: center; padding: 60px 20px;">
    <div style="width: 70px; height: 70px; background: rgba(2, 132, 199, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto;">
        <i data-lucide="layers" style="width: 34px; height: 34px; color: #0284c7;"></i>
    </div>
    <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--z-text); margin: 0 0 10px 0;">Pilih Kelas</h3>
    <p style="font-size: 0.9rem; color: var(--z-muted); margin: 0; max-width: 400px; margin: 0 auto;">Silakan pilih kelas dari formulir filter di atas untuk melihat verifikasi syarat mengikuti PAS.</p>
</div>
<?php else: 
    $kls = $pasData['kelas'];
    $st = $pasData['stat'];
    $display_siswa = $display_siswa ?? ($pasData['siswa_data'] ?? []);
    if (!isset($st['persen_kesiapan_kelas'])) {
        $st['persen_kesiapan_kelas'] = ($st['total_siswa'] > 0) ? round(($st['layak'] / $st['total_siswa']) * 100) : 0;
    }
?>

<!-- Info Ringkas Kelas & Wali Kelas -->
<div class="z-card" style="margin-bottom: 25px; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; border-left: 4px solid #0284c7;">
    <div style="display: flex; align-items: center; gap: 15px;">
        <div style="width: 44px; height: 44px; background: rgba(2, 132, 199, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #0284c7;">
            <i data-lucide="graduation-cap" style="width: 24px; height: 24px;"></i>
        </div>
        <div>
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: var(--z-text);">Kelas <?= htmlspecialchars($kls['nama_kelas']) ?></h3>
            <p style="margin: 2px 0 0 0; font-size: 0.85rem; color: var(--z-muted);">
                Wali Kelas: <strong><?= htmlspecialchars($kls['wali_kelas']) ?></strong> &bull; Semester: <strong><?= htmlspecialchars($semester) ?></strong> &bull; TA: <strong><?= htmlspecialchars($activeYear['name'] ?? $tahun_ajaran_id) ?></strong>
            </p>
        </div>
    </div>
    <div style="display: flex; gap: 10px; align-items: center;">
        <span style="font-size: 0.85rem; color: var(--z-muted); font-weight: 600;">Total Mata Pelajaran:</span>
        <span style="background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-weight: 800; color: #334155; font-size: 0.85rem;">
            <?= count($pasData['mapel_list']) ?> Mapel
        </span>
    </div>
</div>

<!-- 4 Stat Box Kesiapan Kelas -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 25px;">
    <!-- Stat 1: Total Siswa -->
    <div class="z-card" style="padding: 20px; display: flex; align-items: center; gap: 16px; border-radius: 12px;">
        <div style="width: 52px; height: 52px; background: rgba(59, 130, 246, 0.12); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #2563eb; flex-shrink: 0;">
            <i data-lucide="users" style="width: 28px; height: 28px;"></i>
        </div>
        <div>
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--z-muted); text-transform: uppercase; letter-spacing: 0.5px;">Total Siswa</div>
            <div style="font-size: 1.6rem; font-weight: 800; color: var(--z-text); line-height: 1.2;"><?= $st['total_siswa'] ?> <span style="font-size: 0.85rem; font-weight: 500; color: var(--z-muted);">Siswa</span></div>
        </div>
    </div>

    <!-- Stat 2: Layak (100%) -->
    <div class="z-card" style="padding: 20px; display: flex; align-items: center; gap: 16px; border-radius: 12px; border-left: 4px solid #10b981;">
        <div style="width: 52px; height: 52px; background: rgba(16, 185, 129, 0.12); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #059669; flex-shrink: 0;">
            <i data-lucide="check-circle-2" style="width: 28px; height: 28px;"></i>
        </div>
        <div>
            <div style="font-size: 0.8rem; font-weight: 700; color: #059669; text-transform: uppercase; letter-spacing: 0.5px;">Boleh Ikut PAS (100%)</div>
            <div style="font-size: 1.6rem; font-weight: 800; color: #059669; line-height: 1.2;"><?= $st['layak'] ?> <span style="font-size: 0.85rem; font-weight: 600; color: #10b981;">(<?= $st['total_siswa'] > 0 ? round(($st['layak']/$st['total_siswa'])*100) : 0 ?>%)</span></div>
        </div>
    </div>

    <!-- Stat 3: Tertahan (< 100%) -->
    <div class="z-card" style="padding: 20px; display: flex; align-items: center; gap: 16px; border-radius: 12px; border-left: 4px solid #ef4444;">
        <div style="width: 52px; height: 52px; background: rgba(239, 68, 68, 0.12); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #dc2626; flex-shrink: 0;">
            <i data-lucide="shield-alert" style="width: 28px; height: 28px;"></i>
        </div>
        <div>
            <div style="font-size: 0.8rem; font-weight: 700; color: #dc2626; text-transform: uppercase; letter-spacing: 0.5px;">Tertahan / Ada Kosong</div>
            <div style="font-size: 1.6rem; font-weight: 800; color: #dc2626; line-height: 1.2;"><?= $st['tidak_layak'] ?> <span style="font-size: 0.85rem; font-weight: 600; color: #ef4444;">(<?= $st['total_siswa'] > 0 ? round(($st['tidak_layak']/$st['total_siswa'])*100) : 0 ?>%)</span></div>
        </div>
    </div>

    <!-- Stat 4: Kesiapan Kelas -->
    <div class="z-card" style="padding: 20px; border-radius: 12px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <span style="font-size: 0.8rem; font-weight: 700; color: var(--z-muted); text-transform: uppercase;">Kesiapan Kelas</span>
            <span style="font-size: 1.1rem; font-weight: 800; color: #0284c7;"><?= $st['persen_kesiapan_kelas'] ?>%</span>
        </div>
        <div style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
            <div style="width: <?= $st['persen_kesiapan_kelas'] ?>%; height: 100%; background: linear-gradient(90deg, #0284c7, #10b981); transition: width 0.5s ease;"></div>
        </div>
        <div style="font-size: 0.72rem; color: var(--z-muted); margin-top: 6px; text-align: right;">
            <?= $st['layak'] ?> dari <?= $st['total_siswa'] ?> siswa siap mengikuti PAS
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="z-card">
    <div style="padding: 18px 20px; border-bottom: 1px solid var(--z-border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: var(--z-text); display: flex; align-items: center; gap: 8px;">
                <i data-lucide="clipboard-check" style="color: #0284c7; width: 20px; height: 20px;"></i>
                Daftar Verifikasi Kelayakan Peserta PAS
            </h3>
            <span id="counterSiswa" style="background: #f1f5f9; color: #475569; font-size: 0.8rem; font-weight: 700; padding: 3px 8px; border-radius: 6px;">
                <?= count($display_siswa) ?> Siswa Ditampilkan
            </span>
        </div>

        <div style="display: flex; gap: 8px;">
            <button type="button" onclick="filterTableQuick('all')" class="btn btn-sm btn-outline quick-filter active" id="btn-q-all">Semua</button>
            <button type="button" onclick="filterTableQuick('layak')" class="btn btn-sm btn-outline quick-filter" id="btn-q-layak" style="color: #059669; border-color: #a7f3d0;">Boleh Ikut (<?= $st['layak'] ?>)</button>
            <button type="button" onclick="filterTableQuick('tidak_layak')" class="btn btn-sm btn-outline quick-filter" id="btn-q-tidak" style="color: #dc2626; border-color: #fca5a5;">Tertahan (<?= $st['tidak_layak'] ?>)</button>
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="z-table" id="tableKelayakan">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">No</th>
                    <th style="width: 90px; text-align: center;">NIS</th>
                    <th style="min-width: 180px;">Nama Siswa</th>
                    <th style="width: 140px; text-align: center;">Kelengkapan</th>
                    <th style="width: 220px; text-align: center;">Status Syarat PAS</th>
                    <th style="min-width: 250px;">Rincian Tagihan Kosong (Wajib Susulan)</th>
                    <th style="width: 140px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($display_siswa)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 35px; color: var(--z-muted);">
                        <i data-lucide="info" style="width: 24px; height: 24px; display: inline-block; vertical-align: middle; margin-right: 6px; color: #94a3b8;"></i>
                        Tidak ada siswa yang sesuai dengan filter.
                    </td>
                </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($display_siswa as $s): 
                        $statusClass = $s['is_layak'] ? 'row-layak' : 'row-tidak-layak';
                    ?>
                    <tr class="student-row <?= $statusClass ?>" data-status="<?= $s['is_layak'] ? 'layak' : 'tidak_layak' ?>" data-search="<?= htmlspecialchars(strtolower($s['nama'] . ' ' . ($s['nis'] ?? ''))) ?>" style="<?= !$s['is_layak'] ? 'background: #fffcfc;' : '' ?>">
                        <td style="text-align: center; font-weight: 600; color: var(--z-muted);"><?= $no++ ?></td>
                        <td style="text-align: center; font-family: monospace; font-weight: 700; color: var(--z-muted);"><?= htmlspecialchars($s['nis']) ?></td>
                        <td>
                            <strong style="color: var(--z-text); font-size: 0.95rem;"><?= htmlspecialchars($s['nama']) ?></strong>
                            <div style="font-size: 0.72rem; color: #94a3b8;"><?= $s['jk'] == 'L' ? 'Laki-laki' : ($s['jk'] == 'P' ? 'Perempuan' : '-') ?></div>
                        </td>
                        <td style="text-align: center;">
                            <div style="font-weight: 800; font-size: 0.95rem; color: <?= $s['is_layak'] ? '#059669' : '#dc2626' ?>;">
                                <?= $s['persen'] ?>%
                            </div>
                            <div style="font-size: 0.72rem; color: var(--z-muted); margin-top: 2px;">
                                <?= $s['total_terisi'] ?> / <?= $s['total_tagihan'] ?> Nilai
                            </div>
                            <div style="width: 80px; height: 4px; background: #e2e8f0; border-radius: 2px; margin: 4px auto 0 auto; overflow: hidden;">
                                <div style="width: <?= $s['persen'] ?>%; height: 100%; background: <?= $s['is_layak'] ? '#10b981' : '#ef4444' ?>;"></div>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <?php if ($s['is_layak']): ?>
                                <span style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 6px 12px; border-radius: 20px; font-weight: 800; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
                                    <i data-lucide="check-check" style="width: 14px; height: 14px;"></i> MEMENUHI SYARAT
                                </span>
                                <div style="font-size: 0.68rem; color: #059669; font-weight: 600; margin-top: 3px;">Diperbolehkan Ikut PAS</div>
                            <?php else: ?>
                                <span style="background: #fef2f2; color: #b91c1c; border: 1px solid #fca5a5; padding: 6px 12px; border-radius: 20px; font-weight: 800; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 1px 2px rgba(220,38,38,0.05);">
                                    <i data-lucide="shield-alert" style="width: 14px; height: 14px;"></i> TIDAK DIPERBOLEHKAN
                                </span>
                                <div style="font-size: 0.68rem; color: #dc2626; font-weight: 700; margin-top: 3px;">Ada <?= $s['total_kosong'] ?? count($s['tagihan_kosong']) ?> Nilai Kosong!</div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($s['is_layak']): ?>
                                <div style="display: flex; align-items: center; gap: 6px; color: #059669; font-size: 0.82rem; font-weight: 600;">
                                    <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i> Seluruh nilai PH & PTS lengkap
                                </div>
                            <?php else: ?>
                                <div style="display: flex; flex-wrap: wrap; gap: 5px; align-items: center;">
                                    <?php 
                                    $kosong = $s['tagihan_kosong'];
                                    $showMax = 3;
                                    $countKosong = count($kosong);
                                    foreach(array_slice($kosong, 0, $showMax) as $k): ?>
                                        <span style="background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; font-size: 0.72rem; font-weight: 700; padding: 3px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;" title="Guru: <?= htmlspecialchars($k['guru']) ?>">
                                            <i data-lucide="x" style="width: 10px; height: 10px;"></i>
                                            <?= htmlspecialchars($k['mapel']) ?> (<?= htmlspecialchars($k['jenis'] ?? $k['eval'] ?? '') ?>)
                                        </span>
                                    <?php endforeach; ?>

                                    <?php if ($countKosong > $showMax): ?>
                                        <span onclick="showStudentDetail(<?= htmlspecialchars(json_encode($s)) ?>)" style="background: #e2e8f0; color: #334155; font-size: 0.72rem; font-weight: 700; padding: 3px 8px; border-radius: 6px; cursor: pointer;">
                                            +<?= $countKosong - $showMax ?> lainnya...
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                <button type="button" 
                                        onclick="showStudentDetail(<?= htmlspecialchars(json_encode($s)) ?>)" 
                                        class="btn btn-sm btn-outline" 
                                        style="padding: 5px 9px; font-size: 0.75rem; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; font-weight: 600;"
                                        title="Lihat Rincian Per Mapel">
                                    <i data-lucide="eye" style="width: 13px; height: 13px;"></i> Rincian
                                </button>
                                <?php if (!$s['is_layak']): ?>
                                <a href="<?= Helper::url('/kurikulum/syarat-pas/cetak-tagihan?kelas_id=' . $kls['id'] . '&siswa_id=' . $s['id']) ?>" 
                                   target="_blank" 
                                   class="btn btn-sm btn-danger" 
                                   style="background: #dc2626; color: white; padding: 5px 9px; font-size: 0.75rem; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; font-weight: 700;"
                                   title="Cetak Lembar Tagihan Susulan Siswa">
                                    <i data-lucide="printer" style="width: 13px; height: 13px;"></i> Slip
                                </a>
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

<?php endif; ?>

<!-- MODAL DETAIL SISWA -->
<div id="modalDetailSiswa" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); z-index: 9999; backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 14px; width: 100%; max-width: 720px; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);">
        
        <!-- Modal Header -->
        <div style="background: linear-gradient(135deg, #0284c7, #0369a1); color: white; padding: 18px 22px; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="user" style="color: white; width: 22px; height: 22px;"></i>
                </div>
                <div>
                    <h3 id="modalSiswaNama" style="margin: 0; font-size: 1.15rem; font-weight: 800; color: white;">Nama Siswa</h3>
                    <p id="modalSiswaNis" style="margin: 2px 0 0 0; font-size: 0.8rem; color: #bae6fd;">NIS: -</p>
                </div>
            </div>
            <button type="button" onclick="closeStudentDetail()" style="background: rgba(255,255,255,0.15); border: none; color: white; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                <i data-lucide="x" style="width: 18px; height: 18px;"></i>
            </button>
        </div>

        <!-- Modal Status Banner -->
        <div id="modalStatusBanner" style="padding: 12px 22px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <span id="modalStatusText" style="font-weight: 800; font-size: 0.88rem;">Status</span>
            </div>
            <div id="modalPersenText" style="font-weight: 800; font-size: 1rem;">100%</div>
        </div>

        <!-- Modal Body Content -->
        <div style="padding: 20px 22px; overflow-y: auto; flex: 1;" class="z-scroll">
            <h4 style="margin: 0 0 12px 0; font-size: 0.95rem; font-weight: 800; color: var(--z-text); display: flex; align-items: center; gap: 6px;">
                <i data-lucide="list-checks" style="width: 18px; height: 18px; color: #0284c7;"></i> Rincian Kelengkapan Per Mata Pelajaran
            </h4>
            
            <table class="z-table" style="font-size: 0.85rem; width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">No</th>
                        <th>Mata Pelajaran & Guru</th>
                        <th style="text-align: center;">Komponen Nilai (PH & PTS)</th>
                        <th style="width: 110px; text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody id="modalTableBody">
                    <!-- Populated by JavaScript -->
                </tbody>
            </table>
        </div>

        <!-- Modal Footer -->
        <div style="padding: 14px 22px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <div id="modalPrintBtnWrapper">
                <!-- Cetak slip button -->
            </div>
            <button type="button" onclick="closeStudentDetail()" class="btn btn-outline" style="padding: 8px 18px; font-weight: 600;">
                Tutup
            </button>
        </div>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        lucide.createIcons();
    });

    function applyInstantFilter() {
        const select = document.getElementById('selectStatus');
        const statusVal = select ? select.value : 'all';
        const searchInput = document.getElementById('searchSiswaInput');
        const searchVal = searchInput ? searchInput.value.toLowerCase().trim() : '';

        // Update quick filter buttons active state
        document.querySelectorAll('.quick-filter').forEach(b => b.classList.remove('active'));
        if (statusVal === 'all') {
            const b = document.getElementById('btn-q-all'); if (b) b.classList.add('active');
        } else if (statusVal === 'layak') {
            const b = document.getElementById('btn-q-layak'); if (b) b.classList.add('active');
        } else if (statusVal === 'tidak_layak') {
            const b = document.getElementById('btn-q-tidak'); if (b) b.classList.add('active');
        }

        const rows = document.querySelectorAll('.student-row');
        let visibleCount = 0;

        rows.forEach(r => {
            const rowStatus = r.getAttribute('data-status');
            const rowSearch = (r.getAttribute('data-search') || r.textContent).toLowerCase();

            let matchStatus = (statusVal === 'all' || rowStatus === statusVal);
            let matchSearch = (searchVal === '' || rowSearch.includes(searchVal));

            if (matchStatus && matchSearch) {
                r.style.display = '';
                visibleCount++;
            } else {
                r.style.display = 'none';
            }
        });

        const counter = document.getElementById('counterSiswa');
        if (counter) {
            counter.textContent = visibleCount + ' Siswa Ditampilkan';
        }

        const emptyRow = document.getElementById('rowEmptyFiltered');
        if (emptyRow) {
            emptyRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
        }
    }

    function filterTableQuick(mode) {
        const select = document.getElementById('selectStatus');
        if (select) {
            select.value = mode;
        }
        applyInstantFilter();
    }

    function showStudentDetail(siswa) {
        document.getElementById('modalSiswaNama').textContent = siswa.nama;
        document.getElementById('modalSiswaNis').textContent = 'NIS: ' + (siswa.nis || '-') + ' | Tagihan: ' + siswa.total_terisi + ' / ' + siswa.total_tagihan + ' Nilai';

        const banner = document.getElementById('modalStatusBanner');
        const statusText = document.getElementById('modalStatusText');
        const persenText = document.getElementById('modalPersenText');

        if (siswa.is_layak) {
            banner.style.background = '#ecfdf5';
            banner.style.borderBottom = '1px solid #a7f3d0';
            banner.style.color = '#065f46';
            statusText.innerHTML = '<i data-lucide="check-check" style="width:16px;height:16px;vertical-align:middle;margin-right:5px;"></i> MEMENUHI SYARAT &bull; DIPERBOLEHKAN MENGIKUTI PAS';
            persenText.textContent = siswa.persen + '% (Lengkap)';
            persenText.style.color = '#059669';
        } else {
            banner.style.background = '#fef2f2';
            banner.style.borderBottom = '1px solid #fca5a5';
            banner.style.color = '#991b1b';
            statusText.innerHTML = '<i data-lucide="shield-alert" style="width:16px;height:16px;vertical-align:middle;margin-right:5px;"></i> TIDAK DIPERBOLEHKAN &bull; ' + siswa.total_kosong + ' NILAI KOSONG';
            persenText.textContent = siswa.persen + '% (Belum Lengkap)';
            persenText.style.color = '#dc2626';
        }

        const tbody = document.getElementById('modalTableBody');
        tbody.innerHTML = '';

        if (siswa.rincian_mapel && siswa.rincian_mapel.length > 0) {
            siswa.rincian_mapel.forEach((rm, idx) => {
                let allFilled = true;
                let evalHtml = '<div style="display:flex; flex-wrap:wrap; gap:6px; justify-content:center;">';
                
                rm.evaluasi.forEach(ev => {
                    if (ev.is_filled) {
                        evalHtml += '<span style="background:#ecfdf5; color:#047857; border:1px solid #a7f3d0; padding:2px 7px; border-radius:6px; font-weight:700; font-size:0.75rem;">' + ev.jenis + ': <strong>' + ev.nilai + '</strong></span>';
                    } else {
                        allFilled = false;
                        evalHtml += '<span style="background:#fef2f2; color:#b91c1c; border:1px solid #fca5a5; padding:2px 7px; border-radius:6px; font-weight:800; font-size:0.75rem;">' + ev.jenis + ': KOSONG</span>';
                    }
                });
                evalHtml += '</div>';

                const statusBadge = allFilled 
                    ? '<span style="color:#059669; font-weight:800; font-size:0.75rem;"><i data-lucide="check-circle" style="width:13px;height:13px;vertical-align:middle;"></i> Tuntas</span>'
                    : '<span style="color:#dc2626; font-weight:800; font-size:0.75rem;"><i data-lucide="x-circle" style="width:13px;height:13px;vertical-align:middle;"></i> Ada Kosong</span>';

                const tr = document.createElement('tr');
                if (!allFilled) tr.style.background = '#fffafa';
                tr.innerHTML = `
                    <td style="text-align:center; font-weight:600; color:#64748b;">${idx + 1}</td>
                    <td>
                        <strong style="color:var(--z-text);">${rm.nama_mapel}</strong>
                        <div style="font-size:0.72rem; color:#64748b;">Guru: ${rm.nama_guru}</div>
                    </td>
                    <td>${evalHtml}</td>
                    <td style="text-align:center;">${statusBadge}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        const printWrapper = document.getElementById('modalPrintBtnWrapper');
        if (!siswa.is_layak) {
            printWrapper.innerHTML = `
                <a href="<?= Helper::url('/kurikulum/syarat-pas/cetak-tagihan?kelas_id=' . ($pasData['kelas']['id'] ?? '')) ?>&siswa_id=${siswa.id}" target="_blank" class="btn btn-danger" style="background:#dc2626; color:white; font-size:0.82rem; font-weight:700; padding:8px 14px; border-radius:6px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                    <i data-lucide="printer" style="width:15px;height:15px;"></i> Cetak Lembar Tagihan Siswa Ini
                </a>
            `;
        } else {
            printWrapper.innerHTML = '';
        }

        const modal = document.getElementById('modalDetailSiswa');
        modal.style.display = 'flex';
        lucide.createIcons();
    }

    function closeStudentDetail() {
        document.getElementById('modalDetailSiswa').style.display = 'none';
    }
</script>

    </div>
</div>
<script>
    if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
</body>
</html>
