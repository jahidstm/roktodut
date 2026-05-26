@extends('layouts.app')

@section('title', 'অ্যাম্বুলেন্স সার্ভিস — রক্তদূত')

@section('content')

@php
    $btnPrimary = 'inline-flex items-center justify-center gap-2.5 bg-red-600 hover:bg-red-700 text-white rounded-2xl font-black shadow-[0_10px_25px_rgba(239,68,68,0.3)] hover:shadow-[0_15px_35px_rgba(239,68,68,0.4)] transition-all duration-300 hover-lift';
@endphp

{{-- Hero Section --}}
<section class="relative bg-white flex flex-col items-center justify-center overflow-hidden pb-12 pt-8 sm:pt-16" aria-label="অ্যাম্বুলেন্স সার্ভিস হিরো">
    {{-- Background glows --}}
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[700px] h-[500px] bg-red-50/60 rounded-full blur-[130px] pointer-events-none"></div>

    <div class="relative z-10 mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 flex flex-col items-center text-center">
        <div class="inline-flex items-center gap-2.5 bg-white border border-red-100 text-red-600 shadow-sm text-xs font-bold px-4 py-2 rounded-full mb-6">
            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse flex-shrink-0"></span>
            জরুরি অ্যাম্বুলেন্স ডিরেক্টরি
        </div>

        <h1 class="text-3xl sm:text-5xl font-black text-slate-900 leading-[1.15] mb-5 tracking-tight">
            জরুরি মুহূর্তে <span class="text-red-600">অ্যাম্বুলেন্স</span> খুঁজুন
        </h1>

        <p class="text-sm sm:text-lg text-slate-500 mb-8 max-w-2xl mx-auto leading-relaxed font-medium">
            আপনার আশেপাশের ভেরিফাইড অ্যাম্বুলেন্স সার্ভিসগুলো খুঁজুন এবং সরাসরি যোগাযোগ করুন।
        </p>
    </div>
</section>

{{-- Filter & List Section --}}
<section class="pb-24 pt-8 bg-slate-50 relative">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 -mt-20 relative z-20">
        
        {{-- Search Filter --}}
        <div class="bg-white border border-slate-100 rounded-[2rem] p-6 shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] mb-12" x-data="{
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
            <form action="{{ route('ambulances.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                
                <div class="lg:col-span-1">
                    <select name="type" class="w-full bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-red-500/30 transition-all cursor-pointer">
                        <option value="">সব ধরণ</option>
                        <option value="non-ac" {{ request('type') == 'non-ac' ? 'selected' : '' }}>Non-AC</option>
                        <option value="ac" {{ request('type') == 'ac' ? 'selected' : '' }}>AC</option>
                        <option value="icu" {{ request('type') == 'icu' ? 'selected' : '' }}>ICU / Life Support</option>
                        <option value="nicu" {{ request('type') == 'nicu' ? 'selected' : '' }}>NICU</option>
                        <option value="freezer" {{ request('type') == 'freezer' ? 'selected' : '' }}>Freezer Van</option>
                    </select>
                </div>

                <div class="lg:col-span-1">
                    <select name="division_id" @change="fetchDistricts($event.target.value)" class="w-full bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-red-500/30 transition-all cursor-pointer">
                        <option value="">বিভাগ নির্বাচন</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-1">
                    <select name="district_id" @change="fetchUpazilas($event.target.value)" class="w-full bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-red-500/30 transition-all cursor-pointer">
                        <option value="" x-text="loadingDistricts ? 'লোড হচ্ছে...' : 'জেলা নির্বাচন'"></option>
                        <template x-for="d in districts" :key="d.id">
                            <option :value="d.id" x-text="d.name" :selected="d.id == '{{ request('district_id') }}'"></option>
                        </template>
                    </select>
                </div>

                <div class="lg:col-span-1">
                    <select name="upazila_id" class="w-full bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-red-500/30 transition-all cursor-pointer">
                        <option value="" x-text="loadingUpazilas ? 'লোড হচ্ছে...' : 'উপজেলা নির্বাচন'"></option>
                        <template x-for="u in upazilas" :key="u.id">
                            <option :value="u.id" x-text="u.name" :selected="u.id == '{{ request('upazila_id') }}'"></option>
                        </template>
                    </select>
                </div>

                <div class="lg:col-span-1">
                    <button type="submit" class="w-full {{ $btnPrimary }} px-4 py-3 text-sm h-[48px]">
                        সার্চ করুন
                    </button>
                </div>
            </form>
        </div>

        {{-- Add Ambulance CTA --}}
        <div class="mb-12 bg-white rounded-2xl border border-red-100 p-6 flex flex-col md:flex-row items-center justify-between gap-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-red-50 text-red-600 flex items-center justify-center text-2xl flex-shrink-0">🎁</div>
                <div>
                    <h3 class="font-black text-slate-900 text-lg">আপনার এলাকার অ্যাম্বুলেন্স যুক্ত করুন</h3>
                    <p class="text-sm font-medium text-slate-500">সঠিক তথ্য সাবমিট করে ভেরিফাই হলে জিতে নিন স্পেশাল পয়েন্ট!</p>
                </div>
            </div>
            <a href="{{ route('user.ambulances.create') }}" class="{{ $btnPrimary }} px-6 py-2.5 text-sm shrink-0 whitespace-nowrap">
                অ্যাম্বুলেন্স যোগ করুন &rarr;
            </a>
        </div>

        {{-- Results Grid --}}
        @if($ambulances->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($ambulances as $ambulance)
                    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:shadow-lg transition-all duration-300 relative group overflow-hidden">
                        
                        {{-- Badges --}}
                        <div class="flex justify-between items-start mb-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-slate-100 text-slate-600">
                                {{ strtoupper($ambulance->type) }}
                            </span>
                            @if($ambulance->is_verified)
                                <span class="inline-flex items-center gap-1 text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md text-xs font-bold" title="Verified by Admin">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    Verified
                                </span>
                            @endif
                        </div>

                        <h3 class="text-xl font-black text-slate-900 mb-2 truncate" title="{{ $ambulance->name }}">{{ $ambulance->name }}</h3>
                        
                        <div class="flex items-center gap-2 text-slate-500 text-sm font-medium mb-6">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            {{ $ambulance->upazila?->name }}, {{ $ambulance->district?->name }}
                        </div>

                        {{-- Call Action --}}
                        <a href="tel:{{ $ambulance->phone }}" class="w-full flex items-center justify-center gap-2 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border border-red-100 hover:border-red-600 py-3 rounded-2xl font-bold transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            {{ $ambulance->phone }}
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $ambulances->links() }}
            </div>
        @else
            <div class="text-center py-24 bg-white rounded-[2rem] border border-slate-100 shadow-sm">
                <div class="text-5xl mb-4">🚑</div>
                <h3 class="text-xl font-black text-slate-900 mb-2">কোনো অ্যাম্বুলেন্স পাওয়া যায়নি</h3>
                <p class="text-slate-500 font-medium">আপনার সার্চ করা ক্রাইটেরিয়ায় কোনো ভেরিফাইড অ্যাম্বুলেন্স লিস্টেড নেই।</p>
            </div>
        @endif

    </div>
</section>

@endsection
