@extends('layouts.app')

@section('title', 'অর্গানাইজেশন ড্যাশবোর্ড — রক্তদূত')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">অর্গানাইজেশন প্যানেল</h1>
            <p class="text-slate-500 font-medium mt-1">আপনার এরিয়ার ডোনারদের ভেরিফিকেশন এবং ম্যানেজমেন্ট ড্যাশবোর্ড।</p>
        </div>
        <div class="flex items-center gap-2 bg-blue-50 px-4 py-2 rounded-xl border border-blue-100 shadow-sm">
            <span class="relative flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
            </span>
            <span class="text-sm font-bold text-blue-700">অর্গানাইজেশন অ্যাডমিন মোড অ্যাক্টিভ</span>
        </div>
    </div>

    {{-- 📊 Analytics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="p-4 bg-slate-100 text-slate-600 rounded-xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">মোট মেম্বার</p>
                <h3 class="text-3xl font-black text-slate-900">{{ $stats['total'] }} <span class="text-base text-slate-400 font-medium">জন</span></h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="p-4 bg-amber-100 text-amber-600 rounded-xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">অপেক্ষমাণ ভেরিফিকেশন</p>
                <h3 class="text-3xl font-black text-slate-900">{{ $stats['pending'] }} <span class="text-base text-slate-400 font-medium">জন</span></h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="p-4 bg-emerald-100 text-emerald-600 rounded-xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">মোট ভেরিফাইড</p>
                <h3 class="text-3xl font-black text-slate-900">{{ $stats['approved'] }} <span class="text-base text-slate-400 font-medium">জন</span></h3>
            </div>
        </div>
    </div>

    {{-- 🔍 Filters & Table --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">মেম্বার তালিকা</h2>
                <p class="text-xs text-slate-500 font-bold mt-1 uppercase tracking-tight">অর্গানাইজেশনের সকল ডোনারের স্ট্যাটাস</p>
            </div>
            
            {{-- Tabs / Filter --}}
            <div class="flex bg-white border border-slate-200 rounded-lg p-1 shadow-sm">
                <a href="{{ route('org.dashboard') }}" class="px-4 py-1.5 text-xs font-extrabold rounded-md transition-colors {{ !$request->status ? 'bg-slate-900 text-white' : 'text-slate-500 hover:text-slate-900' }}">সবাই</a>
                <a href="{{ route('org.dashboard', ['status' => 'pending']) }}" class="px-4 py-1.5 text-xs font-extrabold rounded-md transition-colors {{ $request->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'text-slate-500 hover:text-slate-900' }}">পেন্ডিং</a>
                <a href="{{ route('org.dashboard', ['status' => 'approved']) }}" class="px-4 py-1.5 text-xs font-extrabold rounded-md transition-colors {{ $request->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'text-slate-500 hover:text-slate-900' }}">অ্যাপ্রুভড</a>
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
                                @if($user->nid_status === 'approved')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-700 uppercase">Verified</span>
                                @elseif($user->nid_status === 'pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-700 uppercase">Pending Review</span>
                                @elseif($user->nid_status === 'rejected')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-red-100 text-red-700 uppercase">Rejected</span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-600 uppercase">Not Submitted</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if($user->nid_status === 'pending')
                                    <div class="flex items-center justify-end gap-2">
                                        
                                        {{-- Approve Button --}}
                                        <form action="{{ route('org.members.verify', $user->id) }}" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিত যে এই ডোনার আপনাদের ক্লাবের ভেরিফাইড মেম্বার?');">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-emerald-100 text-emerald-700 hover:bg-emerald-600 hover:text-white rounded-md font-bold transition-colors text-xs">
                                                ✓ Approve
                                            </button>
                                        </form>

                                        {{-- Reject Button --}}
                                        <form action="{{ route('org.members.verify', $user->id) }}" method="POST" onsubmit="return confirm('আপনি কি এই রিকোয়েস্টটি বাতিল করতে চান?');">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-md font-bold transition-colors text-xs">
                                                ✕ Reject
                                            </button>
                                        </form>

                                    </div>

                                @elseif($user->nid_status === 'approved')
                                    <span class="inline-flex items-center text-emerald-600 font-extrabold text-sm">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Verified
                                    </span>

                                @else
                                    <span class="text-slate-400 font-bold text-sm">Rejected</span>
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
        @if($members->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $members->links() }}
            </div>
        @endif
    </div>
</div>
@endsection