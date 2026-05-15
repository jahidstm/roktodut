@extends('layouts.app')

@section('title', 'ইউজার ড্যাশবোর্ড — রক্তদূত')

@section('content')

{{-- 🚀 Welcome Back Prompt — shows once right after login (session flash) --}}
@if(session('welcome_back_prompt'))
    <div x-data="{ open: true }"
         x-show="open"
         x-cloak
         x-on:keydown.escape.window="open = false"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div x-show="open"
             x-transition.opacity.duration.200ms
             class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
             @click="open = false"></div>

        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.stop
             class="relative w-full max-w-lg rounded-3xl border border-slate-200 bg-white/95 p-7 sm:p-8 shadow-2xl ring-1 ring-black/5">
            <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-red-50 text-red-600">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M12 21s-7-4.35-7-10a4 4 0 0 1 7-2.65A4 4 0 0 1 19 11c0 5.65-7 10-7 10Z"/>
                </svg>
            </div>

            <h2 class="text-center text-2xl font-black text-slate-900">ফিরে আসায় স্বাগতম!</h2>
            <p class="mt-2 text-center text-slate-600 font-medium leading-relaxed">
                আপনি কি এই মুহূর্তে জরুরি প্রয়োজনে রক্ত দিতে প্রস্তুত?
            </p>

            <div class="mt-7 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <button type="button"
                        @click="
                            // TODO: এখানে future-এ availability=true DB update API call যোগ করা হবে
                            open = false
                        "
                        class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-5 py-3 text-sm font-black text-white shadow-sm shadow-emerald-200 transition hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                    হ্যাঁ, আমি প্রস্তুত
                </button>

                <button type="button"
                        @click="open = false"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2">
                    এখন নয়
                </button>
            </div>
        </div>
    </div>
@endif

{{-- 🩸 Recipient Confirmation Pop-up (The Truth Loop) --}}
@if(isset($pendingClaim))
    <div class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-900/80 backdrop-blur-md">
        <div class="bg-white rounded-[2rem] p-8 max-w-lg w-full mx-4 shadow-2xl border border-red-100 animate-pop-in">
            <div class="relative">
                {{-- Donor Avatar Thumbnail --}}
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
                    <span class="text-red-600 font-bold">{{ $pendingClaim->donor->name ?? 'একজন ডোনার' }}</span> ক্লেইম করেছেন যে তিনি আপনার রিকোয়েস্টে রক্ত দিয়েছেন। এটি কি সঠিক?
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

            <div class="grid grid-cols-1 gap-3 mt-8">
                <form action="{{ route('donations.recipient_verify', $pendingClaim->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="decision" value="approve">
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 rounded-2xl shadow-lg shadow-emerald-100 transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        হ্যাঁ, তিনি রক্ত দিয়েছেন
                    </button>
                </form>

                <form action="{{ route('donations.recipient_verify', $pendingClaim->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="decision" value="dispute">
                    <button type="submit" onclick="return confirm('আপনি কি নিশ্চিত যে এই ডোনার রক্ত দেননি? এটি তার প্রোফাইলে নেতিবাচক প্রভাব ফেলবে।')" class="w-full bg-white hover:bg-red-50 text-red-600 font-bold py-3.5 rounded-2xl border-2 border-red-100 transition-all">
                        না, তিনি আসেননি (Report)
                    </button>
                </form>
            </div>
            
            <p class="text-[10px] text-slate-400 text-center mt-6 font-bold uppercase tracking-tight">
                আপনার একটি কনফার্মেশন ডোনারকে উৎসাহিত করবে
            </p>
        </div>
    </div>
@endif

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col gap-8">
    
    {{-- A) Donor Quick Overview (Merged: Identity + Gamification) --}}
    @php $user = auth()->user(); @endphp
    @if($isDonor)
        @php
            $currentPoints = $gamificationStats['currentPoints'] ?? ($user->points ?? 0);
            $totalDonations = $gamificationStats['totalDonations'] ?? ($user->total_verified_donations ?? 0);
            $myRank = $gamificationStats['myRank'] ?? null;
            $nextMilestone = $gamificationStats['nextMilestone'] ?? null;
            $progressPercent = $gamificationStats['progressPercent'] ?? 0;
            $isEligibleTop = $user->is_eligible_to_donate;
            $nextDateTop = $user->next_eligible_date;
            $isEffectivelyAvailableTop = $isEligibleTop && $user->is_available;
        @endphp
        <div class="rounded-[2rem] overflow-hidden bg-slate-900 shadow-xl p-6 sm:p-8 relative">
            <div class="absolute top-0 right-0 w-64 h-64 bg-red-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 sm:gap-8">
                {{-- User Profile --}}
                <div class="flex items-center gap-4 sm:gap-5">
                    <div class="relative">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center border-4 border-slate-800 shadow-xl">
                            <span class="text-2xl sm:text-3xl font-black text-white">{{ $user->blood_group?->value ?? $user->blood_group ?? '?' }}</span>
                        </div>
                        @if($user->nid_status === 'verified' || $user->nid_status === 'approved' || $user->verified_badge)
                        <div class="absolute bottom-0 right-0 w-5 h-5 sm:w-6 sm:h-6 bg-blue-500 rounded-full border-2 border-slate-900 flex items-center justify-center text-white" title="Verified">
                            <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs sm:text-sm font-semibold mb-0.5">স্বাগতম ফিরে আসার জন্য,</p>
                        <h2 class="text-xl sm:text-2xl font-black text-white leading-tight">{{ $user->name }}</h2>
                        <p class="text-slate-400 text-xs sm:text-sm font-medium mt-1 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-slate-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                            <span class="truncate">{{ $user->upazila?->name ?? 'উপজেলা দেওয়া নেই' }}, {{ $user->district?->name ?? 'জেলা দেওয়া নেই' }}</span>
                        </p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-wrap gap-2 sm:gap-3 w-full md:w-auto">
                    <a href="{{ route('gamification.guide') }}" class="flex-1 md:flex-none text-center inline-flex items-center justify-center bg-white/10 hover:bg-white/20 text-white px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition">
                        🪙 গাইড
                    </a>
                    <a href="{{ route('leaderboard') }}" class="flex-1 md:flex-none text-center inline-flex items-center justify-center bg-white/10 hover:bg-white/20 text-white px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition">
                        লিডারবোর্ড
                    </a>
                    
                    <form action="{{ route('donor_profile.is_available_now') }}" method="POST" class="m-0 w-full sm:w-auto">
                        @csrf
                        <button type="submit" 
                                {{ !$isEligibleTop ? 'disabled' : '' }}
                                title="{{ !$isEligibleTop ? 'কুলডাউন চলাকালীন স্ট্যাটাস পরিবর্তন করা যাবে না' : 'উপস্থিতি স্ট্যাটাস পরিবর্তন করুন' }}"
                                class="inline-flex items-center justify-center gap-2 {{ $isEffectivelyAvailableTop ? 'bg-red-600 hover:bg-red-700 shadow-red-900/50' : 'bg-slate-700 hover:bg-slate-600 shadow-slate-900/50' }} text-white px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed w-full">
                            <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full {{ $isEffectivelyAvailableTop ? 'bg-white animate-pulse' : 'bg-slate-400' }}"></span>
                            {{ $isEffectivelyAvailableTop ? 'রক্তদানে প্রস্তুত' : 'বর্তমানে ব্যস্ত' }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Stats Row --}}
            <div class="relative z-10 grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mt-6 sm:mt-8">
                <div class="bg-white/5 rounded-2xl p-4 sm:p-5 border border-white/10 flex flex-col justify-center">
                    <p class="text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-1">মোট পয়েন্ট</p>
                    <p class="text-2xl sm:text-3xl font-black text-white">{{ number_format($currentPoints) }}</p>
                </div>
                <div class="bg-white/5 rounded-2xl p-4 sm:p-5 border border-white/10 flex flex-col justify-center">
                    <p class="text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-1">রক্তদান</p>
                    <p class="text-2xl sm:text-3xl font-black text-white">{{ $totalDonations }}</p>
                </div>
                <div class="bg-white/5 rounded-2xl p-4 sm:p-5 border border-white/10 flex flex-col justify-center">
                    <p class="text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-1">গ্লোবাল র‍্যাঙ্ক</p>
                    <p class="text-2xl sm:text-3xl font-black text-blue-400">{{ $myRank ? '#'.$myRank : '--' }}</p>
                </div>
                
                @if($nextMilestone)
                <div class="bg-white/5 rounded-2xl p-4 sm:p-5 border border-white/10 flex flex-col justify-center">
                    <p class="text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-1">পরবর্তী লক্ষ্য</p>
                    <div class="flex items-end justify-between mt-1">
                        <p class="text-xs sm:text-sm font-bold text-white truncate">{{ $nextMilestone['bn'] }}</p>
                        <span class="text-[10px] sm:text-xs font-bold text-slate-400">{{ $progressPercent }}%</span>
                    </div>
                    <div class="w-full h-1.5 rounded-full bg-slate-800 overflow-hidden mt-2" title="আর {{ $nextMilestone['donations'] - $totalDonations }} টি ডোনেশন বাকি">
                        <div class="h-full bg-gradient-to-r from-red-500 to-rose-500 rounded-full" style="width: {{ $progressPercent }}%"></div>
                    </div>
                </div>
                @else
                <div class="bg-white/5 rounded-2xl p-4 sm:p-5 border border-white/10 flex flex-col justify-center">
                    <p class="text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-1">স্ট্যাটাস</p>
                    <p class="text-sm sm:text-base font-black {{ $isEligibleTop ? 'text-emerald-400' : 'text-amber-400' }}">
                        {{ $isEligibleTop ? 'রক্তদানে যোগ্য' : 'কুলডাউনে আছেন' }}
                    </p>
                    @if(!$isEligibleTop && $nextDateTop)
                        <p class="text-[10px] sm:text-xs font-bold text-slate-400 mt-1">
                            {{ (int) now()->startOfDay()->diffInDays($nextDateTop->copy()->startOfDay()) }} দিন বাকি
                        </p>
                    @endif
                </div>
                @endif
            </div>

            @if($user->badges->count() > 0)
                <div class="relative z-10 flex flex-wrap gap-2 mt-4 sm:mt-5">
                    @foreach($user->badges->take(4) as $badge)
                        @php $bd = \App\Services\GamificationService::getBadgeDisplayData($badge->name); @endphp
                        <div class="inline-flex items-center gap-1.5 text-[10px] sm:text-xs font-bold bg-white/5 border border-white/10 text-slate-300 rounded-full px-2.5 py-1 sm:px-3 sm:py-1.5" title="{{ $bd['bn'] }}">
                            <span>{{ $bd['emoji'] }}</span>
                            <span class="truncate">{{ $bd['bn'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <x-donor-identity-header :user="$user" :total-contributions="$totalContributions ?? 0" />
    @endif

    {{-- 🚀 NID Upload Prompt for Organization Members --}}
    @if($user->organization_id && $user->nid_status === 'pending' && empty($user->nid_path))
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-start gap-4">
                <div class="shrink-0 text-amber-600 bg-amber-100 p-3 rounded-full hidden md:block">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-black text-amber-900">ভেরিফিকেশন প্রয়োজন!</h3>
                    <p class="text-sm text-amber-800 font-medium mt-1">
                        আপনি একটি ব্লাড ক্লাবের সদস্য হিসেবে যুক্ত হতে চেয়েছেন। ভেরিফাইড ডোনার (ব্লু-ব্যাজ) হতে আপনার এনআইডি (NID), জন্মনিবন্ধন বা স্টুডেন্ট আইডি কার্ডের ছবি আপলোড করুন।
                    </p>
                    
                    <form action="{{ route('donor.upload_nid') }}" method="POST" enctype="multipart/form-data" class="mt-4 flex flex-col sm:flex-row items-center gap-3">
                        @csrf
                        <input type="file" name="nid_document" accept=".jpg,.jpeg,.png,.pdf" class="w-full sm:w-auto text-sm text-amber-900 bg-white border border-amber-200 rounded-xl file:mr-4 file:py-2.5 file:px-4 file:border-0 file:text-sm file:font-bold file:bg-amber-600 file:text-white hover:file:bg-amber-700 cursor-pointer" required>
                        <button type="submit" class="w-full sm:w-auto bg-amber-700 hover:bg-amber-800 text-white px-8 py-2.5 rounded-xl font-bold transition-all shadow-sm">
                            আপলোড করুন
                        </button>
                    </form>
                    @error('nid_document') <p class="text-red-600 text-xs mt-2 font-bold">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    @elseif($user->organization_id && $user->nid_status === 'pending' && !empty($user->nid_path))
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 shadow-sm flex items-center gap-4">
            <div class="shrink-0 text-blue-600 bg-blue-100 p-3 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h3 class="text-base font-black text-blue-900">ডকুমেন্ট রিভিউ হচ্ছে...</h3>
                <p class="text-sm text-blue-700 font-medium mt-0.5">আপনার পরিচয়পত্র অর্গানাইজেশনের কাছে পাঠানো হয়েছে। তারা যাচাই করলে আপনার প্রোফাইলে ব্লু-ব্যাজ যুক্ত হবে।</p>
            </div>
        </div>
    @endif

{{-- B) Eligibility + Availability --}}
    @php
        $isEligible = $user->is_eligible_to_donate;
        $nextDate = $user->next_eligible_date;
    @endphp

    @if($isDonor)
    <div class="bg-white p-6 rounded-3xl border {{ $isEligible ? 'border-emerald-200 shadow-emerald-50' : 'border-amber-200 shadow-amber-50' }} shadow-lg flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full {{ $isEligible ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' }}">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-extrabold {{ $isEligible ? 'text-emerald-700' : 'text-amber-700' }}">
                    {{ $isEligible ? 'আপনি রক্তদানের জন্য যোগ্য' : 'আপনি আপাতত রক্তদানের জন্য যোগ্য নন' }}
                </h3>
                <p class="text-sm font-semibold text-slate-500 mt-1">
                    @if(!$user->last_donated_at)
                        আমাদের সিস্টেমে আপনার পূর্বের রক্তদানের কোনো রেকর্ড নেই।
                    @elseif($isEligible)
                        আপনার সর্বশেষ রক্তদানের পর ১২০ দিন পার হয়ে গেছে।
                    @else
                        পরবর্তী রক্তদানের তারিখ: <span class="text-slate-800 font-extrabold">{{ $nextDate->format('d M, Y') }}</span> 
                        (আর মাত্র <span class="text-red-600 font-extrabold">{{ (int) now()->startOfDay()->diffInDays($nextDate->startOfDay()) }} দিন</span> বাকি)
                    @endif
                </p>
            </div>
        </div>

        <form action="{{ route('donation.record.update') }}" method="POST" class="flex items-end gap-3 w-full md:w-auto">
            @csrf
            <div class="flex-1 md:w-48">
                <label class="block text-xs font-bold text-slate-500 mb-1">শেষ রক্তদানের তারিখ আপডেট করুন</label>
                <input type="date" name="last_donated_at" value="{{ $user->last_donated_at?->format('Y-m-d') }}" max="{{ date('Y-m-d') }}" {{ !$isEligible && $user->last_donated_at ? 'readonly disabled' : '' }} class="w-full rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-700 {{ !$isEligible && $user->last_donated_at ? 'bg-slate-100 cursor-not-allowed opacity-70' : '' }}">
            </div>
            <button type="submit" {{ !$isEligible && $user->last_donated_at ? 'disabled' : '' }} class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-lg font-extrabold shadow-sm transition-colors {{ !$isEligible && $user->last_donated_at ? 'opacity-50 cursor-not-allowed' : '' }}">
                সেভ
            </button>
        </form>
    </div>

    @else
    {{-- Become a Donor Upgrade CTA --}}
    <div x-data="{ upgradeModalOpen: {{ $errors->any() ? 'true' : 'false' }} }" class="bg-gradient-to-r from-red-50 to-red-100 p-6 rounded-3xl border border-red-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-white text-red-600 shadow-sm">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-extrabold text-slate-900">
                    রক্তদাতা হিসেবে যুক্ত হতে চান?
                </h3>
                <p class="text-sm font-semibold text-slate-600 mt-1">
                    আপনার প্রোফাইল আপগ্রেড করুন এবং মানুষের জীবন বাঁচাতে অবদান রাখুন।
                </p>
            </div>
        </div>
        <button @click="upgradeModalOpen = true" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-extrabold shadow-md transition-all text-center whitespace-nowrap">
            রক্তদাতা হোন (Become a Donor)
        </button>

        {{-- Upgrade Modal --}}
        <div x-show="upgradeModalOpen"
             style="display: none;"
             class="fixed inset-0 z-[120] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto"
             x-transition.opacity>
            <div @click.away="upgradeModalOpen = false" class="w-full max-w-2xl rounded-2xl bg-white shadow-2xl border border-slate-200 text-left my-8">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white rounded-t-2xl z-10">
                    <h3 class="text-xl font-black text-slate-900">রক্তদাতা হিসেবে প্রোফাইল আপগ্রেড করুন</h3>
                    <button @click="upgradeModalOpen = false" class="text-slate-400 hover:text-red-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6">
                    <form method="POST" action="{{ route('profile.upgrade_to_donor') }}" class="space-y-6">
                        @csrf
                        
                        @if(empty($user->phone))
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">ফোন নম্বর <span class="text-red-500">*</span></label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" class="w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500" required placeholder="01XXX-XXXXXX">
                            @error('phone') <p class="text-xs font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">রক্তের গ্রুপ <span class="text-red-500">*</span></label>
                                <select name="blood_group" class="w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500" required>
                                    <option value="">নির্বাচন করুন</option>
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                        <option value="{{ $bg }}" @selected(old('blood_group', $user->blood_group?->value ?? $user->blood_group) == $bg)>{{ $bg }}</option>
                                    @endforeach
                                </select>
                                @error('blood_group') <p class="text-xs font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">লিঙ্গ <span class="text-red-500">*</span></label>
                                <select name="gender" class="w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500" required>
                                    <option value="">নির্বাচন করুন</option>
                                    <option value="male" @selected(old('gender', $user->gender) == 'male')>পুরুষ</option>
                                    <option value="female" @selected(old('gender', $user->gender) == 'female')>মহিলা</option>
                                </select>
                                @error('gender') <p class="text-xs font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">লোকেশন (কোথায় রক্ত দিতে ইচ্ছুক) <span class="text-red-500">*</span></label>
                            <x-location-selector 
                                :selected-division="old('division_id', $user->division_id)"
                                :selected-district="old('district_id', $user->district_id)"
                                :selected-upazila="old('upazila_id', $user->upazila_id)"
                            />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">ওজন (কেজি)</label>
                                <input type="number" name="weight" value="{{ old('weight', $user->weight) }}" class="w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500" placeholder="যেমন: 65">
                                @error('weight') <p class="text-xs font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">শেষ রক্তদানের তারিখ</label>
                                <input type="date" name="last_donation_date" value="{{ old('last_donation_date', $user->last_donated_at?->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}" class="w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500 text-slate-700">
                                @error('last_donation_date') <p class="text-xs font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end border-t border-slate-100">
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3 rounded-xl font-black shadow-md shadow-emerald-200 transition-all">
                                আপগ্রেড নিশ্চিত করুন
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
    {{-- 2. Core Actions (CTA Row) --}}
    <div class="grid grid-cols-1 {{ $isDonor ? 'md:grid-cols-4' : 'md:grid-cols-3' }} gap-5">
        {{-- Action 1: Create Request --}}
        <a href="{{ route('requests.create') }}" class="group relative overflow-hidden p-6 rounded-3xl bg-gradient-to-br from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 transition-all shadow-lg shadow-red-100 hover:-translate-y-1 flex flex-col gap-3 min-h-[150px]">
            <div class="absolute -right-4 -top-4 w-28 h-28 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
            <div class="w-11 h-11 bg-white/20 rounded-2xl flex items-center justify-center text-white border border-white/30 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div>
                <h3 class="text-white font-black text-lg leading-tight">জরুরি রক্তের দরকার?</h3>
                <p class="text-red-100 text-sm font-medium mt-1 leading-snug">নতুন রিকোয়েস্ট তৈরি করুন এবং ডোনারদের সাথে যোগাযোগ করুন।</p>
            </div>
        </a>

        {{-- Action 2: Donate Blood --}}
        <a href="{{ route('requests.index') }}" class="group relative overflow-hidden p-6 rounded-3xl bg-white hover:shadow-lg transition-all shadow-sm border border-slate-200 hover:border-emerald-300 hover:-translate-y-1 flex flex-col gap-3 min-h-[150px]">
            <div class="absolute -right-4 -bottom-4 w-28 h-28 bg-emerald-50 rounded-full blur-xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col gap-3">
                <div class="w-11 h-11 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 border border-emerald-100 shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-slate-900 font-black text-lg leading-tight">রক্ত দিতে চান?</h3>
                    <p class="text-slate-500 text-sm font-medium mt-1 leading-snug">আপনার এলাকার সাম্প্রতিক রিকোয়েস্টগুলো দেখুন এবং সাড়া দিন।</p>
                </div>
            </div>
        </a>

        {{-- Action 3: My Requests --}}
        <a href="{{ route('requests.my-requests') }}" class="group relative overflow-hidden p-6 rounded-3xl bg-white hover:shadow-lg transition-all shadow-sm border border-slate-200 hover:border-blue-300 hover:-translate-y-1 flex flex-col gap-3 min-h-[150px]">
            <div class="absolute -left-4 -bottom-4 w-28 h-28 bg-blue-50 rounded-full blur-xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col gap-3">
                <div class="w-11 h-11 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 border border-blue-100 shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <h3 class="text-slate-900 font-black text-lg leading-tight">আমার রিকোয়েস্ট</h3>
                    <p class="text-slate-500 text-sm font-medium mt-1 leading-snug">আপনার তৈরি করা রক্তের রিকোয়েস্টগুলো দেখুন ও ম্যানেজ করুন।</p>
                </div>
            </div>
        </a>

        @if($isDonor)
            <button type="button"
                    x-data
                    @click="$dispatch('open-modal', 'offline-donation-claim')"
                    class="group relative overflow-hidden p-6 rounded-3xl bg-white hover:shadow-lg transition-all shadow-sm border border-slate-200 hover:border-red-300 hover:-translate-y-1 flex flex-col gap-3 min-h-[150px] text-left">
                <div class="absolute -left-4 -top-4 w-28 h-28 bg-red-50 rounded-full blur-xl pointer-events-none"></div>
                <div class="relative z-10 flex flex-col gap-3">
                    <div class="w-11 h-11 bg-red-50 rounded-2xl flex items-center justify-center text-red-600 border border-red-100 shrink-0 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-slate-900 font-black text-lg leading-tight">অফলাইনে রক্তদান ক্লেইম করুন</h3>
                        <p class="text-slate-500 text-sm font-medium mt-1 leading-snug">ফোনে যোগাযোগে করা রক্তদান যাচাই করে পয়েন্ট যুক্ত করুন।</p>
                    </div>
                </div>
            </button>
        @endif
    </div>

    @if($isDonor)
        <x-modal name="offline-donation-claim" :show="$errors->hasAny(['recipient_phone', 'patient_name', 'district_id', 'hospital_name', 'donation_date', 'proof_path'])" maxWidth="2xl">
            <form method="POST" action="{{ route('offline-claims.store') }}" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf

                <div>
                    <h3 class="text-lg font-extrabold text-slate-900">অফলাইন রক্তদান ক্লেইম</h3>
                    <p class="text-sm font-medium text-slate-500 mt-1">রিকোয়েস্ট ছাড়া ফোনে যোগাযোগে রক্ত দিলে এখানে ক্লেইম জমা দিন।</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">গ্রহীতার ফোন নম্বর <span class="text-red-500">*</span></label>
                        <input type="text" name="recipient_phone" value="{{ old('recipient_phone') }}" placeholder="01XXXXXXXXX" class="w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500" required>
                        @error('recipient_phone') <p class="text-xs font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">রোগীর নাম <span class="text-red-500">*</span></label>
                        <input type="text" name="patient_name" value="{{ old('patient_name') }}" class="w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500" required>
                        @error('patient_name') <p class="text-xs font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">জেলা <span class="text-red-500">*</span></label>
                        <select name="district_id" class="w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500" required>
                            <option value="">জেলা নির্বাচন করুন</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}" @selected((int) old('district_id') === (int) $district->id)>{{ $district->name }}</option>
                            @endforeach
                        </select>
                        @error('district_id') <p class="text-xs font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">রক্তদানের তারিখ <span class="text-red-500">*</span></label>
                        <input type="date" name="donation_date" value="{{ old('donation_date') }}" max="{{ now()->toDateString() }}" class="w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500" required>
                        @error('donation_date') <p class="text-xs font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">হাসপাতালের নাম (ঐচ্ছিক)</label>
                    <input type="text" name="hospital_name" value="{{ old('hospital_name') }}" class="w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500">
                    @error('hospital_name') <p class="text-xs font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">প্রুফ ছবি (ঐচ্ছিক)</label>
                    <input type="file" name="proof_path" accept="image/*" class="w-full rounded-xl border border-slate-300 bg-white text-sm font-semibold file:mr-4 file:border-0 file:bg-red-600 file:px-4 file:py-2.5 file:text-white hover:file:bg-red-700">
                    <p class="text-xs text-slate-500 font-medium mt-1">ছবি না দিলে ক্লেইমটি অ্যাডমিন রিভিউতে যাবে।</p>
                    @error('proof_path') <p class="text-xs font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" x-data @click="$dispatch('close-modal', 'offline-donation-claim')" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50 transition">
                        বাতিল
                    </button>
                    <button type="submit" class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-black text-white hover:bg-red-700 shadow-sm transition">
                        ক্লেইম সাবমিট
                    </button>
                </div>
            </form>
        </x-modal>
    @endif


    {{-- E) My Commitments (Ongoing) --}}
    @if(isset($ongoingCommitments) && $ongoingCommitments->count() > 0)
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">আমার চলমান কমিটমেন্ট</h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">যে রিকোয়েস্টগুলোতে আপনি রক্ত দেওয়ার প্রতিশ্রুতি দিয়েছেন</p>
                </div>
            </div>
            <a href="{{ route('requests.index') }}" class="text-xs font-bold text-red-600 hover:text-red-700 flex items-center gap-1 hover:underline underline-offset-2">সব দেখুন →</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <tbody class="divide-y divide-slate-100 text-sm">
                    @foreach($ongoingCommitments as $commitment)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-extrabold text-slate-900">রোগী: {{ $commitment->bloodRequest->patient_name ?? 'তথ্য নেই' }}</div>
                                <div class="text-xs font-bold text-slate-500 mt-0.5">গ্রুপ: <span class="text-blue-600">{{ $commitment->bloodRequest->blood_group?->value ?? $commitment->bloodRequest->blood_group }}</span></div>
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
                                <a href="{{ route('requests.show', $commitment->blood_request_id) }}" class="inline-flex items-center justify-center px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-extrabold text-slate-700 hover:bg-slate-100 transition">
                                    ডিটেইলস
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- 7. Verified Donation History --}}
    <div>
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">ভেরিফাইড রক্তদান হিস্ট্রি</h3>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">আপনার অতীতের সফল রক্তদানের লগ</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase tracking-wider font-bold text-slate-500">
                            <th class="px-6 py-4">তারিখ</th>
                            <th class="px-6 py-4">হাসপাতাল ও লোকেশন</th>
                            <th class="px-6 py-4">রিকোয়েস্ট রেফারেন্স</th>
                            <th class="px-6 py-4 text-right">স্ট্যাটাস</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @if(isset($donationHistory))
                            @forelse($donationHistory as $history)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-extrabold text-slate-900">{{ $history->fulfilled_at ? $history->fulfilled_at->format('d M, Y') : 'তথ্য নেই' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-700">{{ $history->bloodRequest->hospital?->display_name ?? 'তথ্য নেই' }}</div>
                                        <div class="text-xs text-slate-500 font-medium">{{ $history->bloodRequest->district?->name ?? 'তথ্য নেই' }}, {{ $history->bloodRequest->upazila?->name ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-bold text-slate-400 font-mono bg-slate-100 px-2 py-1 rounded-md">
                                            REQ-{{ str_pad($history->blood_request_id, 4, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 uppercase tracking-widest">
                                            সম্পন্ন
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="text-4xl mb-3">📋</div>
                                        <p class="font-bold text-slate-600">কোনো হিস্ট্রি পাওয়া যায়নি</p>
                                        <p class="text-xs text-slate-500 mt-1">আপনার প্রথম রক্তদানের পর এখানে তা সংরক্ষিত থাকবে।</p>
                                    </td>
                                </tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- 4. User Impact (Stats Grid) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900">{{ $totalRequestsMade ?? 0 }}</div>
                <div class="text-xs font-bold text-slate-500 mt-0.5">মোট রিকোয়েস্ট</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-emerald-100 p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </div>
            <div>
                <div class="text-2xl font-black text-emerald-600">{{ $totalContributions ?? 0 }}</div>
                <div class="text-xs font-bold text-emerald-500 mt-0.5">আপনার অবদান</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-red-100 p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="text-2xl font-black text-red-600">{{ $fulfilledRequests ?? 0 }}</div>
                <div class="text-xs font-bold text-red-500 mt-0.5">সফল রিকোয়েস্ট</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-blue-100 p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div>
                <div class="text-2xl font-black text-blue-600">{{ $successRate ?? 0 }}{{ ($successRate !== 'তথ্য নেই') ? '%' : '' }}</div>
                <div class="text-xs font-bold text-blue-500 mt-0.5">সফলতার হার</div>
            </div>
        </div>
    </div>

    
    @if(isset($recentRequests))
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            </div>
            <div>
                <h2 class="text-base font-extrabold text-slate-900">আপনার সাম্প্রতিক রিকোয়েস্টসমূহ</h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">{{ ($isDonor ?? false) ? 'আপনি যেসব রিকোয়েস্টে সাড়া দিয়েছেন (গৃহীত)' : 'সর্বশেষ ৫টি রিকোয়েস্টের আপডেট' }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider font-bold">
                        <th class="px-6 py-4 border-b border-slate-100">{{ ($isDonor ?? false) ? 'রিকোয়েস্ট ও গ্রুপ' : 'রোগীর নাম ও গ্রুপ' }}</th>
                        <th class="px-6 py-4 border-b border-slate-100">দরকার</th>
                        <th class="px-6 py-4 border-b border-slate-100">সাড়া (গৃহীত)</th>
                        <th class="px-6 py-4 border-b border-slate-100">স্ট্যাটাস</th>
                        <th class="px-6 py-4 border-b border-slate-100 text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($recentRequests as $req)
                        @php
                            $isOwner = ((int) ($req->requested_by ?? 0) === (int) auth()->id());
                            $currentStatus = strtolower((string) $req->status);
                            $isExpiredStatus = $currentStatus === 'expired';
                            $isPendingLikeStatus = in_array($currentStatus, ['pending', 'in_progress'], true);
                            $isPastNeededAt = $req->needed_at && \Carbon\Carbon::parse($req->needed_at)->isPast();
                            $canRenew = $isOwner && ($isExpiredStatus || ($isPastNeededAt && $isPendingLikeStatus));
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-extrabold text-slate-900">{{ $req->patient_name ?? 'রোগী' }}</div>
                                <div class="text-xs font-bold text-red-600 mt-0.5">{{ $req->blood_group?->value ?? (string) $req->blood_group }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-700">{{ $req->needed_at?->format('d M, Y') ?? 'যত দ্রুত সম্ভব' }}</div>
                                <div class="text-xs text-slate-500 font-medium">{{ $req->needed_at?->format('h:i A') ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-extrabold text-emerald-600">{{ $req->accepted_responses ?? 0 }}</span>
                                    <span class="text-slate-400 font-bold">/</span>
                                    <span class="font-semibold text-slate-500">{{ $req->total_responses ?? 0 }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if(strtolower($req->status) === 'fulfilled')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 uppercase">
                                        সম্পন্ন
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 uppercase">
                                        {{ $req->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div x-data="{ renewOpen: false }" class="inline-flex items-center gap-2">
                                    @if($canRenew)
                                        <button type="button"
                                                @click="renewOpen = true"
                                                class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-xs font-extrabold bg-red-600 text-white hover:bg-red-700 transition">
                                            রিনিউ করুন
                                        </button>
                                    @endif

                                    <a href="{{ route('requests.show', $req->id) }}" class="inline-flex items-center justify-center px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-extrabold text-slate-700 hover:bg-slate-100 hover:text-red-600 transition">
                                        ডিটেইলস
                                    </a>

                                    @if($canRenew)
                                        <div x-show="renewOpen"
                                             style="display:none;"
                                             class="fixed inset-0 z-[120] flex items-center justify-center bg-slate-900/55 backdrop-blur-sm p-4"
                                             x-transition.opacity>
                                            <div @click.away="renewOpen = false" class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl border border-slate-200 text-left">
                                                <div class="mb-4">
                                                    <h3 class="text-lg font-black text-slate-900">রিকোয়েস্ট রিনিউ করুন</h3>
                                                    <p class="mt-1 text-xs font-semibold text-slate-500">নতুন সময় ও জরুরিতা সেট করলে রিকোয়েস্ট আবার ফিডে যাবে।</p>
                                                </div>

                                                <form method="POST" action="{{ route('requests.renew', $req->id) }}" class="space-y-4" data-renew-modal>
                                                    @csrf

                                                    <div>
                                                        <label class="block text-xs font-bold text-slate-600 mb-1.5">কবে রক্ত লাগবে</label>
                                                        <input type="datetime-local"
                                                               name="needed_at"
                                                               value="{{ old('needed_at') }}"
                                                               class="renew-needed-at w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500" required>
                                                        @error('needed_at')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                                                    </div>

                                                    <div>
                                                        <label class="block text-xs font-bold text-slate-600 mb-1.5">জরুরিতা</label>
                                                        <select name="urgency" class="renew-urgency w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500" required>
                                                            @foreach (\App\Enums\UrgencyLevel::cases() as $case)
                                                                <option value="{{ $case->value }}" @selected(old('urgency', $req->urgency?->value ?? (string) $req->urgency) === $case->value)>
                                                                    {{ $case->label() }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('urgency')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                                                        <p class="renew-threshold-note mt-1 text-xs font-semibold text-amber-700 hidden"></p>
                                                    </div>

                                                    <div class="pt-2 flex items-center justify-end gap-2">
                                                        <button type="button"
                                                                @click="renewOpen = false"
                                                                class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">
                                                            বাতিল
                                                        </button>
                                                        <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-xs font-black text-white hover:bg-red-700">
                                                            রিনিউ সাবমিট
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500 font-medium">
                                {{ ($isDonor ?? false) ? 'আপনি এখনো কোনো রিকোয়েস্টে গৃহীত সাড়া দেননি।' : 'আপনি এখনো কোনো রক্তের রিকোয়েস্ট করেননি।' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- 5. Motivation Engine (Gamification Summary) --}}
    {{-- ══════════════════════════════════════════
         🪪 Digital Smart Card — QR Verified Identity
    ══════════════════════════════════════════ --}}
    @if(false && $user->qr_token && $user->nid_status === 'verified')
    <div class="mb-10 relative overflow-hidden rounded-3xl border border-slate-700/50 shadow-2xl"
         style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #1c0808 100%);">

        {{-- Decorative blobs --}}
        <div class="absolute -top-16 -right-16 w-60 h-60 rounded-full pointer-events-none"
             style="background: radial-gradient(circle, rgba(220,38,38,0.13) 0%, transparent 70%);"></div>
        <div class="absolute -bottom-12 -left-12 w-48 h-48 rounded-full pointer-events-none"
             style="background: radial-gradient(circle, rgba(153,27,27,0.10) 0%, transparent 70%);"></div>
        <div class="absolute top-0 left-0 right-0 h-px pointer-events-none"
             style="background: linear-gradient(to right, transparent, rgba(220,38,38,0.35), transparent);"></div>

        <div class="relative z-10 p-6 sm:p-8">

            {{-- ── Header Row ── --}}
            <div class="flex items-start justify-between gap-4 mb-7">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0"
                         style="background:rgba(220,38,38,.12); border:1px solid rgba(220,38,38,.22);">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-white font-black text-base leading-tight">ডিজিটাল স্মার্ট কার্ড</h2>
                        <p class="text-slate-500 text-xs font-semibold mt-0.5">QR কোড স্ক্যান করে পরিচয় যাচাই করুন</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-emerald-400 text-[11px] font-bold rounded-full shrink-0"
                      style="background:rgba(74,222,128,.1); border:1px solid rgba(74,222,128,.25);">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    এনআইডি ভেরিফাইড
                </span>
            </div>

            {{-- ── Card Body ── --}}
            <div class="flex flex-col sm:flex-row items-center gap-7">

                {{-- QR Code (JavaScript rendered) --}}
                <div class="shrink-0 flex flex-col items-center gap-2">
                    <div class="bg-white p-3.5 rounded-2xl"
                         style="box-shadow: 0 20px 40px rgba(0,0,0,.55), 0 0 0 4px rgba(255,255,255,.06);">
                        <div id="smart-card-qr" style="width:148px;height:148px;"></div>
                    </div>
                    <p class="text-slate-600 text-[10px] font-bold uppercase tracking-widest">হাসপাতালে স্ক্যান করুন</p>
                </div>

                {{-- Donor Details --}}
                <div class="flex-1 w-full text-center sm:text-left">

                    {{-- Name --}}
                    <h3 class="text-2xl sm:text-3xl font-black text-white leading-tight mb-3">{{ $user->name }}</h3>

                    {{-- Chips Row --}}
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mb-4">

                        {{-- Blood Group --}}
                        <span class="inline-flex items-center gap-1.5 text-lg font-black px-4 py-2 rounded-xl text-red-300"
                              style="background:rgba(220,38,38,.15); border:1.5px solid rgba(220,38,38,.3);">
                            🩸 {{ $user->blood_group?->value ?? 'তথ্য নেই' }}
                        </span>

                        {{-- Availability Status --}}
                        @if(($isEligible ?? false) && $user->is_available)
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-2 rounded-xl text-emerald-300"
                                  style="background:rgba(22,163,74,.12); border:1.5px solid rgba(22,163,74,.25);">
                                <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                উপলব্ধ
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-2 rounded-xl text-amber-300"
                                  style="background:rgba(217,119,6,.1); border:1.5px solid rgba(217,119,6,.25);">
                                <span class="inline-block w-2 h-2 rounded-full bg-amber-400"></span>
                                কুলডাউনে
                            </span>
                        @endif

                        {{-- Verified Donor Badge --}}
                        @if($user->verified_badge)
                            <span class="inline-flex items-center gap-1 text-xs font-bold px-3 py-2 rounded-xl text-blue-300"
                                  style="background:rgba(59,130,246,.12); border:1.5px solid rgba(59,130,246,.25);">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                ভেরিফাইড ডোনার
                            </span>
                        @endif
                    </div>

                    {{-- Quick Stats --}}
                    <div class="grid grid-cols-2 gap-2.5 mb-5">
                        <div class="rounded-xl p-3 text-center sm:text-left"
                             style="background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.08);">
                            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider mb-1">মোট ডোনেশন</p>
                            <p class="text-white font-black text-xl leading-none">
                                {{ $totalContributions ?? 0 }}
                                <span class="text-slate-500 font-semibold text-xs ml-0.5">বার</span>
                            </p>
                        </div>
                        <div class="rounded-xl p-3 text-center sm:text-left"
                             style="background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.08);">
                            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider mb-1">গ্যামিফিকেশন পয়েন্ট</p>
                            <p class="text-amber-400 font-black text-xl leading-none">
                                {{ number_format($user->points ?? 0) }}
                                <span class="text-slate-500 font-semibold text-xs ml-0.5">পয়েন্ট</span>
                            </p>
                        </div>
                    </div>

                    {{-- Shareable Link --}}
                    <div class="flex items-center gap-2 mb-3">
                        <input id="smart-card-link"
                               type="text"
                               value="{{ route('public.verify', $user->qr_token) }}"
                               class="flex-1 text-xs py-2.5 px-3 rounded-xl text-slate-400 font-mono focus:outline-none focus:ring-1 focus:ring-red-600/40 min-w-0 truncate"
                               style="background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1);"
                               readonly>
                        <button onclick="copySmartCardLink()" id="smart-card-copy-btn"
                                class="shrink-0 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-colors whitespace-nowrap border border-slate-600">
                            কপি লিঙ্ক
                        </button>
                    </div>

                    {{-- Dual-Asset Action Buttons --}}
                    <div class="grid grid-cols-2 gap-2">
                        <!-- 1. Offline Wallet Pass (Download) -->
                        <a href="{{ route('public.verify.download', $user->qr_token) }}" 
                           download
                           class="flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs px-3 py-2.5 rounded-xl transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            ডাউনলোড কার্ড
                        </a>

                        <!-- 2. Social OG Card (Share) -->
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('public.verify', $user->qr_token)) }}" 
                           target="_blank"
                           class="flex items-center justify-center gap-2 bg-[#1877F2] hover:bg-[#166fe5] text-white font-bold text-xs px-3 py-2.5 rounded-xl transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            ফেসবুকে শেয়ার
                        </a>
                    </div>
                </div>
            </div>

            {{-- Security Note --}}
            <p class="text-slate-600 text-[10px] font-semibold mt-5 pt-4 text-center"
               style="border-top: 1px solid rgba(255,255,255,.06);">
                🔒 এই QR কোডে ফোন নম্বর, ইমেইল বা ব্যক্তিগত তথ্য নেই — শুধুমাত্র পরিচয় যাচাই করা যাবে।
            </p>
        </div>
    </div>

    {{-- QR Code Generator via CDN (no PHP extension required) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var verifyUrl = @json(route('public.verify', $user->qr_token));
            var container = document.getElementById('smart-card-qr');
            if (container) {
                new QRCode(container, {
                    text: verifyUrl,
                    width: 148,
                    height: 148,
                    colorDark : "#1e293b",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.M
                });
            }
        });

        window.copySmartCardLink = function() {
            var copyText = document.getElementById("smart-card-link");
            copyText.select();
            copyText.setSelectionRange(0, 99999); 
            navigator.clipboard.writeText(copyText.value);
            
            var btn = document.getElementById("smart-card-copy-btn");
            var originalText = btn.innerText;
            btn.innerText = "কপি হয়েছে!";
            btn.classList.replace("bg-red-600", "bg-emerald-600");
            btn.classList.replace("hover:bg-red-700", "hover:bg-emerald-700");
            
            setTimeout(function() {
                btn.innerText = originalText;
                btn.classList.replace("bg-emerald-600", "bg-red-600");
                btn.classList.replace("hover:bg-emerald-700", "hover:bg-red-700");
            }, 2000);
        }
    </script>
    @endif

    @if($isDonor)

    <x-dashboard.recovery-timer :items="$donationRecoveryCards" />

    {{-- 7. Static Info (Points & Badges Rules) --}}
    {{-- ══════════════════════════════════════════
         🪙 পয়েন্ট ও ব্যাজ সিস্টেম — Quick Guide Teaser
         ব্যবহারকারী কীভাবে পয়েন্ট আয় করতে পারে সেটা
         সংক্ষেপে দেখানো হচ্ছে, পূর্ণ গাইডে লিঙ্ক সহ।
    ══════════════════════════════════════════ --}}
    <div class="rounded-3xl overflow-hidden border border-slate-200 shadow-sm bg-white">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center text-xl shrink-0">🪙</div>
                <div>
                    <h2 class="text-white font-black text-base leading-tight">পয়েন্ট ও ব্যাজ সিস্টেম</h2>
                    <p class="text-amber-100 text-xs font-semibold">রক্তদান করুন, পয়েন্ট আয় করুন, ব্যাজ জিতুন!</p>
                </div>
            </div>
            <a href="{{ route('gamification.guide') }}"
               class="inline-flex items-center gap-2 bg-white text-amber-700 font-extrabold text-xs px-4 py-2.5 rounded-xl hover:bg-amber-50 transition-colors shadow-sm shrink-0">
                সম্পূর্ণ গাইড দেখুন
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        {{-- Earning Actions Grid --}}
        <div class="p-6">
            <p class="text-xs font-extrabold text-slate-400 uppercase tracking-widest mb-4">⚡ কীভাবে পয়েন্ট আয় করবেন</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-6">

                {{-- সফল রক্তদান --}}
                <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-100 rounded-2xl">
                    <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center text-lg shrink-0">🩸</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-extrabold text-slate-800">সফল রক্তদান</p>
                        <p class="text-xs text-slate-500 font-medium truncate">রিকোয়েস্টে সাড়া দিয়ে রক্তদান করুন</p>
                    </div>
                    <span class="text-sm font-black text-red-600 shrink-0">+৫০</span>
                </div>

                {{-- First Responder Bonus --}}
                <div class="flex items-center gap-3 p-4 bg-orange-50 border border-orange-100 rounded-2xl">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center text-lg shrink-0">⚡</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-extrabold text-slate-800">প্রথম সাড়াদাতা বোনাস</p>
                        <p class="text-xs text-slate-500 font-medium truncate">৩ ঘণ্টার মধ্যে ইমার্জেন্সিতে রেসপন্ড</p>
                    </div>
                    <span class="text-sm font-black text-orange-600 shrink-0">+১০</span>
                </div>

                {{-- রেফারেল --}}
                <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-lg shrink-0">👥</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-extrabold text-slate-800">রেফারেল বোনাস</p>
                        <p class="text-xs text-slate-500 font-medium truncate">বন্ধু সাইন-আপে +১০, প্রথম ডোনেশনে +৩০</p>
                    </div>
                    <span class="text-sm font-black text-emerald-600 shrink-0">+১০/+৩০</span>
                </div>

                {{-- প্রোফাইল কমপ্লিট --}}
                <div class="flex items-center gap-3 p-4 bg-blue-50 border border-blue-100 rounded-2xl">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-lg shrink-0">✅</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-extrabold text-slate-800">প্রোফাইল কমপ্লিট</p>
                        <p class="text-xs text-slate-500 font-medium truncate">১০০% প্রোফাইল + NID ভেরিফিকেশন</p>
                    </div>
                    <span class="text-sm font-black text-blue-600 shrink-0">+২০</span>
                </div>

                {{-- গ্রহীতার রিভিউ --}}
                <div class="flex items-center gap-3 p-4 bg-purple-50 border border-purple-100 rounded-2xl">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center text-lg shrink-0">💬</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-extrabold text-slate-800">গ্রহীতার পজিটিভ রিভিউ</p>
                        <p class="text-xs text-slate-500 font-medium truncate">রক্ত পাওয়ার পর গ্রহীতা রিভিউ দিলে</p>
                    </div>
                    <span class="text-sm font-black text-purple-600 shrink-0">+১০</span>
                </div>

                {{-- ব্যাজ টিজার --}}
                <div class="flex items-center gap-3 p-4 bg-gradient-to-br from-yellow-50 to-amber-50 border border-amber-100 rounded-2xl">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-lg shrink-0">🏅</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-extrabold text-slate-800">মাইলস্টোন ব্যাজ</p>
                        <p class="text-xs text-slate-500 font-medium truncate">ব্রোঞ্জ → সিলভার → গোল্ড → প্লাটিনাম</p>
                    </div>
                    <a href="{{ route('gamification.guide') }}"
                       class="text-xs font-extrabold text-amber-600 hover:text-amber-700 shrink-0 underline underline-offset-2">
                        দেখুন →
                    </a>
                </div>
            </div>

            {{-- Bottom CTA --}}
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-100">
                <p class="text-sm text-slate-500 font-medium">
                    🎯 আরো ব্যাজ, স্পেশাল অ্যাচিভমেন্ট এবং পূর্ণ পয়েন্ট সিস্টেম দেখতে —
                </p>
                <a href="{{ route('gamification.guide') }}"
                   class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-extrabold text-sm px-6 py-3 rounded-xl hover:from-amber-600 hover:to-orange-600 transition-all shadow-md shadow-amber-100 shrink-0">
                    🪙 পয়েন্ট ও ব্যাজ সিস্টেম সম্পর্কে জানুন
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- 3. Actionable Queue --}}
    {{-- ══════════════════════════════════════════════════════════════
         🔴 LOCAL EMERGENCY RADAR
         ইউজারের জেলার সক্রিয় রক্তের রিকোয়েস্ট — Priority sorted
    ══════════════════════════════════════════════════════════════ --}}
    @if($radarRequests->isNotEmpty())
    <div>

        {{-- ── Header ── --}}
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="relative flex items-center">
                    {{-- Pulsing outer ring --}}
                    <span class="absolute inline-flex h-10 w-10 rounded-full bg-red-500 opacity-20 animate-ping"></span>
                    <div class="relative w-10 h-10 rounded-xl bg-red-600 flex items-center justify-center shadow-lg shadow-red-200">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-900 leading-tight">আপনার এলাকায় জরুরি অনুরোধ</h2>
                    <p class="text-xs font-semibold text-slate-500">
                        আপনার এলাকায় স্ক্যান করা হচ্ছে…
                    </p>
                </div>
            </div>
            <a href="{{ route('requests.index') }}"
               class="text-xs font-bold text-red-600 hover:text-red-700 flex items-center gap-1 hover:underline underline-offset-2">
                সব দেখুন
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        {{-- ── Request Cards Grid ── --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @php $user = auth()->user(); @endphp
            @foreach($radarRequests as $req)
                @php
                    $urgencyVal  = $req->urgency?->value ?? $req->urgency ?? 'normal';
                    $reqGroup    = $req->blood_group?->value ?? $req->blood_group ?? '?';
                    $userGroup   = $user->blood_group?->value ?? $user->blood_group ?? '';
                    $isMyGroup   = ($reqGroup === $userGroup);
                    $isEmergency = ($urgencyVal === 'emergency');
                    $isUrgent    = ($urgencyVal === 'urgent');
                    $neededAt    = $req->needed_at;
                    $diffHours   = $neededAt ? (int) now()->diffInHours($neededAt, false) : null;
                    // Light card styles
                    $cardBorder  = $isEmergency ? 'border-red-200 shadow-red-50' : ($isUrgent ? 'border-amber-200 shadow-amber-50' : 'border-slate-200');
                    $groupBg     = $isEmergency ? 'bg-red-100 text-red-700 ring-1 ring-red-200' : ($isUrgent ? 'bg-amber-100 text-amber-700 ring-1 ring-amber-200' : 'bg-slate-100 text-slate-600 ring-1 ring-slate-200');
                @endphp

                <div class="relative overflow-hidden rounded-2xl bg-white border {{ $cardBorder }} shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">

                    {{-- Pulse bar at top for emergency --}}
                    @if($isEmergency)
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-red-400 via-red-600 to-red-400 animate-pulse rounded-t-2xl"></div>
                    @elseif($isUrgent)
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-300 via-amber-500 to-amber-300 rounded-t-2xl"></div>
                    @endif

                    {{-- My blood group match indicator --}}
                    @if($isMyGroup)
                        <div class="absolute top-3 right-3 z-10">
                            <span class="inline-flex items-center gap-1 text-[10px] font-black text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                                ✓ আপনার গ্রুপ
                            </span>
                        </div>
                    @endif

                    <div class="p-5">
                        {{-- Top row: Blood group + urgency --}}
                        <div class="flex items-start gap-3 mb-4">
                            {{-- Blood Group Badge --}}
                            <div class="shrink-0 w-14 h-14 rounded-xl flex items-center justify-center font-black text-xl {{ $groupBg }}">
                                {{ $reqGroup }}
                            </div>

                            <div class="flex-1 min-w-0 pt-0.5">
                                {{-- Urgency badge --}}
                                <div class="mb-1.5">
                                    @if($isEmergency)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-black text-red-700 bg-red-50 border border-red-200 px-2 py-0.5 rounded-full uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse inline-block"></span>
                                            অতি জরুরি
                                        </span>
                                    @elseif($isUrgent)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-black text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block"></span>
                                            জরুরি
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-500 bg-slate-50 border border-slate-200 px-2 py-0.5 rounded-full">
                                            সাধারণ
                                        </span>
                                    @endif
                                </div>

                                {{-- Patient name --}}
                                <p class="text-sm font-black text-slate-900 leading-tight truncate">
                                    {{ $req->patient_name ?? 'অজ্ঞাত রোগী' }}
                                </p>
                                <p class="text-xs text-slate-500 font-medium truncate mt-0.5">
                                    🏥 {{ $req->hospital?->display_name ?? 'হাসপাতাল উল্লেখ নেই' }}
                                </p>
                            </div>
                        </div>

                        {{-- Time countdown --}}
                        <div class="mb-4 flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-50 border border-slate-100">
                            <svg class="w-3.5 h-3.5 shrink-0 {{ $isEmergency ? 'text-red-500' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            @if($neededAt && $diffHours !== null)
                                @if($diffHours < 0)
                                    <span class="text-xs font-bold text-red-600">{{ abs($diffHours) }} ঘণ্টা আগে ছিল</span>
                                @elseif($diffHours < 1)
                                    <span class="text-xs font-black text-red-600 animate-pulse">এখনই প্রয়োজন!</span>
                                @elseif($diffHours < 24)
                                    <span class="text-xs font-bold {{ $isEmergency ? 'text-red-600' : 'text-amber-600' }}">
                                        {{ $diffHours }} ঘণ্টার মধ্যে প্রয়োজন
                                    </span>
                                @else
                                    <span class="text-xs font-semibold text-slate-500">
                                        {{ $neededAt->format('d M, Y') }}-এর মধ্যে
                                    </span>
                                @endif
                            @else
                                <span class="text-xs font-semibold text-slate-500">যত দ্রুত সম্ভব</span>
                            @endif

                            {{-- Bags needed chip --}}
                            @if($req->bags_needed > 1)
                                <span class="ml-auto text-[10px] font-bold text-slate-500 shrink-0">
                                    {{ $req->bags_needed }} ব্যাগ
                                </span>
                            @endif
                        </div>

                        {{-- Action Button --}}
                        <a href="{{ route('requests.show', $req->id) }}"
                           class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-sm font-extrabold transition-all duration-200
                           {{ $isEmergency
                                ? 'bg-red-600 hover:bg-red-700 text-white shadow-sm shadow-red-100'
                                : ($isUrgent
                                    ? 'bg-amber-500 hover:bg-amber-600 text-white shadow-sm shadow-amber-100'
                                    : 'bg-slate-100 hover:bg-slate-200 text-slate-700') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            রক্ত দিতে চাই
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @elseif(auth()->user()->district_id)
    {{-- Radar active but no requests --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0 text-xl">✅</div>
        <div>
            <p class="font-black text-slate-800">এই মুহূর্তে জরুরি অনুরোধ নেই—আপনার এলাকা পর্যবেক্ষণে আছে।</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">রাডার সক্রিয় আছে — নতুন রিকোয়েস্ট আসলে এখানে দেখা যাবে।</p>
        </div>
    </div>
    @endif

    
    
    {{-- 6. Growth Loop (Referral / Invite) --}}
    {{-- ══════════════════════════════════════════
         🎁 Referral Banner — বন্ধুকে আমন্ত্রণ জানান
    ══════════════════════════════════════════ --}}
    @if($isDonor)
    @auth
    @php
        $gamification = app(\App\Services\GamificationService::class);
        $myCode       = $gamification->generateReferralCode(auth()->user());
        $referralLink = url('/register?ref=' . $myCode);
    @endphp
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 p-6 sm:p-8 shadow-xl">
        <div class="absolute -top-8 -right-8 w-40 h-40 bg-white/10 rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-10 right-24 w-28 h-28 bg-white/10 rounded-full pointer-events-none"></div>

        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            {{-- Left: Text --}}
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-3xl">👥</span>
                    <h2 class="text-white font-black text-lg sm:text-xl leading-tight">
                        বন্ধুকে আমন্ত্রণ জানান &amp; আয় করুন!
                    </h2>
                </div>
                <p class="text-emerald-100 text-sm font-medium max-w-sm">
                    বন্ধু সাইন-আপ করলে <span class="text-white font-black">+১০ পয়েন্ট</span>,
                    প্রথমবার রক্তদিলে আরও <span class="text-white font-black">+৩০ পয়েন্ট</span> পাবেন!
                </p>
                <div class="flex flex-wrap gap-2 mt-3">
                    <span class="inline-flex items-center gap-1 bg-white/20 border border-white/30 text-white text-xs font-extrabold px-3 py-1 rounded-full">
                        🎉 সাইন-আপ বোনাস: +১০ পয়েন্ট
                    </span>
                    <span class="inline-flex items-center gap-1 bg-white/20 border border-white/30 text-white text-xs font-extrabold px-3 py-1 rounded-full">
                        🩸 প্রথম ডোনেশন বোনাস: +৩০ পয়েন্ট
                    </span>
                </div>
            </div>

            {{-- Right: Code + Copy --}}
            <div class="flex-shrink-0 w-full sm:w-auto">
                <div class="bg-white/15 border border-white/25 backdrop-blur rounded-2xl p-4 text-center mb-3">
                    <div class="text-white/75 text-[10px] font-extrabold uppercase tracking-widest mb-1">আপনার রেফারেল কোড</div>
                    <div class="text-white font-black text-2xl tracking-[0.2em]">{{ $myCode }}</div>
                </div>
                <div class="flex items-center gap-2">
                    <input id="referral-link-dashboard"
                           type="text"
                           value="{{ $referralLink }}"
                           class="flex-1 text-xs py-2.5 px-3 rounded-xl bg-white/15 border border-white/25 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-white/50 min-w-0"
                           readonly>
                    <button onclick="copyDashboardReferral()" id="dash-copy-btn"
                            class="flex-shrink-0 bg-white text-emerald-700 font-black text-xs px-4 py-2.5 rounded-xl hover:bg-emerald-50 transition-colors shadow-sm whitespace-nowrap">
                        কপি করুন
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endauth
    @endif
</div>

<script>

function copyDashboardReferral() {
    const input = document.getElementById('referral-link-dashboard');
    const btn   = document.getElementById('dash-copy-btn');
    navigator.clipboard.writeText(input.value).then(() => {
        btn.textContent = '✓ কপি হয়েছে!';
        btn.classList.add('bg-emerald-100', 'text-emerald-800');
        setTimeout(() => {
            btn.textContent = 'কপি করুন';
            btn.classList.remove('bg-emerald-100', 'text-emerald-800');
        }, 2500);
    });
}

function copySmartCardLink() {
    const input = document.getElementById('smart-card-link');
    const btn   = document.getElementById('smart-card-copy-btn');
    if (!input || !btn) return;
    navigator.clipboard.writeText(input.value).then(() => {
        btn.textContent = '✓ কপি হয়েছে!';
        btn.classList.replace('bg-red-600', 'bg-emerald-600');
        btn.classList.replace('hover:bg-red-700', 'hover:bg-emerald-700');
        setTimeout(() => {
            btn.textContent = 'কপি লিঙ্ক';
            btn.classList.replace('bg-emerald-600', 'bg-red-600');
            btn.classList.replace('hover:bg-emerald-700', 'hover:bg-red-700');
        }, 2500);
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const modals = document.querySelectorAll('[data-renew-modal]');

    modals.forEach((form) => {
        const neededAtInput = form.querySelector('.renew-needed-at');
        const urgencySelect = form.querySelector('.renew-urgency');
        const note = form.querySelector('.renew-threshold-note');

        if (!neededAtInput || !urgencySelect || !note) {
            return;
        }

        const emergencyOption = urgencySelect.querySelector('option[value="emergency"]');
        const urgentOption = urgencySelect.querySelector('option[value="urgent"]');
        const normalOption = urgencySelect.querySelector('option[value="normal"]');

        const updateUrgencyAvailability = () => {
            const raw = neededAtInput.value;

            if (!raw) {
                if (emergencyOption) emergencyOption.disabled = false;
                if (urgentOption) urgentOption.disabled = false;
                note.classList.add('hidden');
                note.textContent = '';
                return;
            }

            const selectedDate = new Date(raw);
            if (Number.isNaN(selectedDate.getTime())) {
                return;
            }

            const now = new Date();
            const emergencyLimit = new Date(now.getTime() + (24 * 60 * 60 * 1000));
            const urgentLimit = new Date(now.getTime() + (72 * 60 * 60 * 1000));

            const disableEmergency = selectedDate > emergencyLimit;
            const disableUrgent = selectedDate > urgentLimit;

            if (emergencyOption) emergencyOption.disabled = disableEmergency;
            if (urgentOption) urgentOption.disabled = disableUrgent;

            if (urgencySelect.value === 'emergency' && disableEmergency) {
                urgencySelect.value = normalOption ? 'normal' : '';
            }

            if (urgencySelect.value === 'urgent' && disableUrgent) {
                urgencySelect.value = normalOption ? 'normal' : '';
            }

            if (disableUrgent) {
                note.textContent = 'নির্বাচিত সময় ৭২ ঘণ্টার বেশি — Emergency ও Urgent অপশন নিষ্ক্রিয়।';
                note.classList.remove('hidden');
                return;
            }

            if (disableEmergency) {
                note.textContent = 'নির্বাচিত সময় ২৪ ঘণ্টার বেশি — Emergency অপশন নিষ্ক্রিয়।';
                note.classList.remove('hidden');
                return;
            }

            note.classList.add('hidden');
            note.textContent = '';
        };

        neededAtInput.addEventListener('change', updateUrgencyAvailability);
        neededAtInput.addEventListener('input', updateUrgencyAvailability);
        updateUrgencyAvailability();
    });
});
</script>
@endsection
