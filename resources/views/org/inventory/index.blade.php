@extends('layouts.org-dashboard')

@section('title', 'ব্লাড ব্যাংক ইনভেন্টরি — ' . ($org->name ?? 'অর্গ ড্যাশবোর্ড'))

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8" data-panel-id="inventory">

    {{-- ── Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black text-slate-900">🩸 ব্লাড ব্যাংক ইনভেন্টরি</h1>
            <p class="text-sm text-slate-500 mt-1">আপনার রক্তের মজুদ আপডেট করুন — এটি পাবলিক সার্চে দেখানো হবে।</p>
        </div>

        {{-- Accepting Toggle --}}
        @php
            $firstRow = $rows->first(fn($r) => $r->exists);
            $accepting = $firstRow ? $firstRow->is_accepting_donations : true;
        @endphp
        <div id="toggle-accepting-wrap" class="flex items-center gap-3"
             x-data="{ accepting: {{ $accepting ? 'true' : 'false' }}, loading: false }">
            <span class="text-sm font-semibold text-slate-600">আজ Donation নিচ্ছি:</span>
            <button type="button"
                    @click="
                        if(loading) return;
                        loading = true;
                        fetch('{{ route('org.inventory.toggle') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                            body: JSON.stringify({ state: !accepting })
                        }).then(r => r.json()).then(data => {
                            if(data.success) { accepting = data.accepting; document.getElementById('accepting-hidden').value = accepting ? '1' : '0'; }
                        }).finally(() => loading = false);
                    "
                    class="relative inline-flex h-7 w-12 items-center rounded-full transition-colors duration-300 focus:outline-none"
                    :class="accepting ? 'bg-emerald-500' : 'bg-slate-300'"
                    :aria-pressed="accepting.toString()">
                <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition-transform duration-300"
                      :class="accepting ? 'translate-x-6' : 'translate-x-1'">
                </span>
            </button>
            <span class="text-sm font-bold"
                  :class="accepting ? 'text-emerald-600' : 'text-slate-400'"
                  x-text="accepting ? 'হ্যাঁ' : 'না'">
            </span>
        </div>
    </div>

    {{-- ── Last Updated Info ── --}}
    @if($rows->where('exists', true)->isNotEmpty())
    <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 mb-6 flex items-center gap-3 text-sm">
        <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-blue-700 font-medium">
            শেষ আপডেট:
            <strong>{{ $rows->where('exists', true)->max('updated_at')?->diffForHumans() ?? 'এখনো কোনো আপডেট নেই' }}</strong>
        </span>
    </div>
    @else
    <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mb-6 flex items-center gap-3 text-sm">
        <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span class="text-amber-700 font-medium">এখনো কোনো stock data নেই। নিচে আপডেট করুন।</span>
    </div>
    @endif

    {{-- ── Inventory Form ── --}}
    <form method="POST" action="{{ route('org.inventory.update') }}" id="inventory-form">
        @csrf
        @method('PUT')
        <input type="hidden" name="is_accepting_donations" id="accepting-hidden" value="{{ $accepting ? '1' : '0' }}">

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            {{-- Table Header --}}
            <div class="flex items-center justify-between bg-slate-50 border-b border-slate-200 px-6 py-3 text-xs font-black uppercase tracking-widest text-slate-500">
                <div class="w-1/3">রক্তের গ্রুপ</div>
                <div class="w-1/3 flex justify-center text-center">পরিমাণ (ব্যাগ)</div>
                <div class="w-1/3 flex justify-end text-right">অবস্থা</div>
            </div>

            {{-- Blood Group Rows --}}
            @foreach($rows as $i => $row)
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 last:border-0 hover:bg-slate-50 transition">
                <input type="hidden" name="inventory[{{ $i }}][blood_group]" value="{{ $row->blood_group }}">

                {{-- Blood Group Badge --}}
                <div class="w-1/3">
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-2xl font-black text-lg
                        @if(str_contains($row->blood_group, 'O')) bg-red-100 text-red-700
                        @elseif(str_contains($row->blood_group, 'AB')) bg-purple-100 text-purple-700
                        @elseif(str_starts_with($row->blood_group, 'A')) bg-blue-100 text-blue-700
                        @else bg-green-100 text-green-700 @endif">
                        {{ $row->blood_group }}
                    </span>
                </div>

                {{-- Units Input --}}
                <div class="w-1/3 flex justify-center">
                    <div class="flex items-center gap-2">
                        <button type="button"
                                onclick="changeUnit(this, -1)"
                                class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-red-100 hover:text-red-600 text-slate-600 font-bold transition flex items-center justify-center text-lg">
                            −
                        </button>
                        <input type="number"
                               name="inventory[{{ $i }}][units_available]"
                               value="{{ $row->units_available ?? 0 }}"
                               min="0" max="9999"
                               class="unit-input w-20 text-center rounded-xl border border-slate-200 py-1.5 text-base font-black text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               oninput="updateStatus(this)">
                        <button type="button"
                                onclick="changeUnit(this, 1)"
                                class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-emerald-100 hover:text-emerald-600 text-slate-600 font-bold transition flex items-center justify-center text-lg">
                            +
                        </button>
                    </div>
                </div>

                {{-- Status Badge --}}
                <div class="w-1/3 flex justify-end">
                    <span class="status-badge inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border
                        {{ ($row->units_available ?? 0) >= 5 ? 'bg-emerald-100 text-emerald-700 border-emerald-200' :
                           (($row->units_available ?? 0) >= 1 ? 'bg-amber-100 text-amber-700 border-amber-200' : 'bg-red-100 text-red-600 border-red-200') }}">
                        <span class="status-dot">
                            {{ ($row->units_available ?? 0) >= 5 ? '✅' : (($row->units_available ?? 0) >= 1 ? '⚠️' : '❌') }}
                        </span>
                        <span class="status-text">
                            {{ ($row->units_available ?? 0) >= 5 ? 'পর্যাপ্ত' : (($row->units_available ?? 0) >= 1 ? 'সীমিত' : 'নেই') }}
                        </span>
                    </span>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Notes --}}
        <div class="mt-5">
            <label class="block text-sm font-bold text-slate-700 mb-2">বিশেষ তথ্য / সময়সূচি (ঐচ্ছিক)</label>
            <textarea name="notes" rows="2"
                      placeholder="যেমন: সকাল ৯টা — বিকাল ৫টা, জরুরি যোগাযোগ: ০১৭XXXXXXXX"
                      class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ $rows->first(fn($r) => $r->exists)?->notes }}</textarea>
        </div>

        {{-- Save Button --}}
        <div class="mt-6 flex items-center justify-between">
            <p class="text-xs text-slate-400">
                📊 প্রতিটি আপডেট টাইম-সিরিজ লগে সংরক্ষিত হয়।
            </p>
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-black px-6 py-3 rounded-xl transition shadow-sm hover:shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 13l4 4L19 7"/>
                </svg>
                সব পরিবর্তন সেভ করুন
            </button>
        </div>
    </form>

    {{-- ── Recent Update Log ── --}}
    @if($recentLogs->isNotEmpty())
    <div class="mt-10">
        <h2 class="text-base font-black text-slate-800 mb-4">📋 সাম্প্রতিক আপডেট লগ</h2>
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="grid grid-cols-4 bg-slate-50 border-b px-4 py-2.5 text-xs font-black uppercase tracking-wider text-slate-500">
                <div>রক্তের গ্রুপ</div>
                <div>পরিমাণ</div>
                <div>আপডেট দিয়েছেন</div>
                <div>সময়</div>
            </div>
            @foreach($recentLogs as $log)
            <div class="grid grid-cols-4 px-4 py-3 border-b border-slate-100 last:border-0 text-sm">
                <div>
                    <span class="font-black text-slate-800">{{ $log->blood_group }}</span>
                </div>
                <div class="font-bold text-slate-700">{{ $log->units }} ব্যাগ</div>
                <div class="text-slate-500">{{ $log->recorder?->name ?? 'সিস্টেম' }}</div>
                <div class="text-slate-400 text-xs">{{ $log->created_at->diffForHumans() }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script>
    window.changeUnit = function(btn, delta) {
        const row = btn.closest('.flex');
        const input = row.querySelector('.unit-input');
        const newVal = Math.max(0, Math.min(9999, parseInt(input.value || 0) + delta));
        input.value = newVal;
        window.updateStatusForInput(input, newVal);
    };

    window.updateStatus = function(input) {
        window.updateStatusForInput(input, parseInt(input.value || 0));
    };

    window.updateStatusForInput = function(input, val) {
        const row = input.closest('.flex');
        const badge = row.querySelector('.status-badge');
        const dot = row.querySelector('.status-dot');
        const text = row.querySelector('.status-text');

        if (val >= 5) {
            badge.className = badge.className.replace(/bg-\w+-\d+\s|text-\w+-\d+\s|border-\w+-\d+/g, '');
            badge.classList.add('bg-emerald-100', 'text-emerald-700', 'border-emerald-200');
            dot.textContent = '✅'; text.textContent = 'পর্যাপ্ত';
        } else if (val >= 1) {
            badge.classList.remove('bg-emerald-100', 'text-emerald-700', 'border-emerald-200',
                                   'bg-red-100', 'text-red-600', 'border-red-200');
            badge.classList.add('bg-amber-100', 'text-amber-700', 'border-amber-200');
            dot.textContent = '⚠️'; text.textContent = 'সীমিত';
        } else {
            badge.classList.remove('bg-emerald-100', 'text-emerald-700', 'border-emerald-200',
                                   'bg-amber-100', 'text-amber-700', 'border-amber-200');
            badge.classList.add('bg-red-100', 'text-red-600', 'border-red-200');
            dot.textContent = '❌'; text.textContent = 'নেই';
        }
    };

    window.toggleAccepting = async function() {
        const btn = document.getElementById('toggle-accepting-btn');
        const dot = document.getElementById('toggle-dot');
        const label = document.getElementById('toggle-label');
        const hidden = document.getElementById('accepting-hidden');

        try {
            const res = await fetch('{{ route("org.inventory.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            });
            const data = await res.json();

            if (data.success) {
                const on = data.accepting;
                btn.classList.toggle('bg-emerald-500', on);
                btn.classList.toggle('bg-slate-300', !on);
                dot.classList.toggle('translate-x-6', on);
                dot.classList.toggle('translate-x-1', !on);
                label.textContent = on ? 'হ্যাঁ' : 'না';
                label.className = `text-sm font-bold ${on ? 'text-emerald-600' : 'text-slate-400'}`;
                hidden.value = on ? '1' : '0';
            }
        } catch (e) {
            console.error('Toggle failed:', e);
        }
    }
</script>
@endpush
@endsection
