<?php
require_once 'koneksi.php';

if (!isset($_SESSION['siswa_id'])) {
    header("Location: login.php");
    exit;
}

$siswa_id = $_SESSION['siswa_id'];

// Proses Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $nis = trim($_POST['nis'] ?? '');
    $nisn = trim($_POST['nisn'] ?? '');
    $tempat_lahir = trim($_POST['tempat_lahir'] ?? '');
    $tanggal_lahir = trim($_POST['tanggal_lahir'] ?? null);
    $jenis_kelamin = trim($_POST['jenis_kelamin'] ?? 'L');
    $agama = trim($_POST['agama'] ?? '');
    $provinsi = trim($_POST['provinsi'] ?? '');
    $kota = trim($_POST['kota'] ?? '');
    $kecamatan = trim($_POST['kecamatan'] ?? '');
    $desa = trim($_POST['desa'] ?? '');
    $rt = trim($_POST['rt'] ?? '');
    $rw = trim($_POST['rw'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $nama_ayah = trim($_POST['nama_ayah'] ?? '');
    $pekerjaan_ayah = trim($_POST['pekerjaan_ayah'] ?? '');
    $nama_ibu = trim($_POST['nama_ibu'] ?? '');
    $pekerjaan_ibu = trim($_POST['pekerjaan_ibu'] ?? '');
    $no_hp_ortu = trim($_POST['no_hp_ortu'] ?? '');

    try {
        $stmt = $core_db->prepare("UPDATE siswa SET 
            nama = :nama,
            nis = :nis,
            nisn = :nisn,
            tempat_lahir = :tempat_lahir,
            tanggal_lahir = :tanggal_lahir,
            jenis_kelamin = :jenis_kelamin,
            agama = :agama,
            provinsi = :provinsi,
            kota = :kota,
            kecamatan = :kecamatan,
            desa = :desa,
            rt = :rt,
            rw = :rw,
            alamat = :alamat,
            kode_pos = :kode_pos,
            nama_ayah = :nama_ayah,
            pekerjaan_ayah = :pekerjaan_ayah,
            nama_ibu = :nama_ibu,
            pekerjaan_ibu = :pekerjaan_ibu,
            penghasilan_ortu = :penghasilan_ortu,
            no_hp_ortu = :no_hp_ortu,
            nama_wali = :nama_wali,
            pekerjaan_wali = :pekerjaan_wali,
            no_hp_wali = :no_hp_wali,
            nik = :nik,
            no_kk = :no_kk,
            gol_darah = :gol_darah,
            anak_ke = :anak_ke,
            kewarganegaraan = :kewarganegaraan,
            sekolah_asal = :sekolah_asal,
            pendidikan_terakhir = :pendidikan_terakhir,
            no_ijazah = :no_ijazah
            WHERE id = :id");
            
        $stmt->execute([
            'nama' => $nama,
            'nis' => $nis,
            'nisn' => $nisn,
            'tempat_lahir' => $tempat_lahir,
            'tanggal_lahir' => $tanggal_lahir ?: null,
            'jenis_kelamin' => $jenis_kelamin,
            'agama' => $agama,
            'provinsi' => $provinsi,
            'kota' => $kota,
            'kecamatan' => $kecamatan,
            'desa' => $desa,
            'rt' => $rt,
            'rw' => $rw,
            'alamat' => $alamat,
            'kode_pos' => trim($_POST['kode_pos'] ?? ''),
            'nama_ayah' => $nama_ayah,
            'pekerjaan_ayah' => $pekerjaan_ayah,
            'nama_ibu' => $nama_ibu,
            'pekerjaan_ibu' => $pekerjaan_ibu,
            'penghasilan_ortu' => trim($_POST['penghasilan_ortu'] ?? ''),
            'no_hp_ortu' => $no_hp_ortu,
            'nama_wali' => trim($_POST['nama_wali'] ?? ''),
            'pekerjaan_wali' => trim($_POST['pekerjaan_wali'] ?? ''),
            'no_hp_wali' => trim($_POST['no_hp_wali'] ?? ''),
            'nik' => trim($_POST['nik'] ?? ''),
            'no_kk' => trim($_POST['no_kk'] ?? ''),
            'gol_darah' => trim($_POST['gol_darah'] ?? ''),
            'anak_ke' => trim($_POST['anak_ke'] ?? ''),
            'kewarganegaraan' => trim($_POST['kewarganegaraan'] ?? 'WNI'),
            'sekolah_asal' => trim($_POST['sekolah_asal'] ?? ''),
            'pendidikan_terakhir' => trim($_POST['pendidikan_terakhir'] ?? ''),
            'no_ijazah' => trim($_POST['no_ijazah'] ?? ''),
            'id' => $siswa_id
        ]);

        // Sync ke sistem lain
        if (file_exists('../app/Services/DataSyncService.php')) {
            require_once '../vendor/autoload.php';
            require_once '../app/Core/Database.php';
            // get user_id from siswa table to sync
            $stmt_u = $core_db->prepare("SELECT user_id FROM siswa WHERE id = :id LIMIT 1");
            $stmt_u->execute(['id' => $siswa_id]);
            $s_user = $stmt_u->fetch(PDO::FETCH_ASSOC);
            if ($s_user && !empty($s_user['user_id'])) {
                \App\Services\DataSyncService::runSiswaSync($s_user['user_id']);
            }
        }
        
        // Handle Foto Upload
        if (!empty($_FILES['foto']['name'])) {
            $file = $_FILES['foto'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($ext, $allowed) && $file['size'] <= 5000000) {
                $upload_dir = '../public/uploads/siswa/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                $filename = 'siswa_' . time() . '_' . rand(100,999) . '.' . $ext;
                
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
                    $stmt_old = $core_db->prepare("SELECT foto FROM siswa WHERE id = ?");
                    $stmt_old->execute([$siswa_id]);
                    $old = $stmt_old->fetchColumn();
                    if ($old && file_exists($upload_dir . $old)) @unlink($upload_dir . $old);
                    
                    // update db
                    $stmt_foto = $core_db->prepare("UPDATE siswa SET foto = ? WHERE id = ?");
                    $stmt_foto->execute([$filename, $siswa_id]);
                }
            }
        }
        
        $success_msg = "Profil berhasil diperbarui!";
    } catch (PDOException $e) {
        $error_msg = "Gagal memperbarui profil: " . $e->getMessage();
    }
}

// Ambil data siswa lengkap
$stmt = $core_db->prepare("SELECT * FROM siswa WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $siswa_id]);
$siswa = $stmt->fetch(PDO::FETCH_ASSOC);

if (isset($success_msg)) {
    require_once __DIR__ . '/../vendor/autoload.php';
    if (class_exists('\App\Services\AuditLogger')) {
        \App\Services\AuditLogger::dataChange('Siswa', 'UPDATE', "id={$siswa_id}, nama={$siswa['nama']} (APK)");
    }
}

$page_title = 'Edit Profil';
include 'header.php';
?>

<div class="header">
    <div class="header-top">
        <a href="profil.php" style="color: white; font-size: 1.5rem; text-decoration: none;"><i class="fa-solid fa-arrow-left"></i></a>
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
                <div style="width: 80px; height: 80px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: var(--text-muted); margin: 0 auto 10px; overflow: hidden; border: 3px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); position: relative;">
                    <?php if(!empty($siswa['foto'])): ?>
                        <img id="preview_foto" src="/public/uploads/siswa/<?= htmlspecialchars($siswa['foto']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <i id="preview_icon" class="fa-solid fa-user"></i>
                        <img id="preview_foto" src="" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                    <?php endif; ?>
                </div>
                
                <div style="text-align: center; margin-top: 15px;">
                    <p style="font-size: 0.8rem; color: #ef4444; font-weight: 500; font-style: italic; background: #fee2e2; padding: 6px 12px; border-radius: 6px; display: inline-block; border: 1px solid #fca5a5;">
                        <i class="fa-solid fa-lock"></i> Hubungi admin sekolah untuk mengubah foto profil
                    </p>
                </div>
            </div>
            
            <h3 style="font-size: 1rem; color: var(--primary); margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
                <i class="fa-solid fa-address-card"></i> Data Pokok
            </h3>
            
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Nama Lengkap</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($siswa['nama']) ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
            </div>
            
            <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">NIS <i class="fa-solid fa-lock" style="font-size: 0.7rem; color: #ef4444;"></i></label>
                    <input type="text" name="nis" value="<?= htmlspecialchars($siswa['nis']) ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif; background: #f1f5f9; cursor: not-allowed; color: #64748b;" readonly>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">NISN <i class="fa-solid fa-lock" style="font-size: 0.7rem; color: #ef4444;"></i></label>
                    <input type="text" name="nisn" value="<?= htmlspecialchars($siswa['nisn'] ?: '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif; background: #f1f5f9; cursor: not-allowed; color: #64748b;" readonly>
                </div>
            </div>
            
            <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">NIK (16 Digit) <i class="fa-solid fa-lock" style="font-size: 0.7rem; color: #ef4444;"></i></label>
                    <input type="text" name="nik" value="<?= htmlspecialchars($siswa['nik'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif; background: #f1f5f9; cursor: not-allowed; color: #64748b;" readonly>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">No. KK</label>
                    <input type="text" name="no_kk" value="<?= htmlspecialchars($siswa['no_kk'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
            </div>
            <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 5px; font-style: italic;">* Hubungi Admin Sekolah jika terdapat kesalahan pada data pokok.</p>
        </div>

        <div class="data-card" style="margin-top: 15px;">
            <h3 style="font-size: 1rem; color: var(--primary); margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
                <i class="fa-solid fa-user-pen"></i> Data Pribadi
            </h3>
            
            <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="<?= htmlspecialchars($siswa['tempat_lahir'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="<?= htmlspecialchars($siswa['tanggal_lahir'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Jenis Kelamin</label>
                    <select name="jenis_kelamin" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif; background: white;">
                        <option value="L" <?= ($siswa['jenis_kelamin'] == 'L') ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= ($siswa['jenis_kelamin'] == 'P') ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Agama</label>
                    <select name="agama" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif; background: white;">
                        <?php 
                        $agama_list = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
                        foreach($agama_list as $a): 
                        ?>
                            <option value="<?= $a ?>" <?= ($siswa['agama'] == $a) ? 'selected' : '' ?>><?= $a ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Golongan Darah</label>
                    <select name="gol_darah" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif; background: white;">
                        <option value="">- Pilih -</option>
                        <?php foreach(['A','B','AB','O'] as $gd): ?>
                            <option value="<?= $gd ?>" <?= ($siswa['gol_darah'] == $gd) ? 'selected' : '' ?>><?= $gd ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Anak Ke-</label>
                    <input type="number" name="anak_ke" value="<?= htmlspecialchars($siswa['anak_ke'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;" min="1">
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Kewarganegaraan</label>
                    <input type="text" name="kewarganegaraan" value="<?= htmlspecialchars($siswa['kewarganegaraan'] ?? 'WNI') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Sekolah Asal</label>
                    <input type="text" name="sekolah_asal" value="<?= htmlspecialchars($siswa['sekolah_asal'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
            </div>
            <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Pendidikan Terakhir</label>
                    <input type="text" name="pendidikan_terakhir" value="<?= htmlspecialchars($siswa['pendidikan_terakhir'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">No. Ijazah</label>
                    <input type="text" name="no_ijazah" value="<?= htmlspecialchars($siswa['no_ijazah'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
            </div>
            <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Provinsi</label>
                    <input type="hidden" name="provinsi" id="val_prov" value="<?= htmlspecialchars($siswa['provinsi'] ?? '') ?>">
                    <select id="sel_prov" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif; background: white;">
                        <option value=""><?= htmlspecialchars($siswa['provinsi'] ?: '-- Pilih Provinsi --') ?></option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Kab/Kota</label>
                    <input type="hidden" name="kota" id="val_kota" value="<?= htmlspecialchars($siswa['kota'] ?? '') ?>">
                    <select id="sel_kota" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif; background: white;" disabled>
                        <option value=""><?= htmlspecialchars($siswa['kota'] ?: '-- Pilih Kab/Kota --') ?></option>
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Kecamatan</label>
                    <input type="hidden" name="kecamatan" id="val_kec" value="<?= htmlspecialchars($siswa['kecamatan'] ?? '') ?>">
                    <select id="sel_kec" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif; background: white;" disabled>
                        <option value=""><?= htmlspecialchars($siswa['kecamatan'] ?: '-- Pilih Kecamatan --') ?></option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Kelurahan/Desa</label>
                    <input type="hidden" name="desa" id="val_desa" value="<?= htmlspecialchars($siswa['desa'] ?? '') ?>">
                    <select id="sel_desa" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif; background: white;" disabled>
                        <option value=""><?= htmlspecialchars($siswa['desa'] ?: '-- Pilih Kelurahan --') ?></option>
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">RT</label>
                    <input type="text" name="rt" value="<?= htmlspecialchars($siswa['rt'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">RW</label>
                    <input type="text" name="rw" value="<?= htmlspecialchars($siswa['rw'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Kode Pos</label>
                    <input type="text" name="kode_pos" value="<?= htmlspecialchars($siswa['kode_pos'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
            </div>

            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Nama Jalan / Dusun</label>
                <textarea name="alamat" rows="2" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif; resize: vertical;" placeholder="Cth: Jl. Sudirman No. 12"><?= htmlspecialchars($siswa['alamat'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="data-card" style="margin-top: 15px;">
            <h3 style="font-size: 1rem; color: var(--primary); margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
                <i class="fa-solid fa-users"></i> Data Orang Tua
            </h3>
            
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Nama Ayah</label>
                <input type="text" name="nama_ayah" value="<?= htmlspecialchars($siswa['nama_ayah'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
            </div>
            
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Pekerjaan Ayah</label>
                <select name="pekerjaan_ayah" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif; background: white;">
                    <?php 
                    $pekerjaan_list = ['-', 'PNS', 'TNI/Polri', 'Guru/Dosen', 'Dokter/Tenaga Medis', 'Pegawai Swasta', 'Pegawai BUMN/BUMD', 'Wiraswasta / Pengusaha', 'Pedagang', 'Petani', 'Peternak', 'Nelayan', 'Buruh', 'Sopir/Driver', 'Pensiunan', 'Lainnya'];
                    foreach($pekerjaan_list as $p): 
                    ?>
                        <option value="<?= $p ?>" <?= ($siswa['pekerjaan_ayah'] == $p) ? 'selected' : '' ?>><?= $p ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Nama Ibu</label>
                <input type="text" name="nama_ibu" value="<?= htmlspecialchars($siswa['nama_ibu'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
            </div>

            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Pekerjaan Ibu</label>
                <select name="pekerjaan_ibu" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif; background: white;">
                    <?php 
                    $pekerjaan_ibu_list = ['-', 'Ibu Rumah Tangga', 'PNS', 'TNI/Polri', 'Guru/Dosen', 'Dokter/Tenaga Medis', 'Pegawai Swasta', 'Pegawai BUMN/BUMD', 'Wiraswasta / Pengusaha', 'Pedagang', 'Petani', 'Buruh', 'Pensiunan', 'Lainnya'];
                    foreach($pekerjaan_ibu_list as $p): 
                    ?>
                        <option value="<?= $p ?>" <?= ($siswa['pekerjaan_ibu'] == $p) ? 'selected' : '' ?>><?= $p ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Penghasilan Orang Tua</label>
                <input type="text" name="penghasilan_ortu" value="<?= htmlspecialchars($siswa['penghasilan_ortu'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;" placeholder="Cth: Rp 2.000.000">
            </div>

            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Nomor HP/WA Orang Tua</label>
                <input type="tel" name="no_hp_ortu" value="<?= htmlspecialchars($siswa['no_hp_ortu'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
            </div>

            <div style="margin-top: 25px; margin-bottom: 15px; border-top: 1px solid #e2e8f0; padding-top: 15px;">
                <h4 style="font-size: 0.95rem; color: var(--primary); margin-bottom: 10px;">Data Wali (Opsional)</h4>
            </div>

            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Nama Wali</label>
                <input type="text" name="nama_wali" value="<?= htmlspecialchars($siswa['nama_wali'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Pekerjaan Wali</label>
                    <input type="text" name="pekerjaan_wali" value="<?= htmlspecialchars($siswa['pekerjaan_wali'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">No HP Wali</label>
                    <input type="tel" name="no_hp_wali" value="<?= htmlspecialchars($siswa['no_hp_wali'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;">
                </div>
            </div>
        </div>

        <button type="submit" style="width: 100%; background: var(--primary); color: white; border: none; padding: 15px; border-radius: var(--border-radius); font-size: 1rem; font-weight: 600; font-family: 'Outfit', sans-serif; margin-top: 20px; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3); cursor: pointer;">
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

<?php include 'footer.php'; ?>
