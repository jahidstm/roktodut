<!doctype html>
<html lang="bn">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'রক্তদূত')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css','resources/js/app.js'])

    <style>
        :root { font-family: 'Hind Siliguri', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial; }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased">
<header class="bg-white border-b border-slate-100 relative z-50">
    <div class="mx-auto max-w-6xl px-4 py-4 sm:py-5 flex items-center justify-between">
        
        {{-- 🩸 Logo & Brand --}}
        <a href="{{ route('home') }}" class="flex items-center gap-3 group">
            <div class="h-9 w-9 sm:h-10 sm:w-10 rounded-xl bg-red-50 border border-red-100 flex items-center justify-center group-hover:bg-red-100 transition-colors">
                <span class="text-red-600 font-extrabold tracking-tight">RD</span>
            </div>
            <div class="leading-tight hidden sm:block">
                <div class="font-extrabold tracking-tight text-slate-900 group-hover:text-red-600 transition-colors">রক্তদূত</div>
                <div class="text-[10px] sm:text-xs text-slate-500 font-semibold uppercase tracking-wider">Blood Donation Platform</div>
            </div>
        </a>

        {{-- 🧭 Navigation & Actions --}}
        <nav class="flex items-center gap-3 sm:gap-5">
            {{-- ১. রিকোয়েস্ট ফিড --}}
            <a href="{{ route('requests.index') }}" class="text-sm font-bold text-slate-600 hover:text-red-600 transition-colors hidden sm:block">রিকোয়েস্ট ফিড</a>
            
            {{-- ২. রিকোয়েস্ট করুন বাটন (সঠিক অর্ডারে আনা হলো) --}}
            <a href="{{ route('requests.create') }}" class="text-sm font-extrabold bg-slate-900 hover:bg-slate-800 text-white px-3 py-2 sm:px-4 sm:py-2 rounded-lg shadow-sm transition-colors hidden sm:block">
                রিকোয়েস্ট করুন
            </a>
            
            @auth
                {{-- ৩. Notification Bell --}}
                <div class="relative" x-data="{ openNotification: false }" @click.outside="openNotification = false" @close.stop="openNotification = false">
                    <button @click="openNotification = ! openNotification" class="relative p-2 text-slate-500 hover:text-red-600 transition rounded-full hover:bg-red-50 focus:outline-none">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                         class="absolute right-0 mt-3 w-80 sm:w-96 rounded-2xl bg-white shadow-xl ring-1 ring-slate-200 overflow-hidden"
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

                {{-- ৪. User Profile Chip & Dropdown --}}
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
            @else
                {{-- Guest Buttons --}}
                <a href="{{ route('login') }}" class="text-sm font-bold text-slate-700 hover:text-red-600 transition-colors">লগইন</a>
                <a href="{{ route('register') }}" class="bg-red-600 text-white text-sm font-bold px-4 py-2 sm:px-5 sm:py-2.5 rounded-full hover:bg-red-700 shadow-sm transition-colors">অ্যাকাউন্ট খুলুন</a>
            @endauth
        </nav>
    </div>
</header>

<main class="min-h-screen">
    @if (session('success'))
        <div class="max-w-6xl mx-auto px-4 mt-6">
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 font-semibold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="max-w-6xl mx-auto px-4 mt-6">
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 font-semibold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    @yield('content')
</main>

<footer class="border-t border-slate-200 bg-white mt-auto">
    <div class="mx-auto max-w-6xl px-4 py-8 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-2">
            <span class="text-red-600 font-extrabold tracking-tight">RD</span>
            <span class="text-sm text-slate-500 font-semibold">© {{ date('Y') }} রক্তদূত প্ল্যাটফর্ম</span>
        </div>
        <div class="flex items-center gap-6 text-sm font-semibold text-slate-500">
            <a href="#" class="hover:text-red-600 transition-colors">শর্তাবলী</a>
            <a href="#" class="hover:text-red-600 transition-colors">প্রাইভেসি পলিসি</a>
            <a href="#" class="hover:text-red-600 transition-colors">যোগাযোগ</a>
        </div>
    </div>
</footer>
</body>
</html>