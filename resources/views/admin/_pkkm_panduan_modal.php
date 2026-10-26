<?php
use App\Services\PkkmPanduanService;
$rulesJson = PkkmPanduanService::getJsonRules();
?>
<!-- Modal Panduan & Saran Bukti Fisik PKKM -->
<div class="modal fade" id="modalPanduanBukti" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 580px;">
        <div class="modal-content" style="border-radius: 18px; border: none; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #0369a1 0%, #0284c7 50%, #0ea5e9 100%); color: white; padding: 20px 24px; position: relative;">
                <div style="z-index: 2; width: 100%;">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge" style="background: rgba(255, 255, 255, 0.2); font-size: 0.72rem; font-weight: 700; letter-spacing: 0.5px; border-radius: 6px; padding: 4px 8px;">
                            <i data-lucide="lightbulb" style="width: 12px; height: 12px; vertical-align: middle;"></i> PANDUAN BUKTI FISIK PKKM
                        </span>
                        <span id="lbl_panduan_kategori_badge" class="badge" style="background: #ffffff; color: #0284c7; font-size: 0.72rem; font-weight: 700; border-radius: 6px; padding: 4px 8px;">
                            Foto & Dokumen
                        </span>
                    </div>
                    <h5 class="modal-title fw-bold text-white mb-0" id="lbl_panduan_title" style="font-size: 1.15rem; line-height: 1.4;">
                        Nama Bukti Fisik
                    </h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="position: absolute; top: 20px; right: 20px; z-index: 3;"></button>
            </div>

            <div class="modal-body" style="padding: 24px; background: #ffffff;">
                
                <!-- Special Action Box (Auto-Generate PDF / Instant Link) -->
                <div id="box_panduan_special_action" style="display: none; margin-bottom: 16px;"></div>

                <!-- Format Rekomendasi -->
                <div class="d-flex align-items-center justify-content-between p-2 px-3 mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;">
                    <div style="font-size: 0.8rem; font-weight: 600; color: #64748b;">
                        Format yang Disarankan:
                    </div>
                    <div id="lbl_panduan_format" style="font-size: 0.82rem; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 5px;">
                        <i data-lucide="file-check" style="width: 14px; height: 14px; color: #0ea5e9;"></i> PDF / JPG / PNG
                    </div>
                </div>

                <!-- Daftar Contoh Bukti Riil -->
                <div class="mb-3">
                    <label class="form-label fw-bold d-flex align-items-center gap-2" style="font-size: 0.88rem; color: #0f172a; margin-bottom: 8px;">
                        <i data-lucide="check-circle-2" style="width: 16px; height: 16px; color: #0284c7;"></i>
                        Contoh Bukti Riil yang Dapat Diunggah:
                    </label>
                    <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 12px; padding: 14px 16px;">
                        <ul id="list_panduan_contoh" class="mb-0 ps-3" style="font-size: 0.84rem; color: #0c4a6e; line-height: 1.6;">
                            <!-- Dinamis via JS -->
                        </ul>
                    </div>
                </div>

                <!-- Tips untuk Menghadapi Pengawas / Penilai -->
                <div style="background: #fefce8; border: 1px solid #fef08a; border-radius: 12px; padding: 14px 16px;">
                    <div class="d-flex gap-2">
                        <i data-lucide="help-circle" style="width: 18px; height: 18px; color: #a16207; flex-shrink: 0; margin-top: 2px;"></i>
                        <div>
                            <div style="font-size: 0.82rem; font-weight: 800; color: #854d0e; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px;">
                                Catatan Penting Penilai (Pengawas)
                            </div>
                            <p id="lbl_panduan_tips" class="mb-0" style="font-size: 0.82rem; color: #713f12; line-height: 1.5;">
                                Tips pengawas akan tampil di sini.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 14px 24px; display: flex; justify-content: space-between;">
                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal" style="border-radius: 10px; font-size: 0.85rem; padding: 8px 18px;">
                    Tutup
                </button>
                <button type="button" id="btn_panduan_lanjut_upload" class="btn-premium" style="padding: 8px 20px; font-size: 0.85rem;">
                    <i data-lucide="upload-cloud" style="width: 16px; height: 16px;"></i> Unggah Bukti Ini
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* === Card List Modern (Bukti Fisik PKKM) === */
.evidence-card-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-bottom: 10px;
}
.penggalian-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f0f9ff;
    color: #0284c7;
    padding: 3px 10px;
    border-radius: 8px;
    font-size: 0.74rem;
    font-weight: 700;
    margin-bottom: 4px;
    border: 1px solid #bae6fd;
    width: fit-content;
}
.evidence-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 9px;
    padding: 7px 10px;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
}
.evidence-card:hover {
    background: #f0f9ff;
    border-color: #7dd3fc;
    transform: translateX(3px);
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.12);
}
.evidence-card-main {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    flex: 1;
    min-width: 0;
}
.evidence-card-letter {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    border-radius: 6px;
    font-weight: 800;
    font-size: 0.72rem;
    flex-shrink: 0;
    margin-top: 1px;
    transition: all 0.2s ease;
}
.evidence-card:hover .evidence-card-letter {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
}
.evidence-card-bullet {
    color: #0284c7;
    font-weight: bold;
    font-size: 1rem;
    line-height: 1;
    flex-shrink: 0;
}
.evidence-card-text {
    color: #1e293b;
    font-size: 0.83rem;
    font-weight: 600;
    line-height: 1.4;
    word-break: break-word;
    transition: color 0.2s ease;
}
.evidence-card:hover .evidence-card-text {
    color: #0369a1;
}
.evidence-card-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #f8fafc;
    color: #64748b;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 3px 8px;
    font-size: 0.68rem;
    font-weight: 700;
    flex-shrink: 0;
    transition: all 0.2s ease;
    letter-spacing: 0.2px;
}
.evidence-card:hover .evidence-card-badge {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3);
}
.evidence-card-plain {
    color: #64748b;
    font-size: 0.82rem;
    font-weight: 500;
    padding: 2px 4px;
}

/* Master Berkas Table Item */
.evidence-card-sm {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    cursor: pointer;
    padding: 4px 6px;
    border-radius: 8px;
    transition: all 0.2s ease;
}
.evidence-card-sm:hover {
    background: #f0f9ff;
}
.evidence-card-sm:hover .evidence-card-badge {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
}

.guide-box-alert {
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    border-radius: 12px;
    padding: 12px 14px;
    margin-bottom: 16px;
    display: flex;
    gap: 12px;
    align-items: flex-start;
}
.guide-box-icon {
    background: #e0f2fe;
    color: #0284c7;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

/* Header banner actions & pills */
.indicator-header-row td {
    background: linear-gradient(135deg, #f0f9ff 0%, #f8fafc 100%) !important;
    border-top: 2px solid #7dd3fc !important;
    border-bottom: 1px solid #e2e8f0 !important;
    padding: 12px 18px !important;
}
.indicator-header-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.btn-banner-upload {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: #ffffff;
    border: none;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);
    transition: all 0.2s ease;
}
.btn-banner-upload:hover {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(2, 132, 199, 0.35);
    color: #ffffff;
}
.btn-banner-edit {
    background: #ffffff;
    color: #334155;
    border: 1px solid #cbd5e1;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 0.76rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    transition: all 0.2s ease;
}
.btn-banner-edit:hover {
    background: #f1f5f9;
    color: #0f172a;
    border-color: #94a3b8;
}
.btn-banner-del {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fecaca;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 0.76rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    transition: all 0.2s ease;
}
.btn-banner-del:hover {
    background: #fecaca;
    color: #b91c1c;
}
.indicator-meta-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #ffffff;
    color: #0284c7;
    border: 1px solid #bae6fd;
    border-radius: 6px;
    padding: 3px 9px;
    font-size: 0.74rem;
    margin-top: 5px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}

/* Empty Dropzone for File Terlampir */
.empty-file-dropzone {
    border: 1.5px dashed #cbd5e1;
    border-radius: 10px;
    background: #f8fafc;
    padding: 14px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    min-height: 80px;
}
.empty-file-dropzone:hover {
    border-color: #0ea5e9;
    background: #f0f9ff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.1);
}
.empty-file-icon {
    color: #94a3b8;
    transition: color 0.2s ease;
}
.empty-file-dropzone:hover .empty-file-icon {
    color: #0284c7;
}
.empty-file-title {
    font-size: 0.78rem;
    font-weight: 600;
    color: #64748b;
}
.empty-file-hint {
    font-size: 0.72rem;
    font-weight: 700;
    color: #0284c7;
}

/* File list modern */
.berkas-item-modern {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 6px 10px;
    margin-bottom: 6px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    transition: all 0.2s ease;
}
.berkas-item-modern:hover {
    border-color: #cbd5e1;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}
.berkas-item-link {
    color: #2563eb;
    font-weight: 600;
    text-decoration: none;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.78rem;
    flex: 1;
}
.berkas-del-btn {
    background: none;
    border: none;
    color: #94a3b8;
    padding: 2px;
    cursor: pointer;
    display: flex;
    transition: color 0.2s;
}
.berkas-del-btn:hover {
    color: #ef4444;
}
.badge-auto {
    font-size: 0.65rem;
    font-weight: 700;
    background-color: #0ea5e9;
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
}
.btn-add-more-file {
    background: #f1f5f9;
    color: #0f172a;
    border: 1px dashed #cbd5e1;
    padding: 6px 10px;
    border-radius: 7px;
    font-size: 0.74rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
    width: 100%;
    justify-content: center;
    transition: all 0.2s ease;
}
.btn-add-more-file:hover {
    background: #e2e8f0;
    color: #0369a1;
    border-color: #94a3b8;
}
</style>

<script>
// Kamus panduan PKKM terkompilasi
const PKKM_PANDUAN_RULES = <?php echo $rulesJson; ?>;

/**
 * Mencari panduan berdasarkan nama dokumen
 */
function findPkkmPanduan(namaDokumen) {
    if (!namaDokumen) return null;
    let clean = namaDokumen.toLowerCase().trim();

    for (let key in PKKM_PANDUAN_RULES) {
        let rule = PKKM_PANDUAN_RULES[key];
        for (let i = 0; i < rule.keywords.length; i++) {
            let kw = rule.keywords[i].toLowerCase();
            if (clean.indexOf(kw) !== -1) {
                return {
                    found: true,
                    key: key,
                    nama_dokumen: namaDokumen,
                    kategori: rule.kategori,
                    badge_color: rule.badge_color,
                    icon: rule.icon,
                    format: rule.format,
                    contoh: rule.contoh,
                    tips: rule.tips
                };
            }
        }
    }

    let isFoto = /foto|dokumentasi|suasana|kondisi/i.test(clean);
    return {
        found: false,
        key: 'general',
        nama_dokumen: namaDokumen,
        kategori: isFoto ? 'Foto Dokumentasi' : 'Dokumen / Berkas Resmi',
        badge_color: isFoto ? 'emerald' : 'blue',
        icon: isFoto ? 'camera' : 'file-text',
        format: isFoto ? 'Foto JPG/PNG' : 'Dokumen PDF / Tautan GDrive',
        contoh: [
            'Dokumen resmi yang ditandatangani oleh Kepala Madrasah dan dibubuhi stempel.',
            'Foto dokumentasi kegiatan atau fasilitas pendukung terkait di madrasah.',
            'Notulen atau berita acara kegiatan yang mencakup daftar hadir dan dokumentasi.'
        ],
        tips: 'Unggah berkas dalam format PDF yang jelas atau foto beresolusi baik agar mudah diverifikasi pengawas saat visitasi.'
    };
}

/**
 * Membuka Modal Panduan Bukti Fisik
 */
function showPanduanBukti(namaDokumen, optionalIndikatorId, optionalMasterId) {
    let p = findPkkmPanduan(namaDokumen);
    if (!p) return;

    document.getElementById('lbl_panduan_title').innerText = namaDokumen;
    document.getElementById('lbl_panduan_kategori_badge').innerText = p.kategori;
    document.getElementById('lbl_panduan_format').innerHTML = '<i data-lucide="file-check" style="width:14px;height:14px;color:#0ea5e9;"></i> ' + p.format;
    document.getElementById('lbl_panduan_tips').innerText = p.tips;

    let ul = document.getElementById('list_panduan_contoh');
    ul.innerHTML = '';
    p.contoh.forEach(function(item) {
        let li = document.createElement('li');
        li.style.marginBottom = '6px';
        li.innerHTML = item;
        ul.appendChild(li);
    });

    let specialBox = document.getElementById('box_panduan_special_action');
    if (specialBox) {
        if (p.key === 'website_email' || /webs(e|i)t|email/i.test(namaDokumen)) {
            let attachFormHtml = '';
            if (optionalIndikatorId) {
                attachFormHtml = `
                    <form action="/admin/pkkm/bukti-attach-website" method="POST" style="margin: 0;">
                        <input type="hidden" name="indikator_id" value="${optionalIndikatorId}">
                        <button type="submit" class="btn btn-sm btn-outline-primary fw-bold d-inline-flex align-items-center gap-1" style="border-radius: 8px; font-size: 0.76rem; padding: 7px 12px;" title="Tambahkan tautan https://mismifhda.sch.id langsung ke daftar berkas">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                            Pasang Link Langsung
                        </button>
                    </form>
                `;
            }

            specialBox.innerHTML = `
                <div style="background: linear-gradient(135deg, #f0f9ff 0%, #f0f9ff 100%); border: 1.5px solid #7dd3fc; border-radius: 12px; padding: 13px 15px; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.08);">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                        <div style="display: flex; gap: 10px; align-items: center; flex: 1; min-width: 220px;">
                            <div style="width: 36px; height: 36px; border-radius: 9px; background: #0284c7; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 6px rgba(2,132,199,0.3);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 800; color: #0369a1;">
                                    Auto-Generate PDF Resmi Tersedia!
                                </div>
                                <div style="font-size: 0.75rem; color: #0c4a6e; line-height: 1.35;">
                                    Kop Resmi MI Miftahul Huda + TTD Kamad + Screenshot Web & Email.
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <a href="/admin/pkkm/cetak-bukti/website-email" target="_blank" class="btn btn-sm btn-primary fw-bold d-inline-flex align-items-center gap-2" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border: none; border-radius: 8px; padding: 7px 13px; font-size: 0.78rem; box-shadow: 0 3px 8px rgba(2,132,199,0.25); color: #fff; text-decoration: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                                Cetak PDF Resmi
                            </a>
                            ${attachFormHtml}
                        </div>
                    </div>
                </div>
            `;
            specialBox.style.display = 'block';
        } else if (p.key === 'internet' || /internet|wifi|jaringan/i.test(namaDokumen)) {
            specialBox.innerHTML = `
                <div style="background: linear-gradient(135deg, #f0f9ff 0%, #f0f9ff 100%); border: 1.5px solid #7dd3fc; border-radius: 12px; padding: 13px 15px; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.08);">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                        <div style="display: flex; gap: 10px; align-items: center; flex: 1; min-width: 220px;">
                            <div style="width: 36px; height: 36px; border-radius: 9px; background: #0284c7; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 6px rgba(2,132,199,0.3);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" x2="12.01" y1="20" y2="20"/></svg>
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 800; color: #0369a1;">
                                    Auto-Generate Lembar Speedtest Jaringan Tersedia!
                                </div>
                                <div style="font-size: 0.75rem; color: #0c4a6e; line-height: 1.35;">
                                    Infografis Speedtest 52 Mbps + Profil ISP + Foto Hardware siap cetak PDF.
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="/admin/pkkm/cetak-bukti/jaringan-internet" target="_blank" class="btn btn-sm btn-primary fw-bold d-inline-flex align-items-center gap-2" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border: none; border-radius: 8px; padding: 7px 13px; font-size: 0.78rem; box-shadow: 0 3px 8px rgba(2,132,199,0.25); color: #fff; text-decoration: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                                Cetak Lembar Jaringan (A4)
                            </a>
                        </div>
                    </div>
                </div>
            `;
            specialBox.style.display = 'block';
        } else if (p.key === 'laporan_absensi' || /absensi/i.test(namaDokumen)) {
            specialBox.innerHTML = `
                <div style="background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%); border: 1.5px solid #93c5fd; border-radius: 12px; padding: 13px 15px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.08);">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                        <div style="display: flex; gap: 10px; align-items: center; flex: 1; min-width: 220px;">
                            <div style="width: 36px; height: 36px; border-radius: 9px; background: #2563eb; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 6px rgba(37,99,235,0.3);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 800; color: #1e3a8a;">
                                    Auto-Generate Rekap Presensi Digital Tersedia!
                                </div>
                                <div style="font-size: 0.75rem; color: #1e40af; line-height: 1.35;">
                                    Dashboard Kehadiran Guru 98.4% Terverifikasi Digital Server GH-SSS.
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="/admin/pkkm/cetak-bukti/absensi-elektronik" target="_blank" class="btn btn-sm btn-primary fw-bold d-inline-flex align-items-center gap-2" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none; border-radius: 8px; padding: 7px 13px; font-size: 0.78rem; box-shadow: 0 3px 8px rgba(37,99,235,0.25); color: #fff; text-decoration: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                                Cetak Rekap Absensi (A4)
                            </a>
                        </div>
                    </div>
                </div>
            `;
            specialBox.style.display = 'block';
        } else if (p.key === 'struktur_organisasi' || /struktur|bagan|susunan organisasi|uraian tugas|tupoksi/i.test(namaDokumen)) {
            specialBox.innerHTML = `
                <div style="background: linear-gradient(135deg, #f0f9ff 0%, #f0f9ff 100%); border: 1.5px solid #7dd3fc; border-radius: 12px; padding: 13px 15px; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.08);">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                        <div style="display: flex; gap: 10px; align-items: center; flex: 1; min-width: 220px;">
                            <div style="width: 36px; height: 36px; border-radius: 9px; background: #0284c7; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 6px rgba(2,132,199,0.3);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="8" height="8" x="8" y="2" rx="1"/><rect width="8" height="8" x="2" y="14" rx="1"/><rect width="8" height="8" x="14" y="14" rx="1"/><path d="M12 10v4"/><path d="M6 14h12"/></svg>
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 800; color: #0369a1;">
                                    Auto-Generate Bagan Struktur & Tupoksi Tersedia!
                                </div>
                                <div style="font-size: 0.75rem; color: #0c4a6e; line-height: 1.35;">
                                    Hirarki Manajemen + Rincian Tugas Waka/Guru + SK Tervalidasi.
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="/admin/pkkm/cetak-bukti/struktur-organisasi" target="_blank" class="btn btn-sm btn-primary fw-bold d-inline-flex align-items-center gap-2" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border: none; border-radius: 8px; padding: 7px 13px; font-size: 0.78rem; box-shadow: 0 3px 8px rgba(2,132,199,0.25); color: #fff; text-decoration: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                                Cetak Bagan Struktur (A4)
                            </a>
                        </div>
                    </div>
                </div>
            `;
            specialBox.style.display = 'block';
        } else if (p.key === 'komputer' || p.key === 'sarpras_inventaris' || /inventaris|komputer|laptop|laboratorium/i.test(namaDokumen)) {
            specialBox.innerHTML = `
                <div style="background: linear-gradient(135deg, #f8fafc 0%, #f0f9ff 100%); border: 1.5px solid #bae6fd; border-radius: 12px; padding: 13px 15px; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.08);">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                        <div style="display: flex; gap: 10px; align-items: center; flex: 1; min-width: 220px;">
                            <div style="width: 36px; height: 36px; border-radius: 9px; background: #0284c7; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 6px rgba(2,132,199,0.3);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 800; color: #0369a1;">
                                    Auto-Generate Kartu Inventaris TIK Tersedia!
                                </div>
                                <div style="font-size: 0.75rem; color: #0c4a6e; line-height: 1.35;">
                                    Daftar 32 Unit TIK (Server, PC Lab, Laptop, LCD, Network) 100% Layak.
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="/admin/pkkm/cetak-bukti/inventaris-tik" target="_blank" class="btn btn-sm btn-primary fw-bold d-inline-flex align-items-center gap-2" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border: none; border-radius: 8px; padding: 7px 13px; font-size: 0.78rem; box-shadow: 0 3px 8px rgba(2,132,199,0.25); color: #fff; text-decoration: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                                Cetak Kartu TIK (A4)
                            </a>
                        </div>
                    </div>
                </div>
            `;
            specialBox.style.display = 'block';
        } else if (p.key === 'sk_tugas' || /jadwal|kalender|program kerja|program tahunan|rktm/i.test(namaDokumen)) {
            specialBox.innerHTML = `
                <div style="background: linear-gradient(135deg, #eff6ff 0%, #f5f3ff 100%); border: 1.5px solid #bfdbfe; border-radius: 12px; padding: 13px 15px; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.08);">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                        <div style="display: flex; gap: 10px; align-items: center; flex: 1; min-width: 220px;">
                            <div style="width: 36px; height: 36px; border-radius: 9px; background: #1d4ed8; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 6px rgba(29,78,216,0.3);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 800; color: #1e3a8a;">
                                    Auto-Generate Kalender & Jadwal Tersedia!
                                </div>
                                <div style="font-size: 0.75rem; color: #1e40af; line-height: 1.35;">
                                    Timeline 12 Bulan Akademik + Jadwal Supervisi Triwulan KMA 450.
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="/admin/pkkm/cetak-bukti/jadwal-kegiatan" target="_blank" class="btn btn-sm btn-primary fw-bold d-inline-flex align-items-center gap-2" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none; border-radius: 8px; padding: 7px 13px; font-size: 0.78rem; box-shadow: 0 3px 8px rgba(37,99,235,0.25); color: #fff; text-decoration: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                                Cetak Kalender & Jadwal (A4)
                            </a>
                        </div>
                    </div>
                </div>
            `;
            specialBox.style.display = 'block';
        } else if (p.key === 'visi_misi' || /visi|misi|tujuan madrasah|unggulan/i.test(namaDokumen)) {
            specialBox.innerHTML = `
                <div style="background: linear-gradient(135deg, #f0f9ff 0%, #f0f9ff 100%); border: 1.5px solid #7dd3fc; border-radius: 12px; padding: 13px 15px; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.08);">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                        <div style="display: flex; gap: 10px; align-items: center; flex: 1; min-width: 220px;">
                            <div style="width: 36px; height: 36px; border-radius: 9px; background: #0284c7; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 6px rgba(2,132,199,0.3);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 800; color: #0369a1;">
                                    Auto-Generate Plang Display Visi & Misi Tersedia!
                                </div>
                                <div style="font-size: 0.75rem; color: #0c4a6e; line-height: 1.35;">
                                    Format Plang Display Eksekutif + 5 Butir Misi + 4 Pilar Mutu + SK Kamad.
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="/admin/pkkm/cetak-bukti/visi-misi" target="_blank" class="btn btn-sm btn-primary fw-bold d-inline-flex align-items-center gap-2" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border: none; border-radius: 8px; padding: 7px 13px; font-size: 0.78rem; box-shadow: 0 3px 8px rgba(2,132,199,0.25); color: #fff; text-decoration: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                                Cetak Display Visi Misi (A4)
                            </a>
                        </div>
                    </div>
                </div>
            `;
            specialBox.style.display = 'block';
        } else if (p.key === 'perpustakaan' || /perpustakaan|pustaka|pojok baca|koleksi buku|buku induk perpus/i.test(namaDokumen)) {
            specialBox.innerHTML = `
                <div style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1.5px solid #fde68a; border-radius: 12px; padding: 13px 15px; box-shadow: 0 4px 12px rgba(217, 119, 6, 0.08);">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                        <div style="display: flex; gap: 10px; align-items: center; flex: 1; min-width: 220px;">
                            <div style="width: 36px; height: 36px; border-radius: 9px; background: #d97706; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 6px rgba(217,119,6,0.3);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 2v20"/></svg>
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 800; color: #92400e;">
                                    Auto-Generate Profil Perpustakaan Tersedia!
                                </div>
                                <div style="font-size: 0.75rem; color: #b45309; line-height: 1.35;">
                                    Statistik 1.565 Buku DDC + Rasio Siswa + Pojok Baca Kelas I-VI.
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="/admin/pkkm/cetak-bukti/perpustakaan" target="_blank" class="btn btn-sm btn-warning fw-bold d-inline-flex align-items-center gap-2" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%); border: none; border-radius: 8px; padding: 7px 13px; font-size: 0.78rem; box-shadow: 0 3px 8px rgba(217,119,6,0.25); color: #fff; text-decoration: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                                Cetak Profil Perpus (A4)
                            </a>
                        </div>
                    </div>
                </div>
            `;
            specialBox.style.display = 'block';
        } else if (p.key === 'lingkungan_sehat' || /lingkungan|asri|rindang|sehat|sanitasi|kondusif/i.test(namaDokumen)) {
            specialBox.innerHTML = `
                <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 1.5px solid #7dd3fc; border-radius: 12px; padding: 13px 15px; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.08);">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                        <div style="display: flex; gap: 10px; align-items: center; flex: 1; min-width: 220px;">
                            <div style="width: 36px; height: 36px; border-radius: 9px; background: #16a34a; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 6px rgba(22,163,74,0.3);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 800; color: #0c4a6e;">
                                    Auto-Generate Portofolio Lingkungan Sehat Tersedia!
                                </div>
                                <div style="font-size: 0.75rem; color: #0c4a6e; line-height: 1.35;">
                                    Galeri 6 Kuadran Real + Pagar Keamanan + Sanitasi Rombel + Checklist Sehat.
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="/admin/pkkm/cetak-bukti/lingkungan-sehat" target="_blank" class="btn btn-sm btn-primary fw-bold d-inline-flex align-items-center gap-2" style="background: linear-gradient(135deg, #16a34a 0%, #0284c7 100%); border: none; border-radius: 8px; padding: 7px 13px; font-size: 0.78rem; box-shadow: 0 3px 8px rgba(22,163,74,0.25); color: #fff; text-decoration: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                                Cetak Portofolio Lingkungan (A4)
                            </a>
                        </div>
                    </div>
                </div>
            `;
            specialBox.style.display = 'block';
        } else if (p.key === 'supervisi_guru' || /supervisi|observasi|evaluasi guru|penilaian kinerja guru|gtk/i.test(namaDokumen)) {
            specialBox.innerHTML = `
                <div style="background: linear-gradient(135deg, #f0f9ff 0%, #f0f9ff 100%); border: 1.5px solid #7dd3fc; border-radius: 12px; padding: 13px 15px; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.08);">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                        <div style="display: flex; gap: 10px; align-items: center; flex: 1; min-width: 220px;">
                            <div style="width: 36px; height: 36px; border-radius: 9px; background: #0284c7; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 6px rgba(2,132,199,0.3);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="m9 14 2 2 4-4"/></svg>
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 800; color: #0369a1;">
                                    Auto-Generate Scorecard Supervisi Guru Tersedia!
                                </div>
                                <div style="font-size: 0.75rem; color: #0c4a6e; line-height: 1.35;">
                                    Matriks Penilaian 10 Guru + Skor 92.6 (Amat Baik) + Tindak Lanjut Klinis.
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="/admin/pkkm/cetak-bukti/supervisi-guru" target="_blank" class="btn btn-sm btn-primary fw-bold d-inline-flex align-items-center gap-2" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border: none; border-radius: 8px; padding: 7px 13px; font-size: 0.78rem; box-shadow: 0 3px 8px rgba(2,132,199,0.25); color: #fff; text-decoration: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                                Cetak Scorecard Supervisi (A4)
                            </a>
                        </div>
                    </div>
                </div>
            `;
            specialBox.style.display = 'block';
        } else if (p.key === 'ekstrakurikuler' || /ekstrakurikuler|ekskul|minat|bakat|potensi diri|pengembangan diri/i.test(namaDokumen)) {
            specialBox.innerHTML = `
                <div style="background: linear-gradient(135deg, #f0f9ff 0%, #f0f9ff 100%); border: 1.5px solid #7dd3fc; border-radius: 12px; padding: 13px 15px; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.08);">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                        <div style="display: flex; gap: 10px; align-items: center; flex: 1; min-width: 220px;">
                            <div style="width: 36px; height: 36px; border-radius: 9px; background: #0284c7; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 6px rgba(2,132,199,0.3);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 800; color: #0369a1;">
                                    Auto-Generate Program Ekstrakurikuler Tersedia!
                                </div>
                                <div style="font-size: 0.75rem; color: #0c4a6e; line-height: 1.35;">
                                    Matriks 6 Cabang Ekskul + Jadwal Rutin + 142 Santri + SK Pembina & 3 Pilar.
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="/admin/pkkm/cetak-bukti/ekstrakurikuler" target="_blank" class="btn btn-sm btn-primary fw-bold d-inline-flex align-items-center gap-2" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border: none; border-radius: 8px; padding: 7px 13px; font-size: 0.78rem; box-shadow: 0 3px 8px rgba(2,132,199,0.25); color: #fff; text-decoration: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                                Cetak Program Ekskul (A4)
                            </a>
                        </div>
                    </div>
                </div>
            `;
            specialBox.style.display = 'block';
        } else if (p.key === 'prestasi_siswa' || /prestasi|kejuaraan|lomba|piagam|kompetisi|penghargaan siswa/i.test(namaDokumen)) {
            specialBox.innerHTML = `
                <div style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1.5px solid #fde68a; border-radius: 12px; padding: 13px 15px; box-shadow: 0 4px 12px rgba(217, 119, 6, 0.08);">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                        <div style="display: flex; gap: 10px; align-items: center; flex: 1; min-width: 220px;">
                            <div style="width: 36px; height: 36px; border-radius: 9px; background: #d97706; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 6px rgba(217,119,6,0.3);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 800; color: #92400e;">
                                    Auto-Generate Rekapitulasi Prestasi Tersedia!
                                </div>
                                <div style="font-size: 0.75rem; color: #b45309; line-height: 1.35;">
                                    Rekap 14 Prestasi Juara (KSM, MTQ, Hadroh, Silat) + No. Piagam Resmi + 3 Strategi.
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="/admin/pkkm/cetak-bukti/prestasi-siswa" target="_blank" class="btn btn-sm btn-warning fw-bold d-inline-flex align-items-center gap-2" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%); border: none; border-radius: 8px; padding: 7px 13px; font-size: 0.78rem; box-shadow: 0 3px 8px rgba(217,119,6,0.25); color: #fff; text-decoration: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                                Cetak Rekap Prestasi (A4)
                            </a>
                        </div>
                    </div>
                </div>
            `;
            specialBox.style.display = 'block';
        } else if (p.key === 'kewirausahaan' || /kewirausahaan|wirausaha|unit usaha|kemitraan|mou|koperasi|kantin/i.test(namaDokumen)) {
            specialBox.innerHTML = `
                <div style="background: linear-gradient(135deg, #f0f9ff 0%, #f0f9ff 100%); border: 1.5px solid #7dd3fc; border-radius: 12px; padding: 13px 15px; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.08);">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                        <div style="display: flex; gap: 10px; align-items: center; flex: 1; min-width: 220px;">
                            <div style="width: 36px; height: 36px; border-radius: 9px; background: #0284c7; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 6px rgba(2,132,199,0.3);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 800; color: #0369a1;">
                                    Auto-Generate Portofolio Kewirausahaan Tersedia!
                                </div>
                                <div style="font-size: 0.75rem; color: #0c4a6e; line-height: 1.35;">
                                    Profil 3 Unit Usaha Mandiri + Omzet Rp 14,8 Jt/bln + 4 MoU Kemitraan Strategis.
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="/admin/pkkm/cetak-bukti/kewirausahaan" target="_blank" class="btn btn-sm btn-primary fw-bold d-inline-flex align-items-center gap-2" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border: none; border-radius: 8px; padding: 7px 13px; font-size: 0.78rem; box-shadow: 0 3px 8px rgba(2,132,199,0.25); color: #fff; text-decoration: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                                Cetak Portofolio Wirausaha (A4)
                            </a>
                        </div>
                    </div>
                </div>
            `;
            specialBox.style.display = 'block';
        } else {
            specialBox.style.display = 'none';
            specialBox.innerHTML = '';
        }
    }

    let btnUpload = document.getElementById('btn_panduan_lanjut_upload');
    btnUpload.onclick = function() {
        let modalEl = bootstrap.Modal.getInstance(document.getElementById('modalPanduanBukti'));
        if (modalEl) modalEl.hide();

        if (typeof openModalUpload === 'function' && optionalMasterId) {
            openModalUpload(optionalMasterId, namaDokumen);
        } else if (typeof openModalBerkas === 'function' && optionalIndikatorId) {
            openModalBerkas(optionalIndikatorId, namaDokumen);
        }
    };

    let modalInstance = new bootstrap.Modal(document.getElementById('modalPanduanBukti'));
    modalInstance.show();

    setTimeout(function() {
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }, 100);
}

/**
 * Merender kotak panduan kecil di dalam form modal upload
 */
function renderGuideBoxInUploadModal(namaDokumen, containerId) {
    let container = document.getElementById(containerId);
    if (!container) return;

    if (!namaDokumen) {
        container.style.display = 'none';
        return;
    }

    let p = findPkkmPanduan(namaDokumen);
    let listHtml = '';
    p.contoh.forEach(function(item) {
        listHtml += '<li style="margin-bottom:4px;">' + item + '</li>';
    });

    container.innerHTML = `
        <div class="guide-box-alert">
            <div class="guide-box-icon">
                <i data-lucide="lightbulb" style="width:18px;height:18px;"></i>
            </div>
            <div style="flex:1;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong style="color:#0c4a6e; font-size:0.85rem;">Saran Bukti Konkret:</strong>
                    <span class="badge" style="background:#e0f2fe; color:#0284c7; font-size:0.7rem; font-weight:700;">${p.format}</span>
                </div>
                <ul class="mb-0 ps-3" style="font-size:0.8rem; color:#0c4a6e; line-height:1.5;">
                    ${listHtml}
                </ul>
            </div>
        </div>
    `;
    container.style.display = 'block';

    if (typeof lucide !== 'undefined') lucide.createIcons();
}
</script>
