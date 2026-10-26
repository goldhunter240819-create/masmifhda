<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="siakad-main">
        <?php include __DIR__ . '/header.php'; ?>

        <div class="card" style="margin-bottom: 2rem; background: #0f172a; color: white; border: none;">
            <h2 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                <i data-lucide="monitor-smartphone" style="color: #0ea5e9;"></i> APK Phone Emulator
            </h2>
            <p style="opacity: 0.7; margin-top: 5px;">Preview Tampilan Mobile App (Guru & Siswa)</p>
        </div>

        <style>
            .emulator-container {
                display: flex;
                flex-wrap: wrap;
                gap: 4rem;
                justify-content: center;
                padding: 1rem 0 3rem;
            }
            .phone-mockup {
                width: 380px;
                height: 780px;
                background: #000;
                border-radius: 45px;
                padding: 12px;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), inset 0 0 0 2px rgba(255, 255, 255, 0.1);
                position: relative;
                display: flex;
                flex-direction: column;
            }
            .phone-mockup.guru { border: 2px solid #0ea5e9; box-shadow: 0 0 25px rgba(14, 165, 233, 0.2), inset 0 0 0 2px rgba(255, 255, 255, 0.1); }
            .phone-mockup.siswa { border: 2px solid #10b981; box-shadow: 0 0 25px rgba(16, 185, 129, 0.2), inset 0 0 0 2px rgba(255, 255, 255, 0.1); }

            .notch {
                position: absolute;
                top: 12px;
                left: 50%;
                transform: translateX(-50%);
                width: 130px;
                height: 28px;
                background: #000;
                border-bottom-left-radius: 16px;
                border-bottom-right-radius: 16px;
                z-index: 100;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .camera { width: 10px; height: 10px; background: #1a1a1a; border-radius: 50%; box-shadow: inset 0 0 2px rgba(0,0,0,0.8); margin-right: 15px;}
            .speaker { width: 40px; height: 4px; background: #1a1a1a; border-radius: 10px;}
            
            .screen {
                flex-grow: 1;
                background: #fff;
                border-radius: 35px;
                overflow: hidden;
                position: relative;
            }
            .screen iframe {
                width: 100%;
                height: 100%;
                border: none;
                background: #f8fafc;
            }
            .mockup-header {
                text-align: center;
                margin-bottom: 1.5rem;
                color: #334155;
            }
            .mockup-header h3 { margin: 0 0 5px; font-size: 1.2rem; display: flex; justify-content: center; align-items: center; gap: 8px;}
            .mockup-header p { margin: 0; font-size: 0.85rem; color: #64748b; }
            .refresh-btn {
                background: white;
                border: 1px solid #e2e8f0;
                color: #475569;
                padding: 6px 15px;
                border-radius: 8px;
                cursor: pointer;
                font-size: 0.8rem;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                margin-top: 15px;
                transition: all 0.3s;
                box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            }
            .refresh-btn:hover { background: #f8fafc; border-color: #cbd5e1; color: #0f172a; }
        </style>

        <div class="emulator-container">
            <!-- Siswa Emulator -->
            <div>
                <div class="mockup-header">
                    <h3><i data-lucide="graduation-cap" style="color: #10b981;"></i> Tampilan Siswa</h3>
                    <p>Mode Dummy (NIS: 111222)</p>
                    <button class="refresh-btn" onclick="document.getElementById('frame-siswa').src = document.getElementById('frame-siswa').src">
                        <i data-lucide="rotate-cw" style="width: 14px; height: 14px;"></i> Refresh Frame
                    </button>
                </div>
                <div class="phone-mockup siswa">
                    <div class="notch"><div class="camera"></div><div class="speaker"></div></div>
                    <div class="screen">
                        <iframe id="frame-siswa" src="<?php echo base_url('apk_preview.php?role=siswa'); ?>"></iframe>
                    </div>
                </div>
            </div>

            <!-- Guru Emulator -->
            <div>
                <div class="mockup-header">
                    <h3><i data-lucide="briefcase" style="color: #0ea5e9;"></i> Tampilan Guru</h3>
                    <p>Mode Dummy (NIP: 1234567890123456)</p>
                    <button class="refresh-btn" onclick="document.getElementById('frame-guru').src = document.getElementById('frame-guru').src">
                        <i data-lucide="rotate-cw" style="width: 14px; height: 14px;"></i> Refresh Frame
                    </button>
                </div>
                <div class="phone-mockup guru">
                    <div class="notch"><div class="camera"></div><div class="speaker"></div></div>
                    <div class="screen">
                        <iframe id="frame-guru" src="<?php echo base_url('apk_preview.php?role=guru'); ?>"></iframe>
                    </div>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 1rem; color: #64748b; font-size: 0.9rem;">
            <i data-lucide="info" style="width: 16px; height: 16px; vertical-align: -3px; margin-right: 5px;"></i> Emulator ini menggunakan <code>&lt;iframe&gt;</code> untuk memuat simulasi resolusi ponsel cerdas. Navigasi di dalam *frame* tidak akan mengubah URL halaman Developer Panel utama Anda.
        </div>

        <?php include __DIR__ . '/footer.php'; ?>
    </div>
</div>
