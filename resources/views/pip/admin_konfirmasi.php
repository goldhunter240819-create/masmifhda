<style>
    body {
        margin: 0; padding: 0; min-height: 100vh;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .z-header {
        height: 70px; padding: 0 2rem; background: white;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        display: flex; align-items: center; justify-content: space-between;
        position: sticky; top: 0; z-index: 100;
    }
    
    @media (max-width: 991px) {
        .pip-wrapper { margin-left: 0; }
        .z-sidebar { transform: translateX(-150%); transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .z-sidebar.open { transform: translateX(0); }
    }

    .header-breadcrumb { display: flex; align-items: center; gap: 10px; }
    .header-breadcrumb .parent { font-weight: 800; color: #0ea5e9; font-size: 0.9rem; text-decoration: none;}
    .header-breadcrumb .sep { width: 14px; color: #cbd5e1; }
    .header-breadcrumb .current { font-weight: 700; color: #1e293b; font-size: 0.9rem; }

    .z-header-right { display: flex; align-items: center; gap: 1.5rem; }
    .header-status-pill { 
        display: flex; align-items: center; gap: 6px; background: #f0fdf4;
        padding: 6px 12px; border-radius: 100px; border: 1px solid #dcfce7;
        font-size: 0.75rem; font-weight: 700; color: #15803d;
    }
    .dot { width: 8px; height: 8px; background: #22c55e; border-radius: 50%; }
    
    .z-avatar-premium {
        width: 36px; height: 36px; background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center;
        font-weight: 800; box-shadow: 0 4px 12px rgba(14,165,233,0.2);
    }
    .z-user-info .name { font-weight: 700; color: #1e293b; font-size: 0.85rem; }
    .z-user-info .role { font-size: 0.7rem; color: #64748b; }

    .pip-container { max-width: 1200px; margin: 2rem auto; padding: 0 20px; width: 100%; }
    
    .z-panel {
        background: white; border-radius: 16px; border: 1px solid rgba(0,0,0,0.05); overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
    }
    .panel-header {
        padding: 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;
    }
    .panel-header h3 { margin: 0; font-size: 1.1rem; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 10px; }
    
    table { width: 100%; border-collapse: collapse; }
    th { background: #f8fafc; padding: 14px 1.5rem; text-align: left; font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; }
    td { padding: 16px 1.5rem; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; color: #334155; }
    tr:last-child td { border-bottom: none; }
    
    .badge { padding: 4px 10px; border-radius: 100px; font-size: 0.75rem; font-weight: 700; }
    .badge-success { background: #dcfce7; color: #166534; }
    
    .btn-action {
        display: inline-flex; align-items: center; gap: 6px;
        background: #0ea5e9; color: white; padding: 6px 12px;
        border-radius: 8px; text-decoration: none; font-size: 0.75rem;
        font-weight: 700; transition: 0.2s; border: none; cursor: pointer;
    }
    .btn-action:hover { background: #0284c7; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2); transform: translateY(-1px); }
</style>

<div class="siakad-container">
    <?php include __DIR__ . '/admin_sidebar.php'; ?>

    <div class="z-main">
        <header class="z-header">
    <div class="z-header-left" style="display: flex; align-items: center; gap: 15px;">
        <button onclick="zToggleSidebar()" class="z-hamburger" style="display: none; background: transparent; border: none; cursor: pointer; color: #1e293b;">
            <i data-lucide="menu"></i>
        </button>
        <div class="header-breadcrumb">
            <a href="/" class="parent">Portal Admin</a>
            <i data-lucide="chevron-right" class="sep"></i>
            <span class="current">Data Konfirmasi PIP</span>
        </div>
    </div>
    <div class="z-header-right">
        <div class="header-status-pill">
            <div class="dot pulse"></div>
            <span>System Online</span>
        </div>
        <div class="z-header-user" style="display: flex; align-items: center; gap: 10px;">
            <div class="z-avatar-premium"><?php echo $initial; ?></div>
            <div class="z-user-info">
                <div class="name"><?php echo $nama; ?></div>
                <div class="role">Administrator</div>
            </div>
        </div>
    </div>
</header>

<div class="pip-container">
    <div class="z-panel">
        <div class="panel-header">
            <h3><i data-lucide="check-square"></i> Daftar Siswa Yang Sudah Mencetak / Konfirmasi</h3>
        </div>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Waktu Konfirmasi</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($confirmedList)): ?>
                    <tr><td colspan="6" style="text-align: center; color: #94a3b8; padding: 3rem;">Belum ada siswa yang mengkonfirmasi.</td></tr>
                    <?php else: ?>
                        <?php foreach($confirmedList as $index => $c): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><strong><?php echo htmlspecialchars($c['NISN']); ?></strong></td>
                            <td><?php echo htmlspecialchars($c['NAMA_SISWA'] ?? $c['NAMA']); ?></td>
                            <td><?php echo date('d M Y H:i', strtotime($c['confirmed_at'])); ?></td>
                            <td><span class="badge badge-success">Selesai</span></td>
                            <td style="text-align: right;">
                                <a href="<?php echo base_url('admin/pip/cetak-pernyataan?id=' . urlencode($c['NIK'])); ?>" target="_blank" class="btn-action">
                                    <i data-lucide="printer" style="width: 14px; height: 14px;"></i> Cetak Pernyataan
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        </div>
    </div>
</div>
</div>
</div>

<script>
    lucide.createIcons();
    
    function zToggleSidebar() {
        const sidebar = document.getElementById('zSidebar');
        sidebar.classList.toggle('open');
    }

    // Tampilkan tombol hamburger di layar kecil
    function checkScreenSize() {
        const hamburger = document.querySelector('.z-hamburger');
        const closeBtn = document.querySelector('.z-close-btn');
        if (window.innerWidth <= 991) {
            if(hamburger) hamburger.style.display = 'block';
            if(closeBtn) closeBtn.style.display = 'block';
        } else {
            if(hamburger) hamburger.style.display = 'none';
            if(closeBtn) closeBtn.style.display = 'none';
        }
    }
    
    window.addEventListener('resize', checkScreenSize);
    checkScreenSize();
</script>
