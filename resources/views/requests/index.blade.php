@extends('layouts.app')

@section('title', 'রিকোয়েস্ট ফিড — রক্তদূত')

@section('content')
<div class="flex items-start justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight">রক্তের রিকোয়েস্ট ফিড</h1>
        <p class="text-slate-500 font-medium mt-1">সাম্প্রতিক পেন্ডিং রিকোয়েস্টগুলো</p>
    </div>

    <a href="{{ route('requests.create') }}"
       class="shrink-0 inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg font-extrabold shadow-sm shadow-red-200">
        নতুন রিকোয়েস্ট
    </a>
</div>

@if ($requests->isEmpty())
    <div class="rounded-2xl border border-slate-200 bg-white p-10 text-center">
        <div class="text-slate-900 font-extrabold text-lg">কোনো পেন্ডিং রিকোয়েস্ট পাওয়া যায়নি</div>
        <div class="text-slate-500 text-sm mt-2 font-medium">নতুন রিকোয়েস্ট তৈরি হলে এখানে দেখাবে।</div>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @foreach ($requests as $r)
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="text-lg font-extrabold truncate">{{ $r->patient_name ?? 'রোগী' }}</div>
                        <div class="text-sm text-slate-500 font-medium truncate mt-1">{{ $r->hospital ?? 'হাসপাতাল উল্লেখ নেই' }}</div>
                    </div>

                    <div class="shrink-0 px-3 py-1 rounded-lg bg-red-50 text-red-700 border border-red-100 font-extrabold">
                        {{ $r->blood_group?->value ?? (string) $r->blood_group }}
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
                        <div class="text-xs text-slate-500 font-semibold">লোকেশন</div>
                        <div class="font-extrabold text-slate-800 mt-1">{{ $r->thana ?? '-' }}, {{ $r->district ?? '-' }}</div>
                    </div>
                    <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
                        <div class="text-xs text-slate-500 font-semibold">দরকার</div>
                        <div class="font-extrabold text-slate-800 mt-1">{{ $r->needed_at?->format('Y-m-d H:i') ?? 'ASAP' }}</div>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between text-xs text-slate-500 font-semibold">
                    <span>পোস্ট: {{ $r->created_at?->diffForHumans() }}</span>
                    <span>ব্যাগ: {{ $r->bags_needed ?? '-' }}</span>
                </div>

                {{-- Optional actions (won't show unless backend adds these routes) --}}
                <div class="mt-5 flex flex-wrap gap-2">
                    @if (Route::has('requests.respond'))
                        <form method="POST" action="{{ route('requests.respond', $r) }}">
                            @csrf
                            <input type="hidden" name="status" value="accepted" />
                            <button class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm">Accept</button>
                        </form>

                        <form method="POST" action="{{ route('requests.respond', $r) }}">
                            @csrf
                            <input type="hidden" name="status" value="declined" />
                            <button class="px-4 py-2 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-extrabold text-sm">Decline</button>
                        </form>
                    @endif

                    @if (Route::has('requests.fulfill'))
                        <form method="POST" action="{{ route('requests.fulfill', $r) }}">
                            @csrf
                            <button class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-extrabold text-sm">Fulfilled</button>
                        </form>
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