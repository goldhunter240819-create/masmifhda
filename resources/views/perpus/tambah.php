<?php
$title = "Tambah Buku - Admin";
$active_module = 'perpus';
$active_page = 'tambah';
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
                    <a href="<?php echo base_url('admin/perpus'); ?>" class="parent" style="font-weight:800;letter-spacing:1px;font-size:0.8rem;color:#9333ea;text-decoration:none;">PERPUSTAKAAN DIGITAL</a>
                    <i data-lucide="chevron-right" class="sep" style="width:14px;color:#cbd5e1;"></i>
                    <span class="current" style="font-weight:700;color:#1e293b;">Tambah Buku</span>
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
            
            <?php if(isset($_SESSION['error'])): ?>
                <div style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                    <i data-lucide="alert-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div style="background: white; border-radius: 16px; border: 1px solid #f1f5f9; padding: 2rem; max-width: 700px; margin: 0 auto;">
                <h3 style="margin: 0 0 1.5rem; font-size: 1.2rem; color: #0f172a; display: flex; align-items: center; gap: 10px;">
                    <i data-lucide="book-plus" style="color: #9333ea;"></i> Tambah Koleksi Buku Baru
                </h3>
                
                <form action="<?php echo base_url('admin/perpus/tambah'); ?>" method="POST" enctype="multipart/form-data">
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #475569; margin-bottom: 6px;">Judul Buku <span style="color:red;">*</span></label>
                        <input type="text" name="judul" required style="width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.95rem; font-family: inherit; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#9333ea'" onblur="this.style.borderColor='#cbd5e1'">
                    </div>

                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #475569; margin-bottom: 6px;">Kategori Buku</label>
                        <select name="kategori_id" style="width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.95rem; font-family: inherit; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#9333ea'" onblur="this.style.borderColor='#cbd5e1'">
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach($kategoriList as $kat): ?>
                                <option value="<?php echo $kat['id']; ?>"><?php echo htmlspecialchars($kat['nama_kategori']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #475569; margin-bottom: 6px;">Deskripsi / Sinopsis Singkat</label>
                        <textarea name="deskripsi" rows="4" style="width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.95rem; font-family: inherit; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#9333ea'" onblur="this.style.borderColor='#cbd5e1'"></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #475569; margin-bottom: 6px;">File PDF <span style="color:red;">*</span></label>
                            <div style="border: 2px dashed #cbd5e1; padding: 20px; border-radius: 12px; text-align: center; background: #f8fafc;">
                                <i data-lucide="file-text" style="color: #ef4444; width: 32px; height: 32px; margin-bottom: 10px;"></i>
                                <input type="file" name="file_pdf" accept="application/pdf" required style="width: 100%; font-size: 0.8rem; color: #64748b;">
                                <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 5px;">Hanya format .pdf</div>
                            </div>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #475569; margin-bottom: 6px;">Cover Buku (Opsional)</label>
                            <div style="border: 2px dashed #cbd5e1; padding: 20px; border-radius: 12px; text-align: center; background: #f8fafc;">
                                <i data-lucide="image" style="color: #3b82f6; width: 32px; height: 32px; margin-bottom: 10px;"></i>
                                <input type="file" name="cover_image" accept="image/jpeg, image/png" style="width: 100%; font-size: 0.8rem; color: #64748b;">
                                <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 5px;">Format .jpg / .png</div>
                            </div>
                        </div>
                    </div>

                    <div style="border-top: 1px solid #e2e8f0; margin-top: 2rem; padding-top: 1.5rem; display: flex; justify-content: flex-end; gap: 1rem;">
                        <a href="<?php echo base_url('admin/perpus'); ?>" style="padding: 10px 20px; border: 1px solid #cbd5e1; border-radius: 8px; color: #475569; font-weight: 600; text-decoration: none;">Batal</a>
                        <button type="submit" style="background: #9333ea; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 700; font-size: 0.95rem; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(147,51,234,0.2);">
                            <i data-lucide="save" style="width: 16px; height: 16px;"></i> Simpan Buku
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../siakad/layout.php';
?>
