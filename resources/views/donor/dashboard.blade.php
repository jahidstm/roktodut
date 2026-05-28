@extends('layouts.donor-dashboard')

@section('title', 'ডোনার ড্যাশবোর্ড — রক্তদূত')
@section('page-title', 'ওভারভিউ')

@section('content')

{{-- 🩸 Recipient Confirmation Pop-up --}}
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
                <h2 class="text-2xl font-black text-slate-900 leading-tight">ডোনেশন কনফার্ম করুন</h2>
                <p class="text-slate-500 font-medium mt-3 px-2">
                    <span class="text-red-600 font-bold">{{ $pendingClaim->donor->name ?? 'একজন ডোনার' }}</span> ক্লেইম করেছেন যে তিনি আপনার রিকোয়েস্টে রক্ত দিয়েছেন।
                </p>
                <div class="mt-4 inline-block bg-slate-50 px-4 py-2 rounded-full border border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-widest">
                    Request ID: {{ $pendingClaim->bloodRequest->unique_id ?? 'REQ-'.$pendingClaim->bloodRequest->id }}
                </div>
                @if($pendingClaim->proof_image_path)
                    <div class="mt-4">
                        <a href="{{ route('donations.proof', $pendingClaim->id) }}" target="_blank" class="text-sm font-bold text-blue-600 hover:underline">
                            🔍 ডোনারের দেওয়া প্রমাণ দেখুন
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
                        হ্যাঁ, তিনি রক্ত দিয়েছেন
                    </button>
                </form>
                <form action="{{ route('donations.recipient_verify', $pendingClaim->id) }}" method="POST" class="w-full">
                    @csrf
                    <input type="hidden" name="decision" value="dispute">
                    <button type="submit" onclick="return confirm('আপনি কি নিশ্চিত?')" class="w-full bg-white hover:bg-red-50 text-red-600 font-bold py-3.5 rounded-2xl border border-red-200 transition-all">
                        না, তিনি আসেননি (Report)
                    </button>
                </form>
            </div>
        </div>
    </div>
@endif

<div class="flex flex-col gap-6">

    {{-- ══ A) HERO BANNER ══ --}}
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

        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start gap-6">
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
                    <h2 class="text-xl sm:text-2xl font-black text-white leading-tight">{{ $user->name }}</h2>
                    <p class="text-slate-400 text-xs sm:text-sm font-medium mt-1 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                        <span class="truncate">{{ $user->upazila?->name ?? 'উপজেলা নেই' }}, {{ $user->district?->name ?? 'জেলা নেই' }}</span>
                    </p>

                    {{-- Badges --}}
                    @if($user->badges->count() > 0)
                    <div x-data="{ 
                            expanded: false, 
                            overflowCount: 0,
                            calc() {
                                if (this.expanded) return;
                                let hidden = 0;
                                const items = Array.from(this.$refs.container.children).filter(el => el.classList.contains('badge-item'));
                                if(!items.length) return;
                                const firstTop = items[0].offsetTop;
                                items.forEach(item => {
                                    if (item.offsetTop > firstTop) hidden++;
                                });
                                this.overflowCount = hidden;
                            }
                         }" 
                         x-init="$nextTick(() => calc()); window.addEventListener('resize', () => calc());"
                         class="mt-3 relative w-full max-w-[280px] sm:max-w-md"
                    >
                        {{-- The badges container --}}
                        <div x-ref="container" 
                             :class="expanded ? 'flex-wrap' : 'flex-wrap max-h-[28px] overflow-hidden'" 
                             class="flex gap-2 relative pr-14"
                        >
                            @foreach($user->badges as $badge)
                                @php $bd = \App\Services\GamificationService::getBadgeDisplayData($badge->name); @endphp
                                <div class="badge-item inline-flex items-center gap-1.5 text-[10px] font-bold bg-white/10 border border-white/10 text-slate-200 rounded-full px-2.5 py-1 transition-all" 
                                     title="{{ $bd['bn'] }}">
                                    <span>{{ $bd['emoji'] }}</span>
                                    <span class="truncate max-w-[120px]">{{ $bd['bn'] }}</span>
                                </div>
                            @endforeach
                            
                            {{-- Expand Button (Absolute positioned at the end of the first line) --}}
                            <button type="button"
                                    x-show="!expanded && overflowCount > 0" 
                                    @click="expanded = true" 
                                    class="absolute top-0 right-0 h-[26px] inline-flex items-center justify-center gap-1 text-[10px] font-bold bg-slate-800 hover:bg-slate-700 border border-slate-600 text-white rounded-full px-2.5 transition-colors shadow-sm"
                                    style="display: none;">
                                <span x-text="'+' + overflowCount + ' আরও'"></span>
                            </button>
                        </div>
                        
                        {{-- Collapse Button --}}
                        <button type="button"
                                x-show="expanded && overflowCount > 0" 
                                @click="expanded = false" 
                                class="mt-2 inline-flex items-center gap-1 text-[10px] font-bold text-slate-400 hover:text-white transition-colors"
                                style="display: none;">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"></path></svg>
                            কম দেখান
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap gap-2 sm:gap-3 w-full md:w-auto">

                <form action="{{ route('donor_profile.is_available_now') }}" method="POST" class="m-0 w-full sm:w-auto">
                    @csrf
                    <button type="submit"
                            {{ !$isEligible ? 'disabled' : '' }}
                            title="{{ !$isEligible ? 'কুলডাউন চলাকালীন পরিবর্তন করা যাবে না' : 'স্ট্যাটাস পরিবর্তন করুন' }}"
                            class="inline-flex items-center justify-center gap-2 {{ $isAvailable ? 'bg-red-600 hover:bg-red-700' : 'bg-slate-700 hover:bg-slate-600' }} text-white px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all transform hover:-translate-y-1 shadow-lg hover:shadow-xl hover:shadow-red-500/20 disabled:transform-none disabled:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed w-full">
                        <span class="w-2 h-2 rounded-full {{ $isAvailable ? 'bg-white animate-pulse' : 'bg-slate-400' }}"></span>
                        {{ $isAvailable ? 'রক্তদানে প্রস্তুত' : 'বর্তমানে ব্যস্ত' }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Stats Row --}}
        <div class="relative z-10 grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mt-6 sm:mt-8">
            <div class="bg-white/5 rounded-2xl p-4 sm:p-5 border border-white/10">
                <p class="text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-1">মোট পয়েন্ট</p>
                <p class="text-2xl sm:text-3xl font-black text-white">{{ number_format($currentPoints) }}</p>
            </div>
            <div class="bg-white/5 rounded-2xl p-4 sm:p-5 border border-white/10">
                <p class="text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-1">রক্তদান</p>
                <p class="text-2xl sm:text-3xl font-black text-white">{{ $totalDonations }}</p>
            </div>
            <div class="bg-white/5 rounded-2xl p-4 sm:p-5 border border-white/10">
                <p class="text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-1">গ্লোবাল র‍্যাঙ্ক</p>
                <p class="text-2xl sm:text-3xl font-black text-blue-400">{{ $myRank ? '#'.$myRank : '--' }}</p>
            </div>
            @if($nextMilestone)
            <div class="bg-white/5 rounded-2xl p-4 sm:p-5 border border-white/10">
                <p class="text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-1">পরবর্তী লক্ষ্য</p>
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
                <p class="text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-1">স্ট্যাটাস</p>
                <p class="text-sm font-black {{ $isEligible ? 'text-emerald-400' : 'text-amber-400' }}">
                    {{ $isEligible ? 'রক্তদানে যোগ্য' : 'কুলডাউনে আছেন' }}
                </p>
                @if(!$isEligible && $nextDate)
                    <p class="text-[10px] font-bold text-slate-400 mt-1">{{ (int) now()->startOfDay()->diffInDays($nextDate->copy()->startOfDay()) }} দিন বাকি</p>
                @endif
            </div>
            @endif
        </div>
    </div>

    {{-- ══ IMPACT STATS ══ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6 mt-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-center gap-4 scroll-reveal" data-scroll-reveal>
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900">{{ $totalRequestsMade ?? 0 }}</div>
                <div class="text-xs font-bold text-slate-500 mt-0.5">সাড়া দিয়েছেন</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-emerald-100 p-5 shadow-sm flex items-center gap-4 scroll-reveal" data-scroll-reveal>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </div>
            <div>
                <div class="text-2xl font-black text-emerald-600">{{ $totalContributions ?? 0 }}</div>
                <div class="text-xs font-bold text-emerald-500 mt-0.5">ভেরিফাইড ডোনেশন</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-red-100 p-5 shadow-sm flex items-center gap-4 scroll-reveal" data-scroll-reveal>
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.593c-5.63-5.539-11-10.297-11-14.402 0-3.791 3.068-5.191 5.281-5.191 1.312 0 4.151.501 5.719 4.457 1.59-3.968 4.464-4.447 5.726-4.447 2.54 0 5.274 1.621 5.274 5.181 0 4.069-5.136 8.625-11 14.402z"/></svg>
            </div>
            <div>
                <div class="text-2xl font-black text-red-600">{{ $totalLivesSaved ?? 0 }}</div>
                <div class="text-xs font-bold text-red-500 mt-0.5">জীবন বাঁচিয়েছেন</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-blue-100 p-5 shadow-sm flex items-center gap-4 scroll-reveal" data-scroll-reveal>
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div>
                <div class="text-2xl font-black text-blue-600">{{ $successRate ?? 0 }}{{ ($successRate !== 'তথ্য নেই') ? '%' : '' }}</div>
                <div class="text-xs font-bold text-blue-500 mt-0.5">সফলতার হার</div>
            </div>
        </div>
    </div>

    {{-- ══ B) DONATION READINESS PANEL (NEW DESIGN — KEEP) ══ --}}
    <div class="bg-white rounded-3xl border {{ $isEligible ? 'border-emerald-200' : 'border-amber-200' }} shadow-sm overflow-hidden scroll-reveal" data-scroll-reveal>

        {{-- State Banner --}}
        <div class="px-5 sm:px-6 py-5 {{ $isEligible ? 'bg-emerald-50' : 'bg-amber-50' }} flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b {{ $isEligible ? 'border-emerald-100' : 'border-amber-100' }}">
            <div class="flex items-center gap-4">
                <div class="w-11 h-11 rounded-full {{ $isEligible ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' }} flex items-center justify-center shrink-0">
                    @if($isEligible)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>
                <div>
                    <h3 class="text-sm font-extrabold {{ $isEligible ? 'text-emerald-800' : 'text-amber-800' }}">
                        {{ $isEligible ? '✓ আপনি রক্তদানের জন্য যোগ্য' : 'কুলডাউন চলছে' }}
                    </h3>
                    <p class="text-xs font-medium {{ $isEligible ? 'text-emerald-600' : 'text-amber-700' }} mt-0.5">
                        @if(!$user->last_donated_at)
                            আমাদের সিস্টেমে পূর্বের কোনো রেকর্ড নেই।
                        @elseif($isEligible)
                            সর্বশেষ রক্তদানের পর ১২০+ দিন পার হয়েছে।
                        @else
                            পরবর্তী তারিখ: <strong>{{ $nextDate->format('d M, Y') }}</strong> — আর <strong>{{ (int) now()->startOfDay()->diffInDays($nextDate->startOfDay()) }} দিন</strong> বাকি
                        @endif
                    </p>
                </div>
            </div>

            {{-- Last Donation Date Update Form --}}
            <form action="{{ route('donation.record.update') }}" method="POST" class="flex items-end gap-2 w-full sm:w-auto shrink-0">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase tracking-wider">শেষ ডোনেশন তারিখ</label>
                    <input type="date" name="last_donated_at"
                           value="{{ $user->last_donated_at?->format('Y-m-d') }}"
                           max="{{ date('Y-m-d') }}"
                           {{ !$isEligible && $user->last_donated_at ? 'readonly disabled' : '' }}
                           class="rounded-lg border-slate-300 text-sm font-semibold text-slate-700 focus:border-red-500 focus:ring-red-500 shadow-sm {{ !$isEligible && $user->last_donated_at ? 'bg-slate-100 opacity-60 cursor-not-allowed' : '' }}">
                </div>
                <button type="submit"
                        {{ !$isEligible && $user->last_donated_at ? 'disabled' : '' }}
                        class="bg-slate-800 hover:bg-slate-700 text-white px-4 py-2.5 rounded-lg font-extrabold text-sm transition shadow-sm {{ !$isEligible && $user->last_donated_at ? 'opacity-40 cursor-not-allowed' : '' }}">
                    সেভ
                </button>
            </form>
        </div>

        {{-- Blood Component Recovery Cards --}}
        <div class="px-5 sm:px-6 py-5">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-4">রক্তের উপাদান পুনরুদ্ধার অগ্রগতি</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                @foreach($donationRecoveryCards as $card)
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <p class="text-sm font-extrabold text-slate-700">{{ $card['title'] }}</p>
                        <span class="text-xs font-black {{ $card['text_class'] }}">
                            {{ $card['remaining_days'] === 0 ? '✓ প্রস্তুত' : $card['remaining_days'].' দিন' }}
                        </span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full {{ $card['bar_class'] }} rounded-full transition-all duration-700" style="width: {{ $card['progress_percent'] }}%"></div>
                    </div>
                    <p class="text-[10px] text-slate-400 font-medium mt-1.5">
                        {{ $card['is_ready'] ? 'রক্তদানের জন্য প্রস্তুত' : 'উপলব্ধ: '.$card['eligible_on_formatted'] }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ══ E) QUICK ACTIONS GRID ══ --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <a href="{{ route('requests.create') }}" class="group relative overflow-hidden p-5 rounded-2xl bg-white hover:shadow-lg transition-all shadow-sm border border-slate-200 hover:border-red-300 hover:-translate-y-0.5 flex flex-col gap-3 scroll-reveal" data-scroll-reveal>
            <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center text-red-600 border border-red-100 shrink-0 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div>
                <h3 class="text-slate-900 font-black text-sm leading-tight">জরুরি রিকোয়েস্ট</h3>
                <p class="text-slate-500 text-xs font-medium mt-0.5">নতুন রক্তের অনুরোধ</p>
            </div>
        </a>

        <a href="{{ route('requests.index') }}" class="group relative overflow-hidden p-5 rounded-2xl bg-white hover:shadow-lg transition-all shadow-sm border border-slate-200 hover:border-emerald-300 hover:-translate-y-0.5 flex flex-col gap-3 scroll-reveal" data-scroll-reveal>
            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 border border-emerald-100 shrink-0 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </div>
            <div>
                <h3 class="text-slate-900 font-black text-sm leading-tight">রক্ত দিন</h3>
                <p class="text-slate-500 text-xs font-medium mt-0.5">রিকোয়েস্ট দেখুন</p>
            </div>
        </a>

        <a href="{{ route('requests.my-requests') }}" class="group relative overflow-hidden p-5 rounded-2xl bg-white hover:shadow-lg transition-all shadow-sm border border-slate-200 hover:border-blue-300 hover:-translate-y-0.5 flex flex-col gap-3 scroll-reveal" data-scroll-reveal>
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 border border-blue-100 shrink-0 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <h3 class="text-slate-900 font-black text-sm leading-tight">আমার রিকোয়েস্ট</h3>
                <p class="text-slate-500 text-xs font-medium mt-0.5">ম্যানেজ করুন</p>
            </div>
        </a>


    </div>

    {{-- ══ G) LOCAL EMERGENCY RADAR ══ --}}
    @if($radarRequests->isNotEmpty())
    <div class="bg-white rounded-3xl border border-red-100 shadow-sm overflow-hidden scroll-reveal" data-scroll-reveal>
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                    <span class="text-base">📍</span>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">আপনার এলাকার জরুরি রিকোয়েস্ট</h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $user->district?->name }} — রক্তের প্রয়োজন আছে</p>
                </div>
            </div>
            <a href="{{ route('requests.index') }}" class="text-xs font-bold text-red-600 hover:underline">সব দেখুন →</a>
        </div>
        <div class="divide-y divide-slate-50">
            @foreach($radarRequests->take(4) as $radar)
            <div class="flex items-center justify-between px-6 py-4 hover:bg-slate-50 transition">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center font-black text-red-600 text-sm shrink-0">
                        {{ $radar->blood_group?->value ?? $radar->blood_group }}
                    </div>
                    <div>
                        <p class="font-bold text-slate-900 text-sm">{{ $radar->patient_name ?? 'অজানা রোগী' }}</p>
                        <p class="text-xs text-slate-500 font-medium">{{ $radar->hospital?->display_name ?? 'হাসপাতাল নির্ধারিত নয়' }} · {{ $radar->upazila?->name ?? '' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    @php
                        $urgency = $radar->urgency?->value ?? $radar->urgency ?? 'normal';
                        $urgencyMap = ['emergency' => ['label' => 'ইমার্জেন্সি', 'class' => 'bg-red-100 text-red-700'], 'urgent' => ['label' => 'জরুরি', 'class' => 'bg-amber-100 text-amber-700'], 'normal' => ['label' => 'স্বাভাবিক', 'class' => 'bg-slate-100 text-slate-600']];
                        $urgencyDisplay = $urgencyMap[$urgency] ?? $urgencyMap['normal'];
                    @endphp
                    <span class="text-[10px] font-black px-2 py-0.5 rounded-full {{ $urgencyDisplay['class'] }}">{{ $urgencyDisplay['label'] }}</span>
                    <a href="{{ route('requests.show', $radar->id) }}" class="text-xs font-bold bg-red-600 text-white px-3 py-1.5 rounded-lg hover:bg-red-700 transition whitespace-nowrap">সাড়া দিন</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ══ H) MY ONGOING COMMITMENTS ══ --}}
    @if(isset($ongoingCommitments) && $ongoingCommitments->count() > 0)
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden scroll-reveal" data-scroll-reveal>
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">আমার চলমান কমিটমেন্ট</h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">যে রিকোয়েস্টে রক্ত দেওয়ার প্রতিশ্রুতি দিয়েছেন</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <tbody class="divide-y divide-slate-100 text-sm">
                    @foreach($ongoingCommitments as $commitment)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-extrabold text-slate-900">{{ $commitment->bloodRequest->patient_name ?? 'তথ্য নেই' }}</div>
                            <div class="text-xs font-bold text-blue-600 mt-0.5">{{ $commitment->bloodRequest->blood_group?->value ?? $commitment->bloodRequest->blood_group }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-700">{{ $commitment->bloodRequest->hospital?->display_name ?? 'তথ্য নেই' }}</div>
                            <div class="text-xs font-bold text-slate-500 mt-0.5">{{ $commitment->bloodRequest->district?->name ?? 'অজানা জেলা' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($commitment->verification_status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-blue-100 text-blue-800">চলমান</span>
                            @elseif($commitment->verification_status === 'claimed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800">রিভিউ হচ্ছে</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('requests.show', $commitment->blood_request_id) }}" class="inline-flex items-center px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-extrabold text-slate-700 hover:bg-slate-100 transition">ডিটেইলস</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif



    {{-- ══ K) REFERRAL BANNER (NEW DESIGN — KEEP) ══ --}}
    @if(isset($myCode))
    <div class="relative overflow-hidden rounded-3xl bg-slate-900 border border-slate-800 p-6 sm:p-8 shadow-xl scroll-reveal" data-scroll-reveal>
        <div class="absolute -top-12 -right-12 w-56 h-56 bg-emerald-500/8 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-12 -left-12 w-40 h-40 bg-blue-500/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-2xl">👥</span>
                    <h2 class="text-white font-black text-lg sm:text-xl">বন্ধুকে আমন্ত্রণ জানান</h2>
                </div>
                <p class="text-slate-400 text-sm font-medium">বন্ধু সাইন-আপ করলে <span class="text-emerald-400 font-black">+১০ পয়েন্ট</span>, প্রথম ডোনেশনে <span class="text-emerald-400 font-black">+৩০ পয়েন্ট</span> পাবেন!</p>
            </div>
            <div class="flex-shrink-0 w-full sm:w-auto">
                <div class="bg-white/5 border border-white/10 rounded-2xl p-4 text-center mb-3">
                    <div class="text-slate-500 text-[10px] font-extrabold uppercase tracking-widest mb-1">আপনার রেফারেল কোড</div>
                    <div class="text-white font-black text-2xl tracking-[0.2em]">{{ $myCode }}</div>
                </div>
                <div class="flex gap-2">
                    <input id="referral-link-dashboard" type="text" value="{{ $referralLink }}" class="flex-1 text-xs py-2.5 px-3 rounded-xl bg-white/5 border border-white/10 text-slate-300 font-semibold focus:outline-none min-w-0" readonly>
                    <button id="dash-copy-btn" onclick="copyDashboardReferral()" class="shrink-0 bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs px-4 py-2.5 rounded-xl transition">কপি করুন</button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script>
function copyDashboardReferral() {
    const input = document.getElementById('referral-link-dashboard');
    const btn   = document.getElementById('dash-copy-btn');
    navigator.clipboard.writeText(input.value).then(() => {
        btn.textContent = '✓ কপি হয়েছে!';
        btn.classList.remove('bg-emerald-600', 'hover:bg-emerald-500');
        btn.classList.add('bg-emerald-800');
        setTimeout(() => {
            btn.textContent = 'কপি করুন';
            btn.classList.add('bg-emerald-600', 'hover:bg-emerald-500');
            btn.classList.remove('bg-emerald-800');
        }, 2500);
    });
}
</script>
@endpush

@endsection
