<?php
include 'includes/koneksi.php';

if (isset($_SESSION['admin'])) {
    header("Location: pages/dashboard.php");
    exit;
}

$error = "";
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];
    
    $query = "SELECT * FROM admin WHERE username = '$username'";
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin'] = $row['username'];
            header("Location: pages/dashboard.php");
            exit;
        }
    }
    $error = "Username atau Password salah!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Login - <?php echo $app_name; ?></title>
    <link href="/public/absen/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=1.1">
    <link rel="icon" href="<?php echo $app_favicon; ?>">
    <script src="/public/assets/js/lucide.min.js"></script>
    <style>
        body { background-color: #f3f4f6; }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 3rem;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255,255,255,0.5);
        }
        .form-control {
            padding: 12px;
            border: 1px solid #eee;
            border-radius: 12px;
            background: #f9f9f9;
            font-size: 0.9rem;
        }
        .form-control:focus {
            background: #f9f9f9;
            border-color: #0ea5e9;
            box-shadow: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            border: none;
            padding: 1rem;
            border-radius: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.3);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(14, 165, 233, 0.4);
        }
    </style>
</head>
<body>

<div class="login-container" style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div class="login-card">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="margin: 0 auto 1.5rem; display: flex; justify-content: center;">
                <?php if ($app_logo): ?>
                    <img src="<?php echo $app_logo; ?>" alt="Logo" style="height: 80px; object-fit: contain;">
                <?php else: ?>
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: white; border-radius: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(14, 165, 233, 0.3);">
                        <i data-lucide="qr-code" style="width: 40px; height: 40px;"></i>
                    </div>
                <?php endif; ?>
            </div>
            <h2 style="margin: 0; color: #1e293b; font-weight: 800;"><?php echo $app_name; ?></h2>
            <p style="font-size: 0.9rem; color: #64748b;">Sistem Manajemen Kehadiran Terpadu</p>
        </div>

        <?php if ($error): ?>
            <div style="color: #d32f2f; background: #ffebee; padding: 10px; border-radius: 8px; font-size: 0.8rem; margin-bottom: 1.5rem; text-align: center;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; font-weight: 600; color: #1f2937;">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Username Anda" required>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; font-weight: 600; color: #1f2937;">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" name="login" class="btn btn-primary w-100">
                MASUK <i data-lucide="log-in"></i>
            </button>
        </form>

        <div style="margin-top: 2rem; text-align: center; font-size: 0.8rem; color: #9ca3af;">
            Powered by GH-SSS Engine &copy; <?php echo date('Y'); ?>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
</body>
</html>


