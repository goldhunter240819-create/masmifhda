<?php
$role_id = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : 0;
$perms_file = __DIR__ . '/../../../config/menu_permissions.json';
$all_perms = json_decode(file_get_contents($perms_file), true);
$perms = isset($all_perms['siakad']) ? $all_perms['siakad'] : array();
$_s_nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'Pengguna');
$_s_init = strtoupper(substr($_s_nama, 0, 2));
$_s_role_label = ($role_id == 1 || $role_id == 99) ? 'Super Admin' : ($role_id == 2 ? 'Pengajar' : 'Siswa');
$_s_uri = $_SERVER['REQUEST_URI'];
$sp = in_array($role_id, [1, 99]) ? base_url('admin/siakad') : base_url('siakad');
$sp_dash = in_array($role_id, [1, 99]) ? base_url('admin/siakad/dashboard') : base_url('siakad');

// Ambil logo institusi dari DB
$_sidebar_logo = \App\Core\Branding::getInstitusiLogo();
$db = \App\Core\Database::connect('core');
$inst = $db->query("SELECT nama FROM institusi LIMIT 1")->fetch();
$nama_institusi = $inst ? strtoupper($inst['nama']) : 'NAMA INSTITUSI';

function can_access($menu, $role_id, $perms) {
    if (!isset($perms[$menu])) return false;
    return in_array($role_id, $perms[$menu]);
}
?>
<!-- SIAKAD Sidebar (Zamrud Theme) -->
<aside class="z-sidebar" id="zSidebar">

    <!-- Brand -->
    <div class="z-sidebar-logo">
        <div class="logo-icon" style="background: white; overflow: hidden; padding: 2px;">
            <img src="<?php echo $_sidebar_logo; ?>" alt="Logo Institusi" style="width:100%;height:100%;object-fit:contain;">
        </div>
        <div>
            <div class="logo-text">MA Miftahul Huda</div>
            <div class="logo-sub" style="color:#0ea5e9;">SIAKAD</div>
        </div>
        <button class="z-close-btn" onclick="zToggleSidebar()" style="display:none;background:none;border:none;color:white;position:absolute;right:1rem;"><i data-lucide="x"></i></button>
    </div>

    <!-- Nav -->
    <div class="z-sidebar-scroll">
        <nav class="z-nav" style="padding: 1rem;">
        <?php include __DIR__ . '/../partials/academic_year_widget.php'; ?>
        <div class="z-nav-label">Akademik</div>

        <?php if(can_access('dashboard', $role_id, $perms)): ?>
        <a href="<?php echo $sp_dash; ?>" class="z-nav-item <?php echo ($_s_uri == $sp_dash || $_s_uri == $sp || $_s_uri == base_url('siakad')) ? 'active' : ''; ?>">
            <i data-lucide="layout-dashboard"></i> Dashboard
        </a>
        <?php endif; ?>

        <?php if(can_access('data_master', $role_id, $perms)): ?>
        <div class="z-nav-group <?php echo (strpos($_s_uri, $sp . '/institusi') !== false || strpos($_s_uri, $sp . '/kop-surat') !== false || strpos($_s_uri, $sp . '/mapel') !== false || strpos($_s_uri, $sp . '/kelas') !== false || strpos($_s_uri, $sp . '/ekstra') !== false) ? 'open' : ''; ?>">
            <div class="z-nav-header" onclick="zToggleNavGroup(this)">
                <div style="display:flex;align-items:center;gap:10px;"><i data-lucide="database"></i> Data Master</div>
                <i data-lucide="chevron-right" class="chev"></i>
            </div>
            <div class="z-subnav">
                <a href="<?php echo $sp; ?>/institusi" class="z-subnav-item <?php echo strpos($_s_uri, $sp . '/institusi') !== false ? 'active' : ''; ?>">Profil Institusi</a>
                <a href="<?php echo $sp; ?>/kop-surat" class="z-subnav-item <?php echo strpos($_s_uri, $sp . '/kop-surat') !== false ? 'active' : ''; ?>">Pengaturan Kop Surat</a>
                <a href="<?php echo $sp; ?>/mapel" class="z-subnav-item <?php echo strpos($_s_uri, $sp . '/mapel') !== false ? 'active' : ''; ?>">Mata Pelajaran</a>
                <a href="<?php echo $sp; ?>/kelas" class="z-subnav-item <?php echo strpos($_s_uri, $sp . '/kelas') !== false ? 'active' : ''; ?>">Data Kelas</a>
                <a href="<?php echo $sp; ?>/ekstra" class="z-subnav-item <?php echo strpos($_s_uri, $sp . '/ekstra') !== false ? 'active' : ''; ?>">Data Ekstrakurikuler</a>
            </div>
        </div>
        <?php endif; ?>

        <?php if(can_access('manajemen_guru', $role_id, $perms)): ?>
        <div class="z-nav-group <?php echo strpos($_s_uri, 'guru') !== false ? 'open' : ''; ?>">
            <div class="z-nav-header" onclick="zToggleNavGroup(this)">
                <div style="display:flex;align-items:center;gap:10px;"><i data-lucide="users"></i> Manajemen Guru</div>
                <i data-lucide="chevron-right" class="chev"></i>
            </div>
            <div class="z-subnav">
                <a href="<?php echo $sp; ?>/guru" class="z-subnav-item <?php echo ($_s_uri == $sp . base_url('guru')) ? 'active' : ''; ?>">Data Guru</a>
                <a href="<?php echo $sp; ?>/guru/tugas" class="z-subnav-item <?php echo strpos($_s_uri, 'guru/tugas') !== false ? 'active' : ''; ?>">Tugas Tambahan</a>
                <a href="<?php echo $sp; ?>/guru/mengajar" class="z-subnav-item <?php echo strpos($_s_uri, 'mengajar') !== false ? 'active' : ''; ?>">Penugasan Mengajar</a>
                <a href="<?php echo $sp; ?>/guru-wali" class="z-subnav-item <?php echo strpos($_s_uri, 'guru-wali') !== false ? 'active' : ''; ?>">Guru Wali (Anak Asuh)</a>
                <a href="<?php echo $sp; ?>/jurnal-global" class="z-subnav-item <?php echo strpos($_s_uri, 'jurnal-global') !== false ? 'active' : ''; ?>">Jurnal Global Guru</a>
            </div>
        </div>
        <?php endif; ?>

        <?php if(can_access('manajemen_siswa', $role_id, $perms)): ?>
        <div class="z-nav-group <?php echo strpos($_s_uri, 'siswa') !== false ? 'open' : ''; ?>">
            <div class="z-nav-header" onclick="zToggleNavGroup(this)">
                <div style="display:flex;align-items:center;gap:10px;"><i data-lucide="user-square"></i> Manajemen Siswa</div>
                <i data-lucide="chevron-right" class="chev"></i>
            </div>
            <div class="z-subnav">
                <a href="<?php echo $sp; ?>/siswa" class="z-subnav-item <?php echo ($_s_uri == $sp . base_url('siswa')) ? 'active' : ''; ?>">Data Siswa</a>
                <a href="<?php echo $sp; ?>/siswa/naik-kelas" class="z-subnav-item <?php echo strpos($_s_uri, 'naik-kelas') !== false ? 'active' : ''; ?>">Naik Kelas</a>
                <a href="<?php echo $sp; ?>/siswa/alumni" class="z-subnav-item <?php echo strpos($_s_uri, 'alumni') !== false ? 'active' : ''; ?>">Data Alumni</a>
            </div>
        </div>
        <?php endif; ?>

        <?php if(can_access('manajemen_nilai', $role_id, $perms) || can_access('input_nilai_guru', $role_id, $perms)): ?>
        <div class="z-nav-group <?php echo (strpos($_s_uri, 'nilai') !== false || strpos($_s_uri, 'raport') !== false || strpos($_s_uri, 'jadwal') !== false || strpos($_s_uri, 'kalender') !== false) ? 'open' : ''; ?>">
            <div class="z-nav-header" onclick="zToggleNavGroup(this)">
                <div style="display:flex;align-items:center;gap:10px;"><i data-lucide="book-open-check"></i> Akademik & Nilai</div>
                <i data-lucide="chevron-right" class="chev"></i>
            </div>
            <div class="z-subnav">
                <?php if(can_access('manajemen_nilai', $role_id, $perms)): ?>
                <a href="<?php echo $sp; ?>/nilai" class="z-subnav-item <?php echo ($_s_uri == $sp . '/nilai') ? 'active' : ''; ?>">Data Nilai (Admin)</a>
                <a href="<?php echo $sp; ?>/jadwal" class="z-subnav-item <?php echo strpos($_s_uri, $sp . '/jadwal') !== false ? 'active' : ''; ?>">Jadwal Pelajaran</a>
                <a href="<?php echo $sp; ?>/kalender" class="z-subnav-item <?php echo strpos($_s_uri, $sp . '/kalender') !== false ? 'active' : ''; ?>">Kalender Akademik</a>
                <a href="<?php echo $sp; ?>/tugas-daring" class="z-subnav-item <?php echo strpos($_s_uri, $sp . '/tugas-daring') !== false ? 'active' : ''; ?>">Cetak Tugas Daring</a>
                <?php endif; ?>
                <?php if(can_access('input_nilai_guru', $role_id, $perms)): ?>
                <a href="<?php echo $sp; ?>/nilai/input" class="z-subnav-item <?php echo strpos($_s_uri, '/nilai/input') !== false ? 'active' : ''; ?>">Input Nilai (Guru)</a>
                <?php endif; ?>
                <?php if(can_access('manajemen_nilai', $role_id, $perms) || can_access('input_nilai_guru', $role_id, $perms)): ?>
                <a href="<?php echo $sp; ?>/nilai/template" class="z-subnav-item <?php echo strpos($_s_uri, '/nilai/template') !== false ? 'active' : ''; ?>">Template Nilai</a>
                <?php endif; ?>
                <?php if(can_access('raport', $role_id, $perms)): ?>
                <a href="<?php echo $sp; ?>/raport" class="z-subnav-item <?php echo strpos($_s_uri, 'raport') !== false ? 'active' : ''; ?>">Raport</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if(can_access('manajemen_siswa', $role_id, $perms)): ?>
        <div class="z-nav-group <?php echo (strpos($_s_uri, 'lain-lain') !== false) ? 'open' : ''; ?>">
            <div class="z-nav-header" onclick="zToggleNavGroup(this)">
                <div style="display:flex;align-items:center;gap:10px;"><i data-lucide="folder-open"></i> Lain-lain</div>
                <i data-lucide="chevron-right" class="chev"></i>
            </div>
            <div class="z-subnav">
                <a href="<?php echo $sp; ?>/lain-lain/rapat" class="z-subnav-item <?php echo strpos($_s_uri, 'lain-lain/rapat') !== false ? 'active' : ''; ?>">Daftar Hadir Rapat</a>
                <a href="<?php echo $sp; ?>/lain-lain/broadcast" class="z-subnav-item <?php echo strpos($_s_uri, 'lain-lain/broadcast') !== false ? 'active' : ''; ?>">Broadcast Notifikasi</a>
            </div>
        </div>
        <?php endif; ?>

    </nav>
    </div>

</aside>

<script>
function zToggleNavGroup(element) {
    let parent = element.parentElement;
    let wasOpen = parent.classList.contains('open');
    
    // Close all nav groups first
    document.querySelectorAll('.z-nav-group').forEach(group => {
        group.classList.remove('open');
    });

    // If the clicked one wasn't open, open it
    if (!wasOpen) {
        parent.classList.add('open');
    }
}
</script>

