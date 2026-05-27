<!doctype html>
<html lang="bn">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="theme-color" content="#dc2626" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="default" />
    <meta name="apple-mobile-web-app-title" content="রক্তদূত" />
    <title>@yield('title', 'ডোনার ড্যাশবোর্ড — রক্তদূত')</title>
    <link rel="manifest" href="/manifest.json" />
    <link rel="icon" href="{{ asset('images/image_14.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('images/image_14.png') }}">

    @auth
    <script>
        window.__userId      = {{ auth()->id() }};
        window.__unreadCount = {{ auth()->user()->unreadNotifications()->count() }};
    </script>
    @endauth

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css','resources/js/app.js'])

    <style>
        :root { font-family: 'Hind Siliguri', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial; }

        /* ── Donor Dashboard Shell ── */
        .donor-shell {
            display: flex;
            min-height: 100vh;
            background: #f8fafc;
        }

        /* ── Sidebar ── */
        .donor-sidebar {
            width: 280px;
            min-width: 280px;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 76px;
            height: calc(100vh - 76px);
            z-index: 40;
            border-right: 1px solid #e2e8f0;
            overflow-y: auto;
            scrollbar-width: none;
        }
        /* hide original */
        .old-donor-sidebar {
            width: 260px;
            min-width: 260px;
            background: #0f172a; /* slate-900 */
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 40;
            border-right: 1px solid #e2e8f0;
            overflow-y: auto;
            scrollbar-width: none;
        }
        .donor-sidebar::-webkit-scrollbar { display: none; }

        /* ── Main Content ── */
        .donor-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: calc(100vh - 76px);
            min-width: 0;
        }
        #page-content {
            width: 100%;
            max-width: 72rem;
            margin: 0 auto;
            padding: 1.5rem 1rem 2.5rem;
        }
        @media (min-width: 640px) {
            #page-content {
                padding: 1.75rem 1.5rem 3rem;
            }
        }
        @media (min-width: 1024px) {
            #page-content {
                padding: 2rem 2rem 3.5rem;
            }
        }
        /* hide original */
        .old-donor-content {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── Top Header ── */
        .donor-header {
            height: 60px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 30;
        }

        /* ── Sidebar Nav Items ── */
        .sidebar-nav-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.85rem 1.25rem;
            border-radius: 0.75rem;
            margin: 0.35rem 1.25rem;
            font-size: 0.95rem;
            font-weight: 600;
            color: #64748b; /* slate-500 */
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            white-space: nowrap;
            position: relative;
            overflow: hidden;
            background: transparent;
        }
        
        .sidebar-nav-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background-color: #ef4444;
            transform: scaleY(0);
            transition: transform 0.3s ease;
            border-radius: 0 4px 4px 0;
        }

        .sidebar-nav-item:hover {
            background: #f8fafc;
            color: #1e293b; /* slate-800 */
            transform: translateX(4px);
        }

        .sidebar-nav-item.active {
            background: linear-gradient(90deg, #fee2e2 0%, #fff1f2 100%);
            color: #b91c1c; /* red-700 */
            box-shadow: 0 1px 3px rgba(220, 38, 38, 0.1);
        }
        
        .sidebar-nav-item.active::before {
            transform: scaleY(1);
        }

        .sidebar-nav-item.active svg {
            color: #dc2626; /* red-600 */
            transform: scale(1.1);
        }
        
        .sidebar-nav-item svg {
            width: 1.3rem;
            height: 1.3rem;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        /* ── Sidebar Section Label ── */
        .sidebar-section-label {
            font-size: 0.625rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #475569; /* slate-600 */
            padding: 0.5rem 1.75rem 0.25rem;
            margin-top: 0.5rem;
        }

        /* ── Mobile overlay ── */
        .sidebar-mobile-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            backdrop-filter: blur(4px);
            z-index: 39;
        }

        /* ── Responsive: Mobile ── */
        @media (max-width: 1023px) {
            .donor-sidebar {
                position: fixed;
                top: 0;
                height: 100vh;
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: none;
            }
        /* hide original */
        .old-donor-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: none;
            }
            .donor-sidebar.open {
                transform: translateX(0);
                box-shadow: 8px 0 32px rgba(0,0,0,0.35);
            }
            .donor-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: calc(100vh - 76px);
            min-width: 0;
        }
        /* hide original */
        .old-donor-content {
                margin-left: 0;
            }
            .sidebar-mobile-overlay.open {
                display: block;
            }
        }

        /* ── Scroll Reveal ── */
        .scroll-reveal {
            opacity: 0;
            --sr-x: 0px;
            --sr-y: 24px;
            transform: translate3d(var(--sr-x), var(--sr-y), 0);
            transition: opacity 0.65s ease, transform 0.65s ease;
            will-change: opacity, transform;
        }
        .scroll-reveal.is-visible,
        [data-scroll-reveal].is-visible {
            opacity: 1;
            --sr-x: 0px;
            --sr-y: 0px;
        }
        @media (prefers-reduced-motion: reduce) {
            .scroll-reveal, [data-scroll-reveal] {
                opacity: 1 !important;
                transform: none !important;
                transition: none !important;
            }
        }
    </style>

    @stack('head')
</head>

<body class="bg-slate-50 text-slate-900 antialiased"
      x-data="{ sidebarOpen: false }"
      @keydown.escape.window="sidebarOpen = false">

{{-- ── Top Header ── --}}
@include('partials.header', ['isDashboard' => true])

<div class="donor-shell flex w-full items-start">

{{-- ── Mobile Overlay ── --}}
<div class="sidebar-mobile-overlay"
     :class="sidebarOpen ? 'open' : ''"
     @click="sidebarOpen = false"
     x-cloak></div>

{{-- ══════════════════════════════════════
     🔴 LEFT SIDEBAR
══════════════════════════════════════ --}}
<aside class="donor-sidebar" :class="sidebarOpen ? 'open' : ''" id="donor-sidebar">

    



    {{-- ── Navigation ── --}}
    <nav class="flex-1 py-6">
        {{-- Main --}}


        <a href="{{ route('donor.dashboard') }}"
           class="sidebar-nav-item {{ request()->routeIs('donor.dashboard') ? 'active' : '' }}" data-tab="overview">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            ওভারভিউ
        </a>

        <a href="{{ route('profile.edit') }}"
           class="sidebar-nav-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}" data-tab="profile">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            আমার প্রোফাইল
        </a>

        {{-- রক্তদান --}}




        <a href="{{ route('requests.my-requests') }}"
           class="sidebar-nav-item {{ request()->routeIs('requests.my-requests') ? 'active' : '' }}" data-tab="my-requests">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            আমার রিকোয়েস্ট
        </a>

        <a href="{{ route('donor.recent_requests') }}"
           class="sidebar-nav-item {{ request()->routeIs('donor.recent_requests') ? 'active' : '' }}" data-tab="recent-requests">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            সাম্প্রতিক রিকোয়েস্ট
        </a>

        <a href="{{ route('donor.blood_history') }}"
           class="sidebar-nav-item {{ request()->routeIs('donor.blood_history') ? 'active' : '' }}" data-tab="blood-history">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            রক্তদান হিস্ট্রি
        </a>

        <a href="{{ route('health-ledger.index') }}"
           class="sidebar-nav-item {{ request()->routeIs('health-ledger.*') ? 'active' : '' }}" data-tab="health-ledger">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            স্বাস্থ্য রেকর্ড
        </a>



        <a href="{{ route('leaderboard') }}"
           class="sidebar-nav-item {{ request()->routeIs('leaderboard') ? 'active' : '' }}" data-tab="leaderboard">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            লিডারবোর্ড
        </a>

        <a href="{{ route('gamification.guide') }}"
           class="sidebar-nav-item {{ request()->routeIs('gamification.guide') ? 'active' : '' }}" data-tab="gamification-guide">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            পয়েন্ট গাইড
        </a>

        
    </nav>

    
</aside>


{{-- ══════════════════════════════════════
     🖥️ MAIN CONTENT WRAPPER
══════════════════════════════════════ --}}
<div class="donor-content">

    {{-- ── Top Header ── --}}
    {{-- ── Flash Messages ── --}}
    @if (session('success'))
        <div class="mx-6 mt-4">
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 font-semibold flex items-center gap-2 text-sm">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="mx-6 mt-4">
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 font-semibold flex items-center gap-2 text-sm">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- ── Page Content ── --}}
    <main class="flex-1" id="page-content">
        @yield('content')
    </main>

    {{-- ── Footer (minimal) ── --}}
    <footer class="border-t border-slate-200 py-4 px-6 flex items-center justify-between">
        <p class="text-xs text-slate-400 font-medium">&copy; {{ date('Y') }} রক্তদূত — রক্তদান প্ল্যাটফর্ম</p>
        <a href="{{ route('home') }}" class="text-xs text-slate-400 hover:text-red-600 font-semibold transition-colors">পাবলিক সাইট &rarr;</a>
    </footer>
</div>
</div> {{-- End donor-shell --}}

{{-- Toast container --}}
<div id="notif-toast-container"
     class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none"
     aria-live="polite"></div>

@stack('scripts')

{{-- Scroll Reveal --}}
<script>
    window.initScrollReveal = function(root = document) {
        const revealItems = root.querySelectorAll('[data-scroll-reveal]');
        if (!revealItems.length) return;
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('is-visible');
                    observer.unobserve(e.target);
                }
            });
        }, { threshold: 0.05, rootMargin: '0px 0px -10% 0px' });
        revealItems.forEach(item => observer.observe(item));
    };
    document.addEventListener('DOMContentLoaded', () => window.initScrollReveal());
</script>

{{-- Lenis smooth scroll --}}
<script src="https://unpkg.com/@studio-freight/lenis@1.0.42/bundled/lenis.min.js"></script>
<style>
    /* SPA Loading bar */
    #spa-progress {
        position: fixed;
        top: 0; left: 0; right: 0;
        height: 2.5px;
        background: linear-gradient(90deg, #dc2626, #f87171, #dc2626);
        background-size: 200% 100%;
        animation: spa-shimmer 1s linear infinite;
        z-index: 9999;
        opacity: 0;
        transition: opacity 0.15s;
        pointer-events: none;
    }
    #spa-progress.active { opacity: 1; }
    @keyframes spa-shimmer {
        0%   { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    /* Content fade transition */
    #page-content {
        transition: opacity 0.18s ease;
    }
    
    /* Hide elements specific to dashboard view */
    .donor-content .hide-in-dashboard {
        display: none !important;
    }
</style>

<div id="spa-progress"></div>

<script>
    (function () {
    'use strict';

    const cache = { overview: true }; // overview is already loaded natively

    const tabLinks = document.querySelectorAll('.sidebar-nav-item[data-tab]');
    const contentEl = document.getElementById('page-content');
    const progressBar = document.getElementById('spa-progress');
    const titleEl = document.querySelector('.donor-header h1');

    function setActiveTab(tabId) {
        tabLinks.forEach(link => {
            if (link.dataset.tab === tabId) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
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

    // Re-init Alpine helper
    function reinitAlpine(el) {
        if (typeof Alpine === 'undefined') return;
        el.querySelectorAll('[x-data]').forEach(node => {
            if (node._x_dataStack) { try { Alpine.destroyTree(node); } catch {} }
        });
        try { Alpine.initTree(el); } catch {}
    }

    function runPageScripts(fetchedDoc, injectEl) {
        const seen = new Set();
        const execCode = (code) => {
            try { new Function(code)(); } catch(e) { console.error(e); }
        };

        const LAYOUT_SIGS = ['Lenis', 'initScrollReveal', '__spaNavigate'];
        const isLayoutScript = (code) => LAYOUT_SIGS.some(s => code.includes(s));

        injectEl.querySelectorAll('script:not([type="module"])').forEach(s => {
            const c = s.textContent;
            if (!c.trim() || seen.has(c) || isLayoutScript(c)) return;
            seen.add(c); execCode(c);
        });

        if (fetchedDoc) {
            fetchedDoc.body?.querySelectorAll('script:not([src]):not([type="module"])').forEach(s => {
                const c = s.textContent;
                if (!c.trim() || seen.has(c) || isLayoutScript(c)) return;
                seen.add(c); execCode(c);
            });
        }
    }

    let currentTab = 'overview';

    async function switchTab(tabId, url) {
        if (tabId === currentTab) return;
        
        currentTab = tabId;
        setActiveTab(tabId);

        // If in cache, render instantly
        if (typeof cache[tabId] === 'string') {
            contentEl.innerHTML = cache[tabId];
            window.scrollTo({ top: 0 });
            document.querySelector('.donor-content')?.scrollTo?.({ top: 0 });
            reinitAlpine(contentEl);
            runPageScripts(null, contentEl);
            if (window.initScrollReveal) {
                requestAnimationFrame(() => window.initScrollReveal(contentEl));
            }
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

            if (res.url.includes('/login') || res.url.includes('/onboarding')) {
                window.location.href = res.url;
                return;
            }

            const html = await res.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');

            // Find the panel specifically
            let newContentHtml = '';
            const panel = doc.querySelector(`[data-panel-id="${tabId}"]`);
            if (panel) {
                newContentHtml = panel.outerHTML;
            } else {
                const pageContent = doc.getElementById('page-content');
                if (pageContent) {
                    newContentHtml = pageContent.innerHTML;
                } else {
                    window.location.href = url;
                    return;
                }
            }

            // Save to cache
            cache[tabId] = newContentHtml;

            // Destroy old alpine
            contentEl.querySelectorAll('[x-data]').forEach(node => {
                if (node._x_dataStack) { try { Alpine.destroyTree?.(node); } catch {} }
            });

            contentEl.innerHTML = newContentHtml;

            // Update title
            const t = doc.querySelector('title')?.textContent;
            if (t) document.title = t;
            
            const spaTitle = doc.querySelector('[data-spa-title]')?.textContent?.trim()
                || doc.querySelector('#page-content h1, #page-content h2')?.textContent?.trim();
            if (titleEl && spaTitle) titleEl.textContent = spaTitle;

            window.scrollTo({ top: 0 });
            document.querySelector('.donor-content')?.scrollTo?.({ top: 0 });

            reinitAlpine(contentEl);
            runPageScripts(doc, contentEl);
            if (window.initScrollReveal) {
                requestAnimationFrame(() => window.initScrollReveal(contentEl));
            }

        } catch (err) {
            console.error('[SPA] fetch error, hard-navigating:', err);
            window.location.href = url;
        } finally {
            fadeIn();
            hideProgress();
        }
    }

    tabLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            // Ignore links with target="_blank" or specific escape hatches
            if (this.getAttribute('target') === '_blank') return;
            if (this.hasAttribute('data-no-spa')) return;
            if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
            
            e.preventDefault();
            switchTab(this.dataset.tab, this.href);
        });
    });

})();
</script>



{{-- FCM Push Notifications --}}
@auth
<script type="module">
(() => {
    const firebaseConfig = {
        apiKey: "{{ config('services.firebase.api_key') }}",
        authDomain: "{{ config('services.firebase.auth_domain') }}",
        projectId: "{{ config('services.firebase.project_id') }}",
        storageBucket: "{{ config('services.firebase.storage_bucket') }}",
        messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
        appId: "{{ config('services.firebase.app_id') }}",
        measurementId: "{{ config('services.firebase.measurement_id') }}"
    };
    const vapidKey = "{{ config('services.firebase.vapid_key') }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const updateTokenUrl = "{{ route('profile.fcm-token.update') }}";

    const requiredKeys = ['apiKey', 'authDomain', 'projectId', 'storageBucket', 'messagingSenderId', 'appId'];
    const hasConfig = requiredKeys.every(k => typeof firebaseConfig[k] === 'string' && firebaseConfig[k].length > 0);
    if (!hasConfig || !vapidKey || !('Notification' in window) || !('serviceWorker' in navigator)) return;

    const initFcm = async () => {
        const [{ initializeApp }, { getMessaging, getToken, isSupported }] = await Promise.all([
            import('https://www.gstatic.com/firebasejs/12.3.0/firebase-app.js'),
            import('https://www.gstatic.com/firebasejs/12.3.0/firebase-messaging.js'),
        ]);
        if (!(await isSupported())) return;
        if ((await Notification.requestPermission()) !== 'granted') return;
        const swReg = await navigator.serviceWorker.register('{{ route('firebase.messaging.sw') }}');
        const messaging = getMessaging(initializeApp(firebaseConfig));
        const token = await getToken(messaging, { vapidKey, serviceWorkerRegistration: swReg });
        if (!token || localStorage.getItem('fcm_token') === token) return;
        const res = await fetch(updateTokenUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            credentials: 'same-origin',
            body: JSON.stringify({ fcm_token: token }),
        });
        if (res.ok) localStorage.setItem('fcm_token', token);
    };
    window.addEventListener('load', () => initFcm().catch(e => console.warn('[FCM]', e)));
})();
</script>
@endauth

</body>
</html>
