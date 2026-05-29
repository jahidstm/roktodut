@extends('layouts.admin-dashboard')

@section('title', 'সিস্টেম অ্যাডমিন ড্যাশবোর্ড — রক্তদূত')

@section('content')
<div data-panel-id="overview" class="max-w-7xl mx-auto px-4 py-8">
    
    {{-- 🎯 হেডার --}}
    <div class="mb-8 border-b border-slate-200 pb-5 scroll-reveal" data-scroll-reveal>
        <h1 class="text-2xl font-extrabold text-slate-900">ওভারভিউ</h1>
        <p class="text-slate-500 font-medium mt-2">পুরো সিস্টেমের রিয়েল-টাইম ডেটা অ্যানালিটিক্স এবং পেন্ডিং ভেরিফিকেশন ম্যানেজ করুন।</p>
    </div>

    {{-- সাকসেস/এরর মেসেজ --}}
    @if(session('success'))
        <div class="mb-8 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl font-bold flex items-center gap-2 scroll-reveal" data-scroll-reveal>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-8 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl font-bold flex items-center gap-2 scroll-reveal" data-scroll-reveal>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- 📊 ১. গ্লোবাল স্ট্যাটিস্টিকস --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-12 scroll-reveal" data-scroll-reveal>
        <x-card>
            <div class="text-slate-500 text-sm font-bold uppercase tracking-wider">মোট ইউজার</div>
            <div class="mt-2 text-4xl font-black text-slate-900">{{ $totalUsers }}</div>
        </x-card>
        <x-card>
            <div class="text-blue-600 text-sm font-bold uppercase tracking-wider">মোট ডোনার</div>
            <div class="mt-2 text-4xl font-black text-blue-600">{{ $totalDonors }}</div>
        </x-card>
        <x-card>
            <div class="text-red-600 text-sm font-bold uppercase tracking-wider">সফল রিকোয়েস্ট</div>
            <div class="mt-2 text-4xl font-black text-red-600">{{ $fulfilledRequests }} / {{ $totalRequests }}</div>
        </x-card>
        <x-card>
            <div class="text-emerald-600 text-sm font-bold uppercase tracking-wider">সাকসেস রেট</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-black text-emerald-600">{{ $successRate }}</span>
                <span class="text-emerald-400 font-bold text-sm">%</span>
            </div>
        </x-card>
    </div>

    {{-- ⚡ পেন্ডিং অ্যাকশন সেন্টার --}}
    @php
        $totalPending = ($pendingClaims ?? 0) + ($pendingNids ?? 0) + ($pendingOrgs ?? 0) + ($pendingHospitals ?? 0) + ($pendingBlogCount ?? 0) + ($pendingReports ?? 0) + ($pendingSupportMessages ?? 0) + ($pendingAmbulances ?? 0);
    @endphp

    @if($totalPending > 0)
    <div class="mb-4 flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-2xl px-5 py-3 scroll-reveal" data-scroll-reveal>
        <span class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center font-black text-sm shrink-0 animate-pulse">{{ $totalPending }}</span>
        <p class="text-sm font-bold text-amber-800">{{ $totalPending }}টি আইটেম আপনার মনোযোগ চাইছে — নিচে দেখুন।</p>
    </div>
    @else
    <div class="mb-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 rounded-2xl px-5 py-3 scroll-reveal" data-scroll-reveal>
        <span class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm shrink-0">✅</span>
        <p class="text-sm font-bold text-emerald-800">সব কিছু আপ-টু-ডেট। কোনো পেন্ডিং আইটেম নেই।</p>
    </div>
    @endif

    {{-- পেন্ডিং আইটেম কার্ডস --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mb-10 scroll-reveal" data-scroll-reveal>

        {{-- পেন্ডিং ক্লেইম --}}
        <a href="{{ route('admin.donations.proof_reviews') }}" class="group relative bg-white rounded-2xl border {{ ($pendingClaims ?? 0) > 0 ? 'border-red-200 shadow-red-50' : 'border-slate-200' }} shadow-sm p-4 flex flex-col items-center gap-2 text-center hover:shadow-md transition hover:-translate-y-0.5">
            <div class="w-10 h-10 rounded-xl {{ ($pendingClaims ?? 0) > 0 ? 'bg-red-100 text-red-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-xl">🩸</div>
            <span class="text-2xl font-black {{ ($pendingClaims ?? 0) > 0 ? 'text-red-600' : 'text-slate-400' }}">{{ $pendingClaims ?? 0 }}</span>
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide leading-tight">পেন্ডিং ক্লেইম</span>
            @if(($pendingClaims ?? 0) > 0)
                <span class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-red-500 rounded-full border-2 border-white animate-pulse"></span>
            @endif
        </a>

        {{-- পেন্ডিং NID --}}
        <a href="{{ route('admin.nid.reviews') }}" class="group relative bg-white rounded-2xl border {{ ($pendingNids ?? 0) > 0 ? 'border-amber-200 shadow-amber-50' : 'border-slate-200' }} shadow-sm p-4 flex flex-col items-center gap-2 text-center hover:shadow-md transition hover:-translate-y-0.5">
            <div class="w-10 h-10 rounded-xl {{ ($pendingNids ?? 0) > 0 ? 'bg-amber-100 text-amber-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-xl">🪪</div>
            <span class="text-2xl font-black {{ ($pendingNids ?? 0) > 0 ? 'text-amber-600' : 'text-slate-400' }}">{{ $pendingNids ?? 0 }}</span>
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide leading-tight">পেন্ডিং NID</span>
            @if(($pendingNids ?? 0) > 0)
                <span class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-amber-500 rounded-full border-2 border-white animate-pulse"></span>
            @endif
        </a>

        {{-- পেন্ডিং অর্গানাইজেশন --}}
        <a href="{{ route('admin.org.reviews') }}" class="group relative bg-white rounded-2xl border {{ ($pendingOrgs ?? 0) > 0 ? 'border-blue-200 shadow-blue-50' : 'border-slate-200' }} shadow-sm p-4 flex flex-col items-center gap-2 text-center hover:shadow-md transition hover:-translate-y-0.5">
            <div class="w-10 h-10 rounded-xl {{ ($pendingOrgs ?? 0) > 0 ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-xl">🏢</div>
            <span class="text-2xl font-black {{ ($pendingOrgs ?? 0) > 0 ? 'text-blue-600' : 'text-slate-400' }}">{{ $pendingOrgs ?? 0 }}</span>
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide leading-tight">পেন্ডিং অর্গ</span>
            @if(($pendingOrgs ?? 0) > 0)
                <span class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-blue-500 rounded-full border-2 border-white animate-pulse"></span>
            @endif
        </a>

        {{-- পেন্ডিং হাসপাতাল --}}
        <a href="{{ route('admin.hospitals.unverified') }}" class="group relative bg-white rounded-2xl border {{ ($pendingHospitals ?? 0) > 0 ? 'border-emerald-200 shadow-emerald-50' : 'border-slate-200' }} shadow-sm p-4 flex flex-col items-center gap-2 text-center hover:shadow-md transition hover:-translate-y-0.5">
            <div class="w-10 h-10 rounded-xl {{ ($pendingHospitals ?? 0) > 0 ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-xl">🏥</div>
            <span class="text-2xl font-black {{ ($pendingHospitals ?? 0) > 0 ? 'text-emerald-600' : 'text-slate-400' }}">{{ $pendingHospitals ?? 0 }}</span>
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide leading-tight">পেন্ডিং হাসপাতাল</span>
            @if(($pendingHospitals ?? 0) > 0)
                <span class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-emerald-500 rounded-full border-2 border-white animate-pulse"></span>
            @endif
        </a>

        {{-- পেন্ডিং ব্লগ --}}
        <a href="{{ route('admin.blog.moderation.index') }}" class="group relative bg-white rounded-2xl border {{ ($pendingBlogCount ?? 0) > 0 ? 'border-purple-200 shadow-purple-50' : 'border-slate-200' }} shadow-sm p-4 flex flex-col items-center gap-2 text-center hover:shadow-md transition hover:-translate-y-0.5">
            <div class="w-10 h-10 rounded-xl {{ ($pendingBlogCount ?? 0) > 0 ? 'bg-purple-100 text-purple-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-xl">📝</div>
            <span class="text-2xl font-black {{ ($pendingBlogCount ?? 0) > 0 ? 'text-purple-600' : 'text-slate-400' }}">{{ $pendingBlogCount ?? 0 }}</span>
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide leading-tight">পেন্ডিং ব্লগ</span>
            @if(($pendingBlogCount ?? 0) > 0)
                <span class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-purple-500 rounded-full border-2 border-white animate-pulse"></span>
            @endif
        </a>

        {{-- পেন্ডিং রিপোর্ট --}}
        <a href="{{ route('admin.reports.index') }}" class="group relative bg-white rounded-2xl border {{ ($pendingReports ?? 0) > 0 ? 'border-orange-200 shadow-orange-50' : 'border-slate-200' }} shadow-sm p-4 flex flex-col items-center gap-2 text-center hover:shadow-md transition hover:-translate-y-0.5">
            <div class="w-10 h-10 rounded-xl {{ ($pendingReports ?? 0) > 0 ? 'bg-orange-100 text-orange-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-xl">🚩</div>
            <span class="text-2xl font-black {{ ($pendingReports ?? 0) > 0 ? 'text-orange-600' : 'text-slate-400' }}">{{ $pendingReports ?? 0 }}</span>
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide leading-tight">পেন্ডিং রিপোর্ট</span>
            @if(($pendingReports ?? 0) > 0)
                <span class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-orange-500 rounded-full border-2 border-white animate-pulse"></span>
            @endif
        </a>

        {{-- সাপোর্ট ইনবক্স --}}
        <a href="{{ route('admin.support.messages.index') }}" class="group relative bg-white rounded-2xl border {{ ($pendingSupportMessages ?? 0) > 0 ? 'border-indigo-200 shadow-indigo-50' : 'border-slate-200' }} shadow-sm p-4 flex flex-col items-center gap-2 text-center hover:shadow-md transition hover:-translate-y-0.5">
            <div class="w-10 h-10 rounded-xl {{ ($pendingSupportMessages ?? 0) > 0 ? 'bg-indigo-100 text-indigo-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-xl">📬</div>
            <span class="text-2xl font-black {{ ($pendingSupportMessages ?? 0) > 0 ? 'text-indigo-600' : 'text-slate-400' }}">{{ $pendingSupportMessages ?? 0 }}</span>
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide leading-tight">সাপোর্ট মেসেজ</span>
            @if(($pendingSupportMessages ?? 0) > 0)
                <span class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-indigo-500 rounded-full border-2 border-white animate-pulse"></span>
            @endif
        </a>

        {{-- পেন্ডিং অ্যাম্বুলেন্স --}}
        <a href="{{ route('admin.ambulances.index') }}" class="group relative bg-white rounded-2xl border {{ ($pendingAmbulances ?? 0) > 0 ? 'border-cyan-200 shadow-cyan-50' : 'border-slate-200' }} shadow-sm p-4 flex flex-col items-center gap-2 text-center hover:shadow-md transition hover:-translate-y-0.5">
            <div class="w-10 h-10 rounded-xl {{ ($pendingAmbulances ?? 0) > 0 ? 'bg-cyan-100 text-cyan-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-xl">🚑</div>
            <span class="text-2xl font-black {{ ($pendingAmbulances ?? 0) > 0 ? 'text-cyan-600' : 'text-slate-400' }}">{{ $pendingAmbulances ?? 0 }}</span>
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide leading-tight">অ্যাম্বুলেন্স</span>
            @if(($pendingAmbulances ?? 0) > 0)
                <span class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-cyan-500 rounded-full border-2 border-white animate-pulse"></span>
            @endif
        </a>

    </div>

    {{-- 🔒 ৫. সিকিউরিটি ও অডিট প্যানেল --}}

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-12">
        {{-- Security Radar Widget --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-[400px] scroll-reveal" data-scroll-reveal>
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center shrink-0">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">🚨</span>
                        সন্দেহজনক কার্যক্রম
                    </h3>
                    <p class="text-xs text-slate-500 font-bold mt-1">সিস্টেম সিকিউরিটি রাডার (MVP-Lite)</p>
                </div>
                <div class="flex flex-col items-end">
                    <span class="text-2xl font-black {{ $todaysSecurityEventsCount > 0 ? 'text-red-600' : 'text-emerald-500' }}">
                        {{ $todaysSecurityEventsCount }}
                    </span>
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">আজকের ইভেন্ট</span>
                </div>
            </div>
            <div class="p-0 flex-1 overflow-y-auto">
                @if($recentSecurityLogs->isEmpty())
                    <div class="p-8 text-center flex flex-col items-center justify-center h-full">
                        <div class="w-12 h-12 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-400 mb-3 text-2xl">🛡️</div>
                        <p class="text-sm font-bold text-slate-500">কোনো সন্দেহজনক কার্যক্রম পাওয়া যায়নি। সিস্টেম নিরাপদ।</p>
                    </div>
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach($recentSecurityLogs as $log)
                            <li class="p-4 hover:bg-slate-50 transition flex gap-3">
                                <div class="w-2 h-2 mt-1.5 rounded-full bg-red-500 shrink-0 animate-pulse"></div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-red-600 bg-red-50 px-2 py-0.5 rounded-md border border-red-100">{{ str_replace('_', ' ', $log->event_type) }}</span>
                                        <span class="text-[10px] font-bold text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm font-bold text-slate-800">{{ $log->description }}</p>
                                    @if($log->user)
                                        <p class="text-xs font-semibold text-slate-500 mt-1">ইউজার: <span class="text-slate-700">{{ $log->user->name }}</span> (ID: {{ $log->user->id }}) @if($log->ip_address) • IP: {{ $log->ip_address }} @endif</p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- Admin Audit Trail Widget --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-[400px] scroll-reveal" data-scroll-reveal>
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center shrink-0">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">📑</span>
                        অ্যাডমিন অডিট ট্রেইল
                    </h3>
                    <p class="text-xs text-slate-500 font-bold mt-1">সর্বশেষ ২০টি অ্যাকশন</p>
                </div>
            </div>
            <div class="p-0 flex-1 overflow-y-auto">
                @if($recentAuditLogs->isEmpty())
                    <div class="p-8 text-center flex flex-col items-center justify-center h-full">
                        <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center text-slate-400 mb-3 text-2xl">📋</div>
                        <p class="text-sm font-bold text-slate-500">এখনো কোনো অডিট লগ তৈরি হয়নি।</p>
                    </div>
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach($recentAuditLogs as $audit)
                            <li class="p-4 hover:bg-slate-50 transition flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 text-indigo-600 flex items-center justify-center font-black text-xs shrink-0 border border-slate-200">
                                    {{ mb_substr($audit->admin->name ?? 'A', 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start mb-0.5">
                                        <p class="text-sm font-extrabold text-slate-900 truncate pr-2">{{ $audit->admin->name ?? 'System Admin' }}</p>
                                        <span class="text-[10px] font-bold text-slate-400 whitespace-nowrap shrink-0">{{ $audit->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-xs font-semibold text-slate-500 mb-1.5 flex items-center gap-1.5 flex-wrap">
                                        <span class="bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded border border-indigo-100 uppercase tracking-wider text-[9px]">{{ str_replace('_', ' ', $audit->action_type) }}</span>
                                        @if($audit->target_id)
                                            <span>টার্গেট আইডি: <strong class="text-slate-700">{{ $audit->target_id }}</strong></span>
                                        @endif
                                    </div>
                                    @if(isset($audit->details['reason']))
                                        <p class="text-xs font-medium text-red-600 bg-red-50 p-2 rounded border border-red-100 mt-1">কারণ: {{ $audit->details['reason'] }}</p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    {{-- 📈 ৬. চার্ট সেকশন (Professional Horizontal Bars) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">

        {{-- Pie Chart: ব্লাড গ্রুপ ডিমান্ড --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-7 scroll-reveal" data-scroll-reveal>
            <div class="mb-5 border-b border-slate-100 pb-4">
                <h3 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-red-100 text-red-600 flex items-center justify-center text-sm">🩸</span>
                    ব্লাড গ্রুপ ডিমান্ড
                </h3>
                <p class="text-xs text-slate-500 font-semibold mt-1.5">কোন রক্তের গ্রুপ সবচেয়ে বেশি রিকোয়েস্ট হয়েছে</p>
            </div>
            
            @if(empty($bloodGroupDemand))
                <div class="flex flex-col items-center justify-center h-[240px] text-slate-400">
                    <span class="text-4xl mb-3">📊</span>
                    <span class="text-sm font-semibold">গত ৩০ দিনে কোনো ডিমান্ড নেই</span>
                </div>
            @else
                <div style="height:260px;">
                    <canvas id="bloodGroupChart"></canvas>
                </div>
            @endif
        </div>

        {{-- Bar Chart: জেলা ভিত্তিক ইমার্জেন্সি --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-7 scroll-reveal" data-scroll-reveal>
            <div class="mb-5 border-b border-slate-100 pb-4">
                <h3 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-sm">📍</span>
                    শীর্ষ ৫ ইমার্জেন্সি জেলা (গত ৩০ দিন)
                </h3>
            </div>
            
            @if(empty($districtDemand))
                <div class="flex flex-col items-center justify-center h-[240px] text-slate-400">
                    <span class="text-4xl mb-3">📉</span>
                    <span class="text-sm font-semibold">গত ৩০ দিনে কোনো রিকোয়েস্ট নেই</span>
                </div>
            @else
                <div style="height:260px;">
                    <canvas id="districtChart"></canvas>
                </div>
            @endif
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Check if Chart.js is loaded
    if(typeof Chart === 'undefined') return;

    Chart.defaults.font.family = "'Inter', 'Hind Siliguri', sans-serif";
    Chart.defaults.color = '#64748b';

    // ─── ১. ব্লাড গ্রুপ Pie Chart ────────────────────────────────
    @if(!empty($bloodGroupDemand))
    const bloodGroupData = @json($bloodGroupDemand);
    const bgColors = ['#ef4444','#f97316','#eab308','#22c55e','#06b6d4','#3b82f6','#8b5cf6','#ec4899'];
    new Chart(document.getElementById('bloodGroupChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(bloodGroupData),
            datasets: [{
                data: Object.values(bloodGroupData),
                backgroundColor: bgColors.slice(0, Object.keys(bloodGroupData).length),
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'right', 
                    labels: { font: { size: 12, weight: 'bold', family: "'Inter', sans-serif" }, padding: 15, usePointStyle: true, pointStyle: 'circle' } 
                },
                tooltip: { 
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { size: 13 },
                    bodyFont: { size: 14, weight: 'bold' },
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed} রিকোয়েস্ট` } 
                }
            },
            cutout: '65%'
        }
    });
    @endif

    // ─── ২. জেলা Bar Chart ───────────────────────────────────────
    @if(!empty($districtDemand))
    const districtData = @json($districtDemand);
    new Chart(document.getElementById('districtChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(districtData),
            datasets: [{
                label: 'রিকোয়েস্ট সংখ্যা',
                data: Object.values(districtData),
                backgroundColor: 'rgba(59, 130, 246, 0.85)',
                hoverBackgroundColor: 'rgba(37, 99, 235, 1)',
                borderRadius: 4,
                barThickness: 20,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { display: false },
                tooltip: { 
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { size: 13 },
                    bodyFont: { size: 14, weight: 'bold' },
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: { label: ctx => ` ${ctx.label} — ${ctx.parsed.x} টি জরুরি রিকোয়েস্ট` } 
                } 
            },
            scales: {
                x: { 
                    beginAtZero: true, 
                    ticks: { precision: 0, font: { weight: '600' } }, 
                    grid: { color: '#f1f5f9', drawBorder: false } 
                },
                y: { 
                    grid: { display: false },
                    ticks: { font: { weight: 'bold', size: 12 }, color: '#1e293b' }
                }
            }
        }
    });
    @endif
});
</script>
@endsection
