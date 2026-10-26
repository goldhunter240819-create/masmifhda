<?php
$adminNama = $_SESSION['nama'] ?? 'Admin';
$adminInitial = strtoupper(substr($adminNama, 0, 2));
$activeMenu = 'kurikulum_rekap_ketuntasan';
$am = 'kurikulum';
$ap = 'kurikulum_rekap_ketuntasan';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include __DIR__ . '/_zamrud_head.php'; ?>
    <style>
        .z-btn {
            font-size: 12px !important;
            padding: 8px 16px !important;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        .z-btn:hover { filter: brightness(0.9); }
    </style>
</head>
<body>
    <div class="z-wrap">
        <?php include __DIR__ . '/_sidebar_kurikulum.php'; ?>
        <main class="z-main">
            <header class="z-header">
                <div class="z-header-left">
                    <button class="z-hamburger" id="zHamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
                    <span class="z-header-title">Rekap Ketuntasan</span>
                </div>
                <div class="z-header-user">
                    <div class="z-user-info">
                        <div class="name"><?php echo $adminNama; ?></div>
                        <div class="role">Waka Kurikulum</div>
                    </div>
                    <div class="z-avatar"><?php echo $adminInitial; ?></div>
                </div>
            </header>
            
            <div class="z-scroll">
                <div class="elite-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: var(--z-radius, 12px); margin-bottom: 2rem; color: white;">
                    <div>
                        <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                            <i data-lucide="bar-chart-2" style="color: rgba(255,255,255,0.8);"></i> Rekap Ketuntasan Nilai
                        </h1>
                        <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Pantau tingkat kelulusan KKM siswa per kelas dan mata pelajaran.</p>
                    </div>
                </div>

<div class="z-card" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; margin-bottom: 25px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
    <form method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;" id="formFilterRekap">
        <div style="flex: 1; min-width: 200px;">
            <label style="font-size: 0.8rem; font-weight: 700; color: var(--z-muted); margin-bottom: 8px; display: block;">Guru Pengampu</label>
            <select name="guru_id" id="filter_guru" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                <option value="">-- Pilih Guru --</option>
                <?php foreach($guru_list as $g): ?>
                    <option value="<?= $g['id'] ?>" <?= $guru_id == $g['id'] ? 'selected' : '' ?>><?= htmlspecialchars($g['nama']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="flex: 1; min-width: 200px;">
            <label style="font-size: 0.8rem; font-weight: 700; color: var(--z-muted); margin-bottom: 8px; display: block;">Kelas</label>
            <select name="kelas_id" id="filter_kelas" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                <option value="">-- Pilih Kelas --</option>
                <?php foreach($kelas_list as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= $kelas_id == $k['id'] ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kelas']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="flex: 1; min-width: 200px;">
            <label style="font-size: 0.8rem; font-weight: 700; color: var(--z-muted); margin-bottom: 8px; display: block;">Mata Pelajaran</label>
            <select name="mapel_id" id="filter_mapel" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                <option value="">-- Pilih Mata Pelajaran --</option>
                <?php foreach($mapel_list as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= $mapel_id == $m['id'] ? 'selected' : '' ?>><?= htmlspecialchars($m['nama_mapel']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label style="font-size: 0.8rem; font-weight: 700; color: var(--z-muted); margin-bottom: 8px; display: block;">Jenis Evaluasi</label>
            <select name="jenis_evaluasi" id="filter_jenis_evaluasi" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;">
                <option value="">-- Pilih Jenis Evaluasi --</option>
                <?php if ($jenis_evaluasi): ?>
                    <?php 
                        $label_ev = $jenis_evaluasi;
                        if ($jenis_evaluasi === 'PTS') $label_ev = 'Penilaian Tengah Semester (PTS)';
                        elseif ($jenis_evaluasi === 'PAS') $label_ev = 'Penilaian Akhir Semester (PAS)';
                        elseif (stripos($jenis_evaluasi, 'PH') !== false) $label_ev = 'Penilaian Harian ' . preg_replace('/\D/', '', $jenis_evaluasi);
                    ?>
                    <option value="<?= htmlspecialchars($jenis_evaluasi) ?>" selected><?= htmlspecialchars($label_ev) ?></option>
                <?php endif; ?>
            </select>
        </div>
        <div>
            <button type="submit" class="z-btn z-btn-primary" style="padding: 10px 20px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="filter" style="width: 16px; height: 16px;"></i> Tampilkan Data
            </button>
        </div>
    </form>
</div>

<?php if(!$kelas_id || !$mapel_id || !$jenis_evaluasi): ?>
<div class="z-card" style="text-align: center; padding: 60px 20px;">
    <div style="width: 70px; height: 70px; background: rgba(14, 165, 233, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto;">
        <i data-lucide="search" style="width: 34px; height: 34px; color: #0ea5e9;"></i>
    </div>
    <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--z-text); margin: 0 0 10px 0;">Pilih Filter</h3>
    <p style="font-size: 0.9rem; color: var(--z-muted); margin: 0; max-width: 400px; margin: 0 auto;">Silakan lengkapi pilihan filter (Guru, Kelas, Mapel, dan Jenis Evaluasi) untuk melihat rekap ketuntasan nilai.</p>
</div>
<?php else: ?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 25px;">
    <div class="z-card" style="padding: 20px; display: flex; align-items: center; gap: 15px;">
        <div style="width: 50px; height: 50px; background: #f1f5f9; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
            <i data-lucide="target" style="color: #64748b;"></i>
        </div>
        <div>
            <div style="font-size: 0.85rem; color: #64748b; font-weight: 600; margin-bottom: 4px;">STANDAR KKM</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: #334155;"><?= $kkm_nilai !== null ? htmlspecialchars($kkm_nilai) : '<span style="font-size: 1rem; color: #dc2626;">Belum Diatur</span>' ?></div>
        </div>
    </div>
    
    <div class="z-card" style="padding: 20px; display: flex; align-items: center; gap: 15px;">
        <div style="width: 50px; height: 50px; background: #f0fdf4; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
            <i data-lucide="check-circle-2" style="color: #16a34a;"></i>
        </div>
        <div>
            <div style="font-size: 0.85rem; color: #64748b; font-weight: 600; margin-bottom: 4px;">SISWA TUNTAS</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: #16a34a;"><?= $summary['tuntas'] ?> <span style="font-size: 0.9rem; color: #94a3b8; font-weight: 500;">/ <?= $summary['total'] ?></span></div>
        </div>
    </div>
    
    <div class="z-card" style="padding: 20px; display: flex; align-items: center; gap: 15px;">
        <div style="width: 50px; height: 50px; background: #fef2f2; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
            <i data-lucide="alert-triangle" style="color: #dc2626;"></i>
        </div>
        <div>
            <div style="font-size: 0.85rem; color: #64748b; font-weight: 600; margin-bottom: 4px;">SISWA REMEDIAL</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: #dc2626;"><?= $summary['remedial'] ?> <span style="font-size: 0.9rem; color: #94a3b8; font-weight: 500;">/ <?= $summary['total'] ?></span></div>
        </div>
    </div>
</div>

<div class="z-card">
    <div class="z-table-wrap">
        <table class="z-table">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">No</th>
                    <th>NIS</th>
                    <th>Nama Siswa</th>
                    <th style="text-align: center;">Nilai</th>
                    <th style="text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($nilai_data)): ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding: 2rem; color: #64748b;">Belum ada siswa di kelas ini.</td>
                </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($nilai_data as $nd): ?>
                    <tr>
                        <td style="text-align: center; color: #64748b;"><?= $no++ ?></td>
                        <td style="color: #475569; font-family: monospace;"><?= htmlspecialchars($nd['nis']) ?></td>
                        <td style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($nd['nama']) ?></td>
                        <td style="text-align: center;">
                            <?php if ($nd['nilai'] !== null): ?>
                                <span style="font-weight: 700; font-size: 1.1rem; color: <?= $nd['is_tuntas'] === true ? '#16a34a' : ($nd['is_tuntas'] === false ? '#dc2626' : '#334155') ?>;">
                                    <?= htmlspecialchars($nd['nilai']) ?>
                                </span>
                            <?php else: ?>
                                <span style="color: #94a3b8; font-style: italic;">Belum diinput</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <?php if ($nd['nilai'] !== null): ?>
                                <?php if ($nd['is_tuntas'] === true): ?>
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #f0fdf4; color: #16a34a; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                                        <i data-lucide="check-circle-2" style="width: 14px; height: 14px;"></i> Tuntas
                                    </span>
                                <?php elseif ($nd['is_tuntas'] === false): ?>
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #fef2f2; color: #dc2626; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                                        <i data-lucide="alert-circle" style="width: 14px; height: 14px;"></i> Remedial
                                    </span>
                                <?php else: ?>
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #f1f5f9; color: #64748b; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterGuru = document.getElementById('filter_guru');
    const filterKelas = document.getElementById('filter_kelas');
    const filterMapel = document.getElementById('filter_mapel');

    // Simpan data asli jika filter_guru kosong
    const originalKelasHtml = filterKelas.innerHTML;
    const originalMapelHtml = filterMapel.innerHTML;

    // Nilai awal dari PHP
    const currentKelasId = "<?= $kelas_id ?>";
    const currentMapelId = "<?= $mapel_id ?>";

    filterGuru.addEventListener('change', function() {
        const guruId = this.value;
        if (!guruId) {
            // Restore original options
            filterKelas.innerHTML = originalKelasHtml;
            filterMapel.innerHTML = originalMapelHtml;
            filterJenisEvaluasi.innerHTML = '<option value="">-- Pilih Jenis Evaluasi --</option>';
            return;
        }

        // Tampilkan loading state
        filterKelas.innerHTML = '<option value="">Memuat...</option>';
        filterKelas.disabled = true;
        filterMapel.innerHTML = '<option value="">Memuat...</option>';
        filterMapel.disabled = true;

        fetch('<?= base_url("admin/kurikulum/api/guru-mengajar") ?>?guru_id=' + guruId)
            .then(res => res.json())
            .then(data => {
                // Populate Kelas
                let kelasHtml = '<option value="">-- Pilih Kelas --</option>';
                data.kelas.forEach(k => {
                    const selected = (k.id == currentKelasId) ? 'selected' : '';
                    kelasHtml += `<option value="${k.id}" ${selected}>${k.nama_kelas}</option>`;
                });
                filterKelas.innerHTML = kelasHtml;
                filterKelas.disabled = false;

                // Populate Mapel
                let mapelHtml = '<option value="">-- Pilih Mata Pelajaran --</option>';
                data.mapel.forEach(m => {
                    const selected = (m.id == currentMapelId) ? 'selected' : '';
                    mapelHtml += `<option value="${m.id}" ${selected}>${m.nama_mapel}</option>`;
                });
                filterMapel.innerHTML = mapelHtml;
                filterMapel.disabled = false;
                
                // Jika mapel dan kelas terpilih (setelah refresh), update juga jenis evaluasinya
                if (filterKelas.value && filterMapel.value) {
                    updateJenisEvaluasi();
                }
            })
            .catch(err => {
                console.error("Gagal mengambil data kelas/mapel", err);
                filterKelas.innerHTML = '<option value="">Error</option>';
                filterMapel.innerHTML = '<option value="">Error</option>';
            });
    });

    const filterJenisEvaluasi = document.getElementById('filter_jenis_evaluasi');
    const currentJenisEvaluasi = "<?= $jenis_evaluasi ?>";

    function updateJenisEvaluasi() {
        const kelasId = filterKelas.value;
        const mapelId = filterMapel.value;

        if (!kelasId || !mapelId) {
            return;
        }

        filterJenisEvaluasi.innerHTML = '<option value="">Memuat...</option>';
        filterJenisEvaluasi.disabled = true;

        fetch(`<?= base_url("admin/kurikulum/api/jenis-evaluasi") ?>?kelas_id=${kelasId}&mapel_id=${mapelId}`)
            .then(res => res.json())
            .then(data => {
                let html = '<option value="">-- Pilih Jenis Evaluasi --</option>';
                data.evaluasi.forEach(ev => {
                    const selected = (ev == currentJenisEvaluasi) ? 'selected' : '';
                    let label = ev;
                    if (ev === 'PTS') label = 'Penilaian Tengah Semester (PTS)';
                    else if (ev === 'PAS') label = 'Penilaian Akhir Semester (PAS)';
                    else if (ev.toUpperCase().includes('PH')) label = 'Penilaian Harian ' + ev.replace(/\D/g,'');
                    else if (ev.toUpperCase().includes('UH')) label = 'Ulangan Harian ' + ev.replace(/\D/g,'');
                    
                    html += `<option value="${ev}" ${selected}>${label}</option>`;
                });
                filterJenisEvaluasi.innerHTML = html;
                filterJenisEvaluasi.disabled = false;
            })
            .catch(err => {
                console.error("Gagal mengambil jenis evaluasi", err);
                filterJenisEvaluasi.disabled = false;
            });
    }

    filterKelas.addEventListener('change', updateJenisEvaluasi);
    filterMapel.addEventListener('change', updateJenisEvaluasi);

    // Panggil saat pertama kali load jika guru sudah terpilih
    if (filterGuru.value) {
        filterGuru.dispatchEvent(new Event('change'));
    }
});
</script>

            </div>
        </main>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>

<!-- DEBUG GURU: count=<?= count($guru_list) ?> -->
