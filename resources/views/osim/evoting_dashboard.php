<?php include __DIR__ . '/_layout_top.php'; ?>

<div class="elite-page-header" style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);">
    <div class="header-bg-shapes"></div>
    <div class="header-content-inner">
        <div>
            <span class="header-badge" style="background: rgba(255,255,255,0.2); color: #fff; border-color: rgba(255,255,255,0.3);">
                MODUL E-VOTING OSIM
            </span>
            <div class="header-text-premium">
                <h1 style="color:#fff; font-size: 2rem; margin: 0;"><i data-lucide="vote" style="color:#fff; vertical-align: middle;"></i> Dashboard E-Voting</h1>
                <p style="color: rgba(255,255,255,0.85); margin-top: 10px; font-size: 1.05rem;">
                    Pantau perolehan suara pemilihan ketua dan wakil ketua OSIM.
                </p>
            </div>
        </div>
        <div class="header-actions-premium" style="display:flex; gap:12px; margin-top: 15px;">
            <?php if($status_evoting == 'open'): ?>
                <a href="<?= base_url('osim/evoting/set?key=evoting_status&val=closed') ?>" class="e-btn" style="background: #ef4444; color: #fff; box-shadow: 0 10px 20px rgba(239,68,68,0.3);">
                    <i data-lucide="lock"></i> Tutup TPS
                </a>
            <?php else: ?>
                <a href="<?= base_url('osim/evoting/set?key=evoting_status&val=open') ?>" class="e-btn" style="background: #10b981; color: #fff; box-shadow: 0 10px 20px rgba(16,185,129,0.3);">
                    <i data-lucide="unlock"></i> Buka TPS
                </a>
            <?php endif; ?>
            
            <a href="<?= base_url('osim/evoting/kandidat') ?>" class="e-btn e-btn-glass" style="color:#fff; border-color: rgba(255,255,255,0.4);">
                <i data-lucide="users"></i> Data Kandidat
            </a>
            <a href="<?= base_url('apk/osim_tps_scanner.php') ?>" target="_blank" class="e-btn e-btn-glass" style="color:#fff; border-color: rgba(255,255,255,0.4);">
                <i data-lucide="scan-line"></i> Buka Scanner
            </a>
            <a href="<?= base_url('apk/osim_tps_monitor.php') ?>" target="_blank" class="e-btn e-btn-glass" style="color:#fff; border-color: rgba(255,255,255,0.4);">
                <i data-lucide="monitor-play"></i> Buka Layar
            </a>
        </div>
    </div>
</div>

<div class="z-stats" style="grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
    <!-- Pemilih Berhasil -->
    <div class="z-stat" style="border-radius: 20px;">
        <div class="z-stat-left">
            <div class="z-stat-label">Siswa Memilih</div>
            <div class="z-stat-value"><?= number_format($total_pemilih) ?></div>
            <div class="z-stat-trend text-muted" style="font-size: 0.8rem; margin-top: 5px;">Siswa telah menggunakan hak suara</div>
        </div>
        <div class="z-stat-icon zi-blue" style="width: 52px; height: 52px;"><i data-lucide="users"></i></div>
    </div>
    
    <!-- Total Suara Masuk -->
    <div class="z-stat" style="border-radius: 20px;">
        <div class="z-stat-left">
            <div class="z-stat-label">Total Suara Masuk</div>
            <div class="z-stat-value" style="color: #10b981;"><?= number_format($total_suara) ?></div>
            <div class="z-stat-trend text-muted" style="font-size: 0.8rem; margin-top: 5px;">Surat suara tersimpan di kotak suara</div>
        </div>
        <div class="z-stat-icon zi-green" style="width: 52px; height: 52px;"><i data-lucide="box"></i></div>
    </div>

    <!-- Kotak Suara Status -->
    <div class="z-stat" style="border-radius: 20px;">
        <div class="z-stat-left">
            <div class="z-stat-label">Status Kotak Suara</div>
            <div class="z-stat-value" style="color: <?= $status_kotak == 'open' ? '#3b82f6' : '#ef4444' ?>; font-size: 1.5rem;">
                <?= $status_kotak == 'open' ? 'TERBUKA' : 'TERKUNCI' ?>
            </div>
            <div class="z-stat-trend text-muted" style="font-size: 0.8rem; margin-top: 5px;">
                <?= $status_kotak == 'open' ? 'Hasil perolehan ditampilkan di layar' : 'Hasil perolehan disembunyikan' ?>
            </div>
        </div>
        <div class="z-stat-icon <?= $status_kotak == 'open' ? 'zi-blue' : 'zi-red' ?>" style="width: 52px; height: 52px;">
            <i data-lucide="<?= $status_kotak == 'open' ? 'unlock' : 'lock' ?>"></i>
        </div>
    </div>
</div>

<div class="z-panel" style="border-radius: 24px; padding: 25px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h3 style="margin:0; font-size: 1.25rem;">Live Count TPS</h3>
            <p style="margin:5px 0 0 0; color: #64748b; font-size: 0.85rem;">Status kotak suara menentukan apakah hasil bisa dilihat di layar proyektor.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <?php if($status_kotak == 'closed'): ?>
                <a href="<?= base_url('osim/evoting/set?key=evoting_kotak&val=open') ?>" class="btn" style="background: #3b82f6; color: white; border-radius: 12px; font-weight: 700;">
                    <i data-lucide="unlock" style="width: 16px;"></i> Buka Kotak Suara (Tampilkan Hasil)
                </a>
            <?php else: ?>
                <a href="<?= base_url('osim/evoting/set?key=evoting_kotak&val=closed') ?>" class="btn" style="background: #ef4444; color: white; border-radius: 12px; font-weight: 700;">
                    <i data-lucide="lock" style="width: 16px;"></i> Kunci Kotak Suara (Sembunyikan Hasil)
                </a>
            <?php endif; ?>
            <button onclick="confirmReset()" class="btn" style="background: #fff; color: #ef4444; border: 1px solid #fca5a5; border-radius: 12px; font-weight: 700;">
                <i data-lucide="rotate-ccw" style="width: 16px;"></i> Reset Suara
            </button>
        </div>
    </div>

    <!-- Tampilkan Perolehan Suara Jika Buka -->
    <?php if($status_kotak == 'open'): ?>
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            <?php foreach($kandidat as $k): 
                $persentase = ($total_suara > 0) ? round(($k['perolehan'] / $total_suara) * 100, 1) : 0;
            ?>
            <div style="flex: 1; min-width: 280px; border: 1px solid #e2e8f0; border-radius: 20px; overflow: hidden;">
                <div style="background: #f8fafc; padding: 20px; text-align: center; border-bottom: 1px solid #e2e8f0;">
                    <?php if($k['foto']): ?>
                        <img src="<?= base_url('public/uploads/osim/'.$k['foto']) ?>" style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%; box-shadow: 0 10px 20px rgba(0,0,0,0.1); margin-bottom: 15px;">
                    <?php else: ?>
                        <div style="width: 120px; height: 120px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto;">
                            <i data-lucide="users" style="width: 50px; height: 50px; color: #94a3b8;"></i>
                        </div>
                    <?php endif; ?>
                    <div style="font-size: 1.1rem; font-weight: 900; color: #0f172a;">Paslon <?= $k['nomor_urut'] ?></div>
                    <div style="font-size: 0.9rem; color: #475569; font-weight: 700;"><?= htmlspecialchars($k['nama_ketua']) ?> & <?= htmlspecialchars($k['nama_wakil']) ?></div>
                </div>
                <div style="padding: 20px; text-align: center; background: #fff;">
                    <div style="font-size: 0.85rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Perolehan Suara</div>
                    <div style="font-size: 2.5rem; font-weight: 900; color: #3b82f6; margin: 5px 0;"><?= number_format($k['perolehan']) ?></div>
                    
                    <div style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 8px; margin-top: 15px; overflow: hidden;">
                        <div style="width: <?= $persentase ?>%; height: 100%; background: linear-gradient(to right, #3b82f6, #60a5fa); border-radius: 8px;"></div>
                    </div>
                    <div style="font-size: 0.85rem; font-weight: 800; color: #64748b; margin-top: 8px;"><?= $persentase ?>% dari Total Suara</div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px; background: #f8fafc; border-radius: 16px; border: 1px dashed #cbd5e1;">
            <i data-lucide="lock" style="width: 64px; height: 64px; color: #cbd5e1; margin-bottom: 15px;"></i>
            <h3 style="margin: 0; color: #475569;">Kotak Suara Terkunci</h3>
            <p style="margin: 5px 0 0 0; color: #64748b;">Perolehan suara saat ini sedang disembunyikan. Buka kotak suara untuk melihat hasilnya.</p>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmReset() {
    Swal.fire({
        title: 'Apakah Anda Yakin?',
        text: "Semua suara masuk dan data pemilih akan dihapus! Proses ini tidak dapat dibatalkan.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#cbd5e1',
        confirmButtonText: 'Ya, Reset Total!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "<?= base_url('osim/evoting/reset') ?>";
        }
    })
}
</script>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
