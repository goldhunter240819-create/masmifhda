<?php
$pageTitle = 'Sinkronisasi CBT Semi-Online';
$active_menu = 'semi_online';

$isServerPusat = (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'masmifhda.sch.id') !== false);
?>
<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/header.php'; ?>

        <div class="z-scroll" style="padding: 0 0 4rem 0;">

            <!-- Header Banner -->
            <div style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%); border-radius: 20px; padding: 2rem 2.25rem; color: white; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(49, 46, 129, 0.25); position: relative; overflow: hidden;">
                <div style="position: absolute; right: -20px; bottom: -20px; opacity: 0.08; font-size: 14rem; pointer-events: none;">
                    <i data-lucide="cpu"></i>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; position: relative; z-index: 1;">
                    <div>
                        <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.15); backdrop-filter: blur(8px); padding: 5px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.75rem; border: 1px solid rgba(255,255,255,0.2);">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: <?= $isServerPusat ? '#38bdf8' : '#4ade80' ?>; display: inline-block;"></span>
                            <?= $isServerPusat ? 'Mode Server Pusat (Online Cloud)' : 'Mode Server Lokal (Proktor Semi-Online)' ?>
                        </div>
                        <h1 style="font-size: 1.65rem; font-weight: 800; margin-bottom: 0.5rem; letter-spacing: -0.5px;">Sinkronisasi CBT Semi-Online</h1>
                        <p style="color: #c7d2fe; font-size: 0.9rem; max-width: 650px; line-height: 1.5; margin: 0;">
                            Teknologi pelaksanaan ujian mandiri ala ANBK. Siswa madrasah mengerjakan ujian di jaringan lokal Wi-Fi (0 delay & bebas kuota internet), lalu hasilnya disinkronkan kembali ke server online pusat!
                        </p>
                    </div>

                    <div style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); border-radius: 16px; padding: 1rem 1.25rem; min-width: 260px; text-align: right;">
                        <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 800; color: #a5b4fc; margin-bottom: 4px;">Alamat IP Server Ini (Wi-Fi)</div>
                        <div style="font-size: 1.25rem; font-weight: 900; color: #ffffff; font-family: monospace; letter-spacing: 0.5px;">
                            <?= htmlspecialchars($localIp) ?>
                        </div>
                        <div style="font-size: 0.72rem; color: #e0e7ff; margin-top: 4px;">
                            Link Ujian Siswa: <a href="<?= htmlspecialchars($localUrl) ?>" target="_blank" style="color: #38bdf8; text-decoration: underline; font-weight: 700;"><?= htmlspecialchars($localUrl) ?></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Card Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                <!-- Card 1 -->
                <div style="background: white; border-radius: 16px; padding: 1.25rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="font-size: 0.78rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Topik & Jadwal</span>
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="calendar"></i>
                        </div>
                    </div>
                    <div style="font-size: 1.5rem; font-weight: 900; color: #1e1b4b;"><?= number_format($countJadwal) ?> <span style="font-size: 0.85rem; font-weight: 600; color: #64748b;">Sesi</span></div>
                    <div style="font-size: 0.75rem; color: #059669; font-weight: 700; margin-top: 4px;"><i data-lucide="check-circle" style="width: 13px; height: 13px; display: inline;"></i> Terpasang di database lokal</div>
                </div>

                <!-- Card 2 -->
                <div style="background: white; border-radius: 16px; padding: 1.25rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="font-size: 0.78rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Butir Soal CBT</span>
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="database"></i>
                        </div>
                    </div>
                    <div style="font-size: 1.5rem; font-weight: 900; color: #1e1b4b;"><?= number_format($countSoal) ?> <span style="font-size: 0.85rem; font-weight: 600; color: #64748b;">Soal</span></div>
                    <div style="font-size: 0.75rem; color: #059669; font-weight: 700; margin-top: 4px;"><i data-lucide="check-circle" style="width: 13px; height: 13px; display: inline;"></i> Siap diujikan di server lokal</div>
                </div>

                <!-- Card 3 -->
                <div style="background: white; border-radius: 16px; padding: 1.25rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="font-size: 0.78rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Peserta Siswa</span>
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="users"></i>
                        </div>
                    </div>
                    <div style="font-size: 1.5rem; font-weight: 900; color: #1e1b4b;"><?= number_format($countSiswa) ?> <span style="font-size: 0.85rem; font-weight: 600; color: #64748b;">Siswa</span></div>
                    <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; margin-top: 4px;">Akun & kredensial login aktif</div>
                </div>

                <!-- Card 4: Presensi Hari Ini -->
                <div style="background: white; border-radius: 16px; padding: 1.25rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="font-size: 0.78rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Presensi Hari Ini</span>
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: #fdf4ff; color: #a855f7; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="scan-line"></i>
                        </div>
                    </div>
                    <div style="font-size: 1.5rem; font-weight: 900; color: #1e1b4b;"><?= number_format($countAbsenToday) ?> <span style="font-size: 0.85rem; font-weight: 600; color: #64748b;">Hadir</span></div>
                    <div style="font-size: 0.75rem; color: #a855f7; font-weight: 700; margin-top: 4px;">Tersinkron di server lokal</div>
                </div>

                <!-- Card 5 -->
                <div style="background: white; border-radius: 16px; padding: 1.25rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="font-size: 0.78rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Hasil Ujian Lokal</span>
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="award"></i>
                        </div>
                    </div>
                    <div style="font-size: 1.5rem; font-weight: 900; color: #1e1b4b;"><?= number_format($countHasil) ?> <span style="font-size: 0.85rem; font-weight: 600; color: #64748b;">Selesai</span></div>
                    <div style="font-size: 0.75rem; color: #2563eb; font-weight: 700; margin-top: 4px;">Siap diunggah ke Pusat</div>
                </div>
            </div>

            <!-- PAKET STANDALONE LAPTOP SERVER MADRASAH -->
            <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 20px; padding: 1.75rem 2rem; color: white; margin-bottom: 2rem; border: 1px solid #334155; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.15); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1.25rem; flex: 1; min-width: 300px;">
                    <div style="width: 52px; height: 52px; border-radius: 14px; background: rgba(56, 189, 248, 0.15); border: 1px solid rgba(56, 189, 248, 0.3); color: #38bdf8; display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
                        <i data-lucide="package-check"></i>
                    </div>
                    <div>
                        <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(56, 189, 248, 0.2); color: #7dd3fc; padding: 3px 10px; border-radius: 100px; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; margin-bottom: 4px;">
                            Khusus Server Proktor Madrasah
                        </div>
                        <h3 style="font-size: 1.18rem; font-weight: 800; margin: 0 0 4px 0; color: #ffffff;">Paket Standalone CBT (Tanpa SIAKAD / Keuangan)</h3>
                        <p style="font-size: 0.82rem; color: #94a3b8; margin: 0; line-height: 1.4;">
                            Unduh zip ini untuk dipasang di <b>laptop lain di madrasah</b>. Berisi <b>hanya engine CBT, soal, dan akun peserta</b> tanpa data keuangan, SPP, buku kasir, rapor, atau modul madrasah sensitif lainnya.
                        </p>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="/apk/download_standalone.php" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: white; text-decoration: none; padding: 12px 22px; border-radius: 12px; font-size: 0.88rem; font-weight: 800; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(14, 165, 233, 0.4); transition: 0.2s;">
                        <i data-lucide="download"></i>
                        <span>Unduh Server CBT (.zip)</span>
                    </a>
                </div>
            </div>

            <!-- Two Main Action Panels (Tahap 1 & Tahap 2) -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                
                <!-- TAHAP 1: TARIK DATA DARI PUSAT -->
                <div style="background: white; border-radius: 20px; border: 2px solid #e0e7ff; padding: 1.75rem; box-shadow: 0 8px 24px rgba(67, 56, 202, 0.06); display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 1rem;">
                            <div style="width: 44px; height: 44px; border-radius: 12px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                                <i data-lucide="cloud-download"></i>
                            </div>
                            <div>
                                <span style="font-size: 0.72rem; font-weight: 800; color: #6366f1; text-transform: uppercase; letter-spacing: 0.5px;">Langkah 1 (Pra-Ujian)</span>
                                <h3 style="font-size: 1.15rem; font-weight: 800; color: #1e1b4b; margin: 0;">Tarik Data dari Server Pusat</h3>
                            </div>
                        </div>

                        <p style="font-size: 0.82rem; color: #475569; line-height: 1.5; margin-bottom: 1.25rem;">
                            Jalankan proses ini sebelum ujian dimulai. Sistem akan mengunduh seluruh <b>Topik Ujian, Matriks Jadwal, Bank Soal Lengkap, Token Ujian, dan Akun Siswa</b> dari server online ke laptop proktor ini.
                        </p>

                        <div style="background: #f8fafc; border-radius: 12px; padding: 12px 14px; margin-bottom: 1.5rem; border: 1px solid #e2e8f0;">
                            <div style="font-size: 0.75rem; font-weight: 700; color: #334155; margin-bottom: 6px;">URL Server Pusat:</div>
                            <input type="text" id="targetServerUrl" value="https://apk.masmifhda.sch.id" style="width: 100%; border: 1px solid #cbd5e1; background: white; padding: 8px 12px; border-radius: 8px; font-size: 0.82rem; font-weight: 700; color: #1e293b; outline: none;">
                        </div>
                    </div>

                    <div>
                        <button type="button" id="btnPullData" onclick="executePullData()" style="width: 100%; background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); color: white; border: none; padding: 13px 20px; border-radius: 12px; font-size: 0.92rem; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35); transition: 0.2s; margin-bottom: 0.75rem;">
                            <i data-lucide="download"></i>
                            <span>Tarik Paket Lengkap (Soal, Jadwal & Siswa)</span>
                        </button>

                        <button type="button" id="btnPullAbsen" onclick="executePullAbsen()" style="width: 100%; background: #fdf4ff; color: #7e22ce; border: 1.5px solid #d8b4fe; padding: 11px 20px; border-radius: 12px; font-size: 0.88rem; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: 0.2s;">
                            <i data-lucide="zap" style="color: #a855f7;"></i>
                            <span>⚡ Tarik Presensi Hari Ini Saja (Kilat)</span>
                        </button>

                        <div style="font-size: 0.72rem; color: #64748b; margin-top: 10px; line-height: 1.45; background: #f8fafc; padding: 8px 12px; border-radius: 8px; border: 1px dashed #cbd5e1;">
                            <span style="color: #4338ca; font-weight: 800;"><i data-lucide="info" style="width: 12px; height: 12px; display: inline;"></i> Info Alur Presensi:</span> Siswa scan kartu di gerbang madrasah via Server Online (<a href="https://apk.masmifhda.sch.id/apk/cbt_scan.php" target="_blank" style="color: #4f46e5; font-weight: 700; text-decoration: underline;">Scan Online</a>). Setelah anak-anak masuk ruangan, proktor cukup klik <b>Tarik Presensi (Kilat)</b> di laptop ini agar kuncian soal langsung terbuka!
                        </div>
                    </div>
                </div>

                <!-- TAHAP 2: KIRIM HASIL KE PUSAT -->
                <div style="background: white; border-radius: 20px; border: 2px solid #bbf7d0; padding: 1.75rem; box-shadow: 0 8px 24px rgba(34, 197, 94, 0.06); display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 1rem;">
                            <div style="width: 44px; height: 44px; border-radius: 12px; background: #dcfce7; color: #15803d; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                                <i data-lucide="cloud-upload"></i>
                            </div>
                            <div>
                                <span style="font-size: 0.72rem; font-weight: 800; color: #16a34a; text-transform: uppercase; letter-spacing: 0.5px;">Langkah 2 (Pasca-Ujian)</span>
                                <h3 style="font-size: 1.15rem; font-weight: 800; color: #14532d; margin: 0;">Kirim Hasil ke Server Pusat</h3>
                            </div>
                        </div>

                        <p style="font-size: 0.82rem; color: #475569; line-height: 1.5; margin-bottom: 1.25rem;">
                            Jalankan proses ini setelah seluruh sesi ujian madrasah selesai. Sistem akan mengunggah <b>Nilai Siswa, Lembar Jawaban, dan Rekap Absensi CBT</b> dari laptop lokal ke database online pusat secara otomatis.
                        </p>

                        <div style="background: #f0fdf4; border-radius: 12px; padding: 12px 14px; margin-bottom: 1.5rem; border: 1px solid #bbf7d0;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 0.78rem; font-weight: 700; color: #166534;">Data Siap Unggah Hari Ini:</span>
                                <span style="font-size: 0.82rem; font-weight: 900; color: #15803d;"><?= number_format($countHasil) ?> Nilai • <?= number_format($countAbsenToday) ?> Absensi</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <button type="button" id="btnPushData" onclick="executePushData()" style="width: 100%; background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); color: white; border: none; padding: 13px 20px; border-radius: 12px; font-size: 0.92rem; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 14px rgba(22, 163, 74, 0.35); transition: 0.2s;">
                            <i data-lucide="upload"></i>
                            <span>Kirim Hasil Ujian ke Pusat</span>
                        </button>
                    </div>
                </div>

            </div>

            <!-- Panduan Siswa & QR Code Akses Wi-Fi Lokal -->
            <div style="background: white; border-radius: 20px; border: 1px solid #e2e8f0; padding: 1.75rem; box-shadow: 0 4px 16px rgba(0,0,0,0.03); margin-bottom: 2rem;">
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #1e293b; margin-bottom: 1rem; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="qr-code" style="color: #4f46e5;"></i>
                    <span>Petunjuk Akses Siswa di Madrasah (Jaringan Lokal Wi-Fi)</span>
                </h3>

                <div style="display: flex; gap: 2rem; align-items: center; flex-wrap: wrap;">
                    <div style="text-align: center; background: #f8fafc; padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= urlencode($localUrl) ?>" alt="QR Akses CBT Lokal" style="width: 150px; height: 150px; border-radius: 10px; display: block; margin: 0 auto 8px auto;">
                        <div style="font-size: 0.72rem; font-weight: 800; color: #475569;">Scan QR untuk Buka CBT</div>
                    </div>

                    <div style="flex: 1; min-width: 280px;">
                        <ol style="margin: 0; padding-left: 1.25rem; font-size: 0.85rem; color: #475569; line-height: 1.8;">
                            <li>Pastikan laptop proktor ini dan HP/Laptop siswa terhubung ke <b>nama Wi-Fi yang sama</b> di madrasah.</li>
                            <li>Siswa membuka Google Chrome atau Safe Exam Browser di HP, lalu mengetikkan alamat: <br><code style="background: #eef2ff; color: #4338ca; padding: 3px 8px; border-radius: 6px; font-weight: 800; font-size: 0.9rem;"><?= htmlspecialchars($localUrl) ?></code></li>
                            <li>Siswa juga bisa scan barcode QR di samping untuk langsung masuk ke halaman ujian lokal.</li>
                            <li>Siswa login menggunakan <b>NIS & Password</b> biasa atau scan kartu ujian via barcode scanner.</li>
                            <li>Ujian berlangsung 100% cepat tanpa hambatan kuota internet!</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Opsi Tambahan: Cadangan File Offline (.json) -->
            <div style="background: white; border-radius: 20px; border: 1px solid #e2e8f0; padding: 1.75rem; box-shadow: 0 4px 16px rgba(0,0,0,0.03);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h4 style="font-size: 1.05rem; font-weight: 800; color: #1e293b; margin: 0 0 4px 0;">Cadangan Manual Berkas Offline (Flashdisk)</h4>
                        <p style="font-size: 0.78rem; color: #64748b; margin: 0;">Gunakan opsi ini jika di madrasah sama sekali tidak ada jaringan internet untuk menarik data secara online.</p>
                    </div>

                    <div style="display: flex; gap: 8px;">
                        <form method="POST" action="<?= base_url('cbt/semi-online/export-file') ?>" style="display: inline;">
                            <button type="submit" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 10px; font-size: 0.8rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                                <i data-lucide="file-down"></i> Unduh File Paket (.json)
                            </button>
                        </form>

                        <button type="button" onclick="document.getElementById('fileImportInput').click()" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 10px; font-size: 0.8rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                            <i data-lucide="file-up"></i> Impor File (.json)
                        </button>
                        <form id="formImportFile" method="POST" action="<?= base_url('cbt/semi-online/import-file') ?>" enctype="multipart/form-data" style="display: none;">
                            <input type="file" id="fileImportInput" name="backup_file" accept=".json" onchange="document.getElementById('formImportFile').submit()">
                        </form>
                    </div>
                </div>
            </div>

        </div> <!-- .z-scroll -->
    </div> <!-- .z-main -->
</div> <!-- .siakad-container -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function executePullData() {
    const serverUrl = document.getElementById('targetServerUrl').value.trim();
    if (!serverUrl) {
        Swal.fire('Perhatian', 'URL server pusat tidak boleh kosong!', 'warning');
        return;
    }

    Swal.fire({
        title: 'Tarik Data Ujian?',
        text: 'Sistem akan mengunduh paket topik, bank soal, jadwal ujian, dan data siswa dari server pusat ke server lokal ini.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fa-solid fa-cloud-arrow-down"></i> Ya, Mulai Tarik Data',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Sedang Menarik Data...',
                text: 'Mohon tunggu, sedang mengunduh bank soal dan jadwal dari pusat...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('<?= base_url('cbt/semi-online/pull') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ server_url: serverUrl })
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    Swal.fire({
                        title: 'Sinkronisasi Berhasil!',
                        html: `<div style="text-align: left; font-size: 0.88rem; line-height: 1.6;">
                            <b>${res.message}</b><br><br>
                            📊 <b>Rincian Data yang Diperbarui:</b><br>
                            • Topik Ujian: <b>${res.details ? res.details.topik : '-'}</b><br>
                            • Sesi Jadwal: <b>${res.details ? res.details.jadwal : '-'}</b><br>
                            • Butir Soal CBT: <b>${res.details ? res.details.soal : '-'}</b><br>
                            • Data Siswa: <b>${res.details ? res.details.siswa : '-'}</b><br>
                            • Presensi Hari Ini: <b>${res.details ? (res.details.absensi || 0) : 0} Siswa</b>
                        </div>`,
                        icon: 'success',
                        confirmButtonText: 'Selesai'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Gagal Menarik Data', res.message || 'Terjadi kesalahan saat sinkronisasi.', 'error');
                }
            })
            .catch(err => {
                Swal.fire('Koneksi Gagal', 'Tidak dapat menghubungi server pusat. Pastikan URL benar dan server pusat aktif.', 'error');
            });
        }
    });
}

function executePullAbsen() {
    const serverUrl = document.getElementById('targetServerUrl').value.trim();
    if (!serverUrl) {
        Swal.fire('Perhatian', 'URL server pusat tidak boleh kosong!', 'warning');
        return;
    }

    Swal.fire({
        title: 'Tarik Presensi Hari Ini?',
        text: 'Sistem akan menarik data kehadiran siswa yang baru saja scan di server online agar kuncian soal terbuka di server lokal.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#7e22ce',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fa-solid fa-bolt"></i> Ya, Tarik Presensi',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Menarik Presensi Kilat...',
                text: 'Mohon tunggu beberapa detik...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('<?= base_url('cbt/semi-online/pull-absen') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ server_url: serverUrl })
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    Swal.fire({
                        title: 'Presensi Berhasil Ditarik!',
                        html: `<div style="text-align: left; font-size: 0.88rem; line-height: 1.6;">
                            <b>${res.message}</b><br><br>
                            👥 <b>Jumlah Presensi Diterima:</b> <b>${res.details ? (res.details.absensi || 0) : 0} Siswa</b><br>
                            Kuncian ujian siswa yang telah hadir kini otomatis terbuka di jaringan Wi-Fi lokal madrasah!
                        </div>`,
                        icon: 'success',
                        confirmButtonText: 'Alhamdulillah, Selesai'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Gagal Menarik Presensi', res.message || 'Terjadi kesalahan saat menarik data presensi.', 'error');
                }
            })
            .catch(err => {
                Swal.fire('Koneksi Gagal', 'Gagal menghubungi server pusat. Pastikan server pusat online aktif.', 'error');
            });
        }
    });
}

function executePushData() {
    const serverUrl = document.getElementById('targetServerUrl').value.trim();
    if (!serverUrl) {
        Swal.fire('Perhatian', 'URL server pusat tidak boleh kosong!', 'warning');
        return;
    }

    Swal.fire({
        title: 'Kirim Hasil Ujian ke Pusat?',
        text: 'Seluruh hasil ujian (lembar jawaban, nilai skor, dan rekap absensi) dari server lokal akan diunggah dan disatukan ke server pusat online.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fa-solid fa-cloud-arrow-up"></i> Ya, Kirim Sekarang',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Sedang Mengunggah Hasil...',
                text: 'Mohon tunggu, sedang menyatukan lembar jawaban dan nilai siswa ke pusat...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('<?= base_url('cbt/semi-online/push') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ server_url: serverUrl })
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    Swal.fire({
                        title: 'Hasil Berhasil Diunggah!',
                        html: `<div style="text-align: left; font-size: 0.88rem; line-height: 1.6;">
                            <b>${res.message}</b><br><br>
                            📊 <b>Rincian Data Terkirim:</b><br>
                            • Siswa Selesai: <b>${res.details ? res.details.peserta : '-'}</b><br>
                            • Lembar Jawaban: <b>${res.details ? res.details.jawaban : '-'}</b><br>
                            • Rekap Absensi: <b>${res.details ? res.details.absensi : '-'}</b>
                        </div>`,
                        icon: 'success',
                        confirmButtonText: 'Alhamdulillah, Selesai'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Gagal Mengunggah', res.message || 'Terjadi kesalahan saat pengiriman data.', 'error');
                }
            })
            .catch(err => {
                Swal.fire('Koneksi Gagal', 'Gagal menghubungi server pusat. Pastikan laptop proktor terhubung ke internet saat mengirim hasil.', 'error');
            });
        }
    });
}
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>
