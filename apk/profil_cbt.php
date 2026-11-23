<?php
require_once 'koneksi.php';

if (!isset($_SESSION['siswa_id']) || empty($_SESSION['is_cbt_only'])) {
    header("Location: login.php");
    exit;
}

$siswa_id = (int)$_SESSION['siswa_id'];
$msg_success = '';
$msg_error = '';

// Ambil data peserta khusus
$stmt = $core_db->prepare("SELECT * FROM cbt_peserta_khusus WHERE id = ? LIMIT 1");
$stmt->execute([$siswa_id]);
$peserta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$peserta) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Handle Form Update Biodata, Foto, & Password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_siswa'] ?? $peserta['nama_siswa']);
    $jk = trim($_POST['jenis_kelamin'] ?? 'L');
    $tempat_lahir = trim($_POST['tempat_lahir'] ?? '');
    $tanggal_lahir = !empty($_POST['tanggal_lahir']) ? $_POST['tanggal_lahir'] : null;
    $no_hp = trim($_POST['no_hp'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $password_baru = trim($_POST['password_baru'] ?? '');

    try {
        $foto_name = $peserta['foto'] ?? null;

        // Upload Foto
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['foto'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($ext, $allowed)) {
                throw new Exception("Format foto harus JPG, PNG, atau WebP.");
            }
            if ($file['size'] > 3 * 1024 * 1024) {
                throw new Exception("Ukuran foto maksimal 3 MB.");
            }

            $uploadDir = __DIR__ . '/../public/uploads/siswa/';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0777, true);
            }

            $foto_name = 'kj_' . $siswa_id . '_' . time() . '.' . $ext;
            $dest = $uploadDir . $foto_name;

            if (move_uploaded_file($file['tmp_name'], $dest)) {
                // Berhasil upload
            } else {
                $foto_name = $peserta['foto'];
            }
        }

        // Query Update
        if (!empty($password_baru)) {
            if (strlen($password_baru) < 4) {
                throw new Exception("Password baru minimal 4 karakter.");
            }
            $hash = password_hash($password_baru, PASSWORD_BCRYPT);
            $stmtUp = $core_db->prepare("
                UPDATE cbt_peserta_khusus 
                SET nama_siswa = ?, jenis_kelamin = ?, tempat_lahir = ?, tanggal_lahir = ?, no_hp = ?, alamat = ?, foto = ?, password_plain = ?, password_hash = ?
                WHERE id = ?
            ");
            $stmtUp->execute([$nama, $jk, $tempat_lahir, $tanggal_lahir, $no_hp, $alamat, $foto_name, $password_baru, $hash, $siswa_id]);
        } else {
            $stmtUp = $core_db->prepare("
                UPDATE cbt_peserta_khusus 
                SET nama_siswa = ?, jenis_kelamin = ?, tempat_lahir = ?, tanggal_lahir = ?, no_hp = ?, alamat = ?, foto = ?
                WHERE id = ?
            ");
            $stmtUp->execute([$nama, $jk, $tempat_lahir, $tanggal_lahir, $no_hp, $alamat, $foto_name, $siswa_id]);
        }

        // Update Session
        $_SESSION['siswa_nama'] = $nama;
        $_SESSION['nama'] = $nama;

        // Refresh Data
        $stmt = $core_db->prepare("SELECT * FROM cbt_peserta_khusus WHERE id = ? LIMIT 1");
        $stmt->execute([$siswa_id]);
        $peserta = $stmt->fetch(PDO::FETCH_ASSOC);

        $msg_success = "Data profil dan informasi peserta berhasil diperbarui!";
    } catch (Exception $e) {
        $msg_error = $e->getMessage();
    }
}

$page_title = 'Profil Peserta CBT';
include 'header.php';
?>

<!-- SweetAlert2 (Local Offline) -->
<script src="<?= ($baseFolder ? $baseFolder : '') ?>/public/assets/js/sweetalert2.all.min.js"></script>

<style>
    .profil-header {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #0284c7 100%);
        padding: 24px 20px 48px;
        color: white;
        border-bottom-left-radius: 28px;
        border-bottom-right-radius: 28px;
        box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.35);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .profil-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 20px;
        margin: -32px 16px 20px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
        border: 1px solid #e2e8f0;
        position: relative;
        z-index: 10;
    }
    .avatar-wrapper {
        position: relative;
        width: 86px;
        height: 86px;
        margin: 0 auto 12px;
    }
    .avatar-img {
        width: 86px;
        height: 86px;
        border-radius: 24px;
        object-fit: cover;
        background: #e0e7ff;
        border: 3px solid #ffffff;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2563eb;
        font-size: 2.2rem;
    }
    .btn-camera {
        position: absolute;
        bottom: -4px;
        right: -4px;
        background: #2563eb;
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        cursor: pointer;
        font-size: 0.8rem;
    }
    .form-group-pro {
        margin-bottom: 14px;
    }
    .form-label-pro {
        display: block;
        font-size: 0.78rem;
        font-weight: 800;
        color: #475569;
        margin-bottom: 5px;
    }
    .form-input-pro {
        width: 100%;
        box-sizing: border-box;
        padding: 10px 14px;
        border-radius: 12px;
        border: 1.5px solid #cbd5e1;
        font-size: 0.88rem;
        font-weight: 700;
        color: #0f172a;
        background: #ffffff;
        transition: border-color 0.2s, box-shadow 0.2s;
        outline: none;
    }
    .form-input-pro:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    .form-input-readonly {
        background: #f8fafc;
        color: #64748b;
        cursor: not-allowed;
    }
</style>

<div class="profil-header">
    <div>
        <div style="font-size: 0.72rem; font-weight: 700; color: #93c5fd; text-transform: uppercase; letter-spacing: 1px;">Pengaturan Akun</div>
        <h1 style="margin: 2px 0 0 0; font-size: 1.3rem; font-weight: 900;">Profil Peserta CBT</h1>
    </div>
    <div style="width: 42px; height: 42px; border-radius: 12px; background: rgba(255,255,255,0.18); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
        <i class="fa-solid fa-user-gear"></i>
    </div>
</div>

<div class="main-content" style="padding: 0 0 95px 0;">

    <form method="POST" action="profil_cbt.php" enctype="multipart/form-data">
        <div class="profil-card">
            
            <!-- Avatar Foto Profil -->
            <div class="avatar-wrapper">
                <input type="file" id="fotoInput" name="foto" accept="image/*" style="display: none;" onchange="previewFoto(this)">
                <div class="avatar-img" id="avatarDisplay">
                    <?php if (!empty($peserta['foto'])): ?>
                        <img src="/public/uploads/siswa/<?= htmlspecialchars($peserta['foto']) ?>" alt="Foto" style="width: 100%; height: 100%; object-fit: cover; border-radius: 20px;" onerror="this.onerror=null; this.outerHTML='<i class=\'fa-solid fa-user-graduate\'></i>';">
                    <?php else: ?>
                        <i class="fa-solid fa-user-graduate"></i>
                    <?php endif; ?>
                </div>
                <label for="fotoInput" class="btn-camera" title="Ganti Foto">
                    <i class="fa-solid fa-camera"></i>
                </label>
            </div>

            <div style="text-align: center; margin-bottom: 16px;">
                <h3 style="margin: 0; font-size: 1.05rem; font-weight: 900; color: #0f172a;"><?= htmlspecialchars($peserta['nama_siswa']) ?></h3>
                <div style="font-size: 0.75rem; color: #64748b; margin-top: 3px;">
                    <span style="background: #eff6ff; color: #2563eb; padding: 2px 8px; border-radius: 6px; font-weight: 800;"><?= htmlspecialchars($peserta['kelas_nama']) ?></span>
                </div>
            </div>

            <!-- Field Nomor Peserta & Username (Readonly) -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 14px;">
                <div>
                    <label class="form-label-pro">Nomor Peserta</label>
                    <input type="text" class="form-input-pro form-input-readonly" value="<?= htmlspecialchars($peserta['no_peserta']) ?>" readonly>
                </div>
                <div>
                    <label class="form-label-pro">Username Login</label>
                    <input type="text" class="form-input-pro form-input-readonly" value="<?= htmlspecialchars($peserta['username']) ?>" readonly>
                </div>
            </div>

            <!-- Nama Lengkap -->
            <div class="form-group-pro">
                <label class="form-label-pro">Nama Lengkap Peserta</label>
                <input type="text" name="nama_siswa" class="form-input-pro" value="<?= htmlspecialchars($peserta['nama_siswa']) ?>" required>
            </div>

            <!-- Jenis Kelamin -->
            <div class="form-group-pro">
                <label class="form-label-pro">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-input-pro">
                    <option value="L" <?= ($peserta['jenis_kelamin'] ?? 'L') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="P" <?= ($peserta['jenis_kelamin'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>

            <!-- Tempat & Tanggal Lahir -->
            <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 10px; margin-bottom: 14px;">
                <div>
                    <label class="form-label-pro">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-input-pro" placeholder="Contoh: Liwa" value="<?= htmlspecialchars($peserta['tempat_lahir'] ?? '') ?>">
                </div>
                <div>
                    <label class="form-label-pro">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-input-pro" value="<?= htmlspecialchars($peserta['tanggal_lahir'] ?? '') ?>">
                </div>
            </div>

            <!-- No HP / WA -->
            <div class="form-group-pro">
                <label class="form-label-pro">No. HP / WhatsApp (Aktif)</label>
                <input type="tel" name="no_hp" class="form-input-pro" placeholder="08xxxxxxxxxx" value="<?= htmlspecialchars($peserta['no_hp'] ?? '') ?>">
            </div>

            <!-- Alamat Domisili -->
            <div class="form-group-pro">
                <label class="form-label-pro">Alamat Lengkap</label>
                <textarea name="alamat" class="form-input-pro" rows="2" placeholder="Nama desa/pekon, RT/RW, kecamatan..."><?= htmlspecialchars($peserta['alamat'] ?? '') ?></textarea>
            </div>

            <!-- Ganti Password -->
            <div style="border-top: 1px dashed #cbd5e1; padding-top: 14px; margin-top: 14px;">
                <label class="form-label-pro" style="color: #1e3a8a;">
                    <i class="fa-solid fa-lock" style="color: #2563eb;"></i> Ganti Password Akun CBT (Opsional)
                </label>
                <input type="password" name="password_baru" class="form-input-pro" placeholder="Kosongkan jika tidak ingin mengubah password">
                <span style="font-size: 0.7rem; color: #64748b; margin-top: 3px; display: block;">
                    Gunakan minimal 4 karakter yang mudah diingat untuk login ujian berikutnya.
                </span>
            </div>

            <!-- Tombol Simpan -->
            <div style="margin-top: 20px;">
                <button type="submit" class="btn-ujian btn-ujian-start" style="width: 100%; border: none; cursor: pointer; padding: 13px; font-size: 0.92rem;">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan Profil
                </button>
            </div>

        </div>
    </form>

</div>

<script>
function previewFoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const avatar = document.getElementById('avatarDisplay');
            avatar.innerHTML = `<img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover; border-radius:20px;">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

<?php if (!empty($msg_success)): ?>
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '<?= addslashes($msg_success) ?>',
    confirmButtonColor: '#2563eb'
});
<?php elseif (!empty($msg_error)): ?>
Swal.fire({
    icon: 'error',
    title: 'Gagal Menyimpan',
    text: '<?= addslashes($msg_error) ?>',
    confirmButtonColor: '#ef4444'
});
<?php endif; ?>
</script>

<?php include 'footer.php'; ?>
