<style>
    .z-avatar-premium { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .header-breadcrumb .parent { color: #f59e0b; }
    
    .absen-row { border-bottom: 1px solid #f1f5f9; transition: 0.15s; }
    .absen-row:hover { background: #fafafa; }
    
    select.status-sel {
        border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 6px 10px;
        font-size: 0.8rem; font-weight: 700; outline: none;
        appearance: none; background-image: url("data:image/svg+xml,..."); cursor: pointer;
    }
    select.status-sel.s-hadir    { border-color: #86efac; background: #f0fdf4; color: #15803d; }
    select.status-sel.s-terlambat{ border-color: #fcd34d; background: #fefce8; color: #92400e; }
    select.status-sel.s-sakit    { border-color: #93c5fd; background: #eff6ff; color: #1d4ed8; }
    select.status-sel.s-izin     { border-color: #c4b5fd; background: #f5f3ff; color: #7c3aed; }
    select.status-sel.s-alpa     { border-color: #fca5a5; background: #fef2f2; color: #dc2626; }
    
    input[type=time] {
        border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 6px 10px;
        font-size: 0.8rem; font-weight: 700; outline: none; color: #0f172a;
        width: 100px;
    }
</style>

<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    
    <div class="z-main">
        <?php $page_badge = 'Admin Portal'; include __DIR__.'/../../siakad/_siakad_header.php'; ?>


        <div class="z-scroll" style="padding: 1.5rem 2rem;">

            <?php if (isset($msg) && $msg === 'success'): ?>
            <div style="background:#dcfce7;border:1px solid #86efac;border-radius:12px;padding:1rem 1.5rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:10px;color:#15803d;font-weight:700;">
                <i data-lucide="check-circle" style="width:18px;"></i> Data absensi guru berhasil disimpan!
            </div>
            <?php endif; ?>

            <!-- Pilih Tanggal -->
            <div style="background:white;border-radius:16px;padding:1.25rem;border:1px solid #f1f5f9;margin-bottom:1.5rem;display:flex;align-items:center;gap:12px;">
                <i data-lucide="calendar" style="color:#f59e0b;width:20px;"></i>
                <label style="font-weight:700;font-size:0.9rem;color:#0f172a;">Tanggal Absensi:</label>
                <input type="date" id="tanggalInput" value="<?php echo htmlspecialchars($tanggal); ?>"
                       style="border:1.5px solid #e2e8f0;border-radius:10px;padding:7px 12px;font-weight:700;color:#0f172a;font-size:0.85rem;outline:none;"
                       onchange="window.location.href='?tanggal='+this.value">
                <div style="margin-left:auto;font-size:0.8rem;color:#64748b;font-weight:600;">
                    <i data-lucide="info" style="width:14px;vertical-align:middle;"></i>
                    Ganti tanggal untuk memuat data hari berbeda.
                </div>
            </div>

            <form method="POST" action="/admin/absen_guru_v2/save-input-manual">
                <input type="hidden" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>">
                
                <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;overflow:hidden;">
                    <div style="padding:1rem 1.5rem;background:linear-gradient(135deg,#fffbeb,#fef3c7);border-bottom:1px solid #fde68a;display:flex;align-items:center;justify-content:space-between;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <i data-lucide="clipboard-edit" style="color:#d97706;width:18px;"></i>
                            <h3 style="margin:0;font-size:0.9rem;font-weight:800;color:#92400e;">
                                Input Absensi Guru — <?php echo date('d F Y', strtotime($tanggal)); ?>
                            </h3>
                        </div>
                        <span style="font-size:0.75rem;color:#92400e;font-weight:700;"><?php echo count($guruList); ?> Guru Terdaftar</span>
                    </div>

                    <table style="width:100%;border-collapse:collapse;">
                        <thead>
                            <tr style="background:#f8fafc;text-align:left;">
                                <th style="padding:0.75rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;">No</th>
                                <th style="padding:0.75rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;">Nama Guru</th>
                                <th style="padding:0.75rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;">Jabatan</th>
                                <th style="padding:0.75rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;">Status</th>
                                <th style="padding:0.75rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;">Jam Masuk</th>
                                <th style="padding:0.75rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;">Jam Pulang</th>
                                <th style="padding:0.75rem 1rem;font-size:0.7rem;font-weight:800;color:#64748b;text-transform:uppercase;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach($guruList as $g): 
                                $ex = $existingAbsenByGuruId[$g['id']] ?? null;
                                $curStatus = $ex['status'] ?? 'Hadir';
                            ?>
                            <tr class="absen-row">
                                <td style="padding:0.75rem 1rem;color:#94a3b8;font-size:0.8rem;"><?php echo $no++; ?></td>
                                <td style="padding:0.75rem 1rem;">
                                    <div style="font-weight:700;color:#0f172a;font-size:0.85rem;"><?php echo htmlspecialchars($g['nama']); ?></div>
                                </td>
                                <td style="padding:0.75rem 1rem;font-size:0.8rem;color:#64748b;"><?php echo htmlspecialchars($g['jabatan'] ?? '-'); ?></td>
                                <td style="padding:0.75rem 1rem;">
                                    <select name="absen[<?php echo $g['id']; ?>][status]"
                                            class="status-sel s-<?php echo strtolower($curStatus); ?>"
                                            onchange="updateStyle(this)">
                                        <option value="Hadir"     <?php echo $curStatus=='Hadir'?'selected':''; ?>>✅ Hadir</option>
                                        <option value="Terlambat" <?php echo $curStatus=='Terlambat'?'selected':''; ?>>⚠️ Terlambat</option>
                                        <option value="Sakit"     <?php echo $curStatus=='Sakit'?'selected':''; ?>>🤒 Sakit</option>
                                        <option value="Izin"      <?php echo $curStatus=='Izin'?'selected':''; ?>>📝 Izin</option>
                                        <option value="Alpa"      <?php echo $curStatus=='Alpa'?'selected':''; ?>>❌ Alpa</option>
                                    </select>
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <input type="time" name="absen[<?php echo $g['id']; ?>][waktu_masuk]"
                                           value="<?php echo !empty($ex['waktu_masuk']) ? substr($ex['waktu_masuk'],0,5) : ''; ?>">
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <input type="time" name="absen[<?php echo $g['id']; ?>][waktu_keluar]"
                                           value="<?php echo !empty($ex['waktu_keluar']) ? substr($ex['waktu_keluar'],0,5) : ''; ?>">
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <input type="text" name="absen[<?php echo $g['id']; ?>][keterangan]"
                                           value="<?php echo htmlspecialchars($ex['keterangan'] ?? ''); ?>"
                                           placeholder="Opsional..."
                                           style="border:1.5px solid #e2e8f0;border-radius:8px;padding:6px 10px;font-size:0.8rem;outline:none;width:130px;">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div style="padding:1.25rem;border-top:1px solid #f1f5f9;display:flex;justify-content:flex-end;gap:12px;">
                        <a href="/admin/absen_guru_v2" style="text-decoration:none;background:#f1f5f9;color:#475569;padding:10px 20px;border-radius:10px;font-weight:700;font-size:0.85rem;">Batal</a>
                        <button type="submit" style="background:linear-gradient(135deg,#f59e0b,#d97706);color:white;border:none;padding:10px 24px;border-radius:10px;font-weight:700;font-size:0.85rem;cursor:pointer;display:flex;align-items:center;gap:8px;">
                            <i data-lucide="save" style="width:16px;"></i> Simpan Semua Data Absensi
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
function updateStyle(el) {
    el.className = 'status-sel s-' + el.value.toLowerCase();
}
</script>
