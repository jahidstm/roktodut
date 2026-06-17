@php
    $requestsRoute = \Illuminate\Support\Facades\Route::has('requests') ? route('requests') : route('requests.index');
@endphp

{{-- 📱 Mobile Off-Canvas Sidebar --}}
<template x-teleport="body">
    <div x-show="mobileMenuOpen" 
         class="fixed inset-0 z-[9999] lg:hidden" 
         style="display: none;">
         
        <!-- Backdrop -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition-opacity ease-linear duration-200" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition-opacity ease-linear duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" 
             @click.stop="mobileMenuOpen = false"></div>

        <!-- Sidebar -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200 transform" 
             x-transition:enter-start="-translate-x-full" 
             x-transition:enter-end="translate-x-0" 
             x-transition:leave="transition ease-in duration-200 transform" 
             x-transition:leave-start="translate-x-0" 
             x-transition:leave-end="-translate-x-full" 
             class="fixed inset-y-0 left-0 w-72 bg-white shadow-2xl flex flex-col pointer-events-auto">
             
            <div class="flex items-center justify-between px-5 py-5 border-b border-slate-100">
                <a href="{{ route('home') }}" class="inline-flex" aria-label="রক্তদূত">
                    <x-logo size="md" variant="full" />
                </a>
                <button @click="mobileMenuOpen = false" class="text-slate-400 hover:text-red-600 focus:outline-none p-1 bg-slate-50 rounded-full transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-4 py-6 flex flex-col gap-2">
                <a href="{{ route('home') }}" class="flex items-center px-4 py-3.5 min-h-[44px] rounded-xl font-bold transition-colors {{ request()->routeIs('home') ? 'bg-red-50 text-red-600' : 'text-slate-700 hover:bg-slate-50' }}">
                    হোম
                </a>
                {{-- 1 & 2. রক্ত খুঁজুন (Accordion) --}}
                <div x-data="{ openMenu: {{ request()->routeIs('requests.*') || request()->routeIs('requests') || request()->routeIs('search.*') || request()->routeIs('search') ? 'true' : 'false' }} }" class="flex flex-col">
                    <button @click="openMenu = !openMenu" class="flex items-center justify-between px-4 py-3.5 min-h-[44px] rounded-xl font-bold transition-colors w-full text-left {{ request()->routeIs('requests.*') || request()->routeIs('requests') || request()->routeIs('search.*') || request()->routeIs('search') ? 'bg-red-50 text-red-600' : 'text-slate-700 hover:bg-slate-50' }}">
                        <span>রক্ত খুঁজুন</span>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="openMenu ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    
                    <div x-show="openMenu" x-collapse class="pl-4 pr-2 pt-1 flex flex-col gap-1">
                        <a href="{{ $requestsRoute }}" class="flex items-center gap-3 px-3 py-3 rounded-xl transition-colors {{ request()->routeIs('requests') || request()->routeIs('requests.*') ? 'bg-red-50/80 text-red-700 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                            <div class="p-1.5 rounded-lg {{ request()->routeIs('requests') || request()->routeIs('requests.*') ? 'bg-red-100 text-red-600' : 'bg-slate-100 text-slate-500' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <span class="text-sm">রক্তের অনুরোধ</span>
                        </a>
                        <a href="{{ route('search') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl transition-colors {{ request()->routeIs('search') || request()->routeIs('search.*') ? 'bg-emerald-50/80 text-emerald-700 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                            <div class="p-1.5 rounded-lg {{ request()->routeIs('search') || request()->routeIs('search.*') ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-500' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <span class="text-sm">রক্তদাতা খুঁজুন</span>
                        </a>
                    </div>
                </div>
                <a href="{{ route('blood-bank.index') }}" class="flex items-center px-4 py-3.5 min-h-[44px] rounded-xl font-bold transition-colors {{ request()->routeIs('blood-bank.*') ? 'bg-red-50 text-red-600' : 'text-slate-700 hover:bg-slate-50' }}">
                    ব্লাড ব্যাংক
                </a>
                <a href="{{ route('live-demand.index') }}" class="flex items-center px-4 py-3.5 min-h-[44px] rounded-xl font-bold transition-colors {{ request()->routeIs('live-demand.*') ? 'bg-red-50 text-red-600' : 'text-slate-700 hover:bg-slate-50' }}">
                    লাইভ ম্যাপ
                </a>
                <a href="{{ route('leaderboard') }}" class="flex items-center px-4 py-3.5 min-h-[44px] rounded-xl font-bold transition-colors {{ request()->routeIs('leaderboard') ? 'bg-red-50 text-red-600' : 'text-slate-700 hover:bg-slate-50' }}">
                    লিডারবোর্ড
                </a>
                <a href="{{ route('blog.index') }}" class="flex items-center px-4 py-3.5 min-h-[44px] rounded-xl font-bold transition-colors {{ request()->routeIs('blog.*') ? 'bg-red-50 text-red-600' : 'text-slate-700 hover:bg-slate-50' }}">
                    স্বাস্থ্যবার্তা
                </a>
            </div>
            
            @guest
            <div class="p-5 border-t border-slate-100 flex flex-col gap-3">
                <a href="{{ route('login') }}" class="w-full py-3.5 px-4 min-h-[44px] text-center rounded-xl font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 transition-colors">লগইন করুন</a>
                <a href="{{ route('register') }}" class="w-full py-3.5 px-4 min-h-[44px] text-center rounded-xl font-bold text-white bg-red-600 hover:bg-red-700 shadow-sm transition-colors">নতুন অ্যাকাউন্ট খুলুন</a>
            </div>
            @endguest
        </div>
    </div>
</template>
