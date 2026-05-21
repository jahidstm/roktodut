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
        @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Hind Siliguri', sans-serif;
            background: #0a0f1e;
            color: #e2e8f0;
            min-height: 100vh;
        }

        /* ── Header ─────────────────────────── */
        .heatmap-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
            border-bottom: 1px solid rgba(99, 102, 241, 0.3);
            padding: 1.25rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .heatmap-header .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .heatmap-header .brand-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, #ef4444, #b91c1c);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem;
            box-shadow: 0 0 16px rgba(239, 68, 68, 0.4);
        }
        .heatmap-header h1 {
            font-size: 1.2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #c7d2fe, #a5b4fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .live-badge {
            display: flex; align-items: center; gap: 0.5rem;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.4);
            border-radius: 9999px;
            padding: 0.35rem 1rem;
            font-size: 0.78rem;
            font-weight: 600;
            color: #fca5a5;
        }
        .live-dot {
            width: 8px; height: 8px;
            background: #ef4444;
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%        { opacity: 0.4; transform: scale(0.8); }
        }

        /* ── Main Layout ─────────────────────────── */
        .heatmap-layout {
            display: grid;
            grid-template-columns: 320px 1fr;
            height: calc(100vh - 73px);
        }

        /* ── Sidebar ─────────────────────────── */
        .sidebar {
            background: #0f172a;
            border-right: 1px solid rgba(99, 102, 241, 0.2);
            overflow-y: auto;
            padding: 1.5rem;
        }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(99, 102, 241, 0.3); border-radius: 4px; }

        .sidebar-section { margin-bottom: 2rem; }
        .sidebar-title {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #6366f1;
            margin-bottom: 1rem;
        }

        /* Legend */
        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }
        .legend-dot {
            width: 14px; height: 14px;
            border-radius: 3px;
            flex-shrink: 0;
        }
        .legend-label { font-size: 0.82rem; color: #cbd5e1; }

        /* Stats */
        .stat-card {
            background: rgba(99, 102, 241, 0.08);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            padding: 1rem 1.2rem;
            margin-bottom: 0.75rem;
            transition: border-color 0.2s;
        }
        .stat-card:hover { border-color: rgba(99, 102, 241, 0.5); }
        .stat-label { font-size: 0.72rem; color: #94a3b8; margin-bottom: 0.25rem; }
        .stat-value { font-size: 1.5rem; font-weight: 700; color: #e2e8f0; }
        .stat-sub { font-size: 0.72rem; color: #64748b; margin-top: 0.2rem; }

        /* Top Districts */
        .district-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.6rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .district-row:last-child { border-bottom: none; }
        .district-name { font-size: 0.82rem; color: #e2e8f0; }
        .district-badge {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.2rem 0.6rem;
            border-radius: 9999px;
        }
        .badge-critical { background: rgba(220, 38, 38, 0.2); color: #fca5a5; border: 1px solid rgba(220,38,38,0.4); }
        .badge-warning  { background: rgba(245, 158, 11, 0.2); color: #fde68a; border: 1px solid rgba(245,158,11,0.4); }
        .badge-stable   { background: rgba(16, 185, 129, 0.2); color: #6ee7b7; border: 1px solid rgba(16,185,129,0.4); }

        /* ── Map ─────────────────────────── */
        #map {
            width: 100%;
            height: 100%;
            background: #0a0f1e;
        }

        /* Leaflet overrides */
        .leaflet-popup-content-wrapper {
            background: #1e293b;
            border: 1px solid rgba(99,102,241,0.4);
            border-radius: 12px;
            color: #e2e8f0;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            padding: 0;
        }
        .leaflet-popup-content { margin: 0; }
        .leaflet-popup-tip { background: #1e293b; }

        .popup-inner { padding: 1.2rem 1.4rem; min-width: 220px; }
        .popup-district {
            font-size: 1rem;
            font-weight: 700;
            color: #c7d2fe;
            margin-bottom: 0.8rem;
            padding-bottom: 0.6rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .popup-status-badge {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            margin-bottom: 0.8rem;
            letter-spacing: 0.04em;
        }
        .popup-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            color: #94a3b8;
            margin-bottom: 0.4rem;
        }
        .popup-row span:last-child { color: #e2e8f0; font-weight: 600; }

        /* ── Loading Overlay ─────────────────────────── */
        #loading-overlay {
            position: absolute;
            inset: 0;
            background: rgba(10,15,30,0.9);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            gap: 1rem;
            backdrop-filter: blur(4px);
        }
        .spinner {
            width: 48px; height: 48px;
            border: 3px solid rgba(99,102,241,0.2);
            border-top-color: #6366f1;
            border-radius: 50%;
            animation: spin 0.9s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .loading-text { font-size: 0.85rem; color: #94a3b8; }

        /* ── Responsive ─────────────────────────── */
        @media (max-width: 768px) {
            .heatmap-layout { grid-template-columns: 1fr; grid-template-rows: auto 1fr; }
            .sidebar { max-height: 220px; overflow-y: auto; }
        }
    </style>
</head>
<body>

{{-- Header --}}
<header class="heatmap-header">
    <div class="brand">
        <div class="brand-icon">🩸</div>
        <h1>লাইভ রক্তের চাহিদা মানচিত্র</h1>
    </div>
    <div class="live-badge">
        <div class="live-dot"></div>
        রিয়েল-টাইম
    </div>
</header>

{{-- Layout --}}
<div class="heatmap-layout">

    {{-- Sidebar --}}
    <aside class="sidebar">

        {{-- Legend --}}
        <div class="sidebar-section">
            <p class="sidebar-title">রঙের অর্থ</p>
            <div class="legend-item">
                <div class="legend-dot" style="background:#800026;"></div>
                <span class="legend-label">⚠️ সংকট — অবিলম্বে ডোনার প্রয়োজন!</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background:#E31A1C;"></div>
                <span class="legend-label">🔴 জরুরি — চাহিদা তীব্র</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background:#FD8D3C;"></div>
                <span class="legend-label">🟠 সতর্কতা — চাহিদা বাড়ছে</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background:#FEB24C;"></div>
                <span class="legend-label">🟡 মনোযোগ প্রয়োজন</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background:#21b86c;"></div>
                <span class="legend-label">🟢 স্বাভাবিক — কোনো জরুরি রিকোয়েস্ট নেই</span>
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
                <div class="stat-value" style="font-size:1rem" id="stat-top-district">—</div>
                <div class="stat-sub" id="stat-top-district-count">লোড হচ্ছে...</div>
            </div>
        </div>

        {{-- Top Critical Districts --}}
        <div class="sidebar-section">
            <p class="sidebar-title">জরুরি জেলাসমূহ</p>
            <div id="top-districts-list">
                <div style="font-size:0.8rem;color:#64748b;">লোড হচ্ছে...</div>
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

    // ── 1. Initialize Leaflet Map ──────────────────────────────────
    const map = L.map('map', {
        center: [23.685, 90.356],
        zoom: 7,
        zoomControl: true,
        attributionControl: false,
    });

    // Dark tile layer
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_nolabels/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
    }).addTo(map);

    // ── 2. Color function using CRS (0-100) ────────────────────────
    function getColor(crs) {
        return crs > 75 ? '#800026' :   // Critical
               crs > 50 ? '#E31A1C' :   // Emergency
               crs > 30 ? '#FD8D3C' :   // Warning
               crs > 0  ? '#FEB24C' :   // Attention
                          '#21b86c';    // Stable (demand === 0, always green)
    }

    // ── 3. Status label for tooltip (user-friendly, no raw DFI/CRS) ──
    function getStatusLabel(crs, demand) {
        if (demand === 0 || crs === 0) {
            return { label: '✅ স্বাভাবিক', cls: 'badge-stable', color: '#10b981', bg: 'rgba(16,185,129,0.15)' };
        }
        if (crs > 75) {
            return { label: '🚨 সংকট: অবিলম্বে ডোনার প্রয়োজন!', cls: 'badge-critical', color: '#ef4444', bg: 'rgba(239,68,68,0.15)' };
        }
        if (crs > 50) {
            return { label: '🔴 জরুরি: চাহিদা তীব্র', cls: 'badge-critical', color: '#ef4444', bg: 'rgba(239,68,68,0.15)' };
        }
        if (crs > 30) {
            return { label: '⚠️ সতর্কতা: চাহিদা বাড়ছে', cls: 'badge-warning', color: '#f59e0b', bg: 'rgba(245,158,11,0.15)' };
        }
        return { label: '🟡 মনোযোগ প্রয়োজন', cls: 'badge-warning', color: '#f59e0b', bg: 'rgba(245,158,11,0.15)' };
    }

    // ── 4. Fetch data in parallel ──────────────────────────────────
    let geojsonData, heatmapData;
    try {
        const [geoRes, apiRes] = await Promise.all([
            fetch('/geojson/bangladesh.geojson'),
            fetch('/api/analytics/spatial-heatmap'),
        ]);
        geojsonData  = await geoRes.json();
        heatmapData  = await apiRes.json();
    } catch (err) {
        console.error('Failed to load heatmap data:', err);
        document.getElementById('loading-overlay').innerHTML =
            '<p style="color:#fca5a5;font-size:0.9rem;">ডেটা লোড করতে সমস্যা হয়েছে। পরে আবার চেষ্টা করুন।</p>';
        return;
    }

    // ── 5. Render GeoJSON polygons ────────────────────────────────
    let totalRequests = 0;
    let topDistrict = { name: '—', demand: 0 };
    const criticalList = [];

    L.geoJSON(geojsonData, {
        style: function(feature) {
            // GeoJSON property key: "properties.name"
            const name   = feature.properties?.name || '';
            const info   = heatmapData[name] || { demand: 0, crs: 0 };
            const color  = getColor(info.crs);
            const opacity = info.demand > 0 ? 0.75 : 0.25;

            return {
                fillColor:   color,
                weight:      1.5,
                color:       '#1e293b',
                fillOpacity: opacity,
            };
        },

        onEachFeature: function(feature, layer) {
            const name   = feature.properties?.name || 'Unknown';
            const info   = heatmapData[name] || { demand: 0, crs: 0 };
            const status = getStatusLabel(info.crs, info.demand);

            // Track stats
            totalRequests += info.demand;
            if (info.demand > topDistrict.demand) {
                topDistrict = { name, demand: info.demand };
            }
            if (info.demand > 0) {
                criticalList.push({ name, demand: info.demand, crs: info.crs });
            }

            // Tooltip popup (UX-masked — no raw DFI/CRS shown to user)
            const popup = `
                <div class="popup-inner">
                    <div class="popup-district">📍 ${name}</div>
                    <span class="popup-status-badge"
                          style="background:${status.bg};color:${status.color};border:1px solid ${status.color}40;">
                        ${status.label}
                    </span>
                    <div class="popup-row">
                        <span>সক্রিয় রক্তের অনুরোধ</span>
                        <span>${info.demand} টি</span>
                    </div>
                    ${info.demand > 0 ? `
                    <div style="margin-top:0.75rem;padding-top:0.6rem;border-top:1px solid rgba(255,255,255,0.06);font-size:0.75rem;color:#64748b;">
                        আপনি কি রক্ত দিতে পারবেন?
                        <a href="/search" style="color:#6366f1;text-decoration:none;font-weight:600;"> এখানে দেখুন →</a>
                    </div>` : ''}
                </div>
            `;

            layer.bindPopup(popup, { maxWidth: 280 });

            // Hover effects
            layer.on({
                mouseover(e) {
                    e.target.setStyle({ weight: 2.5, color: '#6366f1', fillOpacity: 0.9 });
                    e.target.bringToFront();
                },
                mouseout(e) {
                    const i = heatmapData[name] || { demand: 0, crs: 0 };
                    e.target.setStyle({
                        weight: 1.5,
                        color: '#1e293b',
                        fillOpacity: i.demand > 0 ? 0.75 : 0.25
                    });
                },
            });
        }
    }).addTo(map);

    // ── 6. Update sidebar stats ───────────────────────────────────
    document.getElementById('stat-total-requests').textContent = totalRequests;
    document.getElementById('stat-top-district').textContent   = topDistrict.name;
    document.getElementById('stat-top-district-count').textContent =
        topDistrict.demand > 0 ? `${topDistrict.demand}টি সক্রিয় রিকোয়েস্ট` : 'কোনো রিকোয়েস্ট নেই';

    // Top critical districts list
    const listEl = document.getElementById('top-districts-list');
    criticalList.sort((a, b) => b.crs - a.crs);
    const top5 = criticalList.slice(0, 5);

    if (top5.length === 0) {
        listEl.innerHTML = '<div style="font-size:0.8rem;color:#64748b;">সারাদেশে কোনো জরুরি রিকোয়েস্ট নেই 🎉</div>';
    } else {
        listEl.innerHTML = top5.map(d => {
            const s = getStatusLabel(d.crs, d.demand);
            return `<div class="district-row">
                <span class="district-name">${d.name}</span>
                <span class="district-badge ${s.cls}">${d.demand}টি</span>
            </div>`;
        }).join('');
    }

    // ── 7. Hide loading overlay ───────────────────────────────────
    document.getElementById('loading-overlay').style.display = 'none';
});
</script>

</body>
</html>
