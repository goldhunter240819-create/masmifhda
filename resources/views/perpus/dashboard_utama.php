<?php
$title = "Dashboard Utama Perpustakaan - Admin";
$active_module = 'perpus';
$active_page = 'dashboard_utama';
$adminNama = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Admin';
$adminInitial = strtoupper(substr($adminNama, 0, 2));
ob_start();
?>
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    corePlugins: { preflight: false },
    theme: {
      extend: {
        colors: {
          brand: {
            50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 
            500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e',
          }
        }
      }
    }
  }
</script>
<style>
  *, ::before, ::after { border-width: 0; border-style: solid; border-color: #e5e7eb; }
</style>

<div class="siakad-container">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>
    
    <div class="z-main">
        <header class="z-header" style="height: 80px; padding: 0 2rem; background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100;">
            <div class="z-header-left" style="display:flex;align-items:center;gap:15px;">
                <button class="z-hamburger" onclick="document.getElementById('zSidebar').classList.toggle('open')" style="background:none;border:none;cursor:pointer;color:#64748b;">
                    <i data-lucide="menu"></i>
                </button>
                <div class="breadcrumb">
                    <span class="parent">PERPUSTAKAAN DIGITAL</span>
                    <i data-lucide="chevron-right"></i>
                    <span class="current">Dashboard Utama</span>
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
        
        <div class="z-scroll" style="padding: 1.5rem 1rem;">
            <div class="elite-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: var(--z-radius, 12px); margin-bottom: 2rem; color: white; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="layout-dashboard" style="color: rgba(255,255,255,0.8);"></i> Dashboard Perpustakaan
                    </h1>
                    <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Ringkasan aktivitas E-Library dan Perpustakaan Fisik.</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Stat Card 1 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-brand-50 text-brand-600 rounded-xl">
                            <i data-lucide="book" class="w-6 h-6"></i>
                        </div>
                        <span class="text-xs font-bold text-brand-600 bg-brand-100 px-2 py-1 rounded-full">Fisik</span>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800 mb-1"><?= number_format($stats['total_fisik']) ?></h3>
                    <p class="text-sm text-slate-500 font-medium">Total Stok Buku Fisik</p>
                </div>
                
                <!-- Stat Card 2 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                            <i data-lucide="monitor-play" class="w-6 h-6"></i>
                        </div>
                        <span class="text-xs font-bold text-purple-600 bg-purple-100 px-2 py-1 rounded-full">Digital</span>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800 mb-1"><?= number_format($stats['total_ebook']) ?></h3>
                    <p class="text-sm text-slate-500 font-medium">Koleksi E-Book / Digital</p>
                </div>
                
                <!-- Stat Card 3 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                            <i data-lucide="arrow-right-left" class="w-6 h-6"></i>
                        </div>
                        <span class="text-xs font-bold text-amber-600 bg-amber-100 px-2 py-1 rounded-full">Sirkulasi</span>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800 mb-1"><?= number_format($stats['dipinjam']) ?></h3>
                    <p class="text-sm text-slate-500 font-medium">Buku Sedang Dipinjam</p>
                </div>
                
                <!-- Stat Card 4 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                            <i data-lucide="users" class="w-6 h-6"></i>
                        </div>
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-100 px-2 py-1 rounded-full">Hari Ini</span>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800 mb-1"><?= number_format($stats['kunjungan_hari_ini']) ?></h3>
                    <p class="text-sm text-slate-500 font-medium">Pengunjung Hari Ini</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Action Cards -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-center items-center text-center hover:shadow-md transition cursor-pointer" onclick="location.href='<?= base_url('admin/perpus/sirkulasi/pinjam') ?>'">
                    <div class="w-16 h-16 bg-brand-50 text-brand-600 rounded-2xl flex items-center justify-center mb-4">
                        <i data-lucide="scan" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Kasir Sirkulasi</h3>
                    <p class="text-sm text-slate-500 mb-4">Buka menu peminjaman dan pengembalian buku fisik (Scan Barcode).</p>
                    <span class="text-brand-600 font-bold text-sm">Buka Sirkulasi &rarr;</span>
                </div>
                
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-center items-center text-center hover:shadow-md transition cursor-pointer" onclick="window.open('<?= base_url('admin/perpus/kunjungan/scan') ?>', '_blank')">
                    <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-4">
                        <i data-lucide="focus" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Buku Tamu Kiosk</h3>
                    <p class="text-sm text-slate-500 mb-4">Buka mode fullscreen untuk ditaruh di PC depan (Pintu Masuk).</p>
                    <span class="text-indigo-600 font-bold text-sm">Buka Kiosk &rarr;</span>
                </div>
            </div>
            
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../siakad/layout.php';
?>
