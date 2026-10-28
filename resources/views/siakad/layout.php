<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'SIAKAD - Aula System'; ?></title>
    <link rel="icon" type="image/png" href="<?php echo \App\Core\Branding::getInstitusiFavicon(); ?>?v=<?php echo time(); ?>">
    <!-- Zamrud Design System -->
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/zamrud.css?v=' . time()); ?>">
    <!-- Lucide Icons -->
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="<?php echo base_url('public/assets/js/sweetalert2.all.min.js'); ?>"></script>
    <script>
        if (typeof Swal === 'undefined') {
            document.write('<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"><\/script>');
        }
        window.baseUrl = '<?php echo base_url(); ?>'.replace(/\/+$/, '');
        function base_url(path = '') {
            path = String(path).replace(/^\/+/, '');
            return window.baseUrl ? window.baseUrl + '/' + path : '/' + path;
        }
    </script>
    <style>
        /* ── Legacy CSS variable bridge ── */
        :root {
            --primary-blue: #0284c7;
            --emerald: #0ea5e9;
            --text-muted: #64748b;
            --text-main: #0f172a;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            background-attachment: fixed;
            font-size: 13px !important;
            position: relative;
            min-height: 100vh;
        }
        
        /* Ambient Light Background Animation (Blue Theme) */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.08) 0%, transparent 40%),
                        radial-gradient(circle at 80% 80%, rgba(59, 130, 246, 0.06) 0%, transparent 40%);
            z-index: -1;
            animation: pulseAmbient 20s infinite alternate cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
        }
        @keyframes pulseAmbient {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(4%, 4%) scale(1.08); }
        }

        h1, h2, h3, h4, h5, h6 { font-weight: 800 !important; }
        h1 { font-size: 1.25rem !important; margin-bottom: 5px !important; }
        h2 { font-size: 1.15rem !important; }
        h3 { font-size: 1.05rem !important; }
        
        p, span, td, th, a, input, select, textarea, button, label {
            font-size: 12.8px !important;
        }

        /* ── Elite Page Header (Mismifhda Concept with Masmifhda Blue) ── */
        .elite-page-header {
            position: relative; padding: 2rem; margin-bottom: 1.5rem;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.92) 0%, rgba(3, 105, 161, 0.9) 100%);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-radius: 24px; overflow: hidden; color: #fff;
            box-shadow: 0 15px 30px -10px rgba(2, 132, 199, 0.35), 0 0 0 1px rgba(255,255,255,0.15) inset;
            border: 1px solid rgba(255,255,255,0.15);
        }
        .header-bg-shapes {
            position: absolute; top: 0; right: 0; bottom: 0; left: 0;
            background-image: radial-gradient(circle at 80% 20%, rgba(14, 165, 233, 0.2) 0%, transparent 45%),
                              radial-gradient(circle at 20% 80%, rgba(59, 130, 246, 0.15) 0%, transparent 45%);
            z-index: 1; pointer-events: none;
        }
        .header-bg-shapes::after {
            content: ''; position: absolute; inset: 0; opacity: 0.12;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M54.627 0l.83.83L1.244 55.443L.414 54.613L54.627 0zm-3.883 0l.83.83L0 51.573L0 50.743L50.744 0h3.883zM0 54.613l.83.83L4.713 60H3.883L0 56.117v-1.504zM0 46.857l.83.83L12.426 60H11.596L0 48.404v-1.547zM0 39.102l.83.83L20.14 60h-.83L0 40.648v-1.546zm0-7.746l.83.83L27.853 60h-.83L0 32.893v-1.546zm0-7.746l.83.83L35.566 60h-.83L0 25.138v-1.546zm0-7.746l.83.83L43.28 60h-.83L0 17.382v-1.546zM0 8.39l.83.83L50.993 60h-.83L0 9.728V8.39zM8.39 0l.83.83L60 50.78v.83L9.728 0H8.39zM16.136 0l.83.83L60 43.033v.83L17.474 0h-1.338zm7.746 0l.83.83L60 35.287v.83L25.22 0h-1.338zm7.746 0l.83.83L60 27.54v.83L32.966 0h-1.338zm7.746 0l.83.83L60 19.794v.83L40.712 0h-1.338zm7.746 0l.83.83L60 12.048v.83L48.458 0h-1.338zm7.746 0l.83.83L60 4.302v.83L56.204 0h-1.338z' fill='%23ffffff' fill-opacity='1' fill-rule='evenodd'/%3E%3C/svg%3E");
        }
        .header-content-inner { position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .header-badge {
            display: inline-block; padding: 4px 12px; background: rgba(14, 165, 233, 0.2);
            color: #38bdf8; font-size: 0.65rem; font-weight: 800; border-radius: 20px;
            border: 1px solid rgba(56, 189, 248, 0.35); margin-bottom: 0.6rem; letter-spacing: 0.8px; text-transform: uppercase;
        }
        .header-text-premium h1 { font-size: 1.6rem; font-weight: 900; margin: 0; letter-spacing: -0.5px; line-height: 1.2; display: flex; align-items: center; gap: 10px; }
        .header-text-premium p { font-size: 0.85rem; opacity: 0.82; margin-top: 0.4rem; max-width: 520px; font-weight: 500; line-height: 1.5; }
        .header-actions-premium { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }

        /* ── Elite Button System ── */
        .e-btn {
            display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px;
            border-radius: 12px; font-weight: 700; font-size: 0.82rem; cursor: pointer;
            border: none; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: inherit; text-decoration: none; line-height: 1.4;
        }
        .e-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.12); }
        .e-btn i, .e-btn svg { width: 16px; height: 16px; }
        .e-btn-primary { background: #0284c7; color: #fff; box-shadow: 0 8px 20px rgba(2, 132, 199, 0.35); }
        .e-btn-primary:hover { background: #0369a1; }
        .e-btn-dark { background: #0f172a; color: #fff; border: 1px solid rgba(255,255,255,0.12); }
        .e-btn-dark:hover { background: #1e293b; }
        .e-btn-glass { background: rgba(255, 255, 255, 0.12); color: #fff; backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.18); }
        .e-btn-glass:hover { background: rgba(255, 255, 255, 0.22); }
        .e-btn-blue-light { background: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; }
        .e-btn-blue-light:hover { background: #e0f2fe; }

        /* ── Glassmorphic Cards & Pills ── */
        .filter-card { 
            padding: 1rem 1.5rem; border-radius: 18px; 
            background: rgba(255, 255, 255, 0.75); backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.9); margin-bottom: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            display: flex; align-items: center; flex-wrap: wrap; gap: 15px;
        }
        .table-card { 
            border-radius: 20px; overflow: hidden; 
            background: rgba(255, 255, 255, 0.85); 
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 10px 30px rgba(0,0,0,0.03); 
            padding: 0.5rem;
        }
        .summary-pill { 
            background: rgba(240, 249, 255, 0.85); backdrop-filter: blur(6px); 
            color: #0284c7; padding: 8px 18px; border-radius: 50px; 
            font-size: 0.85rem; font-weight: 800; border: 1px solid rgba(2, 132, 199, 0.25); 
        }
        .z-badge-blue {
            background: #f0f9ff; color: #0284c7; padding: 4px 10px;
            border-radius: 10px; font-size: 0.75rem; font-weight: 800;
            border: 1px solid rgba(2, 132, 199, 0.2);
        }

        /* â”€â”€ Override page titles and stats from zamrud.css â”€â”€ */
        .z-page-head h1, .z-page-head-row h1 {
            font-size: 1.25rem !important;
        }
        .z-page-head p, .z-page-head-row p {
            font-size: 12.5px !important;
        }
        .z-stat-value {
            font-size: 1.15rem !important;
        }
        .z-stat-label {
            font-size: 11px !important;
        }
        .z-stat-trend {
            font-size: 11px !important;
        }
        .btn, .btn-sm {
            font-size: 12px !important;
            padding: 6px 12px !important;
        }
        .z-input, .z-field {
            font-size: 12.8px !important;
            padding: 6px 10px !important;
        }

        /* â”€â”€ BUTTON COMPAT FOR .z-btn â”€â”€ */
        .z-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px !important;
            border-radius: 8px !important;
            font-weight: 700 !important;
            font-size: 12.5px !important;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            text-decoration: none;
            font-family: inherit;
        }
        .z-btn:hover {
            transform: translateY(-1px);
        }
        .z-btn-primary {
            background: var(--z-primary) !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.2);
        }
        .z-btn-primary:hover {
            background: var(--z-primary-h) !important;
        }
        .z-btn-secondary {
            background: #eff6ff !important;
            color: #2563eb !important;
        }
        .z-btn-danger {
            background: #fff1f2 !important;
            color: #e11d48 !important;
        }
        .z-btn-ghost {
            background: transparent !important;
            color: var(--z-muted) !important;
            border: 1px solid var(--z-border) !important;
        }
        .z-btn-ghost:hover {
            background: var(--z-border) !important;
        }
        .z-btn-sm {
            padding: 4px 8px !important;
            font-size: 11.5px !important;
        }
        .z-btn-white {
            background: #fff !important;
            color: var(--z-text) !important;
            border: 1px solid var(--z-border) !important;
        }
        .z-btn-black {
            background: #0f172a !important;
            color: #fff !important;
        }
        .z-btn-emerald-light {
            background: #f0fdf4 !important;
            color: #0ea5e9 !important;
        }

        /* â”€â”€ GLOBAL MODALS (.z-modal) â”€â”€ */
        .z-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100vw !important;
            height: 100vh !important;
            background: rgba(15, 23, 42, 0.55) !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
            z-index: 99999 !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 20px !important;
            box-sizing: border-box !important;
            /* Core Fixes: Prevents inheritance of card styles from zamrud.css */
            max-width: none !important;
            max-height: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            border: none !important;
            overflow-y: hidden !important;
        }
        .z-modal-content {
            background: #fff;
            width: 100%;
            max-width: 520px;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.3);
            animation: modalFadeUp 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            display: flex;
            flex-direction: column;
            max-height: 92vh;
            margin: auto;
        }
        .z-modal-content.small { max-width: 440px; }
        .z-modal-content.large { 
            max-width: 1100px;
            width: 95vw;
            max-height: 92vh;
        }
        @keyframes modalFadeUp {
            from { opacity: 0; transform: translateY(30px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .z-modal-header {
            background: #f8fafc;
            color: #0f172a;
            padding: 1.15rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #f1f5f9;
        }
        .z-modal-header h3 { margin: 0; font-size: 1.05rem; font-weight: 800; display: flex; align-items: center; gap: 8px; }
        .z-modal-header .close-btn { background: none; border: none; color: #94a3b8; cursor: pointer; transition: 0.2s; font-size: 1.25rem; display: flex; align-items: center; }
        .z-modal-header .close-btn:hover { color: #e11d48; }
        .z-modal-body { padding: 1.5rem; overflow-y: auto; max-height: calc(85vh - 120px); }
        .z-modal-footer { padding: 1rem 1.5rem; background: #f8fafc; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; gap: 10px; }
        .z-textarea { min-height: 80px; resize: none; }

        /* â”€â”€ MODAL FORMS & GRID UTILS â”€â”€ */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }
        .form-grid.full-width { grid-column: span 2; }
        .form-sections { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .form-group { margin-bottom: 1.25rem; }
        .form-group.full-width { grid-column: span 2; }
        .form-group label {
            display: block;
            font-size: 0.82rem;
            font-weight: 700;
            color: rgba(255,255,255,0.4);
            margin-bottom: 6px;
        }
        .form-section { background: #f8fafc; padding: 1.25rem; border-radius: 14px; border: 1px solid #e2e8f0; }
        .form-section h4 {
            display: flex; align-items: center; gap: 8px; font-size: 0.92rem;
            color: #1e293b; margin-bottom: 1.25rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;
        }



        /* â”€â”€ SIAKAD sidebar brand area â”€â”€ */
        .z-brand {
            display: flex; align-items: center; gap: 12px;
            padding: 1.25rem 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            flex-shrink: 0;
        }
        .z-logo-box {
            width: 40px; height: 40px; border-radius: 11px;
            background: #fff; display: flex; align-items: center;
            justify-content: center; overflow: hidden; flex-shrink: 0;
            padding: 5px;
        }
        .z-close-btn {
            background: none; border: none; color: rgba(255,255,255,.45);
            cursor: pointer; margin-left: auto; display: none; padding: 4px;
            border-radius: 6px;
        }
        .z-close-btn:hover { background: rgba(255,255,255,.1); color: #fff; }
        .z-close-btn i { width: 18px; height: 18px; }

        /* â”€â”€ Nav â”€â”€ */
        .z-nav { flex: 1; overflow-y: auto; padding: .75rem 0; scrollbar-width: none; }
        .z-nav::-webkit-scrollbar { display: none; }
        .z-nav-label {
            font-size: .62rem; font-weight: 800;
            color: rgba(255,255,255,.3);
            padding: 1rem 1.25rem .3rem;
            letter-spacing: 1.5px; text-transform: uppercase;
        }
        .z-nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: .55rem 1rem; color: rgba(255,255,255,.6);
            font-size: .8rem; font-weight: 600; transition: .15s;
            margin: 1px .5rem; border-radius: 8px; text-decoration: none;
        }
        .z-nav-item:hover { background: rgba(255,255,255,.07); color: #fff; }
        .z-nav-item.active {
            background: linear-gradient(90deg, #0ea5e9, #0284c7);
            color: #fff; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.25);
            font-weight: 800;
        }
        .z-nav-item i { width: 16px; height: 16px; flex-shrink: 0; }

        /* â”€â”€ Nav Group (Dropdown) â”€â”€ */
        .z-nav-group { margin: 1px .5rem; border-radius: 8px; }
        .z-nav-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: .55rem .75rem; color: rgba(255,255,255,.6);
            font-size: .8rem; font-weight: 600; cursor: pointer;
            transition: .15s; border-radius: 8px;
        }
        .z-nav-header:hover { background: rgba(255,255,255,.07); color: #fff; }
        .z-subnav { max-height: 0; overflow: hidden; transition: max-height .3s ease; }
        .z-nav-group.open .z-subnav { max-height: 400px; }
        .z-nav-group.open .chev { transform: rotate(90deg); }
        .z-subnav-item {
            display: block; padding: .4rem 1rem .4rem 2.5rem;
            color: rgba(255,255,255,.5); font-size: 0.65rem;
            text-decoration: none; transition: .25s; border-radius: 0 10px 10px 0;
            margin: 2px 1rem 2px 0;
        }.z-subnav-item:hover { color: #fff; background: rgba(255,255,255,.04); }
        .z-subnav-item.active {
            color: #fff; background: rgba(255,255,255,.1);
            font-weight: 800; border-left: 3px solid #0ea5e9;
            padding-left: calc(2.75rem - 3px);
        }
        .chev { transition: transform .3s ease; opacity: .5; width: 12px !important; height: 12px !important; }

        /* â”€â”€ User card at bottom â”€â”€ */
        .z-user-card {
            margin: .75rem; background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.06); border-radius: 14px;
            padding: .75rem; display: flex; align-items: center;
            gap: 10px; flex-shrink: 0;
        }
        .z-uc-av {
            width: 38px; height: 38px; border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: .8rem; flex-shrink: 0;
        }
        .z-uc-name { font-size: .82rem; font-weight: 700; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .z-uc-role { font-size: .65rem; color: rgba(255,255,255,.4); }
        .z-uc-info { flex: 1; min-width: 0; }

        /* â”€â”€ MAIN CONTENT OFFSET from fixed sidebar â”€â”€ */
        /*
         * .z-sidebar is position:fixed (from zamrud.css).
         * Anything using siakad-container pattern OR z-main needs margin-left.
         */

        /* Pattern 1: siakad-container > div[style*="flex:1"] */
        .siakad-container { display: contents; }
        .siakad-container > div[style*="flex: 1"],
        .siakad-container > div[style*="flex:1"] {
            margin-left: calc(var(--z-sidebar-w) + var(--z-gap) * 2);
            min-height: 100vh;
            background: var(--z-bg);
            padding: var(--z-gap) !important;
            display: flex;
            flex-direction: column;
        }

        /* Pattern 2: z-main (used by SiakadDashboardController) */
        /* Already handled by zamrud.css .z-main margin-left */

        /* â”€â”€ CARD â”€â”€ */
        .card {
            background: #fff;
            border: 1px solid var(--z-border);
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,.03);
            transition: all .2s;
        }
        .card:hover {
            box-shadow: 0 8px 30px rgba(2,132,199,.08);
            transform: translateY(-1px);
        }

        /* â”€â”€ TABLE â”€â”€ */
        .z-table-wrap { width: 100%; overflow-x: auto; }
        .z-table { width: 100%; border-collapse: collapse; background: #fff; }
        .z-table th {
            text-align: left; padding: .65rem 1rem;
            font-size: .65rem; font-weight: 800; color: var(--z-muted);
            text-transform: uppercase; letter-spacing: .8px;
            background: #f8fafc; border-bottom: 2px solid #f1f5f9;
            white-space: nowrap;
        }
        .z-table td { padding: .65rem 1rem; border-bottom: 1px solid #f1f5f9; font-size: .81rem; vertical-align: middle; }
        .z-table tr:last-child td { border-bottom: none; }
        .z-table tr:hover td { background: #fafffe; }

        /* â”€â”€ FORM COMPAT â”€â”€ */
        .label-sm {
            display: block; margin-bottom: 4px;
            font-size: .68rem; font-weight: 700;
            color: var(--z-muted); text-transform: uppercase; letter-spacing: .5px;
        }
        .input-sm {
            width: 100%; padding: 7px 12px;
            border: 1px solid var(--z-border); border-radius: 7px;
            font-family: inherit; font-size: .81rem;
            color: var(--z-text); outline: none; background: #fff;
            transition: .15s;
        }
        .input-sm:focus { border-color: var(--z-primary); box-shadow: 0 0 0 3px rgba(2,132,199,.1); }
        select.input-sm {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 10px center; background-size: 1rem;
            padding-right: 2rem;
        }
        .input-sm[style*="color: white"] option { color: #fff; }

        /* â”€â”€ MODAL COMPAT â”€â”€ */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.5); backdrop-filter: blur(6px);
            z-index: 2000; align-items: center; justify-content: center;
        }
        .modal-overlay.open, .modal-overlay[style*="display: flex"] { display: flex; }

        /* â”€â”€ BTN COMPAT â”€â”€ */
        .btn-primary { background: var(--z-primary) !important; color: #fff !important; }
        .btn-primary:hover { background: var(--z-primary-h) !important; }

        /* â”€â”€ NOTIFICATION â”€â”€ */
        #zns-container {
            position: fixed; top: 20px; right: 20px; z-index: 9999;
            display: flex; flex-direction: column; gap: 10px; pointer-events: none;
        }
        .zns-toast {
            pointer-events: auto; min-width: 300px;
            padding: 16px 20px; border-radius: 16px;
            background: rgba(255,255,255,.92); backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,.5);
            box-shadow: 0 10px 30px rgba(0,0,0,.1);
            display: flex; align-items: center; gap: 12px;
            transform: translateX(120%); transition: all .5s cubic-bezier(.68,-.55,.265,1.55);
        }
        .zns-toast.active { transform: translateX(0); }
        .zns-toast.success { border-left: 5px solid #0ea5e9; }
        .zns-toast.error   { border-left: 5px solid #ef4444; }
        .zns-icon { width: 24px; height: 24px; }
        .zns-success .zns-icon { color: #0ea5e9; }
        .zns-error .zns-icon { color: #ef4444; }
        .zns-msg { font-weight: 600; color: #1e293b; font-size: .95rem; }
        #zns-confirm-overlay {
            position: fixed; inset: 0;
            background: rgba(15,23,42,.45); backdrop-filter: blur(4px);
            z-index: 10000; display: none; align-items: center; justify-content: center;
        }
        .zns-confirm-card {
            background: #fff; padding: 30px; border-radius: 24px;
            width: 90%; max-width: 400px; text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,.25);
            transform: scale(.9); transition: .3s;
        }
        #zns-confirm-overlay.active { display: flex; }
        #zns-confirm-overlay.active .zns-confirm-card { transform: scale(1); }
        .zns-confirm-title { font-size: 1.25rem; font-weight: 800; color: #1e293b; margin-bottom: 10px; }
        .zns-confirm-msg { color: #64748b; margin-bottom: 25px; line-height: 1.5; font-size: 0.95rem; }
        .zns-confirm-btns { display: flex; gap: 10px; justify-content: center; }
        .zns-btn { padding: 10px 24px; border-radius: 12px; font-weight: 700; cursor: pointer; border: none; transition: .2s; }
        .zns-btn-cancel { background: #f1f5f9; color: #475569; }
        .zns-btn-confirm { background: #0ea5e9; color: white; box-shadow: 0 4px 12px rgba(14,165,233,.2); }
        .zns-btn-confirm.danger { background: #e11d48; box-shadow: 0 4px 12px rgba(225,29,72,.2); }
        .zns-btn:hover { transform: translateY(-2px); filter: brightness(.95); }

        /* â”€â”€ RESPONSIVE â”€â”€ */
        @media(max-width: 768px) {
            .z-close-btn { display: flex !important; }
            .z-sidebar { transform: translateX(calc(-100% - 30px)); top: 0; left: 0; bottom: 0; border-radius: 0; }
            .z-sidebar.open { transform: translateX(0); }
            .siakad-container > div[style*="flex: 1"],
            .siakad-container > div[style*="flex:1"] {
                margin-left: 0;
                padding: 1rem !important;
            }
            .z-main { margin-left: 0 !important; }
        }

        /* â”€â”€ MINIMIZED SIDEBAR (DESKTOP) â”€â”€ */
        body.sidebar-minimized { --z-sidebar-w: 80px; }
        body.sidebar-minimized .z-sidebar-logo > div,
        body.sidebar-minimized .z-sidebar-logo .logo-text,
        body.sidebar-minimized .z-sidebar-logo .logo-sub,
        body.sidebar-minimized .z-nav-cat,
        body.sidebar-minimized .z-nav-header .left,
        body.sidebar-minimized .chev { display: none !important; }
        body.sidebar-minimized .z-sidebar-logo > img { margin-bottom: 0 !important; width: 40px !important; height: 40px !important; }
        body.sidebar-minimized .z-nav-item,
        body.sidebar-minimized .z-subnav-item {
            font-size: 0; white-space: nowrap; overflow: hidden; color: transparent;
        }
        body.sidebar-minimized .z-nav-item i { margin: 0 auto; color: rgba(255,255,255,0.6); }
        body.sidebar-minimized .z-nav-item:hover i,
        body.sidebar-minimized .z-nav-item.active i { color: white; }
        body.sidebar-minimized .z-sidebar-logo { padding: 0; justify-content: center; }
        body.sidebar-minimized .z-nav-item { padding: .85rem 0; justify-content: center; margin: 4px .5rem; }
        body.sidebar-minimized .z-nav-header { justify-content: center; padding: .85rem 0; }
        body.sidebar-minimized .z-nav-header i { margin: 0 auto; }
        body.sidebar-minimized .z-user-card { padding: .75rem 0; justify-content: center; border-color: transparent; background: transparent; }
        body.sidebar-minimized .z-uc-info { display: none; }
        body.sidebar-minimized .academic-year-widget { display: none; }
    </style>
</head>
<body>
<div style="min-height: 100vh;">
    <?php echo $content; ?>
</div>

<!-- ZNS Notification System -->
<div id="zns-container"></div>
<div id="zns-confirm-overlay">
    <div class="zns-confirm-card">
        <div id="zns-confirm-icon" style="margin-bottom:15px;color:#0ea5e9;">
            <i data-lucide="help-circle" style="width:48px;height:48px;"></i>
        </div>
        <div class="zns-confirm-title" id="zns-confirm-title">Konfirmasi</div>
        <div class="zns-confirm-msg" id="zns-confirm-msg">Apakah Anda yakin?</div>
        <div class="zns-confirm-btns">
            <button class="zns-btn zns-btn-cancel" onclick="ZNS.closeConfirm(false)">Batal</button>
            <button class="zns-btn zns-btn-confirm" id="zns-confirm-btn-ok" onclick="ZNS.closeConfirm(true)">Ya, Lanjutkan</button>
        </div>
    </div>
</div>

<!-- Mobile overlay for sidebar -->
<div id="zOverlay" onclick="document.querySelectorAll('.z-sidebar').forEach(s=>s.classList.remove('open'));this.style.display='none';" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:299;backdrop-filter:blur(2px);"></div>

<script>
lucide.createIcons();

function zToggleSidebar() {
    const sidebar = document.getElementById('zSidebar');
    const overlay = document.getElementById('zOverlay');
    if(sidebar) {
        if(window.innerWidth > 768) {
            document.body.classList.toggle('sidebar-minimized');
        } else {
            sidebar.classList.toggle('open');
            overlay.style.display = sidebar.classList.contains('open') ? 'block' : 'none';
        }
    }
}

const ZNS = {
    confirmCallback: null,
    showSuccess(msg) { this.createToast(msg, 'success', 'check-circle'); },
    showError(msg) { this.createToast(msg, 'error', 'alert-circle'); },
    createToast(msg, type, icon) {
        const id = 'zns-' + Date.now();
        const html = `<div id="${id}" class="zns-toast ${type} zns-${type}"><i data-lucide="${icon}" class="zns-icon"></i><span class="zns-msg">${msg}</span></div>`;
        document.getElementById('zns-container').insertAdjacentHTML('beforeend', html);
        lucide.createIcons();
        setTimeout(() => document.getElementById(id)?.classList.add('active'), 10);
        setTimeout(() => { const el = document.getElementById(id); if(el){el.classList.remove('active');setTimeout(()=>el.remove(),500);} }, 4000);
    },
    alert(msg, title = 'Informasi', callback = null) {
        document.getElementById('zns-confirm-msg').innerText = msg;
        document.getElementById('zns-confirm-title').innerText = title;
        document.getElementById('zns-confirm-btn-ok').innerText = 'OK';
        document.querySelector('.zns-btn-cancel').style.display = 'none';
        this.confirmCallback = callback;
        document.getElementById('zns-confirm-overlay').classList.add('active');
    },
    confirm(msg, callback, title = 'Konfirmasi', btnText = 'Ya, Lanjutkan', isDanger = false) {
        document.getElementById('zns-confirm-msg').innerHTML = msg;
        document.getElementById('zns-confirm-title').innerText = title;
        let btnOk = document.getElementById('zns-confirm-btn-ok');
        btnOk.innerText = btnText;
        if(isDanger) {
            btnOk.classList.add('danger');
            document.getElementById('zns-confirm-icon').innerHTML = '<i data-lucide="alert-triangle" style="width:48px;height:48px;color:#e11d48;"></i>';
        } else {
            btnOk.classList.remove('danger');
            document.getElementById('zns-confirm-icon').innerHTML = '<i data-lucide="help-circle" style="width:48px;height:48px;"></i>';
        }
        lucide.createIcons();
        document.querySelector('.zns-btn-cancel').style.display = 'inline-block';
        this.confirmCallback = callback;
        document.getElementById('zns-confirm-overlay').classList.add('active');
    },
    closeConfirm(result) {
        document.getElementById('zns-confirm-overlay').classList.remove('active');
        if (result && this.confirmCallback) this.confirmCallback();
        this.confirmCallback = null;
    }
};
</script>
</body>
</html>



