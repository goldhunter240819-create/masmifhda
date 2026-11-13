<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title = 'Pengaturan KKM'; include __DIR__ . '/../admin/_zamrud_head.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php $page_badge = 'Kurikulum'; ?>
<div class="z-wrap">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../siakad/_siakad_header.php'; ?>

        <div class="modern-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 1.75rem 2rem; border-radius: 18px; margin-bottom: 1.5rem; box-shadow: 0 10px 25px rgba(14, 165, 233, 0.2);">
            <div>
                <h1 class="mph-title" style="color: white; font-size: 1.75rem; font-weight: 800; margin: 0 0 0.4rem 0; display: flex; align-items: center; gap: 12px;">
                    <i data-lucide="target" style="color: rgba(255,255,255,0.85); width: 28px; height: 28px;"></i> Pengaturan KKM & Bobot Nilai
                </h1>
                <p class="mph-subtitle" style="color: rgba(255,255,255,0.92); margin: 0; font-size: 0.95rem;">
                    Kelola dan pantau Kriteria Ketuntasan Minimal (KKM) tiap mata pelajaran jenjang MA.
                </p>
            </div>
        </div>

        <div class="z-card" style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.02); overflow: hidden; margin-bottom: 2rem;">
            <form id="formKkm" method="POST" action="<?= base_url('kurikulum/monitoring-kkm/save') ?>">
                <div style="display: flex; justify-content: space-between; margin-bottom: 20px; align-items: center; padding: 20px 20px 0 20px; flex-wrap: wrap; gap: 15px;">
                    <p style="color: #64748b; font-size: 0.9rem; font-weight: 600; margin: 0;">Tentukan nilai KKM per tingkat kelas dan Bobot Nilai Rapor secara terpusat.</p>
                    <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 6px; font-size: 0.85rem; background: #eff6ff; padding: 6px 12px; border-radius: 8px; border: 1px solid #bfdbfe;">
                            <label style="color: #1e3a8a; font-weight: 700;">Bobot Harian (PH+PTS) %:</label>
                            <input type="number" name="bobot_harian" value="<?= $institusi['bobot_harian'] ?? 50 ?>" style="width: 55px; padding: 4px; border: 1px solid #93c5fd; border-radius: 4px; text-align: center; font-weight: 700;">
                            <label style="color: #1e3a8a; font-weight: 700; margin-left: 8px;">Sumatif (PAS) %:</label>
                            <input type="number" name="bobot_pas" value="<?= $institusi['bobot_pas'] ?? 50 ?>" style="width: 55px; padding: 4px; border: 1px solid #93c5fd; border-radius: 4px; text-align: center; font-weight: 700;">
                        </div>
                        <button type="submit" class="z-btn z-btn-primary" id="btnSimpanKkm" style="padding: 10px 20px; border-radius: 8px; font-weight: 700; display: flex; align-items: center; gap: 8px; height: 38px; background: #0284c7; color: white; border: none; cursor: pointer; box-shadow: 0 2px 8px rgba(2, 132, 199, 0.3);">
                            <i data-lucide="save" style="width: 16px; height: 16px;"></i> Simpan Pengaturan
                        </button>
                    </div>
                </div>

                <div class="z-table-wrap">
                    <table class="z-table" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                <th style="padding: 12px 20px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase;">Kelompok</th>
                                <th style="padding: 12px 20px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase;">Mata Pelajaran</th>
                                <th style="width: 130px; text-align: center; padding: 12px 20px; font-size: 0.75rem; font-weight: 800; color: #0284c7; text-transform: uppercase;">KKM KELAS 10</th>
                                <th style="width: 130px; text-align: center; padding: 12px 20px; font-size: 0.75rem; font-weight: 800; color: #0284c7; text-transform: uppercase;">KKM KELAS 11</th>
                                <th style="width: 130px; text-align: center; padding: 12px 20px; font-size: 0.75rem; font-weight: 800; color: #0284c7; text-transform: uppercase;">KKM KELAS 12</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($mapels)): ?>
                            <tr>
                                <td colspan="5" style="text-align:center; padding: 2.5rem; color: #64748b;">Tidak ada data mata pelajaran.</td>
                            </tr>
                            <?php else: ?>
                                <?php 
                                $current_kelompok = '';
                                foreach ($mapels as $mapel): 
                                    $kel = $mapel['kelompok'] ?: 'Umum';
                                    if ($current_kelompok !== $kel) {
                                        $current_kelompok = $kel;
                                        echo "<tr><td colspan='5' style='background: #f1f5f9; font-weight: 800; color: #334155; padding: 8px 20px; font-size: 0.8rem; letter-spacing: 0.5px;'>KELOMPOK " . htmlspecialchars($current_kelompok) . "</td></tr>";
                                    }
                                ?>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding-left: 20px; vertical-align: middle;">
                                        <span style="background: #e0f2fe; color: #0284c7; padding: 3px 8px; border-radius: 4px; font-size: 0.72rem; font-weight: 700;">Kel. <?= htmlspecialchars($current_kelompok) ?></span>
                                    </td>
                                    <td style="font-weight: 700; color: #1e293b; vertical-align: middle; padding: 10px 20px;">
                                        <?= htmlspecialchars($mapel['nama_mapel']) ?>
                                    </td>
                                    <td style="text-align: center; vertical-align: middle; padding: 10px 20px;">
                                        <input type="number" name="kkm[<?= $mapel['id'] ?>][10]" class="z-input" style="width: 80px; text-align: center; font-weight: 700; border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px;" min="0" max="100" value="<?= $kkm_data[$mapel['id']][10] ?? '' ?>" placeholder="75">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle; padding: 10px 20px;">
                                        <input type="number" name="kkm[<?= $mapel['id'] ?>][11]" class="z-input" style="width: 80px; text-align: center; font-weight: 700; border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px;" min="0" max="100" value="<?= $kkm_data[$mapel['id']][11] ?? '' ?>" placeholder="75">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle; padding: 10px 20px;">
                                        <input type="number" name="kkm[<?= $mapel['id'] ?>][12]" class="z-input" style="width: 80px; text-align: center; font-weight: 700; border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px;" min="0" max="100" value="<?= $kkm_data[$mapel['id']][12] ?? '' ?>" placeholder="75">
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
document.getElementById('formKkm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const bobotHarian = document.querySelector('input[name="bobot_harian"]').value;
    const bobotPas = document.querySelector('input[name="bobot_pas"]').value;
    
    if (parseInt(bobotHarian) + parseInt(bobotPas) !== 100) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Total persentase bobot Harian + PAS harus 100%!'
        });
        return;
    }

    const btn = document.getElementById('btnSimpanKkm');
    btn.disabled = true;
    btn.innerHTML = 'Menyimpan...';

    fetch(this.action, {
        method: 'POST',
        body: new FormData(this)
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="save" style="width: 16px; height: 16px;"></i> Simpan Pengaturan';
        if (typeof lucide !== 'undefined') lucide.createIcons();

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: data.message || 'Terjadi kesalahan saat menyimpan.'
            });
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="save" style="width: 16px; height: 16px;"></i> Simpan Pengaturan';
        if (typeof lucide !== 'undefined') lucide.createIcons();

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan jaringan atau server.'
        });
    });
});

if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
</body>
</html>

