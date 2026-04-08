<!doctype html>
<html lang="bn">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>রক্তদূত — জরুরি রক্ত সহায়তা</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css','resources/js/app.js'])

    <style>
        :root { font-family: 'Hind Siliguri', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial; }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased overflow-x-hidden">
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur border-b border-slate-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-10 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-red-50 border border-red-100 flex items-center justify-center">
                    <span class="text-red-600 font-extrabold tracking-tight">RD</span>
                </div>
                <div class="leading-tight">
                    <div class="text-lg font-extrabold tracking-tight">রক্তদূত</div>
                    <div class="text-xs text-slate-500 font-semibold">ইমার্জেন্সি ব্লাড নেটওয়ার্ক</div>
                </div>
            </a>

            <nav class="hidden md:flex items-center gap-7 font-semibold text-slate-600">
                <a href="{{ route('home') }}" class="text-red-600">হোম</a>
                <a href="#donate" class="hover:text-red-600 transition">রক্ত দিন</a>
                <a href="#urgent" class="hover:text-red-600 transition">জরুরি অনুরোধ</a>
                <a href="{{ route('requests.index') }}" class="hover:text-red-600 transition">ডোনার ফিড</a>
            </nav>

            {{-- 🎯 Dynamic Auth Buttons & Profile Chip --}}
            <div class="flex items-center gap-3">
                @guest
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center justify-center px-4 py-2 rounded-lg font-semibold text-slate-700 hover:text-red-600 transition">লগইন</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center bg-red-600 text-white px-5 py-2.5 rounded-lg font-extrabold hover:bg-red-700 transition shadow-sm shadow-red-200">রেজিস্টার</a>
                @endguest

                @auth
                    {{-- 🔔 Notification Bell (Added specifically for Home Page) --}}
                    <div class="relative flex items-center" x-data="{ openNotification: false }" @click.outside="openNotification = false" @close.stop="openNotification = false">
                        <button @click="openNotification = ! openNotification" class="relative p-2 text-slate-500 hover:text-red-600 transition rounded-full hover:bg-red-50 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[10px] font-extrabold text-white shadow-sm ring-2 ring-white">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </button>

                        {{-- Notification Dropdown --}}
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
                                    <div class="p-8 text-center flex flex-col items-center justify-center">
                                        <div class="bg-slate-50 p-3 rounded-full mb-3 text-slate-300">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        </div>
                                        <span class="text-sm font-bold text-slate-400">নতুন কোনো নোটিফিকেশন নেই।</span>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- 🎯 User Profile Chip & Dropdown --}}
                    <div x-data="{ openProfile: false }" class="relative inline-block text-left ml-1 sm:ml-2">
                        
                        {{-- 🔘 Trigger Button (Profile Chip) --}}
                        <button @click="openProfile = !openProfile" @click.away="openProfile = false" type="button" class="flex items-center gap-2 p-1.5 sm:pr-4 bg-white border border-slate-200 rounded-full hover:bg-slate-50 hover:border-red-200 transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            
                            {{-- Avatar Thumbnail --}}
                            <div class="w-7 h-7 sm:w-8 sm:h-8 shrink-0 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden">
                                @if(auth()->user()->profile_image)
                                    <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="Profile" class="w-full h-full object-cover">
                                @else
                                    <span class="text-slate-600 font-black text-xs sm:text-sm">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                                @endif
                            </div>
                            
                            {{-- Name & Chevron --}}
                            <span class="text-sm font-bold text-slate-800 hidden sm:block">{{ explode(' ', auth()->user()->name)[0] }}</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 hidden sm:block" :class="openProfile ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        {{-- 🔽 Dropdown Menu --}}
                        <div x-show="openProfile" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                             style="display: none;"
                             class="absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden z-50">
                            
                            {{-- Section 1: User Identity --}}
                            <div class="px-5 py-4 bg-slate-50/50 border-b border-slate-100">
                                <p class="text-base font-black text-slate-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs font-semibold text-slate-500 mt-0.5 flex items-center gap-1.5">
                                    @if(auth()->user()->role?->value === 'org_admin' || auth()->user()->role === 'org_admin')
                                        <span class="w-2 h-2 rounded-full bg-blue-500"></span> অর্গানাইজেশন অ্যাডমিন
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-red-500"></span> ডোনার • <span class="text-red-600 font-bold">{{ auth()->user()->blood_group?->value ?? auth()->user()->blood_group ?? 'N/A' }}</span>
                                    @endif
                                </p>
                            </div>

                            {{-- Section 2: Core Actions --}}
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

                            {{-- Section 3: System Actions (Logout) --}}
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
            </div>
        </div>
    </header>

    <section class="relative bg-white overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-red-50 via-white to-white"></div>
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-10 pt-16 pb-28 lg:pt-24 lg:pb-36">
            <div class="flex flex-col lg:flex-row items-center gap-14">
                <div class="lg:w-1/2 text-center lg:text-left">
                    <span class="inline-flex items-center gap-2 bg-red-50 text-red-700 px-4 py-1.5 rounded-full text-sm font-extrabold tracking-wide border border-red-100">ইমার্জেন্সি ব্লাড ডোনেশন নেটওয়ার্ক</span>
                    <h1 class="mt-6 text-4xl md:text-5xl lg:text-6xl font-extrabold text-slate-900 leading-[1.12] tracking-tight">
                        জরুরি মুহূর্তে রক্তের সন্ধানে—<span class="text-red-600">আমরা আছি আপনার পাশে</span>
                    </h1>
                    <p class="mt-6 text-lg text-slate-600 leading-relaxed max-w-2xl mx-auto lg:mx-0 font-medium">
                        রক্তদূত প্ল্যাটফর্মের মাধ্যমে আপনার এলাকার ভেরিফায়েড ডোনারদের সাথে দ্রুত সংযোগ করুন। রক্ত দিন, জীবন বাঁচান।
                    </p>
                    <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="{{ route('requests.create') }}" class="inline-flex items-center justify-center bg-red-600 text-white px-7 py-3.5 rounded-lg font-extrabold shadow-sm shadow-red-200 hover:bg-red-700 transition">রক্তের রিকোয়েস্ট করুন</a>
                        <a href="{{ route('search') }}" class="inline-flex items-center justify-center border-2 border-red-600 text-red-600 px-7 py-3.5 rounded-lg font-extrabold hover:bg-red-50 transition">ডোনার খুঁজুন</a>
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

    {{-- 🔍 DYNAMIC AJAX SEARCH SECTION --}}
    <section class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-10 relative -mt-16 md:-mt-20">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 md:p-8">
            <div class="flex items-center justify-between gap-4 mb-4">
                <h3 class="text-lg font-extrabold text-slate-800">দ্রুত অনুসন্ধান করুন</h3>
            </div>

            <form action="{{ route('search') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                
                {{-- 🎯 অপটিমাইজড বিভাগ ড্রপডাউন --}}
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
                    <option value="A+">A+</option><option value="A-">A-</option><option value="B+">B+</option><option value="B-">B-</option>
                    <option value="AB+">AB+</option><option value="AB-">AB-</option><option value="O+">O+</option><option value="O-">O-</option>
                </select>

                <button type="submit" class="bg-red-600 text-white font-extrabold rounded-lg py-3.5 hover:bg-red-700 transition shadow-sm shadow-red-200">
                    খুঁজুন
                </button>
            </form>
        </div>
    </section>

    {{-- Stats Section --}}
    <section id="donate" class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-10 mt-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
            <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm">
                <div class="text-4xl font-extrabold text-red-600">৫০০+</div>
                <div class="text-slate-500 mt-2 font-semibold">ভেরিফাইড ডোনার</div>
            </div>
            <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm">
                <div class="text-4xl font-extrabold text-red-600">১২০+</div>
                <div class="text-slate-500 mt-2 font-semibold">সফল কানেকশন</div>
            </div>
            <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm">
                <div class="text-4xl font-extrabold text-red-600">৬৪</div>
                <div class="text-slate-500 mt-2 font-semibold">জেলায় সেবা চালু</div>
            </div>
        </div>
    </section>

    {{-- Urgent Requests Section --}}
    <section id="urgent" class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-10 mt-20">
        <div class="text-center mb-12">
            <span class="text-red-500 font-extrabold text-sm tracking-widest uppercase">জরুরি</span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mt-2">
                জরুরি <span class="text-red-600">রক্তের প্রয়োজন</span>
            </h2>
            <p class="text-slate-500 mt-3 font-medium">এই রোগীদের এখনই আপনার সাহায্য প্রয়োজন</p>
        </div>

        @php
            $urgentCards = [
                ['name'=>'রহিম উদ্দিন','place'=>'ঢাকা মেডিকেল কলেজ হাসপাতাল','area'=>'সাভার, ঢাকা','bg'=>'O+','time'=>'২ ঘণ্টা আগে'],
                ['name'=>'ফাতেমা বেগম','place'=>'ইবনে সিনা হাসপাতাল','area'=>'গুলশান, ঢাকা','bg'=>'B-','time'=>'৪ ঘণ্টা আগে'],
                ['name'=>'করিম মিয়া','place'=>'চট্টগ্রাম মেডিকেল কলেজ','area'=>'হালিশহর, চট্টগ্রাম','bg'=>'AB+','time'=>'৫ ঘণ্টা আগে'],
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach ($urgentCards as $c)
                <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm hover:shadow-lg transition flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-4 gap-3">
                            <div>
                                <h3 class="font-extrabold text-xl text-slate-900">{{ $c['name'] }}</h3>
                                <p class="text-slate-500 text-sm mt-1 font-medium">{{ $c['place'] }}</p>
                            </div>
                            <span class="bg-red-50 text-red-600 font-extrabold px-3 py-1.5 rounded-lg border border-red-100 shadow-sm">
                                {{ $c['bg'] }}
                            </span>
                        </div>
                        <div class="bg-slate-50 rounded-lg p-4 space-y-3 mt-4 border border-slate-100">
                            <div class="flex items-center gap-3 text-slate-600 text-sm font-semibold">
                                <span class="inline-block w-2 h-2 rounded-full bg-red-500"></span>{{ $c['area'] }}
                            </div>
                            <div class="flex items-center gap-3 text-slate-500 text-sm font-semibold">
                                <span class="inline-block w-2 h-2 rounded-full bg-slate-300"></span>{{ $c['time'] }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Donor Feed Section --}}
    <section class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-10 py-20">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mt-2">
                আমাদের <span class="text-red-600">সম্মানিত রক্তদাতা</span>
            </h2>
            <p class="text-slate-500 mt-3 font-medium">যারা রক্তদানে প্রস্তুত আছেন</p>
        </div>

        @php
            $donorCards = [
                ['name'=>'তানভীর আহমেদ','district'=>'ঢাকা','upazila'=>'মিরপুর','bg'=>'A+'],
                ['name'=>'সাদিয়া ইসলাম','district'=>'চট্টগ্রাম','upazila'=>'খুলশী','bg'=>'O-'],
                ['name'=>'রাকিবুল হাসান','district'=>'সিলেট','upazila'=>'জিন্দাবাজার','bg'=>'B+'],
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($donorCards as $donor)
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-lg transition relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-red-50 rounded-bl-full z-0 transition group-hover:bg-red-100"></div>
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-extrabold text-slate-800">{{ $donor['name'] }}</h3>
                                <p class="text-slate-500 text-sm mt-1 font-semibold">
                                    <svg class="w-4 h-4 inline mr-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                    {{ $donor['upazila'] }}, {{ $donor['district'] }}
                                </p>
                            </div>
                            <div class="bg-red-600 text-white font-black text-lg px-3 py-1.5 rounded-lg shadow-sm">
                                {{ $donor['bg'] }}
                            </div>
                        </div>
                        <div class="mb-5">
                            <span class="inline-flex items-center bg-green-50 text-green-700 text-xs px-2.5 py-1 rounded-full font-bold border border-green-200">
                                <span class="w-2 h-2 mr-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                রক্তদানে প্রস্তুত
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-12 text-center">
            <a href="{{ route('search') }}" class="inline-flex items-center justify-center border border-slate-300 bg-white text-slate-700 font-extrabold px-7 py-3 rounded-lg hover:bg-slate-50 transition shadow-sm">
                আরও ডোনার খুঁজুন
            </a>
        </div>
    </section>

    <footer class="bg-white border-t border-slate-100 py-8 text-center text-slate-500 font-medium">
        <p>© {{ date('Y') }} রক্তদূত. সর্বস্বত্ব সংরক্ষিত.</p>
    </footer>

    {{-- ⚙️ THE AJAX LOGIC SCRIPT (Optimized) --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const divisionSelect = document.getElementById('division_select');
            const districtSelect = document.getElementById('district_select');
            const upazilaSelect = document.getElementById('upazila_select');

            // On Division Change -> Fetch Districts
            divisionSelect.addEventListener('change', function() {
                const divId = this.value;
                districtSelect.innerHTML = '<option value="">লোড হচ্ছে...</option>';
                districtSelect.disabled = true;
                upazilaSelect.innerHTML = '<option value="">উপজেলা/এরিয়া</option>';
                upazilaSelect.disabled = true;

                if (divId) {
                    fetch(`/ajax/districts/${divId}`)
                        .then(res => res.json())
                        .then(data => {
                            districtSelect.innerHTML = '<option value="">জেলা নির্বাচন</option>';
                            districtSelect.disabled = false;
                            data.forEach(dist => {
                                districtSelect.innerHTML += `<option value="${dist.id}">${dist.name}</option>`;
                            });
                        })
                        .catch(err => console.error("Error fetching districts:", err));
                } else {
                    districtSelect.innerHTML = '<option value="">জেলা নির্বাচন</option>';
                }
            });

            // On District Change -> Fetch Upazilas
            districtSelect.addEventListener('change', function() {
                const distId = this.value;
                upazilaSelect.innerHTML = '<option value="">লোড হচ্ছে...</option>';
                upazilaSelect.disabled = true;

                if (distId) {
                    fetch(`/ajax/upazilas/${distId}`)
                        .then(res => res.json())
                        .then(data => {
                            upazilaSelect.innerHTML = '<option value="">উপজেলা/এরিয়া</option>';
                            upazilaSelect.disabled = false;
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