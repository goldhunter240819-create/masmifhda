<?php
$pageTitle = 'Rekapitulasi Presensi Pengawas Ruang CBT';
$active_menu = 'pengawas_rekap';
$totalRuangDiawasi = $totalRuangDiawasi ?? 0;
?>
<!-- SweetAlert2 Modern -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.stat-card-presensi {
    background: #ffffff;
    border-radius: 18px;
    padding: 1.25rem 1.5rem;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    display: flex;
    align-items: center;
    gap: 1.25rem;
    transition: transform 0.2s, box-shadow 0.2s;
}
.stat-card-presensi:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.05);
}
.stat-icon-wrapper {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}
.chip-soft {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 700;
}

/* Standard Elite Header Buttons */
.e-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 18px;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 800;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    border: none;
    line-height: 1.2;
}
.e-btn-primary {
    background: #ffffff;
    color: #059669;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.9);
}
.e-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    background: #f8fafc;
    color: #047857;
}
.e-btn-glass {
    background: rgba(255, 255, 255, 0.18);
    color: #ffffff;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}
.e-btn-glass:hover {
    background: rgba(255, 255, 255, 0.3);
    color: #ffffff;
    transform: translateY(-2px);
}
</style>

<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/header.php'; ?>

        <div class="z-scroll" style="padding: 0 0 3rem 0;">

            <!-- Header Banner -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);">
                            <i class="fa-solid fa-clipboard-check"></i> PRESENSI & MONITORING PENGAWAS
                        </div>
                        <h1><i class="fa-solid fa-user-check" style="color: #10b981;"></i> Rekap Presensi Pengawas Ruang</h1>
                        <p>Monitoring kehadiran dan rekapitulasi sesi pengawasan ruang ujian per pengawas berdasarkan presensi kartu scan terverifikasi.</p>
                    </div>
                    <div class="header-actions-premium" style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <a href="<?= function_exists('base_url') ? base_url('cbt/pengawas/scan') : '/cbt/pengawas/scan' ?>" class="e-btn e-btn-glass">
                            <i class="fa-solid fa-camera"></i> Buka Scanner
                        </a>
                        <a href="<?= function_exists('base_url') ? base_url('cbt/pengawas/jadwal') : '/cbt/pengawas/jadwal' ?>" class="e-btn e-btn-glass">
                            <i class="fa-solid fa-calendar-days"></i> Matriks Jadwal
                        </a>
                        <a href="<?= function_exists('base_url') ? base_url('cbt/pengawas/cetak-rekap?topik_id=' . $selectedTopikId . '&tgl_mulai=' . urlencode($tglMulai) . '&tgl_selesai=' . urlencode($tglSelesai)) : '/cbt/pengawas/cetak-rekap' ?>" target="_blank" class="e-btn e-btn-primary">
                            <i class="fa-solid fa-print"></i> Cetak Rekap Presensi (A4)
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content Container -->
            <div style="max-width: 1300px; margin: 1.5rem auto; padding: 0 1.5rem;">

                <!-- Filter Bar -->
                <div style="background: #ffffff; border-radius: 20px; padding: 1.25rem 1.5rem; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                    <form method="GET" action="" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
                        <div style="flex: 1; min-width: 220px;">
                            <label style="display: block; font-size: 0.78rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Topik Ujian CBT:</label>
                            <select name="topik_id" onchange="this.form.submit()" style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-weight: 700; font-size: 0.85rem; color: #0f172a;">
                                <option value="0">-- Semua Topik Ujian --</option>
                                <?php foreach ($topikList as $tp): ?>
                                    <option value="<?= $tp['id'] ?>" <?= ((int)$selectedTopikId === (int)$tp['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tp['nama_topik']) ?> (<?= htmlspecialchars($tp['tahun_ajaran']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.78rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Dari Tanggal:</label>
                            <input type="date" name="tgl_mulai" value="<?= htmlspecialchars($tglMulai) ?>" style="padding: 8px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-weight: 700; font-size: 0.85rem;">
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.78rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Sampai Tanggal:</label>
                            <input type="date" name="tgl_selesai" value="<?= htmlspecialchars($tglSelesai) ?>" style="padding: 8px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-weight: 700; font-size: 0.85rem;">
                        </div>

                        <div style="display: flex; gap: 8px;">
                            <button type="submit" style="padding: 9px 18px; border-radius: 10px; background: #2563eb; color: white; border: none; font-weight: 800; font-size: 0.85rem; cursor: pointer;">
                                <i class="fa-solid fa-filter"></i> Filter
                            </button>
                            <a href="<?= function_exists('base_url') ? base_url('cbt/pengawas/rekap') : '/cbt/pengawas/rekap' ?>" style="padding: 9px 14px; border-radius: 10px; background: #f1f5f9; color: #475569; text-decoration: none; font-weight: 700; font-size: 0.85rem; border: 1px solid #cbd5e1;">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                <!-- 4 KPI Statistic Cards -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                    
                    <div class="stat-card-presensi">
                        <div class="stat-icon-wrapper" style="background: #eff6ff; color: #2563eb;">
                            <i class="fa-solid fa-business-time"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.78rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Total Sesi Terawasi</div>
                            <div style="font-size: 1.7rem; font-weight: 900; color: #0f172a; line-height: 1.2;"><?= $totalSesiDiawasi ?></div>
                            <div style="font-size: 0.72rem; color: #10b981; font-weight: 700; margin-top: 2px;">
                                <i class="fa-solid fa-circle-check"></i> Sesi terverifikasi scan
                            </div>
                        </div>
                    </div>

                    <div class="stat-card-presensi">
                        <div class="stat-icon-wrapper" style="background: #f0fdf4; color: #16a34a;">
                            <i class="fa-solid fa-chalkboard-user"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.78rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Pengawas Bertugas</div>
                            <div style="font-size: 1.7rem; font-weight: 900; color: #0f172a; line-height: 1.2;"><?= count($rekapList) ?></div>
                            <div style="font-size: 0.72rem; color: #64748b; font-weight: 700; margin-top: 2px;">
                                Guru / Pengawas Aktif
                            </div>
                        </div>
                    </div>

                    <div class="stat-card-presensi">
                        <div class="stat-icon-wrapper" style="background: #fefce8; color: #ca8a04;">
                            <i class="fa-solid fa-door-open"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.78rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Ruang Terlayani</div>
                            <div style="font-size: 1.7rem; font-weight: 900; color: #0f172a; line-height: 1.2;">
                                <?= $totalRuangDiawasi ?>
                            </div>
                            <div style="font-size: 0.72rem; color: #ca8a04; font-weight: 700; margin-top: 2px;">
                                Ruang Ujian Aktif
                            </div>
                        </div>
                    </div>

                    <div class="stat-card-presensi">
                        <div class="stat-icon-wrapper" style="background: #eef2ff; color: #4f46e5;">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.78rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Validasi Kehadiran</div>
                            <div style="font-size: 1.45rem; font-weight: 900; color: #4f46e5; line-height: 1.2;">
                                100% Valid
                            </div>
                            <div style="font-size: 0.72rem; color: #10b981; font-weight: 700; margin-top: 2px;">
                                <i class="fa-solid fa-qrcode"></i> Scan Kartu QR Proktor
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Table 1: Rekapitulasi Presensi Pengawas -->
                <div style="background: #ffffff; border-radius: 20px; padding: 1.5rem; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 10px;">
                        <div>
                            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-list-check" style="color: #059669;"></i> Rekapitulasi Kehadiran Pengawas Ruang
                            </h3>
                            <p style="margin: 4px 0 0; font-size: 0.8rem; color: #64748b;">Daftar akumulasi jumlah sesi dan ruangan yang diawasi oleh masing-masing guru pengawas.</p>
                        </div>
                        <a href="<?= function_exists('base_url') ? base_url('cbt/pengawas/cetak-rekap?topik_id=' . $selectedTopikId . '&tgl_mulai=' . urlencode($tglMulai) . '&tgl_selesai=' . urlencode($tglSelesai)) : '/cbt/pengawas/cetak-rekap' ?>" target="_blank" style="padding: 8px 16px; border-radius: 10px; background: #059669; color: white; text-decoration: none; font-size: 0.82rem; font-weight: 800; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-print"></i> Cetak Rekap Presensi A4
                        </a>
                    </div>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
                            <thead>
                                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; font-weight: 800; text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.5px;">
                                    <th style="padding: 12px 14px; width: 50px;">No</th>
                                    <th style="padding: 12px 14px;">Nama Pengawas</th>
                                    <th style="padding: 12px 14px;">NIP / ID</th>
                                    <th style="padding: 12px 14px;">Ruang Yang Diawasi</th>
                                    <th style="padding: 12px 14px;">Tanggal Bertugas</th>
                                    <th style="padding: 12px 14px; text-align: center;">Jumlah Sesi</th>
                                    <th style="padding: 12px 14px; text-align: center;">Status Presensi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($rekapList)): ?>
                                    <tr>
                                        <td colspan="7" style="padding: 3rem 1rem; text-align: center; color: #94a3b8;">
                                            <i class="fa-solid fa-folder-open" style="font-size: 2.5rem; margin-bottom: 10px; color: #cbd5e1;"></i>
                                            <div style="font-weight: 700; font-size: 0.95rem;">Belum ada riwayat pengawasan pada filter ini.</div>
                                            <div style="font-size: 0.78rem; margin-top: 4px;">Lakukan scan kartu pengawas di menu Scan Presensi terlebih dahulu.</div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($rekapList as $idx => $rk): ?>
                                        <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                            <td style="padding: 12px 14px; font-weight: 800; color: #64748b;"><?= $idx + 1 ?></td>
                                            <td style="padding: 12px 14px;">
                                                <div style="font-weight: 800; color: #0f172a; font-size: 0.9rem;"><?= htmlspecialchars($rk['guru_nama']) ?></div>
                                            </td>
                                            <td style="padding: 12px 14px; font-family: monospace; font-weight: 700; color: #475569;"><?= htmlspecialchars($rk['guru_nip'] ?: '-') ?></td>
                                            <td style="padding: 12px 14px; color: #475569; font-size: 0.8rem;">
                                                <span class="chip-soft" style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;">
                                                    <i class="fa-solid fa-door-open"></i> <?= htmlspecialchars($rk['daftar_ruang'] ?: '-') ?>
                                                </span>
                                            </td>
                                            <td style="padding: 12px 14px; font-size: 0.78rem; color: #475569;">
                                                <?= htmlspecialchars($rk['daftar_tanggal'] ?: '-') ?>
                                            </td>
                                            <td style="padding: 12px 14px; text-align: center;">
                                                <span class="chip-soft" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 0.82rem; font-weight: 800;">
                                                    <?= (int)$rk['total_sesi'] ?> Sesi
                                                </span>
                                            </td>
                                            <td style="padding: 12px 14px; text-align: center;">
                                                <span class="chip-soft" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; font-size: 0.75rem; font-weight: 800;">
                                                    <i class="fa-solid fa-circle-check"></i> Terverifikasi Hadir
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr style="background: #f8fafc; font-weight: 900; border-top: 2px solid #cbd5e1;">
                                        <td colspan="5" style="padding: 14px; text-align: right; color: #0f172a; font-size: 0.95rem;">TOTAL KESELURUHAN SESI DIAWASI:</td>
                                        <td style="padding: 14px; text-align: center; color: #2563eb; font-size: 1rem;"><?= $totalSesiDiawasi ?> Sesi</td>
                                        <td style="padding: 14px; text-align: center; color: #059669; font-size: 0.85rem;">Terdata Lengkap</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Table 2: Detail Log Presensi -->
                <div style="background: #ffffff; border-radius: 20px; padding: 1.5rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                        <div>
                            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-clock-rotate-left" style="color: #2563eb;"></i> Riwayat Detail Presensi Pengawas (100 Terakhir)
                            </h3>
                            <p style="margin: 4px 0 0; font-size: 0.8rem; color: #64748b;">Log transaksi presensi per jam scan kartu pengawas.</p>
                        </div>
                    </div>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.82rem;">
                            <thead>
                                <tr style="background: #f8fafc; border-bottom: 1.5px solid #e2e8f0; color: #475569; font-weight: 800;">
                                    <th style="padding: 10px 12px; width: 40px;">No</th>
                                    <th style="padding: 10px 12px;">Tanggal & Hari</th>
                                    <th style="padding: 10px 12px;">Sesi</th>
                                    <th style="padding: 10px 12px;">Ruang</th>
                                    <th style="padding: 10px 12px;">Nama Pengawas</th>
                                    <th style="padding: 10px 12px;">Waktu Scan</th>
                                    <th style="padding: 10px 12px;">Metode</th>
                                    <th style="padding: 10px 12px; text-align: center; width: 60px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($detailList)): ?>
                                    <tr>
                                        <td colspan="8" style="padding: 2rem; text-align: center; color: #94a3b8;">Belum ada log presensi tercatat.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($detailList as $i => $dt): ?>
                                        <tr style="border-bottom: 1px solid #f1f5f9;">
                                            <td style="padding: 10px 12px; color: #64748b; font-weight: 700;"><?= $i + 1 ?></td>
                                            <td style="padding: 10px 12px; font-weight: 700; color: #0f172a;">
                                                <?= htmlspecialchars($dt['hari']) ?>, <?= date('d/m/Y', strtotime($dt['tanggal'])) ?>
                                            </td>
                                            <td style="padding: 10px 12px;">
                                                <span class="chip-soft" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">
                                                    <?= htmlspecialchars($dt['sesi_label']) ?>
                                                </span>
                                            </td>
                                            <td style="padding: 10px 12px;">
                                                <span class="chip-soft" style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;">
                                                    <?= htmlspecialchars($dt['ruang_nama']) ?>
                                                </span>
                                            </td>
                                            <td style="padding: 10px 12px; font-weight: 800; color: #0f172a;">
                                                <?= htmlspecialchars($dt['guru_nama']) ?>
                                            </td>
                                            <td style="padding: 10px 12px; font-weight: 700; color: #0f172a;">
                                                <?= date('H:i:s', strtotime($dt['waktu_scan'])) ?> WIB
                                            </td>
                                            <td style="padding: 10px 12px; color: #64748b; font-size: 0.75rem;">
                                                <?= htmlspecialchars($dt['metode_scan']) ?>
                                            </td>
                                            <td style="padding: 10px 12px; text-align: center;">
                                                <button type="button" onclick="hapusPresensi(<?= $dt['id'] ?>, '<?= addslashes($dt['guru_nama']) ?>', '<?= htmlspecialchars($dt['sesi_label']) ?>')" style="padding: 4px 8px; border-radius: 6px; background: #fee2e2; color: #dc2626; border: none; font-size: 0.75rem; font-weight: 700; cursor: pointer;">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
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
</div>

<script>
function hapusPresensi(id, nama, sesi) {
    Swal.fire({
        title: 'Hapus Presensi?',
        html: `Apakah Anda yakin ingin menghapus catatan presensi <strong>${nama}</strong> pada <strong>${sesi}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus Data',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const delUrl = '<?= function_exists('base_url') ? base_url('cbt/pengawas/absensi/delete') : '/cbt/pengawas/absensi/delete' ?>';
            const formData = new FormData();
            formData.append('id', id);

            fetch(delUrl, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: 'Presensi berhasil dihapus.',
                        timer: 1200,
                        showConfirmButton: false
                    }).then(() => location.reload());
                }
            });
        }
    });
}
</script>
