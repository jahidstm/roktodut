<header
    x-data="{ scrolled: false, mobileMenuOpen: false }"
    x-init="scrolled = window.scrollY > 8; window.addEventListener('scroll', () => scrolled = window.scrollY > 8)"
    :class="scrolled ? 'bg-white/80 backdrop-blur-md border-slate-200 shadow-sm' : 'bg-white border-slate-100'"
    class="sticky top-0 z-50 border-b transition-all duration-300">
    <div class="mx-auto max-w-6xl px-4 py-4 sm:py-5 relative flex items-center justify-between">
        @php
            $requestsRoute = \Illuminate\Support\Facades\Route::has('requests') ? route('requests') : route('requests.index');
        @endphp
        
        <div class="flex items-center gap-3 sm:gap-4">
            {{-- 📱 Mobile Hamburger Button (Extreme Left) --}}
            <button @click.stop="{{ isset($isDashboard) ? 'sidebarOpen = !sidebarOpen' : 'mobileMenuOpen = true' }}" type="button" class="lg:hidden p-1.5 -ml-2 text-slate-500 hover:text-red-600 focus:outline-none transition-colors">
                <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>

            {{-- 🩸 Logo & Brand --}}
            <a href="{{ route('home') }}"
               class="group inline-flex items-center focus:outline-none"
               aria-label="রক্তদূত — হোম">
                <x-logo size="md" variant="full"
                        class="[&_.transition-colors]:group-hover:!text-red-600" />
            </a>
        </div>

        {{-- 🧭 Navigation & Actions --}}
        <nav class="ml-auto flex items-center gap-3 sm:gap-5">
            {{-- Center Menu (Exact 5 items) --}}
            <div class="hidden lg:flex absolute left-1/2 -translate-x-1/2 items-center justify-center">
                <div x-data="{ left: 0, width: 0, opacity: 0 }"
                     @mouseleave="opacity = 0"
                     class="relative flex items-center gap-1 p-1.5 bg-slate-100/80 border border-slate-200/60 rounded-full shadow-sm backdrop-blur-md">
                     
                    {{-- Sliding Hover Indicator --}}
                    <div class="absolute top-1.5 bottom-1.5 bg-slate-200/80 rounded-full transition-all duration-300 ease-out z-0 pointer-events-none"
                         :style="`left: ${left}px; width: ${width}px; opacity: ${opacity}; transform: scale(${opacity ? 1 : 0.95})`"></div>

                    {{-- 1 & 2. রক্ত খুঁজুন (Dropdown) --}}
                    <div x-data="{ openDropdown: false }" 
                         @mouseenter="openDropdown = true; left = $el.offsetLeft; width = $el.offsetWidth; opacity = 1" 
                         @mouseleave="openDropdown = false" 
                         class="relative z-10 flex items-center">
                        <button type="button"
                           class="flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition-colors duration-300 ease-out
                                  {{ request()->routeIs('requests.*') || request()->routeIs('requests') || request()->routeIs('search.*') || request()->routeIs('search')
                                     ? 'text-red-600 bg-white shadow-sm ring-1 ring-slate-900/5' 
                                     : 'text-slate-600 hover:text-slate-900' }}">
                            রক্ত খুঁজুন
                            <svg class="w-4 h-4 transition-transform duration-200" :class="openDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        {{-- Dropdown Panel --}}
                        <div x-show="openDropdown" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                             class="absolute top-full left-1/2 -translate-x-1/2 pt-2 w-64 z-50"
                             style="display: none;">
                             <div class="bg-white rounded-2xl shadow-xl ring-1 ring-slate-900/5 p-2 overflow-hidden">
                                 
                                 {{-- Link 1: রক্তের অনুরোধ --}}
                                 <a href="{{ $requestsRoute }}" class="flex items-start gap-3 p-3 rounded-xl hover:bg-red-50/80 transition-colors group">
                                     <div class="bg-red-100 text-red-600 p-2 rounded-lg group-hover:bg-red-200 transition-colors shrink-0">
                                         <svg class="w-5 h-5 transform transition-transform duration-300 group-hover:scale-110 group-hover:-rotate-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                     </div>
                                     <div>
                                         <p class="text-sm font-black text-slate-800 group-hover:text-red-700 transition-colors">রক্তের অনুরোধ</p>
                                         <p class="text-[10px] text-slate-500 font-medium mt-0.5 leading-snug">চলমান জরুরি রক্তের রিকোয়েস্টগুলো দেখুন</p>
                                     </div>
                                 </a>

                                 {{-- Link 2: রক্তদাতা খুঁজুন --}}
                                 <a href="{{ route('search') }}" class="flex items-start gap-3 p-3 rounded-xl hover:bg-emerald-50/80 transition-colors mt-1 group">
                                     <div class="bg-emerald-100 text-emerald-600 p-2 rounded-lg group-hover:bg-emerald-200 transition-colors shrink-0">
                                         <svg class="w-5 h-5 transform transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                     </div>
                                     <div>
                                         <p class="text-sm font-black text-slate-800 group-hover:text-emerald-700 transition-colors">রক্তদাতা খুঁজুন</p>
                                         <p class="text-[10px] text-slate-500 font-medium mt-0.5 leading-snug">নির্দিষ্ট এলাকায় ফিল্টার করে ডোনার খুঁজুন</p>
                                     </div>
                                 </a>
                             </div>
                        </div>
                    </div>

                    {{-- 2.5 ব্লাড ব্যাংক --}}
                    <a href="{{ route('blood-bank.index') }}"
                       @mouseenter="left = $el.offsetLeft; width = $el.offsetWidth; opacity = 1"
                       class="relative z-10 flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition-colors duration-300 ease-out
                              {{ request()->routeIs('blood-bank.*') 
                                 ? 'text-red-600 bg-white shadow-sm ring-1 ring-slate-900/5' 
                                 : 'text-slate-600 hover:text-slate-900' }}">
                        ব্লাড ব্যাংক
                    </a>

                    {{-- 3. লাইভ ম্যাপ --}}
                    <a href="{{ route('live-demand.index') }}"
                       @mouseenter="left = $el.offsetLeft; width = $el.offsetWidth; opacity = 1"
                       class="relative z-10 flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition-colors duration-300 ease-out
                              {{ request()->routeIs('live-demand.*') || request()->routeIs('live-demand.index')
                                 ? 'text-red-600 bg-white shadow-sm ring-1 ring-slate-900/5' 
                                 : 'text-slate-600 hover:text-slate-900' }}">
                        লাইভ ম্যাপ
                    </a>

                    {{-- 4. লিডারবোর্ড --}}
                    <a href="{{ route('leaderboard') }}"
                       @mouseenter="left = $el.offsetLeft; width = $el.offsetWidth; opacity = 1"
                       class="relative z-10 flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition-colors duration-300 ease-out
                              {{ request()->routeIs('leaderboard') 
                                 ? 'text-red-600 bg-white shadow-sm ring-1 ring-slate-900/5' 
                                 : 'text-slate-600 hover:text-slate-900' }}">
                        লিডারবোর্ড
                    </a>

                    {{-- 5. স্বাস্থ্যবার্তা --}}
                    <a href="{{ route('blog.index') }}"
                       @mouseenter="left = $el.offsetLeft; width = $el.offsetWidth; opacity = 1"
                       class="relative z-10 flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition-colors duration-300 ease-out
                              {{ request()->routeIs('blog.*') 
                                 ? 'text-red-600 bg-white shadow-sm ring-1 ring-slate-900/5' 
                                 : 'text-slate-600 hover:text-slate-900' }}">
                        স্বাস্থ্যবার্তা
                    </a>
                </div>
            </div>

            @auth
                {{-- ৩. Notification Bell (Real-time via Reverb) --}}
                <div class="relative"
                     x-data="notificationBell()"
                     @click.outside="open = false">

                    {{-- Bell Button --}}
                    <button @click="toggle()"
                            id="notif-bell-btn"
                            class="relative p-2 text-slate-500 hover:text-red-600 transition rounded-full hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-300"
                            aria-label="নোটিফিকেশন">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        {{-- Unread badge (Alpine-driven) --}}
                        <span x-show="unreadCount > 0"
                              x-text="unreadCount > 99 ? '99+' : unreadCount"
                              id="notif-badge"
                              class="absolute top-0.5 right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[9px] font-black text-white shadow-sm ring-2 ring-white"
                              style="display:none;"></span>
                    </button>

                    {{-- Notification Dropdown --}}
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                         class="absolute right-4 sm:right-0 mt-3 w-[calc(100vw-2rem)] sm:w-96 rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200 overflow-hidden z-50"
                         style="display:none;"
                         role="dialog"
                         aria-label="নোটিফিকেশন তালিকা">

                        {{-- Header --}}
                        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-extrabold text-slate-800">নোটিফিকেশন</h3>
                                <span x-show="unreadCount > 0"
                                      x-text="unreadCount + ' নতুন'"
                                      class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-black"
                                      style="display:none;"></span>
                            </div>
                            <button x-show="unreadCount > 0"
                                    @click.prevent="markAllRead()"
                                    class="text-[11px] text-slate-500 hover:text-red-600 font-bold transition-colors focus:outline-none focus:underline"
                                    style="display:none;">
                                সব পড়া হয়েছে ✓
                            </button>
                        </div>

                        {{-- List Body --}}
                        <div id="notif-list" class="max-h-80 overflow-y-auto divide-y divide-slate-50">

                            {{-- Loading State --}}
                            <div x-show="loading" class="flex items-center justify-center gap-2 py-10 text-slate-400">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <span class="text-sm font-semibold">লোড হচ্ছে...</span>
                            </div>

                            {{-- Empty State --}}
                            <div x-show="!loading && notifications.length === 0"
                                 data-empty-state
                                 class="py-10 text-center flex flex-col items-center gap-3">
                                <div class="w-14 h-14 rounded-full bg-slate-50 flex items-center justify-center text-slate-300">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-slate-400">এখনো কোনো নোটিফিকেশন নেই</p>
                            </div>

                            {{-- Notification Items (Alpine x-for reactive list) --}}
                            <template x-for="n in notifications" :key="n.id">
                                <a :href="n.url"
                                   :class="n.read_at ? 'bg-white' : 'bg-red-50/40'"
                                   class="flex gap-3 items-start px-4 py-3.5 hover:bg-slate-50 transition-colors focus:outline-none focus:bg-slate-100"
                                   tabindex="0">
                                    <div class="shrink-0 mt-0.5 w-9 h-9 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-slate-800 leading-snug line-clamp-2" x-text="n.message"></p>
                                        <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                            <template x-if="n.blood_group">
                                                <span x-text="n.blood_group"
                                                      :class="bloodGroupColor(n.blood_group)"
                                                      class="text-[10px] font-black px-1.5 py-0.5 rounded-full"></span>
                                            </template>
                                            <span x-text="urgencyText(n.urgency)"
                                                  :class="urgencyClass(n.urgency)"
                                                  class="text-[10px] font-bold px-1.5 py-0.5 rounded-full"></span>
                                            <span x-text="n.time_ago" class="text-[10px] text-slate-400 font-medium"></span>
                                        </div>
                                    </div>
                                    <div x-show="!n.read_at"
                                         class="shrink-0 mt-2 w-2 h-2 rounded-full bg-red-500"
                                         style="display:none;"></div>
                                </a>
                            </template>

                        </div>{{-- /#notif-list --}}
                    </div>{{-- /dropdown --}}
                </div>{{-- /notification bell --}}

                {{-- ৪. User Profile Chip & Dropdown --}}
                <div x-data="{ openProfile: false }" class="relative inline-block text-left ml-1 sm:ml-2">
                    
                    {{-- 🔘 Trigger Button (Profile Chip) --}}
                    <button @click="openProfile = !openProfile" @click.away="openProfile = false" type="button" class="flex items-center gap-2 p-1.5 sm:pr-4 bg-white border border-slate-200 rounded-full hover:bg-slate-50 hover:border-red-200 transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        
                        {{-- Avatar Thumbnail --}}
                        <div class="w-7 h-7 sm:w-8 sm:h-8 shrink-0 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden">
                            @if(auth()->user()->profile_image)
                                <img src="{{ route('profile.avatar', auth()->user()) }}" alt="Profile" class="w-full h-full object-cover" loading="lazy" decoding="async">
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
                         class="absolute right-4 sm:right-0 mt-3 w-64 max-w-[calc(100vw-2rem)] bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden z-50">
                        
                        {{-- Section 1: User Identity --}}
                        <div class="px-5 py-4 bg-slate-50/50 border-b border-slate-100">
                            <p class="text-base font-black text-slate-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs font-semibold text-slate-500 mt-0.5 flex items-center gap-1.5">
                                @if(auth()->user()->role?->value === 'org_admin' || auth()->user()->role === 'org_admin')
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> অর্গানাইজেশন অ্যাডমিন
                                @elseif(auth()->user()->role?->value === 'admin' || auth()->user()->role === 'admin')
                                    <span class="w-2 h-2 rounded-full bg-purple-500"></span> সিস্টেম অ্যাডমিন
                                @elseif(auth()->user()->is_donor)
                                    <span class="w-2 h-2 rounded-full bg-red-500"></span> ডোনার • <span class="text-red-600 font-bold">{{ auth()->user()->blood_group?->value ?? auth()->user()->blood_group ?? 'N/A' }}</span>
                                @else
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> সাধারণ সদস্য
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

    @if(!isset($isDashboard))
        @include('components.mobile-menu')
    @endif
</header>
