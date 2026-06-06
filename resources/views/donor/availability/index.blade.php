@extends('layouts.donor-dashboard')

@section('title', 'আমার সময়সূচি — রক্তদূত')
@section('page-title', 'আমার সময়সূচি')

@section('content')
@php
    use App\Models\DonorAvailability;
    $dayBits  = DonorAvailability::DAY_BITS;
    $dayNames = DonorAvailability::DAY_LABELS;
@endphp

<div class="max-w-4xl mx-auto px-4 py-8 space-y-8">

    {{-- ── Header ── --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">📅 আমার দান করার সময়সূচি</h1>
            <p class="text-sm text-slate-500 mt-1">কোন দিন বা সময়ে রক্ত দিতে পারবেন তা আগে থেকে সেট করুন। AI dispatch এই তথ্য দেখে আপনাকে সঠিক সময়ে notify করবে।</p>
        </div>
        @if($rules->isEmpty())
            <span class="shrink-0 text-xs font-bold text-amber-700 bg-amber-100 border border-amber-200 px-3 py-1.5 rounded-full">
                ⚠️ কোনো rule নেই
            </span>
        @else
            <span class="shrink-0 text-xs font-bold text-emerald-700 bg-emerald-100 border border-emerald-200 px-3 py-1.5 rounded-full">
                ✅ {{ $rules->where('is_active', true)->count() }}টি সক্রিয় rule
            </span>
        @endif
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 font-semibold flex items-center gap-2 text-sm">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- ══ LEFT: নতুন Rule যোগ করুন ══ --}}
        <div class="lg:col-span-3 space-y-5">

            {{-- Quick Presets --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h2 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-3">⚡ দ্রুত সেটআপ</h2>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="applyPreset([0,6])"
                            class="preset-btn px-3 py-1.5 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition">
                        শুক্র–শনি (ছুটির দিন)
                    </button>
                    <button type="button" onclick="applyPreset([0,1,2,3,4,5,6])"
                            class="preset-btn px-3 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200 transition"
                            data-bits="127">
                        সব দিন
                    </button>
                    <button type="button" onclick="applyPreset(null, '09:00', '17:00')"
                            class="preset-btn px-3 py-1.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition">
                        শুধু সকাল ৯টা–বিকাল ৫টা
                    </button>
                    <button type="button" onclick="applyPreset([0,6], '10:00', '14:00')"
                            class="preset-btn px-3 py-1.5 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200 hover:bg-purple-100 transition">
                        শুক্র–শনি সকাল
                    </button>
                </div>
            </div>

            {{-- Add Rule Form --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h2 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-4">✚ নতুন Rule যোগ করুন</h2>

                <form action="{{ route('donor.availability.store') }}" method="POST" id="availability-form">
                    @csrf

                    {{-- Type Tabs --}}
                    <div class="flex gap-2 mb-5 p-1 bg-slate-100 rounded-xl" x-data="{ tab: 'weekly' }">
                        @foreach(['weekly' => '📅 সাপ্তাহিক', 'specific_date' => '📌 নির্দিষ্ট দিন', 'date_range' => '📆 তারিখ পরিসর'] as $val => $label)
                            <button type="button"
                                    onclick="switchTab('{{ $val }}')"
                                    id="tab-{{ $val }}"
                                    class="flex-1 py-2 px-3 rounded-lg text-xs font-bold transition-all tab-btn {{ $val === 'weekly' ? 'bg-white shadow text-slate-900' : 'text-slate-500 hover:text-slate-700' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                        <input type="hidden" name="type" id="type-input" value="weekly">
                    </div>

                    {{-- Weekly Panel --}}
                    <div id="panel-weekly" class="tab-panel">
                        <label class="block text-xs font-bold text-slate-600 mb-3">কোন কোন দিন?</label>
                        <div class="grid grid-cols-7 gap-1.5 mb-4" id="weekday-grid">
                            @foreach([0 => 'শনি', 1 => 'রবি', 2 => 'সোম', 3 => 'মঙ্গল', 4 => 'বুধ', 5 => 'বৃহঃ', 6 => 'শুক্র'] as $day => $label)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="weekdays[]" value="{{ $day }}" class="sr-only weekday-cb" id="day-{{ $day }}">
                                    <span id="day-pill-{{ $day }}"
                                          onclick="toggleDay({{ $day }})"
                                          class="day-pill block text-center py-2 rounded-lg text-xs font-bold border transition-all select-none
                                                 bg-slate-100 border-slate-200 text-slate-500 hover:bg-red-50 hover:border-red-200 hover:text-red-600">
                                        {{ $label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @error('weekdays') <p class="text-red-500 text-xs mb-3">{{ $message }}</p> @enderror
                    </div>

                    {{-- Specific Date Panel --}}
                    <div id="panel-specific_date" class="tab-panel hidden">
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">তারিখ বেছে নিন</label>
                        <input type="date" name="specific_date"
                               min="{{ date('Y-m-d') }}"
                               value="{{ old('specific_date') }}"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        @error('specific_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Date Range Panel --}}
                    <div id="panel-date_range" class="tab-panel hidden">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1.5">শুরু</label>
                                <input type="date" name="date_from" min="{{ date('Y-m-d') }}" value="{{ old('date_from') }}"
                                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1.5">শেষ</label>
                                <input type="date" name="date_to" min="{{ date('Y-m-d') }}" value="{{ old('date_to') }}"
                                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            </div>
                        </div>
                    </div>

                    {{-- Time Window (shared) --}}
                    <div class="mt-4">
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">সময়সীমা (ঐচ্ছিক — ফাঁকা রাখলে সারাদিন)</label>
                        <div class="flex items-center gap-3">
                            <input type="time" name="time_from" id="time-from" value="{{ old('time_from') }}"
                                   class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            <span class="text-slate-400 font-bold text-sm">—</span>
                            <input type="time" name="time_to" id="time-to" value="{{ old('time_to') }}"
                                   class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>

                    {{-- Note --}}
                    <div class="mt-4">
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">নোট (ঐচ্ছিক)</label>
                        <input type="text" name="note" value="{{ old('note') }}"
                               placeholder="যেমন: রমজানে unavailable, পরীক্ষার সপ্তাহ…"
                               maxlength="200"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>

                    <button type="submit"
                            class="mt-5 w-full bg-red-600 hover:bg-red-700 text-white font-black py-3 rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Rule যোগ করুন
                    </button>
                </form>
            </div>
        </div>

        {{-- ══ RIGHT: ৩০-দিনের Calendar Preview ══ --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sticky top-24">
                <h2 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-4">📆 পরবর্তী ৩০ দিন</h2>

                @if($rules->where('is_active', true)->isEmpty())
                    <div class="text-center py-6">
                        <p class="text-xs text-slate-400 font-medium">কোনো সক্রিয় rule নেই।<br>Rule যোগ করলে এখানে preview দেখাবে।</p>
                    </div>
                @else
                    <div class="grid grid-cols-7 gap-1 text-center mb-2">
                        @foreach(['শ', 'র', 'সো', 'মঙ', 'বু', 'বৃ', 'শু'] as $h)
                            <div class="text-[10px] font-black text-slate-400">{{ $h }}</div>
                        @endforeach
                    </div>

                    @php
                        // Pad the start of the calendar to the correct weekday
                        // Maps Carbon (Sun=0, Sat=6) to our BD UI (Sat=0, Sun=1)
                        $firstDay = $calendarDays[0]['date'];
                        $padCount = ($firstDay->dayOfWeek + 1) % 7; 
                    @endphp

                    <div class="grid grid-cols-7 gap-1">
                        {{-- Padding cells --}}
                        @for($p = 0; $p < $padCount; $p++)
                            <div></div>
                        @endfor

                        @foreach($calendarDays as $day)
                            @php
                                $bg = match(true) {
                                    $day['is_today']    => 'bg-red-600 text-white font-black ring-2 ring-red-300',
                                    $day['available']   => 'bg-emerald-100 text-emerald-700 font-bold',
                                    $day['available'] === false => 'bg-slate-100 text-slate-400',
                                    default             => 'bg-slate-50 text-slate-400',
                                };
                            @endphp
                            <div class="aspect-square flex items-center justify-center rounded-lg text-[11px] {{ $bg }} transition"
                                 title="{{ $day['date']->format('d M') }}">
                                {{ $day['date']->format('j') }}
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 flex items-center gap-4 text-[10px] text-slate-500">
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-emerald-100 inline-block"></span> উপলব্ধ</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-slate-100 inline-block"></span> অনুপলব্ধ</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-red-600 inline-block"></span> আজ</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ══ Active Rules List ══ --}}
    @if($rules->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/60">
            <h2 class="text-sm font-black text-slate-700 uppercase tracking-wider">📋 আমার সব Rule ({{ $rules->count() }}টি)</h2>
        </div>
        <div class="divide-y divide-slate-100">
            @foreach($rules as $rule)
            <div class="flex items-center justify-between px-5 py-4 gap-4 hover:bg-slate-50/50 transition {{ $rule->is_active ? '' : 'opacity-50' }}">
                <div class="flex items-start gap-3 min-w-0">
                    <span class="shrink-0 mt-0.5 text-lg">
                        @if($rule->type === 'weekly') 📅
                        @elseif($rule->type === 'specific_date') 📌
                        @else 📆
                        @endif
                    </span>
                    <div class="min-w-0">
                        {{-- Rule description --}}
                        <p class="font-bold text-slate-800 text-sm">
                            @if($rule->type === 'weekly')
                                @if($rule->weekdays_bitmask === 127)
                                    সব দিন
                                @else
                                    {{ implode(', ', $rule->active_days) }}
                                @endif
                            @elseif($rule->type === 'specific_date')
                                {{ $rule->specific_date?->format('d M, Y') }}
                            @else
                                {{ $rule->date_from?->format('d M') }} — {{ $rule->date_to?->format('d M, Y') }}
                            @endif
                        </p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            @if($rule->time_from)
                                🕐 {{ substr($rule->time_from, 0, 5) }} — {{ substr($rule->time_to, 0, 5) }}
                            @else
                                🕐 সারাদিন
                            @endif
                            @if($rule->note)
                                · {{ $rule->note }}
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    {{-- Status badge --}}
                    <span class="text-[10px] font-black px-2 py-0.5 rounded-full {{ $rule->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ $rule->is_active ? 'সক্রিয়' : 'বন্ধ' }}
                    </span>

                    {{-- Toggle --}}
                    <form action="{{ route('donor.availability.toggle', $rule) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="text-xs font-bold px-3 py-1.5 rounded-lg border transition {{ $rule->is_active ? 'border-amber-200 text-amber-700 hover:bg-amber-50' : 'border-emerald-200 text-emerald-700 hover:bg-emerald-50' }}">
                            {{ $rule->is_active ? 'বন্ধ করুন' : 'চালু করুন' }}
                        </button>
                    </form>

                    {{-- Delete --}}
                    <form action="{{ route('donor.availability.destroy', $rule) }}" method="POST"
                          onsubmit="return confirm('এই rule মুছে ফেলবেন?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="text-xs font-bold px-3 py-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 transition">
                            মুছুন
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Info Box --}}
    <div class="bg-blue-50 border border-blue-200 rounded-2xl px-5 py-4 text-sm text-blue-800">
        <p class="font-bold mb-1">💡 কীভাবে কাজ করে?</p>
        <ul class="list-disc list-inside space-y-1 text-xs font-medium text-blue-700">
            <li>কোনো rule না থাকলে আগের মতো <strong>Available/Unavailable</strong> toggle কাজ করবে।</li>
            <li>Rule থাকলে AI dispatch শুধুমাত্র সেই দিন/সময়ে আপনাকে notify করবে।</li>
            <li><strong>Super-critical (জরুরি)</strong> request এলে calendar bypass হয়ে সবাইকে notify করা হবে।</li>
        </ul>
    </div>

</div>

@push('scripts')
<script>
    // ── Tab switching ──────────────────────────────────────────────────────
    function switchTab(type) {
        document.getElementById('type-input').value = type;
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
        document.getElementById('panel-' + type).classList.remove('hidden');
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('bg-white', 'shadow', 'text-slate-900');
            b.classList.add('text-slate-500');
        });
        const active = document.getElementById('tab-' + type);
        active.classList.add('bg-white', 'shadow', 'text-slate-900');
        active.classList.remove('text-slate-500');
    }

    // ── Weekday pill toggle ────────────────────────────────────────────────
    function toggleDay(day) {
        const cb   = document.getElementById('day-' + day);
        const pill = document.getElementById('day-pill-' + day);
        cb.checked = !cb.checked;
        if (cb.checked) {
            pill.classList.remove('bg-slate-100', 'border-slate-200', 'text-slate-500');
            pill.classList.add('bg-red-600', 'border-red-600', 'text-white');
        } else {
            pill.classList.remove('bg-red-600', 'border-red-600', 'text-white');
            pill.classList.add('bg-slate-100', 'border-slate-200', 'text-slate-500');
        }
    }

    // ── Quick preset apply ─────────────────────────────────────────────────
    // dayNumbers = array of weekday numbers (0=Sun..6=Sat), or null for "all"
    function applyPreset(dayNumbers, timeFrom = null, timeTo = null) {
        switchTab('weekly');

        // Reset all
        for (let d = 0; d <= 6; d++) {
            const cb = document.getElementById('day-' + d);
            if (cb && cb.checked) toggleDay(d);
        }

        // Apply
        const targets = dayNumbers === null ? [0,1,2,3,4,5,6] : dayNumbers;
        targets.forEach(d => {
            const cb = document.getElementById('day-' + d);
            if (cb && !cb.checked) toggleDay(d);
        });

        if (timeFrom) document.getElementById('time-from').value = timeFrom;
        if (timeTo)   document.getElementById('time-to').value   = timeTo;
    }
</script>
@endpush
@endsection
