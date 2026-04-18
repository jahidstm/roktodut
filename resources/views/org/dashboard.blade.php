@extends('layouts.app')

@section('title', 'অর্গানাইজেশন ড্যাশবোর্ড — রক্তদূত')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">অর্গানাইজেশন কমান্ড সেন্টার</h1>
            <p class="text-slate-500 font-medium mt-1">আপনার এরিয়ার ডোনারদের ভেরিফিকেশন এবং ম্যানেজমেন্ট ড্যাশবোর্ড।</p>
        </div>
        <div class="flex items-center gap-2 bg-blue-50 px-4 py-2 rounded-xl border border-blue-100 shadow-sm">
            <span class="relative flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
            </span>
            <span class="text-sm font-bold text-blue-700">অ্যাডমিন মোড অ্যাক্টিভ</span>
        </div>
    </div>

    <div id="org-command-shell">
    {{-- 🧭 Top Navigation Tabs --}}
    <div class="mb-8 flex overflow-x-auto bg-white border border-slate-200 rounded-2xl p-2 shadow-sm gap-2 whitespace-nowrap">
        <a href="{{ route('org.dashboard') }}" data-org-tab class="px-5 py-2.5 rounded-xl font-extrabold text-sm transition-all bg-slate-900 text-white shadow-sm">
            👥 মেম্বার ম্যানেজমেন্ট
        </a>
        <a href="{{ route('org.requests.index') }}" data-org-tab class="px-5 py-2.5 rounded-xl font-extrabold text-sm transition-all text-slate-600 hover:bg-slate-50 hover:text-red-600">
            🩸 রক্তের অনুরোধ (অর্গ জোন)
        </a>
        <a href="{{ route('org.camps.index') }}" data-org-tab class="px-5 py-2.5 rounded-xl font-extrabold text-sm transition-all text-slate-600 hover:bg-slate-50 hover:text-teal-600">
            🏕️ রক্তদান ক্যাম্প
        </a>
    </div>

    {{-- 📊 Analytics Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-5 rounded-2xl border border-emerald-100 shadow-sm flex flex-col justify-center">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">ভেরিফাইড মেম্বার</p>
            <h3 class="text-3xl font-black text-emerald-600 mt-1">{{ $stats['verified'] ?? 0 }}</h3>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-blue-100 shadow-sm flex flex-col justify-center">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">রেডি মেম্বার</p>
                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
            </div>
            <h3 class="text-3xl font-black text-blue-600 mt-1">{{ $stats['ready'] ?? 0 }}</h3>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-red-100 shadow-sm flex flex-col justify-center">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">অনলাইন ডোনেশন</p>
            <h3 class="text-3xl font-black text-red-600 mt-1">{{ $stats['online_donations'] ?? 0 }} <span class="text-xs font-bold text-slate-400">(ট্র্যাকড)</span></h3>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-teal-100 shadow-sm flex flex-col justify-center">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">ক্যাম্প ডোনেশন</p>
            <h3 class="text-3xl font-black text-teal-600 mt-1">{{ $stats['camp_donations'] ?? 0 }} <span class="text-xs font-bold text-slate-400">(লগড)</span></h3>
        </div>
    </div>
    
    {{-- District Chart --}}
    @if(isset($districtWiseMembers) && count($districtWiseMembers) > 0)
    <div class="mb-10 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
        <h3 class="text-lg font-extrabold text-slate-800 mb-4 flex items-center gap-2">📍 জেলা ভিত্তিক ভেরিফাইড মেম্বার</h3>
        <div class="flex flex-wrap gap-3">
            @foreach($districtWiseMembers as $district => $count)
                <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-2 flex items-center justify-between gap-4">
                    <span class="text-sm font-bold text-slate-700">{{ $district }}</span>
                    <span class="bg-blue-100 text-blue-700 text-xs font-black px-2 py-0.5 rounded-full">{{ $count }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- 🔍 Filters & Table --}}
    <div id="org-members-panel" class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">মেম্বার তালিকা</h2>
                <p class="text-xs text-slate-500 font-bold mt-1 uppercase tracking-tight">অর্গানাইজেশনের সকল ডোনারের স্ট্যাটাস</p>
            </div>
            
            {{-- Tabs / Filter --}}
            <div class="flex bg-white border border-slate-200 rounded-lg p-1 shadow-sm">
                <a href="{{ route('org.dashboard') }}" data-member-filter class="px-4 py-1.5 text-xs font-extrabold rounded-md transition-colors {{ !request('status') ? 'bg-slate-900 text-white' : 'text-slate-500 hover:text-slate-900' }}">সবাই</a>
                <a href="{{ route('org.dashboard', ['status' => 'pending']) }}" data-member-filter class="px-4 py-1.5 text-xs font-extrabold rounded-md transition-colors {{ request('status') === 'pending' ? 'bg-amber-100 text-amber-800' : 'text-slate-500 hover:text-slate-900' }}">পেন্ডিং</a>
                <a href="{{ route('org.dashboard', ['status' => 'verified']) }}" data-member-filter class="px-4 py-1.5 text-xs font-extrabold rounded-md transition-colors {{ request('status') === 'verified' ? 'bg-emerald-100 text-emerald-800' : 'text-slate-500 hover:text-slate-900' }}">ভেরিফাইড</a>
                <a href="{{ route('org.dashboard', ['status' => 'rejected']) }}" data-member-filter class="px-4 py-1.5 text-xs font-extrabold rounded-md transition-colors {{ request('status') === 'rejected' ? 'bg-red-100 text-red-800' : 'text-slate-500 hover:text-slate-900' }}">বাতিলকৃত</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-widest font-black">
                        <th class="px-6 py-4 border-b border-slate-100">ডোনারের নাম ও গ্রুপ</th>
                        <th class="px-6 py-4 border-b border-slate-100">যোগাযোগ</th>
                        <th class="px-6 py-4 border-b border-slate-100">স্ট্যাটাস</th>
                        <th class="px-6 py-4 border-b border-slate-100 text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($members as $user)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-extrabold text-slate-900">{{ $user->name }}</div>
                                <div class="text-xs font-black text-red-600 mt-0.5">{{ $user->blood_group ?? 'সেট করা নেই' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-700">{{ $user->phone ?? 'N/A' }}</div>
                                <div class="text-[10px] text-slate-400 font-bold">{{ $user->district?->name ?? 'লোকেশন নেই' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->nid_status === 'verified')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-700 uppercase">Verified</span>
                                @elseif($user->nid_status === 'pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-700 uppercase">Pending Review</span>
                                @elseif($user->nid_status === 'rejected')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-red-100 text-red-700 uppercase">Rejected</span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-600 uppercase">Not Submitted</span>
                                @endif
                            </td>
                            
                            {{-- 🎯 Fixed Action Column --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if($user->nid_status === 'pending')
                                    <div class="flex items-center justify-end gap-2">
                                        
                                        {{-- 👁️ Review Details Button --}}
                                        <a href="{{ route('org.donor.verify', $user->id) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white rounded-md font-bold transition-colors text-xs">
                                            রিভিউ করুন
                                        </a>

                                        {{-- Quick Approve Button --}}
                                        <form action="{{ route('org.members.verify', $user->id) }}" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিত যে এই ডোনার আপনাদের ক্লাবের ভেরিফাইড মেম্বার?');">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="verified">
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-emerald-100 text-emerald-700 hover:bg-emerald-600 hover:text-white rounded-md font-bold transition-colors text-xs">
                                                ✓
                                            </button>
                                        </form>

                                        {{-- Quick Reject Button --}}
                                        <form action="{{ route('org.members.verify', $user->id) }}" method="POST" onsubmit="let reason = prompt('বাতিল করার কারণ লিখুন:'); if(reason) { this.reject_reason.value = reason; return true; } return false;">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <input type="hidden" name="reject_reason" value="">
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-md font-bold transition-colors text-xs">
                                                ✕
                                            </button>
                                        </form>
                                    </div>
                                @elseif($user->nid_status === 'verified')
                                    <span class="inline-flex items-center text-emerald-600 font-extrabold text-sm">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        Accepted
                                    </span>
                                @else
                                    <div class="flex items-center justify-end gap-1 flex-wrap w-48 ml-auto">
                                        <span class="text-slate-400 font-bold text-xs uppercase bg-slate-50 px-2 py-1 rounded">Rejected</span>
                                        @if($user->rejected_reason)
                                            <p class="text-[10px] text-red-500 text-right w-full font-medium" title="কারণ">কারণ: {{ Str::limit($user->rejected_reason, 30) }}</p>
                                        @endif
                                        @if($user->reviewed_by)
                                            <p class="text-[10px] text-slate-400 text-right w-full" title="Reviewed By">রিভিউয়ার আইডি #{{ $user->reviewed_by }}</p>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-bold">
                                এই ফিল্টারে কোনো মেম্বার পাওয়া যায়নি।
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if(method_exists($members, 'hasPages') && $members->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $members->links() }}
            </div>
        @endif
    </div>
    </div>
</div>
@endsection
