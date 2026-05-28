@extends('layouts.donor-dashboard')

@section('title', 'αªíαºïαª¿αª╛αª░ αªíαºìαª»αª╛αª╢αª¼αºïαª░αºìαªí ΓÇö αª░αªòαºìαªñαªªαºéαªñ')
@section('page-title', 'αªôαª¡αª╛αª░αª¡αª┐αªë')

@section('content')

{{-- ≡ƒ⌐╕ Recipient Confirmation Pop-up --}}
@if(isset($pendingClaim))
    <div class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-900/80 backdrop-blur-md">
        <div class="bg-white rounded-[2rem] p-8 max-w-lg w-full mx-4 shadow-2xl border border-red-100">
            <div class="relative">
                <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 border-4 border-white shadow-md overflow-hidden">
                    @if($pendingClaim->donor && $pendingClaim->donor->profile_image)
                        <img src="{{ asset('storage/' . $pendingClaim->donor->profile_image) }}" class="w-full h-full object-cover">
                    @elseif($pendingClaim->donor)
                        <span class="text-red-600 font-black text-2xl">{{ mb_substr($pendingClaim->donor->name, 0, 1) }}</span>
                    @else
                        <span class="text-red-600 font-black text-2xl">?</span>
                    @endif
                </div>
                <div class="absolute -bottom-2 right-1/2 translate-x-12 bg-emerald-500 text-white p-1.5 rounded-full border-4 border-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                </div>
            </div>
            <div class="text-center">
                <h2 class="text-2xl font-black text-slate-900 leading-tight">αªíαºïαª¿αºçαª╢αª¿ αªòαª¿αª½αª╛αª░αºìαª« αªòαª░αºüαª¿</h2>
                <p class="text-slate-500 font-medium mt-3 px-2">
                    <span class="text-red-600 font-bold">{{ $pendingClaim->donor->name ?? 'αªÅαªòαª£αª¿ αªíαºïαª¿αª╛αª░' }}</span> αªòαºìαª▓αºçαªçαª« αªòαª░αºçαª¢αºçαª¿ αª»αºç αªñαª┐αª¿αª┐ αªåαª¬αª¿αª╛αª░ αª░αª┐αªòαºïαª»αª╝αºçαª╕αºìαªƒαºç αª░αªòαºìαªñ αªªαª┐αª»αª╝αºçαª¢αºçαª¿αÑñ
                </p>
                <div class="mt-4 inline-block bg-slate-50 px-4 py-2 rounded-full border border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-widest">
                    Request ID: {{ $pendingClaim->bloodRequest->unique_id ?? 'REQ-'.$pendingClaim->bloodRequest->id }}
                </div>
                @if($pendingClaim->proof_image_path)
                    <div class="mt-4">
                        <a href="{{ route('donations.proof', $pendingClaim->id) }}" target="_blank" class="text-sm font-bold text-blue-600 hover:underline">
                            ≡ƒöì αªíαºïαª¿αª╛αª░αºçαª░ αªªαºçαªôαª»αª╝αª╛ αª¬αºìαª░αª«αª╛αªú αªªαºçαªûαºüαª¿
                        </a>
                    </div>
                @endif
            </div>
            <div class="flex flex-col gap-3 mt-8 w-full">
                <form action="{{ route('donations.recipient_verify', $pendingClaim->id) }}" method="POST" class="w-full">
                    @csrf
                    <input type="hidden" name="decision" value="approve">
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        αª╣αºìαª»αª╛αªü, αªñαª┐αª¿αª┐ αª░αªòαºìαªñ αªªαª┐αª»αª╝αºçαª¢αºçαª¿
                    </button>
                </form>
                <form action="{{ route('donations.recipient_verify', $pendingClaim->id) }}" method="POST" class="w-full">
                    @csrf
                    <input type="hidden" name="decision" value="dispute">
                    <button type="submit" onclick="return confirm('αªåαª¬αª¿αª┐ αªòαª┐ αª¿αª┐αª╢αºìαªÜαª┐αªñ?')" class="w-full bg-white hover:bg-red-50 text-red-600 font-bold py-3.5 rounded-2xl border border-red-200 transition-all">
                        αª¿αª╛, αªñαª┐αª¿αª┐ αªåαª╕αºçαª¿αª¿αª┐ (Report)
                    </button>
                </form>
            </div>
        </div>
    </div>
@endif

<div class="flex flex-col gap-6">
    
    {{-- ΓòÉΓòÉ A) HERO BANNER ΓòÉΓòÉ --}}
    @php
        $currentPoints   = $gamificationStats['currentPoints'] ?? 0;
        $totalDonations  = $gamificationStats['totalDonations'] ?? 0;
        $myRank          = $gamificationStats['myRank'] ?? null;
        $nextMilestone   = $gamificationStats['nextMilestone'] ?? null;
        $progressPercent = $gamificationStats['progressPercent'] ?? 0;
        $isEligible      = $user->is_eligible_to_donate;
        $nextDate        = $user->next_eligible_date;
        $isAvailable     = $isEligible && $user->is_available;
    @endphp

    <div class="rounded-[2rem] overflow-hidden bg-slate-900 shadow-xl p-6 sm:p-8 relative scroll-reveal" data-scroll-reveal>
        <div class="absolute top-0 right-0 w-64 h-64 bg-red-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-blue-500/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            {{-- Profile --}}
            <div class="flex items-center gap-4 sm:gap-5">
                <div class="relative">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center border-4 border-slate-800 shadow-xl">
                        <span class="text-2xl sm:text-3xl font-black text-white">{{ $user->blood_group?->value ?? $user->blood_group ?? '?' }}</span>
                    </div>
                    @if($user->nid_status === 'verified' || $user->nid_status === 'approved' || $user->verified_badge)
                    <div class="absolute bottom-0 right-0 w-5 h-5 sm:w-6 sm:h-6 bg-blue-500 rounded-full border-2 border-slate-900 flex items-center justify-center text-white" title="Verified">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    @endif
                </div>
                <div>
                    <p class="text-slate-400 text-xs sm:text-sm font-semibold mb-0.5">αª╕αºìαª¼αª╛αªùαªñαª« αª½αª┐αª░αºç αªåαª╕αª╛αª░ αª£αª¿αºìαª»,</p>
                    <h2 class="text-xl sm:text-2xl font-black text-white leading-tight">{{ $user->name }}</h2>
                    <p class="text-slate-400 text-xs sm:text-sm font-medium mt-1 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                        <span class="truncate">{{ $user->upazila?->name ?? 'αªëαª¬αª£αºçαª▓αª╛ αª¿αºçαªç' }}, {{ $user->district?->name ?? 'αª£αºçαª▓αª╛ αª¿αºçαªç' }}</span>
                    </p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap gap-2 sm:gap-3 w-full md:w-auto">
                <a href="{{ route('gamification.guide') }}" class="flex-1 md:flex-none text-center inline-flex items-center justify-center bg-white/10 hover:bg-white/20 text-white px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition">
                    ≡ƒ¬Ö αª¬αª»αª╝αºçαª¿αºìαªƒ αªùαª╛αªçαªí
                </a>
                <a href="{{ route('leaderboard') }}" class="flex-1 md:flex-none text-center inline-flex items-center justify-center bg-white/10 hover:bg-white/20 text-white px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition">
                    ≡ƒÅå αª▓αª┐αªíαª╛αª░αª¼αºïαª░αºìαªí
                </a>
                <form action="{{ route('donor_profile.is_available_now') }}" method="POST" class="m-0 w-full sm:w-auto">
                    @csrf
                    <button type="submit"
                            {{ !$isEligible ? 'disabled' : '' }}
                            title="{{ !$isEligible ? 'αªòαºüαª▓αªíαª╛αªëαª¿ αªÜαª▓αª╛αªòαª╛αª▓αºÇαª¿ αª¬αª░αª┐αª¼αª░αºìαªñαª¿ αªòαª░αª╛ αª»αª╛αª¼αºç αª¿αª╛' : 'αª╕αºìαªƒαºìαª»αª╛αªƒαª╛αª╕ αª¬αª░αª┐αª¼αª░αºìαªñαª¿ αªòαª░αºüαª¿' }}"
                            class="inline-flex items-center justify-center gap-2 {{ $isAvailable ? 'bg-red-600 hover:bg-red-700' : 'bg-slate-700 hover:bg-slate-600' }} text-white px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed w-full">
                        <span class="w-2 h-2 rounded-full {{ $isAvailable ? 'bg-white animate-pulse' : 'bg-slate-400' }}"></span>
                        {{ $isAvailable ? 'αª░αªòαºìαªñαªªαª╛αª¿αºç αª¬αºìαª░αª╕αºìαªñαºüαªñ' : 'αª¼αª░αºìαªñαª«αª╛αª¿αºç αª¼αºìαª»αª╕αºìαªñ' }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Stats Row --}}
        <div class="relative z-10 grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mt-6 sm:mt-8">
            <div class="bg-white/5 rounded-2xl p-4 sm:p-5 border border-white/10">
                <p class="text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-1">αª«αºïαªƒ αª¬αª»αª╝αºçαª¿αºìαªƒ</p>
                <p class="text-2xl sm:text-3xl font-black text-white">{{ number_format($currentPoints) }}</p>
            </div>
            <div class="bg-white/5 rounded-2xl p-4 sm:p-5 border border-white/10">
                <p class="text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-1">αª░αªòαºìαªñαªªαª╛αª¿</p>
                <p class="text-2xl sm:text-3xl font-black text-white">{{ $totalDonations }}</p>
            </div>
            <div class="bg-white/5 rounded-2xl p-4 sm:p-5 border border-white/10">
                <p class="text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-1">αªùαºìαª▓αºïαª¼αª╛αª▓ αª░ΓÇìαºìαª»αª╛αªÖαºìαªò</p>
                <p class="text-2xl sm:text-3xl font-black text-blue-400">{{ $myRank ? '#'.$myRank : '--' }}</p>
            </div>
            @if($nextMilestone)
            <div class="bg-white/5 rounded-2xl p-4 sm:p-5 border border-white/10">
                <p class="text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-1">αª¬αª░αª¼αª░αºìαªñαºÇ αª▓αªòαºìαª╖αºìαª»</p>
                <div class="flex items-end justify-between mt-1">
                    <p class="text-xs sm:text-sm font-bold text-white truncate">{{ $nextMilestone['bn'] }}</p>
                    <span class="text-[10px] sm:text-xs font-bold text-slate-400">{{ $progressPercent }}%</span>
                </div>
                <div class="w-full h-1.5 rounded-full bg-slate-800 overflow-hidden mt-2">
                    <div class="h-full bg-gradient-to-r from-red-500 to-rose-500 rounded-full" style="width: {{ $progressPercent }}%"></div>
                </div>
            </div>
            @else
            <div class="bg-white/5 rounded-2xl p-4 sm:p-5 border border-white/10">
                <p class="text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-1">αª╕αºìαªƒαºìαª»αª╛αªƒαª╛αª╕</p>
                <p class="text-sm font-black {{ $isEligible ? 'text-emerald-400' : 'text-amber-400' }}">
                    {{ $isEligible ? 'αª░αªòαºìαªñαªªαª╛αª¿αºç αª»αºïαªùαºìαª»' : 'αªòαºüαª▓αªíαª╛αªëαª¿αºç αªåαª¢αºçαª¿' }}
                </p>
                @if(!$isEligible && $nextDate)
                    <p class="text-[10px] font-bold text-slate-400 mt-1">{{ (int) now()->startOfDay()->diffInDays($nextDate->copy()->startOfDay()) }} αªªαª┐αª¿ αª¼αª╛αªòαª┐</p>
                @endif
            </div>
            @endif
        </div>

        {{-- Badges --}}
        @if($user->badges->count() > 0)
        <div class="relative z-10 flex flex-wrap gap-2 mt-4">
            @foreach($user->badges->take(4) as $badge)
                @php $bd = \App\Services\GamificationService::getBadgeDisplayData($badge->name); @endphp
                <div class="inline-flex items-center gap-1.5 text-[10px] sm:text-xs font-bold bg-white/5 border border-white/10 text-slate-300 rounded-full px-2.5 py-1" title="{{ $bd['bn'] }}">
                    <span>{{ $bd['emoji'] }}</span>
                    <span class="truncate">{{ $bd['bn'] }}</span>
                </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ΓòÉΓòÉ B) ELIGIBILITY + DATE UPDATE ΓòÉΓòÉ --}}
    <div class="bg-white p-5 sm:p-6 rounded-3xl border {{ $isEligible ? 'border-emerald-200' : 'border-amber-200' }} shadow-sm flex flex-col md:flex-row items-center justify-between gap-5 scroll-reveal" data-scroll-reveal>
        <div class="flex items-center gap-4">
            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full {{ $isEligible ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' }}">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-extrabold {{ $isEligible ? 'text-emerald-700' : 'text-amber-700' }}">
                    {{ $isEligible ? 'αªåαª¬αª¿αª┐ αª░αªòαºìαªñαªªαª╛αª¿αºçαª░ αª£αª¿αºìαª» αª»αºïαªùαºìαª» Γ£ô' : 'αªåαª¬αª╛αªñαªñ αª░αªòαºìαªñαªªαª╛αª¿αºçαª░ αª£αª¿αºìαª» αª»αºïαªùαºìαª» αª¿αª¿' }}
                </h3>
                <p class="text-xs font-semibold text-slate-500 mt-0.5">
                    @if(!$user->last_donated_at)
                        αªåαª«αª╛αªªαºçαª░ αª╕αª┐αª╕αºìαªƒαºçαª«αºç αªåαª¬αª¿αª╛αª░ αª¬αºéαª░αºìαª¼αºçαª░ αª░αºçαªòαª░αºìαªí αª¿αºçαªçαÑñ
                    @elseif($isEligible)
                        αª╕αª░αºìαª¼αª╢αºçαª╖ αª░αªòαºìαªñαªªαª╛αª¿αºçαª░ αª¬αª░ αººαº¿αºª αªªαª┐αª¿ αª¬αª╛αª░ αª╣αª»αª╝αºçαª¢αºçαÑñ
                    @else
                        αª¬αª░αª¼αª░αºìαªñαºÇ αªñαª╛αª░αª┐αªû: <span class="text-slate-800 font-extrabold">{{ $nextDate->format('d M, Y') }}</span>
                        (αªåαª░ <span class="text-red-600 font-extrabold">{{ (int) now()->startOfDay()->diffInDays($nextDate->startOfDay()) }} αªªαª┐αª¿</span> αª¼αª╛αªòαª┐)
                    @endif
                </p>
            </div>
        </div>
        <form action="{{ route('donation.record.update') }}" method="POST" class="flex items-end gap-3 w-full md:w-auto">
            @csrf
            <div class="flex-1 md:w-44">
                <label class="block text-xs font-bold text-slate-500 mb-1">αª╢αºçαª╖ αª░αªòαºìαªñαªªαª╛αª¿αºçαª░ αªñαª╛αª░αª┐αªû αªåαª¬αªíαºçαªƒ</label>
                <input type="date" name="last_donated_at"
                       value="{{ $user->last_donated_at?->format('Y-m-d') }}"
                       max="{{ date('Y-m-d') }}"
                       {{ !$isEligible && $user->last_donated_at ? 'readonly disabled' : '' }}
                       class="w-full rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-sm text-slate-700 {{ !$isEligible && $user->last_donated_at ? 'bg-slate-100 cursor-not-allowed opacity-70' : '' }}">
            </div>
            <button type="submit"
                    {{ !$isEligible && $user->last_donated_at ? 'disabled' : '' }}
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg font-extrabold text-sm shadow-sm transition {{ !$isEligible && $user->last_donated_at ? 'opacity-50 cursor-not-allowed' : '' }}">
                αª╕αºçαª¡
            </button>
        </form>
    </div>

    {{-- ΓòÉΓòÉ C) DYNAMIC RECOVERY TIMER ΓòÉΓòÉ --}}
    <x-dashboard.recovery-timer :items="$donationRecoveryCards" />

    {{-- ΓòÉΓòÉ D) HEALTH LEDGER BANNER ΓòÉΓòÉ --}}
    <a href="{{ route('health-ledger.index') }}"
       class="group relative overflow-hidden p-5 sm:p-7 rounded-[2rem] bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 transition-all shadow-lg hover:shadow-2xl hover:-translate-y-0.5 flex flex-col sm:flex-row items-center justify-between gap-5 scroll-reveal border border-slate-700/50"
       data-scroll-reveal>
        <div class="absolute -right-12 -bottom-12 w-56 h-56 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none group-hover:bg-emerald-500/20 transition-all duration-700"></div>
        <div class="absolute -left-12 -top-12 w-44 h-44 bg-blue-500/10 rounded-full blur-3xl pointer-events-none group-hover:bg-blue-500/20 transition-all duration-700"></div>
        <div class="flex items-center gap-5 relative z-10 w-full sm:w-auto">
            <div class="w-14 h-14 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-400 border border-emerald-500/20 shrink-0">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div>
                <h3 class="text-white font-black text-lg leading-tight flex items-center gap-2">
                    αª¬αºìαª░αºçαªíαª┐αªòαºìαªƒαª┐αª¡ αª╣αºçαª▓αªÑ αª▓αºçαª£αª╛αª░
                    <span class="inline-flex items-center bg-emerald-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider">New</span>
                </h3>
                <p class="text-slate-400 text-sm font-medium mt-1">αª╣αª┐αª«αºïαªùαºìαª▓αºïαª¼αª┐αª¿, αª¼αºìαª▓αª╛αªí αª¬αºìαª░αºçαª╢αª╛αª░ αªƒαºìαª░αºìαª»αª╛αªò αªòαª░αºüαª¿ ΓÇö AI αªùαª╛αªçαªíαª▓αª╛αªçαª¿ αª¬αª╛αª¼αºçαª¿αÑñ</p>
            </div>
        </div>
        <span class="shrink-0 relative z-10 flex sm:inline-flex items-center justify-center bg-white text-slate-900 px-5 py-3 rounded-xl text-sm font-black transition-transform group-hover:scale-105 shadow-md w-full sm:w-auto">
            αª╣αºçαª▓αªÑ αª▓αºçαª£αª╛αª░ αªªαºçαªûαºüαª¿ ΓåÆ
        </span>
    </a>

    {{-- ΓòÉΓòÉ E) QUICK ACTIONS GRID ΓòÉΓòÉ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('requests.create') }}" class="group relative overflow-hidden p-5 rounded-2xl bg-gradient-to-br from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5 flex flex-col gap-3 scroll-reveal" data-scroll-reveal>
            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-white border border-white/30 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div>
                <h3 class="text-white font-black text-sm leading-tight">αª£αª░αºüαª░αª┐ αª░αª┐αªòαºïαª»αª╝αºçαª╕αºìαªƒ</h3>
                <p class="text-red-100 text-xs font-medium mt-0.5">αª¿αªñαºüαª¿ αª░αªòαºìαªñαºçαª░ αªàαª¿αºüαª░αºïαªº</p>
            </div>
        </a>

        <a href="{{ route('requests.index') }}" class="group relative overflow-hidden p-5 rounded-2xl bg-white hover:shadow-lg transition-all shadow-sm border border-slate-200 hover:border-emerald-300 hover:-translate-y-0.5 flex flex-col gap-3 scroll-reveal" data-scroll-reveal>
            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 border border-emerald-100 shrink-0 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </div>
            <div>
                <h3 class="text-slate-900 font-black text-sm leading-tight">αª░αªòαºìαªñ αªªαª┐αª¿</h3>
                <p class="text-slate-500 text-xs font-medium mt-0.5">αª░αª┐αªòαºïαª»αª╝αºçαª╕αºìαªƒ αªªαºçαªûαºüαª¿</p>
            </div>
        </a>

        <a href="{{ route('requests.my-requests') }}" class="group relative overflow-hidden p-5 rounded-2xl bg-white hover:shadow-lg transition-all shadow-sm border border-slate-200 hover:border-blue-300 hover:-translate-y-0.5 flex flex-col gap-3 scroll-reveal" data-scroll-reveal>
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 border border-blue-100 shrink-0 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <h3 class="text-slate-900 font-black text-sm leading-tight">αªåαª«αª╛αª░ αª░αª┐αªòαºïαª»αª╝αºçαª╕αºìαªƒ</h3>
                <p class="text-slate-500 text-xs font-medium mt-0.5">αª«αºìαª»αª╛αª¿αºçαª£ αªòαª░αºüαª¿</p>
            </div>
        </a>

        <a href="{{ route('donor.offline-claim') }}"
           class="group relative overflow-hidden p-5 rounded-2xl bg-white hover:shadow-lg transition-all shadow-sm border border-slate-200 hover:border-purple-300 hover:-translate-y-0.5 flex flex-col gap-3 text-left scroll-reveal" data-scroll-reveal>
            <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 border border-purple-100 shrink-0 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h3 class="text-slate-900 font-black text-sm leading-tight">αªàαª½αª▓αª╛αªçαª¿ αªòαºìαª▓αºçαªçαª«</h3>
                <p class="text-slate-500 text-xs font-medium mt-0.5">αª¬αª»αª╝αºçαª¿αºìαªƒ αª»αºïαªù αªòαª░αºüαª¿</p>
            </div>
        </a>

    {{-- ΓòÉΓòÉ G) LOCAL EMERGENCY RADAR ΓòÉΓòÉ --}}
    @if($radarRequests->isNotEmpty())
    <div class="bg-white rounded-3xl border border-red-100 shadow-sm overflow-hidden scroll-reveal" data-scroll-reveal>
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                    <span class="text-base">≡ƒôì</span>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">αªåαª¬αª¿αª╛αª░ αªÅαª▓αª╛αªòαª╛αª░ αª£αª░αºüαª░αª┐ αª░αª┐αªòαºïαª»αª╝αºçαª╕αºìαªƒ</h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $user->district?->name }} ΓÇö αª░αªòαºìαªñαºçαª░ αª¬αºìαª░αª»αª╝αºïαª£αª¿ αªåαª¢αºç</p>
                </div>
            </div>
            <a href="{{ route('requests.index') }}" class="text-xs font-bold text-red-600 hover:underline">αª╕αª¼ αªªαºçαªûαºüαª¿ ΓåÆ</a>
        </div>
        <div class="divide-y divide-slate-50">
            @foreach($radarRequests->take(4) as $radar)
            <div class="flex items-center justify-between px-6 py-4 hover:bg-slate-50 transition">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center font-black text-red-600 text-sm shrink-0">
                        {{ $radar->blood_group?->value ?? $radar->blood_group }}
                    </div>
                    <div>
                        <p class="font-bold text-slate-900 text-sm">{{ $radar->patient_name ?? 'αªàαª£αª╛αª¿αª╛ αª░αºïαªùαºÇ' }}</p>
                        <p class="text-xs text-slate-500 font-medium">{{ $radar->hospital?->display_name ?? 'αª╣αª╛αª╕αª¬αª╛αªñαª╛αª▓ αª¿αª┐αª░αºìαªºαª╛αª░αª┐αªñ αª¿αª»αª╝' }} ┬╖ {{ $radar->upazila?->name ?? '' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    @php
                        $urgency = $radar->urgency?->value ?? $radar->urgency ?? 'normal';
                        $urgencyMap = ['emergency' => ['label' => 'αªçαª«αª╛αª░αºìαª£αºçαª¿αºìαª╕αª┐', 'class' => 'bg-red-100 text-red-700'], 'urgent' => ['label' => 'αª£αª░αºüαª░αª┐', 'class' => 'bg-amber-100 text-amber-700'], 'normal' => ['label' => 'αª╕αºìαª¼αª╛αª¡αª╛αª¼αª┐αªò', 'class' => 'bg-slate-100 text-slate-600']];
                        $urgencyDisplay = $urgencyMap[$urgency] ?? $urgencyMap['normal'];
                    @endphp
                    <span class="text-[10px] font-black px-2 py-0.5 rounded-full {{ $urgencyDisplay['class'] }}">{{ $urgencyDisplay['label'] }}</span>
                    <a href="{{ route('requests.show', $radar->id) }}" class="text-xs font-bold bg-red-600 text-white px-3 py-1.5 rounded-lg hover:bg-red-700 transition whitespace-nowrap">αª╕αª╛αªíαª╝αª╛ αªªαª┐αª¿</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ΓòÉΓòÉ H) MY ONGOING COMMITMENTS ΓòÉΓòÉ --}}
    @if(isset($ongoingCommitments) && $ongoingCommitments->count() > 0)
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden scroll-reveal" data-scroll-reveal>
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">αªåαª«αª╛αª░ αªÜαª▓αª«αª╛αª¿ αªòαª«αª┐αªƒαª«αºçαª¿αºìαªƒ</h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">αª»αºç αª░αª┐αªòαºïαª»αª╝αºçαª╕αºìαªƒαºç αª░αªòαºìαªñ αªªαºçαªôαª»αª╝αª╛αª░ αª¬αºìαª░αªñαª┐αª╢αºìαª░αºüαªñαª┐ αªªαª┐αª»αª╝αºçαª¢αºçαª¿</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <tbody class="divide-y divide-slate-100 text-sm">
                    @foreach($ongoingCommitments as $commitment)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-extrabold text-slate-900">{{ $commitment->bloodRequest->patient_name ?? 'αªñαªÑαºìαª» αª¿αºçαªç' }}</div>
                            <div class="text-xs font-bold text-blue-600 mt-0.5">{{ $commitment->bloodRequest->blood_group?->value ?? $commitment->bloodRequest->blood_group }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-700">{{ $commitment->bloodRequest->hospital?->display_name ?? 'αªñαªÑαºìαª» αª¿αºçαªç' }}</div>
                            <div class="text-xs font-bold text-slate-500 mt-0.5">{{ $commitment->bloodRequest->district?->name ?? 'αªàαª£αª╛αª¿αª╛ αª£αºçαª▓αª╛' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($commitment->verification_status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-blue-100 text-blue-800">αªÜαª▓αª«αª╛αª¿</span>
                            @elseif($commitment->verification_status === 'claimed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800">αª░αª┐αª¡αª┐αªë αª╣αªÜαºìαª¢αºç</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('requests.show', $commitment->blood_request_id) }}" class="inline-flex items-center px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-extrabold text-slate-700 hover:bg-slate-100 transition">αªíαª┐αªƒαºçαªçαª▓αª╕</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ΓòÉΓòÉ I) IMPACT STATS ΓòÉΓòÉ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900">{{ $totalRequestsMade ?? 0 }}</div>
                <div class="text-xs font-bold text-slate-500 mt-0.5">αª╕αª╛αªíαª╝αª╛ αªªαª┐αª»αª╝αºçαª¢αºçαª¿</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-emerald-100 p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </div>
            <div>
                <div class="text-2xl font-black text-emerald-600">{{ $totalContributions ?? 0 }}</div>
                <div class="text-xs font-bold text-emerald-500 mt-0.5">αª¡αºçαª░αª┐αª½αª╛αªçαªí αªíαºïαª¿αºçαª╢αª¿</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-red-100 p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </div>
            <div>
                <div class="text-2xl font-black text-red-600">{{ ($totalContributions ?? 0) * 3 }}</div>
                <div class="text-xs font-bold text-red-500 mt-0.5">αª£αºÇαª¼αª¿ αª¼αª╛αªüαªÜαª┐αª»αª╝αºçαª¢αºçαª¿</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-blue-100 p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div>
                <div class="text-2xl font-black text-blue-600">{{ $successRate ?? 0 }}{{ ($successRate !== 'αªñαªÑαºìαª» αª¿αºçαªç') ? '%' : '' }}</div>
                <div class="text-xs font-bold text-blue-500 mt-0.5">αª╕αª½αª▓αªñαª╛αª░ αª╣αª╛αª░</div>
            </div>
        </div>
    </div>

    {{-- ΓòÉΓòÉ J) DONATION HISTORY ΓòÉΓòÉ --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden scroll-reveal" data-scroll-reveal>
        <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-slate-900">αª¡αºçαª░αª┐αª½αª╛αªçαªí αª░αªòαºìαªñαªªαª╛αª¿ αª╣αª┐αª╕αºìαªƒαºìαª░αª┐</h3>
                <p class="text-xs text-slate-500 font-medium mt-0.5">αªåαª¬αª¿αª╛αª░ αªàαªñαºÇαªñαºçαª░ αª╕αª½αª▓ αª░αªòαºìαªñαªªαª╛αª¿αºçαª░ αª▓αªù</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase tracking-wider font-bold text-slate-500">
                        <th class="px-6 py-4">αªñαª╛αª░αª┐αªû</th>
                        <th class="px-6 py-4">αª╣αª╛αª╕αª¬αª╛αªñαª╛αª▓ αªô αª▓αºïαªòαºçαª╢αª¿</th>
                        <th class="px-6 py-4">αª░αºçαª½αª╛αª░αºçαª¿αºìαª╕</th>
                        <th class="px-6 py-4 text-right">αª╕αºìαªƒαºìαª»αª╛αªƒαª╛αª╕</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($donationHistory as $history)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-extrabold text-slate-900">{{ $history->fulfilled_at ? $history->fulfilled_at->format('d M, Y') : 'αªñαªÑαºìαª» αª¿αºçαªç' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-700">{{ $history->bloodRequest->hospital?->display_name ?? 'αªñαªÑαºìαª» αª¿αºçαªç' }}</div>
                            <div class="text-xs text-slate-500 font-medium">{{ $history->bloodRequest->district?->name ?? '' }}, {{ $history->bloodRequest->upazila?->name ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-slate-400 font-mono bg-slate-100 px-2 py-1 rounded-md">
                                REQ-{{ str_pad($history->blood_request_id, 4, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 uppercase tracking-widest">αª╕αª«αºìαª¬αª¿αºìαª¿</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="text-4xl mb-3">≡ƒôï</div>
                            <p class="font-bold text-slate-600">αªòαºïαª¿αºï αª╣αª┐αª╕αºìαªƒαºìαª░αª┐ αª¬αª╛αªôαª»αª╝αª╛ αª»αª╛αª»αª╝αª¿αª┐</p>
                            <p class="text-xs text-slate-500 mt-1">αª¬αºìαª░αªÑαª« αª░αªòαºìαªñαªªαª╛αª¿αºçαª░ αª¬αª░ αªÅαªûαª╛αª¿αºç αªªαºçαªûαª╛ αª»αª╛αª¼αºçαÑñ</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ΓòÉΓòÉ K) REFERRAL BANNER ΓòÉΓòÉ --}}
    @if(isset($myCode))
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 p-6 sm:p-8 shadow-xl scroll-reveal" data-scroll-reveal>
        <div class="absolute -top-8 -right-8 w-40 h-40 bg-white/10 rounded-full pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-3xl">≡ƒæÑ</span>
                    <h2 class="text-white font-black text-lg sm:text-xl">αª¼αª¿αºìαªºαºüαªòαºç αªåαª«αª¿αºìαªñαºìαª░αªú αª£αª╛αª¿αª╛αª¿ &amp; αªåαª»αª╝ αªòαª░αºüαª¿!</h2>
                </div>
                <p class="text-emerald-100 text-sm font-medium">αª¼αª¿αºìαªºαºü αª╕αª╛αªçαª¿-αªåαª¬ αªòαª░αª▓αºç <span class="text-white font-black">+αººαºª αª¬αª»αª╝αºçαª¿αºìαªƒ</span>, αª¬αºìαª░αªÑαª« αªíαºïαª¿αºçαª╢αª¿αºç <span class="text-white font-black">+αº⌐αºª αª¬αª»αª╝αºçαª¿αºìαªƒ</span>!</p>
            </div>
            <div class="flex-shrink-0 w-full sm:w-auto">
                <div class="bg-white/15 border border-white/25 rounded-2xl p-4 text-center mb-3">
                    <div class="text-white/75 text-[10px] font-extrabold uppercase tracking-widest mb-1">αªåαª¬αª¿αª╛αª░ αª░αºçαª½αª╛αª░αºçαª▓ αªòαºïαªí</div>
                    <div class="text-white font-black text-2xl tracking-[0.2em]">{{ $myCode }}</div>
                </div>
                <div class="flex gap-2">
                    <input id="referral-link-dashboard" type="text" value="{{ $referralLink }}" class="flex-1 text-xs py-2.5 px-3 rounded-xl bg-white/15 border border-white/25 text-white font-semibold focus:outline-none min-w-0" readonly>
                    <button id="dash-copy-btn" onclick="copyDashboardReferral()" class="shrink-0 bg-white text-emerald-700 font-black text-xs px-4 py-2.5 rounded-xl hover:bg-emerald-50 transition">αªòαª¬αª┐ αªòαª░αºüαª¿</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    </div>
</div>

@push('scripts')
<script>
function copyDashboardReferral() {
    const input = document.getElementById('referral-link-dashboard');
    const btn   = document.getElementById('dash-copy-btn');
    navigator.clipboard.writeText(input.value).then(() => {
        btn.textContent = 'Γ£ô αªòαª¬αª┐ αª╣αª»αª╝αºçαª¢αºç!';
        btn.classList.add('bg-emerald-100', 'text-emerald-800');
        setTimeout(() => {
            btn.textContent = 'αªòαª¬αª┐ αªòαª░αºüαª¿';
            btn.classList.remove('bg-emerald-100', 'text-emerald-800');
        }, 2500);
    });
}
</script>
@endpush

@endsection
