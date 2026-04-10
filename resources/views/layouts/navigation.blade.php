<nav x-data="{ open: false }" class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            {{-- Left Side: Logo & Desktop Nav --}}
            <div class="flex items-center gap-8">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <x-application-logo class="block h-9 w-auto fill-current text-red-600" />
                        <span class="text-lg font-black text-slate-900 tracking-tight">রক্তদূত</span>
                    </a>
                </div>

                {{-- Desktop Nav Links (5 exact items) --}}
                <div class="hidden md:flex items-center gap-1 text-[15px] font-semibold text-slate-600">
                    <a href="{{ route('home') }}"
                       class="px-3 py-2 rounded-lg hover:text-red-600 hover:bg-red-50 transition-colors {{ request()->routeIs('home') ? 'text-red-600 font-bold bg-red-50' : '' }}">
                        হোম
                    </a>
                    @auth
                        <a href="{{ route('requests.index') }}"
                           class="px-3 py-2 rounded-lg hover:text-red-600 hover:bg-red-50 transition-colors {{ request()->routeIs('requests.*') ? 'text-red-600 font-bold bg-red-50' : '' }}">
                            রক্ত দিন
                        </a>
                    @endauth
                    @guest
                        <a href="{{ route('public.requests.index') }}"
                           class="px-3 py-2 rounded-lg hover:text-red-600 hover:bg-red-50 transition-colors {{ request()->routeIs('public.requests.*') ? 'text-red-600 font-bold bg-red-50' : '' }}">
                            রক্ত দিন
                        </a>
                    @endguest
                    <a href="{{ route('search') }}"
                       class="px-3 py-2 rounded-lg hover:text-red-600 hover:bg-red-50 transition-colors {{ request()->routeIs('search') ? 'text-red-600 font-bold bg-red-50' : '' }}">
                        স্মার্ট ডোনার সার্চ
                    </a>
                    <a href="{{ route('leaderboard') }}"
                       class="px-3 py-2 rounded-lg hover:text-red-600 hover:bg-red-50 transition-colors {{ request()->routeIs('leaderboard') ? 'text-red-600 font-bold bg-red-50' : '' }}">
                        লিডারবোর্ড
                    </a>
                    <a href="{{ route('blog.index') }}"
                       class="px-3 py-2 rounded-lg hover:text-red-600 hover:bg-red-50 transition-colors {{ request()->routeIs('blog.*') ? 'text-red-600 font-bold bg-red-50' : '' }}">
                        ব্লগ
                    </a>
                </div>
            </div>

            {{-- Right Side: Action CTA + Notification + Profile --}}
            <div class="hidden sm:flex sm:items-center gap-4">

                {{-- রিকোয়েস্ট করুন --}}
                <a href="{{ route('requests.create') }}"
                   class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl font-black text-sm shadow-sm transition">
                    রিকোয়েস্ট করুন
                </a>

                <div class="h-5 w-px bg-slate-200"></div>

                {{-- Notification Icon --}}
                <div class="relative flex items-center" x-data="{ dropdownOpen: false }" @click.outside="dropdownOpen = false" @close.stop="dropdownOpen = false">
                    <button @click="dropdownOpen = ! dropdownOpen" class="relative p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-full transition focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[10px] font-black text-white shadow-sm ring-2 ring-white">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>

                    <div x-show="dropdownOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 top-12 z-50 w-80 rounded-2xl bg-white shadow-xl ring-1 ring-slate-200"
                         style="display: none;">
                        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50 rounded-t-2xl">
                            <h3 class="text-sm font-extrabold text-slate-800">নোটিফিকেশন</h3>
                        </div>
                        <div class="max-h-80 overflow-y-auto p-4 text-center text-sm font-bold text-slate-400">
                            আপাতত কোনো নোটিফিকেশন নেই।
                        </div>
                    </div>
                </div>

                {{-- Profile Dropdown --}}
                <div class="flex items-center">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-2 px-3 py-1.5 border border-slate-200 hover:border-slate-300 rounded-full bg-white hover:bg-slate-50 transition focus:outline-none">
                                <div class="w-7 h-7 rounded-full bg-slate-800 text-white flex items-center justify-center text-xs font-black">
                                    {{ mb_substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <span class="text-sm font-extrabold text-slate-700">{{ Auth::user()->name }}</span>
                                <svg class="fill-current h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')" class="font-bold">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" class="font-bold text-red-600"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            {{-- Mobile Hamburger Button --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu Content --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-slate-100 bg-white">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')" class="font-bold">
                হোম
            </x-responsive-nav-link>
            @auth
                <x-responsive-nav-link :href="route('requests.index')" :active="request()->routeIs('requests.*')" class="font-bold">
                    রক্ত দিন
                </x-responsive-nav-link>
            @endauth
            @guest
                <x-responsive-nav-link :href="route('public.requests.index')" class="font-bold">
                    রক্ত দিন
                </x-responsive-nav-link>
            @endguest
            <x-responsive-nav-link :href="route('search')" :active="request()->routeIs('search')" class="font-bold">
                স্মার্ট ডোনার সার্চ
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('leaderboard')" :active="request()->routeIs('leaderboard')" class="font-bold">
                লিডারবোর্ড
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('blog.index')" :active="request()->routeIs('blog.*')" class="font-bold">
                ব্লগ
            </x-responsive-nav-link>
        </div>
        <div class="pt-4 pb-1 border-t border-slate-100">
            <div class="px-4">
                <div class="font-extrabold text-base text-slate-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-slate-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="font-bold">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" class="text-red-600 font-bold"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>