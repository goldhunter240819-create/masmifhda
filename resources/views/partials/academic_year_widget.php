<?php
// /resources/views/partials/academic_year_widget.php
$currYear = \App\Core\AcademicYear::current();
$allYears = \App\Core\AcademicYear::getAll();
?>
<div style="margin-bottom: 1rem; padding: 0.75rem; background: rgba(0,0,0,0.2); border-radius: 10px; border: 1px solid rgba(255,255,255,0.12);">
    <label style="display: block; font-size: 0.68rem; color: rgba(255,255,255,0.7); font-weight: 700; text-transform: uppercase; margin-bottom: 0.4rem; letter-spacing: 0.5px;">
        <i data-lucide="clock" style="width:12px; height:12px; display:inline-block; margin-right:4px;"></i> Tahun Ajaran
    </label>
    <div style="position:relative; width: 100%;">
        <select onchange="window.location.href='<?php echo base_url('switch-academic-year.php?id='); ?>' + this.value" style="width: 100%; background: #064e3b; color: #a7f3d0; border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; padding: 7px 26px 7px 10px; appearance: none; font-weight: 700; font-size: 0.78rem; outline: none; cursor: pointer; transition: 0.2s;">
            <?php foreach($allYears as $y): ?>
                <?php $smtStr = ($y['semester'] == 2) ? 'Genap' : 'Ganjil'; ?>
                <option value="<?= $y['id'] ?>" <?= $currYear['id'] == $y['id'] ? 'selected' : '' ?> style="background:#064e3b; color:#fff;">
                    <?= htmlspecialchars($y['name']) ?> (<?= $smtStr ?>)
                </option>
            <?php endforeach; ?>
            <option value="0" style="background:#064e3b; color:#fff;">Default Aktif</option>
        </select>
        <i data-lucide="chevron-down" style="width:14px; color:rgba(255,255,255,0.6); position:absolute; right:8px; top:50%; transform:translateY(-50%); pointer-events:none;"></i>
    </div>
</div>
