<?php
// data variables: $gurus, $guru_siswa_map, $selected_guru_id, $anak_asuh_list, $available_siswas
?>
<style>
    .z-action-btns { display: flex; gap: 6px; justify-content: center; }
    .action-btn {
        width: 28px; height: 28px; border-radius: 8px; display: flex;
        align-items: center; justify-content: center; border: none;
        cursor: pointer; transition: 0.2s;
    }
    .edit-btn { background: #eff6ff; color: #2563eb; text-decoration: none; }
    .delete-btn { background: #fff1f2; color: #e11d48; text-decoration: none; }
    .action-btn:hover { transform: translateY(-2px); filter: brightness(0.95); }

    .guru-info-main .name { font-weight: 800; color: #1e293b; font-size: 0.95rem; margin-bottom: 2px; }
    .guru-info-main .nip { font-size: 0.75rem; color: #94a3b8; font-family: 'JetBrains Mono', monospace; }
    
    .anak-asuh-badge {
        background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0;
        padding: 4px 12px; border-radius: 20px; font-weight: 700; font-size: 0.8rem;
    }

    /* Modal Form Select2 */
    .select2-container--default .select2-selection--multiple {
        border: 1.5px solid #cbd5e1; border-radius: 12px; padding: 4px;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #0ea5e9;
    }

    /* DataTables Custom Spacing & Form Styling */
    .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter { margin-bottom: 1.5rem; padding: 1.5rem 1.5rem 0 1.5rem; }
    .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate { padding: 1.5rem; }
    .dataTables_wrapper .dataTables_paginate .paginate_button { 
        border-radius: 12px !important; border: 1px solid #e2e8f0 !important; 
        background: #fff !important; color: #475569 !important; font-weight: 700 !important; 
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #0284c7 !important; color: #fff !important; border-color: #0284c7 !important; }
    
    .dataTables_wrapper select, .dataTables_wrapper input[type="search"] {
        border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 6px 12px;
        font-family: inherit; font-size: 0.85rem; color: #334155; outline: none; transition: 0.2s;
    }
    .dataTables_wrapper select:focus, .dataTables_wrapper input[type="search"]:focus {
        border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
    }
</style>

<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/_siakad_header.php'; ?>

        <div class="elite-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: var(--z-radius, 12px); margin-bottom: 2rem; color: white;">
            <div>
                <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                    <i data-lucide="users" style="color: rgba(255,255,255,0.8);"></i> Guru Wali (Anak Asuh)
                </h1>
                <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Delegasikan siswa tertentu kepada Guru Wali untuk dibina secara personal.</p>
            </div>
            <?php if ($selected_guru_id > 0): ?>
            <div style="margin-top: 15px;">
                <a href="<?php echo base_url('siakad/guru-wali'); ?>" class="z-btn z-btn-secondary" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white;">
                    <i data-lucide="arrow-left"></i> Kembali ke Daftar Guru
                </a>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($selected_guru_id == 0): ?>
        <!-- DAFTAR GURU -->
        <div class="z-panel">
            <div class="z-table-wrap">
                <table id="table-guru-wali" class="">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">NO</th>
                            <th>NAMA GURU WALI</th>
                            <th class="text-center">JUMLAH ANAK ASUH</th>
                            <th class="text-center" style="width: 150px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($gurus as $g): ?>
                        <tr>
                            <td class="text-center"><?php echo $no++; ?></td>
                            <td>
                                <div class="guru-info-main">
                                    <div class="name"><?php echo $g['nama']; ?></div>
                                    <div class="nip">NIP: <?php echo $g['nip'] ?: '-'; ?></div>
                                </div>
                            </td>
                            <td class="text-center">
                                <?php if($g['jumlah_anak_asuh'] > 0): ?>
                                    <span class="anak-asuh-badge"><?php echo $g['jumlah_anak_asuh']; ?> Siswa</span>
                                <?php else: ?>
                                    <span style="color:#94a3b8; font-size: 0.8rem; font-weight:600;">Belum ada</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="z-action-btns">
                                    <a href="?guru_id=<?php echo $g['id']; ?>" class="z-btn z-btn-primary" style="font-size:0.75rem; padding: 6px 12px;">
                                        <i data-lucide="users"></i> Kelola Anak Asuh
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <?php else: ?>
        <!-- KELOLA ANAK ASUH -->
        <?php
            $guru_selected = array_filter($gurus, function($g) use ($selected_guru_id) { return $g['id'] == $selected_guru_id; });
            $guru_selected = reset($guru_selected);
        ?>
        <div class="z-panel" style="margin-bottom: 1.5rem; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-color: #bae6fd; padding: 1.5rem; border-radius: 14px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div>
                    <h3 style="margin: 0 0 5px; color: #0369a1; font-weight: 800;">Daftar Anak Asuh: <?php echo $guru_selected['nama']; ?></h3>
                    <p style="margin:0; font-size:0.85rem; color:#0284c7;">Tambahkan siswa yang menjadi tanggung jawab mentor ini.</p>
                </div>
                <button class="z-btn z-btn-primary" onclick="openModalAsuh()">
                    <i data-lucide="user-plus"></i> Tambah Siswa
                </button>
            </div>
        </div>

        <div class="z-panel">
            <div class="z-table-wrap">
                <table id="table-anak-asuh" class="">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">NO</th>
                            <th>NAMA SISWA</th>
                            <th>KELAS</th>
                            <th class="text-center" style="width: 100px;">HAPUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($anak_asuh_list)): ?>
                            <tr><td colspan="4" class="text-center" style="padding: 2rem; color:#94a3b8;">Belum ada anak asuh yang ditambahkan.</td></tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($anak_asuh_list as $aa): ?>
                            <tr>
                                <td class="text-center"><?php echo $no++; ?></td>
                                <td>
                                    <div class="guru-info-main">
                                        <div class="name"><?php echo $aa['siswa']['nama']; ?></div>
                                        <div class="nip">NIS: <?php echo $aa['siswa']['nis'] ?: '-'; ?></div>
                                    </div>
                                </td>
                                <td>
                                    <span style="background:#f1f5f9; color:#475569; padding: 4px 10px; border-radius: 8px; font-weight:600; font-size:0.8rem;">
                                        <?php echo $aa['siswa']['nama_kelas'] ?: 'Tidak ada kelas'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="z-action-btns">
                                        <button class="action-btn delete-btn" onclick="hapusAnakAsuh(<?php echo $aa['mapping_id']; ?>, '<?php echo addslashes($aa['siswa']['nama']); ?>')">
                                            <i data-lucide="trash-2"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Modal Tambah Anak Asuh -->
        <div class="z-modal" id="modal-tambah-asuh" style="display:none;">
            <div class="z-modal-content" style="width: 600px; max-width: 95%;">
                <div class="z-modal-header">
                    <h3 id="modal-title-guru"><i data-lucide="user-plus"></i> Pilih Anak Asuh Baru</h3>
                    <button class="close-btn" onclick="document.getElementById('modal-tambah-asuh').style.display='none'"><i data-lucide="x"></i></button>
                </div>
                <form id="form-assign-asuh" onsubmit="event.preventDefault(); assignAsuh();">
                    <div class="z-modal-body">
                        <input type="hidden" name="guru_id" value="<?php echo $selected_guru_id; ?>">
                        
                        <div style="margin-bottom: 15px;">
                            <label style="display:block; font-weight:700; margin-bottom:8px; font-size:0.85rem;">1. Pilih Kelas</label>
                            <select id="filter-kelas" class="z-input" onchange="filterSiswaByKelas(this.value)">
                                <option value="all">-- Semua Kelas --</option>
                                <?php foreach($kelas_list as $kname => $kid): ?>
                                    <option value="<?php echo htmlspecialchars($kname); ?>"><?php echo htmlspecialchars($kname); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <label style="display:block; font-weight:700; margin-bottom:8px; font-size:0.85rem;">2. Pilih Siswa</label>
                            <div id="siswa-checkbox-container" style="max-height: 250px; overflow-y: auto; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 10px; background: #f8fafc;">
                                <?php if(empty($available_siswas)): ?>
                                    <div style="padding:10px; text-align:center; color:#94a3b8; font-size:0.85rem;">Semua siswa sudah memiliki Guru Wali.</div>
                                <?php else: ?>
                                    <?php foreach($available_siswas as $as): ?>
                                        <label class="siswa-item" data-kelas="<?php echo htmlspecialchars($as['nama_kelas'] ?: 'Tanpa Kelas'); ?>" style="display:flex; align-items:center; gap:10px; padding:8px 10px; border-bottom:1px solid #e2e8f0; cursor:pointer;">
                                            <input type="checkbox" name="siswa_ids[]" value="<?php echo $as['id']; ?>" style="width:16px; height:16px; accent-color:#0284c7;">
                                            <div style="font-size:0.85rem; color:#334155;">
                                                <div style="font-weight:700;"><?php echo htmlspecialchars($as['nama']); ?></div>
                                                <div style="font-size:0.75rem; color:#64748b;"><?php echo htmlspecialchars($as['nama_kelas'] ?: 'Tanpa Kelas'); ?></div>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="z-modal-footer">
                        <button type="button" class="z-btn z-btn-ghost" onclick="document.getElementById('modal-tambah-asuh').style.display='none'">Batal</button>
                        <button type="submit" class="z-btn z-btn-primary" id="btn-save-asuh">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <?php endif; ?>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    let successMsg = sessionStorage.getItem('siakad_success_msg');
    if (successMsg) {
        ZNS.alert(successMsg, 'Berhasil!');
        sessionStorage.removeItem('siakad_success_msg');
    }

    lucide.createIcons();
    
    if($('#table-guru-wali').length) {
        $('#table-guru-wali').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json' }
        });
    }
    
    if($('#table-anak-asuh').length) {
        $('#table-anak-asuh').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json' }
        });
    }
});

function openModalAsuh() {
    document.getElementById('modal-tambah-asuh').style.display='flex';
    // Reset filter
    document.getElementById('filter-kelas').value = 'all';
    filterSiswaByKelas('all');
}

function filterSiswaByKelas(kelas) {
    let items = document.querySelectorAll('.siswa-item');
    let count = 0;
    items.forEach(item => {
        if (kelas === 'all' || item.getAttribute('data-kelas') === kelas) {
            item.style.display = 'flex';
            count++;
        } else {
            item.style.display = 'none';
        }
    });
}

function assignAsuh() {
    let btn = $('#btn-save-asuh');
    let originalText = btn.html();
    btn.html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);
    
    $.ajax({
        url: '<?php echo base_url("siakad/guru-wali/assign"); ?>',
        type: 'POST',
        data: $('#form-assign-asuh').serialize(),
        success: function(res) {
            try { if(typeof res === 'string') res = JSON.parse(res); } catch(e){}
            if(res.status == 'success') {
                sessionStorage.setItem('siakad_success_msg', res.message);
                location.reload();
            } else {
                ZNS.showError(res.message || 'Terjadi kesalahan sistem');
                btn.html(originalText).prop('disabled', false);
            }
        },
        error: function(err) {
            console.error(err);
            ZNS.showError('Terjadi kesalahan jaringan.');
            btn.html(originalText).prop('disabled', false);
        }
    });
}

function hapusAnakAsuh(mapping_id, nama_siswa) {
    ZNS.confirm(`Anda yakin ingin menghapus <b>${nama_siswa}</b> dari daftar anak asuh guru ini?`, () => {
        $.ajax({
            url: '<?php echo base_url("siakad/guru-wali/unassign"); ?>',
            type: 'POST',
            data: { mapping_id: mapping_id },
            success: function(res) {
                try { if(typeof res === 'string') res = JSON.parse(res); } catch(e){}
                if(res.status == 'success') {
                    ZNS.showSuccess(res.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    ZNS.showError(res.message || 'Terjadi kesalahan sistem');
                }
            }
        });
    }, 'Hapus Anak Asuh?', 'Ya, Hapus', true);
}
</script>
