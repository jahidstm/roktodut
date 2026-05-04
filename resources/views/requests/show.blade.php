@extends('layouts.app')

@section('title', 'রিকোয়েস্ট ডিটেইলস — রক্তদূত')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 font-bold">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700 font-bold">
            {{ session('error') }}
        </div>
    @endif

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">রিকোয়েস্ট ডিটেইলস</h1>
            <p class="text-slate-500 font-medium mt-1">Accepted ডোনার লিস্ট এবং স্ট্যাটাস</p>
        </div>
        
        <a href="{{ route('requests.index') }}" class="shrink-0 bg-white border-2 border-slate-200 hover:border-slate-300 text-slate-700 font-extrabold py-2.5 px-5 rounded-xl shadow-sm transition-colors flex items-center gap-2">
            ফিডে ফিরে যান
        </a>
    </div>

    {{-- রিকোয়েস্ট কার্ড --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <div class="text-lg font-extrabold truncate">{{ $bloodRequest->patient_name ?? 'রোগী' }}</div>
                <div class="text-sm text-slate-500 font-medium truncate mt-1">{{ $bloodRequest->hospital?->display_name ?? 'হাসপাতাল উল্লেখ নেই' }}</div>
                <div class="text-sm text-slate-500 font-semibold mt-2">
                    লোকেশন: <span class="text-slate-800 font-extrabold">{{ $bloodRequest->upazila?->name ?? '-' }}, {{ $bloodRequest->district?->name ?? '-' }}</span>
                </div>
            </div>

            <div class="shrink-0 flex flex-col items-end gap-2">
                <div class="px-3 py-1 rounded-lg bg-red-50 text-red-700 border border-red-100 font-extrabold">
                    {{ $bloodRequest->blood_group?->value ?? (string) $bloodRequest->blood_group }}
                </div>
                <button type="button"
                        x-data
                        @click="$dispatch('open-modal', 'report-blood-request')"
                        class="inline-flex items-center rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-black text-red-700 hover:bg-red-50">
                    Report
                </button>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3 text-sm">
            <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
                <div class="text-xs text-slate-500 font-semibold">দরকার</div>
                <div class="font-extrabold text-slate-800 mt-1">{{ $bloodRequest->needed_at?->format('d M, Y h:i A') ?? 'ASAP' }}</div>
            </div>
            <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
                <div class="text-xs text-slate-500 font-semibold">ব্যাগ</div>
                <div class="font-extrabold text-slate-800 mt-1">{{ $bloodRequest->bags_needed ?? '-' }}</div>
            </div>
            <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
                <div class="text-xs text-slate-500 font-semibold">যোগাযোগ</div>
                <div class="font-extrabold text-slate-800 mt-1">
                    @if($bloodRequest->is_phone_hidden)
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-purple-700 bg-purple-50 border border-purple-200 rounded-full px-2 py-0.5">
                            🛡️ সুরক্ষিত মোড — ডোনাররা Telegram-এ পাঠাবে
                        </span>
                    @else
                        <a href="tel:{{ $bloodRequest->contact_number }}" class="text-red-600 hover:text-red-700 hover:underline">
                            {{ $bloodRequest->contact_number }}
                        </a>
                    @endif
                </div>
            </div>
            <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
                <div class="text-xs text-slate-500 font-semibold">স্ট্যাটাস</div>
                <div class="font-extrabold text-slate-800 mt-1 uppercase">{{ $bloodRequest->status }}</div>
            </div>
        </div>

        @if($bloodRequest->notes)
            <div class="mt-4">
                <div class="text-xs text-slate-500 font-semibold">নোট</div>
                <div class="mt-1 font-semibold text-slate-800 whitespace-pre-line">{{ $bloodRequest->notes }}</div>
            </div>
        @endif
    </div>

    <x-modal name="report-blood-request" maxWidth="lg">
        <form method="POST" action="{{ route('reports.store') }}" class="p-6">
            @csrf
            <input type="hidden" name="reportable_type" value="blood_request">
            <input type="hidden" name="reportable_id" value="{{ $bloodRequest->id }}">

            <h3 class="text-lg font-extrabold text-slate-900">রিকোয়েস্ট রিপোর্ট করুন</h3>
            <p class="mt-1 text-sm font-medium text-slate-500">ভুল তথ্য, স্প্যাম বা অপব্যবহার হলে রিপোর্ট পাঠান।</p>

            <div class="mt-4">
                <label for="report-category" class="block text-sm font-bold text-slate-700 mb-2">কারণ</label>
                <select id="report-category" name="category" required class="w-full rounded-xl border-slate-300 text-sm font-semibold">
                    <option value="">কারণ নির্বাচন করুন</option>
                    <option value="fake_info">Fake info</option>
                    <option value="harassment">Harassment</option>
                    <option value="spam">Spam</option>
                    <option value="inappropriate">Inappropriate</option>
                    <option value="other">Other</option>
                </select>
                @error('category')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="mt-4">
                <label for="report-message" class="block text-sm font-bold text-slate-700 mb-2">বিস্তারিত (ঐচ্ছিক)</label>
                <textarea id="report-message" name="message" rows="4" class="w-full rounded-xl border-slate-300 text-sm">{{ old('message') }}</textarea>
                @error('message')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            @error('reportable_type')<p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
            @error('reportable_id')<p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror

            <div class="mt-6 flex items-center justify-end gap-2">
                <button type="button"
                        x-data
                        @click="$dispatch('close-modal', 'report-blood-request')"
                        class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50">
                    Cancel
                </button>
                <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-black text-white hover:bg-red-700">
                    Submit report
                </button>
            </div>
        </form>
    </x-modal>

    {{-- 🎯 Action Box: Accept, Decline, or Fulfill Buttons --}}
    @php
        $myResponse = auth()->check() ? $bloodRequest->responses->where('user_id', auth()->id())->first() : null;
        $isOwner = auth()->check() && ((int) $bloodRequest->requested_by === (int) auth()->id());
        $currentStatus = strtolower((string) $bloodRequest->status);
        $isExpiredStatus = $currentStatus === 'expired';
        $isPendingLikeStatus = in_array($currentStatus, ['pending', 'in_progress'], true);
        $isPastNeededAt = $bloodRequest->needed_at && \Carbon\Carbon::parse($bloodRequest->needed_at)->isPast();
        $canRenew = $isOwner && ($isExpiredStatus || ($isPastNeededAt && $isPendingLikeStatus));
    @endphp

    <div x-data="{ renewOpen: {{ ($errors->has('needed_at') || $errors->has('urgency')) ? 'true' : 'false' }} }" class="mt-6 mb-6 p-6 bg-white border border-slate-200 rounded-2xl shadow-sm flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
        <div>
            <h3 class="text-base font-black text-slate-800">আপনার সিদ্ধান্ত (Action)</h3>
            <p class="text-sm text-slate-500 font-medium">এই রিকোয়েস্টের জন্য আপনার সাড়া দিন</p>
        </div>
        
        <div class="flex flex-wrap gap-3">
            @if($canRenew)
                <button type="button"
                        @click="renewOpen = true"
                        class="px-6 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black shadow-sm transition">
                    রিনিউ করুন
                </button>
            @endif

            @if (Route::has('requests.fulfill') && $isOwner && strtolower($bloodRequest->status) !== 'fulfilled')
                <form method="POST" action="{{ route('requests.fulfill', $bloodRequest) }}">
                    @csrf
                    <button class="px-6 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black shadow-sm transition">
                        Mark as Fulfilled
                    </button>
                </form>
            @endif

            @if (Route::has('requests.respond') && !$isOwner && strtolower($bloodRequest->status) !== 'fulfilled')
                @if (!$myResponse)
                    @if(auth()->check() && auth()->user()->is_eligible_to_donate)

                        @if($bloodRequest->is_phone_hidden)
                        {{-- 🚨 UNIDIRECTIONAL PUSH: ক্লিক করলেই রোগীর Telegram-এ পিং যাবে --}}
                        <form method="POST" action="{{ route('requests.respond', $bloodRequest) }}">
                            @csrf
                            <input type="hidden" name="status" value="pending" />
                            <button class="px-8 py-3 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-black shadow-sm transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                রক্ত দিতে চাই (রোগীকে পিং করুন)
                            </button>
                        </form>
                        <p class="text-xs text-purple-700 font-semibold bg-purple-50 border border-purple-100 rounded-lg px-3 py-2">
                            🛡️ ক্লিক করলে আপনার নম্বর সরাসরি রোগীর Telegram-এ পৌঁছাবে — কোনো অপেক্ষা নেই।
                        </p>

                        @else
                        <form method="POST" action="{{ route('requests.respond', $bloodRequest) }}">
                            @csrf
                            <input type="hidden" name="status" value="pending" />
                            <button class="px-8 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black shadow-sm transition">
                                রক্ত দিতে চাই (Send Request)
                            </button>
                        </form>
                        @endif

                    @else
                        <button disabled title="আপনি রক্তদানের যোগ্য নন" class="px-8 py-3 rounded-xl bg-slate-200 text-slate-400 font-black cursor-not-allowed border border-slate-300">
                            রক্ত দিতে চাই
                        </button>
                    @endif

                    @if(!$bloodRequest->is_phone_hidden)
                    <form method="POST" action="{{ route('requests.respond', $bloodRequest) }}">
                        @csrf
                        <input type="hidden" name="status" value="declined" />
                        <button class="px-8 py-3 rounded-xl border-2 border-slate-200 bg-white hover:bg-slate-50 text-slate-800 font-black transition">
                            Decline
                        </button>
                    </form>
                    @endif

                @elseif ($myResponse->status === 'contacted')
                    <div class="flex items-center gap-4">
                        <span class="px-6 py-3 rounded-xl bg-purple-50 text-purple-700 border border-purple-200 font-black flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            পিং পাঠানো হয়েছে — রোগী Telegram-এ আপনার নম্বর পেয়েছেন
                        </span>
                    </div>

                @elseif ($myResponse->status === 'pending')
                    <div class="flex items-center gap-4">
                        <span class="px-6 py-3 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 font-black flex items-center gap-2">
                            <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            রোগীর অনুমোদনের অপেক্ষায়...
                        </span>
                        
                        <form method="POST" action="{{ route('requests.respond', $bloodRequest) }}">
                            @csrf
                            <input type="hidden" name="status" value="withdrawn" />
                            <button class="text-sm text-slate-500 hover:text-red-600 font-bold underline transition">
                                ক্যান্সেল করুন
                            </button>
                        </form>
                    </div>
                @elseif ($myResponse->status === 'accepted')
                    <div class="flex items-center gap-4">
                        <span class="px-6 py-3 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 font-black flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            রোগী আপনার রিকোয়েস্ট অ্যাকসেপ্ট করেছেন
                        </span>
                    </div>
                @elseif ($myResponse->status === 'rejected')
                    <div class="flex items-center gap-4">
                        <span class="px-6 py-3 rounded-xl bg-red-50 text-red-700 border border-red-200 font-black flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            রোগী রিকোয়েস্টটি ডিক্লাইন করেছেন
                        </span>
                    </div>
                @elseif ($myResponse->status === 'declined' || $myResponse->status === 'withdrawn')
                    <div class="flex items-center gap-4">
                        <span class="px-6 py-3 rounded-xl bg-slate-100 text-slate-700 border border-slate-200 font-black">
                            আপনি ক্যান্সেল করেছেন
                        </span>

                        @if(auth()->check() && auth()->user()->is_eligible_to_donate)
                            <form method="POST" action="{{ route('requests.respond', $bloodRequest) }}">
                                @csrf
                                <input type="hidden" name="status" value="pending" />
                                <button class="text-sm text-emerald-600 hover:text-emerald-700 font-bold underline transition">
                                    আবার রিকোয়েস্ট পাঠান
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            @endif
            
            @if(strtolower($bloodRequest->status) === 'fulfilled')
                <div class="px-8 py-3 rounded-xl bg-emerald-100 text-emerald-800 font-black border border-emerald-200">
                    এই রিকোয়েস্টটি সম্পন্ন হয়েছে (Fulfilled)
                </div>
            @endif
        </div>

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

                    <form method="POST" action="{{ route('requests.renew', $bloodRequest->id) }}" class="space-y-4" data-renew-modal>
                        @csrf

                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">কবে রক্ত লাগবে</label>
                            <input type="datetime-local"
                                   name="needed_at"
                                   value="{{ old('needed_at', optional($bloodRequest->needed_at)->format('Y-m-d\TH:i')) }}"
                                   class="renew-needed-at w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500" required>
                            @error('needed_at')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">জরুরিতা</label>
                            <select name="urgency" class="renew-urgency w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500" required>
                                @foreach (\App\Enums\UrgencyLevel::cases() as $case)
                                    <option value="{{ $case->value }}" @selected(old('urgency', $bloodRequest->urgency?->value ?? (string) $bloodRequest->urgency) === $case->value)>
                                        {{ $case->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('urgency')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                            <p class="renew-threshold-note mt-1 text-xs font-semibold text-amber-700 hidden"></p>
                        </div>

                        <div class="pt-1 flex items-center justify-end gap-2">
                            <button type="button" @click="renewOpen = false" class="px-4 py-2 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm font-bold hover:bg-slate-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-black">
                                রিনিউ সাবমিট
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>

    {{-- 🎯 ডোনারের জন্য Donation Claim Action Box --}}
    @if($myResponse && strtolower($myResponse->status) === 'accepted' && $myResponse->verification_status === 'pending')
        <div x-data="{ claimMethod: 'pin', fileName: null }" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mt-6 mb-6">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                <h3 class="text-lg font-extrabold text-slate-800">রক্তদান কনফার্ম করুন</h3>
                <p class="text-sm font-medium text-slate-500">পয়েন্ট এবং ব্যাজ পেতে আপনার রক্তদান ভেরিফাই করুন。</p>
            </div>

            <form action="{{ route('donations.claim', $myResponse->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                
                {{-- Method Selector (Tabs) --}}
                <div class="flex p-1 bg-slate-100 rounded-xl mb-6">
                    <button type="button" @click="claimMethod = 'pin'" :class="{'bg-white shadow-sm text-red-600': claimMethod === 'pin', 'text-slate-500 hover:text-slate-700': claimMethod !== 'pin'}" class="flex-1 py-2.5 text-sm font-bold rounded-lg transition-all">
                        পিন কোড (ইনস্ট্যান্ট)
                    </button>
                    <button type="button" @click="claimMethod = 'image'" :class="{'bg-white shadow-sm text-red-600': claimMethod === 'image', 'text-slate-500 hover:text-slate-700': claimMethod !== 'image'}" class="flex-1 py-2.5 text-sm font-bold rounded-lg transition-all">
                        ছবি আপলোড (রিভিউ)
                    </button>
                </div>

                <input type="hidden" name="claim_method" x-model="claimMethod">

                {{-- PIN Input Block --}}
                <div x-show="claimMethod === 'pin'" x-transition.opacity class="space-y-4">
                    <label class="block text-sm font-bold text-slate-700">রোগীর লোকের কাছ থেকে পাওয়া ৪-ডিজিটের পিনটি দিন:</label>
                    <input type="text" name="pin" placeholder="যেমন: 8492" class="w-full text-center tracking-[0.5em] text-2xl font-black rounded-xl border-slate-300 focus:ring-red-500 focus:border-red-500 py-4 placeholder:text-slate-300">
                    <p class="text-xs font-semibold text-slate-500 text-center">পিন মেলা মাত্রই আপনার প্রোফাইলে পয়েন্ট যুক্ত হবে।</p>
                    @error('pin') <p class="text-red-600 text-xs font-bold text-center mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Image Upload Block --}}
                <div x-show="claimMethod === 'image'" x-transition.opacity style="display: none;" class="space-y-4">
                    <label class="block text-sm font-bold text-slate-700">প্রমাণ আপলোড করুন:</label>
                    <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center hover:bg-slate-50 transition cursor-pointer relative" :class="fileName ? 'bg-red-50 border-red-300' : ''">
                        <input type="file" name="proof_image" accept="image/*,application/pdf" @change="fileName = $event.target.files[0].name" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        
                        <svg class="mx-auto h-10 w-10 text-slate-400 mb-3" :class="fileName ? 'text-red-500' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        
                        <p class="text-sm font-bold text-slate-600" x-text="fileName ? fileName : 'হাসপাতালের বেড স্লিপ বা ব্লাড ব্যাগের ছবি সিলেক্ট করুন'"></p>
                        <p class="text-xs mt-1" :class="fileName ? 'text-red-600 font-bold' : 'text-slate-400'" x-text="fileName ? 'ফাইল সিলেক্ট করা হয়েছে। এখন কনফার্ম করুন।' : 'JPG, PNG, PDF (সর্বোচ্চ ২ MB)'"></p>
                    </div>
                    @error('proof_image') <p class="text-red-600 text-xs font-bold text-center mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="w-full mt-6 bg-red-600 hover:bg-red-700 text-white font-black py-3.5 rounded-xl shadow-sm transition">
                    কনফার্ম করুন
                </button>
            </form>
        </div>
    @elseif($myResponse && strtolower($myResponse->status) === 'accepted' && $myResponse->verification_status !== 'pending')
        <div class="mt-6 mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-center gap-3">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-emerald-800 font-bold">
                @if($myResponse->verification_status === 'verified')
                    আপনার রক্তদান সফলভাবে ভেরিফাইড হয়েছে!
                @elseif($myResponse->verification_status === 'claimed')
                    আপনার ক্লেইম রিভিউতে আছে। অনুগ্রহ করে অপেক্ষা করুন।
                @elseif($myResponse->verification_status === 'disputed')
                    আপনার ক্লেইমটি ডিসপুট করা হয়েছে। অ্যাডমিন রিভিউ করবে।
                @else
                    আপনার ক্লেইমটি বাতিল করা হয়েছে।
                @endif
            </span>
        </div>
    @endif

    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
        {{-- রেসপন্স সামারি --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="text-lg font-extrabold text-slate-800">রেসপন্স সারাংশ</div>
            <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                <div class="bg-blue-50 border border-blue-100 p-3 rounded-xl">
                    <span class="text-blue-600 font-bold block mb-1">আগ্রহী ডোনার</span>
                    <span class="text-2xl font-black text-blue-800">{{ $pendingCount ?? 0 }}</span>
                </div>
                <div class="bg-emerald-50 border border-emerald-100 p-3 rounded-xl">
                    <span class="text-emerald-600 font-bold block mb-1">অ্যাকসেপ্টেড</span>
                    <span class="text-2xl font-black text-emerald-800">{{ $acceptedCount ?? 0 }}</span>
                </div>
            </div>
        </div>

        {{-- ডোনার লিস্ট ও ম্যানেজমেন্ট --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="text-lg font-extrabold text-slate-800 mb-4">ডোনার তালিকা</div>

            @if(empty($canViewAcceptedDonors) || !$canViewAcceptedDonors)
                <div class="text-sm text-slate-500 font-medium p-4 bg-slate-50 rounded-xl border border-slate-100 text-center">
                    🔒 প্রাইভেসির কারণে আপনি ডোনারদের তালিকা দেখতে পারবেন না। শুধুমাত্র রোগীর নিজস্ব ড্যাশবোর্ডে এটি দৃশ্যমান।
                </div>
            @else
                {{-- Pending Donors Section (Only for requester) --}}
                @if(isset($pendingResponses) && $pendingResponses->isNotEmpty())
                    <h4 class="text-xs font-black text-blue-600 uppercase tracking-wider mb-3">আগ্রহী ডোনার (অনুমোদনের অপেক্ষায়)</h4>
                    <div class="space-y-3 mb-6">
                        @foreach($pendingResponses as $resp)
                            <div class="rounded-xl border border-blue-100 bg-blue-50/50 p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                <div>
                                    <div class="font-extrabold text-slate-900">{{ $resp->user?->name ?? 'Unknown' }}</div>
                                    <div class="text-xs text-slate-500 font-medium mt-0.5">রক্ত দিতে ইচ্ছুক</div>
                                </div>
                                <div class="flex items-center gap-2 w-full sm:w-auto">
                                    <form method="POST" action="{{ route('requests.responses.update', $resp->id) }}" class="flex-1 sm:flex-none">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="accepted">
                                        <button class="w-full sm:w-auto px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-sm transition">
                                            অ্যাকসেপ্ট করুন
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('requests.responses.update', $resp->id) }}" class="flex-1 sm:flex-none">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button class="w-full sm:w-auto px-4 py-2 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold text-sm transition">
                                            ডিক্লাইন
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Accepted Donors Section --}}
                <h4 class="text-xs font-black text-emerald-600 uppercase tracking-wider mb-3">আপনার অনুমোদিত ডোনার</h4>
                @if(empty($acceptedResponses) || $acceptedResponses->isEmpty())
                    <div class="text-sm text-slate-500 font-medium p-4 bg-slate-50 rounded-xl border border-slate-100 text-center">
                        এখনো কোনো ডোনারকে অ্যাকসেপ্ট করা হয়নি।
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($acceptedResponses as $resp)
                            <div class="rounded-xl border border-emerald-100 bg-emerald-50/30 p-4">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                                    <div class="min-w-0">
                                        <div class="font-extrabold text-slate-900 truncate">
                                            {{ $resp->user?->name ?? 'Unknown' }}
                                        </div>
                                        <div class="text-xs text-emerald-600 font-bold mt-1 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            অনুমোদিত
                                        </div>
                                    </div>
                                    <button type="button"
                                            class="reveal-btn w-full sm:w-auto shrink-0 px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-900 text-white font-extrabold text-sm transition-colors shadow-sm"
                                            data-url="{{ route('requests.donors.reveal_phone', ['bloodRequest' => $bloodRequest->id, 'donor' => $resp->user_id]) }}"
                                            data-target="phone-{{ $resp->user_id }}">
                                        নম্বর দেখুন (Reveal)
                                    </button>
                                </div>

                                <div class="text-sm font-semibold text-slate-700 mb-3 p-3 bg-white border border-emerald-100 rounded-lg flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    <span id="phone-{{ $resp->user_id }}" class="font-black text-slate-900 tracking-wider">Hidden</span>
                                </div>

                                {{-- 🎯 রোগীর লোকের জন্য PIN Display Box --}}
                                @if(auth()->id() === $bloodRequest->requested_by)
                                <div class="p-3 bg-red-50 rounded-lg border border-red-100 flex items-center justify-between shadow-sm">
                                    <div>
                                        <p class="text-[10px] font-bold text-red-500 uppercase tracking-wider mb-0.5">ভেরিফিকেশন পিন (রক্তদানের পর ডোনারকে দিন)</p>
                                        <p class="text-2xl font-black text-red-600 tracking-widest">{{ $resp->verification_pin ?? '----' }}</p>
                                    </div>
                                    <div class="text-right">
                                        @if($resp->verification_status === 'verified')
                                            <span class="bg-emerald-100 text-emerald-700 text-[10px] font-black px-2 py-1 rounded uppercase tracking-wide">Verified</span>
                                        @elseif($resp->verification_status === 'claimed')
                                            <span class="bg-amber-100 text-amber-700 text-[10px] font-black px-2 py-1 rounded uppercase tracking-wide">Reviewing</span>
                                        @else
                                            <span class="bg-slate-200 text-slate-500 text-[10px] font-black px-2 py-1 rounded uppercase tracking-wide">Pending</span>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

{{-- Reveal Phone Number Script --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
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

        neededAtInput.addEventListener('input', updateUrgencyAvailability);
        urgencySelect.addEventListener('change', updateUrgencyAvailability);
        updateUrgencyAvailability();
    });

    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.reveal-btn');
        if (!btn) return;

        const url = btn.getAttribute('data-url');
        const targetId = btn.getAttribute('data-target');
        const targetEl = document.getElementById(targetId);

        if (!url || !targetEl) return;

        btn.disabled = true;
        const originalText = btn.textContent;
        btn.textContent = 'Revealing...';
        btn.classList.add('opacity-75', 'cursor-not-allowed');

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const data = await res.json();

            if (!res.ok) {
                throw new Error(data.message || 'নাম্বার দেখতে সমস্যা হচ্ছে। আবার চেষ্টা করুন।');
            }
            
            targetEl.textContent = data.phone ?? 'N/A';
            btn.textContent = 'Revealed';
            btn.classList.replace('bg-red-600', 'bg-slate-400');
            btn.classList.replace('hover:bg-red-700', 'hover:bg-slate-500');
            btn.classList.remove('opacity-75', 'cursor-not-allowed');
            
            btn.classList.remove('reveal-btn');

        } catch (err) {
            btn.disabled = false;
            btn.textContent = originalText;
            btn.classList.remove('opacity-75', 'cursor-not-allowed');
            alert(err.message);
        }
    });
});
</script>
@endsection
