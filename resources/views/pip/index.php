<style>
    body, html {
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* Override layout container */
    .container {
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
    }

    .pip-wrapper {
        position: relative;
        min-height: 100vh;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #0f172a; /* Dark sleek background */
        overflow: hidden;
        padding: 20px;
        box-sizing: border-box;
    }

    /* Abstract floating shapes for modern aesthetic */
    .pip-wrapper::before,
    .pip-wrapper::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        filter: blur(100px);
        z-index: 0;
        animation: floatShape 10s ease-in-out infinite alternate;
    }

    .pip-wrapper::before {
        width: 500px;
        height: 500px;
        background: rgba(14, 165, 233, 0.4); /* Sky blue */
        top: -150px;
        left: -150px;
    }

    .pip-wrapper::after {
        width: 600px;
        height: 600px;
        background: rgba(16, 185, 129, 0.3); /* Emerald green */
        bottom: -200px;
        right: -100px;
        animation-delay: -5s;
    }

    @keyframes floatShape {
        0% { transform: translate(0, 0) scale(1); }
        100% { transform: translate(50px, 50px) scale(1.1); }
    }

    /* Glassmorphism Card */
    .pip-card {
        position: relative;
        z-index: 10;
        width: 100%;
        max-width: 480px;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 30px;
        padding: 40px;
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .pip-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 40px 80px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }

    /* Logo / Icon Header */
    .pip-logo-box {
        width: 72px;
        height: 72px;
        background: linear-gradient(135deg, #0ea5e9 0%, #10b981 100%);
        border-radius: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
        box-shadow: 0 15px 30px rgba(16, 185, 129, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff;
    }

    .pip-logo-box svg {
        width: 34px;
        height: 34px;
    }

    /* Typography */
    .pip-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 28px;
        font-weight: 700;
        color: #fff;
        margin: 0 0 10px 0;
        letter-spacing: -0.5px;
    }

    .pip-subtitle {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 15px;
        color: #94a3b8;
        line-height: 1.6;
        margin: 0 0 32px 0;
    }

    /* Form Styles */
    #pipForm {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .input-wrapper {
        position: relative;
        width: 100%;
    }

    .input-wrapper svg {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        transition: color 0.3s ease;
    }

    .pip-input {
        width: 100%;
        box-sizing: border-box;
        background: rgba(15, 23, 42, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 16px 20px 16px 50px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 16px;
        color: #fff;
        outline: none;
        transition: all 0.3s ease;
    }

    .pip-input::placeholder {
        color: #64748b;
    }

    .form-group input:focus {
        outline: none;
        border-color: #38bdf8;
        box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.1);
    }

    .warning-box {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 12px;
        padding: 15px;
        margin-top: 20px;
        margin-bottom: 20px;
    }
    .warning-box p { margin: 0 0 10px 0; font-size: 13px; color: #fca5a5; line-height: 1.5; text-align: left; }
    .warning-box label { display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: #e2e8f0; cursor: pointer; text-align: left; }
    .warning-box input[type="checkbox"] { margin-top: 3px; width: 16px; height: 16px; cursor: pointer; }
    
    .btn-secondary[disabled] {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    .pip-input:focus {
        border-color: #0ea5e9;
        background: rgba(15, 23, 42, 0.6);
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.15);
    }

    .pip-input:focus + svg {
        color: #0ea5e9;
    }

    .pip-btn {
        width: 100%;
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        color: #fff;
        border: none;
        border-radius: 16px;
        padding: 16px;
        font-family: 'Outfit', sans-serif;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 10px 25px rgba(2, 132, 199, 0.4);
    }

    .pip-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(2, 132, 199, 0.5);
        background: linear-gradient(135deg, #38bdf8 0%, #0369a1 100%);
    }

    .pip-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    /* Result Area */
    .pip-result {
        display: none;
        width: 100%;
        margin-top: 30px;
        padding-top: 30px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        text-align: left;
        animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes slideUp {
        0% { opacity: 0; transform: translateY(20px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    .result-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(16, 185, 129, 0.15);
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: #34d399;
        padding: 8px 16px;
        border-radius: 100px;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 16px;
    }

    .student-name {
        font-size: 24px;
        font-weight: 700;
        color: #fff;
        margin: 0 0 20px 0;
    }

    .req-box {
        background: rgba(15, 23, 42, 0.4);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 24px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .req-title {
        font-size: 14px;
        font-weight: 600;
        color: #94a3b8;
        margin: 0 0 12px 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .req-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .req-list li {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        font-size: 15px;
        color: #cbd5e1;
        margin-bottom: 12px;
        line-height: 1.4;
    }

    .req-list li:last-child {
        margin-bottom: 0;
    }

    .req-list li svg {
        color: #34d399;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .btn-secondary {
        width: 100%;
        background: rgba(255, 255, 255, 0.05);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 16px;
        font-family: 'Outfit', sans-serif;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-decoration: none;
        box-sizing: border-box;
    }

    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }

    /* Back link */
    .back-home {
        position: absolute;
        top: 30px;
        left: 30px;
        z-index: 20;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #cbd5e1;
        text-decoration: none;
        font-weight: 500;
        font-family: 'Plus Jakarta Sans', sans-serif;
        transition: color 0.3s;
        background: rgba(255,255,255,0.05);
        padding: 10px 20px;
        border-radius: 100px;
        border: 1px solid rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
    }

    .back-home:hover {
        color: #fff;
        background: rgba(255,255,255,0.1);
    }

    @media (max-width: 600px) {
        .pip-card {
            padding: 30px 20px;
            border-radius: 24px;
        }
        .pip-title { font-size: 24px; }
        .back-home { top: 15px; left: 15px; padding: 8px 16px; font-size: 14px; }
    }
</style>

<div class="pip-wrapper">
    <a href="#" onclick="window.location.href = window.location.pathname.replace(base_url('pip'), base_url('portal')); return false;" class="back-home">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg> 
        Ke Halaman Utama
    </a>

    <div class="pip-card">
        <div class="pip-logo-box">
            <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><path d="M11 8v6"></path><path d="M8 11h6"></path></svg>
        </div>
        
        <h1 class="pip-title">Cek Data PIP</h1>
        <p class="pip-subtitle">Masukkan NIK atau NISN Anda untuk melihat status penerimaan Program Indonesia Pintar.</p>

        <form id="pipForm" onsubmit="checkPip(event)">
            <div class="input-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                <input type="text" id="queryPip" class="pip-input" placeholder="Ketik NIK atau NISN disini..." required autocomplete="off">
            </div>
            <button type="submit" class="pip-btn" id="btnCheck">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                Periksa Sekarang
            </button>
        </form>

        <div id="pipResult" class="pip-result">
            <div class="result-badge">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                Status: Diterima
            </div>
            <h2 class="student-name" id="resName">NAMA SISWA</h2>
            
            <div class="req-box">
                <h3 class="req-title">Persyaratan Pencairan Bank</h3>
                <ul class="req-list">
                    <li><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> <span>Fotokopi Kartu Keluarga (KK)</span></li>
                    <li><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> <span>Fotokopi KTP Orang Tua / Wali</span></li>
                    <li><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> <span>Fotokopi Halaman Data Diri Siswa pada Rapor</span></li>
                    <li><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> <span>Surat Keterangan Kepala Madrasah</span></li>
                    <li><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> <span>Membawa Buku Tabungan Asli & Fotokopi</span></li>
                     <li><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> <span>Siswa Harus Ikut Mengambil</span></li>
                </ul>
            </div>

            <div class="warning-box">
                <p><strong>Pemberitahuan Penting:</strong> Sebagian dana PIP yang dicairkan akan digunakan / dipotong untuk keperluan pembayaran Komite Madrasah dan Field Trip. Harap maklum dan menyetujui ketentuan ini sebelum mencetak surat keterangan.</p>
                <label>
                    <input type="checkbox" id="checkAgree" onchange="togglePrintBtn()">
                    Saya memahami dan menyetujui pernyataan di atas.
                </label>
            </div>

            <a href="#" id="btnLihatSurat" class="btn-secondary" target="_blank" disabled onclick="confirmPrint(event)">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                Lihat & Cetak Surat
            </a>
            <a href="#" id="btnCetakRapor" class="btn-secondary" target="_blank" disabled style="margin-top: 10px; background-color: #28a745; border-color: #28a745; color: white;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                Cetak Profil Rapor
            </a>
        </div>
    </div>
</div>

<script>
function checkPip(e) {
    e.preventDefault();
    const query = document.getElementById('queryPip').value;
    const btn = document.getElementById('btnCheck');
    const resDiv = document.getElementById('pipResult');
    
    // Reset state
    resDiv.style.display = 'none';
    btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="spin"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg> Memproses...`;
    btn.disabled = true;

    // Add spin animation CSS dynamically if not present
    if (!document.getElementById('spinStyle')) {
        const style = document.createElement('style');
        style.id = 'spinStyle';
        style.innerHTML = `@keyframes spin { 100% { transform: rotate(360deg); } } .spin { animation: spin 1s linear infinite; }`;
        document.head.appendChild(style);
    }

    const fetchUrl = window.location.pathname.replace(/\/$/, '') + '/check';
    fetch(fetchUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'query=' + encodeURIComponent(query)
    })
    .then(res => res.json())
    .then(data => {
        btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg> Periksa Sekarang`;
        btn.disabled = false;
        
        if(data.status === 'success') {
            document.getElementById('resName').textContent = data.data.NAMA_SISWA;
            const suratUrl = window.location.pathname.replace(/\/$/, '') + '/surat?token=' + encodeURIComponent(data.data.TOKEN);
            const raporUrl = window.location.pathname.replace(/\/$/, '') + '/rapor?token=' + encodeURIComponent(data.data.TOKEN);
            
            document.getElementById('btnLihatSurat').href = suratUrl;
            document.getElementById('btnLihatSurat').dataset.token = data.data.TOKEN;
            
            document.getElementById('btnCetakRapor').href = raporUrl;
            
            // Reset checkbox
            document.getElementById('checkAgree').checked = false;
            document.getElementById('btnLihatSurat').setAttribute('disabled', 'disabled');
            document.getElementById('btnCetakRapor').setAttribute('disabled', 'disabled');
            
            resDiv.style.display = 'block';
            if (typeof ZNS !== 'undefined') ZNS.showSuccess('Data ditemukan!');
        } else {
            if (typeof ZNS !== 'undefined') ZNS.showError(data.message);
            else alert(data.message);
        }
    })
    .catch(err => {
        btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg> Periksa Sekarang`;
        btn.disabled = false;
        if (typeof ZNS !== 'undefined') ZNS.showError('Terjadi kesalahan jaringan.');
        else alert('Terjadi kesalahan jaringan.');
    });
}

function togglePrintBtn() {
    const isChecked = document.getElementById('checkAgree').checked;
    const btn1 = document.getElementById('btnLihatSurat');
    const btn2 = document.getElementById('btnCetakRapor');
    if (isChecked) {
        btn1.removeAttribute('disabled');
        btn2.removeAttribute('disabled');
    } else {
        btn1.setAttribute('disabled', 'disabled');
        btn2.setAttribute('disabled', 'disabled');
    }
}

function confirmPrint(e) {
    if (document.getElementById('btnLihatSurat').hasAttribute('disabled')) {
        e.preventDefault();
        return;
    }
    
    const token = document.getElementById('btnLihatSurat').dataset.token;
    if (!token) return;

    // We don't prevent default, so the link opens in a new tab.
    // Meanwhile, we send the AJAX request in the background.
    const confirmUrl = window.location.pathname.replace(/\/$/, '') + '/confirm';
    fetch(confirmUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'token=' + encodeURIComponent(token)
    }).catch(err => console.error(err));
}
</script>
