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
    
    {{-- A) ডোনার পরিচিতি কার্ড (Identity Zone) --}}
    @php $user = auth()->user(); @endphp
    <x-donor-identity-header :user="$user" :total-contributions="$totalContributions ?? 0" />
    
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

    <div class="bg-white p-6 rounded-3xl border {{ $isEligible ? 'border-emerald-200 shadow-emerald-50' : 'border-amber-200 shadow-amber-50' }} shadow-lg flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full {{ $isEligible ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' }}">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-extrabold {{ $isEligible ? 'text-emerald-700' : 'text-amber-700' }}">
                    {{ $isEligible ? 'আপনি রক্তদানের জন্য যোগ্য (Eligible)' : 'আপনি আপাতত রক্তদানের জন্য যোগ্য নন' }}
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
                <input type="date" name="last_donated_at" value="{{ $user->last_donated_at?->format('Y-m-d') }}" max="{{ date('Y-m-d') }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-700">
            </div>
            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2.5 rounded-lg font-extrabold shadow-sm transition-colors">
                সেভ
            </button>
        </form>
    </div>


    {{-- 2. Core Action (CTA Row) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="{{ route('requests.create') }}" class="group p-8 rounded-3xl bg-red-600 hover:bg-red-700 transition shadow-lg shadow-red-200">
            <div class="text-white font-black text-xl mb-2">জরুরি রক্তের দরকার?</div>
            <p class="text-red-100 text-sm font-medium">সহজেই নতুন রিকোয়েস্ট তৈরি করুন এবং ডোনারদের সাথে যোগাযোগ করুন।</p>
        </a>

        <a href="{{ route('requests.index') }}" class="group p-8 rounded-3xl bg-white border-2 border-slate-200 hover:border-red-500 transition shadow-sm">
            <div class="text-slate-900 font-black text-xl mb-2">রক্ত দিতে চান?</div>
            <p class="text-slate-500 text-sm font-medium">আপনার এরিয়ার সাম্প্রতিক রিকোয়েস্টগুলো দেখুন এবং সাড়া দিন।</p>
        </a>
    </div>


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
                @endphp

                <div class="relative overflow-hidden rounded-2xl border transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl
                    {{ $isEmergency ? 'border-red-800/60 shadow-lg shadow-red-900/20' : ($isUrgent ? 'border-amber-700/40 shadow-sm' : 'border-slate-700/30') }}"
                     style="background: {{ $isEmergency ? 'linear-gradient(135deg,#1c0808 0%,#2d1010 60%,#1e1a1a 100%)' : ($isUrgent ? 'linear-gradient(135deg,#1c1400 0%,#2d2010 60%,#1a1a1e 100%)' : 'linear-gradient(135deg,#0f172a 0%,#1e293b 100%)') }};">

                    {{-- Pulse bar at top for emergency --}}
                    @if($isEmergency)
                        <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-transparent via-red-500 to-transparent animate-pulse"></div>
                    @endif

                    {{-- My blood group match indicator --}}
                    @if($isMyGroup)
                        <div class="absolute top-3 right-3 z-10">
                            <span class="inline-flex items-center gap-1 text-[10px] font-black text-emerald-300 px-2 py-0.5 rounded-full"
                                  style="background:rgba(16,185,129,.15); border:1px solid rgba(16,185,129,.3);">
                                ✓ আপনার গ্রুপ
                            </span>
                        </div>
                    @endif

                    <div class="p-5">
                        {{-- Top row: Blood group + urgency --}}
                        <div class="flex items-start gap-3 mb-4">
                            {{-- Blood Group Badge --}}
                            <div class="shrink-0 w-14 h-14 rounded-xl flex items-center justify-center font-black text-xl shadow-lg
                                {{ $isEmergency ? 'bg-red-600/30 text-red-200 ring-1 ring-red-500/40' : ($isUrgent ? 'bg-amber-600/25 text-amber-200 ring-1 ring-amber-500/30' : 'bg-slate-600/30 text-slate-200 ring-1 ring-slate-500/20') }}">
                                {{ $reqGroup }}
                            </div>

                            <div class="flex-1 min-w-0 pt-0.5">
                                {{-- Urgency badge --}}
                                <div class="mb-1.5">
                                    @if($isEmergency)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-black text-red-300 px-2 py-0.5 rounded-full uppercase tracking-wider"
                                              style="background:rgba(220,38,38,.25); border:1px solid rgba(220,38,38,.4);">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-400 animate-pulse inline-block"></span>
                                            অতি জরুরি
                                        </span>
                                    @elseif($isUrgent)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-black text-amber-300 px-2 py-0.5 rounded-full uppercase tracking-wider"
                                              style="background:rgba(217,119,6,.2); border:1px solid rgba(217,119,6,.35);">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 inline-block"></span>
                                            জরুরি
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-400 px-2 py-0.5 rounded-full"
                                              style="background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.1);">
                                            সাধারণ
                                        </span>
                                    @endif
                                </div>

                                {{-- Patient name --}}
                                <p class="text-sm font-black text-white leading-tight truncate">
                                    {{ $req->patient_name ?? 'অজ্ঞাত রোগী' }}
                                </p>
                                <p class="text-xs text-slate-400 font-medium truncate mt-0.5">
                                    🏥 {{ $req->hospital ?? 'হাসপাতাল উল্লেখ নেই' }}
                                </p>
                            </div>
                        </div>

                        {{-- Time countdown --}}
                        <div class="mb-4 flex items-center gap-2 px-3 py-2 rounded-xl"
                             style="background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.07);">
                            <svg class="w-3.5 h-3.5 shrink-0 {{ $isEmergency ? 'text-red-400' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            @if($neededAt && $diffHours !== null)
                                @if($diffHours < 0)
                                    <span class="text-xs font-bold text-red-400">{{ abs($diffHours) }} ঘণ্টা আগে ছিল</span>
                                @elseif($diffHours < 1)
                                    <span class="text-xs font-black text-red-300 animate-pulse">এখনই প্রয়োজন!</span>
                                @elseif($diffHours < 24)
                                    <span class="text-xs font-bold {{ $isEmergency ? 'text-red-300' : 'text-amber-300' }}">
                                        {{ $diffHours }} ঘণ্টার মধ্যে প্রয়োজন
                                    </span>
                                @else
                                    <span class="text-xs font-semibold text-slate-400">
                                        {{ $neededAt->format('d M, Y') }}-এর মধ্যে
                                    </span>
                                @endif
                            @else
                                <span class="text-xs font-semibold text-slate-500">যত দ্রুত সম্ভব</span>
                            @endif

                            {{-- Bags needed chip --}}
                            @if($req->bags_needed > 1)
                                <span class="ml-auto text-[10px] font-bold text-slate-400 shrink-0">
                                    {{ $req->bags_needed }} ব্যাগ
                                </span>
                            @endif
                        </div>

                        {{-- Action Button --}}
                        <a href="{{ route('requests.show', $req->id) }}"
                           class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-sm font-extrabold transition-all duration-200
                           {{ $isEmergency
                                ? 'bg-red-600 hover:bg-red-500 text-white shadow-md shadow-red-900/50'
                                : ($isUrgent
                                    ? 'bg-amber-600 hover:bg-amber-500 text-white shadow-md shadow-amber-900/30'
                                    : 'text-slate-200 hover:text-white hover:bg-white/10') }}"
                           style="{{ (!$isEmergency && !$isUrgent) ? 'border:1px solid rgba(255,255,255,.12);' : '' }}">
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

    
    {{-- E) My Commitments (Ongoing) --}}
    @if(isset($ongoingCommitments) && $ongoingCommitments->count() > 0)
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-blue-50/50">
            <div>
                <h2 class="text-lg font-extrabold text-blue-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    আমার চলমান কমিটমেন্ট
                </h2>
                <p class="text-sm text-blue-700/70 font-medium mt-1">যে রিকোয়েস্টগুলোতে আপনি রক্ত দেওয়ার প্রতিশ্রুতি দিয়েছেন</p>
            </div>
            <a href="{{ route('requests.index') }}" class="text-xs font-bold text-blue-600 hover:underline">সব দেখুন</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <tbody class="divide-y divide-slate-100 text-sm">
                    @foreach($ongoingCommitments as $commitment)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-extrabold text-slate-900">রোগী: {{ $commitment->bloodRequest->patient_name ?? 'N/A' }}</div>
                                <div class="text-xs font-bold text-slate-500 mt-0.5">গ্রুপ: <span class="text-blue-600">{{ $commitment->bloodRequest->blood_group?->value ?? $commitment->bloodRequest->blood_group }}</span></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-700">{{ $commitment->bloodRequest->hospital ?? 'N/A' }}</div>
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

    {{-- 4. User Impact (Stats Grid) --}}
    {{-- ══════════════════════════════════════════
         📊 Stats Grid
    ══════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-card>
            <div class="text-slate-500 text-sm font-bold uppercase tracking-wider">মোট রিকোয়েস্ট</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-black text-slate-900">{{ $totalRequestsMade ?? 0 }}</span>
                <span class="text-slate-400 font-bold text-sm">টি</span>
            </div>
        </x-card>

        <x-card>
            <div class="text-emerald-600 text-sm font-bold uppercase tracking-wider">আপনার অবদান</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-black text-emerald-600">{{ $totalContributions ?? 0 }}</span>
                <span class="text-emerald-400 font-bold text-sm">বার</span>
            </div>
        </x-card>

        <x-card>
            <div class="text-red-600 text-sm font-bold uppercase tracking-wider">সফল রিকোয়েস্ট</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-black text-red-600">{{ $fulfilledRequests ?? 0 }}</span>
                <span class="text-red-400 font-bold text-sm">টি</span>
            </div>
        </x-card>

        <x-card>
            <div class="text-blue-600 text-sm font-bold uppercase tracking-wider">সফলতার হার</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-black text-blue-600">{{ $successRate ?? 0 }}</span>
                @if($successRate !== 'N/A')<span class="text-blue-400 font-bold text-sm">%</span>@endif
            </div>
        </x-card>
    </div>

    {{-- 7. Verified Donation History --}}
    <div>
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="text-lg font-extrabold text-slate-900">ভেরিফাইড রক্তদান হিস্ট্রি</h3>
                <p class="text-sm text-slate-500 font-medium mt-1">আপনার অতীতের সফল রক্তদানের লগ</p>
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
                                        <div class="font-extrabold text-slate-900">{{ $history->fulfilled_at ? $history->fulfilled_at->format('d M, Y') : 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-700">{{ $history->bloodRequest->hospital ?? 'N/A' }}</div>
                                        <div class="text-xs text-slate-500 font-medium">{{ $history->bloodRequest->district?->name ?? 'N/A' }}, {{ $history->bloodRequest->upazila?->name ?? '' }}</div>
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

    @if(isset($recentRequests))
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">আপনার সাম্প্রতিক রিকোয়েস্টসমূহ</h2>
                <p class="text-sm text-slate-500 font-medium mt-1">
                    {{ ($isDonor ?? false) ? 'আপনি যেসব রিকোয়েস্টে সাড়া দিয়েছেন (Accepted)' : 'সর্বশেষ ৫টি রিকোয়েস্টের আপডেট' }}
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider font-bold">
                        <th class="px-6 py-4 border-b border-slate-100">{{ ($isDonor ?? false) ? 'রিকোয়েস্ট ও গ্রুপ' : 'রোগীর নাম ও গ্রুপ' }}</th>
                        <th class="px-6 py-4 border-b border-slate-100">দরকার</th>
                        <th class="px-6 py-4 border-b border-slate-100">সাড়া (Accepted)</th>
                        <th class="px-6 py-4 border-b border-slate-100">স্ট্যাটাস</th>
                        <th class="px-6 py-4 border-b border-slate-100 text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($recentRequests as $req)
                        @php
                            $isOwner = ((int) ($req->requested_by ?? 0) === (int) auth()->id());
                            $isExpiredStatus = strtolower((string) $req->status) === 'expired';
                            $isPastNeededAt = $req->needed_at && $req->needed_at->lt(now());
                            $canRenew = $isOwner && ($isExpiredStatus || $isPastNeededAt);
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-extrabold text-slate-900">{{ $req->patient_name ?? 'রোগী' }}</div>
                                <div class="text-xs font-bold text-red-600 mt-0.5">{{ $req->blood_group?->value ?? (string) $req->blood_group }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-700">{{ $req->needed_at?->format('d M, Y') ?? 'ASAP' }}</div>
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
                                        Fulfilled
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
                                {{ ($isDonor ?? false) ? 'আপনি এখনো কোনো রিকোয়েস্টে Accepted সাড়া দেননি।' : 'আপনি এখনো কোনো রক্তের রিকোয়েস্ট করেননি।' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════
         🏆 গ্যামিফিকেশন উইজেট
    ══════════════════════════════════════════ --}}
    @if(isset($gamificationStats))
    @php extract($gamificationStats); @endphp
    <div class="rounded-3xl overflow-hidden border border-slate-100 shadow-lg bg-white">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-red-600 to-red-800 px-6 py-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-white/15 flex items-center justify-center text-xl">🏆</div>
                <div>
                    <h2 class="text-white font-black text-base leading-tight">আপনার গ্যামিফিকেশন স্ট্যাটাস</h2>
                    <p class="text-red-200 text-xs font-semibold">পয়েন্ট উপার্জন করুন, ব্যাজ জিতুন!</p>
                </div>
            </div>
            {{-- ডানপাশের বাটন গ্রুপ --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('gamification.guide') }}"
                   class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white/80 hover:text-white bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl transition-all backdrop-blur-sm">
                    🪙 গাইড
                </a>
                <a href="{{ route('leaderboard') }}"
                   class="inline-flex items-center gap-1.5 text-xs font-bold text-white/80 hover:text-white bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl px-3 py-2 transition-all">
                    লিডারবোর্ড
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        <div class="p-6">
            {{-- Stats Row --}}
            <div class="grid grid-cols-3 gap-4 mb-6">
                {{-- Points --}}
                <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4 text-center">
                    <div class="text-2xl font-black text-amber-600">{{ number_format($currentPoints) }}</div>
                    <div class="text-xs font-bold text-amber-500 mt-1">মোট পয়েন্ট</div>
                </div>
                {{-- Donations --}}
                <div class="bg-red-50 border border-red-100 rounded-2xl p-4 text-center">
                    <div class="text-2xl font-black text-red-600">{{ $totalDonations }}</div>
                    <div class="text-xs font-bold text-red-500 mt-1">রক্তদান</div>
                </div>
                {{-- Rank --}}
                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 text-center">
                    <div class="text-2xl font-black text-blue-600">#{{ $myRank }}</div>
                    <div class="text-xs font-bold text-blue-500 mt-1">র‍্যাঙ্ক</div>
                </div>
            </div>

            {{-- Earned Badges --}}
            @if(auth()->user()->badges->count() > 0)
            <div class="mb-6">
                <div class="text-xs font-black text-slate-500 uppercase tracking-wider mb-3">অর্জিত ব্যাজসমূহ</div>
                <div class="flex flex-wrap gap-2">
                    @foreach(auth()->user()->badges as $badge)
                        @php $bd = \App\Services\GamificationService::getBadgeDisplayData($badge->name); @endphp
                        <div class="inline-flex items-center gap-1.5 text-xs font-bold {{ $bd['color'] }} border rounded-full px-3 py-1.5 shadow-sm {{ $bd['glow'] }}">
                            <span class="text-base">{{ $bd['emoji'] }}</span>
                            <span>{{ $bd['bn'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Next Milestone Progress Bar --}}
            @if($nextMilestone)
            <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-sm font-black text-slate-700">
                        {{ $nextMilestone['emoji'] }} পরবর্তী লক্ষ্য: <span class="text-red-600">{{ $nextMilestone['bn'] }}</span>
                    </div>
                    <div class="text-xs font-bold text-slate-500">
                        {{ $totalDonations }}/{{ $nextMilestone['donations'] }} রক্তদান
                    </div>
                </div>
                {{-- Progress Bar --}}
                <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                    <div class="h-3 rounded-full bg-gradient-to-r from-red-500 to-red-600 transition-all duration-700 relative"
                         style="width: {{ $progressPercent }}%">
                        <div class="absolute inset-0 bg-white/20 animate-pulse rounded-full"></div>
                    </div>
                </div>
                <div class="flex justify-between text-[10px] font-bold text-slate-400 mt-1.5">
                    <span>{{ $progressPercent }}% সম্পন্ন</span>
                    <span>আর {{ $nextMilestone['donations'] - $totalDonations }} টি ডোনেশন বাকি</span>
                </div>
            </div>
            @else
            <div class="bg-purple-50 border border-purple-200 rounded-2xl p-4 text-center">
                <div class="text-2xl mb-1">✨</div>
                <div class="font-black text-purple-800">অভিনন্দন! আপনি সর্বোচ্চ Platinum Hero!</div>
                <div class="text-xs text-purple-600 font-semibold mt-1">আপনি রক্তদূতের সর্বোচ্চ পদে আছেন।</div>
            </div>
            @endif
        </div>
    </div>
    @endif

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
                        <p class="text-sm font-extrabold text-slate-800">First Responder বোনাস</p>
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
                        <p class="text-xs text-slate-500 font-medium truncate">Bronze → Silver → Gold → Platinum</p>
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

    {{-- 6. Growth Loop (Referral / Invite) --}}
    {{-- ══════════════════════════════════════════
         🎁 Referral Banner — বন্ধুকে আমন্ত্রণ জানান
    ══════════════════════════════════════════ --}}
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
                        🎉 সাইন-আপ বোনাস: +১০ pts
                    </span>
                    <span class="inline-flex items-center gap-1 bg-white/20 border border-white/30 text-white text-xs font-extrabold px-3 py-1 rounded-full">
                        🩸 প্রথম ডোনেশন বোনাস: +৩০ pts
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
