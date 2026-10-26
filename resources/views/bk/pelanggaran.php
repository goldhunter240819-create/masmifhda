<?php
$title = $title ?? 'Catat Poin Siswa | MASMIFHDA';
$active_page = $activeMenu ?? 'bk_pelanggaran';
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
                    <span class="z-header-title">Catat Poin Siswa</span>
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
                            <i data-lucide="clipboard-list" style="color: rgba(255,255,255,0.8);"></i> Catat Poin Siswa
                        </h1>
                        <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Input pelanggaran atau prestasi siswa untuk tahun ajaran ini.</p>
                    </div>
                    <div>
                        <button onclick="document.getElementById('modalAdd').classList.add('open')" style="background: white; color: #0284c7; border: none; padding: 0.75rem 1.25rem; border-radius: 12px; font-weight: 700; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: 0.2s;">
                            <i data-lucide="plus" style="width:18px;height:18px;"></i> Input Poin Baru
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
                <th>Tanggal</th>
                <th>Siswa</th>
                <th>Kategori</th>
                <th>Keterangan</th>
                <th>Poin</th>
                <th>Pencatat</th>
                <th style="width: 80px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($riwayat as $r): ?>
            <tr>
                <td><?php echo date('d/m/Y', strtotime($r['tanggal'])); ?></td>
                <td>
                    <div style="font-weight:600; color:#1e293b;"><?php echo htmlspecialchars($r['nama_siswa']); ?></div>
                    <div style="font-size:0.75rem; color:#64748b;"><?php echo htmlspecialchars($r['nama_kelas']); ?></div>
                </td>
                <td><?php echo htmlspecialchars($r['nama_kategori']); ?></td>
                <td><?php echo htmlspecialchars($r['keterangan']); ?></td>
                <td style="font-weight: bold; color: <?php echo $r['tipe'] == 'pelanggaran' ? '#b91c1c' : '#15803d'; ?>;">
                    <?php echo $r['tipe'] == 'pelanggaran' ? '+' : '-'; ?><?php echo $r['poin']; ?>
                </td>
                <td style="font-size:0.85rem; color:#64748b;"><?php echo htmlspecialchars($r['nama_guru']); ?></td>
                <td>
                    <a href="<?php echo base_url('bk/pelanggaran/delete/' . $r['id']); ?>" class="z-btn" style="background:#fee2e2; color:#dc2626; padding: 4px 8px; font-size:0.8rem;" onclick="return confirm('Hapus riwayat poin ini?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($riwayat)): ?>
                <tr><td colspan="7" style="text-align:center;">Belum ada catatan poin.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Input -->
<div id="modalAdd" class="z-modal-ov">
    <div class="z-modal">
        <div class="z-modal-head">
            <h3 class="z-modal-title"><i data-lucide="plus-circle"></i> Input Poin Baru</h3>
            <button class="z-modal-close" onclick="document.getElementById('modalAdd').classList.remove('open')"><i data-lucide="x"></i></button>
        </div>
        <form action="<?php echo base_url('bk/pelanggaran/save'); ?>" method="POST">
            <div class="z-modal-body">
                <div class="z-form-group">
                    <label class="z-label">Pilih Kelas</label>
                    <select id="pilih_kelas" class="z-input" onchange="filterSiswa()">
                        <option value="">-- Pilih Kelas --</option>
                        <?php 
                        $kelasUniq = [];
                        foreach($siswaList as $s) {
                            $kelasUniq[$s['kelas_id']] = $s['nama_kelas'];
                        }
                        foreach($kelasUniq as $id => $nama_kelas): ?>
                            <option value="<?php echo htmlspecialchars($id); ?>"><?php echo htmlspecialchars($nama_kelas); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="z-form-group">
                    <label class="z-label">Pilih Siswa</label>
                    <select name="siswa_id" id="pilih_siswa" class="z-input" required disabled>
                        <option value="">-- Pilih Kelas Terlebih Dahulu --</option>
                    </select>
                </div>

                <div class="z-form-group">
                    <label class="z-label">Tanggal</label>
                    <input type="date" name="tanggal" class="z-input" required value="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="z-form-group">
                    <label class="z-label">Tipe Kategori</label>
                    <select id="pilih_tipe" class="z-input" onchange="filterKategori()" required>
                        <option value="">-- Pilih Tipe --</option>
                        <option value="pelanggaran">Pelanggaran (Poin Plus)</option>
                        <option value="prestasi">Prestasi (Poin Minus)</option>
                    </select>
                </div>

                <div class="z-form-group">
                    <label class="z-label">Detail Poin (Pelanggaran / Prestasi)</label>
                    <select name="kategori_id" id="pilih_kategori" class="z-input" required disabled>
                        <option value="">-- Pilih Tipe Terlebih Dahulu --</option>
                    </select>
                </div>

                <div class="z-form-group">
                    <label class="z-label">Keterangan Tambahan (Opsional)</label>
                    <textarea name="keterangan" class="z-input" rows="3" placeholder="Misal: Terlambat 15 menit, dipulangkan dll"></textarea>
                </div>
            </div>
            <div class="z-modal-foot">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('modalAdd').classList.remove('open')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Poin</button>
            </div>
        </form>
    </div>
</div>

<script>
    const dataSiswa = <?php echo json_encode($siswaList); ?>;
    const dataKategori = <?php echo json_encode($kategoriList); ?>;

    function filterSiswa() {
        const kelasId = document.getElementById('pilih_kelas').value;
        const selectSiswa = document.getElementById('pilih_siswa');
        
        selectSiswa.innerHTML = '<option value="">-- Pilih Siswa --</option>';
        if(!kelasId) {
            selectSiswa.disabled = true;
            return;
        }

        const filteredSiswa = dataSiswa.filter(s => s.kelas_id == kelasId);
        filteredSiswa.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = s.nama_siswa;
            selectSiswa.appendChild(opt);
        });
        selectSiswa.disabled = false;
    }

    function filterKategori() {
        const tipe = document.getElementById('pilih_tipe').value;
        const selectKat = document.getElementById('pilih_kategori');
        
        selectKat.innerHTML = '<option value="">-- Pilih Kategori --</option>';
        if(!tipe) {
            selectKat.disabled = true;
            return;
        }

        const filteredKat = dataKategori.filter(k => k.tipe === tipe);
        filteredKat.forEach(k => {
            const opt = document.createElement('option');
            opt.value = k.id;
            opt.textContent = `${k.nama_kategori} (${k.poin} Poin)`;
            selectKat.appendChild(opt);
        });
        selectKat.disabled = false;
    }
</script>

</div>
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
