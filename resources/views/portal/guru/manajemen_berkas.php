<?php
$nama = $_SESSION['nama'] ?? ($_SESSION['username'] ?? 'Guru');
$roleName = !empty($_SESSION['is_kamad']) ? "Kepala Madrasah" : "Guru / Tendik";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusat Dokumen & Perangkat Saya — Portal Guru</title>
    <link rel="icon" type="image/png" href="<?= \App\Core\Branding::getInstitusiFavicon() ?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/css/plus-jakarta-sans.css') ?>">
    <script src="<?= base_url('public/assets/js/lucide.min.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('public/assets/css/zamrud.css?v=' . date('YmdH')) ?>">
    <link rel="stylesheet" href="<?= base_url('public/assets/css/multi_dashboard.css?v=' . date('YmdH')) ?>">
    <style>
        .tabs-header-guru {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #e2e8f0;
            padding: 4px;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            width: fit-content;
        }
        .tab-btn-guru {
            padding: 8px 18px;
            border-radius: 9px;
            border: none;
            background: transparent;
            color: #64748b;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: all 0.2s;
            text-decoration: none;
        }
        .tab-btn-guru.active {
            background: #ffffff;
            color: #0284c7;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        }

        .custom-card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }
        .item-file-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            transition: all 0.2s;
        }
        .item-file-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.05);
            border-color: #cbd5e1;
        }

        /* Modal */
        .modal-wrap {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .modal-wrap.active { display: flex; }
        .modal-box {
            background: #ffffff;
            width: 100%;
            max-width: 520px;
            border-radius: 16px;
            padding: 1.75rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            position: relative;
        }
        .form-label {
            display: block;
            margin-bottom: 6px;
            font-size: 0.82rem;
            font-weight: 700;
            color: #334155;
        }
        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            font-size: 0.88rem;
            font-family: inherit;
            box-sizing: border-box;
            background: #f8fafc;
            outline: none;
        }
        .form-input:focus {
            border-color: #0ea5e9;
            background: #fff;
        }
    </style>
</head>
<body>

<div class="siakad-container">
    <?php include __DIR__ . '/../sidebar_multi_guru.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/../header_multi_guru.php'; ?>

        <div class="z-scroll" style="padding: 1.5rem var(--z-gap) var(--z-gap);">

            <!-- Alerts -->
            <?php if(isset($_GET['msg']) && $_GET['msg'] === 'uploaded'): ?>
            <div style="background:#f0fdf4; border:1px solid #86efac; color:#166534; padding:12px 16px; border-radius:10px; margin-bottom:1.5rem; display:flex; align-items:center; gap:8px; font-size:0.9rem; font-weight:600;">
                <i data-lucide="check-circle" style="width:18px;height:18px;"></i> Dokumen berhasil diunggah!
            </div>
            <?php endif; ?>

            <?php if(isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
            <div style="background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:12px 16px; border-radius:10px; margin-bottom:1.5rem; display:flex; align-items:center; gap:8px; font-size:0.9rem; font-weight:600;">
                <i data-lucide="trash-2" style="width:18px;height:18px;"></i> Dokumen telah berhasil dihapus.
            </div>
            <?php endif; ?>

            <!-- Hero Banner -->
            <div class="elite-page-header" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); padding: 2rem 2.2rem; border-radius: var(--z-radius, 14px); margin-bottom: 2rem; color: white;">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px;">
                    <div>
                        <h1 style="font-size: 1.7rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                            <i data-lucide="folder-check" style="color: rgba(255,255,255,0.85);"></i> Manajemen Berkas Saya
                        </h1>
                        <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.92rem;">
                            Kelola perangkat pembelajaran digital (Modul Ajar, ATP, Prota, Promes) dan simpan arsip portofolio pribadi Anda.
                        </p>
                    </div>
                    <div style="display:flex; gap:8px;">
                        <button type="button" onclick="document.getElementById('modalUploadPribadi').classList.add('active')" class="z-btn" style="background:#ffffff; color:#0369a1; border:none; padding:10px 16px; font-weight:700; border-radius:10px; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
                            <i data-lucide="upload" style="width:16px;height:16px;"></i> Upload Berkas Pribadi
                        </button>
                        <a href="<?= base_url('guru/perangkat') ?>" class="z-btn" style="background:rgba(255,255,255,0.2); color:#ffffff; border:1px solid rgba(255,255,255,0.4); padding:10px 16px; font-weight:700; border-radius:10px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                            <i data-lucide="book-open" style="width:16px;height:16px;"></i> Input Perangkat Mengajar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tabs Nav -->
            <div class="tabs-header-guru">
                <a href="?tab=perangkat" class="tab-btn-guru <?= $active_tab === 'perangkat' ? 'active' : '' ?>">
                    <i data-lucide="book-marked" style="width:16px;height:16px;"></i>
                    Perangkat Pembelajaran Saya (<?= count($myPerangkat) ?>)
                </a>
                <a href="?tab=pribadi" class="tab-btn-guru <?= $active_tab === 'pribadi' ? 'active' : '' ?>">
                    <i data-lucide="file-badge" style="width:16px;height:16px;"></i>
                    Berkas Pribadi & Portofolio (<?= count($myBerkasPribadi) ?>)
                </a>
            </div>

            <!-- TAB 1: PERANGKAT SAYA -->
            <?php if($active_tab === 'perangkat'): ?>
                <?php if(empty($myPerangkat)): ?>
                    <div style="background:#ffffff; border:1px dashed #cbd5e1; border-radius:16px; padding:3.5rem 2rem; text-align:center; color:#64748b;">
                        <i data-lucide="folder-x" style="width:48px;height:48px;margin-bottom:12px;color:#cbd5e1;"></i>
                        <h3 style="margin:0 0 6px 0; color:#1e293b; font-size:1.1rem;">Belum Ada Perangkat Terunggah</h3>
                        <p style="margin:0 0 1rem 0; font-size:0.88rem;">Unggah RPP, Modul Ajar, atau Silabus Anda untuk memenuhi supervisi kurikulum & PKKM.</p>
                        <a href="<?= base_url('guru/perangkat') ?>" class="z-btn" style="background:#0284c7; color:#fff; padding:9px 18px; border-radius:9px; text-decoration:none; font-weight:700; display:inline-flex; align-items:center; gap:6px;">
                            <i data-lucide="plus-circle" style="width:16px;height:16px;"></i> Unggah Sekarang di Portal Perangkat
                        </a>
                    </div>
                <?php else: ?>
                    <div class="custom-card-grid">
                        <?php foreach($myPerangkat as $mp): 
                            $fileUrl = base_url('public/uploads/guru_perangkat/' . $mp['file_path']);
                        ?>
                        <div class="item-file-card">
                            <div>
                                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
                                    <span style="font-size:0.75rem; font-weight:700; padding:3px 9px; border-radius:6px; background:#e0f2fe; color:#0284c7;">
                                        <?= htmlspecialchars($mp['kategori'] ?: 'Perangkat') ?>
                                    </span>
                                    <span style="font-size:0.72rem; font-weight:700; color:#16a34a; background:#f0fdf4; padding:2px 8px; border-radius:100px;">
                                        <?= htmlspecialchars($mp['status_review'] ?: 'Menunggu') ?>
                                    </span>
                                </div>
                                <h4 style="margin:0 0 4px 0; font-size:0.95rem; font-weight:800; color:#0f172a;">
                                    <?= htmlspecialchars($mp['nama_berkas']) ?>
                                </h4>
                                <div style="font-size:0.8rem; color:#64748b;">
                                    <?= htmlspecialchars($mp['mapel_nama'] ?: '-') ?> &bull; Kelas <?= htmlspecialchars($mp['kelas_nama'] ?: '-') ?>
                                </div>
                            </div>
                            <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #f1f5f9; padding-top:10px;">
                                <div style="font-size:0.72rem; color:#94a3b8;">
                                    <?= date('d M Y', strtotime($mp['created_at'])) ?>
                                </div>
                                <a href="<?= $fileUrl ?>" target="_blank" style="display:inline-flex; align-items:center; gap:4px; padding:6px 12px; background:#f1f5f9; color:#0284c7; border-radius:8px; font-weight:700; font-size:0.78rem; text-decoration:none;">
                                    <i data-lucide="eye" style="width:13px;height:13px;"></i> Lihat File
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- TAB 2: BERKAS PRIBADI SAYA -->
            <?php if($active_tab === 'pribadi'): ?>
                <?php if(empty($myBerkasPribadi)): ?>
                    <div style="background:#ffffff; border:1px dashed #cbd5e1; border-radius:16px; padding:3.5rem 2rem; text-align:center; color:#64748b;">
                        <i data-lucide="file-plus-2" style="width:48px;height:48px;margin-bottom:12px;color:#cbd5e1;"></i>
                        <h3 style="margin:0 0 6px 0; color:#1e293b; font-size:1.1rem;">Belum Ada Berkas Pribadi</h3>
                        <p style="margin:0 0 1rem 0; font-size:0.88rem;">Simpan SK Pengangkatan, Sertifikat Pendidik, Ijazah, atau portofolio dinas Anda di sini.</p>
                        <button type="button" onclick="document.getElementById('modalUploadPribadi').classList.add('active')" class="z-btn" style="background:#0284c7; color:#fff; border:none; padding:9px 18px; border-radius:9px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
                            <i data-lucide="upload" style="width:16px;height:16px;"></i> Upload Dokumen Pertama
                        </button>
                    </div>
                <?php else: ?>
                    <div class="custom-card-grid">
                        <?php foreach($myBerkasPribadi as $bp): 
                            $bpFileUrl = base_url('public/uploads/guru_berkas/' . $bp['file_path']);
                        ?>
                        <div class="item-file-card">
                            <div>
                                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
                                    <span style="font-size:0.75rem; font-weight:700; padding:3px 9px; border-radius:6px; background:#f1f5f9; color:#475569;">
                                        <?= htmlspecialchars($bp['kategori'] ?? 'Portofolio') ?>
                                    </span>
                                </div>
                                <h4 style="margin:0 0 4px 0; font-size:0.95rem; font-weight:800; color:#0f172a;">
                                    <?= htmlspecialchars($bp['nama_berkas']) ?>
                                </h4>
                                <div style="font-size:0.78rem; color:#64748b;">
                                    <?= htmlspecialchars($bp['file_path']) ?>
                                </div>
                            </div>
                            <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #f1f5f9; padding-top:10px;">
                                <div style="font-size:0.72rem; color:#94a3b8;">
                                    <?= date('d M Y, H:i', strtotime($bp['created_at'])) ?>
                                </div>
                                <div style="display:flex; gap:6px;">
                                    <a href="<?= $bpFileUrl ?>" target="_blank" style="display:inline-flex; align-items:center; gap:4px; padding:6px 12px; background:#eff6ff; color:#0284c7; border-radius:8px; font-weight:700; font-size:0.78rem; text-decoration:none;">
                                        <i data-lucide="eye" style="width:13px;height:13px;"></i> Buka
                                    </a>
                                    <form method="POST" action="<?= base_url('guru/manajemen-berkas/delete-pribadi') ?>" style="margin:0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berkas ini?');">
                                        <input type="hidden" name="id" value="<?= $bp['id'] ?>">
                                        <button type="submit" style="background:#fee2e2; border:none; color:#dc2626; padding:6px 10px; border-radius:8px; cursor:pointer;" title="Hapus">
                                            <i data-lucide="trash-2" style="width:13px;height:13px;"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Modal Upload Berkas Pribadi -->
<div id="modalUploadPribadi" class="modal-wrap" onclick="if(event.target===this) this.classList.remove('active')">
    <div class="modal-box">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:8px;">
                <i data-lucide="upload-cloud" style="width:20px;height:20px;color:#0ea5e9;"></i> Upload Berkas Pribadi
            </h3>
            <button type="button" onclick="document.getElementById('modalUploadPribadi').classList.remove('active')" style="background:none; border:none; cursor:pointer; color:#94a3b8;">
                <i data-lucide="x" style="width:20px;height:20px;"></i>
            </button>
        </div>

        <form method="POST" action="<?= base_url('guru/manajemen-berkas/upload-pribadi') ?>" enctype="multipart/form-data">
            <div style="margin-bottom:1rem;">
                <label class="form-label">Nama Berkas / Dokumen <span style="color:#ef4444;">*</span></label>
                <input type="text" name="nama_berkas" class="form-input" placeholder="Contoh: SK Mengajar 2026/2027 atau Sertifikat Pendidik" required>
            </div>

            <div style="margin-bottom:1rem;">
                <label class="form-label">Kategori Berkas</label>
                <select name="kategori" class="form-input">
                    <option value="SK Kepegawaian">SK Kepegawaian / Mengajar</option>
                    <option value="Sertifikat">Sertifikat / Piagam Pelatihan</option>
                    <option value="Ijazah">Ijazah & Transkrip Nilai</option>
                    <option value="Dokumen Pribadi">Dokumen Pribadi (KTP, NUPTK, KK)</option>
                    <option value="Portofolio">Portofolio & Karya Ilmiah</option>
                    <option value="Umum">Lainnya / Umum</option>
                </select>
            </div>

            <div style="margin-bottom:1.5rem;">
                <label class="form-label">File Dokumen (PDF / Word / JPG / PNG) <span style="color:#ef4444;">*</span></label>
                <input type="file" name="berkas_file" class="form-input" accept=".pdf,.doc,.docx,.xls,.xlsx,image/*" required>
                <div style="font-size:0.72rem; color:#94a3b8; margin-top:4px;">Format yang didukung: PDF, DOCX, XLSX, JPG, PNG (Maks 10MB)</div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:8px;">
                <button type="button" onclick="document.getElementById('modalUploadPribadi').classList.remove('active')" class="z-btn" style="background:#f1f5f9; color:#475569; border:none; padding:9px 16px; border-radius:8px; font-weight:700; cursor:pointer;">
                    Batal
                </button>
                <button type="submit" class="z-btn" style="background:#0ea5e9; color:#ffffff; border:none; padding:9px 20px; border-radius:8px; font-weight:700; cursor:pointer; box-shadow:0 4px 10px rgba(14,165,233,0.3);">
                    <i data-lucide="upload" style="width:14px;height:14px;margin-right:4px;"></i> Upload Berkas
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    lucide.createIcons();
</script>

</body>
</html>

