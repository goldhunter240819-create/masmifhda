<?php
// data variables: $years, $mapels, $kelas, $title
?>
<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/_siakad_header.php'; ?>

        <div class="z-page-head-row">
            <div class="header-text">
                <h1>Input Nilai Akademik</h1>
                <p>Silakan pilih kombinasi filter untuk memulai pengisian nilai siswa.</p>
            </div>
        </div>

        <div class="z-card-center">
            <div class="z-card filter-box">
                <div class="filter-header">
                    <div class="filter-icon-box"><i data-lucide="edit-3"></i></div>
                    <h3>Pengaturan Penilaian</h3>
                </div>
                <form action="<?php echo base_url('siakad/nilai/input'); ?>" method="GET" class="z-form-vertical">
                    <div class="form-group">
                        <label>Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" required class="z-input select-modern">
                            <?php foreach($years as $y): ?>
                                <option value="<?php echo $y['id']; ?>" <?php echo $y['is_active'] ? 'selected' : ''; ?>>
                                    <?php echo $y['name']; ?> <?php echo $y['is_active'] ? '(Aktif)' : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Mata Pelajaran</label>
                        <select name="mapel_id" required class="z-input select-modern">
                            <option value="">Pilih Mapel</option>
                            <?php foreach($mapels as $m): ?>
                                <option value="<?php echo $m['id']; ?>"><?php echo $m['nama_mapel']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kelas</label>
                        <select name="kelas_id" required class="z-input select-modern">
                            <option value="">Pilih Kelas</option>
                            <?php foreach($kelas as $k): ?>
                                <option value="<?php echo $k['id']; ?>"><?php echo $k['nama_kelas']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary z-btn-full">
                        LANJUT INPUT NILAI <i data-lucide="arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* â”€â”€â”€ Layout â”€â”€â”€ */
    
    
    
    

    .z-card-center { display: flex; justify-content: center; align-items: flex-start; padding-top: 2rem; }
    .filter-box { width: 100%; max-width: 500px; padding: 2.5rem; border-radius: 32px; box-shadow: 0 20px 40px rgba(0,0,0,0.03); }
    
    .filter-header { text-align: center; margin-bottom: 2.5rem; }
    .filter-icon-box { 
        width: 64px; height: 64px; background: #f0fdf4; color: #0ea5e9; 
        border-radius: 20px; display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1.25rem;
    }
    .filter-icon-box i { width: 32px; height: 32px; }
    .filter-header h3 { margin: 0; font-size: 1.25rem; font-weight: 800; color: #1e293b; }

    .z-form-vertical { display: flex; flex-direction: column; gap: 1.5rem; }
    .form-group label { display: block; font-size: 0.85rem; font-weight: 700; color: #64748b; margin-bottom: 8px; }
    .z-input {
        width: 100%; padding: 0.85rem 1.1rem; border: 1.5px solid #e2e8f0;
        border-radius: 14px; font-size: 0.95rem; transition: 0.2s;
    }
    .z-input:focus { border-color: #0ea5e9; outline: none; box-shadow: 0 0 0 4px rgba(16,185,129,0.1); }
    
    .select-modern { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1.2em; }
    
    .z-btn-full { width: 100%; padding: 1.1rem; justify-content: center; font-size: 1rem; font-weight: 800; border-radius: 16px; margin-top: 1rem; }
</style>

<script>lucide.createIcons();</script>

