<?php
// data variables: $institusi, $years, $title
?>
<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/_siakad_header.php'; ?>

        <div class="elite-page-header">
            <div class="header-bg-shapes"></div>
            <div class="header-content-inner">
                <div class="header-text-premium">
                    <div class="header-badge">DATA MASTER</div>
                    <h1><i data-lucide="building-2"></i> Profil Institusi</h1>
                    <p>Kelola identitas resmi madrasah dan konfigurasi sistem aktif.</p>
                </div>
                <div class="header-actions-premium">
                    <button type="submit" form="form-institusi" class="e-btn e-btn-primary">
                        <i data-lucide="save"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>

        <form id="form-institusi" action="<?php echo base_url('siakad/institusi/update'); ?>" method="POST" enctype="multipart/form-data">
            <div class="z-grid2" style="gap:1.5rem; align-items: start;">
                <!-- Left Column: Settings & Logo -->
                <div style="display:flex; flex-direction:column; gap:1.5rem;">
                    <!-- Logo Card -->
                    <div class="z-panel" style="text-align: center; padding: 1.5rem;">
                        <div class="logo-preview-wrapper" style="width: 180px; height: 180px; margin: 0 auto 1.25rem; border-radius: 32px; background: #f8fafc; border: 2px dashed #cbd5e1; position: relative; overflow: hidden; transition: all 0.3s ease;">
                            <?php if($institusi['logo']): ?>
                                <img src="<?php echo base_url('uploads/logo/' . $institusi['logo']); ?>" id="logo-preview" alt="Logo" style="width:100%; height:100%; object-fit:contain; padding:10px;">
                            <?php else: ?>
                                <div class="logo-placeholder" id="logo-placeholder" style="display:flex; align-items:center; justify-content:center; height:100%; color:#94a3b8;">
                                    <i data-lucide="image" style="width:48px;height:48px;"></i>
                                </div>
                            <?php endif; ?>
                            <div class="upload-overlay" style="position: absolute; inset: 0; background: rgba(15, 23, 42, 0.6); display: flex; align-items: center; justify-content: center; opacity: 0; transition: 0.3s; cursor: pointer;">
                                <label for="logo-input" style="color: #fff; display: flex; flex-direction: column; align-items: center; gap: 8px; font-size: 0.85rem; font-weight: 700; cursor: pointer;">
                                    <i data-lucide="camera"></i>
                                    <span>Ganti Logo</span>
                                </label>
                            </div>
                        </div>
                        <input type="file" name="logo" id="logo-input" hidden onchange="previewLogo(this)">
                        <div>
                            <p style="font-size: 0.75rem; color: #94a3b8; margin: 0;">Format: PNG/JPG (Maks 2MB)</p>
                        </div>
                    </div>

                    <!-- Config Card -->
                    <div class="z-panel" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); border: none; color: #fff;">
                        <div style="padding: 1.5rem;">
                            <div style="display: flex; align-items: center; gap: 10px; font-size: 0.8rem; font-weight: 800; letter-spacing: 1px; margin-bottom: 1.5rem;">
                                <i data-lucide="settings-2"></i>
                                <span>KONFIGURASI SISTEM</span>
                            </div>
                            
                            <div class="z-form-group" style="margin-bottom: 1.25rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                    <label style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: rgba(255,255,255,0.8);">Tahun Pelajaran</label>
                                    <button type="button" style="background:none; border:none; color:#bae6fd; font-size:0.72rem; font-weight:700; cursor:pointer; text-decoration:underline;" onclick="document.getElementById('modal-manage-years').style.display='flex'">Kelola</button>
                                </div>
                                <select name="tahun_ajaran_id" style="width: 100%; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 0.75rem 1rem; border-radius: 12px; font-size: 0.9rem; font-weight: 600; outline: none;">
                                    <option value="" style="color:#000;">-- Pilih Tahun --</option>
                                    <?php foreach($years as $y): ?>
                                        <option value="<?php echo $y['id']; ?>" style="color:#000;" <?php echo $institusi['tahun_ajaran_id'] == $y['id'] ? 'selected' : ''; ?>>
                                            <?php echo $y['name']; ?> <?php echo $y['is_active'] ? ' - [ AKTIF ]' : ' - (Tidak Aktif)'; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="z-form-group">
                                <label style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: rgba(255,255,255,0.8); display:block; margin-bottom:6px;">Semester</label>
                                <select name="semester" style="width: 100%; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 0.75rem 1rem; border-radius: 12px; font-size: 0.9rem; font-weight: 600; outline: none;">
                                    <option value="Ganjil" style="color:#000;" <?php echo $institusi['semester'] == 'Ganjil' ? 'selected' : ''; ?>>Ganjil</option>
                                    <option value="Genap" style="color:#000;" <?php echo $institusi['semester'] == 'Genap' ? 'selected' : ''; ?>>Genap</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Identity & Location -->
                <div style="display:flex; flex-direction:column; gap:1.5rem;">
                    <!-- Identity Section -->
                    <div class="z-panel">
                        <div class="z-panel-head">
                            <div class="z-panel-title">
                                <i data-lucide="badge-check"></i> Identitas Madrasah
                            </div>
                        </div>
                        <div class="z-panel-body">
                            <div class="z-grid2">
                                <div class="z-form-group" style="grid-column: span 2;">
                                    <label class="z-label">Nama Resmi Institusi</label>
                                    <input type="text" name="nama" value="<?php echo htmlspecialchars($institusi['nama']); ?>" class="z-field" placeholder="Masukkan nama resmi madrasah">
                                </div>
                                <div class="z-form-group">
                                    <label class="z-label">NPSN</label>
                                    <input type="text" name="npsn" value="<?php echo htmlspecialchars($institusi['npsn']); ?>" class="z-field" placeholder="00000000">
                                </div>
                                <div class="z-form-group">
                                    <label class="z-label">NSM</label>
                                    <input type="text" name="nsm" value="<?php echo htmlspecialchars($institusi['nsm']); ?>" class="z-field" placeholder="000000000000">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location Section -->
                    <div class="z-panel">
                        <div class="z-panel-head">
                            <div class="z-panel-title">
                                <i data-lucide="map-pin"></i> Lokasi &amp; Alamat
                            </div>
                        </div>
                        <div class="z-panel-body">
                            <div class="z-grid2">
                                <div class="z-form-group" style="grid-column: span 2;">
                                    <label class="z-label">Alamat Jalan / Gedung</label>
                                    <textarea name="alamat" class="z-field" placeholder="Alamat lengkap..." style="height:100px; resize:none;"><?php echo htmlspecialchars($institusi['alamat']); ?></textarea>
                                </div>
                                <div class="z-form-group">
                                    <label class="z-label">Desa / Kelurahan</label>
                                    <input type="text" name="desa" value="<?php echo htmlspecialchars($institusi['desa']); ?>" class="z-field" placeholder="Nama desa/kelurahan">
                                </div>
                                <div class="z-form-group">
                                    <label class="z-label">Kecamatan</label>
                                    <input type="text" name="kecamatan" value="<?php echo htmlspecialchars($institusi['kecamatan']); ?>" class="z-field" placeholder="Nama kecamatan">
                                </div>
                                <div class="z-form-group">
                                    <label class="z-label">Kota / Kabupaten</label>
                                    <input type="text" name="kota" value="<?php echo htmlspecialchars($institusi['kota']); ?>" class="z-field" placeholder="Nama kota/kab">
                                </div>
                                <div class="z-form-group">
                                    <label class="z-label">Provinsi</label>
                                    <input type="text" name="provinsi" value="<?php echo htmlspecialchars($institusi['provinsi']); ?>" class="z-field" placeholder="Nama provinsi">
                                </div>
                                <div class="z-form-group">
                                    <label class="z-label">Kode Pos</label>
                                    <input type="text" name="kodepos" value="<?php echo htmlspecialchars($institusi['kodepos']); ?>" class="z-field" placeholder="00000">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kontak Section -->
                    <div class="z-panel">
                        <div class="z-panel-head">
                            <div class="z-panel-title">
                                <i data-lucide="phone"></i> Kontak &amp; Media
                            </div>
                        </div>
                        <div class="z-panel-body">
                            <div class="z-grid2">
                                <div class="z-form-group">
                                    <label class="z-label">Nomor Telepon / WA</label>
                                    <input type="text" name="telepon" value="<?php echo htmlspecialchars($institusi['telepon']); ?>" class="z-field" placeholder="Contoh: 0812xxxxxxx">
                                </div>
                                <div class="z-form-group">
                                    <label class="z-label">Email Resmi</label>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($institusi['email']); ?>" class="z-field" placeholder="email@madrasah.sch.id">
                                </div>
                                <div class="z-form-group" style="grid-column: span 2;">
                                    <label class="z-label">Website / Media Sosial</label>
                                    <input type="text" name="website" value="<?php echo htmlspecialchars($institusi['website']); ?>" class="z-field" placeholder="https://www.madrasah.sch.id">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pimpinan Section -->
                    <div class="z-panel">
                        <div class="z-panel-head">
                            <div class="z-panel-title">
                                <i data-lucide="user-cog"></i> Pimpinan Institusi
                            </div>
                        </div>
                        <div class="z-panel-body">
                            <div class="z-grid2">
                                <div class="z-form-group">
                                    <label class="z-label">Nama Kepala Madrasah</label>
                                    <input type="text" name="nama_kepala" value="<?php echo htmlspecialchars($institusi['nama_kepala']); ?>" class="z-field" placeholder="Nama lengkap & gelar">
                                </div>
                                <div class="z-form-group">
                                    <label class="z-label">NIP / NIY Kepala</label>
                                    <input type="text" name="nip_kepala" value="<?php echo htmlspecialchars($institusi['nip_kepala']); ?>" class="z-field" placeholder="NIP atau identitas pegawai">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Manage Years -->
<div id="modal-manage-years" class="z-modal">
    <div class="z-modal-content small">
        <div class="z-modal-header">
            <div class="z-modal-title"><i data-lucide="calendar"></i> Daftar Tahun Pelajaran</div>
            <button type="button" onclick="document.getElementById('modal-manage-years').style.display='none'" class="close-btn"><i data-lucide="x"></i></button>
        </div>
        <div class="z-modal-body">
            <form action="<?php echo base_url('siakad/tahun-ajaran/add'); ?>" method="POST" style="display: flex; gap: 10px; margin-bottom: 1.5rem;">
                <input type="text" name="name" placeholder="Contoh: 2025/2026" required class="z-field" style="flex:1;">
                <button type="submit" class="btn btn-primary">Tambah</button>
            </form>
            <div style="max-height: 300px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px;">
                <table class="z-table-wrap">
                    <?php foreach($years as $y): ?>
                        <tr>
                            <td style="padding: 10px; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #0f172a;"><?php echo htmlspecialchars($y['name']); ?></td>
                            <td style="text-align: right; padding: 10px; border-bottom: 1px solid #f1f5f9;">
                                <?php if($y['is_active']): ?>
                                    <span style="background: #f0f9ff; color: #0ea5e9; padding: 4px 10px; border-radius: 50px; font-size: 0.75rem; font-weight: 800; border: 1px solid rgba(16,185,129,0.2);">[ AKTIF ]</span>
                                <?php else: ?>
                                    <a href="<?php echo base_url('siakad/tahun-ajaran/activate/' . $y['id']); ?>" style="display: inline-block; background: #fff; color: #64748b; padding: 4px 10px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; border: 1px solid #cbd5e1; text-decoration: none; transition: 0.2s;" onmouseover="this.style.color='#0284c7'; this.style.borderColor='#0284c7'" onmouseout="this.style.color='#64748b'; this.style.borderColor='#cbd5e1'">Jadikan Aktif</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .z-main {
        margin-left: calc(var(--z-sidebar-w) + var(--z-gap) * 2);
        flex: 1;
        background: var(--z-bg);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        padding: var(--z-gap) var(--z-gap) var(--z-gap) 0;
    }
    
    .logo-preview-wrapper:hover .upload-overlay {
        opacity: 1 !important;
    }

    @media (max-width: 1024px) {
        .z-grid2 { grid-template-columns: 1fr; }
        form .z-grid2 > div:first-child { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    }
    @media (max-width: 768px) {
        .z-main { margin-left: 0; padding: 1.25rem; }
        .z-page-head-row { flex-direction: column; align-items: flex-start; gap: 1rem; }
    }
</style>

<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('logo-preview');
            const placeholder = document.getElementById('logo-placeholder');
            
            if (preview) {
                preview.src = e.target.result;
            } else if (placeholder) {
                const img = document.createElement('img');
                img.id = 'logo-preview';
                img.src = e.target.result;
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'contain';
                img.style.padding = '10px';
                placeholder.parentNode.replaceChild(img, placeholder);
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
lucide.createIcons();
</script>

