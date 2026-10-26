<?php
session_start();

$config_file = __DIR__ . '/../config/database.php';
$is_installed = false;

// Check if config exists and has valid data
if (file_exists($config_file)) {
    $config = require $config_file;
    if (isset($config['core']['host']) && $config['core']['host'] !== '') {
        $is_installed = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = $_POST['host'];
    $user = $_POST['username'];
    $pass = $_POST['password'];

    try {
        // Test connection
        $pdo = new PDO("mysql:host=$host", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Write to config
        $config_content = "<?php\n\nreturn [\n" .
            "    'core' => ['host' => '$host', 'db_name' => 'core_db', 'username' => '$user', 'password' => '$pass'],\n" .
            "    'cbt' => ['host' => '$host', 'db_name' => 'cbt_db', 'username' => '$user', 'password' => '$pass'],\n" .
            "    'siakad' => ['host' => '$host', 'db_name' => 'siakad_db', 'username' => '$user', 'password' => '$pass'],\n" .
            "    'keuangan' => ['host' => '$host', 'db_name' => 'keuangan_db', 'username' => '$user', 'password' => '$pass'],\n" .
            "    'absensi' => ['host' => '$host', 'db_name' => 'absensi_db', 'username' => '$user', 'password' => '$pass'],\n" .
            "    'absen' => ['host' => '$host', 'db_name' => 'absen_db', 'username' => '$user', 'password' => '$pass'],\n" .
            "];\n";
            
        file_put_contents($config_file, $config_content);
        
        // Optional: Import SQL here if you have a combined structure SQL
        // For now, assume client imports the SQL provided by you manually, or we just connect them.
        
        header('Location: /activate');
        exit;
    } catch (PDOException $e) {
        $error = "Gagal terhubung ke MySQL: " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Instalasi GH-SSS</title>
    <link rel="stylesheet" href="/public/assets/css/plus-jakarta-sans.css">
    <script src="/public/assets/js/lucide.min.js"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0f172a;
            color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .setup-card {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 3rem;
            border-radius: 24px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.85rem;
            color: #cbd5e1;
            font-weight: 600;
        }
        .form-control {
            width: 100%;
            padding: 12px 15px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #f8fafc;
            border-radius: 12px;
            font-family: inherit;
            outline: none;
            box-sizing: border-box;
        }
        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: 0.3s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
        }
    </style>
</head>
<body>
    <div class="setup-card">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="width: 70px; height: 70px; background: rgba(59, 130, 246, 0.1); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; border: 1px solid rgba(59, 130, 246, 0.3);">
                <i data-lucide="database" style="color: #60a5fa; width: 32px; height: 32px;"></i>
            </div>
            <h1 style="margin: 0; font-size: 1.5rem; color: #f8fafc;">Database Setup</h1>
            <p style="color: #94a3b8; font-size: 0.9rem; margin-top: 8px;">Hubungkan aplikasi ke MySQL Server</p>
        </div>

        <?php if(isset($error)): ?>
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-size: 0.85rem;">
                <i data-lucide="alert-triangle" style="width: 16px; vertical-align: -3px;"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if($is_installed && !isset($_GET['force'])): ?>
            <div style="background: rgba(14, 165, 233, 0.1); border: 1px solid rgba(14, 165, 233, 0.3); color: #34d399; padding: 1rem; border-radius: 12px; text-align: center; margin-bottom: 1.5rem;">
                Database sudah dikonfigurasi.
            </div>
            <a href="/" class="btn-submit" style="text-decoration: none; background: linear-gradient(135deg, #0ea5e9, #0284c7);">
                LANJUT KE APLIKASI <i data-lucide="arrow-right"></i>
            </a>
            <div style="text-align: center; margin-top: 15px;">
                <a href="?force=1" style="color: #64748b; font-size: 0.8rem; text-decoration: none;">Ulangi Setup Konfigurasi</a>
            </div>
        <?php else: ?>
            <form method="POST">
                <div class="form-group">
                    <label>Database Host</label>
                    <input type="text" name="host" class="form-control" value="127.0.0.1" required>
                </div>
                <div class="form-group">
                    <label>Database Username</label>
                    <input type="text" name="username" class="form-control" value="root" required>
                </div>
                <div class="form-group">
                    <label>Database Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ada password">
                </div>
                
                <div style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3); padding: 12px; border-radius: 12px; margin-bottom: 20px; font-size: 0.8rem; color: #94a3b8; line-height: 1.5;">
                    <i data-lucide="info" style="width: 14px; vertical-align: -2px; color: #60a5fa;"></i> 
                    <b>Catatan Instalasi:</b><br>Pastikan Anda telah mengimpor (Import) file <code style="color:#60a5fa;">GH-SSS_Database.sql</code> ke dalam MySQL Server Anda melalui phpMyAdmin atau Command Line. File SQL tersebut akan otomatis membuat struktur 6 database utama.
                </div>

                <button type="submit" class="btn-submit">
                    SIMPAN & LANJUTKAN <i data-lucide="chevron-right"></i>
                </button>
            </form>
        <?php endif; ?>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>

