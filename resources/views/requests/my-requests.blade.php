@extends('layouts.app')

@section('title', 'আমার রিকোয়েস্ট — রক্তদূত')

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

    <div class="flex items-start justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight">আমার রিকোয়েস্ট</h1>
            <p class="text-slate-500 font-medium mt-1">আপনার তৈরি করা সকল রিকোয়েস্ট (Expired সহ)</p>
        </div>

        <a href="{{ route('requests.create') }}"
           class="shrink-0 inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg font-extrabold shadow-sm shadow-red-200">
            নতুন রিকোয়েস্ট
        </a>
    </div>

    @if($requests->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-10 text-center">
            <div class="text-slate-900 font-extrabold text-lg">আপনার কোনো রিকোয়েস্ট পাওয়া যায়নি</div>
            <div class="text-slate-500 text-sm mt-2 font-medium">রিকোয়েস্ট তৈরি করলে এখানে দেখতে পাবেন।</div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach ($requests as $req)
                @php
                    $currentStatus = strtolower((string) $req->status);
                    $isExpiredStatus = $currentStatus === 'expired';
                    $isPendingLikeStatus = in_array($currentStatus, ['pending', 'in_progress'], true);
                    $isPastNeededAt = $req->needed_at && \Carbon\Carbon::parse($req->needed_at)->isPast();
                    $canRenew = $isExpiredStatus || ($isPastNeededAt && $isPendingLikeStatus);

                    $statusMap = [
                        'pending' => ['label' => 'Pending', 'cls' => 'bg-amber-100 text-amber-800 border-amber-200'],
                        'in_progress' => ['label' => 'In Progress', 'cls' => 'bg-indigo-100 text-indigo-800 border-indigo-200'],
                        'expired' => ['label' => 'Expired', 'cls' => 'bg-rose-100 text-rose-800 border-rose-200'],
                        'fulfilled' => ['label' => 'Fulfilled', 'cls' => 'bg-emerald-100 text-emerald-800 border-emerald-200'],
                    ];
                    $statusInfo = $statusMap[$currentStatus] ?? ['label' => strtoupper((string) $req->status), 'cls' => 'bg-slate-100 text-slate-700 border-slate-200'];
                @endphp

                <article x-data="{ renewOpen: false }" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="text-lg font-black tracking-tight text-slate-900 truncate">
                                {{ $req->blood_group?->value ?? (string) $req->blood_group }} রক্ত প্রয়োজন
                            </h3>
                            <p class="mt-1 text-sm font-semibold text-slate-800 truncate">{{ $req->hospital ?: 'হাসপাতাল উল্লেখ নেই' }}</p>
                            <p class="text-sm text-slate-600 truncate">{{ $req->district?->name ?? '-' }}{{ $req->upazila?->name ? ' · ' . $req->upazila->name : '' }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-black {{ $statusInfo['cls'] }}">
                            {{ $statusInfo['label'] }}
                        </span>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                            <p class="text-[11px] font-semibold text-slate-500">প্রয়োজন</p>
                            <p class="mt-0.5 text-sm font-bold text-slate-800">
                                {{ $req->needed_at ? $req->needed_at->format('d M, Y h:i A') : 'ASAP' }}
                            </p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                            <p class="text-[11px] font-semibold text-slate-500">পোস্ট</p>
                            <p class="mt-0.5 text-sm font-bold text-slate-800">{{ \App\Support\BanglaDate::relative($req->created_at) }}</p>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        @if($canRenew)
                            <button type="button"
                                    @click="renewOpen = true"
                                    class="inline-flex h-10 items-center justify-center rounded-xl bg-red-600 px-4 text-sm font-black text-white transition hover:bg-red-700">
                                রিনিউ করুন
                            </button>
                        @endif

                        <a href="{{ route('requests.show', $req->id) }}"
                           class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                            View Details
                        </a>
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

                                <form method="POST" action="{{ route('requests.renew', $req->id) }}" class="space-y-4" data-renew-modal>
                                    @csrf

                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 mb-1.5">কবে রক্ত লাগবে</label>
                                        <input type="datetime-local"
                                               name="needed_at"
                                               value="{{ old('needed_at', optional($req->needed_at)->format('Y-m-d\TH:i')) }}"
                                               class="renew-needed-at w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500" required>
                                        @error('needed_at')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 mb-1.5">জরুরিতা</label>
                                        <select name="urgency" class="renew-urgency w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500" required>
                                            @foreach (\App\Enums\UrgencyLevel::cases() as $case)
                                                <option value="{{ $case->value }}" @selected(old('urgency', $req->urgency?->value ?? (string) $req->urgency) === $case->value)>
                                                    {{ $case->label() }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('urgency')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                                        <p class="renew-threshold-note mt-1 text-xs font-semibold text-amber-700 hidden"></p>
                                    </div>

                                    <div class="pt-2 flex items-center justify-end gap-2">
                                        <button type="button"
                                                @click="renewOpen = false"
                                                class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">
                                            বাতিল
                                        </button>
                                        <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-xs font-black text-white hover:bg-red-700">
                                            রিনিউ সাবমিট
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </article>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $requests->links() }}
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
});
</script>
@endsection
