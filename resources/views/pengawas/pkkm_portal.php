<?php
use App\Core\Branding;

$brand = Branding::get();
$logoUrl = Branding::getInstitusiLogo();

$sekolah_nama = $inst['nama'] ?? 'MAS MIFTAHUL HUDA MATHLA\'UL ANWAR';
$npsn = $inst['npsn'] ?? '60705617';
$nsm = $inst['nsm'] ?? '111218040009';
$kepala_nama = $kamad['nama'] ?? 'SAMSUL ARIFIN, M.Pd';
$kepala_nip = $kamad['nip'] ?? '-';

$pgId = $pengawas['id'] ?? 1;
$pgNama = $pengawas['nama'] ?? 'Pengawas Penilai';
$pgNip = $pengawas['nip'] ?? '-';
$pgJabatan = $pengawas['jabatan'] ?? 'Pengawas Madrasah Ahli Madya';
$pgInstansi = $pengawas['instansi'] ?? 'Kantor Kementerian Agama Kab. Lampung Barat';
$pgPeran = $pengawas['peran'] ?? 'penilai_1';
$pgTtd = $pengawas['ttd_digital'] ?? null;
$pgPeranLabel = ($pgPeran === 'penilai_1') ? 'PENILAI 1 (KETUA TIM ASESOR)' : 'PENILAI 2 (ANGGOTA TIM ASESOR)';

// Inisial Nama Pengawas
$words = explode(' ', trim($pgNama));
$pgInitial = '';
foreach ($words as $w) {
    if (strlen($w) > 0 && !preg_match('/^(H\.|Dr\.|Dra\.|Drs\.|M\.|S\.|Ir\.)$/i', $w)) {
        $pgInitial .= strtoupper($w[0]);
        if (strlen($pgInitial) >= 2) break;
    }
}
if (empty($pgInitial)) $pgInitial = strtoupper(substr($pgNama, 0, 2));

$curTab = $active_tab ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Asesor PKKM - <?php echo htmlspecialchars($sekolah_nama); ?></title>
    <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($brand['favicon']); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/assets/css/plus-jakarta-sans.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-emerald: #0284c7;
            --emerald-hover: #0284c7;
            --dark-emerald: #0369a1;
            --sidebar-width: 235px;
            --sidebar-collapsed-width: 66px;
            --bg-canvas: #edf2f7;
            --border-light: #e2e8f0;
            --border-medium: #cbd5e1;
            --text-heading: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background-color: var(--bg-canvas);
            color: var(--text-body);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ─── APP SHELL WITH SIDEBAR ─── */
        .app-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* ─── SIDEBAR ULTRA MINIMALIS (COLLAPSIBLE) ─── */
        .sidebar-asesor {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #0c4a6e 0%, #0f172a 100%);
            color: #ffffff;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1040;
            transition: width 0.22s cubic-bezier(0.4, 0, 0.2, 1), transform 0.22s cubic-bezier(0.4, 0, 0.2, 1);
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.12);
            overflow-x: hidden;
            white-space: nowrap;
        }

        .sidebar-brand {
            padding: 14px 14px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            min-height: 60px;
        }
        .sidebar-brand img {
            width: 32px;
            height: 32px;
            object-fit: contain;
            border-radius: 8px;
            padding: 2px;
            background: #ffffff;
            flex-shrink: 0;
        }
        .sidebar-brand .brand-title {
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 0.3px;
            line-height: 1.2;
            color: #ffffff;
        }
        .sidebar-brand .brand-sub {
            font-size: 0.62rem;
            color: #38bdf8;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .btn-collapse-sidebar {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: rgba(255, 255, 255, 0.8);
            border-radius: 6px;
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s ease;
            padding: 0;
        }
        .btn-collapse-sidebar:hover {
            background: rgba(255, 255, 255, 0.18);
            color: #ffffff;
        }

        /* User Profile in Sidebar Minimalis */
        .sidebar-user {
            padding: 8px 10px;
            background: rgba(255, 255, 255, 0.05);
            margin: 8px 10px 10px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            gap: 9px;
            transition: all 0.2s ease;
        }
        .user-avatar-initials {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.82rem;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3);
        }
        .user-status-dot {
            position: absolute;
            bottom: -2px;
            right: -2px;
            width: 9px;
            height: 9px;
            border-radius: 50%;
            border: 1.5px solid #0369a1;
        }
        .user-status-dot.ready { background: #38bdf8; }
        .user-status-dot.pending { background: #f59e0b; }

        .user-role-badge {
            display: inline-block;
            font-size: 0.58rem;
            font-weight: 700;
            padding: 1px 5px;
            border-radius: 4px;
            background: rgba(52, 211, 153, 0.18);
            color: #7dd3fc;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid rgba(52, 211, 153, 0.25);
            line-height: 1.2;
        }

        /* Nav Links */
        .sidebar-nav {
            padding: 2px 8px;
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }
        .nav-section-title {
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.4);
            padding: 6px 10px 4px;
            margin-top: 2px;
        }
        .side-nav-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 8px 10px;
            border-radius: 8px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.8rem;
            border: none;
            background: transparent;
            text-align: left;
            margin-bottom: 2px;
            transition: all 0.15s ease;
            cursor: pointer;
        }
        .side-nav-btn .nav-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .side-nav-btn:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
        }
        .side-nav-btn.active {
            background: #0284c7;
            color: #ffffff;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(2, 132, 199, 0.4);
        }
        .side-nav-btn .pill-badge {
            font-size: 0.66rem;
            font-weight: 700;
            padding: 1.5px 6px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
        }
        .side-nav-btn.active .pill-badge {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        /* Sidebar Footer Minimalis */
        .sidebar-footer {
            padding: 8px 10px;
            background: rgba(0, 0, 0, 0.22);
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }
        .btn-sidebar-collapse-footer {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.74rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 8px;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            transition: all 0.15s;
        }
        .btn-sidebar-collapse-footer:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
        }

        /* ─── COLLAPSED / MINI SIDEBAR RULES (DESKTOP) ─── */
        @media (min-width: 993px) {
            .app-wrapper.sidebar-collapsed .sidebar-asesor {
                width: var(--sidebar-collapsed-width);
            }
            .app-wrapper.sidebar-collapsed .main-content-wrap {
                margin-left: var(--sidebar-collapsed-width);
            }
            .app-wrapper.sidebar-collapsed .brand-text,
            .app-wrapper.sidebar-collapsed .user-text,
            .app-wrapper.sidebar-collapsed .side-nav-btn span,
            .app-wrapper.sidebar-collapsed .side-nav-btn .pill-badge,
            .app-wrapper.sidebar-collapsed .side-nav-btn .nav-badge-icon,
            .app-wrapper.sidebar-collapsed .nav-section-title,
            .app-wrapper.sidebar-collapsed .sidebar-footer-text,
            .app-wrapper.sidebar-collapsed .btn-collapse-sidebar {
                display: none !important;
            }
            .app-wrapper.sidebar-collapsed .sidebar-brand {
                justify-content: center;
                padding: 14px 8px;
            }
            .app-wrapper.sidebar-collapsed .sidebar-user {
                padding: 6px 2px;
                margin: 6px 4px 10px;
                background: transparent;
                border-color: transparent;
                justify-content: center;
            }
            .app-wrapper.sidebar-collapsed .side-nav-btn {
                justify-content: center;
                padding: 9px 8px;
            }
            .app-wrapper.sidebar-collapsed .side-nav-btn .nav-left {
                justify-content: center;
                gap: 0;
            }
            .app-wrapper.sidebar-collapsed .btn-sidebar-collapse-footer {
                justify-content: center;
                padding: 8px 0;
            }
        }

        /* ─── MAIN CONTENT AREA ─── */
        .main-content-wrap {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            min-height: 100vh;
            transition: margin-left 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ─── PORTAL TOPBAR (FLOATING HIJAU-PUTIH GRADIENT) ─── */
        .portal-topbar {
            background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 28%, #ffffff 70%, #ffffff 100%);
            border: 1.5px solid #bae6fd;
            border-radius: 16px;
            margin: 14px 22px 0;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 14px;
            z-index: 1000;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 10px 25px -5px rgba(2, 132, 199, 0.12), 0 4px 10px rgba(0, 0, 0, 0.03);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .portal-topbar:hover {
            box-shadow: 0 12px 28px -4px rgba(2, 132, 199, 0.16), 0 4px 10px rgba(0, 0, 0, 0.04);
            border-color: #7dd3fc;
        }

        .topbar-page-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #f0f9ff;
            color: var(--primary-emerald);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1px solid #e0f2fe;
        }
        .topbar-sub-badges {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 2px;
        }
        .badge-school-meta {
            font-size: 0.7rem;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-weight: 600;
        }
        .badge-tp-meta {
            font-size: 0.64rem;
            background: #f0f9ff;
            color: #0284c7;
            font-weight: 700;
            padding: 1px 7px;
            border-radius: 999px;
            border: 1px solid #e0f2fe;
        }

        /* TOPBAR KPI PILLS */
        .topbar-kpi-pill {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 999px;
            padding: 4px 11px;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            transition: all 0.15s ease;
            font-size: 0.74rem;
        }
        .topbar-kpi-pill:hover {
            border-color: #cbd5e1;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .topbar-kpi-pill.emerald-glow {
            background: #f0f9ff;
            border-color: #bae6fd;
            color: #0369a1;
        }
        .topbar-kpi-lbl {
            font-size: 0.64rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #64748b;
        }
        .topbar-kpi-val {
            font-size: 0.82rem;
            font-weight: 800;
            color: #0f172a;
        }

        .btn-topbar-print {
            background: #ffffff;
            color: #0f172a;
            border: 1.5px solid #e2e8f0;
            border-radius: 999px;
            padding: 5px 12px;
            font-size: 0.76rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.15s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }
        .btn-topbar-print:hover {
            background: #0284c7;
            color: #ffffff;
            border-color: #0284c7;
            box-shadow: 0 3px 10px rgba(2, 132, 199, 0.25);
        }

        .topbar-user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            font-weight: 800;
            font-size: 0.78rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.2);
        }

        /* PAGE BODY CONTAINER (FLUID & RESPONSIF SEJAJAR DENGAN HEADER) */
        .portal-page-body {
            padding: 16px 22px 40px;
            flex: 1;
            width: 100%;
            max-width: 100%;
            margin: 0;
            box-sizing: border-box;
        }

        /* ─── EXECUTIVE CARD (UNIFORM MODERN CARD CONTAINER) ─── */
        .executive-card {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 18px;
            box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.05), 0 1px 3px rgba(0, 0, 0, 0.02);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        .executive-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 10px 25px -4px rgba(15, 23, 42, 0.08), 0 3px 8px rgba(0, 0, 0, 0.03);
        }

        /* ─── GREEN BANNER TARGET EVALUASI (TAB PENILAIAN) ─── */
        .banner-penilaian-green {
            background: linear-gradient(135deg, #0c4a6e 0%, #0369a1 45%, #0284c7 100%);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 16px;
            padding: 16px 22px;
            color: #ffffff;
            box-shadow: 0 8px 24px -4px rgba(2, 132, 199, 0.28), 0 2px 6px rgba(0,0,0,0.04);
            position: relative;
            overflow: hidden;
            margin-bottom: 16px;
        }
        .banner-penilaian-green::before {
            content: '';
            position: absolute;
            top: -40px;
            right: 15%;
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, rgba(52, 211, 153, 0.2) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .banner-green-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.22);
            color: #38bdf8;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .banner-tag {
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.22);
            color: #7dd3fc;
            font-size: 0.62rem;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 999px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .banner-tag-sub {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.62rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 999px;
            letter-spacing: 0.4px;
        }
        .banner-green-progress-box {
            background: rgba(0, 0, 0, 0.22);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 12px;
            padding: 10px 16px;
            min-width: 250px;
        }

        /* ─── GREEN HERO BANNER (PRESTIGIOUS SULTAN GLASS-EMERALD CARD) ─── */
        .green-hero-banner {
            position: relative;
            padding: 22px 26px;
            margin-bottom: 22px;
            background: 
                radial-gradient(circle at 95% 5%, rgba(52, 211, 153, 0.22) 0%, transparent 45%),
                radial-gradient(circle at 5% 95%, rgba(16, 185, 129, 0.16) 0%, transparent 40%),
                radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px),
                linear-gradient(135deg, #0c4a6e 0%, #0369a1 45%, #0284c7 100%);
            background-size: 100% 100%, 100% 100%, 18px 18px, 100% 100%;
            border-radius: 18px;
            box-shadow: 0 12px 32px -4px rgba(2, 132, 199, 0.3), 0 4px 12px rgba(0, 0, 0, 0.05);
            color: #ffffff;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        .green-hero-banner::before {
            content: '';
            position: absolute;
            top: -60px;
            right: 20%;
            width: 240px;
            height: 240px;
            background: radial-gradient(circle, rgba(52, 211, 153, 0.18) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.22);
            color: #7dd3fc;
            font-size: 0.65rem;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 999px;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .live-pulse-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #38bdf8;
            box-shadow: 0 0 8px #38bdf8;
            display: inline-block;
            animation: hero-dot-pulse 2s infinite ease-in-out;
        }
        @keyframes hero-dot-pulse {
            0%, 100% { transform: scale(1); opacity: 1; box-shadow: 0 0 6px #38bdf8; }
            50% { transform: scale(1.25); opacity: 0.6; box-shadow: 0 0 12px #38bdf8; }
        }

        .hero-heading {
            font-size: 1.28rem;
            font-weight: 800;
            letter-spacing: -0.3px;
            margin-bottom: 4px;
            line-height: 1.25;
            color: #ffffff;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        }
        .hero-subtext {
            font-size: 0.76rem;
            color: rgba(255, 255, 255, 0.82);
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .hero-meta-bar {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(0, 0, 0, 0.22);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 5px 10px;
            border-radius: 10px;
            font-size: 0.74rem;
            color: rgba(255, 255, 255, 0.92);
            flex-wrap: wrap;
        }
        .hero-meta-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .hero-meta-divider {
            width: 3px;
            height: 3px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.35);
        }
        .badge-role-hero {
            background: rgba(52, 211, 153, 0.22);
            color: #bae6fd;
            font-weight: 700;
            font-size: 0.66rem;
            padding: 1.5px 6px;
            border-radius: 4px;
            border: 1px solid rgba(52, 211, 153, 0.3);
        }
        .ttd-badge-ok {
            color: #38bdf8;
            font-weight: 700;
        }
        .ttd-badge-warn {
            color: #fde047;
            font-weight: 700;
            background: rgba(251, 191, 36, 0.16);
            padding: 1px 6px;
            border-radius: 4px;
            border: 1px solid rgba(251, 191, 36, 0.3);
            transition: all 0.15s;
        }
        .ttd-badge-warn:hover {
            background: rgba(251, 191, 36, 0.28);
            color: #ffffff;
        }

        /* HERO ACTION / COMMAND CARD (RIGHT SIDE) */
        .hero-action-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 14px;
            padding: 12px 14px;
            min-width: 260px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }
        .hero-card-title {
            font-size: 0.72rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.9);
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .hero-card-pct {
            font-size: 0.78rem;
            font-weight: 800;
            color: #38bdf8;
        }
        .hero-card-progress {
            height: 5px;
            border-radius: 999px;
            background: rgba(0, 0, 0, 0.25);
            overflow: hidden;
            margin-bottom: 10px;
        }
        .hero-card-bar {
            height: 100%;
            background: linear-gradient(90deg, #0284c7 0%, #38bdf8 100%);
            border-radius: 999px;
            box-shadow: 0 0 8px rgba(52, 211, 153, 0.5);
            transition: width 0.3s ease;
        }
        .hero-card-actions {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .btn-hero-primary {
            background: #ffffff;
            color: #0369a1;
            font-weight: 800;
            font-size: 0.8rem;
            padding: 7px 13px;
            border-radius: 9px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            text-decoration: none;
            flex: 1;
            justify-content: center;
        }
        .btn-hero-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
            background: #f8fafc;
            color: #0284c7;
        }
        .btn-hero-primary .badge-count {
            background: #f0f9ff;
            color: #0284c7;
            padding: 1.5px 6px;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 800;
        }

        .btn-hero-secondary {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.8rem;
            padding: 7px 11px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.15s ease;
            cursor: pointer;
            text-decoration: none;
            justify-content: center;
        }
        .btn-hero-secondary:hover {
            background: rgba(255, 255, 255, 0.22);
            border-color: rgba(255, 255, 255, 0.4);
            color: #ffffff;
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .green-hero-banner { padding: 16px 18px; }
            .hero-heading { font-size: 1.12rem; }
            .hero-action-card { width: 100%; min-width: 0; margin-top: 6px; }
        }

        /* STAT CARDS (EXECUTIVE METRICS) */
        .stat-card-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 20px;
        }
        .stat-card-box {
            background: #ffffff;
            border-radius: 16px;
            padding: 14px 16px;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 4px 14px -2px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(0, 0, 0, 0.02);
            display: flex;
            align-items: center;
            gap: 14px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .stat-card-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -3px rgba(15, 23, 42, 0.08);
            border-color: #cbd5e1;
        }

        .stat-icon-wrap {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        }
        .stat-card-box.emerald .stat-icon-wrap { background: #f0f9ff; color: #0284c7; }
        .stat-card-box.blue .stat-icon-wrap { background: #eff6ff; color: #2563eb; }
        .stat-card-box.purple .stat-icon-wrap { background: #faf5ff; color: #7c3aed; }
        .stat-card-box.amber .stat-icon-wrap { background: #fffbeb; color: #d97706; }

        .stat-card-val { font-size: 1.32rem; font-weight: 800; line-height: 1.1; margin-bottom: 2px; color: var(--text-heading); letter-spacing: -0.3px; }
        .stat-card-lbl { font-size: 0.68rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.4px; }

        /* KOMPONEN PROGRESS CARDS */
        .komp-progress-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 0;
        }
        .komp-card {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px 16px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }
        .komp-card:hover {
            background: #ffffff;
            border-color: #38bdf8;
            box-shadow: 0 8px 20px rgba(2, 132, 199, 0.12);
            transform: translateY(-2px);
        }
        .komp-card-header {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 10px;
        }
        .komp-num-pill {
            width: 26px;
            height: 26px;
            border-radius: 6px;
            background: #f0f9ff;
            color: var(--primary-emerald);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.78rem;
            flex-shrink: 0;
        }

        /* TAB NAVIGATION KOMPONEN (PADA LEMBAR PENILAIAN) */
        .nav-tabs-komp {
            display: flex;
            gap: 6px;
            overflow-x: auto;
            padding-bottom: 6px;
            margin-bottom: 16px;
            scrollbar-width: thin;
        }
        .tab-komp-btn {
            background: #ffffff;
            border: 1px solid var(--border-medium);
            border-radius: 8px;
            padding: 6px 12px;
            font-weight: 600;
            font-size: 0.78rem;
            color: var(--text-muted);
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .tab-komp-btn:hover { border-color: var(--primary-emerald); color: var(--primary-emerald); }
        .tab-komp-btn.active {
            background: var(--primary-emerald);
            border-color: var(--primary-emerald);
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(2,132,199,0.25);
        }

        /* INDIKATOR CARD (EXECUTIVE ELEVATED) */
        .ind-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 18px 20px;
            margin-bottom: 16px;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 4px 14px -2px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(0, 0, 0, 0.02);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .ind-card:hover { 
            border-color: #cbd5e1; 
            box-shadow: 0 8px 24px -4px rgba(15, 23, 42, 0.08), 0 2px 6px rgba(0, 0, 0, 0.03); 
        }
        .ind-card.scored { border-left: 4.5px solid #0ea5e9; }

        .ind-num-pill {
            background: #f1f5f9;
            color: #475569;
            font-weight: 800;
            font-size: 0.75rem;
            padding: 2px 7px;
            border-radius: 6px;
            flex-shrink: 0;
        }

        .berkas-pill-link {
            background: #ffffff;
            color: #0284c7;
            border: 1px solid #e0f2fe;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            transition: all 0.15s;
        }
        .berkas-pill-link:hover {
            background: #f0f9ff;
            border-color: #bae6fd;
            color: #0369a1;
        }

        /* SCORING PANEL RAPIH & MODERN */
        .scoring-box {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            padding: 12px 14px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .ind-card.scored .scoring-box {
            background: #f0f9ff;
            border-color: #bae6fd;
        }
        .scoring-box-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 9px;
        }
        .scoring-label {
            font-size: 0.72rem;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .scoring-status-badge {
            font-size: 0.66rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .scoring-status-badge.pending {
            background: #fffbeb;
            color: #d97706;
            border: 1px solid #fde68a;
        }
        .scoring-status-badge.scored {
            background: #f0f9ff;
            color: #0284c7;
            border: 1px solid #bae6fd;
        }

        .scoring-options-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
            margin-bottom: 8px;
        }
        .score-option-btn {
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            padding: 6px 3px 5px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.16s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
            min-height: 52px;
            text-align: center;
        }
        .score-option-btn:hover {
            border-color: #0284c7;
            background: #f0f9ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(2, 132, 199, 0.15);
        }
        .score-option-btn .opt-num {
            font-size: 1.05rem;
            font-weight: 800;
            line-height: 1;
            color: #1e293b;
        }
        .score-option-btn .opt-desc {
            font-size: 0.58rem;
            font-weight: 700;
            color: #64748b;
            line-height: 1.15;
            letter-spacing: 0.1px;
            margin-top: 3px;
            display: block;
            text-align: center;
        }
        .score-option-btn.active {
            background: #0284c7;
            border-color: #0284c7;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.35);
            transform: translateY(-1px);
        }
        .score-option-btn.active .opt-num,
        .score-option-btn.active .opt-desc {
            color: #ffffff;
        }

        .scoring-box-footer {
            font-size: 0.72rem;
            text-align: center;
            padding-top: 6px;
            border-top: 1px dashed rgba(203, 213, 225, 0.8);
            line-height: 1.2;
        }

        /* TOAST NOTIF */
        .save-toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #0f172a;
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            display: none;
            align-items: center;
            gap: 10px;
            z-index: 9999;
            font-weight: 700;
            font-size: 0.86rem;
        }

        /* SIGNATURE CANVAS STYLES */
        .signature-pad-wrapper {
            background: #ffffff;
            border: 1.5px dashed #cbd5e1;
            border-radius: 16px;
            position: relative;
            touch-action: none;
            overflow: hidden;
            width: 100%;
            max-width: 520px;
            height: 200px;
            cursor: crosshair;
        }
        .signature-canvas {
            width: 100%;
            height: 100%;
            display: block;
        }
        .signature-guide-line {
            position: absolute;
            bottom: 45px;
            left: 30px;
            right: 30px;
            border-bottom: 1px dashed #cbd5e1;
            pointer-events: none;
        }
        .signature-guide-text {
            position: absolute;
            bottom: 12px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 0.72rem;
            color: #94a3b8;
            pointer-events: none;
            font-weight: 600;
        }

        /* BAP PREVIEW CARD */
        .bap-preview-box {
            background: #ffffff;
            border: 1px solid var(--border-medium);
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            border-radius: 14px;
            padding: 24px;
            max-width: 450px;
        }

        /* RESPONSIVE DRAWER */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0; right: 0; bottom: 0; left: 0;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(4px);
            z-index: 1030;
        }

        /* ─── FULLY SYNCHRONIZED RESPONSIVE LAYOUT & CARDS ─── */
        @media (max-width: 1200px) {
            .stat-card-row { grid-template-columns: repeat(2, 1fr); }
            .komp-progress-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 992px) {
            .sidebar-asesor { transform: translateX(-100%) !important; width: 250px !important; }
            .sidebar-asesor.open { transform: translateX(0) !important; }
            .sidebar-backdrop.active { display: block; }
            .main-content-wrap { margin-left: 0 !important; }
            .btn-sidebar-toggle { display: inline-flex; }

            /* Perfectly matched topbar and page body padding */
            .portal-topbar { margin: 12px 16px 0; padding: 9px 16px; border-radius: 14px; }
            .portal-page-body { padding: 14px 16px 36px; }

            .hero-action-card { width: 100%; min-width: 0; margin-top: 10px; }
        }

        @media (max-width: 768px) {
            .portal-topbar { 
                margin: 10px 12px 0; 
                padding: 8px 12px; 
                top: 10px; 
                border-radius: 12px; 
            }
            .portal-page-body { padding: 12px 12px 30px; }

            .green-hero-banner { padding: 16px 16px; border-radius: 14px; }
            .hero-heading { font-size: 1.12rem; }
            .banner-penilaian-green { padding: 14px 16px; border-radius: 14px; }
            .banner-green-progress-box { width: 100%; min-width: 0; margin-top: 8px; }

            .executive-card { border-radius: 14px; }
            .ind-card { padding: 14px 14px; border-radius: 14px; }
            .komp-progress-grid { grid-template-columns: 1fr; gap: 10px; }
            .stat-card-row { grid-template-columns: repeat(2, 1fr); gap: 10px; }
        }

        @media (max-width: 480px) {
            .portal-topbar { 
                margin: 8px 8px 0; 
                padding: 8px 10px; 
                top: 8px; 
                border-radius: 10px; 
            }
            .portal-page-body { padding: 10px 8px 24px; }

            .stat-card-row { grid-template-columns: repeat(2, 1fr); gap: 8px; }
            .stat-card-box { padding: 10px 8px !important; gap: 8px !important; border-radius: 12px !important; }
            .stat-card-val { font-size: 1.05rem !important; }
            .stat-card-lbl { font-size: 0.58rem !important; }
            .stat-icon-wrap { width: 32px !important; height: 32px !important; border-radius: 8px !important; }

            .score-option-btn { min-height: 42px; padding: 4px 1px; }
            .score-option-btn .opt-num { font-size: 0.95rem; }
            .score-option-btn .opt-desc { font-size: 0.52rem; }
        }
    </style>
</head>
<body>

<div class="app-wrapper">

    <!-- SIDEBAR ASESOR (ULTRA MINIMALIS & COLLAPSIBLE) -->
    <aside class="sidebar-asesor" id="assessorSidebar">
        <!-- Logo & Title -->
        <div class="sidebar-brand">
            <div class="d-flex align-items-center gap-2 brand-wrap" style="min-width:0;">
                <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Logo">
                <div class="brand-text">
                    <div class="brand-title">PORTAL ASESOR</div>
                    <div class="brand-sub">PKKM KEMENAG RI</div>
                </div>
            </div>
            <button type="button" class="btn-collapse-sidebar d-none d-lg-flex" onclick="toggleSidebarCollapse()" title="Kecilkan / Luaskan Sidebar">
                <i data-lucide="chevron-left" id="collapseIcon" style="width:14px;height:14px;"></i>
            </button>
        </div>

        <!-- Asesor Profile Card Minimalis -->
        <div class="sidebar-user">
            <div class="position-relative flex-shrink-0">
                <div class="user-avatar-initials"><?php echo $pgInitial; ?></div>
                <span class="user-status-dot <?php echo !empty($pgTtd) ? 'ready' : 'pending'; ?>" title="<?php echo !empty($pgTtd) ? 'TTD Digital Siap' : 'Belum Setting TTD'; ?>"></span>
            </div>
            <div class="user-text" style="min-width: 0;">
                <span class="user-role-badge"><?php echo ($pgPeran === 'penilai_1') ? 'PENILAI 1 (KETUA)' : 'PENILAI 2 (ANGGOTA)'; ?></span>
                <div class="text-truncate fw-bold text-white" style="font-size: 0.8rem;" title="<?php echo htmlspecialchars($pgNama); ?>">
                    <?php echo htmlspecialchars($pgNama); ?>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <div class="sidebar-nav">
            <div class="nav-section-title">Navigasi Utama</div>

            <!-- Tab Dashboard -->
            <button type="button" class="side-nav-btn <?php echo ($curTab === 'dashboard') ? 'active' : ''; ?>" onclick="switchTab('dashboard')" title="Dashboard Evaluasi">
                <div class="nav-left">
                    <i data-lucide="layout-dashboard" style="width:16px;height:16px;"></i>
                    <span>Dashboard</span>
                </div>
            </button>

            <!-- Tab Lembar Penilaian -->
            <button type="button" class="side-nav-btn <?php echo ($curTab === 'penilaian') ? 'active' : ''; ?>" onclick="switchTab('penilaian')" title="Lembar Penilaian (42 Indikator)">
                <div class="nav-left">
                    <i data-lucide="clipboard-check" style="width:16px;height:16px;"></i>
                    <span>Penilaian</span>
                </div>
                <span class="pill-badge" id="sideDinilaiBadge"><?php echo $totalDinilai; ?>/42</span>
            </button>

            <!-- Tab Setting TTD Digital -->
            <button type="button" class="side-nav-btn <?php echo ($curTab === 'ttd') ? 'active' : ''; ?>" onclick="switchTab('ttd')" title="Setting TTD Digital">
                <div class="nav-left">
                    <i data-lucide="pen-tool" style="width:16px;height:16px;"></i>
                    <span>Setting TTD</span>
                </div>
                <?php if (!empty($pgTtd)): ?>
                    <i data-lucide="check" style="width:13px;height:13px;color:#38bdf8;" class="nav-badge-icon"></i>
                <?php else: ?>
                    <span class="badge nav-badge-icon" style="font-size:0.6rem; background: rgba(245, 158, 11, 0.2); color: #fcd34d; border: 1px solid rgba(245, 158, 11, 0.3);">Atur</span>
                <?php endif; ?>
            </button>

            <div class="nav-section-title">Laporan & Output</div>

            <!-- Cetak Rekap BAP (Buka di Tab Baru) -->
            <a href="/pengawas/pkkm/cetak-rekap" target="_blank" class="side-nav-btn" title="Cetak Berita Acara & Nilai">
                <div class="nav-left">
                    <i data-lucide="printer" style="width:16px;height:16px;"></i>
                    <span>Cetak BAP & Nilai</span>
                </div>
                <i data-lucide="external-link" style="width:12px;height:12px;opacity:0.6;" class="nav-badge-icon"></i>
            </a>

            <!-- Keluar Portal with SweetAlert2 Confirmation -->
            <button type="button" class="side-nav-btn" style="color: #fca5a5; margin-top: 8px;" onclick="confirmLogout()" title="Keluar Portal">
                <div class="nav-left">
                    <i data-lucide="log-out" style="width:16px;height:16px;color:#f87171;"></i>
                    <span>Keluar Portal</span>
                </div>
            </button>
        </div>

        <!-- Sidebar Minimal Footer -->
        <div class="sidebar-footer">
            <button type="button" class="btn-sidebar-collapse-footer d-none d-lg-flex" onclick="toggleSidebarCollapse()" title="Kecilkan / Luaskan Sidebar">
                <i data-lucide="panel-left-close" id="footerCollapseIcon" style="width:14px;height:14px;"></i>
                <span class="sidebar-footer-text">Minimalisir Menu</span>
            </button>
            <div class="sidebar-footer-info d-lg-none">
                <div class="fw-bold mb-0.5 text-white" style="font-size:0.72rem;"><?php echo htmlspecialchars($sekolah_nama); ?></div>
                <div style="font-size:0.66rem; color:rgba(255,255,255,0.6);">NSM: <?php echo htmlspecialchars($nsm); ?></div>
            </div>
        </div>
    </aside>

    <!-- Backdrop untuk Mobile -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()"></div>

    <!-- MAIN WRAPPER -->
    <div class="main-content-wrap">

        <!-- TOPBAR EXECUTIVE MODERN (FLOATING) -->
        <header class="portal-topbar">
            <div class="d-flex align-items-center gap-2.5">
                <div class="topbar-page-icon d-flex">
                    <i data-lucide="<?php echo ($curTab === 'penilaian') ? 'clipboard-check' : (($curTab === 'ttd') ? 'pen-tool' : 'layout-dashboard'); ?>" id="topbarPageIcon" style="width:16px;height:16px;"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark" id="topbarTitle" style="font-size: 0.94rem; letter-spacing: -0.2px;">
                        <?php 
                        if ($curTab === 'penilaian') echo 'Lembar Penilaian PKKM (42 Indikator)';
                        elseif ($curTab === 'ttd') echo 'Pengaturan Tanda Tangan Digital Asesor';
                        else echo 'Dashboard Evaluasi Asesor PKKM';
                        ?>
                    </h5>
                    <div class="topbar-sub-badges">
                        <span class="badge-school-meta"><i data-lucide="building" style="width:12px;height:12px;color:#0284c7;"></i> <?php echo htmlspecialchars($sekolah_nama); ?></span>
                        <span class="badge-tp-meta">TP 2026/2027</span>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- KPI Skor Badges Executive Capsule -->
                <div class="topbar-kpi-pill d-none d-sm-flex">
                    <i data-lucide="check-circle-2" style="width:13px;height:13px;color:#0284c7;"></i>
                    <span class="topbar-kpi-lbl">Dinilai:</span>
                    <span class="topbar-kpi-val" id="topDinilai"><?php echo $totalDinilai; ?> <span style="font-size:0.68rem;color:#94a3b8;font-weight:600;">/ 42</span></span>
                </div>
                <div class="topbar-kpi-pill emerald-glow d-none d-sm-flex">
                    <i data-lucide="activity" style="width:13px;height:13px;color:#0284c7;"></i>
                    <span class="topbar-kpi-lbl" style="color:#0369a1;">NKKM:</span>
                    <span class="topbar-kpi-val text-success" id="topPersen"><?php echo $persentase; ?>%</span>
                </div>
                <div class="d-none d-md-block">
                    <span id="topPredikat" class="badge <?php echo $badgeClass; ?> fw-bold px-2.5 py-1 rounded-pill" style="font-size: 0.72rem; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                        <?php echo $predikat; ?>
                    </span>
                </div>

                <!-- Tombol Cetak BAP -->
                <a href="/pengawas/pkkm/cetak-rekap" target="_blank" class="btn-topbar-print ms-1" title="Cetak Berita Acara & Rekapitulasi Nilai">
                    <i data-lucide="printer" style="width:13px;height:13px;color:#0284c7;"></i>
                    <span class="d-none d-md-inline">Cetak Rekap</span>
                </a>

                <!-- Mini Asesor Avatar on Topbar -->
                <div class="d-none d-lg-flex align-items-center gap-2 ms-2 ps-2 border-start">
                    <div class="topbar-user-avatar" title="<?php echo htmlspecialchars($pgNama); ?>">
                        <?php echo $pgInitial; ?>
                    </div>
                </div>
            </div>
        </header>

        <!-- PAGE BODY CONTAINER -->
        <div class="portal-page-body">

            <!-- ============================================================== -->
            <!-- TAB 1: DASHBOARD EVALUASI -->
            <!-- ============================================================== -->
            <div id="tab-dashboard" class="tab-content-panel" style="<?php echo ($curTab === 'dashboard') ? 'display:block;' : 'display:none;'; ?>">
                
                <!-- GREEN HERO BANNER (PRESTIGIOUS SULTAN GLASS-EMERALD CARD) -->
                <div class="green-hero-banner">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div style="max-width: 660px;">
                            <div class="hero-tag">
                                <span class="live-pulse-dot"></span>
                                <span>PORTAL ASESOR PKKM &bull; KEMENAG RI</span>
                            </div>
                            <h3 class="hero-heading">
                                Penilaian Kinerja Kepala Madrasah (PKKM)
                            </h3>
                            <p class="hero-subtext">
                                Instrumen Evaluasi Kinerja & Validasi Bukti Fisik 4 Tahunan &bull; Standar Juknis Dirjen Pendis
                            </p>
                            <div class="hero-meta-bar">
                                <div class="hero-meta-item">
                                    <i data-lucide="user-check" style="width:13px;height:13px;color:#38bdf8;"></i>
                                    <span>Asesor: <strong><?php echo htmlspecialchars($pgNama); ?></strong></span>
                                </div>
                                <div class="hero-meta-divider"></div>
                                <div class="hero-meta-item">
                                    <i data-lucide="award" style="width:13px;height:13px;color:#7dd3fc;"></i>
                                    <span class="badge-role-hero"><?php echo ($pgPeran === 'penilai_1') ? 'Ketua Tim (Penilai 1)' : 'Anggota Tim (Penilai 2)'; ?></span>
                                </div>
                                <div class="hero-meta-divider"></div>
                                <div class="hero-meta-item">
                                    <i data-lucide="building" style="width:13px;height:13px;color:#bae6fd;"></i>
                                    <span><?php echo htmlspecialchars($sekolah_nama); ?></span>
                                </div>
                                <div class="hero-meta-divider"></div>
                                <?php if (!empty($pgTtd)): ?>
                                    <div class="hero-meta-item ttd-badge-ok">
                                        <i data-lucide="shield-check" style="width:13px;height:13px;color:#38bdf8;"></i>
                                        <span>TTD Siap</span>
                                    </div>
                                <?php else: ?>
                                    <div class="hero-meta-item ttd-badge-warn" onclick="switchTab('ttd')" style="cursor:pointer;" title="Klik untuk atur TTD">
                                        <i data-lucide="alert-circle" style="width:13px;height:13px;color:#fde047;"></i>
                                        <span>TTD Belum Diset</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- RIGHT COMMAND CARD -->
                        <div class="hero-action-card">
                            <div class="d-flex align-items-center justify-content-between mb-1.5">
                                <span class="hero-card-title d-flex align-items-center gap-1.5">
                                    <i data-lucide="sparkles" style="width:13px;height:13px;color:#38bdf8;"></i>
                                    Progress Penilaian
                                </span>
                                <span class="hero-card-pct" id="heroProgressPct"><?php echo round(($totalDinilai / 42) * 100); ?>%</span>
                            </div>
                            <div class="hero-card-progress">
                                <div class="hero-card-bar" id="heroProgressBar" style="width: <?php echo ($totalDinilai / 42) * 100; ?>%;"></div>
                            </div>
                            <div class="hero-card-actions">
                                <button type="button" class="btn-hero-primary" onclick="switchTab('penilaian')" title="Buka Lembar Penilaian PKKM">
                                    <i data-lucide="clipboard-check" style="width:15px;height:15px;"></i>
                                    <span>Buka Penilaian</span>
                                    <span class="badge-count" id="heroBadgeCount"><?php echo $totalDinilai; ?>/42</span>
                                </button>
                                <button type="button" class="btn-hero-secondary" onclick="switchTab('ttd')" title="Atur Tanda Tangan Digital">
                                    <i data-lucide="pen-tool" style="width:14px;height:14px;"></i>
                                    <span>Setting TTD</span>
                                    <?php if (!empty($pgTtd)): ?>
                                        <i data-lucide="check" style="width:13px;height:13px;color:#38bdf8;"></i>
                                    <?php endif; ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4 METRIK CARDS MINIMALIS -->
                <div class="stat-card-row">
                    <div class="stat-card-box emerald">
                        <div class="stat-icon-wrap"><i data-lucide="check-circle-2" style="width:18px;height:18px;"></i></div>
                        <div>
                            <div class="stat-card-val" id="dashDinilaiVal"><?php echo $totalDinilai; ?> <span style="font-size:0.82rem;color:#94a3b8;font-weight:600;">/ 42</span></div>
                            <div class="stat-card-lbl">Indikator Selesai</div>
                        </div>
                    </div>

                    <div class="stat-card-box blue">
                        <div class="stat-icon-wrap"><i data-lucide="percent" style="width:18px;height:18px;"></i></div>
                        <div>
                            <div class="stat-card-val" id="dashNkkmVal"><?php echo $persentase; ?>%</div>
                            <div class="stat-card-lbl">Nilai Kinerja (NKKM)</div>
                        </div>
                    </div>

                    <div class="stat-card-box purple">
                        <div class="stat-icon-wrap"><i data-lucide="award" style="width:18px;height:18px;"></i></div>
                        <div>
                            <div class="stat-card-val" id="dashPredikatVal" style="font-size: 1.15rem;"><?php echo $predikat; ?></div>
                            <div class="stat-card-lbl">Predikat Capaian</div>
                        </div>
                    </div>

                    <div class="stat-card-box amber">
                        <div class="stat-icon-wrap"><i data-lucide="file-check" style="width:18px;height:18px;"></i></div>
                        <div>
                            <div class="stat-card-val">42 Butir</div>
                            <div class="stat-card-lbl">Standar Juknis 1, 2, 3</div>
                        </div>
                    </div>
                </div>

                <!-- CAPAIAN EVALUASI BERDASARKAN 4 KOMPONEN (EXECUTIVE CONTAINER CARD) -->
                <div class="executive-card p-3 p-md-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2.5 border-bottom">
                        <div>
                            <h6 class="fw-bold text-dark mb-0.5 d-flex align-items-center gap-2" style="font-size: 0.95rem;">
                                <i data-lucide="bar-chart-2" style="width:18px;height:18px;color:#0284c7;"></i>
                                Capaian Evaluasi Berdasarkan 4 Komponen
                            </h6>
                            <div style="font-size: 0.74rem; color: #64748b;">
                                Distribusi kelengkapan butir indikator dan perolehan skor per komponen PKKM
                            </div>
                        </div>
                        <span class="badge bg-success-subtle text-success fw-bold d-inline-flex align-items-center gap-1.5" style="font-size: 0.72rem; padding: 4px 9px; border-radius: 999px;">
                            <i data-lucide="mouse-pointer-click" style="width:12px;height:12px;"></i> Klik kartu untuk menilai
                        </span>
                    </div>

                    <div class="komp-progress-grid">
                        <?php foreach ($komponen_stats as $ks): ?>
                        <div class="komp-card" onclick="goToKomponen(<?php echo $ks['id']; ?>)" title="Klik untuk menilai Komponen <?php echo $ks['urutan']; ?>">
                            <div class="komp-card-header">
                                <div class="komp-num-pill"><?php echo $ks['urutan']; ?></div>
                                <div style="min-width:0;">
                                    <h6 class="fw-bold mb-0.5 text-dark" style="font-size:0.88rem;line-height:1.35;">
                                        <?php echo htmlspecialchars($ks['nama']); ?>
                                    </h6>
                                    <div style="font-size: 0.72rem; color: #94a3b8;">
                                        Total: <strong><?php echo $ks['total']; ?> Indikator</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 0.76rem; font-weight: 700;">
                                <span class="text-muted">Kelengkapan:</span>
                                <span class="text-success"><?php echo $ks['dinilai']; ?> / <?php echo $ks['total']; ?></span>
                            </div>

                            <div class="progress mb-2.5" style="height: 6px; border-radius: 999px; background: #e2e8f0;">
                                <div class="progress-bar bg-success" style="width: <?php echo ($ks['total'] > 0) ? round(($ks['dinilai'] / $ks['total']) * 100) : 0; ?>%;"></div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-2 border-top" style="font-size: 0.76rem;">
                                <span class="text-muted">Skor Perolehan:</span>
                                <span class="fw-bold text-dark"><?php echo $ks['skor']; ?> / <?php echo $ks['max']; ?> (<?php echo $ks['persen']; ?>%)</span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- TIM ASESOR & TANDA TANGAN SECTION -->
                <div class="row g-3 mb-4">
                    <div class="col-lg-7">
                        <div class="executive-card p-3 p-md-4 h-100">
                            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2" style="font-size: 0.95rem;">
                                <i data-lucide="users" style="width:18px;height:18px;color:#2563eb;"></i>
                                Tim Asesor / Pengawas Kemenag
                            </h6>

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-3" style="font-size: 0.82rem; border-color: #f1f5f9;">
                                    <thead style="background: #f8fafc;">
                                        <tr>
                                            <th>Peran</th>
                                            <th>Nama & NIP Asesor</th>
                                            <th>Tanda Tangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tim_penilai as $tp): ?>
                                        <tr>
                                            <td>
                                                <span class="badge <?php echo ($tp['peran'] === 'penilai_1') ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary'; ?> fw-bold">
                                                    <?php echo ($tp['peran'] === 'penilai_1') ? 'Penilai 1 (Ketua)' : 'Penilai 2 (Anggota)'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($tp['nama']); ?></div>
                                                <small class="text-muted">NIP: <?php echo htmlspecialchars($tp['nip']); ?></small>
                                            </td>
                                            <td>
                                                <?php if (!empty($tp['ttd_digital'])): ?>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <img src="<?php echo htmlspecialchars($tp['ttd_digital']); ?>" style="max-height: 28px; max-width: 75px; object-fit: contain; border: 1px solid #e2e8f0; border-radius: 4px; padding: 2px;" alt="TTD">
                                                        <span class="text-success fw-bold" style="font-size: 0.72rem;">✓ Terpasang</span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="badge bg-warning-subtle text-warning-emphasis fw-bold">Belum Diatur</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="alert alert-info rounded-3 py-2 px-3 mb-0 d-flex align-items-center gap-2" style="font-size: 0.78rem; background: #f0f9ff; border-color: #ccfbf1; color: #0f766e;">
                                <i data-lucide="info" style="width:15px;height:15px;flex-shrink:0;"></i>
                                <div>Tanda tangan digital otomatis terpasang pada lembar Berita Acara & Rekap Nilai resmi PKKM.</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="executive-card p-3 p-md-4 h-100">
                            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2" style="font-size: 0.95rem;">
                                <i data-lucide="building" style="width:18px;height:18px;color:#0284c7;"></i>
                                Satuan Pendidikan yang Dinilai
                            </h6>

                            <div class="p-3 rounded-3 mb-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                                <h6 class="fw-bold text-dark mb-1" style="font-size:0.9rem;"><?php echo htmlspecialchars($sekolah_nama); ?></h6>
                                <div style="font-size: 0.76rem; color: #64748b;">
                                    NSM: <strong><?php echo htmlspecialchars($nsm); ?></strong> &bull; NPSN: <strong><?php echo htmlspecialchars($npsn); ?></strong>
                                </div>
                                <div class="mt-2 pt-2 border-top" style="font-size: 0.78rem;">
                                    Kepala Madrasah: <strong><?php echo htmlspecialchars($kepala_nama); ?></strong><br>
                                    <small class="text-muted">NIP: <?php echo htmlspecialchars($kepala_nip); ?></small>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-primary fw-bold rounded-3 py-2" style="background:#0284c7; border:none;" onclick="switchTab('penilaian')">
                                    <i data-lucide="edit-3" style="width:15px;height:15px;vertical-align:middle;"></i> Lanjut Penilaian (42 Butir)
                                </button>
                                <a href="/pengawas/pkkm/cetak-rekap" target="_blank" class="btn btn-outline-secondary fw-bold rounded-3 py-2">
                                    <i data-lucide="file-text" style="width:15px;height:15px;vertical-align:middle;"></i> Pratinjau Berita Acara (BAP)
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ============================================================== -->
            <!-- TAB 2: LEMBAR PENILAIAN (42 INDIKATOR) -->
            <!-- ============================================================== -->
            <div id="tab-penilaian" class="tab-content-panel" style="<?php echo ($curTab === 'penilaian') ? 'display:block;' : 'display:none;'; ?>">
                
                <!-- GREEN BANNER TARGET EVALUASI (TAB PENILAIAN) -->
                <div class="banner-penilaian-green">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="banner-green-icon">
                                <i data-lucide="clipboard-check" style="width:22px;height:22px;"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="banner-tag">
                                        <i data-lucide="target" style="width:11px;height:11px;"></i> TARGET EVALUASI PKKM
                                    </span>
                                    <span class="banner-tag-sub">42 INDIKATOR JUKNIS</span>
                                </div>
                                <h5 class="fw-bold text-white mb-1" style="font-size: 1.05rem; letter-spacing: -0.2px;">
                                    <?php echo htmlspecialchars($sekolah_nama); ?>
                                </h5>
                                <div class="d-flex align-items-center gap-2 flex-wrap" style="font-size: 0.76rem; color: rgba(255,255,255,0.85);">
                                    <span>Kepala Madrasah: <strong class="text-white"><?php echo htmlspecialchars($kepala_nama); ?></strong></span>
                                    <span>&bull;</span>
                                    <span>NIP: <?php echo htmlspecialchars($kepala_nip); ?></span>
                                    <span>&bull;</span>
                                    <span>NSM: <?php echo htmlspecialchars($nsm); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Mini Progress Box on Right of Banner -->
                        <div class="banner-green-progress-box">
                            <div class="d-flex justify-content-between align-items-center mb-1.5" style="font-size: 0.76rem; font-weight: 700;">
                                <span style="color: rgba(255,255,255,0.9);"><i data-lucide="sparkles" style="width:12px;height:12px;color:#38bdf8;"></i> Kelengkapan:</span>
                                <span id="lbl_progress_pct" class="text-white fw-bold" style="font-size: 0.84rem;"><?php echo round(($totalDinilai / 42) * 100); ?>%</span>
                            </div>
                            <div class="progress" style="height: 7px; border-radius: 999px; background: rgba(0,0,0,0.25);">
                                <div id="bar_eval_progress" class="progress-bar bg-success" style="width: <?php echo ($totalDinilai / 42) * 100; ?>%; box-shadow: 0 0 8px rgba(52, 211, 153, 0.6);"></div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2 pt-1.5" style="border-top: 1px dashed rgba(255,255,255,0.2);">
                                <button type="button" class="btn btn-sm btn-outline-danger text-white py-0.5 px-2" style="font-size:0.68rem; border-color:rgba(239,68,68,0.6); background:rgba(239,68,68,0.22); border-radius:6px;" onclick="confirmResetNilai()" title="Reset semua skor kembali ke 0">
                                    <i data-lucide="rotate-ccw" style="width:11px;height:11px;vertical-align:middle;"></i> Reset Nilai
                                </button>
                                <div style="font-size: 0.72rem; color: rgba(255,255,255,0.85);">
                                    <span id="lbl_dinilai_count"><?php echo $totalDinilai; ?></span> dari 42 Dinilai
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Filter Komponen (Per-Komponen) -->
                <div class="nav-tabs-komp">
                    <?php 
                    $firstKompId = !empty($komponens) ? $komponens[0]['id'] : 1;
                    foreach ($komponens as $idx => $k): 
                        $namaKomp = $k['nama'] ?? ($k['nama_komponen'] ?? ('Komponen ' . ($k['urutan'] ?? ''))); 
                        $isActive = ($idx === 0);
                    ?>
                        <button class="tab-komp-btn <?php echo $isActive ? 'active' : ''; ?>" onclick="filterKomponen(<?php echo $k['id']; ?>, this)">
                            <span class="badge bg-secondary-subtle text-dark" style="font-size:0.68rem;"><?php echo $k['urutan']; ?></span>
                            <?php echo htmlspecialchars($namaKomp); ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <!-- Indikator Cards List -->
                <div id="indicator_list_container">
                    <?php 
                    $lastSubKompId = null;
                    foreach ($indikators as $ind): 
                        $isScored = !empty($ind['skor_penilai']);
                        $kompId = $ind['komp_id'] ?? ($ind['komponen_id'] ?? 1);
                        $subKompId = $ind['sub_komponen_id'] ?? 0;
                        $isInitialHidden = ($kompId != $firstKompId);
                    ?>
                        <?php if ($subKompId !== $lastSubKompId && !empty($ind['sub_nama'])): ?>
                            <div class="ind-card sub-komp-header-divider" data-komp="<?php echo $kompId; ?>" style="<?php echo $isInitialHidden ? 'display:none;' : 'display:block;'; ?> background: linear-gradient(135deg, #f0f9ff 0%, #ecfeff 100%); border-left: 4.5px solid #0284c7; border-radius: 14px; padding: 12px 18px; margin-bottom: 14px; box-shadow: 0 2px 6px rgba(0,0,0,0.03);">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge" style="background: #0284c7; color: #ffffff; font-size: 0.82rem; font-weight: 800; padding: 4px 10px; border-radius: 8px;"><?php echo htmlspecialchars($ind['sub_urutan'] ?? ''); ?></span>
                                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem; line-height: 1.4;">
                                        <?php echo htmlspecialchars($ind['sub_nama']); ?>
                                    </h6>
                                </div>
                            </div>
                            <?php $lastSubKompId = $subKompId; ?>
                        <?php endif; ?>

                        <div class="ind-card <?php echo $isScored ? 'scored' : ''; ?>" data-komp="<?php echo $kompId; ?>" id="card_ind_<?php echo $ind['id']; ?>" style="<?php echo $isInitialHidden ? 'display:none;' : 'display:block;'; ?>">
                            <div class="row align-items-center">
                                <div class="col-lg-8 col-md-12 mb-3 mb-lg-0">
                                    <div class="d-flex align-items-start gap-2 mb-2">
                                        <span class="ind-num-pill" style="min-width: 48px; text-align: center; font-size: 0.80rem; font-weight: 800; background: #0369a1; color: #fff; padding: 4px 10px; border-radius: 8px;">
                                            <?php echo htmlspecialchars($ind['kode_lengkap'] ?? ($kompId . '.' . $ind['urutan'])); ?>
                                        </span>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem; line-height: 1.45;">
                                            <?php echo htmlspecialchars($ind['indikator']); ?>
                                        </h6>
                                    </div>

                                    <div style="font-size: 0.82rem; color: #64748b; margin-bottom: 10px;">
                                        <strong>Kriteria Penilaian:</strong> <?php echo htmlspecialchars($ind['kriteria']); ?>
                                    </div>

                                    <!-- DATA YANG DIHARAPKAN (JUKNIS RESMI PKKM) -->
                                    <?php 
                                        $bf_lines = explode("\n", $ind['bukti_fisik'] ?? '');
                                        $penggalian_data = null;
                                        $expected_items = [];
                                        foreach($bf_lines as $l) {
                                            $lt = trim($l);
                                            if(empty($lt)) continue;
                                            if(stripos($lt, 'Penggalian Data:') === 0) {
                                                $penggalian_data = trim(substr($lt, strlen('Penggalian Data:')));
                                                if (empty($penggalian_data)) $penggalian_data = $lt;
                                            } else {
                                                $expected_items[] = $lt;
                                            }
                                        }
                                    ?>
                                    <?php if(!empty(trim($ind['bukti_fisik'] ?? ''))): ?>
                                    <div class="p-3 rounded-3 mb-2.5" style="background: #f8fafc; border: 1px solid #e2e8f0; font-size: 0.8rem;">
                                        <div class="d-flex justify-content-between align-items-center mb-1.5 flex-wrap gap-1">
                                            <div class="fw-bold d-flex align-items-center gap-1.5" style="color: #2563eb; font-size: 0.8rem;">
                                                <i data-lucide="clipboard-list" style="width:14px;height:14px;"></i>
                                                <span>Data yang Diharapkan (Bukti Fisik Juknis):</span>
                                            </div>
                                            <?php if(!empty($penggalian_data)): ?>
                                                <span class="badge bg-white text-primary border border-primary-subtle" style="font-size: 0.68rem; font-weight: 700; padding: 3px 8px;">
                                                    <i data-lucide="search" style="width:11px;height:11px;vertical-align:middle;"></i> Metode: <?php echo htmlspecialchars($penggalian_data); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div style="color: #334155; line-height: 1.55;">
                                            <?php if(!empty($expected_items)): ?>
                                                <ul class="mb-0 ps-3" style="padding-left: 1.15rem; margin-bottom: 0;">
                                                    <?php foreach($expected_items as $ei): ?>
                                                        <li style="margin-bottom: 3px;">
                                                             <?php echo htmlspecialchars(ltrim($ei, '- ')); ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <div class="text-muted fst-italic"><?php echo nl2br(htmlspecialchars($ind['bukti_fisik'])); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Bukti Fisik & Berkas yang Disiapkan Madrasah -->
                                    <div class="p-3 rounded-3" style="background: #fafafa; border: 1px dashed #cbd5e1; font-size: 0.8rem;">
                                        <div class="fw-bold text-dark mb-2 d-flex align-items-center justify-content-between flex-wrap gap-1" style="font-size: 0.78rem;">
                                            <span class="d-flex align-items-center gap-1.5" style="color: #0284c7;">
                                                <i data-lucide="folder-check" style="width:15px;height:15px;color:#0284c7;"></i>
                                                <span>Dokumen Bukti Fisik yang Disiapkan Madrasah:</span>
                                            </span>
                                            <?php if (!empty($ind['berkas_list'])): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.68rem; font-weight: 700;">
                                                    <?php echo count($ind['berkas_list']); ?> Berkas Terlampir
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="d-flex flex-wrap gap-2 mt-1">
                                            <?php if (!empty($ind['berkas_list'])): ?>
                                                <?php foreach ($ind['berkas_list'] as $bk): ?>
                                                    <a href="<?php echo htmlspecialchars($bk['file_path'] ?? $bk['url']); ?>" target="_blank" class="berkas-pill-link" style="background: #ffffff; border: 1.5px solid #bae6fd; color: #0369a1; font-weight: 700; padding: 6px 12px; border-radius: 8px; display: inline-flex; align-items: center; gap: 7px; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.04); transition: all 0.2s ease;">
                                                        <i data-lucide="<?php echo ($bk['tipe'] ?? '') === 'link' ? 'external-link' : 'file-text'; ?>" style="width:14px;height:14px;color:#0284c7;"></i>
                                                        <span><?php echo htmlspecialchars($bk['nama_file'] ?: 'Berkas Bukti'); ?></span>
                                                    </a>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-muted fst-italic" style="font-size: 0.78rem;">
                                                    Belum ada berkas yang diunggah madrasah untuk butir ini. (Madrasah menyiapkan bukti fisik cetak saat telaah dokumen)
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Scoring Action Panel Rapi & Modern -->
                                <div class="col-lg-4 col-md-12 mt-2 mt-lg-0 d-flex flex-column justify-content-center">
                                    <div class="scoring-box" id="scoring_box_<?php echo $ind['id']; ?>">
                                        <div class="scoring-box-header">
                                            <span class="scoring-label">
                                                <i data-lucide="award" style="width:13px;height:13px;color:#0284c7;"></i>
                                                Skor Evaluasi Asesor
                                                <span class="badge bg-white text-muted border ms-1" style="font-size:0.6rem; cursor:pointer;" onclick="showRubrikModal()" title="Lihat Petunjuk Penskoran Juknis">
                                                    <i data-lucide="help-circle" style="width:10px;height:10px;vertical-align:middle;"></i> Juknis
                                                </span>
                                            </span>
                                            <span class="scoring-status-badge <?php echo $isScored ? 'scored' : 'pending'; ?>" id="status_badge_<?php echo $ind['id']; ?>">
                                                <?php if ($isScored): ?>
                                                    <i data-lucide="check-circle-2" style="width:12px;height:12px;"></i>
                                                    <span>Skor: <?php echo $ind['skor_penilai']; ?></span>
                                                <?php else: ?>
                                                    <i data-lucide="clock" style="width:12px;height:12px;"></i>
                                                    <span>Belum Dinilai</span>
                                                <?php endif; ?>
                                            </span>
                                        </div>

                                        <div class="scoring-options-grid">
                                            <?php 
                                            $optLabels = [
                                                1 => 'Sgt Terbatas', 
                                                2 => 'Kurang Lengkap', 
                                                3 => 'Lengkap & Cukup', 
                                                4 => 'Lengkap & Yakin'
                                            ];
                                            $optTitles = [
                                                1 => 'Bukti Sangat Terbatas & Kurang Meyakinkan / Tidak Ditemukan',
                                                2 => 'Bukti Kurang Lengkap & Cukup Meyakinkan',
                                                3 => 'Bukti Lengkap & Cukup Meyakinkan',
                                                4 => 'Bukti Lengkap & Sangat Meyakinkan'
                                            ];
                                            for ($s = 1; $s <= 4; $s++): 
                                                $isSelected = ($ind['skor_penilai'] == $s);
                                            ?>
                                                <button type="button" 
                                                    class="score-option-btn <?php echo $isSelected ? 'active selected' : ''; ?>" 
                                                    onclick="submitSkor(<?php echo $ind['id']; ?>, <?php echo $s; ?>)"
                                                    title="Skor <?php echo $s; ?>: <?php echo $optTitles[$s]; ?>">
                                                    <span class="opt-num"><?php echo $s; ?></span>
                                                    <span class="opt-desc"><?php echo $optLabels[$s]; ?></span>
                                                </button>
                                            <?php endfor; ?>
                                        </div>

                                        <div class="scoring-box-footer" id="footer_desc_<?php echo $ind['id']; ?>">
                                            <?php if ($isScored): ?>
                                                <?php 
                                                    $skVal = (int)$ind['skor_penilai'];
                                                    $textColor = ($skVal >= 3) ? 'text-success' : (($skVal === 2) ? 'text-warning-emphasis' : 'text-danger');
                                                ?>
                                                <span class="<?php echo $textColor; ?> fw-bold" style="font-size: 0.72rem;">
                                                    <i data-lucide="check" style="width:12px;height:12px;"></i> 
                                                    Skor <?php echo $skVal; ?>: <?php echo $optTitles[$skVal] ?? ''; ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted" style="font-size: 0.71rem;">
                                                    <i data-lucide="info" style="width:11px;height:11px;vertical-align:middle;"></i> 1 (Sgt Terbatas) s.d 4 (Lengkap & Sangat Meyakinkan)
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>

            <!-- ============================================================== -->
            <!-- TAB 3: SETTING TTD DIGITAL ASESOR -->
            <!-- ============================================================== -->
            <div id="tab-ttd" class="tab-content-panel" style="<?php echo ($curTab === 'ttd') ? 'display:block;' : 'display:none;'; ?>">
                
                <div class="row g-3 mb-4">
                    <div class="col-lg-7">
                        <div class="executive-card p-4">
                                <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                                    <i data-lucide="pen-tool" style="width:20px;height:20px;color:#0284c7;"></i>
                                    Atur Tanda Tangan Digital Asesor
                                </h5>
                                <p class="text-muted mb-4" style="font-size: 0.85rem;">
                                    Tanda tangan digital ini akan secara otomatis terpasang pada dokumen cetak <strong>Berita Acara</strong> dan <strong>Rekapitulasi Nilai PKKM</strong>. Anda dapat menggambar tanda tangan langsung di layar atau mengunggah file foto tanda tangan Anda.
                                </p>

                                <form id="formTtd" action="/pengawas/pkkm/simpan-ttd" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="is_ajax" value="1">
                                    <input type="hidden" name="signature_data" id="signatureData">

                                    <!-- PILIHAN METODE TTD -->
                                    <ul class="nav nav-pills mb-3" id="pills-tab-ttd" role="tablist">
                                        <li class="nav-item">
                                            <button class="nav-link active fw-bold py-2 px-3 rounded-3" id="pills-pad-tab" data-bs-toggle="pill" data-bs-target="#pills-pad" type="button">
                                                <i data-lucide="edit-3" style="width:15px;height:15px;vertical-align:middle;"></i> Gores TTD Langsung (Canvas)
                                            </button>
                                        </li>
                                        <li class="nav-item ms-2">
                                            <button class="nav-link fw-bold py-2 px-3 rounded-3" id="pills-file-tab" data-bs-toggle="pill" data-bs-target="#pills-file" type="button">
                                                <i data-lucide="upload" style="width:15px;height:15px;vertical-align:middle;"></i> Unggah File Foto TTD
                                            </button>
                                        </li>
                                    </ul>

                                    <div class="tab-content mb-4" id="pills-tabContent">
                                        <!-- MODE A: CANVAS SIGNATURE PAD -->
                                        <div class="tab-pane fade show active" id="pills-pad">
                                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Goreskan Tanda Tangan Anda di Bawah:</label>
                                            
                                            <div class="signature-pad-wrapper" id="padWrapper">
                                                <canvas id="signatureCanvas" class="signature-canvas" width="500" height="200"></canvas>
                                                <div class="signature-guide-line"></div>
                                                <div class="signature-guide-text">Tanda tangani di area ini (Gunakan Mouse / Stylus / Sentuhan Jari)</div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center mt-2" style="max-width: 520px;">
                                                <small class="text-muted" style="font-size: 0.72rem;">* Bersihkan jika ingin mengulang goresan</small>
                                                <button type="button" class="btn btn-sm btn-outline-danger fw-bold" onclick="confirmClearCanvas()">
                                                    <i data-lucide="rotate-ccw" style="width:13px;height:13px;"></i> Bersihkan Canvas
                                                </button>
                                            </div>
                                        </div>

                                        <!-- MODE B: FILE UPLOAD -->
                                        <div class="tab-pane fade" id="pills-file">
                                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Pilih File Gambar TTD (Format PNG / JPG transparan disarankan):</label>
                                            <input type="file" name="file_ttd" id="fileTtdInput" class="form-control rounded-3" accept="image/png, image/jpeg, image/jpg, image/webp" onchange="previewUploadImage(this)">
                                            <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Ukuran file maksimal 2 MB.</small>
                                            
                                            <div id="fileUploadPreviewWrap" class="mt-3 p-3 bg-light rounded-3 text-center" style="display: none; border: 1px dashed #cbd5e1;">
                                                <div class="text-muted mb-1" style="font-size:0.75rem;">Pratinjau File:</div>
                                                <img id="fileUploadPreviewImg" src="" style="max-height: 80px; max-width: 180px; object-fit: contain;">
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4 text-muted">

                                    <h6 class="fw-bold text-dark mb-3">Identitas & Gelar Pengawas Penilai:</h6>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold" style="font-size: 0.82rem;">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                                        <input type="text" name="nama" id="inputNama" class="form-control rounded-3" value="<?php echo htmlspecialchars($pgNama); ?>" required oninput="updateLivePreview()">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold" style="font-size: 0.82rem;">NIP Pengawas</label>
                                            <input type="text" name="nip" id="inputNip" class="form-control rounded-3" value="<?php echo htmlspecialchars($pgNip); ?>" oninput="updateLivePreview()">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold" style="font-size: 0.82rem;">Jabatan</label>
                                            <input type="text" name="jabatan" id="inputJabatan" class="form-control rounded-3" value="<?php echo htmlspecialchars($pgJabatan); ?>">
                                        </div>
                                    </div>

                                    <div class="mt-4 pt-2">
                                        <button type="submit" id="btnSimpanTtd" class="btn btn-primary fw-bold px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2" style="background:#0284c7; border:none;">
                                            <i data-lucide="check-circle" style="width:18px;height:18px;"></i> Simpan Tanda Tangan Digital
                                        </button>
                                    </div>
                                </form>
                        </div>
                    </div>

                    <!-- PREVIEW HASIL TTD PADA DOKUMEN CETAK -->
                    <div class="col-lg-5">
                        <div class="executive-card p-4 h-100">
                            <h5 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                                <i data-lucide="file-check-2" style="width:20px;height:20px;color:#2563eb;"></i>
                                Pratinjau di Berita Acara (BAP)
                            </h5>
                            <p class="text-muted" style="font-size: 0.8rem;">
                                Berikut simulasi posisi tanda tangan Anda pada dokumen resmi Rekapitulasi Nilai & Berita Acara PKKM Kemenag:
                            </p>

                            <div class="bap-preview-box mx-auto">
                                <div style="font-size: 0.82rem; color: #475569; text-align: center; margin-bottom: 4px;">
                                    Way Tenong, <?php echo date('d F Y'); ?>
                                </div>
                                <div style="font-size: 0.82rem; font-weight: 700; color: #0f172a; text-align: center; margin-bottom: 8px;">
                                    Tim Penilai Kinerja (Pengawas),
                                </div>

                                <div style="height: 75px; display: flex; align-items: center; justify-content: center; margin: 8px 0; border: 1px dashed #e2e8f0; border-radius: 8px; background: #fafafa;">
                                    <img id="livePreviewTtdImg" 
                                         src="<?php echo htmlspecialchars($pgTtd ?: '/public/assets/img/ttd_placeholder.png'); ?>" 
                                         style="<?php echo empty($pgTtd) ? 'display:none;' : ''; ?> max-height: 65px; max-width: 140px; object-fit: contain;" 
                                         alt="TTD Preview">
                                    <span id="livePreviewTtdPlaceholder" style="<?php echo !empty($pgTtd) ? 'display:none;' : ''; ?> font-size: 0.75rem; color: #94a3b8; font-style: italic;">
                                        (Belum ada tanda tangan)
                                    </span>
                                </div>

                                <div style="text-align: center; margin-top: 6px;">
                                    <div class="fw-bold text-dark text-decoration-underline" id="livePreviewNama" style="font-size: 0.88rem;">
                                        <?php echo htmlspecialchars($pgNama); ?>
                                    </div>
                                    <div style="font-size: 0.74rem; color: #64748b;" id="livePreviewNip">
                                        <?php echo ($pgPeran === 'penilai_1') ? 'Penilai 1' : 'Penilai 2'; ?> / NIP. <?php echo htmlspecialchars($pgNip); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <a href="/pengawas/pkkm/cetak-rekap" target="_blank" class="btn btn-outline-primary btn-sm fw-bold px-3 py-2 rounded-3">
                                    <i data-lucide="printer" style="width:14px;height:14px;"></i> Uji Cetak Dokumen PDF Lengkap
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

<!-- TOAST NOTIFICATION -->
<div id="saveToast" class="save-toast">
    <i data-lucide="check-circle" style="width:20px;height:20px;color:#38bdf8;"></i>
    <span id="toastMsg">Skor berhasil disimpan!</span>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// TAB SWITCHER
function switchTab(tabName) {
    document.querySelectorAll('.tab-content-panel').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.side-nav-btn').forEach(btn => btn.classList.remove('active'));

    let targetTab = document.getElementById('tab-' + tabName);
    if (targetTab) {
        targetTab.style.display = 'block';
    }

    // Highlight button in sidebar
    let btns = document.querySelectorAll('.side-nav-btn');
    btns.forEach(btn => {
        if (btn.getAttribute('onclick') && btn.getAttribute('onclick').includes(tabName)) {
            btn.classList.add('active');
        }
    });

    let topbarTitle = document.getElementById('topbarTitle');
    if (tabName === 'dashboard') topbarTitle.innerText = 'Dashboard Evaluasi Asesor PKKM';
    else if (tabName === 'penilaian') topbarTitle.innerText = 'Lembar Penilaian PKKM (42 Indikator)';
    else if (tabName === 'ttd') topbarTitle.innerText = 'Pengaturan Tanda Tangan Digital Asesor';

    let iconWrap = document.getElementById('topbarPageIcon');
    if (iconWrap) {
        let iconName = (tabName === 'penilaian') ? 'clipboard-check' : ((tabName === 'ttd') ? 'pen-tool' : 'layout-dashboard');
        iconWrap.setAttribute('data-lucide', iconName);
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    // Update URL history state without reloading
    window.history.replaceState({}, '', '/pengawas/pkkm?tab=' + tabName);

    // Close mobile sidebar if open
    closeSidebar();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// SIDEBAR TOGGLE HANDLER (RESPONSIVE)
function handleSidebarToggle() {
    if (window.innerWidth >= 993) {
        toggleSidebarCollapse();
    } else {
        toggleSidebarMobile();
    }
}

function toggleSidebarMobile() {
    let sb = document.getElementById('assessorSidebar');
    let bd = document.getElementById('sidebarBackdrop');
    if (sb) sb.classList.toggle('open');
    if (bd) bd.classList.toggle('active');
}

function toggleSidebar() {
    handleSidebarToggle();
}

function closeSidebar() {
    let sb = document.getElementById('assessorSidebar');
    let bd = document.getElementById('sidebarBackdrop');
    if (sb) sb.classList.remove('open');
    if (bd) bd.classList.remove('active');
}

function toggleSidebarCollapse() {
    let app = document.querySelector('.app-wrapper');
    if (!app) return;
    app.classList.toggle('sidebar-collapsed');
    let isCollapsed = app.classList.contains('sidebar-collapsed');
    localStorage.setItem('pkkm_sidebar_collapsed', isCollapsed ? '1' : '0');
    updateCollapseIcons(isCollapsed);
}

function updateCollapseIcons(isCollapsed) {
    let icon = document.getElementById('collapseIcon');
    let fIcon = document.getElementById('footerCollapseIcon');
    if (icon) {
        icon.setAttribute('data-lucide', isCollapsed ? 'chevron-right' : 'chevron-left');
    }
    if (fIcon) {
        fIcon.setAttribute('data-lucide', isCollapsed ? 'panel-left-open' : 'panel-left-close');
    }
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

// FILTER KOMPONEN PADA LEMBAR PENILAIAN
function filterKomponen(kompId, btnEl) {
    document.querySelectorAll('.tab-komp-btn').forEach(b => b.classList.remove('active'));
    if (btnEl) btnEl.classList.add('active');

    let cards = document.querySelectorAll('.ind-card');
    cards.forEach(c => {
        if (kompId === 'all' || c.getAttribute('data-komp') == kompId) {
            c.style.display = 'block';
        } else {
            c.style.display = 'none';
        }
    });
}

// JUMP TO KOMPONEN DARI DASHBOARD
function goToKomponen(kompId) {
    switchTab('penilaian');
    let btns = document.querySelectorAll('.tab-komp-btn');
    let targetBtn = null;
    btns.forEach(b => {
        let oc = b.getAttribute('onclick') || '';
        if (oc.includes(kompId)) targetBtn = b;
    });
    if (targetBtn) {
        filterKomponen(kompId, targetBtn);
    }
}

// REALTIME SUBMIT SKOR DENGAN AJAX
function submitSkor(indikatorId, skorVal) {
    let card = document.getElementById('card_ind_' + indikatorId);
    if (!card) return;

    // Toggle active state on option buttons
    let btns = card.querySelectorAll('.score-option-btn, .score-choice-btn');
    btns.forEach((b, idx) => {
        if (idx + 1 === skorVal) {
            b.classList.add('active');
            b.classList.add('selected');
        } else {
            b.classList.remove('active');
            b.classList.remove('selected');
        }
    });
    card.classList.add('scored');

    let optShort = {
        1: 'Sgt Terbatas',
        2: 'Kurang Lengkap',
        3: 'Lengkap & Cukup',
        4: 'Lengkap & Yakin'
    };
    let optFull = {
        1: 'Bukti Sangat Terbatas & Kurang Meyakinkan / Tidak Ditemukan',
        2: 'Bukti Kurang Lengkap & Cukup Meyakinkan',
        3: 'Bukti Lengkap & Cukup Meyakinkan',
        4: 'Bukti Lengkap & Sangat Meyakinkan'
    };

    // Update Status Badge on Scoring Widget
    let statusBadge = document.getElementById('status_badge_' + indikatorId);
    if (statusBadge) {
        statusBadge.className = 'scoring-status-badge scored';
        statusBadge.innerHTML = '<i data-lucide="check-circle-2" style="width:12px;height:12px;"></i> <span>Skor: ' + skorVal + ' (' + (optShort[skorVal] || '') + ')</span>';
    }

    // Update Footer Text on Scoring Widget
    let footerDesc = document.getElementById('footer_desc_' + indikatorId);
    if (footerDesc) {
        let textCls = (skorVal >= 3) ? 'text-success' : ((skorVal === 2) ? 'text-warning-emphasis' : 'text-danger');
        footerDesc.innerHTML = '<span class="' + textCls + ' fw-bold" style="font-size:0.72rem;"><i data-lucide="check" style="width:12px;height:12px;"></i> Skor ' + skorVal + ': ' + (optFull[skorVal] || '') + '</span>';
    }

    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    let formData = new FormData();
    formData.append('indikator_id', indikatorId);
    formData.append('skor', skorVal);

    fetch('/pengawas/pkkm/nilai', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 1) {
            // Update Topbar and Sidebar Badges
            document.getElementById('topDinilai').innerHTML = data.total_dinilai + ' <span style="font-size:0.8rem;color:#64748b;font-weight:600;">/ 42</span>';
            document.getElementById('topPersen').innerText = data.persentase + '%';
            document.getElementById('sideDinilaiBadge').innerText = data.total_dinilai + '/42';

            if (document.getElementById('lbl_dinilai_count')) {
                document.getElementById('lbl_dinilai_count').innerText = data.total_dinilai;
            }

            if (document.getElementById('dashDinilaiVal')) {
                document.getElementById('dashDinilaiVal').innerHTML = data.total_dinilai + ' <span style="font-size:1rem;color:#94a3b8;font-weight:600;">/ 42</span>';
                document.getElementById('dashNkkmVal').innerText = data.persentase + '%';
            }

            let pct = Math.round((data.total_dinilai / 42) * 100);
            if (document.getElementById('lbl_progress_pct')) document.getElementById('lbl_progress_pct').innerText = pct + '%';
            if (document.getElementById('bar_eval_progress')) document.getElementById('bar_eval_progress').style.width = pct + '%';
            if (document.getElementById('heroProgressPct')) document.getElementById('heroProgressPct').innerText = pct + '%';
            if (document.getElementById('heroProgressBar')) document.getElementById('heroProgressBar').style.width = pct + '%';
            if (document.getElementById('heroBadgeCount')) document.getElementById('heroBadgeCount').innerText = data.total_dinilai + '/42';

            let predikatText = 'Kurang';
            let badgeClass = 'badge bg-danger fw-bold';
            if (data.persentase >= 91) {
                predikatText = 'Amat Baik (A)';
                badgeClass = 'badge bg-success fw-bold';
            } else if (data.persentase >= 76) {
                predikatText = 'Baik (B)';
                badgeClass = 'badge bg-primary fw-bold';
            } else if (data.persentase >= 61) {
                predikatText = 'Cukup (C)';
                badgeClass = 'badge bg-warning text-dark fw-bold';
            } else if (data.persentase >= 51) {
                predikatText = 'Sedang (D)';
                badgeClass = 'badge bg-secondary fw-bold';
            }

            let topPredikat = document.getElementById('topPredikat');
            if (topPredikat) {
                topPredikat.innerText = predikatText;
                topPredikat.className = badgeClass;
            }

            if (document.getElementById('dashPredikatVal')) {
                document.getElementById('dashPredikatVal').innerText = predikatText;
            }

            showToast('Skor ' + skorVal + ' (' + (optShort[skorVal] || '') + ') berhasil disimpan!');
        }
    })
    .catch(err => {
        console.error(err);
    });
}

// MODAL RUBRIK RESMI JUKNIS PKKM KEMENAG
function showRubrikModal() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: '<div class="d-flex align-items-center justify-content-center gap-2" style="font-size:1.1rem;color:#0369a1;"><i class="bi bi-journal-bookmark-fill text-success"></i> <span>Pedoman Penskoran Resmi PKKM</span></div>',
            html: `
                <div class="text-start" style="font-size: 0.85rem; line-height: 1.5;">
                    <div class="alert alert-success py-2 px-3 mb-3 border-0 rounded-3 text-dark fw-medium" style="background:#f0f9ff;font-size:0.8rem;">
                        <i class="bi bi-info-circle-fill text-success me-1"></i> Standar <strong>Petunjuk Teknis (Juknis) Penilaian Kinerja Kepala Madrasah (PKKM) Dirjen Pendis Kemenag RI</strong>:
                    </div>

                    <div class="d-flex flex-column gap-2">
                        <div class="p-2.5 px-3 rounded-3 border-start border-4 border-success bg-light d-flex gap-2.5 align-items-start">
                            <span class="badge bg-success fs-6 px-2 py-1">Skor 4</span>
                            <div>
                                <strong class="text-success d-block mb-1">LENGKAP & SANGAT MEYAKINKAN</strong>
                                <span class="text-muted" style="font-size:0.8rem;">Bukti-bukti yang teridentifikasi secara nyata <b>lengkap dan sangat meyakinkan</b> bahwa kepala madrasah berkinerja sesuai dengan masing-masing indikator.</span>
                            </div>
                        </div>

                        <div class="p-2.5 px-3 rounded-3 border-start border-4 border-primary bg-light d-flex gap-2.5 align-items-start">
                            <span class="badge bg-primary fs-6 px-2 py-1">Skor 3</span>
                            <div>
                                <strong class="text-primary d-block mb-1">LENGKAP & CUKUP MEYAKINKAN</strong>
                                <span class="text-muted" style="font-size:0.8rem;">Bukti-bukti yang teridentifikasi secara nyata <b>lengkap dan cukup meyakinkan</b> bahwa kepala madrasah berkinerja sesuai dengan masing-masing indikator.</span>
                            </div>
                        </div>

                        <div class="p-2.5 px-3 rounded-3 border-start border-4 border-warning bg-light d-flex gap-2.5 align-items-start">
                            <span class="badge bg-warning text-dark fs-6 px-2 py-1">Skor 2</span>
                            <div>
                                <strong class="text-warning-emphasis d-block mb-1">KURANG LENGKAP & CUKUP MEYAKINKAN</strong>
                                <span class="text-muted" style="font-size:0.8rem;">Bukti-bukti yang teridentifikasi secara nyata <b>kurang lengkap dan cukup meyakinkan</b> bahwa kepala madrasah berkinerja sesuai dengan masing-masing indikator.</span>
                            </div>
                        </div>

                        <div class="p-2.5 px-3 rounded-3 border-start border-4 border-danger bg-light d-flex gap-2.5 align-items-start">
                            <span class="badge bg-danger fs-6 px-2 py-1">Skor 1</span>
                            <div>
                                <strong class="text-danger d-block mb-1">SANGAT TERBATAS & KURANG MEYAKINKAN</strong>
                                <span class="text-muted" style="font-size:0.8rem;">Bukti-bukti yang teridentifikasi secara nyata <b>sangat terbatas dan kurang meyakinkan atau tidak ditemukan bukti fisik</b> yang memperkuat kinerja kepala madrasah.</span>
                            </div>
                        </div>
                    </div>
                </div>
            `,
            confirmButtonText: 'Tutup Panduan',
            confirmButtonColor: '#0284c7',
            customClass: {
                popup: 'rounded-4 shadow-lg border-0 p-3'
            },
            width: '600px'
        });
    }
}

// SWEETALERT2 CONFIRM RESET NILAI
function confirmResetNilai() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Reset Seluruh Nilai Penilaian?',
            html: `
                <div class="text-start" style="font-size:0.86rem;">
                    <p class="text-muted mb-2">Seluruh nilai indikator yang telah diisi pada simulasi/uji coba ini akan <b>dikosongkan kembali ke 0</b>.</p>
                    <div class="alert alert-warning py-2 px-3 border-0 rounded-3 text-dark mb-0" style="background:#fef3c7;font-size:0.8rem;">
                        <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Data skor pada tabel <code>pkkm_penilai_skor</code> dan <code>pkkm_nilai</code> akan dihapus bersih.
                    </div>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="bi bi-arrow-counterclockwise me-1"></i> Ya, Reset Semua Nilai',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-4 shadow-lg border-0'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Mereset Nilai...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                let formData = new FormData();
                formData.append('reset_all', '1');

                fetch('/pengawas/pkkm/reset-nilai', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 1) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Direset!',
                            text: data.pesan || 'Semua nilai PKKM telah direset ke 0.',
                            timer: 1800,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Mereset',
                            text: data.pesan || 'Terjadi kesalahan sistem.'
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Jaringan',
                        text: 'Gagal menghubungi server untuk reset nilai.'
                    });
                });
            }
        });
    } else {
        if (confirm('Reset seluruh nilai PKKM ke 0? Tindakan ini tidak dapat dibatalkan.')) {
            let formData = new FormData();
            formData.append('reset_all', '1');
            fetch('/pengawas/pkkm/reset-nilai', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(() => window.location.reload());
        }
    }
}

function showToast(msg) {
    if (typeof Swal !== 'undefined') {
        Swal.mixin({
            toast: true,
            position: 'bottom-end',
            showConfirmButton: false,
            timer: 1600,
            timerProgressBar: true,
            background: '#0369a1',
            color: '#ffffff',
            iconColor: '#38bdf8',
            customClass: {
                popup: 'rounded-3 shadow-lg border-0'
            }
        }).fire({
            icon: 'success',
            title: msg
        });
    } else {
        let t = document.getElementById('saveToast');
        if (t) {
            document.getElementById('toastMsg').innerText = msg;
            t.style.display = 'flex';
            setTimeout(() => { t.style.display = 'none'; }, 2000);
        }
    }
}

// MODERN SWEETALERT2 LOGOUT DIALOG
function confirmLogout() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Keluar dari Portal?',
            text: 'Semua penilaian dan data tanda tangan Anda tersimpan otomatis di sistem.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Keluar Portal',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-4 shadow-lg border-0'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '/logout';
            }
        });
    } else {
        if (confirm('Keluar dari Portal Pengawas?')) {
            window.location.href = '/logout';
        }
    }
}

// CONFIRM CLEAR CANVAS
function confirmClearCanvas() {
    if (!hasDrawn) {
        clearCanvas();
        return;
    }
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Kosongkan Kanvas?',
            text: 'Goresan tanda tangan yang belum disimpan akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Bersihkan',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-4 shadow-lg border-0'
            }
        }).then((res) => {
            if (res.isConfirmed) {
                clearCanvas();
                Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500
                }).fire({
                    icon: 'info',
                    title: 'Kanvas telah dikosongkan'
                });
            }
        });
    } else {
        clearCanvas();
    }
}

// ==============================================================
// SIGNATURE PAD (CANVAS) ENGINE
// ==============================================================
let canvas = document.getElementById('signatureCanvas');
let ctx = canvas.getContext('2d');
let isDrawing = false;
let hasDrawn = false;

// Setup High DPI Canvas
function initCanvas() {
    let rect = canvas.getBoundingClientRect();
    let dpr = window.devicePixelRatio || 1;
    canvas.width = rect.width * dpr;
    canvas.height = rect.height * dpr;
    ctx.scale(dpr, dpr);
    ctx.lineWidth = 2.5;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.strokeStyle = '#0f172a'; // Tinta pena hitam-biru elegan
}

function getPos(e) {
    let rect = canvas.getBoundingClientRect();
    let clientX = e.clientX;
    let clientY = e.clientY;
    if (e.touches && e.touches.length > 0) {
        clientX = e.touches[0].clientX;
        clientY = e.touches[0].clientY;
    }
    return {
        x: clientX - rect.left,
        y: clientY - rect.top
    };
}

canvas.addEventListener('pointerdown', (e) => {
    isDrawing = true;
    hasDrawn = true;
    let pos = getPos(e);
    ctx.beginPath();
    ctx.moveTo(pos.x, pos.y);
});

canvas.addEventListener('pointermove', (e) => {
    if (!isDrawing) return;
    let pos = getPos(e);
    ctx.lineTo(pos.x, pos.y);
    ctx.stroke();
});

window.addEventListener('pointerup', () => {
    if (isDrawing) {
        isDrawing = false;
        updateSignatureFromCanvas();
    }
});

function clearCanvas() {
    let rect = canvas.getBoundingClientRect();
    ctx.clearRect(0, 0, rect.width, rect.height);
    hasDrawn = false;
    document.getElementById('signatureData').value = '';
    
    // Reset Live Preview
    let curTtd = '<?php echo addslashes($pgTtd ?: ""); ?>';
    let previewImg = document.getElementById('livePreviewTtdImg');
    let previewPlaceholder = document.getElementById('livePreviewTtdPlaceholder');
    if (curTtd) {
        previewImg.src = curTtd;
        previewImg.style.display = 'block';
        previewPlaceholder.style.display = 'none';
    } else {
        previewImg.style.display = 'none';
        previewPlaceholder.style.display = 'block';
    }
}

function updateSignatureFromCanvas() {
    if (hasDrawn) {
        let dataUrl = canvas.toDataURL('image/png');
        document.getElementById('signatureData').value = dataUrl;

        // Update live preview box
        let previewImg = document.getElementById('livePreviewTtdImg');
        let previewPlaceholder = document.getElementById('livePreviewTtdPlaceholder');
        previewImg.src = dataUrl;
        previewImg.style.display = 'block';
        previewPlaceholder.style.display = 'none';
    }
}

function previewUploadImage(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('fileUploadPreviewImg').src = e.target.result;
            document.getElementById('fileUploadPreviewWrap').style.display = 'block';

            // Also update live preview document
            let previewImg = document.getElementById('livePreviewTtdImg');
            let previewPlaceholder = document.getElementById('livePreviewTtdPlaceholder');
            previewImg.src = e.target.result;
            previewImg.style.display = 'block';
            previewPlaceholder.style.display = 'none';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function updateLivePreview() {
    let namaVal = document.getElementById('inputNama').value || 'Pengawas Penilai';
    let nipVal = document.getElementById('inputNip').value || '-';
    let peran = '<?php echo ($pgPeran === "penilai_1") ? "Penilai 1" : "Penilai 2"; ?>';
    
    document.getElementById('livePreviewNama').innerText = namaVal;
    document.getElementById('livePreviewNip').innerText = peran + ' / NIP. ' + nipVal;
}

// FORM SUBMISSION AJAX UNTUK TTD
document.getElementById('formTtd').addEventListener('submit', function(e) {
    e.preventDefault();
    let btn = document.getElementById('btnSimpanTtd');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';

    // Pastikan data canvas ter-serialize jika ada goresan
    if (hasDrawn) {
        updateSignatureFromCanvas();
    }

    let formData = new FormData(this);

    fetch('/pengawas/pkkm/simpan-ttd', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="check-circle" style="width:18px;height:18px;"></i> Simpan Tanda Tangan Digital';
        if (typeof lucide !== 'undefined') lucide.createIcons();

        if (data.status === 1) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Disimpan!',
                text: 'Tanda tangan digital Anda telah tersimpan dan siap dibubuhkan otomatis pada dokumen cetak Berita Acara.',
                confirmButtonColor: '#0284c7'
            });

            if (data.ttd_url) {
                document.getElementById('livePreviewTtdImg').src = data.ttd_url;
                document.getElementById('livePreviewTtdImg').style.display = 'block';
                document.getElementById('livePreviewTtdPlaceholder').style.display = 'none';
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menyimpan',
                text: data.pesan || 'Terjadi kesalahan saat menyimpan tanda tangan.',
                confirmButtonColor: '#ef4444'
            });
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="check-circle" style="width:18px;height:18px;"></i> Simpan Tanda Tangan Digital';
        if (typeof lucide !== 'undefined') lucide.createIcons();
        Swal.fire({
            icon: 'error',
            title: 'Kesalahan Sistem',
            text: 'Gagal terhubung ke server.',
            confirmButtonColor: '#ef4444'
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    initCanvas();
    window.addEventListener('resize', initCanvas);

    // Restore sidebar collapse preference on desktop
    if (window.innerWidth >= 993 && localStorage.getItem('pkkm_sidebar_collapsed') === '1') {
        let app = document.querySelector('.app-wrapper');
        if (app) {
            app.classList.add('sidebar-collapsed');
            updateCollapseIcons(true);
        }
    }

    if (typeof lucide !== 'undefined') lucide.createIcons();
});
</script>
</body>
</html>
