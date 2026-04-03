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

{{-- 🎯 Advanced Filter Section (AJAX Driven) --}}
<div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm mb-8">
    <form action="{{ route('requests.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        
        <div>
            <label for="blood_group" class="block text-sm font-bold text-slate-700 mb-1">রক্তের গ্রুপ</label>
            <select name="blood_group" id="blood_group" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-700">
                <option value="">সব গ্রুপ</option>
                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                    <option value="{{ $bg }}" {{ request('blood_group') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="division_id" class="block text-sm font-bold text-slate-700 mb-1">বিভাগ</label>
            <select name="division_id" id="division_id" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-700">
                <option value="">বিভাগ নির্বাচন</option>
            </select>
        </div>

        <div>
            <label for="district_id" class="block text-sm font-bold text-slate-700 mb-1">জেলা</label>
            <select name="district_id" id="district_id" disabled class="w-full rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-700">
                <option value="">প্রথমে বিভাগ নির্বাচন করুন</option>
            </select>
        </div>

        <div>
            <label for="upazila_id" class="block text-sm font-bold text-slate-700 mb-1">উপজেলা/থানা</label>
            <select name="upazila_id" id="upazila_id" disabled class="w-full rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-700">
                <option value="">প্রথমে জেলা নির্বাচন করুন</option>
            </select>
        </div>

        <div class="md:col-span-4 flex justify-end gap-2 mt-2">
            @if(request()->hasAny(['blood_group', 'division_id', 'district_id', 'upazila_id']))
                <a href="{{ route('requests.index') }}" class="shrink-0 bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-2.5 rounded-lg font-extrabold transition-colors flex items-center justify-center">
                    ক্লিয়ার
                </a>
            @endif
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-8 py-2.5 rounded-lg font-extrabold shadow-sm transition-colors">
                খুঁজুন
            </button>
        </div>
    </form>
</div>

@if ($requests->isEmpty())
    <div class="rounded-2xl border border-slate-200 bg-white p-10 text-center">
        <div class="text-slate-900 font-extrabold text-lg">কোনো পেন্ডিং রিকোয়েস্ট পাওয়া যায়নি</div>
        <div class="text-slate-500 text-sm mt-2 font-medium">নতুন রিকোয়েস্ট তৈরি হলে এখানে দেখাবে। অথবা আপনার ফিল্টার পরিবর্তন করে দেখতে পারেন।</div>
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
                        
                        {{-- 🚀 Tabassum: Requester name with Verified Badge --}}
                        <div class="flex items-center gap-2 mt-2">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">প্রার্থনাকারী:</span>
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs font-bold text-slate-700">{{ $r->requester->name }}</span>
                                <x-verified-badge :user="$r->requester" />
                            </div>
                        </div>
                    </div>

                    <div class="shrink-0 px-3 py-1 rounded-lg bg-red-50 text-red-700 border border-red-100 font-extrabold">
                        {{ $r->blood_group?->value ?? (string) $r->blood_group }}
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
                        <div class="text-xs text-slate-500 font-semibold">লোকেশন</div>
                        <div class="font-extrabold text-slate-800 mt-1">{{ $r->upazila?->name ?? '-' }}, {{ $r->district?->name ?? '-' }}</div>
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

                {{-- ডাইনামিক অ্যাকশন বাটন সেকশন (আলিফের কাজ এখানে চলছে) --}}
                <div class="mt-5 flex flex-wrap gap-2">
                    @php
                        $myResponse = $r->responses->first();
                        $isOwner = auth()->check() && ((int) $r->requested_by === (int) auth()->id());
                    @endphp

                    @if (Route::has('requests.fulfill') && $isOwner && strtolower($r->status) !== 'fulfilled')
                        <form method="POST" action="{{ route('requests.fulfill', $r) }}">
                            @csrf
                            <button class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-extrabold text-sm shadow-sm transition">
                                Fulfilled
                            </button>
                        </form>
                    @endif

                    @if (Route::has('requests.respond') && !$isOwner && strtolower($r->status) !== 'fulfilled')
                        @if (!$myResponse)
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
                            <div class="flex items-center gap-2">
                                <span class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 font-extrabold text-sm">
                                    Declined
                                </span>

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

{{-- ⚙️ AJAX Script for Filter Location --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const divSelect = document.getElementById('division_id');
        const distSelect = document.getElementById('district_id');
        const upzSelect = document.getElementById('upazila_id');

        const oldDiv = "{{ request('division_id') }}";
        const oldDist = "{{ request('district_id') }}";
        const oldUpz = "{{ request('upazila_id') }}";

        fetch('/ajax/divisions')
            .then(res => res.json())
            .then(data => {
                data.forEach(div => {
                    const selected = (div.id == oldDiv) ? 'selected' : '';
                    divSelect.innerHTML += `<option value="${div.id}" ${selected}>${div.name}</option>`;
                });
                if(oldDiv) divSelect.dispatchEvent(new Event('change'));
            });

        divSelect.addEventListener('change', function() {
            if (!this.value) {
                distSelect.disabled = true; distSelect.innerHTML = '<option value="">প্রথমে বিভাগ নির্বাচন করুন</option>';
                upzSelect.disabled = true; upzSelect.innerHTML = '<option value="">প্রথমে জেলা নির্বাচন করুন</option>';
                return;
            }
            distSelect.disabled = true; distSelect.innerHTML = '<option value="">লোড হচ্ছে...</option>';
            fetch(`/ajax/districts/${this.value}`)
                .then(res => res.json())
                .then(data => {
                    distSelect.innerHTML = '<option value="">জেলা নির্বাচন করুন</option>';
                    distSelect.disabled = false;
                    data.forEach(dist => {
                        const selected = (dist.id == oldDist) ? 'selected' : '';
                        distSelect.innerHTML += `<option value="${dist.id}" ${selected}>${dist.name}</option>`;
                    });
                    if(oldDist) distSelect.dispatchEvent(new Event('change'));
                });
        });

        distSelect.addEventListener('change', function() {
            if (!this.value) {
                upzSelect.disabled = true; upzSelect.innerHTML = '<option value="">প্রথমে জেলা নির্বাচন করুন</option>';
                return;
            }
            upzSelect.disabled = true; upzSelect.innerHTML = '<option value="">লোড হচ্ছে...</option>';
            fetch(`/ajax/upazilas/${this.value}`)
                .then(res => res.json())
                .then(data => {
                    upzSelect.innerHTML = '<option value="">উপজেলা/থানা নির্বাচন করুন</option>';
                    upzSelect.disabled = false;
                    data.forEach(upz => {
                        const selected = (upz.id == oldUpz) ? 'selected' : '';
                        upzSelect.innerHTML += `<option value="${upz.id}" ${selected}>${upz.name}</option>`;
                    });
                });
        });
    });
</script>
@endsection