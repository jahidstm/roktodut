@extends('layouts.app')

@section('title', 'রিকোয়েস্ট ডিটেইলস — রক্তদূত')

@section('content')
<div class="max-w-5xl mx-auto">

    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight">রিকোয়েস্ট ডিটেইলস</h1>
            <p class="text-slate-500 font-medium mt-1">Accepted ডোনার লিস্ট</p>
        </div>

        <a href="{{ route('requests.index') }}"
           class="shrink-0 inline-flex items-center justify-center border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 px-4 py-2.5 rounded-lg font-extrabold shadow-sm transition-colors">
            ফিডে ফিরে যান
        </a>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <div class="text-lg font-extrabold truncate">{{ $bloodRequest->patient_name ?? 'রোগী' }}</div>
                <div class="text-sm text-slate-500 font-medium truncate mt-1">{{ $bloodRequest->hospital ?? 'হাসপাতাল উল্লেখ নেই' }}</div>
                <div class="text-sm text-slate-500 font-semibold mt-2">
                    লোকেশন: <span class="text-slate-800 font-extrabold">{{ $bloodRequest->thana ?? '-' }}, {{ $bloodRequest->district ?? '-' }}</span>
                </div>
            </div>

            <div class="shrink-0 px-3 py-1 rounded-lg bg-red-50 text-red-700 border border-red-100 font-extrabold">
                {{ $bloodRequest->blood_group?->value ?? (string) $bloodRequest->blood_group }}
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
            <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
                <div class="text-xs text-slate-500 font-semibold">দরকার</div>
                <div class="font-extrabold text-slate-800 mt-1">{{ $bloodRequest->needed_at?->format('Y-m-d h:i A') ?? 'ASAP' }}</div>
            </div>
            <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
                <div class="text-xs text-slate-500 font-semibold">ব্যাগ</div>
                <div class="font-extrabold text-slate-800 mt-1">{{ $bloodRequest->bags_needed ?? '-' }}</div>
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
                <div class="mt-3 text-sm text-slate-500 font-medium p-4 bg-slate-50 rounded-xl border border-slate-100">
                    প্রাইভেসির কারণে আপনি accepted ডোনারদের তালিকা দেখতে পারবেন না। শুধুমাত্র রিকোয়েস্টের মালিক এটি দেখতে পারবেন।
                </div>
            @else
                @if($acceptedResponses->isEmpty())
                    <div class="mt-3 text-sm text-slate-500 font-medium p-4 bg-slate-50 rounded-xl border border-slate-100">
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
                                    <div class="text-xs text-emerald-600 font-bold mt-1">
                                        ✓ Accepted
                                    </div>

                                    <div class="mt-2 text-sm font-semibold text-slate-700">
                                        ফোন: <span id="phone-{{ $resp->user_id }}" class="font-extrabold text-slate-900 tracking-wider">Hidden</span>
                                    </div>
                                </div>

                                <button type="button"
                                        class="reveal-btn shrink-0 px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-extrabold text-sm transition-colors"
                                        data-url="{{ route('requests.donors.reveal_phone', ['bloodRequest' => $bloodRequest->id, 'donor' => $resp->user_id]) }}"
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
    btn.classList.add('opacity-75', 'cursor-not-allowed');

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
        
        if (data.success || data.phone) {
            targetEl.textContent = data.phone ?? 'N/A';
            btn.textContent = 'Revealed';
            btn.classList.replace('bg-red-600', 'bg-slate-400');
            btn.classList.replace('hover:bg-red-700', 'hover:bg-slate-500');
            btn.classList.remove('opacity-75', 'cursor-not-allowed');
        } else {
            throw new Error(data.message || 'Unknown error');
        }
    } catch (err) {
        btn.disabled = false;
        btn.textContent = 'Reveal';
        btn.classList.remove('opacity-75', 'cursor-not-allowed');
        alert('নাম্বার দেখতে সমস্যা হচ্ছে। আবার চেষ্টা করুন।');
    }
});
</script>
@endsection