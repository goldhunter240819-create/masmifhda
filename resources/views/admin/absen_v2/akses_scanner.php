<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="z-main">
        <header class="z-header" style="height: 80px; padding: 0 2rem; background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100;">
            <div class="z-header-left" style="display:flex;align-items:center;gap:15px;">
                <button class="z-hamburger" onclick="zToggleSidebar()" style="background:none;border:none;cursor:pointer;color:#64748b;">
                    <i data-lucide="menu"></i>
                </button>
                <div class="header-breadcrumb" style="display:flex;align-items:center;gap:10px;font-size:0.9rem;">
                    <span style="font-weight:800;letter-spacing:1px;font-size:0.8rem;color:#0ea5e9;">ABSEN V2</span>
                    <i data-lucide="chevron-right" style="width:14px;color:#cbd5e1;"></i>
                    <span style="font-weight:700;color:#1e293b;">Pengaturan Akses Scanner</span>
                </div>
            </div>
            <div class="z-header-right" style="display:flex;align-items:center;gap:1.5rem;">
                <!-- PORTAL HUB BUTTON -->
                <a href="<?php echo base_url('portal'); ?>" style="display:flex;align-items:center;gap:8px;padding:6px 16px;border-radius:100px;border:1px solid #bfdbfe;background:#eff6ff;color:#3b82f6;font-size:0.75rem;font-weight:800;text-decoration:none;transition:0.2s;white-space:nowrap;">
                    <i data-lucide="layout-grid" style="width:14px;height:14px;"></i> Portal HUB
                </a>

                <div class="z-header-user" style="display:flex;align-items:center;gap:12px;cursor:pointer;">
                    <div class="z-user-info" style="text-align: right;">
                        <div class="name" style="font-weight:800;font-size:0.85rem;color:#0f172a;line-height:1.2;"><?php echo isset($nama) ? $nama : 'Administrator'; ?></div>
                        <div class="role" style="font-size:0.7rem;color:#64748b;">Administrator</div>
                    </div>
                    <?php $usr_initial = isset($initial) && !empty($initial) ? $initial : (isset($nama) ? strtoupper(substr($nama,0,2)) : 'AD'); ?>
                    <div class="z-avatar-premium" style="width:36px;height:36px;color:white;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:800;background:linear-gradient(135deg, #0ea5e9, #0284c7);box-shadow:0 4px 12px rgba(14,165,233,0.2);"><?php echo $usr_initial; ?></div>
                </div>
                
                <!-- LOGOUT BUTTON -->
                <a href="<?php echo base_url('logout'); ?>" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:10px;background:#fef2f2;color:#ef4444;text-decoration:none;transition:0.2s;border:1px solid #fee2e2;">
                    <i data-lucide="log-out" style="width:18px;height:18px;"></i>
                </a>
            </div>
        </header>

        <div class="z-scroll" style="padding: 2rem;">
            <!-- Welcome Hero (Elite Page Header) -->
            <div class="elite-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: 16px; margin-bottom: 2rem; color: white; position: relative; overflow: hidden;">
                <div class="header-bg-shapes" style="position: absolute; top: 0; right: 0; bottom: 0; left: 0; background-image: radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.15) 0%, transparent 40%), radial-gradient(circle at 20% 80%, rgba(0, 0, 0, 0.1) 0%, transparent 40%); z-index: 1;"></div>
                <div style="position: absolute; inset: 0; opacity: 0.15; pointer-events: none; background: url('data:image/svg+xml,%3Csvg width=\'24\' height=\'24\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Ccircle cx=\'2\' cy=\'2\' r=\'1.5\' fill=\'white\' /%3E%3C/svg%3E'); z-index: 1;"></div>
                <div style="position: relative; z-index: 2;">
                    <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="scan" style="color: rgba(255,255,255,0.8);"></i> Pengaturan Akses Scanner
                    </h1>
                    <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Kelola hak akses guru untuk menggunakan scanner absensi di perangkat mereka.</p>
                </div>
            </div>

            <div style="background:white; border-radius:16px; border:1px solid #f1f5f9; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden;">
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="margin-bottom: 0;">
                        <thead style="background: #f8fafc;">
                            <tr>
                                <th width="50" style="padding: 1rem 1.5rem; font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 800; border-bottom: 1px solid #e2e8f0;">No</th>
                                <th style="padding: 1rem 1.5rem; font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 800; border-bottom: 1px solid #e2e8f0;">Nama Guru</th>
                                <th style="padding: 1rem 1.5rem; font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 800; border-bottom: 1px solid #e2e8f0;">NIP/NUPTK</th>
                                <th style="padding: 1rem 1.5rem; font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 800; border-bottom: 1px solid #e2e8f0; text-align: center;">Scan Siswa</th>
                                <th style="padding: 1rem 1.5rem; font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 800; border-bottom: 1px solid #e2e8f0; text-align: center;">Scan Guru</th>
                                <th style="padding: 1rem 1.5rem; font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 800; border-bottom: 1px solid #e2e8f0; text-align: center;">Admin Izin (Piket)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach($guruList as $g): ?>
                            <tr>
                                <td style="padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; color: #64748b; font-weight: 600; font-size: 0.9rem;"><?= $no++ ?></td>
                                <td style="padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 36px; height: 36px; background: #e0f2fe; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #0ea5e9; font-weight: 800; flex-shrink: 0; font-size: 0.9rem;">
                                            <?= strtoupper(substr($g['nama'], 0, 1)) ?>
                                        </div>
                                        <div style="font-weight: 700; color: #1e293b; font-size: 0.9rem;"><?= htmlspecialchars($g['nama']) ?></div>
                                    </div>
                                </td>
                                <td style="padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; color: #64748b; font-size: 0.85rem;"><?= htmlspecialchars($g['nip'] ?? '-') ?></td>
                                <td class="text-center" style="padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; text-align: center;">
                                    <div class="form-check form-switch d-flex justify-content-center" style="margin: 0; padding: 0;">
                                        <input class="form-check-input akses-toggle" type="checkbox" role="switch" 
                                               style="margin: 0; cursor: pointer; width: 45px; height: 22px;"
                                               data-id="<?= $g['id'] ?>" data-tipe="siswa" <?= $g['akses_siswa'] > 0 ? 'checked' : '' ?>>
                                    </div>
                                </td>
                                <td class="text-center" style="padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; text-align: center;">
                                    <div class="form-check form-switch d-flex justify-content-center" style="margin: 0; padding: 0;">
                                        <input class="form-check-input akses-toggle" type="checkbox" role="switch" 
                                               style="margin: 0; cursor: pointer; width: 45px; height: 22px; background-color: <?php echo $g['akses_guru'] > 0 ? '#10b981' : ''; ?>; border-color: <?php echo $g['akses_guru'] > 0 ? '#10b981' : ''; ?>;"
                                               data-id="<?= $g['id'] ?>" data-tipe="guru" <?= $g['akses_guru'] > 0 ? 'checked' : '' ?>>
                                    </div>
                                </td>
                                <td class="text-center" style="padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; text-align: center;">
                                    <div class="form-check form-switch d-flex justify-content-center" style="margin: 0; padding: 0;">
                                        <input class="form-check-input akses-toggle" type="checkbox" role="switch" 
                                               style="margin: 0; cursor: pointer; width: 45px; height: 22px; background-color: <?php echo $g['akses_izin'] > 0 ? '#8b5cf6' : ''; ?>; border-color: <?php echo $g['akses_izin'] > 0 ? '#8b5cf6' : ''; ?>;"
                                               data-id="<?= $g['id'] ?>" data-tipe="izin" <?= $g['akses_izin'] > 0 ? 'checked' : '' ?>>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
lucide.createIcons();
function zToggleSidebar() {
    document.getElementById('zSidebar').classList.toggle('active');
}

document.addEventListener('DOMContentLoaded', function() {
    const toggles = document.querySelectorAll('.akses-toggle');
    toggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const guruId = this.getAttribute('data-id');
            const tipe = this.getAttribute('data-tipe');
            const isChecked = this.checked ? 1 : 0;
            const self = this;
            
            // disable temporarily
            self.disabled = true;

            const formData = new FormData();
            formData.append('guru_id', guruId);
            formData.append('tipe', tipe);
            formData.append('status', isChecked);

            fetch('<?= base_url('admin/absen_v2/akses-scanner/save') ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(!data.success) {
                    alert('Gagal menyimpan akses: ' + (data.message || 'Error'));
                    self.checked = !isChecked; // revert
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan jaringan.');
                self.checked = !isChecked; // revert
            })
            .finally(() => {
                self.disabled = false;
            });
        });
    });
});
</script>
