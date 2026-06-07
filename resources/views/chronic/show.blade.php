@extends('layouts.app')

@section('title', $subscription->patient_name . ' - দীর্ঘমেয়াদী সাবস্ক্রিপশন')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('chronic.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-slate-500 hover:text-slate-800 mb-3 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                সকল সাবস্ক্রিপশন
            </a>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                {{ $subscription->patient_name }}
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-{{ $subscription->condition_color }}-100 text-{{ $subscription->condition_color }}-700">
                    {{ $subscription->condition_label }}
                </span>
            </h1>
        </div>
        
        <div class="flex items-center gap-2">
            @if($subscription->is_active)
                <a href="{{ route('chronic.edit', $subscription->id) }}" class="bg-white border border-slate-200 text-slate-700 font-bold py-2 px-4 rounded-xl hover:bg-slate-50 transition shadow-sm">
                    এডিট করুন
                </a>
            @endif
            
            <div class="px-3 py-2 rounded-xl text-sm font-bold shadow-sm flex items-center gap-2 
                {{ !$subscription->is_active ? 'bg-slate-100 text-slate-600' : ($subscription->is_paused ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">
                <span class="w-2.5 h-2.5 rounded-full {{ !$subscription->is_active ? 'bg-slate-400' : ($subscription->is_paused ? 'bg-amber-500 animate-pulse' : 'bg-emerald-500 animate-pulse') }}"></span>
                {{ !$subscription->is_active ? 'নিষ্ক্রিয়' : ($subscription->is_paused ? 'সাময়িক বিরতি' : 'সচল') }}
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 text-sm font-bold flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Left Column: Details & Schedule --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Details Card --}}
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center gap-4">
                    <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center text-2xl font-black text-red-600 border border-red-100 shrink-0">
                        {{ $subscription->blood_group?->value ?? $subscription->blood_group }}
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-800">রোগীর তথ্য</h2>
                        <p class="text-sm font-medium text-slate-500">
                            {{ $subscription->component_type instanceof \App\Enums\BloodComponentType ? $subscription->component_type->label() : $subscription->component_type }} • 
                            প্রতিবারে {{ $subscription->bags_needed }} ব্যাগ •
                            {{ $subscription->urgency instanceof \App\Enums\UrgencyLevel ? $subscription->urgency->label() : $subscription->urgency }}
                        </p>
                    </div>
                </div>
                
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">হাসপাতাল ও ঠিকানা</p>
                        <p class="text-sm font-bold text-slate-800">{{ $subscription->hospital?->display_name ?? 'হাসপাতাল নির্দিষ্ট নয়' }}</p>
                        <p class="text-xs font-medium text-slate-600 mt-1">{{ $subscription->address }}</p>
                        <p class="text-xs font-medium text-slate-500 mt-0.5">{{ $subscription->upazila?->name }}, {{ $subscription->district?->name }}</p>
                    </div>
                    
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">যোগাযোগ</p>
                        <p class="text-sm font-bold text-slate-800">{{ $subscription->contact_name ?? 'নাম নেই' }}</p>
                        <p class="text-xs font-bold text-blue-600 mt-1 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            {{ $subscription->contact_number }}
                        </p>
                        @if($subscription->is_phone_hidden)
                            <span class="inline-flex mt-1 items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">পাবলিকলি লুকানো</span>
                        @endif
                    </div>
                </div>

                @if($subscription->notes || $subscription->notes_for_donor)
                    <div class="p-6 border-t border-slate-100 bg-slate-50/50 space-y-4">
                        @if($subscription->notes)
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">পাবলিক নোট</p>
                                <p class="text-sm text-slate-700 italic">"{{ $subscription->notes }}"</p>
                            </div>
                        @endif
                        @if($subscription->notes_for_donor)
                            <div>
                                <p class="text-[10px] font-bold text-amber-500 uppercase tracking-widest mb-1 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    ব্লাড বাডিদের জন্য নির্দেশিকা
                                </p>
                                <p class="text-sm font-semibold text-amber-900 bg-amber-50 p-3 rounded-xl border border-amber-100">{{ $subscription->notes_for_donor }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Dispatched Requests Timeline --}}
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-6">
                <h2 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    সাম্প্রতিক অটো-রিকোয়েস্ট হিস্ট্রি
                </h2>

                @if($subscription->dispatchedRequests->isEmpty())
                    <div class="text-center py-6 bg-slate-50 rounded-2xl border border-slate-100">
                        <p class="text-sm font-medium text-slate-500">এখনো কোনো অটো-রিকোয়েস্ট তৈরি হয়নি। নির্ধারিত তারিখে সিস্টেম স্বয়ংক্রিয়ভাবে রিকোয়েস্ট তৈরি করবে।</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($subscription->dispatchedRequests as $req)
                            <a href="{{ route('requests.show', $req->id) }}" class="block bg-slate-50 hover:bg-blue-50 rounded-2xl p-4 border border-slate-100 hover:border-blue-200 transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center font-bold text-sm text-slate-700 shadow-sm border border-slate-200">
                                            {{ \Carbon\Carbon::parse($req->needed_at)->format('d M') }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900">রিকোয়েস্ট ID: #{{ $req->id }}</p>
                                            <p class="text-xs font-medium text-slate-500">{{ $req->created_at->diffForHumans() }} তৈরি হয়েছে</p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest
                                        {{ $req->status === 'fulfilled' ? 'bg-emerald-100 text-emerald-700' : ($req->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-600') }}">
                                        {{ $req->status }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        {{-- Right Column: Buddy Pool & Upcoming --}}
        <div class="space-y-6">
            
            {{-- Buddy Pool Panel --}}
            <div class="bg-white rounded-3xl border border-red-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-red-100 bg-red-50/50">
                    <h2 class="text-lg font-black text-slate-800 flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span class="text-xl">🤝</span> ব্লাড বাডি পুল
                        </span>
                        <span class="text-xs font-black text-red-600 bg-red-100 px-2.5 py-1 rounded-full">{{ $subscription->buddies->where('is_active', true)->count() }}/4</span>
                    </h2>
                    <p class="text-xs text-slate-600 mt-1 font-medium">সিস্টেম এই ৪ জনকে খুঁজবে আপনার রিকোয়েস্টের জন্য।</p>
                </div>
                
                <div class="p-5">
                    @if($subscription->buddies->where('is_active', true)->count() > 0)
                        <div class="space-y-3">
                            @foreach($subscription->buddies->where('is_active', true)->sortBy('position') as $buddy)
                                <div class="flex items-center justify-between bg-slate-50 border border-slate-100 rounded-xl p-3">
                                    <div class="flex items-center gap-3">
                                        @if($buddy->donor->profile_image)
                                            <img class="h-10 w-10 rounded-full object-cover border border-slate-200" src="{{ asset('storage/'.$buddy->donor->profile_image) }}" alt="">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center text-sm font-bold text-slate-600 border border-slate-300">
                                                {{ mb_substr($buddy->donor->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-bold text-slate-900 leading-tight">{{ $buddy->donor->name }}</p>
                                            <p class="text-[10px] font-medium text-slate-500 mt-0.5">র‍্যাঙ্ক #{{ $buddy->position }}</p>
                                        </div>
                                    </div>
                                    
                                    <form action="{{ route('chronic.buddies.remove', [$subscription->id, $buddy->id]) }}" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিত? সিস্টেম নতুন কাউকে খুঁজবে।');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition" title="রিমুভ করুন">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 bg-slate-50 rounded-2xl border border-slate-100">
                            <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <p class="text-sm font-bold text-slate-700">বাডি খোঁজা হচ্ছে...</p>
                            <p class="text-xs text-slate-500 mt-1">সিস্টেম আপনার এলাকার সেরা ডোনারদের খুঁজছে।</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Upcoming Schedule --}}
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-6">
                <h2 class="text-lg font-black text-slate-800 mb-4">পরবর্তী শিডিউল</h2>
                
                <div class="mb-5 bg-slate-50 rounded-2xl p-4 border border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">ফ্রিকোয়েন্সি</p>
                        <p class="text-sm font-bold text-slate-700">প্রতি {{ $subscription->cadence_days }} দিন পর</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">অটো-রিকোয়েস্ট</p>
                        <p class="text-sm font-bold text-slate-700">{{ $subscription->lead_time_days }} দিন আগে</p>
                    </div>
                </div>

                <div class="relative pl-4 border-l-2 border-slate-200 space-y-5">
                    @foreach($subscription->upcoming_dates as $index => $date)
                        <div class="relative">
                            <div class="absolute -left-[21px] top-1 w-2.5 h-2.5 rounded-full {{ $index === 0 ? 'bg-red-500 ring-4 ring-red-100' : 'bg-slate-300' }}"></div>
                            <p class="text-sm font-black {{ $index === 0 ? 'text-red-600' : 'text-slate-700' }}">{{ $date->format('d F, Y') }}</p>
                            @if($index === 0 && $subscription->days_until_next !== null)
                                <p class="text-xs font-bold text-slate-500 mt-0.5">
                                    {{ $subscription->days_until_next === 0 ? 'আজই' : 'আর ' . $subscription->days_until_next . ' দিন বাকি' }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
