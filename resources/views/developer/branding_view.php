<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="siakad-main">
        <!-- Developer Header -->
        <?php include __DIR__ . '/header.php'; ?>

        <div class="card" style="margin-bottom: 2rem; background: transparent; padding: 1.5rem; border: none; box-shadow: none;">
            <h2 style="margin: 0; display: flex; align-items: center; gap: 10px; color: #0ea5e9; text-shadow: 0 0 15px rgba(16,185,129,0.5);">
                <i data-lucide="palette"></i> Global Identity Hub
            </h2>
            <p style="opacity: 0.7; margin-top: 5px; color: #94a3b8;">Atur identitas aplikasi (Nama, Logo, Favicon, Tema) secara terpusat.</p>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid rgba(14, 165, 233, 0.3); box-shadow: 0 0 20px rgba(16,185,129,0.1);">
                <i data-lucide="check-circle" style="vertical-align: -5px; width: 20px; height: 20px;"></i> Konfigurasi branding berhasil diperbarui!
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem;">
            <?php foreach($apps as $app): ?>
            <div class="card" style="padding: 1.5rem; position: relative; overflow: hidden;">
                <!-- Decorative Glow -->
                <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: <?php echo $app['theme_color']; ?>; filter: blur(50px); opacity: 0.3; border-radius: 50%;"></div>

                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 15px; position:relative; z-index:1;">
                    <?php if($app['logo_path']): ?>
                        <img src="<?php echo base_url('public/images/' . $app['logo_path']); ?>" alt="Logo" style="height: 45px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.5);">
                    <?php else: ?>
                        <div style="width: 45px; height: 45px; background: rgba(255,255,255,0.05); border-radius: 10px; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(255,255,255,0.1);">
                            <i data-lucide="image" style="color: #64748b;"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h3 style="margin: 0; font-size: 1.3rem; color: #f8fafc; text-shadow: 0 2px 4px rgba(0,0,0,0.5);"><?php echo strtoupper($app['app_code']); ?></h3>
                        <p style="margin: 0; color: #94a3b8; font-size: 0.85rem;"><?php echo $app['app_name']; ?> <span style="background:rgba(255,255,255,0.1); padding: 2px 6px; border-radius:4px; font-size: 0.75rem;">v<?php echo $app['app_version']; ?></span></p>
                    </div>
                </div>

                <form action="<?php echo base_url('dev-panel/branding/save'); ?>" method="POST" enctype="multipart/form-data" style="position:relative; z-index:1;">
                    <input type="hidden" name="app_code" value="<?php echo $app['app_code']; ?>">
                    
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-size: 0.85rem; color: #cbd5e1; margin-bottom: 5px; font-weight: 500;">Nama Aplikasi</label>
                        <input type="text" name="app_name" value="<?php echo $app['app_name']; ?>" class="form-control" style="width: 100%; padding: 10px 12px; border-radius: 8px;">
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; color: #cbd5e1; margin-bottom: 5px; font-weight: 500;">Versi</label>
                            <input type="text" name="app_version" value="<?php echo $app['app_version']; ?>" class="form-control" style="width: 100%; padding: 10px 12px; border-radius: 8px;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.85rem; color: #cbd5e1; margin-bottom: 5px; font-weight: 500;">Warna Tema</label>
                            <input type="color" name="theme_color" value="<?php echo $app['theme_color']; ?>" class="form-control" style="width: 100%; padding: 4px; border-radius: 8px; height: 42px; cursor: pointer;">
                        </div>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-size: 0.85rem; color: #cbd5e1; margin-bottom: 5px; font-weight: 500;">Ganti Logo (PNG/JPG)</label>
                        <input type="file" name="logo_file" accept="image/*" class="form-control" style="width: 100%; font-size: 0.85rem; padding: 8px;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 0.85rem; color: #cbd5e1; margin-bottom: 5px; font-weight: 500;">Ganti Favicon (ICO/PNG)</label>
                        <input type="file" name="favicon_file" accept="image/*" class="form-control" style="width: 100%; font-size: 0.85rem; padding: 8px;">
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 12px; font-weight: bold; letter-spacing: 1px;"><i data-lucide="save"></i> TERAPKAN PERUBAHAN</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Developer Footer -->
        <?php include __DIR__ . '/footer.php'; ?>
    </div>
</div>
<script>lucide.createIcons();</script>

