<?php
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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title_page) ? $title_page : 'Absensi Kelas'; ?> - Aula System</title>
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

        .z-tab-nav { display: flex; gap: 1rem; margin-bottom: 1.5rem; border-bottom: 1px solid #e2e8f0; }
        .z-tab { font-weight: 700; color: #64748b; text-decoration: none; padding: 0.5rem 0.5rem 1rem 0.5rem; border-bottom: 2px solid transparent; transition: all 0.2s; }
        .z-tab:hover { color: #0f172a; }
        .z-tab.active { color: #0ea5e9; border-bottom-color: #0ea5e9; }

        /* Radio buttons untuk Absen */
        .absen-radios { display: flex; gap: 8px; justify-content: center; }
        .absen-radio { display: none; }
        .absen-label { 
            width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; font-weight: 800; font-size: 0.85rem; cursor: pointer;
            border: 2px solid #e2e8f0; color: #64748b; transition: 0.2s; background: white;
        }
        .absen-label:hover { background: #f1f5f9; }
        
        .absen-radio[value="H"]:checked + .absen-label { background: #0ea5e9; color: white; border-color: #0ea5e9; box-shadow: 0 4px 10px rgba(16,185,129,0.2); }
        .absen-radio[value="S"]:checked + .absen-label { background: #3b82f6; color: white; border-color: #3b82f6; box-shadow: 0 4px 10px rgba(59,130,246,0.2); }
        .absen-radio[value="I"]:checked + .absen-label { background: #f59e0b; color: white; border-color: #f59e0b; box-shadow: 0 4px 10px rgba(245,158,11,0.2); }
        .absen-radio[value="A"]:checked + .absen-label { background: #ef4444; color: white; border-color: #ef4444; box-shadow: 0 4px 10px rgba(239,68,68,0.2); }

        .date-picker-wrap { display: flex; align-items: center; gap: 15px; background: white; padding: 15px 20px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .date-input { border: 1.5px solid #cbd5e1; padding: 8px 12px; border-radius: 8px; font-family: inherit; font-weight: 700; color: #0f172a; outline: none; }
        .date-input:focus { border-color: #0ea5e9; }
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
                <a href="<?php echo base_url('guru/tugas_tambahan/wali_kelas/detail?id=<?php echo $kelas_id; ?>'); ?>" class="z-tab">Data Siswa</a>
                <a href="<?php echo base_url('guru/tugas_tambahan/wali_kelas/absensi?id=<?php echo $kelas_id; ?>'); ?>" class="z-tab active">Absensi</a>
                <a href="#" class="z-tab">Nilai Sikap</a>
                <a href="#" class="z-tab">Cetak Rapor</a>
            </div>

            <?php if(isset($_GET['success'])): ?>
                <div style="background:#f0f9ff; border:1px solid #bae6fd; color:#0369a1; padding:15px 20px; border-radius:12px; margin-bottom:20px; font-weight:700; display:flex; align-items:center; gap:10px;">
                    <i data-lucide="check-circle" style="width:20px;height:20px;"></i> Absensi berhasil disimpan!
                </div>
            <?php endif; ?>

            <?php if(isset($error_msg)): ?>
                <div style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:15px 20px; border-radius:12px; margin-bottom:20px; font-weight:700; display:flex; align-items:center; gap:10px;">
                    <i data-lucide="alert-circle" style="width:20px;height:20px;"></i> <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <div class="date-picker-wrap">
                <i data-lucide="calendar" style="color:#64748b;"></i>
                <div>
                    <div style="font-size:0.75rem; font-weight:800; color:#64748b; text-transform:uppercase; margin-bottom:4px;">Tanggal Absensi</div>
                    <form action="" method="GET" id="dateForm" style="display:flex;gap:10px;">
                        <input type="hidden" name="id" value="<?php echo $kelas_id; ?>">
                        <input type="date" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>" class="date-input" onchange="document.getElementById('dateForm').submit()">
                    </form>
                </div>
                <div style="margin-left:auto; display:flex; gap:15px; font-size:0.8rem; font-weight:700; color:#64748b;">
                    <div style="display:flex;align-items:center;gap:5px;"><div style="width:12px;height:12px;border-radius:3px;background:#0ea5e9;"></div>Hadir</div>
                    <div style="display:flex;align-items:center;gap:5px;"><div style="width:12px;height:12px;border-radius:3px;background:#3b82f6;"></div>Sakit</div>
                    <div style="display:flex;align-items:center;gap:5px;"><div style="width:12px;height:12px;border-radius:3px;background:#f59e0b;"></div>Izin</div>
                    <div style="display:flex;align-items:center;gap:5px;"><div style="width:12px;height:12px;border-radius:3px;background:#ef4444;"></div>Alpa</div>
                </div>
            </div>

            <?php if(empty($siswaList)): ?>
                <div class="z-panel" style="text-align:center; padding:4rem 2rem; color:#64748b;">
                    <i data-lucide="users" style="width:48px; height:48px; margin-bottom:1rem; opacity:0.3; display:inline-block;"></i>
                    <h3 style="color:#0f172a; margin:0 0 10px 0;">Belum Ada Siswa</h3>
                    <p style="margin:0;">Tidak ada siswa yang terdaftar di kelas ini pada database SIAKAD.</p>
                </div>
            <?php else: ?>
                <form action="<?php echo base_url('guru/tugas_tambahan/wali_kelas/absensi?id=<?php echo $kelas_id; ?>&tanggal=<?php echo $tanggal; ?>'); ?>" method="POST">
                    <table class="student-table">
                        <thead>
                            <tr>
                                <th style="width:50px;text-align:center;">No</th>
                                <th>Profil Siswa</th>
                                <th style="text-align:center; width: 250px;">Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=1; foreach($siswaList as $s): 
                                $sid = $s['id'];
                                $status_now = isset($absenMap[$sid]) ? $absenMap[$sid]['status'] : 'H';
                            ?>
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
                                            <div class="s-nis"><?php echo !empty($s['nis']) ? $s['nis'] : '-'; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="absen-radios">
                                        <input type="radio" name="absen[<?php echo $sid; ?>]" id="H_<?php echo $sid; ?>" value="H" class="absen-radio" <?php echo $status_now == 'H' ? 'checked' : ''; ?>>
                                        <label for="H_<?php echo $sid; ?>" class="absen-label" title="Hadir">H</label>

                                        <input type="radio" name="absen[<?php echo $sid; ?>]" id="S_<?php echo $sid; ?>" value="S" class="absen-radio" <?php echo $status_now == 'S' ? 'checked' : ''; ?>>
                                        <label for="S_<?php echo $sid; ?>" class="absen-label" title="Sakit">S</label>

                                        <input type="radio" name="absen[<?php echo $sid; ?>]" id="I_<?php echo $sid; ?>" value="I" class="absen-radio" <?php echo $status_now == 'I' ? 'checked' : ''; ?>>
                                        <label for="I_<?php echo $sid; ?>" class="absen-label" title="Izin">I</label>

                                        <input type="radio" name="absen[<?php echo $sid; ?>]" id="A_<?php echo $sid; ?>" value="A" class="absen-radio" <?php echo $status_now == 'A' ? 'checked' : ''; ?>>
                                        <label for="A_<?php echo $sid; ?>" class="absen-label" title="Alpa">A</label>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
                        <button type="submit" class="z-btn" style="background:#0f172a; color:white; border:none; padding:12px 25px; border-radius:10px; font-weight:800; font-size:0.95rem; display:flex; align-items:center; gap:8px; cursor:pointer; box-shadow:0 10px 20px rgba(15,23,42,0.15);">
                            <i data-lucide="save" style="width:18px;height:18px;"></i> Simpan Data Absensi
                        </button>
                    </div>
                </form>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
</body>
</html>


