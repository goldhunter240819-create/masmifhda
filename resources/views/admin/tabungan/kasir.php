<?php
$page_title = 'Kasir Tabungan Siswa';
include __DIR__ . '/_layout_top.php';

$autoSiswaId = intval($_GET['siswa_id'] ?? 0);
?>

<!-- Header Banner Elite Sultan -->
<div class="elite-page-header" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
    <div class="header-bg-shapes"></div>
    <div class="header-content-inner">
        <div>
            <span class="header-badge" style="background: rgba(56,189,248,0.2); color: #38bdf8; border-color: rgba(56,189,248,0.3);">
                LAYANAN TRANSAKSI REALTIME
            </span>
            <div class="header-text-premium">
                <h1 style="color:#fff;"><i data-lucide="monitor-smartphone" style="color:#38bdf8;"></i> Kasir Layanan Tabungan</h1>
                <p style="color: rgba(255,255,255,0.75);">Proses penyetoran dan penarikan tunai siswa secara instan & tercatat di mutasi</p>
            </div>
        </div>
        <div class="header-actions-premium" style="display:flex; gap:12px; margin-top: 15px;">
            <a href="<?= base_url('admin/tabungan/siswa') ?>" class="e-btn e-btn-glass" style="color:#fff;">
                <i data-lucide="users"></i> Daftar Nasabah
            </a>
        </div>
    </div>
</div>

<?php if(isset($message) && !empty($message)): ?>
<div style="background: <?= $status=='success'?'#ecfdf5':'#fff1f2' ?>; border: 1px solid <?= $status=='success'?'#a7f3d0':'#fecdd3' ?>; color: <?= $status=='success'?'#065f46':'#9f1239' ?>; padding: 14px 18px; border-radius: 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 700;">
    <i data-lucide="<?= $status=='success'?'check-circle':'alert-circle' ?>"></i>
    <span><?= htmlspecialchars($message) ?></span>
</div>
<?php endif; ?>

<!-- 2-Column Grid Kasir -->
<div style="display: grid; grid-template-columns: minmax(320px, 420px) 1fr; gap: 24px; align-items: start;" id="kasirGrid">
    <!-- Kolom Pencarian Siswa -->
    <div class="z-panel" style="border-radius: 24px; margin-bottom: 0;">
        <div class="z-panel-head">
            <div class="z-panel-title">
                <i data-lucide="search"></i> Cari Nasabah Siswa
            </div>
        </div>
        <div class="z-panel-body">
            <div style="position: relative; margin-bottom: 16px;">
                <input type="text" id="searchInput" class="z-field" placeholder="Ketik Nama atau NIS siswa..." onkeyup="liveSearch()" autocomplete="off" style="padding-left: 38px;">
                <i data-lucide="user" style="width: 18px; height: 18px; position: absolute; left: 12px; top: 12px; color: #94a3b8;"></i>
            </div>
            
            <div id="searchResult" style="max-height: 440px; overflow-y: auto;">
                <div style="text-align: center; color: var(--z-muted); padding: 45px 15px;">
                    <i data-lucide="users" style="width:48px;height:48px;opacity:0.25;margin-bottom:10px;"></i>
                    <p style="font-size: 0.88rem; margin: 0;">Silakan ketik nama siswa untuk memilih nasabah yang akan bertransaksi.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Kolom Transaksi -->
    <div class="z-panel" id="cardTransaksi" style="display: none; border-radius: 24px; margin-bottom: 0;">
        <div class="z-panel-head">
            <div class="z-panel-title">
                <i data-lucide="wallet"></i> Panel Transaksi Kasir
            </div>
            <button type="button" class="btn btn-ghost btn-sm" onclick="resetKasir()">
                <i data-lucide="x"></i> Tutup
            </button>
        </div>
        <div class="z-panel-body" style="background: #fdfefe;">
            
            <!-- Profil Siswa Terpilih -->
            <div style="display: flex; align-items: center; gap: 16px; padding: 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 18px; margin-bottom: 20px;">
                <div style="width: 52px; height: 52px; border-radius: 14px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 1.3rem; flex-shrink: 0;" id="lblInitial">
                    S
                </div>
                <div>
                    <h5 style="font-weight: 800; color: #0f172a; font-size: 1.1rem; margin: 0 0 4px 0;" id="lblNamaSiswa">-</h5>
                    <div style="font-size: 0.82rem; color: var(--z-muted); font-weight: 600;" id="lblKelasNis">-</div>
                </div>
            </div>

            <!-- Box Saldo -->
            <div style="text-align: center; padding: 24px; background: #fff; border: 1px solid #e2e8f0; border-radius: 18px; margin-bottom: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                <div style="font-size: 0.72rem; font-weight: 800; color: var(--z-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px;">SALDO REKENING SAAT INI</div>
                <div style="font-size: 2.2rem; font-weight: 900; color: #0f172a;" id="lblSaldo">Rp 0</div>
            </div>

            <!-- Form Transaksi -->
            <form method="POST" id="formMutasi" style="display: none; padding: 20px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 18px; margin-bottom: 16px;" onsubmit="return handleMutasiSubmit(this);">
                <input type="hidden" name="action" value="transaksi">
                <input type="hidden" name="siswa_id" id="mutasiSiswaId">
                <input type="hidden" name="jenis_mutasi" id="mutasiJenisMutasi">
                
                <div style="font-weight: 800; font-size: 1.05rem; margin-bottom: 16px;" id="formTitle">Form Transaksi</div>
                
                <div class="z-form-group">
                    <label class="z-label">Nominal Transaksi (Rp)</label>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-weight: 800; color: var(--z-text); font-size: 1.1rem;">Rp</span>
                        <input type="number" name="nominal" class="z-field" placeholder="0" required min="1000" step="500" style="font-size: 1.15rem; font-weight: 800;">
                    </div>
                </div>
                
                <div class="z-form-group">
                    <label class="z-label">Keterangan / Catatan</label>
                    <input type="text" name="keterangan" class="z-field" placeholder="Contoh: Tabungan harian / Keperluan sekolah">
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="button" class="btn btn-ghost" style="flex: 1; justify-content: center;" onclick="resetForm()">Batal</button>
                    <button type="submit" id="btnSubmitMutasi" class="btn btn-primary" style="flex: 2; justify-content: center; font-size: 0.95rem;">
                        Konfirmasi Transaksi
                    </button>
                </div>
            </form>

            <!-- Tombol Pilihan Setor / Tarik -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;" id="btnPilihan">
                <button type="button" class="btn" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 20px; border-radius: 18px; flex-direction: column; gap: 10px; cursor: pointer; transition: 0.2s;" onclick="bukaForm('Setor')">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: #10b981; color: #fff; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="arrow-down-left"></i>
                    </div>
                    <span style="font-weight: 800; font-size: 1rem;">Setor Tunai</span>
                </button>

                <button type="button" class="btn" style="background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; padding: 20px; border-radius: 18px; flex-direction: column; gap: 10px; cursor: pointer; transition: 0.2s;" onclick="bukaForm('Tarik')">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: #ef4444; color: #fff; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="arrow-up-right"></i>
                    </div>
                    <span style="font-weight: 800; font-size: 1rem;">Tarik Tunai</span>
                </button>
            </div>

        </div>
    </div>
</div>

<style>
@media (max-width: 900px) {
    #kasirGrid { grid-template-columns: 1fr !important; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    
    <?php if($autoSiswaId > 0): ?>
    setTimeout(function() {
        pilihSiswaById(<?= $autoSiswaId ?>);
    }, 300);
    <?php endif; ?>
});

let searchTimeout;
function liveSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        var kw = document.getElementById('searchInput').value;
        var res = document.getElementById('searchResult');
        if (kw.length >= 2) {
            res.innerHTML = '<div style="text-align:center; padding: 30px; color: var(--z-muted);">Mencari siswa...</div>';
            
            fetch('<?= base_url('apk/api_search_siswa.php') ?>?module=tabungan&q=' + encodeURIComponent(kw))
                .then(r => r.json())
                .then(data => {
                    if(data.length === 0) {
                        res.innerHTML = '<div style="text-align:center; padding: 40px; color: var(--z-muted);"><i data-lucide="user-x" style="width:40px;height:40px;opacity:0.3;margin-bottom:10px;"></i><p>Siswa tidak ditemukan.</p></div>';
                        lucide.createIcons();
                        return;
                    }
                    var html = '<div style="display:flex; flex-direction:column; gap:6px;">';
                    data.forEach(s => {
                        var kls = s.nama_kelas ? s.nama_kelas : '-';
                        html += `
                        <div onclick="pilihSiswa(${s.id}, '${s.nama.replace(/'/g, "\\'")}', '${s.nis}', '${kls}')" style="display:flex; justify-content:space-between; align-items:center; padding: 12px 14px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; cursor:pointer; transition:0.2s;">
                            <div>
                                <div style="font-weight:800; color:#0f172a; font-size:0.92rem;">${s.nama}</div>
                                <div style="font-size:0.75rem; color:#64748b;">NIS: ${s.nis} &bull; Kelas: ${kls}</div>
                            </div>
                            <i data-lucide="chevron-right" style="color:#cbd5e1;width:18px;height:18px;"></i>
                        </div>
                        `;
                    });
                    html += '</div>';
                    res.innerHTML = html;
                    lucide.createIcons();
                });
        } else if (kw.length === 0) {
            res.innerHTML = '<div style="text-align:center; color:var(--z-muted); padding:45px 15px;"><i data-lucide="users" style="width:48px;height:48px;opacity:0.25;margin-bottom:10px;"></i><p style="font-size:0.88rem; margin:0;">Silakan ketik nama siswa untuk memilih nasabah yang akan bertransaksi.</p></div>';
            lucide.createIcons();
        }
    }, 350); 
}

function pilihSiswa(id, nama, nis, kelas) {
    document.getElementById('lblNamaSiswa').innerText = nama;
    document.getElementById('lblKelasNis').innerText = 'NIS: ' + nis + ' | Kelas: ' + kelas;
    document.getElementById('lblInitial').innerText = nama.substring(0, 1).toUpperCase();
    document.getElementById('mutasiSiswaId').value = id;
    
    document.getElementById('cardTransaksi').style.display = 'block';
    resetForm();
    
    document.getElementById('lblSaldo').innerText = 'Memuat...';
    
    fetch('<?= base_url('apk/api_get_tabungan_siswa.php') ?>?siswa_id=' + id)
        .then(r => r.text())
        .then(html => {
            let match = html.match(/Rp\s*([0-9.,]+)/i);
            if (match) {
                document.getElementById('lblSaldo').innerText = 'Rp ' + match[1];
            } else {
                document.getElementById('lblSaldo').innerText = 'Rp 0';
            }
        });
}

function pilihSiswaById(id) {
    fetch('<?= base_url('apk/api_search_siswa.php') ?>?module=tabungan&q=' + id)
        .then(r => r.json())
        .then(data => {
            if(data && data.length > 0) {
                var s = data[0];
                pilihSiswa(s.id, s.nama, s.nis, s.nama_kelas || '-');
            }
        });
}

function bukaForm(jenis) {
    document.getElementById('btnPilihan').style.display = 'none';
    const form = document.getElementById('formMutasi');
    const title = document.getElementById('formTitle');
    const btn = document.getElementById('btnSubmitMutasi');
    const jenisInput = document.getElementById('mutasiJenisMutasi');
    
    form.style.display = 'block';
    jenisInput.value = jenis;
    
    if (jenis === 'Setor') {
        title.innerText = 'Setor Tunai';
        title.style.color = '#059669';
        btn.style.background = '#10b981';
        btn.innerHTML = '<i data-lucide="arrow-down-left" style="margin-right:6px;"></i> Konfirmasi Setoran';
    } else {
        title.innerText = 'Tarik Tunai';
        title.style.color = '#e11d48';
        btn.style.background = '#ef4444';
        btn.innerHTML = '<i data-lucide="arrow-up-right" style="margin-right:6px;"></i> Konfirmasi Penarikan';
    }
    lucide.createIcons();
}

function resetForm() {
    document.getElementById('formMutasi').style.display = 'none';
    document.getElementById('formMutasi').reset();
    document.getElementById('btnPilihan').style.display = 'grid';
}

function resetKasir() {
    document.getElementById('cardTransaksi').style.display = 'none';
    document.getElementById('searchInput').value = '';
    document.getElementById('searchResult').innerHTML = '<div style="text-align:center; color:var(--z-muted); padding:45px 15px;"><i data-lucide="users" style="width:48px;height:48px;opacity:0.25;margin-bottom:10px;"></i><p style="font-size:0.88rem; margin:0;">Silakan ketik nama siswa untuk memilih nasabah yang akan bertransaksi.</p></div>';
    lucide.createIcons();
}

function handleMutasiSubmit(form) {
    const btn = document.getElementById('btnSubmitMutasi');
    if (btn.disabled) return false; // Prevent double click
    
    btn.disabled = true;
    btn.style.opacity = '0.7';
    btn.style.cursor = 'wait';
    btn.innerHTML = '<i data-lucide="hourglass" style="margin-right:6px;"></i> Memproses...';
    lucide.createIcons();
    return true;
}
</script>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
