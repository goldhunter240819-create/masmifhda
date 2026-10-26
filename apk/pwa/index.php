<?php
// Halaman PWA Install - Global Access (Tidak perlu login)
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#0ea5e9">
    <title>Install Aplikasi MAS Mifhda</title>
    
    <!-- Link to manifest -->
    <link rel="manifest" href="/apk/manifest.json">
    <link rel="icon" type="image/png" href="/public/logo_kemenag.png">
    <link rel="apple-touch-icon" href="/public/logo_kemenag.png">
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #0ea5e9;
            --primary-dark: #0284c7;
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        body { 
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); 
            color: var(--text-main); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            min-height: 100vh; 
            padding: 20px; 
            overflow: hidden;
            position: relative;
        }
        
        /* Dekorasi background */
        .circle-1 { position: absolute; width: 300px; height: 300px; background: rgba(14, 165, 233, 0.1); border-radius: 50%; top: -100px; left: -100px; z-index: 0; }
        .circle-2 { position: absolute; width: 250px; height: 250px; background: rgba(16, 185, 129, 0.1); border-radius: 50%; bottom: -50px; right: -50px; z-index: 0; }
        
        .install-card { 
            background: var(--card-bg); 
            border-radius: 28px; 
            padding: 35px 25px; 
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.1); 
            text-align: center; 
            width: 100%; 
            max-width: 400px; 
            position: relative;
            z-index: 10;
        }
        
        .logo-container {
            width: 110px; 
            height: 110px; 
            border-radius: 28px; 
            margin: 0 auto 20px; 
            background: #ffffff; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            box-shadow: 0 10px 25px rgba(14, 165, 233, 0.15);
            border: 2px solid #f1f5f9;
        }
        
        .logo-container img { width: 75%; height: 75%; object-fit: contain; }
        
        h1 { font-size: 1.6rem; font-weight: 800; margin-bottom: 8px; color: var(--text-main); }
        p.subtitle { font-size: 0.95rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 30px; }
        
        .features { text-align: left; margin-bottom: 30px; background: #f8fafc; padding: 15px; border-radius: 20px; border: 1px dashed #cbd5e1; }
        .feature-item { display: flex; align-items: center; gap: 12px; margin-bottom: 15px; font-size: 0.95rem; color: var(--text-main); font-weight: 600; }
        .feature-item:last-child { margin-bottom: 0; }
        .feature-icon { width: 36px; height: 36px; background: #e0f2fe; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--primary); flex-shrink: 0; }
        
        .btn-install { 
            background: linear-gradient(135deg, var(--primary), var(--primary-dark)); 
            color: white; 
            border: none; 
            width: 100%; 
            padding: 18px; 
            border-radius: 18px; 
            font-size: 1.05rem; 
            font-weight: 700; 
            cursor: pointer; 
            box-shadow: 0 10px 20px rgba(14, 165, 233, 0.3); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            gap: 10px; 
            transition: all 0.2s ease; 
            text-decoration: none;
        }
        .btn-install:active { transform: scale(0.97); }
        .btn-install i { font-size: 1.3rem; }
        
        .hidden { display: none !important; }
        
        .install-guide { 
            font-size: 0.85rem; 
            color: #0f172a; 
            margin-top: 20px; 
            background: #fffbeb; 
            padding: 15px; 
            border-radius: 16px; 
            text-align: left;
            border: 1px solid #fde68a;
            line-height: 1.5;
        }
        .install-guide i.fa-info-circle { color: #d97706; margin-right: 5px; }
        
        .status-msg { margin-top: 15px; font-size: 0.85rem; color: var(--text-muted); font-weight: 600; }
    </style>
</head>
<body>

<div class="circle-1"></div>
<div class="circle-2"></div>

<div class="install-card">
    <div class="logo-container">
        <img src="/public/logo_kemenag.png" alt="Logo">
    </div>
    <h1>MAS Mifhda App</h1>
    <p class="subtitle">Install aplikasi resmi untuk pengalaman akses yang lebih cepat, ringan, dan stabil.</p>
    
    <div class="features">
        <div class="feature-item">
            <div class="feature-icon"><i class="fa-solid fa-bolt"></i></div>
            Akses lebih ringan & cepat
        </div>
        <div class="feature-item">
            <div class="feature-icon"><i class="fa-solid fa-mobile-screen"></i></div>
            Tampilan penuh aplikasi Native
        </div>
        <div class="feature-item">
            <div class="feature-icon"><i class="fa-solid fa-house-signal"></i></div>
            Mendukung akses offline dasar
        </div>
    </div>

    <!-- Tombol Install PWA -->
    <button id="btnInstall" class="btn-install hidden">
        <i class="fa-brands fa-google-play"></i> Install Sekarang
    </button>
    
    <!-- Panduan iOS -->
    <div id="installGuide" class="install-guide hidden">
        <p><i class="fa-solid fa-info-circle"></i> <b>Cara Install (iPhone/iPad):</b></p>
        <p style="margin-top: 8px;">1. Ketuk ikon <b>Share</b> <i class="fa-solid fa-arrow-up-from-bracket" style="margin: 0 4px;"></i> di menu browser.</p>
        <p>2. Gulir ke bawah, pilih <b>Add to Home Screen</b> <i class="fa-solid fa-square-plus" style="margin: 0 4px;"></i>.</p>
    </div>
    
    <div id="statusMsg" class="status-msg">Menyiapkan instalasi...</div>
    
    <a href="/apk/" style="display: block; margin-top: 20px; font-size: 0.85rem; color: var(--text-muted); font-weight: 600; text-decoration: none;">
        Lanjut ke Web Browser <i class="fa-solid fa-arrow-right"></i>
    </a>
</div>

<script>
    // 1. Mendaftarkan Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/apk/sw.js')
                .then(registration => console.log('Service Worker registered.'))
                .catch(error => console.log('Service Worker failed:', error));
        });
    }

    // 2. Logic PWA Install Prompt
    let deferredPrompt;
    const btnInstall = document.getElementById('btnInstall');
    const installGuide = document.getElementById('installGuide');
    const statusMsg = document.getElementById('statusMsg');

    // Cek iOS (iPhone, iPad, iPod)
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    // Cek jika sudah terinstall (standalone)
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

    if (isStandalone) {
        statusMsg.innerHTML = '<i class="fa-solid fa-check-circle" style="color:#10b981;"></i> Aplikasi sudah terinstall di perangkat Anda.';
    } else if (isIOS) {
        // Tampilkan panduan manual khusus iOS
        installGuide.classList.remove('hidden');
        statusMsg.classList.add('hidden');
    } else {
        // Menunggu event beforeinstallprompt (Android / Desktop Chrome)
        window.addEventListener('beforeinstallprompt', (e) => {
            // Mencegah browser menampilkan prompt otomatis
            e.preventDefault();
            // Simpan event
            deferredPrompt = e;
            // Tampilkan tombol
            btnInstall.classList.remove('hidden');
            statusMsg.classList.add('hidden');
        });
        
        // Timeout jika event tidak muncul (berarti mungkin browser tidak mendukung atau sudah di-install)
        setTimeout(() => {
            if (!deferredPrompt && !isIOS && !isStandalone) {
                statusMsg.innerHTML = 'Browser Anda mungkin tidak mendukung instalasi otomatis, atau aplikasi sudah terinstall.';
            }
        }, 3000);
    }

    // 3. Aksi klik tombol Install
    btnInstall.addEventListener('click', async () => {
        if (deferredPrompt) {
            // Munculkan prompt bawaan sistem
            deferredPrompt.prompt();
            // Tunggu pilihan user
            const { outcome } = await deferredPrompt.userChoice;
            console.log(`User response: ${outcome}`);
            
            // Sembunyikan tombol setelah user merespon
            deferredPrompt = null;
            btnInstall.classList.add('hidden');
            statusMsg.innerHTML = outcome === 'accepted' ? 'Sedang menginstall...' : 'Instalasi dibatalkan.';
            statusMsg.classList.remove('hidden');
        }
    });

    // 4. Deteksi sukses install
    window.addEventListener('appinstalled', () => {
        console.log('Aplikasi sukses di-install');
        btnInstall.classList.add('hidden');
        installGuide.classList.add('hidden');
        statusMsg.innerHTML = '<i class="fa-solid fa-check-circle" style="color:#10b981;"></i> Instalasi berhasil! Silakan buka dari Home Screen Anda.';
        statusMsg.classList.remove('hidden');
    });
</script>

</body>
</html>
