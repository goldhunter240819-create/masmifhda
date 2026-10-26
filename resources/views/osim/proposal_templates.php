<?php include __DIR__ . '/_layout_top.php'; ?>

<div style="background: #fff; border-radius: 20px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.03);">
    <div style="margin-bottom: 30px; text-align: center;">
        <h2 style="margin: 0; font-size: 1.8rem; color: #1e293b;">Pilih Template Proposal</h2>
        <p style="color: #64748b; margin-top: 10px;">Pilih template awal untuk mempercepat pembuatan proposal kegiatan Anda.</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px;">
        
        <!-- Template MATAMUDA -->
        <div style="border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; transition: 0.3s; cursor: pointer; background: #fff;" onmouseover="this.style.boxShadow='0 15px 30px rgba(0,0,0,0.1)'; this.style.borderColor='#8b5cf6'" onmouseout="this.style.boxShadow='none'; this.style.borderColor='#e2e8f0'">
            <div style="height: 140px; background: linear-gradient(135deg, #f5f3ff 0%, #e0e7ff 100%); display: flex; align-items: center; justify-content: center; position: relative;">
                <div style="position: absolute; top: 10px; right: 10px; background: #8b5cf6; color: white; padding: 4px 10px; border-radius: 50px; font-size: 0.7rem; font-weight: 800;">REKOMENDASI</div>
                <i data-lucide="file-badge-2" style="width: 50px; height: 50px; color: #8b5cf6;"></i>
            </div>
            <div style="padding: 20px;">
                <h3 style="margin: 0 0 10px 0; font-size: 1.2rem; color: #1e293b;">Proposal MATAMUDA</h3>
                <p style="margin: 0 0 20px 0; color: #64748b; font-size: 0.9rem; line-height: 1.5;">Template proposal orientasi siswa baru dengan struktur lengkap 20 halaman, anti-bullying, dan moderasi beragama.</p>
                <a href="<?= base_url('osim/proposal/create?type=matamuda') ?>" class="e-btn" style="background: #8b5cf6; color: #fff; width: 100%; justify-content: center; box-sizing: border-box;">Gunakan Template</a>
            </div>
        </div>

        <!-- Template LDKS -->
        <div style="border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; transition: 0.3s; cursor: pointer; background: #fff;" onmouseover="this.style.boxShadow='0 15px 30px rgba(0,0,0,0.1)'; this.style.borderColor='#0ea5e9'" onmouseout="this.style.boxShadow='none'; this.style.borderColor='#e2e8f0'">
            <div style="height: 140px; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); display: flex; align-items: center; justify-content: center;">
                <i data-lucide="tent" style="width: 50px; height: 50px; color: #0ea5e9;"></i>
            </div>
            <div style="padding: 20px;">
                <h3 style="margin: 0 0 10px 0; font-size: 1.2rem; color: #1e293b;">Proposal LDKS</h3>
                <p style="margin: 0 0 20px 0; color: #64748b; font-size: 0.9rem; line-height: 1.5;">Template untuk kegiatan Latihan Dasar Kepemimpinan Siswa (Outdoor/Indoor) khusus pengurus OSIM baru.</p>
                <a href="<?= base_url('osim/proposal/create?type=ldks') ?>" class="e-btn" style="background: #0ea5e9; color: #fff; width: 100%; justify-content: center; box-sizing: border-box;">Gunakan Template</a>
            </div>
        </div>

        <!-- Template Bebas -->
        <div style="border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; transition: 0.3s; cursor: pointer; background: #fff;" onmouseover="this.style.boxShadow='0 15px 30px rgba(0,0,0,0.1)'; this.style.borderColor='#64748b'" onmouseout="this.style.boxShadow='none'; this.style.borderColor='#e2e8f0'">
            <div style="height: 140px; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); display: flex; align-items: center; justify-content: center; border-bottom: 1px dashed #cbd5e1;">
                <i data-lucide="file-plus-2" style="width: 50px; height: 50px; color: #94a3b8;"></i>
            </div>
            <div style="padding: 20px;">
                <h3 style="margin: 0 0 10px 0; font-size: 1.2rem; color: #1e293b;">Proposal Blank (Kosong)</h3>
                <p style="margin: 0 0 20px 0; color: #64748b; font-size: 0.9rem; line-height: 1.5;">Mulai dari kertas kosong. Cocok untuk kegiatan spesifik seperti Peringatan Hari Besar Islam (PHBI) atau Class Meeting.</p>
                <a href="<?= base_url('osim/proposal/create?type=bebas') ?>" class="e-btn" style="background: #fff; color: #475569; border: 1px solid #cbd5e1; width: 100%; justify-content: center; box-sizing: border-box;">Mulai dari Kosong</a>
            </div>
        </div>

    </div>
</div>

<?php include __DIR__ . '/_layout_bottom.php'; ?>
