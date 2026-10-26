<style>
    /* â”€â”€â”€ Hero Siswa â”€â”€â”€ */
    .welcome-hero-siswa {
        background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
        margin: 2rem; border-radius: 32px; padding: 3rem; position: relative;
        overflow: hidden; color: white; box-shadow: 0 10px 30px rgba(37,99,235,0.2);
    }
    .welcome-hero-siswa h1 { font-size: 2rem; font-weight: 900; margin: 0 0 0.5rem; letter-spacing: -0.5px; }
    .welcome-hero-siswa p { font-size: 1rem; opacity: 0.85; max-width: 500px; margin: 0; }
    .welcome-hero-siswa .hero-bg-icon { position: absolute; right: -20px; bottom: -20px; font-size: 120px; opacity: 0.1; transform: rotate(-15deg); }

    .s-blue-bg { background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%) !important; }

    .schedule-empty { text-align: center; padding: 4rem 0; color: #94a3b8; }
    .se-icon { font-size: 3rem; opacity: 0.2; margin-bottom: 1rem; }

    .summary-box { background: #f8fafc; padding: 1.5rem; border-radius: 20px; border: 1.5px solid #f1f5f9; }
    .summary-box p { font-size: 0.9rem; color: #64748b; margin-bottom: 1.5rem; line-height: 1.6; }
</style>
<?php
// data variables: $nama, $initial, $title
?>
<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="z-main">
        <header class="z-header">
            <div class="z-header-left">
                <button class="z-hamburger" id="zHamburger" onclick="zToggleSidebar()">
                    <i data-lucide="menu"></i>
                </button>
                <div class="header-breadcrumb">
                    <span class="parent">SIAKAD</span>
                    <i data-lucide="chevron-right" class="sep"></i>
                    <span class="current">Student Portal</span>
                </div>
            </div>
            <div class="z-header-right">
                <div class="z-header-user">
                    <div class="z-avatar-premium s-blue-bg"><?php echo $initial; ?></div>
                    <div class="z-user-info">
                        <div class="name"><?php echo $nama; ?></div>
                        <div class="role">Peserta Didik</div>
                    </div>
                </div>
            </div>
        </header>

        <div class="z-scroll">
            <div class="welcome-hero-siswa">
                <div class="hero-content">
                    <h1>Halo, <?php echo $nama; ?>! ðŸ“š</h1>
                    <p>Selamat datang di portal akademik digitalmu. Pantau nilai, absensi, dan jadwal pelajaranmu di sini.</p>
                </div>
                <div class="hero-bg-icon"><i data-lucide="award"></i></div>
            </div>

            <div class="z-dashboard-grid">
                <div class="z-panel-modern">
                    <div class="panel-header">
                        <h3><i data-lucide="calendar"></i> Jadwal Pelajaran Hari Ini</h3>
                    </div>
                    <div class="schedule-empty">
                        <div class="se-icon"><i data-lucide="coffee"></i></div>
                        <p>Tidak ada jadwal pelajaran untuk hari ini. Waktunya istirahat!</p>
                    </div>
                </div>

                <div class="z-panel-modern">
                    <div class="panel-header">
                        <h3><i data-lucide="award"></i> Ringkasan Nilai</h3>
                    </div>
                    <div class="summary-box">
                        <p>Nilai akhir semestermu dapat dilihat melalui menu <strong>E-Raport</strong> di sidebar.</p>
                        <a href="<?php echo base_url('siakad/raport'); ?>" class="z-btn z-btn-primary z-btn-full">Buka E-Raport <i data-lucide="arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script>lucide.createIcons();</script>
