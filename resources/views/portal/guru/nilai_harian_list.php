<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$initial = strtoupper(substr($nama, 0, 1));

// get myApps (dibutuhkan sidebar)
$myApps = [];
if (isset($_SESSION['user_id'])) {
    $db_core_view = \App\Core\Database::connect('core');
    try {
        $res = $db_core_view->query("SELECT app_code FROM app_admins WHERE user_id = " . (int)$_SESSION['user_id'])->fetchAll();
        foreach ($res as $r) { $myApps[] = $r['app_code']; }
    } catch (\PDOException $e) {}
}

// Grouping Riwayat by Kelas and Mapel
$riwayatGrouped = [];
$total_evaluasi = count($riwayat);
$total_siswa_dinilai = 0;
foreach ($riwayat as $r) {
    $kelas = $r['nama_kelas'];
    $mapel = $r['nama_mapel'];
    if (!isset($riwayatGrouped[$kelas])) $riwayatGrouped[$kelas] = [];
    if (!isset($riwayatGrouped[$kelas][$mapel])) $riwayatGrouped[$kelas][$mapel] = [];
    $riwayatGrouped[$kelas][$mapel][] = $r;
    $total_siswa_dinilai += (int)$r['jumlah_siswa'];
}
$rata_rata_semua = $total_evaluasi > 0 ? round(array_sum(array_column($riwayat, 'rata_rata')) / $total_evaluasi, 1) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Nilai Harian - MASMIFHDA SYSTEM</title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . date('YmdH')); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/multi_dashboard.css?v=' . date('YmdH')); ?>">
    <style>
        .nh-banner {
            background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 60%, #38bdf8 100%);
            color: #fff;
            border-radius: 20px;
            padding: 28px 30px;
            margin-bottom: 1.5rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 12px 28px -6px rgba(14, 165, 233, 0.35);
        }
        .nh-banner-decor {
            position: absolute;
            right: -25px;
            top: -30px;
            opacity: 0.12;
            pointer-events: none;
        }
        .nh-banner-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 16px;
            position: relative;
            z-index: 1;
        }
        .nh-banner h2 { margin: 0 0 6px 0; font-size: 1.55rem; font-weight: 800; color: #fff; letter-spacing: -0.4px; }
        .nh-banner p { margin: 0; color: #e0f2fe; font-size: 0.92rem; max-width: 600px; }
        .nh-btn-add {
            background: #fff;
            color: #0284c7;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 0.88rem;
            font-family: inherit;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .nh-btn-add:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(0, 0, 0, 0.18); }
        .nh-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 14px;
            margin-top: 22px;
            position: relative;
            z-index: 1;
        }
        .nh-stat-box {
            background: rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 14px 18px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.24);
        }
        .nh-stat-lbl { font-size: 0.78rem; color: #e0f2fe; font-weight: 600; margin-bottom: 4px; }
        .nh-stat-val { font-size: 1.6rem; font-weight: 800; color: #fff; line-height: 1.1; }

        .nh-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 10px -2px rgba(15, 23, 42, 0.04);
            margin-bottom: 1.5rem;
        }
        .nh-card-head {
            padding: 1rem 1.4rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .nh-card-head h3 {
            margin: 0;
            font-size: 1.02rem;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nh-mapel-block { margin-bottom: 20px; }
        .nh-mapel-block:last-child { margin-bottom: 0; }
        .nh-mapel-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .nh-mapel-title {
            font-weight: 800;
            font-size: 0.95rem;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nh-mapel-actions { display: flex; flex-wrap: wrap; gap: 8px; }
        .nh-act-btn {
            border: none;
            cursor: pointer;
            color: #fff;
            border-radius: 8px;
            font-weight: 700;
            padding: 6px 12px;
            font-size: 0.78rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-family: inherit;
            transition: opacity 0.15s, transform 0.15s;
        }
        .nh-act-btn:hover { opacity: 0.92; transform: translateY(-1px); }

        .nh-table { width: 100%; border-collapse: collapse; }
        .nh-table th {
            padding: 10px 14px;
            background: #f8fafc;
            font-size: 0.72rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            border-bottom: 2px solid #f1f5f9;
            text-align: left;
            white-space: nowrap;
        }
        .nh-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.86rem;
            vertical-align: middle;
            color: #334155;
        }
        .nh-table tr:last-child td { border-bottom: none; }
        .nh-table tr:hover td { background: #f8fbff; }

        .nilai-badge { font-weight: 800; font-size: 0.98rem; color: #0284c7; }
        .siswa-badge { background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 50px; font-weight: 700; font-size: 0.78rem; }
        .eval-pill {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 800;
            margin-right: 6px;
            background: #f1f5f9;
            color: #475569;
        }
        .eval-pill.pts { background: #fef3c7; color: #b45309; }
        .eval-pill.pas { background: #ede9fe; color: #6d28d9; }
        .eval-pill.ph  { background: #e0f2fe; color: #0369a1; }

        .btn-edit {
            padding: 6px 12px;
            font-size: 0.78rem;
            background: #2563eb;
            color: #fff;
            border-radius: 7px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-weight: 700;
        }
        .btn-hapus {
            padding: 6px 10px;
            font-size: 0.78rem;
            background: #fef2f2;
            color: #dc2626;
            border-radius: 7px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-weight: 700;
        }
        .empty-state { text-align: center; padding: 3.5rem 2rem; color: #64748b; }
        .empty-state h3 { color: #0f172a; margin: 12px 0 6px 0; }
        .empty-state p { margin: 0 0 18px 0; font-size: 0.88rem; }

        .nh-modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 20px;
            backdrop-filter: blur(4px);
        }
        .nh-modal {
            background: #fff;
            width: 100%;
            max-width: 480px;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.18);
            animation: modalIn 0.22s ease-out;
        }
        .nh-input {
            width: 100%;
            padding: 10px 12px;
            border-radius: 9px;
            border: 1px solid #cbd5e1;
            font-family: inherit;
            font-size: 0.92rem;
            outline: none;
            background: #fff;
            box-sizing: border-box;
        }
        .nh-input:focus { border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15); }
        @keyframes modalIn {
            from { opacity: 0; transform: translateY(16px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
    </style>
</head>
<body>
<div class="siakad-container">
    <?php include __DIR__ . '/../sidebar_multi_guru.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../header_multi_guru.php'; ?>
        <div class="z-scroll" style="padding: 1.5rem var(--z-gap) var(--z-gap);">

            <!-- Page Header / Banner -->
            <div class="nh-banner">
                <div class="nh-banner-decor">
                    <i data-lucide="bar-chart-3" style="width: 210px; height: 210px;"></i>
                </div>
                <div class="nh-banner-top">
                    <div>
                        <h2>Rekap Nilai Harian</h2>
                        <p>Riwayat pengisian nilai harian, ulangan, dan tugas siswa pada semester aktif ini.</p>
                    </div>
                    <button type="button" onclick="document.getElementById('modalTambahNilai').style.display='flex'" class="nh-btn-add">
                        <i data-lucide="plus" style="width:18px;height:18px;"></i> Tambah Nilai
                    </button>
                </div>
                <div class="nh-stats">
                    <div class="nh-stat-box">
                        <div class="nh-stat-lbl">Total Evaluasi</div>
                        <div class="nh-stat-val"><?= $total_evaluasi ?></div>
                    </div>
                    <div class="nh-stat-box">
                        <div class="nh-stat-lbl">Siswa Dinilai</div>
                        <div class="nh-stat-val"><?= $total_siswa_dinilai ?></div>
                    </div>
                    <div class="nh-stat-box">
                        <div class="nh-stat-lbl">Rata-rata Kelas</div>
                        <div class="nh-stat-val"><?= $rata_rata_semua ?></div>
                    </div>
                </div>
            </div>

            <?php if (empty($riwayatGrouped)): ?>
                <div class="nh-card">
                    <div class="nh-card-head">
                        <h3><i data-lucide="history" style="width:18px;height:18px;color:#0ea5e9;"></i> Riwayat Input Nilai</h3>
                    </div>
                    <div class="empty-state">
                        <i data-lucide="inbox" style="width:48px;height:48px;color:#cbd5e1;"></i>
                        <h3>Belum Ada Data Nilai Harian</h3>
                        <p>Klik tombol <strong>Tambah Nilai</strong> di atas untuk mulai menginput nilai siswa, atau gunakan Aplikasi Mobile (APK) Guru.</p>
                        <button type="button" onclick="document.getElementById('modalTambahNilai').style.display='flex'" class="nh-act-btn" style="background:#0284c7; padding:10px 18px; font-size:0.86rem;">
                            <i data-lucide="plus" style="width:16px;height:16px;"></i> Tambah Nilai Sekarang
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($riwayatGrouped as $kelas => $mapels): ?>
                    <div class="nh-card">
                        <div class="nh-card-head">
                            <h3><i data-lucide="users" style="width:18px;height:18px;color:#0284c7;"></i> Kelas <?= htmlspecialchars($kelas) ?></h3>
                            <span style="font-size:0.78rem; font-weight:700; color:#64748b;"><?= count($mapels) ?> Mata Pelajaran</span>
                        </div>

                        <div style="padding: 16px 20px;">
                            <?php foreach ($mapels as $mapel => $items):
                                $first_item = $items[0];
                            ?>
                                <div class="nh-mapel-block">
                                    <div class="nh-mapel-bar">
                                        <div class="nh-mapel-title">
                                            <i data-lucide="book-open" style="width:16px;height:16px;color:#6366f1;"></i>
                                            <?= htmlspecialchars($mapel) ?>
                                        </div>
                                        <div class="nh-mapel-actions">
                                            <a href="<?php echo base_url('guru/nilai_harian/rekap_detail?kelas_id=' . $first_item['kelas_id'] . '&mapel_id=' . $first_item['mapel_id']); ?>" class="nh-act-btn" style="background:#3b82f6;">
                                                <i data-lucide="eye" style="width:14px;height:14px;"></i> Detail
                                            </a>
                                            <a href="<?php echo base_url('guru/nilai_harian/cetak_rekap?kelas_id=' . $first_item['kelas_id'] . '&mapel_id=' . $first_item['mapel_id']); ?>" target="_blank" class="nh-act-btn" style="background:#0ea5e9;">
                                                <i data-lucide="printer" style="width:14px;height:14px;"></i> Rekap Nilai
                                            </a>
                                            <button type="button" onclick="cetakBlankoKosong(<?= (int)$first_item['kelas_id'] ?>, <?= (int)$first_item['mapel_id'] ?>)" class="nh-act-btn" style="background:#8b5cf6;">
                                                <i data-lucide="file-text" style="width:14px;height:14px;"></i> Blanko Kosong
                                            </button>
                                        </div>
                                    </div>

                                    <div style="overflow-x:auto;">
                                        <table class="nh-table">
                                            <thead>
                                                <tr>
                                                    <th style="width:130px;">Tanggal</th>
                                                    <th>Nama Materi / Ujian</th>
                                                    <th style="text-align:center; width:130px;">Siswa Dinilai</th>
                                                    <th style="text-align:center; width:110px;">Rata-rata</th>
                                                    <th style="text-align:center; width:150px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($items as $r):
                                                    $judulUpper = strtoupper(trim($r['judul_materi']));
                                                    $pillClass = 'ph';
                                                    $pillLabel = 'PH';
                                                    if (strpos($judulUpper, 'PTS') === 0) { $pillClass = 'pts'; $pillLabel = 'PTS'; }
                                                    elseif (strpos($judulUpper, 'PAS') === 0) { $pillClass = 'pas'; $pillLabel = 'PAS'; }
                                                ?>
                                                <tr>
                                                    <td style="white-space:nowrap; color:#64748b; font-weight:600;"><?= date('d/m/Y', strtotime($r['tanggal'])) ?></td>
                                                    <td style="font-weight:600; color:#0f172a;">
                                                        <span class="eval-pill <?= $pillClass ?>"><?= $pillLabel ?></span>
                                                        <?= htmlspecialchars($r['judul_materi']) ?>
                                                    </td>
                                                    <td style="text-align:center;"><span class="siswa-badge"><?= (int)$r['jumlah_siswa'] ?> Siswa</span></td>
                                                    <td style="text-align:center;" class="nilai-badge"><?= number_format((float)$r['rata_rata'], 1) ?></td>
                                                    <td style="text-align:center; white-space:nowrap;">
                                                        <a href="<?php echo base_url('guru/nilai_harian/detail/' . $r['id']); ?>" class="btn-edit">
                                                            <i data-lucide="pencil" style="width:13px;height:13px;"></i> Edit
                                                        </a>
                                                        <a href="#" onclick="hapusNilai(event, '<?php echo base_url('guru/nilai_harian/delete/' . $r['id']); ?>')" class="btn-hapus" style="margin-left:5px;" title="Hapus">
                                                            <i data-lucide="trash-2" style="width:13px;height:13px;"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Modal Tambah Nilai Harian -->
<div id="modalTambahNilai" class="nh-modal-backdrop" onclick="if(event.target===this) this.style.display='none'">
    <div class="nh-modal">
        <div style="padding: 18px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
            <h3 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="file-plus" style="color: #0284c7; width:20px; height:20px;"></i> Tambah Nilai Harian Baru
            </h3>
            <button type="button" onclick="document.getElementById('modalTambahNilai').style.display='none'" style="background:none; border:none; color:#64748b; cursor:pointer; padding:4px;">
                <i data-lucide="x" style="width:20px; height:20px;"></i>
            </button>
        </div>
        <form action="<?php echo base_url('guru/nilai_harian/store_new'); ?>" method="POST" style="padding: 22px 24px;">
            <div style="margin-bottom: 14px;">
                <label style="display:block; margin-bottom: 6px; font-weight: 700; color: #334155; font-size: 0.85rem;">Kelas</label>
                <select name="kelas_id" id="modal_kelas_id" required class="nh-input" onchange="updateMapelList()">
                    <option value="">-- Pilih Kelas --</option>
                    <?php if (!empty($kelasList)): foreach ($kelasList as $k): ?>
                        <option value="<?= (int)$k['id'] ?>"><?= htmlspecialchars($k['nama_kelas']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display:block; margin-bottom: 6px; font-weight: 700; color: #334155; font-size: 0.85rem;">Mata Pelajaran</label>
                <select name="mapel_id" id="modal_mapel_id" required class="nh-input">
                    <option value="">-- Pilih Mapel --</option>
                </select>
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display:block; margin-bottom: 6px; font-weight: 700; color: #334155; font-size: 0.85rem;">Jenis Evaluasi</label>
                <select name="jenis_evaluasi" required class="nh-input">
                    <option value="PH">Penilaian Harian (Auto PH 1, PH 2, dst)</option>
                    <option value="PTS">Penilaian Tengah Semester (PTS)</option>
                    <option value="PAS">Penilaian Akhir Semester (PAS)</option>
                </select>
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display:block; margin-bottom: 6px; font-weight: 700; color: #334155; font-size: 0.85rem;">Materi Pokok (Opsional)</label>
                <input type="text" name="materi_pokok" placeholder="Cth: Bab 1 Algoritma & Pemrograman..." class="nh-input">
            </div>

            <div style="margin-bottom: 22px;">
                <label style="display:block; margin-bottom: 6px; font-weight: 700; color: #334155; font-size: 0.85rem;">Tanggal</label>
                <input type="date" name="tanggal" required value="<?= date('Y-m-d') ?>" class="nh-input">
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('modalTambahNilai').style.display='none'" style="padding: 10px 18px; border-radius: 9px; border: 1px solid #cbd5e1; background: #fff; color: #475569; font-weight: 700; cursor: pointer; font-family: inherit;">Batal</button>
                <button type="submit" style="padding: 10px 20px; border-radius: 9px; border: none; background: #0284c7; color: #fff; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; font-family: inherit;">
                    <i data-lucide="arrow-right" style="width:16px;height:16px;"></i> Lanjut Input Nilai
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    lucide.createIcons();

    const mengajarData = <?= json_encode($mengajarData ?? []) ?>;
    function updateMapelList() {
        const kelasId = document.getElementById('modal_kelas_id').value;
        const mapelSelect = document.getElementById('modal_mapel_id');
        mapelSelect.innerHTML = '<option value="">-- Pilih Mapel --</option>';
        if (!kelasId) return;

        const mapels = mengajarData.filter(m => m.kelas_id == kelasId);
        const seen = new Set();
        mapels.forEach(m => {
            if (!seen.has(m.mapel_id)) {
                seen.add(m.mapel_id);
                const opt = document.createElement('option');
                opt.value = m.mapel_id;
                opt.textContent = m.nama_mapel;
                mapelSelect.appendChild(opt);
            }
        });
    }

    <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] === 'deleted'): ?>
        Swal.fire({
            icon: 'success',
            title: 'Terhapus!',
            text: 'Data nilai harian berhasil dihapus.',
            showConfirmButton: false,
            timer: 2500,
            toast: true,
            position: 'top-end'
        });
        <?php elseif ($_GET['msg'] === 'created'): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data nilai harian baru berhasil ditambahkan.',
            showConfirmButton: false,
            timer: 2500,
            toast: true,
            position: 'top-end'
        });
        <?php endif; ?>
        window.history.replaceState({}, document.title, window.location.pathname);
    <?php endif; ?>

    function cetakBlankoKosong(kelas_id, mapel_id) {
        Swal.fire({
            title: 'Blanko Kosong',
            text: 'Masukkan jumlah kolom Penilaian Harian (PH) yang diinginkan:',
            input: 'number',
            inputValue: 5,
            showCancelButton: true,
            confirmButtonText: 'Cetak',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#0284c7',
            inputAttributes: { min: 1, max: 15, step: 1 },
            inputValidator: (value) => {
                if (!value || value <= 0) return 'Jumlah kolom harus lebih dari 0!';
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const jumlah = parseInt(result.value) || 5;
                const url = `<?php echo base_url('guru/nilai_harian/cetak_blanko'); ?>?kelas_id=${kelas_id}&mapel_id=${mapel_id}&jumlah_ph=${jumlah}`;
                window.open(url, '_blank');
            }
        });
    }

    function hapusNilai(event, url) {
        event.preventDefault();
        Swal.fire({
            title: 'Hapus Evaluasi?',
            text: 'Yakin ingin menghapus data nilai ini beserta seluruh rincian siswanya? Data yang dihapus tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                        window.location.href = url;
                    }
                });
            }
        });
    }
</script>
</body>
</html>
