<?php
$active_module = 'pkkm';
$active_page = 'tim_pkkm';

// Hitung statistik
$assigned_guru_ids = [];
$total_koordinator = 0;
foreach($tim_list as $t) {
    $assigned_guru_ids[$t['guru_id']] = true;
    if (stripos($t['peran'], 'koordinator') !== false) {
        $total_koordinator++;
    }
}
$total_guru_terlibat = count($assigned_guru_ids);

$komponen_terisi = 0;
foreach($komponens as $k) {
    if (!empty($tim_by_komponen[$k['id']])) {
        $komponen_terisi++;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title = $title_page ?? 'Tim Penanggung Jawab PKKM'; include __DIR__ . '/_zamrud_head.php'; ?>
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
        .stat-box.amber { background: linear-gradient(135deg, #fffbeb, #ffffff); border-color: #fde68a; color: #92400e; }
        .stat-box.amber .stat-icon { color: #f59e0b; }
        .stat-box.purple { background: linear-gradient(135deg, #faf5ff, #ffffff); border-color: #e9d5ff; color: #6b21a8; }
        .stat-box.purple .stat-icon { color: #8b5cf6; }
        .stat-value { font-size: 1.7rem; font-weight: 800; line-height: 1.1; margin-bottom: 2px; }
        .stat-label { font-size: 0.78rem; font-weight: 700; opacity: 0.75; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Komponen Cards Grid */
        .komponen-card-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 30px;
        }
        .komp-card {
            background: #ffffff; border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03); transition: transform 0.2s, box-shadow 0.2s;
            display: flex; flex-direction: column;
        }
        .komp-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.07); }
        .komp-card-header {
            padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;
        }
        .komp-card-1 .komp-card-header { background: linear-gradient(135deg, #ecfdf5, #ffffff); border-top: 4px solid #10b981; }
        .komp-card-2 .komp-card-header { background: linear-gradient(135deg, #eff6ff, #ffffff); border-top: 4px solid #3b82f6; }
        .komp-card-3 .komp-card-header { background: linear-gradient(135deg, #fffbeb, #ffffff); border-top: 4px solid #f59e0b; }
        .komp-card-4 .komp-card-header { background: linear-gradient(135deg, #faf5ff, #ffffff); border-top: 4px solid #8b5cf6; }

        .komp-title { font-size: 0.95rem; font-weight: 800; color: #0f172a; margin: 0; line-height: 1.3; }
        .komp-number-badge {
            width: 28px; height: 28px; border-radius: 8px; font-weight: 800; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .komp-card-1 .komp-number-badge { background: #dcfce7; color: #15803d; }
        .komp-card-2 .komp-number-badge { background: #dbeafe; color: #1e40af; }
        .komp-card-3 .komp-number-badge { background: #fef3c7; color: #b45309; }
        .komp-card-4 .komp-number-badge { background: #f3e8ff; color: #6b21a8; }

        .komp-card-body { padding: 16px 20px; flex: 1; }
        .komp-team-list { display: flex; flex-direction: column; gap: 10px; margin-bottom: 14px; }
        
        .team-member-item {
            display: flex; align-items: center; justify-content: space-between; gap: 10px;
            padding: 8px 12px; border-radius: 10px; background: #f8fafc; border: 1px solid #e2e8f0;
        }
        .member-info-wrap { display: flex; align-items: center; gap: 10px; min-width: 0; }
        .member-avatar {
            width: 34px; height: 34px; border-radius: 50%; object-fit: cover; background: #e2e8f0; font-weight: 700; color: #475569; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; flex-shrink: 0; border: 1px solid #cbd5e1;
        }
        .member-name { font-size: 0.82rem; font-weight: 700; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .member-role-tag { font-size: 0.68rem; font-weight: 700; padding: 2px 7px; border-radius: 6px; display: inline-block; margin-top: 2px; }
        .tag-koor { background: #dcfce7; color: #166534; }
        .tag-pj { background: #dbeafe; color: #1e40af; }
        .tag-anggota { background: #f1f5f9; color: #475569; }

        .komp-card-footer {
            padding: 12px 20px; border-top: 1px solid #f1f5f9; background: #fafafa;
        }
        .btn-assign-komp {
            width: 100%; padding: 8px; font-size: 0.8rem; font-weight: 700; border-radius: 8px; border: 1px dashed #cbd5e1; background: #ffffff; color: #475569; display: flex; align-items: center; justify-content: center; gap: 6px; cursor: pointer; transition: 0.2s;
        }
        .btn-assign-komp:hover { border-color: #10b981; color: #047857; background: #f0fdf4; }

        /* Glass Card & Table */
        .glass-card {
            background: #ffffff; border-radius: 18px; border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03); padding: 24px; margin-bottom: 24px;
        }
        .table-custom {
            width: 100%; border-collapse: separate; border-spacing: 0;
        }
        .table-custom th {
            background: #f8fafc; padding: 14px 16px; font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0;
        }
        .table-custom td {
            padding: 14px 16px; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; vertical-align: middle;
        }
        .table-custom tr:hover td { background-color: #fbfcfe; }

        .badge-komp {
            font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 8px; display: inline-flex; align-items: center; gap: 5px;
        }
        .b-komp-1 { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .b-komp-2 { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
        .b-komp-3 { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
        .b-komp-4 { background: #faf5ff; color: #6b21a8; border: 1px solid #e9d5ff; }

        /* Modal Styles */
        .modal-custom {
            display: none; position: fixed; z-index: 1050; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px);
        }
        .modal-custom.show { display: flex; align-items: center; justify-content: center; }
        .modal-dialog-custom {
            background: #ffffff; border-radius: 20px; width: 100%; max-width: 540px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #e2e8f0; overflow: hidden; animation: modalIn 0.2s ease-out;
        }
        @keyframes modalIn {
            from { transform: translateY(-20px) scale(0.97); opacity: 0; }
            to { transform: translateY(0) scale(1); opacity: 1; }
        }
    </style>
</head>
<body>

<div class="z-wrap">
    <?php include __DIR__ . '/_zamrud_sidebar.php'; ?>

    <main class="z-main">
        <?php $page_badge = 'Admin Portal'; include __DIR__ . '/../siakad/_siakad_header.php'; ?>
        <div class="z-scroll">
            
            <!-- Elite Header Banner -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-tag-pill"><i data-lucide="shield-check" style="width:13px;height:13px;"></i> PKKM TAHUN 2026/2027</div>
                        <h1><i data-lucide="users-2"></i> Tim Penanggung Jawab Komponen</h1>
                        <p>Atur dan tetapkan personil guru serta koordinator untuk setiap komponen PKKM agar proses pengumpulan bukti fisik lebih terstruktur dan kolaboratif.</p>
                    </div>
                    <div class="header-actions-premium d-flex gap-2">
                        <a href="/admin/pkkm/master-berkas" class="e-btn e-btn-secondary">
                            <i data-lucide="folder-open"></i> Daftar Berkas
                        </a>
                        <button onclick="openTimModal()" class="e-btn e-btn-primary">
                            <i data-lucide="user-plus"></i> Tambah Penugasan Tim
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stat Grid -->
            <div class="stat-grid">
                <div class="stat-box green">
                    <div class="stat-icon"><i data-lucide="users" style="width:24px;height:24px;"></i></div>
                    <div>
                        <div class="stat-value"><?php echo $total_guru_terlibat; ?></div>
                        <div class="stat-label">Guru Terlibat</div>
                    </div>
                </div>
                <div class="stat-box blue">
                    <div class="stat-icon"><i data-lucide="layers" style="width:24px;height:24px;"></i></div>
                    <div>
                        <div class="stat-value"><?php echo $komponen_terisi; ?> / 4</div>
                        <div class="stat-label">Komponen Terisi</div>
                    </div>
                </div>
                <div class="stat-box amber">
                    <div class="stat-icon"><i data-lucide="crown" style="width:24px;height:24px;"></i></div>
                    <div>
                        <div class="stat-value"><?php echo $total_koordinator; ?></div>
                        <div class="stat-label">Koordinator Komponen</div>
                    </div>
                </div>
                <div class="stat-box purple">
                    <div class="stat-icon"><i data-lucide="check-circle" style="width:24px;height:24px;"></i></div>
                    <div>
                        <div class="stat-value"><?php echo count($tim_list); ?></div>
                        <div class="stat-label">Total SK Penugasan</div>
                    </div>
                </div>
            </div>

            <!-- Komponen Grid Cards -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 style="font-weight: 800; margin:0; font-size:1.15rem; color:#0f172a;"><i data-lucide="grid" style="width:20px;height:20px;color:#047857;vertical-align:middle;"></i> Pembagian Tim Berdasarkan Komponen</h4>
                <span class="text-muted" style="font-size:0.85rem;">4 Komponen Utama Penilaian Kinerja Kepala Madrasah</span>
            </div>

            <div class="komponen-card-grid">
                <?php foreach($komponens as $idx => $k): 
                    $cardClass = 'komp-card-' . ($k['urutan'] ?? ($idx+1));
                    $members = $tim_by_komponen[$k['id']] ?? [];
                ?>
                <div class="komp-card <?php echo $cardClass; ?>">
                    <div class="komp-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <span class="komp-number-badge"><?php echo $k['urutan']; ?></span>
                            <div>
                                <h5 class="komp-title"><?php echo htmlspecialchars($k['nama_komponen']); ?></h5>
                                <span style="font-size:0.72rem; color:#64748b; font-weight:600;"><?php echo count($members); ?> Personil Guru Bertugas</span>
                            </div>
                        </div>
                    </div>
                    <div class="komp-card-body">
                        <?php if(empty($members)): ?>
                            <div class="text-center py-4" style="color:#94a3b8;">
                                <i data-lucide="user-x" style="width:32px;height:32px;margin-bottom:6px;opacity:0.6;"></i>
                                <div style="font-size:0.8rem; font-weight:600;">Belum ada tim yang ditugaskan</div>
                                <div style="font-size:0.75rem; color:#cbd5e1;">Klik tombol di bawah untuk menambah</div>
                            </div>
                        <?php else: ?>
                            <div class="komp-team-list">
                                <?php foreach($members as $m): 
                                    $tagClass = 'tag-anggota';
                                    if(stripos($m['peran'], 'koordinator') !== false) $tagClass = 'tag-koor';
                                    elseif(stripos($m['peran'], 'penanggung') !== false) $tagClass = 'tag-pj';
                                ?>
                                <div class="team-member-item">
                                    <div class="member-info-wrap">
                                        <?php if(!empty($m['foto']) && file_exists(__DIR__ . '/../../public' . $m['foto'])): ?>
                                            <img src="<?php echo htmlspecialchars($m['foto']); ?>" class="member-avatar" alt="Avatar">
                                        <?php else: ?>
                                            <div class="member-avatar"><?php echo strtoupper(substr($m['nama_guru'], 0, 1)); ?></div>
                                        <?php endif; ?>
                                        <div style="min-width:0;">
                                            <div class="member-name" title="<?php echo htmlspecialchars($m['nama_guru']); ?>"><?php echo htmlspecialchars($m['nama_guru']); ?></div>
                                            <span class="member-role-tag <?php echo $tagClass; ?>"><?php echo htmlspecialchars($m['peran']); ?></span>
                                            <?php if(!empty($m['tugas_keterangan'])): ?>
                                                <div style="font-size:0.68rem; color:#64748b; margin-top:2px; line-height:1.2; text-overflow:ellipsis; overflow:hidden; white-space:nowrap; max-width:180px;" title="<?php echo htmlspecialchars($m['tugas_keterangan']); ?>">
                                                    • <?php echo htmlspecialchars($m['tugas_keterangan']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <button onclick='editTim(<?php echo json_encode($m); ?>)' class="btn btn-sm btn-light p-1" title="Edit" style="border-radius:6px;">
                                            <i data-lucide="edit-2" style="width:13px;height:13px;color:#3b82f6;"></i>
                                        </button>
                                        <button onclick="deleteTim(<?php echo $m['id']; ?>, '<?php echo htmlspecialchars(addslashes($m['nama_guru'])); ?>')" class="btn btn-sm btn-light p-1" title="Hapus" style="border-radius:6px;">
                                            <i data-lucide="trash-2" style="width:13px;height:13px;color:#ef4444;"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="komp-card-footer">
                        <button onclick="openTimModal(<?php echo $k['id']; ?>)" class="btn-assign-komp">
                            <i data-lucide="user-plus" style="width:14px;height:14px;"></i> Tugaskan Guru ke Komponen <?php echo $k['urutan']; ?>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Tabel Seluruh Penugasan Tim -->
            <div class="glass-card">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <div>
                        <h4 style="font-weight: 800; margin:0; font-size:1.15rem; color:#0f172a;"><i data-lucide="list" style="width:20px;height:20px;color:#047857;vertical-align:middle;"></i> Daftar Lengkap Personil Tim PKKM</h4>
                        <span class="text-muted" style="font-size:0.85rem;">Seluruh guru yang memiliki tanggung jawab pengumpulan berkas fisik</span>
                    </div>
                    <div class="d-flex gap-2">
                        <input type="text" id="searchInput" placeholder="Cari nama guru..." class="form-control form-control-sm" style="width:200px; border-radius:10px;" onkeyup="filterTable()">
                        <select id="filterKomponen" class="form-select form-select-sm" style="width:180px; border-radius:10px;" onchange="filterTable()">
                            <option value="">Semua Komponen</option>
                            <?php foreach($komponens as $k): ?>
                                <option value="Komponen <?php echo $k['urutan']; ?>">Komponen <?php echo $k['urutan']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table-custom" id="timTable">
                        <thead>
                            <tr>
                                <th style="width:50px;">No</th>
                                <th>Guru / Tenaga Pendidik</th>
                                <th>Komponen Ditugaskan</th>
                                <th>Peran Dalam Tim</th>
                                <th>Tugas Khusus / Fokus Berkas</th>
                                <th style="text-align:center; width:100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($tim_list)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i data-lucide="users" style="width:40px;height:40px;margin-bottom:10px;opacity:0.5;"></i>
                                    <p style="margin:0; font-weight:600;">Belum ada data penugasan tim PKKM.</p>
                                    <button onclick="openTimModal()" class="btn btn-sm btn-success mt-2" style="border-radius:8px;">+ Tambah Penugasan Sekarang</button>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach($tim_list as $no => $t): 
                                    $bKompClass = 'b-komp-' . ($t['komponen_urutan'] ?? 1);
                                ?>
                                <tr class="tim-row" data-komponen="Komponen <?php echo $t['komponen_urutan']; ?>">
                                    <td style="font-weight:700; color:#94a3b8;"><?php echo $no + 1; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <?php if(!empty($t['foto']) && file_exists(__DIR__ . '/../../public' . $t['foto'])): ?>
                                                <img src="<?php echo htmlspecialchars($t['foto']); ?>" class="member-avatar" alt="Foto">
                                            <?php else: ?>
                                                <div class="member-avatar"><?php echo strtoupper(substr($t['nama_guru'], 0, 1)); ?></div>
                                            <?php endif; ?>
                                            <div>
                                                <div style="font-weight:800; color:#0f172a; font-size:0.9rem;"><?php echo htmlspecialchars($t['nama_guru']); ?></div>
                                                <div style="font-size:0.75rem; color:#64748b;">NIP: <?php echo htmlspecialchars($t['nip'] ?? '-'); ?> • <?php echo htmlspecialchars($t['jabatan'] ?? 'Guru'); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-komp <?php echo $bKompClass; ?>">
                                            <i data-lucide="tag" style="width:12px;height:12px;"></i>
                                            Komponen <?php echo $t['komponen_urutan']; ?>: <?php echo htmlspecialchars($t['nama_komponen']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if(stripos($t['peran'], 'koordinator') !== false): ?>
                                            <span class="badge bg-success" style="font-weight:700; font-size:0.78rem; padding:5px 10px; border-radius:6px;">
                                                <i data-lucide="crown" style="width:12px;height:12px;vertical-align:text-bottom;"></i> <?php echo htmlspecialchars($t['peran']); ?>
                                            </span>
                                        <?php elseif(stripos($t['peran'], 'penanggung') !== false): ?>
                                            <span class="badge bg-primary" style="font-weight:700; font-size:0.78rem; padding:5px 10px; border-radius:6px;">
                                                <i data-lucide="file-check" style="width:12px;height:12px;vertical-align:text-bottom;"></i> <?php echo htmlspecialchars($t['peran']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary" style="font-weight:700; font-size:0.78rem; padding:5px 10px; border-radius:6px;">
                                                <?php echo htmlspecialchars($t['peran']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span style="font-size:0.85rem; color:#334155;">
                                            <?php echo !empty($t['tugas_keterangan']) ? htmlspecialchars($t['tugas_keterangan']) : '<span class="text-muted fst-italic">- Semua Bukti Fisik Komponen -</span>'; ?>
                                        </span>
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button onclick='editTim(<?php echo json_encode($t); ?>)' class="btn btn-sm btn-outline-primary p-1 px-2" title="Edit Penugasan" style="border-radius:6px;">
                                                <i data-lucide="edit-3" style="width:14px;height:14px;"></i>
                                            </button>
                                            <button onclick="deleteTim(<?php echo $t['id']; ?>, '<?php echo htmlspecialchars(addslashes($t['nama_guru'])); ?>')" class="btn btn-sm btn-outline-danger p-1 px-2" title="Hapus Penugasan" style="border-radius:6px;">
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

        </div>
    </main>
</div>

<!-- Modal Form Penugasan Tim -->
<div id="timModal" class="modal-custom">
    <div class="modal-dialog-custom">
        <form method="POST" action="/admin/pkkm/tim/save" style="margin:0;">
            <input type="hidden" name="id" id="form_id" value="0">
            <div style="padding:20px 24px; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; background:linear-gradient(135deg, #f0fdf4, #ffffff);">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px; height:36px; border-radius:10px; background:#dcfce7; color:#15803d; display:flex; align-items:center; justify-content:center;">
                        <i data-lucide="user-plus" style="width:18px;height:18px;"></i>
                    </div>
                    <div>
                        <h5 style="margin:0; font-weight:800; font-size:1.1rem; color:#0f172a;" id="modalTitle">Penugasan Tim Komponen PKKM</h5>
                        <div style="font-size:0.75rem; color:#64748b;">Tentukan guru, komponen, dan perannya</div>
                    </div>
                </div>
                <button type="button" onclick="closeTimModal()" style="border:none; background:transparent; cursor:pointer; color:#94a3b8;">
                    <i data-lucide="x" style="width:20px;height:20px;"></i>
                </button>
            </div>
            
            <div style="padding:24px;">
                <!-- Pilih Guru -->
                <div class="mb-3">
                    <label class="form-label" style="font-weight:700; font-size:0.85rem; color:#1e293b;">Pilih Guru / Tendik <span class="text-danger">*</span></label>
                    <select name="guru_id" id="form_guru_id" class="form-select" required style="border-radius:10px;">
                        <option value="">-- Pilih Guru / Tendik --</option>
                        <?php foreach($guru_list as $g): ?>
                            <option value="<?php echo $g['id']; ?>">
                                <?php echo htmlspecialchars($g['nama']); ?> (<?php echo htmlspecialchars($g['nip'] ?? '-'); ?>) - <?php echo htmlspecialchars($g['jabatan'] ?? 'Guru'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Pilih Komponen -->
                <div class="mb-3">
                    <label class="form-label" style="font-weight:700; font-size:0.85rem; color:#1e293b;">Komponen Tanggung Jawab <span class="text-danger">*</span></label>
                    <select name="komponen_id" id="form_komponen_id" class="form-select" required style="border-radius:10px;">
                        <option value="">-- Pilih Komponen PKKM --</option>
                        <?php foreach($komponens as $k): ?>
                            <option value="<?php echo $k['id']; ?>">
                                Komponen <?php echo $k['urutan']; ?>: <?php echo htmlspecialchars($k['nama_komponen']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Pilih Peran -->
                <div class="mb-3">
                    <label class="form-label" style="font-weight:700; font-size:0.85rem; color:#1e293b;">Peran dalam Tim <span class="text-danger">*</span></label>
                    <select name="peran" id="form_peran" class="form-select" required style="border-radius:10px;">
                        <option value="Koordinator Komponen">Koordinator Komponen</option>
                        <option value="Penanggung Jawab Bukti Fisik" selected>Penanggung Jawab Bukti Fisik</option>
                        <option value="Notulis & Dokumentator">Notulis & Dokumentator</option>
                        <option value="Anggota Tim">Anggota Tim</option>
                    </select>
                </div>

                <!-- Keterangan / Fokus Berkas -->
                <div class="mb-3">
                    <label class="form-label" style="font-weight:700; font-size:0.85rem; color:#1e293b;">Catatan / Fokus Berkas Tugas (Opsional)</label>
                    <input type="text" name="tugas_keterangan" id="form_tugas_keterangan" class="form-control" placeholder="Contoh: Mengurus Berkas Visi-Misi, EDM & RKT" style="border-radius:10px;">
                    <div style="font-size:0.75rem; color:#94a3b8; margin-top:4px;">Akan ditampilkan di portal guru sebagai panduan fokus tugas.</div>
                </div>
            </div>

            <div style="padding:16px 24px; background:#f8fafc; border-top:1px solid #f1f5f9; display:flex; justify-content:flex-end; gap:8px;">
                <button type="button" onclick="closeTimModal()" class="btn btn-light" style="border-radius:10px; font-weight:700; font-size:0.85rem;">Batal</button>
                <button type="submit" class="btn btn-success" style="border-radius:10px; font-weight:700; font-size:0.85rem; background:#047857; border-color:#047857;">
                    <i data-lucide="check" style="width:16px;height:16px;vertical-align:text-bottom;"></i> Simpan Penugasan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Form Delete Hidden -->
<form id="deleteTimForm" method="POST" action="/admin/pkkm/tim/delete" style="display:none;">
    <input type="hidden" name="id" id="delete_tim_id">
</form>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('msg') === 'saved') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Penugasan tim komponen PKKM berhasil disimpan.',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (urlParams.get('msg') === 'deleted') {
            Swal.fire({
                icon: 'success',
                title: 'Dihapus!',
                text: 'Personil berhasil dikeluarkan dari tim komponen.',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (urlParams.get('error')) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan: ' + urlParams.get('error')
            });
        }
    });

    function openTimModal(komponenId = null) {
        document.getElementById('modalTitle').textContent = 'Penugasan Tim Komponen PKKM';
        document.getElementById('form_id').value = '0';
        document.getElementById('form_guru_id').value = '';
        document.getElementById('form_peran').value = 'Penanggung Jawab Bukti Fisik';
        document.getElementById('form_tugas_keterangan').value = '';
        
        if (komponenId) {
            document.getElementById('form_komponen_id').value = komponenId;
        } else {
            document.getElementById('form_komponen_id').value = '';
        }
        
        document.getElementById('timModal').classList.add('show');
    }

    function closeTimModal() {
        document.getElementById('timModal').classList.remove('show');
    }

    function editTim(data) {
        document.getElementById('modalTitle').textContent = 'Edit Penugasan Tim';
        document.getElementById('form_id').value = data.id;
        document.getElementById('form_guru_id').value = data.guru_id;
        document.getElementById('form_komponen_id').value = data.komponen_id;
        document.getElementById('form_peran').value = data.peran;
        document.getElementById('form_tugas_keterangan').value = data.tugas_keterangan || '';
        document.getElementById('timModal').classList.add('show');
    }

    function deleteTim(id, nama) {
        Swal.fire({
            title: 'Hapus Penugasan?',
            text: 'Apakah Anda yakin ingin menghapus penugasan ' + nama + ' dari tim komponen ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete_tim_id').value = id;
                document.getElementById('deleteTimForm').submit();
            }
        });
    }

    function filterTable() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const komp = document.getElementById('filterKomponen').value;
        const rows = document.querySelectorAll('.tim-row');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            const rowKomp = row.getAttribute('data-komponen');
            
            const matchSearch = text.includes(search);
            const matchKomp = !komp || rowKomp === komp;

            if (matchSearch && matchKomp) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>

</body>
</html>
