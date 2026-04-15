@props([
    'donor',
    'challenge' => null,
    'revealedPhone' => null,
    'isTarget' => false,
])

@php
    $bloodGroup = $donor->blood_group?->value ?? (string) $donor->blood_group;
    $isOrgVerified = !empty($donor->organization_id) && (($donor->org_status ?? null) === 'approved');
    $isNidVerified = in_array($donor->nid_status, ['approved', 'verified'], true);
    $isAvailableNow = (bool) $donor->is_ready_now;
    $canDonate = $donor->is_eligible_to_donate;
    $daysFromLastDonation = $donor->last_donated_at ? (int) $donor->last_donated_at->diffInDays(now()) : null;
    $location = trim(($donor->district?->name ?? 'জেলা নেই') . (($donor->upazila?->name ?? null) ? ' · ' . $donor->upazila->name : ''));

    $masked = $donor->phone
        ? substr($donor->phone, 0, 3) . str_repeat('*', max(1, strlen($donor->phone) - 7)) . substr($donor->phone, -4)
        : 'নম্বর দেওয়া নেই';
    $showInlineError = $isTarget && session('error');
    $canRequest = auth()->check() && ((auth()->user()->role?->value ?? auth()->user()->role) === 'recipient');
@endphp

<article class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-red-200 hover:shadow-lg"
         data-donor-card
         data-donor-id="{{ $donor->id }}"
         data-reveal-start-url="{{ route('donors.reveal.start', $donor->id) }}"
         data-reveal-verify-url="{{ route('donors.reveal.verify', $donor->id) }}"
         data-csrf-token="{{ csrf_token() }}"
         data-masked-phone="{{ $masked }}"
         data-revealed-phone="{{ $revealedPhone ?: '' }}"
         data-can-request="{{ $canRequest ? '1' : '0' }}"
         data-request-url="{{ route('requests.create') }}">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <div class="flex items-center gap-1.5">
                <h3 class="truncate text-base font-extrabold text-slate-900">{{ $donor->name }}</h3>
                @if($isOrgVerified || $isNidVerified || $donor->verified_badge)
                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-blue-50 text-blue-600" title="Verified">
                        <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.707-9.293a1 1 0 0 0-1.414-1.414L9 10.586 7.707 9.293a1 1 0 0 0-1.414 1.414l2 2a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                @endif
            </div>
        </div>

        <span class="inline-flex h-8 shrink-0 items-center rounded-full border border-red-200 bg-red-50 px-3 text-sm font-black text-red-700">
            {{ $bloodGroup }}
        </span>
    </div>

    <div class="mt-3 flex flex-wrap gap-2">
        @if($isAvailableNow)
            <span class="inline-flex h-7 items-center rounded-full border border-emerald-200 bg-emerald-50 px-3 text-xs font-bold text-emerald-700">Available</span>
        @endif
        @if($isOrgVerified)
            <span class="inline-flex h-7 items-center rounded-full border border-blue-200 bg-blue-50 px-3 text-xs font-bold text-blue-700">Org Verified</span>
        @endif
        @if($isNidVerified)
            <span class="inline-flex h-7 items-center rounded-full border border-teal-200 bg-teal-50 px-3 text-xs font-bold text-teal-700">NID Verified</span>
        @endif
        @if(!$isAvailableNow && !$isOrgVerified && !$isNidVerified)
            <span class="inline-flex h-7 items-center rounded-full border border-slate-200 bg-slate-100 px-3 text-xs font-bold text-slate-700">Regular</span>
        @endif
    </div>

    <div class="mt-3 grid grid-cols-1 gap-2 text-sm sm:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
            <p class="text-[11px] font-semibold text-slate-500">লোকেশন</p>
            <p class="mt-0.5 flex items-center gap-1.5 font-bold text-slate-800">
                <svg class="h-3.5 w-3.5 shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657 13.414 20.9a2 2 0 0 1-2.827 0l-4.243-4.243a8 8 0 1 1 11.313 0Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                </svg>
                <span class="truncate">{{ $location }}</span>
            </p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
            <p class="text-[11px] font-semibold text-slate-500">যোগ্যতা</p>
            @if($canDonate)
                <p class="mt-0.5 font-bold text-emerald-700">রক্তদানে প্রস্তুত</p>
            @else
                <p class="mt-0.5 font-bold text-amber-700">Cooldown চলছে</p>
            @endif
            @if(!is_null($daysFromLastDonation))
                <p class="mt-1 text-xs font-semibold text-slate-500">শেষ রক্তদান: {{ \App\Support\BanglaDate::digits((string) $daysFromLastDonation) }} দিন আগে</p>
            @endif
        </div>
    </div>

    <div class="mt-4 rounded-xl border border-slate-200 bg-white p-3">
        <div class="mb-3 flex items-center justify-between gap-2">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">মোবাইল নম্বর</p>
            <p class="font-mono text-sm font-bold text-slate-800 js-phone-text">{{ $revealedPhone ?: $masked }}</p>
        </div>

        <div class="js-reveal-container">
            @if(!$revealedPhone)
                @if($isTarget && is_array($challenge))
                    <form method="POST" action="{{ route('donors.reveal.verify', $donor->id) }}" class="space-y-2 js-reveal-verify-form" data-url="{{ route('donors.reveal.verify', $donor->id) }}">
                        @csrf
                        <label class="block rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 js-question-label">
                            OTP নিরাপত্তা প্রশ্ন: {{ $challenge['question'] }}
                        </label>
                        <div class="flex gap-2">
                            <input type="number" name="answer" required class="min-w-0 flex-1 rounded-lg border-slate-300 text-sm focus:border-red-500 focus:ring-red-500 js-answer-input" placeholder="উত্তর লিখুন">
                            <button type="submit" class="inline-flex h-10 items-center justify-center rounded-lg bg-slate-800 px-4 text-sm font-bold text-white transition hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-300 js-verify-btn">
                                Verify
                            </button>
                        </div>
                    </form>
                @else
                    <button type="button"
                            data-reveal-start
                            class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-lg bg-red-600 px-4 text-sm font-black text-white transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                        <svg class="hidden h-4 w-4 animate-spin reveal-spinner" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"></path>
                        </svg>
                        <span class="reveal-btn-text">নম্বর দেখুন</span>
                    </button>
                @endif
            @else
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 js-success-actions">
                    <a href="tel:{{ $revealedPhone }}" class="inline-flex h-10 items-center justify-center rounded-lg bg-emerald-600 px-4 text-sm font-black text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-300 js-call-btn">
                        কল করুন
                    </a>
                    @auth
                        @if(auth()->user()->role?->value === 'recipient' || auth()->user()->role === 'recipient')
                            <a href="{{ route('requests.create') }}" class="inline-flex h-10 items-center justify-center rounded-lg border border-red-200 bg-red-50 px-4 text-sm font-bold text-red-700 transition hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-200">
                                রিকোয়েস্ট করুন
                            </a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>

        <p class="mt-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 js-inline-error {{ $showInlineError ? '' : 'hidden' }}">
            {{ $showInlineError ? session('error') : '' }}
        </p>
    </div>

    <div class="mt-3 flex flex-wrap gap-2">
        @if(!empty($donor->qr_token))
            <a href="{{ route('public.verify', $donor->qr_token) }}"
               class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-300 bg-white px-3 text-xs font-bold text-slate-700 transition hover:bg-slate-50">
                প্রোফাইল দেখুন
            </a>
        @endif
    </div>
</article>
