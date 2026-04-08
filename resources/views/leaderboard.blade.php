@extends('layouts.app')

@section('title', 'লিডারবোর্ড – রক্তদূত')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8 sm:py-12">

    {{-- ══════════════════════════════════════════
         Hero Section
    ══════════════════════════════════════════ --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-red-600 via-red-700 to-red-900 p-8 sm:p-12 mb-8 shadow-2xl">
        {{-- Background decoration --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/3"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4"></div>

        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center text-2xl shadow-lg">
                        🏆
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-black text-white leading-tight">রক্তদূত লিডারবোর্ড</h1>
                        <p class="text-red-200 text-sm font-semibold">সেরা রক্তদাতাদের সম্মানের তালিকা</p>
                    </div>
                </div>
                <p class="text-red-100 text-sm max-w-md">
                    প্রতিটি রক্তদান জীবন বাঁচায়। এখানে আছেন বাংলাদেশের সবচেয়ে নিবেদিতপ্রাণ রক্তবীরেরা।
                </p>
            </div>
            {{-- Current Period Badge --}}
            <div class="text-center">
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
         Filter Bar
    ══════════════════════════════════════════ --}}
    <form method="GET" action="{{ route('leaderboard') }}" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3">

            {{-- Period Filter --}}
            <div class="flex bg-slate-100 rounded-xl p-1 gap-1 flex-1">
                <button type="submit" name="period" value="all_time"
                    class="flex-1 text-sm font-bold py-2 px-4 rounded-lg transition-all {{ $period === 'all_time' ? 'bg-white text-red-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                    ⭐ সর্বকালের
                </button>
                <button type="submit" name="period" value="monthly"
                    class="flex-1 text-sm font-bold py-2 px-4 rounded-lg transition-all {{ $period === 'monthly' ? 'bg-white text-red-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                    📅 এই মাসের
                </button>
            </div>

            {{-- Scope Filter --}}
            <div class="flex bg-slate-100 rounded-xl p-1 gap-1 flex-1">
                <button type="submit" name="scope" value="national"
                    class="flex-1 text-sm font-bold py-2 px-4 rounded-lg transition-all {{ $scope === 'national' ? 'bg-white text-red-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                    🇧🇩 জাতীয়
                </button>
                <button type="submit" name="scope" value="district"
                    class="flex-1 text-sm font-bold py-2 px-4 rounded-lg transition-all {{ $scope === 'district' ? 'bg-white text-red-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                    📍 জেলা
                </button>
            </div>

            {{-- District Dropdown --}}
            @if($scope === 'district')
                <div class="flex-1">
                    <select name="district_id" onchange="this.form.submit()"
                        class="w-full text-sm font-semibold border border-slate-200 rounded-xl px-4 py-2.5 bg-white text-slate-700 focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="">-- জেলা বেছে নিন --</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}" {{ $districtId == $district->id ? 'selected' : '' }}>
                                {{ $district->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Hidden Inputs to preserve state --}}
            <input type="hidden" name="period" value="{{ $period }}">
            <input type="hidden" name="scope" value="{{ $scope }}">
        </div>
    </form>

    {{-- ══════════════════════════════════════════
         Top 3 Podium
    ══════════════════════════════════════════ --}}
    @if($donors->count() >= 3)
    <div class="grid grid-cols-3 gap-4 mb-6">
        @php
            $podiumOrder = [1, 0, 2]; // ২য়, ১ম, ৩য় (ক্লাসিক পোডিয়াম)
            $podiumConfig = [
                0 => ['height' => 'h-36 sm:h-44', 'crown' => '👑', 'bg' => 'from-yellow-400 to-amber-500', 'border' => 'border-yellow-300', 'rank_bg' => 'bg-yellow-500'],
                1 => ['height' => 'h-28 sm:h-36', 'crown' => '🥈', 'bg' => 'from-slate-400 to-slate-500', 'border' => 'border-slate-300', 'rank_bg' => 'bg-slate-500'],
                2 => ['height' => 'h-24 sm:h-32', 'crown' => '🥉', 'bg' => 'from-amber-600 to-amber-700', 'border' => 'border-amber-300', 'rank_bg' => 'bg-amber-700'],
            ];
        @endphp

        @foreach($podiumOrder as $realIndex => $displayIndex)
            @php $d = $donors[$displayIndex]; $cfg = $podiumConfig[$displayIndex]; @endphp
            <div class="flex flex-col items-center group" style="order: {{ $displayIndex === 0 ? 2 : ($displayIndex === 1 ? 1 : 3) }}">
                {{-- Avatar --}}
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

                {{-- Name & Stats --}}
                <div class="text-center mb-2 w-full px-1">
                    <div class="font-black text-slate-800 text-xs sm:text-sm truncate">{{ explode(' ', $d->name)[0] }}</div>
                    <div class="text-xs text-slate-500 font-semibold">{{ $d->blood_group?->value ?? $d->blood_group ?? 'N/A' }}</div>
                    {{-- Badges (small) --}}
                    @if($d->badges->count() > 0)
                        <div class="flex justify-center gap-0.5 mt-1 flex-wrap">
                            @foreach($d->badges->take(3) as $badge)
                                <span class="text-xs" title="{{ $badge->bn_name ?? $badge->name }}">{{ $badge->emoji ?? $badge->icon }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Podium bar --}}
                <div class="{{ $cfg['height'] }} w-full rounded-t-2xl bg-gradient-to-b {{ $cfg['bg'] }} flex flex-col items-center justify-start pt-3 shadow-lg">
                    <div class="text-white font-black text-lg sm:text-xl">{{ $d->total_verified_donations ?? 0 }}</div>
                    <div class="text-white/80 text-[10px] sm:text-xs font-semibold">রক্তদান</div>
                    <div class="text-white/70 text-[10px] font-bold mt-1">{{ number_format($d->points ?? 0) }} pts</div>
                </div>
            </div>
        @endforeach
    </div>
    @endif

    {{-- ══════════════════════════════════════════
         Full Rankings List (4th and beyond)
    ══════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/60">
            <h2 class="font-black text-slate-800 flex items-center gap-2">
                <span>📋</span>
                পূর্ণ তালিকা
                <span class="ml-auto text-xs font-semibold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full">
                    {{ $donors->count() }} জন
                </span>
            </h2>
        </div>

        @forelse($donors as $index => $donor)
            @php
                $rank = $index + 1;
                $isPlatinum = ($donor->total_verified_donations >= 20 || ($donor->points ?? 0) >= 1500);
                $isTop3 = $rank <= 3;
            @endphp
            <div class="flex items-center gap-4 px-5 py-4 border-b border-slate-50 hover:bg-slate-50/50 transition-colors duration-200 {{ $isPlatinum ? 'bg-purple-50/30' : '' }}">

                {{-- Rank --}}
                <div class="flex-shrink-0 w-8 text-center">
                    @if($rank === 1)
                        <span class="text-xl">🥇</span>
                    @elseif($rank === 2)
                        <span class="text-xl">🥈</span>
                    @elseif($rank === 3)
                        <span class="text-xl">🥉</span>
                    @else
                        <span class="text-sm font-black text-slate-400">#{{ $rank }}</span>
                    @endif
                </div>

                {{-- Avatar --}}
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-gradient-to-br from-red-100 to-red-200 flex items-center justify-center overflow-hidden {{ $isPlatinum ? 'ring-2 ring-purple-300 ring-offset-1' : '' }}">
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
                        @if($isPlatinum)
                            <span class="inline-flex items-center text-[10px] font-black text-purple-700 bg-purple-100 border border-purple-200 rounded-full px-1.5 py-0.5 animate-pulse">
                                ✨ Platinum
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                        {{-- Blood Group --}}
                        <span class="text-[10px] font-black text-red-700 bg-red-50 border border-red-100 rounded-full px-2 py-0.5">
                            {{ $donor->blood_group?->value ?? $donor->blood_group ?? 'N/A' }}
                        </span>
                        {{-- District --}}
                        @if($donor->district)
                            <span class="text-[10px] text-slate-500 font-semibold flex items-center gap-0.5">
                                📍 {{ $donor->district->name }}
                            </span>
                        @endif
                        {{-- Badges --}}
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
    </div>

    {{-- Points Guide moved to /gamification-guide page --}}
    <div class="mt-6 flex justify-center">
        <a href="{{ route('gamification.guide') }}"
            class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-red-600 transition-colors border border-slate-200 hover:border-red-200 bg-white rounded-xl px-4 py-2.5 shadow-sm">
            🪙 পয়েন্ট ও ব্যাজ সিস্টেম সম্পর্কে জানুন
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    {{-- ══════════════════════════════════════════
         Referral Card (Logged-in users)
    ══════════════════════════════════════════ --}}
    @auth
        @php
            $gamification = app(\App\Services\GamificationService::class);
            $myCode = $gamification->generateReferralCode(auth()->user());
            $referralLink = url('/register?ref=' . $myCode);
        @endphp
        <div class="mt-6 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl p-6 shadow-lg relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/4"></div>
            <div class="relative z-10">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-2xl">👥</span>
                            <h3 class="font-black text-white text-lg">আপনার রেফারেল কোড</h3>
                        </div>
                        <p class="text-emerald-100 text-sm">বন্ধুকে রেফার করুন। তারা সাইন-আপ করলে +১০ পয়েন্ট, প্রথম ডোনেশনে +৩০ পয়েন্ট পাবেন!</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="bg-white/20 border border-white/30 rounded-2xl p-3 text-center backdrop-blur">
                            <div class="text-white text-xs font-bold opacity-75 mb-1">আপনার কোড</div>
                            <div class="text-white font-black text-2xl tracking-widest">{{ $myCode }}</div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-3">
                    <input id="referral-link-input" type="text" value="{{ $referralLink }}"
                        class="flex-1 text-xs py-2.5 px-4 rounded-xl bg-white/20 border border-white/30 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-white/50 font-semibold"
                        readonly>
                    <button onclick="copyReferralLink()" id="copy-btn"
                        class="flex-shrink-0 bg-white text-emerald-700 font-black text-sm px-4 py-2.5 rounded-xl hover:bg-emerald-50 transition-colors shadow-sm">
                        কপি করুন
                    </button>
                </div>
            </div>
        </div>
    @endauth

</div>

<script>
function copyReferralLink() {
    const input = document.getElementById('referral-link-input');
    const btn = document.getElementById('copy-btn');
    navigator.clipboard.writeText(input.value).then(() => {
        btn.textContent = '✓ কপি হয়েছে!';
        btn.classList.add('bg-emerald-100', 'text-emerald-800');
        setTimeout(() => {
            btn.textContent = 'কপি করুন';
            btn.classList.remove('bg-emerald-100', 'text-emerald-800');
        }, 2500);
    });
}
</script>
@endsection
