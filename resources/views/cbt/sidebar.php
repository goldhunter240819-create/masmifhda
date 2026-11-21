<?php
$active_menu = $active_menu ?? 'dashboard';

$nama = $_SESSION['nama'] ?? ($_SESSION['username'] ?? 'Pengguna');
$initial = strtoupper(substr($nama, 0, 2));

$roleId = isset($_SESSION['role_id']) ? (int)$_SESSION['role_id'] : 0;
$roleLabel = 'Guru / Pengawas';
if ($roleId === 1 || $roleId === 99) {
    $roleLabel = 'Administrator';
} elseif (!empty($_SESSION['is_kamad'])) {
    $roleLabel = 'Kepala Madrasah';
}

$logoUrl = \App\Core\Branding::getInstitusiLogo();

// Deteksi status open masing-masing dropdown accordion
$isUjianOpen = in_array($active_menu, ['topik', 'jadwal', 'bank_soal', 'token']);
$isRuangOpen = in_array($active_menu, ['ruang', 'denah']);
$isAbsenOpen = in_array($active_menu, ['absensi_cbt', 'absensi_rekap']);
$isHasilOpen = in_array($active_menu, ['hasil', 'analisis']);
?>
<aside class="z-sidebar" id="zSidebar">
    <!-- Brand -->
    <div class="z-sidebar-logo">
        <div class="logo-icon" style="background: transparent; padding: 0;">
            <img src="<?php echo $logoUrl; ?>" alt="Logo" style="width: 32px; height: 32px; object-fit: contain; border-radius: 6px;">
        </div>
        <div>
            <div class="logo-text">MASMIFHDA</div>
            <div class="logo-sub" style="color:#0ea5e9;">CBT Online</div>
        </div>
        <button class="z-close-btn" onclick="zToggleSidebar()" style="display:none;background:none;border:none;color:white;position:absolute;right:1rem;">
            <i data-lucide="x"></i>
        </button>
    </div>

    <!-- Nav List -->
    <div class="z-sidebar-scroll">
        <nav class="z-nav">
            <div style="padding: 0.75rem 1rem 0 1rem;">
                <?php 
                $widgetPath = __DIR__ . '/../partials/academic_year_widget.php';
                if (file_exists($widgetPath)) {
                    include $widgetPath;
                }
                ?>
            </div>

            <div class="z-nav-label">Menu Ujian</div>

            <!-- 1. Dashboard -->
            <a href="<?php echo base_url('cbt/dashboard'); ?>" class="z-nav-item <?php echo $active_menu === 'dashboard' ? 'active' : ''; ?>">
                <i data-lucide="layout-dashboard"></i>
                <span>Dashboard</span>
            </a>

            <!-- 2. Dropdown: Manajemen Ujian -->
            <div class="z-nav-dropdown <?php echo $isUjianOpen ? 'open' : ''; ?>">
                <div class="z-nav-dropdown-toggle <?php echo $isUjianOpen ? 'active' : ''; ?>" onclick="toggleCbtDropdown(this)">
                    <div class="dt-left">
                        <i data-lucide="layers"></i>
                        <span>Manajemen Ujian</span>
                    </div>
                    <i data-lucide="chevron-down" class="dt-icon"></i>
                </div>
                <div class="z-nav-dropdown-menu">
                    <a href="<?php echo base_url('cbt/topik'); ?>" class="z-nav-item <?php echo $active_menu === 'topik' ? 'active' : ''; ?>">
                        <i data-lucide="bookmark"></i>
                        <span>Topik Ujian</span>
                    </a>
                    <a href="<?php echo base_url('cbt/jadwal'); ?>" class="z-nav-item <?php echo $active_menu === 'jadwal' ? 'active' : ''; ?>">
                        <i data-lucide="calendar"></i>
                        <span>Jadwal Ujian</span>
                    </a>
                    <a href="<?php echo base_url('bank_soal'); ?>" class="z-nav-item <?php echo $active_menu === 'bank_soal' ? 'active' : ''; ?>">
                        <i data-lucide="database"></i>
                        <span>Bank Soal</span>
                    </a>
                    <a href="<?php echo base_url('cbt/token'); ?>" class="z-nav-item <?php echo $active_menu === 'token' ? 'active' : ''; ?>">
                        <i data-lucide="key-round"></i>
                        <span>Token Ujian</span>
                    </a>
                </div>
            </div>

            <!-- 3. Dropdown: Ruang & Denah Meja -->
            <div class="z-nav-dropdown <?php echo $isRuangOpen ? 'open' : ''; ?>">
                <div class="z-nav-dropdown-toggle <?php echo $isRuangOpen ? 'active' : ''; ?>" onclick="toggleCbtDropdown(this)">
                    <div class="dt-left">
                        <i data-lucide="door-open"></i>
                        <span>Ruang & Tempat Duduk</span>
                    </div>
                    <i data-lucide="chevron-down" class="dt-icon"></i>
                </div>
                <div class="z-nav-dropdown-menu">
                    <a href="<?php echo base_url('cbt/ruang'); ?>" class="z-nav-item <?php echo $active_menu === 'ruang' ? 'active' : ''; ?>">
                        <i data-lucide="layout-grid"></i>
                        <span>Kelola Ruang</span>
                    </a>
                    <a href="<?php echo base_url('cbt/denah'); ?>" class="z-nav-item <?php echo $active_menu === 'denah' ? 'active' : ''; ?>">
                        <i data-lucide="grid"></i>
                        <span>Denah Tempat Duduk</span>
                    </a>
                </div>
            </div>

            <!-- 4. Dropdown: Absensi & Kehadiran CBT -->
            <div class="z-nav-dropdown <?php echo $isAbsenOpen ? 'open' : ''; ?>">
                <div class="z-nav-dropdown-toggle <?php echo $isAbsenOpen ? 'active' : ''; ?>" onclick="toggleCbtDropdown(this)">
                    <div class="dt-left">
                        <i data-lucide="qr-code"></i>
                        <span>Absensi CBT</span>
                    </div>
                    <i data-lucide="chevron-down" class="dt-icon"></i>
                </div>
                <div class="z-nav-dropdown-menu">
                    <a href="<?php echo base_url('cbt/scan'); ?>" class="z-nav-item <?php echo $active_menu === 'absensi_cbt' ? 'active' : ''; ?>">
                        <i data-lucide="scan"></i>
                        <span>Scan 1 Pintu</span>
                    </a>
                    <a href="<?php echo base_url('cbt/absensi'); ?>" class="z-nav-item <?php echo $active_menu === 'absensi_rekap' ? 'active' : ''; ?>">
                        <i data-lucide="clipboard-check"></i>
                        <span>Rekap Absensi</span>
                    </a>
                </div>
            </div>

            <!-- 5. Peserta Kelas Jauh -->
            <a href="<?php echo base_url('cbt/peserta_khusus'); ?>" class="z-nav-item <?php echo $active_menu === 'peserta_khusus' ? 'active' : ''; ?>">
                <i data-lucide="users-2"></i>
                <span>Peserta Kelas Jauh</span>
            </a>

            <?php if (\App\Controllers\CbtController::isStandalone()): ?>
            <!-- 5b. Peserta Siswa SIAKAD (Hanya Tampil di Server CBT Offline) -->
            <a href="<?php echo base_url('cbt/peserta_siakad'); ?>" class="z-nav-item <?php echo $active_menu === 'peserta_siakad' ? 'active' : ''; ?>">
                <i data-lucide="graduation-cap"></i>
                <span>Peserta SIAKAD</span>
            </a>
            <?php endif; ?>

            <!-- 6. Dropdown: Hasil & Evaluasi -->
            <div class="z-nav-dropdown <?php echo $isHasilOpen ? 'open' : ''; ?>">
                <div class="z-nav-dropdown-toggle <?php echo $isHasilOpen ? 'active' : ''; ?>" onclick="toggleCbtDropdown(this)">
                    <div class="dt-left">
                        <i data-lucide="award"></i>
                        <span>Hasil & Evaluasi</span>
                    </div>
                    <i data-lucide="chevron-down" class="dt-icon"></i>
                </div>
                <div class="z-nav-dropdown-menu">
                    <a href="<?php echo base_url('cbt/hasil'); ?>" class="z-nav-item <?php echo $active_menu === 'hasil' ? 'active' : ''; ?>">
                        <i data-lucide="file-spreadsheet"></i>
                        <span>Rekap Hasil Ujian</span>
                    </a>
                    <a href="<?php echo base_url('cbt/analisis'); ?>" class="z-nav-item <?php echo $active_menu === 'analisis' ? 'active' : ''; ?>">
                        <i data-lucide="bar-chart-2"></i>
                        <span>Analisis Butir Soal</span>
                    </a>
                </div>
            </div>

            <!-- 7. Pengaturan CBT & Keamanan -->
            <a href="<?php echo base_url('cbt/pengaturan'); ?>" class="z-nav-item <?php echo $active_menu === 'pengaturan' ? 'active' : ''; ?>">
                <i data-lucide="shield-check"></i>
                <span>Pengaturan CBT</span>
            </a>

            <!-- 8. Sinkronisasi CBT Semi-Online -->
            <a href="<?php echo base_url('cbt/semi-online'); ?>" class="z-nav-item <?php echo $active_menu === 'semi_online' ? 'active' : ''; ?>">
                <i data-lucide="refresh-cw"></i>
                <span>Sinkron Semi-Online</span>
            </a>

            <div class="z-nav-label" style="margin-top: 1rem;">Integrasi</div>

            <!-- Portal Utama -->
            <a href="<?php echo base_url('portal'); ?>" class="z-nav-item">
                <i data-lucide="home"></i>
                <span>Portal Utama</span>
            </a>
        </nav>
    </div>
</aside>

<script>
function toggleCbtDropdown(toggleEl) {
    const parent = toggleEl.parentElement;
    const isAlreadyOpen = parent.classList.contains('open');
    
    // Opsional: Accordion behavior - jika ingin satu per satu terbuka
    // document.querySelectorAll('.z-nav-dropdown').forEach(d => { if(d !== parent) d.classList.remove('open'); });

    parent.classList.toggle('open');
}
</script>

<style>
/* Styling Tambahan untuk Dropdown Sidebar CBT */
.z-nav-dropdown {
    display: flex;
    flex-direction: column;
    margin-bottom: 2px;
}
.z-nav-dropdown-toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.65rem 1rem;
    color: rgba(255, 255, 255, 0.72);
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.2s ease;
    cursor: pointer;
    border-radius: 10px;
    margin: 1px 0.5rem;
}
.z-nav-dropdown-toggle:hover {
    background: rgba(255, 255, 255, 0.08);
    color: #ffffff;
}
.z-nav-dropdown-toggle.active {
    background: rgba(14, 165, 233, 0.15);
    color: #38bdf8;
}
.z-nav-dropdown-toggle .dt-left {
    display: flex;
    align-items: center;
    gap: 10px;
}
.z-nav-dropdown-toggle .dt-left i,
.z-nav-dropdown-toggle .dt-left svg {
    width: 17px !important;
    height: 17px !important;
    opacity: 0.85;
}
.z-nav-dropdown-toggle .dt-icon {
    width: 14px !important;
    height: 14px !important;
    transition: transform 0.25s ease;
    opacity: 0.6;
}
.z-nav-dropdown.open .dt-icon {
    transform: rotate(180deg);
    opacity: 1;
}
.z-nav-dropdown-menu {
    display: none;
    flex-direction: column;
    padding-left: 1.25rem;
    margin: 2px 0 6px 0;
    border-left: 2px solid rgba(255, 255, 255, 0.1);
    margin-left: 1.25rem;
}
.z-nav-dropdown.open .z-nav-dropdown-menu {
    display: flex;
}
.z-nav-dropdown-menu .z-nav-item {
    padding: 0.45rem 0.85rem;
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.65);
    margin: 1px 0;
}
.z-nav-dropdown-menu .z-nav-item:hover {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.08);
}
.z-nav-dropdown-menu .z-nav-item.active {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
    color: #ffffff !important;
    font-weight: 700;
}
</style>
