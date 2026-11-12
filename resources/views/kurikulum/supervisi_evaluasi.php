<?php
// Tentukan URL Action
$actionUrl = isset($actionUrl) ? $actionUrl : (($tipe == 'Kelas') ? '/kurikulum/supervisi/kelas/evaluasi' : '/kurikulum/supervisi/administrasi/evaluasi');
$backUrl = isset($backUrl) ? $backUrl : (($tipe == 'Kelas') ? '/kurikulum/supervisi/kelas' : '/kurikulum/supervisi/administrasi');

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title='Evaluasi Supervisi ' . $tipe; include __DIR__.'/../admin/_zamrud_head.php'; ?>
</head>
<body>
<?php $page_badge = 'Kurikulum'; ?>
<div class="z-wrap">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../siakad/_siakad_header.php'; ?>
        
        <!-- MULAI KONTEN ANDA DI SINI -->
        <div class="elite-page-header">
            <div class="header-bg-shapes"></div>
            <div class="header-content-inner">
                <div class="header-text-premium">
                    <div class="header-badge">SUPERVISI</div>
                    <h1><i data-lucide="clipboard-check"></i> Evaluasi Supervisi <?= $tipe ?></h1>
                    <p>Form penilaian dan catatan rekomendasi hasil pemantauan.</p>
                </div>
            </div>
        </div>
        
        <div class="z-panel" style="margin-top: 1.5rem;">
            <div class="z-panel-head" style="display:flex; justify-content:space-between; align-items:center;">
                <div class="z-panel-title">
                    <i data-lucide="clipboard-list"></i> Form Evaluasi
                </div>
                <a href="<?= $backUrl ?>" class="e-btn e-btn-outline" style="text-decoration:none;"><i data-lucide="arrow-left"></i> Kembali</a>
            </div>
            
            <form action="<?= $actionUrl ?>" method="POST">
                <input type="hidden" name="id" value="<?= $supervisi['id'] ?>">
                
                <div style="padding: 1.5rem; background:#f8fafc; border-bottom:1px solid #e2e8f0; display:flex; flex-direction:column; gap:1.25rem;">
                    <div>
                        <div style="font-size:0.8rem; color:#64748b; font-weight:600; text-transform:uppercase;">TANGGAL SUPERVISI</div>
                        <div style="font-size:1rem; color:#1e293b; font-weight:500; margin-top:0.25rem;"><?= date('d F Y', strtotime($supervisi['tanggal'])) ?></div>
                    </div>
                    <div>
                        <div style="font-size:0.8rem; color:#64748b; font-weight:600; text-transform:uppercase;"><?= $tipe == 'Kelas' ? 'NAMA GURU' : 'NAMA PEGAWAI' ?></div>
                        <div style="font-size:1rem; color:#1e293b; font-weight:500; margin-top:0.25rem;"><?= htmlspecialchars($supervisi['nama_guru'] ?? $supervisi['nama_pegawai'] ?? '-') ?></div>
                    </div>
                    <?php if(!empty($supervisi['nama_kelas']) && !empty($supervisi['nama_mapel'])): ?>
                    <div>
                        <div style="font-size:0.8rem; color:#64748b; font-weight:600; text-transform:uppercase;">KELAS & MAPEL</div>
                        <div style="font-size:1rem; color:#1e293b; font-weight:500; margin-top:0.25rem;"><?= htmlspecialchars($supervisi['nama_kelas']) ?> - <?= htmlspecialchars($supervisi['nama_mapel']) ?></div>
                    </div>
                    <?php endif; ?>
                    <div>
                        <div style="font-size:0.8rem; color:#64748b; font-weight:600; text-transform:uppercase;">SUPERVISOR</div>
                        <div style="font-size:1rem; color:#1e293b; font-weight:500; margin-top:0.25rem;"><?= htmlspecialchars($supervisi['nama_supervisor']) ?></div>
                    </div>
                </div>

                <div style="padding: 1.5rem; overflow-x:auto;">
                    <table class="z-table" style="width:100%; border-collapse:collapse;">
                        <thead>
                            <tr style="background:#f1f5f9;">
                                <th style="width:50px; text-align:center;">No</th>
                                <th>Aspek Penilaian</th>
                                <th style="width:120px; text-align:center;">Skor Maks</th>
                                <th style="width:120px; text-align:center;">Skor Diberikan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($instrumen)): ?>
                            <tr>
                                <td colspan="4" align="center" style="padding: 2rem; color:#64748b;">
                                    Belum ada instrumen untuk kategori ini. Silakan tambahkan instrumen terlebih dahulu.
                                </td>
                            </tr>
                            <?php else: 
                                $grouped = [];
                                foreach($instrumen as $ins) {
                                    $kategori = $ins['kategori'] ?: 'LAIN-LAIN';
                                    $grouped[$kategori][] = $ins;
                                }
                                $letters = range('A', 'Z');
                                $c_index = 0;
                            ?>
                                <?php foreach($grouped as $kategori => $items): ?>
                                <tr>
                                    <td align="center" style="font-weight:bold; background:#f8fafc; padding:0.75rem;"><?= $letters[$c_index++] ?></td>
                                    <td colspan="3" style="font-weight:bold; background:#f8fafc; padding:0.75rem; color:#1e293b;"><?= strtoupper(htmlspecialchars($kategori)) ?></td>
                                </tr>
                                <?php $no = 1; foreach($items as $ins): ?>
                                <tr>
                                    <td align="center" style="vertical-align:top; padding-top:1.2rem;"><?= $no++ ?></td>
                                    <td style="vertical-align:top; padding-top:1.2rem;">
                                        <div style="font-weight:600; color:#1e293b; margin-bottom:0.25rem;"><?= htmlspecialchars($ins['kode']) ?></div>
                                        <div style="color:#475569; line-height:1.5;"><?= htmlspecialchars($ins['aspek']) ?></div>
                                    </td>
                                    <td align="center" style="vertical-align:top; padding-top:1.2rem; font-weight:600; color:#10b981;">
                                        <?= $ins['bobot_maksimal'] ?>
                                    </td>
                                    <td align="center" style="vertical-align:top; padding-top:1rem; width:220px;">
                                        <div style="display:flex; gap:0.25rem; justify-content:center; flex-wrap:wrap;">
                                            <?php 
                                            $current = isset($hasil[$ins['id']]) ? $hasil[$ins['id']] : null;
                                            for($i=1; $i<=$ins['bobot_maksimal']; $i++): 
                                                $checked = ($current !== null && $current == $i) ? 'checked' : '';
                                            ?>
                                                <label style="cursor:pointer;" class="skor-label-<?= $ins['id'] ?>">
                                                    <input type="radio" name="skor[<?= $ins['id'] ?>]" value="<?= $i ?>" <?= $checked ?> data-max="<?= $ins['bobot_maksimal'] ?>" required style="display:none;" class="skor-radio">
                                                    <div class="skor-btn" style="width:32px; height:32px; display:flex; align-items:center; justify-content:center; border-radius:8px; border:1px solid <?= $checked ? '#10b981' : '#cbd5e1' ?>; background:<?= $checked ? '#10b981' : '#fff' ?>; color:<?= $checked ? '#fff' : '#475569' ?>; font-weight:600; font-size:0.9rem; transition:all 0.2s;">
                                                        <?= $i ?>
                                                    </div>
                                                </label>
                                            <?php endfor; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <div style="margin-top:2rem;">
                        <label style="display:block; margin-bottom:0.5rem; font-weight:600; font-size:1rem; color:#1e293b;">Catatan Evaluasi / Rekomendasi</label>
                        <textarea name="catatan_evaluasi" class="e-input" rows="4" style="width:100%; padding:1rem; font-size:0.95rem; line-height:1.6;" placeholder="Berikan catatan, masukan, atau rekomendasi untuk guru..."><?= htmlspecialchars($supervisi['catatan_evaluasi'] ?? '') ?></textarea>
                    </div>

                    <!-- PENGATURAN RENCANA TINDAK LANJUT (RTL) -->
                    <div style="margin-top:1.5rem; padding:1.25rem; background:#f8fafc; border:1px solid #cbd5e1; border-radius:12px;">
                        <div style="font-weight:700; font-size:1rem; color:#0f172a; margin-bottom:0.8rem; display:flex; align-items:center; gap:8px;">
                            <i data-lucide="compass" style="width:18px; height:18px; color:#10b981;"></i>
                            Rencana Tindak Lanjut (RTL) Hasil Supervisi
                        </div>
                        <div style="font-size:0.85rem; color:#64748b; margin-bottom:1rem;">
                            Bentuk program tindak lanjut yang akan dilaksanakan untuk pembinaan pendidik pasca-observasi. (Jika dikosongkan, sistem akan otomatis menghasilkan rekomendasi cerdas sesuai standar PKKM Kemenag pada lembar cetak).
                        </div>
                        
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                            <div>
                                <label style="display:block; margin-bottom:0.4rem; font-size:0.88rem; font-weight:600; color:#475569;">Bentuk Program RTL</label>
                                <select name="tindak_lanjut_jenis" class="e-input" style="width:100%; padding:0.6rem 0.8rem; border-radius:8px; border:1px solid #cbd5e1;">
                                    <?php 
                                    $cur_jenis = $supervisi['tindak_lanjut_jenis'] ?? '';
                                    $options_jenis = [
                                        'Supervisi Klinis & Bimbingan' => 'Supervisi Klinis & Bimbingan',
                                        'Workshop Kombel KKG' => 'Workshop Kombel KKG (Kelompok Kerja Guru)',
                                        'Pelatihan Mandiri MOOC Pintar Kemenag' => 'Pelatihan Mandiri MOOC Pintar Kemenag',
                                        'Sharing Praktik Baik' => 'Sharing Praktik Baik (Diseminasi)',
                                        'Coaching Kesejawatan' => 'Coaching Kesejawatan / Peer-Mentoring',
                                        'Supervisi Ulang (Siklus II)' => 'Supervisi Ulang (Siklus II)'
                                    ];
                                    ?>
                                    <option value="">-- Rekomendasi Cerdas Otomatis Sesuai Nilai --</option>
                                    <?php foreach($options_jenis as $val => $lbl): ?>
                                    <option value="<?= $val ?>" <?= $cur_jenis === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label style="display:block; margin-bottom:0.4rem; font-size:0.88rem; font-weight:600; color:#475569;">Target Waktu Pelaksanaan</label>
                                <input type="text" name="tindak_lanjut_deadline" class="e-input" style="width:100%; padding:0.6rem 0.8rem; border-radius:8px; border:1px solid #cbd5e1;" placeholder="Misal: 2 Minggu Pasca Supervisi" value="<?= htmlspecialchars($supervisi['tindak_lanjut_deadline'] ?? '') ?>">
                            </div>
                        </div>

                        <div style="margin-bottom:1rem;">
                            <label style="display:block; margin-bottom:0.4rem; font-size:0.88rem; font-weight:600; color:#475569;">Uraian Fokus Kegiatan RTL</label>
                            <textarea name="tindak_lanjut" class="e-input" rows="2" style="width:100%; padding:0.6rem 0.8rem; font-size:0.9rem; line-height:1.5; border-radius:8px; border:1px solid #cbd5e1;" placeholder="Deskripsikan program bimbingan / perbaikan... (Opsional)"><?= htmlspecialchars($supervisi['tindak_lanjut'] ?? '') ?></textarea>
                        </div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                            <div>
                                <label style="display:block; margin-bottom:0.4rem; font-size:0.88rem; font-weight:600; color:#475569;">Status Realisasi</label>
                                <select name="tindak_lanjut_status" class="e-input" style="width:100%; padding:0.6rem 0.8rem; border-radius:8px; border:1px solid #cbd5e1;">
                                    <?php $cur_st = $supervisi['tindak_lanjut_status'] ?? 'Dalam Proses'; ?>
                                    <option value="Dalam Proses" <?= $cur_st === 'Dalam Proses' ? 'selected' : '' ?>>Dalam Proses</option>
                                    <option value="Terlaksana" <?= $cur_st === 'Terlaksana' ? 'selected' : '' ?>>Terlaksana</option>
                                    <option value="Terjadwal" <?= $cur_st === 'Terjadwal' ? 'selected' : '' ?>>Terjadwal</option>
                                    <option value="Belum Terlaksana" <?= $cur_st === 'Belum Terlaksana' ? 'selected' : '' ?>>Belum Terlaksana</option>
                                </select>
                            </div>
                            <div>
                                <label style="display:block; margin-bottom:0.4rem; font-size:0.88rem; font-weight:600; color:#475569;">Catatan Bukti Realisasi (Opsional)</label>
                                <input type="text" name="tindak_lanjut_realisasi" class="e-input" style="width:100%; padding:0.6rem 0.8rem; border-radius:8px; border:1px solid #cbd5e1;" placeholder="Misal: Telah mengikuti pembinaan di KKG" value="<?= htmlspecialchars($supervisi['tindak_lanjut_realisasi'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div style="padding: 1.5rem; background:#f8fafc; border-top:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
                    <div style="display:flex; align-items:center; gap:1rem;">
                        <div style="font-size:0.9rem; color:#64748b; font-weight:600; text-transform:uppercase;">Estimasi Nilai:</div>
                        <div style="font-size:1.5rem; font-weight:800; color:#1e293b;" id="liveNilai">0</div>
                        <div id="liveGrade" style="font-size:0.85rem; font-weight:700; color:#64748b; background:#e2e8f0; padding:0.2rem 0.75rem; border-radius:12px;">Menunggu Input</div>
                    </div>
                    <button type="submit" class="e-btn e-btn-primary" style="background:#10b981; border:none; display:flex; align-items:center; gap:0.5rem;">
                        <i data-lucide="save" style="width:18px;height:18px;"></i> Simpan Evaluasi
                    </button>
                </div>
            </form>
        </div>
        <!-- AKHIR KONTEN ANDA -->
    </div>
</div>

<script>
    if (typeof lucide !== 'undefined') lucide.createIcons();

    const allRadios = document.querySelectorAll('.skor-radio');
    
    function updateStyles(radio) {
        const name = radio.getAttribute('name');
        document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
            const btn = r.nextElementSibling;
            if(r.checked) {
                btn.style.background = '#10b981';
                btn.style.color = '#fff';
                btn.style.borderColor = '#10b981';
            } else {
                btn.style.background = '#fff';
                btn.style.color = '#475569';
                btn.style.borderColor = '#cbd5e1';
            }
        });
    }

    function calculateTotal() {
        let totalSkor = 0;
        let maxSkor = 0;
        const checkedRadios = document.querySelectorAll('.skor-radio:checked');
        
        // Calculate max possible score first (sum of max data attribute of each distinct group)
        const groups = new Set();
        let totalMax = 0;
        document.querySelectorAll('.skor-radio').forEach(r => {
            const name = r.getAttribute('name');
            if(!groups.has(name)) {
                groups.add(name);
                totalMax += parseInt(r.getAttribute('data-max')) || 0;
            }
        });
        
        checkedRadios.forEach(r => {
            totalSkor += parseInt(r.value) || 0;
        });

        const nilaiAkhir = totalMax > 0 ? Math.round((totalSkor / totalMax) * 100) : 0;
        document.getElementById('liveNilai').innerText = nilaiAkhir;

        const gradeEl = document.getElementById('liveGrade');
        if(nilaiAkhir >= 91) { gradeEl.innerText = 'Amat Baik'; gradeEl.style.color = '#10b981'; gradeEl.style.background = '#d1fae5'; }
        else if(nilaiAkhir >= 81) { gradeEl.innerText = 'Baik'; gradeEl.style.color = '#3b82f6'; gradeEl.style.background = '#dbeafe'; }
        else if(nilaiAkhir >= 71) { gradeEl.innerText = 'Cukup'; gradeEl.style.color = '#f59e0b'; gradeEl.style.background = '#fef3c7'; }
        else { gradeEl.innerText = 'Kurang'; gradeEl.style.color = '#ef4444'; gradeEl.style.background = '#fee2e2'; }
    }

    allRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            updateStyles(this);
            calculateTotal();
        });
    });
    
    // Initial calculation on load
    calculateTotal();
</script>
</body>
</html>
