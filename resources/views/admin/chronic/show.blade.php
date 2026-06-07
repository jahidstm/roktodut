@extends('layouts.admin-dashboard')

@section('title', 'সাবস্ক্রিপশন বিস্তারিত — রক্তদূত')

@section('content')
<div data-panel-id="chronic">
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.chronic.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-slate-500 hover:text-slate-800 mb-3 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                ব্যাক
            </a>
            <h1 class="text-2xl font-black text-slate-900">
                সাবস্ক্রিপশন বিস্তারিত: {{ $subscription->patient_name }}
            </h1>
        </div>
        
        <form action="{{ route('admin.chronic.toggle', $subscription->id) }}" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিত?');">
            @csrf
            @if($subscription->is_active)
                <input type="hidden" name="action" value="deactivate">
                <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-100 font-bold py-2 px-4 rounded-xl transition">
                    Force Deactivate
                </button>
            @else
                <input type="hidden" name="action" value="activate">
                <button type="submit" class="bg-emerald-50 text-emerald-600 hover:bg-emerald-100 font-bold py-2 px-4 rounded-xl transition">
                    Force Activate
                </button>
            @endif
        </form>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 text-sm font-bold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 mb-6">
        <h2 class="text-lg font-black text-slate-800 mb-4">বেসিক তথ্য</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-slate-500 font-bold uppercase text-[10px]">রোগীর নাম</p>
                <p class="font-semibold text-slate-900">{{ $subscription->patient_name }}</p>
            </div>
            <div>
                <p class="text-slate-500 font-bold uppercase text-[10px]">രক্ত</p>
                <p class="font-semibold text-slate-900">{{ $subscription->blood_group?->value ?? $subscription->blood_group }}</p>
            </div>
            <div>
                <p class="text-slate-500 font-bold uppercase text-[10px]">ধরন</p>
                <p class="font-semibold text-slate-900">{{ $subscription->condition_label }}</p>
            </div>
            <div>
                <p class="text-slate-500 font-bold uppercase text-[10px]">অ্যাকাউন্ট</p>
                <p class="font-semibold text-slate-900">{{ $subscription->user->name }}</p>
            </div>
            <div>
                <p class="text-slate-500 font-bold uppercase text-[10px]">স্ট্যাটাস</p>
                <p class="font-semibold text-slate-900">
                    @if(!$subscription->is_active) নিষ্ক্রিয় @elseif($subscription->is_paused) বিরতি @else সক্রিয় @endif
                </p>
            </div>
            @if($subscription->status_reason)
                <div class="col-span-2">
                    <p class="text-slate-500 font-bold uppercase text-[10px]">স্ট্যাটাস কারণ (ML Dataset)</p>
                    <p class="font-semibold text-slate-900">{{ $subscription->status_reason }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
</div>
@endsection
