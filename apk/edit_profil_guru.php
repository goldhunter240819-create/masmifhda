<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];

// Proses Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $nip = trim($_POST['nip'] ?? '');
    $nik = trim($_POST['nik'] ?? '');
    $tempat_lahir = trim($_POST['tempat_lahir'] ?? '');
    $tanggal_lahir = trim($_POST['tanggal_lahir'] ?? null);
    $jenis_kelamin = trim($_POST['jenis_kelamin'] ?? 'L');
    $provinsi = trim($_POST['provinsi'] ?? '');
    $kota = trim($_POST['kota'] ?? '');
    $kecamatan = trim($_POST['kecamatan'] ?? '');
    $desa = trim($_POST['desa'] ?? '');
    $rt = trim($_POST['rt'] ?? '');
    $rw = trim($_POST['rw'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $npk = trim($_POST['npk'] ?? '');
    $npwp = trim($_POST['npwp'] ?? '');
    $no_rekening = trim($_POST['no_rekening'] ?? '');
    $kode_pos = trim($_POST['kode_pos'] ?? '');
    $nrg = trim($_POST['nrg'] ?? '');
    $no_peserta_sertifikasi = trim($_POST['no_peserta_sertifikasi'] ?? '');
    $no_sertifikat = trim($_POST['no_sertifikat'] ?? '');
    $tgl_sertifikat = trim($_POST['tgl_sertifikat'] ?? null);
    $jenjang_sertifikat = trim($_POST['jenjang_sertifikat'] ?? '');
    $mapel_sertifikat = trim($_POST['mapel_sertifikat'] ?? '');

    try {
        $stmt = $core_db->prepare("UPDATE guru SET 
            nama = :nama,
            nip = :nip,
            nik = :nik,
            tempat_lahir = :tempat_lahir,
            tanggal_lahir = :tanggal_lahir,
            jenis_kelamin = :jenis_kelamin,
            provinsi = :provinsi,
            kota = :kota,
            kecamatan = :kecamatan,
            desa = :desa,
            rt = :rt,
            rw = :rw,
            alamat = :alamat,
            no_hp = :no_hp,
            email = :email,
            npk = :npk,
            npwp = :npwp,
            no_rekening = :no_rekening,
            kode_pos = :kode_pos,
            nrg = :nrg,
            no_peserta_sertifikasi = :no_peserta_sertifikasi,
            no_sertifikat = :no_sertifikat,
            tgl_sertifikat = :tgl_sertifikat,
            jenjang_sertifikat = :jenjang_sertifikat,
            mapel_sertifikat = :mapel_sertifikat
            WHERE id = :id");
            
        $stmt->execute([
            'nama' => $nama,
            'nip' => $nip,
            'nik' => $nik,
            'tempat_lahir' => $tempat_lahir,
            'tanggal_lahir' => $tanggal_lahir ?: null,
            'jenis_kelamin' => $jenis_kelamin,
            'provinsi' => $provinsi,
            'kota' => $kota,
            'kecamatan' => $kecamatan,
            'desa' => $desa,
            'rt' => $rt,
            'rw' => $rw,
            'alamat' => $alamat,
            'no_hp' => $no_hp,
            'email' => $email,
            'npk' => $npk,
            'npwp' => $npwp,
            'no_rekening' => $no_rekening,
            'kode_pos' => $kode_pos,
            'nrg' => $nrg,
            'no_peserta_sertifikasi' => $no_peserta_sertifikasi,
            'no_sertifikat' => $no_sertifikat,
            'tgl_sertifikat' => $tgl_sertifikat ?: null,
            'jenjang_sertifikat' => $jenjang_sertifikat ?: null,
            'mapel_sertifikat' => $mapel_sertifikat,
            'id' => $guru_id
        ]);

        // Sync ke sistem lain
        if (file_exists('../app/Services/DataSyncService.php')) {
            require_once '../vendor/autoload.php';
            require_once '../app/Core/Database.php';
            \App\Services\DataSyncService::runGuruSync();
        }
        
        // Handle Foto Upload
        if (!empty($_FILES['foto']['name'])) {
            $file = $_FILES['foto'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($ext, $allowed) && $file['size'] <= 5000000) {
                $upload_dir = '../public/uploads/guru/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                $filename = 'guru_' . time() . '_' . rand(100,999) . '.' . $ext;
                
                $source = $file['tmp_name'];
                $destination = $upload_dir . $filename;
                $info = @getimagesize($source);
                $uploaded = false;
                
                if ($info) {
                    if ($info['mime'] == 'image/jpeg') $img = @imagecreatefromjpeg($source);
                    elseif ($info['mime'] == 'image/png') $img = @imagecreatefrompng($source);
                    elseif ($info['mime'] == 'image/webp') $img = @imagecreatefromwebp($source);
                    else $img = false;

                    if ($img) {
                        $width = imagesx($img);
                        $height = imagesy($img);
                        $maxWidth = 400; // kompres ukuran maksimal 400px
                        
                        if ($width > $maxWidth) {
                            $newWidth = $maxWidth;
                            $newHeight = floor($height * ($maxWidth / $width));
                            $tmp = imagecreatetruecolor($newWidth, $newHeight);
                            if ($info['mime'] == 'image/png') {
                                imagealphablending($tmp, false);
                                imagesavealpha($tmp, true);
                            }
                            imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                            imagedestroy($img);
                            $img = $tmp;
                        }

                        if ($info['mime'] == 'image/jpeg') $uploaded = imagejpeg($img, $destination, 70);
                        elseif ($info['mime'] == 'image/png') $uploaded = imagepng($img, $destination, 7);
                        elseif ($info['mime'] == 'image/webp') $uploaded = imagewebp($img, $destination, 70);
                        
                        imagedestroy($img);
                    } else {
                        $uploaded = move_uploaded_file($source, $destination);
                    }
                } else {
                    $uploaded = move_uploaded_file($source, $destination);
                }
                
                if ($uploaded) {
                    // hapus foto lama
                    $stmt_old = $core_db->prepare("SELECT foto FROM guru WHERE id = ?");
                    $stmt_old->execute([$guru_id]);
                    $old = $stmt_old->fetchColumn();
                    if ($old && file_exists($upload_dir . $old)) @unlink($upload_dir . $old);
                    
                    // update db
                    $stmt_foto = $core_db->prepare("UPDATE guru SET foto = ? WHERE id = ?");
                    $stmt_foto->execute([$filename, $guru_id]);
                }
            }
        }

        $success_msg = "Profil berhasil diperbarui!";
    } catch (PDOException $e) {
        $error_msg = "Gagal memperbarui profil: " . $e->getMessage();
    }
}

// Ambil data guru lengkap
$stmt = $core_db->prepare("SELECT * FROM guru WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $guru_id]);
$guru = $stmt->fetch(PDO::FETCH_ASSOC);

if (isset($success_msg)) {
    require_once __DIR__ . '/../vendor/autoload.php';
    if (class_exists('\App\Services\AuditLogger')) {
        \App\Services\AuditLogger::dataChange('Guru', 'UPDATE', "id={$guru_id}, nama={$guru['nama']} (APK)");
    }
}

$page_title = 'Edit Profil Guru';
include 'header.php';
?>

<div class="header">
    <div class="header-top">
        <a href="profil_guru.php" style="color: white; font-size: 1.5rem; text-decoration: none;"><i class="fa-solid fa-arrow-left"></i></a>
        <div class="greeting" style="flex: 1; text-align: center; padding-right: 24px;">
            <h1 style="margin: 0; font-size: 1.2rem;">Edit Profil</h1>
        </div>
    </div>
</div>

<div class="main-content" style="padding-bottom: 80px;">
    <?php if (isset($success_msg)): ?>
        <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-size: 0.9rem; border-left: 4px solid #22c55e;">
            <i class="fa-solid fa-circle-check"></i> <?= $success_msg ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error_msg)): ?>
        <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-size: 0.9rem; border-left: 4px solid #ef4444;">
            <i class="fa-solid fa-circle-exclamation"></i> <?= $error_msg ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <div class="data-card" style="margin-top: -1.5rem; z-index: 10;">
            <div style="text-align: center; margin-bottom: 20px;">
                <style>
                #foto_upload::file-selector-button {
                    background: linear-gradient(135deg, #0ea5e9, #0284c7);
                    color: white;
                    padding: 8px 16px;
                    border-radius: 25px;
                    border: none;
                    font-size: 0.85rem;
                    font-weight: 600;
                    cursor: pointer;
                    margin-right: 15px;
                    box-shadow: 0 3px 10px rgba(14,165,233,0.3);
                    transition: transform 0.2s, box-shadow 0.2s;
                }
                #foto_upload::file-selector-button:active {
                    transform: scale(0.95);
                    box-shadow: 0 2px 5px rgba(14,165,233,0.4);
                }
                </style>
                <div id="foto_container" style="width: 80px; height: 80px; background: #e0f2fe; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: #0ea5e9; margin: 0 auto 10px; overflow: hidden; border: 3px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); position: relative;">
                    <?php if(!empty($guru['foto'])): ?>
                        <img id="preview_foto" src="/public/uploads/guru/<?= htmlspecialchars($guru['foto']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <i id="preview_icon" class="fa-solid fa-chalkboard-user"></i>
                        <img id="preview_foto" src="" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                    <?php endif; ?>
                </div>
                
                <div style="text-align: left; margin-top: 15px;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 5px; color: #475569;">Ganti Foto Profil</label>
                    <input type="file" name="foto" id="foto_upload" accept="image/*" onchange="previewImage(this)" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.85rem; color: #64748b; background: white; cursor: pointer;">
                </div>
            </div>
            
            <h3 style="font-size: 1rem; color: var(--primary); margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
                <i class="fa-solid fa-address-card"></i> Data Pokok
            </h3>
            
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Nama Lengkap</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($guru['nama']) ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
            </div>
            
            <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">NIP</label>
                    <input type="text" name="nip" value="<?= htmlspecialchars($guru['nip'] ?: '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">NIK</label>
                    <input type="text" name="nik" value="<?= htmlspecialchars($guru['nik'] ?: '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
            </div>
        </div>

        <div class="data-card" style="margin-top: 15px;">
            <h3 style="font-size: 1rem; color: var(--primary); margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
                <i class="fa-solid fa-user-pen"></i> Data Pribadi
            </h3>
            
            <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="<?= htmlspecialchars($guru['tempat_lahir'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="<?= htmlspecialchars($guru['tanggal_lahir'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Jenis Kelamin</label>
                    <select name="jenis_kelamin" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif; background: white;">
                        <option value="L" <?= ($guru['jenis_kelamin'] == 'L') ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= ($guru['jenis_kelamin'] == 'P') ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">No. HP/WA</label>
                    <input type="tel" name="no_hp" value="<?= htmlspecialchars($guru['no_hp'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
            </div>

            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($guru['email'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Provinsi</label>
                    <input type="hidden" name="provinsi" id="val_prov" value="<?= htmlspecialchars($guru['provinsi'] ?? '') ?>">
                    <select id="sel_prov" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif; background: white;">
                        <option value=""><?= htmlspecialchars($guru['provinsi'] ?: '-- Pilih Provinsi --') ?></option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Kab/Kota</label>
                    <input type="hidden" name="kota" id="val_kota" value="<?= htmlspecialchars($guru['kota'] ?? '') ?>">
                    <select id="sel_kota" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif; background: white;" disabled>
                        <option value=""><?= htmlspecialchars($guru['kota'] ?: '-- Pilih Kab/Kota --') ?></option>
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Kecamatan</label>
                    <input type="hidden" name="kecamatan" id="val_kec" value="<?= htmlspecialchars($guru['kecamatan'] ?? '') ?>">
                    <select id="sel_kec" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif; background: white;" disabled>
                        <option value=""><?= htmlspecialchars($guru['kecamatan'] ?: '-- Pilih Kecamatan --') ?></option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Kelurahan/Desa</label>
                    <input type="hidden" name="desa" id="val_desa" value="<?= htmlspecialchars($guru['desa'] ?? '') ?>">
                    <select id="sel_desa" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif; background: white;" disabled>
                        <option value=""><?= htmlspecialchars($guru['desa'] ?: '-- Pilih Kelurahan --') ?></option>
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">RT</label>
                    <input type="text" name="rt" value="<?= htmlspecialchars($guru['rt'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">RW</label>
                    <input type="text" name="rw" value="<?= htmlspecialchars($guru['rw'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
            </div>

            <div class="display: flex; gap: 10px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Kode Pos</label>
                    <input type="text" name="kode_pos" value="<?= htmlspecialchars($guru['kode_pos'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
            </div>

            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Nama Jalan / Dusun</label>
                <textarea name="alamat" rows="2" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif; resize: vertical;" placeholder="Cth: Jl. Sudirman No. 12"><?= htmlspecialchars($guru['alamat'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="data-card" style="margin-top: 15px;">
            <h3 style="font-size: 1rem; color: var(--primary); margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
                <i class="fa-solid fa-briefcase"></i> Data Kepegawaian & Rekening
            </h3>
            
            <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">NPK</label>
                    <input type="text" name="npk" value="<?= htmlspecialchars($guru['npk'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">NPWP</label>
                    <input type="text" name="npwp" value="<?= htmlspecialchars($guru['npwp'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
            </div>

            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Nomor Rekening</label>
                <input type="text" name="no_rekening" value="<?= htmlspecialchars($guru['no_rekening'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
            </div>
        </div>

        <div class="data-card" style="margin-top: 15px;">
            <h3 style="font-size: 1rem; color: var(--primary); margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
                <i class="fa-solid fa-award"></i> Data Sertifikasi
            </h3>
            
            <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">NRG</label>
                    <input type="text" name="nrg" value="<?= htmlspecialchars($guru['nrg'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">No Peserta Sertifikasi</label>
                    <input type="text" name="no_peserta_sertifikasi" value="<?= htmlspecialchars($guru['no_peserta_sertifikasi'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">No Sertifikat</label>
                    <input type="text" name="no_sertifikat" value="<?= htmlspecialchars($guru['no_sertifikat'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Tgl TTD Sertifikat</label>
                    <input type="date" name="tgl_sertifikat" value="<?= htmlspecialchars($guru['tgl_sertifikat'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Jenjang Sertifikat</label>
                    <select name="jenjang_sertifikat" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif; background: white;">
                        <option value="" <?= empty($guru['jenjang_sertifikat']) ? 'selected' : '' ?>>- Pilih -</option>
                        <option value="RA" <?= (isset($guru['jenjang_sertifikat']) && $guru['jenjang_sertifikat'] == 'RA') ? 'selected' : '' ?>>RA</option>
                        <option value="MI" <?= (isset($guru['jenjang_sertifikat']) && $guru['jenjang_sertifikat'] == 'MI') ? 'selected' : '' ?>>MI</option>
                        <option value="MTs" <?= (isset($guru['jenjang_sertifikat']) && $guru['jenjang_sertifikat'] == 'MTs') ? 'selected' : '' ?>>MTs</option>
                        <option value="MA" <?= (isset($guru['jenjang_sertifikat']) && $guru['jenjang_sertifikat'] == 'MA') ? 'selected' : '' ?>>MA</option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Mapel Sertifikat</label>
                    <input type="text" name="mapel_sertifikat" value="<?= htmlspecialchars($guru['mapel_sertifikat'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
            </div>
        </div>

        <button type="submit" style="width: 100%; background: var(--primary); color: white; border: none; padding: 15px; border-radius: var(--border-radius); font-size: 1rem; font-weight: 600; font-family: 'Outfit', sans-serif; margin-top: 20px; margin-bottom: 80px; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3); cursor: pointer;">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
        </button>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const selProv = document.getElementById('sel_prov');
    const selKota = document.getElementById('sel_kota');
    const selKec = document.getElementById('sel_kec');
    const selDesa = document.getElementById('sel_desa');
    
    const valProv = document.getElementById('val_prov');
    const valKota = document.getElementById('val_kota');
    const valKec = document.getElementById('val_kec');
    const valDesa = document.getElementById('val_desa');

    // Fetch Provinces
    fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json`)
        .then(response => response.json())
        .then(provinces => {
            let options = '<option value="">-- Pilih Provinsi --</option>';
            provinces.forEach(p => {
                options += `<option value="${p.id}" data-name="${p.name}">${p.name}</option>`;
            });
            selProv.innerHTML = options;
        });

    selProv.addEventListener('change', function() {
        selKota.innerHTML = '<option value="">-- Pilih Kab/Kota --</option>';
        selKec.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        selDesa.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
        selKota.disabled = true;
        selKec.disabled = true;
        selDesa.disabled = true;
        
        let selected = this.options[this.selectedIndex];
        valProv.value = selected.getAttribute('data-name') || '';

        if(this.value) {
            selKota.disabled = false;
            fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${this.value}.json`)
                .then(response => response.json())
                .then(regencies => {
                    let options = '<option value="">-- Pilih Kab/Kota --</option>';
                    regencies.forEach(r => {
                        options += `<option value="${r.id}" data-name="${r.name}">${r.name}</option>`;
                    });
                    selKota.innerHTML = options;
                });
        } else {
            valKota.value = ''; valKec.value = ''; valDesa.value = '';
        }
    });

    selKota.addEventListener('change', function() {
        selKec.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        selDesa.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
        selKec.disabled = true;
        selDesa.disabled = true;

        let selected = this.options[this.selectedIndex];
        valKota.value = selected.getAttribute('data-name') || '';

        if(this.value) {
            selKec.disabled = false;
            fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${this.value}.json`)
                .then(response => response.json())
                .then(districts => {
                    let options = '<option value="">-- Pilih Kecamatan --</option>';
                    districts.forEach(d => {
                        options += `<option value="${d.id}" data-name="${d.name}">${d.name}</option>`;
                    });
                    selKec.innerHTML = options;
                });
        } else {
            valKec.value = ''; valDesa.value = '';
        }
    });

    selKec.addEventListener('change', function() {
        selDesa.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
        selDesa.disabled = true;

        let selected = this.options[this.selectedIndex];
        valKec.value = selected.getAttribute('data-name') || '';

        if(this.value) {
            selDesa.disabled = false;
            fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${this.value}.json`)
                .then(response => response.json())
                .then(villages => {
                    let options = '<option value="">-- Pilih Kelurahan --</option>';
                    villages.forEach(v => {
                        options += `<option value="${v.id}" data-name="${v.name}">${v.name}</option>`;
                    });
                    selDesa.innerHTML = options;
                });
        } else {
            valDesa.value = '';
        }
    });

    selDesa.addEventListener('change', function() {
        let selected = this.options[this.selectedIndex];
        valDesa.value = selected.getAttribute('data-name') || '';
    });
});

function previewImage(input) {
    if (input.files && input.files[0]) {
        var file = input.files[0];
        
        // Validasi ukuran (max 5MB)
        if (file.size > 5000000) {
            alert('Ukuran foto maksimal 5MB!');
            input.value = '';
            return;
        }
        
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview_foto').src = e.target.result;
            document.getElementById('preview_foto').style.display = 'block';
            if (document.getElementById('preview_icon')) {
                document.getElementById('preview_icon').style.display = 'none';
            }
        }
        reader.readAsDataURL(file);
        
        // Tampilkan nama file
        var fname = file.name.length > 25 ? file.name.substring(0, 22) + '...' : file.name;
        document.getElementById('foto_name_text').textContent = fname;
        document.getElementById('foto_filename').style.display = 'block';
    }
}
</script>

<?php include 'footer_guru.php'; ?>
