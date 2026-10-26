<?php
$title = "Buku Tamu Kunjungan - Admin";
$active_module = 'perpus';
$active_page = 'kunjungan';
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
                <span class="current">Log Kunjungan</span>
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
                        <i data-lucide="users" style="color: rgba(255,255,255,0.8);"></i> Buku Tamu Perpustakaan
                    </h1>
                    <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Rekap harian pengunjung perpustakaan fisik.</p>
                </div>
                <a href="<?= base_url('admin/perpus/kunjungan/scan') ?>" target="_blank" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(5px); color: white; padding: 12px 24px; border-radius: 12px; font-weight: 700; display: flex; align-items: center; gap: 8px; transition: 0.2s; text-decoration: none; border: none; cursor: pointer;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                    <i data-lucide="scan-line" class="w-5 h-5"></i> Buka Mode Scanner
                </a>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl border border-slate-100 p-4">
                    <div class="text-sm text-slate-500 font-medium mb-1">Total Hari Ini</div>
                    <div class="text-3xl font-bold text-slate-800 flex items-baseline gap-2">
                        24 <span class="text-xs text-emerald-500 font-semibold"><i data-lucide="trending-up" class="w-3 h-3 inline"></i> +12%</span>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-slate-100 p-4">
                    <div class="text-sm text-slate-500 font-medium mb-1">Siswa</div>
                    <div class="text-3xl font-bold text-slate-800">18</div>
                </div>
                <div class="bg-white rounded-xl border border-slate-100 p-4">
                    <div class="text-sm text-slate-500 font-medium mb-1">Guru/Pegawai</div>
                    <div class="text-3xl font-bold text-slate-800">4</div>
                </div>
                <div class="bg-white rounded-xl border border-slate-100 p-4">
                    <div class="text-sm text-slate-500 font-medium mb-1">Tamu Umum</div>
                    <div class="text-3xl font-bold text-slate-800">2</div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex flex-wrap justify-between items-center bg-slate-50 gap-4">
                    <div class="flex items-center gap-2">
                        <input type="date" value="<?= date('Y-m-d') ?>" class="border border-slate-200 rounded-lg text-sm px-3 py-2 text-slate-600 focus:outline-none focus:border-brand-500">
                    </div>
                    <div class="relative w-64">
                        <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 w-4 h-4"></i>
                        <input type="text" placeholder="Cari nama tamu..." class="w-full pl-9 pr-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-brand-500">
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-white text-slate-500 text-xs uppercase font-semibold border-b border-slate-100">
                            <tr>
                                <th class="px-4 py-4 w-20 text-center">Waktu</th>
                                <th class="px-4 py-4">Nama Pengunjung</th>
                                <th class="px-4 py-4">Kategori</th>
                                <th class="px-4 py-4">Tujuan / Keperluan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php if(!empty($tamu)): ?>
                                <?php foreach($tamu as $t): ?>
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-3 text-center">
                                            <div class="font-bold text-slate-700"><?= date('H:i', strtotime($t['waktu_masuk'])) ?></div>
                                            <div class="text-[10px] text-slate-400 uppercase"><?= date('d M', strtotime($t['waktu_masuk'])) ?></div>
                                        </td>
                                        <td class="px-4 py-3 font-medium text-slate-800">
                                            <?= htmlspecialchars($t['nama_tamu']) ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <?php if($t['peminjam_type'] == 'Siswa'): ?>
                                                <span class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-semibold">Siswa</span>
                                            <?php elseif($t['peminjam_type'] == 'Guru'): ?>
                                                <span class="px-2.5 py-1 bg-amber-50 text-amber-600 rounded-full text-xs font-semibold">Guru</span>
                                            <?php else: ?>
                                                <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-semibold">Tamu Umum</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 text-slate-500 italic">
                                            <?= htmlspecialchars($t['tujuan'] ?? 'Membaca di tempat') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="px-4 py-12 text-center text-slate-500">
                                        <i data-lucide="coffee" class="w-10 h-10 mx-auto mb-3 text-slate-300"></i>
                                        <div class="text-base font-medium text-slate-600">Belum Ada Kunjungan</div>
                                        <div class="text-sm">Buka mode scanner untuk membiarkan pengunjung melakukan absensi.</div>
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
