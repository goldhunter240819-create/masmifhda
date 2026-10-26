<?php
$adminNama = $_SESSION['nama'] ?? 'Admin';
$adminInitial = strtoupper(substr($adminNama, 0, 2));
$current_label = 'Waka Kurikulum';

$instrumen_items = [
    'A. KEGIATAN AWAL' => [
        '1' => '<b>Kegiatan apersepsi</b><br>a. Menyebutkan tujuan pembelajaran<br>b. Memberikan contoh-contoh dalam kehidupan sehari-hari<br>c. Manfaat belajar materi yang disampaikan. (KBC)',
        '2' => '<b>Memahami</b><br>Guru memberikan stimulus untuk membangkitkan rasa ingin tahu peserta didik dan mulai mengidentifikasi topik yang akan dieksplorasi (Berkesadaran dan Bermakna)/KBC'
    ],
    'B. KEGIATAN INTI' => [
        '3' => '<b>Memahami</b><br>Peserta didik mengidentifikasi dan merumuskan pertanyaan yang akan dijawab dalam proses inkuiri dan guru membimbing peserta didik untuk menyusun hipotesis atau dugaan awal.<br>(Berkesadaran dan Bermakna) / KBC',
        '4' => '<b>Mengaplikasi</b><br>Peserta didik mengumpulkan informasi dari berbagai sumber, seperti eksperimen, observasi, wawancara, atau literatur dan mencari pola atau hubungan antar konsep.<br>(Bermakna, Mengembirakan)',
        '5' => '<b>Mengaplikasi</b><br>Peserta didik mengorganisasi dan menafsirkan data yang telah dikumpulkan, menggunakan berbagai teknik analisis.<br>(Bermakna, Mengembirakan) / KBC',
        '6' => '<b>Mengaplikasi</b><br>Peserta didik menyusun kesimpulan berdasarkan bukti dan hasil analisis, serta menjelaskan temuan dan menghubungkannya dengan konsep atau teori yang relevan.<br>(Bermakna, Mengembirakan)',
        '7' => '<b>Mengaplikasi</b><br>Peserta didik menyampaikan hasil temuannya melalui presentasi, laporan tertulis, atau diskusi kelas.<br>(Bermakna, Mengembirakan) / KBC'
    ],
    'C. KEGIATAN PENUTUP' => [
        '8' => '<b>Mengaplikasi</b><br>Melakukan refleksi terhadap proses pembelajaran: apa yang telah dipelajari, kesulitan yang dihadapi, dan bagaimana meningkatkan pemahaman lebih lanjut, serta guru memberikan umpan balik konstruktif untuk memperkaya pengalaman belajar peserta didik.<br>(Berkesadaran, Bermakna)',
        '9' => '<b>Mengaplikasi</b><br>Peserta didik menerapkan hasil pembelajaran dalam konteks nyata atau proyek lanjutan dan mendorong eksplorasi lebih lanjut untuk memperdalam pemahaman konsep.<br>(Berkesadaran, Bermakna)'
    ]
];
?>
<?php include __DIR__ . '/_zamrud_head.php'; ?>
<style>
.mph-title {
    font-size: 1.5rem;
    font-weight: 800;
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 10px;
}
.mph-subtitle {
    margin: 0;
    font-size: 0.9rem;
    line-height: 1.5;
}
.e-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 0.5rem 1rem;
    background: var(--z-primary);
    color: white;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}
.e-btn:hover {
    background: var(--z-primary-h);
    transform: translateY(-1px);
}
.e-btn-ghost {
    background: transparent;
    color: var(--z-muted);
    border: 1px solid var(--z-border);
}
.e-btn-ghost:hover {
    background: #f1f5f9;
    color: #1e293b;
    transform: translateY(0);
}
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 30px;
}
.form-label {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--z-muted);
    display: block;
    margin-bottom: 8px;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body style="background: var(--z-bg); margin: 0; font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="z-wrap">
        <?php include __DIR__ . '/_sidebar_kurikulum.php'; ?>
        
        <main class="z-main">
            <header class="z-header">
                <div class="z-header-title">Supervisi Kelas</div>
                <div class="z-header-right">
                    <div style="text-align: right; line-height: 1.2;">
                        <div style="font-weight: 700; font-size: 0.9rem; color: var(--z-text);"><?php echo htmlspecialchars($adminNama); ?></div>
                        <div style="font-size: 0.75rem; color: var(--z-muted);"><?php echo $current_label; ?></div>
                    </div>
                    <div class="z-avatar"><?php echo $adminInitial; ?></div>
                </div>
            </header>
            
            <div class="z-content">
                <div class="elite-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: var(--z-radius, 12px); margin-bottom: 2rem; color: white; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                            <i data-lucide="clipboard-check" style="color: rgba(255,255,255,0.8);"></i> Instrumen Supervisi Kelas
                        </h1>
                        <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Penilaian Pelaksanaan Pembelajaran di Kelas</p>
                    </div>
                    <a href="<?= base_url('admin/kurikulum/supervisi-kelas') ?>" class="e-btn" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.4);">
                        <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Kembali
                    </a>
                </div>

                <div class="z-card" style="padding: 2rem;">
                    <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 25px; display: flex; align-items: center; gap: 15px;">
                        <div style="width: 50px; height: 50px; background: #e0f2fe; color: #0284c7; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="user" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px;">GURU YANG DISUPERVISI</div>
                            <div style="font-size: 1.1rem; font-weight: 800; color: #1e293b;"><?= htmlspecialchars($guru['nama']) ?></div>
                            <div style="font-size: 0.85rem; color: #64748b;">NIP/NUPTK: <?= htmlspecialchars($guru['nip'] ?: '-') ?></div>
                        </div>
                    </div>

                    <form method="POST" action="<?= base_url('admin/kurikulum/supervisi-kelas/save') ?>">
                        <input type="hidden" name="guru_id" value="<?= $guru['id'] ?>">

                        <div class="form-grid">
                            <div>
                                <label class="form-label">Tanggal Supervisi</label>
                                <input type="date" name="tanggal_supervisi" class="z-input" style="width: 100%;" required value="<?= $supervisi['tanggal_supervisi'] ?? date('Y-m-d') ?>">
                            </div>
                            <div>
                                <label class="form-label">Mata Pelajaran</label>
                                <input type="text" name="mata_pelajaran" class="z-input" style="width: 100%;" required placeholder="Contoh: Matematika" value="<?= htmlspecialchars($supervisi['mata_pelajaran'] ?? '') ?>">
                            </div>
                            <div>
                                <label class="form-label">Kelas</label>
                                <input type="text" name="kelas" class="z-input" style="width: 100%;" required placeholder="Contoh: VII A" value="<?= htmlspecialchars($supervisi['kelas'] ?? '') ?>">
                            </div>
                            <div>
                                <label class="form-label">Jam Ke-</label>
                                <input type="text" name="jam_ke" class="z-input" style="width: 100%;" placeholder="Contoh: 1-2" value="<?= htmlspecialchars($supervisi['jam_ke'] ?? '') ?>">
                            </div>
                            <div style="grid-column: 1 / -1;">
                                <label class="form-label">Materi Pokok</label>
                                <input type="text" name="materi_pokok" class="z-input" style="width: 100%;" required placeholder="Tuliskan materi pokok yang diajarkan..." value="<?= htmlspecialchars($supervisi['materi_pokok'] ?? '') ?>">
                            </div>
                        </div>

                        <h3 style="font-size: 1rem; font-weight: 800; color: #1e293b; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #f1f5f9;">Instrumen Penilaian Pelaksanaan Pembelajaran</h3>
                        
                        <div style="background: #eff6ff; padding: 12px 15px; border-radius: 8px; border-left: 4px solid #3b82f6; margin-bottom: 20px;">
                            <div style="font-size: 0.8rem; font-weight: 700; color: #1e40af; margin-bottom: 5px;">Keterangan Skor:</div>
                            <div style="display: flex; gap: 15px; font-size: 0.8rem; color: #1e3a8a;">
                                <div><strong style="background: white; padding: 2px 6px; border-radius: 4px; margin-right: 4px;">4</strong> Sangat Baik</div>
                                <div><strong style="background: white; padding: 2px 6px; border-radius: 4px; margin-right: 4px;">3</strong> Baik</div>
                                <div><strong style="background: white; padding: 2px 6px; border-radius: 4px; margin-right: 4px;">2</strong> Cukup</div>
                                <div><strong style="background: white; padding: 2px 6px; border-radius: 4px; margin-right: 4px;">1</strong> Kurang</div>
                            </div>
                        </div>

                        <div class="z-table-wrap" style="margin-bottom: 25px;">
                            <table class="z-table" style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th style="width: 40px; text-align: center;">NO</th>
                                        <th>KOMPONEN PENILAIAN</th>
                                        <th style="width: 150px; text-align: center;">NILAI (1-4)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($instrumen_items as $kategori => $items): ?>
                                    <tr>
                                        <td colspan="3" style="background: #f8fafc; font-weight: 800; color: #0f172a; padding: 10px 15px; border-bottom: 1px solid #e2e8f0;"><?= $kategori ?></td>
                                    </tr>
                                        <?php 
                                        $no = 1; 
                                        foreach ($items as $key => $label): 
                                            $val = $supervisi['instrumen'][$key] ?? '';
                                        ?>
                                        <tr>
                                            <td style="text-align: center; color: #64748b; padding: 12px 15px; border-bottom: 1px solid #f1f5f9;"><?= $no++ ?></td>
                                            <td style="padding: 12px 15px; color: #334155; line-height: 1.4; border-bottom: 1px solid #f1f5f9;"><?= $label ?></td>
                                            <td style="padding: 12px; text-align: center; vertical-align: middle; border-bottom: 1px solid #f1f5f9;">
                                                <select name="instrumen[<?= $key ?>]" class="z-input" required style="width: 100%; padding: 8px; font-size: 0.95rem; border-radius: 8px; text-align: center; font-weight: 700;">
                                                    <option value="">-</option>
                                                    <option value="4" <?= $val === '4' ? 'selected' : '' ?>>4</option>
                                                    <option value="3" <?= $val === '3' ? 'selected' : '' ?>>3</option>
                                                    <option value="2" <?= $val === '2' ? 'selected' : '' ?>>2</option>
                                                    <option value="1" <?= $val === '1' ? 'selected' : '' ?>>1</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label class="form-label">Catatan Tambahan (Kelebihan / Kekurangan)</label>
                            <textarea name="catatan" class="z-input" style="width: 100%; resize: vertical;" rows="4" placeholder="Tuliskan catatan observasi di sini..."><?= htmlspecialchars($supervisi['catatan'] ?? '') ?></textarea>
                        </div>

                        <div style="margin-bottom: 30px;">
                            <label class="form-label">Saran & Tindak Lanjut</label>
                            <textarea name="tindak_lanjut" class="z-input" style="width: 100%; resize: vertical;" rows="4" placeholder="Tuliskan saran perbaikan untuk guru..."><?= htmlspecialchars($supervisi['tindak_lanjut'] ?? '') ?></textarea>
                        </div>

                        <div style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 20px; border-top: 1px solid var(--z-border);">
                            <a href="<?= base_url('admin/kurikulum/supervisi-kelas') ?>" class="e-btn e-btn-ghost">
                                Batal
                            </a>
                            <button type="submit" class="e-btn" id="btnSaveSupervisi" style="padding-left: 25px; padding-right: 25px;">
                                <i data-lucide="save" style="width: 18px; height: 18px;"></i> Simpan Evaluasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        lucide.createIcons();
        document.addEventListener("DOMContentLoaded", () => {
            const form = document.querySelector('form');
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btn = document.getElementById('btnSaveSupervisi');
                const originalContent = btn.innerHTML;
                btn.innerHTML = '<i data-lucide="loader" class="spin" style="width: 18px; height: 18px;"></i> Menyimpan...';
                btn.disabled = true;
                lucide.createIcons();

                try {
                    const formData = new FormData(form);
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    if (result.success) {
                        Swal.fire({ icon: 'success', title: 'Tersimpan!', text: result.message, timer: 1500, showConfirmButton: false }).then(() => {
                            window.location.href = "<?= base_url('admin/kurikulum/supervisi-kelas') ?>";
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: result.message });
                        btn.innerHTML = originalContent;
                        btn.disabled = false;
                        lucide.createIcons();
                    }
                } catch (err) {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan sistem' });
                    btn.innerHTML = originalContent;
                    btn.disabled = false;
                    lucide.createIcons();
                }
            });
        });
    </script>
</body>
</html>
