<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'] ?? 0;

// Cek hak akses BOS
$is_bos = false;
$stmt_app = $core_db->prepare("SELECT app_code FROM app_admins WHERE user_id = :uid AND app_code = 'bos' LIMIT 1");
$stmt_app->execute(['uid' => $user_id]);
if ($stmt_app->fetch()) {
    $is_bos = true;
}

if (!$is_bos) {
    // Cek root/superadmin
    $stmt_role = $core_db->prepare("SELECT role_id FROM users WHERE id = :uid");
    $stmt_role->execute(['uid' => $user_id]);
    $role = $stmt_role->fetchColumn();
    if ($role == 1 || $role == 99) {
        $is_bos = true;
    }
}

if (!$is_bos) {
    header("Location: akses_ditolak.php");
    exit;
}

$message = '';
$status = '';

// Proses POST Simpan Transaksi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'simpan_transaksi') {
    $jenis = $_POST['jenis'] ?? ''; // Pemasukan / Pengeluaran
    $jenis_transaksi = $_POST['jenis_transaksi'] ?? 'Tunai'; // Bank / Tunai
    $uraian = trim($_POST['uraian'] ?? '');
    $nominal = intval($_POST['nominal'] ?? 0);
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    
    if ($nominal > 0 && !empty($uraian) && !empty($jenis) && !empty($jenis_transaksi)) {
        try {
            $penerimaan = ($jenis == 'Pemasukan') ? $nominal : 0;
            $pengeluaran = ($jenis == 'Pengeluaran') ? $nominal : 0;

            $stmt = $core_db->prepare("INSERT INTO bos_bku (tanggal, uraian, penerimaan, pengeluaran, jenis_transaksi, petugas_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$tanggal, $uraian, $penerimaan, $pengeluaran, $jenis_transaksi, $user_id]);
            
            $message = 'Transaksi BOS berhasil disimpan!';
            $status = 'success';
        } catch (Exception $e) {
            $message = 'Gagal menyimpan transaksi: ' . $e->getMessage();
            $status = 'error';
        }
    } else {
        $message = 'Mohon lengkapi semua data transaksi.';
        $status = 'error';
    }
}

// Hitung Saldo BOS
$saldo_bos = 0;
try {
    $stmt_s = $core_db->query("SELECT SUM(penerimaan) as masuk, SUM(pengeluaran) as keluar FROM bos_bku");
    $res = $stmt_s->fetch(PDO::FETCH_ASSOC);
    if ($res) {
        $saldo_bos = floatval($res['masuk']) - floatval($res['keluar']);
    }
} catch (Exception $e) {}

// Ambil Riwayat BKU (15 Terbaru)
$riwayat = [];
try {
    $stmt_r = $core_db->query("SELECT * FROM bos_bku ORDER BY tanggal DESC, id DESC LIMIT 15");
    $riwayat = $stmt_r->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

$page_title = 'Keuangan BOS';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
    <div class="header-top">
        <a href="aplikasi_saya.php" style="color: white; font-size: 1.2rem; text-decoration: none;"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 style="margin: 0; font-size: 18px; flex: 1; text-align: center;">Keuangan BOS</h1>
        <div style="width: 20px;"></div>
    </div>
    
    <div style="text-align: center; margin-top: 15px;">
        <p style="margin: 0; font-size: 0.8rem; opacity: 0.9;">Saldo Kas Berjalan (BKU)</p>
        <h2 style="margin: 5px 0 0 0; font-size: 28px; font-weight: 800;">Rp <?= number_format($saldo_bos, 0, ',', '.') ?></h2>
    </div>
</div>

<div class="main-content">
    <?php if ($message): ?>
        <div style="background: <?= $status === 'success' ? '#dcfce7' : '#fee2e2' ?>; color: <?= $status === 'success' ? '#166534' : '#991b1b' ?>; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-size: 0.9rem; text-align: center; font-weight: 600; border: 1px solid <?= $status === 'success' ? '#bbf7d0' : '#fecaca' ?>;">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Form Input Cepat -->
    <h3 class="section-title"><i class="fa-solid fa-bolt" style="color: #f59e0b;"></i> Input Transaksi Cepat</h3>
    <div style="background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.03); margin-bottom: 25px; border: 1px solid #f1f5f9;">
        <form method="POST">
            <input type="hidden" name="action" value="simpan_transaksi">
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.8rem; color: #64748b; margin-bottom: 5px; font-weight: 600;">Jenis Transaksi (Keluar/Masuk)</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <label style="background: #f1f5f9; padding: 12px; border-radius: 10px; text-align: center; cursor: pointer; border: 1px solid #e2e8f0; display: block;">
                        <input type="radio" name="jenis" value="Pemasukan" style="display: none;" onchange="updateRadioUI(this)">
                        <div class="radio-label" style="font-weight: 600; color: #64748b; font-size: 0.9rem;"><i class="fa-solid fa-arrow-down-left"></i> Pemasukan</div>
                    </label>
                    <label style="background: #f1f5f9; padding: 12px; border-radius: 10px; text-align: center; cursor: pointer; border: 1px solid #e2e8f0; display: block;">
                        <input type="radio" name="jenis" value="Pengeluaran" style="display: none;" onchange="updateRadioUI(this)">
                        <div class="radio-label" style="font-weight: 600; color: #64748b; font-size: 0.9rem;"><i class="fa-solid fa-arrow-up-right"></i> Pengeluaran</div>
                    </label>
                </div>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.8rem; color: #64748b; margin-bottom: 5px; font-weight: 600;">Metode / Kasir</label>
                <select name="jenis_transaksi" required style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 0.9rem; outline: none; background: #f8fafc;">
                    <option value="Tunai">Buku Kas Tunai (Cash)</option>
                    <option value="Bank">Buku Pembantu Bank (Transfer)</option>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.8rem; color: #64748b; margin-bottom: 5px; font-weight: 600;">Nominal (Rp)</label>
                <input type="number" name="nominal" required min="1" placeholder="Cth: 500000" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 1rem; font-weight: bold; outline: none; background: #f8fafc; color: #0f172a;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.8rem; color: #64748b; margin-bottom: 5px; font-weight: 600;">Uraian Transaksi</label>
                <textarea name="uraian" required rows="3" placeholder="Cth: Pembelian ATK untuk kegiatan MATSAMA" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 0.9rem; outline: none; background: #f8fafc; resize: none;"></textarea>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.8rem; color: #64748b; margin-bottom: 5px; font-weight: 600;">Tanggal Transaksi</label>
                <input type="date" name="tanggal" required value="<?= date('Y-m-d') ?>" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 0.9rem; outline: none; background: #f8fafc;">
            </div>

            <button type="submit" style="width: 100%; background: #059669; color: white; border: none; padding: 14px; border-radius: 10px; font-size: 1rem; font-weight: bold; cursor: pointer;">Simpan Transaksi</button>
            <div style="font-size: 0.7rem; color: #94a3b8; text-align: center; margin-top: 10px;">
                Note: Untuk input detail pajak dan tautan RKAS silakan gunakan versi Web (Desktop).
            </div>
        </form>
    </div>

    <!-- Riwayat Transaksi -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 15px;">
        <h3 class="section-title" style="margin: 0;"><i class="fa-solid fa-clock-rotate-left" style="color: #64748b;"></i> Riwayat BKU Terkini</h3>
    </div>
    
    <div style="background: white; border-radius: 16px; padding: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.03); border: 1px solid #f1f5f9;">
        <?php if(empty($riwayat)): ?>
            <div style="text-align: center; padding: 30px 10px; color: #94a3b8;">
                <i class="fa-solid fa-receipt" style="font-size: 40px; margin-bottom: 10px; opacity: 0.3;"></i>
                <div style="font-size: 0.9rem;">Belum ada riwayat transaksi BOS.</div>
            </div>
        <?php else: ?>
            <?php foreach($riwayat as $r): 
                $is_masuk = floatval($r['penerimaan']) > 0;
                $nom = $is_masuk ? $r['penerimaan'] : $r['pengeluaran'];
            ?>
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f1f5f9; <?= $r === end($riwayat) ? 'border-bottom: none;' : '' ?>">
                <div style="flex: 1; padding-right: 10px;">
                    <div style="font-weight: 700; color: #1e293b; font-size: 0.9rem; margin-bottom: 3px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        <?= htmlspecialchars($r['uraian']) ?>
                    </div>
                    <div style="font-size: 0.75rem; color: #64748b;">
                        <?= date('d M Y', strtotime($r['tanggal'])) ?> &bull; Via <?= htmlspecialchars($r['jenis_transaksi']) ?>
                    </div>
                </div>
                <div style="text-align: right; flex-shrink: 0;">
                    <?php if($is_masuk): ?>
                        <div style="color: #0284c7; font-weight: 800; font-size: 0.95rem;">+Rp <?= number_format($nom, 0, ',', '.') ?></div>
                    <?php else: ?>
                        <div style="color: #ef4444; font-weight: 800; font-size: 0.95rem;">-Rp <?= number_format($nom, 0, ',', '.') ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function updateRadioUI(input) {
    // Reset all
    document.querySelectorAll('input[name="jenis"]').forEach(el => {
        let parent = el.closest('label');
        parent.style.background = '#f1f5f9';
        parent.style.borderColor = '#e2e8f0';
        parent.querySelector('.radio-label').style.color = '#64748b';
    });
    
    // Set active
    let parent = input.closest('label');
    if(input.value === 'Pemasukan') {
        parent.style.background = '#e0f2fe';
        parent.style.borderColor = '#38bdf8';
        parent.querySelector('.radio-label').style.color = '#0284c7';
    } else {
        parent.style.background = '#fee2e2';
        parent.style.borderColor = '#f87171';
        parent.querySelector('.radio-label').style.color = '#dc2626';
    }
}
</script>

<style>
/* Sembunyikan panah input type number */
input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
  -webkit-appearance: none; 
  margin: 0; 
}
</style>

<?php include 'footer_guru.php'; ?>
