<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title = 'Master Instrumen Supervisi'; include __DIR__ . '/../admin/_zamrud_head.php'; ?>
    <style>
        .filter-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
            cursor: pointer;
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #475569;
            transition: all 0.2s;
            text-decoration: none;
        }
        .filter-pill:hover {
            border-color: #059669;
            color: #059669;
        }
        .filter-pill.active {
            background: #059669;
            border-color: #059669;
            color: #fff;
            box-shadow: 0 4px 10px rgba(5,150,105,0.25);
        }
        .filter-badge {
            background: rgba(0,0,0,0.08);
            padding: 2px 7px;
            border-radius: 999px;
            font-size: 0.74rem;
        }
        .filter-pill.active .filter-badge {
            background: rgba(255,255,255,0.25);
            color: #fff;
        }
        @keyframes modalIn {
            from { opacity: 0; transform: translateY(-16px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
<?php $page_badge = 'Kurikulum'; ?>
<div class="z-wrap">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../siakad/_siakad_header.php'; ?>
        
        <div style="padding: 24px;">
            <!-- Header Halaman -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-badge">SUPERVISI &bull; MASTER DATA</div>
                        <h1><i data-lucide="clipboard-list"></i> Master Instrumen Supervisi</h1>
                        <p>Kelola indikator, aspek penilaian, dan butir evaluasi supervisi akademik MAS Miftahul Huda.</p>
                    </div>
                    <div class="header-actions-premium" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                        <a href="/kurikulum/supervisi/laporan-hasil" class="e-btn" style="background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.35); color: #fff; text-decoration: none;" title="Buka Dokumen Laporan Hasil Supervisi Bab I - IV">
                            <i data-lucide="file-bar-chart-2"></i> Laporan Hasil
                        </a>
                        <button class="e-btn e-btn-primary" onclick="document.getElementById('modalInstrumen').style.display='flex'">
                            <i data-lucide="plus-circle"></i> Tambah Instrumen
                        </button>
                    </div>
                </div>
            </div>

            <!-- Flash Alert -->
            <?php if (!empty($_SESSION['alert'])): ?>
                <?php $aType = $_SESSION['alert']['type'] ?? 'info'; ?>
                <div style="padding: 12px 18px; margin-top: 1.5rem; border-radius: 10px; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 10px; <?= $aType === 'success' ? 'background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;' : ($aType === 'warning' ? 'background: #fffbeb; color: #92400e; border: 1px solid #fde68a;' : 'background: #fef2f2; color: #991b1b; border: 1px solid #fecaca;') ?>">
                    <i data-lucide="<?= $aType === 'success' ? 'check-circle' : ($aType === 'warning' ? 'alert-triangle' : 'alert-circle') ?>" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
                    <span style="line-height: 1.4;"><?= htmlspecialchars($_SESSION['alert']['message']) ?></span>
                </div>
                <?php unset($_SESSION['alert']); ?>
            <?php endif; ?>

            <?php
            // Hitung total per tipe
            $countTotal = count($instrumen ?? []);
            $countKelas = 0;
            $countAdmin = 0;
            $countLain  = 0;
            if (!empty($instrumen)) {
                foreach ($instrumen as $ins) {
                    $tp = strtolower(trim($ins['tipe_supervisi'] ?? ''));
                    if ($tp === 'kelas') $countKelas++;
                    elseif ($tp === 'administrasi') $countAdmin++;
                    else $countLain++;
                }
            }
            ?>

            <!-- Filter Pills -->
            <div style="display: flex; gap: 8px; align-items: center; margin-top: 1.5rem; flex-wrap: wrap;">
                <button type="button" class="filter-pill active" onclick="setFilter('all', this)">
                    <i data-lucide="layers" style="width: 14px; height: 14px;"></i> Semua
                    <span class="filter-badge"><?= $countTotal ?></span>
                </button>
                <button type="button" class="filter-pill" onclick="setFilter('Kelas', this)">
                    <i data-lucide="video" style="width: 14px; height: 14px;"></i> Supervisi Kelas (KBM)
                    <span class="filter-badge"><?= $countKelas ?></span>
                </button>
                <button type="button" class="filter-pill" onclick="setFilter('Administrasi', this)">
                    <i data-lucide="file-check-2" style="width: 14px; height: 14px;"></i> Supervisi Administrasi
                    <span class="filter-badge"><?= $countAdmin ?></span>
                </button>
                <?php if ($countLain > 0): ?>
                <button type="button" class="filter-pill" onclick="setFilter('lain', this)">
                    <i data-lucide="sparkles" style="width: 14px; height: 14px;"></i> Tipe Khusus / Universal
                    <span class="filter-badge"><?= $countLain ?></span>
                </button>
                <?php endif; ?>
            </div>
            
            <div class="z-panel" style="margin-top: 1rem;">
                <div class="z-panel-head" style="display: flex; justify-content: space-between; align-items: center;">
                    <div class="z-panel-title">
                        <i data-lucide="list"></i> Daftar Butir & Indikator Supervisi
                    </div>
                    <div style="font-size: 0.8rem; color: #64748b; font-weight: 600;">
                        Menampilkan <span id="visibleCount"><?= $countTotal ?></span> instrumen
                    </div>
                </div>
                <div style="padding: 0; overflow-x: auto;">
                    <table class="z-table">
                        <thead>
                            <tr style="background: #f8fafc;">
                                <th style="width: 50px; text-align: center;">No</th>
                                <th style="width: 140px;">Tipe Supervisi</th>
                                <th style="width: 180px;">Kategori & Kode</th>
                                <th>Aspek Penilaian / Indikator</th>
                                <th style="width: 90px; text-align: center;">Skor Maks</th>
                                <th style="width: 140px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="instrumenTbody">
                            <?php if (isset($instrumen) && count($instrumen) > 0): ?>
                                <?php $no = 1; foreach ($instrumen as $ins): ?>
                                <tr class="row-instrumen" data-tipe="<?= htmlspecialchars($ins['tipe_supervisi'] ?? '') ?>">
                                    <td style="text-align: center; color: #64748b; font-weight: 600;"><?= $no++ ?></td>
                                    <td>
                                        <?php if ($ins['tipe_supervisi'] === 'Kelas'): ?>
                                            <span style="display:inline-block; font-weight: 700; color: #0284c7; background: #e0f2fe; padding: 3px 8px; border-radius: 6px; font-size: 0.78rem;">Kelas (KBM)</span>
                                        <?php elseif ($ins['tipe_supervisi'] === 'Administrasi'): ?>
                                            <span style="display:inline-block; font-weight: 700; color: #059669; background: #d1fae5; padding: 3px 8px; border-radius: 6px; font-size: 0.78rem;">Administrasi</span>
                                        <?php else: ?>
                                            <span style="display:inline-block; font-weight: 700; color: #7c3aed; background: #ede9fe; padding: 3px 8px; border-radius: 6px; font-size: 0.78rem;"><?= htmlspecialchars($ins['tipe_supervisi']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.78rem; color: #64748b; font-weight: 700; text-transform: uppercase; margin-bottom: 3px;">
                                            <?= htmlspecialchars($ins['kategori'] ?: 'UMUM') ?>
                                        </div>
                                        <span style="font-weight: 700; font-family: monospace; background:#f1f5f9; color:#0f172a; padding:2px 7px; border-radius:4px; font-size: 0.8rem; border: 1px solid #e2e8f0;">
                                            #<?= htmlspecialchars($ins['kode']) ?>
                                        </span>
                                    </td>
                                    <td style="line-height: 1.5; color: #1e293b;">
                                        <?= nl2br(htmlspecialchars($ins['aspek'])) ?>
                                    </td>
                                    <td align="center" style="font-weight: 800; color: #059669;">
                                        <?= htmlspecialchars($ins['bobot_maksimal'] ?? 5) ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display: inline-flex; gap: 6px; align-items: center; justify-content: center;">
                                            <button type="button" class="e-btn e-btn-outline" style="padding: 4px 10px; font-size: 0.8rem; color: #0284c7; border-color: #bae6fd; background: #f0f9ff;" onclick='openEditModal(<?= htmlspecialchars(json_encode($ins), ENT_QUOTES, "UTF-8") ?>)' title="Ubah redaksi indikator ini (nilai hasil tetap aman)">
                                                <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i> Edit
                                            </button>
                                            <form action="/kurikulum/supervisi/instrumen/delete" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('Yakin ingin menghapus instrumen kode <?= htmlspecialchars($ins['kode']) ?>?\n\nPerhatian: Jika instrumen sudah dinilai guru, sistem akan mencegah penghapusan agar nilai tidak hilang.');">
                                                <input type="hidden" name="id" value="<?= htmlspecialchars($ins['id']) ?>">
                                                <button type="submit" class="e-btn e-btn-outline" style="padding: 4px 8px; color: #ef4444; border-color: #fca5a5; background: #fff5f5;" title="Hapus instrumen">
                                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" align="center" style="padding: 3rem 1rem;">
                                        <div style="color: var(--z-muted);">
                                            <i data-lucide="list-x" style="width:48px;height:48px;opacity:0.3;margin-bottom:1rem;"></i>
                                            <p style="font-size: 1rem; font-weight: 600;">Belum ada instrumen supervisi yang ditambahkan.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Tambah Instrumen -->
<div id="modalInstrumen" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(15,23,42,0.6); z-index:99999; justify-content:center; align-items:center; backdrop-filter: blur(4px);">
    <div style="background:#fff; border-radius:14px; width:100%; max-width:540px; box-shadow:0 20px 40px rgba(0,0,0,0.25); animation: modalIn 0.25s ease; border: 1px solid #e2e8f0; overflow:hidden;">
        <div style="display:flex; justify-content:space-between; align-items:center; padding:1.25rem 1.5rem; border-bottom:1px solid #e2e8f0; background:#f8fafc;">
            <h3 style="margin:0; font-size:1.15rem; font-weight: 800; display:flex; align-items:center; gap:0.5rem; color:#0f172a;">
                <i data-lucide="plus-circle" style="color:#059669; width:20px; height:20px;"></i> Tambah Instrumen Baru
            </h3>
            <button type="button" onclick="document.getElementById('modalInstrumen').style.display='none'" style="background:none; border:none; cursor:pointer; font-size:1.4rem; color:#64748b; line-height:1;">&times;</button>
        </div>
        <form action="/kurikulum/supervisi/instrumen/tambah" method="POST">
            <div style="padding:1.5rem; display:flex; flex-direction:column; gap:1rem;">
                <div>
                    <label style="display:block; margin-bottom:0.35rem; font-weight:700; font-size:0.85rem; color:#334155;">Tipe Supervisi</label>
                    <select name="tipe_supervisi" style="width:100%; padding:0.65rem 0.85rem; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit; font-weight:600;" required>
                        <option value="Kelas">Supervisi Kelas (KBM Tatap Muka)</option>
                        <option value="Administrasi">Supervisi Administrasi (Perangkat Ajar)</option>
                        <option value="Asesmen">Asesmen Pembelajaran</option>
                        <option value="Pengelolaan Kelas">Pengelolaan Kelas</option>
                        <option value="Literasi">Literasi</option>
                        <option value="Numerasi">Numerasi</option>
                        <option value="Pendidikan Karakter">Pendidikan Karakter</option>
                        <option value="Kurikulum Berbasis Cinta">Kurikulum Berbasis Cinta (KBC)</option>
                        <option value="Deep Learning">Deep Learning</option>
                        <option value="Teknologi Digitalisasi">Teknologi Digitalisasi</option>
                        <option value="Ekstrakurikuler">Ekstrakurikuler</option>
                    </select>
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:0.75rem;">
                    <div>
                        <label style="display:block; margin-bottom:0.35rem; font-weight:700; font-size:0.85rem; color:#334155;">Kategori / Bagian</label>
                        <input type="text" name="kategori" style="width:100%; padding:0.65rem 0.85rem; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit;" placeholder="Cth: KEGIATAN INTI" required>
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:0.35rem; font-weight:700; font-size:0.85rem; color:#334155;">Kode Urut</label>
                        <input type="text" name="kode" style="width:100%; padding:0.65rem 0.85rem; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit;" placeholder="Cth: 1, 2, 3..." required>
                    </div>
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.35rem; font-weight:700; font-size:0.85rem; color:#334155;">Aspek Penilaian / Bunyi Indikator</label>
                    <textarea name="aspek" style="width:100%; padding:0.65rem 0.85rem; border:1px solid #cbd5e1; border-radius:8px; min-height:85px; font-family:inherit; font-size:0.9rem;" placeholder="Tuliskan indikator yang akan diobservasi / dinilai..." required></textarea>
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.35rem; font-weight:700; font-size:0.85rem; color:#334155;">Skor Maksimal</label>
                    <input type="number" name="bobot_maksimal" value="5" style="width:100%; padding:0.65rem 0.85rem; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit;" max="100" min="1" required>
                </div>
            </div>
            <div style="padding:1rem 1.5rem; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:0.75rem; background:#f8fafc;">
                <button type="button" onclick="document.getElementById('modalInstrumen').style.display='none'" style="padding:0.65rem 1.25rem; border:1px solid #cbd5e1; background:#fff; border-radius:8px; cursor:pointer; font-weight:700; color:#475569;">Batal</button>
                <button type="submit" style="padding:0.65rem 1.4rem; background:#059669; color:#fff; border:none; border-radius:8px; cursor:pointer; font-weight:700; box-shadow:0 4px 10px rgba(5,150,105,0.25);">Simpan Instrumen</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Instrumen -->
<div id="modalEditInstrumen" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(15,23,42,0.6); z-index:99999; justify-content:center; align-items:center; backdrop-filter: blur(4px);">
    <div style="background:#fff; border-radius:14px; width:100%; max-width:540px; box-shadow:0 20px 40px rgba(0,0,0,0.25); animation: modalIn 0.25s ease; border: 1px solid #e2e8f0; overflow:hidden;">
        <div style="display:flex; justify-content:space-between; align-items:center; padding:1.25rem 1.5rem; border-bottom:1px solid #e2e8f0; background:#f8fafc;">
            <h3 style="margin:0; font-size:1.15rem; font-weight: 800; display:flex; align-items:center; gap:0.5rem; color:#0f172a;">
                <i data-lucide="edit-3" style="color:#0284c7; width:20px; height:20px;"></i> Edit Redaksi Instrumen
            </h3>
            <button type="button" onclick="document.getElementById('modalEditInstrumen').style.display='none'" style="background:none; border:none; cursor:pointer; font-size:1.4rem; color:#64748b; line-height:1;">&times;</button>
        </div>
        <form action="/kurikulum/supervisi/instrumen/update" method="POST">
            <input type="hidden" name="id" id="edit_id" value="">
            
            <div style="padding:1.5rem; display:flex; flex-direction:column; gap:1rem;">
                <!-- Catatan Keamanan Nilai -->
                <div style="background: #e0f2fe; border: 1px solid #bae6fd; border-radius: 8px; padding: 10px 14px; display: flex; align-items: flex-start; gap: 8px;">
                    <i data-lucide="shield-check" style="width: 18px; height: 18px; color: #0284c7; flex-shrink: 0; margin-top: 2px;"></i>
                    <div style="font-size: 0.82rem; color: #0369a1; line-height: 1.4;">
                        <strong>Nilai Guru 100% Aman:</strong> Mengubah teks / redaksi indikator melalui formulir ini tidak akan mengubah ID instrumen, sehingga seluruh riwayat penilaian guru yang telah tersimpan tetap utuh.
                    </div>
                </div>

                <div>
                    <label style="display:block; margin-bottom:0.35rem; font-weight:700; font-size:0.85rem; color:#334155;">Tipe Supervisi</label>
                    <select name="tipe_supervisi" id="edit_tipe" style="width:100%; padding:0.65rem 0.85rem; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit; font-weight:600;" required>
                        <option value="Kelas">Supervisi Kelas (KBM Tatap Muka)</option>
                        <option value="Administrasi">Supervisi Administrasi (Perangkat Ajar)</option>
                        <option value="Asesmen">Asesmen Pembelajaran</option>
                        <option value="Pengelolaan Kelas">Pengelolaan Kelas</option>
                        <option value="Literasi">Literasi</option>
                        <option value="Numerasi">Numerasi</option>
                        <option value="Pendidikan Karakter">Pendidikan Karakter</option>
                        <option value="Kurikulum Berbasis Cinta">Kurikulum Berbasis Cinta (KBC)</option>
                        <option value="Deep Learning">Deep Learning</option>
                        <option value="Teknologi Digitalisasi">Teknologi Digitalisasi</option>
                        <option value="Ekstrakurikuler">Ekstrakurikuler</option>
                    </select>
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:0.75rem;">
                    <div>
                        <label style="display:block; margin-bottom:0.35rem; font-weight:700; font-size:0.85rem; color:#334155;">Kategori / Bagian</label>
                        <input type="text" name="kategori" id="edit_kategori" style="width:100%; padding:0.65rem 0.85rem; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit;" required>
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:0.35rem; font-weight:700; font-size:0.85rem; color:#334155;">Kode Urut</label>
                        <input type="text" name="kode" id="edit_kode" style="width:100%; padding:0.65rem 0.85rem; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit;" required>
                    </div>
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.35rem; font-weight:700; font-size:0.85rem; color:#334155;">Aspek Penilaian / Bunyi Indikator</label>
                    <textarea name="aspek" id="edit_aspek" style="width:100%; padding:0.65rem 0.85rem; border:1px solid #cbd5e1; border-radius:8px; min-height:95px; font-family:inherit; font-size:0.9rem;" required></textarea>
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.35rem; font-weight:700; font-size:0.85rem; color:#334155;">Skor Maksimal</label>
                    <input type="number" name="bobot_maksimal" id="edit_bobot" style="width:100%; padding:0.65rem 0.85rem; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit;" max="100" min="1" required>
                </div>
            </div>
            <div style="padding:1rem 1.5rem; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:0.75rem; background:#f8fafc;">
                <button type="button" onclick="document.getElementById('modalEditInstrumen').style.display='none'" style="padding:0.65rem 1.25rem; border:1px solid #cbd5e1; background:#fff; border-radius:8px; cursor:pointer; font-weight:700; color:#475569;">Batal</button>
                <button type="submit" style="padding:0.65rem 1.4rem; background:#0284c7; color:#fff; border:none; border-radius:8px; cursor:pointer; font-weight:700; box-shadow:0 4px 10px rgba(2,132,199,0.25);">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    if (typeof lucide !== 'undefined') lucide.createIcons();

    // Buka Modal Edit
    function openEditModal(ins) {
        if (!ins) return;
        document.getElementById('edit_id').value = ins.id || '';
        document.getElementById('edit_tipe').value = ins.tipe_supervisi || 'Kelas';
        document.getElementById('edit_kategori').value = ins.kategori || '';
        document.getElementById('edit_kode').value = ins.kode || '';
        document.getElementById('edit_aspek').value = ins.aspek || '';
        document.getElementById('edit_bobot').value = ins.bobot_maksimal || 5;

        var modal = document.getElementById('modalEditInstrumen');
        modal.style.display = 'flex';
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    // Filter Kategori
    function setFilter(tipe, btn) {
        document.querySelectorAll('.filter-pill').forEach(function(el) {
            el.classList.remove('active');
        });
        if (btn) btn.classList.add('active');

        var rows = document.querySelectorAll('.row-instrumen');
        var count = 0;
        rows.forEach(function(row) {
            var rowTipe = (row.getAttribute('data-tipe') || '').trim();
            if (tipe === 'all') {
                row.style.display = '';
                count++;
            } else if (tipe === 'lain') {
                if (rowTipe.toLowerCase() !== 'kelas' && rowTipe.toLowerCase() !== 'administrasi') {
                    row.style.display = '';
                    count++;
                } else {
                    row.style.display = 'none';
                }
            } else {
                if (rowTipe.toLowerCase() === tipe.toLowerCase()) {
                    row.style.display = '';
                    count++;
                } else {
                    row.style.display = 'none';
                }
            }
        });
        var vc = document.getElementById('visibleCount');
        if (vc) vc.textContent = count;
    }

    // Close on backdrop click
    window.onclick = function(e) {
        var m1 = document.getElementById('modalInstrumen');
        var m2 = document.getElementById('modalEditInstrumen');
        if (e.target === m1) m1.style.display = 'none';
        if (e.target === m2) m2.style.display = 'none';
    };
</script>
</body>
</html>
