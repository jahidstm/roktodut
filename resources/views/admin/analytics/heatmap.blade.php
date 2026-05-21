@extends('layouts.app')

@section('title', 'Geo-Spatial Demand Heatmap — Admin — রক্তদূত')

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        /* ── Admin Heatmap Wrapper ──────────────────────────── */
        .heatmap-wrap {
            display: grid;
            grid-template-columns: 240px 1fr 270px;
            gap: 0;
            height: calc(100vh - 130px);  /* account for site header */
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }

        /* ── Panels ─────────────────────────────────────────── */
        .hm-panel {
            background: #ffffff;
            overflow-y: auto;
            padding: 1rem;
        }
        .hm-panel.left  { border-right: 1px solid #e2e8f0; }
        .hm-panel.right { border-left:  1px solid #e2e8f0; }
        .hm-panel::-webkit-scrollbar { width: 3px; }
        .hm-panel::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

        .hm-section { margin-bottom: 1.25rem; }
        .hm-section-title {
            font-size: 0.58rem; font-weight: 800;
            letter-spacing: 0.1em; text-transform: uppercase;
            color: #94a3b8; margin-bottom: 0.6rem;
            padding-bottom: 0.35rem; border-bottom: 1px solid #f1f5f9;
        }

        /* Legend */
        .legend-row {
            display: flex; align-items: center; gap: 0.5rem;
            padding: 0.35rem 0.4rem; border-radius: 6px; margin-bottom: 0.1rem;
        }
        .legend-row:hover { background: #f8fafc; }
        .legend-swatch { width: 15px; height: 9px; border-radius: 3px; flex-shrink: 0; border: 1px solid rgba(0,0,0,0.07); }
        .legend-text { font-size: 0.71rem; color: #374151; }

        /* Stat blocks */
        .stat-b {
            padding: 0.65rem 0.8rem; border-radius: 10px;
            background: #f8fafc; border: 1px solid #e2e8f0;
            margin-bottom: 0.5rem;
        }
        .stat-b-label { font-size: 0.58rem; font-weight: 700; color: #94a3b8; letter-spacing: 0.07em; text-transform: uppercase; margin-bottom: 0.18rem; }
        .stat-b-value { font-size: 1.45rem; font-weight: 800; color: #0f172a; line-height: 1.1; letter-spacing: -0.02em; }
        .stat-b-value.red { color: #dc2626; }
        .stat-b-sub { font-size: 0.6rem; color: #94a3b8; margin-top: 0.15rem; }

        /* Raw metric table in right panel */
        .raw-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 0.4rem 0.5rem; border-radius: 6px; margin-bottom: 0.12rem;
            font-size: 0.72rem; transition: background 0.1s;
        }
        .raw-row:hover { background: #f8fafc; }
        .raw-district { font-weight: 600; color: #1e293b; max-width: 100px; truncate: true; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
        .raw-metrics { display: flex; gap: 0.4rem; }
        .raw-chip {
            font-size: 0.6rem; font-weight: 700;
            padding: 0.15rem 0.45rem; border-radius: 9999px;
        }
        .chip-demand { background: #eff6ff; color: #2563eb; }
        .chip-dfi    { background: #fff7ed; color: #ea580c; }
        .chip-crs    { background: #fef2f2; color: #dc2626; }

        /* ── Map ─────────────────────────────────────────────── */
        #admin-map {
            width: 100%; height: 100%;
            background-color: #eef2f7;
            background-image: radial-gradient(#c8d6e5 1px, transparent 1px);
            background-size: 22px 22px;
        }

        /* ── Loading overlay ─────────────────────────────────── */
        #map-loading {
            position: absolute; inset: 0;
            background: rgba(238,242,247,0.9);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center; gap: 0.7rem;
            z-index: 9999; backdrop-filter: blur(4px);
        }
        .spinner {
            width: 34px; height: 34px;
            border: 3px solid #fecaca; border-top-color: #dc2626;
            border-radius: 50%; animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Tooltip card ────────────────────────────────────── */
        .leaflet-tooltip.admin-tt { background: transparent !important; border: none !important; box-shadow: none !important; padding: 0 !important; }
        .leaflet-tooltip.admin-tt::before { display: none !important; }
        .att-card {
            background: #ffffff; border: 1px solid #e2e8f0;
            border-radius: 14px; box-shadow: 0 8px 30px rgba(15,23,42,0.14);
            padding: 0.9rem 1.1rem; min-width: 210px; pointer-events: none;
            animation: ttIn 0.16s cubic-bezier(0.22, 1, 0.36, 1) both;
        }
        @keyframes ttIn { from { opacity:0; transform: translateY(6px) scale(0.97); } to { opacity:1; transform: none; } }
        .att-name { font-size: 0.9rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem; padding-bottom: 0.4rem; border-bottom: 1px solid #f1f5f9; }
        .att-row  { display: flex; justify-content: space-between; font-size: 0.74rem; color: #475569; margin-bottom: 0.28rem; }
        .att-row span { font-weight: 700; color: #0f172a; }
        .att-admin-badge {
            display: inline-block; font-size: 0.62rem; font-weight: 800;
            padding: 0.15rem 0.55rem; border-radius: 9999px;
            background: #f1f5f9; color: #475569; margin-bottom: 0.55rem;
        }

        /* Leaflet overrides */
        .leaflet-control-attribution { display: none !important; }
        .leaflet-control-zoom { border: none !important; box-shadow: 0 2px 8px rgba(0,0,0,0.10) !important; border-radius: 10px !important; overflow: hidden; }
        .leaflet-control-zoom a { background: #fff !important; color: #374151 !important; border-color: #e2e8f0 !important; }
        .leaflet-control-zoom a:hover { background: #f8fafc !important; color: #dc2626 !important; }
        .leaflet-pane svg path { filter: none !important; }
    </style>
@endpush

@section('content')
<div class="max-w-[1600px] mx-auto px-4 py-6">

    {{-- Page header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
                🗺️ Geo-Spatial Demand Heatmap
                <span class="text-xs font-bold bg-purple-100 text-purple-700 px-2.5 py-1 rounded-full border border-purple-200">ADMIN VIEW</span>
            </h1>
            <p class="text-sm text-slate-500 font-medium mt-1">
                Raw metrics visible. Generated: <span class="font-bold text-slate-700">{{ $generatedAt }}</span> &nbsp;|&nbsp;
                Cache TTL: 15 minutes &nbsp;|&nbsp;
                <a href="{{ route('live-demand.index') }}" target="_blank" class="text-blue-600 hover:underline font-semibold">Public View ↗</a>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.analytics.index') }}"
               class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl px-4 py-2 hover:border-slate-300 transition">
                ← Analytics Dashboard
            </a>
            <span class="inline-flex items-center gap-1.5 text-xs font-bold bg-red-50 border border-red-200 text-red-600 rounded-full px-3 py-1.5">
                <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span>
                Live Data
            </span>
        </div>
    </div>

    {{-- 3-Column Heatmap BI Panel --}}
    <div class="heatmap-wrap">

        {{-- LEFT: Legend + Top Demand List --}}
        <div class="hm-panel left">

            <div class="hm-section">
                <p class="hm-section-title">রঙের স্কেল (CRS)</p>
                <div class="legend-row"><div class="legend-swatch" style="background:#800026;"></div><span class="legend-text">CRS &gt; 75 — Critical</span></div>
                <div class="legend-row"><div class="legend-swatch" style="background:#E31A1C;"></div><span class="legend-text">CRS 51–75 — High</span></div>
                <div class="legend-row"><div class="legend-swatch" style="background:#FD8D3C;"></div><span class="legend-text">CRS 31–50 — Warning</span></div>
                <div class="legend-row"><div class="legend-swatch" style="background:#FEB24C;"></div><span class="legend-text">CRS 1–30 — Elevated</span></div>
                <div class="legend-row"><div class="legend-swatch" style="background:#52b788;"></div><span class="legend-text">CRS = 0 — Safe (No demand)</span></div>
            </div>

            <div class="hm-section">
                <p class="hm-section-title">মেট্রিক সংক্ষেপ</p>
                <div style="font-size:0.68rem;color:#64748b;line-height:1.6;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:0.6rem;">
                    <strong style="color:#0f172a;">CRS</strong> = (Demand × 0.6) + (DFI × 0.4)<br>
                    <strong style="color:#0f172a;">DFI</strong> = Donor Fatigue Index (0–100)<br>
                    <strong style="color:#0f172a;">Demand</strong> = Active pending requests
                </div>
            </div>

            <div class="hm-section">
                <p class="hm-section-title">শীর্ষ চাহিদার জেলা</p>
                <div id="top-demand-list">
                    <p style="font-size:0.73rem;color:#94a3b8;">লোড হচ্ছে...</p>
                </div>
            </div>
        </div>

        {{-- CENTER: Map --}}
        <div style="position:relative;">
            <div id="map-loading">
                <div class="spinner"></div>
                <p style="font-size:0.78rem;color:#64748b;font-family:'Hind Siliguri',sans-serif;">মানচিত্র লোড হচ্ছে...</p>
            </div>
            <div id="admin-map"></div>
        </div>

        {{-- RIGHT: Raw Metrics Table + Stats --}}
        <div class="hm-panel right">

            <div class="hm-section">
                <p class="hm-section-title">সামগ্রিক পরিসংখ্যান</p>
                <div class="stat-b">
                    <div class="stat-b-label">মোট সক্রিয় রিকোয়েস্ট</div>
                    <div class="stat-b-value red">{{ $totalDemand }}</div>
                    <div class="stat-b-sub">সারাদেশে এই মুহূর্তে</div>
                </div>
                <div class="stat-b">
                    <div class="stat-b-label">Critical জেলা (CRS > 50)</div>
                    <div class="stat-b-value red">{{ $criticalCount }}</div>
                    <div class="stat-b-sub">উচ্চ ঝুঁকিপূর্ণ এলাকা</div>
                </div>
                <div class="stat-b">
                    <div class="stat-b-label">মোট জেলা ট্র্যাক করা হচ্ছে</div>
                    <div class="stat-b-value">{{ count($heatmapData) }}</div>
                    <div class="stat-b-sub">GADM 4.1 GeoJSON</div>
                </div>
            </div>

            <div class="hm-section">
                <p class="hm-section-title">Raw District Metrics (all)</p>
                <div id="raw-metrics-list">
                    @php
                        $sorted = collect($heatmapData)
                            ->map(fn($v, $k) => array_merge($v, ['name' => $k]))
                            ->sortByDesc('crs')
                            ->values();
                    @endphp
                    @foreach($sorted as $d)
                        @if($d['demand'] > 0)
                        <div class="raw-row">
                            <span class="raw-district" title="{{ $d['name'] }}">{{ $d['name'] }}</span>
                            <div class="raw-metrics">
                                <span class="raw-chip chip-demand">D:{{ $d['demand'] }}</span>
                                <span class="raw-chip chip-dfi">DFI:{{ $d['avg_dfi'] }}</span>
                                <span class="raw-chip chip-crs">CRS:{{ $d['crs'] }}</span>
                            </div>
                        </div>
                        @endif
                    @endforeach
                    @if($totalDemand === 0)
                        <p style="font-size:0.73rem;color:#94a3b8;padding:0.4rem 0;">কোনো সক্রিয় রিকোয়েস্ট নেই।</p>
                    @endif
                </div>
            </div>

        </div>

    </div>{{-- /.heatmap-wrap --}}
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async () => {

    // ── GADM EN → Bengali name ─────────────────────────────────
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
    const toBn = (n) => EN_TO_BN[n] || n;

    // ── Colour ramp ────────────────────────────────────────────
    const getColor = (crs) =>
        crs > 75 ? '#800026' : crs > 50 ? '#E31A1C' :
        crs > 30 ? '#FD8D3C' : crs > 0  ? '#FEB24C' : '#52b788';

    // ── Fetch heatmap data (already computed by controller, but we
    //    still call the API so the map JS is self-contained) ────
    let geojsonData, heatmapData;
    try {
        const [geoRes, apiRes] = await Promise.all([
            fetch('/geojson/bangladesh.geojson'),
            fetch('/api/analytics/spatial-heatmap'),
        ]);
        geojsonData  = await geoRes.json();
        heatmapData  = await apiRes.json();
    } catch {
        document.getElementById('map-loading').innerHTML =
            '<p style="color:#dc2626;font-size:0.8rem;">ডেটা লোড ব্যর্থ। রিফ্রেশ করুন।</p>';
        return;
    }

    // ── Leaflet map ────────────────────────────────────────────
    const map = L.map('admin-map', {
        center: [23.685, 90.356], zoom: 7,
        attributionControl: false, maxBoundsViscosity: 1.0,
    });

    const BASE_OP = 0.85, HOVER_OP = 1.0, BASE_W = 1.5, HOVER_W = 2.5;

    const topList = [];

    const geoLayer = L.geoJSON(geojsonData, {
        style(feature) {
            const en   = feature.properties?.NAME_2 || '';
            const info = heatmapData[en] ?? { demand: 0, crs: 0, avg_dfi: 0 };
            return { fillColor: getColor(info.crs), fillOpacity: BASE_OP, color: '#ffffff', weight: BASE_W };
        },
        onEachFeature(feature, layer) {
            const en   = feature.properties?.NAME_2 || 'Unknown';
            const bn   = toBn(en);
            const info = heatmapData[en] ?? { demand: 0, crs: 0, avg_dfi: 0 };

            if (info.demand > 0) topList.push({ bn, ...info });

            // ✅ Admin tooltip — raw metrics EXPOSED
            layer.bindTooltip(`
                <div class="att-card">
                    <div class="att-name">📍 ${bn} <span style="font-size:0.65rem;color:#94a3b8;font-weight:500;">(${en})</span></div>
                    <span class="att-admin-badge">🔐 Admin Raw Metrics</span>
                    <div class="att-row"><span>Demand</span>          <span>${info.demand} টি</span></div>
                    <div class="att-row"><span>Avg DFI</span>         <span>${info.avg_dfi}</span></div>
                    <div class="att-row"><span>Composite Risk (CRS)</span><span style="color:${getColor(info.crs)};font-size:0.85rem;">${info.crs}</span></div>
                </div>`, {
                sticky: true, permanent: false,
                direction: 'top', className: 'admin-tt', offset: [0, -8],
            });

            // Zero-lag hover — fillOpacity + weight only, NO filter
            layer.on({
                mouseover(e) { e.target.setStyle({ fillOpacity: HOVER_OP, weight: HOVER_W }); e.target.bringToFront(); },
                mouseout(e)  { e.target.setStyle({ fillOpacity: BASE_OP,  weight: BASE_W  }); },
            });
        },
    }).addTo(map);

    map.fitBounds(geoLayer.getBounds(), { padding: [16, 16] });
    map.setMinZoom(map.getZoom());
    map.setMaxBounds(geoLayer.getBounds().pad(0.08));

    // ── Top demand list (left panel) ─────────────────────────
    topList.sort((a, b) => b.demand - a.demand);
    const listEl = document.getElementById('top-demand-list');
    listEl.innerHTML = topList.slice(0, 8).map(d =>
        `<div class="raw-row">
            <span class="raw-district" title="${d.bn}">${d.bn}</span>
            <div class="raw-metrics">
                <span class="raw-chip chip-demand">${d.demand}টি</span>
                <span class="raw-chip chip-crs">CRS:${d.crs}</span>
            </div>
        </div>`
    ).join('') || '<p style="font-size:0.73rem;color:#94a3b8;">কোনো সক্রিয় রিকোয়েস্ট নেই।</p>';

    document.getElementById('map-loading').style.display = 'none';
});
</script>
@endpush
