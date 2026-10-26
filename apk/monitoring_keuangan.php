<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$is_kamad = false;
$guru_id = $_SESSION['guru_id'];
try {
    $stmt_g = $core_db->prepare("SELECT is_kamad, nama FROM guru WHERE id = :id LIMIT 1");
    $stmt_g->execute(['id' => $guru_id]);
    $guru_data = $stmt_g->fetch(PDO::FETCH_ASSOC);
    if ($guru_data && $guru_data['is_kamad'] == 1) {
        $is_kamad = true;
    }
} catch (Exception $e) {}

if (!$is_kamad) {
    header("Location: akses_ditolak.php");
    exit;
}

// Hitung Monitoring Keuangan
$finance_categories = [];
$total_saldo = 0;
$tagihanBelumBayar = 0;

try {
    $r_cats = $core_db->query("
        SELECT c.nama_kategori, g.nama as nama_guru 
        FROM keuangan_komite_kategori c
        LEFT JOIN guru g ON c.guru_id = g.id
        ORDER BY c.nama_kategori ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $payments_by_cat = [];
    $res_pay = $core_db->query("
        SELECT k.kategori, SUM(p.jumlah) as total 
        FROM keuangan_komite_pembayaran p
        JOIN (
            SELECT DISTINCT nama_tagihan, kategori FROM keuangan_komite_jenis
        ) k ON p.jenis_pembayaran = k.nama_tagihan
        GROUP BY k.kategori
    ")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($res_pay as $r) {
        $payments_by_cat[$r['kategori']] = floatval($r['total']);
    }

    $manual_by_cat = [];
    $res_man = $core_db->query("
        SELECT kategori, 
               SUM(CASE WHEN jenis='Pemasukan' THEN jumlah ELSE 0 END) as total_in,
               SUM(CASE WHEN jenis='Pengeluaran' THEN jumlah ELSE 0 END) as total_out
        FROM keuangan_komite_transaksi
        GROUP BY kategori
    ")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($res_man as $r) {
        $manual_by_cat[$r['kategori']] = [
            'in' => floatval($r['total_in']),
            'out' => floatval($r['total_out'])
        ];
    }

    $komite_categories = [];
    foreach ($r_cats as $c) {
        $cat_name = $c['nama_kategori'];
        $pay_in = $payments_by_cat[$cat_name] ?? 0;
        $man_in = $manual_by_cat[$cat_name]['in'] ?? 0;
        $man_out = $manual_by_cat[$cat_name]['out'] ?? 0;
        
        $pemasukan = $pay_in + $man_in;
        $pengeluaran = $man_out;
        $saldo = $pemasukan - $pengeluaran;
        
        $komite_categories[] = [
            'nama' => $cat_name,
            'petugas' => $c['nama_guru'] ?? 'Belum Ditunjuk',
            'saldo' => $saldo,
            'pemasukan' => $pemasukan,
            'pengeluaran' => $pengeluaran
        ];
    }
    
    $total_komite = array_sum(array_column($komite_categories, 'saldo'));

    // --- TABUNGAN ---
    $tab_pemasukan = 0;
    $tab_pengeluaran = 0;
    $tab_saldo = 0;
    
    $r_tab = $core_db->query("
        SELECT 
            SUM(CASE WHEN jenis_mutasi='Setor' THEN jumlah ELSE 0 END) as in_tot,
            SUM(CASE WHEN jenis_mutasi='Tarik' THEN jumlah ELSE 0 END) as out_tot
        FROM keuangan_tabungan_mutasi
    ")->fetch(PDO::FETCH_ASSOC);
    
    if ($r_tab) {
        $tab_pemasukan = floatval($r_tab['in_tot']);
        $tab_pengeluaran = floatval($r_tab['out_tot']);
    }
    
    $r_tab_saldo = $core_db->query("SELECT SUM(saldo) as total_saldo FROM keuangan_tabungan")->fetch(PDO::FETCH_ASSOC);
    if ($r_tab_saldo) {
        $tab_saldo = floatval($r_tab_saldo['total_saldo']);
    }
    
    $tabungan_data = [
        'nama' => 'TABUNGAN SISWA',
        'petugas' => 'Admin Tabungan',
        'saldo' => $tab_saldo,
        'pemasukan' => $tab_pemasukan,
        'pengeluaran' => $tab_pengeluaran
    ];

    $r_tagihan = $core_db->query("SELECT COUNT(*) as cnt FROM keuangan_tagihan WHERE status != 'Lunas'")->fetch(PDO::FETCH_ASSOC);
    $tagihanBelumBayar = $r_tagihan ? intval($r_tagihan['cnt']) : 0;
} catch(Exception $e) {}

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}

$page_title = 'Monitoring Keuangan';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
    <div class="header-top">
        <a href="aplikasi_saya.php" style="color: white; font-size: 1.2rem; text-decoration: none;"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 style="color: white; font-size: 1.2rem; margin: 0; flex: 1; text-align: center;">Monitoring Keuangan</h1>
        <div style="width: 20px;"></div>
    </div>
</div>

<div class="main-content">
    <!-- KOMITE CARD -->
    <div class="data-card" style="margin-top: 10px;">
        <div style="font-size: 1.1rem; font-weight: bold; color: #1e293b; margin-bottom: 15px; border-bottom: 2px solid #10b981; display: inline-block; padding-bottom: 4px;">Keuangan Komite</div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div style="background: #ffffff; border-radius: 12px; padding: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 4px solid #10b981;">
                <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; margin-bottom: 5px;">Total Saldo Kas</div>
                <div style="font-size: 0.95rem; font-weight: bold; color: #10b981;"><?= formatRupiah($total_komite) ?></div>
            </div>
            <div style="background: #ffffff; border-radius: 12px; padding: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 4px solid #ef4444;">
                <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; margin-bottom: 5px;">Tagihan Belum Lunas</div>
                <div style="font-size: 0.95rem; font-weight: bold; color: #ef4444;"><?= $tagihanBelumBayar ?> <span style="font-size: 0.75rem; font-weight: normal; color: #64748b;">tagihan</span></div>
            </div>
        </div>

        <div style="font-size: 0.85rem; font-weight: bold; color: #475569; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px; margin-bottom: 10px;">Saldo Kas Per Kategori</div>
        <?php foreach ($komite_categories as $index => $fc): ?>
            <div style="border-bottom: <?= ($index === count($komite_categories) - 1) ? 'none' : '1px solid #f1f5f9' ?>; padding: 12px 0; display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;">
                <div>
                    <div style="font-weight: bold; color: #1e293b; font-size: 0.85rem; letter-spacing: 0.2px;"><?= htmlspecialchars($fc['nama']) ?></div>
                    <div style="margin-top: 5px;">
                        <span style="font-size: 0.68rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; display: block;">Petugas</span>
                        <span style="font-size: 0.78rem; color: #475569; font-weight: bold; margin-top: 1px; display: flex; align-items: center; gap: 4px;">
                            <i class="fa-solid fa-user-tie" style="color: #6366f1; font-size: 0.75rem;"></i>
                            <?= htmlspecialchars($fc['petugas']) ?>
                        </span>
                    </div>
                </div>
                <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 4px; min-width: 120px;">
                    <div style="font-weight: 800; color: #0f172a; font-size: 0.95rem; margin-bottom: 2px;"><?= formatRupiah($fc['saldo']) ?></div>
                    <div style="font-size: 0.7rem; color: #16a34a; background: #f0fdf4; border: 1px solid #dcfce7; font-weight: 700; padding: 2px 6px; border-radius: 4px; white-space: nowrap; display: inline-block;">
                        Masuk: <?= formatRupiah($fc['pemasukan']) ?>
                    </div>
                    <div style="font-size: 0.7rem; color: #dc2626; background: #fef2f2; border: 1px solid #fee2e2; font-weight: 700; padding: 2px 6px; border-radius: 4px; white-space: nowrap; display: inline-block;">
                        Keluar: <?= formatRupiah($fc['pengeluaran']) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- TABUNGAN CARD -->
    <div class="data-card" style="margin-top: 15px;">
        <div style="font-size: 1.1rem; font-weight: bold; color: #1e293b; margin-bottom: 15px; border-bottom: 2px solid #f59e0b; display: inline-block; padding-bottom: 4px;">Tabungan Siswa</div>
        
        <div style="display: grid; grid-template-columns: 1fr; gap: 15px; margin-bottom: 20px;">
            <div style="background: #ffffff; border-radius: 12px; padding: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 4px solid #f59e0b;">
                <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; margin-bottom: 5px;">Total Saldo Tabungan</div>
                <div style="font-size: 0.95rem; font-weight: bold; color: #f59e0b;"><?= formatRupiah($tabungan_data['saldo']) ?></div>
            </div>
        </div>

        <div style="font-size: 0.85rem; font-weight: bold; color: #475569; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px; margin-bottom: 10px;">Rekap Mutasi Tabungan</div>
        <div style="padding: 12px 0; display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;">
            <div>
                <div style="font-weight: bold; color: #1e293b; font-size: 0.85rem; letter-spacing: 0.2px;"><?= htmlspecialchars($tabungan_data['nama']) ?></div>
                <div style="margin-top: 5px;">
                    <span style="font-size: 0.68rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; display: block;">Petugas</span>
                    <span style="font-size: 0.78rem; color: #475569; font-weight: bold; margin-top: 1px; display: flex; align-items: center; gap: 4px;">
                        <i class="fa-solid fa-user-tie" style="color: #6366f1; font-size: 0.75rem;"></i>
                        <?= htmlspecialchars($tabungan_data['petugas']) ?>
                    </span>
                </div>
            </div>
            <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 4px; min-width: 120px;">
                <div style="font-size: 0.7rem; color: #16a34a; background: #f0fdf4; border: 1px solid #dcfce7; font-weight: 700; padding: 2px 6px; border-radius: 4px; white-space: nowrap; display: inline-block;">
                    Masuk: <?= formatRupiah($tabungan_data['pemasukan']) ?>
                </div>
                <div style="font-size: 0.7rem; color: #dc2626; background: #fef2f2; border: 1px solid #fee2e2; font-weight: 700; padding: 2px 6px; border-radius: 4px; white-space: nowrap; display: inline-block;">
                    Keluar: <?= formatRupiah($tabungan_data['pengeluaran']) ?>
                </div>
            </div>
        </div>
    </div>

    </div>
</div>

<?php include 'footer_guru.php'; ?>
