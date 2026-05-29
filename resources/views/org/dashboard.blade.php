@extends('layouts.org-dashboard')

@section('title', 'অর্গানাইজেশন ড্যাশবোর্ড — রক্তদূত')

@section('content')
<div data-panel-id="members">

    {{-- Pending verification warning --}}
    @if($organization && $organization->status === 'pending')
    <div class="mb-6 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-5 shadow-sm flex flex-col sm:flex-row items-center gap-4 relative overflow-hidden scroll-reveal" data-scroll-reveal>
        <div class="absolute right-0 top-0 w-24 h-24 bg-amber-500/10 rounded-full blur-3xl -z-0"></div>
        <div class="w-12 h-12 bg-white text-amber-600 rounded-2xl flex items-center justify-center shrink-0 shadow-sm border border-amber-100 z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="z-10">
            <h3 class="text-lg font-black text-amber-900">ভেরিফিকেশন পেন্ডিং ⏳</h3>
            <p class="text-amber-800 text-sm font-bold mt-1 leading-relaxed">
                আপনার অর্গানাইজেশনের দেওয়া ডকুমেন্টস সিস্টেম অ্যাডমিন যাচাই করছেন। ভেরিফিকেশন সম্পন্ন হলে নোটিফিকেশন পাবেন।
            </p>
        </div>
    </div>
    @endif

    {{-- 📊 Analytics Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8 scroll-reveal" data-scroll-reveal>
        <div class="bg-white p-5 rounded-2xl border border-emerald-100 shadow-sm flex flex-col justify-center">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">ভেরিফাইড মেম্বার</p>
            <h3 class="text-3xl font-black text-emerald-600 mt-1">{{ $stats['verified'] ?? 0 }}</h3>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-blue-100 shadow-sm flex flex-col justify-center">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">রেডি মেম্বার</p>
                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
            </div>
            <h3 class="text-3xl font-black text-blue-600 mt-1">{{ $stats['ready'] ?? 0 }}</h3>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-red-100 shadow-sm flex flex-col justify-center">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">অনলাইন ডোনেশন</p>
            <h3 class="text-3xl font-black text-red-600 mt-1">{{ $stats['online_donations'] ?? 0 }} <span class="text-xs font-bold text-slate-400">(ট্র্যাকড)</span></h3>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-teal-100 shadow-sm flex flex-col justify-center">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">ক্যাম্প ডোনেশন</p>
            <h3 class="text-3xl font-black text-teal-600 mt-1">{{ $stats['camp_donations'] ?? 0 }} <span class="text-xs font-bold text-slate-400">(লগড)</span></h3>
        </div>
    </div>

    {{-- District Chart --}}
    @if(isset($districtWiseMembers) && count($districtWiseMembers) > 0)
    <div class="mb-8 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm scroll-reveal" data-scroll-reveal>
        <h3 class="text-base font-extrabold text-slate-800 mb-4 flex items-center gap-2">📍 জেলা ভিত্তিক ভেরিফাইড মেম্বার</h3>
        <div class="flex flex-wrap gap-3">
            @foreach($districtWiseMembers as $district => $count)
                <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-2 flex items-center justify-between gap-4">
                    <span class="text-sm font-bold text-slate-700">{{ $district }}</span>
                    <span class="bg-blue-100 text-blue-700 text-xs font-black px-2 py-0.5 rounded-full">{{ $count }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- 📊 Visualizations --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8 scroll-reveal" data-scroll-reveal>
        
        {{-- Member Status Visualization --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-lg font-extrabold text-slate-900 mb-6 flex items-center gap-2">
                👥 মেম্বার স্ট্যাটাস ওভারভিউ
            </h3>
            
            @php
                $total = $totalMembers > 0 ? $totalMembers : 1; // Prevent division by zero
                $verifiedPct = round(($stats['verified'] / $total) * 100);
                $pendingPct = round(($stats['pending'] / $total) * 100);
                $readyPct = round(($stats['ready'] / $total) * 100);
            @endphp

            <div class="space-y-5">
                <div>
                    <div class="flex justify-between text-sm font-bold mb-1">
                        <span class="text-emerald-700">ভেরিফাইড মেম্বার ({{ $stats['verified'] }})</span>
                        <span class="text-emerald-600">{{ $verifiedPct }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-3">
                        <div class="bg-emerald-500 h-3 rounded-full" style="width: {{ $verifiedPct }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-sm font-bold mb-1">
                        <span class="text-amber-700">পেন্ডিং রিভিউ ({{ $stats['pending'] }})</span>
                        <span class="text-amber-600">{{ $pendingPct }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-3">
                        <div class="bg-amber-400 h-3 rounded-full" style="width: {{ $pendingPct }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-sm font-bold mb-1">
                        <span class="text-blue-700">ডোনেট করতে প্রস্তুত ({{ $stats['ready'] }})</span>
                        <span class="text-blue-600">{{ $readyPct }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-3">
                        <div class="bg-blue-500 h-3 rounded-full" style="width: {{ $readyPct }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Donation Impact Visualization --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-red-50 rounded-full blur-3xl -z-0"></div>
            
            <h3 class="text-lg font-extrabold text-slate-900 mb-6 flex items-center gap-2 relative z-10">
                🩸 ডোনেশন ইমপ্যাক্ট
            </h3>

            @php
                $totalDonations = $stats['online_donations'] + $stats['camp_donations'];
                $onlinePct = $totalDonations > 0 ? round(($stats['online_donations'] / $totalDonations) * 100) : 0;
                $campPct = $totalDonations > 0 ? round(($stats['camp_donations'] / $totalDonations) * 100) : 0;
            @endphp

            <div class="flex items-center justify-center mb-8 relative z-10">
                <div class="relative w-40 h-40">
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                        <!-- Background Circle -->
                        <path class="text-slate-100" stroke-width="4" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <!-- Camp Donations -->
                        <path class="text-teal-500" stroke-width="4" stroke-dasharray="{{ $campPct }}, 100" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <!-- Online Donations -->
                        <path class="text-red-500" stroke-width="4" stroke-dasharray="{{ $onlinePct }}, 100" stroke-dashoffset="-{{ $campPct }}" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-3xl font-black text-slate-800">{{ $totalDonations }}</span>
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">মোট ডোনেশন</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-center gap-6 relative z-10">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                    <span class="text-sm font-bold text-slate-600">অনলাইন ({{ $stats['online_donations'] }})</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-teal-500"></span>
                    <span class="text-sm font-bold text-slate-600">ক্যাম্প ({{ $stats['camp_donations'] }})</span>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
