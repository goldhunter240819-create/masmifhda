<style>
    .z-avatar-premium { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); }
    .header-breadcrumb .parent { color: #0ea5e9; }
    
    .table-rekap-matrix th {
        background: #f1f5f9;
        color: #334155;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 8px 6px;
        border: 1px solid #cbd5e1;
        text-align: center;
        vertical-align: middle;
    }
    .table-rekap-matrix td {
        padding: 8px 6px;
        border: 1px solid #e2e8f0;
        vertical-align: middle;
        font-size: 0.8rem;
    }
    .table-rekap-matrix tr:hover td {
        background: #f8fafc;
    }
    
    .badge-st {
        display: inline-block;
        width: 22px;
        height: 22px;
        line-height: 22px;
        border-radius: 6px;
        font-weight: 900;
        font-size: 0.75rem;
        text-align: center;
    }
    .badge-st-H { background: #dcfce7; color: #15803d; }
    .badge-st-S { background: #dbeafe; color: #1d4ed8; }
    .badge-st-I { background: #f3e8ff; color: #7e22ce; }
    .badge-st-A { background: #fee2e2; color: #b91c1c; }
    .badge-st-none { color: #cbd5e1; font-weight: 400; }
</style>

<div class="siakad-container">
    <?php include __DIR__ . '/../sidebar.php'; ?>
    
    <div class="z-main">
        <?php $page_badge = 'Admin Portal'; include __DIR__.'/../../../siakad/_siakad_header.php'; ?>

        <div class="z-scroll" style="padding: 1.5rem 2rem;">

            <!-- Alerts -->
            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'seed_success'): ?>
            <div style="background:#dcfce7;border:1px solid #86efac;border-radius:12px;padding:1rem 1.5rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:10px;color:#15803d;font-weight:700;">
                <i data-lucide="check-circle" style="width:20px;"></i>
                Alhamdulillah! Data dummy ekstrakurikuler ROHIS (30 siswa, 10 pertemuan latihan lengkap) berhasil dibuat di server!
            </div>
            <?php endif; ?>

            <!-- Header Filter & Actions -->
            <div style="background:white;border-radius:16px;padding:1.25rem 1.5rem;border:1px solid #f1f5f9;margin-bottom:1.5rem;box-shadow:0 4px 15px rgba(0,0,0,0.02);">
                <form method="GET" action="<?php echo base_url('admin/absen_v2/ekstra/rekap'); ?>" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:15px;">
                    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <i data-lucide="award" style="color:#0ea5e9;width:20px;"></i>
                            <span style="font-weight:800;font-size:0.85rem;color:#0f172a;">Cabang Ekstra:</span>
                        </div>
                        <select name="ekstra" style="border:1.5px solid #cbd5e1;border-radius:10px;padding:7px 12px;font-weight:700;color:#0f172a;font-size:0.85rem;background:#fff;outline:none;">
                            <?php foreach($ekstraList as $el): ?>
                                <option value="<?php echo htmlspecialchars($el); ?>" <?php echo $selected_ekstra === $el ? 'selected' : ''; ?>>
                                    🏆 <?php echo htmlspecialchars($el); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="font-weight:700;font-size:0.85rem;color:#475569;">Semester:</span>
                        </div>
                        <select name="semester" style="border:1.5px solid #cbd5e1;border-radius:10px;padding:7px 12px;font-weight:700;color:#0f172a;font-size:0.85rem;background:#fff;outline:none;">
                            <option value="Ganjil" <?php echo $semester === 'Ganjil' ? 'selected' : ''; ?>>Ganjil</option>
                            <option value="Genap" <?php echo $semester === 'Genap' ? 'selected' : ''; ?>>Genap</option>
                            <option value="Semua" <?php echo $semester === 'Semua' ? 'selected' : ''; ?>>Semua Semester</option>
                        </select>

                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="font-weight:700;font-size:0.85rem;color:#475569;">Bulan:</span>
                        </div>
                        <select name="bulan" style="border:1.5px solid #cbd5e1;border-radius:10px;padding:7px 12px;font-weight:700;color:#0f172a;font-size:0.85rem;background:#fff;outline:none;">
                            <option value="">Semua Bulan</option>
                            <?php 
                            $bulanNames = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
                            foreach($bulanNames as $bn => $bTxt):
                            ?>
                                <option value="<?php echo $bn; ?>" <?php echo $bulan == $bn ? 'selected' : ''; ?>>
                                    <?php echo $bTxt; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <button type="submit" style="background:#0ea5e9;color:white;border:none;padding:8px 16px;border-radius:10px;font-weight:700;font-size:0.85rem;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                            <i data-lucide="filter" style="width:14px;"></i> Terapkan Filter
                        </button>
                    </div>

                    <div style="display:flex;align-items:center;gap:10px;">
                        <a href="<?php echo base_url('admin/absen_v2/ekstra/seed-dummy'); ?>" onclick="return confirm('Generate data dummy ROHIS (10 pertemuan & 30 siswa) sekarang?');" style="background:#fef3c7;color:#b45309;text-decoration:none;padding:9px 16px;border-radius:10px;font-weight:800;font-size:0.85rem;display:inline-flex;align-items:center;gap:6px;border:1px solid #fde68a;">
                            <i data-lucide="sparkles" style="width:16px;"></i> + Data Dummy ROHIS (10 Ptm)
                        </a>
                        <a href="<?php echo base_url('admin/absen_v2/ekstra/cetak-rekap?ekstra=' . urlencode($selected_ekstra) . '&semester=' . urlencode($semester) . '&tahun_ajaran=' . urlencode($tahun_ajaran) . '&bulan=' . urlencode($bulan)); ?>" target="_blank" style="background:linear-gradient(135deg, #10b981 0%, #059669 100%);color:white;text-decoration:none;padding:9px 20px;border-radius:10px;font-weight:800;font-size:0.85rem;display:inline-flex;align-items:center;gap:8px;box-shadow:0 4px 12px rgba(16,185,129,0.3);">
                            <i data-lucide="printer" style="width:16px;"></i> Cetak Dokumen (A4 Landscape)
                        </a>
                    </div>
                </form>
            </div>

            <!-- Header Info Ekstra & Statistik Ringkas -->
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:15px;margin-bottom:1.5rem;">
                <div style="background:white;padding:1.25rem;border-radius:14px;border:1px solid #e2e8f0;display:flex;align-items:center;gap:15px;">
                    <div style="width:48px;height:48px;border-radius:12px;background:#e0f2fe;color:#0284c7;display:flex;align-items:center;justify-content:center;font-size:1.4rem;">
                        🏆
                    </div>
                    <div>
                        <div style="font-size:0.75rem;font-weight:700;color:#64748b;text-transform:uppercase;">Cabang Ekstrakurikuler</div>
                        <div style="font-size:1.15rem;font-weight:900;color:#0f172a;"><?php echo htmlspecialchars($selected_ekstra); ?></div>
                        <div style="font-size:0.75rem;color:#0284c7;font-weight:700;">Pembina: <?php echo htmlspecialchars($ekstraDetail['nama_pembina'] ?? '-'); ?></div>
                    </div>
                </div>

                <div style="background:white;padding:1.25rem;border-radius:14px;border:1px solid #e2e8f0;display:flex;align-items:center;gap:15px;">
                    <div style="width:48px;height:48px;border-radius:12px;background:#dcfce7;color:#15803d;display:flex;align-items:center;justify-content:center;">
                        <i data-lucide="users" style="width:24px;height:24px;"></i>
                    </div>
                    <div>
                        <div style="font-size:0.75rem;font-weight:700;color:#64748b;text-transform:uppercase;">Total Anggota</div>
                        <div style="font-size:1.5rem;font-weight:900;color:#15803d;"><?php echo count($anggotaList); ?> Siswa</div>
                    </div>
                </div>

                <div style="background:white;padding:1.25rem;border-radius:14px;border:1px solid #e2e8f0;display:flex;align-items:center;gap:15px;">
                    <div style="width:48px;height:48px;border-radius:12px;background:#fef3c7;color:#d97706;display:flex;align-items:center;justify-content:center;">
                        <i data-lucide="calendar" style="width:24px;height:24px;"></i>
                    </div>
                    <div>
                        <div style="font-size:0.75rem;font-weight:700;color:#64748b;text-transform:uppercase;">Pertemuan Terlaksana</div>
                        <div style="font-size:1.5rem;font-weight:900;color:#d97706;"><?php echo count($daftarPertemuan); ?> Kali</div>
                    </div>
                </div>
            </div>

            <!-- TABEL MATRIKS REKAPITULASI PRESENSI -->
            <div style="background:white;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.02);">
                <div style="padding:1rem 1.5rem;background:#f8fafc;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
                    <div style="font-weight:800;color:#0f172a;font-size:0.95rem;display:flex;align-items:center;gap:8px;">
                        <i data-lucide="table" style="color:#0ea5e9;width:18px;"></i>
                        Matriks Presensi Kehadiran Siswa
                    </div>
                    <div style="display:flex;gap:12px;font-size:0.75rem;font-weight:800;">
                        <span><span class="badge-st badge-st-H" style="width:18px;height:18px;line-height:18px;font-size:0.65rem;">H</span> Hadir</span>
                        <span><span class="badge-st badge-st-S" style="width:18px;height:18px;line-height:18px;font-size:0.65rem;">S</span> Sakit</span>
                        <span><span class="badge-st badge-st-I" style="width:18px;height:18px;line-height:18px;font-size:0.65rem;">I</span> Izin</span>
                        <span><span class="badge-st badge-st-A" style="width:18px;height:18px;line-height:18px;font-size:0.65rem;">A</span> Alpa</span>
                    </div>
                </div>

                <?php if (empty($rekapSiswa)): ?>
                    <div style="text-align:center;padding:3rem 1rem;color:#64748b;">
                        <i data-lucide="folder-x" style="width:48px;height:48px;color:#cbd5e1;margin-bottom:10px;"></i>
                        <h4 style="margin:0 0 6px 0;font-weight:800;color:#334155;">Belum Ada Data Rekapitulasi</h4>
                        <p style="margin:0 0 1rem 0;font-size:0.85rem;">Tidak ada anggota atau kegiatan yang tercatat pada filter ini.</p>
                        <a href="<?php echo base_url('admin/absen_v2/ekstra?ekstra=' . urlencode($selected_ekstra)); ?>" style="background:#0ea5e9;color:white;text-decoration:none;padding:8px 16px;border-radius:10px;font-weight:700;font-size:0.85rem;display:inline-flex;align-items:center;gap:6px;">
                            <i data-lucide="plus" style="width:14px;"></i> Input Presensi Baru
                        </a>
                    </div>
                <?php else: ?>
                    <div style="overflow-x:auto;">
                        <table class="table-rekap-matrix" style="width:100%;border-collapse:collapse;">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width:40px;">No</th>
                                    <th rowspan="2" style="width:90px;">NIS</th>
                                    <th rowspan="2" style="text-align:left;min-width:180px;padding-left:12px;">Nama Siswa</th>
                                    <th rowspan="2" style="width:75px;">Kelas</th>
                                    
                                    <?php if (!empty($daftarPertemuan)): ?>
                                        <th colspan="<?php echo count($daftarPertemuan); ?>">
                                            Pertemuan & Tanggal Latihan
                                        </th>
                                    <?php endif; ?>

                                    <th colspan="4" style="background:#e2e8f0;">Rekapitulasi</th>
                                    <th rowspan="2" style="width:65px;background:#e0f2fe;color:#0369a1;">% Hadir</th>
                                </tr>
                                <tr>
                                    <?php foreach($daftarPertemuan as $p): ?>
                                        <th style="font-size:0.65rem;min-width:32px;max-width:40px;line-height:1.2;padding:4px 2px;" title="<?php echo htmlspecialchars($p['materi']); ?>">
                                            P-<?php echo $p['pertemuan_ke'] ?: '1'; ?><br>
                                            <span style="font-size:0.6rem;color:#64748b;"><?php echo date('d/m', strtotime($p['tanggal'])); ?></span>
                                        </th>
                                    <?php endforeach; ?>
                                    <th style="width:30px;background:#f0fdf4;color:#15803d;">H</th>
                                    <th style="width:30px;background:#eff6ff;color:#1e40af;">S</th>
                                    <th style="width:30px;background:#faf5ff;color:#7e22ce;">I</th>
                                    <th style="width:30px;background:#fef2f2;color:#b91c1c;">A</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach($rekapSiswa as $rs): ?>
                                <tr>
                                    <td style="text-align:center;color:#94a3b8;font-weight:700;"><?php echo $no++; ?></td>
                                    <td style="text-align:center;font-weight:700;color:#475569;"><?php echo htmlspecialchars($rs['nis'] ?: '-'); ?></td>
                                    <td style="font-weight:700;color:#0f172a;padding-left:12px;"><?php echo htmlspecialchars($rs['nama']); ?></td>
                                    <td style="text-align:center;">
                                        <span style="background:#f1f5f9;color:#475569;padding:2px 8px;border-radius:6px;font-weight:800;font-size:0.75rem;">
                                            <?php echo htmlspecialchars($rs['nama_kelas']); ?>
                                        </span>
                                    </td>

                                    <?php foreach($daftarPertemuan as $p): 
                                        $st = $rs['harian'][$p['id']] ?? '-';
                                    ?>
                                        <td style="text-align:center;padding:4px 2px;">
                                            <?php if ($st !== '-'): ?>
                                                <span class="badge-st badge-st-<?php echo $st; ?>"><?php echo $st; ?></span>
                                            <?php else: ?>
                                                <span class="badge-st-none">-</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>

                                    <td style="text-align:center;font-weight:800;color:#15803d;background:#f0fdf4;"><?php echo $rs['hadir']; ?></td>
                                    <td style="text-align:center;font-weight:800;color:#1e40af;background:#eff6ff;"><?php echo $rs['sakit']; ?></td>
                                    <td style="text-align:center;font-weight:800;color:#7e22ce;background:#faf5ff;"><?php echo $rs['izin']; ?></td>
                                    <td style="text-align:center;font-weight:800;color:#b91c1c;background:#fef2f2;"><?php echo $rs['alpa']; ?></td>
                                    <td style="text-align:center;font-weight:900;background:#f0f9ff;color:#0369a1;">
                                        <?php echo $rs['persentase']; ?>%
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>
