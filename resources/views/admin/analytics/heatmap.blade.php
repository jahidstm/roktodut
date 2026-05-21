@extends('layouts.app')

@section('title', 'Geo-Spatial Demand Heatmap — Admin — রক্তদূত')

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        /* ══ Layout ════════════════════════════════════════ */
        .hm-wrap {
            display: grid;
            grid-template-columns: 255px 1fr 285px;
            gap: 0;
            height: calc(100vh - 148px);
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
        }

        /* ══ Panels ═════════════════════════════════════════ */
        .hm-panel {
            background: #fff;
            overflow-y: auto;
            padding: 1rem;
        }
        .hm-panel.left  { border-right: 1px solid #e2e8f0; }
        .hm-panel.right { border-left:  1px solid #e2e8f0; display: flex; flex-direction: column; }
        .hm-panel::-webkit-scrollbar { width: 3px; }
        .hm-panel::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

        .hm-stitle {
            font-size: 0.58rem; font-weight: 800; letter-spacing: 0.1em;
            text-transform: uppercase; color: #94a3b8; margin-bottom: 0.6rem;
            padding-bottom: 0.35rem; border-bottom: 1px solid #f1f5f9;
        }
        .hm-sec { margin-bottom: 1.25rem; }

        /* Legend */
        .leg-row { display: flex; align-items: center; gap: 0.5rem; padding: 0.32rem 0.4rem; border-radius: 6px; }
        .leg-row:hover { background: #f8fafc; }
        .leg-sw { width: 14px; height: 9px; border-radius: 3px; flex-shrink: 0; border: 1px solid rgba(0,0,0,0.07); }
        .leg-tx { font-size: 0.7rem; color: #374151; }

        /* ══ Date-Range Filter Toolbar ═══════════════════════ */
        .range-toolbar {
            display: flex; gap: 0.4rem; flex-wrap: wrap;
            padding: 0.75rem 1rem;
            background: #f8fafc; border-bottom: 1px solid #e2e8f0;
        }
        .range-btn {
            font-size: 0.72rem; font-weight: 700;
            padding: 0.38rem 0.85rem; border-radius: 9999px;
            border: 1px solid #e2e8f0; background: #fff; color: #475569;
            cursor: pointer; transition: all 0.15s; white-space: nowrap;
        }
        .range-btn:hover { border-color: #14b8a6; color: #0d9488; }
        .range-btn.active {
            background: #14b8a6; border-color: #14b8a6;
            color: #fff; box-shadow: 0 2px 6px rgba(20,184,166,0.3);
        }

        /* ══ Map ════════════════════════════════════════════ */
        #admin-map {
            width: 100%; height: 100%;
            background-color: #eef2f7;
            background-image: radial-gradient(#c8d6e5 1px, transparent 1px);
            background-size: 22px 22px;
        }
        .map-col { position: relative; display: flex; flex-direction: column; }
        .map-col .range-toolbar { flex-shrink: 0; }

        /* ══ Loading overlay ════════════════════════════════ */
        #map-loading {
            position: absolute; inset: 0; z-index: 9999;
            background: rgba(238,242,247,0.88);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center; gap: 0.65rem;
            backdrop-filter: blur(4px);
        }
        .spinner {
            width: 32px; height: 32px;
            border: 3px solid #99f6e4; border-top-color: #14b8a6;
            border-radius: 50%; animation: spin 0.75s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .ld-txt { font-size: 0.76rem; color: #475569; }

        /* ══ Stat blocks ════════════════════════════════════ */
        .stat-b {
            padding: 0.6rem 0.8rem; border-radius: 10px;
            background: #f8fafc; border: 1px solid #e2e8f0;
            margin-bottom: 0.5rem; transition: box-shadow 0.12s;
        }
        .stat-b:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .stat-b-lbl { font-size: 0.57rem; font-weight: 700; color: #94a3b8; letter-spacing: 0.07em; text-transform: uppercase; margin-bottom: 0.15rem; }
        .stat-b-val { font-size: 1.4rem; font-weight: 800; color: #0f172a; line-height: 1.1; letter-spacing: -0.02em; }
        .stat-b-val.red { color: #dc2626; }
        .stat-b-val.teal { color: #0d9488; }
        .stat-b-sub { font-size: 0.6rem; color: #94a3b8; margin-top: 0.12rem; }

        /* ══ Raw metric rows ════════════════════════════════ */
        .raw-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 0.38rem 0.5rem; border-radius: 6px; font-size: 0.71rem;
            transition: background 0.1s;
        }
        .raw-row:hover { background: #f8fafc; }
        .raw-district { font-weight: 600; color: #1e293b; max-width: 95px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
        .raw-chips { display: flex; gap: 0.35rem; }
        .chip { font-size: 0.59rem; font-weight: 700; padding: 0.14rem 0.42rem; border-radius: 9999px; }
        .chip-d { background: #eff6ff; color: #2563eb; }
        .chip-f { background: #fff7ed; color: #ea580c; }
        .chip-c { background: #fef2f2; color: #dc2626; }

        /* ══ Export CSV button ══════════════════════════════ */
        .export-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.45rem;
            width: 100%; padding: 0.75rem 1rem;
            background: #0f172a; color: #fff;
            font-size: 0.78rem; font-weight: 700;
            border: none; border-radius: 10px; cursor: pointer;
            text-decoration: none; transition: background 0.15s;
        }
        .export-btn:hover { background: #1e293b; }
        .export-btn svg { flex-shrink: 0; }

        /* ══ Tooltip card ═══════════════════════════════════ */
        .leaflet-tooltip.admin-tt { background: transparent !important; border: none !important; box-shadow: none !important; padding: 0 !important; }
        .leaflet-tooltip.admin-tt::before { display: none !important; }
        .att-card {
            background: #fff; border: 1px solid #e2e8f0;
            border-radius: 14px; box-shadow: 0 8px 30px rgba(15,23,42,0.14);
            padding: 0.85rem 1.05rem; min-width: 215px; pointer-events: none;
            animation: ttIn 0.16s cubic-bezier(0.22,1,0.36,1) both;
        }
        @keyframes ttIn { from { opacity:0; transform: translateY(6px) scale(0.97); } to { opacity:1; transform: none; } }
        .att-name { font-size: 0.88rem; font-weight: 800; color: #0f172a; margin-bottom: 0.45rem; padding-bottom: 0.38rem; border-bottom: 1px solid #f1f5f9; }
        .att-badge { display: inline-block; font-size: 0.6rem; font-weight: 800; padding: 0.12rem 0.5rem; border-radius: 9999px; background: #f1f5f9; color: #475569; margin-bottom: 0.5rem; }
        .att-row  { display: flex; justify-content: space-between; font-size: 0.73rem; color: #475569; margin-bottom: 0.25rem; }
        .att-row span { font-weight: 700; color: #0f172a; }

        /* Leaflet chrome */
        .leaflet-control-attribution { display: none !important; }
        .leaflet-control-zoom { border: none !important; box-shadow: 0 2px 8px rgba(0,0,0,0.10) !important; border-radius: 10px !important; overflow: hidden; }
        .leaflet-control-zoom a { background: #fff !important; color: #374151 !important; border-color: #e2e8f0 !important; }
        .leaflet-control-zoom a:hover { background: #f0fdfa !important; color: #0d9488 !important; }
        .leaflet-pane svg path { filter: none !important; }
    </style>
@endpush

@section('content')
<div class="max-w-[1700px] mx-auto px-4 py-5">

    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
                🗺️ Geo-Spatial Demand Heatmap
                <span class="text-xs font-bold bg-purple-100 text-purple-700 px-2.5 py-1 rounded-full border border-purple-200">ADMIN VIEW</span>
            </h1>
            <p class="text-sm text-slate-500 font-medium mt-1">
                রিয়েল-টাইম কাঁচা মেট্রিক — Demand · Avg DFI · Composite Risk Score (CRS) &nbsp;|&nbsp;
                <span id="page-gen-time" class="font-bold text-slate-700">{{ $generatedAt }}</span>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('live-demand.index') }}" target="_blank"
               class="text-sm font-semibold text-slate-600 border border-slate-200 bg-white rounded-xl px-4 py-2 hover:border-slate-300 transition">
                Public View ↗
            </a>
            <a href="{{ route('admin.analytics.index') }}"
               class="text-sm font-semibold text-slate-600 border border-slate-200 bg-white rounded-xl px-4 py-2 hover:border-slate-300 transition">
                ← Analytics
            </a>
            <span class="inline-flex items-center gap-1.5 text-xs font-bold bg-red-50 border border-red-200 text-red-600 rounded-full px-3 py-1.5">
                <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span>Live
            </span>
        </div>
    </div>

    {{-- 3-Column Heatmap Panel --}}
    <div class="hm-wrap">

        {{-- LEFT: Legend + Top Demand List --}}
        <div class="hm-panel left">
            <div class="hm-sec">
                <p class="hm-stitle">রঙের স্কেল (CRS)</p>
                <div class="leg-row"><div class="leg-sw" style="background:#800026;"></div><span class="leg-tx">CRS &gt; 75 — Critical</span></div>
                <div class="leg-row"><div class="leg-sw" style="background:#E31A1C;"></div><span class="leg-tx">CRS 51–75 — High</span></div>
                <div class="leg-row"><div class="leg-sw" style="background:#FD8D3C;"></div><span class="leg-tx">CRS 31–50 — Warning</span></div>
                <div class="leg-row"><div class="leg-sw" style="background:#FEB24C;"></div><span class="leg-tx">CRS 1–30 — Elevated</span></div>
                <div class="leg-row"><div class="leg-sw" style="background:#52b788;"></div><span class="leg-tx">CRS = 0 — Safe</span></div>
            </div>
            <div class="hm-sec">
                <p class="hm-stitle">CRS ফর্মুলা</p>
                <div style="font-size:0.67rem;color:#64748b;line-height:1.7;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:0.6rem;">
                    <strong style="color:#0f172a;">CRS</strong> = (Demand × 0.6) + (DFI × 0.4)<br>
                    <strong style="color:#0f172a;">DFI</strong> = Donor Fatigue Index (0–100)
                </div>
            </div>
            <div class="hm-sec">
                <p class="hm-stitle">শীর্ষ চাহিদার জেলা</p>
                <div id="top-demand-list">
                    <p style="font-size:0.73rem;color:#94a3b8;">লোড হচ্ছে...</p>
                </div>
            </div>
        </div>

        {{-- CENTER: Date Filter Toolbar + Map --}}
        <div class="map-col">
            {{-- Date-range filter toolbar --}}
            <div class="range-toolbar" id="range-toolbar">
                <button class="range-btn {{ $dateRange === 'all_time' ? 'active' : '' }}" data-range="all_time">সকল সময়</button>
                <button class="range-btn {{ $dateRange === 'today' ? 'active' : '' }}" data-range="today">আজকে</button>
                <button class="range-btn {{ $dateRange === 'last_7_days' ? 'active' : '' }}" data-range="last_7_days">গত ৭ দিন</button>
                <button class="range-btn {{ $dateRange === 'last_30_days' ? 'active' : '' }}" data-range="last_30_days">গত ৩০ দিন</button>
                <span id="range-label" style="margin-left:auto;font-size:0.68rem;color:#94a3b8;font-weight:600;align-self:center;"></span>
            </div>

            <div style="flex:1;position:relative;">
                <div id="map-loading">
                    <div class="spinner"></div>
                    <p class="ld-txt">মানচিত্র লোড হচ্ছে...</p>
                </div>
                <div id="admin-map"></div>
            </div>
        </div>

        {{-- RIGHT: Stats + Raw Metrics + CSV Export --}}
        <div class="hm-panel right">

            <div class="hm-sec">
                <p class="hm-stitle">সামগ্রিক পরিসংখ্যান</p>
                <div class="stat-b">
                    <div class="stat-b-lbl">মোট সক্রিয় রিকোয়েস্ট</div>
                    <div class="stat-b-val red" id="stat-total">—</div>
                    <div class="stat-b-sub" id="stat-range-label">লোড হচ্ছে...</div>
                </div>
                <div class="stat-b">
                    <div class="stat-b-lbl">Critical জেলা (CRS &gt; 50)</div>
                    <div class="stat-b-val red" id="stat-critical">—</div>
                    <div class="stat-b-sub">উচ্চ ঝুঁকিপূর্ণ</div>
                </div>
                <div class="stat-b">
                    <div class="stat-b-lbl">Warning জেলা (CRS 31–50)</div>
                    <div class="stat-b-val" style="color:#ea580c;" id="stat-warning">—</div>
                    <div class="stat-b-sub">সতর্কতার জেলা</div>
                </div>
            </div>

            <div class="hm-sec" style="flex:1;overflow:hidden;display:flex;flex-direction:column;">
                <p class="hm-stitle">Raw District Metrics</p>
                <div id="raw-metrics-list" style="overflow-y:auto;flex:1;">
                    <p style="font-size:0.73rem;color:#94a3b8;">লোড হচ্ছে...</p>
                </div>
            </div>

            {{-- ✅ Export CSV Button --}}
            <div style="padding:0.75rem 0 0.25rem;">
                <a id="export-btn" href="{{ route('admin.heatmap.export') }}?range={{ $dateRange }}"
                   class="export-btn">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export CSV ডাউনলোড করুন
                </a>
                <p style="font-size:0.62rem;color:#94a3b8;text-align:center;margin-top:0.4rem;">
                    নির্বাচিত তারিখ ফিল্টার অনুযায়ী ৬৪ জেলার ডেটা
                </p>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    // ══ EN → Bengali ═══════════════════════════════════════
    const EN_TO_BN = {
        'Comilla':'কুমিল্লা','Feni':'ফেনী','Brahamanbaria':'ব্রাহ্মণবাড়িয়া','Rangamati':'রাঙ্গামাটি',
        'Noakhali':'নোয়াখালী','Chandpur':'চাঁদপুর','Lakshmipur':'লক্ষ্মীপুর','Chittagong':'চট্টগ্রাম',
        "Cox'SBazar":'কক্সবাজার','Khagrachhari':'খাগড়াছড়ি','Bandarban':'বান্দরবান','Sirajganj':'সিরাজগঞ্জ',
        'Pabna':'পাবনা','Bogra':'বগুড়া','Rajshahi':'রাজশাহী','Natore':'নাটোর','Joypurhat':'জয়পুরহাট',
        'Nawabganj':'চাঁপাইনবাবগঞ্জ','Naogaon':'নওগাঁ','Jessore':'যশোর','Satkhira':'সাতক্ষীরা',
        'Meherpur':'মেহেরপুর','Narail':'নড়াইল','Chuadanga':'চুয়াডাঙ্গা','Kushtia':'কুষ্টিয়া',
        'Magura':'মাগুরা','Khulna':'খুলনা','Bagerhat':'বাগেরহাট','Jhenaidah':'ঝিনাইদহ',
        'Jhalokati':'ঝালকাঠি','Patuakhali':'পটুয়াখালী','Pirojpur':'পিরোজপুর','Barisal':'বরিশাল',
        'Bhola':'ভোলা','Barguna':'বরগুনা','Sylhet':'সিলেট','Maulvibazar':'মৌলভীবাজার',
        'Habiganj':'হবিগঞ্জ','Sunamganj':'সুনামগঞ্জ','Narsingdi':'নরসিংদী','Gazipur':'গাজীপুর',
        'Shariatpur':'শরীয়তপুর','Narayanganj':'নারায়ণগঞ্জ','Tangail':'টাঙ্গাইল','Kishoreganj':'কিশোরগঞ্জ',
        'Manikganj':'মানিকগঞ্জ','Dhaka':'ঢাকা','Munshiganj':'মুন্সিগঞ্জ','Rajbari':'রাজবাড়ী',
        'Madaripur':'মাদারীপুর','Gopalganj':'গোপালগঞ্জ','Faridpur':'ফরিদপুর','Panchagarh':'পঞ্চগড়',
        'Dinajpur':'দিনাজপুর','Lalmonirhat':'লালমনিরহাট','Nilphamari':'নীলফামারী','Gaibandha':'গাইবান্ধা',
        'Thakurgaon':'ঠাকুরগাঁও','Rangpur':'রংপুর','Kurigram':'কুড়িগ্রাম','Sherpur':'শেরপুর',
        'Mymensingh':'ময়মনসিংহ','Jamalpur':'জামালপুর','Netrakona':'নেত্রকোণা',
    };
    const bn = n => EN_TO_BN[n] || n;

    const rangeBn = {
        all_time:     'সকল সময়কাল',
        today:        'শুধুমাত্র আজকের ডেটা',
        last_7_days:  'গত ৭ দিনের ডেটা',
        last_30_days: 'গত ৩০ দিনের ডেটা',
    };

    // ══ Colour ramp ═════════════════════════════════════════
    const getColor = c =>
        c > 75 ? '#800026' : c > 50 ? '#E31A1C' :
        c > 30 ? '#FD8D3C' : c > 0  ? '#FEB24C' : '#52b788';

    // ══ Init Leaflet (once) ══════════════════════════════════
    const map = L.map('admin-map', {
        center: [23.685, 90.356], zoom: 7,
        attributionControl: false, maxBoundsViscosity: 1.0,
    });

    const BASE_OP = 0.85, HOVER_OP = 1.0, BASE_W = 1.5, HOVER_W = 2.5;
    let geoLayer = null;
    let geojsonData = null;
    let currentRange = '{{ $dateRange }}';

    // ══ Fetch GeoJSON once ═══════════════════════════════════
    async function loadGeoJSON() {
        const res = await fetch('/geojson/bangladesh.geojson');
        return await res.json();
    }

    // ══ Fetch heatmap data by range ══════════════════════════
    async function fetchHeatmap(range) {
        const res = await fetch(`/api/analytics/spatial-heatmap?range=${range}`);
        return await res.json();
    }

    // ══ Render / re-render GeoJSON layer ════════════════════
    function renderLayer(heatmapData) {
        if (geoLayer) { map.removeLayer(geoLayer); geoLayer = null; }

        const topList = [];
        let totalDemand = 0, criticalCount = 0, warningCount = 0;

        geoLayer = L.geoJSON(geojsonData, {
            style(feature) {
                const en   = feature.properties?.NAME_2 || '';
                const info = heatmapData[en] ?? { demand: 0, crs: 0, avg_dfi: 0 };
                return { fillColor: getColor(info.crs), fillOpacity: BASE_OP, color: '#ffffff', weight: BASE_W };
            },
            onEachFeature(feature, layer) {
                const en   = feature.properties?.NAME_2 || 'Unknown';
                const info = heatmapData[en] ?? { demand: 0, crs: 0, avg_dfi: 0 };

                totalDemand  += info.demand;
                if (info.crs > 50) criticalCount++;
                if (info.crs > 30 && info.crs <= 50) warningCount++;
                if (info.demand > 0) topList.push({ en, demand: info.demand, crs: info.crs });

                // ✅ Admin tooltip — raw metrics
                layer.bindTooltip(`
                    <div class="att-card">
                        <div class="att-name">📍 ${bn(en)} <span style="font-size:0.63rem;color:#94a3b8;">(${en})</span></div>
                        <span class="att-badge">🔐 Admin Raw Metrics</span>
                        <div class="att-row"><span>Demand</span><span>${info.demand} টি</span></div>
                        <div class="att-row"><span>Avg DFI</span><span>${info.avg_dfi}</span></div>
                        <div class="att-row"><span>CRS</span><span style="color:${getColor(info.crs)};font-size:0.85rem;font-weight:800;">${info.crs}</span></div>
                    </div>`, {
                    sticky: true, permanent: false,
                    direction: 'top', className: 'admin-tt', offset: [0, -8],
                });

                // Zero-lag hover
                layer.on({
                    mouseover(e) { e.target.setStyle({ fillOpacity: HOVER_OP, weight: HOVER_W }); e.target.bringToFront(); },
                    mouseout(e)  { e.target.setStyle({ fillOpacity: BASE_OP,  weight: BASE_W  }); },
                });
            },
        }).addTo(map);

        // Update sidebar stats
        document.getElementById('stat-total').textContent    = totalDemand;
        document.getElementById('stat-critical').textContent = criticalCount;
        document.getElementById('stat-warning').textContent  = warningCount;
        document.getElementById('stat-range-label').textContent = rangeBn[currentRange] || '';

        // Top demand list (left panel)
        topList.sort((a,b) => b.demand - a.demand);
        document.getElementById('top-demand-list').innerHTML = topList.slice(0,8).map(d => `
            <div class="raw-row">
                <span class="raw-district" title="${bn(d.en)}">${bn(d.en)}</span>
                <div class="raw-chips">
                    <span class="chip chip-d">${d.demand}টি</span>
                    <span class="chip chip-c">CRS:${d.crs}</span>
                </div>
            </div>
        `).join('') || '<p style="font-size:0.73rem;color:#94a3b8;">কোনো সক্রিয় রিকোয়েস্ট নেই।</p>';

        // Raw metrics (right panel)
        const sorted = Object.entries(heatmapData)
            .filter(([,d]) => d.demand > 0)
            .sort(([,a],[,b]) => b.crs - a.crs);

        document.getElementById('raw-metrics-list').innerHTML = sorted.map(([enName, d]) => `
            <div class="raw-row">
                <span class="raw-district" title="${bn(enName)}">${bn(enName)}</span>
                <div class="raw-chips">
                    <span class="chip chip-d">D:${d.demand}</span>
                    <span class="chip chip-f">DFI:${d.avg_dfi}</span>
                    <span class="chip chip-c">CRS:${d.crs}</span>
                </div>
            </div>
        `).join('') || '<p style="font-size:0.73rem;color:#94a3b8;">কোনো সক্রিয় রিকোয়েস্ট নেই।</p>';

        // Update export link
        document.getElementById('export-btn').href = `/admin/heatmap/export?range=${currentRange}`;
    }

    // ══ Show / hide loading overlay ══════════════════════════
    function showLoading(show) {
        document.getElementById('map-loading').style.display = show ? 'flex' : 'none';
    }

    // ══ Switch range ═════════════════════════════════════════
    async function switchRange(range) {
        currentRange = range;

        // Update toolbar button states
        document.querySelectorAll('.range-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.range === range);
        });

        showLoading(true);
        try {
            const data = await fetchHeatmap(range);
            renderLayer(data);
            document.getElementById('range-label').textContent = rangeBn[range] || '';
        } catch (err) {
            console.error('[Heatmap] fetch failed:', err);
        } finally {
            showLoading(false);
        }
    }

    // ══ Bootstrap ════════════════════════════════════════════
    (async function () {
        try {
            const [geo, initialData] = await Promise.all([
                loadGeoJSON(),
                fetchHeatmap(currentRange),
            ]);
            geojsonData = geo;
            renderLayer(initialData);

            // Fit + lock zoom
            map.fitBounds(geoLayer.getBounds(), { padding: [16, 16] });
            map.setMinZoom(map.getZoom());
            map.setMaxBounds(geoLayer.getBounds().pad(0.08));

            document.getElementById('range-label').textContent = rangeBn[currentRange] || '';
        } finally {
            showLoading(false);
        }

        // ── Date-range button click ───────────────────────
        document.getElementById('range-toolbar').addEventListener('click', e => {
            const btn = e.target.closest('.range-btn');
            if (!btn || btn.dataset.range === currentRange) return;
            switchRange(btn.dataset.range);
        });
    })();
})();
</script>
@endpush
