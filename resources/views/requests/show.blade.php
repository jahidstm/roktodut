@extends('layouts.app')

@section('title', 'রিকোয়েস্ট ডিটেইলস — রক্তদূত')

@section('content')
<div class="max-w-5xl">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight">রিকোয়েস্ট ডিটেইলস</h1>
            <p class="text-slate-500 font-medium mt-1">Accepted ডোনার লিস্ট (Reveal-flow সহ)</p>
        </div>

        <a href="{{ route('requests.index') }}"
           class="shrink-0 inline-flex items-center justify-center border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 px-4 py-2.5 rounded-lg font-extrabold">
            ফিডে ফিরে যান
        </a>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <div class="text-lg font-extrabold truncate">{{ $request->patient_name ?? 'রোগী' }}</div>
                <div class="text-sm text-slate-500 font-medium truncate mt-1">{{ $request->hospital ?? 'হাসপাতাল উল্লেখ নেই' }}</div>
                <div class="text-sm text-slate-500 font-semibold mt-2">
                    লোকেশন: <span class="text-slate-800 font-extrabold">{{ $request->thana ?? '-' }}, {{ $request->district ?? '-' }}</span>
                </div>
            </div>

            <div class="shrink-0 px-3 py-1 rounded-lg bg-red-50 text-red-700 border border-red-100 font-extrabold">
                {{ $request->blood_group?->value ?? (string) $request->blood_group }}
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
            <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
                <div class="text-xs text-slate-500 font-semibold">দরকার</div>
                <div class="font-extrabold text-slate-800 mt-1">{{ $request->needed_at?->format('Y-m-d H:i') ?? 'ASAP' }}</div>
            </div>
            <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
                <div class="text-xs text-slate-500 font-semibold">ব্যাগ</div>
                <div class="font-extrabold text-slate-800 mt-1">{{ $request->bags_needed ?? '-' }}</div>
            </div>
            <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
                <div class="text-xs text-slate-500 font-semibold">স্ট্যাটাস</div>
                <div class="font-extrabold text-slate-800 mt-1">{{ $request->status }}</div>
            </div>
        </div>

        @if($request->notes)
            <div class="mt-4">
                <div class="text-xs text-slate-500 font-semibold">নোট</div>
                <div class="mt-1 font-semibold text-slate-800 whitespace-pre-line">{{ $request->notes }}</div>
            </div>
        @endif
    </div>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="text-lg font-extrabold">রেসপন্স সারাংশ</div>
            <div class="mt-3 text-sm font-semibold text-slate-700">
                Accepted: <span class="font-extrabold text-emerald-700">{{ $acceptedCount }}</span><br>
                Declined: <span class="font-extrabold text-slate-700">{{ $declinedCount }}</span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="text-lg font-extrabold">Accepted ডোনার</div>

            @if(!$canViewAcceptedDonors)
                <div class="mt-3 text-sm text-slate-500 font-medium">
                    প্রাইভেসির কারণে আপনি accepted ডোনারদের তালিকা দেখতে পারবেন না।
                </div>
            @else
                @if($acceptedResponses->isEmpty())
                    <div class="mt-3 text-sm text-slate-500 font-medium">
                        এখনো কেউ accept করেনি।
                    </div>
                @else
                    <div class="mt-4 space-y-3">
                        @foreach($acceptedResponses as $resp)
                            <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <div class="min-w-0">
                                    <div class="font-extrabold text-slate-900 truncate">
                                        {{ $resp->user?->name ?? 'Unknown' }}
                                    </div>
                                    <div class="text-xs text-slate-500 font-semibold mt-1">
                                        Accepted
                                    </div>

                                    <div class="mt-2 text-sm font-semibold text-slate-700">
                                        ফোন: <span id="phone-{{ $resp->user_id }}" class="font-extrabold text-slate-900">Hidden</span>
                                    </div>
                                </div>

                                <button type="button"
                                        class="reveal-btn px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-extrabold text-sm"
                                        data-url="{{ route('requests.donors.reveal_phone', ['bloodRequest' => $request->id, 'donor' => $resp->user_id]) }}"
                                        data-target="phone-{{ $resp->user_id }}">
                                    Reveal
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.reveal-btn');
    if (!btn) return;

    const url = btn.getAttribute('data-url');
    const targetId = btn.getAttribute('data-target');
    const targetEl = document.getElementById(targetId);

    btn.disabled = true;
    btn.textContent = 'Revealing...';

    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        });

        if (!res.ok) throw new Error('Failed to reveal');

        const data = await res.json();
        targetEl.textContent = data.phone ?? 'N/A';
        btn.textContent = 'Revealed';
    } catch (err) {
        btn.disabled = false;
        btn.textContent = 'Reveal';
        alert('Reveal ব্যর্থ হয়েছে। আবার চেষ্টা করুন।');
    }
});
</script>
@endsection