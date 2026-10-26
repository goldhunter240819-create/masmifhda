<?php
// data variables: $siswas, $kelas, $selected_kelas, $title
$db = \App\Core\Database::connect('core');
$inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();
?>

<style>
    /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ DataTables Custom Styling Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #e2e8f0; border-radius: 8px; padding: 5px 10px; outline: none;
    }
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px 12px; outline: none; margin-left: 8px;
    }
    .dataTables_wrapper .dataTables_filter input:focus { border-color: #0ea5e9; }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 5px 12px; border-radius: 8px; margin: 0 3px; border: 1px solid transparent; transition: 0.2s;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #0ea5e9 !important; color: white !important; border-color: #0ea5e9 !important;
    }
    .dataTables_wrapper {
        padding: 0 1rem 1rem 1rem;
    }
    .dataTables_length select, .dataTables_filter input {
        border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px 12px; outline: none; background: #f8fafc; font-size: 0.85rem;
    }
    .dataTables_filter input { margin-left: 8px; min-width: 200px; }
    .dataTables_filter input:focus { border-color: #0ea5e9; background: #fff; }
    .dt-controls-wrap { display: flex; align-items: center; gap: 15px; margin-left: 20px; }
    
    /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Layout Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
    .z-main {
        margin-left: calc(var(--z-sidebar-w) + var(--z-gap) * 2);
        flex: 1; background: #f4f7f6; min-height: 100vh; padding: 1.5rem;
        display: flex; flex-direction: column;
        background-image: radial-gradient(circle at 2px 2px, rgba(0,0,0,0.02) 1px, transparent 0);
        background-size: 24px 24px;
    }
    /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Elite Page Header Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
    .elite-page-header {
        position: relative; padding: 2rem; margin-bottom: 1.5rem;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 24px; overflow: hidden; color: #fff;
        box-shadow: 0 15px 30px -10px rgba(15, 23, 42, 0.3);
        border: 1px solid rgba(255,255,255,0.05);
    }
    .header-bg-shapes {
        position: absolute; top: 0; right: 0; bottom: 0; left: 0;
        background-image: radial-gradient(circle at 80% 20%, rgba(14, 165, 233, 0.15) 0%, transparent 40%),
                          radial-gradient(circle at 20% 80%, rgba(59, 130, 246, 0.1) 0%, transparent 40%);
        z-index: 1;
    }
    .header-bg-shapes::after {
        content: ''; position: absolute; inset: 0; opacity: 0.15;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M54.627 0l.83.83L1.244 55.443L.414 54.613L54.627 0zm-3.883 0l.83.83L0 51.573L0 50.743L50.744 0h3.883zM0 54.613l.83.83L4.713 60H3.883L0 56.117v-1.504zM0 46.857l.83.83L12.426 60H11.596L0 48.404v-1.547zM0 39.102l.83.83L20.14 60h-.83L0 40.648v-1.546zm0-7.746l.83.83L27.853 60h-.83L0 32.893v-1.546zm0-7.746l.83.83L35.566 60h-.83L0 25.138v-1.546zm0-7.746l.83.83L43.28 60h-.83L0 17.382v-1.546zM0 8.39l.83.83L50.993 60h-.83L0 9.728V8.39zM8.39 0l.83.83L60 50.78v.83L9.728 0H8.39zM16.136 0l.83.83L60 43.033v.83L17.474 0h-1.338zm7.746 0l.83.83L60 35.287v.83L25.22 0h-1.338zm7.746 0l.83.83L60 27.54v.83L32.966 0h-1.338zm7.746 0l.83.83L60 19.794v.83L40.712 0h-1.338zm7.746 0l.83.83L60 12.048v.83L48.458 0h-1.338zm7.746 0l.83.83L60 4.302v.83L56.204 0h-1.338z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
    }
    .header-content-inner { position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; }
    .header-badge {
        display: inline-block; padding: 4px 10px; background: rgba(14, 165, 233, 0.2);
        color: #0ea5e9; font-size: 0.6rem; font-weight: 800; border-radius: 20px;
        border: 1px solid rgba(14, 165, 233, 0.3); margin-bottom: 0.75rem; letter-spacing: 0.5px;
    }
    .header-text-premium h1 { font-size: 1.6rem; font-weight: 900; margin: 0; letter-spacing: -1px; line-height: 1.2; }
    .header-text-premium p { font-size: 0.85rem; opacity: 0.8; margin-top: 0.5rem; max-width: 500px; font-weight: 500; line-height: 1.5; }

    .header-actions-premium { display: flex; gap: 8px; }
    .e-btn {
        display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px;
        border-radius: 12px; font-weight: 700; font-size: 0.8rem; cursor: pointer;
        border: none; transition: 0.2s ease;
        font-family: inherit; text-decoration: none;
    }
    .e-btn:hover { transform: translateY(-5px) scale(1.02); }
    .e-btn i { width: 18px; height: 18px; }

    .e-btn-primary { background: #0ea5e9; color: #fff; box-shadow: 0 10px 25px rgba(14, 165, 233, 0.3); }
    .e-btn-dark { background: #0f172a; color: #fff; border: 1px solid rgba(255,255,255,0.1); }
    .e-btn-glass { background: rgba(255, 255, 255, 0.1); color: #fff; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); }
    .e-btn-emerald-light { background: #f0f9ff; color: #0284c7; }
    .e-btn-glass:hover { background: rgba(255, 255, 255, 0.2); }

    /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Filter & Search Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
    .filter-card { 
        padding: 1rem 1.5rem; border-radius: 16px; 
        background: #fff;
        border: 1px solid #f1f5f9; margin-bottom: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        display: flex; align-items: center;
    }
    .filter-group { display: flex; align-items: center; gap: 12px; }
    .filter-label { font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    .z-select-premium {
        background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;
        padding: 8px 16px; font-weight: 700; color: #1e293b; font-size: 0.85rem;
        outline: none; cursor: pointer; min-width: 150px; transition: 0.2s;
    }
    .z-select-premium:focus { border-color: #0ea5e9; background: #fff; box-shadow: 0 0 0 4px rgba(16,185,129,0.1); }
    .summary-pill { background: #f0fdf4; color: #16a34a; padding: 8px 16px; border-radius: 50px; font-size: 0.85rem; font-weight: 800; border: 1px solid rgba(22,163,74,0.1); }

    /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Table Modern Elite Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
    .table-card { 
        border-radius: 16px; overflow: hidden; 
        background: rgba(255, 255, 255, 0.8); 
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 10px 30px rgba(0,0,0,0.03); 
        padding: 0.5rem;
    }
    .z-table-wrap { border-radius: 12px; overflow: hidden; border: 1px solid #f1f5f9; background: #fff; margin: 0; }
    
    .bulk-action-bar {
        background: #fff1f2; padding: 1rem 1.5rem; display: none;
        justify-content: space-between; align-items: center; border-bottom: 1px solid #fecaca;
        animation: slideDown 0.3s ease-out;
    }
    @keyframes slideDown { from { transform: translateY(-100%); } to { transform: translateY(0); } }
    .selected-text { color: #e11d48; font-weight: 900; font-size: 0.95rem; }
    .bulk-btns { display: flex; gap: 10px; }
    .z-table-modern thead th {
        background: #f8fafc; padding: 0.75rem 1rem; text-align: left;
        font-size: 0.7rem; font-weight: 800; color: #475569;
        text-transform: uppercase; letter-spacing: 0.5px;
        border-bottom: 1px solid #e2e8f0;
    }
    .z-table-modern tbody td { padding: 0.5rem 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; font-size: 0.85rem; }
    .z-table-modern tr:last-child td { border-bottom: none; }
    .z-table-modern tr:hover td { background: #f8fafc; }
    
    .z-table-modern tbody tr { animation: fadeInUp 0.4s ease-out forwards; opacity: 0; }
    .z-table-modern tbody tr:nth-child(1) { animation-delay: 0.05s; }
    .z-table-modern tbody tr:nth-child(2) { animation-delay: 0.1s; }
    .z-table-modern tbody tr:nth-child(3) { animation-delay: 0.15s; }
    .z-table-modern tbody tr:nth-child(4) { animation-delay: 0.2s; }
    .z-table-modern tbody tr:nth-child(5) { animation-delay: 0.25s; }

    @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

    .avatar-group { display: flex; align-items: center; gap: 10px; }
    .avatar-box {
        width: 36px; height: 36px; border-radius: 10px; background: #f1f5f9;
        overflow: hidden; border: 1px solid #e2e8f0;
        cursor: pointer; flex-shrink: 0; transition: 0.2s;
    }
    .avatar-box:hover { transform: scale(1.05); }
    .avatar-img { width: 100%; height: 100%; object-fit: cover; }
    .avatar-actions { display: flex; flex-direction: row; gap: 4px; margin-left: 5px; }
    .av-btn {
        width: 24px; height: 24px; border-radius: 6px; border: 1px solid #e2e8f0;
        background: white; color: #64748b; cursor: pointer; transition: 0.2s;
        display: flex; align-items: center; justify-content: center;
    }
    .av-btn i { width: 12px; }
    .av-btn:hover { background: #f8fafc; color: #0ea5e9; border-color: #0ea5e9; }

    .name-box .main-name { font-weight: 700; color: #0f172a; font-size: 0.85rem; }
    .name-box .sub-info { font-size: 0.7rem; color: #64748b; font-family: 'JetBrains Mono', monospace; margin-top: 2px; }
    
    .contact-box { font-size: 0.8rem; color: #64748b; line-height: 1.6; }
    .contact-box i { width: 14px; vertical-align: middle; margin-right: 6px; opacity: 0.7; }

    .z-badge-emerald {
        background: #f0f9ff; color: #0284c7; padding: 4px 10px;
        border-radius: 10px; font-size: 0.75rem; font-weight: 800;
        border: 1px solid rgba(14, 165, 233, 0.2);
    }

    .z-action-btns {
        display: flex; gap: 6px; align-items: center; justify-content: center;
    }
    .action-btn {
        width: 30px; height: 30px; border-radius: 8px; display: flex;
        align-items: center; justify-content: center; border: none;
        cursor: pointer; transition: 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .action-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.06); }
    .action-btn i { width: 14px; height: 14px; }
    .card-btn { background: #f0fdf4; color: #16a34a; }
    .edit-btn { background: #eff6ff; color: #2563eb; }
    .delete-btn { background: #fff1f2; color: #e11d48; }

    .z-modal-tabs { 
        display: flex; gap: 8px; background: #f1f5f9; 
        padding: 8px; border-radius: 20px; margin: 1.5rem 2rem 0.5rem;
        border: 1px solid #e2e8f0;
    }
    .z-tab-btn {
        flex: 1; padding: 12px; font-size: 0.9rem; font-weight: 800; color: #64748b;
        cursor: pointer; position: relative; transition: 0.3s;
        text-align: center; border-radius: 14px;
        display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .z-tab-btn:hover { color: #0ea5e9; background: #fff; }
    .z-tab-btn.active { 
        color: #fff; background: #0ea5e9; 
        box-shadow: 0 10px 20px rgba(16,185,129,0.2);
    }
    .z-tab-content { display: none; padding: 2rem; max-height: calc(90vh - 250px); overflow-y: auto; scrollbar-width: thin; }
    .z-tab-content.active { display: block; animation: slideUpFade 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes slideUpFade { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

    .elite-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        padding: 2.5rem 3rem; color: #fff; border: none;
        position: relative; overflow: hidden;
    }
    .elite-header::before {
        content: ''; position: absolute; right: -30px; top: -30px;
        width: 150px; height: 150px; background: rgba(14, 165, 233, 0.2);
        border-radius: 50%; filter: blur(40px);
    }
    .elite-header h3 { font-size: 1.6rem; font-weight: 950; letter-spacing: -1px; margin: 0; display: flex; align-items: center; gap: 15px; }
    .elite-header p { margin: 8px 0 0; font-size: 0.95rem; opacity: 0.7; font-weight: 500; }
    .elite-header .close-btn { color: #fff !important; opacity: 0.8; }
    .elite-header .close-btn:hover { opacity: 1; transform: rotate(90deg); }

    /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Elite Form Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
    .z-form-grid-3, .z-form-grid-2, .z-form-grid-4 { 
        display: grid; gap: 1.5rem; 
    }
    .z-form-grid-3 { grid-template-columns: repeat(3, 1fr); }
    .z-form-grid-2 { grid-template-columns: repeat(2, 1fr); }
    .z-form-grid-4 { grid-template-columns: repeat(4, 1fr); }

    .z-tab-content label {
        display: block; font-size: 0.75rem; font-weight: 800;
        color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;
        margin-bottom: 10px; margin-top: 5px;
    }
    .z-input {
        width: 100%; padding: 14px 18px; border-radius: 16px;
        border: 2px solid #f1f5f9; background: #f8fafc;
        font-weight: 700; color: #1e293b; transition: 0.3s;
        font-size: 0.95rem !important;
    }
    .z-input:focus {
        border-color: #0ea5e9;
        box-shadow: 0 0 0 5px rgba(16,185,129,0.1);
        background: #fff; outline: none;
    }
    
    .form-section { background: #f8fafc; padding: 1.5rem; border-radius: 20px; border: 1px solid #e2e8f0; }
    .form-section h4 { 
        margin: 0 0 1.25rem 0; color: #0ea5e9; font-size: 0.95rem; font-weight: 900;
        display: flex; align-items: center; gap: 8px;
    }
    .form-section h4::before { content: ''; width: 4px; height: 16px; background: #0ea5e9; border-radius: 4px; }
    
    .upload-area {
        text-align: center; padding: 4rem 2rem; background: #f8fafc;
        border: 2px dashed #cbd5e1; border-radius: 24px; color: #64748b;
        transition: 0.3s; cursor: pointer;
    }
    .upload-area:hover { border-color: var(--z-primary); background: #f0fdf4; color: var(--z-primary); }
    .upload-area i { width: 48px; height: 48px; margin-bottom: 1rem; color: #94a3b8; transition: 0.3s; }
    .upload-area:hover i { transform: translateY(-5px); color: var(--z-primary); }

    .col-span-2 { grid-column: span 2; }
    .col-span-3 { grid-column: span 3; }
    .col-span-4 { grid-column: span 4; }

    /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Modal Override (Core diatur di layout.php) Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
    /* Hanya override visual spesifik halaman ini */
    .z-modal-content.large {
        border-radius: 32px !important;
        overflow: hidden !important;
    }
    @keyframes modalEntrance {
        from { opacity: 0; transform: scale(0.92) translateY(25px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .z-modal-content {
        animation: modalEntrance 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Export Modal Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
    .export-icon { width: 64px; height: 64px; background: #f0f9ff; color: #0284c7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; }
    .export-icon i { width: 32px; height: 32px; }
    .export-desc { color: #64748b; margin-bottom: 2rem; }
    .export-options { display: flex; flex-direction: column; gap: 12px; margin-bottom: 2rem; }
    .export-label {
        display: flex; align-items: center; gap: 15px; padding: 1.25rem;
        border: 2px solid #e2e8f0; border-radius: 16px; cursor: pointer; transition: 0.2s; text-align: left;
    }
    .export-label:hover { border-color: #0ea5e9; background: #fafffe; }
    .export-label input[type="radio"] { width: 20px; height: 20px; accent-color: #0ea5e9; }
    .option-title { display: block; font-weight: 800; color: #1e293b; font-size: 0.95rem; }
    .option-sub { font-size: 0.75rem; color: #94a3b8; }

    /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Card Print Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
    #modal-student-card { overflow-y: auto !important; display: none; align-items: flex-start !important; justify-content: center !important; padding: 2rem 1rem !important; }
    #modal-student-card .card-print-container { display: flex; flex-direction: column; align-items: center; gap: 1.5rem; margin: auto; }
    .card-side { width: 85.6mm; height: 54mm; background: white; border-radius: 4mm; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.3); border: 1px solid #e2e8f0; position: relative; flex-shrink: 0; }
    .card-header-bg { height: 16mm; background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%); display: flex; align-items: center; padding: 0 4mm; color: white; }
    .card-logo { width: 12mm; height: 12mm; object-fit: contain; margin-right: 3mm; }
    .card-type { font-weight: 800; font-size: 11px; letter-spacing: 1px; }
    .inst-name { font-weight: 700; font-size: 10px; }
    .inst-addr { font-size: 6px; opacity: 0.8; }
    
    .card-body { display: flex; padding: 3mm 4mm; gap: 4mm; }
    .photo-area { width: 22mm; height: 30mm; border: 1px solid #e2e8f0; border-radius: 1mm; overflow: hidden; padding: 1mm; background: white; }
    .photo-area img { width: 100%; height: 100%; object-fit: cover; }
    .data-area { flex: 1; text-align: left; }
    .card-table { width: 100%; font-size: 8px; line-height: 1.5; }
    .card-table td { vertical-align: top; }
    .card-table td.bold { font-weight: 800; color: #0f172a; }
    
    .seal-area { display: flex; align-items: flex-end; gap: 2mm; margin-top: 2mm; }
    .qr-ttd { width: 12mm; height: 12mm; }
    .seal-text { font-size: 5px; color: #64748b; line-height: 1.3; }
    .head-name { font-weight: 800; color: #0f172a; margin-top: 1.5mm; border-bottom: 0.5px solid #0f172a; display: inline-block; }
    
    .back { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 4mm; }
    .back-title { font-weight: 800; font-size: 10px; color: #0284c7; margin-bottom: 3mm; }
    .back-qr-box { background: white; padding: 2mm; border: 1px solid #e2e8f0; border-radius: 2mm; margin-bottom: 3mm; }
    .main-qr { width: 25mm; height: 25mm; }
    .back-credentials { width: 100%; border-top: 1px dashed #e2e8f0; padding-top: 2mm; display: flex; justify-content: space-between; }
    .cred { text-align: left; }
    .cred span { display: block; font-size: 6px; color: #94a3b8; font-weight: 700; }
    .cred b { font-size: 9px; color: #0f172a; letter-spacing: 1px; }

    /* Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ Modal View Photo Ã¢â€â‚¬Ã¢â€â‚¬Ã¢â€â‚¬ */
    .z-modal-photo {
        display: none; position: fixed; inset: 0; z-index: 110000;
        background: rgba(0,0,0,0.9); backdrop-filter: blur(20px);
        align-items: center; justify-content: center; padding: 20px;
    }
    .photo-wrapper { position: relative; max-width: 90vw; max-height: 90vh; text-align: center; }
    .photo-wrapper img { max-width: 100%; max-height: 80vh; border-radius: 20px; box-shadow: 0 25px 50px rgba(0,0,0,0.5); border: 3px solid rgba(255,255,255,0.1); }
    #view-photo-name { color: #fff; margin-top: 20px; font-weight: 800; font-size: 1.2rem; }

    @media print {
        body * { visibility: hidden; }
        #printable-student-card, #printable-student-card * { visibility: visible; }
        #printable-student-card { position: fixed; left: 0; top: 0; width: 210mm; height: 297mm; display: block !important; padding: 20mm !important; }
        .card-side { margin-bottom: 10mm; box-shadow: none !important; border: 1px solid #ccc !important; }
        .print-actions { display: none !important; }
    }
</style>

<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/_siakad_header.php'; ?>

        <div class="elite-page-header">
            <!-- DataTables CSS & jQuery (since DataTables requires jQuery) -->
            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
            <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
            <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs/qrcode.min.js"></script>
            
            <div class="header-bg-shapes"></div>
            <div class="header-content-inner">
                <div class="header-text-premium">
                    <div class="header-badge">SIAKAD</div>
                    <h1><i data-lucide="users"></i> Data Siswa</h1>
                    <p>Manajemen biodata, identitas, dan cetak kartu pelajar.</p>
                </div>
                <div class="header-actions-premium">
                    <button class="e-btn e-btn-glass" onclick="document.getElementById('modal-export-siswa').style.display='flex'">
                        <i data-lucide="printer"></i> Export / PDF
                    </button>
                    <a href="<?php echo base_url('siakad/siswa/cetak-kartu' . ($selected_kelas ? '?kelas_id=' . $selected_kelas : '')); ?>" target="_blank" class="e-btn e-btn-dark">
                        <i data-lucide="id-card"></i> Cetak Massal
                    </a>
                    <button class="e-btn e-btn-emerald-light" onclick="document.getElementById('modal-import-siswa').style.display='flex'">
                        <i data-lucide="copy-plus"></i> Import Data
                    </button>
                    <button class="e-btn" style="background: #fef08a; color: #854d0e; border: 1px solid #fde047;" onclick="document.getElementById('modal-update-bulk').style.display='flex'">
                        <i data-lucide="refresh-ccw"></i> Update Massal
                    </button>
                    <a href="<?php echo base_url('siakad/siswa/tambah'); ?>" class="e-btn e-btn-primary">
                        <i data-lucide="user-plus"></i> Tambah Siswa
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter & Summary -->
        <div class="z-card filter-card">
            <form action="" method="GET" class="filter-form">
                <div class="filter-group">
                    <span class="filter-label">Filter Kelas:</span>
                    <select name="kelas_id" onchange="this.form.submit()" class="z-select-premium">
                        <option value="">-- Pilih Kelas --</option>
                        <option value="all" <?php echo $selected_kelas === 'all' ? 'selected' : ''; ?>>Tampilkan Semua</option>
                        <?php foreach($kelas as $k): ?>
                            <option value="<?php echo $k['id']; ?>" <?php echo $selected_kelas == $k['id'] ? 'selected' : ''; ?>>
                                KELAS <?php echo $k['nama_kelas']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
            <div id="dt-custom-controls" class="dt-controls-wrap"></div>
            <div class="summary-pill" style="margin-left: auto;">
                <i data-lucide="users" style="width: 16px; margin-right: 5px; vertical-align: middle;"></i>
                <b><?php echo count($siswas); ?></b> Siswa Aktif
            </div>
        </div>

        <!-- Rekapitulasi Siswa -->
        <div style="display: flex; gap: 15px; margin-bottom: 1.5rem; overflow-x: auto; padding-bottom: 5px; scrollbar-width: thin;">
            <?php
            $rekap_query = $db->query("
                SELECT k.nama_kelas, 
                       SUM(CASE WHEN s.jenis_kelamin = 'L' THEN 1 ELSE 0 END) as jml_l,
                       SUM(CASE WHEN s.jenis_kelamin = 'P' THEN 1 ELSE 0 END) as jml_p,
                       COUNT(s.id) as total
                FROM kelas k
                LEFT JOIN (
                    SELECT s.id, s.jenis_kelamin, COALESCE(rks.kelas_id, s.kelas_id) as actual_kelas_id
                    FROM siswa s 
                    LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                        AND rks.tahun_ajaran_id IN (SELECT id FROM tahun_ajaran WHERE name = '$active_year_name')
                    WHERE s.status IN ('Aktif', 'Alumni') AND (rks.id IS NOT NULL OR s.tahun_ajaran = '$active_year_name')
                ) s ON s.actual_kelas_id = k.id
                GROUP BY k.id, k.nama_kelas
                ORDER BY k.id ASC
            ")->fetchAll(\PDO::FETCH_ASSOC);

            $total_l = 0; $total_p = 0; $total_all = 0;
            foreach($rekap_query as $r):
                $total_l += $r['jml_l'];
                $total_p += $r['jml_p'];
                $total_all += $r['total'];
            ?>
            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 16px; min-width: 140px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); flex-shrink: 0;">
                <div style="font-weight: 800; color: #1e293b; font-size: 0.9rem; margin-bottom: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 4px;">Kelas <?php echo htmlspecialchars($r['nama_kelas']); ?></div>
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: #64748b; margin-bottom: 3px;">
                    <span>Laki-laki:</span> <span style="font-weight: 700; color: #3b82f6;"><?php echo $r['jml_l'] ?: 0; ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: #64748b; margin-bottom: 3px;">
                    <span>Perempuan:</span> <span style="font-weight: 700; color: #ec4899;"><?php echo $r['jml_p'] ?: 0; ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 800; color: #0f172a; margin-top: 5px; padding-top: 5px; border-top: 1px solid #f1f5f9;">
                    <span>Total:</span> <span><?php echo $r['total']; ?></span>
                </div>
            </div>
            <?php endforeach; ?>
            
            <!-- Total Keseluruhan -->
            <div style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: white; border-radius: 12px; padding: 12px 16px; min-width: 140px; box-shadow: 0 4px 10px rgba(16,185,129,0.2); flex-shrink: 0;">
                <div style="font-weight: 800; font-size: 0.9rem; margin-bottom: 8px; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 4px;">TOTAL SEMUA</div>
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 3px; opacity: 0.9;">
                    <span>Laki-laki:</span> <span style="font-weight: 700;"><?php echo $total_l; ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 3px; opacity: 0.9;">
                    <span>Perempuan:</span> <span style="font-weight: 700;"><?php echo $total_p; ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 800; margin-top: 5px; padding-top: 5px; border-top: 1px solid rgba(255,255,255,0.2);">
                    <span>Total:</span> <span><?php echo $total_all; ?></span>
                </div>
            </div>
        </div>

        <?php if($selected_kelas === ''): ?>
            <div class="z-card table-card" style="padding: 4rem; text-align: center; color: #64748b;">
                <i data-lucide="filter" style="width: 48px; height: 48px; opacity: 0.2; margin-bottom: 1rem;"></i>
                <h3 style="margin: 0 0 10px; color: #1e293b;">Pilih Kelas Terlebih Dahulu</h3>
                <p style="margin: 0; font-size: 0.95rem;">Silakan pilih kelas pada filter di atas untuk menampilkan data siswa.</p>
            </div>
        <?php else: ?>
        <form id="form-bulk-siswa" action="<?php echo base_url('siakad/siswa/delete-bulk'); ?>" method="POST">
            <div class="z-card table-card">
                <div class="bulk-action-bar" id="bulk-action-bar-siswa">
                    <span class="selected-text"><span id="selected-count-siswa">0</span> siswa terpilih</span>
                    <div class="bulk-btns">
                        <button type="button" onclick="confirmBulkActionSiswa('print')" class="z-btn z-btn-primary z-btn-sm">
                            <i data-lucide="printer"></i> Cetak Kartu
                        </button>
                        <button type="button" onclick="confirmBulkActionSiswa('reset-password')" class="z-btn z-btn-dark z-btn-sm" style="background: #0f172a; color: white;">
                            <i data-lucide="refresh-cw"></i> Reset Akun
                        </button>
                        <button type="button" onclick="confirmBulkActionSiswa('delete')" class="z-btn z-btn-danger z-btn-sm">
                            <i data-lucide="trash-2"></i> Hapus
                        </button>
                    </div>
                </div>

                <div class="z-table-wrap">
                    <table id="table-data-siswa" class="z-table-modern">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 40px;"><input type="checkbox" id="check-all-siswa" class="z-check"></th>
                                <th style="width: 120px;">Foto</th>
                                <th style="width: 100px;">NIS</th>
                                <th style="width: 100px;">NISN</th>
                                <th>Nama Lengkap</th>
                                <th class="text-center">Kelas</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($siswas as $s): ?>
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="ids[]" value="<?php echo $s['id']; ?>" class="check-siswa z-check" onchange="updateBulkSiswa()">
                                </td>
                                <td>
                                    <div class="avatar-group">
                                        <div class="avatar-box" onclick="viewPhoto('<?php echo $s['foto']; ?>', '<?php echo addslashes($s['nama']); ?>')">
                                            <?php if($s['foto'] && file_exists(__DIR__ . "/../../../public/uploads/siswa/" . $s['foto'])): ?>
                                                <img loading="lazy" id="img-siswa-<?php echo $s['id']; ?>" src="<?php echo base_url('thumb.php?src=uploads/siswa/' . $s['foto'] . '&w=100&h=100'); ?>" class="avatar-img">
                                            <?php else: ?>
                                                <img loading="lazy" id="img-siswa-<?php echo $s['id']; ?>" src="https://ui-avatars.com/api/?name=<?php echo urlencode($s['nama']); ?>&background=065f46&color=fff&bold=true&size=128" class="avatar-img">
                                            <?php endif; ?>
                                        </div>
                                        <div class="avatar-actions">
                                            <button type="button" onclick="viewPhoto(document.getElementById('img-siswa-<?php echo $s['id']; ?>').src, '<?php echo addslashes($s['nama']); ?>', true)" class="av-btn view-btn"><i data-lucide="eye"></i></button>
                                            <button type="button" onclick="quickUpload('<?php echo $s['id']; ?>', '<?php echo addslashes($s['nama']); ?>')" class="av-btn upload-btn"><i data-lucide="upload"></i></button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; font-weight: 700; color: #475569;">
                                        <?php echo $s['nis']; ?>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; font-weight: 700; color: #94a3b8;">
                                        <?php echo $s['nisn'] ?: '-'; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="name-box">
                                        <div class="main-name"><?php echo $s['nama']; ?></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="z-badge-emerald"><?php echo $s['nama_kelas']; ?></span>
                                </td>
                                <td>
                                    <div class="z-action-btns">
                                        <button type="button" class="action-btn card-btn" onclick='openStudentCardNewTab(<?php echo $s['id']; ?>)' title="Kartu Pelajar">
                                            <i data-lucide="contact"></i>
                                        </button>
                                        <button type="button" class="action-btn edit-btn" onclick='editSiswa(<?php echo htmlspecialchars(json_encode($s), ENT_QUOTES, "UTF-8"); ?>)' title="Edit">
                                            <i data-lucide="edit-3"></i>
                                        </button>
                                        <a href="<?php echo base_url('siakad/siswa/delete/' . $s['id']); ?>" onclick="return confirm('Hapus siswa ini?')" class="action-btn delete-btn">
                                            <i data-lucide="trash-2"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>

<input type="file" id="quick-auto-input" accept="image/*" style="display: none;" onchange="handleAutoUpload(this)">
<input type="hidden" id="quick-auto-siswa-id">

<!-- Modal Export -->
<div id="modal-export-siswa" class="z-modal">
    <div class="z-modal-content small">
        <div class="z-modal-header">
            <h3>Export Data Siswa</h3>
            <button onclick="document.getElementById('modal-export-siswa').style.display='none'" class="close-btn"><i data-lucide="x"></i></button>
        </div>
        <div class="z-modal-body text-center">
            <div class="export-icon"><i data-lucide="printer"></i></div>
            <p class="export-desc">Pilih jenis data yang ingin diexport / dicetak.</p>
            <form action="<?php echo base_url('siakad/siswa/export'); ?>" method="GET" target="_blank">
                <input type="hidden" name="kelas_id" value="<?php echo $selected_kelas; ?>">
                <div class="export-options">
                    <label class="export-label">
                        <input type="radio" name="mode" value="public" checked>
                        <div class="option-content">
                            <span class="option-title">Data Publik</span>
                            <span class="option-sub">Identitas dasar (Puskesmas/Dinas)</span>
                        </div>
                    </label>
                    <label class="export-label">
                        <input type="radio" name="mode" value="full">
                        <div class="option-content">
                            <span class="option-title">Data Lengkap</span>
                            <span class="option-sub">Biodata, Alamat & Data Orang Tua</span>
                        </div>
                    </label>
                    <label class="export-label" style="border-color: #10b981;">
                        <input type="radio" name="mode" value="excel" style="accent-color: #10b981;">
                        <div class="option-content">
                            <span class="option-title" style="color: #10b981;">Download Format Update (Excel)</span>
                            <span class="option-sub">Berisi QR Token unik, bisa diedit di Excel lalu di-paste ke Update Massal</span>
                        </div>
                    </label>
                </div>
                <div class="z-modal-footer">
                    <button type="button" class="z-btn z-btn-ghost" onclick="document.getElementById('modal-export-siswa').style.display='none'">Batal</button>
                    <button type="submit" class="z-btn z-btn-primary">PROSES EXPORT</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Add/Edit Siswa -->
<div id="modal-add-siswa" class="z-modal">
    <div class="z-modal-content large">
        <div class="z-modal-header elite-header">
            <div class="header-content">
                <h3><i data-lucide="file-text"></i> Form Biodata Siswa</h3>
                <p>Lengkapi profil peserta didik untuk database institusi.</p>
            </div>
            <button onclick="document.getElementById('modal-add-siswa').style.display='none'" class="close-btn white"><i data-lucide="x"></i></button>
        </div>
        <div class="z-modal-tabs">
            <div class="z-tab-btn active" onclick="switchTab('tab-identitas', this)"><i data-lucide="user"></i> Identitas</div>
            <div class="z-tab-btn" onclick="switchTab('tab-legal', this)"><i data-lucide="graduation-cap"></i> Pendidikan</div>
            <div class="z-tab-btn" onclick="switchTab('tab-keluarga', this)"><i data-lucide="users"></i> Orang Tua</div>
            <div class="z-tab-btn" onclick="switchTab('tab-alamat', this)"><i data-lucide="map-pin"></i> Alamat</div>
            <div class="z-tab-btn" onclick="switchTab('tab-foto', this)"><i data-lucide="camera"></i> Foto</div>
        </div>
        <div class="z-modal-body has-tabs">
            <form id="siswa-form" action="<?php echo base_url('siakad/siswa/add'); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id">
                
                <div id="tab-identitas" class="z-tab-content active">
                    <div class="z-form-grid-3">
                        <div class="col-span-2">
                            <label>Nama Lengkap (Sesuai Akta/KK)</label>
                            <input type="text" name="nama" class="z-input" required>
                        </div>
                        <div>
                            <label>NIK (16 Digit)</label>
                            <input type="text" name="nik" class="z-input" maxlength="16">
                        </div>
                        <div>
                            <label>No. Kartu Keluarga (KK)</label>
                            <input type="text" name="no_kk" class="z-input" maxlength="16">
                        </div>
                        <div>
                            <label>NIS (Sekolah)</label>
                            <input type="text" name="nis" class="z-input" required>
                        </div>
                        <div>
                            <label>NISN (Nasional)</label>
                            <input type="text" name="nisn" class="z-input">
                        </div>
                        <div>
                            <label>Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="z-input">
                        </div>
                        <div>
                            <label>Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="z-input">
                        </div>
                        <div>
                            <label>Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="z-input">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label>Agama</label>
                            <select name="agama" class="z-input">
                                <option value="Islam">Islam</option>
                                <option value="Kristen">Kristen</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Budha">Budha</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div id="tab-legal" class="z-tab-content">
                    <div class="z-form-grid-2">
                        <div>
                            <label>Pendidikan Terakhir</label>
                            <input type="text" name="pendidikan_terakhir" class="z-input" placeholder="SD/MI">
                        </div>
                        <div>
                            <label>Kelas Saat Ini</label>
                            <select name="kelas_id" class="z-input">
                                <option value="">Pilih Kelas</option>
                                <?php foreach($kelas as $k): ?>
                                    <option value="<?php echo $k['id']; ?>"><?php echo $k['nama_kelas']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label>Sekolah Asal</label>
                            <input type="text" name="sekolah_asal" class="z-input">
                        </div>
                        <div>
                            <label>No. Ijazah</label>
                            <input type="text" name="no_ijazah" class="z-input">
                        </div>
                    </div>
                </div>

                <div id="tab-keluarga" class="z-tab-content">
                    <div class="z-form-grid-2">
                        <div class="form-section">
                            <h4>Data Ayah</h4>
                            <label>Nama Ayah</label>
                            <input type="text" name="nama_ayah" class="z-input">
                            <label>Pekerjaan Ayah</label>
                            <input type="text" name="pekerjaan_ayah" class="z-input">
                        </div>
                        <div class="form-section">
                            <h4>Data Ibu</h4>
                            <label>Nama Ibu</label>
                            <input type="text" name="nama_ibu" class="z-input">
                            <label>Pekerjaan Ibu</label>
                            <input type="text" name="pekerjaan_ibu" class="z-input">
                        </div>
                        <div class="col-span-2">
                            <label>Nama Wali (Jika ada)</label>
                            <input type="text" name="nama_wali" class="z-input">
                        </div>
                    </div>
                </div>

                <div id="tab-alamat" class="z-tab-content">
                    <div class="z-form-grid-4">
                        <div class="col-span-4">
                            <label>Alamat Jalan / Dusun</label>
                            <input type="text" name="alamat" class="z-input">
                        </div>
                        <div><label>RT</label><input type="text" name="rt" class="z-input"></div>
                        <div><label>RW</label><input type="text" name="rw" class="z-input"></div>
                        <div class="col-span-2"><label>Kelurahan</label><input type="text" name="desa" class="z-input"></div>
                        <div class="col-span-2"><label>Kecamatan</label><input type="text" name="kecamatan" class="z-input"></div>
                        <div class="col-span-2"><label>Kabupaten/Kota</label><input type="text" name="kota" class="z-input"></div>
                        <div class="col-span-4"><label>No. HP Orang Tua</label><input type="text" name="no_hp_ortu" class="z-input"></div>
                    </div>
                </div>

                <div id="tab-foto" class="z-tab-content">
                    <div class="upload-area">
                        <i data-lucide="image"></i>
                        <h3>Pilih Foto Siswa</h3>
                        <input type="file" name="foto" accept="image/*">
                    </div>
                </div>

                <div class="z-modal-footer" style="padding: 2rem; background: #f8fafc; border-top: 1px solid #e2e8f0; gap: 15px;">
                    <button type="button" class="e-btn e-btn-glass" style="color: #64748b; border: 1px solid #cbd5e1;" onclick="document.getElementById('modal-add-siswa').style.display='none'">Batal</button>
                    <button type="submit" class="e-btn e-btn-primary" style="padding: 14px 40px; font-size: 1rem;">
                        <i data-lucide="save"></i> SIMPAN BIODATA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import -->
<div id="modal-import-siswa" class="z-modal">
    <div class="z-modal-content large">
        <div class="z-modal-header bg-emerald">
            <h3>Import Siswa (Smart-Paste)</h3>
            <button onclick="document.getElementById('modal-import-siswa').style.display='none'" class="close-btn white"><i data-lucide="x"></i></button>
        </div>
        <div class="z-modal-body">
            <form action="<?php echo base_url('siakad/siswa/import'); ?>" method="POST">
                <div class="form-group">
                    <label>Pilih Kelas Tujuan (Opsional)</label>
                    <select name="kelas_id" class="z-input">
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach($kelas as $k): ?>
                            <option value="<?php echo $k['id']; ?>">Kelas <?php echo $k['nama_kelas']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <textarea name="paste_data" placeholder="Paste data dari Excel di sini..." class="z-textarea-import"></textarea>
                <div class="import-guide">
                    <h4>Urutan Kolom:</h4>
                    <p>NIS / Username | Nama Lengkap | JK (L/P) | Tempat Lahir | Tgl Lahir (YYYY-MM-DD) | Kelas</p>
                </div>
                <div class="z-modal-footer">
                    <button type="button" class="z-btn z-btn-ghost" onclick="document.getElementById('modal-import-siswa').style.display='none'">Batal</button>
                    <button type="submit" class="z-btn z-btn-primary">PROSES IMPORT</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Update Bulk -->
<div id="modal-update-bulk" class="z-modal">
    <div class="z-modal-content large" style="border-top: 5px solid #f59e0b;">
        <div class="z-modal-header" style="background: #fffbeb; color: #b45309; border-bottom: 1px solid #fef3c7;">
            <h3 style="display: flex; align-items: center; gap: 10px;"><i data-lucide="refresh-ccw"></i> Update Massal Siswa</h3>
            <button onclick="document.getElementById('modal-update-bulk').style.display='none'" class="close-btn"><i data-lucide="x"></i></button>
        </div>
        <div class="z-modal-body">
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 15px; border-radius: 12px; margin-bottom: 15px; font-size: 0.85rem; font-weight: 500;">
                <i data-lucide="alert-triangle" style="width: 20px; float: left; margin-right: 10px;"></i>
                <strong>PERHATIAN:</strong> Pastikan Anda menggunakan file hasil "Download Format Update (Excel)"! Kolom pertama (QR Token) <b>TIDAK BOLEH</b> diubah karena digunakan sebagai kunci sistem. Data siswa lama akan <b>ditimpa</b> dengan data baru ini berdasarkan QR Token.
            </div>
            <form action="<?php echo base_url('siakad/siswa/update-bulk'); ?>" method="POST">
                <textarea name="paste_data" placeholder="Paste (Copy dari Excel lalu tempel di sini)..." class="z-textarea-import" style="border: 2px dashed #fcd34d; min-height: 200px;" required></textarea>
                <div class="import-guide" style="background: #fffbeb; border-color: #fde68a; color: #b45309;">
                    <h4>Urutan Kolom (Harus Sama Persis dengan Excel):</h4>
                    <p style="font-family: monospace; font-size: 0.8rem; font-weight: bold;">[QR Token] | [NIS] | [NISN] | [NIK] | [Nama Lengkap] | [JK (L/P)] | [Tempat Lahir] | [Tgl Lahir (YYYY-MM-DD)] | [Kelas] | [Alamat]</p>
                </div>
                <div class="z-modal-footer">
                    <button type="button" class="z-btn z-btn-ghost" onclick="document.getElementById('modal-update-bulk').style.display='none'">Batal</button>
                    <button type="submit" class="z-btn" style="background: #f59e0b; color: white; border: none;"><i data-lucide="refresh-cw" style="width: 18px; margin-right: 5px;"></i> PROSES UPDATE</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Student Card -->
<div id="modal-student-card" class="z-modal">
    <div id="printable-student-card" class="card-print-container">
        <!-- Front Side -->
        <div class="card-side front">
            <div class="card-header-bg">
                <?php if($inst && $inst['logo']): ?>
                    <img src="<?php echo base_url('uploads/logo/' . $inst['logo']); ?>" class="card-logo">
                <?php endif; ?>
                <div class="card-header-text">
                    <div class="card-type">KARTU PELAJAR</div>
                    <div class="inst-name"><?php echo $inst ? $inst['nama'] : 'Aula System ACADEMY'; ?></div>
                    <div class="inst-addr"><?php echo $inst ? $inst['alamat'] : ''; ?></div>
                </div>
            </div>
            <div class="card-body">
                <div class="photo-area">
                    <img id="sc-photo" src="">
                </div>
                <div class="data-area">
                    <table class="card-table">
                        <tr><td>Nama</td><td>:</td><td style="max-width: 38mm; overflow: hidden; white-space: nowrap;"><div id="sc-name" class="bold auto-fit-modal" style="font-size: 8px;"></div></td></tr>
                        <tr><td>NIS / NISN</td><td>:</td><td id="sc-nisn"></td></tr>
                        <tr><td>TTL</td><td>:</td><td id="sc-ttl"></td></tr>
                        <tr><td style="vertical-align: top;">Alamat</td><td style="vertical-align: top;">:</td><td id="sc-alamat" style="white-space: normal; line-height: 1.1;"></td></tr>
                    </table>
                    <div class="seal-area">
                        <img id="sc-ttd-qr" class="qr-ttd">
                        <div class="seal-text">
                            <div><?php echo $inst ? $inst['kota'] : 'Jakarta'; ?>, <?php echo date('d M Y'); ?></div>
                            <div class="head-title">Kepala Sekolah</div>
                            <div class="head-name"><?php echo $inst ? $inst['nama_kepala'] : ''; ?></div>
                            <div class="head-nip">NIP. <?php echo $inst ? $inst['nip_kepala'] : ''; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Back Side -->
        <div class="card-side back">
            <div class="back-title">KARTU PELAJAR ELEKTRONIK</div>
            <div class="back-qr-box">
                <img id="sc-qr" class="main-qr">
            </div>

        </div>
        <div class="print-actions">
            <button class="z-btn z-btn-black" onclick="window.print()"><i data-lucide="printer"></i> CETAK SEKARANG</button>
            <button class="z-btn z-btn-ghost" onclick="document.getElementById('modal-student-card').style.display='none'">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal View Photo -->
<div id="modal-view-photo" class="z-modal-photo" onclick="this.style.display='none'">
    <div class="photo-wrapper">
        <img id="view-photo-img" src="">
        <div id="view-photo-name"></div>
    </div>
</div>

<script>
    function switchTab(tabId, btn) {
        document.querySelectorAll('.z-tab-content').forEach(c => c.classList.remove('active'));
        document.querySelectorAll('.z-tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById(tabId).classList.add('active');
        btn.classList.add('active');
    }

    function updateBulkSiswa() {
        let count = document.querySelectorAll('.check-siswa:checked').length;
        document.getElementById('selected-count-siswa').innerText = count;
        document.getElementById('bulk-action-bar-siswa').style.display = count > 0 ? 'flex' : 'none';
    }

    document.getElementById('check-all-siswa').addEventListener('change', function() {
        document.querySelectorAll('.check-siswa').forEach(cb => cb.checked = this.checked);
        updateBulkSiswa();
    });

    function confirmBulkActionSiswa(action) {
        let form = document.getElementById('form-bulk-siswa');
        if(action === 'delete') {
            if(confirm('Hapus siswa terpilih secara permanen?')) {
                form.action = base_url('siakad/siswa/delete-bulk');
                form.submit();
            }
        } else if(action === 'print') {
            form.action = base_url('siakad/siswa/cetak-kartu-massal');
            form.target = '_blank';
            form.submit();
        } else if(action === 'reset-password') {
            if(confirm('Reset username dan password untuk siswa terpilih ke random 8 karakter?')) {
                form.action = base_url('siakad/siswa/reset-password-bulk');
                form.target = '';
                form.submit();
            }
        }
    }

    function viewPhoto(src, name, fromAction = false) {
        document.getElementById('view-photo-img').src = src;
        document.getElementById('view-photo-name').innerText = name;
        document.getElementById('modal-view-photo').style.display = 'flex';
    }

    function quickUpload(id, name) {
        document.getElementById('quick-auto-siswa-id').value = id;
        document.getElementById('quick-auto-input').click();
    }

    function handleAutoUpload(input) {
        if (input.files && input.files[0]) {
            let id = document.getElementById('quick-auto-siswa-id').value;
            let formData = new FormData();
            formData.append('foto', input.files[0]);
            formData.append('id', id);

            fetch(base_url('siakad/siswa/update-foto'), {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update image source directly to avoid reload
                    let img = document.getElementById('img-siswa-' + id);
                    if (img) {
                        // Use a query parameter to bust browser cache
                        img.src = base_url('uploads/siswa/' + data.foto + '?t=' + new Date().getTime());
                    }
                    alert('Foto berhasil diupdate!');
                } else {
                    alert('Gagal upload foto: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat upload.');
            });
        }
    }

    function editSiswa(data) {
        const form = document.getElementById('siswa-form');
        form.action = base_url('siakad/siswa/update');
        
        // Map data to inputs
        for (let key in data) {
            let input = form.querySelector(`[name="${key}"]`);
            if (input && input.type !== 'file') {
                input.value = data[key] || '';
            }
        }
        
        document.getElementById('modal-add-siswa').style.display = 'flex';
        switchTab('tab-identitas', document.querySelector('.z-tab-btn'));
        lucide.createIcons();
    }

    function showStudentCard(s) {
        document.getElementById('sc-photo').src = s.foto ? '/uploads/siswa/' + s.foto : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(s.nama) + '&background=065f46&color=fff';
        
        let scName = document.getElementById('sc-name');
        scName.innerText = s.nama;
        scName.style.fontSize = '8px'; // reset
        
        document.getElementById('sc-nisn').innerText = s.nis + (s.nisn ? ' / ' + s.nisn : '');
        
        let ttl = '-';
        if (s.tempat_lahir || s.tanggal_lahir) {
            let tglFormat = '';
            if (s.tanggal_lahir) {
                let parts = s.tanggal_lahir.split('-');
                if(parts.length === 3) {
                    let months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    tglFormat = parts[2] + ' ' + months[parseInt(parts[1]) - 1] + ' ' + parts[0];
                } else {
                    tglFormat = s.tanggal_lahir;
                }
            }
            ttl = (s.tempat_lahir ? s.tempat_lahir + ', ' : '') + tglFormat;
        }
        document.getElementById('sc-ttl').innerText = ttl;
        
        let alamatText = '-';
        if (s.alamat) {
            alamatText = s.alamat;
            if (s.desa) alamatText += ', ' + s.desa;
            if (s.kecamatan) alamatText += ', ' + s.kecamatan;
        }
        document.getElementById('sc-alamat').innerText = alamatText;
        

        // Generate QR Belakang
        let qrBox = document.getElementById('sc-qr');
        qrBox.innerHTML = '';
        let txtSiswa = s.qr_token;
        new QRCode(qrBox, { text: txtSiswa, width: 128, height: 128, colorDark: "#0f172a", colorLight: "#ffffff", correctLevel: QRCode.CorrectLevel.M });
        
        // Generate QR TTD
        let qrTtdBox = document.getElementById('sc-ttd-qr');
        qrTtdBox.removeAttribute('src');
        let ttdWrapper = document.createElement('div');
        let tglCetak = new Date().toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'});
        new QRCode(ttdWrapper, { text: "Telah disahkan pada " + tglCetak, width: 128, height: 128, colorDark: "#475569", colorLight: "#ffffff", correctLevel: QRCode.CorrectLevel.L });
        setTimeout(() => { 
            let canvas = ttdWrapper.querySelector('canvas');
            if (canvas) qrTtdBox.src = canvas.toDataURL(); 
        }, 50);

        document.getElementById('modal-student-card').style.display = 'flex';
        
        // Auto-fit name
        setTimeout(() => {
            let parent = scName.parentElement;
            let fontSize = 8;
            while(scName.scrollWidth > parent.clientWidth && fontSize > 4.5) {
                fontSize -= 0.2;
                scName.style.fontSize = fontSize + 'px';
            }
        }, 100);
    }

    function openStudentCardNewTab(id) {
        let f = document.createElement('form');
        f.method = 'POST';
        f.action = base_url('siakad/siswa/cetak-kartu-massal');
        f.target = '_blank';
        let i = document.createElement('input');
        i.type = 'hidden';
        i.name = 'ids[]';
        i.value = id;
        f.appendChild(i);
        document.body.appendChild(f);
        f.submit();
        f.remove();
    }

    // Initialize DataTable
    $(document).ready(function() {
        if($('#table-data-siswa').length) {
            var table = $('#table-data-siswa').DataTable({
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                "language": {
                    "search": "Cari Cepat:",
                    "lengthMenu": "Tampilkan _MENU_ siswa",
                    "info": "Menampilkan _START_ s/d _END_ dari _TOTAL_ siswa",
                    "paginate": { "next": "Next", "previous": "Prev" }
                },
                "columnDefs": [ { "orderable": false, "targets": [0, 1, 6] } ],
                "drawCallback": function() { lucide.createIcons(); }
            });

            // Pindahkan kontrol DataTables ke atas sebelah filter kelas
            $('.dataTables_length, .dataTables_filter').appendTo('#dt-custom-controls');
        }
    });

    // Initialize Lucide
    lucide.createIcons();
</script>


