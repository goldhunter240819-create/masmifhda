<?php include __DIR__ . '/_layout_top.php'; ?>

<div style="background: #fff; border-radius: 20px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); max-width: 800px; margin: 0 auto;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px;">
        <h2 style="margin: 0; font-size: 1.5rem; color: #1e293b;"><i data-lucide="settings" style="width: 26px; height: 26px; vertical-align: middle; color: #475569;"></i> Pengaturan Dokumen OSIM</h2>
    </div>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
        <div style="background: #dcfce7; color: #16a34a; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 700; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
            Pengaturan berhasil disimpan!
        </div>
    <?php endif; ?>

    <form action="<?= base_url('osim/pengaturan/simpan') ?>" method="POST">
        
        <h3 style="color: #0d9488; margin-top: 0; margin-bottom: 15px; font-size: 1.1rem;"><i data-lucide="building" style="width: 18px; height: 18px; vertical-align: text-bottom;"></i> Identitas Madrasah (Kop Surat)</h3>
        
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Nama Madrasah Lengkap</label>
            <input type="text" name="nama_madrasah" value="<?= htmlspecialchars($settings['nama_madrasah'] ?? 'MAS Miftahul Huda') ?>" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Alamat Lengkap</label>
            <textarea name="alamat_madrasah" rows="2" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;"><?= htmlspecialchars($settings['alamat_madrasah'] ?? 'Jl. Pendidikan No. 1, Desa Miftahul Huda') ?></textarea>
        </div>

        <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Pilih Wilayah (Provinsi -> Kota -> Kec -> Kelurahan)</label>
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 15px;">
            <div>
                <select id="selectProvinsi" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box; background: #f8fafc;">
                    <option value="">-- Provinsi --</option>
                </select>
            </div>
            <div id="containerKabupaten" style="display: none;">
                <select id="selectKabupaten" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box; background: #f8fafc;">
                    <option value="">-- Kabupaten/Kota --</option>
                </select>
            </div>
            <div id="containerKecamatan" style="display: none;">
                <select id="selectKecamatan" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box; background: #f8fafc;">
                    <option value="">-- Kecamatan --</option>
                </select>
            </div>
            <div id="containerKelurahan" style="display: none;">
                <select id="selectKelurahan" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box; background: #f8fafc;">
                    <option value="">-- Kelurahan/Desa --</option>
                </select>
            </div>
        </div>
        
        <div style="margin-bottom: 15px; display:none;" id="kotaFinalContainer">
            <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Nama Kota / Kabupaten (Tersimpan)</label>
            <input type="text" id="inputKotaFinal" name="kota_madrasah" value="<?= htmlspecialchars($settings['kota_madrasah'] ?? '') ?>" readonly style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box; background: #e2e8f0; color: #475569;">
            <p style="font-size: 0.8rem; color: #64748b; margin-top: 5px;">*Kota ini otomatis digunakan di Lembar Pengesahan Proposal.</p>
        </div>

        <div style="margin-bottom: 30px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Tahun Pelajaran Aktif</label>
            <input type="text" name="tahun_pelajaran" value="<?= htmlspecialchars($settings['tahun_pelajaran'] ?? '2026/2027') ?>" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 30px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">URL / Link Logo Madrasah (Opsional)</label>
            <input type="text" name="logo_url" value="<?= htmlspecialchars($settings['logo_url'] ?? '') ?>" placeholder="https://contoh.com/logo.png" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;">
        </div>

        <h3 style="color: #0d9488; margin-top: 0; margin-bottom: 15px; font-size: 1.1rem; border-top: 1px solid #f1f5f9; padding-top: 25px;"><i data-lucide="pen-tool" style="width: 18px; height: 18px; vertical-align: text-bottom;"></i> Pejabat Berwenang (Lembar Pengesahan)</h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Nama Kepala Madrasah</label>
                <input type="text" name="kamad_nama" value="<?= htmlspecialchars($settings['kamad_nama'] ?? '') ?>" placeholder="Beserta gelar" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">NIP Kepala Madrasah</label>
                <input type="text" name="kamad_nip" value="<?= htmlspecialchars($settings['kamad_nip'] ?? '') ?>" placeholder="Kosongi jika tidak ada" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Nama Waka Kesiswaan</label>
                <input type="text" name="wakasis_nama" value="<?= htmlspecialchars($settings['wakasis_nama'] ?? '') ?>" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">NIP Waka Kesiswaan</label>
                <input type="text" name="wakasis_nip" value="<?= htmlspecialchars($settings['wakasis_nip'] ?? '') ?>" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Nama Pembina OSIM</label>
                <input type="text" name="pembina_nama" value="<?= htmlspecialchars($settings['pembina_nama'] ?? '') ?>" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">NIP Pembina OSIM</label>
                <input type="text" name="pembina_nip" value="<?= htmlspecialchars($settings['pembina_nip'] ?? '') ?>" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;">
            </div>
        </div>

        <h3 style="color: #0d9488; margin-top: 0; margin-bottom: 15px; font-size: 1.1rem; border-top: 1px solid #f1f5f9; padding-top: 25px;"><i data-lucide="users" style="width: 18px; height: 18px; vertical-align: text-bottom;"></i> Pengurus Inti OSIM</h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Nama Ketua OSIM</label>
                <input type="text" name="ketua_osim_nama" value="<?= htmlspecialchars($settings['ketua_osim_nama'] ?? '') ?>" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">NIS Ketua OSIM</label>
                <input type="text" name="ketua_osim_nis" value="<?= htmlspecialchars($settings['ketua_osim_nis'] ?? '') ?>" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;">
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Nama Sekretaris OSIM</label>
                <input type="text" name="sekretaris_osim_nama" value="<?= htmlspecialchars($settings['sekretaris_osim_nama'] ?? '') ?>" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">NIS Sekretaris OSIM</label>
                <input type="text" name="sekretaris_osim_nis" value="<?= htmlspecialchars($settings['sekretaris_osim_nis'] ?? '') ?>" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;">
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">Nama Bendahara OSIM</label>
                <input type="text" name="bendahara_osim_nama" value="<?= htmlspecialchars($settings['bendahara_osim_nama'] ?? '') ?>" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 700; color: #475569; font-size: 0.9rem;">NIS Bendahara OSIM</label>
                <input type="text" name="bendahara_osim_nis" value="<?= htmlspecialchars($settings['bendahara_osim_nis'] ?? '') ?>" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box;">
            </div>
        </div>

        <div style="text-align: right; border-top: 2px solid #f1f5f9; padding-top: 20px;">
            <button type="submit" class="e-btn" style="background: #1e293b; color: #fff; border: none; cursor: pointer; padding: 14px 30px; font-size: 1rem;"><i data-lucide="save"></i> Simpan Pengaturan</button>
        </div>

    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const apiBaseUrl = 'https://www.emsifa.com/api-wilayah-indonesia/api';
    const selectProvinsi = document.getElementById('selectProvinsi');
    const selectKabupaten = document.getElementById('selectKabupaten');
    const selectKecamatan = document.getElementById('selectKecamatan');
    const selectKelurahan = document.getElementById('selectKelurahan');
    
    const containerKab = document.getElementById('containerKabupaten');
    const containerKec = document.getElementById('containerKecamatan');
    const containerKel = document.getElementById('containerKelurahan');
    
    const inputKotaFinal = document.getElementById('inputKotaFinal');
    const kotaFinalContainer = document.getElementById('kotaFinalContainer');

    // Tampilkan field final kota jika sudah ada isinya
    if(inputKotaFinal.value !== '') {
        kotaFinalContainer.style.display = 'block';
    }

    // Helper Title Case
    function toTitleCase(str) {
        return str.toLowerCase().replace(/\b\w/g, s => s.toUpperCase());
    }

    // 1. Fetch Provinces
    fetch(`${apiBaseUrl}/provinces.json`)
        .then(res => res.json())
        .then(data => {
            let options = '<option value="">-- Provinsi --</option>';
            data.forEach(d => options += `<option value="${d.id}">${toTitleCase(d.name)}</option>`);
            selectProvinsi.innerHTML = options;
        })
        .catch(err => {
            console.error('API Wilayah Gagal:', err);
            // Fallback manual jika API mati
            kotaFinalContainer.style.display = 'block';
            inputKotaFinal.removeAttribute('readonly');
            inputKotaFinal.style.background = '#fff';
            selectProvinsi.parentNode.style.display = 'none';
        });

    // 2. Provinsi Change -> Fetch Kabupaten
    selectProvinsi.addEventListener('change', function() {
        containerKab.style.display = 'none';
        containerKec.style.display = 'none';
        containerKel.style.display = 'none';
        if(!this.value) return;
        
        selectKabupaten.innerHTML = '<option value="">Memuat...</option>';
        containerKab.style.display = 'block';
        
        fetch(`${apiBaseUrl}/regencies/${this.value}.json`)
            .then(res => res.json())
            .then(data => {
                let options = '<option value="">-- Kabupaten/Kota --</option>';
                data.forEach(d => options += `<option value="${d.id}" data-name="${toTitleCase(d.name)}">${toTitleCase(d.name)}</option>`);
                selectKabupaten.innerHTML = options;
            });
    });

    // 3. Kabupaten Change -> Fetch Kecamatan
    selectKabupaten.addEventListener('change', function() {
        containerKec.style.display = 'none';
        containerKel.style.display = 'none';
        if(!this.value) return;
        
        // Update Kota Final input
        const selectedOption = this.options[this.selectedIndex];
        inputKotaFinal.value = selectedOption.getAttribute('data-name');
        kotaFinalContainer.style.display = 'block';

        selectKecamatan.innerHTML = '<option value="">Memuat...</option>';
        containerKec.style.display = 'block';
        
        fetch(`${apiBaseUrl}/districts/${this.value}.json`)
            .then(res => res.json())
            .then(data => {
                let options = '<option value="">-- Kecamatan --</option>';
                data.forEach(d => options += `<option value="${d.id}">${toTitleCase(d.name)}</option>`);
                selectKecamatan.innerHTML = options;
            });
    });

    // 4. Kecamatan Change -> Fetch Kelurahan
    selectKecamatan.addEventListener('change', function() {
        containerKel.style.display = 'none';
        if(!this.value) return;
        
        selectKelurahan.innerHTML = '<option value="">Memuat...</option>';
        containerKel.style.display = 'block';
        
        fetch(`${apiBaseUrl}/villages/${this.value}.json`)
            .then(res => res.json())
            .then(data => {
                let options = '<option value="">-- Kelurahan/Desa --</option>';
                data.forEach(d => options += `<option value="${d.id}">${toTitleCase(d.name)}</option>`);
                selectKelurahan.innerHTML = options;
            });
    });
    
    // 5. Kelurahan Change
    selectKelurahan.addEventListener('change', function() {
        if(this.value) {
            // Animasi kecil tanda selesai
            this.style.borderColor = '#10b981';
            setTimeout(() => this.style.borderColor = '#cbd5e1', 1000);
        }
    });
});
</script>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
