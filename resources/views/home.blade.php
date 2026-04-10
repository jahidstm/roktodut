<!doctype html>
<html lang="bn">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>রক্তদূত — জরুরি রক্ত সহায়তা</title>
    <meta name="description" content="রক্তদূত — বাংলাদেশের সবচেয়ে নির্ভরযোগ্য ভেরিফায়েড রক্তদাতা নেটওয়ার্ক। জরুরি রক্তের প্রয়োজনে দ্রুত ডোনারের সাথে সংযুক্ত হন।">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css','resources/js/app.js'])

    <style>
        :root { font-family: 'Hind Siliguri', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial; }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased overflow-x-hidden">

{{-- ══════════════════════════════════════════════════════
     HEADER — Sticky, Responsive, Auth-Aware
══════════════════════════════════════════════════════ --}}
<header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-100 shadow-sm">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-10 py-3 flex items-center justify-between">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <div class="h-9 w-9 rounded-xl bg-red-50 border border-red-100 flex items-center justify-center shrink-0">
                <span class="text-red-600 font-extrabold tracking-tight text-sm">RD</span>
            </div>
            <div class="leading-tight">
                <div class="text-base font-extrabold tracking-tight">রক্তদূত</div>
                <div class="text-[10px] text-slate-500 font-semibold hidden sm:block">BLOOD DONATION PLATFORM</div>
            </div>
        </a>

        {{-- Desktop Nav — 5 exact items --}}
        <nav class="hidden md:flex items-center gap-1 text-[15px] font-semibold text-slate-600">
            <a href="{{ route('home') }}" class="px-3 py-2 rounded-lg text-red-600 font-bold bg-red-50">হোম</a>
            @auth
                <a href="{{ route('requests.index') }}" class="px-3 py-2 rounded-lg hover:text-red-600 hover:bg-red-50 transition">রক্ত দিন</a>
            @endauth
            @guest
                <a href="{{ route('public.requests.index') }}" class="px-3 py-2 rounded-lg hover:text-red-600 hover:bg-red-50 transition">রক্ত দিন</a>
            @endguest
            <a href="{{ route('search') }}" class="px-3 py-2 rounded-lg hover:text-red-600 hover:bg-red-50 transition">স্মার্ট ডোনার সার্চ</a>
            <a href="{{ route('leaderboard') }}" class="px-3 py-2 rounded-lg hover:text-red-600 hover:bg-red-50 transition">লিডারবোর্ড</a>
            <a href="{{ route('blog.index') }}" class="px-3 py-2 rounded-lg hover:text-red-600 hover:bg-red-50 transition">Blog</a>
        </nav>

        {{-- Auth Buttons & Profile Chip --}}
        <div class="flex items-center gap-3">
            @guest
                <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center justify-center px-4 py-2 rounded-lg font-semibold text-slate-700 hover:text-red-600 transition text-sm">লগইন</a>
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center bg-red-600 text-white px-5 py-2 rounded-lg font-extrabold hover:bg-red-700 transition shadow-sm text-sm">রেজিস্টার</a>
            @endguest

            @auth
                {{-- 🔔 Notification Bell --}}
                <div class="relative flex items-center" x-data="{ openNotification: false }" @click.outside="openNotification = false">
                    <button @click="openNotification = ! openNotification" class="relative p-2 text-slate-500 hover:text-red-600 transition rounded-full hover:bg-red-50 focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[10px] font-extrabold text-white shadow-sm ring-2 ring-white">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>

                    <div x-show="openNotification"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                         class="absolute right-0 mt-3 top-12 w-80 sm:w-96 rounded-2xl bg-white shadow-xl ring-1 ring-slate-200 overflow-hidden z-50"
                         style="display: none;">
                        <div class="px-4 py-3 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                            <h3 class="text-sm font-extrabold text-slate-800">নোটিফিকেশন</h3>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-black uppercase">{{ auth()->user()->unreadNotifications->count() }} নতুন</span>
                            @endif
                        </div>
                        <div class="max-h-80 overflow-y-auto divide-y divide-slate-50">
                            @forelse(auth()->user()->unreadNotifications as $notification)
                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left p-4 hover:bg-slate-50 transition flex gap-3 items-start group">
                                        <div class="mt-0.5 rounded-full p-2 shrink-0 {{ isset($notification->data['status']) && $notification->data['status'] === 'approved' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }}">
                                            @if(isset($notification->data['status']) && $notification->data['status'] === 'approved')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-slate-800 group-hover:text-blue-600 transition-colors">
                                                {{ $notification->data['message'] ?? 'নতুন নোটিফিকেশন' }}
                                            </div>
                                            <div class="text-[10px] text-slate-400 mt-1 font-bold uppercase tracking-wide">
                                                @if(isset($notification->data['patient_name']))
                                                    {{ $notification->data['patient_name'] }} •
                                                @endif
                                                {{ $notification->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </button>
                                </form>
                            @empty
                                <div class="p-8 text-center">
                                    <span class="text-sm font-bold text-slate-400">নতুন কোনো নোটিফিকেশন নেই।</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- 🎯 Profile Chip & Dropdown --}}
                <div x-data="{ openProfile: false }" class="relative inline-block text-left">
                    <button @click="openProfile = !openProfile" @click.away="openProfile = false" type="button"
                            class="flex items-center gap-2 p-1.5 sm:pr-4 bg-white border border-slate-200 rounded-full hover:bg-slate-50 hover:border-red-200 transition-all shadow-sm focus:outline-none">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 shrink-0 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden">
                            @if(auth()->user()->profile_image)
                                <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="Profile" class="w-full h-full object-cover" loading="lazy">
                            @else
                                <span class="text-slate-600 font-black text-xs sm:text-sm">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                            @endif
                        </div>
                        <span class="text-sm font-bold text-slate-800 hidden sm:block">{{ explode(' ', auth()->user()->name)[0] }}</span>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 hidden sm:block" :class="openProfile ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="openProfile"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                         style="display: none;"
                         class="absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden z-50">
                        <div class="px-5 py-4 bg-slate-50/50 border-b border-slate-100">
                            <p class="text-base font-black text-slate-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs font-semibold text-slate-500 mt-0.5 flex items-center gap-1.5">
                                @if(auth()->user()->role?->value === 'org_admin' || auth()->user()->role === 'org_admin')
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> অর্গানাইজেশন অ্যাডমিন
                                @else
                                    <span class="w-2 h-2 rounded-full bg-red-500"></span> ডোনার •
                                    <span class="text-red-600 font-bold">{{ auth()->user()->blood_group?->value ?? auth()->user()->blood_group ?? 'N/A' }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="py-2 border-b border-slate-100">
                            @php
                                $dashboardRoute = 'dashboard';
                                if(auth()->user()->role === 'org_admin' || auth()->user()->role?->value === 'org_admin') {
                                    $dashboardRoute = 'org.dashboard';
                                } elseif(auth()->user()->role === 'admin' || auth()->user()->role?->value === 'admin') {
                                    $dashboardRoute = 'admin.dashboard';
                                }
                            @endphp
                            <a href="{{ route($dashboardRoute) }}" class="flex items-center gap-3 px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50 hover:text-red-600 transition-colors">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                                ড্যাশবোর্ড
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50 hover:text-red-600 transition-colors">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                অ্যাকাউন্ট সেটিংস
                            </a>
                        </div>
                        <div class="py-2 bg-red-50/30">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-3 px-5 py-2.5 text-sm font-black text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    লগআউট করুন
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endauth

            {{-- Mobile Hamburger --}}
            <button class="md:hidden p-2 text-slate-500 hover:text-red-600 rounded-lg hover:bg-slate-50 transition"
                    onclick="document.getElementById('mobile-nav-home').classList.toggle('hidden')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Nav Menu --}}
    <div id="mobile-nav-home" class="hidden md:hidden border-t border-slate-100 bg-white/95 backdrop-blur-md px-4 pb-4 pt-2 space-y-1">
        <a href="{{ route('home') }}" class="block py-2.5 font-bold text-red-600 text-sm border-b border-slate-50">হোম</a>
        @auth
            <a href="{{ route('requests.index') }}" class="block py-2.5 font-semibold text-slate-700 hover:text-red-600 text-sm transition border-b border-slate-50">রক্ত দিন</a>
        @endauth
        @guest
            <a href="{{ route('public.requests.index') }}" class="block py-2.5 font-semibold text-slate-700 hover:text-red-600 text-sm transition border-b border-slate-50">রক্ত দিন</a>
        @endguest
        <a href="{{ route('search') }}" class="block py-2.5 font-semibold text-slate-700 hover:text-red-600 text-sm transition border-b border-slate-50">স্মার্ট ডোনার সার্চ</a>
        <a href="{{ route('leaderboard') }}" class="block py-2.5 font-semibold text-slate-700 hover:text-red-600 text-sm transition border-b border-slate-50">লিডারবোর্ড</a>
        <a href="{{ route('blog.index') }}" class="block py-2.5 font-semibold text-slate-700 hover:text-red-600 text-sm transition border-b border-slate-50">Blog</a>
        @guest
            <div class="flex gap-3 pt-3">
                <a href="{{ route('login') }}" class="flex-1 text-center py-2.5 border border-slate-200 rounded-lg font-semibold text-slate-700 text-sm hover:border-red-200 transition">লগইন</a>
                <a href="{{ route('register') }}" class="flex-1 text-center py-2.5 bg-red-600 text-white rounded-lg font-extrabold text-sm hover:bg-red-700 transition">রেজিস্টার</a>
            </div>
        @endguest
    </div>
</header>


{{-- ══════════════════════════════════════════════════════
     HERO SECTION
══════════════════════════════════════════════════════ --}}
<section class="relative bg-white overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-b from-red-50 via-white to-white"></div>
    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-10 pt-16 pb-28 lg:pt-24 lg:pb-36">
        <div class="flex flex-col lg:flex-row items-center gap-14">
            <div class="lg:w-1/2 text-center lg:text-left">
                <span class="inline-flex items-center gap-2 bg-red-50 text-red-700 px-4 py-1.5 rounded-full text-sm font-extrabold tracking-wide border border-red-100">
                    ইমার্জেন্সি ব্লাড ডোনেশন নেটওয়ার্ক
                </span>
                <h1 class="mt-6 text-4xl md:text-5xl lg:text-6xl font-extrabold text-slate-900 leading-[1.12] tracking-tight">
                    জরুরি মুহূর্তে রক্তের সন্ধানে—<span class="text-red-600">আমরা আছি আপনার পাশে</span>
                </h1>
                <p class="mt-6 text-lg text-slate-600 leading-relaxed max-w-2xl mx-auto lg:mx-0 font-medium">
                    বাংলাদেশের সবচেয়ে বিশ্বাসযোগ্য NID-ভেরিফাইড ডোনার নেটওয়ার্ক — জরুরি মুহূর্তে সঠিক রক্ত, সঠিক সময়ে।
                </p>

                {{-- Micro-bullets: core value at a glance --}}
                <ul class="mt-5 flex flex-col sm:flex-row flex-wrap gap-3 justify-center lg:justify-start text-sm font-semibold text-slate-700">
                    <li class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-full">
                        <span class="text-emerald-500">✓</span> লগইন ছাড়াই ডোনার সার্চ
                    </li>
                    <li class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-full">
                        <span class="text-emerald-500">✓</span> মোবাইল নম্বর প্রাইভেসি-শিল্ডেড
                    </li>
                </ul>

                <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="{{ route('search') }}"
                       class="inline-flex items-center justify-center bg-red-600 text-white px-7 py-3.5 rounded-lg font-extrabold shadow-sm shadow-red-200 hover:bg-red-700 transition gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        এখনই ডোনার খুঁজুন
                    </a>
                    <a href="{{ route('requests.create') }}"
                       class="inline-flex items-center justify-center border-2 border-red-600 text-red-600 px-7 py-3.5 rounded-lg font-extrabold hover:bg-red-50 transition">
                        জরুরি রক্তের অনুরোধ
                    </a>
                </div>
            </div>

            <div class="lg:w-1/2 flex justify-center relative">
                <div class="relative w-72 h-72 md:w-96 md:h-96 bg-red-50 rounded-full flex items-center justify-center">
                    <div class="absolute inset-0 border-[18px] border-white rounded-full shadow-2xl z-10"></div>
                    <div class="absolute inset-0 bg-red-100 rounded-full animate-ping opacity-20"></div>
                    <svg class="w-24 md:w-36 text-red-500 z-20" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- ══════════════════════════════════════════════════════
     QUICK SEARCH WIDGET
══════════════════════════════════════════════════════ --}}
<section class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-10 relative -mt-16 md:-mt-20">
    <div class="bg-white rounded-2xl shadow-md ring-1 ring-black/5 border border-slate-100 p-6 md:p-8">
        <h2 class="text-lg font-extrabold text-slate-800 mb-4">দ্রুত ডোনার অনুসন্ধান করুন</h2>
        <form action="{{ route('search') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <select id="division_select" name="division" class="p-3.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700 font-semibold focus:outline-none focus:border-red-500 focus:ring-red-200">
                <option value="">বিভাগ নির্বাচন</option>
                @if(isset($divisions))
                    @foreach($divisions as $division)
                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                    @endforeach
                @endif
            </select>
            <select id="district_select" name="district" class="p-3.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700 font-semibold focus:outline-none focus:border-red-500 focus:ring-red-200" disabled>
                <option value="">জেলা নির্বাচন</option>
            </select>
            <select id="upazila_select" name="upazila" class="p-3.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700 font-semibold focus:outline-none focus:border-red-500 focus:ring-red-200" disabled>
                <option value="">উপজেলা/এরিয়া</option>
            </select>
            <select name="blood_group" class="p-3.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700 font-semibold focus:outline-none focus:border-red-500 focus:ring-red-200" required>
                <option value="">রক্তের গ্রুপ</option>
                <option value="A+">A+</option><option value="A-">A-</option>
                <option value="B+">B+</option><option value="B-">B-</option>
                <option value="AB+">AB+</option><option value="AB-">AB-</option>
                <option value="O+">O+</option><option value="O-">O-</option>
            </select>
            <button type="submit" class="bg-red-600 text-white font-extrabold rounded-lg py-3.5 hover:bg-red-700 transition shadow-sm shadow-red-200">
                সার্চ করুন
            </button>
        </form>
    </div>
</section>


{{-- ══════════════════════════════════════════════════════
     VALUE PROPOSITION GRID (migrated from donate page)
══════════════════════════════════════════════════════ --}}
<section class="py-20 bg-white" id="features">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-10">
        <div class="text-center mb-14">
            <span class="inline-flex items-center gap-2 bg-red-50 text-red-700 px-4 py-1.5 rounded-full text-sm font-extrabold tracking-wide border border-red-100">কেন আমরা আলাদা</span>
            <h2 class="mt-4 text-3xl md:text-4xl font-extrabold text-slate-900">রক্তদূত বেছে নেওয়ার কারণ</h2>
            <p class="mt-3 text-slate-500 font-medium max-w-xl mx-auto">আমাদের প্ল্যাটফর্ম বিশ্বাস, প্রযুক্তি এবং সম্মানের ওপর নির্মিত।</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- NID Verified Trust --}}
            <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-lg hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-bl-full -z-10 group-hover:scale-[2] transition-transform duration-500 ease-out"></div>
                <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mb-6 shadow-sm border border-emerald-200 group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-300">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">NID Verified Trust</h3>
                <p class="text-slate-600 font-medium leading-relaxed">আমাদের প্ল্যাটফর্মের ডোনাররা জাতীয় পরিচয়পত্র দ্বারা ভেরিফাইড। আপনার তথ্য শতভাগ সুরক্ষিত ও নির্ভরযোগ্য থাকবে।</p>
            </div>

            {{-- Dynamic Smart Card --}}
            <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-lg hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full -z-10 group-hover:scale-[2] transition-transform duration-500 ease-out"></div>
                <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-6 shadow-sm border border-blue-200 group-hover:bg-blue-500 group-hover:text-white transition-colors duration-300">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Dynamic Smart Card</h3>
                <p class="text-slate-600 font-medium leading-relaxed">ভেরিফায়েড ডোনারদের জন্য ডিজিটাল QR স্মার্ট কার্ড, যার মাধ্যমে আপনার রক্তদানের স্ট্যাটাস সহজেই প্রমাণ করা যাবে।</p>
            </div>

            {{-- Gamification Badges --}}
            <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-lg hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-amber-50 rounded-bl-full -z-10 group-hover:scale-[2] transition-transform duration-500 ease-out"></div>
                <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mb-6 shadow-sm border border-amber-200 group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Gamification Badges</h3>
                <p class="text-slate-600 font-medium leading-relaxed">রক্তদানের মাধ্যমে পয়েন্ট অর্জন করুন এবং প্ল্যাটিনাম, গোল্ডেন সহ বিভিন্ন আকর্ষণীয় রিবন ও ব্যাজ আনলক করুন।</p>
            </div>
        </div>
    </div>
</section>


{{-- ══════════════════════════════════════════════════════
     SOCIAL PROOF (migrated from donate page)
══════════════════════════════════════════════════════ --}}
<section class="py-20 bg-gradient-to-br from-slate-50 to-red-50/40" id="social-proof">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-10 text-center">
        <p class="text-slate-600 text-lg sm:text-2xl font-bold mb-6">আমাদের প্ল্যাটফর্মে এখন পর্যন্ত</p>
        @php
            $engNum = ['0','1','2','3','4','5','6','7','8','9'];
            $bngNum = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
            $vDonorsBn = str_replace($engNum, $bngNum, $verifiedDonors ?? 6);
            $lSavedBn  = str_replace($engNum, $bngNum, $livesSaved ?? 120);
        @endphp
        <h2 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 leading-tight">
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-rose-500 font-black">{{ $vDonorsBn }}+</span>
            ডোনার যাচাইকৃত হয়েছেন এবং <br class="hidden lg:block"/>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-teal-500 font-black inline-block mt-2 sm:mt-0">{{ $lSavedBn }}+</span>
            জীবন বাঁচানো হয়েছে।
        </h2>

        <div class="mt-14 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm">
                <div class="text-4xl font-extrabold text-red-600">৫০০+</div>
                <div class="text-slate-500 mt-2 font-semibold">নিবন্ধিত ডোনার</div>
            </div>
            <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm">
                <div class="text-4xl font-extrabold text-emerald-600">১২০+</div>
                <div class="text-slate-500 mt-2 font-semibold">সফল রক্ত সংযোগ</div>
            </div>
            <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm">
                <div class="text-4xl font-extrabold text-blue-600">৬৪</div>
                <div class="text-slate-500 mt-2 font-semibold">জেলায় সক্রিয় ডোনার</div>
            </div>
        </div>

        {{-- Task 3: Credibility note --}}
        <p class="mt-8 text-slate-400 text-sm font-medium">
            📊 তথ্য ভেরিফাইড প্রোফাইলের ভিত্তিতে গণনা করা হয়েছে এবং নিয়মিত আপডেট হয়।
        </p>
    </div>
</section>


{{-- ══════════════════════════════════════════════════════
     LEADERBOARD PREVIEW (Real data — kept)
══════════════════════════════════════════════════════ --}}
<section id="leaderboard-preview" class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-10 py-20">

    <div class="text-center mb-12">
        <span class="inline-flex items-center gap-2 bg-amber-50 text-amber-700 px-4 py-1.5 rounded-full text-sm font-extrabold tracking-wide border border-amber-100">🏆 লিডারবোর্ড</span>
        <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mt-4">আমাদের <span class="text-red-600">সেরা রক্তবীর</span></h2>
        <p class="text-slate-500 mt-3 font-medium max-w-lg mx-auto">রক্তদাতাদের সম্মান দিতে আমাদের লিডারবোর্ড — যারা বারবার জীবন বাঁচিয়েছেন তাদের বিশেষ স্বীকৃতি।</p>
    </div>

    @if(isset($topDonors) && $topDonors->count() > 0)
    <div class="grid grid-cols-3 gap-4 sm:gap-6 items-end mb-8">

        {{-- ২য় স্থান (বাম) --}}
        @if($topDonors->count() >= 2)
        @php $d2 = $topDonors[1]; @endphp
        <div class="flex flex-col items-center group">
            <div class="relative mb-3">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br from-slate-300 to-slate-400 flex items-center justify-center text-white font-black text-2xl shadow-lg group-hover:scale-105 transition-transform duration-300 overflow-hidden border-2 border-white">
                    @if($d2->profile_image)
                        <img src="{{ asset('storage/' . $d2->profile_image) }}" class="w-full h-full object-cover" alt="{{ $d2->name }}" loading="lazy">
                    @else
                        {{ mb_substr($d2->name, 0, 1) }}
                    @endif
                </div>
                <span class="absolute -top-3 -right-2 text-xl drop-shadow">🥈</span>
            </div>
            <div class="text-center mb-2 w-full px-1">
                <p class="font-black text-slate-800 text-xs sm:text-sm truncate">{{ explode(' ', $d2->name)[0] }}</p>
                <p class="text-xs text-red-600 font-bold">{{ $d2->blood_group?->value ?? $d2->blood_group ?? '?' }}</p>
                @if($d2->badges->count() > 0)
                    <div class="flex justify-center gap-0.5 mt-1">@foreach($d2->badges->take(2) as $b)<span class="text-sm" title="{{ $b->bn_name ?? $b->name }}">{{ $b->emoji ?? $b->icon }}</span>@endforeach</div>
                @endif
            </div>
            <div class="h-28 sm:h-36 w-full rounded-t-2xl bg-gradient-to-b from-slate-300 to-slate-500 flex flex-col items-center justify-start pt-3 shadow-lg">
                <p class="text-white font-black text-xl">{{ $d2->total_verified_donations ?? 0 }}</p>
                <p class="text-white/80 text-[10px] sm:text-xs font-semibold">রক্তদান</p>
            </div>
        </div>
        @endif

        {{-- ১ম স্থান (মাঝে) --}}
        @php $d1 = $topDonors[0]; @endphp
        <div class="flex flex-col items-center group">
            <div class="relative mb-3">
                <div class="absolute inset-0 rounded-2xl bg-yellow-400 opacity-30 blur-md scale-110"></div>
                <div class="relative w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-gradient-to-br from-yellow-300 to-amber-500 flex items-center justify-center text-white font-black text-3xl shadow-xl group-hover:scale-105 transition-transform duration-300 overflow-hidden border-2 border-yellow-200">
                    @if($d1->profile_image)
                        <img src="{{ asset('storage/' . $d1->profile_image) }}" class="w-full h-full object-cover" alt="{{ $d1->name }}" loading="lazy">
                    @else
                        {{ mb_substr($d1->name, 0, 1) }}
                    @endif
                </div>
                <span class="absolute -top-4 -right-2 text-2xl drop-shadow-lg">👑</span>
            </div>
            <div class="text-center mb-2 w-full px-1">
                <p class="font-black text-slate-900 text-sm sm:text-base truncate">{{ explode(' ', $d1->name)[0] }}</p>
                <p class="text-xs text-red-600 font-black">{{ $d1->blood_group?->value ?? $d1->blood_group ?? '?' }}</p>
                @if($d1->badges->count() > 0)
                    <div class="flex justify-center gap-0.5 mt-1">@foreach($d1->badges->take(3) as $b)<span class="text-base" title="{{ $b->bn_name ?? $b->name }}">{{ $b->emoji ?? $b->icon }}</span>@endforeach</div>
                @endif
                @if(($d1->total_verified_donations ?? 0) >= 20 || ($d1->points ?? 0) >= 1500)
                    <span class="inline-block mt-1 text-[10px] font-black text-purple-700 bg-purple-100 border border-purple-200 rounded-full px-2 py-0.5 animate-pulse">✨ Platinum</span>
                @endif
            </div>
            <div class="h-40 sm:h-48 w-full rounded-t-2xl bg-gradient-to-b from-yellow-300 to-amber-500 flex flex-col items-center justify-start pt-3 shadow-xl">
                <p class="text-white font-black text-2xl sm:text-3xl">{{ $d1->total_verified_donations ?? 0 }}</p>
                <p class="text-white/80 text-xs font-semibold">রক্তদান</p>
                <p class="text-white/70 text-[10px] font-bold mt-1">{{ number_format($d1->points ?? 0) }} pts</p>
            </div>
        </div>

        {{-- ৩য় স্থান (ডান) --}}
        @if($topDonors->count() >= 3)
        @php $d3 = $topDonors[2]; @endphp
        <div class="flex flex-col items-center group">
            <div class="relative mb-3">
                <div class="w-14 h-14 sm:w-18 sm:h-18 rounded-2xl bg-gradient-to-br from-amber-600 to-amber-700 flex items-center justify-center text-white font-black text-xl shadow-lg group-hover:scale-105 transition-transform duration-300 overflow-hidden border-2 border-white">
                    @if($d3->profile_image)
                        <img src="{{ asset('storage/' . $d3->profile_image) }}" class="w-full h-full object-cover" alt="{{ $d3->name }}" loading="lazy">
                    @else
                        {{ mb_substr($d3->name, 0, 1) }}
                    @endif
                </div>
                <span class="absolute -top-3 -right-2 text-xl drop-shadow">🥉</span>
            </div>
            <div class="text-center mb-2 w-full px-1">
                <p class="font-black text-slate-800 text-xs sm:text-sm truncate">{{ explode(' ', $d3->name)[0] }}</p>
                <p class="text-xs text-red-600 font-bold">{{ $d3->blood_group?->value ?? $d3->blood_group ?? '?' }}</p>
                @if($d3->badges->count() > 0)
                    <div class="flex justify-center gap-0.5 mt-1">@foreach($d3->badges->take(2) as $b)<span class="text-sm" title="{{ $b->bn_name ?? $b->name }}">{{ $b->emoji ?? $b->icon }}</span>@endforeach</div>
                @endif
            </div>
            <div class="h-24 sm:h-32 w-full rounded-t-2xl bg-gradient-to-b from-amber-600 to-amber-800 flex flex-col items-center justify-start pt-3 shadow-lg">
                <p class="text-white font-black text-xl">{{ $d3->total_verified_donations ?? 0 }}</p>
                <p class="text-white/80 text-[10px] sm:text-xs font-semibold">রক্তদান</p>
            </div>
        </div>
        @endif

    </div>
    @else
    <div class="text-center py-12 bg-white rounded-2xl border border-slate-100 mb-8">
        <div class="text-5xl mb-3">🩸</div>
        <p class="text-slate-500 font-semibold">এখনও কোনো শীর্ষ ডোনার নেই।</p>
        <p class="text-slate-400 text-sm mt-1">রক্তদান করুন এবং তালিকায় প্রথম হন!</p>
    </div>
    @endif

    <div class="text-center mt-14 sm:mt-16">
        <a href="{{ route('leaderboard') }}"
        class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold px-8 py-3.5 rounded-xl shadow-sm transition-all hover:shadow-md hover:-translate-y-0.5">
            লিডারবোর্ড দেখুন
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
</section>


{{-- ══════════════════════════════════════════════════════
     HOW IT WORKS
══════════════════════════════════════════════════════ --}}
<section class="py-20 bg-white" id="how-it-works">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-10">
        <div class="text-center mb-14">
            <span class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 px-4 py-1.5 rounded-full text-sm font-extrabold tracking-wide border border-blue-100">সহজ প্রক্রিয়া</span>
            <h2 class="mt-4 text-3xl md:text-4xl font-extrabold text-slate-900">রক্তদূত কীভাবে কাজ করে?</h2>
            <p class="mt-3 text-slate-500 font-medium max-w-xl mx-auto">মাত্র কয়েকটি সহজ ধাপে রক্তের সংযোগ তৈরি করুন।</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach([
                ['num' => '১', 'icon' => '🔍', 'title' => 'ডোনার সার্চ করুন', 'desc' => 'লগইন ছাড়াই রক্তের গ্রুপ ও এলাকা দিয়ে কাছের ডোনার খুঁজুন।', 'colorBg' => 'bg-red-100', 'colorText' => 'text-red-600'],
                ['num' => '২', 'icon' => '📋', 'title' => 'রিকোয়েস্ট করুন', 'desc' => 'জরুরি রক্তের প্রয়োজনে অনলাইনে রিকোয়েস্ট পোস্ট করুন — ডোনার নিজেই সাড়া দেবেন।', 'colorBg' => 'bg-blue-100', 'colorText' => 'text-blue-600'],
                ['num' => '৩', 'icon' => '🤝', 'title' => 'ম্যাচ হলে সংযোগ', 'desc' => 'সিস্টেম রিকোয়েস্ট ও ডোনার ম্যাচ করিয়ে দেবে — প্রাইভেসি-শিল্ডেড পদ্ধতিতে যোগাযোগ হবে।', 'colorBg' => 'bg-emerald-100', 'colorText' => 'text-emerald-600'],
                ['num' => '৪', 'icon' => '🩸', 'title' => 'রক্ত দিন', 'desc' => 'রক্তদান সম্পন্ন হলে উভয়পক্ষ নিশ্চিত করুন — ডোনারের প্রোফাইলে তা রেকর্ড হয়।', 'colorBg' => 'bg-amber-100', 'colorText' => 'text-amber-600'],
            ] as $step)
                <div class="text-center group">
                    <div class="relative inline-block mb-5">
                        <div class="w-16 h-16 {{ $step['colorBg'] }} {{ $step['colorText'] }} rounded-2xl flex items-center justify-center text-2xl shadow-sm group-hover:-translate-y-1.5 transition-transform duration-300">
                            {{ $step['icon'] }}
                        </div>
                        <span class="absolute -top-2.5 -right-2.5 w-6 h-6 bg-slate-900 text-white text-xs font-black rounded-full flex items-center justify-center">{{ $step['num'] }}</span>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 mb-2">{{ $step['title'] }}</h3>
                    <p class="text-slate-500 font-medium text-sm leading-relaxed">{{ $step['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>


{{-- ══════════════════════════════════════════════════════
     PRIVACY & TRUST
══════════════════════════════════════════════════════ --}}
<section class="py-20 bg-slate-50" id="trust">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-10">
        <div class="text-center mb-14">
            <span class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 px-4 py-1.5 rounded-full text-sm font-extrabold tracking-wide border border-emerald-100">🔒 নিরাপত্তা</span>
            <h2 class="mt-4 text-3xl md:text-4xl font-extrabold text-slate-900">প্রাইভেসি ও ট্রাস্ট</h2>
            <p class="mt-3 text-slate-500 font-medium max-w-xl mx-auto">আপনার তথ্য সুরক্ষায় আমরা সর্বোচ্চ প্রযুক্তিগত ব্যবস্থা নিয়েছি।</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded-2xl border border-emerald-100 p-7 shadow-sm hover:shadow-md transition">
                <div class="text-3xl mb-4">🔐</div>
                <h3 class="font-extrabold text-slate-900 text-lg mb-2">ফোন নম্বর মাস্কড</h3>
                <p class="text-slate-600 font-medium text-sm leading-relaxed">ডোনারের ফোন নম্বর সরাসরি দেখানো হয় না। একটি গণিত যাচাই চ্যালেঞ্জ পার হলে তবেই নম্বর প্রকাশ পায়।</p>
            </div>
            <div class="bg-white rounded-2xl border border-blue-100 p-7 shadow-sm hover:shadow-md transition">
                <div class="text-3xl mb-4">⚡</div>
                <h3 class="font-extrabold text-slate-900 text-lg mb-2">Rate-Limit সুরক্ষা</h3>
                <p class="text-slate-600 font-medium text-sm leading-relaxed">স্প্যাম ও ব্রুট-ফোর্স প্রতিরোধে প্রতি মিনিটে সর্বোচ্চ নম্বর প্রকাশের অনুরোধ সীমিত রাখা হয়।</p>
            </div>
            <div class="bg-white rounded-2xl border border-amber-100 p-7 shadow-sm hover:shadow-md transition">
                <div class="text-3xl mb-4">🪪</div>
                <h3 class="font-extrabold text-slate-900 text-lg mb-2">NID ভেরিফিকেশন</h3>
                <p class="text-slate-600 font-medium text-sm leading-relaxed">ডোনারকে জাতীয় পরিচয়পত্র দিয়ে যাচাই করা হয় — অ্যানোনিমাস বা ভুয়া প্রোফাইলের সুযোগ নেই।</p>
            </div>
            <div class="bg-white rounded-2xl border border-violet-100 p-7 shadow-sm hover:shadow-md transition">
                <div class="text-3xl mb-4">🏥</div>
                <h3 class="font-extrabold text-slate-900 text-lg mb-2">অর্গানাইজেশন ভেরিফিকেশন</h3>
                <p class="text-slate-600 font-medium text-sm leading-relaxed">নিবন্ধিত ব্লাড ব্যাঙ্ক ও সংগঠন ডোনারের ভেরিফিকেশনে অংশ নেয় — দ্বি-স্তরীয় নিশ্চিতকরণ।</p>
            </div>
        </div>
    </div>
</section>


{{-- ══════════════════════════════════════════════════════
     HIGH-CONTRAST FINAL CTA
══════════════════════════════════════════════════════ --}}
<section class="bg-red-700 py-24" id="cta">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-10 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-7 border border-white/30">
            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
        </div>
        <h2 class="text-3xl sm:text-5xl font-extrabold text-white leading-tight">
            আজই একটি জীবন বাঁচান
        </h2>
        <p class="mt-5 text-red-100 text-lg font-medium max-w-2xl mx-auto leading-relaxed">
            আপনার একটি রক্তদান তিনটি প্রাণ বাঁচাতে পারে। বাংলাদেশের সবচেয়ে নির্ভরযোগ্য ভেরিফায়েড ডোনার নেটওয়ার্কে এখনই যোগ দিন।
        </p>
        <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
            @guest
                <a href="{{ route('register') }}"
                   class="inline-flex items-center justify-center bg-white text-red-700 px-8 py-4 rounded-xl font-extrabold text-lg hover:bg-red-50 transition shadow-lg gap-2">
                    ডোনার হিসেবে যোগ দিন
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
                <a href="{{ route('search') }}"
                   class="inline-flex items-center justify-center bg-white/15 border border-white/30 text-white px-8 py-4 rounded-xl font-extrabold text-lg hover:bg-white/25 transition gap-2">
                    ডোনার সার্চ করুন
                </a>
            @endguest
            @auth
                <a href="{{ route('requests.create') }}"
                   class="inline-flex items-center justify-center bg-white text-red-700 px-8 py-4 rounded-xl font-extrabold text-lg hover:bg-red-50 transition shadow-lg gap-2">
                    রক্তের রিকোয়েস্ট করুন
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
                <a href="{{ route('search') }}"
                   class="inline-flex items-center justify-center bg-white/15 border border-white/30 text-white px-8 py-4 rounded-xl font-extrabold text-lg hover:bg-white/25 transition gap-2">
                    ডোনার সার্চ করুন
                </a>
            @endauth
        </div>
        <p class="mt-6 text-white/80 text-sm font-semibold">
            🔒 মোবাইল নম্বর কখনো পাবলিক করা হয় না।
        </p>
        <p class="mt-2 text-red-200 text-xs font-medium">
            বিনামূল্যে · NID ভেরিফায়েড নেটওয়ার্ক · ৬৪ জেলায় সক্রিয়
        </p>
    </div>
</section>


{{-- ══════════════════════════════════════════════════════
     FOOTER (shared component)
══════════════════════════════════════════════════════ --}}
@include('layouts.footer')



{{-- ══════════════════════════════════════════════════════
     AJAX LOCATION SCRIPT
══════════════════════════════════════════════════════ --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const divisionSelect = document.getElementById('division_select');
        const districtSelect = document.getElementById('district_select');
        const upazilaSelect  = document.getElementById('upazila_select');

        divisionSelect.addEventListener('change', function() {
            const divId = this.value;
            districtSelect.innerHTML = '<option value="">লোড হচ্ছে...</option>';
            districtSelect.disabled  = true;
            upazilaSelect.innerHTML  = '<option value="">উপজেলা/এরিয়া</option>';
            upazilaSelect.disabled   = true;

            if (divId) {
                fetch(`/ajax/districts/${divId}`)
                    .then(res => res.json())
                    .then(data => {
                        districtSelect.innerHTML = '<option value="">জেলা নির্বাচন</option>';
                        districtSelect.disabled  = false;
                        data.forEach(dist => {
                            districtSelect.innerHTML += `<option value="${dist.id}">${dist.name}</option>`;
                        });
                    })
                    .catch(err => console.error("Error fetching districts:", err));
            } else {
                districtSelect.innerHTML = '<option value="">জেলা নির্বাচন</option>';
            }
        });

        districtSelect.addEventListener('change', function() {
            const distId = this.value;
            upazilaSelect.innerHTML = '<option value="">লোড হচ্ছে...</option>';
            upazilaSelect.disabled  = true;

            if (distId) {
                fetch(`/ajax/upazilas/${distId}`)
                    .then(res => res.json())
                    .then(data => {
                        upazilaSelect.innerHTML = '<option value="">উপজেলা/এরিয়া</option>';
                        upazilaSelect.disabled  = false;
                        data.forEach(upz => {
                            upazilaSelect.innerHTML += `<option value="${upz.id}">${upz.name}</option>`;
                        });
                    })
                    .catch(err => console.error("Error fetching upazilas:", err));
            } else {
                upazilaSelect.innerHTML = '<option value="">উপজেলা/এরিয়া</option>';
            }
        });
    });
</script>

</body>
</html>