@extends('layouts.app')

@section('title', 'ইউজার ড্যাশবোর্ড — রক্তদূত')

@section('content')

{{-- 🚀 Welcome Back Smart Prompt (The Re-engagement Loop) --}}
@if(auth()->user()->is_onboarded && !auth()->user()->welcome_back_checked)
    <div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-3xl p-8 max-w-lg w-full mx-4 shadow-2xl animate-fade-in-up">
            <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h2 class="text-2xl font-black text-slate-900 text-center">অনেক দিন পর দেখা!</h2>
            <p class="text-slate-500 text-center font-medium mt-2 mb-6">দীর্ঘদিন পর রক্তদূতে আপনাকে আবার স্বাগতম। আপনি কি বর্তমানে জরুরি প্রয়োজনে রক্তদানে প্রস্তুত আছেন?</p>
            
            <form action="{{ route('welcome_back.update') }}" method="POST">
                @csrf
                <label class="flex items-center gap-3 p-4 border-2 border-slate-100 rounded-xl cursor-pointer hover:border-blue-500 transition-colors bg-slate-50">
                    <input type="checkbox" name="is_available" value="1" class="w-5 h-5 text-blue-600 rounded border-slate-300 focus:ring-blue-500" {{ auth()->user()->is_available ? 'checked' : '' }}>
                    <div>
                        <p class="font-bold text-slate-800">হ্যাঁ, আমি রক্তদানে প্রস্তুত</p>
                        <p class="text-xs text-slate-500 font-medium">আপনার প্রোফাইল ডোনার সার্চে দৃশ্যমান হবে</p>
                    </div>
                </label>
                
                <button type="submit" class="w-full mt-6 bg-slate-900 hover:bg-slate-800 text-white font-extrabold py-3.5 rounded-xl shadow-sm transition">
                    স্ট্যাটাস আপডেট করুন
                </button>
            </form>
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
                        <a href="{{ asset('storage/' . $pendingClaim->proof_image_path) }}" target="_blank" class="text-sm font-bold text-blue-600 hover:underline">
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

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    {{-- 🎯 THE FIX: হেডারের স্পেসিং ঠিক করা হয়েছে --}}
    <div class="mb-8">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
                স্বাগতম, {{ auth()->user()->name }}!
                
                @if(auth()->user()->verified_badge)
                    <div class="group relative flex items-center justify-center cursor-help">
                        <svg class="w-7 h-7 text-blue-500 drop-shadow-sm" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-max px-2.5 py-1 bg-slate-800 text-white text-[10px] font-bold rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none shadow-lg">
                            ভেরিফাইড ডোনার
                        </span>
                    </div>
                @endif
            </h1>
        </div>
        <p class="text-slate-500 font-medium mt-1">আপনার রক্তদান এবং রিকোয়েস্টের বিস্তারিত ড্যাশবোর্ড।</p>
    </div>

    {{-- 🚀 NID Upload Prompt for Organization Members --}}
    @php $user = auth()->user(); @endphp
    @if($user->organization_id && $user->nid_status === 'pending' && empty($user->nid_path))
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 mb-8 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-start gap-4">
                <div class="shrink-0 text-amber-600 bg-amber-100 p-3 rounded-full hidden md:block">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-black text-amber-900">ভেরিফিকেশন প্রয়োজন!</h3>
                    <p class="text-sm text-amber-800 font-medium mt-1">
                        আপনি একটি ব্লাড ক্লাবের সদস্য হিসেবে যুক্ত হতে চেয়েছেন। ভেরিফাইড ডোনার (ব্লু-ব্যাজ) হতে আপনার NID, জন্মনিবন্ধন বা স্টুডেন্ট আইডি কার্ডের ছবি আপলোড করুন।
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
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 mb-8 shadow-sm flex items-center gap-4">
            <div class="shrink-0 text-blue-600 bg-blue-100 p-3 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h3 class="text-base font-black text-blue-900">ডকুমেন্ট রিভিউ হচ্ছে...</h3>
                <p class="text-sm text-blue-700 font-medium mt-0.5">আপনার পরিচয়পত্র অর্গানাইজেশনের কাছে পাঠানো হয়েছে। তারা যাচাই করলে আপনার প্রোফাইলে ব্লু-ব্যাজ যুক্ত হবে।</p>
            </div>
        </div>
    @endif

    @php
        $isEligible = $user->is_eligible_to_donate;
        $nextDate = $user->next_eligible_date;
    @endphp

    <div class="mb-10 bg-white p-6 rounded-3xl border {{ $isEligible ? 'border-emerald-200 shadow-emerald-50' : 'border-amber-200 shadow-amber-50' }} shadow-lg flex flex-col md:flex-row items-center justify-between gap-6">
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

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-slate-500 text-sm font-bold uppercase tracking-wider">মোট রিকোয়েস্ট</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-black text-slate-900">{{ $totalRequestsMade ?? 0 }}</span>
                <span class="text-slate-400 font-bold text-sm">টি</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-emerald-600 text-sm font-bold uppercase tracking-wider">আপনার অবদান</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-black text-emerald-600">{{ $totalContributions ?? 0 }}</span>
                <span class="text-emerald-400 font-bold text-sm">বার</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-red-600 text-sm font-bold uppercase tracking-wider">সফল রিকোয়েস্ট</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-black text-red-600">{{ $fulfilledRequests ?? 0 }}</span>
                <span class="text-red-400 font-bold text-sm">টি</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-blue-600 text-sm font-bold uppercase tracking-wider">সফলতার হার</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-black text-blue-600">{{ $successRate ?? 0 }}</span>
                <span class="text-blue-400 font-bold text-sm">%</span>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         🪪 Digital Smart Card — QR Verified Identity
    ══════════════════════════════════════════ --}}
    @if($user->qr_token && $user->nid_status === 'verified')
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
                        <h2 class="text-white font-black text-base leading-tight">Digital Smart Card</h2>
                        <p class="text-slate-500 text-xs font-semibold mt-0.5">QR কোড স্ক্যান করে পরিচয় যাচাই করুন</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-emerald-400 text-[11px] font-bold rounded-full shrink-0"
                      style="background:rgba(74,222,128,.1); border:1px solid rgba(74,222,128,.25);">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    NID Verified
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
                            🩸 {{ $user->blood_group?->value ?? 'N/A' }}
                        </span>

                        {{-- Availability Status --}}
                        @if($isEligible && $user->is_available)
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-2 rounded-xl text-emerald-300"
                                  style="background:rgba(22,163,74,.12); border:1.5px solid rgba(22,163,74,.25);">
                                <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                Available
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-2 rounded-xl text-amber-300"
                                  style="background:rgba(217,119,6,.1); border:1.5px solid rgba(217,119,6,.25);">
                                <span class="inline-block w-2 h-2 rounded-full bg-amber-400"></span>
                                In Cooldown
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
                                <span class="text-slate-500 font-semibold text-xs ml-0.5">pts</span>
                            </p>
                        </div>
                    </div>

                    {{-- Shareable Link --}}
                    <div class="flex items-center gap-2">
                        <input id="smart-card-link"
                               type="text"
                               value="{{ route('public.verify', $user->qr_token) }}"
                               class="flex-1 text-xs py-2.5 px-3 rounded-xl text-slate-400 font-mono focus:outline-none focus:ring-1 focus:ring-red-600/40 min-w-0 truncate"
                               style="background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1);"
                               readonly>
                        <button onclick="copySmartCardLink()" id="smart-card-copy-btn"
                                class="shrink-0 bg-red-600 hover:bg-red-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-colors whitespace-nowrap">
                            কপি লিঙ্ক
                        </button>
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
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <script>
        (function () {
            var verifyUrl = @json(route('public.verify', $user->qr_token));
            var canvas = document.createElement('canvas');
            QRCode.toCanvas(canvas, verifyUrl, {
                width: 148,
                margin: 1,
                color: { dark: '#1e293b', light: '#ffffff' }
            }, function () {
                var container = document.getElementById('smart-card-qr');
                if (container) container.appendChild(canvas);
            });
        })();
    </script>
    @endif

    {{-- ══════════════════════════════════════════
         🏆 গ্যামিফিকেশন উইজেট
    ══════════════════════════════════════════ --}}
    @if(isset($gamificationStats))
    @php extract($gamificationStats); @endphp
    <div class="mb-10 rounded-3xl overflow-hidden border border-slate-100 shadow-lg bg-white">

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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        <a href="{{ route('requests.create') }}" class="group p-8 rounded-3xl bg-red-600 hover:bg-red-700 transition shadow-lg shadow-red-200">
            <div class="text-white font-black text-xl mb-2">জরুরি রক্তের দরকার?</div>
            <p class="text-red-100 text-sm font-medium">সহজেই নতুন রিকোয়েস্ট তৈরি করুন এবং ডোনারদের সাথে যোগাযোগ করুন।</p>
        </a>

        <a href="{{ route('requests.index') }}" class="group p-8 rounded-3xl bg-white border-2 border-slate-200 hover:border-red-500 transition shadow-sm">
            <div class="text-slate-900 font-black text-xl mb-2">রক্ত দিতে চান?</div>
            <p class="text-slate-500 text-sm font-medium">আপনার এরিয়ার সাম্প্রতিক রিকোয়েস্টগুলো দেখুন এবং সাড়া দিন।</p>
        </a>
    </div>

    {{-- ══════════════════════════════════════════
         🎁 Referral Banner — বন্ধুকে আমন্ত্রণ জানান
    ══════════════════════════════════════════ --}}
    @auth
    @php
        $gamification = app(\App\Services\GamificationService::class);
        $myCode       = $gamification->generateReferralCode(auth()->user());
        $referralLink = url('/register?ref=' . $myCode);
    @endphp
    <div class="mb-8 relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 p-6 sm:p-8 shadow-xl">
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

    @if(isset($recentRequests))
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">আপনার সাম্প্রতিক রিকোয়েস্টসমূহ</h2>
                <p class="text-sm text-slate-500 font-medium mt-1">সর্বশেষ ৫টি রিকোয়েস্টের আপডেট</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider font-bold">
                        <th class="px-6 py-4 border-b border-slate-100">রোগীর নাম ও গ্রুপ</th>
                        <th class="px-6 py-4 border-b border-slate-100">দরকার</th>
                        <th class="px-6 py-4 border-b border-slate-100">সাড়া (Accepted)</th>
                        <th class="px-6 py-4 border-b border-slate-100">স্ট্যাটাস</th>
                        <th class="px-6 py-4 border-b border-slate-100 text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($recentRequests as $req)
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
                                <a href="{{ route('requests.show', $req->id) }}" class="inline-flex items-center justify-center px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-extrabold text-slate-700 hover:bg-slate-100 hover:text-red-600 transition">
                                    ডিটেইলস
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500 font-medium">আপনি এখনো কোনো রক্তের রিকোয়েস্ট করেননি।</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- 🎯 ডোনারের ড্যাশবোর্ডে অ্যাকসেপ্ট করা রিকোয়েস্টের লিস্ট --}}
    @if(isset($acceptedDonations) && $acceptedDonations->count() > 0)
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden mt-10">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-red-50/50">
            <div>
                <h2 class="text-lg font-extrabold text-red-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    আপনি যেসব রিকোয়েস্ট অ্যাকসেপ্ট করেছেন
                </h2>
                <p class="text-sm text-red-700/70 font-medium mt-1">রক্তদানের পর এখান থেকে প্রমাণ জমা দিন বা পিন দিন</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <tbody class="divide-y divide-slate-100 text-sm">
                    @foreach($acceptedDonations as $donation)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-extrabold text-slate-900">রোগী: {{ $donation->bloodRequest->patient_name ?? 'N/A' }}</div>
                                <div class="text-xs font-bold text-slate-500 mt-0.5">গ্রুপ: <span class="text-red-600">{{ $donation->bloodRequest->blood_group?->value ?? $donation->bloodRequest->blood_group }}</span></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-700">{{ $donation->bloodRequest->needed_at?->format('d M, Y') ?? 'ASAP' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($donation->verification_status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800">অপেক্ষমাণ (Pending)</span>
                                @elseif($donation->verification_status === 'claimed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-blue-100 text-blue-800">রিভিউ হচ্ছে (Claimed)</span>
                                @elseif($donation->verification_status === 'verified')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800">ভেরিফাইড (Verified)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($donation->verification_status === 'pending')
                                    <a href="{{ route('requests.show', $donation->blood_request_id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-slate-900 text-white rounded-lg text-xs font-extrabold shadow-sm hover:bg-slate-800 transition">
                                        প্রমাণ জমা দিন (Claim)
                                    </a>
                                @else
                                    <a href="{{ route('requests.show', $donation->blood_request_id) }}" class="inline-flex items-center justify-center px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-extrabold text-slate-700 hover:bg-slate-100 transition">
                                        ভিউ রিকোয়েস্ট
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════
         🪙 পয়েন্ট ও ব্যাজ সিস্টেম — Quick Guide Teaser
         ব্যবহারকারী কীভাবে পয়েন্ট আয় করতে পারে সেটা
         সংক্ষেপে দেখানো হচ্ছে, পূর্ণ গাইডে লিঙ্ক সহ।
    ══════════════════════════════════════════ --}}
    <div class="mt-10 rounded-3xl overflow-hidden border border-slate-200 shadow-sm bg-white">

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
                        <p class="text-xs text-slate-500 font-medium truncate">বন্ধু সাইন-আপ + প্রথম ডোনেশনে</p>
                    </div>
                    <span class="text-sm font-black text-emerald-600 shrink-0">+৪০</span>
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
</script>
@endsection