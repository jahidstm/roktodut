@extends('layouts.org-dashboard')

@section('title', 'মেম্বার ম্যানেজমেন্ট — রক্তদূত')

@section('content')
<div data-panel-id="members">

    {{-- 🔍 Members Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden scroll-reveal" data-scroll-reveal>

        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">মেম্বার তালিকা</h2>
                <p class="text-xs text-slate-500 font-bold mt-1 uppercase tracking-tight">অর্গানাইজেশনের সকল ডোনারের স্ট্যাটাস</p>
            </div>

            {{-- Filter Tabs --}}
            <div class="flex bg-white border border-slate-200 rounded-lg p-1 shadow-sm">
                <a href="{{ route('org.members.index') }}" class="px-4 py-1.5 text-xs font-extrabold rounded-md transition-colors {{ !request('status') ? 'bg-slate-900 text-white' : 'text-slate-500 hover:text-slate-900' }}">সবাই</a>
                <a href="{{ route('org.members.index', ['status' => 'pending']) }}" class="px-4 py-1.5 text-xs font-extrabold rounded-md transition-colors {{ request('status') === 'pending' ? 'bg-amber-100 text-amber-800' : 'text-slate-500 hover:text-slate-900' }}">পেন্ডিং</a>
                <a href="{{ route('org.members.index', ['status' => 'verified']) }}" class="px-4 py-1.5 text-xs font-extrabold rounded-md transition-colors {{ request('status') === 'verified' ? 'bg-emerald-100 text-emerald-800' : 'text-slate-500 hover:text-slate-900' }}">ভেরিফাইড</a>
                <a href="{{ route('org.members.index', ['status' => 'rejected']) }}" class="px-4 py-1.5 text-xs font-extrabold rounded-md transition-colors {{ request('status') === 'rejected' ? 'bg-red-100 text-red-800' : 'text-slate-500 hover:text-slate-900' }}">বাতিলকৃত</a>
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

                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if($user->nid_status === 'pending')
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('org.donor.verify', $user->id) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white rounded-md font-bold transition-colors text-xs">
                                            রিভিউ করুন
                                        </a>
                                        <form action="{{ route('org.members.verify', $user->id) }}" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিত যে এই ডোনার আপনাদের ক্লাবের ভেরিফাইড মেম্বার?');">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="verified">
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-emerald-100 text-emerald-700 hover:bg-emerald-600 hover:text-white rounded-md font-bold transition-colors text-xs">✓</button>
                                        </form>
                                        <form action="{{ route('org.members.verify', $user->id) }}" method="POST" onsubmit="let reason = prompt('বাতিল করার কারণ লিখুন:'); if(reason) { this.reject_reason.value = reason; return true; } return false;">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <input type="hidden" name="reject_reason" value="">
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-md font-bold transition-colors text-xs">✕</button>
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
                                            <p class="text-[10px] text-red-500 text-right w-full font-medium">কারণ: {{ Str::limit($user->rejected_reason, 30) }}</p>
                                        @endif
                                        @if($user->reviewed_by)
                                            <p class="text-[10px] text-slate-400 text-right w-full">রিভিউয়ার আইডি #{{ $user->reviewed_by }}</p>
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
@endsection
