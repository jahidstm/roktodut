@extends('layouts.app')

@section('title', 'রক্তের ব্যাংক খুঁজুন — রক্তদূত')
@section('meta_description', 'আপনার কাছের হাসপাতালে কোন রক্তের গ্রুপ পাওয়া যাচ্ছে জানুন।')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-red-50 via-white to-white">

    {{-- ── Hero / Search Section ── --}}
    <div class="bg-gradient-to-br from-red-600 via-red-700 to-rose-800 text-white py-12 px-4">
        <div class="max-w-4xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-1.5 rounded-full text-sm font-bold mb-4">
                🏥 রিয়েল-টাইম রক্তের মজুদ
            </div>
            <h1 class="text-3xl sm:text-4xl font-black mb-3">কাছের রক্তের ব্যাংক খুঁজুন</h1>
            <p class="text-red-100 text-base mb-8">হাসপাতালে এই মুহূর্তে কোন রক্তের গ্রুপ আছে জেনে নিন</p>

            {{-- Search Form --}}
            <form method="GET" action="{{ route('blood-bank.index') }}"
                  class="bg-white rounded-2xl p-4 shadow-2xl max-w-2xl mx-auto">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    {{-- Blood Group --}}
                    <select name="blood_group"
                            class="rounded-xl border border-slate-200 px-3 py-2.5 text-slate-700 font-semibold text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">সব রক্তের গ্রুপ</option>
                        @foreach($bloodGroups as $group)
                            <option value="{{ $group }}" {{ request('blood_group') === $group ? 'selected' : '' }}>
                                {{ $group }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Division --}}
                    <select name="division_id" id="division-select"
                            class="rounded-xl border border-slate-200 px-3 py-2.5 text-slate-700 font-semibold text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                            onchange="this.form.submit()">
                        <option value="">সব বিভাগ</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>
                                {{ $div->name }}
                            </option>
                        @endforeach
                    </select>

                    {{-- District --}}
                    <select name="district_id"
                            class="rounded-xl border border-slate-200 px-3 py-2.5 text-slate-700 font-semibold text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">সব জেলা</option>
                        @foreach($districts as $dist)
                            <option value="{{ $dist->id }}" {{ request('district_id') == $dist->id ? 'selected' : '' }}>
                                {{ $dist->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                        class="mt-3 w-full bg-red-600 hover:bg-red-700 text-white font-black py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    রক্তের ব্যাংক খুঁজুন
                </button>
            </form>
        </div>
    </div>

    {{-- ── Legend ── --}}
    <div class="max-w-4xl mx-auto px-4 py-4">
        <div class="flex items-center gap-4 text-xs text-slate-500 flex-wrap">
            <span class="flex items-center gap-1"><span class="text-emerald-600 font-bold">✅ পর্যাপ্ত</span> — ৫+ ব্যাগ</span>
            <span class="flex items-center gap-1"><span class="text-amber-600 font-bold">⚠️ সীমিত</span> — ১–৪ ব্যাগ</span>
            <span class="flex items-center gap-1"><span class="text-red-600 font-bold">❌ নেই</span> — ০ ব্যাগ</span>
        </div>
    </div>

    {{-- ── Results ── --}}
    <div class="max-w-4xl mx-auto px-4 pb-16">
        @if($bloodBanks->isEmpty())
            <div class="text-center py-20">
                <div class="text-6xl mb-4">🏥</div>
                <p class="text-xl font-black text-slate-700 mb-2">কোনো রক্তের ব্যাংক পাওয়া যায়নি</p>
                <p class="text-slate-500 text-sm">এই এলাকায় এখনো কোনো হাসপাতাল তাদের রক্তের মজুদ আপলোড করেনি।</p>
            </div>
        @else
            <div class="mb-4 text-sm text-slate-500 font-medium">
                {{ $bloodBanks->total() }}টি রক্তের ব্যাংক পাওয়া গেছে
            </div>

            <div class="space-y-4">
                @foreach($bloodBanks as $bank)
                @php
                    $inventories = $bank->bloodInventories->keyBy('blood_group');
                    $isOpen = $bank->bloodInventories->where('is_accepting_donations', true)->isNotEmpty();
                    $lastUpdated = $bank->bloodInventories->max('updated_at');
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition overflow-hidden">
                    <div class="p-5">
                        {{-- Hospital Header --}}
                        <div class="flex items-start justify-between mb-4 gap-3">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h2 class="font-black text-slate-900 text-lg">{{ $bank->name }}</h2>
                                    @if($isOpen)
                                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">✅ খোলা</span>
                                    @else
                                        <span class="text-xs font-bold text-slate-400 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full">🔴 বন্ধ</span>
                                    @endif
                                </div>
                                <p class="text-sm text-slate-500 mt-0.5">
                                    {{ $bank->address ?? ($bank->upazila . ', ' . $bank->district) }}
                                </p>
                                @if($lastUpdated)
                                    <p class="text-xs text-slate-400 mt-1">শেষ আপডেট: {{ \Carbon\Carbon::parse($lastUpdated)->diffForHumans() }}</p>
                                @endif
                            </div>
                            <a href="{{ route('blood-bank.show', $bank) }}"
                               class="shrink-0 text-sm font-bold text-red-600 hover:text-red-700 flex items-center gap-1">
                                বিস্তারিত
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>

                        {{-- Blood Group Grid --}}
                        <div class="grid grid-cols-4 sm:grid-cols-8 gap-2">
                            @foreach($bloodGroups as $group)
                            @php
                                $inv = $inventories->get($group);
                                $units = $inv?->units_available ?? 0;
                                $emoji = $units >= 5 ? '✅' : ($units >= 1 ? '⚠️' : '❌');
                                $colorClass = $units >= 5
                                    ? 'bg-emerald-50 border-emerald-200 text-emerald-700'
                                    : ($units >= 1
                                        ? 'bg-amber-50 border-amber-200 text-amber-700'
                                        : 'bg-red-50 border-red-200 text-red-400');
                            @endphp
                            <div class="flex flex-col items-center gap-1 p-2 rounded-xl border {{ $colorClass }} text-center">
                                <span class="text-xs font-black">{{ $group }}</span>
                                <span class="text-base leading-none">{{ $emoji }}</span>
                                @if($inv)
                                    <span class="text-[10px] font-bold opacity-70">{{ $units }}টি</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $bloodBanks->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
