<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];
$nama_ekstra = $_GET['ekstra'] ?? '';
$tahun_ajaran = $_GET['ta'] ?? '';

if (empty($nama_ekstra) || empty($tahun_ajaran)) {
    echo "<script>alert('Data tidak valid.'); window.location.href='ekstra_pembina.php';</script>";
    exit;
}

// Cek hak akses pembina
$stmt = $core_db->prepare("SELECT id FROM guru_tugas WHERE guru_id = :gid AND tugas = 'Pembina Ekstrakurikuler' AND keterangan = :ekstra AND tahun_ajaran = :ta");
$stmt->execute(['gid' => $guru_id, 'ekstra' => $nama_ekstra, 'ta' => $tahun_ajaran]);
if (!$stmt->fetch()) {
    echo "<script>alert('Akses ditolak.'); window.location.href='ekstra_pembina.php';</script>";
    exit;
}

// Handle Delete Jurnal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete_jurnal') {
    $id_hapus = $_POST['id_hapus'] ?? 0;
    if ($id_hapus > 0) {
        $db_siakad = $siakad_db;
        $jurnal_old = $db_siakad->query("SELECT dokumentasi FROM ekstra_jurnal WHERE id = " . intval($id_hapus))->fetch();
        if ($jurnal_old && !empty($jurnal_old['dokumentasi'])) {
            $path = __DIR__ . '/../public/uploads/ekstra_jurnal/' . $jurnal_old['dokumentasi'];
            if (file_exists($path)) {
                unlink($path);
            }
        }
        
        $del = $db_siakad->prepare("DELETE FROM ekstra_jurnal WHERE id = ? AND nama_ekstra = ?");
        $del->execute([$id_hapus, $nama_ekstra]);
        $msg = "Jurnal berhasil dihapus.";
    }
}

// Ambil Riwayat Jurnal
$db_siakad = $siakad_db;
$jurnals = $db_siakad->query("SELECT * FROM ekstra_jurnal 
                                WHERE nama_ekstra = ".$db_siakad->quote($nama_ekstra)." 
                                AND tahun_ajaran = ".$db_siakad->quote($tahun_ajaran)." 
                                ORDER BY tanggal DESC, id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Helper Tanggal Indo
$days_indo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
$months_indo = ['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'];

function formatTanggalIndo($date) {
    global $days_indo, $months_indo;
    $d = date('l', strtotime($date));
    $m = date('m', strtotime($date));
    return $days_indo[$d] . ', ' . date('d', strtotime($date)) . ' ' . $months_indo[$m] . ' ' . date('Y', strtotime($date));
}

$page_title = 'Jurnal ' . htmlspecialchars($nama_ekstra);
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%); padding-top: 3.5rem;">
    <div class="header-top">
        <div class="greeting">
            <p>Jurnal & Absensi</p>
            <h1><?= htmlspecialchars($nama_ekstra) ?></h1>
        </div>
        <div class="avatar" style="color: #10b981; background: white;">
            <i class="fa-solid fa-book"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <?php if (isset($msg)): ?>
        <div style="background: #dcfce7; color: #16a34a; padding: 10px; border-radius: 8px; font-size: 0.8rem; margin-top: -1.5rem; margin-bottom: 15px; border: 1px solid #bbf7d0; text-align: center; position: relative; z-index: 20;">
            <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['success'])): ?>
        <div style="background: #dcfce7; color: #16a34a; padding: 10px; border-radius: 8px; font-size: 0.8rem; margin-top: -1.5rem; margin-bottom: 15px; border: 1px solid #bbf7d0; text-align: center; position: relative; z-index: 20;">
            <i class="fa-solid fa-circle-check"></i> Jurnal & Absensi berhasil disimpan.
        </div>
    <?php endif; ?>

    <div class="data-card" style="margin-top: <?= isset($msg) || isset($_GET['success']) ? '0' : '-2.5rem' ?>; z-index: 10;">
        <a href="ekstra_jurnal_form.php?ekstra=<?= urlencode($nama_ekstra) ?>&ta=<?= urlencode($tahun_ajaran) ?>" style="display: flex; justify-content: center; align-items: center; gap: 8px; background: #10b981; color: white; padding: 12px; border-radius: 12px; font-weight: 700; text-decoration: none; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2);">
            <i class="fa-solid fa-circle-plus" style="font-size: 1.1rem;"></i> Isi Jurnal & Absen Hari Ini
        </a>
    </div>

    <div class="data-card" style="margin-top: 15px;">
        <h3 class="section-title" style="margin-bottom: 15px;"><i class="fa-solid fa-clock-rotate-left" style="color: #10b981; margin-right: 5px;"></i> Riwayat Kegiatan</h3>
        
        <?php if (empty($jurnals)): ?>
            <div style="text-align: center; padding: 20px; color: #64748b;">
                <i class="fa-regular fa-folder-open" style="font-size: 2rem; opacity: 0.3; margin-bottom: 10px; display: block;"></i>
                <p style="font-size: 0.85rem;">Belum ada jurnal yang diisi.</p>
            </div>
        <?php else: ?>
            <div style="position: relative; padding-left: 15px; border-left: 2px solid #e2e8f0; margin-left: 10px; margin-bottom: 30px;">
                <?php foreach ($jurnals as $idx => $jurnal): 
                    // Get attendance counts
                    $hadir = $db_siakad->query("SELECT COUNT(*) as t FROM ekstra_absen WHERE jurnal_id = ".$jurnal['id']." AND status_kehadiran = 'Hadir'")->fetch()['t'];
                    $sakit = $db_siakad->query("SELECT COUNT(*) as t FROM ekstra_absen WHERE jurnal_id = ".$jurnal['id']." AND status_kehadiran = 'Sakit'")->fetch()['t'];
                    $izin = $db_siakad->query("SELECT COUNT(*) as t FROM ekstra_absen WHERE jurnal_id = ".$jurnal['id']." AND status_kehadiran = 'Izin'")->fetch()['t'];
                    $alpa = $db_siakad->query("SELECT COUNT(*) as t FROM ekstra_absen WHERE jurnal_id = ".$jurnal['id']." AND status_kehadiran = 'Alpa'")->fetch()['t'];
                ?>
                    
                    <div style="position: relative; margin-bottom: 20px;">
                        <div style="position: absolute; left: -22px; top: 0px; width: 12px; height: 12px; border-radius: 50%; background: #10b981; border: 2px solid white; box-shadow: 0 0 0 1px #10b981;"></div>
                        
                        <div style="margin-left: 5px;">
                            <span style="font-size: 0.75rem; font-weight: 800; color: #10b981;">
                                <i class="fa-regular fa-calendar" style="margin-right: 4px;"></i> <?= formatTanggalIndo($jurnal['tanggal']) ?>
                            </span>
                            
                            <div style="background: white; border-radius: 12px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; margin-top: 8px;">
                                <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 3px; font-weight: 700;">Materi / Topik:</div>
                                <p style="margin: 0; font-size: 0.85rem; color: #1e293b; line-height: 1.5; white-space: pre-wrap; font-weight: 500;"><?= htmlspecialchars($jurnal['materi']) ?></p>
                                
                                <?php if (!empty($jurnal['dokumentasi'])): ?>
                                    <div style="margin-top: 10px;">
                                        <img src="/public/uploads/ekstra_jurnal/<?= $jurnal['dokumentasi'] ?>" style="width: 100%; max-height: 150px; object-fit: cover; border-radius: 8px;">
                                    </div>
                                <?php endif; ?>
                                
                                <div style="margin-top: 10px; border-top: 1px solid #cbd5e1; padding-top: 8px; display: flex; gap: 5px; flex-wrap: wrap;">
                                    <span style="font-size:0.7rem; color:#475569; font-weight:700; margin-right: 5px; margin-top: 2px;">Absen:</span>
                                    <span style="background:white; border: 1px solid #dcfce7; color:#16a34a; padding:2px 8px; border-radius:4px; font-size:0.7rem; font-weight:600;">H: <?= $hadir ?></span>
                                    <span style="background:white; border: 1px solid #fef3c7; color:#d97706; padding:2px 8px; border-radius:4px; font-size:0.7rem; font-weight:600;">S: <?= $sakit ?></span>
                                    <span style="background:white; border: 1px solid #dbeafe; color:#1d4ed8; padding:2px 8px; border-radius:4px; font-size:0.7rem; font-weight:600;">I: <?= $izin ?></span>
                                    <span style="background:white; border: 1px solid #fee2e2; color:#dc2626; padding:2px 8px; border-radius:4px; font-size:0.7rem; font-weight:600;">A: <?= $alpa ?></span>
                                </div>

                                <div style="display: flex; gap: 10px; margin-top: 15px;">
                                    <a href="ekstra_jurnal_form.php?ekstra=<?= urlencode($nama_ekstra) ?>&ta=<?= urlencode($tahun_ajaran) ?>&id=<?= $jurnal['id'] ?>" style="flex: 1; text-align: center; background: #f8fafc; border: 1px solid #cbd5e1; color: #475569; padding: 6px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; text-decoration: none;">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                    <form method="POST" style="flex: 1; margin: 0;" onsubmit="return confirm('Hapus jurnal ini? Seluruh data absen terkait juga akan hilang.');">
                                        <input type="hidden" name="action" value="delete_jurnal">
                                        <input type="hidden" name="id_hapus" value="<?= $jurnal['id'] ?>">
                                        <button type="submit" style="width: 100%; background: #fee2e2; border: 1px solid #fca5a5; color: #dc2626; padding: 6px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                                            <i class="fa-solid fa-trash-can"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <a href="ekstra_pembina.php" class="btn" style="display: block; text-align: center; background: #f1f5f9; color: #475569; padding: 12px; border-radius: 50px; font-weight: 600; text-decoration: none; margin-bottom: 20px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Menu
    </a>
</div>

<?php include 'footer_guru.php'; ?>
