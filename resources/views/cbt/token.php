<?php
$pageTitle = 'Token Ujian CBT';
$active_menu = 'token';
?>
<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/header.php'; ?>

        <div class="z-scroll" style="padding: 1.5rem 2rem 3rem;">

            <!-- Alerts -->
            <?php if(isset($_GET['msg']) && $_GET['msg'] === 'generated'): ?>
            <div style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                <i data-lucide="check-circle" style="width: 20px; height: 20px; color: #22c55e;"></i> Token ujian baru berhasil dirilis!
            </div>
            <?php endif; ?>

            <?php if(isset($_GET['msg']) && $_GET['msg'] === 'expired'): ?>
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 18px; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-weight: 700;">
                <i data-lucide="alert-triangle" style="width: 20px; height: 20px; color: #ef4444;"></i> Token aktif telah dihentikan/kadaluarsa.
            </div>
            <?php endif; ?>

            <!-- Elite Page Header -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-badge">AUTHENTICATION & ACCESS</div>
                        <h1><i data-lucide="key-round"></i> Token Pelaksanaan Ujian</h1>
                        <p>Generate kode token dinamis untuk autentikasi siswa masuk ke sesi ujian anti-cheat secara terpusat.</p>
                    </div>
                </div>
            </div>

            <!-- Active Token Card (Big Hero Display) -->
            <div class="card" style="padding: 2.5rem 2rem; border-radius: 20px; border: 1px solid #e2e8f0; margin-bottom: 2rem; text-align: center; background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
                <div style="font-size: 0.85rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 10px;">
                    TOKEN UJIAN AKTIF SAAT INI
                </div>

                <?php if (!empty($tokenAktif)): ?>
                    <div style="display: inline-block; padding: 1rem 3rem; background: #0f172a; color: #38bdf8; font-family: monospace; font-size: 3.5rem; font-weight: 900; letter-spacing: 8px; border-radius: 18px; box-shadow: 0 15px 30px rgba(15,23,42,0.25); border: 2px solid #38bdf8; margin-bottom: 1.25rem;">
                        <?php echo htmlspecialchars($tokenAktif['token']); ?>
                    </div>
                    
                    <div style="display: flex; justify-content: center; align-items: center; gap: 15px; margin-bottom: 1.75rem; flex-wrap: wrap;">
                        <span style="background: #dcfce7; color: #15803d; font-weight: 800; font-size: 0.82rem; padding: 4px 12px; border-radius: 100px; display: inline-flex; align-items: center; gap: 6px;">
                            <span class="dot pulse" style="width: 8px; height: 8px; background: #22c55e; border-radius: 50%;"></span> Status: Aktif
                        </span>
                        <span style="color: #64748b; font-size: 0.82rem; font-weight: 600;">
                            Dirilis pada: <?php echo date('H:i:s, d M Y', strtotime($tokenAktif['created_at'])); ?>
                        </span>
                        <span style="color: #64748b; font-size: 0.82rem; font-weight: 600;">
                            Masa Berlaku: <?php echo $tokenAktif['masa_berlaku_menit']; ?> Menit
                        </span>
                    </div>

                    <div style="display: flex; justify-content: center; gap: 10px; flex-wrap: wrap;">
                        <!-- Generate Baru -->
                        <form method="POST" action="<?php echo base_url('cbt/token/generate'); ?>">
                            <button type="submit" class="e-btn e-btn-primary" style="background: #4f46e5; padding: 10px 20px;">
                                <i data-lucide="refresh-cw" style="width: 16px; height: 16px;"></i> Rilis / Perbarui Token
                            </button>
                        </form>

                        <!-- Nonaktifkan Token -->
                        <form method="POST" action="<?php echo base_url('cbt/token/expire'); ?>" onsubmit="return confirm('Hentikan masa aktif token ini?')">
                            <input type="hidden" name="token_id" value="<?php echo $tokenAktif['id']; ?>">
                            <button type="submit" class="e-btn e-btn-dark" style="background: #fee2e2; color: #b91c1c; border-color: #fecaca; padding: 10px 20px;">
                                <i data-lucide="power" style="width: 16px; height: 16px;"></i> Hentikan Token
                            </button>
                        </form>
                    </div>

                <?php else: ?>
                    <div style="display: inline-block; padding: 1rem 3rem; background: #f1f5f9; color: #94a3b8; font-family: monospace; font-size: 3rem; font-weight: 800; letter-spacing: 6px; border-radius: 18px; border: 2px dashed #cbd5e1; margin-bottom: 1.25rem;">
                        TIDAK AKTIF
                    </div>
                    <p style="margin: 0 0 1.5rem 0; color: #64748b; font-size: 0.9rem;">
                        Belum ada token yang aktif. Siswa tidak dapat memulai ujian sebelum token dirilis oleh proktor / pengawas.
                    </p>
                    <form method="POST" action="<?php echo base_url('cbt/token/generate'); ?>">
                        <button type="submit" class="e-btn e-btn-primary" style="background: #4f46e5; padding: 11px 24px; font-size: 0.9rem;">
                            <i data-lucide="key" style="width: 16px; height: 16px;"></i> Rilis Token Sekarang
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Riwayat Token -->
            <div class="card" style="border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden; background: #ffffff;">
                <div style="padding: 1.25rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9;">
                    <div style="font-weight: 800; font-size: 0.95rem; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="history" style="width: 18px; height: 18px; color: #64748b;"></i>
                        Riwayat Token Sebelumnya
                    </div>
                </div>

                <div class="z-table-wrap">
                    <table class="z-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Kode Token</th>
                                <th>Masa Berlaku</th>
                                <th>Waktu Rilis</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($tokenHistory)): ?>
                            <tr>
                                <td colspan="5" style="padding: 2.5rem 1.5rem; text-align: center; color: #94a3b8;">
                                    Belum ada riwayat token sebelumnya.
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach($tokenHistory as $th): 
                                    $isAktif = ($th['status'] === 'Aktif');
                                ?>
                                <tr>
                                    <td style="color: #94a3b8; font-weight: 600;"><?php echo $no++; ?></td>
                                    <td>
                                        <span style="font-family: monospace; font-size: 1.1rem; font-weight: 900; color: <?php echo $isAktif ? '#4338ca' : '#64748b'; ?>; background: <?php echo $isAktif ? '#e0e7ff' : '#f1f5f9'; ?>; padding: 2px 10px; border-radius: 6px; letter-spacing: 2px;">
                                            <?php echo htmlspecialchars($th['token']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo $th['masa_berlaku_menit']; ?> Menit
                                    </td>
                                    <td style="color: #64748b;">
                                        <?php echo date('d M Y, H:i:s', strtotime($th['created_at'])); ?> WIB
                                    </td>
                                    <td>
                                        <span style="font-size: 0.72rem; font-weight: 800; padding: 3px 9px; border-radius: 6px; <?php echo $isAktif ? 'background:#dcfce7;color:#15803d;' : 'background:#f1f5f9;color:#64748b;'; ?>">
                                            <?php echo htmlspecialchars($th['status']); ?>
                                        </span>
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

