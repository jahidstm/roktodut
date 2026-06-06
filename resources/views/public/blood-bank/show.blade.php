@extends('layouts.app')

@section('title', ($organization->name ?? 'রক্তের ব্যাংক') . ' — রক্তদূত')

@section('content')
<div class="min-h-screen bg-slate-50">

    {{-- ── Breadcrumb ── --}}
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('blood-bank.index') }}" class="hover:text-red-600 font-medium transition">রক্তের ব্যাংক</a>
            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-slate-700 font-semibold truncate">{{ $organization->name }}</span>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 py-8">

        {{-- ── Hospital Info Card ── --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-slate-900">{{ $organization->name }}</h1>
                        <p class="text-sm text-slate-500 mt-0.5">{{ $organization->address ?? 'ঠিকানা পাওয়া যায়নি' }}</p>
                        @if($organization->phone)
                            <p class="text-sm text-slate-600 font-medium mt-1">📞 {{ $organization->phone }}</p>
                        @endif
                    </div>
                </div>

                <div class="text-right">
                    @php
                        $isOpen = $rows->first(fn($r) => $r->exists)?->is_accepting_donations ?? false;
                    @endphp
                    @if($isOpen)
                        <span class="inline-flex items-center gap-1.5 text-sm font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-full">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                            আজ Donation নিচ্ছে
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-sm font-bold text-slate-500 bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-full">
                            <span class="w-2 h-2 bg-slate-400 rounded-full"></span>
                            আজ বন্ধ
                        </span>
                    @endif
                    @if($lastUpdated)
                        <p class="text-xs text-slate-400 mt-2">
                            শেষ আপডেট: {{ \Carbon\Carbon::parse($lastUpdated)->diffForHumans() }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Inventory Table ── --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="font-black text-slate-800">🩸 বর্তমান রক্তের মজুদ</h2>
                <div class="flex items-center gap-3 text-xs text-slate-400 flex-wrap">
                    <span>✅ পর্যাপ্ত (৫+)</span>
                    <span>⚠️ সীমিত (১–৪)</span>
                    <span>❌ নেই</span>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-y divide-slate-100">
                @foreach($rows as $row)
                @php
                    $units = $row->units_available ?? 0;
                    $status = $units >= 5 ? 'adequate' : ($units >= 1 ? 'limited' : 'empty');
                    $colorClass = match($status) {
                        'adequate' => 'bg-emerald-50',
                        'limited'  => 'bg-amber-50',
                        default    => 'bg-red-50',
                    };
                    $textClass = match($status) {
                        'adequate' => 'text-emerald-700',
                        'limited'  => 'text-amber-700',
                        default    => 'text-red-500',
                    };
                    $emoji = match($status) {
                        'adequate' => '✅',
                        'limited'  => '⚠️',
                        default    => '❌',
                    };
                    $label = match($status) {
                        'adequate' => 'পর্যাপ্ত',
                        'limited'  => 'সীমিত',
                        default    => 'নেই',
                    };
                @endphp
                <div class="flex flex-col items-center gap-2 p-6 {{ $colorClass }} text-center">
                    <span class="text-2xl font-black {{ $textClass }}">{{ $row->blood_group }}</span>
                    <span class="text-3xl">{{ $emoji }}</span>
                    <div>
                        <p class="text-lg font-black {{ $textClass }}">{{ $units }} ব্যাগ</p>
                        <p class="text-xs font-bold {{ $textClass }} opacity-70">{{ $label }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            @if($rows->first(fn($r) => $r->exists)?->notes)
                <div class="px-6 py-4 bg-blue-50 border-t border-blue-100">
                    <p class="text-sm text-blue-700 font-medium">
                        ℹ️ {{ $rows->first(fn($r) => $r->exists)->notes }}
                    </p>
                </div>
            @endif
        </div>

        {{-- ── Staleness Warning ── --}}
        @if($lastUpdated && \Carbon\Carbon::parse($lastUpdated)->diffInHours() > 24)
            <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mb-6 text-sm text-amber-700 font-medium flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                এই তথ্য {{ \Carbon\Carbon::parse($lastUpdated)->diffForHumans() }} আপডেট হয়েছে।
                যাওয়ার আগে হাসপাতালকে ফোন করে নিশ্চিত করুন।
            </div>
        @endif

        {{-- ── Back Button ── --}}
        <a href="{{ route('blood-bank.index') }}"
           class="inline-flex items-center gap-2 text-sm font-bold text-slate-600 hover:text-red-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            সব রক্তের ব্যাংক দেখুন
        </a>
    </div>
</div>
@endsection
