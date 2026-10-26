<!DOCTYPE html><html lang="id"><head>
<?php $page_title='Pembayaran Siswa'; include __DIR__.'/_zamrud_head.php'; ?>
</head><body>
<div class="z-wrap">
<?php include __DIR__.'/_zamrud_sidebar.php'; ?>
<main class="z-main">
<header class="z-header">
    <div class="z-header-left">
        <button class="z-hamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
        <span class="z-header-title">Pembayaran Siswa</span><span class="z-badge">Admin</span>
    </div>
    <div class="z-header-user">
        <div class="z-user-info"><div class="name"><?php echo $adminNama; ?></div><div class="role">Master Administrator</div></div>
        <div class="z-avatar"><?php echo $adminInitial; ?></div>
    </div>
</header>
<div class="z-scroll">
<div class="z-page-head-row">
    <div><h1>Pembayaran Siswa</h1><p>Pencatatan uang SPP / tagihan dari siswa</p></div>
    <button class="btn btn-primary" onclick="document.getElementById('mOv').classList.add('open')"><i data-lucide="plus" style="width:14px;height:14px;"></i>Tambah Pembayaran</button>
</div>
<?php
$total_dana = 0;
$lunas_count = 0;
foreach($pembayaran as $p){ 
    $total_dana += $p['jumlah']; 
    if($p['status']==='Lunas') $lunas_count++;
}
?>
<div class="z-stats" style="grid-template-columns:repeat(2,1fr);">
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Siswa Telah Bayar</div><div class="z-stat-value"><?php echo $lunas_count; ?></div></div><div class="z-stat-icon zi-blue"><i data-lucide="users"></i></div></div>
    <div class="z-stat"><div class="z-stat-left"><div class="z-stat-label">Total Dana Terkumpul</div><div class="z-stat-value" style="color:#16a34a;">Rp <?php echo number_format($total_dana,0,',','.'); ?></div></div><div class="z-stat-icon zi-green"><i data-lucide="wallet"></i></div></div>
</div>
<div class="z-panel">
    <div class="z-panel-head" style="justify-content: space-between;">
        <div class="z-panel-title"><i data-lucide="list"></i>Daftar Pembayaran Siswa</div>
        <form action="" method="GET" style="display:flex; align-items:center; gap:10px;">
            <label class="z-label" style="margin:0; font-size:0.85rem;"><i data-lucide="filter" style="width:14px; height:14px;"></i> Tampilkan:</label>
            <select id="entryLimit" class="z-field" style="min-width:80px; padding:4px 8px; margin:0;" onchange="changePage(1)">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <label class="z-label" style="margin:0; font-size:0.85rem;">Kelas:</label>
            <select name="kelas_id" class="z-field" style="min-width:150px; padding:4px 8px; margin:0;" onchange="this.form.submit()">
                <option value="0">- Semua Kelas -</option>
                <?php foreach($kelasList as $k): ?>
                <option value="<?php echo $k['id']; ?>" <?php echo $kelas_id==$k['id']?'selected':''; ?>><?php echo $k['nama_kelas']; ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    <div class="z-table-wrap"><table id="tbl">
        <thead><tr><th>Siswa</th><th>Kelas</th><th>Tanggal Bayar</th><th>Periode</th><th>Status</th><th>Jumlah</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php foreach($pembayaran as $p): ?>
            <tr>
                <td><div class="z-uc"><div class="z-uc-av"><?php echo strtoupper(substr($p['nama'],0,2)); ?></div><div><div class="z-uc-name"><?php echo $p['nama']; ?></div><div class="z-uc-sub">NIS: <?php echo $p['nis']; ?></div></div></div></td>
                <td style="font-size:.82rem;"><?php echo $p['kelas']; ?></td>
                <td style="font-size:.82rem; color:var(--z-muted);"><?php echo date('d/m/Y',strtotime($p['tanggal'])); ?></td>
                <td style="font-size:.82rem;"><span style="font-weight:600;"><?php echo $p['jenis']; ?></span> <br><span style="color:var(--z-muted);"><?php echo $p['periode']; ?></span></td>
                <td><span class="pill <?php echo $p['status']==='Lunas' ? 'pill-green' : ($p['status']==='Menyicil' ? 'pill-blue' : 'pill-amber'); ?>"><?php echo $p['status']; ?></span></td>
                <td class="z-amount">Rp <?php echo number_format($p['jumlah'],0,',','.'); ?></td>
                <td>
                    <a href="<?php echo base_url('admin/finance/kwitansi/' . $p['id']); ?>" target="_blank" class="btn btn-ghost btn-sm">Cetak Kwitansi</a>
                    <?php if (isset($can_delete) && $can_delete): ?>
                    <button onclick="hapusPembayaran(<?php echo $p['id']; ?>)" class="btn btn-ghost btn-sm" style="color: #ef4444;"><i data-lucide="trash-2" style="width:14px;height:14px;"></i> Hapus</button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($pembayaran)): ?>
            <tr><td colspan="7" style="text-align:center;color:var(--z-muted);padding:20px;">
                <?php echo $kelas_id==0 ? 'Silakan pilih kelas terlebih dahulu untuk melihat daftar siswa.' : 'Belum ada data siswa di kelas ini.'; ?>
            </td></tr>
            <?php endif; ?>
        </tbody>
    </table></div>
    <div class="z-panel-foot" style="display:flex; justify-content:space-between; align-items:center; padding: 10px 20px;">
        <div id="pageInfo" style="font-size: 0.85rem; color: var(--z-muted);">Menampilkan 1-10 dari <?php echo count($pembayaran); ?> data</div>
        <div style="display:flex; gap:5px;">
            <button class="btn btn-ghost btn-sm" onclick="changePage(currentPage-1)"><i data-lucide="chevron-left" style="width:14px;"></i></button>
            <div id="pageList" style="display:flex; gap:5px;"></div>
            <button class="btn btn-ghost btn-sm" onclick="changePage(currentPage+1)"><i data-lucide="chevron-right" style="width:14px;"></i></button>
        </div>
    </div>
</div>
</div></main></div>

<div class="z-modal-ov" id="mOv" onclick="if(event.target===this)this.classList.remove('open')">
<div class="z-modal">
    <div class="z-modal-head">
        <div class="z-modal-title"><i data-lucide="plus-circle"></i>Catat Pembayaran Siswa</div>
        <button type="button" class="z-modal-close" onclick="document.getElementById('mOv').classList.remove('open')"><i data-lucide="x"></i></button>
    </div>
    <form action="<?php echo base_url('admin/finance/komite/pembayaran-siswa/save'); ?>" method="POST">
        <div class="z-modal-body">
        <input type="hidden" name="kelas_id" value="<?php echo $kelas_id; ?>">
        <input type="hidden" name="jenis_periode_value" id="jenisPeriodeVal" value="">
        <div class="z-grid2">
            <div class="z-form-group">
                <label class="z-label">1. Filter Kelas</label>
                <select class="z-field" id="modalFilterKelas" onchange="filterModalStudents()">
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach($kelasList as $k): ?>
                    <option value="<?php echo $k['id']; ?>" <?php echo $kelas_id==$k['id']?'selected':''; ?>><?php echo $k['nama_kelas']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="z-form-group">
                <label class="z-label">2. Pilih Siswa</label>
                <select class="z-field" name="siswa_id" id="siswaSelect" onchange="filterTagihan()" required>
                    <option value="">-- Pilih Siswa --</option>
                    <?php 
                    // Populate based on current filter or show all if needed
                    foreach($siswas as $s): 
                    ?>
                    <option value="<?php echo $s['id']; ?>" data-kelas="<?php echo $s['kelas_id']; ?>"><?php echo $s['nama'] . ' (NIS: '.$s['nis'].')'; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="z-form-group">
            <label class="z-label">Jenis Pembayaran</label>
            <select class="z-field" id="jenisBayar" name="jenis_pembayaran" onchange="togglePeriode()" required>
                <option value="">-- Pilih Siswa Terlebih Dahulu --</option>
            </select>
        </div>
        <div class="z-grid2">
            <div class="z-form-group" id="wrapBulanan">
                <label class="z-label">Bulan Pembayaran</label>
                <input class="z-field" type="month" name="periode_bulanan" value="<?php echo date('Y-m'); ?>">
            </div>
            <div class="z-form-group" id="wrapTahunan" style="display:none;">
                <label class="z-label">Tahun Ajaran</label>
                <select class="z-field" name="periode_tahunan">
                    <option value="<?php echo date('Y').'/'.(date('Y')+1); ?>"><?php echo date('Y').'/'.(date('Y')+1); ?></option>
                    <option value="<?php echo (date('Y')-1).'/'.date('Y'); ?>"><?php echo (date('Y')-1).'/'.date('Y'); ?></option>
                </select>
            </div>
            <div class="z-form-group"><label class="z-label">Jumlah Bayar (Rp)</label><input class="z-field" type="number" name="jumlah" id="inputJumlah" placeholder="150000" required></div>
        </div>
        <div class="z-form-group"><label class="z-label">Tanggal Bayar</label><input class="z-field" type="date" name="tanggal_bayar" required value="<?php echo date('Y-m-d'); ?>"></div>
        </div>
        <div class="z-modal-foot"><button type="button" class="btn btn-ghost" onclick="document.getElementById('mOv').classList.remove('open')">Batal</button><button type="submit" class="btn btn-primary"><i data-lucide="save" style="width:13px;height:13px;"></i>Simpan</button></div>
    </form>
</div></div>
<script>
function hapusPembayaran(id) {
    if (confirm('Apakah Anda yakin ingin menghapus pembayaran ini?\nStatus tagihan akan dikalkulasi ulang secara otomatis.')) {
        var formData = new FormData();
        formData.append('id', id);
        fetch('<?php echo base_url("admin/finance/komite/pembayaran-siswa/delete"); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Gagal: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan pada server.');
        });
    }
}
var tagihanList = <?php echo json_encode($jenisTagihan); ?>;
var allStudents = <?php echo json_encode($allSiswaList); ?>;
var tagihanData  = <?php echo json_encode(isset($tagihanData) ? $tagihanData : []); ?>; // {siswa_id, nama_tagihan, jumlah_tagihan, terbayar}
var currentPage = 1;

// -- Alert banner dari server --
<?php if(isset($_GET['msg'])): ?>
(function(){
    var msg = '<?php echo $_GET['msg']; ?>';
    var banner = document.createElement('div');
    if(msg === 'overpaid'){
        var sisa = '<?php echo isset($_GET['sisa']) ? number_format(intval($_GET['sisa']),0,",",".") : "0"; ?>';
        banner.innerHTML = 'â›” <strong>Pembayaran ditolak!</strong> Jumlah yang dimasukkan melebihi sisa tagihan. Sisa yang harus dibayar: <strong>Rp '+sisa+'</strong>';
        banner.style = 'background:#fef2f2;color:#dc2626;border:1px solid #fca5a5;padding:12px 18px;border-radius:10px;margin-bottom:16px;font-size:13px;';
    } else if(msg === 'success'){
        banner.innerHTML = 'âœ… <strong>Pembayaran berhasil disimpan!</strong>';
        banner.style = 'background:#f0fdf4;color:#15803d;border:1px solid #86efac;padding:12px 18px;border-radius:10px;margin-bottom:16px;font-size:13px;';
    }
    if(banner.innerHTML){
        var main = document.querySelector('.z-content') || document.body;
        main.insertBefore(banner, main.firstChild);
        setTimeout(function(){banner.remove();}, 6000);
    }
})();
<?php endif; ?>

function changePage(p) {
    var limit = parseInt(document.getElementById('entryLimit').value);
    var rows = document.querySelectorAll('#tbl tbody tr:not(.empty-row)');
    var total = rows.length;
    var totalPages = Math.ceil(total / limit);
    
    if (p < 1) p = 1;
    if (p > totalPages) p = totalPages;
    if (totalPages === 0) p = 1;
    currentPage = p;
    
    var start = (p - 1) * limit;
    var end = start + limit;
    
    rows.forEach(function(r, i) {
        r.style.display = (i >= start && i < end) ? '' : 'none';
    });
    
    document.getElementById('pageInfo').innerHTML = 'Menampilkan ' + (total === 0 ? 0 : start + 1) + '-' + Math.min(end, total) + ' dari ' + total + ' data';
    
    // Simple page numbers
    var pgList = document.getElementById('pageList');
    pgList.innerHTML = '';
    for (var i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= p - 1 && i <= p + 1)) {
            var btn = document.createElement('button');
            btn.className = 'btn ' + (i === p ? 'btn-primary' : 'btn-ghost') + ' btn-sm';
            btn.innerHTML = i;
            btn.onclick = (function(page) { return function() { changePage(page); }; })(i);
            pgList.appendChild(btn);
        } else if (i === p - 2 || i === p + 2) {
            var dot = document.createElement('span');
            dot.innerHTML = '...';
            dot.style.padding = '0 5px';
            pgList.appendChild(dot);
        }
    }
    lucide.createIcons();
}

// Init pagination
setTimeout(function() { changePage(1); }, 100);

function filterModalStudents() {
    var kid = document.getElementById('modalFilterKelas').value;
    var sel = document.getElementById('siswaSelect');
    sel.innerHTML = '<option value="">-- Pilih Siswa --</option>';
    
    allStudents.forEach(function(s) {
        if(!kid || s.kelas_id == kid) {
            var opt = document.createElement('option');
            opt.value = s.id;
            opt.text = s.nama + ' (NIS: ' + s.nis + ')';
            sel.appendChild(opt);
        }
    });
    filterTagihan(); // Reset tagihan dropdown
}

function filterTagihan() {
    var siswaId = document.getElementById('siswaSelect').value;
    var kid = document.getElementById('modalFilterKelas').value;
    var sel = document.getElementById('jenisBayar');
    sel.innerHTML = '<option value="">-- Pilih Jenis Tagihan --</option>';
    
    if(!siswaId) return;
    
    // 1. Ambil semua yang relevan
    var applicable = tagihanList.filter(function(t) {
        var isTargetSiswa = false;
        if(t.target_tipe === 'Siswa' && t.target_siswa_id) {
            var ids = t.target_siswa_id.split(',');
            if(ids.includes(String(siswaId))) isTargetSiswa = true;
        }
        return (t.target_tipe === 'Semua' || (t.target_tipe === 'Kelas' && t.target_kelas_id == kid) || isTargetSiswa);
    });

    // 2. Kelompokkan berdasarkan Nama dan ambil yang paling spesifik (Siswa > Kelas > Semua)
    var filtered = {};
    applicable.forEach(function(t) {
        var existing = filtered[t.nama_tagihan];
        if (!existing) {
            filtered[t.nama_tagihan] = t;
        } else {
            // Prioritas: Siswa (3) > Kelas (2) > Semua (1)
            var p = { 'Siswa': 3, 'Kelas': 2, 'Semua': 1 };
            if (p[t.target_tipe] > p[existing.target_tipe]) {
                filtered[t.nama_tagihan] = t;
            }
        }
    });

    // 3. Tampilkan hasil saringan
    Object.values(filtered).forEach(function(t) {
        var opt = document.createElement('option');
        opt.value = t.nama_tagihan;
        opt.setAttribute('data-periode', t.jenis_periode.toLowerCase());
        opt.setAttribute('data-nominal', t.nominal);
        var reqStr = t.sifat === 'Wajib' ? ' (Wajib)' : ' (Opsional)';
        opt.innerHTML = t.nama_tagihan + reqStr + ' - Rp ' + Number(t.nominal).toLocaleString('id-ID');
        sel.appendChild(opt);
    });
    
    togglePeriode();
}

function zToggleSidebar(){document.getElementById('zSidebar').classList.toggle('open');document.getElementById('zOverlay').classList.toggle('open');}
function togglePeriode() {
    var sel = document.getElementById('jenisBayar');
    var opt = sel.options[sel.selectedIndex];
    var j = opt.getAttribute('data-periode');
    var nom = opt.getAttribute('data-nominal');
    
    document.getElementById('jenisPeriodeVal').value = j;
    
    if (j === 'tahunan') {
        document.getElementById('wrapBulanan').style.display = 'none';
        document.getElementById('wrapTahunan').style.display = 'block';
    } else {
        document.getElementById('wrapBulanan').style.display = 'block';
        document.getElementById('wrapTahunan').style.display = 'none';
    }
    
    // Hitung sisa tagihan untuk validasi max input
    var siswaId = document.getElementById('siswaSelect').value;
    var namaBayar = opt.value;
    var sisamax = null;
    if(siswaId && namaBayar) {
        var found = tagihanData.find(function(d){ return d.siswa_id == siswaId && d.nama_tagihan === namaBayar; });
        if(found) {
            var terbayar = parseFloat(found.terbayar) || 0;
            sisamax = parseFloat(found.jumlah_tagihan) - terbayar;
        }
    }
    
    var inp = document.getElementById('inputJumlah');
    if(sisamax !== null && sisamax >= 0) {
        inp.max = sisamax;
        inp.value = sisamax > 0 ? sisamax : 0;
        inp.title = 'Maksimal pembayaran: Rp ' + Number(sisamax).toLocaleString('id-ID');
        // Warn label
        var label = document.getElementById('labelSisa');
        if(!label) {
            label = document.createElement('div');
            label.id = 'labelSisa';
            label.style = 'font-size:11px;color:#2563eb;margin-top:4px;font-weight:600;';
            inp.parentNode.appendChild(label);
        }
        if(sisamax <= 0) {
            label.innerHTML = 'âœ… Tagihan ini sudah LUNAS';
            inp.disabled = true;
        } else {
            label.innerHTML = 'Sisa tagihan: <strong>Rp ' + Number(sisamax).toLocaleString('id-ID') + '</strong> (maks yang bisa dibayar)';
            inp.disabled = false;
        }
    } else if (nom) {
        inp.removeAttribute('max');
        inp.value = nom;
        inp.disabled = false;
        var label = document.getElementById('labelSisa');
        if(label) label.remove();
    } else {
        inp.removeAttribute('max');
        inp.value = '';
        inp.disabled = false;
        var label = document.getElementById('labelSisa');
        if(label) label.remove();
    }
}

// Realtime client-side validation
document.addEventListener('DOMContentLoaded', function() {
    var form = document.querySelector('#mOv form');
    if(form) form.addEventListener('submit', function(e) {
        var inp = document.getElementById('inputJumlah');
        var maxVal = parseFloat(inp.max);
        var val = parseFloat(inp.value);
        if(!isNaN(maxVal) && val > maxVal) {
            e.preventDefault();
            alert('â›” Jumlah melebihi sisa tagihan!\nMaksimal: Rp ' + Number(maxVal).toLocaleString('id-ID'));
        }
    });
});

lucide.createIcons();
</script>
</body></html>
