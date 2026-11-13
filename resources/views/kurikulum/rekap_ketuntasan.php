<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title = 'Rekap Ketuntasan Nilai'; include __DIR__ . '/../admin/_zamrud_head.php'; ?>
</head>
<body>
<?php $page_badge = 'Kurikulum'; ?>
<div class="z-wrap">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../siakad/_siakad_header.php'; ?>

        <div class="modern-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 1.75rem 2rem; border-radius: 18px; margin-bottom: 1.5rem; box-shadow: 0 10px 25px rgba(14, 165, 233, 0.2);">
            <div>
                <h1 class="mph-title" style="color: white; font-size: 1.75rem; font-weight: 800; margin: 0 0 0.4rem 0; display: flex; align-items: center; gap: 12px;">
                    <i data-lucide="activity" style="color: rgba(255,255,255,0.85); width: 28px; height: 28px;"></i> Rekap Ketuntasan Nilai
                </h1>
                <p class="mph-subtitle" style="color: rgba(255,255,255,0.92); margin: 0; font-size: 0.95rem;">
                    Pantau tingkat kelulusan KKM siswa per kelas dan mata pelajaran.
                </p>
            </div>
        </div>

        <div class="z-card" style="margin-bottom: 25px; padding: 20px; background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
            <form method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;" id="formFilterRekap">
                <div style="flex: 1; min-width: 180px;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 8px; display: block;">Guru Pengampu</label>
                    <select name="guru_id" id="filter_guru" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; background: white; color: #1e293b; outline: none;">
                        <option value="">-- Pilih Guru --</option>
                        <?php foreach($guru_list as $g): ?>
                            <option value="<?= $g['id'] ?>" <?= $guru_id == $g['id'] ? 'selected' : '' ?>><?= htmlspecialchars($g['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 1; min-width: 180px;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 8px; display: block;">Kelas</label>
                    <select name="kelas_id" id="filter_kelas" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; background: white; color: #1e293b; outline: none;">
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach($kelas_list as $k): ?>
                            <option value="<?= $k['id'] ?>" <?= $kelas_id == $k['id'] ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kelas']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 1; min-width: 180px;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 8px; display: block;">Mata Pelajaran</label>
                    <select name="mapel_id" id="filter_mapel" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; background: white; color: #1e293b; outline: none;">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        <?php foreach($mapel_list as $m): ?>
                            <option value="<?= $m['id'] ?>" <?= $mapel_id == $m['id'] ? 'selected' : '' ?>><?= htmlspecialchars($m['nama_mapel']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 1; min-width: 180px;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 8px; display: block;">Jenis Evaluasi</label>
                    <select name="jenis_evaluasi" id="filter_jenis_evaluasi" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; background: white; color: #1e293b; outline: none;">
                        <option value="">-- Pilih Jenis Evaluasi --</option>
                        <?php if ($jenis_evaluasi): ?>
                            <option value="<?= htmlspecialchars($jenis_evaluasi) ?>" selected><?= htmlspecialchars($jenis_evaluasi) ?></option>
                        <?php endif; ?>
                    </select>
                </div>
                <div>
                    <button type="submit" class="z-btn z-btn-primary" style="padding: 10px 22px; border-radius: 8px; font-weight: 700; display: flex; align-items: center; gap: 8px; background: #0284c7; color: white; border: none; cursor: pointer; box-shadow: 0 2px 8px rgba(2, 132, 199, 0.3);">
                        <i data-lucide="filter" style="width: 16px; height: 16px;"></i> Tampilkan Data
                    </button>
                </div>
            </form>
        </div>

        <?php if(!$kelas_id || !$mapel_id || !$jenis_evaluasi): ?>
        <div class="z-card" style="text-align: center; padding: 60px 20px; background: white; border-radius: 16px; border: 1px solid #e2e8f0;">
            <div style="width: 70px; height: 70px; background: rgba(14, 165, 233, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto;">
                <i data-lucide="search" style="width: 34px; height: 34px; color: #0284c7;"></i>
            </div>
            <h3 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin: 0 0 10px 0;">Pilih Filter</h3>
            <p style="font-size: 0.9rem; color: #64748b; margin: 0; max-width: 400px; margin: 0 auto;">Silakan lengkapi pilihan filter (Guru, Kelas, Mapel, dan Jenis Evaluasi) untuk melihat rekap ketuntasan nilai.</p>
        </div>
        <?php else: ?>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 25px;">
            <div class="z-card" style="padding: 20px; background: white; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px;">
                <div style="width: 50px; height: 50px; background: #f1f5f9; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #64748b;">
                    <i data-lucide="target" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <div style="font-size: 0.8rem; color: #64748b; font-weight: 700; margin-bottom: 4px; text-transform: uppercase;">STANDAR KKM</div>
                    <div style="font-size: 1.6rem; font-weight: 800; color: #1e293b;"><?= $kkm_nilai !== null ? htmlspecialchars($kkm_nilai) : '<span style="font-size: 1rem; color: #dc2626;">Belum Diatur</span>' ?></div>
                </div>
            </div>
            
            <div class="z-card" style="padding: 20px; background: white; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px;">
                <div style="width: 50px; height: 50px; background: #f0fdf4; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #16a34a;">
                    <i data-lucide="check-circle-2" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <div style="font-size: 0.8rem; color: #64748b; font-weight: 700; margin-bottom: 4px; text-transform: uppercase;">SISWA TUNTAS</div>
                    <div style="font-size: 1.6rem; font-weight: 800; color: #16a34a;"><?= $summary['tuntas'] ?> <span style="font-size: 0.95rem; color: #94a3b8; font-weight: 600;">/ <?= $summary['total'] ?></span></div>
                </div>
            </div>
            
            <div class="z-card" style="padding: 20px; background: white; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px;">
                <div style="width: 50px; height: 50px; background: #fef2f2; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #dc2626;">
                    <i data-lucide="alert-triangle" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <div style="font-size: 0.8rem; color: #64748b; font-weight: 700; margin-bottom: 4px; text-transform: uppercase;">SISWA REMEDIAL</div>
                    <div style="font-size: 1.6rem; font-weight: 800; color: #dc2626;"><?= $summary['remedial'] ?> <span style="font-size: 0.95rem; color: #94a3b8; font-weight: 600;">/ <?= $summary['total'] ?></span></div>
                </div>
            </div>

            <div class="z-card" style="padding: 20px; background: white; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px;">
                <div style="width: 50px; height: 50px; background: #eff6ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #2563eb;">
                    <i data-lucide="percent" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <div style="font-size: 0.8rem; color: #64748b; font-weight: 700; margin-bottom: 4px; text-transform: uppercase;">KETUNTASAN KELAS</div>
                    <div style="font-size: 1.6rem; font-weight: 800; color: #2563eb;"><?= $summary['persentase'] ?>%</div>
                </div>
            </div>
        </div>

        <div class="z-card" style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            <div class="z-table-wrap">
                <table class="z-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                            <th style="width: 50px; text-align: center; padding: 12px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase;">No</th>
                            <th style="padding: 12px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase;">NIS</th>
                            <th style="padding: 12px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase;">Nama Siswa</th>
                            <th style="text-align: center; padding: 12px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase;">Nilai</th>
                            <th style="text-align: center; padding: 12px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($nilai_data)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 2.5rem; color: #64748b;">Belum ada siswa di kelas ini.</td>
                        </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($nilai_data as $nd): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="text-align: center; color: #64748b; font-weight: 700; padding: 12px;"><?= $no++ ?></td>
                                <td style="color: #475569; font-family: monospace; font-weight: 600; padding: 12px;"><?= htmlspecialchars($nd['nis'] ?: '-') ?></td>
                                <td style="font-weight: 700; color: #1e293b; padding: 12px;"><?= htmlspecialchars($nd['nama']) ?></td>
                                <td style="text-align: center; padding: 12px;">
                                    <?php if ($nd['nilai'] !== null): ?>
                                        <span style="font-weight: 800; font-size: 1.1rem; color: <?= $nd['is_tuntas'] === true ? '#16a34a' : ($nd['is_tuntas'] === false ? '#dc2626' : '#334155') ?>;">
                                            <?= number_format((float)$nd['nilai'], 0) ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: #94a3b8; font-style: italic;">Belum diinput</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center; padding: 12px;">
                                    <?php if ($nd['nilai'] !== null): ?>
                                        <?php if ($nd['is_tuntas'] === true): ?>
                                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #f0fdf4; color: #16a34a; border-radius: 20px; font-size: 0.78rem; font-weight: 800;">
                                                <i data-lucide="check-circle-2" style="width: 14px; height: 14px;"></i> Tuntas
                                            </span>
                                        <?php elseif ($nd['is_tuntas'] === false): ?>
                                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #fef2f2; color: #dc2626; border-radius: 20px; font-size: 0.78rem; font-weight: 800;">
                                                <i data-lucide="alert-circle" style="width: 14px; height: 14px;"></i> Remedial
                                            </span>
                                        <?php else: ?>
                                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #f1f5f9; color: #64748b; border-radius: 20px; font-size: 0.78rem; font-weight: 700;">
                                                <i data-lucide="help-circle" style="width: 14px; height: 14px;"></i> KKM Kosong
                                            </span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span style="color: #94a3b8;">-</span>
                                    <?php endif; ?>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterGuru = document.getElementById('filter_guru');
    const filterKelas = document.getElementById('filter_kelas');
    const filterMapel = document.getElementById('filter_mapel');
    const filterJenisEvaluasi = document.getElementById('filter_jenis_evaluasi');

    function loadEvaluasi() {
        const kId = filterKelas.value;
        const mId = filterMapel.value;
        const gId = filterGuru.value;

        if (kId && mId) {
            filterJenisEvaluasi.innerHTML = '<option value="">Memuat...</option>';
            fetch('<?= base_url("kurikulum/api/jenis-evaluasi") ?>?guru_id=' + gId + '&kelas_id=' + kId + '&mapel_id=' + mId)
                .then(r => r.json())
                .then(res => {
                    let html = '<option value="">-- Pilih Jenis Evaluasi --</option>';
                    if (res.evaluasi && res.evaluasi.length > 0) {
                        res.evaluasi.forEach(ev => {
                            const sel = ev === "<?= htmlspecialchars($jenis_evaluasi) ?>" ? 'selected' : '';
                            html += `<option value="${ev}" ${sel}>${ev}</option>`;
                        });
                    } else {
                        html += '<option value="PH 1">PH 1</option><option value="PH 2">PH 2</option><option value="PTS">PTS</option><option value="PAS">PAS</option>';
                    }
                    filterJenisEvaluasi.innerHTML = html;
                })
                .catch(() => {
                    filterJenisEvaluasi.innerHTML = '<option value="PH 1">PH 1</option><option value="PH 2">PH 2</option><option value="PTS">PTS</option><option value="PAS">PAS</option>';
                });
        }
    }

    if (filterKelas && filterMapel) {
        filterKelas.addEventListener('change', loadEvaluasi);
        filterMapel.addEventListener('change', loadEvaluasi);
        if (filterKelas.value && filterMapel.value && (!filterJenisEvaluasi.value || filterJenisEvaluasi.options.length <= 1)) {
            loadEvaluasi();
        }
    }
});

if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
</body>
</html>

