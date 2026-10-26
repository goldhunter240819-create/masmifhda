<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="siakad-main">
        <!-- Developer Header -->
        <?php include __DIR__ . '/header.php'; ?>

        <div class="card" style="margin-bottom: 2rem; background: transparent; padding: 1.5rem; border: none; box-shadow: none;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 style="margin: 0; display: flex; align-items: center; gap: 10px; color: #0ea5e9; text-shadow: 0 0 15px rgba(16,185,129,0.5);">
                        <i data-lucide="key"></i> Keymaster Pro
                    </h2>
                    <p style="opacity: 0.7; margin-top: 5px; color: #94a3b8;">Sistem manajemen Lisensi dan Kode Aktivasi Master Aula System.</p>
                </div>
                <button onclick="document.getElementById('add-license-modal').style.display='flex'" class="btn btn-primary" style="padding: 10px 20px; font-weight: bold;">
                    <i data-lucide="plus"></i> GENERATE LICENSE
                </button>
            </div>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid rgba(14, 165, 233, 0.3); box-shadow: 0 0 20px rgba(16,185,129,0.1);">
                <i data-lucide="check-circle" style="vertical-align: -5px; width: 20px; height: 20px;"></i> Operasi Lisensi Berhasil!
            </div>
        <?php endif; ?>

        <div class="card" style="padding: 0; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="padding: 15px 20px; text-align: left; text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem;">Activation Key</th>
                        <th style="padding: 15px 20px; text-align: left; text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem;">Domain / Klien</th>
                        <th style="padding: 15px 20px; text-align: left; text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem;">Status</th>
                        <th style="padding: 15px 20px; text-align: left; text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem;">Valid Hingga</th>
                        <th style="padding: 15px 20px; text-align: right; text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($licenses)): ?>
                        <tr><td colspan="5" style="text-align:center; padding: 30px; color:#64748b; font-style:italic;">Belum ada lisensi yang digenerate.</td></tr>
                    <?php else: ?>
                        <?php foreach($licenses as $l): ?>
                        <tr style="transition: all 0.3s ease;">
                            <td style="padding: 15px 20px;">
                                <code style="background: #000; padding: 6px 10px; border-radius: 6px; color: #0ea5e9; font-weight: bold; letter-spacing: 1.5px; font-size: 0.9rem; border: 1px solid rgba(16,185,129,0.3); text-shadow: 0 0 5px rgba(16,185,129,0.5);">
                                    <?php echo $l['activation_code']; ?>
                                </code>
                            </td>
                            <td style="padding: 15px 20px; color: #cbd5e1; font-weight: 500;">
                                <i data-lucide="globe" style="width: 14px; height: 14px; color: #64748b; margin-right:5px; vertical-align:-2px;"></i> <?php echo $l['domain'] ?: 'Belum Terikat'; ?>
                            </td>
                            <td style="padding: 15px 20px;">
                                <?php if($l['status'] == 'aktif'): ?>
                                    <span style="background: rgba(16,185,129,0.1); color: #34d399; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; border: 1px solid rgba(16,185,129,0.3); box-shadow: 0 0 10px rgba(16,185,129,0.2);">
                                        <i data-lucide="check-circle-2" style="width: 12px; height: 12px; vertical-align:-2px;"></i> Aktif
                                    </span>
                                <?php else: ?>
                                    <span style="background: rgba(239,68,68,0.1); color: #f87171; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; border: 1px solid rgba(239,68,68,0.3);">
                                        <i data-lucide="x-circle" style="width: 12px; height: 12px; vertical-align:-2px;"></i> Revoked
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 15px 20px; font-size: 0.85rem; color: #94a3b8;">
                                <?php echo date('d M Y', strtotime($l['expired_at'])); ?>
                            </td>
                            <td style="padding: 15px 20px; text-align: right;">
                                <?php if($l['status'] == 'aktif'): ?>
                                <form action="<?php echo base_url('dev-panel/licenses/save'); ?>" method="POST" onsubmit="return confirm('Peringatan: Mencabut lisensi ini akan mengunci semua aplikasi milik klien. Lanjutkan?');">
                                    <input type="hidden" name="revoke" value="1">
                                    <input type="hidden" name="id" value="<?php echo $l['id']; ?>">
                                    <button type="submit" class="btn" style="background: rgba(239,68,68,0.1); color: #f87171; border: 1px solid rgba(239,68,68,0.3); padding: 8px 15px; font-size: 0.85rem; border-radius: 8px; transition: 0.3s; box-shadow: 0 0 10px rgba(239,68,68,0.1);">
                                        <i data-lucide="power-off" style="width: 14px; height: 14px; vertical-align:-2px;"></i> REVOKE
                                    </button>
                                </form>
                                <?php else: ?>
                                    <span style="font-size: 0.8rem; color: #475569; font-style: italic;">Tidak aktif</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Developer Footer -->
        <?php include __DIR__ . '/footer.php'; ?>
    </div>
</div>

<!-- Modal Generate -->
<div id="add-license-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:1000; align-items:center; justify-content:center; backdrop-filter: blur(8px);">
    <div class="card" style="width: 100%; max-width: 450px; padding: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; margin-bottom: 20px;">
            <h3 style="margin: 0; color: #0ea5e9; font-size: 1.3rem;">Generate New License</h3>
            <button onclick="document.getElementById('add-license-modal').style.display='none'" style="background:rgba(255,255,255,0.1); color:white; border:none; width:30px; height:30px; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:0.3s;"><i data-lucide="x" style="width:16px;height:16px;"></i></button>
        </div>
        <form action="<?php echo base_url('dev-panel/licenses/save'); ?>" method="POST">
            <input type="hidden" name="generate" value="1">
            
            <div style="margin-bottom: 20px;">
                <label style="display:block; margin-bottom:5px; font-size:0.85rem; color:#cbd5e1;">Domain Target / IP Klien (Hardware ID)</label>
                <input type="text" name="domain" placeholder="Contoh: 192.168.1.100 atau smpn1.sch.id" required class="form-control" style="width:100%; padding:10px 12px; border-radius:8px;">
                <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 5px;">*Klien dapat melihat Domain/IP mereka di halaman aktivasi (SECURITY_GATE).</div>
            </div>
            
            <div style="margin-bottom: 25px;">
                <label style="display:block; margin-bottom:5px; font-size:0.85rem; color:#cbd5e1;">Batas Waktu (Expired At)</label>
                <input type="date" name="expired_at" value="<?php echo date('Y-m-d', strtotime('+1 year')); ?>" required class="form-control" style="width:100%; padding:10px 12px; border-radius:8px;">
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; padding: 12px; font-weight:bold; letter-spacing:1px;"><i data-lucide="key-round"></i> GENERATE NOW</button>
        </form>
    </div>
</div>

<script>lucide.createIcons();</script>


