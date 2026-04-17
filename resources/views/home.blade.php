@extends('layouts.app')

@section('title', 'রক্তদূত — রক্ত দিন, জীবন বাঁচান')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 1 — HERO (Listygo Inspired)
═══════════════════════════════════════════════════════════════ --}}
{{-- ═══════════════════════════════════════════════════════════════
     SECTION 1 — HERO (Light Theme Centered)
═══════════════════════════════════════════════════════════════ --}}
<section class="relative bg-gradient-to-b from-[#FFF5F5] to-white pt-20 pb-20 lg:pt-28 lg:pb-24 text-center" aria-label="হিরো সেকশন">
    
    {{-- Decorative Background Elements (optional subtle pattern) --}}
    <div class="absolute inset-0 opacity-40 pointer-events-none" style="background-image: radial-gradient(circle at 2px 2px, rgba(211,47,47,0.1) 1px, transparent 0); background-size: 40px 40px;"></div>

    <div class="relative z-10 mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 flex flex-col items-center">
        
        {{-- Top Pill --}}
        <div class="inline-flex items-center gap-2 bg-white border border-red-100 text-[#D32F2F] text-xs font-black px-5 py-2 rounded-full mb-8 shadow-sm">
            ইমার্জেন্সি ব্লাড ডোনেশন নেটওয়ার্ক
        </div>
        
        {{-- Headline --}}
        <h1 class="text-4xl sm:text-5xl lg:text-[4rem] font-black text-[#1e293b] leading-[1.15] mb-6 tracking-tight">
            জরুরি মুহূর্তে রক্তের সন্ধানে—
            <span class="block mt-2 text-[#D32F2F]">আমরা আছি আপনার পাশে</span>
        </h1>
        
        {{-- Subtitle --}}
        <p class="text-lg text-slate-600 mb-10 font-normal max-w-2xl mx-auto leading-relaxed">
            বাংলাদেশের সবচেয়ে নির্ভরযোগ্য NID-ভেরিফাইড ডোনার নেটওয়ার্ক — জরুরি মুহূর্তে সঠিক রক্ত, সঠিক সময়ে।
        </p>
        
        {{-- Buttons --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-8 w-full sm:w-auto">
            <button onclick="document.getElementById('search-section').scrollIntoView({behavior: 'smooth'})" class="w-full sm:w-auto bg-[#D32F2F] text-white font-black px-8 py-4 rounded-xl shadow-lg shadow-red-500/30 hover:bg-[#B71C1C] transition-transform hover:scale-105 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg> এখনই ডোনার খুঁজুন
            </button>
            <a href="{{ route('requests.create') }}" class="w-full sm:w-auto bg-white border-2 border-[#D32F2F] text-[#D32F2F] hover:bg-red-50 font-black px-8 py-3.5 rounded-xl transition-colors flex items-center justify-center gap-2 shadow-sm h-[56px]">
                জরুরি রক্তের অনুরোধ
            </a>
        </div>
        
        {{-- Mini Stats --}}
        <div class="flex flex-wrap justify-center items-center gap-4 sm:gap-6 text-xs sm:text-sm font-bold text-slate-600">
            <div class="flex items-center gap-2">
                <span class="bg-red-50 p-1.5 rounded-full text-[10px] sm:text-xs">🔒</span> ফোন নম্বর সর্বদা গোপন
            </div>
            <div class="hidden sm:block w-[1px] h-4 bg-slate-300"></div>
            <div class="flex items-center gap-2">
                <span class="bg-red-50 p-1.5 rounded-full text-[10px] sm:text-xs">💳</span> NID ভেরিফাইড ডোনার
            </div>
            <div class="hidden sm:block w-[1px] h-4 bg-slate-300"></div>
            <div class="flex items-center gap-2">
                <span class="bg-red-50 p-1.5 rounded-full text-[10px] sm:text-xs">📍</span> ৬৪ জেলায় সক্রিয়
            </div>
        </div>
        
    </div>
{{-- Search Bar (Inside Hero Section) --}}
<div id="search-section" class="relative z-20 mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 mt-10 lg:mt-14" x-data="{
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
    
    <div class="bg-white rounded-[2rem] shadow-[0_20px_50px_-12px_rgba(0,0,0,0.15)] border border-slate-100 p-6 sm:p-8">
        
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-[#D32F2F]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-900">দ্রুত ডোনার খুঁজুন</h3>
                <p class="text-xs font-bold text-slate-400 mt-0.5">রক্তের গ্রুপ ও এলাকা দিয়ে দ্রুত ডোনার খুঁজুন</p>
            </div>
        </div>

        <form action="{{ route('search') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            
            {{-- Division --}}
            <div class="lg:col-span-1">
                <div class="relative">
                    <select name="division_id" @change="fetchDistricts($event.target.value)" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3.5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-[#D32F2F]/20 focus:border-[#D32F2F] transition-all appearance-none cursor-pointer">
                        <option value="">বিভাগ নির্বাচন</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}">{{ $div->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- District --}}
            <div class="lg:col-span-1">
                <div class="relative">
                    <select name="district_id" @change="fetchUpazilas($event.target.value)" :disabled="districts.length === 0" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3.5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-[#D32F2F]/20 focus:border-[#D32F2F] transition-all appearance-none cursor-pointer disabled:opacity-50 disabled:bg-slate-50">
                        <option value="" x-text="loadingDistricts ? 'লোড হচ্ছে...' : (districts.length === 0 ? 'জেলা নির্বাচন' : 'সব জেলা')"></option>
                        <template x-for="d in districts" :key="d.id">
                            <option :value="d.id" x-text="d.name"></option>
                        </template>
                    </select>
                </div>
            </div>

            {{-- Upazila Placeholder --}}
            <div class="lg:col-span-1">
                <div class="relative">
                    <select name="upazila_id" :disabled="upazilas.length === 0" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3.5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-[#D32F2F]/20 focus:border-[#D32F2F] transition-all appearance-none cursor-pointer disabled:opacity-50 disabled:bg-slate-50">
                        <option value="" x-text="loadingUpazilas ? 'লোড হচ্ছে...' : (upazilas.length === 0 ? 'উপজেলা / এরিয়া' : 'সব উপজেলা / এরিয়া')"></option>
                        <template x-for="u in upazilas" :key="u.id">
                            <option :value="u.id" x-text="u.name"></option>
                        </template>
                    </select>
                </div>
            </div>

            {{-- Blood Group --}}
            <div class="lg:col-span-1">
                <div class="relative">
                    <select name="blood_group" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3.5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-[#D32F2F]/20 focus:border-[#D32F2F] transition-all appearance-none cursor-pointer">
                        <option value="">রক্তের গ্রুপ</option>
                        @foreach(\App\Enums\BloodGroup::cases() as $bg)
                            <option value="{{ $bg->value }}">{{ $bg->value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Button --}}
            <div class="lg:col-span-1">
                <button type="submit" class="w-full bg-[#D32F2F] hover:bg-[#B71C1C] text-white rounded-xl px-4 py-3.5 text-sm font-black transition-colors flex items-center justify-center gap-2 h-[50px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    সার্চ করুন
                </button>
            </div>
        </form>
    </div>
</div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 2 — TRUST BAND (Dark background style)
═══════════════════════════════════════════════════════════════ --}}
<section class="bg-[#1e1e24] pt-16 lg:pt-20 pb-14 relative z-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 text-center divide-y sm:divide-y-0 sm:divide-x divide-white/10">
            
            <div class="pt-4 sm:pt-0">
                <div class="text-4xl lg:text-5xl font-black text-white mb-2"><span class="counter" data-target="{{ $totalDonors }}">0</span><span class="text-[#FF385C]">+</span></div>
                <div class="text-sm font-bold text-slate-400">নিবন্ধিত ডোনার</div>
            </div>

            <div class="pt-8 sm:pt-0">
                <div class="text-4xl lg:text-5xl font-black text-white mb-2"><span class="counter" data-target="{{ $verifiedDonors }}">0</span><span class="text-[#FF385C]">+</span></div>
                <div class="text-sm font-bold text-slate-400">ভেরিফাইড ডোনার</div>
            </div>

            <div class="pt-8 sm:pt-0">
                <div class="text-4xl lg:text-5xl font-black text-white mb-2"><span class="counter" data-target="{{ $totalDonations }}">0</span><span class="text-[#FF385C]">+</span></div>
                <div class="text-sm font-bold text-slate-400">সফল রক্তদান</div>
            </div>

        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 3 — EMERGENCY FEED (Listygo "Most Popular" style)
═══════════════════════════════════════════════════════════════ --}}
<section class="py-24 bg-white" aria-label="জরুরি রক্তের অনুরোধ">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        {{-- Section Header --}}
        <div class="text-center max-w-2xl mx-auto mb-16">
            <p class="text-sm font-extrabold text-[#FF385C] uppercase tracking-widest mb-3">লাইভ আপডেট</p>
            <h2 class="text-3xl lg:text-4xl font-black text-slate-900 mb-5">জরুরি রক্তের অনুরোধ</h2>
            <p class="text-slate-500 font-medium">যেসব রোগীর এই মুহূর্তে জরুরি ভিত্তিতে রক্তের প্রয়োজন। আপনার একটু সাহায্য বাঁচাতে পারে একটি জীবন।</p>
        </div>

        @if($homeRequests->isNotEmpty())
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($homeRequests as $req)
                    @php
                        $urgencyVal   = $req->urgency?->value ?? 'normal';
                        $urgencyLabel = match($urgencyVal) { 'emergency' => 'অতি জরুরি', 'urgent' => 'জরুরি', default => 'সাধারণ' };
                        $urgencyBg = match($urgencyVal) {
                            'emergency' => 'bg-[#FF385C] text-white',
                            'urgent'    => 'bg-amber-500 text-white',
                            default     => 'bg-slate-800 text-white',
                        };
                        $bgGroup  = $req->blood_group?->value ?? '?';
                        $bagsNeeded = max((int) ($req->bags_needed ?? 1), 1);
                        $acceptedCount = (int) ($req->accepted_responses_count ?? 0);
                        $claimedCount = (int) ($req->claimed_verifications_count ?? 0);
                        $verifiedCount = (int) ($req->verified_verifications_count ?? 0);
                        $requestStatus = strtolower((string) ($req->status ?? 'pending'));

                        [$statusLabel, $statusCls] = match (true) {
                            $requestStatus === 'expired' => ['বাতিল', 'bg-rose-50 text-rose-700 border-rose-200'],
                            $verifiedCount > 0 || $requestStatus === 'fulfilled' => ['সফল (যাচাইকৃত)', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                            $claimedCount > 0 => ['ক্লেইম রিভিউতে', 'bg-amber-50 text-amber-700 border-amber-200'],
                            $acceptedCount > 0 => ['ডোনার পাওয়া গেছে', 'bg-indigo-50 text-indigo-700 border-indigo-200'],
                            default => ['চলমান', 'bg-sky-50 text-sky-700 border-sky-200'],
                        };
                    @endphp
                    <div class="bg-white rounded-[2rem] overflow-hidden shadow-[0_4px_20px_-10px_rgba(0,0,0,0.08)] hover:shadow-[0_20px_40px_-10px_rgba(0,0,0,0.12)] transition-all duration-300 group border border-slate-100 flex flex-col">
                        
                        {{-- Top Banner Image area --}}
                        <div class="h-36 bg-slate-50 relative overflow-hidden flex items-center justify-center">
                            {{-- Placeholder pattern --}}
                            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#e5e7eb_2px,transparent_2px)] [background-size:16px_16px]"></div>
                            
                            {{-- Big Blood Group Watermark --}}
                            <div class="text-[5rem] font-black text-slate-200/50 select-none z-0 transform -rotate-12">{{ $bgGroup }}</div>
                            
                            {{-- Badges --}}
                            <div class="absolute top-5 left-5 flex gap-2 z-10">
                                <span class="{{ $urgencyBg }} text-xs font-bold px-3.5 py-1.5 rounded-full flex items-center gap-1.5 shadow-sm">
                                    @if($urgencyVal === 'emergency') <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> @endif
                                    {{ $urgencyLabel }}
                                </span>
                                <span class="bg-white/95 text-slate-700 text-xs font-bold px-3.5 py-1.5 rounded-full border border-slate-200 shadow-sm">
                                    {{ \App\Support\BanglaDate::digits((string) $bagsNeeded) }} ব্যাগ
                                </span>
                            </div>
                            
                            <div class="absolute top-5 right-5 bg-white text-[#FF385C] font-black text-sm px-4 py-1.5 rounded-full shadow-sm border border-slate-100 z-10">
                                {{ $bgGroup }}
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="p-8 flex-1 flex flex-col">
                            <h3 class="text-xl font-black text-slate-900 mb-4 truncate group-hover:text-[#FF385C] transition-colors" title="{{ $req->patient_name }}">
                                {{ $req->patient_name ?? 'রোগী' }}
                            </h3>
                            
                            <div class="space-y-3 mb-6 flex-1">
                                <p class="text-sm font-semibold text-slate-600 flex items-start gap-3">
                                    <span class="w-6 h-6 rounded-full bg-red-50 flex items-center justify-center shrink-0 text-red-500">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </span>
                                    <span class="truncate mt-0.5">{{ $req->hospital ?? 'হাসপাতাল উল্লেখ নেই' }}</span>
                                </p>
                                <p class="text-sm font-semibold text-slate-600 flex items-start gap-3">
                                    <span class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center shrink-0 text-slate-500">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    </span>
                                    <span class="truncate mt-0.5">{{ $req->district?->name ?? '' }}{{ $req->upazila?->name ? ', '.$req->upazila->name : '' }}</span>
                                </p>
                                <p class="text-sm font-bold text-slate-900 flex items-center gap-3">
                                    <span class="w-6 h-6 rounded-full bg-orange-50 flex items-center justify-center shrink-0 text-orange-500">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </span>
                                    <span class="mt-0.5">{{ \App\Support\BanglaDate::absolute($req->needed_at) }}</span>
                                </p>
                                <p class="text-xs font-semibold text-slate-500 flex items-center gap-3">
                                    <span class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center shrink-0 text-slate-500">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"/></svg>
                                    </span>
                                    <span class="mt-0.5">পোস্ট {{ \App\Support\BanglaDate::relative($req->created_at) }}</span>
                                </p>
                            </div>

                            <hr class="border-slate-100 mb-5">
                            
                            {{-- Footer actions --}}
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex h-7 items-center rounded-full border px-3 text-xs font-bold {{ $statusCls }}">
                                        {{ $statusLabel }}
                                    </span>
                                    <div class="text-xs font-bold text-slate-500 flex items-center gap-1.5">
                                        <span class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </span>
                                        {{ \App\Support\BanglaDate::digits((string) $acceptedCount) }} সাড়া
                                    </div>
                                </div>
                                
                                <a href="{{ route('requests.show', $req) }}" class="text-sm font-black text-[#FF385C] hover:text-white border-2 border-[#FF385C] hover:bg-[#FF385C] px-5 py-2 rounded-full transition-colors flex items-center gap-1.5">
                                    বিস্তারিত <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-12 text-center">
                <a href="{{ route('public.requests.index') }}" class="inline-flex items-center justify-center gap-2 bg-[#FF385C] hover:bg-[#E31C5F] text-white font-black text-sm px-8 py-4 rounded-full transition-transform hover:scale-105 shadow-lg shadow-red-500/30">
                    সব অনুরোধ দেখুন 
                </a>
            </div>
        @else
            <div class="text-center py-10 bg-slate-50 rounded-3xl border border-slate-100">
                <p class="text-slate-500 font-medium">বর্তমানে কোনো জরুরি অনুরোধ নেই।</p>
            </div>
        @endif
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 4 — HOW IT WORKS (Listygo "Explore" style split view)
═══════════════════════════════════════════════════════════════ --}}
<section class="py-24 bg-slate-50 overflow-hidden relative">
    
    {{-- Background decorations --}}
    <div class="absolute right-0 top-0 w-1/3 h-full bg-[#FF385C]/5 rounded-l-[100px] z-0"></div>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid lg:grid-cols-2 gap-16 lg:gap-24 items-center">
            
            {{-- Left: Image composition --}}
            <div class="relative order-2 lg:order-1">
                <div class="bg-white rounded-[3rem] p-4 sm:p-6 relative z-10 w-4/5 ml-auto shadow-[0_20px_50px_-10px_rgba(0,0,0,0.1)] border border-slate-100">
                    <img src="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Hospital" class="rounded-[2rem] w-full h-auto object-cover">
                </div>
                <div class="absolute top-20 left-0 w-2/3 z-20 shadow-2xl rounded-[2.5rem] overflow-hidden border-8 border-white">
                    <img src="https://images.unsplash.com/photo-1551076805-e1869033e561?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Mobile UI" class="w-full h-auto object-cover">
                </div>
                
                {{-- Floating badge --}}
                <div class="absolute bottom-10 -left-6 bg-white p-5 rounded-2xl shadow-[0_10px_30px_-10px_rgba(0,0,0,0.15)] z-30 flex items-center gap-4 border border-slate-50 animate-[float_4s_ease-in-out_infinite]">
                    <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center text-green-600 font-black">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="text-lg font-black text-slate-900">ম্যাচিং সিস্টেম</p>
                        <p class="text-sm font-bold text-slate-500">স্মার্ট ও দ্রুত</p>
                    </div>
                </div>
            </div>

            {{-- Right: Content --}}
            <div class="order-1 lg:order-2">
                <p class="text-sm font-extrabold text-[#FF385C] uppercase tracking-widest mb-3">কীভাবে কাজ করে</p>
                <h2 class="text-3xl lg:text-5xl font-black text-slate-900 mb-6 leading-tight">খুব সহজেই রক্ত<br>দিন বা নিন</h2>
                <p class="text-lg font-medium text-slate-500 mb-10 leading-relaxed">রক্তদূত এমন একটি প্ল্যাটফর্ম যা রক্তদাতা এবং গ্রহীতার মধ্যে সরাসরি সংযোগ স্থাপন করে। কোনো মধ্যস্বত্বভোগী নেই, সম্পূর্ণ বিনামূল্যে এবং সুরক্ষিত।</p>
                
                <ul class="space-y-8 mb-12">
                    <li class="flex items-start gap-5">
                        <div class="mt-1 w-10 h-10 rounded-full bg-red-100 text-[#FF385C] flex items-center justify-center shrink-0">
                            <span class="font-black">১</span>
                        </div>
                        <div>
                            <h4 class="text-xl font-black text-slate-900 mb-2">প্রোফাইল তৈরি করুন</h4>
                            <p class="text-slate-500 font-medium">আপনার রক্তের গ্রুপ, ঠিকানা এবং অন্যান্য তথ্য দিয়ে ফ্রি প্রোফাইল তৈরি করুন।</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-5">
                        <div class="mt-1 w-10 h-10 rounded-full bg-red-100 text-[#FF385C] flex items-center justify-center shrink-0">
                            <span class="font-black">২</span>
                        </div>
                        <div>
                            <h4 class="text-xl font-black text-slate-900 mb-2">ডোনার খুঁজুন</h4>
                            <p class="text-slate-500 font-medium">আমাদের স্মার্ট ফিল্টার দিয়ে আপনার নির্দিষ্ট এলাকায় উপযুক্ত ডোনার খুঁজে বের করুন।</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-5">
                        <div class="mt-1 w-10 h-10 rounded-full bg-red-100 text-[#FF385C] flex items-center justify-center shrink-0">
                            <span class="font-black">৩</span>
                        </div>
                        <div>
                            <h4 class="text-xl font-black text-slate-900 mb-2">জীবন বাঁচান</h4>
                            <p class="text-slate-500 font-medium">সরাসরি কল করুন বা প্ল্যাটফর্মের মাধ্যমে সাড়া দিন এবং জীবন বাঁচানোর এই মহৎ কাজে শরিক হোন।</p>
                        </div>
                    </li>
                </ul>
                
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 bg-[#FF385C] hover:bg-[#E31C5F] text-white font-black text-sm px-8 py-4 rounded-full transition-transform hover:scale-105 shadow-lg shadow-red-500/30">
                    আজই যুক্ত হোন <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
            
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 5 — TOP DONORS (Podium Style)
═══════════════════════════════════════════════════════════════ --}}
<section class="py-24 bg-[#FAFAFA]" aria-label="শীর্ষ রক্তদাতা">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-2xl mx-auto mb-16">
            <div class="inline-flex items-center justify-center gap-1.5 bg-red-50 text-[#FF385C] font-extrabold text-[11px] px-4 py-1.5 rounded-full mb-4 shadow-sm border border-red-100">
                <span>🦸</span> রিয়েল লাইফ সুপারহিরো
            </div>
            <h2 class="text-3xl lg:text-4xl font-black text-slate-800 mb-4 tracking-tight">আমাদের সেরা রক্তদাতাগণ</h2>
            <p class="text-slate-500 font-medium">প্ল্যাটফর্মে যারা সবচেয়ে বেশি মানুষের জীবন বাঁচিয়েছেন, তাদের প্রতি আমাদের কৃতজ্ঞতা।</p>
        </div>

        @if($topDonors->isNotEmpty())
            @php
                $orderedDonors = collect();
                // 2nd Place (Left)
                if($topDonors->has(1)) $orderedDonors->push(['donor' => $topDonors[1], 'rank' => 2, 'emoji' => '🥈', 'label' => '২য় স্থান']);
                // 1st Place (Center)
                if($topDonors->has(0)) $orderedDonors->push(['donor' => $topDonors[0], 'rank' => 1, 'emoji' => '🥇', 'label' => '১ম স্থান']);
                // 3rd Place (Right)
                if($topDonors->has(2)) $orderedDonors->push(['donor' => $topDonors[2], 'rank' => 3, 'emoji' => '🥉', 'label' => '৩য় স্থান']);
            @endphp

            <div class="flex flex-col lg:flex-row items-end justify-center gap-6 lg:gap-8 mt-12 px-4">
                @foreach($orderedDonors as $item)
                    @php
                        $d = $item['donor'];
                        $rank = $item['rank'];
                        $initial = mb_strtoupper(mb_substr($d->name, 0, 1));
                        
                        $isFirst = $rank === 1;
                        
                        // Dynamic Classes based on rank
                        $cardClasses = $isFirst 
                            ? "border-[#FACC15] shadow-[0_15px_50px_-10px_rgba(251,191,36,0.3)] z-10 scale-100 lg:scale-110 pb-8 pt-12" 
                            : "border-slate-100 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.08)] scale-100 pb-6 pt-10 mt-8 lg:mt-0";
                            
                        $avatarBorder = match($rank) {
                            1 => "border-[#FACC15] text-[#FACC15]",
                            2 => "border-slate-300 text-slate-400",
                            3 => "border-orange-300 text-orange-400",
                            default => "border-slate-200"
                        };
                    @endphp
                    
                    <div class="bg-white rounded-[2rem] px-6 w-full max-w-sm mx-auto lg:mx-0 flex flex-col items-center relative border-2 {{ $cardClasses }} transition-transform duration-300 hover:-translate-y-2">
                        
                        {{-- Floating Rank Badge --}}
                        <div class="absolute -top-4 bg-slate-900 text-white text-xs font-black px-5 py-1.5 rounded-full flex items-center gap-1.5 shadow-md border-[3px] border-white">
                            <span>{{ $item['emoji'] }}</span> {{ $item['label'] }}
                        </div>
                        
                        {{-- Avatar Circle --}}
                        <div class="w-24 h-24 rounded-full border-[3px] {{ $avatarBorder }} flex items-center justify-center text-4xl font-black mb-5 bg-white shadow-sm">
                            {{ $initial }}
                        </div>
                        
                        {{-- Name --}}
                        <h3 class="text-xl font-black text-slate-900 text-center mb-3 truncate w-full">{{ $d->name }}</h3>
                        
                        {{-- Blood Group Pill --}}
                        @if($d->blood_group)
                        <div class="bg-red-50 text-[#FF385C] font-black text-[10px] px-3 py-1 rounded-md mb-6 uppercase tracking-widest border border-red-100">
                            {{ $d->blood_group?->value ?? $d->blood_group }} ডোনার
                        </div>
                        @else
                        <div class="h-6 mb-6"></div>
                        @endif
                        
                        {{-- Stats Box --}}
                        <div class="w-full bg-[#F8FAFC] border border-slate-100 rounded-2xl p-4 flex items-center divide-x divide-slate-200 mb-6">
                            <div class="flex-1 text-center">
                                <div class="text-xl font-black text-slate-800">{{ $d->total_verified_donations ?? 0 }}</div>
                                <div class="text-[10px] font-bold text-slate-400 mt-1">রক্তদান</div>
                            </div>
                            <div class="flex-1 text-center">
                                <div class="text-xl font-black text-slate-800">{{ number_format($d->points ?? 0) }}</div>
                                <div class="text-[10px] font-bold text-slate-400 mt-1">পয়েন্ট</div>
                            </div>
                        </div>
                        
                        {{-- Medals/Badges (Tiny versions) --}}
                        <div class="flex gap-2 justify-center h-6">
                            @if($d->badges->isNotEmpty())
                                @foreach($d->badges->take(3) as $badge)
                                    @php $bd = \App\Services\GamificationService::getBadgeDisplayData($badge->name); @endphp
                                    <span class="text-lg drop-shadow-sm hover:scale-110 transition-transform cursor-help" title="{{ $bd['bn'] }}">{{ $bd['emoji'] }}</span>
                                @endforeach
                            @else
                                <span class="text-lg opacity-30 grayscale">🎖️</span>
                                <span class="text-lg opacity-30 grayscale">🎖️</span>
                                <span class="text-lg opacity-30 grayscale">🎖️</span>
                            @endif
                        </div>
                        
                    </div>
                @endforeach
            </div>
            
            <div class="mt-16 text-center">
                <a href="{{ route('leaderboard') }}" class="inline-flex items-center justify-center gap-2 bg-white border-2 border-slate-100 hover:border-slate-300 text-slate-700 font-bold text-sm px-8 py-4 rounded-full transition-all shadow-sm hover:shadow-md group">
                    সম্পূর্ণ লিডারবোর্ড দেখুন 
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        @endif
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 6 — BLOG (Listygo Latest Blog style)
═══════════════════════════════════════════════════════════════ --}}
<section class="py-24 bg-slate-50 border-t border-slate-200/50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-2xl mx-auto mb-16">
            <p class="text-sm font-extrabold text-[#FF385C] uppercase tracking-widest mb-3">সংবাদ ও নিবন্ধ</p>
            <h2 class="text-3xl lg:text-4xl font-black text-slate-900 mb-4">সর্বশেষ ব্লগ পোস্ট</h2>
        </div>

        @if($recentPosts->isNotEmpty())
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($recentPosts as $post)
                    <div class="group bg-white rounded-[2rem] p-4 border border-slate-100 shadow-sm hover:shadow-[0_10px_40px_-10px_rgba(0,0,0,0.1)] transition-all duration-300">
                        <div class="rounded-[1.5rem] overflow-hidden mb-6 relative h-56 bg-slate-100">
                            {{-- Placeholder image --}}
                            <div class="absolute inset-0 bg-gradient-to-br from-slate-200 to-slate-300 group-hover:scale-105 transition-transform duration-500"></div>
                            
                            @if($post->category?->name)
                                <div class="absolute top-4 left-4 bg-[#FF385C] text-white text-xs font-black tracking-wider px-4 py-2 rounded-full shadow-md z-10">
                                    {{ $post->category->name }}
                                </div>
                            @endif
                        </div>
                        
                        <div class="px-2">
                            <div class="flex items-center gap-4 text-xs font-bold text-slate-400 mb-3">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ optional($post->published_at ?? $post->created_at)->format('d M, Y') }}
                                </span>
                            </div>
                            
                            <h3 class="text-xl font-black text-slate-900 mb-4 group-hover:text-[#FF385C] transition-colors line-clamp-2 leading-snug">
                                <a href="{{ route('blog.show', $post->slug) }}" class="focus:outline-none focus:underline">
                                    {{ $post->title }}
                                </a>
                            </h3>
                            
                            <a href="{{ route('blog.show', $post->slug) }}" class="inline-flex items-center gap-2 text-sm font-black text-slate-700 group-hover:text-[#FF385C] transition-colors">
                                আরও পড়ুন <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-12 text-center">
                <a href="{{ route('blog.index') }}" class="inline-block border-2 border-slate-200 hover:border-slate-900 text-slate-700 hover:text-white hover:bg-slate-900 font-black text-sm px-8 py-4 rounded-full transition-all">
                    সব পোস্ট দেখুন
                </a>
            </div>
        @else
            <div class="text-center py-10">
                <p class="text-slate-500 font-medium">এখনো কোনো নিবন্ধ প্রকাশিত হয়নি।</p>
            </div>
        @endif
        
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 7 — CTA BANNER (Centered Design)
═══════════════════════════════════════════════════════════════ --}}
<section class="bg-[#BE1B21] relative py-12 lg:py-14 overflow-hidden text-center">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 relative z-10 flex flex-col items-center">
        
        {{-- Heart Icon --}}
        <div class="w-16 h-16 rounded-full border border-white/30 flex items-center justify-center mb-6 bg-white/10">
            <svg class="w-8 h-8 text-white fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
        </div>

        {{-- Headline --}}
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white mb-5 tracking-tight">আজই একটি জীবন বাঁচান</h2>
        
        {{-- Subtitle --}}
        <p class="text-white/90 font-medium text-base md:text-lg max-w-2xl mx-auto mb-10 leading-relaxed">
            আপনার একটি রক্তদান তিনটি প্রাণ বাঁচাতে পারে। বাংলাদেশের সবচেয়ে নির্ভরযোগ্য ভেরিফায়েড ডোনার<br class="hidden md:block"> নেটওয়ার্কে এখনই যোগ দিন।
        </p>
        
        {{-- Buttons --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-10 w-full sm:w-auto">
            <a href="{{ route('requests.create') }}" class="w-full sm:w-auto bg-white text-[#BE1B21] hover:bg-slate-50 font-black px-8 py-3.5 rounded-xl transition-all shadow-lg flex items-center justify-center gap-2 text-[15px]">
                রক্তের রিকোয়েস্ট করুন
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
            <a href="{{ route('search') }}" class="w-full sm:w-auto bg-white/10 hover:bg-white/20 border border-white/30 text-white font-black px-8 py-3.5 rounded-xl transition-all flex items-center justify-center text-[15px]">
                ডোনার সার্চ করুন
            </a>
        </div>
        
        {{-- Security Info --}}
        <div class="text-center">
            <div class="flex items-center justify-center gap-2 text-white text-sm font-bold mb-2.5">
                <svg class="w-4 h-4 text-[#FACC15]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C9.243 2 7 4.243 7 7v3H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2v-8a2 2 0 00-2-2h-1V7c0-2.757-2.243-5-5-5zm-3 5c0-1.654 1.346-3 3-3s3 1.346 3 3v3H9V7z"/></svg>
                মোবাইল নম্বর কখনো পাবলিক করা হয় না।
            </div>
            <div class="text-white/70 text-xs font-semibold">
                বিনামূল্যে &middot; NID ভেরিফায়েড নেটওয়ার্ক &middot; ৬৪ জেলায় সক্রিয়
            </div>
        </div>

    </div>
</section>

@endsection

@push('scripts')
<script>
    // Counter animation for Trust Band
    document.addEventListener('DOMContentLoaded', () => {
        const counters = document.querySelectorAll('.counter[data-target]');
        if (!counters.length) return;
        
        const run = (el) => {
            const target = parseInt(el.dataset.target, 10) || 0;
            const duration = 2500;
            const start = performance.now();
            
            const step = (now) => {
                const progress = Math.min((now - start) / duration, 1);
                // Ease out Expo
                const ease = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                el.textContent = Math.floor(ease * target).toLocaleString('bn-BD');
                
                if (progress < 1) {
                    requestAnimationFrame(step);
                } else {
                    el.textContent = target.toLocaleString('bn-BD');
                }
            };
            requestAnimationFrame(step);
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    run(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        counters.forEach(c => observer.observe(c));
    });
</script>

<style>
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }
</style>
@endpush
