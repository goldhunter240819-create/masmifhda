<!DOCTYPE html><html lang="id"><head>
<?php $page_title='Tagihan Siswa'; include __DIR__.'/_zamrud_head.php'; ?>
<style>
.student-card { border: 1px solid var(--z-border); border-radius: 10px; margin-bottom: 12px; overflow: hidden; }
.student-head { display:flex; align-items:center; gap:12px; padding: 12px 18px; background: #f8fafc; cursor: pointer; user-select:none; }
.student-head:hover { background: #f1f5f9; }
.student-bills { display: none; padding: 0 0 8px 0; }
.student-bills.open { display: block; }
.bill-row { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 100px 80px; gap: 10px; padding: 10px 18px; border-top: 1px solid var(--z-border); align-items: center; font-size: .84rem; }
.bill-row:hover { background: #fafafa; }
.bill-header { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 100px 80px; gap: 10px; padding: 8px 18px; background: #f0f4ff; font-size: .78rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .04em; border-top: 1px solid var(--z-border); }
.s-summary { display: flex; gap: 15px; margin-left: auto; align-items: center; }
.s-badge { padding: 3px 10px; border-radius: 20px; font-size: .75rem; font-weight: 600; }
.s-badge-red { background: #fee2e2; color: #dc2626; }
.s-badge-blue { background: #dbeafe; color: #2563eb; }
.s-badge-green { background: #dcfce7; color: #16a34a; }
.pill-blue { background: #dbeafe; color: #1d4ed8; }
</style>
</head><body>
<div class="z-wrap">
<?php include __DIR__.'/_zamrud_sidebar.php'; ?>
<main class="z-main">
<header class="z-header">
    <div class="z-header-left">
        <button class="z-hamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
        <span class="z-header-title">Tagihan Siswa</span><span class="z-badge">Admin</span>
    </div>
    <div class="z-header-user">
        <div class="z-user-info"><div class="name"><?php echo $adminNama; ?></div><div class="role">Master Administrator</div></div>
        <div class="z-avatar"><?php echo $adminInitial; ?></div>
    </div>
</header>
<div class="z-scroll">
<div class="z-page-head-row">
    <div><h1>Tagihan Siswa</h1><p>Rekap tagihan per siswa berdasarkan Jenis Tagihan Wajib</p></div>
</div>

<?php
// Hitung statistik
$totalSiswa = count($grouped);
$totalMenyicil = 0; $totalBelum = 0;
foreach ($grouped as $s) {
    foreach ($s['tagihan'] as $t) {
        if ($t['status'] === 'Menyicil') $totalMenyicil++;
        elseif ($t['status'] === 'Belum Bayar') $totalBelum++;
    }
}
$totalTagihan = $totalMenyicil + $totalBelum;
?>
<div class="z-stats" style="grid-template-columns:repeat(3,1fr);">
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Siswa Bertagihan</div><div class="z-stat-value"><?php echo $totalSiswa; ?></div></div><div class="z-stat-icon zi-blue"><i data-lucide="users"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Sedang Menyicil</div><div class="z-stat-value" style="color:#2563eb;"><?php echo $totalMenyicil; ?></div></div><div class="z-stat-icon zi-blue"><i data-lucide="loader"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Belum Bayar</div><div class="z-stat-value" style="color:var(--z-danger);"><?php echo $totalBelum; ?></div></div><div class="z-stat-icon zi-red"><i data-lucide="alert-circle"></i></div></div>
</div>

<div class="z-panel">
<div style="padding: 14px 16px; border-bottom: 1px solid var(--z-border);">
    <div class="z-panel-title" style="margin:0 0 12px 0;"><i data-lucide="file-text"></i>Daftar Tagihan Per Siswa</div>
    <form id="filterForm" action="" method="GET">
        <!-- Baris 1: Semua kontrol dalam satu baris -->
        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:12px;">
            <!-- Tampilkan -->
            <div style="display:flex; align-items:center; gap:5px; font-size:0.82rem; color:#64748b; font-weight:600;white-space:nowrap;">
                Tampilkan
                <select name="per_page" class="z-field" style="width:68px; padding:4px 8px; margin:0; font-weight:700;" onchange="performAjaxFilter()">
                    <option value="10" <?php echo (($_GET['per_page'] ?? 10) == 10) ? 'selected' : ''; ?>>10</option>
                    <option value="25" <?php echo (($_GET['per_page'] ?? 10) == 25) ? 'selected' : ''; ?>>25</option>
                    <option value="50" <?php echo (($_GET['per_page'] ?? 10) == 50) ? 'selected' : ''; ?>>50</option>
                    <option value="100" <?php echo (($_GET['per_page'] ?? 10) == 100) ? 'selected' : ''; ?>>100</option>
                    <option value="999" <?php echo (($_GET['per_page'] ?? 10) == 999) ? 'selected' : ''; ?>>Semua</option>
                </select>
            </div>
            <!-- Filter Kelas -->
            <select name="kelas" class="z-field" style="min-width:130px; padding:4px 8px; margin:0; flex:1;" onchange="performAjaxFilter()">
                <option value="">- Semua Kelas -</option>
                <?php foreach($kelasList as $k): ?>
                <option value="<?php echo $k['nama_kelas']; ?>" <?php echo $filter_kelas==$k['nama_kelas']?'selected':''; ?>><?php echo $k['nama_kelas']; ?></option>
                <?php endforeach; ?>
            </select>
            <!-- Filter Status -->
            <select name="status" class="z-field" style="min-width:125px; padding:4px 8px; margin:0; flex:1;" onchange="performAjaxFilter()">
                <option value="">- Semua Status -</option>
                <option value="Menyicil" <?php echo $filter_status=='Menyicil'?'selected':''; ?>>Menyicil</option>
                <option value="Belum Bayar" <?php echo $filter_status=='Belum Bayar'?'selected':''; ?>>Belum Bayar</option>
            </select>
            <input type="hidden" name="page" value="1">
            <?php if (!empty($_GET['cari'])): ?>
            <input type="hidden" name="cari" value="<?php echo htmlspecialchars($_GET['cari']); ?>">
            <?php endif; ?>
        </div>
        <!-- Baris 2: Search Bar -->
        <div style="position:relative;">
            <i data-lucide="search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#94a3b8;pointer-events:none;"></i>
            <input
                type="text"
                id="searchSiswa"
                name="cari"
                placeholder="Cari nama siswa atau NIS... (cari di semua halaman)"
                value="<?php echo htmlspecialchars($_GET['cari'] ?? ''); ?>"
                class="z-field"
                style="width:100%;padding:7px 32px 7px 32px;margin:0;font-size:0.85rem;box-sizing:border-box;"
                oninput="scheduleSearch()"
                onkeydown="if(event.key==='Enter'){event.preventDefault();performAjaxFilter();}"
                autocomplete="off"
            >
            <span id="searchClearBtn" onclick="clearSearch()" style="display:<?php echo !empty($_GET['cari']) ? 'block' : 'none'; ?>;position:absolute;right:10px;top:50%;transform:translateY(-50%);cursor:pointer;color:#94a3b8;font-size:1.2rem;line-height:1;" title="Hapus">&times;</span>
        </div>
    </form>
</div>
<?php
$filter_cari = isset($_GET['cari']) ? trim($_GET['cari']) : '';
if ($filter_cari !== '') {
    $grouped = array_values(array_filter($grouped, function($s) use ($filter_cari) {
        return stripos($s['nama'], $filter_cari) !== false || stripos($s['nis'], $filter_cari) !== false;
    }));
}
$per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 10;
if ($per_page <= 0) $per_page = 10;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$total_grouped = count($grouped);
$total_pages = ($per_page >= 999) ? 1 : max(1, ceil($total_grouped / $per_page));
if ($current_page > $total_pages) $current_page = $total_pages;
$offset = ($per_page >= 999) ? 0 : ($current_page - 1) * $per_page;
$grouped_page = ($per_page >= 999) ? $grouped : array_slice($grouped, $offset, $per_page);
?>
    <div id="tagihan-list-container" style="padding: 15px; transition: opacity 0.2s;">
        <?php if(empty($grouped)): ?>
            <p style="text-align:center; color:var(--z-muted); padding:30px;">Tidak ada data tagihan.</p>
        <?php else: ?>
        <?php foreach($grouped_page as $idx => $s):
            $realIdx = $offset + $idx;
            $sLunas = 0; $sMenyicil = 0; $sBelum = 0; $sTotalSisa = 0;
            foreach($s['tagihan'] as $t) {
                if ($t['status'] === 'Lunas') $sLunas++;
                elseif ($t['status'] === 'Menyicil') { $sMenyicil++; $sTotalSisa += $t['sisa']; }
                else { $sBelum++; $sTotalSisa += $t['sisa']; }
            }
        ?>
        <div class="student-card">
            <!-- Header Siswa -->
            <div class="student-head" onclick="toggleCard(<?php echo $realIdx; ?>)">
                <div class="z-uc-av" style="width:38px;height:38px;border-radius:50%;background:var(--z-primary);color:white;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;flex-shrink:0;">
                    <?php echo strtoupper(substr($s['nama'],0,2)); ?>
                </div>
                <div>
                    <div style="font-weight:700; font-size:.9rem;"><?php echo $s['nama']; ?></div>
                    <div style="font-size:.78rem; color:var(--z-muted);">NIS: <?php echo $s['nis']; ?> &bull; <?php echo $s['kelas']; ?></div>
                </div>
                <div class="s-summary">
                    <?php if($sLunas > 0): ?><span class="s-badge s-badge-green"><?php echo $sLunas; ?> Lunas</span><?php endif; ?>
                    <?php if($sMenyicil > 0): ?><span class="s-badge s-badge-blue"><?php echo $sMenyicil; ?> Menyicil</span><?php endif; ?>
                    <?php if($sBelum > 0): ?><span class="s-badge s-badge-red"><?php echo $sBelum; ?> Belum Bayar</span><?php endif; ?>
                    <?php if($sTotalSisa > 0): ?>
                    <span style="font-size:.82rem; color:var(--z-danger); font-weight:700;">Sisa: Rp <?php echo number_format($sTotalSisa,0,',','.'); ?></span>
                    <?php endif; ?>
                    <a href="<?php echo base_url('admin/finance/cetak-tagihan-siswa/' . $s['siswa_id']); ?>" target="_blank" onclick="event.stopPropagation()" class="btn btn-outline btn-sm" style="margin-left:8px;">
                        <i data-lucide="printer" style="width:13px;height:13px;"></i> Cetak
                    </a>
                    <i data-lucide="chevron-down" id="chevron-<?php echo $realIdx; ?>" style="width:16px;height:16px;color:var(--z-muted);transition:transform .2s;"></i>
                </div>
            </div>

            <!-- Daftar Tagihan Siswa -->
            <div class="student-bills" id="bills-<?php echo $realIdx; ?>">
                <div class="bill-header" style="grid-template-columns: 2fr 1fr 1fr 1fr 100px 50px;">
                    <div>Jenis Tagihan</div>
                    <div>Nominal</div>
                    <div>Sudah Dibayar</div>
                    <div>Sisa</div>
                    <div>Status</div>
                    <div>Aksi</div>
                </div>
                <?php foreach($s['tagihan'] as $t): ?>
                <div class="bill-row" style="grid-template-columns: 2fr 1fr 1fr 1fr 100px 50px; align-items:center;">
                    <div style="font-weight:600;"><?php echo $t['jenis']; ?>
                        <div style="font-size:.75rem; color:var(--z-muted);">JT: <?php echo date('d/m/Y', strtotime($t['jatuh_tempo'])); ?></div>
                    </div>
                    <div style="color:var(--z-muted);">Rp <?php echo number_format($t['jumlah'],0,',','.'); ?></div>
                    <div style="color:#16a34a;"><?php echo $t['terbayar'] > 0 ? 'Rp '.number_format($t['terbayar'],0,',','.') : '-'; ?></div>
                    <div>
                        <?php if($t['status'] === 'Lunas'): ?>
                            <span style="color:#16a34a; font-weight:700; font-size:.85rem;">LUNAS</span>
                        <?php else: ?>
                            <strong style="color:var(--z-danger);">Rp <?php echo number_format($t['sisa'],0,',','.'); ?></strong>
                        <?php endif; ?>
                    </div>
                    <div><span class="pill <?php echo $t['status']==='Lunas'?'pill-green':($t['status']==='Menyicil'?'pill-blue':'pill-amber'); ?>"><?php echo $t['status']; ?></span></div>
                    <div style="display:flex; gap:5px;">
                        <?php if($t['status'] !== 'Lunas'): ?>
                        <button class="btn btn-primary btn-sm" onclick="openBayarModal(<?php echo $t['id']; ?>, '<?php echo htmlspecialchars($t['jenis'], ENT_QUOTES); ?>', <?php echo $t['sisa']; ?>)" style="padding:4px 8px;" title="Bayar Tagihan"><i data-lucide="dollar-sign" style="width:14px;height:14px;"></i></button>
                        <?php endif; ?>
                        <?php if(isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 99])): ?>
                        <button class="btn btn-outline btn-sm" onclick="openEditModal(<?php echo $t['id']; ?>, '<?php echo htmlspecialchars($t['jenis'], ENT_QUOTES); ?>', <?php echo $t['jumlah']; ?>, '<?php echo $t['jatuh_tempo']; ?>')" style="padding:4px 8px;" title="Edit Tagihan"><i data-lucide="edit-3" style="width:14px;height:14px;"></i></button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- PAGINATION BAR -->
        <?php if($total_pages > 1 || $per_page < 999): ?>
        <div style="display:flex; align-items:center; justify-content:space-between; margin-top:16px; padding-top:12px; border-top:1px solid #f1f5f9; flex-wrap:wrap; gap:10px;">
            <div style="font-size:0.82rem; color:#64748b; font-weight:600;">
                Menampilkan <?php echo min($offset+1, $total_grouped); ?>â€“<?php echo min($offset+$per_page, $total_grouped); ?> dari <b><?php echo $total_grouped; ?></b> siswa
            </div>
            <div style="display:flex; gap:6px; align-items:center;">
                <?php
                $base_params = http_build_query(array_filter([
                    'kelas'    => $filter_kelas,
                    'status'   => $filter_status,
                    'per_page' => ($per_page < 999) ? $per_page : 999,
                ]));
                ?>
                <?php if($current_page > 1): ?>
                <a href="?<?php echo $base_params; ?>&page=1" class="btn btn-outline btn-sm"><i data-lucide="chevrons-left" style="width:13px;height:13px;"></i></a>
                <a href="?<?php echo $base_params; ?>&page=<?php echo $current_page - 1; ?>" class="btn btn-outline btn-sm"><i data-lucide="chevron-left" style="width:13px;height:13px;"></i></a>
                <?php endif; ?>

                <?php
                $window = 2;
                $start = max(1, $current_page - $window);
                $end   = min($total_pages, $current_page + $window);
                if ($start > 1) echo '<span style="color:#94a3b8;padding:0 4px;">...</span>';
                for ($p = $start; $p <= $end; $p++):
                ?>
                <a href="?<?php echo $base_params; ?>&page=<?php echo $p; ?>" class="btn btn-sm <?php echo $p == $current_page ? 'btn-primary' : 'btn-outline'; ?>"><?php echo $p; ?></a>
                <?php endfor;
                if ($end < $total_pages) echo '<span style="color:#94a3b8;padding:0 4px;">...</span>';
                ?>

                <?php if($current_page < $total_pages): ?>
                <a href="?<?php echo $base_params; ?>&page=<?php echo $current_page + 1; ?>" class="btn btn-outline btn-sm"><i data-lucide="chevron-right" style="width:13px;height:13px;"></i></a>
                <a href="?<?php echo $base_params; ?>&page=<?php echo $total_pages; ?>" class="btn btn-outline btn-sm"><i data-lucide="chevrons-right" style="width:13px;height:13px;"></i></a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php endif; ?>
    </div>
</div>

<!-- Modal Edit Tagihan -->
<div id="modal-edit" class="z-modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;z-index:9999;">
    <div class="z-modal-content" style="background:#fff;border-radius:12px;padding:24px;width:100%;max-width:400px;box-shadow:0 10px 25px rgba(0,0,0,0.1);">
        <h2 style="margin:0 0 16px 0;font-size:1.2rem;display:flex;align-items:center;gap:10px;"><i data-lucide="edit-3" style="color:#0ea5e9;"></i> Edit Tagihan Manual</h2>
        <form action="<?php echo base_url('admin/finance/tagihan/update'); ?>" method="POST">
            <input type="hidden" name="tagihan_id" id="edit_tagihan_id">
            <div class="z-form-group">
                <label>Nama/Jenis Tagihan</label>
                <input type="text" name="nama_tagihan" id="edit_nama_tagihan" class="z-input" required>
            </div>
            <div class="z-form-group">
                <label>Nominal Tagihan (Rp)</label>
                <input type="number" name="jumlah_tagihan" id="edit_jumlah_tagihan" class="z-input" required>
            </div>
            <div class="z-form-group">
                <label>Jatuh Tempo (Opsional)</label>
                <input type="date" name="jatuh_tempo" id="edit_jatuh_tempo" class="z-input">
            </div>
            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-edit').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Bayar Tagihan -->
<div id="modal-bayar" class="z-modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;z-index:9999;">
    <div class="z-modal-content" style="background:#fff;border-radius:12px;padding:24px;width:100%;max-width:400px;box-shadow:0 10px 25px rgba(0,0,0,0.1);">
        <h2 style="margin:0 0 16px 0;font-size:1.2rem;display:flex;align-items:center;gap:10px;"><i data-lucide="dollar-sign" style="color:#0ea5e9;"></i> Bayar Tagihan</h2>
        <form action="<?php echo base_url('admin/finance/tagihan/bayar'); ?>" method="POST">
            <input type="hidden" name="tagihan_id" id="bayar_tagihan_id">
            <div class="z-form-group">
                <label>Nama/Jenis Tagihan</label>
                <input type="text" id="bayar_nama_tagihan" class="z-input" readonly style="background:#f1f5f9;">
            </div>
            <div class="z-form-group">
                <label>Nominal Pembayaran (Rp)</label>
                <input type="number" name="jumlah_bayar" id="bayar_jumlah_bayar" class="z-input" required>
                <div style="font-size:0.8rem; color:var(--z-muted); margin-top:5px;">Sisa Tagihan: <strong id="bayar_sisa_text" style="color:var(--z-danger);"></strong></div>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-bayar').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary">Bayar Sekarang</button>
            </div>
        </form>
    </div>
</div>

</div></main></div>
<script>
function openEditModal(id, nama, jumlah, jt) {
    document.getElementById('edit_tagihan_id').value = id;
    document.getElementById('edit_nama_tagihan').value = nama;
    document.getElementById('edit_jumlah_tagihan').value = jumlah;
    document.getElementById('edit_jatuh_tempo').value = jt;
    document.getElementById('modal-edit').style.display = 'flex';
}
function openBayarModal(id, nama, sisa) {
    document.getElementById('bayar_tagihan_id').value = id;
    document.getElementById('bayar_nama_tagihan').value = nama;
    document.getElementById('bayar_jumlah_bayar').value = sisa;
    document.getElementById('bayar_jumlah_bayar').max = sisa;
    document.getElementById('bayar_sisa_text').innerText = 'Rp ' + sisa.toLocaleString('id-ID');
    document.getElementById('modal-bayar').style.display = 'flex';
}
function zToggleSidebar(){document.getElementById('zSidebar').classList.toggle('open');document.getElementById('zOverlay').classList.toggle('open');}
function toggleCard(idx) {
    var bills = document.getElementById('bills-' + idx);
    var chevron = document.getElementById('chevron-' + idx);
    bills.classList.toggle('open');
    chevron.style.transform = bills.classList.contains('open') ? 'rotate(180deg)' : '';
    lucide.createIcons();
}
// Auto-expand if there are unpaid/cicil
document.addEventListener('DOMContentLoaded', function() {
    <?php foreach($grouped_page as $idx => $s): 
        $realIdx = $offset + $idx;
        $hasUnpaid = false;
        foreach($s['tagihan'] as $t) { if($t['status'] !== 'Lunas') { $hasUnpaid = true; break; } }
        if($hasUnpaid): ?>
    toggleCard(<?php echo $realIdx; ?>);
    <?php endif; endforeach; ?>
    lucide.createIcons();

    // Tampilkan tombol clear jika ada value dari GET
    var sv = document.getElementById('searchSiswa');
    var cb = document.getElementById('searchClearBtn');
    if (sv && sv.value.trim() !== '') cb.style.display = 'block';
});

// â”€â”€ AJAX Search & Filter â”€â”€
var _searchTimer;
function scheduleSearch() {
    var val = document.getElementById('searchSiswa').value;
    document.getElementById('searchClearBtn').style.display = val.trim() ? 'block' : 'none';
    clearTimeout(_searchTimer);
    _searchTimer = setTimeout(function() {
        performAjaxFilter();
    }, 400); // tunggu 400ms setelah berhenti mengetik
}

function clearSearch() {
    var sv = document.getElementById('searchSiswa');
    sv.value = '';
    document.getElementById('searchClearBtn').style.display = 'none';
    performAjaxFilter();
}

function performAjaxFilter(customUrl = null) {
    var url = customUrl;
    if (!url) {
        var form = document.getElementById('filterForm');
        var formData = new FormData(form);
        var searchVal = document.getElementById('searchSiswa').value;
        formData.set('cari', searchVal);
        formData.set('page', '1'); // reset ke page 1 tiap filter
        var params = new URLSearchParams(formData);
        url = '?' + params.toString();
    }
    
    // Update URL bar without reload
    window.history.pushState({}, '', url);
    
    var container = document.getElementById('tagihan-list-container');
    container.style.opacity = '0.5';
    
    fetch(url)
        .then(res => res.text())
        .then(html => {
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');
            var newContainer = doc.getElementById('tagihan-list-container');
            if (newContainer) {
                container.innerHTML = newContainer.innerHTML;
                lucide.createIcons();
                
                // Re-evaluate auto-expand for new content
                var scriptContent = "";
                var scripts = doc.querySelectorAll('script');
                scripts.forEach(s => { if(s.textContent.includes('toggleCard(')) scriptContent += s.textContent; });
                if (scriptContent) {
                    try { eval(scriptContent); } catch(e){}
                }
            }
            container.style.opacity = '1';
        })
        .catch(err => {
            console.error(err);
            container.style.opacity = '1';
        });
}

// Intercept pagination clicks to use AJAX
document.addEventListener('click', function(e) {
    var link = e.target.closest('#tagihan-list-container a.btn');
    if (link && link.href) {
        // Abaikan tombol cetak
        if (!link.href.includes('cetak-tagihan-siswa')) {
            e.preventDefault();
            performAjaxFilter(link.href);
        }
    }
});
</script>
</body></html>

