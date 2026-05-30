<!doctype html>
<html lang="bn">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="theme-color" content="#2563eb" />
    <title>@yield('title', 'অর্গানাইজেশন ড্যাশবোর্ড — রক্তদূত')</title>
    <link rel="icon" href="{{ asset('images/image_14.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">

    @auth
    <script>
        window.__userId      = {{ auth()->id() }};
        window.__unreadCount = {{ auth()->user()->unreadNotifications()->count() }};
    </script>
    @endauth

    @vite(['resources/css/app.css','resources/js/app.js'])

    <style>
        :root { font-family: 'Hind Siliguri', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial; }

        /* ── Org Dashboard Shell ── */
        .org-shell {
            display: flex;
            min-height: calc(100vh - 73px);
            background: #f8fafc;
        }

        /* ── Sidebar ── */
        .org-sidebar {
            width: 280px;
            min-width: 280px;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: calc(100vh - 73px);
            z-index: 40;
            border-right: 1px solid #e2e8f0;
            overflow-y: auto;
            scrollbar-width: none;
        }
        .org-sidebar::-webkit-scrollbar { display: none; }

        /* ── Main Content ── */
        .org-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-width: 0;
        }
        #org-page-content {
            width: 100%;
            max-width: 80rem;
            margin: 0 auto;
            padding: 1.5rem 1rem 2.5rem;
        }
        @media (min-width: 640px) {
            #org-page-content { padding: 1.75rem 1.5rem 3rem; }
        }
        @media (min-width: 1024px) {
            #org-page-content { padding: 2rem 2rem 3.5rem; }
        }

        /* ── Sidebar Nav Items ── */
        .org-nav-item {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 0.875rem;
            padding: 0.75rem 1.25rem;
            border-radius: 0.75rem;
            margin: 0.2rem 1rem;
            width: calc(100% - 2rem);
            box-sizing: border-box;
            font-size: 0.9rem;
            font-weight: 600;
            color: #64748b;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            white-space: nowrap;
            position: relative;
            overflow: hidden;
            background: transparent;
        }

        .org-nav-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background-color: #2563eb;
            transform: scaleY(0);
            transition: transform 0.3s ease;
            border-radius: 0 4px 4px 0;
        }

        .org-nav-item:hover {
            background: #f8fafc;
            color: #1e293b;
            transform: translateX(4px);
        }

        .org-nav-item.active {
            background: linear-gradient(90deg, #dbeafe 0%, #eff6ff 100%);
            color: #1d4ed8;
            box-shadow: 0 1px 3px rgba(37, 99, 235, 0.1);
        }

        .org-nav-item.active::before {
            transform: scaleY(1);
        }

        .org-nav-item.active svg {
            color: #2563eb;
            transform: scale(1.1);
        }

        .org-nav-item svg {
            width: 1.2rem;
            height: 1.2rem;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        /* Badge on nav item */
        .org-nav-badge {
            margin-left: auto;
            font-size: 0.65rem;
            font-weight: 800;
            padding: 0.2rem 0.5rem;
            border-radius: 9999px;
            background: #2563eb;
            color: white;
            line-height: 1.2;
        }

        /* ── Sidebar Header ── */
        .org-sidebar-header {
            padding: 1.25rem 1.5rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* ── Section label ── */
        .org-section-label {
            font-size: 0.6rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #94a3b8;
            padding: 0.75rem 1.5rem 0.25rem;
        }

        /* ── Mobile overlay ── */
        .org-sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            backdrop-filter: blur(4px);
            z-index: 39;
        }

        /* ── Responsive ── */
        @media (max-width: 1023px) {
            .org-sidebar {
                position: fixed;
                top: 0;
                height: 100vh;
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .org-sidebar.open {
                transform: translateX(0);
                box-shadow: 8px 0 32px rgba(0,0,0,0.35);
            }
            .org-sidebar-overlay.open { display: block; }
        }

        /* ── Scroll Reveal ── */
        .scroll-reveal {
            opacity: 0;
            --sr-y: 20px;
            transform: translateY(var(--sr-y));
            transition: opacity 0.55s ease, transform 0.55s ease;
            will-change: opacity, transform;
        }
        .scroll-reveal.is-visible,
        [data-scroll-reveal].is-visible {
            opacity: 1;
            --sr-y: 0px;
        }
        @media (prefers-reduced-motion: reduce) {
            .scroll-reveal, [data-scroll-reveal] {
                opacity: 1 !important; transform: none !important; transition: none !important;
            }
        }

        /* SPA Loading bar */
        #org-spa-progress {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 2.5px;
            background: linear-gradient(90deg, #2563eb, #60a5fa, #2563eb);
            background-size: 200% 100%;
            animation: org-shimmer 1s linear infinite;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.15s;
            pointer-events: none;
        }
        #org-spa-progress.active { opacity: 1; }
        @keyframes org-shimmer {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        #org-page-content { transition: opacity 0.18s ease; }

        /* ── Pending status badge on org header ── */
        .org-pending-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
            border-radius: 9999px;
            font-size: 0.65rem;
            font-weight: 800;
            padding: 0.2rem 0.6rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>

    @stack('head')
</head>

<body class="bg-slate-50 text-slate-900 antialiased"
      x-data="{ sidebarOpen: false }"
      @keydown.escape.window="sidebarOpen = false">

{{-- ── Shared Site Header ── --}}
@include('partials.header', ['isDashboard' => true])

<div class="org-shell">

    {{-- ── Mobile Overlay ── --}}
    <div class="org-sidebar-overlay"
         :class="sidebarOpen ? 'open' : ''"
         @click="sidebarOpen = false"
         x-cloak></div>

    {{-- ══════════════════════════════════════
         🔵 LEFT SIDEBAR
    ══════════════════════════════════════ --}}
    <aside class="org-sidebar" :class="sidebarOpen ? 'open' : ''" id="org-sidebar">

        {{-- Sidebar Header: Org Info --}}
        <div class="org-sidebar-header">
            <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-black text-slate-800 truncate">অর্গ ড্যাশবোর্ড</p>
                @auth
                @php $org = auth()->user()->organization ?? null; @endphp
                @if($org)
                    <div class="flex items-center gap-1 mt-0.5">
                        <p class="text-[10px] font-bold text-slate-500 truncate">{{ Str::limit($org->name, 22) }}</p>
                        @if($org->status === 'verified')
                            <span class="shrink-0 text-[8px] font-black text-blue-600 bg-blue-50 border border-blue-100 px-1.5 py-0.5 rounded-full uppercase">✓ ভেরিফাইড</span>
                        @elseif($org->status === 'pending')
                            <span class="shrink-0 text-[8px] font-black text-amber-600 bg-amber-50 border border-amber-100 px-1.5 py-0.5 rounded-full uppercase">⏳ পেন্ডিং</span>
                        @endif
                    </div>
                @else
                    <p class="text-[10px] font-bold text-slate-400">অর্গ অ্যাডমিন</p>
                @endif
                @endauth
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 py-4">

            <a href="{{ route('org.dashboard') }}"
               class="org-nav-item {{ request()->routeIs('org.dashboard') ? 'active' : '' }}" data-tab="overview">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                ওভারভিউ
            </a>

            <div class="org-section-label">মেম্বার</div>

            <a href="{{ route('org.members.index') }}"
               class="org-nav-item {{ request()->routeIs('org.members.index') ? 'active' : '' }}" data-tab="members">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                মেম্বার ম্যানেজমেন্ট
            </a>

            <div class="org-section-label">কার্যক্রম</div>

            <a href="{{ route('org.requests.index') }}"
               class="org-nav-item {{ request()->routeIs('org.requests.*') ? 'active' : '' }}" data-tab="blood-requests">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                রক্তের অনুরোধ
            </a>

            <a href="{{ route('org.camps.index') }}"
               class="org-nav-item {{ request()->routeIs('org.camps.*') ? 'active' : '' }}" data-tab="camps">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                রক্তদান ক্যাম্প
            </a>

            <a href="{{ route('org.ambulances.index') }}"
               class="org-nav-item {{ request()->routeIs('org.ambulances.*') ? 'active' : '' }}" data-tab="ambulances">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                অ্যাম্বুলেন্স সার্ভিস
            </a>

            <div class="h-px bg-slate-100 mx-4 my-2"></div>

            <div class="org-section-label">অ্যাকাউন্ট</div>

            <a href="{{ route('profile.edit') }}"
               class="org-nav-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}" data-tab="profile">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                প্রোফাইল
            </a>

            <form method="POST" action="{{ route('logout') }}" x-data>
                @csrf
                <a href="{{ route('logout') }}"
                   @click.prevent="$root.submit();"
                   class="org-nav-item text-slate-500 hover:text-blue-600 hover:bg-blue-50">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    লগআউট
                </a>
            </form>

        </nav>

        {{-- Org user info at bottom --}}
        @auth
        <div class="px-4 py-4 border-t border-slate-100 mt-auto">
            <div class="flex items-center gap-3 px-2">
                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-black text-sm shrink-0">
                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] font-bold text-blue-500 uppercase tracking-wider">Org Admin</p>
                </div>
            </div>
        </div>
        @endauth

    </aside>

    {{-- ══════════════════════════════════════
         🖥️ MAIN CONTENT AREA
    ══════════════════════════════════════ --}}
    <div class="org-content">

        {{-- ── Mobile topbar (hamburger) ── --}}
        <div class="lg:hidden flex items-center gap-3 px-4 py-3 bg-white border-b border-slate-200 sticky top-0 z-30">
            <button @click="sidebarOpen = true"
                    class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <span class="text-sm font-black text-slate-800">অর্গানাইজেশন ড্যাশবোর্ড</span>
        </div>

        {{-- ── Flash Messages ── --}}
        @if(session('success'))
            <div class="mx-6 mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 font-semibold flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mx-6 mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 font-semibold flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- ── Page Content ── --}}
        <main class="flex-1" id="org-page-content">
            @yield('content')
        </main>

    </div>
</div>

{{-- ── Shared Footer ── --}}
@include('layouts.footer')

{{-- SPA Progress Bar --}}
<div id="org-spa-progress"></div>

{{-- Scroll Reveal --}}
<script>
    window.initScrollReveal = function(root = document) {
        const items = root.querySelectorAll('[data-scroll-reveal]');
        if (!items.length) return;
        const obs = new IntersectionObserver((entries) => {
            entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('is-visible'); obs.unobserve(e.target); } });
        }, { threshold: 0.05, rootMargin: '0px 0px -10% 0px' });
        items.forEach(el => obs.observe(el));
    };
    document.addEventListener('DOMContentLoaded', () => window.initScrollReveal());
</script>

{{-- SPA Navigation --}}
<script>
(function () {
    'use strict';

    const cache = {};
    const navLinks = document.querySelectorAll('.org-nav-item[data-tab]');
    const contentEl = document.getElementById('org-page-content');
    const progressBar = document.getElementById('org-spa-progress');

    function setActive(tabId) {
        navLinks.forEach(link => {
            link.classList.toggle('active', link.dataset.tab === tabId);
        });
    }

    function showProgress() { progressBar?.classList.add('active'); }
    function hideProgress() { progressBar?.classList.remove('active'); }

    function fadeOut() {
        if (!contentEl) return;
        contentEl.style.opacity = '0.4';
        contentEl.style.pointerEvents = 'none';
    }

    function fadeIn() {
        if (!contentEl) return;
        contentEl.style.opacity = '1';
        contentEl.style.pointerEvents = '';
    }

    function reinitAlpine(el) {
        if (typeof Alpine === 'undefined') return;
        el.querySelectorAll('[x-data]').forEach(node => {
            if (node._x_dataStack) { try { Alpine.destroyTree(node); } catch {} }
        });
        try { Alpine.initTree(el); } catch {}
    }

    function runPageScripts(fetchedDoc, injectEl) {
        const seen = new Set();
        const exec = (code) => { try { new Function(code)(); } catch(e) { console.error(e); } };
        const SKIP_SIGS = ['initScrollReveal', '__orgSpaNavigate'];
        const isSkip = (c) => SKIP_SIGS.some(s => c.includes(s));

        injectEl.querySelectorAll('script:not([type="module"])').forEach(s => {
            const c = s.textContent;
            if (!c.trim() || seen.has(c) || isSkip(c)) return;
            seen.add(c); exec(c);
        });

        if (fetchedDoc) {
            fetchedDoc.body?.querySelectorAll('script:not([src]):not([type="module"])').forEach(s => {
                const c = s.textContent;
                if (!c.trim() || seen.has(c) || isSkip(c)) return;
                seen.add(c); exec(c);
            });
        }
    }

    let currentTab = document.querySelector('.org-nav-item[data-tab].active')?.dataset?.tab || 'members';

    async function switchTab(tabId, url) {
        if (tabId === currentTab) return;
        currentTab = tabId;
        setActive(tabId);

        // Serve from cache instantly
        if (typeof cache[tabId] === 'string') {
            window.history.pushState({ tab: tabId }, '', url);
            contentEl.innerHTML = cache[tabId];
            window.scrollTo({ top: 0 });
            runPageScripts(null, contentEl);
            reinitAlpine(contentEl);
            if (window.initScrollReveal) requestAnimationFrame(() => window.initScrollReveal(contentEl));
            return;
        }

        showProgress();
        fadeOut();

        try {
            const res = await fetch(url, {
                headers: { 'Accept': 'text/html', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
                redirect: 'follow'
            });

            if (res.url.includes('/login')) { window.location.href = res.url; return; }

            const html = await res.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');

            let newHtml = '';
            const panel = doc.querySelector(`[data-panel-id="${tabId}"]`);
            if (panel) {
                newHtml = panel.outerHTML;
            } else {
                const pageContent = doc.getElementById('org-page-content');
                if (pageContent) {
                    newHtml = pageContent.innerHTML;
                } else {
                    window.location.href = url;
                    return;
                }
            }

            cache[tabId] = newHtml;

            contentEl.querySelectorAll('[x-data]').forEach(node => {
                if (node._x_dataStack) { try { Alpine.destroyTree?.(node); } catch {} }
            });

            contentEl.innerHTML = newHtml;

            const t = doc.querySelector('title')?.textContent;
            if (t) document.title = t;

            window.history.pushState({ tab: tabId }, t || '', url);
            window.scrollTo({ top: 0 });

            runPageScripts(doc, contentEl);
            reinitAlpine(contentEl);
            if (window.initScrollReveal) requestAnimationFrame(() => window.initScrollReveal(contentEl));

        } catch (err) {
            console.error('[Org SPA] Error:', err);
            window.location.href = url;
        } finally {
            fadeIn();
            hideProgress();
        }
    }

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.getAttribute('target') === '_blank') return;
            if (this.hasAttribute('data-no-spa')) return;
            if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
            e.preventDefault();
            switchTab(this.dataset.tab, this.href);
        });
    });

    window.__orgSpaNavigate = switchTab;
    window.__orgSpaSetActive = setActive;

    window.addEventListener('popstate', function(e) {
        if (e.state && e.state.tab) {
            switchTab(e.state.tab, window.location.href);
        } else {
            window.location.reload();
        }
    });
})();
</script>

@stack('scripts')

</body>
</html>
