<!doctype html>
<html lang="bn">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="theme-color" content="#2563eb" />
    <title>@yield('title', 'অ্যাডমিন ড্যাশবোর্ড — রক্তদূত')</title>
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

        /* ── Admin Dashboard Shell ── */
        .admin-shell {
            display: flex;
            min-height: calc(100vh - 73px); /* subtract shared header height */
            background: #f8fafc;
        }

        /* ── Sidebar ── */
        .admin-sidebar {
            width: 280px;
            min-width: 280px;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;          /* sticks just below the shared header */
            height: calc(100vh - 73px); /* fill viewport minus header */
            z-index: 40;
            border-right: 1px solid #e2e8f0;
            overflow-y: auto;
            scrollbar-width: none;
        }
        .admin-sidebar::-webkit-scrollbar { display: none; }

        /* ── Main Content ── */
        .admin-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-width: 0;
        }
        #admin-page-content {
            width: 100%;
            max-width: 80rem;
            margin: 0 auto;
            padding: 1.5rem 1rem 2.5rem;
        }
        @media (min-width: 640px) {
            #admin-page-content { padding: 1.75rem 1.5rem 3rem; }
        }
        @media (min-width: 1024px) {
            #admin-page-content { padding: 2rem 2rem 3.5rem; }
        }

        /* ── Sidebar Nav Items ── */
        .admin-nav-item {
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

        .admin-nav-item::before {
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

        .admin-nav-item:hover {
            background: #f8fafc;
            color: #1e293b;
            transform: translateX(4px);
        }

        .admin-nav-item.active {
            background: linear-gradient(90deg, #dbeafe 0%, #eff6ff 100%);
            color: #1d4ed8;
            box-shadow: 0 1px 3px rgba(37, 99, 235, 0.1);
        }

        .admin-nav-item.active::before {
            transform: scaleY(1);
        }

        .admin-nav-item.active svg {
            color: #2563eb;
            transform: scale(1.1);
        }

        .admin-nav-item svg {
            width: 1.2rem;
            height: 1.2rem;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        /* Badge on nav item */
        .admin-nav-badge {
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
        .admin-sidebar-header {
            padding: 1.25rem 1.5rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* ── Section label ── */
        .admin-section-label {
            font-size: 0.6rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #94a3b8;
            padding: 0.75rem 1.5rem 0.25rem;
        }

        /* ── Top header bar ── */
        .admin-topbar {
            height: 56px;
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

        /* ── Mobile overlay ── */
        .admin-sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            backdrop-filter: blur(4px);
            z-index: 39;
        }

        /* ── Responsive ── */
        @media (max-width: 1023px) {
            .admin-sidebar {
                position: fixed;
                top: 0;
                height: 100vh;
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .admin-sidebar.open {
                transform: translateX(0);
                box-shadow: 8px 0 32px rgba(0,0,0,0.35);
            }
            .admin-sidebar-overlay.open { display: block; }
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
        #admin-spa-progress {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 2.5px;
            background: linear-gradient(90deg, #2563eb, #60a5fa, #2563eb);
            background-size: 200% 100%;
            animation: admin-shimmer 1s linear infinite;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.15s;
            pointer-events: none;
        }
        #admin-spa-progress.active { opacity: 1; }
        @keyframes admin-shimmer {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        #admin-page-content { transition: opacity 0.18s ease; }
    </style>

    @stack('head')
</head>

<body class="bg-slate-50 text-slate-900 antialiased"
      x-data="{ sidebarOpen: false }"
      @keydown.escape.window="sidebarOpen = false">

{{-- ── Shared Site Header ── --}}
@include('partials.header', ['isDashboard' => true])

<div class="admin-shell">

    {{-- ── Mobile Overlay ── --}}
    <div class="admin-sidebar-overlay"
         :class="sidebarOpen ? 'open' : ''"
         @click="sidebarOpen = false"
         x-cloak></div>

    {{-- ══════════════════════════════════════
         🔴 LEFT SIDEBAR
    ══════════════════════════════════════ --}}
    <aside class="admin-sidebar" :class="sidebarOpen ? 'open' : ''" id="admin-sidebar">


        {{-- Sidebar Header: System Admin Info --}}
        <div class="admin-sidebar-header">
            <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-black text-slate-800 truncate">সিস্টেম অ্যাডমিন</p>
                <p class="text-[10px] font-bold text-slate-500 truncate">রক্তদূত সেন্ট্রাল কমান্ড</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 py-4">

            <a href="{{ route('admin.dashboard') }}"
               class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" data-tab="overview">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                ওভারভিউ
            </a>

            <div class="admin-section-label">ভেরিফিকেশন</div>

            <a href="{{ route('admin.donations.proof_reviews') }}"
               class="admin-nav-item {{ request()->routeIs('admin.donations.proof_reviews') ? 'active' : '' }}" data-tab="proof-reviews">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                পেন্ডিং ভেরিফিকেশন
                @if(isset($pendingClaimsCount) && $pendingClaimsCount > 0)
                    <span class="admin-nav-badge">{{ $pendingClaimsCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.nid.reviews') }}"
               class="admin-nav-item {{ request()->routeIs('admin.nid.reviews') ? 'active' : '' }}" data-tab="nid-reviews">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                NID ভেরিফিকেশন
                @if(isset($pendingNidsCount) && $pendingNidsCount > 0)
                    <span class="admin-nav-badge">{{ $pendingNidsCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.org.reviews') }}"
               class="admin-nav-item {{ request()->routeIs('admin.org.reviews') ? 'active' : '' }}" data-tab="org-reviews">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                অর্গ/হাসপাতাল যাচাই
                @if(isset($pendingOrgsCount) && $pendingOrgsCount > 0)
                    <span class="admin-nav-badge">{{ $pendingOrgsCount }}</span>
                @endif
            </a>

            <div class="admin-section-label">মডারেশন</div>

            <a href="{{ route('admin.support.messages.index') }}"
               class="admin-nav-item {{ request()->routeIs('admin.support.messages.*') ? 'active' : '' }}" data-tab="support">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                সাপোর্ট ইনবক্স
                @if(isset($pendingSupportCount) && $pendingSupportCount > 0)
                    <span class="admin-nav-badge">{{ $pendingSupportCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.blog.moderation.index') }}"
               class="admin-nav-item {{ request()->routeIs('admin.blog.moderation.*') ? 'active' : '' }}" data-tab="blog-moderation">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                ব্লগ মডারেশন
                @if(isset($pendingBlogCount) && $pendingBlogCount > 0)
                    <span class="admin-nav-badge">{{ $pendingBlogCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.reports.index') }}"
               class="admin-nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" data-tab="reports">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                রিপোর্ট
                @if(isset($pendingReportsCount) && $pendingReportsCount > 0)
                    <span class="admin-nav-badge">{{ $pendingReportsCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.spam-radar.index') }}"
               class="admin-nav-item {{ request()->routeIs('admin.spam-radar.*') ? 'active' : '' }}" data-tab="spam-radar">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                স্প্যাম রাডার
            </a>

            <a href="{{ route('admin.gamification.index') }}"
               class="admin-nav-item {{ request()->routeIs('admin.gamification.*') ? 'active' : '' }}" data-tab="gamification">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                গেমিফিকেশন
            </a>

            <div class="admin-section-label">অ্যানালিটিক্স</div>

            <a href="{{ route('admin.analytics.index') }}"
               class="admin-nav-item {{ request()->routeIs('admin.analytics.index') ? 'active' : '' }}" data-tab="analytics">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                অ্যানালিটিক্স
            </a>

            <a href="{{ route('admin.heatmap.index') }}"
               class="admin-nav-item {{ request()->routeIs('admin.heatmap.*') ? 'active' : '' }}" data-tab="heatmap">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                হিটম্যাপ
            </a>

            <a href="{{ route('admin.audit-logs.index') }}"
               class="admin-nav-item {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}" data-tab="audit-logs">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                অডিট লগ
            </a>

            <div class="admin-section-label">ম্যানেজমেন্ট</div>

            <a href="{{ route('admin.chronic.index') }}"
               class="admin-nav-item {{ request()->routeIs('admin.chronic.*') ? 'active' : '' }}" data-tab="chronic">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                দীর্ঘমেয়াদী রোগী
            </a>

            <a href="{{ route('admin.hospitals.unverified') }}"
               class="admin-nav-item {{ request()->routeIs('admin.hospitals.*') ? 'active' : '' }}" data-tab="hospitals">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                হাসপাতাল যাচাই
                @if(isset($pendingHospitalsCount) && $pendingHospitalsCount > 0)
                    <span class="admin-nav-badge">{{ $pendingHospitalsCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.ambulances.index') }}"
               class="admin-nav-item {{ request()->routeIs('admin.ambulances.*') ? 'active' : '' }}" data-tab="ambulances">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                অ্যাম্বুলেন্স
                @if(isset($pendingAmbulancesCount) && $pendingAmbulancesCount > 0)
                    <span class="admin-nav-badge">{{ $pendingAmbulancesCount }}</span>
                @endif
            </a>

            <div class="h-px bg-slate-100 mx-4 my-2"></div>

            <a href="{{ route('profile.edit') }}"
               class="admin-nav-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}" data-tab="profile">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                প্রোফাইল
            </a>

            <form method="POST" action="{{ route('logout') }}" x-data>
                @csrf
                <a href="{{ route('logout') }}"
                   @click.prevent="$root.submit();"
                   class="admin-nav-item text-slate-500 hover:text-red-600 hover:bg-red-50">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    লগআউট
                </a>
            </form>

        </nav>

        {{-- Admin info at bottom --}}
        @auth
        <div class="px-4 py-4 border-t border-slate-100 mt-auto">
            <div class="flex items-center gap-3 px-2">
                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-black text-sm shrink-0">
                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] font-bold text-blue-500 uppercase tracking-wider">Admin</p>
                </div>
            </div>
        </div>
        @endauth

    </aside>

    {{-- ══════════════════════════════════════
         🖥️ MAIN CONTENT AREA
    ══════════════════════════════════════ --}}
    <div class="admin-content">

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
        <main class="flex-1" id="admin-page-content">
            @yield('content')
        </main>

    </div>
</div>

{{-- ── Shared Footer ── --}}
@include('layouts.footer')

{{-- SPA Progress Bar --}}
<div id="admin-spa-progress"></div>

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
    const navLinks = document.querySelectorAll('.admin-nav-item[data-tab]');
    const contentEl = document.getElementById('admin-page-content');
    const progressBar = document.getElementById('admin-spa-progress');

    function setActive(tabId) {
        navLinks.forEach(link => {
            link.classList.toggle('active', link.dataset.tab === tabId);
        });
    }

    function showProgress() { progressBar?.classList.add('active'); }
    function hideProgress() { progressBar?.classList.remove('active'); }

    function reinitAlpine(el) {
        if (typeof Alpine !== 'undefined' && Alpine.initTree) {
            Alpine.initTree(el);
        }
    }

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
        const SKIP_SIGS = ['initScrollReveal', '__adminSpaNavigate'];
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

    let currentTab = document.querySelector('.admin-nav-item[data-tab].active')?.dataset?.tab || 'overview';

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
                const pageContent = doc.getElementById('admin-page-content');
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
            console.error('[Admin SPA] Error:', err);
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

    window.__adminSpaNavigate = switchTab;
    window.__adminSpaSetActive = setActive;

    window.addEventListener('popstate', function(e) {
        if (e.state && e.state.tab) {
            switchTab(e.state.tab, window.location.href);
        } else {
            window.location.reload();
        }
    });
})();
</script>

@include('layouts.chatbot-widget')

@stack('scripts')

</body>
</html>
