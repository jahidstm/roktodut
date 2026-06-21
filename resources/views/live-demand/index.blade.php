<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>লাইভ রক্তের চাহিদা মানচিত্র — রক্তদূত</title>
    <meta name="description" content="বাংলাদেশের জেলাভিত্তিক রিয়েল-টাইম রক্তের জরুরি চাহিদার ইন্টারেক্টিভ মানচিত্র।">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    {{-- Leaflet --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap');

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            font-family: 'Hind Siliguri', ui-sans-serif, system-ui, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            overflow: hidden;
        }

        /* ══ Topbar ══════════════════════════════════════════ */
        .topbar {
            height: 60px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            position: relative;
            z-index: 200;
        }
        .topbar-brand {
            display: flex; align-items: center; gap: 0.65rem;
            text-decoration: none;
        }
        .topbar-logo {
            width: 36px; height: 36px; border-radius: 10px;
            border: 1px solid #f1f5f9; overflow: hidden;
            background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            display: flex; align-items: center; justify-content: center;
        }
        .topbar-logo img { width: 100%; height: 100%; object-fit: contain; padding: 4px; }
        .topbar-name  { font-size: 1rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; }
        .topbar-sub   { font-size: 0.62rem; color: #94a3b8; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; }
        .topbar-center{ font-size: 0.88rem; font-weight: 700; color: #334155; }
        .topbar-right { display: flex; align-items: center; gap: 0.75rem; }

        .live-pill {
            display: flex; align-items: center; gap: 0.4rem;
            background: #fef2f2; border: 1px solid #fecaca;
            border-radius: 9999px; padding: 0.25rem 0.8rem;
            font-size: 0.72rem; font-weight: 700; color: #dc2626;
        }
        .live-dot {
            width: 6px; height: 6px; background: #dc2626;
            border-radius: 50%; animation: blink 1.5s ease-in-out infinite;
        }
        @keyframes blink { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.3;transform:scale(0.8)} }

        .last-updated {
            font-size: 0.65rem; color: #94a3b8; font-weight: 500;
            white-space: nowrap;
        }

        .back-btn {
            display: flex; align-items: center; gap: 0.4rem;
            font-size: 0.78rem; font-weight: 600; color: #64748b;
            text-decoration: none; padding: 0.35rem 0.75rem;
            border-radius: 8px; border: 1px solid #e2e8f0;
            background: #fff; transition: all 0.15s;
        }
        .back-btn:hover { border-color: #dc2626; color: #dc2626; }

        /* ══ Blood Group Filter Bar ══════════════════════════ */
        .filter-bar {
            height: 44px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0 1.5rem;
            overflow-x: auto;
            z-index: 190;
        }
        .filter-bar::-webkit-scrollbar { height: 2px; }
        .filter-bar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

        .filter-label {
            font-size: 0.63rem; font-weight: 700;
            color: #94a3b8; text-transform: uppercase;
            letter-spacing: 0.08em; white-space: nowrap;
            margin-right: 0.3rem;
        }

        .filter-pill {
            display: flex; align-items: center; gap: 0.3rem;
            font-size: 0.72rem; font-weight: 700;
            padding: 0.22rem 0.75rem; border-radius: 9999px;
            border: 1.5px solid #e2e8f0; background: #f8fafc;
            color: #475569; cursor: pointer; white-space: nowrap;
            transition: all 0.15s ease; user-select: none;
        }
        .filter-pill:hover { border-color: #dc2626; color: #dc2626; background: #fef2f2; }
        .filter-pill.active {
            background: #dc2626; border-color: #dc2626;
            color: #ffffff; box-shadow: 0 2px 8px rgba(220,38,38,0.25);
        }
        .filter-pill-dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: currentColor; opacity: 0.7;
            display: none;
        }
        .filter-pill.active .filter-pill-dot { display: block; background: #fff; }

        /* ══ 3-Column BI Layout ══════════════════════════════ */
        .bi-layout {
            display: grid;
            grid-template-columns: 290px 1fr 290px;
            height: calc(100vh - 104px); /* topbar 60 + filter 44 */
            overflow: hidden;
        }

        /* ══ Side Panels ══════════════════════════════════════ */
        .panel {
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            overflow-y: auto;
            padding: 1rem;
        }
        .panel.right { border-right: none; border-left: 1px solid #e2e8f0; }
        .panel::-webkit-scrollbar { width: 3px; }
        .panel::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

        .panel-section { margin-bottom: 1.25rem; }
        .panel-section-title {
            font-size: 0.58rem; font-weight: 800;
            letter-spacing: 0.12em; text-transform: uppercase;
            color: #94a3b8; margin-bottom: 0.6rem;
            padding-bottom: 0.4rem; border-bottom: 1px solid #f1f5f9;
        }

        /* Legend */
        .legend-row {
            display: flex; align-items: center; gap: 0.55rem;
            padding: 0.4rem 0.5rem; border-radius: 8px;
            margin-bottom: 0.15rem; transition: background 0.12s;
        }
        .legend-row:hover { background: #f8fafc; }
        .legend-swatch {
            width: 16px; height: 10px; border-radius: 3px;
            flex-shrink: 0; border: 1px solid rgba(0,0,0,0.07);
        }
        .legend-text { font-size: 0.73rem; color: #374151; font-weight: 500; }

        /* Stat blocks */
        .stat-block {
            padding: 0.7rem 0.85rem; border-radius: 10px;
            background: #f8fafc; border: 1px solid #e2e8f0;
            margin-bottom: 0.55rem; transition: box-shadow 0.15s;
        }
        .stat-block:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.07); }
        .stat-block-label { font-size: 0.6rem; font-weight: 700; color: #94a3b8; letter-spacing: 0.06em; text-transform: uppercase; margin-bottom: 0.2rem; }
        .stat-block-value { font-size: 1.55rem; font-weight: 800; color: #0f172a; line-height: 1.1; letter-spacing: -0.03em; }
        .stat-block-value.accent { color: #dc2626; }
        .stat-block-sub { font-size: 0.62rem; color: #94a3b8; margin-top: 0.2rem; }

        /* Top Blood Group highlight block */
        .blood-group-hero {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.7rem 0.85rem; border-radius: 10px;
            background: linear-gradient(135deg, #fef2f2 0%, #fff 100%);
            border: 1.5px solid #fecaca;
            margin-bottom: 0.55rem;
        }
        .blood-group-badge {
            font-size: 1.3rem; font-weight: 900; color: #dc2626;
            background: #fee2e2; border-radius: 8px;
            padding: 0.3rem 0.6rem; letter-spacing: -0.02em;
            border: 1px solid #fca5a5; min-width: 48px;
            text-align: center;
        }
        .blood-group-info-label { font-size: 0.6rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.06em; }
        .blood-group-info-value { font-size: 0.85rem; font-weight: 700; color: #0f172a; }

        /* District entries */
        .district-entry {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.45rem 0.6rem; border-radius: 8px;
            margin-bottom: 0.15rem; transition: background 0.12s; cursor: pointer;
        }
        .district-entry:hover { background: #fef2f2; }
        .district-entry-name { font-size: 0.78rem; font-weight: 600; color: #1e293b; }
        .district-entry-badge {
            font-size: 0.63rem; font-weight: 800;
            padding: 0.18rem 0.5rem; border-radius: 9999px;
        }
        .badge-critical { background: #fee2e2; color: #dc2626; }
        .badge-warning  { background: #fef3c7; color: #d97706; }
        .badge-stable   { background: #dcfce7; color: #16a34a; }

        /* ══ Map Container ══════════════════════════════════ */
        .map-wrapper {
            position: relative;
            background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
        }

        #map {
            width: calc(100% - 24px);
            height: calc(100% - 24px);
            margin: 12px;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 14px 36px rgba(15, 23, 42, 0.10);
            background-color: #eef2f7;
            background-image: radial-gradient(#c8d6e5 1px, transparent 1px);
            background-size: 22px 22px;
            overflow: hidden;
        }

        .map-wrapper::after {
            content: '';
            position: absolute;
            inset: 12px;
            border-radius: 18px;
            box-shadow: inset 0 0 40px rgba(15, 23, 42, 0.06);
            pointer-events: none;
        }

        /* ══ Loading Overlay ══════════════════════════════════ */
        #loading-overlay {
            position: absolute; inset: 12px;
            background: rgba(238,242,247,0.92);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 0.75rem; z-index: 9999;
            backdrop-filter: blur(4px);
            border-radius: 18px;
        }
        .spinner {
            width: 36px; height: 36px;
            border: 3px solid #fecaca;
            border-top-color: #dc2626;
            border-radius: 50%; animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .loading-text { font-size: 0.8rem; color: #64748b; font-weight: 500; }

        /* ══ Leaflet overrides ════════════════════════════════ */
        .leaflet-control-attribution { display: none !important; }
        .leaflet-control-zoom {
            border: none !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.10) !important;
            border-radius: 10px !important; overflow: hidden;
        }
        .leaflet-control-zoom a {
            background: #fff !important; color: #374151 !important;
            border-color: #e2e8f0 !important;
            width: 32px !important; height: 32px !important;
            line-height: 32px !important; font-size: 16px !important;
            font-weight: 700 !important;
        }
        .leaflet-control-zoom a:hover { background: #f8fafc !important; color: #dc2626 !important; }
        .leaflet-pane svg path { filter: none !important; }

        /* ══ Custom Hover Tooltip ════════════════════════════ */
        .leaflet-tooltip.map-tooltip {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }
        .leaflet-tooltip.map-tooltip::before { display: none !important; }

        .tt-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 8px 32px rgba(15,23,42,0.13);
            padding: 0.9rem 1.1rem;
            min-width: 210px;
            pointer-events: none;
            animation: tooltipIn 0.18s cubic-bezier(0.22, 1, 0.36, 1) both;
        }
        @keyframes tooltipIn {
            from { opacity: 0; transform: translateY(6px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .tt-name {
            font-size: 0.92rem; font-weight: 800; color: #0f172a;
            margin-bottom: 0.5rem; padding-bottom: 0.45rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex; align-items: center; gap: 0.4rem;
        }
        .tt-status {
            display: inline-block;
            font-size: 0.68rem; font-weight: 700;
            padding: 0.2rem 0.6rem; border-radius: 9999px;
            margin-bottom: 0.6rem;
        }
        .tt-meta {
            font-size: 0.75rem; color: #475569; margin-bottom: 0.25rem;
            display: flex; justify-content: space-between;
        }
        .tt-meta span { font-weight: 700; color: #0f172a; }

        /* Blood group breakdown bar */
        .tt-bg-section {
            margin-top: 0.55rem; padding-top: 0.45rem;
            border-top: 1px solid #f1f5f9;
        }
        .tt-bg-label {
            font-size: 0.6rem; font-weight: 700; color: #94a3b8;
            text-transform: uppercase; letter-spacing: 0.08em;
            margin-bottom: 0.35rem;
        }
        .tt-bg-row {
            display: flex; align-items: center; gap: 0.45rem;
            margin-bottom: 0.22rem;
        }
        .tt-bg-name {
            font-size: 0.68rem; font-weight: 800; color: #dc2626;
            width: 30px; flex-shrink: 0;
        }
        .tt-bg-bar-wrap {
            flex: 1; background: #f1f5f9; border-radius: 3px; height: 6px; overflow: hidden;
        }
        .tt-bg-bar {
            height: 100%; background: #dc2626; border-radius: 3px;
            transition: width 0.4s ease;
        }
        .tt-bg-count {
            font-size: 0.67rem; font-weight: 700; color: #374151;
            width: 18px; text-align: right; flex-shrink: 0;
        }

        .tt-hint {
            margin-top: 0.55rem; padding-top: 0.45rem;
            border-top: 1px solid #f1f5f9;
            font-size: 0.67rem; color: #94a3b8;
        }

        /* Quick-links */
        .quick-link {
            display: flex; align-items: center; gap: 0.5rem;
            padding: 0.6rem 0.8rem; border-radius: 10px;
            text-decoration: none; margin-bottom: 0.5rem; transition: all 0.15s;
        }
        .quick-link-icon { font-size: 1rem; }
        .quick-link-title { font-size: 0.73rem; font-weight: 700; }
        .quick-link-sub   { font-size: 0.62rem; }

        /* ══ Responsive ═════════════════════════════════════ */
        @media (max-width: 900px) {
            body, html { overflow: auto; height: auto; }
            .topbar { padding: 0 0.8rem; }
            .topbar-sub { display: none; }
            .topbar-center { display: none; }
            .live-pill { padding: 0.25rem 0.5rem; font-size: 0.65rem; }
            .back-btn { padding: 0.3rem 0.5rem; font-size: 0.7rem; }
            .filter-bar { padding: 0 0.8rem; }

            .bi-layout {
                display: flex;
                flex-direction: column;
                height: auto;
                min-height: calc(100vh - 104px);
            }
            .map-wrapper { height: 60vh; min-height: 400px; flex-shrink: 0; order: -1; }
            .panel { max-height: none !important; border-right: none !important; border-bottom: 1px solid #e2e8f0; padding: 1.25rem 1rem; }
            .panel.right { border-left: none !important; border-top: none !important; }
        }
    </style>
</head>
<body>

{{-- ══ Topbar ══════════════════════════════════════════════ --}}
<nav class="topbar" x-data="{ mobileMenuOpen: false }">
    <div style="display:flex; align-items:center; gap:0.5rem;">
        <button @click="mobileMenuOpen = true" class="lg:hidden" style="background:none; border:none; padding:0.25rem; color:#64748b; cursor:pointer;">
            <svg style="width:24px; height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
        <a href="{{ route('home') }}" class="topbar-brand">
            <div class="topbar-logo">
                <x-logo size="md" variant="full" />
            </div>
            <div>
                <div class="topbar-name">রক্তদূত</div>
                <div class="topbar-sub">Blood Donation Platform</div>
            </div>
        </a>
    </div>

    <span class="topbar-center">🩸 লাইভ রক্তের চাহিদা মানচিত্র</span>

    <div class="topbar-right">
        <div class="live-pill">
            <div class="live-dot"></div>
            লাইভ ডেটা
        </div>
        <span class="last-updated" id="last-updated-text">লোড হচ্ছে...</span>
        <a href="{{ route('home') }}" class="back-btn">← হোমে ফিরুন</a>
    </div>

    @include('components.mobile-menu')
</nav>

{{-- ══ Blood Group Filter Bar ══════════════════════════════ --}}
<div class="filter-bar" id="filter-bar">
    <span class="filter-label">ফিল্টার:</span>
    <button class="filter-pill active" data-group="" id="pill-all">সব গ্রুপ</button>
    <button class="filter-pill" data-group="A+" id="pill-A+">A+</button>
    <button class="filter-pill" data-group="A-" id="pill-A-">A−</button>
    <button class="filter-pill" data-group="B+" id="pill-B+">B+</button>
    <button class="filter-pill" data-group="B-" id="pill-B-">B−</button>
    <button class="filter-pill" data-group="O+" id="pill-O+">O+</button>
    <button class="filter-pill" data-group="O-" id="pill-O-">O−</button>
    <button class="filter-pill" data-group="AB+" id="pill-AB+">AB+</button>
    <button class="filter-pill" data-group="AB-" id="pill-AB-">AB−</button>
</div>

{{-- ══ 3-Column BI Layout ═══════════════════════════════════ --}}
<div class="bi-layout">

    {{-- LEFT: Legend + Critical Districts --}}
    <aside class="panel">

        <div class="panel-section">
            <p class="panel-section-title">রঙের অর্থ</p>
            <div class="legend-row"><div class="legend-swatch" style="background:#800026;"></div><span class="legend-text">সংকট — জরুরি ডোনার প্রয়োজন</span></div>
            <div class="legend-row"><div class="legend-swatch" style="background:#E31A1C;"></div><span class="legend-text">জরুরি — চাহিদা তীব্র</span></div>
            <div class="legend-row"><div class="legend-swatch" style="background:#FD8D3C;"></div><span class="legend-text">সতর্কতা — চাহিদা বাড়ছে</span></div>
            <div class="legend-row"><div class="legend-swatch" style="background:#FEB24C;"></div><span class="legend-text">মনোযোগ — কিছুটা চাহিদা আছে</span></div>
            <div class="legend-row"><div class="legend-swatch" style="background:#52b788;"></div><span class="legend-text">স্বাভাবিক — কোনো অনুরোধ নেই</span></div>
        </div>

        <div class="panel-section">
            <p class="panel-section-title">জরুরি জেলাসমূহ</p>
            <div id="critical-districts-list">
                <div style="font-size:0.75rem;color:#94a3b8;padding:0.5rem;">লোড হচ্ছে...</div>
            </div>
        </div>

    </aside>

    {{-- CENTER: Map --}}
    <div class="map-wrapper">
        <div id="loading-overlay">
            <div class="spinner"></div>
            <p class="loading-text">মানচিত্র লোড হচ্ছে...</p>
        </div>
        <div id="map"></div>
    </div>

    {{-- RIGHT: Stats --}}
    <aside class="panel right">

        <div class="panel-section">
            <p class="panel-section-title">সামগ্রিক পরিসংখ্যান</p>

            <div class="stat-block">
                <div class="stat-block-label">মোট সক্রিয় রিকোয়েস্ট</div>
                <div class="stat-block-value accent" id="stat-total">—</div>
                <div class="stat-block-sub">সারাদেশে এই মুহূর্তে</div>
            </div>

            <div class="stat-block">
                <div class="stat-block-label">সর্বোচ্চ চাহিদার জেলা</div>
                <div class="stat-block-value" style="font-size:1.05rem;" id="stat-top-name">—</div>
                <div class="stat-block-sub" id="stat-top-count">লোড হচ্ছে...</div>
            </div>

            <div class="stat-block">
                <div class="stat-block-label">মোট জেলা</div>
                <div class="stat-block-value">৬৪</div>
                <div class="stat-block-sub">সকল জেলা অন্তর্ভুক্ত</div>
            </div>

            <div class="stat-block">
                <div class="stat-block-label">জরুরি অবস্থায় জেলা</div>
                <div class="stat-block-value accent" id="stat-critical-count">—</div>
                <div class="stat-block-sub">CRS &gt; 50 এর জেলা</div>
            </div>
        </div>

        <div class="panel-section">
            <p class="panel-section-title">সারাদেশে সবচেয়ে বেশি চাহিদা</p>
            <div class="blood-group-hero" id="top-bg-hero" style="display:none;">
                <div class="blood-group-badge" id="top-bg-badge">—</div>
                <div>
                    <div class="blood-group-info-label">সর্বোচ্চ চাহিদার গ্রুপ</div>
                    <div class="blood-group-info-value" id="top-bg-count">— টি অনুরোধ</div>
                </div>
            </div>
            <div id="top-bg-empty" style="font-size:0.75rem;color:#94a3b8;padding:0.5rem 0;">লোড হচ্ছে...</div>
        </div>

        <div class="panel-section">
            <p class="panel-section-title">দ্রুত সংযোগ</p>
            <a href="{{ route('requests.index') }}" class="quick-link"
               style="background:#fef2f2;border:1px solid #fecaca;"
               onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                <span class="quick-link-icon">🩸</span>
                <div>
                    <div class="quick-link-title" style="color:#dc2626;">রক্তের অনুরোধ করুন</div>
                    <div class="quick-link-sub" style="color:#f87171;">জরুরি রক্তের প্রয়োজন?</div>
                </div>
            </a>
            <a href="{{ route('search') }}" class="quick-link"
               style="background:#f0fdf4;border:1px solid #bbf7d0;"
               onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                <span class="quick-link-icon">🔍</span>
                <div>
                    <div class="quick-link-title" style="color:#16a34a;">ডোনার খুঁজুন</div>
                    <div class="quick-link-sub" style="color:#4ade80;">আপনার এলাকায় ডোনার</div>
                </div>
            </a>
        </div>

        <div class="panel-section">
            <p class="panel-section-title">তথ্য আপডেট</p>
            <div style="font-size:0.68rem;color:#94a3b8;line-height:1.7;">
                এই মানচিত্র প্রতি <strong style="color:#64748b;">৩ মিনিট</strong> অন্তর আপডেট হয়।
                সার্ভার ক্যাশ ও ক্লায়েন্ট রিফ্রেশ পূর্ণভাবে সিঙ্ক করা।
                শুধুমাত্র <em>pending / in_progress</em> রক্তের অনুরোধ গণনা হচ্ছে।
            </div>
        </div>

    </aside>

</div>

<script>
// ══════════════════════════════════════════════════════════════════
//  রক্তদূত Live Demand Map — Full Implementation
//  Cache & Polling: 3 minutes (synchronized)
// ══════════════════════════════════════════════════════════════════

(function () {
    // ── Constants ────────────────────────────────────────────────
    const REFRESH_MS = 3 * 60 * 1000; // 3 minutes — same as server cache TTL

    const EN_TO_BN = {
        'Comilla':'কুমিল্লা','Feni':'ফেনী','Brahamanbaria':'ব্রাহ্মণবাড়িয়া',
        'Rangamati':'রাঙ্গামাটি','Noakhali':'নোয়াখালী','Chandpur':'চাঁদপুর',
        'Lakshmipur':'লক্ষ্মীপুর','Chittagong':'চট্টগ্রাম',"Cox'SBazar":'কক্সবাজার',
        'Khagrachhari':'খাগড়াছড়ি','Bandarban':'বান্দরবান','Sirajganj':'সিরাজগঞ্জ',
        'Pabna':'পাবনা','Bogra':'বগুড়া','Rajshahi':'রাজশাহী','Natore':'নাটোর',
        'Joypurhat':'জয়পুরহাট','Nawabganj':'চাঁপাইনবাবগঞ্জ','Naogaon':'নওগাঁ',
        'Jessore':'যশোর','Satkhira':'সাতক্ষীরা','Meherpur':'মেহেরপুর',
        'Narail':'নড়াইল','Chuadanga':'চুয়াডাঙ্গা','Kushtia':'কুষ্টিয়া',
        'Magura':'মাগুরা','Khulna':'খুলনা','Bagerhat':'বাগেরহাট',
        'Jhenaidah':'ঝিনাইদহ','Jhalokati':'ঝালকাঠি','Patuakhali':'পটুয়াখালী',
        'Pirojpur':'পিরোজপুর','Barisal':'বরিশাল','Bhola':'ভোলা','Barguna':'বরগুনা',
        'Sylhet':'সিলেট','Maulvibazar':'মৌলভীবাজার','Habiganj':'হবিগঞ্জ',
        'Sunamganj':'সুনামগঞ্জ','Narsingdi':'নরসিংদী','Gazipur':'গাজীপুর',
        'Shariatpur':'শরীয়তপুর','Narayanganj':'নারায়ণগঞ্জ','Tangail':'টাঙ্গাইল',
        'Kishoreganj':'কিশোরগঞ্জ','Manikganj':'মানিকগঞ্জ','Dhaka':'ঢাকা',
        'Munshiganj':'মুন্সিগঞ্জ','Rajbari':'রাজবাড়ী','Madaripur':'মাদারীপুর',
        'Gopalganj':'গোপালগঞ্জ','Faridpur':'ফরিদপুর','Panchagarh':'পঞ্চগড়',
        'Dinajpur':'দিনাজপুর','Lalmonirhat':'লালমনিরহাট','Nilphamari':'নীলফামারী',
        'Gaibandha':'গাইবান্ধা','Thakurgaon':'ঠাকুরগাঁও','Rangpur':'রংপুর',
        'Kurigram':'কুড়িগ্রাম','Sherpur':'শেরপুর','Mymensingh':'ময়মনসিংহ',
        'Jamalpur':'জামালপুর','Netrakona':'নেত্রকোণা',
    };

    const toBn = (en) => EN_TO_BN[en] || en;

    const getColor = (crs) =>
        crs > 75 ? '#800026' : crs > 50 ? '#E31A1C' :
        crs > 30 ? '#FD8D3C' : crs > 0  ? '#FEB24C' : '#52b788';

    const getStatus = (crs, demand) => {
        if (!demand || !crs) return { label:'✅ স্বাভাবিক', color:'#16a34a', bg:'#dcfce7', cls:'badge-stable' };
        if (crs > 75) return { label:'🚨 সংকট: অবিলম্বে ডোনার প্রয়োজন!', color:'#dc2626', bg:'#fee2e2', cls:'badge-critical' };
        if (crs > 50) return { label:'🔴 জরুরি: চাহিদা তীব্র',             color:'#dc2626', bg:'#fee2e2', cls:'badge-critical' };
        if (crs > 30) return { label:'⚠️ সতর্কতা: চাহিদা বাড়ছে',          color:'#d97706', bg:'#fef3c7', cls:'badge-warning'  };
        return               { label:'🟡 মনোযোগ প্রয়োজন',                 color:'#d97706', bg:'#fef3c7', cls:'badge-warning'  };
    };

    // ── Build blood-group breakdown HTML ─────────────────────────
    function buildBgBreakdown(bgGroups) {
        if (!bgGroups || Object.keys(bgGroups).length === 0) return '';

        const maxCnt = Math.max(...Object.values(bgGroups));
        const sorted = Object.entries(bgGroups).sort((a, b) => b[1] - a[1]);
        const rows   = sorted.map(([grp, cnt]) => {
            const pct = maxCnt > 0 ? Math.round((cnt / maxCnt) * 100) : 0;
            return `<div class="tt-bg-row">
                <span class="tt-bg-name">${grp}</span>
                <div class="tt-bg-bar-wrap"><div class="tt-bg-bar" style="width:${pct}%"></div></div>
                <span class="tt-bg-count">${cnt}</span>
            </div>`;
        }).join('');

        return `<div class="tt-bg-section">
            <div class="tt-bg-label">ব্লাড গ্রুপ বিভাজন</div>
            ${rows}
        </div>`;
    }

    // ── Map + GeoJSON state ──────────────────────────────────────
    let map       = null;
    let geoLayer  = null;
    let geojsonData = null;
    let heatmapData = {};
    let activeGroup = '';

    // ── Init Leaflet ─────────────────────────────────────────────
    function initMap() {
        map = L.map('map', {
            center: [23.685, 90.356], zoom: 7,
            zoomSnap: 0, zoomDelta: 0.5,
            attributionControl: false, maxBoundsViscosity: 1.0,
        });
    }

    // ── Build or refresh the GeoJSON layer ───────────────────────
    function renderLayer() {
        if (geoLayer) { geoLayer.remove(); geoLayer = null; }

        let totalDemand   = 0;
        let topDistrict   = { name: '—', demand: 0 };
        const criticalList = [];

        // Nationwide blood group totals
        const bgNationwide = {};

        const isTouch = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0);
        const BASE_OPACITY = 0.85, HOVER_OPACITY = 1.0;
        const BASE_WEIGHT  = 1.5,  HOVER_WEIGHT  = 2.5;

        geoLayer = L.geoJSON(geojsonData, {
            style(feature) {
                const enName = feature.properties?.NAME_2 || '';
                const info   = heatmapData[enName] ?? { demand: 0, crs: 0 };
                return { fillColor: getColor(info.crs), fillOpacity: BASE_OPACITY, color: '#ffffff', weight: BASE_WEIGHT };
            },
            onEachFeature(feature, layer) {
                const enName = feature.properties?.NAME_2 || 'Unknown';
                const bnName = toBn(enName);
                const info   = heatmapData[enName] ?? { demand: 0, crs: 0, blood_groups: {}, top_blood_group: null };
                const status = getStatus(info.crs, info.demand);

                totalDemand += info.demand;
                if (info.demand > topDistrict.demand) topDistrict = { name: bnName, demand: info.demand };
                if (info.demand > 0) criticalList.push({ enName, bnName, demand: info.demand, crs: info.crs });

                // Aggregate nationwide blood groups
                Object.entries(info.blood_groups || {}).forEach(([g, c]) => {
                    bgNationwide[g] = (bgNationwide[g] || 0) + c;
                });

                const hintText = isTouch && info.demand > 0 ? `👆 ডাবল ট্যাপ করে ডোনার খুঁজুন` : `👆 ক্লিক করে ডোনার খুঁজুন`;

                const bgBreakdown = buildBgBreakdown(info.blood_groups);

                const tooltipHtml = `
                    <div class="tt-card">
                        <div class="tt-name">📍 ${bnName}</div>
                        <span class="tt-status" style="background:${status.bg};color:${status.color};">${status.label}</span>
                        <div class="tt-meta">
                            <span>সক্রিয় রক্তের অনুরোধ</span>
                            <span>${info.demand} টি</span>
                        </div>
                        ${bgBreakdown}
                        ${info.demand > 0 ? `<div class="tt-hint">${hintText}</div>` : ''}
                    </div>`;

                layer.bindTooltip(tooltipHtml, {
                    sticky: true, permanent: false,
                    direction: 'top', className: 'map-tooltip', offset: [0, -8],
                });

                if (info.demand > 0) {
                    let tooltipOpenedTime = 0;
                    layer.on('tooltipopen', () => { tooltipOpenedTime = Date.now(); });
                    layer.on('click', () => {
                        if (isTouch && Date.now() - tooltipOpenedTime < 400) return;
                        window.location.href = '/search';
                    });
                }

                layer.on({
                    mouseover(e) { e.target.setStyle({ fillOpacity: HOVER_OPACITY, weight: HOVER_WEIGHT }); e.target.bringToFront(); },
                    mouseout(e)  { e.target.setStyle({ fillOpacity: BASE_OPACITY,  weight: BASE_WEIGHT  }); },
                });
            },
        }).addTo(map);

        // ── Update sidebar ────────────────────────────────────────
        document.getElementById('stat-total').textContent     = totalDemand;
        document.getElementById('stat-top-name').textContent  = topDistrict.name;
        document.getElementById('stat-top-count').textContent =
            topDistrict.demand > 0 ? `${topDistrict.demand}টি সক্রিয় রিকোয়েস্ট` : 'কোনো রিকোয়েস্ট নেই';

        const criticalCount = criticalList.filter(d => d.crs > 50).length;
        document.getElementById('stat-critical-count').textContent = criticalCount;

        criticalList.sort((a, b) => b.crs - a.crs);
        const listEl = document.getElementById('critical-districts-list');
        listEl.innerHTML = criticalList.length === 0
            ? '<div style="font-size:0.75rem;color:#94a3b8;padding:0.5rem 0;">সারাদেশে কোনো জরুরি রিকোয়েস্ট নেই 🎉</div>'
            : criticalList.slice(0, 8).map(d => {
                const s = getStatus(d.crs, d.demand);
                return `<div class="district-entry">
                    <span class="district-entry-name">${d.bnName}</span>
                    <span class="district-entry-badge ${s.cls}">${d.demand}টি</span>
                </div>`;
            }).join('');

        // ── Nationwide top blood group panel ─────────────────────
        const heroEl  = document.getElementById('top-bg-hero');
        const emptyEl = document.getElementById('top-bg-empty');
        if (Object.keys(bgNationwide).length > 0) {
            const topBg  = Object.entries(bgNationwide).sort((a,b) => b[1]-a[1])[0];
            document.getElementById('top-bg-badge').textContent = topBg[0];
            document.getElementById('top-bg-count').textContent = `${topBg[1]} টি অনুরোধ`;
            heroEl.style.display  = 'flex';
            emptyEl.style.display = 'none';
        } else {
            heroEl.style.display  = 'none';
            emptyEl.textContent   = 'কোনো সক্রিয় অনুরোধ নেই';
            emptyEl.style.display = 'block';
        }

        // ── Update last-updated text ──────────────────────────────
        const now = new Date();
        document.getElementById('last-updated-text').textContent =
            `শেষ আপডেট: ${now.getHours().toString().padStart(2,'0')}:${now.getMinutes().toString().padStart(2,'0')}`;
    }

    // ── Fit map to Bangladesh bounds ─────────────────────────────
    function fitMapToBangladesh() {
        if (!geoLayer) return;
        map.invalidateSize();
        map.setMinZoom(1);
        map.fitBounds(geoLayer.getBounds(), { padding: [0, 0] });
        setTimeout(() => {
            map.setMinZoom(map.getZoom());
            map.setMaxBounds(geoLayer.getBounds().pad(0.02));
        }, 50);
    }

    // ── Fetch heatmap data from API ───────────────────────────────
    async function fetchHeatmapData(group = '') {
        const url = group
            ? `/api/analytics/spatial-heatmap?group=${encodeURIComponent(group)}`
            : '/api/analytics/spatial-heatmap';
        const res  = await fetch(url);
        return res.json();
    }

    // ── Refresh map data (called by auto-refresh timer) ──────────
    async function refreshMap(group) {
        try {
            heatmapData = await fetchHeatmapData(group ?? activeGroup);
            renderLayer();
            fitMapToBangladesh();
        } catch (err) {
            console.warn('[LiveMap] Refresh failed:', err);
        }
    }

    // ── Filter pill click handler ─────────────────────────────────
    function setupFilterPills() {
        document.querySelectorAll('.filter-pill').forEach(pill => {
            pill.addEventListener('click', async () => {
                document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
                pill.classList.add('active');
                activeGroup = pill.dataset.group;

                document.getElementById('loading-overlay').style.display = 'flex';
                await refreshMap(activeGroup);
                document.getElementById('loading-overlay').style.display = 'none';
            });
        });
    }

    // ── Main init ────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', async () => {
        initMap();
        setupFilterPills();

        try {
            const [geoRes, apiRes] = await Promise.all([
                fetch('/geojson/bangladesh.geojson'),
                fetchHeatmapData(''),
            ]);
            geojsonData = await geoRes.json();
            heatmapData = apiRes;
        } catch (err) {
            document.getElementById('loading-overlay').innerHTML =
                '<p style="color:#dc2626;font-size:0.82rem;font-family:\'Hind Siliguri\',sans-serif;">ডেটা লোড করতে সমস্যা হয়েছে।<br>পেইজ রিফ্রেশ করুন।</p>';
            return;
        }

        renderLayer();

        // ✅ Perfect framing
        setTimeout(() => {
            fitMapToBangladesh();
            window.addEventListener('resize', fitMapToBangladesh);
            document.getElementById('loading-overlay').style.display = 'none';
        }, 150);

        // ✅ Auto-refresh: 3 minutes — synchronized with server cache TTL
        // Server cache = 3 min → client polls at 3 min → always gets fresh data
        setInterval(() => refreshMap(activeGroup), REFRESH_MS);
    });

})();
</script>

</body>
</html>
