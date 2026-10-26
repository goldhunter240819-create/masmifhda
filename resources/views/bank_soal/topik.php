<?php
if (!isset($page_title)) $page_title = "Kelola Topik Soal";
if (!isset($active_page)) $active_page = "topik";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - MASMIFHDA</title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>?v=<?php echo time(); ?>">
    <link href="<?php echo base_url('public/assets/css/zamrud.css'); ?>" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* DataTables Custom Spacing & Form Styling from Zamrud UI Rule 20 */
        .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter { margin-bottom: 1.5rem; padding: 1.5rem 1.5rem 0 1.5rem; }
        .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate { padding: 1.5rem; }
        .dataTables_wrapper .dataTables_paginate .paginate_button { 
            border-radius: 8px !important; border: 1px solid #e2e8f0 !important; 
            background: #fff !important; color: #475569 !important; font-weight: 600 !important;
            padding: 6px 12px !important; margin: 0 3px !important; transition: 0.2s;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f1f5f9 !important; border-color: #cbd5e1 !important; color: #0f172a !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current { 
            background: #0ea5e9 !important; color: #fff !important; border-color: #0ea5e9 !important; 
        }
        
        .dataTables_wrapper select, .dataTables_wrapper input[type="search"] {
            border: 1px solid #e2e8f0; border-radius: 10px; padding: 8px 14px;
            font-family: inherit; font-size: 0.85rem; color: #0f172a; outline: none; transition: 0.2s;
            background-color: #f8fafc;
        }
        .dataTables_wrapper select:focus, .dataTables_wrapper input[type="search"]:focus {
            border-color: #0ea5e9; background-color: white; box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
        }
        
        /* Table Edge Alignment */
        .z-table th:first-child, .z-table td:first-child { padding-left: 1.5rem !important; }
        .z-table th:last-child, .z-table td:last-child { padding-right: 1.5rem !important; }

        /* Action Buttons Alignment Fix */
        .btn-action {
            width: 32px !important; height: 32px !important; padding: 0 !important;
            display: inline-flex !important; justify-content: center !important; align-items: center !important;
            border-radius: 8px !important; border: none !important; cursor: pointer; transition: 0.2s; outline: none !important;
        }
        .btn-action svg { width: 16px !important; height: 16px !important; margin: 0 !important; }
        .btn-edit { background: #e0f2fe !important; color: #0ea5e9 !important; }
        .btn-edit:hover { background: #bae6fd !important; }
        .btn-delete { background: #fee2e2 !important; color: #ef4444 !important; }
        .btn-delete:hover { background: #fecaca !important; }

        .welcome-hero::before {
            content: ''; position: absolute; inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.15' fill-rule='evenodd'%3E%3Ccircle cx='3' cy='3' r='3'/%3E%3Ccircle cx='13' cy='13' r='3'/%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none; opacity: 0.5;
        }
    </style>
</head>
<body class="z-bg">

<div class="z-wrap">
    <?php include __DIR__ . '/_sidebar.php'; ?>

    <main class="z-main">
        <?php 
        $page_badge = 'Bank Soal';
        include __DIR__ . '/../siakad/_siakad_header.php'; 
        ?>

        <div class="z-content">
            <!-- Hero Banner -->
            <div class="elite-page-header welcome-hero" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: var(--z-radius, 12px); margin-bottom: 2rem; color: white; position: relative; overflow: hidden;">
                <div style="position: relative; z-index: 1;">
                    <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="library" style="color: rgba(255,255,255,0.8);"></i> Kelola Topik Ujian
                    </h1>
                    <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Kelola topik agenda ujian seperti PTS, PAS, PAT, atau Latihan Harian.</p>
                </div>
            </div>

            <!-- Table Card -->
            <div class="z-card" style="padding: 0;">
                <div style="padding: 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0; font-size: 1.1rem;">Daftar Topik Ujian</h3>
                    <button class="btn btn-primary" onclick="document.getElementById('modalTambah').classList.add('open')">
                        <i data-lucide="plus"></i> Tambah Topik
                    </button>
                </div>
                
                <table id="tabelTopik" class="z-table" style="width:100%;">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Jenis / Tingkat</th>
                            <th>Nama Topik Ujian</th>
                            <th>Keterangan</th>
                            <th style="text-align: center;">Jml Soal</th>
                            <th style="text-align: center; width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach($topiks as $t): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><span class="z-badge" style="background: #e0f2fe; color: #0284c7;"><?= htmlspecialchars($t['jenis']) ?></span></td>
                            <td style="font-weight: 600; color: #334155;"><?= htmlspecialchars($t['nama_topik']) ?></td>
                            <td style="color: #64748b;"><?= htmlspecialchars($t['keterangan']) ?></td>
                            <td style="text-align: center;"><span class="z-badge" style="background: #f1f5f9; color: #475569;"><?= $t['jml_soal'] ?> Soal</span></td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 5px; justify-content: center;">
                                    <button type="button" class="btn-action btn-edit" onclick="openEditModal(this, <?= $t['id'] ?>)" title="Edit"><i data-lucide="edit"></i></button>
                                    <button type="button" class="btn-action btn-delete" onclick="deleteTopik(<?= $t['id'] ?>)" title="Hapus"><i data-lucide="trash-2"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($topiks)): ?>
                        <tr><td colspan="6" style="text-align: center; color: #64748b; padding: 2rem;">Belum ada topik ujian. Silakan tambah topik baru.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Modal Tambah Topik (Zamrud Style) -->
<div id="modalTambah" class="z-modal-ov">
    <div class="z-modal">
        <div class="z-modal-head">
            <h3 class="z-modal-title"><i data-lucide="plus-circle"></i> Tambah Topik Ujian Baru</h3>
            <button class="z-modal-close" type="button" onclick="document.getElementById('modalTambah').classList.remove('open')">
                <i data-lucide="x"></i>
            </button>
        </div>
        <form action="<?= base_url('bank_soal/topik/simpan') ?>" method="POST">
            <div class="z-modal-body">
                <div class="z-form-group">
                    <label>Jenis Ujian <span style="color:red;">*</span></label>
                    <select name="jenis" class="z-input" required>
                        <option value="">-- Pilih Jenis Ujian --</option>
                        <option value="PAS">Penilaian Akhir Semester (PAS)</option>
                        <option value="PAT">Penilaian Akhir Tahun (PAT)</option>
                        <option value="PTS">Penilaian Tengah Semester (PTS)</option>
                        <option value="UH">Ulangan Harian (UH)</option>
                        <option value="TO">Try Out / Simulasi</option>
                        <option value="Lainnya">Lainnya...</option>
                    </select>
                </div>
                <div class="z-form-group">
                    <label>Nama Topik Ujian <span style="color:red;">*</span></label>
                    <input type="text" name="nama_topik" class="z-input" placeholder="Contoh: PAS Ganjil 2026/2027" required>
                </div>
                <div class="z-form-group">
                    <label>Keterangan (Opsional)</label>
                    <textarea name="keterangan" class="z-input" rows="3" placeholder="Tambahkan catatan khusus untuk topik ini..."></textarea>
                </div>
            </div>
            <div class="z-modal-foot">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalTambah').classList.remove('open')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Topik</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Topik (Zamrud Style) -->
<div id="modalEdit" class="z-modal-ov">
    <div class="z-modal">
        <div class="z-modal-head">
            <h3 class="z-modal-title"><i data-lucide="edit-3"></i> Edit Topik Ujian</h3>
            <button class="z-modal-close" type="button" onclick="document.getElementById('modalEdit').classList.remove('open')">
                <i data-lucide="x"></i>
            </button>
        </div>
        <form action="<?= base_url('bank_soal/topik/simpan') ?>" method="POST">
            <input type="hidden" name="id" id="edit_id">
            <div class="z-modal-body">
                <div class="z-form-group">
                    <label>Jenis Ujian <span style="color:red;">*</span></label>
                    <select name="jenis" id="edit_jenis" class="z-input" required>
                        <option value="">-- Pilih Jenis Ujian --</option>
                        <option value="PAS">Penilaian Akhir Semester (PAS)</option>
                        <option value="PAT">Penilaian Akhir Tahun (PAT)</option>
                        <option value="PTS">Penilaian Tengah Semester (PTS)</option>
                        <option value="UH">Ulangan Harian (UH)</option>
                        <option value="TO">Try Out / Simulasi</option>
                        <option value="Lainnya">Lainnya...</option>
                    </select>
                </div>
                <div class="z-form-group">
                    <label>Nama Topik Ujian <span style="color:red;">*</span></label>
                    <input type="text" name="nama_topik" id="edit_nama" class="z-input" required>
                </div>
                <div class="z-form-group">
                    <label>Keterangan (Opsional)</label>
                    <textarea name="keterangan" id="edit_ket" class="z-input" rows="3"></textarea>
                </div>
            </div>
            <div class="z-modal-foot">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalEdit').classList.remove('open')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    lucide.createIcons();
    function zToggleSidebar() {
        document.querySelector('.z-sidebar').classList.toggle('open');
    }
    
    function openEditModal(btn, id) {
        const row = btn.closest('tr');
        const jenis = row.cells[1].innerText.trim();
        const nama = row.cells[2].innerText.trim();
        const ket = row.cells[3].innerText.trim();
        
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_ket').value = ket;
        
        const sel = document.getElementById('edit_jenis');
        for (let i = 0; i < sel.options.length; i++) {
            if (sel.options[i].text === jenis || sel.options[i].value === jenis) {
                sel.selectedIndex = i; break;
            }
        }
        
        document.getElementById('modalEdit').classList.add('open');
    }

    function deleteTopik(id) {
        Swal.fire({
            title: 'Hapus Topik?',
            text: 'Aksi ini tidak bisa dibatalkan! Semua soal di dalam topik ini juga akan ikut terhapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('bank_soal/topik/hapus?id=') ?>" + id;
            }
        });
    }

    // Tampilkan pesan SweetAlert jika ada parameter success/deleted di URL
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.has('success')) {
            Swal.fire('Berhasil!', 'Data topik berhasil disimpan.', 'success');
        } else if(urlParams.has('deleted')) {
            Swal.fire('Terhapus!', 'Data topik berhasil dihapus.', 'success');
        }
    });

    $(document).ready(function() {
        $('#tabelTopik').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            }
        });
    });
</script>
</body>
</html>
