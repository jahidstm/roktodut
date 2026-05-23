@extends('layouts.app')

@section('title', 'রক্তদূত — রক্ত দিন, জীবন বাঁচান')

@section('content')

@php
    $btnPrimary = 'inline-flex items-center gap-2.5 bg-red-600 hover:bg-red-700 text-white rounded-2xl font-black shadow-[0_10px_25px_rgba(239,68,68,0.3)] hover:shadow-[0_15px_35px_rgba(239,68,68,0.4)] transition-all duration-300 hover-lift';
    $btnSecondary = 'inline-flex items-center gap-2.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-800 rounded-2xl font-black shadow-sm transition-all duration-300 hover:shadow-md hover-lift';
    $btnOutlineOnRed = 'inline-flex items-center gap-2.5 bg-transparent border border-white/30 hover:border-white text-white rounded-2xl font-black shadow-sm transition-all duration-300 hover:bg-white/5 hover:shadow-md hover-lift';
@endphp

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 1 — LIGHT HERO (Premium Aesthetic)
═══════════════════════════════════════════════════════════════ --}}
<section class="relative bg-white flex flex-col items-center justify-center overflow-hidden pb-0 pt-16 sm:pt-24" aria-label="হিরো সেকশন">

    {{-- Background glows --}}
    <div class="absolute top-[-100px] left-1/2 -translate-x-1/2 w-[700px] h-[500px] bg-red-100/60 rounded-full blur-[130px] pointer-events-none"></div>
    <div class="absolute bottom-20 right-10 w-[300px] h-[300px] bg-red-50/80 rounded-full blur-[100px] pointer-events-none"></div>

    {{-- Subtle grid --}}
    <div class="absolute inset-0 opacity-[0.4] pointer-events-none" style="background-image: linear-gradient(rgba(0,0,0,0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,0.05) 1px, transparent 1px); background-size: 64px 64px;"></div>

    {{-- Hero content --}}
    <div class="relative z-10 mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 flex flex-col items-center text-center pt-8 pb-10">

        {{-- Badge --}}
        <div class="inline-flex items-center gap-2.5 bg-white border border-red-100 text-red-600 shadow-sm text-xs font-bold px-4 py-2 rounded-full mb-6 scroll-reveal" data-scroll-reveal>
            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse flex-shrink-0"></span>
            বাংলাদেশের সবচেয়ে বড় ভেরিফাইড ব্লাড ডোনার নেটওয়ার্ক
        </div>

        {{-- Headline --}}
        <h1 class="text-3xl sm:text-5xl lg:text-[54px] font-black text-slate-900 leading-[1.15] mb-5 tracking-tight scroll-reveal" data-scroll-reveal>
            জরুরি মুহূর্তে রক্তের সন্ধানে—<br>
            <span class="text-red-600">আমরা আছি আপনার পাশে</span>
        </h1>

        {{-- Subtitle --}}
        <p class="text-sm sm:text-lg text-slate-500 mb-8 max-w-2xl mx-auto leading-relaxed font-medium scroll-reveal" data-scroll-reveal>
            NID-ভেরিফাইড ডোনার নেটওয়ার্ক — জরুরি মুহূর্তে সঠিক রক্ত, সঠিক সময়ে।
            ৬৪ জেলায় সক্রিয়, সম্পূর্ণ বিনামূল্যে।
        </p>

        {{-- CTAs --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-8 scroll-reveal" data-scroll-reveal>
            <a href="{{ route('search') }}"
               class="{{ $btnPrimary }} px-5 py-3 text-sm sm:px-7 sm:py-3.5 sm:text-base">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                এখনই ডোনার খুঁজুন
            </a>
            <a href="{{ route('requests.create') }}"
               class="{{ $btnSecondary }} px-5 py-3 text-sm sm:px-7 sm:py-3.5 sm:text-base">
                জরুরি রিকোয়েস্ট করুন
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

        {{-- Trust pills --}}
        <div class="flex flex-wrap justify-center gap-2.5 mb-4 scroll-reveal" data-scroll-reveal>
            @foreach(['🔒 ফোন নম্বর সর্বদা গোপন', '💳 NID ভেরিফাইড ডোনার', '📍 ৬৪ জেলায় সক্রিয়', '✅ সম্পূর্ণ বিনামূল্যে'] as $pill)
            <span class="inline-flex items-center gap-2 bg-slate-50 border border-slate-200 text-slate-600 text-[11px] sm:text-xs font-semibold px-3 py-1.5 rounded-full">{{ $pill }}</span>
            @endforeach
        </div>
    </div>

    {{-- Search Box — sits at the very bottom of the hero --}}
    <div id="search-section" class="relative z-20 w-full" x-data="{
        districts: [],
        upazilas: [],
        loadingDistricts: false,
        loadingUpazilas: false,
        fetchDistricts(divisionId) {
            this.districts = [];
            this.upazilas = [];
            if (!divisionId) return;
            this.loadingDistricts = true;
            fetch('/ajax/districts/' + divisionId)
                .then(r => r.json())
                .then(data => { this.districts = data; this.loadingDistricts = false; })
                .catch(() => { this.loadingDistricts = false; });
        },
        fetchUpazilas(districtId) {
            this.upazilas = [];
            if (!districtId) return;
            this.loadingUpazilas = true;
            fetch('/ajax/upazilas/' + districtId)
                .then(r => r.json())
                .then(data => { this.upazilas = data; this.loadingUpazilas = false; })
                .catch(() => { this.loadingUpazilas = false; });
        }
    }">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 pb-16">
            <div class="bg-white border border-slate-100 rounded-[2rem] p-6 sm:p-8 shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] scroll-reveal" data-scroll-reveal>

                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-full bg-red-50 border border-red-100 flex items-center justify-center text-red-500 flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">দ্রুত ডোনার খুঁজুন</h3>
                        <p class="text-xs font-medium text-slate-500 mt-0.5">রক্তের গ্রুপ ও এলাকা দিয়ে দ্রুত ডোনার খুঁজুন</p>
                    </div>
                </div>

                <form action="{{ route('search') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3 items-end">

                    <div class="lg:col-span-1">
                        <select name="blood_group" class="w-full bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl px-4 py-3 sm:py-3.5 text-sm font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-red-500/30 focus:border-red-500 transition-all cursor-pointer">
                            <option value="" class="text-slate-500">রক্তের গ্রুপ</option>
                            @foreach(\App\Enums\BloodGroup::cases() as $bg)
                                <option value="{{ $bg->value }}" class="text-slate-900">{{ $bg->value }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-1">
                        <select name="division_id" @change="fetchDistricts($event.target.value)" class="w-full bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl px-4 py-3 sm:py-3.5 text-sm font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-red-500/30 focus:border-red-500 transition-all cursor-pointer">
                            <option value="" class="text-slate-500">বিভাগ নির্বাচন</option>
                            @foreach($divisions as $div)
                                <option value="{{ $div->id }}" class="text-slate-900">{{ $div->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-1">
                        <select name="district_id" @change="fetchUpazilas($event.target.value)" :disabled="districts.length === 0" class="w-full bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl px-4 py-3 sm:py-3.5 text-sm font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-red-500/30 focus:border-red-500 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                            <option value="" x-text="loadingDistricts ? 'লোড হচ্ছে...' : (districts.length === 0 ? 'জেলা নির্বাচন' : 'সব জেলা')" class="text-slate-500"></option>
                            <template x-for="d in districts" :key="d.id">
                                <option :value="d.id" x-text="d.name" class="text-slate-900"></option>
                            </template>
                        </select>
                    </div>

                    <div class="lg:col-span-1">
                        <select name="upazila_id" :disabled="upazilas.length === 0" class="w-full bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl px-4 py-3 sm:py-3.5 text-sm font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-red-500/30 focus:border-red-500 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                            <option value="" x-text="loadingUpazilas ? 'লোড হচ্ছে...' : (upazilas.length === 0 ? 'উপজেলা / এরিয়া' : 'সব উপজেলা')" class="text-slate-500"></option>
                            <template x-for="u in upazilas" :key="u.id">
                                <option :value="u.id" x-text="u.name" class="text-slate-900"></option>
                            </template>
                        </select>
                    </div>

                    <div class="lg:col-span-1">
                        <button type="submit" class="w-full {{ $btnPrimary }} px-4 py-3 sm:py-3.5 text-sm sm:text-base h-[48px] sm:h-[52px]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            সার্চ করুন
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>



{{-- ═══════════════════════════════════════════════════════════════
     SECTION 3 — STATS COUNTER
═══════════════════════════════════════════════════════════════ --}}
<section class="bg-[#18181b] py-16 sm:py-20 border-t border-[#2a2a30]">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-white/10">
            <div class="text-center px-8 py-6 sm:py-0 scroll-reveal" data-scroll-reveal>
                <div class="text-4xl sm:text-5xl lg:text-[56px] font-black text-white mb-3 tabular-nums">
                    <span class="counter" data-target="{{ $totalDonors }}">0</span><span class="text-[#e11d48]">+</span>
                </div>
                <div class="text-sm sm:text-[15px] font-bold text-slate-400">রেজিস্টার্ড ডোনার</div>
            </div>
            <div class="text-center px-8 py-6 sm:py-0 scroll-reveal" data-scroll-reveal>
                <div class="text-4xl sm:text-5xl lg:text-[56px] font-black text-white mb-3 tabular-nums">
                    <span class="counter" data-target="{{ $verifiedDonors }}">0</span><span class="text-[#e11d48]">+</span>
                </div>
                <div class="text-sm sm:text-[15px] font-bold text-slate-400">ভেরিফাইড ডোনার</div>
            </div>
            <div class="text-center px-8 py-6 sm:py-0 scroll-reveal" data-scroll-reveal>
                <div class="text-4xl sm:text-5xl lg:text-[56px] font-black text-white mb-3 tabular-nums">
                    <span class="counter" data-target="{{ $totalDonations }}">0</span><span class="text-[#e11d48]">+</span>
                </div>
                <div class="text-sm sm:text-[15px] font-bold text-slate-400">সফল রক্তদান</div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 4 — FEATURES (Red)
═══════════════════════════════════════════════════════════════ --}}
<section class="bg-[#c82128] py-24 relative overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_50%_at_50%_0%,rgba(255,255,255,0.07),transparent)] pointer-events-none"></div>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16">
            <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-white text-xs font-bold px-5 py-2 rounded-full mb-6 shadow-sm scroll-reveal" data-scroll-reveal>✦ কেন রক্তদূত?</div>
            <h2 class="text-4xl lg:text-5xl font-black text-white mb-5 tracking-tight scroll-reveal" data-scroll-reveal>একটি প্ল্যাটফর্ম, <span class="text-white/90">অসংখ্য সুবিধা</span></h2>
            <p class="text-white/80 font-medium max-w-xl mx-auto text-lg scroll-reveal" data-scroll-reveal>রক্তদূত শুধু একটি ওয়েবসাইট নয় — এটি বাংলাদেশের সবচেয়ে নির্ভরযোগ্য জীবনরক্ষা নেটওয়ার্ক।</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @php
            $features = [
                ['icon'=>'🔒','title'=>'সম্পূর্ণ গোপনীয়','desc'=>'ডোনারের ফোন নম্বর কখনো পাবলিক করা হয় না। OTP সিস্টেমে সুরক্ষিত যোগাযোগ।','ring'=>'ring-white/20','glow'=>'bg-white/10'],
                ['icon'=>'💳','title'=>'NID ভেরিফিকেশন','desc'=>'জাতীয় পরিচয়পত্র দিয়ে যাচাইকৃত ডোনার। ভুয়া ডোনার শূন্য।','ring'=>'ring-white/20','glow'=>'bg-white/10'],
                ['icon'=>'⚡','title'=>'স্মার্ট ম্যাচিং','desc'=>'সবচেয়ে কাছের ও উপযুক্ত ডোনার খুঁজে দেয় মুহূর্তের মধ্যে।','ring'=>'ring-white/20','glow'=>'bg-white/10'],
                ['icon'=>'🗺️','title'=>'লাইভ ডিমান্ড ম্যাপ','desc'=>'রিয়েল-টাইম হিটম্যাপে দেখুন কোন জেলায় রক্তের চাহিদা সবচেয়ে বেশি।','ring'=>'ring-white/20','glow'=>'bg-white/10'],
            ];
            @endphp
            @foreach($features as $f)
            <div class="bg-white/5 border border-white/10 rounded-2xl p-7 hover:bg-white/10 hover:border-white/20 transition-all duration-300 group cursor-default shadow-sm hover:shadow-md scroll-reveal" data-scroll-reveal>
                <div class="w-14 h-14 rounded-2xl {{ $f['glow'] }} ring-1 {{ $f['ring'] }} flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
                    {{ $f['icon'] }}
                </div>
                <h3 class="text-white font-black text-lg mb-3">{{ $f['title'] }}</h3>
                <p class="text-white/80 text-sm leading-relaxed font-medium">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 5 — EMERGENCY FEED
═══════════════════════════════════════════════════════════════ --}}
<section class="py-24 bg-white" aria-label="জরুরি রক্তের অনুরোধ">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between gap-6 mb-14">
            <div>
                <div class="inline-flex items-center gap-2 bg-red-50 border border-red-100 text-red-600 text-xs font-bold px-4 py-1.5 rounded-full mb-5 scroll-reveal" data-scroll-reveal>
                    <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse flex-shrink-0"></span>
                    লাইভ আপডেট
                </div>
                <h2 class="text-4xl font-black text-slate-900 tracking-tight scroll-reveal" data-scroll-reveal>জরুরি রক্তের অনুরোধ</h2>
                <p class="text-slate-500 font-medium mt-2 max-w-lg scroll-reveal" data-scroll-reveal>এই মুহূর্তে যারা জরুরি ভিত্তিতে রক্তের প্রয়োজনে আছেন। আপনার একটু সাহায্য একটি জীবন বাঁচাতে পারে।</p>
            </div>
            <a href="{{ route('public.requests.index') }}" class="shrink-0 {{ $btnSecondary }} px-5 py-2.5 text-sm scroll-reveal" data-scroll-reveal>
                সব দেখুন <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        @if($homeRequests->isNotEmpty())
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($homeRequests as $req)
                    <div class="scroll-reveal" data-scroll-reveal>
                        <x-request-feed-card :request="$req" :show-requester="true" />
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16 bg-slate-50 rounded-3xl border border-slate-100">
                <p class="text-slate-500 font-medium">বর্তমানে কোনো জরুরি অনুরোধ নেই।</p>
            </div>
        @endif
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 6 — DONATION JOURNEY
═══════════════════════════════════════════════════════════════ --}}
<section class="py-24 bg-slate-50 relative overflow-hidden border-t border-slate-100">
    <div class="absolute -top-24 right-10 w-72 h-72 bg-red-500/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-6 w-80 h-80 bg-emerald-400/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-14">
            <div class="inline-flex items-center gap-2 bg-white border border-red-100 text-red-600 text-xs font-bold px-4 py-2 rounded-full mb-5 shadow-sm scroll-reveal" data-scroll-reveal>
                রক্তদানের জার্নি
            </div>
            <h2 class="text-4xl lg:text-5xl font-black text-slate-900 mb-4 tracking-tight leading-tight scroll-reveal" data-scroll-reveal>
                মাত্র ৩ ধাপে<br>রক্ত দিন বা নিন
            </h2>
            <p class="text-slate-500 font-medium text-lg scroll-reveal" data-scroll-reveal>
                স্মার্ট ফিল্টার, ভেরিফায়েড ডোনার এবং নিরাপদ যোগাযোগ — সবকিছু এক প্ল্যাটফর্মে।
            </p>
        </div>

        @php
            $journeySteps = [
                [
                    'num' => '০১',
                    'title' => 'প্রোফাইল ও যাচাই সম্পন্ন করুন',
                    'desc' => 'রক্তের গ্রুপ, ঠিকানা ও NID দিয়ে ফ্রি অ্যাকাউন্ট খুলুন।',
                    'bullets' => ['ফোন নম্বর গোপন থাকে', 'NID ভেরিফাইড ডোনার', 'ডোনার অ্যাভেইলেবিলিটি সেট করুন'],
                    'cardClass' => 'bg-red-50/70 border-red-100',
                    'pillClass' => 'bg-red-100 text-red-600',
                    'badgeClass' => 'bg-red-500 text-white',
                    'dotClass' => 'text-red-500',
                    'icon' => '🩸',
                ],
                [
                    'num' => '০২',
                    'title' => 'সঠিক ডোনার বা রিকোয়েস্ট ম্যাচ করুন',
                    'desc' => 'স্মার্ট ফিল্টার ও লোকেশন দিয়ে দ্রুত উপযুক্ত ম্যাচ খুঁজুন।',
                    'bullets' => ['রক্তের গ্রুপ ও এলাকা ফিল্টার', 'রিয়েল-টাইম স্ট্যাটাস দেখা যায়', 'প্রয়োজনীয় নোট যুক্ত করুন'],
                    'cardClass' => 'bg-amber-50/70 border-amber-100',
                    'pillClass' => 'bg-amber-100 text-amber-600',
                    'badgeClass' => 'bg-amber-500 text-white',
                    'dotClass' => 'text-amber-500',
                    'icon' => '🧭',
                ],
                [
                    'num' => '০৩',
                    'title' => 'সাড়া দিন ও জীবন বাঁচান',
                    'desc' => 'প্ল্যাটফর্মে যোগাযোগ করে নিরাপদে রক্তদান সম্পন্ন করুন।',
                    'bullets' => ['রিকোয়েস্ট ট্র্যাক করা যায়', 'সেফ যোগাযোগ ব্যবস্থাপনা', 'কমিউনিটি কৃতজ্ঞতা শেয়ার'],
                    'cardClass' => 'bg-emerald-50/70 border-emerald-100',
                    'pillClass' => 'bg-emerald-100 text-emerald-600',
                    'badgeClass' => 'bg-emerald-500 text-white',
                    'dotClass' => 'text-emerald-500',
                    'icon' => '🤝',
                ],
            ];
        @endphp

        <div class="relative">
            <div class="absolute left-1/2 top-0 hidden lg:block -translate-x-1/2 h-full w-px border-l-2 border-dashed border-slate-200"></div>
            <div class="space-y-10">
                @foreach($journeySteps as $step)
                    <div class="relative grid lg:grid-cols-[1fr_auto_1fr] items-center gap-8">
                        @if($loop->odd)
                            <div class="lg:col-start-1 flex lg:justify-end scroll-reveal scroll-reveal--left" data-scroll-reveal>
                                <div class="w-full max-w-md rounded-3xl border {{ $step['cardClass'] }} p-6 sm:p-7 shadow-[0_15px_45px_rgba(0,0,0,0.06)]">
                                    <div class="flex items-center justify-between gap-3 mb-4">
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold {{ $step['pillClass'] }}">ধাপ {{ $step['num'] }}</span>
                                        <span class="text-xl">{{ $step['icon'] }}</span>
                                    </div>
                                    <h3 class="text-xl font-black text-slate-900 mb-2">{{ $step['title'] }}</h3>
                                    <p class="text-slate-600 font-medium leading-relaxed">{{ $step['desc'] }}</p>
                                    <ul class="mt-4 space-y-2 text-sm font-semibold text-slate-600">
                                        @foreach($step['bullets'] as $bullet)
                                            <li class="flex items-start gap-2">
                                                <span class="mt-1 text-xs {{ $step['dotClass'] }}">●</span>
                                                <span>{{ $bullet }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="hidden lg:flex items-center justify-center scroll-reveal" data-scroll-reveal>
                                <div class="w-16 h-16 rounded-full border-4 border-white shadow-lg {{ $step['badgeClass'] }} flex items-center justify-center font-black text-lg">
                                    {{ $step['num'] }}
                                </div>
                            </div>
                            <div class="lg:col-start-3"></div>
                        @else
                            <div class="lg:col-start-1"></div>
                            <div class="hidden lg:flex items-center justify-center scroll-reveal" data-scroll-reveal>
                                <div class="w-16 h-16 rounded-full border-4 border-white shadow-lg {{ $step['badgeClass'] }} flex items-center justify-center font-black text-lg">
                                    {{ $step['num'] }}
                                </div>
                            </div>
                            <div class="lg:col-start-3 flex lg:justify-start scroll-reveal scroll-reveal--right" data-scroll-reveal>
                                <div class="w-full max-w-md rounded-3xl border {{ $step['cardClass'] }} p-6 sm:p-7 shadow-[0_15px_45px_rgba(0,0,0,0.06)]">
                                    <div class="flex items-center justify-between gap-3 mb-4">
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold {{ $step['pillClass'] }}">ধাপ {{ $step['num'] }}</span>
                                        <span class="text-xl">{{ $step['icon'] }}</span>
                                    </div>
                                    <h3 class="text-xl font-black text-slate-900 mb-2">{{ $step['title'] }}</h3>
                                    <p class="text-slate-600 font-medium leading-relaxed">{{ $step['desc'] }}</p>
                                    <ul class="mt-4 space-y-2 text-sm font-semibold text-slate-600">
                                        @foreach($step['bullets'] as $bullet)
                                            <li class="flex items-start gap-2">
                                                <span class="mt-1 text-xs {{ $step['dotClass'] }}">●</span>
                                                <span>{{ $bullet }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-14 flex justify-center scroll-reveal" data-scroll-reveal>
            <a href="{{ route('register') }}" class="{{ $btnPrimary }} px-7 py-3.5 text-sm sm:text-base">
                আজই যুক্ত হোন
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 7 — LIVE MAP CTA (Red Compact)
═══════════════════════════════════════════════════════════════ --}}
<section class="bg-[#c82128] py-14 relative overflow-hidden">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/10 text-3xl mb-6 scroll-reveal" data-scroll-reveal>🗺️</div>
        
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white mb-4 tracking-tight scroll-reveal" data-scroll-reveal>
            লাইভ রক্তের চাহিদা মানচিত্র
        </h2>
        
        <p class="text-white/90 text-sm sm:text-base font-medium max-w-2xl mx-auto mb-8 leading-relaxed scroll-reveal" data-scroll-reveal>
            কোন জেলায় রক্তের সংকট সবচেয়ে বেশি? রিয়েল-টাইম হিটম্যাপে দেখুন এবং সবচেয়ে প্রয়োজনীয় জায়গায় রক্তদান করুন।
        </p>
        
        <a href="{{ route('live-demand.index') }}"
           class="{{ $btnSecondary }} px-7 py-3.5 text-sm sm:text-base scroll-reveal" data-scroll-reveal>
            লাইভ ম্যাপ দেখুন
            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
        
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 8 — TOP DONORS (Podium)
═══════════════════════════════════════════════════════════════ --}}
<section class="py-24 bg-white">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-16">
            <div class="inline-flex items-center gap-2 bg-amber-50 border border-amber-100 text-amber-600 text-xs font-bold px-4 py-2 rounded-full mb-5 scroll-reveal" data-scroll-reveal>🦸 রিয়েল লাইফ সুপারহিরো</div>
            <h2 class="text-4xl lg:text-5xl font-black text-slate-900 tracking-tight mb-4 scroll-reveal" data-scroll-reveal>আমাদের সেরা রক্তদাতাগণ</h2>
            <p class="text-slate-500 font-medium max-w-xl mx-auto scroll-reveal" data-scroll-reveal>যারা সবচেয়ে বেশি মানুষের জীবন বাঁচিয়েছেন, তাদের প্রতি আমাদের কৃতজ্ঞতা।</p>
        </div>

        @if($topDonors->isNotEmpty())
            @php
                $orderedDonors = collect();
                if($topDonors->has(0)) $orderedDonors->push(['donor'=>$topDonors[0],'rank'=>1,'emoji'=>'🥇','label'=>'১ম স্থান']);
                if($topDonors->has(1)) $orderedDonors->push(['donor'=>$topDonors[1],'rank'=>2,'emoji'=>'🥈','label'=>'২য় স্থান']);
                if($topDonors->has(2)) $orderedDonors->push(['donor'=>$topDonors[2],'rank'=>3,'emoji'=>'🥉','label'=>'৩য় স্থান']);
            @endphp

            <div class="flex flex-col lg:flex-row items-end justify-center gap-5 mt-8">
                @foreach($orderedDonors as $item)
                    @php
                        $d = $item['donor'];
                        $rank = $item['rank'];
                        $initial = mb_strtoupper(mb_substr($d->name, 0, 1));
                        $isFirst = $rank === 1;
                        $cardClasses = $isFirst
                            ? "border-amber-200 shadow-[0_20px_60px_rgba(251,191,36,0.12)] lg:scale-110 pb-8 pt-12"
                            : "border-slate-100 shadow-sm pb-6 pt-10 mt-0 lg:mt-8";
                        $avatarRing = match($rank) { 1=>"ring-4 ring-amber-100 border-amber-300", 2=>"border-slate-200", 3=>"border-orange-200", default=>"border-slate-100" };
                        $orderClass = match($rank) { 1=>"order-1 lg:order-2", 2=>"order-2 lg:order-1", 3=>"order-3 lg:order-3", default=>"order-4" };
                    @endphp

                    <div class="bg-white rounded-3xl px-6 w-full max-w-xs mx-auto lg:mx-0 flex flex-col items-center relative border-2 {{ $cardClasses }} {{ $orderClass }} transition-all duration-300 hover:-translate-y-2 scroll-reveal" data-scroll-reveal>
                        <div class="absolute -top-5 bg-slate-900 text-white text-xs font-black px-5 py-2 rounded-full flex items-center gap-1.5 shadow-lg">
                            {{ $item['emoji'] }} {{ $item['label'] }}
                        </div>
                        <div class="w-20 h-20 rounded-full border-[3px] {{ $avatarRing }} flex items-center justify-center text-3xl font-black mb-5 bg-gradient-to-br from-red-50 to-rose-100 text-red-600">{{ $initial }}</div>
                        <h3 class="text-lg font-black text-slate-900 text-center mb-2 truncate w-full">{{ $d->name }}</h3>
                        @if($d->blood_group)
                        <div class="bg-red-50 text-red-600 font-bold text-[10px] px-3 py-1 rounded-lg mb-5 uppercase tracking-widest border border-red-100">{{ $d->blood_group?->value ?? $d->blood_group }} ডোনার</div>
                        @else
                        <div class="h-6 mb-5"></div>
                        @endif
                        <div class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 flex items-center divide-x divide-slate-200 mb-5">
                            <div class="flex-1 text-center pr-4">
                                <div class="text-2xl font-black text-slate-900">{{ $d->total_verified_donations ?? 0 }}</div>
                                <div class="text-[10px] font-bold text-slate-400 mt-1">রক্তদান</div>
                            </div>
                            <div class="flex-1 text-center pl-4">
                                <div class="text-2xl font-black text-slate-900">{{ number_format($d->points ?? 0) }}</div>
                                <div class="text-[10px] font-bold text-slate-400 mt-1">পয়েন্ট</div>
                            </div>
                        </div>
                        <div class="flex gap-2 justify-center">
                            @if($d->badges->isNotEmpty())
                                @foreach($d->badges->take(3) as $badge)
                                    @php $bd = \App\Services\GamificationService::getBadgeDisplayData($badge->name); @endphp
                                    <span class="text-xl drop-shadow-sm hover:scale-125 transition-transform cursor-help" title="{{ $bd['bn'] }}">{{ $bd['emoji'] }}</span>
                                @endforeach
                            @else
                                <span class="text-xl opacity-20 grayscale">🎖️</span><span class="text-xl opacity-20 grayscale">🎖️</span><span class="text-xl opacity-20 grayscale">🎖️</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-16 text-center scroll-reveal" data-scroll-reveal>
                <a href="{{ route('leaderboard') }}" class="{{ $btnSecondary }} px-7 py-3.5 text-sm sm:text-base">
                    সম্পূর্ণ লিডারবোর্ড দেখুন
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        @endif
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 9 — BLOG
═══════════════════════════════════════════════════════════════ --}}
<section class="py-24 bg-slate-50 border-t border-slate-100">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between gap-6 mb-14">
            <div>
                <div class="inline-flex items-center gap-2 bg-sky-50 border border-sky-100 text-sky-600 text-xs font-bold px-4 py-1.5 rounded-full mb-5 scroll-reveal" data-scroll-reveal>সংবাদ ও নিবন্ধ</div>
                <h2 class="text-4xl font-black text-slate-900 tracking-tight scroll-reveal" data-scroll-reveal>সর্বশেষ ব্লগ পোস্ট</h2>
            </div>
            <a href="{{ route('blog.index') }}" class="shrink-0 {{ $btnSecondary }} px-5 py-2.5 text-sm scroll-reveal" data-scroll-reveal>
                সব পোস্ট <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        @if($recentPosts->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($recentPosts as $post)
                    @php
                        $isStory   = $post->type === 'story';
                        $wordCount = str_word_count(strip_tags($post->body_sanitized ?? ''));
                        $readMins  = max(1, (int) ceil($wordCount / 200));
                        $displayName = $post->author->name ?? 'অজানা';
                        $showRealAvatar = true;
                        if ($isStory && $post->storyMeta) {
                            $lvl = $post->storyMeta->anonymize_level;
                            if ($lvl === 'anonymous') { $displayName = 'একজন রক্তদাতা'; $showRealAvatar = false; }
                            elseif ($lvl === 'initials') {
                                $parts = explode(' ', $post->author->name ?? '');
                                $displayName = collect($parts)->map(fn($p) => mb_substr($p, 0, 1) . '.')->implode(' ');
                                $showRealAvatar = false;
                            }
                        }
                    @endphp

                    <article class="group bg-white rounded-2xl border border-slate-100 hover:border-slate-200 hover:shadow-[0_8px_30px_rgba(0,0,0,0.07)] transition-all duration-300 overflow-hidden flex flex-col hover:-translate-y-0.5 scroll-reveal" data-scroll-reveal>
                        <a href="{{ route('blog.show', $post->slug) }}" class="block relative overflow-hidden shrink-0 aspect-video bg-gradient-to-br {{ $isStory ? 'from-rose-100 to-pink-50' : 'from-sky-100 to-blue-50' }}">
                            @if($post->cover_image)
                                <img src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->title }}" loading="lazy" decoding="async" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="absolute inset-0 flex items-center justify-center"><span class="text-5xl opacity-20">{{ $isStory ? '💪' : '🩺' }}</span></div>
                            @endif
                            <div class="absolute top-3 left-3">
                                @if($isStory)
                                    <span class="inline-flex items-center gap-1 bg-rose-600 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-lg shadow-sm">💪 সাফল্যের গল্প</span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-sky-600 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-lg shadow-sm">🏥 স্বাস্থ্য ব্লগ</span>
                                @endif
                            </div>
                            @if($isStory && $post->storyMeta?->is_verified_story)
                                <div class="absolute top-3 right-3"><span class="inline-flex items-center gap-1 bg-emerald-500 text-white text-[10px] font-extrabold px-2 py-1 rounded-lg">✅ Verified</span></div>
                            @endif
                        </a>
                        <div class="flex flex-col flex-1 p-5">
                            <div class="flex items-center gap-2 mb-3 text-xs text-slate-400 font-medium">
                                <span>{{ $readMins }} মিনিট পাঠযোগ্য</span>
                                <span>•</span>
                                <span>{{ $post->published_at?->locale('bn')->isoFormat('D MMM, YYYY') ?? $post->created_at->locale('bn')->isoFormat('D MMM, YYYY') }}</span>
                            </div>
                            <h3 class="font-extrabold text-slate-900 text-base leading-snug mb-3 group-hover:text-red-600 transition-colors line-clamp-2 flex-1">
                                <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                            </h3>
                            @if($post->categories->count() > 0)
                                <div class="flex flex-wrap gap-1.5 mb-4">
                                    @foreach($post->categories->take(3) as $cat)
                                        <span class="text-[11px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-md">{{ $cat->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                            <div class="flex items-center justify-between gap-2 pt-4 border-t border-slate-100">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-red-400 to-rose-500 flex items-center justify-center text-white text-xs font-black overflow-hidden">
                                        @if(!$showRealAvatar)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        @elseif($post->author?->profile_image)
                                            <img src="{{ asset('storage/' . $post->author->profile_image) }}" alt="Author" class="w-full h-full object-cover" loading="lazy">
                                        @else
                                            {{ mb_substr($displayName, 0, 1) }}
                                        @endif
                                    </div>
                                    <span class="text-xs font-bold text-slate-600 truncate">{{ $displayName }}</span>
                                </div>
                                <a href="{{ route('blog.show', $post->slug) }}" class="shrink-0 text-red-600 text-xs font-extrabold hover:text-red-700 flex items-center gap-1">
                                    পড়ুন <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="text-center py-10"><p class="text-slate-500 font-medium">এখনো কোনো নিবন্ধ প্রকাশিত হয়নি।</p></div>
        @endif
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 10 — FINAL CTA (Red Compact)
═══════════════════════════════════════════════════════════════ --}}
<section class="bg-[#c82128] py-14 relative overflow-hidden">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white mb-4 tracking-tight scroll-reveal" data-scroll-reveal>
            আজই একটি জীবন বাঁচান
        </h2>
        
        <p class="text-white/90 text-sm sm:text-base font-medium max-w-2xl mx-auto mb-8 leading-relaxed scroll-reveal" data-scroll-reveal>
            আপনার একটি রক্তদান তিনটি প্রাণ বাঁচাতে পারে। বাংলাদেশের সবচেয়ে নির্ভরযোগ্য ভেরিফাইড ডোনার নেটওয়ার্কে এখনই যোগ দিন।
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-8 scroll-reveal" data-scroll-reveal>
            <a href="{{ route('requests.create') }}"
               class="{{ $btnSecondary }} px-7 py-3.5 text-sm sm:text-base">
                রক্তের রিকোয়েস্ট করুন
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
            <a href="{{ route('search') }}"
               class="{{ $btnOutlineOnRed }} px-7 py-3.5 text-sm sm:text-base">
                ডোনার সার্চ করুন
            </a>
        </div>

        <div class="flex flex-col items-center justify-center gap-2 scroll-reveal" data-scroll-reveal>
            <div class="flex items-center justify-center gap-2 text-white font-bold text-xs sm:text-sm">
                <svg class="w-3.5 h-3.5 text-yellow-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C9.243 2 7 4.243 7 7v3H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2v-8a2 2 0 00-2-2h-1V7c0-2.757-2.243-5-5-5zm-3 5c0-1.654 1.346-3 3-3s3 1.346 3 3v3H9V7z"/></svg>
                মোবাইল নম্বর কখনো পাবলিক করা হয় না।
            </div>
            <div class="text-white/70 text-[10px] sm:text-xs font-semibold tracking-wide">
                বিনামূল্যে &middot; NID ভেরিফায়েড নেটওয়ার্ক &middot; ৬৪ জেলায় সক্রিয়
            </div>
        </div>
        
    </div>
</section>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const counters = document.querySelectorAll('.counter[data-target]');
        if (!counters.length) return;
        const run = (el) => {
            const target = parseInt(el.dataset.target, 10) || 0;
            const duration = 2500;
            const start = performance.now();
            const step = (now) => {
                const progress = Math.min((now - start) / duration, 1);
                const ease = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                el.textContent = Math.floor(ease * target).toLocaleString('bn-BD');
                if (progress < 1) requestAnimationFrame(step);
                else el.textContent = target.toLocaleString('bn-BD');
            };
            requestAnimationFrame(step);
        };
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => { if (entry.isIntersecting) { run(entry.target); observer.unobserve(entry.target); } });
        }, { threshold: 0.5 });
        counters.forEach(c => observer.observe(c));
    });
</script>

<style>
    @keyframes marquee {
        from { transform: translateX(0); }
        to   { transform: translateX(-33.333%); }
    }
    .marquee-strip {
        animation: marquee 35s linear infinite;
    }
    .marquee-strip:hover {
        animation-play-state: paused;
    }
</style>
@endpush
