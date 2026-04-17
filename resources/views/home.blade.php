@extends('layouts.app')

@section('title', 'রক্তদূত — রক্ত দিন, জীবন বাঁচান')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 1 — HERO (Listygo Inspired)
═══════════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden bg-gradient-to-br from-[#fff5f5] via-white to-[#fff0f0] pt-16 pb-20 lg:pt-24 lg:pb-28" aria-label="হিরো সেকশন">
    
    {{-- Decorative Background Elements --}}
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        {{-- City/Building outline placeholder at bottom like Listygo --}}
        <div class="absolute bottom-0 right-0 w-full md:w-1/2 opacity-[0.03]">
            <svg viewBox="0 0 100 20" preserveAspectRatio="none" class="w-full h-32 fill-current text-slate-900">
                <path d="M0,20 L0,10 L5,10 L5,5 L10,5 L10,15 L15,15 L15,8 L20,8 L20,20 Z M25,20 L25,12 L30,12 L30,20 Z M35,20 L35,5 L45,5 L45,20 Z M50,20 L50,15 L60,15 L60,20 Z M65,20 L65,10 L75,10 L75,20 Z M80,20 L80,5 L90,5 L90,20 Z M95,20 L95,10 L100,10 L100,20 Z"></path>
            </svg>
        </div>
    </div>

    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-8 items-center">
            
            {{-- Left Content --}}
            <div class="max-w-2xl">
                <p class="text-sm font-bold text-[#FF385C] uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#FF385C]"></span>
                    বাংলাদেশের ভেরিফায়েড ব্লাড নেটওয়ার্ক
                </p>
                <h1 class="text-4xl sm:text-5xl lg:text-[3.5rem] font-black text-slate-900 leading-[1.1] mb-6">
                    সঠিক ডোনার খুঁজুন,<br>
                    <span class="text-[#FF385C] relative">
                        জীবন বাঁচান
                        <svg class="absolute w-full h-3 -bottom-1 left-0 text-red-200" viewBox="0 0 100 10" preserveAspectRatio="none"><path d="M0,5 Q50,10 100,5" stroke="currentColor" stroke-width="4" fill="none"/></svg>
                    </span>
                </h1>
                <p class="text-lg text-slate-500 mb-8 font-medium max-w-lg">
                    জরুরি মুহূর্তে আপনার নির্দিষ্ট এলাকায় রক্তের সন্ধানে। মাত্র কয়েক ক্লিকে খুঁজে নিন ভেরিফাইড রক্তদাতা।
                </p>
                
                {{-- Horizontal Search Form (Listygo style) --}}
                <div x-data="{
                        districts: [],
                        upazilas: [],
                        loadingDistricts: false,
                        fetchDistricts(divisionId) {
                            this.districts = [];
                            this.upazilas = [];
                            if (!divisionId) return;
                            this.loadingDistricts = true;
                            fetch('/ajax/districts/' + divisionId)
                                .then(r => r.json())
                                .then(data => { this.districts = data; this.loadingDistricts = false; })
                                .catch(() => { this.loadingDistricts = false; });
                        }
                    }" 
                    class="bg-white p-2 rounded-3xl sm:rounded-full shadow-[0_10px_40px_-10px_rgba(0,0,0,0.08)] mt-6 border border-slate-100 flex flex-col sm:flex-row items-center relative z-20">
                    
                    <form action="{{ route('search') }}" method="GET" class="flex flex-col sm:flex-row w-full items-center divide-y sm:divide-y-0 sm:divide-x divide-slate-100">
                        
                        {{-- Blood Group --}}
                        <div class="flex-1 w-full px-5 py-3 sm:py-2">
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-1">রক্তের গ্রুপ</label>
                            <select name="blood_group" class="w-full bg-transparent border-none p-0 text-sm font-black text-slate-800 focus:ring-0 cursor-pointer">
                                <option value="">সব গ্রুপ</option>
                                @foreach(\App\Enums\BloodGroup::cases() as $bg)
                                    <option value="{{ $bg->value }}">{{ $bg->value }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Division --}}
                        <div class="flex-1 w-full px-5 py-3 sm:py-2">
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-1">বিভাগ</label>
                            <select name="division_id" @change="fetchDistricts($event.target.value)" class="w-full bg-transparent border-none p-0 text-sm font-black text-slate-800 focus:ring-0 cursor-pointer">
                                <option value="">সব বিভাগ</option>
                                @foreach($divisions as $div)
                                    <option value="{{ $div->id }}">{{ $div->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- District --}}
                        <div class="flex-1 w-full px-5 py-3 sm:py-2">
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-1">জেলা</label>
                            <select name="district_id" :disabled="districts.length === 0" class="w-full bg-transparent border-none p-0 text-sm font-black text-slate-800 focus:ring-0 cursor-pointer disabled:opacity-50">
                                <option value="" x-text="loadingDistricts ? 'লোড হচ্ছে...' : (districts.length === 0 ? 'বিভাগ বাছুন' : 'সব জেলা')"></option>
                                <template x-for="d in districts" :key="d.id">
                                    <option :value="d.id" x-text="d.name"></option>
                                </template>
                            </select>
                        </div>

                        {{-- Button --}}
                        <div class="w-full sm:w-auto p-2 sm:p-0 sm:pl-2 shrink-0">
                            <button type="submit" class="w-full sm:w-auto bg-[#FF385C] hover:bg-[#E31C5F] text-white rounded-2xl sm:rounded-full px-8 py-4 text-sm font-black transition-transform hover:scale-105 whitespace-nowrap shadow-lg shadow-red-500/30 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                সার্চ করুন
                            </button>
                        </div>
                    </form>
                </div>
                
                {{-- Quick links --}}
                <div class="mt-8 flex flex-wrap items-center gap-3 text-sm">
                    <span class="text-slate-400 font-bold text-xs uppercase tracking-widest">জনপ্রিয়:</span>
                    <a href="{{ route('search', ['blood_group' => 'O+']) }}" class="bg-white border border-slate-200 px-4 py-1.5 rounded-full text-slate-700 font-black hover:border-[#FF385C] hover:text-[#FF385C] transition-colors shadow-sm">O+ পজিটিভ</a>
                    <a href="{{ route('search', ['blood_group' => 'A+']) }}" class="bg-white border border-slate-200 px-4 py-1.5 rounded-full text-slate-700 font-black hover:border-[#FF385C] hover:text-[#FF385C] transition-colors shadow-sm">A+ পজিটিভ</a>
                    <a href="{{ route('search', ['blood_group' => 'B+']) }}" class="bg-white border border-slate-200 px-4 py-1.5 rounded-full text-slate-700 font-black hover:border-[#FF385C] hover:text-[#FF385C] transition-colors shadow-sm">B+ পজিটিভ</a>
                </div>

            </div>

            {{-- Right Image Collage (Listygo style) --}}
            <div class="hidden lg:block relative h-[550px] w-full">
                {{-- Decorative blobs --}}
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[400px] h-[400px] bg-red-100/50 rounded-full blur-3xl z-0"></div>
                
                {{-- Main Left Tall Image --}}
                <div class="absolute right-[50%] top-10 w-[240px] h-[340px] rounded-[100px] border-8 border-white shadow-2xl overflow-hidden z-10 animate-[float_6s_ease-in-out_infinite]">
                    <img src="https://images.unsplash.com/photo-1538108149393-cebb47cdf141?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Blood Donation" class="w-full h-full object-cover">
                </div>
                
                {{-- Top Right Circle --}}
                <div class="absolute right-[5%] top-0 w-[200px] h-[200px] rounded-full border-8 border-white shadow-2xl overflow-hidden z-20 animate-[float_7s_ease-in-out_infinite_reverse]">
                    <img src="https://images.unsplash.com/photo-1579684385127-1ef15d508118?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Medical Lab" class="w-full h-full object-cover">
                </div>
                
                {{-- Bottom Right Circle --}}
                <div class="absolute right-[10%] bottom-16 w-[220px] h-[220px] rounded-full border-8 border-white shadow-2xl overflow-hidden z-30 animate-[float_5s_ease-in-out_infinite]">
                    <img src="https://images.unsplash.com/photo-1615461066841-6116e61058f4?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Doctor" class="w-full h-full object-cover object-top">
                </div>
                
                {{-- Accent Icon --}}
                <div class="absolute top-20 right-[55%] text-[#FF385C] animate-pulse">
                    <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </div>

                {{-- Floating Stat Box --}}
                <div class="absolute bottom-32 left-[5%] bg-white px-5 py-4 rounded-2xl shadow-xl flex items-center gap-4 z-40 border border-slate-50">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <div class="text-xs text-slate-400 font-extrabold uppercase tracking-wider">ভেরিফাইড ডোনার</div>
                        <div class="text-2xl font-black text-slate-900">{{ number_format($verifiedDonors) }}+</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     SECTION 2 — TRUST BAND (Dark background style)
═══════════════════════════════════════════════════════════════ --}}
<section class="bg-[#1e1e24] py-14 relative z-20">
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
                                    <span class="mt-0.5">{{ $req->needed_at ? $req->needed_at->format('d M, Y (h:i A)') : 'যত দ্রুত সম্ভব' }}</span>
                                </p>
                            </div>

                            <hr class="border-slate-100 mb-5">
                            
                            {{-- Footer actions --}}
                            <div class="flex items-center justify-between">
                                <div class="text-xs font-bold text-slate-500 flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </span>
                                    {{ $req->accepted_responses_count ?? 0 }} সাড়া
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
     SECTION 7 — BOTTOM BANNER (Listygo App Banner style)
═══════════════════════════════════════════════════════════════ --}}
<section class="bg-[#FF385C] relative overflow-hidden">
    {{-- Background pattern --}}
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 32px 32px;"></div>
    <div class="absolute -top-64 -right-64 w-[500px] h-[500px] bg-white opacity-5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-64 -left-64 w-[500px] h-[500px] bg-black opacity-10 rounded-full blur-3xl pointer-events-none"></div>
    
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-24 relative z-10">
        <div class="grid md:grid-cols-2 items-center gap-12">
            <div class="text-white text-center md:text-left">
                <p class="text-sm font-extrabold text-red-200 uppercase tracking-widest mb-3">সবসময় সাথে থাকুন</p>
                <h2 class="text-4xl lg:text-5xl font-black mb-6 leading-tight">জরুরি মুহূর্তে<br>আপনার পাশে</h2>
                <p class="text-red-100 font-medium mb-10 text-lg max-w-md mx-auto md:mx-0">আমাদের প্ল্যাটফর্ম মোবাইল থেকেও সমানভাবে ব্যবহারযোগ্য। যেকোনো সময়, যেকোনো স্থানে ডোনার খুঁজুন।</p>
                
                <div class="flex flex-wrap justify-center md:justify-start gap-4">
                    <a href="{{ route('search') }}" class="bg-white text-[#FF385C] font-black px-8 py-4 rounded-full shadow-xl hover:shadow-2xl hover:bg-slate-50 transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        ডোনার খুঁজুন
                    </a>
                    <a href="{{ route('register') }}" class="bg-slate-900 text-white font-black px-8 py-4 rounded-full shadow-xl hover:shadow-2xl hover:bg-black transition-all">
                        অ্যাকাউন্ট খুলুন
                    </a>
                </div>
            </div>
            
            <div class="hidden md:flex justify-center relative">
                {{-- Mockup image placeholder --}}
                <div class="w-72 h-[500px] bg-white rounded-[2.5rem] border-8 border-slate-900 shadow-2xl mx-auto -mb-40 relative overflow-hidden transform rotate-6 hover:rotate-0 transition-transform duration-500">
                    <div class="absolute top-0 inset-x-0 h-7 bg-slate-900 rounded-b-2xl w-1/2 mx-auto z-20"></div>
                    
                    {{-- App UI Mockup --}}
                    <div class="p-5 pt-10 bg-slate-50 h-full relative z-10">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center text-red-500">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                            </div>
                            <div class="w-10 h-10 bg-white rounded-full shadow-sm flex items-center justify-center">
                                <div class="w-4 h-4 bg-slate-200 rounded-full"></div>
                            </div>
                        </div>
                        
                        <div class="w-3/4 h-8 bg-slate-200 rounded-lg mb-2"></div>
                        <div class="w-1/2 h-4 bg-slate-200 rounded-lg mb-6"></div>
                        
                        <div class="space-y-4">
                            <div class="w-full h-28 bg-white rounded-2xl shadow-sm border border-slate-100 flex p-3 gap-3">
                                <div class="w-1/3 h-full bg-slate-100 rounded-xl"></div>
                                <div class="w-2/3 space-y-2 py-1">
                                    <div class="w-full h-3 bg-slate-200 rounded"></div>
                                    <div class="w-2/3 h-3 bg-slate-200 rounded"></div>
                                    <div class="w-1/2 h-4 bg-red-100 rounded mt-auto"></div>
                                </div>
                            </div>
                            <div class="w-full h-28 bg-white rounded-2xl shadow-sm border border-slate-100 flex p-3 gap-3">
                                <div class="w-1/3 h-full bg-slate-100 rounded-xl"></div>
                                <div class="w-2/3 space-y-2 py-1">
                                    <div class="w-full h-3 bg-slate-200 rounded"></div>
                                    <div class="w-2/3 h-3 bg-slate-200 rounded"></div>
                                    <div class="w-1/2 h-4 bg-red-100 rounded mt-auto"></div>
                                </div>
                            </div>
                            <div class="w-full h-28 bg-white rounded-2xl shadow-sm border border-slate-100 flex p-3 gap-3">
                                <div class="w-1/3 h-full bg-slate-100 rounded-xl"></div>
                                <div class="w-2/3 space-y-2 py-1">
                                    <div class="w-full h-3 bg-slate-200 rounded"></div>
                                    <div class="w-2/3 h-3 bg-slate-200 rounded"></div>
                                    <div class="w-1/2 h-4 bg-red-100 rounded mt-auto"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
