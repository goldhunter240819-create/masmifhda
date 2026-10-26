<style>
    /* â”€â”€â”€ Hero Guru â”€â”€â”€ */
    .welcome-hero-guru {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        margin: 2rem; border-radius: 32px; padding: 3rem; position: relative;
        overflow: hidden; color: white; box-shadow: 0 10px 30px rgba(16,185,129,0.2);
    }
    .welcome-hero-guru h1 { font-size: 2rem; font-weight: 900; margin: 0 0 0.5rem; letter-spacing: -0.5px; }
    .welcome-hero-guru p { font-size: 1rem; opacity: 0.85; max-width: 500px; margin: 0; }
    .welcome-hero-guru .hero-bg-icon { position: absolute; right: -20px; bottom: -20px; font-size: 120px; opacity: 0.1; transform: rotate(-15deg); }

    /* â”€â”€â”€ Menu Grid â”€â”€â”€ */
    .menu-grid { display: flex; flex-direction: column; gap: 1rem; }
    .menu-item-lg {
        text-decoration: none; display: flex; align-items: center; gap: 1.5rem;
        padding: 1.5rem; border-radius: 20px; border: 1.5px solid #f1f5f9; transition: 0.2s;
    }
    .m-icon { width: 54px; height: 54px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
    .m-text { flex: 1; }
    .m-text strong { display: block; font-size: 1.1rem; color: #1e293b; margin-bottom: 2px; }
    .m-text span { font-size: 0.85rem; color: #94a3b8; }
    .m-arrow { color: #cbd5e1; }

    .green .m-icon { background: #f0fdf4; color: #0ea5e9; }
    .blue .m-icon { background: #eff6ff; color: #3b82f6; }

    .menu-item-lg:hover { border-color: #0ea5e9; background: #fafffe; transform: scale(1.01); }
    .menu-item-lg:hover .m-arrow { color: #0ea5e9; transform: translateX(5px); }

    .info-empty { text-align: center; padding: 3rem 0; color: #94a3b8; }
    .ie-icon { font-size: 2.5rem; opacity: 0.2; margin-bottom: 1rem; }
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
                    <span class="current">Guru Panel</span>
                </div>
            </div>
            <div class="z-header-right">
                <div class="z-header-user">
                    <div class="z-avatar-premium"><?php echo $initial; ?></div>
                    <div class="z-user-info">
                        <div class="name"><?php echo $nama; ?></div>
                        <div class="role">Tenaga Pendidik</div>
                    </div>
                </div>
            </div>
        </header>

        <div class="z-scroll">
            <div class="welcome-hero-guru">
                <div class="hero-content">
                    <h1>Semangat Mengajar, <?php echo $nama; ?>! ðŸŽ</h1>
                    <p>Pantau perkembangan siswa, kelola nilai, dan atur jadwal mengajar Anda dengan lebih mudah dan terorganisir.</p>
                </div>
                <div class="hero-bg-icon"><i data-lucide="book-open"></i></div>
            </div>

            <div class="z-dashboard-grid">
                <div class="z-panel-modern">
                    <div class="panel-header">
                        <h3><i data-lucide="layout"></i> Menu Utama Guru</h3>
                    </div>
                    <div class="menu-grid">
                        <a href="<?php echo base_url('siakad/nilai/input'); ?>" class="menu-item-lg green">
                            <div class="m-icon"><i data-lucide="edit-3"></i></div>
                            <div class="m-text">
                                <strong>Input Nilai Akademik</strong>
                                <span>Kelola tugas, UTS, dan UAS siswa</span>
                            </div>
                            <i data-lucide="chevron-right" class="m-arrow"></i>
                        </a>
                        <a href="<?php echo base_url('siakad/guru/mengajar'); ?>" class="menu-item-lg blue">
                            <div class="m-icon"><i data-lucide="calendar"></i></div>
                            <div class="m-text">
                                <strong>Jadwal Mengajar</strong>
                                <span>Lihat waktu dan kelas mengajar</span>
                            </div>
                            <i data-lucide="chevron-right" class="m-arrow"></i>
                        </a>
                    </div>
                </div>

                <div class="z-panel-modern">
                    <div class="panel-header">
                        <h3><i data-lucide="info"></i> Informasi Akademik</h3>
                    </div>
                    <div class="info-empty">
                        <div class="ie-icon"><i data-lucide="bell"></i></div>
                        <p>Belum ada pengumuman baru untuk saat ini.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script>lucide.createIcons();</script>

