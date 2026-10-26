<?php
// data variables: $years, $kelas, $selectedKelas, $selectedTahun, $siswas, $title
?>
<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/_siakad_header.php'; ?>

        <div class="z-page-head-row">
            <div class="header-text">
                <h1>Laporan Raport Siswa</h1>
                <p>Cetak hasil evaluasi belajar siswa per semester.</p>
            </div>
        </div>

        <div class="z-card filter-card">
            <form action="<?php echo base_url('siakad/raport'); ?>" method="GET" class="filter-form-row">
                <div class="filter-col">
                    <label>Tahun Ajaran</label>
                    <select name="tahun_ajaran_id" class="z-input select-modern">
                        <?php foreach($years as $y): ?>
                            <option value="<?php echo $y['id']; ?>" <?php echo $selectedTahun == $y['id'] ? 'selected' : ''; ?>><?php echo $y['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-col">
                    <label>Kelas</label>
                    <select name="kelas_id" class="z-input select-modern">
                        <option value="">Pilih Kelas</option>
                        <?php foreach($kelas as $k): ?>
                            <option value="<?php echo $k['id']; ?>" <?php echo $selectedKelas == $k['id'] ? 'selected' : ''; ?>><?php echo $k['nama_kelas']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-col action">
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="search"></i> Cari Siswa
                    </button>
                </div>
            </form>
        </div>

        <?php if ($selectedKelas): ?>
        <div class="z-panel">
            <div class="z-table-wrap">
                <table class="">
                    <thead>
                        <tr>
                            <th style="width: 150px;">NIS</th>
                            <th>Nama Siswa</th>
                            <th class="text-center" style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($siswas as $s): ?>
                        <tr>
                            <td><span class="z-badge-code"><?php echo $s['nis']; ?></span></td>
                            <td><div class="student-name"><?php echo $s['nama']; ?></div></td>
                            <td>
                                <div class="z-action-btns justify-center">
                                    <a href="<?php echo base_url('siakad/raport/cetak/' . $s['id'] . '/' . $selectedTahun); ?>" target="_blank" class="btn z-btn-emerald-light z-btn-sm">
                                        <i data-lucide="printer"></i> Cetak Raport
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($siswas)): ?>
                            <tr><td colspan="3" class="text-center" style="padding: 3rem; color: #94a3b8;">Tidak ada data siswa di kelas ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon"><i data-lucide="users"></i></div>
                <p>Silakan pilih kelas dan tahun ajaran untuk menampilkan daftar siswa.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    /* â”€â”€â”€ Layout â”€â”€â”€ */
    
    
    
    

    /* â”€â”€â”€ Filter â”€â”€â”€ */
    .filter-card { padding: 1.5rem; border-radius: 20px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; }
    .filter-form-row { display: flex; gap: 1.5rem; align-items: flex-end; }
    .filter-col { flex: 1; }
    .filter-col.action { flex: 0; }
    .filter-col label { display: block; font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 8px; }

    /* â”€â”€â”€ Table â”€â”€â”€ */
    
    
    
    
    

    .z-badge-code { font-family: monospace; font-weight: 700; color: #475569; background: #f1f5f9; padding: 4px 10px; border-radius: 8px; }
    .student-name { font-weight: 700; color: #1e293b; font-size: 1rem; }

    .empty-state { text-align: center; padding: 5rem 2rem; color: #94a3b8; }
    .empty-icon { font-size: 4rem; margin-bottom: 1rem; opacity: 0.2; }
</style>

<script>lucide.createIcons();</script>
