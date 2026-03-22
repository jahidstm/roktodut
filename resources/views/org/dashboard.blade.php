@extends('layouts.app')

@section('title', 'অর্গানাইজেশন ড্যাশবোর্ড — রক্তদূত')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">অর্গানাইজেশন প্যানেল</h1>
            <p class="text-slate-500 font-medium mt-1">আপনার এরিয়ার ডোনারদের ভেরিফিকেশন এবং ম্যানেজমেন্ট ড্যাশবোর্ড।</p>
        </div>
        <div class="flex items-center gap-2 bg-blue-50 px-4 py-2 rounded-xl border border-blue-100">
            <span class="relative flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
            </span>
            <span class="text-sm font-bold text-blue-700">অর্গানাইজেশন অ্যাডমিন মোড অ্যাক্টিভ</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="p-4 bg-amber-100 text-amber-600 rounded-xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-500 uppercase tracking-wide">অপেক্ষমাণ ভেরিফিকেশন</p>
                <h3 class="text-3xl font-black text-slate-900">{{ $totalPending }} <span class="text-base text-slate-400 font-medium">জন</span></h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="p-4 bg-emerald-100 text-emerald-600 rounded-xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-500 uppercase tracking-wide">মোট ভেরিফাইড ডোনার</p>
                <h3 class="text-3xl font-black text-slate-900">{{ $totalVerified }} <span class="text-base text-slate-400 font-medium">জন</span></h3>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-lg font-extrabold text-slate-900">অপেক্ষমাণ ডোনার তালিকা</h2>
            <p class="text-xs text-slate-500 font-bold mt-1 uppercase tracking-tight">যাদের এনআইডি/আইডি কার্ড যাচাই করা প্রয়োজন</p>
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
                    @forelse($pendingVerifications as $user)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-extrabold text-slate-900">{{ $user->name }}</div>
                                <div class="text-xs font-black text-red-600 mt-0.5">{{ $user->blood_group?->value ?? (string) $user->blood_group }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-700">{{ $user->phone ?? 'N/A' }}</div>
                                <div class="text-[10px] text-slate-400 font-bold">{{ $user->district ?? 'লোকেশন নেই' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-700 uppercase">
                                    Pending Review
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('org.donor.verify', $user->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-black hover:bg-blue-700 transition-all shadow-sm">
                                    ডকুমেন্ট দেখুন
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-bold">
                                এই মুহূর্তে কোনো অপেক্ষমাণ ভেরিফিকেশন নেই। দারুণ কাজ!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection