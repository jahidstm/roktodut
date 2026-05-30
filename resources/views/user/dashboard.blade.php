@extends('layouts.user-dashboard')

@section('title', 'ইউজার ড্যাশবোর্ড — রক্তদূত')

@section('content')
<div data-panel-id="overview">

{{-- 🚀 Welcome Back Prompt --}}
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
            <h2 class="text-center text-2xl font-black text-slate-900">ফিরে আসায় স্বাগতম!</h2>
            <p class="mt-2 text-center text-slate-600 font-medium leading-relaxed">
                আজ কি কারো জীবন বাঁচাতে রক্তের অনুরোধ করবেন?
            </p>
            <div class="mt-7 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <a href="{{ route('requests.create') }}"
                   class="inline-flex items-center justify-center rounded-xl bg-red-600 px-5 py-3 text-sm font-black text-white shadow-sm transition hover:bg-red-700">
                    নতুন রিকোয়েস্ট করুন
                </a>
                <button type="button" @click="open = false"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                    পরে করব
                </button>
            </div>
        </div>
    </div>
@endif

{{-- 🩸 Recipient Donation Confirmation Pop-up --}}
@if(isset($pendingClaim))
    <div class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-900/80 backdrop-blur-md">
        <div class="bg-white rounded-[2rem] p-8 max-w-lg w-full mx-4 shadow-2xl border border-red-100 animate-pop-in">
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
            <p class="text-[10px] text-slate-400 text-center mt-6 font-bold uppercase tracking-tight">
                আপনার একটি কনফার্মেশন ডোনারকে উৎসাহিত করবে
            </p>
        </div>
    </div>
@endif

<div class="flex flex-col gap-8">

    {{-- ── A) Hero Profile Card (Recipient) ── --}}
    @php $user = auth()->user(); @endphp
    <div class="rounded-[2rem] overflow-hidden bg-slate-900 shadow-xl p-6 sm:p-8 relative scroll-reveal" data-scroll-reveal>
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 sm:gap-8">
            {{-- Profile --}}
            <div class="flex items-center gap-4 sm:gap-5">
                <div class="relative">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-gradient-to-br from-slate-600 to-slate-800 flex items-center justify-center border-4 border-slate-700 shadow-xl">
                        @if($user->profile_image)
                            <img src="{{ asset('storage/'.$user->profile_image) }}" class="w-full h-full object-cover rounded-full">
                        @else
                            <span class="text-2xl sm:text-3xl font-black text-white">{{ mb_substr($user->name, 0, 1) }}</span>
                        @endif
                    </div>
                    @if($user->nid_status === 'verified')
                    <div class="absolute bottom-0 right-0 w-5 h-5 sm:w-6 sm:h-6 bg-blue-500 rounded-full border-2 border-slate-900 flex items-center justify-center text-white">
                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    @endif
                </div>
                <div>
                    <p class="text-slate-400 text-xs sm:text-sm font-semibold mb-0.5">স্বাগতম ফিরে আসার জন্য,</p>
                    <h2 class="text-xl sm:text-2xl font-black text-white leading-tight">{{ $user->name }}</h2>
                    <p class="text-slate-400 text-xs sm:text-sm font-medium mt-1 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-slate-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                        <span class="truncate">{{ $user->upazila?->name ?? 'উপজেলা দেওয়া নেই' }}, {{ $user->district?->name ?? 'জেলা দেওয়া নেই' }}</span>
                    </p>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <a href="{{ route('search') }}" class="w-full sm:w-auto text-center inline-flex items-center justify-center bg-white/10 hover:bg-white/20 text-white px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition">
                    🔍 ডোনার খুঁজুন
                </a>
                <a href="{{ route('requests.create') }}" class="w-full sm:w-auto text-center inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition shadow-lg">
                    নতুন রিকোয়েস্ট
                </a>
            </div>
        </div>

        {{-- Stats Row --}}
        <div class="relative z-10 grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mt-6 sm:mt-8">
            <div class="bg-white/5 rounded-2xl p-4 sm:p-5 border border-white/10 flex flex-col justify-center">
                <p class="text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-1">মোট রিকোয়েস্ট</p>
                <p class="text-2xl sm:text-3xl font-black text-white">{{ $totalRequestsMade ?? 0 }}</p>
            </div>
            <div class="bg-white/5 rounded-2xl p-4 sm:p-5 border border-white/10 flex flex-col justify-center">
                <p class="text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-1">সফল রিকোয়েস্ট</p>
                <p class="text-2xl sm:text-3xl font-black text-emerald-400">{{ $fulfilledRequests ?? 0 }}</p>
            </div>
            <div class="bg-white/5 rounded-2xl p-4 sm:p-5 border border-white/10 flex flex-col justify-center">
                <p class="text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-1">এনআইডি স্ট্যাটাস</p>
                <p class="text-sm sm:text-base font-black {{ $user->nid_status === 'verified' ? 'text-emerald-400' : 'text-amber-400' }}">
                    {{ $user->nid_status === 'verified' ? 'যাচাইকৃত' : 'যাচাইকৃত নয়' }}
                </p>
            </div>
            <div class="bg-white/5 rounded-2xl p-4 sm:p-5 border border-white/10 flex flex-col justify-center">
                <p class="text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-1">সফলতার হার</p>
                <p class="text-2xl sm:text-3xl font-black text-blue-400">
                    {{ ($successRate !== 'তথ্য নেই') ? $successRate.'%' : '—' }}
                </p>
            </div>
        </div>

        {{-- NID badge --}}
        <div class="relative z-10 flex flex-wrap gap-2 mt-4 sm:mt-5">
            <div class="inline-flex items-center gap-1.5 text-[10px] sm:text-xs font-bold bg-white/5 border border-white/10 text-slate-300 rounded-full px-2.5 py-1 sm:px-3 sm:py-1.5">
                <span>🛡️</span>
                <span class="truncate">{{ $user->nid_status === 'verified' ? 'এনআইডি যাচাইকৃত' : 'এনআইডি যাচাইকৃত নয়' }}</span>
            </div>
        </div>
    </div>

    {{-- ── NID Upload/Review Banner ── --}}
    @if($user->organization_id && $user->nid_status === 'pending' && empty($user->nid_path))
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 shadow-sm scroll-reveal" data-scroll-reveal>
            <div class="flex flex-col md:flex-row md:items-start gap-4">
                <div class="shrink-0 text-amber-600 bg-amber-100 p-3 rounded-full hidden md:block">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-black text-amber-900">ভেরিফিকেশন প্রয়োজন!</h3>
                    <p class="text-sm text-amber-800 font-medium mt-1">
                        আপনি একটি ব্লাড ক্লাবের সদস্য। ভেরিফাইড হতে আপনার NID, জন্মনিবন্ধন বা স্টুডেন্ট আইডি আপলোড করুন।
                    </p>
                    <form action="{{ route('donor.upload_nid') }}" method="POST" enctype="multipart/form-data" class="mt-4 flex flex-col sm:flex-row items-center gap-3">
                        @csrf
                        <input type="file" name="nid_document" accept=".jpg,.jpeg,.png,.pdf" class="w-full sm:w-auto text-sm text-amber-900 bg-white border border-amber-200 rounded-xl file:mr-4 file:py-2.5 file:px-4 file:border-0 file:text-sm file:font-bold file:bg-amber-600 file:text-white hover:file:bg-amber-700 cursor-pointer" required>
                        <button type="submit" class="w-full sm:w-auto bg-amber-700 hover:bg-amber-800 text-white px-8 py-2.5 rounded-xl font-bold transition-all shadow-sm">আপলোড করুন</button>
                    </form>
                    @error('nid_document') <p class="text-red-600 text-xs mt-2 font-bold">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    @elseif($user->organization_id && $user->nid_status === 'pending' && !empty($user->nid_path))
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 shadow-sm flex items-center gap-4 scroll-reveal" data-scroll-reveal>
            <div class="shrink-0 text-blue-600 bg-blue-100 p-3 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h3 class="text-base font-black text-blue-900">ডকুমেন্ট রিভিউ হচ্ছে...</h3>
                <p class="text-sm text-blue-700 font-medium mt-0.5">আপনার পরিচয়পত্র অর্গানাইজেশনের কাছে পাঠানো হয়েছে।</p>
            </div>
        </div>
    @endif

    {{-- ── B) Become a Donor Banner ── --}}
    <div x-data="{ upgradeModalOpen: {{ $errors->any() ? 'true' : 'false' }} }"
         class="bg-gradient-to-r from-red-50 to-red-100 p-6 rounded-3xl border border-red-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6 scroll-reveal" data-scroll-reveal>
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-white text-red-600 shadow-sm">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-extrabold text-slate-900">রক্তদাতা হিসেবে যুক্ত হতে চান?</h3>
                <p class="text-sm font-semibold text-slate-600 mt-1">আপনার প্রোফাইল আপগ্রেড করুন এবং মানুষের জীবন বাঁচাতে অবদান রাখুন।</p>
            </div>
        </div>
        <button @click="upgradeModalOpen = true" class="w-full md:w-auto bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-extrabold shadow-md transition-all text-center whitespace-nowrap">
            রক্তদাতা হোন (Become a Donor)
        </button>

        {{-- Upgrade Modal --}}
        <template x-teleport="body">
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
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">লোকেশন <span class="text-red-500">*</span></label>
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
                            <div class="pt-4 flex flex-col sm:flex-row sm:justify-end border-t border-slate-100 mt-6 gap-3">
                                <button type="submit" class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3 rounded-xl font-black shadow-md transition-all">
                                    আপগ্রেড নিশ্চিত করুন
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- ── C) Quick Action Cards ── --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        {{-- জরুরি রক্ত --}}
        <a href="{{ route('requests.create') }}" class="group relative overflow-hidden p-6 rounded-3xl bg-gradient-to-br from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 transition-all shadow-lg shadow-red-100 hover:-translate-y-1 flex flex-col gap-3 min-h-[150px] scroll-reveal" data-scroll-reveal>
            <div class="absolute -right-4 -top-4 w-28 h-28 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
            <div class="w-11 h-11 bg-white/20 rounded-2xl flex items-center justify-center text-white border border-white/30 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div>
                <h3 class="text-white font-black text-lg leading-tight">জরুরি রক্তের দরকার?</h3>
                <p class="text-red-100 text-sm font-medium mt-1 leading-snug">নতুন রিকোয়েস্ট তৈরি করুন এবং ডোনারদের সাথে যোগাযোগ করুন।</p>
            </div>
        </a>

        {{-- রক্তের ফিড --}}
        <a href="{{ route('requests.index') }}" class="group relative overflow-hidden p-6 rounded-3xl bg-white hover:shadow-lg transition-all shadow-sm border border-slate-200 hover:border-emerald-300 hover:-translate-y-1 flex flex-col gap-3 min-h-[150px] scroll-reveal" data-scroll-reveal>
            <div class="absolute -right-4 -bottom-4 w-28 h-28 bg-emerald-50 rounded-full blur-xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col gap-3">
                <div class="w-11 h-11 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 border border-emerald-100 shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-slate-900 font-black text-lg leading-tight">রক্তের ফিড দেখুন</h3>
                    <p class="text-slate-500 text-sm font-medium mt-1 leading-snug">আপনার এলাকার সাম্প্রতিক রক্তের রিকোয়েস্টগুলো দেখুন।</p>
                </div>
            </div>
        </a>

        {{-- আমার রিকোয়েস্ট --}}
        <a href="{{ route('requests.my-requests') }}" class="group relative overflow-hidden p-6 rounded-3xl bg-white hover:shadow-lg transition-all shadow-sm border border-slate-200 hover:border-blue-300 hover:-translate-y-1 flex flex-col gap-3 min-h-[150px] scroll-reveal" data-scroll-reveal>
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
    </div>

    {{-- ── D) Impact Stats ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 scroll-reveal" data-scroll-reveal>
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
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="text-2xl font-black text-emerald-600">{{ $fulfilledRequests ?? 0 }}</div>
                <div class="text-xs font-bold text-emerald-500 mt-0.5">সফল রিকোয়েস্ট</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-red-100 p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </div>
            <div>
                <div class="text-2xl font-black text-red-600">{{ $totalContributions ?? 0 }}</div>
                <div class="text-xs font-bold text-red-500 mt-0.5">আপনার অবদান</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-blue-100 p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div>
                <div class="text-2xl font-black text-blue-600">{{ $successRate !== 'তথ্য নেই' ? $successRate.'%' : '—' }}</div>
                <div class="text-xs font-bold text-blue-500 mt-0.5">সফলতার হার</div>
            </div>
        </div>
    </div>

    {{-- ── E) Recent Requests Table ── --}}
    @if(isset($recentRequests) && $recentRequests->count() > 0)
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden scroll-reveal" data-scroll-reveal>
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">আপনার সাম্প্রতিক রিকোয়েস্টসমূহ</h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">সর্বশেষ ৫টি রিকোয়েস্টের আপডেট</p>
                </div>
            </div>
            <a href="{{ route('requests.my-requests') }}" class="text-xs font-bold text-red-600 hover:text-red-700 flex items-center gap-1 hover:underline underline-offset-2">সব দেখুন →</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider font-bold">
                        <th class="px-6 py-4 border-b border-slate-100">রোগীর নাম ও গ্রুপ</th>
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
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 uppercase">সম্পন্ন</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 uppercase">{{ $req->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div x-data="{ renewOpen: false }" class="inline-flex items-center gap-2">
                                    @if($canRenew)
                                        <button type="button" @click="renewOpen = true"
                                                class="whitespace-nowrap inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-xs font-extrabold bg-red-600 text-white hover:bg-red-700 transition">
                                            রিনিউ করুন
                                        </button>
                                    @endif
                                    <a href="{{ route('requests.show', $req->id) }}" class="whitespace-nowrap inline-flex items-center justify-center px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-extrabold text-slate-700 hover:bg-slate-100 hover:text-red-600 transition">
                                        ডিটেইলস
                                    </a>
                                    @if($canRenew)
                                        <div x-show="renewOpen" style="display:none;"
                                             class="fixed inset-0 z-[120] flex items-center justify-center bg-slate-900/55 backdrop-blur-sm p-4"
                                             x-transition.opacity>
                                            <div @click.away="renewOpen = false" class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl border border-slate-200 text-left">
                                                <div class="mb-4">
                                                    <h3 class="text-lg font-black text-slate-900">রিকোয়েস্ট রিনিউ করুন</h3>
                                                    <p class="mt-1 text-xs font-semibold text-slate-500">নতুন সময় ও জরুরিতা সেট করলে রিকোয়েস্ট আবার ফিডে যাবে।</p>
                                                </div>
                                                <form method="POST" action="{{ route('requests.renew', $req->id) }}" class="space-y-4" data-renew-modal>
                                                    @csrf
                                                    <div>
                                                        <label class="block text-xs font-bold text-slate-600 mb-1.5">কবে রক্ত লাগবে</label>
                                                        <input type="datetime-local" name="needed_at" value="{{ old('needed_at') }}"
                                                               class="renew-needed-at w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500" required>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-bold text-slate-600 mb-1.5">জরুরিতা</label>
                                                        <select name="urgency" class="renew-urgency w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500" required>
                                                            @foreach(\App\Enums\UrgencyLevel::cases() as $case)
                                                                <option value="{{ $case->value }}" @selected(old('urgency', $req->urgency?->value ?? (string) $req->urgency) === $case->value)>
                                                                    {{ $case->label() }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <p class="renew-threshold-note mt-1 text-xs font-semibold text-amber-700 hidden"></p>
                                                    </div>
                                                    <div class="pt-2 flex items-center justify-end gap-2">
                                                        <button type="button" @click="renewOpen = false"
                                                                class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">বাতিল</button>
                                                        <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-xs font-black text-white hover:bg-red-700">রিনিউ সাবমিট</button>
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
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500 font-medium">আপনি এখনো কোনো রক্তের রিকোয়েস্ট করেননি।</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modals = document.querySelectorAll('[data-renew-modal]');
    modals.forEach((form) => {
        const neededAtInput = form.querySelector('.renew-needed-at');
        const urgencySelect = form.querySelector('.renew-urgency');
        const note = form.querySelector('.renew-threshold-note');
        if (!neededAtInput || !urgencySelect || !note) return;

        const emergencyOption = urgencySelect.querySelector('option[value="emergency"]');
        const urgentOption = urgencySelect.querySelector('option[value="urgent"]');
        const normalOption = urgencySelect.querySelector('option[value="normal"]');

        const updateUrgencyAvailability = () => {
            const raw = neededAtInput.value;
            if (!raw) {
                if (emergencyOption) emergencyOption.disabled = false;
                if (urgentOption) urgentOption.disabled = false;
                note.classList.add('hidden');
                return;
            }
            const selectedDate = new Date(raw);
            if (Number.isNaN(selectedDate.getTime())) return;
            const now = new Date();
            const emergencyLimit = new Date(now.getTime() + (24 * 60 * 60 * 1000));
            const urgentLimit = new Date(now.getTime() + (72 * 60 * 60 * 1000));
            const disableEmergency = selectedDate > emergencyLimit;
            const disableUrgent = selectedDate > urgentLimit;
            if (emergencyOption) emergencyOption.disabled = disableEmergency;
            if (urgentOption) urgentOption.disabled = disableUrgent;
            if (urgencySelect.value === 'emergency' && disableEmergency) urgencySelect.value = normalOption ? 'normal' : '';
            if (urgencySelect.value === 'urgent' && disableUrgent) urgencySelect.value = normalOption ? 'normal' : '';
            if (disableUrgent) { note.textContent = 'নির্বাচিত সময় ৭২ ঘণ্টার বেশি — Emergency ও Urgent নিষ্ক্রিয়।'; note.classList.remove('hidden'); return; }
            if (disableEmergency) { note.textContent = 'নির্বাচিত সময় ২৪ ঘণ্টার বেশি — Emergency নিষ্ক্রিয়।'; note.classList.remove('hidden'); return; }
            note.classList.add('hidden');
        };
        neededAtInput.addEventListener('change', updateUrgencyAvailability);
        neededAtInput.addEventListener('input', updateUrgencyAvailability);
        updateUrgencyAvailability();
    });
});
</script>
</div>
@endsection
