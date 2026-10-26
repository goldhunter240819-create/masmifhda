<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$roleName = "Guru / Tendik";
if(!empty($_SESSION['is_kamad'])) $roleName = "Kepala Madrasah";
$initial = strtoupper(substr($nama, 0, 2));

$myApps = [];
if(isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2 && isset($_SESSION['user_id'])) {
    $db_core = \App\Core\Database::connect('core');
    $uid = $_SESSION['user_id'];
    try {
        $res = $db_core->query("SELECT app_code FROM app_admins WHERE user_id = $uid")->fetchAll();
        foreach($res as $r) {
            $myApps[] = $r['app_code'];
        }
    } catch(\PDOException $e) {}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Jurnal Mengajar'; ?></title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <!-- Font -->
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . date('YmdH')); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/multi_dashboard.css?v=' . date('YmdH')); ?>">
</head>
<body>

<div class="siakad-container">

<?php include __DIR__ . '/../sidebar_multi_guru.php'; ?>

    <!-- â”€â”€ MAIN AREA â”€â”€ -->
    <div class="z-main">

<?php include __DIR__ . '/../header_multi_guru.php'; ?>

        <!-- Scrollable Content -->
        <div class="z-scroll" style="padding: 0 var(--z-gap) var(--z-gap);">
            
            <!-- Hero Banner Premium -->
            <div class="elite-page-header welcome-hero" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: var(--z-radius, 12px); margin-bottom: 2rem; color: white; position: relative; overflow: hidden;">
                <div style="position: relative; z-index: 1;">
                    <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="book-open" style="color: rgba(255,255,255,0.8);"></i> Jurnal Mengajar Pribadi
                    </h1>
                    <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Kelola aktivitas mengajar dan absensi siswa di kelas Anda.</p>
                </div>
            </div>

            <?php if(isset($agendaToday) && $agendaToday): ?>
            <?php
            // Setup colors based on tipe
            if ($agendaToday['tipe'] == 'libur') {
                $bg_color = '#fef2f2';
                $border_color = '#ef4444';
                $icon = 'calendar-off';
                $text_color = '#991b1b';
                $subtext_color = '#b91c1c';
                $warning_text = 'Peringatan: Anda tidak diwajibkan mengisi Jurnal Mengajar dan Absensi Kelas pada hari ini. (Status: Dibebaskan).';
            } else if ($agendaToday['tipe'] == 'kegiatan') {
                $bg_color = '#f0fdf4';
                $border_color = '#22c55e';
                $icon = 'calendar-check';
                $text_color = '#166534';
                $subtext_color = '#15803d';
                $warning_text = 'Peringatan: Anda tidak diwajibkan mengisi Jurnal Mengajar dan Absensi Kelas pada hari ini. (Status: Dibebaskan).';
            } else if ($agendaToday['tipe'] == 'pulang_awal') {
                $bg_color = '#fefce8';
                $border_color = '#eab308';
                $icon = 'clock';
                $text_color = '#854d0e';
                $subtext_color = '#a16207';
                $jam_kosong = substr($agendaToday['jam_mulai_kosong'], 0, 5);
                $warning_text = "Peringatan: Ada jadwal Rapat/Pulang Awal. Jurnal mengajar untuk jam setelah pukul $jam_kosong dibebaskan dan tidak wajib diisi.";
            }
            ?>
            <div style="background: <?php echo $bg_color; ?>; border-left: 4px solid <?php echo $border_color; ?>; padding: 1.2rem; border-radius: 8px; margin-bottom: 2rem; display: flex; align-items: flex-start; gap: 15px;">
                <i data-lucide="<?php echo $icon; ?>" style="color: <?php echo $border_color; ?>; flex-shrink: 0; width: 24px; height: 24px;"></i>
                <div>
                    <h3 style="margin: 0 0 5px 0; color: <?php echo $text_color; ?>; font-size: 1.1rem;">
                        Hari ini ada <?php echo ucfirst(str_replace('_', ' ', $agendaToday['tipe'])); ?>: <?php echo htmlspecialchars($agendaToday['keterangan']); ?>
                    </h3>
                    <p style="margin: 0; color: <?php echo $subtext_color; ?>; font-size: 0.9rem;">
                        <?php echo $warning_text; ?>
                    </p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Konten Grid (Dari Template) -->
            <?php if(isset($_GET['success'])): ?>
                <?php if($_GET['success'] == 'delete'): ?>
                    <div style="background:#fef2f2; border:1px solid #fecaca; border-left:4px solid #ef4444; color:#991b1b; padding:15px; border-radius:8px; margin-bottom:20px;">Jurnal berhasil dihapus.</div>
                <?php else: ?>
                    <div style="background:#f0f9ff; border:1px solid #bae6fd; border-left:4px solid #0ea5e9; color:#0369a1; padding:15px; border-radius:8px; margin-bottom:20px;">Jurnal dan absensi berhasil disimpan!</div>
                <?php endif; ?>
            <?php endif; ?>
            <?php if(isset($_GET['error'])): ?>
                <div style="background:#fffbeb; border:1px solid #fde68a; border-left:4px solid #f59e0b; color:#92400e; padding:15px; border-radius:8px; margin-bottom:20px;">Terjadi kesalahan: <?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                <!-- Info Card -->
                <div class="z-card" style="padding: 1.5rem;">
                    <h3 style="margin: 0 0 1rem 0; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="smartphone" style="color: #0ea5e9; width: 20px; height: 20px;"></i> Gunakan Aplikasi Mobile
                    </h3>
                    <p style="color: #64748b; font-size: 0.95rem; line-height: 1.6;">
                        Pengisian <b>Jurnal Mengajar</b> dan <b>Absensi Siswa</b> kini dipindahkan dan dilakukan secara eksklusif melalui aplikasi mobile (HP) demi kemudahan dan kecepatan. Riwayat jurnal Anda tetap dapat dilihat pada tabel di bawah ini.
                    </p>
                </div>
                
                <!-- Aksi Cepat (Dari Template) -->
                <div class="z-card" style="padding: 1.5rem;">
                    <h3 style="margin: 0 0 1rem 0; font-size: 1.1rem;">Aksi Cepat</h3>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <button class="btn btn-primary" onclick="alert('Contoh aksi')"><i data-lucide="download" style="width:16px;height:16px;"></i> Download Panduan</button>
                        <button class="btn btn-outline" onclick="window.location.reload()"><i data-lucide="refresh-cw" style="width:16px;height:16px;"></i> Refresh Data</button>
                    </div>
                </div>
            </div>

<div class="z-panel mt-4">
    <div class="z-panel-head">
        <div class="z-panel-title"><i data-lucide="history"></i> Riwayat Jurnal Bulan Ini</div>
    </div>
    <div class="z-panel-body">
        
        <div style="margin-bottom: 20px; display: flex; gap: 10px; align-items: center;">
            <form method="GET" action="" style="display:flex; gap:10px;">
                <select name="m" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:6px;">
                    <?php
                    $months = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
                    for($i=1; $i<=12; $i++):
                        $m_val = str_pad($i, 2, "0", STR_PAD_LEFT);
                        $sel = ($m_val == $filter_month) ? 'selected' : '';
                    ?>
                    <option value="<?= $m_val ?>" <?= $sel ?>><?= $months[$i-1] ?></option>
                    <?php endfor; ?>
                </select>
                <select name="y" style="padding:8px 12px; border:1px solid #cbd5e1; border-radius:6px;">
                    <?php
                    $y_now = date('Y');
                    for($i=$y_now; $i>=$y_now-2; $i--):
                        $sel = ($i == $filter_year) ? 'selected' : '';
                    ?>
                    <option value="<?= $i ?>" <?= $sel ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit" class="z-btn z-btn-dark"><i data-lucide="filter"></i> Filter</button>
            </form>
        </div>

        <div class="z-table-responsive">
            <table class="z-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Materi Pembahasan</th>
                        <th>Status Presensi</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($jurnal_riwayat)): ?>
                    <tr><td colspan="6" style="text-align:center; color:#94a3b8; padding:20px;">Belum ada jurnal bulan ini.</td></tr>
                    <?php else: foreach($jurnal_riwayat as $j): ?>
                    <tr>
                        <td><strong><?= date('d M Y', strtotime($j['tanggal'])) ?></strong></td>
                        <td><?= htmlspecialchars($j['nama_kelas']) ?></td>
                        <td><?= htmlspecialchars($j['nama_mapel']) ?></td>
                        <td>
                            <div style="font-weight:600; color:#1e293b; margin-bottom:4px;"><?= htmlspecialchars($j['materi']) ?></div>
                            <div style="font-size:0.8rem; color:#64748b;"><?= htmlspecialchars($j['keterangan']) ?></div>
                        </td>
                        <td>
                            <span style="background:#f0f9ff; color:#0ea5e9; padding:4px 8px; border-radius:20px; font-size:0.75rem; font-weight:600;">
                                <i data-lucide="users" style="width:12px; height:12px;"></i> <?= $j['total_absen'] ?> Siswa
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo base_url('guru/jurnal/delete/' . $j['id']); ?>" onclick="return confirm('Yakin ingin menghapus jurnal ini?')" class="z-btn z-btn-danger" style="padding:6px 10px; font-size:0.8rem;"><i data-lucide="trash-2"></i> Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
function loadSiswa() {
    var kelas_id = document.getElementById('kelas_id').value;
    var wrap = document.getElementById('wrapAbsensi');
    var loading = document.getElementById('loadingSiswa');
    var list = document.getElementById('listSiswa');
    
    if (!kelas_id) {
        wrap.style.display = 'none';
        return;
    }
    
    wrap.style.display = 'block';
    list.innerHTML = '';
    loading.style.display = 'block';
    
    fetch('/api/guru/siswa-kelas?kelas_id=' + kelas_id)
        .then(response => response.json())
        .then(data => {
            loading.style.display = 'none';
            if(data.error) {
                list.innerHTML = '<div style="color:red; font-size:0.85rem; grid-column: 1 / -1;">Error: '+data.error+'</div>';
                return;
            }
            if(data.length === 0) {
                list.innerHTML = '<div style="text-align:center; color:#94a3b8; font-size:0.85rem; padding: 20px; grid-column: 1 / -1;">Tidak ada data siswa di kelas ini.</div>';
                return;
            }
            
            data.forEach(function(s, index) {
                var no = index + 1;
                var html = `
                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
                    <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem; margin-bottom: 12px; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">
                        ${no}. ${s.nama}
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                        <label style="text-align:center; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 6px; padding: 8px 5px; cursor: pointer; transition: 0.2s;">
                            <input type="radio" name="absensi[${s.id}]" value="Hadir" checked> <span style="font-size:0.8rem; font-weight:700; color:#0ea5e9;">Hadir</span>
                        </label>
                        <label style="text-align:center; background: #fefce8; border: 1px solid #fef08a; border-radius: 6px; padding: 8px 5px; cursor: pointer; transition: 0.2s;">
                            <input type="radio" name="absensi[${s.id}]" value="Sakit"> <span style="font-size:0.8rem; font-weight:700; color:#eab308;">Sakit</span>
                        </label>
                        <label style="text-align:center; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 8px 5px; cursor: pointer; transition: 0.2s;">
                            <input type="radio" name="absensi[${s.id}]" value="Izin"> <span style="font-size:0.8rem; font-weight:700; color:#3b82f6;">Izin</span>
                        </label>
                        <label style="text-align:center; background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 8px 5px; cursor: pointer; transition: 0.2s;">
                            <input type="radio" name="absensi[${s.id}]" value="Alpa"> <span style="font-size:0.8rem; font-weight:700; color:#ef4444;">Alpa</span>
                        </label>
                    </div>
                </div>
                `;
                list.innerHTML += html;
            });
        })
        .catch(err => {
            loading.style.display = 'none';
            list.innerHTML = '<div style="color:red; font-size:0.85rem; grid-column: 1 / -1;">Error koneksi jaringan.</div>';
        });
}
</script>

        </div><!-- /z-scroll -->
    </div><!-- /z-main -->
</div><!-- /siakad-container -->

<?php include __DIR__ . '/../footer_multi_guru.php'; ?>


