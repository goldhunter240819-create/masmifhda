<?php
$adminNama = $_SESSION['nama'] ?? 'Admin';
$adminInitial = strtoupper(substr($adminNama, 0, 2));
$active_page = 'pengaturan_kkm';
$am = 'kurikulum';
$ap = 'pengaturan_kkm';
$current_label = 'Waka Kurikulum';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring KKM - Waka Kurikulum</title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>?v=<?php echo time(); ?>">
    <!-- Zamrud CSS -->
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . time()); ?>">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .z-btn {
            font-size: 12px !important;
            padding: 8px 16px !important;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        .z-btn:hover { filter: brightness(0.9); }
    </style>
</head>
<body>
    <div class="z-wrap">
        <?php include __DIR__ . '/_sidebar_kurikulum.php'; ?>
        <main class="z-main">
            <header class="z-header">
                <div class="z-header-left">
                    <button class="z-hamburger" id="zHamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
                    <span class="z-header-title">Monitoring KKM</span>
                </div>
                <div class="z-header-user">
                    <div class="z-user-info">
                        <div class="name"><?php echo $adminNama; ?></div>
                        <div class="role">Administrator</div>
                    </div>
                    <div class="z-avatar"><?php echo $adminInitial; ?></div>
                </div>
            </header>
            
            <div class="z-scroll">

<div class="elite-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: var(--z-radius); margin-bottom: 2rem; color: white;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="target" style="color: rgba(255,255,255,0.8);"></i> Monitoring KKM
        </h1>
        <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Kelola dan pantau Kriteria Ketuntasan Minimal tiap mata pelajaran.</p>
    </div>
</div>

<div class="content-card" style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9;">
        <div style="color: #64748b; font-size: 0.95rem;">
            Tentukan nilai KKM untuk setiap jenjang kelas (10, 11, dan 12).
        </div>
        <div>
            <button type="button" id="btnSimpan" onclick="simpanSemuaKKM()" class="z-btn z-btn-primary" style="display: flex; align-items: center; gap: 8px; padding: 10px 20px;">
                <i data-lucide="save" style="width: 18px; height: 18px;"></i>
                <span id="btnText">Simpan Semua KKM</span>
            </button>
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 0.95rem;" id="tableKKM">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 12px 15px; color: #64748b; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; width: 15%;">Kelompok</th>
                    <th style="text-align: left; padding: 12px 15px; color: #64748b; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; width: 40%;">Mata Pelajaran</th>
                    <th style="text-align: center; padding: 12px 15px; color: #64748b; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; border-bottom: 2px solid #e2e8f0;">KKM Kelas 10</th>
                    <th style="text-align: center; padding: 12px 15px; color: #64748b; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; border-bottom: 2px solid #e2e8f0;">KKM Kelas 11</th>
                    <th style="text-align: center; padding: 12px 15px; color: #64748b; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; border-bottom: 2px solid #e2e8f0;">KKM Kelas 12</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($grouped_mapels as $kelompok => $mapel_list): ?>
                    <!-- Baris Header Kelompok -->
                    <tr>
                        <td colspan="5" style="padding: 15px; background: #f8fafc; font-weight: 700; color: #334155; border-bottom: 1px solid #e2e8f0; border-top: 1px solid #e2e8f0;">
                            <?= htmlspecialchars($kelompok) ?>
                        </td>
                    </tr>
                    
                    <?php foreach($mapel_list as $m): ?>
                    <tr class="mapel-row" data-id="<?= $m['id'] ?>">
                        <td style="padding: 12px 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle;">
                            <span style="background: #e0f2fe; color: #0284c7; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">
                                <?= htmlspecialchars(str_replace('Kelompok ', 'Kel. ', $kelompok)) ?>
                            </span>
                        </td>
                        <td style="padding: 12px 15px; border-bottom: 1px solid #f1f5f9; color: #1e293b; font-weight: 500; vertical-align: middle;">
                            <?= htmlspecialchars($m['nama_mapel']) ?>
                        </td>
                        <td style="padding: 12px 15px; border-bottom: 1px solid #f1f5f9; text-align: center; vertical-align: middle;">
                            <input type="number" class="kkm-input" data-field="kkm_10" value="<?= $m['kkm_kelas_10'] ?>" placeholder="-" style="width: 60px; text-align: center; padding: 8px; border: 1px solid #e2e8f0; border-radius: 6px; font-weight: 600; color: #334155; background: #f8fafc; transition: all 0.2s;">
                        </td>
                        <td style="padding: 12px 15px; border-bottom: 1px solid #f1f5f9; text-align: center; vertical-align: middle;">
                            <input type="number" class="kkm-input" data-field="kkm_11" value="<?= $m['kkm_kelas_11'] ?>" placeholder="-" style="width: 60px; text-align: center; padding: 8px; border: 1px solid #e2e8f0; border-radius: 6px; font-weight: 600; color: #334155; background: #f8fafc; transition: all 0.2s;">
                        </td>
                        <td style="padding: 12px 15px; border-bottom: 1px solid #f1f5f9; text-align: center; vertical-align: middle;">
                            <input type="number" class="kkm-input" data-field="kkm_12" value="<?= $m['kkm_kelas_12'] ?>" placeholder="-" style="width: 60px; text-align: center; padding: 8px; border: 1px solid #e2e8f0; border-radius: 6px; font-weight: 600; color: #334155; background: #f8fafc; transition: all 0.2s;">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                
                <?php if(empty($grouped_mapels)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 30px; color: #94a3b8;">
                        Tidak ada mata pelajaran ditemukan.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .kkm-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        background: white;
    }
    .kkm-input::placeholder {
        color: #cbd5e1;
    }
    #btnSimpan:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    lucide.createIcons();

    function simpanSemuaKKM() {
        const rows = document.querySelectorAll('.mapel-row');
        const data = [];
        
        rows.forEach(row => {
            const id = row.getAttribute('data-id');
            const k10 = row.querySelector('input[data-field="kkm_10"]').value;
            const k11 = row.querySelector('input[data-field="kkm_11"]').value;
            const k12 = row.querySelector('input[data-field="kkm_12"]').value;
            
            data.push({
                id: id,
                kkm_10: k10,
                kkm_11: k11,
                kkm_12: k12
            });
        });

        const btn = document.getElementById('btnSimpan');
        const btnText = document.getElementById('btnText');
        const originalText = btnText.innerHTML;
        
        btn.disabled = true;
        btn.style.opacity = '0.7';
        btnText.innerHTML = 'Menyimpan...';

        fetch('/admin/kurikulum/pengaturan-kkm/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.message,
                    confirmButtonColor: '#3b82f6'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: result.message || 'Terjadi kesalahan.',
                    confirmButtonColor: '#3b82f6'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Gagal menghubungi server.',
                confirmButtonColor: '#3b82f6'
            });
        })
        .finally(() => {
            btn.disabled = false;
            btn.style.opacity = '1';
            btnText.innerHTML = originalText;
        });
    }
</script>

            </div>
        </main>
    </div>

    <!-- ZNS Notification System -->
    <div id="zns-container"></div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide) lucide.createIcons();
        });

        function zToggleSidebar() {
            const sidebar = document.querySelector('.z-sidebar');
            if (sidebar) sidebar.classList.toggle('open');
        }
    </script>
</body>
</html>
