@props(['popular' => collect()])

{{-- ═══════════════════════════════════════════════════════════════════════
     Blog Sidebar Component — resources/views/components/blog-sidebar.blade.php
     Usage: <x-blog-sidebar :popular="$popularPosts" />
     ═══════════════════════════════════════════════════════════════════════ --}}

{{-- ── 1. Search Widget ─────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5" id="sidebar-search">
    <h3 class="text-sm font-extrabold text-slate-800 mb-4 flex items-center gap-2">
        <span class="w-6 h-6 bg-red-50 border border-red-100 rounded-lg flex items-center justify-center text-red-500 text-xs">🔍</span>
        ব্লগ খুঁজুন
    </h3>
    <form action="{{ route('blog.index') }}" method="GET" role="search">
        <div class="relative">
            <input
                type="search"
                id="sidebar-search-input"
                name="q"
                value="{{ request('q') }}"
                placeholder="কীওয়ার্ড লিখুন..."
                autocomplete="off"
                class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 placeholder-slate-400
                       focus:outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100 transition-all"
            >
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        {{-- Preserve type filter if set --}}
        @if(request('type'))
            <input type="hidden" name="type" value="{{ request('type') }}">
        @endif
        <button type="submit"
                class="mt-3 w-full bg-red-600 hover:bg-red-700 text-white font-extrabold text-sm py-2.5 rounded-xl transition-colors shadow-sm">
            সার্চ করুন
        </button>
    </form>
</div>

{{-- ── 2. Category Filter ───────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5" id="sidebar-categories">
    <h3 class="text-sm font-extrabold text-slate-800 mb-4 flex items-center gap-2">
        <span class="w-6 h-6 bg-red-50 border border-red-100 rounded-lg flex items-center justify-center text-red-500 text-xs">📂</span>
        বিভাগ
    </h3>
    <div class="space-y-1.5">
        <a href="{{ route('blog.index') }}"
           class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-bold transition-colors
                  {{ !request('type') ? 'bg-red-50 text-red-700 border border-red-100' : 'text-slate-600 hover:bg-slate-50' }}">
            <span class="flex items-center gap-2">📋 সব পোস্ট</span>
            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
        <a href="{{ route('blog.index', ['type' => 'health']) }}"
           class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-bold transition-colors
                  {{ request('type') === 'health' ? 'bg-sky-50 text-sky-700 border border-sky-100' : 'text-slate-600 hover:bg-slate-50' }}">
            <span class="flex items-center gap-2">🏥 স্বাস্থ্য ব্লগ</span>
            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
        <a href="{{ route('blog.index', ['type' => 'story']) }}"
           class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-bold transition-colors
                  {{ request('type') === 'story' ? 'bg-rose-50 text-rose-700 border border-rose-100' : 'text-slate-600 hover:bg-slate-50' }}">
            <span class="flex items-center gap-2">💪 সাফল্যের গল্প</span>
            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</div>

{{-- ── 3. জনপ্রিয় গল্প (Most Read) ────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5" id="sidebar-popular">
    <h3 class="text-sm font-extrabold text-slate-800 mb-4 flex items-center gap-2">
        <span class="w-6 h-6 bg-rose-50 border border-rose-100 rounded-lg flex items-center justify-center text-rose-500 text-xs">🔥</span>
        জনপ্রিয় গল্প
    </h3>

    @if($popular->count() > 0)
        <ol class="space-y-3">
            @foreach($popular as $i => $pop)
                @php
                    $popReadMins = max(1, (int) ceil(str_word_count(strip_tags($pop->body_sanitized ?? '')) / 200));
                @endphp
                <li class="flex items-start gap-3 group">
                    {{-- Rank Number --}}
                    <span class="shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-black
                        {{ $i === 0 ? 'bg-amber-400 text-white' : ($i === 1 ? 'bg-slate-300 text-slate-700' : ($i === 2 ? 'bg-amber-600/70 text-white' : 'bg-slate-100 text-slate-500')) }}">
                        {{ $i + 1 }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('blog.show', $pop->slug) }}"
                           class="text-sm font-bold text-slate-700 group-hover:text-red-600 transition-colors leading-snug line-clamp-2 block">
                            {{ $pop->title }}
                        </a>
                        <span class="text-[11px] text-slate-400 font-semibold mt-0.5 block">
                            {{ $popReadMins }} মিনিট পাঠযোগ্য
                        </span>
                    </div>
                </li>
                @if(!$loop->last)
                    <li class="border-b border-slate-50 mx-1" aria-hidden="true"></li>
                @endif
            @endforeach
        </ol>
    @else
        {{-- Static placeholder (no data yet) --}}
        <div class="space-y-3">
            @foreach([
                'রক্তদান কি সত্যিই শরীরের জন্য উপকারী?',
                'প্রথমবার রক্ত দেওয়ার অভিজ্ঞতা',
                'রক্তদান করে কেন আমি গর্বিত',
                'O- রক্ত কেন বিশেষভাবে গুরুত্বপূর্ণ?',
                'রমজান মাসে রক্তদান — যা জানা জরুরি',
            ] as $idx => $placeholder)
                <div class="flex items-start gap-3 opacity-50">
                    <span class="shrink-0 w-6 h-6 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-[11px] font-black">
                        {{ $idx + 1 }}
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-slate-500 leading-snug line-clamp-2">{{ $placeholder }}</p>
                        <span class="text-[11px] text-slate-300 font-semibold mt-0.5 block">শীঘ্রই আসছে</span>
                    </div>
                </div>
            @endforeach
        </div>
        <p class="text-center text-[11px] font-bold text-slate-400 mt-4 border-t border-slate-100 pt-3">
            পোস্ট প্রকাশিত হলে এখানে দেখাবে
        </p>
    @endif
</div>

{{-- ── 4. "ডোনার হোন" CTA Banner ─────────────────────────────────────── --}}
<div id="sidebar-cta-banner"
     class="relative overflow-hidden rounded-2xl shadow-lg bg-gradient-to-br from-red-700 via-red-600 to-rose-500">
    {{-- Background pattern --}}
    <div class="absolute inset-0 opacity-10"
         style="background-image: radial-gradient(circle, rgba(255,255,255,.3) 1px, transparent 1px);
                background-size: 18px 18px;">
    </div>
    {{-- Glow blobs --}}
    <div class="absolute -top-6 -right-6 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
    <div class="absolute -bottom-4 -left-4 w-20 h-20 bg-red-900/20 rounded-full blur-xl"></div>

    <div class="relative p-6 text-center">
        {{-- Icon --}}
        <div class="w-14 h-14 mx-auto mb-4 bg-white/20 rounded-2xl flex items-center justify-center border border-white/25 backdrop-blur-sm">
            <span class="text-2xl">🩸</span>
        </div>

        <h3 class="text-white font-extrabold text-lg leading-tight mb-2">
            আজই ডোনার হোন
        </h3>
        <p class="text-white/75 text-xs font-medium mb-5 leading-relaxed">
            আপনার একটি রক্তদান পারে একটি জীবন বাঁচাতে। রক্তদূতের ভেরিফাইড ডোনার নেটওয়ার্কে যোগ দিন আজই।
        </p>

        {{-- Stats row --}}
        <div class="grid grid-cols-2 gap-3 mb-5">
            <div class="bg-white/15 rounded-xl py-2.5 px-3 border border-white/20">
                <p class="text-white font-extrabold text-lg leading-none">৫০০+</p>
                <p class="text-white/65 text-[10px] font-bold mt-0.5">ভেরিফাইড ডোনার</p>
            </div>
            <div class="bg-white/15 rounded-xl py-2.5 px-3 border border-white/20">
                <p class="text-white font-extrabold text-lg leading-none">৬৪</p>
                <p class="text-white/65 text-[10px] font-bold mt-0.5">জেলায় সক্রিয়</p>
            </div>
        </div>

        {{-- CTA Buttons --}}
        @guest
            <a href="{{ route('register') }}"
               id="sidebar-cta-register-btn"
               class="block w-full bg-white text-red-600 font-extrabold text-sm py-3 rounded-xl shadow-sm hover:bg-red-50 transition-colors mb-2">
                🩸 রক্তদাতা হিসেবে রেজিস্টার করুন
            </a>
            <a href="{{ route('login') }}"
               class="block w-full bg-white/15 border border-white/25 text-white font-bold text-sm py-2.5 rounded-xl hover:bg-white/25 transition-colors">
                লগইন করুন
            </a>
        @else
            <a href="{{ route('dashboard') }}"
               class="block w-full bg-white text-red-600 font-extrabold text-sm py-3 rounded-xl shadow-sm hover:bg-red-50 transition-colors mb-2">
                📊 আমার ড্যাশবোর্ড দেখুন
            </a>
            <a href="{{ route('public.requests.index') }}"
               class="block w-full bg-white/15 border border-white/25 text-white font-bold text-sm py-2.5 rounded-xl hover:bg-white/25 transition-colors">
                🆘 জরুরি রিকোয়েস্ট দেখুন
            </a>
        @endguest

        {{-- Privacy seal --}}
        <p class="text-white/45 text-[10px] font-semibold mt-4 flex items-center justify-center gap-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            NID ভেরিফায়েড · ডেটা সুরক্ষিত
        </p>
    </div>
</div>

{{-- ── 5. Quick Info Widget ─────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5" id="sidebar-quick-facts">
    <h3 class="text-sm font-extrabold text-slate-800 mb-4 flex items-center gap-2">
        <span class="w-6 h-6 bg-blue-50 border border-blue-100 rounded-lg flex items-center justify-center text-blue-500 text-xs">💡</span>
        দ্রুত তথ্য
    </h3>
    <ul class="space-y-3">
        @foreach([
            ['emoji' => '🩸', 'text' => 'প্রতি ৩ মাসে একবার রক্ত দেওয়া যায়'],
            ['emoji' => '💉', 'text' => 'গড় দান সময়: মাত্র ১০-১৫ মিনিট'],
            ['emoji' => '❤️', 'text' => 'একটি দান ৩টি জীবন বাঁচাতে পারে'],
            ['emoji' => '✅', 'text' => '১৬-৬৫ বছর বয়সীরা দান করতে পারেন'],
        ] as $fact)
            <li class="flex items-start gap-2.5 text-sm font-medium text-slate-600">
                <span class="shrink-0 text-base">{{ $fact['emoji'] }}</span>
                <span class="leading-snug">{{ $fact['text'] }}</span>
            </li>
        @endforeach
    </ul>
</div>
