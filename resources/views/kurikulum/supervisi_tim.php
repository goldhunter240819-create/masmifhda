<?php
use App\Core\Branding;
$brand = Branding::get();
$sekolah_nama = !empty($institusi['nama']) ? $institusi['nama'] : 'MADRASAH ALIYAH MIFTAHUL HUDA';
$ta = $config['tahun_ajaran'] ?? '2026/2027';
$sem = $config['semester'] ?? 'Ganjil';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title = 'Tim Supervisi & Pengaturan SK'; include __DIR__ . '/../admin/_zamrud_head.php'; ?>
    <style>
        .setting-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.03);
            transition: all 0.2s ease;
        }
        .setting-card:hover {
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
            border-color: #cbd5e1;
        }
        .card-header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }
        .form-grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 18px;
        }
        .form-group label {
            display: block;
            font-size: 0.82rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 6px;
        }
        .form-control-custom {
            width: 100%;
            padding: 9px 13px;
            font-size: 0.88rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            box-sizing: border-box;
            font-family: inherit;
            color: #0f172a;
            background: #ffffff;
            transition: border-color 0.2s;
        }
        .form-control-custom:focus {
            outline: none;
            border-color: #059669;
            box-shadow: 0 0 0 3px rgba(5,150,105,0.12);
        }
        .badge-role {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.74rem;
            font-weight: 700;
        }
        .role-kamad { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .role-ketua { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
        .role-sekretaris { background: #faf5ff; color: #6b21a8; border: 1px solid #e9d5ff; }
        .role-observer { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .btn-role-select {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 8px 4px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #f8fafc;
            cursor: pointer;
            font-size: 0.78rem;
            color: #1e293b;
            transition: all 0.2s;
        }
        .btn-role-select:hover, .btn-role-select.active {
            background: #eff6ff;
            border-color: #3b82f6;
            color: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(59,130,246,0.15);
        }
        .modal-backdrop {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.6);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(3px);
        }
    </style>
</head>
<body>
<?php $page_badge = 'Kurikulum'; ?>
<div class="z-wrap">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../siakad/_siakad_header.php'; ?>
        
        <div style="padding: 24px;">

            <!-- Breadcrumb Navigation -->
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
                <div style="display:flex; align-items:center; gap:8px; font-size:0.86rem; color:#64748b; font-weight:600;">
                    <a href="/kurikulum/supervisi" style="color:#059669; text-decoration:none;">Supervisi</a>
                    <span>/</span>
                    <span style="color:#0f172a; font-weight:700;">Tim Supervisi & SK</span>
                </div>
                <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                    <a href="/kurikulum/supervisi/tim/sk-cetak" target="_blank" class="btn-act btn-white" style="border:1px solid #cbd5e1; padding:8px 14px; font-size:0.82rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                        <i data-lucide="printer" style="width:15px;height:15px; color:#059669;"></i> Cetak Naskah SK Tim
                    </a>
                    <a href="/kurikulum/supervisi/laporan-hasil" class="btn-act btn-white" style="border:1px solid #cbd5e1; padding:8px 14px; font-size:0.82rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                        <i data-lucide="file-bar-chart-2" style="width:15px;height:15px; color:#2563eb;"></i> Laporan Hasil
                    </a>
                    <a href="/kurikulum/supervisi/laporan-tindak-lanjut" class="btn-act btn-white" style="border:1px solid #cbd5e1; padding:8px 14px; font-size:0.82rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                        <i data-lucide="file-check" style="width:15px;height:15px; color:#10b981;"></i> Laporan RTL
                    </a>
                </div>
            </div>

            <?php if(isset($_SESSION['flash_msg'])): ?>
            <div style="background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; padding:12px 18px; border-radius:10px; margin-bottom:20px; font-size:0.88rem; font-weight:600; display:flex; align-items:center; gap:8px;">
                <i data-lucide="check-circle" style="width:18px;height:18px;color:#059669;"></i>
                <?= htmlspecialchars($_SESSION['flash_msg']) ?>
            </div>
            <?php unset($_SESSION['flash_msg']); endif; ?>

            <!-- Zamrud Banner -->
            <div style="background:linear-gradient(135deg, #064e3b 0%, #047857 50%, #065f46 100%); color:#ffffff; border-radius:16px; padding:22px 26px; margin-bottom:24px; box-shadow:0 8px 24px -4px rgba(6,78,59,0.25); border:1px solid rgba(255,255,255,0.15);">
                <div style="display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,0.18); padding:3px 10px; border-radius:999px; font-size:0.72rem; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;">
                    <i data-lucide="shield-check" style="width:12px;height:12px;"></i> Surat Keputusan (SK) & Dewan Observer &bull; PKKM Komponen 4.2 & 4.3
                </div>
                <h1 style="font-size:1.45rem; font-weight:800; margin:0 0 6px 0; color:#fff; display:flex; align-items:center; gap:10px;">
                    <i data-lucide="user-check" style="width:26px;height:26px;color:#34d399;"></i>
                    Pengaturan Tim Supervisi Pembelajaran & Legalitas SK
                </h1>
                <p style="margin:0; font-size:0.88rem; color:rgba(255,255,255,0.9); max-width:850px; line-height:1.5;">
                    Atur susunan dewan penilai (supervisor), wewenang observer, serta legalitas SK penetapan tahun ajaran berjalan. Data nama pengawas, kepala madrasah, dan dewan observer yang disimpan di sini akan otomatis tertaut ke <strong>Laporan Hasil Supervisi</strong>, <strong>Laporan RTL</strong>, dan <strong>Buku Program Supervisi</strong>.
                </p>
            </div>

            <!-- CARD 1: PENGATURAN SK & PERSONEL UTAMA -->
            <div class="setting-card">
                <div class="card-header-flex">
                    <div>
                        <h2 style="font-size:1.1rem; font-weight:800; color:#0f172a; margin:0 0 4px 0; display:flex; align-items:center; gap:8px;">
                            <i data-lucide="file-signature" style="width:20px;height:20px;color:#059669;"></i>
                            Legalitas Surat Keputusan (SK) & Supervisor Utama
                        </h2>
                        <span style="font-size:0.8rem; color:#64748b;">Pengaturan nomor SK, pengesahan, dan pejabat pembina Kemenag yang tampil pada dokumen laporan.</span>
                    </div>
                </div>

                <form action="/kurikulum/supervisi/tim/update-config" method="POST">
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Nomor SK Penetapan Tim Supervisi</label>
                            <input type="text" name="nomor_sk" class="form-control-custom" value="<?= htmlspecialchars($config['nomor_sk'] ?? 'MA.10.04/PP.01.1/045/SK-TIM-SPV/VII/2026') ?>" required>
                            <span style="font-size:0.75rem; color:#94a3b8;">Format: No/Kode Klasifikasi/Bulan/Tahun penetapan madrasah</span>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Ditetapkan SK</label>
                            <input type="date" name="tanggal_sk" class="form-control-custom" value="<?= htmlspecialchars($config['tanggal_sk'] ?? '2026-07-15') ?>" required>
                            <span style="font-size:0.75rem; color:#94a3b8;">Tanggal penandatanganan SK awal tahun ajaran</span>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Pengesahan Dokumen Laporan & RTL</label>
                            <input type="date" name="tanggal_laporan" class="form-control-custom" value="<?= htmlspecialchars($config['tanggal_laporan'] ?? '2026-11-11') ?>" required>
                            <span style="font-size:0.75rem; color:#94a3b8;">Tanggal penandatanganan pada Lembar Pengesahan Laporan Supervisi & Dokumen RTL</span>
                        </div>
                        <div class="form-group">
                            <label>Tahun Pelajaran & Semester</label>
                            <div style="display:flex; gap:10px;">
                                <input type="text" name="tahun_ajaran" class="form-control-custom" value="<?= htmlspecialchars($config['tahun_ajaran'] ?? '2026/2027') ?>" style="flex:1;">
                                <select name="semester" class="form-control-custom" style="flex:1;">
                                    <option value="Ganjil" <?= ($sem === 'Ganjil') ? 'selected' : '' ?>>Ganjil</option>
                                    <option value="Genap" <?= ($sem === 'Genap') ? 'selected' : '' ?>>Genap</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Nama Kepala Madrasah (Supervisor Utama)</label>
                            <input type="text" name="kamad_nama" class="form-control-custom" value="<?= htmlspecialchars($config['kamad_nama'] ?? 'Drs. Hamim Hasyim') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>NIP Kepala Madrasah</label>
                            <input type="text" name="kamad_nip" class="form-control-custom" value="<?= htmlspecialchars($config['kamad_nip'] ?? '-') ?>">
                        </div>
                        <div class="form-group">
                            <label>Jabatan Kepala Madrasah</label>
                            <input type="text" name="kamad_jabatan" class="form-control-custom" value="<?= htmlspecialchars($config['kamad_jabatan'] ?? 'Kepala Madrasah / Supervisor Utama') ?>">
                        </div>
                        <div class="form-group">
                            <label>Nama Pengawas Pembina Kemenag</label>
                            <input type="text" name="pengawas_nama" class="form-control-custom" value="<?= htmlspecialchars($config['pengawas_nama'] ?? 'Drs. H. M. YUSUF, M.Pd.I') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>NIP Pengawas Pembina</label>
                            <input type="text" name="pengawas_nip" class="form-control-custom" value="<?= htmlspecialchars($config['pengawas_nip'] ?? '196805141994031002') ?>">
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label>Jabatan / Instansi Pengawas Pembina</label>
                            <input type="text" name="pengawas_jabatan" class="form-control-custom" value="<?= htmlspecialchars($config['pengawas_jabatan'] ?? 'Pengawas Madrasah Ahli Madya Kemenag Kab. Lampung Barat') ?>">
                        </div>
                    </div>

                    <div style="margin-top:20px; text-align:right;">
                        <button type="submit" style="background:#059669; color:#fff; border:none; padding:10px 22px; border-radius:8px; font-weight:700; font-size:0.88rem; cursor:pointer; display:inline-flex; align-items:center; gap:8px; box-shadow:0 4px 12px rgba(5,150,105,0.25);">
                            <i data-lucide="save" style="width:16px;height:16px;"></i> Simpan Pengaturan Legalitas SK
                        </button>
                    </div>
                </form>
            </div>

            <!-- CARD 2: SUSUNAN TIM SUPERVISI / DEWAN OBSERVER -->
            <div class="setting-card">
                <div class="card-header-flex">
                    <div>
                        <h2 style="font-size:1.1rem; font-weight:800; color:#0f172a; margin:0 0 4px 0; display:flex; align-items:center; gap:8px;">
                            <i data-lucide="users" style="width:20px;height:20px;color:#2563eb;"></i>
                            Daftar Susunan Tim Supervisi & Dewan Observer (Observer KBM)
                        </h2>
                        <span style="font-size:0.8rem; color:#64748b;">Daftar guru senior dan tenaga pendidik yang didelegasikan wewenang untuk menilai dan mendampingi guru sasaran.</span>
                    </div>
                    <button type="button" onclick="openAddModal()" style="background:#2563eb; color:#fff; border:none; padding:8px 16px; border-radius:8px; font-weight:700; font-size:0.82rem; cursor:pointer; display:inline-flex; align-items:center; gap:6px; box-shadow:0 4px 10px rgba(37,99,235,0.2);">
                        <i data-lucide="plus-circle" style="width:15px;height:15px;"></i> Tambah Anggota Observer
                    </button>
                </div>

                <?php
                $has_pj = false;
                $has_ketua = false;
                $has_sekretaris = false;
                $has_supervisor = false;

                $pj_member = null;
                $ketua_member = null;
                $sek_member = null;
                $sup_members = [];

                foreach($timSupervisi as $ts) {
                    $p = strtolower(trim($ts['peran_tim'] ?? ''));
                    $u = (int)($ts['urutan'] ?? 0);
                    if (!$has_pj && (strpos($p, 'penanggung') !== false || $u === 1)) {
                        $has_pj = true;
                        $pj_member = $ts;
                    } elseif (!$has_ketua && (strpos($p, 'ketua') !== false || $u === 2)) {
                        $has_ketua = true;
                        $ketua_member = $ts;
                    } elseif (!$has_sekretaris && (strpos($p, 'sekretaris') !== false || $u === 3)) {
                        $has_sekretaris = true;
                        $sek_member = $ts;
                    } elseif (strpos($p, 'supervisor') !== false || strpos($p, 'observer') !== false || $u >= 4) {
                        $has_supervisor = true;
                        $sup_members[] = $ts;
                    }
                }
                ?>

                <!-- STATUS STRUKTUR 4 PANITIA BAKU -->
                <div style="margin: 18px 0; padding: 14px 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
                    <div style="font-size: 0.82rem; font-weight: 800; color: #1e293b; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; flex-wrap:wrap; gap:8px;">
                        <span style="display:flex; align-items:center; gap:6px;">
                            <i data-lucide="shield-check" style="width:16px;height:16px;color:#059669;"></i>
                            Struktur 4 Panitia Baku (Penanggung Jawab, Ketua, Sekretaris, Supervisor):
                        </span>
                        <span style="font-size:0.75rem; color:#64748b; font-weight:normal;">Wajib terisi agar TTD seluruh laporan otomatis tersinkron</span>
                    </div>

                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(210px, 1fr)); gap:10px;">
                        <!-- 1. Penanggung Jawab -->
                        <div style="background:#fff; border:1px solid <?= $has_pj ? '#bbf7d0' : '#fed7aa' ?>; border-radius:10px; padding:10px 12px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                                <span style="font-size:0.72rem; font-weight:800; color:#065f46; text-transform:uppercase;">👑 1. Penanggung Jawab</span>
                                <span style="font-size:0.68rem; font-weight:700; padding:1px 6px; border-radius:4px; background:<?= $has_pj ? '#dcfce7; color:#15803d;' : '#ffedd5; color:#c2410c;' ?>">
                                    <?= $has_pj ? 'Terdaftar' : 'Belum Ada' ?>
                                </span>
                            </div>
                            <div style="font-weight:700; font-size:0.86rem; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                <?= $has_pj ? htmlspecialchars($pj_member['nama']) : '<em>(Belum ditentukan)</em>' ?>
                            </div>
                            <div style="font-size:0.72rem; color:#64748b; margin-top:2px;">
                                <?= $has_pj ? htmlspecialchars($pj_member['jabatan_madrasah'] ?? 'Kepala Madrasah') : 'Kepala Madrasah' ?>
                            </div>
                            <?php if(!$has_pj): ?>
                            <button type="button" onclick="chooseRole('add', 'Penanggung Jawab', 1, 'Pengarah, Penanggung Jawab Mutu & Monev Keseluruhan KBM'); openAddModal();" style="margin-top:6px; width:100%; font-size:0.72rem; padding:4px 8px; border-radius:6px; background:#059669; color:#fff; border:none; cursor:pointer; font-weight:700;">+ Atur Penanggung Jawab</button>
                            <?php endif; ?>
                        </div>

                        <!-- 2. Ketua Panitia -->
                        <div style="background:#fff; border:1px solid <?= $has_ketua ? '#bfdbfe' : '#fed7aa' ?>; border-radius:10px; padding:10px 12px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                                <span style="font-size:0.72rem; font-weight:800; color:#1e40af; text-transform:uppercase;">⭐ 2. Ketua Panitia</span>
                                <span style="font-size:0.68rem; font-weight:700; padding:1px 6px; border-radius:4px; background:<?= $has_ketua ? '#dbeafe; color:#1d4ed8;' : '#ffedd5; color:#c2410c;' ?>">
                                    <?= $has_ketua ? 'Terdaftar' : 'Belum Ada' ?>
                                </span>
                            </div>
                            <div style="font-weight:700; font-size:0.86rem; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                <?= $has_ketua ? htmlspecialchars($ketua_member['nama']) : '<em>(Belum ditentukan)</em>' ?>
                            </div>
                            <div style="font-size:0.72rem; color:#64748b; margin-top:2px;">
                                <?= $has_ketua ? htmlspecialchars($ketua_member['jabatan_madrasah'] ?? 'Waka Kurikulum') : 'Waka Kurikulum / Guru' ?>
                            </div>
                            <?php if(!$has_ketua): ?>
                            <button type="button" onclick="chooseRole('add', 'Ketua Panitia', 2, 'Koordinasi Jadwal, Verifikasi Administrasi Guru & Analisis Hasil'); openAddModal();" style="margin-top:6px; width:100%; font-size:0.72rem; padding:4px 8px; border-radius:6px; background:#2563eb; color:#fff; border:none; cursor:pointer; font-weight:700;">+ Atur Ketua Panitia</button>
                            <?php endif; ?>
                        </div>

                        <!-- 3. Sekretaris -->
                        <div style="background:#fff; border:1px solid <?= $has_sekretaris ? '#e9d5ff' : '#fed7aa' ?>; border-radius:10px; padding:10px 12px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                                <span style="font-size:0.72rem; font-weight:800; color:#6b21a8; text-transform:uppercase;">📝 3. Sekretaris</span>
                                <span style="font-size:0.68rem; font-weight:700; padding:1px 6px; border-radius:4px; background:<?= $has_sekretaris ? '#f3e8ff; color:#7e22ce;' : '#ffedd5; color:#c2410c;' ?>">
                                    <?= $has_sekretaris ? 'Terdaftar' : 'Belum Ada' ?>
                                </span>
                            </div>
                            <div style="font-weight:700; font-size:0.86rem; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                <?= $has_sekretaris ? htmlspecialchars($sek_member['nama']) : '<em>(Belum ditentukan)</em>' ?>
                            </div>
                            <div style="font-size:0.72rem; color:#64748b; margin-top:2px;">
                                <?= $has_sekretaris ? htmlspecialchars($sek_member['jabatan_madrasah'] ?? 'Guru') : 'Guru / Tenaga Pendidik' ?>
                            </div>
                            <?php if(!$has_sekretaris): ?>
                            <button type="button" onclick="chooseRole('add', 'Sekretaris', 3, 'Notulensi Coaching, Administrasi Rekapitulasi & Rapor Supervisi'); openAddModal();" style="margin-top:6px; width:100%; font-size:0.72rem; padding:4px 8px; border-radius:6px; background:#7c3aed; color:#fff; border:none; cursor:pointer; font-weight:700;">+ Atur Sekretaris</button>
                            <?php endif; ?>
                        </div>

                        <!-- 4. Supervisor -->
                        <div style="background:#fff; border:1px solid <?= $has_supervisor ? '#ccfbf1' : '#fed7aa' ?>; border-radius:10px; padding:10px 12px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                                <span style="font-size:0.72rem; font-weight:800; color:#0f766e; text-transform:uppercase;">🔍 4. Supervisor</span>
                                <span style="font-size:0.68rem; font-weight:700; padding:1px 6px; border-radius:4px; background:<?= $has_supervisor ? '#ccfbf1; color:#0f766e;' : '#ffedd5; color:#c2410c;' ?>">
                                    <?= $has_supervisor ? count($sup_members) . ' Terdaftar' : 'Belum Ada' ?>
                                </span>
                            </div>
                            <div style="font-weight:700; font-size:0.86rem; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                <?= $has_supervisor ? htmlspecialchars($sup_members[0]['nama']) . (count($sup_members) > 1 ? ' +' . (count($sup_members)-1) : '') : '<em>(Belum ditentukan)</em>' ?>
                            </div>
                            <div style="font-size:0.72rem; color:#64748b; margin-top:2px;">
                                Dewan Observer KBM
                            </div>
                            <?php if(!$has_supervisor): ?>
                            <button type="button" onclick="chooseRole('add', 'Supervisor', 4, 'Observasi KBM Tatap Muka, Penilaian Modul Ajar, dan Pendampingan Refleksi'); openAddModal();" style="margin-top:6px; width:100%; font-size:0.72rem; padding:4px 8px; border-radius:6px; background:#0d9488; color:#fff; border:none; cursor:pointer; font-weight:700;">+ Atur Supervisor</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; font-size:0.88rem;">
                        <thead>
                            <tr style="background:#f8fafc; border-bottom:2px solid #e2e8f0; color:#475569; text-align:left;">
                                <th style="padding:12px; width:45px; text-align:center;">Urutan</th>
                                <th style="padding:12px; min-width:220px;">Nama Observer / Supervisor</th>
                                <th style="padding:12px; min-width:180px;">Peran dalam Tim</th>
                                <th style="padding:12px; min-width:240px;">Fokus Tugas Observasi & Sasaran</th>
                                <th style="padding:12px; width:100px; text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($timSupervisi)): ?>
                            <tr>
                                <td colspan="5" style="text-align:center; padding:30px; color:#94a3b8;">
                                    Belum ada anggota tim supervisi yang ditambahkan. Silakan klik "Tambah Anggota Observer".
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach($timSupervisi as $ts): 
                                $badgeClass = 'role-observer';
                                if (strpos(strtolower($ts['peran_tim']), 'utama') !== false || strpos(strtolower($ts['peran_tim']), 'penanggung') !== false) $badgeClass = 'role-kamad';
                                elseif (strpos(strtolower($ts['peran_tim']), 'ketua') !== false) $badgeClass = 'role-ketua';
                                elseif (strpos(strtolower($ts['peran_tim']), 'sekretaris') !== false) $badgeClass = 'role-sekretaris';
                            ?>
                            <tr style="border-bottom:1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                <td align="center" style="padding:12px; font-weight:700; color:#64748b;">
                                    <?= htmlspecialchars($ts['urutan']) ?>
                                </td>
                                <td style="padding:12px;">
                                    <strong style="color:#0f172a;"><?= htmlspecialchars($ts['nama']) ?></strong><br>
                                    <?php if(!empty($ts['nip']) && $ts['nip'] !== '-' && strlen($ts['nip']) > 6): ?>
                                    <span style="font-size:0.75rem; color:#64748b;">NIP: <?= htmlspecialchars($ts['nip']) ?></span><br>
                                    <?php endif; ?>
                                    <span style="font-size:0.75rem; color:#059669; font-weight:600;"><?= htmlspecialchars($ts['jabatan_madrasah'] ?? 'Guru') ?></span>
                                </td>
                                <td style="padding:12px;">
                                    <span class="badge-role <?= $badgeClass ?>">
                                        <?= htmlspecialchars($ts['peran_tim']) ?>
                                    </span>
                                </td>
                                <td style="padding:12px; color:#334155; font-size:0.84rem; line-height:1.4;">
                                    <?php
                                    $fokusAdmin = trim($ts['tugas_fokus'] ?? '');
                                    if (empty($fokusAdmin)) {
                                        $p = strtolower(trim($ts['peran_tim'] ?? ''));
                                        if (strpos($p, 'penanggung') !== false || strpos($p, 'utama') !== false) {
                                            $fokusAdmin = 'Pengarah, Penanggung Jawab Mutu & Monev Keseluruhan KBM';
                                        } elseif (strpos($p, 'ketua') !== false) {
                                            $fokusAdmin = 'Koordinasi Jadwal Supervisi, Verifikasi Administrasi Guru & Analisis Hasil Observasi';
                                        } elseif (strpos($p, 'sekretaris') !== false) {
                                            $fokusAdmin = 'Notulensi Coaching, Administrasi Instrumen & Rekapitulasi Laporan RTL';
                                        } elseif (strpos($p, 'supervisor') !== false || strpos($p, 'observer') !== false) {
                                            $fokusAdmin = 'Observasi KBM Tatap Muka Kelas, Penilaian 25 Indikator KBM & Bimbingan Klinis';
                                        } else {
                                            $fokusAdmin = 'Observasi Pembelajaran Kelas & Pendampingan Perangkat Ajar';
                                        }
                                    }
                                    echo htmlspecialchars($fokusAdmin);
                                    ?>
                                </td>
                                <td align="center" style="padding:12px;">
                                    <div style="display:flex; gap:6px; justify-content:center;">
                                        <button type="button" onclick='openEditModal(<?= json_encode($ts) ?>)' style="background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; border-radius:6px; padding:5px 8px; cursor:pointer;" title="Edit Anggota">
                                            <i data-lucide="edit-3" style="width:14px;height:14px;"></i>
                                        </button>
                                        <button type="button" onclick="confirmDelete(<?= $ts['id'] ?>, '<?= htmlspecialchars(addslashes($ts['nama'])) ?>')" style="background:#fef2f2; color:#ef4444; border:1px solid #fecaca; border-radius:6px; padding:5px 8px; cursor:pointer;" title="Hapus Anggota">
                                            <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- CARD 3: SINKRONISASI & INFORMASI SISTEM -->
            <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:16px 20px; display:flex; gap:14px; align-items:flex-start;">
                <div style="background:#059669; color:#fff; width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i data-lucide="sparkles" style="width:18px;height:18px;"></i>
                </div>
                <div>
                    <h3 style="margin:0 0 4px 0; font-size:0.92rem; font-weight:800; color:#065f46;">
                        Otomatisasi Integrasi ke Dokumen Laporan & PKKM
                    </h3>
                    <p style="margin:0; font-size:0.83rem; color:#047857; line-height:1.5;">
                        Setiap perubahan pada Nomor SK, Tanggal SK, data Pengawas Kemenag, Kepala Madrasah, dan Dewan Observer di halaman ini akan <strong>secara otomatis diperbarui secara *real-time*</strong> pada:
                        <br>&bull; Lembar Pengesahan & Bab II Laporan Hasil Supervisi Pembelajaran.
                        <br>&bull; Lembar Pengesahan & Bab II Laporan Realisasi Tindak Lanjut (RTL).
                        <br>&bull; Naskah Dokumen Program Supervisi Tahunan & SK Pendelegasian Observer.
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- MODAL TAMBAH ANGGOTA -->
<div id="modalAdd" class="modal-backdrop">
    <div style="background:#fff; border-radius:14px; max-width:560px; width:92%; padding:24px; box-shadow:0 20px 40px rgba(0,0,0,0.2); max-height:90vh; overflow-y:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid #e2e8f0; padding-bottom:12px;">
            <h3 style="margin:0; font-size:1.1rem; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:8px;">
                <i data-lucide="user-plus" style="width:20px;height:20px;color:#2563eb;"></i>
                Tambah Anggota Tim Supervisi
            </h3>
            <button onclick="closeModal('modalAdd')" style="background:none; border:none; font-size:1.2rem; cursor:pointer; color:#94a3b8;">&times;</button>
        </div>

        <form action="/kurikulum/supervisi/tim/add-member" method="POST">
            <input type="hidden" name="nama" id="add_nama" value="">

            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:0.82rem; font-weight:700; color:#334155; margin-bottom:6px;">
                    Nama Guru & PTK (Dropdown Database) <span style="color:#ef4444;">*</span>
                </label>
                <select name="guru_id" id="add_guru_id" class="form-control-custom" onchange="onGuruChange('add')" required>
                    <option value="">-- Pilih Guru dari Database --</option>
                    <?php foreach($daftarGuru as $dg): ?>
                    <option value="<?= $dg['id'] ?>" 
                            data-nama="<?= htmlspecialchars($dg['nama']) ?>" 
                            data-nip="<?= htmlspecialchars($dg['nip'] ?: '-') ?>" 
                            data-jabatan="<?= htmlspecialchars($dg['jabatan'] ?: 'Guru') ?>"
                            data-kamad="<?= !empty($dg['is_kamad']) ? '1' : '0' ?>">
                        <?= htmlspecialchars($dg['nama']) ?> <?= !empty($dg['is_kamad']) ? '(Kepala Madrasah)' : (!empty($dg['jabatan']) ? '('.htmlspecialchars($dg['jabatan']).')' : '') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <small style="font-size:0.73rem; color:#64748b;">Pilih guru, NIP dan jabatan madrasah akan terisi otomatis.</small>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1.2fr; gap:12px; margin-bottom:14px;">
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:#334155; margin-bottom:6px;">NIP / PegID</label>
                    <input type="text" name="nip" id="add_nip" class="form-control-custom" value="-" readonly style="background:#f8fafc; color:#64748b;">
                </div>
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:#334155; margin-bottom:6px;">Jabatan Madrasah</label>
                    <select name="jabatan_madrasah" id="add_jabatan" class="form-control-custom">
                        <option value="Kepala Madrasah">Kepala Madrasah</option>
                        <option value="Waka Kurikulum">Waka Kurikulum</option>
                        <option value="Waka Kesiswaan">Waka Kesiswaan</option>
                        <option value="Waka Sarpras">Waka Sarpras</option>
                        <option value="Waka Humas">Waka Humas</option>
                        <option value="Bendahara Madrasah">Bendahara Madrasah</option>
                        <option value="Pembina IT / Operator">Pembina IT / Operator</option>
                        <option value="Guru Mata Pelajaran" selected>Guru Mata Pelajaran</option>
                        <option value="Dewan Observer KBM">Dewan Observer KBM</option>
                        <option value="Tenaga Kependidikan">Tenaga Kependidikan</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:0.82rem; font-weight:700; color:#334155; margin-bottom:6px;">
                    Peran dalam Kepanitiaan (4 Posisi Baku):
                </label>
                <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:6px; margin-bottom:8px;">
                    <button type="button" class="btn-role-select" id="btn-add-pj" onclick="chooseRole('add', 'Penanggung Jawab', 1, 'Pengarah, Penanggung Jawab Mutu & Monev Keseluruhan KBM')">
                        <span style="font-size:1.15rem;">👑</span>
                        <strong>Penanggung Jawab</strong>
                    </button>
                    <button type="button" class="btn-role-select" id="btn-add-ketua" onclick="chooseRole('add', 'Ketua Panitia', 2, 'Koordinasi Jadwal, Verifikasi Administrasi Guru & Analisis Hasil')">
                        <span style="font-size:1.15rem;">⭐</span>
                        <strong>Ketua Panitia</strong>
                    </button>
                    <button type="button" class="btn-role-select" id="btn-add-sek" onclick="chooseRole('add', 'Sekretaris', 3, 'Notulensi Coaching, Administrasi Rekapitulasi & Rapor Supervisi')">
                        <span style="font-size:1.15rem;">📝</span>
                        <strong>Sekretaris</strong>
                    </button>
                    <button type="button" class="btn-role-select" id="btn-add-sup" onclick="chooseRole('add', 'Supervisor', <?= count($timSupervisi) + 1 ?>, 'Observasi KBM Tatap Muka Kelas, Penilaian 25 Indikator KBM & Bimbingan Klinis')">
                        <span style="font-size:1.15rem;">🔍</span>
                        <strong>Supervisor</strong>
                    </button>
                </div>
                <div style="display:grid; grid-template-columns:1.5fr 1fr; gap:12px;">
                    <div>
                        <select name="peran_tim" id="add_peran" class="form-control-custom" onchange="onPeranSelectChange('add')" required>
                            <option value="Penanggung Jawab">Penanggung Jawab</option>
                            <option value="Ketua Panitia">Ketua Panitia</option>
                            <option value="Sekretaris">Sekretaris</option>
                            <option value="Supervisor" selected>Supervisor</option>
                        </select>
                    </div>
                    <div>
                        <input type="number" name="urutan" id="add_urutan" class="form-control-custom" value="<?= count($timSupervisi) + 1 ?>" min="1" placeholder="Urutan Tampil" title="Urutan susunan dalam SK & Laporan">
                    </div>
                </div>
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block; font-size:0.82rem; font-weight:700; color:#334155; margin-bottom:6px;">Fokus Tugas Observasi & Sasaran Guru</label>
                <textarea name="tugas_fokus" id="add_fokus" class="form-control-custom" rows="2" placeholder="Misal: Observasi KBM Tatap Muka Kelas, Penilaian 25 Indikator KBM & Bimbingan Klinis">Observasi KBM Tatap Muka Kelas, Penilaian 25 Indikator KBM & Bimbingan Klinis</textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="closeModal('modalAdd')" style="background:#f1f5f9; color:#475569; border:none; padding:9px 16px; border-radius:8px; font-weight:600; cursor:pointer;">Batal</button>
                <button type="submit" style="background:#2563eb; color:#fff; border:none; padding:9px 18px; border-radius:8px; font-weight:700; cursor:pointer;">Simpan Anggota</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT ANGGOTA -->
<div id="modalEdit" class="modal-backdrop">
    <div style="background:#fff; border-radius:14px; max-width:560px; width:92%; padding:24px; box-shadow:0 20px 40px rgba(0,0,0,0.2); max-height:90vh; overflow-y:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid #e2e8f0; padding-bottom:12px;">
            <h3 style="margin:0; font-size:1.1rem; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:8px;">
                <i data-lucide="edit-3" style="width:20px;height:20px;color:#059669;"></i>
                Edit Data Anggota Tim Supervisi
            </h3>
            <button onclick="closeModal('modalEdit')" style="background:none; border:none; font-size:1.2rem; cursor:pointer; color:#94a3b8;">&times;</button>
        </div>

        <form action="/kurikulum/supervisi/tim/update-member" method="POST">
            <input type="hidden" name="id" id="edit_id">
            <input type="hidden" name="nama" id="edit_nama">

            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:0.82rem; font-weight:700; color:#334155; margin-bottom:6px;">
                    Nama Guru & PTK (Dropdown Database) <span style="color:#ef4444;">*</span>
                </label>
                <select name="guru_id" id="edit_guru_id" class="form-control-custom" onchange="onGuruChange('edit')" required>
                    <option value="">-- Pilih Guru dari Database --</option>
                    <?php foreach($daftarGuru as $dg): ?>
                    <option value="<?= $dg['id'] ?>" 
                            data-nama="<?= htmlspecialchars($dg['nama']) ?>" 
                            data-nip="<?= htmlspecialchars($dg['nip'] ?: '-') ?>" 
                            data-jabatan="<?= htmlspecialchars($dg['jabatan'] ?: 'Guru') ?>"
                            data-kamad="<?= !empty($dg['is_kamad']) ? '1' : '0' ?>">
                        <?= htmlspecialchars($dg['nama']) ?> <?= !empty($dg['is_kamad']) ? '(Kepala Madrasah)' : (!empty($dg['jabatan']) ? '('.htmlspecialchars($dg['jabatan']).')' : '') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <small style="font-size:0.73rem; color:#64748b;">Ubah guru langsung dari daftar tanpa perlu mengetik ulang.</small>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1.2fr; gap:12px; margin-bottom:14px;">
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:#334155; margin-bottom:6px;">NIP / PegID</label>
                    <input type="text" name="nip" id="edit_nip" class="form-control-custom" readonly style="background:#f8fafc; color:#64748b;">
                </div>
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:#334155; margin-bottom:6px;">Jabatan Madrasah</label>
                    <select name="jabatan_madrasah" id="edit_jabatan" class="form-control-custom">
                        <option value="Kepala Madrasah">Kepala Madrasah</option>
                        <option value="Waka Kurikulum">Waka Kurikulum</option>
                        <option value="Waka Kesiswaan">Waka Kesiswaan</option>
                        <option value="Waka Sarpras">Waka Sarpras</option>
                        <option value="Waka Humas">Waka Humas</option>
                        <option value="Bendahara Madrasah">Bendahara Madrasah</option>
                        <option value="Pembina IT / Operator">Pembina IT / Operator</option>
                        <option value="Guru Mata Pelajaran">Guru Mata Pelajaran</option>
                        <option value="Dewan Observer KBM">Dewan Observer KBM</option>
                        <option value="Tenaga Kependidikan">Tenaga Kependidikan</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:0.82rem; font-weight:700; color:#334155; margin-bottom:6px;">
                    Peran dalam Kepanitiaan (4 Posisi Baku):
                </label>
                <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:6px; margin-bottom:8px;">
                    <button type="button" class="btn-role-select" id="btn-edit-pj" onclick="chooseRole('edit', 'Penanggung Jawab', 1, 'Pengarah, Penanggung Jawab Mutu & Monev Keseluruhan KBM')">
                        <span style="font-size:1.15rem;">👑</span>
                        <strong>Penanggung Jawab</strong>
                    </button>
                    <button type="button" class="btn-role-select" id="btn-edit-ketua" onclick="chooseRole('edit', 'Ketua Panitia', 2, 'Koordinasi Jadwal, Verifikasi Administrasi Guru & Analisis Hasil')">
                        <span style="font-size:1.15rem;">⭐</span>
                        <strong>Ketua Panitia</strong>
                    </button>
                    <button type="button" class="btn-role-select" id="btn-edit-sek" onclick="chooseRole('edit', 'Sekretaris', 3, 'Notulensi Coaching, Administrasi Rekapitulasi & Rapor Supervisi')">
                        <span style="font-size:1.15rem;">📝</span>
                        <strong>Sekretaris</strong>
                    </button>
                    <button type="button" class="btn-role-select" id="btn-edit-sup" onclick="chooseRole('edit', 'Supervisor', 4, 'Observasi KBM Tatap Muka Kelas, Penilaian 25 Indikator KBM & Bimbingan Klinis')">
                        <span style="font-size:1.15rem;">🔍</span>
                        <strong>Supervisor</strong>
                    </button>
                </div>
                <div style="display:grid; grid-template-columns:1.5fr 1fr; gap:12px;">
                    <div>
                        <select name="peran_tim" id="edit_peran" class="form-control-custom" onchange="onPeranSelectChange('edit')" required>
                            <option value="Penanggung Jawab">Penanggung Jawab</option>
                            <option value="Ketua Panitia">Ketua Panitia</option>
                            <option value="Sekretaris">Sekretaris</option>
                            <option value="Supervisor">Supervisor</option>
                        </select>
                    </div>
                    <div>
                        <input type="number" name="urutan" id="edit_urutan" class="form-control-custom" min="1" placeholder="Urutan Tampil" title="Urutan susunan dalam SK & Laporan">
                    </div>
                </div>
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block; font-size:0.82rem; font-weight:700; color:#334155; margin-bottom:6px;">Fokus Tugas Observasi & Sasaran Guru</label>
                <textarea name="tugas_fokus" id="edit_fokus" class="form-control-custom" rows="2"></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="closeModal('modalEdit')" style="background:#f1f5f9; color:#475569; border:none; padding:9px 16px; border-radius:8px; font-weight:600; cursor:pointer;">Batal</button>
                <button type="submit" style="background:#059669; color:#fff; border:none; padding:9px 18px; border-radius:8px; font-weight:700; cursor:pointer;">Perbarui Data</button>
            </div>
        </form>
    </div>
</div>

<!-- FORM DELETE -->
<form id="deleteMemberForm" action="/kurikulum/supervisi/tim/delete-member" method="POST" style="display:none;">
    <input type="hidden" name="id" id="delete_member_id">
</form>

<script>
    var standardFokus = {
        'Penanggung Jawab': 'Pengarah, Penanggung Jawab Mutu & Monev Keseluruhan KBM',
        'Ketua Panitia': 'Koordinasi Jadwal, Verifikasi Administrasi Guru & Analisis Hasil',
        'Sekretaris': 'Notulensi Coaching, Administrasi Rekapitulasi & Rapor Supervisi',
        'Supervisor': 'Observasi KBM Tatap Muka Kelas, Penilaian 25 Indikator KBM & Bimbingan Klinis'
    };

    function updateRoleButtonsActive(mode, role) {
        var prefix = mode === 'add' ? 'btn-add-' : 'btn-edit-';
        var r = (role || '').toLowerCase();
        
        var btns = ['pj', 'ketua', 'sek', 'sup'];
        btns.forEach(function(b) {
            var el = document.getElementById(prefix + b);
            if (el) {
                el.classList.remove('active');
                el.style.border = '1px solid #cbd5e1';
                el.style.background = '#f8fafc';
                el.style.color = '#1e293b';
            }
        });

        var targetId = null;
        if (r.indexOf('penanggung') !== -1 || r.indexOf('utama') !== -1) targetId = prefix + 'pj';
        else if (r.indexOf('ketua') !== -1) targetId = prefix + 'ketua';
        else if (r.indexOf('sekretaris') !== -1) targetId = prefix + 'sek';
        else if (r.indexOf('supervisor') !== -1 || r.indexOf('observer') !== -1) targetId = prefix + 'sup';

        if (targetId) {
            var activeEl = document.getElementById(targetId);
            if (activeEl) {
                activeEl.classList.add('active');
                activeEl.style.border = '2px solid #2563eb';
                activeEl.style.background = '#eff6ff';
                activeEl.style.color = '#1d4ed8';
            }
        }
    }

    function chooseRole(mode, role, urutan, fokus) {
        var peranSelect = document.getElementById(mode + '_peran');
        if (peranSelect) {
            // Find best matching option
            for (var i = 0; i < peranSelect.options.length; i++) {
                if (peranSelect.options[i].value === role || role.indexOf(peranSelect.options[i].value) !== -1) {
                    peranSelect.selectedIndex = i;
                    break;
                }
            }
        }

        if (urutan) {
            var urutanInput = document.getElementById(mode + '_urutan');
            if (urutanInput) urutanInput.value = urutan;
        }

        var fokusEl = document.getElementById(mode + '_fokus');
        if (fokusEl && fokus) {
            fokusEl.value = fokus;
        } else if (fokusEl && standardFokus[role]) {
            fokusEl.value = standardFokus[role];
        }

        updateRoleButtonsActive(mode, role);
    }

    function onPeranSelectChange(mode) {
        var peranSelect = document.getElementById(mode + '_peran');
        var val = peranSelect ? peranSelect.value : '';
        if (standardFokus[val]) {
            var fokusEl = document.getElementById(mode + '_fokus');
            if (fokusEl && (!fokusEl.value || fokusEl.value.trim() === '')) {
                fokusEl.value = standardFokus[val];
            }
        }
        updateRoleButtonsActive(mode, val);
    }

    function onGuruChange(mode) {
        var sel = document.getElementById(mode + '_guru_id');
        if (!sel) return;
        var opt = sel.options[sel.selectedIndex];
        if (!opt || !opt.value) return;

        var nama = opt.getAttribute('data-nama') || '';
        var nip = opt.getAttribute('data-nip') || '-';
        var rawJabatan = opt.getAttribute('data-jabatan') || 'Guru';
        var isKamad = opt.getAttribute('data-kamad') === '1';

        var namaEl = document.getElementById(mode + '_nama');
        if (namaEl) namaEl.value = nama;

        var nipEl = document.getElementById(mode + '_nip');
        if (nipEl) nipEl.value = nip;

        var jabSelect = document.getElementById(mode + '_jabatan');
        if (jabSelect) {
            var found = false;
            var jabLower = rawJabatan.toLowerCase();

            if (isKamad) {
                for (var i = 0; i < jabSelect.options.length; i++) {
                    if (jabSelect.options[i].value.toLowerCase().indexOf('kepala madrasah') !== -1) {
                        jabSelect.selectedIndex = i;
                        found = true;
                        break;
                    }
                }
            } else {
                for (var j = 0; j < jabSelect.options.length; j++) {
                    var optVal = jabSelect.options[j].value.toLowerCase();
                    if (optVal === jabLower || (jabLower.indexOf('kurikulum') !== -1 && optVal.indexOf('kurikulum') !== -1) ||
                        (jabLower.indexOf('kesiswaan') !== -1 && optVal.indexOf('kesiswaan') !== -1) ||
                        (jabLower.indexOf('bendahara') !== -1 && optVal.indexOf('bendahara') !== -1) ||
                        (jabLower.indexOf('it') !== -1 && optVal.indexOf('it') !== -1)) {
                        jabSelect.selectedIndex = j;
                        found = true;
                        break;
                    }
                }
            }
            if (!found) {
                jabSelect.value = 'Guru Mata Pelajaran';
            }
        }

        // Suggest role if mode is add
        if (mode === 'add') {
            if (isKamad) {
                chooseRole('add', 'Penanggung Jawab', 1, standardFokus['Penanggung Jawab']);
            } else if (rawJabatan.toLowerCase().indexOf('kurikulum') !== -1) {
                chooseRole('add', 'Ketua Panitia', 2, standardFokus['Ketua Panitia']);
            }
        }
    }

    function openAddModal() {
        document.getElementById('modalAdd').style.display = 'flex';
        updateRoleButtonsActive('add', document.getElementById('add_peran').value);
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function openEditModal(data) {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_nama').value = data.nama;
        document.getElementById('edit_nip').value = data.nip || '-';
        document.getElementById('edit_urutan').value = data.urutan;
        document.getElementById('edit_fokus').value = data.tugas_fokus || '';

        // Select Guru
        var guruSel = document.getElementById('edit_guru_id');
        var matchedGuru = false;
        if (guruSel) {
            for (var i = 0; i < guruSel.options.length; i++) {
                var gOpt = guruSel.options[i];
                if ((data.guru_id && gOpt.value == data.guru_id) || (gOpt.getAttribute('data-nama') && gOpt.getAttribute('data-nama').toLowerCase() === (data.nama || '').toLowerCase())) {
                    guruSel.selectedIndex = i;
                    matchedGuru = true;
                    break;
                }
            }
            if (!matchedGuru) {
                guruSel.selectedIndex = 0;
            }
        }

        // Select Jabatan
        var jabSel = document.getElementById('edit_jabatan');
        if (jabSel) {
            var rawJab = (data.jabatan_madrasah || '').toLowerCase();
            var matchedJab = false;
            for (var j = 0; j < jabSel.options.length; j++) {
                var oVal = jabSel.options[j].value.toLowerCase();
                if (oVal === rawJab || rawJab.indexOf(oVal) !== -1 || oVal.indexOf(rawJab) !== -1) {
                    jabSel.selectedIndex = j;
                    matchedJab = true;
                    break;
                }
            }
            if (!matchedJab && data.jabatan_madrasah) {
                var optNew = document.createElement('option');
                optNew.value = data.jabatan_madrasah;
                optNew.text = data.jabatan_madrasah;
                optNew.selected = true;
                jabSel.appendChild(optNew);
            }
        }

        // Select Peran
        var peranSel = document.getElementById('edit_peran');
        var peranRaw = (data.peran_tim || 'Supervisor');
        var pLower = peranRaw.toLowerCase();
        var normalizedPeran = 'Supervisor';
        if (pLower.indexOf('penanggung') !== -1 || pLower.indexOf('utama') !== -1) normalizedPeran = 'Penanggung Jawab';
        else if (pLower.indexOf('ketua') !== -1) normalizedPeran = 'Ketua Panitia';
        else if (pLower.indexOf('sekretaris') !== -1) normalizedPeran = 'Sekretaris';
        else normalizedPeran = 'Supervisor';

        if (peranSel) {
            peranSel.value = normalizedPeran;
        }
        updateRoleButtonsActive('edit', normalizedPeran);

        document.getElementById('modalEdit').style.display = 'flex';
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    function confirmDelete(id, nama) {
        Swal.fire({
            title: 'Hapus Anggota Tim?',
            text: 'Apakah Anda yakin ingin menghapus ' + nama + ' dari susunan tim supervisi?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete_member_id').value = id;
                document.getElementById('deleteMemberForm').submit();
            }
        });
    }

    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
</body>
</html>
