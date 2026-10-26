<?php
$adminNama = $_SESSION['nama'] ?? 'Admin';
$adminInitial = strtoupper(substr($adminNama, 0, 2));
$current_label = 'Waka Kurikulum';
?>
<?php include __DIR__ . '/_zamrud_head.php'; ?>
<style>
.e-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 22px;
    border-radius: 16px;
    font-weight: 800;
    font-size: 0.88rem;
    cursor: pointer;
    border: none;
    transition: 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    font-family: inherit;
    text-decoration: none;
}
.z-btn-primary {
    background: var(--z-primary);
    color: white;
}
.z-btn-primary:hover {
    background: var(--z-primary-h);
    transform: translateY(-1px);
}
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body style="background: var(--z-bg); margin: 0; font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="z-wrap">
        <?php include __DIR__ . '/_sidebar_kurikulum.php'; ?>
        
        <main class="z-main">
            <header class="z-header">
                <div class="z-header-title">Supervisi Penilaian</div>
                <div class="z-header-right">
                    <div style="text-align: right; line-height: 1.2;">
                        <div style="font-weight: 700; font-size: 0.9rem; color: var(--z-text);"><?php echo htmlspecialchars($adminNama); ?></div>
                        <div style="font-size: 0.75rem; color: var(--z-muted);"><?php echo $current_label; ?></div>
                    </div>
                    <div class="z-avatar"><?php echo $adminInitial; ?></div>
                </div>
            </header>
            
            <div class="z-content">
                <div class="elite-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: var(--z-radius, 12px); margin-bottom: 2rem; color: white;">
                    <div>
                        <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                            <i data-lucide="clipboard-check" style="color: rgba(255,255,255,0.8);"></i> Supervisi Penilaian & Evaluasi
                        </h1>
                        <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Pantau progres pengisian nilai harian dan kelengkapan administrasi evaluasi guru.</p>
                    </div>
                </div>

                <div class="z-card" style="margin-bottom: 25px; padding: 20px;">
                    <form method="GET" style="display:flex; gap:15px; flex-wrap:wrap; align-items:flex-end;">
                        <div style="flex:1; min-width:200px;">
                            <label style="display:block; margin-bottom:8px; font-size:0.8rem; font-weight:700; color:var(--z-muted);">Guru Pengampu</label>
                            <select name="guru_id" class="z-input" style="width: 100%;">
                                <option value="">-- Semua Guru --</option>
                                <?php foreach($guru_list as $g): ?>
                                    <option value="<?= $g['id'] ?>" <?= $guru_id == $g['id'] ? 'selected' : '' ?>><?= htmlspecialchars($g['nama']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div style="flex:1; min-width:150px;">
                            <label style="display:block; margin-bottom:8px; font-size:0.8rem; font-weight:700; color:var(--z-muted);">Kelas</label>
                            <select name="kelas_id" class="z-input" style="width: 100%;">
                                <option value="">-- Semua Kelas --</option>
                                <?php foreach($kelas_list as $k): ?>
                                    <option value="<?= $k['id'] ?>" <?= $kelas_id == $k['id'] ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kelas']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <button type="submit" class="e-btn z-btn-primary">
                                <i data-lucide="filter" style="width: 16px; height: 16px;"></i> Tampilkan
                            </button>
                        </div>
                    </form>
                </div>

                <?php if ($kelas_id || $guru_id): ?>
                <div class="z-card">
                    <div class="z-table-wrap">
                        <table class="z-table" style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="padding-left: 20px;">KELAS</th>
                                    <th>MATA PELAJARAN</th>
                                    <th>GURU PENGAMPU</th>
                                    <th style="padding-right: 20px;">PROGRES & SUPERVISI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($monitoring_data)): ?>
                                <tr>
                                    <td colspan="4" style="text-align:center; padding: 2rem; color: #64748b;">Tidak ada data yang ditemukan.</td>
                                </tr>
                                <?php endif; ?>
                                
                                <?php foreach($monitoring_data as $row): ?>
                                <tr>
                                    <td style="font-weight:700; color: var(--z-text); vertical-align: top; padding-top: 15px; padding-left: 20px;">
                                        Kelas <?= htmlspecialchars($row['nama_kelas']) ?>
                                        <div style="font-size: 0.75rem; color: var(--z-muted); margin-top: 3px; font-weight: normal;">
                                            <i data-lucide="users" style="width:12px; height:12px; margin-right:2px; vertical-align:middle;"></i> <?= $row['total_siswa'] ?> Siswa
                                        </div>
                                    </td>
                                    <td style="vertical-align: top; padding-top: 15px;">
                                        <div style="font-weight:600; color: var(--z-primary);"><?= htmlspecialchars($row['nama_mapel']) ?></div>
                                    </td>
                                    <td style="vertical-align: top; padding-top: 15px;">
                                        <div style="font-weight:600; color: var(--z-text);"><?= htmlspecialchars($row['nama_guru']) ?></div>
                                    </td>
                                    <td style="vertical-align: top; padding-top: 15px; padding-bottom: 15px; padding-right: 20px;">
                                        <?php if (empty($row['evaluasi'])): ?>
                                            <span style="font-size: 0.8rem; color: #94a3b8; font-style: italic;">Belum ada nilai diinput</span>
                                        <?php else: ?>
                                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                                <?php foreach($row['evaluasi'] as $ev): 
                                                    $pct = $row['total_siswa'] > 0 ? round(($ev['total_dinilai'] / $row['total_siswa']) * 100) : 0;
                                                    $color = $pct >= 100 ? '#10b981' : ($pct > 0 ? '#f59e0b' : '#ef4444');
                                                    
                                                    // Supervision status
                                                    $sup = $ev['supervisi'];
                                                    $is_supervised = !empty($sup);
                                                    $all_checked = $is_supervised && $sup['ada_kisi'] && $sup['ada_analisis'] && $sup['ada_remedial'];
                                                    $sup_color = $all_checked ? '#10b981' : ($is_supervised ? '#f59e0b' : '#94a3b8');
                                                ?>
                                                <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px 16px; border-radius: 8px;">
                                                    <div style="flex: 1;">
                                                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px;">
                                                            <span style="font-weight: 700; font-size: 0.85rem; color: #334155; min-width: 40px;"><?= htmlspecialchars($ev['jenis_evaluasi']) ?></span>
                                                            <div style="flex: 1; max-width: 150px; background: #e2e8f0; height: 6px; border-radius: 3px; overflow: hidden;">
                                                                <div style="height: 100%; width: <?= $pct ?>%; background: <?= $color ?>;"></div>
                                                            </div>
                                                            <span style="font-size: 0.75rem; font-weight: 700; color: <?= $color ?>;"><?= $ev['total_dinilai'] ?>/<?= $row['total_siswa'] ?></span>
                                                        </div>
                                                        <div style="font-size: 0.75rem; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 250px;">
                                                            Materi: <?= htmlspecialchars($ev['materi']) ?>
                                                        </div>
                                                    </div>
                                                    <div style="margin-left: 15px; display: flex; flex-direction: column; align-items: flex-end;">
                                                        <button type="button" onclick="bukaSupervisi(<?= $row['guru_id'] ?>, <?= $row['kelas_id'] ?>, <?= $row['mapel_id'] ?>, '<?= htmlspecialchars($ev['jenis_evaluasi']) ?>', <?= htmlspecialchars(json_encode($sup ?? [])) ?>)" style="background: #ffffff; border: 1px solid <?= $sup_color ?>; color: <?= $sup_color ?>; padding: 6px 14px; border-radius: 6px; font-size: 0.8rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                                            <i data-lucide="check-square" style="width: 14px; height: 14px;"></i>
                                                            <?= $is_supervised ? 'Telah Disupervisi' : 'Supervisi' ?>
                                                        </button>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Modal Supervisi -->
    <div id="modalSupervisi" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15,23,42,0.5); z-index:9999; align-items:center; justify-content:center; backdrop-filter: blur(4px);">
        <div style="background:#fff; width:90%; max-width:500px; border-radius:16px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); overflow:hidden;">
            <div style="padding:15px 20px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; background: linear-gradient(to right, #f8fafc, #fff);">
                <h3 style="margin:0; font-size:1.1rem; color:#1e293b; display:flex; align-items:center; gap:8px;">
                    <i data-lucide="clipboard-check" style="color:var(--z-primary);"></i> Form Supervisi Evaluasi
                </h3>
                <button onclick="tutupSupervisi()" style="background:none; border:none; cursor:pointer; color:#94a3b8;"><i data-lucide="x"></i></button>
            </div>
            <div style="padding:20px;">
                <form id="formSupervisi" onsubmit="simpanSupervisi(event)">
                    <input type="hidden" name="guru_id" id="sup_guru_id">
                    <input type="hidden" name="kelas_id" id="sup_kelas_id">
                    <input type="hidden" name="mapel_id" id="sup_mapel_id">
                    <input type="hidden" name="jenis_evaluasi" id="sup_jenis">

                    <div style="margin-bottom: 15px; font-size: 0.9rem; color: #475569; background: #f0f9ff; padding: 12px; border-radius: 8px; border-left: 4px solid var(--z-primary);">
                        Jenis Evaluasi: <strong id="sup_jenis_label" style="color: var(--z-primary);"></strong>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom:10px; font-weight:700; font-size:0.85rem; color:#334155;">Kelengkapan Administrasi</label>
                        
                        <label style="display:flex; align-items:center; gap:10px; margin-bottom:8px; cursor:pointer; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 8px; transition: 0.2s; background: #fff;">
                            <input type="checkbox" name="ada_kisi" id="sup_ada_kisi" value="1" style="width:16px; height:16px; accent-color: var(--z-primary);">
                            <span style="font-size: 0.9rem; color: #475569;">1. Kisi-kisi Soal Tersedia</span>
                        </label>

                        <label style="display:flex; align-items:center; gap:10px; margin-bottom:8px; cursor:pointer; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 8px; transition: 0.2s; background: #fff;">
                            <input type="checkbox" name="ada_analisis" id="sup_ada_analisis" value="1" style="width:16px; height:16px; accent-color: var(--z-primary);">
                            <span style="font-size: 0.9rem; color: #475569;">2. Analisis Butir Soal Dikerjakan</span>
                        </label>

                        <label style="display:flex; align-items:center; gap:10px; margin-bottom:8px; cursor:pointer; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 8px; transition: 0.2s; background: #fff;">
                            <input type="checkbox" name="ada_remedial" id="sup_ada_remedial" value="1" style="width:16px; height:16px; accent-color: var(--z-primary);">
                            <span style="font-size: 0.9rem; color: #475569;">3. Program Remedial / Pengayaan Tersedia</span>
                        </label>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom:8px; font-weight:700; font-size:0.85rem; color:#334155;">Catatan Supervisi (Opsional)</label>
                        <textarea name="catatan" id="sup_catatan" class="z-input" rows="3" style="width:100%; resize:none;" placeholder="Berikan catatan perbaikan atau masukan untuk guru..."></textarea>
                    </div>

                    <div style="margin-bottom: 25px;">
                        <label style="display:block; margin-bottom:8px; font-weight:700; font-size:0.85rem; color:#334155;">Rencana Tindak Lanjut</label>
                        <select name="tindak_lanjut" id="sup_tindak_lanjut" class="z-input" style="width: 100%;">
                            <option value="">-- Pilih Tindak Lanjut --</option>
                            <option value="Apresiasi / Dipertahankan">Apresiasi / Dipertahankan</option>
                            <option value="Pembinaan Internal (Kepsek/Waka)">Pembinaan Internal (Kepsek/Waka)</option>
                            <option value="Teguran Lisan / Tertulis">Teguran Lisan / Tertulis</option>
                            <option value="Diikutsertakan KKG / Diklat">Diikutsertakan KKG / Diklat</option>
                        </select>
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:10px;">
                        <button type="button" onclick="tutupSupervisi()" class="e-btn" style="background:#f1f5f9; color:#475569;">Batal</button>
                        <button type="submit" class="e-btn z-btn-primary" id="btnSimpanSup">
                            <i data-lucide="save" style="width:16px; height: 16px;"></i> Simpan Supervisi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function bukaSupervisi(guruId, kelasId, mapelId, jenis, supData) {
            document.getElementById('sup_guru_id').value = guruId;
            document.getElementById('sup_kelas_id').value = kelasId;
            document.getElementById('sup_mapel_id').value = mapelId;
            document.getElementById('sup_jenis').value = jenis;
            document.getElementById('sup_jenis_label').textContent = jenis;
            
            // Set values if already supervised
            document.getElementById('sup_ada_kisi').checked = (supData && supData.ada_kisi == 1);
            document.getElementById('sup_ada_analisis').checked = (supData && supData.ada_analisis == 1);
            document.getElementById('sup_ada_remedial').checked = (supData && supData.ada_remedial == 1);
            document.getElementById('sup_catatan').value = (supData && supData.catatan) ? supData.catatan : '';
            document.getElementById('sup_tindak_lanjut').value = (supData && supData.tindak_lanjut) ? supData.tindak_lanjut : '';

            const modal = document.getElementById('modalSupervisi');
            modal.style.display = 'flex';
        }

        function tutupSupervisi() {
            document.getElementById('modalSupervisi').style.display = 'none';
        }

        function simpanSupervisi(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSimpanSup');
            btn.disabled = true;
            btn.innerHTML = '<i data-lucide="loader" class="spin" style="width:16px; height:16px;"></i> Menyimpan...';
            lucide.createIcons();

            const form = document.getElementById('formSupervisi');
            const formData = new FormData(form);

            fetch('<?= base_url('admin/kurikulum/supervisi-penilaian/save') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message || 'Terjadi kesalahan', 'error');
                    btn.disabled = false;
                    btn.innerHTML = '<i data-lucide="save" style="width:16px; height:16px;"></i> Simpan Supervisi';
                    lucide.createIcons();
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Gagal menghubungi server', 'error');
                btn.disabled = false;
                btn.innerHTML = '<i data-lucide="save" style="width:16px; height:16px;"></i> Simpan Supervisi';
                lucide.createIcons();
            });
        }
    </script>
</body>
</html>
