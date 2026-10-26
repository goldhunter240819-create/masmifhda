<style>
    .pip-container { max-width: 1200px; margin: 2rem auto; padding: 0 20px; width: 100%; }
    
    .z-panel {
        background: white; border-radius: 16px; border: 1px solid rgba(0,0,0,0.05); overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
    }
    .panel-header {
        padding: 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;
    }
    .panel-header h3 { margin: 0; font-size: 1.1rem; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 10px; }
    
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
                    <a href="/" class="parent" style="text-decoration:none;">Portal Admin</a>
                    <i data-lucide="chevron-right" class="sep"></i>
                    <span class="current">Surat Kehilangan</span>
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
                    <h3><i data-lucide="file-warning"></i> Cetak Surat Kehilangan Buku PIP</h3>
                </div>
                <div class="z-table-wrap" style="padding: 1.5rem;">
                    <div class="z-filter" style="margin-bottom:15px;">
                        <input type="text" id="searchInput" class="z-input" placeholder="Cari NISN atau Nama Siswa..." style="width:100%; max-width:300px;">
                    </div>
                    <table class="z-table" id="dataTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NISN</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Nama Ibu Kandung</th>
                                <th style="text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($pipData)): ?>
                            <tr><td colspan="6" style="text-align: center; color: #94a3b8; padding: 3rem;">Data PIP kosong.</td></tr>
                            <?php else: ?>
                                <?php foreach($pipData as $index => $c): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><strong><?php echo htmlspecialchars($c['NISN'] ?? '-'); ?></strong></td>
                                    <td><?php echo htmlspecialchars($c['NAMA_SISWA'] ?? $c['NAMA'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($c['KELAS'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($c['NAMA_IBU_KANDUNG'] ?? '-'); ?></td>
                                    <td style="text-align: right;">
                                        <a href="<?php echo base_url('admin/pip/cetak-kehilangan?id=' . urlencode($c['NIK'])); ?>" target="_blank" class="btn-action">
                                            <i data-lucide="printer" style="width: 14px; height: 14px;"></i> Cetak Surat
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

<script>
    lucide.createIcons();
    
    // Simple search
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toUpperCase();
        let rows = document.querySelector("#dataTable tbody").rows;
        
        for (let i = 0; i < rows.length; i++) {
            let col1 = rows[i].cells[1].textContent.toUpperCase();
            let col2 = rows[i].cells[2].textContent.toUpperCase();
            if (col1.indexOf(filter) > -1 || col2.indexOf(filter) > -1) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    });
</script>
