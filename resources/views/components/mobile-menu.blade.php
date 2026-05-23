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
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-xl border border-slate-100 flex items-center justify-center overflow-hidden bg-white shadow-sm">
                        <img src="{{ asset('images/image_14.png') }}" class="w-full h-full object-contain p-1" alt="Logo">
                    </div>
                    <span class="font-extrabold text-slate-900 text-lg tracking-tight">রক্তদূত</span>
                </a>
                <button @click="mobileMenuOpen = false" class="text-slate-400 hover:text-red-600 focus:outline-none p-1 bg-slate-50 rounded-full transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-4 py-6 flex flex-col gap-2">
                <a href="{{ route('home') }}" class="flex items-center px-4 py-3.5 min-h-[44px] rounded-xl font-bold transition-colors {{ request()->routeIs('home') ? 'bg-red-50 text-red-600' : 'text-slate-700 hover:bg-slate-50' }}">
                    হোম
                </a>
                <a href="{{ $requestsRoute }}" class="flex items-center px-4 py-3.5 min-h-[44px] rounded-xl font-bold transition-colors {{ request()->routeIs('requests') || request()->routeIs('requests.*') ? 'bg-red-50 text-red-600' : 'text-slate-700 hover:bg-slate-50' }}">
                    রক্তের অনুরোধ
                </a>
                <a href="{{ route('search') }}" class="flex items-center px-4 py-3.5 min-h-[44px] rounded-xl font-bold transition-colors {{ request()->routeIs('search') || request()->routeIs('search.*') ? 'bg-red-50 text-red-600' : 'text-slate-700 hover:bg-slate-50' }}">
                    রক্তদাতা খুঁজুন
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
