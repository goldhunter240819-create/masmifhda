<!DOCTYPE html><html lang="id"><head>
<?php $page_title='Master Jenis Tagihan'; include __DIR__.'/_zamrud_head.php'; ?>
</head><body>
<div class="z-wrap">
<?php include __DIR__.'/_zamrud_sidebar.php'; ?>
<main class="z-main">
<header class="z-header">
    <div class="z-header-left">
        <button class="z-hamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
        <span class="z-header-title">Jenis Tagihan</span><span class="z-badge">Admin</span>
    </div>
    <div class="z-header-user">
        <div class="z-user-info"><div class="name"><?php echo $adminNama; ?></div><div class="role">Master Administrator</div></div>
        <div class="z-avatar"><?php echo $adminInitial; ?></div>
    </div>
</header>
<div class="z-scroll">
<div class="z-page-head-row">
    <div><h1>Master Jenis Tagihan</h1><p>Kelola daftar tarif baku untuk SPP atau tagihan komite</p></div>
    <div style="display:flex; gap:10px;">
        <button type="button" class="btn btn-primary" onclick="resetModal(); document.getElementById('mOv').classList.add('open')"><i data-lucide="plus" style="width:14px;height:14px;"></i>Tambah Jenis Tagihan</button>
    </div>
</div>
<div class="z-panel">
    <div class="z-panel-head"><div class="z-panel-title"><i data-lucide="tags"></i>Daftar Jenis Tagihan</div></div>
    <div class="z-table-wrap"><table id="tbl">
        <thead><tr><th>Tahun Pelajaran</th><th>Nama Tagihan</th><th>Kategori</th><th>Periode</th><th>Sifat</th><th>Target</th><th>Nominal (Rp)</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php foreach($jenisList as $j): ?>
            <tr>
                <td><span class="pill pill-green"><?php echo $j['nama_tahun'] ?: '-'; ?></span></td>
                <td style="font-weight:600;"><?php echo $j['nama_tagihan']; ?></td>
                <td><span class="pill" style="background:#f1f5f9; color:#475569; border-color:#cbd5e1;"><?php echo isset($j['kategori']) && !empty($j['kategori']) ? htmlspecialchars($j['kategori']) : 'Lainnya'; ?></span></td>
                <td><span class="pill pill-purple"><?php echo $j['jenis_periode']; ?></span></td>
                <td><span class="pill <?php echo $j['sifat']=='Wajib'?'pill-blue':'pill-amber'; ?>"><?php echo $j['sifat']; ?></span></td>
                <td>
                    <?php 
                    if($j['target_tipe']=='Semua'){ echo 'Semua Siswa'; }
                    else if($j['target_tipe']=='Kelas'){ echo 'Kelas: '.$j['nama_kelas']; }
                    else if($j['target_tipe']=='Siswa'){ echo 'Siswa: '.$j['nama_siswa']; }
                    else if($j['target_tipe']=='Individu'){ echo 'Per Individu'; }
                    ?>
                </td>
                <td class="z-amount">Rp <?php echo number_format($j['nominal'],0,',','.'); ?></td>
                <td>
                    <?php if (isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 99])): ?>
                        <div style="display:flex; gap:5px;">
                            <button type="button" class="btn btn-primary btn-sm" onclick="openEditModalJenis('<?php echo $j['id']; ?>', '<?php echo htmlspecialchars($j['nama_tagihan'], ENT_QUOTES); ?>', '<?php echo $j['jenis_periode']; ?>', '<?php echo $j['sifat']; ?>', '<?php echo $j['nominal']; ?>', '<?php echo $j['target_tipe']; ?>', '<?php echo $j['target_kelas_id']; ?>', '<?php echo $j['target_siswa_id']; ?>', '<?php echo htmlspecialchars(isset($j['kategori']) ? $j['kategori'] : 'Lainnya', ENT_QUOTES); ?>')">Edit</button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="openDeleteModal('<?php echo $j['id']; ?>', '<?php echo htmlspecialchars($j['nama_tagihan'], ENT_QUOTES); ?>')">Hapus</button>
                        </div>
                    <?php else: ?>
                        <span class="pill pill-amber" style="font-size:10px;">Akses Terbatas</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($jenisList)): ?>
            <tr><td colspan="6" style="text-align:center;color:var(--z-muted);padding:20px;">Belum ada master jenis tagihan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table></div>
</div>
</div></main></div>

<div class="z-modal-ov" id="mOv" onclick="if(event.target===this)this.classList.remove('open')">
<div class="z-modal">
    <div class="z-modal-head">
        <div class="z-modal-title"><i data-lucide="plus-circle"></i><span id="modalTitle">Tambah Jenis Tagihan</span></div>
        <button type="button" class="z-modal-close" onclick="document.getElementById('mOv').classList.remove('open')"><i data-lucide="x"></i></button>
    </div>
    <form id="modalForm" action="<?php echo base_url('admin/finance/komite/jenis-tagihan/save'); ?>" method="POST">
        <input type="hidden" name="jenis_id" id="jenis_id_input" value="0">
        <div class="z-modal-body">
        <div class="z-form-group">
            <label class="z-label">Tahun Pelajaran Berlaku</label>
            <select class="z-field" name="tahun_ajaran_id" id="m_tahun_ajaran_id" onchange="filterStudentList()" required>
                <?php foreach($tahunAjaranList as $ta): ?>
                <option value="<?php echo $ta['id']; ?>"><?php echo $ta['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="z-grid2">
            <div class="z-form-group"><label class="z-label">Nama Tagihan</label><input class="z-field" type="text" name="nama_tagihan" id="m_nama_tagihan" placeholder="Contoh: Uang SPP Bulanan" required></div>
            <div class="z-form-group">
                <label class="z-label">Kategori</label>
                <select class="z-field" name="kategori" id="m_kategori" required>
                    <option value="Lainnya">Lainnya</option>
                    <?php if(isset($kategoriList)): foreach($kategoriList as $kat): ?>
                        <option value="<?php echo htmlspecialchars($kat['kategori']); ?>"><?php echo htmlspecialchars($kat['kategori']); ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
        </div>
        <div class="z-grid2">
            <div class="z-form-group">
                <label class="z-label">Tipe Pembayaran</label>
                <select class="z-field" name="jenis_periode" id="m_jenis_periode" required>
                    <option value="Bulanan">Bulanan</option>
                    <option value="Tahunan">Tahunan</option>
                </select>
            </div>
            <div class="z-form-group">
                <label class="z-label">Sifat Tagihan</label>
                <select class="z-field" name="sifat" id="m_sifat" required>
                    <option value="Wajib">Wajib</option>
                    <option value="Opsional">Opsional/Sukarela</option>
                </select>
            </div>
        </div>
        <div class="z-grid2">
            <div class="z-form-group">
                <label class="z-label">Target Tagihan</label>
                <select class="z-field" name="target_tipe" id="targetTipe" onchange="toggleTarget()" required>
                    <option value="Semua">Semua Siswa (Umum)</option>
                    <option value="Kelas">Per Kelas (Spesifik Kelas)</option>
                    <option value="Siswa">Per Siswa (Spesifik Individu)</option>
                    <option value="Individu">Per Individu (Sudah Set Data Tunggakan)</option>
                </select>
            </div>
            <div class="z-form-group">
                <label class="z-label">Nominal (Rp)</label>
                <input class="z-field" type="number" name="nominal" id="m_nominal" placeholder="150000" required>
            </div>
        </div>
        
        <div class="z-form-group" id="wrapKelas" style="display:none;">
            <label class="z-label">Pilih Kelas Target (Bisa pilih lebih dari satu)</label>
            <select class="z-field" name="target_kelas_ids[]" multiple style="min-height:100px;">
                <?php foreach($kelasList as $k): ?>
                <option value="<?php echo $k['id']; ?>"><?php echo $k['nama_kelas']; ?></option>
                <?php endforeach; ?>
            </select>
            <small style="color:#64748b; font-size:11px;">Tahan tombol CTRL untuk memilih beberapa kelas</small>
        </div>
        
        <div id="wrapSiswa" style="display:none;">
            <div class="z-grid2" style="grid-template-columns: 1fr 1fr 80px; align-items: end; gap:10px;">
                <div class="z-form-group">
                    <label class="z-label">1. Filter Kelas</label>
                    <select class="z-field" id="fKelas" onchange="filterStudentList()">
                        <option value="">-- Semua Kelas --</option>
                        <?php foreach($kelasList as $k): ?>
                        <option value="<?php echo $k['id']; ?>"><?php echo $k['nama_kelas']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="z-form-group">
                    <label class="z-label">2. Pilih Siswa</label>
                    <select class="z-field" id="fSiswa">
                        <option value="">-- Pilih Siswa --</option>
                    </select>
                </div>
                <div class="z-form-group">
                    <button type="button" class="btn btn-primary" onclick="addSiswaToTarget()" style="width:100%; padding: 10px 0;"><i data-lucide="plus"></i></button>
                </div>
            </div>
            <div class="z-label" style="margin-top:10px;">Daftar Siswa Terpilih:</div>
            <div id="targetList" style="display:flex; flex-wrap:wrap; gap:5px; margin-top:5px; padding:10px; border:1px dashed #ddd; border-radius:8px; min-height:40px;">
                <!-- Selected students will appear here -->
            </div>
            <div id="hiddenInputs"></div>
        </div>
        
        </div>
        <div class="z-modal-foot"><button type="button" class="btn btn-ghost" onclick="document.getElementById('mOv').classList.remove('open')">Batal</button><button type="submit" class="btn btn-primary"><i data-lucide="save" style="width:13px;height:13px;"></i>Simpan</button></div>
    </form>
</div></div>

<!-- Modal Confirm Delete -->
<div class="z-modal-ov" id="mConfirmDelete" onclick="if(event.target===this)this.classList.remove('open')">
<div class="z-modal" style="max-width: 400px; text-align: center;">
    <div class="z-modal-body" style="padding: 30px 20px;">
        <div style="width: 60px; height: 60px; background: #fee2e2; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto;">
            <i data-lucide="trash-2" style="width: 32px; height: 32px;"></i>
        </div>
        <h3 style="margin-bottom: 10px; font-size: 1.2rem; color: #1e293b;">Hapus <span id="delTagName">Tagihan</span>?</h3>
        <p style="color: #64748b; font-size: 0.95rem; line-height: 1.5; margin-bottom: 0;">Peringatan! Menghapus master tagihan ini juga akan <b>menghapus seluruh tagihan siswa yang bersangkutan</b> pada aplikasi HP siswa. Tindakan ini tidak bisa dibatalkan.</p>
    </div>
    <div class="z-modal-foot" style="justify-content: center; background: #f8fafc;">
        <button type="button" class="btn btn-ghost" onclick="document.getElementById('mConfirmDelete').classList.remove('open')">Batal</button>
        <a href="#" id="btnConfirmDelete" class="btn btn-danger" onclick="this.innerHTML='<i data-lucide=\'loader\' class=\'spin\'></i> Menghapus...'; this.style.pointerEvents='none';"><i data-lucide="trash" style="width:14px;height:14px;"></i> Ya, Hapus Data</a>
    </div>
</div></div>

<script>
function openDeleteModal(id, name) {
    document.getElementById('delTagName').innerText = name;
    document.getElementById('btnConfirmDelete').href = '<?php echo base_url('admin/finance/komite/jenis-tagihan/delete/'); ?>' + id;
    document.getElementById('mConfirmDelete').classList.add('open');
}

var allSiswas = <?php echo json_encode($siswas); ?>;
var allSiswasRaw = <?php echo json_encode($allSiswasRaw); ?>;
var selectedSiswaIds = [];

function toggleTarget() {
    var v = document.getElementById('targetTipe').value;
    document.getElementById('wrapKelas').style.display = (v === 'Kelas') ? 'block' : 'none';
    document.getElementById('wrapSiswa').style.display = (v === 'Siswa') ? 'block' : 'none';
    if(v === 'Siswa') filterStudentList();
}

function filterStudentList() {
    var kid = document.getElementById('fKelas').value;
    var thn = document.getElementById('m_tahun_ajaran_id').value;
    var sel = document.getElementById('fSiswa');
    sel.innerHTML = '<option value="">-- Pilih Siswa --</option>';
    allSiswas.forEach(function(s) {
        if((!kid || s.kelas_id == kid) && (!thn || s.tahun_ajaran_id == thn)) {
            var opt = document.createElement('option');
            opt.value = s.id;
            opt.text = s.nama + ' (NIS: ' + s.nis + ')';
            sel.appendChild(opt);
        }
    });
}

function addSiswaToTarget() {
    var sid = document.getElementById('fSiswa').value;
    if(!sid || selectedSiswaIds.includes(sid)) return;
    
    var sObj = allSiswasRaw.find(x => x.id == sid);
    selectedSiswaIds.push(sid);
    
    var pill = document.createElement('div');
    pill.className = 'pill pill-blue';
    pill.style.display = 'flex'; pill.style.alignItems = 'center'; pill.style.gap = '5px';
    pill.innerHTML = sObj.nama + ' <i data-lucide="x" style="width:12px;cursor:pointer" onclick="removeSiswaFromTarget('+sid+', this.parentElement)"></i>';
    document.getElementById('targetList').appendChild(pill);
    
    var inp = document.createElement('input');
    inp.type = 'hidden'; inp.name = 'target_siswa_ids[]'; inp.value = sid; inp.id = 'inp_s_'+sid;
    document.getElementById('hiddenInputs').appendChild(inp);
    
    lucide.createIcons();
}

function removeSiswaFromTarget(sid, el) {
    selectedSiswaIds = selectedSiswaIds.filter(id => id != sid);
    el.remove();
    document.getElementById('inp_s_'+sid).remove();
}

function openEditModalJenis(id, nama, periode, sifat, nominal, target_tipe, target_kelas_id, target_siswa_id, kategori) {
    document.getElementById('modalForm').reset();
    document.getElementById('jenis_id_input').value = id;
    document.getElementById('m_nama_tagihan').value = nama;
    document.getElementById('m_kategori').value = kategori || 'Lainnya';
    document.getElementById('m_jenis_periode').value = periode;
    document.getElementById('m_sifat').value = sifat;
    document.getElementById('m_nominal').value = nominal;
    document.getElementById('targetTipe').value = target_tipe;
    document.getElementById('modalTitle').innerText = 'Edit Jenis Tagihan';
    
    // Hide all target sections
    document.getElementById('wrapKelas').style.display = 'none';
    document.getElementById('wrapSiswa').style.display = 'none';
    
    // Reset siswa target list
    document.getElementById('targetList').innerHTML = '';
    document.getElementById('hiddenInputs').innerHTML = '';
    selectedSiswaIds = [];
    
    if (target_tipe === 'Kelas') {
        document.getElementById('wrapKelas').style.display = 'block';
        if (target_kelas_id) {
            var cids = target_kelas_id.split(',');
            var kelasSelect = document.querySelector('select[name="target_kelas_ids[]"]');
            if(kelasSelect) {
                Array.from(kelasSelect.options).forEach(opt => {
                    opt.selected = cids.includes(opt.value);
                });
            }
        }
    } else if (target_tipe === 'Siswa' || target_tipe === 'Individu') {
        document.getElementById('wrapSiswa').style.display = 'block';
        if (target_siswa_id) {
            var sids = target_siswa_id.split(',');
            sids.forEach(function(sid) {
                if (sid) {
                    var sObj = allSiswasRaw.find(x => x.id == sid);
                    if (sObj) {
                        selectedSiswaIds.push(sid);
                        var pill = document.createElement('div');
                        pill.className = 'pill pill-blue';
                        pill.style.display = 'flex'; pill.style.alignItems = 'center'; pill.style.gap = '5px';
                        pill.innerHTML = sObj.nama + ' <i data-lucide="x" style="width:12px;cursor:pointer" onclick="removeSiswaFromTarget('+sid+', this.parentElement)"></i>';
                        document.getElementById('targetList').appendChild(pill);
                        
                        var inp = document.createElement('input');
                        inp.type = 'hidden'; inp.name = 'target_siswa_ids[]'; inp.value = sid; inp.id = 'inp_s_'+sid;
                        document.getElementById('hiddenInputs').appendChild(inp);
                    }
                }
            });
            lucide.createIcons();
        }
    }
    
    document.getElementById('mOv').classList.add('open');
}

function resetModal() {
    document.getElementById('modalForm').reset();
    document.getElementById('jenis_id_input').value = '0';
    document.getElementById('m_kategori').value = '';
    document.getElementById('modalTitle').innerText = 'Tambah Jenis Tagihan';
    document.getElementById('wrapKelas').style.display = 'none';
    document.getElementById('wrapSiswa').style.display = 'none';
}

function zToggleSidebar(){document.getElementById('zSidebar').classList.toggle('open');document.getElementById('zOverlay').classList.toggle('open');}
lucide.createIcons();
</script>
</body></html>
