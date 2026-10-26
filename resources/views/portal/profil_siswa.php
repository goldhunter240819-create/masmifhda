<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$roleName = "Siswa";
$profileUrl = base_url('siswa/profil-siswa');
$initial = strtoupper(substr($nama, 0, 2));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <!-- Font -->
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . date('YmdH')); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/multi_dashboard.css?v=' . date('YmdH')); ?>">
    <style>
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; }
        .grid-3 { display: grid; grid-template-columns: 250px 1fr; gap: 2rem; align-items: start; }
        .form-label { font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 5px; display: block; }
        .form-control { width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; font-size: 0.95rem; background: #fff; }
        .form-control:focus { outline: none; border-color: var(--z-primary); }
        .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 2rem; box-shadow: 0 4px 24px rgba(7,31,20,0.04); }
        .btn { padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; text-decoration: none; justify-content: center; }
        .btn-primary { background: var(--z-primary); color: #fff; }
        .btn-primary:hover { opacity: 0.9; }
        @media (max-width: 992px) { .grid-3 { grid-template-columns: 1fr; } .grid-2 { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="siakad-container">

<?php include __DIR__ . '/sidebar_multi_siswa.php'; ?>

    <!-- ── MAIN AREA ── -->
    <div class="z-main">

<?php include __DIR__ . '/header_multi_siswa.php'; ?>

        <!-- Scrollable Content -->
        <div class="z-scroll" style="padding: 0 var(--z-gap) var(--z-gap);">
            <div style="margin-bottom: 2rem;">
                <h2 style="margin: 0;">Pengaturan Profil</h2>
                <p style="color: var(--text-muted); margin-top: 5px;">Perbarui informasi biodata, kontak, dan foto profil Anda.</p>
            </div>

            <form action="<?php echo base_url('profil-siswa/update'); ?>" method="POST" enctype="multipart/form-data" class="grid-3">
                
                <!-- Kiri: Foto & Ringkasan -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <div class="card" style="padding: 2rem; text-align: center;">
                        <div style="width: 150px; height: 150px; border-radius: 50%; border: 4px solid #f0fdf4; margin: 0 auto 1.5rem; background: #e2e8f0; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                            <?php if($siswa['foto']): ?>
                                <img src="<?php echo base_url('uploads/siswa/' . $siswa['foto']); ?>" style="width:100%; height:100%; object-fit:cover;">
                            <?php else: ?>
                                <i data-lucide="user" style="width: 64px; height: 64px; color: #94a3b8;"></i>
                            <?php endif; ?>
                        </div>
                        <h3 style="font-size: 1.2rem; margin-bottom: 5px;"><?php echo $siswa['nama']; ?></h3>
                        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;"><?php echo isset($siswa['nama_kelas']) ? $siswa['nama_kelas'] : 'Siswa'; ?></p>
                        
                        <label class="btn" style="background: #f1f5f9; color: #475569; padding: 8px 16px; font-size: 0.85rem; width: 100%; justify-content: center;">
                            <i data-lucide="camera" style="width: 16px;"></i> Ganti Foto
                            <input type="file" name="foto" style="display: none;" accept="image/*">
                        </label>
                    </div>

                    <div class="card" style="padding: 1.5rem; background: #0f172a; color: white;">
                        <h4 style="margin-bottom: 1rem; color: #cbd5e1; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;"><i data-lucide="shield" style="width: 14px; display: inline-block; vertical-align: -2px;"></i> Info Akun</h4>
                        <div style="margin-bottom: 10px;">
                            <div style="font-size: 0.75rem; color: #94a3b8;">Username</div>
                            <div style="font-weight: 600; font-size: 1rem; letter-spacing: 0.5px;"><?php echo isset($siswa['username']) ? $siswa['username'] : (isset($siswa['nisn']) ? $siswa['nisn'] : ''); ?></div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: #94a3b8;">Password (Default)</div>
                            <div style="font-weight: 600; font-size: 1rem; letter-spacing: 0.5px; color: #38bdf8;"><?php echo isset($siswa['plain_password']) ? $siswa['plain_password'] : 'hubungi admin'; ?></div>
                        </div>
                    </div>
                </div>

                <!-- Kanan: Area Form -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    
                    <!-- Biodata Diri -->
                    <div class="card" style="padding: 2rem;">
                        <h3 style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 8px; color: var(--primary);">
                            <i data-lucide="user" style="width: 20px;"></i> Biodata Utama
                        </h3>
                        
                        <div class="grid-2">
                            <div>
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" value="<?php echo htmlspecialchars($siswa['nama']); ?>" required>
                            </div>
                            <div>
                                <label class="form-label">NIS / NISN</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($siswa['nis']); ?> / <?php echo htmlspecialchars($siswa['nisn']); ?>" disabled style="background: #f8fafc; color: #94a3b8;">
                            </div>
                        </div>

                        <div class="grid-2">
                            <div>
                                <label class="form-label">NIK (Nomor Induk Kependudukan)</label>
                                <input type="text" name="nik" class="form-control" value="<?php echo htmlspecialchars($siswa['nik'] ?? ''); ?>">
                            </div>
                            <div>
                                <label class="form-label">No. Kartu Keluarga (KK)</label>
                                <input type="text" name="no_kk" class="form-control" value="<?php echo htmlspecialchars($siswa['no_kk'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="grid-2">
                            <div>
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" value="<?php echo htmlspecialchars($siswa['tempat_lahir']); ?>">
                            </div>
                            <div>
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control" value="<?php echo $siswa['tanggal_lahir']; ?>">
                            </div>
                        </div>

                        <div class="grid-2">
                            <div>
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-control">
                                    <option value="L" <?php echo ($siswa['jenis_kelamin'] ?? 'L') == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="P" <?php echo ($siswa['jenis_kelamin'] ?? 'L') == 'P' ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Agama</label>
                                <select name="agama" class="form-control">
                                    <?php $agamas = ['Islam','Kristen','Katolik','Hindu','Budha']; foreach($agamas as $ag): ?>
                                    <option value="<?php echo $ag; ?>" <?php echo ($siswa['agama'] ?? '') == $ag ? 'selected' : ''; ?>><?php echo $ag; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid-2">
                            <div>
                                <label class="form-label">Golongan Darah</label>
                                <select name="gol_darah" class="form-control">
                                    <option value="">- Pilih -</option>
                                    <?php $gols = ['A','B','AB','O']; foreach($gols as $gd): ?>
                                    <option value="<?php echo $gd; ?>" <?php echo ($siswa['gol_darah'] ?? '') == $gd ? 'selected' : ''; ?>><?php echo $gd; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Anak Ke-</label>
                                <input type="number" name="anak_ke" class="form-control" value="<?php echo htmlspecialchars($siswa['anak_ke'] ?? ''); ?>" min="1">
                            </div>
                        </div>
                    </div>

                    <!-- Orang Tua & Wali -->
                    <div class="card" style="padding: 2rem;">
                        <h3 style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 8px; color: var(--primary);">
                            <i data-lucide="users" style="width: 20px;"></i> Orang Tua & Wali
                        </h3>
                        
                        <div class="grid-2">
                            <div>
                                <label class="form-label">Nama Ayah</label>
                                <input type="text" name="nama_ayah" class="form-control" value="<?php echo htmlspecialchars($siswa['nama_ayah'] ?? ''); ?>">
                            </div>
                            <div>
                                <label class="form-label">Pekerjaan Ayah</label>
                                <input type="text" name="pekerjaan_ayah" class="form-control" value="<?php echo htmlspecialchars($siswa['pekerjaan_ayah'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="grid-2">
                            <div>
                                <label class="form-label">Nama Ibu</label>
                                <input type="text" name="nama_ibu" class="form-control" value="<?php echo htmlspecialchars($siswa['nama_ibu'] ?? ''); ?>">
                            </div>
                            <div>
                                <label class="form-label">Pekerjaan Ibu</label>
                                <input type="text" name="pekerjaan_ibu" class="form-control" value="<?php echo htmlspecialchars($siswa['pekerjaan_ibu'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="grid-2">
                            <div>
                                <label class="form-label">No. HP Orang Tua / WhatsApp</label>
                                <input type="text" name="no_hp_ortu" class="form-control" value="<?php echo htmlspecialchars($siswa['no_hp_ortu'] ?? ''); ?>">
                            </div>
                            <div>
                                <label class="form-label">Penghasilan Orang Tua</label>
                                <input type="text" name="penghasilan_ortu" class="form-control" value="<?php echo htmlspecialchars($siswa['penghasilan_ortu'] ?? ''); ?>" placeholder="Rp/bulan">
                            </div>
                        </div>
                        
                        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e2e8f0;">
                            <div class="grid-2">
                                <div>
                                    <label class="form-label">Nama Wali (Opsional)</label>
                                    <input type="text" name="nama_wali" class="form-control" value="<?php echo htmlspecialchars($siswa['nama_wali'] ?? ''); ?>">
                                </div>
                                <div>
                                    <label class="form-label">Pekerjaan Wali</label>
                                    <input type="text" name="pekerjaan_wali" class="form-control" value="<?php echo htmlspecialchars($siswa['pekerjaan_wali'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="grid-2" style="margin-top: 1.5rem; margin-bottom: 0;">
                                <div>
                                    <label class="form-label">No. HP Wali</label>
                                    <input type="text" name="no_hp_wali" class="form-control" value="<?php echo htmlspecialchars($siswa['no_hp_wali'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alamat & Domisili -->
                    <div class="card" style="padding: 2rem;">
                        <h3 style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 8px; color: var(--primary);">
                            <i data-lucide="map-pin" style="width: 20px;"></i> Alamat Domisili
                        </h3>
                        
                        <div style="margin-bottom: 1.5rem;">
                            <label class="form-label">Alamat Lengkap (Jalan/Dusun)</label>
                            <textarea name="alamat" class="form-control" rows="3"><?php echo htmlspecialchars($siswa['alamat']); ?></textarea>
                        </div>
                        
                        <div class="grid-2">
                            <div>
                                <label class="form-label">RT</label>
                                <input type="text" name="rt" class="form-control" value="<?php echo htmlspecialchars($siswa['rt'] ?? ''); ?>">
                            </div>
                            <div>
                                <label class="form-label">RW</label>
                                <input type="text" name="rw" class="form-control" value="<?php echo htmlspecialchars($siswa['rw'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="grid-2">
                            <div>
                                <label class="form-label">Desa / Kelurahan</label>
                                <input type="text" name="desa" class="form-control" value="<?php echo htmlspecialchars($siswa['desa'] ?? ''); ?>">
                            </div>
                            <div>
                                <label class="form-label">Kecamatan</label>
                                <input type="text" name="kecamatan" class="form-control" value="<?php echo htmlspecialchars($siswa['kecamatan'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="grid-2">
                            <div>
                                <label class="form-label">Kabupaten / Kota</label>
                                <input type="text" name="kota" class="form-control" value="<?php echo htmlspecialchars($siswa['kota'] ?? ''); ?>">
                            </div>
                            <div>
                                <label class="form-label">Provinsi</label>
                                <input type="text" name="provinsi" class="form-control" value="<?php echo htmlspecialchars($siswa['provinsi'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="grid-2" style="margin-bottom:0;">
                            <div>
                                <label class="form-label">Kode Pos</label>
                                <input type="text" name="kode_pos" class="form-control" value="<?php echo htmlspecialchars($siswa['kode_pos'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Pendidikan -->
                    <div class="card" style="padding: 2rem;">
                        <h3 style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 8px; color: var(--primary);">
                            <i data-lucide="graduation-cap" style="width: 20px;"></i> Data Pendidikan
                        </h3>
                        
                        <div class="grid-2">
                            <div>
                                <label class="form-label">Kelas Saat Ini</label>
                                <input type="text" class="form-control" value="<?php echo isset($siswa['nama_kelas']) ? $siswa['nama_kelas'] : ''; ?>" disabled style="background: #f8fafc; color: #94a3b8;">
                            </div>
                            <div>
                                <label class="form-label">Sekolah Asal</label>
                                <input type="text" name="sekolah_asal" class="form-control" value="<?php echo htmlspecialchars($siswa['sekolah_asal'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="grid-2" style="margin-bottom:0;">
                            <div>
                                <label class="form-label">Pendidikan Terakhir</label>
                                <input type="text" name="pendidikan_terakhir" class="form-control" value="<?php echo htmlspecialchars($siswa['pendidikan_terakhir'] ?? ''); ?>" placeholder="Misal: SD / MI">
                            </div>
                            <div>
                                <label class="form-label">No. Ijazah Sebelumnya</label>
                                <input type="text" name="no_ijazah" class="form-control" value="<?php echo htmlspecialchars($siswa['no_ijazah'] ?? ''); ?>">
                            </div>
                        </div>

                        <div style="margin-top: 2rem; border-top: 1px solid var(--border); padding-top: 1.5rem; text-align: right;">
                            <button type="submit" class="btn btn-primary">
                                <i data-lucide="save"></i> SIMPAN PERUBAHAN
                            </button>
                        </div>
                    </div>

                </div>

            </form>
        </div><!-- /z-scroll -->
    </div><!-- /z-main -->
</div><!-- /siakad-container -->

<?php include __DIR__ . '/footer_multi_siswa.php'; ?>
