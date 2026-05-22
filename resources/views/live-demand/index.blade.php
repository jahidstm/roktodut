<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>লাইভ রক্তের চাহিদা মানচিত্র — রক্তদূত</title>
    <meta name="description" content="বাংলাদেশের জেলাভিত্তিক রিয়েল-টাইম রক্তের জরুরি চাহিদার ইন্টারেক্টিভ মানচিত্র।">
    <link rel="icon" href="{{ asset('images/image_14.png') }}" type="image/png">

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

        .back-btn {
            display: flex; align-items: center; gap: 0.4rem;
            font-size: 0.78rem; font-weight: 600; color: #64748b;
            text-decoration: none; padding: 0.35rem 0.75rem;
            border-radius: 8px; border: 1px solid #e2e8f0;
            background: #fff; transition: all 0.15s;
        }
        .back-btn:hover { border-color: #dc2626; color: #dc2626; }

        /* ══ 3-Column BI Layout ══════════════════════════════ */
        .bi-layout {
            display: grid;
            grid-template-columns: 290px 1fr 290px;
            height: calc(100vh - 60px);
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

        /* ✅ Dotted-grid background — professional BI dashboard feel */
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

        /* ✅ Performance guard: block CSS filter on ALL Leaflet SVG paths.
           filter: drop-shadow on complex GeoJSON boundaries causes severe FPS drops
           because the browser must rasterize the entire SVG layer on every frame.
           Hover feedback is achieved purely via fillOpacity + stroke weight changes. */
        .leaflet-pane svg path { filter: none !important; }

        /* ══ Custom Hover Tooltip ════════════════════════════ */
        .leaflet-tooltip.map-tooltip {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }
        .leaflet-tooltip.map-tooltip::before { display: none !important; }

        /* The actual card inside the tooltip */
        .tt-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 8px 32px rgba(15,23,42,0.13);
            padding: 0.9rem 1.1rem;
            min-width: 190px;
            pointer-events: none;
            /* ✅ Smooth fade-in + slide-up animation */
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
            .bi-layout { grid-template-columns: 1fr; grid-template-rows: auto 1fr auto; overflow: auto; }
            .panel { max-height: 180px; border-right: none; border-bottom: 1px solid #e2e8f0; }
            .panel.right { border-left: none; border-top: 1px solid #e2e8f0; }
        }
    </style>
</head>
<body>

{{-- ══ Topbar ══════════════════════════════════════════════ --}}
<nav class="topbar">
    <a href="{{ route('home') }}" class="topbar-brand">
        <div class="topbar-logo">
            <img src="{{ asset('images/image_14.png') }}" alt="রক্তদূত লোগো">
        </div>
        <div>
            <div class="topbar-name">রক্তদূত</div>
            <div class="topbar-sub">Blood Donation Platform</div>
        </div>
    </a>

    <span class="topbar-center">🩸 লাইভ রক্তের চাহিদা মানচিত্র</span>

    <div class="topbar-right">
        <div class="live-pill">
            <div class="live-dot"></div>
            লাইভ ডেটা
        </div>
        <a href="{{ route('home') }}" class="back-btn">← হোমে ফিরুন</a>
    </div>
</nav>

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
                এই মানচিত্র প্রতি <strong style="color:#64748b;">১৫ মিনিট</strong> অন্তর আপডেট হয়।
                শুধুমাত্র <em>pending / in_progress</em> রক্তের অনুরোধ গণনা করা হচ্ছে।
            </div>
        </div>

    </aside>

</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {

    // ══ 1. GADM English → Bengali name dictionary ══════════════
    const EN_TO_BN = {
        'Comilla':        'কুমিল্লা',
        'Feni':           'ফেনী',
        'Brahamanbaria':  'ব্রাহ্মণবাড়িয়া',
        'Rangamati':      'রাঙ্গামাটি',
        'Noakhali':       'নোয়াখালী',
        'Chandpur':       'চাঁদপুর',
        'Lakshmipur':     'লক্ষ্মীপুর',
        'Chittagong':     'চট্টগ্রাম',
        "Cox'SBazar":     'কক্সবাজার',
        'Khagrachhari':   'খাগড়াছড়ি',
        'Bandarban':      'বান্দরবান',
        'Sirajganj':      'সিরাজগঞ্জ',
        'Pabna':          'পাবনা',
        'Bogra':          'বগুড়া',
        'Rajshahi':       'রাজশাহী',
        'Natore':         'নাটোর',
        'Joypurhat':      'জয়পুরহাট',
        'Nawabganj':      'চাঁপাইনবাবগঞ্জ',
        'Naogaon':        'নওগাঁ',
        'Jessore':        'যশোর',
        'Satkhira':       'সাতক্ষীরা',
        'Meherpur':       'মেহেরপুর',
        'Narail':         'নড়াইল',
        'Chuadanga':      'চুয়াডাঙ্গা',
        'Kushtia':        'কুষ্টিয়া',
        'Magura':         'মাগুরা',
        'Khulna':         'খুলনা',
        'Bagerhat':       'বাগেরহাট',
        'Jhenaidah':      'ঝিনাইদহ',
        'Jhalokati':      'ঝালকাঠি',
        'Patuakhali':     'পটুয়াখালী',
        'Pirojpur':       'পিরোজপুর',
        'Barisal':        'বরিশাল',
        'Bhola':          'ভোলা',
        'Barguna':        'বরগুনা',
        'Sylhet':         'সিলেট',
        'Maulvibazar':    'মৌলভীবাজার',
        'Habiganj':       'হবিগঞ্জ',
        'Sunamganj':      'সুনামগঞ্জ',
        'Narsingdi':      'নরসিংদী',
        'Gazipur':        'গাজীপুর',
        'Shariatpur':     'শরীয়তপুর',
        'Narayanganj':    'নারায়ণগঞ্জ',
        'Tangail':        'টাঙ্গাইল',
        'Kishoreganj':    'কিশোরগঞ্জ',
        'Manikganj':      'মানিকগঞ্জ',
        'Dhaka':          'ঢাকা',
        'Munshiganj':     'মুন্সিগঞ্জ',
        'Rajbari':        'রাজবাড়ী',
        'Madaripur':      'মাদারীপুর',
        'Gopalganj':      'গোপালগঞ্জ',
        'Faridpur':       'ফরিদপুর',
        'Panchagarh':     'পঞ্চগড়',
        'Dinajpur':       'দিনাজপুর',
        'Lalmonirhat':    'লালমনিরহাট',
        'Nilphamari':     'নীলফামারী',
        'Gaibandha':      'গাইবান্ধা',
        'Thakurgaon':     'ঠাকুরগাঁও',
        'Rangpur':        'রংপুর',
        'Kurigram':       'কুড়িগ্রাম',
        'Sherpur':        'শেরপুর',
        'Mymensingh':     'ময়মনসিংহ',
        'Jamalpur':       'জামালপুর',
        'Netrakona':      'নেত্রকোণা',
    };

    // Helper: get Bengali name, fallback to English if not mapped
    const toBn = (enName) => EN_TO_BN[enName] || enName;

    // ══ Init Leaflet Map ════════════════════════════════════
    const map = L.map('map', {
        center:             [23.685, 90.356],
        zoom:               7,
        zoomSnap:           0,
        zoomDelta:          0.5,
        attributionControl: false,
        maxBoundsViscosity: 1.0,
    });

    // ══ 3. CRS colour ramp ═══════════════════════════════════
    const getColor = (crs) =>
        crs > 75 ? '#800026' :
        crs > 50 ? '#E31A1C' :
        crs > 30 ? '#FD8D3C' :
        crs >  0 ? '#FEB24C' :
                   '#52b788';

    // ══ 4. UX status (no raw CRS/DFI exposed) ════════════════
    const getStatus = (crs, demand) => {
        if (demand === 0 || crs === 0)
            return { label: '✅ স্বাভাবিক',                       color: '#16a34a', bg: '#dcfce7', cls: 'badge-stable' };
        if (crs > 75)
            return { label: '🚨 সংকট: অবিলম্বে ডোনার প্রয়োজন!', color: '#dc2626', bg: '#fee2e2', cls: 'badge-critical' };
        if (crs > 50)
            return { label: '🔴 জরুরি: চাহিদা তীব্র',             color: '#dc2626', bg: '#fee2e2', cls: 'badge-critical' };
        if (crs > 30)
            return { label: '⚠️ সতর্কতা: চাহিদা বাড়ছে',          color: '#d97706', bg: '#fef3c7', cls: 'badge-warning' };
        return       { label: '🟡 মনোযোগ প্রয়োজন',               color: '#d97706', bg: '#fef3c7', cls: 'badge-warning' };
    };

    // ══ 5. Fetch GeoJSON + API in parallel ═══════════════════
    let geojsonData, heatmapData;
    try {
        const [geoRes, apiRes] = await Promise.all([
            fetch('/geojson/bangladesh.geojson'),
            fetch('/api/analytics/spatial-heatmap'),
        ]);
        geojsonData = await geoRes.json();
        heatmapData = await apiRes.json();
    } catch (err) {
        document.getElementById('loading-overlay').innerHTML =
            '<p style="color:#dc2626;font-size:0.82rem;font-family:\'Hind Siliguri\',sans-serif;">ডেটা লোড করতে সমস্যা হয়েছে।<br>পেইজ রিফ্রেশ করুন।</p>';
        return;
    }

    // ══ 6. Build GeoJSON layer ════════════════════════════════
    let totalDemand = 0;
    let topDistrict = { name: '—', demand: 0 };
    const criticalList = [];

    const geoLayer = L.geoJSON(geojsonData, {

        style(feature) {
            const enName = feature.properties?.NAME_2 || '';
            const info   = heatmapData[enName] ?? { demand: 0, crs: 0 };
            return {
                fillColor:   getColor(info.crs),
                fillOpacity: 0.85,
                color:       '#ffffff',   // ✅ Clean white borders
                weight:      1.5,
            };
        },

        onEachFeature(feature, layer) {
            const enName = feature.properties?.NAME_2 || 'Unknown';
            const bnName = toBn(enName);                              // ✅ Bengali name
            const info   = heatmapData[enName] ?? { demand: 0, crs: 0 };
            const status = getStatus(info.crs, info.demand);

            totalDemand += info.demand;
            if (info.demand > topDistrict.demand)
                topDistrict = { name: bnName, demand: info.demand };
            if (info.demand > 0)
                criticalList.push({ enName, bnName, demand: info.demand, crs: info.crs });

            // ✅ Hover tooltip (sticky, animates in) — NO click popup
            const tooltipHtml = `
                <div class="tt-card">
                    <div class="tt-name">📍 ${bnName}</div>
                    <span class="tt-status"
                          style="background:${status.bg};color:${status.color};">
                        ${status.label}
                    </span>
                    <div class="tt-meta">
                        <span>সক্রিয় রক্তের অনুরোধ</span>
                        <span>${info.demand} টি</span>
                    </div>
                    ${info.demand > 0
                        ? `<div class="tt-hint">👆 ক্লিক করে ডোনার খুঁজুন</div>`
                        : ''}
                </div>`;

            layer.bindTooltip(tooltipHtml, {
                sticky:    true,           // ✅ Follows the mouse
                permanent: false,
                direction: 'top',
                className: 'map-tooltip',  // ✅ Our animated card class
                offset:    [0, -8],
            });

            // Click → navigate to search
            if (info.demand > 0) {
                layer.on('click', () => {
                    window.location.href = '/search';
                });
            }

            // ✅ Zero-Lag Hover: ONLY mutate fillOpacity + weight + bringToFront.
            //    No CSS filter, no color change, no box-shadow — avoids full SVG
            //    layer rasterization and keeps hover at 60fps on complex boundaries.
            const BASE_OPACITY   = 0.85;
            const HOVER_OPACITY  = 1.0;
            const BASE_WEIGHT    = 1.5;
            const HOVER_WEIGHT   = 2.5;

            layer.on({
                mouseover(e) {
                    e.target.setStyle({
                        fillOpacity: HOVER_OPACITY,
                        weight:      HOVER_WEIGHT,
                    });
                    e.target.bringToFront(); // ensures hovered district renders above neighbours
                },
                mouseout(e) {
                    e.target.setStyle({
                        fillOpacity: BASE_OPACITY,
                        weight:      BASE_WEIGHT,
                    });
                },
            });
        },
    }).addTo(map);

    // ✅ Perfect framing — fit Bangladesh and lock minZoom dynamically
    function fitMapToBangladesh() {
        if (!geoLayer) return;
        map.invalidateSize();
        map.setMinZoom(1); // Reset temporarily
        map.fitBounds(geoLayer.getBounds(), { padding: [0, 0] });
        
        setTimeout(() => {
            map.setMinZoom(map.getZoom());
            map.setMaxBounds(geoLayer.getBounds().pad(0.02));
        }, 50);
    }
    
    // Call initially with a slight delay to allow layout (fonts/CSS) to settle
    setTimeout(fitMapToBangladesh, 150);
    window.addEventListener('resize', fitMapToBangladesh);

    // ══ 7. Update sidebar panels ══════════════════════════════
    document.getElementById('stat-total').textContent    = totalDemand;
    document.getElementById('stat-top-name').textContent = topDistrict.name;
    document.getElementById('stat-top-count').textContent =
        topDistrict.demand > 0 ? `${topDistrict.demand}টি সক্রিয় রিকোয়েস্ট` : 'কোনো রিকোয়েস্ট নেই';

    const criticalCount = criticalList.filter(d => d.crs > 50).length;
    document.getElementById('stat-critical-count').textContent = criticalCount;

    criticalList.sort((a, b) => b.crs - a.crs);
    const listEl = document.getElementById('critical-districts-list');
    if (criticalList.length === 0) {
        listEl.innerHTML = '<div style="font-size:0.75rem;color:#94a3b8;padding:0.5rem 0;">সারাদেশে কোনো জরুরি রিকোয়েস্ট নেই 🎉</div>';
    } else {
        listEl.innerHTML = criticalList.slice(0, 8).map(d => {
            const s = getStatus(d.crs, d.demand);
            return `<div class="district-entry">
                <span class="district-entry-name">${d.bnName}</span>
                <span class="district-entry-badge ${s.cls}">${d.demand}টি</span>
            </div>`;
        }).join('');
    }

    // Hide loader
    document.getElementById('loading-overlay').style.display = 'none';
});
</script>

</body>
</html>
