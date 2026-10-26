<?php
/* E-BK Sidebar - Zamrud Design System */
$ap = isset($active_page) ? $active_page : '';

$bk_menus = array(
    array('icon'=>'layout-dashboard', 'label'=>'Dashboard E-BK', 'url'=>base_url('bk'), 'page'=>'bk_dashboard'),
    array('icon'=>'tags', 'label'=>'Kategori Pelanggaran', 'url'=>base_url('bk/kategori'), 'page'=>'bk_kategori'),
    array('icon'=>'alert-triangle', 'label'=>'Input Pelanggaran', 'url'=>base_url('bk/pelanggaran'), 'page'=>'bk_pelanggaran')
);
?>
<div class="z-sidebar" id="zSidebar">
    <div class="z-sidebar-logo">
        <div class="logo-icon" style="background: transparent; padding: 0;">
            <img src="<?php echo \App\Core\Branding::getInstitusiLogo(); ?>" alt="Logo" style="width: 32px; height: 32px; object-fit: contain; border-radius: 6px;">
        </div>
        <div>
            <div class="logo-text">MASMIFHDA</div>
            <div class="logo-sub">Platform Digital</div>
        </div>
    </div>

    <div class="z-sidebar-scroll">
        <?php include __DIR__ . '/../partials/academic_year_widget.php'; ?>

        <div class="z-nav-cat">BIMBINGAN KONSELING</div>
        
        <?php foreach($bk_menus as $item): ?>
            <a href="<?php echo $item['url']; ?>" class="z-nav-item <?php echo ($ap===$item['page'])?'active':''; ?>">
                <i data-lucide="<?php echo $item['icon']; ?>"></i>
                <?php echo $item['label']; ?>
            </a>
        <?php endforeach; ?>
    </div>
    
    <div class="z-sidebar-foot">
        <a href="<?php echo base_url(); ?>" class="z-nav-item" style="color: #ef4444; background: rgba(239,68,68,0.1); justify-content: center; border-radius: 8px;">
            <i data-lucide="log-out" style="width: 16px; height: 16px;"></i>
            Keluar Portal
        </a>
    </div>
</div>
