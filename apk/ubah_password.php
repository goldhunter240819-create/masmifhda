<?php
require_once 'koneksi.php';

if (!isset($_SESSION['siswa_id'])) {
    header("Location: login.php");
    exit;
}

$siswa_id = $_SESSION['siswa_id'];

// Get user_id from siswa table
$stmt = $core_db->prepare("SELECT user_id, nama FROM siswa WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $siswa_id]);
$siswa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$siswa || empty($siswa['user_id'])) {
    die("Data pengguna tidak valid.");
}

$user_id = $siswa['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass_lama = $_POST['password_lama'] ?? '';
    $pass_baru = $_POST['password_baru'] ?? '';
    $pass_konfirmasi = $_POST['konfirmasi_password'] ?? '';

    // Validasi input
    if (empty($pass_lama) || empty($pass_baru) || empty($pass_konfirmasi)) {
        $error_msg = "Semua kolom harus diisi.";
    } elseif ($pass_baru !== $pass_konfirmasi) {
        $error_msg = "Konfirmasi password tidak cocok.";
    } elseif (strlen($pass_baru) < 6) {
        $error_msg = "Password baru minimal 6 karakter.";
    } else {
        // Cek password lama
        $stmt_u = $core_db->prepare("SELECT password FROM users WHERE id = :uid LIMIT 1");
        $stmt_u->execute(['uid' => $user_id]);
        $user_row = $stmt_u->fetch(PDO::FETCH_ASSOC);

        if ($user_row && password_verify($pass_lama, $user_row['password'])) {
            // Update password baru
            $hashed = password_hash($pass_baru, PASSWORD_DEFAULT);
            $stmt_update = $core_db->prepare("UPDATE users SET password = :pass WHERE id = :uid");
            if ($stmt_update->execute(['pass' => $hashed, 'uid' => $user_id])) {
                $success_msg = "Password berhasil diubah!";
                
                require_once __DIR__ . '/../vendor/autoload.php';
                if (class_exists('\App\Services\AuditLogger')) {
                    \App\Services\AuditLogger::dataChange('Users', 'UPDATE', "Ubah password oleh siswa {$siswa['nama']} (APK)");
                }
            } else {
                $error_msg = "Terjadi kesalahan saat menyimpan password baru.";
            }
        } else {
            $error_msg = "Password lama salah.";
        }
    }
}

$page_title = 'Ubah Password';
include 'header.php';
?>

<div class="header">
    <div class="header-top">
        <a href="profil.php" style="color: white; font-size: 1.5rem; text-decoration: none;"><i class="fa-solid fa-arrow-left"></i></a>
        <div class="greeting" style="flex: 1; text-align: center; padding-right: 24px;">
            <h1 style="margin: 0; font-size: 1.2rem;">Ubah Password</h1>
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

    <form method="POST" action="">
        <div class="data-card" style="margin-top: -1.5rem; z-index: 10;">
            <h3 style="font-size: 1rem; color: var(--primary); margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
                <i class="fa-solid fa-key"></i> Form Ganti Kata Sandi
            </h3>
            
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Password Lama</label>
                <div style="position: relative;">
                    <input type="password" name="password_lama" id="plama" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;" required>
                    <i class="fa-solid fa-eye" id="toggle-plama" style="position: absolute; right: 10px; top: 12px; color: #94a3b8; cursor: pointer;"></i>
                </div>
            </div>
            
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Password Baru</label>
                <div style="position: relative;">
                    <input type="password" name="password_baru" id="pbaru" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;" required>
                    <i class="fa-solid fa-eye" id="toggle-pbaru" style="position: absolute; right: 10px; top: 12px; color: #94a3b8; cursor: pointer;"></i>
                </div>
                <p style="font-size: 0.7rem; color: #64748b; margin-top: 4px;">* Minimal 6 karakter.</p>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Konfirmasi Password Baru</label>
                <div style="position: relative;">
                    <input type="password" name="konfirmasi_password" id="pkonf" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: 'Outfit', sans-serif;" required>
                    <i class="fa-solid fa-eye" id="toggle-pkonf" style="position: absolute; right: 10px; top: 12px; color: #94a3b8; cursor: pointer;"></i>
                </div>
            </div>
        </div>

        <button type="submit" style="width: 100%; background: var(--primary); color: white; border: none; padding: 15px; border-radius: var(--border-radius); font-size: 1rem; font-weight: 600; font-family: 'Outfit', sans-serif; margin-top: 20px; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3); cursor: pointer;">
            <i class="fa-solid fa-save"></i> Simpan Password
        </button>
    </form>
</div>

<script>
function setupTogglePassword(toggleId, inputId) {
    document.getElementById(toggleId).addEventListener('click', function (e) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            this.classList.remove('fa-eye');
            this.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            this.classList.remove('fa-eye-slash');
            this.classList.add('fa-eye');
        }
    });
}

setupTogglePassword('toggle-plama', 'plama');
setupTogglePassword('toggle-pbaru', 'pbaru');
setupTogglePassword('toggle-pkonf', 'pkonf');
</script>

<?php include 'footer.php'; ?>
