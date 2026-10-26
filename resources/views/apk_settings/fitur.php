<?php
$page_title = 'Pengaturan Fitur APK';
$active_menu = 'fitur';

$settings_file = __DIR__ . '/../../apk/apk_settings.json';
$settings = [];
if (file_exists($settings_file)) {
    $settings = json_decode(file_get_contents($settings_file), true);
}
$elearning_enabled = isset($settings['elearning_enabled']) ? $settings['elearning_enabled'] : true;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Platform Digital</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <?php include __DIR__ . '/../admin/_zamrud_head.php'; ?>
    <style>
        .setting-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .setting-info {
            flex: 1;
        }
        .setting-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 6px;
        }
        .setting-desc {
            font-size: 0.9rem;
            color: #64748b;
        }
        
        /* Switch Toggle */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 28px;
        }
        .switch input { 
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: .4s;
            border-radius: 34px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #10b981;
        }
        input:checked + .slider:before {
            transform: translateX(22px);
        }
    </style>
</head>
<body class="z-body">
    <?php include 'sidebar.php'; ?>

    <main class="z-main">
        <header class="z-header">
            <div class="z-header-left">
                <button class="z-menu-btn" onclick="zToggleSidebar()">
                    <i data-lucide="menu"></i>
                </button>
                <div class="z-breadcrumb">
                    <span class="z-breadcrumb-item">Pengaturan APK</span>
                    <i data-lucide="chevron-right" class="z-breadcrumb-sep"></i>
                    <span class="z-breadcrumb-item active">Pengaturan Fitur</span>
                </div>
            </div>
        </header>

        <div class="z-content">
            <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 20px; padding: 30px; color: white; margin-bottom: 30px; box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);">
                <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 10px 0;"><i data-lucide="toggle-right" style="width: 28px; height: 28px; display: inline-block; vertical-align: text-bottom; margin-right: 10px;"></i> Master Saklar Fitur</h1>
                <p style="font-size: 0.95rem; opacity: 0.9; margin: 0;">Pusat kontrol untuk mematikan atau menyalakan fitur-fitur di aplikasi mobile secara global (real-time) tanpa perlu update aplikasi.</p>
            </div>
            
            <div class="setting-card">
                <div class="setting-info">
                    <div class="setting-title"><i data-lucide="book-open" style="width: 18px; height: 18px; display: inline-block; vertical-align: text-bottom; margin-right: 6px; color: #3b82f6;"></i> E-Learning (Tugas Daring)</div>
                    <div class="setting-desc">Jika dimatikan, seluruh fitur E-Learning di aplikasi guru dan siswa akan diblokir sementara dengan pesan pemeliharaan sistem.</div>
                </div>
                <div>
                    <label class="switch">
                        <input type="checkbox" id="toggle-elearning" <?= $elearning_enabled ? 'checked' : '' ?> onchange="toggleFeature('elearning_enabled', this.checked)">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>

        </div>
    </main>

    <script>
        lucide.createIcons();

        function toggleFeature(featureKey, isEnabled) {
            fetch('<?= base_url("admin/apk-settings/fitur/save") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ [featureKey]: isEnabled })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Optional: Show toast or feedback
                } else {
                    alert('Gagal menyimpan pengaturan.');
                    // Revert UI
                    document.getElementById('toggle-' + featureKey.split('_')[0]).checked = !isEnabled;
                }
            })
            .catch(err => {
                alert('Terjadi kesalahan sistem.');
            });
        }
    </script>
</body>
</html>
