<?php
$active_module = 'pkkm';
$active_page = 'bukti_digital';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title = 'Bukti Digital & TIK PKKM'; include __DIR__ . '/_zamrud_head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #0f172a; }
        .z-scroll { padding-bottom: 3rem !important; }

        /* Banner Header */
        .elite-page-header {
            position: relative; padding: 2rem 2.5rem; margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #064e3b 0%, #047857 50%, #0f766e 100%);
            border-radius: 20px; overflow: hidden; color: #ffffff;
            box-shadow: 0 12px 30px -8px rgba(6, 78, 59, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .elite-page-header .header-bg-shapes {
            position: absolute; top: 0; right: 0; bottom: 0; left: 0;
            background-image: radial-gradient(circle at 85% 20%, rgba(255, 255, 255, 0.18) 0%, transparent 40%),
                              radial-gradient(circle at 15% 80%, rgba(0, 0, 0, 0.12) 0%, transparent 40%);
            z-index: 1; pointer-events: none;
        }
        .elite-page-header .header-content-inner {
            position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap;
        }
        .elite-page-header .header-text-premium h1 {
            font-size: 1.75rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 12px; color: #fff;
        }
        .elite-page-header .header-text-premium p {
            font-size: 0.9rem; color: rgba(255,255,255,0.88); margin-top: 0.35rem; margin-bottom: 0; max-width: 680px; font-weight: 400; line-height: 1.5;
        }
        .header-tag-pill {
            display: inline-flex; align-items: center; gap: 6px; background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.3); color: #fff; font-size: 0.72rem; font-weight: 800; padding: 4px 12px; border-radius: 999px; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 8px; backdrop-filter: blur(4px);
        }

        .e-btn {
            display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px;
            border-radius: 12px; font-weight: 700; font-size: 0.85rem; cursor: pointer;
            border: none; transition: 0.2s ease; text-decoration: none;
        }
        .e-btn:hover { transform: translateY(-2px); }
        .e-btn-primary { background: #ffffff; color: #047857; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12); }
        .e-btn-primary:hover { background: #f8fafc; color: #064e3b; }
        .e-btn-secondary { background: rgba(255, 255, 255, 0.18); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(4px); }
        .e-btn-secondary:hover { background: rgba(255, 255, 255, 0.28); color: #ffffff; }

        /* Stat Grid */
        .stat-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-bottom: 24px;
        }
        .stat-box {
            padding: 14px 18px; border-radius: 14px; display: flex; align-items: center; gap: 14px; background: #fff; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }
        .stat-box.green { background: #f0fdf4; border-color: #dcfce7; color: #14532d; }
        .stat-box.blue { background: #eff6ff; border-color: #dbeafe; color: #1e3a8a; }
        .stat-box.yellow { background: #fffbeb; border-color: #fef3c7; color: #92400e; }
        .stat-box.purple { background: #faf5ff; border-color: #f3e8ff; color: #6b21a8; }
        .stat-value { font-size: 1.5rem; font-weight: 800; line-height: 1; }
        .stat-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-top: 4px; opacity: 0.8; letter-spacing: 0.5px; }

        /* Filter & Search Bar */
        .filter-panel-card {
            background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0;
            padding: 16px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.03); margin-bottom: 24px;
            display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;
        }
        .cat-pills-wrap {
            display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
        }
        .cat-pill {
            padding: 6px 14px; border-radius: 20px; font-size: 0.78rem; font-weight: 700;
            background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; cursor: pointer;
            transition: all 0.15s ease;
        }
        .cat-pill:hover { background: #e2e8f0; color: #0f172a; }
        .cat-pill.active {
            background: #059669; color: #ffffff; border-color: #059669;
            box-shadow: 0 2px 6px rgba(5, 150, 105, 0.25);
        }
        .search-input-box {
            position: relative; min-width: 240px; flex: 1; max-width: 360px;
        }
        .search-input-box input {
            width: 100%; padding: 8px 12px 8px 34px; border-radius: 10px;
            border: 1px solid #cbd5e1; font-size: 0.85rem; font-family: inherit;
            background: #f8fafc; transition: all 0.2s;
        }
        .search-input-box input:focus {
            outline: none; border-color: #10b981; background: #fff;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        }
        .search-input-box i {
            position: absolute; left: 10px; top: 50%; transform: translateY(-50%);
            color: #94a3b8; width: 16px; height: 16px;
        }

        /* Digital Proof Cards Grid */
        .digital-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 20px;
        }
        .digital-card {
            background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.03); overflow: hidden;
            display: flex; flex-direction: column; transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
        }
        .digital-card:hover {
            transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,0,0,0.06);
            border-color: #cbd5e1;
        }
        .digital-card-top {
            padding: 18px 20px 14px 20px; flex: 1;
        }
        .digital-card-header {
            display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 12px;
        }
        .icon-box-digital {
            width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; flex-shrink: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .digital-meta-tags {
            display: flex; flex-direction: column; align-items: flex-end; gap: 4px;
        }
        .badge-cat {
            font-size: 0.7rem; font-weight: 700; padding: 2px 8px; border-radius: 6px;
            background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;
        }
        .badge-ind {
            font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 6px;
            background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;
        }
        .digital-title {
            font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0 0 6px 0; line-height: 1.35;
        }
        .digital-desc {
            font-size: 0.82rem; color: #64748b; line-height: 1.45; margin-bottom: 14px;
        }
        .url-box {
            background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;
            padding: 6px 10px; display: flex; align-items: center; justify-content: space-between; gap: 8px;
            font-size: 0.76rem; font-family: monospace; color: #334155; margin-bottom: 14px;
        }
        .url-text {
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 600;
        }
        .btn-copy-url {
            background: transparent; border: none; color: #059669; font-weight: 700;
            font-size: 0.72rem; cursor: pointer; padding: 2px 6px; border-radius: 4px;
            display: inline-flex; align-items: center; gap: 4px;
        }
        .btn-copy-url:hover { background: #e2e8f0; }

        /* Attachment Status Bar */
        .attachment-status-bar {
            padding: 8px 12px; border-radius: 8px; font-size: 0.76rem;
            display: flex; align-items: center; justify-content: space-between; gap: 8px;
            margin-bottom: 14px;
        }
        .status-attached {
            background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46;
        }
        .status-unattached {
            background: #fffbeb; border: 1px solid #fde68a; color: #92400e;
        }

        /* Card Actions Footer */
        .digital-card-footer {
            padding: 12px 20px; background: #fafafa; border-top: 1px solid #f1f5f9;
            display: flex; align-items: center; justify-content: space-between; gap: 8px;
        }
        .btn-action-view {
            padding: 7px 14px; border-radius: 8px; font-size: 0.8rem; font-weight: 700;
            background: #059669; color: #ffffff; text-decoration: none; display: inline-flex;
            align-items: center; gap: 6px; transition: background 0.15s;
        }
        .btn-action-view:hover { background: #047857; color: #ffffff; }
        .btn-action-attach {
            padding: 7px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 600;
            background: #ffffff; color: #334155; border: 1px solid #cbd5e1; cursor: pointer;
            display: inline-flex; align-items: center; gap: 4px; transition: all 0.15s;
        }
        .btn-action-attach:hover { background: #f1f5f9; color: #0f172a; border-color: #94a3b8; }
    </style>
</head>
<body>

<div class="z-wrap">
    <?php include __DIR__ . '/_zamrud_sidebar.php'; ?>

    <main class="z-main">
        <?php $page_badge = 'Admin Portal'; include __DIR__ . '/../siakad/_siakad_header.php'; ?>
        <div class="z-scroll">

            <!-- ELITE PAGE HEADER -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-tag-pill">
                            <i data-lucide="sparkles" style="width:13px;height:13px;"></i> PORTAL BUKTI DIGITAL PKKM
                        </div>
                        <h1>
                            <i data-lucide="shield-check"></i> Bukti Digital & Portal TIK
                        </h1>
                        <p>
                            Pusat tautan berkas resmi digital, display monitoring perangkat guru, dan lembar verifikasi sistem PKKM. Dikelola dalam satu menu khusus agar tidak tercampur dan memudahkan penelaahan asesor/pengawas.
                        </p>
                    </div>
                    <div class="header-actions-premium d-flex gap-2">
                        <a href="/admin/pkkm/master-berkas" class="e-btn e-btn-secondary">
                            <i data-lucide="folder-open"></i> Daftar Berkas Master
                        </a>
                        <a href="/admin/pkkm/instrumen" class="e-btn e-btn-primary">
                            <i data-lucide="clipboard-list"></i> Instrumen PKKM
                        </a>
                    </div>
                </div>
            </div>

            <!-- STAT SUMMARY CARDS -->
            <div class="stat-grid">
                <div class="stat-box purple">
                    <div class="stat-icon"><i data-lucide="sparkles" style="width:24px;height:24px;"></i></div>
                    <div>
                        <div class="stat-value"><?= count($buktiList); ?></div>
                        <div class="stat-label">Total Bukti Digital</div>
                    </div>
                </div>
                <div class="stat-box green">
                    <div class="stat-icon"><i data-lucide="check-circle-2" style="width:24px;height:24px;"></i></div>
                    <div>
                        <div class="stat-value"><?= $totalAttached ?? 0; ?></div>
                        <div class="stat-label">Sudah Tertaut Master</div>
                    </div>
                </div>
                <div class="stat-box yellow">
                    <div class="stat-icon"><i data-lucide="clock" style="width:24px;height:24px;"></i></div>
                    <div>
                        <div class="stat-value"><?= count($buktiList) - ($totalAttached ?? 0); ?></div>
                        <div class="stat-label">Belum Tertaut</div>
                    </div>
                </div>
                <div class="stat-box blue">
                    <div class="stat-icon"><i data-lucide="activity" style="width:24px;height:24px;"></i></div>
                    <div>
                        <div class="stat-value">100%</div>
                        <div class="stat-label">Kesiapan Sistem Digital</div>
                    </div>
                </div>
            </div>

            <!-- FILTER & SEARCH PANEL -->
            <div class="filter-panel-card">
                <div class="cat-pills-wrap">
                    <button type="button" class="cat-pill active" onclick="filterCategory('all', this)">
                        Semua Bukti (<?= count($buktiList) ?>)
                    </button>
                    <button type="button" class="cat-pill" onclick="filterCategory('Kurikulum & Pembelajaran', this)">
                        📖 Kurikulum & KBM
                    </button>
                    <button type="button" class="cat-pill" onclick="filterCategory('Sarana TIK & Publikasi', this)">
                        💻 TIK & Publikasi
                    </button>
                    <button type="button" class="cat-pill" onclick="filterCategory('Kelembagaan & Regulasi', this)">
                        🏛️ Kelembagaan
                    </button>
                    <button type="button" class="cat-pill" onclick="filterCategory('Ketenagaan & Disiplin', this)">
                        👥 Guru & Tendik
                    </button>
                    <button type="button" class="cat-pill" onclick="filterCategory('Kesiswaan & Prestasi', this)">
                        🏆 Kesiswaan
                    </button>
                </div>

                <div class="search-input-box">
                    <i data-lucide="search"></i>
                    <input type="text" id="inpCari" placeholder="Cari nama bukti atau indikator..." onkeyup="filterLive()">
                </div>
            </div>

            <!-- CARDS GRID -->
            <div class="digital-grid" id="digitalGrid">
                <?php foreach($buktiList as $b): ?>
                <div class="digital-card" 
                     data-category="<?= htmlspecialchars($b['kategori']) ?>" 
                     data-title="<?= htmlspecialchars(strtolower($b['nama'])) ?>"
                     data-indikator="<?= htmlspecialchars(strtolower($b['indikator'])) ?>">
                    
                    <div class="digital-card-top">
                        <div class="digital-card-header">
                            <div class="icon-box-digital" style="background: <?= $b['bg'] ?>; color: <?= $b['color'] ?>;">
                                <i data-lucide="<?= $b['icon'] ?>" style="width: 22px; height: 22px;"></i>
                            </div>
                            <div class="digital-meta-tags">
                                <span class="badge-ind"><?= htmlspecialchars($b['indikator']) ?></span>
                                <span class="badge-cat"><?= htmlspecialchars($b['kategori']) ?></span>
                            </div>
                        </div>

                        <h3 class="digital-title"><?= htmlspecialchars($b['nama']) ?></h3>
                        <p class="digital-desc"><?= htmlspecialchars($b['deskripsi']) ?></p>

                        <!-- URL Box -->
                        <div class="url-box">
                            <span class="url-text"><?= htmlspecialchars($b['url']) ?></span>
                            <button type="button" class="btn-copy-url" onclick="copyLink('<?= htmlspecialchars($b['url']) ?>', this)">
                                <i data-lucide="copy" style="width: 12px; height: 12px;"></i> Salin
                            </button>
                        </div>

                        <!-- Status Keterkaitan Master Berkas -->
                        <?php if($b['is_attached']): ?>
                            <div class="attachment-status-bar status-attached">
                                <div style="display:flex; align-items:center; gap:6px; overflow:hidden;">
                                    <i data-lucide="check-circle" style="width:14px; height:14px; color:#059669; flex-shrink:0;"></i>
                                    <span style="font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                        Tertaut: <?= htmlspecialchars($b['attached_to'][0]['nama_master'] ?? 'Master Berkas'); ?>
                                        <?php if(count($b['attached_to']) > 1): ?>
                                            (+<?= count($b['attached_to'])-1 ?> lainnya)
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <form action="/admin/pkkm/bukti-digital/detach" method="POST" class="m-0" onsubmit="return confirm('Lepas tautan bukti digital ini dari master berkas?')">
                                    <input type="hidden" name="file_id" value="<?= $b['attached_to'][0]['id'] ?>">
                                    <button type="submit" style="background:none; border:none; color:#ef4444; font-size:0.72rem; font-weight:700; cursor:pointer; padding:0;">
                                        Lepas
                                    </button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="attachment-status-bar status-unattached">
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <i data-lucide="alert-circle" style="width:14px; height:14px; color:#d97706; flex-shrink:0;"></i>
                                    <span style="font-weight:700;">Belum tertaut ke Master Berkas</span>
                                </div>
                                <span style="font-size:0.7rem; color:#b45309;">Siap ditautkan</span>
                            </div>
                        <?php endif; ?>

                    </div>

                    <!-- Card Footer Actions -->
                    <div class="digital-card-footer">
                        <a href="<?= htmlspecialchars($b['url']) ?>" target="_blank" class="btn-action-view">
                            <i data-lucide="external-link" style="width: 14px; height: 14px;"></i>
                            <span>Buka / Preview</span>
                        </a>

                        <button type="button" class="btn-action-attach" onclick="openAttachModal(<?= htmlspecialchars(json_encode([
                            'id' => $b['id'],
                            'nama' => $b['nama'],
                            'url' => $b['url'],
                            'recommended' => $b['recommended_masters']
                        ])) ?>)">
                            <i data-lucide="link" style="width: 14px; height: 14px;"></i>
                            <span><?= $b['is_attached'] ? 'Tautkan Lagi' : 'Tautkan Master' ?></span>
                        </button>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>

        </div>
    </main>
</div>

<!-- MODAL TAUTKAN KE MASTER BERKAS -->
<div class="modal fade" id="modalAttachDigital" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:18px; overflow:hidden; border:1px solid #e2e8f0;">
            <div class="modal-header" style="background:#0f172a; color:#fff;">
                <h5 class="modal-title fs-6 fw-bold d-flex align-items-center gap-2">
                    <i data-lucide="link" style="color:#34d399; width:18px; height:18px;"></i>
                    <span>Tautkan Bukti Digital ke Master Berkas</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="/admin/pkkm/bukti-digital/attach" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="url" id="modalAttachUrl">
                    <input type="hidden" name="nama_file" id="modalAttachNama">

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">BUKTI DIGITAL YANG DITAUTKAN</label>
                        <div id="modalAttachLabel" class="p-2 rounded bg-light border fw-bold text-dark small">
                            -
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">PILIH DOKUMEN MASTER BERKAS PKKM</label>
                        <select name="master_berkas_id" id="modalSelectMaster" class="form-select form-select-sm" required style="font-size:0.85rem;">
                            <option value="">-- Pilih Dokumen Master Berkas --</option>
                            <?php foreach($allMaster as $m): ?>
                                <option value="<?= $m['id'] ?>">
                                    <?= htmlspecialchars($m['nama_berkas']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text" style="font-size:0.75rem;">
                            Setelah ditautkan, bukti digital ini akan otomatis diakui sebagai lampiran fisik resmi untuk indikator terkait.
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-success fw-bold px-3 d-inline-flex align-items-center gap-1">
                        <i data-lucide="check" style="width:14px; height:14px;"></i> Simpan Tautan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
var currentCategory = 'all';

function filterCategory(cat, btn) {
    currentCategory = cat;
    document.querySelectorAll('.cat-pill').forEach(function(el) {
        el.classList.remove('active');
    });
    btn.classList.add('active');
    filterLive();
}

function filterLive() {
    var query = document.getElementById('inpCari').value.toLowerCase().trim();
    var cards = document.querySelectorAll('.digital-card');

    cards.forEach(function(card) {
        var cat = card.getAttribute('data-category');
        var title = card.getAttribute('data-title');
        var ind = card.getAttribute('data-indikator');

        var matchCat = (currentCategory === 'all' || cat.indexOf(currentCategory) !== -1);
        var matchQuery = (query === '' || title.indexOf(query) !== -1 || ind.indexOf(query) !== -1);

        if (matchCat && matchQuery) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}

function copyLink(path, btn) {
    var fullUrl = window.location.origin + path;
    navigator.clipboard.writeText(fullUrl).then(function() {
        var originalHtml = btn.innerHTML;
        btn.innerHTML = '<i data-lucide="check" style="width:12px;height:12px;"></i> Tersalin!';
        if (typeof lucide !== 'undefined') lucide.createIcons();
        setTimeout(function() {
            btn.innerHTML = originalHtml;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }, 2000);
    });
}

function openAttachModal(data) {
    document.getElementById('modalAttachUrl').value = data.url;
    document.getElementById('modalAttachNama').value = data.nama;
    document.getElementById('modalAttachLabel').innerText = data.nama + ' (' + data.url + ')';

    var select = document.getElementById('modalSelectMaster');
    if (data.recommended && data.recommended.length > 0) {
        select.value = data.recommended[0].id;
    } else {
        select.value = '';
    }

    new bootstrap.Modal(document.getElementById('modalAttachDigital')).show();
}

document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    <?php if(isset($_GET['msg'])): ?>
        if (typeof Swal !== 'undefined') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            <?php if($_GET['msg'] === 'attached'): ?>
                Toast.fire({ icon: 'success', title: 'Bukti digital berhasil ditautkan ke Master Berkas!' });
            <?php elseif($_GET['msg'] === 'detached'): ?>
                Toast.fire({ icon: 'info', title: 'Tautan bukti digital berhasil dilepas.' });
            <?php elseif($_GET['msg'] === 'error'): ?>
                Toast.fire({ icon: 'error', title: 'Terjadi kesalahan saat memproses data.' });
            <?php endif; ?>
            window.history.replaceState(null, null, window.location.pathname);
        }
    <?php endif; ?>
});
</script>
</body>
</html>
