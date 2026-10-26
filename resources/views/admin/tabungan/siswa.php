<?php
$page_title = 'Rekening Tabungan Siswa';
include __DIR__ . '/_layout_top.php';

$selected_kelas = intval($_GET['kelas_id'] ?? 0);

// Hitung statistik cepat
$total_tampil = count($siswa_list);
$sudah_buka = 0;
foreach($siswa_list as $s) {
    if(!empty($s['tabungan_id'])) $sudah_buka++;
}
?>

<!-- Header Banner Elite Sultan -->
<div class="elite-page-header" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
    <div class="header-bg-shapes"></div>
    <div class="header-content-inner">
        <div>
            <span class="header-badge" style="background: rgba(16,185,129,0.2); color: #34d399; border-color: rgba(16,185,129,0.3);">
                KELOLA NASABAH TABUNGAN
            </span>
            <div class="header-text-premium">
                <h1 style="color:#fff;"><i data-lucide="wallet-cards" style="color:#34d399;"></i> Kelola Rekening Tabungan Siswa</h1>
                <p style="color: rgba(255,255,255,0.75);">Filter per kelas untuk melihat siswa yang sudah atau belum membuka rekening</p>
            </div>
        </div>
        <div class="header-actions-premium" style="display:flex; gap:12px; margin-top: 15px;">
            <?php if($selected_kelas > 0 && ($total_tampil - $sudah_buka) > 0): ?>
            <form method="POST" action="<?= base_url('admin/tabungan/siswa?kelas_id=' . $selected_kelas) ?>" onsubmit="return confirm('Buka rekening tabungan sekaligus untuk semua siswa yang belum membuka rekening di kelas ini?');">
                <input type="hidden" name="action" value="buka_semua_kelas">
                <input type="hidden" name="kelas_target" value="<?= $selected_kelas ?>">
                <button type="submit" class="e-btn e-btn-primary" style="background: #10b981; box-shadow: 0 10px 25px rgba(16,185,129,0.3);">
                    <i data-lucide="users"></i> Buka Rekening Massal 1 Kelas Ini
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if(isset($message) && !empty($message)): ?>
<div style="background: <?= $status=='success'?'#ecfdf5':'#fff1f2' ?>; border: 1px solid <?= $status=='success'?'#a7f3d0':'#fecdd3' ?>; color: <?= $status=='success'?'#065f46':'#9f1239' ?>; padding: 14px 18px; border-radius: 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 700;">
    <i data-lucide="<?= $status=='success'?'check-circle':'alert-circle' ?>"></i>
    <span><?= htmlspecialchars($message) ?></span>
</div>
<?php endif; ?>

<!-- Baris Filter Kelas & Pencarian -->
<div class="z-panel" style="border-radius: 20px; margin-bottom: 20px;">
    <div style="padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <!-- Filter Dropdown Kelas -->
        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <label style="font-weight: 800; font-size: 0.82rem; color: var(--z-muted); text-transform: uppercase;">Filter Kelas:</label>
            <select id="selectKelas" class="z-field" style="width: auto; min-width: 200px; font-weight: 700;" onchange="filterPerKelas(this.value)">
                <option value="0">-- Semua Kelas Madrasah --</option>
                <?php foreach($kelas_list as $k): ?>
                <option value="<?= $k['id'] ?>" <?= $selected_kelas == $k['id'] ? 'selected' : '' ?>>
                    Kelas <?= htmlspecialchars($k['nama_kelas']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            
            <span class="pill pill-blue" style="font-size: 0.78rem;">
                <?= $sudah_buka ?> dari <?= $total_tampil ?> Siswa Sudah Buka Rekening
            </span>
        </div>

        <!-- Input Cari Siswa -->
        <div>
            <input type="text" id="searchInput" class="z-field" placeholder="Cari Nama atau NIS..." style="width: 260px;">
        </div>
    </div>
</div>

<!-- Panel Tabel Siswa -->
<div class="z-panel" style="border-radius: 24px;">
    <div class="z-panel-head" style="display: flex; justify-content: space-between; align-items: center;">
        <div class="z-panel-title">
            <i data-lucide="users"></i>
            <span>Daftar Siswa & Status Rekening</span>
        </div>
    </div>
    <div class="z-table-wrap">
        <table id="siswaTable">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Nama Siswa & NIS</th>
                    <th>Kelas</th>
                    <th>No. Rekening</th>
                    <th style="text-align: center;">Status Rekening</th>
                    <th style="text-align: right;">Saldo</th>
                    <th style="text-align: right;" width="20%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($siswa_list)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 45px;">
                        <div style="color: var(--z-muted); margin-bottom: 10px;"><i data-lucide="users" style="width:44px;height:44px;opacity:0.3;"></i></div>
                        <div style="font-weight: 800; color: var(--z-text); font-size: 1.05rem;">Data Siswa Tidak Ditemukan</div>
                        <div style="font-size: 0.85rem; color: var(--z-muted);">Silakan pilih kelas lain atau periksa data siswa madrasah</div>
                    </td>
                </tr>
                <?php else: $no = 1; foreach($siswa_list as $s): 
                    $punya_rekening = !empty($s['tabungan_id']);
                    $no_rek = $punya_rekening ? 'TBG-' . str_pad($s['siswa_id'], 5, '0', STR_PAD_LEFT) : '-';
                ?>
                <tr id="row-<?= $s['siswa_id'] ?>">
                    <td style="color: var(--z-muted); font-size: 0.84rem;"><?= $no++ ?></td>
                    <td>
                        <div style="font-weight: 800; color: var(--z-text);"><?= htmlspecialchars($s['nama']) ?></div>
                        <div style="font-size: 0.75rem; color: var(--z-muted);">NIS: <?= htmlspecialchars($s['nis'] ?: '-') ?></div>
                    </td>
                    <td>
                        <span class="pill pill-gray"><?= htmlspecialchars($s['nama_kelas'] ?? '-') ?></span>
                    </td>
                    <td class="col-norek" style="font-family: monospace; font-weight: 800; color: <?= $punya_rekening ? '#0ea5e9' : '#94a3b8' ?>;">
                        <?= $no_rek ?>
                    </td>
                    <td class="col-status" style="text-align: center;">
                        <?php if($punya_rekening): ?>
                            <span class="pill pill-green">
                                <i data-lucide="check-circle-2" style="width:12px;height:12px;margin-right:3px;"></i>Sudah Memiliki Rekening
                            </span>
                        <?php else: ?>
                            <span class="pill pill-gray" style="background:#f1f5f9; color:#64748b;">
                                <i data-lucide="circle" style="width:10px;height:10px;margin-right:3px;"></i>Belum Ada Rekening
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="col-saldo" style="text-align: right; font-weight: 800; color: <?= $punya_rekening ? '#10b981' : '#94a3b8' ?>; font-size: 0.95rem;">
                        <?= $punya_rekening ? 'Rp ' . number_format($s['saldo'], 0, ',', '.') : '-' ?>
                    </td>
                    <td class="col-aksi" style="text-align: right;">
                        <?php if($punya_rekening): ?>
                            <div style="display: inline-flex; gap: 6px;">
                                <a href="<?= base_url('admin/tabungan/kasir?siswa_id=' . $s['siswa_id']) ?>" class="btn btn-ghost btn-sm" title="Setor / Tarik Saldo">
                                    <i data-lucide="arrow-left-right" style="width:14px;height:14px;color:#0ea5e9;"></i> Transaksi
                                </a>
                                <?php if(floatval($s['saldo']) == 0): ?>
                                <button type="button" class="btn btn-danger btn-sm" title="Tutup Rekening" onclick="konfirmasiTutup(<?= $s['siswa_id'] ?>, '<?= htmlspecialchars(addslashes($s['nama']), ENT_QUOTES) ?>')">
                                    <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <!-- Tombol Buka Rekening Cepat (AJAX) -->
                            <button type="button" onclick="bukaRekening(<?= $s['siswa_id'] ?>, '<?= htmlspecialchars(addslashes($s['nama']), ENT_QUOTES) ?>', this)" class="btn btn-primary btn-sm" style="background: #10b981; border: none; font-weight: 800; padding: 6px 14px;">
                                <i data-lucide="plus" style="width:14px;height:14px;margin-right:4px;"></i> Buka Rekening
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Hidden Form Tutup Rekening -->
<form id="formTutupRekening" action="<?= base_url('admin/tabungan/siswa' . ($selected_kelas > 0 ? '?kelas_id=' . $selected_kelas : '')) ?>" method="POST" style="display:none;">
    <input type="hidden" name="action" value="tutup_rekening">
    <input type="hidden" name="siswa_id" id="tutup_siswa_id">
</form>

<script>
function filterPerKelas(kelasId) {
    if (kelasId > 0) {
        window.location.href = '<?= base_url("admin/tabungan/siswa") ?>?kelas_id=' + kelasId;
    } else {
        window.location.href = '<?= base_url("admin/tabungan/siswa") ?>';
    }
}

function konfirmasiTutup(siswaId, nama) {
    if (confirm("Apakah Anda yakin ingin menutup rekening tabungan milik " + nama + "?")) {
        document.getElementById('tutup_siswa_id').value = siswaId;
        document.getElementById('formTutupRekening').submit();
    }
}

function bukaRekening(siswaId, nama, btnEl) {
    var originalHtml = btnEl.innerHTML;
    btnEl.innerHTML = '<i data-lucide="loader-2" class="lucide-spin" style="width:14px;height:14px;margin-right:4px;"></i> Memproses...';
    btnEl.disabled = true;
    
    var formData = new FormData();
    formData.append('action', 'buka_rekening');
    formData.append('siswa_id', siswaId);
    formData.append('siswa_nama', nama);
    formData.append('setoran_awal', '0');
    formData.append('ajax', '1');

    fetch('<?= base_url('admin/tabungan/siswa') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Update row UI dynamically
            var tr = document.getElementById('row-' + siswaId);
            
            // Update Norek
            var colNorek = tr.querySelector('.col-norek');
            colNorek.innerHTML = data.no_rek;
            colNorek.style.color = '#0ea5e9';
            
            // Update Status
            var colStatus = tr.querySelector('.col-status');
            colStatus.innerHTML = '<span class="pill pill-green"><i data-lucide="check-circle-2" style="width:12px;height:12px;margin-right:3px;"></i>Sudah Memiliki Rekening</span>';
            
            // Update Saldo
            var colSaldo = tr.querySelector('.col-saldo');
            colSaldo.innerHTML = 'Rp 0';
            colSaldo.style.color = '#10b981';
            
            var safeNama = nama.replace(/'/g, "\\'");
            
            // Update Aksi
            var colAksi = tr.querySelector('.col-aksi');
            colAksi.innerHTML = `
                <div style="display: inline-flex; gap: 6px;">
                    <a href="${data.transaksi_url}" class="btn btn-ghost btn-sm" title="Setor / Tarik Saldo">
                        <i data-lucide="arrow-left-right" style="width:14px;height:14px;color:#0ea5e9;"></i> Transaksi
                    </a>
                    <button type="button" class="btn btn-danger btn-sm" title="Tutup Rekening" onclick="konfirmasiTutup(${siswaId}, '${safeNama}')">
                        <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                    </button>
                </div>
            `;
            
            lucide.createIcons(); // Re-initialize icons
        } else {
            alert(data.message || 'Terjadi kesalahan.');
            btnEl.innerHTML = originalHtml;
            btnEl.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan jaringan.');
        btnEl.innerHTML = originalHtml;
        btnEl.disabled = false;
    });
}

document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    
    var searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            var filter = this.value.toLowerCase();
            var rows = document.querySelectorAll('#siswaTable tbody tr');
            rows.forEach(function(row) {
                var text = row.textContent.toLowerCase();
                row.style.display = text.indexOf(filter) > -1 ? '' : 'none';
            });
        });
    }
});
</script>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
