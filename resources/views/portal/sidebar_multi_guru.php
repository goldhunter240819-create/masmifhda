<?php
$uri = $_SERVER['REQUEST_URI'] ?? '';
$isCbtActive = (strpos($uri, 'evaluasi') !== false || strpos($uri, 'hasil-ujian') !== false || strpos($uri, 'analisis-soal') !== false || strpos($uri, 'soal-saya') !== false);
$isTugasActive = (strpos($uri, 'tugas_tambahan') !== false);
$isBerkasActive = (strpos($uri, 'berkas-ujian') !== false || strpos($uri, 'template-analisis') !== false);

$myTasks = [];
if(isset($_SESSION['user_id'])) {
    try {
        $db_core_tugas = \App\Core\Database::connect('core');
        $active_year_sidebar = \App\Core\AcademicYear::current();
        $active_year_sidebar_name = $active_year_sidebar ? $active_year_sidebar['name'] : '2025/2026';
        $myTasks = $db_core_tugas->query("SELECT tugas FROM guru_tugas WHERE guru_id = (SELECT id FROM guru WHERE user_id = ".$_SESSION['user_id'].") AND tahun_ajaran = '$active_year_sidebar_name'")->fetchAll(\PDO::FETCH_COLUMN);
        
        $guru_profile = $db_core_tugas->query("SELECT status_kepegawaian FROM guru WHERE user_id = ".$_SESSION['user_id'])->fetch();
        $status_kepegawaian = $guru_profile ? $guru_profile['status_kepegawaian'] : 'Honorer';
    } catch(\PDOException $e) {}
}
?>
    <!-- Ã¢â€â‚¬Ã¢â€â‚¬ FLOATING SIDEBAR Ã¢â€â‚¬Ã¢â€â‚¬ -->
    <aside class="z-sidebar" id="zSidebar">
        <div class="z-sidebar-logo">
            <div class="logo-icon">
                <img src="<?php echo \App\Core\Branding::getInstitusiLogo(); ?>" alt="Logo Institusi" style="width:34px;height:34px;object-fit:contain;border-radius:6px;">
            </div>
            <div>
                <div class="logo-text">MA Miftahul Huda</div>
                <div class="logo-sub">PORTAL GURU</div>
            </div>
        </div>

        <div class="z-sidebar-scroll">
            <?php include __DIR__ . '/../partials/academic_year_widget.php'; ?>
            <div class="z-nav-cat">Menu Utama</div>
            <a href="<?php echo base_url('guru/dashboard'); ?>" class="z-nav-item <?php echo (strpos($uri, base_url('guru/dashboard')) !== false && strpos($uri, base_url('guru/kamad')) === false) ? 'active' : ''; ?>">
                <i data-lucide="layout-dashboard"></i> Dashboard
            </a>
            <a href="<?php echo base_url('guru/jurnal'); ?>" class="z-nav-item <?php echo (strpos($uri, base_url('guru/jurnal')) !== false && strpos($uri, base_url('guru/kamad')) === false) ? 'active' : ''; ?>">
                <i data-lucide="book-open"></i> Jurnal Mengajar
            </a>
            <a href="<?php echo base_url('guru/absen'); ?>" class="z-nav-item <?php echo (strpos($uri, base_url('guru/absen')) !== false) ? 'active' : ''; ?>">
                <i data-lucide="calendar-check"></i> Absensi Pribadi
            </a>
            <a href="<?php echo base_url('guru/profil-guru'); ?>" class="z-nav-item <?php echo (strpos($uri, 'profil-guru') !== false) ? 'active' : ''; ?>">
                <i data-lucide="user"></i> Profil Saya
            </a>

            <?php if(!empty($myTasks) || (isset($status_kepegawaian) && in_array($status_kepegawaian, ['PNS', 'P3K', 'PPPK', 'Sertifikasi']))): ?>
            <div class="z-nav-dropdown <?php echo ($isTugasActive || strpos($uri, 'pemberkasan') !== false) ? 'open' : ''; ?>">
                <div class="z-nav-dropdown-header" onclick="this.parentElement.classList.toggle('open')">
                    <div class="left"><i data-lucide="briefcase"></i> Tugas Tambahan</div>
                    <i data-lucide="chevron-right" class="chev" style="width:14px;height:14px;"></i>
                </div>
                <div class="z-nav-subnav">
                    <?php if(in_array('Wali Kelas', $myTasks)): ?>
                        <a href="<?php echo base_url('guru/tugas_tambahan/wali_kelas'); ?>" class="z-nav-subnav-item <?php echo (strpos($uri, 'wali_kelas') !== false || strpos($uri, 'wali-kelas') !== false) ? 'active' : ''; ?>">Wali Kelas</a>
                    <?php endif; ?>
                    <?php if(in_array('Pembina Ekstrakurikuler', $myTasks)): ?>
                        <a href="<?php echo base_url('guru/tugas_tambahan/pembina_ekstra'); ?>" class="z-nav-subnav-item <?php echo (strpos($uri, 'pembina_ekstra') !== false) ? 'active' : ''; ?>">Pembina Ekstrakurikuler</a>
                    <?php endif; ?>
                    <?php if(in_array('Waka Kurikulum', $myTasks)): ?>
                        <a href="<?php echo base_url('guru/tugas_tambahan/waka_kurikulum'); ?>" class="z-nav-subnav-item <?php echo (strpos($uri, 'waka_kurikulum') !== false) ? 'active' : ''; ?>">Waka Kurikulum</a>
                    <?php endif; ?>
                    <?php 
                        $otherTasks = array_diff($myTasks, ['Wali Kelas', 'Pembina Ekstrakurikuler', 'Waka Kurikulum']);
                        foreach($otherTasks as $taskName): 
                    ?>
                        <a href="#" class="z-nav-subnav-item" style="color:#94a3b8;"><?php echo $taskName; ?> <span style="font-size:0.65rem;background:#f1f5f9;padding:1px 4px;border-radius:4px;color:#64748b;">Segera</span></a>
                    <?php endforeach; ?>
                    
                    <?php if(isset($status_kepegawaian) && in_array($status_kepegawaian, ['PNS', 'P3K', 'PPPK', 'Sertifikasi'])): ?>
                        <!-- <a href="<?php echo base_url('guru/pemberkasan'); ?>" class="z-nav-subnav-item <?php echo (strpos($uri, 'pemberkasan') !== false) ? 'active' : ''; ?>">Pemberkasan Dinas</a> -->
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if(!empty($_SESSION['is_kamad'])): ?>
            <div class="z-nav-dropdown <?php echo (strpos($uri, base_url('guru/kamad')) !== false) ? 'open' : ''; ?>">
                <div class="z-nav-dropdown-header" onclick="this.parentElement.classList.toggle('open')">
                    <div class="left"><i data-lucide="user-cog"></i> Kepala Madrasah</div>
                    <i data-lucide="chevron-right" class="chev" style="width:14px;height:14px;"></i>
                </div>
                <div class="z-nav-subnav">
                    <a href="<?php echo base_url('guru/kamad/dashboard'); ?>" class="z-nav-subnav-item <?php echo (strpos($uri, base_url('guru/kamad/dashboard')) !== false) ? 'active' : ''; ?>">Dashboard Kamad</a>
                    <a href="<?php echo base_url('guru/kamad/program'); ?>" class="z-nav-subnav-item <?php echo (strpos($uri, base_url('guru/kamad/program')) !== false) ? 'active' : ''; ?>">Program Kerja</a>
                    <a href="<?php echo base_url('guru/kamad/monitoring/jurnal'); ?>" class="z-nav-subnav-item <?php echo (strpos($uri, base_url('guru/kamad/monitoring/jurnal')) !== false) ? 'active' : ''; ?>">Monitor Jurnal Guru</a>
                    <a href="<?php echo base_url('guru/kamad/monitoring/nilai'); ?>" class="z-nav-subnav-item <?php echo (strpos($uri, base_url('guru/kamad/monitoring/nilai')) !== false) ? 'active' : ''; ?>">Monitor Nilai Siswa</a>
                    <a href="<?php echo base_url('admin/pkkm'); ?>" class="z-nav-subnav-item"><i data-lucide="settings" style="width:12px;height:12px;vertical-align:-2px;margin-right:4px;"></i> Instrumen PKKM</a>
                </div>
            </div>
            <?php endif; ?>

            <div class="z-nav-cat">Pusat Dokumen</div>
            
            <div class="z-nav-dropdown <?php echo (strpos($uri, 'perangkat') !== false || strpos($uri, 'berkas-pribadi') !== false || strpos($uri, 'manajemen-berkas') !== false || strpos($uri, 'pemberkasan') !== false) ? 'open' : ''; ?>">
                <div class="z-nav-dropdown-header" onclick="this.parentElement.classList.toggle('open')">
                    <div class="left"><i data-lucide="folder-open"></i> Pusat Dokumen</div>
                    <i data-lucide="chevron-right" class="chev" style="width:14px;height:14px;"></i>
                </div>
                <div class="z-nav-subnav">
                    <a href="<?php echo base_url('guru/perangkat'); ?>" class="z-nav-subnav-item <?php echo (strpos($uri, 'guru/perangkat') !== false) ? 'active' : ''; ?>">Perangkat Pembelajaran</a>
                    <a href="<?php echo base_url('guru/manajemen-berkas'); ?>" class="z-nav-subnav-item <?php echo (strpos($uri, 'guru/manajemen-berkas') !== false) ? 'active' : ''; ?>">Manajemen Berkas Guru</a>
                    
                    <?php if(isset($status_kepegawaian) && in_array($status_kepegawaian, ['PNS', 'P3K', 'PPPK', 'Sertifikasi'])): ?>
                        <a href="<?php echo base_url('guru/pemberkasan'); ?>" class="z-nav-subnav-item <?php echo (strpos($uri, 'pemberkasan') !== false) ? 'active' : ''; ?>">Pemberkasan Dinas</a>
                    <?php endif; ?>
                </div>
            </div>

            <?php 
            $isPkkmActive = (strpos($uri, 'guru/pkkm') !== false);
            ?>
            <div class="z-nav-dropdown <?php echo $isPkkmActive ? 'open' : ''; ?>">
                <div class="z-nav-dropdown-header" onclick="this.parentElement.classList.toggle('open')">
                    <div class="left"><i data-lucide="award"></i> PKKM</div>
                    <i data-lucide="chevron-right" class="chev" style="width:14px;height:14px;"></i>
                </div>
                <div class="z-nav-subnav">
                    <a href="<?php echo base_url('guru/pkkm/instrumen'); ?>" class="z-nav-subnav-item <?php echo (strpos($uri, 'guru/pkkm/instrumen') !== false) ? 'active' : ''; ?>">Instrumen Penilaian</a>
                    <a href="<?php echo base_url('guru/pkkm/master-berkas'); ?>" class="z-nav-subnav-item <?php echo (strpos($uri, 'guru/pkkm/master-berkas') !== false) ? 'active' : ''; ?>">Daftar Berkas</a>
                </div>
            </div>

            <div class="z-nav-cat">Aplikasi</div>

            <a href="<?php echo base_url('guru/nilai_harian'); ?>" class="z-nav-item <?php echo (strpos($uri, 'nilai_harian') !== false) ? 'active' : ''; ?>">
                <i data-lucide="file-pen"></i> Nilai Harian
            </a>
            
            <div class="z-nav-dropdown <?php echo $isBerkasActive ? 'open' : ''; ?>">
                <div class="z-nav-dropdown-header" onclick="this.parentElement.classList.toggle('open')">
                    <div class="left"><i data-lucide="upload-cloud"></i> Upload Hasil Ujian</div>
                    <i data-lucide="chevron-right" class="chev" style="width:14px;height:14px;"></i>
                </div>
                <div class="z-nav-subnav">
                    <a href="<?php echo base_url('guru/berkas-ujian/hasil'); ?>" class="z-nav-subnav-item <?php echo (strpos($uri, 'berkas-ujian') !== false) ? 'active' : ''; ?>">Upload & Hasil</a>
                    <a href="<?php echo base_url('guru/template-analisis'); ?>" class="z-nav-subnav-item <?php echo (strpos($uri, 'template-analisis') !== false) ? 'active' : ''; ?>">Cetak Template Analisis</a>
                </div>
            </div>

            <div class="z-nav-dropdown <?php echo $isCbtActive ? 'open' : ''; ?>">
                <div class="z-nav-dropdown-header" onclick="this.parentElement.classList.toggle('open')">
                    <div class="left"><i data-lucide="monitor"></i> GHCBT System</div>
                    <i data-lucide="chevron-right" class="chev" style="width:14px;height:14px;"></i>
                </div>
                <div class="z-nav-subnav">
                    <a href="<?php echo base_url('guru/soal-saya'); ?>" class="z-nav-subnav-item <?php echo (strpos($uri, 'soal-saya') !== false) ? 'active' : ''; ?>">Soal Saya</a>
                    <a href="<?php echo base_url('guru/evaluasi'); ?>" class="z-nav-subnav-item <?php echo (strpos($uri, 'evaluasi') !== false) ? 'active' : ''; ?>">Evaluasi</a>
                    <a href="<?php echo base_url('guru/hasil-ujian'); ?>" class="z-nav-subnav-item <?php echo (strpos($uri, 'hasil-ujian') !== false) ? 'active' : ''; ?>">Hasil Ujian</a>
                    <a href="<?php echo base_url('guru/analisis-soal'); ?>" class="z-nav-subnav-item <?php echo (strpos($uri, 'analisis-soal') !== false) ? 'active' : ''; ?>">Analisis Soal</a>
                    <?php if(in_array('cbt', $myApps)): ?>
                        <a href="<?php echo base_url('exam'); ?>" class="z-nav-subnav-item" style="color:#0ea5e9; font-weight:bold;"><i data-lucide="settings" style="width:12px;height:12px;vertical-align:-2px;"></i> Panel Admin CBT</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </aside>
    <div class="z-overlay" id="zOverlay" onclick="document.getElementById('zSidebar').classList.remove('open');this.classList.remove('open');"></div>


