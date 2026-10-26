<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div style="flex: 1; padding: 1.5rem;">
        <?php include __DIR__ . '/_siakad_header.php'; ?>
        
        <div class="z-page-head-row">
            <div>
                <h1><i data-lucide="bell"></i> Broadcast Notifikasi</h1>
                <p>Kirim pemberitahuan massal (Push Notification) ke seluruh HP Wali Murid yang menginstal aplikasi SIAKAD.</p>
            </div>
        </div>

        <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 2rem; max-width: 800px;">
            <form action="<?php echo base_url('admin/siakad/lain-lain/broadcast/send'); ?>" method="POST">
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">Judul Notifikasi <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="judul" required 
                           placeholder="Contoh: PENGUMUMAN LIBUR SEMESTER" 
                           style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: 0.3s; font-size: 1rem;">
                    <small style="color: #64748b; margin-top: 5px; display: block;">Judul akan tercetak tebal di atas notifikasi.</small>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">Isi Pesan Notifikasi <span style="color: #ef4444;">*</span></label>
                    <textarea name="pesan" required rows="4" 
                              placeholder="Ketik isi pengumuman atau pesan di sini..." 
                              style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: 0.3s; font-size: 1rem; resize: vertical;"></textarea>
                    <small style="color: #64748b; margin-top: 5px; display: block;">Isi pesan yang akan dibaca oleh wali murid.</small>
                </div>
                
                <div style="margin-bottom: 2rem;">
                    <label style="display: block; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">URL Tautan (Opsional)</label>
                    <input type="text" name="url" 
                           placeholder="Contoh: https://mismifhda.my.id/pengumuman" 
                           style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: 0.3s; font-size: 1rem;">
                    <small style="color: #64748b; margin-top: 5px; display: block;">Jika diisi, saat wali murid menekan notifikasi, aplikasi akan langsung membuka tautan ini.</small>
                </div>

                <div style="margin-bottom: 2rem;">
                    <label style="display: block; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">Tampilkan di Mading Sampai Tanggal <span style="color: #ef4444;">*</span></label>
                    <input type="date" name="tanggal_berakhir" required value="<?= date('Y-m-d', strtotime('+7 days')) ?>"
                           style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: 0.3s; font-size: 1rem;">
                    <small style="color: #64748b; margin-top: 5px; display: block;">Pengumuman akan otomatis menghilang dari aplikasi setelah tanggal ini terlewati. (Standar: 7 hari)</small>
                </div>

                <div style="border-top: 1px solid #e2e8f0; padding-top: 1.5rem;">
                    <button type="submit" class="z-btn z-btn-primary" style="padding: 12px 24px; border-radius: 8px; font-size: 1rem; width: 100%; display: flex; justify-content: center; gap: 10px; background: #0ea5e9;">
                        <i data-lucide="send" style="width: 20px; height: 20px;"></i> KIRIM NOTIFIKASI SEKARANG
                    </button>
                    <p style="text-align: center; color: #ef4444; font-size: 0.85rem; margin-top: 10px; font-weight: bold;">
                        <i data-lucide="alert-triangle" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;"></i> Peringatan: Pesan akan langsung masuk ke seluruh HP yang menginstal APK. Harap teliti sebelum mengirim!
                    </p>
                </div>

            </form>
        </div>

    </div>
</div>

<div class="siakad-container">
    <div style="flex: 1; padding: 1.5rem; padding-top: 0;">
        <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 2rem; max-width: 800px;">
            <h2 style="margin-top: 0; font-size: 1.25rem; color: #1e293b; margin-bottom: 1rem;"><i data-lucide="list"></i> Daftar Pengumuman Mading</h2>
            
            <?php if(empty($pengumumans)): ?>
                <div style="padding: 20px; text-align: center; color: #64748b; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1;">
                    Belum ada pengumuman yang dikirim.
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <?php foreach($pengumumans as $p): ?>
                        <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; position: relative;">
                            <div style="position: absolute; top: 15px; right: 15px;">
                                <button onclick="deletePengumuman(<?= $p['id'] ?>)" style="background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; border-radius: 6px; padding: 6px 10px; cursor: pointer; display: flex; align-items: center; gap: 5px; font-size: 0.8rem; font-weight: bold; transition: 0.2s;">
                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i> Hapus
                                </button>
                            </div>
                            <div style="font-size: 0.8rem; color: #64748b; margin-bottom: 5px;">
                                <?= date('d M Y, H:i', strtotime($p['tanggal_dibuat'])) ?> &bull; 
                                <?php if($p['tanggal_berakhir'] && $p['tanggal_berakhir'] < date('Y-m-d')): ?>
                                    <span style="color: #ef4444; font-weight: bold;">Kedaluwarsa</span>
                                <?php else: ?>
                                    <span style="color: #0ea5e9; font-weight: bold;">Aktif s/d <?= date('d M Y', strtotime($p['tanggal_berakhir'])) ?></span>
                                <?php endif; ?>
                            </div>
                            <h3 style="margin: 0 0 8px 0; font-size: 1.1rem; color: #0f172a; padding-right: 80px;"><?= htmlspecialchars($p['judul']) ?></h3>
                            <p style="margin: 0; font-size: 0.9rem; color: #475569; line-height: 1.5; white-space: pre-wrap;"><?= htmlspecialchars($p['pesan']) ?></p>
                            <?php if(!empty($p['url'])): ?>
                                <div style="margin-top: 10px;">
                                    <a href="<?= htmlspecialchars($p['url']) ?>" target="_blank" style="color: #3b82f6; font-size: 0.85rem; text-decoration: none;"><i data-lucide="link" style="width: 12px; height: 12px;"></i> <?= htmlspecialchars($p['url']) ?></a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('input[type="text"], textarea').forEach(el => {
        el.addEventListener('focus', function() { this.style.borderColor = '#0ea5e9'; this.style.boxShadow = '0 0 0 3px rgba(14, 165, 233, 0.1)'; });
        el.addEventListener('blur', function() { this.style.borderColor = '#cbd5e1'; this.style.boxShadow = 'none'; });
    });

    function deletePengumuman(id) {
        Swal.fire({
            title: 'Hapus Pengumuman?',
            text: "Pengumuman ini akan dihapus dari mading aplikasi selamanya!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#cbd5e1',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(base_url('admin/siakad/lain-lain/broadcast/delete'), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + id
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Terhapus!', 'Pengumuman telah dihapus.', 'success').then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    }
</script>

