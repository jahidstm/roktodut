<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>লাইভ রক্তের চাহিদা মানচিত্র — RoktoDut</title>
    <meta name="description" content="বাংলাদেশের জেলাভিত্তিক রিয়েল-টাইম রক্তের জরুরি চাহিদার ইন্টারেক্টিভ মানচিত্র।">

    {{-- Leaflet.js CDN --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap');

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Hind Siliguri', 'Inter', sans-serif;
            background: #f1f5f9;
            color: #1f2937;
            min-height: 100vh;
        }

        /* ── Header ──────────────────────────────────────── */
        .heatmap-header {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .brand-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, #ef4444, #b91c1c);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
            box-shadow: 0 2px 8px rgba(239,68,68,0.3);
        }
        .brand-text h1 {
            font-size: 1rem;
            font-weight: 700;
            color: #111827;
            line-height: 1.2;
        }
        .brand-text p {
            font-size: 0.7rem;
            color: #6b7280;
            font-family: 'Inter', sans-serif;
        }
        .live-badge {
            display: flex; align-items: center; gap: 0.45rem;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 9999px;
            padding: 0.3rem 0.9rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #dc2626;
        }
        .live-dot {
            width: 7px; height: 7px;
            background: #ef4444;
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.4; transform: scale(0.75); }
        }

        /* ── Layout ──────────────────────────────────────── */
        .heatmap-layout {
            display: grid;
            grid-template-columns: 300px 1fr;
            height: calc(100vh - 65px);
        }

        /* ── Sidebar ──────────────────────────────────────── */
        .sidebar {
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            overflow-y: auto;
            padding: 1.25rem;
        }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

        .sidebar-section { margin-bottom: 1.5rem; }
        .sidebar-title {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 0.75rem;
            font-family: 'Inter', sans-serif;
        }

        /* Legend */
        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            margin-bottom: 0.6rem;
        }
        .legend-swatch {
            width: 16px; height: 10px;
            border-radius: 3px;
            flex-shrink: 0;
            border: 1px solid rgba(0,0,0,0.08);
        }
        .legend-label { font-size: 0.78rem; color: #374151; }

        /* Stat Cards */
        .stat-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.85rem 1rem;
            margin-bottom: 0.6rem;
            transition: box-shadow 0.2s, border-color 0.2s;
        }
        .stat-card:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-color: #cbd5e1;
        }
        .stat-label { font-size: 0.68rem; color: #6b7280; margin-bottom: 0.2rem; font-family: 'Inter', sans-serif; }
        .stat-value { font-size: 1.4rem; font-weight: 700; color: #111827; font-family: 'Inter', sans-serif; }
        .stat-sub   { font-size: 0.68rem; color: #9ca3af; margin-top: 0.15rem; }

        /* District rows */
        .district-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.55rem 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .district-row:last-child { border-bottom: none; }
        .district-name { font-size: 0.8rem; color: #374151; }
        .district-badge {
            font-size: 0.68rem;
            font-weight: 600;
            padding: 0.18rem 0.55rem;
            border-radius: 9999px;
            font-family: 'Inter', sans-serif;
        }
        .badge-critical { background: #fee2e2; color: #dc2626; }
        .badge-warning  { background: #fef3c7; color: #d97706; }
        .badge-stable   { background: #d1fae5; color: #059669; }

        /* ── Map ──────────────────────────────────────── */
        #map {
            width: 100%;
            height: 100%;
            /* ✅ Light canvas — NO world tiles */
            background: #eef2f7;
        }

        /* ── Custom Leaflet popup overrides (light) ──── */
        .leaflet-popup-content-wrapper {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            color: #1f2937;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            padding: 0;
        }
        .leaflet-popup-content { margin: 0 !important; }
        .leaflet-popup-tip     { background: #ffffff; }
        .leaflet-popup-close-button { color: #6b7280 !important; right: 10px !important; top: 10px !important; }

        .popup-inner { padding: 1.1rem 1.3rem; min-width: 210px; }
        .popup-district {
            font-size: 0.95rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.65rem;
            padding-bottom: 0.55rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .popup-status-badge {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.2rem 0.65rem;
            border-radius: 9999px;
            margin-bottom: 0.7rem;
            letter-spacing: 0.03em;
            font-family: 'Inter', sans-serif;
        }
        .popup-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.78rem;
            color: #6b7280;
            margin-bottom: 0.35rem;
        }
        .popup-row span:last-child { color: #111827; font-weight: 600; }
        .popup-cta {
            margin-top: 0.7rem;
            padding-top: 0.55rem;
            border-top: 1px solid #f1f5f9;
            font-size: 0.72rem;
            color: #6b7280;
        }
        .popup-cta a {
            color: #ef4444;
            text-decoration: none;
            font-weight: 600;
        }

        /* ── Loading Overlay ──────────────────────────── */
        #loading-overlay {
            position: absolute;
            inset: 0;
            background: rgba(241,245,249,0.92);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            gap: 0.75rem;
            backdrop-filter: blur(3px);
        }
        .spinner {
            width: 40px; height: 40px;
            border: 3px solid #e2e8f0;
            border-top-color: #ef4444;
            border-radius: 50%;
            animation: spin 0.85s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .loading-text { font-size: 0.82rem; color: #6b7280; font-family: 'Inter', sans-serif; }

        /* ── Responsive ───────────────────────────────── */
        @media (max-width: 768px) {
            .heatmap-layout { grid-template-columns: 1fr; grid-template-rows: auto 1fr; }
            .sidebar { max-height: 200px; }
        }

        /* Hide Leaflet attribution & zoom control styling */
        .leaflet-control-attribution { display: none !important; }
        .leaflet-control-zoom a {
            background: #ffffff !important;
            color: #374151 !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1) !important;
        }
        .leaflet-control-zoom a:hover { background: #f1f5f9 !important; }
    </style>
</head>
<body>

{{-- ── Header ──────────────────────────────────────── --}}
<header class="heatmap-header">
    <div class="brand">
        <div class="brand-icon">🩸</div>
        <div class="brand-text">
            <h1>লাইভ রক্তের চাহিদা মানচিত্র</h1>
            <p>Bangladesh District-Level Emergency Blood Demand</p>
        </div>
    </div>
    <div class="live-badge">
        <div class="live-dot"></div>
        রিয়েল-টাইম আপডেট
    </div>
</header>

{{-- ── Main Layout ─────────────────────────────────── --}}
<div class="heatmap-layout">

    {{-- Sidebar --}}
    <aside class="sidebar">

        {{-- Legend --}}
        <div class="sidebar-section">
            <p class="sidebar-title">রঙের অর্থ</p>
            <div class="legend-item">
                <div class="legend-swatch" style="background:#800026;"></div>
                <span class="legend-label">🚨 সংকট — অবিলম্বে ডোনার প্রয়োজন</span>
            </div>
            <div class="legend-item">
                <div class="legend-swatch" style="background:#E31A1C;"></div>
                <span class="legend-label">🔴 জরুরি — চাহিদা তীব্র</span>
            </div>
            <div class="legend-item">
                <div class="legend-swatch" style="background:#FD8D3C;"></div>
                <span class="legend-label">🟠 সতর্কতা — চাহিদা বাড়ছে</span>
            </div>
            <div class="legend-item">
                <div class="legend-swatch" style="background:#FEB24C;"></div>
                <span class="legend-label">🟡 মনোযোগ প্রয়োজন</span>
            </div>
            <div class="legend-item">
                <div class="legend-swatch" style="background:#52b788;"></div>
                <span class="legend-label">🟢 স্বাভাবিক — কোনো জরুরি অনুরোধ নেই</span>
            </div>
        </div>

        {{-- Stats --}}
        <div class="sidebar-section">
            <p class="sidebar-title">সামগ্রিক পরিসংখ্যান</p>
            <div class="stat-card">
                <div class="stat-label">মোট সক্রিয় রিকোয়েস্ট</div>
                <div class="stat-value" id="stat-total-requests">—</div>
                <div class="stat-sub">সারাদেশে এই মুহূর্তে</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">সর্বোচ্চ চাহিদার জেলা</div>
                <div class="stat-value" style="font-size:1rem;" id="stat-top-district">—</div>
                <div class="stat-sub" id="stat-top-district-count">লোড হচ্ছে...</div>
            </div>
        </div>

        {{-- Top Critical Districts --}}
        <div class="sidebar-section">
            <p class="sidebar-title">জরুরি জেলাসমূহ</p>
            <div id="top-districts-list">
                <div style="font-size:0.78rem;color:#9ca3af;">লোড হচ্ছে...</div>
            </div>
        </div>

    </aside>

    {{-- Map Container --}}
    <div style="position:relative;">
        <div id="loading-overlay">
            <div class="spinner"></div>
            <p class="loading-text">মানচিত্র লোড হচ্ছে...</p>
        </div>
        <div id="map"></div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {

    // ── 1. Initialize Leaflet Map (NO tile layer) ──────────────────
    const map = L.map('map', {
        center:            [23.685, 90.356],
        zoom:              7,
        zoomControl:       true,
        attributionControl: false,
        // ✅ Restrict interaction to Bangladesh bounds after load
        minZoom: 6,
        maxZoom: 10,
    });

    // ✅ NO L.tileLayer — we only render GeoJSON polygons on a plain canvas

    // ── 2. CRS Color Scale (0–100) ─────────────────────────────────
    function getColor(crs) {
        return crs > 75 ? '#800026' :
               crs > 50 ? '#E31A1C' :
               crs > 30 ? '#FD8D3C' :
               crs > 0  ? '#FEB24C' :
                          '#52b788';   // Safe Green (demand === 0)
    }

    // ── 3. User-friendly status labels (NO raw DFI/CRS exposed) ───
    function getStatusLabel(crs, demand) {
        if (demand === 0 || crs === 0) {
            return { label: '✅ স্বাভাবিক', color: '#059669', bg: '#d1fae5', cls: 'badge-stable' };
        }
        if (crs > 75) {
            return { label: '🚨 সংকট: অবিলম্বে ডোনার প্রয়োজন!', color: '#dc2626', bg: '#fee2e2', cls: 'badge-critical' };
        }
        if (crs > 50) {
            return { label: '🔴 জরুরি: চাহিদা তীব্র', color: '#dc2626', bg: '#fee2e2', cls: 'badge-critical' };
        }
        if (crs > 30) {
            return { label: '⚠️ সতর্কতা: চাহিদা বাড়ছে', color: '#d97706', bg: '#fef3c7', cls: 'badge-warning' };
        }
        return { label: '🟡 মনোযোগ প্রয়োজন', color: '#d97706', bg: '#fef3c7', cls: 'badge-warning' };
    }

    // ── 4. Fetch GeoJSON + API in parallel ────────────────────────
    let geojsonData, heatmapData;
    try {
        const [geoRes, apiRes] = await Promise.all([
            fetch('/geojson/bangladesh.geojson'),
            fetch('/api/analytics/spatial-heatmap'),
        ]);
        geojsonData = await geoRes.json();
        heatmapData = await apiRes.json();
    } catch (err) {
        console.error('Heatmap load failed:', err);
        document.getElementById('loading-overlay').innerHTML =
            '<p style="color:#dc2626;font-size:0.85rem;font-family:Inter,sans-serif;">ডেটা লোড করতে সমস্যা হয়েছে।<br>পেইজ রিফ্রেশ করুন।</p>';
        return;
    }

    // ── 5. Render GeoJSON polygons ────────────────────────────────
    let totalRequests = 0;
    let topDistrict   = { name: '—', demand: 0 };
    const criticalList = [];

    const geojsonLayer = L.geoJSON(geojsonData, {

        style: function (feature) {
            // ✅ GADM property key: NAME_2
            const name  = feature.properties?.NAME_2 || '';
            // ✅ Fallback: unmatched district → Safe Green (crs=0, demand=0)
            const info  = (heatmapData[name] !== undefined)
                ? heatmapData[name]
                : { demand: 0, crs: 0 };

            return {
                fillColor:   getColor(info.crs),
                fillOpacity: 0.90,          // ✅ Solid, professional BI look
                color:       '#ffffff',     // ✅ Clean white borders between districts
                weight:      1.5,
            };
        },

        onEachFeature: function (feature, layer) {
            const name   = feature.properties?.NAME_2 || 'Unknown';
            const info   = (heatmapData[name] !== undefined)
                ? heatmapData[name]
                : { demand: 0, crs: 0 };
            const status = getStatusLabel(info.crs, info.demand);

            // Accumulate stats
            totalRequests += info.demand;
            if (info.demand > topDistrict.demand) {
                topDistrict = { name, demand: info.demand };
            }
            if (info.demand > 0) {
                criticalList.push({ name, demand: info.demand, crs: info.crs });
            }

            // ── Popup (UX-abstracted — no raw CRS/DFI) ──────────
            const popup = `
                <div class="popup-inner">
                    <div class="popup-district">📍 ${name}</div>
                    <span class="popup-status-badge"
                          style="background:${status.bg};color:${status.color};">
                        ${status.label}
                    </span>
                    <div class="popup-row">
                        <span>সক্রিয় রক্তের অনুরোধ</span>
                        <span>${info.demand} টি</span>
                    </div>
                    ${info.demand > 0 ? `
                    <div class="popup-cta">
                        আপনি কি রক্ত দিতে পারবেন?
                        <a href="/search"> এখানে দেখুন →</a>
                    </div>` : ''}
                </div>
            `;
            layer.bindPopup(popup, { maxWidth: 260 });

            // ── Hover effects ────────────────────────────────────
            layer.on({
                mouseover(e) {
                    e.target.setStyle({
                        weight:      2.5,
                        color:       '#374151',
                        fillOpacity: 1.0,
                    });
                    e.target.bringToFront();
                },
                mouseout(e) {
                    const i = (heatmapData[name] !== undefined)
                        ? heatmapData[name]
                        : { demand: 0, crs: 0 };
                    e.target.setStyle({
                        weight:      1.5,
                        color:       '#ffffff',
                        fillOpacity: 0.90,
                    });
                },
            });
        },

    }).addTo(map);

    // ✅ Perfect Framing: fit map exactly to Bangladesh bounds, lock movement
    map.fitBounds(geojsonLayer.getBounds(), { padding: [20, 20] });
    map.setMaxBounds(geojsonLayer.getBounds().pad(0.15));

    // ── 6. Update sidebar stats ────────────────────────────────────
    document.getElementById('stat-total-requests').textContent = totalRequests;
    document.getElementById('stat-top-district').textContent   = topDistrict.name;
    document.getElementById('stat-top-district-count').textContent =
        topDistrict.demand > 0 ? `${topDistrict.demand}টি সক্রিয় রিকোয়েস্ট` : 'কোনো রিকোয়েস্ট নেই';

    // Top critical districts list (sorted by CRS)
    criticalList.sort((a, b) => b.crs - a.crs);
    const listEl = document.getElementById('top-districts-list');
    const top5   = criticalList.slice(0, 5);

    if (top5.length === 0) {
        listEl.innerHTML = '<div style="font-size:0.78rem;color:#9ca3af;">সারাদেশে কোনো জরুরি রিকোয়েস্ট নেই 🎉</div>';
    } else {
        listEl.innerHTML = top5.map(d => {
            const s = getStatusLabel(d.crs, d.demand);
            return `<div class="district-row">
                <span class="district-name">${d.name}</span>
                <span class="district-badge ${s.cls}">${d.demand}টি</span>
            </div>`;
        }).join('');
    }

    // ── 7. Hide loading overlay ────────────────────────────────────
    document.getElementById('loading-overlay').style.display = 'none';
});
</script>

</body>
</html>
