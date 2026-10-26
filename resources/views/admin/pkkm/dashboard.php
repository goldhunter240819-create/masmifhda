<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title = $title_page; include (file_exists(__DIR__ . '/_zamrud_head.php') ? __DIR__ . '/_zamrud_head.php' : __DIR__ . '/../_zamrud_head.php'); ?>
    <style>
        .pkkm-card {
            background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; border: 1px solid #e2e8f0;
        }
        .stat-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;
        }
        .stat-box {
            padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .stat-box blue { background: linear-gradient(135deg, #f3e8ff, #faf5ff); border: 1px solid #e9d5ff; color: #6b21a8; }
        .stat-box.blue { background: linear-gradient(135deg, #eff6ff, #f8fafc); border: 1px solid #bfdbfe; color: #1e3a8a; }
        .stat-box.green { background: linear-gradient(135deg, #f0f9ff, #f8fafc); border: 1px solid #bae6fd; color: #0c4a6e; }
        .stat-box.yellow { background: linear-gradient(135deg, #fefce8, #f8fafc); border: 1px solid #fef08a; color: #713f12; }
        
        .stat-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.6); }
        .stat-value { font-size: 2rem; font-weight: 800; line-height: 1; margin-bottom: 5px; }
        .stat-label { font-size: 0.85rem; font-weight: 600; opacity: 0.8; }
        
        .progress-bar-wrap { width: 100%; height: 24px; background: #e2e8f0; border-radius: 100px; overflow: hidden; margin-top: 10px; }
        .progress-bar { height: 100%; background: linear-gradient(90deg, #a855f7, #d946ef); display: flex; align-items: center; justify-content: flex-end; padding-right: 10px; color: white; font-weight: bold; font-size: 0.8rem; transition: width 1s; }
    </style>
</head>
<body>

<div class="z-wrap">
    <?php include (file_exists(__DIR__ . '/_zamrud_sidebar.php') ? __DIR__ . '/_zamrud_sidebar.php' : __DIR__ . '/../_zamrud_sidebar.php'); ?>
    
    <main class="z-main">
        <?php $page_badge = 'Admin Portal'; include (file_exists(__DIR__ . '/../siakad/_siakad_header.php') ? __DIR__ . '/../siakad/_siakad_header.php' : __DIR__ . '/../../siakad/_siakad_header.php'); ?>

        <div class="z-scroll">
            <div class="z-page-head">
                <h1>Dashboard PKKM</h1>
                <p>Ringkasan kelengkapan instrumen dan penilaian simulasi PKKM.</p>
            </div>
            
            <div class="stat-grid mb-4">
                <div class="stat-box purple">
                    <div class="stat-icon"><i data-lucide="layers" style="width:24px;height:24px;"></i></div>
                    <div>
                        <div class="stat-value"><?php echo $totalKomponen; ?></div>
                        <div class="stat-label">Total Komponen</div>
                    </div>
                </div>
                <div class="stat-box blue">
                    <div class="stat-icon"><i data-lucide="list-checks" style="width:24px;height:24px;"></i></div>
                    <div>
                        <div class="stat-value"><?php echo $totalIndikator; ?></div>
                        <div class="stat-label">Total Indikator</div>
                    </div>
                </div>
                <div class="stat-box green">
                    <div class="stat-icon"><i data-lucide="folder-check" style="width:24px;height:24px;"></i></div>
                    <div>
                        <div class="stat-value"><?php echo $totalBerkas; ?></div>
                        <div class="stat-label">Berkas Diunggah</div>
                    </div>
                </div>
                <div class="stat-box yellow">
                    <div class="stat-icon"><i data-lucide="award" style="width:24px;height:24px;"></i></div>
                    <div>
                        <div class="stat-value"><?php echo $skorTotal; ?> <span style="font-size:1rem; opacity:0.6;">/ <?php echo $skorMax; ?></span></div>
                        <div class="stat-label">Estimasi Skor (<?php echo $kategori; ?>)</div>
                    </div>
                </div>
            </div>

            <div class="pkkm-card">
                <h3 style="margin-top:0; color:#1e293b; font-size:1.2rem;">Progress Skor PKKM (Simulasi)</h3>
                <p style="color:#64748b; font-size:0.9rem; margin-bottom:15px;">Persentase kelengkapan berdasarkan simulasi penilaian (Skor 1-4) yang diinputkan.</p>
                <div class="progress-bar-wrap">
                    <div class="progress-bar" style="width: <?php echo $persentase; ?>%;">
                        <?php echo $persentase; ?>%
                    </div>
                </div>
            </div>

            <div class="pkkm-card">
                <h3 style="margin-top:0; color:#1e293b; font-size:1.2rem;">Status Kelengkapan per Komponen</h3>
                <div class="table-responsive mt-3">
                    <table class="table" style="width:100%; border-collapse:collapse;">
                        <thead style="background:#f8fafc; border-bottom:2px solid #e2e8f0;">
                            <tr>
                                <th width="5%" style="padding:12px; font-weight:600; color:#475569; text-align:center;">No</th>
                                <th style="padding:12px; font-weight:600; color:#475569; text-align:left;">Nama Komponen</th>
                                <th width="15%" style="padding:12px; font-weight:600; color:#475569; text-align:center;">Indikator</th>
                                <th width="15%" style="padding:12px; font-weight:600; color:#475569; text-align:center;">Berkas</th>
                                <th width="15%" style="padding:12px; font-weight:600; color:#475569; text-align:center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($komponenStats as $idx => $k): ?>
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:12px; text-align:center; color:#64748b; font-weight:500;"><?php echo $idx + 1; ?></td>
                                <td style="padding:12px; color:#1e293b; font-weight:600;"><?php echo htmlspecialchars($k['nama_komponen']); ?></td>
                                <td style="padding:12px; text-align:center; color:#475569;"><?php echo $k['jml_indikator']; ?> Item</td>
                                <td style="padding:12px; text-align:center; color:#475569;"><?php echo $k['jml_berkas']; ?> File</td>
                                <td style="padding:12px; text-align:center;">
                                    <?php if ($k['jml_indikator'] > 0 && $k['jml_berkas'] >= $k['jml_indikator']): ?>
                                        <span class="badge" style="background:#e0f2fe; color:#0c4a6e; padding:6px 12px; border-radius:50px; font-weight:600;">Lengkap</span>
                                    <?php else: ?>
                                        <span class="badge" style="background:#fef3c7; color:#92400e; padding:6px 12px; border-radius:50px; font-weight:600;">Proses</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

<script>
function zToggleSidebar(){document.getElementById('zSidebar').classList.toggle('open');document.getElementById('zOverlay').classList.toggle('open');}
lucide.createIcons();
</script>
</body>
</html>
