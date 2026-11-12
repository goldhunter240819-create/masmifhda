<!DOCTYPE html>
<html lang="id">
<head>
    <?php $page_title='Tindak Lanjut Supervisi'; include __DIR__.'/../admin/_zamrud_head.php'; ?>
    <style>
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .badge-status.selesai { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .badge-status.proses { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .badge-status.belum { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        
        .score-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 0.85rem;
        }
        .score-a { background: #dcfce7; color: #15803d; }
        .score-b { background: #e0e7ff; color: #4338ca; }
        .score-c { background: #fef3c7; color: #b45309; }
        .score-d { background: #fee2e2; color: #b91c1c; }

        .stat-card-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-top: 20px;
            margin-bottom: 24px;
        }
        .stat-mini-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 16px 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .stat-icon-wrap {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
<?php $page_badge = 'Kurikulum'; ?>
<div class="z-wrap">
    <?php include __DIR__ . '/../admin/_zamrud_sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/../siakad/_siakad_header.php'; ?>
        
        <!-- HEADER -->
        <div class="elite-page-header">
            <div class="header-bg-shapes"></div>
            <div class="header-content-inner">
                <div class="header-text-premium">
                    <div class="header-badge">PKKM KOMPONEN 4.3</div>
                    <h1><i data-lucide="arrow-right-circle"></i> Tindak Lanjut Supervisi (RTL)</h1>
                    <p>Program pembinaan guru, supervisi klinis, pelatihan MOOC Pintar, dan realisasi hasil evaluasi KBM.</p>
                </div>
                <div class="header-actions-premium" style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <a href="/kurikulum/supervisi/laporan-tindak-lanjut" class="e-btn e-btn-outline" style="background:#fff; color:#2563eb; border-color:#bfdbfe; text-decoration:none; font-weight:700;">
                        <i data-lucide="file-check"></i> Buku Laporan Hasil RTL (Bab I - IV)
                    </a>
                    <a href="/kurikulum/supervisi/tindak-lanjut/cetak" target="_blank" class="e-btn e-btn-primary" style="background:#059669; border-color:#059669; color:#fff; text-decoration:none;">
                        <i data-lucide="printer"></i> Cetak Matriks RTL (PKKM 4.3)
                    </a>
                    <a href="/kurikulum/supervisi/kelas" class="e-btn e-btn-outline" style="background:#fff; color:#475569; border-color:#cbd5e1; text-decoration:none;">
                        <i data-lucide="arrow-left"></i> Supervisi Kelas
                    </a>
                </div>
            </div>
        </div>

        <?php
        // Hitung statistik
        $totalEvaluasi = 0;
        $perluPembinaan = 0;
        $adaRtl = 0;
        $selesaiRtl = 0;

        foreach ($daftar_supervisi as $item) {
            if ($item['status'] === 'Dievaluasi' || !empty($item['nilai'])) {
                $totalEvaluasi++;
                $val = (float)$item['nilai'];
                if ($val < 75) $perluPembinaan++;
                if (!empty($item['tindak_lanjut'])) $adaRtl++;
                if (($item['tindak_lanjut_status'] ?? '') === 'Selesai') $selesaiRtl++;
            }
        }
        ?>

        <!-- STAT MINI CARDS -->
        <div class="stat-card-row">
            <div class="stat-mini-card">
                <div class="stat-icon-wrap" style="background: #ecfdf5; color: #059669;">
                    <i data-lucide="check-circle" style="width:22px; height:22px;"></i>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Sudah Disupervisi</div>
                    <div style="font-size: 1.4rem; font-weight: 800; color: #0f172a; line-height: 1.2;"><?= $totalEvaluasi ?> Guru</div>
                </div>
            </div>

            <div class="stat-mini-card">
                <div class="stat-icon-wrap" style="background: #fffbeb; color: #d97706;">
                    <i data-lucide="alert-triangle" style="width:22px; height:22px;"></i>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Perlu Pembinaan (&lt;75)</div>
                    <div style="font-size: 1.4rem; font-weight: 800; color: #0f172a; line-height: 1.2;"><?= $perluPembinaan ?> Guru</div>
                </div>
            </div>

            <div class="stat-mini-card">
                <div class="stat-icon-wrap" style="background: #eff6ff; color: #2563eb;">
                    <i data-lucide="clipboard-list" style="width:22px; height:22px;"></i>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Rencana RTL Siap</div>
                    <div style="font-size: 1.4rem; font-weight: 800; color: #0f172a; line-height: 1.2;"><?= $adaRtl ?> Guru</div>
                </div>
            </div>

            <div class="stat-mini-card">
                <div class="stat-icon-wrap" style="background: #f0fdf4; color: #16a34a;">
                    <i data-lucide="award" style="width:22px; height:22px;"></i>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Tindak Lanjut Selesai</div>
                    <div style="font-size: 1.4rem; font-weight: 800; color: #0f172a; line-height: 1.2;"><?= $selesaiRtl ?> Guru</div>
                </div>
            </div>
        </div>

        <?php if(isset($_SESSION['alert'])): ?>
        <div style="padding: 12px 18px; border-radius: 10px; margin-bottom: 20px; font-weight: 600; font-size: 0.88rem; background: <?= $_SESSION['alert']['type'] === 'success' ? '#ecfdf5; color: #065f46; border: 1px solid #a7f3d0;' : '#fef2f2; color: #991b1b; border: 1px solid #fecaca;' ?>">
            <?= htmlspecialchars($_SESSION['alert']['message']) ?>
        </div>
        <?php unset($_SESSION['alert']); endif; ?>

        <!-- TABEL TINDAK LANJUT -->
        <div class="z-panel">
            <div class="z-panel-head" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                <div class="z-panel-title">
                    <i data-lucide="list"></i> Matriks Tindak Lanjut Hasil Supervisi Guru
                </div>
                <div style="font-size:0.8rem; color:#64748b;">
                    Standar Bukti Fisik PKKM Butir 4.3.1 (Perencanaan, Pelaksanaan & Realisasi RTL)
                </div>
            </div>
            <div style="padding: 0; overflow-x: auto;">
                <table class="z-table">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">No</th>
                            <th>Guru & Supervisor</th>
                            <th>Mapel / Kelas</th>
                            <th style="width: 100px; text-align: center;">Skor Hasil</th>
                            <th>Catatan / Masalah KBM</th>
                            <th>Bentuk Tindak Lanjut</th>
                            <th>Target Waktu</th>
                            <th style="width: 130px; text-align: center;">Status</th>
                            <th style="width: 100px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($daftar_supervisi)): ?>
                        <tr>
                            <td colspan="9" align="center" style="padding: 3rem;">
                                <div style="color: var(--z-muted);">
                                    <i data-lucide="clipboard-x" style="width:48px;height:48px;opacity:0.3;margin-bottom:1rem;"></i>
                                    <p>Belum ada data supervisi guru yang terdata.</p>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php 
                            $no = 1;
                            foreach($daftar_supervisi as $row): 
                                $nilai = (float)($row['nilai'] ?? 0);
                                $isEvaluated = ($row['status'] === 'Dievaluasi' || $nilai > 0);
                                
                                if($nilai >= 91) { $gradeClass = 'score-a'; $gradeLabel = 'Amat Baik'; }
                                elseif($nilai >= 81) { $gradeClass = 'score-b'; $gradeLabel = 'Baik'; }
                                elseif($nilai >= 71) { $gradeClass = 'score-c'; $gradeLabel = 'Cukup'; }
                                else { $gradeClass = 'score-d'; $gradeLabel = 'Kurang'; }

                                $statusRtl = $row['tindak_lanjut_status'] ?? 'Belum Terlaksana';
                                $badgeClass = 'belum';
                                if ($statusRtl === 'Selesai') $badgeClass = 'selesai';
                                elseif ($statusRtl === 'Dalam Proses') $badgeClass = 'proses';

                                // Saran default jika belum terisi
                                $jenisDefault = $row['tindak_lanjut_jenis'] ?? '';
                                $deskripsiDefault = $row['tindak_lanjut'] ?? '';
                                if (empty($deskripsiDefault) && $isEvaluated) {
                                    if ($nilai < 70) {
                                        $jenisDefault = 'Supervisi Klinis & MOOC Pintar';
                                        $deskripsiDefault = 'Bimbingan klinis intensif perencanaan modul ajar dan rekomendasi pelatihan MOOC PINTAR Kemenag.';
                                    } elseif ($nilai < 80) {
                                        $jenisDefault = 'Workshop Kombel KKG';
                                        $deskripsiDefault = 'Pendampingan variasi media pembelajaran interaktif berbasis TIK di Komunitas Belajar KKG.';
                                    } else {
                                        $jenisDefault = 'Sharing Praktik Baik';
                                        $deskripsiDefault = 'Penguatan pembelajaran berdiferensiasi dan menjadi model sharing praktik baik guru sejawat.';
                                    }
                                }
                            ?>
                            <tr>
                                <td align="center" style="vertical-align:middle; font-weight:bold;"><?= $no++ ?></td>
                                <td style="vertical-align:middle;">
                                    <div style="font-weight:700; color:#1e293b;"><?= htmlspecialchars($row['nama_guru']) ?></div>
                                    <div style="font-size:0.75rem; color:#64748b;">NIP: <?= htmlspecialchars($row['guru_nip'] ?: '-') ?></div>
                                    <div style="font-size:0.75rem; color:#059669; margin-top:2px;">
                                        <i data-lucide="user-check" style="width:12px; height:12px; display:inline;"></i> Spv: <?= htmlspecialchars($row['nama_supervisor']) ?>
                                    </div>
                                </td>
                                <td style="vertical-align:middle;">
                                    <div style="font-weight:600; color:#334155;"><?= htmlspecialchars($row['nama_mapel']) ?></div>
                                    <div style="font-size:0.75rem; color:#64748b;">Kelas <?= htmlspecialchars($row['nama_kelas']) ?></div>
                                </td>
                                <td align="center" style="vertical-align:middle;">
                                    <?php if($isEvaluated): ?>
                                    <div class="score-badge <?= $gradeClass ?>"><?= $nilai ?></div>
                                    <div style="font-size:0.72rem; color:#64748b; margin-top:3px;"><?= $gradeLabel ?></div>
                                    <?php else: ?>
                                    <span style="font-size:0.75rem; color:#94a3b8; font-style:italic;">Belum Nilai</span>
                                    <?php endif; ?>
                                </td>
                                <td style="vertical-align:middle; max-width:240px;">
                                    <div style="font-size:0.83rem; color:#475569; line-height:1.4;">
                                        <?= !empty($row['catatan_evaluasi']) ? htmlspecialchars($row['catatan_evaluasi']) : '<span style="color:#94a3b8; font-style:italic;">Belum ada catatan khusus</span>' ?>
                                    </div>
                                </td>
                                <td style="vertical-align:middle; max-width:260px;">
                                    <?php if(!empty($row['tindak_lanjut_jenis'])): ?>
                                    <div style="font-size:0.72rem; font-weight:700; color:#2563eb; text-transform:uppercase; margin-bottom:2px;">
                                        <?= htmlspecialchars($row['tindak_lanjut_jenis']) ?>
                                    </div>
                                    <?php endif; ?>
                                    <div style="font-size:0.83rem; color:#1e293b; line-height:1.4;">
                                        <?= !empty($row['tindak_lanjut']) ? htmlspecialchars($row['tindak_lanjut']) : htmlspecialchars($deskripsiDefault) ?>
                                    </div>
                                    <?php if(!empty($row['tindak_lanjut_realisasi'])): ?>
                                    <div style="font-size:0.75rem; color:#059669; margin-top:4px; border-top:1px dashed #cbd5e1; padding-top:3px;">
                                        <strong>Realisasi:</strong> <?= htmlspecialchars($row['tindak_lanjut_realisasi']) ?>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td style="vertical-align:middle; font-size:0.82rem; color:#64748b;">
                                    <?= !empty($row['tindak_lanjut_deadline']) ? htmlspecialchars($row['tindak_lanjut_deadline']) : '<span style="color:#94a3b8;">1-2 Pekan</span>' ?>
                                </td>
                                <td align="center" style="vertical-align:middle;">
                                    <span class="badge-status <?= $badgeClass ?>">
                                        <i data-lucide="<?= $statusRtl === 'Selesai' ? 'check' : ($statusRtl === 'Dalam Proses' ? 'clock' : 'alert-circle') ?>" style="width:12px; height:12px;"></i>
                                        <?= htmlspecialchars($statusRtl) ?>
                                    </span>
                                </td>
                                <td align="center" style="vertical-align:middle;">
                                    <button class="e-btn e-btn-outline" style="padding:6px 12px; font-size:0.78rem; display:inline-flex; align-items:center; gap:4px;" 
                                            onclick="openModalRtl(<?= htmlspecialchars(json_encode([
                                                'id' => $row['id'],
                                                'nama_guru' => $row['nama_guru'],
                                                'nilai' => $nilai,
                                                'catatan' => $row['catatan_evaluasi'] ?? '',
                                                'jenis' => $row['tindak_lanjut_jenis'] ?: $jenisDefault,
                                                'tindak_lanjut' => $row['tindak_lanjut'] ?: $deskripsiDefault,
                                                'deadline' => $row['tindak_lanjut_deadline'] ?: 'Semester Ganjil 2026/2027',
                                                'status' => $statusRtl,
                                                'realisasi' => $row['tindak_lanjut_realisasi'] ?? ''
                                            ])) ?>)">
                                        <i data-lucide="edit-3" style="width:14px; height:14px;"></i> Atur RTL
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- MODAL FORM ATUR RTL -->
<div id="modalRtl" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); z-index:9999; align-items:center; justify-content:center; padding:16px;">
    <div style="background:#fff; border-radius:16px; width:100%; max-width:600px; box-shadow:0 20px 40px rgba(0,0,0,0.2); overflow:hidden; animation: modalPop 0.2s ease-out;">
        <div style="background:linear-gradient(135deg, #059669 0%, #047857 100%); color:#fff; padding:18px 24px; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px; opacity:0.85;">Form Rencana Tindak Lanjut (RTL)</div>
                <h3 style="margin:2px 0 0 0; font-size:1.15rem; font-weight:700;" id="m_guru">Nama Guru</h3>
            </div>
            <button onclick="closeModalRtl()" style="background:none; border:none; color:#fff; cursor:pointer; font-size:1.5rem; line-height:1;">&times;</button>
        </div>
        
        <form action="/kurikulum/supervisi/tindak-lanjut/update" method="POST" style="padding:24px;">
            <input type="hidden" name="id" id="m_id">

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div>
                    <label style="display:block; font-size:0.8rem; font-weight:700; color:#334155; margin-bottom:6px;">Bentuk / Jenis Pembinaan</label>
                    <select name="tindak_lanjut_jenis" id="m_jenis" style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit; font-size:0.88rem;">
                        <option value="Supervisi Klinis & Bimbingan Individual">Supervisi Klinis & Bimbingan Individual</option>
                        <option value="Pelatihan MOOC PINTAR Kemenag">Pelatihan MOOC PINTAR Kemenag</option>
                        <option value="Workshop Komunitas Belajar (Kombel) KKG">Workshop Komunitas Belajar (Kombel) KKG</option>
                        <option value="Pemodelan & Sharing Teman Sejawat">Pemodelan & Sharing Teman Sejawat</option>
                        <option value="Pendampingan Mandiri oleh Supervisor">Pendampingan Mandiri oleh Supervisor</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:0.8rem; font-weight:700; color:#334155; margin-bottom:6px;">Target Waktu Pelaksanaan</label>
                    <input type="text" name="tindak_lanjut_deadline" id="m_deadline" placeholder="Misal: 30 Oktober 2026" style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit; font-size:0.88rem;">
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:0.8rem; font-weight:700; color:#334155; margin-bottom:6px;">Rencana / Uraian Tindak Lanjut</label>
                <textarea name="tindak_lanjut" id="m_tindak_lanjut" rows="3" placeholder="Jelaskan tindakan pembinaan yang akan dilakukan untuk guru ini..." style="width:100%; padding:10px 12px; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit; font-size:0.88rem; line-height:1.5;"></textarea>
            </div>

            <div style="display:grid; grid-template-columns:1fr 2fr; gap:16px; margin-bottom:20px;">
                <div>
                    <label style="display:block; font-size:0.8rem; font-weight:700; color:#334155; margin-bottom:6px;">Status Realisasi</label>
                    <select name="tindak_lanjut_status" id="m_status" style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit; font-size:0.88rem;">
                        <option value="Belum Terlaksana">Belum Terlaksana</option>
                        <option value="Dalam Proses">Dalam Proses</option>
                        <option value="Selesai">Selesai (Terlaksana)</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:0.8rem; font-weight:700; color:#334155; margin-bottom:6px;">Catatan Realisasi / Hasil Pembinaan</label>
                    <input type="text" name="tindak_lanjut_realisasi" id="m_realisasi" placeholder="Misal: Modul ajar telah direvisi & media Quizizz aktif" style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:8px; font-family:inherit; font-size:0.88rem;">
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid #e2e8f0; padding-top:16px;">
                <button type="button" onclick="closeModalRtl()" style="padding:9px 16px; background:#f1f5f9; color:#475569; border:none; border-radius:8px; font-weight:600; cursor:pointer;">Batal</button>
                <button type="submit" style="padding:9px 20px; background:#059669; color:#fff; border:none; border-radius:8px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:6px;">
                    <i data-lucide="check" style="width:16px; height:16px;"></i> Simpan Rencana Tindak Lanjut
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    if (typeof lucide !== 'undefined') lucide.createIcons();

    function openModalRtl(data) {
        document.getElementById('m_id').value = data.id;
        document.getElementById('m_guru').innerText = data.nama_guru + ' (Nilai: ' + data.nilai + ')';
        document.getElementById('m_jenis').value = data.jenis || 'Supervisi Klinis & Bimbingan Individual';
        document.getElementById('m_deadline').value = data.deadline || 'Semester Ganjil 2026/2027';
        document.getElementById('m_tindak_lanjut').value = data.tindak_lanjut || '';
        document.getElementById('m_status').value = data.status || 'Belum Terlaksana';
        document.getElementById('m_realisasi').value = data.realisasi || '';

        const modal = document.getElementById('modalRtl');
        modal.style.display = 'flex';
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function closeModalRtl() {
        document.getElementById('modalRtl').style.display = 'none';
    }

    window.onclick = function(e) {
        const modal = document.getElementById('modalRtl');
        if (e.target === modal) closeModalRtl();
    }
</script>
</body>
</html>
