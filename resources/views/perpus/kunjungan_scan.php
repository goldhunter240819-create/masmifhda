<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu Digital - <?= htmlspecialchars($nama_sekolah) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f8fafc; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
        }
        .scan-pulse {
            box-shadow: 0 0 0 0 rgba(20, 184, 166, 0.4);
            animation: pulse 2s infinite cubic-bezier(0.66, 0, 0, 1);
        }
        @keyframes pulse {
            to { box-shadow: 0 0 0 30px rgba(20, 184, 166, 0); }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col antialiased bg-[url('<?= base_url('public/assets/images/mesh-bg.png') ?>')] bg-cover bg-center">

    <div class="flex-1 flex items-center justify-center p-4">
        <div class="glass-panel w-full max-w-5xl rounded-3xl overflow-hidden flex flex-col md:flex-row">
            
            <!-- Left Side: Scanner Area -->
            <div class="w-full md:w-1/2 p-10 md:p-14 flex flex-col justify-center items-center text-center bg-gradient-to-br from-brand-50 to-white relative">
                <div class="absolute top-8 left-8">
                    <img src="<?= htmlspecialchars($logo_sekolah) ?>" class="h-12 w-auto" alt="Logo">
                </div>

                <div class="w-48 h-48 rounded-3xl bg-white border-2 border-dashed border-brand-300 flex flex-col items-center justify-center text-brand-500 mb-8 scan-pulse relative overflow-hidden">
                    <div class="absolute w-full h-1 bg-brand-500 shadow-[0_0_10px_2px_#14b8a6] top-1/2 -translate-y-1/2 animate-[ping_3s_ease-in-out_infinite]"></div>
                    <i class="fa-solid fa-qrcode text-6xl mb-3 opacity-50"></i>
                    <span class="font-bold text-sm tracking-widest uppercase opacity-70">Scan QR Card</span>
                </div>
                
                <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight mb-3">Selamat Datang di<br>Perpustakaan Digital</h2>
                <p class="text-slate-500 font-medium">Silakan arahkan <b class="text-brand-600">Kartu Pelajar / Guru</b> Anda ke alat pemindai (scanner) di bawah layar.</p>
                
                <!-- Hidden Input for Barcode Scanner Hardware -->
                <form class="mt-8 w-full max-w-xs">
                    <input type="text" id="scan_input" autofocus placeholder="Atau ketik NIS/NIP disini..." class="w-full text-center bg-white border border-slate-200 rounded-xl px-4 py-3 text-lg font-bold text-slate-700 focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition">
                </form>
            </div>

            <!-- Right Side: Manual / Guest Input -->
            <div class="w-full md:w-1/2 p-10 md:p-14 bg-white border-l border-slate-100 flex flex-col justify-center">
                
                <div class="mb-8">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-bold uppercase tracking-wider mb-4">
                        <i class="fa-solid fa-user-pen"></i> Tamu Umum
                    </span>
                    <h3 class="text-2xl font-bold text-slate-800 mb-2">Bukan Siswa/Guru?</h3>
                    <p class="text-slate-500 text-sm">Jika Anda pengunjung dari luar, silakan isi buku tamu secara manual di bawah ini.</p>
                </div>

                <form class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Nama Lengkap</label>
                        <div class="relative">
                            <i class="fa-regular fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" placeholder="Masukkan nama Anda" class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:bg-white focus:border-brand-500 transition">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Instansi / Kelas</label>
                        <div class="relative">
                            <i class="fa-regular fa-building absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" placeholder="Asal instansi atau sekolah" class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:bg-white focus:border-brand-500 transition">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Tujuan Kunjungan</label>
                        <textarea rows="3" placeholder="Contoh: Membaca buku, meminjam buku referensi..." class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:bg-white focus:border-brand-500 transition resize-none"></textarea>
                    </div>
                    <button type="button" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold text-lg py-3.5 rounded-xl transition shadow-lg shadow-slate-900/20">
                        Simpan Kunjungan
                    </button>
                </form>

            </div>
        </div>
    </div>

    <!-- Clock -->
    <div class="fixed bottom-6 right-8 text-right">
        <div id="clock" class="text-2xl font-extrabold text-slate-700 tracking-tight">00:00:00</div>
        <div id="date" class="text-sm font-medium text-slate-500 uppercase tracking-wider">Senin, 1 Jan 2024</div>
    </div>

    <script>
        // Keep focus on scan input for hardware scanners
        const scanInput = document.getElementById('scan_input');
        document.addEventListener('click', (e) => {
            if(e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                scanInput.focus();
            }
        });

        // Simple Clock
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').textContent = now.toLocaleTimeString('id-ID');
            document.getElementById('date').textContent = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>
