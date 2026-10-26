<?php
$title = "Transaksi Peminjaman - Admin";
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
                <a href="<?= base_url('admin/perpus/sirkulasi') ?>" class="parent hover:text-brand-500">Sirkulasi</a>
                <i data-lucide="chevron-right"></i>
                <span class="current">Pinjam Baru</span>
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
            <div class="flex items-center gap-4 mb-6">
                <a href="<?= base_url('admin/perpus/sirkulasi') ?>" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-brand-500 hover:border-brand-200 transition shadow-sm">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Transaksi Peminjaman (Kasir)</h2>
                    <p class="text-sm text-slate-500">Pilih peminjam dan buku yang akan dipinjam.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left Column: Form -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Peminjam -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                        <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <i data-lucide="user" class="text-brand-500 w-5 h-5"></i> 1. Identitas Peminjam
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Tipe Peminjam</label>
                                <select id="tipe_peminjam" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-brand-500">
                                    <option value="Siswa">Siswa</option>
                                    <option value="Guru">Guru / Pegawai</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Cari Nama / NIS / NIP</label>
                                <div class="relative">
                                    <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 w-4 h-4"></i>
                                    <select id="peminjam_id" class="w-full pl-9 pr-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-brand-500 bg-white">
                                        <option value="">-- Pilih Peminjam --</option>
                                        <optgroup label="Data Siswa Aktif">
                                            <?php foreach($siswa as $s): ?>
                                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nis'].' - '.$s['nama']) ?></option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Buku -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                        <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <i data-lucide="book-copy" class="text-brand-500 w-5 h-5"></i> 2. Buku yang Dipinjam
                        </h3>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Cari Judul / Kode Buku</label>
                            <div class="flex gap-2">
                                <div class="relative flex-1">
                                    <i data-lucide="barcode" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 w-4 h-4"></i>
                                    <select id="buku_id" class="w-full pl-9 pr-3 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-brand-500 bg-white">
                                        <option value="">-- Scan Barcode atau Pilih Buku --</option>
                                        <?php foreach($buku as $b): ?>
                                            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['kode_buku'].' - '.$b['judul']) ?> (Stok: <?= $b['stok'] ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2.5 rounded-lg font-medium transition whitespace-nowrap border border-slate-200">
                                    Tambahkan
                                </button>
                            </div>
                        </div>

                        <!-- Cart / List Pinjaman -->
                        <div class="border border-slate-200 rounded-lg overflow-hidden bg-slate-50">
                            <table class="w-full text-left text-sm text-slate-600">
                                <thead class="bg-slate-100/50 text-slate-500 text-xs uppercase font-semibold">
                                    <tr>
                                        <th class="px-4 py-3">Kode Buku</th>
                                        <th class="px-4 py-3">Judul Buku</th>
                                        <th class="px-4 py-3 text-center w-16">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="3" class="px-4 py-6 text-center text-slate-500 bg-white">
                                            <i data-lucide="shopping-cart" class="w-6 h-6 mx-auto mb-2 text-slate-300"></i>
                                            Belum ada buku yang dipilih.
                                        </td>
                                    </tr>
                                    <!-- Dynamic rows will go here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Summary & Submit -->
                <div class="space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 sticky top-24">
                        <h3 class="font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3 flex items-center gap-2">
                            <i data-lucide="clipboard-list" class="text-brand-500 w-5 h-5"></i> Ringkasan
                        </h3>
                        
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500">Total Buku:</span>
                                <span class="font-bold text-slate-800 text-lg">0</span>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Pinjam</label>
                                <input type="date" value="<?= date('Y-m-d') ?>" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-brand-500" readonly>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Batas Pengembalian</label>
                                <input type="date" value="<?= date('Y-m-d', strtotime('+3 days')) ?>" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-brand-500">
                                <div class="text-[11px] text-slate-500 mt-1">* Default 3 hari peminjaman</div>
                            </div>
                        </div>

                        <button class="w-full bg-brand-500 hover:bg-brand-600 text-white py-3 rounded-xl font-bold shadow-sm shadow-brand-500/30 transition flex items-center justify-center gap-2">
                            <i data-lucide="check-circle-2" class="w-5 h-5"></i> Simpan Transaksi
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
// Mockup UI behavior can be added here
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../siakad/layout.php';
?>
