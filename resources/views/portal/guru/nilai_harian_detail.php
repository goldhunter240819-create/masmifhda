<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$initial = strtoupper(substr($nama, 0, 1));

$myApps = [];
if (isset($_SESSION['user_id'])) {
    $db_core_view = \App\Core\Database::connect('core');
    try {
        $res = $db_core_view->query("SELECT app_code FROM app_admins WHERE user_id = " . (int)$_SESSION['user_id'])->fetchAll();
        foreach ($res as $r) { $myApps[] = $r['app_code']; }
    } catch (\PDOException $e) {}
}

$total_siswa = count($details);
$rata_rata = 0;
if ($total_siswa > 0) {
    $sum = 0;
    foreach ($details as $d) { $sum += (float)$d['nilai']; }
    $rata_rata = $sum / $total_siswa;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Nilai Harian - MASMIFHDA SYSTEM</title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . date('YmdH')); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/multi_dashboard.css?v=' . date('YmdH')); ?>">
    <style>
        .nh-edit-banner {
            background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 60%, #38bdf8 100%);
            border-radius: 18px;
            padding: 1.5rem 2rem;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 1.5rem;
            box-shadow: 0 12px 26px -6px rgba(14, 165, 233, 0.35);
            position: relative;
            overflow: hidden;
        }
        .nh-edit-banner .decor { position: absolute; right: -20px; top: -25px; opacity: 0.12; pointer-events: none; }
        .nh-back-btn {
            width: 40px; height: 40px;
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; text-decoration: none;
            transition: background 0.15s;
        }
        .nh-back-btn:hover { background: rgba(255, 255, 255, 0.28); }
        .info-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.35rem 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 10px -2px rgba(15, 23, 42, 0.04);
        }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; }
        .info-item .lbl { font-size: 0.72rem; color: #64748b; font-weight: 800; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 6px; }
        .info-item .val { font-size: 0.98rem; font-weight: 700; color: #0f172a; }
        .nh-input {
            width: 100%;
            padding: 8px 11px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.92rem;
            font-weight: 600;
            color: #0f172a;
            outline: none;
            box-sizing: border-box;
        }
        .nh-input:focus { border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15); }
        .nh-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 10px -2px rgba(15, 23, 42, 0.04); }
        .nh-card-head { padding: 1.1rem 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
        .nh-card-head h3 { margin: 0; font-size: 1rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px; }
        .nh-table { width: 100%; border-collapse: collapse; }
        .nh-table th { padding: 10px 16px; background: #f8fafc; font-size: 0.72rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 2px solid #f1f5f9; text-align: left; }
        .nh-table td { padding: 10px 16px; border-bottom: 1px solid #f8fafc; font-size: 0.88rem; vertical-align: middle; color: #334155; }
        .nh-table tr:last-child td { border-bottom: none; }
        .nh-table tr:hover td { background: #f8fbff; }
        .nilai-input {
            width: 90px;
            text-align: center;
            padding: 7px 8px;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            font-weight: 800;
            font-size: 0.95rem;
            font-family: inherit;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .nilai-input:focus { border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15); }
        .nilai-input.low { color: #dc2626; border-color: #fca5a5; background: #fef2f2; }
        .nilai-input.pass { color: #0284c7; }
        .btn-save {
            background: #0284c7;
            color: #fff;
            border: none;
            padding: 9px 18px;
            border-radius: 9px;
            font-weight: 700;
            font-size: 0.86rem;
            font-family: inherit;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 10px rgba(2, 132, 199, 0.25);
        }
        .btn-save:hover { background: #0369a1; }
    </style>
</head>
<body>
<div class="siakad-container">
    <?php include __DIR__ . '/../sidebar_multi_guru.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../header_multi_guru.php'; ?>
        <div class="z-scroll" style="padding: 1.5rem var(--z-gap) var(--z-gap);">

            <form method="POST" action="<?php echo base_url('guru/nilai_harian/update'); ?>">
                <input type="hidden" name="id" value="<?= (int)$header['id'] ?>">

                <!-- BANNER -->
                <div class="nh-edit-banner">
                    <div class="decor">
                        <i data-lucide="bar-chart-2" style="width: 190px; height: 190px;"></i>
                    </div>
                    <div style="display:flex; align-items:center; gap: 15px; position:relative; z-index:1;">
                        <a href="<?php echo base_url('guru/nilai_harian'); ?>" class="nh-back-btn" title="Kembali">
                            <i data-lucide="arrow-left" style="width:18px;height:18px;"></i>
                        </a>
                        <div>
                            <h2 style="margin: 0 0 4px 0; font-size: 1.3rem; font-weight: 800; letter-spacing: -0.4px; color:#fff;">Edit Nilai Harian</h2>
                            <p style="margin: 0; color: #e0f2fe; font-size: 0.9rem;">Materi: <strong><?= htmlspecialchars($header['judul_materi']) ?></strong></p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 2rem; position:relative; z-index:1;">
                        <div>
                            <div style="font-size: 0.72rem; color: #e0f2fe; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Siswa Dinilai</div>
                            <div style="font-size: 1.8rem; font-weight: 900; line-height: 1;"><?= $total_siswa ?></div>
                        </div>
                        <div>
                            <div style="font-size: 0.72rem; color: #e0f2fe; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Rata-rata</div>
                            <div id="liveRataRata" style="font-size: 1.8rem; font-weight: 900; line-height: 1;"><?= number_format($rata_rata, 1) ?></div>
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="info-card">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="lbl">Tanggal</div>
                            <div class="val">
                                <input type="date" name="tanggal" value="<?= htmlspecialchars($header['tanggal']) ?>" required class="nh-input">
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="lbl">Materi / Ujian</div>
                            <div class="val">
                                <input type="text" name="judul_materi" value="<?= htmlspecialchars($header['judul_materi']) ?>" required class="nh-input">
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="lbl">Kelas</div>
                            <div class="val" style="padding: 8px 0;"><?= htmlspecialchars($header['nama_kelas']) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="lbl">Mata Pelajaran</div>
                            <div class="val" style="padding: 8px 0;"><?= htmlspecialchars($header['nama_mapel']) ?></div>
                        </div>
                    </div>
                </div>

                <!-- Table Siswa -->
                <div class="nh-card">
                    <div class="nh-card-head">
                        <h3><i data-lucide="users" style="width:18px;height:18px;color:#0284c7;"></i> Daftar Nilai Siswa</h3>
                        <button type="submit" class="btn-save">
                            <i data-lucide="save" style="width:16px;height:16px;"></i> Simpan Perubahan
                        </button>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="nh-table">
                            <thead>
                                <tr>
                                    <th style="width:55px;text-align:center;">No</th>
                                    <th style="width:130px;">NIS</th>
                                    <th>Nama Siswa</th>
                                    <th style="width:150px;text-align:center;">Nilai (0 - 100)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($details)): ?>
                                <tr>
                                    <td colspan="4" style="text-align:center; padding:2.5rem; color:#64748b;">Tidak ada rincian data siswa.</td>
                                </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach ($details as $d):
                                        $val = floatval($d['nilai']);
                                        $cls = ($val > 0 && $val < 75) ? 'low' : 'pass';
                                    ?>
                                    <tr>
                                        <td style="text-align:center; color:#94a3b8; font-weight:700;"><?= $no++ ?></td>
                                        <td style="font-family:monospace; color:#475569;"><?= htmlspecialchars($d['nis'] ?? '-') ?></td>
                                        <td style="font-weight:700; color:#0f172a;"><?= htmlspecialchars($d['nama']) ?></td>
                                        <td style="text-align:center;">
                                            <input type="number" step="0.01" max="100" min="0" name="nilai[<?= (int)$d['siswa_id'] ?>]" value="<?= $val ?>" class="nilai-input <?= $cls ?>" oninput="updateLiveStats(this)">
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    lucide.createIcons();

    function updateLiveStats(el) {
        const v = parseFloat(el.value);
        if (!isNaN(v) && v > 0 && v < 75) {
            el.classList.add('low');
            el.classList.remove('pass');
        } else {
            el.classList.remove('low');
            el.classList.add('pass');
        }
        const inputs = document.querySelectorAll('.nilai-input');
        let sum = 0, cnt = 0;
        inputs.forEach(inp => {
            const num = parseFloat(inp.value);
            if (!isNaN(num)) { sum += num; cnt++; }
        });
        document.getElementById('liveRataRata').textContent = cnt > 0 ? (sum / cnt).toFixed(1) : '0.0';
    }

    // Enter key moves to next score input
    document.querySelectorAll('.nilai-input').forEach((inp, idx, all) => {
        inp.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (all[idx + 1]) {
                    all[idx + 1].focus();
                    all[idx + 1].select();
                }
            }
        });
    });

    <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] === 'updated'): ?>
        Swal.fire({ icon: 'success', title: 'Tersimpan!', text: 'Perubahan nilai berhasil disimpan.', showConfirmButton: false, timer: 2500, toast: true, position: 'top-end' });
        <?php elseif ($_GET['msg'] === 'created'): ?>
        Swal.fire({ icon: 'success', title: 'Evaluasi Dibuat!', text: 'Silakan isi nilai siswa lalu klik Simpan Perubahan.', showConfirmButton: false, timer: 3000, toast: true, position: 'top-end' });
        <?php endif; ?>
        window.history.replaceState({}, document.title, window.location.pathname);
    <?php endif; ?>
</script>
</body>
</html>
