<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$roleName = "Guru / Tendik";
if(!empty($_SESSION['is_kamad'])) $roleName = "Kepala Madrasah";
$initial = strtoupper(substr($nama, 0, 2));

$myApps = [];
if(isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2 && isset($_SESSION['user_id'])) {
    $db_core = \App\Core\Database::connect('core');
    $uid = $_SESSION['user_id'];
    try {
        $res = $db_core->query("SELECT app_code FROM app_admins WHERE user_id = $uid")->fetchAll();
        foreach($res as $r) {
            $myApps[] = $r['app_code'];
        }
    } catch(\PDOException $e) {}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Detail Perangkat - Portal Guru'; ?></title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . date('YmdH')); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/multi_dashboard.css?v=' . date('YmdH')); ?>">
</head>
<body>

<div class="siakad-container">

<?php include __DIR__ . '/../sidebar_multi_guru.php'; ?>

    <div class="z-main">

<?php include __DIR__ . '/../header_multi_guru.php'; ?>

        <div class="z-scroll" style="padding: 0 var(--z-gap) var(--z-gap);">
            
            <!-- Hero Banner -->
            <div class="elite-page-header welcome-hero" style="background: var(--z-primary-gradient); padding: 2rem 2.5rem; border-radius: var(--z-radius, 12px); margin-bottom: 2rem; color: white; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: space-between;">
                <div style="position: relative; z-index: 1; display: flex; align-items: center; gap: 20px;">
                    <a href="<?php echo base_url('guru/perangkat'); ?>" style="background: rgba(255,255,255,0.2); color: white; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; transition: 0.2s; text-decoration: none;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                        <i data-lucide="arrow-left" style="width:16px;height:16px;"></i> Kembali
                    </a>
                    <div>
                        <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                            <i data-lucide="book-open" style="color: rgba(255,255,255,0.8);"></i> Detail Perangkat
                        </h1>
                        <div style="background: rgba(0,0,0,0.15); padding: 6px 16px; border-radius: 100px; display: inline-flex; align-items: center; gap: 8px; font-size: 0.85rem; font-weight: 600;">
                            <?php echo $currentMapel['nama'] . ' - Kelas ' . $currentMapel['kelas']; ?>
                            <i data-lucide="chevron-down" style="width:14px;height:14px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="z-card" style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 1rem; margin-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 48px; height: 48px; border-radius: 12px; background: #f0f9ff; color: #0ea5e9; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="folder" style="width:24px;height:24px;"></i>
                        </div>
                        <div>
                            <h2 style="margin: 0; font-size: 1.2rem; color: #0f172a; font-weight: 800;">Daftar Dokumen</h2>
                            <p style="margin: 5px 0 0 0; color: #64748b; font-size: 0.9rem;">Upload dan kelola dokumen untuk mapel ini.</p>
                        </div>
                    </div>
                    <?php if($bolong > 0): ?>
                    <div style="background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2; padding: 8px 16px; border-radius: 100px; font-size: 0.85rem; font-weight: 700; display: flex; align-items: center; gap: 6px;">
                        <i data-lucide="bell" style="width:14px;height:14px;"></i> <?php echo $bolong; ?> berkas wajib belum diupload!
                    </div>
                    <?php endif; ?>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php 
                    $requiredDocs = ['Capaian Pembelajaran (CP)', 'Alur Tujuan Pembelajaran (ATP)', 'Modul Ajar', 'KKTP', 'Program Tahunan (Prota)', 'Program Semester (Promes)'];
                    foreach($requiredDocs as $jenis): 
                        $docs = $documents[$jenis] ?? [];
                    ?>
                    <div style="border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; background: white; transition: 0.2s;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: <?php echo empty($docs) ? '0' : '1rem'; ?>;">
                            <h3 style="margin: 0; font-size: 1.05rem; color: #0f172a; font-weight: 700;"><?php echo $jenis; ?></h3>
                            <button onclick="openUploadModal('<?php echo $jenis; ?>')" style="background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; padding: 6px 14px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: 0.2s;" onmouseover="this.style.background='#bae6fd'" onmouseout="this.style.background='#e0f2fe'">
                                <i data-lucide="plus" style="width:14px;height:14px;"></i> Tambah
                            </button>
                        </div>
                        
                        <?php if(empty($docs)): ?>
                            <p style="margin: 5px 0 0 0; color: #ef4444; font-size: 0.85rem; font-style: italic;">Belum ada dokumen yang diupload</p>
                        <?php else: ?>
                            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                <?php foreach($docs as $doc): ?>
                                <div style="border: 1px solid #f1f5f9; border-radius: 12px; padding: 1rem; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
                                    <div>
                                        <h4 style="margin: 0 0 4px 0; font-size: 0.95rem; font-weight: 600; color: #1e293b;">
                                            <?php echo htmlspecialchars($doc['nama_berkas'] ?: $jenis); ?>
                                        </h4>
                                        <p style="margin: 0; color: #64748b; font-size: 0.8rem; display: flex; align-items: center; gap: 6px;">
                                            <?php echo date('d M Y', strtotime($doc['created_at'])); ?> &bull; 
                                            <span style="font-weight: 600; color: <?php echo $doc['status_review'] == 'Disetujui' || $doc['status_review'] == 'Sempurna' ? '#10b981' : ($doc['status_review'] == 'Revisi' ? '#ef4444' : '#f59e0b'); ?>;">
                                                <?php echo $doc['status_review']; ?>
                                            </span>
                                        </p>
                                    </div>
                                    <div style="display: flex; gap: 6px; align-items: center;">
                                        <button onclick="openCopyModal(<?php echo $doc['id']; ?>, '<?php echo htmlspecialchars(addslashes($doc['nama_berkas'] ?: $jenis)); ?>')" style="width: 32px; height: 32px; padding: 0; display: inline-flex; justify-content: center; align-items: center; border-radius: 8px; background: #f0fdf4; color: #22c55e; border: 1px solid #bbf7d0; cursor: pointer;" title="Salin ke kelas lain">
                                            <i data-lucide="copy" style="width:14px;height:14px;"></i>
                                        </button>
                                        <button onclick="openUploadModal('<?php echo $jenis; ?>', <?php echo $doc['id']; ?>, '<?php echo htmlspecialchars(addslashes($doc['nama_berkas'])); ?>')" style="width: 32px; height: 32px; padding: 0; display: inline-flex; justify-content: center; align-items: center; border-radius: 8px; background: #fffbeb; color: #f59e0b; border: 1px solid #fef3c7; cursor: pointer;" title="Edit">
                                            <i data-lucide="edit-2" style="width:14px;height:14px;"></i>
                                        </button>
                                        <a href="<?php echo base_url('public/uploads/guru_perangkat/' . $doc['file_path']); ?>" target="_blank" style="width: 32px; height: 32px; padding: 0; display: inline-flex; justify-content: center; align-items: center; border-radius: 8px; background: #eff6ff; color: #3b82f6; border: 1px solid #dbeafe; text-decoration: none;" title="Lihat">
                                            <i data-lucide="eye" style="width:14px;height:14px;"></i>
                                        </a>
                                        <?php if ($doc['status_review'] !== 'Sempurna'): ?>
                                        <a href="<?php echo base_url('guru/perangkat/delete/' . $doc['id']); ?>" onclick="return confirm('Yakin ingin menghapus dokumen ini?')" style="width: 32px; height: 32px; padding: 0; display: inline-flex; justify-content: center; align-items: center; border-radius: 8px; background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2; text-decoration: none;" title="Hapus">
                                            <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                                        </a>
                                        <?php else: ?>
                                        <div style="width: 32px; height: 32px; padding: 0; display: inline-flex; justify-content: center; align-items: center; border-radius: 8px; background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0; cursor: not-allowed;" title="Dikunci (Sempurna)">
                                            <i data-lucide="lock" style="width:14px;height:14px;"></i>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>
            
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 16px; width: 100%; max-width: 500px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); overflow: hidden; max-height: 90vh; overflow-y: auto;">
        <div style="padding: 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: white; z-index: 10;">
            <h3 id="modal_title" style="margin: 0; font-size: 1.25rem; font-weight: 800; color: #0f172a;">Upload Perangkat</h3>
            <button onclick="closeUploadModal()" style="background: none; border: none; cursor: pointer; color: #64748b;">
                <i data-lucide="x" style="width:20px;height:20px;"></i>
            </button>
        </div>
        <form action="<?php echo base_url('guru/perangkat/upload'); ?>" method="POST" enctype="multipart/form-data" style="padding: 1.5rem;">
            <input type="hidden" name="doc_id" id="doc_id_input" value="0">
            <input type="hidden" name="kelas_id" value="<?php echo $kelas_id; ?>">
            <input type="hidden" name="mapel_id" value="<?php echo $mapel_id; ?>">
            <input type="hidden" name="jenis_perangkat" id="jenis_perangkat_input">
            
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155; font-size: 0.9rem;">Jenis Dokumen</label>
                <input type="text" id="jenis_perangkat_display" readonly style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc; color: #64748b; font-weight: 500;">
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155; font-size: 0.9rem;">Judul Dokumen (Opsional)</label>
                <input type="text" id="judul_input" name="judul" placeholder="Contoh: Modul Ajar Bab 1" style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; outline: none; transition: 0.2s;" onfocus="this.style.borderColor='var(--z-primary)'" onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155; font-size: 0.9rem;">File Dokumen</label>
                <input type="file" id="file_input" name="file_perangkat" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" style="width: 100%; padding: 10px; border: 1px dashed #cbd5e1; border-radius: 8px; background: #f8fafc;">
                <div id="file_hint" style="margin-top: 6px; font-size: 0.75rem; color: #94a3b8;">Format: PDF, Word, Excel, PowerPoint.</div>
            </div>

            <?php if (!empty($otherClasses)): ?>
            <div id="bulk_upload_section" style="margin-bottom: 1.5rem; padding: 1rem; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155; font-size: 0.9rem;">
                    <i data-lucide="copy" style="width:14px;height:14px; display:inline-block; vertical-align:middle;"></i> Terapkan juga ke kelas lain:
                </label>
                <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 10px;">
                    <?php foreach($otherClasses as $oc): ?>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="kelas_lain[]" value="<?php echo $oc['kelas_id']; ?>" style="width: 16px; height: 16px; accent-color: var(--z-primary);">
                        <span style="font-size: 0.85rem; color: #475569;"><?php echo htmlspecialchars($oc['kelas'] . ' - ' . $oc['nama']); ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
                <div style="margin-top: 8px; font-size: 0.75rem; color: #94a3b8; font-style: italic;">
                    Sistem akan meng-copy file otomatis ke kelas yang dicentang.
                </div>
            </div>
            <?php endif; ?>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeUploadModal()" style="padding: 10px 16px; border-radius: 8px; border: 1px solid #e2e8f0; background: white; color: #64748b; font-weight: 600; cursor: pointer;">Batal</button>
                <button type="submit" style="padding: 10px 16px; border-radius: 8px; border: none; background: var(--z-primary); color: white; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                    <i data-lucide="save" style="width:16px;height:16px;"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Copy Existing Modal -->
<div id="copyModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 16px; width: 100%; max-width: 500px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); overflow: hidden;">
        <div style="padding: 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.25rem; font-weight: 800; color: #0f172a;">Salin ke Kelas Lain</h3>
            <button onclick="closeCopyModal()" style="background: none; border: none; cursor: pointer; color: #64748b;">
                <i data-lucide="x" style="width:20px;height:20px;"></i>
            </button>
        </div>
        <form action="<?php echo base_url('guru/perangkat/copy'); ?>" method="POST" style="padding: 1.5rem;">
            <input type="hidden" name="doc_id" id="copy_doc_id_input" value="0">
            
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155; font-size: 0.9rem;">Dokumen</label>
                <input type="text" id="copy_doc_title_display" readonly style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc; color: #0f172a; font-weight: 600;">
            </div>

            <?php if (!empty($otherClasses)): ?>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 12px; font-weight: 600; color: #334155; font-size: 0.9rem;">Pilih Kelas Tujuan:</label>
                <div style="display: flex; flex-direction: column; gap: 8px; max-height: 200px; overflow-y: auto; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc;">
                    <?php foreach($otherClasses as $oc): ?>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 6px 0;">
                        <input type="checkbox" name="kelas_lain[]" value="<?php echo $oc['kelas_id']; ?>" style="width: 16px; height: 16px; accent-color: var(--z-primary);">
                        <span style="font-size: 0.9rem; color: #1e293b; font-weight: 500;"><?php echo htmlspecialchars($oc['kelas'] . ' - ' . $oc['nama']); ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
            <div style="margin-bottom: 1.5rem; padding: 1rem; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; color: #b45309; font-size: 0.85rem;">
                Anda tidak memiliki kelas lain untuk mata pelajaran ini.
            </div>
            <?php endif; ?>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeCopyModal()" style="padding: 10px 16px; border-radius: 8px; border: 1px solid #e2e8f0; background: white; color: #64748b; font-weight: 600; cursor: pointer;">Batal</button>
                <button type="submit" <?php echo empty($otherClasses) ? 'disabled' : ''; ?> style="padding: 10px 16px; border-radius: 8px; border: none; background: #10b981; color: white; font-weight: 600; cursor: <?php echo empty($otherClasses) ? 'not-allowed' : 'pointer'; ?>; opacity: <?php echo empty($otherClasses) ? '0.5' : '1'; ?>; display: flex; align-items: center; gap: 6px;">
                    <i data-lucide="copy" style="width:16px;height:16px;"></i> Salin Dokumen
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    lucide.createIcons();

    function openUploadModal(jenis, id = 0, judul = '') {
        document.getElementById('jenis_perangkat_input').value = jenis;
        document.getElementById('jenis_perangkat_display').value = jenis;
        document.getElementById('doc_id_input').value = id;
        if (id === 0 && judul === '') {
            document.getElementById('judul_input').value = jenis;
        } else {
            document.getElementById('judul_input').value = judul;
        }
        
        const fileInput = document.getElementById('file_input');
        const fileHint = document.getElementById('file_hint');
        const modalTitle = document.getElementById('modal_title');
        const bulkSection = document.getElementById('bulk_upload_section');
        
        if (id > 0) {
            modalTitle.innerText = 'Edit Dokumen';
            fileInput.required = false;
            fileHint.innerText = 'Format: PDF, Word, Excel, PowerPoint. Biarkan kosong jika tidak ingin mengubah file.';
            if (bulkSection) bulkSection.style.display = 'none';
        } else {
            modalTitle.innerText = 'Upload Dokumen';
            fileInput.required = true;
            fileHint.innerText = 'Format: PDF, Word, Excel, PowerPoint.';
            if (bulkSection) bulkSection.style.display = 'block';
        }

        const modal = document.getElementById('uploadModal');
        modal.style.display = 'flex';
        // Add tiny animation
        modal.querySelector('div').style.transform = 'scale(0.95)';
        modal.querySelector('div').style.opacity = '0';
        setTimeout(() => {
            modal.querySelector('div').style.transition = '0.2s cubic-bezier(0.34, 1.56, 0.64, 1)';
            modal.querySelector('div').style.transform = 'scale(1)';
            modal.querySelector('div').style.opacity = '1';
        }, 10);
    }

    function closeUploadModal() {
        const modal = document.getElementById('uploadModal');
        modal.querySelector('div').style.transform = 'scale(0.95)';
        modal.querySelector('div').style.opacity = '0';
        setTimeout(() => {
            modal.style.display = 'none';
        }, 200);
    }

    function openCopyModal(id, judul) {
        document.getElementById('copy_doc_id_input').value = id;
        document.getElementById('copy_doc_title_display').value = judul;
        
        const modal = document.getElementById('copyModal');
        modal.style.display = 'flex';
        modal.querySelector('div').style.transform = 'scale(0.95)';
        modal.querySelector('div').style.opacity = '0';
        setTimeout(() => {
            modal.querySelector('div').style.transition = '0.2s cubic-bezier(0.34, 1.56, 0.64, 1)';
            modal.querySelector('div').style.transform = 'scale(1)';
            modal.querySelector('div').style.opacity = '1';
        }, 10);
    }

    function closeCopyModal() {
        const modal = document.getElementById('copyModal');
        modal.querySelector('div').style.transform = 'scale(0.95)';
        modal.querySelector('div').style.opacity = '0';
        setTimeout(() => {
            modal.style.display = 'none';
        }, 200);
    }
</script>

<?php include __DIR__ . '/../footer_multi_guru.php'; ?>
</body>
</html>
