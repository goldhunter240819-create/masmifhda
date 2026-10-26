<?php
$title = "Atur Jadwal - " . htmlspecialchars($kelas['nama_kelas']);
$sp = strpos($_SERVER['REQUEST_URI'], base_url('admin/siakad')) !== false ? base_url('admin/siakad') : base_url('siakad');
$hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

// Standard 12 slots mapping
$jam_waktu = [
    1 => ['07:30', '08:10'],
    2 => ['08:10', '08:45'],
    3 => ['08:45', '09:20'],
    4 => ['09:20', '09:55'],
    5 => ['09:55', '10:10'], // Istirahat
    6 => ['10:10', '10:40'],
    7 => ['10:40', '11:15'],
    8 => ['11:15', '11:45'],
    9 => ['11:45', '12:10'],
    10=> ['12:10', '12:40'], // Ishoma
    11=> ['12:40', '13:15'],
    12=> ['13:15', '13:50']
];
?>
<div class="siakad-container">
    <?php include __DIR__ . '/../../sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../../_siakad_header.php'; ?>

        <div class="z-main-content">
        <div class="z-page-head-row" style="display:flex;justify-content:space-between;align-items:center;">
            <div>
                <a href="<?php echo $sp; ?>/jadwal" style="text-decoration:none; color:var(--z-muted); margin-bottom: 5px; display:inline-block;"><i data-lucide="arrow-left" style="width:16px;height:16px;"></i> Kembali</a>
                <h1>Atur Jadwal Kelas <?php echo htmlspecialchars($kelas['nama_kelas']); ?></h1>
                <p>Tahun Ajaran <?php echo htmlspecialchars($activeYear['name']); ?> Semester <?php echo htmlspecialchars($semester); ?></p>
            </div>
            <div>
                <button class="e-btn e-btn-glass" onclick="document.getElementById('modal-import-jadwal').style.display='flex'">
                    <i data-lucide="file-spreadsheet"></i> Import dari Excel
                </button>
            </div>
        </div>

        <?php if(isset($_GET['msg'])): ?>
        <div class="z-alert z-alert-success" style="margin-bottom: 20px;">
            <i data-lucide="check-circle"></i>
            <div>
                <?php 
                if($_GET['msg'] == 'saved') echo 'Jadwal berhasil ditambahkan.';
                elseif($_GET['msg'] == 'imported') echo htmlspecialchars($_GET['count'] ?? 'Beberapa') . ' Jadwal berhasil di-import dari Excel.';
                else echo 'Jadwal berhasil dihapus.'; 
                ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="z-panel">
            <div class="z-panel-head">
                <div class="z-panel-title">Pengaturan Jadwal Pelajaran</div>
            </div>
            <div class="z-panel-body">
                <p style="color:var(--z-text-light); margin-bottom: 20px;">Pilih mata pelajaran pada slot jam yang tersedia. Nama guru akan otomatis menyesuaikan dengan data Penugasan Mengajar. Klik tombol centang biru di sebelah kanan untuk menyimpan baris jadwal.</p>

                <?php foreach($hari_list as $hari): ?>
                    <h3 style="margin-top:0; color:var(--z-primary); border-bottom: 2px solid #eee; padding-bottom:5px; margin-bottom:15px; display:flex; align-items:center; gap:8px;">
                        <i data-lucide="calendar" style="width:18px;height:18px;"></i> <?php echo $hari; ?>
                    </h3>
                    <table class="z-table" style="margin-bottom: 40px;">
                        <thead>
                            <tr>
                                <th style="width: 70px; text-align:center;">Jam Ke</th>
                                <th style="width: 130px;">Waktu</th>
                                <th>Mata Pelajaran</th>
                                <th>Guru Pengampu</th>
                                <th style="width: 100px; text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            for($i=1; $i<=12; $i++): 
                                $jam_mulai_txt = $jam_waktu[$i][0];
                                $jam_selesai_txt = $jam_waktu[$i][1];
                                
                                // Cek apakah ada jadwal di jam ini. controller pass substr($j['jam_mulai'], 0, 5) which is $jam_mulai_txt
                                $existing = $jadwal[$hari][$jam_mulai_txt] ?? null;
                                
                                // Jam istirahat disable
                                $is_break = ($i == 5 || $i == 10);
                            ?>
                            <tr id="row-<?php echo $hari; ?>-<?php echo $i; ?>" style="<?php echo $is_break ? 'background-color:#f8fafc;' : ''; ?>">
                                <td style="text-align:center; font-weight:600; <?php echo $is_break ? 'color:#94a3b8;' : ''; ?>">
                                    <?php echo $i; ?>
                                </td>
                                <td style="font-size:0.9rem; <?php echo $is_break ? 'color:#94a3b8;' : ''; ?>">
                                    <?php echo $jam_mulai_txt; ?> - <?php echo $jam_selesai_txt; ?>
                                </td>
                                <?php if($is_break): ?>
                                    <td colspan="2" style="color:#94a3b8; font-style:italic;">
                                        <?php echo $i == 5 ? 'ISTIRAHAT' : 'ISHOMA'; ?>
                                    </td>
                                    <td></td>
                                <?php else: ?>
                                    <td>
                                        <select class="z-input select-mapel" data-hari="<?php echo $hari; ?>" data-jam-ke="<?php echo $i; ?>" style="padding: 6px 12px; font-size: 0.9rem; height: auto;">
                                            <option value="">-- Kosong --</option>
                                            <?php foreach($mapel as $m): ?>
                                            <option value="<?php echo $m['id']; ?>" <?php echo ($existing && $existing['mata_pelajaran_id'] == $m['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($m['nama_mapel']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="guru-name" id="guru-<?php echo $hari; ?>-<?php echo $i; ?>" style="font-weight: 500; font-size:0.9rem; color:var(--z-text);">
                                            <?php echo $existing ? htmlspecialchars($existing['nama_guru']) : '<span style="color:#cbd5e1;font-style:italic;">Belum diatur</span>'; ?>
                                        </div>
                                        <input type="hidden" class="guru-id-input" id="guru-id-<?php echo $hari; ?>-<?php echo $i; ?>" value="<?php echo $existing ? $existing['guru_id'] : ''; ?>">
                                    </td>
                                    <td style="text-align:center;">
                                        <div id="loader-<?php echo $hari; ?>-<?php echo $i; ?>" style="display:none; text-align:center; padding: 6px;">
                                            <i data-lucide="loader" class="z-spin" style="width:16px;height:16px; color:var(--z-primary);"></i>
                                        </div>
                                        <div id="success-<?php echo $hari; ?>-<?php echo $i; ?>" style="display:none; text-align:center; padding: 6px; color: #10b981;">
                                            <i data-lucide="check-circle" style="width:16px;height:16px;"></i>
                                        </div>
                                        <button class="save-btn" 
                                            data-hari="<?php echo $hari; ?>" 
                                            data-jam-ke="<?php echo $i; ?>" 
                                            data-jam-mulai="<?php echo $jam_mulai_txt . ':00'; ?>"
                                            data-jam-selesai="<?php echo $jam_selesai_txt . ':00'; ?>"
                                            data-jadwal-id="<?php echo $existing ? $existing['id'] : ''; ?>"
                                            id="btn-save-<?php echo $hari; ?>-<?php echo $i; ?>"
                                            style="display:none;"></button>
                                    </td>
                                <?php endif; ?>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    </div>
</div>

<!-- Modal Import -->
<div id="modal-import-jadwal" class="z-modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;z-index:9999;">
    <div class="z-modal-content" style="background:#fff;border-radius:12px;padding:24px;width:100%;max-width:600px;box-shadow:0 10px 25px rgba(0,0,0,0.1);">
        <h2 style="margin:0 0 16px 0;font-size:1.2rem;display:flex;align-items:center;gap:10px;"><i data-lucide="file-spreadsheet" style="color:#0ea5e9;"></i> Import Jadwal dari Excel</h2>
        <form action="<?php echo $sp; ?>/jadwal/import/<?php echo $kelas['id']; ?>" method="POST">
            <div style="margin-bottom:16px;">
                <label style="display:block;margin-bottom:8px;font-weight:600;font-size:0.9rem;">Paste Data Excel</label>
                <p style="font-size:0.8rem;color:var(--z-text-light);margin-top:0;margin-bottom:8px;">Silakan *copy* data dari Excel (Hanya datanya saja tanpa header) dan *paste* ke kotak di bawah ini.</p>
                <div style="background:#f8fafc;padding:10px;border-radius:6px;margin-bottom:10px;font-size:0.8rem;border:1px dashed #cbd5e1;">
                    <strong>Format Kolom:</strong><br>
                    Hari (Tab) Jam Mulai (Tab) Jam Selesai (Tab) Nama Mapel (Tab) Nama Guru
                </div>
                <textarea name="paste_data" rows="10" style="width:100%;border:1px solid var(--z-border);border-radius:8px;padding:12px;font-family:monospace;font-size:0.85rem;" placeholder="Senin    07:00    08:00    Matematika    Budi Santoso..." required></textarea>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:10px;">
                <button type="button" class="z-btn z-btn-outline" onclick="document.getElementById('modal-import-jadwal').style.display='none'">Batal</button>
                <button type="submit" class="z-btn z-btn-primary"><i data-lucide="upload"></i> Proses Import</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Data Mengajar (Mapel -> Guru)
    const mengajarMap = <?php echo json_encode($mengajar_map); ?>;
    const saveUrl = "<?php echo $sp; ?>/jadwal/save_inline/<?php echo $kelas['id']; ?>";

    // Handle mapel change
    $('.select-mapel').on('change', function() {
        const hari = $(this).data('hari');
        const jamKe = $(this).data('jam-ke');
        const mapelId = $(this).val();
        
        const guruTextEl = $('#guru-' + hari + '-' + jamKe);
        const guruIdEl = $('#guru-id-' + hari + '-' + jamKe);
        const btnSaveEl = $('#btn-save-' + hari + '-' + jamKe);

        // Indicate changes pending
        btnSaveEl.css('opacity', '1');
        btnSaveEl.removeClass('btn-primary').addClass('btn-warning');

        if (!mapelId) {
            guruTextEl.html('<span style="color:#cbd5e1;font-style:italic;">Dikosongkan</span>');
            guruIdEl.val('');
            btnSaveEl.click();
            return;
        }

        // Look up guru
        if (mengajarMap[mapelId]) {
            guruTextEl.html(mengajarMap[mapelId].nama_guru);
            guruIdEl.val(mengajarMap[mapelId].guru_id);
            // Trigger auto-save when Guru is successfully populated
            btnSaveEl.click();
        } else {
            guruTextEl.html('<span style="color:#ef4444;font-size:0.8rem;"><i data-lucide="alert-circle" style="width:14px;height:14px;display:inline-block;vertical-align:middle;"></i> Belum ada penugasan</span>');
            guruIdEl.val('');
            lucide.createIcons();
            // Also trigger auto save to clear it if it was empty, or it will just fail saving
            btnSaveEl.click();
        }
    });

    // Handle save (Auto triggered now)
    $('.save-btn').on('click', function() {
        const btn = $(this);
        const hari = btn.data('hari');
        const jamKe = btn.data('jam-ke');
        
        const mapelId = btn.closest('tr').find('.select-mapel').val();
        const guruId = $('#guru-id-' + hari + '-' + jamKe).val();
        const jamMulai = btn.data('jam-mulai');
        const jamSelesai = btn.data('jam-selesai');
        const jadwalId = btn.attr('data-jadwal-id'); // Use attr to get dynamic updates

        if (mapelId && !guruId) {
            // Cannot save if mapel selected but no guru assigned in penugasan
            return;
        }

        const loader = $('#loader-' + hari + '-' + jamKe);
        const successIcon = $('#success-' + hari + '-' + jamKe);
        successIcon.hide();
        loader.show();

        $.ajax({
            url: saveUrl,
            type: 'POST',
            data: {
                hari: hari,
                jam_mulai: jamMulai,
                jam_selesai: jamSelesai,
                mapel_id: mapelId,
                guru_id: guruId,
                jadwal_id: jadwalId
            },
            success: function(res) {
                loader.hide();
                
                if (res.status === 'success') {
                    // Update UI to success state
                    if (res.jadwal_id) {
                        btn.attr('data-jadwal-id', res.jadwal_id);
                    } else {
                        btn.attr('data-jadwal-id', '');
                    }
                    // Show checkmark briefly
                    successIcon.show();
                    lucide.createIcons();
                    setTimeout(() => { successIcon.fadeOut(); }, 2000);
                } else {
                    alert(res.message);
                }
            },
            error: function() {
                loader.hide();
                alert('Terjadi kesalahan jaringan.');
            }
        });
    });
});
</script>

<style>
.z-spin {
    animation: z-spin 1s linear infinite;
}
@keyframes z-spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
