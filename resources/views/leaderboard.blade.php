@extends('layouts.app')

@section('title', 'লিডারবোর্ড – রক্তদূত')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8 sm:py-12">

    {{-- ══════════════════════════════════════════
         Hero Section — dynamic based on filters
    ══════════════════════════════════════════ --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-red-600 via-red-700 to-red-900 p-8 sm:p-12 mb-6 shadow-2xl">
        {{-- Decorative blobs --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4 pointer-events-none"></div>

        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center text-2xl shadow-lg">🏆</div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-black text-white leading-tight">
                            {{ $time === 'month' ? 'এই মাসের' : 'সর্বকালের' }} লিডারবোর্ড
                        </h1>
                        <p class="text-red-200 text-sm font-semibold">সেরা রক্তদাতাদের সম্মানের তালিকা</p>
                    </div>
                </div>
                <p class="text-red-100 text-sm max-w-md">প্রতিটি রক্তদান জীবন বাঁচায়। এখানে আছেন বাংলাদেশের সবচেয়ে নিবেদিতপ্রাণ রক্তবীরেরা।</p>
            </div>

            {{-- Dynamic period + region badges --}}
            <div class="flex flex-wrap gap-2 shrink-0">
                {{-- Time badge --}}
                <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur border border-white/20 rounded-2xl px-4 py-2.5">
                    <span class="text-xl">{{ $time === 'month' ? '📅' : '⭐' }}</span>
                    <div class="text-left">
                        <div class="text-white font-black text-xs">{{ $time === 'month' ? 'মাসিক' : 'সর্বকালীন' }}</div>
                        @if($time === 'month')
                            <div class="text-red-200 text-[10px] font-semibold">{{ $currentMonth }}</div>
                        @else
                            <div class="text-red-200 text-[10px] font-semibold">All-Time</div>
                        @endif
                    </div>
                </div>

                {{-- Region badge --}}
                <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur border border-white/20 rounded-2xl px-4 py-2.5">
                    <span class="text-xl">{{ $scope === 'district' ? '📍' : '🇧🇩' }}</span>
                    <div class="text-left">
                        <div class="text-white font-black text-xs">
                            @if($scope === 'district' && $selectedDistrict)
                                {{ $selectedDistrict->name }}
                            @elseif($scope === 'district')
                                জেলা
                            @else
                                বাংলাদেশ
                            @endif
                        </div>
                        <div class="text-red-200 text-[10px] font-semibold">
                            {{ $scope === 'district' ? 'জেলা র‌্যাঙ্কিং' : 'জাতীয় র‌্যাঙ্কিং' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         Filter Cards — Two Independent Groups
    ══════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">

        {{-- ── Card A: সময়কাল ────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                <span>⏱</span> সময়কাল
            </p>
            <div class="flex bg-slate-100 rounded-xl p-1 gap-1">
                @php
                    $baseParams = array_filter(request()->query(), fn($v) => $v !== '');
                @endphp

                {{-- সর্বকাল tab --}}
                <a href="{{ route('leaderboard', array_merge($baseParams, ['time' => 'all'])) }}"
                   id="tab-time-all"
                   class="flex-1 text-center text-sm font-bold py-2.5 px-3 rounded-lg transition-all duration-200
                          {{ $time === 'all'
                              ? 'bg-white text-red-600 shadow-sm'
                              : 'text-slate-500 hover:text-slate-700' }}">
                    ⭐ সর্বকাল
                </a>

                {{-- এই মাস tab --}}
                <a href="{{ route('leaderboard', array_merge($baseParams, ['time' => 'month'])) }}"
                   id="tab-time-month"
                   class="flex-1 text-center text-sm font-bold py-2.5 px-3 rounded-lg transition-all duration-200
                          {{ $time === 'month'
                              ? 'bg-white text-red-600 shadow-sm'
                              : 'text-slate-500 hover:text-slate-700' }}">
                    📅 এই মাস
                </a>
            </div>
        </div>

        {{-- ── Card B: অঞ্চল ──────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                <span>📍</span> অঞ্চল
            </p>
            <div class="flex flex-col gap-2">
                {{-- Tab row --}}
                <div class="flex bg-slate-100 rounded-xl p-1 gap-1">
                    {{-- বাংলাদেশ tab --}}
                    <a href="{{ route('leaderboard', array_merge($baseParams, ['scope' => 'bd', 'district_id' => ''])) }}"
                       id="tab-scope-bd"
                       class="flex-1 text-center text-sm font-bold py-2.5 px-3 rounded-lg transition-all duration-200
                              {{ $scope === 'bd'
                                  ? 'bg-white text-red-600 shadow-sm'
                                  : 'text-slate-500 hover:text-slate-700' }}">
                        🇧🇩 বাংলাদেশ
                    </a>

                    {{-- জেলা tab --}}
                    <a href="{{ route('leaderboard', array_merge($baseParams, ['scope' => 'district'])) }}"
                       id="tab-scope-district"
                       class="flex-1 text-center text-sm font-bold py-2.5 px-3 rounded-lg transition-all duration-200
                              {{ $scope === 'district'
                                  ? 'bg-white text-red-600 shadow-sm'
                                  : 'text-slate-500 hover:text-slate-700' }}">
                        📍 জেলা
                    </a>
                </div>

                {{-- District dropdown — শুধু জেলা scope-এ দেখাবে --}}
                @if($scope === 'district')
                    <form method="GET" action="{{ route('leaderboard') }}" class="w-full">
                        <input type="hidden" name="scope" value="district">
                        <input type="hidden" name="time"  value="{{ $time }}">
                        <select name="district_id"
                                id="district-select"
                                onchange="this.form.submit()"
                                class="w-full text-sm font-semibold border border-slate-200 rounded-xl px-4 py-2.5
                                       bg-white text-slate-700 focus:ring-2 focus:ring-red-500 focus:border-transparent cursor-pointer">
                            <option value="">— জেলা বেছে নিন —</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}" {{ $districtId == $district->id ? 'selected' : '' }}>
                                    {{ $district->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         Top 3 Podium
    ══════════════════════════════════════════ --}}
    @if($top3->count() >= 3)
    @php
        $podiumConfig = [
            0 => ['crown' => '👑', 'bg' => 'from-yellow-400 to-amber-500', 'border' => 'border-yellow-300', 'height' => 'h-36 sm:h-44', 'order' => 'sm:order-2 order-1'],
            1 => ['crown' => '🥈', 'bg' => 'from-slate-400 to-slate-500', 'border' => 'border-slate-300', 'height' => 'h-28 sm:h-36', 'order' => 'sm:order-1 order-2'],
            2 => ['crown' => '🥉', 'bg' => 'from-amber-600 to-amber-700', 'border' => 'border-amber-400', 'height' => 'h-24 sm:h-32', 'order' => 'sm:order-3 order-3'],
        ];
        $podiumList = $top3->values();
        // Display order: 2nd (left), 1st (center), 3rd (right)
        $podiumDisplay = [1, 0, 2];
    @endphp

    <div class="mb-6">
        <div class="flex sm:grid sm:grid-cols-3 flex-col sm:flex-row gap-4 items-end sm:items-end">
            @foreach($podiumDisplay as $displayIndex)
                @if(isset($podiumList[$displayIndex]))
                @php $d = $podiumList[$displayIndex]; $cfg = $podiumConfig[$displayIndex]; @endphp
                <div class="flex flex-col items-center group {{ $cfg['order'] }} sm:w-auto w-full">
                    {{-- Avatar --}}
                    <div class="relative mb-3">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br {{ $cfg['bg'] }} flex items-center justify-center text-white font-black text-2xl shadow-lg border-2 {{ $cfg['border'] }} group-hover:scale-110 transition-transform duration-300 overflow-hidden">
                            @if($d->profile_image)
                                <img src="{{ asset('storage/' . $d->profile_image) }}" class="w-full h-full object-cover" alt="{{ $d->name }}">
                            @else
                                {{ mb_substr($d->name, 0, 1) }}
                            @endif
                        </div>
                        <div class="absolute -top-3 -right-2 text-xl drop-shadow">{{ $cfg['crown'] }}</div>
                    </div>

                    {{-- Name & info --}}
                    <div class="text-center mb-2 w-full px-2">
                        <div class="font-black text-slate-800 text-xs sm:text-sm truncate">{{ explode(' ', $d->name)[0] }}</div>
                        <div class="text-xs text-slate-500 font-semibold">
                            {{ $d->blood_group?->value ?? $d->blood_group ?? 'N/A' }}
                        </div>
                        @if($d->badges->count() > 0)
                            <div class="flex justify-center gap-0.5 mt-1 flex-wrap">
                                @foreach($d->badges->take(3) as $badge)
                                    <span class="text-xs" title="{{ $badge->bn_name ?? $badge->name }}">{{ $badge->emoji ?? $badge->icon }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Podium platform --}}
                    <div class="{{ $cfg['height'] }} w-full rounded-t-2xl bg-gradient-to-b {{ $cfg['bg'] }} flex flex-col items-center justify-start pt-3 shadow-lg">
                        <div class="text-white font-black text-lg sm:text-xl">
                            {{ $time === 'month' ? number_format($d->monthly_points ?? 0) : number_format($d->points ?? 0) }}
                        </div>
                        <div class="text-white/80 text-[10px] sm:text-xs font-semibold">{{ $pointsLabel }}</div>
                        <div class="text-white/70 text-[10px] font-bold mt-1">{{ $d->total_verified_donations ?? 0 }} রক্তদান</div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════
         Full Rankings List — Top 50
    ══════════════════════════════════════════ --}}
    <div x-data="{ showAll: false }" class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        {{-- List header --}}
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/60 ">
            <h2 class="font-black text-slate-800 flex items-center gap-2 text-sm sm:text-base">
                <span>📋</span>
                শীর্ষ ১০ তালিকা
                <span class="text-xs font-semibold text-slate-500 ">—</span>
                <span class="text-xs font-semibold text-slate-500 ">
                    {{ $time === 'month' ? 'মাসিক পয়েন্ট' : 'সর্বকালীন' }}
                    •
                    {{ $scope === 'district' && $selectedDistrict ? $selectedDistrict->name : 'বাংলাদেশ' }}
                </span>
                <span class="ml-auto text-xs font-semibold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full">
                    {{ $donors->count() }} জন
                </span>
            </h2>
        </div>

        @forelse($donors as $index => $donor)
            @php
                $rank       = $index + 1;
                $isPlatinum = ($donor->total_verified_donations >= 20 || ($donor->points ?? 0) >= 1500);
                $isMe       = Auth::check() && Auth::id() === $donor->id;
                $displayPts = $time === 'month' ? ($donor->monthly_points ?? 0) : ($donor->points ?? 0);
            @endphp
            <div @if($index >= 10) x-show="showAll" x-cloak style="display: none;" @endif 
                 class="flex items-center gap-3 sm:gap-4 px-4 sm:px-5 py-3.5 border-b border-slate-50 transition-colors duration-200
                        {{ $isMe       ? 'bg-blue-50/70 border-l-4 border-l-blue-400' : 'hover:bg-slate-50/60' }}
                        {{ $isPlatinum && !$isMe ? 'bg-purple-50/20 ' : '' }}">

                {{-- Rank badge --}}
                <div class="flex-shrink-0 w-8 text-center">
                    @if($rank === 1)     <span class="text-xl leading-none">🥇</span>
                    @elseif($rank === 2) <span class="text-xl leading-none">🥈</span>
                    @elseif($rank === 3) <span class="text-xl leading-none">🥉</span>
                    @else <span class="text-sm font-black text-slate-400 ">#{{ $rank }}</span>
                    @endif
                </div>

                {{-- Avatar --}}
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-gradient-to-br from-red-100 to-red-200  flex items-center justify-center overflow-hidden
                            {{ $isPlatinum ? 'ring-2 ring-purple-300 ring-offset-1' : '' }}
                            {{ $isMe ? 'ring-2 ring-blue-400 ring-offset-1' : '' }}">
                    @if($donor->profile_image)
                        <img src="{{ asset('storage/' . $donor->profile_image) }}" class="w-full h-full object-cover" alt="{{ $donor->name }}">
                    @else
                        <span class="text-red-700 font-black text-sm">{{ mb_substr($donor->name, 0, 1) }}</span>
                    @endif
                </div>

                {{-- Name, blood group, badges, district --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="font-black text-slate-800 text-sm truncate {{ $isPlatinum ? 'text-purple-800 ' : '' }}">
                            {{ $donor->name }}
                        </span>
                        @if($isMe)
                            <span class="inline-flex items-center text-[10px] font-black text-blue-700 bg-blue-100 border border-blue-200 rounded-full px-1.5 py-0.5">
                                আপনি
                            </span>
                        @endif
                        @if($isPlatinum)
                            <span class="inline-flex items-center text-[10px] font-black text-purple-700 bg-purple-100 border border-purple-200 rounded-full px-1.5 py-0.5">
                                ✨ প্লাটিনাম
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                        {{-- Blood group chip --}}
                        <span class="text-[10px] font-black text-red-700 bg-red-50 border border-red-100 rounded-full px-2 py-0.5">
                            {{ $donor->blood_group?->value ?? $donor->blood_group ?? 'N/A' }}
                        </span>
                        @if($donor->district)
                            <span class="text-[10px] text-slate-500 font-semibold flex items-center gap-0.5">
                                📍 {{ $donor->district->name }}
                            </span>
                        @endif
                        @foreach($donor->badges->take(3) as $badge)
                            @php $bd = \App\Services\GamificationService::getBadgeDisplayData($badge->name); @endphp
                            <span class="hidden sm:inline-flex items-center gap-0.5 text-[10px] font-bold {{ $bd['color'] }} border rounded-full px-1.5 py-0.5"
                                  title="{{ $badge->bn_name ?? $badge->name }}">
                                {{ $bd['emoji'] }} {{ $badge->bn_name ?? $badge->name }}
                            </span>
                        @endforeach
                    </div>
                </div>

                {{-- Points & donations --}}
                <div class="flex-shrink-0 text-right">
                    <div class="font-black text-slate-800 text-base tabular-nums">
                        {{ number_format($displayPts) }}
                    </div>
                    <div class="text-[10px] text-slate-400 font-semibold">{{ $pointsLabel }}</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">
                        {{ $donor->total_verified_donations ?? 0 }} রক্তদান
                    </div>
                </div>
            </div>

        @empty
            {{-- Empty state — differentiated by mode --}}
            <div class="py-16 text-center px-6">
                <div class="text-5xl mb-4">
                    @if($scope === 'district' && !$districtId)
                        🗺️
                    @elseif($time === 'month')
                        📅
                    @else
                        🩸
                    @endif
                </div>
                @if($scope === 'district' && !$districtId)
                    <p class="text-slate-600 font-bold text-lg">জেলা বেছে নিন</p>
                    <p class="text-slate-400 text-sm mt-1">উপরের ড্রপডাউন থেকে একটি জেলা সিলেক্ট করুন।</p>
                @elseif($time === 'month' && $scope === 'district')
                    <p class="text-slate-600 font-bold text-lg">এই মাসে এখনো কেউ নেই</p>
                    <p class="text-slate-400 text-sm mt-1">
                        {{ $selectedDistrict ? $selectedDistrict->name . ' জেলায়' : 'এই জেলায়' }} এই মাসে এখনো কোনো রক্তদান লগ হয়নি।
                    </p>
                @elseif($time === 'month')
                    <p class="text-slate-600 font-bold text-lg">এই মাসে এখনো কোনো তথ্য নেই</p>
                    <p class="text-slate-400 text-sm mt-1">{{ $currentMonth }} মাসে রক্তদান করুন এবং প্রথম হোন!</p>
                @else
                    <p class="text-slate-600 font-bold text-lg">এখনো কোনো তথ্য নেই</p>
                    <p class="text-slate-400 text-sm mt-1">রক্তদান করুন এবং তালিকায় আপনার নাম যুক্ত করুন!</p>
                @endif
            </div>
        @endforelse

        @if($donors->count() > 10)
            <div x-show="!showAll" class="p-4 text-center border-b border-slate-50">
                <button @click="showAll = true" type="button" class="inline-flex items-center gap-2 text-sm font-bold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 transition-colors rounded-full px-5 py-2.5">
                    ⬇️ আরো দেখুন (Top 50)
                </button>
            </div>
        @endif

        {{-- ── "আপনার অবস্থান" sticky row — লগইন ইউজার top 50-এ না থাকলে ── --}}
        @auth
            @php
                $authId      = Auth::id();
                $inTopList   = $donors->contains('id', $authId);
                $hasActivity = (Auth::user()->points ?? 0) > 0 || (Auth::user()->total_verified_donations ?? 0) > 0;
            @endphp
            @if(!$inTopList && $hasActivity && $myRank !== null)
                <div class="flex items-center gap-3 px-5 py-2 bg-slate-100 ">
                    <div class="flex-1 h-px bg-slate-300 "></div>
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest whitespace-nowrap">আপনার অবস্থান</span>
                    <div class="flex-1 h-px bg-slate-300 "></div>
                </div>

                <div class="flex items-center gap-4 px-5 py-4 bg-gradient-to-r from-blue-50 to-indigo-50  border-l-4 border-l-blue-500 sticky bottom-0">
                    <div class="flex-shrink-0 w-8 text-center">
                        <span class="text-sm font-black text-blue-600 ">#{{ $myRank }}</span>
                    </div>
                    <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center overflow-hidden ring-2 ring-blue-400 ring-offset-1">
                        @if(Auth::user()->profile_image)
                            <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" class="w-full h-full object-cover" alt="{{ Auth::user()->name }}">
                        @else
                            <span class="text-blue-700 font-black text-sm">{{ mb_substr(Auth::user()->name, 0, 1) }}</span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-black text-blue-900 text-sm truncate">{{ Auth::user()->name }}</span>
                            <span class="text-[10px] font-black text-blue-600 bg-blue-100 border border-blue-200 rounded-full px-1.5 py-0.5">আপনি</span>
                        </div>
                        <p class="text-[11px] text-blue-600 font-semibold mt-0.5">
                            আরো {{ max(0, $myRank - 50) }} ধাপ এগিয়ে শীর্ষ ৫০-এ আসুন!
                        </p>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        <div class="font-black text-blue-800 text-base tabular-nums">
                            {{ number_format($myPoints ?? 0) }}
                        </div>
                        <div class="text-[10px] text-blue-500 font-semibold">{{ $pointsLabel }}</div>
                    </div>
                </div>
            @endif
        @endauth
    </div>

    {{-- ══════════════════════════════════════════
         Footer link row
    ══════════════════════════════════════════ --}}
    <div class="mt-5 flex items-center justify-center">
        <a href="{{ route('gamification.guide') }}"
           class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-red-600 transition-colors border border-slate-200 hover:border-red-200 bg-white rounded-xl px-5 py-2.5 shadow-sm">
            🪙 পয়েন্ট ও ব্যাজ সিস্টেম সম্পর্কে জানুন
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

</div>
@endsection
