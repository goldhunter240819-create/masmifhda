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

// Handle Add Anggota
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_anggota') {
    $siswa_ids = $_POST['siswa_ids'] ?? [];
    if (!empty($siswa_ids)) {
        $ins = $siakad_db->prepare("INSERT IGNORE INTO ekstra_anggota (nama_ekstra, siswa_id, tahun_ajaran) VALUES (?, ?, ?)");
        $count = 0;
        foreach($siswa_ids as $s_id) {
            $ins->execute([$nama_ekstra, $s_id, $tahun_ajaran]);
            if ($ins->rowCount() > 0) $count++;
        }
        $msg = "Berhasil menambahkan $count anggota baru!";
    }
}

// Handle Delete Anggota
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete_anggota') {
    $id_hapus = $_POST['id_hapus'] ?? 0;
    if ($id_hapus > 0) {
        $del = $siakad_db->prepare("DELETE FROM ekstra_anggota WHERE id = ? AND nama_ekstra = ?");
        $del->execute([$id_hapus, $nama_ekstra]);
        $msg = "Anggota berhasil dihapus.";
    }
}

// Ambil anggota terdaftar
$anggotaList = $siakad_db->query("SELECT ea.id as ea_id, s.id, s.nama, s.nis, k.nama_kelas 
                                FROM ekstra_anggota ea 
                                JOIN aula_db.siswa s ON ea.siswa_id = s.id 
                                LEFT JOIN aula_db.kelas k ON s.kelas_id = k.id 
                                WHERE ea.nama_ekstra = ".$siakad_db->quote($nama_ekstra)." 
                                AND ea.tahun_ajaran = ".$siakad_db->quote($tahun_ajaran)."
                                ORDER BY k.nama_kelas ASC, s.nama ASC")->fetchAll(PDO::FETCH_ASSOC);

$siswaIdsJoined = array_column($anggotaList, 'id');
$notIn = count($siswaIdsJoined) > 0 ? "AND s.id NOT IN (" . implode(',', $siswaIdsJoined) . ")" : "";
$siswaTersedia = $siakad_db->query("SELECT s.id, s.nama, k.nama_kelas 
                                    FROM aula_db.siswa s 
                                    LEFT JOIN aula_db.kelas k ON s.kelas_id = k.id 
                                    WHERE s.status IN ('Aktif') $notIn 
                                    ORDER BY k.nama_kelas ASC, s.nama ASC")->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Anggota ' . htmlspecialchars($nama_ekstra);
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%); padding-top: 3.5rem;">
    <div class="header-top">
        <div class="greeting">
            <p>Anggota Ekstrakurikuler</p>
            <h1><?= htmlspecialchars($nama_ekstra) ?></h1>
        </div>
        <div class="avatar" style="color: #10b981; background: white;">
            <i class="fa-solid fa-users"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <?php if (isset($msg)): ?>
        <div style="background: #dcfce7; color: #16a34a; padding: 10px; border-radius: 8px; font-size: 0.8rem; margin-top: -1.5rem; margin-bottom: 15px; border: 1px solid #bbf7d0; text-align: center; position: relative; z-index: 20;">
            <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <div class="data-card" style="margin-top: <?= isset($msg) ? '0' : '-2.5rem' ?>; z-index: 10;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 class="section-title" style="margin: 0;"><i class="fa-solid fa-user-plus" style="color: #10b981; margin-right: 5px;"></i> Tambah Anggota</h3>
            <button onclick="toggleForm()" style="background: #e2e8f0; border: none; border-radius: 8px; padding: 6px 12px; font-size: 0.8rem; font-weight: 700; color: #475569; cursor: pointer;"><i class="fa-solid fa-chevron-down" id="toggleIcon"></i> Buka Form</button>
        </div>
        
        <div id="formTambah" style="display: none;">
            <form method="POST">
                <input type="hidden" name="action" value="add_anggota">
            <div style="margin-bottom: 10px;">
                <input type="text" id="cari_siswa" placeholder="Cari nama siswa..." style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.85rem;" onkeyup="filterSiswa()">
            </div>
            <div id="list_siswa" style="max-height: 250px; overflow-y: auto; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; background: white;">
                <?php if (empty($siswaTersedia)): ?>
                    <div style="font-size: 0.8rem; color: #64748b; text-align: center;">Tidak ada siswa tersisa untuk ditambahkan.</div>
                <?php else: ?>
                    <?php 
                    $current_kelas = '';
                    foreach ($siswaTersedia as $st): 
                        if ($current_kelas != $st['nama_kelas']) {
                            $current_kelas = $st['nama_kelas'];
                            echo "<div class='kelas-header' style='font-weight: bold; font-size: 0.8rem; background: #f1f5f9; padding: 5px 8px; margin-top: 5px; border-radius: 4px; color: #475569;'>Kelas " . htmlspecialchars($current_kelas) . "</div>";
                        }
                    ?>
                        <label class="siswa-item" style="display: flex; align-items: center; gap: 10px; padding: 8px 5px; border-bottom: 1px solid #f1f5f9; cursor: pointer;">
                            <input type="checkbox" name="siswa_ids[]" value="<?= $st['id'] ?>" style="width: 18px; height: 18px; accent-color: #10b981;">
                            <span class="siswa-nama" style="font-size: 0.85rem; color: #1e293b;"><?= htmlspecialchars($st['nama']) ?></span>
                        </label>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="submit" style="background: #10b981; color: white; border: none; border-radius: 8px; width: 100%; padding: 12px; margin-top: 10px; font-weight: 700; cursor: pointer;">
                <i class="fa-solid fa-plus"></i> Tambah Anggota Terpilih
            </button>
        </form>
        </div>
    </div>

    <script>
    function toggleForm() {
        let form = document.getElementById('formTambah');
        let icon = document.getElementById('toggleIcon');
        if (form.style.display === 'none') {
            form.style.display = 'block';
            icon.className = 'fa-solid fa-chevron-up';
        } else {
            form.style.display = 'none';
            icon.className = 'fa-solid fa-chevron-down';
        }
    }

    function filterSiswa() {
        let input = document.getElementById('cari_siswa').value.toLowerCase();
        let items = document.querySelectorAll('.siswa-item');
        items.forEach(item => {
            let nama = item.querySelector('.siswa-nama').innerText.toLowerCase();
            if (nama.indexOf(input) > -1) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }
    </script>

    <div class="data-card" style="margin-top: 15px;">
        <h3 class="section-title" style="margin-bottom: 15px;"><i class="fa-solid fa-users" style="color: #10b981; margin-right: 5px;"></i> Daftar Anggota (<?= count($anggotaList) ?>)</h3>
        
        <?php if (empty($anggotaList)): ?>
            <div style="text-align: center; padding: 20px; color: #64748b; font-size: 0.85rem;">Belum ada anggota yang terdaftar.</div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <?php foreach ($anggotaList as $idx => $anggota): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;">
                        <div style="display: flex; gap: 12px; align-items: center;">
                            <div style="width: 35px; height: 35px; background: #e0f2fe; color: #0284c7; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem;">
                                <?= $idx + 1 ?>
                            </div>
                            <div>
                                <div style="font-weight: 700; font-size: 0.85rem; color: #1e293b; line-height: 1.2;"><?= htmlspecialchars($anggota['nama']) ?></div>
                                <div style="font-size: 0.7rem; color: #64748b; margin-top: 3px;"><i class="fa-solid fa-door-open" style="margin-right: 3px;"></i> Kelas <?= htmlspecialchars($anggota['nama_kelas']) ?> &nbsp;|&nbsp; NIS: <?= htmlspecialchars($anggota['nis']) ?></div>
                            </div>
                        </div>
                        <form method="POST" onsubmit="return confirm('Hapus anggota ini?');">
                            <input type="hidden" name="action" value="delete_anggota">
                            <input type="hidden" name="id_hapus" value="<?= $anggota['ea_id'] ?>">
                            <button type="submit" style="background: none; border: none; color: #ef4444; font-size: 1.1rem; cursor: pointer; padding: 5px;"><i class="fa-solid fa-trash-can"></i></button>
                        </form>
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
