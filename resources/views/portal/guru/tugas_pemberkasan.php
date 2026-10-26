<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$initial = strtoupper(substr($nama, 0, 1));

// get myApps
$myApps = [];
if(isset($_SESSION['user_id'])) {
    $db_core_view = \App\Core\Database::connect('core');
    try {
        $res = $db_core_view->query("SELECT app_code FROM app_admins WHERE user_id = ".$_SESSION['user_id'])->fetchAll();
        foreach($res as $r) { $myApps[] = $r['app_code']; }
    } catch(\PDOException $e) {}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title_page; ?> - Aula System</title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . date('YmdH')); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/multi_dashboard.css?v=' . date('YmdH')); ?>">
    <style>
        .files-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .files-table th { background: #f8fafc; padding: 12px 15px; text-align: left; font-size: 0.8rem; font-weight: 700; color: #475569; border-bottom: 2px solid #e2e8f0; }
        .files-table td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; color: #1e293b; vertical-align: middle; }
        .files-table tr:hover { background: #f8fafc; }
        .file-icon { display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 8px; margin-right: 15px; }
        .file-pdf { background: #fee2e2; color: #ef4444; }
        .file-word { background: #e0f2fe; color: #0ea5e9; }
        .file-excel { background: #dcfce7; color: #0ea5e9; }
        .file-img { background: #fef3c7; color: #f59e0b; }
        .file-other { background: #f1f5f9; color: #64748b; }

        .btn-action { padding: 6px 12px; font-size: 0.8rem; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; border: none; cursor: pointer; font-weight: 600; }
        .btn-view { background: #eff6ff; color: #2563eb; }
        .btn-view:hover { background: #dbeafe; }
        .btn-delete { background: #fef2f2; color: #dc2626; }
        .btn-delete:hover { background: #fee2e2; }

        /* Modal */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); }
        .modal.show { display: flex; align-items: center; justify-content: center; }
        .modal-content { background: #fff; padding: 2rem; border-radius: 16px; width: 100%; max-width: 500px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); position: relative; animation: modalIn 0.3s ease; }
        @keyframes modalIn { from { opacity: 0; transform: translateY(-20px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .close-modal { position: absolute; right: 20px; top: 20px; cursor: pointer; color: #94a3b8; }
        .close-modal:hover { color: #0f172a; }
        
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #1e293b; font-size: 0.9rem; }
        .form-control { width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; font-family: inherit; }
        .form-control:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        .upload-box { border: 2px dashed #cbd5e1; border-radius: 12px; padding: 2rem; text-align: center; background: #f8fafc; cursor: pointer; transition: 0.2s; }
        .upload-box:hover, .upload-box.dragover { border-color: #3b82f6; background: #eff6ff; }
        .upload-box i { color: #94a3b8; margin-bottom: 10px; }
        .upload-box:hover i, .upload-box.dragover i { color: #3b82f6; }
        
        .empty-state { padding: 4rem 2rem; text-align: center; color: #64748b; }
        .empty-state i { color: #cbd5e1; margin-bottom: 15px; }
    </style>
</head>
<body>
<div class="siakad-container">
    <?php include __DIR__ . '/../sidebar_multi_guru.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../header_multi_guru.php'; ?>
        <div class="z-scroll" style="padding: 1.5rem var(--z-gap) var(--z-gap);">
            
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px; margin-bottom:1.5rem;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg, #0ea5e9, #0284c7);color:white;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                        <i data-lucide="folder-check" style="width:24px;height:24px;"></i>
                    </div>
                    <div>
                        <h2 style="margin:0; font-size:1.5rem; font-weight:800; color:#0f172a;">Pemberkasan Kemenag</h2>
                        <p style="margin:0; font-size:0.85rem; color:#64748b;">Simpan dokumen portofolio dan SK resmi Anda di sini.</p>
                    </div>
                </div>
                <div style="display:flex; gap:10px;">
                    <button onclick="document.getElementById('generateModal').classList.add('show')" class="z-btn" style="background:linear-gradient(135deg, #f59e0b, #d97706); color:white; padding:10px 15px; font-weight:700; border:none; box-shadow:0 4px 12px rgba(245,158,11,0.3);">
                        <i data-lucide="printer" style="width:18px;height:18px;margin-right:6px;"></i> Buat Surat Otomatis
                    </button>
                    <button onclick="document.getElementById('uploadModal').classList.add('show')" class="z-btn" style="background:linear-gradient(135deg, #3b82f6, #2563eb); color:white; padding:10px 15px; font-weight:700; border:none; box-shadow:0 4px 12px rgba(59,130,246,0.3);">
                        <i data-lucide="upload-cloud" style="width:18px;height:18px;margin-right:6px;"></i> Upload Arsip
                    </button>
                </div>
            </div>

            <?php if(isset($_GET['success'])): ?>
                <div style="background:#f0f9ff; border:1px solid #0ea5e9; color:#0284c7; padding:15px; border-radius:10px; margin-bottom:1.5rem; display:flex; gap:10px; align-items:center;">
                    <i data-lucide="check-circle"></i>
                    <div><strong>Upload Berhasil!</strong> Berkas Anda telah tersimpan di sistem.</div>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_GET['deleted'])): ?>
                <div style="background:#f0fdf4; border:1px solid #86efac; color:#166534; padding:15px; border-radius:10px; margin-bottom:1.5rem; display:flex; gap:10px; align-items:center;">
                    <i data-lucide="trash-2"></i>
                    <div><strong>Berhasil Dihapus!</strong> Berkas telah dihapus secara permanen.</div>
                </div>
            <?php endif; ?>

            <div class="z-panel">
                <div class="z-panel-head">
                    <div class="z-panel-title"><i data-lucide="folder-open"></i> Daftar Berkas Saya</div>
                </div>
                <div class="z-panel-body" style="padding:0;">
                    <?php if(empty($berkas_rows)): ?>
                        <div class="empty-state">
                            <i data-lucide="files" style="width:64px;height:64px;"></i>
                            <h3 style="margin:0 0 8px 0; color:#1e293b;">Belum Ada Arsip Berkas</h3>
                            <p style="margin:0; font-size:0.9rem;">Klik tombol "Upload Berkas Baru" untuk mulai menyimpan SK atau sertifikat Anda.</p>
                        </div>
                    <?php else: ?>
                        <div style="overflow-x:auto;">
                            <table class="files-table">
                                <thead>
                                    <tr>
                                        <th style="width:40%;">Nama Dokumen</th>
                                        <th>Tgl Upload</th>
                                        <th>Ukuran</th>
                                        <th style="text-align:right;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($berkas_rows as $b): 
                                        $ext = strtolower(pathinfo($b['file_path'], PATHINFO_EXTENSION));
                                        $iconClass = 'file-other'; $iconName = 'file';
                                        if($ext == 'pdf') { $iconClass = 'file-pdf'; $iconName = 'file-text'; }
                                        elseif(in_array($ext, ['doc','docx'])) { $iconClass = 'file-word'; $iconName = 'file-type-2'; }
                                        elseif(in_array($ext, ['xls','xlsx'])) { $iconClass = 'file-excel'; $iconName = 'table'; }
                                        elseif(in_array($ext, ['jpg','jpeg','png'])) { $iconClass = 'file-img'; $iconName = 'image'; }
                                        
                                        $filepathFull = __DIR__ . '/../../public/uploads/guru_berkas/' . $b['file_path'];
                                        $size = file_exists($filepathFull) ? round(filesize($filepathFull) / 1024) . ' KB' : '-';
                                    ?>
                                    <tr>
                                        <td>
                                            <div style="display:flex; align-items:center;">
                                                <div class="file-icon <?php echo $iconClass; ?>">
                                                    <i data-lucide="<?php echo $iconName; ?>" style="width:20px;height:20px;"></i>
                                                </div>
                                                <div>
                                                    <div style="font-weight:700; color:#0f172a;"><?php echo htmlspecialchars($b['nama_berkas']); ?></div>
                                                    <div style="font-size:0.75rem; color:#64748b; margin-top:2px;">Format: <?php echo strtoupper($ext); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="color:#334155; font-weight:600;"><?php echo date('d M Y', strtotime($b['created_at'])); ?></div>
                                            <div style="color:#94a3b8; font-size:0.75rem;"><?php echo date('H:i', strtotime($b['created_at'])); ?> WIB</div>
                                        </td>
                                        <td style="color:#475569;"><?php echo $size; ?></td>
                                        <td style="text-align:right;">
                                            <a href="<?php echo base_url('public/uploads/guru_berkas/<?php echo htmlspecialchars($b['file_path']); ?>'); ?>" target="_blank" class="btn-action btn-view" title="Lihat/Download">
                                                <i data-lucide="external-link" style="width:14px;height:14px;"></i> Lihat
                                            </a>
                                            <form action="<?php echo base_url('guru/pemberkasan/delete'); ?>" method="POST" style="display:inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus arsip <?php echo htmlspecialchars($b['nama_berkas']); ?>?');">
                                                <input type="hidden" name="berkas_id" value="<?php echo $b['id']; ?>">
                                                <button type="submit" class="btn-action btn-delete" title="Hapus">
                                                    <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal" id="uploadModal">
    <div class="modal-content">
        <i data-lucide="x" class="close-modal" onclick="document.getElementById('uploadModal').classList.remove('show')"></i>
        <h3 style="margin:0 0 20px 0; font-size:1.25rem; font-weight:800; color:#0f172a;">Upload Arsip Baru</h3>
        <form action="<?php echo base_url('guru/pemberkasan/upload'); ?>" method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label>Nama Berkas / Surat Keputusan</label>
                <input type="text" name="nama_berkas" class="form-control" placeholder="Contoh: SK Mengajar Ganjil 2026/2027" required autocomplete="off">
            </div>

            <div class="form-group">
                <label>Pilih File (PDF / Word / Excel / JPG)</label>
                <input type="file" name="berkas_file" id="berkas_file" accept=".pdf,.doc,.docx,.xls,.xlsx,image/*" required style="display:none;" onchange="updateFileName(this)">
                <div class="upload-box" onclick="document.getElementById('berkas_file').click()">
                    <i data-lucide="upload-cloud" style="width:40px;height:40px;"></i>
                    <div id="upload-text" style="font-weight:600; color:#3b82f6;">Klik untuk memilih file</div>
                    <div style="font-size:0.75rem; color:#64748b; margin-top:5px;">Maksimal 5MB</div>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:30px;">
                <button type="button" class="z-btn" style="background:#f1f5f9; color:#475569; padding:10px 20px; font-weight:600; border:none;" onclick="document.getElementById('uploadModal').classList.remove('show')">Batal</button>
                <button type="submit" class="z-btn" style="background:linear-gradient(135deg, #3b82f6, #2563eb); color:white; padding:10px 20px; font-weight:700; border:none; box-shadow:0 4px 12px rgba(59,130,246,0.3);">
                    <i data-lucide="save" style="width:16px;height:16px;vertical-align:-2px;margin-right:6px;"></i> Simpan Arsip
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Generate Surat Modal -->
<div class="modal" id="generateModal">
    <div class="modal-content">
        <i data-lucide="x" class="close-modal" onclick="document.getElementById('generateModal').classList.remove('show')"></i>
        <h3 style="margin:0 0 5px 0; font-size:1.25rem; font-weight:800; color:#0f172a;">Buat Surat / SK Otomatis</h3>
        <p style="margin:0 0 20px 0; font-size:0.85rem; color:#64748b;">Pilih jenis surat, sistem akan menarik data Anda dan menghasilkan surat siap cetak.</p>
        <form action="<?php echo base_url('guru/pemberkasan/generate'); ?>" method="GET" target="_blank">
            
            <div class="form-group">
                <label>Jenis Surat</label>
                <select name="jenis_surat" class="form-control" required>
                    <option value="surat_rekomendasi">Surat Rekomendasi (Kelayakan Mengajar)</option>
                    <option value="sk_pembagian_tugas">SK Pembagian Tugas (Segera Hadir)</option>
                </select>
            </div>

            <div style="background:#fffbeb; padding:15px; border-radius:8px; border:1px solid #fde68a; margin-bottom:20px; font-size:0.85rem; color:#b45309;">
                <i data-lucide="info" style="width:16px;height:16px;vertical-align:-3px;margin-right:4px;"></i> Data akan ditarik otomatis dari Profil Anda (Nama, TTL, Jabatan). Pastikan data profil Anda sudah lengkap dan benar.
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="z-btn" style="background:#f1f5f9; color:#475569; padding:10px 20px; font-weight:600; border:none;" onclick="document.getElementById('generateModal').classList.remove('show')">Batal</button>
                <button type="submit" class="z-btn" style="background:linear-gradient(135deg, #f59e0b, #d97706); color:white; padding:10px 20px; font-weight:700; border:none; box-shadow:0 4px 12px rgba(245,158,11,0.3);" onclick="setTimeout(()=>document.getElementById('generateModal').classList.remove('show'), 500);">
                    <i data-lucide="printer" style="width:16px;height:16px;vertical-align:-2px;margin-right:6px;"></i> Cetak Surat
                </button>
            </div>
        </form>
    </div>
</div>

<script>
lucide.createIcons();

function updateFileName(input) {
    const textEl = document.getElementById('upload-text');
    if(input.files && input.files.length > 0) {
        textEl.innerHTML = '<span style="color:#0f172a;">' + input.files[0].name + '</span>';
        textEl.parentElement.style.borderColor = '#0ea5e9';
        textEl.parentElement.style.background = '#f0f9ff';
    } else {
        textEl.innerHTML = 'Klik untuk memilih file';
        textEl.parentElement.style.borderColor = '';
        textEl.parentElement.style.background = '';
    }
}
</script>
</body>
</html>


