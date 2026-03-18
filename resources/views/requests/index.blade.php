@extends('layouts.app')

@section('title', 'রিকোয়েস্ট ফিড — রক্তদূত')

@section('content')
<div class="flex items-start justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight">রক্তের রিকোয়েস্ট ফিড</h1>
        <p class="text-slate-500 font-medium mt-1">সাম্প্রতিক পেন্ডিং রিকোয়েস্টগুলো</p>
    </div>

    <a href="{{ route('requests.create') }}"
       class="shrink-0 inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg font-extrabold shadow-sm shadow-red-200">
        নতুন রিকোয়েস্ট
    </a>
</div>

@if ($requests->isEmpty())
    <div class="rounded-2xl border border-slate-200 bg-white p-10 text-center">
        <div class="text-slate-900 font-extrabold text-lg">কোনো পেন্ডিং রিকোয়েস্ট পাওয়া যায়নি</div>
        <div class="text-slate-500 text-sm mt-2 font-medium">নতুন রিকোয়েস্ট তৈরি হলে এখানে দেখাবে।</div>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @foreach ($requests as $r)
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <a href="{{ route('requests.show', $r) }}" class="text-lg font-extrabold truncate hover:text-red-600">
                            {{ $r->patient_name ?? 'রোগী' }}
                        </a>
                        <div class="text-sm text-slate-500 font-medium truncate mt-1">{{ $r->hospital ?? 'হাসপাতাল উল্লেখ নেই' }}</div>
                    </div>

                    <div class="shrink-0 px-3 py-1 rounded-lg bg-red-50 text-red-700 border border-red-100 font-extrabold">
                        {{ $r->blood_group->label() ?? $r->blood_group }}
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
                        <div class="text-xs text-slate-500 font-semibold">লোকেশন</div>
                        <div class="font-extrabold text-slate-800 mt-1">{{ $r->thana ?? '-' }}, {{ $r->district ?? '-' }}</div>
                    </div>
                    <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
                        <div class="text-xs text-slate-500 font-semibold">দরকার</div>
                        <div class="font-extrabold text-slate-800 mt-1">{{ $r->needed_at?->format('d M, Y h:i A') ?? 'ASAP' }}</div>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between text-xs text-slate-500 font-semibold">
                    <span>পোস্ট: {{ $r->created_at?->diffForHumans() }}</span>
                    <span>ব্যাগ: {{ $r->bags_needed ?? '-' }}</span>
                </div>

                {{-- ডাইনামিক অ্যাকশন বাটন সেকশন --}}
                <div class="mt-5 flex flex-wrap gap-2">
                    @php
                        // Controller-এ eager loading করা আছে, তাই responses->first() অতিরিক্ত কোয়েরি করবে না
                        $myResponse = $r->responses->first();
                        $isOwner = auth()->check() && ((int) $r->requested_by === (int) auth()->id());
                    @endphp

                    {{-- Fulfill Button: শুধুমাত্র রিকোয়েস্টের মালিক দেখবে --}}
                    @if (Route::has('requests.fulfill') && $isOwner && $r->status !== 'fulfilled')
                        <form method="POST" action="{{ route('requests.fulfill', $r) }}">
                            @csrf
                            <button class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-extrabold text-sm shadow-sm transition">
                                Fulfilled
                            </button>
                        </form>
                    @endif

                    {{-- Respond Section: মালিক ছাড়া অন্যরা দেখবে --}}
                    @if (Route::has('requests.respond') && !$isOwner && strtolower($r->status) !== 'fulfilled')
                        @if (!$myResponse)
                            {{-- এখনো রেসপন্স করেনি --}}
                            
                            {{-- 🚨 Eligibility Check --}}
                            @if(auth()->user()->is_eligible_to_donate)
                                <form method="POST" action="{{ route('requests.respond', $r) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="accepted" />
                                    <button class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm transition">
                                        Accept
                                    </button>
                                </form>
                            @else
                                <button disabled title="আপনি রক্তদানের যোগ্য নন (ড্যাশবোর্ড চেক করুন)" class="px-4 py-2 rounded-lg bg-slate-200 text-slate-400 font-extrabold text-sm cursor-not-allowed border border-slate-300">
                                    Accept
                                </button>
                            @endif

                            <form method="POST" action="{{ route('requests.respond', $r) }}">
                                @csrf
                                <input type="hidden" name="status" value="declined" />
                                <button class="px-4 py-2 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-extrabold text-sm transition">
                                    Decline
                                </button>
                            </form>
                        @elseif ($myResponse->status === 'accepted')
                            {{-- অলরেডি এক্সেপ্টেড --}}
                            <div class="flex items-center gap-2">
                                <span class="px-4 py-2 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100 font-extrabold text-sm">
                                    ✓ Accepted
                                </span>
                                
                                <form method="POST" action="{{ route('requests.respond', $r) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="declined" />
                                    <button class="text-xs text-slate-500 hover:text-red-600 font-bold underline transition">
                                        Change to Decline
                                    </button>
                                </form>
                            </div>
                        @elseif ($myResponse->status === 'declined')
                            {{-- অলরেডি ডিক্লাইনড --}}
                            <div class="flex items-center gap-2">
                                <span class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 font-extrabold text-sm">
                                    Declined
                                </span>

                                {{-- 🚨 Eligibility Check for changing mind --}}
                                @if(auth()->user()->is_eligible_to_donate)
                                    <form method="POST" action="{{ route('requests.respond', $r) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="accepted" />
                                        <button class="text-xs text-emerald-600 hover:text-emerald-700 font-bold underline transition">
                                            Change to Accept
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400 font-bold cursor-not-allowed" title="আপনি রক্তদানের যোগ্য নন">
                                        Cannot Accept
                                    </span>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $requests->links() }}
    </div>
@endif
@endsection