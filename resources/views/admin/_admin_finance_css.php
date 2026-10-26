<style>
    :root {
        --primary: #0284c7;
        --primary-hover: #075985;
        --sidebar-bg: #071f14;
        --sidebar-hover: rgba(255, 255, 255, 0.05);
        --sidebar-text: #e2e8f0;
        --sidebar-active: #0ea5e9;
        --bg-body: #eef2f0;
        --card-bg: #ffffff;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --border: #e2e8f0;
        --sidebar-width: 252px;
        --header-height: 70px;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-body); color: var(--text-main); overflow: hidden; }
    .wrapper { display: flex; width: 100%; height: 100vh; overflow: hidden; }

    /* ── Sidebar ── */
    .sidebar { width: var(--sidebar-width); background-color: var(--sidebar-bg); color: var(--sidebar-text); display: flex; flex-direction: column; transition: all 0.3s ease; position: relative; z-index: 100; flex-shrink: 0; }
    .sidebar-header { height: var(--header-height); display: flex; align-items: center; justify-content: center; border-bottom: 1px solid rgba(255,255,255,0.1); font-size: 1.05rem; font-weight: 700; color: #fff; gap: 10px; letter-spacing: 0.5px; }
    .sidebar-scroll { flex: 1; overflow-y: auto; padding: 1rem 0; }
    .nav-category { font-size: 0.7rem; text-transform: uppercase; font-weight: 800; color: rgba(255,255,255,0.4); padding: 1rem 1.5rem 0.4rem; letter-spacing: 1.2px; }
    .nav-item { display: flex; align-items: center; gap: 12px; padding: 0.75rem 1.5rem; color: var(--sidebar-text); text-decoration: none; transition: 0.2s; font-size: 0.9rem; }
    .nav-item:hover { background-color: var(--sidebar-hover); color: #fff; }
    .nav-item.active { background-color: var(--sidebar-active); color: #fff; border-left: 4px solid #fff; }
    .nav-item i { width: 18px; height: 18px; opacity: 0.85; flex-shrink: 0; }
    .nav-item.active i { opacity: 1; }
    .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 90; }

    /* ── Main ── */
    .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }
    .header { height: var(--header-height); background: var(--card-bg); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 1.5rem; flex-shrink: 0; }
    .header-left { display: flex; align-items: center; gap: 12px; }
    .menu-toggle { display: none; background: none; border: none; color: var(--text-main); cursor: pointer; padding: 4px; }
    .header-badge { background: #f0f9ff; color: var(--primary); font-size: 0.7rem; font-weight: 800; padding: 3px 10px; border-radius: 20px; border: 1px solid #bae6fd; text-transform: uppercase; letter-spacing: 0.5px; }
    .header-user { display: flex; align-items: center; gap: 10px; }
    .header-user-info { text-align: right; }
    .header-user-info .name { font-weight: 700; font-size: 0.85rem; color: var(--text-main); }
    .header-user-info .role { font-size: 0.72rem; color: var(--text-muted); }
    .user-avatar { width: 36px; height: 36px; border-radius: 10px; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; flex-shrink: 0; }

    .content-scroll { flex: 1; overflow-y: auto; padding: 1.5rem; }
    .page-header { margin-bottom: 1.5rem; }
    .page-header h1 { font-size: 1.4rem; font-weight: 800; color: var(--text-main); }
    .page-header p { font-size: 0.85rem; color: var(--text-muted); margin-top: 3px; }

    /* ── Cards ── */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem; }
    .stat-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 14px; padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 14px; box-shadow: 0 1px 4px rgba(0,0,0,0.04); }
    .stat-card-left { flex: 1; min-width: 0; }
    .stat-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
    .stat-value { font-size: 1.35rem; font-weight: 800; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .stat-trend { font-size: 0.73rem; font-weight: 600; margin-top: 4px; display: flex; align-items: center; gap: 3px; }
    .trend-up { color: #0ea5e9; } .trend-down { color: #ef4444; }
    .stat-icon { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .stat-icon i { width: 22px; height: 22px; }
    .icon-green { background: #f0f9ff; color: #0ea5e9; }
    .icon-red   { background: #fef2f2; color: #ef4444; }
    .icon-blue  { background: #eff6ff; color: #3b82f6; }
    .icon-amber { background: #fffbeb; color: #f59e0b; }
    .icon-purple{ background: #f5f3ff; color: #7c3aed; }

    /* ── Panel ── */
    .panel { background: var(--card-bg); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.04); margin-bottom: 1.5rem; }
    .panel-header { padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    .panel-title { font-weight: 700; font-size: 0.95rem; color: var(--text-main); display: flex; align-items: center; gap: 8px; }
    .panel-title i { color: var(--primary); width: 18px; height: 18px; }
    .panel-body { padding: 1.25rem 1.5rem; }

    /* ── Table ── */
    .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    table { width: 100%; border-collapse: collapse; min-width: 500px; }
    th { text-align: left; padding: 10px 16px; font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; background: #f9fafb; border-bottom: 1px solid var(--border); white-space: nowrap; }
    td { padding: 12px 16px; border-bottom: 1px solid #f3f4f6; font-size: 0.875rem; vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: #fafafa; }

    /* ── User cell ── */
    .user-cell { display: flex; align-items: center; gap: 10px; }
    .user-avatar-sm { width: 30px; height: 30px; border-radius: 8px; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem; flex-shrink: 0; }
    .user-name { font-weight: 700; font-size: 0.85rem; color: var(--text-main); }
    .user-sub  { font-size: 0.72rem; color: var(--text-muted); }

    /* ── Pills ── */
    .pill { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; white-space: nowrap; }
    .pill-green  { background: #dcfce7; color: #16a34a; }
    .pill-red    { background: #fee2e2; color: #dc2626; }
    .pill-amber  { background: #fef3c7; color: #d97706; }
    .pill-blue   { background: #dbeafe; color: #2563eb; }
    .pill-gray   { background: #f3f4f6; color: #6b7280; }

    .amount { font-weight: 800; color: var(--primary); }
    .amount-red { font-weight: 800; color: #ef4444; }

    /* ── Buttons ── */
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-weight: 700; font-size: 0.82rem; cursor: pointer; border: none; transition: 0.2s; text-decoration: none; }
    .btn-primary { background: var(--primary); color: #fff; }
    .btn-primary:hover { background: var(--primary-hover); }
    .btn-outline { background: #f0f9ff; color: var(--primary); border: 1px solid #bae6fd; }
    .btn-outline:hover { background: var(--primary); color: #fff; }
    .btn-sm { padding: 5px 12px; font-size: 0.75rem; }
    .btn-danger { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .btn-danger:hover { background: #dc2626; color: #fff; }

    /* ── Filter Row ── */
    .filter-row { display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap; margin-bottom: 1rem; }
    .filter-group { display: flex; flex-direction: column; gap: 4px; }
    .filter-label { font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
    select, input[type="text"] { padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; font-family: inherit; font-size: 0.875rem; color: var(--text-main); background: #fff; min-width: 160px; }
    select:focus, input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(6,95,70,0.1); }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .menu-toggle { display: block !important; }
        .sidebar { position: fixed; left: -250px; height: 100vh; box-shadow: 10px 0 30px rgba(0,0,0,0.1); }
        .sidebar.active { left: 0; }
        .sidebar-overlay.active { display: block; }
        .header-user-info { display: none; }
        .stats-grid { grid-template-columns: repeat(2,1fr); }
    }
    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; }
        .content-scroll { padding: 1rem; }
        .filter-row { flex-direction: column; }
        select, input[type="text"] { min-width: 100%; width: 100%; }
    }
</style>

