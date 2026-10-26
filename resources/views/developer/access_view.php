<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="siakad-main">
        <!-- Developer Header -->
        <?php include __DIR__ . '/header.php'; ?>

        <div class="card" style="padding: 1.5rem;">
            <h3 style="margin:0 0 5px 0; color: #0ea5e9; display:flex; align-items:center; gap:8px;">
                <i data-lucide="list-tree"></i> Matrix Hak Akses Menu
            </h3>
            <p style="font-size: 0.9rem; color: #94a3b8; margin-bottom: 1.5rem;">Centang untuk menampilkan menu pada role tertentu.</p>
            
            <?php if(isset($_GET['success'])): ?>
                <div style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9; padding: 0.8rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid rgba(14, 165, 233, 0.3); font-size: 0.9rem;">
                    <i data-lucide="check-circle" style="vertical-align: -3px; width: 16px; height: 16px;"></i> Konfigurasi menu berhasil disimpan!
                </div>
            <?php endif; ?>

            <form action="<?php echo base_url('dev-panel/access/save'); ?>" method="POST">
                <?php 
                    $menuLabels = [
                        'siakad' => [
                            'dashboard' => ['icon' => 'layout-dashboard', 'label' => 'Dashboard Akademik'],
                            'data_master' => ['icon' => 'database', 'label' => 'Master Data & Referensi'],
                            'manajemen_guru' => ['icon' => 'users', 'label' => 'Manajemen Data Guru & Tendik'],
                            'manajemen_siswa' => ['icon' => 'user-square', 'label' => 'Manajemen Data Siswa'],
                            'manajemen_nilai' => ['icon' => 'calculator', 'label' => 'Manajemen Nilai (Admin)'],
                            'input_nilai_guru' => ['icon' => 'pen-tool', 'label' => 'Input Nilai (Guru)'],
                            'raport' => ['icon' => 'file-spreadsheet', 'label' => 'E-Raport & Cetak']
                        ],
                        'cbt' => [
                            'dashboard' => ['icon' => 'layout-dashboard', 'label' => 'Dashboard Ujian'],
                            'bank_soal' => ['icon' => 'file-text', 'label' => 'Bank Soal & Materi'],
                            'manajemen_ujian' => ['icon' => 'calendar-days', 'label' => 'Manajemen Ujian & Jadwal'],
                            'peserta_ujian' => ['icon' => 'users-2', 'label' => 'Data Peserta Ujian'],
                            'hasil_evaluasi' => ['icon' => 'bar-chart-3', 'label' => 'Hasil Evaluasi & Analisis'],
                            'pengawasan' => ['icon' => 'eye', 'label' => 'Live Pengawasan Ujian']
                        ]
                    ];
                ?>

                <h4 style="color: #3b82f6; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px; margin-bottom: 1rem; text-shadow: 0 0 10px rgba(59,130,246,0.5);"><i data-lucide="graduation-cap" style="width:18px; height:18px; vertical-align:-3px; margin-right:5px;"></i> Modul Utama: SIAKAD</h4>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 2rem; font-size:0.9rem;">
                    <tr>
                        <th style="padding: 12px; text-align: left;">Menu Name</th>
                        <th style="padding: 12px; text-align: center;">Admin</th>
                        <th style="padding: 12px; text-align: center;">Guru</th>
                        <th style="padding: 12px; text-align: center;">Siswa</th>
                        <th style="padding: 12px; text-align: center; color:#0ea5e9;">Dev</th>
                    </tr>
                    <?php foreach($permissions['siakad'] as $menu => $roles): ?>
                    <tr>
                        <td style="padding: 12px; color: #f8fafc; font-weight: 500;">
                            <?php 
                            $icon = isset($menuLabels['siakad'][$menu]['icon']) ? $menuLabels['siakad'][$menu]['icon'] : 'menu';
                            $label = isset($menuLabels['siakad'][$menu]['label']) ? $menuLabels['siakad'][$menu]['label'] : str_replace('_', ' ', strtoupper($menu));
                            ?>
                            <i data-lucide="<?php echo $icon; ?>" style="width:14px; height:14px; color:#94a3b8; margin-right:8px; vertical-align:-2px;"></i>
                            <?php echo $label; ?>
                        </td>
                        <td style="text-align: center;">
                            <label class="custom-checkbox"><input type="checkbox" name="perms[siakad][<?php echo $menu; ?>][]" value="1" <?php echo in_array(1, $roles) ? 'checked' : ''; ?>><span class="check-slider"></span></label>
                        </td>
                        <td style="text-align: center;">
                            <label class="custom-checkbox"><input type="checkbox" name="perms[siakad][<?php echo $menu; ?>][]" value="2" <?php echo in_array(2, $roles) ? 'checked' : ''; ?>><span class="check-slider"></span></label>
                        </td>
                        <td style="text-align: center;">
                            <label class="custom-checkbox"><input type="checkbox" name="perms[siakad][<?php echo $menu; ?>][]" value="3" <?php echo in_array(3, $roles) ? 'checked' : ''; ?>><span class="check-slider"></span></label>
                        </td>
                        <td style="text-align: center;">
                            <label class="custom-checkbox"><input type="checkbox" name="perms[siakad][<?php echo $menu; ?>][]" value="99" <?php echo in_array(99, $roles) ? 'checked' : ''; ?>><span class="check-slider"></span></label>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>

                <h4 style="color: #ec4899; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px; margin-bottom: 1rem; text-shadow: 0 0 10px rgba(236,72,153,0.5);"><i data-lucide="monitor-play" style="width:18px; height:18px; vertical-align:-3px; margin-right:5px;"></i> Modul Utama: GHCBT</h4>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 2rem; font-size:0.9rem;">
                    <tr>
                        <th style="padding: 12px; text-align: left;">Menu Name</th>
                        <th style="padding: 12px; text-align: center;">Admin</th>
                        <th style="padding: 12px; text-align: center;">Guru</th>
                        <th style="padding: 12px; text-align: center;">Siswa</th>
                        <th style="padding: 12px; text-align: center; color:#0ea5e9;">Dev</th>
                    </tr>
                    <?php foreach($permissions['cbt'] as $menu => $roles): ?>
                    <tr>
                        <td style="padding: 12px; color: #f8fafc; font-weight: 500;">
                            <?php 
                            $icon = isset($menuLabels['cbt'][$menu]['icon']) ? $menuLabels['cbt'][$menu]['icon'] : 'menu';
                            $label = isset($menuLabels['cbt'][$menu]['label']) ? $menuLabels['cbt'][$menu]['label'] : str_replace('_', ' ', strtoupper($menu));
                            ?>
                            <i data-lucide="<?php echo $icon; ?>" style="width:14px; height:14px; color:#94a3b8; margin-right:8px; vertical-align:-2px;"></i>
                            <?php echo $label; ?>
                        </td>
                        <td style="text-align: center;">
                            <label class="custom-checkbox"><input type="checkbox" name="perms[cbt][<?php echo $menu; ?>][]" value="1" <?php echo in_array(1, $roles) ? 'checked' : ''; ?>><span class="check-slider"></span></label>
                        </td>
                        <td style="text-align: center;">
                            <label class="custom-checkbox"><input type="checkbox" name="perms[cbt][<?php echo $menu; ?>][]" value="2" <?php echo in_array(2, $roles) ? 'checked' : ''; ?>><span class="check-slider"></span></label>
                        </td>
                        <td style="text-align: center;">
                            <label class="custom-checkbox"><input type="checkbox" name="perms[cbt][<?php echo $menu; ?>][]" value="3" <?php echo in_array(3, $roles) ? 'checked' : ''; ?>><span class="check-slider"></span></label>
                        </td>
                        <td style="text-align: center;">
                            <label class="custom-checkbox"><input type="checkbox" name="perms[cbt][<?php echo $menu; ?>][]" value="99" <?php echo in_array(99, $roles) ? 'checked' : ''; ?>><span class="check-slider"></span></label>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 12px; font-weight:bold; letter-spacing:1px;"><i data-lucide="shield-check"></i> SIMPAN KONFIGURASI MATRIX</button>
            </form>
        </div>

        <!-- Developer Footer -->
        <?php include __DIR__ . '/footer.php'; ?>
    </div>
</div>

<script>lucide.createIcons();</script>

