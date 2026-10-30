<?php
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'User');
$roleName = "Guru / Tendik";
if(!empty($_SESSION['is_kamad'])) $roleName = "Kepala Madrasah";
$initial = strtoupper(substr($nama, 0, 2));

$myApps = [];
if(isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2 && isset($_SESSION['user_id'])) {
    $db_core = \App\Core\Database::connect('core');
    $uid = $_SESSION['user_id'];
    try {
        $res = $db_core->query("SELECT app_code FROM app_admins WHERE user_id = $uid")->fetchAll();
        foreach($res as $r) {
            $myApps[] = $r['app_code'];
        }
    } catch(\PDOException $e) {}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Kas Umum - Bendahara</title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <!-- Font & Icons -->
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . date('YmdH')); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/multi_dashboard.css?v=' . date('YmdH')); ?>">
    <style>
        .finance-card {
            background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;
        }
        .fc-icon {
            width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;
        }
        .fc-info h3 { margin: 0; font-size: 1.5rem; font-weight: 800; color: #0f172a; }
        .fc-info p { margin: 0; font-size: 0.9rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    </style>
</head>
<body>

<div class="siakad-container">

<?php include __DIR__ . '/../sidebar_multi_guru.php'; ?>

    <!-- ── MAIN AREA ── -->
    <div class="z-main">

<?php include __DIR__ . '/../header_multi_guru.php'; ?>

        <!-- Scrollable Content -->
        <div class="z-scroll" style="padding: 0 var(--z-gap) var(--z-gap);">
            
            <div class="elite-page-header" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%); margin-bottom: 20px;">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-badge" style="background: rgba(255,255,255,0.2); color: #fff;">BENDAHARA</div>
                        <h1 style="color: #fff;"><i data-lucide="wallet" style="color: #fff;"></i> Laporan Rekapitulasi Kas</h1>
                        <p style="color: rgba(255,255,255,0.85);">Ringkasan keuangan dari seluruh transaksi masuk dan keluar</p>
                    </div>
                </div>
            </div>

            <form method="GET" action="<?php echo base_url('guru/bendahara/bku'); ?>" style="display:flex; flex-wrap:wrap; gap:10px; align-items:center; background:#fff; padding:15px 20px; border-radius:12px; border:1px solid #e2e8f0; margin-bottom: 20px;">
                <select name="kategori" style="padding:10px; border-radius:8px; border:1px solid #cbd5e1; flex:1; min-width:200px;">
                    <option value="">Semua Kategori (Milik Anda)</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>" <?= ($selected_kat == $cat) ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                    <?php endforeach; ?>
                </select>
                
                <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" style="padding:10px; border-radius:8px; border:1px solid #cbd5e1;">
                <span style="color:#64748b; font-weight:600;">s.d</span>
                <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" style="padding:10px; border-radius:8px; border:1px solid #cbd5e1;">
                
                <button type="submit" class="btn" style="background:#10b981; color:#fff; padding:10px 20px; border:none; border-radius:8px; display:flex; align-items:center; gap:8px; font-weight:600; cursor:pointer;">
                    <i data-lucide="filter" style="width:16px;height:16px;"></i> Filter
                </button>
                <a href="<?php echo base_url('guru/bendahara/bku'); ?>" style="color:#64748b; font-weight:600; text-decoration:none; margin-left:10px;">Reset</a>
            </form>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 20px;">
                <!-- Pemasukan -->
                <div class="finance-card" style="padding: 25px;">
                    <div class="fc-info">
                        <p>TOTAL PEMASUKAN</p>
                        <h3 style="color: #10b981; font-size: 1.8rem; margin-top: 5px;">Rp <?= number_format($totalPemasukan, 0, ',', '.') ?></h3>
                    </div>
                    <div class="fc-icon" style="background: #ecfdf5; color: #10b981;">
                        <i data-lucide="trending-up"></i>
                    </div>
                </div>
                
                <!-- Pengeluaran -->
                <div class="finance-card" style="padding: 25px;">
                    <div class="fc-info">
                        <p>TOTAL PENGELUARAN</p>
                        <h3 style="color: #ef4444; font-size: 1.8rem; margin-top: 5px;">Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></h3>
                    </div>
                    <div class="fc-icon" style="background: #fef2f2; color: #ef4444;">
                        <i data-lucide="trending-down"></i>
                    </div>
                </div>
                
                <!-- Saldo -->
                <div class="finance-card" style="padding: 25px;">
                    <div class="fc-info">
                        <p>TOTAL SALDO KAS</p>
                        <h3 style="color: #3b82f6; font-size: 1.8rem; margin-top: 5px;">Rp <?= number_format($saldo, 0, ',', '.') ?></h3>
                    </div>
                    <div class="fc-icon" style="background: #eff6ff; color: #3b82f6;">
                        <i data-lucide="wallet"></i>
                    </div>
                </div>

                <!-- Lunas -->
                <div class="finance-card" style="padding: 25px;">
                    <div class="fc-info">
                        <p>TAGIHAN LUNAS</p>
                        <h3 style="color: #0f172a; font-size: 1.5rem; margin-top: 5px;"><?= number_format($tagihanLunas['cnt'] ?? 0) ?> <span style="font-size:0.9rem;color:#64748b;">tagihan</span></h3>
                        <div style="font-size:0.85rem; color:#94a3b8; margin-top:5px;">Rp <?= number_format($tagihanLunas['total'] ?? 0, 0, ',', '.') ?></div>
                    </div>
                    <div class="fc-icon" style="background: #ecfdf5; color: #10b981;">
                        <i data-lucide="check-circle"></i>
                    </div>
                </div>
                
                <!-- Menyicil -->
                <div class="finance-card" style="padding: 25px;">
                    <div class="fc-info">
                        <p>TAGIHAN MENYICIL</p>
                        <h3 style="color: #0f172a; font-size: 1.5rem; margin-top: 5px;"><?= number_format($tagihanMenyicil['cnt'] ?? 0) ?> <span style="font-size:0.9rem;color:#64748b;">tagihan</span></h3>
                        <div style="font-size:0.85rem; color:#94a3b8; margin-top:5px;">Rp <?= number_format($tagihanMenyicil['total'] ?? 0, 0, ',', '.') ?></div>
                    </div>
                    <div class="fc-icon" style="background: #eff6ff; color: #3b82f6;">
                        <i data-lucide="loader"></i>
                    </div>
                </div>

                <!-- Belum Bayar -->
                <div class="finance-card" style="padding: 25px;">
                    <div class="fc-info">
                        <p>BELUM BAYAR</p>
                        <h3 style="color: #ef4444; font-size: 1.5rem; margin-top: 5px;"><?= number_format($tagihanBelumBayar['cnt'] ?? 0) ?> <span style="font-size:0.9rem;color:#64748b;">tagihan</span></h3>
                        <div style="font-size:0.85rem; color:#94a3b8; margin-top:5px;">Rp <?= number_format($tagihanBelumBayar['total'] ?? 0, 0, ',', '.') ?></div>
                    </div>
                    <div class="fc-icon" style="background: #fef2f2; color: #ef4444;">
                        <i data-lucide="alert-circle"></i>
                    </div>
                </div>
            </div>

            <div class="z-panel mt-4">
                <div class="z-panel-head">
                    <div class="z-panel-title"><i data-lucide="pie-chart"></i> Rincian Saldo per Kategori</div>
                </div>
                <div class="z-table-wrap">
                    <table style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="padding: 15px 20px;">KATEGORI</th>
                                <th style="padding: 15px 20px; text-align: right;">TOTAL PEMASUKAN</th>
                                <th style="padding: 15px 20px; text-align: right;">TOTAL PENGELUARAN</th>
                                <th style="padding: 15px 20px; text-align: right;">SISA SALDO</th>
                                <th style="padding: 15px 20px; text-align: center;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($kategoriSummary as $cat => $ks): ?>
                            <tr>
                                <td style="padding: 15px 20px; font-weight: 600; color: #0f172a;"><?= htmlspecialchars($cat) ?></td>
                                <td style="padding: 15px 20px; font-weight: 600; color: #10b981; text-align: right;">Rp <?= number_format($ks['pemasukan'], 0, ',', '.') ?></td>
                                <td style="padding: 15px 20px; font-weight: 600; color: #ef4444; text-align: right;">Rp <?= number_format($ks['pengeluaran'], 0, ',', '.') ?></td>
                                <td style="padding: 15px 20px; font-weight: 800; color: #3b82f6; text-align: right;">Rp <?= number_format($ks['saldo'], 0, ',', '.') ?></td>
                                <td style="padding: 15px 20px; text-align: center;">
                                    <a href="<?php echo base_url('guru/bendahara/transaksi?kategori=' . urlencode($cat)); ?>" class="btn btn-sm" style="background: #e2e8f0; color: #475569; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600; display: inline-flex; align-items: center; gap: 5px;">
                                        <i data-lucide="eye" style="width: 14px; height: 14px;"></i> Lihat Detail
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($kategoriSummary)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 20px; color: #94a3b8;">Belum ada data kategori</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="z-panel mt-4">
                <div class="z-panel-head">
                    <div class="z-panel-title"><i data-lucide="bar-chart-2"></i> Tren Pemasukan Bulanan (6 Bulan Terakhir)</div>
                </div>
                <div class="z-table-wrap">
                    <table style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="padding: 15px 20px;">BULAN</th>
                                <th style="padding: 15px 20px;">PEMASUKAN</th>
                                <th style="padding: 15px 20px; width: 60%;">GRAFIK</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $maxPemasukan = 0;
                            foreach($trenData as $td) {
                                if($td['pemasukan'] > $maxPemasukan) $maxPemasukan = $td['pemasukan'];
                            }
                            if($maxPemasukan == 0) $maxPemasukan = 1;

                            foreach($trenData as $td): 
                                $pct = ($td['pemasukan'] / $maxPemasukan) * 100;
                            ?>
                            <tr>
                                <td style="padding: 15px 20px; font-weight: 600; color: #0f172a;"><?= htmlspecialchars($td['bulan']) ?></td>
                                <td style="padding: 15px 20px; font-weight: 700; color: #10b981;">Rp <?= number_format($td['pemasukan'], 0, ',', '.') ?></td>
                                <td style="padding: 15px 20px;">
                                    <div style="width: 100%; height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                                        <div style="width: <?= $pct ?>%; height: 100%; background: #10b981; border-radius: 4px;"></div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div> <!-- z-scroll -->
    </div> <!-- z-main -->
</div>

<script>
    lucide.createIcons();
</script>
</body>
</html>

