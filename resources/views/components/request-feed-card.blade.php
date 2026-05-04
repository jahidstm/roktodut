@props([
    'request',
    'isPublic' => false,
    'showRequester' => false,
])

@php
    $urgency = $request->urgency?->value ?? $request->urgency ?? 'normal';
    $bloodGroup = $request->blood_group?->value ?? (string) $request->blood_group;
    $neededAt = $request->needed_at;
    $status = strtolower((string) $request->status);
    $acceptedCount = (int) ($request->accepted_responses_count ?? 0);
    $claimedCount = (int) ($request->claimed_verifications_count ?? 0);
    $verifiedCount = (int) ($request->verified_verifications_count ?? 0);

    $urgencyMap = [
        'emergency' => ['label' => 'Emergency', 'cls' => 'bg-red-50 text-red-700 border-red-200'],
        'urgent' => ['label' => 'Urgent', 'cls' => 'bg-amber-50 text-amber-700 border-amber-200'],
        'normal' => ['label' => 'Normal', 'cls' => 'bg-slate-100 text-slate-700 border-slate-200'],
    ];
    $urgencyInfo = $urgencyMap[$urgency] ?? $urgencyMap['normal'];

    $statusLabel = 'Running';
    $statusCls = 'bg-sky-50 text-sky-700 border-sky-200';
    if ($status === 'expired') {
        $statusLabel = 'Cancelled';
        $statusCls = 'bg-rose-50 text-rose-700 border-rose-200';
    } elseif ($verifiedCount > 0 || $status === 'fulfilled') {
        $statusLabel = 'Successful (Verified)';
        $statusCls = 'bg-emerald-50 text-emerald-700 border-emerald-200';
    } elseif ($claimedCount > 0) {
        $statusLabel = 'Claimed (In Review)';
        $statusCls = 'bg-amber-50 text-amber-700 border-amber-200';
    } elseif ($acceptedCount > 0) {
        $statusLabel = 'Donor Found';
        $statusCls = 'bg-indigo-50 text-indigo-700 border-indigo-200';
    }

    $isOwner = auth()->check() && ((int) $request->requested_by === (int) auth()->id());
    $isOpen = $status === 'pending';
    $detailsUrl = route('requests.show', $request);
    if ($isPublic && !auth()->check()) {
        $detailsUrl = route('login') . '?redirect=' . urlencode(route('public.requests.index'));
    }
    $locationText = trim(($request->district?->name ?? '-') . (($request->upazila?->name ?? null) ? ' · ' . $request->upazila->name : ''));
    $myResponse = (!$isPublic && isset($request->responses)) ? $request->responses->first() : null;
@endphp

<article class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <h3 class="text-lg font-black tracking-tight text-slate-900">
                    {{ $bloodGroup }} রক্ত প্রয়োজন
                </h3>
                <span class="inline-flex h-7 items-center rounded-full border px-3 text-xs font-extrabold {{ $urgencyInfo['cls'] }}">
                    {{ $urgencyInfo['label'] }}
                </span>
            </div>
        </div>
        <span class="inline-flex h-7 shrink-0 items-center rounded-full border border-slate-200 bg-slate-50 px-3 text-xs font-bold text-slate-700">
            Units: {{ \App\Support\BanglaDate::digits((string) ($request->bags_needed ?? 1)) }}
        </span>
    </div>

    <div class="mt-3 space-y-1.5">
        <p class="truncate text-sm font-semibold text-slate-800">
            {{ $request->hospital?->display_name ?: 'হাসপাতাল উল্লেখ নেই' }}
        </p>
        <p class="flex items-center gap-1.5 text-sm text-slate-600">
            <svg class="h-4 w-4 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657 13.414 20.9a2 2 0 0 1-2.827 0l-4.243-4.243a8 8 0 1 1 11.313 0Z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
            </svg>
            <span class="truncate">{{ $locationText }}</span>
        </p>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
            <p class="text-[11px] font-semibold text-slate-500">প্রয়োজন</p>
            <p class="mt-0.5 text-sm font-bold text-slate-800">{{ \App\Support\BanglaDate::absolute($neededAt) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
            <p class="text-[11px] font-semibold text-slate-500">পোস্ট</p>
            <p class="mt-0.5 text-sm font-bold text-slate-800">{{ \App\Support\BanglaDate::relative($request->created_at) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 sm:col-span-2 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-slate-500">যোগাযোগ</p>
                <p class="mt-0.5 text-sm font-bold text-slate-800">
                    @auth
                        <a href="tel:{{ $request->contact_number }}" class="text-red-600 hover:underline">{{ $request->contact_number }}</a>
                    @else
                        {{ substr($request->contact_number, 0, 3) . '****' . substr($request->contact_number, -3) }}
                    @endauth
                </p>
            </div>
            @guest
                <a href="{{ route('login') }}" class="text-[10px] bg-red-50 text-red-600 font-bold px-2 py-1 rounded border border-red-100 hover:bg-red-100 transition">লগইন করুন</a>
            @endguest
        </div>
    </div>

    <div class="mt-3 flex items-center justify-between gap-2">
        <span class="inline-flex h-7 items-center rounded-full border px-3 text-xs font-bold {{ $statusCls }}">
            {{ $statusLabel }}
        </span>

        @if($showRequester && $request->requester)
            <div class="text-[11px] font-medium text-slate-500">
                Requester: <span class="font-semibold text-slate-700">{{ $request->requester->name }}</span>
            </div>
        @endif
    </div>

    <div class="mt-4 flex flex-wrap gap-2">
        <a href="{{ $detailsUrl }}"
           class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-bold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">
            View Details
        </a>

        @if($isPublic)
            @guest
                <a href="{{ route('login') }}?redirect={{ urlencode(route('public.requests.index')) }}"
                   class="inline-flex h-10 items-center justify-center rounded-xl bg-red-600 px-4 text-sm font-black text-white transition hover:bg-red-700">
                    Respond
                </a>
            @else
                <a href="{{ $detailsUrl }}"
                   class="inline-flex h-10 items-center justify-center rounded-xl bg-red-600 px-4 text-sm font-black text-white transition hover:bg-red-700">
                    Respond
                </a>
            @endguest
        @else
            @if($isOwner && $isOpen)
                <form method="POST" action="{{ route('requests.fulfill', $request) }}">
                    @csrf
                    <button class="inline-flex h-10 items-center justify-center rounded-xl bg-emerald-600 px-4 text-sm font-black text-white transition hover:bg-emerald-700">
                        Complete
                    </button>
                </form>
                <a href="{{ $detailsUrl }}"
                   class="inline-flex h-10 items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 px-4 text-sm font-bold text-emerald-700 transition hover:bg-emerald-100">
                    Manage
                </a>
            @elseif(!$isOwner && $isOpen)
                @if($myResponse && $myResponse->status === 'accepted')
                    <span class="inline-flex h-10 items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 px-4 text-sm font-bold text-emerald-700">
                        Response Sent
                    </span>
                    <a href="{{ $detailsUrl }}"
                       class="inline-flex h-10 items-center justify-center rounded-xl border border-emerald-200 bg-white px-4 text-sm font-bold text-emerald-700 transition hover:bg-emerald-50">
                        Manage
                    </a>
                @elseif($myResponse && $myResponse->status === 'declined')
                    <a href="{{ $detailsUrl }}"
                       class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                        Manage
                    </a>
                @else
                    <form method="POST" action="{{ route('requests.respond', $request) }}">
                        @csrf
                        <input type="hidden" name="status" value="accepted" />
                        <button class="inline-flex h-10 items-center justify-center rounded-xl bg-red-600 px-4 text-sm font-black text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:bg-slate-300"
                                @disabled(!(auth()->check() && auth()->user()->is_eligible_to_donate))
                                title="{{ auth()->check() && !auth()->user()->is_eligible_to_donate ? 'আপনি রক্তদানের যোগ্য নন' : '' }}">
                            Respond
                        </button>
                    </form>
                @endif
            @elseif(($status === 'fulfilled' || $verifiedCount > 0) && auth()->check() && (auth()->id() === (int) $request->requested_by || auth()->user()->role === 'admin'))
                <a href="{{ $detailsUrl }}"
                   class="inline-flex h-10 items-center justify-center rounded-xl bg-indigo-600 px-4 text-sm font-black text-white transition hover:bg-indigo-700">
                    View Proof
                </a>
            @endif
        @endif
    </div>
</article>
