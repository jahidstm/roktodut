@extends('layouts.app')

@section('title', 'লিডারবোর্ড – রক্তদূত')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8 sm:py-12">

    {{-- ══════════════════════════════════════════
         Hero Section
    ══════════════════════════════════════════ --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-red-600 via-red-700 to-red-900 p-8 sm:p-12 mb-8 shadow-2xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/3"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4"></div>

        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center text-2xl shadow-lg">🏆</div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-black text-white leading-tight">রক্তদূত লিডারবোর্ড</h1>
                        <p class="text-red-200 text-sm font-semibold">সেরা রক্তদাতাদের সম্মানের তালিকা</p>
                    </div>
                </div>
                <p class="text-red-100 text-sm max-w-md">প্রতিটি রক্তদান জীবন বাঁচায়। এখানে আছেন বাংলাদেশের সবচেয়ে নিবেদিতপ্রাণ রক্তবীরেরা।</p>
            </div>

            {{-- Period Badge --}}
            <div class="text-center shrink-0">
                @if ($period === 'monthly')
                    <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur border border-white/20 rounded-2xl px-5 py-3">
                        <span class="text-2xl">📅</span>
                        <div class="text-left">
                            <div class="text-white font-black text-sm">মাসিক র্যাঙ্কিং</div>
                            <div class="text-red-200 text-xs font-semibold">{{ $currentMonth }}</div>
                        </div>
                    </div>
                @else
                    <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur border border-white/20 rounded-2xl px-5 py-3">
                        <span class="text-2xl">⭐</span>
                        <div class="text-left">
                            <div class="text-white font-black text-sm">সর্বকালের র্যাঙ্কিং</div>
                            <div class="text-red-200 text-xs font-semibold">All-Time Leaderboard</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         Functional Filter Bar (Query String based)
         গ্রুপ ১: Time  |  গ্রুপ ২: Location
    ══════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mb-6 space-y-3">

        {{-- গ্রুপ ১: সময়ের ফিল্টার --}}
        <div>
            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2">⏱ সময়কাল</p>
            <div class="flex bg-slate-100 rounded-xl p-1 gap-1">
                <a href="{{ route('leaderboard', array_merge(request()->query(), ['period' => 'all_time'])) }}"
                   class="flex-1 text-center text-sm font-bold py-2 px-4 rounded-lg transition-all
                          {{ $period === 'all_time' ? 'bg-white text-red-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                    ⭐ সর্বকালের সেরা
                </a>
                <a href="{{ route('leaderboard', array_merge(request()->query(), ['period' => 'monthly'])) }}"
                   class="flex-1 text-center text-sm font-bold py-2 px-4 rounded-lg transition-all
                          {{ $period === 'monthly' ? 'bg-white text-red-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                    📅 এই মাসের
                </a>
            </div>
        </div>

        {{-- গ্রুপ ২: অবস্থানের ফিল্টার --}}
        <div>
            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2">📍 অবস্থান</p>
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="flex bg-slate-100 rounded-xl p-1 gap-1 flex-1">
                    <a href="{{ route('leaderboard', array_merge(request()->query(), ['scope' => 'national', 'district_id' => ''])) }}"
                       class="flex-1 text-center text-sm font-bold py-2 px-4 rounded-lg transition-all
                              {{ $scope === 'national' ? 'bg-white text-red-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                        🇧🇩 জাতীয়
                    </a>
                    <a href="{{ route('leaderboard', array_merge(request()->query(), ['scope' => 'district'])) }}"
                       class="flex-1 text-center text-sm font-bold py-2 px-4 rounded-lg transition-all
                              {{ $scope === 'district' ? 'bg-white text-red-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                        📍 জেলা
                    </a>
                </div>

                {{-- জেলা ড্রপডাউন — শুধু district scope-এ দেখাবে --}}
                @if($scope === 'district')
                    <form method="GET" action="{{ route('leaderboard') }}" class="flex-1">
                        <input type="hidden" name="scope" value="district">
                        <input type="hidden" name="period" value="{{ $period }}">
                        <select name="district_id" onchange="this.form.submit()"
                            class="w-full text-sm font-semibold border border-slate-200 rounded-xl px-4 py-2.5 bg-white text-slate-700 focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">-- জেলা বেছে নিন --</option>
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
    @if($donors->count() >= 3)
    <div class="grid grid-cols-3 gap-4 mb-6">
        @php
            $podiumOrder  = [1, 0, 2];
            $podiumConfig = [
                0 => ['height' => 'h-36 sm:h-44', 'crown' => '👑', 'bg' => 'from-yellow-400 to-amber-500', 'border' => 'border-yellow-300', 'rank_bg' => 'bg-yellow-500'],
                1 => ['height' => 'h-28 sm:h-36', 'crown' => '🥈', 'bg' => 'from-slate-400 to-slate-500', 'border' => 'border-slate-300', 'rank_bg' => 'bg-slate-500'],
                2 => ['height' => 'h-24 sm:h-32', 'crown' => '🥉', 'bg' => 'from-amber-600 to-amber-700', 'border' => 'border-amber-300', 'rank_bg' => 'bg-amber-700'],
            ];
            $donorsList = $donors->values();
        @endphp

        @foreach($podiumOrder as $realIndex => $displayIndex)
            @if(isset($donorsList[$displayIndex]))
            @php $d = $donorsList[$displayIndex]; $cfg = $podiumConfig[$displayIndex]; @endphp
            <div class="flex flex-col items-center group" style="order: {{ $displayIndex === 0 ? 2 : ($displayIndex === 1 ? 1 : 3) }}">
                <div class="relative mb-3">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br {{ $cfg['bg'] }} flex items-center justify-center text-white font-black text-2xl shadow-lg border-2 {{ $cfg['border'] }} group-hover:scale-110 transition-transform duration-300">
                        @if($d->profile_image)
                            <img src="{{ asset('storage/' . $d->profile_image) }}" class="w-full h-full object-cover rounded-2xl" alt="{{ $d->name }}">
                        @else
                            {{ mb_substr($d->name, 0, 1) }}
                        @endif
                    </div>
                    <div class="absolute -top-3 -right-2 text-xl">{{ $cfg['crown'] }}</div>
                </div>
                <div class="text-center mb-2 w-full px-1">
                    <div class="font-black text-slate-800 text-xs sm:text-sm truncate">{{ explode(' ', $d->name)[0] }}</div>
                    <div class="text-xs text-slate-500 font-semibold">{{ $d->blood_group?->value ?? $d->blood_group ?? 'N/A' }}</div>
                    @if($d->badges->count() > 0)
                        <div class="flex justify-center gap-0.5 mt-1 flex-wrap">
                            @foreach($d->badges->take(3) as $badge)
                                <span class="text-xs" title="{{ $badge->bn_name ?? $badge->name }}">{{ $badge->emoji ?? $badge->icon }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="{{ $cfg['height'] }} w-full rounded-t-2xl bg-gradient-to-b {{ $cfg['bg'] }} flex flex-col items-center justify-start pt-3 shadow-lg">
                    <div class="text-white font-black text-lg sm:text-xl">{{ $d->total_verified_donations ?? 0 }}</div>
                    <div class="text-white/80 text-[10px] sm:text-xs font-semibold">রক্তদান</div>
                    <div class="text-white/70 text-[10px] font-bold mt-1">{{ number_format($d->points ?? 0) }} pts</div>
                </div>
            </div>
            @endif
        @endforeach
    </div>
    @endif

    {{-- ══════════════════════════════════════════
         Full Rankings List — Top 10
    ══════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/60">
            <h2 class="font-black text-slate-800 flex items-center gap-2">
                <span>📋</span>
                টপ ১০ তালিকা
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
            @endphp
            <div class="flex items-center gap-4 px-5 py-4 border-b border-slate-50 transition-colors duration-200
                        {{ $isMe       ? 'bg-blue-50/60 border-l-4 border-l-blue-400' : 'hover:bg-slate-50/50' }}
                        {{ $isPlatinum ? 'bg-purple-50/30' : '' }}">

                {{-- Rank --}}
                <div class="flex-shrink-0 w-8 text-center">
                    @if($rank === 1) <span class="text-xl">🥇</span>
                    @elseif($rank === 2) <span class="text-xl">🥈</span>
                    @elseif($rank === 3) <span class="text-xl">🥉</span>
                    @else <span class="text-sm font-black text-slate-400">#{{ $rank }}</span>
                    @endif
                </div>

                {{-- Avatar --}}
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-gradient-to-br from-red-100 to-red-200 flex items-center justify-center overflow-hidden {{ $isPlatinum ? 'ring-2 ring-purple-300 ring-offset-1' : '' }} {{ $isMe ? 'ring-2 ring-blue-400 ring-offset-1' : '' }}">
                    @if($donor->profile_image)
                        <img src="{{ asset('storage/' . $donor->profile_image) }}" class="w-full h-full object-cover" alt="{{ $donor->name }}">
                    @else
                        <span class="text-red-700 font-black text-sm">{{ mb_substr($donor->name, 0, 1) }}</span>
                    @endif
                </div>

                {{-- Name & Badges --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="font-black text-slate-800 text-sm {{ $isPlatinum ? 'text-purple-800' : '' }} truncate">
                            {{ $donor->name }}
                        </span>
                        @if($isMe)
                            <span class="inline-flex items-center text-[10px] font-black text-blue-700 bg-blue-100 border border-blue-200 rounded-full px-1.5 py-0.5">
                                আপনি
                            </span>
                        @endif
                        @if($isPlatinum)
                            <span class="inline-flex items-center text-[10px] font-black text-purple-700 bg-purple-100 border border-purple-200 rounded-full px-1.5 py-0.5 animate-pulse">
                                ✨ Platinum
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                        <span class="text-[10px] font-black text-red-700 bg-red-50 border border-red-100 rounded-full px-2 py-0.5">
                            {{ $donor->blood_group?->value ?? $donor->blood_group ?? 'N/A' }}
                        </span>
                        @if($donor->district)
                            <span class="text-[10px] text-slate-500 font-semibold flex items-center gap-0.5">
                                📍 {{ $donor->district->name }}
                            </span>
                        @endif
                        @foreach($donor->badges as $badge)
                            @php $bd = \App\Services\GamificationService::getBadgeDisplayData($badge->name); @endphp
                            <span class="inline-flex items-center gap-0.5 text-[10px] font-bold {{ $bd['color'] }} border rounded-full px-1.5 py-0.5" title="{{ $badge->bn_name ?? $badge->name }}">
                                {{ $bd['emoji'] }} {{ $badge->bn_name ?? $badge->name }}
                            </span>
                        @endforeach
                    </div>
                </div>

                {{-- Stats --}}
                <div class="flex-shrink-0 text-right">
                    <div class="font-black text-slate-800 text-base">
                        {{ $donor->total_verified_donations ?? 0 }}
                        <span class="text-[10px] font-semibold text-slate-400">বার</span>
                    </div>
                    <div class="text-[11px] text-slate-400 font-bold">
                        {{ number_format($period === 'monthly' ? ($donor->monthly_points ?? 0) : ($donor->points ?? 0)) }} pts
                    </div>
                </div>
            </div>
        @empty
            <div class="py-16 text-center">
                <div class="text-5xl mb-4">🩸</div>
                <p class="text-slate-500 font-bold">এখনো কোনো তথ্য নেই।</p>
                <p class="text-slate-400 text-sm mt-1">রক্তদান করুন এবং তালিকায় আপনার নাম যুক্ত করুন!</p>
            </div>
        @endforelse

        {{-- ══════════════════════════════════════════
             Sticky "আপনার অবস্থান" Row
             শুধু লগইন ইউজার টপ ১০-এ না থাকলে দেখাবে
        ══════════════════════════════════════════ --}}
        @auth
            @php
                $authId     = Auth::id();
                $inTop10    = $donors->contains('id', $authId);
                $hasActivity = (Auth::user()->points ?? 0) > 0 || (Auth::user()->total_verified_donations ?? 0) > 0;
            @endphp
            @if(!$inTop10 && $hasActivity && $myRank !== null)
                {{-- Divider --}}
                <div class="flex items-center gap-3 px-5 py-2 bg-slate-100">
                    <div class="flex-1 h-px bg-slate-300"></div>
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">আপনার অবস্থান</span>
                    <div class="flex-1 h-px bg-slate-300"></div>
                </div>

                {{-- Sticky My Rank Row --}}
                <div class="flex items-center gap-4 px-5 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-l-blue-500 sticky bottom-0">
                    <div class="flex-shrink-0 w-8 text-center">
                        <span class="text-sm font-black text-blue-600">#{{ $myRank }}</span>
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
                            আরো {{ max(0, $myRank - 10) }} ধাপ এগিয়ে টপ ১০-এ আসুন!
                        </p>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        <div class="font-black text-blue-800 text-base">
                            {{ Auth::user()->total_verified_donations ?? 0 }}
                            <span class="text-[10px] font-semibold text-blue-400">বার</span>
                        </div>
                        <div class="text-[11px] text-blue-500 font-bold">{{ number_format($myPoints ?? 0) }} pts</div>
                    </div>
                </div>
            @endif
        @endauth
    </div>

    {{-- ══════════════════════════════════════════
         "Show More" / Load More লিঙ্ক
    ══════════════════════════════════════════ --}}
    <div class="mt-5 flex flex-col sm:flex-row items-center justify-between gap-3">
        <a href="{{ route('gamification.guide') }}"
           class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-red-600 transition-colors border border-slate-200 hover:border-red-200 bg-white rounded-xl px-4 py-2.5 shadow-sm">
            🪙 পয়েন্ট ও ব্যাজ সিস্টেম সম্পর্কে জানুন
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>

        {{-- পরের ব্যাচ লোড করার লিঙ্ক (সহজ "Show More" — full page reload) --}}
        @if($donors->count() >= 10)
            <a href="{{ route('leaderboard', array_merge(request()->query(), ['limit' => 50])) }}"
               class="inline-flex items-center gap-2 text-sm font-bold text-white bg-red-600 hover:bg-red-700 transition rounded-xl px-5 py-2.5 shadow-sm">
                🔽 আরো দেখুন (Top 50)
            </a>
        @endif
    </div>

</div>
@endsection
