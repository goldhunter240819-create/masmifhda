<?php
require_once 'koneksi.php';

$is_logged_in = isset($_SESSION['siswa_id']) || isset($_SESSION['guru_id']);
if (!$is_logged_in) {
    header("Location: login.php");
    exit;
}

$page_title = 'Buku Saku Digital';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding-top: 3.5rem;">
    <div class="header-top">
        <a href="<?= isset($_SESSION['guru_id']) ? 'profil_guru.php' : 'profil.php' ?>" style="color: white; font-size: 1.2rem; padding: 5px;"><i class="fa-solid fa-arrow-left"></i></a>
        <div class="greeting" style="flex: 1; text-align: center; padding-right: 25px;">
            <p style="margin: 0; font-size: 0.85rem; opacity: 0.9;">Panduan & Peraturan</p>
            <h1 style="margin: 0; font-size: 1.2rem; font-weight: 700;">Buku Saku Digital</h1>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10;">
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="width: 70px; height: 70px; background: #ecfdf5; color: #10b981; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 15px;">
                <i class="fa-solid fa-book"></i>
            </div>
            <h2 style="font-size: 1.1rem; color: #1e293b; margin: 0;">Buku Saku Madrasah</h2>
            <p style="font-size: 0.85rem; color: #64748b; margin: 5px 0 0 0;">Baca pedoman, tata tertib, dan informasi penting madrasah.</p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 15px;">
            <!-- BAB 1 -->
            <details style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; padding: 10px;">
                <summary style="font-weight: 700; color: #1e293b; cursor: pointer; display: flex; align-items: center; gap: 10px; font-size: 0.95rem; outline: none; list-style: none;">
                    <div style="background: #10b981; color: white; width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">1</div>
                    Visi & Misi Madrasah
                </summary>
                <div style="padding: 15px 5px 5px 40px; font-size: 0.85rem; color: #475569; line-height: 1.6;">
                    <strong>Visi Madrasah:</strong><br>
                    "UNGGUL DALAM IMTAQ, IPTEK, TERAMPIL, BERJIWA MANDIRI, DAN BERMANFAAT."<br><br>
                    <strong>Misi Madrasah:</strong>
                    <ol style="padding-left: 20px; margin-top: 5px;">
                        <li style="margin-bottom: 5px;">Memperkuat Keimanan, Ketakwaan, dan Akhlak Mulia.</li>
                        <li style="margin-bottom: 5px;">Meningkatkan Mutu Pembelajaran yang Bermakna dan Mendalam.</li>
                        <li style="margin-bottom: 5px;">Mengembangkan Kompetensi Akademik dan Literasi Peserta Didik.</li>
                        <li style="margin-bottom: 5px;">Mengembangkan Keterampilan dan Potensi Peserta Didik.</li>
                        <li style="margin-bottom: 5px;">Menumbuhkan Kemandirian dan Jiwa Produktif.</li>
                        <li style="margin-bottom: 5px;">Memanfaatkan Teknologi dan AI secara Produktif, Etis, dan Bertanggung Jawab.</li>
                        <li style="margin-bottom: 5px;">Meningkatkan Profesionalisme Guru dan Tenaga Kependidikan.</li>
                        <li style="margin-bottom: 5px;">Membangun Tata Kelola Madrasah yang Efektif, Transparan, dan Akuntabel.</li>
                        <li style="margin-bottom: 5px;">Mewujudkan Lingkungan Madrasah yang Aman, Nyaman, Bersih, Sehat, dan Ramah.</li>
                        <li style="margin-bottom: 5px;">Memperkuat Kemitraan dan Kemanfaatan Madrasah bagi Masyarakat.</li>
                    </ol>
                </div>
            </details>

            <!-- BAB 2 -->
            <details style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; padding: 10px;">
                <summary style="font-weight: 700; color: #1e293b; cursor: pointer; display: flex; align-items: center; gap: 10px; font-size: 0.95rem; outline: none; list-style: none;">
                    <div style="background: #3b82f6; color: white; width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">2</div>
                    Tata Tertib Seragam
                </summary>
                <div style="padding: 15px 5px 5px 40px; font-size: 0.85rem; color: #475569; line-height: 1.6;">
                    Siswa diwajibkan memakai seragam sesuai jadwal yang ditentukan:<br>
                    <ul style="padding-left: 20px; margin-top: 5px;">
                        <li><strong>Senin - Selasa:</strong> Putih Abu-abu, lengkap dengan dasi, sabuk, dan topi saat upacara.</li>
                        <li><strong>Rabu - Kamis:</strong> Seragam Batik Identitas Madrasah.</li>
                        <li><strong>Jumat:</strong> Busana Muslim / Baju Kurung khas Madrasah.</li>
                        <li><strong>Sabtu:</strong> Seragam Pramuka Lengkap.</li>
                    </ul>
                    *Sepatu wajib berwarna hitam dengan kaos kaki putih (kecuali pramuka mengenakan kaos kaki hitam).
                </div>
            </details>

            <!-- BAB 3 -->
            <details style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; padding: 10px;">
                <summary style="font-weight: 700; color: #1e293b; cursor: pointer; display: flex; align-items: center; gap: 10px; font-size: 0.95rem; outline: none; list-style: none;">
                    <div style="background: #f59e0b; color: white; width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">3</div>
                    Aturan Kehadiran & Izin
                </summary>
                <div style="padding: 15px 5px 5px 40px; font-size: 0.85rem; color: #475569; line-height: 1.6;">
                    <ul style="padding-left: 20px;">
                        <li>Siswa hadir di sekolah paling lambat 10 menit sebelum bel masuk berbunyi (07:00 WIB).</li>
                        <li>Siswa yang tidak masuk karena sakit atau izin **wajib** memberikan surat keterangan dari orang tua/wali atau menghubungi Wali Kelas via aplikasi ini.</li>
                        <li>Alpa (tanpa keterangan) lebih dari 3 hari berturut-turut akan ditindaklanjuti dengan pemanggilan orang tua.</li>
                    </ul>
                </div>
            </details>
            
            <!-- BAB 4 -->
            <details style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; padding: 10px;">
                <summary style="font-weight: 700; color: #1e293b; cursor: pointer; display: flex; align-items: center; gap: 10px; font-size: 0.95rem; outline: none; list-style: none;">
                    <div style="background: #8b5cf6; color: white; width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">4</div>
                    Program Unggulan Madrasah
                </summary>
                <div style="padding: 15px 5px 5px 40px; font-size: 0.85rem; color: #475569; line-height: 1.6;">
                    <ul style="padding-left: 20px; margin-top: 5px;">
                        <li style="margin-bottom: 8px;"><strong>Madrasah Berbasis Pesantren:</strong> Menjadikan nilai keislaman sebagai budaya dan fondasi keilmuan, bukan sekadar materi.</li>
                        <li style="margin-bottom: 8px;"><strong>Kurikulum Berbasis Cinta:</strong> Mendidik dengan nilai, mengajar dengan hati. Mengutamakan empati, akhlak, dan kepedulian.</li>
                        <li style="margin-bottom: 8px;"><strong>Deep Learning Madrasah:</strong> Belajar lebih dalam, memahami lebih bermakna. Mengubah kebiasaan menghafal menjadi memecahkan masalah.</li>
                        <li style="margin-bottom: 8px;"><strong>Digital & AI Madrasah:</strong> Menjadi cakap di era teknologi, namun tetap teguh pada etika dan nilai kemanusiaan.</li>
                    </ul>
                </div>
            </details>
            
            <div style="text-align: center; margin-top: 15px;">
                <a href="pdf_viewer.php?id=17" style="font-size: 0.85rem; color: #10b981; font-weight: 700; text-decoration: none;"><i class="fa-solid fa-file-pdf"></i> Buka / Download Versi PDF Lengkap</a>
            </div>
        </div>
    </div>
</div>

<style>
details > summary::-webkit-details-marker { display: none; }
</style>

<?php 
if (isset($_SESSION['guru_id'])) {
    include 'footer_guru.php'; 
} else {
    include 'footer.php';
}
?>
