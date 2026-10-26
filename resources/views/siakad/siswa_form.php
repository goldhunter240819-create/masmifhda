<?php
$isEdit = !empty($siswa);
$formAction = $isEdit ? base_url('siakad/siswa/update') : base_url('siakad/siswa/add');
$v = function($key) use ($siswa) { return $siswa ? htmlspecialchars(isset($siswa[$key]) ? $siswa[$key] : '') : ''; };
?>
<style>
    .sf-wrap { display: flex; flex-direction: column; gap: 0; min-height: 100vh; }
    .sf-topbar {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        padding: 1.5rem 2.5rem; display: flex; align-items: center;
        justify-content: space-between; color: #fff;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        position: sticky; top: 0; z-index: 100;
    }
    .sf-topbar-left { display: flex; align-items: center; gap: 18px; }
    .sf-back-btn {
        width: 42px; height: 42px; border-radius: 14px; background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.12); color: #fff; display: flex;
        align-items: center; justify-content: center; cursor: pointer; transition: 0.2s;
        text-decoration: none;
    }
    .sf-back-btn:hover { background: rgba(255,255,255,0.15); transform: translateX(-2px); }
    .sf-topbar-title h1 { font-size: 1.4rem; font-weight: 900; margin: 0; letter-spacing: -0.5px; }
    .sf-topbar-title p { font-size: 0.82rem; opacity: 0.6; margin: 3px 0 0; }
    .sf-badge { background: rgba(16,185,129,0.2); color: #0ea5e9; padding: 5px 14px;
        border-radius: 50px; font-size: 0.72rem; font-weight: 800; letter-spacing: 1px;
        border: 1px solid rgba(16,185,129,0.3); }
    .sf-save-btn {
        display: inline-flex; align-items: center; gap: 10px; padding: 13px 32px;
        background: #0ea5e9; color: #fff; border: none; border-radius: 16px;
        font-weight: 800; font-size: 0.95rem; cursor: pointer; transition: 0.3s;
        box-shadow: 0 8px 20px rgba(16,185,129,0.3); font-family: inherit;
    }
    .sf-save-btn:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(16,185,129,0.4); }
    .sf-save-btn i { width: 20px; height: 20px; }

    /* Layout 2-kolom: sidebar tabs + konten */
    .sf-body { display: flex; gap: 0; flex: 1; min-height: calc(100vh - 85px); }

    /* Tab Sidebar Vertikal */
    .sf-tabs-sidebar {
        width: 240px; flex-shrink: 0; background: #f8fafc;
        border-right: 1px solid #e2e8f0; padding: 2rem 1rem;
        display: flex; flex-direction: column; gap: 6px;
        position: sticky; top: 85px; height: calc(100vh - 85px); overflow-y: auto;
    }
    .sf-tab {
        display: flex; align-items: center; gap: 12px; padding: 13px 16px;
        border-radius: 14px; cursor: pointer; transition: 0.2s; color: #64748b;
        font-weight: 700; font-size: 0.88rem; border: none; background: none;
        width: 100%; text-align: left;
    }
    .sf-tab:hover { background: #fff; color: #0ea5e9; }
    .sf-tab.active { background: #fff; color: #0ea5e9; box-shadow: 0 4px 15px rgba(0,0,0,0.06); }
    .sf-tab i { width: 18px; height: 18px; flex-shrink: 0; }
    .sf-tab-num {
        margin-left: auto; width: 22px; height: 22px; border-radius: 8px;
        background: #f1f5f9; font-size: 0.7rem; font-weight: 800;
        display: flex; align-items: center; justify-content: center; color: #94a3b8;
    }
    .sf-tab.active .sf-tab-num { background: #f0f9ff; color: #0ea5e9; }

    /* Konten Panel */
    .sf-panels { flex: 1; padding: 2.5rem; background: #fff; overflow-y: auto; }
    .sf-panel { display: none; }
    .sf-panel.active { display: block; animation: panelIn 0.3s ease; }
    @keyframes panelIn { from { opacity: 0; transform: translateX(15px); } to { opacity: 1; transform: translateX(0); } }

    .sf-section-title {
        font-size: 1.1rem; font-weight: 900; color: #0f172a; margin: 0 0 1.75rem;
        padding-bottom: 1rem; border-bottom: 2px solid #f1f5f9;
        display: flex; align-items: center; gap: 12px;
    }
    .sf-section-title::before { content: ''; width: 5px; height: 22px; background: #0ea5e9; border-radius: 4px; }

    .sf-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
    .sf-grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
    .sf-grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; }
    .sf-col2 { grid-column: span 2; }
    .sf-col3 { grid-column: span 3; }
    .sf-col4 { grid-column: span 4; }

    .sf-field label {
        display: block; font-size: 0.72rem; font-weight: 800; color: #64748b;
        text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px;
    }
    .sf-input {
        width: 100%; padding: 13px 16px; border-radius: 14px;
        border: 2px solid #f1f5f9; background: #f8fafc;
        font-weight: 700; color: #1e293b; font-size: 0.9rem;
        transition: 0.2s; box-sizing: border-box; font-family: inherit;
    }
    .sf-input:focus {
        border-color: #0ea5e9; background: #fff; outline: none;
        box-shadow: 0 0 0 5px rgba(16,185,129,0.1);
    }
    .sf-subsection {
        background: #f8fafc; border-radius: 20px; padding: 1.5rem;
        border: 1px solid #e2e8f0; margin-bottom: 1.5rem;
    }
    .sf-subsection h4 {
        margin: 0 0 1.25rem; font-size: 0.9rem; font-weight: 900; color: #0ea5e9;
        display: flex; align-items: center; gap: 8px;
    }
    .sf-subsection h4::before { content: ''; width: 4px; height: 16px; background: #0ea5e9; border-radius: 4px; }

    /* Upload Area */
    .sf-upload-area {
        border: 2px dashed #cbd5e1; border-radius: 24px; padding: 5rem 2rem;
        text-align: center; color: #94a3b8; transition: 0.3s; cursor: pointer;
        background: #f8fafc; position: relative;
    }
    .sf-upload-area:hover { border-color: #0ea5e9; background: #f0fdf4; color: #0ea5e9; }
    .sf-upload-area input[type="file"] {
        position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
    }
    .sf-upload-area i { width: 52px; height: 52px; margin-bottom: 1rem; display: block; margin: 0 auto 1.25rem; }
    .sf-upload-area h3 { font-size: 1.1rem; margin: 0 0 0.5rem; color: #334155; }
    .sf-upload-area p { font-size: 0.85rem; margin: 0; }
    .sf-photo-preview { display: flex; justify-content: center; margin-bottom: 1.5rem; }
    .sf-photo-preview img { width: 130px; height: 130px; object-fit: cover; border-radius: 20px; border: 3px solid #e2e8f0; box-shadow: 0 8px 20px rgba(0,0,0,0.08); }

    /* Footer Bar */
    .sf-footer {
        background: #f8fafc; border-top: 1px solid #e2e8f0;
        padding: 1.25rem 2.5rem; display: flex; justify-content: flex-end;
        align-items: center; gap: 15px; position: sticky; bottom: 0; z-index: 99;
    }
    .sf-cancel-btn {
        padding: 12px 28px; border-radius: 14px; background: transparent;
        border: 2px solid #e2e8f0; color: #64748b; font-weight: 700; cursor: pointer;
        transition: 0.2s; font-family: inherit; font-size: 0.9rem; text-decoration: none;
        display: inline-flex; align-items: center;
    }
    .sf-cancel-btn:hover { background: #f1f5f9; }
</style>

<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="z-main" style="padding: 0;">
        <form action="<?php echo $formAction; ?>" method="POST" enctype="multipart/form-data" id="sf-form">
            <?php if($isEdit): ?><input type="hidden" name="id" value="<?php echo $siswa['id']; ?>"><?php endif; ?>

            <!-- TOP BAR -->
            <div class="sf-topbar">
                <div class="sf-topbar-left">
                    <a href="<?php echo base_url('siakad/siswa'); ?>" class="sf-back-btn"><i data-lucide="arrow-left"></i></a>
                    <div class="sf-topbar-title">
                        <h1><?php echo $isEdit ? 'Edit Data Siswa' : 'Tambah Siswa Baru'; ?></h1>
                        <p><?php echo $isEdit ? 'Memperbarui biodata ' . htmlspecialchars($siswa['nama']) : 'Lengkapi semua data untuk mendaftarkan siswa baru'; ?></p>
                    </div>
                    <span class="sf-badge">SIAKAD ELITE</span>
                </div>
                <button type="submit" class="sf-save-btn">
                    <i data-lucide="save"></i>
                    <?php echo $isEdit ? 'SIMPAN PERUBAHAN' : 'SIMPAN SISWA'; ?>
                </button>
            </div>

            <!-- BODY: Sidebar Tabs + Panels -->
            <div class="sf-body">
                <div class="sf-tabs-sidebar">
                    <div class="sf-tab active" onclick="sfTab('identitas', this)">
                        <i data-lucide="user"></i> Identitas Diri <span class="sf-tab-num">1</span>
                    </div>
                    <div class="sf-tab" onclick="sfTab('pendidikan', this)">
                        <i data-lucide="graduation-cap"></i> Pendidikan <span class="sf-tab-num">2</span>
                    </div>
                    <div class="sf-tab" onclick="sfTab('keluarga', this)">
                        <i data-lucide="users"></i> Orang Tua <span class="sf-tab-num">3</span>
                    </div>
                    <div class="sf-tab" onclick="sfTab('alamat', this)">
                        <i data-lucide="map-pin"></i> Alamat <span class="sf-tab-num">4</span>
                    </div>
                    <div class="sf-tab" onclick="sfTab('foto', this)">
                        <i data-lucide="camera"></i> Foto <span class="sf-tab-num">5</span>
                    </div>
                </div>

                <div class="sf-panels">
                    <!-- Panel 1: Identitas -->
                    <div id="panel-identitas" class="sf-panel active">
                        <div class="sf-section-title">Identitas Diri Siswa</div>
                        <div class="sf-grid-3">
                            <div class="sf-field sf-col2">
                                <label>Nama Lengkap (Sesuai Akta/KK) *</label>
                                <input type="text" name="nama" class="sf-input" value="<?php echo $v('nama'); ?>" required>
                            </div>
                            <div class="sf-field">
                                <label>NIK (16 Digit)</label>
                                <input type="text" name="nik" class="sf-input" value="<?php echo $v('nik'); ?>" maxlength="16">
                            </div>
                            <div class="sf-field">
                                <label>No. Kartu Keluarga (KK)</label>
                                <input type="text" name="no_kk" class="sf-input" value="<?php echo $v('no_kk'); ?>" maxlength="16">
                            </div>
                            <div class="sf-field">
                                <label>NIS (Nomor Induk Sekolah) *</label>
                                <input type="text" name="nis" class="sf-input" value="<?php echo $v('nis'); ?>" <?php echo $isEdit ? 'readonly' : 'required'; ?>>
                            </div>
                            <div class="sf-field">
                                <label>NISN (Nasional)</label>
                                <input type="text" name="nisn" class="sf-input" value="<?php echo $v('nisn'); ?>">
                            </div>
                            <div class="sf-field">
                                <label>Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="sf-input" value="<?php echo $v('tempat_lahir'); ?>">
                            </div>
                            <div class="sf-field">
                                <label>Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="sf-input" value="<?php echo $v('tanggal_lahir'); ?>">
                            </div>
                            <div class="sf-field">
                                <label>Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="sf-input">
                                    <option value="L" <?php echo $v('jenis_kelamin') == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="P" <?php echo $v('jenis_kelamin') == 'P' ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="sf-field">
                                <label>Agama</label>
                                <select name="agama" class="sf-input">
                                    <?php foreach(['Islam','Kristen','Katolik','Hindu','Budha'] as $ag): ?>
                                    <option value="<?php echo $ag; ?>" <?php echo $v('agama') == $ag ? 'selected' : ''; ?>><?php echo $ag; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="sf-field">
                                <label>Golongan Darah</label>
                                <select name="gol_darah" class="sf-input">
                                    <option value="">- Pilih -</option>
                                    <?php foreach(['A','B','AB','O'] as $gd): ?>
                                    <option value="<?php echo $gd; ?>" <?php echo $v('gol_darah') == $gd ? 'selected' : ''; ?>><?php echo $gd; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="sf-field">
                                <label>Kewarganegaraan</label>
                                <input type="text" name="kewarganegaraan" class="sf-input" value="<?php echo $v('kewarganegaraan') ?: 'WNI'; ?>">
                            </div>
                            <div class="sf-field">
                                <label>Anak Ke-</label>
                                <input type="number" name="anak_ke" class="sf-input" value="<?php echo $v('anak_ke'); ?>" min="1">
                            </div>
                        </div>
                    </div>

                    <!-- Panel 2: Pendidikan -->
                    <div id="panel-pendidikan" class="sf-panel">
                        <div class="sf-section-title">Data Pendidikan</div>
                        <div class="sf-grid-2">
                            <div class="sf-field">
                                <label>Kelas Saat Ini *</label>
                                <select name="kelas_id" class="sf-input">
                                    <option value="">-- Pilih Kelas --</option>
                                    <?php foreach($kelas as $k): ?>
                                    <option value="<?php echo $k['id']; ?>" <?php echo $v('kelas_id') == $k['id'] ? 'selected' : ''; ?>>
                                        KELAS <?php echo $k['nama_kelas']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="sf-field">
                                <label>Pendidikan Terakhir</label>
                                <input type="text" name="pendidikan_terakhir" class="sf-input" value="<?php echo $v('pendidikan_terakhir'); ?>" placeholder="SD/MI">
                            </div>
                            <div class="sf-field">
                                <label>Sekolah Asal</label>
                                <input type="text" name="sekolah_asal" class="sf-input" value="<?php echo $v('sekolah_asal'); ?>">
                            </div>
                            <div class="sf-field">
                                <label>No. Ijazah</label>
                                <input type="text" name="no_ijazah" class="sf-input" value="<?php echo $v('no_ijazah'); ?>">
                            </div>
                            <div class="sf-field">
                                <label>No. Paspor</label>
                                <input type="text" name="no_paspor" class="sf-input" value="<?php echo $v('no_paspor'); ?>">
                            </div>
                            <div class="sf-field">
                                <label>No. KITAS</label>
                                <input type="text" name="no_kitas" class="sf-input" value="<?php echo $v('no_kitas'); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Panel 3: Orang Tua -->
                    <div id="panel-keluarga" class="sf-panel">
                        <div class="sf-section-title">Data Orang Tua & Wali</div>
                        <div class="sf-grid-2">
                            <div class="sf-subsection">
                                <h4>Data Ayah</h4>
                                <div class="sf-field" style="margin-bottom:1rem;">
                                    <label>Nama Ayah</label>
                                    <input type="text" name="nama_ayah" class="sf-input" value="<?php echo $v('nama_ayah'); ?>">
                                </div>
                                <div class="sf-field">
                                    <label>Pekerjaan Ayah</label>
                                    <input type="text" name="pekerjaan_ayah" class="sf-input" value="<?php echo $v('pekerjaan_ayah'); ?>">
                                </div>
                            </div>
                            <div class="sf-subsection">
                                <h4>Data Ibu</h4>
                                <div class="sf-field" style="margin-bottom:1rem;">
                                    <label>Nama Ibu</label>
                                    <input type="text" name="nama_ibu" class="sf-input" value="<?php echo $v('nama_ibu'); ?>">
                                </div>
                                <div class="sf-field">
                                    <label>Pekerjaan Ibu</label>
                                    <input type="text" name="pekerjaan_ibu" class="sf-input" value="<?php echo $v('pekerjaan_ibu'); ?>">
                                </div>
                            </div>
                            <div class="sf-field">
                                <label>Penghasilan Orang Tua</label>
                                <input type="text" name="penghasilan_ortu" class="sf-input" value="<?php echo $v('penghasilan_ortu'); ?>" placeholder="Rp/bulan">
                            </div>
                            <div class="sf-field">
                                <label>No. HP Orang Tua *</label>
                                <input type="text" name="no_hp_ortu" class="sf-input" value="<?php echo $v('no_hp_ortu'); ?>">
                            </div>
                            <div class="sf-field sf-col2">
                                <label>Nama Wali (Jika ada)</label>
                                <input type="text" name="nama_wali" class="sf-input" value="<?php echo $v('nama_wali'); ?>">
                            </div>
                            <div class="sf-field">
                                <label>Pekerjaan Wali</label>
                                <input type="text" name="pekerjaan_wali" class="sf-input" value="<?php echo $v('pekerjaan_wali'); ?>">
                            </div>
                            <div class="sf-field">
                                <label>No. HP Wali</label>
                                <input type="text" name="no_hp_wali" class="sf-input" value="<?php echo $v('no_hp_wali'); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Panel 4: Alamat -->
                    <div id="panel-alamat" class="sf-panel">
                        <div class="sf-section-title">Data Alamat</div>
                        <div class="sf-grid-4">
                            <div class="sf-field sf-col4">
                                <label>Alamat Jalan / Dusun</label>
                                <input type="text" name="alamat" class="sf-input" value="<?php echo $v('alamat'); ?>">
                            </div>
                            <div class="sf-field">
                                <label>RT</label>
                                <input type="text" name="rt" class="sf-input" value="<?php echo $v('rt'); ?>">
                            </div>
                            <div class="sf-field">
                                <label>RW</label>
                                <input type="text" name="rw" class="sf-input" value="<?php echo $v('rw'); ?>">
                            </div>
                            <div class="sf-field sf-col2">
                                <label>Kelurahan / Desa</label>
                                <input type="text" name="desa" class="sf-input" value="<?php echo $v('desa'); ?>">
                            </div>
                            <div class="sf-field sf-col2">
                                <label>Kecamatan</label>
                                <input type="text" name="kecamatan" class="sf-input" value="<?php echo $v('kecamatan'); ?>">
                            </div>
                            <div class="sf-field sf-col2">
                                <label>Kabupaten / Kota</label>
                                <input type="text" name="kota" class="sf-input" value="<?php echo $v('kota'); ?>">
                            </div>
                            <div class="sf-field">
                                <label>Provinsi</label>
                                <input type="text" name="provinsi" class="sf-input" value="<?php echo $v('provinsi'); ?>">
                            </div>
                            <div class="sf-field">
                                <label>Kode Pos</label>
                                <input type="text" name="kode_pos" class="sf-input" value="<?php echo $v('kode_pos'); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Panel 5: Foto -->
                    <div id="panel-foto" class="sf-panel">
                        <div class="sf-section-title">Foto Siswa</div>
                        <?php if($isEdit && $siswa['foto']): ?>
                        <div class="sf-photo-preview">
                            <img id="foto-preview-img" src="<?php echo base_url('uploads/siswa/' . $siswa['foto']); ?>" alt="Foto Siswa">
                        </div>
                        <?php else: ?>
                        <div class="sf-photo-preview" id="preview-wrap" style="display:none;">
                            <img id="foto-preview-img" src="" alt="Preview">
                        </div>
                        <?php endif; ?>
                        <div class="sf-upload-area" id="upload-drop">
                            <input type="file" name="foto" accept="image/*" onchange="previewFoto(this)">
                            <i data-lucide="image-plus"></i>
                            <h3>Klik atau Drag & Drop Foto</h3>
                            <p>Format JPG, PNG, WEBP Ã‚Â· Maksimal 2MB</p>
                        </div>
                    </div>
                </div><!-- /sf-panels -->
            </div><!-- /sf-body -->

            <!-- FOOTER -->
            <div class="sf-footer">
                <a href="<?php echo base_url('siakad/siswa'); ?>" class="sf-cancel-btn">Batal</a>
                <button type="submit" class="sf-save-btn">
                    <i data-lucide="save"></i>
                    <?php echo $isEdit ? 'SIMPAN PERUBAHAN' : 'SIMPAN SISWA BARU'; ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function sfTab(panelId, btn) {
    document.querySelectorAll('.sf-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.sf-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('panel-' + panelId).classList.add('active');
    btn.classList.add('active');
}

function previewFoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('foto-preview-img').src = e.target.result;
            const wrap = document.getElementById('preview-wrap');
            if (wrap) wrap.style.display = 'flex';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

lucide.createIcons();
</script>

