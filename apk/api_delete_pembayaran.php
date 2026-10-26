<?php
require_once 'koneksi.php';
header('Content-Type: application/json');

if (!isset($_SESSION['guru_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
    exit;
}

try {
    // Cek Hak Akses Delete
    $is_app_admin = $core_db->prepare("SELECT id FROM app_admins WHERE user_id = ? AND app_code = 'keuangan'");
    $is_app_admin->execute([$_SESSION['user_id'] ?? 0]);
    $can_delete = false;
    $is_super_keuangan = false;
    
    if ($is_app_admin->fetch()) {
        $can_delete = true;
        $is_super_keuangan = true;
    } else {
        $cekDel = $core_db->prepare("SELECT can_delete FROM keuangan_akses WHERE guru_id = ?");
        $cekDel->execute([$_SESSION['guru_id']]);
        $adm = $cekDel->fetch(PDO::FETCH_ASSOC);
        if ($adm && $adm['can_delete'] == 1) {
            $can_delete = true;
        }
    }

    if (!$can_delete) {
        throw new Exception("Anda tidak memiliki akses untuk menghapus pembayaran.");
    }

    $core_db->beginTransaction();

    // 1. Dapatkan detail pembayaran
    $stmt = $core_db->prepare("SELECT * FROM keuangan_komite_pembayaran WHERE id = ?");
    $stmt->execute([$id]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$payment) {
        throw new Exception("Pembayaran tidak ditemukan");
    }

    $siswa_id = $payment['siswa_id'];
    $jenis_pembayaran = $payment['jenis_pembayaran'];

    // 2. Hapus pembayaran
    $del = $core_db->prepare("DELETE FROM keuangan_komite_pembayaran WHERE id = ?");
    $del->execute([$id]);

    // 3. Kalkulasi ulang status tagihan siswa
    $sum_stmt = $core_db->prepare("SELECT COALESCE(SUM(jumlah), 0) as total_bayar FROM keuangan_komite_pembayaran WHERE siswa_id = ? AND jenis_pembayaran = ?");
    $sum_stmt->execute([$siswa_id, $jenis_pembayaran]);
    $total_bayar = floatval($sum_stmt->fetchColumn());

    $tagihan_stmt = $core_db->prepare("SELECT id, jumlah_tagihan FROM keuangan_tagihan WHERE siswa_id = ? AND nama_tagihan = ?");
    $tagihan_stmt->execute([$siswa_id, $jenis_pembayaran]);
    $tagihan = $tagihan_stmt->fetch(PDO::FETCH_ASSOC);

    if ($tagihan) {
        $jumlah_tagihan = floatval($tagihan['jumlah_tagihan']);
        
        if ($total_bayar <= 0) {
            $status = 'Belum Bayar';
        } else if ($total_bayar >= $jumlah_tagihan) {
            $status = 'Lunas';
        } else {
            $status = 'Menyicil';
        }

        $upd = $core_db->prepare("UPDATE keuangan_tagihan SET status = ? WHERE id = ?");
        $upd->execute([$status, $tagihan['id']]);
    }

    // Catat log
    require_once __DIR__ . '/../vendor/autoload.php';
    if (class_exists('\App\Services\AuditLogger')) {
        \App\Services\AuditLogger::dataChange('Keuangan', 'DELETE_PEMBAYARAN_KASIR', "id={$id}, tagihan={$jenis_pembayaran}");
    }
    
    // 4. Kalkulasi ulang Total Saldo untuk dikembalikan ke frontend
    $total_saldo = 0;
    if ($is_super_keuangan) {
        $tot_siswa_all = $core_db->query("SELECT SUM(jumlah) FROM keuangan_komite_pembayaran")->fetchColumn() ?: 0;
        $tot_in_all = $core_db->query("SELECT SUM(jumlah) FROM keuangan_komite_transaksi WHERE jenis='Pemasukan'")->fetchColumn() ?: 0;
        $tot_out_all = $core_db->query("SELECT SUM(jumlah) FROM keuangan_komite_transaksi WHERE jenis='Pengeluaran'")->fetchColumn() ?: 0;
        $total_saldo = ($tot_siswa_all + $tot_in_all) - $tot_out_all;
    } else {
        // Ambil tagihan yang diizinkan untuk kasir ini
        $cek_bend = $core_db->prepare("SELECT akses_pembayaran FROM keuangan_akses WHERE guru_id = ?");
        $cek_bend->execute([$_SESSION['guru_id']]);
        $bend_data = $cek_bend->fetch(PDO::FETCH_ASSOC);
        $allowed = [];
        if ($bend_data && !empty($bend_data['akses_pembayaran'])) {
            $allowed = json_decode($bend_data['akses_pembayaran'], true) ?: [];
        }
        
        if (!empty($allowed)) {
            $in_tot = str_repeat('?,', count($allowed) - 1) . '?';
            $sql_tot_siswa = "SELECT SUM(jumlah) FROM keuangan_komite_pembayaran WHERE jenis_pembayaran IN ($in_tot)";
            $stmt_tot_siswa = $core_db->prepare($sql_tot_siswa);
            $stmt_tot_siswa->execute($allowed);
            $tot_siswa_all = $stmt_tot_siswa->fetchColumn() ?: 0;

            $sql_kat = "SELECT DISTINCT kategori FROM keuangan_komite_jenis WHERE nama_tagihan IN ($in_tot)";
            $stmt_kat = $core_db->prepare($sql_kat);
            $stmt_kat->execute($allowed);
            $kategori_managed = $stmt_kat->fetchAll(PDO::FETCH_COLUMN);

            $tot_in_all = 0;
            $tot_out_all = 0;
            if (!empty($kategori_managed)) {
                $in_kat = str_repeat('?,', count($kategori_managed) - 1) . '?';
                
                $sql_in = "SELECT SUM(jumlah) FROM keuangan_komite_transaksi WHERE jenis='Pemasukan' AND kategori IN ($in_kat)";
                $stmt_in = $core_db->prepare($sql_in);
                $stmt_in->execute($kategori_managed);
                $tot_in_all = $stmt_in->fetchColumn() ?: 0;
                
                $sql_out = "SELECT SUM(jumlah) FROM keuangan_komite_transaksi WHERE jenis='Pengeluaran' AND kategori IN ($in_kat)";
                $stmt_out = $core_db->prepare($sql_out);
                $stmt_out->execute($kategori_managed);
                $tot_out_all = $stmt_out->fetchColumn() ?: 0;
            }

            $total_saldo = ($tot_siswa_all + $tot_in_all) - $tot_out_all;
        }
    }

    $core_db->commit();
    echo json_encode([
        'success' => true, 
        'new_saldo' => $total_saldo,
        'new_saldo_formatted' => 'Rp ' . number_format($total_saldo, 0, ',', '.')
    ]);
} catch (Exception $e) {
    if ($core_db->inTransaction()) {
        $core_db->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
