<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title='Master Instrumen Supervisi'; include __DIR__.'/../admin/_zamrud_head.php'; ?>
</head>
<body>
<?php $page_badge = 'Kurikulum'; ?>
<div class="z-wrap">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../siakad/_siakad_header.php'; ?>
        
        <!-- MULAI KONTEN ANDA DI SINI -->
        <div class="elite-page-header">
            <div class="header-bg-shapes"></div>
            <div class="header-content-inner">
                <div class="header-text-premium">
                    <div class="header-badge">SUPERVISI</div>
                    <h1><i data-lucide="clipboard-list"></i> Master Instrumen Supervisi</h1>
                    <p>Manajemen parameter dan butir soal untuk berbagai tipe supervisi.</p>
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
        
        <div class="z-panel" style="margin-top: 1.5rem;">
            <div class="z-panel-head">
                <div class="z-panel-title">
                    <i data-lucide="list"></i> Daftar Instrumen Penilaian
                </div>
            </div>
            <div style="padding: 0; overflow-x: auto;">
                <table class="z-table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">No</th>
                            <th style="width: 150px;">Tipe Supervisi</th>
                            <th>Kode & Kategori</th>
                            <th>Aspek Penilaian</th>
                            <th style="width: 100px; text-align: center;">Skor Maks</th>
                            <th style="width: 120px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($instrumen) && count($instrumen) > 0): ?>
                            <?php $no = 1; foreach ($instrumen as $ins): ?>
                            <tr>
                                <td style="text-align: center;"><?= $no++ ?></td>
                                <td><span style="font-weight: 600; color:var(--z-primary);"><?= htmlspecialchars($ins['tipe_supervisi']) ?></span></td>
                                <td>
                                    <div style="font-size: 0.85rem; color: #64748b; font-weight: 600; margin-bottom: 2px;"><?= htmlspecialchars($ins['kategori']) ?></div>
                                    <span style="font-weight: 500; font-family: monospace; background:#f1f5f9; padding:2px 6px; border-radius:4px;"><?= htmlspecialchars($ins['kode']) ?></span>
                                </td>
                                <td style="line-height:1.5;">
                                    <?= htmlspecialchars($ins['aspek']) ?>
                                </td>
                                <td align="center" style="font-weight:600; color:#10b981;"><?= htmlspecialchars($ins['bobot_maksimal']) ?></td>
                                <td style="text-align: center; display: flex; gap: 4px; justify-content: center; align-items: center;">
                                    <button class="e-btn e-btn-outline" style="padding: 4px 8px;" onclick="alert('Edit belum tersedia')"><i data-lucide="edit-3" style="width: 16px; height: 16px;"></i></button>
                                    <form action="/kurikulum/supervisi/instrumen/delete" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('Yakin ingin menghapus instrumen ini?');">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($ins['id']) ?>">
                                        <button type="submit" class="e-btn e-btn-outline" style="padding: 4px 8px; color: #ef4444; border-color: #fca5a5;">
                                            <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" align="center" style="padding: 2rem;">
                                    <div style="color: var(--z-muted);">
                                        <i data-lucide="list-x" style="width:48px;height:48px;opacity:0.3;margin-bottom:1rem;"></i>
                                        <p>Belum ada instrumen supervisi yang ditambahkan.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- AKHIR KONTEN ANDA -->

    </div>
</div>

<!-- Modal Tambah Instrumen -->
<div id="modalInstrumen" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); z-index:99999; justify-content:center; align-items:center;">
    <div style="background:#fff; border-radius:12px; width:100%; max-width:500px; box-shadow:0 10px 25px rgba(0,0,0,0.2); animation: modalIn 0.3s ease;">
        <div style="display:flex; justify-content:space-between; align-items:center; padding:1.5rem; border-bottom:1px solid #e2e8f0;">
            <h3 style="margin:0; font-size:1.2rem; display:flex; align-items:center; gap:0.5rem; color:#1e293b;"><i data-lucide="plus-circle" style="color:#10b981;"></i> Tambah Instrumen</h3>
            <button onclick="document.getElementById('modalInstrumen').style.display='none'" style="background:none; border:none; cursor:pointer; font-size:1.5rem; color:#64748b;">&times;</button>
        </div>
        <form action="/kurikulum/supervisi/instrumen/tambah" method="POST">
            <div style="padding:1.5rem; display:flex; flex-direction:column; gap:1rem;">
                <div>
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">Tipe Supervisi</label>
                    <select name="tipe_supervisi" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit;" required>
                        <option value="">- Pilih Tipe -</option>
                        <option value="Administrasi">Administrasi</option>
                        <option value="Kelas">Kelas</option>
                        <option value="Asesmen">Asesmen</option>
                        <option value="Pengelolaan Kelas">Pengelolaan Kelas</option>
                        <option value="Literasi">Literasi</option>
                        <option value="Numerasi">Numerasi</option>
                        <option value="Pendidikan Karakter">Pendidikan Karakter</option>
                        <option value="Kurikulum Berbasis Cinta">Kurikulum Berbasis Cinta (KBC)</option>
                        <option value="Deep Learning">Deep Learning</option>
                        <option value="Teknologi & Digitalisasi">Teknologi & Digitalisasi</option>
                        <option value="Ekstrakurikuler">Ekstrakurikuler</option>
                    </select>
                </div>
                <div style="display:flex; gap:1rem;">
                    <div style="flex:1;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">Kategori / Bagian</label>
                        <input type="text" name="kategori" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit;" placeholder="Cth: Pendahuluan">
                    </div>
                    <div style="flex:1;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">Kode Instrumen</label>
                        <input type="text" name="kode" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit;" placeholder="Cth: INS-01" required>
                    </div>
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">Aspek Penilaian</label>
                    <textarea name="aspek" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:8px; min-height:80px; font-family:inherit;" placeholder="Contoh: Kegiatan Pendahuluan - Apersepsi dan Motivasi" required></textarea>
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">Bobot Maksimal (0 - 100)</label>
                    <input type="number" name="bobot" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit;" max="100" min="0" required>
                </div>
            </div>
            <div style="padding:1.5rem; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:1rem; background:#f8fafc; border-bottom-left-radius:12px; border-bottom-right-radius:12px;">
                <button type="button" onclick="document.getElementById('modalInstrumen').style.display='none'" style="padding:0.75rem 1.5rem; border:1px solid #cbd5e1; background:#fff; border-radius:8px; cursor:pointer; font-weight:600; color:#475569;">Batal</button>
                <button type="submit" style="padding:0.75rem 1.5rem; background:#10b981; color:#fff; border:none; border-radius:8px; cursor:pointer; font-weight:600; box-shadow:0 4px 6px -1px rgba(16,185,129,0.2);">Simpan Instrumen</button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes modalIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
</body>
</html>
