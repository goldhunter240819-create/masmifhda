<?php
require_once 'koneksi.php';
if (!isset($_SESSION['guru_id'])) { header("Location: login.php"); exit; }

$hari_indo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$hari_ini = $hari_indo[date('w')];
$filter_kelas = isset($_GET['kelas_id']) ? (int)$_GET['kelas_id'] : 0;

$jadwal = [];
$classes = [];
try {
    $core_db_name = $core_db->query("SELECT database()")->fetchColumn();

    // Fetch classes for dropdown
    $stmt_classes = $core_db->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat ASC, nama_kelas ASC");
    $classes = $stmt_classes->fetchAll(PDO::FETCH_ASSOC);

    // Build query with filter
    $query = "
        SELECT j.jam_mulai, j.jam_selesai, j.mata_pelajaran_id as mapel_id, m.nama_mapel, k.nama_kelas, g.nama as nama_guru 
        FROM jadwal_pelajaran j 
        LEFT JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id 
        LEFT JOIN {$core_db_name}.kelas k ON j.kelas_id = k.id 
        LEFT JOIN {$core_db_name}.guru g ON j.guru_id = g.id 
        WHERE j.hari = :hari
    ";
    
    $params = [':hari' => $hari_ini];
    if ($filter_kelas > 0) {
        $query .= " AND j.kelas_id = :kelas_id";
        $params[':kelas_id'] = $filter_kelas;

        $query .= " ORDER BY j.jam_mulai ASC, k.tingkat ASC, k.nama_kelas ASC";
        
        $stmt = $siakad_db->prepare($query);
        $stmt->execute($params);
        $raw_jadwal = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group consecutive same-subject periods
        $grouped = [];
        foreach ($raw_jadwal as $jdwl) {
            $mapel_id = $jdwl['mapel_id'];
            if (!isset($grouped[$mapel_id])) {
                $grouped[$mapel_id] = $jdwl;
            } else {
                if (strtotime($jdwl['jam_selesai']) > strtotime($grouped[$mapel_id]['jam_selesai'])) {
                    $grouped[$mapel_id]['jam_selesai'] = $jdwl['jam_selesai'];
                }
            }
        }
        $jadwal = array_values($grouped);
    }
} catch (Exception $e) {
    // silently fail or log error
}

$page_title = 'Jadwal KBM';
include 'header.php';
?>
<div class="header" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
    <div class="header-top">
        <div class="greeting">
            <p>Waka Kurikulum</p>
            <h1>Jadwal KBM Hari Ini</h1>
        </div>
        <div class="avatar" style="color: #3b82f6; background: white;" onclick="window.location.href='aplikasi_saya.php'">
            <i class="fa-solid fa-arrow-left"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10; padding: 20px; min-height: 70vh;">
        
        <!-- Filter Kelas -->
        <div style="margin-bottom: 20px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px;">
            <form method="GET" action="" style="display: flex; gap: 10px; align-items: center;">
                <select name="kelas_id" onchange="this.form.submit()" style="flex: 1; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 0.95rem; color: #334155; background: white; appearance: none; outline: none; cursor: pointer;">
                    <option value="0">Pilih Kelas...</option>
                    <?php foreach ($classes as $kls): ?>
                        <option value="<?= $kls['id'] ?>" <?= $filter_kelas == $kls['id'] ? 'selected' : '' ?>>
                            Kelas <?= htmlspecialchars($kls['nama_kelas']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div style="background: #3b82f6; color: white; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; border-radius: 8px; flex-shrink: 0;">
                    <i class="fa-solid fa-filter"></i>
                </div>
            </form>
        </div>

        <div style="margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
            <h3 style="margin: 0; color: #1e293b; font-size: 1.1rem;"><i class="fa-solid fa-calendar-day" style="color: #3b82f6; margin-right: 8px;"></i> Hari <?= $hari_ini ?></h3>
            <span style="background: #eff6ff; color: #3b82f6; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 700;"><?= count($jadwal) ?> Jadwal</span>
        </div>

        <?php if ($filter_kelas == 0): ?>
            <div style="text-align: center; padding: 40px 20px; color: #64748b;">
                <i class="fa-solid fa-hand-pointer" style="font-size: 3rem; margin-bottom: 15px; color: #cbd5e1;"></i>
                <h4 style="margin: 0 0 10px 0; font-size: 1.1rem;">Pilih Kelas</h4>
                <p style="margin: 0; font-size: 0.9rem;">Silakan pilih kelas melalui filter di atas untuk melihat jadwal KBM.</p>
            </div>
        <?php elseif (empty($jadwal)): ?>
            <div style="text-align: center; padding: 40px 20px; color: #64748b;">
                <i class="fa-solid fa-inbox" style="font-size: 3rem; margin-bottom: 15px; color: #cbd5e1;"></i>
                <h4 style="margin: 0 0 10px 0; font-size: 1.1rem;">Belum Ada Jadwal</h4>
                <p style="margin: 0; font-size: 0.9rem;">Tidak ada jadwal KBM yang ditemukan untuk kelas ini pada hari <?= $hari_ini ?>.</p>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <?php foreach ($jadwal as $j): ?>
                    <div style="background: #fff; padding: 15px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); position: relative; overflow: hidden;">
                        <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: #3b82f6;"></div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                            <div>
                                <div style="font-size: 0.75rem; font-weight: 800; color: #3b82f6; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">
                                    <i class="fa-regular fa-clock" style="margin-right: 4px;"></i> <?= substr($j['jam_mulai'], 0, 5) ?> - <?= substr($j['jam_selesai'], 0, 5) ?>
                                </div>
                                <h4 style="margin: 0; font-size: 1.05rem; font-weight: 700; color: #1e293b;">
                                    <?= htmlspecialchars($j['nama_mapel'] ?? 'Mapel Tidak Diketahui') ?>
                                </h4>
                            </div>
                            <div style="background: #f1f5f9; padding: 6px 12px; border-radius: 10px; font-weight: 800; font-size: 0.9rem; color: #0f172a; border: 1px solid #e2e8f0;">
                                Kelas <?= htmlspecialchars($j['nama_kelas'] ?? '-') ?>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: #64748b; font-weight: 500; border-top: 1px dashed #e2e8f0; padding-top: 10px; margin-top: 5px;">
                            <i class="fa-solid fa-chalkboard-user" style="color: #94a3b8;"></i>
                            <?= htmlspecialchars($j['nama_guru'] ?? 'Guru Belum Ditentukan') ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include 'footer_guru.php'; ?>
