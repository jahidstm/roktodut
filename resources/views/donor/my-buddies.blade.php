@extends('layouts.donor-dashboard')

@section('title', 'আমার ব্লাড বাডি রোগী — রক্তদূত')

@section('content')
<div data-panel-id="my-buddies">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900" data-spa-title>আমার ব্লাড বাডি রোগী</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">আপনি যেসব দীর্ঘমেয়াদী রোগীর নিয়মিত রক্তদান করার প্রতিশ্রুতি দিয়েছেন</p>
        </div>
    </div>

    @if($buddySubscriptions->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-10 text-center scroll-reveal" data-scroll-reveal>
            <div class="w-20 h-20 bg-purple-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-3xl opacity-50">🎗️</span>
            </div>
            <h3 class="text-lg font-black text-slate-800">কোনো ব্লাড বাডি নেই</h3>
            <p class="text-sm font-medium text-slate-500 mt-2 max-w-md mx-auto leading-relaxed">
                বর্তমানে আপনি কোনো দীর্ঘমেয়াদী রোগীর ব্লাড বাডি হিসেবে যুক্ত নেই। সিস্টেম যখন আপনাকে কোনো থ্যালাসেমিয়া বা দীর্ঘমেয়াদী রোগীর জন্য সিলেক্ট করবে, তখন এখানে তার বিস্তারিত দেখা যাবে।
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($buddySubscriptions as $sub)
                <div class="bg-white rounded-2xl p-5 border {{ !$sub->is_active || $sub->is_paused ? 'border-slate-200' : 'border-purple-200 hover:border-purple-300' }} shadow-sm flex flex-col justify-between h-full relative overflow-hidden group transition-all scroll-reveal" data-scroll-reveal>
                    
                    @if($sub->is_paused || !$sub->is_active)
                        <div class="absolute inset-0 bg-slate-50/80 backdrop-blur-[1px] z-10 flex items-center justify-center">
                            <span class="bg-white px-4 py-2 rounded-xl text-sm font-bold text-slate-600 shadow-sm border border-slate-200">
                                {{ !$sub->is_active ? 'সাবস্ক্রিপশন নিষ্ক্রিয়' : 'সাময়িক বিরতিতে' }}
                            </span>
                        </div>
                    @endif
                    
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center text-lg font-black text-red-600 border border-red-100 shrink-0">
                                    {{ $sub->blood_group?->value ?? $sub->blood_group }}
                                </div>
                                <div>
                                    <h3 class="text-base font-black text-slate-900 leading-tight">{{ $sub->patient_name }}</h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[10px] font-bold text-purple-700 bg-purple-50 border border-purple-100 px-2 py-0.5 rounded-md">{{ $sub->condition_label }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-2 text-sm text-slate-600 font-medium bg-slate-50 rounded-xl p-4 border border-slate-100">
                            <p class="flex items-start gap-2.5">
                                <span class="mt-0.5 opacity-50">🏥</span> 
                                <span class="flex-1">{{ $sub->hospital?->display_name ?? 'হাসপাতাল নির্দিষ্ট নয়' }} <br><span class="text-xs text-slate-400">{{ $sub->district?->name }}</span></span>
                            </p>
                            <p class="flex items-center gap-2.5">
                                <span class="opacity-50">📅</span> 
                                <span>পরবর্তী রক্তদান: 
                                    @if($sub->next_needed_at)
                                        <strong class="{{ $sub->days_until_next <= 5 ? 'text-red-600' : 'text-slate-800' }}">{{ $sub->next_needed_at->format('d M, Y') }}</strong>
                                    @else
                                        --
                                    @endif
                                </span>
                            </p>
                        </div>
                        
                        @if($sub->notes_for_donor)
                            <div class="mt-4 text-xs font-medium text-amber-800 bg-amber-50 p-3 rounded-xl border border-amber-100 flex items-start gap-2">
                                <svg class="w-4 h-4 shrink-0 mt-0.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="leading-relaxed">{{ $sub->notes_for_donor }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
