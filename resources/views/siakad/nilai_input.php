<?php
// data variables: $tahunId, $mapelId, $kelasId, $kelasInfo, $mapelInfo, $tahunInfo, $students, $gradeMap, $title
?>
<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/_siakad_header.php'; ?>

        <div class="z-page-head-row">
            <div class="header-text">
                <a href="<?php echo base_url('siakad/nilai'); ?>" class="back-link"><i data-lucide="arrow-left"></i> Kembali ke Filter</a>
                <h1><?php echo $mapelInfo['nama_mapel']; ?></h1>
                <div class="header-badges">
                    <span class="z-badge-emerald">KELAS <?php echo $kelasInfo['nama_kelas']; ?></span>
                    <span class="z-badge-blue"><?php echo $tahunInfo['name']; ?></span>
                </div>
            </div>
        </div>

        <form action="<?php echo base_url('siakad/nilai/save'); ?>" method="POST">
            <input type="hidden" name="tahun_ajaran_id" value="<?php echo $tahunId; ?>">
            <input type="hidden" name="mapel_id" value="<?php echo $mapelId; ?>">
            <input type="hidden" name="kelas_id" value="<?php echo $kelasId; ?>">

            <div class="z-panel">
                <div class="z-table-wrap">
                    <table class="">
                        <thead>
                            <tr>
                                <th style="width: 300px;">Nama Siswa</th>
                                <th class="text-center">Tugas (30%)</th>
                                <th class="text-center">UTS (30%)</th>
                                <th class="text-center">UAS (40%)</th>
                                <th class="text-center">Nilai Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($students as $s): 
                                $g = isset($gradeMap[$s['id']]) ? $gradeMap[$s['id']] : null;
                            ?>
                            <tr>
                                <td>
                                    <div class="student-name"><?php echo $s['nama']; ?></div>
                                    <div class="student-sub"><?php echo $s['nis']; ?></div>
                                </td>
                                <td class="text-center">
                                    <input type="number" name="nilai[<?php echo $s['id']; ?>][tugas]" step="0.01" value="<?php echo $g ? $g['nilai_tugas'] : 0; ?>" class="z-input-nilai" data-siswa="<?php echo $s['id']; ?>" data-type="tugas">
                                </td>
                                <td class="text-center">
                                    <input type="number" name="nilai[<?php echo $s['id']; ?>][uts]" step="0.01" value="<?php echo $g ? $g['nilai_uts'] : 0; ?>" class="z-input-nilai" data-siswa="<?php echo $s['id']; ?>" data-type="uts">
                                </td>
                                <td class="text-center">
                                    <input type="number" name="nilai[<?php echo $s['id']; ?>][uas]" step="0.01" value="<?php echo $g ? $g['nilai_uas'] : 0; ?>" class="z-input-nilai" data-siswa="<?php echo $s['id']; ?>" data-type="uas">
                                </td>
                                <td class="text-center">
                                    <span id="akhir-<?php echo $s['id']; ?>" class="final-score"><?php echo $g ? number_format($g['nilai_akhir'], 2) : '0.00'; ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary z-btn-lg">
                    SIMPAN SEMUA NILAI <i data-lucide="save"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* â”€â”€â”€ Layout â”€â”€â”€ */
    
    .back-link { display: flex; align-items: center; gap: 6px; text-decoration: none; color: #0ea5e9; font-size: 0.85rem; font-weight: 700; margin-bottom: 1rem; }
    .page-header h1 { font-size: 2rem; font-weight: 900; color: #0f172a; margin: 0; letter-spacing: -1px; }
    .header-badges { display: flex; gap: 8px; margin-top: 10px; }

    /* â”€â”€â”€ Table â”€â”€â”€ */
    
    
    
    
    

    .student-name { font-weight: 700; color: #1e293b; font-size: 1rem; }
    .student-sub { font-size: 0.8rem; color: #94a3b8; font-family: monospace; }

    .z-input-nilai {
        width: 80px; padding: 0.65rem; border: 1.5px solid #e2e8f0;
        border-radius: 12px; font-size: 1rem; font-weight: 700; text-align: center;
        transition: 0.2s; background: #f8fafc;
    }
    .z-input-nilai:focus { border-color: #0ea5e9; outline: none; background: white; box-shadow: 0 0 0 4px rgba(16,185,129,0.1); }

    .final-score { font-size: 1.1rem; font-weight: 900; color: #0ea5e9; font-family: 'JetBrains Mono', monospace; }

    .form-actions { display: flex; justify-content: flex-end; margin-top: 2rem; padding-bottom: 3rem; }
</style>

<script>
    document.querySelectorAll('.z-input-nilai').forEach(input => {
        input.addEventListener('input', function() {
            const sid = this.getAttribute('data-siswa');
            const tugas = parseFloat(document.querySelector(`.z-input-nilai[data-siswa="${sid}"][data-type="tugas"]`).value) || 0;
            const uts = parseFloat(document.querySelector(`.z-input-nilai[data-siswa="${sid}"][data-type="uts"]`).value) || 0;
            const uas = parseFloat(document.querySelector(`.z-input-nilai[data-siswa="${sid}"][data-type="uas"]`).value) || 0;
            
            const akhir = (tugas * 0.3) + (uts * 0.3) + (uas * 0.4);
            document.getElementById(`akhir-${sid}`).innerText = akhir.toFixed(2);
        });
    });
    lucide.createIcons();
</script>

