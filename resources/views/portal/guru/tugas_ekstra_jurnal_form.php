<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$initial = strtoupper(substr($nama, 0, 1));

// get myApps
$myApps = [];
if(isset($_SESSION['user_id'])) {
    $db_core_view = \App\Core\Database::connect('core');
    try {
        $res = $db_core_view->query("SELECT app_code FROM app_admins WHERE user_id = ".$_SESSION['user_id'])->fetchAll();
        foreach($res as $r) { $myApps[] = $r['app_code']; }
    } catch(\PDOException $e) {}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title_page; ?> - Aula System</title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . date('YmdH')); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/multi_dashboard.css?v=' . date('YmdH')); ?>">
    <style>
        .radio-group { display: flex; gap: 5px; }
        .radio-group label { flex: 1; text-align: center; padding: 6px 0; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer; font-size: 0.8rem; font-weight: 700; color: #64748b; transition: 0.2s; background: white; }
        .radio-group input[type="radio"] { display: none; }
        .radio-group input[value="Hadir"]:checked + label { background: #f0f9ff; border-color: #0ea5e9; color: #0ea5e9; }
        .radio-group input[value="Sakit"]:checked + label { background: #eff6ff; border-color: #3b82f6; color: #3b82f6; }
        .radio-group input[value="Izin"]:checked + label { background: #fefce8; border-color: #eab308; color: #eab308; }
        .radio-group input[value="Alpa"]:checked + label { background: #fef2f2; border-color: #ef4444; color: #ef4444; }
    </style>
</head>
<body>
<div class="siakad-container">
    <?php include __DIR__ . '/../sidebar_multi_guru.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../header_multi_guru.php'; ?>
        <div class="z-scroll" style="padding: 1.5rem var(--z-gap) var(--z-gap);">
            
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:1.5rem;">
                <a href="<?php echo base_url('guru/tugas_tambahan/pembina_ekstra/jurnal?ekstra=<?php echo urlencode($nama_ekstra); ?>'); ?>" class="z-btn-icon" style="background:white; border:1px solid #e2e8f0;"><i data-lucide="arrow-left"></i></a>
                <div>
                    <h2 style="margin:0; font-size:1.4rem; font-weight:800; color:#0f172a;"><?php echo $jurnal ? 'Edit' : 'Tambah'; ?> Jurnal & Absensi</h2>
                    <p style="margin:0; font-size:0.85rem; color:#64748b;">Ekstrakurikuler: <?php echo htmlspecialchars($nama_ekstra); ?></p>
                </div>
            </div>

            <form action="<?php echo base_url('guru/tugas_tambahan/pembina_ekstra/jurnal/save'); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="nama_ekstra" value="<?php echo htmlspecialchars($nama_ekstra); ?>">
                <?php if($jurnal): ?>
                    <input type="hidden" name="id" value="<?php echo $jurnal['id']; ?>">
                <?php endif; ?>

                <div style="display:grid; grid-template-columns: 1fr 2fr; gap:1.5rem; align-items:start;">
                    
                    <!-- Kiri: Form Jurnal -->
                    <div class="z-panel" style="padding:1.5rem;">
                        <h3 style="margin:0 0 1.5rem 0; font-size:1.1rem; border-bottom:1px solid #f1f5f9; padding-bottom:10px;"><i data-lucide="book-open" style="width:18px;height:18px;vertical-align:-3px;color:#8b5cf6;margin-right:5px;"></i> Detail Pertemuan</h3>
                        
                        <div style="margin-bottom:1rem;">
                            <label style="display:block; font-size:0.85rem; font-weight:700; color:#475569; margin-bottom:6px;">Tanggal Pertemuan</label>
                            <input type="date" name="tanggal" required value="<?php echo $jurnal ? $jurnal['tanggal'] : date('Y-m-d'); ?>" class="z-field" style="width:100%; padding:10px; border-radius:8px; border:1px solid #e2e8f0; outline:none; font-family:inherit;">
                        </div>

                        <div style="margin-bottom:1rem;">
                            <label style="display:block; font-size:0.85rem; font-weight:700; color:#475569; margin-bottom:6px;">Materi / Kegiatan</label>
                            <textarea name="materi" required rows="4" placeholder="Misal: Latihan dasar baris berbaris (PBB)..." style="width:100%; padding:10px; border-radius:8px; border:1px solid #e2e8f0; outline:none; font-family:inherit; resize:vertical; line-height:1.6;"><?php echo $jurnal ? htmlspecialchars($jurnal['materi']) : ''; ?></textarea>
                        </div>

                        <div style="margin-bottom:1.5rem;">
                            <label style="display:block; font-size:0.85rem; font-weight:700; color:#475569; margin-bottom:6px;">Upload Dokumentasi (Opsional)</label>
                            <input type="file" name="dokumentasi" accept="image/*" class="z-field" style="width:100%; padding:8px; border-radius:8px; border:1px solid #e2e8f0; outline:none; font-family:inherit;">
                            <?php if($jurnal && $jurnal['dokumentasi']): ?>
                                <div style="margin-top:10px; padding:10px; background:#f8fafc; border-radius:8px; border:1px solid #e2e8f0;">
                                    <span style="font-size:0.8rem; color:#64748b; display:block; margin-bottom:5px;">Foto Saat Ini:</span>
                                    <img src="<?php echo base_url('public/uploads/ekstra_jurnal/<?php echo $jurnal['dokumentasi']; ?>'); ?>" style="max-width:100%; height:auto; border-radius:6px; border:1px solid #cbd5e1;">
                                </div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="z-btn" style="width:100%; background:linear-gradient(135deg, #8b5cf6, #7c3aed); color:white; padding:12px; border-radius:10px; font-weight:bold; border:none; cursor:pointer; box-shadow:0 4px 15px rgba(139,92,246,0.3);"><i data-lucide="save" style="width:16px;height:16px;vertical-align:-3px;margin-right:5px;"></i> Simpan Jurnal & Absensi</button>
                    </div>

                    <!-- Kanan: Form Absensi -->
                    <div class="z-panel" style="padding:0; overflow:hidden;">
                        <div style="padding:1.5rem 1.5rem 1rem 1.5rem; background:#f8fafc; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
                            <h3 style="margin:0; font-size:1.1rem;"><i data-lucide="users" style="width:18px;height:18px;vertical-align:-3px;color:#8b5cf6;margin-right:5px;"></i> Presensi Kehadiran</h3>
                            <button type="button" onclick="setAllHadir()" class="z-btn" style="background:white; border:1px solid #0ea5e9; color:#0ea5e9; padding:5px 10px; border-radius:6px; font-size:0.8rem; font-weight:700; cursor:pointer;"><i data-lucide="check-square" style="width:14px;height:14px;vertical-align:-3px;"></i> Hadir Semua</button>
                        </div>
                        
                        <?php if(empty($anggotaList)): ?>
                            <div style="padding:3rem 1.5rem; text-align:center; color:#64748b;">
                                <i data-lucide="users" style="width:48px;height:48px;opacity:0.2;margin-bottom:10px;"></i>
                                <p style="margin:0;">Belum ada anggota yang bergabung di ekstrakurikuler ini.</p>
                            </div>
                        <?php else: ?>
                            <div style="max-height: 500px; overflow-y:auto;">
                                <table class="z-table" style="margin:0; border:none;">
                                    <thead style="position:sticky; top:0; background:white; z-index:10;">
                                        <tr>
                                            <th style="width:40px; text-align:center;">No</th>
                                            <th>Nama Siswa</th>
                                            <th style="width:100px;">Kelas</th>
                                            <th style="width:250px; text-align:center;">Status Kehadiran</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no=1; foreach($anggotaList as $a): 
                                            $sid = $a['siswa_id'];
                                            $status = isset($absen_map[$sid]) ? $absen_map[$sid] : 'Hadir'; // Default Hadir
                                        ?>
                                        <tr>
                                            <td style="text-align:center;"><?php echo $no++; ?></td>
                                            <td>
                                                <div style="font-weight:700; color:#0f172a;"><?php echo htmlspecialchars($a['nama']); ?></div>
                                                <div style="font-size:0.75rem; color:#64748b;"><?php echo htmlspecialchars($a['nisn']); ?></div>
                                            </td>
                                            <td><span style="background:#f1f5f9; padding:4px 8px; border-radius:6px; font-size:0.8rem; font-weight:700; color:#475569;">Kelas <?php echo htmlspecialchars($a['nama_kelas']); ?></span></td>
                                            <td>
                                                <div class="radio-group">
                                                    <input type="radio" id="h_<?php echo $sid; ?>" name="absensi[<?php echo $sid; ?>]" value="Hadir" <?php echo $status=='Hadir'?'checked':''; ?>>
                                                    <label for="h_<?php echo $sid; ?>">H</label>
                                                    
                                                    <input type="radio" id="s_<?php echo $sid; ?>" name="absensi[<?php echo $sid; ?>]" value="Sakit" <?php echo $status=='Sakit'?'checked':''; ?>>
                                                    <label for="s_<?php echo $sid; ?>">S</label>
                                                    
                                                    <input type="radio" id="i_<?php echo $sid; ?>" name="absensi[<?php echo $sid; ?>]" value="Izin" <?php echo $status=='Izin'?'checked':''; ?>>
                                                    <label for="i_<?php echo $sid; ?>">I</label>
                                                    
                                                    <input type="radio" id="a_<?php echo $sid; ?>" name="absensi[<?php echo $sid; ?>]" value="Alpa" <?php echo $status=='Alpa'?'checked':''; ?>>
                                                    <label for="a_<?php echo $sid; ?>">A</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
<script>
    lucide.createIcons();
    function setAllHadir() {
        document.querySelectorAll('input[type="radio"][value="Hadir"]').forEach(function(el) {
            el.checked = true;
        });
    }
</script>
</body>
</html>


