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
    <title>Riwayat Transaksi - Bendahara</title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>">
    <!-- Font & Icons -->
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . date('YmdH')); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/multi_dashboard.css?v=' . date('YmdH')); ?>">
    <style>
        .trx-radio-btn {
            flex: 1;
            padding: 10px 14px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            cursor: pointer;
            text-align: center;
            font-weight: 700;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #f8fafc;
            color: #64748b;
        }
        .trx-radio-btn input[type="radio"] {
            display: none;
        }
        .trx-radio-btn.pemasukan.active {
            border-color: #10b981;
            background: #ecfdf5;
            color: #065f46;
        }
        .trx-radio-btn.pengeluaran.active {
            border-color: #ef4444;
            background: #fef2f2;
            color: #991b1b;
        }
        .action-icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .action-icon-btn.edit {
            background: #eff6ff;
            color: #2563eb;
            border-color: #bfdbfe;
        }
        .action-icon-btn.edit:hover {
            background: #2563eb;
            color: #ffffff;
        }
        .action-icon-btn.delete {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fecaca;
        }
        .action-icon-btn.delete:hover {
            background: #dc2626;
            color: #ffffff;
        }
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
                <div class="header-content-inner" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                    <div class="header-text-premium">
                        <div class="header-badge" style="background: rgba(255,255,255,0.2); color: #fff;">BENDAHARA</div>
                        <h1 style="color: #fff;"><i data-lucide="arrow-right-left" style="color: #fff;"></i> Riwayat Transaksi</h1>
                        <p style="color: rgba(255,255,255,0.85);">Pencatatan & riwayat transaksi dana masuk dan keluar pos komite</p>
                    </div>
                    <div class="header-actions-premium">
                        <button type="button" onclick="openTambahModal()" style="background: #ffffff; color: #047857; font-weight: 700; border: none; padding: 11px 22px; border-radius: 10px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(0,0,0,0.12); font-size: 0.92rem; transition: transform 0.15s ease;">
                            <i data-lucide="plus-circle" style="width: 18px; height: 18px; color: #10b981;"></i> Tambah Transaksi
                        </button>
                    </div>
                </div>
            </div>

            <!-- Notifikasi Pesan -->
            <?php if(isset($_GET['msg'])): ?>
                <?php if($_GET['msg'] == 'added'): ?>
                    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 12px 18px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 0.88rem;">
                        <i data-lucide="check-circle" style="width: 20px; height: 20px; color: #10b981;"></i> Transaksi baru berhasil ditambahkan dan disinkronkan ke Buku Kas!
                    </div>
                <?php elseif($_GET['msg'] == 'updated'): ?>
                    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 12px 18px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 0.88rem;">
                        <i data-lucide="check-circle" style="width: 20px; height: 20px; color: #10b981;"></i> Perubahan data transaksi berhasil disimpan!
                    </div>
                <?php elseif($_GET['msg'] == 'updated_siswa'): ?>
                    <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 12px 18px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 0.88rem;">
                        <i data-lucide="check-circle" style="width: 20px; height: 20px; color: #3b82f6;"></i> Pembayaran siswa berhasil diperbarui dan status tagihan disesuaikan!
                    </div>
                <?php elseif($_GET['msg'] == 'deleted'): ?>
                    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 12px 18px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 0.88rem;">
                        <i data-lucide="check-circle" style="width: 20px; height: 20px; color: #10b981;"></i> Transaksi berhasil dihapus!
                    </div>
                <?php elseif($_GET['msg'] == 'deleted_siswa'): ?>
                    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 12px 18px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 0.88rem;">
                        <i data-lucide="check-circle" style="width: 20px; height: 20px; color: #10b981;"></i> Pembayaran siswa berhasil dihapus dan status tagihan dikembalikan!
                    </div>
                <?php elseif($_GET['msg'] == 'unauthorized'): ?>
                    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 18px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 0.88rem;">
                        <i data-lucide="alert-triangle" style="width: 20px; height: 20px; color: #ef4444;"></i> Akses ditolak: Anda tidak memiliki wewenang untuk kategori transaksi ini.
                    </div>
                <?php elseif($_GET['msg'] == 'invalid_input'): ?>
                    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 18px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 0.88rem;">
                        <i data-lucide="alert-triangle" style="width: 20px; height: 20px; color: #ef4444;"></i> Mohon isi nominal dan keterangan transaksi dengan valid.
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Filter Panel -->
            <form method="GET" action="<?php echo base_url('guru/bendahara/transaksi'); ?>" style="display:flex; flex-wrap:wrap; gap:10px; align-items:center; background:#fff; padding:15px 20px; border-radius:12px; border:1px solid #e2e8f0; margin-bottom: 20px;">
                <input type="text" name="search" value="<?= htmlspecialchars(isset($search) ? $search : '') ?>" placeholder="Cari keterangan / nama siswa..." style="padding:10px; border-radius:8px; border:1px solid #cbd5e1; flex:1; min-width:200px; font-size: 0.85rem;">
                
                <select name="kategori" style="padding:10px; border-radius:8px; border:1px solid #cbd5e1; width:200px; font-size: 0.85rem;">
                    <option value="">Semua Kategori (Milik Anda)</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>" <?= (isset($selected_kat) && $selected_kat == $cat) ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                    <?php endforeach; ?>
                </select>
                
                <select name="tipe" style="padding:10px; border-radius:8px; border:1px solid #cbd5e1; width:140px; font-size: 0.85rem;">
                    <option value="">Semua Tipe</option>
                    <option value="Pemasukan" <?= (isset($tipe) && $tipe == 'Pemasukan') ? 'selected' : '' ?>>Pemasukan</option>
                    <option value="Pengeluaran" <?= (isset($tipe) && $tipe == 'Pengeluaran') ? 'selected' : '' ?>>Pengeluaran</option>
                </select>

                <input type="date" name="start_date" value="<?= htmlspecialchars(isset($start_date) ? $start_date : '') ?>" style="padding:9px; border-radius:8px; border:1px solid #cbd5e1; font-size: 0.85rem;">
                <span style="color:#64748b; font-weight:600; font-size: 0.85rem;">s.d</span>
                <input type="date" name="end_date" value="<?= htmlspecialchars(isset($end_date) ? $end_date : '') ?>" style="padding:9px; border-radius:8px; border:1px solid #cbd5e1; font-size: 0.85rem;">
                
                <button type="submit" class="btn" style="background:#10b981; color:#fff; padding:9px 18px; border:none; border-radius:8px; display:flex; align-items:center; gap:8px; font-weight:600; cursor:pointer; font-size: 0.85rem;">
                    <i data-lucide="search" style="width:16px;height:16px;"></i> Cari
                </button>
                <a href="<?php echo base_url('guru/bendahara/transaksi'); ?>" style="color:#64748b; font-weight:600; text-decoration:none; margin-left:10px; font-size: 0.85rem;">Reset</a>
            </form>

            <div class="z-panel mt-4">
                <div class="z-panel-head" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div class="z-panel-title" style="display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="table" style="width: 18px; height: 18px; color: #10b981;"></i> Daftar Transaksi
                    </div>
                    <button type="button" class="btn btn-primary" onclick="openTambahModal()" style="background: #10b981; color: #fff; padding: 7px 15px; border-radius: 8px; border: none; font-weight: 600; font-size: 0.82rem; display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
                        <i data-lucide="plus" style="width: 15px; height: 15px;"></i> Tambah Transaksi
                    </button>
                </div>
                <div class="z-table-wrap">
                    <table style="font-size: 0.85rem; width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 45px; text-align: center; font-size: 0.75rem; letter-spacing: 0.5px; white-space: nowrap;">NO</th>
                                <th style="font-size: 0.75rem; letter-spacing: 0.5px; white-space: nowrap;">TANGGAL</th>
                                <th style="font-size: 0.75rem; letter-spacing: 0.5px; min-width: 220px;">KETERANGAN</th>
                                <th style="font-size: 0.75rem; letter-spacing: 0.5px; white-space: nowrap;">NAMA SISWA</th>
                                <th style="font-size: 0.75rem; letter-spacing: 0.5px; white-space: nowrap;">SUMBER / KATEGORI</th>
                                <th style="text-align: right; font-size: 0.75rem; letter-spacing: 0.5px; white-space: nowrap;">JUMLAH</th>
                                <th style="text-align: center; font-size: 0.75rem; letter-spacing: 0.5px; white-space: nowrap;">TIPE</th>
                                <th style="text-align: center; font-size: 0.75rem; letter-spacing: 0.5px; white-space: nowrap; width: 140px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach($transaksi as $t): ?>
                            <tr>
                                <td style="text-align: center; color: #64748b; white-space: nowrap;"><?= $no++ ?></td>
                                <td style="white-space: nowrap; color: #475569;"><?= date('d/m/Y H:i', strtotime($t['tanggal'])) ?></td>
                                <td style="font-weight: 500; color: #1e293b;">
                                    <div><?= htmlspecialchars($t['keterangan']) ?></div>
                                    <?php if(!empty($t['bukti'])): ?>
                                        <a href="<?php echo base_url('uploads/finance/' . htmlspecialchars($t['bukti'])); ?>" target="_blank" style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.73rem; color: #0284c7; text-decoration: none; margin-top: 3px; font-weight: 600;">
                                            <i data-lucide="paperclip" style="width: 12px; height: 12px;"></i> Lihat Bukti Struk
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td style="font-weight: 600; color: #3b82f6; white-space: nowrap;"><?= !empty($t['nama_siswa']) ? htmlspecialchars($t['nama_siswa']) : '<span style="color:#cbd5e1;">-</span>' ?></td>
                                <td style="white-space: nowrap;">
                                    <span style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;"><?= htmlspecialchars($t['sumber']) ?></span>
                                </td>
                                <td style="text-align: right; font-weight: 700; color: #0f172a; white-space: nowrap;">Rp <?= number_format($t['jumlah'], 0, ',', '.') ?></td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <?php if($t['tipe'] == 'Pemasukan'): ?>
                                        <span class="pill pill-green" style="font-size: 0.72rem; padding: 4px 10px;"><i data-lucide="arrow-down-to-line" style="width:11px; height:11px;"></i> Pemasukan</span>
                                    <?php else: ?>
                                        <span class="pill pill-red" style="font-size: 0.72rem; padding: 4px 10px;"><i data-lucide="arrow-up-to-line" style="width:11px; height:11px;"></i> Pengeluaran</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                        <?php if(isset($t['sumber_transaksi']) && $t['sumber_transaksi'] === 'siswa'): ?>
                                            <!-- Transaksi dari Pembayaran Siswa -->
                                            <button type="button" class="action-icon-btn edit" onclick="openEditSiswa(<?= htmlspecialchars(json_encode($t), ENT_QUOTES, 'UTF-8') ?>)" title="Edit Pembayaran Siswa">
                                                <i data-lucide="edit-3" style="width:13px; height:13px;"></i> Edit
                                            </button>
                                            <button type="button" class="action-icon-btn delete" onclick="confirmDeleteSiswa(<?= $t['id'] ?>, '<?= htmlspecialchars(addslashes($t['nama_siswa']), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($t['sumber']), ENT_QUOTES) ?>', <?= $t['jumlah'] ?>)" title="Hapus Pembayaran Siswa">
                                                <i data-lucide="trash-2" style="width:13px; height:13px;"></i> Hapus
                                            </button>
                                        <?php else: ?>
                                            <!-- Transaksi Manual Komite / Kasir -->
                                            <button type="button" class="action-icon-btn edit" onclick="openEditManual(<?= htmlspecialchars(json_encode($t), ENT_QUOTES, 'UTF-8') ?>)" title="Edit Transaksi">
                                                <i data-lucide="edit-3" style="width:13px; height:13px;"></i> Edit
                                            </button>
                                            <button type="button" class="action-icon-btn delete" onclick="confirmDeleteManual(<?= $t['id'] ?>, '<?= htmlspecialchars(addslashes($t['keterangan']), ENT_QUOTES) ?>', <?= $t['jumlah'] ?>)" title="Hapus Transaksi">
                                                <i data-lucide="trash-2" style="width:13px; height:13px;"></i> Hapus
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($transaksi)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 35px 20px; color: #94a3b8;">
                                    <i data-lucide="inbox" style="width:42px; height:42px; margin-bottom:10px; opacity:0.5;"></i><br>
                                    Belum ada transaksi di kategori Anda.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div> <!-- z-scroll -->
    </div> <!-- z-main -->
</div>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- MODAL 1: TAMBAH & EDIT TRANSAKSI MANUAL                         -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<div class="z-modal-ov" id="modalManualTrx" onclick="if(event.target===this)closeModalManual()">
    <div class="z-modal" style="max-width: 540px;">
        <div class="z-modal-head">
            <div class="z-modal-title" id="manualModalTitle">
                <i data-lucide="plus-circle" style="color: #10b981;"></i> Tambah Transaksi
            </div>
            <button type="button" class="z-modal-close" onclick="closeModalManual()">
                <i data-lucide="x"></i>
            </button>
        </div>
        <form action="<?php echo base_url('guru/bendahara/transaksi/save'); ?>" method="POST" enctype="multipart/form-data" id="formManualTrx">
            <input type="hidden" name="id" id="manual_id" value="0">
            <div class="z-modal-body" style="padding: 1.5rem;">
                
                <!-- Pilihan Tipe Transaksi: Pemasukan / Pengeluaran -->
                <div class="z-form-group">
                    <label class="z-label">Tipe Transaksi</label>
                    <div style="display: flex; gap: 12px;">
                        <label class="trx-radio-btn pemasukan active" id="btnRadioPemasukan" onclick="setJenisTrx('Pemasukan')">
                            <input type="radio" name="jenis" id="radio_pemasukan" value="Pemasukan" checked>
                            <i data-lucide="arrow-down-to-line" style="width:16px;height:16px;"></i> Pemasukan (Kas Masuk)
                        </label>
                        <label class="trx-radio-btn pengeluaran" id="btnRadioPengeluaran" onclick="setJenisTrx('Pengeluaran')">
                            <input type="radio" name="jenis" id="radio_pengeluaran" value="Pengeluaran">
                            <i data-lucide="arrow-up-to-line" style="width:16px;height:16px;"></i> Pengeluaran (Kas Keluar)
                        </label>
                    </div>
                </div>

                <div class="z-grid2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 1rem;">
                    <div class="z-form-group" style="margin-bottom: 0;">
                        <label class="z-label">Tanggal Transaksi</label>
                        <input class="z-input" type="date" name="tanggal_transaksi" id="manual_tanggal" required style="width: 100%;" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="z-form-group" style="margin-bottom: 0;">
                        <label class="z-label">Kategori / Pos Kas</label>
                        <select class="z-input" name="kategori" id="manual_kategori" required style="width: 100%;">
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="z-form-group">
                    <label class="z-label">Jumlah Nominal (Rp)</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-weight: 700; color: #64748b; font-size: 0.9rem;">Rp</span>
                        <input class="z-input" type="text" name="jumlah" id="manual_jumlah" placeholder="0" required style="width: 100%; padding-left: 45px; font-size: 1rem; font-weight: 700; color: #0f172a;" oninput="formatRupiah(this)">
                    </div>
                </div>

                <div class="z-form-group">
                    <label class="z-label">Keterangan Transaksi</label>
                    <textarea class="z-input" name="keterangan" id="manual_keterangan" rows="2" placeholder="Contoh: Infaq donatur, Pembelian spidol & ATK..." required style="width: 100%; resize: vertical;"></textarea>
                </div>

                <div class="z-form-group" style="margin-bottom: 0;">
                    <label class="z-label">Upload Bukti Struk / Nota (Opsional)</label>
                    <input class="z-input" type="file" name="bukti" id="manual_bukti" accept="image/*,application/pdf" style="width: 100%; padding: 6px;">
                    <div id="manual_bukti_preview" style="margin-top: 6px; font-size: 0.8rem;"></div>
                </div>

            </div>
            <div class="z-modal-foot">
                <button type="button" class="btn btn-ghost" onclick="closeModalManual()">Batal</button>
                <button type="submit" class="btn btn-primary" style="background: #10b981; color: #fff; padding: 9px 20px;">
                    <i data-lucide="check" style="width:16px;height:16px;"></i> <span id="manualSubmitText">Simpan Transaksi</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- MODAL 2: EDIT PEMBAYARAN SISWA                                  -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<div class="z-modal-ov" id="modalSiswaTrx" onclick="if(event.target===this)closeModalSiswa()">
    <div class="z-modal" style="max-width: 480px;">
        <div class="z-modal-head">
            <div class="z-modal-title">
                <i data-lucide="user-check" style="color: #3b82f6;"></i> Edit Pembayaran Siswa
            </div>
            <button type="button" class="z-modal-close" onclick="closeModalSiswa()">
                <i data-lucide="x"></i>
            </button>
        </div>
        <form action="<?php echo base_url('guru/bendahara/transaksi/update-pembayaran'); ?>" method="POST" id="formSiswaTrx">
            <input type="hidden" name="id" id="siswa_id" value="0">
            <div class="z-modal-body" style="padding: 1.5rem;">
                
                <!-- Info Siswa Card -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px 16px; border-radius: 10px; margin-bottom: 1.25rem;">
                    <div style="font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Nama Siswa</div>
                    <div id="siswa_info_nama" style="font-size: 1rem; font-weight: 700; color: #1e293b; margin-top: 2px;">-</div>
                    <div id="siswa_info_sumber" style="font-size: 0.8rem; color: #3b82f6; font-weight: 600; margin-top: 4px;">-</div>
                </div>

                <div class="z-form-group">
                    <label class="z-label">Tanggal Pembayaran</label>
                    <input class="z-input" type="date" name="tanggal_bayar" id="siswa_tanggal" required style="width: 100%;">
                </div>

                <div class="z-form-group">
                    <label class="z-label">Nominal Terbayar (Rp)</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-weight: 700; color: #64748b; font-size: 0.9rem;">Rp</span>
                        <input class="z-input" type="text" name="jumlah" id="siswa_jumlah" placeholder="0" required style="width: 100%; padding-left: 45px; font-size: 1rem; font-weight: 700; color: #0f172a;" oninput="formatRupiah(this)">
                    </div>
                </div>

                <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 10px 14px; border-radius: 8px; font-size: 0.78rem; display: flex; align-items: flex-start; gap: 8px;">
                    <i data-lucide="info" style="width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px;"></i>
                    <span>Mengubah nominal bayar akan secara otomatis memperbarui status tagihan siswa di sistem (Lunas / Menyicil / Belum Bayar).</span>
                </div>

            </div>
            <div class="z-modal-foot">
                <button type="button" class="btn btn-ghost" onclick="closeModalSiswa()">Batal</button>
                <button type="submit" class="btn btn-primary" style="background: #3b82f6; color: #fff; padding: 9px 20px;">
                    <i data-lucide="check" style="width:16px;height:16px;"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- MODAL 3: KONFIRMASI HAPUS TRANSAKSI                             -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<div class="z-modal-ov" id="modalDeleteTrx" onclick="if(event.target===this)closeModalDelete()">
    <div class="z-modal" style="max-width: 440px; border-radius: 18px;">
        <div class="z-modal-head" style="background: #fef2f2; border-bottom: 1px solid #fee2e2;">
            <div class="z-modal-title" style="color: #ef4444;">
                <i data-lucide="alert-triangle" style="color: #ef4444;"></i> Konfirmasi Hapus
            </div>
            <button type="button" class="z-modal-close" onclick="closeModalDelete()">
                <i data-lucide="x"></i>
            </button>
        </div>
        <div class="z-modal-body" style="padding: 1.5rem;">
            <p id="deleteModalMessage" style="font-size: 0.92rem; color: #1e293b; line-height: 1.5; margin-bottom: 10px;">
                Apakah Anda yakin ingin menghapus transaksi ini?
            </p>
            <div id="deleteModalDetail" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; font-size: 0.82rem; color: #475569; margin-bottom: 12px;"></div>
            <div id="deleteModalWarning" style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 10px 14px; border-radius: 8px; font-size: 0.78rem; display: flex; align-items: flex-start; gap: 8px;">
                <i data-lucide="alert-circle" style="width: 15px; height: 15px; flex-shrink: 0; margin-top: 1px;"></i>
                <span id="deleteModalWarningText">Data transaksi yang dihapus tidak dapat dipulihkan.</span>
            </div>
        </div>
        <div class="z-modal-foot">
            <button type="button" class="btn btn-ghost" onclick="closeModalDelete()">Batal</button>
            <a href="#" id="deleteConfirmBtn" class="btn btn-danger" style="background: #ef4444; color: #fff; border: none; padding: 9px 18px; border-radius: 8px; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i> Hapus Sekarang
            </a>
        </div>
    </div>
</div>

<script>
    // Inisialisasi ikon Lucide
    lucide.createIcons();

    const BASE_URL = '<?php echo rtrim(base_url(), "/"); ?>';

    // Format input rupiah
    function formatRupiah(input) {
        let val = input.value.replace(/[^0-9]/g, '');
        if (val === '') {
            input.value = '';
            return;
        }
        input.value = new Intl.NumberFormat('id-ID').format(val);
    }

    // Toggle jenis transaksi (Pemasukan / Pengeluaran)
    function setJenisTrx(jenis) {
        const btnIn = document.getElementById('btnRadioPemasukan');
        const btnOut = document.getElementById('btnRadioPengeluaran');
        const radioIn = document.getElementById('radio_pemasukan');
        const radioOut = document.getElementById('radio_pengeluaran');

        if (jenis === 'Pemasukan') {
            btnIn.classList.add('active');
            btnOut.classList.remove('active');
            radioIn.checked = true;
        } else {
            btnOut.classList.add('active');
            btnIn.classList.remove('active');
            radioOut.checked = true;
        }
    }

    // ─── Modal Manual ───
    function openTambahModal() {
        document.getElementById('manualModalTitle').innerHTML = '<i data-lucide="plus-circle" style="color: #10b981;"></i> Tambah Transaksi';
        document.getElementById('manualSubmitText').innerText = 'Simpan Transaksi';
        document.getElementById('manual_id').value = '0';
        document.getElementById('manual_keterangan').value = '';
        document.getElementById('manual_jumlah').value = '';
        document.getElementById('manual_tanggal').value = '<?= date("Y-m-d") ?>';
        document.getElementById('manual_bukti').value = '';
        document.getElementById('manual_bukti_preview').innerHTML = '';
        setJenisTrx('Pemasukan');

        document.getElementById('modalManualTrx').classList.add('open');
        lucide.createIcons();
    }

    function openEditManual(data) {
        document.getElementById('manualModalTitle').innerHTML = '<i data-lucide="edit-3" style="color: #2563eb;"></i> Edit Transaksi';
        document.getElementById('manualSubmitText').innerText = 'Simpan Perubahan';
        document.getElementById('manual_id').value = data.id;
        document.getElementById('manual_keterangan').value = data.keterangan || '';
        document.getElementById('manual_jumlah').value = new Intl.NumberFormat('id-ID').format(data.jumlah);
        
        // Ambil format YYYY-MM-DD dari tanggal
        let tgl = data.tanggal ? data.tanggal.substring(0, 10) : '<?= date("Y-m-d") ?>';
        document.getElementById('manual_tanggal').value = tgl;

        // Kategori
        if (data.sumber) {
            document.getElementById('manual_kategori').value = data.sumber;
        }

        // Tipe
        setJenisTrx(data.tipe === 'Pengeluaran' ? 'Pengeluaran' : 'Pemasukan');

        // Bukti Struk preview
        const previewDiv = document.getElementById('manual_bukti_preview');
        if (data.bukti) {
            previewDiv.innerHTML = '<span style="color:#64748b;">File tersimpan: </span><a href="' + BASE_URL + '/uploads/finance/' + data.bukti + '" target="_blank" style="color:#0284c7;font-weight:600;text-decoration:underline;">Lihat Bukti Saat Ini</a> <span style="color:#94a3b8;">(Pilih file baru jika ingin mengganti)</span>';
        } else {
            previewDiv.innerHTML = '';
        }

        document.getElementById('modalManualTrx').classList.add('open');
        lucide.createIcons();
    }

    function closeModalManual() {
        document.getElementById('modalManualTrx').classList.remove('open');
    }

    // ─── Modal Siswa ───
    function openEditSiswa(data) {
        document.getElementById('siswa_id').value = data.id;
        document.getElementById('siswa_info_nama').innerText = data.nama_siswa || 'Siswa';
        document.getElementById('siswa_info_sumber').innerText = 'Tagihan: ' + (data.sumber || '-');
        
        let tgl = data.tanggal ? data.tanggal.substring(0, 10) : '<?= date("Y-m-d") ?>';
        document.getElementById('siswa_tanggal').value = tgl;
        document.getElementById('siswa_jumlah').value = new Intl.NumberFormat('id-ID').format(data.jumlah);

        document.getElementById('modalSiswaTrx').classList.add('open');
        lucide.createIcons();
    }

    function closeModalSiswa() {
        document.getElementById('modalSiswaTrx').classList.remove('open');
    }

    // ─── Modal Delete ───
    function confirmDeleteManual(id, ket, jumlah) {
        document.getElementById('deleteModalMessage').innerText = 'Apakah Anda yakin ingin menghapus transaksi manual berikut?';
        document.getElementById('deleteModalDetail').innerHTML = '<strong>' + ket + '</strong><br>Nominal: <strong style="color:#0f172a;">Rp ' + new Intl.NumberFormat('id-ID').format(jumlah) + '</strong>';
        document.getElementById('deleteModalWarningText').innerText = 'Data transaksi dan sinkronisasi kas akan dihapus secara permanen.';
        document.getElementById('deleteConfirmBtn').href = BASE_URL + '/guru/bendahara/transaksi/delete/' + id;

        document.getElementById('modalDeleteTrx').classList.add('open');
        lucide.createIcons();
    }

    function confirmDeleteSiswa(id, nama, sumber, jumlah) {
        document.getElementById('deleteModalMessage').innerText = 'Apakah Anda yakin ingin menghapus data pembayaran siswa berikut?';
        document.getElementById('deleteModalDetail').innerHTML = 'Siswa: <strong>' + nama + '</strong><br>Jenis: <strong>' + sumber + '</strong><br>Nominal: <strong style="color:#0f172a;">Rp ' + new Intl.NumberFormat('id-ID').format(jumlah) + '</strong>';
        document.getElementById('deleteModalWarningText').innerText = 'PERHATIAN: Status tagihan siswa ini akan otomatis dikembalikan ke Belum Lunas / Menyicil!';
        document.getElementById('deleteConfirmBtn').href = BASE_URL + '/guru/bendahara/transaksi/delete-pembayaran/' + id;

        document.getElementById('modalDeleteTrx').classList.add('open');
        lucide.createIcons();
    }

    function closeModalDelete() {
        document.getElementById('modalDeleteTrx').classList.remove('open');
    }
</script>
</body>
</html>

