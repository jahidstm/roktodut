<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>লাইভ রক্তের চাহিদা মানচিত্র — রক্তদূত</title>
    <meta name="description" content="বাংলাদেশের জেলাভিত্তিক রিয়েল-টাইম রক্তের জরুরি চাহিদার ইন্টারেক্টিভ মানচিত্র।">

    <link rel="icon" href="{{ asset('images/image_14.png') }}" type="image/png">

    {{-- Leaflet.js --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap');

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            font-family: 'Hind Siliguri', ui-sans-serif, system-ui, -apple-system, sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }

        /* ── Topbar — matches site header exactly ────────── */
        .topbar {
            height: 60px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            text-decoration: none;
        }
        .topbar-logo {
            width: 36px; height: 36px;
            border-radius: 10px;
            border: 1px solid #f1f5f9;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            display: flex; align-items: center; justify-content: center;
        }
        .topbar-logo img { width: 100%; height: 100%; object-fit: contain; padding: 4px; }
        .topbar-name {
            font-size: 1rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
        }
        .topbar-sub {
            font-size: 0.65rem;
            color: #94a3b8;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }
        .topbar-center {
            font-size: 0.9rem;
            font-weight: 700;
            color: #334155;
        }
        .topbar-right { display: flex; align-items: center; gap: 0.75rem; }
        .live-pill {
            display: flex; align-items: center; gap: 0.4rem;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 9999px;
            padding: 0.25rem 0.8rem;
            font-size: 0.72rem;
            font-weight: 700;
            color: #dc2626;
        }
        .live-dot {
            width: 6px; height: 6px;
            background: #dc2626;
            border-radius: 50%;
            animation: blink 1.5s infinite;
        }
        @keyframes blink {
            0%,100% { opacity: 1; }
            50%      { opacity: 0.3; }
        }
        .back-btn {
            display: flex; align-items: center; gap: 0.4rem;
            font-size: 0.78rem;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #fff;
            transition: all 0.15s;
        }
        .back-btn:hover { border-color: #dc2626; color: #dc2626; }

        /* ── 3-Column BI Layout ──────────────────────────── */
        .bi-layout {
            display: grid;
            grid-template-columns: 260px 1fr 260px;
            height: calc(100vh - 60px);
            overflow: hidden;
        }

        /* ── Panels ──────────────────────────────────────── */
        .panel {
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            overflow-y: auto;
            padding: 1rem;
        }
        .panel.right {
            border-right: none;
            border-left: 1px solid #e2e8f0;
        }
        .panel::-webkit-scrollbar { width: 3px; }
        .panel::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

        .panel-section { margin-bottom: 1.25rem; }
        .panel-section-title {
            font-size: 0.6rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 0.65rem;
            padding-bottom: 0.4rem;
            border-bottom: 1px solid #f1f5f9;
        }

        /* ── Legend ──────────────────────────────────────── */
        .legend-row {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.45rem 0.5rem;
            border-radius: 8px;
            margin-bottom: 0.2rem;
            transition: background 0.15s;
        }
        .legend-row:hover { background: #f8fafc; }
        .legend-swatch {
            width: 18px; height: 12px;
            border-radius: 4px;
            flex-shrink: 0;
            border: 1px solid rgba(0,0,0,0.07);
        }
        .legend-text { font-size: 0.75rem; color: #374151; font-weight: 500; }

        /* ── Summary Stat (right panel) ─────────────────── */
        .stat-block {
            padding: 0.75rem 0.85rem;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            margin-bottom: 0.6rem;
            transition: box-shadow 0.15s;
        }
        .stat-block:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.07); }
        .stat-block-label {
            font-size: 0.65rem;
            font-weight: 700;
            color: #94a3b8;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 0.2rem;
        }
        .stat-block-value {
            font-size: 1.6rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
            letter-spacing: -0.03em;
        }
        .stat-block-value.accent { color: #dc2626; }
        .stat-block-sub {
            font-size: 0.65rem;
            color: #94a3b8;
            margin-top: 0.2rem;
        }

        /* ── Critical district rows (left panel) ────────── */
        .district-entry {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 0.6rem;
            border-radius: 8px;
            margin-bottom: 0.2rem;
            transition: background 0.12s;
            cursor: pointer;
        }
        .district-entry:hover { background: #fef2f2; }
        .district-entry-name { font-size: 0.78rem; font-weight: 600; color: #1e293b; }
        .district-entry-badge {
            font-size: 0.65rem;
            font-weight: 800;
            padding: 0.18rem 0.55rem;
            border-radius: 9999px;
        }
        .badge-critical { background: #fee2e2; color: #dc2626; }
        .badge-warning  { background: #fef3c7; color: #d97706; }
        .badge-stable   { background: #dcfce7; color: #16a34a; }

        /* ── Map canvas ──────────────────────────────────── */
        .map-wrapper {
            position: relative;
            background: #eef2f7;
        }
        #map {
            width: 100%;
            height: 100%;
            background: #eef2f7;
        }

        /* ── Loading overlay ─────────────────────────────── */
        #loading-overlay {
            position: absolute; inset: 0;
            background: rgba(248,250,252,0.9);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 0.75rem;
            z-index: 9999;
            backdrop-filter: blur(4px);
        }
        .spinner {
            width: 36px; height: 36px;
            border: 3px solid #fee2e2;
            border-top-color: #dc2626;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .loading-text { font-size: 0.8rem; color: #64748b; font-weight: 500; }

        /* ── Leaflet popup — site design language ────────── */
        .leaflet-popup-content-wrapper {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            color: #0f172a;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12);
            padding: 0;
        }
        .leaflet-popup-content { margin: 0 !important; }
        .leaflet-popup-tip { background: #ffffff; }
        .leaflet-popup-close-button {
            color: #94a3b8 !important;
            right: 10px !important; top: 10px !important;
            font-size: 18px !important;
        }
        .leaflet-control-attribution { display: none !important; }
        .leaflet-control-zoom a {
            background: #fff !important;
            color: #374151 !important;
            border-color: #e2e8f0 !important;
            border-radius: 8px !important;
            font-size: 16px !important;
        }
        .leaflet-control-zoom { border: none !important; box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important; }

        .popup-card { padding: 1rem 1.2rem; min-width: 200px; }
        .popup-district-name {
            font-size: 0.9rem; font-weight: 800; color: #0f172a;
            margin-bottom: 0.5rem; padding-bottom: 0.5rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .popup-status {
            display: inline-block;
            font-size: 0.68rem; font-weight: 700;
            padding: 0.2rem 0.6rem; border-radius: 9999px;
            margin-bottom: 0.65rem;
        }
        .popup-meta { font-size: 0.75rem; color: #475569; margin-bottom: 0.3rem; }
        .popup-meta span { font-weight: 700; color: #0f172a; }
        .popup-cta {
            margin-top: 0.6rem; padding-top: 0.5rem;
            border-top: 1px solid #f1f5f9;
            font-size: 0.7rem; color: #94a3b8;
        }
        .popup-cta a { color: #dc2626; font-weight: 700; text-decoration: none; }
        .popup-cta a:hover { text-decoration: underline; }

        /* ── Responsive ──────────────────────────────────── */
        @media (max-width: 900px) {
            .bi-layout { grid-template-columns: 1fr; grid-template-rows: auto 1fr auto; }
            .panel { max-height: 180px; border-right: none; border-bottom: 1px solid #e2e8f0; }
            .panel.right { border-left: none; border-top: 1px solid #e2e8f0; }
        }
    </style>
</head>
<body>

{{-- ── Topbar ──────────────────────────────────────── --}}
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
        <a href="{{ route('home') }}" class="back-btn">
            ← হোমে ফিরুন
        </a>
    </div>
</nav>

{{-- ── 3-Column BI Layout ────────────────────────────── --}}
<div class="bi-layout">

    {{-- ── LEFT PANEL: Legend + Critical Districts ────── --}}
    <aside class="panel">

        <div class="panel-section">
            <p class="panel-section-title">রঙের অর্থ</p>
            <div class="legend-row">
                <div class="legend-swatch" style="background:#800026;"></div>
                <span class="legend-text">সংকট — জরুরি ডোনার প্রয়োজন</span>
            </div>
            <div class="legend-row">
                <div class="legend-swatch" style="background:#E31A1C;"></div>
                <span class="legend-text">জরুরি — চাহিদা তীব্র</span>
            </div>
            <div class="legend-row">
                <div class="legend-swatch" style="background:#FD8D3C;"></div>
                <span class="legend-text">সতর্কতা — চাহিদা বাড়ছে</span>
            </div>
            <div class="legend-row">
                <div class="legend-swatch" style="background:#FEB24C;"></div>
                <span class="legend-text">মনোযোগ — কিছুটা চাহিদা আছে</span>
            </div>
            <div class="legend-row">
                <div class="legend-swatch" style="background:#52b788;"></div>
                <span class="legend-text">স্বাভাবিক — কোনো অনুরোধ নেই</span>
            </div>
        </div>

        <div class="panel-section">
            <p class="panel-section-title">জরুরি জেলাসমূহ</p>
            <div id="critical-districts-list">
                <div style="font-size:0.75rem;color:#94a3b8;padding:0.5rem;">লোড হচ্ছে...</div>
            </div>
        </div>

    </aside>

    {{-- ── CENTER: Map ─────────────────────────────────── --}}
    <div class="map-wrapper">
        <div id="loading-overlay">
            <div class="spinner"></div>
            <p class="loading-text">মানচিত্র লোড হচ্ছে...</p>
        </div>
        <div id="map"></div>
    </div>

    {{-- ── RIGHT PANEL: Summary Stats ──────────────────── --}}
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
                <div class="stat-block-value" style="font-size:1.1rem;" id="stat-top-name">—</div>
                <div class="stat-block-sub" id="stat-top-count">লোড হচ্ছে...</div>
            </div>

            <div class="stat-block">
                <div class="stat-block-label">মোট জেলা</div>
                <div class="stat-block-value">৬৪</div>
                <div class="stat-block-sub">বাংলাদেশের সকল জেলা অন্তর্ভুক্ত</div>
            </div>

            <div class="stat-block">
                <div class="stat-block-label">জরুরি অবস্থায় জেলা</div>
                <div class="stat-block-value accent" id="stat-critical-count">—</div>
                <div class="stat-block-sub">CRS &gt; 50 এর জেলা</div>
            </div>
        </div>

        <div class="panel-section">
            <p class="panel-section-title">দ্রুত সংযোগ</p>
            <a href="{{ route('requests.index') }}"
               style="display:flex;align-items:center;gap:0.5rem;padding:0.6rem 0.8rem;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;text-decoration:none;margin-bottom:0.5rem;transition:all 0.15s;"
               onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                <span style="font-size:1rem;">🩸</span>
                <div>
                    <div style="font-size:0.75rem;font-weight:700;color:#dc2626;">রক্তের অনুরোধ করুন</div>
                    <div style="font-size:0.65rem;color:#f87171;">জরুরি রক্তের প্রয়োজন?</div>
                </div>
            </a>
            <a href="{{ route('search') }}"
               style="display:flex;align-items:center;gap:0.5rem;padding:0.6rem 0.8rem;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;text-decoration:none;transition:all 0.15s;"
               onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                <span style="font-size:1rem;">🔍</span>
                <div>
                    <div style="font-size:0.75rem;font-weight:700;color:#16a34a;">ডোনার খুঁজুন</div>
                    <div style="font-size:0.65rem;color:#4ade80;">আপনার এলাকায় ডোনার</div>
                </div>
            </a>
        </div>

        <div class="panel-section">
            <p class="panel-section-title">তথ্য আপডেট</p>
            <div style="font-size:0.7rem;color:#94a3b8;line-height:1.6;">
                এই মানচিত্রটি প্রতি ১৫ মিনিট পর পর স্বয়ংক্রিয়ভাবে আপডেট হয়।
                শুধুমাত্র সক্রিয় (pending/in_progress) রক্তের অনুরোধ গণনা করা হচ্ছে।
            </div>
        </div>

    </aside>

</div>{{-- /.bi-layout --}}

<script>
document.addEventListener('DOMContentLoaded', async () => {

    // ── 1. Leaflet map — NO tile layer ────────────────────────────
    const map = L.map('map', {
        center:             [23.685, 90.356],
        zoom:               7,
        zoomControl:        true,
        attributionControl: false,
        minZoom:            6,
        maxZoom:            10,
    });
    // ✅ Zero base tiles — only GeoJSON polygons on plain canvas

    // ── 2. CRS colour ramp ────────────────────────────────────────
    function getColor(crs) {
        return crs > 75 ? '#800026' :
               crs > 50 ? '#E31A1C' :
               crs > 30 ? '#FD8D3C' :
               crs >  0 ? '#FEB24C' :
                          '#52b788';
    }

    // ── 3. UX-friendly status (no raw DFI/CRS shown to users) ─────
    function getStatus(crs, demand) {
        if (demand === 0 || crs === 0)
            return { label: '✅ স্বাভাবিক',                       color: '#16a34a', bg: '#dcfce7', cls: 'badge-stable' };
        if (crs > 75)
            return { label: '🚨 সংকট: অবিলম্বে ডোনার প্রয়োজন!', color: '#dc2626', bg: '#fee2e2', cls: 'badge-critical' };
        if (crs > 50)
            return { label: '🔴 জরুরি: চাহিদা তীব্র',             color: '#dc2626', bg: '#fee2e2', cls: 'badge-critical' };
        if (crs > 30)
            return { label: '⚠️ সতর্কতা: চাহিদা বাড়ছে',          color: '#d97706', bg: '#fef3c7', cls: 'badge-warning' };
        return   { label: '🟡 মনোযোগ প্রয়োজন',                    color: '#d97706', bg: '#fef3c7', cls: 'badge-warning' };
    }

    // ── 4. Fetch data in parallel ─────────────────────────────────
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
            '<p style="color:#dc2626;font-size:0.82rem;">ডেটা লোড করতে সমস্যা। পেইজ রিফ্রেশ করুন।</p>';
        return;
    }

    // ── 5. Build GeoJSON layer ────────────────────────────────────
    let totalDemand = 0, topDistrict = { name: '—', demand: 0 };
    const criticalList = [];

    const geoLayer = L.geoJSON(geojsonData, {

        style(feature) {
            const name = feature.properties?.NAME_2 || '';
            const info = heatmapData[name] ?? { demand: 0, crs: 0 };  // ✅ fallback → Safe Green
            return {
                fillColor:   getColor(info.crs),
                fillOpacity: 0.88,
                color:       '#ffffff',   // ✅ white district borders
                weight:      1.5,
            };
        },

        onEachFeature(feature, layer) {
            const name   = feature.properties?.NAME_2 || 'Unknown';
            const info   = heatmapData[name] ?? { demand: 0, crs: 0 };
            const status = getStatus(info.crs, info.demand);

            totalDemand += info.demand;
            if (info.demand > topDistrict.demand) topDistrict = { name, demand: info.demand };
            if (info.demand > 0) criticalList.push({ name, demand: info.demand, crs: info.crs });

            // Popup
            layer.bindPopup(`
                <div class="popup-card">
                    <div class="popup-district-name">📍 ${name}</div>
                    <span class="popup-status"
                          style="background:${status.bg};color:${status.color};">
                        ${status.label}
                    </span>
                    <div class="popup-meta">সক্রিয় রক্তের অনুরোধ: <span>${info.demand} টি</span></div>
                    ${info.demand > 0 ? `
                    <div class="popup-cta">
                        আপনি রক্ত দিতে পারবেন?
                        <a href="/search">ডোনার খুঁজুন →</a>
                    </div>` : ''}
                </div>
            `, { maxWidth: 260 });

            // Hover
            layer.on({
                mouseover(e) {
                    e.target.setStyle({ weight: 2.5, color: '#dc2626', fillOpacity: 1.0 });
                    e.target.bringToFront();
                },
                mouseout(e) {
                    const i = heatmapData[name] ?? { demand: 0, crs: 0 };
                    e.target.setStyle({ weight: 1.5, color: '#ffffff', fillOpacity: 0.88 });
                },
            });
        },
    }).addTo(map);

    // ✅ Perfect Bangladesh framing — lock outside scrolling
    map.fitBounds(geoLayer.getBounds(), { padding: [24, 24] });
    map.setMaxBounds(geoLayer.getBounds().pad(0.12));

    // ── 6. Sidebar stats ──────────────────────────────────────────
    document.getElementById('stat-total').textContent    = totalDemand;
    document.getElementById('stat-top-name').textContent = topDistrict.name;
    document.getElementById('stat-top-count').textContent =
        topDistrict.demand > 0 ? `${topDistrict.demand}টি সক্রিয় রিকোয়েস্ট` : 'কোনো রিকোয়েস্ট নেই';

    const criticalCount = criticalList.filter(d => d.crs > 50).length;
    document.getElementById('stat-critical-count').textContent = criticalCount;

    // Critical district list (left panel)
    criticalList.sort((a, b) => b.crs - a.crs);
    const listEl = document.getElementById('critical-districts-list');
    if (criticalList.length === 0) {
        listEl.innerHTML = '<div style="font-size:0.75rem;color:#94a3b8;padding:0.5rem;">সারাদেশে কোনো জরুরি রিকোয়েস্ট নেই 🎉</div>';
    } else {
        listEl.innerHTML = criticalList.slice(0, 8).map(d => {
            const s = getStatus(d.crs, d.demand);
            return `<div class="district-entry">
                <span class="district-entry-name">${d.name}</span>
                <span class="district-entry-badge ${s.cls}">${d.demand}টি</span>
            </div>`;
        }).join('');
    }

    // ── 7. Hide loader ────────────────────────────────────────────
    document.getElementById('loading-overlay').style.display = 'none';
});
</script>

</body>
</html>
