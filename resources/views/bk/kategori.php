<?php
$title = $title ?? 'Kategori Poin BK | MASMIFHDA';
$active_page = $activeMenu ?? 'bk_kategori';
$adminNama = $_SESSION['nama'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>?v=<?php echo time(); ?>">
    <!-- Zamrud CSS -->
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . time()); ?>">
    <!-- Lucide Icons -->
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
</head>
<body>
    <div class="z-wrap">
        <?php include __DIR__ . '/../admin/_sidebar_bk.php'; ?>
        <main class="z-main">
            <header class="z-header">
                <div class="z-header-left">
                    <button class="z-hamburger" id="zHamburger" onclick="zToggleSidebar()"><i data-lucide="menu"></i></button>
                    <span class="z-header-title">Kategori Poin BK</span>
                </div>
                <div class="z-header-user">
                    <div class="z-user-info">
                        <div class="name"><?php echo $adminNama; ?></div>
                        <div class="role">Bimbingan Konseling</div>
                    </div>
                </div>
            </header>
            
            <div class="z-content">
                <div class="elite-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: var(--z-radius, 12px); margin-bottom: 2rem; color: white; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                            <i data-lucide="tags" style="color: rgba(255,255,255,0.8);"></i> Kategori Poin BK
                        </h1>
                        <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Kelola master data pelanggaran dan prestasi beserta poinnya.</p>
                    </div>
                    <div>
                        <button onclick="showAddKategori()" style="background: white; color: #0284c7; border: none; padding: 0.75rem 1.25rem; border-radius: 12px; font-weight: 700; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: 0.2s;">
                            <i data-lucide="plus" style="width:18px;height:18px;"></i> Tambah Kategori
                        </button>
                    </div>
                </div>


<?php if(isset($_SESSION['flash_success'])): ?>
    <div style="background: #ecfdf5; border-left: 4px solid #10b981; color: #065f46; padding: 1rem; margin-bottom: 1.5rem; border-radius: 8px;">
        <div style="display: flex; align-items: center; gap: 8px; font-weight: 600;">
            <i data-lucide="check-circle" style="width: 20px; height: 20px; color: #10b981;"></i>
            <?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
        </div>
    </div>
<?php endif; ?>
<?php if(isset($_SESSION['flash_error'])): ?>
    <div style="background: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; padding: 1rem; margin-bottom: 1.5rem; border-radius: 8px;">
        <div style="display: flex; align-items: center; gap: 8px; font-weight: 600;">
            <i data-lucide="alert-circle" style="width: 20px; height: 20px; color: #ef4444;"></i>
            <?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
        </div>
    </div>
<?php endif; ?>

<div class="z-panel">
    <table class="z-table">
        <thead>
            <tr>
                <th>Tipe</th>
                <th>Nama Kategori</th>
                <th>Poin</th>
                <th style="width: 150px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $currentSection = '';
            foreach($kategoriList as $k): 
                $sectionName = '';
                $bgColor = '#f8fafc';
                $textColor = '#334155';
                $iconColor = '#64748b';
                
                if ($k['tipe'] == 'prestasi') {
                    $sectionName = 'PRESTASI';
                    $bgColor = '#d1fae5';
                    $textColor = '#065f46';
                    $iconColor = '#10b981';
                } else {
                    $tingkatLabel = $k['tingkat'] ? ucfirst($k['tingkat']) : 'Tidak Diketahui';
                    $sectionName = 'PELANGGARAN ' . strtoupper($tingkatLabel);
                    
                    if ($k['tingkat'] == 'berat') {
                        $bgColor = '#fee2e2';
                        $textColor = '#991b1b';
                        $iconColor = '#ef4444';
                    } elseif ($k['tingkat'] == 'sedang') {
                        $bgColor = '#ffedd5';
                        $textColor = '#9a3412';
                        $iconColor = '#f97316';
                    } elseif ($k['tingkat'] == 'ringan') {
                        $bgColor = '#fef9c3';
                        $textColor = '#854d0e';
                        $iconColor = '#eab308';
                    }
                }

                if ($sectionName !== $currentSection): 
                    $currentSection = $sectionName;
            ?>
            <tr style="background: <?php echo $bgColor; ?>;">
                <td colspan="4" style="font-weight: 800; color: <?php echo $textColor; ?>; padding: 1.25rem 1rem 0.75rem; border-bottom: 2px solid #cbd5e1; font-size: 0.95rem; letter-spacing: 0.5px;">
                    <i data-lucide="<?php echo $k['tipe'] == 'prestasi' ? 'star' : 'alert-triangle'; ?>" style="width: 16px; height: 16px; margin-right: 6px; color: <?php echo $iconColor; ?>; vertical-align: middle;"></i>
                    <span style="vertical-align: middle;"><?php echo $sectionName; ?></span>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <td>
                    <?php if ($k['tipe'] == 'pelanggaran'): ?>
                        <?php if (isset($k['tingkat']) && $k['tingkat'] == 'berat'): ?>
                            <span class="pill" style="background:#fee2e2; color:#b91c1c;">Pel. Berat</span>
                        <?php elseif (isset($k['tingkat']) && $k['tingkat'] == 'sedang'): ?>
                            <span class="pill" style="background:#ffedd5; color:#c2410c;">Pel. Sedang</span>
                        <?php elseif (isset($k['tingkat']) && $k['tingkat'] == 'ringan'): ?>
                            <span class="pill" style="background:#fef9c3; color:#a16207;">Pel. Ringan</span>
                        <?php else: ?>
                            <span class="pill pill-red">Pelanggaran</span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="pill pill-green">Prestasi</span>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($k['nama_kategori']); ?></td>
                <td style="font-weight: bold;"><?php echo $k['poin']; ?></td>
                <td>
                    <div style="display: flex; gap: 8px;">
                        <button style="padding: 6px 12px; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px; border: 1px solid #e2e8f0; background: white; color: #475569; border-radius: 6px; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='white'" onclick='editKategori(<?php echo htmlspecialchars(json_encode($k)); ?>)'>
                            <i data-lucide="edit-3" style="width:14px; height:14px;"></i> Edit
                        </button>
                        <a href="<?php echo base_url('bk/kategori/delete/' . $k['id']); ?>" style="padding: 6px 12px; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px; background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; border-radius: 6px; text-decoration: none; transition: 0.2s;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'" onclick="return confirm('Hapus kategori ini?')">
                            <i data-lucide="trash-2" style="width:14px; height:14px;"></i> Hapus
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($kategoriList)): ?>
                <tr><td colspan="4" style="text-align:center; padding: 2rem; color: #64748b;">Belum ada kategori data poin yang ditambahkan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Form -->
<style>
    .modal-input { width: 100%; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; margin-top: 0.4rem; box-sizing: border-box; font-family: inherit; font-size: 0.95rem; transition: 0.2s; background: #f8fafc; color: #0f172a; }
    .modal-input:focus { border-color: #3b82f6; background: #ffffff; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
    .modal-label { font-size: 0.8rem; font-weight: 800; color: #64748b; display: block; margin-bottom: 0.25rem; letter-spacing: 0.5px; text-transform: uppercase; }
    .modal-form-group { margin-bottom: 1.25rem; }
    .btn-modal-cancel { background: #f1f5f9; color: #475569; padding: 0.7rem 1.25rem; border-radius: 8px; border: none; font-weight: 700; cursor: pointer; transition: 0.2s; font-size: 0.9rem; }
    .btn-modal-cancel:hover { background: #e2e8f0; }
    .btn-modal-save { background: #3b82f6; color: white; padding: 0.7rem 1.5rem; border-radius: 8px; border: none; font-weight: 700; cursor: pointer; transition: 0.2s; font-size: 0.9rem; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25); display: flex; align-items: center; gap: 8px; }
    .btn-modal-save:hover { background: #2563eb; transform: translateY(-1px); }
</style>

<div id="modalKategori" class="z-modal-ov">
    <div class="z-modal">
        <div class="z-modal-head">
            <h3 class="z-modal-title"><i data-lucide="tag"></i> <span id="modalTitle">Tambah Kategori</span></h3>
            <button type="button" class="z-modal-close" onclick="document.getElementById('modalKategori').classList.remove('open')"><i data-lucide="x"></i></button>
        </div>
        <form action="<?php echo base_url('bk/kategori/save'); ?>" method="POST" id="formKategori">
            <div class="z-modal-body">
                <input type="hidden" name="id" id="kategori_id">
                
                <div class="z-form-group">
                    <label class="z-label">Nama Kategori</label>
                    <input type="text" name="nama_kategori" id="nama_kategori" class="z-input" required placeholder="Cth: Terlambat datang ke sekolah">
                </div>

                <div class="z-form-group">
                    <label class="z-label">Tipe</label>
                    <select name="tipe" id="tipe" class="z-input" required onchange="toggleTingkat()">
                        <option value="pelanggaran">Pelanggaran</option>
                        <option value="prestasi">Prestasi</option>
                    </select>
                </div>

                <div class="z-form-group" id="tingkat_group">
                    <label class="z-label">Tingkat Pelanggaran</label>
                    <select name="tingkat" id="tingkat" class="z-input">
                        <option value="ringan">Ringan</option>
                        <option value="sedang">Sedang</option>
                        <option value="berat">Berat</option>
                    </select>
                </div>

                <div class="z-form-group">
                    <label class="z-label">Bobot Poin</label>
                    <input type="number" name="poin" id="kategori_poin" class="z-input" required value="0" min="0" placeholder="0">
                </div>
            </div>
            <div class="z-modal-foot">
                <button type="button" class="z-btn z-btn-secondary" onclick="document.getElementById('modalKategori').classList.remove('open')">Batal</button>
                <button type="submit" class="z-btn z-btn-primary">
                    <i data-lucide="save" style="width: 16px; height: 16px;"></i> Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showAddKategori() {
    document.getElementById('modalTitle').innerHTML = 'Tambah Kategori';
    document.getElementById('kategori_id').value = '';
    document.getElementById('tipe').value = 'pelanggaran';
    document.getElementById('tingkat_group').style.display = 'block';
    document.getElementById('tingkat').value = 'ringan';
    document.getElementById('nama_kategori').value = '';
    document.getElementById('kategori_poin').value = '0';
    document.getElementById('modalKategori').classList.add('open');
}

function editKategori(data) {
    document.getElementById('modalTitle').innerHTML = 'Edit Kategori';
    document.getElementById('kategori_id').value = data.id;
    document.getElementById('tipe').value = data.tipe;
    if (data.tipe === 'pelanggaran') {
        document.getElementById('tingkat_group').style.display = 'block';
        document.getElementById('tingkat').value = data.tingkat || 'ringan';
    } else {
        document.getElementById('tingkat_group').style.display = 'none';
        document.getElementById('tingkat').value = 'ringan';
    }
    document.getElementById('nama_kategori').value = data.nama_kategori;
    document.getElementById('kategori_poin').value = data.poin;
    document.getElementById('modalKategori').classList.add('open');
}

function toggleTingkat() {
    var tipe = document.getElementById('tipe').value;
    if(tipe === 'pelanggaran') {
        document.getElementById('tingkat_group').style.display = 'block';
    } else {
        document.getElementById('tingkat_group').style.display = 'none';
    }
}

</script>

</script>
            </div>
        </main>
    </div>
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
