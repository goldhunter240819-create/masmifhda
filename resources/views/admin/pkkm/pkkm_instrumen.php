<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title = $title_page; include (file_exists(__DIR__ . '/_zamrud_head.php') ? __DIR__ . '/_zamrud_head.php' : __DIR__ . '/../_zamrud_head.php'); ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Zamrud UI Integration Overrides */
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #0f172a; }
        .z-scroll { padding-bottom: 3rem; }
        .table-responsive { overflow-x: auto; border-radius: 8px; }

        /* Elite Sultan Banner */
        .elite-page-header {
            position: relative; padding: 2rem 2.5rem; margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            border-radius: 20px; overflow: hidden; color: #ffffff;
            box-shadow: 0 12px 30px -8px rgba(30, 58, 138, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .elite-page-header .header-bg-shapes {
            position: absolute; top: 0; right: 0; bottom: 0; left: 0;
            background-image: radial-gradient(circle at 85% 20%, rgba(255, 255, 255, 0.18) 0%, transparent 40%),
                              radial-gradient(circle at 15% 80%, rgba(0, 0, 0, 0.12) 0%, transparent 40%);
            z-index: 1; pointer-events: none;
        }
        .elite-page-header .header-content-inner { position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap; }
        .elite-page-header .header-text-premium h1 { font-size: 1.75rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 12px; color: #fff; }
        .elite-page-header .header-text-premium p { font-size: 0.9rem; color: rgba(255,255,255,0.85); margin-top: 0.35rem; margin-bottom: 0; max-width: 650px; font-weight: 400; line-height: 1.5; }
        .header-tag-pill { display: inline-flex; align-items: center; gap: 6px; background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); color: #fff; font-size: 0.72rem; font-weight: 700; padding: 3px 10px; border-radius: 999px; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 8px; backdrop-filter: blur(4px); }

        /* Stat Cards */
        .stat-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px; margin-bottom: 24px;
        }
        .stat-box {
            padding: 16px 20px; border-radius: 16px; display: flex; align-items: center; gap: 16px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03); background: #fff; border: 1px solid #e2e8f0; transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-box:hover {
            transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        }
        .stat-box blue { background: linear-gradient(135deg, #faf5ff, #ffffff); border-color: #e9d5ff; color: #6b21a8; }
        .stat-box.blue { background: linear-gradient(135deg, #eff6ff, #ffffff); border-color: #bfdbfe; color: #1e3a8a; }
        .stat-box.green { background: linear-gradient(135deg, #f0fdf4, #ffffff); border-color: #bbf7d0; color: #14532d; }
        .stat-box.yellow { background: linear-gradient(135deg, #fefce8, #ffffff); border-color: #fef08a; color: #713f12; }
        .stat-icon { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.9); box-shadow: 0 2px 6px rgba(0,0,0,0.04); flex-shrink: 0; }
        .stat-box blue .stat-icon { color: #8b5cf6; }
        .stat-box.blue .stat-icon { color: #3b82f6; }
        .stat-box.green .stat-icon { color: #38bdf8; }
        .stat-box.yellow .stat-icon { color: #f59e0b; }
        .stat-value { font-size: 1.7rem; font-weight: 800; line-height: 1.1; margin-bottom: 2px; }
        .stat-label { font-size: 0.78rem; font-weight: 700; opacity: 0.75; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Komponen & Sub-Komponen */
        .pkkm-card { background: #fff; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 24px; border: 1px solid #e2e8f0; }
        .komponen-header { background: linear-gradient(135deg, #f0fdf4 0%, #f8fafc 100%); border-left: 5px solid #0284c7; padding: 14px 18px; font-weight: 800; color: #0f172a; display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-radius: 0 10px 10px 0; border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; }
        .sub-komponen-box { border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px; padding: 18px; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
        .sub-komponen-header { font-weight: 700; color: #1e293b; margin-bottom: 14px; display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; border-bottom: 1px dashed #cbd5e1; font-size: 0.95rem; }
        .sub-komponen-header .badge-num { background: #0284c7; color: #ffffff; padding: 3px 8px; border-radius: 6px; margin-right: 8px; font-size: 0.75rem; font-weight: 700; }
        
        /* Nav Tabs */
        .nav-pills .nav-link { color: #475569; background: #f8fafc; font-weight: 700; font-size: 0.88rem; border-radius: 12px; padding: 10px 18px; border: 1px solid #e2e8f0; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 8px; }
        .nav-pills .nav-link:hover { background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; }
        .nav-pills .nav-link.active { background: linear-gradient(135deg, #0284c7, #1e3a8a) !important; color: #ffffff !important; border-color: #1e3a8a !important; box-shadow: 0 6px 16px rgba(56, 189, 248, 0.3) !important; }
        .nav-pills .nav-link.active .badge-count { background: rgba(255, 255, 255, 0.25) !important; color: #fff !important; }
        .badge-count { background: #e2e8f0; color: #475569; font-size: 0.75rem; font-weight: 700; padding: 3px 8px; border-radius: 999px; }

        .btn-upload { background: #ecfdf5; color: #0284c7; border: 1px solid #6ee7b7; padding: 6px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; transition: background 0.2s; }
        .btn-upload:hover { background: #d1fae5; color: #1e3a8a; }
        .berkas-item { font-size: 0.8rem; background: #f1f5f9; padding: 6px 10px; border-radius: 6px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; border: 1px solid #e2e8f0; }
        .select-skor { border: 1px solid #cbd5e1; border-radius: 8px; padding: 6px 10px; font-weight: 700; cursor: pointer; background: #fff; font-size: 0.85rem; }
        .select-skor:focus { outline: none; border-color: #0284c7; box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15); }

        .e-btn {
            display: inline-flex; align-items: center; gap: 8px; padding: 11px 20px;
            border-radius: 12px; font-weight: 700; font-size: 0.88rem; cursor: pointer;
            border: none; transition: 0.2s ease; text-decoration: none;
        }
        .e-btn:hover { transform: translateY(-2px); }
        .e-btn-primary { background: #ffffff; color: #1e3a8a; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12); }
        .e-btn-primary:hover { background: #f8fafc; color: #0f172a; }
    </style>
</head>
<body>

<div class="z-wrap">
    <?php include (file_exists(__DIR__ . '/_zamrud_sidebar.php') ? __DIR__ . '/_zamrud_sidebar.php' : __DIR__ . '/../_zamrud_sidebar.php'); ?>
    
    <main class="z-main">
        <?php $page_badge = 'Admin Portal'; include (file_exists(__DIR__ . '/../siakad/_siakad_header.php') ? __DIR__ . '/../siakad/_siakad_header.php' : __DIR__ . '/../../siakad/_siakad_header.php'); ?>

        <div class="z-scroll">
            
            <!-- ELITE SULTAN BANNER GLOBAL -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-tag-pill">
                            <i data-lucide="award" style="width:14px;height:14px;"></i> JUKNIS TAHUNAN TERBARU • TAHUN 1, 2, 3
                        </div>
                        <h1><i data-lucide="clipboard-check"></i> Instrumen Penilaian PKKM</h1>
                        <p>Kelola 42 indikator instrumen penilaian kinerja kepala madrasah, unggah dokumen bukti fisik, dan set simulasi nilai.</p>
                    </div>
                    <div class="header-actions-premium d-flex gap-2">
                        <button class="e-btn e-btn-primary" onclick="openModalKomponen()">
                            <i data-lucide="plus-circle" style="width:18px;height:18px;"></i> Tambah Komponen Baru
                        </button>
                    </div>
                </div>
            </div>

            <!-- STAT SUMMARY CARDS -->
            <div class="stat-grid">
                <div class="stat-box purple">
                    <div class="stat-icon"><i data-lucide="layers" style="width:24px;height:24px;"></i></div>
                    <div>
                        <div class="stat-value"><?= $totalKomponen ?? 4; ?></div>
                        <div class="stat-label">Komponen Utama</div>
                    </div>
                </div>
                <div class="stat-box blue">
                    <div class="stat-icon"><i data-lucide="list-checks" style="width:24px;height:24px;"></i></div>
                    <div>
                        <div class="stat-value"><?= $totalIndikator ?? 42; ?></div>
                        <div class="stat-label">Indikator Kinerja</div>
                    </div>
                </div>
                <div class="stat-box green">
                    <div class="stat-icon"><i data-lucide="folder-check" style="width:24px;height:24px;"></i></div>
                    <div>
                        <div class="stat-value"><?= $totalBerkas ?? 0; ?></div>
                        <div class="stat-label">Berkas Terlampir</div>
                    </div>
                </div>
                <div class="stat-box yellow">
                    <div class="stat-icon"><i data-lucide="pie-chart" style="width:24px;height:24px;"></i></div>
                    <div>
                        <div class="stat-value"><?= $persentase ?? 0; ?>%</div>
                        <div class="stat-label">Skor (<?= $skorTotal ?? 0; ?>/<?= $skorMax ?? 168; ?>)</div>
                    </div>
                </div>
            </div>
            
            <?php if(isset($_GET['msg'])): ?>
                <?php if($_GET['msg'] == 'synced'): ?>
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" style="background:#dcfce7; color:#166534; padding:16px 20px; border-radius:12px; border:1px solid #bbf7d0;" role="alert">
                        <i data-lucide="check-circle-2" style="width:22px;height:22px;flex-shrink:0;"></i>
                        <div>
                            <strong>Instrumen Berhasil Tersinkron!</strong> Database kini aktif dengan <strong>42 Indikator</strong> sesuai Juknis Penilaian Kinerja Kepala Madrasah (Tahun 1, 2, 3).
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="margin-left:auto;"></button>
                    </div>
                <?php elseif($_GET['msg'] == 'error_sync'): ?>
                    <div class="alert alert-danger alert-dismissible fade show mb-4" style="background:#fee2e2; color:#991b1b; padding:15px; border-radius:12px; border:1px solid #fecaca;" role="alert">Gagal menyinkronkan data instrumen.</div>
                <?php elseif($_GET['msg'] == 'success' || $_GET['msg'] == 'berkas_saved'): ?>
                    <div class="alert alert-success alert-dismissible fade show mb-4" style="background:#dcfce7; color:#166534; padding:15px; border-radius:12px; border:1px solid #bbf7d0;" role="alert">Berhasil menyimpan data!</div>
                <?php elseif($_GET['msg'] == 'deleted'): ?>
                    <div class="alert alert-danger alert-dismissible fade show mb-4" style="background:#fee2e2; color:#991b1b; padding:15px; border-radius:12px; border:1px solid #fecaca;" role="alert">Berhasil menghapus data.</div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="pkkm-card">
                <?php if(empty($data)): ?>
                    <div class="text-center py-5" style="color:#94a3b8;">
                        <i data-lucide="folder-open" style="width:48px;height:48px; margin-bottom:10px;"></i>
                        <p>Belum ada data komponen instrumen PKKM.</p>
                    </div>
                <?php else: ?>
                    <!-- Navigation Tabs for Komponen -->
                    <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist" style="gap:10px; border-bottom: 2px solid #e2e8f0; padding-bottom: 16px;">
                    <?php $i=0; foreach($data as $komp): 
                        $countInd = 0;
                        foreach($komp['sub_komponens'] as $s) { $countInd += count($s['indikators']); }
                        $countInd += count($komp['indikators']);
                    ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo $i==0 ? 'active' : ''; ?>" id="pills-komp-<?php echo $komp['id']; ?>-tab" data-bs-toggle="pill" data-bs-target="#pills-komp-<?php echo $komp['id']; ?>" type="button" role="tab">
                                <span>Komponen <?php echo htmlspecialchars($komp['urutan']); ?></span>
                                <span class="badge-count"><?php echo $countInd; ?> Indikator</span>
                            </button>
                        </li>
                    <?php $i++; endforeach; ?>
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                    <?php $i=0; foreach($data as $komp): ?>
                        <div class="tab-pane fade <?php echo $i==0 ? 'show active' : ''; ?>" id="pills-komp-<?php echo $komp['id']; ?>" role="tabpanel" tabindex="0">
                        
                            <div class="komponen-header">
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <span style="background:#0284c7; color:#fff; padding:4px 10px; border-radius:8px; font-size:0.85rem; font-weight:800;"><?php echo htmlspecialchars($komp['urutan']); ?></span>
                                    <span><?php echo htmlspecialchars($komp['nama_komponen']); ?></span>
                                </div>
                                <div style="display:flex; gap:8px; align-items:center;">
                                    <button class="btn btn-sm btn-outline-success" onclick="openModalSubKomponen(<?php echo $komp['id']; ?>)" style="padding: 4px 10px; font-size:0.8rem; font-weight:600; border-radius:6px;">+ Sub-Komponen</button>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="openModalIndikator(<?php echo $komp['id']; ?>, null)" style="padding: 4px 10px; font-size:0.8rem; font-weight:600; border-radius:6px;">+ Indikator</button>
                                    <button class="btn btn-sm" onclick="editKomponen(<?php echo $komp['id']; ?>, '<?php echo htmlspecialchars(addslashes($komp['nama_komponen'])); ?>', <?php echo $komp['urutan']; ?>)" style="padding: 4px 8px; font-size:0.8rem; background:#e2e8f0; color:#475569; border:none; border-radius:6px;" title="Edit Komponen"><i data-lucide="edit" style="width:14px;height:14px;"></i></button>
                                    <form action="/admin/pkkm/komponen/delete" method="POST" style="margin:0;" onsubmit="return confirm('Hapus komponen ini beserta semua sub dan indikatornya?')">
                                        <input type="hidden" name="id" value="<?php echo $komp['id']; ?>">
                                        <button type="submit" class="btn btn-sm" style="padding: 4px 8px; font-size:0.8rem; background:#fee2e2; color:#ef4444; border:none; border-radius:6px;" title="Hapus Komponen"><i data-lucide="trash-2" style="width:14px;height:14px;"></i></button>
                                    </form>
                                </div>
                            </div>

                        <?php foreach($komp['sub_komponens'] as $sub): ?>
                        <div class="sub-komponen-box">
                            <div class="sub-komponen-header">
                                <div>
                                    <span class="badge-num"><?php echo htmlspecialchars($sub['urutan']); ?></span>
                                    <?php echo htmlspecialchars($sub['nama_sub']); ?>
                                </div>
                                <div style="display:flex; gap:8px; align-items:center;">
                                    <button class="btn btn-sm btn-outline-success" onclick="openModalIndikator(<?php echo $komp['id']; ?>, <?php echo $sub['id']; ?>)" style="padding: 3px 8px; font-size:0.75rem; font-weight:600; border-radius:6px;">+ Indikator</button>
                                    <button class="btn btn-sm" onclick="editSubKomponen(<?php echo $sub['id']; ?>, <?php echo $komp['id']; ?>, '<?php echo htmlspecialchars(addslashes($sub['nama_sub'])); ?>', '<?php echo htmlspecialchars($sub['urutan']); ?>')" style="padding: 3px 8px; font-size:0.75rem; background:#e2e8f0; color:#475569; border:none; border-radius:6px;" title="Edit Sub"><i data-lucide="edit" style="width:12px;height:12px;"></i></button>
                                    <form action="/admin/pkkm/subkomponen/delete" method="POST" style="margin:0;" onsubmit="return confirm('Hapus sub-komponen ini beserta semua indikator di dalamnya?')">
                                        <input type="hidden" name="id" value="<?php echo $sub['id']; ?>">
                                        <button type="submit" class="btn btn-sm" style="padding: 3px 8px; font-size:0.75rem; background:#fee2e2; color:#ef4444; border:none; border-radius:6px;" title="Hapus Sub"><i data-lucide="trash-2" style="width:12px;height:12px;"></i></button>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm" style="font-size: 0.9rem;">
                                    <thead style="background: #f1f5f9;">
                                        <tr>
                                            <th width="28%">Kriteria Penilaian</th>
                                            <th width="42%">Bukti Fisik (Dokumen)</th>
                                            <th width="20%">File Terlampir</th>
                                            <th width="10%" class="text-center">Skor (1-4)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($sub['indikators'])): ?>
                                        <tr><td colspan="4" class="text-center" style="color:#94a3b8; padding:15px;">Belum ada indikator.</td></tr>
                                        <?php else: foreach($sub['indikators'] as $ind): ?>
                                            <?php include 'pkkm_ind_row.php'; ?>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <?php if(!empty($komp['indikators'])): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" style="font-size: 0.9rem;">
                                <thead style="background: #f1f5f9;">
                                    <tr>
                                        <th width="28%">Kriteria Penilaian</th>
                                        <th width="42%">Bukti Fisik (Dokumen)</th>
                                        <th width="20%">File Terlampir</th>
                                        <th width="10%" class="text-center">Skor (1-4)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($komp['indikators'] as $ind): ?>
                                        <?php include 'pkkm_ind_row.php'; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>                        </div> <!-- End tab-pane -->
                    <?php $i++; endforeach; ?>
                    </div> <!-- End tab-content -->
                <?php endif; ?>
            </div>

        </div>
    </main>
</div>

<!-- Modal Komponen -->
<div class="modal fade" id="modalKomponen" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/pkkm/komponen/save" method="POST">
                <input type="hidden" name="id" id="inp_komp_id" value="0">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalKomponenTitle">Tambah Komponen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label">Nama Komponen</label>
                        <input type="text" name="nama_komponen" id="inp_komp_nama" class="form-control" required placeholder="Contoh: Usaha Pengembangan Madrasah">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Urutan</label>
                        <input type="number" name="urutan" id="inp_komp_urutan" class="form-control" required value="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sub Komponen -->
<div class="modal fade" id="modalSubKomponen" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/pkkm/subkomponen/save" method="POST">
                <input type="hidden" name="id" id="inp_sub_id" value="0">
                <input type="hidden" name="komponen_id" id="inp_sub_komponen_id" value="0">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSubKomponenTitle">Tambah Sub-Komponen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label">Urutan / Nomor (contoh: 1.1)</label>
                        <input type="text" name="urutan" id="inp_sub_urutan" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Sub-Komponen</label>
                        <textarea name="nama_sub" id="inp_sub_nama" class="form-control" required rows="2" placeholder="Contoh: Mengembangkan madrasah sesuai dengan kebutuhan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Indikator -->
<div class="modal fade" id="modalIndikator" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="/admin/pkkm/indikator/save" method="POST">
                <input type="hidden" name="id" id="inp_ind_id" value="0">
                <input type="hidden" name="komponen_id" id="inp_komponen_id">
                <input type="hidden" name="sub_komponen_id" id="inp_ind_sub_komponen_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalIndikatorTitle">Tambah Indikator</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label">Indikator Kinerja</label>
                        <textarea name="indikator" id="inp_ind_nama" class="form-control" required rows="2"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Kriteria Penilaian</label>
                        <textarea name="kriteria" id="inp_ind_kriteria" class="form-control" required rows="3"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Dokumen Bukti Fisik yang Diminta</label>
                        <textarea name="bukti_fisik" id="inp_ind_bukti" class="form-control" required rows="3" placeholder="1. Dokumen A...&#10;2. Dokumen B..."></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor Urut</label>
                        <input type="number" name="urutan" id="inp_ind_urutan" class="form-control" required value="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Berkas -->
<div class="modal fade" id="modalBerkas" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/pkkm/berkas/save" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="indikator_id" id="inp_berkas_indikator_id">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Bukti Fisik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="box_panduan_modal_berkas" style="display:none;"></div>
                    <div class="form-group mb-3">
                        <label class="form-label">Tipe Lampiran</label>
                        <select name="tipe" id="tipe_lampiran" class="form-control" onchange="toggleTipeLampiran()">
                            <option value="file">Upload File (PDF/Gambar/Word/Excel)</option>
                            <option value="link">Tautan GDrive / Tautan Web Eksternal</option>
                        </select>
                    </div>
                    
                    <div id="wrap_file">
                        <div class="form-group">
                            <label class="form-label">Pilih File (Bisa pilih banyak file sekaligus)</label>
                            <input type="file" name="file_upload[]" multiple="multiple" class="form-control">
                            <small class="text-muted">Maksimal file yang disarankan < 10MB per file. Foto akan otomatis dikompres.</small>
                        </div>
                    </div>
                    
                    <div id="wrap_link" style="display:none;">
                        <div class="form-group">
                            <label class="form-label">Link Tautan</label>
                            <input type="url" name="url" class="form-control" placeholder="https://drive.google.com/...">
                        </div>
                    </div>
                    
                    <div class="form-group mt-3 mb-2">
                        <label class="form-label">Judul/Label File (Opsional)</label>
                        <input type="text" name="nama_file" class="form-control" placeholder="Contoh: Dokumen Hasil Analisis">
                        <small class="text-muted" style="font-size:0.75rem;">Biarkan kosong jika ingin menggunakan nama asli file.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" style="background:#38bdf8; border:none;">Simpan Lampiran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function zToggleSidebar(){document.getElementById('zSidebar').classList.toggle('open');document.getElementById('zOverlay').classList.toggle('open');}
    lucide.createIcons();

    function openModalKomponen() {
        document.getElementById('inp_komp_id').value = "0";
        document.getElementById('inp_komp_nama').value = "";
        document.getElementById('inp_komp_urutan').value = "1";
        document.getElementById('modalKomponenTitle').innerText = "Tambah Komponen";
        new bootstrap.Modal(document.getElementById('modalKomponen')).show();
    }

    function editKomponen(id, nama, urutan) {
        document.getElementById('inp_komp_id').value = id;
        document.getElementById('inp_komp_nama').value = nama;
        document.getElementById('inp_komp_urutan').value = urutan;
        document.getElementById('modalKomponenTitle').innerText = "Edit Komponen";
        new bootstrap.Modal(document.getElementById('modalKomponen')).show();
    }

    function openModalSubKomponen(komponen_id) {
        document.getElementById('inp_sub_id').value = "0";
        document.getElementById('inp_sub_komponen_id').value = komponen_id;
        document.getElementById('inp_sub_urutan').value = "";
        document.getElementById('inp_sub_nama').value = "";
        document.getElementById('modalSubKomponenTitle').innerText = "Tambah Sub-Komponen";
        new bootstrap.Modal(document.getElementById('modalSubKomponen')).show();
    }

    function editSubKomponen(id, komponen_id, nama_sub, urutan) {
        document.getElementById('inp_sub_id').value = id;
        document.getElementById('inp_sub_komponen_id').value = komponen_id;
        document.getElementById('inp_sub_urutan').value = urutan;
        document.getElementById('inp_sub_nama').value = nama_sub;
        document.getElementById('modalSubKomponenTitle').innerText = "Edit Sub-Komponen";
        new bootstrap.Modal(document.getElementById('modalSubKomponen')).show();
    }

    function openModalIndikator(komponen_id, sub_komponen_id = null) {
        document.getElementById('inp_ind_id').value = "0";
        document.getElementById('inp_komponen_id').value = komponen_id;
        document.getElementById('inp_ind_sub_komponen_id').value = sub_komponen_id || "";
        document.getElementById('inp_ind_nama').value = "";
        document.getElementById('inp_ind_kriteria').value = "";
        document.getElementById('inp_ind_bukti').value = "";
        document.getElementById('inp_ind_urutan').value = "1";
        document.getElementById('modalIndikatorTitle').innerText = "Tambah Indikator";
        new bootstrap.Modal(document.getElementById('modalIndikator')).show();
    }

    function editIndikator(id, komp_id, sub_komp_id, nama, kriteria, bukti, urutan) {
        document.getElementById('inp_ind_id').value = id;
        document.getElementById('inp_komponen_id').value = komp_id;
        document.getElementById('inp_ind_sub_komponen_id').value = sub_komp_id || "";
        document.getElementById('inp_ind_nama').value = nama;
        document.getElementById('inp_ind_kriteria').value = kriteria;
        document.getElementById('inp_ind_bukti').value = bukti;
        document.getElementById('inp_ind_urutan').value = urutan;
        document.getElementById('modalIndikatorTitle').innerText = "Edit Indikator";
        new bootstrap.Modal(document.getElementById('modalIndikator')).show();
    }

    function openModalBerkas(indikator_id, optionalNamaDokumen = null) {
        document.getElementById('inp_berkas_indikator_id').value = indikator_id;
        document.getElementById('tipe_lampiran').value = 'file';
        toggleTipeLampiran();
        
        if (typeof renderGuideBoxInUploadModal === 'function') {
            renderGuideBoxInUploadModal(optionalNamaDokumen, 'box_panduan_modal_berkas');
        }
        
        new bootstrap.Modal(document.getElementById('modalBerkas')).show();
    }

    function toggleTipeLampiran() {
        var tipe = document.getElementById('tipe_lampiran').value;
        if (tipe === 'file') {
            document.getElementById('wrap_file').style.display = 'block';
            document.getElementById('wrap_link').style.display = 'none';
        } else {
            document.getElementById('wrap_file').style.display = 'none';
            document.getElementById('wrap_link').style.display = 'block';
        }
    }

    function saveSkor(indikator_id, skor) {
        $.ajax({
            url: '/admin/pkkm/nilai/save',
            type: 'POST',
            data: {indikator_id: indikator_id, skor: skor},
            success: function(res) {
                // Berhasil disimpan (background save)
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>

<?php include (file_exists(__DIR__ . '/_pkkm_panduan_modal.php') ? __DIR__ . '/_pkkm_panduan_modal.php' : __DIR__ . '/pkkm/_pkkm_panduan_modal.php'); ?>
</body>
</html>
