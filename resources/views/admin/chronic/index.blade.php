@extends('layouts.app')

@section('title', 'Admin - Chronic Patients')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                দীর্ঘমেয়াদী রোগী ব্যবস্থাপনা
            </h1>
            <p class="text-slate-500 font-medium mt-1">সিস্টেমের সমস্ত Chronic Subscriptions এর তালিকা।</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 text-sm font-bold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        
        {{-- Filters (Simplified) --}}
        <div class="p-4 border-b border-slate-100 bg-slate-50">
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <select name="status" class="border-slate-200 rounded-lg text-sm font-semibold focus:ring-red-500">
                    <option value="">সকল স্ট্যাটাস</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>সক্রিয় (Active)</option>
                    <option value="paused" {{ request('status') === 'paused' ? 'selected' : '' }}>বিরতিতে (Paused)</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>নিষ্ক্রিয় (Inactive)</option>
                </select>
                <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-slate-700">ফিল্টার</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-500 uppercase tracking-widest">
                    <tr>
                        <th class="px-6 py-4">রোগী ও অবস্থা</th>
                        <th class="px-6 py-4">এলাকা</th>
                        <th class="px-6 py-4">শিডিউল</th>
                        <th class="px-6 py-4">স্ট্যাটাস</th>
                        <th class="px-6 py-4 text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($subscriptions as $sub)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-red-50 border border-red-100 flex items-center justify-center font-bold text-red-600 shrink-0">
                                        {{ $sub->blood_group?->value ?? $sub->blood_group }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $sub->patient_name }}</p>
                                        <p class="text-xs font-semibold text-slate-500">{{ $sub->condition_label }} • {{ $sub->user->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-700">{{ $sub->district?->name }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-700">প্রতি {{ $sub->cadence_days }} দিন</p>
                                @if($sub->next_needed_at)
                                    <p class="text-[10px] font-bold {{ $sub->days_until_next <= 5 ? 'text-red-500' : 'text-slate-500' }}">পরবর্তী: {{ $sub->next_needed_at->format('d M') }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if(!$sub->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-600">নিষ্ক্রিয়</span>
                                @elseif($sub->is_paused)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-700">বিরতিতে</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-700">সক্রিয়</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.chronic.show', $sub->id) }}" class="text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-lg text-xs font-bold transition">বিস্তারিত</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500 font-medium">কোনো সাবস্ক্রিপশন পাওয়া যায়নি।</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-slate-100">
            {{ $subscriptions->links() }}
        </div>
    </div>
</div>
@endsection
