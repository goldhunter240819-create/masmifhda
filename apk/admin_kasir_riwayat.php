<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];
$user_id = $_SESSION['user_id'] ?? 0;

// Cek hak akses keuangan (app_admins atau keuangan_akses)
$is_keuangan = false;
$allowed_tagihan = [];
$is_super_keuangan = false;

$stmt_app = $core_db->prepare("SELECT app_code FROM app_admins WHERE user_id = :uid AND app_code = 'keuangan' LIMIT 1");
$stmt_app->execute(['uid' => $user_id]);
if ($stmt_app->fetch()) {
    $is_keuangan = true;
    $is_super_keuangan = true;
} else {
    $cek_bendahara = $core_db->prepare("SELECT id, akses_pembayaran, can_delete FROM keuangan_akses WHERE guru_id = ?");
    $cek_bendahara->execute([$guru_id]);
    $bendahara = $cek_bendahara->fetch(PDO::FETCH_ASSOC);
    if ($bendahara) {
        $is_keuangan = true;
        if (!empty($bendahara['akses_pembayaran'])) {
            $allowed_tagihan = json_decode($bendahara['akses_pembayaran'], true) ?: [];
        }
    }
}

if (!$is_keuangan) {
    header("Location: akses_ditolak.php");
    exit;
}

// Ambil list jenis pembayaran (tagihan) untuk dropdown filter
if ($is_super_keuangan) {
    $tagihan_list = $core_db->query("SELECT nama_tagihan FROM keuangan_komite_jenis ORDER BY nama_tagihan ASC")->fetchAll(PDO::FETCH_COLUMN);
} else {
    $tagihan_list = $allowed_tagihan;
}

// Filter
$filter_waktu = isset($_GET['waktu']) ? $_GET['waktu'] : 'hari_ini';
$filter_jenis = isset($_GET['jenis']) ? $_GET['jenis'] : '';
$search_siswa = isset($_GET['q']) ? trim($_GET['q']) : '';

// Susun Query
$where = [];
$params = [];

// Filter Waktu
if ($filter_waktu === 'hari_ini') {
    $where[] = "kp.tanggal_bayar = CURDATE()";
} elseif ($filter_waktu === 'minggu_ini') {
    $where[] = "YEARWEEK(kp.tanggal_bayar, 1) = YEARWEEK(CURDATE(), 1)";
} elseif ($filter_waktu === 'bulan_ini') {
    $where[] = "MONTH(kp.tanggal_bayar) = MONTH(CURDATE()) AND YEAR(kp.tanggal_bayar) = YEAR(CURDATE())";
}

// Filter Hak Akses & Jenis Pembayaran
if (!empty($filter_jenis)) {
    if (!$is_super_keuangan && !in_array($filter_jenis, $allowed_tagihan)) {
        // Akses ditolak untuk filter ini, set ke empty yang tak mungkin
        $where[] = "1 = 0"; 
    } else {
        $where[] = "kp.jenis_pembayaran = ?";
        $params[] = $filter_jenis;
    }
} else {
    if (!$is_super_keuangan) {
        if (empty($allowed_tagihan)) {
            $where[] = "1 = 0";
        } else {
            $in = str_repeat('?,', count($allowed_tagihan) - 1) . '?';
            $where[] = "kp.jenis_pembayaran IN ($in)";
            $params = array_merge($params, $allowed_tagihan);
        }
    }
}

// Pencarian Siswa
if (!empty($search_siswa)) {
    $where[] = "(s.nama LIKE ? OR s.nis LIKE ?)";
    $params[] = "%$search_siswa%";
    $params[] = "%$search_siswa%";
}

$where_clause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Query Data
$sql = "
    SELECT kp.*, s.nama
    FROM keuangan_komite_pembayaran kp
    JOIN siswa s ON kp.siswa_id = s.id
    $where_clause
    ORDER BY kp.id DESC
    LIMIT 100
";

try {
    $stmt = $core_db->prepare($sql);
    $stmt->execute($params);
    $riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $riwayat = [];
}

// Hitung Total tanpa Limit
try {
    $sql_total = "
        SELECT SUM(kp.jumlah) as total, COUNT(kp.id) as count
        FROM keuangan_komite_pembayaran kp
        JOIN siswa s ON kp.siswa_id = s.id
        $where_clause
    ";
    $stmt_total = $core_db->prepare($sql_total);
    $stmt_total->execute($params);
    $tot_row = $stmt_total->fetch(PDO::FETCH_ASSOC);
    $total_riwayat = $tot_row['total'] ? floatval($tot_row['total']) : 0;
    $count_riwayat = $tot_row['count'] ? intval($tot_row['count']) : 0;
} catch (Exception $e) {
    $total_riwayat = 0;
    $count_riwayat = 0;
}

$page_title = 'Riwayat Kasir';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding-bottom: 70px;">
    <div class="header-top">
        <div class="greeting">
            <p style="color: #d1fae5;">Kasir Pembayaran</p>
            <h1 style="color: white;">Riwayat Transaksi</h1>
        </div>
        <div class="avatar" style="color: #10b981; background: white;">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -55px; z-index: 10; position: relative;">
        <h3 class="section-title" style="margin-top: 0; margin-bottom: 15px;">Filter Pencarian</h3>
        
        <form method="GET" style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 0;">
            <div style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <select name="waktu" onchange="this.form.submit()" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; background: #f8fafc;">
                        <option value="hari_ini" <?= $filter_waktu == 'hari_ini' ? 'selected' : '' ?>>Hari Ini</option>
                        <option value="minggu_ini" <?= $filter_waktu == 'minggu_ini' ? 'selected' : '' ?>>Minggu Ini</option>
                        <option value="bulan_ini" <?= $filter_waktu == 'bulan_ini' ? 'selected' : '' ?>>Bulan Ini</option>
                        <option value="semua" <?= $filter_waktu == 'semua' ? 'selected' : '' ?>>Semua Waktu</option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <select name="jenis" onchange="this.form.submit()" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; background: #f8fafc;">
                        <option value="">Semua Jenis</option>
                        <?php foreach($tagihan_list as $tag): ?>
                            <option value="<?= htmlspecialchars($tag) ?>" <?= $filter_jenis == $tag ? 'selected' : '' ?>><?= htmlspecialchars($tag) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <input type="text" name="q" value="<?= htmlspecialchars($search_siswa) ?>" placeholder="Cari nama atau NIS siswa..." style="flex: 1; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; background: white;">
                <button type="submit" style="background: #10b981; color: white; border: none; padding: 0 15px; border-radius: 8px; font-weight: bold;"><i class="fa-solid fa-search"></i></button>
            </div>
        </form>
    </div>
    
    <div style="margin-bottom: 15px; padding: 0 5px; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;">Total Pemasukan (Filter)</div>
            <div style="font-weight: 800; font-size: 1.3rem; color: #10b981;">Rp <?= number_format($total_riwayat,0,',','.') ?></div>
        </div>
        <div style="background: #e2e8f0; color: #64748b; font-size: 0.75rem; font-weight: bold; padding: 4px 10px; border-radius: 20px;">
            <?= $count_riwayat ?> transaksi
        </div>
    </div>
    
    <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 25px;">
        <?php if (empty($riwayat)): ?>
            <div style="text-align: center; padding: 30px; background: white; border-radius: 12px; border: 1px dashed #cbd5e1; color: #94a3b8;">
                <i class="fa-solid fa-receipt" style="font-size: 2rem; margin-bottom: 10px; opacity: 0.5;"></i>
                <p style="margin:0;">Tidak ada riwayat pembayaran.</p>
            </div>
        <?php else: ?>
            <?php foreach($riwayat as $r): ?>
                <div style="background: white; border-radius: 12px; padding: 15px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                    <div style="display: flex; align-items: center; gap: 12px; flex: 1; overflow: hidden;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #ecfdf5; color: #10b981; flex-shrink: 0;">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div style="overflow: hidden;">
                            <div style="font-weight: 600; font-size: 0.95rem; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($r['nama']) ?></div>
                            <div style="font-size: 0.75rem; color: #64748b; margin-top: 3px;">
                                <?= date('d M Y', strtotime($r['tanggal_bayar'])) ?> &bull; <?= htmlspecialchars($r['jenis_pembayaran']) ?>
                            </div>
                        </div>
                    </div>
                        <div style="text-align: right; margin-left: 10px;">
                            <div style="font-weight: bold; font-size: 0.95rem; color: #10b981; margin-bottom: 5px;">
                                +Rp <?= number_format($r['jumlah'],0,',','.') ?>
                            </div>
                        </div>
                    </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <a href="admin_kasir.php" class="btn" style="display: block; text-align: center; background: #f1f5f9; color: #475569; padding: 12px; border-radius: 50px; font-weight: 600; text-decoration: none; margin-bottom: 20px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Kasir
    </a>
</div>

<script>
function bukaKwitansi(id) {
    window.location.href = 'admin_kasir_kwitansi.php?id=' + id;
}

function hapusPembayaran(id, event) {
    if (confirm("Apakah Anda yakin ingin menghapus riwayat pembayaran ini?")) {
        var btn = event.currentTarget;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
        btn.disabled = true;
        
        fetch('../admin/finance/komite/pembayaran/delete/' + id, {
            method: 'GET'
        }).then(r => r.json())
        .then(data => {
            if(data.success) {
                alert("Pembayaran berhasil dihapus!");
                window.location.reload();
            } else {
                alert("Gagal menghapus: " + (data.message || "Unknown error"));
                btn.innerHTML = '<i class="fa-solid fa-trash"></i>';
                btn.disabled = false;
            }
        }).catch(err => {
            alert("Terjadi kesalahan jaringan.");
            btn.innerHTML = '<i class="fa-solid fa-trash"></i>';
            btn.disabled = false;
        });
    }
}
</script>

<?php include 'footer_guru.php'; ?>
