<?php
$active_page = 'perpus_kategori';
$active_module = 'perpus';
// Layout wrapper for Master Kategori
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Kategori E-Book - Perpustakaan Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css'); ?>">
    
    <style>
        .dataTables_wrapper { padding: 1.5rem; }
        .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter { margin-bottom: 1.5rem; }
        .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate { padding-top: 1.5rem; }
        .dataTables_wrapper .dataTables_paginate .paginate_button { 
            border-radius: 8px !important; border: 1px solid #e2e8f0 !important; 
            background: #fff !important; color: #475569 !important; font-weight: 700 !important;
            padding: 5px 12px !important; margin: 0 4px !important; cursor: pointer;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f1f5f9 !important; color: #0f172a !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #0284c7 !important; color: #fff !important; border-color: #0284c7 !important; }
        
        .dataTables_wrapper select, .dataTables_wrapper input[type="search"] {
            border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 6px 12px;
            font-family: inherit; font-size: 0.85rem; color: #334155; outline: none; transition: 0.2s;
        }
        .dataTables_wrapper select:focus, .dataTables_wrapper input[type="search"]:focus {
            border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
        }
        .z-btn {
            padding: 8px 16px !important; border-radius: 8px !important; font-weight: 600 !important; font-size: 0.85rem !important;
            border: none !important; cursor: pointer !important; transition: 0.2s !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; gap: 8px !important;
        }
        .z-btn-primary { background: #0ea5e9 !important; color: white !important; }
        .z-btn-primary:hover { background: #0284c7 !important; }
        .z-btn-secondary { background: #f1f5f9 !important; color: #475569 !important; border: 1px solid #cbd5e1 !important; }
        .z-btn-secondary:hover { background: #e2e8f0 !important; }
        .z-btn-danger { background: #ef4444 !important; color: white !important; }
        .z-btn-danger:hover { background: #dc2626 !important; }

        @media (max-width: 768px) {
            .elite-page-header { flex-direction: column !important; text-align: center !important; gap: 1rem !important; padding: 1.5rem !important; }
            .elite-page-header h1 { font-size: 1.4rem !important; justify-content: center; }
            .z-scroll { padding: 1rem !important; }
            .z-header { padding: 0 1rem !important; }
            .dataTables_wrapper { padding: 1rem !important; }
            .z-header-right { gap: 1rem !important; }
            .z-header-right > div:nth-child(1), .z-header-right > a { display: none !important; }
        }
    </style>
</head>
<body style="background: #f8fafc; font-family: 'Inter', sans-serif;">

<div class="z-wrap">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>

    <div class="z-main">
        <header class="z-header">
            <div class="z-header-left">
                <button class="z-toggle-sidebar" onclick="zToggleSidebar()">
                    <i data-lucide="menu"></i>
                </button>
                <div class="header-breadcrumb" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;">
                    <a href="<?php echo base_url('admin/perpus'); ?>" class="parent" style="font-weight:800;letter-spacing:1px;font-size:0.8rem;color:#0ea5e9;text-decoration:none;">PERPUSTAKAAN DIGITAL</a>
                    <i data-lucide="chevron-right" class="sep" style="width:14px;color:#cbd5e1;"></i>
                    <span class="current" style="font-weight:700;color:#1e293b;">Master Kategori</span>
                </div>
            </div>
            <div class="z-header-right" style="display: flex; align-items: center; gap: 2rem;">
                <div style="display: flex; align-items: center; gap: 8px; background: #f0fdf4; padding: 6px 14px; border-radius: 100px; border: 1px solid #dcfce7; font-size: 0.75rem; font-weight: 700; color: #15803d;">
                    <div style="width: 8px; height: 8px; background: #22c55e; border-radius: 50%; box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);"></div>
                    <span>System Online</span>
                </div>
                <a href="<?php echo base_url('portal'); ?>" style="display:flex;align-items:center;gap:8px;padding:6px 16px;border-radius:100px;border:1px solid #bfdbfe;background:#eff6ff;color:#3b82f6;font-size:0.75rem;font-weight:800;text-decoration:none;transition:0.2s;white-space:nowrap;">
                    <i data-lucide="layout-grid" style="width:14px;height:14px;"></i> Portal HUB
                </a>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;background:linear-gradient(135deg,#0ea5e9 0%,#0284c7 100%);color:white;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;box-shadow:0 4px 12px rgba(14,165,233,0.2);"><?php echo $adminInitial; ?></div>
                    <div style="display:flex;flex-direction:column;">
                        <div style="font-size:0.85rem;font-weight:700;color:#1e293b;"><?php echo $adminNama; ?></div>
                        <div style="font-size:0.7rem;color:#94a3b8;">Super Administrator</div>
                    </div>
                </div>
                <a href="<?php echo base_url('logout'); ?>" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:10px;background:#fef2f2;color:#ef4444;text-decoration:none;transition:0.2s;border:1px solid #fee2e2;">
                    <i data-lucide="log-out" style="width:18px;height:18px;"></i>
                </a>
            </div>
        </header>

        <div class="z-scroll" style="padding: 1.25rem 2rem;">
            
            <?php if(isset($_SESSION['success'])): ?>
                <div style="background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                    <i data-lucide="check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['error'])): ?>
                <div style="background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                    <i data-lucide="alert-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Elite Page Header -->
            <div class="elite-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: var(--z-radius, 12px); margin-bottom: 2rem; color: white; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="tags" style="color: rgba(255,255,255,0.8);"></i> Master Kategori E-Book
                    </h1>
                    <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Kelola kategori untuk pengelompokan buku digital.</p>
                </div>
                <button onclick="document.getElementById('modalTambah').classList.add('open')" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(5px); color: white; padding: 12px 24px; border-radius: 12px; font-weight: 700; display: flex; align-items: center; gap: 8px; transition: 0.2s; border: none; cursor: pointer;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                    <i data-lucide="plus-circle" style="width: 20px; height: 20px;"></i> Tambah Kategori
                </button>
            </div>

            <!-- Table -->
            <div class="z-card" style="background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); overflow: hidden; border: 1px solid #f1f5f9;">
                <div style="overflow-x: auto;">
                    <table class="z-table" id="tableKategori">
                    <thead>
                        <tr>
                            <th style="width: 80px;">NO</th>
                            <th style="width: 150px;">KODE</th>
                            <th>NAMA KATEGORI</th>
                            <th>DESKRIPSI</th>
                            <th style="width: 150px; text-align: right;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach($kategoriList as $k): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><span style="background: #f1f5f9; padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 0.8rem; color: #475569;"><?php echo htmlspecialchars($k['kode_kategori']); ?></span></td>
                            <td style="font-weight: 700; color: #0f172a;"><?php echo htmlspecialchars($k['nama_kategori']); ?></td>
                            <td style="color: #64748b; font-size: 0.9rem;"><?php echo htmlspecialchars($k['deskripsi'] ?? '-'); ?></td>
                            <td style="text-align: right;">
                                <button onclick="editKategori(<?php echo $k['id']; ?>, '<?php echo addslashes($k['kode_kategori']); ?>', '<?php echo addslashes($k['nama_kategori']); ?>', '<?php echo addslashes($k['deskripsi'] ?? ''); ?>')" style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #fffbeb; color: #f59e0b; border-radius: 8px; margin-right: 5px; border: none; cursor: pointer;">
                                    <i data-lucide="edit" style="width: 16px;"></i>
                                </button>
                                <a href="javascript:void(0)" onclick="deleteKategori(<?php echo $k['id']; ?>)" style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #fef2f2; color: #ef4444; border-radius: 8px;">
                                    <i data-lucide="trash-2" style="width: 16px;"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Kategori -->
<div id="modalTambah" class="z-modal-ov">
    <div class="z-modal">
        <div class="z-modal-head">
            <h3 class="z-modal-title"><i data-lucide="tags"></i> <span id="modalTitle">Tambah Kategori</span></h3>
            <button type="button" class="z-modal-close" onclick="closeModal()"><i data-lucide="x"></i></button>
        </div>
        <form action="<?php echo base_url('admin/perpus/kategori'); ?>" method="POST">
            <input type="hidden" name="id" id="kat_id">
            <div class="z-modal-body">
                <div class="z-form-group">
                    <label>Kode Kategori <span style="color:red">*</span></label>
                    <input type="text" name="kode_kategori" id="kat_kode" class="z-input" required placeholder="Misal: FIC, SCI, dsb.">
                </div>
                <div class="z-form-group">
                    <label>Nama Kategori <span style="color:red">*</span></label>
                    <input type="text" name="nama_kategori" id="kat_nama" class="z-input" required placeholder="Misal: Fiksi, Sains, Sejarah">
                </div>
                <div class="z-form-group">
                    <label>Deskripsi Kategori</label>
                    <textarea name="deskripsi" id="kat_desk" class="z-input" rows="3" placeholder="Keterangan singkat kategori (opsional)"></textarea>
                </div>
            </div>
            <div class="z-modal-foot">
                <button type="button" class="z-btn z-btn-secondary" onclick="closeModal()">Batal</button>
                <button type="submit" class="z-btn z-btn-primary">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    lucide.createIcons();
    function zToggleSidebar() {
        document.querySelector('.z-sidebar').classList.toggle('open');
    }
    
    $(document).ready(function() {
        $('#tableKategori').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' }
        });
    });

    function closeModal() {
        document.getElementById('modalTambah').classList.remove('open');
        document.getElementById('kat_id').value = '';
        document.getElementById('kat_kode').value = '';
        document.getElementById('kat_nama').value = '';
        document.getElementById('kat_desk').value = '';
        document.getElementById('modalTitle').innerText = 'Tambah Kategori';
    }

    function editKategori(id, kode, nama, deskripsi) {
        document.getElementById('kat_id').value = id;
        document.getElementById('kat_kode').value = kode;
        document.getElementById('kat_nama').value = nama;
        document.getElementById('kat_desk').value = deskripsi;
        document.getElementById('modalTitle').innerText = 'Edit Kategori';
        document.getElementById('modalTambah').classList.add('open');
    }

    function deleteKategori(id) {
        Swal.fire({
            title: 'Hapus Kategori?',
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'z-btn z-btn-danger',
                cancelButton: 'z-btn z-btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?php echo base_url('admin/perpus/kategori/hapus/'); ?>" + id;
            }
        })
    }
</script>
</body>
</html>
