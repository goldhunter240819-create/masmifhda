<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$initial = strtoupper(substr($nama, 0, 2));

// get myApps (if needed for sidebar)
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
    <title><?php echo isset($title_page) ? $title_page : 'Detail Kelas'; ?> - Aula System</title>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . date('YmdH')); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/multi_dashboard.css?v=' . date('YmdH')); ?>">
    <style>
        .page-header { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); border-radius: 16px; padding: 2rem; color: white; display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; box-shadow: 0 10px 25px rgba(14, 165, 233, 0.2); }
        .ph-title { font-size: 1.8rem; font-weight: 800; margin: 0 0 5px 0; }
        .ph-sub { font-size: 0.95rem; opacity: 0.9; margin: 0; }
        .ph-badge { background: rgba(255,255,255,0.2); padding: 5px 12px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; backdrop-filter: blur(4px); display: inline-flex; align-items: center; }
        
        .student-table { width: 100%; border-collapse: collapse; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .student-table th { background: #f8fafc; padding: 1rem 1.25rem; text-align: left; font-size: 0.8rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; }
        .student-table td { padding: 1rem 1.25rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .student-table tr:hover td { background: #f8fafc; }
        
        .s-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; background: #e2e8f0; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; color: #64748b; }
        .s-name { font-weight: 700; color: #0f172a; font-size: 0.95rem; margin-bottom: 2px; }
        .s-nis { font-size: 0.8rem; color: #64748b; font-family: monospace; }
        
        .gender-badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; }
        .gender-l { background: #e0f2fe; color: #0369a1; }
        .gender-p { background: #fce7f3; color: #be185d; }

        .z-tab-nav { display: flex; gap: 1rem; margin-bottom: 1.5rem; border-bottom: 1px solid #e2e8f0; }
        .z-tab { font-weight: 700; color: #64748b; text-decoration: none; padding: 0.5rem 0.5rem 1rem 0.5rem; border-bottom: 2px solid transparent; transition: all 0.2s; }
        .z-tab:hover { color: #0f172a; }
        .z-tab.active { color: #0ea5e9; border-bottom-color: #0ea5e9; }
    </style>
</head>
<body>
<div class="siakad-container">
    <?php include __DIR__ . '/../sidebar_multi_guru.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/../header_multi_guru.php'; ?>

        <div class="z-scroll" style="padding: 0 var(--z-gap) var(--z-gap);">
            
            <div style="margin-top: 1.5rem; margin-bottom: 1rem;">
                <a href="<?php echo base_url('guru/tugas_tambahan/wali_kelas'); ?>" style="display:inline-flex;align-items:center;gap:4px;color:#64748b;text-decoration:none;font-size:0.85rem;font-weight:700;"><i data-lucide="arrow-left" style="width:16px;height:16px;"></i> Kembali ke Dashboard Wali Kelas</a>
            </div>

            <div class="page-header">
                <div>
                    <h1 class="ph-title">Kelas <?php echo htmlspecialchars(isset($kelasInfo['nama_kelas']) ? $kelasInfo['nama_kelas'] : ''); ?></h1>
                    <p class="ph-sub">Tahun Ajaran <?php echo htmlspecialchars(isset($tahun_ajaran) ? $tahun_ajaran : ''); ?></p>
                </div>
                <div class="ph-badge">
                    <i data-lucide="users" style="width:16px;height:16px;margin-right:6px;"></i> 
                    <?php echo isset($siswaList) ? count($siswaList) : 0; ?> Siswa
                </div>
            </div>

            <!-- Tab Navigasi Wali Kelas -->
            <div class="z-tab-nav">
                <a href="<?php echo base_url('guru/tugas_tambahan/wali_kelas/detail?id=<?php echo $kelas_id; ?>'); ?>" class="z-tab active">Data Siswa</a>
                <a href="<?php echo base_url('guru/tugas_tambahan/wali_kelas/absensi?id=<?php echo $kelas_id; ?>'); ?>" class="z-tab">Absensi</a>
                <a href="#" class="z-tab">Nilai Sikap</a>
                <a href="#" class="z-tab">Cetak Rapor</a>
            </div>

            <?php if(empty($siswaList)): ?>
                <div class="z-panel" style="text-align:center; padding:4rem 2rem; color:#64748b;">
                    <i data-lucide="users" style="width:48px; height:48px; margin-bottom:1rem; opacity:0.3; display:inline-block;"></i>
                    <h3 style="color:#0f172a; margin:0 0 10px 0;">Belum Ada Siswa</h3>
                    <p style="margin:0;">Tidak ada siswa yang terdaftar di kelas ini pada database SIAKAD.</p>
                </div>
            <?php else: ?>
                <table class="student-table">
                    <thead>
                        <tr>
                            <th style="width:50px;text-align:center;">No</th>
                            <th>Profil Siswa</th>
                            <th>NIS / NISN</th>
                            <th style="text-align:center;">L/P</th>
                            <th style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach($siswaList as $s): ?>
                        <tr>
                            <td style="text-align:center;font-weight:700;color:#94a3b8;"><?php echo $no++; ?></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:12px;">
                                    <?php if(!empty($s['foto'])): ?>
                                        <img src="<?php echo base_url('public/uploads/siswa/<?php echo $s['foto']; ?>'); ?>" class="s-avatar" onerror="this.src="<?php echo base_url('public/images/default-avatar.png'); ?>";this.onerror='';">
                                    <?php else: ?>
                                        <div class="s-avatar"><?php echo strtoupper(substr($s['nama'],0,1)); ?></div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="s-name"><?php echo htmlspecialchars($s['nama']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="s-nis"><?php echo !empty($s['nis']) ? $s['nis'] : '-'; ?> / <?php echo !empty($s['nisn']) ? $s['nisn'] : '-'; ?></div>
                            </td>
                            <td style="text-align:center;">
                                <?php if(strtoupper($s['jenis_kelamin']) == 'L'): ?>
                                    <span class="gender-badge gender-l">Laki-laki</span>
                                <?php elseif(strtoupper($s['jenis_kelamin']) == 'P'): ?>
                                    <span class="gender-badge gender-p">Perempuan</span>
                                <?php else: ?>
                                    <span class="gender-badge" style="background:#f1f5f9;color:#64748b;">-</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:center;">
                                <button class="z-btn" style="padding:0.4rem 0.75rem;font-size:0.75rem;background:#f1f5f9;color:#475569;border:none;border-radius:6px;font-weight:700;cursor:pointer;"><i data-lucide="eye" style="width:14px;height:14px;vertical-align:-2px;margin-right:2px;"></i> Detail</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
</body>
</html>


