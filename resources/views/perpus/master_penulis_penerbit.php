<?php
$title = "Master Penulis & Penerbit - Admin";
$active_module = 'perpus';
$active_page = 'penulis_penerbit';
$adminNama = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Admin';
$adminInitial = strtoupper(substr($adminNama, 0, 2));
ob_start();
?>
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    corePlugins: {
      preflight: false,
    },
    theme: {
      extend: {
        colors: {
          brand: {
            50: '#f0f9ff',
            100: '#e0f2fe',
            200: '#bae6fd',
            300: '#7dd3fc',
            400: '#38bdf8',
            500: '#0ea5e9',
            600: '#0284c7',
            700: '#0369a1',
            800: '#075985',
            900: '#0c4a6e',
          }
        }
      }
    }
  }
</script>
<style>
  /* Fix tailwind preflight missing border defaults */
  *, ::before, ::after { border-width: 0; border-style: solid; border-color: #e5e7eb; }
</style>

<div class="siakad-container">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>
    
    <div class="z-main">
        <header class="z-header" style="height: 80px; padding: 0 2rem; background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100;"><div class="z-header-left" style="display:flex;align-items:center;gap:15px;"><button class="z-hamburger" onclick="document.getElementById('zSidebar').classList.toggle('open')" style="background:none;border:none;cursor:pointer;color:#64748b;"><i data-lucide="menu"></i></button><div class="breadcrumb">
                <span class="parent">PERPUSTAKAAN DIGITAL</span>
                <i data-lucide="chevron-right"></i>
                <span class="current">Penulis & Penerbit</span>
            </div></div>            <div class="z-header-right" style="display: flex; align-items: center; gap: 2rem;">
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
        </header><div class="z-scroll" style="padding: 1.5rem 1rem;">
            <div class="elite-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: var(--z-radius, 12px); margin-bottom: 2rem; color: white; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="users" style="color: rgba(255,255,255,0.8);"></i> Data Penulis & Penerbit
                    </h1>
                    <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Kelola referensi master data penulis dan penerbit buku.</p>
                </div>
            </div>
            
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Data Penulis -->
                <div class="flex-1">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <i data-lucide="pen-tool" class="text-brand-500"></i> Data Penulis
                        </h2>
                        <button class="bg-brand-50 hover:bg-brand-100 text-brand-600 px-3 py-1.5 rounded-lg text-sm font-medium transition flex items-center gap-1">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah
                        </button>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-600">
                                <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold">
                                    <tr>
                                        <th class="px-4 py-3 w-10 text-center">No</th>
                                        <th class="px-4 py-3">Nama Penulis</th>
                                        <th class="px-4 py-3 text-center w-20">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php if(!empty($penulis)): ?>
                                        <?php $no=1; foreach($penulis as $p): ?>
                                            <tr class="hover:bg-slate-50 transition">
                                                <td class="px-4 py-3 text-center text-slate-400"><?= $no++ ?></td>
                                                <td class="px-4 py-3 font-medium text-slate-700"><?= htmlspecialchars($p['nama_penulis']) ?></td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center justify-center gap-2">
                                                        <button class="text-slate-400 hover:text-brand-500 transition" title="Edit"><i data-lucide="edit-2" class="w-3.5 h-3.5"></i></button>
                                                        <button class="text-slate-400 hover:text-rose-500 transition" title="Hapus"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="3" class="px-4 py-6 text-center text-slate-500">Data masih kosong.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Data Penerbit -->
                <div class="flex-1">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <i data-lucide="building-2" class="text-brand-500"></i> Data Penerbit
                        </h2>
                        <button class="bg-brand-50 hover:bg-brand-100 text-brand-600 px-3 py-1.5 rounded-lg text-sm font-medium transition flex items-center gap-1">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah
                        </button>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-600">
                                <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold">
                                    <tr>
                                        <th class="px-4 py-3 w-10 text-center">No</th>
                                        <th class="px-4 py-3">Nama Penerbit</th>
                                        <th class="px-4 py-3 text-center w-20">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php if(!empty($penerbit)): ?>
                                        <?php $no=1; foreach($penerbit as $pb): ?>
                                            <tr class="hover:bg-slate-50 transition">
                                                <td class="px-4 py-3 text-center text-slate-400"><?= $no++ ?></td>
                                                <td class="px-4 py-3 font-medium text-slate-700">
                                                    <?= htmlspecialchars($pb['nama_penerbit']) ?>
                                                    <?php if($pb['telepon']): ?>
                                                        <div class="text-xs text-slate-400"><i data-lucide="phone" class="w-3 h-3 inline"></i> <?= htmlspecialchars($pb['telepon']) ?></div>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center justify-center gap-2">
                                                        <button class="text-slate-400 hover:text-brand-500 transition" title="Edit"><i data-lucide="edit-2" class="w-3.5 h-3.5"></i></button>
                                                        <button class="text-slate-400 hover:text-rose-500 transition" title="Hapus"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="3" class="px-4 py-6 text-center text-slate-500">Data masih kosong.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../siakad/layout.php';
?>
