<?php
$active_module = 'pkkm';
$active_page = 'pengawas';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title = $title_page ?? 'Kelola Akun Pengawas PKKM'; include __DIR__ . '/_zamrud_head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #0f172a; }
        .z-scroll { padding-bottom: 3rem !important; }
        
        /* Banner Header */
        .elite-page-header {
            position: relative; padding: 2rem 2.5rem; margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #064e3b 0%, #047857 100%);
            border-radius: 20px; overflow: hidden; color: #ffffff;
            box-shadow: 0 12px 30px -8px rgba(6, 78, 59, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .elite-page-header .header-bg-shapes {
            position: absolute; top: 0; right: 0; bottom: 0; left: 0;
            background-image: radial-gradient(circle at 85% 20%, rgba(255, 255, 255, 0.18) 0%, transparent 40%),
                              radial-gradient(circle at 15% 80%, rgba(0, 0, 0, 0.12) 0%, transparent 40%);
            z-index: 1; pointer-events: none;
        }
        .elite-page-header .header-content-inner {
            position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap;
        }
        .elite-page-header .header-text-premium h1 {
            font-size: 1.75rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 12px; color: #fff;
        }
        .elite-page-header .header-text-premium p {
            font-size: 0.9rem; color: rgba(255,255,255,0.85); margin-top: 0.35rem; margin-bottom: 0; max-width: 680px; font-weight: 400; line-height: 1.5;
        }
        .header-tag-pill {
            display: inline-flex; align-items: center; gap: 6px; background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); color: #fff; font-size: 0.72rem; font-weight: 700; padding: 3px 10px; border-radius: 999px; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 8px; backdrop-filter: blur(4px);
        }

        .e-btn {
            display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px;
            border-radius: 12px; font-weight: 700; font-size: 0.85rem; cursor: pointer;
            border: none; transition: 0.2s ease; text-decoration: none;
        }
        .e-btn:hover { transform: translateY(-2px); }
        .e-btn-primary { background: #ffffff; color: #047857; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12); }
        .e-btn-primary:hover { background: #f8fafc; color: #064e3b; }
        .e-btn-secondary { background: rgba(255, 255, 255, 0.18); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(4px); }
        .e-btn-secondary:hover { background: rgba(255, 255, 255, 0.28); color: #ffffff; }

        /* Stat Grid */
        .stat-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px; margin-bottom: 24px;
        }
        .stat-box {
            padding: 16px 20px; border-radius: 16px; display: flex; align-items: center; gap: 16px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03); background: #fff; border: 1px solid #e2e8f0; transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-box:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06); }
        .stat-icon { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.9); box-shadow: 0 2px 6px rgba(0,0,0,0.04); flex-shrink: 0; }
        .stat-box.green { background: linear-gradient(135deg, #f0fdf4, #ffffff); border-color: #bbf7d0; color: #14532d; }
        .stat-box.green .stat-icon { color: #10b981; }
        .stat-box.blue { background: linear-gradient(135deg, #eff6ff, #ffffff); border-color: #bfdbfe; color: #1e3a8a; }
        .stat-box.blue .stat-icon { color: #3b82f6; }
        .stat-box.purple { background: linear-gradient(135deg, #faf5ff, #ffffff); border-color: #e9d5ff; color: #6b21a8; }
        .stat-box.purple .stat-icon { color: #8b5cf6; }
        .stat-value { font-size: 1.7rem; font-weight: 800; line-height: 1.1; margin-bottom: 2px; }
        .stat-label { font-size: 0.78rem; font-weight: 700; opacity: 0.75; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Card Container */
        .glass-card {
            background: #ffffff; border-radius: 16px;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.02);
            border: 1px solid rgba(226, 232, 240, 0.8);
            padding: 24px; margin-bottom: 24px;
        }

        .badge-role-p1 {
            background: #dcfce7; color: #15803d; border: 1px solid #86efac;
            padding: 4px 10px; border-radius: 8px; font-weight: 800; font-size: 0.75rem;
            display: inline-flex; align-items: center; gap: 4px;
        }
        .badge-role-p2 {
            background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd;
            padding: 4px 10px; border-radius: 8px; font-weight: 800; font-size: 0.75rem;
            display: inline-flex; align-items: center; gap: 4px;
        }

        /* Access Card Preview */
        .credential-pill {
            background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 8px;
            padding: 3px 8px; font-family: monospace; font-size: 0.82rem; font-weight: 700;
            color: #0f172a; display: inline-flex; align-items: center; gap: 6px;
        }
    </style>
</head>
<body>

<div class="z-wrap">
    <?php include __DIR__ . '/_zamrud_sidebar.php'; ?>

    <main class="z-main">
        <?php $page_badge = 'Admin Portal'; include __DIR__ . '/../siakad/_siakad_header.php'; ?>

        <div class="z-scroll">
            <div class="container-fluid px-4 pt-3">

                <!-- Alert Messages -->
                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'saved'): ?>
                    <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm d-flex align-items-center gap-2 mb-3" role="alert">
                        <i data-lucide="check-circle" style="width:20px;height:20px;color:#16a34a;"></i>
                        <div><strong>Berhasil!</strong> Data akun pengawas penilai berhasil disimpan.</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
                    <div class="alert alert-info alert-dismissible fade show rounded-4 shadow-sm d-flex align-items-center gap-2 mb-3" role="alert">
                        <i data-lucide="info" style="width:20px;height:20px;color:#2563eb;"></i>
                        <div>Akun pengawas telah dihapus dari sistem.</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'generated'): ?>
                    <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm d-flex align-items-center gap-2 mb-3" role="alert">
                        <i data-lucide="sparkles" style="width:20px;height:20px;color:#059669;"></i>
                        <div><strong>Selesai!</strong> Akun standar Penilai 1 & Penilai 2 berhasil digenerate otomatis.</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Elite Header Banner -->
                <div class="elite-page-header">
                    <div class="header-bg-shapes"></div>
                    <div class="header-content-inner">
                        <div class="header-text-premium">
                            <span class="header-tag-pill">
                                <i data-lucide="shield-check" style="width:13px;height:13px;"></i> Akses Asesor Eksternal
                            </span>
                            <h1>
                                <i data-lucide="user-check" style="width:30px;height:30px;"></i>
                                Akun & Profil Pengawas PKKM
                            </h1>
                            <p>
                                Kelola akun Pengawas / Penilai Kemenag untuk pelaksanaan Penilaian Kinerja Kepala Madrasah. Buat akun Penilai 1 dan Penilai 2 agar asesor dapat langsung login ke portal penilaian mandiri.
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <a href="/pengawas/pkkm" target="_blank" class="e-btn e-btn-secondary" title="Pratinjau tampilan portal khusus pengawas">
                                <i data-lucide="external-link" style="width:16px;height:16px;"></i> Buka Portal Pengawas
                            </a>
                            <?php if (empty($pengawas_list)): ?>
                            <a href="/admin/pkkm/pengawas/generate-default" class="e-btn e-btn-secondary" onclick="return confirm('Generate akun otomatis untuk Penilai 1 dan Penilai 2?')">
                                <i data-lucide="wand-2" style="width:16px;height:16px;"></i> Auto-Generate 2 Akun
                            </a>
                            <?php endif; ?>
                            <button type="button" class="e-btn e-btn-primary" onclick="openModalPengawas()">
                                <i data-lucide="user-plus" style="width:16px;height:16px;"></i> Tambah Akun Penilai
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stat Summary Cards -->
                <?php 
                    $penilai1_ready = false;
                    $penilai2_ready = false;
                    foreach ($pengawas_list as $pg) {
                        if ($pg['peran'] === 'penilai_1') $penilai1_ready = true;
                        if ($pg['peran'] === 'penilai_2') $penilai2_ready = true;
                    }
                ?>
                <div class="stat-grid">
                    <div class="stat-box green">
                        <div class="stat-icon">
                            <i data-lucide="users" style="width:24px;height:24px;"></i>
                        </div>
                        <div>
                            <div class="stat-value"><?php echo count($pengawas_list); ?></div>
                            <div class="stat-label">Total Akun Asesor</div>
                        </div>
                    </div>

                    <div class="stat-box blue">
                        <div class="stat-icon">
                            <i data-lucide="award" style="width:24px;height:24px;"></i>
                        </div>
                        <div>
                            <div class="stat-value" style="font-size: 1.25rem;">
                                <?php echo $penilai1_ready ? '<span class="text-success">Siap (Aktif)</span>' : '<span class="text-secondary">Belum Dibuat</span>'; ?>
                            </div>
                            <div class="stat-label">Penilai 1 (Ketua Tim)</div>
                        </div>
                    </div>

                    <div class="stat-box purple">
                        <div class="stat-icon">
                            <i data-lucide="clipboard-check" style="width:24px;height:24px;"></i>
                        </div>
                        <div>
                            <div class="stat-value" style="font-size: 1.25rem;">
                                <?php echo $penilai2_ready ? '<span class="text-primary">Siap (Aktif)</span>' : '<span class="text-secondary">Belum Dibuat</span>'; ?>
                            </div>
                            <div class="stat-label">Penilai 2 (Anggota)</div>
                        </div>
                    </div>
                </div>

                <!-- Info Box Untuk Hari-H -->
                <div class="glass-card mb-4" style="background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%); border-left: 5px solid #059669;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: #059669; color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 10px rgba(5,150,105,0.3);">
                            <i data-lucide="laptop" style="width:22px;height:22px;"></i>
                        </div>
                        <div style="flex: 1;">
                            <h6 class="fw-bold mb-1" style="color: #064e3b; font-size: 0.95rem;">
                                Cara Menggunakan Saat Hari-H PKKM:
                            </h6>
                            <p class="mb-0 text-muted" style="font-size: 0.85rem; line-height: 1.5;">
                                Berikan username & password kepada masing-masing Pengawas (Asesor 1 & 2). Pengawas dapat membuka laptop/tablet mereka, mengakses URL madrasah <code>masmifhda.sch.id/login</code>, lalu memasukkan akun mereka. Sistem akan langsung menampilkan <strong>Portal Penilaian Khusus Asesor</strong> tanpa menu-menu administrasi umum lainnya.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Daftar Akun Pengawas Table -->
                <div class="glass-card">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold mb-1" style="color: #0f172a;">
                                <i data-lucide="list" style="width: 18px; height: 18px; vertical-align: middle; color: #059669;"></i>
                                Daftar Asesor / Pengawas Terdaftar
                            </h5>
                            <p class="text-muted mb-0" style="font-size: 0.82rem;">
                                Seluruh akun di bawah memiliki akses eksklusif untuk mengevaluasi 42 Indikator PKKM.
                            </p>
                        </div>
                    </div>

                    <?php if (empty($pengawas_list)): ?>
                        <div class="text-center py-5">
                            <div style="width: 70px; height: 70px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px auto; color: #94a3b8;">
                                <i data-lucide="user-x" style="width: 32px; height: 32px;"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Belum Ada Akun Pengawas</h6>
                            <p class="text-muted mb-3" style="font-size: 0.85rem; max-width: 420px; margin: 0 auto;">
                                Anda belum menambahkan akun untuk pengawas penilai PKKM. Klik tombol di bawah untuk membuat akun otomatis.
                            </p>
                            <a href="/admin/pkkm/pengawas/generate-default" class="btn btn-sm btn-success fw-bold px-3 py-2 rounded-3 shadow-sm" onclick="return confirm('Buat akun default untuk Penilai 1 & 2?')">
                                <i data-lucide="wand-2" style="width: 15px; height: 15px; vertical-align: middle;"></i> Auto-Generate Penilai 1 & 2
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                                <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; font-weight: 700; color: #475569; font-size: 0.78rem; text-transform: uppercase;">
                                    <tr>
                                        <th style="width: 5%; text-align: center;">No</th>
                                        <th style="width: 25%;">Nama & NIP Pengawas</th>
                                        <th style="width: 15%;">Peran Penilai</th>
                                        <th style="width: 22%;">Instansi / Jabatan</th>
                                        <th style="width: 18%;">Akun Login</th>
                                        <th style="width: 15%; text-align: right;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($pengawas_list as $row): ?>
                                    <tr>
                                        <td style="text-align: center; font-weight: 700; color: #64748b;"><?php echo $no++; ?></td>
                                        <td>
                                            <div class="fw-bold text-dark" style="font-size: 0.92rem;">
                                                <?php echo htmlspecialchars($row['nama']); ?>
                                            </div>
                                            <div style="font-size: 0.78rem; color: #64748b;">
                                                NIP: <span class="font-monospace"><?php echo htmlspecialchars($row['nip']); ?></span>
                                            </div>
                                            <?php if (!empty($row['no_hp'])): ?>
                                                <div style="font-size: 0.74rem; color: #059669;">
                                                    <i data-lucide="phone" style="width:11px;height:11px;vertical-align:middle;"></i> <?php echo htmlspecialchars($row['no_hp']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['peran'] === 'penilai_1'): ?>
                                                <span class="badge-role-p1">
                                                    <i data-lucide="star" style="width:12px;height:12px;"></i> PENILAI 1 (KETUA)
                                                </span>
                                            <?php else: ?>
                                                <span class="badge-role-p2">
                                                    <i data-lucide="user-check" style="width:12px;height:12px;"></i> PENILAI 2 (ANGGOTA)
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-secondary" style="font-size: 0.83rem;">
                                                <?php echo htmlspecialchars($row['jabatan'] ?: 'Pengawas Madrasah'); ?>
                                            </div>
                                            <div style="font-size: 0.75rem; color: #94a3b8;">
                                                <?php echo htmlspecialchars($row['instansi'] ?: 'Kemenag'); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="credential-pill">
                                                <i data-lucide="user" style="width:12px;height:12px;color:#059669;"></i>
                                                <span><?php echo htmlspecialchars($row['username'] ?? '-'); ?></span>
                                            </div>
                                            <div style="font-size: 0.72rem; color: #64748b; margin-top: 3px;">
                                                Status: <span class="badge bg-success-subtle text-success fw-bold">Aktif</span>
                                            </div>
                                        </td>
                                        <td style="text-align: right;">
                                            <div class="d-inline-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-primary" style="border-radius: 8px; padding: 5px 10px;" title="Edit Data Akun" onclick='editPengawas(<?php echo json_encode($row); ?>)'>
                                                    <i data-lucide="edit-3" style="width:14px;height:14px;"></i>
                                                </button>
                                                <form action="/admin/pkkm/pengawas/delete" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus akun pengawas ini?')">
                                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 8px; padding: 5px 10px;" title="Hapus Akun">
                                                        <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </main>
</div>

<!-- Modal Form Tambah/Edit Pengawas -->
<div class="modal fade" id="modalPengawas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 18px; border: none; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #064e3b 0%, #047857 100%); color: #fff; padding: 18px 24px;">
                <h5 class="modal-title fw-bold" id="modalPengawasTitle">
                    <i data-lucide="user-check" style="width:20px;height:20px;vertical-align:middle;"></i> Tambah Akun Pengawas Penilai
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="/admin/pkkm/pengawas/save" method="POST">
                <input type="hidden" name="id" id="pg_id" value="0">
                <div class="modal-body" style="padding: 24px;">
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 0.85rem;">Peran Penilai <span class="text-danger">*</span></label>
                        <select name="peran" id="pg_peran" class="form-select" style="border-radius: 10px;" required>
                            <option value="penilai_1">Penilai 1 (Ketua Tim Asesor)</option>
                            <option value="penilai_2">Penilai 2 (Anggota Tim Asesor)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 0.85rem;">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="pg_nama" class="form-control" placeholder="Contoh: H. AHMAD SYARIF, M.Pd.I" style="border-radius: 10px;" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold" style="font-size: 0.85rem;">NIP Pengawas</label>
                            <input type="text" name="nip" id="pg_nip" class="form-control" placeholder="19740512..." style="border-radius: 10px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold" style="font-size: 0.85rem;">No. WhatsApp / HP</label>
                            <input type="text" name="no_hp" id="pg_no_hp" class="form-control" placeholder="0812..." style="border-radius: 10px;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 0.85rem;">Jabatan & Instansi</label>
                        <input type="text" name="jabatan" id="pg_jabatan" class="form-control mb-2" placeholder="Jabatan: Pengawas Madrasah Ahli Madya" style="border-radius: 10px;" value="Pengawas Madrasah Ahli Madya">
                        <input type="text" name="instansi" id="pg_instansi" class="form-control" placeholder="Instansi: Kantor Kemenag Kab. Lampung Barat" style="border-radius: 10px;" value="Kantor Kementerian Agama Kab. Lampung Barat">
                    </div>

                    <hr class="my-3 text-muted">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Username Login <span class="text-danger">*</span></label>
                            <input type="text" name="username" id="pg_username" class="form-control" placeholder="penilai1" style="border-radius: 10px;" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Password</label>
                            <input type="password" name="password" id="pg_password" class="form-control" placeholder="Kosongkan jika tdk diubah" style="border-radius: 10px;">
                            <small class="text-muted" style="font-size: 0.72rem;">Default: <code>pengawas123</code></small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 14px 24px;">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button type="submit" class="btn btn-success fw-bold px-4" style="border-radius: 10px; background: #059669; border: none;">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openModalPengawas() {
    document.getElementById('modalPengawasTitle').innerHTML = '<i data-lucide="user-plus" style="width:20px;height:20px;vertical-align:middle;"></i> Tambah Akun Pengawas Penilai';
    document.getElementById('pg_id').value = '0';
    document.getElementById('pg_nama').value = '';
    document.getElementById('pg_nip').value = '';
    document.getElementById('pg_no_hp').value = '';
    document.getElementById('pg_jabatan').value = 'Pengawas Madrasah Ahli Madya';
    document.getElementById('pg_instansi').value = 'Kantor Kementerian Agama Kab. Lampung Barat';
    document.getElementById('pg_username').value = '';
    document.getElementById('pg_password').value = '';
    document.getElementById('pg_peran').value = 'penilai_1';

    let m = new bootstrap.Modal(document.getElementById('modalPengawas'));
    m.show();
    setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 100);
}

function editPengawas(data) {
    document.getElementById('modalPengawasTitle').innerHTML = '<i data-lucide="edit-3" style="width:20px;height:20px;vertical-align:middle;"></i> Edit Akun Pengawas Penilai';
    document.getElementById('pg_id').value = data.id || '0';
    document.getElementById('pg_nama').value = data.nama || '';
    document.getElementById('pg_nip').value = data.nip || '';
    document.getElementById('pg_no_hp').value = data.no_hp || '';
    document.getElementById('pg_jabatan').value = data.jabatan || '';
    document.getElementById('pg_instansi').value = data.instansi || '';
    document.getElementById('pg_username').value = data.username || '';
    document.getElementById('pg_password').value = '';
    document.getElementById('pg_peran').value = data.peran || 'penilai_1';

    let m = new bootstrap.Modal(document.getElementById('modalPengawas'));
    m.show();
    setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 100);
}

document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') lucide.createIcons();
});
</script>
</body>
</html>
