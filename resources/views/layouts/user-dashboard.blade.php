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
    <title>@yield('title', 'ইউজার ড্যাশবোর্ড — রক্তদূত')</title>
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

        /* ── User Dashboard Shell ── */
        .user-shell {
            display: flex;
            min-height: calc(100vh - 76px);
            background: #f8fafc;
        }

        /* ── Sidebar ── */
        .user-sidebar {
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
        .user-sidebar::-webkit-scrollbar { display: none; }

        /* ── Main Content ── */
        .user-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: calc(100vh - 76px);
            min-width: 0;
        }
        #user-page-content {
            width: 100%;
            max-width: 72rem;
            margin: 0 auto;
            padding: 1.5rem 1rem 2.5rem;
        }
        @media (min-width: 640px) {
            #user-page-content { padding: 1.75rem 1.5rem 3rem; }
        }
        @media (min-width: 1024px) {
            #user-page-content { padding: 2rem 2rem 3.5rem; }
        }

        /* ── Sidebar Nav Items ── */
        .user-nav-item {
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

        button.user-nav-item {
            appearance: none;
            border: 0;
            cursor: pointer;
            text-align: left;
            font: inherit;
        }

        .user-nav-item:focus { outline: none; }
        .user-nav-item:focus-visible {
            outline: 2px solid rgba(239, 68, 68, 0.35);
            outline-offset: 2px;
        }

        .user-nav-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background-color: #dc2626;
            transform: scaleY(0);
            transition: transform 0.3s ease;
            border-radius: 0 4px 4px 0;
        }

        .user-nav-item:hover {
            background: #f8fafc;
            color: #1e293b;
            transform: translateX(4px);
        }

        .user-nav-item.active {
            background: linear-gradient(90deg, #fee2e2 0%, #fff1f2 100%);
            color: #b91c1c;
            box-shadow: 0 1px 3px rgba(220, 38, 38, 0.1);
        }

        .user-nav-item.active::before { transform: scaleY(1); }

        .user-nav-item.active svg {
            color: #dc2626;
            transform: scale(1.1);
        }

        .user-nav-item svg {
            width: 1.2rem;
            height: 1.2rem;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        /* ── Section Label ── */
        .user-section-label {
            font-size: 0.625rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #475569;
            padding: 0.5rem 1.75rem 0.25rem;
            margin-top: 0.5rem;
        }

        /* ── Sidebar Header ── */
        .user-sidebar-header {
            padding: 1.25rem 1.5rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* ── Mobile overlay ── */
        .user-sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            backdrop-filter: blur(4px);
            z-index: 39;
        }

        /* ── Responsive: Mobile ── */
        @media (max-width: 1023px) {
            .user-sidebar {
                position: fixed;
                top: 0;
                height: 100vh;
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: none;
            }
            .user-sidebar.open {
                transform: translateX(0);
                box-shadow: 8px 0 32px rgba(0,0,0,0.35);
            }
            .user-content {
                flex: 1;
                display: flex;
                flex-direction: column;
                min-height: calc(100vh - 76px);
                min-width: 0;
            }
            .user-sidebar-overlay.open { display: block; }
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

        /* SPA Loading bar */
        #user-spa-progress {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 2.5px;
            background: linear-gradient(90deg, #dc2626, #f87171, #dc2626);
            background-size: 200% 100%;
            animation: user-shimmer 1s linear infinite;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.15s;
            pointer-events: none;
        }
        #user-spa-progress.active { opacity: 1; }
        @keyframes user-shimmer {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        #user-page-content { transition: opacity 0.18s ease; }
    </style>

    @stack('head')
</head>

<body class="bg-slate-50 text-slate-900 antialiased"
      x-data="{ sidebarOpen: false }"
      @keydown.escape.window="sidebarOpen = false">

{{-- ── Top Header ── --}}
@include('partials.header', ['isDashboard' => true])

<div class="user-shell flex w-full items-start">

{{-- ── Mobile Overlay ── --}}
<div class="user-sidebar-overlay"
     :class="sidebarOpen ? 'open' : ''"
     @click="sidebarOpen = false"
     x-cloak></div>

{{-- ══════════════════════════════════════
     🔴 LEFT SIDEBAR
══════════════════════════════════════ --}}
<aside class="user-sidebar" :class="sidebarOpen ? 'open' : ''" id="user-sidebar">

    {{-- Sidebar Header: User Info --}}
    <div class="user-sidebar-header">
        <div class="w-9 h-9 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-sm font-black text-slate-800 truncate">ইউজার ড্যাশবোর্ড</p>
            <p class="text-[10px] font-bold text-slate-500 truncate">রক্তদূত - রক্তের সন্ধান</p>
        </div>
    </div>

    {{-- ── Navigation ── --}}
    <nav class="flex-1 py-4">

        {{-- Overview --}}
        <a href="{{ route('dashboard') }}"
           class="user-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" data-tab="overview">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            ওভারভিউ
        </a>

        <div class="user-section-label">রক্তের সেবা</div>

        <a href="{{ route('requests.create') }}"
           class="user-nav-item {{ request()->routeIs('requests.create') ? 'active' : '' }}" data-tab="new-request">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            নতুন রিকোয়েস্ট
        </a>

        <a href="{{ route('requests.my-requests') }}"
           class="user-nav-item {{ request()->routeIs('requests.my-requests') ? 'active' : '' }}" data-tab="my-requests">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            আমার রিকোয়েস্ট
        </a>

        <a href="{{ route('requests.my-responses') }}"
           class="user-nav-item {{ request()->routeIs('requests.my-responses') ? 'active' : '' }}" data-tab="my-responses">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            রিকোয়েস্টে সাড়া
        </a>

        <a href="{{ route('requests.index') }}"
           class="user-nav-item {{ request()->routeIs('requests.index') ? 'active' : '' }}" data-tab="blood-feed">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            রক্তের ফিড
        </a>

        <a href="{{ route('search') }}"
           class="user-nav-item {{ request()->routeIs('search') ? 'active' : '' }}" data-tab="search">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            ডোনার খুঁজুন
        </a>

        <div class="user-section-label">আপগ্রেড</div>

        {{-- Become a Donor CTA --}}
        @auth
        {{-- Become Donor (Upgrade) --}}
        <a href="{{ route('profile.upgrade') }}"
           class="user-nav-item {{ request()->routeIs('profile.upgrade') ? 'active' : '' }}" data-tab="upgrade">
            <span class="user-nav-icon shadow-sm shadow-red-200">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </span>
            রক্তদাতা হোন
        </a>
        @endauth

        <div class="h-px bg-slate-200 mx-5 my-2"></div>

        <a href="{{ route('profile.edit') }}"
           class="user-nav-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}" data-tab="profile">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            আমার প্রোফাইল
        </a>

        <form method="POST" action="{{ route('logout') }}" x-data>
            @csrf
            <a href="{{ route('logout') }}"
               @click.prevent="$root.submit();"
               data-no-spa
               class="user-nav-item text-slate-600 hover:text-red-600 hover:bg-red-50">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                লগআউট
            </a>
        </form>

    </nav>

    {{-- User info at bottom --}}
    @auth
    <div class="px-4 py-4 border-t border-slate-200 mt-auto">
        <div class="flex items-center gap-3 px-2">
            <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center font-black text-sm shrink-0">
                {{ mb_substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="min-w-0">
                <p class="text-sm font-bold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Recipient</p>
            </div>
        </div>
    </div>
    @endauth

</aside>


{{-- ══════════════════════════════════════
     🖥️ MAIN CONTENT WRAPPER
══════════════════════════════════════ --}}
<div class="user-content">

    {{-- ── Mobile topbar (hamburger) ── --}}
    <div class="lg:hidden flex items-center gap-3 px-4 py-3 bg-white border-b border-slate-200 sticky top-0 z-30">
        <button @click="sidebarOpen = true"
                class="w-9 h-9 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <span class="text-sm font-black text-slate-800">ইউজার ড্যাশবোর্ড</span>
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
    <main class="flex-1" id="user-page-content">
        @yield('content')
    </main>

</div>
</div> {{-- End user-shell --}}

{{-- ── Shared Footer ── --}}
@include('layouts.footer')

{{-- SPA Progress Bar --}}
<div id="user-spa-progress"></div>

{{-- Toast container --}}
<div id="notif-toast-container"
     class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none"
     aria-live="polite"></div>

@stack('scripts')

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
    const navLinks = document.querySelectorAll('.user-nav-item[data-tab]');
    const contentEl = document.getElementById('user-page-content');
    const progressBar = document.getElementById('user-spa-progress');

    if (!contentEl) return;

    function setActive(tabId) {
        navLinks.forEach(link => {
            link.classList.toggle('active', link.dataset.tab === tabId);
        });
    }

    function showProgress() { progressBar?.classList.add('active'); }
    function hideProgress() { progressBar?.classList.remove('active'); }

    function fadeOut() {
        contentEl.style.opacity = '0.4';
        contentEl.style.pointerEvents = 'none';
    }

    function fadeIn() {
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
        const SKIP_SIGS = ['initScrollReveal', '__userSpaNavigate'];
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

    let currentTab = document.querySelector('.user-nav-item[data-tab].active')?.dataset?.tab || 'overview';

    async function switchTab(tabId, url) {
        if (tabId === currentTab && cache[tabId]) return;
        currentTab = tabId;
        setActive(tabId);

        // Mobile: close sidebar after nav click
        const bodyEl = document.querySelector('body[x-data]');
        if (bodyEl && bodyEl._x_dataStack) {
            try { bodyEl._x_dataStack[0].sidebarOpen = false; } catch {}
        }

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
                const pageContent = doc.getElementById('user-page-content');
                if (pageContent) {
                    newHtml = pageContent.innerHTML;
                } else {
                    const fallbackMain = doc.querySelector('main');
                    if (fallbackMain) {
                        newHtml = fallbackMain.innerHTML;
                    } else {
                        window.location.href = url;
                        return;
                    }
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
            console.error('[User SPA] Error:', err);
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

    window.__userSpaNavigate = switchTab;
    window.__userSpaSetActive = setActive;

    window.addEventListener('popstate', function(e) {
        if (e.state && e.state.tab) {
            const link = document.querySelector(`.user-nav-item[data-tab="${e.state.tab}"]`);
            if (link) switchTab(e.state.tab, link.href);
        }
    });
})();
</script>

</body>
</html>
