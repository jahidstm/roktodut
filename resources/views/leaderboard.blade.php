@extends('layouts.app')

@section('title', 'সেরা রক্তদাতা – রক্তদূত')

@section('content')
@php
    if (!function_exists('getAvatarInitials')) {
        function getAvatarInitials($name) {
            $name = trim($name);
            if (preg_match('/[a-zA-Z]/', $name)) {
                $words = explode(' ', $name);
                $initials = '';
                foreach ($words as $w) {
                    if (!empty($w)) $initials .= mb_substr($w, 0, 1);
                }
                return mb_strtoupper(mb_substr($initials, 0, 2));
            }
            return mb_substr($name, 0, 1);
        }
    }

    if (!function_exists('getAvatarColor')) {
        function getAvatarColor($name) {
            $colors = [
                'bg-blue-100 text-blue-700',
                'bg-emerald-100 text-emerald-700',
                'bg-amber-100 text-amber-700',
                'bg-purple-100 text-purple-700',
                'bg-pink-100 text-pink-700',
                'bg-indigo-100 text-indigo-700',
                'bg-rose-100 text-rose-700',
                'bg-teal-100 text-teal-700',
                'bg-cyan-100 text-cyan-700',
                'bg-fuchsia-100 text-fuchsia-700',
            ];
            $index = abs(crc32($name)) % count($colors);
            return $colors[$index];
        }
    }
@endphp
<div id="leaderboard-root" class="max-w-5xl mx-auto px-4 py-8 sm:py-12" data-panel-id="leaderboard">

    {{-- ══════════════════════════════════════════
         Hero Section — dynamic based on filters
    ══════════════════════════════════════════ --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-red-600 via-red-700 to-red-900 p-8 sm:p-12 mb-6 shadow-2xl scroll-reveal hide-in-dashboard" data-scroll-reveal>
        {{-- Decorative blobs --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4 pointer-events-none"></div>

        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center text-2xl shadow-lg">🏆</div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-black text-white leading-tight">
                            {{ $time === 'month' ? 'এই মাসের' : 'সর্বকালের' }} সেরা রক্তদাতা
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
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6 hide-in-dashboard">

        {{-- ── Card A: সময়কাল ────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 scroll-reveal" data-scroll-reveal>
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
                   data-leaderboard-tab="1"
                   class="flex-1 text-center text-sm font-bold py-2.5 px-3 rounded-lg transition-all duration-200
                          {{ $time === 'all'
                              ? 'bg-red-600 text-white shadow-md'
                              : 'bg-slate-200 text-slate-600 hover:bg-slate-300 hover:text-slate-800' }}">
                    ⭐ সর্বকাল
                </a>

                {{-- এই মাস tab --}}
                <a href="{{ route('leaderboard', array_merge($baseParams, ['time' => 'month'])) }}"
                   id="tab-time-month"
                   data-leaderboard-tab="1"
                   class="flex-1 text-center text-sm font-bold py-2.5 px-3 rounded-lg transition-all duration-200
                          {{ $time === 'month'
                              ? 'bg-red-600 text-white shadow-md'
                              : 'bg-slate-200 text-slate-600 hover:bg-slate-300 hover:text-slate-800' }}">
                    📅 এই মাস
                </a>
            </div>
        </div>

        {{-- ── Card B: অঞ্চল ──────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 scroll-reveal" data-scroll-reveal>
            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                <span>📍</span> অঞ্চল
            </p>
            <div class="flex flex-col gap-2">
                {{-- Tab row --}}
                <div class="flex bg-slate-100 rounded-xl p-1 gap-1">
                    {{-- বাংলাদেশ tab --}}
                    <a href="{{ route('leaderboard', array_merge($baseParams, ['scope' => 'bd', 'district_id' => ''])) }}"
                       id="tab-scope-bd"
                       data-leaderboard-tab="1"
                       class="flex-1 text-center text-sm font-bold py-2.5 px-3 rounded-lg transition-all duration-200
                              {{ $scope === 'bd'
                                  ? 'bg-red-600 text-white shadow-md'
                                  : 'bg-slate-200 text-slate-600 hover:bg-slate-300 hover:text-slate-800' }}">
                        🇧🇩 বাংলাদেশ
                    </a>

                    {{-- জেলা tab --}}
                    <a href="{{ route('leaderboard', array_merge($baseParams, ['scope' => 'district'])) }}"
                       id="tab-scope-district"
                       data-leaderboard-tab="1"
                       class="flex-1 text-center text-sm font-bold py-2.5 px-3 rounded-lg transition-all duration-200
                              {{ $scope === 'district'
                                  ? 'bg-red-600 text-white shadow-md'
                                  : 'bg-slate-200 text-slate-600 hover:bg-slate-300 hover:text-slate-800' }}">
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
         Top 3 Podium (Home Page Style)
    ══════════════════════════════════════════ --}}
    @if($top3->count() >= 3)
    @php
        $orderedDonors = collect();
        if($top3->has(0)) $orderedDonors->push(['donor'=>$top3[0],'rank'=>1,'emoji'=>'🥇','label'=>'১ম স্থান']);
        if($top3->has(1)) $orderedDonors->push(['donor'=>$top3[1],'rank'=>2,'emoji'=>'🥈','label'=>'২য় স্থান']);
        if($top3->has(2)) $orderedDonors->push(['donor'=>$top3[2],'rank'=>3,'emoji'=>'🥉','label'=>'৩য় স্থান']);
    @endphp

    <div class="flex flex-col sm:flex-row items-stretch sm:items-end justify-center gap-6 mb-16 mt-6 hide-in-dashboard">
        @foreach($orderedDonors as $item)
            @php
                $d = $item['donor'];
                $rank = $item['rank'];
                $initial = mb_strtoupper(mb_substr($d->name, 0, 1));
                $isFirst = $rank === 1;
                $cardClasses = $isFirst
                    ? "border-amber-200 shadow-[0_20px_60px_rgba(251,191,36,0.12)] sm:scale-110 pb-8 pt-12 z-10"
                    : "border-slate-100 shadow-sm pb-6 pt-10 mt-0 sm:mt-8";
                $avatarRing = match($rank) { 1=>"ring-4 ring-amber-100 border-amber-300", 2=>"border-slate-200", 3=>"border-orange-200", default=>"border-slate-100" };
                $orderClass = match($rank) { 1=>"order-1 sm:order-2", 2=>"order-2 sm:order-1", 3=>"order-3 sm:order-3", default=>"order-4" };
            @endphp

            <div class="bg-white rounded-3xl px-6 w-full max-w-xs mx-auto sm:mx-0 flex flex-col items-center relative border-2 {{ $cardClasses }} {{ $orderClass }} transition-all duration-300 hover:-translate-y-2 scroll-reveal" data-scroll-reveal>
                <div class="absolute -top-4 sm:-top-5 bg-slate-900 text-white text-xs font-black px-5 py-2 rounded-full flex items-center gap-1.5 shadow-lg">
                    {{ $item['emoji'] }} {{ $item['label'] }}
                </div>
                <div class="w-20 h-20 rounded-full border-[3px] {{ $avatarRing }} flex items-center justify-center text-3xl font-black mb-5 overflow-hidden relative">
                    @php
                        $init = getAvatarInitials($d->name);
                        $color = getAvatarColor($d->name);
                    @endphp
                    @if($d->profile_image)
                        <img src="{{ route('profile.avatar', $d) }}" class="w-full h-full object-cover z-10" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <span class="w-full h-full hidden items-center justify-center {{ $color }}">{{ $init }}</span>
                    @else
                        <span class="w-full h-full flex items-center justify-center {{ $color }}">{{ $init }}</span>
                    @endif
                </div>
                <h3 class="text-lg font-black text-slate-900 text-center mb-2 truncate w-full">{{ $d->name }}</h3>
                @if($d->blood_group)
                <div class="bg-red-50 text-red-600 font-bold text-[10px] px-3 py-1 rounded-lg mb-5 uppercase tracking-widest border border-red-100">{{ $d->blood_group?->value ?? $d->blood_group }} ডোনার</div>
                @else
                <div class="h-6 mb-5"></div>
                @endif
                <div class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 flex items-center divide-x divide-slate-200 mb-5">
                    <div class="flex-1 text-center pr-4">
                        <div class="text-2xl font-black text-slate-900">
                            <x-number-ticker :value="$d->total_verified_donations ?? 0" :duration="2000" />
                        </div>
                        <div class="text-[10px] font-bold text-slate-400 mt-1">রক্তদান</div>
                    </div>
                    <div class="flex-1 text-center pl-4">
                        <div class="text-2xl font-black text-slate-900">
                            <x-number-ticker :value="$time === 'month' ? ($d->monthly_points ?? 0) : ($d->points ?? 0)" :duration="2500" :format="true" />
                        </div>
                        <div class="text-[10px] font-bold text-slate-400 mt-1">পয়েন্ট</div>
                    </div>
                </div>
                <div class="flex gap-2 justify-center">
                    @if($d->badges->isNotEmpty())
                        @foreach($d->badges->take(3) as $badge)
                            @php $bd = \App\Services\GamificationService::getBadgeDisplayData($badge->name); @endphp
                            {{-- Lottie Animation Structure for Podium Badges --}}
                            {{-- We fallback to emoji if the Lottie JSON path is empty or fails --}}
                            <div class="group relative cursor-help" title="{{ $bd['bn'] }}">
                                <lottie-player 
                                    src="{{ asset('animations/badges/' . strtolower($badge->name) . '.json') }}" 
                                    background="transparent" 
                                    speed="1" 
                                    style="width: 32px; height: 32px;" 
                                    hover loop
                                    {{-- If JSON is missing, show emoji --}}
                                    onError="this.style.display='none'; this.nextElementSibling.style.display='inline-block';"
                                ></lottie-player>
                                <span class="text-xl drop-shadow-sm hover:scale-125 transition-transform" style="display:none;">{{ $bd['emoji'] }}</span>
                            </div>
                        @endforeach
                    @else
                        <span class="text-xl opacity-20 grayscale">🎖️</span><span class="text-xl opacity-20 grayscale">🎖️</span><span class="text-xl opacity-20 grayscale">🎖️</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    @endif

    {{-- ══════════════════════════════════════════
         Full Rankings List (IELTSly Style)
    ══════════════════════════════════════════ --}}
    <div class="mb-4 flex items-center justify-between scroll-reveal" data-scroll-reveal>
        <div class="flex items-center gap-2">
            <span class="text-2xl drop-shadow-sm">🏆</span>
            <h2 class="text-xl font-black text-slate-800">শীর্ষ রক্তদাতা</h2>
        </div>
        <div class="text-xs font-bold text-slate-400 bg-white border border-slate-200 px-3 py-1.5 rounded-full shadow-sm">
            {{ $donors->count() }} জন ডোনার
        </div>
    </div>

    <div x-data="{ showAll: false }" class="bg-white rounded-3xl shadow-[0_8px_30px_rgba(0,0,0,0.04)] border border-slate-100 overflow-hidden mb-8 scroll-reveal" data-scroll-reveal>
        @forelse($donors as $index => $donor)
            @php
                $rank       = $index + 1;
                $isPlatinum = ($donor->total_verified_donations >= 20 || ($donor->points ?? 0) >= 1500);
                $isMe       = Auth::check() && Auth::id() === $donor->id;
                $displayPts = $time === 'month' ? ($donor->monthly_points ?? 0) : ($donor->points ?? 0);
            @endphp
            <div @if($index >= 10) x-show="showAll" x-cloak style="display: none;" @endif 
                 class="flex items-center gap-4 px-6 py-4 border-b border-slate-50 transition-colors duration-200 scroll-reveal
                        {{ $isMe ? 'bg-red-50/30' : 'hover:bg-slate-50/60' }}"
                 data-scroll-reveal>

                {{-- Rank badge --}}
                <div class="flex-shrink-0 w-10 text-center">
                    @if($rank === 1)     <span class="text-3xl drop-shadow-sm">🥇</span>
                    @elseif($rank === 2) <span class="text-3xl drop-shadow-sm">🥈</span>
                    @elseif($rank === 3) <span class="text-3xl drop-shadow-sm">🥉</span>
                    @else <span class="text-sm font-bold text-slate-400">#{{ $rank }}</span>
                    @endif
                </div>

                {{-- Avatar --}}
                <div class="flex-shrink-0 w-12 h-12 rounded-full border border-slate-200 flex items-center justify-center overflow-hidden relative">
                    @php
                        $init = getAvatarInitials($donor->name);
                        $color = getAvatarColor($donor->name);
                    @endphp
                    @if($donor->profile_image)
                        <img src="{{ route('profile.avatar', $donor) }}" class="w-full h-full object-cover z-10" alt="{{ $donor->name }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <span class="w-full h-full hidden items-center justify-center font-black text-sm {{ $color }}">{{ $init }}</span>
                    @else
                        <span class="w-full h-full flex items-center justify-center font-black text-sm {{ $color }}">{{ $init }}</span>
                    @endif
                </div>

                {{-- Name & Badges --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-800 text-base truncate">{{ $donor->name }}</span>
                        @if($isMe)
                            <span class="text-[10px] font-bold text-red-600 bg-red-100 px-1.5 py-0.5 rounded uppercase tracking-wider">You</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-1.5 mt-1">
                        @foreach($donor->badges->take(1) as $badge)
                            @php $bd = \App\Services\GamificationService::getBadgeDisplayData($badge->name); @endphp
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 rounded px-1.5 py-0.5">
                                {{ $bd['emoji'] }} {{ $badge->bn_name ?? $badge->name }}
                            </span>
                        @endforeach
                        @if($donor->badges->isEmpty())
                            <span class="text-[10px] font-bold text-slate-400 bg-slate-100 rounded px-1.5 py-0.5">রক্তদাতা</span>
                        @endif
                    </div>
                </div>

                {{-- Stats (রক্তদান & পয়েন্ট) --}}
                <div class="flex flex-col items-end gap-1 sm:flex-row sm:items-center sm:gap-6 text-right pr-2">
                    <div class="flex items-center gap-1 text-[11px] font-semibold text-slate-500 sm:hidden">
                        রক্তদান <span class="font-bold text-blue-600"><x-number-ticker :value="$donor->total_verified_donations ?? 0" /></span>
                    </div>
                    <div class="hidden sm:block">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">রক্তদান</div>
                        <div class="font-bold text-blue-600 text-sm"><x-number-ticker :value="$donor->total_verified_donations ?? 0" /></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">{{ $pointsLabel }}</div>
                        <div class="font-bold text-amber-600 text-sm flex items-center gap-1 justify-end">
                            <x-number-ticker :value="$displayPts" :format="true" />
                        </div>
                    </div>
                </div>
            </div>

        @empty
            {{-- Empty state --}}
            <div class="py-16 text-center px-6">
                <div class="text-5xl mb-4">🩸</div>
                <p class="text-slate-600 font-bold text-lg">এখনো কোনো তথ্য নেই</p>
                <p class="text-slate-400 text-sm mt-1">রক্তদান করুন এবং তালিকায় আপনার নাম যুক্ত করুন!</p>
            </div>
        @endforelse

        {{-- ── Current User Preview Row ── --}}
        @auth
            @php
                $myPts       = $time === 'month' ? (Auth::user()->monthly_points ?? 0) : (Auth::user()->points ?? 0);
                $myDons      = Auth::user()->total_verified_donations ?? 0;
                $hasActivity = $myPts > 0 || $myDons > 0;
                $isOutsideTop10 = $myRank === null || $myRank > 10;
                $isInTop50 = $myRank !== null && $myRank <= 50;
            @endphp
            @if($isOutsideTop10 && $hasActivity)
                <div @if($isInTop50) x-show="!showAll" x-cloak @endif class="flex items-center gap-4 px-6 py-4 bg-red-50/90 border-t border-red-100 transition-all duration-300">
                    {{-- Rank badge --}}
                    <div class="flex-shrink-0 w-10 text-center">
                        <span class="text-sm font-bold text-slate-400">#{{ $myRank ?? 'N/A' }}</span>
                    </div>

                    {{-- Avatar --}}
                    <div class="flex-shrink-0 w-12 h-12 rounded-full border border-red-200 flex items-center justify-center overflow-hidden relative">
                        @php
                            $init = getAvatarInitials(Auth::user()->name);
                            $color = getAvatarColor(Auth::user()->name);
                        @endphp
                        @if(Auth::user()->profile_image)
                            <img src="{{ route('profile.avatar', Auth::user()) }}" class="w-full h-full object-cover z-10" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <span class="w-full h-full hidden items-center justify-center font-black text-sm {{ $color }}">{{ $init }}</span>
                        @else
                            <span class="w-full h-full flex items-center justify-center font-black text-sm {{ $color }}">{{ $init }}</span>
                        @endif
                    </div>

                    {{-- Name & Badges --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-800 text-base truncate">{{ Auth::user()->name }}</span>
                            <span class="text-[10px] font-bold text-red-600 bg-red-100 px-1.5 py-0.5 rounded uppercase tracking-wider">You</span>
                        </div>
                        <div class="flex items-center gap-1.5 mt-1">
                            <p class="text-[11px] text-slate-500 font-semibold truncate">
                                @if($myRank && $myRank > 50)
                                    আরো {{ max(0, $myRank - 50) }} ধাপ এগিয়ে শীর্ষ ৫০-এ আসুন!
                                @else
                                    শীর্ষ ১০-এ আসতে রক্তদান করুন!
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="flex flex-col items-end gap-1 sm:flex-row sm:items-center sm:gap-6 text-right pr-2">
                        <div class="flex items-center gap-1 text-[11px] font-semibold text-slate-500 sm:hidden">
                            রক্তদান <span class="font-bold text-blue-600"><x-number-ticker :value="$myDons" /></span>
                        </div>
                        <div class="hidden sm:block">
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">রক্তদান</div>
                            <div class="font-bold text-blue-600 text-sm"><x-number-ticker :value="$myDons" /></div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">{{ $pointsLabel }}</div>
                            <div class="font-bold text-amber-600 text-sm flex items-center gap-1 justify-end">
                                <x-number-ticker :value="$myPts" :format="true" />
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endauth

        @if($donors->count() > 10)
            <div x-show="!showAll" class="p-4 text-center border-t border-slate-50 bg-slate-50/50">
                <button @click="showAll = true" type="button" class="inline-flex items-center gap-2 text-sm font-bold text-slate-600 hover:text-slate-900 transition-colors">
                    সম্পূর্ণ তালিকা দেখুন ⬇️
                </button>
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════
         Footer link row
    ══════════════════════════════════════════ --}}
    <div class="mt-5 flex items-center justify-center hide-in-dashboard">
        <a href="{{ route('gamification.guide') }}"
           class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-red-600 transition-colors border border-slate-200 hover:border-red-200 bg-white rounded-xl px-5 py-2.5 shadow-sm">
            🪙 পয়েন্ট ও ব্যাজ সিস্টেম সম্পর্কে জানুন
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

</div>
@push('scripts')
{{-- Lottie Player Script (Lightweight) --}}
<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

<script>
(() => {
    const root = document.getElementById('leaderboard-root');
    if (!root) return;

    const tabSelector = '[data-leaderboard-tab]';
    const pageCache = {};

    const loadLeaderboard = async (url, { pushState = true } = {}) => {
        try {
            // Check cache first for instant response
            if (pageCache[url]) {
                const nextRoot = pageCache[url];
                root.innerHTML = nextRoot.innerHTML;
                root.style.opacity = '1';
                root.style.pointerEvents = 'auto';
                
                if (pushState) {
                    window.history.pushState({ leaderboard: true }, '', url);
                }
                
                if (window.Alpine && typeof window.Alpine.initTree === 'function') {
                    window.Alpine.initTree(root);
                }
                if (typeof window.initScrollReveal === 'function') {
                    window.initScrollReveal(root);
                }
                return;
            }

            // Visual loading state
            root.style.opacity = '0.5';
            root.style.pointerEvents = 'none';
            root.style.transition = 'opacity 0.2s';

            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-Partial': 'leaderboard' },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                window.location.href = url;
                return;
            }

            const html = await response.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const nextRoot = doc.getElementById('leaderboard-root');

            if (!nextRoot) {
                window.location.href = url;
                return;
            }

            // Cache for future clicks
            pageCache[url] = nextRoot.cloneNode(true);

            root.innerHTML = nextRoot.innerHTML;
            
            // Remove loading state
            root.style.opacity = '1';
            root.style.pointerEvents = 'auto';

            if (pushState) {
                window.history.pushState({ leaderboard: true }, '', url);
            }

            if (window.Alpine && typeof window.Alpine.initTree === 'function') {
                window.Alpine.initTree(root);
            }
            if (typeof window.initScrollReveal === 'function') {
                window.initScrollReveal(root);
            }
        } catch (error) {
            window.location.href = url;
        }
    };

    document.addEventListener('click', (event) => {
        const link = event.target.closest(tabSelector);
        if (!link) return;
        if (!root.contains(link)) return;

        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        loadLeaderboard(link.href);
    });

    document.addEventListener('change', (event) => {
        if (event.target.id !== 'district-select') return;
        if (!root.contains(event.target)) return;

        const form = event.target.closest('form');
        if (!form) return;

        const params = new URLSearchParams(new FormData(form));
        const url = `${form.action}?${params.toString()}`;
        loadLeaderboard(url);
    });

    window.addEventListener('popstate', () => {
        loadLeaderboard(window.location.href, { pushState: false });
    });
})();
</script>
@endpush

@endsection
