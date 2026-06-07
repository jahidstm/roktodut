@extends('layouts.app')

@section('title', 'আমার দীর্ঘমেয়াদী রক্তদান পরিকল্পনা — রক্তদূত')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                আমার দীর্ঘমেয়াদী সাবস্ক্রিপশন
            </h1>
            <p class="text-slate-500 font-medium text-sm mt-1">থ্যালাসেমিয়া, ডায়ালাইসিস বা অন্য রোগীদের জন্য নিয়মিত রক্তের অটো-রিকোয়েস্ট।</p>
        </div>
        <a href="{{ route('chronic.create') }}" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-5 rounded-xl transition shadow-md hover:shadow-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            নতুন সাবস্ক্রিপশন
        </a>
    </div>

    @if($subscriptions->isEmpty())
        <div class="bg-white rounded-[2rem] border border-slate-200 p-12 text-center shadow-sm">
            <div class="w-24 h-24 mx-auto bg-slate-50 rounded-full flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <h3 class="text-xl font-black text-slate-800 mb-2">কোনো সাবস্ক্রিপশন নেই</h3>
            <p class="text-slate-500 font-medium max-w-md mx-auto mb-6">আপনার যদি এমন কোনো রোগী থাকে যার নিয়মিত রক্তের প্রয়োজন হয়, তবে একটি সাবস্ক্রিপশন তৈরি করুন। সিস্টেম স্বয়ংক্রিয়ভাবে সময়মতো রিকোয়েস্ট তৈরি করবে।</p>
            <a href="{{ route('chronic.create') }}" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 px-6 rounded-xl transition">
                তৈরি করুন
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($subscriptions as $sub)
                <div class="bg-white rounded-3xl border {{ $sub->is_active ? ($sub->is_paused ? 'border-amber-200 shadow-amber-100' : 'border-red-100 shadow-red-50') : 'border-slate-200 opacity-70' }} overflow-hidden shadow-lg transition hover:shadow-xl flex flex-col h-full relative">
                    
                    {{-- Status Banner --}}
                    @if(!$sub->is_active)
                        <div class="absolute inset-0 bg-slate-50/50 backdrop-blur-[2px] z-10 pointer-events-none"></div>
                        <div class="bg-slate-100 border-b border-slate-200 px-5 py-2.5 flex items-center justify-between z-20">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-slate-400"></span> নিষ্ক্রিয়
                            </span>
                        </div>
                    @elseif($sub->is_paused)
                        <div class="bg-amber-50 border-b border-amber-100 px-5 py-2.5 flex items-center justify-between">
                            <span class="text-xs font-bold text-amber-700 uppercase tracking-widest flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span> সাময়িক বিরতি
                            </span>
                            @if($sub->paused_until)
                                <span class="text-[10px] font-bold text-amber-600 bg-amber-100 px-2 py-0.5 rounded-md">{{ $sub->paused_until->format('d M') }} পর্যন্ত</span>
                            @endif
                        </div>
                    @else
                        <div class="bg-red-50/50 border-b border-red-50 px-5 py-2.5 flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-600 uppercase tracking-widest flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> সচল
                            </span>
                            @if($sub->days_until_next !== null)
                                <span class="text-[10px] font-bold {{ $sub->days_until_next <= 5 ? 'text-red-600 bg-red-100' : 'text-blue-600 bg-blue-100' }} px-2 py-0.5 rounded-md">
                                    পরবর্তী: {{ $sub->days_until_next === 0 ? 'আজই' : $sub->days_until_next . ' দিন বাকি' }}
                                </span>
                            @endif
                        </div>
                    @endif

                    <div class="p-6 flex-1 flex flex-col z-20">
                        <div class="flex items-start justify-between gap-4 mb-4">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center text-xl font-black text-red-600 border border-red-100 shrink-0">
                                    {{ $sub->blood_group?->value ?? $sub->blood_group }}
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-slate-900 leading-tight">{{ $sub->patient_name }}</h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-{{ $sub->condition_color }}-100 text-{{ $sub->condition_color }}-700">
                                            {{ $sub->condition_label }}
                                        </span>
                                        <span class="text-xs font-medium text-slate-500">প্রতি {{ $sub->cadence_days }} দিন পর</span>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Dropdown Actions --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" @click.away="open = false" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-full transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                </button>
                                <div x-show="open" x-transition class="absolute right-0 mt-1 w-48 bg-white rounded-xl shadow-xl border border-slate-100 py-1 z-50" style="display: none;">
                                    <a href="{{ route('chronic.show', $sub->id) }}" class="block px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">বিস্তারিত দেখুন</a>
                                    @if($sub->is_active)
                                        <a href="{{ route('chronic.edit', $sub->id) }}" class="block px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">এডিট করুন</a>
                                        <hr class="my-1 border-slate-100">
                                        @if($sub->is_paused)
                                            <form action="{{ route('chronic.pause', $sub->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="action" value="resume">
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm font-semibold text-emerald-600 hover:bg-slate-50">▶️ আবার চালু করুন</button>
                                            </form>
                                        @else
                                            <button @click="$dispatch('open-pause-modal', {{ $sub->id }})" class="w-full text-left px-4 py-2 text-sm font-semibold text-amber-600 hover:bg-slate-50">⏸️ বিরতি দিন</button>
                                        @endif
                                        <hr class="my-1 border-slate-100">
                                        <form action="{{ route('chronic.destroy', $sub->id) }}" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিত? এটি একেবারে বন্ধ হয়ে যাবে।');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full text-left px-4 py-2 text-sm font-semibold text-red-600 hover:bg-slate-50">🗑️ ডিলিট/বন্ধ করুন</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mt-2 mb-4 bg-slate-50 rounded-xl p-3 text-xs font-medium text-slate-600 flex items-start gap-2">
                            <span class="shrink-0 mt-0.5">🏥</span>
                            <span>{{ $sub->hospital?->display_name ?? 'হাসপাতাল নির্দিষ্ট নয়' }} ({{ $sub->district?->name }})</span>
                        </div>

                        {{-- Buddies --}}
                        <div class="mt-auto pt-4 border-t border-slate-100">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">আপনার ব্লাড বাডি ({{ $sub->buddies->where('is_active', true)->count() }}/4)</p>
                            @if($sub->buddies->where('is_active', true)->count() > 0)
                                <div class="flex -space-x-2 overflow-hidden">
                                    @foreach($sub->buddies->where('is_active', true) as $buddy)
                                        @if($buddy->donor->profile_image)
                                            <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white object-cover" src="{{ asset('storage/'.$buddy->donor->profile_image) }}" alt="{{ $buddy->donor->name }}" title="{{ $buddy->donor->name }}">
                                        @else
                                            <div class="inline-flex h-8 w-8 rounded-full ring-2 ring-white bg-slate-200 items-center justify-center text-xs font-bold text-slate-600" title="{{ $buddy->donor->name }}">
                                                {{ mb_substr($buddy->donor->name, 0, 1) }}
                                            </div>
                                        @endif
                                    @endforeach
                                    @if($sub->buddies->where('is_active', true)->count() < 4)
                                        <div class="inline-flex h-8 w-8 rounded-full ring-2 ring-white border border-dashed border-slate-300 items-center justify-center text-slate-400 bg-white" title="খোঁজা হচ্ছে...">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-xs font-medium text-amber-600 bg-amber-50 px-3 py-1.5 rounded-lg inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    সিস্টেম বাডি খুঁজছে...
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <a href="{{ route('chronic.show', $sub->id) }}" class="absolute inset-0 z-10"></a>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Pause Modal Component --}}
<div x-data="{ 
        isOpen: false, 
        subId: null,
        init() {
            window.addEventListener('open-pause-modal', (e) => {
                this.subId = e.detail;
                this.isOpen = true;
            });
        }
    }" 
    x-show="isOpen" 
    class="relative z-[100]" 
    style="display: none;" 
    aria-labelledby="modal-title" 
    role="dialog" 
    aria-modal="true">
    
    <div x-show="isOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="isOpen" 
                 x-transition.translate.y.bottom 
                 @click.away="isOpen = false"
                 class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                
                <form :action="`/my-subscriptions/${subId}/pause`" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="pause">
                    
                    <div class="bg-white px-6 pb-6 pt-8">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-black text-slate-900" id="modal-title">সাময়িক বিরতি (Pause)</h3>
                            <button type="button" @click="isOpen = false" class="text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 rounded-full p-2 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <p class="text-sm font-medium text-slate-500">যদি আগামী কয়েক সপ্তাহ/মাসের জন্য রক্তের প্রয়োজন না হয় (যেমন রোগী ছুটি নিয়েছেন বা অসুস্থ), তবে আপনি সাবস্ক্রিপশনটি সাময়িক বন্ধ রাখতে পারেন।</p>
                            
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">কত তারিখ পর্যন্ত বন্ধ থাকবে? (ঐচ্ছিক)</label>
                                <input type="date" name="paused_until" min="{{ date('Y-m-d') }}" class="w-full border-slate-200 rounded-xl focus:ring-amber-500 focus:border-amber-500">
                                <p class="text-xs text-slate-500 mt-1">ফাঁকা রাখলে অনির্দিষ্টকালের জন্য বন্ধ থাকবে।</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">কারণ (ঐচ্ছিক)</label>
                                <input type="text" name="reason" placeholder="যেমন: রোগী গ্রামের বাড়িতে গিয়েছেন" class="w-full border-slate-200 rounded-xl focus:ring-amber-500 focus:border-amber-500">
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3 rounded-b-3xl">
                        <button type="submit" class="inline-flex w-full justify-center rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-amber-600 sm:w-auto transition">
                            বিরতি দিন
                        </button>
                        <button type="button" @click="isOpen = false" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto transition">
                            বাতিল
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
