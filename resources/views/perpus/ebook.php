<?php
$title = "Katalog E-Book - Admin";
$active_module = 'perpus';
$active_page = 'ebook';
$adminNama = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Admin';
$adminInitial = strtoupper(substr($adminNama, 0, 2));
ob_start();
?>
<div class="siakad-container">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>
    
    <div class="z-main">
        <header class="z-header" style="height: 80px; padding: 0 2rem; background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100;">
            <div class="z-header-left" style="display:flex;align-items:center;gap:15px;">
                <button class="z-hamburger" onclick="document.getElementById('zSidebar').classList.toggle('open')" style="background:none;border:none;cursor:pointer;color:#64748b;">
                    <i data-lucide="menu"></i>
                </button>
                <div class="header-breadcrumb" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;">
                    <span class="parent" style="font-weight:800;letter-spacing:1px;font-size:0.8rem;color:#9333ea;">PERPUSTAKAAN DIGITAL</span>
                    <i data-lucide="chevron-right" class="sep" style="width:14px;color:#cbd5e1;"></i>
                    <span class="current" style="font-weight:700;color:#1e293b;">Dashboard</span>
                </div>
            </div>
            <div class="z-header-right" style="display: flex; align-items: center; gap: 2rem;">
                <div style="display: flex; align-items: center; gap: 8px; background: #f0fdf4; padding: 6px 14px; border-radius: 100px; border: 1px solid #dcfce7; font-size: 0.75rem; font-weight: 700; color: #15803d;">
                    <div style="width: 8px; height: 8px; background: #22c55e; border-radius: 50%; box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);"></div>
                    <span>System Online</span>
                </div>
                <a href="<?php echo base_url('portal'); ?>" style="display:flex;align-items:center;gap:8px;padding:6px 16px;border-radius:100px;border:1px solid #bfdbfe;background:#eff6ff;color:#3b82f6;font-size:0.75rem;font-weight:800;text-decoration:none;transition:0.2s;white-space:nowrap;">
                    <i data-lucide="layout-grid" style="width:14px;height:14px;"></i> Portal HUB
                </a>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;background:linear-gradient(135deg,#0ea5e9 0%,#0284c7 100%);color:white;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;box-shadow:0 4px 12px rgba(14,165,233,0.2);"><?php echo $adminInitial; ?></div>
                    <div style="display:flex;flex-direction:column;">
                        <div style="font-size:0.85rem;font-weight:700;color:#1e293b;"><?php echo $adminNama; ?></div>
                        <div style="font-size:0.7rem;color:#94a3b8;">Super Administrator</div>
                    </div>
                </div>
                <a href="<?php echo base_url('logout'); ?>" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:10px;background:#fef2f2;color:#ef4444;text-decoration:none;transition:0.2s;border:1px solid #fee2e2;">
                    <i data-lucide="log-out" style="width:18px;height:18px;"></i>
                </a>
            </div>
        </header>

        <div class="z-scroll" style="padding: 2rem;">
            
            <?php if(isset($_SESSION['success'])): ?>
                <div style="background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                    <i data-lucide="check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['error'])): ?>
                <div style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                    <i data-lucide="alert-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Elite Page Header Banner -->
            <div class="elite-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: var(--z-radius, 12px); margin-bottom: 2rem; color: white; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="book-open" style="color: rgba(255,255,255,0.8);"></i> Daftar Koleksi E-Book
                    </h1>
                    <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Kelola seluruh koleksi buku digital / E-Book perpustakaan.</p>
                </div>
                <a href="<?php echo base_url('admin/perpus/tambah'); ?>" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(5px); color: white; text-decoration: none; padding: 12px 24px; border-radius: 12px; font-weight: 700; display: flex; align-items: center; gap: 8px; transition: 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                    <i data-lucide="plus-circle" style="width: 20px; height: 20px;"></i> Tambah Buku Baru
                </a>
            </div>
            
            <div style="background: white; border-radius: 16px; border: 1px solid #f1f5f9; padding: 1.5rem;">
                <h3 style="margin: 0 0 1.5rem; font-size: 1.1rem; color: #0f172a; display: flex; align-items: center; gap: 10px;">
                    <i data-lucide="book-open" style="color: #9333ea;"></i> Daftar Koleksi Buku (E-Library)
                </h3>
                
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 0.85rem; font-weight: 800; text-align: left;">
                            <th style="padding: 12px 16px;">ID</th>
                            <th style="padding: 12px 16px;">Cover</th>
                            <th style="padding: 12px 16px;">Judul Buku</th>
                            <th style="padding: 12px 16px;">Waktu Upload</th>
                            <th style="padding: 12px 16px; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($buku)): ?>
                        <tr>
                            <td colspan="5" style="padding: 30px; text-align: center; color: #94a3b8;">
                                <i data-lucide="inbox" style="width: 40px; height: 40px; margin-bottom: 10px; opacity: 0.5;"></i><br>
                                Belum ada koleksi buku.
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach($buku as $b): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9; font-size: 0.9rem;">
                                <td style="padding: 16px; font-weight: 600; color: #64748b;">#<?php echo $b['id']; ?></td>
                                <td style="padding: 16px;">
                                    <?php if($b['cover_image']): ?>
                                        <img src="<?php echo base_url('public/uploads/buku/'.$b['cover_image']); ?>" style="width: 50px; height: 65px; object-fit: cover; border-radius: 6px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                    <?php else: ?>
                                        <div style="width: 50px; height: 65px; background: #e2e8f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                                            <i data-lucide="image" style="width: 20px;"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 16px;">
                                    <div style="font-weight: 700; color: #0f172a; margin-bottom: 4px;"><?php echo htmlspecialchars($b['judul']); ?></div>
                                    <?php if(!empty($b['nama_kategori'])): ?>
                                        <div style="font-size: 0.7rem; font-weight: 700; color: #0ea5e9; margin-bottom: 6px; display: inline-block; background: #e0f2fe; padding: 2px 8px; border-radius: 4px;">
                                            <?php echo htmlspecialchars($b['nama_kategori']); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div style="font-size: 0.75rem; color: #64748b;"><?php echo htmlspecialchars(substr($b['deskripsi'], 0, 80)); ?>...</div>
                                </td>
                                <td style="padding: 16px; color: #475569; font-size: 0.85rem;">
                                    <?php echo date('d M Y H:i', strtotime($b['created_at'])); ?>
                                </td>
                                <td style="padding: 16px; text-align: right;">
                                    <a href="<?php echo base_url('public/uploads/buku/'.$b['file_pdf']); ?>" target="_blank" style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #eff6ff; color: #3b82f6; border-radius: 8px; margin-right: 5px;" title="Lihat PDF">
                                        <i data-lucide="external-link" style="width: 16px;"></i>
                                    </a>
                                    <a href="<?php echo base_url('admin/perpus/edit/'.$b['id']); ?>" style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #fef3c7; color: #d97706; border-radius: 8px; margin-right: 5px;" title="Edit Buku">
                                        <i data-lucide="edit" style="width: 16px;"></i>
                                    </a>
                                    <a href="<?php echo base_url('admin/perpus/hapus/'.$b['id']); ?>" onclick="return confirm('Yakin ingin menghapus buku ini?')" style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: #fef2f2; color: #ef4444; border-radius: 8px;">
                                        <i data-lucide="trash-2" style="width: 16px;"></i>
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
<?php
$content = ob_get_clean();
include __DIR__ . '/../siakad/layout.php';
?>
