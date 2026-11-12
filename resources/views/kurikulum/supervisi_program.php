<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title='Program Supervisi'; include __DIR__.'/../admin/_zamrud_head.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <style>
        .modal-large { max-width: 900px !important; }
        .note-editor.note-frame { border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden; }
        .note-toolbar { background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
    </style>
</head>
<body>
<?php $page_badge = 'Kurikulum'; ?>
<div class="z-wrap">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../siakad/_siakad_header.php'; ?>
        
        <div class="elite-page-header">
            <div class="header-bg-shapes"></div>
            <div class="header-content-inner">
                <div class="header-text-premium">
                    <div class="header-badge">SUPERVISI</div>
                    <h1><i data-lucide="calendar-check"></i> Program Supervisi</h1>
                    <p>Manajemen dokumen program supervisi akademik dan manajerial per tahun ajaran.</p>
                </div>
                <div class="header-actions-premium" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                    <a href="/kurikulum/supervisi/laporan-hasil" class="e-btn" style="background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.35); color: #fff; text-decoration: none;" title="Buka Dokumen Laporan Hasil Supervisi Bab I - IV">
                        <i data-lucide="file-bar-chart-2"></i> Laporan Hasil
                    </a>
                    <button class="e-btn e-btn-primary" onclick="openNew()">
                        <i data-lucide="upload-cloud"></i> Tambah Program
                    </button>
                </div>
            </div>
        </div>
        
        <div class="z-panel" style="margin-top: 1.5rem;">
            <div class="z-panel-head">
                <div class="z-panel-title">
                    <i data-lucide="list"></i> Daftar Dokumen Program Supervisi
                </div>
            </div>
            <div style="padding: 0; overflow-x: auto;">
                <table class="z-table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">No</th>
                            <th>Judul Program</th>
                            <th>Tahun Ajaran</th>
                            <th>Semester</th>
                            <th>Tanggal Upload</th>
                            <th style="width: 150px; text-align: center;">File / Dokumen</th>
                            <th style="width: 120px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($programs)): ?>
                        <tr>
                            <td colspan="7" align="center" style="padding: 2rem;">
                                <div style="color: var(--z-muted);">
                                    <i data-lucide="folder-open" style="width:48px;height:48px;opacity:0.3;margin-bottom:1rem;"></i>
                                    <p>Belum ada dokumen program supervisi yang diunggah.</p>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php 
                            $no = 1;
                            $ta_map = [];
                            if(isset($ta_list)) {
                                foreach($ta_list as $ta) {
                                    $ta_map[$ta['id']] = $ta['name'];
                                }
                            }
                            $bulanIndoSingkat = [
                                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
                                7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
                            ];
                            foreach($programs as $row): 
                                $ta_nama = $ta_map[$row['tahun_ajaran_id']] ?? 'Tidak Diketahui';
                                $tglCreated = strtotime($row['created_at']);
                                $tglIndo = date('d', $tglCreated) . ' ' . ($bulanIndoSingkat[(int)date('n', $tglCreated)] ?? date('M', $tglCreated)) . ' ' . date('Y', $tglCreated);
                                $tglInput = date('Y-m-d', $tglCreated);
                            ?>
                            <tr>
                                <td align="center" style="vertical-align:top; padding-top:1.2rem;"><?= $no++ ?></td>
                                <td style="vertical-align:top; padding-top:1.2rem;">
                                    <div style="font-weight:600; color:#1e293b;"><?= htmlspecialchars($row['judul']) ?></div>
                                </td>
                                <td style="vertical-align:top; padding-top:1.2rem;"><?= htmlspecialchars($ta_nama) ?></td>
                                <td style="vertical-align:top; padding-top:1.2rem;"><?= htmlspecialchars($row['semester']) ?></td>
                                <td style="vertical-align:top; padding-top:1.2rem;"><?= $tglIndo ?></td>
                                <td align="center" style="vertical-align:top; padding-top:1.2rem;">
                                    <?php if($row['file_path']): ?>
                                        <a href="/public/uploads/supervisi/<?= htmlspecialchars($row['file_path']) ?>" target="_blank" class="z-badge" style="background:#e0e7ff; color:#4f46e5; text-decoration:none; display:inline-flex; align-items:center; gap:0.25rem;">
                                            <i data-lucide="download" style="width:14px; height:14px;"></i> Unduh File
                                        </a>
                                    <?php else: ?>
                                        <span class="z-badge z-badge-warning">Tidak ada file</span>
                                    <?php endif; ?>
                                </td>
                                <td align="center" style="vertical-align:top; padding-top:1.2rem;">
                                    <div style="display:flex; gap:0.5rem; justify-content:center;">
                                        <button onclick="cetakProgram(<?= $row['id'] ?>)" class="e-btn e-btn-outline" style="color:#8b5cf6; border-color:rgba(139,92,246,0.2); background:rgba(139,92,246,0.05);" title="Cetak"><i data-lucide="printer"></i></button>
                                        <button onclick="editProgram(<?= $row['id'] ?>, `<?= htmlspecialchars($row['judul'], ENT_QUOTES) ?>`, <?= $row['tahun_ajaran_id'] ?>, '<?= $row['semester'] ?>', `<?= htmlspecialchars($row['pendahuluan'] ?? '', ENT_QUOTES) ?>`, `<?= htmlspecialchars($row['tujuan'] ?? '', ENT_QUOTES) ?>`, `<?= htmlspecialchars($row['sasaran'] ?? '', ENT_QUOTES) ?>`, `<?= htmlspecialchars($row['ruang_lingkup'] ?? '', ENT_QUOTES) ?>`, `<?= htmlspecialchars($row['jadwal_pelaksanaan'] ?? '', ENT_QUOTES) ?>`, '<?= $tglInput ?>')" class="e-btn e-btn-outline" style="color:#3b82f6; border-color:rgba(59,130,246,0.2); background:rgba(59,130,246,0.05);" title="Edit"><i data-lucide="edit"></i></button>
                                        <button onclick="deleteProgram(<?= $row['id'] ?>)" class="e-btn e-btn-outline" style="color:#ef4444; border-color:rgba(239,68,68,0.2); background:rgba(239,68,68,0.05);" title="Hapus"><i data-lucide="trash-2"></i></button>
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

<!-- Modal Program Supervisi -->
<div id="modalProgram" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); z-index:99999; justify-content:center; align-items:center;">
    <div class="modal-large" style="background:#fff; border-radius:12px; width:95%; max-height:90vh; overflow-y:auto; box-shadow:0 10px 25px rgba(0,0,0,0.2); animation: modalIn 0.3s ease; position:relative;">
        <div style="display:flex; justify-content:space-between; align-items:center; padding:1.5rem; border-bottom:1px solid #e2e8f0;">
            <h3 id="modalTitle" style="margin:0; font-size:1.2rem; display:flex; align-items:center; gap:0.5rem; color:#1e293b;"><i data-lucide="upload-cloud" style="color:#10b981;"></i> Tambah Program Supervisi</h3>
            <button onclick="document.getElementById('modalProgram').style.display='none'" style="background:none; border:none; cursor:pointer; font-size:1.5rem; color:#64748b;">&times;</button>
        </div>
        <form id="programForm" action="/kurikulum/supervisi/program" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" id="program_id">
            <div style="padding:1.5rem; display:flex; flex-direction:column; gap:1rem;">
                <div style="display:flex; gap:1rem; flex-wrap:wrap;">
                    <div style="flex:2; min-width:240px;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">Judul Program</label>
                        <input type="text" name="judul" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit;" placeholder="Misal: Program Supervisi Akademik 2026/2027" required>
                    </div>
                    <div style="flex:1; min-width:160px;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">Tanggal Program / Dokumen</label>
                        <input type="date" name="tanggal" id="program_tanggal" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit; background:#fff;" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                <div style="display:flex; gap:1rem;">
                    <div style="flex:1;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:8px; background:#fff; font-family:inherit;" required>
                            <?php if(isset($ta_list) && is_array($ta_list)): ?>
                                <?php foreach($ta_list as $ta): ?>
                                    <option value="<?= $ta['id'] ?>" <?= (isset($ta['is_active']) && $ta['is_active'] == 1) ? 'selected' : '' ?>><?= htmlspecialchars($ta['name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div style="flex:1;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:0.9rem; color:#475569;">Semester</label>
                        <select name="semester" style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:8px; background:#fff; font-family:inherit;" required>
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                            <option value="Satu Tahun Penuh">Satu Tahun Penuh</option>
                        </select>
                    </div>
                </div>


            </div>
            <div style="padding:1.5rem; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:1rem; background:#f8fafc; border-bottom-left-radius:12px; border-bottom-right-radius:12px; position:sticky; bottom:0; z-index:10;">
                <button type="button" onclick="document.getElementById('modalProgram').style.display='none'" style="padding:0.75rem 1.5rem; border:1px solid #cbd5e1; background:#fff; border-radius:8px; cursor:pointer; font-weight:600; color:#475569;">Batal</button>
                <button type="submit" style="padding:0.75rem 1.5rem; background:#10b981; color:#fff; border:none; border-radius:8px; cursor:pointer; font-weight:600; box-shadow:0 4px 6px -1px rgba(16,185,129,0.2);">Simpan Program</button>
            </div>
        </form>
    </div>
</div>

<form id="deleteForm" action="/kurikulum/supervisi/program/delete" method="POST" style="display:none;">
    <input type="hidden" name="id" id="delete_id">
</form>

<style>
    @keyframes modalIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    $(document).ready(function() {
        if (typeof lucide !== 'undefined') lucide.createIcons();
        $('.summernote').summernote({
            height: 120,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['table']]
            ]
        });
    });

    function cetakProgram(id) {
        window.open('/kurikulum/supervisi/program/cetak?id=' + id, '_blank');
    }

    function openNew() {
        document.getElementById('programForm').reset();
        document.getElementById('program_id').value = '';
        document.getElementById('program_tanggal').value = '<?= date('Y-m-d') ?>';
        document.getElementById('programForm').action = '/kurikulum/supervisi/program';
        document.getElementById('modalTitle').innerHTML = '<i data-lucide="upload-cloud" style="color:#10b981;"></i> Tambah Program Supervisi';
        
        document.getElementById('modalProgram').style.display='flex';
    }

    function editProgram(id, judul, tahun_ajaran_id, semester, pendahuluan, tujuan, sasaran, ruang_lingkup, jadwal_pelaksanaan, tanggal) {
        document.getElementById('program_id').value = id;
        document.getElementById('program_tanggal').value = tanggal || '<?= date('Y-m-d') ?>';
        document.getElementById('programForm').action = '/kurikulum/supervisi/program/update';
        document.getElementById('modalTitle').innerHTML = '<i data-lucide="edit" style="color:#3b82f6;"></i> Edit Program Supervisi';
        
        document.querySelector('input[name="judul"]').value = judul;
        document.querySelector('select[name="tahun_ajaran_id"]').value = tahun_ajaran_id;
        document.querySelector('select[name="semester"]').value = semester;
        
        $('textarea[name="pendahuluan"]').summernote('code', pendahuluan || '');
        $('textarea[name="tujuan"]').summernote('code', tujuan || '');
        $('textarea[name="sasaran"]').summernote('code', sasaran || '');
        $('textarea[name="ruang_lingkup"]').summernote('code', ruang_lingkup || '');
        $('textarea[name="jadwal_pelaksanaan"]').summernote('code', jadwal_pelaksanaan || '');
        
        document.getElementById('modalProgram').style.display='flex';
    }

    function deleteProgram(id) {
        if(confirm('Apakah Anda yakin ingin menghapus program supervisi ini beserta file dokumennya?')) {
            document.getElementById('delete_id').value = id;
            document.getElementById('deleteForm').submit();
        }
    }
</script>
</body>
</html>
