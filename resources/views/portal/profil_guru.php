<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$roleName = "Guru / Tendik";
if(!empty($_SESSION['is_kamad'])) $roleName = "Kepala Madrasah";
$profileUrl = base_url('guru/profil-guru');
$initial = strtoupper(substr($nama, 0, 2));

$myApps = [];
if(isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2 && isset($_SESSION['user_id'])) {
    $db_core = \App\Core\Database::connect('core');
    $uid = $_SESSION['user_id'];
    try {
        $res = $db_core->query("SELECT app_code FROM app_admins WHERE user_id = $uid")->fetchAll();
        foreach($res as $r) {
            $myApps[] = $r['app_code'];
        }
    } catch(\PDOException $e) {}
}
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

<?php include __DIR__ . '/sidebar_multi_guru.php'; ?>

    <!-- ── MAIN AREA ── -->
    <div class="z-main">

<?php include __DIR__ . '/header_multi_guru.php'; ?>

        <!-- Scrollable Content -->
        <div class="z-scroll" style="padding: 0 var(--z-gap) var(--z-gap);">
            <div style="margin-bottom: 2rem;">
                <h2 style="margin: 0;">Pengaturan Profil</h2>
                <p style="color: var(--text-muted); margin-top: 5px;">Perbarui informasi biodata, kontak, dan foto profil Anda.</p>
            </div>

            <form action="<?php echo base_url('profil-guru/update'); ?>" method="POST" enctype="multipart/form-data" class="grid-3">
                
                <!-- Kiri: Foto & Ringkasan -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <div class="card" style="padding: 2rem; text-align: center;">
                        <div style="width: 150px; height: 150px; border-radius: 50%; border: 4px solid #f0fdf4; margin: 0 auto 1.5rem; background: #e2e8f0; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                            <?php if($guru['foto']): ?>
                                <img src="<?php echo base_url('uploads/guru/' . $guru['foto']); ?>" style="width:100%; height:100%; object-fit:cover;">
                            <?php else: ?>
                                <i data-lucide="user" style="width: 64px; height: 64px; color: #94a3b8;"></i>
                            <?php endif; ?>
                        </div>
                        <h3 style="font-size: 1.2rem; margin-bottom: 5px;"><?php echo $guru['nama']; ?></h3>
                        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;"><?php echo $guru['jabatan']; ?></p>
                        
                        <label class="btn" style="background: #f1f5f9; color: #475569; padding: 8px 16px; font-size: 0.85rem; width: 100%; justify-content: center;">
                            <i data-lucide="camera" style="width: 16px;"></i> Ganti Foto
                            <input type="file" name="foto" style="display: none;" accept="image/*">
                        </label>
                    </div>

                    <div class="card" style="padding: 1.5rem; background: #0f172a; color: white;">
                        <h4 style="margin-bottom: 1rem; color: #cbd5e1; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;"><i data-lucide="shield" style="width: 14px; display: inline-block; vertical-align: -2px;"></i> Info Akun</h4>
                        <div style="margin-bottom: 10px;">
                            <div style="font-size: 0.75rem; color: #94a3b8;">Username</div>
                            <div style="font-weight: 600; font-size: 1rem; letter-spacing: 0.5px;"><?php echo $guru['username']; ?></div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: #94a3b8;">Password (Default)</div>
                            <div style="font-weight: 600; font-size: 1rem; letter-spacing: 0.5px; color: #38bdf8;"><?php echo $guru['plain_password']; ?></div>
                        </div>
                    </div>
                </div>

                <!-- Kanan: Area Form -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <!-- Biodata Diri -->
                    <div class="card" style="padding: 2rem;">
                        <h3 style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 8px; color: var(--primary);">
                            <i data-lucide="contact" style="width: 20px;"></i> Biodata Diri
                        </h3>
                        
                        <div class="grid-2">
                            <div>
                                <label class="form-label">Nama Lengkap & Gelar</label>
                                <input type="text" name="nama" class="form-control" value="<?php echo htmlspecialchars($guru['nama']); ?>" required>
                            </div>
                            <div>
                                <label class="form-label">NIP (Nomor Induk Pegawai)</label>
                                <input type="text" name="nip" class="form-control" value="<?php echo $guru['nip']; ?>">
                            </div>
                        </div>

                        <div class="grid-2">
                            <div>
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" value="<?php echo htmlspecialchars($guru['tempat_lahir']); ?>">
                            </div>
                            <div>
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control" value="<?php echo $guru['tanggal_lahir']; ?>">
                            </div>
                        </div>

                        <div class="grid-2">
                            <div>
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-control">
                                    <option value="L" <?php echo $guru['jenis_kelamin'] == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="P" <?php echo $guru['jenis_kelamin'] == 'P' ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">NIK (Nomor Induk Kependudukan)</label>
                                <input type="text" name="nik" class="form-control" value="<?php echo htmlspecialchars($guru['nik']); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Kontak & Domisili -->
                    <div class="card" style="padding: 2rem;">
                        <h3 style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 8px; color: var(--primary);">
                            <i data-lucide="map-pin" style="width: 20px;"></i> Kontak & Domisili
                        </h3>
                        
                        <div class="grid-2">
                            <div>
                                <label class="form-label">Nomor HP / WhatsApp</label>
                                <input type="text" name="no_hp" class="form-control" value="<?php echo htmlspecialchars($guru['no_hp']); ?>">
                            </div>
                            <div>
                                <label class="form-label">Email Aktif</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($guru['email']); ?>">
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 1.5rem;">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="3"><?php echo htmlspecialchars($guru['alamat']); ?></textarea>
                        </div>
                        
                        <div class="grid-2">
                            <div>
                                <label class="form-label">RT</label>
                                <input type="text" name="rt" class="form-control" value="<?php echo htmlspecialchars($guru['rt']); ?>">
                            </div>
                            <div>
                                <label class="form-label">RW</label>
                                <input type="text" name="rw" class="form-control" value="<?php echo htmlspecialchars($guru['rw']); ?>">
                            </div>
                        </div>
                        <div class="grid-2">
                            <div>
                                <label class="form-label">Desa / Kelurahan</label>
                                <input type="text" name="desa" class="form-control" value="<?php echo htmlspecialchars($guru['desa']); ?>">
                            </div>
                            <div>
                                <label class="form-label">Kecamatan</label>
                                <input type="text" name="kecamatan" class="form-control" value="<?php echo htmlspecialchars($guru['kecamatan']); ?>">
                            </div>
                        </div>
                        <div class="grid-2">
                            <div>
                                <label class="form-label">Kabupaten / Kota</label>
                                <input type="text" name="kota" class="form-control" value="<?php echo htmlspecialchars($guru['kota']); ?>">
                            </div>
                            <div>
                                <label class="form-label">Provinsi</label>
                                <input type="text" name="provinsi" class="form-control" value="<?php echo htmlspecialchars($guru['provinsi']); ?>">
                            </div>
                        </div>
                        <div class="grid-2">
                            <div>
                                <label class="form-label">Kode Pos</label>
                                <input type="text" name="kode_pos" class="form-control" value="<?php echo htmlspecialchars($guru['kode_pos']); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Kepegawaian & Sertifikasi -->
                    <div class="card" style="padding: 2rem;">
                        <h3 style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 8px; color: var(--primary);">
                            <i data-lucide="award" style="width: 20px;"></i> Kepegawaian & Sertifikasi
                        </h3>
                        
                        <div class="grid-2">
                            <div>
                                <label class="form-label">Jabatan (Preview)</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($guru['jabatan']); ?>" disabled style="background: #f8fafc; color: #94a3b8;">
                            </div>
                            <div>
                                <label class="form-label">Status Kepegawaian (Preview)</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($guru['status_kepegawaian']); ?>" disabled style="background: #f8fafc; color: #94a3b8;">
                            </div>
                        </div>

                        <div class="grid-2">
                            <div>
                                <label class="form-label">NPK</label>
                                <input type="text" name="npk" class="form-control" value="<?php echo htmlspecialchars($guru['npk']); ?>">
                            </div>
                            <div>
                                <label class="form-label">NPWP</label>
                                <input type="text" name="npwp" class="form-control" value="<?php echo htmlspecialchars($guru['npwp']); ?>">
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 1.5rem;">
                            <label class="form-label">Nomor Rekening</label>
                            <input type="text" name="no_rekening" class="form-control" value="<?php echo htmlspecialchars($guru['no_rekening']); ?>">
                        </div>

                        <div class="grid-2">
                            <div>
                                <label class="form-label">NRG</label>
                                <input type="text" name="nrg" class="form-control" value="<?php echo htmlspecialchars($guru['nrg']); ?>">
                            </div>
                            <div>
                                <label class="form-label">No Peserta Sertifikasi</label>
                                <input type="text" name="no_peserta_sertifikasi" class="form-control" value="<?php echo htmlspecialchars($guru['no_peserta_sertifikasi']); ?>">
                            </div>
                        </div>

                        <div class="grid-2">
                            <div>
                                <label class="form-label">No Sertifikat</label>
                                <input type="text" name="no_sertifikat" class="form-control" value="<?php echo htmlspecialchars($guru['no_sertifikat']); ?>">
                            </div>
                            <div>
                                <label class="form-label">Tanggal Sertifikat</label>
                                <input type="date" name="tgl_sertifikat" class="form-control" value="<?php echo $guru['tgl_sertifikat']; ?>">
                            </div>
                        </div>

                        <div class="grid-2">
                            <div>
                                <label class="form-label">Jenjang Sertifikat</label>
                                <select name="jenjang_sertifikat" class="form-control">
                                    <option value="">- Pilih -</option>
                                    <option value="RA" <?php echo $guru['jenjang_sertifikat'] == 'RA' ? 'selected' : ''; ?>>RA</option>
                                    <option value="MI" <?php echo $guru['jenjang_sertifikat'] == 'MI' ? 'selected' : ''; ?>>MI</option>
                                    <option value="MTs" <?php echo $guru['jenjang_sertifikat'] == 'MTs' ? 'selected' : ''; ?>>MTs</option>
                                    <option value="MA" <?php echo $guru['jenjang_sertifikat'] == 'MA' ? 'selected' : ''; ?>>MA</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Mapel Sertifikat</label>
                                <input type="text" name="mapel_sertifikat" class="form-control" value="<?php echo htmlspecialchars($guru['mapel_sertifikat']); ?>">
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

            <!-- Daftar Mengajar Section -->
            <div class="card" style="margin-top: 2rem; padding: 2rem;">
                <h3 style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 8px; color: var(--primary);">
                    <i data-lucide="book-open"></i> Daftar Mengajar Anda
                </h3>
                

                <?php if (empty($daftarMengajar)): ?>
                    <div style="text-align: center; padding: 3rem; background: #f8fafc; border-radius: 12px; border: 2px dashed #e2e8f0;">
                        <i data-lucide="calendar-off" style="width: 48px; height: 48px; color: #cbd5e1; margin-bottom: 1rem;"></i>
                        <p style="color: #64748b; font-weight: 500;">Belum ada jadwal mengajar yang terdaftar untuk Anda.</p>
                        <p style="font-size: 0.8rem; color: #94a3b8; margin-top: 5px;">Silakan hubungi bagian kurikulum untuk sinkronisasi jadwal.</p>
                    </div>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f1f5f9; text-align: left; border-bottom: 2px solid #e2e8f0;">
                                    <th style="padding: 1rem; border-top-left-radius: 8px; border-bottom-left-radius: 8px;">No</th>
                                    <th style="padding: 1rem;">Mata Pelajaran</th>
                                    <th style="padding: 1rem;">Kode</th>
                                    <th style="padding: 1rem;">Kelas / Rombel</th>
                                    <th style="padding: 1rem; border-top-right-radius: 8px; border-bottom-right-radius: 8px;">Tahun Pelajaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($daftarMengajar as $dm): ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9; transition: 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                                        <td style="padding: 1rem; color: var(--text-muted); font-weight: 600;"><?php echo $no++; ?></td>
                                        <td style="padding: 1rem;">
                                            <div style="font-weight: 700; color: #1e293b;"><?php echo $dm['nama_mapel']; ?></div>
                                        </td>
                                        <td style="padding: 1rem;">
                                            <span style="background: #f1f5f9; color: #475569; padding: 4px 8px; border-radius: 6px; font-family: monospace; font-size: 0.85rem; font-weight: 600; border: 1px solid #e2e8f0;">
                                                <?php echo $dm['kode_mapel']; ?>
                                            </span>
                                        </td>
                                        <td style="padding: 1rem;">
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--primary);"></div>
                                                <span style="font-weight: 600; color: #334155;"><?php echo $dm['nama_kelas']; ?></span>
                                            </div>
                                        </td>
                                        <td style="padding: 1rem;">
                                            <span style="background: #fdf4ff; color: #a21caf; padding: 4px 8px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; border: 1px solid #fbcfe8;">
                                                <?php echo $dm['tahun_pelajaran']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div><!-- /z-scroll -->
    </div><!-- /z-main -->
</div><!-- /siakad-container -->

<?php include __DIR__ . '/footer_multi_guru.php'; ?>
