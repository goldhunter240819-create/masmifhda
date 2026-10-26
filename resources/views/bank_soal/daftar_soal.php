<?php
if (!isset($page_title)) $page_title = "Daftar Soal - Bank Soal";
if (!isset($active_page)) $active_page = "kelola_soal"; // tetep highlight Kelola Soal di sidebar
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php include __DIR__ . '/../admin/_zamrud_head.php'; ?>
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <style>
        .welcome-hero::before {
            content: ''; position: absolute; inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.15' fill-rule='evenodd'%3E%3Ccircle cx='3' cy='3' r='3'/%3E%3Ccircle cx='13' cy='13' r='3'/%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none; opacity: 0.5;
        }
        /* DataTables Custom Styling */
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
                    <a href="<?php echo base_url('bank_soal/kelola_soal?topik_id=' . $topikInfo['id']); ?>" style="color: rgba(255,255,255,0.8); text-decoration: none; display: inline-flex; align-items: center; gap: 5px; font-size: 0.9rem; margin-bottom: 10px;">
                        <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Kembali ke Daftar Mapel
                    </a>
                    <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="file-question" style="color: rgba(255,255,255,0.8);"></i> Daftar Soal: <?= htmlspecialchars($mapel_nama) ?>
                    </h1>
                    <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Topik: <strong><?= htmlspecialchars($topikInfo['jenis'] . ' - ' . $topikInfo['nama_topik']) ?></strong></p>
                </div>
            </div>

            <!-- Table Card -->
            <div class="z-card" style="padding: 0;">
                <div style="padding: 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0; font-size: 1.1rem;">Daftar Soal Tersimpan <span class="z-badge" style="margin-left:10px; background:#e0f2fe; color:#0284c7;">Total: <?= count($soals) ?> Soal</span></h3>
                    <a href="<?php echo base_url('bank_soal/tambah_soal?topik_id='.$topikInfo['id'].'&mapel='.urlencode($mapel_nama)); ?>" class="btn btn-primary">
                        <i data-lucide="plus"></i> Tambah Soal
                    </a>
                </div>
                
                <table id="tabelSoal" class="z-table" style="width:100%;">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th style="width: 150px;">Tipe Soal</th>
                            <th>Cuplikan Pertanyaan</th>
                            <th style="text-align: center; width: 80px;">Skor</th>
                            <th style="text-align: center; width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no=1; 
                        $tipe_badges = [
                            'sec_pg' => ['Pilihan Ganda', '#f1f5f9', '#475569'],
                            'sec_pgk' => ['PG Kompleks', '#e0e7ff', '#4338ca'],
                            'sec_bs' => ['Benar / Salah', '#dcfce7', '#15803d'],
                            'sec_jodoh' => ['Menjodohkan', '#fae8ff', '#a21caf'],
                            'sec_isian' => ['Isian Singkat', '#ffedd5', '#c2410c'],
                            'sec_essay' => ['Esai / Uraian', '#fef3c7', '#d97706']
                        ];
                        foreach($soals as $s): 
                            $badge = $tipe_badges[$s['tipe_soal']] ?? ['Unknown', '#eee', '#333'];
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><span class="z-badge" style="background: <?= $badge[1] ?>; color: <?= $badge[2] ?>;"><?= $badge[0] ?></span></td>
                            <td style="color: #334155;"><?= strip_tags(substr($s['pertanyaan'], 0, 100)) ?>...</td>
                            <td style="text-align: center; font-weight: 600;"><?= $s['skor_maksimal'] ?></td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 5px; justify-content: center;">
                                    <button type="button" class="btn-action btn-delete" onclick="deleteSoal(<?= $s['id'] ?>)" title="Hapus"><i data-lucide="trash-2"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    lucide.createIcons();
    function zToggleSidebar() {
        document.querySelector('.z-sidebar').classList.toggle('open');
    }

    function deleteSoal(id) {
        Swal.fire({
            title: 'Hapus Soal?',
            text: 'Soal ini akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('bank_soal/hapus_soal?id=') ?>" + id;
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.has('success')) {
            Swal.fire('Berhasil!', 'Soal berhasil disimpan.', 'success');
        } else if(urlParams.has('deleted')) {
            Swal.fire('Terhapus!', 'Soal berhasil dihapus.', 'success');
        }
    });

    $(document).ready(function() {
        $('#tabelSoal').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            }
        });
    });
</script>
</body>
</html>
