<?php
$title_page = "Program Wali Kelas - " . htmlspecialchars($kelas['nama_kelas']);
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$initial = strtoupper(substr($nama, 0, 2));

$myApps = [];
if(isset($_SESSION['user_id'])) {
    $db_core_view = \App\Core\Database::connect('core');
    try {
        $res = $db_core_view->query("SELECT app_code FROM app_admins WHERE user_id = ".$_SESSION['user_id'])->fetchAll();
        foreach($res as $r) { $myApps[] = $r['app_code']; }
    } catch(\PDOException $e) {}
}

$days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title_page; ?> - Aula System</title>
    <link rel="icon" href="<?php echo htmlspecialchars(\App\Core\Branding::getInstitusiFavicon()); ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . date('YmdH')); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/multi_dashboard.css?v=' . date('YmdH')); ?>">
</head>
<body>
<div class="siakad-container">
    <?php include __DIR__ . '/../../portal/sidebar_multi_guru.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/../../portal/header_multi_guru.php'; ?>

        <div class="z-scroll" style="padding: 0 var(--z-gap) var(--z-gap);">

    <div class="z-page-head-row" style="margin-top: 1.5rem;">
        <div>
            <h1>Program Wali Kelas</h1>
            <p>Pengaturan struktur dan program kerja khusus untuk kelas <b><?php echo htmlspecialchars($kelas['nama_kelas']); ?></b></p>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="<?php echo base_url('siakad/guru/wali-kelas/program/cetak'); ?>" target="_blank" class="btn btn-secondary"><i data-lucide="printer"></i> Preview & Cetak PDF</a>
        </div>
    </div>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'saved'): ?>
    <div class="z-alert z-alert-success" style="margin-bottom: 20px;">
        <i data-lucide="check-circle"></i>
        <div>Pengaturan kelas berhasil disimpan. Silakan klik Preview & Cetak PDF untuk melihat hasilnya.</div>
    </div>
    <?php endif; ?>

    <form action="<?php echo base_url('siakad/guru/wali-kelas/pengaturan/save'); ?>" method="POST">
        
        <div class="z-panel" style="margin-bottom: 20px;">
            <div class="z-panel-head">
                <div class="z-panel-title"><i data-lucide="users"></i> Struktur Organisasi Kelas</div>
            </div>
            <div class="z-panel-body">
                <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div class="z-form-group">
                        <label class="z-label">Ketua Kelas</label>
                        <select name="ketua" class="z-input" required>
                            <option value="">-- Pilih Ketua --</option>
                            <?php foreach($siswas as $s): ?>
                            <option value="<?php echo htmlspecialchars($s['nama']); ?>" <?php if(($struktur['ketua']??'') == $s['nama']) echo 'selected'; ?>><?php echo htmlspecialchars($s['nama']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="z-form-group">
                        <label class="z-label">Sekretaris</label>
                        <select name="sekretaris" class="z-input" required>
                            <option value="">-- Pilih Sekretaris --</option>
                            <?php foreach($siswas as $s): ?>
                            <option value="<?php echo htmlspecialchars($s['nama']); ?>" <?php if(($struktur['sekretaris']??'') == $s['nama']) echo 'selected'; ?>><?php echo htmlspecialchars($s['nama']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="z-form-group">
                        <label class="z-label">Bendahara</label>
                        <select name="bendahara" class="z-input" required>
                            <option value="">-- Pilih Bendahara --</option>
                            <?php foreach($siswas as $s): ?>
                            <option value="<?php echo htmlspecialchars($s['nama']); ?>" <?php if(($struktur['bendahara']??'') == $s['nama']) echo 'selected'; ?>><?php echo htmlspecialchars($s['nama']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="z-panel" style="margin-bottom: 20px;">
            <div class="z-panel-head">
                <div class="z-panel-title"><i data-lucide="broom"></i> Jadwal Piket Kelas</div>
            </div>
            <div class="z-panel-body">
                <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <?php foreach($days as $day): ?>
                    <div class="z-form-group">
                        <label class="z-label">Piket <?php echo $day; ?></label>
                        <select name="piket_<?php echo strtolower($day); ?>[]" class="z-input piket-select" multiple="multiple" data-day="<?php echo $day; ?>">
                            <?php 
                            $selected_piket = $jadwal_piket[$day] ?? [];
                            if (is_string($selected_piket)) $selected_piket = array_map('trim', explode("\n", $selected_piket));
                            foreach($siswas as $s): 
                                $is_selected = in_array($s['nama'], $selected_piket);
                            ?>
                            <option value="<?php echo htmlspecialchars($s['nama']); ?>" <?php if($is_selected) echo 'selected'; ?>><?php echo htmlspecialchars($s['nama']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div style="text-align: right; margin-bottom: 50px;">
            <button type="submit" class="btn btn-primary btn-lg"><i data-lucide="save"></i> Simpan Pengaturan Program</button>
        </div>
    </form>

    <div class="z-panel" style="margin-bottom: 20px;">
        <div class="z-panel-head">
            <div class="z-panel-title"><i data-lucide="book-open"></i> Jadwal Mata Pelajaran (Dari SIAKAD)</div>
        </div>
        <div class="z-panel-body">
            <p style="color:var(--z-muted); margin-bottom: 15px; font-size:13px;">Jadwal pelajaran di bawah ini disinkronisasikan secara otomatis dari sistem pusat SIAKAD (diatur oleh Admin/Kurikulum).</p>
            <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                <?php foreach($days as $day): ?>
                <div class="z-form-group" style="background:#f8fafc; padding:15px; border-radius:8px; border:1px solid #e2e8f0;">
                    <label class="z-label" style="border-bottom:2px solid #e2e8f0; padding-bottom:5px; margin-bottom:10px;"><?php echo $day; ?></label>
                    <?php if(!empty($jadwal_mapel[$day])): ?>
                        <ul style="padding-left:15px; margin:0; font-size:13px;">
                        <?php foreach($jadwal_mapel[$day] as $j): ?>
                            <li style="margin-bottom:5px;">
                                <strong><?php echo substr($j['jam_mulai'],0,5); ?>-<?php echo substr($j['jam_selesai'],0,5); ?></strong><br>
                                <?php echo htmlspecialchars($j['nama_mapel']); ?><br>
                                <span style="color:var(--z-muted); font-size:11px;"><?php echo htmlspecialchars($j['nama_guru']); ?></span>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div style="color:#94a3b8; font-size:13px; text-align:center; padding:10px 0;">- Belum ada jadwal -</div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
lucide.createIcons();

$(document).ready(function() {
    $('.piket-select').select2({
        width: '100%',
        placeholder: 'Pilih siswa...'
    });

    // Mutually exclusive logic for Piket
    function updatePiketOptions() {
        let selectedAll = [];
        $('.piket-select').each(function() {
            let vals = $(this).val();
            if (vals) selectedAll = selectedAll.concat(vals);
        });

        $('.piket-select').each(function() {
            let currentSelect = $(this);
            let currentVals = currentSelect.val() || [];
            
            currentSelect.find('option').each(function() {
                let optVal = $(this).val();
                if (optVal === "") return;
                
                // If it's selected in another select, disable it here
                if (selectedAll.includes(optVal) && !currentVals.includes(optVal)) {
                    $(this).prop('disabled', true);
                } else {
                    $(this).prop('disabled', false);
                }
            });
            // Need to re-init select2 to reflect disabled state
            currentSelect.select2({ width: '100%', placeholder: 'Pilih siswa...' });
        });
    }

    $('.piket-select').on('change', function() {
        updatePiketOptions();
    });

    // Run once on load
    updatePiketOptions();
});
</script>
        </div> <!-- End z-scroll -->
    </div> <!-- End z-main -->
</div> <!-- End siakad-container -->
</body>
</html>

