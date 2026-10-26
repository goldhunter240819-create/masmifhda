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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . date('YmdH')); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/multi_dashboard.css?v=' . date('YmdH')); ?>">
    
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"/>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <style>
        .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter { margin-bottom: 1.5rem; padding: 0 1.5rem; }
        .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate { padding: 1.5rem; }
        .dataTables_wrapper .dataTables_paginate .paginate_button { 
            border-radius: 8px !important; border: 1px solid #e2e8f0 !important; 
            background: #fff !important; color: #475569 !important; font-weight: 700 !important; 
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #8b5cf6 !important; color: #fff !important; border-color: #8b5cf6 !important; }
        .select2-container--default .select2-selection--multiple { border: 1px solid #cbd5e1; border-radius: 8px; padding: 5px; }
        .select2-container--default.select2-container--focus .select2-selection--multiple { border: 1px solid #8b5cf6; }

        /* Modal Custom CSS */
        .z-modal {
            display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            width: 100vw !important; height: 100vh !important;
            background: rgba(15, 23, 42, 0.55) !important;
            backdrop-filter: blur(10px) !important; z-index: 99999 !important;
            align-items: center !important; justify-content: center !important; padding: 20px !important;
            box-sizing: border-box !important; max-width: none !important; border-radius: 0 !important;
        }
        .z-modal-content {
            background: #fff; width: 100%; border-radius: 24px; overflow: hidden;
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.3);
            display: flex; flex-direction: column; max-height: 92vh; margin: auto;
            animation: modalFadeUp 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes modalFadeUp {
            from { opacity: 0; transform: translateY(30px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
    </style>
</head>
<body>
<div class="siakad-container">
    <?php include __DIR__ . '/../sidebar_multi_guru.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/../header_multi_guru.php'; ?>

        <div class="z-scroll" style="padding: 1.5rem var(--z-gap) var(--z-gap);">
            
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:1.5rem; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <a href="<?php echo base_url('guru/tugas_tambahan/pembina_ekstra'); ?>" class="z-btn-icon" style="background:white; border:1px solid #e2e8f0;"><i data-lucide="arrow-left"></i></a>
                    <div>
                        <h2 style="margin:0; font-size:1.4rem; font-weight:800; color:#0f172a;">Daftar Anggota</h2>
                        <p style="margin:0; font-size:0.85rem; color:#64748b;">Ekstrakurikuler: <?php echo htmlspecialchars($nama_ekstra); ?> (<?php echo htmlspecialchars($active_year_name); ?>)</p>
                    </div>
                </div>
                <div>
                    <button class="z-btn" onclick="$('#modal-add').css('display', 'flex');" style="background:#8b5cf6; color:white; font-weight:bold; border-radius:8px; border:none; padding:10px 20px; cursor:pointer;"><i data-lucide="user-plus" style="width:16px;height:16px;vertical-align:-3px;margin-right:5px;"></i> Tambah Anggota</button>
                </div>
            </div>

            <?php if(isset($_GET['success'])): ?>
                <div style="background:#f0f9ff; border:1px solid #0ea5e9; color:#0284c7; padding:15px; border-radius:10px; margin-bottom:1.5rem; display:flex; gap:10px; align-items:center;">
                    <i data-lucide="check-circle"></i>
                    <div><strong>Berhasil!</strong> <?php echo $_GET['success'] == 'add' ? 'Anggota berhasil ditambahkan.' : 'Anggota berhasil dihapus.'; ?></div>
                </div>
            <?php endif; ?>

            <div class="z-panel" style="padding:1.5rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <label style="font-weight: 700; color: #475569; font-size: 0.85rem;"><i data-lucide="filter" style="width:14px;height:14px;vertical-align:-2px;margin-right:4px;"></i> Filter Kelas:</label>
                        <select id="filter-kelas-tabel" style="padding: 8px 15px; border-radius: 8px; border: 1px solid #e2e8f0; outline: none; background: #f8fafc; font-family: inherit; font-weight: 600; color: #0f172a; min-width: 150px;">
                            <option value="">Semua Kelas</option>
                            <?php 
                                $kelasUnik = [];
                                foreach($anggotaList as $a) {
                                    if (!in_array($a['nama_kelas'], $kelasUnik) && $a['nama_kelas']) {
                                        $kelasUnik[] = $a['nama_kelas'];
                                    }
                                }
                                sort($kelasUnik);
                                foreach($kelasUnik as $ku) {
                                    echo '<option value="'.htmlspecialchars($ku).'">Kelas '.htmlspecialchars($ku).'</option>';
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <table id="table-anggota" class="z-table">
                    <thead>
                        <tr>
                            <th style="width:50px; text-align:center;">No</th>
                            <th>NISN</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th style="width:100px; text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach($anggotaList as $a): ?>
                        <tr>
                            <td style="text-align:center;"><?php echo $no++; ?></td>
                            <td style="font-family:'JetBrains Mono', monospace; font-size:0.85rem; font-weight:600; color:#475569;"><?php echo htmlspecialchars($a['nisn']); ?></td>
                            <td style="font-weight:700; color:#1e293b;"><?php echo htmlspecialchars($a['nama']); ?></td>
                            <td><span class="z-badge-outline"><?php echo htmlspecialchars($a['nama_kelas'] ?? 'Tidak Ada Kelas'); ?></span></td>
                            <td style="text-align:center;">
                                <a href="<?php echo base_url('guru/tugas_tambahan/pembina_ekstra/anggota/delete/<?php echo $a['id']; ?>?ekstra=<?php echo urlencode($nama_ekstra); ?>'); ?>" onclick="return confirm('Keluarkan siswa ini dari Ekstrakurikuler?')" style="background:#fff1f2; color:#e11d48; padding:6px 10px; border-radius:6px; display:inline-block; transition:0.2s;"><i data-lucide="trash-2" style="width:16px;height:16px;"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<!-- Modal Tambah Anggota -->
<div id="modal-add" class="z-modal">
    <div class="z-modal-content" style="max-width: 600px; border-radius: 20px; overflow: hidden; box-shadow: 0 25px 50px rgba(0,0,0,0.2);">
        <div class="z-modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1.5rem;">
            <h3 style="margin:0; font-size:1.15rem; color:#0f172a; display:flex; align-items:center; gap:8px; font-weight:800;">
                <div style="background:#ede9fe; color:#8b5cf6; padding:8px; border-radius:10px; display:flex;">
                    <i data-lucide="user-plus" style="width:20px;height:20px;"></i>
                </div>
                Tambah Anggota Baru
            </h3>
            <button type="button" onclick="$('#modal-add').css('display', 'none');" class="close-btn" style="background:none; border:none; color:#94a3b8; cursor:pointer; padding:5px; transition:0.2s;"><i data-lucide="x"></i></button>
        </div>
        <div class="z-modal-body" style="padding: 1.5rem;">
            <form action="<?php echo base_url('guru/tugas_tambahan/pembina_ekstra/anggota/add'); ?>" method="POST">
                <input type="hidden" name="nama_ekstra" value="<?php echo htmlspecialchars($nama_ekstra); ?>">
                
                <div style="margin-bottom:1rem;">
                    <label style="display:block; font-size:0.85rem; font-weight:800; color:#475569; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.5px;">Filter Kelas</label>
                    <select id="modal-filter-kelas" style="width:100%; padding:10px 15px; border-radius:8px; border:1px solid #e2e8f0; outline:none; font-family:inherit; color:#0f172a; background:#f8fafc;">
                        <option value="">Semua Kelas</option>
                        <?php 
                            // Re-use $kelasUnik from the table filter, but we need to derive it from $siswaTersedia
                            $kelasTersediaUnik = [];
                            foreach($siswaTersedia as $s) {
                                if (!in_array($s['nama_kelas'], $kelasTersediaUnik) && $s['nama_kelas']) {
                                    $kelasTersediaUnik[] = $s['nama_kelas'];
                                }
                            }
                            sort($kelasTersediaUnik);
                            foreach($kelasTersediaUnik as $ku) {
                                echo '<option value="'.htmlspecialchars($ku).'">Kelas '.htmlspecialchars($ku).'</option>';
                            }
                        ?>
                    </select>
                </div>

                <div style="margin-bottom:1.5rem;">
                    <label style="display:block; font-size:0.85rem; font-weight:800; color:#475569; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.5px;">Pilih Siswa (Bisa lebih dari 1)</label>
                    <select name="siswa_ids[]" id="select-siswa" multiple="multiple" style="width:100%;">
                        <?php 
                        $current_kelas = '';
                        foreach($siswaTersedia as $s): 
                            if ($current_kelas != $s['nama_kelas']) {
                                if ($current_kelas != '') echo "</optgroup>";
                                $current_kelas = $s['nama_kelas'];
                                echo "<optgroup label='Kelas " . htmlspecialchars($current_kelas) . "'>";
                            }
                        ?>
                            <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['nama']); ?></option>
                        <?php endforeach; 
                        if ($current_kelas != '') echo "</optgroup>";
                        ?>
                    </select>
                </div>

                <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:15px; margin-bottom:1.5rem; font-size:0.85rem; color:#16a34a; line-height:1.5; display:flex; gap:10px;">
                    <i data-lucide="info" style="width:18px;height:18px;flex-shrink:0;"></i> 
                    <div>Hanya menampilkan siswa yang belum bergabung di ekstrakurikuler ini. Kamu bisa mencari berdasarkan nama atau kelas.</div>
                </div>

                <div style="text-align:right; border-top:1px solid #f1f5f9; margin-top:1.5rem; padding-top:1.5rem;">
                    <button type="button" onclick="$('#modal-add').css('display', 'none');" class="z-btn z-btn-ghost" style="margin-right:10px; color:#64748b; font-weight:700;">Batal</button>
                    <button type="submit" class="z-btn" style="background:linear-gradient(135deg, #8b5cf6, #7c3aed); color:white; border:none; padding:10px 24px; border-radius:10px; font-weight:bold; cursor:pointer; box-shadow:0 4px 15px rgba(139,92,246,0.3);">
                        <i data-lucide="save" style="width:16px;height:16px;vertical-align:-3px;margin-right:5px;"></i> Tambahkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
    $(document).ready(function() {
        var table = $('#table-anggota').DataTable({
            pageLength: 25,
            language: {
                search: "",
                searchPlaceholder: "Cari NISN / Nama Siswa...",
                lengthMenu: "Tampilkan _MENU_ data",
                paginate: { next: 'Next', previous: 'Prev' }
            }
        });

        $('#filter-kelas-tabel').on('change', function() {
            var selectedClass = $(this).val();
            // Kelas is on column index 3 (No is 0, NISN is 1, Nama is 2, Kelas is 3)
            // But since the cell content includes 'Kelas' inside badge, we can search by exact string or just the value.
            // Since the badge text is literally the class name.
            if(selectedClass) {
                // Use regex for exact match to avoid "1" matching "10"
                table.column(3).search('^' + selectedClass + '$', true, false).draw();
            } else {
                table.column(3).search('').draw();
            }
        });

        // Simpan semua options awal Select2
        var allSiswaOptions = $('#select-siswa').html();

        $('#select-siswa').select2({
            placeholder: "Ketik nama siswa...",
            allowClear: true,
            dropdownParent: $('#modal-add')
        });

        // Event listener untuk Filter Kelas di dalam modal
        $('#modal-filter-kelas').on('change', function() {
            var selectedKelas = $(this).val();
            
            // Kosongkan dan destroy sementara
            $('#select-siswa').empty().select2('destroy');
            
            if (selectedKelas === '') {
                // Kembalikan semua
                $('#select-siswa').html(allSiswaOptions);
            } else {
                // Buat dummy element untuk membungkus html string, lalu cari optgroup yang pas
                var $wrapper = $('<div>').html(allSiswaOptions);
                var $matchedOptgroup = $wrapper.find("optgroup[label='Kelas " + selectedKelas + "']");
                $('#select-siswa').append($matchedOptgroup);
            }
            
            // Re-initialize Select2
            $('#select-siswa').select2({
                placeholder: "Ketik nama siswa...",
                allowClear: true,
                dropdownParent: $('#modal-add')
            });
        });
    });
</script>
</body>
</html>


