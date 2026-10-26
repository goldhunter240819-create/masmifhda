<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$role_id = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : 0;
$roleName = "Siswa";
$profileUrl = '/profil-siswa';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tugas - <?php echo htmlspecialchars($tugas['judul']); ?></title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <style>
        :root {
            --primary: #0284c7;
            --primary-hover: #075985;
            --sidebar-bg: #071f14;
            --sidebar-hover: rgba(255, 255, 255, 0.05);
            --sidebar-text: #e2e8f0;
            --sidebar-active: #0ea5e9;
            --bg-body: #eef2f0;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --sidebar-width: 252px;
            --header-height: 70px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-body); color: var(--text-main); overflow: hidden; }
        .wrapper { display: flex; width: 100%; height: 100vh; overflow: hidden; }
        
        .sidebar { width: var(--sidebar-width); background-color: var(--sidebar-bg); color: var(--sidebar-text); display: flex; flex-direction: column; transition: all 0.3s ease; position: relative; z-index: 100; }
        .sidebar-header { height: var(--header-height); display: flex; align-items: center; justify-content: center; border-bottom: 1px solid rgba(255, 255, 255, 0.1); font-size: 1.25rem; font-weight: 700; color: #fff; gap: 10px; }
        .sidebar-scroll { flex: 1; overflow-y: auto; padding: 1rem 0; }
        .sidebar-scroll::-webkit-scrollbar { width: 5px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: rgba(0,0,0,0.1); }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }
        .nav-category { font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: rgba(255, 255, 255, 0.6); padding: 1rem 1.5rem 0.5rem; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 0.75rem 1.5rem; color: var(--sidebar-text); text-decoration: none; transition: 0.2s; font-size: 0.95rem; }
        .nav-item:hover { background-color: var(--sidebar-hover); color: #fff; }
        .nav-item.active { background-color: var(--sidebar-active); color: #fff; border-left: 4px solid #fff; }
        .nav-item i { width: 18px; height: 18px; opacity: 0.8; }
        .nav-item.active i { opacity: 1; }
        
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .header { height: var(--header-height); background-color: transparent; border-bottom: none; display: flex; align-items: center; justify-content: space-between; padding: 0 1.5rem; z-index: 10; }
        .header-left { display: flex; align-items: center; gap: 15px; }
        .menu-toggle { display: none; background: none; border: none; color: var(--text-main); cursor: pointer; padding: 5px; }
        .content-scroll { flex: 1; overflow-y: auto; padding: 1.5rem; }

        .btn-ujian { padding: 10px 20px; border-radius: 8px; font-weight: 700; text-decoration: none; font-size: 0.85rem; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; border: none; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-hover); }
        .btn-outline { background: white; border: 1px solid var(--border); color: var(--text-main); }
        .btn-outline:hover { background: #f8fafc; }

        .task-card { background: white; border-radius: 16px; border: 1px solid var(--border); padding: 30px; margin-bottom: 2rem; box-shadow: 0 4px 24px rgba(7,31,20,0.04); }
        .task-title { font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem; }
        .task-meta { display: flex; gap: 15px; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem; border-bottom: 1px solid var(--border); padding-bottom: 1rem; flex-wrap: wrap; }
        .task-description { font-size: 0.98rem; line-height: 1.7; color: #334155; white-space: pre-wrap; margin-bottom: 1.5rem; }
        
        .form-group { margin-bottom: 1.5rem; }
        .form-label { display: block; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 8px; }
        .form-control { width: 100%; border: 1px solid var(--border); border-radius: 8px; padding: 12px 16px; font-family: inherit; font-size: 0.95rem; color: var(--text-main); outline: none; transition: 0.2s; }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(5,150,105,0.1); }
        
        .status-pill { padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; }
        .status-success { background: #dcfce7; color: #0ea5e9; }
        .status-warning { background: #fef3c7; color: #d97706; }
        .status-danger { background: #fee2e2; color: #ef4444; }

        .submission-info { background: #f8fafc; border: 1px solid var(--border); border-radius: 12px; padding: 20px; margin-top: 1.5rem; }

        @media (max-width: 768px) {
            .menu-toggle { display: block !important; }
            .sidebar { position: fixed; left: -250px; height: 100vh; }
            .sidebar.active { left: 0; }
            .sidebar-overlay.active { display: block; }
            .header-right > div:first-child { display: none !important; }
        }
    </style>
</head>
<body>

<div class="wrapper">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i data-lucide="graduation-cap"></i> Aula System Portal
        </div>
        
        <div class="sidebar-scroll">
            <div class="nav-category">Main Menu</div>
            <a href="<?php echo base_url(''); ?>" class="nav-item">
                <i data-lucide="layout-dashboard"></i>
                <span>Dashboard Multi</span>
            </a>
            <a href="<?php echo $profileUrl; ?>" class="nav-item">
                <i data-lucide="user"></i>
                <span>Profil Saya</span>
            </a>

            <div class="nav-category">Aplikasi Terintegrasi</div>
            <a href="<?php echo base_url('portal/ghcbt-siswa'); ?>" class="nav-item">
                <i data-lucide="monitor"></i>
                <span>GHCBT System</span>
            </a>
            <a href="<?php echo base_url('absen-saya'); ?>" class="nav-item">
                <i data-lucide="calendar-check"></i>
                <span>Absen Saya</span>
            </a>
            <a href="<?php echo base_url('keuangan'); ?>" class="nav-item">
                <i data-lucide="wallet"></i>
                <span>Keuangan Saya</span>
            </a>
            <a href="javascript:void(0)" onclick="alert('Coming Soon')" class="nav-item">
                <i data-lucide="file-spreadsheet"></i>
                <span>E-Raport</span>
            </a>
            <a href="<?php echo base_url('elearning'); ?>" class="nav-item active">
                <i data-lucide="video"></i>
                <span>E-Learning (Tugas)</span>
            </a>

            <div class="nav-category">Lainnya</div>
            <a href="javascript:void(0)" onclick="alert('Coming Soon')" class="nav-item">
                <i data-lucide="settings"></i>
                <span>Pengaturan Akun</span>
            </a>
            
            <a href="<?php echo base_url('logout'); ?>" class="nav-item text-danger" style="margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.5rem; color: #fca5a5 !important;">
                <i data-lucide="log-out"></i>
                <span>Keluar (Logout)</span>
            </a>
        </div>
    </aside>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <main class="main-content">
        <header class="header">
            <div class="header-left">
                <button class="menu-toggle" onclick="toggleSidebar()"><i data-lucide="menu"></i></button>
                <div style="font-weight: 700; color: var(--primary);">Detail Tugas Digital</div>
            </div>
            <div class="header-right" style="display: flex; align-items: center; gap: 15px;">
                <div style="text-align: right;">
                    <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-main);"><?php echo $nama; ?></div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo $roleName; ?></div>
                </div>
                <div style="width: 36px; height: 36px; border-radius: 50%; overflow: hidden; border: 2px solid var(--primary); background: #0ea5e9; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                    <?php if (isset($foto) && $foto): ?>
                        <img src="<?php echo base_url('uploads/siswa/<?php echo $foto; ?>'); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <?php echo substr(htmlspecialchars($nama), 0, 1); ?>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <div class="content-scroll">
            
            <?php if(isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
                <div style="background: #dcfce7; color: #15803d; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-weight: 600; border: 1px solid #bbf7d0;">
                    Tugas berhasil dikumpulkan!
                </div>
            <?php elseif(isset($_GET['msg']) && $_GET['msg'] === 'error_size'): ?>
                <div style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-weight: 600; border: 1px solid #fca5a5;">
                    Ukuran file terlalu besar! Maksimal ukuran file adalah 20MB.
                </div>
            <?php endif; ?>

            <div style="margin-bottom: 1.5rem;">
                <a href="<?php echo base_url('elearning'); ?>" class="btn-ujian btn-outline"><i data-lucide="arrow-left" style="width:14px;"></i> Kembali ke E-Learning</a>
            </div>

            <div class="task-card">
                <h1 class="task-title"><?php echo htmlspecialchars($tugas['judul']); ?></h1>
                
                <div class="task-meta">
                    <div><i data-lucide="book" style="width:14px; vertical-align:middle;"></i> Mapel: <b><?php echo htmlspecialchars($tugas['nama_mapel']); ?></b></div>
                    <div><i data-lucide="user" style="width:14px; vertical-align:middle;"></i> Guru: <b><?php echo htmlspecialchars($tugas['nama_guru']); ?></b></div>
                    <div><i data-lucide="calendar" style="width:14px; vertical-align:middle;"></i> Batas Akhir: <b style="color:#ef4444;"><?php echo date('d/m/Y H:i', strtotime($tugas['tgl_tutup'])); ?></b></div>
                </div>

                <div class="task-description">
                    <?php echo htmlspecialchars($tugas['deskripsi']); ?>
                </div>

                <?php if($tugas['file_lampiran']): ?>
                    <div style="background: #f1f5f9; border-radius: 10px; padding: 15px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                        <span style="font-size:0.9rem; font-weight: 700; color:var(--text-main);"><i data-lucide="paperclip" style="width:16px; vertical-align:middle;"></i> Berkas Petunjuk Guru</span>
                        <a href="<?php echo base_url('uploads/elearning/<?php echo $tugas['file_lampiran']; ?>'); ?>" target="_blank" class="btn-ujian btn-primary" style="padding: 6px 14px; font-size: 0.8rem;"><i data-lucide="download" style="width:12px;"></i> Unduh Berkas</a>
                    </div>
                <?php endif; ?>

                <?php 
                $is_closed = strtotime($tugas['tgl_tutup']) < time();
                if($kumpul): 
                ?>
                    <div class="submission-info">
                        <h3 style="font-weight: 800; font-size: 1.05rem; margin-bottom: 1rem; color: var(--primary);"><i data-lucide="check-circle" style="vertical-align:middle; width: 18px;"></i> Status Pengumpulan Anda</h3>
                        
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 1rem; font-size: 0.88rem;">
                            <div>Tanggal Kumpul: <b><?php echo date('d/m/Y H:i', strtotime($kumpul['waktu_kumpul'])); ?></b></div>
                            <div>Status Kelulusan/Nilai: 
                                <?php if($kumpul['status'] === 'Dinilai'): ?>
                                    <span class="status-pill status-success">Sudah Dinilai</span>
                                <?php else: ?>
                                    <span class="status-pill status-warning">Menunggu Koreksi</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if($kumpul['jawaban_text']): ?>
                            <div style="margin-bottom: 1rem; font-size: 0.9rem; border-top: 1px dashed var(--border); padding-top: 10px;">
                                <div style="font-weight:700; color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; margin-bottom:5px;">Jawaban Teks:</div>
                                <div style="white-space:pre-wrap; background:white; padding:10px; border-radius:6px; border:1px solid var(--border);"><?php echo htmlspecialchars($kumpul['jawaban_text']); ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if($kumpul['file_path']): ?>
                            <div style="margin-bottom: 1rem; font-size: 0.9rem;">
                                <div style="font-weight:700; color:var(--text-muted); font-size:0.75rem; text-transform:uppercase; margin-bottom:5px;">Berkas Terlampir:</div>
                                <a href="<?php echo base_url('uploads/elearning/<?php echo $kumpul['file_path']; ?>'); ?>" target="_blank" class="btn-ujian btn-outline" style="padding: 6px 14px; font-size: 0.8rem; display:inline-flex; align-items:center; gap:6px;"><i data-lucide="file" style="width:14px;"></i> Lihat Jawaban Anda</a>
                            </div>
                        <?php endif; ?>

                        <?php if($kumpul['status'] === 'Dinilai'): ?>
                            <div style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); border: 1px solid #86efac; border-radius: 10px; padding: 15px; text-align: center; margin-top: 15px;">
                                <div style="font-size:0.85rem; color: #15803d; font-weight:800; text-transform:uppercase; letter-spacing:0.5px;">Nilai Anda</div>
                                <div style="font-size: 2.2rem; font-weight: 900; color: #166534;"><?php echo floatval($kumpul['nilai']); ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if(!$is_closed && (!$kumpul || $kumpul['status'] !== 'Dinilai')): ?>
                    <div style="border-top: 1px solid var(--border); padding-top: 2rem; margin-top: 2rem;">
                        <h2 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 1.5rem;"><i data-lucide="upload" style="vertical-align:middle; width: 18px; color: var(--primary);"></i> <?php echo $kumpul ? 'Kirim Ulang Jawaban' : 'Kumpulkan Jawaban Anda'; ?></h2>
                        
                        <form action="<?php echo base_url('elearning/tugas/kumpul'); ?>" method="POST" enctype="multipart/form-data" id="taskForm">
                            <input type="hidden" name="tugas_id" value="<?php echo $tugas['id']; ?>">
                            
                            <div class="form-group">
                                <label class="form-label" for="jawaban_text">Jawaban Teks / Catatan Tambahan (Opsional)</label>
                                <textarea class="form-control" name="jawaban_text" id="jawaban_text" rows="5" placeholder="Tulis jawaban esai atau catatan pengerjaan di sini..."><?php echo $kumpul ? htmlspecialchars($kumpul['jawaban_text']) : ''; ?></textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="file_jawaban">Unggah Berkas Jawaban (Maksimal 20MB)</label>
                                <input class="form-control" type="file" name="file_jawaban" id="file_jawaban">
                                <small style="color:var(--text-muted); margin-top:5px; display:block;">Mendukung berkas dokumen (PDF, Word) atau gambar (JPG, PNG). Maksimal 20MB.</small>
                            </div>

                            <div style="display:flex; justify-content: flex-end;">
                                <button type="submit" class="btn-ujian btn-primary"><i data-lucide="send" style="width:14px;"></i> Kirim Tugas Sekarang</button>
                            </div>
                        </form>
                    </div>
                <?php elseif($is_closed && !$kumpul): ?>
                    <div style="background: #fee2e2; color: #991b1b; padding: 1.25rem; border-radius: 10px; border: 1px solid #fca5a5; font-weight: 600; text-align: center; margin-top: 2rem;">
                        Batas waktu pengumpulan telah terlewati. Anda tidak dapat lagi mengirimkan tugas.
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </main>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
        document.getElementById('sidebarOverlay').classList.toggle('active');
    }
    
    // Front-end 20MB validation
    document.getElementById('taskForm')?.addEventListener('submit', function(e) {
        var fileInput = document.getElementById('file_jawaban');
        if (fileInput.files.length > 0) {
            var size = fileInput.files[0].size;
            var maxSize = 20 * 1024 * 1024; // 20MB
            if (size > maxSize) {
                alert('Ukuran file terlalu besar! Maksimal ukuran file adalah 20MB.');
                e.preventDefault();
            }
        }
    });

    lucide.createIcons();
</script>
</body>
</html>


