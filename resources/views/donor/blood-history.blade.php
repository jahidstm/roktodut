@extends('layouts.donor-dashboard')

@section('title', 'রক্তদান হিস্ট্রি — রক্তদূত')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 py-6" data-panel-id="blood-history">

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden scroll-reveal" data-scroll-reveal>
        <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-slate-900">ভেরিফাইড রক্তদান হিস্ট্রি</h3>
                <p class="text-xs text-slate-500 font-medium mt-0.5">আপনার অতীতের সফল রক্তদানের লগ</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase tracking-wider font-bold text-slate-500">
                        <th class="px-6 py-4">তারিখ</th>
                        <th class="px-6 py-4">হাসপাতাল ও লোকেশন</th>
                        <th class="px-6 py-4">রিকোয়েস্ট রেফারেন্স</th>
                        <th class="px-6 py-4 text-center">স্ট্যাটাস</th>
                        <th class="px-6 py-4 text-right">সার্টিফিকেট</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($donationHistory as $history)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-extrabold text-slate-900">{{ $history->fulfilled_at ? $history->fulfilled_at->format('d M, Y') : 'তথ্য নেই' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-700">{{ $history->bloodRequest->hospital?->display_name ?? 'তথ্য নেই' }}</div>
                            <div class="text-xs text-slate-500 font-medium">{{ $history->bloodRequest->district?->name ?? '' }}{{ $history->bloodRequest->upazila?->name ? ', ' . $history->bloodRequest->upazila->name : '' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-slate-400 font-mono bg-slate-100 px-2 py-1 rounded-md">
                                REQ-{{ str_pad($history->blood_request_id, 4, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 uppercase tracking-widest">সম্পন্ন</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @php
                                // Find the matching Donation record for this response
                                $donationRecord = \App\Models\Donation::where('donor_id', auth()->id())
                                    ->where('blood_request_id', $history->blood_request_id)
                                    ->latest()->first();
                            @endphp
                            @if($donationRecord?->certificate_token)
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('certificate.show', $donationRecord->certificate_token) }}"
                                       target="_blank"
                                       title="সার্টিফিকেট দেখুন ও শেয়ার করুন"
                                       class="inline-flex items-center gap-1 text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 hover:bg-amber-100 px-2.5 py-1.5 rounded-lg transition">
                                        📜 দেখুন
                                    </a>
                                    <a href="{{ route('certificate.download', $donationRecord->certificate_token) }}"
                                       title="PNG ডাউনলোড করুন"
                                       class="inline-flex items-center gap-1 text-xs font-bold text-slate-600 bg-slate-100 border border-slate-200 hover:bg-slate-200 px-2.5 py-1.5 rounded-lg transition">
                                        ⬇
                                    </a>
                                </div>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="text-4xl mb-3">📋</div>
                            <p class="font-bold text-slate-600">কোনো হিস্ট্রি পাওয়া যায়নি</p>
                            <p class="text-xs text-slate-500 mt-1">আপনার প্রথম রক্তদানের পর এখানে তা সংরক্ষিত থাকবে।</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
