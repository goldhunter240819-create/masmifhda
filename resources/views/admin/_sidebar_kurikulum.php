<?php
/* Universal Admin Sidebar - Zamrud Design System */
$ap = isset($active_page) ? $active_page : '';

$sub_menus = array(
    'kurikulum' => array(
        array('icon'=>'layout-dashboard', 'label'=>'Dashboard', 'url'=>base_url('kurikulum'), 'page'=>'overview'),
        array(
            'icon'=>'activity',
            'label'=>'Akademik & Monitoring',
            'page'=>'akademik',
            'subpages'=>array('kurikulum_jadwal','kurikulum_monitoring_jurnal','kurikulum_monitoring_nilai','kurikulum_syarat_pas','kurikulum_kkm','kurikulum_rekap_ketuntasan','monitoring_jadwal','monitoring_jurnal','pengaturan_kkm','rekap_ketuntasan'),
            'dropdown'=>array(
                array('icon'=>'calendar', 'label'=>'Monitoring Jadwal', 'url'=>base_url('kurikulum/jadwal-pelajaran'), 'page'=>'kurikulum_jadwal'),
                array('icon'=>'book-open', 'label'=>'Monitoring Jurnal', 'url'=>base_url('kurikulum/monitoring-jurnal'), 'page'=>'kurikulum_monitoring_jurnal'),
                array('icon'=>'bar-chart-2', 'label'=>'Monitoring Nilai', 'url'=>base_url('kurikulum/monitoring-nilai'), 'page'=>'kurikulum_monitoring_nilai'),
                array('icon'=>'shield-check', 'label'=>'Kelayakan Ikut PAS', 'url'=>base_url('kurikulum/syarat-pas'), 'page'=>'kurikulum_syarat_pas'),
                array('icon'=>'settings-2', 'label'=>'Pengaturan KKM', 'url'=>base_url('kurikulum/monitoring-kkm'), 'page'=>'kurikulum_kkm'),
                array('icon'=>'check-square', 'label'=>'Rekap Ketuntasan', 'url'=>base_url('kurikulum/rekap-ketuntasan'), 'page'=>'kurikulum_rekap_ketuntasan'),
            )
        ),
        array(
            'icon'=>'clipboard-list',
            'label'=>'Supervisi & Evaluasi',
            'page'=>'supervisi',
            'subpages'=>array('supervisi_dashboard','program_supervisi','supervisi_tim','supervisi_administrasi','supervisi_kelas','supervisi_tindak_lanjut','supervisi_instrumen','supervisi_laporan_hasil','supervisi_laporan_tindak_lanjut','kurikulum_supervisi_administrasi','kurikulum_supervisi_kelas','kurikulum_supervisi_penilaian'),
            'dropdown'=>array(
                array('icon'=>'layout-dashboard','label'=>'Dashboard Supervisi', 'url'=>base_url('kurikulum/supervisi'), 'page'=>'supervisi_dashboard'),
                array('icon'=>'calendar-check','label'=>'Program Supervisi', 'url'=>base_url('kurikulum/supervisi/program'), 'page'=>'program_supervisi'),
                array('icon'=>'user-check','label'=>'Tim Supervisi & SK', 'url'=>base_url('kurikulum/supervisi/tim'), 'page'=>'supervisi_tim'),
                array('icon'=>'file-text','label'=>'Supervisi Administrasi', 'url'=>base_url('kurikulum/supervisi/administrasi'), 'page'=>'supervisi_administrasi'),
                array('icon'=>'users','label'=>'Supervisi Kelas', 'url'=>base_url('kurikulum/supervisi/kelas'), 'page'=>'supervisi_kelas'),
                array('icon'=>'arrow-right-circle','label'=>'Tindak Lanjut Supervisi', 'url'=>base_url('kurikulum/supervisi/tindak-lanjut'), 'page'=>'supervisi_tindak_lanjut'),
                array('icon'=>'clipboard-list','label'=>'Instrumen Supervisi', 'url'=>base_url('kurikulum/supervisi/instrumen'), 'page'=>'supervisi_instrumen'),
                array('icon'=>'file-bar-chart-2','label'=>'Laporan Hasil Supervisi', 'url'=>base_url('kurikulum/supervisi/laporan-hasil'), 'page'=>'supervisi_laporan_hasil'),
                array('icon'=>'file-check','label'=>'Laporan Hasil Tindak Lanjut', 'url'=>base_url('kurikulum/supervisi/laporan-tindak-lanjut'), 'page'=>'supervisi_laporan_tindak_lanjut'),
            )
        ),
        array(
            'icon'=>'folder',
            'label'=>'Manajemen Berkas',
            'page'=>'berkas',
            'subpages'=>array('berkas_pribadi','berkas_perangkat'),
            'dropdown'=>array(
                array('icon'=>'user', 'label'=>'Berkas Pribadi', 'url'=>base_url('kurikulum/berkas/pribadi'), 'page'=>'berkas_pribadi'),
                array('icon'=>'book', 'label'=>'Perangkat Pembelajaran', 'url'=>base_url('kurikulum/berkas/perangkat'), 'page'=>'berkas_perangkat'),
            )
        ),
    )
);
?>
<div class="z-sidebar" id="zSidebar">
    <div class="z-sidebar-logo">
        <div class="logo-icon" style="background: transparent; padding: 0;">
            <img src="<?php echo \App\Core\Branding::getInstitusiLogo(); ?>" alt="Logo" style="width: 32px; height: 32px; object-fit: contain; border-radius: 6px;">
        </div>
        <div>
            <div class="logo-text">MASMIFHDA SYSTEM</div>
            <div class="logo-sub">Platform Digital</div>
        </div>
    </div>

    <div class="z-sidebar-scroll">
        <!-- Academic Year Widget -->
        <?php include __DIR__ . '/../partials/academic_year_widget.php'; ?>

        <div class="z-nav-cat">WAKA KURIKULUM</div>
        
        <?php foreach($sub_menus['kurikulum'] as $item): ?>
            <?php if(isset($item['dropdown'])): ?>
                <?php $isOpen = ($ap===$item['page'] || (isset($item['subpages']) && in_array($ap, $item['subpages']))); ?>
                <div class="z-nav-dropdown <?php echo $isOpen ? 'open' : ''; ?>">
                    <div class="z-nav-dropdown-toggle <?php echo $isOpen ? 'active' : ''; ?>" onclick="var p = this.parentElement; document.querySelectorAll('.z-nav-dropdown').forEach(function(d){ if(d !== p) d.classList.remove('open'); }); p.classList.toggle('open');">
                        <div class="dt-left">
                            <i data-lucide="<?php echo $item['icon']; ?>"></i>
                            <?php echo $item['label']; ?>
                        </div>
                        <i data-lucide="chevron-down" class="dt-icon"></i>
                    </div>
                    <div class="z-nav-dropdown-menu">
                        <?php foreach($item['dropdown'] as $sub): ?>
                        <a href="<?php echo $sub['url']; ?>" class="z-nav-item <?php echo $ap===$sub['page']?'active':''; ?>">
                            <i data-lucide="<?php echo $sub['icon'] ?? 'circle'; ?>" style="width: 16px; height: 16px; margin-right: 8px;"></i>
                            <?php echo $sub['label']; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?php echo $item['url']; ?>" class="z-nav-item <?php echo ($ap == $item['page']) ? 'active' : ''; ?>">
                    <i data-lucide="<?php echo $item['icon']; ?>"></i> <span><?php echo $item['label']; ?></span>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- BACK TO PORTAL -->
    <div style="padding: 1rem; border-top: 1px solid rgba(255,255,255,0.05);">
        <a href="<?php echo base_url('portal'); ?>" class="z-nav-item" style="background:rgba(255,255,255,0.1); color:#cbd5e1; justify-content: center; gap: 8px;">
            <i data-lucide="arrow-left"></i> <span>Ke Portal Hub</span>
        </a>
    </div>
</div>
