<?php
$title = "Sirkulasi Peminjaman - Admin";
$active_module = 'perpus';
$active_page = 'sirkulasi';
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
                <span class="current">Sirkulasi & Peminjaman</span>
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
            <!-- Header & Action -->
            <div class="elite-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: var(--z-radius, 12px); margin-bottom: 2rem; color: white; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="arrow-right-left" style="color: rgba(255,255,255,0.8);"></i> Sirkulasi Buku
                    </h1>
                    <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Kelola data peminjaman dan pengembalian buku fisik.</p>
                </div>
                <a href="<?= base_url('admin/perpus/sirkulasi/pinjam') ?>" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(5px); color: white; padding: 12px 24px; border-radius: 12px; font-weight: 700; display: flex; align-items: center; gap: 8px; transition: 0.2s; text-decoration: none; border: none; cursor: pointer;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i> Transaksi Baru
                </a>
            </div>

            <!-- Stats/Filter -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl border border-slate-100 p-4 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center">
                        <i data-lucide="book-open" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <div class="text-sm text-slate-500 font-medium">Sedang Dipinjam</div>
                        <div class="text-2xl font-bold text-slate-800">12</div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-slate-100 p-4 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center">
                        <i data-lucide="alert-circle" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <div class="text-sm text-slate-500 font-medium">Terlambat</div>
                        <div class="text-2xl font-bold text-slate-800">3</div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-slate-100 p-4 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center">
                        <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <div class="text-sm text-slate-500 font-medium">Selesai Hari Ini</div>
                        <div class="text-2xl font-bold text-slate-800">5</div>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex flex-wrap justify-between items-center bg-slate-50 gap-4">
                    <div class="flex items-center gap-2">
                        <select class="border border-slate-200 rounded-lg text-sm px-3 py-2 text-slate-600 focus:outline-none focus:border-brand-500">
                            <option value="">Semua Status</option>
                            <option value="Pinjam">Sedang Dipinjam</option>
                            <option value="Kembali">Sudah Kembali</option>
                            <option value="Terlambat">Terlambat</option>
                        </select>
                    </div>
                    <div class="relative w-64">
                        <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 w-4 h-4"></i>
                        <input type="text" placeholder="Cari nama / ID transaksi..." class="w-full pl-9 pr-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-brand-500">
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-white text-slate-500 text-xs uppercase font-semibold border-b border-slate-100">
                            <tr>
                                <th class="px-4 py-4">ID Transaksi</th>
                                <th class="px-4 py-4">Peminjam</th>
                                <th class="px-4 py-4">Buku (Kode)</th>
                                <th class="px-4 py-4">Tgl Pinjam</th>
                                <th class="px-4 py-4">Tenggat</th>
                                <th class="px-4 py-4">Status</th>
                                <th class="px-4 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php if(!empty($transaksi)): ?>
                                <?php foreach($transaksi as $t): ?>
                                    <tr class="hover:bg-slate-50 transition group">
                                        <td class="px-4 py-3 font-medium text-brand-600">#<?= htmlspecialchars($t['kode_transaksi']) ?></td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-slate-700">ID: <?= htmlspecialchars($t['peminjam_id']) ?></div>
                                            <div class="text-xs text-slate-500"><?= htmlspecialchars($t['peminjam_type']) ?></div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-slate-700 line-clamp-1" title="<?= htmlspecialchars($t['judul']) ?>"><?= htmlspecialchars($t['judul'] ?? '-') ?></div>
                                            <div class="text-xs text-slate-500"><?= htmlspecialchars($t['kode_buku'] ?? '-') ?></div>
                                        </td>
                                        <td class="px-4 py-3 text-slate-600"><?= date('d M Y', strtotime($t['tgl_pinjam'])) ?></td>
                                        <td class="px-4 py-3 text-slate-600">
                                            <?= date('d M Y', strtotime($t['tgl_tenggat'])) ?>
                                            <?php if($t['status_buku'] == 'Pinjam' && strtotime($t['tgl_tenggat']) < time()): ?>
                                                <span class="ml-1 flex w-2 h-2 rounded-full bg-rose-500 absolute -translate-y-4 translate-x-16" title="Terlambat"></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <?php if($t['status_buku'] == 'Pinjam'): ?>
                                                <?php if(strtotime($t['tgl_tenggat']) < time()): ?>
                                                    <span class="px-2.5 py-1 bg-rose-50 text-rose-600 rounded-full text-xs font-semibold">Terlambat</span>
                                                <?php else: ?>
                                                    <span class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-semibold">Dipinjam</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-full text-xs font-semibold">Kembali</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <?php if($t['status_buku'] == 'Pinjam'): ?>
                                                <button class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium shadow-sm transition">
                                                    Proses Kembali
                                                </button>
                                            <?php else: ?>
                                                <button class="text-slate-400 hover:text-slate-600 transition" title="Detail">
                                                    <i data-lucide="eye" class="w-4 h-4 mx-auto"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center text-slate-500">
                                        <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-3 text-slate-300"></i>
                                        <div class="text-base font-medium text-slate-600">Belum Ada Transaksi</div>
                                        <div class="text-sm">Klik "Transaksi Baru" untuk memulai peminjaman.</div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../siakad/layout.php';
?>
