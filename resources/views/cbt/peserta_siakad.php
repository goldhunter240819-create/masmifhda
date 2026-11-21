<?php
$pageTitle = 'Peserta Siswa SIAKAD (CBT Offline) - MAS Miftahul Huda';
$active_menu = 'peserta_siakad';
?>
<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/header.php'; ?>

        <div class="z-scroll" style="padding: 0 0 3rem 0;">

            <!-- Elite Page Header -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-badge" style="background: rgba(14, 165, 233, 0.2); border-color: rgba(14, 165, 233, 0.4); color: #38bdf8;">
                            SINKRONISASI PESERTA SIAKAD (SERVER OFFLINE)
                        </div>
                        <h1><i data-lucide="graduation-cap"></i> Peserta Ujian SIAKAD (Lokal)</h1>
                        <p>Daftar seluruh siswa reguler madrasah dari database <strong>SIAKAD Pusat</strong> yang disinkronkan ke server lokal untuk keperluan ujian CBT. Tekan tombol di samping untuk menarik data siswa & akun login terbaru secara instan.</p>
                    </div>
                    <div class="header-actions-premium" style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button type="button" onclick="location.reload()" class="e-btn e-btn-glass" style="background: rgba(255,255,255,0.85); color: #475569;">
                            <i data-lucide="rotate-cw"></i> Refresh
                        </button>
                        <button type="button" id="btnPullSiswa" onclick="tarikSiswaDariPusat()" class="e-btn e-btn-primary" style="background: linear-gradient(135deg, #0284c7, #0369a1); box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35);">
                            <i data-lucide="cloud-download"></i> Tarik Siswa dari SIAKAD Pusat
                        </button>
                    </div>
                </div>
            </div>

            <!-- Notification Box / Alert -->
            <div id="syncAlertBox" style="display: none; margin-bottom: 1.5rem;"></div>

            <!-- KPI Summary Cards -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.75rem;">
                <!-- Total Siswa -->
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                        <i data-lucide="users"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.78rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Total Siswa SIAKAD</div>
                        <div style="font-size: 1.6rem; font-weight: 900; color: #0f172a; line-height: 1.2;"><?php echo number_format($totalSiswa ?? 0); ?></div>
                    </div>
                </div>

                <!-- Kelas X -->
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #ede9fe; color: #7c3aed; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                        <i data-lucide="award"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.78rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Tingkat Kelas X</div>
                        <div style="font-size: 1.6rem; font-weight: 900; color: #7c3aed; line-height: 1.2;"><?php echo number_format($countX ?? 0); ?></div>
                    </div>
                </div>

                <!-- Kelas XI -->
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                        <i data-lucide="award"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.78rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Tingkat Kelas XI</div>
                        <div style="font-size: 1.6rem; font-weight: 900; color: #d97706; line-height: 1.2;"><?php echo number_format($countXI ?? 0); ?></div>
                    </div>
                </div>

                <!-- Kelas XII -->
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                        <i data-lucide="award"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.78rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Tingkat Kelas XII</div>
                        <div style="font-size: 1.6rem; font-weight: 900; color: #16a34a; line-height: 1.2;"><?php echo number_format($countXII ?? 0); ?></div>
                    </div>
                </div>

                <!-- Akun Aktif -->
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                        <i data-lucide="shield-check"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.78rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Akun Siap Ujian</div>
                        <div style="font-size: 1.6rem; font-weight: 900; color: #4338ca; line-height: 1.2;"><?php echo number_format($countActiveAccount ?? 0); ?></div>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Box -->
            <div class="card" style="border-radius: 18px; border: 1px solid #e2e8f0; background: #ffffff; margin-bottom: 1.5rem; padding: 1.25rem 1.5rem;">
                <form method="GET" action="<?php echo base_url('cbt/peserta_siakad'); ?>" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center; justify-content: space-between;">
                    <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center; flex: 1;">
                        <!-- Filter Tingkat -->
                        <div style="min-width: 150px;">
                            <label style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 4px; display: block;">Tingkat Kelas</label>
                            <select name="tingkat" class="form-control" style="font-size: 0.85rem; padding: 7px 12px; border-radius: 10px;" onchange="this.form.submit()">
                                <option value="">Semua Tingkat</option>
                                <option value="X" <?php echo ($selectedTingkat === 'X' || $selectedTingkat === 'Kelas X') ? 'selected' : ''; ?>>Kelas X</option>
                                <option value="XI" <?php echo ($selectedTingkat === 'XI' || $selectedTingkat === 'Kelas XI') ? 'selected' : ''; ?>>Kelas XI</option>
                                <option value="XII" <?php echo ($selectedTingkat === 'XII' || $selectedTingkat === 'Kelas XII') ? 'selected' : ''; ?>>Kelas XII</option>
                            </select>
                        </div>

                        <!-- Filter Rombel Kelas -->
                        <div style="min-width: 170px;">
                            <label style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 4px; display: block;">Rombel Kelas</label>
                            <select name="kelas_id" class="form-control" style="font-size: 0.85rem; padding: 7px 12px; border-radius: 10px;" onchange="this.form.submit()">
                                <option value="">Semua Rombel</option>
                                <?php foreach (($kelasList ?? []) as $kls): ?>
                                    <option value="<?php echo $kls['id']; ?>" <?php echo ($selectedKelasId == $kls['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($kls['nama_kelas']); ?> (<?php echo htmlspecialchars($kls['tingkat'] ?? ''); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Search Input -->
                        <div style="min-width: 240px; flex: 1;">
                            <label style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 4px; display: block;">Pencarian Siswa</label>
                            <div style="position: relative;">
                                <input type="text" name="q" value="<?php echo htmlspecialchars($search ?? ''); ?>" placeholder="Cari Nama, NIS, NIK, atau Username..." class="form-control" style="padding-left: 36px; font-size: 0.85rem; padding-top: 7px; padding-bottom: 7px; border-radius: 10px;">
                                <i data-lucide="search" style="position: absolute; left: 11px; top: 9px; width: 16px; height: 16px; color: #94a3b8;"></i>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 8px; align-items: flex-end; margin-top: 18px;">
                        <button type="submit" class="e-btn e-btn-primary" style="padding: 7px 16px; font-size: 0.85rem; border-radius: 10px;">
                            <i data-lucide="filter"></i> Terapkan Filter
                        </button>
                        <?php if ($selectedTingkat !== '' || $selectedKelasId > 0 || $search !== ''): ?>
                            <a href="<?php echo base_url('cbt/peserta_siakad'); ?>" class="e-btn e-btn-secondary" style="padding: 7px 14px; font-size: 0.85rem; border-radius: 10px;">
                                Reset
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="card" style="border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden; background: #ffffff; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                <div style="padding: 1.25rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div style="font-weight: 800; font-size: 0.95rem; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="list" style="width: 18px; height: 18px; color: #0284c7;"></i>
                        <span>Daftar Siswa SIAKAD di Server Lokal</span>
                        <span style="background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 20px; font-size: 0.75rem; font-weight: 800;">
                            <?php echo count($siswaList ?? []); ?> Siswa Ditemukan
                        </span>
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 650px; overflow-y: auto;">
                    <table class="table" style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
                        <thead>
                            <tr style="background: #f8fafc; color: #475569; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0;">
                                <th style="padding: 12px 16px; text-align: center; width: 50px;">No</th>
                                <th style="padding: 12px 16px; text-align: left;">Nama Siswa & NIS</th>
                                <th style="padding: 12px 16px; text-align: left;">Rombel / Kelas</th>
                                <th style="padding: 12px 16px; text-align: center; width: 70px;">JK</th>
                                <th style="padding: 12px 16px; text-align: left;">Akun Login CBT</th>
                                <th style="padding: 12px 16px; text-align: center; width: 120px;">Status Akun</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 0.88rem; color: #1e293b;">
                            <?php if (empty($siswaList)): ?>
                                <tr>
                                    <td colspan="6" style="padding: 3.5rem 1rem; text-align: center;">
                                        <div style="max-width: 420px; margin: 0 auto;">
                                            <div style="width: 64px; height: 64px; background: #e0f2fe; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; color: #0284c7;">
                                                <i data-lucide="cloud-off" style="width: 32px; height: 32px;"></i>
                                            </div>
                                            <h4 style="font-weight: 800; font-size: 1.1rem; color: #0f172a; margin-bottom: 6px;">Belum Ada Data Siswa SIAKAD di Lokal</h4>
                                            <p style="font-size: 0.84rem; color: #64748b; line-height: 1.5; margin-bottom: 1.25rem;">
                                                Data siswa SIAKAD belum ditarik dari Server Online Pusat ke server offline madrasah ini. Tekan tombol di bawah untuk menyinkronkan data siswa seketika.
                                            </p>
                                            <button type="button" onclick="tarikSiswaDariPusat()" class="e-btn e-btn-primary" style="background: linear-gradient(135deg, #0284c7, #0369a1); padding: 10px 20px; font-size: 0.9rem;">
                                                <i data-lucide="cloud-download"></i> Tarik Siswa dari SIAKAD Pusat Sekarang
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($siswaList as $s): ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                        <td style="padding: 12px 16px; text-align: center; color: #64748b; font-weight: 700;"><?php echo $no++; ?></td>
                                        <td style="padding: 12px 16px;">
                                            <div style="font-weight: 800; color: #0f172a; font-size: 0.92rem;"><?php echo htmlspecialchars($s['nama']); ?></div>
                                            <div style="display: flex; gap: 8px; margin-top: 3px; font-size: 0.76rem; color: #64748b;">
                                                <span>NIS: <strong style="color: #0284c7;"><?php echo htmlspecialchars($s['nis'] ?: '-'); ?></strong></span>
                                                <?php if (!empty($s['nik'])): ?>
                                                    <span>• NIK: <?php echo htmlspecialchars($s['nik']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td style="padding: 12px 16px;">
                                            <span style="display: inline-block; padding: 4px 10px; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 0.8rem; font-weight: 800;">
                                                <?php echo htmlspecialchars($s['nama_kelas'] ?? ($s['tingkat'] ?? 'Kelas X')); ?>
                                            </span>
                                        </td>
                                        <td style="padding: 12px 16px; text-align: center;">
                                            <?php if (($s['jenis_kelamin'] ?? 'L') === 'L'): ?>
                                                <span style="display: inline-block; padding: 3px 8px; background: #e0f2fe; color: #0369a1; border-radius: 6px; font-size: 0.75rem; font-weight: 800;">L</span>
                                            <?php else: ?>
                                                <span style="display: inline-block; padding: 3px 8px; background: #fce7f3; color: #be185d; border-radius: 6px; font-size: 0.75rem; font-weight: 800;">P</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding: 12px 16px;">
                                            <div style="display: flex; flex-direction: column; gap: 2px; font-size: 0.82rem;">
                                                <div><span style="color:#64748b; font-size: 0.74rem;">User:</span> <code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-weight: 700; color: #334155;"><?php echo htmlspecialchars($s['username'] ?? $s['nis']); ?></code></div>
                                                <div><span style="color:#64748b; font-size: 0.74rem;">Pass:</span> <code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-weight: 700; color: #059669;"><?php echo htmlspecialchars($s['plain_password'] ?? '123456'); ?></code></div>
                                            </div>
                                        </td>
                                        <td style="padding: 12px 16px; text-align: center;">
                                            <?php if (!empty($s['username'])): ?>
                                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #ecfdf5; color: #059669; border-radius: 100px; font-size: 0.75rem; font-weight: 800;">
                                                    <i data-lucide="check" style="width: 13px; height: 13px;"></i> Siap Ujian
                                                </span>
                                            <?php else: ?>
                                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #fee2e2; color: #dc2626; border-radius: 100px; font-size: 0.75rem; font-weight: 800;">
                                                    <i data-lucide="alert-circle" style="width: 13px; height: 13px;"></i> Belum Ada Akun
                                                </span>
                                            <?php endif; ?>
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

<!-- Modal Loading Progress Tarik Siswa -->
<div id="modalLoadingSync" style="display: none; position: fixed; inset: 0; z-index: 99999; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); align-items: center; justify-content: center;">
    <div style="background: #ffffff; border-radius: 20px; padding: 2rem 2.5rem; max-width: 440px; width: 90%; text-align: center; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
        <div style="width: 64px; height: 64px; margin: 0 auto 1.25rem auto; border: 4px solid #e0f2fe; border-top-color: #0284c7; border-radius: 50%; animation: spinSync 1s linear infinite;"></div>
        <h3 style="font-weight: 800; font-size: 1.2rem; color: #0f172a; margin-bottom: 8px;">Menarik Siswa dari SIAKAD Pusat</h3>
        <p style="font-size: 0.88rem; color: #64748b; line-height: 1.5; margin-bottom: 1.25rem;">
            Sedang mengunduh data siswa reguler, rombel kelas, dan akun login CBT dari server cloud online... Harap tunggu sebentar.
        </p>
        <div style="font-size: 0.78rem; font-weight: 700; color: #0284c7; background: #f0f9ff; padding: 6px 14px; border-radius: 100px; display: inline-block;">
            Target: https://apk.masmifhda.sch.id
        </div>
    </div>
</div>

<style>
@keyframes spinSync {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
function tarikSiswaDariPusat() {
    if (!confirm("Tarik dan perbarui data seluruh siswa SIAKAD dari Server Online Pusat (https://apk.masmifhda.sch.id)?\n\nData siswa reguler, kelas, dan akun login akan otomatis diperbarui di server lokal.")) {
        return;
    }

    const modal = document.getElementById('modalLoadingSync');
    const alertBox = document.getElementById('syncAlertBox');
    modal.style.display = 'flex';
    alertBox.style.display = 'none';

    fetch('<?php echo base_url('cbt/peserta_siakad/pull'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'server_url': 'https://apk.masmifhda.sch.id'
        })
    })
    .then(res => res.json())
    .then(data => {
        modal.style.display = 'none';
        if (data.success) {
            alertBox.innerHTML = `
                <div style="background: #dcfce7; border: 1.5px solid #86efac; color: #15803d; padding: 14px 18px; border-radius: 14px; font-weight: 700; font-size: 0.92rem; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="check-circle-2" style="width: 22px; height: 22px; color: #16a34a;"></i>
                        <span>${data.message} (Total ${data.count} Siswa Berhasil Disinkronkan).</span>
                    </div>
                    <button type="button" onclick="location.reload()" style="background: #16a34a; color: #fff; border: none; padding: 5px 12px; border-radius: 8px; font-weight: 800; cursor: pointer;">
                        Muat Ulang
                    </button>
                </div>
            `;
            alertBox.style.display = 'block';
            if (window.lucide) lucide.createIcons();
            setTimeout(() => {
                location.reload();
            }, 1200);
        } else {
            alertBox.innerHTML = `
                <div style="background: #fee2e2; border: 1.5px solid #fca5a5; color: #b91c1c; padding: 14px 18px; border-radius: 14px; font-weight: 700; font-size: 0.92rem; display: flex; align-items: center; gap: 10px;">
                    <i data-lucide="alert-triangle" style="width: 22px; height: 22px; color: #dc2626;"></i>
                    <span>${data.message || 'Gagal menarik data siswa dari server pusat.'}</span>
                </div>
            `;
            alertBox.style.display = 'block';
            if (window.lucide) lucide.createIcons();
        }
    })
    .catch(err => {
        modal.style.display = 'none';
        alertBox.innerHTML = `
            <div style="background: #fee2e2; border: 1.5px solid #fca5a5; color: #b91c1c; padding: 14px 18px; border-radius: 14px; font-weight: 700; font-size: 0.92rem; display: flex; align-items: center; gap: 10px;">
                <i data-lucide="wifi-off" style="width: 22px; height: 22px; color: #dc2626;"></i>
                <span>Terjadi kendala jaringan: ${err.message}. Pastikan server lokal terhubung ke internet saat menarik data.</span>
            </div>
        `;
        alertBox.style.display = 'block';
        if (window.lucide) lucide.createIcons();
    });
}
</script>
